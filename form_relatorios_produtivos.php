<?php
    include "valida_sessao.inc";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/jquery-ui.css" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_relatorios'])) {
        $array_relatorios = explode("!",$_SESSION['menu_relatorios']);

        if ($array_relatorios[0] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuou o login!</span>';  
        echo '</div>';         
        exit;
    }

    $controle_estoque = $_SESSION['controle_estoque'];

    $_SESSION['tipo_rel_historico_animais']='G'; 
    $_SESSION['categoria_historico_animais']=''; 
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->


    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i> 
            <span class="titulo">Relatórios Produtivos</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-file-alt"></i> 
                    Relatórios Produtivos</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="#" enctype="multipart/form-data" >

                            <h3 style="margin: 0; margin-bottom: 10px;">Animais</h3>
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <div  class="form-group opcoes_topo">

                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>
                                            <a href="form_rel_historico_animais.php?tipo=1">
                                                <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                                  value="Histórico de Animais"/>
                                            </a>
                                        <?php
                                           else:
                                        ?>

                                            <a href="form_rel_historico_pesagem_lote.php?tipo=1">
                                                <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                                  value="Histórico de Pesagem"/>
                                            </a>

                                        <?php
                                            endif;
                                        ?>

                                        <a href="form_rel_estoque_animais.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Estoque de Animais"/>
                                        </a>

                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>
                                        <a href="form_rel_gmd.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Ganho de Peso"/>
                                        </a>

                                        <?php
                                            else:
                                        ?>

                                        <a href="form_rel_gmd_lote.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Ganho de Peso"/>
                                        </a>

                                        <?php
                                            endif;
                                        ?>


                                        <a href="form_rel_mapa_gados.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Mapa de Gado"/>
                                        </a>
                                    </div> 
                                </div>
                            </div>
                        </form>
                    </div>
    	        </div>
	        </div>

            <?php
                if ($controle_estoque=='I') :
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="#" enctype="multipart/form-data" >
                            <h3 style="margin: 0; margin-bottom: 10px;">Reprodutivos</h3>

                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <div  class="form-group opcoes_topo">

                                        <a href="form_rel_situacao_reprodutiva.php?tipo=1">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                            value="Situação Reprodutiva"/>
                                        </a>

                                        <a href="form_rel_indices_reprodutivos.php">
                                            <input type="button" class="btn btn-info " aria-label="Left Align" 
                                              value="Indices Reprodutivos"/>
                                        </a>
                                    </div> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
                endif;
            ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="#" enctype="multipart/form-data" >

                            <h3 style="margin: 0; margin-bottom: 10px;">Outros</h3>
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <div  class="form-group opcoes_topo">
                                        <a href="form_rel_consumo_nutricao.php?tipo=1">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Consumo de Nutrição"/>
                                        </a>

                                        <a href="form_rel_registro_chuvas.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Chuvas"/>
                                        </a>
                                    </div> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- page end-->
        </section>
    </section>
</section>

<?php 
  $javascript_file_name = 'tabela_animais.js';
  require 'rodape.php';
?>



                
                
