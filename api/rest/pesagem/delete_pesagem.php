<?php
include_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";
require_once __DIR__ . '/../../dao/PesagemDao.php';

// Recebe os dados do Flutter
$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['bd']) && isset($dados['id_pesagem'])) {
    $con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $dados['bd']);
    mysqli_set_charset($con, "utf8");

    $id = intval($dados['id_pesagem']);

    // Só é permitido excluir se a pesagem já estiver finalizada (regra atual, sem checar origem)
    // ou se ainda estiver aberta e pertencer ao aplicativo ('APP')
    $sqlCheck = "SELECT tbl_pesagem_finalizada, tbl_pesagem_origem FROM tbl_pesagem WHERE tbl_pesagem_id = $id LIMIT 1";
    $resCheck = mysqli_query($con, $sqlCheck);
    $regCheck = $resCheck ? mysqli_fetch_assoc($resCheck) : null;

    $permitido = $regCheck && ($regCheck['tbl_pesagem_finalizada'] === 'S' || $regCheck['tbl_pesagem_origem'] === 'APP');

    if (!$permitido) {
        header('Content-type: application/json');
        echo json_encode(["success" => false, "message" => "Pesagem não encontrada ou não pertence ao aplicativo."]);
        mysqli_close($con);
        exit;
    }

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