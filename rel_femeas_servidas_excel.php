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

/*$codigo_local = $_REQUEST["local"];
$id_estacao = $_REQUEST["id_estacao"];
$previsao_parto_de = $_REQUEST["previsao_parto_de"];
$previsao_parto_ate = $_REQUEST["previsao_parto_ate"];
$diagnostico = $_REQUEST["diagnostico"];
$descricao_filtro = $_REQUEST["filtro"];
$tipo_cobertura = $_REQUEST["tipo_cobertura"];

if ($previsao_parto_de=='') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-12-31';
}

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
}*/

$codigo_local = $_REQUEST["local"];
$id_estacao = (int)$_REQUEST["id_estacao"];
$previsao_parto_de = $_REQUEST["previsao_parto_de"] ?: '0000-00-00';
$previsao_parto_ate = $_REQUEST["previsao_parto_ate"] ?: '9999-12-31';
$diagnostico = $_REQUEST["diagnostico"];
$tipo_cobertura = $_REQUEST['tipo_cobertura'];
$descricao_filtro = $_REQUEST["filtro"];

// Variáveis de sessão
$_SESSION['cobertura_controle'] = $tipo_cobertura;
$_SESSION['local_cobertura_diagnostico'] = $codigo_local;

$id_estacao_atual = 0;
$tbl_par_query = "SELECT tbl_par_estacao_id FROM tbl_parametro_estacao_monta
    WHERE tbl_par_codigo_local = ? AND 
          tbl_par_lixeira = 0 AND 
          tbl_par_estacao_monta_final >= ? LIMIT 1";

if ($stmt = mysqli_prepare($conector, $tbl_par_query)) {
    mysqli_stmt_bind_param($stmt, "ss", $codigo_local, $data_sistema);
    mysqli_stmt_execute($stmt);
    $result_par = mysqli_stmt_get_result($stmt);

    if ($reg_par = mysqli_fetch_object($result_par)) {
        if ($id_estacao == 0) {
            $id_estacao = $reg_par->tbl_par_estacao_id;
        }
        $id_estacao_atual = $reg_par->tbl_par_estacao_id;
    }
    mysqli_stmt_close($stmt);
} else {
    // Tratar erro na preparação da consulta
    die("Erro ao preparar consulta da estação: " . mysqli_error($conector));
}

/*$wsituacao = "";
$situacao_array = [];
if (isset($_REQUEST['situacao'])) {
    $situacao = utf8_decode($_REQUEST['situacao']);
    if (!in_array("", $situacao)) {
        $situacao_array = array_map(function($s) use ($conector) {
            // Garante que a situação seja tratada como string limpa para o IN
            return trim($s); 
        }, $situacao);
        
        // Remove valores vazios se houver
        //$situacao_array = array_filter($situacao_array); 

        if (!empty($situacao_array)) {
            // Cria placeholders (?) para prepared statement, um para cada item
            $placeholders = implode(',', array_fill(0, count($situacao_array), '?'));
            
            // Verifica se o array continha o espaço em branco (sua lógica original)
            if (in_array(" ", $situacao)) {
                 // Esta lógica é complexa para Prepared Statements dinâmicos. 
                 // Voltarei à concatenação, mas focando na performance de arrays.
                 $wsituacao_list = "'" . implode("','", $situacao_array) . "'";
                 $wsituacao = " AND (tbl_ite_cobertura_nascido IN ($wsituacao_list) OR tbl_ite_cobertura_nascido IS NULL) ";
            } else {
                 $wsituacao_list = "'" . implode("','", $situacao_array) . "'";
                 $wsituacao = " AND tbl_ite_cobertura_nascido IN ($wsituacao_list) ";
            }
        }
    }
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

if ($diagnostico=='P') {
    $nome_relatorio = "Diagnóstico Final - Positivas";
}
else {
    $nome_relatorio = "Diagnóstico Final - Negativas";
}

$registros = 0;

$spreadsheet->getActiveSheet()->mergeCells('A1:J1');
$spreadsheet->getActiveSheet()->mergeCells('K1:L1');
$spreadsheet->getActiveSheet()->mergeCells('B2:L2');
$spreadsheet->getActiveSheet()->mergeCells('C3:L3');

if ($diagnostico=='P') {
    $spreadsheet->getActiveSheet()->mergeCells('I4:J4');
}

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $nome_relatorio)
	->setCellValue("K1", "Data: " . $data_hoje)
	->setCellValue("A2", "Filtro: ")
	->setCellValue("B2", $descricao_filtro)
    ->setCellValue("A3", "Registros: ");

$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setSize(10);

if ($diagnostico=='P') {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Nº Fêmea")
        ->setCellValue("B4","Raça")
        ->setCellValue("C4","Idade (meses)")
        ->setCellValue("D4","Nº Partos")
        ->setCellValue("E4","Nº Coberturas")
        ->setCellValue("F4","Data Serviço")
        ->setCellValue("G4","Semen")
        ->setCellValue("H4","Previsão do Parto")
        ->setCellValue("I4","Confirma Diagnóstico?")
        ->setCellValue("K4","Qtde Diagnóstico")
        ->setCellValue("L4","Nascidos?")
        ->setCellValue("M4","Descarte");
}
else {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A4","Nº Fêmea")
        ->setCellValue("B4","Raça")
        ->setCellValue("C4","Idade (meses)")
        ->setCellValue("D4","Nº Partos")
        ->setCellValue("E4","Nº Coberturas")
        ->setCellValue("F4","Data Serviço")
        ->setCellValue("G4","Semen")
        ->setCellValue("H4","Previsão do Parto")
        ->setCellValue("I4","Descarte");
}

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(19);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(13);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(11);
$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);

$spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('K1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
$spreadsheet->getActiveSheet()->getStyle('A3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
/*$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical($align);*/

