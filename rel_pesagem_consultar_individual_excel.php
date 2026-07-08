<?php
$data_sistema = date("d/m/Y");

//      Começa Excel
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

$bancoselecionado = mysqli_select_db($conector, $banco);

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

if ($num_rows != 0) {
    $reg_pesagem = mysqli_fetch_object($tbl_pesagem);

    $desc_local = $reg_pesagem->tbl_pessoa_nome;
    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
    $desc_motivo = $reg_pesagem->tab_descricao_epoca_pesagem;
    $desc_filtro = $reg_pesagem->tbl_pesagem_filtros;
    $desc_lote = $reg_pesagem->tbl_pesagem_lote;
    $animais_pesados = intval($reg_pesagem->tbl_pesagem_qtd_animais_pesados);
    $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
    $peso_arroba = $reg_pesagem->tbl_pesagem_peso_arroba;
    $peso_medio_kg = $reg_pesagem->tbl_pesagem_peso_medio_kg;
    $peso_medio_arroba = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
    $num_movimentacao = $reg_pesagem->tbl_pesagem_codigo_movimentacao;

    $data_pesagem = new DateTime($reg_pesagem->tbl_pesagem_data);
    $data_pesagem_edi = $data_pesagem->format('d/m/Y');

    $nome_inclusao = $reg_pesagem->tbl_pesagem_incluido_por;
    $data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');
}
else {
    print_r("Pesagem não encontrada.");
    exit;
}

$nome_relatorio = "Pesagem Finalizada";

$spreadsheet->getActiveSheet()->mergeCells('A1:I1');
$spreadsheet->getActiveSheet()->mergeCells('J1:K1');
$spreadsheet->getActiveSheet()->mergeCells('B2:C2');
$spreadsheet->getActiveSheet()->mergeCells('E2:F2');
$spreadsheet->getActiveSheet()->mergeCells('B3:C3');
$spreadsheet->getActiveSheet()->mergeCells('E3:F3');
$spreadsheet->getActiveSheet()->mergeCells('B4:K4');
$spreadsheet->getActiveSheet()->mergeCells('B5:K5');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue('K1', 'Data: ' . $data_sistema)
    ->setCellValue('A2', 'Nº do Documento: ')
    ->setCellValue('B2', $pesagem_id)
    ->setCellValue('D2', 'Data da Pesagem: ')
    ->setCellValue('E2', $data_pesagem_edi)
    ->setCellValue('A3', 'Lote: ')
    ->setCellValue('B3', $desc_lote)
    ->setCellValue('D3', 'Motivo: ')
    ->setCellValue('E3', $desc_motivo)
    ->setCellValue('G3', 'Nº da Movimentação: ')
    ->setCellValue('H3', $num_movimentacao)
    ->setCellValue('A4', 'Incluído por: ')
    ->setCellValue('B4', $incluido_por)
    ->setCellValue('A5', 'Filtros: ')
    ->setCellValue('B5', $desc_filtro)
    ->setCellValue('B6', 'Animais Pesados: ' . $animais_pesados)
    ->setCellValue('D6', 'Peso Total Kg: ' . number_format($peso_kg, 2, ',', '.'))
    ->setCellValue('F6', 'Peso Total Arrobas: ' . number_format($peso_arroba, 2, ',', '.'))
    ->setCellValue('H6', 'Peso Médio Kg: ' . number_format($peso_medio_kg, 2, ',', '.'))
    ->setCellValue('J6', 'Peso Médio Arrobas: ' . number_format($peso_medio_arroba, 2, ',', '.'));

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A8", "Id")
    ->setCellValue("B8", "Pesagem")
    ->setCellValue("C8", "Ganho de Peso")
    ->setCellValue("D8", "Último Peso")
    ->setCellValue("E8", "Data Último Peso")
    ->setCellValue("F8", "Sexo")
    ->setCellValue("G8", "Nascimento")
    ->setCellValue("H8", "Apartação")
    ->setCellValue("I8", "Observação da Pesagem")
    ->setCellValue("J8", "Mãe")
    ->setCellValue("K8", "Categoria");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(14);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(16);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(24);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(16);

$spreadsheet->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A8:K8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('A8:K8')->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A8:K8')->getFill()->getStartColor()->setARGB('D6DBDF');

$spreadsheet->getActiveSheet()->getStyle('B5:K5')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B5:K5')->getFont()->setSize(10);

$spreadsheet->getActiveSheet()->setShowGridlines(true);

// Faixas de categoria por idade (mesma lógica de ler_pesagem_consulta.php)
$arrayCategorias = [];

$tab_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
    WHERE tab_registro_lixeira_categoria_idade='0'");

while ($reg_categoria = mysqli_fetch_object($tab_categoria)) {
    $arrayCategorias[] = [
        "idade_de" => $reg_categoria->tab_categoria_idade_de,
        "idade_ate" => $reg_categoria->tab_categoria_idade_ate,
    ];
}

$tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    INNER JOIN tbl_animais
            ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
    WHERE tbl_ite_pesagem_numero_id='$pesagem_id'
    ORDER BY tbl_animal_codigo_numerico ASC");

$num_rows = mysqli_num_rows($tbl_itens);

if ($num_rows != 0) {
    $linha = 8;

    while ($reg_itens = mysqli_fetch_object($tbl_itens)) {
        $codigo_animal = intval($reg_itens->tbl_ite_pesagem_codigo_animal);
        $peso = intval($reg_itens->tbl_ite_pesagem_peso);
        $sexo = utf8_encode($reg_itens->tbl_ite_pesagem_sexo);
        $apartacao = utf8_encode($reg_itens->tbl_ite_pesagem_criterio_apartacao);
        $observacao = utf8_encode($reg_itens->tbl_ite_pesagem_observacao);
        $mae = $reg_itens->tbl_ite_pesagem_mae;

        $data_nasc = $reg_itens->tbl_ite_pesagem_nascimento;
        $data_nasc = str_replace("/", "-", $data_nasc);
        $data_nasc = date('Y-m-d', strtotime($data_nasc));
        $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc);

        // Ganho de peso / último peso, mesma cascata de ler_pesagem_consulta.php
        $ultimo_peso = 0;
        $data_ultimo_peso = 0;

        if ($reg_itens->tbl_ite_pesagem_ultimo_peso == '' || $reg_itens->tbl_ite_pesagem_ultimo_peso == 0) {
            $diferenca_peso = (int)$reg_itens->tbl_ite_pesagem_peso - (int)$reg_itens->tbl_animal_ultimo_peso;

            if ($reg_itens->tbl_animal_ultimo_peso != 0 && $reg_itens->tbl_animal_ultimo_peso != '') {
                $ultimo_peso = (int)$reg_itens->tbl_animal_ultimo_peso;
                $data_ultimo_peso = $reg_itens->tbl_animal_data_ultimo;
            }
            else if ($reg_itens->tbl_animal_peso_desmama != 0 && $reg_itens->tbl_animal_peso_desmama != '') {
                $ultimo_peso = (int)$reg_itens->tbl_animal_peso_desmama;
                $data_ultimo_peso = $reg_itens->tbl_animal_data_desmama;
            }
            else if ($reg_itens->tbl_animal_primeiro_peso != 0 && $reg_itens->tbl_animal_primeiro_peso != '') {
                $ultimo_peso = (int)$reg_itens->tbl_animal_primeiro_peso;
                $data_ultimo_peso = $reg_itens->tbl_animal_data_primeiro_peso;
            }
        }
        else {
            $diferenca_peso = (int)$reg_itens->tbl_ite_pesagem_peso - (int)$reg_itens->tbl_ite_pesagem_ultimo_peso;
            $ultimo_peso = (int)$reg_itens->tbl_ite_pesagem_ultimo_peso;
            $data_ultimo_peso = $reg_itens->tbl_ite_pesagem_data_emissao;
        }

        if ($data_ultimo_peso != 0 && $data_ultimo_peso != '') {
            $data_ultimo_peso_dt = new DateTime($data_ultimo_peso);
            $data_ultimo_peso_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_ultimo_peso_dt->format('Y-m-d'));
        }
        else {
            $data_ultimo_peso_edi = '';
        }

        // Categoria por idade em meses
        $descricao_categoria = '';
        $idade_meses = 0;

        if ($data_nasc && $data_nasc != '0000-00-00') {
            $nascimento = new DateTime($data_nasc);
            $hoje = new DateTime();
            $intervalo = $hoje->diff($nascimento);
            $idade_meses = ($intervalo->y * 12) + $intervalo->m;
        }

        foreach ($arrayCategorias as $categoria) {
            if ($idade_meses >= $categoria['idade_de'] && $idade_meses <= $categoria['idade_ate']) {
                if ($categoria['idade_ate'] == 999999999) {
                    $descricao_categoria = '> 36 meses';
                }
                else {
                    $descricao_categoria = $categoria['idade_de'] . ' a ' . $categoria['idade_ate'] . ' meses';
                }
            }
        }

        $linha++;

        $spreadsheet->getActiveSheet()->getStyle('B' . $linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $spreadsheet->getActiveSheet()->getStyle('C' . $linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $spreadsheet->getActiveSheet()->getStyle('D' . $linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $spreadsheet->getActiveSheet()->getStyle('E' . $linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
        $spreadsheet->getActiveSheet()->getStyle('G' . $linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_animal);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $peso);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $diferenca_peso);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $ultimo_peso);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_ultimo_peso_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sexo);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $nascimento_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $apartacao);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $observacao);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $mae);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $descricao_categoria);

        $spreadsheet->getActiveSheet()->getStyle('A' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('B' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('C' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('D' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $spreadsheet->getActiveSheet()->getStyle('E' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('F' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('G' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('H' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('I' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('J' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('K' . $linha)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }
}

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Pesagem');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="pesagem_' . $pesagem_id . '.xlsx"');
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
