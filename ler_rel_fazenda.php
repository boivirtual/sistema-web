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
                                            tbl_resumo_diario_chuva_mm,
                                            tbl_resumo_diario_conferiu_gado, tbl_resumo_diario_pastos_conferidos,
                                            tbl_resumo_diario_animais_esperados, tbl_resumo_diario_animais_encontrados,
                                            tbl_resumo_diario_conferiu_prenhas
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
    mysqli_stmt_bind_result($st, $data, $tipo, $usuario, $chuva_mm, $conferiu_gado, $pastos_conferidos,
                            $animais_esperados, $animais_encontrados, $conferiu_prenhas);
    while (mysqli_stmt_fetch($st)) {
        // pastos_conferidos: JSON gravado pelo app [{id, nome}, ...] -> só os nomes
        $nomes_pastos = array();
        $lista_pastos = json_decode((string) $pastos_conferidos, true);
        if (is_array($lista_pastos)) {
            foreach ($lista_pastos as $pasto) {
                if (is_array($pasto) && isset($pasto['nome']) && trim($pasto['nome']) !== '') {
                    $nomes_pastos[] = trim($pasto['nome']);
                }
            }
        }

        $registros[] = array(
            'data'                => $data,
            'tipo'                => $tipo,
            'usuario'             => (string) $usuario,
            'chuva_mm'            => $chuva_mm === null ? null : (float) $chuva_mm,
            'conferiu_gado'       => $conferiu_gado === null ? null : (string) $conferiu_gado, // T, P, N
            'pastos_conferidos'   => $nomes_pastos,
            'animais_esperados'   => $animais_esperados === null ? null : (int) $animais_esperados,
            'animais_encontrados' => $animais_encontrados === null ? null : (int) $animais_encontrados,
            'conferiu_prenhas'    => $conferiu_prenhas === null ? null : (string) $conferiu_prenhas // S, N
        );
    }
    mysqli_stmt_close($st);

    responder(array('ok' => true, 'fazenda_nome' => (string) $fazenda_nome, 'registros' => $registros));
