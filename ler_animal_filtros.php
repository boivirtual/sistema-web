
<?php
	include "conecta_mysql.inc";

	for ($i=1; $i<=31; $i++){
	    $valor[$i]=0;
	}

    $animais_listados=0;

    $codigo_alfa_numerico = $_POST["codigo_alfa_numerico"];
    $num_parto_de = $_POST['num_parto_de'];
    $num_parto_ate = $_POST['num_parto_ate'];
    $num_aborto_de = $_POST['num_aborto_de'];
    $num_aborto_ate = $_POST['num_aborto_ate'];
    $previsao_parto_de = $_POST["previsao_parto_de"];
    $previsao_parto_ate = $_POST["previsao_parto_ate"];
    $data_paricao_de = $_POST["data_paricao_de"];
    $data_paricao_ate = $_POST["data_paricao_ate"];
    $filtro_reproducao = $_POST["filtro_reproducao"];
    $filtro_num_parto = $_POST['filtro_num_parto'];
    $filtro_num_aborto = $_POST['filtro_num_aborto'];
    $filtro_previsao_parto = $_POST['filtro_previsao_parto'];
    $filtro_data_paricao = $_POST['filtro_data_paricao'];
    $filtro_vacas_paridas = $_POST['filtro_vacas_paridas'];
    $filtro_vacas_solteiras = $_POST['filtro_vacas_solteiras'];
    $filtro_vacas_prenhas = $_POST['filtro_vacas_prenhas'];
    $filtro_descarte = $_POST["filtro_descarte"];
    $filtro_positivas = $_POST["filtro_positivas"];
    $filtro_negativas = $_POST["filtro_negativas"];
    $filtro_monta_natural = $_POST["filtro_monta_natural"];

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

        $wlocal = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal.= $local;
        $wlocal.= ")";

        $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= ")";
        $wlocal_anterior.= " OR (tbl_animal_codigo_origem IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= ") AND tbl_animal_situacao='V'))";
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

    // Pega estacao atual da fazenda (ultima estacao)
    $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
        WHERE tbl_par_codigo_local='$local' AND 
              tbl_par_lixeira=0
        ORDER BY tbl_par_estacao_id DESC LIMIT 1");

    $num_rows_estacao = mysqli_num_rows($tbl_estacao);

    if ($num_rows_estacao!=0) {
        $reg_estacao = mysqli_fetch_object($tbl_estacao);
        $id_estacao_atual = $reg_estacao->tbl_par_estacao_id;
        $desc_estacao_atual = $reg_estacao->tbl_par_estacao_nome;
    }
    else {
        $id_estacao_atual = 0;
    }

    //if ($codigo_alfa_numerico!='') {
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
    /*}
    else {
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
    }*/

    $tbl_animais = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($tbl_animais);

    if ($num_rows_animais==0) {
    	$valor[0]='Nao tem animal';
		$valor[1]='Animal não cadastrado com esse código.';
		$str=$valor[0] . '<|>' . $valor[1] . '<|>';
		echo $str; 
		mysqli_close($conector);
		exit;	
    }

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
                       tbl_cobertura_codigo_local = '$local' AND 
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

        //if ($wcategoria=="") {
            $animalAtual = [
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
            'desc_estacao' => $desc_estacao
            ];
            $animais[] = $animalAtual;
        /*}
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
                    'selecionadaCoberturaControle' => $selecionadaCoberturaControle
                    ];

                    $animais[] = $animalAtual;
                }
            }
        }*/
    }

    //print_r($animais);


    $tem_categoria = 'N';
	$tem_origem = 'N';
	$tem_raca = 'N';
	$tem_pai = 'N';
	$tem_mae = 'N';
	$tem_sexo = 'N';
	$tem_data_nasc = 'N';
	$tem_peso_nasc = 'N';
	$tem_peso_desmama = 'N';
	$tem_peso_ult = 'N';
	$tem_solteira = 'N';
	$tem_parida = 'N';
	$tem_descarte = 'N';
	$tem_parto = 'N';
	$tem_aborto = 'N';
	$ultimo_parto = '0000-00-00';
	$tem_previsao_parto = "N";
	$tem_positivo = 'N';
	$tem_negativo = 'N';
	$nascido = '';
    $tem_data_paricao='N';  
    $tem_prenha = 'N';  

    foreach ($animais as $animal) {

        $nascimento = new DateTime($animal['dataNascimento']);
        $nascimento_edi = $nascimento->format('d/m/Y');

        if ($animal['descarte']=='S') {
            $descarte = 'DESCARTE';
            $tem_descarte = 'S';
        }
        else {
            $descarte = '';
        }

        $femea_selecionada_cobertura = '';
        $controle = '';

        if ($animal['selecionadaCoberturaControle']=='C') {
            $femea_selecionada_cobertura = 'S';
            $controle = 'C';
        }
        else if ($animal['selecionadaCoberturaControle']=='M') {
            $femea_selecionada_cobertura = 'S';
            $controle = 'M';
        }

        $animais_listados++;

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
                        $tem_parida = 'S';
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $tem_solteira='S';
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
                        $tem_prenha = 'S';
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
                        $tem_parida = 'S';
                        $tem_prenha = 'S';
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
                        $tem_prenha = 'S';
                        $tem_solteira='S';
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        ($animal['filtroVacasNegativas']=='S' ||
                        $animal['filtroVacasPositivas']=='S')) {
                        $tem_parida = 'S';
                        $tem_solteira='S';
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
                        $tem_parida = 'S';
                        $tem_prenha = 'S';
                        $tem_solteira='S';
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
                        $tem_parida = 'S';
                    }
                }
                else if ($filtro_vacas_paridas=='N' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        $animal['filtroVacasSolteiras']=='S' &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $tem_solteira='S';
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
                        $tem_prenha = 'S';
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
                        $tem_parida = 'S';
                        $tem_prenha = 'S';
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
                        $tem_prenha = 'S';
                        $tem_solteira='S';
                    }
                }
                else if ($filtro_vacas_paridas=='S' && $filtro_vacas_solteiras=='S' && $filtro_vacas_prenhas=='N') {
                    if ($animal['filtroNumPartos']=='S' && 
                        $animal['filtroNumAbortos']=='S' &&
                        $animal['filtroDataPrevisao']=='S' && 
                        $animal['filtroDataParicao']=='S' && 
                        $animal['filtroDescarte']=='S' && 
                        ($animal['filtroVacasParidas']=='S' ||
                         $animal['filtroVacasSolteiras']=='S') &&
                        $animal['filtroVacasNegativas']=='S' &&
                        $animal['filtroVacasPositivas']=='S') {
                        $tem_parida = 'S';
                        $tem_solteira='S';
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
                        $tem_parida = 'S';
                        $tem_prenha = 'S';
                        $tem_solteira='S';
                    }
                }                
            }
        }
        else {
        }
    }

    if ($animais_listados>0) {

        if ($wcategoria=='') {
            $tem_categoria = 'S';
        }
        else {
            foreach ($wcategoria as $value) {
                if ($value==$animal['codigoCategoria']) {
                    $tem_categoria = 'S';
                }
            }
        }

        foreach ($sexo as $value) {
            if ($value==$animal['sexo']) {
                $tem_sexo = 'S';
            }
        }

        if ($animal['sexo']=='M') {
            $sexo_animal = 'Macho';
        }
        else {
            $sexo_animal = 'Fêmea';
        }

        foreach ($raca as $value) {
            if ($value==$animal['codigoRaca']) {
                $tem_raca = 'S';
            }
        }

        foreach ($mae as $value) {
            if ($value==$animal['codigoMae']) {
                $tem_mae = 'S';
            }
        }

        foreach ($pai as $value) {
            if ($value==$animal['codigoPai']) {
                $tem_pai = 'S';
            }
        }

        foreach ($origem as $value) {
            if ($value==$animal['codigoOrigem']) {
                $tem_origem = 'S';
            }
        }

        if ($animal['dataNascimento']>=$data_nasc_inicial && $animal['dataNascimento']<=$data_nasc_final) {
            $tem_data_nasc='S';
        }

        if ($animal['primeiroPeso']>=$peso_nasc_inicial && $animal['primeiroPeso']<=$peso_nasc_final) {
            $tem_peso_nasc='S';
        }

        if ($animal['pesoDesmama']>=$peso_desmama_inicial && $animal['pesoDesmama']<=$peso_desmama_final) {
            $tem_peso_desmama='S';
        }

        if ($animal['ultimoPeso']>=$peso_ult_inicial && $animal['ultimoPeso']<=$peso_ult_final) {
            $tem_peso_ult='S';
        }

        if ($animal['numPartos']>=$num_parto_de && $animal['numPartos']<=$num_parto_ate) {
            $tem_parto='S';
        }

        if ($animal['numAbortos']>=$num_aborto_de && $animal['numAbortos']<=$num_aborto_ate) {
            $tem_aborto='S';
        }

        if ($animal['dataPrevisaoParto']>=$previsao_parto_de && $animal['dataPrevisaoParto']<=$previsao_parto_ate) {
            $tem_previsao_parto='S';
        }

        if ($animal['dataNascimentoFilho']>=$data_paricao_de && $animal['dataNascimentoFilho']<=$data_paricao_ate) {
            $tem_data_paricao='S';
        }

        if ($filtro_positivas=='S') {
            if ($animal['filtroVacasPositivas']=='S') {
                if ($tem_parida=='S') {
                    $tem_positivo = 'N';
                }
                else {
                    $tem_positivo = 'S';
                }
            }
        }

        if ($filtro_negativas=='S') {
            if ($animal['filtroVacasNegativas']=='S') {
                $tem_negativo = 'S';
            }
            else {
                $tem_negativo = 'N';
            }
        }

        $descricao_animal = $sexo_animal . ' - Nasc: ' . $nascimento_edi . ' - '. $animal['descricaoRaca'] . ' ' . $animal['descricaoPelagem'] . ' - Mãe: ' . $animal['codigoMaeAlfaNumerico'];

        if ($animal['ultimoPeso']!=0 && $animal['ultimoPeso']!='') {
            $peso = $animal['ultimoPeso'];
        }
        else if ($animal['pesoDesmama']!=0 && $animal['pesoDesmama']!='') {
            $peso = $animal['pesoDesmama'];
        }
        else if ($animal['primeiroPeso']!=0 && $animal['primeiroPeso']!=''){
            $peso = $animal['primeiroPeso'];
        }
        else {
            $peso = 0;
        }

        if ($nascido!='') {
            $em_estacao_monta = 'N';
        }
        else {
            $em_estacao_monta = 'S';
        }

        $valor[0] = $animal['codigoAnimal'];
        $valor[1] = $sexo_animal;
        $valor[2] = $nascimento_edi;
        $valor[3] = $animal['descricaoRaca'];
        $valor[4] = $animal['descricaoPelagem'];
        $valor[5] = $animal['codigoMaeAlfaNumerico'];
        $valor[6] = $descricao_animal;
        $valor[7] = $tem_categoria;
        $valor[8] = $tem_sexo;
        $valor[9] = $tem_raca;
        $valor[10] = $tem_pai;
        $valor[11] = $tem_mae;
        $valor[12] = $tem_data_nasc;
        $valor[13] = $tem_peso_nasc;
        $valor[14] = $tem_peso_desmama;
        $valor[15] = $tem_peso_ult;
        $valor[16] = $animal['codigoCategoria'];
        $valor[17] = $animal['descricaoCategoria'];
        $valor[18] = $em_estacao_monta;
        $valor[19] = $tem_solteira;
        $valor[20] = $tem_descarte;
        $valor[21] = $tem_parida;
        $valor[22] = $tem_parto;
        $valor[23] = $tem_aborto;
        $valor[24] = $tem_previsao_parto;
        $valor[25] = $tem_origem;
        $valor[26] = $tem_positivo;
        $valor[27] = $tem_negativo;
        $valor[28] = $peso;
        $valor[29] = $descarte;
        $valor[30] = $tem_data_paricao;
        $valor[31] = $tem_prenha;

        $str=$valor[0] . '<|>';

        for ($i=1; $i<=31; $i++){
            $str.=$valor[$i] . '<|>';
        }
        echo $str; 
        mysqli_close($conector);
        exit;
    }
    else {
        $valor[0]='Nao tem animal';
        $valor[1]='Animal não atende os requisitos dos filtros.';
        $str=$valor[0] . '<|>' . $valor[1] . '<|>';
        echo $str; 
        mysqli_close($conector);
        exit;   
    }


