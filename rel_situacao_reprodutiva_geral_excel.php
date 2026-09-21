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

    $num_parto_de = $_REQUEST['num_parto_de'];
    $num_parto_ate = $_REQUEST['num_parto_ate'];
    $num_aborto_de = $_REQUEST['num_aborto_de'];
    $num_aborto_ate = $_REQUEST['num_aborto_ate'];
    $num_natimorto_de = $_REQUEST['num_natimorto_de'];
    $num_natimorto_ate = $_REQUEST['num_natimorto_ate'];
    $previsao_parto_de = $_REQUEST["previsao_parto_de"];
    $previsao_parto_ate = $_REQUEST["previsao_parto_ate"];
    $data_paricao_de = $_REQUEST["data_paricao_de"];
    $data_paricao_ate = $_REQUEST["data_paricao_ate"];
    $filtro_reproducao = $_REQUEST["filtro_reproducao"];
    $filtro_num_parto = $_REQUEST['filtro_num_parto'];
    $filtro_num_aborto = $_REQUEST['filtro_num_aborto'];
    $filtro_num_natimorto = $_REQUEST['filtro_num_natimorto'];
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

    if ($num_natimorto_de=='') {
        $num_natimorto_de = 0;
        $num_natimorto_ate = 999;
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

    $ativo_filtro = $_REQUEST['ativo'];

    $wativo = " AND tbl_animal_ativo IN(";
    $wativo .= "'" . $ativo_filtro . "'";
    $wativo.= ")";
    
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

    $sql = "SELECT * from tbl_animais 
        INNER JOIN tbl_pessoa
                ON tbl_pessoa_id = tbl_animal_codigo_fazenda 
        WHERE tbl_animal_lixeira=0" .
            $wativo . 
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

        if ($idade >=12) {
            $dados_animais[] = $reg_animal;
        }

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

        if ($reg_animal->tbl_animal_sexo == 'F' && $idade >=12) {
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
    $dados_natimortos = [];
    $dados_previsao_partos = [];
    $dados_nascidos = [];
    $dados_data_partos = [];
    $ultimo_filho_cache = [];
    $num_partos = 0;
    $num_abortos = 0;
    $num_natimorto = 0;
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
                       tbl_ite_cobertura_codigo_id_animal IN ($ids_string)
                ORDER BY tbl_ite_cobertura_codigo_id_animal ASC, tbl_ite_cobertura_numero_id DESC"; // Ordena primeiro por fêmea, depois por ID de forma decrescente

        //tbl_cobertura_codigo_local IN({$local_para_query}) AND 

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
                $dados_nascidos[$femea] = $reg_cobertura->tbl_ite_cobertura_nascido;
                $dados_previsao_partos[$femea] = $data_previsao_parto;
            }
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

        /*foreach ($femea_selecionada_cobertura as $codigo_animal => $dados_cobertura) {
        
           
                $diagnostico_atual = $dados_cobertura['diagnostico'];
                echo "Processando animal ID: " . $codigo_animal . ' Diag: ' . $diagnostico_atual . '<br>';
            
        }

        exit;*/

        // Verifica femea selecionada na estacao ou monta 
        if (!empty($femea_selecionada_cobertura)) {
            $id_animal = $reg_animal->tbl_animal_codigo_id;

            if (array_key_exists($id_animal, $femea_selecionada_cobertura)) {
                $dados = $femea_selecionada_cobertura[$id_animal];
                $selecionadaCobertura = $dados['selecionada'];
                $selecionadaCoberturaControle = $dados['controle'];
                $diagnostico = $dados['diagnostico'];
                //$nascido = $dados['nascido'];
                $id_estacao = $dados['estacao_id'];
                $desc_estacao = $dados['desc_estacao'];
            }
            else {
                $selecionadaCobertura = '';
                $selecionadaCoberturaControle = '';
                $diagnostico = '';
                //$nascido = '';
                $id_estacao = '';
                $desc_estacao = '';
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

        // recupera numeros Natimortos
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

        /*if ($filtro_previsao_parto=='N') {
            $filtroDataPrevisao = 'S';
            $data_previsao_parto = 0;

            if (!empty($dados_previsao_partos)) {
                if ($reg_animal->tbl_animal_sexo=='F') {
                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];
                    $nascido = $dados_nascidos[$reg_animal->tbl_animal_codigo_id];
                }
            }
        }
        else {
            if (!empty($dados_previsao_partos)) {
                if ($reg_animal->tbl_animal_sexo=='F') {
                    $filtroDataPrevisao = 'N';
                    $data_previsao_parto = 0;

                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];
                    $nascido = $dados_nascidos[$reg_animal->tbl_animal_codigo_id];

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
        }*/

        if ($filtro_previsao_parto=='N') {
            $filtroDataPrevisao = 'S';
            $data_previsao_parto = 0;

            if (!empty($dados_previsao_partos)) {
                $id_animal = $reg_animal->tbl_animal_codigo_id;

                if (array_key_exists($id_animal, $dados_previsao_partos)) {
                    $data_previsao_parto = $dados_previsao_partos[$id_animal];
                    $nascido = $dados_nascidos[$id_animal];
                }

                /*if ($reg_animal->tbl_animal_sexo=='F') {
                    $data_previsao_parto = $dados_previsao_partos[$reg_animal->tbl_animal_codigo_id];
                    $nascido = $dados_nascidos[$reg_animal->tbl_animal_codigo_id];
                }*/
            }
        }
        else {
            if (!empty($dados_previsao_partos)) {
                $id_animal = $reg_animal->tbl_animal_codigo_id;

                if (array_key_exists($id_animal, $dados_previsao_partos)) {
                    $filtroDataPrevisao = 'N';
                    $data_previsao_parto = 0;

                    $data_previsao_parto = $dados_previsao_partos[$id_animal];
                    $nascido = $dados_nascidos[$id_animal];

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

                        /*$data_ref = date("Y-m-d");
                        $diferenca = strtotime($data_ref) - strtotime($data_nascimento_filho);
                        $dias_nascimento = floor($diferenca / (60 * 60 * 24));*/

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
            'dataPrimeiroPeso' => $data_peso_nasc_edi,
            'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
            'dataPesoDesmana' => $data_peso_desmama_edi,
            'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
            'dataUltimoPeso' => $data_peso_ultimo_edi,
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
                    'dataPrimeiroPeso' => $data_peso_nasc_edi,
                    'pesoDesmama' => $reg_animal->tbl_animal_peso_desmama,
                    'dataPesoDesmana' => $data_peso_desmama_edi,
                    'ultimoPeso' => $reg_animal->tbl_animal_ultimo_peso,
                    'dataUltimoPeso' => $data_peso_ultimo_edi,
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
        }
    }

    //var_dump($animais);
    //exit;

    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

@ session_start(); 

$cnpj_empresa = $_SESSION['id_cliente'];

$empresa = mysqli_query($conector, "SELECT tbl_empresa_nome FROM tbl_empresa
                                     WHERE tbl_empresa_cpf_cnpj ='$cnpj_empresa'"); 
$registro_empresa = mysqli_fetch_object($empresa);  
$nome_empresa = utf8_encode($registro_empresa->tbl_empresa_nome);

$nome_relatorio = "Situação Reprodutiva - Geral";

    $spreadsheet->getActiveSheet()->mergeCells('A1:Q1');
    $spreadsheet->getActiveSheet()->mergeCells('R1:S1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:S2');
    $spreadsheet->getActiveSheet()->mergeCells('A3:S3');

    $spreadsheet->getActiveSheet()->mergeCells('A4:F4');
    $spreadsheet->getActiveSheet()->mergeCells('G4:L4');
    $spreadsheet->getActiveSheet()->mergeCells('M4:S4');

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', $nome_relatorio)
            ->setCellValue("R1", "Data: " . $data_sistema)
            ->setCellValue("A2", $descricao_filtro);

    $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setColor(new Color(Color::COLOR_GRAY));
    $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(10);


    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A4","Fêmeas")
            ->setCellValue("G4","Situação Atual")
            ->setCellValue("M4","Situação Reprodutiva");

    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A5","Id Fêmea")
            ->setCellValue("B5","Raça")
            ->setCellValue("C5","Pelagem")
            ->setCellValue("D5","Nascimento")
            ->setCellValue("E5","Pai")
            ->setCellValue("F5","Mãe")
            ->setCellValue("G5","Nº Partos")
            ->setCellValue("H5","Aborto")
            ->setCellValue("I5","Natimorto")
            ->setCellValue("J5","Último Parto")
            ->setCellValue("K5","Último Bezerro Vivo")
            ->setCellValue("L5","Pai Semen Embrião")
            ->setCellValue("M5","Última Estação de Monta")
            ->setCellValue("N5","Nº Coberturas")
            ->setCellValue("O5","Diagnóstico")
            ->setCellValue("P5","Pai Semen Embrião")
            ->setCellValue("Q5","Previsão Parto")
            ->setCellValue("R5","Data Aptidão")
            ->setCellValue("S5","Descarte");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(11);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(16);
    $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(12);
    $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(9);

    $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('R1') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $spreadsheet->getActiveSheet()->getStyle('B2') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('G4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getStyle('M4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:S5')->getAlignment()->setHorizontal($align);
    $align = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    $spreadsheet->getActiveSheet()->getStyle('A5:S5')->getAlignment()->setVertical($align);

    $spreadsheet->getActiveSheet()->getStyle('G5:H5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('J5:L5')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('M5:S5')->getAlignment()->setWrapText(true);

    $spreadsheet->getActiveSheet()->getStyle('A4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('A4')->getFill()->getStartColor()->setARGB('F2F2F2');

    $spreadsheet->getActiveSheet()->getStyle('G4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('G4')->getFill()->getStartColor()->setARGB('D9D9D9');

    $spreadsheet->getActiveSheet()->getStyle('M4')->getFill()->setFillType(Fill::FILL_SOLID);
    $spreadsheet->getActiveSheet()->getStyle('M4')->getFill()->getStartColor()->setARGB('BFBFBF');

    $linha=5;

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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $animais_listados++;
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' &&$filtro_vacas_prenhas=='S') {
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {

                        $animais_listados++;
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {

                        $animais_listados++;
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='N' &&$filtro_vacas_prenhas=='S') {
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
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
                        $linha = listar($conector, $animal, $linha, $spreadsheet);  
                    }
                }                
            }
        }
        else {
            $linha = listar($conector, $animal, $linha, $spreadsheet);  
            $animais_listados++;
        }
    }

    $spreadsheet->getActiveSheet()->getStyle('A4') ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 4, 'Fêmeas: ' . $animais_listados);

    // Rename worksheet

    $spreadsheet->getActiveSheet()->setTitle('Simple');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');


    header('Content-Disposition: attachment;filename="situacao_reprodutiva.xlsx"');
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


    function listar($conector, $animal, $linha, $spreadsheet) {
        $nascimento = new DateTime($animal['dataNascimento']);
        $nascimento_edi = $nascimento->format('d/m/Y');
        $nascimento_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($nascimento_edi);

        if ($animal['descarte']=='S') {
            $descarte = 'Sim';
        }
        else {
            $descarte = '';
        }

        $codigo_id = $animal['codigoAnimal'];
        $codigo_alfa = $animal['codigoAlfa'];
        $codigo_numerico = $animal['codigoNumerico'];
        $estacao_animal = $animal['idEstacao'];

        if ($codigo_alfa!='') {
            $codigo_edi = $codigo_alfa.'-'.$codigo_numerico;
        }
        else {
            $codigo_edi = $codigo_numerico;
        } 

        $id_filho = $animal['idFilho'];
        $bezzero_ativo = $animal['filhoAtivo'];
        $id_pai_filho = $animal['paiFilho'];
        $dias_nascimento_bezerro = $animal['diasNascimentoFilho'];
        $ultimo_parto_edi = '';
        $codigo_edi_filho = '';
        $codigo_alfa_filho = '';
        $codigo_numerico_filho = '';

        if ($id_filho!=0) {
            $data_nascimento_filho = new DateTime($animal['dataNascimentoFilho']);
            $ultimo_parto_edi = $data_nascimento_filho->format('d/m/Y');

            $sql = "SELECT tbl_animal_codigo_alfa, tbl_animal_codigo_numerico from tbl_animais 
                WHERE tbl_animal_codigo_id ='$id_filho'"; 

            $tbl_animais = mysqli_query($conector, $sql); 
            $num_rows_animais = mysqli_num_rows($tbl_animais);

            if ($num_rows_animais!=0) {
                $reg_animal = mysqli_fetch_object($tbl_animais);
                $codigo_alfa_filho = $reg_animal->tbl_animal_codigo_alfa;
                $codigo_numerico_filho = $reg_animal->tbl_animal_codigo_numerico;
            }
            else {
                $codigo_alfa_filho = '';
                $codigo_numerico_filho = '';
            }
        }

        if ($codigo_alfa_filho!='') {
            $codigo_edi_filho = $codigo_alfa_filho .'-'.intval($codigo_numerico_filho);
        }
        else if ($codigo_numerico_filho!=''){
            $codigo_edi_filho = intval($codigo_numerico_filho);
        }
 
        $descricao_pai_ult_filho = '';

        if ($id_pai_filho!=0) {
            $tab_pai = mysqli_query($conector, "select tbl_semem_nome from tbl_semem where tbl_semem_codigo_id='$id_pai_filho'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai_ult_filho = $reg->tbl_semem_nome;
            }
            else {
                $tab_pai = mysqli_query($conector, "select tbl_animal_codigo_alfa, tbl_animal_codigo_numerico from tbl_animais where tbl_animal_codigo_id='$id_pai_filho'");
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    if ($reg->tbl_animal_codigo_alfa==''){
                        $descricao_pai_ult_filho = intval($reg->tbl_animal_codigo_numerico);
                    }
                    else {
                        $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. intval($reg->tbl_animal_codigo_numerico);
                    }
                }
            }
        }

        $ultimo_parto = $animal['dataNascimentoFilho'];  
        $data_aborto_natimorto = $animal['dataAbortoNatimorto'];

        /*if ($data_natimorto=='0000-00-00' && $data_aborto=='0000-00-00') {
            $data_aborto_natimorto='0000-00-00';
        }
        else if ($data_natimorto>$data_aborto){
            $data_aborto_natimorto=$data_natimorto;
        }
        else if ($data_aborto>$data_natimorto) {
            $data_aborto_natimorto=$data_aborto;
        }*/

        if ($data_aborto_natimorto!=0 && $data_aborto_natimorto>$ultimo_parto) {
            $data = new DateTime($data_aborto_natimorto);
            $ultimo_parto_edi = $data->format('d/m/Y');
            $natimorto = 'S';
        }
        else {
            $natimorto = 'N';
        }

        if ($ultimo_parto_edi!='') {
            $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);
        }

        $data_previsao_parto = $animal['dataPrevisaoParto'];

        if ($data_previsao_parto==0 || $data_previsao_parto=='' || $data_previsao_parto==null) {
            $data_previsao_parto = '0000-00-00';
            $previsao_parto_edi = '';
        }
        else {
            $data = new DateTime($data_previsao_parto);
            $previsao_parto_edi = $data->format('d/m/Y');
            $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                 
        }

        // calcula data da aptidão
        $data_aptidao_edi = '';

        if ($ultimo_parto!=0) {
            $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
        }

        if ($data_aborto_natimorto!=0 && $data_aborto_natimorto>$ultimo_parto) {
            $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
        }

        if ($data_aptidao_edi!='') {
            $data_aptidao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_aptidao_edi);    
        }

        // Verifica numero de coberturas na estação
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            WHERE tbl_cobertura_lixeira=0 AND
                  tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                  tbl_cobertura_controle = 'C' AND 
                  tbl_cobertura_codigo_estacao_monta ='$estacao_animal'"); 

        $num_coberturas = mysqli_num_rows($tbl_item_cobertura);

        if ($num_coberturas==0) {
            $num_coberturas = '';
        } 

        // Verifica o semem ultima cobetura
        $tbl_item_cobertura = mysqli_query($conector, "SELECT tbl_ite_cobertura_codigo_touro_semen FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
                     (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
            ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

        $num_rows = mysqli_num_rows($tbl_item_cobertura);

        if ($num_rows!=0) {
            $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
            $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

            $tab_pai = mysqli_query($conector, "select tbl_semem_nome from tbl_semem where tbl_semem_codigo_id='$semen'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_semen = utf8_encode($reg->tbl_semem_nome);
            }
            else {
                $tab_pai = mysqli_query($conector, "select tbl_animal_codigo_alfa, tbl_animal_codigo_numerico from tbl_animais where tbl_animal_codigo_id='$semen'");
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    if ($reg->tbl_animal_codigo_alfa==''){
                        $descricao_semen = (int)$reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. (int)$reg->tbl_animal_codigo_numerico;
                    }
                }
                else {
                    $descricao_semen = '';
                }
            }
        }
        else {
            $descricao_semen = '';
        }

        $descricao_raca = utf8_encode($animal['descricaoRaca']);
        $descricao_pelagem = utf8_encode($animal['descricaoPelagem']);
        $descricao_mae = $animal['codigoMaeAlfaNumerico'];
        $descricao_pai = utf8_encode($animal['codigoPaiAlfaNumerico']);
        $num_partos = $animal['numPartos'];
        $num_aborto = $animal['numAbortos'];
        $num_natimorto = $animal['numNatimortos'];
        $desc_estacao_monta = $animal['desc_estacao'];

        if ($num_aborto==0) {
            $num_aborto = '';
        }

        if ($num_natimorto==0){
            $num_natimorto = '';
        }

        $femea_selecionada_cobertura = $animal['selecionadaCobertura'];
        $controle = $animal['selecionadaCoberturaControle'];
        $diagnostico = $animal['diagnostico'];
        $nascido = $animal['nascido'];

        if ($diagnostico=='N') {
            $previsao_parto_edi='';
            $desc_diagnostico = 'Negativo';
        }
        else if ($diagnostico=='P') {
            switch ($nascido) {
                case 'N':
                    $desc_diagnostico = 'Nascido';
                    break;
                case 'A':
                    $desc_diagnostico = 'Aborto';
                    break;
                case 'M':
                    $desc_diagnostico = 'Natimorto';
                    break;
                case 'S':
                    $desc_diagnostico = 'Outro';
                    break;
                default:
                    $desc_diagnostico = 'Positivo';
                    break;       
            }
        }
        else {
            $desc_diagnostico = '';
        }

        $linha++;
        $celulas = 'A'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $celulas = 'E'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $celulas = 'F'.$linha.':H'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $celulas = 'I'.$linha.':N'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $celulas = 'K'.$linha.':L'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $celulas = 'O'.$linha.':S'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $celulas = 'P'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas) ->getAlignment() ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $spreadsheet->getActiveSheet()->getStyle('S'.$linha)->getFont()->setColor(new Color(Color::COLOR_RED));

        if ($nascido=='N' || $nascido=='A' || 
            $nascido=='M' || $nascido=='O') {
            $spreadsheet->getActiveSheet()->getStyle('Q'.$linha)->getFont()->setColor(new Color(Color::COLOR_GRAY));
        }
        else {
            $data_aptidao_edi = '';
        }

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $codigo_edi);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $descricao_raca);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $descricao_pelagem);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $nascimento_edi);

        $celulas = 'D'.$linha;
        $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY'); 

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $descricao_pai);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $descricao_mae);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $num_partos);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $num_aborto);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $num_natimorto);

        if ($natimorto=='S') {
            $celulas = 'J'.$linha;
            $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
            $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
            $commentRichText->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
            $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Natimorto ou Aborto.');
            $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
            $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
            $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
            $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');
        }
        else if ($bezzero_ativo=='N' && 
                ($dias_nascimento_bezerro>0 && 
                 $dias_nascimento_bezerro<=35)) {
            $celulas = 'J'.$linha;
            $spreadsheet->getActiveSheet()->getComment($celulas)->setAuthor('');
            $commentRichText = $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Mensagem');
            $commentRichText->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun("\r\n");
            $spreadsheet->getActiveSheet()->getComment($celulas)->getText()->createTextRun('Morreu <=35 dias.');
            $spreadsheet->getActiveSheet()->getComment($celulas)->setWidth('100pt');
            $spreadsheet->getActiveSheet()->getComment($celulas)->setHeight('50pt');
            $spreadsheet->getActiveSheet()->getComment($celulas)->setMarginLeft('100pt');
            $spreadsheet->getActiveSheet()->getComment($celulas)->getFillColor()->setRGB('EEEEEE');
        }
        
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $ultimo_parto_edi);

        if ($ultimo_parto_edi!='') {
            $celulas = 'J'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY'); 
        }

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $codigo_edi_filho);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $descricao_pai_ult_filho);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $desc_estacao_monta);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $num_coberturas);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $desc_diagnostico);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $descricao_semen);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $previsao_parto_edi);

        if ($previsao_parto_edi!='') {
            $celulas = 'Q'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
        }
                                    
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(18, $linha, $data_aptidao_edi);

        if ($data_aptidao_edi!='') {
            $celulas = 'R'.$linha;
            $spreadsheet->getActiveSheet()->getStyle($celulas)->getNumberFormat()->setFormatCode('DD/MM/YYYY');
        }

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(19, $linha, $descarte);

        return $linha;
    }
                          
