<?php
require_once __DIR__ . '/../../dao/PesagemDao.php';
require_once __DIR__ . '/../../service/PesagemService.php';
require_once __DIR__ . '/../../entitie/Pesagem.php';
require_once __DIR__ . '/../../entitie/ItemPesagem.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . '/../../entitie/EpocaPesagem.php';
require_once __DIR__ . '/../../entitie/CategoriaIdade.php';
require_once __DIR__ . '/../../entitie/Pasto.php';
require_once __DIR__ . '/../../entitie/Modulo.php';
require_once __DIR__ . '/../../entitie/Capim.php';

header('Content-type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    echo json_encode([
        "success" => false,
        "message" => "JSON inválido ou vazio."
    ]);
    exit;
}

$service = new PesagemService();
$idGerado = $service->salvarItem($dados, $dados['bd']);

if ($idGerado) {
    echo json_encode([
        "success" => true,
        "pesagem_id" => $idGerado
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao gravar item da pesagem."
    ]);
}