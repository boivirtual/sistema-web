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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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


@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$cobertura_id = $_REQUEST["cobetura_id"];

$sql = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
    WHERE tbl_cobertura_lixeira=0 AND
          tbl_cobertura_id = $cobertura_id");

$reg_cobertura = mysqli_fetch_object($sql);

$local_cobertura = $reg_cobertura->tbl_cobertura_codigo_local;
$protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
$estacao_monta = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
$grupo_cobertura = $reg_cobertura->tbl_cobertura_codigo_grupo;
$encerrada = $reg_cobertura->tbl_cobertura_encerrada;
$descricao_filtro = utf8_encode($reg_cobertura->tbl_cobertura_filtros);
$qtd_animais = $reg_cobertura->tbl_cobertura_qtd_animais;

$sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
    WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

$reg_protocolo_cobertura = mysqli_fetch_object($sql);

$sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
    WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
          tbl_protocoloiatf_lixeira = 0");

$reg_protocolo_iatf = mysqli_fetch_object($sql);

$dias_diagnostico = $reg_protocolo_iatf->tbl_protocoloiatf_dias_diagnostico;
$descricao_iatf = utf8_encode($reg_protocolo_iatf->tbl_protocoloiatf_descricao);

$sql = mysqli_query($conector,"SELECT * FROM tbl_grupo_estacao_monta 
    WHERE tbl_grupo_id='$grupo_cobertura' AND 
          tbl_grupo_codigo_estacao_monta='$estacao_monta' AND 
          tbl_grupo_codigo_local='$local_cobertura'");

$reg_grupo = mysqli_fetch_object($sql);
$descricao_grupo = $grupo_cobertura .' - '. 
                   utf8_encode($reg_grupo->tbl_grupo_descricao) .' - '.
                   $qtd_animais .' fêmea(s) ' ;

$nome_relatorio = "Relatório de Cobertura IATF";

$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);

// paper size
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

$spreadsheet->getActiveSheet()->getPageMargins()->setTop(1); $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.1); $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.1); $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(1);


$spreadsheet->getActiveSheet()->mergeCells('A1:J1');
$spreadsheet->getActiveSheet()->mergeCells('K1:M1');
$spreadsheet->getActiveSheet()->mergeCells('A2:M2');
$spreadsheet->getActiveSheet()->mergeCells('B3:M3');
$spreadsheet->getActiveSheet()->mergeCells('A4:M4');

$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue('A1', $nome_relatorio)
		->setCellValue("K1", "Data: " . $data_sistema)
        ->setCellValue("A2", $descricao_grupo)
		->setCellValue("A3", "Filtro: ")
		->setCellValue("B3", $descricao_filtro)
        ->setCellValue("A4", $descricao_iatf);

$spreadsheet->getActiveSheet()->getStyle('B3')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B3')->getFont()->setSize(10);
$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setWrapText(true);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP;
$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setVertical($align);

