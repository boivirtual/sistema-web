<?php 
    include "conecta_mysql.inc";

$nome_usuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data = date("Y-m-d");

//DADOS DO PASTO ONDE OS ANIMAIS IRÃO SAIR
$pasto_remover_id = $_POST["id_saida"];

$tbl_pasto_sair = mysqli_query($conector, "SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = $pasto_remover_id AND 
          tbl_pasto_lixeira = 0");

$reg_pasto_remover = mysqli_fetch_object($tbl_pasto_sair);

$observacao_remover = $reg_pasto_remover->tbl_pasto_observacao;
$data_com_remover = $reg_pasto_remover->tbl_pasto_data_com_animais;
$data_com_remover_anterior = $reg_pasto_remover->tbl_pasto_data_com_animais_anterior;
$data_sem_remover = $reg_pasto_remover->tbl_pasto_data_sem_animais;
$data_sem_remover_anterior = $reg_pasto_remover->tbl_pasto_data_sem_animais_anterior;


//DADOS DO PASTO ONDE OS ANIMAIS IRÃO ENTRAR
$pasto_incluir_id = $_POST["id_entrada"];

$tbl_pasto_entrar = mysqli_query($conector, "SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = $pasto_incluir_id AND 
          tbl_pasto_lixeira = 0");

$reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

$observacao_incluir = $reg_pasto_incluir->tbl_pasto_observacao;
$data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
$data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
$data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
$data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

$tbl_pasto_animais_entrar = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
    WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");

$num_rows_incluir = mysqli_num_rows($tbl_pasto_animais_entrar);

if($_POST["transfere"] == 1){
    $query = "UPDATE tbl_nutricao SET
            tbl_nutricao_codigo_pasto = $pasto_incluir_id
            WHERE tbl_nutricao_codigo_pasto = $pasto_remover_id AND tbl_nutricao_data = '$data'";

    $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
}

// REVER ESSA ROTINA
//$dataAtual = new DateTime();
//$dataAnterior = new DateTime($reg_pasto_remover->tbl_pasto_data_sem_animais_anterior);
//$dataSem = new DateTime($reg_pasto_remover->tbl_pasto_data_sem_animais);
//$diff = $dataAtual->diff($dataSem);

//if($diff->h + ($diff->days * 24) < 24){
   // $dataSemAnimais = $dataAnterior;
   // $dataSemAnimais = $dataSemAnimais->format('Y-m-d H:i:s');
//}

// UPDATE PARA O PASTO QUE TERÁ OS ANIMAIS REMOVIDOS

$dataAtual = new DateTime();
$dataCom = new DateTime($data_com_remover);
$diff = $dataAtual->diff($dataCom);

if ($diff->h + ($diff->days * 24) < 24){
    $query = "UPDATE tbl_pasto SET 
            tbl_pasto_observacao = '$observacao_incluir',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_com_animais = '$data_sem_remover_anterior',
            tbl_pasto_data_com_animais_anterior = '$data_sem_remover_anterior',
            tbl_pasto_data_sem_animais = '$data_sem_remover_anterior',
            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover_anterior'
        WHERE tbl_pasto_id = $pasto_remover_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql));
        exit;
    } 
}
else {
    $query = "UPDATE tbl_pasto SET 
            tbl_pasto_observacao = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_sem_animais = '$data_sistema',
            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover'
        WHERE tbl_pasto_id = $pasto_remover_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
        exit;
    } 
}

// UPDADE PARA O PASTO QUE TERÁ OS ANIMAIS INCLUIDOS

$dataAtual = new DateTime();
$dataCom = new DateTime($data_com_incluir);
$diff = $dataAtual->diff($dataCom);

if ($diff->h + ($diff->days * 24) < 24){
    $query = "UPDATE tbl_pasto SET
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario',
        tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
        tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
        tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
        tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
    WHERE tbl_pasto_id = $pasto_incluir_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
        exit;
    } 
}
else {
    $dataAtual = new DateTime();
    $dataSem = new DateTime($data_sem_incluir);
    $diff = $dataAtual->diff($dataSem);

    if ($diff->h + ($diff->days * 24) < 24){
        $query = "UPDATE tbl_pasto SET
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
            tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
            tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
            tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
        WHERE tbl_pasto_id = $pasto_incluir_id";

        $resultado = mysqli_query($conector, $query);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
            exit;
        } 
    }
    else {
        if ($num_rows_incluir==0) {
            $query = "UPDATE tbl_pasto SET
                tbl_pasto_alterado_em = '$data_sistema',
                tbl_pasto_alterado_por = '$nome_usuario',
                tbl_pasto_data_com_animais = '$data_sistema',
                tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
            WHERE tbl_pasto_id = $pasto_incluir_id";

            $resultado = mysqli_query($conector, $query);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                header('Content-type: application/json');
                echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                exit;
            } 
        }
    }
}

// ALTERA OBS NO PASTO DE ENTRADA
if ($observacao_remover!='' && $observacao_incluir!='') {
    $query = "UPDATE tbl_pasto SET
        tbl_pasto_observacao = null,
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
    WHERE tbl_pasto_id = $pasto_incluir_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar a Descrição do Lote' . $erro_mysql));
        exit;
    } 
}
else if ($observacao_remover!='' && $observacao_incluir=='') {
    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_observacao = '$observacao_remover',
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
    WHERE tbl_pasto_id = $pasto_incluir_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar a Descrição do Lote' . $erro_mysql));
        exit;
    } 
}

// ALTERAÇÃO DA TBL-ANIMAL_PASTO PARA O PASTO DE ENTRADA
$query = "UPDATE tbl_animal_pasto SET
    tbl_animal_pasto_id = $pasto_incluir_id,
    tbl_animal_pasto_alterado_em = '$data_sistema',
    tbl_animal_pasto_alterado_por = '$nome_usuario'

    WHERE tbl_animal_pasto_id = $pasto_remover_id AND 
          tbl_animal_pasto_situacao = 'A'";
        
$resultado = mysqli_query($conector, $query);
$resposta = array('success' => true, 'message' => 'Animais movidos com sucesso.');
$erro_mysql = mysqli_error($conector);

if (!$resultado){
    header('Content-type: application/json');
    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar os animais no pasto' . $erro_mysql));
    exit;
} 
else {
    header('Content-type: application/json');
    echo json_encode($resposta);
    exit;
}

?>