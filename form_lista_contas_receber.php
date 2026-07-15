<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_data = $_REQUEST["tipo_data"];
    $array_cliente = $_REQUEST["array_cliente"];
    $array_fazenda = $_REQUEST["array_fazenda"];
    $array_cc = $_REQUEST["array_cc"];

    $array_conta = $_REQUEST["array_conta"];
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
        $wconta = " AND ctr_codigo_conta IN(";
        $wconta.= $conta;
        $wconta.= ")";
    }

    $wcliente = '';
    if ($array_cliente != '') {
        $cliente_ids = [];
        foreach (explode(',', $array_cliente) as $item) {
            $id = intval(trim($item));
            if ($id > 0) $cliente_ids[] = $id;
        }
        if (!empty($cliente_ids)) {
            $wcliente = " AND ctr_codigo_cliente_fornecedor IN(" . implode(',', $cliente_ids) . ")";
        }
    }

    $wfazenda = '';
    if ($array_fazenda != '') {
        $fazenda_ids = [];
        foreach (explode(',', $array_fazenda) as $item) {
            $id = intval(trim($item));
            if ($id > 0) $fazenda_ids[] = $id;
        }
        if (!empty($fazenda_ids)) {
            $ids_str = implode(',', $fazenda_ids);
            $wfazenda = " AND (ctr_codigo_fazenda IN($ids_str) OR (ctr_codigo_fazenda IS NULL AND ctr_id IN (SELECT rc_ctr_id FROM tbl_ctr_rateio WHERE rc_codigo_local IN ($ids_str))))";
        }
    }

    $wcc = '';
    if ($array_cc != '') {
        $cc_ids = [];
        foreach (explode(',', $array_cc) as $item) {
            $id = intval(trim($item));
            if ($id > 0) $cc_ids[] = $id;
        }
        if (!empty($cc_ids)) {
            $wcc = " AND ctr_codigo_c_custo IN(" . implode(',', $cc_ids) . ")";
        }
    }

    @ session_start();
    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $_SESSION['data_inicio_ctr']=$data_inicial;
    $_SESSION['data_fim_ctr']=$data_final;
    $_SESSION['tipo_data_ctr']=$tipo_data;
    $_SESSION['razao_nome_ctr']=$array_cliente;
    $_SESSION['lista_ctr']='S';
    $_SESSION['codigo_c_custo_ctr']=$array_cc;
    $_SESSION['codigo_local_ctr']=$array_fazenda;
    $_SESSION['codigo_conta_ctr']=$array_conta;
    $_SESSION['periodo_label_ctr'] = isset($_REQUEST["periodo_label"]) ? $_REQUEST["periodo_label"] : '';
    $_SESSION['limpa_conta_ctr'] = '';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Fazendas Agrolandes</title>

  <!-- Bootstrap CSS -->
  <style>
    .ctr-card-total {
        border: 1px solid #c8c8c8;
        height: 52px;
        padding-top: 4px;
        text-align: center;
        color: #688a7e;
        font-size: 11px;
        font-weight: bold;
        cursor: pointer;
        transition: all .2s ease;
        background: #fff;
    }
    .ctr-card-total:hover {
        background: #f8f9fa;
    }
    .ctr-card-total .valor {
        font-size: 14px;
        margin-top: 1px;
        font-weight: 400;
        color: #555;
    }
    .ctr-card-total.ativo .valor {
        font-weight: 700;
        font-size: 17px;
    }
    .ctr-card-total.ativo.vermelho { border-top: 2px solid #d9534f; }
    .ctr-card-total.ativo.azul     { border-top: 2px solid #4a90e2; }
    .ctr-card-total.ativo.verde    { border-top: 2px solid #5cb85c; }
    .ctr-texto-vermelho { color: #d9534f !important; }
    .ctr-texto-azul     { color: #005ecb !important; }
    .ctr-texto-verde    { color: #00b050 !important; }
  </style>

</head>

<body>
	<section class="panel lista_contas">

        <table class="table table-striped table-advance table-hover" id="tabela_contas_receber"
         width="100%" style="font-size: 10px">

            <tbody>
                <?php
                    $criterio="";

                    if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="V" ){
                        $criterio =
                        " WHERE ctr_lixeira=0 and
                                ctr_data_vencimento >='$data_inicial' and
                                ctr_data_vencimento <='$data_final'" . $wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY ctr_situacao, ctr_data_vencimento, ctr_numero_doc ASC";
                    }
                    else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="E"){
                        $criterio =
                        " WHERE ctr_lixeira=0 and
                                ctr_data_emissao >='$data_inicial' and
                                ctr_data_emissao <='$data_final'" . $wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY ctr_situacao, ctr_data_emissao, ctr_numero_doc ASC";
                    }
                    else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="P"){
                        $criterio =
                        " WHERE bcr_data_pagamento >='$data_inicial' and
                                bcr_data_pagamento <='$data_final' " . $wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY bcr_situacao, bcr_data_pagamento, bcr_numero_doc ASC";
                    }
                    else if ($data_inicial==0 && $data_final==0){
                        $criterio = " WHERE ctr_lixeira=0" .$wcliente . $wfazenda . $wcc . $wconta .
                        " ORDER BY ctr_situacao, ctr_data_emissao, ctr_numero_doc ASC";
                        }


                    if ($criterio=='') {
                        mysqli_close($conector);
                        exit;
                    }
                    else {
                        if ($tipo_data=="P"){
                            $ssql = "SELECT * FROM baixa_contas_receber
                                INNER JOIN contas_receber
                                        ON bcr_id=ctr_id
                                LEFT JOIN tbl_plano_contas
                                        ON tbl_plano_contas_codigo_id=ctr_codigo_conta
                                LEFT JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctr_codigo_fazenda
                                " . $criterio;
                        }
                        else {
                            $ssql = "SELECT * FROM contas_receber
                                LEFT JOIN tbl_plano_contas
                                        ON tbl_plano_contas_codigo_id=ctr_codigo_conta
                                LEFT JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctr_codigo_fazenda
                                " . $criterio;
                        }

                        $rs = mysqli_query($conector, $ssql);

                        // Pré-query: documentos com anexo (lookup O(1) no loop)
                        $docs_com_anexo  = [];
                        $ctridsComAnexo  = [];
                        $rs_anx_docs = mysqli_query($conector,
                            "SELECT DISTINCT c.ctr_numero_doc, c.ctr_codigo_cliente_fornecedor
                             FROM tbl_ctr_anexos a
                             INNER JOIN contas_receber c ON c.ctr_id = a.anexo_ctr_id
                             WHERE c.ctr_numero_doc IS NOT NULL AND c.ctr_numero_doc != ''");
                        if ($rs_anx_docs) {
                            while ($ra = mysqli_fetch_object($rs_anx_docs)) {
                                $docs_com_anexo[$ra->ctr_numero_doc . '|' . $ra->ctr_codigo_cliente_fornecedor] = true;
                            }
                        }
                        $rs_anx_ctrs = mysqli_query($conector,
                            "SELECT DISTINCT a.anexo_ctr_id
                             FROM tbl_ctr_anexos a
                             INNER JOIN contas_receber c ON c.ctr_id = a.anexo_ctr_id
                             WHERE c.ctr_numero_doc = '' OR c.ctr_numero_doc IS NULL");
                        if ($rs_anx_ctrs) {
                            while ($ra = mysqli_fetch_object($rs_anx_ctrs)) {
                                $ctridsComAnexo[intval($ra->anexo_ctr_id)] = true;
                            }
                        }

                        $total_geral = 0;
                        $total_pagos = 0;
                        $total_vencidos = 0;
                        $total_vencem_hoje = 0;
                        $total_avencer = 0;
                        $data_sistema = date("Y-m-d");

                        while ($registro_ctr = mysqli_fetch_object($rs)){
                            if ($tipo_data=="P"){
                                $ctr_id = $registro_ctr->ctr_id;
                                $numero_doc = $registro_ctr->bcr_numero_doc;
                                $numero_parcela = $registro_ctr->bcr_parcela;
                                $codigo_cliente = $registro_ctr->bcr_codigo_cliente_fornecedor;
                                $razao = tirarAcentos($registro_ctr->bcr_nome_cliente);
                                $situacao = $registro_ctr->bcr_situacao;

                                $data_emissao = $registro_ctr->ctr_data_emissao;
                                $data_vencimento = $registro_ctr->ctr_data_vencimento;
                                $vlr_parcela = $registro_ctr->ctr_valor_parcela + $registro_ctr->ctr_valor_juros + $registro_ctr->ctr_valor_acrescimo - $registro_ctr->ctr_valor_desconto;
                                $codigo_forma_pagto = $registro_ctr->ctr_codigo_forma_recebimento;
                                $codigo_conta_pagto = $registro_ctr->ctr_codigo_conta_recebimento;
                                $doc_parcela = str_pad($numero_doc, 9, "0", STR_PAD_LEFT);
                                $emissao_edi = new DateTime($data_emissao);
                                $vencimento_edi = new DateTime($data_vencimento);

                                $pagamentos = "select * from baixa_contas_receber where bcr_id='$ctr_id'";
                                $contas_baixadas = mysqli_query($conector, $pagamentos);
                                $num_rows_contas = mysqli_num_rows($contas_baixadas);
                                $total_pago=0;

                                if ($num_rows_contas!=0){
                                    while ($fila_baixada = mysqli_fetch_object($contas_baixadas)) {
                                        $valor_pagamento = $fila_baixada->bcr_valor_pagamento;
                                        $total_pago+= $valor_pagamento;
                                        $data_pagamento = $fila_baixada->bcr_data_pagamento;
                                        $pagamento_edi = new DateTime($data_pagamento);
                                    }
                                }
                                else {
                                    $pagamento_edi = "";
                                }
                            }
                            else {
                                $ctr_id = $registro_ctr->ctr_id;
                                $numero_doc = $registro_ctr->ctr_numero_doc;
                                $numero_parcela = $registro_ctr->ctr_parcela;
                                $codigo_cliente = $registro_ctr->ctr_codigo_cliente_fornecedor;
                                $razao = tirarAcentos($registro_ctr->ctr_nome_cliente);
                                $data_emissao = $registro_ctr->ctr_data_emissao;
                                $data_vencimento = $registro_ctr->ctr_data_vencimento;
                                $vlr_parcela = $registro_ctr->ctr_valor_parcela + $registro_ctr->ctr_valor_juros + $registro_ctr->ctr_valor_acrescimo - $registro_ctr->ctr_valor_desconto;
                                $codigo_forma_pagto = $registro_ctr->ctr_codigo_forma_recebimento;
                                $codigo_conta_pagto = $registro_ctr->ctr_codigo_conta_recebimento;
                                $situacao = $registro_ctr->ctr_situacao;

                                $contas_baixadas = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                    WHERE bcr_id='$ctr_id'");

                                $num_rows_contas = mysqli_num_rows($contas_baixadas);
                                $total_pago=0;

                                if ($num_rows_contas!=0){
                                    while ($fila_baixada = mysqli_fetch_object($contas_baixadas)) {
                                        $valor_pagamento = $fila_baixada->bcr_valor_pagamento;
                                        $total_pago = $total_pago + $valor_pagamento;
                                        $data_pagamento = $fila_baixada->bcr_data_pagamento;
                                        $pagamento_edi = new DateTime($data_pagamento);
                                    }
                                }
                                else {
                                    $pagamento_edi = "";
                                }

                                $doc_parcela = str_pad($numero_doc, 9, "0", STR_PAD_LEFT);
                                $emissao_edi = new DateTime($data_emissao);
                                $vencimento_edi = new DateTime($data_vencimento);
                            }

                            $tem_rateio  = is_null($registro_ctr->ctr_codigo_fazenda);
                            $icon_rateio = '';

                            if ($tem_rateio) {
                                // Localiza o primeiro ctr_id do documento (onde salvar_rateio_ctr gravou)
                                $num_doc_esc = mysqli_real_escape_string($conector, $numero_doc);
                                $cli_esc     = intval($codigo_cliente);
                                $rs_prim = mysqli_query($conector,
                                    "SELECT MIN(ctr_id) AS primeiro_id FROM contas_receber
                                     WHERE ctr_numero_doc = '$num_doc_esc'
                                       AND ctr_codigo_cliente_fornecedor = '$cli_esc'
                                       AND ctr_codigo_fazenda IS NULL");
                                $row_prim     = mysqli_fetch_object($rs_prim);
                                $primeiro_ctr = $row_prim ? (int)$row_prim->primeiro_id : intval($ctr_id);

                                // Locais distintos para coluna Local
                                $rs_locais = mysqli_query($conector,
                                    "SELECT rc_nome_local FROM tbl_ctr_rateio
                                     WHERE rc_ctr_id = '$primeiro_ctr'
                                     GROUP BY rc_codigo_local, rc_nome_local
                                     ORDER BY MIN(rc_id) ASC");
                                $total_locais = mysqli_num_rows($rs_locais);
                                $first_local  = mysqli_fetch_object($rs_locais);
                                $desc_fazenda = htmlspecialchars($first_local ? $first_local->rc_nome_local : 'Rateio');
                                if ($total_locais > 1) {
                                    $desc_fazenda .= ' <span style="color:#337ab7;font-weight:600">+' . ($total_locais - 1) . '</span>';
                                }

                                // Contas distintas para coluna Conta
                                $rs_contas = mysqli_query($conector,
                                    "SELECT rc_nome_conta FROM tbl_ctr_rateio
                                     WHERE rc_ctr_id = '$primeiro_ctr'
                                       AND rc_nome_conta IS NOT NULL AND rc_nome_conta != ''
                                     GROUP BY rc_codigo_conta, rc_nome_conta
                                     ORDER BY MIN(rc_id) ASC");
                                $total_contas = mysqli_num_rows($rs_contas);
                                $first_conta  = mysqli_fetch_object($rs_contas);
                                $desc_conta   = htmlspecialchars($first_conta ? $first_conta->rc_nome_conta : 'Rateio');
                                if ($total_contas > 1) {
                                    $desc_conta .= ' <span style="color:#337ab7;font-weight:600">+' . ($total_contas - 1) . '</span>';
                                }

                                // Ícone para abrir modal de rateio
                                $icon_rateio = ' <button type="button" onclick="toggleRateioCtr(' . intval($ctr_id) . ')" data-toggle="tooltip" data-placement="right" title="Ver distribuição do rateio" style="background:none;border:none;padding:0 2px;cursor:pointer;color:#337ab7;font-size:12px;"><i class="fas fa-info-circle"></i></button>';

                            } elseif (is_null($registro_ctr->ctr_codigo_conta)) {
                                // Formato legado: fazenda preenchida mas conta via rateio
                                $num_doc_esc  = mysqli_real_escape_string($conector, $numero_doc);
                                $parcela_esc  = mysqli_real_escape_string($conector, $numero_parcela);
                                $fazenda_esc  = mysqli_real_escape_string($conector, ltrim($registro_ctr->ctr_codigo_fazenda, '0'));
                                $rs_rat = mysqli_query($conector,
                                    "SELECT rc_nome_conta FROM tbl_ctr_rateio
                                     WHERE rc_codigo_local = '$fazenda_esc'
                                       AND rc_ctr_id IN (
                                           SELECT ctr_id FROM contas_receber
                                           WHERE ctr_numero_doc = '$num_doc_esc'
                                             AND ctr_parcela    = '$parcela_esc'
                                             AND ctr_codigo_conta IS NULL
                                       )
                                     ORDER BY rc_id ASC");
                                $total_rat = mysqli_num_rows($rs_rat);
                                $first_rat = mysqli_fetch_object($rs_rat);
                                $desc_conta   = $first_rat ? $first_rat->rc_nome_conta : 'Rateio';
                                if ($total_rat > 1) {
                                    $desc_conta .= ' <span style="color:#337ab7;font-weight:600">+' . ($total_rat - 1) . '</span>';
                                }
                                $desc_fazenda = $registro_ctr->tbl_pessoa_nome;
                            } else {
                                $desc_conta   = $registro_ctr->tbl_plano_contas_descricao;
                                $desc_fazenda = $registro_ctr->tbl_pessoa_nome;
                            }

                            $parcela_display = $numero_parcela;

                            // Ícone de anexo — aparece se o documento tem arquivos/links em tbl_ctr_anexos
                            $tem_anexo = !empty($numero_doc)
                                ? isset($docs_com_anexo[$numero_doc . '|' . $codigo_cliente])
                                : isset($ctridsComAnexo[intval($ctr_id)]);
                            $icon_anexo = '';
                            if ($tem_anexo) {
                                $nd_js  = addslashes($numero_doc);
                                $dd_js  = addslashes(!empty($numero_doc) ? (ltrim($numero_doc, '0') ?: '0') : $doc_parcela);
                                $cli_js = intval($codigo_cliente);
                                $id_js  = intval($ctr_id);
                                $icon_anexo = '<a class="btn" style="font-size:11px;" href="#"'
                                    . ' onclick="abrirModalAnexosCtr(\'' . $nd_js . '\',' . $cli_js . ',' . $id_js . ',\'' . $dd_js . '\');return false;"'
                                    . ' data-toggle="tooltip" data-placement="left" title="Ver Anexos/Links">'
                                    . '<i class="fas fa-paperclip" style="color:#337ab7;"></i></a>';
                            }

                            // Totais para os cards
                            if ($situacao != "P"){
                                if ($data_vencimento < $data_sistema) {
                                    $total_vencidos = $total_vencidos + $vlr_parcela - $total_pago;
                                } elseif ($data_vencimento == $data_sistema) {
                                    $total_vencem_hoje = $total_vencem_hoje + $vlr_parcela - $total_pago;
                                } else {
                                    $total_avencer = $total_avencer + $vlr_parcela - $total_pago;
                                }
                            }
                            if ($situacao == "P" || $situacao == "C"){
                                $total_pagos += $total_pago;
                            }
                            $total_geral += $vlr_parcela;

                            // Define categoria da linha para filtro client-side
                            if ($situacao == "P" || $situacao == "C") {
                                $categoria_linha = "pago";
                            } elseif ($data_vencimento < $data_sistema) {
                                $categoria_linha = "vencido";
                            } elseif ($data_vencimento == $data_sistema) {
                                $categoria_linha = "vencem_hoje";
                            } else {
                                $categoria_linha = "a_vencer";
                            }

                            echo "<tr data-categoria='" . $categoria_linha . "' data-ctr-id='" . $ctr_id . "'>";

                            if ($situacao=="P"){
                                echo "<td width='2%'><i class='btn icon_check' style='color:green' data-toggle='tooltip' data-placement='left' title='Pago' ></i></td>";
                                echo "<td width='4%'>".$doc_parcela."</td>";
                                echo "<td width='2%'>".$parcela_display."</td>";
                                echo "<td width='14%'>".$razao."</td>";
                                echo "<td width='14%'>".$desc_fazenda."</td>";
                                echo "<td width='15%'>".$desc_conta."</td>";
                                echo "<td width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".number_format($vlr_parcela, 2, ",", ".").$icon_rateio."</td>";
                                if ($pagamento_edi=='') {
                                    echo "<td width='6%'></td>";
                                    echo "<td width='6%'></td>";
                                }
                                else {
                                    echo "<td width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                }

                                if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4){
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_receber_editar.php?id_ctr=".$ctr_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                    echo $icon_anexo;
                                    echo "</div>";
                                    echo "</td>";
                                }
                                else {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_receber_editar.php?id=".$numero_doc."&parcela=".$numero_parcela."&id_ctr=".$ctr_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                    echo $icon_anexo;
                                    echo "</div>";
                                    echo "</td>";
                                }
                            }
                            else {
                                if ($data_vencimento<$data_sistema) {
                                    if ($numero_doc!=0 && $numero_doc!='') {
                                        echo "<td width='2%'>
                                            <input type='checkbox' name='id_ctr' class='checkbox1' data-toggle='tooltip' data-placement='top'
                                               title='Seleciona esse registro para baixar'
                                             onClick='somar_total_para_baixar()' value='" . $ctr_id . "'></td>";
                                    }
                                    else {
                                        echo "<td style='color:#B22222' width='2%'></td>";
                                    }
                                    echo "<td width='4%' style='color:#B22222'>".$doc_parcela."</td>";
                                    echo "<td width='2%' style='color:#B22222'>".$parcela_display."</td>";
                                    echo "<td width='14%' style='color:#B22222'>".$razao."</td>";
                                    echo "<td width='14%' style='color:#B22222'>".$desc_fazenda."</td>";
                                    echo "<td width='15%' style='color:#B22222'>".$desc_conta."</td>";
                                    echo "<td width='6%' style='color:#B22222'>".$emissao_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%' style='color:#B22222'>".$vencimento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%' style='color:#B22222'>".number_format($vlr_parcela, 2, ",", ".").$icon_rateio."</td>";

                                    if ($situacao=="P" || $situacao=="C"){
                                        echo "<td width='6%' style='color:#B22222'>".$pagamento_edi->format('d/m/Y')."</td>";
                                        echo "<td width='6%' style='color:#B22222'>".number_format($total_pago, 2, ",", ".")."</td>";
                                    }
                                    else {
                                        echo "<td width='6%'></td>";
                                        echo "<td width='6%'></td>";
                                    }

                                    if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4){
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_receber_editar.php?id_ctr=".$ctr_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$numero_doc}\",\"{$numero_parcela}\",\"{$ctr_id}\")' ></i></a>";

                                        echo '<a class="btn" style="font-size: 11px;" href="#"
                                                  data-toggle="modal"
                                                  data-target="#modal_baixar"
                                                  data-wid="'.$ctr_id.'"
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wvalor="'.$vlr_parcela.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wcontapag="'.$codigo_conta_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';

                                        echo $icon_anexo;
                                        echo "</div>";
                                    }
                                    else {
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_receber_editar.php?id=".$numero_doc."&parcela=".$numero_parcela."&id_ctr=".$ctr_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo '<a class="btn" style="font-size: 11px;" href="#"
                                                  data-toggle="modal"
                                                  data-target="#modal_baixar"
                                                  data-wid="'.$ctr_id.'"
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wvalor="'.$vlr_parcela.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wcontapag="'.$codigo_conta_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';
                                        echo $icon_anexo;
                                        echo "</div>";
                                    }
                                    echo "</td>";
                                }
                                else {
                                    if ($numero_doc!=0 && $numero_doc!='') {
                                        echo "<td width='2%'>
                                            <input type='checkbox' name='id_ctr' class='checkbox1'
                                             data-toggle='tooltip' data-placement='top'
                                             title='Seleciona esse registro para baixar'
                                             onClick='somar_total_para_baixar()' value='" . $ctr_id . "'></td>";
                                    }
                                    else {
                                        echo "<td width='2%'></td>";
                                    }
                                    echo "<td width='4%'>".$doc_parcela."</td>";
                                    echo "<td width='2%'>".$parcela_display."</td>";
                                    echo "<td width='14%'>".$razao."</td>";
                                    echo "<td width='14%'>".$desc_fazenda."</td>";
                                    echo "<td width='15%'>".$desc_conta."</td>";
                                    echo "<td width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".number_format($vlr_parcela, 2, ",", ".").$icon_rateio."</td>";

                                    if ($situacao=="P" || $situacao=="C"){
                                        echo "<td width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                        echo "<td width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                    }
                                    else {
                                        echo "<td width='6%'></td>";
                                        echo "<td width='6%'></td>";
                                    }

                                    if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4){
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_receber_editar.php?id_ctr=".$ctr_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$numero_doc}\",\"{$numero_parcela}\",\"{$ctr_id}\")' ></i></a>";

                                        echo '<a class="btn" style="font-size: 11px;" href="#"
                                                  data-toggle="modal"
                                                  data-target="#modal_baixar"
                                                  data-wid="'.$ctr_id.'"
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wvalor="'.$vlr_parcela.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wcontapag="'.$codigo_conta_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';

                                        echo $icon_anexo;
                                        echo "</div>";
                                    }
                                    else {
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_receber_editar.php?id=".$numero_doc."&parcela=".$numero_parcela."&id_ctr=".$ctr_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo '<a class="btn" style="font-size: 11px;" href="#"
                                                  data-toggle="modal"
                                                  data-target="#modal_baixar"
                                                  data-wid="'.$ctr_id.'"
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wvalor="'.$vlr_parcela.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wcontapag="'.$codigo_conta_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';
                                        echo $icon_anexo;
                                        echo "</div>";
                                    }
                                    echo "</td>";
                                }
                            }
                            echo "</tr>";
                        }
                    }

                    mysqli_close($conector);

                    echo '
                    <script type="text/javascript">
                        $("#aguardar").modal("hide");
                    </script>
                    ';
                ?>
            </tbody>

            <!-- Cards de totais — inseridos pelo DataTable initComplete -->
            <div id="ctr-cards-source" style="display:none">
                <div style="display:flex; width:100%; margin-bottom:8px;">
                    <div style="width:20%">
                        <div class="ctr-card-total vermelho" data-filtro="vencidos">
                            <div>Vencidos R$</div>
                            <div class="valor ctr-texto-vermelho"><?php echo number_format($total_vencidos, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctr-card-total vermelho" data-filtro="vencem_hoje">
                            <div>Vencem Hoje R$</div>
                            <div class="valor ctr-texto-vermelho"><?php echo number_format($total_vencem_hoje, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctr-card-total azul" data-filtro="a_vencer">
                            <div>A Vencer R$</div>
                            <div class="valor ctr-texto-azul"><?php echo number_format($total_avencer, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctr-card-total verde" data-filtro="pagos">
                            <div>Recebidos R$</div>
                            <div class="valor ctr-texto-verde"><?php echo number_format($total_pagos, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctr-card-total azul" data-filtro="total_periodo">
                            <div>Total do Período R$</div>
                            <div class="valor ctr-texto-azul"><?php echo number_format($total_geral, 2, ",", "."); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <thead>
                <tr>
                <tr>
                    <div class="row col-md-8 confirmar_baixa_selecionados" hidden="">
                        <div class="form-group col-md-3">
                            <button type="button" class="btn btn-primary pull-left" id="baixar_selecionadas"
                            onClick="modal_baixar()" >Baixar Selecionados</button>
                        </div>
                    </div>
                </tr>

                <tr>
                    <th>
                        <input type="checkbox" class='checkbox1' id="seleciona_todos_somar" data-toggle="tooltip" data-placement="top" title="Selecionar Todos os registros para baixar">
                    </th>
                    <th> Documento</th>
                    <th> Parcela</th>
                    <th> Cliente</th>
                    <th> Local</th>
                    <th> Conta</th>
                    <th> Emissão</th>
                    <th> Vencimento</th>
                    <th> Valor Parcela</th>
                    <th> Pagamento</th>
                    <th> Vlr Pago</th>
                    <th> <i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>
        </table>

    </section>

    <script src="js/contas_receber.js?<?php echo filemtime(__DIR__.'/js/contas_receber.js'); ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html>
