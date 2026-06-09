<?php
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

$data_sistema = date("d/m/Y");
$lista_final = $_REQUEST['ids'];
$fazenda_id  = $_REQUEST['fazenda'];

$nome_relatorio = "Pesagem de Animais";
$descricao_filtro = $_REQUEST['nome_fazenda'];

$spreadsheet->getActiveSheet()->mergeCells('A1:I1');
$spreadsheet->getActiveSheet()->mergeCells('J1:K1');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');
$spreadsheet->getActiveSheet()->mergeCells('A3:K3');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue("J1", "Data: " . $data_sistema)
    ->setCellValue("A2", "Filtro: " . $descricao_filtro);

$spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(10);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A4", "Animais")
    ->setCellValue("C4", "Pesados")
    ->setCellValue("A5", "Id Animal")
    ->setCellValue("B5", "Peso")
    ->setCellValue("C5", "Sexo")
    ->setCellValue("D5", "Nascimento")
    ->setCellValue("E5", "Idade (meses)")
    ->setCellValue("F5", "Categoria")
    ->setCellValue("G5", "Raca")
    ->setCellValue("H5", "Pelagem")
    ->setCellValue("I5", "Mae Id")
    ->setCellValue("J5", "Pai Id")
    ->setCellValue("K5", "Descarte");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);

$spreadsheet->getActiveSheet()->getStyle('A5:K5')->getFont()->setColor(new Color(Color::COLOR_BLACK));
$spreadsheet->getActiveSheet()->getStyle('A5:K5')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

/*$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical($align);
*/
$spreadsheet->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setWrapText(true);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setVertical($align);

$linha = 5;
$animais_pesados = 0;
$total_animais = 0;

if(empty($lista_final) || empty($fazenda_id)){
    die("Parâmetros insuficientes.");
}

$sql = "SELECT 
    a.tbl_animal_codigo_id, 
    a.tbl_animal_codigo_alfa,
    a.tbl_animal_codigo_numerico,
    a.tbl_animal_sexo,
    a.tbl_animal_data_nascimento,
    a.tbl_animal_codigo_mae,
    a.tbl_animal_codigo_pai,
    a.tbl_animal_descarte_reproducao,
    -- Dados da Raça
    r.tab_descricao_raca,
    -- Dados da Pelagem
    p.tab_descricao_pelagem,
    -- Identificação da Mãe (Alfa + Numérico) buscando pelo ID da mãe
    CONCAT(mae.tbl_animal_codigo_alfa, ' ',CAST(mae.tbl_animal_codigo_numerico AS UNSIGNED)) AS mae_identificacao, 
    -- Peso (da sua lista de pesagens)
    item.tbl_ite_pesagem_peso
    FROM 
        tbl_animais a
    LEFT JOIN tabela_racas r ON a.tbl_animal_codigo_raca = r.tab_codigo_raca
    LEFT JOIN tabela_pelagens p ON a.tbl_animal_codigo_pelagem = p.tab_codigo_pelagem
    -- O JOIN da mãe liga o campo 'codigo_mae' do animal ao 'codigo_id' da tabela de apoio (mae)
    LEFT JOIN tbl_animais mae ON a.tbl_animal_codigo_mae = mae.tbl_animal_codigo_id
    LEFT JOIN tbl_item_pesagem item ON a.tbl_animal_codigo_id = item.tbl_ite_pesagem_codigo_id_animal 
        AND item.tbl_ite_pesagem_numero_id IN ($lista_final)
    WHERE 
        a.tbl_animal_codigo_fazenda = '$fazenda_id' 
        AND a.tbl_animal_ativo = 'S'
    ORDER BY 
        a.tbl_animal_codigo_numerico ASC";

$rs = mysqli_query($conector, $sql);

while ($reg = mysqli_fetch_array($rs)) {
    $id_animal = ltrim($reg['tbl_animal_codigo_id'], '0');
    $codigo_alfa = $reg['tbl_animal_codigo_alfa'];
    $codigo_numerico = $reg['tbl_animal_codigo_numerico'];
    $sexo      = ($reg['tbl_animal_sexo']=='M') ? 'Macho' : 'Fêmea';
    $raca      = $reg['tab_descricao_raca'];
    $pelagem   = $reg['tab_descricao_pelagem'];
    $mae_alfa  = $reg['mae_identificacao']; 
    $pai_alfa  = $reg['tbl_animal_codigo_pai'];
    $descarte  = ($reg['tbl_animal_descarte_reproducao'] == 'S') ? 'Sim' : '';    
    $peso      = $reg['tbl_ite_pesagem_peso'];

    $nascimento = new DateTime($reg['tbl_animal_data_nascimento']);
    $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($nascimento);

    $data_nasc = $reg['tbl_animal_data_nascimento'];
    $idade_meses = 0;
    if ($data_nasc && $data_nasc != '0000-00-00') {
        $nascimento = new DateTime($data_nasc);
        $hoje = new DateTime();
        $intervalo = $hoje->diff($nascimento);
        $idade_meses = ($intervalo->y * 12) + $intervalo->m;
    }

    if ($codigo_alfa=='') {
        $codigo_alfa_numerico=(int)$codigo_numerico;
    }
    else {
        $codigo_alfa_numerico=$codigo_alfa.'-'.(int)$codigo_numerico;
    }

    $total_animais++;

    if (is_null($peso) || $peso == "") {
        $exibir_peso = "";
    } else {
        $exibir_peso = (int)$peso;
        $animais_pesados++;
    }

    $categoria = ($idade_meses <= 12) ? "Bezerro(a)" : "Adulto";

    $linha++;

    $celulas = 'A' . $linha . ':K' . $linha;
    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
    $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_alfa_numerico);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $exibir_peso);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nascimento_edi);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $idade_meses);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $categoria);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $raca);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $pelagem);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $mae_alfa);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $pai_alfa);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $descarte);
}


$spreadsheet->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 4, $total_animais);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 4, $animais_pesados);

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="pesagem_sem_finalizar.xlsx"');
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