<?php
    include "conecta_mysql.inc";

    $mes = $_REQUEST["mes"];
    $ano = $_REQUEST["ano"];
    $opc_rel = $_REQUEST["opc_rel"];
    $forma_pag = $_REQUEST["forma_pag"];

    $data_inicial = $ano . '-' . $mes . '-01';

    @ session_start(); 

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
            if ($opc_rel==1) {
                echo '<table id="tabela_caixa_diario" class="table table-striped table-bordered table-advance table-hover"
                width="300%">';
            }
            else {
                echo '<table id="tabela_caixa_diario" class="table table-bordered table-advance table-hover"
                width="100%">';
            }
        ?>
        <tbody>
            <?php
                if ($opc_rel==1){
                    // Realizado e Nao Realizado    
                } // fim opcrel = 1
                else if ($opc_rel==2) {
                    // Realizado
                    //apurar saldo anterior realizado
                    $total_saldo_anterior=0;
                    $total_recebido=0;
                    $total_pago=0;
                    $total_geral_recebido=0;
                    $total_geral_pago=0;
                    $total_geral_mes=0;
                    $total_geral_final=0;

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
                        
                    $total_saldo_anterior+= $total_recebido - $total_pago;
                    $total_saldo = $total_saldo_anterior;

                    echo '<tr>';
                    echo '<td width="25%" align="right" style="font-weight:bold;">Saldo Anterior</td>';
                    echo '<td width="25%"></td>';
                    echo '<td width="25%"></td>';

                    if ($total_saldo_anterior<0) {
                        echo '<td width="25%" align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</td>';
                    }
                    else if ($total_saldo_anterior>0) {
                        echo '<td width="25%" align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</td>';
                    }
                    else {
                        echo '<td width="25%" align="right" style="font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</td>';
                    }

                    echo '</tr>';
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

                        if ($forma_pag==0) {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                                 WHERE bcr_data_pagamento='$data_lista'"); 
                        }
                        else {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                           INNER JOIN contas_receber
                                                   ON bcr_id=ctr_id
                                                WHERE bcr_data_pagamento='$data_lista' AND 
                                                      ctr_codigo_forma_recebimento='$forma_pag'"); 
                        }

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $valor_pago = $registro_contas_rec->bcr_valor_pagamento;
                                $total_recebido+=$valor_pago;
                                $total_geral_recebido+=$valor_pago;
                            } 
                        }

                        if ($forma_pag==0) {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                             INNER JOIN contas_pagar
                                                     ON bcp_numero_id=ctp_numero_doc AND 
                                                        bcp_parcela=ctp_parcela AND 
                                                        bcp_codigo_fornecedor=ctp_codigo_fornecedor
                                
                                               WHERE bcp_data_pagamento='$data_lista'"); 
                        }
                        else {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
                                             INNER JOIN contas_pagar
                                                     ON bcp_numero_id=ctp_numero_doc AND 
                                                        bcp_parcela=ctp_parcela AND 
                                                        bcp_codigo_fornecedor=ctp_codigo_fornecedor
                                                  WHERE bcp_data_pagamento='$data_lista' AND 
                                                        ctp_conta_pagamento='$forma_pag'"); 
                        }

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

                    echo '<tr>';
                    echo '<td align="right" style="font-weight:bold;">Saldo do Mês</td>';
                    echo '<td align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_recebido,2,',','.').'</td>';
                    echo '<td align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_pago,2,',','.').'</td>';

                    if ($total_geral_mes<0) {
                        echo '<td align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</td>';
                    }
                    else if ($total_geral_mes>0) {
                        echo '<td align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</td>';
                    }
                    else {
                        echo '<td align="right" style="font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</td>';
                    }

                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight:bold;">Saldo Final</td>';
                    echo '<td></td>';
                    echo '<td></td>';

                    if ($total_geral_final<0) {
                        echo '<td align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</td>';
                    }
                    else if ($total_geral_final>0) {
                        echo '<td align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</td>';
                    }
                    else {
                        echo '<td align="right" style="font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</td>';
                    }

                    echo '</tr>';

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
                else {
                    // Nao Realizado
                    //apurar saldo anterior nao realizado
                    $total_saldo_anterior=0;
                    $total_recebido=0;
                    $total_pago=0;
                    $total_geral_recebido=0;
                    $total_geral_pago=0;
                    $total_geral_mes=0;
                    $total_geral_final=0;

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
                        
                    $total_saldo_anterior+= $total_recebido - $total_pago;
                    $total_saldo = $total_saldo_anterior;
                    $total_recebido =0;
                    $total_pago=0;                    

                    echo '<tr>';
                    echo '<td width="25%" align="right" style="font-weight:bold;">Saldo Anterior</td>';
                    echo '<td width="25%"></td>';
                    echo '<td width="25%"></td>';

                    if ($total_saldo_anterior<0) {
                        echo '<td width="25%" align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</td>';
                    }
                    else if ($total_saldo_anterior>0) {
                        echo '<td width="25%" align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</td>';
                    }
                    else {
                        echo '<td width="25%" align="right" style="font-weight:bold;">'.number_format($total_saldo_anterior,2,',','.').'</td>';
                    }

                    echo '</tr>';
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

                        if ($forma_pag==0) {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                                 WHERE ctr_data_vencimento='$data_lista' AND 
                                                       ctr_situacao=''"); 
                        }
                        else {
                            $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                                                WHERE ctr_data_vencimento='$data_lista' AND 
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
                                $total_geral_recebido+=$vlr_pagamento;
                            } 
                        }

                        if ($forma_pag==0) {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                               WHERE ctp_data_vencimento='$data_lista' AND 
                                                     ctp_situacao=''"); 
                        }
                        else {
                            $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                                                  WHERE ctp_data_vencimento='$data_lista' AND 
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

                    echo '<tr>';
                    echo '<td align="right" style="font-weight:bold;">Saldo do Mês</td>';
                    echo '<td align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_recebido,2,',','.').'</td>';
                    echo '<td align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_pago,2,',','.').'</td>';

                    if ($total_geral_mes<0) {
                        echo '<td align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</td>';
                    }
                    else if ($total_geral_mes>0) {
                        echo '<td align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</td>';
                    }
                    else {
                        echo '<td align="right" style="font-weight:bold;">'.number_format($total_geral_mes,2,',','.').'</td>';
                    }

                    echo '</tr>';

                    echo '<tr>';
                    echo '<td align="right" style="font-weight:bold;">Saldo Final</td>';
                    echo '<td></td>';
                    echo '<td></td>';

                    if ($total_geral_final<0) {
                        echo '<td align="right" style="color: #FF2F2F; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</td>';
                    }
                    else if ($total_geral_final>0) {
                        echo '<td align="right" style="color: #00B900; font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</td>';
                    }
                    else {
                        echo '<td align="right" style="font-weight:bold;">'.number_format($total_geral_final,2,',','.').'</td>';
                    }

                    echo '</tr>';

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

                } // fim opcrel = 3
            ?>
        </tbody>
        <thead>
            <tr>
                <th class="text-center">Data</th>
                <?php
                    if ($opc_rel==1) {
                            echo '<th class="text-center">Recebimentos Realizados</th>';
                            echo '<th class="text-center">Recebimentos Não Realizados</th>';
                            echo '<th class="text-center">Pagamentos Realizados</th>';
                            echo '<th class="text-center">Pagamentos Não Realizados</th>';
                            echo '<th class="text-center">Saldo Realizado</th>';
                            echo '<th class="text-center">Saldo Não Realizado</th>';
                    }
                    else if ($opc_rel==2) {
                        echo '<th class="text-center">Recebimentos Realizados</th>';
                        echo '<th class="text-center">Pagamentos Realizados</th>';
                        echo '<th class="text-center">Saldo</th>';
                    } 
                    else {
                        echo '<th class="text-center">Recebimentos não Realizados</th>';
                        echo '<th class="text-center">Pagamentos não Realizados</th>';
                        echo '<th class="text-center">Saldo</th>';
                    }
                ?>
            </tr>
        </thead>
        </table>

    </section>

    <script src="js/contas_pagar.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
