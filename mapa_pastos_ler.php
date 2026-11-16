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

$resultado = mapa_pastos_dados($conector, $cnpj_cliente, $local);
mysqli_close($conector);

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
?>
