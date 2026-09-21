<?php
    include "conecta_mysql.inc";

$lidos = 0; 

$codigo_alfa_numerico = $_POST["codigo_alfa_numerico"];
$num_parto_de = $_POST['num_parto_de'];
$num_parto_ate = $_POST['num_parto_ate'];
$num_aborto_de = $_POST['num_aborto_de'];
$num_aborto_ate = $_POST['num_aborto_ate'];
$num_natimorto_de = $_POST['num_natimorto_de'];
$num_natimorto_ate = $_POST['num_natimorto_ate'];
$previsao_parto_de = $_POST["previsao_parto_de"];
$previsao_parto_ate = $_POST["previsao_parto_ate"];
$data_paricao_de = $_POST["data_paricao_de"];
$data_paricao_ate = $_POST["data_paricao_ate"];
$filtro_reproducao = $_POST["filtro_reproducao"];
$filtro_num_parto = $_POST['filtro_num_parto'];
$filtro_num_aborto = $_POST['filtro_num_aborto'];
$filtro_num_natimorto = $_POST['filtro_num_natimorto'];
$filtro_previsao_parto = $_POST['filtro_previsao_parto'];
$filtro_data_paricao = $_POST['filtro_data_paricao'];
$filtro_vacas_paridas = $_POST['filtro_vacas_paridas'];
$filtro_vacas_solteiras = $_POST['filtro_vacas_solteiras'];
$filtro_vacas_prenhas = $_POST['filtro_vacas_prenhas'];
$filtro_descarte = $_POST["filtro_descarte"];
$filtro_positivas = $_POST["filtro_positivas"];
$filtro_negativas = $_POST["filtro_negativas"];
$filtro_monta_natural = $_POST["filtro_monta_natural"];
$filtro_iatf = $_POST["filtro_iatf"];

// Inicia campos vazios 
if ($num_parto_de=='') {
    $num_parto_de = 0;
    $num_parto_ate = 999;
}

if ($num_aborto_de=='') {
    $num_aborto_de = 0;
    $num_aborto_ate = 999;
}

if ($num_natimorto_de=='') {
    $num_natimorto_de = 0;
    $num_natimorto_ate = 999;
}

$array_codigos_estacao = [];
$westacao = "";

