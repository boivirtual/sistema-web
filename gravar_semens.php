<?php 

	$codigo = $_POST['codigo_semem'];
	$nome = $_POST['nome_semem'];
	//$codigo_alfa = $_POST['codigo_alfa'];
	$raca_id = $_POST['raca_id'];
	$registro = $_POST['registro_semem'];
	$tipo_gravacao = $_POST['tipo_gravacao'];
	$ativo = $_POST['animal_ativo'];	
	$data_sistema = date("Y-m-d H:i:s");
	$_SESSION['abre_inclusao'] = false;

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	if ($codigo && $tipo_gravacao == '2') {
		$sql = "UPDATE tbl_semem SET 
	                   tbl_semem_lixeira=1,
	                   tbl_semem_lixeira_em='$data_sistema',
	                   tbl_semem_lixeira_por='$nomeusuario',
	                   tbl_semem_ativo='$ativo'
	                   WHERE tbl_semem_codigo_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Semem enviado para lixeira com sucesso.');
  	    $_SESSION['abre_inclusao'] = false;

	} else if ($codigo && $tipo_gravacao == '3') { 
	    $sql = "UPDATE tbl_semem SET 
	                   tbl_semem_lixeira=0,
	                   tbl_semem_lixeira_em=null,
	                   tbl_semem_lixeira_por=null,
	                   tbl_semem_ativo='$ativo'
	                   WHERE tbl_semem_codigo_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Semem restaurado com sucesso.');
   	    $_SESSION['abre_inclusao'] = false;

	} else if ($codigo) {
	    $sql = "UPDATE tbl_semem SET
	            tbl_semem_nome='$nome',
	            tbl_semem_codigo_raca='$raca_id',
	            tbl_semem_registro='$registro',
	            tbl_semem_alterado_em='$data_sistema',
	            tbl_semem_alterado_por='$nomeusuario',
	            tbl_semem_ativo='$ativo'
	            WHERE tbl_semem_codigo_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Semem alterado com sucesso.');
	    $_SESSION['abre_inclusao'] = false;

	} else {
	    $sql = "INSERT INTO tbl_semem (
	    	tbl_semem_nome,
	        tbl_semem_codigo_raca,
	        tbl_semem_registro,
	        tbl_semem_lixeira,
	        tbl_semem_incluido_em,
	        tbl_semem_incluido_por,
	        tbl_semem_ativo
	        ) VALUES (
	          '$nome',      
	          '$raca_id',
	          '$registro',
	          0,
	          '$data_sistema',
	          '$nomeusuario',
	          '$ativo'
	    )";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Semem incluído com sucesso.');
	    $_SESSION['abre_inclusao'] = true;
	}
	  
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao processar sua solicitação.' . $erro_mysql));
	} else {
	    header('Content-type: application/json');
	    echo json_encode($resposta);
	}

	mysqli_close($conector);
?>