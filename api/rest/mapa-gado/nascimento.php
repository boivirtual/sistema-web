<?php
/**
 * Mapa de Gado (tela do pasto) — botão NASCIMENTO, chamado pelo aplicativo
 * (só online). Regras em MapaGadoNascimentoService — as mesmas de
 * form_mapa_gados_movimentacao.php do web.
 *
 * Entrada — JSON no corpo do POST, com "acao":
 *
 *   "listas" (opções dos selects da tela):
 *     bd
 *     -> { "success",
 *          "semens":   [ {id, nome, raca} ],
 *          "machos":   [ {id, alfa, numerico, raca} ],
 *          "racas":    [ {id, descricao} ],
 *          "pelagens": [ {id, descricao} ] }
 */

require_once __DIR__ . "/../../dao/NascimentoDao.php";
require_once __DIR__ . "/../../service/MapaGadoNascimentoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new MapaGadoNascimentoService();
switch ((string) ($dados['acao'] ?? '')) {
    case 'listas':
        echo json_encode($service->listas($dados));
        break;
    case 'parametros':
        echo json_encode($service->parametros($dados));
        break;
    case 'mae':
        echo json_encode($service->mae($dados));
        break;
    case 'ultimo_nascimento':
        echo json_encode($service->ultimoNascimento($dados));
        break;
    default:
        echo json_encode(["success" => false, "message" => "Ação não informada."]);
}
