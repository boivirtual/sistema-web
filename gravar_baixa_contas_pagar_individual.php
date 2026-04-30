<?php
// Baixa uma conta a pagar no script Form_conta_pagar_editar.php
	 function sonumero($str) { 
		return preg_replace("/[^0-9]/", "", $str); 
	} 

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
$codigo_fornecedor = $dadosarray[5];
$historico = $dadosarray[7];
$nome_cli = $dadosarray[8];
$ctp_id = $dadosarray[9];

$valor_juros=0.00;
$valor_desconto=0.00;
$valor_acrescimo=0.00;

if ($numero==0 || $numero==''){
	do {
		$data_sistema = date("y/m/d");
		$numero_randomico = mt_rand();
		$numero_quatro_digitos = substr($numero_randomico, 0, 4);
		$numero_doc=sonumero($data_sistema).$numero_quatro_digitos;

	    $rs = mysqli_query ($conector, "SELECT * FROM contas_pagar 
	       	WHERE ctp_numero_doc ='$numero_doc' and
	       	      ctp_parcela = '$parcela' and 
				  ctp_codigo_fornecedor='$codigo_fornecedor'");
						
		$num_rows_ctp = mysqli_num_rows($rs);

	} while ($num_rows_ctp==1);

  	$sql = ("UPDATE contas_pagar SET ctp_numero_doc='$numero_doc'
	    	                   WHERE ctp_numero_doc='$numero' and 
	    	                         ctp_parcela='$parcela' and 
	    	                         ctp_codigo_fornecedor='$codigo_fornecedor'");
   	$resultado = mysqli_query($conector, $sql);
	
	if (!$resultado) {
		$mensagem = "Erro na atualizacao do número da conta" . "\n" . mysqli_error($conector);
	}
}
else {
	$numero_doc = $numero;
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

$sql = "INSERT INTO baixa_contas_pagar (bcp_id,
										bcp_numero_id,
                                        bcp_parcela,
                                        bcp_sequencia_pagamento, 
	                                    bcp_data_pagamento, 
	    								bcp_valor_pagamento, 
										bcp_codigo_fornecedor, 
										bcp_nome_fornecedor,
										bcp_situacao, 
		        						bcp_data_aceite, 
										bcp_usuario_aceite, 
										bcp_historico_pagamento)
          VALUES ('$ctp_id',
          		  '$numero_doc', 
    	          '$parcela',
    	          '$sequencia',
				  '$data_pagamento',
				  '$valor_pagamento',
				  '$codigo_fornecedor',
				  '$nome_cli',
				  'P', 
		          '$data_sistema',
				  '$usuario_baixa',
				  '$historico'
				)";
$resultado = mysqli_query($conector,$sql);

if (!$resultado) {
	$mensagem = "Erro na geracao da baixa da conta" . $ctp_id . " " . "\n" . mysqli_error($conector);
	echo $mensagem;
	mysqli_close($conector);
	exit;
}

$total_pago = 0.00;
$total_conta = 0.00;

$rs = mysqli_query ($conector, "SELECT * FROM contas_pagar
    WHERE ctp_id='$ctp_id'");
$registro =  mysqli_fetch_object($rs);
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
	$total_conta+= $registro->ctp_valor_parcela;
	$total_conta-= $registro->ctp_valor_desconto;
	$total_conta+= $registro->ctp_valor_juros;
	$total_conta+= $registro->ctp_outro_valor;
}

$conta_baixada = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar 
    WHERE bcp_id='$ctp_id'");

$num_rows = mysqli_num_rows($conta_baixada);

if ($num_rows != 0) {
    while ($fila_baixada =  mysqli_fetch_object($conta_baixada)) {
        $total_pago+=  $fila_baixada->bcp_valor_pagamento;
    }
}

$pago= number_format($total_pago,2,",",".");
$conta=number_format($total_conta,2,",",".");

if ($pago == $conta) {
   	$sql = ("UPDATE contas_pagar SET ctp_situacao='P'
	    WHERE ctp_id='$ctp_id'");
} 
else {
   	$sql = ("UPDATE contas_pagar SET ctp_situacao='C'
	    WHERE ctp_id='$ctp_id'");
}

$resultado = mysqli_query($conector, $sql);
	
if (!$resultado) {
	$mensagem = "Erro na atualizacao da conta" . "\n" . mysqli_error($conector);
}

echo $mensagem;

mysqli_close($conector);

?>
