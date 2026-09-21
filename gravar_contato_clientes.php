<?php 

	$codigo_cliente = $_POST['codigo_cliente'];
	$codigo_contato = $_POST['codigo_contato'];
	$nome = $_POST['nome_contato'];
	$cargo = $_POST['cargo_contato'];
	$email = $_POST['email_contato'];
	$ddd = $_POST['ddd_contato'];
	$telefone = $_POST['telefone_contato'];
	$tipo_gravacao = $_POST['tipo_gravacao_contato'];

    $data_sistema = date("Y-m-d H:i:s");
	$_SESSION['abre_inclusao'] = false;

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	if ($codigo_contato && $tipo_gravacao == '2') {
		$sql = "UPDATE contatos_cliente_fornecedor SET 
	                   contato_cliente_registro_lixeira=1,
	                   contato_cliente_lixeira_em='$data_sistema',
	                   contato_cliente_lixeira_por='$nomeusuario'
	                   WHERE contato_cliente_id='$codigo_cliente' AND contato_id='$codigo_contato'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Contato enviado para lixeira com sucesso.');
  	    $_SESSION['abre_inclusao'] = false;

	} else if ($codigo_contato && $tipo_gravacao == '3') { 
	    $sql = "UPDATE contatos_cliente_fornecedor SET 
	                   contato_cliente_registro_lixeira=0,
	                   contato_cliente_lixeira_em=null,
	                   contato_cliente_lixeira_por=null
	                   WHERE contato_cliente_id='$codigo_cliente' AND contato_id='$codigo_contato'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Contato restaurado com sucesso.');
   	    $_SESSION['abre_inclusao'] = false;

	} else if ($codigo_contato) {
	    $sql = "UPDATE contatos_cliente_fornecedor SET
	            contato_cliente_nome='$nome',
	            contato_cliente_cargo='$cargo',
	            contato_cliente_ddd='$ddd',
	            contato_cliente_telefone='$telefone',
	            contato_cliente_email='$email'
	            WHERE contato_cliente_id='$codigo_cliente' AND contato_id='$codigo_contato'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Contato alterado com sucesso.');

	} else {
	    $sql = "INSERT INTO contatos_cliente_fornecedor (
	        contato_cliente_id,
			contato_cliente_nome,
			contato_cliente_ddd,
			contato_cliente_telefone,
			contato_cliente_email,
			contato_cliente_cargo,
			contato_cliente_registro_lixeira,
			contato_cliente_incluido_em,
			contato_cliente_incluido_por,
			contato_cliente_lixeira_em,
			contato_cliente_lixeira_por,
			contato_cliente_alterado_em,
			contato_cliente_alterado_por
	        ) VALUES (
	          '$codigo_cliente',
	          '$nome', 
	          '$ddd',
	          '$telefone',
	          '$email',
	          '$cargo',
	          '0',
	          '$data_sistema',
	          '$nomeusuario',
	          null,
	          null,
	          null,
	          null
	    )";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Contato incluído com sucesso.');
	}
	  
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao processar sua solicitação.'));
	} else {
	    header('Content-type: application/json');
	    echo json_encode($resposta);
	}

	mysqli_close($conector);
?>