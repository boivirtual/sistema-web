<?php
/**
 * api/get_anexos.php
 * Retorna HTML com a lista de anexos/links de uma conta a pagar.
 * Reutilizável: aceita numero_doc (preferencial) ou ctp_id (fallback).
 *
 * GET params:
 *   numero_doc        — número do documento (busca todos os ctp_id do documento)
 *   codigo_fornecedor — código do fornecedor (obrigatório quando numero_doc informado)
 *   ctp_id            — id da parcela (usado quando numero_doc está vazio)
 */

include "../valida_sessao.inc";
include "../conecta_mysql.inc";

header('Content-Type: text/html; charset=utf-8');

$numero_doc        = isset($_GET['numero_doc'])        ? trim($_GET['numero_doc'])               : '';
$codigo_fornecedor = isset($_GET['codigo_fornecedor']) ? intval($_GET['codigo_fornecedor'])       : 0;
$ctp_id_param      = isset($_GET['ctp_id'])            ? intval($_GET['ctp_id'])                  : 0;

if ($numero_doc !== '' && $numero_doc !== '0') {
    $nd_esc  = mysqli_real_escape_string($conector, $numero_doc);
    $for_esc = intval($codigo_fornecedor);

    $ssql = "SELECT a.anexo_id, a.anexo_nome, a.anexo_arquivo, a.anexo_tamanho,
                    a.anexo_incluido_em, a.anexo_incluido_por
             FROM tbl_ctp_anexos a
             INNER JOIN contas_pagar c ON c.ctp_id = a.anexo_ctp_id
             WHERE c.ctp_numero_doc = '$nd_esc'
               AND c.ctp_codigo_fornecedor = '$for_esc'
             ORDER BY a.anexo_id ASC";

} elseif ($ctp_id_param > 0) {
    $ssql = "SELECT anexo_id, anexo_nome, anexo_arquivo, anexo_tamanho,
                    anexo_incluido_em, anexo_incluido_por
             FROM tbl_ctp_anexos
             WHERE anexo_ctp_id = '$ctp_id_param'
             ORDER BY anexo_id ASC";
} else {
    echo '<p class="text-muted" style="padding:10px;">Nenhum documento informado.</p>';
    exit;
}

$rs    = mysqli_query($conector, $ssql);
$total = $rs ? mysqli_num_rows($rs) : 0;

if ($total === 0) {
    echo '<p class="text-muted" style="padding:10px 0;">Nenhum anexo encontrado para este documento.</p>';
    mysqli_close($conector);
    exit;
}

$rows = [];
while ($row = mysqli_fetch_object($rs)) {
    $rows[] = $row;
}
$qtd = count($rows);

echo '<ul style="list-style:none;padding:0;margin:0;">';

foreach ($rows as $idx => $row) {
    $arquivo  = $row->anexo_arquivo;
    $is_link  = (stripos($arquivo, 'http://') === 0 || stripos($arquivo, 'https://') === 0);

    if ($is_link) {
        $href   = htmlspecialchars($arquivo, ENT_QUOTES, 'UTF-8');
        $target = ' target="_blank" rel="noopener noreferrer"';
        $extra  = '';
    } else {
        $href   = 'uploads/ctp/' . rawurlencode($arquivo);
        $ext    = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
        $inline = ['pdf','jpg','jpeg','png','gif','bmp','webp','svg'];
        $target = ' target="_blank" rel="noopener noreferrer"';
        $extra  = in_array($ext, $inline) ? '' : ' download';
    }

    $nome     = htmlspecialchars($row->anexo_nome, ENT_QUOTES, 'UTF-8');
    $incluido = $row->anexo_incluido_em
                ? date('d/m/Y H:i', strtotime($row->anexo_incluido_em))
                : '';
    $por      = htmlspecialchars($row->anexo_incluido_por ?? '', ENT_QUOTES, 'UTF-8');
    $borda    = ($idx < $qtd - 1) ? 'border-bottom:1px solid #e8e8e8;' : '';

    $icone = $is_link
        ? '<i class="fas fa-link" style="font-size:12px;color:#337ab7;margin-right:7px;"></i>'
        : '<i class="fas fa-paperclip" style="font-size:12px;color:#337ab7;margin-right:7px;"></i>';

    echo '<li style="display:flex;align-items:flex-start;padding:9px 14px;' . $borda . '">';
    echo '<div style="flex:1;">';
    echo '<a href="' . $href . '"' . $target . $extra . ' style="font-size:13px;">';
    echo $icone . $nome;
    echo '</a>';
    if ($incluido) {
        echo '<small class="text-muted" style="display:block;font-size:10px;margin-top:3px;">';
        echo 'Incluído em ' . $incluido . ($por ? ' por <strong>' . $por . '</strong>' : '');
        echo '</small>';
    }
    echo '</div>';
    echo '<button class="btn-excluir-anexo" data-id="' . $row->anexo_id . '" data-nome="' . $nome . '" data-toggle="tooltip" data-placement="left" title="Excluir" style="background:none;border:none;color:#337ab7;padding:0 4px;margin-left:8px;flex-shrink:0;cursor:pointer;">';
    echo '<i class="fas fa-trash" style="font-size:12px;"></i>';
    echo '</button>';
    echo '</li>';
}

echo '</ul>';

mysqli_close($conector);
