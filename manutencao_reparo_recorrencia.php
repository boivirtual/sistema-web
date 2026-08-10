<?php
/**
 * Reparo pontual: lançamentos "Repetir Lançamento" com Rateio e/ou Anexos/Links
 * gravados só na 1ª ocorrência (bug corrigido em gravar_contas_pagar.php).
 *
 * Copia o rateio (tbl_ctp_rateio) e os anexos/links (tbl_ctp_anexos) da 1ª ocorrência
 * de cada grupo de repetição (ctp_grupo_repeticao) para as demais ocorrências do
 * mesmo grupo que ainda estiverem sem esses dados.
 *
 * Idempotente: só insere o que está faltando. Rodar de novo não duplica nada.
 * Usa a conexão da sessão logada (mesma lógica multi-tenant do resto do sistema) —
 * não precisa de credenciais, só estar logado no cliente certo.
 *
 * Uso:
 *   manutencao_reparo_recorrencia.php            → modo PREVIEW (não grava nada)
 *   manutencao_reparo_recorrencia.php?aplicar=1  → aplica de fato
 *
 * Apague este arquivo depois de usar em todos os clientes.
 */

include "valida_sessao.inc";
include "conecta_mysql.inc";

$aplicar = isset($_GET['aplicar']) && $_GET['aplicar'] === '1';

header('Content-Type: text/html; charset=utf-8');
echo "<pre style='font-family:monospace;font-size:13px;'>";
echo $aplicar ? "MODO: APLICANDO ALTERAÇÕES\n\n" : "MODO: PREVIEW (nada será gravado — adicione ?aplicar=1 na URL para aplicar)\n\n";

function grupos_com_dados($conector, $tabela, $coluna_id) {
    $rs = mysqli_query($conector, "
        SELECT DISTINCT c.ctp_grupo_repeticao
        FROM $tabela a
        INNER JOIN contas_pagar c ON c.ctp_id = a.$coluna_id
        WHERE c.ctp_grupo_repeticao IS NOT NULL AND c.ctp_grupo_repeticao != ''
    ");
    $grupos = [];
    while ($r = mysqli_fetch_assoc($rs)) { $grupos[] = $r['ctp_grupo_repeticao']; }
    return $grupos;
}

// ── RATEIO ──────────────────────────────────────────────────────────────
echo "=== RATEIO (tbl_ctp_rateio) ===\n";
$total_rateio = 0;
foreach (grupos_com_dados($conector, 'tbl_ctp_rateio', 'rc_ctp_id') as $g) {
    $ge = mysqli_real_escape_string($conector, $g);
    $membros = mysqli_query($conector, "SELECT ctp_id FROM contas_pagar WHERE ctp_grupo_repeticao='$ge'");
    $ids = [];
    while ($m = mysqli_fetch_assoc($membros)) { $ids[] = (int)$m['ctp_id']; }
    if (!$ids) continue;

    $com_dados = mysqli_query($conector, "SELECT DISTINCT rc_ctp_id FROM tbl_ctp_rateio WHERE rc_ctp_id IN (" . implode(',', $ids) . ")");
    $fonte = null;
    while ($cd = mysqli_fetch_assoc($com_dados)) { $fonte = (int)$cd['rc_ctp_id']; break; }
    if (!$fonte) continue;

    foreach ($ids as $id) {
        if ($id === $fonte) continue;
        $chk = mysqli_query($conector, "SELECT COUNT(*) c FROM tbl_ctp_rateio WHERE rc_ctp_id=$id");
        if (mysqli_fetch_assoc($chk)['c'] > 0) continue;

        echo "grupo $g: falta rateio em $id (copiando de $fonte)\n";
        if ($aplicar) {
            mysqli_query($conector, "INSERT INTO tbl_ctp_rateio
                    (rc_ctp_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
                     rc_codigo_cc, rc_nome_cc, rc_perc_cc, rc_valor_cc,
                     rc_codigo_conta, rc_nome_conta, rc_perc_conta, rc_valor_conta,
                     rc_incluido_em, rc_incluido_por)
                SELECT $id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
                       rc_codigo_cc, rc_nome_cc, rc_perc_cc, rc_valor_cc,
                       rc_codigo_conta, rc_nome_conta, rc_perc_conta, rc_valor_conta,
                       NOW(), 'Reparo automático (recorrência)'
                FROM tbl_ctp_rateio WHERE rc_ctp_id = $fonte");
            $total_rateio += mysqli_affected_rows($conector);
        }
    }
}
echo $aplicar ? "Total de linhas de rateio inseridas: $total_rateio\n\n" : "\n";

// ── ANEXOS/LINKS ────────────────────────────────────────────────────────
echo "=== ANEXOS/LINKS (tbl_ctp_anexos) ===\n";
$total_anexos = 0;
foreach (grupos_com_dados($conector, 'tbl_ctp_anexos', 'anexo_ctp_id') as $g) {
    $ge = mysqli_real_escape_string($conector, $g);
    $membros = mysqli_query($conector, "SELECT ctp_id FROM contas_pagar WHERE ctp_grupo_repeticao='$ge'");
    $ids = [];
    while ($m = mysqli_fetch_assoc($membros)) { $ids[] = (int)$m['ctp_id']; }
    if (!$ids) continue;

    $com_dados = mysqli_query($conector, "SELECT DISTINCT anexo_ctp_id FROM tbl_ctp_anexos WHERE anexo_ctp_id IN (" . implode(',', $ids) . ")");
    $fonte = null;
    while ($cd = mysqli_fetch_assoc($com_dados)) { $fonte = (int)$cd['anexo_ctp_id']; break; }
    if (!$fonte) continue;

    foreach ($ids as $id) {
        if ($id === $fonte) continue;
        $chk = mysqli_query($conector, "SELECT COUNT(*) c FROM tbl_ctp_anexos WHERE anexo_ctp_id=$id");
        if (mysqli_fetch_assoc($chk)['c'] > 0) continue;

        echo "grupo $g: falta anexo em $id (copiando de $fonte)\n";
        if ($aplicar) {
            mysqli_query($conector, "INSERT INTO tbl_ctp_anexos
                    (anexo_ctp_id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por)
                SELECT $id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por
                FROM tbl_ctp_anexos WHERE anexo_ctp_id = $fonte");
            $total_anexos += mysqli_affected_rows($conector);
        }
    }
}
echo $aplicar ? "Total de linhas de anexo inseridas: $total_anexos\n" : "\n";

echo "\n" . ($aplicar ? "Concluído." : "Revise a lista acima. Se estiver correta, adicione ?aplicar=1 na URL para gravar de verdade.") . "\n";
echo "</pre>";

mysqli_close($conector);
