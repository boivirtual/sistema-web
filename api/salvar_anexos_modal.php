<?php
/**
 * api/salvar_anexos_modal.php
 * Salva novos anexos/links para uma parcela via modal_anexos.
 *
 * POST params:
 *   ctp_id              — id da parcela em contas_pagar
 *   anexo[]             — arquivos (multipart)
 *   anexo_link_url[]    — URLs de links
 *   anexo_link_desc[]   — descrições dos links
 */

include "../valida_sessao.inc";
include "../conecta_mysql.inc";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método inválido.']);
    exit;
}

$ctp_id = isset($_POST['ctp_id']) ? intval($_POST['ctp_id']) : 0;

if ($ctp_id <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID da parcela inválido.']);
    exit;
}

$nomeusuario  = $_SESSION['nome_usuario'] ?? 'Sistema';
$data_sistema = date('Y-m-d H:i:s');
$usuario_esc  = mysqli_real_escape_string($conector, $nomeusuario);
$erros        = [];

// ── Arquivos ──
if (!empty($_FILES['anexo']['name'][0])) {
    $pasta = dirname(__DIR__) . '/uploads/ctp/';
    if (!is_dir($pasta)) { mkdir($pasta, 0755, true); }

    $total = count($_FILES['anexo']['name']);
    for ($i = 0; $i < $total; $i++) {
        if ($_FILES['anexo']['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (empty($_FILES['anexo']['name'][$i])) continue;

        $nome_original = basename($_FILES['anexo']['name'][$i]);
        $ext           = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
        $nome_arquivo  = uniqid('ctp_', true) . '.' . $ext;
        $destino       = $pasta . $nome_arquivo;
        $tamanho       = $_FILES['anexo']['size'][$i];

        if (!move_uploaded_file($_FILES['anexo']['tmp_name'][$i], $destino)) {
            $erros[] = 'Erro ao mover arquivo: ' . $nome_original;
            continue;
        }

        $nome_esc = mysqli_real_escape_string($conector, $nome_original);
        $arq_esc  = mysqli_real_escape_string($conector, $nome_arquivo);

        $sql = "INSERT INTO tbl_ctp_anexos
                    (anexo_ctp_id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por)
                VALUES
                    ('$ctp_id', '$nome_esc', '$arq_esc', '$tamanho', '$data_sistema', '$usuario_esc')";
        if (!mysqli_query($conector, $sql)) {
            $erros[] = 'Erro BD: ' . mysqli_error($conector);
        }
    }
}

// ── Links ──
$links_url  = isset($_POST['anexo_link_url'])  ? $_POST['anexo_link_url']  : [];
$links_desc = isset($_POST['anexo_link_desc']) ? $_POST['anexo_link_desc'] : [];

foreach ($links_url as $i => $url) {
    $url = trim($url);
    if (empty($url)) continue;

    $desc = trim($links_desc[$i] ?? '');
    if (empty($desc)) $desc = $url;

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $erros[] = 'URL inválida: ' . htmlspecialchars($url);
        continue;
    }

    $url_esc  = mysqli_real_escape_string($conector, $url);
    $desc_esc = mysqli_real_escape_string($conector, $desc);

    $sql = "INSERT INTO tbl_ctp_anexos
                (anexo_ctp_id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por)
            VALUES
                ('$ctp_id', '$desc_esc', '$url_esc', 0, '$data_sistema', '$usuario_esc')";
    if (!mysqli_query($conector, $sql)) {
        $erros[] = 'Erro BD: ' . mysqli_error($conector);
    }
}

mysqli_close($conector);

if (empty($erros)) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'msg' => implode('; ', $erros)]);
}
