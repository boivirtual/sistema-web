<?php

include "conecta_mysql.inc";

$numero_doc = str_pad($_POST['num_doc'], 9, "0", STR_PAD_LEFT);  

for ($i = 0; $i <= 30; $i++) {
	$valor[$i]=0;
}

$rs = mysqli_query($conector, "SELECT * FROM tbl_movimentacao
						   WHERE tbl_movimentacao_id  ='$numero_doc'");
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($fila = mysqli_fetch_object($rs)){
    	$codigo_origem = $fila->tbl_movimentacao_codigo_local_origem;
    	$codigo_destino = $fila->tbl_movimentacao_codigo_local_destino;

		$valor[0]= $codigo_origem;
		$valor[1]= $codigo_destino;
		$str=$valor[0] . '<|>' . $valor[1];
		echo $str; 
	}					
}
else {
	$str = 0;
	echo $str;
}

mysqli_free_result($rs); 
mysqli_close($conector);
?>