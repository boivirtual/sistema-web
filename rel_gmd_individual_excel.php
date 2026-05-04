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

$data_sistema = date("d/m/Y");

    $data_inicial = $_REQUEST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = '01';

    $data_final = $_REQUEST['data_final'];
    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));
    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

    $ano_mes = $data_array->format('Y').$data_array->format('m');
    $array_mes_ano[$ano_mes]=$ano_mes;
    $array_peso[$ano_mes]=0;

    for ($i=1; $i < $qtd_meses; $i++) { 
        $proximo_mes=1;
        $data_array->add(new DateInterval('P'.$proximo_mes.'M'));

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
        $mes_extenco = ucfirst(utf8_encode($mes_extenco));
        $array_mes_extenco[$i]=$mes_extenco.'/'.$ano_atual;

        if ($mes_extenco == 'Dezembro') {
            $ano_atual++;
        }

        $array_mes[$i]=$data_array->format('m');
        $array_ano[$i]=$data_array->format('Y');
        $ano_mes = $data_array->format('Y').$data_array->format('m');
        $array_mes_ano[$ano_mes]=$ano_mes;
        $array_peso[$ano_mes]=0;
    } 

    @ session_start(); 

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

    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $num_parto_de = $_REQUEST['num_parto_de'];
    $num_parto_ate = $_REQUEST['num_parto_ate'];
    $num_aborto_de = $_REQUEST['num_aborto_de'];
    $num_aborto_ate = $_REQUEST['num_aborto_ate'];
    $previsao_parto_de = $_REQUEST["previsao_parto_de"];
    $previsao_parto_ate = $_REQUEST["previsao_parto_ate"];
    $data_paricao_de = $_REQUEST["data_paricao_de"];
    $data_paricao_ate = $_REQUEST["data_paricao_ate"];
    $filtro_reproducao = $_REQUEST["filtro_reproducao"];
    $filtro_num_parto = $_REQUEST['filtro_num_parto'];
    $filtro_num_aborto = $_REQUEST['filtro_num_aborto'];
    $filtro_previsao_parto = $_REQUEST['filtro_previsao_parto'];
    $filtro_data_paricao = $_REQUEST['filtro_data_paricao'];
    $filtro_vacas_paridas = $_REQUEST['filtro_vacas_paridas'];
    $filtro_vacas_solteiras = $_REQUEST['filtro_vacas_solteiras'];
    $filtro_vacas_prenhas = $_REQUEST['filtro_vacas_prenhas'];
    $filtro_descarte = $_REQUEST["filtro_descarte"];
    $filtro_positivas = $_REQUEST["filtro_positivas"];
    $filtro_negativas = $_REQUEST["filtro_negativas"];
    $filtro_monta_natural = $_REQUEST["filtro_monta_natural"];

    // Inicia campos vazios 
    if ($num_parto_de=='') {
        $num_parto_de = 0;
        $num_parto_ate = 999;
    }

    if ($num_aborto_de=='') {
        $num_aborto_de = 0;
        $num_aborto_ate = 999;
    }

    $array_codigos_estacao = [];
    $westacao = "";

    // 1. Verifica se existe e se não está vazio/em branco.
    if (isset($_REQUEST['filtro_estacao']) && !empty($_REQUEST['filtro_estacao'])) {
        
        // 2. SE for uma STRING, a convertemos para um ARRAY
        if (is_string($_REQUEST['filtro_estacao'])) {
            // Explode a string pelo delimitador ','
            $estacoes = explode(',', $_REQUEST['filtro_estacao']);
            
            // Remove strings vazias resultantes de vírgulas extras (ex: a vírgula final)
            $estacoes = array_filter($estacoes, 'trim'); 
        } 
        // 3. SE já for um ARRAY (caso venha de um formulário com múltiplos checkboxes, por exemplo)
        else if (is_array($_REQUEST['filtro_estacao'])) {
            $estacoes = $_REQUEST['filtro_estacao'];
        } else {
            // Não é string nem array (ignora o bloco, a condição continua falhando)
            $estacoes = [];
        }

        // Garante que $estacoes ainda tem itens após a conversão
        if (!empty($estacoes)) {
            
            // O restante do seu código pode ser mantido aqui:
            $estacoes_formatadas = array_map(function($estacao) use ($conector) {
                $estacao_limpa = trim($estacao); 
                return "'" . mysqli_real_escape_string($conector, $estacao_limpa) . "'";
            }, $estacoes);

            $in_estacoes = implode(',', $estacoes_formatadas);
            // ... (resto do código para montar $westacao)
            
            $query = "SELECT tbl_par_estacao_id FROM tbl_parametro_estacao_monta
                      WHERE tbl_par_estacao_nome IN ({$in_estacoes})
                      ORDER BY tbl_par_estacao_id ASC";

            $sql = mysqli_query($conector, $query);

            if ($sql && mysqli_num_rows($sql) != 0) {
                while ($reg_estacao = mysqli_fetch_object($sql)){
                    $array_codigos_estacao[] = $reg_estacao->tbl_par_estacao_id;
                }
            }
            
            if (!empty($array_codigos_estacao)) {
                $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(" . implode(',', $array_codigos_estacao) . ")";
            }

        } // Fim do if (!empty($estacoes))
    } else {
        $westacao = ""; // Sem filtro
    }

    // 2. Lógica para definir o controle da cobertura (Corrigido e Simplificado)

    // Se o array de códigos de estação está vazio, o bloco `if` não será executado
    if (empty($array_codigos_estacao)) {
        // Caso 1: SEM filtro de estação
        if ($filtro_monta_natural == 'S') {
            $cobertura_controle = " AND tbl_cobertura_controle = 'M'"; 
        } else {
            // Se filtro_monta_natural for 'N' e não houver estação, buscamos tudo
            $cobertura_controle = "";
        }
    } else {
        // Caso 2: COM filtro de estação (e $westacao já está preenchido corretamente)
        if ($filtro_monta_natural == 'N') {
            // Filtra apenas por Cobertura Controlada (C) E as Estações
            $cobertura_controle = " AND tbl_cobertura_controle = 'C' " . $westacao;
            $westacao = ""; // Limpa $westacao para não duplicar na query final
        } else {
            // Filtra por Monta Natural (M) OU (Controlada (C) E as Estações)
            $cobertura_controle = " AND (tbl_cobertura_controle = 'M' OR (tbl_cobertura_controle = 'C' " . $westacao . "))";
            $westacao = ""; // Limpa $westacao para não duplicar na query final
        }
    }

    $wlocal = '';
    $wlocal_anterior = '';

    if (isset($_REQUEST['local']) && !empty($_REQUEST['local'])) {
        $local_filtro = $_REQUEST['local'];
        $string_limpa = trim($local_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $local_para_query = implode(',', $itens_limpos);

            $wlocal = " AND tbl_animal_codigo_fazenda IN({$local_para_query})";

            $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN({$local_para_query}) OR (tbl_animal_codigo_origem IN({$local_para_query}) 
                AND tbl_animal_situacao='V'))";
        }
    }

    $worigem='';

    if (isset($_REQUEST['origem']) && !empty($_REQUEST['origem'])) {
        $origem_filtro = $_REQUEST['origem'];
        $string_limpa = trim($origem_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $origem_para_query = implode(',', $itens_limpos);

            $worigem = " AND tbl_animal_codigo_origem IN({$origem_para_query})";
        }
    }

    $wsexo = "";

    if (isset($_REQUEST['sexo']) && !empty($_REQUEST['sexo'])) {
        $sexo_filtro = $_REQUEST['sexo'];
        $string_limpa = trim($sexo_filtro, " ,"); 

        if ($string_limpa!='Todos') {
            $matriz_itens = explode(",", $string_limpa);
            
            $itens_limpos = array_map('trim', $matriz_itens);
            
            $itens_validos = array_filter($itens_limpos);

            if (!empty($itens_validos)) {
                $itens_com_aspas = array_map(function($item) {
                    // **IMPORTANTE**: Use aspas simples dentro da string
                    return "'" . $item . "'"; 
                }, $itens_validos);

                $sexo_para_query = implode(',', $itens_com_aspas);
                $wsexo = " AND tbl_animal_sexo IN({$sexo_para_query})";
            }
        }   
    }

    $wmae = "";

    if (isset($_REQUEST['codigos_maes']) && !empty($_REQUEST['codigos_maes'])) {
        $mae_filtro = $_REQUEST['codigos_maes'];
        $string_limpa = trim($mae_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $mae_para_query = implode(',', $itens_limpos);

            $wmae = " AND tbl_animal_codigo_mae IN({$mae_para_query})";
        }
    }

    $wpai = "";

    if (isset($_REQUEST['codigos_pais']) && !empty($_REQUEST['codigos_pais'])) {
        $pai_filtro = $_REQUEST['codigos_pais'];
        $string_limpa = trim($pai_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $pai_para_query = implode(',', $itens_limpos);

            $wpai = " AND tbl_animal_codigo_pai IN({$pai_para_query})";
        }
    }

    $wraca = "";

    if (isset($_REQUEST['codigos_racas']) && !empty($_REQUEST['codigos_racas'])) {
        $raca_filtro = $_REQUEST['codigos_racas'];
        $string_limpa = trim($raca_filtro, " ,"); 
        
        $matriz_itens = explode(",", $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $raca_para_query = implode(',', $itens_limpos);

            $wraca = " AND tbl_animal_codigo_raca IN({$raca_para_query})";
        }
    }

    $data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
    $data_nasc_final = $_REQUEST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $situacao_vendido = $_REQUEST['situacao_vendido'];
    $situacao_morte = $_REQUEST['situacao_morte'];
    $situacao_outra = $_REQUEST['situacao_outra'];
    $ativo_filtro = $_REQUEST['ativo'];

    if ($ativo_filtro=='Todos') {
        $wativo='';
    }
    else {
        $wativo = " AND tbl_animal_ativo IN(";
        $wativo .= "'" . $ativo_filtro . "'";
        $wativo.= ")";
    }
    
    $peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
    $peso_nasc_final = $_REQUEST["peso_nasc_final"];

    $peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
    $peso_desmama_final = $_REQUEST["peso_desmama_final"];

    $peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
    $peso_ult_final = $_REQUEST["peso_ult_final"];

    if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial==0 && $peso_ult_final==0){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $wcategoria = "";

    if (isset($_REQUEST['categoria']) && !empty($_REQUEST['categoria'])) {
        $categoria_filtro = $_REQUEST['categoria'];
        $string_limpa = trim($categoria_filtro, " ,"); 
        $matriz_itens = explode(',', $string_limpa);
        $itens_limpos = array_filter(array_map('trim', $matriz_itens));

        if (!empty($itens_limpos)) {
            $wcategoria = $itens_limpos;
        }
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

$wsituacao='';
$situacoes='';

if ($ativo_filtro=='Todos') {
    if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'N') {
        $situacoes = "''".','."'V'";
    }
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "''".','."'V'".','."'M'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "''".','."'V'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "''".','."'M'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'S') {
        $situacoes = "''".','."'M'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "''".','."'S'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'S') {
       $situacoes = "''".','."'V'".','."'M'".','."'S'";
    }    
}
else if ($ativo_filtro=='N') {
    if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'N') {
        $situacoes = "'V'";
    }
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "'V'".','."'M'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "'V'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'N') {
        $situacoes = "'M'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'S' && $situacao_outra == 'S') {
        $situacoes = "'M'".','."'S'";
    }    
    else if ($situacao_vendido == 'N' && $situacao_morte == 'N' && $situacao_outra == 'S') {
        $situacoes = "'O'";
    }    
    else if ($situacao_vendido == 'S' && $situacao_morte == 'S' && $situacao_outra == 'S') {
       $situacoes = "'V'".','."'M'".','."'S'";
    }    
}

