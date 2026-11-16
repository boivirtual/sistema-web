<?php

function diferenca_data($data_validade) {
    $data_inicial = $data_validade;
    $data_final = date("Y-m-d H:i:s");
    $time_inicial = strtotime($data_inicial);
    $time_final = strtotime($data_final);
    $diferenca = $time_final - $time_inicial; 
    $dias = (int)floor( $diferenca / (60 * 60 * 24)); 
    return $dias;
}

include "conecta_mysql.inc";

$numero_doc = str_pad($_POST['numero_pedido'], 9, "0", STR_PAD_LEFT);  

for ($i = 0; $i <= 30; $i++) {
	$valor[$i]=0;
}

$orcamento_vencido = '';
$ajustar_item = '';

$rs = mysqli_query($conector, "SELECT * FROM tbl_pedido
						   WHERE tbl_ped_numero_id='$numero_doc'");
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($fila = mysqli_fetch_object($rs)){
    	$valor[8]=$fila->tbl_ped_forma_pagamento;
    	$valor[9]=$fila->tbl_ped_qtd_parcelas;

	    $dias_validade=$fila->tbl_ped_dias_validade;
	    $dias_vencimento_orcamento = diferenca_data($fila->tbl_ped_data_emissao); 

	    if ($dias_vencimento_orcamento>=$dias_validade && $fila->tbl_ped_situacao=='O') {
	        $orcamento_vencido = 'S';        
	    	$valor[12]=$dias_vencimento_orcamento;
	    }
    }
}

$numero_do_item=0;
$matriz_itens= array();

$rs = mysqli_query($conector, "SELECT * FROM tbl_itens_pedido
						   WHERE tbl_ite_ped_numero_id='$numero_doc'");
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($fila = mysqli_fetch_object($rs)){
		$valor[0]=$fila->tbl_ite_ped_codigo_produto;
		$codigo_produto = $fila->tbl_ite_ped_codigo_produto;
		$valor[1]=$fila->tbl_ite_ped_descricao;
		$valor[2]=$fila->tbl_ite_ped_unidade;
		$valor[3]=$fila->tbl_ite_ped_qtd;
		$valor[4]=$fila->tbl_ite_ped_valor_unitario;
		$valor[5]=$fila->tbl_ite_ped_valor_total;
		$valor[6]=$fila->tbl_ite_ped_per_desconto;
		$valor[7]=$fila->tbl_ite_ped_valor_desconto;
		$valor[10]=$fila->tbl_ite_ped_preco_venda;
		$preco_venda = $fila->tbl_ite_ped_preco_venda;

		if ($orcamento_vencido == 'S') {
			$tbl_produto = mysqli_query($conector, "SELECT * FROM tbl_produto
									   WHERE tbl_produto_codigo='$codigo_produto'");
			$num_rows_prod = mysqli_num_rows($tbl_produto);

			if ($num_rows_prod!=0){
			    $reg_prod = mysqli_fetch_object($tbl_produto);

			    if ($reg_prod->tbl_produto_preco_venda!=$preco_venda){
			    	$ajustar_item='S';
			    }
			}
		}	

		$valor[11]=$ajustar_item;

		$itens[$numero_do_item] = $valor[0] . '|' . $valor[1] . '|' . $valor[2] . '|' . $valor[3] . '|' . $valor[4] . '|' . $valor[5] . '|' . $valor[6] . '|' . $valor[7] . '|' . $valor[8] . '|' . $valor[9]. '|' . $valor[10]. '|' . $valor[11]. '|' . $valor[12];
				
		array_push($matriz_itens, $itens[$numero_do_item]);
		$numero_do_item++;
    }
	$matriz_com_itens = implode("<|>", $matriz_itens);
}
else {
	$matriz_com_itens = 0;
}

echo $matriz_com_itens;

mysqli_free_result($rs); 
mysqli_close($conector);
?>