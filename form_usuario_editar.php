<?php
    include "valida_sessao.inc";
    $data_sistema = date("Y-m-d");;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/jquery-ui.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/elegant-icons-style.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="css/font-awesome.min.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="css/daterangepicker.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css?<?php echo Versao; ?>"rel="stylesheet" >
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css?<?php echo Versao; ?>" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" rel="stylesheet" crossorigin="anonymous">

</head>

<body>

  <?php

    if(isset($_REQUEST['id'])) {
        $codigo = $_REQUEST['id'];
    }
    else {
        $codigo = 0;
    }

    if ($codigo == 0 || $codigo == ''){
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Acesse o programa pelo menu do sistema. Clique no Nome do Usuário Logado e na opção Meu Cadastro</span>';       
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
            <span class="caminho-programa">Cabeçalho <i class="fa fa-angle-right seta-direita"></i><span class="titulo"> Usuário Editar</span></span>
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-vcard-o"></i> Usuário - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                    <?php
                        include "conecta_mysql.inc";

                        $tab_usuario = mysqli_query($conector_acesso, "select * from usuario where id_usuario='$codigo'"); 
                             
                        $registro_tabela = mysqli_fetch_object($tab_usuario);
                        $nome = $registro_tabela->nome_usuario; 
                        $email = $registro_tabela->email_usuario; 
                        $data_nascimento = $registro_tabela->data_nascimento_usuario; 
                        $idade = $registro_tabela->idade_usuario; 
                        $cpf = $registro_tabela->cpf_usuario; 
                        $cpf_editado = substr($cpf,0,3) . "." . substr($cpf,3,3) . "." . 
                                       substr($cpf,6,3) . "-" . substr($cpf,9,2);
                        $bd = $registro_tabela->cnpj_cpf_empresa_usuario;

                        $sexo = $registro_tabela->sexo_usuario; 
                        $observacao = $registro_tabela->observacao_usuario; 
                        $senha_cad = $registro_tabela->senha_usuario; 
                        $endereco = $registro_tabela->endereco_usuario; 
                        $numero = $registro_tabela->numero_usuario; 
                        $complemento = $registro_tabela->complemento_usuario; 
                        $bairro = $registro_tabela->bairro_usuario; 
                        $cep = $registro_tabela->cep_usuario; 
                        $cidade = $registro_tabela->cidade_usuario; 
                        $estado = $registro_tabela->estado_usuario;
                        $foto = $registro_tabela->foto_usuario;
                        //$foto=base64_encode($registro_tabela->foto_usuario);

                        $tab_estados = mysqli_query($conector, "select * from tabela_estados"); 
                        $tab_municipios = mysqli_query($conector, "select * from tabela_municipios 
                                                                           where mun_estado='$estado'"); 
                    ?>
                        <form method="POST" action="gravar_alterar_foto_usuario.php" enctype="multipart/form-data" id="form_gravar_usuario">
                            <div id="mensagem"></div>

                            <div class="panel"> 
                                <div class=panel-body>

                                    <input type="hidden" name="codigo_usuario" <?php echo "value='".$codigo."'";?>>
                      
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
                                                            <!--    <button type="button" class="btn btn-primary" data-loading-text="Salvando..." onclick="editUser()">Confirmar Edição</button>-->

                                                            <button type="button" class="btn btn-primary" onclick="editUser()" >Confirmar Edição</button>

                                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="foto" class="control-label"></label>
                                                        <?php 
                                                            //echo '<img class="img-control" title="Ideal 39 x 26 Pixels" src="data:image/jpeg;base64,' . $foto . '"  />'; 
                                                            echo '<img src="' . $foto . '"  />'; 
                                                        ?>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4"> 
                                                    <input type="file" name="foto" id="file"/>
                                                </div>

                                                <div class="form-group col-md-2 confirma_foto" hidden> 
                                                    <button type="submit" class="btn btn-info" >Confirmar Imagem</button>
                                                </div>

                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="nome_usuario"><span class="required">*</span>Nome</label>
                                                    <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" required value="<?=$nome?>">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="cpf_usuario" class="control-label"><span class="required">*</span>CPF/CNPJ</label>
                                                    <input name="cpf_usuario" type="text" class="form-control" id="cpf_usuario" required value="<?=$cpf_editado?>" onBlur="validar_cpf_usuario(this);">
                                                    <input type="hidden" name="bd" id="banco" value="<?=$bd?>">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="email_usuario"><span class="required">*</span>E-mail</label>
                                                    <input type="email" class="form-control" name="email_usuario" value="<?=$email?>" required>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="form-group col-md-5">
                                                    <label for="cep_usuario" class="control-label">CEP</label>
                                                    <input name="cep_usuario" type="text" class="form-control" id="cep_usuario" value="<?=$cep?>">
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="endereco_usuario" class="control-label">Endereço</label>
                                                    <input name="endereco_usuario" type="text" class="form-control" id="endereco_usuario" onkeyup="maiuscula(this)" value="<?=$endereco?>">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="numero_usuario" class="control-label">Número</label>
                                                    <input name="numero_usuario" type="text" class="form-control" id="numero_usuario" onkeyup="maiuscula(this)" value="<?=$numero?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="complemento_usuario" class="control-label">Complemento</label>
                                                    <input name="complemento_usuario" type="text" class="form-control" id="complemento_usuario" onkeyup="maiuscula(this)" value="<?=$complemento?>">
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="bairro_usuario" class="control-label">Bairro</label>
                                                    <input name="bairro_usuario" type="text" class="form-control" id="bairro_usuario" onkeyup="maiuscula(this)" value="<?=$bairro?>">
                                                </div>
                                            </div>
             
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                <label for="estado_usuario" class="control-label">Estado</label>
                                                <select class="form-control" id="estado_usuario" name="estado_usuario" >
                                                  <option value="" selected="selected">...</option>

                                                  <?php while($registro_estado = mysqli_fetch_object($tab_estados)) { ?>

                                                  <option value="<?php 
                                                   echo $registro_estado->est_codigo_id ?>"
                                                  
                                                  <?php 
                                                      if($registro_estado->est_codigo_id==$estado) 
                                                         { echo "selected"; }
                                                  ?>>
                                                    
                                                  <?php 
                                                      echo $registro_estado->est_nome;
                                                  ?>
                                                  </option>
                                                  <?php } ?>
                                                </select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="cidade_usuario" class="control-label">Município</label>
                                                    <input name="cidade_usuario" type="text" class="form-control" id="cidade_usuario" onkeyup="maiuscula(this)" readonly=""
                                                    <?php echo "value='".$cidade."'";?>>
                                                </div>

                                                <div class="form-group col-md-4 selecione_municipio">
                                                    <label for="lista_municipio" class="control-label">Selecione</label>
                                                    <select class="form-control" name="lista_municipio" 
                                                            id="lista_municipio">
                                                    <option value="" selected="selected">...</option>
                                                      <?php while($registro_mun = mysqli_fetch_array($tab_municipios)) { ?>
                                                    <option value="<?php echo $registro_mun['mun_nome'];?>"
                                                      
                                                        <?php 
                                                         // if($registro_mun['mun_nome']==$cidade) 
                                                            // { echo "selected"; }
                                                        ?>>
                                                        
                                                      <?php 
                                                          echo $registro_mun['mun_nome'];
                                                      ?>
                                                    </option>
                                                      <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                              
                                            <hr>
                                            
                                            <div class="row">
                                                
                                                <div class = "form-group col-md-4">
                                                     <label for="senha_cad" class="control-label">Alterar a Senha</label>
                                                    <input type="password" class="form-control" id="senha_cad" 
                                                     name="senha_cad" placeholder="Nova Senha" maxlength="8"
                                                     data-toggle='tooltip' data-placement='top' title="Sua senha deve conter até 8 caracteres que podem ser números e letras">
                                                </div>

                                                <div class="form-group col-md-4"> 
                                                    <label for="senha_conf" class="control-label">&nbsp;</label>

                                                    <input type="password" class="form-control" id="senha_conf" 
                                                     name="senha_conf" placeholder="Confirme a Nova Senha" maxlength="8">
                                                </div>
                                            </div>

	                                        <div class="row">                
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary" onclick="editUser()" id="butTeste">Confirmar Edição</button>
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
                        <h4 class="modal-title">Usuário</h4>
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
                        <h4 class="modal-title">Usuario</h4>
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
  $javascript_file_name = 'usuarios.js';
  require 'rodape.php';
?>



