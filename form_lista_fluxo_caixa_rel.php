<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $local_filtro = $_REQUEST["fazenda"];
    $conta_pagamento = $_REQUEST["conta_pagamento"];
    $codigo_cc = $_REQUEST["c_custo"];
    $ano = $_REQUEST["ano"];
    $mes = $_REQUEST["mes"];
    $tipo_rel = $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $cc= array();
    $matriz_itens = explode(",", $codigo_cc);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $cc[$i]=$matriz_itens[$i];
    }

    $cc = implode(',', $cc);
    $cc = substr($cc,0, -1);

    $wcc_ctp = '';
    $wcc_ctr = '';

    if ($codigo_cc!='') {
        $wcc_ctp = " AND ctp_codigo_centro_custos IN(";
        $wcc_ctp.= $cc;
        $wcc_ctp.= ")";
    }

    if ($codigo_cc!='') {
        $wcc_ctr = " AND ctr_codigo_c_custo IN(";
        $wcc_ctr.= $cc;
        $wcc_ctr.= ")";
    }

    $c_pag= array();
    $matriz_itens = explode(",", $conta_pagamento);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $c_pag[$i]=$matriz_itens[$i];
    }

    $c_pag = implode(',', $c_pag);
    $c_pag = substr($c_pag,0, -1);

    $wcpag_ctp = '';
    $wcpag_ctr = '';

    if ($conta_pagamento!=0) {
        $wcpag_ctp = " AND ctp_conta_pagamento IN(";
        $wcpag_ctp.= $c_pag;
        $wcpag_ctp.= ")";
    }

    if ($conta_pagamento!=0) {
        $wcpag_ctr = " AND ctr_codigo_conta_recebimento IN(";
        $wcpag_ctr.= $c_pag;
        $wcpag_ctr.= ")";
    }


    $local= array();
    $matriz_itens = explode(",", $local_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal_ctp = '';
    $wlocal_ctr = '';

    if ($local_filtro!='') {
        $wlocal_ctp = " AND ctp_codigo_fazenda IN(";
        $wlocal_ctp.= $local;
        $wlocal_ctp.= ")";
    }

    if ($local_filtro!='') {
        $wlocal_ctr = " AND ctr_codigo_fazenda IN(";
        $wlocal_ctr.= $local;
        $wlocal_ctr.= ")";
    }

    $data_inicial = $ano . '-' . $mes . '-01';

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
    table.dataTable thead th { border-bottom: 0; padding-bottom: 5px; padding-top: 5px;}

    table.dataTable { width: 30%;}

    /*div.container {
        width: 60%;
        margin: 10px;
    }*/
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
            <span class="titulo">Fluxo de Caixa Diário</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Fluxo de Caixa Diário</h3>
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

                                                <input type="hidden" id="mes_diario"
                                                    <?php echo "value='".$mes."'";?>>

                                                <input type="hidden" id="ano_diario"
                                                    <?php echo "value='".$ano."'";?>>

                                                <input type="hidden" id="tipo_rel"
                                                    <?php echo "value='".$tipo_rel."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                                <input type="hidden" id="codigo_cc"
                                                    <?php echo "value='".$codigo_cc."'";?>>

                                                <input type="hidden" id="codigo_fazenda"
                                                    <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="conta_pagamento"
                                                    <?php echo "value='".$conta_pagamento."'";?>>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel">Filtros:</label>
                                                    span><?php echo $descricao_filtro;?></span>
                                                </div>

                                                <div class="col-md-4">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_caixa()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="listar_fluxo_caixa_excel()">Excel</button>
                                                </div>
                                            </div>

<hr align="center">

<table id="tabela_caixa" class="table table-advance table-hover table-borderless">

<tbody>  
<?php
    if ($tipo_rel==2) { // Realizado - apurar saldo anterior realizado
        $total_saldo_anterior=0;
        $total_recebido=0;
        $total_pago=0;
        $total_geral_recebido=0;
        $total_geral_pago=0;
        $total_geral_mes=0;
        $total_geral_final=0;

        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
            INNER JOIN contas_receber
                    ON bcr_id=ctr_id
                 WHERE bcr_data_pagamento<'$data_inicial'" . $wcc_ctr . $wcpag_ctr . $wlocal_ctr); 

        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

        if ($num_rows_contas_rec!=0){
            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
                $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
                $total_recebido+=$valor_pago;
            } 
        }

        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
            INNER JOIN contas_pagar
                    ON bcp_numero_id=ctp_numero_doc AND 
                       bcp_parcela=ctp_parcela AND 
                       bcp_codigo_fornecedor=ctp_codigo_fornecedor
                 WHERE bcp_data_pagamento<'$data_inicial'" . $wcc_ctp . $wcpag_ctp .$wlocal_ctp); 

        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

        if ($num_rows_contas_pag!=0){
            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
                $total_pago+=$valor_pago;
            } 
        }
                        
        $total_saldo_anterior+= $total_recebido - $total_pago;
        $total_saldo = $total_saldo_anterior;

        //fim apurar saldo anterior realizado

        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        $data_lista = date("Y-m-d", strtotime('-1 day',strtotime($data_inicial)));

        for ($i=1; $i <= $dias_mes ; $i++) { 
            $data_dia[$i] = 0;
            $valor_recebimentos_diario[$i] = 0;
            $valor_pagamentos_diario[$i] = 0;
            $valor_saldo_diario[$i] = 0;
        }

        for ($i=0; $i < $dias_mes ; $i++) { 
            $total_recebido = 0;
            $total_pago = 0;

            $data_lista = date("Y-m-d", strtotime('+1 day',strtotime($data_lista)));
            $data_edi = new DateTime($data_lista);

            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                INNER JOIN contas_receber
                        ON bcr_id=ctr_id
                     WHERE bcr_data_pagamento='$data_lista'" . $wcc_ctr . $wcpag_ctr . $wlocal_ctr); 

            $num_rows_contas_rec = mysqli_num_rows($contas_rec);

            if ($num_rows_contas_rec!=0){
                while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                    $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
                    $total_recebido+=$valor_pago;
                    $total_geral_recebido+=$valor_pago;
                } 
            }

            $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                INNER JOIN contas_pagar
                        ON bcp_numero_id=ctp_numero_doc AND 
                           bcp_parcela=ctp_parcela AND 
                           bcp_codigo_fornecedor=ctp_codigo_fornecedor
                     WHERE bcp_data_pagamento='$data_lista'" . $wcc_ctp . $wcpag_ctp . $wlocal_ctp); 

            $num_rows_contas_pag = mysqli_num_rows($contas_pag);

            if ($num_rows_contas_pag!=0){
                while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){
                    $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
                    $total_pago+=$valor_pago;
                    $total_geral_pago+=$valor_pago;
                } 
            }
                        
            $total_saldo+= $total_recebido - $total_pago;

            $total_geral_mes+=$total_recebido - $total_pago;
            $total_geral_final=$total_saldo;
                        
            $dia = (int)substr($data_lista,8,2);
            $data_dia[$dia] = $data_edi;
            $valor_recebimentos_diario[$dia] = $total_recebido;
            $valor_pagamentos_diario[$dia] = $total_pago;
            $valor_saldo_diario[$dia] = $total_saldo;
        }

        for ($i=1; $i <= $dias_mes ; $i++) { 

            echo '<tr>';
            echo '<td align="center">'.$data_dia[$i]->format('d/m/Y').'</td>';
            echo '<td align="right">'.number_format($valor_recebimentos_diario[$i],2,',','.').'</td>';
            echo '<td align="right">'.number_format($valor_pagamentos_diario[$i],2,',','.').'</td>';

            if ($valor_saldo_diario[$i]<0) {
                echo '<td align="right" style="color: #FF2F2F">'.number_format($valor_saldo_diario[$i],2,',','.').'</td>';
            }
            else if ($total_saldo>0) {
                echo '<td align="right" style="color: #00B900">'.number_format($valor_saldo_diario[$i],2,',','.').'</td>';
            }
            else {
                echo '<td align="right">'.number_format($valor_saldo_diario[$i],2,',','.').'</td>';
            }
            echo '</tr>';
        }

    } // fim opcrel = 2
    else { // Nao Realizado - apurar saldo anterior nao realizado
        $total_saldo_anterior=0;
        $total_recebido=0;
        $total_pago=0;
        $total_geral_recebido=0;
        $total_geral_pago=0;
        $total_geral_mes=0;
        $total_geral_final=0;

        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
            WHERE ctr_data_vencimento<'$data_inicial' AND 
                  ctr_situacao=''" . $wcc_ctr . $wcpag_ctr . $wlocal_ctr); 

        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

        if ($num_rows_contas_rec!=0){
            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
                $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
                $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
                $valor_juros = $registro_contas_rec->ctr_valor_juros;
                $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                $total_recebido+=$vlr_pagamento;
            } 
        }

        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
            WHERE ctp_data_vencimento<'$data_inicial' AND 
                  ctp_situacao=''" . $wcc_ctp . $wcpag_ctp . $wlocal_ctp); 

        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

        if ($num_rows_contas_pag!=0){
            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
                $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
                $valor_juros = $registro_contas_pag->ctp_valor_juros;
                $valor_outro = $registro_contas_pag->ctp_outro_valor;
                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                $total_pago+=$vlr_pagamento;
            } 
        }
                        
        $total_saldo_anterior+= $total_recebido - $total_pago;
        $total_saldo = $total_saldo_anterior;
        $total_recebido =0;
        $total_pago=0;                    

        //fim apurar saldo anterior não realizado
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
        $data_lista = date("Y-m-d", strtotime('-1 day',strtotime($data_inicial)));

        for ($i=1; $i <= $dias_mes ; $i++) { 
            $data_dia[$i] = 0;
            $valor_recebimentos_diario[$i] = 0;
            $valor_pagamentos_diario[$i] = 0;
            $valor_saldo_diario[$i] = 0;
        }

        for ($i=0; $i < $dias_mes ; $i++) { 
            $total_recebido = 0;
            $total_pago = 0;

            $data_lista = date("Y-m-d", strtotime('+1 day',strtotime($data_lista)));
            $data_edi = new DateTime($data_lista);

            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                WHERE ctr_data_vencimento='$data_lista' AND 
                      ctr_situacao=''" . $wcc_ctr . $wcpag_ctr . $wlocal_ctr); 

            $num_rows_contas_rec = mysqli_num_rows($contas_rec);

            if ($num_rows_contas_rec!=0){
                while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                    $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
                    $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
                    $valor_juros = $registro_contas_rec->ctr_valor_juros;
                    $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
                    $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                    $total_recebido+=$vlr_pagamento;
                    $total_geral_recebido+=$vlr_pagamento;
                } 
            }

            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                WHERE ctp_data_vencimento='$data_lista' AND 
                      ctp_situacao=''" . $wcc_ctp . $wcpag_ctp . $wlocal_ctp); 

            $num_rows_contas_pag = mysqli_num_rows($contas_pag);

            if ($num_rows_contas_pag!=0){
                while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                    $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
                    $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
                    $valor_juros = $registro_contas_pag->ctp_valor_juros;
                    $valor_outro = $registro_contas_pag->ctp_outro_valor;
                    $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;
                    $total_pago+=$vlr_pagamento;
                    $total_geral_pago+=$vlr_pagamento;
                } 
            }
                        
            $total_saldo+= $total_recebido - $total_pago;

            $total_geral_mes+=$total_recebido - $total_pago;
            $total_geral_final=$total_saldo;
                        
            $dia = (int)substr($data_lista,8,2);
            $data_dia[$dia] = $data_edi;
            $valor_recebimentos_diario[$dia] = $total_recebido;
            $valor_pagamentos_diario[$dia] = $total_pago;
            $valor_saldo_diario[$dia] = $total_saldo;
        }

        for ($i=1; $i <= $dias_mes ; $i++) { 
            echo '<tr>';
            echo '<td align="center">'.$data_dia[$i]->format('d/m/Y').'</td>';
            echo '<td align="right">'.number_format($valor_recebimentos_diario[$i],2,',','.').'</td>';
            echo '<td align="right">'.number_format($valor_pagamentos_diario[$i],2,',','.').'</td>';

            if ($valor_saldo_diario[$i]<0) {
                echo '<td align="right" style="color: #FF2F2F">'.number_format($valor_saldo_diario[$i],2,',','.').'</td>';
            }
            else if ($total_saldo>0) {
                echo '<td align="right" style="color: #00B900">'.number_format($valor_saldo_diario[$i],2,',','.').'</td>';
            }
            else {
                echo '<td align="right">'.number_format($valor_saldo_diario[$i],2,',','.').'</td>';
            }
            echo '</tr>';
        }

    }

