<?php
include_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";
require_once __DIR__ . '/../../dao/PesagemDao.php';

// Recebe os dados do Flutter
$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['bd']) && isset($dados['id_pesagem'])) {
    $con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $dados['bd']);
    mysqli_set_charset($con, "utf8");

    $id = intval($dados['id_pesagem']);

    // Iniciamos uma transação para garantir a segurança dos dados
    mysqli_begin_transaction($con);

    try {
        // 1. Remove os itens da pesagem
        $sqlItens = "DELETE FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id = $id";
        mysqli_query($con, $sqlItens);

        // 2. Remove o cabeçalho da pesagem
        $sqlCabecalho = "DELETE FROM tbl_pesagem WHERE tbl_pesagem_id = $id";
        mysqli_query($con, $sqlCabecalho);

        // Se chegou aqui sem erros, confirma as exclusões
        mysqli_commit($con);
        
        header('Content-type: application/json');
        echo json_encode(["success" => true, "message" => "Pesagem removida com sucesso"]);

    } catch (Exception $e) {
        // Se algo deu errado, desfaz tudo
        mysqli_rollback($con);
        
        header('Content-type: application/json');
        echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $e->getMessage()]);
    }

    mysqli_close($con);
} else {
    header('Content-type: application/json');
    echo json_encode(["success" => false, "message" => "Dados insuficientes"]);
}