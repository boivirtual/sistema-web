<?php
/**
 * Mapa de Gado (tela do pasto) — botão NUTRIÇÃO, chamado pelo aplicativo
 * (inclusive reenvio de ações feitas offline). Regras em
 * MapaGadoNutricaoService — as mesmas de gravar_nutricao.php e
 * ler_itens_nutricao.php do web.
 *
 * Entrada — JSON no corpo do POST, com "acao":
 *
 *   "incluir" (Confirmar Inclusão):
 *     bd, fazenda, pasto, data (Y-m-d), produto, quantidade, cocho
 *     (situação do cocho), usuario, data_hora (Y-m-d H:i:s)
 *     -> { "success", "message", "ignorado", "id" }
 *
 *   "excluir" (lixeira da tabela):
 *     bd, id (tbl_nutricao_id), usuario, data_hora
 *     -> { "success", "message", "ignorado", "aviso"? }
 *
 *   "itens" (tabela do modal de uma data fora do cache do aplicativo):
 *     bd, fazenda, pasto, data (Y-m-d)
 *     -> { "success", "nutricoes": [ {id, data, local, pasto, produto,
 *          produto_descricao, unidade, quantidade, qtd_animais,
 *          media_cabeca} ] }
 */

require_once __DIR__ . "/../../dao/PastoDao.php";
require_once __DIR__ . "/../../dao/AnimalPastoDao.php";
require_once __DIR__ . "/../../dao/NutricaoDao.php";
require_once __DIR__ . "/../../service/MapaGadoNutricaoService.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$service = new MapaGadoNutricaoService();
switch ((string) ($dados['acao'] ?? '')) {
    case 'incluir':
        echo json_encode($service->incluir($dados));
        break;
    case 'excluir':
        echo json_encode($service->excluir($dados));
        break;
    case 'itens':
        echo json_encode($service->itens($dados));
        break;
    default:
        echo json_encode(["success" => false, "message" => "Ação não informada."]);
}
