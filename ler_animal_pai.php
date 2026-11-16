<?php
include "conecta_mysql.inc";

$codigo_pai = $_POST['codigo_pai'];  
$valor[0]=0;
$valor[1]='';

$tbl_semen = mysqli_query($conector, "SELECT * FROM tbl_semem
                                           WHERE tbl_semem_codigo_id ='$codigo_pai'");
$num_rows = mysqli_num_rows($tbl_semen);	

if ($num_rows!=0) {
	$reg_semen = mysqli_fetch_object($tbl_semen);
	$valor[0]=1;
	$valor[1]= $reg_semen->tbl_semem_nome;

	$str=$valor[0] . '<|>' . $valor[1];
	echo $str; 
	exit;
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                                           WHERE tbl_animal_codigo_id  ='$codigo_pai'");
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