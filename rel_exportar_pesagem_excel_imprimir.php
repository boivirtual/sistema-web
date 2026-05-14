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

//$dataObj = json_decode($_POST["dataObj"]);
//$desc_filtro = $dataObj->desc_filtro;
//$numero_pesagem = $dataObj->num_pesagem;

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

$spreadsheet->getActiveSheet()->mergeCells('A1:H1');
$spreadsheet->getActiveSheet()->mergeCells('B2:F2');
$spreadsheet->getActiveSheet()->mergeCells('B3:F3');
$spreadsheet->getActiveSheet()->mergeCells('B4:I4');
$spreadsheet->getActiveSheet()->mergeCells('A5:I5');

$nome_relatorio = 'Pesagem Individual';

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('A2', 'Local:')
    ->setCellValue('B2', $desc_local)
    ->setCellValue('G2', 'Motivo Pesagem:')
    ->setCellValue('G3', 'Qtde Animais:')
    ->setCellValue('H2', $desc_epoca)
    ->setCellValue('I1', 'Data: ' . $data_sistema);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A3", "Nº Documento:")
    ->setCellValue("B3", '' . $numero_pesagem)
    ->setCellValue("A4", "Descrição da Pesagem:")
    ->setCellValue("A6", "Id Animal")
    ->setCellValue("B6", "Peso")
    ->setCellValue("C6", "Ultimo peso")
    ->setCellValue("D6", "Sexo")
    ->setCellValue("E6", "Nascimento")
    ->setCellValue("F6", "Raça")
    ->setCellValue("G6", "Pelagem")
    ->setCellValue("H6", "Mãe")
    ->setCellValue("I6", "Observação")
    ->setCellValue("J6", "Descarte");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(21);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(17);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(35);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(9);

$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('G2:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A6:I6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('A5')->getFont()->setSize(10);
$spreadsheet->getActiveSheet()->getStyle('A5')->getFont()->setColor(new Color(Color::COLOR_GRAY));

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, $desc_filtro);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $numero_pesagem);

$linha = 6;

$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    INNER JOIN tbl_animais
            ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
    WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'");

$num_rows = mysqli_num_rows($tbl_item);

if ($num_rows != 0) {
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 3, $num_rows);

    while ($reg_item = mysqli_fetch_object($tbl_item)) {

        $descarte = $reg_item->tbl_animal_descarte_reproducao;

        if ($descarte=='S') {
            $animal_descarte='Sim';
        }
        else {
            $animal_descarte='';
        }

        $linha++;
        $spreadsheet->getActiveSheet()->getStyle('A' . $linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('C' . $linha .  ':E' . $linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('H' . $linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('J' . $linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

        $data_nasc = $reg_item->tbl_ite_pesagem_nascimento;
        $data_nasc = str_replace("/", "-", $data_nasc);
        $data_nasc = date('Y-m-d', strtotime($data_nasc));
        $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc);                                             
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $reg_item->tbl_ite_pesagem_codigo_animal);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $reg_item->tbl_ite_pesagem_ultimo_peso);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $reg_item->tbl_ite_pesagem_sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $nascimento_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, utf8_encode($reg_item->tbl_ite_pesagem_raca));
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, utf8_encode($reg_item->tbl_ite_pesagem_pelagem));
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $reg_item->tbl_ite_pesagem_mae);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $animal_descarte);
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

$nome_arquivo = "pesagem_individual_" . $numero_pesagem . ".xlsx";
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
