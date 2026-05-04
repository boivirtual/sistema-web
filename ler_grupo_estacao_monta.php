<?php
include "conecta_mysql.inc";

$str='';
$local = ltrim($_POST['local']);
$id_parametro_estacao = $_POST['id_parametro_estacao'];

$sql = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
                  WHERE tbl_grupo_id != 999 AND 
                        tbl_grupo_codigo_estacao_monta = '$id_parametro_estacao' AND 
                        tbl_grupo_codigo_local = '$local' ORDER BY tbl_grupo_id DESC LIMIT 1");

$num_row = mysqli_num_rows($sql);

if ($num_row==0) {
	$valor[0] = 1;
	$valor[1] = '';
	$valor[2] = '';
	$valor[3] = '';
	$valor[4] = '';

	$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3].'<|>'.$valor[4];
	echo $str; 
	exit;
}
else {
	$reg_grupo = mysqli_fetch_object($sql);
	$codigo_grupo = $reg_grupo->tbl_grupo_id;
	$valor[0] = 1;
	$valor[1] = $codigo_grupo;
	$valor[2] = '';
	$valor[3] = '';
	$valor[4] = '';

	$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3].'<|>'.$valor[4];
	echo $str; 
	exit;
}

echo $str; 

?>