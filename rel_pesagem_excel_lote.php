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


$nome_relatorio = "Pesagem";

$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->getActiveSheet()->mergeCells('B2:G2');
$spreadsheet->getActiveSheet()->mergeCells('B3:G3');
$spreadsheet->getActiveSheet()->mergeCells('B4:H4');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('H2', 'Epoca da Pesagem: ' . $desc_epoca)
    ->setCellValue('H3', 'Data da Pesagem: ' . $data_pesagem_edi)
    ->setCellValue('H1', 'Data: ' . $data_sistema)
    ->setCellValue('C5', 'Animais Pesados: ' . $animais_pesados)
    ->setCellValue('D5', 'Peso Kg: ' . $peso_kg)
    ->setCellValue('E5', 'Peso Médio Kg: ' . $peso_medio_kg)
    ->setCellValue('F5', 'Peso @: ' . $peso_arroba)
    ->setCellValue('G5', 'Peso Médio @ ' . $peso_medio_arroba);
    
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A2","Local:")
    ->setCellValue("A3","Descrição da Pesagem:")
    ->setCellValue("A4","Filtros:")
    ->setCellValue("A7","Categoria")
    ->setCellValue("B7","Sexo")
    ->setCellValue("C7","Qtde")
    ->setCellValue("D7","Peso Kg")
    ->setCellValue("E7","Peso Médio Kg")
    ->setCellValue("F7","Peso @")
    ->setCellValue("G7","Peso Médio @")
    ->setCellValue("H7","Observação");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(21);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(7);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(21);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(17);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(21);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(40);

$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('H1:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A7:H7') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('C5:G5') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->getStartColor()->setARGB('D6DBDF');
$spreadsheet->getActiveSheet()->getStyle('A2:H2')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A2:H2')->getFill()->getStartColor()->setARGB('EBEDEF');

$spreadsheet->getActiveSheet()->getStyle('B4:H4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B4:H4')->getFont()->setSize(10);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 2, $desc_local);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 4, $desc_filtro);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $desc_lote);

$spreadsheet->getActiveSheet()->setShowGridlines(true);

$tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    WHERE tbl_ite_pesagem_numero_id='$pesagem_id'");

$num_rows = mysqli_num_rows($tbl_itens);

if ($num_rows!=0) {
    $linha=7;

    while ($reg_itens = mysqli_fetch_object($tbl_itens)) {
        $item  = $reg_itens->tbl_ite_pesagem_numero_item;
        $codigo_categoria  = $reg_itens->tbl_ite_pesagem_categoria;
        $qtd  = $reg_itens->tbl_ite_pesagem_qtd_animais;
        $sexo =  utf8_encode($reg_itens->tbl_ite_pesagem_sexo);
        $peso  = $reg_itens->tbl_ite_pesagem_peso;
        $peso_medio  = $reg_itens->tbl_ite_pesagem_peso_medio;
        $arroba  = $reg_itens->tbl_ite_pesagem_arroba;
        $arroba_media  = $reg_itens->tbl_ite_pesagem_arroba_media;
        $observacao = utf8_encode($reg_itens->tbl_ite_pesagem_observacao);

        $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_codigo_categoria_idade='$codigo_categoria'");
        $num_rows = mysqli_num_rows($tbl_categoria);
        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tbl_categoria);
            if ($reg->tab_categoria_idade_ate==999999999) {
                $desc_categoria = '> 36 meses'; 
            }
            else {
                $desc_categoria = $reg->tab_categoria_idade_de . ' a ' . 
                                  $reg->tab_categoria_idade_ate . ' meses';
            }
        }
        else {
            $desc_categoria = '';
        }

        $linha++;
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $desc_categoria);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $qtd);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $peso_medio);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $arroba);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $arroba_media);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $observacao);

        $celulas = 'D'.$linha.':G'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

        $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('D'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('F'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('H'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
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
              
                
