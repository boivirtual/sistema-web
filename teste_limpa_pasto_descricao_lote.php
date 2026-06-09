<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

$tbl_pasto = mysqli_query($conector,"SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_lixeira = 0");

$num_rows_pasto = mysqli_num_rows($tbl_pasto);

if ($num_rows_pasto) {
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $id_pasto = $reg_pasto->tbl_pasto_id ;

        $tbl_animais_pasto = mysqli_query($conector,"SELECT * FROM tbl_animal_pasto 
            WHERE tbl_animal_pasto_id = '$id_pasto'");

        $num_rows_pasto_animais = mysqli_num_rows($tbl_animais_pasto);

        if ($num_rows_pasto_animais==0) {
            $query = "UPDATE tbl_pasto SET 
                tbl_pasto_descricao_lote = null,
                tbl_pasto_id_lote = null, 
                tbl_pasto_ano_lote = null,
                tbl_pasto_descricao_lote_1 = null,
                tbl_pasto_descricao_lote_2 = null,
                tbl_pasto_descricao_lote_3 = null,
                tbl_pasto_descricao_lote_4 = null,
                tbl_pasto_descricao_lote_5 = null,
                tbl_pasto_descricao_lote_6 = null
                WHERE tbl_pasto_id = '$id_pasto'";

            $resultado = mysqli_query($conector, $query);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                echo 'erro pasto: '. $id_pasto .' '. $erro_mysql . '</br>';
            }
        }
        else {
            $query = "UPDATE tbl_pasto SET 
                tbl_pasto_id_lote = null, 
                tbl_pasto_ano_lote = null,
                tbl_pasto_descricao_lote_1 = null,
                tbl_pasto_descricao_lote_2 = null,
                tbl_pasto_descricao_lote_3 = null,
                tbl_pasto_descricao_lote_4 = null,
                tbl_pasto_descricao_lote_5 = null,
                tbl_pasto_descricao_lote_6 = null
                WHERE tbl_pasto_id = '$id_pasto'";

            $resultado = mysqli_query($conector, $query);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                echo 'erro pasto: ' . $id_pasto .' '. $erro_mysql . '</br>';
            }

        }
    }

    echo 'Fim ';
}


?>