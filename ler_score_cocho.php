<?php

include "conecta_mysql.inc";

$objCocho = mysqli_query($conector, "SELECT * FROM tbl_score_cocho");

echo "<option value='000000000'>...</option>";

while($regCocho = mysqli_fetch_object($objCocho)){
    echo"<option value='$regCocho->tbl_score_id'>$regCocho->tbl_score_descricao</option>";
}

?>