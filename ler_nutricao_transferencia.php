<?php

include "conecta_mysql.inc";
$data = date("Y-m-d");

$pasto = $_POST["pasto"];

$objNutricao = mysqli_query($conector, "SELECT * FROM tbl_nutricao WHERE tbl_nutricao_codigo_pasto = $pasto AND tbl_nutricao_data = '$data'");

echo mysqli_num_rows($objNutricao);

mysqli_close($conector);

?>