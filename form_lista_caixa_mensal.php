<?php
    include "conecta_mysql.inc";

    $mes_atual = date('m');

    $ano = $_REQUEST["ano"];
    $opc_rel = $_REQUEST["opc_rel"];
    $forma_pag = $_REQUEST["forma_pag"];

    $data_inicial = $ano . '-01-01';
    $data_final = $ano . '-12-31';

    //apurar saldo anterior realizado
    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    if ($forma_pag==0) {
        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                      WHERE bcr_data_pagamento<'$data_inicial'"); 
    }
    else {
        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                        INNER JOIN contas_receber
                                               ON bcr_id=ctr_id
                                            WHERE bcr_data_pagamento<'$data_inicial' AND 
                                                  ctr_codigo_forma_recebimento='$forma_pag'"); 
    }
    $num_rows_contas_rec = mysqli_num_rows($contas_rec);

    if ($num_rows_contas_rec!=0){
        while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){
               $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
               $total_recebido+=$valor_pago;
        } 
    }

    if ($forma_pag==0) {
        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                              WHERE bcp_data_pagamento<'$data_inicial'"); 
    }
    else {
        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                            INNER JOIN contas_pagar
                                                 ON bcp_numero_id=ctp_numero_doc AND 
                                                    bcp_parcela=ctp_parcela AND 
                                                    bcp_codigo_fornecedor=ctp_codigo_fornecedor
                                              WHERE bcp_data_pagamento<'$data_inicial' AND 
                                                    ctp_conta_pagamento='$forma_pag'"); 
    }

    $num_rows_contas_pag = mysqli_num_rows($contas_pag);

    if ($num_rows_contas_pag!=0){
        while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
               $valor_pago = $registro_contas_pag->bcp_valor_pagamento;
               $total_pago+=$valor_pago;
    } 
        }
                      
    $saldo_anterior_realizado+= $total_recebido - $total_pago;

    //apurar saldo anterior nao realizado
    $saldo_anterior_nao_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    if ($forma_pag==0) {
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                             WHERE ctr_data_vencimento<'$data_inicial' AND 
                                                   ctr_situacao=''"); 
    }
    else {
        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                               WHERE ctr_data_vencimento<'$data_inicial' AND 
                                                  ctr_situacao='' AND 
                                                  ctr_codigo_forma_recebimento='$forma_pag'"); 
    }
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

    if ($forma_pag==0) {
        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                           WHERE ctp_data_vencimento<'$data_inicial' AND 
                                                 ctp_situacao=''"); 
    }
    else {
        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                              WHERE ctp_data_vencimento<'$data_inicial' AND 
                                                    ctp_situacao='' AND 
                                                    ctp_conta_pagamento='$forma_pag'"); 
    }

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
                       
    $saldo_anterior_nao_realizado+= $total_recebido - $total_pago;

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

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->

</head>

