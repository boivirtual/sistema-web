<?php
// Cor padrao de cada modulo de pasto (usada ate o cliente definir outra)
function cor_padrao_modulo_pasto($id) {
    $id = (int)$id;

    // Familias de cor com variacao forte de luminosidade (um bem escuro e outro bem claro), para os modulos
    // nunca ficarem parecidos no mapa: vermelho 1 escuro / 5 rosa claro; verde 2 escuro / 10 menta claro;
    // azul 3 escuro / 6 celeste claro; roxo 7 escuro / 11 lilas claro; mais laranja, amarelo e marrom.
    $fixas = array(
        1 => '#B71C1C', 2 => '#2E7D32', 3 => '#0D47A1', 4 => '#F57C00', 5 => '#F48FB1',
        6 => '#81D4FA', 7 => '#4A148C', 8 => '#FFEE58', 9 => '#6D4C41', 10 => '#69F0AE',
        999 => '#BDBDBD', 1000 => '#CE93D8', 1001 => '#F4511E', 1002 => '#00897B',
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