$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("B5","Data")
        ->setCellValue("C5","Item 1")
        ->setCellValue("D5","Item 2")
        ->setCellValue("E5","Item 3")
        ->setCellValue("F5","Item 4");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(3);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(3);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(3);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(3);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(4);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(4);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('K1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('D5:F5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


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

$spreadsheet->getActiveSheet()->getStyle('A4')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4')->getFill()->getStartColor()->setARGB('DCDCDC');
$spreadsheet->getActiveSheet()->getStyle('A5:M5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A5:M5')->getFill()->getStartColor()->setARGB('C0C0C0');
/*$spreadsheet->getActiveSheet()->getStyle('A5:N5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A5:N5')->getFill()->getStartColor()->setARGB('C0C0C0');
*/
$spreadsheet->getActiveSheet()->getStyle('A4:M4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5:M5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->mergeCells('F5:M5');

$linha=5;

$idCobF = "";

$sql = "SELECT * FROM tbl_item_cobertura 
    WHERE tbl_ite_cobertura_numero_id = '$cobertura_id' 
    ORDER BY tbl_ite_cobertura_codigo_numerico ASC";
    //ORDER BY tbl_ite_cobertura_codigo_animal ASC";

$r = mysqli_query($conector, $sql);

$numReg = mysqli_num_rows($r);

$aDias = [
    0 => 0,
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0,
    5 => 0
];

$diagnostico_realizado=0;

while($reg = mysqli_fetch_object($r)){

    if ($diagnostico_realizado==0) {
        $diagnostico_realizado = $reg->tbl_ite_cobertura_data_diagnostico;
    }

    if ($reg->tbl_ite_cobertura_dia_1 == 'S'){
        $aDias[0]++;
    }

    if ($reg->tbl_ite_cobertura_dia_2 == 'S'){
        $aDias[1]++;
    }

    if ($reg->tbl_ite_cobertura_dia_3 == 'S'){
        $aDias[2]++;
    }

    if ($reg->tbl_ite_cobertura_dia_4 == 'S'){
        $aDias[3]++;
    }

    if ($reg->tbl_ite_cobertura_dia_5 == 'S'){
        $aDias[4]++;
    }

    if ($reg->tbl_ite_cobertura_dia_6 == 'S'){
        $aDias[5]++; 
    }
    $idCobF = $reg->tbl_ite_cobertura_numero_id;
}

$sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
    WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
          tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
    ORDER BY tbl_ite_protocoloiatf_id ASC");

$iDias = 0;
$iCheck = true;
$data_protocolo = [];
$index_data_protocolo = 0;

while($reg_itens = mysqli_fetch_object($sql)){
    $descricao = $reg_itens->tbl_ite_protocoloiatf_descricao;

    $linha++;

    $celulas = 'A'.$linha.':B'.$linha;
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

    if($aDias[$iDias] == $numReg && $iCheck){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);
    }
    elseif($aDias[$iDias] < $numReg && $iCheck){
        $iCheck = false;
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);
    }else{
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);
    }

    $iDias++;
    $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
    $prod1 = '';
    $prod2 = '';
    $prod3 = '';
    $prod4 = '';

    if($reg_itens->tbl_ite_protocoloiatf_qtde_1 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_1 != ''){
        $prod1 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_1}";
    }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_1 != ''){
        $prod1 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_1} {$reg_itens->tbl_ite_protocoloiatf_qtde_1}{$reg_itens->tbl_ite_protocoloiatf_unidade_1}";
    }

    if($reg_itens->tbl_ite_protocoloiatf_qtde_2 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_2 != ''){
            $prod2 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_2}";
    }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_2 != ''){
        $prod2 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_2} {$reg_itens->tbl_ite_protocoloiatf_qtde_2}{$reg_itens->tbl_ite_protocoloiatf_unidade_2}";
    }

    if($reg_itens->tbl_ite_protocoloiatf_qtde_3 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_3 != ''){
        $prod3 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_3}";
    }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_3 != ''){
        $prod3 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_3} {$reg_itens->tbl_ite_protocoloiatf_qtde_3}{$reg_itens->tbl_ite_protocoloiatf_unidade_3}";
    }

    if($reg_itens->tbl_ite_protocoloiatf_qtde_4 == 0.000 && $reg_itens->tbl_ite_protocoloiatf_medicamento_4 != ''){
            $prod4 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_4}";
    }elseif($reg_itens->tbl_ite_protocoloiatf_medicamento_4 != ''){
        $prod4 = "{$reg_itens->tbl_ite_protocoloiatf_medicamento_4} {$reg_itens->tbl_ite_protocoloiatf_qtde_4}{$reg_itens->tbl_ite_protocoloiatf_unidade_4}";
    }

    $data = date("d/m/Y", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

    $data_inseminacao = date("Y-m-d", strtotime(str_replace('/', '-', $data)));
    $data_diagnostico = date("d/m/Y", strtotime($data_inseminacao . "+{$dias_diagnostico} days"));

    $index_data_protocolo++;
    $data_protocolo[$index_data_protocolo]=date("Y-m-d", strtotime(str_replace('/', '-', $data)));

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->mergeCells('F'.$linha.':M'.$linha);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha.':M'.$linha)->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $data);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $prod1);
    $celulas = 'C'.$linha.':E'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

    if($prod2 != ''){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $prod2);
    }

    if($prod3 != ''){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $prod3);
    }

    if($prod4 != ''){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $prod4);
    }
}

