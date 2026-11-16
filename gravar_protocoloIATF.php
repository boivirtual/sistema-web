<?php

$nome_protocolo = $_POST["nome_protocolo"];
$quant_protocolo = $_POST["quant_protocolo"];
$dias_diagnostico = $_POST["qtd_dias"];

//0
$descricao_0 = $_POST["descricao_0"];
$nome_prod_0 = $_POST["nome_prod_0"];
$quantidade_0 = $_POST["quantidade_0"];
if($quantidade_0 == ''){
    $quantidade_0 = 0;
}
if(isset($_POST["unidade_0"])){
    $unidade_0 = $_POST["unidade_0"];
}else{
    $unidade_0 = '';
}

$nome_prod_0_1 = $_POST["nome_prod_0_1"];
$quantidade_0_1 = $_POST["quantidade_0_1"];
if($quantidade_0_1 == ''){
    $quantidade_0_1 = 0;
}
if(isset($_POST["unidade_0_1"])){
    $unidade_0_1 = $_POST["unidade_0_1"];
}else{
    $unidade_0_1 = '';
}

$nome_prod_0_2 = $_POST["nome_prod_0_2"];
$quantidade_0_2 = $_POST["quantidade_0_2"];
if($quantidade_0_2 == ''){
    $quantidade_0_2 = 0;
}
if(isset($_POST["unidade_0_2"])){
    $unidade_0_2 = $_POST["unidade_0_2"];
}else{
    $unidade_0_2 = '';
}

$nome_prod_0_3 = $_POST["nome_prod_0_3"];
$quantidade_0_3 = $_POST["quantidade_0_3"];
if($quantidade_0_3 == ''){
    $quantidade_0_3 = 0;
}
if(isset($_POST["unidade_0_3"])){
    $unidade_0_3 = $_POST["unidade_0_3"];
}else{
    $unidade_0_3 = '';
}

if($nome_prod_0_1 == ''){
    $nome_prod_0_1 = '';
    $quantidade_0_1 = 0;
    $unidade_0_1 = '';

    $nome_prod_0_2 = '';
    $quantidade_0_2 = 0;
    $unidade_0_2 = '';

    $nome_prod_0_3 = '';
    $quantidade_0_3 = 0;
    $unidade_0_3 = '';
}elseif($nome_prod_0_2 == ''){
    $nome_prod_0_2 = '';
    $quantidade_0_2 = 0;
    $unidade_0_2 = '';

    $nome_prod_0_3 = '';
    $quantidade_0_3 = 0;
    $unidade_0_3 = '';
}elseif($nome_prod_0_3 == ''){
    $nome_prod_0_3 = '';
    $quantidade_0_3 = 0;
    $unidade_0_3 = '';
}

//1
$descricao_1 = $_POST["descricao_1"];
$nome_prod_1 = $_POST["nome_prod_1"];
$quantidade_1 = $_POST["quantidade_1"];
if($quantidade_1 == ''){
    $quantidade_1 = 0;
}
if(isset($_POST["unidade_1"])){
    $unidade_1 = $_POST["unidade_1"];
}else{
    $unidade_1 = '';
}

$nome_prod_1_1 = $_POST["nome_prod_1_1"];
$quantidade_1_1 = $_POST["quantidade_1_1"];
if($quantidade_1_1 == ''){
    $quantidade_1_1 = 0;
}
if(isset($_POST["unidade_1_1"])){
    $unidade_1_1 = $_POST["unidade_1_1"];
}else{
    $unidade_1_1 = '';
}

$nome_prod_1_2 = $_POST["nome_prod_1_2"];
$quantidade_1_2 = $_POST["quantidade_1_2"];
if($quantidade_1_2 == ''){
    $quantidade_1_2 = 0;
}
if(isset($_POST["unidade_1_2"])){
    $unidade_1_2 = $_POST["unidade_1_2"];
}else{
    $unidade_1_2 = '';
}

