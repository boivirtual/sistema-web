<?php
    include "conecta_mysql.inc";

    @ session_start(); 
    $_SESSION['abrir_agenda']='S';

    //$data_evento = '2022-09-23';
    $local = $_POST["local"];
    //$local=0;
    $eventos = [];

    /*setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    echo strftime('%d de %B de %Y', strtotime($data_evento)) .'<br/>';

    echo strftime('%A', strtotime($data_evento)) .'<br/>';
    */

    if ($local=='000000000') {
        $sql = "SELECT * FROM tbl_agenda 
            WHERE '$data_evento' BETWEEN tbl_agenda_data_inicial AND tbl_agenda_data_final OR 
                DATE(tbl_agenda_data_inicial)='$data_evento' AND  
                tbl_agenda_lixeira = 0";
    }
    else {
        $sql = "SELECT * FROM tbl_agenda 
            WHERE tbl_agenda_local = '$local' AND 
                  tbl_agenda_lixeira = 0 AND 
                  BETWEEN tbl_agenda_data_inicial AND tbl_agenda_data_final OR 
                DATE(tbl_agenda_data_inicial)='$data_evento'"; 
    }

    $objEvento = mysqli_query($conector, $sql);

    $num_rows = mysqli_num_rows($objEvento);

    if ($num_rows!=0) {
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

            $dataArrayinicio = explode(" ", $evento->tbl_agenda_data_inicial);
            $dataHoraStart = "{$dataArrayinicio[0]}"."T"."{$dataArrayinicio[1]}";
            $horaStart = $dataArrayinicio[1];

            if ($evento->tbl_agenda_data_final!='') {
                $dataArray = explode(" ", $evento->tbl_agenda_data_final);
                $dataHoraEnd = "{$dataArray[0]}"."T"."{$dataArray[1]}";
                $horaEnd = $dataArray[1];

                if ($horaEnd=='00:00:00') {
                    $dataArray[0] = date('Y-m-d', strtotime('-1 days', strtotime($dataArray[0])));
                }
            }
            else {
                $dataArray[0] = '';
                $dataArray[1] = '';
            }

            //echo 'Evento: ' . $data_evento .' - Data Inicial: ' . $dataArrayinicio[0] . ' ' . $dataArrayinicio[1] . ' - Data Final: ' . $dataArray[0] .' '. $dataArray[1] . '</br>';

            //if ($data_final>=$data_evento || $dataArrayinicio[0]==$data_evento) {
          
                // Dia Todo sem periodo e sem horario
            /*    if ($dataArrayinicio[0]==$data_evento && $dataArrayinicio[1]=='00:00:00' && $evento->tbl_agenda_data_final=='') {

                    echo $evento->tbl_agenda_titulo . '</br>';
                }

                // Dia Todo com periodo e sem horario
                if ($dataArray[0]!='' && $dataArray[0]>=$data_evento && $dataArray[1]=='00:00:00') {

                    echo $evento->tbl_agenda_titulo . '</br>';
                }

                // Dia com hora sem periodo
                if ($dataArrayinicio[0]==$data_evento && $dataArrayinicio[1]!='00:00:00' && $evento->tbl_agenda_data_final=='') {

                    echo $dataArrayinicio[1] .' '. $evento->tbl_agenda_titulo . '</br>';
                }

                // Dia com hora com periodo e datas iguais
                if ($dataArrayinicio[0]==$data_evento && $dataArray[0]!='' && $dataArray[0]==$data_evento && $dataArray[1]!='00:00:00') {

                    echo $dataArrayinicio[1] .'-'. $dataArray[1] .' '. $evento->tbl_agenda_titulo . '</br>';
                }

                // Dia com hora com periodo e datas diferentes 1ª Data
                if ($dataArrayinicio[0]==$data_evento && $dataArray[0]!='' && $dataArray[0]>$data_evento && $dataArray[1]!='00:00:00') {

                    echo $dataArrayinicio[1] .'- 00:00' .' '. $evento->tbl_agenda_titulo . '</br>';
                }

                // Dia com hora com periodo e datas diferentes 2ª Data
                if ($evento->tbl_agenda_data_final!='' && $dataArray[0]!='' && $dataArray[0]>=$data_evento && $dataArray[1]!='00:00:00') {

                    echo '00:00 - ' . $dataArray[1] .' '. $evento->tbl_agenda_titulo . '</br>';
                }*/

        
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
    }

        /*
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

echo json_encode($eventos);*/

?>