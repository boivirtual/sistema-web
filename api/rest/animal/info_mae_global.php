<?php
require_once __DIR__ . '/../../dao/AnimalDao.php';
require_once __DIR__ . '/../../service/AnimalService.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Endereco.php';

header('Content-type: application/json');

if(isset($_GET["id"]) && isset($_GET["bd"])){
    $animalService = new AnimalService();
    
    // Chamamos o novo método que traz tudo: mãe + filhos
    $resultado = $animalService->getInfoMaeCompleta($_GET["id"], $_GET["bd"]);
    
    if($resultado){
        echo json_encode($resultado);
    } else {
        echo json_encode(["success" => false, "message" => "Matriz não localizada"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Parâmetros insuficientes"]);
}