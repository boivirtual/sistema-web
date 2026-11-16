<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);
$controle_estoque= $_SESSION['controle_estoque'];

$sql = "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_codigo_local = '$local' AND 
              (tbl_pasto_tipo_curral = 'E' OR 
              tbl_pasto_tipo_curral = 'S')
        ORDER BY tbl_pasto_descricao ASC";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['tbl_pasto_id'];
        $descricao = $ln['tbl_pasto_descricao'];
    	echo '<option value="'.$id.'">' .$descricao. '</option>';
   	}
}

$sql = "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_codigo_local = '$local' AND 
              (tbl_pasto_tipo_curral IS NULL OR tbl_pasto_tipo_curral = '') AND 
              tbl_pasto_modulo!=1006
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
