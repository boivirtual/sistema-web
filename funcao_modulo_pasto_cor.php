<?php
// Cor padrao de cada modulo de pasto (usada ate o cliente definir outra)
function cor_padrao_modulo_pasto($id) {
    $id = (int)$id;

    $fixas = array(
        1 => '#E53935', 2 => '#43A047', 3 => '#1E88E5', 4 => '#FB8C00', 5 => '#D81B60',
        6 => '#00ACC1', 7 => '#8E24AA', 8 => '#FDD835', 9 => '#6D4C41', 10 => '#7CB342',
        999 => '#BDBDBD', 1000 => '#3949AB', 1001 => '#F4511E', 1002 => '#00897B',
        1003 => '#C0CA33', 1004 => '#5E35B1', 1005 => '#039BE5', 1006 => '#616161', 1007 => '#F8BBD0'
    );

    if (isset($fixas[$id])) {
        return $fixas[$id];
    }

    $extras = array('#26A69A', '#AB47BC', '#FF7043', '#66BB6A', '#42A5F5', '#FFCA28');

    return $extras[$id % count($extras)];
}

// Lista os modulos ativos (por ID) com a cor de cada um.
// Em bancos sem a coluna tbl_modulo_cor vale a cor padrao acima (a coluna nao e criada automaticamente).
function ler_modulos_pasto($conector) {
    $modulos = array();
    $tem_cor = false;

    try {
        $rs_col = mysqli_query($conector, "SHOW COLUMNS FROM tbl_modulo_pasto LIKE 'tbl_modulo_cor'");
        $tem_cor = ($rs_col && mysqli_num_rows($rs_col) > 0);

        $campo_cor = $tem_cor ? 'tbl_modulo_cor' : "''";

        $rs = mysqli_query($conector, "SELECT tbl_modulo_id, tbl_modulo_descricao, $campo_cor AS cor
            FROM tbl_modulo_pasto
            WHERE tbl_modulo_lixeira=0
            ORDER BY tbl_modulo_id ASC");

        while ($rs && $reg = mysqli_fetch_object($rs)) {
            $id = (int)$reg->tbl_modulo_id;
            $cor = preg_match('/^#[0-9A-Fa-f]{6}$/', (string)$reg->cor) ? $reg->cor : cor_padrao_modulo_pasto($id);

            $modulos[] = array('id' => $id, 'descricao' => $reg->tbl_modulo_descricao, 'cor' => $cor);
        }
    }
    catch (Exception $e) {
        return array();
    }

    return $modulos;
}
?>
