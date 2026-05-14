<?php

require_once __DIR__ . '/../../dao/AnimalDao.php';
require_once __DIR__ . '/../../service/AnimalService.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Endereco.php';

if(isset($_GET["id"]) && isset($_GET["bd"])){
    $animalService = new AnimalService();
    header('Content-type: application/json');
    
    // Passamos '0' ou null no local para indicar ao DAO que é busca GLOBAL
    echo json_encode($animalService->getAnimalByIdLike($_GET["id"], 0, $_GET["bd"]));
}