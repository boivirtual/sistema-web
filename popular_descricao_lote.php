<?php
include "conecta_mysql.inc";

$sql = "SELECT * FROM tbl_descricao_lote_animais 
	WHERE tbl_descricao_lote_lixeira=0";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

echo  '<option value="00">'.htmlentities('Selecione').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['tbl_descricao_lote_id'];
        $descricao = $ln['tbl_descricao_lote'];
	    echo '<option value="'.$id.'">' .$descricao. '</option>';
	}
}                    	

mysqli_close($conector);

 
?>
