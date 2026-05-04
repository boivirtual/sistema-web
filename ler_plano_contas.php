<?php
include "conecta_mysql.inc";

for ($i=1; $i<=7; $i++){
    $valor[$i]=0;
}

$codigo = $_POST['id'];  

$plano_conta= mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                  WHERE tbl_plano_contas_codigo_id='$codigo'");

$num_rows = mysqli_num_rows($plano_conta);	

if ($num_rows!=0) {
	$registro_plano = mysqli_fetch_object($plano_conta);

    $valor[0] = $registro_plano->tbl_plano_contas_nivel;

	$str=$valor[0] . '<|>';

	for ($i=1; $i<=7; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;
}					

$valor[0]=9;
$valor[1]='Código não cadastro no plano de contas.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>