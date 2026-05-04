<?php
    // Em 10/03/2025 foi incluido Atividade vindo do Post para ler somente a atividade Reprodução para a chamado do programa form_agenda_protocolos.php 
    include "conecta_mysql.inc";

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = mysqli_query($conector_acesso, "SELECT * FROM usuario 
        WHERE id_usuario = '$codigo_usuario' AND
              lixeira_usuario=0 ");  

    $num_rows_usuario = mysqli_num_rows($tbl_usuario);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($tbl_usuario);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }

    $local = $_POST["local"];
    $atividade = $_POST["atividade"];

    if ($local == '') {
        $eventos = [];

        if ($atividade==2) {
            $sql = "SELECT * FROM tbl_agenda 
                WHERE tbl_agenda_lixeira = 0 AND 
                      tbl_agenda_atividade_padrao='$atividade'";
        }
        else {
            $sql = "SELECT * FROM tbl_agenda 
                WHERE tbl_agenda_lixeira = 0";
        }

        $objEvento = mysqli_query($conector, $sql);

        while($evento = mysqli_fetch_object($objEvento)){
            $tipo_padrao = $evento->tbl_agenda_atividade_padrao;
            $local_envento = $evento->tbl_agenda_local;

            foreach ($array_locais_usuario as $value) {
                $value = ltrim($value);
                $value = rtrim($value);
                if ($value==$local_envento) {
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
            }
        }
        echo json_encode($eventos);
    }
    else {
        $array_local = explode(",", $_POST["local"]);
        $eventos = [];

        foreach($array_local as $local){
            if ($atividade==2) {
                $sql = "SELECT * FROM tbl_agenda 
                    WHERE tbl_agenda_local = $local AND 
                          tbl_agenda_lixeira = 0 AND 
                          tbl_agenda_atividade_padrao='$atividade'";
            }
            else {
                $sql = "SELECT * FROM tbl_agenda 
                    WHERE tbl_agenda_local = $local AND 
                          tbl_agenda_lixeira = 0";
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
        }
        echo json_encode($eventos);
    }

?>