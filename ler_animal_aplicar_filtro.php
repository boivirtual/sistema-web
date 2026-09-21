<?php
// Verifica se o animal foi digitado corretamente na tela de filtros
// form_cadastro_animais.php  
include "conecta_mysql.inc";

if (isset($_POST['id_animal'])) {
	$codigo_alfa_numerico = $_POST['id_animal'];  
}
else {
	mysqli_close($conector);
	exit;	
}

$codigo_numerico = substr($codigo_alfa_numerico, -9);

if (strlen($codigo_alfa_numerico)!=9){
	$data = explode("-", $codigo_alfa_numerico);
	$codigo_alfa = $data[0];
}
else {
	$codigo_alfa = '';
}

$sql = "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
          tbl_animal_codigo_numerico='$codigo_numerico'";

$tbl_animal = mysqli_query($conector, $sql);
$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows==0) {
	echo $num_rows;
	mysqli_close($conector);
	exit;	
}
else {
	$reg_animal = mysqli_fetch_object($tbl_animal);
	echo $reg_animal->tbl_animal_codigo_id;
	mysqli_close($conector);
	exit;	
}

?>