<?php
require_once __DIR__ . '/../../dao/UsuarioDao.php';
require_once __DIR__ . '/../../entitie/Usuario.php';
require_once __DIR__ . '/../../service/UsuarioService.php';

if(isset($_POST["username"]) && isset($_POST["pass"])){
    $uObj = new UsuarioService();
    header('Content-type: application/json');
    echo json_encode($uObj->getUser($_POST["username"], $_POST["pass"]));
}