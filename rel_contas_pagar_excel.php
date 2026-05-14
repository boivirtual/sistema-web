<?php
include "conecta_mysql.inc"; 
  
$data_sistema = date("Y-m-d");

$conta_inicio=0;    
$data_inicio=0; 
$data_fim=0;
$tipo_data='';
$tipo_rel='';
$a_vencer='';
$vencidos='';
$pagos='';
$criterio="";
$linha=0;
  
if(isset($_REQUEST["conta"]) && $_REQUEST["conta"]!=0) {
    $conta_inicio=$_REQUEST["conta"];
}

if ($conta_inicio==0 || $conta_inicio==3000000){
	$conta_inicio=3000000;
	$conta_fim=9999999;
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

if (isset($_REQUEST["a_vencer"]) && $_REQUEST["a_vencer"]!="") {
      $a_vencer= "S";
}
					
if (isset($_REQUEST["vencidos"]) && $_REQUEST["vencidos"]!="") {
      $vencidos= "S";
}
					
if (isset($_REQUEST["pagos"]) && $_REQUEST["pagos"]!="") {
      $pagos= "S";
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

if ($a_vencer=='' && $vencidos=='' && $pagos==''){
	$a_vencer='S';
	$vencidos='S';
	$pagos='S';
}

if ($tipo_data=="E"){ 
	$desc_tipo_data=" por Data de Emissão";
}
else if ($tipo_data=="V"){
	$desc_tipo_data=" por Data de Vencimento";  
}  
else {
	$desc_tipo_data=" por Data de Pagamento";
}

if ($tipo_rel=="A"){
	$desc_tipo_rel = " Analítico";
}
else {
	$desc_tipo_rel = " Sintético";
}

@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$_SESSION['data_inicio_ctp_rel']=$_REQUEST["data_inicio"];
$_SESSION['data_fim_ctp_rel']=$_REQUEST["data_fim"];
$_SESSION['tipo_data_ctp_rel']=$_REQUEST["tipo_data"];
$_SESSION['tipo_rel_ctp_rel']=$_REQUEST["tipo_rel"]; 
$_SESSION['situacao_avencer_ctp_rel']=$_REQUEST["a_vencer"];
$_SESSION['situacao_vencidas_ctp_rel']=$_REQUEST["vencidos"];
$_SESSION['situacao_pagas_ctp_rel']=$_REQUEST["pagos"];
$_SESSION['codigo_c_custo_ctp_rel']=0; 
$_SESSION['codigo_conta_ctp_rel']=$_REQUEST["conta"]; 

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

// 		Começa Excel
include  'phpexcel/Classes/PHPExcel.php';

// Instanciamos a classe
$objPHPExcel = new PHPExcel();

$nome_relatorio = "Análise de Pagamentos" . $desc_tipo_data . $desc_tipo_rel;

$objPHPExcel->getActiveSheet()->mergeCells('B1:F1');

if($data_inicio!=0){
			$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_empresa )
            ->setCellValue('B1', $nome_relatorio)
			->setCellValue("A2", "Periodo de:  " . $data_inicio_edi->format('d/m/Y'). " Ate:  " . $data_fim_edi->format('d/m/Y'));
}
else{
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_empresa )
            ->setCellValue('B1', $nome_relatorio);
}

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue("B3","Pago")
    ->setCellValue("C3","A vencer")
    ->setCellValue("D3","Vencidas")
    ->setCellValue("E3","Total");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(57);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(12);


$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C3') ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D3') ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E3') ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$linha=3;

// monta array das contas