if ($situacoes!='') {
    $wsituacao = " AND tbl_animal_situacao IN(";
    $wsituacao.=$situacoes;
    $wsituacao.= ")";
}

$array_celula[0] = 'I';
$array_celula[1] = 'J';
$array_celula[2] = 'K';
$array_celula[3] = 'L';
$array_celula[4] = 'M';
$array_celula[5] = 'N';
$array_celula[6] = 'O';
$array_celula[7] = 'P';
$array_celula[8] = 'Q';
$array_celula[9] = 'R';
$array_celula[10] = 'S';
$array_celula[11] = 'T';
$array_celula[12] = 'U';
$array_celula[13] = 'V';
$array_celula[14] = 'W';
$array_celula[15] = 'X';
$array_celula[16] = 'Y';
$array_celula[17] = 'Z';
$array_celula[18] = 'AA';

$array_coluna[1] = 'A';
$array_coluna[2] = 'B';
$array_coluna[3] = 'C';
$array_coluna[4] = 'D';
$array_coluna[5] = 'E';
$array_coluna[6] = 'F';
$array_coluna[7] = 'G';
$array_coluna[8] = 'H';
$array_coluna[9] = 'I';
$array_coluna[10] = 'J';
$array_coluna[11] = 'K';
$array_coluna[12] = 'L';
$array_coluna[13] = 'M';
$array_coluna[14] = 'N';
$array_coluna[15] = 'O';
$array_coluna[16] = 'P';
$array_coluna[17] = 'Q';
$array_coluna[18] = 'R';
$array_coluna[19] = 'S';
$array_coluna[20] = 'T';
$array_coluna[21] = 'U';
$array_coluna[22] = 'V';
$array_coluna[23] = 'W';
$array_coluna[24] = 'X';
$array_coluna[25] = 'Y';
$array_coluna[26] = 'Z';

@session_start();

$nome_relatorio = "Análise de Ganho de Peso Rebanho - Por Indivíduo";

$spreadsheet->getActiveSheet()->mergeCells('A1:N1');
$spreadsheet->getActiveSheet()->mergeCells('A2:N2');
$spreadsheet->getActiveSheet()->mergeCells('B4:N4');

$i = $qtd_meses + 1;
$celula = $array_celula[$i] . 4;

$spreadsheet->getActiveSheet()->getStyle('A4:' . $celula)->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A4:' . $celula)->getFill()->getStartColor()->setARGB('DCDCDC');

$celula = $array_celula[$i] . 6;

$spreadsheet->getActiveSheet()->getStyle('A6:' . $celula)->getFill()->setFillType(Fill::FILL_SOLID);
$spreadsheet->getActiveSheet()->getStyle('A6:' . $celula)->getFill()->getStartColor()->setARGB('DCDCDC');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
    ->setCellValue("A2",$descricao_filtro);

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A4", "Animais");

for ($i = 0; $i < $qtd_meses; $i++) {
    $celula = $array_celula[$i] . 6;
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue($celula, $array_mes[$i] . '/' . $array_ano[$i]);
}

$i = $qtd_meses + 1;
$celula = $array_celula[$i] . 4;

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue($celula, "GMD Médio");

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A6", "Nº Animal")
    ->setCellValue("B6", "Descarte")
    ->setCellValue("C6", "Fazenda")
    ->setCellValue("D6", "Sexo")
    ->setCellValue("E6", "Nascimento")
    ->setCellValue("F6", "Raca")
    ->setCellValue("G6", "Mae Id")
    ->setCellValue("H6", "Pai Id");

for ($i = 0; $i < $qtd_meses; $i++) {
    $celula = $array_celula[$i] . 6;
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue($celula, $array_mes[$i] . '/' . $array_ano[$i]);
}

$celula = $array_celula[$i] . 6;

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue($celula, "Ganho");

$i++;
$celula = $array_celula[$i] . 6;

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue($celula, "GMD");


$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(8);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(10);

// Pega a letra da ultima celula e define o tamanho para 'GMD Médio'
$i = $qtd_meses + 1;
$celula = $array_celula[$i];
$spreadsheet->getActiveSheet()->getColumnDimension($celula)->setWidth(12);

