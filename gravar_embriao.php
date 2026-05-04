<?php 
$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_id = $_POST['codigo_embriao'];
$lote = $_POST['lote'];
$raca = $_POST['raca_id'];
$tipo_1 = $_POST['tipo_1'];
$tipo_2 = $_POST['tipo_2'];
$doadora = $_POST['doadora'];
$touro = $_POST['touro'];
$laboratorio = $_POST['laboratorio'];
$cliente = $_POST['cliente'];
$fazenda = $_POST['fazenda'];

$data_sistema = date("Y-m-d H:i:s");

if ($lote==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Lote do Embrião.'));
	exit;
}

if ($raca=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Raça.'));
	exit;
}

if ($tipo_1==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe Tipo do Embrião.'));
	exit;
}

if ($tipo_2==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Conservação.'));
	exit;
}

if ($doadora==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Doadora.'));
	exit;
}

if ($touro==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Touro.'));
	exit;
}

if ($laboratorio==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Laboratório Aspirador.'));
	exit;
}

if ($cliente=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Cliente.'));
	exit;
}

if ($fazenda==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Fazenda.'));
	exit;
}


@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_embriao SET 
	                   tbl_embriao_lixeira=1,
	                   tbl_embriao_lixeira_em='$data_sistema',
	                   tbl_embriao_lixeira_por='$nomeusuario'
	                   WHERE tbl_embriao_id='$codigo_id'";
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
		$sql = "UPDATE tbl_embriao SET 
	                   tbl_embriao_lixeira=0,
	                   tbl_embriao_lixeira_em=null,
	                   tbl_embriao_lixeira_por=null
	                   WHERE tbl_embriao_id='$codigo_id'";
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
	$sql = ("UPDATE tbl_embriao SET 
		tbl_embriao_lote='$lote',
		tbl_embriao_doadora='$doadora',
		tbl_embriao_touro='$touro',
		tbl_embriao_laboratorio_aspirador='$laboratorio',
		tbl_embriao_codigo_raca='$raca',
		tbl_embriao_codigo_cliente='$cliente',
		tbl_embriao_fazenda='$fazenda',
		tbl_embriao_tipo_1='$tipo_1',
		tbl_embriao_tipo_2='$tipo_2',
		tbl_embriao_alterado_em='$data_sistema',
		tbl_embriao_alterado_por='$nomeusuario'
		WHERE tbl_embriao_id='$codigo_id'");

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
	$sql = "INSERT INTO tbl_embriao (
		tbl_embriao_lote,
		tbl_embriao_doadora,
		tbl_embriao_touro,
		tbl_embriao_laboratorio_aspirador,
		tbl_embriao_codigo_raca,
		tbl_embriao_codigo_cliente,
		tbl_embriao_fazenda,
		tbl_embriao_tipo_1,
		tbl_embriao_tipo_2,
		tbl_embriao_incluido_em,
		tbl_embriao_incluido_por,
		tbl_embriao_alterado_em,
		tbl_embriao_alterado_por,
		tbl_embriao_lixeira,
		tbl_embriao_lixeira_em,
		tbl_embriao_lixeira_por
	    ) 
		    VALUES (
		'$lote',
		'$doadora',
		'$touro',
		'$laboratorio',
		'$raca',
		'$cliente',
		'$fazenda',
		'$tipo_1',
		'$tipo_2',
		'$data_sistema',
		'$nomeusuario',
		null,
		null,
		0,
		null,
		null
    )";

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
		mysqli_close($conector);
		exit;
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