<?php
// 		Começa Excel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$banco = $cnpj_cliente;
include_once "conecta_mysql_credenciais.inc";

  $conector = mysqli_connect($servidor, $usuario_bd, $senha_bd);
  
  if (mysqli_connect_error()) {
  	  print_r("Falha na conexão: ", mysqli_connect_error());
      exit;
  }

  $bancoselecionado = mysqli_select_db($conector,$banco);

  if ($bancoselecionado === FALSE) {
  	  print_r("Falha na seleção do banco de dados: " . mysqli_error($conector));
      exit;
  }
  
$data_sistema = date("Y-m-d");
$data_hoje = date("d/m/Y");

@ session_start(); 

$codigo_fornecedor = $_REQUEST["fornecedor"];
$codigo_conta = $_REQUEST["conta"];
$codigo_fazenda = $_REQUEST["fazendas"];
$codigo_cc = $_REQUEST["codigo_cc"];
$data_inicial = $_REQUEST["data_inicial"];
$data_final = $_REQUEST["data_final"];
$tipo_rel = $_REQUEST["tipo_rel"];
$tipo_data = $_REQUEST["tipo_data"];
$descricao_filtro= $_REQUEST["descricao_filtro"];

$array_conta = $_REQUEST["conta"];
$conta = array();
$matriz_itens = explode(",", $array_conta);
$quantidade_itens = count($matriz_itens);

// monta array das contas
for($i=0; $i < $quantidade_itens; $i++) {
    if (substr($matriz_itens[$i], 3, 4) !=0) {
        $conta[$i]=$matriz_itens[$i];
    }
}

$conta = implode(',', $conta);

$wconta = '';

if ($array_conta!='') {
    $wconta = " AND (ctp_codigo_conta IN($conta) OR (ctp_codigo_conta IS NULL AND ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_codigo_conta IN ($conta))))";
}

$fornecedor= array();
$matriz_itens = explode(",", $codigo_fornecedor);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $fornecedor[$i]=$matriz_itens[$i];
}

$fornecedor = implode(',', $fornecedor);
$fornecedor = substr($fornecedor,0, -1);

$wfornecedor = '';

if ($codigo_fornecedor!='') {
    $wfornecedor = " AND ctp_codigo_fornecedor IN(";
    $wfornecedor.= $fornecedor;
    $wfornecedor.= ")";
}

$fazendas= array();
$matriz_itens = explode(",", $codigo_fazenda);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $fazendas[$i]=$matriz_itens[$i];
}

$fazendas = implode(',', $fazendas);
$fazendas = substr($fazendas,0, -1);

$wfazendas = '';

if ($codigo_fazenda!='') {
    $wfazendas = " AND (ctp_codigo_fazenda IN($fazendas) OR (ctp_codigo_fazenda IS NULL AND ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_codigo_local IN ($fazendas))))";
}

$centro_custo= array();
$matriz_itens = explode(",", $codigo_cc);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $centro_custo[$i]=$matriz_itens[$i];
}

$centro_custo = implode(',', $centro_custo);
$centro_custo = substr($centro_custo,0, -1);

$wcc = '';

if ($codigo_cc!='') {
    $wcc = " AND (ctp_codigo_centro_custos IN($centro_custo) OR (ctp_codigo_centro_custos IS NULL AND ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_codigo_cc IN ($centro_custo))))";
}

$a_vencer='';
$vencidos='';
$pagos='';

$conta_inicio = substr($codigo_conta, 0, 7);

if ($conta_inicio==0 || substr($conta_inicio, 1, 6) == 0){
    if ($conta_inicio==0) {
        $conta_inicio= 2000000;
         $conta_fim= 9999999;
    }
    else {
        $inicio_conta = substr($conta_inicio, 0, 1);
        $conta_inicio= $inicio_conta . '000000';
        $conta_fim=$inicio_conta . 999999;
    }
}
else if (substr($conta_inicio, 3, 4)==0){
    $conta_fim=substr($conta_inicio, 0, 3) . 9999;
}
else {
    $conta_fim=3999999;
    $conta_inicio=substr($conta_fim, 0, 3) . '0000';
}

$nome_relatorio = "Análise de Pagamentos";

$spreadsheet->getActiveSheet()->mergeCells('A1:F1');
$spreadsheet->getActiveSheet()->mergeCells('G1:H1');
$spreadsheet->getActiveSheet()->mergeCells('B2:H2');
$spreadsheet->getActiveSheet()->mergeCells('A3:H3');
$spreadsheet->getActiveSheet()->mergeCells('A4:D4');

