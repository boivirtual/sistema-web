<?php
    include "conecta_mysql.inc";

    $id_evento = $_POST["id_evento"];

    $objEvento = mysqli_query($conector, "SELECT * FROM tbl_agenda 
        WHERE tbl_agenda_id = '$id_evento'");

    $num_rows = mysqli_num_rows($objEvento);

    if ($num_rows!=0) {
        $evento = mysqli_fetch_object($objEvento);
        $json = json_encode($evento);
        echo $json;
    }
    else {
        $erro_mysql = mysqli_error($conector);
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Erro na leitura do evento. ' . $erro_mysql));
        exit;
    }

?>