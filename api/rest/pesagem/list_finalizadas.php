<?php
include_once __DIR__ . "/../../../conecta_mysql_credenciais.inc";
// list_finalizadas.php
require_once __DIR__ . '/../../dao/PesagemDao.php';

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['bd']) && isset($dados['fazendas'])) {
    $con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $dados['bd']);
    mysqli_set_charset($con, "utf8");

    // Filtra pelos IDs das fazendas que o usuário tem acesso
    $ids = implode(',', $dados['fazendas']);
    
    // Filtramos apenas as FINALIZADAS ('S'), não excluídas (lixeira=0) e limitamos aos últimos 4 registros
    $sql = "SELECT p.*, f.tbl_pessoa_nome as fazenda_nome 
            FROM tbl_pesagem p
            LEFT JOIN tbl_pessoa f ON p.tbl_pesagem_codigo_local = f.tbl_pessoa_id
            WHERE p.tbl_pesagem_codigo_local IN ($ids) 
            AND p.tbl_pesagem_finalizada = 'S' 
            AND p.tbl_pesagem_lixeira = 0
            ORDER BY p.tbl_pesagem_data DESC, p.tbl_pesagem_id DESC
            LIMIT 4";

    $res = mysqli_query($con, $sql);
    $lista = [];
    while($row = mysqli_fetch_assoc($res)){
        $lista[] = $row;
    }
    
    header('Content-type: application/json');
    echo json_encode($lista);
}