<?php
$data_sistema = date("d/m/Y");

@ session_start();
$cnpj_cliente = $_SESSION['id_cliente'];

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

// abre banco de dados
$banco = $cnpj_cliente;
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

$movimentacao_id = $_REQUEST["movimento_id"];

$tbl_movimentacao = mysqli_query($conector, "SELECT * FROM tbl_movimentacao
        INNER JOIN tbl_pessoa 
                ON tbl_pessoa_id = tbl_movimentacao_codigo_local_origem
             WHERE tbl_movimentacao_id='$movimentacao_id'");

$num_rows = mysqli_num_rows($tbl_movimentacao);

if ($num_rows!=0) {
    while ($reg_mov = mysqli_fetch_object($tbl_movimentacao)) {
        $desc_local_origem = utf8_encode($reg_mov->tbl_pessoa_nome);
        $desc_filtro = utf8_encode($reg_mov->tbl_movimentacao_filtros);     
        $codigo_tipo = $reg_mov->tbl_movimentacao_tipo;     
        $codigo_local_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;     
        $animais_pesados = $reg_mov->tbl_movimentacao_qtd_animais_pesados;
        $peso_kg = $reg_mov->tbl_movimentacao_peso_kg;
        $peso_arroba = $reg_mov->tbl_movimentacao_peso_arroba;
        $peso_medio_kg = $reg_mov->tbl_movimentacao_peso_medio_kg;
        $peso_medio_arroba = $reg_mov->tbl_movimentacao_peso_medio_arroba;
        $data_movimentacao = new DateTime($reg_mov->tbl_movimentacao_data);
        $data_movimentacao_edi = $data_movimentacao->format('d/m/Y');
        $controle = $reg_mov->tbl_movimentacao_controle;

        switch ($codigo_tipo) {
            case 3:
                $descricao_tipo = 'Venda';
                break;
            case 4:
                $descricao_tipo = 'Compra';
                break;
            case 5:
                $descricao_tipo = 'Transferência';
                break;
            default:
                $descricao_tipo = 'Morte ou Outras Saídas';
                break;
        }

        $tbl_local_destino = mysqli_query($conector, "SELECT * FROM tbl_pessoa
             WHERE tbl_pessoa_id='$codigo_local_destino'");

        $num_rows_destino = mysqli_num_rows($tbl_local_destino);

        if ($num_rows_destino!=0) {
            $reg_local_destino = mysqli_fetch_object($tbl_local_destino);
            $desc_local_destino = utf8_encode($reg_local_destino->tbl_pessoa_nome);            
        }
        else {
            $desc_local_destino = '';
        }
    }
}

$nome_relatorio = "Movimentação - " . $descricao_tipo;

$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->getActiveSheet()->mergeCells('A2:C2');
$spreadsheet->getActiveSheet()->mergeCells('D2:F2');
$spreadsheet->getActiveSheet()->mergeCells('G2:H2');
$spreadsheet->getActiveSheet()->mergeCells('B3:F3');
$spreadsheet->getActiveSheet()->mergeCells('B4:H4');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('A2', 'Local Origem: ' . $desc_local_origem)
    ->setCellValue('D2', 'Local Destino: ' . $desc_local_destino)
    ->setCellValue('G2', 'Data da Movimentação: ' . $data_movimentacao_edi)
    ->setCellValue('H1', 'Data: ' . $data_sistema)
    ->setCellValue('B5', 'Qtde Animais: ' . $animais_pesados)
    ->setCellValue('C5', 'Peso Kg: ' . number_format($peso_kg,2,',','.'))
    ->setCellValue('D5', 'Peso Arrobas: ' . number_format($peso_arroba,2,',','.'))
    ->setCellValue('E5', 'Peso Médio Kg: ' . number_format($peso_medio_kg,2,',','.'))
    ->setCellValue('F5', 'Peso Médio Arrobas: ' . number_format($peso_medio_arroba,2,',','.'));
    
if ($codigo_tipo==4) {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A7","Categoria")
        ->setCellValue("B7","Idade (meses)")
        ->setCellValue("C7","Sexo")
        ->setCellValue("D7","Raça")
        ->setCellValue("E7","Pelagem")
        ->setCellValue("F7","Qtd Catagoria");
        
    if ($controle=='I') {
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("G7","Seq Numérica")
            ->setCellValue("H7","Marcação Alfa");
    }        
}
else if ($controle=='L'){
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Filtros:")
        ->setCellValue("A7","Categoria")
        ->setCellValue("B7","Qtde")
        ->setCellValue("C7","Sexo")
        ->setCellValue("D7","Peso Kg")
        ->setCellValue("E7","Peso Médio Kg")
        ->setCellValue("F7","Peso @")
        ->setCellValue("G7","Peso Médio @")
        ->setCellValue("H7","Observação");
}
else {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Filtros:")
        ->setCellValue("A7","Id Animal")
        ->setCellValue("B7","Peso")
        ->setCellValue("C7","Sexo")
        ->setCellValue("D7","Nascimento")
        ->setCellValue("E7","Raça")
        ->setCellValue("F7","Pelagem")
        ->setCellValue("G7","Mãe")
        ->setCellValue("H7","Observação");
}

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(22);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(35);

$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A3:A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('H1:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A7:H7') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->getStartColor()->setARGB('D6DBDF');
$spreadsheet->getActiveSheet()->getStyle('A2:H2')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A2:H2')->getFill()->getStartColor()->setARGB('EBEDEF');

$spreadsheet->getActiveSheet()->getStyle('B4:H4')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B4:H4')->getFont()->setSize(10);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 4, $desc_filtro);

$spreadsheet->getActiveSheet()->setShowGridlines(true);

if ($controle=='I') {
    $tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
        INNER JOIN tbl_animais
            ON tbl_animal_codigo_id = tbl_ite_movimentacao_codigo_id_animal
        WHERE tbl_ite_movimentacao_numero_id='$movimentacao_id'
        ORDER BY tbl_animal_codigo_numerico ASC");
}
else {
    $tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
        WHERE tbl_ite_movimentacao_numero_id='$movimentacao_id'");
}

$num_rows = mysqli_num_rows($tbl_itens);

if ($num_rows!=0) {
    $linha=7;

    while ($reg_itens = mysqli_fetch_object($tbl_itens)) {
        if ($controle=='I') {
            $codigo_animal  = $reg_itens->tbl_ite_movimentacao_codigo_animal;

            $caracteres = strlen($codigo_animal);
    
            if ($caracteres>=9){
                $codigo_numerico = intval(substr($codigo_animal, (strlen($codigo_animal) - 9), 9));
                $codigo_alfa = strrev(preg_replace('/\d/', '',  strrev($codigo_animal), 9));

                if ($codigo_alfa=='' && $codigo_numerico==0) {
                    $codigo_animal_edi = '';
                }
                else if ($codigo_alfa==''){
                    $codigo_animal_edi = $codigo_numerico;
                }
                else {
                    $codigo_animal_edi = $codigo_alfa.'-'.$codigo_numerico;
                }
            }
            else {
                $codigo_animal_edi = $codigo_animal;
            }

            $codigo_mae = $reg_itens->tbl_ite_movimentacao_mae;
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
        }

        $peso = $reg_itens->tbl_ite_movimentacao_peso;
        $peso_medio = $reg_itens->tbl_ite_movimentacao_peso_medio;
        $arroba = $reg_itens->tbl_ite_movimentacao_peso_arroba;
        $arroba_media = $reg_itens->tbl_ite_movimentacao_peso_medio;
        $nascimento_edi = $reg_itens->tbl_ite_movimentacao_nascimento;        

        $data_nasc = $reg_itens->tbl_ite_movimentacao_nascimento;
        $data_nasc = str_replace("/", "-", $data_nasc);
        $data_nasc = date('Y-m-d', strtotime($data_nasc));

        $desc_raca = utf8_encode($reg_itens->tbl_ite_movimentacao_raca);
        $desc_pelagem = utf8_encode($reg_itens->tbl_ite_movimentacao_pelagem);
        $sexo =  $reg_itens->tbl_ite_movimentacao_sexo;

        if ($sexo == 'Macho' || $sexo == 'M') {
            $sexo = 'M';
        }
        else {
            $sexo = 'F';
        }

        $observacao = $reg_itens->tbl_ite_movimentacao_observacao;
        $codigo_motivo = $reg_itens->tbl_ite_movimentacao_motivo_morte;

        if ($codigo_tipo==4){
            $codigo_categoria = $reg_itens->tbl_ite_categoria_compra;
            $qtd_categoria = $reg_itens->tbl_ite_qtd_categoria_compra;
        }
        else {
            $codigo_categoria = $reg_itens->tbl_ite_movimentacao_codigo_categoria;
            $qtd_categoria = $reg_itens->tbl_ite_movimentacao_qtde_categoria;
        }

        $idade_meses = $reg_itens->tbl_ite_idade_meses_compra;
        $seq_numerica = $reg_itens->tbl_ite_sequencia_numerica_compra;
        $alfa = $reg_itens->tbl_ite_marcacao_alfa_compra;

        $tbl_motivo = mysqli_query($conector, "SELECT * FROM tabela_causa_morte
             WHERE tab_codigo_causa_morte='$codigo_motivo'");

        $num_rows_motivo = mysqli_num_rows($tbl_motivo);

        if ($num_rows_motivo!=0) {
            $reg_local_motivo = mysqli_fetch_object($tbl_motivo);
            $desc_motivo = $reg_local_motivo->tab_descricao_causa_morte;            
        }
        else {
            $desc_motivo = '';
        }

        switch ($codigo_categoria) {
            case '001':
                $desc_categoria = '00 a 07 meses';
                break;
            case '002':
                $desc_categoria = '08 a 12 meses';
                break;
            case '003':
                $desc_categoria = '13 a 24 meses';
                break;
            case '004':
                $desc_categoria = '25 a 36 meses';
                break;
            case '005':
                $desc_categoria = '> 36 meses';
                break;
            default:
                $desc_categoria = '';
                break;
        }

        $linha++;

        if ($controle == 'I') {
            $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc);                                             
            $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
        }

        if ($codigo_tipo==4) {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $desc_categoria);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $idade_meses);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $desc_pelagem);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $qtd_categoria);

            if ($controle == 'I') {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $seq_numerica);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $alfa);
            }
        }
        else if ($controle == 'L') {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $desc_categoria);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $qtd_categoria);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $peso);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $peso_medio);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $arroba);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $arroba_media);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $observacao);
        }
        else {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_animal_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $peso);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nascimento_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $desc_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $desc_pelagem);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $codigo_mae_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $observacao);
        }

        $spreadsheet->getActiveSheet()->getStyle('A'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('B'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('C'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('D'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('E'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('F'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('G'.$linha) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
    }
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="movimento_animais.xlsx"');
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
              
                
