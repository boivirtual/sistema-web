<?php

require_once __DIR__ . "/../../dao/ChuvaDao.php";
require_once __DIR__ . "/../../service/ChuvaService.php";
require_once __DIR__ . '/../../entitie/Chuva.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';

if(isset($_GET['local']) && isset($_GET['data']) && isset($_GET["bd"]) && isset($_GET["volume"])){
    $obj = new ChuvaService();
    echo json_encode($obj->getVolume($_GET["bd"], $_GET["data"], $_GET["local"], $_GET["volume"]), JSON_UNESCAPED_UNICODE);
}