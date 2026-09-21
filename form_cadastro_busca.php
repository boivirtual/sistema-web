<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
    <title>Boi Virtual</title>

    <!-- Bootstrap CSS -->
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
        $array_parametros = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametros[13] == 0){
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

    $tbl_ajuda = mysqli_query($conector, "SELECT * FROM tbl_ajuda_url
        ORDER BY tbl_nome_programa_url ASC"); 

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Parametros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Ajuda</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-question-circle-o"></i> Cadastro de Ajuda</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Nova" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div id="lista_ajuda">
                    </div>
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Usuário - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_busca.php" enctype="multipart/form-data" id="form_gravar_busca">
                              <div class="tab-content">
                                <div id="dados" class="tab-pane active">

                                    <div class="row">
                                        <input  name="tipo_gravacao" type="hidden" id="tipo_gravacao" <?php //echo "value='".$tipo_gravacao."'";?>>

                                        <input name="codigo_id" type="hidden" id="codigo_id" <?php //echo "value='".$id_ajuda."'";?>>

                                        <input name="palavra_anterior" type="hidden" id="palavra_anterior">

                                        <div class="form-group col-md-12">
                                            <label for="palavras" class="control-label"><span class="required">*</span> Palavras-chave</label>

                                            <input type="text" name="palavras" id="palavras" class="form-control" <?php //echo "value='".$palavra_chave."'";?>>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="programa" class="control-label"><span class="required">*</span> Programas</label>

                                            <select class="form-control selectpicker programa" multiple data-live-search="true" id="programa" name="programa[]" style="z-index:5;">

                                            <?php while($reg_ajuda = mysqli_fetch_object($tbl_ajuda)) { ?>

                                            <option value="<?php 
                                                echo $reg_ajuda->tbl_id_url ?>">
                                                                
                                                <?php 
                                                    echo $reg_ajuda->tbl_nome_programa_url
                                                ?>
                                            </option>
                                            <?php } ?>
                                           </select>
                                        </div>

                                        <input type="hidden" name="programas" id="programas"  <?php //echo "value='".$programas."'";?>>
                                    </div>

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_ajuda()">Confirmar Inclusão</button>
                                            <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
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
                            <h4 class="modal-title">Ajuda </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_novo();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_edicao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Ajuda </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="sair_inclusao();">Fechar</button>
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
                            <h4 class="modal-title">Ajuda - Mensagem</h4>
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
  $javascript_file_name = 'tabela_busca.js';
  require 'rodape.php';
?>

