<?php
include "conecta_mysql.inc";

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_conta = mysqli_real_escape_string($conector, $_POST['codigo_conta']);
$descricao = mysqli_real_escape_string($conector, $_POST['descricao']);
$modalidade = mysqli_real_escape_string($conector, $_POST['modalidade']);

$data_sistema = date("Y-m-d H:i:s");

if ($descricao==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Descrição.'));
	exit;
}

if ($modalidade==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Modalidade.'));
	exit;
}

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];

if ($tipo_gravacao==2){
		$sql = "UPDATE tabela_produto_generico SET
	                   pro_generico_registro_lixeira=1,
	                   pro_generico_lixeira_em='$data_sistema',
	                   pro_generico_lixeira_por='$nomeusuario'
	                   WHERE pro_generico_codigo='$codigo_conta'";
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
		$sql = "UPDATE tabela_produto_generico SET
	                   pro_generico_registro_lixeira=0,
	                   pro_generico_lixeira_em=null,
	                   pro_generico_lixeira_por=null
	                   WHERE pro_generico_codigo='$codigo_conta'";
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
	$sql = ("UPDATE tabela_produto_generico SET
				pro_generico_descricao='$descricao',
				pro_codigo_modalidade='$modalidade',
				pro_generico_alterado_em='$data_sistema',
				pro_generico_alterado_por='$nomeusuario'
				WHERE pro_generico_codigo='$codigo_conta'");
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
	$sql = "INSERT INTO tabela_produto_generico (
			pro_generico_descricao,
			pro_codigo_modalidade,
			pro_generico_incluido_em,
			pro_generico_incluido_por,
			pro_generico_registro_lixeira
	        )
		    VALUES (
				    '$descricao',
				    '$modalidade',
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