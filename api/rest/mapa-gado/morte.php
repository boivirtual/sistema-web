<?php
/**
 * Mapa de Gado (tela do pasto) — registrar a MORTE de um animal (botão
 * Morte), chamado pelo aplicativo (inclusive reenvio de ações feitas
 * offline). Só o controle de estoque por animal. Regras em
 * MapaGadoService::gravarMorte() — as mesmas de gravar_morte.php do web.
 *
 * Entrada — JSON no corpo do POST:
 *   bd, fazenda (id da fazenda), pasto (tbl_pasto_id),
 *   animal (tbl_animal_codigo_id), motivo (código da causa da morte),
 *   data_morte (Y-m-d), observacao, usuario, data_hora (Y-m-d H:i:s)
 *
 * Saída: { "success": true|false, "message": "...", "ignorado": bool,
 *          "movimentacao": "000000875" }
 */

require_once __DIR__ . "/../../entitie/CategoriaIdade.php";
require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/CategoriaIdadeDao.php";
require_once __DIR__ . "/../../dao/MorteDao.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new MapaGadoService();
echo json_encode($service->gravarMorte($dados));
