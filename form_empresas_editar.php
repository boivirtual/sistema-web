<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    if(isset($_REQUEST['id'])) {
        $codigo = $_REQUEST['id'];
    }
    else {
        $codigo = 0;
    }

    if ($codigo == 0 || $codigo == ''){
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Algo deu errado, acesse o programa pelo menu do sistema</span>';       
        echo '</div>';         
        exit;
    }

    $tbl_empresa = mysqli_query($conector, "select * from tbl_empresa 
                                                      where tbl_empresa_id='$codigo'"); 
                     
    $reg_empresa = mysqli_fetch_object($tbl_empresa);
    $razao = $reg_empresa->tbl_empresa_nome; 
    $nome_fantasia = $reg_empresa->tbl_empresa_nome_fantasia; 
    $tipo_pessoa = $reg_empresa->tbl_empresa_tipo_pessoa; 
    $email  = $reg_empresa->tbl_empresa_email; 
    $contato  = $reg_empresa->tbl_empresa_contato; 
    $ddd  = $reg_empresa->tbl_empresa_ddd; 
    $telefone  = $reg_empresa->tbl_empresa_telefone; 
    $cpf_cnpj = $reg_empresa->tbl_empresa_cpf_cnpj; 
    $insc_estadual = $reg_empresa->tbl_empresa_insc_estadual; 
    $insc_municipal = $reg_empresa->tbl_empresa_insc_municipal; 
    $observacao = $reg_empresa->tbl_empresa_observacao; 
    $endereco = $reg_empresa->tbl_empresa_endereco; 
    $numero = $reg_empresa->tbl_empresa_numero; 
    $complemento = $reg_empresa->tbl_empresa_complemento; 
    $bairro = $reg_empresa->tbl_empresa_bairro; 
    $cep = $reg_empresa->tbl_empresa_cep; 
    $cidade = $reg_empresa->tbl_empresa_municipio; 
    $estado = $reg_empresa->tbl_empresa_estado; 
    $host = $reg_empresa->tbl_empresa_host_smtp; 
    $porta = $reg_empresa->tbl_empresa_host_porta; 
    $usuario_host = $reg_empresa->tbl_empresa_usuario_email; 
    $senha_host = $reg_empresa->tbl_empresa_senha_email; 
    $controle_pesagem = $reg_empresa->tbl_empresa_controle_pesagem; 

    if ($tipo_pessoa=='F'){
        $cnpj_cpf_editado = substr($cpf_cnpj,0,3) . "." . substr($cpf_cnpj,3,3) . "." . 
                            substr($cpf_cnpj,6,3) . "-" . substr($cpf_cnpj,9,2);
    }
    else {
        $cnpj_cpf_editado = substr($cpf_cnpj,0,2) . "." . substr($cpf_cnpj,2,3) . "." .
                            substr($cpf_cnpj,5,3) . "/" . substr($cpf_cnpj,8,4) . "-" . 
                            substr($cpf_cnpj,12,2);
    }

    $tab_estados = mysqli_query($conector, "select * from tabela_estados"); 
    $tab_municipios = mysqli_query($conector, "select * from tabela_municipios 
                                                       where mun_estado='$estado'"); 
    $data_sistema = date("Y-m-d");

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

        if ($array_parametros[0] == 0){
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
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Parâmetros <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_empresas.php"> Empresa</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Editar</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-building"></i> Empresa - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="gravar_empresas.php" enctype="multipart/form-data" id="form_gravar_empresa">

                            <div class="panel"> 
                                <div class=panel-body>

                                    <input name="codigo_empresa" type="hidden" id="codigo_empresa"
                                    <?php echo "value='".$codigo."'";?>>
                                    <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                      
                                    <div class="row" id="errors"></div>
                      
                                    <ul class="nav nav-tabs m-bot15">
                                        <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                        </li>
                                        <li>
                                        <a data-toggle="tab" href="#configuracoes">Configurações</a>
                                        </li>
                                        <li>
                                        <a data-toggle="tab" href="#dados_fiscais">Dados Fiscais</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="tab-content">
                                                <div class="tab-pane active">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <button type="button" class="btn btn-primary confirma_gravar_empresa">Confirmar Edição</button>

                                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="nome_empresa" class="control-label"><span class="required">*</span>Razão Social</label>
                                                    <input name="nome_empresa" type="text" class="form-control" id="nome_empresa" required="" 
                                                    onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$razao."'";?>>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="nome_fantasia" class="control-label">Nome Fantasia</label>
                                                    <input name="nome_fantasia" type="text" class="form-control" id="nome_fantasia" required="" 
                                                    onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$nome_fantasia."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="tipo_pessoa" class="control-label"><span class="required">*</span>Pessoa</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_pessoa" id="fisica" value="F" required=""
                                                      <?php if ($tipo_pessoa == 'F') { echo "checked"; } ?>>Física
                                                    </label>

                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_pessoa" id="juridica" value="J" required=""
                                                      <?php if ($tipo_pessoa == 'J') { echo "checked"; } ?>>Jurídica
                                                    </label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="documento_pessoa" class="control-label"><span class="required">*</span>CPF/CNPJ</label>
                                                    <input name="documento_pessoa" type="text" class="form-control" id="documento_pessoa" required=""
                                                    <?php echo "value='".$cnpj_cpf_editado."'";?>
                                                    onBlur="validar(this);">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="insc_estadual" class="control-label">Inscrição Estadual</label>
                                                    <input name="insc_estadual" type="text" class="form-control" id="insc_estadual" 
                                                    <?php echo "value='".$insc_estadual."'";?>>
                                                </div>
                                                <div class="form-group col-md-6">
                                                     <label for="insc_municipal" class="control-label">Inscrição Municipal</label>
                                                    <input name="insc_municipal" type="text" class="form-control" id="insc_municipal" 
                                                    <?php echo "value='".$insc_municipal."'";?>>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="contato_pessoa" class="control-label">Contatos</label>
                                                    <input name="contato_pessoa" type="text" class="form-control" id="contato_pessoa" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$contato ."'";?>>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="ddd_pessoa" class="control-label">DDD</label>
                                                    <input name="ddd_pessoa" type="text" class="form-control" id="ddd_pessoa" placeholder="##"
                                                    <?php echo "value='".$ddd ."'";?>>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="telefone_pessoa" class="control-label">Telefone</label>
                                                    <input name="telefone_pessoa" type="text" class="form-control" id="telefone_pessoa " placeholder="#########"
                                                    <?php echo "value='".$telefone ."'";?>>
                                                </div>
                                                                                         
                                                <div class="form-group col-md-4">
                                                    <label for="email_pessoa" class="control-label">Email</label>
                                                    <input name="email_pessoa" type="text" class="form-control" id="email_pessoa" onkeyup="minuscula(this)"
                                                    <?php echo "value='".$email ."'";?>>
                                                </div>
                                            </div>

                                            <hr>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-5">
                                                    <label for="cep_pessoa" class="control-label">CEP</label>
                                                    <input name="cep_pessoa" type="text" class="form-control" id="cep_pessoa"
                                                    <?php echo "value='".$cep."'";?>>
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="endereco_pessoa" class="control-label">Endereço</label>
                                                    <input name="endereco_pessoa" type="text" class="form-control" id="endereco_pessoa" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$endereco."'";?>>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="numero_pessoa" class="control-label">Número</label>
                                                    <input name="numero_pessoa" type="text" class="form-control" id="numero_pessoa" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$numero."'";?>>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="complemento_pessoa" class="control-label">Complemento</label>
                                                    <input name="complemento_pessoa" type="text" class="form-control" id="complemento_pessoa" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$complemento."'";?>>
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="bairro_pessoa" class="control-label">Bairro</label>
                                                    <input name="bairro_pessoa" type="text" class="form-control" id="bairro_pessoa" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$bairro."'";?>>
                                                </div>
                                            </div>
             
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                <label for="estado_pessoa" class="control-label">Estado</label>
                                                <select class="form-control" id="estado_pessoa" name="estado_pessoa" >
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
                                                    <label for="cidade_pessoa" class="control-label">Município</label>
                                                    <input name="cidade_pessoa" type="text" class="form-control" id="cidade_pessoa" onkeyup="maiuscula(this)" readonly=""
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
                                              
                                            <div class="row m-bot15">
                                                <div class="col-md-12">
                                                  <label for="observacao_pessoa" class="control-label">Observação</label>
                                                  <textarea name="observacao_pessoa" type="text" class="form-control" id="observacao_pessoa" rows="1" onkeyup="maiuscula(this)"><?php echo $observacao; ?></textarea>
                                                </div>
                                            </div>


                                            <div class="row">               
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirma_gravar_empresa">Confirmar Edição</button>
                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                </div>
                                            </div>
                                        </div> <!-- dados-->

                                        <div id="configuracoes" class="tab-pane">
                                            <div class="row">               
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirma_gravar_empresa">Confirmar Edição</button>
                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                </div>
                                            </div>

                                            <fieldset class="scheduler-border">
                                                <legend class="scheduler-border fonte-legend">Dados para envio de e-mail</legend>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="host" class="control-label">Host (SMTP)</label>
                                                        <input name="host" type="text" class="form-control" id="host"
                                                        <?php echo "value='".$host."'";?>>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="porta_host" class="control-label">Porta (SMTP)</label>
                                                        <input name="porta_host" type="number" class="form-control" id="porta_host"
                                                        <?php echo "value='".$porta."'";?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="usuario_host" class="control-label">Usuário Host</label>
                                                        <input name="usuario_host" type="text" class="form-control" id="usuario_host"
                                                        <?php echo "value='".$usuario_host."'";?>>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="senha_host" class="control-label">Senha</label>
                                                        <input name="senha_host" type="text" class="form-control" id="senha_host"
                                                        <?php echo "value='".$senha_host."'";?>>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="scheduler-border">
                                                <legend class="scheduler-border fonte-legend">Outros Parâmetros</legend>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="controle_pesagem" class="control-label"><span class="required">*</span>Controle de Estoque de Animais</label>

                                                        <div class="clearfix"></div>

                                                        <label class="radio-inline">
                                                          <input type="radio" name="controle_pesagem" id="individual" value="I" required=""
                                                          <?php if ($controle_pesagem == 'I') { echo "checked"; } ?>>Individual
                                                        </label>

                                                        <label class="radio-inline">
                                                          <input type="radio" name="controle_pesagem" id="lote" value="L" required=""
                                                          <?php if ($controle_pesagem == 'L') { echo "checked"; } ?>>Lote
                                                        </label>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>

                                        <div id="dados_fiscais" class="tab-pane">
                                        </div>

                                    </div> <!--tab-content -->


                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

        <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Empresa</h4>
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
                        <h4 class="modal-title">Empresa</h4>
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
  $javascript_file_name = 'empresas.js';
  require 'rodape.php';
?>




