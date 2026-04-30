<?php 
include "conecta_mysql.inc";

$descriao_pasto = $_POST['descricao_pasto'];

$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
	WHERE tbl_pasto_descricao='$descriao_pasto' AND 
		  tbl_pasto_lixeira=0");

$num_rows_pasto = mysqli_num_rows($tbl_pasto);	

if ($num_rows_pasto==0) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Não existe pasto cadastrado'));
	exit;
}

$reg_pasto = mysqli_fetch_object($tbl_pasto);
$id_pasto = $reg_pasto->tbl_pasto_id;
$id_pasto = strval($id_pasto);

$resposta = array('success' => true, 'message' => 'Sucesso', 'id' => $id_pasto);
header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;

?>