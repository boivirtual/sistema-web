<?php
    include "conecta_mysql.inc";

    $mes_atual = date('m');

    $ano = $_POST["ano"];
    $opc_rel = $_POST["opc_rel"];

    $wforma_pag = '';
    if (isset($_POST['forma_pag'])) {
        $forma_pag = $_POST['forma_pag'];

        if(in_array("", $forma_pag)) {
            $wforma_pag='';
        }
        else {
            $wforma_pag = " AND ctp_conta_pagamento IN(";
            $wforma_pag.= implode(',', $forma_pag);
            $wforma_pag.= ")";
            }
    }
    else {
        $wforma_pag='';
    }

    $wforma_rec = '';
    if (isset($_POST['forma_pag'])) {
        $forma_rec = $_POST['forma_pag'];

        if(in_array("", $forma_rec)) {
            $wforma_rec='';
        }
        else {
            $wforma_rec = " AND ctr_codigo_forma_recebimento IN(";
            $wforma_rec.= implode(',', $forma_rec);
            $wforma_rec.= ")";
            }
    }
    else {
        $wforma_rec='';
    }

    $wlocal_rec = '';
    if (isset($_POST['local'])) {
        $local_rec = $_POST['local'];

        if(in_array("", $local_rec)) {
            $wlocal_rec='';
        }
        else {
            $wlocal_rec = " AND ctr_codigo_c_custo IN(";
            $wlocal_rec.= implode(',', $local_rec);
            $wlocal_rec.= ")";
            }
    }
    else {
        $wlocal_rec='';
    }

    $wlocal_pag = '';
    if (isset($_POST['local'])) {
        $local_pag = $_POST['local'];

        if(in_array("", $local_pag)) {
            $wlocal_pag='';
        }
        else {
            $wlocal_pag = " AND ctp_codigo_centro_custos IN(";
            $wlocal_pag.= implode(',', $local_pag);
            $wlocal_pag.= ")";
            }
    }
    else {
        $wlocal_pag='';
    }

    $wlocal_previsao = '';
    if (isset($_POST['local'])) {
        $local_previsao = $_POST['local'];

        if(in_array("", $local_previsao)) {
            $wlocal_previsao='';
        }
        else {
            $wlocal_previsao = " AND tbl_previsao_conta_codigo_cc IN(";
            $wlocal_previsao.= implode(',', $local_previsao);
            $wlocal_previsao.= ")";
            }
    }
    else {
        $wlocal_previsao='';
    }

    $data_inicial = $ano . '-01-01';
    $data_final = $ano . '-12-31';

    //APURAR SALDO ANTERIOR REALIZADO
    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                    INNER JOIN contas_receber
                                            ON bcr_id=ctr_id
                        WHERE bcr_data_pagamento<'$data_inicial'" . $wforma_rec . $wlocal_rec); 
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
                                    WHERE bcp_data_pagamento<'$data_inicial'" . $wforma_pag  . $wlocal_pag); 

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

    $previsao_conta = mysqli_query($conector, "SELECT * FROM tbl_previsao_conta
        INNER JOIN tbl_plano_contas 
                ON tbl_previsao_conta_codigo=tbl_plano_contas_codigo_id
             WHERE tbl_previsao_conta_ano < '$ano'"  . $wlocal_previsao);
    
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
<!--        <div class="row">
            <div class="form-group col-md-3">
                <p id="aguarde" style="font-size: 14px" hidden='true'>Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
            </div>
        </div>
-->
        <?php
            if ($opc_rel==1) {
                echo '<table id="tabela_contas" class="table table-bordered table-advance table-hover" style="width:100%; font-size:7px;">';
            }
            else {
                echo '<table id="tabela_contas" class="table table-bordered table-advance table-hover" style="width:100%; font-size:8px;">';
            }
        ?>

        <thead>
            <?php
	            echo '<div class="row col-md-12 filtro_escondido" id="total_contas">';

                echo '<div class="form-group col-md-9">';
                echo '<p id="descricao_filtro"
                    class="text-muted" style="font-size: 12px; color: #829c9c"></p>';
                echo '</div>';

	            echo '<div class="form-group col-md-1">';
	            echo '<button type="button" class="form-control btn btn-success pull-right"
	                onClick="imprimir_analize_contas()">Excel</button>';
	            echo '</div>';

	            echo '<div class="form-group col-md-1">';
	            echo '<button type="button" class="form-control btn btn-info pull-right exibir"
	                data-toggle="tooltip" data-placement="top" title="Maximizar tela filtros" onClick="exibir_filtro()"><i class="fa fa-sort-up"></i>&nbsp;<i class="fa fa-filter"></i></button>';
	            echo '</div>';

                echo '<div class="form-group col-md-1 voltar">';
                echo '<button type="button" class="form-control btn btn-info pull-right"
                    onClick="voltar_relatorios()">Voltar</button>';
                echo '</div>';

	            echo '</div>';

                if ($opc_rel==1) {
                    echo '<tr>';
                    echo '<th width="22%" rowspan="2"></th>';

                    for ($i=1; $i <= 12 ; $i++) { 
                        echo '<th width="6%" colspan="2" class="text-center">'.$array_mes[$i].'</th>';
                    }
                    echo '<th width="6%" colspan="2" class="text-center">Total</th>';
                    echo'</tr>';

                    echo '<tr>';
                    for ($i=1; $i <=13 ; $i++) {
                        echo '<th width="3%" class="text-center">Realizado</th>';
                        echo '<th width="3% "class="text-center">Previsto</th>';
                    }
                    echo '</tr>';

                }
                else if ($opc_rel==2) {
                    echo '<tr>';
                    echo '<th width="22%" rowspan="2"></th>';

                    for ($i=1; $i <= 12 ; $i++) { 
                        echo '<th width="3%" class="text-center">'.$array_mes[$i].'</th>';                        
                    }

                    echo '<th width="3%" class="text-center">Total</th>';
                    echo'</tr>';
                    echo '<tr>';

                    for ($i=1; $i <=13 ; $i++) {
                        echo '<th width="3%" class="text-center">Realizado</th>';
                    }
                    echo '</tr>';
                } 
                else {
                    echo '<tr>';
                    echo '<th width="22%" rowspan="2"></th>';

                    for ($i=1; $i <= 12 ; $i++) { 
                        echo '<th width="3%" class="text-center">'.$array_mes[$i].'</th>';                        
                    }

                    echo '<th width="3%" class="text-center">Total</th>';
                    echo'</tr>';
                    echo '<tr>';

                    for ($i=1; $i <=13 ; $i++) {
                        echo '<th width="3%" class="text-center">Previsto</th>';
                    }
                    echo '</tr>';
                }
            ?>
        </thead>
        <tbody style="margin:0; padding: 0">
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

                    /*    $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber

                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  ctr_data_vencimento >='$data_inicial' AND
                                  ctr_data_vencimento <='$data_final'" . $wforma_rec  . $wlocal_rec . 
                                  "ORDER BY ctr_data_vencimento"); 
                    */
                        $valor_pago = 0;
                        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                            inner join contas_receber
                                    on ctr_id=bcr_id
                                                                  
                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  bcr_data_pagamento >='$data_inicial' AND
                                  bcr_data_pagamento <='$data_final'" . $wforma_rec  . $wlocal_rec . 
                                  "ORDER BY bcr_data_pagamento"); 
                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                               // $numero_id = $registro_contas_rec->ctr_numero_doc;
                               // $parcela = $registro_contas_rec->ctr_parcela;
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

                            /*   if ($registro_contas_rec->ctr_situacao != '') {
                                    $conta_baixada = mysqli_query($conector, "SELECT *  FROM baixa_contas_receber 
                                        WHERE bcr_numero_doc='$numero_id' AND 
                                              bcr_parcela='$parcela'");
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
                                } */
                            } // fim while contas a receber
                        } // fim if rows contas receber

                        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                            inner join contas_pagar
                                    on ctp_numero_doc=bcp_numero_id and 
                                       ctp_parcela=bcp_parcela and 
                                       ctp_codigo_fornecedor=bcp_codigo_fornecedor
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  bcp_data_pagamento >='$data_inicial' AND
                                  bcp_data_pagamento <='$data_final'" . $wforma_pag  . $wlocal_pag . 
                                  " ORDER BY bcp_data_pagamento"); 

                    /*    $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  ctp_data_vencimento >='$data_inicial' AND
                                  ctp_data_vencimento <='$data_final'" . $wforma_pag  . $wlocal_pag . 
                                  " ORDER BY ctp_data_vencimento"); 
                    */              
                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                                //$numero_id = $registro_contas_pag->ctp_numero_doc;
                                //$parcela = $registro_contas_pag->ctp_parcela;
                                //$codigo_for = $registro_contas_pag->ctp_codigo_fornecedor;
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
                            /*    
                                if ($registro_contas_pag->ctp_situacao != '') {
                                    $valor_pago = 0;
                                    $conta_baixada = mysqli_query($conector, "SELECT *  FROM baixa_contas_pagar 
                                        WHERE bcp_numero_id='$numero_id' AND 
                                              bcp_parcela='$parcela' AND 
                                              bcp_codigo_fornecedor='$codigo_for'");
                                    $num_rows_contas_pag = mysqli_num_rows($conta_baixada);

                                    if ($num_rows_contas_pag!=0){
                                        while ($registro_conta_baixada = mysqli_fetch_object($conta_baixada)) {
                                            $ctp_valor_pago = $registro_conta_baixada->bcp_valor_pagamento;
                                            $valor_pago = $valor_pago + $ctp_valor_pago;
                                        }
                                    }

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
                                } */
                            } // fim while contas a pagar
                        } // fim if rows contas pagar

                        $previsao_conta = mysqli_query($conector, "SELECT *  FROM tbl_previsao_conta
                                WHERE tbl_previsao_conta_codigo='$codigo_conta' AND 
                                      tbl_previsao_conta_ano = '$ano'"  . $wlocal_previsao);
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

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO ANTERIOR</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0){
                            echo '<td width="3%" width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes[$i]>0){
                            echo '<td width="3%" width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" width="3%" align="right"style="font-weight: bold;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }

                        if ($saldo_anterior_mes_nao[$i]<0){
                            echo '<td width="3%" width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes_nao[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes_nao[$i]>0){
                            echo '<td width="3%" width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes_nao[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_anterior_mes_nao[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO DO MÊS</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }

                        if ($saldo_mes_nao[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes_nao[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes_nao[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes_nao[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_mes_nao[$i],2,',','.').'</td>';
                        }
                    }

                    echo '<td width="3%"></td>';
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO FINAL</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }

                        if ($saldo_final_mes_nao[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes_nao[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes_nao[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes_nao[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_final_mes_nao[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            echo '<tr>';

                            if (substr($codigo_conta, 1,6)==0){
                                echo '<td width="22%" style="background-color: #C2E0E0; color: #1C1C1C">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                echo '<td width="22%" style="background-color: #DEE; color: #696969">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else {
                                echo '<td width="22%">'.$descricao_conta[$codigo_conta].'</td>';
                            }

                            for ($i=1; $i <= 13 ; $i++) { 
                                if (substr($codigo_conta, 1,6)==0){
                                    echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                    echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    echo '<td width="3%" align="right" style="background-color: #DEE; color: #696969">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                    echo '<td width="3%" align="right" style="background-color: #DEE; color: #696969">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else {
                                    echo '<td width="3%" align="right">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                    echo '<td width="3%" align="right">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
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

                    /*    $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  ctr_data_vencimento >='$data_inicial' AND
                                  ctr_data_vencimento <='$data_final' AND
                                  (ctr_situacao='P' OR ctr_situacao='C')" . $wforma_rec  . $wlocal_rec . 
                                  " ORDER BY ctr_data_vencimento"); 
*/
                        $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                            inner join contas_receber
                                    on ctr_id=bcr_id
                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  bcr_data_pagamento >='$data_inicial' AND
                                  bcr_data_pagamento <='$data_final'" . $wforma_rec  . $wlocal_rec . 
                                  "ORDER BY bcr_data_pagamento"); 

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                //$numero_id = $registro_contas_rec->ctr_numero_doc;
                                //$parcela = $registro_contas_rec->ctr_parcela;
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
                            /*        
                                $valor_pago = 0;
                                $conta_baixada = mysqli_query($conector, "SELECT *  FROM 
                                                                    baixa_contas_receber 
                                                      WHERE bcr_numero_doc='$numero_id' AND 
                                                            bcr_parcela='$parcela'");
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
                                } */
                            } // fim while contas a receber
                        } // fim if rows contas receber

                        $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                            inner join contas_pagar
                                    on ctp_numero_doc=bcp_numero_id and 
                                       ctp_parcela=bcp_parcela and 
                                       ctp_codigo_fornecedor=bcp_codigo_fornecedor
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  bcp_data_pagamento >='$data_inicial' AND
                                  bcp_data_pagamento <='$data_final'" . $wforma_pag  . $wlocal_pag . 
                                  " ORDER BY bcp_data_pagamento"); 

                    /*    $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  ctp_data_vencimento >='$data_inicial' AND
                                  ctp_data_vencimento <='$data_final' AND 
                                  (ctp_situacao='P' OR ctp_situacao='C')" . $wforma_pag  . $wlocal_pag . 
                                  " ORDER BY ctp_data_vencimento"); 
                    */
                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){
                                //$numero_id = $registro_contas_pag->ctp_numero_doc;
                                //$parcela = $registro_contas_pag->ctp_parcela;
                                //$codigo_for = $registro_contas_pag->ctp_codigo_fornecedor;
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
                            /*    
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
                                } */
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
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO ANTERIOR</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO DO MÊS</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO FINAL</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            echo '<tr>';

                            if (substr($codigo_conta, 1,6)==0){
                                echo '<td width="22%" style="background-color: #C2E0E0; color: #1C1C1C">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                echo '<td width="22%" style="background-color: #DEE; color: #696969">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else {
                                echo '<td width="22%">'.$descricao_conta[$codigo_conta].'</td>';
                            }

                            for ($i=1; $i <= 13 ; $i++) { 
                                if (substr($codigo_conta, 1,6)==0){
                                    echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    echo '<td width="3%" align="right" style="background-color: #DEE; color: #696969">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else {
                                    echo '<td width="3%" align="right">'.number_format($total_realizado[$codigo_conta ][$i],2,',','.').'</td>';
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

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO ANTERIOR</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_anterior_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_anterior_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO DO MÊS</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td width="22%" align="right" style="font-weight: bold">SALDO FINAL</td>';
                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #8B0000;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else if ($saldo_final_mes[$i]>0){
                            echo '<td width="3%" align="right" style="font-weight: bold; color: #006400;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                        else {
                            echo '<td width="3%" align="right" style="font-weight: bold;">'.number_format($saldo_final_mes[$i],2,',','.').'</td>';
                        }
                    }
                    echo '<td width="3%"></td>';
                    echo '</tr>';

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            echo '<tr>';

                            if (substr($codigo_conta, 1,6)==0){
                                echo '<td width="22%" style="background-color: #C2E0E0; color: #1C1C1C">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                echo '<td width="22%" style="background-color: #DEE; color: #696969">'.$descricao_conta[$codigo_conta].'</td>';
                            }
                            else {
                                echo '<td width="22%">'.$descricao_conta[$codigo_conta].'</td>';
                            }

                            for ($i=1; $i <= 13 ; $i++) { 
                                if (substr($codigo_conta, 1,6)==0){
                                    echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    echo '<td width="3%" align="right" style="background-color: #DEE; color: #696969">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
                                }
                                else {
                                    echo '<td width="3%" align="right">'.number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.').'</td>';
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
            var table = $('#tabela_contas').DataTable( {
                fixedColumns:   {
                    leftColumns: 1,
                    rightColumns: 0,
                },
                scrollY:        "250px",
                scrollX:  true,
                paging:   false,
                search:   true,
                ordering: false,
                info: false,
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


                
                
