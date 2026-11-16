<?php
  include "valida_sessao.inc";
  include "conecta_mysql.inc";

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
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_parametros'])) {
        $array_parametros = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametros[3] == 0){
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
        include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Semen</span></span>

        <div class="row">
          <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Semen</h3>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="form-group">
              <button type="button" class="btn btn-primary" id="botao_incluir">Incluir Novo</button>
            </div> 

            <section class="panel">
              <!--<header class="panel-heading">
                Lista
              </header> -->

              <table class="table table-striped table-advance table-hover" id="tabela_semens">
                <thead>
                  <tr>
                    <th> Nome</th>
                    <th> Raça</th>
                    <th> Registro</th>
                    <th>
                      <i class="icon_cogs"></i> Ações
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    //include "conecta_mysql.inc";

                    $sql = "select * from tbl_semem
                            INNER JOIN tabela_racas 
                            ON tab_codigo_raca = tbl_semem_codigo_raca
                            order by tbl_semem_nome asc"; 
                            
                    $rs = mysqli_query($conector, $sql); 
                    while ($semem = mysqli_fetch_object($rs)) : 
                  ?>
                    <tr>
                      <?php 

                      if ($semem->tbl_semem_ativo=='S' && $semem->tbl_semem_lixeira==0) : ?>

                        <td><?= $semem->tbl_semem_nome ?></td>
                        <td><?= $semem->tab_descricao_raca ?></td>
                        <td><?= $semem->tbl_semem_registro ?></td>

                      <?php 
                      elseif ($semem->tbl_semem_lixeira==1): ?> 

                        <td style="color: #ccc;"><?= $semem->tbl_semem_nome ?></td>
                        <td style="color: #ccc;"><?= $semem->tab_descricao_raca ?></td>
                        <td style="color: #ccc;"><?= $semem->tbl_semem_registro ?></td>

                      <?php 
                      else : ?> 
                        <td style="color: red;"><?= $semem->tbl_semem_nome ?></td>
                        <td style="color: red;"><?= $semem->tab_descricao_raca ?></td>
                        <td style="color: red;"><?= $semem->tbl_semem_registro ?></td>

                      <?php 
                      endif; ?>  

                      <?php 
                      if ($semem->tbl_semem_lixeira==1) : ?>

                        <td>
                          <a class="btn restaurar_semem" href="#" 
                             data-codigo="<?= $semem->tbl_semem_codigo_id ?>"
                             data-nome="<?= $semem->tbl_semem_nome ?>"
                             data-raca-id="<?= $semem->tbl_semem_codigo_raca ?>"
                             data-registro="<?= $semem->tbl_semem_registro ?>"
                             data-ativo="<?= $semem->tbl_semem_ativo ?>">
                            <i class="icon_refresh" data-toggle='tooltip' data-placement='left' title="Remover esse registro da lixeira" ></i>
                          </a>
                        </td>
                      <?php 
                      else : ?> 
                        <td>
                          <a class="btn editar_semem" href="javascript:void(0);"
                              data-codigo="<?= $semem->tbl_semem_codigo_id ?>"
                              data-nome="<?= $semem->tbl_semem_nome ?>"
                              data-raca-id="<?= $semem->tbl_semem_codigo_raca ?>"
                              data-registro="<?= $semem->tbl_semem_registro ?>"
                              data-ativo="<?= $semem->tbl_semem_ativo ?>">
                            <i class="icon_pencil" data-toggle='tooltip' data-placement='left' title="Editar esse registro"></i>
                          </a>
                            
                          <a class="btn excluir_semem" href="#" 
                              data-codigo="<?= $semem->tbl_semem_codigo_id ?>"
                              data-nome="<?= $semem->tbl_semem_nome ?>"
                              data-raca-id="<?= $semem->tbl_semem_codigo_raca ?>"
                              data-registro="<?= $semem->tbl_semem_registro ?>"
                              data-ativo="<?= $semem->tbl_semem_ativo ?>">
                            <i class="icon_trash_alt" data-toggle='tooltip' data-placement='left' title="Enviar esse registro para lixeira"></i>
                          </a>
                        </td>
                      <?php 
                      endif; ?>
                  <?php
                    endwhile;
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
                  <h4 class="modal-title" id="modal_incluirLabel">Semens - Incluir</h4>
              </div>

              <div class="modal-body">
                <form method="POST" action="gravar_semens.php" enctype="multipart/form-data" id="form_gravar_semens">
                  <input name="codigo_semem" type="hidden" id="codigo_semem">
                  <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                  
                  <div class="row" id="errors"></div>
                  
                  <div class="row">
                    <!--<div class="form-group col-md-4">
                        <label for="codigo_alfa" class="control-label"><span class="required">*</span> Código (Alfanumérico)</label>
                        <input name="codigo_alfa" type="text" class="form-control" id="codigo_alfa" required onkeyup="maiuscula(this)" maxlength="6">
                    </div>-->
                    <div class="form-group col-md-9">
                        <label for="nome_semem" class="control-label"><span class="required">*</span> Nome</label>
                        <input name="nome_semem" type="text" class="form-control" id="nome_semem" required onkeyup="maiuscula(this)">
                    </div>

                    <div class="form-group col-md-3 ativo">
                        <label for="animal_ativo" class="control-label">Ativo</label>  
                        <div class="clearfix"></div>
                        <label class="radio-inline">
                        <input type="radio" name="animal_ativo" id="S" value="S" disabled>Sim
                        </label>
                        <label class="radio-inline">
                        <input type="radio" name="animal_ativo" id="N" value="N" disabled >Não
                        </label>
                        </div>
                  </div>
                  
                  <div class="row">
                    <div class="form-group col-md-12">
                      <label for="raca_id" class="control-label"><span class="required">*</span> Raça</label>
                      <select class="form-control" name="raca_id" id="raca_id" required>
                        <option value="">...</option>
                        <?php 
                          $sql2 = "select * from tabela_racas"; 
                          $rs = mysqli_query($conector, $sql2); 
                          while ($raca = mysqli_fetch_object($rs)) : 
                        ?>
                          <option value="<?= $raca->tab_codigo_raca ?>"><?= $raca->tab_descricao_raca ?></option>
                        <?php
                          endwhile;
                        ?>
                      </select>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="form-group col-md-12">
                      <label for="registro_semem" class="control-label">Registro</label>
                      <input name="registro_semem" type="text" class="form-control" id="registro_semem">
                    </div>
                  </div>
                  
                  <div class="row">
                  <div class="form-group col-md-12">
                    <button type="button" class="btn btn-primary" id="confirma_gravar_semens">Confirmar Inclusão</button>
                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar</button>
                  </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Semens</h4>
              </div>
              <div class="modal-body"></div>
              <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>

<?php 
  $javascript_file_name = 'tabela_semens.js';
  require 'rodape.php';
?>

<!--<?php// if ($_SESSION['abre_inclusao']) : ?>
  <script>
   // $("#modal_incluir").modal();
  </script>
<?php
 // endif; 
  //unset($_SESSION['abre_inclusao']);
?> -->
