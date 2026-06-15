<?php
// Script de migração — executar uma única vez e depois deletar
include "conecta_mysql.inc";

$sqls = [];

// 1. Coluna observações em contas_pagar
$sqls[] = "ALTER TABLE contas_pagar ADD COLUMN ctp_observacoes VARCHAR(500) NULL DEFAULT NULL AFTER ctp_descricao_compra";

// 2. Tabela de anexos
$sqls[] = "CREATE TABLE IF NOT EXISTS tbl_ctp_anexos (
    anexo_id           INT(11)      NOT NULL AUTO_INCREMENT,
    anexo_ctp_id       INT(11)      NOT NULL,
    anexo_nome         VARCHAR(255) NOT NULL,
    anexo_arquivo      VARCHAR(255) NOT NULL,
    anexo_tamanho      INT(11)      NULL DEFAULT NULL,
    anexo_incluido_em  DATETIME     NULL DEFAULT NULL,
    anexo_incluido_por VARCHAR(100) NULL DEFAULT NULL,
    PRIMARY KEY (anexo_id),
    KEY idx_ctp_id (anexo_ctp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

echo '<pre>';
foreach ($sqls as $sql) {
    $r = mysqli_query($conector, $sql);
    $err = mysqli_error($conector);
    if ($r) {
        echo "✅ OK: " . substr($sql, 0, 80) . "...\n";
    } else {
        echo "❌ ERRO: $err\n   SQL: $sql\n";
    }
}
echo '</pre>';
echo '<p><strong>Migração concluída. Apague este arquivo.</strong></p>';
mysqli_close($conector);
