<?php

$data_sistema = date("d/m/Y");

// 		Começa Excel
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
$banco = $cnpj_cliente;
//$senha_bd = "";

// Servidor
include_once "conecta_mysql_credenciais.inc";

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

$local_filtro = $_REQUEST['local'];
$data_inicial = $_REQUEST["data_inicial"];
$data_final = $_REQUEST["data_final"];
$tipo_periodo_lote = $_REQUEST["tipo_periodo_lote"];
$descricao_filtro= $_REQUEST["descricao_filtro"];
$origem_relatorio=$_REQUEST['tipo_relatorio'];

$lote_filtro = $_REQUEST["lote"];

if ($tipo_periodo_lote=='P') {
    $lote= array();
    $matriz_itens = explode(",", $lote_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $lote[$i]=$matriz_itens[$i];
    }

    $lote = implode(',', $lote);
    $lote = substr($lote,0, -1);

    $wlote = '';

    if ($lote_filtro!='') {
        $wlote = " AND tbl_nutricao_id_lote IN(";
        $wlote.= $lote;
        $wlote.= ")";
    }
}
else {
    $wlote = " AND tbl_nutricao_id_lote IN(";
        $wlote.= $lote_filtro;
        $wlote.= ")";
}

$pasto_filtro = $_REQUEST["pasto"];
$pasto= array();
$matriz_itens = explode(",", $pasto_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $pasto[$i]=$matriz_itens[$i];
}

$pasto = implode(',', $pasto);
$pasto = substr($pasto,0, -1);

$wpasto= '';

if ($pasto_filtro!='') {
    $wpasto = " AND tbl_nutricao_codigo_pasto IN(";
    $wpasto.= $pasto;
    $wpasto.= ")";
}

$produto_filtro = $_REQUEST["produto"];
$produto= array();
$matriz_itens = explode(",", $produto_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $produto[$i]=$matriz_itens[$i];
}

$produto = implode(',', $produto);
$produto = substr($produto,0, -1);

$wproduto= '';

if ($produto_filtro!='') {
    $wproduto = " AND tbl_nutricao_codigo_produto IN(";
    $wproduto.= $produto;
    $wproduto.= ")";
}

if ($data_inicial=='' && $data_final==''){
    $wperiodo = '';
}
else {
    $wperiodo = " AND tbl_nutricao_data >= '$data_inicial' AND tbl_nutricao_data <= '$data_final'";
}

$array_coluna[1] = 'F';
$array_coluna[2] = 'G';
$array_coluna[3] = 'H';
$array_coluna[4] = 'I';
$array_coluna[5] = 'J';
$array_coluna[6] = 'K';
$array_coluna[7] = 'L';
$array_coluna[8] = 'M';
$array_coluna[9] = 'N';
$array_coluna[10] = 'O';
$array_coluna[11] = 'P';
$array_coluna[12] = 'Q';
$array_coluna[13] = 'R';
$array_coluna[14] = 'S';
$array_coluna[15] = 'T';
$array_coluna[16] = 'U';
$array_coluna[17] = 'V';
$array_coluna[18] = 'W';
$array_coluna[19] = 'X';
$array_coluna[20] = 'Y';
$array_coluna[21] = 'Z';
$array_coluna[22] = 'AA';
$array_coluna[23] = 'AB';
$array_coluna[24] = 'AC';
$array_coluna[25] = 'AD';
$array_coluna[26] = 'AE';
$array_coluna[27] = 'AF';
$array_coluna[28] = 'AG';
$array_coluna[29] = 'AH';
$array_coluna[30] = 'AI';
$array_coluna[31] = 'AJ';

$coluna_index[1] = 5;
$coluna_index[2] = 6;
$coluna_index[3] = 7;
$coluna_index[4] = 8;
$coluna_index[5] = 9;
$coluna_index[6] = 10;
$coluna_index[7] = 11;
$coluna_index[8] = 12;
$coluna_index[9] = 13;
$coluna_index[10] = 14;
$coluna_index[11] = 15;
$coluna_index[12] = 16;
$coluna_index[13] = 17;
$coluna_index[14] = 18;
$coluna_index[15] = 19;
$coluna_index[16] = 20;
$coluna_index[17] = 21;
$coluna_index[18] = 22;
$coluna_index[19] = 23;
$coluna_index[20] = 24;
$coluna_index[21] = 25;
$coluna_index[22] = 26;
$coluna_index[23] = 27;
$coluna_index[24] = 28;
$coluna_index[25] = 29;
$coluna_index[26] = 30;
$coluna_index[27] = 31;
$coluna_index[28] = 32;
$coluna_index[29] = 33;
$coluna_index[30] = 34;
$coluna_index[31] = 35;

$nome_relatorio = "Consumo de Nutrição";

