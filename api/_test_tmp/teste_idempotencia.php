<?php
require_once __DIR__ . '/../dao/PesagemDao.php';
require_once __DIR__ . '/../service/PesagemService.php';
require_once __DIR__ . '/../entitie/Pesagem.php';
require_once __DIR__ . '/../entitie/ItemPesagem.php';
require_once __DIR__ . '/../entitie/Animal.php';
require_once __DIR__ . '/../entitie/Pessoa.php';
require_once __DIR__ . '/../entitie/Raca.php';
require_once __DIR__ . '/../entitie/Pelagem.php';
require_once __DIR__ . '/../entitie/Endereco.php';
require_once __DIR__ . '/../entitie/EpocaPesagem.php';
require_once __DIR__ . '/../entitie/CategoriaIdade.php';
require_once __DIR__ . '/../entitie/Pasto.php';
require_once __DIR__ . '/../entitie/Modulo.php';
require_once __DIR__ . '/../entitie/Capim.php';

$db = 'teste_offline_pesagem';
$uuidPesagem = 'test-uuid-pesagem-0001';
$uuidItem1 = 'test-uuid-item-0001';

function contarPesagensPorUuid($db, $uuid) {
    $mysql = "/c/wamp64/bin/mysql/mysql9.1.0/bin/mysql.exe";
}

echo "=== TESTE 1: criarPesagem (idempotente) ===\n";
$service = new PesagemService();
$json = [
    'local_id' => '57',
    'epoca_id' => '011',
    'lote' => 'Teste Idempotencia',
    'filtro_desc' => 'Teste',
    'usuario' => 'Teste Automatizado',
    'qtd_a_pesar' => 5,
    'criterios_lista' => ['Apartar'],
    'uuid_app' => $uuidPesagem,
];

$id1 = $service->criarPesagem($json, $db);
echo "Primeira chamada -> pesagem_id = $id1\n";

$id2 = $service->criarPesagem($json, $db);
echo "Segunda chamada (mesmo uuid_app) -> pesagem_id = $id2\n";

echo ($id1 === $id2) ? "OK: idempotente (mesmo id)\n" : "FALHA: ids diferentes!\n";

echo "\n=== TESTE 2: salvarItem (idempotente + numero_item) ===\n";
$jsonItem = [
    'pesagem_id' => $id1,
    'local_id' => '57',
    'epoca_id' => '011',
    'lote' => 'Teste Idempotencia',
    'filtro_desc' => 'Teste',
    'usuario' => 'Teste Automatizado',
    'qtd_a_pesar' => 5,
    'criterios_lista' => ['Apartar'],
    'uuid_app' => $uuidPesagem,
    'item' => [
        'id_animal' => '999999',
        'codigo_animal' => 'TESTE-1',
        'peso' => 250,
        'ultimo_peso' => 0,
        'sexo' => 'M',
        'nascimento' => '2024-01-01',
        'raca' => 'Nelore',
        'pelagem' => 'Branca',
        'mae' => 'Não inf.',
        'obs' => '',
        'mens_repetido' => '',
        'id_pesagem_repetido' => 0,
        'criterio_apartacao' => '',
        'uuid_app' => $uuidItem1,
    ],
];

$r1 = $service->salvarItem($jsonItem, $db);
echo "Primeira chamada -> "; var_export($r1); echo "\n";

$r2 = $service->salvarItem($jsonItem, $db);
echo "Segunda chamada (mesmo uuid_app) -> "; var_export($r2); echo "\n";

echo ($r1['numero_item'] === $r2['numero_item']) ? "OK: idempotente (mesmo numero_item)\n" : "FALHA: numero_item diferente!\n";

echo "\n=== TESTE 3: segundo item de verdade (uuid diferente) deve gerar numero_item novo ===\n";
$jsonItem2 = $jsonItem;
$jsonItem2['item']['id_animal'] = '999998';
$jsonItem2['item']['codigo_animal'] = 'TESTE-2';
$jsonItem2['item']['uuid_app'] = 'test-uuid-item-0002';

$r3 = $service->salvarItem($jsonItem2, $db);
echo "Item novo -> "; var_export($r3); echo "\n";
echo ($r3['numero_item'] !== $r1['numero_item']) ? "OK: numero_item diferente do primeiro item\n" : "FALHA: numero_item repetido entre itens diferentes!\n";
