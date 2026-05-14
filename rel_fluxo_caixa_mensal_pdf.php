<?php

include "conecta_mysql.inc";

$data_sistema = date("d/m/Y");

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
//if (substr($mes, 0,1)==0) {
//    $ind_mes = substr($mes, 1,1);
//}
//else {
//    $ind_mes = $mes;
//}

$coluna[1]='B';
$coluna[2]='D';
$coluna[3]='F';
$coluna[4]='H';
$coluna[5]='J';
$coluna[6]='L';
$coluna[7]='N';
$coluna[8]='P';
$coluna[9]='R';
$coluna[10]='T';
$coluna[11]='V';
$coluna[12]='X';
$coluna[13]='Z';

$coluna_nao[1]='C';
$coluna_nao[2]='E';
$coluna_nao[3]='G';
$coluna_nao[4]='I';
$coluna_nao[5]='K';
$coluna_nao[6]='M';
$coluna_nao[7]='O';
$coluna_nao[8]='Q';
$coluna_nao[9]='S';
$coluna_nao[10]='U';
$coluna_nao[11]='W';
$coluna_nao[12]='Y';
$coluna_nao[13]='AA';

$coluna_1[1]='B';
$coluna_1[2]='C';
$coluna_1[3]='D';
$coluna_1[4]='E';
$coluna_1[5]='F';
$coluna_1[6]='G';
$coluna_1[7]='H';
$coluna_1[8]='I';
$coluna_1[9]='J';
$coluna_1[10]='K';
$coluna_1[11]='L';
$coluna_1[12]='M';
$coluna_1[13]='N';

$liny=375;


@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$codigo_fornecedor_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

if ($forma_pag==0) {
    $desc_forma_pag = 'Todas';
}
else {
    $forma_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento 
                where tbl_conta_pagamento_id='$forma_pag' and tbl_conta_pagamento_lixeira=0"); 
    $registro_forma_pag = mysqli_fetch_object($forma_pagamento);
    $desc_forma_pag = $registro_forma_pag->tbl_conta_pagamento_descricao;
}

if ($opc_rel==1) {
    $desc_opc_rel = 'Realizados/Não Realizados';
}
else if ($opc_rel==2) {
    $desc_opc_rel = 'Realizados';
}
else {
    $desc_opc_rel = 'Não Realizados';
} 

$filtros = 'Filtros: Ano ' .  $ano . ' - Tipo Rel: ' .  $desc_opc_rel . ' - Forma Rec/Pag: ' . $desc_forma_pag;
$mes_ano = 'Ano: '. $ano . ' - Forma Recebimento/Pagamento: ' . $desc_forma_pag;

$numero_paginas = 1;
$pagina_atual = 0;

$_SESSION['nome_relatorio']= "Fluxo de Caixa Mensal" . ' - ' . $desc_opc_rel;
$_SESSION['filtros']=$filtros;

