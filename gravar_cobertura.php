<?php
    include "conecta_mysql.inc";
    @ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];
    $data_sistema = date("Y-m-d H:i:s");

//$mensagem = '';

/*if(isset($_POST["tipo_gravacao"]) && $_POST["tipo_gravacao"] == 0){
    $vet = $_POST["vetObj"];

    for($i = 0; $i < count($vet); $i++){
        $cobertura = $vet[$i]["cobertura"];
        $ordem = $vet[$i]["ordem"];

        do {
            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            WHERE tbl_ite_cobertura_numero_id='$cobertura' AND 
                  tbl_ite_cobertura_numero_item = '$ordem'");

            $num_rows = mysqli_num_rows($tbl_item_cobertura);   

            if ($num_rows==1) {
                

            }
            if ($num_rows==0) {
                $mensagem.='Não Achei o Registro ' . $ordem . ' Vou somar! </br>';
                $ordem++;
            }

        } while ($num_rows==0);

        if ($num_rows==1) {
            $mensagem.='Achei o Registro ' . $ordem . '</br>';
        }
    }
}


$resposta = array('success' => true, 'message' => 'Mensagem: ' . $mensagem);
header('Content-type: application/json');
echo json_encode($resposta);
exit;
*/

if(isset($_POST["tipo_gravacao"]) && $_POST["tipo_gravacao"] == 0){
    $vet = $_POST["vetObj"];

    for($i = 0; $i < count($vet); $i++){
        $cobertura = $vet[$i]["cobertura"];
        $ordem = $vet[$i]["ordem"];

        $touro_semem = ($vet[$i]["touro_semem"] == "") ? $vet[$i]["touroSemem"] : $vet[$i]["touro_semem"];
        $lote_semem = $vet[$i]["lote_semem"];
        $animal_id = $vet[$i]["animal_id"];
        $dia_1 = $vet[$i]["dia_1"];
        $dia_2 = $vet[$i]["dia_2"];
        $dia_3 = $vet[$i]["dia_3"];
        $dia_4 = $vet[$i]["dia_4"];
        $dia_5 = $vet[$i]["dia_5"];
        $dia_6 = $vet[$i]["dia_6"];
        $inseminador = $vet[$i]["inseminador"];
        $result_diag = $vet[$i]["resultado_diagnostico"];
        $destino = $vet[$i]["destino"];
        $quantos_dias = $vet[$i]["quantos_dias"];
        $qual_dia = $vet[$i]["qual_dia"];

        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        WHERE tbl_ite_cobertura_numero_id='$cobertura' AND 
              tbl_ite_cobertura_numero_item = '$ordem'");

        $reg_item_cobertura = mysqli_fetch_object($tbl_item_cobertura);

        $tem_D0 = $reg_item_cobertura->tbl_ite_cobertura_dia_1; 
        $qtd_diagnosticos_positivo = $reg_item_cobertura->tbl_ite_cobertura_qtd_diagnosticos_positivo; 
        $codigo_animal = $reg_item_cobertura->tbl_ite_cobertura_codigo_animal; 

        if ($result_diag=='P') {
            if ($qtd_diagnosticos_positivo=='' || $qtd_diagnosticos_positivo==0) {
                $qtd_diagnosticos_positivo=1;
            }
        } 
        else {
            $qtd_diagnosticos_positivo=0;
        }

        $numero_cobertura = 0;

        $tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_id='$animal_id'");

        $reg_animais = mysqli_fetch_object($tbl_animais);
        $numero_cobertura = $reg_animais->tbl_animal_numero_coberturas; 

        if ($numero_cobertura=='') {
            $numero_cobertura = 0;
        }

        if ($tem_D0!='S' && $dia_1=='S') {
            $numero_cobertura++;
            $sql = "UPDATE tbl_animais SET
                tbl_animal_numero_coberturas = '$numero_cobertura'
                WHERE tbl_animal_codigo_id='$animal_id'"; 
            $resultado = mysqli_query($conector,$sql);                           
            $erro_mysql = mysqli_error($conector);  

            if (!$resultado)  {
                $array_conta = array(
                    0,
                    'Ocorreu um erro na atualização do animal.' . $erro_mysql
                );
                $array_string = implode('|', $array_conta);
                echo $array_string;
                exit;
            }
        }

        if ($qual_dia==$quantos_dias) {
            $sql = "UPDATE tbl_animais SET
                tbl_animal_aguardando_diagnostico = 'S'
                WHERE tbl_animal_codigo_id='$animal_id'"; 
            $resultado = mysqli_query($conector,$sql);                           
            $erro_mysql = mysqli_error($conector);  

            if (!$resultado)  {
                $array_conta = array(
                    0,
                    'Ocorreu um erro na atualização do animal.' . $erro_mysql
                );
                $array_string = implode('|', $array_conta);
                echo $array_string;
                exit;
            }
        }

        if ($vet[$i]["data_diagnostico"]=='') {
            $sql = "UPDATE tbl_item_cobertura SET
                tbl_ite_cobertura_codigo_touro_semen = '$touro_semem',
                tbl_ite_cobertura_lote_semen = '$lote_semem',
                tbl_ite_cobertura_data_diagnostico = null,
                tbl_ite_cobertura_resultado_diagnostico = '$result_diag',
                tbl_ite_cobertura_qtd_diagnosticos_positivo='$qtd_diagnosticos_positivo',
                tbl_ite_cobertura_nome_inseminador = '$inseminador',
                tbl_ite_cobertura_destino = '$destino',
                tbl_ite_cobertura_dia_1 = '$dia_1',
                tbl_ite_cobertura_dia_2 = '$dia_2',
                tbl_ite_cobertura_dia_3 = '$dia_3',
                tbl_ite_cobertura_dia_4 = '$dia_4',
                tbl_ite_cobertura_dia_5 = '$dia_5',
                tbl_ite_cobertura_dia_6 = '$dia_6',
                tbl_ite_cobertura_numero_cobertura = '$numero_cobertura'
                WHERE tbl_ite_cobertura_numero_id = '$cobertura' AND tbl_ite_cobertura_numero_item = '$ordem'";
        }
        else {
            $data_diagnostico = date("Y-m-d", strtotime(str_replace('/', '-', $vet[$i]["data_diagnostico"])));
            $sql = "UPDATE tbl_item_cobertura SET
                tbl_ite_cobertura_codigo_touro_semen = '$touro_semem',
                tbl_ite_cobertura_lote_semen = '$lote_semem',
                tbl_ite_cobertura_data_diagnostico = '$data_diagnostico',
                tbl_ite_cobertura_resultado_diagnostico = '$result_diag',
                tbl_ite_cobertura_qtd_diagnosticos_positivo='$qtd_diagnosticos_positivo',
                tbl_ite_cobertura_nome_inseminador = '$inseminador',
                tbl_ite_cobertura_destino = '$destino',
                tbl_ite_cobertura_dia_1 = '$dia_1',
                tbl_ite_cobertura_dia_2 = '$dia_2',
                tbl_ite_cobertura_dia_3 = '$dia_3',
                tbl_ite_cobertura_dia_4 = '$dia_4',
                tbl_ite_cobertura_dia_5 = '$dia_5',
                tbl_ite_cobertura_dia_6 = '$dia_6',
                tbl_ite_cobertura_numero_cobertura = '$numero_cobertura'
                WHERE tbl_ite_cobertura_numero_id = '$cobertura' AND tbl_ite_cobertura_numero_item = '$ordem'";
        }
        
        $resultado = mysqli_query($conector,$sql);  
        $erro_mysql = mysqli_error($conector);  


        if (!$resultado)  {
            $array_conta = array(
                0,
                'Ocorreu um erro na atualização da cobertura.' . $erro_mysql
            );
            $array_string = implode('|', $array_conta);
            echo $array_string;
            exit;
        }

        // ajusta a data do diagnostico na agenda
        if ($vet[$i]["data_diagnostico"]!='') {
            $tbl_agenda =  mysqli_query($conector,"SELECT * FROM tbl_agenda WHERE tbl_agenda_codigo_cobertura = '$cobertura' 
                ORDER BY tbl_agenda_data_inicial DESC LIMIT 1");

            $num_rows_agenda = mysqli_num_rows($tbl_agenda);

            if ($num_rows_agenda!=0){
                $reg_agenda = mysqli_fetch_object($tbl_agenda);
                $id_agenda = $reg_agenda->tbl_agenda_id;

                $data_diagnostico = date("Y-m-d", strtotime(str_replace('/', '-', $vet[$i]["data_diagnostico"])));

                $sql = "UPDATE tbl_agenda SET
                        tbl_agenda_data_inicial = '$data_diagnostico'
                        WHERE tbl_agenda_id = '$id_agenda'";

                $resultado = mysqli_query($conector,$sql);  
                $erro_mysql = mysqli_error($conector);  
            }
        }

        if ($result_diag=='P' && $qtd_diagnosticos_positivo==1) {
            $sql = "INSERT INTO tbl_item_cobertura_diagnostico_positivo(
                tbl_ite_cobertura_diagnostico_numero_id,
                tbl_ite_cobertura_diagnostico_numero_item,
                tbl_ite_cobertura_diagnostico_data_diagnostico,
                tbl_ite_cobertura_diagnostico_codigo_id_animal,
                tbl_ite_cobertura_diagnostico_codigo_animal,
                tbl_ite_cobertura_diagnostico_confirmado_em,
                tbl_ite_cobertura_diagnostico_data_confirmado_por
            )VALUES(
                '$cobertura',
                '$ordem',
                '$data_diagnostico',
                '$animal_id',
                '$codigo_animal',
                '$data_sistema',
                '$nomeusuario'

            )";

            mysqli_query($conector, $sql);
        }
    }

    // Finalizar a cobertura
    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        WHERE tbl_ite_cobertura_numero_id='$cobertura'");

    $finalizar_cobertura = 'S';

    while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
        $diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;

        if ($diagnostico!='P' && $diagnostico!='N'){
            $finalizar_cobertura = 'N';
        }
    }

    if ($finalizar_cobertura == 'S') {
        $sql = "UPDATE tbl_cobertura SET
                tbl_cobertura_encerrada = 'S'
                WHERE tbl_cobertura_id = '$cobertura'";
        $resultado = mysqli_query($conector,$sql);  
        $erro_mysql = mysqli_error($conector);  

        if (!$resultado)  {
            $array_conta = array(
                0,
                'Ocorreu um erro ao finalizar a cobertura.' . $erro_mysql
            );
            $array_string = implode('|', $array_conta);
            echo $array_string;
            exit;
        }
    }

    $sql = "SELECT * FROM tbl_protocolo_cobertura WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura'";
    $cob = mysqli_fetch_object(mysqli_query($conector, $sql));
    $array_conta = array(
        $cobertura,
        $cob->tbl_protocolo_cobertura_codigoiatf
    );
    $array_string = implode('|', $array_conta);

    echo $array_string;
    
}elseif(isset($_POST["tipo_gravacao"]) && $_POST["tipo_gravacao"] == 2){

    $cobertura_id = $_POST["cobertura"];

    $sql = "UPDATE tbl_cobertura SET
            tbl_cobertura_protocoloiatf = '000000000'
            WHERE tbl_cobertura_id = $cobertura_id";
    mysqli_query($conector, $sql) or die(mysqli_error($conector));

    $sql = "UPDATE tbl_item_cobertura SET
            tbl_ite_cobertura_codigo_touro_semen = NULL,
            tbl_ite_cobertura_lote_semen = NULL,
            tbl_ite_cobertura_data_diagnostico = NULL,
            tbl_ite_cobertura_resultado_diagnostico = NULL,
            tbl_ite_cobertura_nome_inseminador = NULL,
            tbl_ite_cobertura_destino = NULL,
            tbl_ite_cobertura_dia_1 = NULL,
            tbl_ite_cobertura_dia_2 = NULL,
            tbl_ite_cobertura_dia_3 = NULL,
            tbl_ite_cobertura_dia_4 = NULL,
            tbl_ite_cobertura_dia_5 = NULL,
            tbl_ite_cobertura_dia_6 = NULL
            WHERE tbl_ite_cobertura_numero_id = $cobertura_id";
    mysqli_query($conector, $sql) or die(mysqli_error($conector));

    $sql = "DELETE FROM tbl_protocolo_cobertura WHERE tbl_protocolo_cobertura_codigo_id = $cobertura_id";
    mysqli_query($conector, $sql) or die(mysqli_error($conector));

    $sql = "DELETE FROM tbl_agenda WHERE tbl_agenda_codigo_cobertura = $cobertura_id";
    mysqli_query($conector, $sql) or die(mysqli_error($conector));

    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        WHERE tbl_ite_cobertura_numero_id='$cobertura_id'");

    $num_rows = mysqli_num_rows($tbl_item_cobertura);

    while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
        $animal_id = $reg_item->tbl_ite_cobertura_codigo_id_animal;

        $tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_id='$animal_id'");

        $reg_animais = mysqli_fetch_object($tbl_animais);
        $numero_cobertura = $reg_animais->tbl_animal_numero_coberturas;

        if ($numero_cobertura>0) {
            $numero_cobertura--;
        }
        else {
            $numero_cobertura=0;
        }

        $sql = "UPDATE tbl_animais SET
            tbl_animal_aguardando_diagnostico = 'N',
            tbl_animal_numero_coberturas = '$numero_cobertura'
            WHERE tbl_animal_codigo_id='$animal_id'"; 
        $resultado = mysqli_query($conector,$sql);  
        $erro_mysql = mysqli_error($conector);  

        if (!$resultado)  {
            echo $erro_mysql;
            exit;
        }
    }

    echo 'Protocolo excluído com sucesso!';

}elseif(isset($_POST["tipo_gravacao"]) && $_POST["tipo_gravacao"] == 3){
    $protocolo_id = $_POST["protocolo"];
    $cobertura_id = $_POST["cobertura"];
    $data_inicial = $_POST["data"];

    $sql = "UPDATE tbl_cobertura SET
            tbl_cobertura_protocoloiatf = '$protocolo_id',
            tbl_cobertura_alterado_em = '$data_sistema',
            tbl_cobertura_alterado_por = '$nomeusuario'
            WHERE tbl_cobertura_id = $cobertura_id";
    
    mysqli_query($conector, $sql) or die(mysqli_error($conector));

    $sql = "INSERT INTO tbl_protocolo_cobertura(
        tbl_protocolo_cobertura_codigo_id,
        tbl_protocolo_cobertura_codigoiatf,
        tbl_protocolo_cobertura_data
    )VALUES(
        '$cobertura_id',
        '$protocolo_id',
        '$data_inicial'
    )";

    mysqli_query($conector, $sql) or die(mysqli_error($conector));
}

?>