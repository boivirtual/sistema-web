<?php
/**
 * api/excluir_anexo_ctr.php
 * Exclui um anexo/link da tabela tbl_ctr_anexos e, se for arquivo, remove o físico.
 *
 * POST params:
 *   anexo_id — id do registro em tbl_ctr_anexos
 */

include "../valida_sessao.inc";
include "../conecta_mysql.inc";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método inválido.']);
    exit;
}

$anexo_id = isset($_POST['anexo_id']) ? intval($_POST['anexo_id']) : 0;

if ($anexo_id <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID inválido.']);
    exit;
}

$ssql = "SELECT anexo_arquivo FROM tbl_ctr_anexos WHERE anexo_id = $anexo_id";
$rs   = mysqli_query($conector, $ssql);

if (!$rs || mysqli_num_rows($rs) === 0) {
    echo json_encode(['ok' => false, 'msg' => 'Anexo não encontrado.']);
    mysqli_close($conector);
    exit;
}

$row    = mysqli_fetch_object($rs);
$arquivo = $row->anexo_arquivo;

$is_link = (stripos($arquivo, 'http://') === 0 || stripos($arquivo, 'https://') === 0);
if (!$is_link && $arquivo !== '') {
    $caminho = dirname(__DIR__) . '/uploads/ctr/' . $arquivo;
    if (file_exists($caminho)) {
        unlink($caminho);
    }
}

$ok = mysqli_query($conector, "DELETE FROM tbl_ctr_anexos WHERE anexo_id = $anexo_id");

mysqli_close($conector);

if ($ok) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao excluir do banco de dados.']);
}
