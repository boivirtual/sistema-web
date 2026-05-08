<?php

$data_hoje = date("d/m/Y");
$data_sistema = date("Y-m-d");

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

$codigo_local = $_REQUEST["local"];
$previsao_parto_de = $_REQUEST["previsao_parto_de"];
$previsao_parto_ate = $_REQUEST["previsao_parto_ate"];
$descricao_filtro = $_REQUEST["descricao_filtro"];
$diagnostico = $_REQUEST["diagnostico"];
$periodo_de = $_REQUEST["periodo_de"];
$periodo_ate = $_REQUEST["periodo_ate"];
$opcao_monta = $_REQUEST["opcao_monta"];

/*if ($previsao_parto_de=='') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-12-31';
}*/

$situacao_filtro = utf8_decode($_REQUEST["array_situacao"]);

$situacao= array();
$matriz_itens = explode(",", $situacao_filtro);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
    $situacao[$i]=$matriz_itens[$i];
}

$situacao = implode(',', $situacao);
$situacao = substr($situacao,0, -1);
$situacao = explode(',', $situacao);

$wsituacao = '';

if ($situacao_filtro!='') {
    if ($situacao[0]==' ') {
        $wsituacao  = " AND (tbl_ite_cobertura_nascido IN(";
        for ($i=0; $i < count($situacao); $i++) { 
            $wsituacao .= "'".$situacao[$i]."',";
        }
        $wsituacao = substr($wsituacao,0, -1);
        $wsituacao .= ") OR tbl_ite_cobertura_nascido IS NULL) ";
    }
    else {
        $wsituacao  = " AND tbl_ite_cobertura_nascido IN(";
        for ($i=0; $i < count($situacao); $i++) { 
            $wsituacao .= "'".$situacao[$i]."',";
        }
        $wsituacao = substr($wsituacao,0, -1);
        $wsituacao .= ") ";
    }
}

@ session_start(); 

/*if ($diagnostico=='P') {
    $nome_relatorio = "Diagnóstico Final - Positivas";
}
else {
    $nome_relatorio = "Diagnóstico Final - Negativas";
}*/

$nome_relatorio = 'Diagnóstico Final Monta ';

$registros = 0;

$spreadsheet->getActiveSheet()->mergeCells('A1:H1');
$spreadsheet->getActiveSheet()->mergeCells('I1:J1');
$spreadsheet->getActiveSheet()->mergeCells('B2:J2');

/*if ($diagnostico=='P') {
    $spreadsheet->getActiveSheet()->mergeCells('I4:J4');
}*/

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
	->setCellValue("I1", "Data: " . $data_hoje)
	->setCellValue("A2", "Filtro: ")
	->setCellValue("B2", $descricao_filtro)
    ->setCellValue("A3", "Registros: ");

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

