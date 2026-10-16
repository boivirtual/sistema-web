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
 *
 *   "parametros", "mae", "ultimo_nascimento", "registros_hoje" (bd,
 *   fazenda): ver MapaGadoNascimentoService.
 *
 *   "gravar_nascimento" / "gravar_aborto_natimorto" (Confirmar):
 *     bd, usuario, campos {os campos do formulário do web}
 *     -> a resposta do próprio script do web: { "error", "message" } ou
 *        { "success", "message", "id_pasto", "descricao_pasto",
 *          "descricao_lote" }
 */

require_once __DIR__ . "/../../dao/NascimentoDao.php";
require_once __DIR__ . "/../../dao/MorteDao.php";
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
    case 'gravar_nascimento':
        $service->gravarPeloWeb($dados, 'nascimento');
        break;
    case 'gravar_aborto_natimorto':
        $service->gravarPeloWeb($dados, 'aborto_natimorto');
        break;
    case 'registros_hoje':
        echo json_encode($service->registrosHoje($dados));
        break;
    case 'ultimo_nascimento':
        echo json_encode($service->ultimoNascimento($dados));
        break;
    default:
        echo json_encode(["success" => false, "message" => "Ação não informada."]);
}
