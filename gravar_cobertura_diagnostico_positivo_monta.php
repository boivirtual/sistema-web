<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data_diagnostico = date("Y-m-d");
$mensagem = 0;

include "conecta_mysql.inc";

$cobertura_numero_id = $_POST['cobertura_numero_id'];
$local = $_POST['local'];
$data_prenhes = $_POST['data_prenhes'];
$previsao_parto = $_POST['previsao_parto'];

$sql = "UPDATE tbl_cobertura SET
    tbl_cobertura_encerrada='S'
    WHERE tbl_cobertura_id = '$cobertura_numero_id'";

$resultado = mysqli_query($conector, $sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
  	header('Content-type: application/json');
   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao alterar o registro da cobertura '. $erro_mysql));
	mysqli_close($conector);
	exit;
} 

$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
    WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id'");

$num_rows_item = mysqli_num_rows($tbl_item);    

if ($num_rows_item!=0) {
    $reg_item = mysqli_fetch_object($tbl_item);
    $qtd_diagnostico =  $reg_item->tbl_ite_cobertura_qtd_diagnosticos_positivo;
    $qtd_diagnostico++;

	$sql = "UPDATE tbl_item_cobertura SET
        tbl_ite_cobertura_data_prenhes='$data_prenhes',
		tbl_ite_cobertura_previsao_parto='$previsao_parto',
        tbl_ite_cobertura_resultado_diagnostico='P',
        tbl_ite_cobertura_data_diagnostico='$data_diagnostico',
        tbl_ite_cobertura_qtd_diagnosticos_positivo='$qtd_diagnostico',
        tbl_ite_cobertura_positivo_alterado_em='$data_sistema',
        tbl_ite_cobertura_positivo_alterado_por='$nomeusuario'

        WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id'";

    $resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao alterar o item do diagnóstico '. $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
	else {
	   	header('Content-type: application/json');
	   	echo json_encode(array('success' => true, 'message' => 'Diagnóstico confirmado com sucesso.'));
		mysqli_close($conector);
		exit;

	}

}
else {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Item para diagnóstico não encontrado '. $erro_mysql));
		mysqli_close($conector);
	exit;
}

?>