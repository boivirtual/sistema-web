<?php
include "conecta_mysql.inc";

$data_sistema = date("Y-m-d");
$str='';
$codigo_local = $_POST['local'];
$estacao_monta = $_POST['estacao_monta'];

if ($estacao_monta=='') {
	$tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
						WHERE tbl_par_codigo_local='$codigo_local' AND 
						      tbl_par_lixeira=0 AND 
						      tbl_par_estacao_monta_final>='$data_sistema'");  

	$num_rows = mysqli_num_rows($tbl_par);

	if ($num_rows!=0){
		$reg_para = mysqli_fetch_object($tbl_par);

		$valor[0] = $reg_para->tbl_par_estacao_id;
		$valor[1] = $reg_para->tbl_par_estacao_nome;
		$valor[2] = $reg_para->tbl_par_estacao_monta_inicial;
		$valor[3] = $reg_para->tbl_par_estacao_monta_final;

		$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3];
		echo $str; 
		exit;
	}
}
else {
	$tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
						WHERE tbl_par_estacao_id='$estacao_monta' AND 
						      tbl_par_lixeira=0");  

	$num_rows = mysqli_num_rows($tbl_par);

	if ($num_rows!=0){
		$reg_para = mysqli_fetch_object($tbl_par);

		$valor[0] = $reg_para->tbl_par_estacao_id;
		$valor[1] = $reg_para->tbl_par_estacao_nome;
		$valor[2] = $reg_para->tbl_par_estacao_monta_inicial;
		$valor[3] = $reg_para->tbl_par_estacao_monta_final;

		$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3];
		echo $str; 
		exit;
	}
}

echo $str; 

?>
