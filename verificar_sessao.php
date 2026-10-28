<?php
    // Consulta/renovação da sessão por inatividade — usado por js/sessao_inatividade.js
    // nas telas de digitação longa (inclusão de Compra e de Venda de animais).
    //
    //   renovar=0 -> só consulta: NÃO conta como atividade (não empurra o prazo).
    //   renovar=1 -> o usuário está digitando na tela: conta como atividade.
    //
    // Toda a regra de expiração continua em conecta_mysql.inc: se a sessão já
    // expirou, o próprio include responde HTTP 401 (JSON) e encerra, e o
    // scripts.js exibe o modal "Sessão Expirada".
    @ session_start();
    $ultima_atividade_anterior = isset($_SESSION['ultima_atividade']) ? $_SESSION['ultima_atividade'] : null;

    include "conecta_mysql.inc";
    mysqli_close($conector);

    $renovar = (isset($_POST['renovar']) && $_POST['renovar'] == '1');

    if (!$renovar && $ultima_atividade_anterior !== null) {
        // conecta_mysql.inc acabou de atualizar o "último uso"; desfaz, porque
        // uma simples consulta não pode manter a sessão viva para sempre
        $_SESSION['ultima_atividade'] = $ultima_atividade_anterior;
    }

    $restante = TEMPO_LIMITE_INATIVIDADE_SEGUNDOS - (time() - $_SESSION['ultima_atividade']);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'ativa'    => true,
        'usuario'  => $_SESSION['id_usuario'],     // usados no modal de senha quando a sessão expirar
        'nome'     => mb_check_encoding($_SESSION['nome_usuario'], 'UTF-8') ? $_SESSION['nome_usuario'] : utf8_encode($_SESSION['nome_usuario']),
        'limite'   => TEMPO_LIMITE_INATIVIDADE_SEGUNDOS,
        'restante' => max(0, $restante)
    ));
?>
