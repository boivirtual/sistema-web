<?php
/**
 * Mapa de Gado (Tabuleiro) — mover TODOS os animais de um pasto para outro,
 * chamado pelo aplicativo (inclusive reenvio de ações feitas offline).
 * Regras em MapaGadoService::transferirTudo().
 *
 * Entrada — JSON no corpo do POST:
 *   bd, origem, destino (tbl_pasto_id), usuario, data_hora (Y-m-d H:i:s)
 *
 * Saída: { "success": true|false, "message": "...", "ignorado": bool,
 *          "descricao_lote_pasto_destino": "..." }
 */

require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/AnimalPastoDao.php";
require_once __DIR__ . "/../../dao/NutricaoDao.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new MapaGadoService();
echo json_encode($service->transferirTudo($dados));
