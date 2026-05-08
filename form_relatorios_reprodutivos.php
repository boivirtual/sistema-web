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
</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_relatorios'])) {
        $array_relatorios = explode("!",$_SESSION['menu_relatorios']);

        if ($array_relatorios[1] == 0){
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
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php";
    ?>
    <!--sidebar end-->


    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i> 
            <span class="titulo">Relatórios Reprodutivos</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-file-alt"></i> 
                    Relatórios Reprodutivos</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="#" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <div  class="form-group opcoes_topo">
                                      <!--
                                        <a href="form_rel_fluxo_caixa.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Fluxo de Caixa Diário"/>
                                        </a>

                                        <a href="form_rel_analise_pagamento.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Análise de Pagamentos"/>
                                        </a>

                                        <a href="form_rel_analise_recebimento.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Análise de Recebimentos"/>
                                        </a>

                                        <a href="form_rel_analise_previsto_realizado.php">
                                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                              value="Análise de Contas Previsto/Realizado"/>
                                        </a> -->
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
  require 'rodape.php';
?>



                
                
