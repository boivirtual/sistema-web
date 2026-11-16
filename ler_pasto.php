<?php
include "conecta_mysql.inc";

$codigo_pasto = ltrim($_POST['codigo_pasto']);
$str = '';

$sql = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id='$codigo_pasto'";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if($num_rows != 0){
	$ln = mysqli_fetch_assoc($qr);
    $valor[0] = $ln['tbl_pasto_array_categoria'];
    $valor[1] = $ln['tbl_pasto_array_qtd_animais_macho'];
    $valor[2] = $ln['tbl_pasto_array_qtd_animais_femea'];
    $valor[3] = $ln['tbl_pasto_array_qtd_animais_ambos'];

	$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . $valor[3];
}

echo $str; 
mysqli_close($conector);
 
?>