$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_relatorio)			
			->setCellValue("G1", "Data: " . $data_hoje)
			->setCellValue("A2", "Filtros: ")
			->setCellValue("B2", $descricao_filtro);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A4","Conta")
    ->setCellValue("E4","A vencer")
    ->setCellValue("F4","Vencidas")
    ->setCellValue("G4","Pago")
    ->setCellValue("H4","Total");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('E4:H4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$linha=4;

    $plano_contas = "SELECT * FROM tbl_plano_contas 
        WHERE tbl_plano_contas_codigo_id >=2000000
        ORDER BY tbl_plano_contas_codigo_id ASC"; 

    $plano_contas = mysqli_query($conector, $plano_contas);

    $num_rows_contas = mysqli_num_rows($plano_contas);

    $total_conta_sintetica=0;
    $total_pago_conta_sintetica=0;
    $total_vencida_conta_sintetica=0;
    $total_aberto_conta_sintetica=0;
    $total_avencer_conta_sintetica=0;

    $total_sem_conta=0;
    $total_pago_sem_conta=0;
    $total_vencido_sem_conta=0;
    $total_avencer_sem_conta=0;

    $arry_conta_sintetica = array();
    $arry_conta = array();
    $arry_sub_conta = array();

    $conta_sintetica_anterior = 0;
    $conta_anterior = 0;
    $sub_conta_anterior = 0;

    $index_array_conta_sintetica=0;
    $index_array_conta=0;
    $index_array_sub_conta=0;

    while ($registro_plano_contas = mysqli_fetch_object($plano_contas)){  
        $cod_conta = $registro_plano_contas->tbl_plano_contas_codigo_id;
        $descricao_conta = utf8_encode($registro_plano_contas->tbl_plano_contas_descricao);
        $codigo_conta_sintetica = substr($cod_conta, 0, 1);
        $codigo_sub_conta = substr($cod_conta, 0, 3);
        $codigo_seis_conta = substr($cod_conta, 1, 6);
        $codigo_quatro_conta = substr($cod_conta, 3, 4);

        if ($codigo_conta_sintetica!=$conta_sintetica_anterior){
            $arry_conta_sintetica[$index_array_conta_sintetica]=$codigo_conta_sintetica;
            $index_array_conta_sintetica++;
            $arry_conta_sintetica[$index_array_conta_sintetica]=$descricao_conta;
            $index_array_conta_sintetica++;
            $arry_conta_sintetica[$index_array_conta_sintetica]=0;
            $index_array_conta_sintetica++;
            $arry_conta_sintetica[$index_array_conta_sintetica]=0;
            $index_array_conta_sintetica++;
            $arry_conta_sintetica[$index_array_conta_sintetica]=0;
            $index_array_conta_sintetica++;
            $arry_conta_sintetica[$index_array_conta_sintetica]=0;
            $index_array_conta_sintetica++;
            $conta_sintetica_anterior=$codigo_conta_sintetica;
        }

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

    $qtd_contas_sintetica = count($arry_conta_sintetica);
    $qtd_contas = count($arry_conta);
    $qtd_sub_contas = count($arry_sub_conta);

    $contas_pag = "SELECT * FROM baixa_contas_pagar
        INNER JOIN contas_pagar
                ON bcp_id=ctp_id 
        WHERE bcp_data_pagamento >='$data_inicial' AND
              bcp_data_pagamento <='$data_final' 
              " . $wconta . $wfazendas . $wcc . $wfornecedor . 
        " ORDER BY bcp_numero_id ASC, bcp_data_pagamento ASC, ctp_codigo_conta ASC"; 

    $contas_pag = mysqli_query($conector, $contas_pag);
    $num_rows_contas = mysqli_num_rows($contas_pag);
    $chave_ctp_anterior = 0;

    while ($registro_contas_pagar = mysqli_fetch_object($contas_pag)){  
        $numero_parcela = $registro_contas_pagar->bcp_parcela;
        $ctp_id = $registro_contas_pagar->ctp_id;
        $chave_ctp = $ctp_id . $numero_parcela;

        $cod_conta = $registro_contas_pagar->ctp_codigo_conta;
        $total_pagar=0;
        $valor_pago=0;
        $total_vencidas=0;
        $total_avencer=0;

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
        $nome_for = utf8_encode($registro_contas_pagar->ctp_nome_fornecedor);
        $codigo_banco = $registro_contas_pagar->ctp_codigo_banco;
        $numero_cheque = $registro_contas_pagar->ctp_numero_cheque;
        $data_pagamento = new DateTime($registro_contas_pagar->bcp_data_pagamento);
        $valor_pago = $registro_contas_pagar->bcp_valor_pagamento;

                /*if ($situacao == "P" || $situacao == "C"){
                    $conta_baixada = mysqli_query($conector, "SELECT bcp_valor_pagamento,bcp_data_pagamento FROM baixa_contas_pagar 
                    WHERE bcp_id='$ctp_id'");
                                                                                     
                    while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                            
                            $valor_pago = $valor_pago + $bcp_vlr_pago;
                            $data_pagamento = new DateTime($registro_conta_baixada->bcp_data_pagamento);
                    }
                }
                else if ($tipo_data=="P"){
                    $valor_pago = $registro_contas_pagar->bcp_valor_pagamento;
                }*/

                //$total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

        if ($chave_ctp_anterior!=$chave_ctp) {
            $total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
            $chave_ctp_anterior=$chave_ctp;
        }
        else {
            $total_pagar = 0;
        }

        //if ($situacao == "C"){
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
                //}
                                                 
                /*if ( $tipo_data!="P"){
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
                }*/

        if (substr($conta_inicio, 3, 4)==0 && substr($conta_fim, 3, 4)!=9999){
            if ($cod_conta==$conta_fim){
                $fatias = montar_fatias_conta_rateio($conector, $ctp_id, $registro_contas_pagar->ctp_codigo_conta, $total_pagar, $valor_pago, $total_vencidas, $total_avencer);

                if (count($fatias) == 0) {
                    $total_sem_conta = $total_sem_conta + $total_pagar;
                    $total_pago_sem_conta = $total_pago_sem_conta + $valor_pago;
                    $total_vencido_sem_conta = $total_vencido_sem_conta + $total_vencidas;
                    $total_avencer_sem_conta = $total_avencer_sem_conta + $total_avencer;
                }

                foreach ($fatias as $fatia) {
                    $cod_conta = $fatia['cod_conta'];
                    $total_pagar = $fatia['total_pagar'];
                    $valor_pago = $fatia['valor_pago'];
                    $total_vencidas = $fatia['total_vencidas'];
                    $total_avencer = $fatia['total_avencer'];
                    $codigo_sub_conta = substr($cod_conta, 0, 3);
                    $codigo_conta_sintetica = substr($cod_conta, 0, 1);

                    $total_conta_sintetica = $total_conta_sintetica + $total_pagar;
                    $total_pago_conta_sintetica = $total_pago_conta_sintetica + $valor_pago;

                    for ($i = 0; $i < $qtd_contas_sintetica; $i++) {
                        if ($arry_conta_sintetica[$i]==$codigo_conta_sintetica) {
                            $j=$i;
                            $j++;

                            // valor da parcela
                            $j++;
                            $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $total_pagar;

                            // valor pago
                            $j++;
                            $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $valor_pago;

                            // valor vencido
                            $j++;
                            $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $total_vencidas;

                            // valor avencer
                            $j++;
                            $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $total_avencer;
                        }
                    }

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
        }
        else {
            $codigo_sub_conta = substr($cod_conta, 0, 3);
            $codigo_conta_sintetica = substr($cod_conta, 0, 1);
        /*    $valor_parcela = $registro_contas_pagar->ctp_valor_parcela;
            $valor_desconto = $registro_contas_pagar->ctp_valor_desconto;
            $valor_juros = $registro_contas_pagar->ctp_valor_juros;
            $valor_outro = $registro_contas_pagar->ctp_outro_valor;
            $emissao = $registro_contas_pagar->ctp_data_emissao;
            $vencimento = $registro_contas_pagar->ctp_data_vencimento;
            $situacao = $registro_contas_pagar->ctp_situacao;
            $numero_id = $registro_contas_pagar->ctp_numero_doc;
            $ctp_id = $registro_contas_pagar->ctp_id;
            $codigo_fornecedor = $registro_contas_pagar->ctp_codigo_fornecedor;
            $parcela = $registro_contas_pagar->ctp_parcela;
            $nome_for = utf8_encode($registro_contas_pagar->ctp_nome_fornecedor);
            $numero_cheque = $registro_contas_pagar->ctp_numero_cheque;
            $data_pagamento = new DateTime($registro_conta_baixada->bcp_data_pagamento);*/

            /*if ($situacao == "P" || $situacao == "C"){
                $conta_baixada = mysqli_query($conector, "SELECT bcp_valor_pagamento,bcp_data_pagamento FROM baixa_contas_pagar 
                    WHERE bcp_id='$ctp_id'");
                                                                                     
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
            */
            $fatias = montar_fatias_conta_rateio($conector, $ctp_id, $registro_contas_pagar->ctp_codigo_conta, $total_pagar, $valor_pago, $total_vencidas, $total_avencer);

            if (count($fatias) == 0) {
                $total_sem_conta = $total_sem_conta + $total_pagar;
                $total_pago_sem_conta = $total_pago_sem_conta + $valor_pago;
                $total_vencido_sem_conta = $total_vencido_sem_conta + $total_vencidas;
                $total_avencer_sem_conta = $total_avencer_sem_conta + $total_avencer;
            }

            foreach ($fatias as $fatia) {
                $cod_conta = $fatia['cod_conta'];
                $total_pagar = $fatia['total_pagar'];
                $valor_pago = $fatia['valor_pago'];
                $total_vencidas = $fatia['total_vencidas'];
                $total_avencer = $fatia['total_avencer'];
                $codigo_sub_conta = substr($cod_conta, 0, 3);
                $codigo_conta_sintetica = substr($cod_conta, 0, 1);

                $total_conta_sintetica = $total_conta_sintetica + $total_pagar;
                $total_pago_conta_sintetica = $total_pago_conta_sintetica + $valor_pago;

                for ($i = 0; $i < $qtd_contas_sintetica; $i++) {
                    if ($arry_conta_sintetica[$i]==$codigo_conta_sintetica) {
                        $j=$i;
                        $j++;
                        // valor da parcela
                        $j++;
                        $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $total_pagar;

                        // valor pago
                        $j++;
                        $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $valor_pago;

                        // valor vencido
                        $j++;
                        $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $total_vencidas;

                        // valor avencer
                        $j++;
                        $arry_conta_sintetica[$j]=$arry_conta_sintetica[$j] + $total_avencer;
                    }
                }

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
    }

$linha++;

$celulas = 'A'.$linha.':H'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('BFBFBF');
$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$celulas = 'E'.$linha.':H'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		

$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'TOTAL GERAL');
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_avencer_conta_sintetica);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_vencida_conta_sintetica);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_pago_conta_sintetica);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_conta_sintetica);

if ($total_sem_conta != 0) {
    $linha++;

    $celulas = 'A'.$linha.':H'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('F0E68C');
    $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    $celulas = 'E'.$linha.':H'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'RATEIO SEM CONTA DEFINIDA');
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_avencer_sem_conta);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_vencido_sem_conta);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_pago_sem_conta);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_sem_conta);
}

