<?php
// Devolve o andamento de um relatório (gravado por gravar_progresso() no script do relatório).
// Não abre sessão de propósito: a sessão fica travada pelo script que está gerando o relatório,
// e esta consulta precisa responder enquanto ele ainda está rodando.

include_once "funcao_progresso_relatorio.php";

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!ctype_digit($token)) {
    echo json_encode(['percentual' => 0, 'etapa' => '']);
    exit;
}

$arquivo = arquivo_progresso($token);

if (is_file($arquivo)) {
    echo file_get_contents($arquivo);
}
else {
    echo json_encode(['percentual' => 0, 'etapa' => 'Iniciando...']);
}
