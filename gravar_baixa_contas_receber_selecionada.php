<?php
	// Baixa uma conta a receber selecionada no botao baixa do scritp Form_contas_receber
    function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }

@ session_start();
$usuario_baixa = $_SESSION['nome_usuario'];
$usuario_baixa = tirarAcentos($usuario_baixa);	
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;

include "conecta_mysql.inc";

$ctr_id = $_POST['ctr_id'];
$numero_doc = $_POST['num_doc'];
$numero_parcela = $_POST['num_parcela'];
$data_pagamento = $_POST['data_pagamento'];
$conta_pgto = $_POST['conta_pag'];

$ssql = "SELECT * FROM contas_receber 
                 WHERE ctr_id='$ctr_id'";
$conta_receber = mysqli_query($conector, $ssql); 

$registro_conta = mysqli_fetch_object($conta_receber); 

$numero_doc = $registro_conta->ctr_numero_doc;
$numero_parcela = $registro_conta->ctr_parcela;		
$codigo_cliente = $registro_conta->ctr_codigo_cliente_fornecedor;
$razao = $registro_conta->ctr_nome_cliente;
$valor_parcela = $registro_conta->ctr_valor_parcela;
$valor_desconto = $registro_conta->ctr_valor_desconto;
$valor_juros = $registro_conta->ctr_valor_juros;
$valor_outro = $registro_conta->ctr_valor_acrescimo;
$tipo_documento = $registro_conta->ctr_tipo;
$situacao = $registro_conta->ctr_situacao;

$total_pago=0;
		
if ($situacao == "C"){
	$ssql="SELECT * FROM baixa_contas_receber 
        WHERE bcr_id='$ctr_id'";

	$conta_baixada = mysqli_query($conector, $ssql);
	$num_rows_contas_baixar = mysqli_num_rows($conta_baixada);

	if ($num_rows_contas_baixar!=0) {
        while ($fila_baixada = mysqli_fetch_object($conta_baixada)) {
            $vlr_pago = $fila_baixada->bcr_valor_pagamento;
            $total_pago = $total_pago + $vlr_pago;
        }
	} 
}

$vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro - $total_pago;

$historico = "Recebimento total de: " . $razao;

// pega o ultimo registro da baixa para saber qual a sequencia
$rs = mysqli_query ($conector, "SELECT * FROM baixa_contas_receber
	WHERE bcr_id ='$ctr_id' 
	ORDER BY bcr_sequencia DESC LIMIT 1");
						
$num_rows_bcr = mysqli_num_rows($rs);

if ($num_rows_bcr==0) {
	$sequencia = 1;
}
else {
	$reg_bcr =  mysqli_fetch_object($rs);
	$sequencia = $reg_bcr->bcr_sequencia;
	$sequencia++;
}

$sql = "INSERT INTO baixa_contas_receber (
						bcr_id,
						bcr_parcela,
						bcr_sequencia,
   						bcr_numero_doc,
						bcr_tipo,
						bcr_codigo_cliente_fornecedor,
						bcr_nome_cliente,
						bcr_data_pagamento,
						bcr_valor_pagamento,
						bcr_valor_juros,
						bcr_valor_desconto,
						bcr_valor_acrescimo,
						bcr_descricao_acrescimo,
						bcr_usuario_aceite,
						bcr_data_aceite,
						bcr_historico,
						bcr_situacao,
						bcr_usuario_aceite_pagamento,
						bcr_data_aceite_pagamento,
						bcr_comissao_paga)
			           VALUES ('$ctr_id',
							   '$numero_parcela',
							   '$sequencia',
					           '$numero_doc', 
							   '$tipo_documento',
					           '$codigo_cliente',
							   '$razao',
				               '$data_pagamento',
							   '$vlr_pagamento',
							   null,
							   null,
							   null,
							   null,
							   '$usuario_baixa',
							   '$data_sistema',
							   '$historico',
							   'P',
							   null,
							   null,
							   null)";
							   
$resultado = mysqli_query($conector, $sql);
if (!$resultado) {
	$mensagem = "Ocorreu um erro ao gravar a baixa da conta." . "\n" . mysqli_error($conector);
	echo $mensagem;
	mysqli_close($conector);
   	exit;
}

$sql = ("UPDATE contas_receber SET ctr_situacao='P',
	                               ctr_codigo_conta_recebimento='$conta_pgto'
	                         WHERE ctr_id='$ctr_id'");
$resultado = mysqli_query($conector, $sql);
				
if (!$resultado) {
	$mensagem = "Ocorreu um erro ao gravar a baixa da conta no ctr." . "\n" . mysqli_error($conector);
	echo $mensagem;
	mysqli_close($conector);
  	exit;
}


echo $mensagem;
mysqli_close($conector);

?>