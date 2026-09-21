<?php

require_once __DIR__ . "/../../dao/MunicipioDao.php";
require_once __DIR__ . '/../../entitie/Municipio.php';
require_once __DIR__ . "/../../service/MunicipioService.php";

if(isset($_GET["uf"]) && isset($_GET["bd"])){
    $mObj = new MunicipioService();
    if(isset($mObj->getMunicipio($_GET["uf"], $_GET["bd"])["error"]))
        echo json_encode($mObj->getMunicipio($_GET["uf"], $_GET["bd"]));
    else
        echo json_encode($mObj->json_encode_privates($mObj->getMunicipio($_GET["uf"], $_GET["bd"])));
}