$linha++;
$celulas = 'A'.$linha.':M'.$linha;
$spreadsheet->getActiveSheet()->mergeCells($celulas);

$linha++;

$celulas = 'A'.$linha.':C'.$linha;
$spreadsheet->getActiveSheet()->mergeCells($celulas);
//$celulas = 'A'.$linha;
//$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, 'Diagnóstivo Previsto: ' . $data_diagnostico);
//$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $data_diagnostico);

$celulas = 'D'.$linha.':M'.$linha;
$spreadsheet->getActiveSheet()->mergeCells($celulas);
//$celulas = 'D'.$linha;
//$spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

//$celulas = 'F'.$linha.':M'.$linha;
//$spreadsheet->getActiveSheet()->mergeCells($celulas);

if ($diagnostico_realizado==0 || $diagnostico_realizado=='0000-00-00' || $diagnostico_realizado=='') {
    $diagnostico_realizado_edi='';
}
else {
    $diagnostico_realizado_edi=date("d/m/Y", strtotime(str_replace('-', '/', $diagnostico_realizado)));
}

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, 'Diagnóstivo Realizado: ' . $diagnostico_realizado_edi);

//$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $diagnostico_realizado_edi);

//$spreadsheet->getActiveSheet()->getRowDimension($linha)->setRowHeight(30);

//$celulas = 'A'.$linha.':F'.$linha;
//$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
//$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
//$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
//$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

//$celulas = 'G'.$linha.':O'.$linha;
//$spreadsheet->getActiveSheet()->mergeCells($celulas);

$celulas = 'A'.$linha.':M'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('DCDCDC');
$spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G'.$linha.':M'.$linha)->applyFromArray($styleArray);

$linha_cab1 = $linha + 1;
$linha_cab2 = $linha + 2;

$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A".$linha_cab1,"N° Fêmea")
        ->setCellValue("B".$linha_cab1,"Raça")
        ->setCellValue("C".$linha_cab1,"Semên/Touro")
        ->setCellValue("J".$linha_cab1,"Inseminador")
        ->setCellValue("K".$linha_cab1,"Resultado")
        ->setCellValue("M".$linha_cab1,"Nº Coberturas");

$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("C".$linha_cab2,"Identificação")
        ->setCellValue("D".$linha_cab2,"Raça")
        ->setCellValue("E".$linha_cab2,"Lote")
        ->setCellValue("J".$linha_cab2,"1")
        ->setCellValue("K".$linha_cab2,"Pos")
        ->setCellValue("L".$linha_cab2,"Neg");

$celulas = 'A'.$linha_cab1.':M'.$linha_cab2;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);

