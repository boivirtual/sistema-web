<?php
/**
 * Mapa de Gado (Mapa Satélite) — mapa dos pastos (GeoJSON) das fazendas,
 * cor dos módulos e coordenada de cada fazenda, para o cache offline do
 * aplicativo. Regras em MapaGadoService::satelite().
 *
 * Entrada — JSON no corpo do POST:
 *   bd       -> nome do banco da conta                    (obrigatório)
 *   fazendas -> lista de tbl_pessoa_id das fazendas        (obrigatório)
 *   versoes  -> {"56":"<md5>", ...} versão que o app já tem (opcional)
 *
 * Saída:
 *   {
 *     "success": true,
 *     "modulos": [ {"id":1,"cor":"#E53935"}, ... ],
 *     "mapas": [ {"local":56,"versao":"<md5>","geojson":{...}|null,
 *                 "latitude":-19.96,"longitude":-42.59}, ... ]
 *   }
 *   geojson null + versao igual à enviada = o mapa do app ainda vale;
 *   versao "" = fazenda sem mapa desenhado.
 */

require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/MapaFazendaDao.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Informe bd e a lista de fazendas."]);
    exit;
}

$service = new MapaGadoService();
echo json_encode(
    $service->satelite($dados['bd'] ?? '', $dados['fazendas'] ?? [], $dados['versoes'] ?? []),
    JSON_UNESCAPED_UNICODE
);
