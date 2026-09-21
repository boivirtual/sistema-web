<?php
include "conecta_mysql.inc";

$local = $_POST['local'];  
$data_nascimento = $_POST['data_nascimento'];  
$estacao_monta = 0;

// pega estacao conforme a data do nascimento para deixar selecionada automaticamente
$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
    WHERE tbl_par_codigo_local = '$local' AND 
          tbl_par_lixeira=0 AND
          tbl_par_estacao_monta_inicial<='$data_nascimento' AND
          tbl_par_estacao_monta_final>='$data_nascimento'"); 

$num_rows = mysqli_num_rows($sql);

if ($num_rows!=0) {
    $ln = mysqli_fetch_assoc($sql);
    $estacao_monta = $ln['tbl_par_estacao_id'];
}

$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
	WHERE tbl_par_codigo_local = '$local' AND 
          tbl_par_lixeira=0
    ORDER BY tbl_par_estacao_monta_inicial ASC");  

$num_rows = mysqli_num_rows($sql);
//echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($sql)){
   		$id = $ln['tbl_par_estacao_id'];
        $nome = $ln['tbl_par_estacao_nome'];

        if ($estacao_monta!=0) {
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
