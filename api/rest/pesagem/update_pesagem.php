<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../service/PesagemService.php';
require_once __DIR__ . '/../../dao/PesagemDao.php';
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

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "message" => "JSON inválido."
    ]);
    exit;
}

try {
    $service = new PesagemService();
    $resultado = $service->updatePesagemCabecalho($input);
    echo json_encode($resultado);
} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}