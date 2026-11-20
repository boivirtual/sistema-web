<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — funcionários (classe 05) ativos da
 * fazenda, para a "Equipe do Dia" da Seção 9 (Fechamento). Regras em
 * ResumoDiarioService::funcionariosDaFazenda().
 *
 * Entrada — JSON no corpo do POST:
 *   bd      -> nome do banco da conta (obrigatório)
 *   fazenda -> código da fazenda (tbl_pessoa_id) (obrigatório)
 *
 * Saída: { "success": true,
 *          "funcionarios": [ { "id": 120, "nome": "Geraldo" } ] }
 */

require_once __DIR__ . "/../../dao/ResumoDiarioDao.php";
require_once __DIR__ . "/../../service/ResumoDiarioService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

$bd      = trim((string) ($dados['bd'] ?? ''));
$fazenda = (int) ($dados['fazenda'] ?? 0);

if ($bd === '' || !preg_match('/^\w+$/', $bd) || $fazenda <= 0) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new ResumoDiarioService();
echo json_encode($service->funcionariosDaFazenda($bd, $fazenda));
