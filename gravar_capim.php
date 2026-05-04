<?php 
$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_conta = $_POST['codigo_conta'];
$descricao = $_POST['descricao'];

$data_sistema = date("Y-m-d H:i:s");

if ($descricao==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Descrição.'));
	exit;
}

if (empty($area)){$area=0;}

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_tipo_capim SET 
	                   tbl_tipo_capim_lixeira=1,
	                   tbl_tipo_capim_lixeira_em='$data_sistema',
	                   tbl_tipo_capim_lixeira_por='$nomeusuario'
	                   WHERE tbl_tipo_capim_id='$codigo_conta'";
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
		$sql = "UPDATE tbl_tipo_capim SET 
	                   tbl_tipo_capim_lixeira=0,
	                   tbl_tipo_capim_lixeira_em=null,
	                   tbl_tipo_capim_lixeira_por=null
	                   WHERE tbl_tipo_capim_id='$codigo_conta'";
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
	$sql = ("UPDATE tbl_tipo_capim SET 
				tbl_tipo_capim_descricao='$descricao',
				tbl_tipo_capim_alterado_em='$data_sistema',
				tbl_tipo_capim_alterado_por='$nomeusuario'
				WHERE tbl_tipo_capim_id='$codigo_conta'");
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
	$sql = "INSERT INTO tbl_tipo_capim (
			tbl_tipo_capim_descricao,
			tbl_tipo_capim_incluido_em,
			tbl_tipo_capim_incluido_por,
			tbl_tipo_capim_lixeira
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