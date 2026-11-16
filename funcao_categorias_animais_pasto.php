<?php
// Conta bezerros (categoria 0, os dois sexos), femeas e machos (demais categorias, por sexo) de
// um pasto - a mesma logica usada no Tabuleiro (ler_mapa_gados.php), para os dois sempre baterem.
function contar_categorias_pasto($conector, $pasto_id, $array_categoria_pasto) {
    $pasto_id = (int)$pasto_id;
    $array_categoria = explode("!", $array_categoria_pasto);
    $arrayCategorias = array();

    foreach ($array_categoria as $codigo_categoria) {
        $codigo_categoria_sql = mysqli_real_escape_string($conector, $codigo_categoria);

        $rs = mysqli_query($conector, "SELECT tab_codigo_categoria_idade, tab_categoria_idade_de, tab_categoria_idade_ate
            FROM tabela_categoria_idade
            WHERE tab_codigo_categoria_idade='$codigo_categoria_sql' AND
                  tab_registro_lixeira_categoria_idade='0'");

        $fila = $rs ? mysqli_fetch_object($rs) : null;

        if (!$fila) {
            continue;
        }

        $arrayCategorias[] = array(
            'idade_de' => $fila->tab_categoria_idade_de,
            'idade_ate' => $fila->tab_categoria_idade_ate
        );
    }

    $qtdCategorias = count($arrayCategorias);
    $arrayMacho = array_fill(0, max($qtdCategorias, 1), 0);
    $arrayFemea = array_fill(0, max($qtdCategorias, 1), 0);

    $rs_animais = mysqli_query($conector, "SELECT tbl_animal_pasto_sexo, tbl_animal_pasto_nascimento FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id=$pasto_id AND tbl_animal_pasto_situacao='A'");

    while ($reg_animal = mysqli_fetch_object($rs_animais)) {
        $sexo = $reg_animal->tbl_animal_pasto_sexo;

        $data = new DateTime($reg_animal->tbl_animal_pasto_nascimento);
        $idade = $data->diff(new DateTime(date('Y-m-d')));
        $meses = ($idade->format('%Y') * 12) + $idade->format('%m');

        for ($i = 0; $i < $qtdCategorias; $i++) {
            $idade_de = $arrayCategorias[$i]['idade_de'];
            $idade_ate = $arrayCategorias[$i]['idade_ate'];

            if ($meses >= $idade_de && $meses <= $idade_ate && $sexo == 'F') {
                $arrayFemea[$i]++;
            }
            else if ($meses >= $idade_de && $meses <= $idade_ate && $sexo == 'M') {
                $arrayMacho[$i]++;
            }
        }
    }

    $bezerros = 0;
    $femeas = 0;
    $machos = 0;

    for ($i = 0; $i < $qtdCategorias; $i++) {
        if ($i == 0) {
            // categoria 0 (bezerro): os dois sexos juntos
            $bezerros += $arrayMacho[$i] + $arrayFemea[$i];
        }
        else {
            $machos += $arrayMacho[$i];
            $femeas += $arrayFemea[$i];
        }
    }

    return array(
        'bezerros' => $bezerros,
        'femeas' => $femeas,
        'machos' => $machos,
        'total' => $bezerros + $femeas + $machos
    );
}
?>