if ($opcao_monta=='I') {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Nº Fêmea")
        ->setCellValue("B4","Raça")
        ->setCellValue("C4","Idade (meses)")
        ->setCellValue("D4","Nº Partos")
        ->setCellValue("E4","Nº Abortos")
        ->setCellValue("F4","Data da Prenhes")
        ->setCellValue("G4","Previsão do Parto");
}
else {
    if ($diagnostico=='P') {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Nº Fêmea")
        ->setCellValue("B4","Raça")
        ->setCellValue("C4","Idade (meses)")
        ->setCellValue("D4","Nº Partos")
        ->setCellValue("E4","Nº Abortos")
        ->setCellValue("F4","Data da Prenhes")
        ->setCellValue("G4","Previsão do Parto")
        ->setCellValue("H4","Diagnóstico")
        ->setCellValue("I4","Nascido")
        ->setCellValue("J4","Descarte");
    }
    else {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Nº Fêmea")
        ->setCellValue("B4","Raça")
        ->setCellValue("C4","Idade (meses)")
        ->setCellValue("D4","Nº Partos")
        ->setCellValue("E4","Nº Abortos")
        ->setCellValue("F4","Negativo em")
        ->setCellValue("G4","Descarte");
    }
}

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(17);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(9);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('I1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('B2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setVertical($align);

//$spreadsheet->getActiveSheet()->getStyle('C4:K4')->getAlignment()->setWrapText(true);

$linha=4;

/*$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
    WHERE tbl_animal_codigo_id = 4996
    ORDER BY tbl_animal_codigo_numerico ASC"); */

$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
    WHERE tbl_animal_sexo = 'F'
    ORDER BY tbl_animal_codigo_numerico ASC"); 

while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
    $codigo_id = $reg_animal->tbl_animal_codigo_id;
    $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
    $codigo_numerico = intval($reg_animal->tbl_animal_codigo_numerico);

    if ($codigo_alfa=='') {
        $codigo_edi = intval($codigo_numerico);
    }
    else {
        $codigo_edi = $codigo_alfa . '-' . intval($codigo_numerico);
    }

    $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
    $descarte_reproducao = $reg_animal->tbl_animal_descarte_reproducao; 
    $data_baixa = $reg_animal->tbl_animal_baixado_em;
    $ativo = $reg_animal->tbl_animal_ativo;

    if ($descarte_reproducao=='S') {
        $descarte = 'Sim';
    }
    else {
        $descarte = '';
    }

    if ($opcao_monta=='I') {
        $sql =  "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                  tbl_cobertura_codigo_local = '$codigo_local' AND 
                  tbl_ite_cobertura_previsao_parto is null AND
                  tbl_cobertura_controle = 'M' AND 
                  (tbl_ite_cobertura_resultado_diagnostico='' OR 
                   tbl_ite_cobertura_resultado_diagnostico is null) AND 
                  tbl_ite_cobertura_nascido is null" . $wsituacao; 
    }
    else {
        if ($diagnostico=='P') {
            if ($periodo_de!='') {
                $sql =  "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                          tbl_cobertura_codigo_local = '$codigo_local' AND 
                          tbl_cobertura_controle = 'M' AND
                          tbl_ite_cobertura_data_prenhes>='$periodo_de' AND
                          tbl_ite_cobertura_data_prenhes<='$periodo_ate' AND 
                          tbl_ite_cobertura_resultado_diagnostico='P'" . 
                          $wsituacao; 
            }
            else {
                $sql =  "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                          tbl_cobertura_codigo_local = '$codigo_local' AND 
                          tbl_cobertura_controle = 'M' AND
                          tbl_ite_cobertura_resultado_diagnostico='P' AND 
                          tbl_ite_cobertura_previsao_parto>='$previsao_parto_de' AND 
                          tbl_ite_cobertura_previsao_parto<='$previsao_parto_ate'" . 
                          $wsituacao; 
            }
        }
        else {
            $sql =  "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                      tbl_cobertura_codigo_local = '$codigo_local' AND 
                      tbl_cobertura_controle = 'M' AND 
                      DATE(tbl_ite_cobertura_negativo_em)>='$periodo_de' AND
                      DATE(tbl_ite_cobertura_negativo_em)<='$periodo_ate' AND 
                      tbl_ite_cobertura_resultado_diagnostico='N'"; 
        }
    }

    //print_r($sql . '</br>');

    $tbl_cobertura = mysqli_query($conector, $sql);
    $num_rows_coberturas = mysqli_num_rows($tbl_cobertura);

    if ($num_rows_coberturas > 0) {
        while($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
            $cobertura_id=$reg_cobertura->tbl_cobertura_id;
            $numero_item=$reg_cobertura->tbl_ite_cobertura_numero_item;
            $cobertura_ordem = $cobertura_id . $numero_item;

            $diagnostico=$reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $nascido=$reg_cobertura->tbl_ite_cobertura_nascido;
            $nascido_outro = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

            $data_prenhes = $reg_cobertura->tbl_ite_cobertura_data_prenhes;
            $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
            $data_negativo = $reg_cobertura->tbl_ite_cobertura_negativo_em;

            if ($data_negativo!='') {
                $data_negativo_edi = date("d/m/Y", strtotime(str_replace('-', '/', $data_negativo)));
                $data_negativo_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_negativo_edi);                
            }
            else {
                $data_negativo_edi = '';
            }

            if ($data_prenhes!='') {
                $data_prenhes_edi = date("d/m/Y", strtotime(str_replace('-', '/', $data_prenhes)));
                $data_prenhes_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_prenhes_edi);                }
            else {
                $data_prenhes_edi = '';
            }

            if ($data_previsao_parto!='') {
                $data_previsao_parto_edi = date("d/m/Y", strtotime(str_replace('-', '/', $data_previsao_parto)));
                $data_previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_previsao_parto_edi);
            }
            else {
                $data_previsao_parto_edi = '';
            }

            if ($data_prenhes!='') {
                $data_acompanhamento_calculo = date("Y-m-d", strtotime($reg_cobertura->tbl_ite_cobertura_data_prenhes));
            }
            else {
                $data_acompanhamento_calculo = date("Y-m-d");
            }

            switch ($nascido) {
                case 'N':
                    $desc_nascido = 'Sim';
                    break;
                case 'A':
                    $desc_nascido = 'Aborto';
                    break;
                case 'M':
                    $desc_nascido = 'Natimorto';
                    break;
                case 'O':
                   $desc_nascido = 'Outro';
                    break;
                default:
                   $desc_nascido = '';
                   break;
            }

            if ($desc_nascido == 'Outro') {
                switch ($nascido_outro) {
                    case 'V':
                        $desc_nascido = 'Fêmea Vendida';
                        break;
                    case 'M':
                        $desc_nascido = 'Fêmea Morreu';
                        break;
                    case 'O':
                       $desc_nascido = 'Fêmea Outra Saída';
                        break;
                    default:
                       $desc_nascido = 'Fêmea Outra Saída';
                       break;
                }
            }

            $date = new DateTime($data_nascimento);
                                
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas 
                WHERE tab_codigo_raca='$codigo_raca' AND 
                      tab_registro_lixeira_raca=0");  

            $num_rows = mysqli_num_rows($tbl_raca);

            if ($num_rows!=0){
                $reg_raca = mysqli_fetch_object($tbl_raca);
                $desc_raca = utf8_encode($reg_raca->tab_descricao_raca);
            }
            else {
                $desc_raca = '';
            }

            $numero_partos = 0;
            $num_abortos = 0;

            // primeiro verifica quantos partos
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_id'");

            $numero_partos = mysqli_num_rows($tbl_filhos);

            // verifica abortos/absorsão
            $tbl_aborto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                where tbl_mov_estoque_codigo_mae='$codigo_id' and 
                      tbl_mov_estoque_entrada_saida='A'");

            $num_abortos = mysqli_num_rows($tbl_aborto);

            $linha++;
            $registros++;

            $celulas = 'A'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $celulas = 'C'.$linha.':J'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $idade);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_partos);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $num_abortos);

            if ($diagnostico=='P') {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $data_prenhes_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $data_previsao_parto_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $diagnostico);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $desc_nascido);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $descarte);
                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                $spreadsheet->getActiveSheet()->getStyle('J'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));
            }
            else {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $data_negativo_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descarte);
                $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                $spreadsheet->getActiveSheet()->getStyle('G'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));
            }


        //$spreadsheet->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        }
    }
} // Fim do while tbl_animais

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 3, $registros);

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="diagnostico_final.xlsx"');
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