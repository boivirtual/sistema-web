<?php
/**
 * Mapa de Gado (Tabuleiro) — mover TODOS os animais de um pasto para
 * outro, chamado pelo aplicativo (inclusive reenvio de ações feitas
 * offline). Mesma regra de transferir_tudo_mapa_gados.php (sistema web):
 *
 *   - datas com/sem animais dos dois pastos (janela de 24h);
 *   - Premissa 1: destino SEM descrição do lote e origem COM -> a
 *     descrição vai para o destino e é limpa na origem;
 *   - Premissa 6: senão, mantém a do destino e limpa a da origem;
 *   - nutrição do dia passa para o pasto destino;
 *   - tbl_animal_pasto ativos da origem passam para o destino.
 *
 * Diferença deliberada: "agora" é a data/hora em que a ação foi feita no
 * aparelho (data_hora), não a hora em que chegou ao servidor — uma ação
 * feita offline e enviada horas depois grava as mesmas datas que gravaria
 * se tivesse internet na hora.
 *
 * Reenvio seguro: se o pasto de origem já não tem animais ativos (ação já
 * aplicada antes, resposta perdida no caminho, ou alguém já esvaziou o
 * pasto), não altera nada e responde sucesso com "ignorado": true.
 *
 * Entrada — JSON no corpo do POST:
 *   bd, origem, destino (tbl_pasto_id), usuario, data_hora (Y-m-d H:i:s)
 *
 * Saída: { "success": true|false, "message": "...", "ignorado": bool,
 *          "descricao_lote_pasto_destino": "..." }
 */

require_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";

header('Content-Type: application/json; charset=utf-8');

function responder_transferencia($dados) {
    echo json_encode($dados);
    exit;
}

function erro_transferencia_app($con, $mensagem) {
    mysqli_rollback($con);
    responder_transferencia(["success" => false, "message" => $mensagem]);
}

function data_pasto_ou_agora_app($valor, $agora) {
    if ($valor === null || $valor === '' || $valor === '0000-00-00' || $valor === '0000-00-00 00:00:00') {
        return $agora;
    }
    return $valor;
}

/** Mesma conta do web: horas entre "agora" e a data informada. */
function horas_desde_app($agora, $data) {
    $diff = (new DateTime($agora))->diff(new DateTime($data ?? 'now'));
    return $diff->h + ($diff->days * 24);
}

mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    responder_transferencia(["success" => false, "message" => "Requisição inválida."]);
}

$bd = trim((string) ($dados['bd'] ?? ''));
$origem = (int) ($dados['origem'] ?? 0);
$destino = (int) ($dados['destino'] ?? 0);

if ($bd === '' || $origem <= 0 || $destino <= 0) {
    responder_transferencia(["success" => false, "message" => "Não foi possível identificar o pasto de origem ou de destino."]);
}
if ($origem === $destino) {
    responder_transferencia(["success" => false, "message" => "O pasto de origem e o de destino são o mesmo."]);
}

$dataHora = DateTime::createFromFormat('Y-m-d H:i:s', (string) ($dados['data_hora'] ?? ''));
$data_sistema = $dataHora ? $dataHora->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
$data_atual = substr($data_sistema, 0, 10);

$con = @mysqli_connect($servidor, $usuario_bd, $senha_bd, $bd);
if (!$con) {
    responder_transferencia(["success" => false, "message" => "Não foi possível conectar ao banco."]);
}
mysqli_set_charset($con, "utf8");

$nome_usuario = mysqli_real_escape_string($con, mb_substr(trim((string) ($dados['usuario'] ?? '')), 0, 30));

$camposPasto = "tbl_pasto_id, tbl_pasto_descricao_lote, tbl_pasto_id_lote, tbl_pasto_ano_lote,
                tbl_pasto_descricao_lote_1, tbl_pasto_descricao_lote_2, tbl_pasto_descricao_lote_3,
                tbl_pasto_descricao_lote_4, tbl_pasto_descricao_lote_5, tbl_pasto_descricao_lote_6,
                tbl_pasto_data_com_animais, tbl_pasto_data_com_animais_anterior,
                tbl_pasto_data_sem_animais, tbl_pasto_data_sem_animais_anterior";

mysqli_begin_transaction($con);

