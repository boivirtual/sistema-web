<?php
// Kg/Ha (inteiro arredondado) por pasto: qtd de animais do pasto em cada categoria
// x peso médio arredondado da categoria na fazenda, dividido pela área do pasto.
// Retorna [tbl_pasto_id => kg_ha]; pastos sem área, sem animais ou sem peso não entram.
function calcular_kg_ha_pastos($conector, $local_id, $pasto_id = 0) {
    $local_id = mysqli_real_escape_string($conector, $local_id);
    $peso_medio_categoria = [];

    $rs_peso_medio = mysqli_query($conector, "SELECT c.tab_codigo_categoria_idade AS categoria,
               SUM(a.peso) / COUNT(*) AS peso_medio
        FROM (SELECT GREATEST(TIMESTAMPDIFF(MONTH, COALESCE(tbl_animal_data_nascimento, CURDATE()), CURDATE()), 0) AS idade,
                     CASE
                         WHEN tbl_animal_ultimo_peso IS NOT NULL AND tbl_animal_ultimo_peso<>0 THEN tbl_animal_ultimo_peso
                         WHEN tbl_animal_peso_desmama IS NOT NULL AND tbl_animal_peso_desmama<>0 THEN tbl_animal_peso_desmama
                         WHEN tbl_animal_primeiro_peso IS NOT NULL AND tbl_animal_primeiro_peso<>0 THEN tbl_animal_primeiro_peso
                         ELSE 0
                     END AS peso
              FROM tbl_animais
              WHERE tbl_animal_codigo_fazenda='$local_id' AND
                    tbl_animal_ativo='S' AND
                    tbl_animal_lixeira=0) a
        INNER JOIN tabela_categoria_idade c
                ON a.idade BETWEEN c.tab_categoria_idade_de AND c.tab_categoria_idade_ate AND
                   c.tab_registro_lixeira_categoria_idade='0'
        GROUP BY c.tab_codigo_categoria_idade");

    while ($reg_peso_medio = mysqli_fetch_object($rs_peso_medio)) {
        $peso_medio_categoria[$reg_peso_medio->categoria] = round($reg_peso_medio->peso_medio);
    }

    $wpasto = '';

    if ($pasto_id) {
        $wpasto = " AND p.tbl_pasto_id='" . mysqli_real_escape_string($conector, $pasto_id) . "'";
    }

    $rs_qtd_pasto = mysqli_query($conector, "SELECT p.tbl_pasto_id AS pasto,
               p.tbl_pasto_area AS area,
               c.tab_codigo_categoria_idade AS categoria,
               COUNT(*) AS qtd
        FROM (SELECT tbl_animal_pasto_id AS pasto_id,
                     GREATEST(TIMESTAMPDIFF(MONTH, COALESCE(tbl_animal_pasto_nascimento, CURDATE()), CURDATE()), 0) AS idade
              FROM tbl_animal_pasto
              WHERE tbl_animal_pasto_situacao='A') a
        INNER JOIN tbl_pasto p
                ON p.tbl_pasto_id = a.pasto_id
        INNER JOIN tabela_categoria_idade c
                ON a.idade BETWEEN c.tab_categoria_idade_de AND c.tab_categoria_idade_ate AND
                   c.tab_registro_lixeira_categoria_idade='0'
        WHERE p.tbl_pasto_codigo_local='$local_id'" . $wpasto . "
        GROUP BY p.tbl_pasto_id, p.tbl_pasto_area, c.tab_codigo_categoria_idade");

    $kg_pasto = [];
    $area_pasto = [];

    while ($reg_qtd_pasto = mysqli_fetch_object($rs_qtd_pasto)) {
        $pasto = $reg_qtd_pasto->pasto;
        $area_pasto[$pasto] = (float) $reg_qtd_pasto->area;

        if (!isset($kg_pasto[$pasto])) {
            $kg_pasto[$pasto] = 0;
        }

        if (isset($peso_medio_categoria[$reg_qtd_pasto->categoria])) {
            $kg_pasto[$pasto] += $peso_medio_categoria[$reg_qtd_pasto->categoria] * $reg_qtd_pasto->qtd;
        }
    }

    $kg_ha = [];

    foreach ($kg_pasto as $pasto => $kg) {
        if ($kg > 0 && $area_pasto[$pasto] > 0) {
            $kg_ha[$pasto] = (int) round($kg / $area_pasto[$pasto]);
        }
    }

    return $kg_ha;
}
?>
