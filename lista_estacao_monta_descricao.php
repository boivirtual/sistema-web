<?php
include "conecta_mysql.inc";

$estacao_anterior = '';

$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
	WHERE tbl_par_lixeira=0
    ORDER BY tbl_par_estacao_nome ASC");  

$num_rows = mysqli_num_rows($sql);
//echo  '<option value="">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($sql)){
        $nome = $ln['tbl_par_estacao_nome'];

        if ($estacao_anterior!=$nome) {
			echo '<option value="'.$nome.'" selected>' .$nome. '</option>';
            $estacao_anterior=$nome;
        }
   	}
}

mysqli_close($conector);

?>
