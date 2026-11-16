<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);
$str = '';
$total_animais=0;
$codigo_pasto = 0;

$sql = "SELECT * FROM tbl_animal_pasto 
	INNER JOIN tbl_pasto
	        ON tbl_pasto_id = tbl_animal_pasto_id
		 WHERE tbl_animal_pasto_local ='$local' AND 
		       tbl_animal_pasto_situacao = 'A' AND
               tbl_pasto_tipo_curral='S'";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if($num_rows != 0){
	$ln = mysqli_fetch_assoc($qr);

    $codigo_pasto = $ln['tbl_pasto_id'];
    $total_animais=$num_rows;
    /*$array_categoria = $ln['tbl_pasto_array_categoria'];
    $array_qtd_macho = explode("!", $ln['tbl_pasto_array_qtd_animais_macho']);
    $array_qtd_femea = explode("!", $ln['tbl_pasto_array_qtd_animais_femea']);

    for ($j=0; $j<5; $j++) { 
	    if ($array_qtd_macho[$j]=='') {
	    	$qtd_macho = 0;
	    }
	    else {
		    $qtd_macho = intval($array_qtd_macho[$j]);
	    }

		if ($array_qtd_femea[$j]=='') {
	    	$qtd_femea = 0;
	    }
	    else {
		    $qtd_femea = intval($array_qtd_femea[$j]);
	    }

	    $total_animais+=$qtd_macho;  
	    $total_animais+=$qtd_femea;  
    }*/
}

$valor[0]=$total_animais;
$valor[1]=$codigo_pasto;
$str=$valor[0] . '<|>' . $valor[1];

echo $str; 
mysqli_close($conector);
 
?>
