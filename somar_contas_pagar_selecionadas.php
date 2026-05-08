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
$forma_pag_ant=0;


for($i=0; $i < $quantidade_itens; $i++) {
	$ctp_id = $matriz_contas[$i];
	//$codigo_fornecedor = substr($matriz_contas[$i],0,9);
	//$parcela_ctp = substr($matriz_contas[$i],9,3);
	//$numero_ctp = substr($matriz_contas[$i],12,15);

    $ssql= "SELECT * FROM contas_pagar WHERE ctp_id='$ctp_id'";

	$conta_pagar = mysqli_query($conector,$ssql);
	$num_rows_contas = mysqli_num_rows($conta_pagar);

	if ($num_rows_contas != 0) {
   		$registro_conta = mysqli_fetch_object($conta_pagar); 
		$vencimento = $registro_conta->ctp_data_vencimento;
		$forma_pag = $registro_conta->ctp_conta_pagamento;
		$numero_ctp = $registro_conta->ctp_numero_doc;
		$codigo_fornecedor = $registro_conta->ctp_codigo_fornecedor;
		$parcela_ctp = $registro_conta->ctp_parcela;

		if ($vencimento!=$vencimento_ant && $vencimento_ant!=0){
			$mensagem=9;
           	echo $mensagem;
            mysqli_close($conector);
			exit;
		}
		else {
			$vencimento_ant=$vencimento;
		}
		
		if ($forma_pag!=$forma_pag_ant && $forma_pag_ant!=0){
			$mensagem=99;
           	echo $mensagem;
            mysqli_close($conector);
			exit;
		}
		else {
			$forma_pag_ant=$forma_pag;
		}

		$valor_parcela = $registro_conta->ctp_valor_parcela;
		$valor_juros = $registro_conta->ctp_valor_juros;
		$valor_desconto = $registro_conta->ctp_valor_desconto;
		$valor_acrescimo = $registro_conta->ctp_outro_valor;
		$situacao = $registro_conta->ctp_situacao;
		
		$total_pago=0;
		
		if ($situacao == "C"){
			$ssql="SELECT * FROM baixa_contas_pagar 
                                          WHERE bcp_numero_id='$numero_ctp' AND 
                                                bcp_codigo_fornecedor='$codigo_fornecedor' AND
							                    bcp_parcela='$parcela_ctp'";

			$conta_baixada = mysqli_query($conector, $ssql);
			$num_rows_contas_baixar = mysqli_num_rows($conta_baixada);

			if ($num_rows_contas_baixar!=0) {
	            while ($fila_baixada = mysqli_fetch_object($conta_baixada)) {
	            	$vlr_pago = $fila_baixada->bcp_valor_pagamento;
	                $total_pago = $total_pago + $vlr_pago;
	            }
			 	
			 } 
		}
        
        $total_conta = $valor_parcela - $valor_desconto - $total_pago + 
					   $valor_juros + $valor_acrescimo;
					   
		$total_baixar_calculado = $total_baixar_calculado + $total_conta;			   
	}
}

if ($total_baixar_calculado !=0){
    $mensagem = $total_baixar_calculado . '<|>' .$vencimento . '<|>' . $forma_pag . '<|>';
}
else {
    $mensagem = 999;
}
	
echo $mensagem;
mysqli_close($conector);

?>