<?php
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_parametros'])) {
        $array_gestao_adm = explode("!",$_SESSION['menu_parametros']);

        if ($array_gestao_adm[3] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';  
        echo '</div>';         
        exit;
    }

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $array_local = $_SESSION['local_pastos'];
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
            <span class="caminho-programa">Parametros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Escore</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-map-o"></i> Escores</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div id="lista_escore">
                    </div>

                </div>
            </div>
            <!-- page end-->


            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Escore - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_escore.php" enctype="multipart/form-data" id="form_gravar_escore">
                                <input name="codigo_conta" type="hidden" id="codigo_conta">
                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="descricao" class="control-label"><span class="required">*</span> Descrição</label>
                                                 <input name="descricao" type="text" class="form-control" id="descricao" onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="aparencia" class="control-label"><span class="required">*</span> Aparência</label>
                                                <input name="aparencia" type="text" class="form-control" id="aparencia" onkeyup="maiuscula(this)">
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_escore()">Confirmar Inclusão</button>
                                                <button type="button" class="btn btn-info pull-right voltar_inclusao" onClick="voltar_inclusao()">Voltar</button>
                                                <button type="button" class="btn btn-info pull-right voltar" data-dismiss="modal">Voltar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Escore </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_inclusao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Escore </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_novo();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Escore - Erro</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

<?php 
  $javascript_file_name = 'tabela_escore.js';
  require 'rodape.php';
?>
