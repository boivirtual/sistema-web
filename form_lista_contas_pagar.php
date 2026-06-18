<?php
    function gerar_conta_sintetica($matriz_itens, $quantidade_itens, $conector) {
        $conta= array();
        $wconta = '';

        for ($i=0; $i < $quantidade_itens; $i++) {
            if ($matriz_itens[$i]!='') {

                $tbl_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas 
                    WHERE tbl_plano_contas_codigo_id>'$matriz_itens[$i]' AND 
                          tbl_plano_contas_lixeira=0");

                while ($reg_conta = mysqli_fetch_object($tbl_contas)){
                    $id_conta = $reg_conta->tbl_plano_contas_codigo_id;

                    if (substr($id_conta, 0,3) ==
                        substr($matriz_itens[$i], 0,3) && substr($id_conta, 3,4)!='0000') {
                        $conta[]=$id_conta;
                    }
                }
            }       
        }

        $conta = implode(',', $conta);

        $wconta = " AND ctp_codigo_conta IN(";
        $wconta.= $conta;
        $wconta.= ")";

        return $wconta;
    }

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_data = $_REQUEST["tipo_data"];
    $array_fornecedor = $_REQUEST["array_fornecedor"];
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
        $wconta = " AND ctp_codigo_conta IN(";
        $wconta.= $conta;
        $wconta.= ")";
    }

    $fornecedor= array();
    $matriz_itens = explode(",", $array_fornecedor);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fornecedor[$i]=$matriz_itens[$i];
    }

    $fornecedor = implode(',', $fornecedor);
    $fornecedor = substr($fornecedor,0, -1);

    $wfornecedor = '';

    if ($array_fornecedor!='') {
        $wfornecedor = " AND ctp_codigo_fornecedor IN(";
        $wfornecedor.= $fornecedor;
        $wfornecedor.= ")";
    }

    $fazenda= array();
    $matriz_itens = explode(",", $array_fazenda);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $fazenda[$i]=$matriz_itens[$i];
    }

    $fazenda = implode(',', $fazenda);
    $fazenda = substr($fazenda,0, -1);

    $wfazenda = '';

    if ($array_fazenda!='') {
        $wfazenda = " AND ctp_codigo_fazenda IN(";
        $wfazenda.= $fazenda;
        $wfazenda.= ")";
    }

    $cc= array();
    $matriz_itens = explode(",", $array_cc);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $cc[$i]=$matriz_itens[$i];
    }

    $cc = implode(',', $cc);
    $cc = substr($cc,0, -1);

    $wcc = '';

    if ($array_cc!='') {
        $wcc = " AND ctp_codigo_centro_custos IN(";
        $wcc.= $cc;
        $wcc.= ")";
    }

    $conta= array();
    $conta_inicial='2000000';
    $conta_final='5999999';
    $tipo_conta = 'A';

