<?php

require_once __DIR__ . "/../../dao/ChuvaDao.php";
require_once __DIR__ . '/../../entitie/Chuva.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . "/../../service/ChuvaService.php";

if(isset($_POST["bd"]) && isset($_POST["data_chuva"]) && isset($_POST["codigo_local_chuva"]) && isset($_POST["volume_chuva"])){
    parse_str(file_get_contents("php://input"), $chuva);
    $obj = new ChuvaService();
    $r = $obj->createChuva($chuva);
    if(!$r["error"]){
        http_response_code(201);
    }
    echo json_encode($r);
}