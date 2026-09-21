<?php
include "conecta_mysql.inc";

$numero_doc  = isset($_POST['numero_doc'])  ? mysqli_real_escape_string($conector, trim($_POST['numero_doc']))  : '';
$codigo_for  = isset($_POST['codigo_for'])  ? mysqli_real_escape_string($conector, trim($_POST['codigo_for']))  : '';

if ($numero_doc === '' || $codigo_for === '' || $codigo_for === '999999999') {
    echo '0';
    exit;
}

$sql = "SELECT COUNT(*) as total FROM contas_pagar
        WHERE ctp_numero_doc = '$numero_doc'
          AND ctp_codigo_fornecedor = '$codigo_for'";

$result = mysqli_query($conector, $sql);
if (!$result) {
    echo '0';
    exit;
}

$row = mysqli_fetch_assoc($result);
echo intval($row['total']) > 0 ? '1' : '0';
