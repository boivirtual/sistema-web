<?php
include "conecta_mysql.inc";

echo "=== contas_pagar doc 963 ===\n";
$r = mysqli_query($conector, "SELECT ctp_id, ctp_numero_doc, ctp_parcela, ctp_codigo_fazenda, ctp_codigo_fornecedor FROM contas_pagar WHERE ctp_numero_doc='963' ORDER BY ctp_id");
while ($row = mysqli_fetch_assoc($r)) { print_r($row); }

echo "\n=== tbl_ctp_rateio rc_ctp_id=10202 ===\n";
$r2 = mysqli_query($conector, "SELECT rc_id, rc_ctp_id, rc_nome_local FROM tbl_ctp_rateio WHERE rc_ctp_id = 10202 LIMIT 5");
while ($row = mysqli_fetch_assoc($r2)) { print_r($row); }

echo "\n=== tbl_ctp_rateio para todos ctp_id do doc 963 ===\n";
$r3 = mysqli_query($conector, "SELECT rc_id, rc_ctp_id, rc_nome_local FROM tbl_ctp_rateio WHERE rc_ctp_id IN (SELECT ctp_id FROM contas_pagar WHERE ctp_numero_doc='963') LIMIT 10");
while ($row = mysqli_fetch_assoc($r3)) { print_r($row); }

echo "\n=== JOIN query para ctp_id=10202 ===\n";
$r4 = mysqli_query($conector, "SELECT MIN(c2.ctp_id) AS primeiro_id FROM contas_pagar c1 JOIN contas_pagar c2 ON c2.ctp_numero_doc = c1.ctp_numero_doc AND c2.ctp_codigo_fornecedor = c1.ctp_codigo_fornecedor AND c2.ctp_codigo_fazenda IS NULL WHERE c1.ctp_id = 10202");
print_r(mysqli_fetch_assoc($r4));