$spreadsheet->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('J1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('H5:U5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('C6:U6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$linha = 6;

    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $animais_listados=0;
    $gmd_total = 0;
    $numero_gmd = 0;

    if ($codigo_alfa_numerico!='') {
        $sql = "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
            ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_consulta'";
    }
    else {
        if ($situacao_vendido == 'S') {
            $sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE 
                tbl_animal_lixeira=0" .
                $wativo . 
                $wsituacao .
                $wlocal_anterior . 
                $wsexo . 
                $wraca . 
                $wpai . 
                $wmae . 
                $wpeso_nasc . 
                $wpeso_desmama . 
                $wpeso_ult . 
                $wdata_nasc .
                " ORDER BY 
                tbl_animal_codigo_fazenda, 
                tbl_animal_codigo_numerico ASC"; 
        }
        else {
            $sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE 
                tbl_animal_lixeira=0" .
                $wativo . 
                $wsituacao .
                $wlocal . 
                $wsexo . 
                $wraca . 
                $wpai . 
                $wmae . 
                $wpeso_nasc . 
                $wpeso_desmama . 
                $wpeso_ult . 
                $wdata_nasc .
            " ORDER BY 
                tbl_animal_codigo_fazenda, 
                tbl_animal_codigo_numerico ASC"; 
        }
    }

    $tbl_animais = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($tbl_animais);

    $codigos_pais = [];
    $codigos_maes = [];
    $codigos_racas = [];
    $codigos_pelagem = [];
    $codigos_origem = [];
    $ids_femeas = [];
    $dados_animais = [];
    $dados_descarte = [];

    // Passo 1: Coletar os dados e os IDs dos pais, mae, origem, raça, femeas para o cache
    while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
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
        $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $dados_animais[] = $reg_animal;

        if ($reg_animal->tbl_animal_codigo_mae) {
            $codigos_maes[] = $reg_animal->tbl_animal_codigo_mae;
        }

        if ($reg_animal->tbl_animal_codigo_pai) {
            $codigos_pais[] = $reg_animal->tbl_animal_codigo_pai;
        }

        if ($reg_animal->tbl_animal_codigo_raca) {
            $codigos_racas[] = $reg_animal->tbl_animal_codigo_raca;
        }

        if ($reg_animal->tbl_animal_codigo_pelagem) {
            $codigos_pelagem[] = $reg_animal->tbl_animal_codigo_pelagem;
        }

        if ($reg_animal->tbl_animal_codigo_origem) {
            $codigos_origem[] = $reg_animal->tbl_animal_codigo_origem;
        }

        if ($reg_animal->tbl_animal_sexo == 'F') {
            $ids_femeas[] = $reg_animal->tbl_animal_codigo_id;
        }

        if ($filtro_descarte=='S' && $reg_animal->tbl_animal_descarte_reproducao) {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }
    }

    $dados_partos = [];
    $dados_abortos = [];
    $dados_natimortos = [];
    $dados_previsao_partos = [];
    $dados_data_partos = [];
    $ultimo_filho_cache = [];
    $num_partos = 0;
    $num_abortos = 0;
    $data_ref = date("Y-m-d");
    $femeas_prenhes = [];
    $femeas_negativas = [];
    $femea_selecionada_cobertura = [];

    // Consulta otimizada para verificar se a femea esta na estacao atual

    if (!empty($ids_femeas)) {
        $ids_string = implode(',', array_map('intval', $ids_femeas));
  
        $sql = "SELECT *
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                 WHERE tbl_cobertura_lixeira=0 AND 
                      (tbl_cobertura_controle = 'C' OR  
                       tbl_cobertura_controle = 'M') AND                        
                       tbl_cobertura_codigo_local IN({$local_para_query}) AND 
                       tbl_ite_cobertura_codigo_id_animal IN ($ids_string)
                ORDER BY tbl_ite_cobertura_codigo_id_animal ASC, tbl_ite_cobertura_numero_id DESC"; // Ordena primeiro por fêmea, depois por ID de forma decrescente

        $tbl_item_cobertura = mysqli_query($conector, $sql);

        $femeas_ja_processadas = [];

        if ($tbl_item_cobertura && mysqli_num_rows($tbl_item_cobertura) > 0) {
            while ($registro = mysqli_fetch_assoc($tbl_item_cobertura)) {
                $codigo_animal = $registro['tbl_ite_cobertura_codigo_id_animal'];
                $diagnostico = $registro['tbl_ite_cobertura_resultado_diagnostico'];
                $id_estacao = $registro['tbl_cobertura_codigo_estacao_monta'];
                $controle = $registro['tbl_cobertura_controle'];
                $nascido = $registro['tbl_ite_cobertura_nascido'];
                $local =  $registro['tbl_cobertura_codigo_local'];

                // Pega estacao atual da fazenda 
                $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
                    WHERE tbl_par_codigo_local = '$local' AND 
                          tbl_par_lixeira=0 AND 
                          tbl_par_estacao_id = '$id_estacao'
                    ORDER BY tbl_par_estacao_id DESC LIMIT 1");

                $num_rows_estacao = mysqli_num_rows($tbl_estacao);

                if ($num_rows_estacao!=0) {
                    $reg_estacao = mysqli_fetch_object($tbl_estacao);
                    //$id_estacao_atual = $reg_estacao->tbl_par_estacao_id;
                    $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
                }
                else {
                    //$id_estacao_atual = 0;
                    $desc_estacao = 'Monta';
                }

                if (!isset($femeas_ja_processadas[$codigo_animal])) {
                    $femeas_ja_processadas[$codigo_animal] = true;

                    if ($controle=='C') {
                        //if ($id_estacao==$id_estacao_atual) {
                            $femea_selecionada_cobertura[$codigo_animal] = [
                            'selecionada' => 'S',
                            'controle' => 'C',
                            'diagnostico' => $diagnostico,
                            'nascido' => $nascido,
                            'estacao_id' => $id_estacao,
                            'desc_estacao' => $desc_estacao
                            ];
                            
                            if ($diagnostico=='N' || $nascido!='') {
                                $femea_selecionada_cobertura[$codigo_animal] = [
                                'selecionada' => '',        
                                'controle' => 'C',
                                'diagnostico' => $diagnostico,
                                'nascido' => $nascido,
                                'estacao_id' => $id_estacao,
                                'desc_estacao' => $desc_estacao
                                ];
                            }
                        //}
                    }
                    else {
                        $femea_selecionada_cobertura[$codigo_animal] = [
                                'selecionada' => 'S',        
                                'controle' => 'M',
                                'diagnostico' => $diagnostico,
                                'nascido' => $nascido,
                                'estacao_id' => $id_estacao,
                                'desc_estacao' => $desc_estacao
                                ];

                        if ($diagnostico=='N' || $nascido!='') {
                            $femea_selecionada_cobertura[$codigo_animal] = [
                                'selecionada' => '',        
                                'controle' => 'M',
                                'diagnostico' => $diagnostico,
                                'nascido' => $nascido,
                                'estacao_id' => $id_estacao,
                                'desc_estacao' => $desc_estacao
                                ];
                        }
                    }
                }
            }
        }
    }

    // Consulta otimizada para verificar as femeas negativas

    if ($filtro_reproducao == 'S') {
        if (!empty($ids_femeas)) {
            $ids_string = implode(',', array_map('intval', $ids_femeas));
            $sql = "SELECT *
                    FROM tbl_item_cobertura
                    INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira = 0 AND
                          tbl_ite_cobertura_codigo_id_animal IN ($ids_string) AND 
                          (tbl_cobertura_controle = 'M' OR tbl_cobertura_controle = 'C')
                    ORDER BY tbl_ite_cobertura_codigo_id_animal ASC, tbl_ite_cobertura_numero_id DESC"; // Ordena primeiro por fêmea, depois por ID de forma decrescente

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            $femeas_ja_processadas = [];

            if ($tbl_item_cobertura && mysqli_num_rows($tbl_item_cobertura) > 0) {
                while ($registro = mysqli_fetch_assoc($tbl_item_cobertura)) {
                    $codigo_animal = $registro['tbl_ite_cobertura_codigo_id_animal'];
                    $diagnostico = $registro['tbl_ite_cobertura_resultado_diagnostico'];

                    if (!isset($femeas_ja_processadas[$codigo_animal])) {
                        $femeas_ja_processadas[$codigo_animal] = true;
                        
                        if ($diagnostico == 'N') {
                            $femeas_negativas[$codigo_animal] = $codigo_animal;
                        }
                    }
                }
            }
        }
    }

    // Consulta otimizada para verificar as femeas prenhes
    if ($filtro_reproducao == 'S') {
        if (!empty($ids_femeas)) {
            $ids_string = implode(',', $ids_femeas);

            // 1. Consulta única com a cláusula IN
            $sql = "SELECT *
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira = 0
                    AND tbl_ite_cobertura_codigo_id_animal IN ($ids_string)
                    AND tbl_ite_cobertura_resultado_diagnostico = 'P'
                    AND (tbl_ite_cobertura_nascido='' OR tbl_ite_cobertura_nascido IS NULL)" . $cobertura_controle . "
                    ORDER BY tbl_ite_cobertura_numero_id DESC";

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            // 2. Processar os resultados e armazená-los em um array
            if ($tbl_item_cobertura && mysqli_num_rows($tbl_item_cobertura) > 0) {
                while ($registro = mysqli_fetch_assoc($tbl_item_cobertura)) {
                    $codigo_animal = $registro['tbl_ite_cobertura_codigo_id_animal'];

                    // Se você quer apenas o registro mais recente (devido ao ORDER BY DESC),
                        // a lógica abaixo é a correta.
                    if (!isset($femeas_prenhes[$codigo_animal])) {
                        $femeas_prenhes[$codigo_animal] = $codigo_animal;
                    }
                }
            }
        }
    }

    // Consulta otimizada para verificar os dias de aborto para cada femea que teve aborto
    $dados_femeas_aborto_natimorto = [];

    if (!empty($ids_femeas)) {
        $ids_string = implode(',', $ids_femeas);

        // 1. Consulta otimizada
        $sql = "SELECT tbl_mov_estoque_codigo_mae, tbl_mov_estoque_nascimento
                FROM tbl_movimentacao_estoque
                WHERE tbl_mov_estoque_codigo_mae IN ($ids_string)
                AND tbl_mov_estoque_codigo_id_animal = 999999999
                AND (tbl_mov_estoque_entrada_saida = 'A'
                AND (tbl_mov_estoque_tipo_movimentacao = 'A' OR tbl_mov_estoque_tipo_movimentacao = 'B')) OR (tbl_mov_estoque_entrada_saida = 'S' AND tbl_mov_estoque_tipo_movimentacao = 'M')
                ORDER BY tbl_mov_estoque_nascimento DESC";

        $tbl_aborto = mysqli_query($conector, $sql);

        // 2. Processamento dos resultados
        if ($tbl_aborto && mysqli_num_rows($tbl_aborto) > 0) {
            // Inicializa o array com todos os IDs, assumindo que não houve aborto
            // Isso garante que você tenha um registro para cada fêmea, mesmo as que não estão na consulta.
            foreach ($ids_femeas as $id) {
                $dados_femeas_aborto_natimorto[$id] = [
                    'teve_aborto_natimorto' => 'N',
                    'dias_aborto_natimorto' => 0,
                    'data_aborto_natimorto' => 0
                ];
            }
            
            // Agora, itera sobre os resultados da consulta e atualiza o array
            while ($reg_aborto = mysqli_fetch_object($tbl_aborto)) {
                $codigo_mae = $reg_aborto->tbl_mov_estoque_codigo_mae;

                // Se a fêmea já foi processada (por ter mais de um aborto), pule para a próxima
                // Isso garante que você só pegue o aborto mais recente devido ao ORDER BY DESC
                if (isset($dados_femeas_aborto_natimorto[$codigo_mae]['teve_aborto_natimorto']) && $dados_femeas_aborto_natimorto[$codigo_mae]['teve_aborto_natimorto'] == 'S') {
                    continue;
                }

                $data_aborto = $reg_aborto->tbl_mov_estoque_nascimento;
                //$data_ref_ajustada = date("Y-m-d", strtotime($data_ref . "- 35 days"));

                $data_ref_ajustada = date("Y-m-d");

                /*if ($codigo_mae==2198) {
                    print_r('Data Aborto: ' . $data_aborto . ' Data Ref: ' . $data_ref_ajustada . '<br>');
                }*/

                $diferenca = strtotime($data_ref_ajustada) - strtotime($data_aborto);
                $dias_aborto_natimorto = floor($diferenca / (60 * 60 * 24));

                $dados_femeas_aborto_natimorto[$codigo_mae]['teve_aborto_natimorto'] = 'S';
                $dados_femeas_aborto_natimorto[$codigo_mae]['dias_aborto_natimorto'] = $dias_aborto_natimorto;
                $dados_femeas_aborto_natimorto[$codigo_mae]['data_aborto_natimorto'] = $data_aborto;
            }
        }
    }

    if (!empty($ids_femeas)) {
        $ids_string = implode(',', $ids_femeas);

        // Consulta otimizada para contar filhos de todas as mães
        $sql_filhos = "SELECT tbl_animal_codigo_mae, COUNT(*) as num_filhos
                       FROM tbl_animais
                       WHERE tbl_animal_codigo_mae IN ($ids_string)
                       GROUP BY tbl_animal_codigo_mae";
        $result_filhos = mysqli_query($conector, $sql_filhos);

        // Adicione os resultados a um array para fácil acesso
        $num_filhos_cache = [];
        while ($row = mysqli_fetch_assoc($result_filhos)) {
            $num_filhos_cache[$row['tbl_animal_codigo_mae']] = $row['num_filhos'];
        }

        // Consulta otimizada para contar natimortos de todas as mães
        $sql_natimorto = "SELECT tbl_mov_estoque_codigo_mae, COUNT(*) as num_natimorto
                          FROM tbl_movimentacao_estoque
                          WHERE tbl_mov_estoque_codigo_mae IN ($ids_string)
                          AND tbl_mov_estoque_codigo_id_animal=999999999
                          AND tbl_mov_estoque_entrada_saida='E'
                          AND tbl_mov_estoque_tipo_movimentacao='N'
                          GROUP BY tbl_mov_estoque_codigo_mae";
        $result_natimorto = mysqli_query($conector, $sql_natimorto);

        // Adicione os resultados a um array para fácil acesso
        $num_natimorto_cache = [];
        while ($row = mysqli_fetch_assoc($result_natimorto)) {
            $num_natimorto_cache[$row['tbl_mov_estoque_codigo_mae']] = $row['num_natimorto'];
        }

        // Percorrer o array de animais e combinar os dados
        foreach ($dados_animais as $reg_animal) {
            if ($reg_animal->tbl_animal_sexo == 'F') {
                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                $num_partos = ($num_filhos_cache[$codigo_animal_id] ?? 0) + ($num_natimorto_cache[$codigo_animal_id] ?? 0);
                $dados_partos[$codigo_animal_id] = $num_partos;
            }
        }

        // Percorrer o array de animais natimorto
        foreach ($dados_animais as $reg_animal) {
            if ($reg_animal->tbl_animal_sexo == 'F') {
                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                $num_natimorto = ($num_natimorto_cache[$codigo_animal_id] ?? 0);
                $dados_natimortos[$codigo_animal_id] = $num_natimorto;
            }
        }

        // Consulta otimizada para contar abortos de todas as mães
        $sql_aborto = "SELECT tbl_mov_estoque_codigo_mae, COUNT(*) as num_aborto
                          FROM tbl_movimentacao_estoque
                          WHERE tbl_mov_estoque_codigo_mae IN ($ids_string)
                          AND tbl_mov_estoque_codigo_id_animal=999999999
                          AND tbl_mov_estoque_entrada_saida='A'
                          GROUP BY tbl_mov_estoque_codigo_mae";
        $result_aborto = mysqli_query($conector, $sql_aborto);

        // Adicione os resultados a um array para fácil acesso
        $num_aborto_cache = [];
        while ($row = mysqli_fetch_assoc($result_aborto)) {
            $num_aborto_cache[$row['tbl_mov_estoque_codigo_mae']] = $row['num_aborto'];
        }

        // Percorrer o array de animais abortos
        foreach ($dados_animais as $reg_animal) {
            if ($reg_animal->tbl_animal_sexo == 'F') {
                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                $num_abortos = ($num_aborto_cache[$codigo_animal_id] ?? 0);
                $dados_abortos[$codigo_animal_id] = $num_abortos;
            }
        }

        // Consulta otimizada para verificar partos de todas as mães
        if ($filtro_data_paricao=='S') {
            $sql_partos = "SELECT tbl_mov_estoque_codigo_mae, tbl_mov_estoque_nascimento
                FROM tbl_movimentacao_estoque
                WHERE tbl_mov_estoque_codigo_mae IN ($ids_string) AND
                      tbl_mov_estoque_entrada_saida='E' AND
                      tbl_mov_estoque_tipo_movimentacao='N' AND 
                      tbl_mov_estoque_nascimento>='$data_paricao_de' AND 
                      tbl_mov_estoque_nascimento<='$data_paricao_ate'";
            $result_partos = mysqli_query($conector, $sql_partos);

            $num_rows_partos = mysqli_num_rows($result_partos);

            // Adicione os resultados a um array para fácil acesso

            if ($num_rows_partos>0) {
                $paricao_cache = [];

                while ($row = mysqli_fetch_assoc($result_partos)) {
                    $paricao_cache[$row['tbl_mov_estoque_codigo_mae']] = 'S';
                }
            }

            // Percorrer o array de animais
            foreach ($dados_animais as $reg_animal) {
                if ($reg_animal->tbl_animal_sexo == 'F') {
                    $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                    if (array_key_exists($codigo_animal_id, $paricao_cache)) {
                        if ($paricao_cache[$codigo_animal_id]=='S') {
                            $dados_data_partos[$codigo_animal_id] = 'S';
                        }                        
                    }
                }
            }
        }

        //if ($filtro_vacas_paridas=='S' || $filtro_vacas_solteiras=='S') {
            $sql_ultimo_filho = "SELECT t1.tbl_animal_codigo_mae, t1.tbl_animal_codigo_id, t1.tbl_animal_data_nascimento, t1.tbl_animal_ativo, t1.tbl_animal_codigo_pai
            FROM tbl_animais t1
            INNER JOIN (
            SELECT tbl_animal_codigo_mae, MAX(tbl_animal_data_nascimento) as ultima_data_nascimento
            FROM tbl_animais
            
            WHERE tbl_animal_codigo_mae IN ($ids_string)
                GROUP BY tbl_animal_codigo_mae
                ) t2 ON t1.tbl_animal_codigo_mae = t2.tbl_animal_codigo_mae AND t1.tbl_animal_data_nascimento = t2.ultima_data_nascimento";

            $result_ultimo_filho = mysqli_query($conector, $sql_ultimo_filho);

            // Armazene os resultados para fácil acesso
            $ultimo_filho_cache = [];
            while ($row = mysqli_fetch_assoc($result_ultimo_filho)) {
                $data_ref = date("Y-m-d");
                $diferenca = strtotime($data_ref) - strtotime($row['tbl_animal_data_nascimento']);
                $dias_nascimento = floor($diferenca / (60 * 60 * 24));

                $ultimo_filho_cache[$row['tbl_animal_codigo_mae']] = [
                    'id_filho' => $row['tbl_animal_codigo_id'],
                    'data_nascimento' => $row['tbl_animal_data_nascimento'],
                    'ativo' => $row['tbl_animal_ativo'],
                    'pai_filho' => $row['tbl_animal_codigo_pai'],
                    'dias_nascimento_filho' => $dias_nascimento
                ];            
            }            
        } 
    //}

    if (!empty($ids_femeas)) {
        $dias_gestacao = 282;

        // Converte os IDs de fêmeas para uma string segura
        $ids_femeas_string = implode(',', array_map('intval', $ids_femeas));

        // Passo 1: Consulta Única para pegar a última cobertura de cada fêmea
        $sql_coberturas = "
            SELECT T1.*, T2.*
            FROM tbl_item_cobertura AS T1
            INNER JOIN tbl_cobertura AS T2 ON T2.tbl_cobertura_id = T1.tbl_ite_cobertura_numero_id
            INNER JOIN (
                SELECT tbl_ite_cobertura_codigo_id_animal, MAX(tbl_cobertura_incluido_em) AS ultimo_registro
                FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_ite_cobertura_codigo_id_animal IN ({$ids_femeas_string})
                    AND tbl_cobertura_lixeira = 0
                    AND tbl_ite_cobertura_resultado_diagnostico = 'P'" . $cobertura_controle . "
                GROUP BY tbl_ite_cobertura_codigo_id_animal
            ) AS subquery
            ON T1.tbl_ite_cobertura_codigo_id_animal = subquery.tbl_ite_cobertura_codigo_id_animal
            AND T2.tbl_cobertura_incluido_em = subquery.ultimo_registro";

           // print_r($sql_coberturas);

        $res_coberturas = mysqli_query($conector, $sql_coberturas);
        $coberturas_por_femea = [];
        $ids_coberturas_c = [];
        $ids_protocolos_c = [];

        while ($row = mysqli_fetch_object($res_coberturas)) {
            $coberturas_por_femea[$row->tbl_ite_cobertura_codigo_id_animal] = $row;
            // Coleta os IDs para as próximas consultas
            if ($row->tbl_cobertura_controle == 'C') {
                $ids_coberturas_c[] = $row->tbl_cobertura_id;
                $ids_protocolos_c[] = $row->tbl_cobertura_protocoloiatf;
            }
        }

        // Passo 2: Otimiza as consultas para o controle 'C' fora do loop
        $protocolos_cobertura = [];
        if (!empty($ids_coberturas_c)) {
            $ids_coberturas_c_string = implode(',', array_map('intval', $ids_coberturas_c));
            $sql_protocolo = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura WHERE tbl_protocolo_cobertura_codigo_id IN ({$ids_coberturas_c_string})");
            while ($reg = mysqli_fetch_object($sql_protocolo)) {
                $protocolos_cobertura[$reg->tbl_protocolo_cobertura_codigo_id] = $reg;
            }
        }

        $itens_protocolo = [];
        if (!empty($ids_protocolos_c)) {
            $ids_protocolos_c_string = implode(',', array_map('intval', $ids_protocolos_c));
            $sql_itens = mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_lixeira = 0 AND tbl_ite_protocoloiatf_protocolo_id IN ({$ids_protocolos_c_string}) ORDER BY tbl_ite_protocoloiatf_id ASC");
            while ($reg = mysqli_fetch_object($sql_itens)) {
                $itens_protocolo[$reg->tbl_ite_protocoloiatf_protocolo_id][] = $reg;
            }
        }
        
        // Passo 3: Agora, itere sobre as fêmeas e use os dados já carregados
        foreach ($ids_femeas as $femea) {
            $data_previsao_parto = 0;
            
            if (isset($coberturas_por_femea[$femea])) {
                $reg_cobertura = $coberturas_por_femea[$femea];
                
                if ($reg_cobertura->tbl_cobertura_controle == 'C') {
                    // Acesse os dados dos arrays pré-carregados
                    $reg_protocolo_cobertura = $protocolos_cobertura[$reg_cobertura->tbl_cobertura_id];
                    $data_protocolo_cobertura = $reg_protocolo_cobertura->tbl_protocolo_cobertura_data;
                    $reg_itens_iatf_array = $itens_protocolo[$reg_cobertura->tbl_cobertura_protocoloiatf];

                    foreach ($reg_itens_iatf_array as $reg_itens_iatf) {
                        $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);
                        $data_servico = date("Y-m-d", strtotime($data_protocolo_cobertura . "+{$dias} days"));
                        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_gestacao} days"));
                    }
                } else {
                    $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                }
            }
            
            $dados_previsao_partos[$femea] = $data_previsao_parto;
        }
    } else {
        $previsao_parto_de = '1900-01-01';
        $previsao_parto_ate = '9999-99-99';
        $data_previsao_parto = '1900-01-01';
    }    

    $dados_maes = [];

    if (!empty($codigos_maes)) {
        $sql_maes = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_maes) . ")";

        $rs_maes = mysqli_query($conector, $sql_maes);

        while ($reg_mae = mysqli_fetch_object($rs_maes)) {
            $dados_maes[$reg_mae->tbl_animal_codigo_id] = $reg_mae->tbl_animal_codigo_alfa . ' ' . intval($reg_mae->tbl_animal_codigo_numerico);
        }
    }

    $dados_pais_semem = [];
    $dados_pais_animal = [];

    if (!empty($codigos_pais)) {
        $sql_pais_semem = "SELECT tbl_semem_codigo_id, tbl_semem_nome FROM tbl_semem WHERE tbl_semem_codigo_id IN (" . implode(',', $codigos_pais) . ")";

        $rs_pais_semem = mysqli_query($conector, $sql_pais_semem);

        while ($reg_pai_semem = mysqli_fetch_object($rs_pais_semem)) {
            $dados_pais_semem[$reg_pai_semem->tbl_semem_codigo_id] = $reg_pai_semem->tbl_semem_nome;
        }

        $sql_pais_animal = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_pais) . ")";

        $rs_pais_animal = mysqli_query($conector, $sql_pais_animal);

        while ($reg_pai_animal = mysqli_fetch_object($rs_pais_animal)) {
            $dados_pais_animal[$reg_pai_animal->tbl_animal_codigo_id] = $reg_pai_animal->tbl_animal_codigo_alfa . ' ' . intval($reg_pai_animal->tbl_animal_codigo_numerico);
        }
    }

    $dados_racas = [];

    if (!empty($codigos_racas)) {
        $sql_racas = "SELECT tab_codigo_raca, tab_descricao_raca FROM tabela_racas WHERE tab_codigo_raca IN (" . implode(',', $codigos_racas) . ")";

        $rs_racas = mysqli_query($conector, $sql_racas);

        while ($reg_racas = mysqli_fetch_object($rs_racas)) {
            $dados_racas[$reg_racas->tab_codigo_raca] = $reg_racas->tab_descricao_raca;
        }
    }

    $dados_pelagem = [];

    if (!empty($codigos_pelagem)) {
        $sql_pelagem = "SELECT tab_codigo_pelagem , tab_descricao_pelagem FROM tabela_pelagens WHERE tab_codigo_pelagem IN (" . implode(',', $codigos_pelagem) . ")";

        $rs_pelagens = mysqli_query($conector, $sql_pelagem);

        while ($reg_pelagens = mysqli_fetch_object($rs_pelagens)) {
            $dados_pelagem[$reg_pelagens->tab_codigo_pelagem] = $reg_pelagens->tab_descricao_pelagem;
        }
    }

    $dados_origem = [];

    if (!empty($codigos_origem)) {
        $sql_origem = "SELECT tbl_pessoa_id , tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id IN (" . implode(',', $codigos_origem) . ")";

        $rs_origem = mysqli_query($conector, $sql_origem);

        while ($reg_origem = mysqli_fetch_object($rs_origem)) {
            $dados_origem[$reg_origem->tbl_pessoa_id] = $reg_origem->tbl_pessoa_nome;
        }
    }

    $animais = [];
    $animais_listados=0;

    // Retornar a consulta para o início para poder reutilizar os dados
    //mysqli_data_seek($tbl_animais, 0);

    //while ($reg_animal = mysqli_fetch_object($tbl_animais)){
    mysqli_free_result($tbl_animais);
    unset($tbl_animais);    

    foreach ($dados_animais as $reg_animal) {
        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
        $desc_local = $reg_animal->tbl_pessoa_nome;
        $codigo_mae_id = $reg_animal->tbl_animal_codigo_mae; 
        $codigo_pai_id = $reg_animal->tbl_animal_codigo_pai; 
        $codigo_origem_id = $reg_animal->tbl_animal_codigo_origem; 
        $descricao_origem = isset($dados_origem[$reg_animal->tbl_animal_codigo_origem]) ? $dados_origem[$reg_animal->tbl_animal_codigo_origem] : '';
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
        $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

        $idade_ano = $idade_acompanhamento->format('%Y');
        $idade_mes = $idade_acompanhamento->format('%m');
        $idade_dia = $idade_acompanhamento->format('%d');

        if ($idade_ano==0 && $idade_mes!=0) {
            $idade_animal = $idade_mes . ' mes(es)';
        }
        else if ($idade_ano!=0 && $idade_mes==0){
            $idade_animal = $idade_ano . ' ano(s)';
        }
        else if ($idade_ano!=0 && $idade_mes!=0) {
            $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
        }
        else if ($idade_ano==0 && $idade_mes==0){
            $idade_animal = $idade_dia . ' dia(s)';
        }
        else {
            $idade_animal = '';
        }

        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        for($i = 0; $i < count($arrayCategorias); $i++){
            $id_categoria = $arrayCategorias[$i]['id'];
            $idade_de = $arrayCategorias[$i]['idade_de'];
            $idade_ate = $arrayCategorias[$i]['idade_ate'];

            if ($idade >= $idade_de && $idade <= $idade_ate) {
                $codigo_categoria = $id_categoria;

                if ($idade_ate==999999999) {
                    $desc_categoria=' > 36 meses';
                }
                else {
                    $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                }
            }
        }                        

        $selecionadaCoberturaControle = '';
        $selecionadaCobertura = '';
        $diagnostico = '';
        $nascido = '';
        $id_estacao = '';
        $desc_estacao = '';

        // Verifica femea selecionada na estacao ou monta 
        if (!empty($femea_selecionada_cobertura)) {
            $id_animal = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($id_animal, $femea_selecionada_cobertura)) {
                $dados = $femea_selecionada_cobertura[$id_animal];
                $selecionadaCobertura = $dados['selecionada'];
                $selecionadaCoberturaControle = $dados['controle'];
                $diagnostico = $dados['diagnostico'];
                $nascido = $dados['nascido'];
                $id_estacao = $dados['estacao_id'];
                $desc_estacao = $dados['desc_estacao'];
            }
            else {
                $selecionadaCobertura = '';
                $selecionadaCoberturaControle = '';
                $diagnostico = '';
                $nascido = '';
                $id_estacao = '';
                $desc_estacao = '';
            }
        }

        if ($filtro_descarte=='N') {
            $filtroDescarte = 'S';
        }
        else {
            $filtroDescarte = 'N';

            if (!empty($dados_descarte)) {
                $id_animal = $reg_animal->tbl_animal_codigo_id;

                if (array_key_exists($id_animal, $dados_descarte)) {
                    if ($dados_descarte[$id_animal]=='S') {
                        $filtroDescarte = 'S';
                    }
                }
            }
        }

        if ($filtro_num_parto=='N') {
            $filtroNumPartos = 'S';
            $num_partos = 0;

            if (!empty($dados_partos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_partos = $dados_partos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumPartos = 'N';
            $num_partos = 0;

            if (!empty($dados_partos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_partos = $dados_partos[$reg_animal->tbl_animal_codigo_id];

                if ($filtro_reproducao=='S') {
                    if ($num_partos>=$num_parto_de && $num_partos<=$num_parto_ate) {
                        $filtroNumPartos = 'S';
                    }
                }
            }
        }

        if ($filtro_num_aborto=='N') {
            $filtroNumAbortos = 'S';
            $num_abortos = 0;

            if (!empty($dados_abortos) && $reg_animal->tbl_animal_sexo=='F') {
                 $num_abortos = $dados_abortos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumAbortos = 'N';
            $num_abortos = 0;

            if (!empty($dados_abortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_abortos = $dados_abortos[$reg_animal->tbl_animal_codigo_id];

                if ($filtro_reproducao=='S') {
                    if ($num_abortos>=$num_aborto_de && $num_abortos<=$num_aborto_ate) {
                        $filtroNumAbortos = 'S';
                    }
                }
            }
        }

        // recupera numeros Natimortos
        $num_natmortos = 0;

        if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
            $num_natmortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
        }

        if ($filtro_previsao_parto=='N') {
            $filtroDataPrevisao = 'S';
            $data_previsao_parto = 0;

            if (!empty($dados_previsao_partos)) {
                if ($reg_animal->tbl_animal_sexo=='F') {
                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];
                }
            }
        }
        else {
            if (!empty($dados_previsao_partos)) {
                if ($reg_animal->tbl_animal_sexo=='F') {
                    $filtroDataPrevisao = 'N';
                    $data_previsao_parto = 0;

                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];

                    if ($filtro_reproducao=='S') {
                        if ($data_previsao_parto>=$previsao_parto_de && 
                            $data_previsao_parto<=$previsao_parto_ate) {
                            $filtroDataPrevisao = 'S';
                        }
                    }
                }
                else {
                    $filtroDataPrevisao = 'N';
                    $data_previsao_parto = 0;
                }
            }
            else {
                $filtroDataPrevisao = 'N';
                $data_previsao_parto = 0;
            }
        }

        if ($filtro_data_paricao=='N') {
            $filtroDataParicao = 'S';
        }
        else {
            $filtroDataParicao = 'N';

            if (!empty($dados_data_partos) && $reg_animal->tbl_animal_sexo=='F') {
                if ($filtro_reproducao=='S') {
                    if (array_key_exists($reg_animal->tbl_animal_codigo_id, $dados_data_partos)) {
                        if ($dados_data_partos[$reg_animal->tbl_animal_codigo_id]=='S') {
                            $filtroDataParicao = 'S';
                        }
                    }
                }
            }
        }

        // VACAS POSITIVAS
        if ($filtro_positivas=='N') {
            $filtroVacasPositivas = 'S';
        }
        else {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($codigo_mae, $femeas_prenhes)) {
                $filtroVacasPositivas = 'S';
            }
            else {
                $filtroVacasPositivas = 'N';
            }
        }

        // VACAS NEGATIVAS
        if ($filtro_negativas=='N') {
            $filtroVacasNegativas = 'S';
        }
        else {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($codigo_mae, $femeas_negativas)) {
                $filtroVacasNegativas = 'S';
            }
            else {
                $filtroVacasNegativas = 'N';
            }
        }

        // VACAS PRENHES
        if ($filtro_vacas_prenhas=='N') {
            $filtroVacasPrenhes = 'S';
        }
        else {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($codigo_mae, $femeas_prenhes)) {
                $filtroVacasPrenhes = 'S';
            }
            else {
                $filtroVacasPrenhes = 'N';
            }
        }

        // VACAS PARIDAS
        if ($filtro_vacas_paridas=='N') {
            $filtroVacasParidas = 'S';
        }
        else {
            $filtroVacasParidas = 'N';

            if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {

                $codigo_mae = $reg_animal->tbl_animal_codigo_id;

                if (!empty($dados_femeas_aborto_natimorto)) {
                    if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto)) {
                        $dados_aborto = $dados_femeas_aborto_natimorto[$codigo_mae];
                        $teve_aborto_natimorto = $dados_aborto['teve_aborto_natimorto'];
                        $dias_aborto_natimorto = $dados_aborto['dias_aborto_natimorto'];
                    }
                    else {
                        $teve_aborto_natimorto = 'N';
                        $dias_aborto_natimorto = 0;
                    }
                }
                else {
                    $teve_aborto_natimorto = 'N';
                    $dias_aborto_natimorto = 0;
                }

                if (!empty($ultimo_filho_cache)) {
                    if (array_key_exists($codigo_mae, $ultimo_filho_cache)) {

                        $dados_filho = $ultimo_filho_cache[$codigo_mae];
                        $id_filho = $dados_filho['id_filho'];
                        $data_nascimento_filho = $dados_filho['data_nascimento'];
                        $filho_ativo = $dados_filho['ativo'];
                        $pai_filho = $dados_filho['pai_filho'];
                        $dias_nascimento = $dados_filho['dias_nascimento_filho'];

                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date_nascimento = new DateTime($data_nascimento_filho);
                        $idade_acompanhamento = $date_nascimento->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        if ($idade_filho < 8) {
                            if ($filho_ativo=='S') {
                                $filtroVacasParidas = 'S';
                            }
                            else {
                                if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                                    $filtroVacasParidas = 'S';
                                }

                                if ($dias_nascimento<=35) {
                                    $filtroVacasParidas = 'S';
                                }
                            }
                        }
                        else {
                            if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                                $filtroVacasParidas = 'S';
                            }
                        }
                    }
                    else {
                        if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                            $filtroVacasParidas = 'S';
                        }
                    }
                }
                else {
                    if ($teve_aborto_natimorto=='S' && $dias_aborto_natimorto<=35) {
                        $filtroVacasParidas = 'S';
                    }
                }
            }
        }

        // DADOS ULTIMO FILHO
        $id_filho = 0;
        $data_nascimento_filho = 0;
        $filho_ativo = '';
        $pai_filho = 0;
        $dias_nascimento = 0;

        if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (!empty($ultimo_filho_cache)) {
                if (array_key_exists($codigo_mae, $ultimo_filho_cache)) {
                    $dados_filho = $ultimo_filho_cache[$codigo_mae];
                    $id_filho = $dados_filho['id_filho'];
                    $data_nascimento_filho = $dados_filho['data_nascimento'];
                    $filho_ativo = $dados_filho['ativo'];
                    $pai_filho = $dados_filho['pai_filho'];
                    $dias_nascimento = $dados_filho['dias_nascimento_filho'];
                }
            }
        }

        // DADOS ABORTO/NATIMORTO
        $data_aborto_natimorto = 0;

        if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {
            $codigo_mae = $reg_animal->tbl_animal_codigo_id;

            if (!empty($dados_femeas_aborto_natimorto)) {
                if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto)) {
                    $dados_aborto_natimorto = $dados_femeas_aborto_natimorto[$codigo_mae];
            
                    $data_aborto_natimorto = $dados_aborto_natimorto['data_aborto_natimorto'];
                }
            }
        }

        // VACAS SOLTEIRAS
        if ($filtro_vacas_solteiras=='N') {
            $filtroVacasSolteiras = 'S';
        }
        else {
            $filtroVacasSolteiras = 'N';
            if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {

                $codigo_mae = $reg_animal->tbl_animal_codigo_id;

                if (!empty($dados_femeas_aborto_natimorto)) {
                    if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto)) {
                        $dados_aborto = $dados_femeas_aborto_natimorto[$codigo_mae];
                        $teve_aborto_natimorto = $dados_aborto['teve_aborto_natimorto'];
                        $dias_aborto_natimorto = $dados_aborto['dias_aborto_natimorto'];
                    }
                    else {
                        $teve_aborto_natimorto = 'N';
                        $dias_aborto_natimorto = 0;
                    }
                }
                else {
                    $teve_aborto_natimorto = 'N';
                    $dias_aborto_natimorto = 0;
                }

                if (!empty($ultimo_filho_cache)) {
                    if (array_key_exists($codigo_mae, $ultimo_filho_cache)) {

                        $dados_filho = $ultimo_filho_cache[$codigo_mae];
                        $id_filho = $dados_filho['id_filho'];
                        $data_nascimento_filho = $dados_filho['data_nascimento'];
                        $filho_ativo = $dados_filho['ativo'];

                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date_nascimento = new DateTime($data_nascimento_filho);
                        $idade_acompanhamento = $date_nascimento->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        $data_ref = date("Y-m-d");
                        $diferenca = strtotime($data_ref) - strtotime($data_nascimento_filho);
                        $dias_nascimento = floor($diferenca / (60 * 60 * 24));

                        if ($idade_filho < 8) {
                            if ($filho_ativo=='N') {

                                if ($dias_nascimento<=35) {
                                    $filtroVacasSolteiras = 'N';    
                                }
                                else {
                                    $filtroVacasSolteiras = 'S';    
                                }
                            }
                        }
                        else {
                            if ($teve_aborto_natimorto=='N' || $dias_aborto_natimorto>35) {
                                $filtroVacasSolteiras = 'S';
                            }
                        }
                    }
                    else {
                        if ($teve_aborto_natimorto=='N' || $dias_aborto_natimorto>35) {
                            $filtroVacasSolteiras = 'S';
                        }
                    }
                }
                else {
                    if ($teve_aborto_natimorto=='N' || $dias_aborto_natimorto>35) {
                        $filtroVacasSolteiras = 'S';
                    }
                }
            }

            if ($filtroVacasSolteiras == 'S') {
                if (!empty($femeas_prenhes)) {
                    if (array_key_exists($codigo_mae, $femeas_prenhes)) {
                        $filtroVacasSolteiras = 'N';
                    }
                }
            }
        }

        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
        $descricao_raca = isset($dados_racas[$reg_animal->tbl_animal_codigo_raca]) ? $dados_racas[$reg_animal->tbl_animal_codigo_raca] : '';

        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
        $descricao_pelagem = isset($dados_pelagem[$reg_animal->tbl_animal_codigo_pelagem]) ? $dados_pelagem[$reg_animal->tbl_animal_codigo_pelagem] : '';

        if ($codigo_pelagem == '' || 
            $codigo_pelagem==999) {
            $codigo_pelagem = '000';
        }

        $codigo_mae_alfa_numerico = isset($dados_maes[$reg_animal->tbl_animal_codigo_mae]) ? $dados_maes[$reg_animal->tbl_animal_codigo_mae] : '';

        $codigo_pai_alfa_numerico = '';

        if (isset($dados_pais_semem[$reg_animal->tbl_animal_codigo_pai])) {
            $codigo_pai_alfa_numerico = $dados_pais_semem[$reg_animal->tbl_animal_codigo_pai];
        } 
        elseif (isset($dados_pais_animal[$reg_animal->tbl_animal_codigo_pai])) {
            $codigo_pai_alfa_numerico = $dados_pais_animal[$reg_animal->tbl_animal_codigo_pai];
        }

        switch ($reg_animal->tbl_animal_situacao) {
            case 'V':
                $desc_situacao = 'Vendido';
                break;
            case 'M':
                $desc_situacao = 'Morte';
                break;
            case 'T':
                $desc_situacao = 'Aguardando Transf';
                break;
            case 'S':
                $desc_situacao = 'Outra Saída';
                break;
            default:
                $desc_situacao = '';
                break;
        }

        if ($reg_animal->tbl_animal_data_primeiro_peso=='') {
            $data_peso_nasc_edi = '';
        }
        else {
            $data_peso_nasc=new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
            $data_peso_nasc_edi = $data_peso_nasc->format('d/m/Y');
        } 

        if ($reg_animal->tbl_animal_data_desmama=='') {
            $data_peso_desmama_edi = '';
        }
        else {
            $data_peso_desmama=new DateTime($reg_animal->tbl_animal_data_desmama);
            $data_peso_desmama_edi = $data_peso_desmama->format('d/m/Y');
        } 


        if ($reg_animal->tbl_animal_data_ultimo=='') {
            $data_peso_ultimo_edi = '';
        }
        else {
            $data_peso_ultimo=new DateTime($reg_animal->tbl_animal_data_ultimo);
            $data_peso_ultimo_edi = $data_peso_ultimo->format('d/m/Y');
        } 


        $incluido_em=new DateTime($reg_animal->tbl_animal_incluido_em);
        $incluido_por=$reg_animal->tbl_animal_incluido_por; 
        $alterado_em=new DateTime($reg_animal->tbl_animal_alterado_em);
        $alterado_por=$reg_animal->tbl_animal_alterado_por; 
        $baixado_em=new DateTime($reg_animal->tbl_animal_baixado_em);
        $baixado_por=$reg_animal->tbl_animal_baixado_por; 
        $descarte_em=new DateTime($reg_animal->tbl_animal_descarte_em);
        $descarte_por=$reg_animal->tbl_animal_descarte_por; 

        $incluido_em_edi = $incluido_em->format('d/m/Y H:i:s');
        $alterado_em_edi = $alterado_em->format('d/m/Y H:i:s');
        $baixado_em_edi = $baixado_em->format('d/m/Y H:i:s');
        $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');


        $movimentacao_compra = $reg_animal->tbl_animal_movimentacao_compra;

        if ($movimentacao_compra!='') {
            $tbl_compra = mysqli_query($conector, "select * from tbl_movimentacao
                where tbl_movimentacao_id='$movimentacao_compra'");
            $num_rows = mysqli_num_rows($tbl_compra);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tbl_compra);
                $data_compra = new DateTime($reg->tbl_movimentacao_data);
                $data_compra = $data_compra->format('d/m/Y');
                $origem_compra = $reg->tbl_movimentacao_codigo_local_origem;
                $destino_compra = $reg->tbl_movimentacao_codigo_local_destino;

                $tbl_origem = mysqli_query($conector, "select * from tbl_pessoa
                    where tbl_pessoa_id='$origem_compra'");
                $num_rows = mysqli_num_rows($tbl_origem);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_origem);
                    $desc_origem_compra = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_origem_compra = '';
                }

                $tbl_destino = mysqli_query($conector, "select * from tbl_pessoa
                    where tbl_pessoa_id='$destino_compra'");
                    $num_rows = mysqli_num_rows($tbl_destino);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_destino);
                    $desc_destino_compra = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_destino_compra = '';
                }
            }
            else {
                $data_compra = '';
                $desc_origem_compra = '';
                $desc_destino_compra = '';
            }
        }
        else {
            $data_compra = '';
            $desc_origem_compra = '';
            $desc_destino_compra = '';
        }

        /*if ($wcategoria=="") {
            $animalAtual = [
            'codigoLocal' => $reg_animal->tbl_animal_codigo_fazenda,
            'nomeFazenda' => $desc_local,
            'codigoAnimal' => $reg_animal->tbl_animal_codigo_id,
            'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
            'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
            'sexo' => $reg_animal->tbl_animal_sexo,
            'ativo' => $reg_animal->tbl_animal_ativo,
            'situacao' => $desc_situacao,
            'descSituacao' => $desc_situacao,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'codigoRaca' => $reg_animal->tbl_animal_codigo_raca,
            'descricaoRaca' => $descricao_raca,
            'codigoPelagem' => $codigo_pelagem,
            'descricaoPelagem' => $descricao_pelagem,
            'codigoMae' => $codigo_mae_id,
            'codigoMaeAlfaNumerico' => $codigo_mae_alfa_numerico,
            'nomeMae' => $reg_animal->tbl_animal_nome_mae,
            'codigoPai' => $codigo_pai_id,
            'codigoPaiAlfaNumerico' => $codigo_pai_alfa_numerico,
            'nomePai' => $reg_animal->tbl_animal_nome_pai,
            'idadeAnimal' => $idade_animal,
            'codigoCategoria' => $codigo_categoria,
            'descricaoCategoria' => $desc_categoria,
            'observacao' => $reg_animal->tbl_animal_observacao,
            'codigoOrigem' => $reg_animal->tbl_animal_codigo_origem,
            'descricaoOrigem' => $descricao_origem,
            'reprodutor' =>  $reg_animal->tbl_animal_reprodutor,
            'dataNascimento' => $reg_animal->tbl_animal_data_nascimento,
            'idadeMeses' => $idade,
            'primeiroPeso' => $reg_animal->tbl_animal_primeiro_peso,
            'dataPrimeiroPeso' => $reg_animal->tbl_animal_data_primeiro_peso,
            'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
            'dataPesoDesmana' => $reg_animal->tbl_animal_data_desmama,
            'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
            'dataUltimoPeso' => $reg_animal->tbl_animal_data_ultimo,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'incluidoEm' => $incluido_em_edi,
            'incluidoPor' => $incluido_por,
            'alteradoEm' =>  $alterado_em_edi,
            'alteradoPor' => $alterado_por,
            'baixadoEm' => $baixado_em_edi,
            'baixadoPor' => $baixado_por,
            'descarteEm' => $descarte_em_edi,
            'descartePor' => $descarte_por,
            'grauSangue' =>  $reg_animal->tbl_animal_grau_sangue,
            'filtroNumPartos' => $filtroNumPartos,
            'numPartos' => $num_partos,
            'filtroNumAbortos' => $filtroNumAbortos,
            'numAbortos' => $num_abortos,
            'numNatimortos' => $num_natmortos,
            'dataAbortoNatimorto' => $data_aborto_natimorto,
            'dataPrevisaoParto' => $data_previsao_parto,
            'filtroDataPrevisao' => $filtroDataPrevisao,
            'codigoMovimentacaoCompra' => $movimentacao_compra,
            'dataCompra' => $data_compra,
            'origemCompra' => $desc_origem_compra,
            'destinoCompra' => $desc_destino_compra,
            'filtroDataParicao' => $filtroDataParicao,
            'filtroVacasParidas' => $filtroVacasParidas,
            'filtroVacasSolteiras' => $filtroVacasSolteiras,
            'filtroDescarte' => $filtroDescarte,
            'filtroVacasPrenhes' => $filtroVacasPrenhes,
            'filtroVacasNegativas' => $filtroVacasNegativas,
            'filtroVacasPositivas' => $filtroVacasPositivas,
            'selecionadaCobertura' => $selecionadaCobertura,
            'selecionadaCoberturaControle' => $selecionadaCoberturaControle,
            'diagnostico' => $diagnostico,
            'nascido' => $nascido,
            'idFilho' => $id_filho,
            'dataNascimentoFilho' => $data_nascimento_filho,
            'filhoAtivo' => $filho_ativo,
            'paiFilho' => $pai_filho,
            'diasNascimentoFilho' => $dias_nascimento,
            'idEstacao' => $id_estacao,
            'desc_estacao' => $desc_estacao
            ];
            $animais[] = $animalAtual;
        }
        else {
            foreach ($wcategoria as $value) {
                if ($value==$codigo_categoria) {
                    $animalAtual = [
                    'codigoLocal' => $reg_animal->tbl_animal_codigo_fazenda,
                    'nomeFazenda' => $desc_local,
                    'codigoAnimal' => $reg_animal->tbl_animal_codigo_id,
                    'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
                    'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
                    'sexo' => $reg_animal->tbl_animal_sexo,
                    'ativo' => $reg_animal->tbl_animal_ativo,
                    'situacao' => $desc_situacao,
                    'descSituacao' => $desc_situacao,
                    'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
                    'codigoRaca' => $reg_animal->tbl_animal_codigo_raca,
                    'descricaoRaca' => $descricao_raca,
                    'codigoPelagem' => $codigo_pelagem,
                    'descricaoPelagem' => $descricao_pelagem,
                    'codigoMae' => $codigo_mae_id,
                    'codigoMaeAlfaNumerico' => $codigo_mae_alfa_numerico,
                    'codigoPai' => $codigo_pai_id,
                    'codigoPaiAlfaNumerico' => $codigo_pai_alfa_numerico,
                    'idadeAnimal' => $idade_animal,
                    'codigoCategoria' => $codigo_categoria,
                    'descricaoCategoria' => $desc_categoria,
                    'observacao' => $reg_animal->tbl_animal_observacao,           
                    'codigoOrigem' => $reg_animal->tbl_animal_codigo_origem,
                    'descricaoOrigem' => $descricao_origem,
                    'reprodutor' =>  $reg_animal->tbl_animal_reprodutor,
                    'dataNascimento' => $reg_animal->tbl_animal_data_nascimento,
                    'idadeMeses' => $idade,
                    'primeiroPeso' => $reg_animal->tbl_animal_primeiro_peso,
                    'dataPrimeiroPeso' => $reg_animal->tbl_animal_data_primeiro_peso,
                    'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
                    'dataPesoDesmana' => $reg_animal->tbl_animal_data_desmama,
                    'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
                    'dataUltimoPeso' => $reg_animal->tbl_animal_data_ultimo,
                    'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
                    'incluidoEm' => $incluido_em_edi,
                    'incluidoPor' => $incluido_por,
                    'alteradoEm' =>  $alterado_em_edi,
                    'alteradoPor' => $alterado_por,
                    'baixadoEm' => $baixado_em_edi,
                    'baixadoPor' => $baixado_por,
                    'descarteEm' => $descarte_em_edi,
                    'descartePor' => $descarte_por,                       
                    'grauSangue' =>  $reg_animal->tbl_animal_grau_sangue,
                    'filtroNumPartos' => $filtroNumPartos,
                    'numPartos' => $num_partos,
                    'filtroNumAbortos' => $filtroNumAbortos,
                    'numAbortos' => $num_abortos,
                    'numNatimortos' => $num_natmortos,
                    'dataAbortoNatimorto' => $data_aborto_natimorto,
                    'dataPrevisaoParto' => $data_previsao_parto,
                    'filtroDataPrevisao' => $filtroDataPrevisao,
                    'codigoMovimentacaoCompra' => $movimentacao_compra,
                    'dataCompra' => $data_compra,
                    'origemCompra' => $desc_origem_compra,
                    'destinoCompra' => $desc_destino_compra,
                    'filtroDataParicao' => $filtroDataParicao,
                    'filtroVacasParidas' => $filtroVacasParidas,
                    'filtroVacasSolteiras' => $filtroVacasSolteiras,
                    'filtroDescarte' => $filtroDescarte,
                    'filtroVacasPrenhes' => $filtroVacasPrenhes,
                    'filtroVacasNegativas' => $filtroVacasNegativas,
                    'filtroVacasPositivas' => $filtroVacasPositivas,
                    'selecionadaCobertura' => $selecionadaCobertura,
                    'selecionadaCoberturaControle' => $selecionadaCoberturaControle,
                    'diagnostico' => $diagnostico,
                    'nascido' => $nascido,
                    'idFilho' => $id_filho,
                    'dataNascimentoFilho' => $data_nascimento_filho,
                    'filhoAtivo' => $filho_ativo,
                    'paiFilho' => $pai_filho,
                    'diasNascimentoFilho' => $dias_nascimento,
                    'idEstacao' => $id_estacao,
                    'desc_estacao' => $desc_estacao
                    ];
                    $animais[] = $animalAtual;
                }
            }
        }*/

        $adicionarAnimal = false; // <<< ESSA LINHA É A CHAVE!        

        // Verifica se o array de categorias ($wcategoria) está VAZIO
        if (empty($wcategoria)) {
            // 1. Se o filtro de categoria está vazio/não aplicado, inclui o animal
            $adicionarAnimal = true;
        } else {
            // 2. Se o filtro TEM categorias selecionadas (não está vazio),
            // verifica se a categoria atual ($codigo_categoria) está dentro delas.
            // É importante garantir que $wcategoria seja um array aqui.
            if (is_array($wcategoria) && in_array($codigo_categoria, $wcategoria)) {
                $adicionarAnimal = true;
            }
        }        

        // 2. Define o array do animal UMA ÚNICA VEZ
        $animalAtual = [
            'codigoLocal' => $reg_animal->tbl_animal_codigo_fazenda,
            'nomeFazenda' => $desc_local,
            'codigoAnimal' => $reg_animal->tbl_animal_codigo_id,
            'codigoAlfa' => $reg_animal->tbl_animal_codigo_alfa,
            'codigoNumerico' => intval($reg_animal->tbl_animal_codigo_numerico),
            'sexo' => $reg_animal->tbl_animal_sexo,
            'ativo' => $reg_animal->tbl_animal_ativo,
            'situacao' => $reg_animal->tbl_animal_situacao,
            'descSituacao' => $desc_situacao,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'codigoRaca' => $reg_animal->tbl_animal_codigo_raca,
            'descricaoRaca' => $descricao_raca,
            'codigoPelagem' => $codigo_pelagem,
            'descricaoPelagem' => $descricao_pelagem,
            'codigoMae' => $codigo_mae_id,
            'codigoMaeAlfaNumerico' => $codigo_mae_alfa_numerico,
            'nomeMae' => $reg_animal->tbl_animal_nome_mae,
            'codigoPai' => $codigo_pai_id,
            'codigoPaiAlfaNumerico' => $codigo_pai_alfa_numerico,
            'nomePai' => $reg_animal->tbl_animal_nome_pai,
            'idadeAnimal' => $idade_animal,
            'codigoCategoria' => $codigo_categoria,
            'descricaoCategoria' => $desc_categoria,
            'observacao' => $reg_animal->tbl_animal_observacao,
            'codigoOrigem' => $reg_animal->tbl_animal_codigo_origem,
            'descricaoOrigem' => $descricao_origem,
            'reprodutor' => $reg_animal->tbl_animal_reprodutor,
            'dataNascimento' => $reg_animal->tbl_animal_data_nascimento,
            'idadeMeses' => $idade,
            'primeiroPeso' => $reg_animal->tbl_animal_primeiro_peso,
            'dataPrimeiroPeso' => $reg_animal->tbl_animal_data_primeiro_peso,
            'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
            'dataPesoDesmana' => $reg_animal->tbl_animal_data_desmama,
            'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
            'dataUltimoPeso' => $reg_animal->tbl_animal_data_ultimo,
            'descarte' => $reg_animal->tbl_animal_descarte_reproducao,
            'incluidoEm' => $incluido_em_edi,
            'incluidoPor' => $incluido_por,
            'alteradoEm' => $alterado_em_edi,
            'alteradoPor' => $alterado_por,
            'baixadoEm' => $baixado_em_edi,
            'baixadoPor' => $baixado_por,
            'descarteEm' => $descarte_em_edi,
            'descartePor' => $descarte_por,
            'grauSangue' => $reg_animal->tbl_animal_grau_sangue,
            'filtroNumPartos' => $filtroNumPartos,
            'numPartos' => $num_partos,
            'filtroNumAbortos' => $filtroNumAbortos,
            'numAbortos' => $num_abortos,
            'numNatimortos' => $num_natmortos,
            'dataAbortoNatimorto' => $data_aborto_natimorto,
            'dataPrevisaoParto' => $data_previsao_parto,
            'filtroDataPrevisao' => $filtroDataPrevisao,
            'codigoMovimentacaoCompra' => $movimentacao_compra,
            'dataCompra' => $data_compra,
            'origemCompra' => $desc_origem_compra,
            'destinoCompra' => $desc_destino_compra,
            'filtroDataParicao' => $filtroDataParicao,
            'filtroVacasParidas' => $filtroVacasParidas,
            'filtroVacasSolteiras' => $filtroVacasSolteiras,
            'filtroDescarte' => $filtroDescarte,
            'filtroVacasPrenhes' => $filtroVacasPrenhes,
            'filtroVacasNegativas' => $filtroVacasNegativas,
            'filtroVacasPositivas' => $filtroVacasPositivas,
            'selecionadaCobertura' => $selecionadaCobertura,
            'selecionadaCoberturaControle' => $selecionadaCoberturaControle,
            'diagnostico' => $diagnostico,
            'nascido' => $nascido,
            'idFilho' => $id_filho,
            'dataNascimentoFilho' => $data_nascimento_filho,
            'filhoAtivo' => $filho_ativo,
            'paiFilho' => $pai_filho,
            'diasNascimentoFilho' => $dias_nascimento,
            'idEstacao' => $id_estacao,
            'desc_estacao' => $desc_estacao
        ];

        // 3. Adiciona ao array final somente se o animal atendeu aos filtros
        if ($adicionarAnimal) {
            $animais[] = $animalAtual;
        }        
    }

    $codigos_animais = array_column($animais, 'codigoAnimal');
    $codigos_str = "'" . implode("','", $codigos_animais) . "'";

    $sql_total_pesos = "
        SELECT 
            tbl_ite_pesagem_data_emissao,
            tbl_ite_pesagem_peso,
            tbl_ite_pesagem_codigo_id_animal AS codigo_animal
        FROM tbl_item_pesagem
        WHERE tbl_ite_pesagem_data_emissao >= '$data_inicial'
          AND tbl_ite_pesagem_data_emissao <= '$data_final'
          AND tbl_ite_pesagem_codigo_id_animal IN ($codigos_str)
          AND tbl_ite_pesagem_peso != 0
        ORDER BY tbl_ite_pesagem_data_emissao ASC
    ";

    $tbl_peso_total = mysqli_query($conector, $sql_total_pesos);

    // 4. Cria um array de lookup para os pesos
    $pesos_por_animal = [];
    while ($reg_peso = mysqli_fetch_object($tbl_peso_total)) {
        $codigo = $reg_peso->codigo_animal;
        if (!isset($pesos_por_animal[$codigo])) {
            $pesos_por_animal[$codigo] = [];
        }
        // Armazena todos os pesos para cada animal
        $pesos_por_animal[$codigo][] = $reg_peso;
    }

    foreach ($animais as $animal) {
        $codigo_animal = $animal['codigoAnimal'];
        $dados_pesos_do_animal = $pesos_por_animal[$codigo_animal] ?? [];

        if ($filtro_reproducao == 'S') {
            if ($filtro_negativas=='S' && $filtro_positivas=='S') {
                if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && 
                    $filtro_vacas_prenhas == 'N') { 
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' 
                    && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasParidas']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' &&    $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasPrenhes']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                         $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' &&$filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }                
            }
            else {
                if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && 
                    $filtro_vacas_prenhas == 'N') { 
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' 
                    && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasParidas']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' &&    $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasPrenhes']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                         $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' &&$filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna);

                        $gmd_total = $dados['gmdTotal'];
                        $numero_gmd = $dados['numeroGmd']; 
                        $animais_listados = $dados['animaisListados']; 
                        $linha = $dados['linha']; 
                    }
                }                
            }
        }
        else {
            $dados = listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna); 

            $gmd_total = $dados['gmdTotal'];
            $numero_gmd = $dados['numeroGmd']; 
            $animais_listados = $dados['animaisListados']; 
            $linha = $dados['linha']; 
        }
    }

