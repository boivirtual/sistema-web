<?php
/**
 * Exportação em massa do registro de chuva das fazendas do usuário, para
 * popular o cache local do aplicativo (tela de Chuva funcionando offline —
 * mesmo padrão do cadastro de animais, ver
 * api/rest/animal/list_fazenda_completo.php).
 *
 * Entrada — JSON no corpo do POST:
 *   bd       -> nome do banco da conta                    (obrigatório)
 *   fazendas -> lista de tbl_pessoa_id das fazendas        (obrigatório)
 *
 * Só traz os últimos 5 anos: é tudo que a tela chega a exibir (o gráfico
 * anual mostra o ano atual e os 4 anteriores), sem sentido baixar pro
 * aparelho um histórico que nunca é exibido.
 *
 * Saída:
 *   {
 *     "success": true,
 *     "chuvas": [ {"id":1,"local":57,"data":"2026-01-15","volume":12.5}, ... ]
 *   }
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
$idsFazendas = array_values(array_filter(
    array_map('intval', $dados['fazendas']),
    function ($id) { return $id > 0; }
));

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

$idsSql = implode(',', $idsFazendas);
$anoLimite = (int) date('Y') - 5;

$sql = "SELECT tbl_chuva_id           AS id,
               tbl_chuva_local        AS local,
               tbl_chuva_data         AS data,
               tbl_chuva_volume_chuva AS volume
          FROM tbl_chuva
         WHERE tbl_chuva_local IN ({$idsSql})
           AND YEAR(tbl_chuva_data) >= {$anoLimite}
      ORDER BY tbl_chuva_data ASC";

$res = mysqli_query($con, $sql);
$lista = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $lista[] = [
            "id"     => (int) $row['id'],
            "local"  => (int) $row['local'],
            "data"   => $row['data'],
            "volume" => (float) $row['volume'],
        ];
    }
}

mysqli_close($con);

echo json_encode([
    "success" => true,
    "chuvas"  => $lista,
]);
