<?php
  $servidor = "127.0.0.1";
  $usuario_bd = "root";
  $senha_bd = "a2ngei9Mxh";
  $banco = "acesso_boi_virtual";
   
  $conector_acesso = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  if (mysqli_connect_error()) {
      header('Content-type: application/json');
      echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação.' . mysqli_connect_error()));
      exit;
  }

  $bancoselecionado = mysqli_select_db($conector_acesso,$banco);

  if ($bancoselecionado === FALSE) {
      header('Content-type: application/json');
      echo json_encode(array('error' => true, 'message' => 'Erro ao acessar o banco de dados'));
      exit;
  }
  
?>
