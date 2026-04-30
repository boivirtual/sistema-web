<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data_diagnostico = date("Y-m-d");

include "conecta_mysql.inc";

$cobertura_numero_id = $_POST['cobertura_numero_id'];
$ordem = $_POST['ordem'];
$codigo_id_animal = $_POST['codigo_id'];
$codigo_animal = $_POST['codigo_animal'];
$estacao_monta = $_POST['estacao_monta'];
$local = $_POST['local'];

$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
    WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id' AND 
    	  tbl_ite_cobertura_numero_item = '$ordem'");

$erro_mysql = mysqli_error($conector);
$num_rows_item = mysqli_num_rows($tbl_item);    

if ($num_rows_item!=0) {
	$sql = "UPDATE tbl_item_cobertura SET
        tbl_ite_cobertura_resultado_diagnostico = 'P',
        tbl_ite_cobertura_data_diagnostico = '$data_diagnostico',
        tbl_ite_cobertura_qtd_diagnosticos_positivo = 1,
        tbl_ite_cobertura_positivo_alterado_em = '$data_sistema',
        tbl_ite_cobertura_positivo_alterado_por = '$nomeusuario'
        WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id' AND 
        	  tbl_ite_cobertura_numero_item = '$ordem'";

    $resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	 	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do item de cobertura!' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	$tbl_item_positivo = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura_diagnostico_positivo
	    WHERE tbl_ite_cobertura_diagnostico_numero_id  = '$cobertura_numero_id' AND 
	    	  tbl_ite_cobertura_diagnostico_numero_item = '$ordem' AND 
	    	  tbl_ite_cobertura_diagnostico_data_diagnostico = '$data_diagnostico'");

	$num_rows_item_positivo = mysqli_num_rows($tbl_item_positivo);    

	if ($num_rows_item_positivo==0) {
		$sql = "INSERT INTO tbl_item_cobertura_diagnostico_positivo(
		    tbl_ite_cobertura_diagnostico_numero_id,
		    tbl_ite_cobertura_diagnostico_numero_item,
		    tbl_ite_cobertura_diagnostico_data_diagnostico,
		    tbl_ite_cobertura_diagnostico_codigo_id_animal,
		    tbl_ite_cobertura_diagnostico_codigo_animal,
		    tbl_ite_cobertura_diagnostico_confirmado_em,
		    tbl_ite_cobertura_diagnostico_data_confirmado_por
		)VALUES(
		    '$cobertura_numero_id',
		    '$ordem',
		    '$data_diagnostico',
		    '$codigo_id_animal',
		    '$codigo_animal',
		    '$data_sistema',
		    '$nomeusuario'
		)";
		
	    $resultado = mysqli_query($conector, $sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		 	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação do diagnostico positivo!' . $erro_mysql));
			mysqli_close($conector);
			exit;

		}
	}

    $resposta = array('success' => true, 'message' => 'Sua solicitação foi processada com sucesso!');
   	header('Content-type: application/json');
   	echo json_encode($resposta);
	mysqli_close($conector);
	exit;
}
else {
 	header('Content-type: application/json');
   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na leitura do item de cobertura!' . $erro_mysql));
	mysqli_close($conector);
	exit;
}

?>