<?php
	// baixa uma conta a pagar selecionado no script Form_contas_pagar.php
    function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }

	 function sonumero($str) { 
		return preg_replace("/[^0-9]/", "", $str); 
	} 

@ session_start();
$usuario_baixa = $_SESSION['nome_usuario'];
$usuario_baixa = tirarAcentos($usuario_baixa);	
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;
//$vlr_pagamento=0;

include "conecta_mysql.inc";

$chave = $_POST['chave'];
$numero_doc = $_POST['num_doc'];
$data_pagamento = $_POST['data_pagamento'];
$forma_pgto = $_POST['forma_pag'];
$total_baixar = $_POST['total_baixar'];

$ssql = "SELECT * FROM contas_pagar 
    WHERE ctp_id='$chave'";
$conta_pagar = mysqli_query($conector, $ssql); 

$registro_conta = mysqli_fetch_object($conta_pagar); 
		
$codigo_for = $registro_conta->ctp_codigo_fornecedor;
$razao = $registro_conta->ctp_nome_fornecedor;
$parcela = $registro_conta->ctp_parcela;

$historico = "Pag total do doc para: " . $razao;

if ($numero_doc==0 || $numero_doc==''){
	do {
		$data_sistema = date("y/m/d");
		$numero_randomico = mt_rand();
		$numero_quatro_digitos = substr($numero_randomico, 0, 4);
		$numero_doc=sonumero($data_sistema).$numero_quatro_digitos;

	    $rs = mysqli_query ($conector, "SELECT *
	                         		   	  FROM contas_pagar 
	        	                         WHERE ctp_numero_doc ='$numero_doc' and
	        	                         	   ctp_parcela = '$parcela' and 
						                       ctp_codigo_fornecedor='$codigo_for'");
						
		$num_rows_ctp = mysqli_num_rows($rs);

	} while ($num_rows_ctp==1);
}

// pega o ultimo registro da baixa para saber qual a sequencia
$rs = mysqli_query ($conector, "SELECT * FROM baixa_contas_pagar 
	WHERE bcp_id ='$chave'
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
           VALUES ('$chave',
           		   '$numero_doc', 
		           '$codigo_for',
				   '$parcela',
				   '$sequencia',
				   '$razao',
				   '$numero_doc', 
	               '$data_pagamento',
				   '$total_baixar',
				   'P',
				   '$data_sistema',
				   '$usuario_baixa',
				   null,
				   '$historico')";
							   
$resultado = mysqli_query($conector, $sql);
if (!$resultado) {
	$mensagem = "Ocorreu um erro ao gravar a baixa da conta. Sequência " . $sequencia . "\n" . mysqli_error($conector);
	echo $mensagem;
	mysqli_close($conector);
   	exit;
}

$sql = ("UPDATE contas_pagar SET ctp_situacao='P',
	                             ctp_numero_doc='$numero_doc',
	                             ctp_conta_pagamento='$forma_pgto'
	                       WHERE ctp_id='$chave'");
$resultado = mysqli_query($conector, $sql);
				
if (!$resultado) {
	$mensagem = "Ocorreu um erro ao gravar a baixa da conta no ctp." . "\n" . mysqli_error($conector);
	echo $mensagem;
	mysqli_close($conector);
  	exit;
}

echo $mensagem;

mysqli_close($conector);
exit;
?>