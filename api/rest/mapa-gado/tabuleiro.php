<?php
/**
 * Exportação do Mapa de Gado (Tabuleiro) das fazendas do usuário, para
 * popular o cache local do aplicativo (tela Mapa de Gado funcionando
 * offline — mesmo padrão de api/rest/chuva/list.php).
 *
 * Replica os dados que ler_mapa_gados.php (sistema web) usa para montar o
 * tabuleiro, mas devolve os dados "crus" (pastos + animais no pasto +
 * faixas de categoria) em vez do HTML pronto: as contagens de bezerros/
 * fêmeas/machos dependem da idade do animal no dia de hoje, então o app
 * calcula na hora, a partir do cache — continua certo mesmo dias depois
 * sem internet.
 *
 * Entrada — JSON no corpo do POST:
 *   bd       -> nome do banco da conta                    (obrigatório)
 *   fazendas -> lista de tbl_pessoa_id das fazendas        (obrigatório)
 *
 * Saída:
 *   {
 *     "success": true,
 *     "categorias": [ {"id":1,"de":0,"ate":7}, ... ],
 *     "pastos": [
 *       {"id":46,"local":56,"descricao":"ENTRADA","modulo":999,
 *        "capim":"","categorias":"001!002!003!004!005","ordem":1}, ...
 *     ],
 *     "animais": [ {"local":56,"item":12,"pasto":46,"sexo":"F",
 *                   "nascimento":"2024-03-10"}, ... ]
 *   }
 *
 * "ordem" é a posição do pasto no tabuleiro dentro da fazenda — os pastos
 * de ENTRADA/SAÍDA (módulo 999) primeiro, depois os demais, com o MESMO
 * ORDER BY do sistema web (tbl_pasto_modulo, tbl_pasto_codigo_local), que
 * não desempata pastos do mesmo módulo; por isso a posição é calculada
 * aqui, a partir do próprio resultado do MySQL, e não reordenada no app.
 * Módulos 1006 (NÃO UTILIZADO) e 1007 (ÁREA COMUM) ficam de fora, igual
 * ao web.
 *
 * Somente leitura. `fazendas` é sempre convertido para inteiro antes de
 * entrar no SQL — nunca concatena texto vindo do cliente.
 */

require_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados) || !isset($dados['bd']) || !isset($dados['fazendas']) || !is_array($dados['fazendas'])) {
    echo json_encode([
        "success" => false,
        "message" => "Informe bd e a lista de fazendas."
    ]);
    exit;
}

$bd = trim((string) $dados['bd']);
$idsFazendas = array_values(array_unique(array_filter(
    array_map('intval', $dados['fazendas']),
    function ($id) { return $id > 0; }
)));

if ($bd === '' || count($idsFazendas) === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Parâmetros inválidos."
    ]);
    exit;
}

$con = @mysqli_connect($servidor, $usuario_bd, $senha_bd, $bd);
if (!$con) {
    echo json_encode([
        "success" => false,
        "message" => "Não foi possível conectar ao banco."
    ]);
    exit;
}
mysqli_set_charset($con, "utf8");

// Faixas de idade das categorias (mesma tabela que o web consulta para
// cada código de tbl_pasto_array_categoria).
$categorias = [];
$res = mysqli_query($con, "SELECT tab_codigo_categoria_idade AS id,
                                  tab_categoria_idade_de    AS de,
                                  tab_categoria_idade_ate   AS ate
                             FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade = '0'");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $categorias[] = [
            "id"  => (int) $row['id'],
            "de"  => (int) $row['de'],
            "ate" => (int) $row['ate'],
        ];
    }
}

// Opções de "Descrição do Lote" (montagem da descrição ao mover animais).
$descricoesLote = [];
$res = mysqli_query($con, "SELECT tbl_descricao_lote_id AS id,
                                  tbl_descricao_lote    AS descricao
                             FROM tbl_descricao_lote_animais
                            WHERE tbl_descricao_lote_lixeira = 0");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $descricoesLote[] = [
            "id"        => (int) $row['id'],
            "descricao" => (string) $row['descricao'],
        ];
    }
}

