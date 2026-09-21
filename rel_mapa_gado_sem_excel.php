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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];
$controle_estoque = $_SESSION['controle_estoque'];

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

$local = $_REQUEST["local"];
$descricao_filtro= $_REQUEST["descricao_filtro"];

@ session_start(); 

$tbl_pessoa = mysqli_query($conector, "select * from tbl_pessoa 
        where tbl_pessoa_id ='$local' and tbl_pessoa_lixeira=0"); 
$reg_local = mysqli_fetch_object($tbl_pessoa);
$desc_local = utf8_encode($reg_local->tbl_pessoa_nome);

$tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
    WHERE tbl_animal_pasto_local='$local' AND 
          tbl_animal_pasto_situacao='A'");

$total_local = mysqli_num_rows($tbl_animal_pasto);

$nome_relatorio = "Mapa de Gado";

$spreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setPaperSize(PageSetup::PAPERSIZE_A4);

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');
$spreadsheet->getActiveSheet()->mergeCells('B2:D2');
$spreadsheet->getActiveSheet()->mergeCells('B3:D3');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
	->setCellValue("D1", "Data: " . $data_sistema)
	->setCellValue("A2", "Filtro ")
	->setCellValue("B2", $descricao_filtro)
    ->setCellValue("A3", "Total de animais na fazenda")
    ->setCellValue("B3", $total_local);

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));

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

$spreadsheet->getActiveSheet()->getStyle('A4:D4')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4:D4')->getFill()->getStartColor()->setARGB('DCDCDC');

$spreadsheet->getActiveSheet()->getStyle('A1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('D1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3:D3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4:D4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A4","Pasto")
    ->setCellValue("B4","Dias sem Animais")
    ->setCellValue("C4","Área do Pasto (ha)")
    ->setCellValue("D4","Descrição Lote");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(27);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(46);


$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setVertical($align);


$linha=4;
$ultima_data = '0000-00-00';

$tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
    WHERE tbl_pasto_codigo_local='$local' AND 
          tbl_pasto_modulo=999");

$num_rows = mysqli_num_rows($tbl_pasto);

if ($num_rows!=0) {
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $descricao = utf8_encode($reg_pasto->tbl_pasto_descricao);
        $codigo_pasto = $reg_pasto->tbl_pasto_id;
        $descricao_lote = utf8_encode($reg_pasto->tbl_pasto_descricao_lote);
        $area = $reg_pasto->tbl_pasto_area;

        if ($area==0) {
            $area='';
        }

        // Pega dias sem animais no pasto
        $dias_pasto = 0;

        $dataAtual = new DateTime();
        $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_sem_animais_anterior);
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_sem_animais);

        if ($dataCom!='') {
            $diff = $dataAtual->diff($dataCom);
            $dias_pasto = $diff->days;
        }

        // Fim pega dias sem animais no pasto

        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                      tbl_animal_pasto_situacao='A'");

        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);

        if ($num_rows_animal==0) {
                $linha++;

            $celulas = 'A'.$linha;
            $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);

            $celulas = 'B'.$linha.':C'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $celulas = 'D'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $dias_pasto);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $area);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $descricao_lote);
        }
        else {
            while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {

                $inclusao = $reg_animal_pasto->tbl_animal_pasto_incluido_em;
                $alteracao = $reg_animal_pasto->tbl_animal_pasto_alterado_em;

                if ($inclusao!='') {
                    if ($inclusao>$ultima_data){
                        $ultima_data=$inclusao;
                    }
                }

                if ($alteracao!='') {
                    if ($alteracao>$ultima_data){
                        $ultima_data=$alteracao;
                    }
                }
            }
        }
    }
}

$tbl_pasto= mysqli_query($conector, "SELECT * FROM tbl_pasto
    WHERE tbl_pasto_codigo_local='$local' AND 
          tbl_pasto_modulo!=999");

$num_rows = mysqli_num_rows($tbl_pasto);

if ($num_rows!=0) {
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $descricao = utf8_encode($reg_pasto->tbl_pasto_descricao);
        $codigo_pasto = $reg_pasto->tbl_pasto_id;
        $descricao_lote = utf8_encode($reg_pasto->tbl_pasto_descricao_lote);
        $area = $reg_pasto->tbl_pasto_area;

        if ($area==0) {
            $area='';
        }

        // Pega dias sem animais no pasto
        $dias_pasto = 0;

        $dataAtual = new DateTime();
        $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_sem_animais_anterior);
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_sem_animais);

        if ($dataCom!='') {
            $diff = $dataAtual->diff($dataCom);
            $dias_pasto = $diff->days;
        }

        // Fim pega sem com animais no pasto

        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                      tbl_animal_pasto_situacao='A'");

        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);

        if ($num_rows_animal==0) {
                $linha++;

            $celulas = 'A'.$linha;
            $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);

            $celulas = 'B'.$linha.':C'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $celulas = 'D'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);
            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $dias_pasto);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $area);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $descricao_lote);
        }
        else {
            while ($reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto)) {

                $inclusao = $reg_animal_pasto->tbl_animal_pasto_incluido_em;
                $alteracao = $reg_animal_pasto->tbl_animal_pasto_alterado_em;

                if ($inclusao!='') {
                    if ($inclusao>$ultima_data){
                        $ultima_data=$inclusao;
                    }
                }

                if ($alteracao!='') {
                    if ($alteracao>$ultima_data){
                        $ultima_data=$alteracao;
                    }
                }
            }
        }
    }
}

$date = new DateTime( $ultima_data );

$linha++;
$celulas = 'A'.$linha.':C'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha,'Última Atualização: ' . $date->format('d-m-Y H:i'));

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="mapa_gado_sem_animais.xlsx"');
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