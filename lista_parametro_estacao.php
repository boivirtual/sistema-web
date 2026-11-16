<?php
include "conecta_mysql.inc";

$data_sistema = date("Y-m-d");

$str='';
$array_fazendas = array();
$array_alfa = array();
$array_numerico = array();
$array_id = array();
$array_inicio = array();
$array_fim = array();
$array_nome = array();
$array_atual = array();


/*$tbl_par = "SELECT * FROM tbl_parametro_estacao_monta 
					WHERE tbl_par_lixeira=0 AND 
					      tbl_par_estacao_monta_final>='$data_sistema'";  
*/					      
$tbl_par = "SELECT * FROM tbl_parametro_estacao_monta 
					WHERE tbl_par_lixeira=0";  

$qr = mysqli_query($conector, $tbl_par);
$num_rows = mysqli_num_rows($qr);

if ($num_rows!=0){
	while ($reg_para = mysqli_fetch_object($qr)) {
		$array_id[] = $reg_para->tbl_par_estacao_id;
		$array_fazendas[] = $reg_para->tbl_par_codigo_local;
		$array_alfa[] = $reg_para->tbl_par_codigo_alfa;
		$array_numerico[] = $reg_para->tbl_par_codigo_numerico;
		$array_inicio[] = $reg_para->tbl_par_estacao_monta_inicial;
		$array_fim[] = $reg_para->tbl_par_estacao_monta_final;
		$array_nome[] = $reg_para->tbl_par_estacao_nome;

		if ($reg_para->tbl_par_estacao_monta_final>=$data_sistema) {
			$array_atual[] = 'S';
		}
		else {
			$array_atual[] = 'N';
		}
	}

    $array_cod_id = implode("!", $array_id);
    $array_local = implode("!", $array_fazendas);
    $array_cod_alfa = implode("!", $array_alfa);
    $array_cod_numerico = implode("!", $array_numerico);
    $array_data_inicial = implode("!", $array_inicio);
    $array_data_final = implode("!", $array_fim);
    $array_nome_estacao = implode("!", $array_nome);
    $array_estacao_atual = implode("!", $array_atual);
	
	$valor[0]=$array_data_inicial;
	$valor[1]=$array_data_final;
	$valor[2]=$array_local;
	$valor[3]=$array_cod_alfa;
	$valor[4]=$array_cod_numerico;
	$valor[5]=$array_cod_id;
	$valor[6]=$array_nome_estacao;
	$valor[7]=$array_estacao_atual;

	$str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3].'<|>'.$valor[4].'<|>'.$valor[5].'<|>'.$valor[6].'<|>'.$valor[7];
	echo $str; 
	exit;
}

echo $str; 

?>
