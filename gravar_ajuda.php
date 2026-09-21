<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$palavra_chave = $_POST['palavras'];
$codigo_url = $_POST['codigo_selecionado'];

if ($tipo_gravacao==1){
	$codigo_id = $_POST['codigo_id'];
}

if (empty($palavra_chave)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Palavra-chave.'));
	exit; 
}

if (empty($codigo_url)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Programa.'));
	exit; 
}

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_ajuda SET descricao_ajuda='$palavra_chave',
		                          codigo_url_ajuda='$codigo_url'
 		                    WHERE id_ajuda ='$codigo_id'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao alterar o registro. ' . $erro_mysql));
		exit;
	}
    else {
    	header('Content-type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Registro alterado com sucesso.'));
    }
}
else{
	$sql = "INSERT INTO tbl_ajuda (descricao_ajuda, 
	                               codigo_url_ajuda
	                                 ) 
	                          VALUES ('$palavra_chave',
	                                  '$codigo_url'
	                                 )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao incluir o registro. ' . $erro_mysql));
		exit;
	}
    else {
    	header('Content-type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Registro incluído com sucesso.'));
	}    
}

mysqli_close($conector);

?>