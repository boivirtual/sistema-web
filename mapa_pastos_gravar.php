<?php
include "conecta_mysql.inc";
include "mapa_pastos_acesso.php";
include "funcao_area_geodesica.php";

@ session_start();
header('Content-type: application/json; charset=utf-8');

function resposta_erro($mensagem) {
    echo json_encode(array('error' => true, 'message' => $mensagem), JSON_UNESCAPED_UNICODE);
    exit;
}

function normalizar_anel($anel) {
    $saida = array();
    foreach ($anel as $ponto) {
        $saida[] = array(round((float)$ponto[0], 9), round((float)$ponto[1], 9));
    }
    return json_encode($saida);
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

if (!preg_match('/^[0-9]{1,12}$/', $local)) {
    resposta_erro('Selecione a Fazenda.');
}

if (!usuario_pode_local($conector_acesso, $local)) {
    resposta_erro('Você não tem acesso a essa Fazenda.');
}

$versao_recebida = isset($_POST['versao']) ? $_POST['versao'] : '';
$novo = json_decode(isset($_POST['geojson']) ? $_POST['geojson'] : '', true);
$renomeados = json_decode(isset($_POST['renomeados']) ? $_POST['renomeados'] : '[]', true);

if (!is_array($novo) || !isset($novo['features']) || !is_array($novo['features'])) {
    resposta_erro('Os dados do mapa enviados são inválidos.');
}

if (!is_array($renomeados)) {
    $renomeados = array();
}

$pasta = __DIR__ . '/mapa/' . $cnpj_cliente;
$arquivo = $pasta . '/' . $local . '.json';

// Impede sobrescrever alteracao feita por outra pessoa desde que o mapa foi carregado
$conteudo_atual = file_exists($arquivo) ? file_get_contents($arquivo) : '';
$versao_atual = $conteudo_atual === '' ? 'novo' : md5($conteudo_atual);

if ($versao_recebida !== $versao_atual) {
    resposta_erro('O mapa dessa fazenda foi alterado por outra pessoa desde que você o carregou. Feche e abra o editor novamente para não perder o trabalho dela.');
}

// Poligonos atuais (antes da gravacao), para saber quais tiveram o traçado alterado
$antigos = array();

if ($conteudo_atual !== '') {
    $json_antigo = json_decode($conteudo_atual, true);

    if (isset($json_antigo['features'])) {
        foreach ($json_antigo['features'] as $f) {
            if (isset($f['geometry']['type']) && $f['geometry']['type'] == 'Polygon') {
                $chave = mb_strtoupper(trim($f['properties']['name']), 'UTF-8');
                $antigos[$chave] = normalizar_anel($f['geometry']['coordinates'][0]);
            }
        }
    }
}

// Validacao e limpeza das feicoes recebidas
$features = array();
$poligonos = array();
$nomes_vistos = array();

foreach ($novo['features'] as $f) {
    if (!isset($f['geometry']['type']) || !isset($f['geometry']['coordinates'])) {
        resposta_erro('Existe um item do mapa sem geometria.');
    }

    $nome = isset($f['properties']['name']) ? trim($f['properties']['name']) : '';
    $tipo = $f['geometry']['type'];
    $coords = $f['geometry']['coordinates'];

    if ($tipo == 'Point') {
        if (!is_array($coords) || count($coords) < 2 || !is_numeric($coords[0]) || !is_numeric($coords[1])) {
            resposta_erro('Existe um ponto com coordenadas inválidas.');
        }

        $features[] = array(
            'type' => 'Feature',
            'properties' => array('name' => $nome),
            'geometry' => array('type' => 'Point', 'coordinates' => array((float)$coords[0], (float)$coords[1], isset($coords[2]) ? (float)$coords[2] : 0))
        );
        continue;
    }

    if ($tipo != 'Polygon') {
        resposta_erro('Tipo de geometria não suportado: ' . $tipo);
    }

    if ($nome === '' || mb_strlen($nome, 'UTF-8') > 60) {
        resposta_erro('Todo pasto precisa ter um nome de até 60 caracteres.');
    }

    $chave = mb_strtoupper($nome, 'UTF-8');

    if (isset($nomes_vistos[$chave])) {
        resposta_erro('O nome "' . $nome . '" está repetido no mapa. Cada pasto precisa ter um nome diferente.');
    }

    $nomes_vistos[$chave] = true;

    if (!is_array($coords) || !isset($coords[0]) || !is_array($coords[0]) || count($coords[0]) < 4) {
        resposta_erro('O pasto "' . $nome . '" precisa ter pelo menos 3 pontos.');
    }

    $aneis = array();

    foreach ($coords as $anel) {
        $novo_anel = array();

        foreach ($anel as $ponto) {
            if (!is_array($ponto) || count($ponto) < 2 || !is_numeric($ponto[0]) || !is_numeric($ponto[1])) {
                resposta_erro('O pasto "' . $nome . '" tem coordenadas inválidas.');
            }

            $lng = (float)$ponto[0];
            $lat = (float)$ponto[1];

            if ($lng < -180 || $lng > 180 || $lat < -90 || $lat > 90) {
                resposta_erro('O pasto "' . $nome . '" tem coordenadas fora do intervalo válido.');
            }

            $novo_anel[] = array($lng, $lat, isset($ponto[2]) ? (float)$ponto[2] : 0);
        }

        $ultimo = count($novo_anel) - 1;

        if ($novo_anel[0][0] != $novo_anel[$ultimo][0] || $novo_anel[0][1] != $novo_anel[$ultimo][1]) {
            $novo_anel[] = $novo_anel[0];
        }

        $aneis[] = $novo_anel;
    }

    $features[] = array(
        'type' => 'Feature',
        'properties' => array('name' => $nome),
        'geometry' => array('type' => 'Polygon', 'coordinates' => $aneis)
    );

    $poligonos[] = array('nome' => $nome, 'chave' => $chave, 'anel' => $aneis[0]);
}

// Renomeacoes: nome antigo (em maiusculas) -> nome novo
$mapa_renomeados = array();

foreach ($renomeados as $r) {
    if (!isset($r['de']) || !isset($r['para'])) {
        continue;
    }

    $de = mb_strtoupper(trim($r['de']), 'UTF-8');
    $para = mb_strtoupper(trim($r['para']), 'UTF-8');

    if ($de !== '' && $para !== '' && $de !== $para) {
        $mapa_renomeados[$de] = $para;
    }
}

$local_sql = mysqli_real_escape_string($conector, $local);
$usuario_sql = mysqli_real_escape_string($conector, $nomeusuario);
$data_sistema = date("Y-m-d H:i:s");

$criados = array();
$atualizados = array();
$renomeados_ok = array();

mysqli_begin_transaction($conector);

// 1) Renomeia no banco os pastos que mudaram de nome no mapa
foreach ($mapa_renomeados as $de => $para) {
    $de_sql = mysqli_real_escape_string($conector, $de);
    $para_sql = mysqli_real_escape_string($conector, $para);

    $rs_de = mysqli_query($conector, "SELECT tbl_pasto_id FROM tbl_pasto
        WHERE tbl_pasto_descricao='$de_sql' AND tbl_pasto_codigo_local='$local_sql' AND tbl_pasto_lixeira=0");

    if (mysqli_num_rows($rs_de) == 0) {
        continue;
    }

    $rs_para = mysqli_query($conector, "SELECT tbl_pasto_id FROM tbl_pasto
        WHERE tbl_pasto_descricao='$para_sql' AND tbl_pasto_codigo_local='$local_sql' AND tbl_pasto_lixeira=0");

    if (mysqli_num_rows($rs_para) > 0) {
        mysqli_rollback($conector);
        resposta_erro('Já existe um pasto chamado "' . $para . '" nessa fazenda. Escolha outro nome para "' . $de . '".');
    }

    $ok = mysqli_query($conector, "UPDATE tbl_pasto SET
        tbl_pasto_descricao='$para_sql',
        tbl_pasto_alterado_em='$data_sistema',
        tbl_pasto_alterado_por='$usuario_sql'
        WHERE tbl_pasto_descricao='$de_sql' AND tbl_pasto_codigo_local='$local_sql' AND tbl_pasto_lixeira=0");

    if (!$ok) {
        $erro = mysqli_error($conector);
        mysqli_rollback($conector);
        resposta_erro('Erro ao renomear o pasto "' . $de . '": ' . $erro);
    }

    $renomeados_ok[] = $de . ' -> ' . $para;
}

$nome_antigo_de = array_flip($mapa_renomeados);

// 2) Cria os pastos que ainda nao existem e atualiza a area dos que tiveram o traçado alterado
foreach ($poligonos as $p) {
    $nome_sql = mysqli_real_escape_string($conector, $p['chave']);
    $area = area_geodesica_ha($p['anel']);

    $rs = mysqli_query($conector, "SELECT tbl_pasto_id FROM tbl_pasto
        WHERE tbl_pasto_descricao='$nome_sql' AND tbl_pasto_codigo_local='$local_sql' AND tbl_pasto_lixeira=0");

    if (mysqli_num_rows($rs) == 0) {
        if ($p['chave'] == 'ENTRADA') {
            $tipo_curral = 'E';
            $modulo = 999;
        }
        else if ($p['chave'] == 'SAIDA' || $p['chave'] == 'SAÍDA') {
            $tipo_curral = 'S';
            $modulo = 999;
        }
        else {
            $tipo_curral = '';
            $modulo = 1;
        }

        $ok = mysqli_query($conector, "INSERT INTO tbl_pasto (
                tbl_pasto_codigo_local, tbl_pasto_descricao, tbl_pasto_latitude, tbl_pasto_longitude,
                tbl_pasto_area, tbl_pasto_modulo, tbl_pasto_tipo_capim, tbl_pasto_descricao_lote,
                tbl_pasto_tipo_curral, tbl_pasto_incluido_em, tbl_pasto_incluido_por,
                tbl_pasto_alterado_em, tbl_pasto_alterado_por, tbl_pasto_lixeira,
                tbl_pasto_lixeira_em, tbl_pasto_lixeira_por, tbl_pasto_array_categoria,
                tbl_pasto_array_qtd_animais_macho, tbl_pasto_array_qtd_animais_femea,
                tbl_pasto_array_qtd_animais_ambos, tbl_pasto_data_com_animais,
                tbl_pasto_data_com_animais_anterior, tbl_pasto_data_sem_animais,
                tbl_pasto_data_sem_animais_anterior
            ) VALUES (
                '$local_sql', '$nome_sql', null, null,
                '$area', '$modulo', 0, null,
                '$tipo_curral', '$data_sistema', '$usuario_sql',
                null, null, 0,
                null, null, '001!002!003!004!005',
                '!!!!', '!!!!',
                null, '$data_sistema',
                '$data_sistema', '$data_sistema',
                '$data_sistema'
            )");

        if (!$ok) {
            $erro = mysqli_error($conector);
            mysqli_rollback($conector);
            resposta_erro('Erro ao criar o pasto "' . $p['nome'] . '": ' . $erro);
        }

        $criados[] = $p['nome'];
        continue;
    }

    $chave_antiga = isset($nome_antigo_de[$p['chave']]) ? $nome_antigo_de[$p['chave']] : $p['chave'];

    if (!isset($antigos[$chave_antiga]) || $antigos[$chave_antiga] !== normalizar_anel($p['anel'])) {
        $ok = mysqli_query($conector, "UPDATE tbl_pasto SET
            tbl_pasto_area='$area',
            tbl_pasto_alterado_em='$data_sistema',
            tbl_pasto_alterado_por='$usuario_sql'
            WHERE tbl_pasto_descricao='$nome_sql' AND tbl_pasto_codigo_local='$local_sql' AND tbl_pasto_lixeira=0");

        if (!$ok) {
            $erro = mysqli_error($conector);
            mysqli_rollback($conector);
            resposta_erro('Erro ao atualizar a área do pasto "' . $p['nome'] . '": ' . $erro);
        }

        $atualizados[] = $p['nome'] . ' (' . number_format($area, 2, ',', '.') . ' ha)';
    }
}

// 3) Backup do mapa atual e gravacao do novo arquivo
if (!is_dir($pasta) && !mkdir($pasta, 0775, true)) {
    mysqli_rollback($conector);
    resposta_erro('Não foi possível criar a pasta do mapa.');
}

if ($conteudo_atual !== '') {
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
}

$json = json_encode(array('type' => 'FeatureCollection', 'features' => $features),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$temporario = $arquivo . '.tmp';

if (file_put_contents($temporario, $json, LOCK_EX) === false || !rename($temporario, $arquivo)) {
    @unlink($temporario);
    mysqli_rollback($conector);
    resposta_erro('Não foi possível gravar o arquivo do mapa.');
}

mysqli_commit($conector);
mysqli_close($conector);

echo json_encode(array(
    'success' => true,
    'message' => 'Mapa gravado com sucesso.',
    'criados' => $criados,
    'atualizados' => $atualizados,
    'renomeados' => $renomeados_ok
), JSON_UNESCAPED_UNICODE);
?>
