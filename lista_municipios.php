<?php
include "conecta_mysql.inc";

$estado = $_POST['estado'];

$sql = "SELECT * FROM tabela_municipios WHERE mun_estado = '$estado' ORDER BY mun_nome ASC";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if($num_rows == 0){
	echo  '<option value="">'.htmlentities('Não existem municípios para este estado').'</option>';
}
else {
	echo '<option value="">...</option>';
   
   	while($ln = mysqli_fetch_assoc($qr)){

		$cidade = $ln['mun_nome'];

    	echo '<option value=" '.$cidade.' ">' .$cidade. '</option>';
   	}
}

//mysql_free_result($sql); 
mysqli_close($conector);

 
?>
