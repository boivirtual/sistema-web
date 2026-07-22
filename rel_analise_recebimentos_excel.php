<?php

// Filtro (conta/local/CC) que também alcança as demais parcelas de um mesmo
// documento rateado (mesmo ctr_numero_doc + cliente/fornecedor). O rateio é salvo
// uma única vez, na 1ª parcela — sem isso, o filtro só encontrava essa 1ª parcela.
function condicao_rateio_ou_grupo_ctr($coluna_ctr, $coluna_rateio, $ids_str) {
    return "($coluna_ctr IS NULL AND ctr_id IN (
        SELECT DISTINCT ctr2.ctr_id
        FROM contas_receber ctr1
        INNER JOIN contas_receber ctr2 ON (
            ctr2.ctr_codigo_fazenda IS NULL
            AND ctr2.ctr_numero_doc = ctr1.ctr_numero_doc
            AND ctr2.ctr_codigo_cliente_fornecedor = ctr1.ctr_codigo_cliente_fornecedor
            AND ctr1.ctr_numero_doc IS NOT NULL AND ctr1.ctr_numero_doc != ''
        )
        WHERE ctr1.ctr_id IN (SELECT rc_ctr_id FROM tbl_ctr_rateio WHERE $coluna_rateio IN ($ids_str))
    ))";
}

// Resolve o ctr_id onde o rateio de fato foi salvo: em parcelamento (mesmo
// ctr_numero_doc + cliente/fornecedor), o rateio fica gravado só na 1ª parcela.
function resolver_primeiro_ctr_rateio($conector, $ctr_id, $ctr_numero_doc = null, $ctr_codigo_cliente = null) {
    if ($ctr_numero_doc === null || $ctr_numero_doc === '' || $ctr_codigo_cliente === null) return $ctr_id;
    $nd_esc  = mysqli_real_escape_string($conector, $ctr_numero_doc);
    $cli_esc = intval($ctr_codigo_cliente);
    $rs = mysqli_query($conector, "SELECT MIN(ctr_id) AS primeiro_id FROM contas_receber WHERE ctr_numero_doc = '$nd_esc' AND ctr_codigo_cliente_fornecedor = '$cli_esc' AND ctr_codigo_fazenda IS NULL");
    $row = $rs ? mysqli_fetch_object($rs) : null;
    return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctr_id;
}

// 		Come�a Excel
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
  	  print_r("Falha na conex�o: ", mysqli_connect_error());
      exit;
  }

  $bancoselecionado = mysqli_select_db($conector,$banco);

  if ($bancoselecionado === FALSE) {
  	  print_r("Falha na sele��o do banco de dados: " . mysqli_error($conector));
      exit;
  }
  
$data_sistema = date("Y-m-d");
$data_hoje = date("d/m/Y");

@ session_start(); 

$codigo_cliente = $_REQUEST["cliente"];
$codigo_conta = $_REQUEST["conta"];
$codigo_cc = $_REQUEST["c_custo"];
$codigo_fazenda = $_REQUEST["fazendas"];
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
    $wconta = " AND (ctr_codigo_conta IN($conta) OR " . condicao_rateio_ou_grupo_ctr('ctr_codigo_conta', 'rc_codigo_conta', $conta) . ")";
}

$cliente= array();
$matriz_itens = explode(",", $codigo_cliente);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $cliente[$i]=$matriz_itens[$i];
}

$cliente = implode(',', $cliente);
$cliente = substr($cliente,0, -1);

$wcliente = '';

if ($codigo_cliente!='') {
    $wcliente = " AND ctr_codigo_cliente_fornecedor IN(";
    $wcliente.= $cliente;
    $wcliente.= ")";
}

$cc= array();
$matriz_itens = explode(",", $codigo_cc);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $cc[$i]=$matriz_itens[$i];
}

$cc = implode(',', $cc);
$cc = substr($cc,0, -1);

$wcc = '';

if ($codigo_cc!='') {
    $wcc = " AND (ctr_codigo_c_custo IN($cc) OR " . condicao_rateio_ou_grupo_ctr('ctr_codigo_c_custo', 'rc_codigo_cc', $cc) . ")";
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
    $wfazendas = " AND (ctr_codigo_fazenda IN($fazendas) OR (ctr_codigo_fazenda IS NULL AND ctr_id IN (SELECT rc_ctr_id FROM tbl_ctr_rateio WHERE rc_codigo_local IN ($fazendas))))";
}

