<?php
include "conecta_mysql.inc";

//$local = ltrim($_POST['local']);

$wlocal = "";
if (isset($_POST['local'])) {
    $local = $_POST['local'];

    if(in_array("", $local)) {
        $wlocal='';
    }
    else {
        $wlocal = " AND tbl_pasto_codigo_local IN(";
        $wlocal.= implode(',', $local);
        $wlocal.= ")";
        }
}

$sql = "SELECT * FROM tbl_pasto 
        WHERE (tbl_pasto_tipo_curral IS NULL OR tbl_pasto_tipo_curral = '') AND 
              tbl_pasto_modulo!=1006 " . $wlocal . "
        ORDER BY tbl_pasto_descricao ASC";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if($num_rows != 0){
    while($ln = mysqli_fetch_assoc($qr)){
        $id = $ln['tbl_pasto_id'];
        $descricao = $ln['tbl_pasto_descricao'];
        echo '<option value="'.$id.'">' .$descricao. '</option>';
    }
}

mysqli_close($conector);

 
?>
