<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$cnpj_empresa = $_SESSION['id_cliente'];

	$data_sistema = date("Y-m-d H:i:s");

	$sql = "UPDATE tbl_empresa SET
	         tbl_empresa_termo_uso_confirmado_em='$data_sistema',
	         tbl_empresa_termo_uso_confirmado_por='$nomeusuario'
	    WHERE tbl_empresa_cpf_cnpj='$cnpj_empresa'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a sua solicitação - '. $erro_mysql));
	    mysqli_close($conector);
		exit;
	} 
	else {
	   	header('Content-type: application/json');
	    echo json_encode(array('success' => true, 'message' => 'Aceite dos termos registrado com sucesso'));
		mysqli_close($conector);
		exit;
	}

?>