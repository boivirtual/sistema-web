<?php 


$num_mov_nascimento = $_POST['num_mov_nascimento'];
$codigo_mae = $_POST['codigo_mae_animal'];
$local = $_POST['local_id'];
$ocorrencia = $_POST['opcao_nascimento'];
$cobertura_id = $_POST['cobertura_id'];
$item_cobertura = $_POST['item_cobertura'];

/*header('Content-type: application/json');
echo json_encode(array('error' => true, 'message' => 'Local: ' . $local . ' Mãe: ' . $codigo_mae . ' Ocorrencia: ' . $ocorrencia . ' Mov Estoque: ' . $num_mov_nascimento));
exit;*/

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($ocorrencia=='A' || $ocorrencia=='B') {
	$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	    WHERE tbl_animal_codigo_id ='$codigo_mae'");

	$num_rows_animal = mysqli_num_rows($tbl_animal);	

	if ($num_rows_animal!=0) {
		$reg_animal = mysqli_fetch_object($tbl_animal);
		$numero_aborto =  $reg_animal->tbl_animal_numero_abortos;
		$numero_aborto--;
	}
	else {
		$numero_aborto = 0;
	}

	$sql = ("UPDATE tbl_animais SET 
			tbl_animal_numero_abortos='$numero_aborto'
		WHERE tbl_animal_codigo_id ='$codigo_mae'");

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do número de abortos do animal.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	$sql = ("DELETE FROM tbl_movimentacao_estoque 
	           WHERE tbl_mov_estoque_numero_id ='$num_mov_nascimento'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do registro' . $erro_mysql));
		mysqli_close($conector);
	   	exit;
	}
}
else {
	$tbl_estoque = mysqli_query($conector, "select * from tbl_movimentacao_estoque
	    where tbl_mov_estoque_numero_id ='$num_mov_nascimento'"); 

	$reg_estoque = mysqli_fetch_object($tbl_estoque);
	$num_movimentacao = $reg_estoque->tbl_mov_estoque_codigo_movimentacao;

	$sql = ("DELETE FROM tbl_movimentacao_estoque 
	           WHERE tbl_mov_estoque_codigo_movimentacao ='$num_movimentacao'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do registro no estoque' . $erro_mysql));
		mysqli_close($conector);
	   	exit;
	}

	$sql = ("DELETE FROM tbl_movimentacao 
	           WHERE tbl_movimentacao_id ='$num_movimentacao'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do registro na movimentação' . $erro_mysql));
		mysqli_close($conector);
	   	exit;
	}

	$sql = ("DELETE FROM tbl_item_movimentacao 
	           WHERE tbl_ite_movimentacao_numero_id ='$num_movimentacao'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do registro no item de movimentação' . $erro_mysql));
		mysqli_close($conector);
	   	exit;
	}
}

// Altera o item de cobertura informando que houve aborto, absorção ou natimorto iatf/monta
$data_sistema = date("Y-m-d");

$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura 
	WHERE tbl_cobertura_lixeira=0 AND 
	      tbl_cobertura_id = '$cobertura_id'"); 

$num_rows = mysqli_num_rows($tbl_cobertura);

if ($num_rows!=0) {
	$reg_cobertura = mysqli_fetch_object($tbl_cobertura);	

	$controle = $reg_cobertura->tbl_cobertura_controle;

	if ($controle=='C') {
		$sql = ("UPDATE tbl_item_cobertura SET 
				tbl_ite_cobertura_aborto_natimorto=null,
				tbl_ite_cobertura_nascido=null
			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			      tbl_ite_cobertura_numero_item = '$item_cobertura'");
	}
	else {
		$sql = ("UPDATE tbl_item_cobertura SET 
				tbl_ite_cobertura_aborto_natimorto=null,
				tbl_ite_cobertura_nascido=null,
				tbl_ite_cobertura_resultado_diagnostico=null,
				tbl_ite_cobertura_data_prenhes=null,
				tbl_ite_cobertura_previsao_parto=null,
				tbl_ite_cobertura_negativo_em=null,
				tbl_ite_cobertura_negativo_por=null
			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			      tbl_ite_cobertura_numero_item = '$item_cobertura'");
	}

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}


/*$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
	INNER JOIN tbl_cobertura
	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		 WHERE 
			   tbl_cobertura_controle = 'C' AND
			   tbl_ite_cobertura_codigo_id_animal = '$codigo_mae' AND 
			   tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
			   tbl_ite_cobertura_aborto_natimorto = 1
		ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1"); 
 
$num_rows = mysqli_num_rows($tbl_item_cobertura);

if ($num_rows!=0){
    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
    $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
    $item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;

	$sql = ("UPDATE tbl_item_cobertura SET 
			tbl_ite_cobertura_aborto_natimorto=null,
			tbl_ite_cobertura_nascido=null
		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
		      tbl_ite_cobertura_numero_item = '$item_cobertura'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}*/

// Altera o item de cobertura limpando o nascimento quando for Nova Monta
/*$data_sistema = date("Y-m-d");

$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
	INNER JOIN tbl_cobertura
	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		 WHERE tbl_cobertura_controle = 'M' AND
			   tbl_ite_cobertura_codigo_id_animal = '$codigo_mae' AND 
			   tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
			   (tbl_ite_cobertura_nascido = 'A' OR tbl_ite_cobertura_nascido = 'M')"); 

$num_rows = mysqli_num_rows($tbl_item_cobertura);

if ($num_rows!=0){
    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
    $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
    $item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;

	$sql = ("UPDATE tbl_item_cobertura SET 
			tbl_ite_cobertura_aborto_natimorto=null,
			tbl_ite_cobertura_nascido=null,
			tbl_ite_cobertura_resultado_diagnostico=null,
			tbl_ite_cobertura_data_prenhes=null,
			tbl_ite_cobertura_previsao_parto=null
		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
		      tbl_ite_cobertura_numero_item = '$item_cobertura'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura Monta.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}*/

$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;

?>