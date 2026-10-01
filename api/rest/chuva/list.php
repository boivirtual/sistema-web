<?php
/**
 * Exportação em massa do registro de chuva das fazendas do usuário, para
 * popular o cache local do aplicativo (tela de Chuva funcionando offline).
 * Regras em ChuvaService::listarParaApp() (só os últimos 5 anos).
 *
 * Entrada — JSON no corpo do POST:
 *   bd       -> nome do banco da conta                    (obrigatório)
 *   fazendas -> lista de tbl_pessoa_id das fazendas        (obrigatório)
 *
 * Saída:
 *   {
 *     "success": true,
 *     "chuvas": [ {"id":1,"local":57,"data":"2026-01-15","volume":12.5}, ... ]
 *   }
 */

require_once __DIR__ . "/../../dao/ChuvaDao.php";
require_once __DIR__ . "/../../service/ChuvaService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados) || !isset($dados['bd']) || !isset($dados['fazendas']) || !is_array($dados['fazendas'])) {
    echo json_encode([
        "success" => false,
        "message" => "Informe bd e a lista de fazendas."
    ]);
    exit;
}

$service = new ChuvaService();
echo json_encode($service->listarParaApp($dados['bd'], $dados['fazendas']));
