<?php

require_once __DIR__ . "/../../dao/ChuvaDao.php";
require_once __DIR__ . '/../../entitie/Chuva.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . "/../../service/ChuvaService.php";

if(isset($_GET["local"]) && isset($_GET["bd"])){
    $obj = new ChuvaService();
    echo json_encode($obj->getDias($_GET["bd"], $_GET["local"]), JSON_UNESCAPED_UNICODE);
}