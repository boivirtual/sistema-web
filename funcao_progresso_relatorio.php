<?php
// Barra de progresso dos relatórios.
// O script do relatório grava o andamento num arquivo temporário identificado pelo token
// (parâmetro download_token, gerado pela tela); a tela consulta ler_progresso_relatorio.php a cada 1 segundo.
// Sem token (relatório chamado de outro lugar), as funções não fazem nada.

$token_progresso = (isset($_GET['download_token']) && ctype_digit($_GET['download_token'])) ? $_GET['download_token'] : '';

function arquivo_progresso($token) {
    return sys_get_temp_dir() . '/boivirtual_progresso_' . $token . '.json';
}

function gravar_progresso($percentual, $etapa) {
    global $token_progresso;

    if ($token_progresso != '') {
        @file_put_contents(arquivo_progresso($token_progresso), json_encode(['percentual' => (int) $percentual, 'etapa' => $etapa]));
    }
}

function remover_progresso() {
    global $token_progresso;

    if ($token_progresso != '') {
        @unlink(arquivo_progresso($token_progresso));
    }
}
