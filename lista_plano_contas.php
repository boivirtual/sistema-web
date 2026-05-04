<?php
include "conecta_mysql.inc";

$conta_pri = $_POST['codigo_pri'];
$conta = $_POST['codigo_pla'];
$nivel =  $_POST['nivel'];
$ultimo_cadastrado=0;

if ($nivel==2) {
	$sql = "SELECT * FROM tbl_plano_contas WHERE tbl_plano_contas_nivel = '$nivel' AND 
											 	 tbl_plano_contas_lixeira = 0
	        							ORDER BY tbl_plano_contas_codigo_id ASC";  
}
else {
	$sql = "SELECT * FROM tbl_plano_contas WHERE tbl_plano_contas_nivel = '$nivel' AND 
											 	 tbl_plano_contas_lixeira = 0
	        							ORDER BY tbl_plano_contas_codigo_id ASC";  
}

$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if($num_rows == 0){
	echo  '<option value="">'.htmlentities('Não existem contas para listar').'</option>';
	if ($nivel==3) {
		echo '</select>';
	}
}
else {
	if ($nivel==2) {
		echo '<option value="00">...</option>';
	}
	else {
        echo '<label for="selecione_ter" class="control-label">&nbsp;</label>';                                
        echo '<select class="form-control" id="selecione_ter" name="selecione_ter">';
		echo '<option value="0000000">Contas Cadastradas</option>';
	}   

   	while($ln = mysqli_fetch_assoc($qr)){

        $codigo = $ln['tbl_plano_contas_codigo_id'];

        if (substr($codigo, 0,1) == $conta && $nivel==2) {
        	$codigo = substr($codigo, 1,2);
        	$descricao = $ln['tbl_plano_contas_descricao'];
	    	echo '<option value=" '.$codigo.' ">' . $codigo .' - '. $descricao. '</option>';
        }
        else if (substr($codigo, 1,2) == $conta && substr($codigo, 0,1) == $conta_pri && $nivel==3){
        	$descricao = $ln['tbl_plano_contas_descricao'];
        	$ultimo_cadastrado = substr($codigo, 3,4);
	    	echo '<option value=" '.$codigo.' ">' . $codigo .' - '. $descricao. '</option>';
        }
   	}

   	if ($nivel==3) {
   		echo '</select>';
   		$ultimo_cadastrado++;
   		echo '<input name="codigo_ultimo" id="codigo_ultimo" type="hidden" class="form-control" value=" '.$ultimo_cadastrado.' ">';
   	}
}
mysqli_close($conector);

 
?>
