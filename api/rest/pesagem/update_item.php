<?php
    require_once __DIR__ . '/../../dao/PesagemDao.php';

    header('Content-type: application/json');

    $dados = json_decode(file_get_contents('php://input'), true);

    $pesagem_id = $dados['pesagem_id'] ?? $dados['id_pesagem'] ?? null;
    $numero_item = $dados['numero_item'] ?? null;
    $criterios_lista = $dados['criterios_lista'] ?? null;

    if ($pesagem_id && $numero_item) {
        $dao = new PesagemDao($dados['bd']);
        
        $sucesso = $dao->alterarItem(
            $pesagem_id, 
            $numero_item, 
            $dados['peso'], 
            $dados['obs'],
            $dados['criterio_apartacao']
        );
        
        if ($sucesso && $criterios_lista && is_array($criterios_lista)) {
            $listaString = implode(', ', $criterios_lista);
            $sqlP = "UPDATE tbl_pesagem 
                     SET tbl_pesagem_criterios_apartacao = '$listaString' 
                     WHERE tbl_pesagem_id = $pesagem_id";
            mysqli_query($dao->getConexao(), $sqlP);
        }
        
        echo json_encode(["success" => $sucesso]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Dados incompletos: pesagem_id ou numero_item nao recebidos."
        ]);
    }
