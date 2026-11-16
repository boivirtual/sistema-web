<?php

include "conecta_mysql.inc";
$data = date("Y-m-d");

$pasto = $_POST["pasto"];

$objPasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
	WHERE tbl_pasto_id = $pasto AND
		  tbl_pasto_descricao_lote != ''");

echo mysqli_num_rows($objPasto);

mysqli_close($conector);

?>