<?php

require_once __DIR__ . '/../../dao/AnimalPastoDao.php';
require_once __DIR__ . '/../../dao/EmpresaDao.php';
require_once __DIR__ . '/../../dao/AnimalDao.php';
require_once __DIR__ . '/../../dao/CategoriaIdadeDao.php';
require_once __DIR__ . '/../../dao/MovimentacaoDao.php';
require_once __DIR__ . '/../../dao/MovimentacaoEstoqueDao.php';
require_once __DIR__ . '/../../dao/ItemMovimentacaoDao.php';
require_once __DIR__ . '/../../dao/MotivoMorteDao.php';
require_once __DIR__ . '/../../dao/UsuarioDao.php';
require_once __DIR__ . '/../../service/AnimalPastoService.php';
require_once __DIR__ . '/../../service/CategoriaIdadeService.php';
require_once __DIR__ . '/../../service/EmpresaService.php';
require_once __DIR__ . '/../../service/AnimalService.php';
require_once __DIR__ . '/../../service/MovimentacaoService.php';
require_once __DIR__ . '/../../service/MovimentacaoEstoqueService.php';
require_once __DIR__ . '/../../service/ItemMovimentacaoService.php';
require_once __DIR__ . '/../../service/UsuarioService.php';
require_once __DIR__ . '/../../service/MotivoMorteService.php';
require_once __DIR__ . '/../../entitie/AnimalPasto.php';
require_once __DIR__ . '/../../entitie/Empresa.php';
require_once __DIR__ . '/../../entitie/Pasto.php';
require_once __DIR__ . '/../../entitie/Modulo.php';
require_once __DIR__ . '/../../entitie/Capim.php';
require_once __DIR__ . '/../../entitie/Endereco.php';
require_once __DIR__ . '/../../entitie/Animal.php';
require_once __DIR__ . '/../../entitie/Raca.php';
require_once __DIR__ . '/../../entitie/Pessoa.php';
require_once __DIR__ . '/../../entitie/Pelagem.php';
require_once __DIR__ . '/../../entitie/CategoriaIdade.php';
require_once __DIR__ . '/../../entitie/Usuario.php';
require_once __DIR__ . '/../../entitie/MotivoMorte.php';

if(isset($_POST["animal"]) && isset($_POST["motivo"]) && isset($_POST["data"]) && isset($_POST["local"]) && isset($_POST["pasto"]) && isset($_POST["obs"]) && isset($_POST["bd"])){
    $animalPastoService = new AnimalPastoService();
    header('Content-type: application/json');
    echo json_encode($animalPastoService->gravarMorte($_POST["animal"], $_POST["motivo"], $_POST["local"], $_POST["pasto"], $_POST["data"], $_POST["obs"],  $_POST["userId"], $_POST["bd"]));
}