<?php
include_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";
require_once __DIR__ . '/../../dao/PesagemDao.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['bd']) && isset($dados['id_pesagem'])) {
    $con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $dados['bd']);
    mysqli_set_charset($con, "utf8");

    $id = intval($dados['id_pesagem']);

    // 1. Busca dados da Pesagem
    $sqlP = "SELECT * FROM tbl_pesagem WHERE tbl_pesagem_id = $id";
    $resP = mysqli_query($con, $sqlP);
    $pesagem = mysqli_fetch_assoc($resP);

    // 2. Busca Itens já pesados
    $sqlI = "SELECT * FROM tbl_item_pesagem 
             WHERE tbl_ite_pesagem_numero_id = $id 
             ORDER BY tbl_ite_pesagem_numero_item DESC";
    $resI = mysqli_query($con, $sqlI);
    
    $itens = [];
    while($row = mysqli_fetch_assoc($resI)) {
        $itens[] = $row;
    }

    echo json_encode([
        "success" => true,
        "pesagem" => $pesagem,
        "itens" => $itens
    ]);
}