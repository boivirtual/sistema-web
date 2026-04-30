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
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/jquery-ui.css" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet" />
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
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

    $cnpj_cliente = $_SESSION['id_cliente'];
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
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Via de Uso de Produtos</h3>
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
                          id="tabela_via_uso_produtos">

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
                                $ssql = "select * from tabela_via_uso_produtos"; 
                                $rs = mysqli_query($conector, $ssql); 
                     
                                while ($registro_tabela = mysqli_fetch_object($rs)){
                                    $codigo = $registro_tabela->tab_codigo_via_uso_produtos;
                                    $descricao = $registro_tabela->tab_descricao_via_uso_produtos; 
                                    $lixeira = $registro_tabela->tab_registro_lixeira_via_uso_produtos; 

                                    if ($lixeira==1){
                                        echo '<tr>';
                                        echo '<td style="color:#ccc">'.$codigo.'</td>';
                                        echo '<td style="color:#ccc">'.$descricao.'</td>';
                                        echo '<td>
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$descricao.'"
                                              data-whatevertipo="3">
                                              <i class="icon_refresh" title="Remover esse registro da lixeira" ></i>
                                              </a>
                                              </td>';
                                        echo '</tr>'; 
                                    }
                                    else {
                                        echo '<tr>';
                                        echo '<td>'.$codigo.'</td>';
                                        echo '<td>'.$descricao.'</td>';
                                        echo '<td>
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_editar" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$descricao.'">
                                              <i class="icon_pencil" title="Editar esse registro" ></i>
                                              </a>
                                        
                                              <a class="btn" href="#" 
                                              data-toggle="modal" 
                                              data-target="#modal_excluir" 
                                              data-whatever="'.$codigo.'"
                                              data-whatevernome="'.$descricao.'"
                                              data-whatevertipo="2">
                                              <i class="icon_trash_alt" 
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
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Via de Uso de Produtos - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_via_uso_produtos.php" enctype="multipart/form-data" id="gravar_via">
                                <div class="form-group col-md-12">
                                    <label for="codigo_via_uso_produtos" class="control-label"></label>
                                    <input name="codigo_via_uso_produtos" type="hidden" class="form-control" id="codigo_via_uso_produtos" 
                                    >
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_via_uso_produtos" class="control-label"><span class="required">*</span>Descrição</label>
                                    <input name="descricao_via_uso_produtos" type="text" class="form-control" id="descricao_via_uso_produtos" required="">
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                <input type="hidden" name="status_erro"  size="100" id="status_erro"
                                <?php echo "value='".$erro_mysql."'";?>>

                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-primary gravar" id="botao_gravar">Confirmar Inclusão</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                    </button>
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
                            <h4 class="modal-title" id="exampleModalLabel">Via de Uso de Produtos - Editar</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_via_uso_produtos.php" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <label for="codigo_via_uso_produtos" class="control-label">Código</label>
                                    <input name="codigo_via_uso_produtos" type="text" class="form-control" id="codigo_via_uso_produtos" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_via_uso_produtos" class="control-label"><span class="required">*</span>Descrição</label>
                                    <input name="descricao_via_uso_produtos" type="text" class="form-control" id="descricao_via_uso_produtos" required="" onkeyup="destrava_alteracao()">
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="1">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                
                                <div class="form-group col-md-12">
                                    <button type="submit" id="confirmar" class="btn btn-primary">Confirmar Alteração</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal"
                                      onClick='trava_alteracao()'>Voltar
                                    </button>
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
                            <h4 class="modal-title" id="modal_excluirLabel">Via de Uso de Produtos - Excluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="excluir_via_uso_produtos.php" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <label for="codigo_via_uso_produtos" class="control-label">Código</label>
                                    <input name="codigo_via_uso_produtos" type="text" class="form-control" id="codigo_via_uso_produtos" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_via_uso_produtos" class="control-label">Descrição</label>
                                    <input name="descricao_via_uso_produtos" type="text" class="form-control" id="descricao_via_uso_produtos" readonly="">
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>

                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-danger">Confirmar</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                    </button>
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
                            <h4 class="modal-title">Via de Uso de Produtos</h4>
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
                            <h4 class="modal-title">Via de Uso de Produtos</h4>
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
                            <h4 class="modal-title">Via de Uso de Produtos</h4>
                        </div>
                        <div class="modal-body">

                        Registro removido da lixeira

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
                            <h4 class="modal-title">Via de Uso de Produtos</h4>
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
                            <h4 class="modal-title">Via de Uso de Produtos</h4>
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

<?php 
  $javascript_file_name = 'tabela_via_uso_produtos.js';
  require 'rodape.php';
?>
