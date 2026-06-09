<?php

require_once __DIR__ . '/../../dao/CategoriaIdadeDao.php';
require_once __DIR__ . '/../../dao/AnimalPastoDao.php';
require_once __DIR__ . '/../../service/CategoriaIdadeService.php';
require_once __DIR__ . '/../../service/CategoriaSexoService.php';
require_once __DIR__ . '/../../service/AnimalPastoService.php';
require_once __DIR__ . '/../../entitie/CategoriaIdade.php';
require_once __DIR__ . '/../../entitie/AnimalPasto.php';
require_once __DIR__ . '/../../entitie/Pasto.php';
require_once __DIR__ . '/../../entitie/Modulo.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Capim.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Endereco.php';

if(isset($_GET["local"]) && isset($_GET["pasto"]) && isset($_GET["bd"])){
    $categoriaSexoService = new CategoriaSexoService();
    header('Content-type: application/json');
    echo json_encode($categoriaSexoService->getCategoriaSexo($_GET["pasto"], $_GET["local"], $_GET["bd"]));
}