<?php
  ob_start();
  header('Content-Type: text/html; charset=utf-8');

  @ session_start(); 
  $cnpj_cliente = $_SESSION['id_cliente'];
 
  $servidor = "localhost";
  $usuario_bd = "root";
  $senha_bd = "a2ngei9Mxh";
  $banco = $cnpj_cliente;
   
  $conector = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  if (mysqli_connect_error()) {
      printf("Falha na conexão: ", mysqli_connect_error());
      exit();
  }

  $bancoselecionado = mysqli_select_db($conector,$banco);

  if ($bancoselecionado === FALSE) {
      printf("Falha na seleção do banco de dados: ", mysqli_error($conector));
      header ("Location: ../index.php");
      exit();
      //exit ("Falha na seleção do banco de dados: " . mysqli_error($conector));
  }

  $servidor = "localhost";
  $usuario_bd = "root";
  $senha_bd = "a2ngei9Mxh";
  $banco = "acesso_boi_virtual";
   
  $conector_acesso = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  if (mysqli_connect_error()) {
      printf("Falha na conexão: ", mysqli_connect_error());
      exit();
  }

  $bancoselecionado = mysqli_select_db($conector_acesso,$banco);

  if ($bancoselecionado === FALSE) {
      exit ("Falha na seleção do banco de dados: " . mysqli_error($conector));
  }
  
  mysqli_query($conector,"SET NAMES 'utf8'");
  mysqli_query($conector,'SET character_set_connection=utf8');
  mysqli_query($conector,'SET character_set_client=utf8');
  mysqli_query($conector,'SET character_set_results=utf8');
?>
