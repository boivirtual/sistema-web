<?php
    require_once __DIR__ . '/../../dao/PesagemDao.php';

    header('Content-Type: application/json');

    $dados = json_decode(file_get_contents('php://input'), true);

    $bd = $dados['bd'] ?? null;
    $pesagemId = $dados['pesagem_id'] ?? null;
    $idAnimal = $dados['id_animal'] ?? null;
    $mensagemRepetido = $dados['mens_repetido'] ?? null;
    $idPesagemRepetido = $dados['id_pesagem_repetido'] ?? 0;

    if (!$bd || !$pesagemId || !$idAnimal) {
        echo json_encode([
            "success" => false,
            "message" => "Dados incompletos. Informe bd, pesagem_id e id_animal."
        ]);
        exit;
    }

    $dao = new PesagemDao($bd);
    $resultado = $dao->adicionarObservacaoItemRepetido(
        $pesagemId,
        $idAnimal,
        $mensagemRepetido,
        $idPesagemRepetido
    );

    echo json_encode($resultado);