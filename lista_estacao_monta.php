<?php
include "conecta_mysql.inc";

$local = $_POST['local'];  
$estacao_monta = $_POST['estacao_monta'];  

$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
	WHERE tbl_par_codigo_local = '$local' AND 
          tbl_par_lixeira=0
    ORDER BY tbl_par_estacao_monta_inicial ASC");  

$num_rows = mysqli_num_rows($sql);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($sql)){
   		$id = $ln['tbl_par_estacao_id'];
        $nome = $ln['tbl_par_estacao_nome'];

        if ($estacao_monta!='') {
        	if ($estacao_monta==$id) {
				echo '<option value="'.$id.'" selected>' .$nome. '</option>';
        	}
        	else {
				echo '<option value="'.$id.'">' .$nome. '</option>';
        	}
        }
        else {
			echo '<option value="'.$id.'" selected>' .$nome. '</option>';
        }
   	}
}

mysqli_close($conector);

 
?>
