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

    $data_sistema = date("d-m-Y");
    $data_inicial = $_REQUEST['data_inicial'];
    $data_final = $_REQUEST['data_final'];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $local_filtro = $_REQUEST["local"];
    $categoria_filtro = $_REQUEST["categoria"];
    $sexo_filtro = $_REQUEST["sexo"];

    $wsexo_pesagem='';

    if ($sexo_filtro!='Todos') {
        $wsexo_pesagem = " AND tbl_ite_pesagem_sexo IN(";
        $wsexo_pesagem .= "'" . $sexo_filtro . "'";
        $wsexo_pesagem.= ")";
    }

    $categoria= array();
    $matriz_itens = explode(",", $categoria_filtro);
    $quantidade_categoria = count($matriz_itens);

    for($i=0; $i < $quantidade_categoria; $i++) {
        $categoria[$i]=$matriz_itens[$i];
    }

    $categoria = implode(',', $categoria);
    $categoria = substr($categoria,0, -1);
    $quantidade_categoria--;

    $wcategoria = '';

    if ($categoria_filtro!='') {
        $wcategoria = " AND tbl_ite_pesagem_categoria IN(";
        $wcategoria.= $categoria;
        $wcategoria.= ")";
    }

    $nome_relatorio = "Histórico de Pesagem";

    $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
    $spreadsheet->getActiveSheet()->mergeCells('E1:F1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:F2');

	$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue('A1', $nome_relatorio)
        ->setCellValue('E1', 'Data: ' . $data_sistema)
		->setCellValue("A2", "Filtro: ")
		->setCellValue("B2", $descricao_filtro);

	$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
	$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);



	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(14);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(13);
	$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(19);
	$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);

	$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('E1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

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

    /*$spreadsheet->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('H4')->applyFromArray($styleArray);

    $spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('G5')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('H5')->applyFromArray($styleArray);*/

    $linha=2;

    $chave_anterior = '';
    $chave_cat_sexo_anterior = '';

    $sql = "SELECT * FROM tbl_item_pesagem
        INNER JOIN tbl_pesagem 
                ON tbl_pesagem_id = tbl_ite_pesagem_numero_id 
        WHERE tbl_pesagem_data>='$data_inicial' AND 
              tbl_pesagem_data<='$data_final' AND 
              tbl_pesagem_codigo_local='$local_filtro' AND 
              tbl_ite_pesagem_peso!=0" . 
              $wsexo_pesagem . $wcategoria . 
        " ORDER BY tbl_ite_pesagem_categoria ASC, 
                   tbl_ite_pesagem_sexo ASC,
                   tbl_ite_pesagem_data_emissao ASC, 
                   tbl_pesagem_codigo_epoca ASC";

    $tbl_item_pesagem = mysqli_query($conector, $sql);
    $num_rows_pesagem = mysqli_num_rows($tbl_item_pesagem);  

    if ($num_rows_pesagem!=0) {
        while ($reg_item = mysqli_fetch_object($tbl_item_pesagem)) {
            $data_peso = new DateTime($reg_item->tbl_ite_pesagem_data_emissao);
            $data_edi = $data_peso->format('d/m/Y');
            $sexo = $reg_item->tbl_ite_pesagem_sexo;
            $categoria = $reg_item->tbl_ite_pesagem_categoria;
            $qtd_animais = $reg_item->tbl_ite_pesagem_qtd_animais;
            $peso_medio = $reg_item->tbl_ite_pesagem_peso_medio;
            $codigo_epoca = $reg_item->tbl_pesagem_codigo_epoca;

            $chave = $categoria.$sexo.$data_peso->format('Y').$data_peso->format('m').
                     $data_peso->format('d').$codigo_epoca;

            $chave_cat_sexo = $categoria.$sexo;

            $tbl_categoria = mysqli_query($conector,"SELECT * FROM tabela_categoria_idade
                WHERE tab_codigo_categoria_idade ='$categoria'");

            $num_rows_categoria = mysqli_num_rows($tbl_categoria);  

            if ($num_rows_categoria!=0) {
                $reg_categoria = mysqli_fetch_object($tbl_categoria);

                if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                    $desc_categoria = '> 36 meses';
                }
                else {
                    $desc_categoria = $reg_categoria->tab_categoria_idade_de.' a '.
                    $reg_categoria->tab_categoria_idade_ate.' meses';
                }
            }
            else {
                $desc_categoria = 'Sem categoria';
            }

            $tbl_epoca_pesagem = mysqli_query($conector,"SELECT * FROM tabela_epoca_pesagem
                WHERE tab_codigo_epoca_pesagem  ='$codigo_epoca'");

            $num_rows_epoca = mysqli_num_rows($tbl_epoca_pesagem);  

            if ($num_rows_epoca!=0) {
                $reg_epoca = mysqli_fetch_object($tbl_epoca_pesagem);
                $desc_epoca=$reg_epoca->tab_descricao_epoca_pesagem;
            }
            else {
                $desc_epoca = '';
            }

            $tbl_peso_medio = mysqli_query($conector,"SELECT * FROM tbl_peso_medio_categoria
                WHERE tbl_pm_categoria_id='$categoria' AND 
                      tbl_pm_sexo='$sexo' AND 
                      tbl_pm_local_id='$local_filtro'");

            $num_rows_peso_medio = mysqli_num_rows($tbl_peso_medio);  

            if ($num_rows_peso_medio!=0) {
                $reg_peso_medio = mysqli_fetch_object($tbl_peso_medio);
                $peso_medio_atual=$reg_peso_medio->tbl_pm_peso_medio_atual;
            }
            else {
                $peso_medio_atual = 0;
            }
            
            if ($sexo=='M') {
                $sexo='Macho';
            }
            else {
                $sexo='Fêmea';
            }
            
            if ($chave_cat_sexo!=$chave_cat_sexo_anterior) {
                if ($chave_cat_sexo_anterior=='') {
                    $chave_cat_sexo_anterior=$chave_cat_sexo;
                    $chave_anterior=$chave;
                    $data_anterior = $data_edi;
                    $epoca_anterior = $desc_epoca;
                    $qtd_animais_anterior = $qtd_animais;
                    $peso_medio_anterior = $peso_medio*$qtd_animais;

                    $linha++;
                    $linha++;
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$linha.':B'.$linha);
                    $spreadsheet->getActiveSheet()->mergeCells('D'.$linha.':E'.$linha);
                    
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A'.$linha, 'Categoria: ' . $desc_categoria)
                        ->setCellValue('C'.$linha, 'Sexo: ' . $sexo)
                        ->setCellValue("D".$linha, 'Peso Médio Atual: ' . $peso_medio_atual);
                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha.':E'.$linha)->getFont()->setBold(true);

                    $celulas = 'D'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                    $linha++;
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue("A".$linha,"Data")
                        ->setCellValue("B".$linha,"Qtd Animais")
                        ->setCellValue("C".$linha,"Motivo da Pesagem")
                        ->setCellValue("D".$linha,"Peso Médio");
                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                }
                else {
                    $peso_medio_anterior = $peso_medio_anterior/$qtd_animais_anterior;

                    $linha++;
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $qtd_animais_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $epoca_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso_medio_anterior);
                    $celulas = 'D'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

                    $chave_anterior=$chave;
                    $chave_cat_sexo_anterior=$chave_cat_sexo;
                    $data_anterior = $data_edi;
                    $epoca_anterior = $desc_epoca;
                    $qtd_animais_anterior = $qtd_animais;
                    $peso_medio_anterior = $peso_medio*$qtd_animais;

                    $linha++;
                    $linha++;
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$linha.':B'.$linha);
                    $spreadsheet->getActiveSheet()->mergeCells('D'.$linha.':E'.$linha);
                    
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A'.$linha, 'Categoria: ' . $desc_categoria)
                        ->setCellValue('C'.$linha, 'Sexo: ' . $sexo)
                        ->setCellValue("D".$linha, 'Peso Médio Atual: ' . $peso_medio_atual);
                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha.':E'.$linha)->getFont()->setBold(true);

                    $celulas = 'D'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                    $linha++;
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue("A".$linha,"Data")
                        ->setCellValue("B".$linha,"Qtd Animais")
                        ->setCellValue("C".$linha,"Motivo da Pesagem")
                        ->setCellValue("D".$linha,"Peso Médio");
                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);
                }
            }
            else 
                if ($chave!=$chave_anterior) {
                    $peso_medio_anterior = $peso_medio_anterior/$qtd_animais_anterior;

                    $linha++;
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $qtd_animais_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $epoca_anterior);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso_medio_anterior);
                    $celulas = 'D'.$linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

                    $chave_anterior=$chave;
                    $data_anterior = $data_edi;
                    $epoca_anterior = $desc_epoca;
                    $qtd_animais_anterior = $qtd_animais;
                    $peso_medio_anterior = $peso_medio*$qtd_animais;
                }
                else {
                    $qtd_animais_anterior+= $qtd_animais;
                    $peso_medio_anterior+= $peso_medio*$qtd_animais;
                }
        } // Fim while item pesagem
    } // Fim if item pesagem

    $peso_medio_anterior = $peso_medio_anterior/$qtd_animais_anterior;

    $linha++;

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_anterior);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $qtd_animais_anterior);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $epoca_anterior);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso_medio_anterior);
    $celulas = 'D'.$linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($styleArray);

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="historico_pesagem.xlsx"');
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