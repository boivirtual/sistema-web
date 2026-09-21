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

    $cpf_cnpj_empresa = $_SESSION['id_cliente'];

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";


        $cli_for = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo'"); 
                                //$rs = mysqli_query($conector, $ssql); 
                     
        $registro_cliente = mysqli_fetch_object($cli_for);
        $razao = $registro_cliente->tbl_pessoa_nome; 
        $classe = $registro_cliente->tbl_pessoa_classe; 
        $tipo_pessoa = $registro_cliente->tbl_pessoa_tipo_pessoa; 
        $email = $registro_cliente->tbl_pessoa_email; 
        $contato = $registro_cliente->tbl_pessoa_contato; 
        $cargo = $registro_cliente->tbl_pessoa_cargo_contato; 
        $ddd = $registro_cliente->tbl_pessoa_ddd; 
        $telefone = $registro_cliente->tbl_pessoa_telefone; 
        $cpf_cnpj = $registro_cliente->tbl_pessoa_cpf_cnpj; 
        $insc_estadual = $registro_cliente->tbl_pessoa_insc_estadual; 
        $insc_municipal = $registro_cliente->tbl_pessoa_insc_municipal; 
        $observacao = $registro_cliente->tbl_pessoa_observacao; 
        $endereco = $registro_cliente->tbl_pessoa_endereco; 
        $numero = $registro_cliente->tbl_pessoa_numero; 
        $complemento = $registro_cliente->tbl_pessoa_complemento; 
        $bairro = $registro_cliente->tbl_pessoa_bairro; 
        $cep = $registro_cliente->tbl_pessoa_cep; 
        $cidade = $registro_cliente->tbl_pessoa_municipio; 
        $estado = $registro_cliente->tbl_pessoa_estado; 
        $cliente_ativo = $registro_cliente->tbl_pessoa_ativo; 
        $data_cadastro = new DateTime($registro_cliente->tbl_pessoa_incluido_em); 

        $area = number_format($registro_cliente->tbl_pessoa_area_fazenda, 2, ',', '.'); 
        $area_util = number_format($registro_cliente->tbl_pessoa_area_util_fazenda, 2, ',', '.'); 
        $localizacao = $registro_cliente->tbl_pessoa_localizacao_fazenda; 
        $latitude = $registro_cliente->tbl_pessoa_latitude_fazenda; 
        $longitude = $registro_cliente->tbl_pessoa_longitude_fazenda; 
        $atv_pec_corte = $registro_cliente->tbl_pessoa_atv_pec_corte; 
        $atv_pec_leite = $registro_cliente->tbl_pessoa_atv_pec_leite; 
        $atv_agricultura = $registro_cliente->tbl_pessoa_atv_agricultura; 
        $atv_outra = $registro_cliente->tbl_pessoa_atv_outra; 
        $descricao_atv_agricola = $registro_cliente->tbl_pessoa_descricao_atv_agricola; 
        $descricao_atv_outra = $registro_cliente->tbl_pessoa_descricao_atv_outra; 

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

        if ($cpf_cnpj_empresa==97174041604 || $cpf_cnpj_empresa==71746307668){
            $tab_classe = mysqli_query($conector, "select * from tabela_tipo_pessoas where tab_registro_lixeira_tipo_pessoa=0"); 
        }
        else {
            $tab_classe = mysqli_query($conector, "select * from tabela_tipo_pessoas where tab_codigo_tipo_pessoa!=4 and tab_registro_lixeira_tipo_pessoa=0"); 
        }

    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastro <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_cliente_fornecedor.php"> Pessoas</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Pessoas Editar</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-id-card"></i> Pessoas - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="gravar_clientes.php" enctype="multipart/form-data" id="form_gravar_cliente">

                            <div class="panel"> 
                                <div class=panel-body>

                                    <input name="cpf_cnpj_empresa" type="hidden" id="cpf_cnpj_empresa"
                                    <?php echo "value='".$cpf_cnpj_empresa."'";?>>

                                    <input name="classe_cliente" type="hidden" id="classe_cliente"
                                    <?php echo "value='".$classe."'";?>>

                                    <input name="codigo_pessoa" type="hidden" id="codigo_pessoa"
                                    <?php echo "value='".$codigo."'";?>>
                                    <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                                    <input name="voltar" type="hidden" id="voltar" value="0"> 
                                                          
                                    <div class="row" id="errors"></div>
                      
                                    <ul class="nav nav-tabs m-bot15">
                                        <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                        </li>
                                        <li class="">
                                        <a data-toggle="tab" href="#outros_contatos">Outros Contatos</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="tab-content">
                                                <div class="tab-pane active">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <button type="button" class="btn btn-primary confirma_gravar_cliente">Confirmar Edição</button>

                                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-7">
                                                    <label for="nome_pessoa" class="control-label"><span class="required">*</span>Razão Social/Nome</label>
                                                    <input name="nome_pessoa" type="text" class="form-control" id="nome_pessoa" required="" 
                                                    onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$razao."'";?>>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="data_inicio" class="control-label">Data do Cadastro</label>
                                                    <input name="data_inicio" type="text" class="form-control" id="data_inicio" readonly=""
                                                    <?php echo "value='".$data_cadastro->format('d/m/Y H:i:s')."'";?>>
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label for="cliente_ativo" class="control-label"></label>
                                                    <div class="clearfix"></div>
                                                    <label class="checkbox-inline">
                                                      <input type="checkbox" name="cliente_ativo" id="cliente_ativo" value="" 
                                                      <?php if ($cliente_ativo == 'S') { echo "checked"; } ?>>Pessoa Ativa
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="row esconde_classe">
                                                <div class="form-group col-md-12">
                                                <label for="classe_pessoa" class="control-label"><span class="required">*</span>Classe de Pessoa</label>
                                                <select class="form-control" id="classe_pessoa" name="classe_pessoa" required="">

                                                  <option value="" selected="selected">...</option>

                                                  <?php while($registro_classe = mysqli_fetch_object($tab_classe)) { ?>

                                                  <option value="<?php 
                                                   echo $registro_classe->tab_codigo_tipo_pessoa ?>"
                                                  
                                                  <?php 
                                                      if($registro_classe->tab_codigo_tipo_pessoa==$classe) 
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
                                                    <?php echo "value='".$cnpj_cpf_editado."'";?> onBlur="validar(this);">
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
                                                <div class="form-group col-md-6">
                                                    <label for="contato_pessoa" class="control-label">Contato</label>
                                                    <input name="contato_pessoa" type="text" class="form-control" id="contato_pessoa" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$contato."'";?>>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="contato_cargo" class="control-label">Cargo</label>
                                                    <input name="contato_cargo" type="text" class="form-control" id="contato_cargo" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$cargo."'";?>>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label for="ddd_pessoa" class="control-label">DDD</label>
                                                    <input name="ddd_pessoa" type="text" class="form-control" id="ddd_pessoa" placeholder="##"
                                                    <?php echo "value='".$ddd."'";?>>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="telefone_pessoa" class="control-label">Telefone</label>
                                                    <input name="telefone_pessoa" type="text" class="form-control" id="telefone_pessoa" placeholder="#########"
                                                    <?php echo "value='".$telefone."'";?>>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="email_pessoa" class="control-label">Email</label>
                                                    <input name="email_pessoa" type="text" class="form-control" id="email_pessoa" onkeyup="minuscula(this)"
                                                    <?php echo "value='".$email."'";?>>
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
                                                    <label for="num_pessoa" class="control-label">Número</label>
                                                    <input name="num_pessoa" type="text" class="form-control" id="num_pessoa" onkeyup="maiuscula(this)"
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

                                    <div id="dados_meus_locais" hidden='true'>
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="area" class="control-label">Área da Fazenda(Ha)</label>
                                                <input name="area" type="text" class="form-control" id="area" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area()" <?php echo "value='".$area."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="area_util" class="control-label">Área Útil da Fazenda(Ha)</label>
                                                <input name="area_util" type="text" class="form-control" id="area_util" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area_util()" <?php echo "value='".$area_util."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="latitude" class="control-label">Latitude</label>
                                                <input name="latitude" type="text" class="form-control" id="latitude" <?php echo "value='".$latitude."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="longitude" class="control-label">Longitude</label>
                                                <input name="longitude" type="text" class="form-control" id="longitude" <?php echo "value='".$longitude."'";?>>
                                            </div>
                                        </div>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border fonte-legend">Atividades</legend>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_pec_corte">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_pec_corte" name="atv_pec_corte" <?php if ($atv_pec_corte == 'S') { echo "checked"; } ?>> Pecuária de Corte
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_pec_leite">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_pec_leite" name="atv_pec_leite" <?php if ($atv_pec_leite == 'S') { echo "checked"; } ?>> Pecuária de Leite
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_agricultura">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_agricultura" name="atv_agricultura" <?php if ($atv_agricultura == 'S') { echo "checked"; } ?>> Agricultura
                                                </label>
                                            </div>

                                            <div class="form-group col-md-10">
                                                <label for="descricao_atv_agricola" class="control-label">Atividades Agrícolas</label>
                                                <textarea name="descricao_atv_agricola" type="text" class="form-control" id="descricao_atv_agricola" rows="2" onkeyup="maiuscula(this)"><?php echo $descricao_atv_agricola;?></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="checkbox-inline" for="atv_outra">
                                                <input class="form-check-input" type="checkbox" value="" id="atv_outra" name="atv_outra" <?php if ($atv_outra == 'S') { echo "checked"; } ?>>Outra
                                                </label>
                                            </div>

                                            <div class="form-group col-md-10">
                                                <label for="descricao_atv_outra" class="control-label">Outras Atividades</label>
                                                <textarea name="descricao_atv_outra" type="text" class="form-control" id="descricao_atv_outra" rows="2" onkeyup="maiuscula(this)"><?php echo $descricao_atv_outra;?></textarea>
                                            </div>
                                        </div>
                                        </fieldset>
                                    </div>
                                              
                                            <div class="row m-bot15">
                                                <div class="col-md-12">
                                                  <label for="observacao_pessoa" class="control-label">Observação</label>
                                                  <textarea name="observacao_pessoa" type="text" class="form-control" id="observacao_pessoa" rows="1" onkeyup="maiuscula(this)"><?php echo $observacao; ?></textarea>
                                                </div>
                                            </div>

	                                        <div class="row">                
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirma_gravar_cliente">Confirmar Edição</button>
                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                </div>
                                            </div>
                                        </div> <!-- dados-->

                                        <div id="outros_contatos" class="tab-pane">
                                        </div>

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