if ($tipo_periodo_lote=='P') {
    $spreadsheet->getActiveSheet()->mergeCells('A1:M1');
    $spreadsheet->getActiveSheet()->mergeCells('N1:AL1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:AL2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:AL3');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('N1', 'Data: ' . $data_sistema)
        ->setCellValue("A2", 'Filtros:' . $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('N1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->mergeCells('A4:A5');
    $spreadsheet->getActiveSheet()->mergeCells('B4:B5');
    $spreadsheet->getActiveSheet()->mergeCells('C4:C5');
    $spreadsheet->getActiveSheet()->mergeCells('D4:D5');
    $spreadsheet->getActiveSheet()->mergeCells('E4:E5');
    $spreadsheet->getActiveSheet()->mergeCells('F4:AL4');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Descrição do Lote")
        ->setCellValue("B4","Lote")
        ->setCellValue("C4","Nº de Cabeças")
        ->setCellValue("D4","Pasto Atual")
        ->setCellValue("E4","Tipo de Nutrição")
        ->setCellValue("F4","Quandidade em gramas por cabeça ao longo do mês");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("F5","1")
        ->setCellValue("G5","2")
        ->setCellValue("H5","3")
        ->setCellValue("I5","4")
        ->setCellValue("J5","5")
        ->setCellValue("K5","6")
        ->setCellValue("L5","7")
        ->setCellValue("M5","8")
        ->setCellValue("N5","9")
        ->setCellValue("O5","10")
        ->setCellValue("P5","11")
        ->setCellValue("Q5","12")
        ->setCellValue("R5","13")
        ->setCellValue("S5","14")
        ->setCellValue("T5","15")
        ->setCellValue("U5","16")
        ->setCellValue("V5","17")
        ->setCellValue("W5","18")
        ->setCellValue("X5","19")
        ->setCellValue("Y5","20")
        ->setCellValue("Z5","21")
        ->setCellValue("AA5","22")
        ->setCellValue("AB5","23")
        ->setCellValue("AC5","24")
        ->setCellValue("AD5","25")
        ->setCellValue("AE5","26")
        ->setCellValue("AF5","27")
        ->setCellValue("AG5","28")
        ->setCellValue("AH5","29")
        ->setCellValue("AI5","30")
        ->setCellValue("AJ5","31")
        ->setCellValue("AK5","Nº Dias")
        ->setCellValue("AL5","Consumo/Cab/Dia");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(23);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AB')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AD')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AE')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AF')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AI')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AJ')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('AK')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('AL')->setWidth(17);

    $spreadsheet->getActiveSheet()->getStyle('E5:AL5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('F4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical($align);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('F5:AL5')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('F5:AL5')->getAlignment()->setVertical($align);

}
else {
    $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:H2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:E3');
    $spreadsheet->getActiveSheet()->mergeCells('F3:H3');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('H1', 'Data: ' . $data_sistema)
        ->setCellValue("A2", 'Filtros:' . $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('H1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Data")
        ->setCellValue("B4","Descrição do Lote")
        ->setCellValue("C4","Nº de Cabeças")
        ->setCellValue("D4","Pasto")
        ->setCellValue("E4","Produto")
        ->setCellValue("F4","Quantidade Colocada no Cocho")
        ->setCellValue("G4","Qtde/Cabeça")
        ->setCellValue("H4","Consumo Cabeça g/dia");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(23);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(28);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(22);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical($align);
}

//$spreadsheet->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(10);

$spreadsheet->getActiveSheet()->setShowGridlines(true);

$styleArray = [
    'borders' => [
        'top' => [
        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
        'right' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
        'left' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];


if ($tipo_periodo_lote=='P') { 
    // Tipo de relatório por Periodo pode ser por varios lotes
    $linha=5;
    $sql = "SELECT * from tbl_nutricao
        INNER JOIN tbl_pessoa
                ON tbl_nutricao_codigo_local = tbl_pessoa_id
        INNER JOIN tbl_produto
                ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 

        WHERE tbl_nutricao_lixeira=0 AND 
              tbl_nutricao_codigo_local='$local_filtro'".$wperiodo . $wlote . $wproduto . $wpasto .
        " ORDER BY tbl_nutricao_id_lote ASC, tbl_nutricao_data ASC"; 

    $tbl_nutricao = mysqli_query($conector, $sql);

    $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

    $lote_anterior = 0;
    $total_nutricao_dia = 0;

    if ($num_rows_nutricao!=0) {
            while ($reg_nutricao = mysqli_fetch_object($tbl_nutricao)) {
                $qtd_produto = $reg_nutricao->tbl_nutricao_quantidade_produto;
                $qtd_animais = $reg_nutricao->tbl_nutricao_qtd_animais;
                $consumo_cabeca_gramas = ($qtd_produto/$qtd_animais)*1000;
                $codigo_produto = $reg_nutricao->tbl_nutricao_codigo_produto;
                $codigo_pasto = $reg_nutricao->tbl_nutricao_codigo_pasto;
                $lote_id = $reg_nutricao->tbl_nutricao_id_lote;

                if ($lote_id!=$lote_anterior) {
                    if ($lote_anterior==0) {
                        $lote_anterior=$lote_id;
                        //$descricao_pasto = utf8_encode($reg_nutricao->tbl_pasto_descricao);
                        $qtd_animais_anterior = intval($reg_nutricao->tbl_nutricao_qtd_animais);

                        /*$descricao_lote = 
                        strstr(utf8_encode($reg_nutricao->tbl_nutricao_lote_pasto), " L-", true);

                        if ($descricao_lote=='') {
                            $descricao_lote = utf8_encode($reg_nutricao->tbl_pasto_descricao_lote);
                        }*/

                        $total_nutricao_dia = $consumo_cabeca_gramas;

                        for ($i=1; $i<=31; $i++){
                            $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                            $valor[$i]='';
                            $encerramento[$i]='';
                        }

                        $dia = substr($reg_nutricao->tbl_nutricao_data, 8, 2);
                        $valor[$dia] = $consumo_cabeca_gramas;

                        if ($reg_nutricao->tbl_nutricao_data_encerramento!='') {
                            $dia = substr($reg_nutricao->tbl_nutricao_data_encerramento, 8, 2);
                            $encerramento[$dia]=$dia;
                        }
                    } 
                    else {
                        // Imprime lote

                        $quantidade_dias = calcular_dias($conector, $local_filtro, $lote_anterior, $data_inicial, $data_final, $tipo_periodo_lote);

                        $media_consumo = $total_nutricao_dia/$quantidade_dias[0];
                        $consumo_edi = number_format($media_consumo, 0, ",", ".");

                        $descricao_produto = 
                        monta_produto($conector, $local_filtro, $lote_anterior, $data_inicial, $data_final);


                        if (strpos($lote_anterior, '/') === false) {
                            $lote_anterior_edi = substr_replace($lote_anterior, '/', -4, 0);
                        }

                        $descricao_pasto_lote= pega_descricao_pasto($conector, $local_filtro, $lote_anterior, $wpasto, $pasto_filtro);

                        $descricao_pasto = $descricao_pasto_lote[0];
                        $descricao_lote = $descricao_pasto_lote[1];

                        if ($descricao_pasto!='') {
                            $linha++;

                            $celulas = 'A'.$linha.':D'.$linha;
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                            $celulas = 'C'.$linha;
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                            $celulas = 'E'.$linha;
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                            $celulas = 'F'.$linha.':AK'.$linha;
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                            $celulas = 'AL'.$linha;
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                            $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                            $celulas = 'E' . $linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_lote));
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $lote_anterior_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_animais_anterior);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, utf8_encode($descricao_pasto));
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_produto);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(37, $linha, $quantidade_dias[0]);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(38, $linha, $consumo_edi.' g');

                            $coluna = 5;
                            $dia_encerramento = 99;

                            $partesData = explode('-', $data_final);
                            $dia_final = $partesData[2];

                            for ($i=1; $i<=31; $i++){
                                $coluna++;
                                $index_coluna = $i;

                                $i = str_pad($i, 2, "0", STR_PAD_LEFT);

                                if ($encerramento[$i]!='') {
                                    $dia_encerramento = $encerramento[$i];
                                }

                                if ($valor[$i]=='' && $i<=$dia_encerramento) {
                                    $partesData = explode('-', $data_inicial);

                                    $ano = $partesData[0];
                                    $mes = $partesData[1];

                                    $data_verificacao = $ano.'-'.$mes.'-'.$i;

                                    $tem_dias_anteriores = verificar_dias_anteriores($conector, $local_filtro, $lote_anterior, $data_verificacao);

                                    if ($tem_dias_anteriores=='N') {
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,$valor[$i]);

                                        if ($i==$dia_encerramento) {
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

                                            // marca de amarelo a coluna do id do lote
                                            $celulas = 'B' . $linha; 
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');
                                        }
                                    }
                                    else {
                                        if ($i<=$dia_final) {
                                            $valor[$i]='*';
                                        }

                                        $celulas = $array_coluna[$index_coluna] . $linha;
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,$valor[$i]);

                                        if ($i==$dia_encerramento) {
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

                                            // marca de amarelo a coluna do id do lote
                                            $celulas = 'B' . $linha; 
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');
                                        }
                                    }
                                }
                                else if ($valor[$i]=='') {
                                    //print_r('Passo 2 valor = espaco' . $valor[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,$valor[$i]);
                                }
                                else {
                                    //print_r('Passo 3 ' . $valor[$i]);
                                    $valor[$i] = round($valor[$i]);
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,intval($valor[$i]));

                                    if ($i==$dia_encerramento) {
                                        $celulas = $array_coluna[$index_coluna] . $linha;

                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

                                        // marca de amarelo a coluna do id do lote
                                        $celulas = 'B' . $linha; 
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');
                                    }
                                }
                            }
                        }

                        $lote_anterior=$lote_id;
                        //$descricao_pasto = utf8_encode($reg_nutricao->tbl_pasto_descricao);
                        $qtd_animais_anterior = intval($reg_nutricao->tbl_nutricao_qtd_animais);
                        /*$descricao_lote = 
                        strstr(utf8_encode($reg_nutricao->tbl_nutricao_lote_pasto), " L-", true);

                        if ($descricao_lote=='') {
                            $descricao_lote = utf8_encode($reg_nutricao->tbl_pasto_descricao_lote);
                        }*/

                        $total_nutricao_dia = $consumo_cabeca_gramas;

                        for ($i=1; $i<=31; $i++){
                            $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                            $valor[$i]='';
                            $encerramento[$i]='';
                        }

                        $dia = substr($reg_nutricao->tbl_nutricao_data, 8, 2);
                        $valor[$dia] = $consumo_cabeca_gramas;

                        if ($reg_nutricao->tbl_nutricao_data_encerramento!='') {
                            $dia = substr($reg_nutricao->tbl_nutricao_data_encerramento, 8, 2);
                            $encerramento[$dia]=$dia;
                        }
                    }
                }
                else {
                    // faz contas aqui
                    $total_nutricao_dia+=$consumo_cabeca_gramas;

                    $dia = substr($reg_nutricao->tbl_nutricao_data, 8, 2);
                    if ($valor[$dia]=='') {
                        $valor[$dia]=0;
                    }
                    
                    $valor[$dia]+= $consumo_cabeca_gramas;

                    if ($reg_nutricao->tbl_nutricao_data_encerramento!='') {
                        $dia = substr($reg_nutricao->tbl_nutricao_data_encerramento, 8, 2);
                        $encerramento[$dia]=$dia;
                    }
                }
            }

            // Imprime lote final do while

            $quantidade_dias = calcular_dias($conector, $local_filtro, $lote_anterior, $data_inicial, $data_final, $tipo_periodo_lote);

            //print_r($quantidade_dias);

            $media_consumo =$total_nutricao_dia/$quantidade_dias[0];
            $consumo_edi = number_format($media_consumo, 0, ",", ".");

            $descricao_produto = 
            monta_produto($conector, $local_filtro, $lote_anterior, 
                $data_inicial, $data_final);

            if (strpos($lote_anterior, '/') === false) {
                $lote_anterior_edi = substr_replace($lote_anterior, '/', -4, 0);
            }

            $descricao_pasto_lote= pega_descricao_pasto($conector, $local_filtro, $lote_anterior, $wpasto, $pasto_filtro);

            $descricao_pasto = $descricao_pasto_lote[0];
            $descricao_lote = $descricao_pasto_lote[1];

            if ($descricao_pasto!='') {
                $linha++;

                $celulas = 'A'.$linha.':D'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'C'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'E'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'F'.$linha.':AK'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'AL'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'E' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_lote));
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $lote_anterior_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_animais_anterior);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, utf8_encode($descricao_pasto));
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_produto);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(37, $linha, $quantidade_dias[0]);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(38, $linha, $consumo_edi.' g');

                $coluna = 5;
                $dia_encerramento = 99;

                $partesData = explode('-', $data_final);
                $dia_final = $partesData[2];

                for ($i=1; $i<=31; $i++){
                    $coluna++;
                    $index_coluna = $i;

                    $i = str_pad($i, 2, "0", STR_PAD_LEFT);

                    if ($encerramento[$i]!='') {
                        $dia_encerramento = $encerramento[$i];
                    }

                    if ($valor[$i]=='' && $i<=$dia_encerramento) {
                        $partesData = explode('-', $data_inicial);

                        $ano = $partesData[0];
                        $mes = $partesData[1];

                        $data_verificacao = $ano.'-'.$mes.'-'.$i;

                        $tem_dias_anteriores = verificar_dias_anteriores($conector, $local_filtro, $lote_anterior, $data_verificacao);

                        if ($tem_dias_anteriores=='N') {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,$valor[$i]);

                            if ($i==$dia_encerramento) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

                                // marca de amarelo a coluna do id do lote
                                $celulas = 'B' . $linha; 
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');
                            }
                        }
                        else {
                            if ($i<=$dia_final) {
                                $valor[$i]='*';
                            }

                            $celulas = $array_coluna[$index_coluna] . $linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_GRAY));
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,$valor[$i]);

                            if ($i==$dia_encerramento) {
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

                                // marca de amarelo a coluna do id do lote
                                $celulas = 'B' . $linha; 
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');
                            }
                        }
                    }
                    else if ($valor[$i]=='') {
                        //print_r('Passo 2 valor = espaco' . $valor[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,$valor[$i]);
                    }
                    else {
                        //print_r('Passo 3 ' . $valor[$i]);
                        $valor[$i] = round($valor[$i]);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha,intval($valor[$i]));

                        if ($i==$dia_encerramento) {
                            $celulas = $array_coluna[$index_coluna] . $linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

                            // marca de amarelo a coluna do id do lote
                            $celulas = 'B' . $linha; 
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');
                        }
                    }
                }
            }
    }
    // imprime os lotes que estão sem nutricao
    $sql = "SELECT * from tbl_pasto
        WHERE tbl_pasto_lixeira=0 AND 
            tbl_pasto_codigo_local='$local_filtro' AND 
            (tbl_pasto_id_lote!='' OR tbl_pasto_id_lote IS NOT NULL)
        ORDER BY tbl_pasto_id_lote ASC, tbl_pasto_ano_lote ASC"; 

    $tbl_pasto = mysqli_query($conector, $sql);

    $num_rows_pasto = mysqli_num_rows($tbl_pasto);

    if ($num_rows_pasto!=0) {
        while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
            $pasto_id = $reg_pasto->tbl_pasto_id;
            $lote_id = $reg_pasto->tbl_pasto_id_lote.$reg_pasto->tbl_pasto_ano_lote;

            $sql = "SELECT * from tbl_nutricao
                WHERE tbl_nutricao_id_lote='$lote_id' AND 
                      tbl_nutricao_lixeira=0 AND 
                      tbl_nutricao_codigo_local='$local_filtro'" . $wperiodo; 

            $tbl_nutricao = mysqli_query($conector, $sql);
            $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

            if ($num_rows_nutricao==0) {
                $descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
                $descricao_pasto = $reg_pasto->tbl_pasto_descricao;
                $lote_edi = $reg_pasto->tbl_pasto_id_lote.'/'.$reg_pasto->tbl_pasto_ano_lote;
                    
                $sql = mysqli_query($conector, "SELECT * from tbl_animal_pasto
                    WHERE tbl_animal_pasto_id='$pasto_id'"); 

                $num_rows_animais = mysqli_num_rows($sql);

                $linha++;

                $celulas = 'A'.$linha.':D'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'C'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $celulas = 'A'.$linha.':D'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('ed7672'));

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, utf8_encode($descricao_lote));
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $lote_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $num_rows_animais);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, utf8_encode($descricao_pasto));
            }
        }                      
    }
}
else { 
    // Tipo de relatório por apenas 1 lote (pode ser por periodo tambem)
    $linha=4;
        $data_anterior = 0;
        $total_nutricao_dia = 0;
        $desc_produto_anterior = '';
        $qtd_produto_anterior = 0;
        $qtd_por_cabeca_grama_anterior =0;
        $dias_consumo_anterior = 0;
        $consumo_cabeca_dia_anterior = 0;
        $codigo_score_anterior = 0;
        $total_consumo_cabeca_dia=0;
        $total_dias=0;

        $sql = "SELECT * FROM tbl_nutricao
            INNER JOIN tbl_pasto 
                    ON tbl_pasto_id = tbl_nutricao_codigo_pasto
            INNER JOIN tbl_produto
                    ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 
            WHERE tbl_nutricao_lixeira=0 AND 
                  tbl_nutricao_codigo_local='$local_filtro'" . $wperiodo . $wlote . $wpasto . $wproduto .
            "ORDER BY tbl_nutricao_data DESC";

        $rs = mysqli_query($conector, $sql); 

        while ($reg_nut = mysqli_fetch_object($rs)){
            $codigo_nutricao_id = $reg_nut->tbl_nutricao_id;
            $codigo_local = $reg_nut->tbl_nutricao_codigo_local;
            $codigo_produto = $reg_nut->tbl_nutricao_codigo_produto;
            $desc_produto = utf8_encode($reg_nut->tbl_produto_descricao);
            $codigo_pasto = $reg_nut->tbl_nutricao_codigo_pasto;
            $desc_pasto = utf8_encode($reg_nut->tbl_pasto_descricao);
            $lote = utf8_encode($reg_nut->tbl_nutricao_lote_pasto);
            $id_lote = $reg_nut->tbl_nutricao_id_lote;
                
            $dias_consumo = $reg_nut->tbl_nutricao_dias_consumo;
            $encerrada = $reg_nut->tbl_nutricao_encerrada;
            $consumo_cabeca_dia = $reg_nut->tbl_nutricao_consumo_cabeca_dia;
            $qtd_animais = intval($reg_nut->tbl_nutricao_qtd_animais); 
            $qtd_produto = $reg_nut->tbl_nutricao_quantidade_produto; 

            $data_nutricao = new DateTime($reg_nut->tbl_nutricao_data);
            $data_nutricao_edi = $data_nutricao->format('d/m/Y');
            $data_nutricao = $reg_nut->tbl_nutricao_data;

            $qtd_por_cabeca_grama = ($qtd_produto / $qtd_animais)*1000;

            if ($data_nutricao!=$data_anterior) {
                if ($data_anterior==0) {
                    $data_anterior=$data_nutricao;
                    $qtd_animais_anterior = $qtd_animais;
                    $desc_pasto_anterior = $desc_pasto;

                    $qtd_produto_anterior=$qtd_produto;
                    $qtd_por_cabeca_grama_anterior=$qtd_por_cabeca_grama;
                    $total_nutricao_dia=$qtd_por_cabeca_grama;

                    $desc_produto_anterior = $desc_produto.'/';

                    if ($encerrada=='S') {
                        $desc_score_anterior = 'Nutrição encerrada';
                        $consumo_cabeca_dia_anterior = $consumo_cabeca_dia; 
                        $dias_consumo_anterior = $dias_consumo;
                    }
                    else {
                        $calculos = calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                        $dias_consumo_anterior = $calculos[0];
                        $consumo_cabeca_dia_anterior = $calculos[1];
                        $codigo_score = $calculos[2];

                        $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                        $num_rows = mysqli_num_rows($tbl_score);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tbl_score);
                            $desc_score_anterior = $reg->tbl_score_descricao;
                        }
                        else {
                            $desc_score_anterior = '';
                        }
                    }
                }
                else {
                    $data_anterior = new DateTime($data_anterior);
                    $data_nutricao_edi = $data_anterior->format('d/m/Y');

                    $qtd_produto_edi = number_format($qtd_produto_anterior, 2, ",", ".");
                    $qtd_por_cabeca_grama_edi = number_format($qtd_por_cabeca_grama_anterior, 2, ",", ".").' g';

                    $quantidade_dias = calcular_dias($conector, $local_filtro, $id_lote, $data_inicial, $data_final, $tipo_periodo_lote);

                    $total_consumo_cabeca_dia+=$qtd_por_cabeca_grama_anterior;

                    if ($dias_consumo_anterior==0) {
                        $consumo_cabeca = '';
                        $total_dias+=$quantidade_dias[0];
                    }
                    else {
                        $consumo_cabeca = number_format($consumo_cabeca_dia_anterior, 0, ",", ".") .' g em ' . $dias_consumo_anterior . ' dia(s)';
                        $total_dias+=$dias_consumo_anterior;
                    }

                    $desc_produto_anterior = substr($desc_produto_anterior, 0, -1);

                        $linha++;

                        $celulas = 'A'.$linha;
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                        $celulas = 'B'.$linha.':D'.$linha;;
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                        $celulas = 'C'.$linha;
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                        $celulas = 'F'.$linha.':H'.$linha;
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                        $celulas = 'E'.$linha;
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                        $celulas = 'E' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_nutricao_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $lote);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_animais_anterior);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_pasto_anterior);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $desc_produto_anterior);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_produto_edi." Kg");
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_por_cabeca_grama_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $consumo_cabeca);

                        /*echo "<td style='vertical-align: middle;text-align:center;' width='15%'>";
                        echo $consumo_cabeca; // Mantém o valor original
                        echo "<i class='icon_info_alt btn' data-toggle='tooltip' data-placement='left' title='Cocho: ".$desc_score_anterior."' style='font-size: 10px;'></i>"; // Ícone com o tooltip
                        echo "</td>";*/                

                    $data_anterior=$data_nutricao;
                    $qtd_animais_anterior = $qtd_animais;
                    $desc_pasto_anterior = $desc_pasto;

                    $qtd_produto_anterior=$qtd_produto;
                    $qtd_por_cabeca_grama_anterior=$qtd_por_cabeca_grama;
                    $total_nutricao_dia=$qtd_por_cabeca_grama;

                    $desc_produto_anterior = $desc_produto.'/';

                    if ($encerrada=='S') {
                        $desc_score_anterior = 'Nutrição encerrada';
                        $consumo_cabeca_dia_anterior = $consumo_cabeca_dia; 
                        $dias_consumo_anterior = $dias_consumo;
                    }
                    else {
                        $calculos = calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                        $dias_consumo_anterior = $calculos[0];
                        $consumo_cabeca_dia_anterior = $calculos[1];
                        $codigo_score = $calculos[2];

                        $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                        $num_rows = mysqli_num_rows($tbl_score);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tbl_score);
                            $desc_score_anterior = $reg->tbl_score_descricao;
                        }
                        else {
                            $desc_score_anterior = '';
                        }
                    }
                }
            }
            else {
                $qtd_produto_anterior+=$qtd_produto;
                $qtd_por_cabeca_grama_anterior+=$qtd_por_cabeca_grama;
                $desc_produto_anterior.= $desc_produto.'/';
                $total_nutricao_dia+=$qtd_por_cabeca_grama;

                if ($encerrada=='S') {
                    $desc_score_anterior = 'Nutrição encerrada';
                    $consumo_cabeca_dia_anterior+= $consumo_cabeca_dia; 
                    $dias_consumo_anterior = $dias_consumo;
                }
                else {
                    $calculos = calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                    $dias_consumo_anterior = $calculos[0];
                    $consumo_cabeca_dia_anterior+= $calculos[1];
                    $codigo_score = $calculos[2];

                    $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                    $num_rows = mysqli_num_rows($tbl_score);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_score);
                        $desc_score_anterior = $reg->tbl_score_descricao;
                    }
                    else {
                        $desc_score_anterior = '';
                    }
                }
            }
        } // Fim while 

        $data_anterior = new DateTime($data_anterior);
        $data_nutricao_edi = $data_anterior->format('d/m/Y');

        $qtd_produto_edi = number_format($qtd_produto_anterior, 2, ",", ".");
        $qtd_por_cabeca_grama_edi = number_format($qtd_por_cabeca_grama_anterior, 2, ",", ".").' g';

        $quantidade_dias = calcular_dias($conector, $local_filtro, $id_lote, $data_inicial, $data_final, $tipo_periodo_lote);

        $total_consumo_cabeca_dia+=$qtd_por_cabeca_grama_anterior;

        if ($dias_consumo_anterior==0) {
            $consumo_cabeca = '';
            $total_dias+=$quantidade_dias[0];
        }
        else {
            $consumo_cabeca = number_format($consumo_cabeca_dia_anterior, 0, ",", ".") .' g em ' . $dias_consumo_anterior . ' dia(s)';
            $total_dias+=$dias_consumo_anterior;
        }

        $desc_produto_anterior = substr($desc_produto_anterior, 0, -1);

    $linha++;

    $celulas = 'A'.$linha;
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    $celulas = 'B'.$linha.':D'.$linha;;
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    $celulas = 'C'.$linha;
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    $celulas = 'F'.$linha.':H'.$linha;
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    $celulas = 'E'.$linha;
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    $celulas = 'E' . $linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_nutricao_edi);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $lote);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd_animais_anterior);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_pasto_anterior);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $desc_produto_anterior);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_produto_edi." Kg");
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $qtd_por_cabeca_grama_edi);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $consumo_cabeca);

    /*echo "<td style='vertical-align: middle;text-align:center;' width='15%'>";
    echo $consumo_cabeca; // Mantém o valor original
    echo "<i class='icon_info_alt btn' data-toggle='tooltip' data-placement='left' title='Cocho: ".$desc_score_anterior."' style='font-size: 10px;'></i>"; // Ícone com o tooltip
    echo "</td>";*/      

    $media_geral = $total_consumo_cabeca_dia/$total_dias;
    $media_geral_edi = number_format($media_geral, 0, ",", ".");
}

