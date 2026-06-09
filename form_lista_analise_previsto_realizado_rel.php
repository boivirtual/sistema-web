<?php
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
    $ano = $_REQUEST["ano"];
    $tipo_rel = $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $scrollY = ($tipo_rel == 1 || $tipo_rel == 2) ? "200px" : "300px";

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

    //APURAR SALDO ANTERIOR REALIZADO
    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    $sql = "SELECT * FROM baixa_contas_receber
        INNER JOIN contas_receber
                ON bcr_id=ctr_id
             WHERE bcr_data_pagamento<'$data_inicial'" . $wcentro_custo_rec . $wlocal_rec; 

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
        WHERE bcp_data_pagamento<'$data_inicial'" . $wcentro_custo_pag  . $wlocal_pag; 

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

    $previsao_conta = mysqli_query($conector, "SELECT * FROM tbl_previsao_conta
        INNER JOIN tbl_plano_contas 
                ON tbl_previsao_conta_codigo=tbl_plano_contas_codigo_id
             WHERE tbl_previsao_conta_ano = '$anoAnterior'"  . $wlocal_previsao);
    
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

                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                            WHERE tbl_plano_contas_nivel=3 AND 
                                  tbl_plano_contas_lixeira=0 
                            ORDER BY tbl_plano_contas_codigo_id ASC"); 

                    WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 

                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
                        $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

                        $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

                        $valor_pago = 0;
                        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                            INNER JOIN contas_receber
                                    on ctr_id=bcr_id                                  
                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  bcr_data_pagamento >='$data_inicial' AND
                                  bcr_data_pagamento <='$data_final'" . $wcentro_custo_rec  . $wlocal_rec . 
                                  "ORDER BY bcr_data_pagamento"); 
                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            WHILE ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $data_pagamento = $registro_contas_rec->bcr_data_pagamento;
                                $mes = (int)substr($data_pagamento, 5, 2);

                                $valor_pago = $registro_contas_rec->bcr_valor_pagamento;

                                $total_realizado[$conta_nivel_1][$mes]+=$valor_pago;
                                $total_realizado[$conta_nivel_1][13]+=$valor_pago;
                                $total_realizado[$conta_nivel_2][$mes]+=$valor_pago;
                                $total_realizado[$conta_nivel_2][13]+=$valor_pago;
                                $total_realizado[$codigo_conta][$mes]+=$valor_pago;
                                $total_realizado[$codigo_conta][13]+=$valor_pago;
                                $valor_credito[$mes]+=$valor_pago;
                                $tem_valor[$conta_nivel_1]="S";
                                $tem_valor[$conta_nivel_2]="S";
                                $tem_valor[$codigo_conta]="S";

                            } // fim WHILE contas a receber
                        } // fim if rows contas receber

                        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                            INNER JOIN contas_pagar
                                    on ctp_id=bcp_id
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  bcp_data_pagamento >='$data_inicial' AND
                                  bcp_data_pagamento <='$data_final'" . $wcentro_custo_pag  . $wlocal_pag . 
                                  " ORDER BY bcp_data_pagamento"); 

                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            WHILE ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 

                                $data_pagamento = $registro_contas_pag->bcp_data_pagamento;
                                $mes = (int)substr($data_pagamento, 5, 2);
                                $valor_pago = $registro_contas_pag->bcp_valor_pagamento;

                                $total_realizado[$conta_nivel_1][$mes]+=$valor_pago;
                                $total_realizado[$conta_nivel_1][13]+=$valor_pago;
                                $total_realizado[$conta_nivel_2][$mes]+=$valor_pago;
                                $total_realizado[$conta_nivel_2][13]+=$valor_pago;
                                $total_realizado[$codigo_conta][$mes]+=$valor_pago;
                                $total_realizado[$codigo_conta][13]+=$valor_pago;
                                $valor_debito[$mes]+=$valor_pago;

                                if ($valor_pago!=0) {
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }
                            } // fim WHILE contas a pagar
                        } // fim if rows contas pagar

                        $previsao_conta = mysqli_query($conector, "SELECT *  FROM tbl_previsao_conta
                                WHERE tbl_previsao_conta_codigo='$codigo_conta' AND 
                                      tbl_previsao_conta_ano = '$ano'"  . $wlocal_previsao);
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

        $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
            WHERE tbl_plano_contas_nivel=3 AND 
                  tbl_plano_contas_lixeira=0 
            ORDER BY tbl_plano_contas_codigo_id ASC"); 

        WHILE ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
            $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
            $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
            $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);
            $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                INNER JOIN contas_receber
                        ON ctr_id=bcr_id              
                WHERE ctr_codigo_conta='$codigo_conta' AND
                      bcr_data_pagamento >='$data_inicial' AND
                      bcr_data_pagamento <='$data_final'" . $wcentro_custo_rec  . $wlocal_rec . 
               "ORDER BY bcr_data_pagamento"); 

            $num_rows_contas_rec = mysqli_num_rows($contas_rec);

            if ($num_rows_contas_rec!=0){
                WHILE ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                    $data_pagamento = $registro_contas_rec->bcr_data_pagamento;
                    $mes = (int)substr($data_pagamento, 5, 2);
                    $valor_pago = $registro_contas_rec->bcr_valor_pagamento;

                    if ($valor_pago!=0) {
                        $total_realizado[$conta_nivel_1][$mes]+=$valor_pago;
                        $total_realizado[$conta_nivel_1][13]+=$valor_pago;
                        $total_realizado[$conta_nivel_2][$mes]+=$valor_pago;
                        $total_realizado[$conta_nivel_2][13]+=$valor_pago;
                        $total_realizado[$codigo_conta][$mes]+=$valor_pago;
                        $total_realizado[$codigo_conta][13]+=$valor_pago;
                        $valor_credito[$mes]+=$valor_pago;
                        $tem_valor[$conta_nivel_1]="S";
                        $tem_valor[$conta_nivel_2]="S";
                        $tem_valor[$codigo_conta]="S";
                    }
                } // fim WHILE contas a receber
            } // fim if rows contas receber

            $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                INNER JOIN contas_pagar
                        ON ctp_id=bcp_id
                WHERE ctp_codigo_conta='$codigo_conta' AND
                      bcp_data_pagamento >='$data_inicial' AND
                      bcp_data_pagamento <='$data_final'" . $wcentro_custo_pag  . $wlocal_pag . 
                " ORDER BY bcp_data_pagamento"); 

            $num_rows_contas_pag = mysqli_num_rows($contas_pag);

            if ($num_rows_contas_pag!=0){
                WHILE ($registro_contas_pag = mysqli_fetch_object($contas_pag)){
                    $data_pagamento = $registro_contas_pag->bcp_data_pagamento;
                    $mes = (int)substr($data_pagamento, 5, 2);
                    $valor_pago = $registro_contas_pag->bcp_valor_pagamento;

                    if ($valor_pago!=0) {
                        $total_realizado[$conta_nivel_1][$mes]+=$valor_pago;
                        $total_realizado[$conta_nivel_1][13]+=$valor_pago;
                        $total_realizado[$conta_nivel_2][$mes]+=$valor_pago;
                        $total_realizado[$conta_nivel_2][13]+=$valor_pago;
                        $total_realizado[$codigo_conta][$mes]+=$valor_pago;
                        $total_realizado[$codigo_conta][13]+=$valor_pago;
                        $valor_debito[$mes]+=$valor_pago;
                        $tem_valor[$conta_nivel_1]="S";
                        $tem_valor[$conta_nivel_2]="S";
                        $tem_valor[$codigo_conta]="S";
                    }
                } // fim WHILE contas a pgar
            } // fim if rows contas pagar
        } // fim WHILE plano de contas

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
                      tbl_previsao_conta_ano = '$ano'" . $wlocal_previsao);
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
                       class="table table-advance table-hover table-borderless"
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
        table = $('#tabela_analise_previsto_realizado').DataTable({
            fixedColumns: {
                heightMatch: 'none'
            },
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
        });

        setTimeout(function () {
            table.columns.adjust().draw();
        }, 100);

        $(window).on('resize', function () {
            setTimeout(function () {
                table.settings()[0].oScroll.sY = calcularScrollTabela();
                $('.dataTables_scrollBody').css('max-height', calcularScrollTabela());
                $('.dataTables_scrollBody').css('height', calcularScrollTabela());
                table.columns.adjust().draw();
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

