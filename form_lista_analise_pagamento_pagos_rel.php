<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $tipo_relatorio = $_REQUEST["tipo"];
    $codigo_fornecedor = $_REQUEST["fornecedor"];
    $codigo_conta = $_REQUEST["conta"];
    $codigo_fazenda = $_REQUEST["fazendas"];
    $codigo_cc = $_REQUEST["codigo_cc"];
    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_rel = $_REQUEST["tipo_rel"];
    $tipo_data = $_REQUEST["tipo_data"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $_SESSION['data_inicio_ctp']=$data_inicial;
    $_SESSION['data_fim_ctp']=$data_final;
    $_SESSION['tipo_data_ctp']=$tipo_data;
    $_SESSION['tipo_rel_ctp']=$tipo_rel; 
    $_SESSION['codigo_c_custo_ctp']=$codigo_cc; 
    $_SESSION['codigo_conta_ctp']=$codigo_conta; 
    $_SESSION['codigo_fornecedor_ctp']=$codigo_fornecedor; 
    $_SESSION['codigo_local_ctp']=$codigo_fazenda;

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
            include "cabecalho.php"; 
            include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php"; 
            include "limpar_secao_compra_venda.php";
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_movimentacao.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";

        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_financeiros.php">Relatórios Financeiros</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Análise de Pagamentos</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-search-dollar"></i> Análise de Pagamentos</h3>
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

                                                <input type="hidden" id="tipo_relatorio" <?php echo "value='".$tipo_relatorio."'";?>>

                                                <input type="hidden" id="expande_tela" value="S">

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

                                                <input type="hidden" id="codigo_fazenda"
                                                    <?php echo "value='".$codigo_fazenda."'";?>>

                                                <input type="hidden" id="codigo_cc"
                                                    <?php echo "value='".$codigo_cc."'";?>>

                                                <input type="hidden" id="codigo_conta"
                                                    <?php echo "value='".$codigo_conta."'";?>>

                                                <input type="hidden" id="codigo_fornecedor"
                                                    <?php echo "value='".$codigo_fornecedor."'";?>>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel">Filtros:</label>
                                                    <span><?php echo $descricao_filtro;?></span>
                                                </div>

                                                <div class="col-md-4">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_pagar()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="listar_contas_pagar_excel()">Excel</button>
                                                </div>
                                            </div>

<table class="table table-bordered table-striped table-advance table-hover" id="tabela_analise_pagamento" width="100%">

<thead>
    <tr>
        <th colspan="4" class="text-center"> Conta</th>
        <th class="text-center"> A Vencer</th>
        <th class="text-center"> Vencidos</th>
        <th class="text-center"> Pago</th>
        <th class="text-center"> Total</th>
    </tr>
</thead>

<tbody>
<?php
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
        $descricao_conta = $registro_plano_contas->tbl_plano_contas_descricao;
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
    $qtd_sub_contas = count($arry_sub_conta);
    $qtd_contas = count($arry_conta);

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

    echo '<tr>';
    echo '<td style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold; width: 15%; border-right: 0;">TOTAL GERAL</td>';
    echo '<td style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold; border-right: 0;"></td>';
    echo '<td style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold; border-right: 0;"></td>';
    echo '<td style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold;"></td>';

    echo '<td width="8%" align="right" style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold;">'.number_format($total_avencer_conta_sintetica,2,",",".").'</td>';

    echo '<td width="8%" align="right" style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold;">'.number_format($total_vencida_conta_sintetica,2,",",".").'</td>';

    echo '<td width="8%" align="right" style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold;">'.number_format($total_pago_conta_sintetica,2,",",".").'</td>';

    echo '<td width="8%" align="right" style="background-color: #C2D0D0; color: #1C1C1C; font-weight:bold;">'.number_format($total_conta_sintetica,2,",",".").'</td>';
    echo '</tr>';

    if ($total_sem_conta != 0) {
        echo '<tr>';
        echo '<td style="background-color: #F0E68C; color: #1C1C1C; font-weight:bold; width: 15%; border-right: 0;">RATEIO SEM CONTA DEFINIDA</td>';
        echo '<td style="background-color: #F0E68C; border-right: 0;"></td>';
        echo '<td style="background-color: #F0E68C; border-right: 0;"></td>';
        echo '<td style="background-color: #F0E68C;"></td>';
        echo '<td width="8%" align="right" style="background-color: #F0E68C; color: #1C1C1C; font-weight:bold;">'.number_format($total_avencer_sem_conta,2,",",".").'</td>';
        echo '<td width="8%" align="right" style="background-color: #F0E68C; color: #1C1C1C; font-weight:bold;">'.number_format($total_vencido_sem_conta,2,",",".").'</td>';
        echo '<td width="8%" align="right" style="background-color: #F0E68C; color: #1C1C1C; font-weight:bold;">'.number_format($total_pago_sem_conta,2,",",".").'</td>';
        echo '<td width="8%" align="right" style="background-color: #F0E68C; color: #1C1C1C; font-weight:bold;">'.number_format($total_sem_conta,2,",",".").'</td>';
        echo '</tr>';
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
        echo '<tr>';
        echo '<td width="15%" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold; border-right: 0;">'.$cod_conta_sintetica.' - '.$descricao_conta_sintetica.'</td>';
        echo '<td style="background-color: #C2E0E0; border-right: 0;"></td>';
        echo '<td style="background-color: #C2E0E0; border-right: 0;"></td>';
        echo '<td style="background-color: #C2E0E0;"></td>';

        echo '<td width="8%" align="right" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($valor_avencer_conta_sintetica,2,',','.').'</td>';
        echo '<td width="8%" align="right" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($valor_vencido_conta_sintetica,2,',','.').'</td>';
        echo '<td width="8%" align="right" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($valor_pago_conta_sintetica,2,',','.').'</td>';
        echo '<td width="8%" align="right" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($valor_conta_sintetica,2,',','.').'</td>';

        if ($tipo_rel=="A"){
        }

        echo '</tr>';

        $index_sub_conta = 0;

        for ($y = 0; $y < $qtd_sub_contas; $y++) {
         
            $index_sub_conta++;

            if ($index_sub_conta>6){
                if ($valor_sub_conta!=0 && substr($cod_sub_conta, 0,1)==$cod_conta_sintetica){
                    $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

                    echo '<tr>';
                    echo '<td width="30%" style="background-color: #DEE; color: #696969; font-weight:bold; border-right: 0;">'.$pla_descricao.'</td>';
                    echo '<td style="background-color: #DEE; border-right: 0;"></td>';
                    echo '<td style="background-color: #DEE; border-right: 0;"></td>';
                    echo '<td style="background-color: #DEE;"></td>';

                    echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_avencer_sub_conta,2,',','.').'</td>';
                    echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_vencido_sub_conta,2,',','.').'</td>';
                    echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_pago_sub_conta,2,',','.').'</td>';
                    echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_sub_conta,2,',','.').'</td>';

                    if ($tipo_rel=="A"){
                    }
                    echo '</tr>';

                    $index_conta=0;

                    for ($j = 0; $j < $qtd_contas; $j++) {

                        $index_conta++;

                        if ($index_conta>6){
                            if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                                if ($valor_conta!=0 && substr($conta_inicio, 0,1)==$cod_conta_sintetica){
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
                                        $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wfazendas,$wfornecedor,$wcc);

                                        for ($k=0; $k < count($array_contas); $k++) { 
                                            if ($k==0){
                                                echo '<tr>';
                                                echo '<td width="8%" style="color: #bfbdbd; font-size: 11px;">Documento/Fazenda</td>';
                                                echo '<td width="38%" style="color: #bfbdbd; font-size: 11px;">Fonte Pagadora</td>';
                                                echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Emissão</td>';
                                                echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Vencimento</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Valor</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Recebimento</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Vlr Recebido</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Situação</td>';
                                                echo '</tr>';

                                                echo '<tr>';
                                                echo '<td width="8%" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                                echo '<td width="38%" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                                echo '<td width="7%" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                                echo '<td width="7%" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][4].'</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][6].'</td>';
                                                echo '<td width="10%" style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                                echo '</tr>';
                                            }
                                            else {
                                                echo '<tr>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][4].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][6].'</td>';
                                                echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                                echo '</tr>';
                                            }
                                        }

                                        echo '<tr>';
                                        echo '<th class="text-center" style="border-right: 0;"> Conta</th>';
                                        echo '<th style="border-right: 0;"></th>';
                                        echo '<th style="border-right: 0;"></th>';
                                        echo '<th></th>';
                                        echo '<th class="text-center"> A Vencer</th>';
                                        echo '<th class="text-center"> Vencidos</th>';
                                        echo '<th class="text-center"> Pago</th>';
                                        echo '<th class="text-center"> Total</th>';
                                        echo '</tr>';
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
            $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

            echo '<tr>';
            echo '<td width="30%" style="background-color: #DEE; color: #696969; font-weight:bold; border-right: 0;">'.$pla_descricao.'</td>';
            echo '<td style="background-color: #DEE; border-right: 0;"></td>';
            echo '<td style="background-color: #DEE; border-right: 0;"></td>';
            echo '<td style="background-color: #DEE;"></td>';

            echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_avencer_sub_conta,2,',','.').'</td>';
            echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_vencido_sub_conta,2,',','.').'</td>';
            echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_pago_sub_conta,2,',','.').'</td>';
            echo '<td align="right" style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_sub_conta,2,',','.').'</td>';

            if ($tipo_rel=="A"){
            }
            echo '</tr>';


            $index_conta=0;

            for ($j = 0; $j < $qtd_contas; $j++) {

                $index_conta++;

                if ($index_conta>6){
                    if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                        if ($valor_conta!=0 && substr($conta_inicio, 0,1)==$cod_conta_sintetica){
                            $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

                            echo '<tr>';
                            echo '<td width="30%" style="border-right: 0;">'.$pla_descricao.'</td>';
                            echo '<td style="border-right: 0;"></td>';
                            echo '<td style="border-right: 0;"></td>';
                            echo '<td ></td>';

                            echo '<td align="right">'.number_format($valor_avencer_conta,2,',','.').'</td>';
                            echo '<td align="right">'.number_format($valor_vencido_conta,2,',','.').'</td>';
                            echo '<td align="right">'.number_format($valor_pago_conta,2,',','.').'</td>';
                            echo '<td align="right">'.number_format($valor_conta,2,',','.').'</td>';

                            if ($tipo_rel=="A"){
                                $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wfazendas,$wfornecedor,$wcc);

                                for ($k=0; $k < count($array_contas); $k++) { 
                                    if ($k==0){

                                        echo '<tr>';
                                        echo '<td width="8%" style="color: #bfbdbd; font-size: 11px;">Documento/Fazenda</td>';
                                        echo '<td width="38%" style="color: #bfbdbd; font-size: 11px;">Fonte Pagadora</td>';
                                        echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Emissão</td>';
                                        echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Vencimento</td>';
                                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Valor</td>';
                                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Recebimento</td>';
                                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Vlr Recebido</td>';
                                        echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Situação</td>';
                                        echo '</tr>';
                                        
                                        echo '<tr>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][4].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][6].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                        echo '</tr>';
                                    }
                                    else {
                                        echo '<tr>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][4].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][6].'</td>';
                                        echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                                        echo '</tr>';
                                    }
                                }
                                echo '<tr>';
                                echo '<th class="text-center" style="border-right: 0;"> Conta</th>';
                                echo '<th style="border-right: 0;"></th>';
                                echo '<th style="border-right: 0;"></th>';
                                echo '<th></th>';
                                echo '<th class="text-center"> A Vencer</th>';
                                echo '<th class="text-center"> Vencidos</th>';
                                echo '<th class="text-center"> Pago</th>';
                                echo '<th class="text-center"> Total</th>';
                                echo '</tr>';
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

            if ($valor_conta!=0 && substr($conta_inicio, 0,1)==$cod_conta_sintetica){
                $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

                echo '<tr>';
                echo '<td width="30%" style="border-right: 0;">'.$pla_descricao.'</td>';
                echo '<td style="border-right: 0;"></td>';
                echo '<td style="border-right: 0;"></td>';
                echo '<td ></td>';

                echo '<td align="right">'.number_format($valor_avencer_conta,2,',','.').'</td>';
                echo '<td align="right">'.number_format($valor_vencido_conta,2,',','.').'</td>';
                echo '<td align="right">'.number_format($valor_pago_conta,2,',','.').'</td>';
                echo '<td align="right">'.number_format($valor_conta,2,',','.').'</td>';

                if ($tipo_rel=="A"){
                    $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wfazendas,$wfornecedor,$wcc);

                    for ($k=0; $k < count($array_contas); $k++) { 
                        if ($k==0){
                            echo '<tr>';
                            echo '<td width="8%" style="color: #bfbdbd; font-size: 11px;">Documento/Fazenda</td>';
                            echo '<td width="38%" style="color: #bfbdbd; font-size: 11px;">Fonte Pagadora</td>';
                            echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Emissão</td>';
                            echo '<td width="7%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Vencimento</td>';
                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Valor</td>';
                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Recebimento</td>';
                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Vlr Recebido</td>';
                            echo '<td width="10%" style="color: #bfbdbd; font-size: 11px; text-align: center;">Situação</td>';
                            echo '</tr>';

                            echo '<tr>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][4].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][6].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][7].'</td>';
                            echo '</tr>';
                        }
                        else {
                            echo '<tr>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][0].' '.$array_contas[$k][10].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][1].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][2].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][3].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][4].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px;">'.$array_contas[$k][5].'</td>';
                            echo '<td style="color: #bfbdbd; font-size: 11px; text-align: right;">'.$array_contas[$k][6].'</td>';
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
    }
}

    function ler_notas($conector, $data_sistema,$tipo_data,$data_inicial,$data_final,$conta_inicio,$conta_fim,$wfazendas,$wfornecedor,$wcc){

        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
            INNER JOIN contas_pagar
                    ON bcp_id=ctp_id
            WHERE bcp_data_pagamento >='$data_inicial' AND
                  bcp_data_pagamento <='$data_final' AND
                  ctp_codigo_conta='$conta_inicio'" . $wfazendas . $wcc . $wfornecedor . 
            " ORDER BY ctp_codigo_conta, bcp_data_pagamento, bcp_numero_id  ASC"); 

        $num_rows_conta = mysqli_num_rows($contas_pag);
        $ind_array = 0;

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
            $data_pagamento = $registro_contas_pag->bcp_data_pagamento;
            
            $tbl_pessoa = mysqli_query($conector, "SELECT tbl_pessoa_nome
            FROM tbl_pessoa 
            WHERE tbl_pessoa_id='$codigo_fazenda'");
                                                                                         
            $registro_pessoa = mysqli_fetch_object($tbl_pessoa);
            $desc_pessoa = utf8_encode($registro_pessoa->tbl_pessoa_nome);
            
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

            $dados = [$doc_imp,$razao,$emissao_edi->format('d/m/Y'), $vencimento_edi->format('d/m/Y'),number_format($total_pagar,2,',','.'), $data_pag_edi, number_format($valor_pago,2,',','.'), $desc_situacao, $desc_conta_pgto, $numero_cheque, $desc_pessoa];

            $array_contas[$ind_array] = $dados;

            $ind_array++;
        }
        return $array_contas;
    }


?>
</tbody>
</table>
                                            <div class="row">  
                                                <div class="col-md-12">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_pagar()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="listar_contas_pagar_excel()">Excel</button>
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
                            <h4 class="modal-title">Relatório Análise de Pagamento</h4>
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
                            <h4 class="modal-title">Relatório Análise de Pagamento - Mensagem</h4>
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