if ($tipo_periodo_lote=='P') {
    $linha = $linha + 2;
    $celulas = 'A' . $linha; 

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, "Legenda");

    $linha++;
    $celulas = 'A' . $linha; 

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('f5e105');

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, "Nutrição encerrada/grupo se desfez");

    $linha++;
    $celulas = 'A' . $linha; 

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(10);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('ed7672'));

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, "Sem Nutrição no período");
}
else {
    $celulas = 'F3';
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_LIGTHBLUE));

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 3, 'Média de Consumo Geral dentro do período: ' .  $media_geral_edi . ' g/cab/dia em ' . $total_dias . ' dia(s)');
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

$data = new DateTime($data_final);
$dias_do_mes = $data->format('t');

$sheet = $spreadsheet->getActiveSheet();

if ($dias_do_mes < 31) {
    $sheet->removeColumnByIndex(36);
}

if ($dias_do_mes < 30) {
    $sheet->removeColumnByIndex(35);
}

if ($dias_do_mes < 29) {
    $sheet->removeColumnByIndex(34);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="consumo_nutricao.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
header('Cache-Control: cache, must-revalidate'); 
header('Pragma: public'); 

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

mysqli_close($conector);
exit;

    function pega_descricao_pasto($conector, $local_filtro, $lote_anterior, $wpasto, $pasto_filtro) {

        /*$partes = explode("/", $lote_anterior);
        $numero_lote = $partes[0];
        $ano_lote = $partes[1];*/        

        $descricao_pasto_atual = '';

        $numero_lote = substr($lote_anterior, 0, 4);
        $ano_lote = substr($lote_anterior, 4, 4);

        $sql = "SELECT * from tbl_pasto
            WHERE tbl_pasto_codigo_local = '$local_filtro' AND 
                  tbl_pasto_id_lote='$numero_lote' AND 
                  tbl_pasto_ano_lote='$ano_lote'"; 

        $tbl_pasto = mysqli_query($conector, $sql);
        $num_rows_pasto = mysqli_num_rows($tbl_pasto);

        if ($num_rows_pasto!=0) {
            $reg_pasto = mysqli_fetch_object($tbl_pasto);
            $id_pasto = $reg_pasto->tbl_pasto_id;

            if ($pasto_filtro!='') {
                $matriz_itens = explode(",", $pasto_filtro);
                $quantidade_itens = count($matriz_itens);

                for($i=0; $i < $quantidade_itens; $i++) {
                    if ($id_pasto==$matriz_itens[$i]) {
                        $descricao_pasto_atual = $reg_pasto->tbl_pasto_descricao;
                        $descricao_lote_atual = $reg_pasto->tbl_pasto_descricao_lote;
                    }
                }
            }
            else {
                $descricao_pasto_atual = $reg_pasto->tbl_pasto_descricao;
                $descricao_lote_atual = $reg_pasto->tbl_pasto_descricao_lote;
            }
        }
        else {
            $lote_id = $numero_lote.$ano_lote;

            $sql = "SELECT * from tbl_nutricao
                INNER JOIN tbl_pasto
                        ON tbl_nutricao_codigo_pasto = tbl_pasto_id  

                WHERE tbl_nutricao_lixeira=0 AND 
                      tbl_nutricao_codigo_local='$local_filtro' AND 
                      tbl_nutricao_id_lote='$lote_id'
                ORDER BY tbl_nutricao_id DESC LIMIT 1"; 

            $tbl_nutricao = mysqli_query($conector, $sql);

            $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

            if ($num_rows_nutricao!=0) {
                $reg_nutricao = mysqli_fetch_object($tbl_nutricao);
                $descricao_pasto_atual = $reg_nutricao->tbl_pasto_descricao;
                $descricao_lote_atual = strstr($reg_nutricao->tbl_nutricao_lote_pasto, " L-", true);
            }
            else {
                $descricao_pasto_atual = 'Não Encontrado';
                $descricao_lote_atual = 'Não Encontrado';
            }
        }

        return [$descricao_pasto_atual,$descricao_lote_atual];
    }

function verificar_dias_anteriores($conector, $local_filtro, $lote_anterior, $data_ref) {

    $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
        WHERE tbl_nutricao_lixeira = 0 AND 
              tbl_nutricao_codigo_local = '$local_filtro' AND 
              tbl_nutricao_id_lote = '$lote_anterior' AND 
              tbl_nutricao_data < '$data_ref'
        ORDER BY tbl_nutricao_data DESC LIMIT 1"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows==0) {
        $tem_dia_anterior = 'N';
    }
    else {
        $tem_dia_anterior = 'S';
    }

    return $tem_dia_anterior;
}

