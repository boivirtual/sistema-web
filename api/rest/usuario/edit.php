<?php
require_once __DIR__ . "/../../dao/UsuarioDao.php";
require_once __DIR__ . '/../../entitie/Usuario.php';
require_once __DIR__ . "/../../service/UsuarioService.php";


if($_SERVER['REQUEST_METHOD'] == 'PATCH'){
    parse_str(file_get_contents("php://input"), $user);
    $uService = new UsuarioService();
    echo json_encode($uService->editUser($user));
}