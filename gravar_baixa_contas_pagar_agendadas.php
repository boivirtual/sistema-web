<?php

@ session_start();
$usuario_baixa = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;

include "conecta_mysql.inc";

$grupo_contas = $_POST['grupo_contas'];

$matriz_contas = explode("<|>", $grupo_contas);
$quantidade_itens = count($matriz_contas);


for($i=0; $i < $quantidade_itens; $i++) {
	$numero_agendamento = $matriz_contas[$i];

	$agendamento = mysqli_query($conector, "SELECT * FROM contas_pagar_agendamento 
		                                WHERE ctp_age_numero_agendamento='$numero_agendamento'");

	$num_rows_agendamento = mysqli_num_rows($agendamento);
	if ($num_rows_agendamento != 0) {
   		$registro_agendamento = mysqli_fetch_object($agendamento);

		$data_pagamento = $registro_agendamento->ctp_age_data_pagamento;

		$contas_pagar = mysqli_query($conector, "SELECT * FROM contas_pagar
										     WHERE ctp_numero_agendamento='$numero_agendamento'");

        while ($reg_contas_pagar = mysqli_fetch_object($contas_pagar)){
			$numero_id = $reg_contas_pagar->ctp_numero_doc;
			$parcela = $reg_contas_pagar->ctp_parcela;
			$codigo_for = $reg_contas_pagar->ctp_codigo_fornecedor;
			$nome_for = $reg_contas_pagar->ctp_nome_fornecedor;						
			$numero_doc = $reg_contas_pagar->ctp_numero_documento;						
			$vlr_parcela = $reg_contas_pagar->ctp_valor_parcela;
			$vlr_juros = $reg_contas_pagar->ctp_valor_juros;
			$vlr_desconto = $reg_contas_pagar->ctp_valor_desconto;
			$vlr_outro = $reg_contas_pagar->ctp_outro_valor;
	        $total_parcela = $vlr_parcela - $vlr_desconto + $vlr_juros + $vlr_outro;
			$historico = "Pag total da fatura para: " . $nome_for;

	        $sql = "INSERT INTO baixa_contas_pagar (bcp_numero_id,
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
		           VALUES ('$numero_id', 
				           '$codigo_for',
						   '$parcela',
						   1,
						   '$nome_for',
						   '$numero_doc', 
			               '$data_pagamento',
						   '$total_parcela',
						   'P',
						   '$data_sistema',
						   '$usuario_baixa',
						   '$numero_agendamento',
						   '$historico')";
						   
			$resultado = mysqli_query($conector, $sql);
	        if (!$resultado) {
	        	$mensagem = "Erro na geracao da baixa da conta" . "\n" . mysqli_error($conector);
			}
			else {
	    		$sql = ("UPDATE contas_pagar SET ctp_situacao='P' 
	    			                       WHERE ctp_numero_doc='$numero_id' AND 
	    			                             ctp_parcela='$parcela' AND
	    			                             ctp_codigo_fornecedor='$codigo_for'");
	    		$resultado = mysqli_query($conector, $sql);
				
	        	if (!$resultado) {
	        		$mensagem = "Erro na atualizacao da conta" . "\n" . mysqli_error($conector);
				}
			}
	    }

	    $sql = ("UPDATE contas_pagar_agendamento SET ctp_age_situacao='P' 
	    	                       WHERE ctp_age_numero_agendamento='$numero_agendamento'");
	    $resultado = mysqli_query($conector,$sql);
				
	    if (!$resultado) {
	    	$mensagem = "Erro na atualizacao do agendamento" . "\n" . mysqli_error($conector);
	    }	
	}
}

echo $mensagem;
mysqli_close($conector);

?>