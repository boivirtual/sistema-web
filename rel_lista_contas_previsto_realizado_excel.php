<?php
// Filtro (conta/local/CC) que também alcança as demais parcelas de um mesmo
// documento — seja um grupo de repetição (ctp_grupo_repeticao) ou um parcelamento
// comum (mesmo ctp_numero_doc + fornecedor). O rateio é salvo uma única vez, na
// 1ª parcela/ocorrência — sem isso, o filtro só encontrava essa 1ª linha.
function condicao_rateio_ou_grupo($coluna_ctp, $coluna_rateio, $ids_str) {
    return "($coluna_ctp IS NULL AND ctp_id IN (
        SELECT DISTINCT cp2.ctp_id
        FROM contas_pagar cp1
        INNER JOIN contas_pagar cp2 ON (
            (cp1.ctp_grupo_repeticao IS NOT NULL AND cp2.ctp_grupo_repeticao = cp1.ctp_grupo_repeticao)
            OR (cp1.ctp_grupo_repeticao IS NULL AND cp1.ctp_numero_doc IS NOT NULL AND cp1.ctp_numero_doc != ''
                AND cp2.ctp_codigo_fazenda IS NULL
                AND cp2.ctp_numero_doc = cp1.ctp_numero_doc
                AND cp2.ctp_codigo_fornecedor = cp1.ctp_codigo_fornecedor)
            OR (cp1.ctp_grupo_repeticao IS NULL AND (cp1.ctp_numero_doc IS NULL OR cp1.ctp_numero_doc = '')
                AND cp2.ctp_codigo_fazenda IS NULL
                AND cp2.ctp_codigo_fornecedor = cp1.ctp_codigo_fornecedor
                AND cp2.ctp_incluido_em = cp1.ctp_incluido_em)
        )
        WHERE cp1.ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE $coluna_rateio IN ($ids_str))
    ))";
}

// Resolve o ctp_id onde o rateio de fato foi salvo: em repetição (ctp_grupo_repeticao
// preenchido), em parcelamento com documento (mesmo ctp_numero_doc + fornecedor), ou em
// parcelamento SEM número de documento (mesmo fornecedor + ctp_incluido_em idêntico — todas
// as parcelas de um lançamento são gravadas no mesmo instante). O rateio fica gravado só na
// 1ª ocorrência/parcela — nunca no ctp_id das demais.
function resolver_primeiro_ctp_rateio($conector, $ctp_id, $ctp_grupo_repeticao, $ctp_numero_doc = null, $ctp_codigo_fornecedor = null, $ctp_incluido_em = null) {
    if (!empty($ctp_grupo_repeticao)) {
        $gr_esc = mysqli_real_escape_string($conector, $ctp_grupo_repeticao);
        $rs = mysqli_query($conector, "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_grupo_repeticao = '$gr_esc'");
        $row = $rs ? mysqli_fetch_object($rs) : null;
        return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctp_id;
    }
    if ($ctp_numero_doc !== null && $ctp_numero_doc !== '' && $ctp_codigo_fornecedor !== null) {
        $nd_esc  = mysqli_real_escape_string($conector, $ctp_numero_doc);
        $for_esc = intval($ctp_codigo_fornecedor);
        $rs = mysqli_query($conector, "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_numero_doc = '$nd_esc' AND ctp_codigo_fornecedor = '$for_esc' AND ctp_codigo_fazenda IS NULL");
        $row = $rs ? mysqli_fetch_object($rs) : null;
        return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctp_id;
    }
    if ($ctp_codigo_fornecedor !== null && $ctp_incluido_em !== null && $ctp_incluido_em !== '') {
        $for_esc  = intval($ctp_codigo_fornecedor);
        $inc_esc  = mysqli_real_escape_string($conector, $ctp_incluido_em);
        $rs = mysqli_query($conector, "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_codigo_fornecedor = '$for_esc' AND ctp_incluido_em = '$inc_esc' AND ctp_codigo_fazenda IS NULL");
        $row = $rs ? mysqli_fetch_object($rs) : null;
        return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctp_id;
    }
    return $ctp_id;
}

// Reparte o valor de um lançamento de contas a pagar entre as contas contábeis do
// rateio (tbl_ctp_rateio), proporcionalmente ao valor de cada conta. Quando a conta
// já vem preenchida no próprio registro (sem rateio), retorna uma única fatia.
function montar_fatias_conta_rateio_ctp($conector, $ctp_id, $cod_conta_header, $valor, $ctp_grupo_repeticao = null, $ctp_numero_doc = null, $ctp_codigo_fornecedor = null, $ctp_incluido_em = null) {
    if ($cod_conta_header !== null && $cod_conta_header !== '') {
        return [['cod_conta' => $cod_conta_header, 'valor' => $valor]];
    }

    $ctp_id_rateio = resolver_primeiro_ctp_rateio($conector, $ctp_id, $ctp_grupo_repeticao, $ctp_numero_doc, $ctp_codigo_fornecedor, $ctp_incluido_em);

    $linhas_rateio = array();
    $soma_rateio = 0;

    $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctp_rateio
        WHERE rc_ctp_id='$ctp_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");

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
        $fatias[] = ['cod_conta' => $r->rc_codigo_conta, 'valor' => $valor * $prop];
    }

    return $fatias;
}

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

