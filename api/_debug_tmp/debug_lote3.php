<?php
include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";

$db = "97174041604";

// primeira conexao (simula getAnimalById)
$con1 = mysqli_connect($servidor, $usuario_bd, $senha_bd, $db);
mysqli_set_charset($con1, "utf8");
$r1 = mysqli_query($con1, "SELECT tbl_animal_codigo_id FROM tbl_animais WHERE tbl_animal_codigo_id = 1099");
$row1 = mysqli_fetch_assoc($r1);
echo "con1 animal id: "; var_dump($row1);

// segunda conexao nova (simula getAnimalInfo -> new AnimalDao)
$con2 = mysqli_connect($servidor, $usuario_bd, $senha_bd, $db);
mysqli_set_charset($con2, "utf8");

$sql = "SELECT
            p.tbl_pesagem_id,
            p.tbl_pesagem_lote
        FROM tbl_item_pesagem i
        JOIN tbl_pesagem p ON i.tbl_ite_pesagem_numero_id = p.tbl_pesagem_id
        WHERE i.tbl_ite_pesagem_codigo_id_animal = 000001099
          AND p.tbl_pesagem_finalizada = 'N'
          AND IFNULL(p.tbl_pesagem_lixeira, 0) = 0
          AND p.tbl_pesagem_origem = 'APP'
        ORDER BY p.tbl_pesagem_id DESC
        LIMIT 1";

$r2 = mysqli_query($con2, $sql);
echo "erro con2: " . mysqli_error($con2) . "\n";
echo "num_rows: " . ($r2 ? mysqli_num_rows($r2) : 'query falhou') . "\n";
var_dump($r2 ? mysqli_fetch_assoc($r2) : null);

echo "current_db con2: ";
$rdb = mysqli_query($con2, "SELECT DATABASE() as d");
var_dump(mysqli_fetch_assoc($rdb));