$index_conta_sintetica=0;

for ($i = 0; $i < $qtd_contas_sintetica; $i++) {
    $cod_conta_sintetica = $arry_conta_sintetica[$index_conta_sintetica];
    $index_conta_sintetica++;
    $descricao_conta_sintetica = $arry_conta_sintetica[$index_conta_sintetica];
    $index_conta_sintetica++;
    $valor_conta_sintetica = $arry_conta_sintetica[$index_conta_sintetica];
    $index_conta_sintetica++;
    $valor_pago_conta_sintetica = $arry_conta_sintetica[$index_conta_sintetica];
    $index_conta_sintetica++;
    $valor_vencido_conta_sintetica = $arry_conta_sintetica[$index_conta_sintetica];
    $index_conta_sintetica++;
    $valor_avencer_conta_sintetica = $arry_conta_sintetica[$index_conta_sintetica];
    $index_conta_sintetica++;
    $i = $i + 6;     

    if ($valor_conta_sintetica!=0) {

		$linha++;

		$celulas = 'A'.$linha.':H'.$linha;
		$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
		$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

		$celulas = 'B'.$linha.':H'.$linha;
		$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		

		$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $cod_conta_sintetica.' - '.$descricao_conta_sintetica);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta_sintetica);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta_sintetica);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta_sintetica);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta_sintetica);

        $index_sub_conta = 0;

        for ($y = 0; $y < $qtd_sub_contas; $y++) {
         
            $index_sub_conta++;

            if ($index_sub_conta>6){
                if ($valor_sub_conta!=0 && substr($cod_sub_conta, 0,1)==$cod_conta_sintetica){
                    $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;
					$linha++;
		            $celulas = 'A'.$linha.':H'.$linha;
					$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
					$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
					$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

					$pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

					$celulas = 'B'.$linha.':H'.$linha;
					$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		
					$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_sub_conta);
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_sub_conta);
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_sub_conta);
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_sub_conta);

                    $index_conta=0;

                    for ($j = 0; $j < $qtd_contas; $j++) {

                        $index_conta++;

                        if ($index_conta>6){
                            if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                                if ($valor_conta!=0 && substr($conta_inicio, 0,1)==$cod_conta_sintetica){
                                    $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

									$linha++;
									$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

									$celulas = 'B'.$linha.':H'.$linha;
									$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		
									$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
									$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
									$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta);
									$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta);
									$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta);
									$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta);

									if ($tipo_rel=="A"){
                                    	$array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wfazendas,$wfornecedor,$wcc,$fazendas,$centro_custo);

	                                    for ($k=0; $k < count($array_contas); $k++) { 
	                                        if ($k==0){

												$linha++;

												$celulas = 'A'.$linha.':K'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

												$spreadsheet->setActiveSheetIndex(0)
											    	->setCellValue("A".$linha,"Documento")
											    	->setCellValue("B".$linha,"Fazenda")
											    	->setCellValue("C".$linha,"Fonte Pagadora")
											    	->setCellValue("D".$linha,"Emissão")
											    	->setCellValue("E".$linha,"Vencimento")
											    	->setCellValue("F".$linha,"Valor")
											    	->setCellValue("G".$linha,"Pagamento")
											    	->setCellValue("H".$linha,"Vlr Pagto")
											    	->setCellValue("I".$linha,"Situação")
											    	->setCellValue("J".$linha,"Forma Pgto")
											    	->setCellValue("K".$linha,"Cheque");

												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

												$celulas = 'D'.$linha.':k'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

												$linha++;
												$doc_imp = $numero_id . '/' . $parcela;

												$celulas = 'A'.$linha.':K'.$linha;

												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

	                                            $celulas = 'F'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

	                                            $celulas = 'H'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

												$celulas = 'A'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'D'.$linha.':E'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'F'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'G'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'H'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'I'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($array_contas[$k][1]));

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][2]);												
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_emissao_edi);

