<?php
/**
 * Mapa de Gado (tela do pasto) — "Levar a Descrição do Lote" do pasto
 * origem para o pasto destino depois de transferir parte dos animais,
 * chamado pelo aplicativo (inclusive reenvio de ações feitas offline).
 * Regras em MapaGadoService::levarDescricaoLote().
 *
 * Entrada — JSON no corpo do POST:
 *   bd, origem, destino (tbl_pasto_id), usuario, data_hora (Y-m-d H:i:s)
 *
 * Saída: { "success": true|false, "message": "...", "ignorado": bool,
 *          "id_lote_origem": "0032", "ano_lote_origem": "2026",
 *          "id_lote_destino": "0031", "ano_lote_destino": "2026" }
 */

require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/LoteAnimaisDao.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new MapaGadoService();
echo json_encode($service->levarDescricaoLote($dados));
