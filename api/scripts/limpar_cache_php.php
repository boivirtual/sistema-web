<?php
/**
 * Limpa o cache de bytecode do PHP (opcache) sem precisar reiniciar o
 * servidor — usar depois de cada deploy manual (FTP) quando o código
 * novo não estiver "pegando" sozinho no ambiente de teste.
 *
 * Não afeta usuários já conectados nem derruba nada: o próximo request de
 * cada arquivo só recompila a partir do código-fonte atual em vez de usar
 * a versão compilada guardada em memória.
 *
 * Acesso: GET .../api/scripts/limpar_cache_php.php?chave=<CHAVE>
 */

header('Content-Type: application/json; charset=utf-8');

// Chave simples só pra não deixar esse endpoint aberto pra qualquer um na
// internet — não precisa ser nada sofisticado, o pior que essa rota faz é
// forçar uma recompilação (sem risco de dado).
const CHAVE_ESPERADA = 'boivirtual-cache-2026';

if (($_GET['chave'] ?? '') !== CHAVE_ESPERADA) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Chave inválida.']);
    exit;
}

if (!function_exists('opcache_reset')) {
    echo json_encode([
        'success' => false,
        'message' => 'opcache_reset não está disponível neste servidor (opcache pode estar desligado, ou a função foi bloqueada na configuração do PHP).',
    ]);
    exit;
}

$ok = opcache_reset();

echo json_encode([
    'success' => $ok,
    'message' => $ok
        ? 'Cache do PHP limpo com sucesso — os próximos acessos já usam o código mais recente.'
        : 'Não foi possível limpar o cache.',
]);
