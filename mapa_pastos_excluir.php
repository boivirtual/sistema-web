<?php
include "conecta_mysql.inc";
include "mapa_pastos_acesso.php";

@ session_start();
header('Content-type: application/json; charset=utf-8');

function resposta_erro($mensagem) {
    echo json_encode(array('error' => true, 'message' => $mensagem), JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['menu_parametros'])) {
    resposta_erro('Você não efetuou o login.');
}

$acesso = explode("!", $_SESSION['menu_parametros']);

if ($acesso[3] == 0) {
    resposta_erro('Você não tem acesso a esse programa.');
}

$cnpj_cliente = $_SESSION['id_cliente'];
$nomeusuario = $_SESSION['nome_usuario'];
$local = isset($_POST['local']) ? $_POST['local'] : '';
$nome = isset($_POST['nome']) ? mb_strtoupper(trim($_POST['nome']), 'UTF-8') : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';
$versao_recebida = isset($_POST['versao']) ? $_POST['versao'] : '';

if (!preg_match('/^[0-9]{1,12}$/', $local) || $nome === '') {
    resposta_erro('Dados inválidos para excluir o pasto.');
}

if (!usuario_pode_local($conector_acesso, $local)) {
    resposta_erro('Você não tem acesso a essa Fazenda.');
}

if ($senha === '' || !isset($_SESSION['senha_usuario']) || !hash_equals((string)$_SESSION['senha_usuario'], (string)$senha)) {
    resposta_erro('Senha incorreta.');
}

if ($nome == 'ENTRADA' || $nome == 'SAIDA' || $nome == 'SAÍDA') {
    resposta_erro('Os pastos ENTRADA e SAIDA não podem ser excluídos.');
}

$pasta = __DIR__ . '/mapa/' . $cnpj_cliente;
$arquivo = $pasta . '/' . $local . '.json';

$conteudo_atual = file_exists($arquivo) ? file_get_contents($arquivo) : '';
$versao_atual = $conteudo_atual === '' ? 'novo' : md5($conteudo_atual);

if ($versao_recebida !== $versao_atual) {
    resposta_erro('O mapa dessa fazenda foi alterado por outra pessoa desde que você o carregou. Feche e abra o editor novamente.');
}

$local_sql = mysqli_real_escape_string($conector, $local);
$nome_sql = mysqli_real_escape_string($conector, $nome);
$usuario_sql = mysqli_real_escape_string($conector, $nomeusuario);
$data_sistema = date("Y-m-d H:i:s");

$rs = mysqli_query($conector, "SELECT tbl_pasto_id, tbl_pasto_modulo FROM tbl_pasto
    WHERE tbl_pasto_descricao='$nome_sql' AND tbl_pasto_codigo_local='$local_sql' AND tbl_pasto_lixeira=0");

mysqli_begin_transaction($conector);

if (mysqli_num_rows($rs) > 0) {
    $pasto = mysqli_fetch_object($rs);
    $pasto_id = (int)$pasto->tbl_pasto_id;

    if ($pasto->tbl_pasto_modulo == 999) {
        mysqli_rollback($conector);
        resposta_erro('Os pastos ENTRADA e SAIDA não podem ser excluídos.');
    }

    $rs_animais = mysqli_query($conector, "SELECT COUNT(*) AS qtd FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id=$pasto_id AND tbl_animal_pasto_situacao='A'");
    $qtd = (int)mysqli_fetch_object($rs_animais)->qtd;

    if ($qtd > 0) {
        mysqli_rollback($conector);
        resposta_erro('O pasto ' . $nome . ' ainda tem animais. Transfira todos os animais para outro pasto antes de excluir.');
    }

    $ok = mysqli_query($conector, "UPDATE tbl_pasto SET
        tbl_pasto_lixeira=1,
        tbl_pasto_lixeira_em='$data_sistema',
        tbl_pasto_lixeira_por='$usuario_sql'
        WHERE tbl_pasto_id=$pasto_id");

    if (!$ok) {
        $erro = mysqli_error($conector);
        mysqli_rollback($conector);
        resposta_erro('Erro ao excluir o pasto: ' . $erro);
    }
}

// Remove o poligono do mapa (com backup do arquivo anterior)
if ($conteudo_atual !== '') {
    $json = json_decode($conteudo_atual, true);
    $restantes = array();

    foreach ($json['features'] as $f) {
        $eh_pasto = isset($f['geometry']['type']) && $f['geometry']['type'] == 'Polygon' &&
            mb_strtoupper(trim($f['properties']['name']), 'UTF-8') == $nome;

        if (!$eh_pasto) {
            $restantes[] = $f;
        }
    }

    $json['features'] = $restantes;

    $pasta_backup = $pasta . '/backup';

    if (!is_dir($pasta_backup)) {
        mkdir($pasta_backup, 0775, true);
    }

    file_put_contents($pasta_backup . '/' . $local . '_' . date('Ymd_His') . '.json', $conteudo_atual);

    $backups = glob($pasta_backup . '/' . $local . '_*.json');
    sort($backups);

    while (count($backups) > 30) {
        unlink(array_shift($backups));
    }

    $temporario = $arquivo . '.tmp';
    $novo_conteudo = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (file_put_contents($temporario, $novo_conteudo, LOCK_EX) === false || !rename($temporario, $arquivo)) {
        @unlink($temporario);
        mysqli_rollback($conector);
        resposta_erro('Não foi possível gravar o arquivo do mapa.');
    }
}

mysqli_commit($conector);
mysqli_close($conector);

echo json_encode(array('success' => true, 'message' => 'Pasto ' . $nome . ' excluído com sucesso.'), JSON_UNESCAPED_UNICODE);
?>
