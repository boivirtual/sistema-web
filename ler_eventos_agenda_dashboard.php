<?php
    include "conecta_mysql.inc";

    @ session_start(); 
    $_SESSION['abrir_agenda']='S';

    $local = $_POST["local"];
    $eventos = [];

    if ($local=='000000000') {
        $sql = "SELECT * FROM tbl_agenda 
            WHERE tbl_agenda_lixeira = 0";
    }
    else {
        $sql = "SELECT * FROM tbl_agenda 
            WHERE tbl_agenda_local = '$local' AND tbl_agenda_lixeira = 0";
    }

    $objEvento = mysqli_query($conector, $sql);

    while($evento = mysqli_fetch_object($objEvento)){
        $tipo_padrao = $evento->tbl_agenda_atividade_padrao;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_atividades_padrao 
            WHERE tbl_atividade_padrao_id = '$tipo_padrao' AND 
                  tbl_atividade_padrao_lixeira = 0");
        $num_rows = mysqli_num_rows($sql);

        if($num_rows != 0){
            $reg_padrao = mysqli_fetch_object($sql);
            $cor_padrao = ltrim($reg_padrao->tbl_atividade_padrao_cor_fundo);
            $cor_padrao = rtrim($cor_padrao);
        }
        else {
            $cor_padrao = '#b52b75';
        }            

        $dataArray = explode(" ", $evento->tbl_agenda_data_inicial);
        $dataHoraStart = "{$dataArray[0]}"."T"."{$dataArray[1]}";
        $horaStart = $dataArray[1];

        if ($evento->tbl_agenda_data_final=='') {
            if ($horaStart=='00:00:00') {
                $arrayEvento = array(
                            "id"     => $evento->tbl_agenda_id,
                            "title"  => $evento->tbl_agenda_titulo,
                            "start"  => $dataHoraStart,
                            "color"  => $cor_padrao,
                            "textColor" => '#0a0a0a',
                            "allDay" => true
                );
            } 
            else {
                $arrayEvento = array(
                            "id"     => $evento->tbl_agenda_id,
                            "title"  => $evento->tbl_agenda_titulo,
                            "start"  => $dataHoraStart,
                            "color"  => $cor_padrao,
                            "textColor" => '#0a0a0a',
                            "allDay" => false
                );
            }
        } 
        else {
            $dataArray = explode(" ", $evento->tbl_agenda_data_final);
            $dataHoraEnd = "{$dataArray[0]}"."T"."{$dataArray[1]}";
            $horaEnd = $dataArray[1];

            if ($horaStart=='00:00:00') {
                $arrayEvento = array(
                            "id"     => $evento->tbl_agenda_id,
                            "title"  => $evento->tbl_agenda_titulo,
                            "start"  => $dataHoraStart,
                            "end"    => $dataHoraEnd,
                            "color"  => $cor_padrao,
                            "textColor" => '#0a0a0a',
                            "allDay" => true
                );
            } 
            else {
                $arrayEvento = array(
                            "id"     => $evento->tbl_agenda_id,
                            "title"  => $evento->tbl_agenda_titulo,
                            "start"  => $dataHoraStart,
                            "end"    => $dataHoraEnd,
                            "color"  => $cor_padrao,
                            "textColor" => '#0a0a0a',
                            "allDay" => false
                );
            }
        }
        array_push($eventos, $arrayEvento);
    }

echo json_encode($eventos);

?>