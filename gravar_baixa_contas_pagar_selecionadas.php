<?php

@ session_start();
$usuario_baixa = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;

include "conecta_mysql.inc";

$grupo_contas = $_POST['grupo_contas'];
$data_pagamento = $_POST['data_pagamento'];
$forma_pag = $_POST['forma_pag'];

$matriz_contas = explode("<|>", $grupo_contas);
$quantidade_itens = count($matriz_contas);

for($i=0; $i < $quantidade_itens; $i++) {
	$ctp_id = $matriz_contas[$i];

	$ssql = "SELECT * FROM contas_pagar 
	                 WHERE ctp_id='$ctp_id'";

	$conta_pagar = mysqli_query($conector, $ssql); 

	$registro_conta = mysqli_fetch_object($conta_pagar); 

	$numero_doc = $registro_conta->ctp_numero_doc;
	$parcela = $registro_conta->ctp_parcela;
	$codigo_for = $registro_conta->ctp_codigo_fornecedor;
	$razao = $registro_conta->ctp_nome_fornecedor;
	$parcela = $registro_conta->ctp_parcela;
	$valor_parcela = $registro_conta->ctp_valor_parcela;
	$valor_juros = $registro_conta->ctp_valor_juros;
	$valor_desconto = $registro_conta->ctp_valor_desconto;
	$valor_outro = $registro_conta->ctp_outro_valor;
	$situacao = $registro_conta->ctp_situacao;

	$total_pago=0;
		
	if ($situacao == "C"){
		$ssql="SELECT * FROM baixa_contas_pagar 
                       WHERE bcp_id='$ctp_id'";

		$conta_baixada = mysqli_query($conector, $ssql);
		$num_rows_contas_baixar = mysqli_num_rows($conta_baixada);

		if ($num_rows_contas_baixar!=0) {
	        while ($fila_baixada = mysqli_fetch_object($conta_baixada)) {
	            $vlr_pago = $fila_baixada->bcp_valor_pagamento;
	            $total_pago = $total_pago + $vlr_pago;
	        }
			 	
		} 
	}

	// pega o ultimo registro da baixa para saber qual a sequencia
	$rs = mysqli_query ($conector, "SELECT * FROM baixa_contas_pagar 
		WHERE bcp_id ='$ctp_id'
		ORDER BY bcp_sequencia_pagamento DESC LIMIT 1");
							
	$num_rows_bcp = mysqli_num_rows($rs);

	if ($num_rows_bcp==0) {
		$sequencia = 0;
	}
	else {
		$reg_bcp =  mysqli_fetch_object($rs);
		$sequencia = $reg_bcp->bcp_sequencia_pagamento;
		$sequencia++;
	}

	$vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro - $total_pago;

	$historico = "Pag total do doc para: " . $razao;

	$sql = "INSERT INTO baixa_contas_pagar (bcp_id,
											bcp_numero_id,
	                                        bcp_codigo_fornecedor, 
	                                        bcp_parcela, 
											bcp_sequencia_pagamento, 
											bcp_nome_fornecedor, 
											bcp_numero_documento, 
											bcp_data_pagamento, 
											bcp_valor_pagamento, 
											bcp_situacao,
											bcp_data_aceite,
											bcp_usuario_aceite,
											bcp_numero_agendamento,
											bcp_historico_pagamento)
	           VALUES ('$ctp_id',
	           		   '$numero_doc', 
			           '$codigo_for',
					   '$parcela',
					   '$sequencia',
					   '$razao',
					   '$numero_doc', 
		               '$data_pagamento',
					   '$vlr_pagamento',
					   'P',
					   '$data_sistema',
					   '$usuario_baixa',
					   null,
					   '$historico')";
								   
	$resultado = mysqli_query($conector, $sql);
	if (!$resultado) {
		$mensagem = "Ocorreu um erro ao gravar a baixa da conta." . "\n" . mysqli_error($conector);
		echo $mensagem;
		mysqli_close($conector);
	   	exit;
	}

	$sql = ("UPDATE contas_pagar SET ctp_situacao='P',
		                             ctp_conta_pagamento='$forma_pag'
		                       WHERE ctp_id='$ctp_id'");
	$resultado = mysqli_query($conector, $sql);
					
	if (!$resultado) {
		$mensagem = "Ocorreu um erro ao gravar a baixa da conta no ctp." . "\n" . mysqli_error($conector);
		echo $mensagem;
		mysqli_close($conector);
	  	exit;
	}

}
echo $mensagem;
mysqli_close($conector);

?>