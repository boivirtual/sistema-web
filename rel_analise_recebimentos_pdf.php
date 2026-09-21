<?php

include "conecta_mysql.inc";

$data_sistema = date("Y-m-d");
$conta_inicio=0;    
$data_inicio=0; 
$data_fim=0;
$tipo_data='';
$tipo_rel='';
$criterio="";
$linha=0;
  
if(isset($_REQUEST["conta"]) && $_REQUEST["conta"]!=0) {
    $conta_inicio=$_REQUEST["conta"];
}

if ($conta_inicio==0 || $conta_inicio==1000000){
	$conta_inicio=1000000;
	$conta_fim=1999999;
}
else if (substr($conta_inicio, 3, 4)==0){
	$conta_fim=substr($conta_inicio, 0, 3) . 9999;
}
else {
	$conta_fim=$conta_inicio;
	$conta_inicio=substr($conta_fim, 0, 3) . '0000';
}

if (isset($_REQUEST["tipo_data"]) && $_REQUEST["tipo_data"]!="") {
    $tipo_data= $_REQUEST["tipo_data"];
}
			
if (isset($_REQUEST["tipo_rel"]) && $_REQUEST["tipo_rel"]!="") {
    $tipo_rel= $_REQUEST["tipo_rel"];
}

if (isset($_REQUEST["codigo_cliente"]) && $_REQUEST["codigo_cliente"]!=0) {
    $codigo_cli = $_REQUEST["codigo_cliente"];
	$cli = mysqli_query($conector, "SELECT cliente_nome FROM cliente_fornecedor
	   	                                               WHERE cliente_id ='$codigo_cli'"); 
	$registro_cli = mysqli_fetch_object($cli);  
	$nome_cliente = $registro_cli->cliente_nome;
}
else {
	$codigo_cli = 0;
	$nome_cliente = "Todos";
}

if(isset($_REQUEST["data_inicio"]) && $_REQUEST["data_inicio"]!=0 && 
   isset($_REQUEST["data_fim"]) && $_REQUEST["data_fim"]!=0) {
    $data_inicio=$_REQUEST["data_inicio"];
	$data_fim=$_REQUEST["data_fim"];	
    $data_inicio_edi = new DateTime($data_inicio);
    $data_fim_edi = new DateTime($data_fim);
}
else {
	$data_inicio_edi = "";
	$data_fim_edi = "";
}

if ($tipo_data==""){
	$tipo_data='V';
}

if ($tipo_data=="E"){ 
	$desc_tipo_data=" por Data de Emissão";
	$desc_filtro="Emissão";
}
else if ($tipo_data=="V"){
	$desc_tipo_data=" por Data de Vencimento";  
	$desc_filtro="Vencimento";
}  
else {
	$desc_tipo_data=" por Data de Recebimento";
	$desc_filtro="Recebimento";
}

if ($tipo_rel=="A"){
	$desc_tipo_rel = " Analítico";
}
else {
	$desc_tipo_rel = " Sintético";
}

if(isset($_REQUEST["centro_custos"])) {
	if ($_REQUEST["centro_custos"]!=0) {
    	$codigo_cc=$_REQUEST["centro_custos"];
		$centro_custos = mysqli_query($conector, "SELECT tbl_cc_descricao FROM tbl_centro_custo
		                                     WHERE tbl_cc_codigo_id >='$codigo_cc'"); 
		$registro_cc = mysqli_fetch_object($centro_custos);  
		$desc_centro_custos = $registro_cc->tbl_cc_descricao;
	}
	else {
		$desc_centro_custos = 'Todos';
		$codigo_cc=0;
	}
}
else {
	$desc_centro_custos = 'Todos';
	$codigo_cc=0;
}

@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$_SESSION['data_inicio_ctr_rel']=$_REQUEST["data_inicio"];
$_SESSION['data_fim_ctr_rel']=$_REQUEST["data_fim"];
$_SESSION['tipo_data_ctr_rel']=$_REQUEST["tipo_data"];
$_SESSION['tipo_rel_ctr_rel']=$_REQUEST["tipo_rel"]; 
$_SESSION['codigo_c_custo_ctr_rel']=$codigo_cc; 
$_SESSION['codigo_conta_ctr_rel']=$_REQUEST["conta"]; 
$_SESSION['codigo_cliente_ctr_rel']=$codigo_cli;


$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$filtros = 'Filtros: Data ' . $data_inicio_edi->format('d/m/Y') . ' até ' . 
            $data_fim_edi->format('d/m/Y') . ' - Tipo Data: ' . $desc_filtro . ' - Centro Custos: ' . $desc_centro_custos  . ' - Conta: ' . $conta_inicio . ' até ' . $conta_fim . ' - Tipo Rel:' . $desc_tipo_rel . ' - Cliente/Parceiro: ' . $nome_cliente;
 

$numero_paginas = 1;
$pagina_atual = 0;

$_SESSION['nome_relatorio']= "Análise de Recebimentos";
$_SESSION['filtros']=$filtros;

ob_start ();
define('FPDF_FONTPATH', 'fpdf/font/');
require_once('fpdf/pdf_padrao_retrato.php');
$pdf=new PDF("P","mm","A4");
$pdf->Open();

$plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                 WHERE tbl_plano_contas_codigo_id >='$conta_inicio' AND 
                                                       tbl_plano_contas_codigo_id <='$conta_fim'
	 	                                      ORDER BY tbl_plano_contas_codigo_id ASC"); 

$num_rows_contas = mysqli_num_rows($plano_contas);

$total_conta_sintetica=0;
$total_pago_conta_sintetica=0;
$total_vencida_conta_sintetica=0;
$total_aberto_conta_sintetica=0;
$total_avencer_conta_sintetica=0;
$arry_conta = array();
$arry_sub_conta = array();
$conta_anterior = 0;
$sub_conta_anterior = 0;
$index_array_conta=0;
$index_array_sub_conta=0;

while ($registro_plano_contas = mysqli_fetch_object($plano_contas)){  
	$cod_conta = $registro_plano_contas->tbl_plano_contas_codigo_id;
    $descricao_conta = utf8_decode($registro_plano_contas->tbl_plano_contas_descricao);                                   

	$codigo_sub_conta = substr($cod_conta, 0, 3);
	$codigo_seis_conta = substr($cod_conta, 1, 6);
	$codigo_quatro_conta = substr($cod_conta, 3, 4);

    if ($codigo_seis_conta!=0 && $codigo_quatro_conta==0){
	    if ($codigo_sub_conta!=$sub_conta_anterior){
	        if ($sub_conta_anterior==0){
	            $arry_sub_conta[$index_array_sub_conta]=$codigo_sub_conta;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=$descricao_conta;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $sub_conta_anterior=$codigo_sub_conta;
	        }
	        else {
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=$codigo_sub_conta;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=$descricao_conta;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $index_array_sub_conta++;
	            $arry_sub_conta[$index_array_sub_conta]=0;
	            $sub_conta_anterior=$codigo_sub_conta;
	        }
	    }
    }
    else if ($codigo_quatro_conta!=0) {
	    if ($cod_conta!=$conta_anterior){
	        if ($conta_anterior==0){
	            $arry_conta[$index_array_conta]=$cod_conta;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=$descricao_conta;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $conta_anterior=$cod_conta;
	        }
	        else {
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=$cod_conta;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=$descricao_conta;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $index_array_conta++;
	            $arry_conta[$index_array_conta]=0;
	            $conta_anterior=$cod_conta;
	        }
	    }
    }
}

$qtd_contas = count($arry_conta);
$qtd_sub_contas = count($arry_sub_conta);

if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc==0 && $codigo_cli==0){
	if ($tipo_data=="E"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_emissao >='$data_inicio' and
								                ctr_data_emissao <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim' and 
								                ctr_lixeira=0 
				 	                   ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="V"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_vencimento >='$data_inicio' and
								                ctr_data_vencimento <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim' and 
								                ctr_lixeira=0 
				 	                   ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="P"){
		$contas_rec = mysqli_query($conector, "SELECT * 
		                               FROM baixa_contas_receber
		                         INNER JOIN contas_receber
		                                 ON bcr_id=ctr_id
		                              WHERE bcr_data_pagamento >='$data_inicio' and
							                bcr_data_pagamento <='$data_fim' and
							                ctr_codigo_conta>='$conta_inicio' and 
							                ctr_codigo_conta<='$conta_fim' and 
								                ctr_lixeira=0
			 	                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
	}
}
else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc!=0 && $codigo_cli==0){
	if ($tipo_data=="E"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_emissao >='$data_inicio' and
								                ctr_data_emissao <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim' and 
								                ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="V"){
			$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_vencimento >='$data_inicio' and
								                ctr_data_vencimento <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim'   and 
								                ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="P"){
		$contas_rec = mysqli_query($conector, "SELECT * 
		                               FROM baixa_contas_receber
		                         INNER JOIN contas_receber
		                                 ON bcr_id=ctr_id
		                              WHERE bcr_data_pagamento >='$data_inicio' and
							                bcr_data_pagamento <='$data_fim' and
							                ctr_codigo_conta>='$conta_inicio' and 
							                ctr_codigo_conta<='$conta_fim'  and 
								            ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_lixeira=0
			 	                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
	}
}
else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc!=0 && $codigo_cli!=0){
	if ($tipo_data=="E"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_emissao >='$data_inicio' and
								                ctr_data_emissao <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim' and 
								                ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_codigo_cliente_fornecedor='$codigo_cli' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="V"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_vencimento >='$data_inicio' and
								                ctr_data_vencimento <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim'   and 
								                ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_codigo_cliente_fornecedor='$codigo_cli' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="P"){
		$contas_rec = mysqli_query($conector, "SELECT * 
		                               FROM baixa_contas_receber
		                         INNER JOIN contas_receber
		                                 ON bcr_id=ctr_id
		                              WHERE bcr_data_pagamento >='$data_inicio' and
							                bcr_data_pagamento <='$data_fim' and
							                ctr_codigo_conta>='$conta_inicio' and 
							                ctr_codigo_conta<='$conta_fim'  and 
								            ctr_codigo_c_custo='$codigo_cc' and 
							                ctr_codigo_cliente_fornecedor='$codigo_cli' and 
								                ctr_lixeira=0
			 	                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
	}
}
else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc==0 && $codigo_cli!=0){
	if ($tipo_data=="E"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_emissao >='$data_inicio' and
								                ctr_data_emissao <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim' and 
								                ctr_codigo_cliente_fornecedor='$codigo_cli' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="V"){
		$contas_rec = mysqli_query($conector, "SELECT *
			                               FROM contas_receber
	                                      WHERE ctr_data_vencimento >='$data_inicio' and
								                ctr_data_vencimento <='$data_fim' and
										        ctr_codigo_conta>='$conta_inicio' and 
								                ctr_codigo_conta<='$conta_fim'   and 
								                ctr_codigo_cliente_fornecedor='$codigo_cli' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
	}
	else if ($tipo_data=="P"){
		$contas_rec = mysqli_query($conector, "SELECT * 
		                               FROM baixa_contas_receber
		                         INNER JOIN contas_receber
		                                 ON bcr_id=ctr_id
		                              WHERE bcr_data_pagamento >='$data_inicio' and
							                bcr_data_pagamento <='$data_fim' and
							                ctr_codigo_conta>='$conta_inicio' and 
							                ctr_codigo_conta<='$conta_fim'  and 
							                ctr_codigo_cliente_fornecedor='$codigo_cli' and 
								                ctr_lixeira=0
			 	                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
	}
}

$num_rows_contas_rec = mysqli_num_rows($contas_rec);

while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){  
	$cod_conta = $registro_contas_rec->ctr_codigo_conta;
    $total_pagar=0;
    $valor_pago=0;
    $total_vencidas=0;
    $total_avencer=0;

    if (substr($conta_inicio, 3, 4)==0 && substr($conta_fim, 3, 4)!=9999){
    	if ($cod_conta==$conta_fim){
			$codigo_sub_conta = substr($cod_conta, 0, 3);
			$valor_parcela = $registro_contas_rec->ctr_valor_parcela;
			$valor_desconto = $registro_contas_rec->ctr_valor_desconto;
			$valor_juros = $registro_contas_rec->ctr_valor_juros;
			$valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
			$emissao = $registro_contas_rec->ctr_data_emissao;
			$vencimento = $registro_contas_rec->ctr_data_vencimento;
			$situacao = $registro_contas_rec->ctr_situacao;
            $ctr_id = $registro_contas_rec->ctr_id;
			$numero_id = $registro_contas_rec->ctr_numero_doc;
			$codigo_cliente = $registro_contas_rec->ctr_codigo_cliente_fornecedor;
			$parcela = $registro_contas_rec->ctr_parcela;
			$razao = substr($registro_contas_rec->ctr_nome_cliente, 0,45);
			$codigo_banco = $registro_contas_rec->ctr_codigo_banco;
			$numero_cheque = $registro_contas_rec->ctr_numero_cheque;
			$data_pagamento=0;

			if ($situacao == "P" || $situacao == "C"){
				$conta_baixada = mysqli_query($conector, "SELECT bcr_valor_pagamento,bcr_data_pagamento
				                                       FROM baixa_contas_receber 
													  WHERE bcr_id='$ctr_id'");
																				 
				while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
						$ctr_valor_pago = $registro_conta_baixada->bcr_valor_pagamento;
						$valor_pago = $valor_pago + $ctr_valor_pago;
						$data_pagamento = new DateTime($registro_conta_baixada->bcr_data_pagamento);
				}
			}
			else if ($tipo_data=="P"){
				$valor_pago = $registro_contas_rec->bcr_valor_pagamento;
			} 

			$total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

			if ( $tipo_data!="P"){
				if ($situacao == "C"){
					if ($vencimento < $data_sistema) {
						$total_vencidas= $total_pagar - $valor_pago;
						$total_abertas=  $total_pagar - $valor_pago;

						$total_vencida_conta_sintetica= $total_vencida_conta_sintetica + $total_pagar - $valor_pago;
						$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar - $valor_pago;
					} else {
						$total_avencer= $total_pagar - $valor_pago;
						$total_abertas= $total_pagar - $valor_pago;
		
						$total_avencer_conta_sintetica= $total_avencer_conta_sintetica + $total_pagar - $valor_pago;
						$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar - $valor_pago;
					}
				}
			}
											 
			if ( $tipo_data!="P"){
				if ($situacao != "P" && $situacao != "C") {
					if ($vencimento < $data_sistema) {
						$total_vencidas= $total_pagar;
						$total_abertas=  $total_pagar;
			
						$total_vencida_conta_sintetica= $total_vencida_conta_sintetica + $total_pagar;
						$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar;
					} else {
						$total_avencer= $total_pagar;
						$total_abertas= $total_pagar;
		
						$total_avencer_conta_sintetica= $total_avencer_conta_sintetica + $total_pagar;
						$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar;
					}
				}
			}

			$total_conta_sintetica = $total_conta_sintetica + $total_pagar;
   			$total_pago_conta_sintetica = $total_pago_conta_sintetica + $valor_pago;

		    for ($i = 0; $i < $qtd_contas; $i++) {
		        if ($arry_conta[$i]==$cod_conta) {
		            $j=$i;
		            $j++;

		            // valor da parcela
		            $j++;
		            $arry_conta[$j]=$arry_conta[$j] + $total_pagar;

		            // valor pago
		            $j++;
		            $arry_conta[$j]=$arry_conta[$j] + $valor_pago;

		            // valor vencido
		            $j++;
		            $arry_conta[$j]=$arry_conta[$j] + $total_vencidas;

		            // valor avencer
		            $j++;
		            $arry_conta[$j]=$arry_conta[$j] + $total_avencer;
		        }
		    }

		    for ($i = 0; $i < $qtd_sub_contas; $i++) {
		        if ($arry_sub_conta[$i]==$codigo_sub_conta) {
		            $j=$i;
		            $j++;

		            // valor da parcela
		            $j++;
		            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_pagar;

		            // valor pago
		            $j++;
		            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $valor_pago;

		            // valor vencido
		            $j++;
		            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_vencidas;

		            // valor avencer
		            $j++;
		            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_avencer;
		        }
		    }
    	}
    }
    else {
		$codigo_sub_conta = substr($cod_conta, 0, 3);
		$valor_parcela = $registro_contas_rec->ctr_valor_parcela;
		$valor_desconto = $registro_contas_rec->ctr_valor_desconto;
		$valor_juros = $registro_contas_rec->ctr_valor_juros;
		$valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
		$emissao = $registro_contas_rec->ctr_data_emissao;
		$vencimento = $registro_contas_rec->ctr_data_vencimento;
		$situacao = $registro_contas_rec->ctr_situacao;
        $ctr_id = $registro_contas_rec->ctr_id;
		$numero_id = $registro_contas_rec->ctr_numero_doc;
		$codigo_cliente = $registro_contas_rec->ctr_codigo_cliente_fornecedor;
		$parcela = $registro_contas_rec->ctr_parcela;
		$razao = substr($registro_contas_rec->ctr_nome_cliente, 0,45);
		$codigo_banco = $registro_contas_rec->ctr_codigo_banco;
		$numero_cheque = $registro_contas_rec->ctr_numero_cheque;
		$data_pagamento=0;

		if ($situacao == "P" || $situacao == "C"){
			$conta_baixada = mysqli_query($conector, "SELECT bcr_valor_pagamento,bcr_data_pagamento
			                                       FROM baixa_contas_receber 
												  WHERE bcr_id='$ctr_id'");
																				 
			while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
					$ctr_valor_pago = $registro_conta_baixada->bcr_valor_pagamento;
					$valor_pago = $valor_pago + $ctr_valor_pago;
					$data_pagamento = new DateTime($registro_conta_baixada->bcr_data_pagamento);
			}
		}
		else if ($tipo_data=="P"){
			$valor_pago = $registro_contas_rec->bcr_valor_pagamento;
		} 

		$total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

		if ( $tipo_data!="P"){
			if ($situacao == "C"){
				if ($vencimento < $data_sistema) {
					$total_vencidas= $total_pagar - $valor_pago;
					$total_abertas=  $total_pagar - $valor_pago;

					$total_vencida_conta_sintetica= $total_vencida_conta_sintetica + $total_pagar - $valor_pago;
					$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar - $valor_pago;
				} else {
					$total_avencer= $total_pagar - $valor_pago;
					$total_abertas= $total_pagar - $valor_pago;
		
					$total_avencer_conta_sintetica= $total_avencer_conta_sintetica + $total_pagar - $valor_pago;
					$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar - $valor_pago;
				}
			}
		}
											 
		if ( $tipo_data!="P"){
			if ($situacao != "P" && $situacao != "C") {
				if ($vencimento < $data_sistema) {
					$total_vencidas= $total_pagar;
					$total_abertas=  $total_pagar;
			
					$total_vencida_conta_sintetica= $total_vencida_conta_sintetica + $total_pagar;
					$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar;
				} else {
					$total_avencer= $total_pagar;
					$total_abertas= $total_pagar;
		
					$total_avencer_conta_sintetica= $total_avencer_conta_sintetica + $total_pagar;
					$total_aberto_conta_sintetica= $total_aberto_conta_sintetica + $total_pagar;
				}
			}
		}

		$total_conta_sintetica = $total_conta_sintetica + $total_pagar;
		$total_pago_conta_sintetica = $total_pago_conta_sintetica + $valor_pago;

	    for ($i = 0; $i < $qtd_contas; $i++) {
	        if ($arry_conta[$i]==$cod_conta) {
	            $j=$i;
	            $j++;

	            // valor da parcela
	            $j++;
	            $arry_conta[$j]=$arry_conta[$j] + $total_pagar;

	            // valor pago
	            $j++;
	            $arry_conta[$j]=$arry_conta[$j] + $valor_pago;

	            // valor vencido
	            $j++;
	            $arry_conta[$j]=$arry_conta[$j] + $total_vencidas;

	            // valor avencer
	            $j++;
	            $arry_conta[$j]=$arry_conta[$j] + $total_avencer;

	        }
	    }

	    for ($i = 0; $i < $qtd_sub_contas; $i++) {
	        if ($arry_sub_conta[$i]==$codigo_sub_conta) {
	            $j=$i;
	            $j++;

	            // valor da parcela
	            $j++;
	            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_pagar;

	            // valor pago
	            $j++;
	            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $valor_pago;

	            // valor vencido
	            $j++;
	            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_vencidas;

	            // valor avencer
	            $j++;
	            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_avencer;
	        }
	    }
    }
}

$liny=0;

$array_retorno = salta_pagina($pdf, $liny, $desc_centro_custos, $data_inicio_edi, $data_fim_edi,$desc_tipo_rel, $desc_tipo_data, $numero_paginas, $pagina_atual, $nome_cliente);	


$pagina_atual=$array_retorno[0];
$liny=$array_retorno[1];

$conta_sintetica = substr($conta_inicio, 0,1);
$conta_sintetica = str_pad($conta_sintetica, 7, "0", STR_PAD_RIGHT);
$plano_contas = mysqli_query($conector, "SELECT tbl_plano_contas_descricao FROM tbl_plano_contas
                                                 WHERE tbl_plano_contas_codigo_id ='$conta_sintetica'"); 
$registro_plano_contas = mysqli_fetch_object($plano_contas);
$descricao_conta = utf8_decode($registro_plano_contas->tbl_plano_contas_descricao);                                   

$pdf->SetFont('arial','',9); 
$pdf->SetXY(5, $liny);
$pdf->Cell(60,4, substr($conta_inicio, 0,1).' - '.$descricao_conta,0,0,'L');
$pdf->SetXY(95, $liny);
$pdf->Cell(25,4, number_format($total_pago_conta_sintetica,2,',','.'),0,0,'R');
$pdf->SetXY(120, $liny);
$pdf->Cell(25,4, number_format($total_avencer_conta_sintetica,2,',','.'),0,0,'R');
$pdf->SetXY(145, $liny);
$pdf->Cell(25,4, number_format($total_vencida_conta_sintetica,2,',','.'),0,0,'R');
$pdf->SetXY(170, $liny);
$pdf->Cell(25,4, number_format($total_conta_sintetica,2,',','.'),0,0,'R');


$index_sub_conta = 0;

for ($i = 0; $i < $qtd_sub_contas; $i++) {
 
    $index_sub_conta++;

	if ($index_sub_conta>6){
		if ($valor_sub_conta!=0){
			$liny=$liny+8;
			$pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

			$pdf->SetXY(5, $liny);
			$pdf->Cell(60,4, $pla_descricao ,0,0,'L');
			$pdf->SetXY(95, $liny);
			$pdf->Cell(25,4, number_format($valor_pago_sub_conta,2,',','.'),0,0,'R');
			$pdf->SetXY(120, $liny);
			$pdf->Cell(25,4, number_format($valor_avencer_sub_conta,2,',','.'),0,0,'R');
			$pdf->SetXY(145, $liny);
			$pdf->Cell(25,4, number_format($valor_vencido_sub_conta,2,',','.'),0,0,'R');
			$pdf->SetXY(170, $liny);
			$pdf->Cell(25,4, number_format($valor_sub_conta,2,',','.'),0,0,'R');

			$index_conta=0;

			for ($j = 0; $j < $qtd_contas; $j++) {

			    $index_conta++;

			    if ($index_conta>6){
			    	if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
			    		if ($valor_conta!=0){
							$liny=$liny+4;
							$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

							$pdf->SetXY(5, $liny);
							$pdf->Cell(60,4, $pla_descricao,0,0,'L');
							$pdf->SetXY(95, $liny);
							$pdf->Cell(25,4, number_format($valor_pago_conta,2,',','.'),0,0,'R');
							$pdf->SetXY(120, $liny);
							$pdf->Cell(25,4, number_format($valor_avencer_conta,2,',','.'),0,0,'R');
							$pdf->SetXY(145, $liny);
							$pdf->Cell(25,4, number_format($valor_vencido_conta,2,',','.'),0,0,'R');
							$pdf->SetXY(170, $liny);
							$pdf->Cell(25,4, number_format($valor_conta,2,',','.'),0,0,'R');

							if ($tipo_rel=="A"){
								$array_retorno=ler_notas($pdf, $liny, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc,$numero_paginas, $pagina_atual, $codigo_cli);
								$pagina_atual=$array_retorno[0];
								$liny=$array_retorno[1];
							}
			    		}
			    	}
				$index_conta=1;
			    }

				if ($index_conta==1){
					$conta_inicio = $arry_conta[$j];
				}
				else if ($index_conta==2){
					$descricao_conta = substr($arry_conta[$j],0,45);
				}
				else if ($index_conta==3){
					$valor_conta = $arry_conta[$j];
				}
				else if ($index_conta==4){
					$valor_pago_conta = $arry_conta[$j];
				}
				else if ($index_conta==5){
					$valor_vencido_conta = $arry_conta[$j];
				}
				else if ($index_conta==6){
					$valor_avencer_conta = $arry_conta[$j];
				}
			}
		}
		$index_sub_conta=1;
	}

	if ($index_sub_conta==1){
		$cod_sub_conta = $arry_sub_conta[$i];
	}
	else if ($index_sub_conta==2){
		$descricao_sub_conta = substr($arry_sub_conta[$i],0,45);
	}
	else if ($index_sub_conta==3){
		$valor_sub_conta = $arry_sub_conta[$i];
	}
	else if ($index_sub_conta==4){
		$valor_pago_sub_conta = $arry_sub_conta[$i];
	}
	else if ($index_sub_conta==5){
		$valor_vencido_sub_conta = $arry_sub_conta[$i];
	}
	else if ($index_sub_conta==6){
		$valor_avencer_sub_conta = $arry_sub_conta[$i];
	}
}

if ($valor_sub_conta!=0){
	$liny=$liny+8;
	$pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, $pla_descricao ,0,0,'L');
	$pdf->SetXY(95, $liny);
	$pdf->Cell(25,4, number_format($valor_pago_sub_conta,2,',','.'),0,0,'R');
	$pdf->SetXY(120, $liny);
	$pdf->Cell(25,4, number_format($valor_avencer_sub_conta,2,',','.'),0,0,'R');
	$pdf->SetXY(145, $liny);
	$pdf->Cell(25,4, number_format($valor_vencido_sub_conta,2,',','.'),0,0,'R');
	$pdf->SetXY(170, $liny);
	$pdf->Cell(25,4, number_format($valor_sub_conta,2,',','.'),0,0,'R');

	$index_conta=0;

	for ($j = 0; $j < $qtd_contas; $j++) {

	    $index_conta++;

		if ($index_conta>6){
		   	if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
		   		if ($valor_conta!=0){
					$liny=$liny+4;
					$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

					$pdf->SetXY(5, $liny);
					$pdf->Cell(60,4, $pla_descricao ,0,0,'L');
					$pdf->SetXY(95, $liny);
					$pdf->Cell(25,4, number_format($valor_pago_conta,2,',','.'),0,0,'R');
					$pdf->SetXY(120, $liny);
					$pdf->Cell(25,4, number_format($valor_avencer_conta,2,',','.'),0,0,'R');
					$pdf->SetXY(145, $liny);
					$pdf->Cell(25,4, number_format($valor_vencido_conta,2,',','.'),0,0,'R');
					$pdf->SetXY(170, $liny);
					$pdf->Cell(25,4, number_format($valor_conta,2,',','.'),0,0,'R');

					if ($tipo_rel=="A"){
						$array_retorno=ler_notas($pdf, $liny, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc,$numero_paginas, $pagina_atual, $codigo_cli);
						$pagina_atual=$array_retorno[0];
						$liny=$array_retorno[1];
					}
		   		}
		   	}
			$index_conta=1;
	   	}

		if ($index_conta==1){
			$conta_inicio = $arry_conta[$j];
		}
		else if ($index_conta==2){
			$descricao_conta = substr($arry_conta[$j],0,45);
		}
		else if ($index_conta==3){
			$valor_conta = $arry_conta[$j];
		}
		else if ($index_conta==4){
			$valor_pago_conta = $arry_conta[$j];
		}
		else if ($index_conta==5){
			$valor_vencido_conta = $arry_conta[$j];
		}
		else if ($index_conta==6){
			$valor_avencer_conta = $arry_conta[$j];
		}
	}

	if ($valor_conta!=0){
		$liny=$liny+4;
		$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

		$pdf->SetXY(5, $liny);
		$pdf->Cell(60,4, $pla_descricao,0,0,'L');
		$pdf->SetXY(95, $liny);
		$pdf->Cell(25,4, number_format($valor_pago_conta,2,',','.'),0,0,'R');
		$pdf->SetXY(120, $liny);
		$pdf->Cell(25,4, number_format($valor_avencer_conta,2,',','.'),0,0,'R');
		$pdf->SetXY(145, $liny);
		$pdf->Cell(25,4, number_format($valor_vencido_conta,2,',','.'),0,0,'R');
		$pdf->SetXY(170, $liny);
		$pdf->Cell(25,4, number_format($valor_conta,2,',','.'),0,0,'R');

		if ($tipo_rel=="A"){
			$array_retorno=ler_notas($pdf, $liny, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc,$numero_paginas, $pagina_atual, $codigo_cli);
			$pagina_atual=$array_retorno[0];
			$liny=$array_retorno[1];	
		}
	}
}

