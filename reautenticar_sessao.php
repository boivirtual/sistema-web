<?php
    // Renova a sessão expirada sem sair da tela — usado por js/sessao_inatividade.js
    // nas telas de digitação longa (inclusão de Compra e de Venda de animais).
    //
    // Quando a sessão expira, conecta_mysql.inc apaga a sessão inteira. Aqui o
    // usuário informa a senha de novo; se conferir, a sessão é montada outra vez
    // com os mesmos dados do login (../login.php) e a tela continua de onde parou,
    // sem perder o que já foi digitado.
    //
    //   POST id_usuario -> usuário que estava logado na tela (vem do verificar_sessao.php)
    //   POST senha      -> senha informada no modal "Sessão Expirada"
    @ session_start();

    header('Content-Type: application/json; charset=utf-8');

    function responder_reautenticacao($sucesso, $mensagem) {
        echo json_encode(array('success' => $sucesso, 'message' => $mensagem));
        exit();
    }

    $id_usuario = isset($_POST['id_usuario']) ? trim($_POST['id_usuario']) : '';
    $senha      = isset($_POST['senha']) ? $_POST['senha'] : '';

    if ($id_usuario === '' || !ctype_digit($id_usuario)) {
        responder_reautenticacao(false, 'Não foi possível identificar o usuário. Clique em Ir para o Login.');
    }

    if ($senha === '') {
        responder_reautenticacao(false, 'Informe a senha.');
    }

    require "conecta_mysql_credenciais.inc";
    $conector_acesso = @mysqli_connect($servidor, $usuario_bd, $senha_bd, 'acesso_boi_virtual');

    if (!$conector_acesso) {
        responder_reautenticacao(false, 'Não foi possível conectar ao banco de dados. Tente novamente.');
    }

    $st = mysqli_prepare($conector_acesso, "SELECT id_usuario, cpf_usuario, cnpj_cpf_empresa_usuario, nome_usuario,
                                                   email_usuario, grupo_usuario, senha_usuario, foto_usuario, situacao_usuario
                                              FROM usuario
                                             WHERE id_usuario = ?");
    mysqli_stmt_bind_param($st, 'i', $id_usuario);
    mysqli_stmt_execute($st);
    $usuario = mysqli_fetch_object(mysqli_stmt_get_result($st));
    mysqli_stmt_close($st);
    mysqli_close($conector_acesso);

    if (!$usuario) {
        responder_reautenticacao(false, 'Usuário não encontrado. Clique em Ir para o Login.');
    }

    if ($usuario->senha_usuario !== $senha) {
        responder_reautenticacao(false, 'Senha incorreta.');
    }

    if ($usuario->situacao_usuario == 'D') {
        responder_reautenticacao(false, 'Usuário desativado.');
    }

    // senha conferida: troca o id da sessão e monta tudo de novo, igual ao login
    session_regenerate_id(true);
    $_SESSION = array();
    $_SESSION['id_cliente']       = $usuario->cnpj_cpf_empresa_usuario;
    $_SESSION['ultima_atividade'] = time();

    include "conecta_mysql.inc";
    header('Content-Type: application/json; charset=utf-8');   // conecta_mysql.inc troca para text/html

    $cnpj = mysqli_real_escape_string($conector, $usuario->cnpj_cpf_empresa_usuario);
    $tblEmpresa = mysqli_query($conector, "SELECT tbl_empresa_nome_fantasia, tbl_empresa_controle_pesagem
                                             FROM tbl_empresa
                                            WHERE tbl_empresa_cpf_cnpj = '$cnpj'");
    $registro_empresa = mysqli_fetch_object($tblEmpresa);

    $grupo = mysqli_real_escape_string($conector, $usuario->grupo_usuario);
    $tblGrupoacesso = mysqli_query($conector, "SELECT array_menu_manejo_animais_grupo_acesso, array_menu_manejo_reprodutivo_grupo_acesso,
                                                      array_menu_suplemento_alimentar_grupo_acesso, array_menu_controle_sanitario_grupo_acesso,
                                                      array_menu_gestao_adm_grupo_acesso, array_menu_cadastro_grupo_acesso,
                                                      array_menu_parametro_grupo_acesso, array_menu_relatorios_grupo_acesso
                                                 FROM grupos_acessos
                                                WHERE codigo_grupo_acesso = '$grupo'");
    $registro_grupo = mysqli_fetch_array($tblGrupoacesso);

    $_SESSION['nome_empresa']     = $registro_empresa ? $registro_empresa->tbl_empresa_nome_fantasia : '';
    $_SESSION['controle_estoque'] = $registro_empresa ? $registro_empresa->tbl_empresa_controle_pesagem : '';

    $_SESSION['id_usuario']    = $usuario->id_usuario;
    $_SESSION['senha_usuario'] = $usuario->senha_usuario;
    $_SESSION['foto_usuario']  = $usuario->foto_usuario;
    $_SESSION['nome_usuario']  = $usuario->nome_usuario;
    $_SESSION['email_usuario'] = $usuario->email_usuario;
    $_SESSION['grupo_usuario'] = $usuario->grupo_usuario;
    $_SESSION['data_sistema']  = date("Y-m-d");
    $_SESSION['ultimo_cliente_cadastrado'] = 0;
    $_SESSION['abrir_agenda']    = '';
    $_SESSION['validar_cliente'] = '';

    $_SESSION['menu_manejo_animais']       = $registro_grupo['array_menu_manejo_animais_grupo_acesso'];
    $_SESSION['menu_manejo_reprodutivo']   = $registro_grupo['array_menu_manejo_reprodutivo_grupo_acesso'];
    $_SESSION['menu_suplemento_alimentar'] = $registro_grupo['array_menu_suplemento_alimentar_grupo_acesso'];
    $_SESSION['menu_controle_sanitario']   = $registro_grupo['array_menu_controle_sanitario_grupo_acesso'];
    $_SESSION['menu_gestao_adm']           = $registro_grupo['array_menu_gestao_adm_grupo_acesso'];
    $_SESSION['menu_cadastros']            = $registro_grupo['array_menu_cadastro_grupo_acesso'];
    $_SESSION['menu_parametros']           = $registro_grupo['array_menu_parametro_grupo_acesso'];
    $_SESSION['menu_relatorios']           = $registro_grupo['array_menu_relatorios_grupo_acesso'];

    $cpf_usuario  = mysqli_real_escape_string($conector, $usuario->cpf_usuario);
    $nome_usuario = mysqli_real_escape_string($conector, $usuario->nome_usuario);
    $data_sistema = date("Y-m-d H:i:s");
    mysqli_query($conector, "INSERT INTO tbl_historico_acesso (cpf_historico_acesso, nome_historico_acesso,
                                                               historico_acesso, incluido_em_historico_acesso)
                                  VALUES ('$cpf_usuario', '$nome_usuario', 'Acesso ao Sistema (sessão renovada)', '$data_sistema')");
    mysqli_close($conector);

    responder_reautenticacao(true, 'Sessão renovada.');
?>
