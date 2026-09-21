<?php

require_once __DIR__ . '/../../dao/MotivoMorteDao.php';
require_once __DIR__ . '/../../service/MorteService.php';
require_once __DIR__ . '/../../entitie/MotivoMorte.php';

if(isset($_GET["bd"])){
    $animalService = new MorteService();
    header('Content-type: application/json');
    echo json_encode($animalService->getMotivoMorte($_GET["bd"]));
}