/*if ($num_rows!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal);
	$codigo_id = $reg_animal->tbl_animal_codigo_id;
	$nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);
	$codigo_origem = $reg_animal->tbl_animal_codigo_origem;
	$raca = $reg_animal->tbl_animal_codigo_raca;
	$pelagem = $reg_animal->tbl_animal_codigo_pelagem;
	$local = $reg_animal->tbl_animal_codigo_fazenda;
	$sexo_animal = $reg_animal->tbl_animal_sexo;
	$pai_animal = $reg_animal->tbl_animal_codigo_pai;
	$mae_animal = $reg_animal->tbl_animal_codigo_mae;
	$data_nasc_animal = $reg_animal->tbl_animal_data_nascimento;
	$peso_nasc_animal = $reg_animal->tbl_animal_primeiro_peso;
	$peso_desmama_animal = $reg_animal->tbl_animal_peso_desmama;
	$peso_ult_animal = $reg_animal->tbl_animal_ultimo_peso;
	$animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

    $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
    $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
    $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

    if ($ultimo_peso!=0 && $ultimo_peso!='') {
        $peso = $ultimo_peso;
    }
    else if ($peso_desmama!=0 && $peso_desmama!='') {
        $peso = $peso_desmama;
    }
    else if ($primeiro_peso!=0 && $primeiro_peso!=''){
        $peso = $primeiro_peso;
    }
    else {
        $peso = 0;
    }

	if ($animal_descarte=='S') {
		$tem_descarte = 'S';
	}

	if ($reg_animal->tbl_animal_sexo=='M') {
		$sexo = 'Macho';
	}
	else {
		$sexo = 'Fêmea';
	}

	if ($local!=$local_filtro) {
		$valor[0]='Nao tem animal';
		$valor[1]='Animal não consta no local ou Id não cadastrado.';
		$str=$valor[0] . '<|>' . $valor[1] . '<|>';
		echo $str; 
		mysqli_close($conector);
		exit;	
	}

	if ($sexo_filtro!='Todos' && $sexo_filtro!=$sexo_animal) {
		$tem_sexo='N';
	}

    /*$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
    $data_acompanhamento_calculo = date("Y-m-d");
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

            if ($idade_animal >= $idade_de && $idade_animal <= $idade_ate) {
            	if ($idade_ate==999999999){
	                $desc_categoria= '> 36 meses';
            	}
            	else {
	                $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
            	}
                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
            }
        }
    }                   

    if ($categoria_filtro!='') {
		foreach ($categoria_filtro as $value) {
	        if ($value==$codigo_categoria) {
	        	$tem_categoria = 'S';
	        }
	    }
    }

    if ($origem_filtro!='') {
		foreach ($origem_filtro as $value) {
	        if ($value==$codigo_origem) {
	        	$tem_origem = 'S';
	        }
	    }
    }

    if ($raca_filtro!='') {
		foreach ($raca_filtro as $value) {
	        if ($value==$raca) {
	        	$tem_raca = 'S';
	        }
	    }
    }

    if ($pai_filtro!='') {
		foreach ($pai_filtro as $value) {
	        if ($value==$pai_animal) {
	        	$tem_pai = 'S';
	        }
	    }
    }

    if ($mae_filtro!='') {
		foreach ($mae_filtro as $value) {
	        if ($value==$mae_animal) {
	        	$tem_mae = 'S';
	        }
	    }
    }

    if ($data_nasc_animal>=$data_nasc_inicial && $data_nasc_animal<=$data_nasc_final) {
    	$tem_data_nasc='S';
    }

    if ($peso_nasc_animal>=$peso_nasc_inicial  && $peso_nasc_animal<=$peso_nasc_final) {
    	$tem_peso_nasc='S';
    }

    if ($peso_desmama_animal>=$peso_desmama_inicial  && 
    	$peso_desmama_animal<=$peso_desmama_final) {
    	$tem_peso_desmama='S';
    }

    if ($peso_ult_animal>=$peso_ult_inicial  && 
    	$peso_ult_animal<=$peso_ult_final) {
    	$tem_peso_ult='S';
    }

	$tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas
	                        WHERE tab_codigo_raca='$raca'");
	$num_rows = mysqli_num_rows($tbl_raca);

	if ($num_rows!=0) {
		$reg_raca = mysqli_fetch_object($tbl_raca);
		$desc_raca = $reg_raca->tab_descricao_raca;
	}
	else {
		$desc_raca = '';
	}

	$tbl_pelagem = mysqli_query($conector, "SELECT * FROM tabela_pelagens
	                        WHERE tab_codigo_pelagem='$pelagem'");
	$num_rows = mysqli_num_rows($tbl_pelagem);

	if ($num_rows!=0) {
		$reg_pelagem = mysqli_fetch_object($tbl_pelagem);
		$desc_pelagem = $reg_pelagem->tab_descricao_pelagem;
	}
	else {
		$desc_pelagem = '';
	}

	$tbl_mae = mysqli_query($conector, "SELECT * FROM tbl_animais
	                        WHERE tbl_animal_codigo_id='$reg_animal->tbl_animal_codigo_mae'");
	$num_rows = mysqli_num_rows($tbl_mae);

	if ($num_rows!=0) {
		$reg_mae = mysqli_fetch_object($tbl_mae);
		$codigo_mae = $reg_mae->tbl_animal_codigo_alfa . $reg_mae->tbl_animal_codigo_numerico;
	}
	else {
		$codigo_mae = '';
	}

    // verifica a cobertura do animal
    $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        INNER JOIN tbl_parametro_estacao_monta
                ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_ite_cobertura_codigo_id_animal='$codigo_id'" . $westacao . "
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

    //$ultima_estacao = $codigo_estacao;

	// verifica vacas solteiras
	if ($solteiras=='S' || $paridas=='S') {
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo_id'
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

            if ($idade < 8 && $ultimo_parto>=$data_paridas_de && $ultimo_parto<=$data_paridas_ate) {
                $tem_parida = 'S';
            }
            else {
                $tem_solteira = 'S';
            }
        }
	}

	// verifica partos
	if ($sexo == 'Fêmea' && $num_parto_de!='' && $num_parto_ate!='') {
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo_id'");

        $num_partos = mysqli_num_rows($tbl_filhos);

        if ($num_partos>=$num_parto_de && 
        	$num_partos<=$num_parto_ate && $idade_animal>=8) {
        	$tem_parto = "S";
        }
        else {
        	$tem_parto = "N";
        }
	}

	// verifica abortos
	if ($sexo == 'Fêmea' && $num_aborto_de!='' && $num_aborto_ate!='') {
	    $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
	        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
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
        	$tem_aborto = "N";
        }
	}	    

	// Verifica previsão de parto
	if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

		$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
		    INNER JOIN tbl_cobertura
		            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
		    WHERE tbl_cobertura_lixeira=0 AND
		          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
		          tbl_cobertura_controle = 'C' AND 
		          tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
		          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
		    ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

		$num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

		if ($num_rows_coberturas!=0) {
			$reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
            $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
			$cobertura_id = $reg_cobertura->tbl_cobertura_id;

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

		if ($data_previsao_parto>=$previsao_parto_de && 
			$data_previsao_parto<=$previsao_parto_ate) {

			$tem_previsao_parto = 'S';
		}
	}

	// Verifica animal em estação de monta
	$em_estacao_monta = 'N';

	$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
	    INNER JOIN tbl_cobertura
	            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
	    WHERE tbl_cobertura_lixeira=0 AND
	          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
	    	  tbl_cobertura_controle = 'C' AND 
	    	  tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
	    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

	$num_rows = mysqli_num_rows($tbl_item_cobertura);

	if ($num_rows!=0) {
		$reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
		$nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

		if ($nascido!='') {
			$em_estacao_monta = 'N';
		}
		else {
			$em_estacao_monta = 'S';
		}
 
	}

	// Fim verifica em estação de monta

	// Verifica diagnostico
	if ($positivo=='S' || $negativo=='S'){
		$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
		    INNER JOIN tbl_cobertura
		            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		    WHERE tbl_cobertura_lixeira=0 AND
		          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
		    	  tbl_cobertura_controle = 'C' AND 
		    	  tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
		    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

		$num_rows = mysqli_num_rows($tbl_item_cobertura);

		if ($num_rows!=0) {
			$reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
			$diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

			if ($diagnostico=='P'){
				$tem_positivo = 'S';
				$tem_negativo = 'N';
			} 
			else if ($diagnostico=='N') {
				$tem_negativo = 'S';
				$tem_positivo = 'N';
			}
			else {
				$tem_negativo = 'N';
				$tem_positivo = 'N';
			}			
		}
	}

    // verifica natimortos, nascidos ou abortos na estacao
    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        INNER JOIN tbl_cobertura
                ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
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

    if ($positivo=='S' AND 
        $nascido_aborto!='') {
        $tem_positivo='';
    }

    if ($animal_descarte=='S') {
    	$animal_descarte = ' DESCARTE';
    }
    else {
    	$animal_descarte = '';
    }

    $descricao_animal = $sexo . ' - Nasc: ' . $nascimento->format('d/m/Y') . ' - '. $desc_raca . ' ' . $desc_pelagem . ' - Mãe: ' . $codigo_mae;

    $valor[0] = $reg_animal->tbl_animal_codigo_id;
    /*$valor[1] = $sexo;
    $valor[2] = $nascimento->format('d/m/Y');
	$valor[3] = $desc_raca;
	$valor[4] = $desc_pelagem;
	$valor[5] = $codigo_mae;
	$valor[6] = $descricao_animal;
	$valor[7] = $tem_categoria;
	$valor[8] = $tem_sexo;
	$valor[9] = $tem_raca;
	$valor[10] = $tem_pai;
	$valor[11] = $tem_mae;
	$valor[12] = $tem_data_nasc;
	$valor[13] = $tem_peso_nasc;
	$valor[14] = $tem_peso_desmama;
	$valor[15] = $tem_peso_ult;
	$valor[16] = $codigo_categoria;
	$valor[17] = $desc_categoria;
	$valor[18] = $em_estacao_monta;
	$valor[19] = $tem_solteira;
	$valor[20] = $tem_descarte;
	$valor[21] = $tem_parida;
	$valor[22] = $tem_parto;
	$valor[23] = $tem_aborto;
	$valor[24] = $tem_previsao_parto;
	$valor[25] = $tem_origem;
	$valor[26] = $tem_positivo;
	$valor[27] = $tem_negativo;
	$valor[28] = $peso;
	$valor[29] = $animal_descarte;
	$str=$valor[0] . '<|>';

	for ($i=1; $i<=29; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;*/


?>