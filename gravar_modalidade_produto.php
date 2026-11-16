<?php
include "conecta_mysql.inc";

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_conta = mysqli_real_escape_string($conector, $_POST['codigo_conta']);
$descricao = mysqli_real_escape_string($conector, $_POST['descricao']);

$data_sistema = date("Y-m-d H:i:s");

if ($descricao==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Descrição.'));
	exit;
}

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_modalidade_produto SET
	                   tbl_modalidade_lixeira=1,
	                   tbl_modalidade_excluido_em='$data_sistema',
	                   tbl_modalidade_excluido_por='$nomeusuario'
	                   WHERE tbl_codigo_modalidade='$codigo_conta'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro enviado para lixeira com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao enviar o registro para a lixeira' . $erro_mysql));
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector);
		exit;
}
else if ($tipo_gravacao==3){
		$sql = "UPDATE tbl_modalidade_produto SET
	                   tbl_modalidade_lixeira=0,
	                   tbl_modalidade_excluido_em=null,
	                   tbl_modalidade_excluido_por=null
	                   WHERE tbl_codigo_modalidade='$codigo_conta'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro removido da lixeira com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao remover o registro da lixeira' . $erro_mysql));
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector);
		exit;
}
else if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_modalidade_produto SET
				tbl_descricao_modalidade='$descricao',
				tbl_modalidade_alterado_em='$data_sistema',
				tbl_modalidade_alterado_por='$nomeusuario'
				WHERE tbl_codigo_modalidade='$codigo_conta'");
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração ' . $erro_mysql));
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector);
		exit;
}
else{
	$sql = "INSERT INTO tbl_modalidade_produto (
			tbl_descricao_modalidade,
			tbl_modalidade_incluido_em,
			tbl_modalidade_incluido_por,
			tbl_modalidade_lixeira
	        )
		    VALUES (
				    '$descricao',
	                '$data_sistema',
	                '$nomeusuario',
					0
	        )";


	$resultado = mysqli_query($conector,$sql);

	$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
	}
	else {
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
	}

	mysqli_close($conector);
	exit;
}

mysqli_close($conector);

?>