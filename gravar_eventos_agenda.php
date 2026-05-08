<?php
    include "conecta_mysql.inc";
    @ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];
    $data_sistema = date("Y-m-d H:i:s");
?>

<?php

if($_POST["tipoGravacao"] == 0){
    $local = $_POST["local"];
    $protocoloId = $_POST["protocolo"];
    $data = $_POST["data"];
    $cob = $_POST["cobertura"];

    $sql = "SELECT tbl_cobertura_codigo_grupo, tbl_cobertura_qtd_animais FROM tbl_cobertura WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_id = $cob AND tbl_cobertura_lixeira = 0";
    $response = mysqli_query($conector, $sql);
    $objCobertura = mysqli_fetch_object($response);

    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
        WHERE tbl_protocoloiatf_id = $protocoloId AND tbl_protocoloiatf_lixeira = 0");
    $reg_protocolo_iatf = mysqli_fetch_object($sql);
    $dias_diagnostico = $reg_protocolo_iatf->tbl_protocoloiatf_dias_diagnostico;

    $sql = "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_protocolo_id = $protocoloId AND tbl_ite_protocoloiatf_lixeira = 0 ORDER BY tbl_ite_protocoloiatf_id ASC";
    $response = mysqli_query($conector, $sql);

    $arrayDias = [];

    while($reg_ite_procolo = mysqli_fetch_object($response)){
        $desc_ite_protocolo = $reg_ite_procolo->tbl_ite_protocoloiatf_descricao;
        $desc = substr($desc_ite_protocolo, 3);

        array_push($arrayDias, $desc);
    }

    $sql = "SELECT tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id = $local AND tbl_pessoa_lixeira = 0";
    $response = mysqli_query($conector, $sql);
    $objLocal = mysqli_fetch_object($response);

    foreach($arrayDias as $dia){
        $diaFormat = trim($dia);
        $dataHoraEvento = date('Y-m-d h:i:s', strtotime($data."+ {$dia} days"));
        $dataEvento = date('Y-m-d', strtotime($data."+ {$dia} days"));
        $stringBase = "{$objLocal->tbl_pessoa_nome}-D{$diaFormat}-GRUPO {$objCobertura->tbl_cobertura_codigo_grupo}-{$objCobertura->tbl_cobertura_qtd_animais} FÊMEAS";
        $sql = "INSERT INTO tbl_agenda(
            tbl_agenda_local,
            tbl_agenda_titulo,
            tbl_agenda_descricao,
            tbl_agenda_data_inicial,
            tbl_agenda_data_final,
            tbl_agenda_atividade_padrao,
            tbl_agenda_codigo_cobertura,
            tbl_agenda_incluido_em,
            tbl_agenda_incluido_por,
            tbl_agenda_alterado_em,
            tbl_agenda_alterado_por,
            tbl_agenda_lixeira,
            tbl_agenda_excluido_em,
            tbl_agenda_excluido_por
        )VALUES(
            '$local',
            '$stringBase',
            null,
            '$dataHoraEvento',
            null,
            2,
            '$cob',            
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            0,
            null,
            null
        )";

        mysqli_query($conector, $sql) or die(mysqli_error($conector));

    }

    $data_diagnostico = date("Y-m-d h:i:s", strtotime($dataEvento . "+{$dias_diagnostico} days"));
    $stringBase = "{$objLocal->tbl_pessoa_nome}-DIAGNOSTICO-GRUPO {$objCobertura->tbl_cobertura_codigo_grupo}-{$objCobertura->tbl_cobertura_qtd_animais} FÊMEAS";

    $sql = "INSERT INTO tbl_agenda(
            tbl_agenda_local,
            tbl_agenda_titulo,
            tbl_agenda_descricao,
            tbl_agenda_data_inicial,
            tbl_agenda_data_final,
            tbl_agenda_atividade_padrao,
            tbl_agenda_codigo_cobertura,
            tbl_agenda_incluido_em,
            tbl_agenda_incluido_por,
            tbl_agenda_alterado_em,
            tbl_agenda_alterado_por,
            tbl_agenda_lixeira,
            tbl_agenda_excluido_em,
            tbl_agenda_excluido_por
    )VALUES(
        '$local',
        '$stringBase',
        null,
        '$data_diagnostico',
        null,
        2,
        '$cob',            
        '$data_sistema',
        '$nomeusuario',
        null,
        null,
        0,
        null,
        null
    )";

    mysqli_query($conector, $sql) or die(mysqli_error($conector));

}elseif($_POST["tipoGravacao"] == 1){
    $idEvento = $_POST["id"];
    $tituloEvento = $_POST["titulo"];
    $dataHoraEvento = date('Y-m-d h:i:s', strtotime($_POST["dataHora"]));

    $sql = "UPDATE tbl_agenda SET
            tbl_agenda_titulo = '$tituloEvento',
            tbl_agenda_data_inicial = '$dataHoraEvento',
            tbl_agenda_alterado_em = '$data_sistema',
            tbl_agenda_alterado_por = '$nomeusuario'
            WHERE tbl_agenda_id = $idEvento";
    
    mysqli_query($conector, $sql) or die(mysqli_error($conector));
}elseif($_POST["tipoGravacao"] == 2){
    $idEvento = $_POST["id"];

    $sql = "UPDATE tbl_agenda SET
            tbl_agenda_lixeira = 1,
            tbl_agenda_excluido_em = '$data_sistema',
            tbl_agenda_excluido_por = '$nomeusuario'
            WHERE tbl_agenda_id = $idEvento";
    
    mysqli_query($conector, $sql) or die(mysqli_error($conector));
}

?>