function calcular_dias($conector, $local_filtro, $id_lote, $data_inicial, $data_final, $tipo_periodo_lote){
    $data_hoje = date("Y-m-d");

    // Se não teve nutricao no dia atual, então subtrai 1 dia na data final conforme o trello Cartão: RELATORIO NUTRICAO - Cheklist: Relatorio Consumo de Nutrição Tela e Excel : Se houver nutrição para o dia atual então o dia e o valor entra na media final, caso contrario a media só será calculada com os dias até o anterior ao dia atual 

    // verifica se tem nutricao para o dia atual
    /*$sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
        WHERE tbl_nutricao_lixeira = 0 AND 
              tbl_nutricao_codigo_local = '$local_filtro' AND 
              tbl_nutricao_id_lote = '$id_lote' AND 
              tbl_nutricao_data = '$data_hoje'"); 
    $num_rows = mysqli_num_rows($sql);

    if ($num_rows==0) { 
        $data = new DateTime($data_final);
        $data->modify('-1 day');
        $data_final = $data->format('Y-m-d');
    }*/

    $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
        WHERE tbl_nutricao_lixeira = 0 AND 
              tbl_nutricao_codigo_local = '$local_filtro' AND 
              tbl_nutricao_id_lote = '$id_lote' 
        ORDER BY tbl_nutricao_data ASC"); 

    $num_rows = mysqli_num_rows($sql);
    $quantidade_dias = 1;
    $data_calculo_inicial = 0;
    $data_calculo_final = $data_final;

    if ($num_rows>0) {
        while ($reg = mysqli_fetch_object($sql)) {
            $data_nutricao = $reg->tbl_nutricao_data;
            $data_encerramento = $reg->tbl_nutricao_data_encerramento;
            $dias_de_consumo = $reg->tbl_nutricao_dias_consumo;

            if ($tipo_periodo_lote=='L') {
                if ($dias_de_consumo==0 || $dias_de_consumo=='') {
                    $data_calculo_inicial = $data_nutricao;
                }
            }

            if ($data_nutricao<$data_inicial) {
                $data_calculo_inicial = $data_inicial;
            }
            else if ($data_nutricao>=$data_inicial && 
                $data_calculo_inicial==0){
                $data_calculo_inicial = $data_nutricao;
            }

            if ($data_nutricao>$data_final) {
                //print_r('passei aqui');
                $data_calculo_final = $data_final;
            }

            if ($data_encerramento !='' && $data_encerramento>=$data_inicial && 
                $data_encerramento<=$data_final) {
                $data_calculo_final = $data_encerramento;
            }

            $data_inicial_str = new DateTime($data_calculo_inicial);
            $data_final_str = new DateTime($data_calculo_final);
            $intervalo = $data_inicial_str->diff($data_final_str);
            $quantidade_dias = $intervalo->days + 1;
        }
    }

    return [$quantidade_dias];
}

