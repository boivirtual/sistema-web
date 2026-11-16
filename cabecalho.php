    <?php

    @session_start();
    $nome_usuario = $_SESSION['nome_usuario'];
    $codigo_usuario = $_SESSION['id_usuario'];
    $foto_usuario = $_SESSION['foto_usuario'];
    $grupo_usuario = $_SESSION['grupo_usuario'];
    $id_empresa = $_SESSION['id_cliente'];
    $nome_fantasia = $_SESSION['nome_empresa'];

    if ($id_empresa == '04527017000152') {
        $icone_empresa = 'img/' . $id_empresa . '.png';
    } else {
        $icone_empresa = 'img/' . $id_empresa . '.ico';
    }

    $array_manejo_animais = $_SESSION['menu_manejo_animais'];
    $array_manejo_reprodutivo = $_SESSION['menu_manejo_reprodutivo'];
    $array_suplemento_alimentar =  $_SESSION['menu_suplemento_alimentar'];
    $array_controle_sanitario =  $_SESSION['menu_controle_sanitario'];
    $array_gestao_adm = $_SESSION['menu_gestao_adm'];
    $array_cadastro = $_SESSION['menu_cadastros'];
    $array_parametros = $_SESSION['menu_parametros'];
    $array_relatorios = $_SESSION['menu_relatorios'];
    ?>

    <!--header inicio-->
<div class="content" style="position: relative;">
    <header class="header dark-bg" style="z-index: 1;">
        <div class="toggle-nav">
            <div class="icon-reorder tooltips" data-original-title="Menu" data-placement="bottom">
                <i class="icon_menu" id="abre_sidebar"></i>
            </div>
        </div>

        <a href="menu.php" class="logo">
            <?php
            if ($id_empresa == '04527017000152') :
            ?>

                <img src="<?php echo $icone_empresa; ?>" alt="" class="logo-img-25" id="logo_empresa_menu">

            <?php
            else :
            ?>

                <img src="<?php echo $icone_empresa; ?>" alt="" class="logo-img" id="logo_empresa_menu">

            <?php
            endif;
            ?>

            <span class="nome_empresa lite"><?php echo $nome_fantasia; ?></span>
        </a>

        <div class="top-nav notification-row mais"> 
            <ul class="nav pull-right top-menu">
                <li>
                    <form method="get" action="#" style="margin-right: 100px;">
                        <input type="text" class="form-control" placeholder="O que você precisa?" name="nome_pesquisa" id="nome_pesquisa" style="margin-top: 5px; margin-right: 100px; border-radius: 100px;" oninput="ler_busca()">

                        <div class="row card card-title-busca" id="tela_busca" style="position:absolute; margin-top: 3px; padding-bottom: 10px; width: 130%; word-wrap: break-word;" hidden>
                            <div id="lido" style="margin-right: 5px;"></div>
                        </div>
                    </form>
                </li>
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="profile-ava">
                            <?php
                            include "conecta_mysql.inc";

                            $tab_usuario = mysqli_query($conector_acesso, "select * from usuario where id_usuario='$codigo_usuario'");
                            $registro_tabela = mysqli_fetch_object($tab_usuario);
                            $foto = $registro_tabela->foto_usuario;
                            echo "<img src='" . $foto . "'>";
                            ?>

                        </span>

                        <span class="username"><?php echo $nome_usuario; ?> </span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu extended logout">
                        <div class="log-arrow-up"></div>

                        <li class="eborder-top">
                            <?php
                            echo "<a href='form_usuario_editar.php?id=" . $codigo_usuario . "'><i class='icon_profile'  title='Editar esse registro' ></i>Meu Cadastro</a>";
                            ?>
                        </li>

                        <?php
                        if (
                            $id_empresa == 97174041604 &&
                            ($grupo_usuario == 01 || $codigo_usuario == 1)
                        ) {
                            echo '<li class="eborder-top">';

                            echo "<a href='form_cliente_boi_virtual.php'><i class='icon_group' title='Clientes cadastrados no Boi Virtual' ></i>Validar Clientes</a>";

                            echo '</li>';
                        }
                        ?>

                        <li>
                            <a href="logout.php"><i class="icon_key_alt"></i> Fechar</a>
                        </li>
                    </ul>
                </li>

            </ul>

            <div>
                <input type="hidden" name="array_manejo_animais" id="array_manejo_animais" <?php echo "value='" . $array_manejo_animais . "'" ?>>
                <input type="hidden" name="array_manejo_reprodutivo" id="array_manejo_reprodutivo" <?php echo "value='" . $array_manejo_reprodutivo . "'" ?>>
                <input type="hidden" name="array_suplemento_alimentar" id="array_suplemento_alimentar" <?php echo "value='" . $array_suplemento_alimentar . "'" ?>>
                <input type="hidden" name="array_controle_sanitario" id="array_controle_sanitario" <?php echo "value='" . $array_controle_sanitario . "'" ?>>
                <input type="hidden" name="array_gestao_adm" id="array_gestao_adm" <?php echo "value='" . $array_gestao_adm . "'" ?>>
                <input type="hidden" name="array_cadastro" id="array_cadastro" <?php echo "value='" . $array_cadastro . "'" ?>>
                <input type="hidden" name="array_parametros" id="array_parametros" <?php echo "value='" . $array_parametros . "'" ?>>
                <input type="hidden" name="array_relatorios" id="array_relatorios" <?php echo "value='" . $array_relatorios . "'" ?>>
            </div>

        </div>
    </header>
</div>

    <!--header end-->