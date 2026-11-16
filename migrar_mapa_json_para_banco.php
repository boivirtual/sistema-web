<?php
// Cria as tabelas do mapa (tbl_mapa_fazenda e tbl_mapa_fazenda_historico) no banco do cliente
// logado e carrega nelas os arquivos mapa/{cnpj}/*.json existentes.
// Pode ser executado varias vezes: fazendas que ja estao no banco nao sao sobrescritas.
include "valida_sessao.inc";
include "conecta_mysql.inc";
include "funcao_mapa_fazenda.php";

@ session_start();

if (!isset($_SESSION['menu_parametros'])) {
    exit('Você não efetuou o login.');
}

$acesso = explode("!", $_SESSION['menu_parametros']);

if ($acesso[3] == 0) {
    exit('Você não tem acesso a esse programa.');
}

$cnpj = $_SESSION['id_cliente'];
$pasta = __DIR__ . '/mapa/' . $cnpj;
$usuario = $_SESSION['nome_usuario'];
$acao = (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao'])) ? $_POST['acao'] : '';

$relatorio = array();

function nome_fazenda($conector, $local) {
    $local_sql = mysqli_real_escape_string($conector, $local);
    $rs = mysqli_query($conector, "SELECT tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id='$local_sql'");

    if ($rs && $reg = mysqli_fetch_object($rs)) {
        return $reg->tbl_pessoa_nome;
    }

    return '(fazenda não encontrada)';
}

function arquivos_mapa($pasta) {
    $lista = array();

    foreach (glob($pasta . '/*.json') as $arq) {
        $nome = basename($arq, '.json');

        if (preg_match('/^[0-9]{1,12}$/', $nome)) {
            $lista[$nome] = $arq;
        }
    }

    ksort($lista);

    return $lista;
}

if ($acao == 'migrar') {
    mapa_criar_tabelas($conector);

    // o cache de "tabelas existem" so vale para consultas anteriores; refaz a checagem
    $tabelas_ok = false;
    $r1 = mysqli_query($conector, "SHOW TABLES LIKE 'tbl_mapa_fazenda'");
    $r2 = mysqli_query($conector, "SHOW TABLES LIKE 'tbl_mapa_fazenda_historico'");
    $tabelas_ok = ($r1 && mysqli_num_rows($r1) > 0 && $r2 && mysqli_num_rows($r2) > 0);

    if (!$tabelas_ok) {
        $relatorio[] = array('erro', 'Não foi possível criar as tabelas. Verifique a permissão do usuário do banco.');
    }
    else {
        foreach (arquivos_mapa($pasta) as $local => $arq) {
            $rotulo = $local . ' - ' . nome_fazenda($conector, $local);
            $geojson = json_decode(file_get_contents($arq), true);

            if (!is_array($geojson) || !isset($geojson['features'])) {
                $relatorio[] = array('erro', $rotulo . ': arquivo inválido, não foi carregado.');
                continue;
            }

            $local_sql = mysqli_real_escape_string($conector, $local);
            $rs = mysqli_query($conector, "SELECT tbl_mapa_local FROM tbl_mapa_fazenda WHERE tbl_mapa_local='$local_sql'");

            if ($rs && mysqli_num_rows($rs) > 0) {
                $relatorio[] = array('aviso', $rotulo . ': já estava no banco, não foi alterado.');
                continue;
            }

            $json_banco = json_encode($geojson, JSON_UNESCAPED_SLASHES);
            $json_sql = mysqli_real_escape_string($conector, $json_banco);
            $versao = md5($json_banco);
            $usuario_sql = mysqli_real_escape_string($conector, $usuario);
            $agora = date('Y-m-d H:i:s');

            $ok = mysqli_query($conector, "INSERT INTO tbl_mapa_fazenda (
                    tbl_mapa_local, tbl_mapa_geojson, tbl_mapa_versao, tbl_mapa_alterado_em, tbl_mapa_alterado_por
                ) VALUES ('$local_sql', '$json_sql', '$versao', '$agora', '$usuario_sql')");

            $qtd = count($geojson['features']);
            $relatorio[] = $ok ? array('ok', $rotulo . ': carregado (' . $qtd . ' itens no mapa).')
                               : array('erro', $rotulo . ': erro ao gravar - ' . mysqli_error($conector));
        }

        if (count($relatorio) == 0) {
            $relatorio[] = array('aviso', 'Não há arquivos .json em mapa/' . $cnpj . '.');
        }
    }
}
else if ($acao == 'regenerar') {
    if (!mapa_tabelas_existem($conector)) {
        $relatorio[] = array('erro', 'As tabelas do mapa ainda não existem. Execute primeiro a carga.');
    }
    else {
        $rs = mysqli_query($conector, "SELECT tbl_mapa_local, tbl_mapa_geojson FROM tbl_mapa_fazenda ORDER BY tbl_mapa_local");

        while ($rs && $reg = mysqli_fetch_object($rs)) {
            $local = $reg->tbl_mapa_local;
            $rotulo = $local . ' - ' . nome_fazenda($conector, $local);
            $geojson = json_decode($reg->tbl_mapa_geojson, true);
            $arq = mapa_caminho_arquivo($cnpj, $local);

            $novo = json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if (file_exists($arq) && file_get_contents($arq) === $novo) {
                $relatorio[] = array('aviso', $rotulo . ': arquivo já estava igual ao banco.');
                continue;
            }

            $erro = mapa_gravar_arquivo($cnpj, $local, $geojson);
            $relatorio[] = ($erro === '') ? array('ok', $rotulo . ': arquivo regenerado.') : array('erro', $rotulo . ': ' . $erro);
        }
    }
}

// Situacao atual
$tabelas = mapa_tabelas_existem($conector);
$no_banco = array();

if ($tabelas) {
    $rs = mysqli_query($conector, "SELECT tbl_mapa_local, tbl_mapa_alterado_em FROM tbl_mapa_fazenda");

    while ($rs && $reg = mysqli_fetch_object($rs)) {
        $no_banco[$reg->tbl_mapa_local] = $reg->tbl_mapa_alterado_em;
    }
}

$arquivos = arquivos_mapa($pasta);
$todos = array_unique(array_merge(array_keys($arquivos), array_keys($no_banco)));
sort($todos);

function h($t) {
    return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Mapa das fazendas - Migração para o banco</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="padding: 20px;">
<div class="container">
    <h3>Mapa das fazendas: arquivos JSON &rarr; banco de dados</h3>
    <p>Cliente: <strong><?php echo h($cnpj); ?></strong> &nbsp;|&nbsp;
       Tabelas do mapa: <strong><?php echo $tabelas ? 'já existem' : 'ainda não existem'; ?></strong></p>

    <?php foreach ($relatorio as $linha) {
        $classe = array('ok' => 'success', 'aviso' => 'warning', 'erro' => 'danger')[$linha[0]]; ?>
        <div class="alert alert-<?php echo $classe; ?>" style="padding: 6px 12px; margin-bottom: 4px;"><?php echo h($linha[1]); ?></div>
    <?php } ?>

    <table class="table table-bordered table-condensed" style="margin-top: 15px;">
        <thead><tr><th>Fazenda</th><th>Arquivo JSON</th><th>No banco</th></tr></thead>
        <tbody>
        <?php foreach ($todos as $local) { ?>
            <tr>
                <td><?php echo h($local . ' - ' . nome_fazenda($conector, $local)); ?></td>
                <td><?php echo isset($arquivos[$local]) ? 'sim' : 'não'; ?></td>
                <td><?php echo isset($no_banco[$local]) ? 'sim (' . h($no_banco[$local]) . ')' : 'não'; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <form method="post" style="display: inline;">
        <input type="hidden" name="acao" value="migrar">
        <button type="submit" class="btn btn-primary">Criar tabelas e carregar os JSON no banco</button>
    </form>

    <form method="post" style="display: inline; margin-left: 10px;">
        <input type="hidden" name="acao" value="regenerar">
        <button type="submit" class="btn btn-info">Regenerar arquivos JSON a partir do banco</button>
    </form>

    <p class="text-muted" style="margin-top: 15px;">
        A carga não altera fazendas que já estão no banco e não apaga nenhum arquivo.
        Depois dela, o editor de mapa passa a gravar no banco (com histórico das últimas 50 versões)
        e o arquivo JSON continua sendo gerado a cada gravação.
    </p>
</div>
</body>
</html>
