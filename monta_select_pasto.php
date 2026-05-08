<?php
include "conecta_mysql.inc";

$local_id = $_POST["local_id"];
$query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_codigo_local = $local_id AND tbl_pasto_lixeira = 0";
$request = mysqli_query($conector, $query);

echo 
"<option value='000000000'>...</option>";

while($reg_pastos = mysqli_fetch_object($request)){
    $nome = $reg_pastos->tbl_pasto_descricao;
    echo 
    "<option value=$reg_pastos->tbl_pasto_id>$nome</option>";
}
?>