if (isset($_POST['filtro_estacao']) && is_array($_POST['filtro_estacao']) && !empty($_POST['filtro_estacao'])) {
    
    $estacoes = $_POST['filtro_estacao']; 

    $estacoes_formatadas = array_map(function($estacao) use ($conector) {
        $estacao_limpa = trim($estacao); 
        return "'" . mysqli_real_escape_string($conector, $estacao_limpa) . "'";
    }, $estacoes);

    $in_estacoes = implode(',', $estacoes_formatadas);
    
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

$wlocal='';
$wlocal_anterior = '';

if (isset($_POST['local'])) {
    $local = $_POST['local'];

    if(in_array("", $local)) {
        $wlocal='';
        $wlocal_anterior = '';
    }
    else {
        $wlocal = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal.= implode(',', $local);
        $wlocal.= ")";

        $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
        $wlocal_anterior.= implode(',', $local);
        $wlocal_anterior.= ")";
        $wlocal_anterior.= " OR (tbl_animal_codigo_origem IN(";
        $wlocal_anterior.= implode(',', $local);
        $wlocal_anterior.= ") AND tbl_animal_situacao='V'))";
    }
}

$worigem='';

if (isset($_POST['origem'])) {
    $origem = $_POST['origem'];

    if(in_array("", $origem)) {
        $worigem='';
    }
    else {
        $worigem = " AND tbl_animal_codigo_origem IN(";
        $worigem.= implode(',', $origem);
        $worigem.= ")";
    }
}

$wsexo = "";
if (isset($_POST['sexo'])) {
    $sexo = $_POST['sexo'];

    if(in_array("Todos", $sexo)) {
        $wsexo='';
    }
    else {
        $wsexo = " AND tbl_animal_sexo IN(";
        $wsexo .= "'" . implode("','", $sexo) . "'";
        $wsexo.= ")";
    }
}

$wmae = "";

if (isset($_POST['codigos_maes'])) {
    $mae = $_POST['codigos_maes'];

    if(in_array("", $mae)) {
        $wmae='';
    }
    else {
        $wmae = " AND tbl_animal_codigo_mae IN(";
        $wmae.= implode(',', $mae);
        $wmae.= ")";
    }
}

$wpai = "";

if (isset($_POST['codigos_pais'])) {
    $pai = $_POST['codigos_pais'];

    if(in_array("", $pai)) {
        $wpais='';
    }
    else {
        $wpai = " AND tbl_animal_codigo_pai IN(";
        $wpai.= implode(',', $pai);
        $wpai.= ")";
    }
}

$wraca = "";

if (isset($_POST['codigos_racas'])) {
    $raca = $_POST['codigos_racas'];

    if(in_array("", $raca)) {
        $wraca='';
    }
    else {
        $wraca = " AND tbl_animal_codigo_raca IN(";
        $wraca.= implode(',', $raca);
        $wraca.= ")";
    }
}

$data_nasc_inicial = $_POST["data_nasc_inicial"];
$data_nasc_final = $_POST["data_nasc_final"];

if ($data_nasc_inicial==0 && $data_nasc_final==0){
    $wdata_nasc = '';
}
else {
    $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
}

$situacao_vendido = $_POST['situacao_vendido'];
$situacao_morte = $_POST['situacao_morte'];
$situacao_outra = $_POST['situacao_outra'];
$ativo_filtro = $_POST['ativo'];

if ($ativo_filtro=='Todos') {
    $wativo='';
}
else {
    $wativo = " AND tbl_animal_ativo IN(";
    $wativo .= "'" . $ativo_filtro . "'";
    $wativo.= ")";
}

$peso_nasc_inicial = $_POST["peso_nasc_inicial"];
$peso_nasc_final = $_POST["peso_nasc_final"];

$peso_desmama_inicial = $_POST["peso_desmama_inicial"];
$peso_desmama_final = $_POST["peso_desmama_final"];

$peso_ult_inicial = $_POST["peso_ult_inicial"];
$peso_ult_final = $_POST["peso_ult_final"];

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

$wcategoria = "";

if (isset($_POST['categoria'])) {
    $categoria_filtro = $_POST['categoria'];

    if(in_array("", $categoria_filtro)) {
        $wcategoria='';
    }
    else {
        $wcategoria= $categoria_filtro;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body> 
  <?php  
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

    if ($codigo_alfa_numerico!='') {
        $codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

        if (strlen($codigo_alfa_numerico)!=9){
            $data = explode("-", $codigo_alfa_numerico);
            $codigo_alfa_consulta = $data[0];
        }
        else {
            $codigo_alfa_consulta = '';
        }

        $sql = "SELECT * FROM tbl_animais 
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
            WHERE tbl_animal_lixeira=0 AND 
                  tbl_animal_codigo_alfa='$codigo_alfa_consulta' AND 
                  tbl_animal_codigo_numerico='$codigo_numerico_consulta'"; 
    }
    else {
        if ($situacao_vendido == 'S') {
            $sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                        ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE tbl_animal_lixeira=0" .
                    $wativo . 
                    $wsituacao .
                    $wlocal_anterior . 
                    $wsexo . 
                    $worigem .
                    $wmae .
                    $wpai .
                    $wraca .
                    $wdata_nasc .
                    $wpeso_nasc .
                    $wpeso_desmama .
                    $wpeso_ult .
                " ORDER BY tbl_animal_codigo_numerico ASC"; 
        }
        else {
            $sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                        ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE tbl_animal_lixeira=0" .
                    $wativo . 
                    $wsituacao .
                    $wlocal . 
                    $wsexo . 
                    $worigem .
                    $wmae .
                    $wpai .
                    $wraca .
                    $wdata_nasc .
                    $wpeso_nasc .
                    $wpeso_desmama .
                    $wpeso_ult .
                " ORDER BY tbl_animal_codigo_numerico ASC"; 
            /*$sql = "SELECT * from tbl_animais 
                INNER JOIN tbl_pessoa
                        ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
                WHERE tbl_animal_lixeira=0 AND 
                      tbl_animal_codigo_id =265 
                ORDER BY tbl_animal_codigo_numerico ASC"; */
        }
    }

    $tbl_animais = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($tbl_animais);

    $codigos_pais = [];
    $codigos_maes = [];
    $codigos_racas = [];
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

        if ($reg_animal->tbl_animal_codigo_origem) {
            $codigos_origem[] = $reg_animal->tbl_animal_codigo_origem;
        }

        if ($reg_animal->tbl_animal_sexo == 'F') {
            $ids_femeas[] = $reg_animal->tbl_animal_codigo_id;
        }

        if ($filtro_descarte=='S' && $reg_animal->tbl_animal_descarte_reproducao) {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }
        else if ($filtro_descarte=='N' && $reg_animal->tbl_animal_descarte_reproducao!="S") {
            $dados_descarte[$reg_animal->tbl_animal_codigo_id] = 'S';
        }
    }

    $dados_partos = [];
    $dados_abortos = [];
    $dados_previsao_partos = [];
    $dados_data_partos = [];
    $ultimo_filho_cache = [];
    $num_partos = 0;
    $num_abortos = 0;
    $num_natimortos = 0;
    $data_ref = date("Y-m-d");
    $femeas_prenhes = [];
    $femeas_negativas = [];

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
                    'dias_aborto_natimorto' => 0
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
            }
        }
    }

