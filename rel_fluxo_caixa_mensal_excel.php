<?php
$data_sistema = date("d/m/Y");

$mes_atual = date('m');
$ano = $_REQUEST["ano"];
$opc_rel = $_REQUEST["opc_rel"];
$forma_pag = $_REQUEST["forma_pag"];

$data_inicial = $ano . '-01-01';
$data_final = $ano . '-12-31';

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

//      Começa Excel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$banco = $cnpj_cliente;
//$senha_bd = "";

// Servidor
$senha_bd = "a2ngei9Mxh";

$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd);
   
if (mysqli_connect_error()) {
    print_r("Falha na conexão: ", mysqli_connect_error());
    exit;
}

$bancoselecionado = mysqli_select_db($conector,$banco);

if ($bancoselecionado === FALSE) {
    print_r("Falha na seleção do banco de dados: " . mysqli_error($conector));
    exit;
}

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

if ($forma_pag==0) {
    $desc_forma_pag = 'Todas';
}
else {
    $forma_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento 
                where tbl_conta_pagamento_id='$forma_pag' and tbl_conta_pagamento_lixeira=0"); 
    $registro_forma_pag = mysqli_fetch_object($forma_pagamento);
    $desc_forma_pag = $registro_forma_pag->tbl_conta_pagamento_descricao;
}

$nome_relatorio = "Fluxo de Caixa Mensal";

if ($opc_rel==1) {
    $desc_opc_rel = 'Realizados/Não Realizados';
}
else if ($opc_rel==2) {
    $desc_opc_rel = 'Realizados';
}
else {
    $desc_opc_rel = 'Não Realizados';
} 

if ($opc_rel==1) {
    $spreadsheet->getActiveSheet()->mergeCells('B1:C1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:AA2');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('B1', 'Data: ' . $data_sistema)
        ->setCellValue("A2", "Ano: " . $ano . ' - Forma de Recebimento/Pagamento: ' . $desc_forma_pag . ' - ' . $desc_opc_rel);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("B3","Janeiro")
        ->setCellValue("D3","Fevereiro")
        ->setCellValue("F3","Março")
        ->setCellValue("H3","Abril")
        ->setCellValue("J3","Maio")
        ->setCellValue("L3","Junho")
        ->setCellValue("N3","Julho")
        ->setCellValue("P3","Agosto")
        ->setCellValue("R3","Setembro")
        ->setCellValue("T3","Outubro")
        ->setCellValue("V3","Novembro")
        ->setCellValue("X3","Dezembro")
        ->setCellValue("Z3","Totais")
        ->setCellValue("A5","Saldo Anterior")
        ->setCellValue("A6","Saldodo Mês")
        ->setCellValue("A7","Saldo Final")
        ->setCellValue("B4","Realizado")
        ->setCellValue("C4","Não Realizado")
        ->setCellValue("D4","Realizado")
        ->setCellValue("E4","Não Realizado")
        ->setCellValue("F4","Realizado")
        ->setCellValue("G4","Não Realizado")
        ->setCellValue("H4","Realizado")
        ->setCellValue("I4","Não Realizado")
        ->setCellValue("J4","Realizado")
        ->setCellValue("K4","Não Realizado")
        ->setCellValue("L4","Realizado")
        ->setCellValue("M4","Não Realizado")
        ->setCellValue("N4","Realizado")
        ->setCellValue("O4","Não Realizado")
        ->setCellValue("P4","Realizado")
        ->setCellValue("Q4","Não Realizado")
        ->setCellValue("R4","Realizado")
        ->setCellValue("S4","Não Realizado")
        ->setCellValue("T4","Realizado")
        ->setCellValue("U4","Não Realizado")
        ->setCellValue("V4","Realizado")
        ->setCellValue("W4","Não Realizado")
        ->setCellValue("X4","Realizado")
        ->setCellValue("Y4","Não Realizado")
        ->setCellValue("Z4","Realizado")
        ->setCellValue("AA4","Não Realizado");

    $spreadsheet->getActiveSheet()->getStyle('A5:A7') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->mergeCells('B3:C3');
    $spreadsheet->getActiveSheet()->mergeCells('D3:E3');
    $spreadsheet->getActiveSheet()->mergeCells('F3:G3');
    $spreadsheet->getActiveSheet()->mergeCells('H3:I3');
    $spreadsheet->getActiveSheet()->mergeCells('J3:K3');
    $spreadsheet->getActiveSheet()->mergeCells('L3:M3');
    $spreadsheet->getActiveSheet()->mergeCells('N3:O3');
    $spreadsheet->getActiveSheet()->mergeCells('P3:Q3');
    $spreadsheet->getActiveSheet()->mergeCells('R3:S3');
    $spreadsheet->getActiveSheet()->mergeCells('T3:U3');
    $spreadsheet->getActiveSheet()->mergeCells('V3:W3');
    $spreadsheet->getActiveSheet()->mergeCells('X3:Y3');
    $spreadsheet->getActiveSheet()->mergeCells('Z3:AA3');

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(50);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(14); 
    $celulas = 'B5'.':AA5';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B6'.':AA6';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B7'.':AA7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

}
else {
    $spreadsheet->getActiveSheet()->mergeCells('B1:C1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:AA2');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('B1', 'Data: ' . $data_sistema)
        ->setCellValue("A2", "Ano: " . $ano . ' - ' . $desc_opc_rel)
        ->setCellValue("A2", "Ano: " . $ano . ' - Forma de Recebimento/Pagamento: ' . $desc_forma_pag . ' - ' . $desc_opc_rel);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("B3","Janeiro")
        ->setCellValue("C3","Fevereiro")
        ->setCellValue("D3","Março")
        ->setCellValue("E3","Abril")
        ->setCellValue("F3","Maio")
        ->setCellValue("G3","Junho")
        ->setCellValue("H3","Julho")
        ->setCellValue("I3","Agosto")
        ->setCellValue("J3","Setembro")
        ->setCellValue("K3","Outubro")
        ->setCellValue("L3","Novembro")
        ->setCellValue("M3","Dezembro")
        ->setCellValue("N3","Totais")
        ->setCellValue("A4","Saldo Anterior")
        ->setCellValue("A5","Saldodo Mês")
        ->setCellValue("A6","Saldo Final");

    $spreadsheet->getActiveSheet()->getStyle('A4:A6') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(50);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(12);

    $celulas = 'B4'.':N4';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B5'.':N5';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $celulas = 'B6'.':N6';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

}


