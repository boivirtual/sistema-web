<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    function diferenca_data($data_validade) {
            
        $data_inicial = date("Y-m-d H:i:s");
        $data_final = $data_validade;
        $time_inicial = strtotime($data_inicial);
        $time_final = strtotime($data_final);
        $diferenca = $time_final - $time_inicial; 
        $dias = (int)floor( $diferenca / (60 * 60 * 24)); 
        return $dias;
    }

    $data_sistema = date("Y-m-d");;

    if(isset($_REQUEST['voltar'])) {
        $voltar = $_REQUEST['voltar'];
        $_SESSION['voltar_movimentacao'] = $voltar;
        $classe_cliente = 2;
    }
    else {
        $voltar = 0;
        $classe_cliente = 0;
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
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

    @ session_start();   

    if(isset($_SESSION['menu_cadastros'])) {
        $array_cadastro = explode("!",$_SESSION['menu_cadastros']);

        if ($array_cadastro[0] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
        echo '</div>';         
        exit;
    }

    $cnpj_cliente = $_SESSION['id_cliente'];

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";


        if ($cnpj_cliente==97174041604 || $cnpj_cliente==71746307668){
            $tab_classe = mysqli_query($conector, "select * from tabela_tipo_pessoas where tab_registro_lixeira_tipo_pessoa=0"); 
        }
        else {
            $tab_classe = mysqli_query($conector, "select * from tabela_tipo_pessoas where tab_codigo_tipo_pessoa!=4 and tab_registro_lixeira_tipo_pessoa=0"); 
        }

        $tab_estados = mysqli_query($conector, "select * from tabela_estados"); 
        $tab_municipios = mysqli_query($conector, "select * from tabela_municipios 
            where mun_estado='MG'"); 
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastro <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_cliente_fornecedor.php"> Pessoas</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Pessoas Incluir</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-id-card"></i> Pessoas - Incluir</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="gravar_clientes.php" enctype="multipart/form-data" id="form_gravar_cliente">

                            <div class="panel"> 
                                <div class=panel-body>

                                    <input name="codigo_pessoa" type="hidden" id="codigo_pessoa">
                                    <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                                    <input name="voltar" type="hidden" id="voltar" 
                                    <?php echo "value='".$voltar."'";?>>
                      
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
                                                            <button type="button" class="btn btn-primary confirma_gravar_cliente">Confirmar Inclusão</button>

                                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="nome_pessoa" class="control-label"><span class="required">*</span>Razão Social/Nome</label>
                                                    <input name="nome_pessoa" type="text" class="form-control" id="nome_pessoa" required="" onkeyup="maiuscula(this)">
                                                </div>

                                                <div class="form-group col-md-6">
                                                <label for="classe_pessoa" class="control-label"><span class="required">*</span>Classe de Pessoa</label>
                                                <select class="form-control" id="classe_pessoa" name="classe_pessoa" required="">

                                                  <option value="" selected="selected">...</option>

                                                  <?php while($registro_classe = mysqli_fetch_object($tab_classe)) { ?>

                                                  <option value="<?php 
                                                   echo $registro_classe->tab_codigo_tipo_pessoa ?>"

                                                    <?php 
                                                          if($registro_classe->tab_codigo_tipo_pessoa==$classe_cliente && $classe_cliente!=0) 
                                                             { echo "selected"; }
                                                      ?>>
                                                    
                                                  <?php 
                                                      echo $registro_classe->tab_descricao_tipo_pessoa;
                                                  ?>
                                                  </option>
                                                  <?php } ?>

                                                </select>

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="tipo_pessoa" class="control-label"><span class="required">*</span>Pessoa</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_pessoa" id="fisica" value="F" required="">Física
                                                    </label>

                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_pessoa" id="juridica" value="J" checked="true" required=""
                                                      >Jurídica
                                                    </label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="documento_pessoa" class="control-label"><span class="required">*</span>CPF/CNPJ</label>
                                                    <input name="documento_pessoa" type="text" class="form-control" id="documento_pessoa" required="" onBlur="validar(this);">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="insc_estadual" class="control-label">Inscrição Estadual</label>
                                                    <input name="insc_estadual" type="text" class="form-control" id="insc_estadual">
                                                </div>
                                                <div class="form-group col-md-6">
                                                     <label for="insc_municipal" class="control-label">Inscrição Municipal</label>
                                                    <input name="insc_municipal" type="text" class="form-control" id="insc_municipal">
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="contato_pessoa" class="control-label">Contato</label>
                                                    <input name="contato_pessoa" type="text" class="form-control" id="contato_pessoa" onkeyup="maiuscula(this)">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="contato_cargo" class="control-label">Cargo</label>
                                                    <input name="contato_cargo" type="text" class="form-control" id="contato_cargo" onkeyup="maiuscula(this)">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label for="ddd_pessoa" class="control-label">DDD</label>
                                                    <input name="ddd_pessoa" type="text" class="form-control" id="ddd_pessoa" placeholder="##">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="telefone_pessoa" class="control-label">Telefone</label>
                                                    <input name="telefone_pessoa" type="text" class="form-control" id="telefone_pessoa" placeholder="#########">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="email_pessoa" class="control-label">Email</label>
                                                    <input name="email_pessoa" type="text" class="form-control" id="email_pessoa" onkeyup="minuscula(this)">
                                                </div>
                                            </div>
                                             
                                            <hr>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-5">
                                                    <label for="cep_pessoa" class="control-label">CEP</label>
                                                    <input name="cep_pessoa" type="text" class="form-control" id="cep_pessoa" >
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="endereco_pessoa" class="control-label">Endereço</label>
                                                    <input name="endereco_pessoa" type="text" class="form-control" id="endereco_pessoa" onkeyup="maiuscula(this)">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="num_pessoa" class="control-label">Número</label>
                                                    <input name="num_pessoa" type="text" class="form-control" id="num_pessoa" onkeyup="maiuscula(this)">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="complemento_pessoa" class="control-label">Complemento</label>
                                                    <input name="complemento_pessoa" type="text" class="form-control" id="complemento_pessoa" onkeyup="maiuscula(this)">
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="bairro_pessoa" class="control-label">Bairro</label>
                                                    <input name="bairro_pessoa" type="text" class="form-control" id="bairro_pessoa" onkeyup="maiuscula(this)">
                                                </div>
                                            </div>
             
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                <label for="estado_pessoa" class="control-label">Estado</label>
                                                <select class="form-control" id="estado_pessoa" name="estado_pessoa" >
                                                  <option value="" selected="selected">...</option>

                                                  <?php while($registro_estado = mysqli_fetch_object($tab_estados)) { ?>

                                                  <option value="<?php 
                                                   echo $registro_estado->est_codigo_id ?>">
                                                    
                                                  <?php 
                                                      echo $registro_estado->est_nome;
                                                  ?>
                                                  </option>
                                                  <?php } ?>
                                                </select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="cidade_pessoa" class="control-label">Município</label>
                                                    <input name="cidade_pessoa" type="text" class="form-control" id="cidade_pessoa" onkeyup="maiuscula(this)" readonly="">
                                                </div>

                                                <div class="form-group col-md-4 selecione_municipio">
                                                    <label for="lista_municipio" class="control-label">Selecione</label>
                                                    <select class="form-control" name="lista_municipio" 
                                                            id="lista_municipio">
                                                    <option value="" selected="selected">...</option>
                                                        <?php while($registro_mun = mysqli_fetch_array($tab_municipios)) { ?>
                                                    <option value="<?php echo $registro_mun['mun_nome'];?>">
                                                        <?php 
                                                            echo $registro_mun['mun_nome'];
                                                        ?>
                                                    </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                    <div id="dados_meus_locais" hidden='true'>
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="area" class="control-label">Área da Fazenda(Ha)</label>
                                                <input name="area" type="text" class="form-control" id="area" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area()">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="area_util" class="control-label">Área Útil da Fazenda(Ha)</label>
                                                <input name="area_util" type="text" class="form-control" id="area_util" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area_util()">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="latitude" class="control-label">Latitude</label>
                                                <input name="latitude" type="text" class="form-control" id="latitude">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="longitude" class="control-label">Longitude</label>
                                                <input name="longitude" type="text" class="form-control" id="longitude">
                                            </div>
                                        </div>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border fonte-legend">Atividades</legend>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_pec_corte">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_pec_corte" name="atv_pec_corte"> Pecuária de Corte
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_pec_leite">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_pec_leite" name="atv_pec_leite"> Pecuária de Leite
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_agricultura">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_agricultura" name="atv_agricultura"> Agricultura
                                                </label>
                                            </div>

                                            <div class="form-group col-md-10">
                                                <label for="descricao_atv_agricola" class="control-label">Atividades Agrícolas</label>
                                                <textarea name="descricao_atv_agricola" type="text" class="form-control" id="descricao_atv_agricola" rows="2" onkeyup="maiuscula(this)"></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_outra">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_outra" name="atv_outra">Outra
                                                </label>
                                            </div>

                                            <div class="form-group col-md-10">
                                                <label for="descricao_atv_outra" class="control-label">Outras Atividades</label>
                                                <textarea name="descricao_atv_outra" type="text" class="form-control" id="descricao_atv_outra" rows="2" onkeyup="maiuscula(this)"></textarea>
                                            </div>
                                        </div>
                                        </fieldset>

                                    </div> <!--Fim dados_meus_locais-->                                              
                                            <hr>

                                            <div class="row m-bot15">
                                                <div class="col-md-12">
                                                  <label for="observacao_pessoa" class="control-label">Observação</label>
                                                  <textarea name="observacao_pessoa" type="text" class="form-control" id="observacao_pessoa" rows="1" onkeyup="maiuscula(this)"></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirma_gravar_cliente">Confirmar Inclusão</button>
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
                        <h4 class="modal-title">Pessoas</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default fecha_editar_dados_incluidos" type="button">Fechar</button>
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
                        <h4 class="modal-title">Pessoas</h4>
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
  $javascript_file_name = 'cliente_fornecedor.js';
  require 'rodape.php';
?>




