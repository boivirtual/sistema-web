<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
        $id_ajuda = $_REQUEST["id_ajuda"];
        $programas = $_REQUEST["programas"];
        $palavra_chave = $_REQUEST["palavra_chave"];
        $tipo_gravacao = 1;
    }
    else {
        $id_ajuda = '';
        $programas = '';
        $palavra_chave = '';
        $tipo_gravacao = 0;
    }

    $_REQUEST["editar"] = false;
    $_REQUEST["id_ajuda"] = '';
    $_REQUEST["programas"] = '';
    $_REQUEST["palavra_chave"] = '';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
    <title>Boi Virtual</title>

    <!-- Bootstrap CSS />-->
    <link href="css/jquery-ui.css" rel="stylesheet" />-->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

    <script>history.scrollRestoration = "manual"</script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

    <link rel="stylesheet" href="css/select-1.13.14.css"> 

<style type="text/css">
        .bootstrap-select {
          width: 1090px !important;
        }
    </style>
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
            <span class="caminho-programa">Parametros <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_cadastro_busca.php"> Cadastro Ajuda</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Incluir</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <?php if ($tipo_gravacao == 0) : ?>
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-question-circle-o"></i> Cadastro de Ajuda - Incluir</h3>
                </div>

                <?php else : ?>

                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-question-circle-o"></i> Cadastro de Ajuda - Editar</h3>
                </div>

                <?php endif; ?>

            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12" id="selecionar_pasagem">
                        <form method="POST" action="#" id="form_gravar_ajuda" enctype="multipart/form-data" >
                            
                            <div class="tab-panel selecionar_dados_pesagem">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Dados</legend>

                                        <div class="row">
                                            <input  name="tipo_gravacao" type="hidden" id="tipo_gravacao" <?php echo "value='".$tipo_gravacao."'";?>>

                                            <input name="codigo_id" type="hidden" id="codigo_id" <?php echo "value='".$id_ajuda."'";?>>

                                            <div class="form-group col-md-12">
                                                <label for="palavras" class="control-label"><span class="required">*</span> Palavras-chave</label>

                                                <input type="text" name="palavras" id="palavras" class="form-control" <?php echo "value='".$palavra_chave."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="programa" class="control-label"><span class="required">*</span> Programas</label>

                                                <select class="form-control selectpicker" multiple data-live-search="true" id="programa" name="programa[]" style="z-index:5;">

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

                                            <input type="hidden" name="programas" id="programas"  <?php echo "value='".$programas."'";?>>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_ajuda()">Confirmar</button>
                                                <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div> 
                            </div>
                        </form>
                    </div>    
                </div>
            </div>
	        <!-- page end-->

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
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
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
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</section>

 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
     </div>
 </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->

<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/ga.js?<?php echo Versao; ?>" type="text/javascript" ></script>
<script src="js/bootstrap-switch.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.tagsinput.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.hotkeys.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg-custom.js?<?php echo Versao; ?>"></script>
<script src="js/moment.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="js/tabela_busca.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>
</html>
