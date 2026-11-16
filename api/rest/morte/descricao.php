<?php

require_once __DIR__ . '/../../dao/PessoaDao.php';
require_once __DIR__ . '/../../dao/PastoDao.php';
require_once __DIR__ . '/../../dao/EmpresaDao.php';
require_once __DIR__ . '/../../service/MorteService.php';
require_once __DIR__ . '/../../service/PessoaService.php';
require_once __DIR__ . '/../../service/PastoService.php';
require_once __DIR__ . '/../../service/EmpresaService.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Pasto.php';
require_once __DIR__ . '/../../entitie/Modulo.php';
require_once __DIR__ . '/../../entitie/Capim.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . '/../../entitie/Empresa.php';

if(isset($_GET["pasto"]) && isset($_GET["local"]) && isset($_GET["bd"])){
    $morteService = new MorteService();
    header('Content-type: application/json');
    echo json_encode($morteService->getDescricao($_GET["pasto"], $_GET["local"], $_GET["bd"]));
}