$nome_prod_1_3 = $_POST["nome_prod_1_3"];
$quantidade_1_3 = $_POST["quantidade_1_3"];
if($quantidade_1_3 == ''){
    $quantidade_1_3 = 0;
}
if(isset($_POST["unidade_1_3"])){
    $unidade_1_3 = $_POST["unidade_1_3"];
}else{
    $unidade_1_3 = '';
}

if($nome_prod_1_1 == ''){
    $nome_prod_1_1 = '';
    $quantidade_1_1 = 0;
    $unidade_1_1 = '';

    $nome_prod_1_2 = '';
    $quantidade_1_2 = 0;
    $unidade_1_2 = '';

    $nome_prod_1_3 = '';
    $quantidade_1_3 = 0;
    $unidade_1_3 = '';
}elseif($nome_prod_1_2 == ''){
    $nome_prod_1_2 = '';
    $quantidade_1_2 = 0;
    $unidade_1_2 = '';

    $nome_prod_1_3 = '';
    $quantidade_1_3 = 0;
    $unidade_1_3 = '';
}elseif($nome_prod_1_3 == ''){
    $nome_prod_1_3 = '';
    $quantidade_1_3 = 0;
    $unidade_1_3 = '';
}

//2
$descricao_2 = $_POST["descricao_2"];
$nome_prod_2 = $_POST["nome_prod_2"];
$quantidade_2 = $_POST["quantidade_2"];
if($quantidade_2 == ''){
    $quantidade_2 = 0;
}
if(isset($_POST["unidade_2"])){
    $unidade_2 = $_POST["unidade_2"];
}else{
    $unidade_2 = '';
}

$nome_prod_2_1 = $_POST["nome_prod_2_1"];
$quantidade_2_1 = $_POST["quantidade_2_1"];
if($quantidade_2_1 == ''){
    $quantidade_2_1 = 0;
}
if(isset($_POST["unidade_2_1"])){
    $unidade_2_1 = $_POST["unidade_2_1"];
}else{
    $unidade_2_1 = '';
}

$nome_prod_2_2 = $_POST["nome_prod_2_2"];
$quantidade_2_2 = $_POST["quantidade_2_2"];
if($quantidade_2_2 == ''){
    $quantidade_2_2 = 0;
}
if(isset($_POST["unidade_2_2"])){
    $unidade_2_2 = $_POST["unidade_2_2"];
}else{
    $unidade_2_2 = '';
}

$nome_prod_2_3 = $_POST["nome_prod_2_3"];
$quantidade_2_3 = $_POST["quantidade_2_3"];
if($quantidade_2_3 == ''){
    $quantidade_2_3 = 0;
}
if(isset($_POST["unidade_2_3"])){
    $unidade_2_3 = $_POST["unidade_2_3"];
}else{
    $unidade_2_3 = '';
}

if($nome_prod_2_1 == ''){
    $nome_prod_2_1 = '';
    $quantidade_2_1 = 0;
    $unidade_2_1 = '';

    $nome_prod_2_2 = '';
    $quantidade_2_2 = 0;
    $unidade_2_2 = '';

    $nome_prod_2_3 = '';
    $quantidade_2_3 = 0;
    $unidade_2_3 = '';
}elseif($nome_prod_2_2 == ''){
    $nome_prod_2_2 = '';
    $quantidade_2_2 = 0;
    $unidade_2_2 = '';

    $nome_prod_2_3 = '';
    $quantidade_2_3 = 0;
    $unidade_2_3 = '';
}elseif($nome_prod_2_3 == ''){
    $nome_prod_2_3 = '';
    $quantidade_2_3 = 0;
    $unidade_2_3 = '';
}

//3
$descricao_3 = $_POST["descricao_3"];
$nome_prod_3 = $_POST["nome_prod_3"];
$quantidade_3 = $_POST["quantidade_3"];
if($quantidade_3 == ''){
    $quantidade_3 = 0;
}
if(isset($_POST["unidade_3"])){
    $unidade_3 = $_POST["unidade_3"];
}else{
    $unidade_3 = '';
}

