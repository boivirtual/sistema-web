<?php 
    include "conecta_mysql.inc";

$nome_usuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data_atual= date("Y-m-d");

//DADOS DO PASTO ONDE OS ANIMAIS IRÃO SAIR
$pasto_remover_id = $_POST["id_saida"];

$tbl_pasto_sair = mysqli_query($conector, "SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = $pasto_remover_id AND 
          tbl_pasto_lixeira = 0");

$reg_pasto_remover = mysqli_fetch_object($tbl_pasto_sair);

$descricao_lote_remover = $reg_pasto_remover->tbl_pasto_descricao_lote;
$id_lote_remover = $reg_pasto_remover->tbl_pasto_id_lote;
$ano_lote_remover = $reg_pasto_remover->tbl_pasto_ano_lote;
$descricao_lote_1_remover = $reg_pasto_remover->tbl_pasto_descricao_lote_1;
$descricao_lote_2_remover = $reg_pasto_remover->tbl_pasto_descricao_lote_2;
$descricao_lote_3_remover = $reg_pasto_remover->tbl_pasto_descricao_lote_3;
$descricao_lote_4_remover = $reg_pasto_remover->tbl_pasto_descricao_lote_4;
$descricao_lote_5_remover = $reg_pasto_remover->tbl_pasto_descricao_lote_5;
$descricao_lote_6_remover = $reg_pasto_remover->tbl_pasto_descricao_lote_6;

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

$descricao_lote_pasto_destino = $reg_pasto_incluir->tbl_pasto_descricao_lote;

$data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
$data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
$data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
$data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

$tbl_pasto_animais_entrar = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
    WHERE tbl_animal_pasto_id = $pasto_incluir_id");

$qtd_animais_pasto_entrada = mysqli_num_rows($tbl_pasto_animais_entrar);

$dataAtual = new DateTime();
$dataCom = new DateTime($data_com_remover);
$diff = $dataAtual->diff($dataCom);

if ($diff->h + ($diff->days * 24) < 24){
    $query = "UPDATE tbl_pasto SET 
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
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql));
        exit;
    } 
}
else {
    $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_id_lote = null, 
            tbl_pasto_ano_lote = null,
            tbl_pasto_descricao_lote_1 = null,
            tbl_pasto_descricao_lote_2 = null,
            tbl_pasto_descricao_lote_3 = null,
            tbl_pasto_descricao_lote_4 = null,
            tbl_pasto_descricao_lote_5 = null,
            tbl_pasto_descricao_lote_6 = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_sem_animais = '$data_sistema',
            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover'
        WHERE tbl_pasto_id = $pasto_remover_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
        exit;
    } 
}

// UPDADE PARA O PASTO QUE TERÁ OS ANIMAIS INCLUIDOS

$dataAtual = new DateTime();
$dataCom = new DateTime($data_com_incluir);
$diff = $dataAtual->diff($dataCom);

if ($qtd_animais_pasto_entrada==0) {
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
            echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
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
                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                exit;
            } 
        }
        else {
            //if ($qtd_animais_pasto_entrada==0) {
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
                    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            //}
        }
    }
}

// ALTERAR DESCRICAO DO LOTE DOS PASTOS

/* Premissa 1 - Levar todos os animais para outro pasto vazio:
                Mover a Descrição do Lote para o Pasto Destino
                Limpar a Descrição do Lote do Pasto Origem
                Mover a nutrição do dia para pasto Destino
*/ 
       