$coluna = $qtd_meses + 10;

if ($gmd_total != 0 && $numero_gmd > 0) {
    $media_gmd = $gmd_total / $numero_gmd;
    $media_gmd_edi = $media_gmd;
} else {
    $media_gmd_edi = 0;
}

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, $animais_listados);

$celulas = $array_coluna[$coluna] . '5';
$spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, 5, $media_gmd_edi);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="gmd_geral_por_individuo.xlsx"');
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

    function listar($conector, $animal, $array_mes_ano, $array_peso, $data_inicial, $data_final, $qtd_meses, $array_mes, $array_ano, $ano_mes, $gmd_total, $numero_gmd, $dados_pesos_do_animal, $animais_listados, $linha, $spreadsheet, $array_coluna) {

        foreach ($array_mes_ano as $value) { 
            $array_peso[$value]=0;
        } 

        $data_peso_inicial = 0;
        $peso_inicial = 0;
        $data_peso_nascimento=0;
        $peso_nascimento=0;

        $codigo = $animal['codigoAnimal'];
        $ativo = $animal['ativo'];
        $situacao = $animal['situacao'];
        $codigo_alfa = $animal['codigoAlfa'];
        $codigo_numerico = $animal['codigoNumerico'];

        if ($codigo_alfa!='') {
            $codigo_edi = $codigo_alfa.'-'.$codigo_numerico;
        }
        else {
            $codigo_edi = $codigo_numerico;
        } 

        if ($animal['descarte']=='S') {
            $animal_descarte = 'Sim';
        }
        else {
            $animal_descarte = '';
        }
        
        $desc_local = utf8_encode($animal['nomeFazenda']);
        
        if ($animal['sexo']=='M') {
            $sexo = 'Macho';
        }
        else {
            $sexo = 'Femea';
        }

        $nascimento = new DateTime($animal['dataNascimento']);
        $data_nasc_edi = $nascimento->format('d/m/Y');

        $descricao_raca = utf8_encode($animal['descricaoRaca']);
        $descricao_pelagem = utf8_encode($animal['descricaoPelagem']);
        $descricao_mae = $animal['codigoMaeAlfaNumerico'];
        $descricao_pai = utf8_encode($animal['codigoPaiAlfaNumerico']);

        if ($animal['dataPrimeiroPeso']!='') {
            $data_primeiro_peso = substr($animal['dataPrimeiroPeso'], 0, 10);

            if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                $data_peso_nascimento = $data_primeiro_peso;
                $peso_nascimento = $animal['primeiroPeso'];
                    }
        }
        else {
            if ($animal['codigoMovimentacaoCompra']!='') {
                $data_primeiro_peso = $animal['dataCompra'];

                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                    $data_peso_nascimento = $data_primeiro_peso;
                    $peso_nascimento = $animal['primeiroPeso'];
                }
            }
        }

        if ($data_peso_nascimento!=0) {
            $partes = explode("-", $data_peso_nascimento);
            $ano_mes_peso = $partes[0].$partes[1];

            for ($i=0; $i < $qtd_meses; $i++) { 
                if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                    $array_peso[$ano_mes_peso]=$peso_nascimento;
                    $data_peso_inicial = $data_peso_nascimento;
                    $peso_inicial = $peso_nascimento;
                }
            }
        }

        if ($data_peso_inicial==0) {
            $data_peso_inicial='0000-00-00';
            $peso_inicial = 9999;
        }

        $data_peso_final = '0000-00-00';
        $peso_final = 9999;

        if (!empty($dados_pesos_do_animal)) {
            foreach ($dados_pesos_do_animal as $reg_peso) {
                $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                $peso = $reg_peso->tbl_ite_pesagem_peso;

                if ($peso == 0) {
                    $peso = 9999;
                }

                $partes = explode("-", $data_peso_inicial);
                $ano_mes_peso_inicial = $partes[0].$partes[1];

                $partes = explode("-", $data_peso_final);
                $ano_mes_peso_final = $partes[0].$partes[1];

                $partes = explode("-", $data_peso);
                $ano_mes_peso = $partes[0].$partes[1];

                if ($data_peso_inicial=='0000-00-00') {
                    $data_peso_inicial=$data_peso;
                    $peso_inicial=$peso;
                }

                if ($ano_mes_peso_inicial==$ano_mes_peso) {
                    if ($peso_inicial==9999) {
                        if ($peso<$peso_inicial && $peso!=0) {
                            $data_peso_inicial=$data_peso;
                            $peso_inicial = $peso;
                        }
                    }
                }

                if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                    if ($ano_mes_peso_final==$ano_mes_peso) {
                        if ($peso<$peso_final && $peso!=0) {
                            $data_peso_final=$data_peso;
                            $peso_final = $peso;
                        }
                    }
                    else {
                        $data_peso_final=$data_peso;
                        $peso_final = $peso;
                    }
                }

                if ($array_peso[$ano_mes_peso]==0) {
                    $array_peso[$ano_mes_peso]=$peso;
                }
            }

            if ($peso_inicial==9999) {
                $peso_inicial = 0;
            }

            if ($peso_final==9999) {
                $peso_final = 0;
            }

            $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
            $dias = floor($diferenca / (60 * 60 * 24)); 

            if ($peso_final && $peso_inicial) {
                $ganho = $peso_final - $peso_inicial;
            }
            else {
                $ganho = 0;
            }

            if ($ganho!=0 && $dias!=0) {
                $gmd = $ganho / $dias;
                $gmd_edi = number_format($gmd,3,',','.');
                $gmd_total+= $gmd;
                $numero_gmd++;
            }
            else {
                $gmd_edi = 0;
            }

            if ($ganho!=0) {

                $linha++;

                if ($ativo == 'N') {
                    $celulas = 'A' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'B' . $linha . ':E' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $celulas = 'G' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $celulas = 'I' . $linha . ':U' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    if ($situacao=='V') {
                        $celulas = 'A' . $linha . ':V' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_BLUE));
                    }
                    else {
                        $celulas = 'A' . $linha . ':V' . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getFont()->setColor(new Color(Color::COLOR_RED));
                    }

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);

                    if ($animal_descarte=='S') {
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'SIM');
                    }

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_local);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_nasc_edi);

                    $celulas = 'E' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_raca);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_mae);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $descricao_pai);

                    $coluna = 8;
                    foreach ($array_peso as $value) {
                        $coluna++;
                        if ($value > 0 && $value!=9999) {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $value);
                        }
                    }

                    if ($ganho != 0) {
                        $coluna++;
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $ganho);

                        $coluna++;
                        $celulas = $array_coluna[$coluna] . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $gmd);
                    }
                } else {
                    $celulas = 'A' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $celulas = 'B' . $linha . ':E' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $celulas = 'G' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $celulas = 'I' . $linha . ':U' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);

                    if ($animal_descarte=='S') {
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, 'SIM');
                    }

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $desc_local);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $sexo);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $data_nasc_edi);

                    $celulas = 'E' . $linha;
                    $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_raca);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $descricao_mae);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $descricao_pai);

                    $coluna = 8;
                    foreach ($array_peso as $value) {
                        $coluna++;
                        if ($value > 0 && $value!=9999) {
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $value);
                        }
                    }

                    if ($ganho != 0) {
                        $coluna++;
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $ganho);

                        $coluna++;
                        $celulas = $array_coluna[$coluna] . $linha;
                        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
                        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $gmd);
                    }
                }
                $animais_listados++;
            }
        }

        return [
        'gmdTotal' => $gmd_total,
        'numeroGmd' => $numero_gmd,
        'animaisListados' => $animais_listados,
        'linha' => $linha
        ];
    }

