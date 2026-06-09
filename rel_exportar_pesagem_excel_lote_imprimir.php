<?php
$data_sistema = date("d/m/Y");
$data_impressao = date("Y-m-d H:i:s");

@session_start();
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$banco = $cnpj_cliente;
$senha_bd = "a2ngei9Mxh";

$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd);
   
if (mysqli_connect_error()) {
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Falha na conexão: ' . mysqli_connect_error()));
    exit;
}

$bancoselecionado = mysqli_select_db($conector,$banco);

if ($bancoselecionado === FALSE) {
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Falha na seleção do banco de dados: ' . mysqli_error($conector)));
    exit;
}

$desc_filtro = $_REQUEST["desc_filtro"];
$numero_pesagem = $_REQUEST["num_pesagem"];
$desc_local = utf8_decode($_REQUEST["desc_local"]);
$desc_epoca = utf8_decode($_REQUEST["desc_epoca"]);

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
$spreadsheet->getActiveSheet()->mergeCells('B2:F2');
$spreadsheet->getActiveSheet()->mergeCells('B3:F3');
$spreadsheet->getActiveSheet()->mergeCells('B4:H4');
$spreadsheet->getActiveSheet()->mergeCells('B5:H5');

$nome_relatorio = 'Pesagem Lote';

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('A2', 'Local:')
    ->setCellValue('B2', $desc_local)
    ->setCellValue('G2', 'Motivo Pesagem:')
    ->setCellValue('G3', 'Qtde Animais:')
    ->setCellValue('H2', $desc_epoca)
    ->setCellValue('H1', 'Data: ' . $data_sistema);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A3", "Nº Documento:")
    ->setCellValue("B3", '' . $numero_pesagem)
    ->setCellValue("A4", "Descrição Pesagem:")
    ->setCellValue("A5", "Filtros:")
    ->setCellValue("A6", "Item")
    ->setCellValue("B6", "Categoria")
    ->setCellValue("C6", "Sexo")
    ->setCellValue("D6", "Raça")
    ->setCellValue("E6", "Peso")
    ->setCellValue("F6", "Pasto")
    ->setCellValue("G6", "Grupo Destino")
    ->setCellValue("H6", "Observação");

/*$spreadsheet->getActiveSheet()->getComment('G6')->setAuthor('');
$commentRichText = $spreadsheet->getActiveSheet()->getComment('G6')->getText()->createTextRun('Pesagem:');
$commentRichText->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getComment('G6')->getText()->createTextRun("\r\n");
$spreadsheet->getActiveSheet()->getComment('G6')->getText()->createTextRun('Informe aqui um número para agrupar animais que posteriormente poderam ser transferidos para outros pastos.');*/

$spreadsheet->getActiveSheet()->getComment('G6')->setAuthor('');
$commentRichText = $spreadsheet->getActiveSheet()->getComment('G6')->getText()->createTextRun('Grupo Destino:');
$commentRichText->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getComment('G6')->getText()->createTextRun("\r\n");
$spreadsheet->getActiveSheet()->getComment('G6')->getText()->createTextRun('Informe aqui um número para agrupar animais que posteriormente poderam ser transferidos para outros pastos.');
$spreadsheet->getActiveSheet()->getComment('G6')->setWidth('150pt');
$spreadsheet->getActiveSheet()->getComment('G6')->setHeight('100pt');
$spreadsheet->getActiveSheet()->getComment('G6')->setMarginLeft('150pt');
$spreadsheet->getActiveSheet()->getComment('G6')->getFillColor()->setRGB('EEEEEE');


$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(23);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(17);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(35);

$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A2:A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('G2:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 5, $desc_filtro);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $numero_pesagem);

$spreadsheet->getActiveSheet()->getStyle('A6:H6')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A6:H6')->getFill()->getStartColor()->setARGB('BFBFBF');

$linha = 6;

$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'");

$num_rows = mysqli_num_rows($tbl_item);

if ($num_rows != 0) {
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 3, $num_rows);

    while ($reg_item = mysqli_fetch_object($tbl_item)) {
        $numero_item = intval($reg_item->tbl_ite_pesagem_numero_item);

        $codigo_categoria = $reg_item->tbl_ite_pesagem_categoria;
        $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_codigo_categoria_idade='$codigo_categoria'");
        $num_rows = mysqli_num_rows($tbl_categoria);
        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tbl_categoria);
            if ($reg->tab_categoria_idade_ate==999999999) {
                $desc_categoria ='> 36 meses';
            }
            else {
                $desc_categoria = $reg->tab_categoria_idade_de . ' a ' . 
                $reg->tab_categoria_idade_ate . ' meses';
            }
        }
        else {
            $desc_categoria = '';
        }

        $codigo_pasto = $reg_item->tbl_ite_pesagem_pasto;
        $tbl_pasto = mysqli_query($conector, "select * from tbl_pasto where tbl_pasto_id ='$codigo_pasto'");
        $num_rows = mysqli_num_rows($tbl_pasto);
        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tbl_pasto);
            $desc_pasto =utf8_encode($reg->tbl_pasto_descricao);
        }
        else {
            $desc_pasto = $codigo_pasto;
        }

        $desc_raca = utf8_decode($reg_item->tbl_ite_pesagem_raca);
        $sexo = utf8_decode($reg_item->tbl_ite_pesagem_sexo);

        $linha++;
        $spreadsheet->getActiveSheet()->getStyle('A' . $linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('C' . $linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getStyle('H' . $linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        /*$celulas = 'G'.$linha.':J'.$linha;
        $spreadsheet->getActiveSheet()->mergeCells($celulas);*/

        /*$data_nasc = $reg_item->tbl_ite_pesagem_nascimento;
        $data_nasc = str_replace("/", "-", $data_nasc);
        $data_nasc = date('Y-m-d', strtotime($data_nasc));
        $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc);                                             
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');*/

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $numero_item);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_categoria);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_raca);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_pasto);
    }
}
else {
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Pesagem nao encontrada '
        . $numero_pesagem . ' ' . $desc_filtro));
    exit;
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

$nome_arquivo = "pesagem_lote_" . $numero_pesagem . ".xlsx";
// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nome_arquivo . '"');
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
