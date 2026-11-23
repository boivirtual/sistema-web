<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — botão "Finalizar" da Seção 9:
 * grava o Resumo do dia (tbl_resumo_diario_tipo = 'R') com os dados das 9
 * seções. Regras em ResumoDiarioService::gravarResumo().
 *
 * Entrada — JSON no corpo do POST:
 *   bd      -> nome do banco da conta (obrigatório)
 *   fazenda -> código da fazenda (tbl_pessoa_id) (obrigatório)
 *   usuario -> nome do usuário logado (obrigatório)
 *   data    -> dia do resumo, AAAA-MM-DD (obrigatório)
 *   resumo  -> objeto com os dados das seções (ver
 *              ResumoDiarioService::camposDoResumo) (obrigatório)
 *
 * Saída: { "success": true|false, "message": "...", "ja_registrado": bool }
 */

require_once __DIR__ . "/../../entitie/ResumoDiario.php";
require_once __DIR__ . "/../../dao/ResumoDiarioDao.php";
require_once __DIR__ . "/../../service/ResumoDiarioService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$bd      = trim((string) ($dados['bd'] ?? ''));
$fazenda = (int) ($dados['fazenda'] ?? 0);
$usuario = trim((string) ($dados['usuario'] ?? ''));
$dia     = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));
$resumo  = $dados['resumo'] ?? null;

if ($bd === '' || !preg_match('/^\w+$/', $bd) || $fazenda <= 0 || $usuario === ''
    || !$dia || !is_array($resumo)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new ResumoDiarioService();
echo json_encode($service->gravarResumo($bd, $fazenda, $usuario, $dia, $resumo));