// Reparte o valor de um lançamento de contas a receber entre as contas contábeis do
// rateio (tbl_ctr_rateio), proporcionalmente ao valor de cada conta. Quando a conta
// já vem preenchida no próprio registro (sem rateio), retorna uma única fatia.
function montar_fatias_conta_rateio_ctr($conector, $ctr_id, $cod_conta_header, $valor, $ctr_numero_doc = null, $ctr_codigo_cliente = null) {
    if ($cod_conta_header !== null && $cod_conta_header !== '') {
        return [['cod_conta' => $cod_conta_header, 'valor' => $valor]];
    }

    $ctr_id_rateio = resolver_primeiro_ctr_rateio($conector, $ctr_id, $ctr_numero_doc, $ctr_codigo_cliente);

    $linhas_rateio = array();
    $soma_rateio = 0;

    $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctr_rateio
        WHERE rc_ctr_id='$ctr_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");

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
        $fatias[] = ['cod_conta' => $r->rc_codigo_conta, 'valor' => $valor * $prop];
    }

    return $fatias;
}

// Acumula o valor de uma fatia (já resolvida por conta) nos totais por conta
// sintética (nível 1), sub-conta (nível 2) e conta analítica (nível 3/folha),
// marcando quais contas têm valor para exibição no relatório.
function acumular_total_realizado(&$total_realizado, &$tem_valor, $cod_conta, $mes, $valor) {
    if ($valor == 0) {
        return;
    }

    $conta_nivel_1 = (int)str_pad(substr($cod_conta, 0, 1), 7, "0", STR_PAD_RIGHT);
    $conta_nivel_2 = (int)str_pad(substr($cod_conta, 0, 3), 7, "0", STR_PAD_RIGHT);

    $total_realizado[$conta_nivel_1][$mes] += $valor;
    $total_realizado[$conta_nivel_1][13] += $valor;
    $total_realizado[$conta_nivel_2][$mes] += $valor;
    $total_realizado[$conta_nivel_2][13] += $valor;
    $total_realizado[$cod_conta][$mes] += $valor;
    $total_realizado[$cod_conta][13] += $valor;

    $tem_valor[$conta_nivel_1] = "S";
    $tem_valor[$conta_nivel_2] = "S";
    $tem_valor[$cod_conta] = "S";
}

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

    $array_mes[1] = 'Janeiro';
    $array_mes[2] = 'Fevereiro';
    $array_mes[3] = 'Março';
    $array_mes[4] = 'Abril';
    $array_mes[5] = 'Maio';
    $array_mes[6] = 'Junho';
    $array_mes[7] = 'Julho';
    $array_mes[8] = 'Agosto';
    $array_mes[9] = 'Setembro';
    $array_mes[10] = 'Outubro';
    $array_mes[11] = 'Novembro';
    $array_mes[12] = 'Dezembro';

    $coluna[1]='B';
    $coluna[2]='D';
    $coluna[3]='F';
    $coluna[4]='H';
    $coluna[5]='J';
    $coluna[6]='L';
    $coluna[7]='N';
    $coluna[8]='P';
    $coluna[9]='R';
    $coluna[10]='T';
    $coluna[11]='V';
    $coluna[12]='X';
    $coluna[13]='Z';

    $coluna_nao[1]='C';
    $coluna_nao[2]='E';
    $coluna_nao[3]='G';
    $coluna_nao[4]='I';
    $coluna_nao[5]='K';
    $coluna_nao[6]='M';
    $coluna_nao[7]='O';
    $coluna_nao[8]='Q';
    $coluna_nao[9]='S';
    $coluna_nao[10]='U';
    $coluna_nao[11]='W';
    $coluna_nao[12]='Y';
    $coluna_nao[13]='AA';

    $coluna_1[1]='B';
    $coluna_1[2]='C';
    $coluna_1[3]='D';
    $coluna_1[4]='E';
    $coluna_1[5]='F';
    $coluna_1[6]='G';
    $coluna_1[7]='H';
    $coluna_1[8]='I';
    $coluna_1[9]='J';
    $coluna_1[10]='K';
    $coluna_1[11]='L';
    $coluna_1[12]='M';
    $coluna_1[13]='N';

    $codigo_cc = $_REQUEST["codigo_cc"];
    $codigo_fazendas = $_REQUEST["fazendas"];
    $codigo_conta = $_REQUEST["conta"];
    $ano = $_REQUEST["ano"];
    $tipo_rel = $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $_SESSION['codigo_conta_previsao'] = $codigo_conta;

    // monta array das contas (mantém só as contas analíticas/folha, nível 3 -
    // últimos 4 dígitos != 0, mesma regra usada em Análise de Pagamentos/Recebimentos)
    $array_conta = $_REQUEST["conta"];
    $conta = array();
    $matriz_itens = explode(",", $array_conta);
    $quantidade_itens = count($matriz_itens);

    for ($i=0; $i < $quantidade_itens; $i++) {
        if (substr($matriz_itens[$i], 3, 4) != 0) {
            $conta[$i] = $matriz_itens[$i];
        }
    }

    $conta = implode(',', $conta);

    // O filtro de conta restringe quais lançamentos entram na apuração (igual à Análise
    // de Pagamentos/Recebimentos); quando um lançamento filtrado tem rateio entre várias
    // contas, o detalhamento por conta abaixo continua mostrando todas as contas do rateio
    // (não só a selecionada) — é assim que o relatório de Análise de Pagamentos já funciona.
    $wconta_pag = '';
    $wconta_rec = '';

    if ($array_conta != '') {
        $wconta_pag = " AND (ctp_codigo_conta IN($conta) OR " . condicao_rateio_ou_grupo('ctp_codigo_conta', 'rc_codigo_conta', $conta) . ")";
        $wconta_rec = " AND (ctr_codigo_conta IN($conta) OR " . condicao_rateio_ou_grupo_ctr('ctr_codigo_conta', 'rc_codigo_conta', $conta) . ")";
    }

    $centro_custos= array();
    $matriz_itens = explode(",", $codigo_cc);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $centro_custos[$i]=$matriz_itens[$i];
    }

    $centro_custos = implode(',', $centro_custos);
    $centro_custos = substr($centro_custos,0, -1);

    $wcentro_custo_pag = '';

    if ($codigo_cc!='') {
        $wcentro_custo_pag = " AND ctp_codigo_centro_custos IN(";
        $wcentro_custo_pag.= $centro_custos;
        $wcentro_custo_pag.= ")";
    }

    $wcentro_custo_rec = '';

    if ($codigo_cc!='') {
        $wcentro_custo_rec = " AND ctr_codigo_c_custo IN(";
        $wcentro_custo_rec.= $centro_custos;
        $wcentro_custo_rec.= ")";
    }


    $fazendas= array();
    $matriz_itens = explode(",", $codigo_fazendas);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fazendas[$i]=$matriz_itens[$i];
    }

    $fazendas = implode(',', $fazendas);
    $fazendas = substr($fazendas,0, -1);

    $wlocal_pag = '';

    if ($codigo_fazendas!='') {
        $wlocal_pag = " AND ctp_codigo_fazenda IN(";
        $wlocal_pag.= $fazendas;
        $wlocal_pag.= ")";
    }

    $wlocal_rec = '';

    if ($codigo_fazendas!='') {
        $wlocal_rec = " AND ctr_codigo_fazenda IN(";
        $wlocal_rec.= $fazendas;
        $wlocal_rec.= ")";
    }

    $wlocal_previsao = '';

    if ($codigo_fazendas!='') {
        $wlocal_previsao = " AND tbl_previsao_conta_codigo_fazenda IN(";
        $wlocal_previsao.= $fazendas;
        $wlocal_previsao.= ")";
    }

    $data_inicial = $ano . '-01-01';
    $data_final = $ano . '-12-31';
    $data_sistema = date("d/m/Y");

    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    //APURAR SALDO ANTERIOR REALIZADO
    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
        INNER JOIN contas_receber
                ON bcr_id=ctr_id
             WHERE bcr_data_pagamento<'$data_inicial'" . $wcentro_custo_rec . $wlocal_rec . $wconta_rec);
    $num_rows_contas_rec = mysqli_num_rows($contas_rec);

    if ($num_rows_contas_rec!=0){
        while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
               $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
               $total_recebido+=$valor_pago;
        }
    }

    $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
        INNER JOIN contas_pagar
                ON bcp_id=ctp_id
        WHERE bcp_data_pagamento<'$data_inicial'" . $wcentro_custo_pag  . $wlocal_pag . $wconta_pag);

    $num_rows_contas_pag = mysqli_num_rows($contas_pag);

    if ($num_rows_contas_pag!=0){
        while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
               $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
               $total_pago+=$valor_pago;
    } 
        }
                      
    $saldo_anterior_realizado+= $total_recebido - $total_pago;
    // FIM DA APURACAO SALDO ANTERIOR REALIZADO

    //APURAR SALDO ANTERIOR NAO REALIZADO
    $saldo_anterior_nao_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    $wconta_previsao = ($array_conta != '') ? " AND tbl_previsao_conta_codigo IN($conta)" : '';

    $previsao_conta = mysqli_query($conector, "SELECT * FROM tbl_previsao_conta
        INNER JOIN tbl_plano_contas
                ON tbl_previsao_conta_codigo=tbl_plano_contas_codigo_id
             WHERE tbl_previsao_conta_ano < '$ano'"  . $wlocal_previsao . $wconta_previsao);

    $num_rows_previsao_conta = mysqli_num_rows($previsao_conta);

    if ($num_rows_previsao_conta!=0){
        while ( $reg_conta = mysqli_fetch_object($previsao_conta)) {
            if ($reg_conta->tbl_plano_contas_debito_credito=='C') {
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jan;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_fev;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mar;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_abr;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mai;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jun;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jul;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_ago;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_set;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_out;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_nov;
                $saldo_anterior_nao_realizado+=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_dez;
                $saldo_anterior_nao_realizado+=$valor_conta;           
            }
            else {
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jan;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_fev;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mar;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_abr;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mai;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jun;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jul;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_ago;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_set;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_out;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_nov;
                $saldo_anterior_nao_realizado-=$valor_conta;           
                $valor_conta = $reg_conta->tbl_previsao_conta_valor_dez;
                $saldo_anterior_nao_realizado-=$valor_conta;           
            }
        }
    }

    //FIM APURAR SALDO ANTERIOR NAO REALIZADO

//      Começa Excel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Worksheet;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

$nome_relatorio = "Análise de Contas Previsto/Realizado";

if ($tipo_rel==1) {
    $desc_opc_rel = 'Realizados/Previsto';
}
else if ($tipo_rel==2) {
    $desc_opc_rel = 'Realizados';
}
else {
    $desc_opc_rel = 'Previsto';
} 

if ($tipo_rel==1) {
    $spreadsheet->getActiveSheet()->mergeCells('A1:Y1');
    $spreadsheet->getActiveSheet()->mergeCells('Z1:AA1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:AA2');
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('Z1', 'Data: ' . $data_sistema)
        ->setCellValue('B2', 'Filtros: ' . $descricao_filtro);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("B3","Janeiro")
        ->setCellValue("D3","Fevereiro")
        ->setCellValue("F3","Março")
        ->setCellValue("H3","Abril")
        ->setCellValue("J3","Maio")
        ->setCellValue("L3","Junho")
        ->setCellValue("N3","Julho")
        ->setCellValue("P3","Agosto")
        ->setCellValue("R3","Setembro")
        ->setCellValue("T3","Outubro")
        ->setCellValue("V3","Novembro")
        ->setCellValue("X3","Dezembro")
        ->setCellValue("Z3","Totais")
        ->setCellValue("A5","Saldo Anterior")
        ->setCellValue("A6","Saldodo Mês")
        ->setCellValue("A7","Saldo Final")
        ->setCellValue("B4","Realizado")
        ->setCellValue("C4","Previsto")
        ->setCellValue("D4","Realizado")
        ->setCellValue("E4","Previsto")
        ->setCellValue("F4","Realizado")
        ->setCellValue("G4","Previsto")
        ->setCellValue("H4","Realizado")
        ->setCellValue("I4","Previsto")
        ->setCellValue("J4","Realizado")
        ->setCellValue("K4","Previsto")
        ->setCellValue("L4","Realizado")
        ->setCellValue("M4","Previsto")
        ->setCellValue("N4","Realizado")
        ->setCellValue("O4","Previsto")
        ->setCellValue("P4","Realizado")
        ->setCellValue("Q4","Previsto")
        ->setCellValue("R4","Realizado")
        ->setCellValue("S4","Previsto")
        ->setCellValue("T4","Realizado")
        ->setCellValue("U4","Previsto")
        ->setCellValue("V4","Realizado")
        ->setCellValue("W4","Previsto")
        ->setCellValue("X4","Realizado")
        ->setCellValue("Y4","Previsto")
        ->setCellValue("Z4","Realizado")
        ->setCellValue("AA4","Previsto");

    $spreadsheet->getActiveSheet()->getStyle('A5:A7') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->mergeCells('B3:C3');
    $spreadsheet->getActiveSheet()->mergeCells('D3:E3');
    $spreadsheet->getActiveSheet()->mergeCells('F3:G3');
    $spreadsheet->getActiveSheet()->mergeCells('H3:I3');
    $spreadsheet->getActiveSheet()->mergeCells('J3:K3');
    $spreadsheet->getActiveSheet()->mergeCells('L3:M3');
    $spreadsheet->getActiveSheet()->mergeCells('N3:O3');
    $spreadsheet->getActiveSheet()->mergeCells('P3:Q3');
    $spreadsheet->getActiveSheet()->mergeCells('R3:S3');
    $spreadsheet->getActiveSheet()->mergeCells('T3:U3');
    $spreadsheet->getActiveSheet()->mergeCells('V3:W3');
    $spreadsheet->getActiveSheet()->mergeCells('X3:Y3');
    $spreadsheet->getActiveSheet()->mergeCells('Z3:AA3');

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(24);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(14); 

    $spreadsheet->getActiveSheet()->getStyle('Z1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B4'.':AA4';
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $celulas = 'B5'.':AA5';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B6'.':AA6';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B7'.':AA7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getStyle('C4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('E4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('G4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('I4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('K4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('M4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('O4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('Q4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('S4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('U4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('W4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('Y4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('AA4')->getFont()->setColor(new Color(Color::COLOR_GRAY));

    $spreadsheet->getActiveSheet()->freezePane('B8');
    $spreadsheet->getActiveSheet()->setShowGridlines(false);
}
else {
    $spreadsheet->getActiveSheet()->mergeCells('A1:L1');
    $spreadsheet->getActiveSheet()->mergeCells('M1:N1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:N2');
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('M1', 'Data: ' . $data_sistema)
        ->setCellValue('B2', 'Filtros: ' . $descricao_filtro);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("B3","Janeiro")
        ->setCellValue("C3","Fevereiro")
        ->setCellValue("D3","Março")
        ->setCellValue("E3","Abril")
        ->setCellValue("F3","Maio")
        ->setCellValue("G3","Junho")
        ->setCellValue("H3","Julho")
        ->setCellValue("I3","Agosto")
        ->setCellValue("J3","Setembro")
        ->setCellValue("K3","Outubro")
        ->setCellValue("L3","Novembro")
        ->setCellValue("M3","Dezembro")
        ->setCellValue("N3","Totais")
        ->setCellValue("A4","Saldo Anterior")
        ->setCellValue("A5","Saldodo Mês")
        ->setCellValue("A6","Saldo Final");

    $spreadsheet->getActiveSheet()->getStyle('A4:A6') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(24);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);

    $spreadsheet->getActiveSheet()->getStyle('M1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $celulas = 'B4'.':N4';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B5'.':N5';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B6'.':N6';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    if  ($tipo_rel==2) {
        $spreadsheet->getActiveSheet()->freezePane('B7');
        $spreadsheet->getActiveSheet()->setShowGridlines(false);
    }
    else {
        $spreadsheet->getActiveSheet()->freezePane('B4');
        $spreadsheet->getActiveSheet()->setShowGridlines(false);
    }     
}

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('B3:AA3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


$linha=8;

if ($tipo_rel==1){
                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                            WHERE tbl_plano_contas_lixeira=0 
                            ORDER BY tbl_plano_contas_codigo_id ASC"); 
                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $descricao_conta[$codigo_conta] = substr($registro_tbl_conta->tbl_plano_contas_descricao, 0, 19);
                        $tem_valor[$codigo_conta] = "N";

                        for ($i=1; $i <= 13 ; $i++) {
                            $total_realizado[$codigo_conta][$i]=0;
                            $total_nao_realizado[$codigo_conta][$i]=0;
                        }
                    }                        

                    for ($i=1; $i <= 13 ; $i++) { 
                        $saldo_final_mes[$i]=0;
                        $saldo_mes[$i]=0;
                        $saldo_anterior_mes[$i]=0;
                        $valor_credito[$i]=0;
                        $valor_debito[$i]=0;

                        $saldo_final_mes_nao[$i]=0;
                        $saldo_mes_nao[$i]=0;
                        $saldo_anterior_mes_nao[$i]=0;
                        $valor_credito_nao[$i]=0;
                        $valor_debito_nao[$i]=0;
                    }

                    // Pré-processamento do Realizado: uma única passada nas contas_receber/contas_pagar
                    // do período, repartindo (fatias) os lançamentos com rateio/parcelamento entre as
                    // contas contábeis correspondentes (tbl_ctr_rateio/tbl_ctp_rateio) — em vez de uma
                    // consulta "WHERE conta='X'" por conta, que não encontrava os lançamentos cuja conta
                    // ficou gravada só no rateio (rateio entre contas e/ou parcelas seguintes de um
                    // parcelamento, onde só a 1ª parcela/ocorrência grava a conta diretamente).
                    $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                        inner join contas_receber
                                on ctr_id=bcr_id
                        WHERE bcr_data_pagamento >='$data_inicial' AND
                              bcr_data_pagamento <='$data_final'" . $wcentro_custo_rec . $wlocal_rec . $wconta_rec);

                    while ($registro_contas_rec = mysqli_fetch_object($contas_rec)) {
                        $mes = (int)substr($registro_contas_rec->bcr_data_pagamento, 5, 2);
                        $valor_pago = $registro_contas_rec->bcr_valor_pagamento;

                        $valor_credito[$mes] += $valor_pago;

                        $fatias = montar_fatias_conta_rateio_ctr($conector, $registro_contas_rec->ctr_id, $registro_contas_rec->ctr_codigo_conta, $valor_pago, $registro_contas_rec->ctr_numero_doc, $registro_contas_rec->ctr_codigo_cliente_fornecedor);

                        foreach ($fatias as $fatia) {
                            acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
                        }
                    } // fim while contas a receber

                    $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                        inner join contas_pagar
                                on ctp_id=bcp_id
                        WHERE bcp_data_pagamento >='$data_inicial' AND
                              bcp_data_pagamento <='$data_final'" . $wcentro_custo_pag . $wlocal_pag . $wconta_pag);

                    while ($registro_contas_pag = mysqli_fetch_object($contas_pag)) {
                        $mes = (int)substr($registro_contas_pag->bcp_data_pagamento, 5, 2);
                        $valor_pago = $registro_contas_pag->bcp_valor_pagamento;

                        $valor_debito[$mes] += $valor_pago;

                        $fatias = montar_fatias_conta_rateio_ctp($conector, $registro_contas_pag->ctp_id, $registro_contas_pag->ctp_codigo_conta, $valor_pago, $registro_contas_pag->ctp_grupo_repeticao, $registro_contas_pag->ctp_numero_doc, $registro_contas_pag->ctp_codigo_fornecedor, $registro_contas_pag->ctp_incluido_em);

                        foreach ($fatias as $fatia) {
                            acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
                        }
                    } // fim while contas a pagar
                    // Fim do pré-processamento do Realizado

                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                            WHERE tbl_plano_contas_nivel=3 AND
                                  tbl_plano_contas_lixeira=0
                            ORDER BY tbl_plano_contas_codigo_id ASC");

                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){

                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
                        $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

                        $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

                        $previsao_conta = mysqli_query($conector, "SELECT *  FROM tbl_previsao_conta
                                WHERE tbl_previsao_conta_codigo='$codigo_conta' AND
                                      tbl_previsao_conta_ano = '$ano'"  . $wlocal_previsao . $wconta_previsao);
                        $num_rows_previsao_conta = mysqli_num_rows($previsao_conta);

                        if ($num_rows_previsao_conta!=0){
                            while ( $reg_conta = mysqli_fetch_object($previsao_conta)) {
                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jan;
                                $mes_conta = 01;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_fev;
                                $mes_conta = 02;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mar;
                                $mes_conta = 03;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_abr;
                                $mes_conta = 04;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mai;
                                $mes_conta = 05;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jun;
                                $mes_conta = 06;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jul;
                                $mes_conta = 07;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_ago;
                                $mes_conta = 8;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_set;
                                $mes_conta = 9;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_out;
                                $mes_conta = 10;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_nov;
                                $mes_conta = 11;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_dez;
                                $mes_conta = 12;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito_nao[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito_nao[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }
                            }
                        }
                    } // fim while plano de contas

                    // apuracao do saldo por mes

                    $saldo_anterior = $saldo_anterior_realizado;
                    $saldo_anterior_nao = $saldo_anterior_nao_realizado;

                    for ($i=1; $i <= 13 ; $i++) {
                        $saldo_mes[$i]=$valor_credito[$i] - $valor_debito[$i];
                        $saldo_mes_nao[$i]=$valor_credito_nao[$i] - $valor_debito_nao[$i];

                        if ($i==1){
                            $saldo_anterior_mes[$i]=$saldo_anterior;
                            $saldo_final_mes[$i]=$saldo_mes[$i] + $saldo_anterior_mes[$i];

                            $saldo_anterior_mes_nao[$i]=$saldo_anterior_nao;
                            $saldo_final_mes_nao[$i]=$saldo_mes_nao[$i] + $saldo_anterior_mes_nao[$i];
                        }
                        else {
                            $saldo_anterior_mes[$i]=$saldo_final_mes[$i-1];
                            $saldo_final_mes[$i]= $saldo_mes[$i] + $saldo_anterior_mes[$i];

                            $saldo_anterior_mes_nao[$i]=$saldo_final_mes_nao[$i-1];
                            $saldo_final_mes_nao[$i]= $saldo_mes_nao[$i] + $saldo_anterior_mes_nao[$i];
                        }
                    } 


        $colx=1;
        $linha=5;

        for ($i=1; $i <= 12 ; $i++) { 
            $colx++;
            $celulas = $coluna[$i].$linha;
            
            if ($saldo_anterior_mes[$i]<0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
            }
            else if ($saldo_anterior_mes[$i]>0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
            }
            else {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
            }
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes[$i]);

            $colx++;
            $celulas = $coluna_nao[$i].$linha;
            
            if ($saldo_anterior_mes_nao[$i]<0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHRED));
            }
            else if ($saldo_anterior_mes_nao[$i]>0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHGREEN));
            }
            else {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
            }
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes_nao[$i]);
        }

        $colx=1;
        $linha=6;

        for ($i=1; $i <= 12 ; $i++) { 
            $colx++;
            $celulas = $coluna[$i].$linha;
            
            if ($saldo_mes[$i]<0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
            }
            else if ($saldo_mes[$i]>0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
            }
            else {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
            }
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes[$i]);

            $colx++;
            $celulas = $coluna_nao[$i].$linha;
            
            if ($saldo_mes_nao[$i]<0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHRED));
            }
            else if ($saldo_mes_nao[$i]>0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHGREEN));
            }
            else {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
            }
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes_nao[$i]);
        }

        $colx=1;
        $linha=7;

        for ($i=1; $i <= 12 ; $i++) { 
            $colx++;
            $celulas = $coluna[$i].$linha;
            
            if ($saldo_final_mes[$i]<0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
            }
            else if ($saldo_final_mes[$i]>0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
            }
            else {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
            }
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes[$i]);

            $colx++;
            $celulas = $coluna_nao[$i].$linha;
            
            if ($saldo_final_mes_nao[$i]<0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHRED));
            }
            else if ($saldo_final_mes_nao[$i]>0) {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHGREEN));
            }
            else {
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
            }
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes_nao[$i]);
        }

        foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
            if ($value_tem_valor == "S"){
                $codigo_conta = (int)$key_tem_valor;
                $linha++;
                $celulas = 'A'.$linha;

                if (substr($codigo_conta, 1,6)==0){
                    if (substr($codigo_conta, 0,1)==1) {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                    }
                    else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                    }
                    else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                    }
                }
                else if (substr($codigo_conta, 3,4)==0){
                    if (substr($codigo_conta, 0,1)==1) {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                    }
                    else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                    }
                    else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                    }
                }
                else {
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                }

                $colx=1;
                for ($i=1; $i <= 13 ; $i++) { 
                    $colx++;
                    $celulas = $coluna[$i].$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    if (substr($codigo_conta, 1,6)==0){
                        if (substr($codigo_conta, 0,1)==1) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                        }
                        else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                        }
                        else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                        }
                    }
                    else if (substr($codigo_conta, 3,4)==0){
                        if (substr($codigo_conta, 0,1)==1) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                        }
                        else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                        }
                        else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                        }
                    }
                    else {
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                    }

                    $colx++;
                    $celulas = $coluna_nao[$i].$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2); 
                    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    if (substr($codigo_conta, 1,6)==0){
                            if (substr($codigo_conta, 0,1)==1) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                            }
                            else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                            }
                            else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                            }
                        }
                        else if (substr($codigo_conta, 3,4)==0){
                            if (substr($codigo_conta, 0,1)==1) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                            }
                            else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                            }
                            else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                            }
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                        }

                }
            }

        }

}
else if($tipo_rel==2) {
                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                   WHERE tbl_plano_contas_lixeira=0 
                                                ORDER BY tbl_plano_contas_codigo_id ASC"); 
                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $descricao_conta[$codigo_conta] = $registro_tbl_conta->tbl_plano_contas_descricao;

                        $tem_valor[$codigo_conta] = "N";

                        for ($i=1; $i <= 13 ; $i++) {
                            $total_realizado[$codigo_conta][$i]=0;
                        }
                    }                        

                    for ($i=1; $i <= 13 ; $i++) { 
                        $saldo_final_mes[$i]=0;
                        $saldo_mes[$i]=0;
                        $saldo_anterior_mes[$i]=0;
                        $valor_credito[$i]=0;
                        $valor_debito[$i]=0;
                    }

                    // Pré-processamento do Realizado: uma única passada nas contas_receber/contas_pagar
                    // do período, repartindo (fatias) os lançamentos com rateio/parcelamento entre as
                    // contas contábeis correspondentes (tbl_ctr_rateio/tbl_ctp_rateio) — em vez de uma
                    // consulta "WHERE conta='X'" por conta, que não encontrava os lançamentos cuja conta
                    // ficou gravada só no rateio (rateio entre contas e/ou parcelas seguintes de um
                    // parcelamento, onde só a 1ª parcela/ocorrência grava a conta diretamente).
                    $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                        inner join contas_receber
                                on ctr_id=bcr_id
                        WHERE bcr_data_pagamento >='$data_inicial' AND
                              bcr_data_pagamento <='$data_final'" . $wcentro_custo_rec . $wlocal_rec . $wconta_rec);

                    while ($registro_contas_rec = mysqli_fetch_object($contas_rec)) {
                        $mes = (int)substr($registro_contas_rec->bcr_data_pagamento, 5, 2);
                        $valor_pago = $registro_contas_rec->bcr_valor_pagamento;

                        $valor_credito[$mes] += $valor_pago;

                        $fatias = montar_fatias_conta_rateio_ctr($conector, $registro_contas_rec->ctr_id, $registro_contas_rec->ctr_codigo_conta, $valor_pago, $registro_contas_rec->ctr_numero_doc, $registro_contas_rec->ctr_codigo_cliente_fornecedor);

                        foreach ($fatias as $fatia) {
                            acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
                        }
                    } // fim while contas a receber

                    $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                        inner join contas_pagar
                                on ctp_id=bcp_id
                        WHERE bcp_data_pagamento >='$data_inicial' AND
                              bcp_data_pagamento <='$data_final'" . $wcentro_custo_pag . $wlocal_pag . $wconta_pag);

                    while ($registro_contas_pag = mysqli_fetch_object($contas_pag)) {
                        $mes = (int)substr($registro_contas_pag->bcp_data_pagamento, 5, 2);
                        $valor_pago = $registro_contas_pag->bcp_valor_pagamento;

                        $valor_debito[$mes] += $valor_pago;

                        $fatias = montar_fatias_conta_rateio_ctp($conector, $registro_contas_pag->ctp_id, $registro_contas_pag->ctp_codigo_conta, $valor_pago, $registro_contas_pag->ctp_grupo_repeticao, $registro_contas_pag->ctp_numero_doc, $registro_contas_pag->ctp_codigo_fornecedor, $registro_contas_pag->ctp_incluido_em);

                        foreach ($fatias as $fatia) {
                            acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
                        }
                    } // fim while contas a pagar
                    // Fim do pré-processamento do Realizado

                    // apuracao do saldo por mes

                    $saldo_anterior = $saldo_anterior_realizado;
                    for ($i=1; $i <= 13 ; $i++) {
                        $saldo_mes[$i]=$valor_credito[$i] - $valor_debito[$i];

                        if ($i==1){
                            $saldo_anterior_mes[$i]=$saldo_anterior;
                            $saldo_final_mes[$i]=$saldo_mes[$i] + $saldo_anterior_mes[$i];
                        }
                        else {
                            $saldo_anterior_mes[$i]=$saldo_final_mes[$i-1];
                            $saldo_final_mes[$i]= $saldo_mes[$i] + $saldo_anterior_mes[$i];
                        }
                    } 

                    $colx=1;
                    $linha=4;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_anterior_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes[$i]);
                    }

                    $colx=1;
                    $linha=5;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes[$i]);
                    }

                    $colx=1;
                    $linha=6;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_final_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes[$i]);
                    }

                    
                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {

                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $linha++;
                            $celulas = 'A'.$linha;

                            if (substr($codigo_conta, 1,6)==0){
                                if (substr($codigo_conta, 0,1)==1) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                if (substr($codigo_conta, 0,1)==1) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                            }
                            else {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }

                            $colx=1;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx++;
                                $celulas = $coluna_1[$i].$linha;
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                if (substr($codigo_conta, 1,6)==0){
                                    if (substr($codigo_conta, 0,1)==1) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                    }
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    if (substr($codigo_conta, 0,1)==1) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                    }
                                }
                                else {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                }
                            }
                        }
                    }

}  
else {
                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                   WHERE tbl_plano_contas_lixeira=0 
                                                ORDER BY tbl_plano_contas_codigo_id ASC"); 
                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $descricao_conta[$codigo_conta] = $registro_tbl_conta->tbl_plano_contas_descricao;

                        $tem_valor[$codigo_conta] = "N";

                        for ($i=1; $i <= 13 ; $i++) {
                            $total_nao_realizado[$codigo_conta][$i]=0;
                        }
                    }                        

                    for ($i=1; $i <= 13 ; $i++) { 
                        $saldo_final_mes[$i]=0;
                        $saldo_mes[$i]=0;
                        $saldo_anterior_mes[$i]=0;
                        $valor_credito[$i]=0;
                        $valor_debito[$i]=0;
                    }

                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                    WHERE tbl_plano_contas_nivel=3 AND 
                                                          tbl_plano_contas_lixeira=0 
                                                 ORDER BY tbl_plano_contas_codigo_id ASC"); 

                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 

                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
                        $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

                        $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

                        $previsao_conta = mysqli_query($conector, "SELECT *  FROM tbl_previsao_conta
                                WHERE tbl_previsao_conta_codigo='$codigo_conta' AND 
                                      tbl_previsao_conta_ano = '$ano'" . $wlocal_previsao);
                        $num_rows_previsao_conta = mysqli_num_rows($previsao_conta);

                        if ($num_rows_previsao_conta!=0){
                            while ($reg_conta = mysqli_fetch_object($previsao_conta)
                                   ) {
                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jan;
                                $mes_conta = 01;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_fev;
                                $mes_conta = 02;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mar;
                                $mes_conta = 03;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_abr;
                                $mes_conta = 04;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_mai;
                                $mes_conta = 05;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jun;
                                $mes_conta = 06;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_jul;
                                $mes_conta = 07;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_ago;
                                $mes_conta = 8;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_set;
                                $mes_conta = 9;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_out;
                                $mes_conta = 10;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_nov;
                                $mes_conta = 11;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }

                                $valor_conta = $reg_conta->tbl_previsao_conta_valor_dez;
                                $mes_conta = 12;

                                $total_nao_realizado[$conta_nivel_1][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_1][13]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$conta_nivel_2][13]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][$mes_conta]+=$valor_conta;
                                $total_nao_realizado[$codigo_conta][13]+=$valor_conta;

                                if ($debito_credito=="C") {
                                    $valor_credito[$mes_conta]+=$valor_conta;
                                }
                                else {
                                    $valor_debito[$mes_conta]+=$valor_conta;
                                }

                                if ($valor_conta!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }
                            }
                        }
                    } // fim while plano de contas

                    // apuracao do saldo por mes

                    $saldo_anterior = $saldo_anterior_nao_realizado;
                    for ($i=1; $i <= 13 ; $i++) {
                        $saldo_mes[$i]=$valor_credito[$i] - $valor_debito[$i];

                        if ($i==1){
                            $saldo_anterior_mes[$i]=$saldo_anterior;
                            $saldo_final_mes[$i]=$saldo_mes[$i] + $saldo_anterior_mes[$i];
                        }
                        else {
                            $saldo_anterior_mes[$i]=$saldo_final_mes[$i-1];
                            $saldo_final_mes[$i]= $saldo_mes[$i] + $saldo_anterior_mes[$i];
                        }
                    } 

                    $colx=1;
                    $linha=3;

                    /*for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_anterior_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes[$i]);
                    }

                    $colx=1;
                    $linha=5;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes[$i]);
                    }

                    $colx=1;
                    $linha=6;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_final_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes[$i]);
                    }*/

                    
                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {

                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $linha++;
                            $celulas = 'A'.$linha;

                            if (substr($codigo_conta, 1,6)==0){
                                if (substr($codigo_conta, 0,1)==1) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                if (substr($codigo_conta, 0,1)==1) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                                else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                                }
                            }
                            else {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }

                            $colx=1;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx++;
                                $celulas = $coluna_1[$i].$linha;
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                if (substr($codigo_conta, 1,6)==0){
                                    if (substr($codigo_conta, 0,1)==1) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('d8e4bc');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('e6b8b7');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('da9694');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                    }
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    if (substr($codigo_conta, 0,1)==1) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('ebfbde');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==2 || substr($codigo_conta, 0,1)==3) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                    }
                                    else if (substr($codigo_conta, 0,1)==4 || substr($codigo_conta, 0,1)==5) {
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f2dcdb');
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                    }
                                }
                                else {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                            }
                        }
                    }

}   

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="contas_previsto_realizado.xlsx"');
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

exit;


?>
              
                
