<?php

require_once __DIR__ . '/../dao/AnimalDao.php';
require_once __DIR__ . '/../service/AnimalService.php';
require_once __DIR__ . '/../entitie/Animal.php';
require_once __DIR__ . '/../entitie/Pessoa.php';
require_once __DIR__ . '/../entitie/Raca.php';
require_once __DIR__ . '/../entitie/Pelagem.php';
require_once __DIR__ . '/../entitie/Endereco.php';

$_GET["id"] = "1099";
$_GET["local"] = "57";
$_GET["bd"] = "97174041604";

$animalService = new AnimalService();
$animal = $animalService->getAnimalById($_GET["id"], $_GET["local"], $_GET["bd"]);

echo "animal->getId(): ";
var_dump($animal ? $animal->getId() : null);

$info = $animalService->getAnimalInfo($animal, $_GET["bd"]);
var_dump($info);
