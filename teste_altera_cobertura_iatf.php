<?php 
    // Gera o numero da cobertura IATF no bezerro nascido
    include "conecta_mysql.inc";
    $data_sistema = date("Y-m-d H:i:s");
    $diagnostico_p = 0;
    $diagnostico_n = 0;
    $diagnostico_sem = 0;
    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id 
        INNER JOIN tbl_parametro_estacao_monta 
                ON tbl_par_estacao_id  = tbl_cobertura_codigo_estacao_monta
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_controle='C'");

//tbl_cobertura_codigo_local='$local_processamento' AND
    $num_rows = mysqli_num_rows($tbl_item_cobertura);   

    if ($num_rows!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
            $cobertura_id = $reg_cobertura->tbl_cobertura_id;
            $estacao_id = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;

            $id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

            $tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_codigo_id= '$id_animal'");

            $reg_animal = mysqli_fetch_object($tbl_animal); 

            $codigo_alfa_consulta = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico_consulta = $reg_animal->tbl_animal_codigo_numerico;

            if ($codigo_alfa_consulta==''){
                $codigo_edi = $codigo_numerico_consulta; 
            }
            else {
                $codigo_edi = $codigo_alfa_consulta.'-'.$codigo_numerico_consulta; 
            }

            echo 'Cobertura: '. $cobertura_id .' Animal: ' . $codigo_edi .' Estação: ' . $reg_cobertura->tbl_par_estacao_nome;
            echo 'Diagnostico: ' . $diagnostico . '</br>';

            if ($diagnostico=='P') {
                $diagnostico_p++;
            }

            if ($diagnostico=='N') {
                $diagnostico_n++;
            }

            if ($diagnostico=='') {
                $diagnostico_sem++;
            }

            // grava o numero da cobertura no registro de nascimento do bezerro dessa femea / estacao monta

            if (($nascido == 'N' || $nascido == 'M') && $diagnostico == 'P') {
                $tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                    WHERE tbl_animal_codigo_mae = '$id_animal' AND 
                          tbl_animal_estacao_monta_nascimento = '$estacao_id'");

                $num_rows = mysqli_num_rows($tbl_animal); 

                if ($num_rows!=0) {
                    $reg_animal = mysqli_fetch_object($tbl_animal); 
                    $codigo_id_bezzero = $reg_animal->tbl_animal_codigo_id;
                    $codigo_num_bezzero = $reg_animal->tbl_animal_codigo_numerico;

                    $sql = ("UPDATE tbl_animais SET 
                        tbl_animal_codigo_cobertura='$cobertura_id'
                    WHERE tbl_animal_codigo_id ='$codigo_id_bezzero'");

                    $resultado = mysqli_query($conector, $sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado) {
                        echo 'Erro ao grava a cobertura no bezerro: ' . $codigo_num_bezzero. ' erro: ' . $erro_mysql .'</br>';
                    }
                    else {
                        echo 'Bezerro: ' . $codigo_num_bezzero . ' alterado </br>';
                    }
                }
            }

        } // fim while

    }

    print_r('Diagnóstico P: ' . $diagnostico_p . '</br>');
    print_r('Diagnóstico N: ' . $diagnostico_n . '</br>');
    print_r('Diagnóstico Sem: ' . $diagnostico_sem . '</br>');

    print_r('Fim');

?>