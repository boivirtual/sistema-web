<?php
$data_sistema = date("d/m/Y");

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
use PhpOffice\PhpSpreadsheet\Style\Borders;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@ session_start(); 
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$servidor = "localhost";
$usuario_bd = "root";
$banco = $cnpj_cliente;
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

$pesagem_id = $_REQUEST["pesagem_id"];

$tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_pesagem
        INNER JOIN tbl_pessoa 
                ON tbl_pessoa_id = tbl_pesagem_codigo_local
        INNER JOIN tabela_epoca_pesagem
                ON tab_codigo_epoca_pesagem = tbl_pesagem_codigo_epoca
             WHERE tbl_pesagem_id='$pesagem_id'");

$num_rows = mysqli_num_rows($tbl_pesagem);

if ($num_rows!=0) {
    while ($reg_pesagem = mysqli_fetch_object($tbl_pesagem)) {
        $desc_local = $reg_pesagem->tbl_pessoa_nome;
        $desc_epoca = $reg_pesagem->tab_descricao_epoca_pesagem;
        $desc_filtro = utf8_encode($reg_pesagem->tbl_pesagem_filtros);     
        $desc_lote = utf8_encode($reg_pesagem->tbl_pesagem_lote);     
        $animais_pesados = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
        $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
        $peso_arroba = $reg_pesagem->tbl_pesagem_peso_arroba;
        $peso_medio_kg = $reg_pesagem->tbl_pesagem_peso_medio_kg;
        $peso_medio_arroba = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
        $data_pesagem = new DateTime($reg_pesagem->tbl_pesagem_data);
        $data_pesagem_edi = $data_pesagem->format('d/m/Y');
    }
}


$nome_relatorio = "Pesagem Individual";

$spreadsheet->getActiveSheet()->mergeCells('A1:H1');
$spreadsheet->getActiveSheet()->mergeCells('B2:C2');
$spreadsheet->getActiveSheet()->mergeCells('B3:I3');
$spreadsheet->getActiveSheet()->mergeCells('B4:I4');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('I1', 'Data: ' . $data_sistema)
    ->setCellValue('A2', 'Fazenda: ')
    ->setCellValue('B2', $desc_local)
    ->setCellValue('D2', 'Motivo da Pesagem: ')
    ->setCellValue('E2', $desc_epoca)
    ->setCellValue('F2', 'Data da Pesagem: ')
    ->setCellValue('G2', $data_pesagem_edi)
    ->setCellValue('A3', 'Descrição do Lote: ')
    ->setCellValue('B3', $desc_lote)
    ->setCellValue('A4', 'Filtros: ')
    ->setCellValue('B4', $desc_filtro)
    ->setCellValue('B5', 'Animais Pesados: ' . $animais_pesados)
    ->setCellValue('C5', 'Peso Kg: ' . $peso_kg)
    ->setCellValue('D5', 'Peso Arrobas: ' . $peso_arroba)
    ->setCellValue('E5', 'Peso Médio Kg: ' . $peso_medio_kg)
    ->setCellValue('F5', 'Peso Médio Arrobas: ' . $peso_medio_arroba);
    
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A7","Código Alfa")
    ->setCellValue("B7","Código Numérico")
    ->setCellValue("C7","Peso")
    ->setCellValue("D7","Sexo")
    ->setCellValue("E7","Nascimento")
    ->setCellValue("F7","Raça")
    ->setCellValue("G7","Pelagem")
    ->setCellValue("H7","Mãe")
    ->setCellValue("I7","Observação");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(21);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(16);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(21);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(17);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(16);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);

$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A3:A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A7:I7') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('A7:I7')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A7:I7')->getFill()->getStartColor()->setARGB('D6DBDF');
/*$spreadsheet->getActiveSheet()->getStyle('A2:I2')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A2:I2')->getFill()->getStartColor()->setARGB('EBEDEF');
*/

$spreadsheet->getActiveSheet()->getStyle('B4:I4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B4:I4')->getFont()->setSize(10);

$spreadsheet->getActiveSheet()->setShowGridlines(true);

$tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    INNER JOIN tbl_animais
            ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
    WHERE tbl_ite_pesagem_numero_id='$pesagem_id'
    ORDER BY tbl_animal_codigo_numerico ASC");

$num_rows = mysqli_num_rows($tbl_itens);

if ($num_rows!=0) {
    $linha=7;

    while ($reg_itens = mysqli_fetch_object($tbl_itens)) {
        $codigo_alfa = $reg_itens->tbl_animal_codigo_alfa;
        $codigo_numerico = intval($reg_itens->tbl_animal_codigo_numerico);

        $peso  = $reg_itens->tbl_ite_pesagem_peso;
        $data_nasc = $reg_itens->tbl_ite_pesagem_nascimento;
        $data_nasc = str_replace("/", "-", $data_nasc);
        $data_nasc = date('Y-m-d', strtotime($data_nasc));
        $desc_raca = $reg_itens->tbl_ite_pesagem_raca;
        $desc_pelagem = $reg_itens->tbl_ite_pesagem_pelagem;
        $sexo =  utf8_encode($reg_itens->tbl_ite_pesagem_sexo);
        $observacao = utf8_encode($reg_itens->tbl_ite_pesagem_observacao);
        $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc);                                             

        $codigo_mae = $reg_itens->tbl_ite_pesagem_mae;
        $codigo_numerico_mae = intval(substr($codigo_mae, (strlen($codigo_mae) - 9), 9));
        $codigo_alfa_mae = strrev(preg_replace('/\d/', '',  strrev($codigo_mae), 9));

        if ($codigo_alfa_mae=='' && $codigo_numerico_mae==0) {
            $codigo_mae_edi = '';
        }
        else if ($codigo_alfa_mae==''){
            $codigo_mae_edi = $codigo_numerico_mae;
        }
        else {
            $codigo_mae_edi = $codigo_alfa_mae.'-'.$codigo_numerico_mae;
        }

        $linha++;
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
        $celulas = 'H'.$linha;
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_alfa);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $codigo_numerico);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $peso);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $nascimento_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_raca);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $desc_pelagem);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $codigo_mae_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $observacao);


        $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('D'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('F'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('H'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('I'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    }
}
   

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="animais_pesados.xlsx"');
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
              
                