//$conta_inicio=$codigo_conta;    
$a_vencer='';
$vencidos='';
$pagos='';

$conta_inicio = substr($codigo_conta, 0, 7);

if ($conta_inicio==0 || substr($conta_inicio, 1, 6) == 0){
    if ($conta_inicio==0) {
        $conta_inicio= 1000000;
        $conta_fim= 1999999;
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
    $conta_fim=1999999;
    $conta_inicio=substr($conta_fim, 0, 3) . '0000';
}

/*if ($conta_inicio==0 || $conta_inicio==1000000){
    $conta_inicio=1000000;
    $conta_fim=1999999;
}
else if (substr($conta_inicio, 3, 4)==0){
    $conta_fim=substr($conta_inicio, 0, 3) . 9999;
}
else {
    $conta_fim=$conta_inicio;
    $conta_inicio=substr($conta_fim, 0, 3) . '0000';
}*/

$nome_relatorio = "Análise de Recebimentos";

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
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(13);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('E4:H4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$linha=4;

    // monta array das contas

    $plano_contas = "SELECT * FROM tbl_plano_contas 
        WHERE tbl_plano_contas_codigo_id >=1000000 AND 
              tbl_plano_contas_codigo_id <=1999999
        ORDER BY tbl_plano_contas_codigo_id ASC"; 

    $plano_contas = mysqli_query($conector, $plano_contas);

    /*$plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
        WHERE tbl_plano_contas_codigo_id >='$conta_inicio' AND 
              tbl_plano_contas_codigo_id <='$conta_fim'
        ORDER BY tbl_plano_contas_codigo_id ASC"); 
    */

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

    while ($reg_conta = mysqli_fetch_object($plano_contas)){  
        $cod_conta = $reg_conta->tbl_plano_contas_codigo_id;
        $descricao_conta = $reg_conta->tbl_plano_contas_descricao;                                   
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

    if ($tipo_data=="E"){
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
            WHERE ctr_data_emissao >='$data_inicial' and
                  ctr_data_emissao <='$data_final' and
                  ctr_lixeira=0" . $wfazendas . $wcc . $wcliente . $wconta .
            " ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC");
    }
    else if ($tipo_data=="V"){
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
            WHERE ctr_data_vencimento >='$data_inicial' and
                  ctr_data_vencimento <='$data_final' and
                  ctr_lixeira=0" . $wfazendas . $wcc . $wcliente . $wconta . 
            " ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC");
    }
    else {
        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
            INNER JOIN contas_receber
                    ON bcr_id=ctr_id
            WHERE bcr_data_pagamento >='$data_inicial' and
                  bcr_data_pagamento <='$data_final' and
                  ctr_lixeira=0" . $wfazendas . $wcc . $wcliente . $wconta . 
            " ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
    }

    $num_rows_contas = mysqli_num_rows($contas_rec);
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

                // Documento com rateio (cod_conta null): reparte pelas contas do rateio.
                // Sem rateio: retorna a própria conta/valores, sem alterar nada.
                $fatias_ctr = montar_fatias_conta_rateio_ctr($conector, $ctr_id, $cod_conta, $total_pagar, $valor_pago, $total_vencidas, $total_avencer);

                foreach ($fatias_ctr as $fatia_ctr) {
                    $cod_conta_fatia = $fatia_ctr['cod_conta'];
                    $codigo_sub_conta_fatia = substr($cod_conta_fatia, 0, 3);
                    $total_pagar_fatia = $fatia_ctr['total_pagar'];
                    $valor_pago_fatia = $fatia_ctr['valor_pago'];
                    $total_vencidas_fatia = $fatia_ctr['total_vencidas'];
                    $total_avencer_fatia = $fatia_ctr['total_avencer'];

                    for ($i = 0; $i < $qtd_contas; $i++) {
                        if ($arry_conta[$i]==$cod_conta_fatia) {
                            $j=$i;
                            $j++;

                            // valor da parcela
                            $j++;
                            $arry_conta[$j]=$arry_conta[$j] + $total_pagar_fatia;

                            // valor pago
                            $j++;
                            $arry_conta[$j]=$arry_conta[$j] + $valor_pago_fatia;

                            // valor vencido
                            $j++;
                            $arry_conta[$j]=$arry_conta[$j] + $total_vencidas_fatia;

                            // valor avencer
                            $j++;
                            $arry_conta[$j]=$arry_conta[$j] + $total_avencer_fatia;
                        }
                    }

                    for ($i = 0; $i < $qtd_sub_contas; $i++) {
                        if ($arry_sub_conta[$i]==$codigo_sub_conta_fatia) {
                            $j=$i;
                            $j++;

                            // valor da parcela
                            $j++;
                            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_pagar_fatia;

                            // valor pago
                            $j++;
                            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $valor_pago_fatia;

                            // valor vencido
                            $j++;
                            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_vencidas_fatia;

                            // valor avencer
                            $j++;
                            $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_avencer_fatia;
                        }
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

            // Documento com rateio (cod_conta null): reparte pelas contas do rateio.
            // Sem rateio: retorna a própria conta/valores, sem alterar nada.
            $fatias_ctr = montar_fatias_conta_rateio_ctr($conector, $ctr_id, $cod_conta, $total_pagar, $valor_pago, $total_vencidas, $total_avencer);

            foreach ($fatias_ctr as $fatia_ctr) {
                $cod_conta_fatia = $fatia_ctr['cod_conta'];
                $codigo_sub_conta_fatia = substr($cod_conta_fatia, 0, 3);
                $total_pagar_fatia = $fatia_ctr['total_pagar'];
                $valor_pago_fatia = $fatia_ctr['valor_pago'];
                $total_vencidas_fatia = $fatia_ctr['total_vencidas'];
                $total_avencer_fatia = $fatia_ctr['total_avencer'];

                for ($i = 0; $i < $qtd_contas; $i++) {
                    if ($arry_conta[$i]==$cod_conta_fatia) {
                        $j=$i;
                        $j++;

                        // valor da parcela
                        $j++;
                        $arry_conta[$j]=$arry_conta[$j] + $total_pagar_fatia;

                        // valor pago
                        $j++;
                        $arry_conta[$j]=$arry_conta[$j] + $valor_pago_fatia;

                        // valor vencido
                        $j++;
                        $arry_conta[$j]=$arry_conta[$j] + $total_vencidas_fatia;

                        // valor avencer
                        $j++;
                        $arry_conta[$j]=$arry_conta[$j] + $total_avencer_fatia;
                    }
                }

                for ($i = 0; $i < $qtd_sub_contas; $i++) {
                    if ($arry_sub_conta[$i]==$codigo_sub_conta_fatia) {
                        $j=$i;
                        $j++;

                        // valor da parcela
                        $j++;
                        $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_pagar_fatia;

                        // valor pago
                        $j++;
                        $arry_sub_conta[$j]=$arry_sub_conta[$j] + $valor_pago_fatia;

                        // valor vencido
                        $j++;
                        $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_vencidas_fatia;

                        // valor avencer
                        $j++;
                        $arry_sub_conta[$j]=$arry_sub_conta[$j] + $total_avencer_fatia;
                    }
                }
            }
        }
    }

    $conta_sintetica = substr($conta_inicio, 0,1);
    $conta_sintetica = str_pad($conta_sintetica, 7, "0", STR_PAD_RIGHT);
    $plano_contas = mysqli_query($conector, "SELECT tbl_plano_contas_descricao FROM tbl_plano_contas
        WHERE tbl_plano_contas_codigo_id ='$conta_sintetica'"); 
        
    $registro_plano_contas = mysqli_fetch_object($plano_contas);
    $descricao_conta = $registro_plano_contas->tbl_plano_contas_descricao;
    
	$linha++;
	$celulas = 'A'.$linha.':H'.$linha;
	$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
	$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
	$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

	$celulas = 'E'.$linha.':H'.$linha;
	$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		

	$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, substr($conta_inicio, 0,1).' - '.$descricao_conta);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_avencer_conta_sintetica);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_vencida_conta_sintetica);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_pago_conta_sintetica);
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $total_conta_sintetica);

    $index_sub_conta = 0;

    for ($i = 0; $i < $qtd_sub_contas; $i++) {

        $index_sub_conta++;

        if ($index_sub_conta>6){
            if ($valor_sub_conta!=0){
                $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

				$linha++;
	            $celulas = 'A'.$linha.':H'.$linha;
				$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
				$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
				$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

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
                            if ($valor_conta!=0){
                                $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

								$linha++;
					            $celulas = 'A'.$linha.':H'.$linha;
								//$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
								//$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');

					            $celulas = 'A'.$linha.':D'.$linha;
								$spreadsheet->getActiveSheet()->mergeCells($celulas);

								$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

								$celulas = 'E'.$linha.':H'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta);

                                if ($tipo_rel=="A"){
                                    $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wcc,$wcliente,$wfazendas);

                                    for ($k=0; $k < count($array_contas); $k++) { 
                                        if ($k==0){

											$linha++;

											$celulas = 'A'.$linha.':K'.$linha;
											$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

											$spreadsheet->setActiveSheetIndex(0)
										    	->setCellValue("A".$linha,"Documento")
                                                ->setCellValue("B".$linha,"Local")
										    	->setCellValue("C".$linha,"Cliente")
										    	->setCellValue("D".$linha,"Emissão")
										    	->setCellValue("E".$linha,"Vencimento")
										    	->setCellValue("F".$linha,"Valor")
										    	->setCellValue("G".$linha,"Pagamento")
										    	->setCellValue("H".$linha,"Valor Pago")
										    	->setCellValue("I".$linha,"Situação")
                                                ->setCellValue("J".$linha,"Banco Pgto")
                                                ->setCellValue("K".$linha,"Cheque");

											$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

											$celulas = 'C'.$linha.':K'.$linha;
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

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

											$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
											$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_contas[$k][1]);
											$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_contas[$k][2]);
											$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_contas[$k][3]);
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

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_contas[$k][1]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_contas[$k][2]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_contas[$k][3]);
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
    } // fim do for

    if ($valor_sub_conta!=0){
        $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

			$linha++;
	        $celulas = 'A'.$linha.':H'.$linha;
			$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
			$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
			$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

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
                    if ($valor_conta!=0){
                            $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

								$linha++;
					            $celulas = 'A'.$linha.':H'.$linha;
								//$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
								//$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');

					            $celulas = 'A'.$linha.':D'.$linha;
								$spreadsheet->getActiveSheet()->mergeCells($celulas);

								$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

								$celulas = 'E'.$linha.':H'.$linha;
								$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		
								$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta);
								$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta);

                        if ($tipo_rel=="A"){
                            $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wcc,$wcliente,$wfazendas);

                            for ($k=0; $k < count($array_contas); $k++) { 
                                if ($k==0){
									$linha++;

                                            $celulas = 'A'.$linha.':K'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

                                            $spreadsheet->setActiveSheetIndex(0)
                                                ->setCellValue("A".$linha,"Documento")
                                                ->setCellValue("B".$linha,"Local")
                                                ->setCellValue("C".$linha,"Cliente")
                                                ->setCellValue("D".$linha,"Emissão")
                                                ->setCellValue("E".$linha,"Vencimento")
                                                ->setCellValue("F".$linha,"Valor")
                                                ->setCellValue("G".$linha,"Pagamento")
                                                ->setCellValue("H".$linha,"Valor Pago")
                                                ->setCellValue("I".$linha,"Situação")
                                                ->setCellValue("J".$linha,"Banco Pgto")
                                                ->setCellValue("K".$linha,"Cheque");

                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                            $celulas = 'C'.$linha.':K'.$linha;
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

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_contas[$k][1]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_contas[$k][2]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_contas[$k][3]);
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

                                            $celulas = 'A'.$linha.':J'.$linha;

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

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_contas[$k][1]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_contas[$k][2]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_contas[$k][3]);
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
                        else {
                            //echo '</tr>';
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
            $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

			$linha++;
			$celulas = 'A'.$linha.':H'.$linha;
			//$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
			//$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');

            $celulas = 'A'.$linha.':D'.$linha;
			$spreadsheet->getActiveSheet()->mergeCells($celulas);

			$spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$celulas = 'E'.$linha.':H'.$linha;
			$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);		
			$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $pla_descricao);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $valor_avencer_conta);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $valor_vencido_conta);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $valor_pago_conta);
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $valor_conta);

            if ($tipo_rel=="A"){
                $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wcc,$wcliente,$wfazendas);

                for ($k=0; $k < count($array_contas); $k++) { 
                    if ($k==0){
						$linha++;

                                            $celulas = 'A'.$linha.':K'.$linha;
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

                                            $spreadsheet->setActiveSheetIndex(0)
                                                ->setCellValue("A".$linha,"Documento")
                                                ->setCellValue("B".$linha,"Local")
                                                ->setCellValue("C".$linha,"Cliente")
                                                ->setCellValue("D".$linha,"Emissão")
                                                ->setCellValue("E".$linha,"Vencimento")
                                                ->setCellValue("F".$linha,"Valor")
                                                ->setCellValue("G".$linha,"Pagamento")
                                                ->setCellValue("H".$linha,"Valor Pago")
                                                ->setCellValue("I".$linha,"Situação")
                                                ->setCellValue("J".$linha,"Banco Pgto")
                                                ->setCellValue("K".$linha,"Cheque");

                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));

                                            $celulas = 'C'.$linha.':K'.$linha;
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

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_contas[$k][1]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_contas[$k][2]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_contas[$k][3]);
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

                                            $celulas = 'A'.$linha.':J'.$linha;

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

