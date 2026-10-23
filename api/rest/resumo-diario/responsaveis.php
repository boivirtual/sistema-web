<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — opções do select "Responsável"
 * do Tratamento: usuários ativos da empresa que têm acesso à fazenda
 * (local_usuario em acesso_boi_virtual.usuario).
 *
 * Entrada — JSON no corpo do POST: bd (CNPJ/CPF da empresa), fazenda (id)
 * Saída: { "success": true|false, "responsaveis": [ {id, nome} ] }
 */

require_once __DIR__ . "/../../dao/UsuarioDao.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$dados = json_decode(file_get_contents('php://input'), true);
$bd = is_array($dados) ? trim((string) ($dados['bd'] ?? '')) : '';
$fazenda = is_array($dados) ? (int) ($dados['fazenda'] ?? 0) : 0;
if ($bd === '' || $fazenda <= 0) {
    echo json_encode(["success" => false, "message" => "Requisição inválida."]);
    exit;
}

$dao = new UsuarioDao();
echo json_encode([
    "success" => true,
    "responsaveis" => $dao->responsaveisDaFazenda($bd, $fazenda),
]);