//print_r($ids_femeas);

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

        // Percorrer o array de animais
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
                    if ($paricao_cache[$codigo_animal_id]=='S') {
                        $dados_data_partos[$codigo_animal_id] = 'S';
                    }
                }
            }
        }

        if ($filtro_vacas_paridas=='S' || $filtro_vacas_solteiras=='S') {
            $sql_ultimo_filho = "SELECT t1.tbl_animal_codigo_mae, t1.tbl_animal_codigo_id, t1.tbl_animal_data_nascimento, t1.tbl_animal_ativo
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
                $ultimo_filho_cache[$row['tbl_animal_codigo_mae']] = [
                    'id_filho' => $row['tbl_animal_codigo_id'],
                    'data_nascimento' => $row['tbl_animal_data_nascimento'],
                    'ativo' => $row['tbl_animal_ativo']
                ];            
            }            
        } 
    }

//print_r($ultimo_filho_cache);

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
            $data_previsao_parto = '1900-01-01';
            
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
        $dados_previsao_partos[$femea] = $data_previsao_parto;
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
    mysqli_data_seek($tbl_animais, 0);

    while ($reg_animal = mysqli_fetch_object($tbl_animais)){
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

        if ($filtro_descarte=='') {
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

               //if ($animal['sexo']=='F' && $animal['idadeMeses']<=12) {
                    //unset($animais[$key]);
               //}

                //if ($animal['codigoAnimal'] === 'algum_codigo_que_deseja_remover') {
                  //  unset($animais[$key]);
                //}
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

        if ($filtro_num_natimorto=='N') {
            $filtroNumNatimortos = 'S';
            $num_natimortos = 0;

            if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_natimortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
            }
        }
        else {
            $filtroNumNatimortos = 'N';
            $num_natimortos = 0;

            if (!empty($dados_natimortos) && $reg_animal->tbl_animal_sexo=='F') {
                $num_natimortos = $dados_natimortos[$reg_animal->tbl_animal_codigo_id];
            
                if ($filtro_reproducao=='S') {
                    if ($num_natimortos>=$num_natimorto_de && $num_natimortos<=$num_natimorto_ate) {
                        $filtroNumNatimortos = 'S';
                    }
                }
            }
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
                    if ($dados_data_partos[$reg_animal->tbl_animal_codigo_id]=='S') {
                        $filtroDataParicao = 'S';
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

        // VACAS SOLTEIRAS
/*        if ($filtro_vacas_solteiras=='N') {
            $filtroVacasSolteiras = 'S';
        }
        else {
            $filtroVacasSolteiras = 'N';

            if ($reg_animal->tbl_animal_sexo=='F' && $idade>=12) {

                $codigo_mae = $reg_animal->tbl_animal_codigo_id;

                if (!empty($dados_femeas_aborto_natimorto)) {

                    if (array_key_exists($codigo_mae, $dados_femeas_aborto_natimorto))
                     {
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

                if ($reg_animal->tbl_animal_codigo_id==189) {
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
*/

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

        if ($reg_animal->tbl_animal_codigo_pelagem == '' || 
            $reg_animal->tbl_animal_codigo_pelagem==999) {
            $codigo_pelagem = '000';
        }
        else {
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
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


        $peso_desmama = $reg_animal->tbl_animal_peso_desmama;
        $peso_nasc = $reg_animal->tbl_animal_primeiro_peso;

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

        if ($wcategoria=="") {
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
            'primeiroPeso' => $peso_nasc,
            'dataPrimeiroPeso' => $data_peso_nasc_edi,
            'pesoDesmama' => $peso_desmama,
            'dataPesoDesmana' => $data_peso_desmama_edi,
            'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
            'dataUltimoPeso' => $data_peso_ultimo_edi,
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
            'filtroNumNatimortos' => $filtroNumNatimortos,
            'numNatimortos' => $num_natimortos,
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
            'filtroVacasPositivas' => $filtroVacasPositivas
            ];
            $animais[] = $animalAtual;

            /*if ($reg_animal->tbl_animal_codigo_id==265) {
                var_dump($animalAtual);
            }*/
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
                    'primeiroPeso' => $peso_nasc,
                    'dataPrimeiroPeso' => $data_peso_nasc_edi,
                    'pesoDesmama' => $peso_desmama,
                    'dataPesoDesmana' => $data_peso_desmama_edi,
                    'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
                    'dataUltimoPeso' => $data_peso_ultimo_edi,
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
                    'filtroNumNatimortos' => $filtroNumNatimortos,
                    'numNatimortos' => $num_natimortos,
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
                    'filtroVacasPositivas' => $filtroVacasPositivas
                    ];

                    $animais[] = $animalAtual;
                }
            }
        }
    }

    // Geração do HTML (fora do loop)
    echo '<section class="panel">';
    echo '<table class="table table-striped table-advance table-hover" id="tabela_animais" style="font-size: 13px">';
    echo '<tbody>';

    foreach ($animais as $animal) {
        if ($filtro_reproducao == 'S') {
            if ($filtro_negativas=='S' && $filtro_positivas=='S') {
                if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && 
                    $filtro_vacas_prenhas == 'N') { 
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' 
                    && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasParidas']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' &&
                    $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasPrenhes']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {

                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                         $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' &&$filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }                
            }
            else {
                if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' && 
                    $filtro_vacas_prenhas == 'N') { 
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' 
                    && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasParidas']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='N' &&$filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasPrenhes']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' && $filtro_vacas_prenhas=='S') {

                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                         $animal['filtroDescarte']=='S' &&
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='S') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroNumNatimortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S' ||
                         $animal['filtroVacasPrenhes']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $animais_listados++;
                        listar($conector, $animal);         
                    }
                }                
            }
        }
        else {
            $animais_listados++;
            listar($conector, $animal);         
        }
    }

    mysqli_close($conector);
    
    echo '</tbody>';
    echo '<thead>';
    echo '
        <tr>
        <th colspan="11" style="text-align:left; border-top: none;">Total de Registros Encontrados:&nbsp;&nbsp;'.$animais_listados.'</th>
        </tr>
    
        <tr>
        <th> <i class="fa fa-sort-alpha-asc"></i></th>
        <th style="text-align: center;">Nº Animal</th>
        <th>Descarte</th>
        <th>Raça</th>
        <th>Categoria</th>
        <th>Sexo</th>
        <th>Local</th>
        <th>Mãe</th>
        <th>Pai</th>
        <th>Situação</th>
        <th><i class="icon_cogs"></i>Ações</th>
        </tr>';
    echo '</thead>';
    echo '</table>';
    echo '</section>';

