<?php

require_once __DIR__ . '/../../dao/AnimalDao.php';
require_once __DIR__ . '/../../service/AnimalService.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Endereco.php';

if(isset($_GET["id"]) && isset($_GET["local"]) && isset($_GET["bd"])){
    $animalService = new AnimalService();
    header('Content-type: application/json');
    $animal = $animalService->getAnimalById($_GET["id"], $_GET["local"], $_GET["bd"]);
    echo json_encode($animalService->getAnimalInfo($animal, $_GET["bd"]));
}