if ($descricao_lote_remover!='' && $descricao_lote_pasto_destino=='') {
    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = '$descricao_lote_remover',
        tbl_pasto_id_lote = '$id_lote_remover', 
        tbl_pasto_ano_lote = '$ano_lote_remover',
        tbl_pasto_descricao_lote_1 = '$descricao_lote_1_remover',
        tbl_pasto_descricao_lote_2 = '$descricao_lote_2_remover',
        tbl_pasto_descricao_lote_3 = '$descricao_lote_3_remover',
        tbl_pasto_descricao_lote_4 = '$descricao_lote_4_remover',
        tbl_pasto_descricao_lote_5 = '$descricao_lote_5_remover',
        tbl_pasto_descricao_lote_6 = '$descricao_lote_6_remover',
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
    WHERE tbl_pasto_id = $pasto_incluir_id";

    $descricao_lote_pasto_destino = $descricao_lote_remover;
    $descricao_lote_remover = '';

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Destino' . $erro_mysql));
        exit;
    } 

    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = null,
        tbl_pasto_id_lote = null, 
        tbl_pasto_ano_lote = null,
        tbl_pasto_descricao_lote_1 = null,
        tbl_pasto_descricao_lote_2 = null,
        tbl_pasto_descricao_lote_3 = null,
        tbl_pasto_descricao_lote_4 = null,
        tbl_pasto_descricao_lote_5 = null,
        tbl_pasto_descricao_lote_6 = null,
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
    WHERE tbl_pasto_id = $pasto_remover_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a Descrição do Lote do Pasto Origem' . $erro_mysql));
        exit;
    } 

    // SE HOUVER NUTRIÇÃO NO DIA DA TRANSFERENCIA DOS ANIMAIS, ENTÃO DEVERÁ SER MOVIDA PARA O PASTO DESTINO CONFORME A PREMISSA 1

    $query = "UPDATE tbl_nutricao SET
        tbl_nutricao_codigo_pasto = $pasto_incluir_id
        WHERE tbl_nutricao_codigo_pasto = $pasto_remover_id AND 
              tbl_nutricao_data = '$data_atual'";

    $resultado = mysqli_query($conector, $query);

}
else {
    /* Premissa 6 - Levar todos os animais para outro pasto com animais:
                    Manter a Descrição do Lote do Pasto Destino
                    Limpar a Descrição do Lote do Pasto Origem
                    Mover a nutrição do dia para pasto Destino
    */

    $descricao_lote_remover = '';

    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = null,
        tbl_pasto_id_lote = null, 
        tbl_pasto_ano_lote = null,
        tbl_pasto_descricao_lote_1 = null,
        tbl_pasto_descricao_lote_2 = null,
        tbl_pasto_descricao_lote_3 = null,
        tbl_pasto_descricao_lote_4 = null,
        tbl_pasto_descricao_lote_5 = null,
        tbl_pasto_descricao_lote_6 = null,
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
        WHERE tbl_pasto_id = $pasto_remover_id";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql));
        exit;
    }

    // SE HOUVER NUTRIÇÃO NO DIA DA TRANSFERENCIA DOS ANIMAIS, ENTÃO DEVERÁ SER MOVIDA PARA O PASTO DESTINO CONFORME A PREMISSA 1

    $query = "UPDATE tbl_nutricao SET
        tbl_nutricao_codigo_pasto = $pasto_incluir_id
        WHERE tbl_nutricao_codigo_pasto = $pasto_remover_id AND 
              tbl_nutricao_data = '$data_atual'";

    $resultado = mysqli_query($conector, $query);
}

// ALTERA OBS NO PASTO DE ENTRADA
/*if ($observacao_remover!='' && $observacao_incluir!='') {
    $query = "UPDATE tbl_pasto SET
        tbl_pasto_descricao_lote = null,
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
        tbl_pasto_descricao_lote = '$observacao_remover',
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
}*/

// ALTERAÇÃO DA TBL-ANIMAL_PASTO PARA O PASTO DE ENTRADA
$query = "UPDATE tbl_animal_pasto SET
    tbl_animal_pasto_id = $pasto_incluir_id,
    tbl_animal_pasto_alterado_em = '$data_sistema',
    tbl_animal_pasto_alterado_por = '$nome_usuario'

    WHERE tbl_animal_pasto_id = $pasto_remover_id AND 
          tbl_animal_pasto_situacao = 'A'";
        
$resultado = mysqli_query($conector, $query);
$resposta = array('success' => true, 'message' => 'Animais movidos com sucesso.', 'descricao_lote_pasto_destino' => $descricao_lote_pasto_destino, 'descricao_lote_pasto_origem' => $descricao_lote_remover);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar os animais no pasto' . $erro_mysql));
    exit;
} 
else {
    header('Content-type: application/json');
    echo json_encode($resposta);
    exit;
}

?>