function listar($conector, $animal) {

    $json_animal = json_encode($animal);
    $onclick_action = "editar_animal(JSON.parse(this.dataset.animal));";
    $onclick_action .= ($animal['sexo'] == 'F') ? "mostrar_reproducao();" : "esconder_reproducao();";
                            
    $row_color = ($animal['ativo'] == "N") ? "style='color:#FF9393'" : "";
                            
    echo "<tr {$row_color}>";
    echo "<td align='right' width='4%'>{$animal['codigoAlfa']}</td>";
    echo "<td width='10%'>{$animal['codigoNumerico']}</td>";
    echo "<td width='5%' " . ($animal['descarte'] == 'S' ? "style='color:red'" : "") . ">" . ($animal['descarte'] == 'S' ? 'Sim' : '') . "</td>";
    echo "<td width='10%'>{$animal['descricaoRaca']}</td>";
    echo "<td width='10%'>{$animal['descricaoCategoria']}</td>";
    echo "<td width='5%'>{$animal['sexo']}</td>";
    echo "<td width='16%'>{$animal['nomeFazenda']}</td>";
    echo "<td width='10%'>{$animal['codigoMaeAlfaNumerico']}</td>";
    echo "<td width='10%'>{$animal['codigoPaiAlfaNumerico']}</td>";
    echo "<td width='10%'>{$animal['descSituacao']}</td>";
    echo "<td width='10%'>";
    echo "<div class='btn-group'>";
    echo "<a class='btn' href='#' data-animal='" . htmlspecialchars($json_animal, ENT_QUOTES) . "' onClick=\"{$onclick_action}\">";
    echo "<i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro'></i>";
    echo "</a>";
    echo "</div>";
    echo '<input type="hidden" id="obs" value="'.htmlspecialchars($animal['observacao'], ENT_QUOTES).'" />';
    echo "</td>";
    echo "</tr>";
}

    function VerAborto($conector, $codigo_mae) {
        $data_ref = date("Y-m-d");
        $teve_aborto_natimorto = 'N';
        $dias_aborto_natimorto = 0;
        $data_aborto = '0000.00.00';

        $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
            WHERE tbl_mov_estoque_codigo_mae='$codigo_mae' AND 
                  tbl_mov_estoque_codigo_id_animal=999999999 AND
                  tbl_mov_estoque_entrada_saida='A' AND 
                  (tbl_mov_estoque_tipo_movimentacao='A' OR
                   tbl_mov_estoque_tipo_movimentacao='B') 
            ORDER BY tbl_mov_estoque_nascimento DESC");

        $num_aborto = mysqli_num_rows($tbl_aborto);

        if ($num_aborto!=0) {
            $teve_aborto_natimorto = 'S';
        }

        if ($teve_aborto_natimorto == 'S') {
            $reg_aborto = mysqli_fetch_object($tbl_aborto);

            $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
            $data_ref = date("Y-m-d", strtotime($data_ref . "- 35 days"));

            $diferenca = strtotime($data_ref) - strtotime($data_aborto);
            $dias_aborto_natimorto = floor($diferenca / (60 * 60 * 24));
        }

        return [$teve_aborto_natimorto, $dias_aborto_natimorto];
    }


