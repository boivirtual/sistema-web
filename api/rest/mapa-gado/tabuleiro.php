<?php
/**
 * Mapa de Gado (Tabuleiro) — exportação para o cache offline do aplicativo.
 * Regras em MapaGadoService::tabuleiro().
 *
 * Entrada — JSON no corpo do POST:
 *   bd       -> nome do banco da conta                    (obrigatório)
 *   fazendas -> lista de tbl_pessoa_id das fazendas        (obrigatório)
 *
 * Saída:
 *   {
 *     "success": true,
 *     "categorias": [ {"id":1,"de":0,"ate":7}, ... ],
 *     "descricoes_lote": [ {"id":1,"descricao":"VACAS"}, ... ],
 *     "pastos": [
 *       {"id":46,"local":56,"descricao":"ENTRADA","modulo":999,"capim":"",
 *        "categorias":"001!002!003!004!005","ordem":1,
 *        "descricao_lote":"VACAS ","lotes":["VACAS ","","","","",""]}, ...
 *     ],
 *     "animais": [ {"local":56,"item":12,"pasto":46,"sexo":"F",
 *                   "nascimento":"2024-03-10"}, ... ]
 *   }
 */

require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/AnimalPastoDao.php";
require_once __DIR__ . "/../../dao/AnimalDao.php";
require_once __DIR__ . "/../../dao/CategoriaIdadeDao.php";
require_once __DIR__ . "/../../dao/LoteAnimaisDao.php";
require_once __DIR__ . "/../../dao/MorteDao.php";
require_once __DIR__ . "/../../entitie/CategoriaIdade.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Informe bd e a lista de fazendas."]);
    exit;
}

$service = new MapaGadoService();
echo json_encode($service->tabuleiro($dados['bd'] ?? '', $dados['fazendas'] ?? []));
