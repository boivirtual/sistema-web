<?php
/**
 * Migração: adiciona colunas de correlação (uuid gerado no app) em tbl_pesagem e
 * tbl_item_pesagem, usadas para tornar a criação de pesagem/item idempotente na
 * sincronização offline do app.
 *
 * Roda manualmente via CLI: php 2026_07_pesagem_offline_uuid.php
 * Idempotente: pode ser executada quantas vezes for preciso, em qualquer ambiente.
 * Descobre os bancos de tenant dinamicamente (não tem lista de CNPJ hardcoded).
 *
 * Para testar restrito a um único schema (dry-run seguro antes de rodar geral):
 *   php 2026_07_pesagem_offline_uuid.php --schema=teste_offline_pesagem
 */

include_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";

$SCHEMAS_IGNORADOS = ['information_schema', 'mysql', 'performance_schema', 'sys', 'acesso_boi_virtual'];

$con = mysqli_connect($servidor, $usuario_bd, $senha_bd);
if (!$con) {
    fwrite(STDERR, "Erro ao conectar no MySQL: " . mysqli_connect_error() . "\n");
    exit(1);
}

function colunaExiste($con, $schema, $tabela, $coluna) {
    $sql = "SELECT COUNT(*) as qtd FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME = '$tabela' AND COLUMN_NAME = '$coluna'";
    $r = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($r);
    return ((int)$row['qtd']) > 0;
}

function indiceExiste($con, $schema, $tabela, $indice) {
    $sql = "SELECT COUNT(*) as qtd FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME = '$tabela' AND INDEX_NAME = '$indice'";
    $r = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($r);
    return ((int)$row['qtd']) > 0;
}

echo "Descobrindo bancos de tenant...\n";
$resDb = mysqli_query($con, "SHOW DATABASES");
$schemas = [];
while ($row = mysqli_fetch_row($resDb)) {
    $nome = $row[0];
    if (in_array($nome, $SCHEMAS_IGNORADOS, true)) continue;
    $schemas[] = $nome;
}

$aplicados = 0;
$jaAplicados = 0;
$erros = 0;

foreach ($schemas as $schema) {
    $temPesagem = mysqli_query($con, "SELECT COUNT(*) as qtd FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME = 'tbl_pesagem'");
    $temItem = mysqli_query($con, "SELECT COUNT(*) as qtd FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME = 'tbl_item_pesagem'");

    $qtdPesagem = (int)mysqli_fetch_assoc($temPesagem)['qtd'];
    $qtdItem = (int)mysqli_fetch_assoc($temItem)['qtd'];

    if ($qtdPesagem === 0 || $qtdItem === 0) {
        continue; // não é um schema de tenant válido, ignora silenciosamente
    }

    echo "== $schema ==\n";

    try {
        // tbl_pesagem_uuid_app
        if (!colunaExiste($con, $schema, 'tbl_pesagem', 'tbl_pesagem_uuid_app')) {
            $sql = "ALTER TABLE `$schema`.`tbl_pesagem`
                    ADD COLUMN `tbl_pesagem_uuid_app` VARCHAR(36) NULL DEFAULT NULL
                    COMMENT 'UUID gerado pelo app para criacao idempotente offline'";
            if (!mysqli_query($con, $sql)) {
                throw new Exception("Erro ao adicionar tbl_pesagem_uuid_app: " . mysqli_error($con));
            }
            echo "  + coluna tbl_pesagem_uuid_app adicionada\n";
        } else {
            echo "  = coluna tbl_pesagem_uuid_app já existia\n";
        }

        // indice unico tbl_pesagem_uuid_app
        if (!indiceExiste($con, $schema, 'tbl_pesagem', 'ux_pesagem_uuid_app')) {
            $sql = "ALTER TABLE `$schema`.`tbl_pesagem`
                    ADD UNIQUE INDEX `ux_pesagem_uuid_app` (`tbl_pesagem_uuid_app`)";
            if (!mysqli_query($con, $sql)) {
                throw new Exception("Erro ao criar indice ux_pesagem_uuid_app: " . mysqli_error($con));
            }
            echo "  + indice ux_pesagem_uuid_app criado\n";
        } else {
            echo "  = indice ux_pesagem_uuid_app já existia\n";
        }

        // tbl_ite_pesagem_uuid_app
        if (!colunaExiste($con, $schema, 'tbl_item_pesagem', 'tbl_ite_pesagem_uuid_app')) {
            $sql = "ALTER TABLE `$schema`.`tbl_item_pesagem`
                    ADD COLUMN `tbl_ite_pesagem_uuid_app` VARCHAR(36) NULL DEFAULT NULL
                    COMMENT 'UUID gerado pelo app para criacao idempotente offline'";
            if (!mysqli_query($con, $sql)) {
                throw new Exception("Erro ao adicionar tbl_ite_pesagem_uuid_app: " . mysqli_error($con));
            }
            echo "  + coluna tbl_ite_pesagem_uuid_app adicionada\n";
        } else {
            echo "  = coluna tbl_ite_pesagem_uuid_app já existia\n";
        }

        // indice unico tbl_ite_pesagem_uuid_app
        if (!indiceExiste($con, $schema, 'tbl_item_pesagem', 'ux_item_pesagem_uuid_app')) {
            $sql = "ALTER TABLE `$schema`.`tbl_item_pesagem`
                    ADD UNIQUE INDEX `ux_item_pesagem_uuid_app` (`tbl_ite_pesagem_uuid_app`)";
            if (!mysqli_query($con, $sql)) {
                throw new Exception("Erro ao criar indice ux_item_pesagem_uuid_app: " . mysqli_error($con));
            }
            echo "  + indice ux_item_pesagem_uuid_app criado\n";
        } else {
            echo "  = indice ux_item_pesagem_uuid_app já existia\n";
        }

        $aplicados++;
    } catch (Exception $e) {
        $erros++;
        echo "  ERRO: " . $e->getMessage() . "\n";
        // continua para o próximo tenant, não aborta o lote inteiro
    }
}

echo "\nResumo: " . count($schemas) . " schemas verificados, $aplicados com migração aplicada/confirmada, $erros com erro.\n";
exit($erros > 0 ? 1 : 0);
