<?php
include "valida_sessao.inc";
include "conecta_mysql.inc";

$ctp_id = intval($_GET['id'] ?? 10202);

echo "<pre>";

echo "=== contas_pagar - todas as parcelas do mesmo documento ===\n";
$r = mysqli_query($conector, "SELECT ctp_id, ctp_numero_doc, ctp_numero_documento, ctp_parcela, ctp_codigo_fazenda, ctp_codigo_fornecedor FROM contas_pagar WHERE ctp_numero_doc = (SELECT ctp_numero_doc FROM contas_pagar WHERE ctp_id = $ctp_id) ORDER BY ctp_id");
while ($row = mysqli_fetch_assoc($r)) { print_r($row); }

echo "\n=== tbl_ctp_rateio - direto pelo ctp_id $ctp_id ===\n";
$r2 = mysqli_query($conector, "SELECT rc_id, rc_ctp_id, rc_nome_local FROM tbl_ctp_rateio WHERE rc_ctp_id = $ctp_id LIMIT 5");
echo "Rows: " . mysqli_num_rows($r2) . "\n";
while ($row = mysqli_fetch_assoc($r2)) { print_r($row); }

echo "\n=== JOIN query (usada no get_rateio_aceite.php) ===\n";
$r3 = mysqli_query($conector, "SELECT MIN(c2.ctp_id) AS primeiro_id FROM contas_pagar c1 JOIN contas_pagar c2 ON c2.ctp_numero_doc = c1.ctp_numero_doc AND c2.ctp_codigo_fornecedor = c1.ctp_codigo_fornecedor AND c2.ctp_codigo_fazenda IS NULL WHERE c1.ctp_id = $ctp_id");
$row3 = mysqli_fetch_assoc($r3);
echo "primeiro_id encontrado: " . ($row3['primeiro_id'] ?? 'NULL') . "\n";

echo "\n=== tbl_ctp_rateio - pelo primeiro_id encontrado ===\n";
$pid = intval($row3['primeiro_id'] ?? $ctp_id);
$r4 = mysqli_query($conector, "SELECT rc_id, rc_ctp_id, rc_nome_local FROM tbl_ctp_rateio WHERE rc_ctp_id = $pid LIMIT 5");
echo "Rows: " . mysqli_num_rows($r4) . "\n";
while ($row = mysqli_fetch_assoc($r4)) { print_r($row); }

echo "\n=== tbl_ctp_rateio - todos os rc_ctp_id existentes para parcelas deste doc ===\n";
$r5 = mysqli_query($conector, "SELECT DISTINCT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_ctp_id IN (SELECT ctp_id FROM contas_pagar WHERE ctp_numero_doc = (SELECT ctp_numero_doc FROM contas_pagar WHERE ctp_id = $ctp_id))");
echo "rc_ctp_ids com rateio: ";
while ($row = mysqli_fetch_assoc($r5)) { echo $row['rc_ctp_id'] . " "; }
echo "\n";

echo "</pre>";
