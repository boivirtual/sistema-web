<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_lixeira=0");

    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
        $pasto_id = $reg_pasto->tbl_pasto_id;
        $data_com = $reg_pasto->tbl_pasto_data_com_animais;
        $data_com_anterior = $reg_pasto->tbl_pasto_data_com_animais_anterior;
        $data_sem = $reg_pasto->tbl_pasto_data_sem_animais;
        $data_sem_anterior = $reg_pasto->tbl_pasto_data_sem_animais_anterior;

        $tbl_animasl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id='$pasto_id'");

        $rows_animais_pasto = mysqli_num_rows($tbl_animasl_pasto);    

        if ($rows_animais_pasto==0) {
            $dataAtual = new DateTime();
            $dataSem = new DateTime($data_sem);
            $diff = $dataAtual->diff($dataSem);

            if ($diff->h + ($diff->days * 24) >= 24){
                if ($data_sem_anterior!=$data_sem) {
                    $query = "UPDATE tbl_pasto SET 
                        tbl_pasto_data_sem_animais_anterior = '$data_sem'
                        WHERE tbl_pasto_id = '$pasto_id'";

                    $resultado = mysqli_query($conector, $query);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                        header('Content-type: application/json');
                        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas SEM anterior ' . $erro_mysql));
                        exit;
                    } 
                }
            }
        }
        else {
            $dataAtual = new DateTime();
            $dataCom = new DateTime($data_com);
            $diff = $dataAtual->diff($dataCom);

            if ($diff->h + ($diff->days * 24) >= 24){
                if ($data_com_anterior!=$data_com) {
                    $query = "UPDATE tbl_pasto SET 
                        tbl_pasto_data_com_animais_anterior = '$data_com'
                        WHERE tbl_pasto_id = '$pasto_id'";

                    $resultado = mysqli_query($conector, $query);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                        header('Content-type: application/json');
                        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM anterior ' . $erro_mysql));
                        exit;
                    } 
                }
            }
        }
    }


//    header('Content-type: application/json');
//    echo json_encode(array('success' => true, 'message' => 'Fim processamento'));

?> 
