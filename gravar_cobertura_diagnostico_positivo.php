<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data_diagnostico = date("Y-m-d");
$mensagem = 0;

include "conecta_mysql.inc";

$cobertura_numero_id = $_POST['cobertura_numero_id'];
$codigo_id_animal = $_POST['codigo_id'];
$codigo_animal = $_POST['codigo_animal'];
$estacao_monta = $_POST['estacao_monta'];
$local = $_POST['local'];
$ordem = $_POST['ordem'];

$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
    WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id' AND 
    	  tbl_ite_cobertura_numero_item = '$ordem'");

$num_rows_item = mysqli_num_rows($tbl_item);    

if ($num_rows_item!=0) {
    $reg_item = mysqli_fetch_object($tbl_item);
    $qtd_diagnostico =  $reg_item->tbl_ite_cobertura_qtd_diagnosticos_positivo;
    $qtd_diagnostico++;

	$sql = "UPDATE tbl_item_cobertura SET
        tbl_ite_cobertura_qtd_diagnosticos_positivo = '$qtd_diagnostico'
        WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id' AND 
        	  tbl_ite_cobertura_numero_item = '$ordem'";

    $resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		$array_conta = array(
		    0,
		    'Ocorreu um erro na atualização da quantidade de diagnósticos.' . $erro_mysql
		);
		$array_string = implode('|', $array_conta);
		echo $array_string;
		exit;
	} 

	$sql = "INSERT INTO tbl_item_cobertura_diagnostico_positivo(
	    tbl_ite_cobertura_diagnostico_numero_id,
	    tbl_ite_cobertura_diagnostico_numero_item,
	    tbl_ite_cobertura_diagnostico_data_diagnostico,
	    tbl_ite_cobertura_diagnostico_codigo_id_animal,
	    tbl_ite_cobertura_diagnostico_codigo_animal,
	    tbl_ite_cobertura_diagnostico_confirmado_em,
	    tbl_ite_cobertura_diagnostico_data_confirmado_por
	)VALUES(
	    '$cobertura_numero_id',
	    '$ordem',
	    '$data_diagnostico',
	    '$codigo_id_animal',
	    '$codigo_animal',
	    '$data_sistema',
	    '$nomeusuario'
	)";

    $resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		$array_conta = array(
		    0,
		    'Ocorreu um erro inclusão do histórico do diagnótico.' . $erro_mysql
		);
		$array_string = implode('|', $array_conta);
		echo $array_string;
		exit;
	} 

	$array_conta = array(
	    $qtd_diagnostico,
	    ''
	);
	$array_string = implode('|', $array_conta);

	echo $array_string;
}

?>