$plano_contas = mysqli_query($conector, "SELECT * FROM tabela_plano_contas
    WHERE tab_codigo_plano_contas >='$conta_inicio' AND 
          tab_codigo_plano_contas <='$conta_fim'
	ORDER BY tab_codigo_plano_contas ASC"); 

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
	$cod_conta = $registro_plano_contas->tab_codigo_plano_contas;
    $descricao_conta = $registro_plano_contas->tab_descricao_plano_contas;                                   

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

//for ($i = 0; $i < $qtd_contas; $i++) {
//   print ($arry_conta[$i] . '<br/>');
//}

//print ('<br/><br/>');

//for ($i = 0; $i < $qtd_sub_contas; $i++) {
//   print ($arry_sub_conta[$i] . '<br/>');
//}

//exit;

// pega valores das contas
if ($data_inicio!=0 && $data_fim!=0){
	if ($tipo_data=="E"){
		if ($a_vencer=='S' && $vencidos=='' && $pagos==''){
			$contas_pagar = mysqli_query($conector, "SELECT * FROM contas_pagar
	        WHERE ctp_data_emissao >='$data_inicio' and
			      ctp_data_emissao <='$data_fim' and
                  ctp_data_vencimento >= '$data_sistema' and
			      ctp_situacao !='P' and					
			      ctp_codigo_conta>='$conta_inicio' and 
			      ctp_codigo_conta<='$conta_fim' 
			ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='S' && $vencidos=='S' && $pagos==''){
			$contas_pagar = mysqli_query($conector, "SELECT *
		        FROM contas_pagar
	        WHERE ctp_data_emissao >='$data_inicio' and
		          ctp_data_emissao <='$data_fim' and
										        ctp_situacao !='P' and					
										        ctp_codigo_conta>='$conta_inicio' and 
								                ctp_codigo_conta<='$conta_fim' 
				 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='S' && $vencidos=='S' && $pagos=='S') {
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE ctp_data_emissao >='$data_inicio' and
								                ctp_data_emissao <='$data_fim' and
								                ctp_codigo_conta>='$conta_inicio' and 
								                ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 

		}
		else if ($a_vencer=='' && $vencidos=='S' && $pagos=='S'){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE (ctp_data_emissao >='$data_inicio' and
										         ctp_data_emissao <='$data_fim' and
										         ctp_data_vencimento < '$data_sistema') or
										        (ctp_data_emissao >='$data_inicio' and
										         ctp_data_emissao <='$data_fim' and 
										         ctp_situacao !='') and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='' && $vencidos=='S' && $pagos==''){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE  ctp_data_emissao >='$data_inicio' and
										         ctp_data_emissao <='$data_fim' and
 										         ctp_data_vencimento < '$data_sistema' and
										         ctp_situacao !='P' and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 

		}
		else if ($a_vencer=='' && $vencidos=='' && $pagos=='S'){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE  ctp_data_emissao >='$data_inicio' and
										         ctp_data_emissao <='$data_fim' and
										         ctp_situacao !='' and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='S' && $vencidos=='' && $pagos=='S'){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE (ctp_data_emissao >='$data_inicio' and
										         ctp_data_emissao <='$data_fim' and
										         ctp_data_vencimento >= '$data_sistema') or
										        (ctp_data_emissao >='$data_inicio' and
									 	         ctp_data_emissao <='$data_fim' and 
										         ctp_situacao !='') and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
		}
	}
	else if ($tipo_data=="V"){
		if ($a_vencer=='S' && $vencidos=='' && $pagos==''){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE ctp_data_vencimento >='$data_inicio' and
								                ctp_data_vencimento <='$data_fim' and
                                                ctp_data_vencimento >= '$data_sistema' and
										        ctp_situacao !='P' and					
										        ctp_codigo_conta>='$conta_inicio' and 
								                ctp_codigo_conta<='$conta_fim' 
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='S' && $vencidos=='S' && $pagos==''){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE ctp_data_vencimento >='$data_inicio' and
								                ctp_data_vencimento <='$data_fim' and
										        ctp_situacao !='P' and					
										        ctp_codigo_conta>='$conta_inicio' and 
								                ctp_codigo_conta<='$conta_fim' 
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='S' && $vencidos=='S' && $pagos=='S') {
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE ctp_data_vencimento >='$data_inicio' and
								                ctp_data_vencimento <='$data_fim' and
								                ctp_codigo_conta>='$conta_inicio' and 
								                ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 

		}
		else if ($a_vencer=='' && $vencidos=='S' && $pagos=='S'){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE (ctp_data_vencimento >='$data_inicio' and
										         ctp_data_vencimento <='$data_fim' and
										         ctp_data_vencimento < '$data_sistema') or
										        (ctp_data_vencimento >='$data_inicio' and
										         ctp_data_vencimento <='$data_fim' and 
										         ctp_situacao !='') and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='' && $vencidos=='S' && $pagos==''){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE  ctp_data_vencimento >='$data_inicio' and
										         ctp_data_vencimento <='$data_fim' and
 										         ctp_data_vencimento < '$data_sistema' and
										         ctp_situacao !='P' and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 

		}
		else if ($a_vencer=='' && $vencidos=='' && $pagos=='S'){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE  ctp_data_vencimento >='$data_inicio' and
										         ctp_data_vencimento <='$data_fim' and
										         ctp_situacao !='' and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
		}
		else if ($a_vencer=='S' && $vencidos=='' && $pagos=='S'){
			$contas_pagar = mysqli_query($conector, "SELECT *
			                               FROM contas_pagar
	                                      WHERE (ctp_data_vencimento >='$data_inicio' and
										         ctp_data_vencimento <='$data_fim' and
										         ctp_data_vencimento >= '$data_sistema') or
										        (ctp_data_vencimento >='$data_inicio' and
									 	         ctp_data_vencimento <='$data_fim' and 
										         ctp_situacao !='') and
								                 ctp_codigo_conta>='$conta_inicio' and 
								                 ctp_codigo_conta<='$conta_fim'
				 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
		}
	}
	else if ($tipo_data=="P"){
		$contas_pagar = mysqli_query($conector, "SELECT * 
		                               FROM baixa_contas_pagar
		                         INNER JOIN contas_pagar
		                                 ON bcp_numero_id=ctp_numero_doc AND 
		                                    bcp_codigo_fornecedor=ctp_codigo_fornecedor AND 
		                                    bcp_parcela=ctp_parcela
		                              WHERE bcp_data_pagamento >='$data_inicio' and
							                bcp_data_pagamento <='$data_fim' and
							                ctp_codigo_conta>='$conta_inicio' and 
							                ctp_codigo_conta<='$conta_fim'
			 	                   ORDER BY ctp_codigo_conta, bcp_data_pagamento, bcp_numero_id  ASC"); 
	}
}

$num_rows_contas_pagar = mysqli_num_rows($contas_pagar);

while ($registro_contas_pagar = mysqli_fetch_object($contas_pagar)){  
	$cod_conta = $registro_contas_pagar->ctp_codigo_conta;
    $total_pagar=0;
    $valor_pago=0;
    $total_vencidas=0;
    $total_avencer=0;

    if (substr($conta_inicio, 3, 4)==0 && substr($conta_fim, 3, 4)!=9999){
    	if ($cod_conta==$conta_fim){
			$codigo_sub_conta = substr($cod_conta, 0, 3);
			$valor_parcela = $registro_contas_pagar->ctp_valor_parcela;
			$valor_desconto = $registro_contas_pagar->ctp_valor_desconto;
			$valor_juros = $registro_contas_pagar->ctp_valor_juros;
			$valor_outro = $registro_contas_pagar->ctp_outro_valor;
			$emissao = $registro_contas_pagar->ctp_data_emissao;
			$vencimento = $registro_contas_pagar->ctp_data_vencimento;
			$situacao = $registro_contas_pagar->ctp_situacao;
			$numero_id = $registro_contas_pagar->ctp_numero_doc;
			$codigo_fornecedor = $registro_contas_pagar->ctp_codigo_fornecedor;
			$parcela = $registro_contas_pagar->ctp_parcela;
			$nome_for = $registro_contas_pagar->ctp_nome_fornecedor;
			$codigo_banco = $registro_contas_pagar->ctp_codigo_banco;
			$numero_cheque = $registro_contas_pagar->ctp_numero_cheque;
			$data_pagamento=0;

			if ($situacao == "P" || $situacao == "C"){
				$conta_baixada = mysqli_query($conector, "SELECT bcp_valor_pagamento,bcp_data_pagamento
				                                       FROM baixa_contas_pagar 
													  WHERE bcp_numero_id='$numero_id' AND 
													        bcp_codigo_fornecedor='$codigo_fornecedor' AND  
															bcp_parcela='$parcela'");
																				 
				while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
						$bcp_vlr_pago = $registro_conta_baixada->bcp_valor_pagamento;
						$valor_pago = $valor_pago + $bcp_vlr_pago;
						$data_pagamento = new DateTime($registro_conta_baixada->bcp_data_pagamento);
				}
			}
			else if ($tipo_data=="P"){
				$valor_pago = $registro_contas_pagar->bcp_valor_pagamento;
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
		$valor_parcela = $registro_contas_pagar->ctp_valor_parcela;
		$valor_desconto = $registro_contas_pagar->ctp_valor_desconto;
		$valor_juros = $registro_contas_pagar->ctp_valor_juros;
		$valor_outro = $registro_contas_pagar->ctp_outro_valor;
		$emissao = $registro_contas_pagar->ctp_data_emissao;
		$vencimento = $registro_contas_pagar->ctp_data_vencimento;
		$situacao = $registro_contas_pagar->ctp_situacao;
		$numero_id = $registro_contas_pagar->ctp_numero_doc;
		$codigo_fornecedor = $registro_contas_pagar->ctp_codigo_fornecedor;
		$parcela = $registro_contas_pagar->ctp_parcela;
		$nome_for = $registro_contas_pagar->ctp_nome_fornecedor;
		$codigo_banco = $registro_contas_pagar->ctp_codigo_banco;
		$numero_cheque = $registro_contas_pagar->ctp_numero_cheque;
		$data_pagamento=0;

		if ($situacao == "P" || $situacao == "C"){
			$conta_baixada = mysqli_query($conector, "SELECT bcp_valor_pagamento,bcp_data_pagamento
			                                       FROM baixa_contas_pagar 
												  WHERE bcp_numero_id='$numero_id' AND 
												        bcp_codigo_fornecedor='$codigo_fornecedor' AND  
														bcp_parcela='$parcela'");
																				 
			while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
					$bcp_vlr_pago = $registro_conta_baixada->bcp_valor_pagamento;
					$valor_pago = $valor_pago + $bcp_vlr_pago;
					$data_pagamento = new DateTime($registro_conta_baixada->bcp_data_pagamento);
			}
		}
		else if ($tipo_data=="P"){
			$valor_pago = $registro_contas_pagar->bcp_valor_pagamento;
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

//for ($i = 0; $i < $qtd_contas; $i++) {
//   print ($arry_conta[$i] . '<br/>');
//}

//print ('<br/><br/>');

//for ($i = 0; $i < $qtd_sub_contas; $i++) {
//   print ($arry_sub_conta[$i] . '<br/>');
//}

//exit;



$linha++;
$pla_descricao = '3 - OBRIGAÇÕES';
$celulas = 'A'.$linha.':E'.$linha;
$objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'D6DBDF')))); 

$celulas = 'B'.$linha.':E'.$linha;
$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $pla_descricao);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $total_pago_conta_sintetica);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $total_avencer_conta_sintetica);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $total_vencida_conta_sintetica);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $total_conta_sintetica);

$index_sub_conta = 0;

for ($i = 0; $i < $qtd_sub_contas; $i++) {
 
    $index_sub_conta++;

	if ($index_sub_conta>6){
		if ($valor_sub_conta!=0){
			$linha++;
            $celulas = 'A'.$linha.':E'.$linha;
			$objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'EBEDEF')))); 

			$pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

			$celulas = 'B'.$linha.':E'.$linha;
			$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
			
			$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $pla_descricao);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $valor_pago_sub_conta);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_avencer_sub_conta);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_vencido_sub_conta);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_sub_conta);

			$index_conta=0;

			for ($j = 0; $j < $qtd_contas; $j++) {

			    $index_conta++;

			    if ($index_conta>6){
			    	if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
			    		if ($valor_conta!=0){
							$linha++;
							$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

							$celulas = 'A'.$linha.':E'.$linha;
            				$objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('font' => array('color' => array('rgb' => '566573')))); 

							$celulas = 'B'.$linha.':E'.$linha;
							$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
							$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $pla_descricao);
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $valor_pago_conta);
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_avencer_conta);
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_vencido_conta);
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_conta);

							if ($tipo_rel=="A"){
								$linha=ler_notas($data_sistema,$tipo_data,$a_vencer,$vencidos,$pagos,$data_inicio,$data_fim,$conta_inicio,$objPHPExcel,$linha);
							}
			    		}
			    	}
				$index_conta=1;
			    }

				if ($index_conta==1){
					$conta_inicio = $arry_conta[$j];
				}
				else if ($index_conta==2){
					$descricao_conta = $arry_conta[$j];
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
		$descricao_sub_conta = $arry_sub_conta[$i];
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
	$linha++;
	$celulas = 'A'.$linha.':E'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'EBEDEF')))); 

	$pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

	$celulas = 'B'.$linha.':E'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
	$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $pla_descricao);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $valor_pago_sub_conta);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_avencer_sub_conta);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_vencido_sub_conta);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_sub_conta);

	$index_conta=0;

	for ($j = 0; $j < $qtd_contas; $j++) {

	    $index_conta++;

		if ($index_conta>6){
		   	if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
		   		if ($valor_conta!=0){
					$linha++;
					$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

					$celulas = 'A'.$linha.':E'.$linha;
        			$objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('font' => array('color' => array('rgb' => '566573')))); 

					$celulas = 'B'.$linha.':E'.$linha;
					$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
					$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $pla_descricao);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $valor_pago_conta);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_avencer_conta);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_vencido_conta);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_conta);

					if ($tipo_rel=="A"){
						$linha=ler_notas($tipo_data,$a_vencer,$vencidos,$pagos,$data_inicio,$data_fim,$conta_inicio,$objPHPExcel,$linha);
					}
		   		}
		   	}
			$index_conta=1;
	   	}

		if ($index_conta==1){
			$conta_inicio = $arry_conta[$j];
		}
		else if ($index_conta==2){
			$descricao_conta = $arry_conta[$j];
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
		$linha++;
		$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

		$celulas = 'A'.$linha.':E'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('font' => array('color' => array('rgb' => '566573')))); 

		$celulas = 'B'.$linha.':E'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $pla_descricao);
    	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $valor_pago_conta);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_avencer_conta);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_vencido_conta);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_conta);

		if ($tipo_rel=="A"){
			$linha=ler_notas($tipo_data,$a_vencer,$vencidos,$pagos,$data_inicio,$data_fim,$conta_inicio,$objPHPExcel,$linha);
		}
	}
}


