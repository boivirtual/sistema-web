<?php
session_write_close();
include "conecta_mysql.inc";

header('Content-type: application/json');

$ctp_id = isset($_POST['ctp_id']) ? intval($_POST['ctp_id']) : 0;
if ($ctp_id <= 0) {
    echo json_encode(['error' => true, 'message' => 'ID inválido']);
    exit;
}

// Localiza o primeiro ctp_id do documento (onde o rateio foi salvo)
$rs_prim = mysqli_query($conector,
    "SELECT MIN(c2.ctp_id) AS primeiro_id, c1.ctp_numero_doc, c1.ctp_codigo_fornecedor
     FROM contas_pagar c1
     JOIN contas_pagar c2
       ON c2.ctp_numero_doc        = c1.ctp_numero_doc
      AND c2.ctp_codigo_fornecedor = c1.ctp_codigo_fornecedor
      AND c2.ctp_codigo_fazenda IS NULL
     WHERE c1.ctp_id = '$ctp_id'");
$row_prim     = mysqli_fetch_object($rs_prim);
$primeiro_ctp = ($row_prim && $row_prim->primeiro_id)            ? (int)$row_prim->primeiro_id            : $ctp_id;
$numero_doc   = ($row_prim && $row_prim->ctp_numero_doc)         ? $row_prim->ctp_numero_doc              : '';
$codigo_for   = ($row_prim && $row_prim->ctp_codigo_fornecedor)  ? (int)$row_prim->ctp_codigo_fornecedor  : 0;

// Total do documento
$num_doc_esc = mysqli_real_escape_string($conector, $numero_doc);
$rs_total = mysqli_query($conector,
    "SELECT SUM(COALESCE(ctp_valor_parcela,0) + COALESCE(ctp_valor_juros,0) + COALESCE(ctp_outro_valor,0)
               - COALESCE(ctp_valor_desconto,0)) AS total_doc
     FROM contas_pagar
     WHERE ctp_numero_doc        = '$num_doc_esc'
       AND ctp_codigo_fornecedor = '$codigo_for'
       AND ctp_codigo_fazenda IS NULL");
$row_total  = mysqli_fetch_object($rs_total);
$valor_total = $row_total ? (float)$row_total->total_doc : 0;

// Linhas do rateio
$rs_det = mysqli_query($conector,
    "SELECT rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
            rc_codigo_cc,    rc_nome_cc,    rc_perc_cc,    rc_valor_cc,
            rc_codigo_conta, rc_nome_conta, rc_perc_conta, rc_valor_conta
     FROM tbl_ctp_rateio
     WHERE rc_ctp_id = '$primeiro_ctp'
     ORDER BY rc_id ASC");

$linhas = [];
while ($r = mysqli_fetch_object($rs_det)) {
    $linhas[] = [
        'local_id'    => (int)($r->rc_codigo_local ?? 0),
        'local_nome'  => $r->rc_nome_local   ?? '',
        'local_valor' => (float)($r->rc_valor_local ?? 0),
        'local_perc'  => (float)($r->rc_perc_local  ?? 0),
        'cc_id'       => $r->rc_codigo_cc    ?? '',
        'cc_nome'     => $r->rc_nome_cc      ?? '',
        'cc_valor'    => (float)($r->rc_valor_cc     ?? 0),
        'cc_perc'     => (float)($r->rc_perc_cc      ?? 0),
        'conta_id'    => $r->rc_codigo_conta ?? '',
        'conta_nome'  => $r->rc_nome_conta   ?? '',
        'conta_valor' => (float)($r->rc_valor_conta  ?? 0),
        'conta_perc'  => (float)($r->rc_perc_conta   ?? 0),
    ];
}

echo json_encode([
    'primeiro_ctp_id' => $primeiro_ctp,
    'numero_doc'      => $numero_doc,
    'valor_total'     => $valor_total,
    'linhas'          => $linhas,
]);
mysqli_close($conector);
