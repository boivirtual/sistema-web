<?php
session_write_close();
include "conecta_mysql.inc";

$ctp_id = isset($_POST['ctp_id']) ? intval($_POST['ctp_id']) : 0;
if ($ctp_id <= 0) { echo ''; exit; }

// Localiza o primeiro ctp_id do documento (onde o rateio foi salvo)
$rs_prim = mysqli_query($conector,
    "SELECT MIN(c2.ctp_id) AS primeiro_id
     FROM contas_pagar c1
     JOIN contas_pagar c2
       ON c2.ctp_numero_doc         = c1.ctp_numero_doc
      AND c2.ctp_codigo_fornecedor  = c1.ctp_codigo_fornecedor
      AND c2.ctp_codigo_fazenda IS NULL
     WHERE c1.ctp_id = '$ctp_id'");
$row_prim     = mysqli_fetch_object($rs_prim);
$primeiro_ctp = ($row_prim && $row_prim->primeiro_id) ? (int)$row_prim->primeiro_id : $ctp_id;

$rs_det = mysqli_query($conector,
    "SELECT rc_nome_local, rc_perc_local, rc_valor_local,
            rc_nome_cc,    rc_perc_cc,    rc_valor_cc,
            rc_nome_conta, rc_perc_conta, rc_valor_conta
     FROM tbl_ctp_rateio
     WHERE rc_ctp_id = '$primeiro_ctp'
     ORDER BY rc_id ASC");

if (!$rs_det || mysqli_num_rows($rs_det) == 0) {
    echo '<p style="color:#888;">Nenhum dado de rateio encontrado.</p>';
    mysqli_close($conector);
    exit;
}

echo '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
echo '<tr style="background:#e8eeff;">';
echo '<th style="padding:5px 10px;text-align:left;">Local</th>';
echo '<th style="padding:5px 10px;text-align:right;">Vlr. Local</th>';
echo '<th style="padding:5px 10px;text-align:left;">Centro de Custo</th>';
echo '<th style="padding:5px 10px;text-align:right;">Vlr. CC</th>';
echo '<th style="padding:5px 10px;text-align:left;">Conta Contábil</th>';
echo '<th style="padding:5px 10px;text-align:right;">Valor</th>';
echo '</tr>';

while ($rr = mysqli_fetch_object($rs_det)) {
    echo '<tr style="border-bottom:1px solid #dde8ff;">';
    echo '<td style="padding:4px 10px;">'                    . htmlspecialchars($rr->rc_nome_local ?? '') . '</td>';
    echo '<td style="padding:4px 10px;text-align:right;">R$ ' . number_format((float)$rr->rc_valor_local, 2, ',', '.') . '</td>';
    echo '<td style="padding:4px 10px;">'                    . htmlspecialchars($rr->rc_nome_cc ?? '') . '</td>';
    echo '<td style="padding:4px 10px;text-align:right;">'   . ($rr->rc_valor_cc  ? 'R$ ' . number_format((float)$rr->rc_valor_cc,  2, ',', '.') : '') . '</td>';
    echo '<td style="padding:4px 10px;">'                    . htmlspecialchars($rr->rc_nome_conta ?? '') . '</td>';
    echo '<td style="padding:4px 10px;text-align:right;">'   . ($rr->rc_valor_conta ? 'R$ ' . number_format((float)$rr->rc_valor_conta, 2, ',', '.') : '') . '</td>';
    echo '</tr>';
}

echo '</table>';
mysqli_close($conector);
