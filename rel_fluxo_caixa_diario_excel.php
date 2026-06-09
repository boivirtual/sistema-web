<?php
$data_sistema = date("d/m/Y");

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

if (substr($mes, 0,1)==0) {
    $ind_mes = substr($mes, 1,1);
}
else {
    $ind_mes = $mes;
}

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

$nome_relatorio = "Fluxo de Caixa Diário";

if ($tipo_rel==2) {
    $desc_opc_rel = 'Realizados';
}
else {
    $desc_opc_rel = 'Não Realizados';
} 

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');
$spreadsheet->getActiveSheet()->mergeCells('A2:D2');
$spreadsheet->getActiveSheet()->mergeCells('A3:D3');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio . ' - ' . $desc_opc_rel)
    ->setCellValue('D1', 'Data: ' . $data_sistema)
    ->setCellValue("A2", "Data: " . $array_mes[$ind_mes] . '/'. $ano)
    ->setCellValue("A3", "" . $descricao_filtro);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A5","Data")
    ->setCellValue("B5","Recebimentos")
    ->setCellValue("C5","Pagamentos")
    ->setCellValue("D5","Saldo")
    ->setCellValue("A6","Saldo Anterior")
    ->setCellValue("A7","Saldodo Mês")
    ->setCellValue("A8","Saldo Final");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(19);

$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('C5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('D5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->freezePane('E9');

$linha=8;

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
                 WHERE bcp_data_pagamento<'$data_inicial'" . $wcc_ctp . $wcpag_ctp . $wlocal_ctp); 

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
    
    $celulas = 'A6'.':D6';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');

    $celulas = 'D6';

    if ($total_saldo_anterior<0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else if ($total_saldo_anterior>0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
    }

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 6, $total_saldo_anterior);

    $celulas = 'A7'.':D7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');

    $celulas = 'B7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 7, $total_geral_recebido);

    $celulas = 'C7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 7, $total_geral_pago);

    $celulas = 'D7';

    if ($total_geral_mes<0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else if ($total_geral_mes>0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
    }

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 7, $total_geral_mes);


    $celulas = 'A8'.':D8';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D2D2D2');
    $celulas = 'D8';

    if ($total_geral_final<0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else if ($total_geral_final>0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
    }

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 8, $total_geral_final);

    for ($i=1; $i <= $dias_mes ; $i++) { 
        $linha++;
        $celulas = 'A'.$linha.':D'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
        $celulas = 'A'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $data_edi = $data_dia[$i]->format('d/m/Y');
        $data_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_edi);
        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_edi);

        $celulas = 'B'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_recebimentos_diario[$i]);

        $celulas = 'C'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_pagamentos_diario[$i]);

        $celulas = 'D'.$linha;

        if ($valor_saldo_diario[$i]<0) {
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
        }
        else if ($valor_saldo_diario[$i]>0) {
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        }
        else {
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
        }
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_saldo_diario[$i]);

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

    $celulas = 'A6'.':D6';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('EBEDEF');

    $celulas = 'D6';

    if ($total_saldo_anterior<0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else if ($total_saldo_anterior>0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
    }

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 6, $total_saldo_anterior);

    $celulas = 'A7'.':D7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D6DBDF');

    $celulas = 'B7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 7, $total_geral_recebido);

    $celulas = 'C7';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 7, $total_geral_pago);

    $celulas = 'D7';

    if ($total_geral_mes<0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else if ($total_geral_mes>0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
    }

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 7, $total_geral_mes);


    $celulas = 'A8'.':D8';
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('D2D2D2');
    $celulas = 'D8';

    if ($total_geral_final<0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
    }
    else if ($total_geral_final>0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
    }
    else {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
    }

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
    $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 8, $total_geral_final);

    for ($i=1; $i <= $dias_mes ; $i++) { 
        $linha++;
        $celulas = 'A'.$linha.':D'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
        $celulas = 'A'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $data_edi = $data_dia[$i]->format('d/m/Y');
        $data_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_edi);
        $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_edi);

        $celulas = 'B'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $valor_recebimentos_diario[$i]);

        $celulas = 'C'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $valor_pagamentos_diario[$i]);

        $celulas = 'D'.$linha;

        if ($valor_saldo_diario[$i]<0) {
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
        }
        else if ($valor_saldo_diario[$i]>0) {
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        }
        else {
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLACK));
        }
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);     
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $valor_saldo_diario[$i]);
    }
} // fim opcrel = 3



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="fluxo_caixa_diario.xlsx"');
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
              
                