$nome_prod_3_1 = $_POST["nome_prod_3_1"];
$quantidade_3_1 = $_POST["quantidade_3_1"];
if($quantidade_3_1 == ''){
    $quantidade_3_1 = 0;
}
if(isset($_POST["unidade_3_1"])){
    $unidade_3_1 = $_POST["unidade_3_1"];
}else{
    $unidade_3_1 = '';
}

$nome_prod_3_2 = $_POST["nome_prod_3_2"];
$quantidade_3_2 = $_POST["quantidade_3_2"];
if($quantidade_3_2 == ''){
    $quantidade_3_2 = 0;
}
if(isset($_POST["unidade_3_2"])){
    $unidade_3_2 = $_POST["unidade_3_2"];
}else{
    $unidade_3_2 = '';
}

$nome_prod_3_3 = $_POST["nome_prod_3_3"];
$quantidade_3_3 = $_POST["quantidade_3_3"];
if($quantidade_3_3 == ''){
    $quantidade_3_3 = 0;
}
if(isset($_POST["unidade_3_3"])){
    $unidade_3_3 = $_POST["unidade_3_3"];
}else{
    $unidade_3_3 = '';
}

if($nome_prod_3_1 == ''){
    $nome_prod_3_1 = '';
    $quantidade_3_1 = 0;
    $unidade_3_1 = '';

    $nome_prod_3_2 = '';
    $quantidade_3_2 = 0;
    $unidade_3_2 = '';

    $nome_prod_3_3 = '';
    $quantidade_3_3 = 0;
    $unidade_3_3 = '';
}elseif($nome_prod_3_2 == ''){
    $nome_prod_3_2 = '';
    $quantidade_3_2 = 0;
    $unidade_3_2 = '';

    $nome_prod_3_3 = '';
    $quantidade_3_3 = 0;
    $unidade_3_3 = '';
}elseif($nome_prod_3_3 == ''){
    $nome_prod_3_3 = '';
    $quantidade_3_3 = 0;
    $unidade_3_3 = '';
}

//4
$descricao_4 = $_POST["descricao_4"];
$nome_prod_4 = $_POST["nome_prod_4"];
$quantidade_4 = $_POST["quantidade_4"];
if($quantidade_4 == ''){
    $quantidade_4 = 0;
}
if(isset($_POST["unidade_4"])){
    $unidade_4 = $_POST["unidade_4"];
}else{
    $unidade_4 = '';
}

$nome_prod_4_1 = $_POST["nome_prod_4_1"];
$quantidade_4_1 = $_POST["quantidade_4_1"];
if($quantidade_4_1 == ''){
    $quantidade_4_1 = 0;
}
if(isset($_POST["unidade_4_1"])){
    $unidade_4_1 = $_POST["unidade_4_1"];
}else{
    $unidade_4_1 = '';
}

$nome_prod_4_2 = $_POST["nome_prod_4_2"];
$quantidade_4_2 = $_POST["quantidade_4_2"];
if($quantidade_4_2 == ''){
    $quantidade_4_2 = 0;
}
if(isset($_POST["unidade_4_2"])){
    $unidade_4_2 = $_POST["unidade_4_2"];
}else{
    $unidade_4_2 = '';
}

$nome_prod_4_3 = $_POST["nome_prod_4_3"];
$quantidade_4_3 = $_POST["quantidade_4_3"];
if($quantidade_4_3 == ''){
    $quantidade_4_3 = 0;
}
if(isset($_POST["unidade_4_3"])){
    $unidade_4_3 = $_POST["unidade_4_3"];
}else{
    $unidade_4_3 = '';
}

