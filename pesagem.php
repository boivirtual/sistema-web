<?php
$data_sistema = date("d/m/Y");
$data_impressao = date("Y-m-d H:i:s");

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$senha_bd = "a2ngei9Mxh";
$banco = $cnpj_cliente;

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

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->getActiveSheet()->mergeCells('A2:G2');
$spreadsheet->getActiveSheet()->mergeCells('B3:H3');
$spreadsheet->getActiveSheet()->mergeCells('B4:H4');
$spreadsheet->getActiveSheet()->mergeCells('B5:H5');

$nome_relatorio='Pesagem Individual';
$desc_local='FAZENDA SANTA HELENA';
$desc_epoca='Controle Ganho de Peso';
$desc_filtro='FAZENDA SANTA HELENA->Controle Ganho de Peso->Sexo:Todos';

// PEGARNO ESSE NUMERO NO PARAMETRO
$numero_pesagem=239;
//---------------------------------

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('A2', 'Local: ' . $desc_local)
    ->setCellValue('H2', 'Motivo Pesagem: ' . $desc_epoca)
    ->setCellValue('H1', 'Data: ' . $data_sistema);
    
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A3","Nº Documento:")
    ->setCellValue("B3", '' . $numero_pesagem)
    ->setCellValue("A4","Lote:")
    ->setCellValue("A5","Filtros:")
    ->setCellValue("A6","Id Animal")
    ->setCellValue("B6","Peso")
    ->setCellValue("C6","Sexo")
    ->setCellValue("D6","Nascimento")
    ->setCellValue("E6","Raça")
    ->setCellValue("F6","Pelagem")
    ->setCellValue("G6","Mãe")
    ->setCellValue("H6","Observação");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(35);

$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A3:A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getActiveSheet()->getStyle('H1:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A6:H6') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('B4:H4')->getFont()->setSize(10);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 5, $desc_filtro);
//$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $numero_pesagem);

$linha=6;

$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'");

$num_rows = mysqli_num_rows($tbl_item);

if ($num_rows!=0) {
    while ($reg_item = mysqli_fetch_object($tbl_item)) {
        $linha++;
        $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $reg_item->tbl_ite_pesagem_codigo_animal);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg_item->tbl_ite_pesagem_sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $reg_item->tbl_ite_pesagem_nascimento);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $reg_item->tbl_ite_pesagem_raca);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $reg_item->tbl_ite_pesagem_pelagem);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $reg_item->tbl_ite_pesagem_mae);
    }
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

$nome_arquivo = "pesagem_individual_" . $numero_pesagem . ".xlsx";
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nome_arquivo.'"');
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

//mysqli_close($conector);
//exit;


?>
              
                
