<?php
/**
 * Reparo pontual: lançamentos "Repetir Lançamento" com Rateio gravado só na
 * 1ª ocorrência (bug corrigido em gravar_contas_pagar.php).
 *
 * RATEIO: copia tbl_ctp_rateio da 1ª ocorrência de cada grupo de repetição
 * (ctp_grupo_repeticao) para as demais ocorrências do mesmo grupo que ainda
 * estiverem sem esses dados — a tela de edição busca o rateio pelo ctp_id
 * exato da ocorrência, então cada uma precisa da própria cópia.
 *
 * ANEXOS/LINKS: NÃO precisam ser duplicados — a busca (api/get_anexos.php)
 * já enxerga o grupo de repetição inteiro numa única consulta, então o anexo
 * salvo só na 1ª ocorrência já aparece em todas. Este script apenas detecta e
 * remove eventuais cópias duplicadas (caso uma versão anterior deste mesmo
 * script já tenha rodado e duplicado por engano).
 *
 * Idempotente: rodar de novo não duplica nem quebra nada.
 * Usa a conexão da sessão logada (mesma lógica multi-tenant do resto do
 * sistema) — não precisa de credenciais, só estar logado no cliente certo.
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

// ── RATEIO: preenche o que falta em cada ocorrência ────────────────────
echo "=== RATEIO (tbl_ctp_rateio) — preenchendo o que falta ===\n";
$total_rateio = 0;
foreach (grupos_com_dados($conector, 'tbl_ctp_rateio', 'rc_ctp_id') as $g) {
    $ge = mysqli_real_escape_string($conector, $g);
    $membros = mysqli_query($conector, "SELECT ctp_id FROM contas_pagar WHERE ctp_grupo_repeticao='$ge' ORDER BY ctp_id");
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

// ── ANEXOS/LINKS: remove cópias duplicadas, mantém só na 1ª ocorrência ──
echo "=== ANEXOS/LINKS (tbl_ctp_anexos) — removendo duplicatas, se houver ===\n";
$total_anexos_removidos = 0;
foreach (grupos_com_dados($conector, 'tbl_ctp_anexos', 'anexo_ctp_id') as $g) {
    $ge = mysqli_real_escape_string($conector, $g);
    $membros = mysqli_query($conector, "SELECT ctp_id FROM contas_pagar WHERE ctp_grupo_repeticao='$ge' ORDER BY ctp_id");
    $ids = [];
    while ($m = mysqli_fetch_assoc($membros)) { $ids[] = (int)$m['ctp_id']; }
    if (!$ids) continue;

    // Mantém os anexos da ocorrência mais antiga (1ª) do grupo; remove cópias das demais.
    $manter = $ids[0];
    $outros = array_slice($ids, 1);
    if (!$outros) continue;

    $rs_dup = mysqli_query($conector, "SELECT anexo_id, anexo_ctp_id FROM tbl_ctp_anexos WHERE anexo_ctp_id IN (" . implode(',', $outros) . ")");
    while ($d = mysqli_fetch_assoc($rs_dup)) {
        echo "grupo $g: anexo duplicado (id {$d['anexo_id']}, na ocorrência {$d['anexo_ctp_id']}) — mantendo só em $manter\n";
        if ($aplicar) {
            mysqli_query($conector, "DELETE FROM tbl_ctp_anexos WHERE anexo_id = {$d['anexo_id']}");
            $total_anexos_removidos += mysqli_affected_rows($conector);
        }
    }
}
echo $aplicar ? "Total de anexos duplicados removidos: $total_anexos_removidos\n" : "\n";

echo "\n" . ($aplicar ? "Concluído." : "Revise a lista acima. Se estiver correta, adicione ?aplicar=1 na URL para gravar de verdade.") . "\n";
echo "</pre>";

mysqli_close($conector);
