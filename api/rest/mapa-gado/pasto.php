<?php

require_once __DIR__ . '/../../dao/AnimalPastoDao.php';
require_once __DIR__ . '/../../dao/EmpresaDao.php';
require_once __DIR__ . '/../../dao/PastoDao.php';
require_once __DIR__ . '/../../entitie/AnimalPasto.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . '/../../entitie/CategoriaIdade.php';
require_once __DIR__ . '/../../entitie/Pasto.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Modulo.php';
require_once __DIR__ . '/../../entitie/Capim.php';
require_once __DIR__ . '/../../entitie/Empresa.php';
require_once __DIR__ . "/../../service/AnimalPastoService.php";
require_once __DIR__ . "/../../service/EmpresaService.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";
require_once __DIR__ . "/../../service/PastoService.php";

if(isset($_GET["local"]) && isset($_GET["bd"]) && isset($_GET["page"])){
    $obj = new MapaGadoService();
    header('Content-type: application/json');
    echo json_encode($obj->getMapaByLocal($_GET["local"], $_GET["page"], $_GET["bd"]));
}