$spreadsheet->getActiveSheet()->getStyle('D'.$linha_cab1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('D'.$linha_cab2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('E'.$linha_cab2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('F'.$linha_cab2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('K'.$linha_cab1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('K'.$linha_cab2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('L'.$linha_cab2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('M'.$linha_cab1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->mergeCells('C'.$linha_cab1.':E'.$linha_cab1);
$spreadsheet->getActiveSheet()->mergeCells('K'.$linha_cab1.':L'.$linha_cab1);
//$spreadsheet->getActiveSheet()->mergeCells('M'.$linha_cab1.':N'.$linha_cab1);

$spreadsheet->getActiveSheet()->mergeCells('A'.$linha_cab1.':A'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('B'.$linha_cab1.':B'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('F'.$linha_cab1.':F'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('G'.$linha_cab1.':G'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('H'.$linha_cab1.':H'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('I'.$linha_cab1.':I'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('J'.$linha_cab1.':J'.$linha_cab2);
$spreadsheet->getActiveSheet()->mergeCells('M'.$linha_cab1.':M'.$linha_cab2);

$celulas = 'A'.$linha_cab1.':A'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'B'.$linha_cab1.':B'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'C'.$linha_cab1.':C'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'F'.$linha_cab1.':F'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'G'.$linha_cab1.':G'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'H'.$linha_cab1.':H'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'I'.$linha_cab1.':I'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'J'.$linha_cab1.':J'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$spreadsheet->getActiveSheet()->getStyle('J'.$linha_cab1)->getAlignment()->setWrapText(true);

$celulas = 'M'.$linha_cab1.':M'.$linha_cab2;
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

$celulas = 'A'.$linha_cab1.':M'.$linha_cab2;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('C0C0C0');

$spreadsheet->getActiveSheet()->getStyle('A'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('H'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('I'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('J'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('L'.$linha_cab1)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('M'.$linha_cab1)->applyFromArray($styleArray);

$spreadsheet->getActiveSheet()->getStyle('A'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('H'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('I'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('J'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('K'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('L'.$linha_cab2)->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('M'.$linha_cab2)->applyFromArray($styleArray);

$linha++;

$sql = "SELECT * FROM tbl_item_protocoloiatf 
    WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
          tbl_ite_protocoloiatf_protocolo_id = $protocolo_id 
    ORDER BY tbl_ite_protocoloiatf_id ASC";
$rs = mysqli_query($conector, $sql);

$count_dias = 0;
$coluna = 'F';

while($reg_itens = mysqli_fetch_object($rs)){
    $dias = trim(substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3));
    $count_dias++;
    $celula = $coluna.$linha;
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue($celula,"D".$dias);

    if ($coluna=='F') {
        $coluna='G';
    }
    else if ($coluna=='G') {
        $coluna='H';
    }
    else if ($coluna=='H') {
        $coluna='I';
    }
}

$linha++;

$sql = "SELECT * FROM tbl_item_cobertura 
    WHERE tbl_ite_cobertura_numero_id = '$cobertura_id' 
    ORDER BY tbl_ite_cobertura_codigo_numerico ASC";
    //ORDER BY tbl_ite_cobertura_codigo_animal ASC";
$rs = mysqli_query($conector, $sql);

while($reg_itensCobertura = mysqli_fetch_object($rs)){
    $linha++;

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->applyFromArray($styleArray);

    $ordem = $reg_itensCobertura->tbl_ite_cobertura_numero_item;
    $animal_id = $reg_itensCobertura->tbl_ite_cobertura_codigo_id_animal;
    $touro_semem = $reg_itensCobertura->tbl_ite_cobertura_codigo_touro_semen;
    $lote_semem = $reg_itensCobertura->tbl_ite_cobertura_lote_semen;
    $resultado = $reg_itensCobertura->tbl_ite_cobertura_resultado_diagnostico;
    $inseminador = utf8_encode($reg_itensCobertura->tbl_ite_cobertura_nome_inseminador);
    $destino = $reg_itensCobertura->tbl_ite_cobertura_destino;

    $codigo_alfa = $reg_itensCobertura->tbl_ite_cobertura_codigo_alfa;
    $codigo_numerico = $reg_itensCobertura->tbl_ite_cobertura_codigo_numerico;

    if ($codigo_alfa=='') {
        $matriz = intval($codigo_numerico);
    }
    else {
        $matriz = $codigo_alfa.'-'.intval($codigo_numerico);
    }

    //$matriz = $reg_itensCobertura->tbl_ite_cobertura_codigo_animal;

    $sql = "SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_id = '$animal_id'";

    $res = mysqli_query($conector, $sql);
    $reg_animal = mysqli_fetch_object($res);


    $raca_id = $reg_animal->tbl_animal_codigo_raca;
    $pai_id = $reg_animal->tbl_animal_codigo_pai;

    // VERIFICA O NUMERO DE COBERTURAS NA ESTACAO

    $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
        inner join tbl_item_cobertura 
                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
        where tbl_cobertura_lixeira=0 and
              tbl_cobertura_codigo_local = '$local_cobertura' and 
              tbl_cobertura_codigo_estacao_monta = '$estacao_monta' and 
              tbl_ite_cobertura_codigo_id_animal='$animal_id' and 
              tbl_ite_cobertura_dia_1='S'");

    $numero_coberturas = mysqli_num_rows($tbl_cobertura);

    $sql = "SELECT * FROM tabela_racas 
        WHERE tab_codigo_raca = '$raca_id' AND 
              tab_registro_lixeira_raca = 0";

    $res = mysqli_query($conector, $sql);
    $reg_raca = mysqli_fetch_object($res);
    $raca = utf8_encode($reg_raca->tab_descricao_raca);

    $celulas = 'A'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $celulas = 'B'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $celulas = 'E'.$linha.':J'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $matriz);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $raca);

    /*$semem = mysqli_query($conector, "select * from tbl_semem 
        where tbl_semem_lixeira=0 and 
              tbl_semem_ativo='S' and  
              tbl_semem_codigo_id=' $pai_id'"); 

    $num_row = mysqli_num_rows($semem);

    if ($num_row!=0) {
        $reg = mysqli_fetch_object($semem);

        if ($reg->tbl_semem_nome == "") {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg->tbl_semem_nome);
        }
        else {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg->tbl_semem_nome.'-'.$reg->tbl_semem_nome);
        }
    }
    else {
        $touro = mysqli_query($conector, "select * from tbl_animais 
            where tbl_animal_lixeira=0 and 
                  tbl_animal_sexo='M' and 
                  tbl_animal_reprodutor='S' and
                  tbl_animal_ativo = 'S' and
                  tbl_animal_codigo_id = ' $pai_id'"); 
        $num_row = mysqli_num_rows($touro);

        if ($num_row!=0) {
            $reg = mysqli_fetch_object($touro);

            if ($reg->tbl_animal_nome == "") {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg->tbl_animal_codigo_alfa.' '.$reg->tbl_animal_codigo_numerico);
            }
            else {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg->tbl_animal_codigo_alfa.' '.$reg->tbl_animal_codigo_numerico.'-'.$reg->tbl_animal_nome);
            }
        }
    }
    */
    $raca_touro_semen_id = '';

    $semem = mysqli_query($conector, "select * from tbl_semem 
        where tbl_semem_lixeira=0 and 
              tbl_semem_ativo='S' and  
              tbl_semem_codigo_id='$touro_semem'"); 

    $num_row = mysqli_num_rows($semem);

    if ($num_row!=0) {
        $reg = mysqli_fetch_object($semem);
        $raca_touro_semen_id = $reg->tbl_semem_codigo_raca;        

        if ($reg->tbl_semem_nome == "") {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg->tbl_semem_nome);
        }
        else {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($reg->tbl_semem_nome));
        }
    }
    else {
        $touro = mysqli_query($conector, "select * from tbl_animais 
            where tbl_animal_lixeira=0 and 
                  tbl_animal_sexo='M' and 
                  tbl_animal_reprodutor='S' and
                  tbl_animal_ativo = 'S' and
                  tbl_animal_codigo_id = '$touro_semem'"); 
        $num_row = mysqli_num_rows($touro);

        if ($num_row!=0) {
            $reg = mysqli_fetch_object($touro);
            $raca_touro_semen_id = $reg->tbl_animal_codigo_raca;

            if ($reg->tbl_animal_nome == "") {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg->tbl_animal_codigo_alfa.' '.$reg->tbl_animal_codigo_numerico);
            }
            else {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, utf8_encode($reg->tbl_animal_nome));
            }
        }
    }

    if ($raca_touro_semen_id!='') {
        $sql = mysqli_query($conector, "SELECT * FROM tabela_racas 
            WHERE tab_codigo_raca = '$raca_touro_semen_id' AND 
                  tab_registro_lixeira_raca = 0");

        $reg_raca = mysqli_fetch_object($sql);
        $desc_raca_touro_semen = utf8_encode($reg_raca->tab_descricao_raca);
    }  
    else {
        $desc_raca_touro_semen = '';
    }               

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_raca_touro_semen);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $lote_semem);

    $celulas = 'F'.$linha.':I'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
    
    if (1 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_1 == 'S'){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, 'X');
    }

    if (2 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_2 == 'S'){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, 'X');
    }

    if (3 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_3 == 'S'){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, 'X');
    }

    if (4 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_4 == 'S'){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, 'X');
    }

    /*if (5 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_5 == 'S'){
    }

    if (6 <= $count_dias && $reg_itensCobertura->tbl_ite_cobertura_dia_6 == 'S'){
    }*/

    $celulas = 'J'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $inseminador);

    $celulas = 'K'.$linha.':L'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    if($resultado == 'N'){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $resultado);
    }
    elseif($resultado == 'P'){
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $resultado);
    }

    $celulas = 'M'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $numero_coberturas);
}
    
// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="cobertura_iatf.xlsx"');
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