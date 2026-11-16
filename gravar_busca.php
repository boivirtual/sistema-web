<?php 
function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

$tipo_gravacao = $_POST['tipo_gravacao'];
$palavra_chave = $_POST['palavras'];
$palavra_chave_anterior = $_POST['palavra_anterior'];

$palavra_chave = tirarAcentos($palavra_chave);

if (isset($_POST['programa'])) {
	$programas = implode(', ', $_POST['programa']);
}
else {
	$programas = '';
}

if (empty($palavra_chave)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Palavra-chave.'));
	exit; 
}

if (empty($programas)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Selecione o(s) Programa(s).'));
	exit; 
}

$matriz_programas = explode(",", $programas);
$quantidade_programas = count($matriz_programas);

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("DELETE FROM tbl_ajuda
	WHERE palavra_chave_ajuda ='$palavra_chave_anterior'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao excluir o registro antes de alterar. ' . $erro_mysql));
		exit;
	}

	for ($k=0; $k < $quantidade_programas; $k++) { 
        $codigo_url = $matriz_programas[$k];

		$sql = "INSERT INTO tbl_ajuda (palavra_chave_ajuda, 
		                               codigo_url_ajuda,
		                               array_programas_ajuda
		                                 ) 
		                          VALUES ('$palavra_chave',
		                                  '$codigo_url',
		                                  '$programas'
		                                 )";
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

	 	if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Erro ao alterar o registro. ' . $erro_mysql));
			exit;
		}
    }
	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Registro alterado com sucesso.'));
}
else{
	for ($k=0; $k < $quantidade_programas; $k++) { 
        $codigo_url = $matriz_programas[$k];

		$sql = "INSERT INTO tbl_ajuda (palavra_chave_ajuda, 
		                               codigo_url_ajuda,
		                               array_programas_ajuda
		                                 ) 
		                          VALUES ('$palavra_chave',
		                                  '$codigo_url',
		                                  '$programas'
		                                 )";
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

	 	if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Erro ao incluir o registro. ' . $erro_mysql));
			exit;
		}
    }
	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Registro incluído com sucesso.'));
}

mysqli_close($conector);

?>