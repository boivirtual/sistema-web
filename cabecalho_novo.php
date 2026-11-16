<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/daterangepicker.css" rel="stylesheet">
  <link href="css/bootstrap-datepicker.css" rel="stylesheet">
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet">

</head>

<body>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php 

        @ session_start(); 
        $nome_usuario = $_SESSION['nome_usuario']; 
        $codigo_usuario = $_SESSION['id_usuario'];
        $foto_usuario = $_SESSION['foto_usuario'];
        $grupo_usuario = $_SESSION['grupo_usuario'];

        $array_manejos = $_SESSION['menu_manejos'];
        $array_movimentacoes = $_SESSION['menu_movimentacoes'];
        $array_financeiros = $_SESSION['menu_financeiros'];
        $array_relatorios = $_SESSION['menu_relatorios'];
        $array_cadastro = $_SESSION['menu_cadastros'];
        $array_parametros = $_SESSION['menu_parametros'];
        $array_relatorios = $_SESSION['menu_relatorios'];
    ?> 
    
    <!--header inicio-->
    <header class="header dark-bg">
      <div class="toggle-nav">
        <div class="icon-reorder tooltips" data-original-title="Menu" data-placement="bottom">
            <i class="icon_menu"></i>
        </div>
      </div>

      <!--logo start-->
      <a href="menu.php" class="logo">Fazendas <span class="lite">Agrolandes</span></a>
      <!--logo end-->

      <div class="top-nav notification-row">
        <ul class="nav pull-right top-menu">
        
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="profile-ava">
                           <?php 
                                include "conecta_mysql.inc";

                                $tab_usuario = mysqli_query($conector, "select * from usuario where id_usuario='$codigo_usuario'");
                                $registro_tabela = mysqli_fetch_object($tab_usuario);
                                $foto = $registro_tabela->foto_usuario;
                                echo "<img src='" .$foto. "'>";
                           ?> 
                    </span>

                    <span class="username"><?php echo $nome_usuario;?> </span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu extended logout">
                    <div class="log-arrow-up"></div>

                    <li class="eborder-top">
                        <?php
                         echo "<a href='form_usuario_editar.php?id=".$codigo_usuario."'><i class='icon_profile'  title='Editar esse registro' ></i>Meu Cadastro</a>";

                      ?>
                    </li>

                    <li>
                        <a href="logout.php"><i class="icon_key_alt"></i> Log Out</a>
                    </li>
                </ul>
            </li> 
        </ul>

        <div>
            <input type="hidden" name="array_manejos" id="array_manejos"
             <?php echo "value='".$array_manejos."'"?>>
            <input type="hidden" name="array_movimentacoes" id="array_movimentacoes"
             <?php echo "value='".$array_movimentacoes."'"?>>
            <input type="hidden" name="array_financeiros" id="array_financeiros"
             <?php echo "value='".$array_financeiros."'"?>>
            <input type="hidden" name="array_relatorios" id="array_relatorios"
             <?php echo "value='".$array_relatorios."'"?>>
            <input type="hidden" name="array_cadastro" id="array_cadastro"
             <?php echo "value='".$array_cadastro."'"?>>
            <input type="hidden" name="array_parametros" id="array_parametros"
             <?php echo "value='".$array_parametros."'"?>>
        </div>

      </div>
    </header>
    <!--header end-->
