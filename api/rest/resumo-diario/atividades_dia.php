<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — atividades já registradas no
 * sistema para a fazenda no dia (Seção 1): chuva, mudança de pasto, nutrição,
 * nascimentos, abortos/absorções/natimortos e mortes. Regras em
 * ResumoDiarioService::atividadesDoDia().
 *
 * Entrada — JSON no corpo do POST:
 *   bd      -> nome do banco da conta (obrigatório)
 *   fazenda -> código da fazenda (tbl_pessoa_id) (obrigatório)
 *   data    -> dia do resumo, AAAA-MM-DD (obrigatório)
 *
 * Saída: { "success": true, "chuva_mm": 30.5|null, "mudou_pasto": bool,
 *          "pastos_alterados": [ "MANGA 07 A", "SAIDA" ],
 *          "nutricao": bool, "nascimentos": 2, "abortos": 0, "mortes": 1,
 *          "pesados": 59, "nutricoes": [ { "pasto": "FUNDACA 01",
 *          "produto": "SAL MINERAL", "quantidade": 10, "unidade": "KG" } ] }
 */

require_once __DIR__ . "/../../dao/ResumoDiarioDao.php";
require_once __DIR__ . "/../../dao/NascimentoDao.php";
require_once __DIR__ . "/../../dao/MorteDao.php";
require_once __DIR__ . "/../../service/ResumoDiarioService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

$bd      = trim((string) ($dados['bd'] ?? ''));
$fazenda = (int) ($dados['fazenda'] ?? 0);
$dia     = DateTime::createFromFormat('!Y-m-d', (string) ($dados['data'] ?? ''));

if ($bd === '' || !preg_match('/^\w+$/', $bd) || $fazenda <= 0 || !$dia) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new ResumoDiarioService();
echo json_encode($service->atividadesDoDia($bd, $fazenda, $dia));