//$pdf->SetXY(4, 20);
//$pdf->Cell(202,51,'',1,0,'C');

//$liny=70;

//if ($liny>60) {
//$pagina_atual = salta_pagina($pdf, $liny, $desc_centro_custos, $data_inicio_edi, $data_fim_edi, $desc_tipo_rel, $desc_tipo_data, $numero_paginas, $pagina_atual);
//}

// FIM DO PROCESSAMENTO
$nomepdf= 'analise_pagamentos.pdf';
ob_clean(); 
$pdf->Output($nomepdf, "I");

mysqli_close($conector);

function salta_pagina ($pdf, $liny, $desc_centro_custos, $data_inicio_edi, $data_fim_edi, $desc_tipo_rel, $desc_tipo_data, $numero_paginas, $pagina_atual, $nome_cliente) {

	$pagina_atual++;
	$_SESSION['nome_setor']='Página: ' . $pagina_atual . ' de ' . $numero_paginas;

	$pdf->AddPage();
	$liny=21;

	$pdf->SetFont('arial','',9); 
	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Fonte Pagadora: ' . $nome_cliente,0,0,'L');

	$liny=$liny+4;
	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Centro de Custos: ' . $desc_centro_custos ,0,0,'L');

	$liny=$liny+4;

	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Período: '.$data_inicio_edi->format('d/m/Y').' até '.$data_fim_edi->format('d/m/Y').
	  ' - Relatório ' . $desc_tipo_rel . $desc_tipo_data,0,0,'L');

	$liny=$liny+5;
	$pdf->SetXY(2, $liny);
	$pdf->Cell(206,0,'',1,0,'L');

	$liny=$liny+4;


