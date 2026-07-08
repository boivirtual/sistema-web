<?php
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

@session_start();
$cnpj_cliente = $_SESSION['id_cliente'];

// abre banco de dados
$banco = $cnpj_cliente;
include_once "conecta_mysql_credenciais.inc";

$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd);

if (mysqli_connect_error()) {
    print_r("Falha na conexão: ", mysqli_connect_error());
    exit;
}

$bancoselecionado = mysqli_select_db($conector, $banco);

if ($bancoselecionado === FALSE) {
    print_r("Falha na seleção do banco de dados: " . mysqli_error($conector));
    exit;
}

@ session_start(); 
$data_sistema = date("d/m/Y");

$_SESSION['local_pesagem']='';
$_SESSION['categoria_historico_animais']='';

$codigo_alfa_numerico = $_REQUEST['codigo_alfa_numerico']; 

if ($codigo_alfa_numerico!='') {
    $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

    if (strlen($codigo_alfa_numerico)!=9){
        $data = explode("-", $codigo_alfa_numerico);
        $codigo_alfa_consulta = $data[0];
    }
    else {
        $codigo_alfa_consulta = '';
    }
}

$nome_relatorio = "Histórico de Animais - Individual";

$spreadsheet->getActiveSheet()->mergeCells('A1:D1');
$spreadsheet->getActiveSheet()->mergeCells('E1:F1');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue("E1", "Data: " . $data_sistema)
    ->setCellValue('A16', 'Histórico das Pesagens');

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(11);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A1:A12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$linha = 6;

        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_consulta'");

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        if ($num_rows_animais==0){
            mysqli_close($conector);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("B2", $descricao_filtro . 'Registro não encontrado');
        }   
        else {
            $reg_animal = mysqli_fetch_object($tbl_animais);
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
            $ativo = $reg_animal->tbl_animal_ativo;
            $animal_situacao = $reg_animal->tbl_animal_situacao;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;
            $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
            $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
            $descarte_por = 'Por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
            $nome_pessoa = utf8_encode($reg_animal->tbl_pessoa_nome); 
            $pai = $reg_animal->tbl_animal_codigo_pai;
            $mae =  $reg_animal->tbl_animal_codigo_mae;
            $codigo_origem = $reg_animal->tbl_animal_codigo_origem;
            $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;
            $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso;

            // AJUSTE DO PESO DE DESMAMA
            if ($peso_desmama!='' && $peso_desmama!=0) {
                if ($peso_nasc=='' || $peso_nasc==0) {
                    $peso_nasc = 30;
                }
                        
                $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                $data_final = $reg_animal->tbl_animal_data_desmama;
                $diferenca = strtotime($data_final) - 
                             strtotime($data_inicial);
                $dias = floor($diferenca / (60 * 60 * 24));

                $diferenca_peso = $peso_desmama - $peso_nasc;
                $gmd = $diferenca_peso/$dias;

                $peso_desmama = $peso_nasc + ($gmd * 205);
            }
            // FIM AJUSTE DO PESO DE DESMAMA

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = utf8_encode($reg->tbl_semem_nome);
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . ltrim($reg->tbl_animal_codigo_numerico, "0");
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
            $num_rows = mysqli_num_rows($tab_mae);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_mae);
                if ($reg->tbl_animal_codigo_alfa==''){
                    $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                }
                else {
                    $descricao_mae = $reg->tbl_animal_codigo_alfa.'-'. intval($reg->tbl_animal_codigo_numerico);
                }
            }
            else {
                $descricao_mae = '';
            }
            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_baixa = $reg_animal->tbl_animal_baixado_em;

            if ($data_baixa!='') {
                $data_acompanhamento_calculo = date($data_baixa);
            }
            else {
                $data_acompanhamento_calculo = date("Y-m-d");
            }

            $date = new DateTime($data_nascimento);

            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');
            $idade_animal = $idade_acompanhamento_mostra_anos+
                            $idade_acompanhamento_mostra_meses;

            if ($idade_ano==0 && $idade_mes!=0) {
                $desc_idade = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $desc_idade = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $desc_idade = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else {
                $desc_idade = '';
            }

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);

            if ($reg_animal->tbl_animal_sexo=='M') {
                $sexo = 'Macho';
            }
            else {
                $sexo = 'Femea';
            }

            if ($codigo_alfa=='') {
                $codigo_edi = intval($codigo_numerico);
            }
            else {
                $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
            }

            if ($ativo=='N') {
                $ativo = 'Não';
            }
            else {
                $ativo = 'Sim';
            }

            switch ($animal_situacao) {
            case 'T':
                $animal_situacao='Aguardando Transferência';
                break;
            case 'V':
                $animal_situacao='Vendido';
                break;
            case 'M':   
                $animal_situacao='Morte';
                break;
            case 'S':   
                $animal_situacao='Outra Saída';
                break;
            } 

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade_animal >= $idade_de && 
                        $idade_animal <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }
            }                   

            switch ($codigo_categoria) {
                case '001':
                    $desc_categoria= '00 a 07 meses';
                    break;
                case '002':
                    $desc_categoria= '08 a 12 meses';
                    break;
                case '003':
                    $desc_categoria= '13 a 24 meses';
                    break;
            case '004':
                    $desc_categoria= '25 a 36 meses';
                    break;
                case '005':
                    $desc_categoria= '> 36 meses';
                    break;
            }     

            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                
            $num_rows = mysqli_num_rows($tab_fazenda);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_fazenda);
                $desc_origem = utf8_encode($reg->tbl_pessoa_nome);
            }
            else {
                $desc_origem = '';
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                
            $num_rows = mysqli_num_rows($tab_raca);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = utf8_encode($reg->tab_descricao_raca);
            }
            else {
                $descricao_raca = '';
            }

            $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
                
            $num_rows = mysqli_num_rows($tab_pelagem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pelagem);
                $descricao_pelagem = utf8_encode($reg->tab_descricao_pelagem);
            }
            else {
                $descricao_pelagem = '';
            }

            $spreadsheet->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode('DD/MM/YYYY');

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A3", "Nº Animal ")
                ->setCellValue("B3", $codigo_edi . ' - ' . $sexo)
                ->setCellValue("A4", "Nascimento ")
                ->setCellValue("B4", $data_nascimento_edi)
                ->setCellValue("C4", "Idade ")
                ->setCellValue("D4", $desc_idade)
                ->setCellValue("A5", "Categoria ")
                ->setCellValue("B5", $desc_categoria)
                ->setCellValue("A6", "Raça ")
                ->setCellValue("B6", $descricao_raca)
                ->setCellValue("C6", "Pelagem ")
                ->setCellValue("D6", $descricao_pelagem)
                ->setCellValue("A7", "Fazenda ")
                ->setCellValue("B7", $nome_pessoa)
                ->setCellValue("A8", "Origem ")
                ->setCellValue("B8", $desc_origem)
                ->setCellValue("A10", "Pai ")
                ->setCellValue("B10", $descricao_pai)
                ->setCellValue("A11", "Mãe ")
                ->setCellValue("B11", $descricao_mae);

            if ($ativo == "Sim") {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A9", "Animal Ativo ")
                ->setCellValue("B9", 'Sim');

                $spreadsheet->getActiveSheet()->getStyle('B9')->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));

            }
            else {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A9", "Animal Ativo")
                ->setCellValue("B9", 'Não')
                ->setCellValue("C9", "Situação ")
                ->setCellValue("D9", $animal_situacao);

                $spreadsheet->getActiveSheet()->getStyle('B9')->getFont()->setColor(new Color(Color::COLOR_RED));
                $spreadsheet->getActiveSheet()->getStyle('D9')->getFont()->setColor(new Color(Color::COLOR_RED));
            }

            if ($descarte == 'S') {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A12", "Descarte ")
                ->setCellValue("B12", $descarte_por);

                $spreadsheet->getActiveSheet()->getStyle('B12')->getFont()->setColor(new Color(Color::COLOR_RED));
            }

            $spreadsheet->getActiveSheet()->mergeCells('A14:B14');
            $spreadsheet->getActiveSheet()->mergeCells('C14:D14');
            $spreadsheet->getActiveSheet()->mergeCells('E14:F14');
            $spreadsheet->getActiveSheet()->mergeCells('A16:F16');

            $spreadsheet->getActiveSheet()->mergeCells('B17:C17');
            $spreadsheet->getActiveSheet()->mergeCells('D17:E17');

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A14", "Peso Inicial: " . number_format($peso_nasc,2,',','.'))
                ->setCellValue("C14", "Peso Desmama: " . number_format($peso_desmama,2,',','.'))
                ->setCellValue("E14", "Último Peso: " . number_format($ultimo_peso,2,',','.'))
                ->setCellValue("A16", "Histórico das Pasagens");

            $spreadsheet->getActiveSheet()->getStyle('A16')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->getStyle('A16')->getFont()->setColor(new Color(Color::COLOR_BLACK));
            $spreadsheet->getActiveSheet()->getStyle('A16')->getFont()->setBold(true);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A17", "Data")
                ->setCellValue("B17", "Motivo da Pesagem")
                ->setCellValue("D17", "Fazenda")
                ->setCellValue("F17", "Peso");

            $spreadsheet->getActiveSheet()->getStyle('A17')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B17')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('D17')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('F17')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $linha = 17;

            $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem 
                INNER JOIN tbl_pesagem
                        ON tbl_pesagem_id = tbl_ite_pesagem_numero_id   
                WHERE tbl_ite_pesagem_codigo_id_animal='$codigo' and 
                      tbl_ite_pesagem_peso!=0
                ORDER BY tbl_ite_pesagem_data_emissao DESC"); 

            $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);

            if ($num_rows_pesagem!=0) {
                while ($reg_ite_peso = mysqli_fetch_object($tbl_pesagem)){
                    $data = new DateTime($reg_ite_peso->tbl_ite_pesagem_data_emissao); 
                    $data_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data);
                    $epoca = $reg_ite_peso->tbl_pesagem_codigo_epoca; 
                    $origem = $reg_ite_peso->tbl_pesagem_codigo_local; 
                    $peso = $reg_ite_peso->tbl_ite_pesagem_peso; 

                    $tab_origem = mysqli_query($conector, "SELECT * FROM tbl_pessoa WHERE tbl_pessoa_id='$origem'");
                    $num_rows = mysqli_num_rows($tab_origem);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_origem);
                        $desc_origem = utf8_encode($reg->tbl_pessoa_nome);
                    }
                    else {
                        $desc_origem = '';
                    }

                    $tab_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem WHERE tab_codigo_epoca_pesagem ='$epoca'");
                    $num_rows = mysqli_num_rows($tab_epoca);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_epoca);
                        $desc_epoca = utf8_encode($reg->tab_descricao_epoca_pesagem);
                    }
                    else {
                        $desc_epoca= '';
                    }

                    $linha++;

                    $celulas = 'B' . $linha . ':C' . $linha;
                    $spreadsheet->getActiveSheet()->mergeCells($celulas);
                    $celulas = 'D' . $linha . ':E' . $linha;
                    $spreadsheet->getActiveSheet()->mergeCells($celulas);

                    $celulas = 'F' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $celulas = 'A' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $spreadsheet->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $data_edi);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_epoca);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $desc_origem);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $peso);
                } 
            }
        }   


// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="historico_animais.xlsx"');
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
