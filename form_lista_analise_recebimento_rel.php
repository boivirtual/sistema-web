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

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start();

    $tipo_relatorio = $_REQUEST["tipo"];
    $codigo_cliente = $_REQUEST["cliente"];
    $codigo_conta = $_REQUEST["conta"];
    $codigo_cc = $_REQUEST["c_custo"];
    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_rel = $_REQUEST["tipo_rel"];
    $tipo_data = $_REQUEST["tipo_data"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $codigo_fazenda = $_REQUEST["array_fazenda"];

    $_SESSION['data_inicio_ctr']=$data_inicial;
    $_SESSION['data_fim_ctr']=$data_final;
    $_SESSION['tipo_data_ctr']=$tipo_data;
    $_SESSION['tipo_rel_ctr']=$tipo_rel; 
    $_SESSION['codigo_c_custo_ctr']=$codigo_cc; 
    $_SESSION['codigo_conta_ctr']=$codigo_conta; 
    $_SESSION['razao_nome_ctr']=$codigo_cliente;
    $_SESSION['codigo_local_ctr']=$codigo_fazenda;

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
        $wcc = " AND (ctr_codigo_c_custo IN($cc) OR (ctr_codigo_c_custo IS NULL AND ctr_id IN (SELECT rc_ctr_id FROM tbl_ctr_rateio WHERE rc_codigo_cc IN ($cc))))";
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
    $criterio='';
    $linha=0;

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

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_financeiros.php">Relatórios Financeiros</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Análise de Recebimentos</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-hand-holding-usd"></i> Análise de Recebimentos</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="tipo_relatorio" <?php echo "value='".$tipo_relatorio."'";?>>

                                                <input type="hidden" id="data_inicial"
                                                    <?php echo "value='".$data_inicial."'";?>>

                                                <input type="hidden" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>

                                                <input type="hidden" id="tipo_rel"
                                                    <?php echo "value='".$tipo_rel."'";?>>

                                                <input type="hidden" id="tipo_data"
                                                    <?php echo "value='".$tipo_data."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                                <input type="hidden" id="codigo_cc"
                                                    <?php echo "value='".$codigo_cc."'";?>>

                                                <input type="hidden" id="codigo_fazenda"
                                                    <?php echo "value='".$codigo_fazenda."'";?>>

                                                <input type="hidden" id="codigo_conta"
                                                    <?php echo "value='".$codigo_conta."'";?>>

                                                <input type="hidden" id="codigo_cliente"
                                                    <?php echo "value='".$codigo_cliente."'";?>>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel">Filtros:</label>
                                                    <span><?php echo $descricao_filtro;?></span>
                                                </div>

                                                <div class="col-md-4">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_receber()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="listar_contas_receber_excel()">Excel</button>
                                                </div>
                                            </div>
<table class="table table-bordered table-striped table-advance table-hover" id="tabela_analise_recebimento" width="100%">

<thead>
    <tr>
    <th colspan="4" class="text-center"> Conta</th>
    <th class="text-center"> A Vencer</th>
    <th class="text-center"> Vencidos</th>
    <th class="text-center"> Recebido</th>
    <th class="text-center"> Total</th>
    </tr>
</thead>


<tbody>

<?php
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
        $contas_rec = "SELECT * FROM contas_receber
            WHERE ctr_data_emissao >='$data_inicial' and
                  ctr_data_emissao <='$data_final' and
                  ctr_lixeira=0" . $wcc . $wcliente . $wfazendas . $wconta .
            " ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC";
    }
    else if ($tipo_data=="V"){
        $contas_rec = "SELECT * FROM contas_receber
            WHERE ctr_data_vencimento >='$data_inicial' and
                  ctr_data_vencimento <='$data_final' and
                  ctr_lixeira=0" . $wcc . $wcliente . $wfazendas . $wconta .
            " ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC";
    }
    else {
        $contas_rec = "SELECT * FROM baixa_contas_receber
            INNER JOIN contas_receber
                    ON bcr_id=ctr_id
            WHERE bcr_data_pagamento >='$data_inicial' and
                  bcr_data_pagamento <='$data_final' and
                  ctr_lixeira=0" . $wcc . $wcliente . $wfazendas . $wconta .
            " ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"; 
    }

    $contas_rec = mysqli_query($conector, $contas_rec);
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
                $razao = utf8_encode(substr($registro_contas_rec->ctr_nome_cliente, 0,45));
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
    
    echo '<tr>';
    echo '<td width="15%" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold; border-right: 0;">'.substr($conta_inicio, 0,1).' - '.$descricao_conta.'</td>';
    echo '<td style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold; border-right: 0;"></td>';
    echo '<td style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold; border-right: 0;"></td>';
    echo '<td style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;"></td>';

    echo '<td align="right" width="8%"style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_avencer_conta_sintetica,2,',','.').'</td>';
    echo '<td align="right" width="8%"style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_vencida_conta_sintetica,2,',','.').'</td>';
    echo '<td align="right" width="8%" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_pago_conta_sintetica,2,',','.').'</td>';
    echo '<td align="right" width="8%"style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_conta_sintetica,2,',','.').'</td>';
    echo '</tr>';

    $index_sub_conta = 0;

    for ($i = 0; $i < $qtd_sub_contas; $i++) {

        $index_sub_conta++;

        if ($index_sub_conta>6){
            if ($valor_sub_conta!=0){
                $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

                echo '<tr>';
                echo '<td width="40%" style="background-color: #DEE; color: #696969; font-weight:bold; border-right: 0;">'.$pla_descricao.'</td>';
                echo '<td style="background-color: #DEE; border-right: 0;"></td>';
                echo '<td style="background-color: #DEE; border-right: 0;"></td>';
                echo '<td style="background-color: #DEE;"></td>';

                echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_avencer_sub_conta,2,',','.').'</td>';
                echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_vencido_sub_conta,2,',','.').'</td>';
                echo '<td align="right" style="background-color:#DEE; color: #696969; font-weight:bold;">'.number_format($valor_pago_sub_conta,2,',','.').'</td>';
                echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_sub_conta,2,',','.').'</td>';
                echo '</tr>';

                $index_conta=0;

                for ($j = 0; $j < $qtd_contas; $j++) {
                    $index_conta++;

                    if ($index_conta>6){
                        if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                            if ($valor_conta!=0){
                                $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

                                echo '<tr>';
                                echo '<td width="40%" style="border-right: 0;" >'.$pla_descricao.'</td>';
                                echo '<td style="border-right: 0;"></td>';
                                echo '<td style="border-right: 0;"></td>';
                                echo '<td></td>';

                                echo '<td align="right">'.number_format($valor_avencer_conta,2,',','.').'</td>';
                                echo '<td align="right">'.number_format($valor_vencido_conta,2,',','.').'</td>';
                                echo '<td align="right">'.number_format($valor_pago_conta,2,',','.').'</td>';
                                echo '<td align="right">'.number_format($valor_conta,2,',','.').'</td>';

                                if ($tipo_rel=="A"){
                                    $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wcc,$wcliente, $wfazendas);

                                    for ($k=0; $k < count($array_contas); $k++) { 
                                        if ($k==0){
                                            echo '<tr>';
                                            echo '<td width="8%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Documento/Local</td>';
                                            echo '<td width="38%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Cliente</td>';
                                            echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Emissão</td>';
                                            echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Vencimento</td>';
                                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Valor</td>';
                                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Pagamento</td>';
                                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Valor Pago</td>';
                                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Situação</td>';
                                            echo '</tr>';

                                            echo '<tr>';
                                            echo '<td width="8%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                            echo '<td width="38%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                            echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                            echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                            echo '<td align="right" width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][4].'</td>';
                                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                            echo '<td align="right" width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][6].'</td>';
                                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                            echo '</tr>';
                                        }
                                        else {
                                            echo '<tr>';
                                            echo '<td style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                            echo '<td style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                            echo '<td style="color: #bfbdbd; font-size: 11px; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                            echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][4].'</td>';
                                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                            echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][6].'</td>';
                                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                            echo '</tr>';
                                        }
                                    }
                                }
                                else {
                                    echo '</tr>';
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

        echo '<tr>';
        echo '<td width="40%" style="background-color: #DEE; color: #696969; font-weight:bold; border-right: 0;">'.$pla_descricao.'</td>';
        echo '<td style="background-color: #DEE; border-right: 0;"></td>';
        echo '<td style="background-color: #DEE; border-right: 0;"></td>';
        echo '<td align="right" style="background-color: #DEE;"></td>';

        echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_avencer_sub_conta,2,',','.').'</td>';
        echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_vencido_sub_conta,2,',','.').'</td>';
        echo '<td align="right" style="background-color:#DEE; color: #696969; font-weight:bold;">'.number_format($valor_pago_sub_conta,2,',','.').'</td>';
        echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_sub_conta,2,',','.').'</td>';
        echo '</tr>';

        $index_conta=0;

        for ($j = 0; $j < $qtd_contas; $j++) {
                $index_conta++;

            if ($index_conta>6){
                if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                    if ($valor_conta!=0){
                        $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

                        echo '<tr>';
                        echo '<td width="40%" style="border-right: 0;">'.$pla_descricao.'</td>';
                        echo '<td style="border-right: 0;"></td>';
                        echo '<td style="border-right: 0;"></td>';
                        echo '<td></td>';

                        echo '<td align="right">'.number_format($valor_avencer_conta,2,',','.').'</td>';
                        echo '<td align="right">'.number_format($valor_vencido_conta,2,',','.').'</td>';
                        echo '<td align="right">'.number_format($valor_pago_conta,2,',','.').'</td>';
                        echo '<td align="right">'.number_format($valor_conta,2,',','.').'</td>';

                        if ($tipo_rel=="A"){
                            $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wcc,$wcliente, $wfazendas);

                            for ($k=0; $k < count($array_contas); $k++) { 
                                if ($k==0){

                                    echo '<tr>';
                                    echo '<td width="8%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Documento/Local</td>';
                                    echo '<td width="38%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Cliente</td>';
                                    echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Emissão</td>';
                                    echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Vencimento</td>';
                                    echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Valor</td>';
                                    echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Pagamento</td>';
                                    echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Valor Pago</td>';
                                    echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Situação</td>';
                                    echo '</tr>';
                                    
                                    echo '<tr>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                    echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][4].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                    echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][6].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                    echo '</tr>';
                                }
                                else {
                                    echo '<tr>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                    echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][4].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                    echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][6].'</td>';
                                    echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        else {
                            echo '</tr>';
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

            echo '<tr>';
            echo '<td width="40%" style="border-right: 0;">'.$pla_descricao.'</td>';
            echo '<td style="border-right: 0;"></td>';
            echo '<td style="border-right: 0;"></td>';
            echo '<td></td>';
            echo '<td align="right">'.number_format($valor_avencer_conta,2,',','.').'</td>';
            echo '<td align="right">'.number_format($valor_vencido_conta,2,',','.').'</td>';
            echo '<td align="right">'.number_format($valor_pago_conta,2,',','.').'</td>';
            echo '<td>'.number_format($valor_conta,2,',','.').'</td>';

            if ($tipo_rel=="A"){
                    $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wcc,$wcliente,$wfazendas);

                for ($k=0; $k < count($array_contas); $k++) { 
                    if ($k==0){
                        echo '<tr>';
                        echo '<td width="8%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Documento/Local</td>';
                        echo '<td width="38%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Cliente</td>';
                        echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Emissão</td>';
                        echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Vencimento</td>';
                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Valor</td>';
                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Pagamento</td>';
                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Valor Pago</td>';
                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; font-size: 11px;">Situação</td>';
                        echo '</tr>';

                        echo '<tr>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                        echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][4].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                        echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][6].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                        echo '</tr>';
                    }
                    else {
                        echo '<tr>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                        echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][4].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                        echo '<td align="right" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][6].'</td>';
                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                        echo '</tr>';
                    }
                }
            }
        }
        else {
            echo '</tr>';
        } 
    }// fim do valor conta
    
?>
</tbody>

</table>

<?php
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

    function ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wcc,$wcliente, $wfazendas){

        $wconta_notas = " AND (ctr_codigo_conta='$conta_inicio' OR (ctr_codigo_conta IS NULL AND ctr_id IN (SELECT rc_ctr_id FROM tbl_ctr_rateio WHERE rc_codigo_conta='$conta_inicio')))";

        if ($tipo_data=="E"){
            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                WHERE ctr_data_emissao >='$data_inicial' and
                      ctr_data_emissao <='$data_final'" . $wconta_notas . " AND
                      ctr_lixeira=0" . $wcc . $wcliente . $wfazendas .
                " ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC");
        }
        else if ($tipo_data=="V"){
            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                WHERE ctr_data_vencimento >='$data_inicial' AND
                      ctr_data_vencimento <='$data_final'" . $wconta_notas . " AND
                      ctr_lixeira=0" . $wcc . $wcliente . $wfazendas .
                " ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC");
        }
        else {
            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                INNER JOIN contas_receber
                        ON bcr_id=ctr_id
                WHERE bcr_data_pagamento >='$data_inicial' AND
                      bcr_data_pagamento <='$data_final'" . $wconta_notas . " AND
                      ctr_lixeira=0" . $wcc . $wcliente . $wfazendas .
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
            $vencimento = $registro_contas_rec->ctr_data_vencimento;
            $vencimento_edi = new DateTime($registro_contas_rec->ctr_data_vencimento);
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
                $desc_conta_pgto = utf8_encode($registro_conta_pagamento->tbl_conta_pagamento_descricao);
            }
            else {
                $desc_conta_pgto = '';  
            }

            $data_pag_edi='';

            if ($situacao == "P" || $situacao == "C"){
                $conta_baixada = mysqli_query($conector, "SELECT bcr_valor_pagamento,bcr_data_pagamento
                FROM baixa_contas_receber 
                WHERE bcr_id='$ctr_id' AND 
                      bcr_parcela='$parcela'");
                                                                                         
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
                    $desc_pessoa = utf8_encode($reg_rateio->rc_nome_local);
                    $valor_fatia = $reg_rateio->rc_valor_conta;
                    $valor_pago_fatia = ($total_pagar != 0) ? $valor_pago * ($valor_fatia / $total_pagar) : 0;

                    $dados = [$doc_imp,
                              $razao,
                              $emissao_edi->format('d/m/Y'),
                              $vencimento_edi->format('d/m/Y'),
                              number_format($valor_fatia,2,',','.'),
                              $data_pag_edi,
                              number_format($valor_pago_fatia,2,',','.'),
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
                $desc_pessoa = utf8_encode($registro_pessoa->tbl_pessoa_nome);

                $dados = [$doc_imp,
                          $razao,
                          $emissao_edi->format('d/m/Y'),
                          $vencimento_edi->format('d/m/Y'),
                          number_format($total_pagar,2,',','.'),
                          $data_pag_edi,
                          number_format($valor_pago,2,',','.'),
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
?>
                                            <div class="row">  
                                                <div class="col-md-12">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_receber()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="listar_contas_receber_excel()">Excel</button>
                                                </div>
                                            </div>
                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Análise de Recebimento</h4>
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
                            <h4 class="modal-title">Relatório Análise de Recebimento - Mensagem</h4>
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
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'relatorios_financeiros.js';
  require 'rodape.php';
?>




