<?php 
$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_conta = $_POST['codigo_conta'];
$descricao = $_POST['descricao'];
$aparencia = $_POST['aparencia'];

$data_sistema = date("Y-m-d H:i:s");

if ($aparencia==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Aparência.'));
	exit;
}

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
		$sql = "UPDATE tbl_escore_corporal SET 
	                   tbl_escore_lixeira=1,
	                   tbl_escore_lixeira_em='$data_sistema',
	                   tbl_escore_lixeira_por='$nomeusuario'
	                   WHERE tbl_escore_id='$codigo_conta'";
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
		$sql = "UPDATE tbl_escore_corporal SET 
	                   tbl_escore_lixeira=0,
	                   tbl_escore_lixeira_em=null,
	                   tbl_escore_lixeira_por=null
	                   WHERE tbl_escore_id='$codigo_conta'";
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
	$sql = ("UPDATE tbl_escore_corporal SET 
				tbl_escore_descricao='$descricao',
				tbl_escore_aparencia='$aparencia',
				tbl_escore_alterado_em='$data_sistema',
				tbl_escore_alterado_por='$nomeusuario'
				WHERE tbl_escore_id='$codigo_conta'");
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
	$sql = "INSERT INTO tbl_escore_corporal (
			tbl_escore_descricao,
			tbl_escore_aparencia,
			tbl_escore_incluido_em,
			tbl_escore_incluido_por,
			tbl_escore_lixeira
	        ) 
		    VALUES (
				    '$descricao',
					'$aparencia',
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