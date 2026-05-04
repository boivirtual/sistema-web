<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
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


<!--  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet"> -->

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_parametros'])) {
        $array_cadastro = explode("!",$_SESSION['menu_parametros']);

        if ($array_cadastro[1] == 0){
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

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

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
            <span class="titulo">Usuários</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-users"></i> Usuários</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div id="lista_usuarios">
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
                            <form method="POST" action="gravar_usuario.php" enctype="multipart/form-data" id="form_gravar_usuario">
                                <input name="codigo_usuario" type="hidden" id="codigo_usuario">
                              
                              <div class="tab-content">
                                <div id="dados" class="tab-pane active">

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="nome_usuario" class="control-label"><span class="required">*</span>Nome</label>
                                        <input name="nome_usuario" type="text" class="form-control" id="nome_usuario" >
                                    </div>
                                </div>

                                <div class="row">    
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="situacao_usuario" class="control-label">
                                            <span class="required">*</span>Situação do Usuário
                                        </label>
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="situacao"></label>

                                        <label class="radio-inline">
                                            <input type="radio" name="situacao" id="ativo" value="A"> Ativo
                                        </label>

                                        <label class="radio-inline">
                                            <input type="radio" name="situacao" id="desligado" value="D"> Desligado
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="cpf_usuario"><span class="required">*</span>CPF</label>
                                        <input type="text" class="form-control" id="cpf_usuario" name="cpf_usuario" required onBlur="validar_cpf_usuario(this);">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="email_usuario"><span class="required">*</span>E-mail</label>
                                        <input type="email" class="form-control" id="email_usuario" name="email_usuario" required
                                        onkeyup="minuscula(this)">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="grupo"><span class="required">*</span>Grupo de Acesso
                                        </label>
                                        <select class="form-control" id="grupo" name="grupo">
                                            <?php
                                                if ($cnpj_cliente==97174041604 || $cnpj_cliente==71746307668){
                                                    $sql = "select * FROM grupos_acessos where registro_lixeira_grupo_acesso = 0
                                                        order by descricao_grupo_acesso ASC";  
                                                }
                                                else {
                                                    $sql = "select * FROM grupos_acessos where registro_lixeira_grupo_acesso = 0 and codigo_grupo_acesso!=1
                                                        order by descricao_grupo_acesso ASC";  
                                                }
                                                $qr = mysqli_query($conector, $sql);
                                            
                                                if(mysqli_num_rows($qr) == 0){
                                                    echo  '<option value="">'.htmlentities('Sem grupos cadastrados').'</option>';
                                                }
                                                else{
                                                    echo '<option value="">...</option>';
                                                    
                                                    while($ln = mysqli_fetch_assoc($qr)){
                                                        $codigo_grupo=$ln['codigo_grupo_acesso'];
                                                        $descricao_grupo=$ln['descricao_grupo_acesso'];
                                                        echo '<option value="'.$codigo_grupo.'">'.$descricao_grupo.'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="local" class="control-label">Locais</label>
                                        <select class="form-control selectpicker local" multiple id="local" name="local[]">
                                                          
                                            <?php while($reg_local = mysqli_fetch_object($tbl_local)) { ?>

                                            <option value="<?php 
                                                echo $reg_local->tbl_pessoa_id ?>">
                                                            
                                                <?php 
                                                    echo $reg_local->tbl_pessoa_nome
                                                ?>
                                            </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>

                              <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                              <div class="row">  
                                  <div class="form-group col-md-12">
                                    <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_usuario()">Confirmar Inclusão</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
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
                            <h4 class="modal-title">Usuário </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="sair_inclusao()">Fechar</button>
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
                            <h4 class="modal-title">Usuário - Erro</h4>
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
  $javascript_file_name = 'tabela_usuarios.js';
  require 'rodape.php';
?>
