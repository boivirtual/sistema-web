<?php
/**
 * Mapa de Gado (Tabuleiro) — gravar uma NOVA Descrição do Lote num pasto
 * ("Criar nova Descrição do Lote" no pasto destino, depois de mover todos
 * os animais), chamado pelo aplicativo (inclusive reenvio de ações feitas
 * offline). Regras em MapaGadoService::gravarDescricaoLote().
 *
 * Entrada — JSON no corpo do POST:
 *   bd, pasto (tbl_pasto_id), descricao_lote (texto montado),
 *   lotes (lista de até 6 textos), usuario, data_hora (Y-m-d H:i:s)
 *
 * Saída: { "success": true|false, "message": "...", "ignorado": bool,
 *          "id_lote": "0031", "ano_lote": "2026" }
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
echo json_encode($service->gravarDescricaoLote($dados));
