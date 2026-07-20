<?php
session_write_close();
include "conecta_mysql.inc";

$ctp_id = isset($_POST['ctp_id']) ? intval($_POST['ctp_id']) : 0;
if ($ctp_id <= 0) { echo ''; exit; }

// Registro base, para saber se é uma ocorrência de repetição (ctp_grupo_repeticao preenchido)
$rs_base  = mysqli_query($conector,
    "SELECT ctp_numero_doc, ctp_codigo_fornecedor, ctp_grupo_repeticao,
            ctp_valor_parcela, ctp_valor_juros, ctp_outro_valor, ctp_valor_desconto
     FROM contas_pagar WHERE ctp_id = '$ctp_id'");
$row_base = $rs_base ? mysqli_fetch_object($rs_base) : null;
$grupo_repeticao = $row_base ? ($row_base->ctp_grupo_repeticao ?? '') : '';

if (!empty($grupo_repeticao)) {
    // Repetição: ctp_numero_doc fica vazio em todas as ocorrências — usar apenas o
    // número faria o primeiro-ctp-id e o total conflitarem com outras séries do
    // mesmo fornecedor. O rateio é salvo uma única vez na 1ª ocorrência do grupo,
    // e o total exibido é apenas o valor desta parcela.
    $gr_esc = mysqli_real_escape_string($conector, $grupo_repeticao);
    $rs_prim = mysqli_query($conector,
        "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_grupo_repeticao = '$gr_esc'");
    $row_prim     = mysqli_fetch_object($rs_prim);
    $primeiro_ctp = ($row_prim && $row_prim->primeiro_id) ? (int)$row_prim->primeiro_id : $ctp_id;
    $numero_doc   = '';
    $total_doc    = (float)$row_base->ctp_valor_parcela + (float)$row_base->ctp_valor_juros
                   + (float)$row_base->ctp_outro_valor  - (float)$row_base->ctp_valor_desconto;
} else {
    // Localiza o primeiro ctp_id do documento (onde o rateio foi salvo), o número e o fornecedor
    $rs_prim = mysqli_query($conector,
        "SELECT MIN(c2.ctp_id) AS primeiro_id, c1.ctp_numero_doc, c1.ctp_codigo_fornecedor
         FROM contas_pagar c1
         JOIN contas_pagar c2
           ON c2.ctp_numero_doc         = c1.ctp_numero_doc
          AND c2.ctp_codigo_fornecedor  = c1.ctp_codigo_fornecedor
          AND c2.ctp_codigo_fazenda IS NULL
         WHERE c1.ctp_id = '$ctp_id'");
    $row_prim       = mysqli_fetch_object($rs_prim);
    $primeiro_ctp   = ($row_prim && $row_prim->primeiro_id) ? (int)$row_prim->primeiro_id : $ctp_id;
    $numero_doc_raw = ($row_prim && $row_prim->ctp_numero_doc) ? $row_prim->ctp_numero_doc : '';
    $codigo_for     = ($row_prim && $row_prim->ctp_codigo_fornecedor) ? (int)$row_prim->ctp_codigo_fornecedor : 0;
    $numero_doc     = $numero_doc_raw !== '' ? htmlspecialchars($numero_doc_raw) : '';

    // Total do documento — COALESCE evita NULL em campos opcionais (juros, desconto, outros)
    $num_doc_esc = mysqli_real_escape_string($conector, $numero_doc_raw);
    $rs_total = mysqli_query($conector,
        "SELECT SUM(COALESCE(ctp_valor_parcela, 0) + COALESCE(ctp_valor_juros, 0) + COALESCE(ctp_outro_valor, 0) - COALESCE(ctp_valor_desconto, 0)) AS total_doc
         FROM contas_pagar
         WHERE ctp_numero_doc = '$num_doc_esc'
           AND ctp_codigo_fornecedor = '$codigo_for'
           AND ctp_codigo_fazenda IS NULL");
    $row_total = mysqli_fetch_object($rs_total);
    $total_doc = $row_total ? (float)$row_total->total_doc : 0;
}

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