$spreadsheet->getActiveSheet()->getStyle('B2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getActiveSheet()->getStyle('B3') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setHorizontal($align);
$align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
$spreadsheet->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setVertical($align);

$spreadsheet->getActiveSheet()->getStyle('C4:K4')->getAlignment()->setWrapText(true);

$linha=4;

/*if ($id_estacao==0) {
    $tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
    WHERE tbl_par_codigo_local='$codigo_local' AND 
          tbl_par_lixeira=0 AND 
          tbl_par_estacao_monta_final>='$data_sistema'");  

    $num_rows = mysqli_num_rows($tbl_par);

    if ($num_rows!=0){
        $reg_para = mysqli_fetch_object($tbl_par);

        $id_estacao = $reg_para->tbl_par_estacao_id;
    }
}

// pega estacao atual
$id_estacao_atual = 0;

$tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
    WHERE tbl_par_codigo_local='$codigo_local' AND 
          tbl_par_lixeira=0 AND 
          tbl_par_estacao_monta_final>='$data_sistema'");  

$num_rows = mysqli_num_rows($tbl_par);

if ($num_rows!=0){
    $reg_para = mysqli_fetch_object($tbl_par);
    $id_estacao_atual = $reg_para->tbl_par_estacao_id;
}*/

$racas_cache = [];
$sql_racas = "SELECT tab_codigo_raca, tab_descricao_raca FROM tabela_racas WHERE tab_registro_lixeira_raca = 0";
$result_racas = mysqli_query($conector, $sql_racas);
while ($reg = mysqli_fetch_object($result_racas)) {
    $racas_cache[$reg->tab_codigo_raca] = $reg->tab_descricao_raca;
}
mysqli_free_result($result_racas);

// Cache de Sêmen
$semen_cache = [];
$sql_semen = "SELECT tbl_semem_codigo_id, tbl_semem_nome FROM tbl_semem";
$result_semen = mysqli_query($conector, $sql_semen);
while ($reg = mysqli_fetch_object($result_semen)) {
    // Sua lógica original usa nome ou nome-nome. Vou armazenar o nome limpo.
    $semen_cache[$reg->tbl_semem_codigo_id] = $reg->tbl_semem_nome;
}
mysqli_free_result($result_semen);

// Cache de Protocolo IATF
$protocolos_iatf_cache = [];
$sql_iatf = "SELECT tbl_protocoloiatf_id, tbl_protocoloiatf_dias_diagnostico, tbl_protocoloiatf_tipo FROM tbl_protocoloiatf WHERE tbl_protocoloiatf_lixeira = 0";
$result_iatf = mysqli_query($conector, $sql_iatf);
while ($reg = mysqli_fetch_object($result_iatf)) {
    $protocolos_iatf_cache[$reg->tbl_protocoloiatf_id] = $reg;
}
mysqli_free_result($result_iatf);

// Cache de Itens do Protocolo IATF (para cálculo da data de serviço)
$itens_iatf_cache = [];
$sql_itens_iatf = "SELECT tbl_ite_protocoloiatf_protocolo_id, tbl_ite_protocoloiatf_descricao FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_lixeira = 0 ORDER BY tbl_ite_protocoloiatf_id ASC";
$result_itens_iatf = mysqli_query($conector, $sql_itens_iatf);
while ($reg = mysqli_fetch_object($result_itens_iatf)) {
    // Supondo que o último item é o que tem a data de serviço desejada
    $itens_iatf_cache[$reg->tbl_ite_protocoloiatf_protocolo_id] = $reg;
}
mysqli_free_result($result_itens_iatf);

$coberturas_por_animal_cache = [];
$sql_coberturas_count = "
    SELECT 
        IC.tbl_ite_cobertura_codigo_id_animal AS animal_id,
        COUNT(IC.tbl_ite_cobertura_numero_id) AS total_coberturas
    FROM tbl_item_cobertura AS IC
    INNER JOIN tbl_cobertura AS C ON IC.tbl_ite_cobertura_numero_id = C.tbl_cobertura_id
    WHERE C.tbl_cobertura_lixeira = 0
      AND C.tbl_cobertura_codigo_local = ?
      AND C.tbl_cobertura_controle = 'C'
      AND C.tbl_cobertura_codigo_estacao_monta = ?
    GROUP BY IC.tbl_ite_cobertura_codigo_id_animal";

if ($stmt_count = mysqli_prepare($conector, $sql_coberturas_count)) {
    mysqli_stmt_bind_param($stmt_count, "si", $codigo_local, $id_estacao);
    mysqli_stmt_execute($stmt_count);
    $result_count = mysqli_stmt_get_result($stmt_count);

    while ($reg_count = mysqli_fetch_object($result_count)) {
        $coberturas_por_animal_cache[(int)$reg_count->animal_id] = (int)$reg_count->total_coberturas;
    }
    mysqli_stmt_close($stmt_count);
}

// 5. Consulta Principal Condicionalmente Otimizada

if ($diagnostico == 'N') {
    // SE DIAGNÓSTICO NEGATIVO ('N'): Traz o animal SOMENTE se o ÚLTIMO evento DELE na estação for 'N'.
    
    // Subconsulta para encontrar o ID da Cobertura mais recente (máximo C.tbl_cobertura_id) 
    // para CADA animal na estação. (Etapa 1)
    $sql_principal = "
        SELECT 
            A.tbl_animal_codigo_id AS animal_id,
            A.tbl_animal_codigo_alfa AS codigo_alfa,
            A.tbl_animal_codigo_numerico AS codigo_numerico,
            A.tbl_animal_codigo_raca AS codigo_raca,
            A.tbl_animal_data_nascimento AS data_nascimento,
            A.tbl_animal_descarte_reproducao AS descarte_reproducao,
            A.tbl_animal_ativo AS ativo,
            
            IC.tbl_ite_cobertura_numero_id AS cobertura_id,
            IC.tbl_ite_cobertura_numero_item AS numero_item,
            IC.tbl_ite_cobertura_codigo_id_animal AS id_animal,
            IC.tbl_ite_cobertura_codigo_animal AS codigo_animal,
            IC.tbl_ite_cobertura_resultado_diagnostico AS diagnostico_animal,
            IC.tbl_ite_cobertura_qtd_diagnosticos_positivo AS qtd_diagnosticos,
            IC.tbl_ite_cobertura_nascido AS nascido,
            IC.tbl_ite_cobertura_situacao_femea_nascido_outro AS nascido_outro,
            IC.tbl_ite_cobertura_codigo_touro_semen AS touro_semem,
            
            C.tbl_cobertura_protocoloiatf AS protocolo_id,
            PC.tbl_protocolo_cobertura_data AS data_protocolo_cobertura
            
        FROM tbl_item_cobertura AS IC
        INNER JOIN tbl_cobertura AS C ON IC.tbl_ite_cobertura_numero_id = C.tbl_cobertura_id
        INNER JOIN tbl_animais AS A ON IC.tbl_ite_cobertura_codigo_id_animal = A.tbl_animal_codigo_id
        INNER JOIN tbl_protocolo_cobertura AS PC ON PC.tbl_protocolo_cobertura_codigo_id = C.tbl_cobertura_id
        
        INNER JOIN (
            -- Subconsulta que encontra a ULTIMA Cobertura ID para cada animal na estação
            SELECT 
                IC_MAX.tbl_ite_cobertura_codigo_id_animal, 
                MAX(IC_MAX.tbl_ite_cobertura_numero_id) AS max_cobertura_id
            FROM tbl_item_cobertura AS IC_MAX
            INNER JOIN tbl_cobertura AS C_MAX ON IC_MAX.tbl_ite_cobertura_numero_id = C_MAX.tbl_cobertura_id
            WHERE C_MAX.tbl_cobertura_lixeira = 0
              AND C_MAX.tbl_cobertura_codigo_local = ?                 -- Parâmetro 1: codigo_local
              AND C_MAX.tbl_cobertura_controle = 'C'
              AND C_MAX.tbl_cobertura_codigo_estacao_monta = ?        -- Parâmetro 2: id_estacao
              AND IC_MAX.tbl_ite_cobertura_dia_1 = 'S'
            GROUP BY IC_MAX.tbl_ite_cobertura_codigo_id_animal
        ) AS UltimaCobertura ON 
            IC.tbl_ite_cobertura_codigo_id_animal = UltimaCobertura.tbl_ite_cobertura_codigo_id_animal AND
            IC.tbl_ite_cobertura_numero_id = UltimaCobertura.max_cobertura_id

        WHERE C.tbl_cobertura_lixeira = 0
          AND A.tbl_animal_sexo = 'F'
          AND C.tbl_cobertura_codigo_local = ?                 -- Parâmetro 3: codigo_local
          AND C.tbl_cobertura_controle = 'C'
          AND C.tbl_cobertura_codigo_estacao_monta = ?         -- Parâmetro 4: id_estacao
          AND IC.tbl_ite_cobertura_dia_1 = 'S'
          AND IC.tbl_ite_cobertura_resultado_diagnostico = ?   -- Parâmetro 5: diagnostico ('N')
          {$wsituacao}
          
        ORDER BY A.tbl_animal_codigo_numerico ASC";
    // Parâmetros para a Subconsulta (2) + Parâmetros para a Consulta Externa (3)
    $bind_params = [$codigo_local, $id_estacao, $codigo_local, $id_estacao, $diagnostico];
    $bind_types = "sisis"; // s:string, i:integer (x2), s:string (x2)
    
} 
else {
    // SE DIAGNÓSTICO POSITIVO ('P') ou OUTRO: Mantém a consulta original
$sql_principal = "
    SELECT 
        A.tbl_animal_codigo_id AS animal_id,
        A.tbl_animal_codigo_alfa AS codigo_alfa,
        A.tbl_animal_codigo_numerico AS codigo_numerico,
        A.tbl_animal_codigo_raca AS codigo_raca,
        A.tbl_animal_data_nascimento AS data_nascimento,
        A.tbl_animal_descarte_reproducao AS descarte_reproducao,
        A.tbl_animal_ativo AS ativo,
        
        IC.tbl_ite_cobertura_numero_id AS cobertura_id,
        IC.tbl_ite_cobertura_numero_item AS numero_item,
        IC.tbl_ite_cobertura_codigo_id_animal AS id_animal,
        IC.tbl_ite_cobertura_codigo_animal AS codigo_animal,
        IC.tbl_ite_cobertura_resultado_diagnostico AS diagnostico_animal,
        IC.tbl_ite_cobertura_qtd_diagnosticos_positivo AS qtd_diagnosticos,
        IC.tbl_ite_cobertura_nascido AS nascido,
        IC.tbl_ite_cobertura_situacao_femea_nascido_outro AS nascido_outro,
        IC.tbl_ite_cobertura_codigo_touro_semen AS touro_semem,
        
        C.tbl_cobertura_protocoloiatf AS protocolo_id,
        PC.tbl_protocolo_cobertura_data AS data_protocolo_cobertura
        
    FROM tbl_item_cobertura AS IC
    INNER JOIN tbl_cobertura AS C ON IC.tbl_ite_cobertura_numero_id = C.tbl_cobertura_id
    INNER JOIN tbl_animais AS A ON IC.tbl_ite_cobertura_codigo_id_animal = A.tbl_animal_codigo_id
    INNER JOIN tbl_protocolo_cobertura AS PC ON PC.tbl_protocolo_cobertura_codigo_id = C.tbl_cobertura_id
    
    WHERE C.tbl_cobertura_lixeira = 0
      AND A.tbl_animal_sexo = 'F'
      AND C.tbl_cobertura_codigo_local = ?
      AND C.tbl_cobertura_controle = 'C'
      AND C.tbl_cobertura_codigo_estacao_monta = ?
      AND IC.tbl_ite_cobertura_dia_1 = 'S'
      AND IC.tbl_ite_cobertura_resultado_diagnostico = ?
      {$wsituacao}
      
    ORDER BY A.tbl_animal_codigo_numerico ASC, C.tbl_cobertura_id DESC";
    $bind_params = [$codigo_local, $id_estacao, $diagnostico];
    $bind_types = "sis"; // s:string, i:integer, s:string
}

$registros_cobertura = [];

if ($stmt = mysqli_prepare($conector, $sql_principal)) {
    
    // Constrói o array de argumentos, começando pela string de tipos
    // $bind_types e $bind_params são definidos nos blocos IF/ELSE
    $bind_args = array_merge([$bind_types], $bind_params);
    
    // --- INÍCIO DA CORREÇÃO ---
    // 1. Cria um array de referências para os argumentos de bind
    $refs = [];
    foreach ($bind_args as $key => $value) {
        // O primeiro elemento (índice 0) é a string de tipos e não precisa de referência
        if ($key > 0) {
            // A partir do segundo elemento (índice 1) são as variáveis, 
            // e elas precisam ser referências
            $refs[$key] = &$bind_args[$key];
        } else {
             // O índice 0 (string de tipos)
             $refs[$key] = $value;
        }
    }
    
    // 2. Chama a função mysqli_stmt_bind_param dinamicamente, passando $stmt e as referências
    // A função precisa ser chamada com $stmt como primeiro argumento, 
    // seguido pela string de tipos (que está em $refs[0]) e as variáveis (que estão em $refs[1] em diante).
    
    // Para simplificar a chamada do array_merge no call_user_func_array:
    // $refs agora contém [0] => string de tipos, [1] => &$var1, [2] => &$var2, etc.
    // Vamos adicionar $stmt no início para a chamada correta.
    array_unshift($refs, $stmt);

    // Agora $refs está na ordem correta: [$stmt, $bind_types, &$var1, &$var2, ...]
    
    call_user_func_array('mysqli_stmt_bind_param', $refs);
    
    // --- FIM DA CORREÇÃO ---
    
    mysqli_stmt_execute($stmt);
    $result_principal = mysqli_stmt_get_result($stmt);

    while ($reg_cobertura = mysqli_fetch_object($result_principal)) {
        $registros_cobertura[] = $reg_cobertura;
    }
    mysqli_stmt_close($stmt);
} 
else {
    die("Erro ao preparar consulta principal: " . mysqli_error($conector));
}

$tem_thead = 'N';

foreach ($registros_cobertura as $reg_cobertura) {
    // Extração de dados (sem consultas SQL!)
    $protocolo_id = $reg_cobertura->protocolo_id;
    $codigo_id = $reg_cobertura->animal_id;
    $codigo_animal = $reg_cobertura->codigo_animal;
    $codigo_raca = $reg_cobertura->codigo_raca;
    $data_nascimento = $reg_cobertura->data_nascimento;
    $descarte_reproducao = $reg_cobertura->descarte_reproducao;
    $ativo = $reg_cobertura->ativo;
    $codigo_alfa = $reg_cobertura->codigo_alfa;
    $codigo_numerico = intval($reg_cobertura->codigo_numerico);

    if ($codigo_alfa=='') {
        $codigo_edi = $codigo_numerico;
    }
    else {
        $codigo_edi = $codigo_alfa . '-' . $codigo_numerico;
    }

    $codigo_raca_padded = str_pad($codigo_raca, 3, '0', STR_PAD_LEFT); 
    $desc_raca = $racas_cache[$codigo_raca_padded] ?? '';
    
    $protocolo_id_padded = str_pad($protocolo_id, 9, '0', STR_PAD_LEFT); 
    $reg_protocolo_iatf = $protocolos_iatf_cache[$protocolo_id_padded] ?? null;

    if ($reg_protocolo_iatf) {
        $qtd_diagnosticos = $reg_cobertura->qtd_diagnosticos;
        $nascido = $reg_cobertura->nascido;
        $nascido_outro = $reg_cobertura->nascido_outro;
        $cobertura_id = str_pad($reg_cobertura->cobertura_id, 9, '0', STR_PAD_LEFT);
        $numero_item = str_pad($reg_cobertura->numero_item, 3, '0', STR_PAD_LEFT);
        $touro_semem = $reg_cobertura->touro_semem;
        $cobertura_ordem = $cobertura_id . $numero_item;
        $num_coberturas = $coberturas_por_animal_cache[(int)$codigo_id] ?? null;

        // 6.1. Cálculo de Partos (AINDA É UMA CONSULTA PONTUAL)
        // Isso pode ser otimizado para um cache se houver muitos animais.
        $tbl_filhos = mysqli_query($conector,"SELECT count(*) as numero_partos FROM tbl_animais WHERE tbl_animal_codigo_mae='$codigo_id'");
        $numero_partos = mysqli_fetch_object($tbl_filhos)->numero_partos;
        mysqli_free_result($tbl_filhos);
        
        // 6.2. Descrição de Sêmen (DO CACHE)
        $desc_semen = $semen_cache[$touro_semem] ?? '';
        
        // 6.3. Descrição de Nascido
        $desc_nascido = '';
        switch ($nascido) {
            case 'N': $desc_nascido = 'Sim'; break;
            case 'A': $desc_nascido = 'Aborto'; break;
            case 'M': $desc_nascido = 'Natimorto'; break;
            case 'O': 
                switch ($nascido_outro) {
                    case 'V': $desc_nascido = 'Fêmea Vendida'; break;
                    case 'M': $desc_nascido = 'Fêmea Morreu'; break;
                    default: $desc_nascido = 'Fêmea Outra Saída'; break;
                }
                break;
        }

        // 6.4. Cálculo de Datas e Idade
        $dias_previsao_parto = 282; // Constante

        $protocolo_id_padded = str_pad($protocolo_id, 9, '0', STR_PAD_LEFT); 
        $reg_itens = $itens_iatf_cache[$protocolo_id_padded] ?? null;

        if ($reg_itens) {

            // Se o campo tbl_ite_protocoloiatf_descricao é tipo '+X dias'
            $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
            
            // Reutiliza a data da protocolo cobertura (já trazida pelo JOIN)
            $data_base_cobertura = $reg_cobertura->data_protocolo_cobertura; 

            // Cálculo da Data de Serviço
            $data_servico = date("Y-m-d", strtotime($data_base_cobertura . "+{$dias} days"));
            
            // Cálculo da Previsão do Parto
            $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));

            // Cálculo da Idade (Igual ao seu original)
            $date_nasc = new DateTime($data_nascimento); 
            $date_servico = new DateTime($data_servico);
            $idade_acompanhamento = $date_nasc->diff($date_servico);
            $idade = ($idade_acompanhamento->format('%Y') * 12) + $idade_acompanhamento->format('%m');
        } else {
             // Tratamento se não encontrar o item do protocolo
             $data_servico = 'N/A';
             $data_previsao_parto = 'N/A';
             $idade = 0;
        }

        // 6.5. Verificação da Estação Atual (AINDA É UMA CONSULTA PONTUAL)
        $num_rows_coberturas_atual = 0;

        if ($diagnostico == "N") {
            // Esta consulta pode ser mantida, mas seria melhor buscar todas de uma vez no cache.
            // Para simplicidade, mantive a lógica original, mas o impacto é menor pois só ocorre para 'N'.
            $sql_cobertura_atual = "SELECT 1 FROM tbl_item_cobertura
                INNER JOIN tbl_cobertura ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = ? AND
                      tbl_cobertura_codigo_local = ? AND
                      tbl_cobertura_controle = 'C' AND
                      tbl_cobertura_codigo_estacao_monta = ?
                ORDER BY tbl_cobertura_id DESC LIMIT 1";

            if ($stmt_atual = mysqli_prepare($conector, $sql_cobertura_atual)) {
                mysqli_stmt_bind_param($stmt_atual, "ssi", $codigo_id, $codigo_local, $id_estacao_atual);
                mysqli_stmt_execute($stmt_atual);
                mysqli_stmt_store_result($stmt_atual);
                $num_rows_coberturas_atual = mysqli_stmt_num_rows($stmt_atual);
                mysqli_stmt_close($stmt_atual);
            }
        }

        // Formato para exibição
        $data_servico_ed = date("d/m/Y", strtotime(str_replace('-','/', $data_servico)));
        $data_previsao_parto_ed = date("d/m/Y", strtotime(str_replace('-','/', $data_previsao_parto)));


        $data_servico_ed = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_servico_ed);                                             
        $data_previsao_parto_ed = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_previsao_parto_ed);
        
        // 6.6. Renderização (Lógica de exibição permanece a mesma)

        if ($data_previsao_parto >= $previsao_parto_de && $data_previsao_parto <= $previsao_parto_ate) {

            $linha++;
            $registros++;

            $celulas = 'A'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $celulas = 'C'.$linha.':F'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $celulas = 'H'.$linha.':K'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_raca);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $idade);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_partos);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $num_coberturas);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $data_servico_ed);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $desc_semen);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_previsao_parto_ed);

            $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

            $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

            $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

            if ($diagnostico=='P') {
                if ($nascido!='N' && $nascido!='A' && $nascido!='M' && $nascido!='O') {
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, 'Sim(   )');
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, 'Não(   )');
                }

                $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $qtd_diagnosticos);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $desc_nascido);
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $descarte_reproducao);
                $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));
                $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            else {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $descarte_reproducao);
                $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));
                $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        }
    }
} // Fim do loop principal (mais rápido)