$celulas = $celulas = 'D'.$linha.':E'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
$celulas = $celulas = 'G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $array_contas[$k][0]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $array_contas[$k][10]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $array_contas[$k][1]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $array_contas[$k][2]);
                                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $array_contas[$k][3]);
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
        else {
            //echo '</tr>';
        } 
    }// fim do valor conta

// Retira o cabe�alho da �ltima linha
if ($tipo_rel=="A"){
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A".$linha,"")
        ->setCellValue("E".$linha,"")
        ->setCellValue("F".$linha,"")
        ->setCellValue("G".$linha,"")
        ->setCellValue("H".$linha,"");
}

// Rename worksheet
   
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client�s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="analise_contas_receber.xlsx"');
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

// Quando ctr_codigo_conta é NULL (documento com rateio), reparte os valores do
// documento entre as contas contábeis gravadas em tbl_ctr_rateio, proporcionalmente
// ao valor de cada conta no rateio. Se não houver rateio até o nível de conta
// (só até local/CC), retorna array vazio — o documento fica de fora do analítico
// por conta, igual ao comportamento equivalente em Contas a Pagar.
function montar_fatias_conta_rateio_ctr($conector, $ctr_id, $cod_conta_header, $total_pagar, $valor_pago, $total_vencidas, $total_avencer) {
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

    $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctr_rateio
        WHERE rc_ctr_id='$ctr_id' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");

    while ($r = mysqli_fetch_object($rs)) {
        $linhas_rateio[] = $r;
        $soma_rateio += $r->rc_valor_conta;
    }

    if (count($linhas_rateio) == 0 || $soma_rateio == 0) {
        // rateio feito só até local/CC, sem conta contábil definida
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

function ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$wcc,$wcliente,$wfazendas){

    $wconta_notas = " AND (ctr_codigo_conta='$conta_inicio' OR (ctr_codigo_conta IS NULL AND ctr_id IN (SELECT rc_ctr_id FROM tbl_ctr_rateio WHERE rc_codigo_conta='$conta_inicio')))";

    if ($tipo_data=="E"){
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
            WHERE ctr_data_emissao >='$data_inicial' and
                  ctr_data_emissao <='$data_final'" . $wconta_notas . " AND
                      ctr_lixeira=0" . $wfazendas . $wcc . $wcliente .
                " ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC");
    }
    else if ($tipo_data=="V"){
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
            WHERE ctr_data_vencimento >='$data_inicial' AND
                  ctr_data_vencimento <='$data_final'" . $wconta_notas . " AND
                  ctr_lixeira=0" . $wfazendas . $wcc . $wcliente .
            " ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC");
    }
    else {
        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
            INNER JOIN contas_receber
                    ON bcr_id=ctr_id
            WHERE bcr_data_pagamento >='$data_inicial' AND
                  bcr_data_pagamento <='$data_final'" . $wconta_notas . " AND
                  ctr_lixeira=0" . $wfazendas . $wcc . $wcliente .
            " ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC");
    }

    $num_rows_conta = mysqli_num_rows($contas_rec);
    $ind_array = 0;

    while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){  
        $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
        $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
        $valor_juros = $registro_contas_rec->ctr_valor_juros;
        $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
        $emissao = $registro_contas_rec->ctr_data_emissao;
        $emissao_edi = new DateTime($registro_contas_rec->ctr_data_emissao);
        $emissao_edi = $emissao_edi->format('d/m/Y');
        $emissao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($emissao_edi);

        $vencimento = $registro_contas_rec->ctr_data_vencimento;
        $vencimento_edi = new DateTime($registro_contas_rec->ctr_data_vencimento);
        $vencimento_edi = $vencimento_edi->format('d/m/Y');
        $vencimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($vencimento_edi);

        $situacao = $registro_contas_rec->ctr_situacao;
        $ctr_id = $registro_contas_rec->ctr_id;
        $numero_id = $registro_contas_rec->ctr_numero_doc;
        $codigo_cliente = $registro_contas_rec->ctr_codigo_cliente_fornecedor;
        $codigo_fazenda = $registro_contas_rec->ctr_codigo_fazenda;
        $parcela = $registro_contas_rec->ctr_parcela;
        $razao = substr($registro_contas_rec->ctr_nome_cliente, 0,38);
        $numero_cheque = $registro_contas_rec->ctr_numero_cheque;
        $conta_pgto = $registro_contas_rec->ctr_codigo_conta_recebimento;
        $data_pagamento=0;
        $desc_situacao="";
        $valor_pago=0;

        if ($conta_pgto!=0){
            $conta_pagamento = mysqli_query($conector, "SELECT tbl_conta_pagamento_descricao
            FROM tbl_conta_pagamento 
            WHERE tbl_conta_pagamento_id='$conta_pgto'");
                                                                                         
            $registro_conta_pagamento = mysqli_fetch_object($conta_pagamento);
            $desc_conta_pgto = $registro_conta_pagamento->tbl_conta_pagamento_descricao;
        }
        else {
            $desc_conta_pgto = '';  
        }

    	$data_pag_edi='';

        if ($situacao == "P" || $situacao == "C"){
            $conta_baixada = mysqli_query($conector, "SELECT bcr_valor_pagamento,bcr_data_pagamento FROM baixa_contas_receber 
            WHERE bcr_id='$ctr_id'");
                                                                                         
            while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                $ctr_valor_pago = $registro_conta_baixada->bcr_valor_pagamento;
                $valor_pago = $valor_pago + $ctr_valor_pago;
                $data_pag_edi = new DateTime($registro_conta_baixada->bcr_data_pagamento);
                $data_pag_edi = $data_pag_edi->format('d/m/Y');
                $data_pag_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_pag_edi);
                $data_pagamento = $registro_conta_baixada->bcr_data_pagamento;
            }
        }
        else if ($tipo_data=="P"){
            $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
        } 

        $total_pagar = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

        if ($situacao == '') {
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

        $doc_imp = $numero_id . '/' . $parcela;

        if ($codigo_fazenda === null) {
            // Documento rateado: uma linha por local que participa desta conta contábil
            $rateio_res = mysqli_query($conector, "SELECT rc_nome_local, rc_valor_conta
                FROM tbl_ctr_rateio
                WHERE rc_ctr_id='$ctr_id' AND rc_codigo_conta='$conta_inicio'");

            while ($reg_rateio = mysqli_fetch_object($rateio_res)) {
                $desc_pessoa = $reg_rateio->rc_nome_local;
                $valor_fatia = $reg_rateio->rc_valor_conta;
                $valor_pago_fatia = ($total_pagar != 0) ? $valor_pago * ($valor_fatia / $total_pagar) : 0;

                $dados = [$doc_imp,
                    $razao,
                    $emissao_edi,
                    $vencimento_edi,
                    $valor_fatia,
                    $data_pag_edi,
                    $valor_pago_fatia,
                    $desc_situacao,
                    $desc_conta_pgto,
                    $numero_cheque,
                    $desc_pessoa];

                $array_contas[$ind_array] = $dados;
                $ind_array++;
            }
        } else {
            $tbl_pessoa = mysqli_query($conector, "SELECT tbl_pessoa_nome
                FROM tbl_pessoa
                WHERE tbl_pessoa_id='$codigo_fazenda'");

            $registro_pessoa = mysqli_fetch_object($tbl_pessoa);
            $desc_pessoa = $registro_pessoa->tbl_pessoa_nome;

            $dados = [$doc_imp,
                $razao,
                $emissao_edi,
                $vencimento_edi,
                $total_pagar,
                $data_pag_edi,
                $valor_pago,
                $desc_situacao,
                $desc_conta_pgto,
                $numero_cheque,
                $desc_pessoa];

            $array_contas[$ind_array] = $dados;
            $ind_array++;
        }
    }
    return $array_contas;
}
