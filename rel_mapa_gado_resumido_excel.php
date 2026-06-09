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

$local = $_REQUEST["local"];
$descricao_filtro= $_REQUEST["descricao_filtro"];

@ session_start(); 

$tbl_pessoa = mysqli_query($conector, "select * from tbl_pessoa 
        where tbl_pessoa_id ='$local' and tbl_pessoa_lixeira=0"); 
$reg_local = mysqli_fetch_object($tbl_pessoa);
$desc_local = utf8_encode($reg_local->tbl_pessoa_nome);

$nome_relatorio = "Mapa de Gado";

/*$spreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getActiveSheet()
    ->getPageSetup()
    ->setPaperSize(PageSetup::PAPERSIZE_A4);
*/

$spreadsheet->getActiveSheet()->mergeCells('A1:E1');
$spreadsheet->getActiveSheet()->mergeCells('F1:G1');
$spreadsheet->getActiveSheet()->mergeCells('B2:G2');
$spreadsheet->getActiveSheet()->mergeCells('B3:G3');
$spreadsheet->getActiveSheet()->mergeCells('A4:A5');
$spreadsheet->getActiveSheet()->mergeCells('B4:B5');
$spreadsheet->getActiveSheet()->mergeCells('F4:G4');
$spreadsheet->getActiveSheet()->mergeCells('C4:C5');
$spreadsheet->getActiveSheet()->mergeCells('D4:D5');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
	->setCellValue("F1", "Data: " . $data_sistema)
	->setCellValue("A2", "Filtro ")
	->setCellValue("B2", $descricao_filtro);

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A2:G2')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A3')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('B3')->getFont()->setSize(12);
$spreadsheet->getActiveSheet()->getStyle('A4:G4')->getFont()->setSize(8);
$spreadsheet->getActiveSheet()->getStyle('A5:G5')->getFont()->setSize(8);

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

$styleHorizontalDotted = [
    'borders' => [
        'top' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
        ],
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
        ],
    ],
];

$spreadsheet->getActiveSheet()->getStyle('A3:G3')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A3:G3')->getFill()->getStartColor()->setARGB('DCDCDC');
$spreadsheet->getActiveSheet()->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4:G4')->getFill()->getStartColor()->setARGB('C0C0C0');
$spreadsheet->getActiveSheet()->getStyle('A5:G5')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A5:G5')->getFill()->getStartColor()->setARGB('C0C0C0');

$spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet()->getStyle('G5')->applyFromArray($styleArray);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A3","Total Animais")

    ->setCellValue("A4","Pasto")
    ->setCellValue("B4","Total")
    ->setCellValue("C4","Descrição Lote")
    ->setCellValue("D4","Dias de Permanência")
    ->setCellValue("E4","Bezerros")
    ->setCellValue("F4","Adultos")
    
    ->setCellValue("E5","Macho/Fêmea")
    ->setCellValue("F5","Macho")
    ->setCellValue("G5","Fêmea");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(8);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('D5:G5')->getAlignment()->setHorizontal($align);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('D5:G5')->getAlignment()->setVertical($align);

$spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('F1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('A3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$linha=5;

$total_animais = 0;
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
        // Pega dias com animais no pasto
        $dias_pasto = 0;

        $dataAtual = new DateTime();
        $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_com_animais_anterior);
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);

        if ($dataCom!='') {
            $diff = $dataAtual->diff($dataCom);
            $dias_pasto = $diff->days;
        }

        // Fim pega dias com animais no pasto

        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                  tbl_animal_pasto_situacao='A'
            ORDER BY tbl_animal_pasto_nascimento DESC");

        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);
        $total_animais_pasto = 0;
        $total_bezerros = 0;
        $total_machos = 0;
        $total_femeas = 0;

        if ($num_rows_animal!=0) {
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

                $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); 
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $total_animais++;
                $total_animais_pasto++;

                $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");
                $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }

                if ($codigo_categoria == 1 || $codigo_categoria == 2) {
                    $total_bezerros++;
                }
                else {
                    if ($sexo=='M') {
                        $total_machos++;
                    }
                    else {
                        $total_femeas++;
                    }
                }
            }

            if ($total_animais_pasto!=0) {
                $linha++;

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

                $celulas = 'A'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);

                $celulas = 'A'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $celulas = 'B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'D'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'A'.$linha.':G'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getFont()->setSize(12);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getFont()->setSize(10);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $total_animais_pasto);

                $celulas = 'C'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $descricao_lote);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $dias_pasto);

                if ($total_bezerros!=0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_bezerros);
                }

                if ($total_machos!=0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_machos);
                }

                if ($total_femeas!=0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_femeas);
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

        // Pega dias com animais no pasto
        $dias_pasto = 0;

        $dataAtual = new DateTime();
        $dataAnterior = new DateTime($reg_pasto->tbl_pasto_data_com_animais_anterior);
        $dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);

        if ($dataCom!='') {
            $diff = $dataAtual->diff($dataCom);
            $dias_pasto = $diff->days;
        }

        // Fim pega dias com animais no pasto

        $tbl_animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id='$codigo_pasto' AND 
                  tbl_animal_pasto_situacao='A'
            ORDER BY tbl_animal_pasto_nascimento DESC");

        $num_rows_animal = mysqli_num_rows($tbl_animal_pasto);

        $total_animais_pasto = 0;
        $total_bezerros = 0;
        $total_machos = 0;
        $total_femeas = 0;

        if ($num_rows_animal!=0) {
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

                $sexo = $reg_animal_pasto->tbl_animal_pasto_sexo;
                $data_nascimento = $reg_animal_pasto->tbl_animal_pasto_nascimento;
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $total_animais++;
                $total_animais_pasto++;

                $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");
                $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

                while ($reg_categoria = mysqli_fetch_object($tbl_categoria_pasto)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }

                if ($codigo_categoria == 1 || $codigo_categoria == 2) {
                    $total_bezerros++;
                }
                else {
                    if ($sexo=='M') {
                        $total_machos++;
                    }
                    else {
                        $total_femeas++;
                    }
                }
            }

            if ($total_animais_pasto!=0) {
                $linha++;

                $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->applyFromArray($styleArray);

                $celulas = 'A'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setSize(8);

                $celulas = 'A'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $celulas = 'B'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'D'.$linha.':G'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'A'.$linha.':G'.$linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->getFont()->setSize(12);
                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getFont()->setSize(10);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $descricao);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $total_animais_pasto);

                $celulas = 'C'.$linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $descricao_lote);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $dias_pasto);

                if ($total_bezerros!=0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $total_bezerros);
                }

                if ($total_machos!=0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $total_machos);
                }

                if ($total_femeas!=0) {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $total_femeas);
                }
            }
        }
    }

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $total_animais);
}

for ($row = 6; $row <= $linha; $row++) {
    $celulas = 'A'.$row.':G'.$row;

    $spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleHorizontalDotted);

    // Se a linha for par, aplica a cor
    if ($row % 2 != 0) {
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->setFillType(Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFill()->getStartColor()->setARGB('F2F2F2');
    }
}

$styleHorizontalDotted = [
    'borders' => [
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];

$celulas = 'A'.$linha.':G'.$linha;
$spreadsheet->getActiveSheet()->getStyle($celulas)->applyFromArray($styleHorizontalDotted);

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
header('Content-Disposition: attachment;filename="mapa_gado_resumido.xlsx"');
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