ob_start ();
define('FPDF_FONTPATH', 'fpdf/font/');
require_once('fpdf/pdf_padrao_paisagem.php');
$pdf=new PDF("L","mm","A4");
$pdf->Open();


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
                        } // fim do if contas a pagar

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

                    if ($liny>372) {
                        $array_retorno = salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano, $opc_rel);   

                        $pagina_atual=$array_retorno[0];
                        $liny=$array_retorno[1];
                    }

                    $pdf->SetFont('arial','B',6); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(50,4, 'Saldo Anterior',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx+=15;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(15,4, number_format($saldo_anterior_mes[$i],2,',','.'),1,0,'R');

                        $colx+=15;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(15,4, number_format($saldo_anterior_mes_nao[$i],2,',','.'),1,0,'R');
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

                    if ($liny>372) {
                        $array_retorno = salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano, $opc_rel);   

                        $pagina_atual=$array_retorno[0];
                        $liny=$array_retorno[1];
                    }

                    $pdf->SetFont('arial','B',7); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(53,4, 'Saldo Anterior',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0) {
                            $pdf->SetTextColor(210, 0, 0);
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                        else {
                            $pdf->SetTextColor(0, 0, 0);
                        }    

                        $colx+=18;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(18,4, number_format($saldo_anterior_mes[$i],2,',','.'),1,0,'R');
                    }

                    $colx+=18;
                    $pdf->SetXY($colx, $liny);
                    $pdf->Cell(18,4,'',1,0,'R');

                    $liny=$liny+4;
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('arial','B',7); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(53,4, 'Saldo do Mês',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0) {
                            $pdf->SetTextColor(210, 0, 0);
                        }
                        else if ($saldo_mes[$i]>0) {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                        else {
                            $pdf->SetTextColor(0, 0, 0);
                        }    
                        $colx+=18;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(18,4, number_format($saldo_mes[$i],2,',','.'),1,0,'R');
                    }
                    $colx+=18;
                    $pdf->SetXY($colx, $liny);
                    $pdf->Cell(18,4,'',1,0,'R');

                    $liny=$liny+4;
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('arial','B',7); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(53,4, 'Saldo Final',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0) {
                            $pdf->SetTextColor(210, 0, 0);
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                        else {
                            $pdf->SetTextColor(0, 0, 0);
                        }    
                        $colx+=18;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(18,4, number_format($saldo_final_mes[$i],2,',','.'),1,0,'R');
                    }
                    $colx+=18;
                    $pdf->SetXY($colx, $liny);
                    $pdf->Cell(18,4,'',1,0,'R');

                    $pdf->SetTextColor(0, 0, 0);
                   
                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $liny=$liny+4;

                            if (substr($codigo_conta, 1,6)==0){
                                $pdf->SetFillColor(167,167,167); 
                                $pdf->SetFont('arial','',6); 
                                $pdf->SetXY(5, $liny);
                                $pdf->Cell(53,4, utf8_decode($descricao_conta[$codigo_conta]),1,0,'L',1);
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                $pdf->SetFillColor(224,224,224); 
                                $pdf->SetFont('arial','',6); 
                                $pdf->SetXY(5, $liny);
                                $pdf->Cell(53,4, utf8_decode($descricao_conta[$codigo_conta]),1,0,'L',1);
                            }
                            else {
                                $pdf->SetFont('arial','',6); 
                                $pdf->SetXY(5, $liny);
                                $pdf->Cell(53,4, utf8_decode($descricao_conta[$codigo_conta]),1,0,'L');
                            }
                            $colx=40;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx+=18;

                                if (substr($codigo_conta, 1,6)==0){
                                    $pdf->SetFillColor(167,167,167); 
                                    $pdf->SetXY($colx, $liny);
                                    $pdf->Cell(18,4, number_format($total_realizado[$codigo_conta ][$i],2,',','.'),1,0,'R',1);
                                    }
                                else if (substr($codigo_conta, 3,4)==0){
                                    $pdf->SetFillColor(224,224,224); 
                                    $pdf->SetXY($colx, $liny);
                                    $pdf->Cell(18,4, number_format($total_realizado[$codigo_conta ][$i],2,',','.'),1,0,'R',1);
                                    }
                                else {
                                    $pdf->SetXY($colx, $liny);
                                    $pdf->Cell(18,4, number_format($total_realizado[$codigo_conta ][$i],2,',','.'),1,0,'R');
                                }
                            }
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

                    if ($liny>372) {
                        $array_retorno = salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano, $opc_rel);   

                        $pagina_atual=$array_retorno[0];
                        $liny=$array_retorno[1];
                    }

                    $pdf->SetFont('arial','B',7); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(53,4, 'Saldo Anterior',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_anterior_mes[$i]<0) {
                            $pdf->SetTextColor(210, 0, 0);
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                        else {
                            $pdf->SetTextColor(0, 0, 0);
                        }    

                        $colx+=18;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(18,4, number_format($saldo_anterior_mes[$i],2,',','.'),1,0,'R');
                    }

                    $colx+=18;
                    $pdf->SetXY($colx, $liny);
                    $pdf->Cell(18,4,'',1,0,'R');

                    $liny=$liny+4;
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('arial','B',7); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(53,4, 'Saldo do Mês',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_mes[$i]<0) {
                            $pdf->SetTextColor(210, 0, 0);
                        }
                        else if ($saldo_mes[$i]>0) {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                        else {
                            $pdf->SetTextColor(0, 0, 0);
                        }    
                        $colx+=18;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(18,4, number_format($saldo_mes[$i],2,',','.'),1,0,'R');
                    }
                    $colx+=18;
                    $pdf->SetXY($colx, $liny);
                    $pdf->Cell(18,4,'',1,0,'R');

                    $liny=$liny+4;
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('arial','B',7); 
                    $pdf->SetXY(5, $liny);
                    $pdf->Cell(53,4, 'Saldo Final',1,0,'R');
                    $colx=40;

                    for ($i=1; $i <= 12 ; $i++) { 
                        if ($saldo_final_mes[$i]<0) {
                            $pdf->SetTextColor(210, 0, 0);
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $pdf->SetTextColor(0, 128, 0);
                        }
                        else {
                            $pdf->SetTextColor(0, 0, 0);
                        }    
                        $colx+=18;
                        $pdf->SetXY($colx, $liny);
                        $pdf->Cell(18,4, number_format($saldo_final_mes[$i],2,',','.'),1,0,'R');
                    }
                    $colx+=18;
                    $pdf->SetXY($colx, $liny);
                    $pdf->Cell(18,4,'',1,0,'R');

                    $pdf->SetTextColor(0, 0, 0);
                   
                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {
                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $liny=$liny+4;

                            if (substr($codigo_conta, 1,6)==0){
                                $pdf->SetFillColor(167,167,167); 
                                $pdf->SetFont('arial','',6); 
                                $pdf->SetXY(5, $liny);
                                $pdf->Cell(53,4, utf8_decode($descricao_conta[$codigo_conta]),1,0,'L',1);
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                $pdf->SetFillColor(224,224,224); 
                                $pdf->SetFont('arial','',6); 
                                $pdf->SetXY(5, $liny);
                                $pdf->Cell(53,4, utf8_decode($descricao_conta[$codigo_conta]),1,0,'L',1);
                            }
                            else {
                                $pdf->SetFont('arial','',6); 
                                $pdf->SetXY(5, $liny);
                                $pdf->Cell(53,4, utf8_decode($descricao_conta[$codigo_conta]),1,0,'L');
                            }
                            $colx=40;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx+=18;

                                if (substr($codigo_conta, 1,6)==0){
                                    $pdf->SetFillColor(167,167,167); 
                                    $pdf->SetXY($colx, $liny);
                                    $pdf->Cell(18,4, number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.'),1,0,'R',1);
                                    }
                                else if (substr($codigo_conta, 3,4)==0){
                                    $pdf->SetFillColor(224,224,224); 
                                    $pdf->SetXY($colx, $liny);
                                    $pdf->Cell(18,4, number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.'),1,0,'R',1);
                                    }
                                else {
                                    $pdf->SetXY($colx, $liny);
                                    $pdf->Cell(18,4, number_format($total_nao_realizado[$codigo_conta ][$i],2,',','.'),1,0,'R');
                                }
                            }
                        }                        
                    } 
                } // fim do if $opc_rel==3