/*    foreach ($animais as $animal) {
        $json_animal = json_encode($animal);

        $onclick_action = "editar_animal(JSON.parse(this.dataset.animal));"; // Usando a nova abordagem de data-attributes
    
        // Adiciona a segunda função com base no sexo
        if ($animal['sexo'] == 'F') {
            $onclick_action .= "mostrar_reproducao();";
        } else {
            $onclick_action .= "esconder_reproducao();";
        }

        if ($animal['ativo']=="N") {
            echo "<tr style='color:#FF9393'>";
        }
        else {
            echo "<tr>";
        }

        echo "<td align='right' width='4%'>".$animal['codigoAlfa']."</td>";
        echo "<td width='10%'>".$animal['codigoNumerico']."</td>";

        if ($animal['descarte']=='S') {
            echo "<td style='color:red' width='5%'>Sim</td>";
        }
        else {
            echo "<td width='5%'></td>";
        }

        echo "<td width='10%'>".$animal['descricaoRaca']."</td>";
        echo "<td width='10%'>".$animal['descricaoCategoria']."</td>";
        echo "<td width='5%'>".$animal['sexo']."</td>";
        echo "<td width='16%'>".$animal['nomeFazenda']."</td>";
        echo "<td width='10%'>".$animal['codigoMaeAlfaNumerico']."</td>";
        echo "<td width='10%'>".$animal['codigoPaiAlfaNumerico']."</td>";
        echo "<td width='10%'>".$animal['descSituacao']."</td>";
        echo "<td width='10%'>";
        echo "<div class='btn-group'>";
        echo "<a class='btn' href='#' data-animal='" . htmlspecialchars($json_animal, ENT_QUOTES) . "' onClick=\"{$onclick_action}\">";
        echo "<i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro'></i>";
        echo "</a>";
        echo "</div>";
        echo '<input type="hidden" id="obs" value="'.$animal['observacao'].'"';
        echo "</td>";
        echo "</tr>";
    }

    mysqli_close($conector);
  
    echo '</tbody>';
    echo '<thead>';
    echo '
        <tr>
        <th colspan="11" style="text-align:left; border-top: none;">Total de Registros Encontrados:&nbsp;&nbsp;'.$animais_listados.'</th>
        </tr>

        <tr>
        <th> <i class="fa fa-sort-alpha-asc"></i></th>
        <th style="text-align: center;">Nº Animal</th>
        <th>Descarte</th>
        <th>Raça</th>
        <th>Categoria</th>
        <th>Sexo</th>
        <th>Local</th>
        <th>Mãe</th>
        <th>Pai</th>
        <th>Situação</th>
        <th><i class="icon_cogs"></i>Ações</th>
        </tr>';
    echo '</thead>';
    echo '</table>';
    echo '</section>';*/
?>

    <script src="js/tabela_animais.js" charset="utf-8" type="text/javascript" ></script>

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
    </script>

</body>
</html> 
