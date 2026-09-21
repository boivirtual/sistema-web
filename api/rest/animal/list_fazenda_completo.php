<?php

require_once __DIR__ . '/../../dao/AnimalDao.php';
require_once __DIR__ . '/../../service/AnimalService.php';

// Exportação em massa dos animais ativos de uma fazenda, para popular o cache
// local do app (autocomplete + ficha do animal funcionando offline).
if (isset($_GET["local"]) && isset($_GET["bd"])) {
    $animalService = new AnimalService();
    header('Content-type: application/json');
    echo json_encode([
        "success" => true,
        "animais" => $animalService->getAnimaisFazendaCompleto($_GET["local"], $_GET["bd"])
    ]);
} else {
    header('Content-type: application/json');
    echo json_encode([
        "success" => false,
        "message" => "Parâmetros insuficientes: informe local e bd."
    ]);
}
