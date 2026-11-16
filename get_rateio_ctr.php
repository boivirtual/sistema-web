<?php
session_write_close();
include "conecta_mysql.inc";

$ctr_id = isset($_POST['ctr_id']) ? intval($_POST['ctr_id']) : 0;
if ($ctr_id <= 0) { echo ''; exit; }

// Localiza o primeiro ctr_id do documento (onde o rateio foi salvo), o número e o cliente/fornecedor
$rs_prim = mysqli_query($conector,
    "SELECT MIN(c2.ctr_id) AS primeiro_id, c1.ctr_numero_doc, c1.ctr_codigo_cliente_fornecedor
     FROM contas_receber c1
     JOIN contas_receber c2
       ON c2.ctr_numero_doc                = c1.ctr_numero_doc
      AND c2.ctr_codigo_cliente_fornecedor  = c1.ctr_codigo_cliente_fornecedor
      AND c2.ctr_codigo_fazenda IS NULL
     WHERE c1.ctr_id = '$ctr_id'");
$row_prim       = mysqli_fetch_object($rs_prim);
$primeiro_ctr   = ($row_prim && $row_prim->primeiro_id) ? (int)$row_prim->primeiro_id : $ctr_id;
$numero_doc_raw = ($row_prim && $row_prim->ctr_numero_doc) ? $row_prim->ctr_numero_doc : '';
$codigo_cli     = ($row_prim && $row_prim->ctr_codigo_cliente_fornecedor) ? (int)$row_prim->ctr_codigo_cliente_fornecedor : 0;
$numero_doc     = $numero_doc_raw !== '' ? htmlspecialchars($numero_doc_raw) : '';

// Total do documento — COALESCE evita NULL em campos opcionais (juros, desconto, acréscimo)
$num_doc_esc = mysqli_real_escape_string($conector, $numero_doc_raw);
$rs_total = mysqli_query($conector,
    "SELECT SUM(COALESCE(ctr_valor_parcela, 0) + COALESCE(ctr_valor_juros, 0) + COALESCE(ctr_valor_acrescimo, 0) - COALESCE(ctr_valor_desconto, 0)) AS total_doc
     FROM contas_receber
     WHERE ctr_numero_doc = '$num_doc_esc'
       AND ctr_codigo_cliente_fornecedor = '$codigo_cli'
       AND ctr_codigo_fazenda IS NULL");
$row_total = mysqli_fetch_object($rs_total);
$total_doc = $row_total ? (float)$row_total->total_doc : 0;

$rs_det = mysqli_query($conector,
    "SELECT rc_nome_local, rc_perc_local, rc_valor_local,
            rc_nome_cc,    rc_perc_cc,    rc_valor_cc,
            rc_nome_conta, rc_perc_conta, rc_valor_conta
     FROM tbl_ctr_rateio
     WHERE rc_ctr_id = '$primeiro_ctr'
     ORDER BY rc_id ASC");

if (!$rs_det || mysqli_num_rows($rs_det) == 0) {
    echo '<p style="color:#888;">Nenhum dado de rateio encontrado.</p>';
    mysqli_close($conector);
    exit;
}

if ($numero_doc !== '') {
    echo '<p style="font-size:13px;font-weight:600;margin-bottom:10px;">Documento N&ordm;: ' . $numero_doc . '&nbsp;&nbsp;|&nbsp;&nbsp;Valor Total: R$ ' . number_format($total_doc, 2, ',', '.') . '</p>';
}

echo '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
echo '<tr style="border-bottom:1px solid #ccc;">';
echo '<th style="padding:5px 10px;text-align:left;font-weight:600;">Local</th>';
echo '<th style="padding:5px 10px;"></th>';
echo '<th style="padding:5px 10px;text-align:left;font-weight:600;">Centro de Custo</th>';
echo '<th style="padding:5px 10px;"></th>';
echo '<th style="padding:5px 10px;text-align:left;font-weight:600;">Conta Contábil</th>';
echo '<th style="padding:5px 10px;"></th>';
echo '</tr>';

$local_ant = null;
$cc_ant    = null;

while ($rr = mysqli_fetch_object($rs_det)) {
    $local_atual = $rr->rc_nome_local ?? '';
    $cc_atual    = $rr->rc_nome_cc   ?? '';

    $mesmo_local = ($local_atual === $local_ant);
    $mesmo_cc    = ($cc_atual    === $cc_ant && $mesmo_local);

    $borda_top = (!$mesmo_local && $local_ant !== null) ? 'border-top:1px solid #b0c4de;' : '';

    echo '<tr style="border-bottom:1px solid #dde8ff;' . $borda_top . '">';

    // Local — exibe só na primeira linha do grupo
    if (!$mesmo_local) {
        echo '<td style="padding:4px 10px;font-weight:600;">'          . htmlspecialchars($local_atual) . '</td>';
        echo '<td style="padding:4px 10px;text-align:right;font-weight:600;">R$ ' . number_format((float)$rr->rc_valor_local, 2, ',', '.') . '</td>';
    } else {
        echo '<td style="padding:4px 10px;"></td>';
        echo '<td style="padding:4px 10px;"></td>';
    }

    // Centro de Custo — exibe só na primeira linha do grupo
    if (!$mesmo_cc) {
        echo '<td style="padding:4px 10px;">'                   . htmlspecialchars($cc_atual) . '</td>';
        echo '<td style="padding:4px 10px;text-align:right;">'  . ($rr->rc_valor_cc ? 'R$ ' . number_format((float)$rr->rc_valor_cc, 2, ',', '.') : '') . '</td>';
    } else {
        echo '<td style="padding:4px 10px;"></td>';
        echo '<td style="padding:4px 10px;"></td>';
    }

    // Conta Contábil — sempre exibe
    echo '<td style="padding:4px 10px;">'                   . htmlspecialchars($rr->rc_nome_conta ?? '') . '</td>';
    echo '<td style="padding:4px 10px;text-align:right;">'  . ($rr->rc_valor_conta ? 'R$ ' . number_format((float)$rr->rc_valor_conta, 2, ',', '.') : '') . '</td>';

    echo '</tr>';

    $local_ant = $local_atual;
    $cc_ant    = $cc_atual;
}

echo '</table>';
mysqli_close($conector);