/*$tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_sexo = 'F'
    ORDER BY tbl_animal_codigo_numerico ASC");

while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
    $codigo_id = $reg_animal->tbl_animal_codigo_id;
    $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
    $codigo_numerico = intval($reg_animal->tbl_animal_codigo_numerico);

    $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
    $data_nascimento= $reg_animal->tbl_animal_data_nascimento;
    $descarte_reproducao = $reg_animal->tbl_animal_descarte_reproducao; 
    $data_baixa = $reg_animal->tbl_animal_baixado_em;
    $ativo = $reg_animal->tbl_animal_ativo;

    // Pega numero de coberturas na estação
    $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        WHERE tbl_cobertura_lixeira=0 AND
              tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
              tbl_cobertura_codigo_local = '$codigo_local' AND 
              tbl_cobertura_controle = 'C' AND 
              tbl_cobertura_codigo_estacao_monta = '$id_estacao' AND 
              tbl_ite_cobertura_dia_1 = 'S'" . $wsituacao); 

    $num_coberturas = mysqli_num_rows($tbl_cobertura);

    $sql = "SELECT * FROM tbl_item_cobertura 
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
              tbl_cobertura_codigo_local = '$codigo_local' AND 
              tbl_cobertura_controle = 'C' AND 
              tbl_cobertura_codigo_estacao_monta = '$id_estacao' AND 
              tbl_ite_cobertura_dia_1 = 'S' " . $wsituacao .
        "ORDER BY tbl_cobertura_id DESC LIMIT 1"; 

    $tbl_cobertura = mysqli_query($conector, $sql);
    $num_rows_coberturas = mysqli_num_rows($tbl_cobertura);

    while($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
        $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
        $diagnostico_animal = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

        //echo 'Animal: ' . $codigo_numerico . ' Diagnostico: ' . $diagnostico_animal . '</br>';

        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
            WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                  tbl_protocoloiatf_lixeira = 0");

        $num_rows_iatf = mysqli_num_rows($sql);  

        if ($num_rows_iatf!=0) {
            $reg_protocolo_iatf = mysqli_fetch_object($sql);
            $protocoloiatf_tipo = $reg_protocolo_iatf->tbl_protocoloiatf_tipo;

            //for ($i=0; $i < count($tipo_cobertura) ; $i++) { 
                //if ($protocoloiatf_tipo == $tipo_cobertura[$i]) {

                    if ($diagnostico_animal==$diagnostico) {
                        $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                        
                        $numero_item = $reg_cobertura->tbl_ite_cobertura_numero_item;
                        $id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
                        $codigo_animal = $reg_cobertura->tbl_ite_cobertura_codigo_animal;
                        
                        $qtd_diagnosticos = intval($reg_cobertura->tbl_ite_cobertura_qtd_diagnosticos_positivo);

                        $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
                        $nascido_outro = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

                        if ($codigo_alfa=='') {
                            $codigo_edi = intval($codigo_numerico);
                        }
                        else {
                            $codigo_edi = $codigo_alfa . '-' . intval($codigo_numerico);
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

                        $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                            where tbl_animal_codigo_mae='$id_animal'
                            order by tbl_animal_codigo_numerico ASC"); 
                            
                        $numero_partos = mysqli_num_rows($tbl_filhos);

                        $touro_semem = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                        if ($touro_semem!='') {
                            $semen = mysqli_query($conector, "select * from tbl_semem 
                                where tbl_semem_codigo_id='$touro_semem'"); 
                            
                            $reg_semen = mysqli_fetch_object($semen);

                            $num_rows = mysqli_num_rows($semen);

                            if ($num_rows!=0) {
                                if($reg_semen->tbl_semem_nome == ""){
                                    $desc_semen = utf8_encode($reg_semen->tbl_semem_nome);
                                }
                                else {
                                    $desc_semen = utf8_encode($reg_semen->tbl_semem_nome) .'-'. utf8_encode($reg_semen->tbl_semem_nome);
                                }
                            }
                            else {
                                $desc_semen = '';
                            }
                        }
                        else {
                            $desc_semen = '';   
                        }

                        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                            WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
                            
                        $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
                            WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                                  tbl_protocoloiatf_lixeira = 0");
                            
                        $reg_protocolo_iatf = mysqli_fetch_object($sql);

                        $dias_diagnostico = $reg_protocolo_iatf->tbl_protocoloiatf_dias_diagnostico;

                        $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                            WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                                  tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                            ORDER BY tbl_ite_protocoloiatf_id ASC");
                            
                        $dias_previsao_parto = 282;

                        while($reg_itens = mysqli_fetch_object($sql)){
                            $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
                            $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
                            $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                        }

                        $data_servico = date("d/m/Y", strtotime(str_replace('-', '/', $data_servico)));
                        $data_previsao_parto_ed = date("d/m/Y", strtotime(str_replace('-', '/', $data_previsao_parto)));
                        $cobertura_ordem = $cobertura_id . $numero_item;

                        // calcula a idade pela data do serviço conforme o trello (CORREÇÕES DA REPRODUÇÃO) 12/01/2024
                        $data_acompanhamento_calculo = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days")); // ESSA É A DATA DO SERVIÇO

                        $date = new DateTime($data_nascimento); // Data de Nascimento
                        
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        // para diagnostico negativo, verifica se o animal esta tambem na estacao atual

                        $num_rows_coberturas_atual = 0;

                        if ($diagnostico == "N") {

                            $tbl_cobertura_atual = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                                WHERE tbl_cobertura_lixeira=0 AND
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                                      tbl_cobertura_codigo_local = '$codigo_local' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_cobertura_codigo_estacao_monta = '$id_estacao_atual'
                                ORDER BY tbl_cobertura_id DESC LIMIT 1"); 

                            $num_rows_coberturas_atual = mysqli_num_rows($tbl_cobertura_atual);
                        }

                        $data_servico_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_servico);                                             
                        $data_previsao_parto_ed = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_previsao_parto_ed);

                        if ($data_previsao_parto>=$previsao_parto_de && 
                            $data_previsao_parto<=$previsao_parto_ate) {
                        
                            $linha++;
                            $registros++;

                            $celulas = 'A'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                            $celulas = 'C'.$linha.':F'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                            $celulas = 'H'.$linha.':K'.$linha;
                            $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $desc_raca);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $idade);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $numero_partos);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $num_coberturas);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $data_servico_edi);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $desc_semen);
                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $data_previsao_parto_ed);

                            $spreadsheet->getActiveSheet()->getStyle('F'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                            $spreadsheet->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('DD/MM/YYYY');

                            $spreadsheet->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                            if ($diagnostico=='P') {
                                if ($nascido!='N' && $nascido!='A' && $nascido!='M' && $nascido!='O') {
                                //if ($nascido=='N' || $nascido=='A' || $nascido=='M' || $nascido=='O') {

                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, 'Sim(   )');
                                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, 'Não(   )');
                                }

                                $spreadsheet->getActiveSheet()->getStyle('K'.$linha)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $qtd_diagnosticos);
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $desc_nascido);
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $descarte_reproducao);
                                $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));
                                $spreadsheet->getActiveSheet()->getStyle('M'.$linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            }
                            else {
                                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $descarte_reproducao);
                                $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));
                                $spreadsheet->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            }
                        }
                    }
              //  }       
            //}
        }
    }
}*/

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


