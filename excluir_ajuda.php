<?php
	$id_ajuda = $_POST['id_ajuda'];

	include "conecta_mysql.inc";

	$sql = ("DELETE FROM tbl_ajuda
		WHERE id_ajuda ='$id_ajuda'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao excluir o registro. ' . $erro_mysql));
	}
	else {
    	header('Content-type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Registro excluido com sucesso.'));
	}

	mysqli_close($conector);

?>