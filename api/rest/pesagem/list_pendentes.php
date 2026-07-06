<?php
require_once __DIR__ . '/../../dao/PesagemDao.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['bd']) && isset($dados['fazendas'])) {
    $con = mysqli_connect('localhost', 'root', 'a2ngei9Mxh', $dados['bd']);
    mysqli_set_charset($con, "utf8");

    // Filtra pelos IDs das fazendas que o usuário tem acesso
    $ids = implode(',', $dados['fazendas']);
    
    $sql = "SELECT p.*, f.tbl_pessoa_nome as fazenda_nome 
            FROM tbl_pesagem p
            LEFT JOIN tbl_pessoa f ON p.tbl_pesagem_codigo_local = f.tbl_pessoa_id
            WHERE p.tbl_pesagem_codigo_local IN ($ids)
            AND p.tbl_pesagem_finalizada = 'N'
            AND p.tbl_pesagem_lixeira = 0
            AND p.tbl_pesagem_origem = 'APP'
            ORDER BY p.tbl_pesagem_id DESC";

    $res = mysqli_query($con, $sql);
    $lista = [];
    while($row = mysqli_fetch_assoc($res)){
        $lista[] = $row;
    }
    
    header('Content-type: application/json');
    echo json_encode($lista);
}