<?php
    // Relatórios da Fazenda (form_rel_fazenda.php): lê a tbl_resumo_diario da fazenda no período.
    // POST: fazenda (tbl_pessoa_id), inicio e fim (AAAA-MM-DD)
    // Retorno JSON: { ok, fazenda_nome, registros: [ {data, tipo, usuario} ] }
    @ session_start();
    include "conecta_mysql.inc";

    header('Content-Type: application/json; charset=utf-8');

    function responder($dados) {
        echo json_encode($dados);
        exit;
    }

    if (!isset($_SESSION['id_usuario'])) {
        responder(array('ok' => false, 'erro' => 'Sessão expirada.'));
    }

    $fazenda = isset($_POST['fazenda']) ? (int) $_POST['fazenda'] : 0;
    $inicio  = isset($_POST['inicio']) ? (string) $_POST['inicio'] : '';
    $fim     = isset($_POST['fim']) ? (string) $_POST['fim'] : '';

    $data_valida = '/^\d{4}-\d{2}-\d{2}$/';
    if ($fazenda <= 0 || !preg_match($data_valida, $inicio) || !preg_match($data_valida, $fim)) {
        responder(array('ok' => false, 'erro' => 'Filtro inválido.'));
    }

    // Só deixa consultar fazenda liberada para o usuário logado
    $codigo_usuario = (int) $_SESSION['id_usuario'];
    $liberada = false;

    $query_usuario = mysqli_query($conector_acesso, "SELECT local_usuario FROM usuario
                                                     WHERE id_usuario = $codigo_usuario AND lixeira_usuario = 0");
    if ($query_usuario && ($reg_usuario = mysqli_fetch_assoc($query_usuario))) {
        foreach (explode(',', $reg_usuario['local_usuario']) as $value) {
            if ((int) trim($value) === $fazenda) {
                $liberada = true;
                break;
            }
        }
    }

    if (!$liberada) {
        responder(array('ok' => false, 'erro' => 'Fazenda não liberada para o usuário.'));
    }

    // Nome da fazenda
    $fazenda_nome = '';
    $st = mysqli_prepare($conector, "SELECT tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id = ?");
    mysqli_stmt_bind_param($st, 'i', $fazenda);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $fazenda_nome);
    mysqli_stmt_fetch($st);
    mysqli_stmt_close($st);

    // Resumos do período
    $registros = array();
    $st = mysqli_prepare($conector, "SELECT tbl_resumo_diario_data, tbl_resumo_diario_tipo, tbl_resumo_diario_incluido_por,
                                            tbl_resumo_diario_chuva_mm, tbl_resumo_diario_mudou_pasto, tbl_resumo_diario_nutricao,
                                            tbl_resumo_diario_nascimentos, tbl_resumo_diario_abortos, tbl_resumo_diario_mortes,
                                            tbl_resumo_diario_conferiu_gado, tbl_resumo_diario_pastos_conferidos,
                                            tbl_resumo_diario_animais_esperados, tbl_resumo_diario_animais_encontrados,
                                            tbl_resumo_diario_conferiu_prenhas, tbl_resumo_diario_pastos_diferenca,
                                            tbl_resumo_diario_obs_rebanho
                                       FROM tbl_resumo_diario
                                      WHERE tbl_resumo_diario_fazenda = ?
                                        AND tbl_resumo_diario_data BETWEEN ? AND ?
                                        AND tbl_resumo_diario_lixeira = 0
                                      ORDER BY tbl_resumo_diario_data");
    if (!$st) {
        responder(array('ok' => false, 'erro' => 'Falha ao consultar o resumo diário.'));
    }
    mysqli_stmt_bind_param($st, 'iss', $fazenda, $inicio, $fim);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $data, $tipo, $usuario, $chuva_mm, $mudou_pasto, $nutricao, $nascimentos, $abortos, $mortes, $conferiu_gado, $pastos_conferidos,
                            $animais_esperados, $animais_encontrados, $conferiu_prenhas, $pastos_diferenca, $obs_rebanho);
    // Listas de pastos gravadas pelo app em JSON [{id, nome}, ...] -> só os nomes
    function nomes_dos_pastos($json) {
        $nomes = array();
        $lista = json_decode((string) $json, true);
        if (is_array($lista)) {
            foreach ($lista as $pasto) {
                if (is_array($pasto) && isset($pasto['nome']) && trim($pasto['nome']) !== '') {
                    $nomes[] = trim($pasto['nome']);
                }
            }
        }
        return $nomes;
    }

    while (mysqli_stmt_fetch($st)) {
        $nomes_pastos = nomes_dos_pastos($pastos_conferidos);

        $registros[] = array(
            'data'                => $data,
            'tipo'                => $tipo,
            'usuario'             => (string) $usuario,
            'chuva_mm'            => $chuva_mm === null ? null : (float) $chuva_mm,
            'mudou_pasto'         => $mudou_pasto === null ? null : (string) $mudou_pasto, // S, N
            'nutricao'            => $nutricao === null ? null : (string) $nutricao,       // S, N
            'nascimentos'         => $nascimentos === null ? null : (int) $nascimentos,
            'abortos'             => $abortos === null ? null : (int) $abortos,
            'mortes'              => $mortes === null ? null : (int) $mortes,
            'conferiu_gado'     => $conferiu_gado === null ? null : (string) $conferiu_gado, // T, P, N
            'pastos_conferidos'   => $nomes_pastos,
            'animais_esperados'   => $animais_esperados === null ? null : (int) $animais_esperados,
            'animais_encontrados' => $animais_encontrados === null ? null : (int) $animais_encontrados,
            'conferiu_prenhas'    => $conferiu_prenhas === null ? null : (string) $conferiu_prenhas, // S, N
            'pastos_diferenca'    => nomes_dos_pastos($pastos_diferenca), // pastos onde o encontrado não bateu
            'obs_rebanho'         => trim((string) $obs_rebanho)
        );
    }
    mysqli_stmt_close($st);

    // Diário: listas que o app mostra na Seção 1 do Resumo (mesmas consultas de
    // ResumoDiarioDao::pastosAlteradosNoDia e ::nutricoesDoDia, aqui com a conexão do sistema web)
    $pastos_alterados = array();
    $nutricoes = array();

    if ($inicio === $fim && count($registros) > 0) {
        $dia_inicio = $inicio . ' 00:00:00';
        $dia_fim    = $inicio . ' 23:59:59';

        // Pastos com troca de animais no dia
        $st = mysqli_prepare($conector, "SELECT DISTINCT tbl_pasto_descricao
                                           FROM tbl_pasto
                                          WHERE tbl_pasto_codigo_local = ? AND tbl_pasto_lixeira = 0
                                            AND (tbl_pasto_data_sem_animais BETWEEN ? AND ?
                                                 OR tbl_pasto_data_com_animais BETWEEN ? AND ?
                                                 OR tbl_pasto_id IN (
                                                    SELECT tbl_animal_pasto_id FROM tbl_animal_pasto
                                                     WHERE tbl_animal_pasto_local = ?
                                                       AND tbl_animal_pasto_alterado_em BETWEEN ? AND ?))
                                          ORDER BY tbl_pasto_descricao");
        if ($st) {
            mysqli_stmt_bind_param($st, 'issssiss', $fazenda, $dia_inicio, $dia_fim, $dia_inicio, $dia_fim,
                                   $fazenda, $dia_inicio, $dia_fim);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $nome_pasto);
            while (mysqli_stmt_fetch($st)) {
                $pastos_alterados[] = (string) $nome_pasto;
            }
            mysqli_stmt_close($st);
        }

        // Nutrição distribuída no dia, por pasto e produto
        $st = mysqli_prepare($conector, "SELECT tbl_pasto_descricao, tbl_produto_descricao,
                                                SUM(tbl_nutricao_quantidade_produto), tab_codigo_unidade_produtos
                                           FROM tbl_nutricao
                                           JOIN tbl_pasto ON tbl_pasto_id = tbl_nutricao_codigo_pasto
                                           JOIN tbl_produto ON tbl_produto_codigo_id = tbl_nutricao_codigo_produto
                                           LEFT JOIN tabela_unidade_produtos
                                                  ON tab_codigo_unidade_id = tbl_produto_unidade
                                          WHERE tbl_nutricao_codigo_local = ? AND tbl_nutricao_data = ?
                                            AND tbl_nutricao_lixeira = 0
                                          GROUP BY tbl_pasto_descricao, tbl_produto_descricao, tab_codigo_unidade_produtos
                                          ORDER BY tbl_pasto_descricao, tbl_produto_descricao");
        if ($st) {
            mysqli_stmt_bind_param($st, 'is', $fazenda, $inicio);
            mysqli_stmt_execute($st);
            mysqli_stmt_bind_result($st, $nut_pasto, $nut_produto, $nut_quantidade, $nut_unidade);
            while (mysqli_stmt_fetch($st)) {
                $nutricoes[] = array(
                    'pasto'      => (string) $nut_pasto,
                    'produto'    => (string) $nut_produto,
                    'quantidade' => (float) $nut_quantidade,
                    'unidade'    => (string) $nut_unidade
                );
            }
            mysqli_stmt_close($st);
        }
    }

    responder(array('ok' => true, 'fazenda_nome' => (string) $fazenda_nome, 'registros' => $registros,
                    'pastos_alterados' => $pastos_alterados, 'nutricoes' => $nutricoes));
