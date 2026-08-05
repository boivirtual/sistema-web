<?php
function resolver_primeiro_ctp_rateio($conector, $ctp_id, $ctp_grupo_repeticao, $ctp_numero_doc = null, $ctp_codigo_fornecedor = null, $ctp_incluido_em = null) {
    if (!empty($ctp_grupo_repeticao)) {
        $gr_esc = mysqli_real_escape_string($conector, $ctp_grupo_repeticao);
        $rs = mysqli_query($conector, "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_grupo_repeticao = '$gr_esc'");
        $row = $rs ? mysqli_fetch_object($rs) : null;
        return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctp_id;
    }
    if ($ctp_numero_doc !== null && $ctp_numero_doc !== '' && $ctp_codigo_fornecedor !== null) {
        $nd_esc  = mysqli_real_escape_string($conector, $ctp_numero_doc);
        $for_esc = intval($ctp_codigo_fornecedor);
        $rs = mysqli_query($conector, "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_numero_doc = '$nd_esc' AND ctp_codigo_fornecedor = '$for_esc' AND ctp_codigo_fazenda IS NULL");
        $row = $rs ? mysqli_fetch_object($rs) : null;
        return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctp_id;
    }
    return $ctp_id;
}

function montar_fatias_conta_rateio($conector, $ctp_id, $cod_conta_header, $total_pagar, $valor_pago, $total_vencidas, $total_avencer, $ctp_grupo_repeticao = null, $ctp_numero_doc = null, $ctp_codigo_fornecedor = null, $ctp_incluido_em = null, $fazendas_ids = '', $cc_ids = '') {
    if ($cod_conta_header !== null && $cod_conta_header !== '') {
        return [['cod_conta' => $cod_conta_header, 'total_pagar' => $total_pagar, 'valor_pago' => $valor_pago, 'total_vencidas' => $total_vencidas, 'total_avencer' => $total_avencer]];
    }

    $ctp_id_rateio = resolver_primeiro_ctp_rateio($conector, $ctp_id, $ctp_grupo_repeticao, $ctp_numero_doc, $ctp_codigo_fornecedor, $ctp_incluido_em);

    $rs_soma = mysqli_query($conector, "SELECT SUM(rc_valor_conta) AS soma FROM tbl_ctp_rateio
        WHERE rc_ctp_id='$ctp_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");
    $row_soma = $rs_soma ? mysqli_fetch_object($rs_soma) : null;
    $soma_rateio = $row_soma ? (float)$row_soma->soma : 0;

    if ($soma_rateio == 0) {
        return array();
    }

    $wlocal_rateio = ($fazendas_ids != '') ? " AND rc_codigo_local IN($fazendas_ids)" : '';
    $wcc_rateio = ($cc_ids != '') ? " AND (rc_codigo_cc IS NULL OR rc_codigo_cc IN($cc_ids))" : '';

    $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctp_rateio
        WHERE rc_ctp_id='$ctp_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''" . $wlocal_rateio . $wcc_rateio);

    $fatias = array();
    while ($r = mysqli_fetch_object($rs)) {
        $prop = $r->rc_valor_conta / $soma_rateio;
        $fatias[] = ['cod_conta' => $r->rc_codigo_conta, 'total_pagar' => $total_pagar * $prop, 'valor_pago' => $valor_pago * $prop, 'total_vencidas' => $total_vencidas * $prop, 'total_avencer' => $total_avencer * $prop];
    }
    return $fatias;
}

include_once "conecta_mysql_credenciais.inc";
$banco = "97174041604";
$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
if (mysqli_connect_error()) { die("Falha na conexao"); }

// doc 125: ctp_id=10383, total_pagar=280, valor_pago=280 (pago total)
echo "--- SEM filtro de local ---\n";
$f = montar_fatias_conta_rateio($conector, 10383, null, 280, 280, 0, 0, null, '125', 50, '2026-07-16 12:32:36', '', '');
print_r($f);

echo "--- filtro Local = 56 (Fazenda Casa Blanca) ---\n";
$f = montar_fatias_conta_rateio($conector, 10383, null, 280, 280, 0, 0, null, '125', 50, '2026-07-16 12:32:36', '56', '');
print_r($f);

echo "--- filtro Local = 77 (Fazenda Santa Helena) ---\n";
$f = montar_fatias_conta_rateio($conector, 10383, null, 280, 280, 0, 0, null, '125', 50, '2026-07-16 12:32:36', '77', '');
print_r($f);

mysqli_close($conector);
