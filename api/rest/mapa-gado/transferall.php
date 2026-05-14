<?php

require_once __DIR__ . '/../../dao/AnimalPastoDao.php';
require_once __DIR__ . '/../../dao/NutricaoDao.php';
require_once __DIR__ . '/../../dao/PastoDao.php';
require_once __DIR__ . '/../../dao/UsuarioDao.php';
require_once __DIR__ . '/../../entitie/AnimalPasto.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . '/../../entitie/Pasto.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Modulo.php';
require_once __DIR__ . '/../../entitie/Usuario.php';
require_once __DIR__ . '/../../entitie/Capim.php';
require_once __DIR__ . "/../../service/AnimalPastoService.php";
require_once __DIR__ . "/../../service/MapaGadoService.php";
require_once __DIR__ . "/../../service/UsuarioService.php";
require_once __DIR__ . "/../../service/NutricaoService.php";
require_once __DIR__ . "/../../service/PastoService.php";

if(isset($_POST["incluirId"]) && isset($_POST["removerId"]) && isset($_POST["userId"]) && isset($_POST["fazenda"]) && isset($_POST["bd"])){
    $obj = new MapaGadoService();
    header('Content-type: application/json');
    echo json_encode($obj->transferAll($_POST["incluirId"], $_POST["removerId"], $_POST["userId"], $_POST["fazenda"], $_POST["bd"]));
}