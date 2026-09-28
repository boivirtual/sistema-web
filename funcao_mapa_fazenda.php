<?php
// Mapa (GeoJSON) das fazendas: fonte da verdade no banco (tbl_mapa_fazenda) com o arquivo
// mapa/{cnpj}/{fazenda}.json sendo regenerado a cada gravacao. Em bancos que ainda nao tem as
// tabelas, tudo continua funcionando so com o arquivo, como antes.

function mapa_caminho_arquivo($cnpj, $local) {
    return __DIR__ . '/mapa/' . $cnpj . '/' . $local . '.json';
}

function mapa_tabelas_existem($conector) {
    static $cache = array();
    $chave = spl_object_id($conector);

    if (!isset($cache[$chave])) {
        try {
            $r1 = mysqli_query($conector, "SHOW TABLES LIKE 'tbl_mapa_fazenda'");
            $r2 = mysqli_query($conector, "SHOW TABLES LIKE 'tbl_mapa_fazenda_historico'");
            $cache[$chave] = ($r1 && mysqli_num_rows($r1) > 0 && $r2 && mysqli_num_rows($r2) > 0);
        }
        catch (Exception $e) {
            $cache[$chave] = false;
        }
    }

    return $cache[$chave];
}

function mapa_criar_tabelas($conector) {
    mysqli_query($conector, "CREATE TABLE IF NOT EXISTS tbl_mapa_fazenda (
        tbl_mapa_local VARCHAR(12) NOT NULL,
        tbl_mapa_geojson LONGTEXT NOT NULL,
        tbl_mapa_versao CHAR(32) NOT NULL,
        tbl_mapa_alterado_em DATETIME NULL,
        tbl_mapa_alterado_por VARCHAR(60) NULL,
        PRIMARY KEY (tbl_mapa_local)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    mysqli_query($conector, "CREATE TABLE IF NOT EXISTS tbl_mapa_fazenda_historico (
        tbl_mapa_hist_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        tbl_mapa_hist_local VARCHAR(12) NOT NULL,
        tbl_mapa_hist_geojson LONGTEXT NOT NULL,
        tbl_mapa_hist_versao CHAR(32) NOT NULL,
        tbl_mapa_hist_gravado_em DATETIME NULL,
        tbl_mapa_hist_gravado_por VARCHAR(60) NULL,
        tbl_mapa_hist_origem VARCHAR(20) NULL,
        PRIMARY KEY (tbl_mapa_hist_id),
        KEY idx_mapa_hist_local (tbl_mapa_hist_local, tbl_mapa_hist_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Le o mapa atual: banco (se ja migrado) ou arquivo. versao = md5 do conteudo lido.
function mapa_ler($conector, $cnpj, $local) {
    if (mapa_tabelas_existem($conector)) {
        $local_sql = mysqli_real_escape_string($conector, $local);
        $rs = mysqli_query($conector, "SELECT tbl_mapa_geojson FROM tbl_mapa_fazenda WHERE tbl_mapa_local='$local_sql'");

        if ($rs && mysqli_num_rows($rs) > 0) {
            $json = mysqli_fetch_row($rs)[0];
            return array('json' => $json, 'versao' => md5($json), 'origem' => 'banco');
        }
    }

    $arquivo = mapa_caminho_arquivo($cnpj, $local);

    if (file_exists($arquivo)) {
        $conteudo = file_get_contents($arquivo);
        return array('json' => $conteudo, 'versao' => md5($conteudo), 'origem' => 'arquivo');
    }

    return array('json' => '', 'versao' => 'novo', 'origem' => 'nenhuma');
}

function mapa_gravar_arquivo($cnpj, $local, $geojson) {
    $pasta = __DIR__ . '/mapa/' . $cnpj;

    if (!is_dir($pasta) && !mkdir($pasta, 0775, true)) {
        return 'Não foi possível criar a pasta do mapa.';
    }

    $arquivo = mapa_caminho_arquivo($cnpj, $local);
    $temporario = $arquivo . '.tmp';
    $json = json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (file_put_contents($temporario, $json, LOCK_EX) === false || !rename($temporario, $arquivo)) {
        @unlink($temporario);
        return 'Não foi possível gravar o arquivo do mapa.';
    }

    return '';
}

// Grava o mapa (array GeoJSON). Devolve '' se deu certo ou a mensagem de erro.
// Nao faz commit: quem chama, se estiver dentro de uma transacao, faz o commit/rollback.
function mapa_gravar($conector, $cnpj, $local, $geojson, $usuario, $origem) {
    $atual = mapa_ler($conector, $cnpj, $local);

    if (mapa_tabelas_existem($conector)) {
        try {
            $json_banco = json_encode($geojson, JSON_UNESCAPED_SLASHES);
            $versao = md5($json_banco);
            $local_sql = mysqli_real_escape_string($conector, $local);
            $usuario_sql = mysqli_real_escape_string($conector, $usuario);
            $origem_sql = mysqli_real_escape_string($conector, $origem);
            $agora = date('Y-m-d H:i:s');

            if ($atual['json'] !== '') {
                $anterior_sql = mysqli_real_escape_string($conector, $atual['json']);
                $versao_anterior = $atual['versao'];

                mysqli_query($conector, "INSERT INTO tbl_mapa_fazenda_historico (
                        tbl_mapa_hist_local, tbl_mapa_hist_geojson, tbl_mapa_hist_versao,
                        tbl_mapa_hist_gravado_em, tbl_mapa_hist_gravado_por, tbl_mapa_hist_origem
                    ) VALUES ('$local_sql', '$anterior_sql', '$versao_anterior', '$agora', '$usuario_sql', '$origem_sql')");

                mysqli_query($conector, "DELETE FROM tbl_mapa_fazenda_historico
                    WHERE tbl_mapa_hist_local='$local_sql' AND tbl_mapa_hist_id NOT IN (
                        SELECT tbl_mapa_hist_id FROM (
                            SELECT tbl_mapa_hist_id FROM tbl_mapa_fazenda_historico
                            WHERE tbl_mapa_hist_local='$local_sql'
                            ORDER BY tbl_mapa_hist_id DESC LIMIT 50
                        ) ultimos
                    )");
            }

            $json_sql = mysqli_real_escape_string($conector, $json_banco);

            mysqli_query($conector, "INSERT INTO tbl_mapa_fazenda (
                    tbl_mapa_local, tbl_mapa_geojson, tbl_mapa_versao, tbl_mapa_alterado_em, tbl_mapa_alterado_por
                ) VALUES ('$local_sql', '$json_sql', '$versao', '$agora', '$usuario_sql')
                ON DUPLICATE KEY UPDATE
                    tbl_mapa_geojson=VALUES(tbl_mapa_geojson),
                    tbl_mapa_versao=VALUES(tbl_mapa_versao),
                    tbl_mapa_alterado_em=VALUES(tbl_mapa_alterado_em),
                    tbl_mapa_alterado_por=VALUES(tbl_mapa_alterado_por)");
        }
        catch (Exception $e) {
            return 'Erro ao gravar o mapa no banco: ' . $e->getMessage();
        }
    }
    else if ($atual['json'] !== '') {
        // Banco sem as tabelas: mantem o backup em arquivo, como antes
        $pasta_backup = __DIR__ . '/mapa/' . $cnpj . '/backup';

        if (!is_dir($pasta_backup)) {
            mkdir($pasta_backup, 0775, true);
        }

        file_put_contents($pasta_backup . '/' . $local . '_' . date('Ymd_His') . '.json', $atual['json']);

        $backups = glob($pasta_backup . '/' . $local . '_*.json');
        sort($backups);

        while (count($backups) > 30) {
            unlink(array_shift($backups));
        }
    }

    return mapa_gravar_arquivo($cnpj, $local, $geojson);
}
?>
