<?php
include_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";
header('Content-type: application/json');

// Mantendo o seu padrão de importação se necessário
// require_once __DIR__ . '/../../dao/PesagemDao.php';

// Pegando os dados no formato que você já usa (Array associativo)
$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['bd']) && isset($dados['id_pesagem'])) {
    
    // Conectando usando os seus dados de acesso
    $con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $dados['bd']);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        echo json_encode(array('success' => false, 'message' => 'Falha na conexão com o banco.'));
        exit;
    }

    $id_pesagem = $dados['id_pesagem'];
    $motivo = intval($dados['motivo']); // Converte "001" para 1
    $lote = $dados['lote'];

    // Só é permitido finalizar pela pesagem se ela pertencer ao aplicativo ('APP')
    $sql_check = "SELECT tbl_pesagem_origem FROM tbl_pesagem WHERE tbl_pesagem_id='$id_pesagem' LIMIT 1";
    $res_check = mysqli_query($con, $sql_check);
    $reg_check = $res_check ? mysqli_fetch_assoc($res_check) : null;

    if (!$reg_check || $reg_check['tbl_pesagem_origem'] !== 'APP') {
        echo json_encode(array('success' => false, 'message' => 'Pesagem não encontrada ou não pertence ao aplicativo.'));
        mysqli_close($con);
        exit;
    }

    // 1. Marcar a pesagem principal como finalizada ('S')
    $sql_fin = "UPDATE tbl_pesagem SET tbl_pesagem_finalizada='S' WHERE tbl_pesagem_id='$id_pesagem'";
    mysqli_query($con, $sql_fin);

    // 2. Buscar todos os animais desta pesagem para atualizar a ficha individual deles
    $query_itens = "SELECT * FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id='$id_pesagem'";
    $item_pesagem = mysqli_query($con, $query_itens);

    while ($reg_item = mysqli_fetch_object($item_pesagem)) {
        $id_animal = $reg_item->tbl_ite_pesagem_codigo_id_animal;
        $peso = $reg_item->tbl_ite_pesagem_peso;
        $data_pesagem = $reg_item->tbl_ite_pesagem_data_emissao;

        // Lógica de atualização conforme o Motivo (Nascimento, Desmama ou Outros)
        if ($motivo == 1) { // Nascimento
            $sql_animal = "UPDATE tbl_animais SET 
                    tbl_animal_primeiro_peso='$peso', 
                    tbl_animal_lote_primeiro_peso='$lote', 
                    tbl_animal_data_primeiro_peso='$data_pesagem', 
                    tbl_animal_ultimo_peso='$peso', 
                    tbl_animal_lote_ultimo='$lote', 
                    tbl_animal_data_ultimo='$data_pesagem' 
                WHERE tbl_animal_codigo_id='$id_animal'";
        } 
        else if ($motivo == 2 || $motivo == 8) { // Desmama
            $sql_animal = "UPDATE tbl_animais SET 
                    tbl_animal_peso_desmama='$peso', 
                    tbl_animal_lote_desmama='$lote', 
                    tbl_animal_data_desmama='$data_pesagem', 
                    tbl_animal_ultimo_peso='$peso', 
                    tbl_animal_lote_ultimo='$lote', 
                    tbl_animal_data_ultimo='$data_pesagem' 
                WHERE tbl_animal_codigo_id='$id_animal'";
        } 
        else { // Controle Ganho de Peso e outros
            $sql_animal = "UPDATE tbl_animais SET 
                    tbl_animal_ultimo_peso='$peso', 
                    tbl_animal_lote_ultimo='$lote', 
                    tbl_animal_data_ultimo='$data_pesagem' 
                WHERE tbl_animal_codigo_id='$id_animal'";
        }

        mysqli_query($con, $sql_animal);
    }

    echo json_encode(array('success' => true, 'message' => 'Pesagem finalizada e pesos atualizados!'));
    mysqli_close($con);

} else {
    echo json_encode(array('success' => false, 'message' => 'Dados insuficientes para finalizar.'));
}
?>