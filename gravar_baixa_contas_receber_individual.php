<?php
// Baixa uma conta a receber no script Form_conta_receber_editar.php
@ session_start();
$usuario_baixa = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;

include "conecta_mysql.inc";

$dadosarray = $_POST['dadosarray'];
$numero = $dadosarray[0];
$parcela = $dadosarray[1];
$data_pagamento = $dadosarray[3];
$valor_pagamento = $dadosarray[4];
$codigo_cliente = $dadosarray[5];
$historico = $dadosarray[7];
$nome_cli = $dadosarray[8];
$id_ctr = $dadosarray[9];
$valor_juros=0.00;
$valor_desconto=0.00;
$valor_acrescimo=0.00;

// pega o ultimo registro da baixa para saber qual a sequencia
$rs = mysqli_query ($conector, "SELECT * FROM baixa_contas_receber
	WHERE bcr_id ='$id_ctr' 
	ORDER BY bcr_sequencia  DESC LIMIT 1");
						
$num_rows_bcr = mysqli_num_rows($rs);

if ($num_rows_bcr==0) {
	$sequencia = 1;
}
else {
	$reg_bcr =  mysqli_fetch_object($rs);
	$sequencia = $reg_bcr->bcr_sequencia;
	$sequencia++;
}

$sql = "INSERT INTO baixa_contas_receber (bcr_id,
                                          bcr_parcela, 
										  bcr_sequencia,
										  bcr_numero_doc,
	                                      bcr_data_pagamento, 
	    								  bcr_valor_pagamento, 
										  bcr_codigo_cliente_fornecedor, 
										  bcr_nome_cliente,
										  bcr_situacao, 
		        						  bcr_data_aceite, 
										  bcr_usuario_aceite, 
										  bcr_historico,
										  bcr_valor_juros,
										  bcr_valor_desconto,
										  bcr_valor_acrescimo,
										  bcr_descricao_acrescimo,
										  bcr_data_aceite_pagamento,
										  bcr_usuario_aceite_pagamento)
          VALUES ('$id_ctr',
    	          '$parcela',
    	          '$sequencia',
          		  '$numero', 
				  '$data_pagamento',
				  '$valor_pagamento',
				  '$codigo_cliente',
				  '$nome_cli',
				  'P', 
		          '$data_sistema',
				  '$usuario_baixa',
				  '$historico',
				  '$valor_juros',
				  '$valor_desconto',
				  '$valor_acrescimo',
				  null,
				  null,
				  null
				)";
$resultado = mysqli_query($conector,$sql);

if (!$resultado) {
	$mensagem = "Erro na geracao da baixa da conta" . "\n" . mysqli_error($conector);
	echo $mensagem;
	mysqli_close($conector);
	exit;
}


$total_pago = 0.00;
$total_conta = 0.00;

$rs = mysqli_query ($conector, "SELECT * FROM contas_receber 
                                        WHERE ctr_id='$id_ctr'");
$registro =  mysqli_fetch_object($rs);
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
	$total_conta+= $registro->ctr_valor_parcela;
	$total_conta-= $registro->ctr_valor_desconto;
	$total_conta+= $registro->ctr_valor_juros;
	$total_conta+= $registro->ctr_valor_acrescimo;
}

$conta_baixada = mysqli_query($conector, "SELECT * FROM baixa_contas_receber 
                                                  WHERE bcr_id='$id_ctr'");

$num_rows = mysqli_num_rows($conta_baixada);

if ($num_rows != 0) {
    while ($fila_baixada =  mysqli_fetch_object($conta_baixada)) {
        $total_pago+=  $fila_baixada->bcr_valor_pagamento;
    }
}

$pago= number_format($total_pago,2,",",".");
$conta=number_format($total_conta,2,",",".");

if ($pago == $conta) {
		$situacao = 'P';
	} 
	else {
		$situacao = 'C';
	}

    $sql = ("UPDATE contas_receber SET ctr_situacao='$situacao'
	    WHERE ctr_id='$id_ctr'");

   	$resultado = mysqli_query($conector, $sql);
	
	if (!$resultado) {
		$mensagem = "Erro na atualizacao da conta" . "\n" . mysqli_error($conector);
	}

echo $mensagem;

mysqli_close($conector);

?>
