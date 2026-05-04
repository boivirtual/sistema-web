<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);

$sql = mysqli_query($conector,"SELECT * FROM tbl_pasto 
        WHERE (tbl_pasto_tipo_curral IS NULL OR tbl_pasto_tipo_curral = '') AND 
              tbl_pasto_modulo!=1006 AND 
              tbl_pasto_codigo_local='$local'
        ORDER BY tbl_pasto_descricao ASC");  

$num_rows = mysqli_num_rows($sql);
//echo  '<option value="000000000">'.htmlentities('Todos').'</option>';

if($num_rows != 0){
    while($ln = mysqli_fetch_assoc($sql)){
        $id = $ln['tbl_pasto_id'];
        $descricao = trim($ln['tbl_pasto_descricao']);

        echo '<option value="'.$id.'">' .$descricao. '</option>';

    }
}

mysqli_close($conector);

 
?>