/*    $sql = "SELECT * from tbl_animais 
        WHERE tbl_animal_lixeira=0 AND 
              tbl_animal_ativo='$wativo' AND 
              tbl_animal_sexo='F'" . $wlocal . $worigem . $wraca . $wpai . 
              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
        " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC";

    $rs = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($rs);

    $animais_listados = 0;
    $ultimo_parto = '0000-00-00';
    $data_previsao_servico = '0000-00-00';
    $data_previsao_parto = '0000-00-00';

    if ($num_rows_animais!=0){
        while ($reg_animal = mysqli_fetch_object($rs)){
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
            $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
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
            $nascido = '';
            $data_aborto_natimorto = '0000-00-00';

            if ($descarte=='S') {
                if ($animal_descarte=='Sim') {
                    $vaca_descarte = 'S';
                }
            }

            // verifica a cobertura do animal

            $sql = "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR  
                       tbl_cobertura_controle = 'M')
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"; 

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            $num_rows = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                $controle = $reg_cobertura->tbl_cobertura_controle;
                $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                
                if ($controle == 'C') {
                    $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
                        WHERE tbl_par_estacao_id ='$estacao_animal'");

                    $num_rows_estacao = mysqli_num_rows($tbl_estacao);

                    if ($num_rows_estacao!=0) {
                        $reg_estacao = mysqli_fetch_object($tbl_estacao);
                        $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                    }
                    else {
                        $estacao_animal = 0;
                        $desc_estacao_monta = 'Desconhecida';                    
                    }
                }
                else {
                    $estacao_animal = 0;
                    $desc_estacao_monta = 'Monta';                    
                }
            }
            else {
                $estacao_animal = 0;
                $desc_estacao_monta = '';
            }

            // verifica numero de coberturas na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'"); 

            $num_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_coberturas==0) {
                $num_coberturas = '';
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

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = utf8_encode($reg->tbl_semem_nome);
                $pai = $reg->tbl_semem_codigo_id;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    if ($reg->tbl_animal_codigo_alfa==''){
                        $descricao_pai = $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_pai = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                    }
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

            //$data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); 
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

            // verifica vacas solteiras
            if ($filtro_solteiras=='S' || $filtro_paridas=='S') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $bezzero_ativo = $reg_filhos->tbl_animal_ativo;
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $data_ref = date("Y-m-d");
                    $diferenca = strtotime($data_ref) - strtotime($ultimo_parto);
                    $dias_nascimento_bezerro = floor($diferenca / (60 * 60 * 24));

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = utf8_encode($reg->tbl_semem_nome);
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai_ult_filho'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                    $descricao_pai_ult_filho = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_pai_ult_filho = '';
                        }
                    }

                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($ultimo_parto); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_ano = $idade_acompanhamento->format('%Y');
                    $idade_mes = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade < 8) {
                        if ($bezzero_ativo=='S') {
                            $vaca_parida = 'S';
                            $vaca_solteira = '';
                        }
                        else {
                            if ($dias_nascimento_bezerro<=35) {
                                $vaca_parida = 'S';
                                $vaca_solteira = '';
                            }
                            else {
                                $vaca_parida = '';
                                $vaca_solteira = 'S';
                            }
                        }
                    }
                    else {
                        $vaca_solteira = 'S';
                        $vaca_parida = '';
                    }
                }
                else {
                    $ultimo_parto = '0000-00-00';
                    $codigo_edi_filho = '';
                    $vaca_solteira = 'S';
                    $vaca_parida = '';
                    $descricao_pai_ult_filho = '';
                    $bezzero_ativo='';
                    $dias_nascimento_bezerro='';
                }

                if ($ultimo_parto=='0000-00-00') {
                    $ultimo_parto_edi = '';
                }
                else {
                    $data = new DateTime($ultimo_parto);
                    $ultimo_parto_edi = $data->format('d/m/Y');
                    $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);
                }

                // VERIFICA SE A VACA ESTA PRENHE

                $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                    INNER JOIN tbl_item_cobertura 
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal='$codigo' AND  
                          tbl_ite_cobertura_resultado_diagnostico='P' AND  
                          (tbl_ite_cobertura_nascido='' OR 
                           tbl_ite_cobertura_nascido IS NULL)");

                $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

                if ($num_rows_prenhe!=0) {
                    //print_r('Vaca prenhe: ' . $codigo_edi . '</br>');
                    $vaca_solteira = '';
                }
            }
            else {
                $ultimo_parto_edi = '';
                $codigo_edi_filho = '';
                $descricao_pai_ult_filho = '';
                $bezzero_ativo='';
                $dias_nascimento_bezerro='';

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $bezzero_ativo = $reg_filhos->tbl_animal_ativo;
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $data_ref = date("Y-m-d");
                    $diferenca = strtotime($data_ref) - strtotime($ultimo_parto);
                    $dias_nascimento_bezerro = floor($diferenca / (60 * 60 * 24));

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = utf8_encode($reg->tbl_semem_nome);
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai_ult_filho'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_pai_ult_filho = $reg->tbl_animal_codigo_numerico;
                            }
                        else {
                            $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_pai_ult_filho = '';
                        }
                    }
                } 
                else {
                    $ultimo_parto = '0000-00-00';
                }
                           
                if ($ultimo_parto=='0000-00-00') {
                    $ultimo_parto_edi = '';
                }
                else {
                    $data = new DateTime($ultimo_parto);
                    $ultimo_parto_edi = $data->format('d/m/Y');
                    $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);
                }
            }

            // verifica partos
            if ($num_parto_de!='' && $num_parto_ate!='') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                // verifica parto natimorto

                $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo' and 
                              tbl_mov_estoque_codigo_id_animal=999999999 and 
                              tbl_mov_estoque_entrada_saida='E' and 
                              tbl_mov_estoque_tipo_movimentacao='N'");
                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                $num_partos = $num_partos + $num_natimorto;

                if ($num_partos>=$num_parto_de && 
                    $num_partos<=$num_parto_ate && $idade_animal>=8) {
                    $tem_parto = "S";
                }
                else {
                    $tem_parto = "";
                }
            }
            else {
                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                // verifica parto natimorto

                $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                    where tbl_mov_estoque_codigo_mae='$codigo' and 
                          tbl_mov_estoque_codigo_id_animal=999999999 and 
                          tbl_mov_estoque_entrada_saida='E' and 
                          tbl_mov_estoque_tipo_movimentacao='N'");
                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                $num_partos = $num_partos + $num_natimorto;
            }

            // verifica se tem abortos ou natimortos
            if ($num_aborto_de!='' && $num_aborto_ate!='') {
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

            // agora verifica o numero de natimortos
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M'
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto==0) {
                $num_natimorto = '';
                $data_natimorto='0000-00-00';
            }
            else {
                $reg_natimorto = mysqli_fetch_object($tbl_natimorto);
                $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
            }

            // agora verifica o numero de abortos
            $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='A' AND 
                      (tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_aborto = mysqli_num_rows($tbl_aborto);

            if ($num_aborto==0) {
                $num_aborto = '';
                $data_aborto='0000-00-00';
            }
            else {
                $reg_aborto = mysqli_fetch_object($tbl_aborto);
                $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
            }

            //print_r('Aborto ' . $data_aborto . '</br>');

            // Verifica qual data será considerada para calcular a aptidao

            if ($data_natimorto=='0000-00-00' && $data_aborto=='0000-00-00') {
                $data_aborto_natimorto='0000-00-00';
            }
            else if ($data_natimorto>$data_aborto){
                $data_aborto_natimorto=$data_natimorto;
            }
            else if ($data_aborto>$data_natimorto) {
                $data_aborto_natimorto=$data_aborto;
            }

            // Se tem natimorto e a data é maior o ultimo parto considera como ultimo parto
            if ($data_aborto_natimorto>$ultimo_parto) {
                $data = new DateTime($data_aborto_natimorto);
                $ultimo_parto_edi = $data->format('d/m/Y');
                $ultimo_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ultimo_parto_edi);                
                $natimorto = 'S';
            }
            else {
                $natimorto = 'N';
            }

            // verifica previsao de parto
            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR
                      tbl_cobertura_controle = 'M') AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' 
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_coberturas!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                $controle = $reg_cobertura->tbl_cobertura_controle;
                $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

                if ($controle=='C') {
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
                    $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                }
            }
            else {
                $data_previsao_parto = '';
            }
        
            if ($data_previsao_parto=='' || $data_previsao_parto=='0000-00-00') {
                $data_previsao_parto = '0000-00-00';
                $previsao_parto_edi = '';
            }
            else {
                $data = new DateTime($data_previsao_parto);
                $previsao_parto_edi = $data->format('d/m/Y');
                $previsao_parto_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($previsao_parto_edi);                 
            }

            // calcula data da aptidão

            $data_aptidao_edi = '';
            
            if ($ultimo_parto!='0000-00-00') {
                $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
                //print_r('Aptidao pelo ultimo parto ' . $data_aptidao_edi . '</br>');
            }

            if ($data_aborto_natimorto!='0000-00-00' && $data_aborto_natimorto>$ultimo_parto) {
                $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
            }

            if ($data_aptidao_edi!='') {
                $data_aptidao_edi = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($data_aptidao_edi);    
            }

            // Verifica diagnostico
            if ($filtro_positivo=='S' || $filtro_negativo=='S'){

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                         (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                    ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$semen'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_semen = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_semen = '';
                        }
                    }

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
                $diagnostico = '';
                $descricao_semen = '';

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                    ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$semen'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_semen = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_semen = '';
                        }
                    }
                }
            }

            // verifica natimortos, nascidos ou abortos na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      ((tbl_cobertura_controle = 'C' AND  
                        tbl_cobertura_codigo_estacao_monta ='$estacao_animal') OR 
                       (tbl_cobertura_controle = 'M')) AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' 
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_item!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
            }
            else {
                $nascido_aborto = '';
            }

            if ($filtro_positivo=='S' AND 
                $nascido_aborto!='') {
                $tem_positivo='';
            }

            if ($data_previsao_parto!='0000-00-00' AND 
                $nascido_aborto!='') {
                $data_previsao_parto='0000-00-00';
            }

            if ($num_partos==0 && $num_aborto=='' && $num_natimorto=='' && $num_coberturas=='') {
                $vaca_solteira = '';
            }

            if ($wcategoria=="" && 
                $descarte==$vaca_descarte && 
                $data_previsao_parto>=$previsao_parto_de &&  
                $data_previsao_parto<=$previsao_parto_ate && 

                (($filtro_solteiras==$vaca_solteira && 
                 ($previsao_parto_edi=='' || 
                 ($nascido=='N' || $nascido=='A' || 
                 $nascido=='M' || $nascido=='O'))) ||

                 ($filtro_paridas==$vaca_parida && 
                 $ultimo_parto>=$data_paridas_de && 
                 $ultimo_parto<=$data_paridas_ate)) &&

                $filtro_parto==$tem_parto &&
                $filtro_aborto==$tem_aborto && 
                $filtro_positivo==$tem_positivo &&
                $filtro_negativo==$tem_negativo 
                ) {

                    if ($diagnostico=='N') {
                        $previsao_parto_edi='';
                        $desc_diagnostico = 'Negativo';
                    }
                    else if ($diagnostico=='P') {
                        switch ($nascido) {
                            case 'N':
                                $desc_diagnostico = 'Nascido';
                                break;
                            case 'A':
                                $desc_diagnostico = 'Aborto';
                                break;
                            case 'M':
                                $desc_diagnostico = 'Natimorto';
                                break;
                            case 'S':
                                $desc_diagnostico = 'Outro';
                                break;
                            default:
                                $desc_diagnostico = 'Positivo';
                                break;       
                        }
                    }
                    else {
                        $desc_diagnostico = '';
                    }
*/

?>