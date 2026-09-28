<?php
include "conecta_mysql.inc";

@ session_start();
header('Content-type: application/json; charset=utf-8');

$cnpj_cliente = $_SESSION['id_cliente'];

if (!isset($_SESSION['menu_parametros'])) {
    echo json_encode(array('error' => true, 'message' => 'Você não efetuou o login.'));
    exit;
}

$acesso = explode("!", $_SESSION['menu_parametros']);

if ($acesso[3] == 0) {
    echo json_encode(array('error' => true, 'message' => 'Você não tem acesso a esse programa.'));
    exit;
}

$local = isset($_POST['local']) ? $_POST['local'] : '';

if (!preg_match('/^[0-9]{1,12}$/', $local)) {
    echo json_encode(array('error' => true, 'message' => 'Selecione a Fazenda.'));
    exit;
}

include "mapa_pastos_acesso.php";

if (!usuario_pode_local($conector_acesso, $local)) {
    echo json_encode(array('error' => true, 'message' => 'Você não tem acesso a essa Fazenda.'));
    exit;
}

include "funcao_mapa_fazenda.php";

$leitura_mapa = mapa_ler($conector, $cnpj_cliente, $local);

if ($leitura_mapa['json'] !== '') {
    $geojson = json_decode($leitura_mapa['json']);

    if ($geojson === null || !isset($geojson->features)) {
        echo json_encode(array('error' => true, 'message' => 'O mapa dessa fazenda está inválido.'));
        exit;
    }

    $versao = $leitura_mapa['versao'];
}
else {
    $geojson = array('type' => 'FeatureCollection', 'features' => array());
    $versao = 'novo';
}

include "funcao_modulo_pasto_cor.php";

$modulos = ler_modulos_pasto($conector);

// nome do pasto (maiusculas) => id do modulo
$pastos = new stdClass();
$local_escapado = mysqli_real_escape_string($conector, $local);

$rs = mysqli_query($conector, "SELECT tbl_pasto_descricao, tbl_pasto_modulo FROM tbl_pasto
    WHERE tbl_pasto_codigo_local='$local_escapado' AND
          tbl_pasto_lixeira=0");

while ($reg = mysqli_fetch_object($rs)) {
    $nome = mb_strtoupper($reg->tbl_pasto_descricao, 'UTF-8');
    $pastos->$nome = (int)$reg->tbl_pasto_modulo;
}

mysqli_close($conector);

echo json_encode(array(
    'success' => true,
    'versao' => $versao,
    'geojson' => $geojson,
    'pastos' => $pastos,
    'modulos' => $modulos
), JSON_UNESCAPED_UNICODE);
?>
