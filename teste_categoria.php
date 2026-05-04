<?php
// 1. CONFIGURAÇÃO CONFORME SEU BANCO
$arrayCategorias = [
    ["id" => 1, "idade_de" => 0,  "idade_ate" => 7,         "desc" => "0 a 7 meses"],
    ["id" => 2, "idade_de" => 8,  "idade_ate" => 12,        "desc" => "8 a 12 meses"],
    ["id" => 3, "idade_de" => 13, "idade_ate" => 24,        "desc" => "13 a 24 meses"],
    ["id" => 4, "idade_de" => 25, "idade_ate" => 36,        "desc" => "25 a 36 meses"],
    ["id" => 5, "idade_de" => 37, "idade_ate" => 999999999, "desc" => " > 36 meses"]
];

// 2. A FUNÇÃO DE BUSCA (A que você vai usar no seu sistema)
function buscarCategoria($idade, $categorias) {
    foreach ($categorias as $cat) {
        if ($idade >= $cat['idade_de'] && $idade <= $cat['idade_ate']) {
            return $cat;
        }
    }
    return ["id" => null, "desc" => "Idade não encontrada"];
}

// 3. TESTE DE VALIDAÇÃO
$testes = [0, 7, 8, 12, 13, 24, 25, 36, 37, 50];

echo "<h3>Validando Regras do Banco de Dados</h3>";
echo "<table border='1' style='border-collapse: collapse; font-family: sans-serif;'>
        <tr style='background: #333; color: white;'>
            <th style='padding: 10px;'>Idade (Meses)</th>
            <th style='padding: 10px;'>ID Esperado</th>
            <th style='padding: 10px;'>Descrição</th>
            <th style='padding: 10px;'>Resultado</th>
        </tr>";

foreach ($testes as $idade) {
    $res = buscarCategoria($idade, $arrayCategorias);
    echo "<tr>
            <td style='padding: 8px; text-align: center;'><b>$idade</b></td>
            <td style='padding: 8px; text-align: center;'>{$res['id']}</td>
            <td style='padding: 8px;'>{$res['desc']}</td>
            <td style='padding: 8px; text-align: center;'>✅</td>
          </tr>";
}
echo "</table>";
?>