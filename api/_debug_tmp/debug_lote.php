<?php
require_once __DIR__ . '/../dao/AnimalDao.php';
require_once __DIR__ . '/../entitie/Animal.php';
require_once __DIR__ . '/../entitie/Pessoa.php';
require_once __DIR__ . '/../entitie/Raca.php';
require_once __DIR__ . '/../entitie/Pelagem.php';
require_once __DIR__ . '/../entitie/Endereco.php';

$dao = new AnimalDao('97174041604');
$animal = $dao->getAnimalById('1099', '57');

var_dump($animal ? $animal->getId() : null);

$info = $dao->getLoteAbertoPorAnimal($animal->getId());
var_dump($info);

// also raw query test using getId() value directly
$con = $dao->getConexao();
var_dump(get_class($dao));
