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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Instanciamos a classe
$spreadsheet = new Spreadsheet();

@session_start();
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

$codigo_alfa_filtro = $_REQUEST['codigo_alfa'];
$codigo_numerico_filtro = $_REQUEST['codigo_numerico']; 
$local_filtro = $_REQUEST["local"];
$origem_filtro = $_REQUEST["origem"];
$raca_filtro = $_REQUEST["raca"];
$categoria_filtro = $_REQUEST["categoria"];
$pai_filtro = $_REQUEST["pai"];
$mae_filtro = $_REQUEST["mae"];
$sexo_filtro = $_REQUEST["sexo"];

if (isset($_REQUEST['estacao'])) {
    $estacao_filtro = $_REQUEST['estacao'];

    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
        WHERE tbl_par_estacao_nome='$estacao_filtro'
        ORDER BY tbl_par_estacao_id ASC");  

    $num_rows = mysqli_num_rows($sql);
    $array_estacao = array();

    if ($num_rows!=0) {
        while ($reg_estacao = mysqli_fetch_object($sql)){
            $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
            $array_estacao[] = $codigo_estacao;
        }

        $array_estacao = implode(',', $array_estacao);
    }
}

$westacao = "";
if (!empty ($array_estacao)) {
    
    $array_estacao = explode(',', $array_estacao);

    $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
    $westacao.= implode(',', $array_estacao);
    $westacao.= ")";
}

$local = array();
$matriz_itens = explode(",", $local_filtro);
$quantidade_itens = count($matriz_itens);

for ($i = 0; $i < $quantidade_itens; $i++) {
    $local[$i] = $matriz_itens[$i];
}

$local = implode(',', $local);
$local = substr($local, 0, -1);

$wlocal = '';

if ($local_filtro != '') {
    $wlocal = " AND tbl_animal_codigo_fazenda IN(";
    $wlocal .= $local;
    $wlocal .= ")";
}

$origem = array();
$matriz_itens = explode(",", $origem_filtro);
$quantidade_itens = count($matriz_itens);

for ($i = 0; $i < $quantidade_itens; $i++) {
    $origem[$i] = $matriz_itens[$i];
}

$origem = implode(',', $origem);
$origem = substr($origem, 0, -1);

$worigem = '';

if ($origem_filtro != '') {
    $worigem = " AND tbl_animal_codigo_origem IN(";
    $worigem .= $origem;
    $worigem .= ")";
}

$wsexo = '';
if ($sexo_filtro == 'Todos') {
    $wsexo = '';
} else {
    $wsexo = " AND tbl_animal_sexo IN(";
    $wsexo .= "'" . $sexo_filtro . "'";
    $wsexo .= ")";
}

$raca = array();
$matriz_itens = explode(",", $raca_filtro);
$quantidade_itens = count($matriz_itens);

for ($i = 0; $i < $quantidade_itens; $i++) {
    $raca[$i] = $matriz_itens[$i];
}

$raca = implode(',', $raca);
$raca = substr($raca, 0, -1);

$wraca = '';

if ($raca_filtro != '') {
    $wraca = " AND tbl_animal_codigo_raca IN(";
    $wraca .= $raca;
    $wraca .= ")";
}

$categoria = array();
$matriz_itens = explode(",", $categoria_filtro);
$quantidade_categoria = count($matriz_itens);

for ($i = 0; $i < $quantidade_categoria; $i++) {
    $categoria[$i] = $matriz_itens[$i];
}

$categoria = implode(',', $categoria);
$categoria = substr($categoria, 0, -1);
$quantidade_categoria--;

$wcategoria = '';

if ($categoria_filtro != '') {
    $wcategoria = explode(",", $categoria);
}

$pai = array();
$matriz_itens = explode(",", $pai_filtro);
$quantidade_itens = count($matriz_itens);

for ($i = 0; $i < $quantidade_itens; $i++) {
    $pai[$i] = $matriz_itens[$i];
}

$pai = implode(',', $pai);
$pai = substr($pai, 0, -1);

$wpai = '';

if ($pai_filtro != '') {
    $wpai = " AND tbl_animal_codigo_pai IN(";
    $wpai .= $pai;
    $wpai .= ")";
}

