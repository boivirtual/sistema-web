<?php
    include "valida_sessao.inc";

    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
            $status_gravacao = $_REQUEST["status_gravacao"];
            $erro_mysql = $_REQUEST["erro_mysql"];
    }
    else {
        $status_gravacao = '';
        $erro_mysql = '';
    }


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
    if(isset($_SESSION['menu_parametros'])) {
        $array_parametros = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametros[19] == 0){
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
            <span class="caminho-programa">Parâmetros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Forma Pagamento</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-file-text"></i> Forma Pagamento</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" 
                            data-toggle="modal" 
                            data-target="#modal_incluir" 
                            value="Incluir Nova"/>
                        </a>
                    </div> 

		       		<section class="panel">
                        <table class="table table-striped table-advance table-hover table-bordered"
                         id="tabela_forma_rec_pag">

                        <thead>
                        	<tr>
                                <th> Código</th>
			                    <th> Descrição</th>
			                    <th><i class="icon_cogs"></i> Ações</th>
                            </tr>
                        </thead>
                          

		                <tbody>
                            <?php 
                                include "conecta_mysql.inc";
                                $ssql = "select * from tbl_forma_pagamento
                                         order by tbl_forma_pagamento_id  ASC"; 
                                $rs = mysqli_query($conector, $ssql); 
                     
                                while ($registro_tabela = mysqli_fetch_object($rs)){
                                    $codigo = $registro_tabela->tbl_forma_pagamento_id;
                                    $descricao = $registro_tabela->tbl_forma_pagamento_descricao; 
                                    $lixeira = $registro_tabela->tbl_forma_pagamento_lixeira; 

                                    if ($lixeira==1){
                                        echo '<tr>';
                                        echo '<td width="20%" style="color:#ccc">'.$codigo.'</td>';
                                        echo '<td width="30%" style="color:#ccc">'.$descricao.'</td>';
                                        echo '<td width="10%">
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$descricao.'"
                                              data-whatevertipo="3">
                                              <i class="icon_refresh" data-toggle="tooltip" data-placement="left" title="Restaurar esse registro da lixeira" ></i>
                                              </a>
                                              </td>';
                                        echo '</tr>'; 
                                    }
                                    else {
                                        echo '<tr>';
                                        echo '<td width="20%">'.$codigo.'</td>';
                                        echo '<td width="30%">'.$descricao.'</td>';
                                        echo '<td width="10%">
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_editar" 
                                              data-whatever="'.$codigo.'"
                                              data-descricao="'.$descricao.'">
                                              <i class="icon_pencil" data-toggle="tooltip" data-placement="left"  title="Editar esse registro" ></i>
                                              </a>
                                        
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$descricao.'"
                                              data-whatevertipo="2">
                                              <i class="icon_trash_alt"data-toggle="tooltip" data-placement="left" 
                                               title="Enviar esse registro para lixeira"></i>
                                              </a></td>';
                                        echo '</tr>'; 
                                    }
                                } 
                                mysqli_close($conector);
                            ?>
                        </tbody>
		                </table>
		            </section>
		        </div>
	        </div>
	        <!-- page end-->
            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Forma Pagamento - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_forma_pagamento.php" enctype="multipart/form-data"  id="gravar_forma">
                                <div class="form-group col-md-12">
                                    <label for="codigo_tipo" class="control-label"></label>
                                    <input name="codigo_tipo" type="hidden" class="form-control" id="codigo_tipo" readonly="">
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="descricao_tipo" class="control-label"><span class="required">*</span>Descrição</label>
                                        <input name="descricao_tipo" type="text" class="form-control" id="descricao_tipo" required="">
                                    </div>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                <input type="hidden" name="status_erro"  size="100" id="status_erro"
                                <?php echo "value='".$erro_mysql."'";?>>
                                
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <button type="submit" class="btn btn-primary gravar" id="botao_gravar">Confirmar Inclusão</button>
                                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                        </button>
                                    </div>
                                </div>
             
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" 
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exampleModalLabel">Forma Pagamento - Editar</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_forma_pagamento.php" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="codigo_tipo" class="control-label">Código</label>
                                        <input name="codigo_tipo_editar" class="form-control" 
                                        id="codigo_tipo_editar" readonly="" >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="descricao_tipo_editar" class="control-label"><span class="required">*</span>Descrição</label>
                                        <input name="descricao_tipo_editar" type="text" class="form-control" id="descricao_tipo_editar" required="" onkeyup="destrava_alteracao()">
                                    </div>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="1">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <button type="submit" id="confirmar" class="btn btn-primary">Confirmar Alteração</button>
                                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal"
                                          onClick='trava_alteracao()'>Voltar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_excluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_excluirCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_excluirLabel">Forma Pagamento - Excluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="excluir_forma_pagamento.php" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="codigo_tipo" class="control-label">Código</label>
                                        <input name="codigo_tipo" type="text" class="form-control" id="codigo_tipo" 
                                        readonly="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="descricao_tipo" class="control-label">Descrição</label>
                                        <input name="descricao_tipo" type="text" class="form-control" id="descricao_tipo" readonly="">
                                    </div>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <button type="submit" class="btn btn-danger">Confirmar</button>
                                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                        </button>
                                    </div>
                                </div>

             
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mensagem_inclusao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Forma Pagamento</h4>
                        </div>
                        <div class="modal-body">

                        Registro incluido com sucesso

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button"
                            onclick="abrir_modal_incluir()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_edicao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Forma Pagamento</h4>
                        </div>
                        <div class="modal-body">

                        Registro alterado com sucesso

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_removido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Forma Pagamento</h4>
                        </div>
                        <div class="modal-body">

                        Registro restaurado da lixeira

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_enviado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Forma Pagamento</h4>
                        </div>
                        <div class="modal-body">

                        Registro enviado para lixeira

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Forma Pagamento</h4>
                        </div>
                        <div class="modal-body" id="erro_mysql">
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</section> <!-- container section start end -->

<?php 
  $javascript_file_name = 'tabela_forma_pagamento.js';
  require 'rodape.php';
?>


                
                
