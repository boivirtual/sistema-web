<?php
require_once __DIR__ . "/../../dao/UsuarioDao.php";
require_once __DIR__ . '/../../entitie/Usuario.php';
require_once __DIR__ . "/../../service/UsuarioService.php";
require_once __DIR__ . '/../../assets/phpmailer/class.phpmailer.php';
require_once __DIR__ . '/../../assets/phpmailer/class.smtp.php';


if(isset($_GET["username"])){
    $uService = new UsuarioService();
    header('Content-type: application/json');
    echo json_encode($uService->getUserByCpf($_GET["username"]), JSON_UNESCAPED_UNICODE);
}