<?php
require_once __DIR__ . '/../../dao/PesagemDao.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['pesagem_id']) && isset($dados['numero_item'])) {
    $dao = new PesagemDao($dados['bd']);
    $sucesso = $dao->excluirItem($dados['pesagem_id'], $dados['numero_item']);
    
    header('Content-type: application/json');
    echo json_encode(["success" => $sucesso]);
}