$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('B3:AA3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$linha=8;

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

                    $colx=1;
                    $linha=5;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna[$i].$linha;
                        if ($saldo_anterior_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes[$i]);

                        $colx++;
                        $celulas = $coluna_nao[$i].$linha;
                        if ($saldo_anterior_mes_nao[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_anterior_mes_nao[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes_nao[$i]);
                    }

                    $colx=1;
                    $linha=6;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna[$i].$linha;
                        if ($saldo_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes[$i]);

                        $colx++;
                        $celulas = $coluna_nao[$i].$linha;
                        if ($saldo_mes_nao[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_mes_nao[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes_nao[$i]);
                    }

                    $colx=1;
                    $linha=7;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna[$i].$linha;
                        if ($saldo_final_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes[$i]);

                        $colx++;
                        $celulas = $coluna_nao[$i].$linha;
                        if ($saldo_final_mes_nao[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_final_mes_nao[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes_nao[$i]);
                    }

                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {

                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $linha++;
                            $celulas = 'A'.$linha;

                            if (substr($codigo_conta, 1,6)==0){
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }
                            else {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }

                            $colx=1;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx++;
                                $celulas = $coluna[$i].$linha;
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                if (substr($codigo_conta, 1,6)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                }
                                else {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                }

                                $colx++;
                                $celulas = $coluna_nao[$i].$linha;
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                if (substr($codigo_conta, 1,6)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                                else {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                            }
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

                    $colx=1;
                    $linha=4;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_anterior_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes[$i]);
                    }

                    $colx=1;
                    $linha=5;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes[$i]);
                    }

                    $colx=1;
                    $linha=6;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_final_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes[$i]);
                    }

                    
                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {

                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $linha++;
                            $celulas = 'A'.$linha;

                            if (substr($codigo_conta, 1,6)==0){
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }
                            else {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }

                            $colx=1;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx++;
                                $celulas = $coluna_1[$i].$linha;
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                if (substr($codigo_conta, 1,6)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
                                }
                                else {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_realizado[$codigo_conta ][$i]);
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

                    $colx=1;
                    $linha=4;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_anterior_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_anterior_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_anterior_mes[$i]);
                    }

                    $colx=1;
                    $linha=5;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_mes[$i]);
                    }

                    $colx=1;
                    $linha=6;

                    for ($i=1; $i <= 12 ; $i++) { 
                        $colx++;
                        $celulas = $coluna_1[$i].$linha;
                        if ($saldo_final_mes[$i]<0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                        }
                        else if ($saldo_final_mes[$i]>0) {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                        }
                        else {
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
                        }
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $saldo_final_mes[$i]);
                    }

                    
                    foreach ($tem_valor as $key_tem_valor => $value_tem_valor) {

                        if ($value_tem_valor == "S"){
                            $codigo_conta = (int)$key_tem_valor;
                            $linha++;
                            $celulas = 'A'.$linha;

                            if (substr($codigo_conta, 1,6)==0){
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }
                            else if (substr($codigo_conta, 3,4)==0){
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }
                            else {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_conta[$codigo_conta]));
                            }

                            $colx=1;

                            for ($i=1; $i <= 13 ; $i++) { 
                                $colx++;
                                $celulas = $coluna_1[$i].$linha;
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
                                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                                if (substr($codigo_conta, 1,6)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                                else if (substr($codigo_conta, 3,4)==0){
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                                else {
                                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($colx, $linha, $total_nao_realizado[$codigo_conta ][$i]);
                                }
                            }
                        }
                    }
                } // fim do if $opc_rel==3

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="fluxo_caixa_mensal.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

mysqli_close($conector);
exit;


?>
              
                
