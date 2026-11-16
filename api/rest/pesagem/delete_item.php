<?php
require_once __DIR__ . '/../../dao/PesagemDao.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['pesagem_id']) && isset($dados['numero_item'])) {
    $dao = new PesagemDao($dados['bd']);
    // uuid_app e id_animal (app novo): localizam o item certo mesmo se a
    // web tiver renumerado os itens da pesagem
    $sucesso = $dao->excluirItem(
        $dados['pesagem_id'],
        $dados['numero_item'],
        $dados['uuid_app'] ?? '',
        $dados['id_animal'] ?? 0
    );

    header('Content-type: application/json');
    $resposta = ["success" => $sucesso];
    if (!$sucesso && $dao->mensagem !== '') {
        $resposta["message"] = $dao->mensagem;
    }
    echo json_encode($resposta);
}
