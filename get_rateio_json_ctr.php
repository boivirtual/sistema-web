<?php
session_write_close();
include "conecta_mysql.inc";

header('Content-type: application/json');

$ctr_id = isset($_POST['ctr_id']) ? intval($_POST['ctr_id']) : 0;
if ($ctr_id <= 0) {
    echo json_encode(['error' => true, 'message' => 'ID inválido']);
    exit;
}

// Localiza o primeiro ctr_id do documento (onde o rateio foi salvo)
$rs_prim = mysqli_query($conector,
    "SELECT MIN(c2.ctr_id) AS primeiro_id, c1.ctr_numero_doc, c1.ctr_codigo_cliente_fornecedor
     FROM contas_receber c1
     JOIN contas_receber c2
       ON c2.ctr_numero_doc                = c1.ctr_numero_doc
      AND c2.ctr_codigo_cliente_fornecedor  = c1.ctr_codigo_cliente_fornecedor
      AND c2.ctr_codigo_fazenda IS NULL
     WHERE c1.ctr_id = '$ctr_id'");
$row_prim     = mysqli_fetch_object($rs_prim);
$primeiro_ctr = ($row_prim && $row_prim->primeiro_id)                   ? (int)$row_prim->primeiro_id                   : $ctr_id;
$numero_doc   = ($row_prim && $row_prim->ctr_numero_doc)                ? $row_prim->ctr_numero_doc                     : '';
$codigo_cli   = ($row_prim && $row_prim->ctr_codigo_cliente_fornecedor) ? (int)$row_prim->ctr_codigo_cliente_fornecedor : 0;

// Total do documento
$num_doc_esc = mysqli_real_escape_string($conector, $numero_doc);
$rs_total = mysqli_query($conector,
    "SELECT SUM(COALESCE(ctr_valor_parcela,0) + COALESCE(ctr_valor_juros,0) + COALESCE(ctr_valor_acrescimo,0)
               - COALESCE(ctr_valor_desconto,0)) AS total_doc
     FROM contas_receber
     WHERE ctr_numero_doc               = '$num_doc_esc'
       AND ctr_codigo_cliente_fornecedor = '$codigo_cli'
       AND ctr_codigo_fazenda IS NULL");
$row_total  = mysqli_fetch_object($rs_total);
$valor_total = $row_total ? (float)$row_total->total_doc : 0;

// Linhas do rateio
$rs_det = mysqli_query($conector,
    "SELECT rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
            rc_codigo_cc,    rc_nome_cc,    rc_perc_cc,    rc_valor_cc,
            rc_codigo_conta, rc_nome_conta, rc_perc_conta, rc_valor_conta
     FROM tbl_ctr_rateio
     WHERE rc_ctr_id = '$primeiro_ctr'
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
    'primeiro_ctr_id' => $primeiro_ctr,
    'numero_doc'      => $numero_doc,
    'valor_total'     => $valor_total,
    'linhas'          => $linhas,
]);
mysqli_close($conector);
