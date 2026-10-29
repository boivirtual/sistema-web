<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — confere se o dia anterior à data
 * escolhida na tela inicial ficou sem resumo do usuário.
 *
 * Regra: o dia anterior está pendente quando não tem registro do usuário
 * na tbl_resumo_diario E o usuário já tem algum resumo mais antigo que ele
 * (sem nenhum resumo anterior — ex.: tabela vazia — não cobra nada).
 *
 * Entrada — JSON no corpo do POST:
 *   bd      -> nome do banco da conta (obrigatório)
 *   usuario -> nome do usuário logado (obrigatório)
 *   data    -> data escolhida, AAAA-MM-DD (obrigatório)
 *
 * Saída: { "success": true, "pendente": true|false, "dia": "AAAA-MM-DD" }
 *   (dia = o dia anterior conferido)
 *
 * Somente leitura; prepared statements e campos explicitados (CLAUDE.md).
 */

require_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

$bd      = trim((string) ($dados['bd'] ?? ''));
$usuario = trim((string) ($dados['usuario'] ?? ''));
$data    = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));

if ($bd === '' || !preg_match('/^\w+$/', $bd) || $usuario === '' || !$data) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$con = @mysqli_connect($servidor, $usuario_bd, $senha_bd, $bd);
if (!$con) {
    echo json_encode(["success" => false, "message" => "Não foi possível conectar ao banco."]);
    exit;
}
mysqli_set_charset($con, "utf8");

// tbl_resumo_diario_data = dia a que o resumo se refere (sem hora); o
// momento da gravação fica em tbl_resumo_diario_incluido_em.
$anterior = (clone $data)->modify('-1 day')->format('Y-m-d');

// Resumo do usuário no dia anterior
$stmt = mysqli_prepare(
    $con,
    "SELECT COUNT(tbl_resumo_diario_id) FROM tbl_resumo_diario
      WHERE tbl_resumo_diario_usuario = ? AND tbl_resumo_diario_data = ?"
);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Erro ao consultar os resumos."]);
    exit;
}
mysqli_stmt_bind_param($stmt, 'ss', $usuario, $anterior);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $noDia);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$pendente = false;
if ((int) $noDia === 0) {
    // Algum resumo do usuário antes do dia anterior?
    $stmt = mysqli_prepare(
        $con,
        "SELECT COUNT(tbl_resumo_diario_id) FROM tbl_resumo_diario
          WHERE tbl_resumo_diario_usuario = ? AND tbl_resumo_diario_data_hora < ?"
    );
    mysqli_stmt_bind_param($stmt, 'ss', $usuario, $inicio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $antes);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    $pendente = (int) $antes > 0;
}

echo json_encode([
    "success"  => true,
    "pendente" => $pendente,
    "dia"      => $anterior,
]);