$data_vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][3]);												

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_vencimento_edi);

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_contas[$k][4]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $array_contas[$k][5]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $array_contas[$k][6]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $array_contas[$k][7]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $array_contas[$k][8]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $array_contas[$k][9]);
	                                        }
	                                        else {
												$linha++;
												$doc_imp = $numero_id . '/' . $parcela;

												$celulas = 'A'.$linha.':K'.$linha;

												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

												$celulas = 'A'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'D'.$linha.':E'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'F'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'G'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'H'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'I'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                                            $celulas = 'F'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

	                                            $celulas = 'H'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($array_contas[$k][1]));
$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][2]);												
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_emissao_edi);

$data_vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][3]);												

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_vencimento_edi);

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_contas[$k][4]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $array_contas[$k][5]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $array_contas[$k][6]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $array_contas[$k][7]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $array_contas[$k][8]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $array_contas[$k][9]);
	                                        }
	                                    }

	                                    $linha++;

	                                    $celulas = 'A'.$linha.':D'.$linha;

	                                    $spreadsheet->getActiveSheet()->mergeCells($celulas);

	                                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                                    $celulas = 'E'.$linha.':H'.$linha;

	                                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                                    $spreadsheet->setActiveSheetIndex(0)
	                                        ->setCellValue("A".$linha,"Conta")
	                                        ->setCellValue("E".$linha,"A vencer")
	                                        ->setCellValue("F".$linha,"Vencidas")
	                                        ->setCellValue("G".$linha,"Pago")
	                                        ->setCellValue("H".$linha,"Total");
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
				$cod_sub_conta = $arry_sub_conta[$y];
			}
			else if ($index_sub_conta==2){
				$descricao_sub_conta = $arry_sub_conta[$y];
			}
			else if ($index_sub_conta==3){
				$valor_sub_conta = $arry_sub_conta[$y];
			}
			else if ($index_sub_conta==4){
				$valor_pago_sub_conta = $arry_sub_conta[$y];
			}
			else if ($index_sub_conta==5){
				$valor_vencido_sub_conta = $arry_sub_conta[$y];
			}
			else if ($index_sub_conta==6){
				$valor_avencer_sub_conta = $arry_sub_conta[$y];
			}
		}

		if ($valor_sub_conta!=0 && substr($cod_sub_conta, 0,1)==$cod_conta_sintetica){
			$linha++;
			$celulas = 'A'.$linha.':H'.$linha;
			$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
			$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
			$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

			$celulas = 'B'.$linha.':H'.$linha;
			$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		

			$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_sub_conta);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_sub_conta);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_sub_conta);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_sub_conta);

			$index_conta=0;

			for ($j = 0; $j < $qtd_contas; $j++) {

			    $index_conta++;

				if ($index_conta>6){
				   	if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
				   		if ($valor_conta!=0 && substr($conta_inicio, 0,1)==$cod_conta_sintetica){
							$linha++;
							$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

/*							$celulas = 'A'.$linha.':H'.$linha;
		        			$spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray(array('font' => array('color' => array('rgb' => '566573'))));*/ 

		/*$celulas = 'A'.$linha.':H'.$linha;
		$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('F7F9FA');*/

							$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

							$celulas = 'B'.$linha.':H'.$linha;
							$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		
							$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
							$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
							$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta);
							$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta);
							$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta);
							$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta);

							if ($tipo_rel=="A"){
                                $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wfazendas,$wfornecedor,$wcc,$fazendas,$centro_custo);
	                            for ($k=0; $k < count($array_contas); $k++) { 
	                                if ($k==0){

												$linha++;

												$celulas = 'A'.$linha.':K'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

												$spreadsheet->setActiveSheetIndex(0)
											    	->setCellValue("A".$linha,"Documento")
											    	->setCellValue("B".$linha,"Fazenda")
											    	->setCellValue("C".$linha,"Fonte Pagadora")
											    	->setCellValue("D".$linha,"Emissão")
											    	->setCellValue("E".$linha,"Vencimento")
											    	->setCellValue("F".$linha,"Valor")
											    	->setCellValue("G".$linha,"Pagamento")
											    	->setCellValue("H".$linha,"Vlr Pagto")
											    	->setCellValue("I".$linha,"Situação")
											    	->setCellValue("J".$linha,"Forma Pgto")
											    	->setCellValue("K".$linha,"Cheque");

												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

												$celulas = 'D'.$linha.':K'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

												$linha++;
												$doc_imp = $numero_id . '/' . $parcela;

												$celulas = 'A'.$linha.':K'.$linha;

												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

	                                            $celulas = 'F'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

	                                            $celulas = 'H'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

												$celulas = 'A'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'D'.$linha.':E'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'F'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'G'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'H'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'I'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($array_contas[$k][1]));

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][2]);												
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_emissao_edi);

