<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");;

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
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css"></script>
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >

</head>

<body>

  <?php

    @ session_start();   

    if(isset($_SESSION['menu_parametros'])) {
        $array_parametroa = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametroa[5] == 0){
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

    $tbl_plano_contas = mysqli_query($conector, "select * from tbl_plano_contas 
                                                      where tbl_plano_contas_nivel=1 and  
                                                            tbl_plano_contas_lixeira=0"); 

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
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Plano de Contas - Incluir</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="gravar_plano_contas.php" enctype="multipart/form-data" id="form_gravar_plano_contas">

                            <div class="panel"> 
                                <div class=panel-body>

                                    <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                      
                                    <div class="row" id="errors"></div>
                      
                                    <ul class="nav nav-tabs m-bot15">
                                        <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="tab-content">
                                                <div class="tab-pane active">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <button type="button" class="btn btn-primary confirma_gravar_plano">Confirmar Inclusão</button>

                                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="codigo_pri" class="control-label">Código Nível 1 </label>
                                        <input name="codigo_pri" type="text" class="form-control" id="codigo_pri" placeholder="Cadastrar novo" maxlength="1" data-toggle='tooltip' data-placement='top' title='Para incluir um novo código Nível 1, digite um código que não esteja listado ao lado, informe o restante dos campos e confirme a inclusão.'>
                                    </div> 

                                    <div class="form-group col-md-6">
                                        <label for="selecione_pri" class="control-label">&nbsp;</label>
                                        <select class="form-control" id="selecione_pri" name="selecione_pri">

                                        <option value="0" selected="selected">Ou Selecione um código já existente</option>

                                        <?php while($reg_plano_contas = mysqli_fetch_object($tbl_plano_contas)) { ?>

                                                <option value="<?php 
                                                   $codigo = substr($reg_plano_contas->tbl_plano_contas_codigo_id, 0, 1);
                                                   echo $codigo ?>">
                                                    
                                                <?php 
                                                    echo $codigo .' - '. $reg_plano_contas->tbl_plano_contas_descricao;
                                                ?>
                                                </option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="row" hidden="true" id="nivel_seg">
                                    <div class="form-group col-md-6">
                                        <label for="codigo_seg" class="control-label">Código Nível 2</label>
                                        <input name="codigo_seg" type="text" class="form-control" id="codigo_seg" placeholder="Cadastrar novo" maxlength="2" data-toggle='tooltip' data-placement='top' title='Para incluir um novo código Nível 2, digite um código que não esteja listado ao lado, informe o restante dos campos e confirme a inclusão.'>
                                    </div> 

                                    <div class="form-group col-md-6">
                                        <label for="selecione_seg" class="control-label">&nbsp;</label>
                                        <select class="form-control" id="selecione_seg" name="selecione_seg">

                                        <option value="" selected="selected">Ou Selecione um código já existente</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row" hidden="true" id="nivel_ter">
                                    <div class="form-group col-md-6">
                                        <label for="codigo_ter" class="control-label">Código Nível 3</label>
                                        <input name="codigo_ter" type="text" class="form-control" id="codigo_ter" maxlength="7">
                                    </div> 

                                    <div class="form-group col-md-6" name="select_ter">
                                        <label for="selecione_ter" class="control-label">&nbsp;</label>
                                        <select class="form-control" id="selecione_ter" name="selecione_ter">
                                        <option value="" selected="selected">...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="descricao_plano_contas" class="control-label"><span class="required">*</span>Descrição</label>
                                        <input name="descricao_plano_contas" type="text" class="form-control" id="descricao_plano_contas" maxlength="100">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="descricao_complementar" class="control-label"></span>Descrição Complementar</label>
                                        <input name="descricao_complementar" type="text" class="form-control" id="descricao_complementar" maxlength="200">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="ref_contabil" class="control-label">Referência da Contabilidade</label>
                                        <input name="ref_contabil" type="text" class="form-control" id="ref_contabil" maxlength="10">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="configuracao_conta" class="control-label">
                                            <span class="required">*</span>Opções da Conta
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="debito_credito"></label>

                                        <label class="radio-inline">
                                            <input type="radio" name="debito_credito" id="debito" value="D" 
                                             required > Débito
                                        </label>

                                        <label class="radio-inline">
                                            <input type="radio" name="debito_credito" id="credito" value="C" 
                                             required> Crédito
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label class="radio-inline">
                                            <input type="radio" name="analitico_sintetico" id="analitico" value="A" 
                                             required > Analítico
                                        </label>

                                        <label class="radio-inline">
                                            <input type="radio" name="analitico_sintetico" id="sintetico" value="S" 
                                            required> Sintético
                                        </label>
                                    </div>
                                </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirma_gravar_plano">Confirmar Inclusão</button>
                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                </div>
                                            </div>
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->
                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

        <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby=" 
            myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Plano de Contas</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby=" 
            myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Plano de Contas</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'tabela_plano_contas.js';
  require 'rodape.php';
?>



