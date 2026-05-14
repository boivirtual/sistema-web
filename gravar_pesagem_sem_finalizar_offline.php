<?php 
	// Grava pesagem sem finalizar off-line campos Motivo e Lote
	include "conecta_mysql.inc";

	$data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_pesagem_id= $_POST['numero_pesagem_id'];
	$epoca_pesagem= $_POST['epoca_pesagem'];
	$descricao_lote= $_POST['lote'];

	$sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_codigo_epoca='$epoca_pesagem',
			tbl_pesagem_lote='$descricao_lote',
			tbl_pesagem_alterado_em='$data_sistema',
			tbl_pesagem_alterado_por='$nomeusuario'
	    WHERE tbl_pesagem_id='$numero_pesagem_id'";

    $resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
    	header('Content-type: application/json');
    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a pesagem ' . $erro_mysql));
    	exit;
	} 
?>