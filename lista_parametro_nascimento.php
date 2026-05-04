<?php
include "conecta_mysql.inc";

$str='';
$array_fazendas = array();
$array_alfa = array();
$array_numerico = array();

$tbl_par = "SELECT * FROM tbl_parametro_nascimento WHERE tbl_par_lixeira=0";  
$qr = mysqli_query($conector, $tbl_par);
$num_rows = mysqli_num_rows($qr);

if ($num_rows!=0){
	while ($reg_para = mysqli_fetch_object($qr)) {
		$array_fazendas[] = $reg_para->tbl_par_codigo_local;
		$array_alfa[] = $reg_para->tbl_par_codigo_alfa;
		$array_numerico[] = $reg_para->tbl_par_codigo_numerico;
		$data_incial = $reg_para->tbl_par_estacao_monta_inicial;
		$data_final = $reg_para->tbl_par_estacao_monta_final;
	}

    $array_local = implode("!", $array_fazendas);
    $array_cod_alfa = implode("!", $array_alfa);
    $array_cod_numerico = implode("!", $array_numerico);
	
	$valor[0]=$data_incial;
	$valor[1]=$data_final;
	$valor[2]=$array_local;
	$valor[3]=$array_cod_alfa;
	$valor[4]=$array_cod_numerico;

	$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3].'<|>'.$valor[4];
	echo $str; 
	exit;
}

echo $str; 

?>