$mae = array();
$matriz_itens = explode(",", $mae_filtro);
$quantidade_itens = count($matriz_itens);

for ($i = 0; $i < $quantidade_itens; $i++) {
    $mae[$i] = $matriz_itens[$i];
}

$mae = implode(',', $mae);
$mae = substr($mae, 0, -1);

$wmae = '';

if ($mae_filtro != '') {
    $wmae = " AND tbl_animal_codigo_mae IN(";
    $wmae .= $mae;
    $wmae .= ")";
}

$peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
$peso_nasc_final = $_REQUEST["peso_nasc_final"];

$peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
$peso_desmama_final = $_REQUEST["peso_desmama_final"];

$peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
$peso_ult_final = $_REQUEST["peso_ult_final"];

if ($peso_nasc_inicial == 0 && $peso_nasc_final == 0) {
    $wpeso_nasc = '';
} else {
    $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
}

if ($peso_desmama_inicial == 0 && $peso_desmama_final == 0) {
    $wpeso_desmama = '';
} else {
    $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
}

if ($peso_ult_inicial == 0 && $peso_ult_final == 0) {
    $wpeso_ult = '';
} else {
    $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
}

$data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
$data_nasc_final = $_REQUEST["data_nasc_final"];

if ($data_nasc_inicial == 0 && $data_nasc_final == 0) {
    $wdata_nasc = '';
} else {
    $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
}

$wativo = $_REQUEST['ativo'];

$tipo_rel = $_REQUEST["tipo_rel"];
$descricao_filtro = $_REQUEST["descricao_filtro"];

if ($tipo_rel == "C") {
    $desc_tipo_rel = 'Completa';
} else {
    $desc_tipo_rel = 'Resumida';
}

$solteiras = $_REQUEST["solteiras"];
$descarte = $_REQUEST["descarte"];
$paridas = $_REQUEST["paridas"];
$data_paridas_ate = $_REQUEST["data_paridas"];
$parto = $_REQUEST["parto"];
$num_parto_de = $_REQUEST['num_parto_de'];
$num_parto_ate = $_REQUEST['num_parto_ate'];
$aborto = $_REQUEST["aborto"];
$num_aborto_de = $_REQUEST['num_aborto_de'];
$num_aborto_ate = $_REQUEST['num_aborto_ate'];
$previsao_parto_de = $_REQUEST['previsao_parto_de'];
$previsao_parto_ate = $_REQUEST['previsao_parto_ate'];
$positivo = $_REQUEST['positivo'];
$negativo = $_REQUEST['negativo'];

if ($_REQUEST["solteiras"]=='' && $_REQUEST["paridas"]=='') {
    $solteiras='';
    $paridas='';
}
else {
    if ($solteiras=='') {
        $solteiras='N';
    }

    if ($paridas=='') {
        $paridas='N';
    }
}

if ($data_paridas_ate == '') {
    $data_paridas_ate = '9999-99-99';
    $data_paridas_de = '0000-00-00';
} else {
    $data_paridas_de = date("Y-m-d", strtotime('-8 month', strtotime($data_paridas_ate)));
    $data_paridas_de = date("Y-m-d", strtotime('-1 day', strtotime($data_paridas_de)));
}

if ($previsao_parto_de == '') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-99-99';
}

$vaca_solteira = '';
$vaca_parida = '';
$vaca_descarte = '';
$tem_parto = '';
$tem_aborto = '';
$tem_previsao_parto = '';
$ultimo_parto = '0000-00-00';
$data_previsao_parto = '0000-00-00';
$tem_positivo = '';
$tem_negativo = '';

@session_start();

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj >='$cnpj_empresa'");
$registro_empresa = mysqli_fetch_object($empresa);
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$nome_relatorio = "Listagem de Animais - " . $desc_tipo_rel;