?>
</tbody>

<thead> 
    <?php
        if ($tipo_rel==2) {
            echo '<tr>';
            echo '<th class="text-center">Data</th>';
            echo '<th class="text-right">Recebimentos Realizados</th>';
            echo '<th class="text-right">Pagamentos Realizados</th>';
            echo '<th class="text-right">Saldo</th>';
            echo '</tr>';

            echo '<tr>';
            echo '<th class="text-right" style="font-weight:bold;">Saldo Anterior</th>';
            echo '<th></th>';
            echo '<th></th>';

            if ($total_saldo_anterior<0) {
                echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</th>';
            }
            else if ($total_saldo_anterior>0) {
                echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</th>';
            }
            else {
                echo '<th class="text-right" style="font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</th>';
            }
            echo '</tr>';

            echo '<tr>';
            echo '<th class="text-right" style="font-weight:bold;">Saldo do Mês</th>';
            echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_recebido,2,',','.').'</th>';
            echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_pago,2,',','.').'</th>';

            if ($total_geral_mes<0) {
                echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</th>';
            }
            else if ($total_geral_mes>0) {
                echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</th>';
            }
            else {
                echo '<th class="text-right" style="font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</th>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th class="text-right" style="font-weight:bold;">Saldo Final</th>';
            echo '<th></th>';
            echo '<th></th>';

            if ($total_geral_final<0) {
                echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</th>';
            }
            else if ($total_geral_final>0) {
                echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</th>';
            }
            else {
                echo '<th class="text-right" style="font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</th>';
            }

            echo '</tr>';
        } 
        else {
            echo '<tr>';
            echo '<th class="text-center">Data</th>';
            echo '<th class="text-right">Recebimentos não Realizados</th>';
            echo '<th class="text-right">Pagamentos não Realizados</th>';
            echo '<th class="text-right">Saldo</th>';
            echo '</tr>';

            echo '<tr>';
            echo '<th class="text-right" style="font-weight:bold;">Saldo Anterior</th>';
            echo '<th width="25%"></th>';
            echo '<th width="25%"></th>';

            if ($total_saldo_anterior<0) {
                echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</th>';
            }
            else if ($total_saldo_anterior>0) {
                echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</th>';
            }
            else {
                echo '<th class="text-right" style="font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</th>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th class="text-right" style="font-weight:bold;">Saldo do Mês</th>';
            echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_recebido,2,',','.').'</th>';
            echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_pago,2,',','.').'</th>';

            if ($total_geral_mes<0) {
                echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</th>';
            }
            else if ($total_geral_mes>0) {
                echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</th>';
            }
            else {
                echo '<th class="text-right" style="font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</th>';
            }

            echo '</tr>';

            echo '<tr>';
            echo '<th class="text-right" style="font-weight:bold;">Saldo Final</th>';
            echo '<th></th>';
            echo '<th></th>';

            if ($total_geral_final<0) {
                echo '<th class="text-right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</th>';
            }
            else if ($total_geral_final>0) {
                echo '<th class="text-right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</th>';
            }
            else {
                echo '<th class="text-right" style="font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</th>';
            }

            echo '</tr>';

        }

    ?>
</thead>

</table>
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
                            <h4 class="modal-title">Relatório Fluxo Caixa</h4>
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
                            <h4 class="modal-title">Relatório Fluxo Caixa - Mensagem</h4>
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
        $(document).ready(function() {
            var table = $('#tabela_caixa').DataTable( {
                scrollY:  "250px",
                scrollX:  false,
                scrollCollapse: false,                
                paging:   false,
                search:   false,
                ordering: false,
                info: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Registros encontrados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                },

                "dom": '<"top">rt<"bottom"ip><"clear">'
            });
        });

    </script>
    
</body>
</html>




