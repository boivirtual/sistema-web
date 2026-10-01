<?php
/**
 * Mapa de Gado (Tabuleiro) — gravar uma NOVA Descrição do Lote num pasto
 * ("Criar nova Descrição do Lote" no pasto destino, depois de mover todos
 * os animais), chamado pelo aplicativo (inclusive reenvio de ações feitas
 * offline). Mesma regra de gravar_alterar_descricao_lote.php (sistema web)
 * com novo_id = 'S':
 *
 *   - as descrições preenchidas (até 6) são reordenadas para as primeiras
 *     posições;
 *   - gera um novo id de lote na sequência da fazenda/ano
 *     (tbl_sequencia_lote_animais) e grava no pasto.
 *
 * "agora" é a data/hora em que a ação foi feita no aparelho (data_hora).
 *
 * Reenvio seguro: se o pasto já está com exatamente essa descrição, gravada
 * pelo mesmo usuário a partir dessa mesma data/hora (ação já aplicada,
 * resposta perdida no caminho), não gera outro id de lote — responde
 * sucesso com "ignorado": true.
 *
 * Entrada — JSON no corpo do POST:
 *   bd, pasto (tbl_pasto_id), descricao_lote (texto montado),
 *   lotes (lista de até 6 textos), usuario, data_hora (Y-m-d H:i:s)
 */

require_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";

header('Content-Type: application/json; charset=utf-8');

function responder_descricao_lote($dados) {
    echo json_encode($dados);
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    responder_descricao_lote(["success" => false, "message" => "Requisição inválida."]);
}

$bd = trim((string) ($dados['bd'] ?? ''));
$pasto = (int) ($dados['pasto'] ?? 0);
$descricao_lote = (string) ($dados['descricao_lote'] ?? '');
$lotesRecebidos = is_array($dados['lotes'] ?? null) ? $dados['lotes'] : [];

if ($bd === '' || $pasto <= 0) {
    responder_descricao_lote(["success" => false, "message" => "Pasto não informado."]);
}
if (trim($descricao_lote) === '') {
    responder_descricao_lote(["success" => false, "message" => "A Descrição do Lote não pode ser vazia."]);
}

$dataHora = DateTime::createFromFormat('Y-m-d H:i:s', (string) ($dados['data_hora'] ?? ''));
$data_sistema = $dataHora ? $dataHora->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
$ano_lote = substr($data_sistema, 0, 4);

// Ordena as descrições preenchidas para as primeiras posições (igual ao web).
$array_lotes = [];
foreach (array_slice($lotesRecebidos, 0, 6) as $l) {
    if ((string) $l !== '') {
        $array_lotes[] = (string) $l;
    }
}
$array_lotes = array_pad($array_lotes, 6, '');

$con = @mysqli_connect($servidor, $usuario_bd, $senha_bd, $bd);
if (!$con) {
    responder_descricao_lote(["success" => false, "message" => "Não foi possível conectar ao banco."]);
}
mysqli_set_charset($con, "utf8");

$esc = function ($v) use ($con) {
    return mysqli_real_escape_string($con, (string) $v);
};
$nome_usuario_bruto = mb_substr(trim((string) ($dados['usuario'] ?? '')), 0, 30);
$nome_usuario = $esc($nome_usuario_bruto);

mysqli_begin_transaction($con);

$res = mysqli_query($con, "SELECT tbl_pasto_codigo_local, tbl_pasto_descricao_lote,
        tbl_pasto_descricao_lote_1, tbl_pasto_descricao_lote_2, tbl_pasto_descricao_lote_3,
        tbl_pasto_descricao_lote_4, tbl_pasto_descricao_lote_5, tbl_pasto_descricao_lote_6,
        tbl_pasto_alterado_em, tbl_pasto_alterado_por
    FROM tbl_pasto WHERE tbl_pasto_id = {$pasto} FOR UPDATE");
$reg_pasto = $res ? mysqli_fetch_object($res) : null;
if (!$reg_pasto) {
    mysqli_rollback($con);
    responder_descricao_lote(["success" => false, "message" => "O pasto não foi encontrado."]);
}

// Reenvio seguro (ver docblock).
$jaAplicado = (string) $reg_pasto->tbl_pasto_descricao_lote === $descricao_lote &&
    (string) $reg_pasto->tbl_pasto_alterado_por === stripslashes($nome_usuario) &&
    (string) $reg_pasto->tbl_pasto_alterado_em >= $data_sistema;
for ($i = 0; $jaAplicado && $i < 6; $i++) {
    $campo = 'tbl_pasto_descricao_lote_' . ($i + 1);
    $jaAplicado = (string) $reg_pasto->$campo === $array_lotes[$i];
}
if ($jaAplicado) {
    mysqli_rollback($con);
    responder_descricao_lote(["success" => true, "ignorado" => true, "message" => "Descrição do Lote já gravada."]);
}

$id_fazenda = $esc($reg_pasto->tbl_pasto_codigo_local);

$res = mysqli_query($con, "SELECT tbl_sequencial_id_lote FROM tbl_sequencia_lote_animais
    WHERE tbl_sequencial_id_local = '{$id_fazenda}' AND
          tbl_sequencial_ano_lote = '{$ano_lote}'
    ORDER BY tbl_sequencial_id_lote DESC LIMIT 1 FOR UPDATE");
$reg_sequencial = $res ? mysqli_fetch_object($res) : null;
$id_lote = $reg_sequencial ? ((int) $reg_sequencial->tbl_sequencial_id_lote) + 1 : 1;

mysqli_query($con, "UPDATE tbl_sequencia_lote_animais SET
    tbl_sequencial_id_lote = '{$id_lote}',
    tbl_sequencial_ano_lote = '{$ano_lote}'
    WHERE tbl_sequencial_id_local = '{$id_fazenda}'");

$sql = "UPDATE tbl_pasto SET
    tbl_pasto_id_lote = '{$id_lote}',
    tbl_pasto_ano_lote = '{$ano_lote}',
    tbl_pasto_descricao_lote = '" . $esc($descricao_lote) . "',
    tbl_pasto_descricao_lote_1 = '" . $esc($array_lotes[0]) . "',
    tbl_pasto_descricao_lote_2 = '" . $esc($array_lotes[1]) . "',
    tbl_pasto_descricao_lote_3 = '" . $esc($array_lotes[2]) . "',
    tbl_pasto_descricao_lote_4 = '" . $esc($array_lotes[3]) . "',
    tbl_pasto_descricao_lote_5 = '" . $esc($array_lotes[4]) . "',
    tbl_pasto_descricao_lote_6 = '" . $esc($array_lotes[5]) . "',
    tbl_pasto_alterado_em = '{$data_sistema}',
    tbl_pasto_alterado_por = '{$nome_usuario}'
    WHERE tbl_pasto_id = {$pasto}";

if (!mysqli_query($con, $sql)) {
    $erro = mysqli_error($con);
    mysqli_rollback($con);
    responder_descricao_lote(["success" => false, "message" => "Ocorreu um erro ao atualizar a Descrição do Lote: " . $erro]);
}

mysqli_commit($con);
mysqli_close($con);

responder_descricao_lote([
    "success" => true,
    "ignorado" => false,
    "message" => "Atualização da Descrição do Lote com sucesso",
    "id_lote" => str_pad($id_lote, 4, "0", STR_PAD_LEFT),
    "ano_lote" => $ano_lote,
]);
