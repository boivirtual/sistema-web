<?php
    $data_sistema = date("d/m/Y");

    $mes_atual = date('m');

    $ano = $_POST["ano"];
    $opc_rel = $_POST["opc_rel"];

    $wforma_pag = '';
    $forma_pag = '';
    if (isset($_POST['forma_pag'])) {
        $forma_pag = $_POST['forma_pag'];

        if(in_array("", $forma_pag)) {
            $wforma_pag='';
        }
        else {
            $wforma_pag = " AND ctp_forma_pagamento IN(";
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
    $local_pag = '';
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

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$senha_bd = "";
$banco = "landes";

// Servidor
//$senha_bd = "a2ngei9Mxh";
//$banco = "landes";

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

    $saldo_anterior_realizado=0;
    $total_recebido=0;
    $total_pago=0;

    $contas_rec = mysqli_query($conector, "SELECT * FROM baixa_contas_receber
                                    INNER JOIN contas_receber
                                            ON bcr_numero_doc=ctr_numero_doc AND 
                                               bcr_parcela=ctr_parcela
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

//if ($forma_pag=='') {
//    $desc_forma_pag = 'Todas';
//}
//else {
//    $forma_pagamento = mysqli_query($conector, "select * from tbl_forma_rec_pag 
//                where tbl_forma_rec_pag_id='$forma_pag' and tbl_forma_rec_pag_lixeira=0"); 
//    $registro_forma_pag = mysqli_fetch_object($forma_pagamento);
//    $desc_forma_pag = $registro_forma_pag->tbl_forma_rec_pag_descricao;
//}

$desc_forma_pag = 'Todas';

if ($local_pag=='') {
    $desc_local_pag = 'Todas';
}
else {
    $local_pagamento = mysqli_query($conector, "select * from tbl_centro_custo 
                where tbl_cc_codigo_id='$local_pag' and tbl_cc_lixeira=0"); 
    $registro_local_pag = mysqli_fetch_object($local_pagamento);
    $desc_local_pag = $registro_local_pag->tbl_cc_descricao;
}


$nome_relatorio = "Análise de Contas Previsto/Realizado";

if ($opc_rel==1) {
    $desc_opc_rel = 'Realizados/Previsto';
}
else if ($opc_rel==2) {
    $desc_opc_rel = 'Realizados';
}
else {
    $desc_opc_rel = 'Previsto';
} 

if ($opc_rel==1) {
    $spreadsheet->getActiveSheet()->mergeCells('B1:C1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:AA2');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('B1', 'Data: ' . $data_sistema)
        ->setCellValue("A2", "Ano: " . $ano . ' - Forma de Pagamento: ' . $desc_forma_pag . ' - Local: ' . $desc_local_pag . ' - ' . $desc_opc_rel);

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
        ->setCellValue("C4","Previsto")
        ->setCellValue("D4","Realizado")
        ->setCellValue("E4","Previsto")
        ->setCellValue("F4","Realizado")
        ->setCellValue("G4","Previsto")
        ->setCellValue("H4","Realizado")
        ->setCellValue("I4","Previsto")
        ->setCellValue("J4","Realizado")
        ->setCellValue("K4","Previsto")
        ->setCellValue("L4","Realizado")
        ->setCellValue("M4","Previsto")
        ->setCellValue("N4","Realizado")
        ->setCellValue("O4","Previsto")
        ->setCellValue("P4","Realizado")
        ->setCellValue("Q4","Previsto")
        ->setCellValue("R4","Realizado")
        ->setCellValue("S4","Previsto")
        ->setCellValue("T4","Realizado")
        ->setCellValue("U4","Previsto")
        ->setCellValue("V4","Realizado")
        ->setCellValue("W4","Previsto")
        ->setCellValue("X4","Realizado")
        ->setCellValue("Y4","Previsto")
        ->setCellValue("Z4","Realizado")
        ->setCellValue("AA4","Previsto");

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
        ->setCellValue("A2", "Ano: " . $ano . ' - Forma de Pagamento: ' . $desc_forma_pag . ' - Local: ' . $desc_local_pag . ' - ' . $desc_opc_rel);

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

                        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  ctr_data_vencimento >='$data_inicial' AND
                                  ctr_data_vencimento <='$data_final'" . $wforma_rec  . $wlocal_rec . 
                                  "ORDER BY ctr_data_vencimento"); 

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $numero_id = $registro_contas_rec->ctr_numero_doc;
                                $parcela = $registro_contas_rec->ctr_parcela;
                                $data_vencimento = $registro_contas_rec->ctr_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);

                                if ($registro_contas_rec->ctr_situacao != '') {
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
                                }
                            } // fim while contas a receber
                        } // fim if rows contas receber

                        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  ctp_data_vencimento >='$data_inicial' AND
                                  ctp_data_vencimento <='$data_final'" . $wforma_pag  . $wlocal_pag . 
                                  " ORDER BY ctp_data_vencimento"); 
                        $num_rows_contas_pag = mysqli_num_rows($contas_pag);

                        if ($num_rows_contas_pag!=0){
                            while ($registro_contas_pag = mysqli_fetch_object($contas_pag)){ 
                                $numero_id = $registro_contas_pag->ctp_numero_doc;
                                $parcela = $registro_contas_pag->ctp_parcela;
                                $codigo_for = $registro_contas_pag->ctp_codigo_fornecedor;
                                $data_vencimento = $registro_contas_pag->ctp_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);

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
                                }
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

                        $contas_rec = mysqli_query($conector, "SELECT * FROM contas_receber
                            WHERE ctr_codigo_conta='$codigo_conta' AND
                                  ctr_data_vencimento >='$data_inicial' AND
                                  ctr_data_vencimento <='$data_final' AND
                                  (ctr_situacao='P' OR ctr_situacao='C')" . $wforma_rec  . $wlocal_rec . 
                                  " ORDER BY ctr_data_vencimento"); 

                        $num_rows_contas_rec = mysqli_num_rows($contas_rec);

                        if ($num_rows_contas_rec!=0){
                            while ($registro_contas_rec = mysqli_fetch_object($contas_rec)){ 
                                $numero_id = $registro_contas_rec->ctr_numero_doc;
                                $parcela = $registro_contas_rec->ctr_parcela;
                                $data_vencimento = $registro_contas_rec->ctr_data_vencimento;
                                $mes = (int)substr($data_vencimento, 5, 2);
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
                                }
                            } // fim while contas a receber
                        } // fim if rows contas receber

                        $contas_pag = mysqli_query($conector, "SELECT * FROM contas_pagar
                            WHERE ctp_codigo_conta='$codigo_conta' AND
                                  ctp_data_vencimento >='$data_inicial' AND
                                  ctp_data_vencimento <='$data_final' AND 
                                  (ctp_situacao='P' OR ctp_situacao='C')" . $wforma_pag  . $wlocal_pag . 
                                  " ORDER BY ctp_data_vencimento"); 

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
header('Content-Disposition: attachment;filename="analise_previsto_realizado.xlsx"');
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
              
                