if ($tipo_rel == "C") {
    $spreadsheet->getActiveSheet()->mergeCells('A1:K1');
    $spreadsheet->getActiveSheet()->mergeCells('L1:N1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:N2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:N3');
    $spreadsheet->getActiveSheet()->mergeCells('B4:I5');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue("L1", "Data: " . $data_sistema)
        ->setCellValue("A2", "Filtro: ")
        ->setCellValue("B2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4", "Animais")
        ->setCellValue("J4", "Peso Nasc Medio")
        ->setCellValue("K4", "Peso Desmama Medio")
        ->setCellValue("L4", "Peso Medio")
        ->setCellValue("M4", "Peso Total")

        ->setCellValue("A6", "Id Animal")
        ->setCellValue("B6", "Fazenda")
        ->setCellValue("C6", "Sexo")
        ->setCellValue("D6", "Nascimento")
        ->setCellValue("E6", "Raca")
        ->setCellValue("F6", "Pelagem")
        ->setCellValue("G6", "Mae Id")
        ->setCellValue("H6", "Pai Id")
        ->setCellValue("I6", "Observação")
        ->setCellValue("J6", "Peso Nasc Kg")
        ->setCellValue("K6", "Peso Desmama Kg")
        ->setCellValue("L6", "Peso Atual Kg")
        ->setCellValue("M6", "Ultima Pesagem")
        ->setCellValue("N6", "Descarte");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(22);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(7);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(9);

    $spreadsheet->getActiveSheet()->getStyle('A6:N6')->getFont()->setColor(new Color(Color::COLOR_BLACK));
    $spreadsheet->getActiveSheet()->getStyle('A6:N6')->getFont()->setBold(true);

    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('J4:N4')->getAlignment()->setWrapText(true);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('J4:N4')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('J4:N4')->getAlignment()->setVertical($align);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A6:I6')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A6:I6')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('J6:N6')->getAlignment()->setWrapText(true);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('J6:N6')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('J6:N6')->getAlignment()->setVertical($align);
} else {
    $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
    $spreadsheet->getActiveSheet()->mergeCells('E1:F1');
    $spreadsheet->getActiveSheet()->mergeCells('B2:F2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:F3');
    $spreadsheet->getActiveSheet()->mergeCells('B4:F5');

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', $nome_relatorio)
        ->setCellValue("E1", "Data: " . $data_sistema)
        ->setCellValue("A2", "Filtro: ")
        ->setCellValue("B2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4", "Animais")
        ->setCellValue("A6", "Id Animal")
        ->setCellValue("B6", "Id Animal")
        ->setCellValue("C6", "Id Animal")
        ->setCellValue("D6", "Id Animal")
        ->setCellValue("E6", "Id Animal")
        ->setCellValue("F6", "Id Animal");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);

    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$linha = 6;
    if ($codigo_alfa_filtro!='' || $codigo_numerico_filtro!='') {
        $sql = "SELECT * from tbl_animais 
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_filtro' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_filtro'";
    }
    else {
        $sql = "SELECT * from tbl_animais 
            WHERE tbl_animal_lixeira=0 AND 
                  tbl_animal_ativo='$wativo'" . $wlocal . $worigem . $wsexo . 
                  $wraca . $wpai . $wmae . $wpeso_nasc . $wpeso_desmama . 
                  $wpeso_ult . $wdata_nasc .
            " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"; 
    }

                $rs = mysqli_query($conector, $sql); 
                $num_rows_animais = mysqli_num_rows($rs);

                $total_peso_nasc = 0;
                $qtd_peso_nasc = 0;
                $total_peso_ultimo = 0;
                $qtd_peso_ultimo = 0;
                $animais_listados = 0;
                $total_peso_desmama = 0;
                $qtd_peso_desmama = 0;
                $coluna_exibida = 1;
                $coluna_1= '';
                $coluna_2= '';
                $coluna_3= '';
                $coluna_4= '';
                $coluna_5= '';
                $coluna_6= '';

                if ($num_rows_animais!=0){
                    while ($reg_animal = mysqli_fetch_object($rs)){
                        $codigo = $reg_animal->tbl_animal_codigo_id;
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
                        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;

                        if ($reg_animal->tbl_animal_sexo=='M') {
                            $sexo = 'Macho';
                        }
                        else {
                            $sexo = 'Femea';
                        }

                        $mae = $reg_animal->tbl_animal_codigo_mae; 
                        $pai = $reg_animal->tbl_animal_codigo_pai; 
                        $ativo = $reg_animal->tbl_animal_ativo; 
                        $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

                        if ($animal_descarte=='S') {
                            $animal_descarte = 'Sim';
                        }
                        else {
                            $animal_descarte = '';   
                        }

                        $tem_negativo = '';
                        $tem_positivo = '';
                        $vaca_descarte = '';

                        if ($descarte=='S') {
                            if ($animal_descarte=='Sim') {
                                $vaca_descarte = 'S';
                            }
                        }

                        $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
                        $data_nasc_edi = $data_nasc->format('d/m/Y');
                        $data_nasc_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_nasc_edi);
                        $peso_nasc = $reg_animal->tbl_animal_primeiro_peso; 
                        $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                        $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                        $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                        $peso_ultimo = $reg_animal->tbl_animal_ultimo_peso; 
                        $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                        $data_ultimo = new DateTime($reg_animal->tbl_animal_data_ultimo);
                        $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                        $observacao = ltrim($reg_animal->tbl_animal_observacao); 
                        $observacao = rtrim($observacao); 
                        $observacao = utf8_encode($observacao);

                        if ($codigo_alfa=='') {
                            $codigo_edi = intval($codigo_numerico);
                        }
                        else {
                            $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
                        }

                        $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                        $num_rows = mysqli_num_rows($tab_fazenda);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_fazenda);
                            $desc_local = $reg->tbl_pessoa_nome;
                        }
                        else {
                            $desc_local = '';
                        }

                        $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
                        $num_rows = mysqli_num_rows($tab_mae);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_mae);
                            if ($reg->tbl_animal_codigo_alfa=='') {
                                $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                            }
                            else {
                                $descricao_mae = $reg->tbl_animal_codigo_alfa. '-' . intval($reg->tbl_animal_codigo_numerico);
                            }
                        }
                        else {
                            $descricao_mae = '';
                        }

                        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            $descricao_pai = $reg->tbl_semem_codigo_alfa;
                            $pai = $reg->tbl_semem_codigo_id;
                        }
                        else {
                            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                            $num_rows = mysqli_num_rows($tab_pai);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_pai);
                                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_pai = '';
                            }
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

                        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                        $data_baixa = $reg_animal->tbl_animal_baixado_em;

                        if ($data_baixa!='') {
                            $data_acompanhamento_calculo = date($data_baixa);
                        }
                        else {
                            $data_acompanhamento_calculo = date("Y-m-d");
                        }

                        $date = new DateTime($data_nascimento); // Data de Nascimento
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

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

                        // verifica a cobertura do animal
                        $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_item_cobertura
                            INNER JOIN tbl_cobertura
                                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                            INNER JOIN tbl_parametro_estacao_monta
                                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                            WHERE tbl_cobertura_lixeira=0 AND 
                                      tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
                            ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            $reg_cobertura = mysqli_fetch_object($sql);
                            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                            $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                            $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
                        }
                        else {
                            $codigo_local = 0;
                            $estacao_animal = 0;
                            $estacao_monta = '';
                        }

                        // verifica vacas solteiras
                        if ($solteiras=='S' || $paridas=='S') {
                            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_mae='$codigo'
                                ORDER BY tbl_animal_data_nascimento DESC limit 1");

                            $ultimo_filho = mysqli_num_rows($tbl_filhos);

                            if ($ultimo_filho!=0) {
                                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                                $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 

                                $data_acompanhamento_calculo = date("Y-m-d");
                                $date = new DateTime($ultimo_parto); // Data de Nascimento
                                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                                $idade_ano = $idade_acompanhamento->format('%Y');
                                $idade_mes = $idade_acompanhamento->format('%m');
                                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                                if ($idade < 8) {
                                    $vaca_parida = 'S';
                                    $vaca_solteira = '';
                                }
                                else {
                                    $vaca_solteira = 'S';
                                    $vaca_parida = '';
                                }
                            }
                            else {
                                $ultimo_parto = '0000-00-00';
                            }
                        }

                        // verifica partos
                        if ($sexo == 'Femea' && $num_parto_de!='' && $num_parto_ate!='') {
                            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_mae='$codigo'");

                            $num_partos = mysqli_num_rows($tbl_filhos);

                            if ($num_partos>=$num_parto_de && 
                                $num_partos<=$num_parto_ate && $idade_animal>=8) {
                                $tem_parto = "S";
                            }
                            else {
                                $tem_parto = "";
                            }
                        }

                        // verifica abortos
                        if ($sexo == 'Femea' && $num_aborto_de!='' && $num_aborto_ate!='') {
                            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                                      (tbl_mov_estoque_entrada_saida='A' OR 
                                       tbl_mov_estoque_entrada_saida='S') AND 
                                      (tbl_mov_estoque_tipo_movimentacao='M' OR
                                       tbl_mov_estoque_tipo_movimentacao='A' OR
                                       tbl_mov_estoque_tipo_movimentacao='B')");

                            $num_natimorto = mysqli_num_rows($tbl_natimorto);

                            if ($num_natimorto>=$num_aborto_de && 
                                $num_natimorto<=$num_aborto_ate) {
                                $tem_aborto = "S";
                            }
                            else {
                                $tem_aborto = "";
                            }
                        }       

                        // Verifica previsão de parto
                        if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_cobertura_lixeira=0 AND
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P'
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows_coberturas!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                                $cobertura_id = $reg_cobertura->tbl_cobertura_id;

                                $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

                                $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                                        WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                                $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                                $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                                    WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                                          tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                                    ORDER BY tbl_ite_protocoloiatf_id ASC");

                                $dias_previsao_parto = 282;

                                while($reg_itens_iatf = mysqli_fetch_object($sql)){
                                    $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                                    $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                                    $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                                }
                            }
                            else {
                                $data_previsao_parto = '0000-00-00';
                            }
                        }

                        // verifica natimortos, nascidos ou abortos na estacao

                        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_cobertura_lixeira=0 AND
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                        $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

                        if ($num_rows_item!=0) {
                            $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);

                            $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
                        }
                        else {
                            $nascido_aborto = '';
                        }
                        
                        // Verifica diagnostico
                        if ($positivo=='S' || $negativo=='S'){
                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_cobertura_lixeira=0 AND
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C'  AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                            $num_rows = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                                if ($diagnostico=='P'){
                                    $tem_positivo = 'S';
                                    $tem_negativo = '';
                                } 
                                else if ($diagnostico=='N') {
                                    $tem_negativo = 'S';
                                    $tem_positivo = '';
                                }
                                else {
                                    $tem_negativo = '';
                                    $tem_positivo = '';
                                }
                            }
                            else {
                                $tem_negativo = '';
                                $tem_positivo = '';
                            }
                        }
                        else {
                            $tem_negativo = '';
                            $tem_positivo = '';
                        }


                        if ($positivo=='S' AND 
                            $nascido_aborto!='') {
                            $tem_positivo='';
                        }

                        if ($data_previsao_parto!='0000-00-00' AND 
                            $nascido_aborto!='') {
                            $data_previsao_parto='0000-00-00';
                        }

        if ($tipo_rel == 'C') {
            if (
                $wcategoria == "" &&
                $descarte == $vaca_descarte &&
                $data_previsao_parto >= $previsao_parto_de &&
                $data_previsao_parto <= $previsao_parto_ate &&

                (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                $nascido=='M' || $nascido=='O'))) || 
                ($paridas==$vaca_parida && 
                $ultimo_parto>=$data_paridas_de && 
                $ultimo_parto<=$data_paridas_ate)) &&

                $parto == $tem_parto &&
                $aborto == $tem_aborto &&
                $positivo == $tem_positivo &&
                $negativo == $tem_negativo
            ) {

                // AJUSTE DO PESO DE DESMAMA
                if ($peso_desmama != '' && $peso_desmama != 0) {
                    if ($peso_nasc == '' || $peso_nasc == 0) {
                        $peso_nasc = 30;
                        $peso_nasc_edi = number_format($peso_nasc, 2, ',', '.');
                    }

                    $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                    $data_final = $reg_animal->tbl_animal_data_desmama;
                    $diferenca = strtotime($data_final) -
                        strtotime($data_inicial);
                    $dias = floor($diferenca / (60 * 60 * 24));
                    $diferenca_peso = $peso_desmama - $peso_nasc;
                    $gmd = $diferenca_peso / $dias;

                    $peso_desmama = $peso_nasc + ($gmd * 205);
                    $peso_desmama_edi = number_format($peso_desmama, 2, ',', '.');
                }
                // FIM AJUSTE DO PESO DE DESMAMA

                if ($peso_nasc != '' && $peso_nasc != 0) {
                    $total_peso_nasc += $peso_nasc;
                    $qtd_peso_nasc++;
                }

                if ($peso_desmama != '' && $peso_desmama != 0) {
                    $total_peso_desmama += $peso_desmama;
                    $qtd_peso_desmama++;
                }

                // ULTIMO PESO
                if ($peso_ultimo == '' || $peso_ultimo == 0) {
                    if ($peso_desmama != '' && $peso_desmama != 0) {
                        $peso_ultimo = $peso_desmama;
                        $data_ultimo = new DateTime($reg_animal->tbl_animal_data_desmama);
                        $peso_ultimo_edi = number_format($peso_ultimo, 2, ',', '.');
                        $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                        $data_ultimo_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_ultimo_edi);
                        $total_peso_ultimo += $peso_ultimo;
                        $qtd_peso_ultimo++;
                    } else if ($peso_nasc != '' && $peso_nasc != 0) {
                        $peso_ultimo = $peso_nasc;
                        $data_ultimo = new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                        $peso_ultimo_edi = number_format($peso_ultimo, 2, ',', '.');
                        $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                        $data_ultimo_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_ultimo_edi);
                        $total_peso_ultimo += $peso_ultimo;
                        $qtd_peso_ultimo++;
                    } else {
                        $data_ultimo_edi = '';
                    }
                } else {
                    $total_peso_ultimo += $peso_ultimo;
                    $qtd_peso_ultimo++;
                }
                // FIM ULTIMO PESO
                $linha++;
                $celulas = 'A' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'G' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'J' . $linha . ':M' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $celulas = 'C' . $linha . ':D' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $celulas = 'I' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

                $celulas = 'A' . $linha . ':N' . $linha;
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                if ($codigo_alfa == '') {
                    $celulas = 'A' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                }

                $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_local);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_raca);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_pelagem);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_mae);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $descricao_pai);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $observacao);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_nasc);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $peso_ultimo);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $data_ultimo_edi);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $animal_descarte);
                $celulas = 'M' . $linha;
                $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

                $animais_listados++;
            } else {
                for ($k = 0; $k < $quantidade_categoria; $k++) {
                    $value = $wcategoria[$k];
                    if (
                        $value == $codigo_categoria &&
                        $descarte == $vaca_descarte &&
                        $data_previsao_parto >= $previsao_parto_de &&
                        $data_previsao_parto <= $previsao_parto_ate &&

                        (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                        $nascido=='M' || $nascido=='O'))) || 
                        ($paridas==$vaca_parida && 
                        $ultimo_parto>=$data_paridas_de && 
                        $ultimo_parto<=$data_paridas_ate)) &&

                        $parto == $tem_parto &&
                        $aborto == $tem_aborto &&
                        $positivo == $tem_positivo &&
                        $negativo == $tem_negativo
                    ) {

                        // AJUSTE DO PESO DE DESMAMA
                        if ($peso_desmama != '' && $peso_desmama != 0) {
                            if ($peso_nasc == '' || $peso_nasc == 0) {
                                $peso_nasc = 30;
                                $peso_nasc_edi = number_format($peso_nasc, 2, ',', '.');
                            }

                            $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                            $data_final = $reg_animal->tbl_animal_data_desmama;
                            $diferenca = strtotime($data_final) -
                                strtotime($data_inicial);
                            $dias = floor($diferenca / (60 * 60 * 24));

                            $diferenca_peso = $peso_desmama - $peso_nasc;
                            $gmd = $diferenca_peso / $dias;
                            $peso_desmama = $peso_nasc + ($gmd * 205);
                            $peso_desmama_edi = number_format($peso_desmama, 2, ',', '.');
                        }
                        // FIM AJUSTE DO PESO DE DESMAMA

                        if ($peso_nasc != '' && $peso_nasc != 0) {
                            $total_peso_nasc += $peso_nasc;
                            $qtd_peso_nasc++;
                        }

                        if ($peso_desmama != '' && $peso_desmama != 0) {
                            $total_peso_desmama += $peso_desmama;
                            $qtd_peso_desmama++;
                        }

                        // ULTIMO PESO
                        if ($peso_ultimo == '' || $peso_ultimo == 0) {
                            if ($peso_desmama != '' && $peso_desmama != 0) {
                                $peso_ultimo = $peso_desmama;
                                $data_ultimo = new DateTime($reg_animal->tbl_animal_data_desmama);
                                $peso_ultimo_edi = number_format($peso_ultimo, 2, ',', '.');
                                $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                $data_ultimo_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_ultimo_edi);
                                $total_peso_ultimo += $peso_ultimo;
                                $qtd_peso_ultimo++;
                            } else if ($peso_nasc != '' && $peso_nasc != 0) {
                                $peso_ultimo = $peso_nasc;
                                $data_ultimo = new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                                $peso_ultimo_edi = number_format($peso_ultimo, 2, ',', '.');
                                $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                $data_ultimo_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_ultimo_edi);
                                $total_peso_ultimo += $peso_ultimo;
                                $qtd_peso_ultimo++;
                            } else {
                                $data_ultimo_edi = '';
                            }
                        } else {
                            $total_peso_ultimo += $peso_ultimo;
                            $qtd_peso_ultimo++;
                        }
                        // FIM ULTIMO PESO
                        $linha++;
                        $celulas = 'A' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $celulas = 'G' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $celulas = 'J' . $linha . ':M' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $celulas = 'C' . $linha . ':D' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $spreadsheet->getActiveSheet()->getStyle('D'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                        $celulas = 'I' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setWrapText(true);

                        $celulas = 'A' . $linha . ':N' . $linha;
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal($align);
                        $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setVertical($align);

                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_local);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $sexo);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $data_nasc_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_raca);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_pelagem);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_mae);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $descricao_pai);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $observacao);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $peso_nasc);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $peso_desmama);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $peso_ultimo);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $data_ultimo_edi);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $animal_descarte);
                        $celulas = 'M' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
                        $spreadsheet->getActiveSheet()->getStyle('N'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

                        $animais_listados++;
                    }
                }
            }
        } else {
            if (
                $wcategoria == "" &&
                $descarte == $vaca_descarte &&
                $data_previsao_parto >= $previsao_parto_de &&
                $data_previsao_parto <= $previsao_parto_ate &&

                (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                $nascido=='M' || $nascido=='O'))) || 
                ($paridas==$vaca_parida && 
                $ultimo_parto>=$data_paridas_de && 
                $ultimo_parto<=$data_paridas_ate)) &&

                $parto == $tem_parto &&
                $aborto == $tem_aborto &&
                $positivo == $tem_positivo &&
                $negativo == $tem_negativo
            ) {

                $animais_listados++;
                if ($coluna_exibida <= 6) {
                    if ($coluna_exibida == 1) {
                        $coluna_1 = $codigo_edi;
                        $coluna_exibida++;
                    } else if ($coluna_exibida == 2) {
                        $coluna_2 = $codigo_edi;
                        $coluna_exibida++;
                    } else if ($coluna_exibida == 3) {
                        $coluna_3 = $codigo_edi;
                        $coluna_exibida++;
                    } else if ($coluna_exibida == 4) {
                        $coluna_4 = $codigo_edi;
                        $coluna_exibida++;
                    } else if ($coluna_exibida == 5) {
                        $coluna_5 = $codigo_edi;
                        $coluna_exibida++;
                    } else if ($coluna_exibida == 6) {
                        $coluna_6 = $codigo_edi;
                        $coluna_exibida++;
                    }
                } else {
                    $linha++;
                    $celulas = 'A' . $linha . ':F' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $coluna_1);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $coluna_2);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $coluna_3);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $coluna_4);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $coluna_5);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $coluna_6);
                    $coluna_exibida = 1;
                    $coluna_1 = '';
                    $coluna_2 = '';
                    $coluna_3 = '';
                    $coluna_4 = '';
                    $coluna_5 = '';
                    $coluna_6 = '';
                    $coluna_1 = $codigo_edi;
                    $coluna_exibida++;
                }
            } else {
                for ($k = 0; $k < $quantidade_categoria; $k++) {
                    $value = $wcategoria[$k];
                    if (
                        $value == $codigo_categoria &&
                        $descarte == $vaca_descarte &&
                        $data_previsao_parto >= $previsao_parto_de &&
                        $data_previsao_parto <= $previsao_parto_ate &&

                        (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                            $nascido=='M' || $nascido=='O'))) || 
                        ($paridas==$vaca_parida && 
                        $ultimo_parto>=$data_paridas_de && 
                        $ultimo_parto<=$data_paridas_ate)) &&

                        $parto == $tem_parto &&
                        $aborto == $tem_aborto &&
                        $positivo == $tem_positivo &&
                        $negativo == $tem_negativo
                    ) {

                        $animais_listados++;
                        if ($coluna_exibida <= 6) {
                            if ($coluna_exibida == 1) {
                                $coluna_1 = $codigo_edi;
                                $coluna_exibida++;
                            } else if ($coluna_exibida == 2) {
                                $coluna_2 = $codigo_edi;
                                $coluna_exibida++;
                            } else if ($coluna_exibida == 3) {
                                $coluna_3 = $codigo_edi;
                                $coluna_exibida++;
                            } else if ($coluna_exibida == 4) {
                                $coluna_4 = $codigo_edi;
                                $coluna_exibida++;
                            } else if ($coluna_exibida == 5) {
                                $coluna_5 = $codigo_edi;
                                $coluna_exibida++;
                            } else if ($coluna_exibida == 6) {
                                $coluna_6 = $codigo_edi;
                                $coluna_exibida++;
                            }
                        } else {
                            $linha++;
                            $celulas = 'A' . $linha . ':F' . $linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $coluna_1);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $coluna_2);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $coluna_3);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $coluna_4);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $coluna_5);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $coluna_6);
                            $coluna_exibida = 1;
                            $coluna_1 = '';
                            $coluna_2 = '';
                            $coluna_3 = '';
                            $coluna_4 = '';
                            $coluna_5 = '';
                            $coluna_6 = '';
                            $coluna_1 = $codigo_edi;
                            $coluna_exibida++;
                        }
                    }
                }
            }
        }
    } // Fim While

} // Fim if $num_rows_animais

