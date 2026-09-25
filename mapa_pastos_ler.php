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

$arquivo = __DIR__ . '/mapa/' . $cnpj_cliente . '/' . $local . '.json';

if (file_exists($arquivo)) {
    $conteudo = file_get_contents($arquivo);
    $geojson = json_decode($conteudo);

    if ($geojson === null || !isset($geojson->features)) {
        echo json_encode(array('error' => true, 'message' => 'O arquivo do mapa dessa fazenda está inválido.'));
        exit;
    }

    $versao = md5($conteudo);
}
else {
    $geojson = array('type' => 'FeatureCollection', 'features' => array());
    $versao = 'novo';
}

$pastos = array();
$local_escapado = mysqli_real_escape_string($conector, $local);

$rs = mysqli_query($conector, "SELECT tbl_pasto_descricao FROM tbl_pasto
    WHERE tbl_pasto_codigo_local='$local_escapado' AND
          tbl_pasto_lixeira=0");

while ($reg = mysqli_fetch_object($rs)) {
    $pastos[] = mb_strtoupper($reg->tbl_pasto_descricao, 'UTF-8');
}

mysqli_close($conector);

echo json_encode(array(
    'success' => true,
    'versao' => $versao,
    'geojson' => $geojson,
    'pastos' => $pastos
), JSON_UNESCAPED_UNICODE);
?>
