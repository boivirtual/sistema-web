<?php
include "conecta_mysql.inc";
@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");


if (isset($_POST["dia_inteiro"])){
    $data_inicial = $_POST["data_agenda_inicio"];

    $data_inicio = date('Y-m-d', strtotime ($data_inicial));
    $hora_inicio = date('H:i:s', strtotime ($data_inicial));
    $data_inicio = new DateTime($data_inicio);

    $data_final = $_POST["data_agenda_fim"];
}
else{
    $data_inicial = $_POST["data_hora_agenda_inicio"];

    $data_inicio = date('Y-m-d', strtotime ($data_inicial));
    $hora_inicio = date('H:i:s', strtotime ($data_inicial));
    $data_inicio = new DateTime($data_inicio);

    $data_final = $_POST["data_hora_agenda_fim"];
}

if ($data_final!='') {
    $data_fim = date('Y-m-d', strtotime ($data_final));
    $data_fim = new DateTime($data_fim);
    $hora_fim = date('H:i:s', strtotime ($data_final));
    
    if ($hora_fim=='00:00:00') {
        $data_final = date("Y-m-d H:i:s", strtotime('+1 day',strtotime($data_final)));
    }
}

/*    $diff=date_diff($data_inicio,$data_fim);
    $dias = $diff->format("%a");
}
else {
    $hora_fim = '';
}*/

$id_evento = $_POST["idEvento"];
$tipo_gravacao = $_POST["tipo_gravacao"];
$titulo = $_POST["titulo_agenda"];
$descricao = $_POST["descricao_agenda"];

if ($tipo_gravacao==0) {
    $atividade = $_POST["atividade"];

    if (isset($_POST['local'])) {
        $local = implode(', ', $_POST['local']);
    }
    else {
        $local = '';
    }

    if ($local==''){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Informe a(s) Fazenda(s).'));
        exit;
    }

    $matriz_local = explode(",", $local);
    $quantidade_local = count($matriz_local);

    if ($atividade=='0'){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Informe a Atividade.'));
        exit;
    }
}

if ($titulo==''){
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Informe o Título.'));
    exit;
}

if ($data_inicial==''){
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Informe a Data.'));
    exit;
}

if ($tipo_gravacao==1) {
    if ($data_final=='') {
        $sql = "UPDATE tbl_agenda SET
            tbl_agenda_titulo='$titulo',
            tbl_agenda_descricao='$descricao',
            tbl_agenda_data_inicial='$data_inicial',
            tbl_agenda_data_final=null,
            tbl_agenda_alterado_em='$data_sistema',
            tbl_agenda_alterado_por='$nomeusuario'
        WHERE tbl_agenda_id='$id_evento'";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação. ' . $erro_mysql));
            mysqli_close($conector);
            exit;
        }
    }
    else {
        $sql = "UPDATE tbl_agenda SET
            tbl_agenda_titulo='$titulo',
            tbl_agenda_descricao='$descricao',
            tbl_agenda_data_inicial='$data_inicial',
            tbl_agenda_data_final='$data_final',
            tbl_agenda_alterado_em='$data_sistema',
            tbl_agenda_alterado_por='$nomeusuario'
        WHERE tbl_agenda_id='$id_evento'";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação. ' . $erro_mysql));
            mysqli_close($conector);
            exit;
        }
    }

    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Registro alterado com sucesso.'));
    mysqli_close($conector);
    exit;
} 
else if ($tipo_gravacao==2) {
    $sql = "UPDATE tbl_agenda SET
        tbl_agenda_lixeira = 1,
        tbl_agenda_excluido_em = '$data_sistema',
        tbl_agenda_excluido_por = '$nomeusuario'
        WHERE tbl_agenda_id = '$id_evento'";

    $resultado = mysqli_query($conector,$sql);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação. ' . $erro_mysql));
        mysqli_close($conector);
        exit;
    }

    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Registro excluido com sucesso.'));
    mysqli_close($conector);
    exit;
}
else {
    for ($k=0; $k < $quantidade_local; $k++) { 
        $local = $matriz_local[$k];

        $inicias = pegar_inicias($local, $conector);

        $titulo = $_POST["titulo_agenda"];
        $titulo.= ' '.$inicias;

        if ($data_final=='') {
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
                '$titulo',
                '$descricao',
                '$data_inicial',
                null,
                '$atividade',
                null,            
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                0,
                null,
                null
            )";
            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                header('Content-type: application/json');
                echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao incluir o registro, local: ' .$local. ' - ' . $erro_mysql));
                mysqli_close($conector);
                exit;
            } 
        }
        else {
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
                '$titulo',
                '$descricao',
                '$data_inicial',
                '$data_final',
                '$atividade',
                null,            
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                0,
                null,
                null
            )";
            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                header('Content-type: application/json');
                echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao incluir o registro, local: ' .$local. ' - ' . $erro_mysql));
                mysqli_close($conector);
                exit;
            } 
        }
    }

    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Registro incluído com sucesso.'));
    mysqli_close($conector);
    exit;
}


function pegar_inicias($local, $conector) {

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$local'"); 
    $reg = mysqli_fetch_object($local);

    $nome_fazenda = $reg->tbl_pessoa_nome;

    $nome = preg_split("/((de|da|do|dos|das)?)[\s,_-]+/", $nome_fazenda);
    $iniciais = "";
    foreach($nome as $n) {
        if (strlen($n) > 0) {
            $iniciais .= $n[0];
        }
    }
    return $iniciais;
}


/*
            if ($hora_fim!='' && $hora_fim!='00:00:00') {
                for ($i=0; $i <$dias ; $i++) { 
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
                        '$titulo',
                        '$descricao',
                        '$data_inicial_fazenda',
                        null,
                        '$atividade',
                        null,            
                        '$data_sistema',
                        '$nomeusuario',
                        null,
                        null,
                        0,
                        null,
                        null
                    )";
                    $resultado = mysqli_query($conector,$sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                        header('Content-type: application/json');
                        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao incluir o registro, local: ' .$local. ' - ' . $erro_mysql));
                        mysqli_close($conector);
                        exit;
                    } 

                    $data_inicial_fazenda = date("Y-m-d H:i:s", strtotime('+1 day',strtotime($data_inicial_fazenda)));
                }
            }
            else {
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
                    '$titulo',
                    '$descricao',
                    '$data_inicial_fazenda',
                    '$data_final',
                    '$atividade',
                    null,            
                    '$data_sistema',
                    '$nomeusuario',
                    null,
                    null,
                    0,
                    null,
                    null
                )";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao incluir o registro, local: ' .$local. ' - ' . $erro_mysql));
                    mysqli_close($conector);
                    exit;
                } 
            }

*/
?>