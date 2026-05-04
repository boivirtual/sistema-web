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
  <link href="css/jquery-ui.css" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_cadastros'])) {
        $array_cadastro = explode("!",$_SESSION['menu_cadastros']);

        if ($array_cadastro[6] == 0){
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

    $pessoa = mysqli_query($conector, "select * from tbl_pessoa where (tbl_pessoa_classe=1 or tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and tbl_pessoa_lixeira=0 order by tbl_pessoa_nome ASC"); 

    $pessoa_filtro = mysqli_query($conector, "select * from tbl_pessoa where (tbl_pessoa_classe=1 or tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and tbl_pessoa_lixeira=0 order by tbl_pessoa_nome ASC"); 

    $raca = mysqli_query($conector, "select * from tabela_racas 
        where tab_registro_lixeira_raca=0"); 

    $array_cliente = $_SESSION['array_cliente_embrioes'];
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Embrião</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Cadastro de Embrião</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_produtos.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                        <input id="lista_embrioes_automatico" type="hidden"
                                        <?php echo "value='".$_SESSION['lista_embrioes']."'";?>>

                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Dados para Consultar</legend>

                                        <div class="row ">
                                            <div class="form-group col-md-4">
                                                <label for="codigo_cliente_filtro" class="control-label">Cliente</label>
                                                <select class="form-control selectpicker" data-live-search="true" id="codigo_cliente_filtro" multiple name="codigo_cliente_filtro">

                                                <?php 
                                                    while($reg_pessoa = mysqli_fetch_object($pessoa_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_pessoa->tbl_pessoa_id ?>"

                                                    <?php 
                                                        if ($array_cliente!="") {
                                                            foreach ($array_cliente as $value) {
                                                                if ($value==$reg_pessoa->tbl_pessoa_id) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_pessoa->tbl_pessoa_nome;
                                                    ?>
                                                    </option>
                                                <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-primary pull-right" onclick="consultar()">Consultar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>    

                    <div id="lista_embrioes">
                    </div>
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Embrião - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_embriao">
                                <input name="codigo_embriao" type="hidden" id="codigo_embriao">
                              
                            <div class="tab-content">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="lote" class="control-label"><span class="required">*</span> Lote do Embrião</label>
                                        <input name="lote" type="text" class="form-control" id="lote" maxlength="15" 
                                        onkeyup="maiuscula(this)">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="raca_id" class="control-label"><span class="required">*</span> Raça</label>
                                        <select class="form-control" required="" name="raca_id"
                                      id="raca_id">
                                        <option value="000000000">...</option>

                                        <?php while($reg_raca = mysqli_fetch_object($raca)) { ?>

                                        <option value="<?php 
                                            echo $reg_raca->tab_codigo_raca ?>">
                                                            
                                            <?php 
                                            echo $reg_raca->tab_descricao_raca;
                                            ?>
                                        </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="tipo_1" class="control-label"><span class="required">*</span> Tipo do Embrião</label>
                                        <select class="form-control" name="tipo_1" id="tipo_1">
                                        <option value="">...</option>
                                        <option value="1">FIV</option>
                                        <option value="2">Convencional</option>
                                        <option value="3">Clone</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="tipo_2" class="control-label"><span class="required">*</span> Conservação</label>
                                        <select class="form-control" name="tipo_2" id="tipo_2">
                                        <option value="">...</option>
                                        <option value="1">Fresco</option>
                                        <option value="2">Descongelado</option>
                                        <option value="3">Desvitrificado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="doadora" class="control-label"><span class="required">*</span> Doadora</label>
                                        <input name="doadora" type="text" class="form-control" id="doadora" 
                                        onkeyup="maiuscula(this)" maxlength="20">
                                    </div>

                                    <div class="form-group col-md-4">
                                      <label for="touro" class="control-label"><span class="required">*</span> Touro</label>
                                      <input name="touro" type="text" class="form-control" id="touro" 
                                      onkeyup="maiuscula(this)" maxlength="20">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="laboratorio" class="control-label"><span class="required">*</span> Laboratório Aspirador</label>
                                        <input name="laboratorio" type="text" class="form-control" id="laboratorio"
                                        onkeyup="maiuscula(this)" maxlength="60">
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-6">
                                      <label for="cliente" class="control-label"><span class="required">*</span> Cliente</label>
                                      <select class="form-control" required="" name="cliente" id="cliente">
                                        <option value="000000000">...</option>
                                        <?php while($reg_pessoa = mysqli_fetch_object($pessoa)) { ?>

                                        <option value="<?php 
                                            echo $reg_pessoa->tbl_pessoa_id ?>">
                                                            
                                            <?php 
                                            echo $reg_pessoa->tbl_pessoa_nome;
                                            ?>
                                        </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="fazenda" class="control-label"><span class="required">*</span> Fazenda</label>
                                        <input name="fazenda" type="text" class="form-control" id="fazenda" 
                                        onkeyup="maiuscula(this)" maxlength="60">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12" style="font-size: 10px;" id="informacao">
                                        <label>Incluído por: </label>
                                        <span id="incluido_por" style="color: green"></span>
                                        &nbsp; 
                                        <span id="incluido_em" style="color: green; margin-right: 10px"></span>

                                        <label id="registro_alterado"> Alterado por: </label>
                                        <span id="alterado_por" style="color: red"></span>
                                        &nbsp;
                                        <span id="alterado_em" style="color: red; margin-right: 10px"></span>
                                    </div>
                                </div>

                                <div class="row">  
                                    <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                                    
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_embriao()">Confirmar Inclusão</button>

                                        <button type="button" class="btn btn-info pull-right" onClick="sair_inclusao()">Voltar</button>
                                    </div>
                                </div>
                            </div> <!-- fim div tab-content-->    
                            </form> <!-- fim form -->
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
                            <h4 class="modal-title">Embrião </h4>
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
                            <h4 class="modal-title">Embrião </h4>
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
                            <h4 class="modal-title">Embrião - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

<?php 
  $javascript_file_name = 'tabela_embriao.js';
  require 'rodape.php';
?>