$data_vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][3]);												

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_vencimento_edi);

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_contas[$k][4]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $array_contas[$k][5]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $array_contas[$k][6]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $array_contas[$k][7]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $array_contas[$k][8]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $array_contas[$k][9]);
	                                }
	                                else {
												$linha++;
												$doc_imp = $numero_id . '/' . $parcela;

												$celulas = 'A'.$linha.':K'.$linha;

												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
												$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

												$celulas = 'A'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'D'.$linha.':E'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'F'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'G'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
												$celulas = 'H'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
												$celulas = 'I'.$linha;
												$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                                            $celulas = 'F'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

	                                            $celulas = 'H'.$linha;
	                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($array_contas[$k][1]));

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][2]);												
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_emissao_edi);

$data_vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][3]);												

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_vencimento_edi);

												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_contas[$k][4]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $array_contas[$k][5]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $array_contas[$k][6]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $array_contas[$k][7]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $array_contas[$k][8]);
												$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $array_contas[$k][9]);
	                                }
	                            }

	                            $linha++;

	                            $celulas = 'A'.$linha.':D'.$linha;

	                            $spreadsheet->getActiveSheet()->mergeCells($celulas);

	                            $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                            $celulas = 'E'.$linha.':H'.$linha;

	                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                            $spreadsheet->setActiveSheetIndex(0)
	                                ->setCellValue("A".$linha,"Conta")
	                                ->setCellValue("E".$linha,"A vencer")
	                                ->setCellValue("F".$linha,"Vencidas")
	                                ->setCellValue("G".$linha,"Pago")
	                                ->setCellValue("H".$linha,"Total");
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

			if ($valor_conta!=0 && substr($conta_inicio, 0,1)==$cod_conta_sintetica){
				$linha++;
				$pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

				$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

				$celulas = 'B'.$linha.':H'.$linha;
				$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		

				$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta);
		    	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta);
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta);

				if ($tipo_rel=="A"){
                    $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wfazendas,$wfornecedor,$wcc,$fazendas,$centro_custo);

	                    for ($k=0; $k < count($array_contas); $k++) { 
	                        if ($k==0){
								$linha++;

								$celulas = 'A'.$linha.':K'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

								$spreadsheet->setActiveSheetIndex(0)
							    	->setCellValue("A".$linha,"Documento")
							    	->setCellValue("B".$linha,"Fazenda")
							    	->setCellValue("C".$linha,"Fonte Pagadora")
							    	->setCellValue("D".$linha,"Emissão")
							    	->setCellValue("E".$linha,"Vencimento")
							    	->setCellValue("F".$linha,"Valor")
							    	->setCellValue("G".$linha,"Pagamento")
							    	->setCellValue("H".$linha,"Vlr Pagto")
							    	->setCellValue("I".$linha,"Situação")
							    	->setCellValue("J".$linha,"Forma Pgto")
							    	->setCellValue("K".$linha,"Cheque");

								$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

								$celulas = 'D'.$linha.':K'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

								$linha++;
								$doc_imp = $numero_id . '/' . $parcela;

								$celulas = 'A'.$linha.':K'.$linha;

								$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
								$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

	                            $celulas = 'F'.$linha;
	                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

	                            $celulas = 'H'.$linha;
	                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

								$celulas = 'A'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
								$celulas = 'D'.$linha.':E'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
								$celulas = 'F'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
								$celulas = 'G'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
								$celulas = 'H'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
								$celulas = 'I'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($array_contas[$k][1]));

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][2]);												
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_emissao_edi);

