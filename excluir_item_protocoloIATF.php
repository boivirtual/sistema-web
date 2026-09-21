<?php

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

$lixeira_item_1 = $_POST["lixeira_item_1"];
$codigo_item_1 = $_POST["codigo_item_1"];
$lixeira_item_2 = $_POST["lixeira_item_2"];
$codigo_item_2 = $_POST["codigo_item_2"];
$lixeira_item_3 = $_POST["lixeira_item_3"];
$codigo_item_3 = $_POST["codigo_item_3"];
$lixeira_item_4 = $_POST["lixeira_item_4"];
$codigo_item_4 = $_POST["codigo_item_4"];

if(isset($lixeira_item_1) && $lixeira_item_1 != '' && $codigo_item_1 != ''){
    $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_1'";

    $resultado = mysqli_query($conector, $sql);

    $resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão -' . $erro_mysql));
    } 
    else {
        header('Content-type: application/json');
        echo json_encode($resposta);
    }

    mysqli_close($conector);
    exit;
}

if(isset($lixeira_item_2) && $lixeira_item_2 != '' && $codigo_item_2 != ''){
    $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_2'";

    $resultado = mysqli_query($conector, $sql);

    $resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão -' . $erro_mysql));
    } 
    else {
        header('Content-type: application/json');
        echo json_encode($resposta);
    }

    mysqli_close($conector);
    exit;
}

if(isset($lixeira_item_3) && $lixeira_item_3 != '' && $codigo_item_3 != ''){
    $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_3'";

    $resultado = mysqli_query($conector, $sql);

    $resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão -' . $erro_mysql));
    } 
    else {
        header('Content-type: application/json');
        echo json_encode($resposta);
    }

    mysqli_close($conector);
    exit;
}

if(isset($lixeira_item_4) && $lixeira_item_4 != '' && $codigo_item_4 != ''){
    $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_4'";

    $resultado = mysqli_query($conector, $sql);

    $resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão -' . $erro_mysql));
    } 
    else {
        header('Content-type: application/json');
        echo json_encode($resposta);
    }

    mysqli_close($conector);
    exit;
}

?>