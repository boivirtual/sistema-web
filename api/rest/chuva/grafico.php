<?php
/**
 * Agrega o volume de chuva para os gráficos do aplicativo.
 *
 * Devolve exatamente a mesma informação dos dois gráficos da home do
 * sistema web (form_lista_registro_chuva_rel_dashboard.php):
 *   1) Precipitação x dias chuvosos por mês, no ano de referência.
 *   2) Precipitação x dias chuvosos por ano, nos últimos 5 anos.
 *
 * Entrada — JSON no corpo do POST (mesmo padrão dos endpoints de pesagem):
 *   bd    -> nome do banco da conta                (obrigatório)
 *   local -> tbl_pessoa_id da fazenda              (obrigatório)
 *   ano   -> ano de referência do gráfico mensal   (opcional; padrão: ano atual)
 *
 * Saída — JSON:
 *   {
 *     "error": false,
 *     "ano": 2026,
 *     "meses": [ {"mes":1,"mm":180,"dias":6}, ... sempre 12 itens (jan..dez) ],
 *     "anos":  [ {"ano":2022,"mm":1400,"dias":62}, ... sempre 5 itens (ano-4..ano) ]
 *   }
 *
 * Somente leitura. Campos explicitados (sem SELECT *) e parâmetros tratados
 * como inteiros, conforme CLAUDE.md. `local` e `ano` são convertidos com
 * (int) antes de entrar no SQL — não há string de usuário concatenada.
 *
 * Observação: a contagem de "dia chuvoso" segue a home — registro com
 * volume > 0. Não há filtro por tbl_chuva_lixeira porque o módulo de chuva
 * grava com DELETE físico (não usa lixeira lógica), e a home também não
 * filtra; incluir o filtro poderia divergir do que o sistema web mostra.
 */

require_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

$bd    = isset($dados['bd']) ? trim((string) $dados['bd']) : '';
$local = isset($dados['local']) ? (int) $dados['local'] : 0;
$ano   = (isset($dados['ano']) && (int) $dados['ano'] > 0)
    ? (int) $dados['ano']
    : (int) date('Y');

if ($bd === '' || $local === 0) {
    echo json_encode([
        "error"   => true,
        "message" => "Informe o banco (bd) e o local.",
    ]);
    exit;
}

$con = @mysqli_connect($servidor, $usuario_bd, $senha_bd, $bd);
if (!$con) {
    echo json_encode([
        "error"   => true,
        "message" => "Não foi possível conectar ao banco.",
    ]);
    exit;
}
mysqli_set_charset($con, "utf8");

$anoInicial = $ano - 4;

// ------------------------------------------------------------------
// Gráfico mensal — ano de referência
// ------------------------------------------------------------------
$meses = [];
for ($m = 1; $m <= 12; $m++) {
    $meses[$m] = ["mes" => $m, "mm" => 0, "dias" => 0];
}

$sqlMes = "SELECT MONTH(tbl_chuva_data)      AS mes,
                  SUM(tbl_chuva_volume_chuva) AS mm,
                  COUNT(*)                    AS dias
             FROM tbl_chuva
            WHERE tbl_chuva_local = {$local}
              AND YEAR(tbl_chuva_data) = {$ano}
              AND tbl_chuva_volume_chuva > 0
         GROUP BY MONTH(tbl_chuva_data)";

$resMes = mysqli_query($con, $sqlMes);
if ($resMes) {
    while ($row = mysqli_fetch_assoc($resMes)) {
        $m = (int) $row['mes'];
        $meses[$m] = [
            "mes"  => $m,
            "mm"   => 0 + $row['mm'],
            "dias" => (int) $row['dias'],
        ];
    }
}

// ------------------------------------------------------------------
// Gráfico anual — últimos 5 anos (ano-4 .. ano)
// ------------------------------------------------------------------
$anos = [];
for ($a = $anoInicial; $a <= $ano; $a++) {
    $anos[$a] = ["ano" => $a, "mm" => 0, "dias" => 0];
}

$sqlAno = "SELECT YEAR(tbl_chuva_data)       AS ano,
                  SUM(tbl_chuva_volume_chuva) AS mm,
                  COUNT(*)                    AS dias
             FROM tbl_chuva
            WHERE tbl_chuva_local = {$local}
              AND YEAR(tbl_chuva_data) BETWEEN {$anoInicial} AND {$ano}
              AND tbl_chuva_volume_chuva > 0
         GROUP BY YEAR(tbl_chuva_data)";

$resAno = mysqli_query($con, $sqlAno);
if ($resAno) {
    while ($row = mysqli_fetch_assoc($resAno)) {
        $a = (int) $row['ano'];
        $anos[$a] = [
            "ano"  => $a,
            "mm"   => 0 + $row['mm'],
            "dias" => (int) $row['dias'],
        ];
    }
}

mysqli_close($con);

echo json_encode([
    "error" => false,
    "ano"   => $ano,
    "meses" => array_values($meses),
    "anos"  => array_values($anos),
], JSON_UNESCAPED_UNICODE);