// FIM DO PROCESSAMENTO
$codigo_fornecedorpdf= 'fluxo_caixa_mensal.pdf';
ob_clean(); 
$pdf->Output($codigo_fornecedorpdf, "I");

mysqli_close($conector);

function salta_pagina($pdf, $liny, $numero_paginas, $pagina_atual, $mes_ano, $opc_rel) {

	$pagina_atual++;
	$_SESSION['nome_setor']='Página: ' . $pagina_atual . ' de ' . $numero_paginas;

	$pdf->AddPage();
	$liny=21;
    
	$pdf->SetFont('arial','',12); 
	$pdf->SetXY(5, $liny);
	$pdf->Cell(35,4, $mes_ano ,0,0,'L');

/*
	$liny=$liny+4;
	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Centro de Custos: ' . $desc_centro_custos ,0,0,'L');

	$liny=$liny+4;

	$pdf->SetXY(5, $liny);
	$pdf->Cell(60,4, 'Período: '.$data_inicio_edi->format('d/m/Y').' até '.$data_fim_edi->format('d/m/Y').
	  ' - Relatório ' . $desc_tipo_rel . $desc_tipo_data,0,0,'L');
*/
    $liny=$liny+5;

    if ($opc_rel==1){
        $pdf->SetFont('arial','',6); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(50,4, '',1,0,'R');
        $colx=55;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Janeiro',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Fevereiro',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Março',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Abril',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Maio',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Junho',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Julho',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Agosto',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Setembro',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Outubro',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Novembro',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Dezembro',1,0,'C');
        $colx+=30;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(30,4, 'Total',1,0,'C');


        $liny=$liny+4;

        $pdf->SetFont('arial','',6); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(50,4, '',1,0,'R');
        $colx=40;
        for ($i=1; $i <= 12 ; $i++) { 
            $colx+=15;
            $pdf->SetXY($colx, $liny);
            $pdf->Cell(15,4, 'Realizado',1,0,'C');
            $colx+=15;
            $pdf->SetXY($colx, $liny);
            $pdf->Cell(15,4, 'Não Realizado',1,0,'C');
        }
    }
    else if ($opc_rel==2){
        $pdf->SetFont('arial','',7); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(53,4, '',1,0,'R');
        $colx=40;

        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Janeiro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Fevereiro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Março',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Abril',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Maio',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Junho',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Julho',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Agosto',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Setembro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Outubro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Novembro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Dezembro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4,'Total',1,0,'C');

        $liny=$liny+4;

        $pdf->SetFont('arial','',6); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(53,4, '',1,0,'R');
        $colx=40;
        for ($i=1; $i <= 13 ; $i++) { 
            $colx+=18;
            $pdf->SetXY($colx, $liny);
            $pdf->Cell(18,4, 'Realizado',1,0,'C');
        }
    }
    else {
        $pdf->SetFont('arial','',7); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(53,4, '',1,0,'R');
        $colx=40;

        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Janeiro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Fevereiro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Março',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Abril',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Maio',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Junho',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Julho',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Agosto',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Setembro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Outubro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Novembro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4, 'Dezembro',1,0,'C');
        $colx+=18;
        $pdf->SetXY($colx, $liny);
        $pdf->Cell(18,4,'Total',1,0,'C');

        $liny=$liny+4;

        $pdf->SetFont('arial','',6); 
        $pdf->SetXY(5, $liny);
        $pdf->Cell(53,4, '',1,0,'R');
        $colx=40;
        for ($i=1; $i <= 13 ; $i++) { 
            $colx+=18;
            $pdf->SetXY($colx, $liny);
            $pdf->Cell(18,4, 'Não Realizado',1,0,'C');
        }
    }

	$liny=$liny+4;

	return [$pagina_atual, $liny];
}



?>