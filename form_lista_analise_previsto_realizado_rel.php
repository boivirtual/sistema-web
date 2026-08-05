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
    function montar_fatias_conta_rateio_ctp($conector, $ctp_id, $cod_conta_header, $valor, $ctp_grupo_repeticao = null, $ctp_numero_doc = null, $ctp_codigo_fornecedor = null, $ctp_incluido_em = null, $fazendas_ids = '', $cc_ids = '') {
        if ($cod_conta_header !== null && $cod_conta_header !== '') {
            return [['cod_conta' => $cod_conta_header, 'valor' => $valor]];
        }

        $ctp_id_rateio = resolver_primeiro_ctp_rateio($conector, $ctp_id, $ctp_grupo_repeticao, $ctp_numero_doc, $ctp_codigo_fornecedor, $ctp_incluido_em);

        // soma global do rateio (todas as linhas, sem filtro de local/CC) - denominador
        // da proporção de cada linha. Não filtrar aqui é o que garante a proporção
        // certa quando o rateio também separa por conta (não só por local/CC).
        $rs_soma = mysqli_query($conector, "SELECT SUM(rc_valor_conta) AS soma FROM tbl_ctp_rateio
            WHERE rc_ctp_id='$ctp_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");
        $row_soma = $rs_soma ? mysqli_fetch_object($rs_soma) : null;
        $soma_rateio = $row_soma ? (float)$row_soma->soma : 0;

        if ($soma_rateio == 0) {
            return array();
        }

        // Quando há filtro de Local/Centro de Custos, considera nos totais só as linhas
        // do rateio que casam com o filtro (mesma lógica já usada em Análise de
        // Pagamentos/Recebimentos) - sem isso, o total somava o documento inteiro mesmo
        // filtrando por um local/CC específico.
        $wlocal_rateio = ($fazendas_ids != '') ? " AND rc_codigo_local IN($fazendas_ids)" : '';
        $wcc_rateio = ($cc_ids != '') ? " AND (rc_codigo_cc IS NULL OR rc_codigo_cc IN($cc_ids))" : '';

        $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctp_rateio
            WHERE rc_ctp_id='$ctp_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''" . $wlocal_rateio . $wcc_rateio);

        $fatias = array();

        while ($r = mysqli_fetch_object($rs)) {
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
    function montar_fatias_conta_rateio_ctr($conector, $ctr_id, $cod_conta_header, $valor, $ctr_numero_doc = null, $ctr_codigo_cliente = null, $fazendas_ids = '', $cc_ids = '') {
        if ($cod_conta_header !== null && $cod_conta_header !== '') {
            return [['cod_conta' => $cod_conta_header, 'valor' => $valor]];
        }

        $ctr_id_rateio = resolver_primeiro_ctr_rateio($conector, $ctr_id, $ctr_numero_doc, $ctr_codigo_cliente);

        // soma global do rateio (todas as linhas, sem filtro de local/CC) - denominador
        // da proporção de cada linha. Não filtrar aqui é o que garante a proporção
        // certa quando o rateio também separa por conta (não só por local/CC).
        $rs_soma = mysqli_query($conector, "SELECT SUM(rc_valor_conta) AS soma FROM tbl_ctr_rateio
            WHERE rc_ctr_id='$ctr_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");
        $row_soma = $rs_soma ? mysqli_fetch_object($rs_soma) : null;
        $soma_rateio = $row_soma ? (float)$row_soma->soma : 0;

        if ($soma_rateio == 0) {
            return array();
        }

        // Quando há filtro de Local/Centro de Custos, considera nos totais só as linhas
        // do rateio que casam com o filtro (mesma lógica já usada em Análise de
        // Pagamentos/Recebimentos) - sem isso, o total somava o documento inteiro mesmo
        // filtrando por um local/CC específico.
        $wlocal_rateio = ($fazendas_ids != '') ? " AND rc_codigo_local IN($fazendas_ids)" : '';
        $wcc_rateio = ($cc_ids != '') ? " AND (rc_codigo_cc IS NULL OR rc_codigo_cc IN($cc_ids))" : '';

        $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctr_rateio
            WHERE rc_ctr_id='$ctr_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''" . $wlocal_rateio . $wcc_rateio);

        $fatias = array();

        while ($r = mysqli_fetch_object($rs)) {
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

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $mes_atual = date('m');

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

    @ session_start(); 

    $codigo_cc = $_REQUEST["codigo_cc"];
    $codigo_fazendas = $_REQUEST["fazendas"];
    $codigo_conta = $_REQUEST["conta"];
    $ano = $_REQUEST["ano"];
    $tipo_rel = $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $_SESSION['codigo_conta_apr'] = $codigo_conta;
    $_SESSION['codigo_local_apr'] = $codigo_fazendas;
    $_SESSION['codigo_c_custo_apr'] = $codigo_cc;
    $_SESSION['tipo_rel_apr'] = $tipo_rel;

    $scrollY = ($tipo_rel == 1 || $tipo_rel == 2) ? "200px" : "300px";

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

    // Rateio-aware (igual Análise de Pagamentos/Recebimentos): quando o lançamento tem
    // rateio, ctp_codigo_centro_custos/ctr_codigo_c_custo ficam NULL no cabeçalho e o
    // centro de custos só existe por linha em tbl_ctp_rateio/tbl_ctr_rateio.
    $wcentro_custo_pag = '';

    if ($codigo_cc!='') {
        $wcentro_custo_pag = " AND (ctp_codigo_centro_custos IN($centro_custos) OR " . condicao_rateio_ou_grupo('ctp_codigo_centro_custos', 'rc_codigo_cc', $centro_custos) . ")";
    }

    $wcentro_custo_rec = '';

    if ($codigo_cc!='') {
        $wcentro_custo_rec = " AND (ctr_codigo_c_custo IN($centro_custos) OR " . condicao_rateio_ou_grupo_ctr('ctr_codigo_c_custo', 'rc_codigo_cc', $centro_custos) . ")";
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
        $wlocal_pag = " AND (ctp_codigo_fazenda IN($fazendas) OR " . condicao_rateio_ou_grupo('ctp_codigo_fazenda', 'rc_codigo_local', $fazendas) . ")";
    }

    $wlocal_rec = '';

    if ($codigo_fazendas!='') {
        $wlocal_rec = " AND (ctr_codigo_fazenda IN($fazendas) OR " . condicao_rateio_ou_grupo_ctr('ctr_codigo_fazenda', 'rc_codigo_local', $fazendas) . ")";
    }

    $wlocal_previsao = '';

    if ($codigo_fazendas!='') {
        $wlocal_previsao = " AND tbl_previsao_conta_codigo_fazenda IN(";
        $wlocal_previsao.= $fazendas;
        $wlocal_previsao.= ")";
    }

    $data_inicial = $ano . '-01-01';
    $data_final = $ano . '-12-31';

    //APURAR SALDO ANTERIOR REALIZADO
    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    $sql = "SELECT * FROM baixa_contas_receber
        INNER JOIN contas_receber
                ON bcr_id=ctr_id
             WHERE bcr_data_pagamento<'$data_inicial'" . $wcentro_custo_rec . $wlocal_rec . $wconta_rec;

    $contas_rec = mysqli_query($conector, $sql);
    $num_rows_contas_rec = mysqli_num_rows($contas_rec);

    if ($num_rows_contas_rec!=0){
        WHILE ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
               $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
               $total_recebido+=$valor_pago;
        }
    }

    $sql = "SELECT * FROM baixa_contas_pagar
        INNER JOIN contas_pagar
                ON bcp_id=ctp_id
        WHERE bcp_data_pagamento<'$data_inicial'" . $wcentro_custo_pag  . $wlocal_pag . $wconta_pag;

    $contas_pag = mysqli_query($conector, $sql);
    $num_rows_contas_pag = mysqli_num_rows($contas_pag);

    if ($num_rows_contas_pag!=0){
        WHILE ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
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
             WHERE tbl_previsao_conta_ano = '$anoAnterior'"  . $wlocal_previsao . $wconta_previsao);
    
    $num_rows_previsao_conta = mysqli_num_rows($previsao_conta);

    if ($num_rows_previsao_conta!=0){
        WHILE ( $reg_conta = mysqli_fetch_object($previsao_conta)) {
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

?> 

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
  <link href="css/tabela.css" rel="stylesheet">
  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style>
    html, body {
        width: 100%;
        overflow-x: hidden;
    }

    table.dataTable thead th {
        border-bottom: 0;
        padding-bottom: 5px;
        padding-top: 5px;
    }

    #dados_cliente {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .panel,
    .panel-body,
    .tab-content,
    .row,
    .col-lg-12,
    .col-md-12 {
        max-width: 100% !important;
    }

    .panel-body {
        overflow: hidden !important;
    }

    div.dataTables_wrapper {
        width: 100% !important;
    }

    div.dataTables_scrollBody {
        overflow-x: auto !important;
    }

    div.dataTables_scrollHeadInner,
    table.dataTable {
        width: 100% !important;
    }

    #tabela_analise_previsto_realizado th,
    #tabela_analise_previsto_realizado td {
        white-space: nowrap;
    }


    #tabela_analise_previsto_realizado thead th {
        white-space: nowrap;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    #tabela_analise_previsto_realizado tbody td {
        white-space: nowrap;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    #tabela_analise_previsto_realizado thead th.text-right,
    #tabela_analise_previsto_realizado thead th[style*="text-align:right"] {
        text-align: right !important;
        padding-right: 14px !important;
    }

    #tabela_analise_previsto_realizado tbody td[style*="text-align:right"] {
        text-align: right !important;
        padding-right: 14px !important;
    }

    /* No modo Realizado/Previsto combinado (classe modo-combinado) a tabela tem 26
       colunas de dados (2 por mês) e o cabeçalho tem linhas extras (SALDO ANTERIOR/DO
       MÊS/FINAL) fixadas dentro do próprio <thead>. Com table-layout:auto (padrão), o
       DataTables mantém 2 tabelas internas (cabeçalho e corpo) que calculam a largura
       de cada coluna de forma independente, cada uma com base só no próprio conteúdo —
       em tabela tão larga essa diferença se acumula e desalinha cabeçalho x dados ao
       rolar. table-layout:fixed sozinho (sem <colgroup>) piora: a especificação só
       considera a 1ª linha da tabela para definir a largura de cada coluna, e essa
       1ª linha usa colspan="2" por mês — colunas com colspan são ignoradas para esse
       cálculo, então o navegador cai no "distribuir o espaço restante igualmente",
       esmagando os valores. Por isso a tabela é gerada com um <colgroup> explícito
       (uma largura fixa por coluna real), que tem prioridade sobre qualquer linha e é
       clonado pelo DataTables tanto no cabeçalho quanto no corpo — garantindo que as
       27 colunas fiquem com a mesma largura nas duas tabelas internas. Também é
       preciso liberar a tabela do width:100% (regra genérica de table.dataTable mais
       abaixo) para que ela possa ficar mais larga que a área visível e rolar
       horizontalmente, já que a soma das larguras do colgroup passa da tela. */
    #tabela_analise_previsto_realizado_wrapper .dataTables_scrollHead table.modo-combinado,
    #tabela_analise_previsto_realizado_wrapper .dataTables_scrollBody table.modo-combinado {
        table-layout: fixed;
        width: auto !important;
    }
  </style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_financeiros.php">Relatórios Financeiros</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Análise de Contas Previsto/Realizado</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-search-dollar"></i> Análise de Contas Previsto/Realizado</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel">
                        <div class=panel-body>
                            <div class="tab-content">
                                <div class="container" id="dados_cliente">
                                    <input type="hidden" id="expande_tela" value="S">

                                    <input type="hidden" id="ano_mensal"
                                    <?php echo "value='".$ano."'";?>>

                                    <input type="hidden" id="tipo_rel"
                                    <?php echo "value='".$tipo_rel."'";?>>

                                    <input type="hidden" id="descricao_filtro"
                                    <?php echo "value='".$descricao_filtro."'";?>>

                                    <input type="hidden" id="codigo_fazenda"
                                    <?php echo "value='".$codigo_fazendas."'";?>>

                                    <input type="hidden" id="codigo_cc"
                                    <?php echo "value='".$codigo_cc."'";?>>

                                    <input type="hidden" id="codigo_conta"
                                    <?php echo "value='".$codigo_conta."'";?>>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="label_consulta_rel">Filtros:</label>
                                            <span><?php echo $descricao_filtro;?></span>
                                        </div>

                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_previsao()">Voltar
                                            </button>

                                            <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="listar_previsao_excel()">Excel
                                            </button>
                                        </div>
                                    </div>

                                    <!--<hr align="center"> -->
<?php
    if ($tipo_rel==1) {
                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                            WHERE tbl_plano_contas_lixeira=0 
                            ORDER BY tbl_plano_contas_codigo_id ASC"); 
                    WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
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
                        INNER JOIN contas_receber
                                on ctr_id=bcr_id
                        WHERE bcr_data_pagamento >='$data_inicial' AND
                              bcr_data_pagamento <='$data_final'" . $wcentro_custo_rec . $wlocal_rec . $wconta_rec);

                    while ($registro_contas_rec = mysqli_fetch_object($contas_rec)) {
                        $mes = (int)substr($registro_contas_rec->bcr_data_pagamento, 5, 2);
                        $valor_pago = $registro_contas_rec->bcr_valor_pagamento;

                        $valor_credito[$mes] += $valor_pago;

                        $fatias = montar_fatias_conta_rateio_ctr($conector, $registro_contas_rec->ctr_id, $registro_contas_rec->ctr_codigo_conta, $valor_pago, $registro_contas_rec->ctr_numero_doc, $registro_contas_rec->ctr_codigo_cliente_fornecedor, $fazendas, $centro_custos);

                        foreach ($fatias as $fatia) {
                            acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
                        }
                    } // fim WHILE contas a receber

                    $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                        INNER JOIN contas_pagar
                                on ctp_id=bcp_id
                        WHERE bcp_data_pagamento >='$data_inicial' AND
                              bcp_data_pagamento <='$data_final'" . $wcentro_custo_pag . $wlocal_pag . $wconta_pag);

                    while ($registro_contas_pag = mysqli_fetch_object($contas_pag)) {
                        $mes = (int)substr($registro_contas_pag->bcp_data_pagamento, 5, 2);
                        $valor_pago = $registro_contas_pag->bcp_valor_pagamento;

                        $valor_debito[$mes] += $valor_pago;

                        $fatias = montar_fatias_conta_rateio_ctp($conector, $registro_contas_pag->ctp_id, $registro_contas_pag->ctp_codigo_conta, $valor_pago, $registro_contas_pag->ctp_grupo_repeticao, $registro_contas_pag->ctp_numero_doc, $registro_contas_pag->ctp_codigo_fornecedor, $registro_contas_pag->ctp_incluido_em, $fazendas, $centro_custos);

                        foreach ($fatias as $fatia) {
                            acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
                        }
                    } // fim WHILE contas a pagar
                    // Fim do pré-processamento do Realizado

                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                            WHERE tbl_plano_contas_nivel=3 AND
                                  tbl_plano_contas_lixeira=0
                            ORDER BY tbl_plano_contas_codigo_id ASC");

                    WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){

                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
                        $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

                        $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

                        $previsao_conta = mysqli_query($conector, "SELECT *  FROM tbl_previsao_conta
                                WHERE tbl_previsao_conta_codigo='$codigo_conta' AND
                                      tbl_previsao_conta_ano = '$ano'"  . $wlocal_previsao . $wconta_previsao);
                        $num_rows_previsao_conta = mysqli_num_rows($previsao_conta);

                        if ($num_rows_previsao_conta!=0){
                            WHILE ( $reg_conta = mysqli_fetch_object($previsao_conta)) {
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
                    } // fim WHILE plano de contas

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

        $thead = '';

        // <colgroup> explícito: com table-layout:fixed, a largura de cada coluna só é
        // determinada pela 1ª linha da tabela (e aqui a 1ª linha usa colspan="2" por
        // mês, que a especificação de fixed layout ignora para cálculo de largura por
        // coluna) ou por <col> — sem <colgroup>, o navegador cai no "distribuir o
        // espaço restante igualmente", esmagando os valores. O <colgroup> é o único
        // jeito confiável de fixar a largura de cada uma das 27 colunas (Descrição +
        // 12 meses x 2 + Total x 2) igual no cabeçalho e no corpo.
        $thead .= '<colgroup><col style="width:220px;">';
        for ($i = 1; $i <= 13; $i++) {
            $thead .= '<col style="width:100px;"><col style="width:100px;">';
        }
        $thead .= '</colgroup>';

        $thead .= '<thead>';

        // Linha 1
        $thead .= '<tr>';
        $thead .= '<th rowspan="2">Descrição da Conta&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>';

        for ($i = 1; $i <= 12; $i++) {
            $thead .= '<th colspan="2" class="text-center">' . $array_mes[$i] . '</th>';
        }

        $thead .= '<th colspan="2" class="text-center">Total</th>';
        $thead .= '</tr>';

        // Linha 2
        $thead .= '<tr>';
        for ($i = 1; $i <= 12; $i++) {
            $thead .= '<th class="text-right">Realizado</th>';
            $thead .= '<th class="text-right" style="color:#a6a6a6;">Previsto</th>';
        }
        $thead .= '<th class="text-right">Realizado</th>';
        $thead .= '<th class="text-right" style="color:#a6a6a6;">Previsto</th>';
        $thead .= '</tr>';

        // SALDO ANTERIOR
        $thead .= '<tr>';
        $thead .= '<th class="text-right" style="font-weight:bold;">SALDO ANTERIOR</th>';

        for ($i = 1; $i <= 12; $i++) {
            if ($saldo_anterior_mes[$i] < 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#8B0000;">'
                       . number_format($saldo_anterior_mes[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_anterior_mes[$i] > 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#006400;">'
                       . number_format($saldo_anterior_mes[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th class="text-right" style="font-weight:bold;">'
                       . number_format($saldo_anterior_mes[$i], 2, ',', '.')
                       . '</th>';
            }

            if ($saldo_anterior_mes_nao[$i] < 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#ff8f8f;">'
                       . number_format($saldo_anterior_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_anterior_mes_nao[$i] > 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#7db87d;">'
                       . number_format($saldo_anterior_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th class="text-right" style="font-weight:bold;">'
                       . number_format($saldo_anterior_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            }
        }

        $thead .= '<th></th>';
        $thead .= '<th></th>';
        $thead .= '</tr>';

        // SALDO DO MÊS
        $thead .= '<tr>';
        $thead .= '<th class="text-right" style="font-weight:bold;">SALDO DO MÊS</th>';

        for ($i = 1; $i <= 12; $i++) {
            if ($saldo_mes[$i] < 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#8B0000;">'
                       . number_format($saldo_mes[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_mes[$i] > 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#006400;">'
                       . number_format($saldo_mes[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th class="text-right" style="font-weight:bold;">'
                       . number_format($saldo_mes[$i], 2, ',', '.')
                       . '</th>';
            }

            if ($saldo_mes_nao[$i] < 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#ff8f8f;">'
                       . number_format($saldo_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_mes_nao[$i] > 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#7db87d;">'
                       . number_format($saldo_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th class="text-right" style="font-weight:bold;">'
                       . number_format($saldo_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            }
        }

        $thead .= '<th></th>';
        $thead .= '<th></th>';
        $thead .= '</tr>';

        // SALDO FINAL
        $thead .= '<tr>';
        $thead .= '<th class="text-right" style="font-weight:bold;">SALDO FINAL</th>';

        for ($i = 1; $i <= 12; $i++) {
            if ($saldo_final_mes[$i] < 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#8B0000;">'
                       . number_format($saldo_final_mes[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_final_mes[$i] > 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#006400;">'
                       . number_format($saldo_final_mes[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th class="text-right" style="font-weight:bold;">'
                       . number_format($saldo_final_mes[$i], 2, ',', '.')
                       . '</th>';
            }

            if ($saldo_final_mes_nao[$i] < 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#ff8f8f;">'
                       . number_format($saldo_final_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_final_mes_nao[$i] > 0) {
                $thead .= '<th class="text-right" style="font-weight:bold; color:#7db87d;">'
                       . number_format($saldo_final_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th class="text-right" style="font-weight:bold;">'
                       . number_format($saldo_final_mes_nao[$i], 2, ',', '.')
                       . '</th>';
            }
        }

        $thead .= '<th></th>';
        $thead .= '<th></th>';
        $thead .= '</tr>';

        $thead .= '</thead>';

        $tbody = '';
        $tbody .= '<tbody>';

        foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
            if ($value_tem_valor == "S") {
                $codigo_conta = (int)$key_tem_valor;

                $tbody .= '<tr>';

                if (substr($codigo_conta, 1, 6) == 0) {
                    if (substr($codigo_conta, 0, 1) == 1) {
                        $tbody .= '<td style="background-color:#d8e4bc; color:#000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                        $tbody .= '<td style="background-color:#e6b8b7; color:#000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                        $tbody .= '<td style="background-color:#da9694; color:#000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    }
                } elseif (substr($codigo_conta, 3, 4) == 0) {
                    if (substr($codigo_conta, 0, 1) == 1) {
                        $tbody .= '<td style="background-color:#ebfbde; color:#000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                        $tbody .= '<td style="background-color:#f2dcdb; color:#000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                        $tbody .= '<td style="background-color:#f2dcdb; color:#000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    }
                } else {
                    $tbody .= '<td>' . $descricao_conta[$codigo_conta] . '</td>';
                }

                for ($i = 1; $i <= 13; $i++) {
                    if (substr($codigo_conta, 1, 6) == 0) {
                        if (substr($codigo_conta, 0, 1) == 1) {
                            $tbody .= '<td style="text-align:right; background-color:#d8e4bc; color:#000000;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                            $tbody .= '<td style="text-align:right; background-color:#d8e4bc; color:#000000;">'
                                   . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                            $tbody .= '<td style="text-align:right; background-color:#e6b8b7; color:#000000;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                            $tbody .= '<td style="text-align:right; background-color:#e6b8b7; color:#000000;">'
                                   . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                            $tbody .= '<td style="text-align:right; background-color:#da9694; color:#000000;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                            $tbody .= '<td style="text-align:right; background-color:#da9694; color:#000000;">'
                                   . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        }
                    } elseif (substr($codigo_conta, 3, 4) == 0) {
                        if (substr($codigo_conta, 0, 1) == 1) {
                            $tbody .= '<td style="text-align:right; background-color:#ebfbde; color:#000000;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                            $tbody .= '<td style="text-align:right; background-color:#ebfbde; color:#000000;">'
                                   . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                            $tbody .= '<td style="text-align:right; background-color:#f2dcdb; color:#000000;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                            $tbody .= '<td style="text-align:right; background-color:#f2dcdb; color:#000000;">'
                                   . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                            $tbody .= '<td style="text-align:right; background-color:#f2dcdb; color:#000000;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                            $tbody .= '<td style="text-align:right; background-color:#f2dcdb; color:#000000;">'
                                   . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        }
                    } else {
                        $tbody .= '<td style="text-align:right;">'
                               . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                               . '</td>';
                        $tbody .= '<td style="text-align:right;">'
                               . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.')
                               . '</td>';
                    }
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= '</tbody>';
    }
    // Inicio do else para Realizado
    else if ($tipo_rel==2){
        $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
            WHERE tbl_plano_contas_lixeira=0 
            ORDER BY tbl_plano_contas_codigo_id ASC"); 
        
        WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
            $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
            $descricao_conta[$codigo_conta] = substr($registro_tbl_conta->tbl_plano_contas_descricao, 0, 19);

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
            INNER JOIN contas_receber
                    ON ctr_id=bcr_id
            WHERE bcr_data_pagamento >='$data_inicial' AND
                  bcr_data_pagamento <='$data_final'" . $wcentro_custo_rec . $wlocal_rec . $wconta_rec);

        while ($registro_contas_rec = mysqli_fetch_object($contas_rec)) {
            $mes = (int)substr($registro_contas_rec->bcr_data_pagamento, 5, 2);
            $valor_pago = $registro_contas_rec->bcr_valor_pagamento;

            $valor_credito[$mes] += $valor_pago;

            $fatias = montar_fatias_conta_rateio_ctr($conector, $registro_contas_rec->ctr_id, $registro_contas_rec->ctr_codigo_conta, $valor_pago, $registro_contas_rec->ctr_numero_doc, $registro_contas_rec->ctr_codigo_cliente_fornecedor, $fazendas, $centro_custos);

            foreach ($fatias as $fatia) {
                acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
            }
        } // fim WHILE contas a receber

        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
            INNER JOIN contas_pagar
                    ON ctp_id=bcp_id
            WHERE bcp_data_pagamento >='$data_inicial' AND
                  bcp_data_pagamento <='$data_final'" . $wcentro_custo_pag . $wlocal_pag . $wconta_pag);

        while ($registro_contas_pag = mysqli_fetch_object($contas_pag)) {
            $mes = (int)substr($registro_contas_pag->bcp_data_pagamento, 5, 2);
            $valor_pago = $registro_contas_pag->bcp_valor_pagamento;

            $valor_debito[$mes] += $valor_pago;

            $fatias = montar_fatias_conta_rateio_ctp($conector, $registro_contas_pag->ctp_id, $registro_contas_pag->ctp_codigo_conta, $valor_pago, $registro_contas_pag->ctp_grupo_repeticao, $registro_contas_pag->ctp_numero_doc, $registro_contas_pag->ctp_codigo_fornecedor, $registro_contas_pag->ctp_incluido_em, $fazendas, $centro_custos);

            foreach ($fatias as $fatia) {
                acumular_total_realizado($total_realizado, $tem_valor, $fatia['cod_conta'], $mes, $fatia['valor']);
            }
        } // fim WHILE contas a pagar
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

        $thead = '';

        $thead .= '<thead>';

        // Linha dos meses
        $thead .= '<tr>';
        $thead .= '<th class="text-center"></th>';

        for ($i = 1; $i <= 12; $i++) {
            $thead .= '<th class="text-center">'.$array_mes[$i].'</th>';
        }

        $thead .= '<th class="text-center">Total</th>';
        $thead .= '</tr>';

        // SALDO ANTERIOR
        $thead .= '<tr>';
        $thead .= '<th class="text-right" style="font-weight:bold;">SALDO ANTERIOR</th>';

        for ($i = 1; $i <= 12; $i++) {
            if ($saldo_anterior_mes[$i] < 0) {
                $thead .= '<th style="font-weight:bold; color:#8B0000; text-align:right;">'
                       . number_format($saldo_anterior_mes[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_anterior_mes[$i] > 0) {
                $thead .= '<th style="font-weight:bold; color:#006400; text-align:right;">'
                       . number_format($saldo_anterior_mes[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th style="font-weight:bold; text-align:right;">'
                       . number_format($saldo_anterior_mes[$i], 2, ',', '.')
                       . '</th>';
            }
        }

        $thead .= '<th></th>';
        $thead .= '</tr>';

        // SALDO DO MÊS
        $thead .= '<tr>';
        $thead .= '<th class="text-right" style="font-weight:bold;">SALDO DO MÊS</th>';

        for ($i = 1; $i <= 12; $i++) {
            if ($saldo_mes[$i] < 0) {
                $thead .= '<th style="font-weight:bold; color:#8B0000; text-align:right;">'
                       . number_format($saldo_mes[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_mes[$i] > 0) {
                $thead .= '<th style="font-weight:bold; color:#006400; text-align:right;">'
                       . number_format($saldo_mes[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th style="font-weight:bold; text-align:right;">'
                       . number_format($saldo_mes[$i], 2, ',', '.')
                       . '</th>';
            }
        }

        $thead .= '<th></th>';
        $thead .= '</tr>';

        // SALDO FINAL
        $thead .= '<tr>';
        $thead .= '<th class="text-right" style="font-weight:bold;">SALDO FINAL</th>';

        for ($i = 1; $i <= 12; $i++) {
            if ($saldo_final_mes[$i] < 0) {
                $thead .= '<th style="font-weight:bold; color:#8B0000; text-align:right;">'
                       . number_format($saldo_final_mes[$i], 2, ',', '.')
                       . '</th>';
            } elseif ($saldo_final_mes[$i] > 0) {
                $thead .= '<th style="font-weight:bold; color:#006400; text-align:right;">'
                       . number_format($saldo_final_mes[$i], 2, ',', '.')
                       . '</th>';
            } else {
                $thead .= '<th style="font-weight:bold; text-align:right;">'
                       . number_format($saldo_final_mes[$i], 2, ',', '.')
                       . '</th>';
            }
        }

        $thead .= '<th></th>';
        $thead .= '</tr>';

        $thead .= '</thead>';
        
        $tbody = '';

        $tbody .= '<tbody>';

        foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
            if ($value_tem_valor == "S") {
                $codigo_conta = (int)$key_tem_valor;

                $tbody .= '<tr>';

                // primeira coluna
                if (substr($codigo_conta, 1, 6) == 0) {
                    if (substr($codigo_conta, 0, 1) == 1) {
                        $tbody .= '<td style="background-color:#d8e4bc; color:#000000;">'.$descricao_conta[$codigo_conta].'</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                        $tbody .= '<td style="background-color:#e6b8b7; color:#000000;">'.$descricao_conta[$codigo_conta].'</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                        $tbody .= '<td style="background-color:#da9694; color:#000000;">'.$descricao_conta[$codigo_conta].'</td>';
                    }
                } elseif (substr($codigo_conta, 3, 4) == 0) {
                    if (substr($codigo_conta, 0, 1) == 1) {
                        $tbody .= '<td style="background-color:#ebfbde; color:#000000;">'.$descricao_conta[$codigo_conta].'</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                        $tbody .= '<td style="background-color:#f2dcdb; color:#000000;">'.$descricao_conta[$codigo_conta].'</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                        $tbody .= '<td style="background-color:#f2dcdb; color:#000000;">'.$descricao_conta[$codigo_conta].'</td>';
                    }
                } else {
                    $tbody .= '<td>'.$descricao_conta[$codigo_conta].'</td>';
                }

                // colunas de valores
                for ($i = 1; $i <= 13; $i++) {
                    if (substr($codigo_conta, 1, 6) == 0) {
                        if (substr($codigo_conta, 0, 1) == 1) {
                            $tbody .= '<td style="background-color:#d8e4bc; color:#000000; text-align:right;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                            $tbody .= '<td style="background-color:#e6b8b7; color:#000000; text-align:right;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                            $tbody .= '<td style="background-color:#da9694; color:#000000; text-align:right;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        }
                    } elseif (substr($codigo_conta, 3, 4) == 0) {
                        if (substr($codigo_conta, 0, 1) == 1) {
                            $tbody .= '<td style="background-color:#ebfbde; color:#000000; text-align:right;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                            $tbody .= '<td style="background-color:#f2dcdb; color:#000000; text-align:right;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                            $tbody .= '<td style="background-color:#f2dcdb; color:#000000; text-align:right;">'
                                   . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                                   . '</td>';
                        }
                    } else {
                        $tbody .= '<td style="text-align:right;">'
                               . number_format($total_realizado[$codigo_conta][$i], 2, ',', '.')
                               . '</td>';
                    }
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= '</tbody>';

    } // fim do if $tipo_rel==2 Fim Realizado
    else { // icinio do $tipo_rel==3 Previsto
        $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
            WHERE tbl_plano_contas_lixeira=0 
            ORDER BY tbl_plano_contas_codigo_id ASC");

        WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
            $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
            $descricao_conta[$codigo_conta] = substr($registro_tbl_conta->tbl_plano_contas_descricao, 0, 19);

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

        WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 

            $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
            $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
            $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

            $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

            $previsao_conta = mysqli_query($conector, "SELECT *  FROM tbl_previsao_conta
                WHERE tbl_previsao_conta_codigo='$codigo_conta' AND
                      tbl_previsao_conta_ano = '$ano'" . $wlocal_previsao . $wconta_previsao);
            $num_rows_previsao_conta = mysqli_num_rows($previsao_conta);

            if ($num_rows_previsao_conta!=0){
                WHILE ($reg_conta = mysqli_fetch_object($previsao_conta)) {
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
        } // fim WHILE plano de contas

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

        $thead = '';
        $thead .= '<thead>';

        $thead .= '<tr>';
        $thead .= '<th></th>';

        for ($i = 1; $i <= 12; $i++) {
            $thead .= '<th class="text-right">' . $array_mes[$i] . '</th>';
        }

        $thead .= '<th class="text-center">Total</th>';
        $thead .= '</tr>';

        $thead .= '<tr>';
        $thead .= '<th></th>';

        for ($i = 1; $i <= 13; $i++) {
            $thead .= '<th class="text-right"></th>';
        }

        $thead .= '</tr>';

        $thead .= '</thead>';

        $tbody = '';
        $tbody .= '<tbody>';

        foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
            if ($value_tem_valor == "S") {
                $codigo_conta = (int)$key_tem_valor;

                $tbody .= '<tr>';

                if (substr($codigo_conta, 1, 6) == 0) {
                    if (substr($codigo_conta, 0, 1) == 1) {
                        $tbody .= '<td style="background-color: #d8e4bc; color: #000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                        $tbody .= '<td style="background-color: #e6b8b7; color: #000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                        $tbody .= '<td style="background-color: #da9694; color: #000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    }
                } elseif (substr($codigo_conta, 3, 4) == 0) {
                    if (substr($codigo_conta, 0, 1) == 1) {
                        $tbody .= '<td style="background-color: #ebfbde; color: #000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                        $tbody .= '<td style="background-color: #f2dcdb; color: #000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                        $tbody .= '<td style="background-color: #f2dcdb; color: #000000;">' . $descricao_conta[$codigo_conta] . '</td>';
                    }
                } else {
                    $tbody .= '<td>' . $descricao_conta[$codigo_conta] . '</td>';
                }

                for ($i = 1; $i <= 13; $i++) {
                    if (substr($codigo_conta, 1, 6) == 0) {
                        if (substr($codigo_conta, 0, 1) == 1) {
                            $tbody .= '<td style="text-align: right; background-color: #d8e4bc; color: #000000;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                            $tbody .= '<td style="text-align: right; background-color: #e6b8b7; color: #000000;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                            $tbody .= '<td style="text-align: right; background-color: #da9694; color: #000000;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                        }
                    } elseif (substr($codigo_conta, 3, 4) == 0) {
                        if (substr($codigo_conta, 0, 1) == 1) {
                            $tbody .= '<td style="text-align: right; background-color: #ebfbde; color: #000000;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 2 || substr($codigo_conta, 0, 1) == 3) {
                            $tbody .= '<td style="text-align: right; background-color: #f2dcdb; color: #000000;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                        } elseif (substr($codigo_conta, 0, 1) == 4 || substr($codigo_conta, 0, 1) == 5) {
                            $tbody .= '<td style="text-align: right; background-color: #f2dcdb; color: #000000;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                        }
                    } else {
                        $tbody .= '<td style="text-align: right;">' . number_format($total_nao_realizado[$codigo_conta][$i], 2, ',', '.') . '</td>';
                    }
                }

                $tbody .= '</tr>';
            }
        }

        $tbody .= '</tbody>';
    } //fim do if $tipo_rel==2 Fim previsto

    echo '<script type="text/javascript">
          $("#aguardar").modal("hide");
          </script>';
?>

    <div class="row">
        <div class="col-md-12" style="padding-right:0; padding-left:10;">
            <div style="width:100%;">
                <table id="tabela_analise_previsto_realizado"
                       class="table table-advance table-hover table-borderless<?php echo ($tipo_rel==1) ? ' modo-combinado' : ''; ?>"
                       style="font-size:11px;">
                    <?php echo $thead; ?>
                    <?php echo $tbody; ?>
                </table>
            </div>
        </div>
    </div>                                </div>  <!--fim container -->
                            </div> <!--tab-content -->
                        </div> <!--panel-body -->
                    </div> <!--panel -->      
                </div> <!--col-lg-12 2-->
            </div> <!--row -->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Análise Contas Previsto/Realizao</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Análise Contas Previsto/Realizao - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spINNER fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- wrapper -->
    </section><!--main-content -->

    <div class="text-center">
         <div class="credits">
             <font size="2"><p style="color:#C0C0C0">Copyright &copy; Agrolandes 2023</p></font>
         </div>
     </div>

    </section> <!-- container section start end -->
      
    <script src="js/jquery.js?<?php echo Versao; ?>"></script>
    <script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
    <script src="js/scripts.js?<?php echo Versao; ?>"></script>
    <script src="js/relatorios_financeiros.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
    <script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
    </script>

    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
    " charset="utf-8" type="text/javascript" >
    </script>
    <script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

<script>
    var table;

    $(document).ready(function() {
        // No modo Realizado/Previsto combinado a tabela tem 26 colunas de dados (2 por
        // mês); o fixedColumns clona o cabeçalho num elemento à parte para "congelar" a
        // 1ª coluna, e com tantas colunas estreitas essa cópia perde a sincronia de
        // largura com o corpo da tabela ao rolar, desalinhando cabeçalho x dados. Os
        // outros modos (13 colunas) não têm esse problema, então o fixedColumns fica
        // desativado só quando tipo_rel==1.
        var tipoRelInicial = $('#tipo_rel').val();
        var dtOptions = {
            scrollY: calcularScrollTabela(),
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            searching: true,
            ordering: false,
            info: false,
            autoWidth: true,
            language: {
                sSearch: "Buscar na lista:",
                zeroRecords: "Nada encontrado",
                info: "Registros encontrados: _END_ ",
                infoEmpty: "Nenhum registro disponível",
                infoFiltered: "(filtrado de _MAX_ registros no total)"
            }
        };

        if (tipoRelInicial != '1') {
            dtOptions.fixedColumns = { heightMatch: 'none' };
        }

        table = $('#tabela_analise_previsto_realizado').DataTable(dtOptions);

        // No modo combinado (27 colunas), o DataTables clona o <colgroup> da tabela
        // original só na tabela de scroll do CORPO — a tabela de scroll do CABEÇALHO
        // fica sem colgroup e volta a calcular a largura das colunas sozinha (o mesmo
        // desalinhamento que o colgroup foi criado para evitar). Por isso o colgroup
        // do corpo é copiado manualmente para o cabeçalho sempre que a tabela é
        // redesenhada/redimensionada.
        function sincronizarColgroupCabecalho() {
            if (tipoRelInicial != '1') return;
            var $wrapper = $('#tabela_analise_previsto_realizado_wrapper');
            var bodyColgroup = $wrapper.find('.dataTables_scrollBody table colgroup')[0];
            var headTable = $wrapper.find('.dataTables_scrollHead table')[0];
            if (!bodyColgroup || !headTable) return;
            var headColgroup = headTable.querySelector('colgroup');
            if (headColgroup) headTable.removeChild(headColgroup);
            headTable.insertBefore(bodyColgroup.cloneNode(true), headTable.firstChild);
        }

        sincronizarColgroupCabecalho();

        setTimeout(function () {
            table.columns.adjust().draw();
            sincronizarColgroupCabecalho();
        }, 100);

        $(window).on('resize', function () {
            setTimeout(function () {
                table.settings()[0].oScroll.sY = calcularScrollTabela();
                $('.dataTables_scrollBody').css('max-height', calcularScrollTabela());
                $('.dataTables_scrollBody').css('height', calcularScrollTabela());
                table.columns.adjust().draw();
                sincronizarColgroupCabecalho();
            }, 100);
        });
    });

    function calcularScrollTabela() {
        var tipoRel = $('#tipo_rel').val();
        var alturaJanela = $(window).height();

        var desconto;

        if (tipoRel == 1 || tipoRel == 2) {
            desconto = 430;
        } else if (tipoRel == 3) {
            desconto = 350; // antes estava 280
        } else {
            desconto = 400;
        }

        var alturaTabela = alturaJanela - desconto;

        if (alturaTabela < 200) alturaTabela = 200;
        if (alturaTabela > 500) alturaTabela = 500;

        return alturaTabela + 'px';
    }
</script>

</body>
</html>

