<?php
function condicao_rateio_ou_grupo($coluna_ctp, $coluna_rateio, $ids_str) {
    return "($coluna_ctp IS NULL AND ctp_id IN (
        SELECT DISTINCT cp2.ctp_id
        FROM contas_pagar cp1
        INNER JOIN contas_pagar cp2 ON (
            (cp1.ctp_grupo_repeticao IS NOT NULL AND cp2.ctp_grupo_repeticao = cp1.ctp_grupo_repeticao)
            OR (cp1.ctp_grupo_repeticao IS NULL AND cp1.ctp_numero_doc IS NOT NULL AND cp1.ctp_numero_doc != ''
                AND cp2.ctp_codigo_fazenda IS NULL
                AND cp2.ctp_numero_doc = cp1.ctp_numero_doc
                AND cp2.ctp_codigo_fornecedor = cp1.ctp_codigo_fornecedor)
            OR (cp1.ctp_grupo_repeticao IS NULL AND (cp1.ctp_numero_doc IS NULL OR cp1.ctp_numero_doc = '')
                AND cp2.ctp_codigo_fazenda IS NULL
                AND cp2.ctp_codigo_fornecedor = cp1.ctp_codigo_fornecedor
                AND cp2.ctp_incluido_em = cp1.ctp_incluido_em)
        )
        WHERE cp1.ctp_id IN (SELECT rc_ctp_id FROM tbl_ctp_rateio WHERE $coluna_rateio IN ($ids_str))
    ))";
}

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
    if ($ctp_codigo_fornecedor !== null && $ctp_incluido_em !== null && $ctp_incluido_em !== '') {
        $for_esc  = intval($ctp_codigo_fornecedor);
        $inc_esc  = mysqli_real_escape_string($conector, $ctp_incluido_em);
        $rs = mysqli_query($conector, "SELECT MIN(ctp_id) AS primeiro_id FROM contas_pagar WHERE ctp_codigo_fornecedor = '$for_esc' AND ctp_incluido_em = '$inc_esc' AND ctp_codigo_fazenda IS NULL");
        $row = $rs ? mysqli_fetch_object($rs) : null;
        return ($row && $row->primeiro_id) ? (int)$row->primeiro_id : $ctp_id;
    }
    return $ctp_id;
}

function montar_fatias_conta_rateio_ctp($conector, $ctp_id, $cod_conta_header, $valor, $ctp_grupo_repeticao = null, $ctp_numero_doc = null, $ctp_codigo_fornecedor = null, $ctp_incluido_em = null) {
    if ($cod_conta_header !== null && $cod_conta_header !== '') {
        return [['cod_conta' => $cod_conta_header, 'valor' => $valor]];
    }

    $ctp_id_rateio = resolver_primeiro_ctp_rateio($conector, $ctp_id, $ctp_grupo_repeticao, $ctp_numero_doc, $ctp_codigo_fornecedor, $ctp_incluido_em);
    echo "  -> ctp_id_rateio resolvido = $ctp_id_rateio\n";

    $linhas_rateio = array();
    $soma_rateio = 0;

    $rs = mysqli_query($conector, "SELECT rc_codigo_conta, rc_valor_conta FROM tbl_ctp_rateio
        WHERE rc_ctp_id='$ctp_id_rateio' AND rc_codigo_conta IS NOT NULL AND rc_codigo_conta != ''");

    while ($r = mysqli_fetch_object($rs)) {
        $linhas_rateio[] = $r;
        $soma_rateio += $r->rc_valor_conta;
    }

    echo "  -> linhas_rateio encontradas: " . count($linhas_rateio) . " soma=$soma_rateio\n";

    if (count($linhas_rateio) == 0 || $soma_rateio == 0) {
        return array();
    }

    $fatias = array();

    foreach ($linhas_rateio as $r) {
        $prop = $r->rc_valor_conta / $soma_rateio;
        $fatias[] = ['cod_conta' => $r->rc_codigo_conta, 'valor' => $valor * $prop];
    }

    return $fatias;
}

include_once "conecta_mysql_credenciais.inc";
$banco = "97174041604";
$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
if (mysqli_connect_error()) {
    die("Falha na conexao: " . mysqli_connect_error());
}

$sql = "SELECT * FROM baixa_contas_pagar
    inner join contas_pagar
            on ctp_id=bcp_id
    WHERE ctp_numero_doc='125'";

$res = mysqli_query($conector, $sql);
while ($row = mysqli_fetch_object($res)) {
    echo "Linha: ctp_id={$row->ctp_id} valor_pago={$row->bcp_valor_pagamento} conta=" . var_export($row->ctp_codigo_conta, true) . " grupo_rep=" . var_export($row->ctp_grupo_repeticao, true) . " numero_doc=" . var_export($row->ctp_numero_doc, true) . " fornecedor=" . var_export($row->ctp_codigo_fornecedor, true) . " incluido_em=" . var_export($row->ctp_incluido_em, true) . "\n";

    $fatias = montar_fatias_conta_rateio_ctp($conector, $row->ctp_id, $row->ctp_codigo_conta, $row->bcp_valor_pagamento, $row->ctp_grupo_repeticao, $row->ctp_numero_doc, $row->ctp_codigo_fornecedor, $row->ctp_incluido_em);

    echo "  -> fatias resultantes:\n";
    print_r($fatias);
}

mysqli_close($conector);