/*    while($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
        $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
        $diagnostico_animal = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
            WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                  tbl_protocoloiatf_lixeira = 0");
                
        $reg_protocolo_iatf = mysqli_fetch_object($sql);

        $protocoloiatf_tipo = $reg_protocolo_iatf->tbl_protocoloiatf_tipo;

        for ($i=0; $i < count($tipo_cobertura) ; $i++) { 
            if ($protocoloiatf_tipo == $tipo_cobertura[$i]) {

                if ($diagnostico_animal==$diagnostico) {
                    $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                    
                    $numero_item = $reg_cobertura->tbl_ite_cobertura_numero_item;
                    $id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
                    $codigo_animal = $reg_cobertura->tbl_ite_cobertura_codigo_animal;
                    
                    $qtd_diagnosticos = intval($reg_cobertura->tbl_ite_cobertura_qtd_diagnosticos_positivo);

                    $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
                    $nascido_outro = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

                    if ($codigo_alfa=='') {
                        $codigo_edi = intval($codigo_numerico);
                    }
                    else {
                        $codigo_edi = $codigo_alfa . '-' . intval($codigo_numerico);
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
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    $tbl_filhos = mysqli_query($conector,"select * from tbl_animais 
                        where tbl_animal_codigo_mae='$id_animal'
                        order by tbl_animal_codigo_numerico ASC"); 
                        
                    $numero_partos = mysqli_num_rows($tbl_filhos);

                    $touro_semem = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    if ($touro_semem!='') {
                        $semen = mysqli_query($conector, "select * from tbl_semem 
                            where tbl_semem_codigo_id='$touro_semem'"); 
                        
                        $reg_semen = mysqli_fetch_object($semen);

                        $num_rows = mysqli_num_rows($semen);

                        if ($num_rows!=0) {
                            if($reg_semen->tbl_semem_nome == ""){
                                $desc_semen = $reg_semen->tbl_semem_nome;
                            }
                            else {
                                $desc_semen = $reg_semen->tbl_semem_nome .'-'. utf8_encode($reg_semen->tbl_semem_nome);
                            }
                        }
                        else {
                            $desc_semen = '';
                        }
                    }
                    else {
                        $desc_semen = '';   
                    }

                    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                        WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
                        
                    $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
                        WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                              tbl_protocoloiatf_lixeira = 0");
                        
                    $reg_protocolo_iatf = mysqli_fetch_object($sql);

                    $dias_diagnostico = $reg_protocolo_iatf->tbl_protocoloiatf_dias_diagnostico;

                    $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                        WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                              tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                        ORDER BY tbl_ite_protocoloiatf_id ASC");
                        
                    $dias_previsao_parto = 282;

                    while($reg_itens = mysqli_fetch_object($sql)){
                        $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);
                        $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));
                        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                    }

                    $data_servico = date("d/m/Y", strtotime(str_replace('-', '/', $data_servico)));
                    $data_previsao_parto_ed = date("d/m/Y", strtotime(str_replace('-', '/', $data_previsao_parto)));

                    $data_servico_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_servico);                                             
                    $data_previsao_parto_ed = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_previsao_parto_ed);

                    $cobertura_ordem = $cobertura_id . $numero_item;
*/



?>