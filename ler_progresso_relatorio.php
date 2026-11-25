<?php
// Devolve o andamento da geração de um Excel (gravado por gravar_progresso() no script do relatório).
// Não abre sessão de propósito: a sessão fica travada pelo script que está gerando o Excel,
// e esta consulta precisa responder enquanto ele ainda está rodando.

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!ctype_digit($token)) {
    echo json_encode(['percentual' => 0, 'etapa' => '']);
    exit;
}

$arquivo = sys_get_temp_dir() . '/boivirtual_progresso_' . $token . '.json';

if (is_file($arquivo)) {
    echo file_get_contents($arquivo);
}
else {
    echo json_encode(['percentual' => 0, 'etapa' => 'Iniciando...']);
}
