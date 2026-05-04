<?php 

$codigo_grupo= $_POST['codigo_grupo'];
$descricao_grupo = $_POST['descricao_grupo'];
$codigo_estacao_monta = $_POST['codigo_estacao_grupo'];
$codigo_local = $_POST['codigo_local_grupo'];
$tipo_gravacao = $_POST['tipo_gravacao_grupo'];

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao == 1) {
	$sql = ("UPDATE tbl_grupo_estacao_monta SET 
					tbl_grupo_descricao='$descricao_grupo'
			WHERE tbl_grupo_id = '$codigo_grupo' AND 
			      tbl_grupo_codigo_estacao_monta = '$codigo_estacao_monta' AND 
			      tbl_grupo_codigo_local = '$codigo_local'");

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração ' . $erro_mysql));
	    exit;
	} 
}
else if($tipo_gravacao == 2) {
	$sql = ("DELETE FROM tbl_grupo_estacao_monta
	               WHERE tbl_grupo_id = '$codigo_grupo' AND 
			             tbl_grupo_codigo_estacao_monta = '$codigo_estacao_monta' AND 
			             tbl_grupo_codigo_local = '$codigo_local'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluido com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão ' . $erro_mysql));
	    exit;
	} 
}
else {
    $sql = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
			      WHERE tbl_grupo_id = '$codigo_grupo' AND 
			            tbl_grupo_codigo_estacao_monta = '$codigo_estacao_monta' AND 
			            tbl_grupo_codigo_local = '$codigo_local'");

    $num_row = mysqli_num_rows($sql);

    if ($num_row!=0) {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Já existe grupo com esse código.'));
	    exit;
	}

	$sql = "INSERT INTO tbl_grupo_estacao_monta (
	   				tbl_grupo_id,
					tbl_grupo_codigo_estacao_monta,
					tbl_grupo_descricao,
					tbl_grupo_codigo_local
				    ) 
				VALUES (
					'$codigo_grupo',
					'$codigo_estacao_monta',
					'$descricao_grupo',
					'$codigo_local'
				)";

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
	   	exit;
	} 
}

header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;

?>