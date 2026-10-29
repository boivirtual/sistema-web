<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — confere se o dia anterior à data
 * escolhida na tela inicial ficou sem resumo da fazenda (um resumo por
 * data e fazenda, não importa o usuário que fez). Regras em
 * ResumoDiarioService::diaAnteriorPendente().
 *
 * Entrada — JSON no corpo do POST:
 *   bd      -> nome do banco da conta (obrigatório)
 *   fazenda -> código da fazenda (tbl_pessoa_id) (obrigatório)
 *   data    -> data escolhida, AAAA-MM-DD (obrigatório)
 *
 * Saída: { "success": true, "pendente": true|false, "dia": "AAAA-MM-DD" }
 *   (dia = o dia anterior conferido)
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
$data    = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));

if ($bd === '' || !preg_match('/^\w+$/', $bd) || $fazenda <= 0 || !$data) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new ResumoDiarioService();
echo json_encode($service->diaAnteriorPendente($bd, $fazenda, $data));