/*    if ($array_conta=='') {
        $conta_inicial='2000000';
        $conta_final='5999999';
        $tipo_conta = 'S';
    }
    else if (substr($matriz_itens[0], 1,6)=='000000') {
        $conta_inicial = $matriz_itens[0];
        $conta_final = substr($matriz_itens[0], 0,1).'999999';
        $tipo_conta = 'S';
    }
    else if (substr($matriz_itens[0], 3,4)=='0000') {
        $wconta = gerar_conta_sintetica($matriz_itens, $quantidade_itens, $conector);
        $tipo_conta = 'A';
    } 
    else if (substr($matriz_itens[0], 3,4)!='0000') {
        $tipo_conta = 'A';
        for($i=0; $i < $quantidade_itens; $i++) {
            $conta[$i]=$matriz_itens[$i];
        }

        $conta = implode(',', $conta);
        $conta = substr($conta,0, -1);

        $wconta = '';

        if ($array_conta!='') {
            $wconta = " AND ctp_codigo_conta IN(";
            $wconta.= $conta;
            $wconta.= ")";
        }
    }
*/
    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $_SESSION['data_inicio_ctp']=$data_inicial;
    $_SESSION['data_fim_ctp']=$data_final;
    $_SESSION['tipo_data_ctp']=$tipo_data;
    $_SESSION['razao_nome_ctp']=$array_fornecedor;
    $_SESSION['lista_ctp']='S'; 
    $_SESSION['codigo_c_custo_ctp']=$array_cc; 
    $_SESSION['codigo_local_ctp']=$array_fazenda; 
    $_SESSION['codigo_conta_ctp']=$array_conta; 

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
    .ctp-card-total {
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
    .ctp-card-total:hover {
        background: #f8f9fa;
    }
    .ctp-card-total .valor {
        font-size: 14px;
        margin-top: 1px;
        font-weight: 400;
        color: #555;
    }
    .ctp-card-total.ativo .valor {
        font-weight: 700;
        font-size: 17px;
    }
    .ctp-card-total.ativo.vermelho { border-top: 2px solid #d9534f; }
    .ctp-card-total.ativo.azul     { border-top: 2px solid #4a90e2; }
    .ctp-card-total.ativo.verde    { border-top: 2px solid #5cb85c; }
    .ctp-texto-vermelho { color: #d9534f !important; }
    .ctp-texto-azul     { color: #005ecb !important; }
    .ctp-texto-verde    { color: #00b050 !important; }
  </style>

</head>

<body>
	<section class="panel lista_contas">

        <table class="table table-striped table-advance table-hover" id="tabela_contas_pagar" 
         width="100%" style="font-size: 10px">
                          
            <tbody>
                <?php
                    $criterio="";

                    if ($tipo_conta=='S') {
                        if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="V" ){
                            $criterio =
                            " WHERE ctp_data_vencimento >='$data_inicial' and
                                    ctp_data_vencimento <='$data_final' and
                                    ctp_codigo_conta >='$conta_inicial' and
                                    ctp_codigo_conta <='$conta_final'" .
                                    $wfornecedor . $wfazenda . $wcc .
                            " ORDER BY IF(ctp_aceite='S',1,0) ASC, IF(ctp_aceite='S' AND (ctp_situacao='P' OR ctp_situacao='C'),1,0) ASC, ctp_data_vencimento ASC, ctp_numero_doc ASC";
                        }
                        else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="E"){
                            $criterio = 
                            " WHERE ctp_data_emissao >='$data_inicial' and
                                    ctp_data_emissao <='$data_final' and 
                                    ctp_codigo_conta >='$conta_inicial' and 
                                    ctp_codigo_conta <='$conta_final'" . 
                                    $wfornecedor . $wfazenda .  $wcc .
                            " ORDER BY ctp_situacao, ctp_data_emissao, ctp_numero_doc ASC";
                        }
                        else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="P"){
                            $criterio = 
                            " WHERE bcp_data_pagamento >='$data_inicial' and
                                    bcp_data_pagamento <='$data_final' and 
                                    ctp_codigo_conta >='$conta_inicial' and 
                                    ctp_codigo_conta <='$conta_final'" . 
                                    $wfornecedor . $wfazenda . $wcc . 
                            " ORDER BY bcp_situacao, bcp_data_pagamento, bcp_numero_id ASC";
                        }
                        else if ($data_inicial==0 && $data_final==0){
                            $criterio = " WHERE 
                            ctp_codigo_conta >='$conta_inicial' and 
                            ctp_codigo_conta <='$conta_final'" .
                            $wfornecedor . $wfazenda . $wcc .
                            " ORDER BY ctp_situacao, ctp_data_emissao, ctp_numero_doc ASC";
                        }
                    }
                    else {
                        if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="V" ){
                            $criterio =
                            " WHERE ctp_data_vencimento >='$data_inicial' and
                                    ctp_data_vencimento <='$data_final' " .
                                    $wfornecedor . $wfazenda . $wcc . $wconta .
                            " ORDER BY IF(ctp_aceite='S',1,0) ASC, IF(ctp_aceite='S' AND (ctp_situacao='P' OR ctp_situacao='C'),1,0) ASC, ctp_data_vencimento ASC, ctp_numero_doc ASC";
                        }
                        else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="E"){
                            $criterio = 
                            " WHERE ctp_data_emissao >='$data_inicial' and
                                    ctp_data_emissao <='$data_final'" . 
                                    $wfornecedor . $wfazenda .  $wcc . $wconta .
                            " ORDER BY ctp_situacao, ctp_data_emissao, ctp_numero_doc ASC";
                        }
                        else if ($data_inicial!=0 && $data_final!=0 && $tipo_data=="P"){
                            $criterio = 
                            " WHERE bcp_data_pagamento >='$data_inicial' and
                                    bcp_data_pagamento <='$data_final'" . 
                                    $wfornecedor . $wfazenda . $wcc . $wconta . 
                            " ORDER BY bcp_situacao, bcp_data_pagamento, bcp_numero_id ASC";
                        }
                        else if ($data_inicial==0 && $data_final==0){
                            $criterio = " WHERE" .
                            $wfornecedor . $wfazenda . $wcc . $wconta .
                            " ORDER BY ctp_situacao, ctp_data_emissao, ctp_numero_doc ASC";
                        }
                    }

                    if ($criterio=='') {
                        mysqli_close($conector);
                        exit;
                    }
                    else {    
                        if ($tipo_data=="P"){
                            /*$ssql = "SELECT * FROM baixa_contas_pagar
                                INNER JOIN contas_pagar
                                        ON bcp_numero_id=ctp_numero_doc AND
                                           bcp_parcela=ctp_parcela AND
                                           bcp_codigo_fornecedor=ctp_codigo_fornecedor
                                INNER JOIN tbl_plano_contas
                                        ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                                INNER JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctp_codigo_fazenda"
                                . $criterio; */

                            $ssql = "SELECT * FROM baixa_contas_pagar
                                INNER JOIN contas_pagar
                                        ON bcp_id=ctp_id
                                LEFT JOIN tbl_plano_contas
                                        ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                                INNER JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctp_codigo_fazenda"
                                . $criterio;

                        }
                        else {
                            $ssql = "SELECT * FROM contas_pagar
                                LEFT JOIN tbl_plano_contas
                                        ON tbl_plano_contas_codigo_id=ctp_codigo_conta
                                INNER JOIN tbl_pessoa
                                        ON tbl_pessoa_id=ctp_codigo_fazenda"
                                . $criterio;
                        }

                        $rs = mysqli_query($conector, $ssql); 
                               
                        $total_geral = 0;
                        $total_pagos = 0;
                        $total_pagos_parcial=0;
                        $total_vencidos=0;
                        $total_vencem_hoje=0;
                        $total_avencer=0;
                        $data_sistema = date("Y-m-d");

                        while ($registro_ctp = mysqli_fetch_object($rs)){
                                    //$dias_vencimento = diferenca_data($registro_ctp->cliente_validade_contrato); 
                            if ($tipo_data=="P"){
                                $numero_doc = $registro_ctp->bcp_numero_id;
                                $numero_parcela = $registro_ctp->bcp_parcela;
                                $codigo_fornecedor = $registro_ctp->bcp_codigo_fornecedor;
                                $sequencia_pag = $registro_ctp->bcp_sequencia_pagamento;
                                $razao = $registro_ctp->bcp_nome_fornecedor; 
                                //$data_pagamento = $registro_ctp->bcp_data_pagamento; 
                                //$valor_pagamento = $registro_ctp->bcp_valor_pagamento; 
                                $situacao = $registro_ctp->bcp_situacao;
                                $agendamento = $registro_ctp->ctp_agendamento;

                                $data_emissao = $registro_ctp->ctp_data_emissao; 
                                $data_vencimento = $registro_ctp->ctp_data_vencimento; 
                                $vlr_parcela = $registro_ctp->ctp_valor_parcela + $registro_ctp->ctp_valor_juros + $registro_ctp->ctp_outro_valor - $registro_ctp->ctp_valor_desconto; 
                                $aceite = $registro_ctp->ctp_aceite;
                                $codigo_forma_pagto = $registro_ctp->ctp_conta_pagamento;
                                $doc_parcela = str_pad($numero_doc, 9, "0", STR_PAD_LEFT);
                                $ctp_id = $registro_ctp->ctp_id;
                                $chave_ctp = $codigo_fornecedor . $numero_parcela . $numero_doc;
                                $emissao_edi = new DateTime($data_emissao);
                                $vencimento_edi = new DateTime($data_vencimento);

                                /*$pagamentos = "select * from baixa_contas_pagar
                                                 where bcp_numero_id='$numero_doc' and 
                                                       bcp_parcela='$numero_parcela' and
                                                       bcp_codigo_fornecedor='$codigo_fornecedor'";*/

                                $pagamentos = "select * from baixa_contas_pagar
                                                 where bcp_id='$ctp_id'";

                                $contas_baixadas = mysqli_query($conector, $pagamentos); 
                                $num_rows_contas = mysqli_num_rows($contas_baixadas);
                                $total_pago=0;

                                if ($num_rows_contas!=0){
                                    while ($fila_baixada = mysqli_fetch_object($contas_baixadas)) {
                                        $valor_pagamento = $fila_baixada->bcp_valor_pagamento;
                                        $total_pago+= $valor_pagamento;
                                        $data_pagamento = $fila_baixada->bcp_data_pagamento;
                                        $pagamento_edi = new DateTime($data_pagamento);
                                    }
                                }   
                                else {
                                    $pagamento_edi = "";
                                } 

                                $total_geral+= $vlr_parcela;
                                $total_pagos+= $total_pago;
                            }
                            else {
                                $ctp_id = $registro_ctp->ctp_id;
                                $numero_doc = $registro_ctp->ctp_numero_doc;
                                $numero_parcela = $registro_ctp->ctp_parcela;
                                $codigo_fornecedor = $registro_ctp->ctp_codigo_fornecedor;
                                $razao = $registro_ctp->ctp_nome_fornecedor; 
                                $data_emissao = $registro_ctp->ctp_data_emissao; 
                                $data_vencimento = $registro_ctp->ctp_data_vencimento; 
                                $vlr_parcela = $registro_ctp->ctp_valor_parcela + $registro_ctp->ctp_valor_juros + $registro_ctp->ctp_outro_valor - $registro_ctp->ctp_valor_desconto; 
                                $situacao = $registro_ctp->ctp_situacao;
                                $agendamento = $registro_ctp->ctp_agendamento;
                                $aceite = $registro_ctp->ctp_aceite;
                                $codigo_forma_pagto = $registro_ctp->ctp_conta_pagamento;

                                /*$ssql = "select * from baixa_contas_pagar
                                                 where bcp_numero_id='$numero_doc' and 
                                                       bcp_parcela='$numero_parcela' and
                                                       bcp_codigo_fornecedor='$codigo_fornecedor'";*/

                                $ssql = "select * from baixa_contas_pagar
                                                 where bcp_id='$ctp_id'";

                                $contas_baixadas = mysqli_query($conector, $ssql); 
                                $num_rows_contas = mysqli_num_rows($contas_baixadas);
                                $total_pago=0;

                                if ($num_rows_contas!=0){
                                    while ($fila_baixada = mysqli_fetch_object($contas_baixadas)) {
                                        $valor_pagamento = $fila_baixada->bcp_valor_pagamento;
                                        $total_pago+= $valor_pagamento;
                                        $data_pagamento = $fila_baixada->bcp_data_pagamento;
                                        $pagamento_edi = new DateTime($data_pagamento);
                                    }
                                }   
                                else {
                                    $pagamento_edi = "";
                                } 

                                $doc_parcela =str_pad($numero_doc, 9, "0", STR_PAD_LEFT);
                                $chave_ctp = $codigo_fornecedor . $numero_parcela . $numero_doc;
                                $emissao_edi = new DateTime($data_emissao);
                                $vencimento_edi = new DateTime($data_vencimento);

                                if ($aceite=="S") {
                                    $total_geral+= $vlr_parcela;

                                    if ($situacao != "P"){
                                        if ($data_vencimento < $data_sistema) {
                                            $total_vencidos= $total_vencidos + $vlr_parcela - $total_pago;
                                        }
                                        elseif ($data_vencimento == $data_sistema) {
                                            $total_vencem_hoje= $total_vencem_hoje + $vlr_parcela - $total_pago;
                                        }
                                        else {
                                            $total_avencer= $total_avencer + $vlr_parcela - $total_pago;
                                        }
                                    }

                                    if ($situacao == "P" or $situacao == "C"){
                                        $total_pagos+= $total_pago;
                                    }
                                }
                            }

                            // Se ctp_codigo_conta for NULL é rateio — busca contas da tbl_ctp_rateio
                            // O rateio usa o rc_ctp_id do primeiro registro criado no lote,
                            // por isso filtra por rc_codigo_local (fazenda) + numero_doc + parcela
                            if (is_null($registro_ctp->ctp_codigo_conta)) {
                                $num_doc_esc  = mysqli_real_escape_string($conector, $numero_doc);
                                $parcela_esc  = mysqli_real_escape_string($conector, $numero_parcela);
                                $fazenda_esc  = mysqli_real_escape_string($conector, ltrim($registro_ctp->ctp_codigo_fazenda, '0'));
                                $rs_rat = mysqli_query($conector,
                                    "SELECT rc_nome_conta FROM tbl_ctp_rateio
                                     WHERE rc_codigo_local = '$fazenda_esc'
                                       AND rc_ctp_id IN (
                                           SELECT ctp_id FROM contas_pagar
                                           WHERE ctp_numero_doc = '$num_doc_esc'
                                             AND ctp_parcela    = '$parcela_esc'
                                             AND ctp_codigo_conta IS NULL
                                       )
                                     ORDER BY rc_id ASC");
                                $total_rat = mysqli_num_rows($rs_rat);
                                $first_rat = mysqli_fetch_object($rs_rat);
                                $desc_conta = $first_rat ? $first_rat->rc_nome_conta : 'Rateio';
                                if ($total_rat > 1) {
                                    $desc_conta .= ' <span style="color:#888;font-size:10px">+' . ($total_rat - 1) . '</span>';
                                }
                            } else {
                                $desc_conta = $registro_ctp->tbl_plano_contas_descricao;
                            }
                            $desc_fazenda = $registro_ctp->tbl_pessoa_nome;

                            $total_a_pagar = $vlr_parcela - $total_pago;

                            // Define categoria da linha para filtro client-side
                            // Registros sem aceite nunca são "pago" — sempre caem na verificação de data
                            if ($aceite == "S" && ($situacao == "P" || $situacao == "C")) {
                                $categoria_linha = "pago";
                            } elseif ($data_vencimento < $data_sistema) {
                                $categoria_linha = "vencido";
                            } elseif ($data_vencimento == $data_sistema) {
                                $categoria_linha = "vencem_hoje";
                            } else {
                                $categoria_linha = "a_vencer";
                            }

                            echo "<tr data-categoria='" . $categoria_linha . "' data-ctp-id='" . $ctp_id . "'>";
                            if ($aceite==""){
                                echo "<td width='2%'></td>";
                                echo "<td width='4%'>";
                                echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$doc_parcela."</a>";
                                echo "</td>";

                                echo "<td width='2%'>";
                                echo "<a style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$numero_parcela."</a>";
                                "</td>";

                                echo "<td width='14%'>";
                                echo "<a style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$razao."</a>";
                                "</td>";

                                echo "<td width='14%'>";
                                echo "<a style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$desc_fazenda."</a>";
                                "</td>";

                                echo "<td width='15%'>";
                                echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$desc_conta."</a>";
                                "</td>";

                                echo "<td width='6%'>";
                                echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$emissao_edi->format('d/m/Y')."</a>";
                                "</td>";

                                echo "<td width='6%'>";
                                echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".$vencimento_edi->format('d/m/Y')."</a>";
                                "</td>";

                                echo "<td width='6%'>";
                                echo "<a  style='color:#ccc' data-toggle='tooltip' data-placement='right' title='Falta Aceite'>".number_format($vlr_parcela, 2, ",", ".")."</a>";
                                "</td>";

                                echo "<td width='6%'></td>";
                                echo "<td width='6%'></td>";
                                echo "<td width='19%'>";
                                echo "<div class='btn-group'>";
                                echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='right' title='Editar esse registro' ></i></a>";
                                echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='right' title='Excluir esse registro' onClick='enviar_lixeira(\"{$ctp_id}\",\"{$doc_parcela}\",\"{$numero_parcela}\",1)' ></i></a>"; 
                                echo "</div>";
                                echo "</td>";
                            }
                            else if ($situacao=="P"){
                                echo "<td width='2%'><i class='btn icon_check' style='color:green' data-toggle='tooltip' data-placement='right' title='Pago' ></i></td>";
                                echo "<td width='4%'>".$doc_parcela."</td>";
                                echo "<td width='2%'>".$numero_parcela."</td>";
                                echo "<td width='14%'>".$razao."</td>";
                                echo "<td width='14%'>".$desc_fazenda."</td>";
                                echo "<td width='15%'>".$desc_conta."</td>";
                                echo "<td width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                echo "<td width='6%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";
                                if ($pagamento_edi=='') {
                                    echo "<td width='6'></td>";
                                    echo "<td width='6%'></td>";
                                }
                                else {
                                    echo "<td width='6'>".$pagamento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                }
                                if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4) {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                    echo "</div>";
                                }
                                else {
                                    echo "<td width='19%'>";
                                    echo "<div class='btn-group'>";
                                    echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                    echo "</div>";
                                }
                                echo "</td>";
                            }
                            else {
                                if ($data_vencimento < $data_sistema) {
                                    if ($numero_doc!=0 && $numero_doc!='') {
                                        echo "<td width='2%'>
                                            <input type='checkbox' name='id_ctp' class='checkbox2' data-toggle='tooltip' data-placement='right' 
                                               title='Seleciona esse registro para baixar' 
                                             onClick='somar_total_para_baixar()' value='" . $ctp_id . "'></td>";
                                    }
                                    else {
                                        echo "<td style='color:#B22222' width='2%'></td>";
                                    }
                                    echo "<td style='color:#B22222' width='4%'>".$doc_parcela."</td>";
                                    echo "<td style='color:#B22222' width='2%'>".$numero_parcela."</td>";
                                    echo "<td style='color:#B22222' width='14%'>".$razao."</td>";
                                    echo "<td style='color:#B22222' width='14%'>".$desc_fazenda."</td>";
                                    echo "<td style='color:#B22222' width='15%'>".$desc_conta."</td>";
                                    echo "<td style='color:#B22222'width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                    echo "<td style='color:#B22222'width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                    echo "<td style='color:#B22222'width='6%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";

                                    if ($situacao=="P" || $situacao=="C"){
                                        echo "<td style='color:#B22222'width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                        echo "<td style='color:#B22222'width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                    }
                                    else {
                                        echo "<td width='6%'></td>";
                                        echo "<td width='6%'></td>";
                                    }

                                    if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4) {
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$ctp_id}\",\"{$doc_parcela}\",\"{$numero_parcela}\",1)' ></i></a>"; 

                                        echo '<a class="btn" style="font-size: 11px;" href="#" 
                                                  data-toggle="modal" 
                                                  data-target="#modal_baixar" 
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wctpid="'.$ctp_id.'"
                                                  data-wvalor="'.$total_a_pagar.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wformapag="'.$codigo_forma_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';

                                        echo "</div>";
                                    }
                                    else {
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo '<a class="btn" style="font-size: 11px;" href="#" 
                                                  data-toggle="modal" 
                                                  data-target="#modal_baixar" 
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wctpid="'.$ctp_id.'"
                                                  data-wvalor="'.$total_a_pagar.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wformapag="'.$codigo_forma_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';
                                        echo "</div>";
                                    }
                                    echo "</td>";
                                }
                                else {
                                    if ($numero_doc!=0 && $numero_doc!='') {
                                        echo "<td width='2%'>
                                            <input type='checkbox' name='id_ctp' class='checkbox2' data-toggle='tooltip' data-placement='right' 
                                               title='Seleciona esse registro para baixar' 
                                             onClick='somar_total_para_baixar()' value='" . $ctp_id . "'></td>";
                                    }
                                    else {
                                        echo "<td width='2%'></td>";
                                    }
                                    echo "<td width='4%'>".$doc_parcela."</td>";
                                    echo "<td width='2%'>".$numero_parcela."</td>";
                                    echo "<td width='14%'>".$razao."</td>";
                                    echo "<td width='14%'>".$desc_fazenda."</td>";
                                    echo "<td width='15%'>".$desc_conta."</td>";
                                    echo "<td width='6%'>".$emissao_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".$vencimento_edi->format('d/m/Y')."</td>";
                                    echo "<td width='6%'>".number_format($vlr_parcela, 2, ",", ".")."</td>";

                                    if ($situacao=="P" || $situacao=="C"){
                                        echo "<td width='6%'>".$pagamento_edi->format('d/m/Y')."</td>";
                                        echo "<td width='6%'>".number_format($total_pago, 2, ",", ".")."</td>";
                                    }
                                    else {
                                        echo "<td width='6%'></td>";
                                        echo "<td width='6%'></td>";
                                    }

                                    if ($codigo_grupo_usuario==1 || $codigo_grupo_usuario==2 || $codigo_grupo_usuario==4) {
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' style='font-size: 11px;' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Excluir esse registro' onClick='enviar_lixeira(\"{$ctp_id}\",\"{$doc_parcela}\",\"{$numero_parcela}\",1)' ></i></a>";

                                        echo '<a class="btn" style="font-size: 11px;" href="#"
                                                  data-toggle="modal"
                                                  data-target="#modal_baixar"
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wctpid="'.$ctp_id.'"
                                                  data-wvalor="'.$total_a_pagar.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wformapag="'.$codigo_forma_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';

                                        echo "</div>";
                                    }
                                    else {
                                        echo "<td width='19%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' style='font-size: 11px;' href='form_contas_pagar_editar.php?id=".$ctp_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo '<a class="btn" style="font-size: 11px;" href="#"
                                                  data-toggle="modal"
                                                  data-target="#modal_baixar"
                                                  data-wdoc="'.$numero_doc.'"
                                                  data-wparcela="'.$numero_parcela.'"
                                                  data-wctpid="'.$ctp_id.'"
                                                  data-wvalor="'.$total_a_pagar.'"
                                                  data-wvencimento="'.$data_vencimento.'"
                                                  data-wformapag="'.$codigo_forma_pagto.'"
                                                  >
                                                  <i class="icon_folder_download" data-toggle="tooltip" data-placement="left"  title="Baixar esse registro" ></i>
                                                  </a>';
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
            <div id="ctp-cards-source" style="display:none">
                <div style="display:flex; width:100%; margin-bottom:8px;">
                    <div style="width:20%">
                        <div class="ctp-card-total vermelho" data-filtro="vencidos">
                            <div>Vencidos R$</div>
                            <div class="valor ctp-texto-vermelho"><?php echo number_format($total_vencidos, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctp-card-total vermelho" data-filtro="vencem_hoje">
                            <div>Vencem Hoje R$</div>
                            <div class="valor ctp-texto-vermelho"><?php echo number_format($total_vencem_hoje, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctp-card-total azul" data-filtro="a_vencer">
                            <div>A Vencer R$</div>
                            <div class="valor ctp-texto-azul"><?php echo number_format($total_avencer, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctp-card-total verde" data-filtro="pagos">
                            <div>Pagos R$</div>
                            <div class="valor ctp-texto-verde"><?php echo number_format($total_pagos, 2, ",", "."); ?></div>
                        </div>
                    </div>
                    <div style="width:20%">
                        <div class="ctp-card-total azul" data-filtro="total_periodo">
                            <div>Total do Período R$</div>
                            <div class="valor ctp-texto-azul"><?php echo number_format($total_geral, 2, ",", "."); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <thead>
                <tr>
                <tr>
                    <div class="row col-md-8 confirmar_baixa_selecionados" hidden="">
                        <div class="form-group col-md-3">
                            <button type="button" class="btn btn-primary pull-left" id="baixar_selecionados"
                            onClick="modal_baixar()" >Baixar Selecionados</button>
                        </div>
                    </div>
                </tr>

                <tr>
                    <th>
                        <input type="checkbox" class='checkbox2' id="seleciona_todos_somar" data-toggle="tooltip" data-placement="right" title="Selecione um registro para baixar ou clique aqui para selecionar todos">
                    </th> 
                    <th> Documento</th>
                    <th> Parcela</th>
                    <th> Fornecedor</th>
                    <th> Local</th>
                    <th> Conta</th>
                    <th> Emissão</th>
                    <th> Vencimento</th>
                    <th> Valor Parcela</th>
                    <th> Pagamento</th>
                    <th> Valor Pago</th>
                    <th> <i class="icon_cogs"></i> Ações</th>
                </tr>
            </thead>
        </table>

    </section>

    <script src="js/contas_pagar.js?<?php echo filemtime(__DIR__.'/js/contas_pagar.js'); ?>" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
