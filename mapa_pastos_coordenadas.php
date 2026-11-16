<?php
include "conecta_mysql.inc";
include "mapa_pastos_acesso.php";

@ session_start();
header('Content-type: application/json; charset=utf-8');

function resposta_erro($mensagem) {
    echo json_encode(array('error' => true, 'message' => $mensagem), JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['menu_parametros'])) {
    resposta_erro('Você não efetuou o login.');
}

$acesso = explode("!", $_SESSION['menu_parametros']);

if ($acesso[3] == 0) {
    resposta_erro('Você não tem acesso a esse programa.');
}

$local = isset($_POST['local']) ? $_POST['local'] : '';
$lat = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$lng = isset($_POST['longitude']) ? $_POST['longitude'] : '';

if (!preg_match('/^[0-9]{1,12}$/', $local)) {
    resposta_erro('Fazenda inválida.');
}

if (!usuario_pode_local($conector_acesso, $local)) {
    resposta_erro('Você não tem acesso a essa Fazenda.');
}

if (!is_numeric($lat) || !is_numeric($lng)) {
    resposta_erro('Latitude e longitude precisam ser números.');
}

$lat = (float)$lat;
$lng = (float)$lng;

if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ($lat == 0 && $lng == 0)) {
    resposta_erro('Latitude ou longitude fora do intervalo válido.');
}

$local_sql = mysqli_real_escape_string($conector, $local);
$usuario_sql = mysqli_real_escape_string($conector, $_SESSION['nome_usuario']);
$agora = date('Y-m-d H:i:s');

// Grava somente em cadastro de fazenda (classe 4)
$ok = mysqli_query($conector, "UPDATE tbl_pessoa SET
    tbl_pessoa_latitude_fazenda=$lat,
    tbl_pessoa_longitude_fazenda=$lng,
    tbl_pessoa_alterado_em='$agora',
    tbl_pessoa_alterado_por='$usuario_sql'
    WHERE tbl_pessoa_id='$local_sql' AND tbl_pessoa_classe=4");

if (!$ok) {
    resposta_erro('Não foi possível gravar as coordenadas: ' . mysqli_error($conector));
}

mysqli_close($conector);

echo json_encode(array('success' => true), JSON_UNESCAPED_UNICODE);
?>
