<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

$tbl_pasto = mysqli_query($conector,"SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_lixeira = 0 and 
          tbl_pasto_modulo = 1 and 
          tbl_pasto_codigo_local=191");

$num_rows_pasto = mysqli_num_rows($tbl_pasto);

echo $num_rows_pasto . '</br>';

if ($num_rows_pasto) {
    $ha = 0;

    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $area = $reg_pasto->tbl_pasto_area;
        $ha+=$area;
    }

    echo 'Area total: ' . $ha;
}

/*if ($num_rows_pasto!=0) {
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $pasto_id = $reg_pasto->tbl_pasto_id;
        $local_id = $reg_pasto->tbl_pasto_codigo_local;
        $descricao_pasto = $reg_pasto->tbl_pasto_descricao;
        $data_com_pasto = $reg_pasto->tbl_pasto_data_com_animais;
        $data_alteracao_pasto = $reg_pasto->tbl_pasto_alterado_em;

        $firstDate  = new DateTime($data_alteracao_pasto);
        $secondDate = new DateTime();
        $intvl = $firstDate->diff($secondDate);
        $dias_calculados_sem = $intvl->days;

        $dias_pasto = 0;

        $tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
            WHERE tbl_animal_pasto_id='$pasto_id' AND 
                  tbl_animal_pasto_situacao = 'A'
            ORDER BY tbl_animal_pasto_alterado_em ASC");  

        $num_rows = mysqli_num_rows($tbl_animal_pasto);

        if ($num_rows!=0) {
            while ($reg = mysqli_fetch_object($tbl_animal_pasto)) {
                $data_inclusao = $reg->tbl_animal_pasto_incluido_em;
                $data_alteracao = $reg->tbl_animal_pasto_alterado_em;

                if ($data_alteracao!='') {
                    $firstDate  = new DateTime($data_alteracao);
                    $secondDate = new DateTime();
                    $intvl = $firstDate->diff($secondDate);
                    $dias_calculados = $intvl->days;
                    $data_calculo = $firstDate;
                }
                else {
                    $firstDate  = new DateTime($data_inclusao);
                    $secondDate = new DateTime();
                    $intvl = $firstDate->diff($secondDate);
                    $dias_calculados = $intvl->days;
                    $data_calculo = $firstDate;
                }

                if ($dias_calculados>$dias_pasto) {
                    $dias_pasto=$dias_calculados;
                    $data_com_animais = $data_calculo;
                }
            }
        }

        if ($num_rows!=0) {
            mysqli_query($conector, "UPDATE tbl_pasto SET
                tbl_pasto_data_com_animais = '$data_alteracao',
                tbl_pasto_data_com_animais_anterior = '$data_alteracao'

                WHERE tbl_pasto_id = '$pasto_id'");

            echo $local_id .' - '. $pasto_id .'-'. $descricao_pasto .' - '.$data_alteracao . ' dias: ' . $dias_pasto . '</br>';
        }
        else {
            mysqli_query($conector, "UPDATE tbl_pasto SET
                tbl_pasto_data_sem_animais = '$data_alteracao_pasto',
                tbl_pasto_data_sem_animais_anterior = '$data_alteracao_pasto'

                WHERE tbl_pasto_id = '$pasto_id'");

            echo $local_id .' - '. $pasto_id .'-'. $descricao_pasto .' - '.$data_alteracao_pasto . ' dias: ' . $dias_calculados_sem . '</br>';
        }
    }
}
*/


?>