$res = mysqli_query($con, "SELECT {$camposPasto} FROM tbl_pasto
    WHERE tbl_pasto_id = {$origem} AND tbl_pasto_lixeira = 0 FOR UPDATE");
$reg_remover = $res ? mysqli_fetch_object($res) : null;
if (!$reg_remover) {
    erro_transferencia_app($con, 'O pasto de origem não foi encontrado.');
}

$res = mysqli_query($con, "SELECT {$camposPasto} FROM tbl_pasto
    WHERE tbl_pasto_id = {$destino} AND tbl_pasto_lixeira = 0 FOR UPDATE");
$reg_incluir = $res ? mysqli_fetch_object($res) : null;
if (!$reg_incluir) {
    erro_transferencia_app($con, 'O pasto de destino não foi encontrado.');
}

// Reenvio seguro (ver docblock).
$res = mysqli_query($con, "SELECT COUNT(*) AS qtd FROM tbl_animal_pasto
    WHERE tbl_animal_pasto_id = {$origem} AND tbl_animal_pasto_situacao = 'A'");
$qtd_origem = $res ? (int) mysqli_fetch_assoc($res)['qtd'] : 0;
if ($qtd_origem === 0) {
    mysqli_rollback($con);
    responder_transferencia([
        "success" => true,
        "ignorado" => true,
        "message" => "O pasto de origem já não tem animais.",
        "descricao_lote_pasto_destino" => (string) $reg_incluir->tbl_pasto_descricao_lote,
    ]);
}

$esc = function ($v) use ($con) {
    return mysqli_real_escape_string($con, (string) $v);
};

$descricao_lote_remover = $reg_remover->tbl_pasto_descricao_lote;
$descricao_lote_pasto_destino = $reg_incluir->tbl_pasto_descricao_lote;

$data_com_remover = $reg_remover->tbl_pasto_data_com_animais;
$data_sem_remover = data_pasto_ou_agora_app($reg_remover->tbl_pasto_data_sem_animais, $data_sistema);
$data_sem_remover_anterior = data_pasto_ou_agora_app($reg_remover->tbl_pasto_data_sem_animais_anterior, $data_sistema);

$data_com_incluir = $reg_incluir->tbl_pasto_data_com_animais;
$data_com_incluir_anterior = data_pasto_ou_agora_app($reg_incluir->tbl_pasto_data_com_animais_anterior, $data_sistema);
$data_sem_incluir = data_pasto_ou_agora_app($reg_incluir->tbl_pasto_data_sem_animais, $data_sistema);

// Igual ao web: conta todas as linhas do pasto destino, de qualquer situação.
$res = mysqli_query($con, "SELECT COUNT(*) AS qtd FROM tbl_animal_pasto WHERE tbl_animal_pasto_id = {$destino}");
$qtd_animais_pasto_entrada = $res ? (int) mysqli_fetch_assoc($res)['qtd'] : 0;

$limpar_lote = "tbl_pasto_descricao_lote = null,
    tbl_pasto_id_lote = null,
    tbl_pasto_ano_lote = null,
    tbl_pasto_descricao_lote_1 = null,
    tbl_pasto_descricao_lote_2 = null,
    tbl_pasto_descricao_lote_3 = null,
    tbl_pasto_descricao_lote_4 = null,
    tbl_pasto_descricao_lote_5 = null,
    tbl_pasto_descricao_lote_6 = null";

// PASTO DE ORIGEM — datas
if (horas_desde_app($data_sistema, $data_com_remover) < 24) {
    $d = $esc($data_sem_remover_anterior);
    $sql = "UPDATE tbl_pasto SET
            tbl_pasto_alterado_em = '{$data_sistema}',
            tbl_pasto_alterado_por = '{$nome_usuario}',
            tbl_pasto_data_com_animais = '{$d}',
            tbl_pasto_data_com_animais_anterior = '{$d}',
            tbl_pasto_data_sem_animais = '{$d}',
            tbl_pasto_data_sem_animais_anterior = '{$d}'
        WHERE tbl_pasto_id = {$origem}";
    if (!mysqli_query($con, $sql)) {
        erro_transferencia_app($con, 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . mysqli_error($con));
    }
} else {
    $d = $esc($data_sem_remover);
    $sql = "UPDATE tbl_pasto SET
            {$limpar_lote},
            tbl_pasto_alterado_em = '{$data_sistema}',
            tbl_pasto_alterado_por = '{$nome_usuario}',
            tbl_pasto_data_sem_animais = '{$data_sistema}',
            tbl_pasto_data_sem_animais_anterior = '{$d}'
        WHERE tbl_pasto_id = {$origem}";
    if (!mysqli_query($con, $sql)) {
        erro_transferencia_app($con, 'Ocorreu um erro ao atualizar as datas SEM ' . mysqli_error($con));
    }
}

// PASTO DE DESTINO — datas (só quando não tinha nenhum registro de animal)
if ($qtd_animais_pasto_entrada == 0) {
    $d = $esc($data_com_incluir_anterior);
    $voltarDatas = "UPDATE tbl_pasto SET
            tbl_pasto_alterado_em = '{$data_sistema}',
            tbl_pasto_alterado_por = '{$nome_usuario}',
            tbl_pasto_data_com_animais = '{$d}',
            tbl_pasto_data_com_animais_anterior = '{$d}',
            tbl_pasto_data_sem_animais = '{$d}',
            tbl_pasto_data_sem_animais_anterior = '{$d}'
        WHERE tbl_pasto_id = {$destino}";

    if (horas_desde_app($data_sistema, $data_com_incluir) < 24 ||
        horas_desde_app($data_sistema, $data_sem_incluir) < 24) {
        $sql = $voltarDatas;
    } else {
        $dc = $esc($data_com_incluir);
        $sql = "UPDATE tbl_pasto SET
                tbl_pasto_alterado_em = '{$data_sistema}',
                tbl_pasto_alterado_por = '{$nome_usuario}',
                tbl_pasto_data_com_animais = '{$data_sistema}',
                tbl_pasto_data_com_animais_anterior = '{$dc}'
            WHERE tbl_pasto_id = {$destino}";
    }
    if (!mysqli_query($con, $sql)) {
        erro_transferencia_app($con, 'Ocorreu um erro ao atualizar as datas COM ' . mysqli_error($con));
    }
}

// DESCRIÇÃO DO LOTE
if ($descricao_lote_remover != '' && $descricao_lote_pasto_destino == '') {
    // Premissa 1
    $sql = "UPDATE tbl_pasto SET
        tbl_pasto_descricao_lote = '" . $esc($descricao_lote_remover) . "',
        tbl_pasto_id_lote = '" . $esc($reg_remover->tbl_pasto_id_lote) . "',
        tbl_pasto_ano_lote = '" . $esc($reg_remover->tbl_pasto_ano_lote) . "',
        tbl_pasto_descricao_lote_1 = '" . $esc($reg_remover->tbl_pasto_descricao_lote_1) . "',
        tbl_pasto_descricao_lote_2 = '" . $esc($reg_remover->tbl_pasto_descricao_lote_2) . "',
        tbl_pasto_descricao_lote_3 = '" . $esc($reg_remover->tbl_pasto_descricao_lote_3) . "',
        tbl_pasto_descricao_lote_4 = '" . $esc($reg_remover->tbl_pasto_descricao_lote_4) . "',
        tbl_pasto_descricao_lote_5 = '" . $esc($reg_remover->tbl_pasto_descricao_lote_5) . "',
        tbl_pasto_descricao_lote_6 = '" . $esc($reg_remover->tbl_pasto_descricao_lote_6) . "',
        tbl_pasto_alterado_em = '{$data_sistema}',
        tbl_pasto_alterado_por = '{$nome_usuario}'
    WHERE tbl_pasto_id = {$destino}";
    if (!mysqli_query($con, $sql)) {
        erro_transferencia_app($con, 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Destino ' . mysqli_error($con));
    }
    $descricao_lote_pasto_destino = $descricao_lote_remover;
}

// Premissas 1 e 6: a origem sempre fica sem descrição do lote.
$sql = "UPDATE tbl_pasto SET
    {$limpar_lote},
    tbl_pasto_alterado_em = '{$data_sistema}',
    tbl_pasto_alterado_por = '{$nome_usuario}'
WHERE tbl_pasto_id = {$origem}";
if (!mysqli_query($con, $sql)) {
    erro_transferencia_app($con, 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Origem ' . mysqli_error($con));
}

// Nutrição do dia vai junto (igual ao web, sem bloquear se falhar).
mysqli_query($con, "UPDATE tbl_nutricao SET tbl_nutricao_codigo_pasto = {$destino}
    WHERE tbl_nutricao_codigo_pasto = {$origem} AND tbl_nutricao_data = '{$data_atual}'");

// ANIMAIS
$sql = "UPDATE tbl_animal_pasto SET
    tbl_animal_pasto_id = {$destino},
    tbl_animal_pasto_alterado_em = '{$data_sistema}',
    tbl_animal_pasto_alterado_por = '{$nome_usuario}'
WHERE tbl_animal_pasto_id = {$origem} AND tbl_animal_pasto_situacao = 'A'";
if (!mysqli_query($con, $sql)) {
    erro_transferencia_app($con, 'Ocorreu um erro ao atualizar os animais no pasto ' . mysqli_error($con));
}

mysqli_commit($con);
mysqli_close($con);

responder_transferencia([
    "success" => true,
    "ignorado" => false,
    "message" => "Animais movidos com sucesso.",
    "descricao_lote_pasto_destino" => (string) $descricao_lote_pasto_destino,
]);
