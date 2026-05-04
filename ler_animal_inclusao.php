<?php
include "conecta_mysql.inc";

$codigo_alfa = $_POST['codigo_alfa'];  
$codigo_animal = $_POST['codigo_animal'];  
$codigo_mae = $_POST['codigo_mae'];  
$valor[0]=0;
$valor[1]='';

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND tbl_animal_codigo_numerico='$codigo_animal' AND 
        	tbl_animal_codigo_mae='$codigo_mae'");
$num_rows = mysqli_num_rows($tbl_animal);	

if ($num_rows!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal); 
	$valor[0]=1;
	$valor[1]= $reg_animal->tbl_animal_nome;

	$str=$valor[0] . '<|>' . $valor[1];
	echo $str; 
	exit;
}

$str=$valor[0] . '<|>' . $valor[1];
echo $str; 

?>