$arquivo = 'planilhas/contas_pagar.xls';

$callStartTime = microtime(true);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

print_r ('passei ate aqui ' . $objWriter);
exit;


//$objWriter->save(str_replace('.php', '.xlsx', 'planilhas/' . $sigla_atividde .'_pagamento_clients.xlsx'));
$objWriter->save(str_replace('.php', '.xls', $arquivo));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
ob_clean();
flush();


echo '<table class="table table-striped table-advance table-hover" id="tabela_lista_pagamentos" width="100%">';
echo '<tbody>';
echo "<tr>";
echo "<td>
    <p id='id_ctr'>Planilha gerada: &nbsp;". $arquivo ."<a class='btn' href='download_palanilha_analise_pagamentos.php?id=".$arquivo."'><i class='icon_cloud-download' title='Download da planilha' ></i></a></p></td>";

echo "</tr>";
echo '</tbody>';
echo '</table>';

mysqli_close($conector);
exit;

function ler_notas($data_sistema,$tipo_data,$a_vencer,$vencidos,$pagos,$data_inicio,$data_fim,$conta_inicio,$objPHPExcel,$linha){

	include "conecta_mysql.inc"; 

	if ($data_inicio!=0 && $data_fim!=0){
		if ($tipo_data=="E"){
			if ($a_vencer=='S' && $vencidos=='' && $pagos==''){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE ctp_data_emissao >='$data_inicio' and
									                ctp_data_emissao <='$data_fim' and
	                                                ctp_data_vencimento >= '$data_sistema' and
											        ctp_situacao !='P' and					
											        ctp_codigo_conta='$conta_inicio'  
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='S' && $vencidos=='S' && $pagos==''){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE ctp_data_emissao >='$data_inicio' and
									                ctp_data_emissao <='$data_fim' and
											        ctp_situacao !='P' and					
											        ctp_codigo_conta='$conta_inicio'  
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='S' && $vencidos=='S' && $pagos=='S') {
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE ctp_data_emissao >='$data_inicio' and
									                ctp_data_emissao <='$data_fim' and
									                ctp_codigo_conta='$conta_inicio'  
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 

			}
			else if ($a_vencer=='' && $vencidos=='S' && $pagos=='S'){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE (ctp_data_emissao >='$data_inicio' and
											         ctp_data_emissao <='$data_fim' and
											         ctp_data_vencimento < '$data_sistema') or
											        (ctp_data_emissao >='$data_inicio' and
											         ctp_data_emissao <='$data_fim' and 
											         ctp_situacao !='') and
									                 ctp_codigo_conta='$conta_inicio' 
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='' && $vencidos=='S' && $pagos==''){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE  ctp_data_emissao >='$data_inicio' and
											         ctp_data_emissao <='$data_fim' and
	 										         ctp_data_vencimento < '$data_sistema' and
											         ctp_situacao !='P' and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 

			}
			else if ($a_vencer=='' && $vencidos=='' && $pagos=='S'){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE  ctp_data_emissao >='$data_inicio' and
											         ctp_data_emissao <='$data_fim' and
											         ctp_situacao !='' and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='S' && $vencidos=='' && $pagos=='S'){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE (ctp_data_emissao >='$data_inicio' and
											         ctp_data_emissao <='$data_fim' and
											         ctp_data_vencimento >= '$data_sistema') or
											        (ctp_data_emissao >='$data_inicio' and
										 	         ctp_data_emissao <='$data_fim' and 
											         ctp_situacao !='') and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_emissao, ctp_numero_doc ASC"); 
			}
		}
		else if ($tipo_data=="V"){
			if ($a_vencer=='S' && $vencidos=='' && $pagos==''){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE ctp_data_vencimento >='$data_inicio' and
									                ctp_data_vencimento <='$data_fim' and
	                                                ctp_data_vencimento >= '$data_sistema' and
											        ctp_situacao !='P' and					
											        ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='S' && $vencidos=='S' && $pagos==''){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE ctp_data_vencimento >='$data_inicio' and
									                ctp_data_vencimento <='$data_fim' and
											        ctp_situacao !='P' and					
											        ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='S' && $vencidos=='S' && $pagos=='S') {
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE ctp_data_vencimento >='$data_inicio' and
									                ctp_data_vencimento <='$data_fim' and
									                ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 

			}
			else if ($a_vencer=='' && $vencidos=='S' && $pagos=='S'){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE (ctp_data_vencimento >='$data_inicio' and
											         ctp_data_vencimento <='$data_fim' and
											         ctp_data_vencimento < '$data_sistema') or
											        (ctp_data_vencimento >='$data_inicio' and
											         ctp_data_vencimento <='$data_fim' and 
											         ctp_situacao !='') and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='' && $vencidos=='S' && $pagos==''){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE  ctp_data_vencimento >='$data_inicio' and
											         ctp_data_vencimento <='$data_fim' and
	 										         ctp_data_vencimento < '$data_sistema' and
											         ctp_situacao !='P' and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 

			}
			else if ($a_vencer=='' && $vencidos=='' && $pagos=='S'){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE  ctp_data_vencimento >='$data_inicio' and
											         ctp_data_vencimento <='$data_fim' and
											         ctp_situacao !='' and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
			}
			else if ($a_vencer=='S' && $vencidos=='' && $pagos=='S'){
				$contas_pagar = mysqli_query($conector, "SELECT *
				                               FROM contas_pagar
		                                      WHERE (ctp_data_vencimento >='$data_inicio' and
											         ctp_data_vencimento <='$data_fim' and
											         ctp_data_vencimento >= '$data_sistema') or
											        (ctp_data_vencimento >='$data_inicio' and
										 	         ctp_data_vencimento <='$data_fim' and 
											         ctp_situacao !='') and
									                 ctp_codigo_conta='$conta_inicio'
					 	                   ORDER BY ctp_codigo_conta, ctp_data_vencimento, ctp_numero_doc ASC"); 
			}
		}
		else if ($tipo_data=="P"){
			$contas_pagar = mysqli_query($conector, "SELECT * 
			                               FROM baixa_contas_pagar
			                         INNER JOIN contas_pagar
			                                 ON bcp_numero_id=ctp_numero_doc AND 
			                                    bcp_codigo_fornecedor=ctp_codigo_fornecedor AND 
			                                    bcp_parcela=ctp_parcela
			                              WHERE bcp_data_pagamento >='$data_inicio' and
								                bcp_data_pagamento <='$data_fim' and
								                ctp_codigo_conta='$conta_inicio'
				 	                   ORDER BY ctp_codigo_conta, bcp_data_pagamento, bcp_numero_id  ASC"); 
		}
	}

	$num_rows_contas_pagar = mysqli_num_rows($contas_pagar);

	//$linha++;

	$celulas = 'G'.$linha.':U'.$linha;
    $objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('font' => array('size'  => 10,'color' => array('rgb' => '566573')))); 

	$celulas = 'H'.$linha.':L'.$linha;
    $objPHPExcel->getActiveSheet()->mergeCells($celulas);

	$celulas = 'G'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$celulas = 'M'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$celulas = 'N'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$celulas = 'O'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$celulas = 'P'.$linha.':U'.$linha;
	$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
    	->setCellValue("G".$linha,"Documento")
    	->setCellValue("H".$linha,"Fonte Recebedora")
    	->setCellValue("M".$linha,"Emissão")
    	->setCellValue("N".$linha,"Vencimento")
    	->setCellValue("O".$linha,"Valor")
    	->setCellValue("P".$linha,"Pagamento")
    	->setCellValue("Q".$linha,"Vlr Pagto")
    	->setCellValue("R".$linha,"Situação")
    	->setCellValue("S".$linha,"Forma Pgto")
    	->setCellValue("T".$linha,"Banco")
    	->setCellValue("U".$linha,"Cheque");


	while ($registro_contas_pagar = mysqli_fetch_object($contas_pagar)){  
		$valor_parcela = $registro_contas_pagar->ctp_valor_parcela;
		$valor_desconto = $registro_contas_pagar->ctp_valor_desconto;
		$valor_juros = $registro_contas_pagar->ctp_valor_juros;
		$valor_outro = $registro_contas_pagar->ctp_outro_valor;
		$emissao = $registro_contas_pagar->ctp_data_emissao;
		$emissao_edi = new DateTime($registro_contas_pagar->ctp_data_emissao);
		$vencimento = $registro_contas_pagar->ctp_data_vencimento;
		$vencimento_edi = new DateTime($registro_contas_pagar->ctp_data_vencimento);
		$situacao = $registro_contas_pagar->ctp_situacao;
		$numero_id = $registro_contas_pagar->ctp_numero_doc;
		$codigo_fornecedor = $registro_contas_pagar->ctp_codigo_fornecedor;
		$parcela = $registro_contas_pagar->ctp_parcela;
		$nome_for = $registro_contas_pagar->ctp_nome_fornecedor;
		$codigo_banco = $registro_contas_pagar->ctp_codigo_banco;
		$numero_cheque = $registro_contas_pagar->ctp_numero_cheque;
		$forma_pgto = $registro_contas_pagar->ctp_conta_pagamento;
		$data_pagamento=0;
		$desc_situacao="";
		$valor_pago=0;
		
        if ($forma_pgto!=0){
			$forma_pagamento = mysqli_query($conector, "SELECT tab_descricao_forma_rec
			                                       FROM tabela_forma_recebimento 
												  WHERE tab_codigo_forma_rec='$forma_pgto'");
																					 
			$registro_forma_pagamento = mysqli_fetch_object($forma_pagamento);
			$desc_forma_pgto = $registro_forma_pagamento->tab_descricao_forma_rec;
        }
        else {
        	$desc_forma_pgto = ''; 	
        }

		if ($situacao == "P" || $situacao == "C"){
			$conta_baixada = mysqli_query($conector, "SELECT bcp_valor_pagamento,bcp_data_pagamento
			                                       FROM baixa_contas_pagar 
												  WHERE bcp_numero_id='$numero_id' AND 
												        bcp_codigo_fornecedor='$codigo_fornecedor' AND  
														bcp_parcela='$parcela'");
																					 
			while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
					$bcp_vlr_pago = $registro_conta_baixada->bcp_valor_pagamento;
					$valor_pago = $valor_pago + $bcp_vlr_pago;
					$data_pag_edi = new DateTime($registro_conta_baixada->bcp_data_pagamento);
					$data_pagamento = $registro_conta_baixada->bcp_data_pagamento;
			}
		}
		else if ($tipo_data=="P"){
			$valor_pago = $registro_contas_pagar->bcp_valor_pagamento;
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

		$linha++;
		$doc_imp = $numero_id . '/' . $parcela;

		$celulas = 'G'.$linha.':U'.$linha;
        $objPHPExcel->getActiveSheet()->getStyle($celulas)->applyFromArray(array('font' => array('size'  => 10,'color' => array('rgb' => '566573')))); 

		$celulas = 'G'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	    $celulas = 'M'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$celulas = 'N'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$celulas = 'O'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$celulas = 'P'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$celulas = 'Q'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$celulas = 'S'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$celulas = 'T'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$celulas = 'U'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $doc_imp);

	    $celulas = 'H'.$linha.':L'.$linha;
        $objPHPExcel->getActiveSheet()->mergeCells($celulas);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $nome_for);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $emissao_edi->format('d/m/Y'));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $vencimento_edi->format('d/m/Y'));
	    $celulas = 'O'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $total_pagar);

		if ($data_pagamento!=0){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $data_pag_edi->format('d/m/Y'));
		}

	    $celulas = 'Q'.$linha;
		$objPHPExcel->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode("#,##0.00");
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $valor_pago);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $desc_situacao);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $linha, $desc_forma_pgto);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $linha, $codigo_banco);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $linha, $numero_cheque);

	}
	return $linha;
}


?>