$data_vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][3]);												

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_vencimento_edi);

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_contas[$k][4]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $array_contas[$k][5]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $array_contas[$k][6]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $array_contas[$k][7]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $array_contas[$k][8]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $array_contas[$k][9]);
	                        }
	                        else {
								$linha++;
								$doc_imp = $numero_id . '/' . $parcela;

								$celulas = 'A'.$linha.':K'.$linha;

								$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
								$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

								$celulas = 'A'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
								$celulas = 'D'.$linha.':E'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
								$celulas = 'F'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
								$celulas = 'G'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
								$celulas = 'H'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
								$celulas = 'I'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	                            $celulas = 'F'.$linha;
	                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

	                            $celulas = 'H'.$linha;
	                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($array_contas[$k][1]));

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$data_emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][2]);												
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_emissao_edi);

$data_vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($array_contas[$k][3]);												

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_vencimento_edi);

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $array_contas[$k][4]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $array_contas[$k][5]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $array_contas[$k][6]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $array_contas[$k][7]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $array_contas[$k][8]);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $array_contas[$k][9]);
	                        }
	                    }
				}
			}
		}
    } //fim if valor_conta_sintetica
} // fim for qtd_contas_sintetica

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="analise_contas_pagar.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');


mysqli_close($conector);
exit;

