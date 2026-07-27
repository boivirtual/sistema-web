<?php
require_once __DIR__ . '/../dao/AnimalDao.php';
require_once __DIR__ . '/../service/AnimalService.php';

$service = new AnimalService();
$lista = $service->getAnimaisFazendaCompleto('57', 'teste_offline_pesagem');

echo "Total de animais retornados: " . count($lista) . "\n";
echo "Primeiros 3:\n";
foreach (array_slice($lista, 0, 3) as $a) {
    echo json_encode($a, JSON_UNESCAPED_UNICODE) . "\n";
}