if ($tipo_rel == 'C') {
    if ($total_peso_nasc != 0) {
        $media_total_peso_nasc = $total_peso_nasc / $qtd_peso_nasc;
        $media_total_peso_nasc_edi = number_format($media_total_peso_nasc, 2, ',', '.');
    } else {
        $media_total_peso_nasc = '';
        $media_total_peso_nasc_edi = '';
    }
    if ($total_peso_desmama != 0) {
        $media_total_peso_desmama = $total_peso_desmama / $qtd_peso_desmama;
        $media_total_peso_desmama_edi = number_format($media_total_peso_desmama, 2, ',', '.');
    } else {
        $media_total_peso_desmama = '';
        $media_total_peso_desmama_edi = '';
    }

    if ($total_peso_ultimo != 0) {
        $media_total_peso_ultimo = $total_peso_ultimo / $qtd_peso_ultimo;
        $total_peso_ultimo_edi = number_format($total_peso_ultimo, 2, ',', '.');
        $media_total_peso_ultimo_edi = number_format($media_total_peso_ultimo, 2, ',', '.');
    } else {
        $total_peso_ultimo = '';
        $total_peso_ultimo_edi = '';
        $media_total_peso_ultimo = '';
        $media_total_peso_ultimo_edi = '';
    }

    $spreadsheet->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $spreadsheet->getActiveSheet()->getStyle('J5:M5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
    $spreadsheet->getActiveSheet()->getStyle('J5:M5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, $animais_listados);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, 5, $media_total_peso_nasc);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, 5, $media_total_peso_desmama);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, 5, $media_total_peso_ultimo);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, 5, $total_peso_ultimo);
} else {
    $linha++;
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $coluna_1);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $coluna_2);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $coluna_3);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $coluna_4);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $coluna_5);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $coluna_6);

    $spreadsheet->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, $animais_listados);
}

// Rename worksheet

$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listagem_animais.xlsx"');
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