function monta_produto($conector, $local_filtro, $id_lote, $data_inicial, $data_final){
    $tbl_nutricao = mysqli_query($conector, "SELECT * from tbl_nutricao
        INNER JOIN tbl_produto
                ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 
            WHERE tbl_nutricao_lixeira=0 AND 
                  tbl_nutricao_codigo_local='$local_filtro' AND
                  tbl_nutricao_id_lote = '$id_lote' 
        ORDER BY tbl_produto_descricao ASC"); 

    $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);
    $descricao_produto_anterior = '';
    $descricao_produto= '';

    if ($num_rows_nutricao>0) {
        while ($reg = mysqli_fetch_object($tbl_nutricao)) {
            if (utf8_encode($reg->tbl_produto_descricao)!=$descricao_produto_anterior) {
                $descricao_produto_anterior=utf8_encode($reg->tbl_produto_descricao);
                $descricao_produto.= utf8_encode($reg->tbl_produto_descricao).'/';
            }
        }
    }

    return $descricao_produto = substr($descricao_produto, 0, -1);
}

function calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto ){
    $dias = 0;
    $consumo = 0;
    $codigo_score = 0;

    $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
        WHERE tbl_nutricao_lixeira = 0 AND 
              tbl_nutricao_codigo_local = '$codigo_local' AND 
              tbl_nutricao_id_lote = '$id_lote' AND 
              tbl_nutricao_data > '$data_nutricao' 
        ORDER BY tbl_nutricao_data ASC"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows>0) {
        $reg = mysqli_fetch_object($sql);
        $data_posterior = $reg->tbl_nutricao_data;
        $codigo_score = $reg->tbl_nutricao_codigo_score_cocho; 
        $firstDate  = new DateTime($data_nutricao);
        $secondDate = new DateTime($data_posterior);
        $intvl = $firstDate->diff($secondDate);
        $dias = $intvl->days;

        if ($dias==0) {
            $dias = 1;
        }

        $consumo = ($qtd_produto/$qtd_animais/$dias)*1000;

        /*$sql = ("UPDATE tbl_nutricao SET
            tbl_nutricao_dias_consumo='$dias',
            tbl_nutricao_consumo_cabeca_dia='$consumo'
            WHERE tbl_nutricao_id='$codigo_nutricao_id'");

        $resultado = mysqli_query($conector,$sql);*/

        //$consumo = number_format($consumo, 0, ",", ".");
        //$consumo = intval($consumo);
    } 

    return [$dias, $consumo, $codigo_score];
}

?>