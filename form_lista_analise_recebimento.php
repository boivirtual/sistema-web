<?php
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $conta_inicio=0;    
    $data_inicio=0; 
    $data_fim=0;
    $tipo_data='';
    $tipo_rel='';
    $a_vencer='';
    $vencidos='';
    $pagos='';
    $criterio="";
    $linha=0;

    if(isset($_REQUEST["conta"]) && $_REQUEST["conta"]!=0) {
        $conta_inicio=$_REQUEST["conta"];
    }

    if ($conta_inicio==0 || $conta_inicio==1000000){
        $conta_inicio=1000000;
        $conta_fim=1999999;
    }
    else if (substr($conta_inicio, 3, 4)==0){
        $conta_fim=substr($conta_inicio, 0, 3) . 9999;
    }
    else {
        $conta_fim=$conta_inicio;
        $conta_inicio=substr($conta_fim, 0, 3) . '0000';
    }

    if (isset($_REQUEST["tipo_data"]) && $_REQUEST["tipo_data"]!="") {
          $tipo_data= $_REQUEST["tipo_data"];
    }
                
    if (isset($_REQUEST["tipo_rel"]) && $_REQUEST["tipo_rel"]!="") {
          $tipo_rel= $_REQUEST["tipo_rel"];
    }

    if (isset($_REQUEST["codigo_cliente"]) && $_REQUEST["codigo_cliente"]!=0) {
        $codigo_cli = $_REQUEST["codigo_cliente"];
        $cli = mysqli_query($conector, "SELECT tbl_pessoa_nome FROM tbl_pessoa
                                                           WHERE tbl_pessoa_id ='$codigo_cli'"); 
        $registro_cli = mysqli_fetch_object($cli);  
        $nome_cli = $registro_cli->tbl_pessoa_nome;

    }
    else {
        $codigo_cli = 0;
        $nome_cli = "Todos";
    }
                    
    if(isset($_REQUEST["data_inicio"]) && $_REQUEST["data_inicio"]!=0 && 
       isset($_REQUEST["data_fim"]) && $_REQUEST["data_fim"]!=0) {
        $data_inicio=$_REQUEST["data_inicio"];
        $data_fim=$_REQUEST["data_fim"];    
        $data_inicio_edi = new DateTime($data_inicio);
        $data_fim_edi = new DateTime($data_fim);
    }
    else {
        $data_inicio_edi = "";
        $data_fim_edi = "";
    }

    if ($tipo_data==""){
        $tipo_data='V';
    }

    if ($tipo_data=="E"){ 
        $desc_tipo_data=" por Data de Emissão";
    }
    else if ($tipo_data=="V"){
        $desc_tipo_data=" por Data de Vencimento";  
    }  
    else {
        $desc_tipo_data=" por Data de Pagamento";
    }

    if ($tipo_rel=="A"){
        $desc_tipo_rel = " Analítico";
    }
    else {
        $desc_tipo_rel = " Sintético";
    }

    if(isset($_REQUEST["centro_custos"])) {
        if ($_REQUEST["centro_custos"]!=0) {
            $codigo_cc=$_REQUEST["centro_custos"];
            $centro_custos = mysqli_query($conector, "SELECT tbl_cc_descricao FROM tbl_centro_custo
                                                 WHERE tbl_cc_codigo_id ='$codigo_cc'"); 
            $registro_cc = mysqli_fetch_object($centro_custos);  
            $desc_centro_custos = utf8_encode($registro_cc->tbl_cc_descricao);
        }
        else {
            $desc_centro_custos = 'Todos';
            $codigo_cc=0;
        }
    }
    else {
        $desc_centro_custos = 'Todos';
        $codigo_cc=0;
    }

    @ session_start(); 

    $_SESSION['data_inicio_ctr_rel']=$_REQUEST["data_inicio"];
    $_SESSION['data_fim_ctr_rel']=$_REQUEST["data_fim"];
    $_SESSION['tipo_data_ctr_rel']=$_REQUEST["tipo_data"];
    $_SESSION['tipo_rel_ctr_rel']=$_REQUEST["tipo_rel"]; 
    $_SESSION['codigo_c_custo_ctr_rel']=$codigo_cc; 
    $_SESSION['codigo_conta_ctr_rel']=$_REQUEST["conta"]; 
    $_SESSION['codigo_cliente_ctr_rel']=$codigo_cli;
    
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
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body>
	<section class="panel">
        <?php
            if ($tipo_rel=="A") {
                echo '<table id="tabela_analise_recebimento" class="table table-striped table-bordered table-advance table-hover" width="180%">';

            }
            else {
                echo '<table class="table table-striped table-bordered table-advance table-hover" id="tabela_analise_recebimento" width="100%" >'; 
            }
        ?>    

        <thead>
            <?php
                if ($tipo_rel=="A") {
                    echo '<tr>';
                    echo '<th class="text-center"> Conta</th>';
                    echo '<th class="text-center"> Recebido</th>';
                    echo '<th class="text-center"> A Vencer</th>';
                    echo '<th class="text-center"> Vencidas</th>';
                    echo '<th class="text-center"> Total</th>';
                    echo '<th class="text-center"> Documento</th>';
                    echo '<th class="text-center"> Fonte Pagadora</th>';
                    echo '<th class="text-center"> Emissão</th>';
                    echo '<th class="text-center"> Vencimento</th>';
                    echo '<th class="text-center"> Valor</th>';
                    echo '<th class="text-center"> Recebimento</th>';
                    echo '<th class="text-center"> Valor</th>';
                    echo '<th class="text-center"> Situação</th>';
                    echo '<th class="text-center"> Forma Rec</th>';
                    echo '<th class="text-center"> Cheque</th>';
                    echo '</tr>';
                }
                else {
                    echo '<tr>';
                    echo '<th class="text-center"> Conta</th>';
                    echo '<th class="text-center"> Recebido</th>';
                    echo '<th class="text-center"> A Vencer</th>';
                    echo '<th class="text-center"> Vencidas</th>';
                    echo '<th class="text-center"> Total</th>';
                    echo '</tr>';
                }
            ?>
        </thead>

        <tbody>
            <?php
                // monta array das contas

                $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                                 WHERE tbl_plano_contas_codigo_id >='$conta_inicio' AND 
                                                                       tbl_plano_contas_codigo_id <='$conta_fim'
                                                              ORDER BY tbl_plano_contas_codigo_id ASC"); 

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

                while ($registro_plano_contas = mysqli_fetch_object($plano_contas)){  
                    $cod_conta = $registro_plano_contas->tbl_plano_contas_codigo_id;
                    $descricao_conta = $registro_plano_contas->tbl_plano_contas_descricao;                                   

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
                if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc==0 && $codigo_cli==0){
                    if ($tipo_data=="E"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_emissao >='$data_inicio' and
                                                                ctr_data_emissao <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim' and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="V"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_vencimento >='$data_inicio' and
                                                                ctr_data_vencimento <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim'
                                                                and
                                                                ctr_lixeira=0  
                                                       ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="P"){
                        $contas_rec = mysqli_query($conector, "SELECT * 
                                                       FROM baixa_contas_receber
                                                 INNER JOIN contas_receber
                                                         ON bcr_id=ctr_id
                                                      WHERE bcr_data_pagamento >='$data_inicio' and
                                                            bcr_data_pagamento <='$data_fim' and
                                                            ctr_codigo_conta>='$conta_inicio' and 
                                                            ctr_codigo_conta<='$conta_fim'
                                                            and
                                                                ctr_lixeira=0 
                                                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
                    }
                }
                else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc!=0 && $codigo_cli==0){
                    if ($tipo_data=="E"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_emissao >='$data_inicio' and
                                                                ctr_data_emissao <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim' and 
                                                                ctr_codigo_c_custo='$codigo_cc'
                                                                and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="V"){
                            $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_vencimento >='$data_inicio' and
                                                                ctr_data_vencimento <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim'   and 
                                                                ctr_codigo_c_custo='$codigo_cc'
                                                                and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="P"){
                        $contas_rec = mysqli_query($conector, "SELECT * 
                                                       FROM baixa_contas_receber
                                                 INNER JOIN contas_receber
                                                         ON bcr_id=ctr_id
                                                      WHERE bcr_data_pagamento >='$data_inicio' and
                                                            bcr_data_pagamento <='$data_fim' and
                                                            ctr_codigo_conta>='$conta_inicio' and 
                                                            ctr_codigo_conta<='$conta_fim'  and 
                                                            ctr_codigo_c_custo='$codigo_cc'
                                                            and
                                                                ctr_lixeira=0 
                                                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
                    }
                }
                else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc!=0 && $codigo_cli!=0){
                    if ($tipo_data=="E"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_emissao >='$data_inicio' and
                                                                ctr_data_emissao <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim' and 
                                                                ctr_codigo_c_custo='$codigo_cc' and 
                                                                ctr_codigo_cliente_fornecedor='$codigo_cli' 
                                                                and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="V"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_vencimento >='$data_inicio' and
                                                                ctr_data_vencimento <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim'   and 
                                                                ctr_codigo_c_custo='$codigo_cc' and 
                                                                ctr_codigo_cliente_fornecedor='$codigo_cli'
                                                                and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="P"){
                        $contas_rec = mysqli_query($conector, "SELECT * 
                                                       FROM baixa_contas_receber
                                                 INNER JOIN contas_receber
                                                         ON bcr_id=ctr_id
                                                      WHERE bcr_data_pagamento >='$data_inicio' and
                                                            bcr_data_pagamento <='$data_fim' and
                                                            ctr_codigo_conta>='$conta_inicio' and 
                                                            ctr_codigo_conta<='$conta_fim'  and 
                                                            ctr_codigo_c_custo='$codigo_cc' and 
                                                            ctr_codigo_cliente_fornecedor='$codigo_cli'
                                                            and
                                                                ctr_lixeira=0 
                                                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
                    }
                }
                else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc==0 && $codigo_cli!=0){
                    if ($tipo_data=="E"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_emissao >='$data_inicio' and
                                                                ctr_data_emissao <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim' and 
                                                                ctr_codigo_cliente_fornecedor='$codigo_cli' and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="V"){
                        $contas_rec = mysqli_query($conector, "SELECT *
                                                           FROM contas_receber
                                                          WHERE ctr_data_vencimento >='$data_inicio' and
                                                                ctr_data_vencimento <='$data_fim' and
                                                                ctr_codigo_conta>='$conta_inicio' and 
                                                                ctr_codigo_conta<='$conta_fim'   and 
                                                                ctr_codigo_cliente_fornecedor='$codigo_cli' and
                                                                ctr_lixeira=0 
                                                       ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
                    }
                    else if ($tipo_data=="P"){
                        $contas_rec = mysqli_query($conector, "SELECT * 
                                                       FROM baixa_contas_receber
                                                 INNER JOIN contas_receber
                                                         ON bcr_id=ctr_id
                                                      WHERE bcr_data_pagamento >='$data_inicio' and
                                                            bcr_data_pagamento <='$data_fim' and
                                                            ctr_codigo_conta>='$conta_inicio' and 
                                                            ctr_codigo_conta<='$conta_fim'  and 
                                                            ctr_codigo_cliente_fornecedor='$codigo_cli' and
                                                                ctr_lixeira=0 
                                                   ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
                    }
                }

                $num_rows_contas_rec = mysqli_num_rows($contas_rec);

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
                                        WHERE bcr_id='$ctr_id''");
                                                                                                 
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
                    else {
                        $codigo_sub_conta = substr($cod_conta, 0, 3);
                        $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
                        $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
                        $valor_juros = $registro_contas_rec->ctr_valor_juros;
                        $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
                        $emissao = $registro_contas_rec->ctr_data_emissao;
                        $vencimento = $registro_contas_rec->ctr_data_vencimento;
                        $situacao = $registro_contas_rec->ctr_situacao;
                        $numero_id = $registro_contas_rec->ctr_numero_doc;
                        $ctr_id = $registro_contas_rec->ctr_id;
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

                $conta_sintetica = substr($conta_inicio, 0,1);
                $conta_sintetica = str_pad($conta_sintetica, 7, "0", STR_PAD_RIGHT);
                $plano_contas = mysqli_query($conector, "SELECT tbl_plano_contas_descricao FROM tbl_plano_contas
                                                                 WHERE tbl_plano_contas_codigo_id ='$conta_sintetica'"); 
                $registro_plano_contas = mysqli_fetch_object($plano_contas);
                $descricao_conta = $registro_plano_contas->tbl_plano_contas_descricao;
                echo '<tr>';
                echo '<td width="15%" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.substr($conta_inicio, 0,1).' - '.$descricao_conta.'</td>';
                echo '<td width="8%" style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_pago_conta_sintetica,2,',','.').'</td>';
                echo '<td width="8%"style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_avencer_conta_sintetica,2,',','.').'</td>';
                echo '<td width="8%"style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_vencida_conta_sintetica,2,',','.').'</td>';
                echo '<td width="8%"style="background-color: #C2E0E0; color: #1C1C1C; font-weight:bold;">'.number_format($total_conta_sintetica,2,',','.').'</td>';

                if ($tipo_rel=="A"){
                    echo '<td width="5%" style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="13%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="12%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                    echo '<td width="3%"style="background-color: #C2E0E0; color: #1C1C1C"></td>';
                }

                echo '</tr>';
                $index_sub_conta = 0;

                for ($i = 0; $i < $qtd_sub_contas; $i++) {
                 
                    $index_sub_conta++;

                    if ($index_sub_conta>6){
                        if ($valor_sub_conta!=0){
                            $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;

                            echo '<tr>';
                            echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.$pla_descricao.'</td>';
                            echo '<td style="background-color:#DEE; color: #696969; font-weight:bold;">'.number_format($valor_pago_sub_conta,2,',','.').'</td>';
                            echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_avencer_sub_conta,2,',','.').'</td>';
                            echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_vencido_sub_conta,2,',','.').'</td>';
                            echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_sub_conta,2,',','.').'</td>';

                            if ($tipo_rel=="A"){
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                                echo '<td style="background-color: #DEE; color: #696969"></td>';
                            }

                            echo '</tr>';
                            $index_conta=0;

                            for ($j = 0; $j < $qtd_contas; $j++) {

                                $index_conta++;

                                if ($index_conta>6){
                                    if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                                        if ($valor_conta!=0){
                                            $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

                                            echo '<tr>';
                                            echo '<td >'.$pla_descricao.'</td>';
                                            echo '<td >'.number_format($valor_pago_conta,2,',','.').'</td>';
                                            echo '<td >'.number_format($valor_avencer_conta,2,',','.').'</td>';
                                            echo '<td >'.number_format($valor_vencido_conta,2,',','.').'</td>';
                                            echo '<td >'.number_format($valor_conta,2,',','.').'</td>';

                                            if ($tipo_rel=="A"){
                                                $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc);

                                                for ($k=0; $k < count($array_contas); $k++) { 
                                                    if ($k==0){
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][0].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][1].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][2].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][3].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][4].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][5].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][6].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][7].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][8].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][9].'</td>';
                                                        echo '</tr>';
                                                    }
                                                    else {
                                                        echo '<tr>';
                                                        echo '<td ></td>';
                                                        echo '<td ></td>';
                                                        echo '<td ></td>';
                                                        echo '<td ></td>';
                                                        echo '<td ></td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][0].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][1].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][2].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][3].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][4].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][5].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][6].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][7].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][8].'</td>';
                                                        echo '<td style="font-size:10px">'.$array_contas[$k][9].'</td>';
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
                }

                if ($valor_sub_conta!=0){

                    $pla_descricao = str_repeat(" ", 6) . $cod_sub_conta . ' - ' . $descricao_sub_conta;
                    echo '<tr>';
                    echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.$pla_descricao.'</td>';
                    echo '<td style="background-color:#DEE; color: #696969; font-weight:bold;">'.number_format($valor_pago_sub_conta,2,',','.').'</td>';
                    echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_avencer_sub_conta,2,',','.').'</td>';
                    echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_vencido_sub_conta,2,',','.').'</td>';
                    echo '<td style="background-color: #DEE; color: #696969; font-weight:bold;">'.number_format($valor_sub_conta,2,',','.').'</td>';

                    if ($tipo_rel=="A"){
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                        echo '<td style="background-color: #DEE; color: #696969"></td>';
                    }
                    echo '</tr>';

                    $index_conta=0;

                    for ($j = 0; $j < $qtd_contas; $j++) {

                        $index_conta++;

                        if ($index_conta>6){
                            if (substr($conta_inicio, 0, 3)==$cod_sub_conta){
                                if ($valor_conta!=0){
                                    $pla_descricao = str_repeat(" ", 16) . $conta_inicio . ' - ' . $descricao_conta;

                                    echo '<tr>';
                                    echo '<td >'.$pla_descricao.'</td>';
                                    echo '<td >'.number_format($valor_pago_conta,2,',','.').'</td>';
                                    echo '<td >'.number_format($valor_avencer_conta,2,',','.').'</td>';
                                    echo '<td >'.number_format($valor_vencido_conta,2,',','.').'</td>';
                                    echo '<td >'.number_format($valor_conta,2,',','.').'</td>';

                                    if ($tipo_rel=="A"){
                                        $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc);

                                        for ($k=0; $k < count($array_contas); $k++) { 
                                            if ($k==0){
                                                echo '<td style="font-size:10px">'.$array_contas[$k][0].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][1].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][2].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][3].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][4].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][5].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][6].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][7].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][8].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][9].'</td>';
                                                echo '</tr>';
                                            }
                                            else {
                                                echo '<tr>';
                                                echo '<td ></td>';
                                                echo '<td ></td>';
                                                echo '<td ></td>';
                                                echo '<td ></td>';
                                                echo '<td ></td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][0].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][1].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][2].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][3].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][4].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][5].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][6].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][7].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][8].'</td>';
                                                echo '<td style="font-size:10px">'.$array_contas[$k][9].'</td>';
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
                        echo '<td>'.$pla_descricao.'</td>';
                        echo '<td>'.number_format($valor_pago_conta,2,',','.').'</td>';
                        echo '<td>'.number_format($valor_avencer_conta,2,',','.').'</td>';
                        echo '<td>'.number_format($valor_vencido_conta,2,',','.').'</td>';
                        echo '<td>'.number_format($valor_conta,2,',','.').'</td>';
                        if ($tipo_rel=="A"){
                            $array_contas=ler_notas($conector, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc);

                            for ($k=0; $k < count($array_contas); $k++) { 
                                if ($k==0){
                                    echo '<td style="font-size:10px">'.$array_contas[$k][0].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][1].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][2].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][3].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][4].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][5].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][6].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][7].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][8].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][9].'</td>';
                                    echo '</tr>';
                                }
                                else {
                                    echo '<tr>';
                                    echo '<td ></td>';
                                    echo '<td ></td>';
                                    echo '<td ></td>';
                                    echo '<td ></td>';
                                    echo '<td ></td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][0].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][1].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][2].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][3].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][4].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][5].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][6].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][7].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][8].'</td>';
                                    echo '<td style="font-size:10px">'.$array_contas[$k][9].'</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        else {
                            echo '</tr>';
                        } 
                    }
                }
            ?>
        </tbody>
        </table>

    <?php
    function ler_notas($conector, $data_sistema,$tipo_data,$data_inicio,$data_fim,$conta_inicio,$codigo_cc){

        if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc==0){
            if ($tipo_data=="E"){
                $contas_rec = mysqli_query($conector, "SELECT *
                                                   FROM contas_receber
                                                  WHERE ctr_data_emissao >='$data_inicio' and
                                                        ctr_data_emissao <='$data_fim' and
                                                        ctr_codigo_conta='$conta_inicio' and
                                                                ctr_lixeira=0   
                                               ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
            }
            else if ($tipo_data=="V"){
                $contas_rec = mysqli_query($conector, "SELECT *
                                                   FROM contas_receber
                                                  WHERE ctr_data_vencimento >='$data_inicio' and
                                                        ctr_data_vencimento <='$data_fim' and
                                                        ctr_codigo_conta='$conta_inicio'  and
                                                                ctr_lixeira=0
                                               ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
            }
            else if ($tipo_data=="P"){
                $contas_rec = mysqli_query($conector, "SELECT * 
                    FROM baixa_contas_receber
                    INNER JOIN contas_receber
                           ON bcr_id=ctr_id
                    WHERE bcr_data_pagamento >='$data_inicio' and
                        bcr_data_pagamento <='$data_fim' and
                        ctr_codigo_conta='$conta_inicio'  and
                        ctr_lixeira=0
                    ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
            }
        }
        else if ($data_inicio!=0 && $data_fim!=0 && $codigo_cc!=0) {
            if ($tipo_data=="E"){
                $contas_rec = mysqli_query($conector, "SELECT *
                                                   FROM contas_receber
                                                  WHERE ctr_data_emissao >='$data_inicio' and
                                                        ctr_data_emissao <='$data_fim' and
                                                        ctr_codigo_conta='$conta_inicio' and
                                                        ctr_codigo_c_custo='$codigo_cc' and
                                                                ctr_lixeira=0
                                               ORDER BY ctr_codigo_conta, ctr_data_emissao, ctr_numero_doc ASC"); 
            }
            else if ($tipo_data=="V"){
                    $contas_rec = mysqli_query($conector, "SELECT *
                                                   FROM contas_receber
                                                  WHERE ctr_data_vencimento >='$data_inicio' and
                                                        ctr_data_vencimento <='$data_fim' and
                                                        ctr_codigo_conta='$conta_inicio' and
                                                        ctr_codigo_c_custo='$codigo_cc'  and
                                                                ctr_lixeira=0
                                               ORDER BY ctr_codigo_conta, ctr_data_vencimento, ctr_numero_doc ASC"); 
            }
            else if ($tipo_data=="P"){
                $contas_rec = mysqli_query($conector, "SELECT * 
                                               FROM baixa_contas_receber
                                         INNER JOIN contas_receber
                                                 ON bcr_id=ctr_id
                                              WHERE bcr_data_pagamento >='$data_inicio' and
                                                    bcr_data_pagamento <='$data_fim' and
                                                    ctr_codigo_conta='$conta_inicio' and
                                                        ctr_codigo_c_custo='$codigo_cc'  and
                                                                ctr_lixeira=0
                                           ORDER BY ctr_codigo_conta, bcr_data_pagamento, bcr_numero_doc  ASC"); 
            }
        }

        $num_rows_contas_rec = mysqli_num_rows($contas_rec);
        $ind_array = 0;

        $data_pag_edi='';

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

            if ($situacao == "P" || $situacao == "C"){
                $conta_baixada = mysqli_query($conector, "SELECT bcr_valor_pagamento,bcr_data_pagamento
                    FROM baixa_contas_receber 
                    WHERE bcr_id='$ctr_id'");
                                                                                         
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

            $dados = [$doc_imp,$razao,$emissao_edi->format('d/m/Y'), $vencimento_edi->format('d/m/Y'),number_format($total_pagar,2,',','.'), $data_pag_edi, number_format($valor_pago,2,',','.'), $desc_situacao, $desc_conta_pgto, $numero_cheque];

            $array_contas[$ind_array] = $dados;

            $ind_array++;
        }
        return $array_contas;
    }
    ?>

    </section>

    <script src="js/contas_receber.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