/*	$a_vencer=='S' && $vencidos=='S' && $pagos=='S'
	$a_vencer=='S' && $vencidos=='S' && $pagos==''
	$a_vencer=='S' && $vencidos=='' && $pagos=='S'
	$a_vencer=='S' && $vencidos=='' && $pagos==''

	$a_vencer=='' && $vencidos=='S' && $pagos=='S'
	$a_vencer=='' && $vencidos=='S' && $pagos==''
	$a_vencer=='' && $vencidos=='' && $pagos=='S'
*/
	
	$pdf->SetXY(95, $liny);
	$pdf->Cell(25,4, 'Recebidos',0,0,'R');
	$pdf->SetXY(120, $liny);
	$pdf->Cell(25,4, 'A Vencer',0,0,'R');
	$pdf->SetXY(145, $liny);
	$pdf->Cell(25,4, 'Vencidas',0,0,'R');
	$pdf->SetXY(170, $liny);
	$pdf->Cell(25,4, 'Total',0,0,'R');

	$liny=$liny+4;

	return [$pagina_atual, $liny];
}


function ler_notas($pdf, $liny, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc,$numero_paginas, $pagina_atual, $codigo_cli){

	include "conecta_mysql.inc"; 

	if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc==0){
		if ($tipo_data=="E"){
			$contas_rec = mysqli_query($conector, "SELECT *
				                               FROM contas_receber
		                                      WHERE ctr_data_emissao >='$data_inicio' and
									                ctr_data_emissao <='$data_fim' and
											        ctr_codigo_conta='$conta_inicio' and 
								                ctr_lixeira=0  
					 	                   ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
		}
		else if ($tipo_data=="V"){
			$contas_rec = mysqli_query($conector, "SELECT *
				                               FROM contas_receber
		                                      WHERE ctr_data_vencimento >='$data_inicio' and
									                ctr_data_vencimento <='$data_fim' and
											        ctr_codigo_conta='$conta_inicio' and 
								                ctr_lixeira=0
					 	                   ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
		}
		else if ($tipo_data=="P"){
			$contas_rec = mysqli_query($conector, "SELECT * 
			                               FROM baixa_contas_receber
			                         INNER JOIN contas_receber
			                                 ON bcr_id=ctr_id
			                              WHERE bcr_data_pagamento >='$data_inicio' and
								                bcr_data_pagamento <='$data_fim' and
								                ctr_codigo_conta='$conta_inicio' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
		}
	}
	else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc!=0) {
		if ($tipo_data=="E"){
			$contas_rec = mysqli_query($conector, "SELECT *
				                               FROM contas_receber
		                                      WHERE ctr_data_emissao >='$data_inicio' and
									                ctr_data_emissao <='$data_fim' and
											        ctr_codigo_conta='$conta_inicio' and
											        ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_lixeira=0 
					 	                   ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
		}
		else if ($tipo_data=="V"){
				$contas_rec = mysqli_query($conector, "SELECT *
				                               FROM contas_receber
		                                      WHERE ctr_data_vencimento >='$data_inicio' and
									                ctr_data_vencimento <='$data_fim' and
											        ctr_codigo_conta='$conta_inicio' and
											        ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_lixeira=0
					 	                   ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
		}
		else if ($tipo_data=="P"){
			$contas_rec = mysqli_query($conector, "SELECT * 
			                               FROM baixa_contas_receber
			                         INNER JOIN contas_receber
			                                 ON bcr_id=ctr_id
			                              WHERE bcr_data_pagamento >='$data_inicio' and
								                bcr_data_pagamento <='$data_fim' and
								                ctr_codigo_conta='$conta_inicio' and
											        ctr_codigo_c_custo='$codigo_cc' and 
								                ctr_lixeira=0
				 	                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
		}
	}

	$num_rows_contas_rec = mysqli_num_rows($contas_rec);

	$liny=$liny+4;

	$pdf->SetTextColor(128,128,128);
	$pdf->SetFont('arial','',7); 
	$pdf->SetXY(20, $liny);
	$pdf->Cell(30,4, 'Documento',0,0,'L');
	$pdf->SetXY(50, $liny);
	$pdf->Cell(60,4, 'Fonte Pagadora',0,0,'L');
	$pdf->SetXY(110, $liny);
	$pdf->Cell(15,4, 'Emissão',0,0,'L');
	$pdf->SetXY(125, $liny);
	$pdf->Cell(15,4, 'Vencimento',0,0,'L');
	$pdf->SetXY(140, $liny);
	$pdf->Cell(15,4, 'Valor',0,0,'R');
	$pdf->SetXY(155, $liny);
	$pdf->Cell(15,4, 'Recebimento',0,0,'L');
	$pdf->SetXY(170, $liny);
	$pdf->Cell(15,4, 'Valor',0,0,'R');
	$pdf->SetXY(185, $liny);
	$pdf->Cell(10,4, 'Forma Pgto',0,0,'L');
	/*$pdf->SetXY(190, $liny);
	$pdf->Cell(10,4, 'Situação',0,0,'L');
	$pdf->SetXY(210, $liny);
	$pdf->Cell(10,4, 'Banco',0,0,'L');
	$pdf->SetXY(220, $liny);
	$pdf->Cell(10,4, 'Cheque',0,0,'L');
*/
	$data_pag_edi='';

	while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){  
		$valor_parcela = $registro_contas_rec->ctr_valor_parcela;
		$valor_desconto = $registro_contas_rec->ctr_valor_desconto;
		$valor_juros = $registro_contas_rec->ctr_valor_juros;
		$valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
		$emissao = $registro_contas_rec->ctr_data_emissao;
		$emissao_edi = new DateTime($registro_contas_rec->ctr_data_emissao);
		$vencimento = $registro_contas_rec->ctr_data_vencimento;
		$vencimento_edi = new DateTime($registro_contas_rec->ctr_data_vencimento);
		$situacao = $registro_contas_rec->ctr_situacao;
        $ctr_id = $registro_contas_rec->ctr_id;
		$numero_id = $registro_contas_rec->ctr_numero_doc;
		$codigo_cliente = $registro_contas_rec->ctr_codigo_cliente_fornecedor;
		$parcela = $registro_contas_rec->ctr_parcela;
		$razao = substr($registro_contas_rec->ctr_nome_cliente, 0,38);
		$codigo_banco = $registro_contas_rec->ctr_codigo_banco;
		$numero_cheque = $registro_contas_rec->ctr_numero_cheque;
		$forma_pgto = $registro_contas_rec->ctr_codigo_forma_recebimento;
		$data_pagamento=0;
		$desc_situacao="";
		$valor_pago=0;
		
        if ($forma_pgto!=0){
			$forma_pagamento = mysqli_query($conector, "SELECT tbl_conta_pagamento_descricao
			                                       FROM tbl_conta_pagamento 
												  WHERE tbl_conta_pagamento_id='$forma_pgto'");
																					 
			$registro_forma_pagamento = mysqli_fetch_object($forma_pagamento);
			$desc_forma_pgto = $registro_forma_pagamento->tbl_conta_pagamento_descricao;
        }
        else {
        	$desc_forma_pgto = ''; 	
        }

		if ($situacao == "P" || $situacao == "C"){
			$conta_baixada = mysqli_query($conector, "SELECT bcr_valor_pagamento,bcr_data_pagamento
			                                       FROM baixa_contas_receber 
												  WHERE bcr_id='$ctr_id'");
																					 
			while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
					$ctr_valor_pago = $registro_conta_baixada->bcr_valor_pagamento;
					$valor_pago = $valor_pago + $ctr_valor_pago;
					$data_pag_edi = new DateTime($registro_conta_baixada->bcr_data_pagamento);
					$data_pag_edi = $data_pag_edi->format('d/m/Y');
					$data_pagamento = $registro_conta_baixada->bcr_data_pagamento;
			}
		}
		else if ($tipo_data=="P"){
			$valor_pago = $registro_contas_rec->bcr_valor_pagamento;
		} 

		$total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

		if ($situacao != "P" && $situacao != "C") {
			if ($vencimento < $data_sistema) {
				$desc_situacao = " Vencido";
			} else {
				$desc_situacao = "";
			}
		} 
		else if ($situacao == "P") {
			$desc_situacao = " Pago";
		} 
		else if ($situacao == "C") {
			if ($vencimento < $data_sistema) {
				$desc_situacao = " P Parc Vencida";
			} 
			else {
				$desc_situacao = " P Parc";
			}
		}

		$liny=$liny+4;

		$doc_imp = $numero_id . '/' . $parcela;

		$pdf->SetFont('arial','',7); 
		$pdf->SetXY(20, $liny);
		$pdf->Cell(30,4, $doc_imp,0,0,'L');
		$pdf->SetXY(50, $liny);
		$pdf->Cell(60,4, utf8_decode($razao),0,0,'L');
		$pdf->SetXY(110, $liny);
		$pdf->Cell(15,4, $emissao_edi->format('d/m/Y'),0,0,'L');
		$pdf->SetXY(125, $liny);
		$pdf->Cell(15,4, $vencimento_edi->format('d/m/Y'),0,0,'L');
		$pdf->SetXY(140, $liny);
		$pdf->Cell(15,4, number_format($total_pagar,2,',','.'),0,0,'R');

		if ($data_pagamento!=0){
			$pdf->SetXY(155, $liny);
			$pdf->Cell(15,4, $data_pag_edi,0,0,'L');
			$pdf->SetXY(170, $liny);
			$pdf->Cell(15,4, number_format($valor_pago,2,',','.'),0,0,'R');
			$pdf->SetXY(185, $liny);
			$pdf->Cell(15,4, utf8_decode($desc_forma_pgto),0,0,'L');
		}

        /*
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $desc_situacao);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $linha, $codigo_banco);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $linha, $numero_cheque);
		*/

	}
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('arial','',9); 

	return [$pagina_atual, $liny];
}


?>