<body>
	<section class="panel">

        <?php
            if ($opc_rel==1) {
                echo '<table id="tabela_caixa_mensal" class="table table-bordered table-advance table-hover" style="width:100%">';
            }
            else {
                echo '<table id="tabela_caixa_mensal" class="table table-bordered table-advance table-hover" style="width:100%">';
            }
        ?>
        <thead>
            <?php
                if ($opc_rel==1) {
                    echo '<tr>';
                    echo '<th rowspan="2"></th>';

                    for ($i=1; $i <= 12 ; $i++) { 
                        echo '<th colspan="2" class="text-center">'.$array_mes[$i].'</th>';
                    }
                    echo '<th colspan="2" class="text-center">Total</th>';
                    echo'</tr>';

                    echo '<tr>';
                    for ($i=1; $i <=13 ; $i++) {
                        echo '<th class="text-center">Realizado</th>';
                        echo '<th class="text-center">Previsto</th>';
                    }
                    echo '</tr>';

                }
                else if ($opc_rel==2) {
                    echo '<tr>';
                    echo '<th rowspan="2"></th>';

                    for ($i=1; $i <= 12 ; $i++) { 
                        echo '<th class="text-center">'.$array_mes[$i].'</th>';                        
                    }

                    echo '<th class="text-center">Total</th>';
                    echo'</tr>';
                    echo '<tr>';

                    for ($i=1; $i <=13 ; $i++) {
                        echo '<th class="text-center">Realizado</th>';
                    }
                    echo '</tr>';
                } 
                else {
                    echo '<tr>';
                    echo '<th rowspan="2"></th>';

                    for ($i=1; $i <= 12 ; $i++) { 
                        echo '<th class="text-center">'.$array_mes[$i].'</th>';                        
                    }

                    echo '<th class="text-center">Total</th>';
                    echo'</tr>';
                    echo '<tr>';

                    for ($i=1; $i <=13 ; $i++) {
                        echo '<th class="text-center">Previsto</th>';
                    }
                    echo '</tr>';
                }
            ?>
        </thead>
        <tbody>
            <?php
                if ($opc_rel==1){
                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                   WHERE tbl_plano_contas_lixeira=0 
                                                ORDER BY tbl_plano_contas_codigo_id ASC"); 
                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 
                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $descricao_conta[$codigo_conta] = $registro_tbl_conta->tbl_plano_contas_descricao;

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

                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 

                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
                        $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

                        $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

                        if ($forma_pag==0){
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                                     WHERE ctr_codigo_conta='$codigo_conta' AND
                                                           ctr_data_vencimento >='$data_inicial' AND
                                                           ctr_data_vencimento <='$data_final' 
                                                  ORDER BY ctr_data_vencimento"); 
                        }
                        else {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                                  ctr_data_vencimento >='$data_inicial' AND
                                                  ctr_data_vencimento <='$data_final' AND
                                                  ctr_codigo_forma_recebimento='$forma_pag' 
                                         ORDER BY ctr_data_vencimento"); 
                        }

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $ctr_id = $registro_contas_rec->ctr_id;
                                $numero_id = $registro_contas_rec->ctr_numero_doc;
                                $parcela = $registro_contas_rec->ctr_parcela;
                                $data_vencimento = $registro_contas_rec->ctr_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);

                                if ($registro_contas_rec->ctr_situacao == '') {
                                    $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
                                    $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
                                    $valor_juros = $registro_contas_rec->ctr_valor_juros;
                                    $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
                                    $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

                                    $total_nao_realizado[$conta_nivel_1][$mes]+=$vlr_pagamento;
                                    $total_nao_realizado[$conta_nivel_1][13]+=$vlr_pagamento;
                                    $total_nao_realizado[$conta_nivel_2][$mes]+=$vlr_pagamento;
                                    $total_nao_realizado[$conta_nivel_2][13]+=$vlr_pagamento;
                                    $total_nao_realizado[$codigo_conta][$mes]+=$vlr_pagamento;
                                    $total_nao_realizado[$codigo_conta][13]+=$vlr_pagamento;
                                    $valor_credito_nao[$mes]+=$vlr_pagamento;
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }
                                else {
                                    $conta_baixada = mysqli_query($conector, "SELECT *  FROM baixa_contas_receber 
                                        WHERE bcr_id='$ctr_id'");
                                    $num_rows_contas_pag = mysqli_num_rows($conta_baixada);

                                    $valor_pago = 0;

                                    if ($num_rows_contas_pag!=0){
                                        while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                                            $ctr_valor_pago = $registro_conta_baixada->bcr_valor_pagamento;
                                            $valor_pago = $valor_pago + $ctr_valor_pago;
                                        }

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
                                    }
                                }

                            } // fim while contas a receber
                        } // fim if rows contas receber

                        if ($forma_pag==0){
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                                  ctp_data_vencimento >='$data_inicial' AND
                                                  ctp_data_vencimento <='$data_final' 
                                         ORDER BY ctp_data_vencimento"); 
                        }
                        else {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                                  ctp_data_vencimento >='$data_inicial' AND
                                                  ctp_data_vencimento <='$data_final' AND 
                                                  ctp_conta_pagamento = '$forma_pag' 
                                         ORDER BY ctp_data_vencimento"); 
                        }

                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                                $numero_id = $registro_contas_pag->ctp_numero_doc;
                                $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
                                $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
                                $valor_juros = $registro_contas_pag->ctp_valor_juros;
                                $valor_outro = $registro_contas_pag->ctp_outro_valor;
                                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

                                $parcela = $registro_contas_pag->ctp_parcela;
                                $codigo_for = $registro_contas_pag->ctp_codigo_fornecedor;
                                $data_vencimento = $registro_contas_pag->ctp_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);

                                if ($registro_contas_pag->ctp_situacao == '') {
                                    $total_nao_realizado[$conta_nivel_1][$mes]+=$vlr_pagamento;
                                    $total_nao_realizado[$conta_nivel_1][13]+=$vlr_pagamento;
                                    $total_nao_realizado[$conta_nivel_2][$mes]+=$vlr_pagamento;
                                    $total_nao_realizado[$conta_nivel_2][13]+=$vlr_pagamento;
                                    $total_nao_realizado[$codigo_conta][$mes]+=$vlr_pagamento;
                                    $total_nao_realizado[$codigo_conta][13]+=$vlr_pagamento;
                                    $valor_debito_nao[$mes]+=$vlr_pagamento;
                                    $tem_valor[$conta_nivel_1]="S";
                                    $tem_valor[$conta_nivel_2]="S";
                                    $tem_valor[$codigo_conta]="S";
                                }
                                else {
                                    $valor_pago = 0;
                                    $conta_baixada = mysqli_query($conector, "SELECT *  FROM 
                                                                        baixa_contas_pagar 
                                                          WHERE bcp_numero_id='$numero_id' AND 
                                                                bcp_parcela='$parcela' AND 
                                                                bcp_codigo_fornecedor='$codigo_for'");
                                    $num_rows_contas_pag = mysqli_num_rows($conta_baixada);

                                    if ($num_rows_contas_pag!=0){
                                        while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                                            $ctp_valor_pago = $registro_conta_baixada->bcp_valor_pagamento;
                                            $valor_pago = $valor_pago + $ctp_valor_pago;
                                        }
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
                                    }
                                }
                            } // fim while contas a pgar
                        } // fim if rows contas pagar
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

                    echo '<tr>';
                    echo '<td width="15%" align="right" style="font-weight: bold">SALDO ANTERIOR</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td  align="right"style="font-weight: bold;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }

                        if ($saldo_anterior_mes_nao[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes_nao[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes_nao[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes_nao[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_anterior_mes_nao[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO DO MÊS</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }

                        if ($saldo_mes_nao[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes_nao[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes_nao[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes_nao[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_mes_nao[$i],2,',','.').'</td>';
                        }
                    }

                    echo '<td></td>';
                    echo '<td></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO FINAL</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }

                        if ($saldo_final_mes_nao[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes_nao[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes_nao[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes_nao[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_final_mes_nao[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '</tr>';

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            echo '<tr>';

                            if (substr($codigo_conta, 1,6)==0){
                                echo '<td style="background-color: #C2E0E0; color: #1C1C1C">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                echo '<td style="background-color: #DEE; color: #696969">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else {
                                echo '<td>'.$descricao_conta[$codigo_conta].'</td>';
                            }

                            for ($i=1; $i <= 13 ; $i++) { 
                                if (substr($codigo_conta, 1,6)==0){
                                    echo '<td align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                    echo '<td align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    echo '<td align="right" style="background-color: #DEE; color: #696969">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                    echo '<td align="right" style="background-color: #DEE; color: #696969">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else {
                                    echo '<td align="right">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                    echo '<td align="right">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                            }
                            echo '</tr>';

                        }
                    }

                } // fim do if $opc_rel==1 Fim Realizado / Nao Realizado

                // Inicio do else para Realizado
                else if ($opc_rel==2) { 
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

                    $plano_contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas
                                                    WHERE tbl_plano_contas_nivel=3 AND 
                                                          tbl_plano_contas_lixeira=0 
                                                 ORDER BY tbl_plano_contas_codigo_id ASC"); 

                    while ($registro_tbl_conta = mysqli_fetch_object($plano_contas)){ 

                        $codigo_conta = $registro_tbl_conta->tbl_plano_contas_codigo_id;
                        $conta_nivel_1 = (int)str_pad(substr($codigo_conta, 0,1), 7, "0", STR_PAD_RIGHT);
                        $conta_nivel_2 = (int)str_pad(substr($codigo_conta, 0,3), 7, "0", STR_PAD_RIGHT);

                        $debito_credito = $registro_tbl_conta->tbl_plano_contas_debito_credito;

                        if ($forma_pag==0){
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                                     WHERE ctr_codigo_conta='$codigo_conta' AND
                                                           ctr_data_vencimento >='$data_inicial' AND
                                                           ctr_data_vencimento <='$data_final' AND 
                                                           (ctr_situacao='P' OR ctr_situacao='C') 
                                                  ORDER BY ctr_data_vencimento"); 
                        }
                        else {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                                  ctr_data_vencimento >='$data_inicial' AND
                                                  ctr_data_vencimento <='$data_final' AND
                                                  ctr_codigo_forma_recebimento='$forma_pag' AND
                                                 (ctr_situacao='P' OR ctr_situacao='C') 
                                         ORDER BY ctr_data_vencimento"); 
                        }

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $ctr_id = $registro_contas_rec->ctr_id;
                                $numero_id = $registro_contas_rec->ctr_numero_doc;
                                $parcela = $registro_contas_rec->ctr_parcela;
                                $data_vencimento = $registro_contas_rec->ctr_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);
                                $valor_pago = 0;
                                $conta_baixada = mysqli_query($conector, "SELECT *  FROM baixa_contas_receber 
                                                      WHERE bcr_id='$ctr_id'");
                                $num_rows_contas_pag = mysqli_num_rows($conta_baixada);

                                if ($num_rows_contas_pag!=0){
                                    while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                                        $ctr_valor_pago = $registro_conta_baixada->bcr_valor_pagamento;
                                        $valor_pago = $valor_pago + $ctr_valor_pago;
                                    }
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
                                }
                            } // fim while contas a receber
                        } // fim if rows contas receber

                        if ($forma_pag==0){
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                                  ctp_data_vencimento >='$data_inicial' AND
                                                  ctp_data_vencimento <='$data_final' AND 
                                                  (ctp_situacao='P' OR ctp_situacao='C')
                                         ORDER BY ctp_data_vencimento"); 
                        }
                        else {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                                  ctp_data_vencimento >='$data_inicial' AND
                                                  ctp_data_vencimento <='$data_final' AND 
                                                  ctp_conta_pagamento = '$forma_pag' AND 
                                                  (ctp_situacao='P' OR ctp_situacao='C')
                                         ORDER BY ctp_data_vencimento"); 
                        }

                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                                $numero_id = $registro_contas_pag->ctp_numero_doc;
                                $parcela = $registro_contas_pag->ctp_parcela;
                                $codigo_for = $registro_contas_pag->ctp_codigo_fornecedor;
                                $data_vencimento = $registro_contas_pag->ctp_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);

                                $valor_pago = 0;
                                $conta_baixada = mysqli_query($conector, "SELECT *  FROM 
                                                                    baixa_contas_pagar 
                                                      WHERE bcp_numero_id='$numero_id' AND 
                                                            bcp_parcela='$parcela' AND 
                                                            bcp_codigo_fornecedor='$codigo_for'");
                                $num_rows_contas_pag = mysqli_num_rows($conta_baixada);

                                if ($num_rows_contas_pag!=0){
                                    while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                                        $ctp_valor_pago = $registro_conta_baixada->bcp_valor_pagamento;
                                        $valor_pago = $valor_pago + $ctp_valor_pago;
                                    }
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
                                }
                            } // fim while contas a pgar
                        } // fim if rows contas pagar
                    } // fim while plano de contas

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

                    echo '<tr>';
                    echo '<td  width="10%" align="right" style="font-weight: bold">SALDO ANTERIOR</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO DO MÊS</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO FINAL</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '</tr>';

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            echo '<tr>';

                            if (substr($codigo_conta, 1,6)==0){
                                echo '<td style="background-color: #C2E0E0; color: #1C1C1C">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                echo '<td style="background-color: #DEE; color: #696969">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else {
                                echo '<td>'.$descricao_conta[$codigo_conta].'</td>';
                            }

                            for ($i=1; $i <= 13 ; $i++) { 
                                if (substr($codigo_conta, 1,6)==0){
                                    echo '<td align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    echo '<td align="right" style="background-color: #DEE; color: #696969">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else {
                                    echo '<td align="right">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                            }
                            echo '</tr>';

                        }
                    }
                } // fim do if $opc_rel==2 Fim Realizado

                // Inicio do else Nao Realizado
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

                        if ($forma_pag==0){
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                                     WHERE ctr_codigo_conta='$codigo_conta' AND
                                                           ctr_data_vencimento >='$data_inicial' AND
                                                           ctr_data_vencimento <='$data_final' AND 
                                                           ctr_situacao='' 
                                                  ORDER BY ctr_data_vencimento"); 
                        }
                        else {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                                  ctr_data_vencimento >='$data_inicial' AND
                                                  ctr_data_vencimento <='$data_final' AND
                                                  ctr_codigo_forma_recebimento='$forma_pag' AND
                                                  ctr_situacao='' 
                                         ORDER BY ctr_data_vencimento"); 
                        }

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $numero_id = $registro_contas_rec->ctr_numero_doc;
                                $parcela = $registro_contas_rec->ctr_parcela;
                                $data_vencimento = $registro_contas_rec->ctr_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);
                                $valor_parcela = $registro_contas_rec->ctr_valor_parcela;
                                $valor_desconto = $registro_contas_rec->ctr_valor_desconto;
                                $valor_juros = $registro_contas_rec->ctr_valor_juros;
                                $valor_outro = $registro_contas_rec->ctr_valor_acrescimo;
                                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

                                $total_nao_realizado[$conta_nivel_1][$mes]+=$vlr_pagamento;
                                $total_nao_realizado[$conta_nivel_1][13]+=$vlr_pagamento;
                                $total_nao_realizado[$conta_nivel_2][$mes]+=$vlr_pagamento;
                                $total_nao_realizado[$conta_nivel_2][13]+=$vlr_pagamento;
                                $total_nao_realizado[$codigo_conta][$mes]+=$vlr_pagamento;
                                $total_nao_realizado[$codigo_conta][13]+=$vlr_pagamento;
                                $valor_credito[$mes]+=$vlr_pagamento;
                                $tem_valor[$conta_nivel_1]="S";
                                $tem_valor[$conta_nivel_2]="S";
                                $tem_valor[$codigo_conta]="S";
                            } // fim while contas a receber
                        } // fim if rows contas receber

                        if ($forma_pag==0){
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                                  ctp_data_vencimento >='$data_inicial' AND
                                                  ctp_data_vencimento <='$data_final' AND 
                                                  ctp_situacao=''
                                         ORDER BY ctp_data_vencimento"); 
                        }
                        else {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                                  ctp_data_vencimento >='$data_inicial' AND
                                                  ctp_data_vencimento <='$data_final' AND 
                                                  ctp_conta_pagamento = '$forma_pag' AND 
                                                  ctp_situacao=''
                                         ORDER BY ctp_data_vencimento"); 
                        }

                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                                $numero_id = $registro_contas_pag->ctp_numero_doc;
                                $parcela = $registro_contas_pag->ctp_parcela;
                                $codigo_for = $registro_contas_pag->ctp_codigo_fornecedor;
                                $data_vencimento = $registro_contas_pag->ctp_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);
                                $valor_parcela = $registro_contas_pag->ctp_valor_parcela;
                                $valor_desconto = $registro_contas_pag->ctp_valor_desconto;
                                $valor_juros = $registro_contas_pag->ctp_valor_juros;
                                $valor_outro = $registro_contas_pag->ctp_outro_valor;
                                $vlr_pagamento = $valor_parcela - $valor_desconto + $valor_juros + $valor_outro;

                                $total_nao_realizado[$conta_nivel_1][$mes]+=$vlr_pagamento;
                                $total_nao_realizado[$conta_nivel_1][13]+=$vlr_pagamento;
                                $total_nao_realizado[$conta_nivel_2][$mes]+=$vlr_pagamento;
                                $total_nao_realizado[$conta_nivel_2][13]+=$vlr_pagamento;
                                $total_nao_realizado[$codigo_conta][$mes]+=$vlr_pagamento;
                                $total_nao_realizado[$codigo_conta][13]+=$vlr_pagamento;
                                $valor_debito[$mes]+=$vlr_pagamento;
                                $tem_valor[$conta_nivel_1]="S";
                                $tem_valor[$conta_nivel_2]="S";
                                $tem_valor[$codigo_conta]="S";

                            } // fim while contas a pgar
                        } // fim if rows contas pagar
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

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO ANTERIOR</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO DO MÊS</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight: bold">SALDO FINAL</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0){
                            echo '<td align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes[$i]>0){
                            echo '<td align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td align="right" style="font-weight: bold;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td></td>';
                    echo '</tr>';

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            echo '<tr>';

                            if (substr($codigo_conta, 1,6)==0){
                                echo '<td style="background-color: #C2E0E0; color: #1C1C1C">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                echo '<td style="background-color: #DEE; color: #696969">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else {
                                echo '<td>'.$descricao_conta[$codigo_conta].'</td>';
                            }

                            for ($i=1; $i <= 13 ; $i++) { 
                                if (substr($codigo_conta, 1,6)==0){
                                    echo '<td align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    echo '<td align="right" style="background-color: #DEE; color: #696969">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else {
                                    echo '<td align="right">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                            }
                            echo '</tr>';

                        }
                    }
                } // fim do if $opc_rel==3
            ?>
        </tbody>
        </table>

    </section>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_caixa_mensal').DataTable( {
                fixedColumns:   {
                    leftColumns: 1,
                    rightColumns: 0,
                },
                scrollY:        "400px",
                scrollX:  true,
                paging:         false,
                search: true,
                ordering: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Registros encontrados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                }
            });
        });

    </script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
