<?php
    include "conecta_mysql.inc";

    @ session_start();
    $_SESSION['abrir_agenda']='S';

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
            $array_locais_usuario=[];
        }
    }
    else {
        $array_locais_usuario=[];
    }

    $local = isset($_POST["local"]) ? trim($_POST["local"]) : '';
    $atividade = isset($_POST["atividade"]) ? trim($_POST["atividade"]) : '';

    $eventos = [];

    if ($local=='') {
        echo json_encode($eventos);
        exit;
    }

    if ($local=='000000000') {
        $array_local = $array_locais_usuario;
    }
    else {
        $array_local = explode(",", $local);
    }

    foreach ($array_local as $local_selecionado) {
        $local_selecionado = trim($local_selecionado);

        $permitido = false;
        foreach ($array_locais_usuario as $value) {
            $value = trim($value);
            if ($value==$local_selecionado) {
                $permitido = true;
            }
        }

        if (!$permitido) {
            continue;
        }

        if ($atividade!='') {
            $sql = "SELECT * FROM tbl_agenda
                WHERE tbl_agenda_local = '$local_selecionado' AND
                      tbl_agenda_lixeira = 0 AND
                      tbl_agenda_atividade_padrao = '$atividade'";
        }
        else {
            $sql = "SELECT * FROM tbl_agenda
                WHERE tbl_agenda_local = '$local_selecionado' AND
                      tbl_agenda_lixeira = 0";
        }

        $objEvento = mysqli_query($conector, $sql);

        while($evento = mysqli_fetch_object($objEvento)){
            $tipo_padrao = $evento->tbl_agenda_atividade_padrao;

            $sql_atv = mysqli_query($conector, "SELECT * FROM tbl_atividades_padrao
                WHERE tbl_atividade_padrao_id = '$tipo_padrao' AND
                      tbl_atividade_padrao_lixeira = 0");
            $num_rows = mysqli_num_rows($sql_atv);

            if($num_rows != 0){
                $reg_padrao = mysqli_fetch_object($sql_atv);
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
                                "allDay" => true,
                                "extendedProps" => array(
                                    "description" => $evento->tbl_agenda_descricao
                                )
                    );
                }
                else {
                    $arrayEvento = array(
                                "id"     => $evento->tbl_agenda_id,
                                "title"  => $evento->tbl_agenda_titulo,
                                "start"  => $dataHoraStart,
                                "color"  => $cor_padrao,
                                "textColor" => '#0a0a0a',
                                "allDay" => false,
                                "extendedProps" => array(
                                    "description" => $evento->tbl_agenda_descricao
                                )
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
                                "allDay" => true,
                                "extendedProps" => array(
                                    "description" => $evento->tbl_agenda_descricao
                                )
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
                                "allDay" => false,
                                "extendedProps" => array(
                                    "description" => $evento->tbl_agenda_descricao
                                )
                    );
                }
            }
            array_push($eventos, $arrayEvento);
        }
    }

    echo json_encode($eventos);

?>