if($nome_prod_4_1 == ''){
    $nome_prod_4_1 = '';
    $quantidade_4_1 = 0;
    $unidade_4_1 = '';

    $nome_prod_4_2 = '';
    $quantidade_4_2 = 0;
    $unidade_4_2 = '';

    $nome_prod_4_3 = '';
    $quantidade_4_3 = 0;
    $unidade_4_3 = '';
}elseif($nome_prod_4_2 == ''){
    $nome_prod_4_2 = '';
    $quantidade_4_2 = 0;
    $unidade_4_2 = '';

    $nome_prod_4_3 = '';
    $quantidade_4_3 = 0;
    $unidade_4_3 = '';
}elseif($nome_prod_4_3 == ''){
    $nome_prod_4_3 = '';
    $quantidade_4_3 = 0;
    $unidade_4_3 = '';
}

$codigo_conta = $_POST['codigo_conta'];
$codigo_item_0 = $_POST['codigo_item_0'];
$codigo_item_1 = $_POST['codigo_item_1'];
$codigo_item_2 = $_POST['codigo_item_2'];
$codigo_item_3 = $_POST['codigo_item_3'];
$codigo_item_4 = $_POST['codigo_item_4'];

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if($_POST["tipo_gravacao"] == 0){
    if($nome_protocolo != '' && $quant_protocolo != '' && $nome_prod_0 != ''){
        $sql = "INSERT INTO tbl_protocoloiatf (
            tbl_protocoloiatf_descricao,
            tbl_protocoloiatf_qtde,
            tbl_protocoloiatf_dias_diagnostico,
            tbl_protocoloiatf_incluido_em,
            tbl_protocoloiatf_incluido_por,
            tbl_protocoloiatf_alterado_em,
            tbl_protocoloiatf_alterado_por,
            tbl_protocoloiatf_lixeira,
            tbl_protocoloiatf_lixeira_em,
            tbl_protocoloiatf_lixeira_por
            )
            VALUES(
                '$nome_protocolo',
                '$quant_protocolo',
                '$dias_diagnostico',
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                0,
                null,
                null
            )";
    
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    
        $protocolo_id = mysqli_insert_id($conector);
    
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_0',
            '$nome_prod_0',
            '$quantidade_0',
            '$unidade_0',
            '$nome_prod_0_1',
            '$quantidade_0_1',
            '$unidade_0_1',
            '$nome_prod_0_2',
            '$quantidade_0_2',
            '$unidade_0_2',
            '$nome_prod_0_3',
            '$quantidade_0_3',
            '$unidade_0_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_1 != '' && $nome_prod_1 != ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_1',
            '$nome_prod_1',
            '$quantidade_1',
            '$unidade_1',
            '$nome_prod_1_1',
            '$quantidade_1_1',
            '$unidade_1_1',
            '$nome_prod_1_2',
            '$quantidade_1_2',
            '$unidade_1_2',
            '$nome_prod_1_3',
            '$quantidade_1_3',
            '$unidade_1_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_2 != '' && $nome_prod_2 != ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_2',
            '$nome_prod_2',
            '$quantidade_2',
            '$unidade_2',
            '$nome_prod_2_1',
            '$quantidade_2_1',
            '$unidade_2_1',
            '$nome_prod_2_2',
            '$quantidade_2_2',
            '$unidade_2_2',
            '$nome_prod_2_3',
            '$quantidade_2_3',
            '$unidade_2_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_3 != '' && $nome_prod_3 != ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_3',
            '$nome_prod_3',
            '$quantidade_3',
            '$unidade_3',
            '$nome_prod_3_1',
            '$quantidade_3_1',
            '$unidade_3_1',
            '$nome_prod_3_2',
            '$quantidade_3_2',
            '$unidade_3_2',
            '$nome_prod_3_3',
            '$quantidade_3_3',
            '$unidade_3_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_4 != '' && $nome_prod_4 != ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_4',
            '$nome_prod_4',
            '$quantidade_4',
            '$unidade_4',
            '$nome_prod_4_1',
            '$quantidade_4_1',
            '$unidade_4_1',
            '$nome_prod_4_2',
            '$quantidade_4_2',
            '$unidade_4_2',
            '$nome_prod_4_3',
            '$quantidade_4_3',
            '$unidade_4_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
}elseif($_POST["tipo_gravacao"] == 1){
    if($nome_protocolo != '' && $quant_protocolo != ''){
        $sql = "UPDATE tbl_protocoloiatf SET
                tbl_protocoloiatf_descricao = '$nome_protocolo',
                tbl_protocoloiatf_qtde = '$quant_protocolo',
                tbl_protocoloiatf_dias_diagnostico = '$dias_diagnostico',
                tbl_protocoloiatf_alterado_em = '$data_sistema',
                tbl_protocoloiatf_alterado_por = '$nomeusuario'
                WHERE tbl_protocoloiatf_id = '$codigo_conta'";
        
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    if($nome_prod_0 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_medicamento_1 = '$nome_prod_0',
                tbl_ite_protocoloiatf_qtde_1 = '$quantidade_0',
                tbl_ite_protocoloiatf_unidade_1 = '$unidade_0',
                tbl_ite_protocoloiatf_medicamento_2 = '$nome_prod_0_1',
                tbl_ite_protocoloiatf_qtde_2 = '$quantidade_0_1',
                tbl_ite_protocoloiatf_unidade_2 = '$unidade_0_1',
                tbl_ite_protocoloiatf_medicamento_3 = '$nome_prod_0_2',
                tbl_ite_protocoloiatf_qtde_3 = '$quantidade_0_2',
                tbl_ite_protocoloiatf_unidade_3 = '$unidade_0_2',
                tbl_ite_protocoloiatf_medicamento_4 = '$nome_prod_0_3',
                tbl_ite_protocoloiatf_qtde_4 = '$quantidade_0_3',
                tbl_ite_protocoloiatf_unidade_4 = '$unidade_0_3',
                tbl_ite_protocoloiatf_alterado_em = '$data_sistema',
                tbl_ite_protocoloiatf_alterado_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_0'";

        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_1 != '' && $nome_prod_1 != '' && $codigo_item_1 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_descricao = '$descricao_1',
                tbl_ite_protocoloiatf_medicamento_1 = '$nome_prod_1',
                tbl_ite_protocoloiatf_qtde_1 = '$quantidade_1',
                tbl_ite_protocoloiatf_unidade_1 = '$unidade_1',
                tbl_ite_protocoloiatf_medicamento_2 = '$nome_prod_1_1',
                tbl_ite_protocoloiatf_qtde_2 = '$quantidade_1_1',
                tbl_ite_protocoloiatf_unidade_2 = '$unidade_1_1',
                tbl_ite_protocoloiatf_medicamento_3 = '$nome_prod_1_2',
                tbl_ite_protocoloiatf_qtde_3 = '$quantidade_1_2',
                tbl_ite_protocoloiatf_unidade_3 = '$unidade_1_2',
                tbl_ite_protocoloiatf_medicamento_4 = '$nome_prod_1_3',
                tbl_ite_protocoloiatf_qtde_4 = '$quantidade_1_3',
                tbl_ite_protocoloiatf_unidade_4 = '$unidade_1_3',
                tbl_ite_protocoloiatf_alterado_em = '$data_sistema',
                tbl_ite_protocoloiatf_alterado_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_1'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }elseif($descricao_1 != '' && $nome_prod_1 != '' && $codigo_item_1 == ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
            )
            VALUES(
                '$protocolo_id',
                '$descricao_1',
                '$nome_prod_1',
                '$quantidade_1',
                '$unidade_1',
                '$nome_prod_1_1',
                '$quantidade_1_1',
                '$unidade_1_1',
                '$nome_prod_1_2',
                '$quantidade_1_2',
                '$unidade_1_2',
                '$nome_prod_1_3',
                '$quantidade_1_3',
                '$unidade_1_3',
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                0,
                null,
                null
            )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_2 != '' && $nome_prod_2 != '' && $codigo_item_2 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_descricao = '$descricao_2',
                tbl_ite_protocoloiatf_medicamento_1 = '$nome_prod_2',
                tbl_ite_protocoloiatf_qtde_1 = '$quantidade_2',
                tbl_ite_protocoloiatf_unidade_1 = '$unidade_2',
                tbl_ite_protocoloiatf_medicamento_2 = '$nome_prod_2_1',
                tbl_ite_protocoloiatf_qtde_2 = '$quantidade_2_1',
                tbl_ite_protocoloiatf_unidade_2 = '$unidade_2_1',
                tbl_ite_protocoloiatf_medicamento_3 = '$nome_prod_2_2',
                tbl_ite_protocoloiatf_qtde_3 = '$quantidade_2_2',
                tbl_ite_protocoloiatf_unidade_3 = '$unidade_2_2',
                tbl_ite_protocoloiatf_medicamento_4 = '$nome_prod_2_3',
                tbl_ite_protocoloiatf_qtde_4 = '$quantidade_2_3',
                tbl_ite_protocoloiatf_unidade_4 = '$unidade_2_3',
                tbl_ite_protocoloiatf_alterado_em = '$data_sistema',
                tbl_ite_protocoloiatf_alterado_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_2'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }elseif($descricao_2 != '' && $nome_prod_2 != '' && $codigo_item_2 == ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
            )
            VALUES(
                '$protocolo_id',
                '$descricao_2',
                '$nome_prod_2',
                '$quantidade_2',
                '$unidade_2',
                '$nome_prod_2_1',
                '$quantidade_2_1',
                '$unidade_2_1',
                '$nome_prod_2_2',
                '$quantidade_2_2',
                '$unidade_2_2',
                '$nome_prod_2_3',
                '$quantidade_2_3',
                '$unidade_2_3',
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                0,
                null,
                null
            )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_3 != '' && $nome_prod_3 != '' && $codigo_item_3 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_descricao = '$descricao_3',
                tbl_ite_protocoloiatf_medicamento_1 = '$nome_prod_3',
                tbl_ite_protocoloiatf_qtde_1 = '$quantidade_3',
                tbl_ite_protocoloiatf_unidade_1 = '$unidade_3',
                tbl_ite_protocoloiatf_medicamento_2 = '$nome_prod_3_1',
                tbl_ite_protocoloiatf_qtde_2 = '$quantidade_3_1',
                tbl_ite_protocoloiatf_unidade_2 = '$unidade_3_1',
                tbl_ite_protocoloiatf_medicamento_3 = '$nome_prod_3_2',
                tbl_ite_protocoloiatf_qtde_3 = '$quantidade_3_2',
                tbl_ite_protocoloiatf_unidade_3 = '$unidade_3_2',
                tbl_ite_protocoloiatf_medicamento_4 = '$nome_prod_3_3',
                tbl_ite_protocoloiatf_qtde_4 = '$quantidade_3_3',
                tbl_ite_protocoloiatf_unidade_4 = '$unidade_3_3',
                tbl_ite_protocoloiatf_alterado_em = '$data_sistema',
                tbl_ite_protocoloiatf_alterado_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_3'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }elseif($descricao_3 != '' && $nome_prod_3 != '' && $codigo_item_3 == ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_3',
            '$nome_prod_3',
            '$quantidade_3',
            '$unidade_3',
            '$nome_prod_3_1',
            '$quantidade_3_1',
            '$unidade_3_1',
            '$nome_prod_3_2',
            '$quantidade_3_2',
            '$unidade_3_2',
            '$nome_prod_3_3',
            '$quantidade_3_3',
            '$unidade_3_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_4 != '' && $nome_prod_4 != '' && $codigo_item_4 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_descricao = '$descricao_4',
                tbl_ite_protocoloiatf_medicamento_1 = '$nome_prod_4',
                tbl_ite_protocoloiatf_qtde_1 = '$quantidade_4',
                tbl_ite_protocoloiatf_unidade_1 = '$unidade_4',
                tbl_ite_protocoloiatf_medicamento_2 = '$nome_prod_4_1',
                tbl_ite_protocoloiatf_qtde_2 = '$quantidade_4_1',
                tbl_ite_protocoloiatf_unidade_2 = '$unidade_4_1',
                tbl_ite_protocoloiatf_medicamento_3 = '$nome_prod_4_2',
                tbl_ite_protocoloiatf_qtde_3 = '$quantidade_4_2',
                tbl_ite_protocoloiatf_unidade_3 = '$unidade_4_2',
                tbl_ite_protocoloiatf_medicamento_4 = '$nome_prod_4_3',
                tbl_ite_protocoloiatf_qtde_4 = '$quantidade_4_3',
                tbl_ite_protocoloiatf_unidade_4 = '$unidade_4_3',
                tbl_ite_protocoloiatf_alterado_em = '$data_sistema',
                tbl_ite_protocoloiatf_alterado_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_4'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }elseif($descricao_4 != '' && $nome_prod_4 != '' && $codigo_item_4 == ''){
        $sql = "INSERT INTO tbl_item_protocoloiatf (
                tbl_ite_protocoloiatf_protocolo_id,
                tbl_ite_protocoloiatf_descricao,
                tbl_ite_protocoloiatf_medicamento_1,
                tbl_ite_protocoloiatf_qtde_1,
                tbl_ite_protocoloiatf_unidade_1,
                tbl_ite_protocoloiatf_medicamento_2,
                tbl_ite_protocoloiatf_qtde_2,
                tbl_ite_protocoloiatf_unidade_2,
                tbl_ite_protocoloiatf_medicamento_3,
                tbl_ite_protocoloiatf_qtde_3,
                tbl_ite_protocoloiatf_unidade_3,
                tbl_ite_protocoloiatf_medicamento_4,
                tbl_ite_protocoloiatf_qtde_4,
                tbl_ite_protocoloiatf_unidade_4,
                tbl_ite_protocoloiatf_incluido_em,
                tbl_ite_protocoloiatf_incluido_por,
                tbl_ite_protocoloiatf_alterado_em,
                tbl_ite_protocoloiatf_alterado_por,
                tbl_ite_protocoloiatf_lixeira,
                tbl_ite_protocoloiatf_lixeira_em,
                tbl_ite_protocoloiatf_lixeira_por
        )
        VALUES(
            '$protocolo_id',
            '$descricao_4',
            '$nome_prod_4',
            '$quantidade_4',
            '$unidade_4',
            '$nome_prod_4_1',
            '$quantidade_4_1',
            '$unidade_4_1',
            '$nome_prod_4_2',
            '$quantidade_4_2',
            '$unidade_4_2',
            '$nome_prod_4_3',
            '$quantidade_4_3',
            '$unidade_4_3',
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
}elseif($_POST["tipo_gravacao"] == 2){
    if($codigo_conta != ''){
        $sql = "UPDATE tbl_protocoloiatf SET
                tbl_protocoloiatf_lixeira = 1,
                tbl_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_protocoloiatf_id = '$codigo_conta'";
        
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    if($codigo_item_0 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_0'";

        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($codigo_item_1 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_1'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($codigo_item_2 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_2'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($codigo_item_3 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_3'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
    
    if($descricao_4 != '' && $nome_prod_4 != '' && $quantidade_4 != '' && $codigo_item_4 != ''){
        $sql = "UPDATE tbl_item_protocoloiatf SET
                tbl_ite_protocoloiatf_lixeira = 1,
                tbl_ite_protocoloiatf_lixeira_em = '$data_sistema',
                tbl_ite_protocoloiatf_lixeira_por = '$nomeusuario'
                WHERE tbl_ite_protocoloiatf_id = '$codigo_item_4'";
                
        $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));
    }
}



?>