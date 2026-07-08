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

$data_sistema = date("d/m/Y");
$fazenda_id  = $_REQUEST['codigo_local'];
$lista_final = $_REQUEST['lista_final'];

$nome_relatorio = "Animais Pesados/Não Pesados";
$descricao_filtro = $_REQUEST['nome_fazenda'];
$descricao_lotes = '';

$spreadsheet->getActiveSheet()->mergeCells('A1:P1');
$spreadsheet->getActiveSheet()->mergeCells('Q1:R1');
$spreadsheet->getActiveSheet()->mergeCells('A2:E2');
$spreadsheet->getActiveSheet()->mergeCells('F2:R2');
$spreadsheet->getActiveSheet()->mergeCells('A3:R3');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue("Q1", "Data: " . $data_sistema)
    ->setCellValue("A2", "Filtro: " . $descricao_filtro)
    ->setCellValue("F2", "Lotes: " . $descricao_lotes);

$spreadsheet->getActiveSheet()->getStyle('A2:R2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('A2:R2')->getFont()->setSize(10);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A4", "Animais")
    ->setCellValue("D4", "Pesados")
    ->setCellValue("A5", "Id Animal")
    ->setCellValue("B5", "Pesagem")
    ->setCellValue("C5", "Ganho de Peso")
    ->setCellValue("D5", "Último Peso")
    ->setCellValue("E5", "Data Último Peso")
    ->setCellValue("F5", "Sexo")
    ->setCellValue("G5", "Nascimento")
    ->setCellValue("H5", "Lote")
    ->setCellValue("I5", "Apartação")
    ->setCellValue("J5", "Obs Pesagem")
    ->setCellValue("K5", "Idade (meses)")
    ->setCellValue("L5", "Categoria")
    ->setCellValue("M5", "Raca")
    ->setCellValue("N5", "Pelagem")
    ->setCellValue("O5", "Mae Id")
    ->setCellValue("P5", "Pai Id")
    ->setCellValue("Q5", "Descarte")
    ->setCellValue("R5", "Obs Cadastro");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(9);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(11);

$spreadsheet->getActiveSheet()->getStyle('A5:R5')->getFont()->setColor(new Color(Color::COLOR_BLACK));
$spreadsheet->getActiveSheet()->getStyle('A5:R5')->getFont()->setBold(true);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('Q1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$spreadsheet->getActiveSheet()->getStyle('A5:R5')->getAlignment()->setWrapText(true);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:R5')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A5:R5')->getAlignment()->setVertical($align);

$linha = 5;
$animais_pesados = 0;
$total_animais = 0;

if(empty($fazenda_id)){
    die("Parâmetros insuficientes.");
}

$desc_categoria = [];
$arrayCategorias = [];
$descricaoCategorias = [];

$sql = "SELECT * FROM tabela_categoria_idade 
    WHERE tab_registro_lixeira_categoria_idade='0'"; 
        
$rs = mysqli_query($conector,$sql); 

while ($fila = mysqli_fetch_object($rs)){
    $codigo_id = $fila->tab_codigo_categoria_idade;
    $idade_de = $fila->tab_categoria_idade_de;
    $idade_ate = $fila->tab_categoria_idade_ate;

    if ($idade_ate==999999999){
        array_push($desc_categoria, '> 36 m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
    else {
        array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
        $descricaoCategorias = [
            "id" => $codigo_id,
            "idade_de" => $idade_de,
            "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
}

$fazenda_id = $_GET['codigo_local'];
$lista_final = $_GET['lista_final']; // Recebe: 123,124,125

$codigos_pais = [];

$sql = mysqli_query($conector, "SELECT * from tbl_animais
    WHERE tbl_animal_codigo_fazenda = '$fazenda_id' AND 
          tbl_animal_ativo = 'S'");

while ($reg_animal = mysqli_fetch_object($sql)) {
    if ($reg_animal->tbl_animal_codigo_pai) {
        $codigos_pais[] = $reg_animal->tbl_animal_codigo_pai;
    }
}

$dados_pais_semem = [];
$dados_pais_animal = [];

if (!empty($codigos_pais)) {
    $sql_pais_semem = "SELECT tbl_semem_codigo_id, tbl_semem_nome FROM tbl_semem WHERE tbl_semem_codigo_id IN (" . implode(',', $codigos_pais) . ")";

$rs_pais_semem = mysqli_query($conector, $sql_pais_semem);

while ($reg_pai_semem = mysqli_fetch_object($rs_pais_semem)) {
    $dados_pais_semem[$reg_pai_semem->tbl_semem_codigo_id] = utf8_encode($reg_pai_semem->tbl_semem_nome);
}

$sql_pais_animal = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_pais) . ")";

$rs_pais_animal = mysqli_query($conector, $sql_pais_animal);

while ($reg_pai_animal = mysqli_fetch_object($rs_pais_animal)) {
    $dados_pais_animal[$reg_pai_animal->tbl_animal_codigo_id] = $reg_pai_animal->tbl_animal_codigo_alfa . ' ' . intval($reg_pai_animal->tbl_animal_codigo_numerico);
    }
}

// SQL para pegar a lista única de lotes
$sql_lotes = "SELECT GROUP_CONCAT(DISTINCT pesagem.tbl_pesagem_lote SEPARATOR ', ') AS todos_lotes
    FROM tbl_item_pesagem item
    INNER JOIN tbl_pesagem pesagem ON pesagem.tbl_pesagem_id = item.tbl_ite_pesagem_numero_id
    WHERE item.tbl_ite_pesagem_numero_id IN ($lista_final)";

$res_lotes = mysqli_query($conector, $sql_lotes);
$row_lotes = mysqli_fetch_assoc($res_lotes);

$descricao_lotes = $row_lotes['todos_lotes'] ? "Lotes: " . $row_lotes['todos_lotes'] : "Sem lote definido";

$sql = "SELECT 
    a.tbl_animal_codigo_id, 
    a.tbl_animal_codigo_alfa,
    a.tbl_animal_codigo_numerico,
    a.tbl_animal_sexo,
    a.tbl_animal_data_nascimento,
    a.tbl_animal_codigo_mae,
    a.tbl_animal_codigo_pai,
    a.tbl_animal_descarte_reproducao,
    a.tbl_animal_ultimo_peso,
    a.tbl_animal_data_ultimo,
    a.tbl_animal_peso_desmama,
    a.tbl_animal_data_desmama,
    a.tbl_animal_primeiro_peso,
    a.tbl_animal_data_primeiro_peso,
    a.tbl_animal_observacao,
    r.tab_descricao_raca,
    p.tab_descricao_pelagem,
    CONCAT(mae.tbl_animal_codigo_alfa, ' ', CAST(mae.tbl_animal_codigo_numerico AS UNSIGNED)) AS mae_identificacao, 
    item.tbl_ite_pesagem_peso, 
    item.tbl_ite_pesagem_criterio_apartacao,
    item.tbl_ite_pesagem_observacao,
    pesagem.tbl_pesagem_lote
    FROM tbl_animais a
    LEFT JOIN tabela_racas r ON a.tbl_animal_codigo_raca = r.tab_codigo_raca
    LEFT JOIN tabela_pelagens p ON a.tbl_animal_codigo_pelagem = p.tab_codigo_pelagem
    LEFT JOIN tbl_animais mae ON a.tbl_animal_codigo_mae = mae.tbl_animal_codigo_id
    LEFT JOIN tbl_item_pesagem item ON a.tbl_animal_codigo_id = item.tbl_ite_pesagem_codigo_id_animal 
        AND item.tbl_ite_pesagem_numero_id IN ($lista_final) -- Usa a lista enviada pelo JS
    LEFT JOIN tbl_pesagem pesagem ON pesagem.tbl_pesagem_id = item.tbl_ite_pesagem_numero_id
    WHERE 
        a.tbl_animal_codigo_fazenda = '$fazenda_id' 
        AND a.tbl_animal_ativo = 'S'
    ORDER BY 
        a.tbl_animal_codigo_numerico ASC";

$rs = mysqli_query($conector, $sql) or die(mysqli_error($conector)); 

$contagem_duplicados = [];
$dados_temporarios = [];

// Armazena os dados em um array temporário para não perder o ponteiro do MySQL
while ($row = mysqli_fetch_array($rs)) {
    $dados_temporarios[] = $row;
    $id_temp = $row['tbl_animal_codigo_id'];
    if (!isset($contagem_duplicados[$id_temp])) {
        $contagem_duplicados[$id_temp] = 0;
    }
    $contagem_duplicados[$id_temp]++;
}

foreach ($dados_temporarios as $reg) {
//while ($reg = mysqli_fetch_array($rs)) {
    $id_animal = ltrim($reg['tbl_animal_codigo_id'], '0');
    $codigo_alfa = $reg['tbl_animal_codigo_alfa'];
    $codigo_numerico = $reg['tbl_animal_codigo_numerico'];
    $sexo      = ($reg['tbl_animal_sexo']=='M') ? 'Macho' : 'Fêmea';
    $raca      = utf8_encode($reg['tab_descricao_raca']);
    $pelagem   = utf8_encode($reg['tab_descricao_pelagem']);
    $mae_alfa  = $reg['mae_identificacao']; 
    $obs_cadastro = utf8_encode($reg['tbl_animal_observacao']);
    $id_pai_animal = $reg['tbl_animal_codigo_pai'];
    $pai_alfa = '';

    if (isset($dados_pais_semem[$id_pai_animal])) {
        $pai_alfa = $dados_pais_semem[$id_pai_animal];
    } 
    elseif (isset($dados_pais_animal[$id_pai_animal])) {
        $pai_alfa = $dados_pais_animal[$id_pai_animal];
    }

    $descarte  = ($reg['tbl_animal_descarte_reproducao'] == 'S') ? 'Sim' : '';    
    $peso = $reg['tbl_ite_pesagem_peso'];
    $lote = utf8_encode($reg['tbl_pesagem_lote']);
    $apartacao = utf8_encode($reg['tbl_ite_pesagem_criterio_apartacao']);
    $obs_pesagem = utf8_encode($reg['tbl_ite_pesagem_observacao']);

    $ultimo_peso = 0; 
    $data_ultimo_peso = 0;

    if ($reg['tbl_animal_ultimo_peso']!=0 && 
        $reg['tbl_animal_ultimo_peso']!='') {
        $ultimo_peso = (int)$reg['tbl_animal_ultimo_peso'];
        $data_ultimo_peso = $reg['tbl_animal_data_ultimo'];
    }
    else if ($reg['tbl_animal_peso_desmama']!=0 && 
        $reg['tbl_animal_peso_desmama']!='') {
        $ultimo_peso = (int)$reg['tbl_animal_peso_desmama'];
        $data_ultimo_peso = $reg['tbl_animal_data_desmama'];
    }
    else if ($reg['tbl_animal_primeiro_peso']!=0 && 
        $reg['tbl_animal_primeiro_peso']!=''){
        $ultimo_peso = (int)$reg['tbl_animal_primeiro_peso'];
        $data_ultimo_peso = $reg['tbl_animal_data_primeiro_peso'];
    }

    if ($peso!=0 && $peso!='') {
        $diferencaPeso = $peso - $ultimo_peso;
    }
    else {
        $diferencaPeso = '';
    }
    
    $data_ultimo_peso_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_ultimo_peso);

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

    for($i = 0; $i < count($arrayCategorias); $i++){
        $id_categoria = $arrayCategorias[$i]['id'];
        $idade_de = $arrayCategorias[$i]['idade_de'];
        $idade_ate = $arrayCategorias[$i]['idade_ate'];

        if ($idade_meses >= $idade_de && $idade_meses <= $idade_ate) {
            $codigo_categoria = $id_categoria;

            if ($idade_ate==999999999) {
                $descricao_categoria=' > 36 meses';
            }
            else {
                $descricao_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
            }
        }
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

    $id_animal_original = $reg['tbl_animal_codigo_id'];
    $linha++;

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_alfa_numerico);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $exibir_peso);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $diferencaPeso);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $ultimo_peso);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_ultimo_peso_edi);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $sexo);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $nascimento_edi);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $lote);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $apartacao);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $obs_pesagem);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $idade_meses);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_categoria);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $raca);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $pelagem);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $mae_alfa);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $pai_alfa);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $descarte);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(18, $linha, $obs_cadastro);

    if ($contagem_duplicados[$id_animal_original] > 1) {
        $spreadsheet->getActiveSheet()->getStyle('A' . $linha . ':R' . $linha)
            ->getFont()->setColor(new Color(Color::COLOR_RED));
    }
}

$ultima_linha = $linha;

$intervalo_geral = 'A5:G' . $ultima_linha;
$spreadsheet->getActiveSheet()->getStyle($intervalo_geral)
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$intervalo_geral = 'K5:L' . $ultima_linha;
// Alinhamento central para tudo de uma vez
$spreadsheet->getActiveSheet()->getStyle($intervalo_geral)
    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->getStyle('E5:E' . $ultima_linha)
    ->getNumberFormat()->setFormatCode('DD/MM/YYYY');

$spreadsheet->getActiveSheet()->getStyle('G5:G' . $ultima_linha)
    ->getNumberFormat()->setFormatCode('DD/MM/YYYY');

// Cor vermelha para a coluna K inteira
$spreadsheet->getActiveSheet()->getStyle('Q6:Q' . $ultima_linha)
    ->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));

$spreadsheet->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 2, $descricao_lotes);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 4, $total_animais);
$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, 4, $animais_pesados);

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