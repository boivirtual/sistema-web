<?php

$mensagem = 0;

include "conecta_mysql.inc";

$grupo_contas = $_POST['grupo_contas'];

$matriz_contas = explode("<|>", $grupo_contas);
$quantidade_itens = count($matriz_contas);
$valor_juros=0.00;
$valor_desconto=0.00;
$valor_acrescimo=0.00;
$total_baixar_calculado=0.00;
$vencimento_ant=0;
$conta_rec_ant=0;

for($i=0; $i < $quantidade_itens; $i++) {
	$ctr_id = $matriz_contas[$i];

    $ssql = "SELECT * FROM contas_receber 
                     WHERE ctr_id='$ctr_id'";
    $rs = mysqli_query($conector, $ssql); 

	$num_rows_contas = mysqli_num_rows($rs);
	if ($num_rows_contas != 0) {
   		$registro_conta = mysqli_fetch_object($rs); 
		
		$codigo_cli_for = $registro_conta->ctr_codigo_cliente_fornecedor;
		$vencimento = $registro_conta->ctr_data_vencimento;
		$conta_rec = $registro_conta->ctr_codigo_conta_recebimento;
		$situacao = $registro_conta->ctr_situacao;
		
		if ($vencimento!=$vencimento_ant && $vencimento_ant!=0){
			$mensagem=9;
           	echo $mensagem;
            mysqli_close($conector);
			exit;
		}
		else {
			$vencimento_ant=$vencimento;
		}
		
		if ($conta_rec!=$conta_rec_ant && $conta_rec_ant!=0){
			$mensagem=99;
           	echo $mensagem;
            mysqli_close($conector);
			exit;
		}
		else {
			$conta_rec_ant=$conta_rec;
		}

		$total_pago=0;
		
		if ($situacao == "C"){
    		$ssql = "select * from baixa_contas_receber 
					         where bcr_id='$ctr_id'";
    		$conta_baixada = mysqli_query($conector, $ssql); 

            while ($fila_baixada = mysqli_fetch_object($conta_baixada)) {
            	$vlr_pago = $fila_baixada->bcr_valor_pagamento;
                $total_pago = $total_pago + $vlr_pago;
            }
		}
        
        $total_conta = $registro_conta->ctr_valor_parcela - $registro_conta->ctr_valor_desconto - $total_pago + 
					   $registro_conta->ctr_valor_juros + $registro_conta->ctr_valor_acrescimo;
					   
		$total_baixar_calculado = $total_baixar_calculado + $total_conta;			   
	}
}

if ($total_baixar_calculado != 0){
    $mensagem = $total_baixar_calculado.'<|>'.$vencimento.'<|>'.$conta_rec.'<|>';
}
else {
    $mensagem = 999;
}
	
echo $mensagem;
mysqli_close($conector);

?>