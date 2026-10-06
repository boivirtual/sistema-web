<?php
/**
 * Mapa de Gado (tela do pasto) — transferir animais de UMA categoria para
 * outro pasto (botão Confirma), chamado pelo aplicativo (inclusive reenvio
 * de ações feitas offline). Regras em
 * MapaGadoService::transferirCategoria().
 *
 * Entrada — JSON no corpo do POST:
 *   bd, origem, destino (tbl_pasto_id), categoria (código da faixa de
 *   idade), sexo ("M", "F" ou "" = bezerros, os dois sexos), quantidade,
 *   usuario, data_hora (Y-m-d H:i:s)
 *
 * Saída: { "success": true|false, "message": "...", "ignorado": bool,
 *          "descricao_lote_pasto_destino": "...",
 *          "descricao_lote_pasto_origem": "..." }
 */

require_once __DIR__ . "/../../entitie/CategoriaIdade.php";
require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/AnimalPastoDao.php";
require_once __DIR__ . "/../../dao/NutricaoDao.php";
require_once __DIR__ . "/../../dao/CategoriaIdadeDao.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new MapaGadoService();
echo json_encode($service->transferirCategoria($dados));
