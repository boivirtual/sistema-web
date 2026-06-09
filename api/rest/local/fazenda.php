<?php
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . '/../../service/PessoaService.php';
require_once __DIR__ . '/../../dao/PessoaDao.php';

$json = json_decode(file_get_contents('php://input'));

$pessoaService = new PessoaService();
header('Content-type: application/json');
echo json_encode($pessoaService->getPessoaByIds($json->local, $json->bd));