// Descrição do lote do pasto: só nas contas que já têm as colunas
// (bancos antigos só têm tbl_pasto_descricao_lote).
$colunasLote = [];
$res = mysqli_query($con, "SHOW COLUMNS FROM tbl_pasto LIKE 'tbl_pasto_descricao_lote%'");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $colunasLote[] = $row['Field'];
    }
}
$camposLote = '';
foreach ($colunasLote as $coluna) {
    $camposLote .= ", p.{$coluna}";
}

$camposPasto = "p.tbl_pasto_id              AS id,
                p.tbl_pasto_codigo_local    AS local,
                p.tbl_pasto_descricao       AS descricao,
                p.tbl_pasto_modulo          AS modulo,
                p.tbl_pasto_array_categoria AS categorias,
                c.tbl_tipo_capim_descricao  AS capim
                {$camposLote}";

$pastos = [];
$idsPastos = [];

foreach ($idsFazendas as $fazenda) {
    $ordem = 0;

    // Mesmos dois SELECTs (e na mesma sequência) de ler_mapa_gados.php.
    $consultas = [
        "SELECT {$camposPasto}
           FROM tbl_pasto p
      LEFT JOIN tbl_tipo_capim c ON c.tbl_tipo_capim_id = p.tbl_pasto_tipo_capim
          WHERE p.tbl_pasto_lixeira = 0 AND
                p.tbl_pasto_modulo = '999' AND
                p.tbl_pasto_codigo_local = '{$fazenda}'
       ORDER BY p.tbl_pasto_modulo, p.tbl_pasto_codigo_local ASC",

        "SELECT {$camposPasto}
           FROM tbl_pasto p
      LEFT JOIN tbl_tipo_capim c ON c.tbl_tipo_capim_id = p.tbl_pasto_tipo_capim
          WHERE p.tbl_pasto_lixeira = 0 AND
                p.tbl_pasto_codigo_local = {$fazenda} AND
                p.tbl_pasto_modulo != '999' AND
                p.tbl_pasto_modulo != '1006' AND
                p.tbl_pasto_modulo != '1007'
       ORDER BY p.tbl_pasto_modulo, p.tbl_pasto_codigo_local ASC",
    ];

    foreach ($consultas as $sql) {
        $res = mysqli_query($con, $sql);
        if (!$res) {
            continue;
        }
        while ($row = mysqli_fetch_assoc($res)) {
            $ordem++;
            $idsPastos[] = (int) $row['id'];
            $lotes = [];
            for ($i = 1; $i <= 6; $i++) {
                $lotes[] = (string) ($row["tbl_pasto_descricao_lote_{$i}"] ?? '');
            }
            $pastos[] = [
                "id"             => (int) $row['id'],
                "local"          => (int) $row['local'],
                "descricao"      => (string) $row['descricao'],
                "modulo"         => (int) $row['modulo'],
                "capim"          => (string) ($row['capim'] ?? ''),
                "categorias"     => (string) ($row['categorias'] ?? ''),
                "ordem"          => $ordem,
                "descricao_lote" => (string) ($row['tbl_pasto_descricao_lote'] ?? ''),
                "lotes"          => $lotes,
            ];
        }
    }
}

// Animais ativos de todos os pastos acima numa consulta só (o web faz uma
// por pasto). Mesmo filtro do web: só pelo pasto + situação 'A'.
$animais = [];
if (count($idsPastos) > 0) {
    $idsSql = implode(',', $idsPastos);
    $res = mysqli_query($con, "SELECT tbl_animal_pasto_local       AS local,
                                      tbl_animal_pasto_numero_item AS item,
                                      tbl_animal_pasto_id          AS pasto,
                                      tbl_animal_pasto_sexo        AS sexo,
                                      tbl_animal_pasto_nascimento  AS nascimento
                                 FROM tbl_animal_pasto
                                WHERE tbl_animal_pasto_id IN ({$idsSql}) AND
                                      tbl_animal_pasto_situacao = 'A'");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $animais[] = [
                "local"      => (int) $row['local'],
                "item"       => (int) $row['item'],
                "pasto"      => (int) $row['pasto'],
                "sexo"       => (string) $row['sexo'],
                "nascimento" => $row['nascimento'],
            ];
        }
    }
}

mysqli_close($con);

echo json_encode([
    "success"         => true,
    "categorias"      => $categorias,
    "descricoes_lote" => $descricoesLote,
    "pastos"     => $pastos,
    "animais"    => $animais,
]);