function montar_fatias_conta_rateio($conector, $ctp_id, $cod_conta_header, $total_pagar, $valor_pago, $total_vencidas, $total_avencer) {
    if ($cod_conta_header !== null && $cod_conta_header !== '') {
        return [[
            'cod_conta' => $cod_conta_header,
            'total_pagar' => $total_pagar,
            'valor_pago' => $valor_pago,
            'total_vencidas' => $total_vencidas,
            'total_avencer' => $total_avencer,
        ]];
    }

    $linhas_rateio = array();
    $soma_rateio = 0;

    $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctp_rateio
        WHERE rc_ctp_id='$ctp_id' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");

    while ($r = mysqli_fetch_object($rs)) {
        $linhas_rateio[] = $r;
        $soma_rateio += $r->rc_valor_conta;
    }

    if (count($linhas_rateio) == 0 || $soma_rateio == 0) {
        return array();
    }

    $fatias = array();

    foreach ($linhas_rateio as $r) {
        $prop = $r->rc_valor_conta / $soma_rateio;
        $fatias[] = [
            'cod_conta' => $r->rc_codigo_conta,
            'total_pagar' => $total_pagar * $prop,
            'valor_pago' => $valor_pago * $prop,
            'total_vencidas' => $total_vencidas * $prop,
            'total_avencer' => $total_avencer * $prop,
        ];
    }

    return $fatias;
}

function ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wfazendas,$wfornecedor,$wcc,$fazendas_ids='',$cc_ids=''){

        $wconta_notas = " AND (ctp_codigo_conta='$conta_inicio' OR (ctp_codigo_conta IS NULL AND ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_codigo_conta='$conta_inicio')))";

        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
            INNER JOIN contas_pagar
                    ON bcp_id=ctp_id
            WHERE bcp_data_pagamento >='$data_inicial' AND
                  bcp_data_pagamento <='$data_final'" . $wconta_notas . $wfazendas . $wcc . $wfornecedor .
            " ORDER BY ctp_codigo_conta, bcp_data_pagamento, bcp_numero_id  ASC");

        $ind_array = 0;
        $array_contas = array();

        $ctp_chave_anterior = 0;

        while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){  
            $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
            $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
            $valor_juros = $registro_contas_pag->ctp_valor_juros;
            $valor_outro = $registro_contas_pag->ctp_outro_valor;
            $emissao = $registro_contas_pag->ctp_data_emissao;
            $emissao_edi = new DateTime($registro_contas_pag->ctp_data_emissao);
            $vencimento = $registro_contas_pag->ctp_data_vencimento;
            $vencimento_edi = new DateTime($registro_contas_pag->ctp_data_vencimento);
            $situacao = $registro_contas_pag->ctp_situacao;
            $numero_id = $registro_contas_pag->ctp_numero_doc;
            $ctp_id = $registro_contas_pag->ctp_id;
            $parcela = $registro_contas_pag->ctp_parcela;
            $ctp_chave = $ctp_id.$parcela;
            $codigo_fornecedor = $registro_contas_pag->ctp_codigo_fornecedor;
            $codigo_fazenda = $registro_contas_pag->ctp_codigo_fazenda;
            $razao = substr($registro_contas_pag->ctp_nome_fornecedor, 0,38);
            $numero_cheque = $registro_contas_pag->ctp_numero_cheque;
            $conta_pgto = $registro_contas_pag->ctp_conta_pagamento;
            $data_pagamento=0;
            $desc_situacao="";
            $valor_pago=0;

            $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
            $data_pag_edi = new DateTime($registro_contas_pag->bcp_data_pagamento);
            $data_pag_edi = $data_pag_edi->format('d/m/Y');
            $data_pag_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_pag_edi);
            $data_pagamento = $registro_contas_pag->bcp_data_pagamento;

            if ($conta_pgto!=0){
                $conta_pagamento = mysqli_query($conector, "SELECT tbl_conta_pagamento_descricao
                FROM tbl_conta_pagamento 
                WHERE tbl_conta_pagamento_id='$conta_pgto'");
                                                                                         
                $registro_conta_pagamento = mysqli_fetch_object($conta_pagamento);
                $desc_conta_pgto = utf8_encode($registro_conta_pagamento->tbl_conta_pagamento_descricao);
            }
            else {
                $desc_conta_pgto = '';  
            }

            /*$data_pag_edi='';

            if ($situacao == "P" || $situacao == "C"){
                $conta_baixada = mysqli_query($conector, "SELECT bcp_valor_pagamento,bcp_data_pagamento
                FROM baixa_contas_pagar 
                WHERE bcp_id='$ctp_id'");
                                                                                         
                while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                    $ctp_valor_pago = $registro_conta_baixada->bcp_valor_pagamento;
                    $valor_pago = $valor_pago + $ctp_valor_pago;
                    $data_pag_edi = new DateTime($registro_conta_baixada->bcp_data_pagamento);
                    $data_pag_edi = $data_pag_edi->format('d/m/Y');
                    $data_pagamento = $registro_conta_baixada->bcp_data_pagamento;
                }
            }
            else if ($tipo_data=="P"){
                $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
            }*/

            //if ($ctp_chave_anterior!=$ctp_chave) {
                $total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                //$ctp_chave_anterior=$ctp_chave;
            //}
            //else {
                //$total_pagar = 0;
            //}

            if ($vencimento < $data_sistema) {
                $desc_situacao = " Vencido";
            } else {
                $desc_situacao = "";
            }
            
            if ($situacao == "P") {
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

            if ($numero_id=='') {
                $numero_id='000';
            }

            $doc_imp = $numero_id . '/' . $parcela;

            if ($codigo_fazenda === null) {
                // Documento rateado: uma linha por local que participa desta conta contábil
                $wlocal_rateio = ($fazendas_ids!='') ? " AND rc_codigo_local IN($fazendas_ids)" : '';
                $wcc_rateio = ($cc_ids!='') ? " AND (rc_codigo_cc IS NULL OR rc_codigo_cc IN($cc_ids))" : '';

                $rateio_res = mysqli_query($conector, "SELECT rc_nome_local, rc_valor_conta
                    FROM tbl_ctp_rateio
                    WHERE rc_ctp_id='$ctp_id' AND rc_codigo_conta='$conta_inicio'" . $wlocal_rateio . $wcc_rateio);

                while ($reg_rateio = mysqli_fetch_object($rateio_res)) {
                    $desc_pessoa_fatia = utf8_encode($reg_rateio->rc_nome_local);
                    $valor_fatia = $reg_rateio->rc_valor_conta;
                    $valor_pago_fatia = ($total_pagar != 0) ? $valor_pago * ($valor_fatia / $total_pagar) : 0;

                    $dados = [$doc_imp,$razao,$emissao_edi->format('d/m/Y'), $vencimento_edi->format('d/m/Y'),$valor_fatia, $data_pag_edi, $valor_pago_fatia, $desc_situacao, $desc_conta_pgto, $numero_cheque, $desc_pessoa_fatia];

                    $array_contas[$ind_array] = $dados;
                    $ind_array++;
                }
            }
            else {
                $tbl_pessoa = mysqli_query($conector, "SELECT tbl_pessoa_nome
                FROM tbl_pessoa
                WHERE tbl_pessoa_id='$codigo_fazenda'");

                $registro_pessoa = mysqli_fetch_object($tbl_pessoa);
                $desc_pessoa = utf8_encode($registro_pessoa->tbl_pessoa_nome);

                $dados = [$doc_imp,$razao,$emissao_edi->format('d/m/Y'), $vencimento_edi->format('d/m/Y'),$total_pagar, $data_pag_edi, $valor_pago, $desc_situacao, $desc_conta_pgto, $numero_cheque, $desc_pessoa];

                $array_contas[$ind_array] = $dados;
                $ind_array++;
            }
        }
        return $array_contas;
    }

