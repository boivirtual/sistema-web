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

include_once "conecta_mysql_credenciais.inc";
$banco = "97174041604";
$conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
if (mysqli_connect_error()) {
    die("Falha na conexao: " . mysqli_connect_error());
}

// simula filtro Local = FAZENDA CASA BLANCA (id 56)
$fazendas = "56";

echo "=== filtro ANTIGO (so match direto, sem rateio) ===\n";
$wlocal_pag_antigo = " AND ctp_codigo_fazenda IN($fazendas)";
$sql = "SELECT ctp_id, ctp_numero_doc FROM contas_pagar WHERE ctp_numero_doc='125'" . $wlocal_pag_antigo;
$res = mysqli_query($conector, $sql);
echo "linhas encontradas: " . mysqli_num_rows($res) . "\n";

echo "\n=== filtro NOVO (rateio-aware) ===\n";
$wlocal_pag_novo = " AND (ctp_codigo_fazenda IN($fazendas) OR " . condicao_rateio_ou_grupo('ctp_codigo_fazenda', 'rc_codigo_local', $fazendas) . ")";
$sql = "SELECT ctp_id, ctp_numero_doc FROM contas_pagar WHERE ctp_numero_doc='125'" . $wlocal_pag_novo;
$res = mysqli_query($conector, $sql);
if (!$res) { die("Erro SQL: " . mysqli_error($conector)); }
echo "linhas encontradas: " . mysqli_num_rows($res) . "\n";

mysqli_close($conector);
