<?php
	$id_excluir = $_POST['id_excluir'];

	include "conecta_mysql.inc";

	$sql = ("DELETE FROM tbl_item_movimentacao 
		  WHERE tbl_ite_movimentacao_numero_id='$id_excluir'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao excluir o registro do item da movimentação! - '. $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	$sql = ("DELETE FROM tbl_movimentacao 
			  WHERE tbl_movimentacao_id='$id_excluir'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao excluir a movimentação! - '. $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Registro excluido com sucesso!'));
	mysqli_close($conector);
	exit;

?>