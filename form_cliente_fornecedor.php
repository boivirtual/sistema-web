<?php
    include "valida_sessao.inc";

    function diferenca_data($data_validade) {
            
        $data_inicial = date("Y-m-d H:i:s");
        $data_final = $data_validade;
        $time_inicial = strtotime($data_inicial);
        $time_final = strtotime($data_final);
        $diferenca = $time_final - $time_inicial; 
        $dias = (int)floor( $diferenca / (60 * 60 * 24)); 
        return $dias;
    }

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
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/select-1.13.14.css" rel="stylesheet" >

  <style type="text/css">
    /* Filtros múltiplos (mesmo visual do Cadastro de Animais) */
    .bootstrap-select > .dropdown-toggle:hover,
    .bootstrap-select > .dropdown-toggle:focus,
    .bootstrap-select.open > .dropdown-toggle,
    .bootstrap-select.btn-group .dropdown-toggle:hover {
        background-color: #fff !important;
        color: #333 !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .bootstrap-select .bs-actionsbox {
        text-align: right;
        padding: 5px 5px 5px 5px;
    }

    .bootstrap-select .bs-actionsbox .bs-deselect-all {
        display: inline-block;
        float: none;
        padding: 0;
        border: none;
        color: #007aff;
        background: transparent;
        font-size: 13px;
        font-weight: 500;
    }
  </style>

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
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';  
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

        include "conecta_mysql.inc";

        // Filtro da última consulta (para remontar a lista ao voltar da edição/inclusão)
        $filtro_classe = isset($_SESSION['pessoas_filtro_classe']) ? explode(',', $_SESSION['pessoas_filtro_classe']) : array();
        $filtro_fazenda = isset($_SESSION['pessoas_filtro_fazenda']) ? explode(',', $_SESSION['pessoas_filtro_fazenda']) : array();
        $pessoas_consultou = isset($_SESSION['pessoas_consultou']) ? 1 : 0;
        // Ativo: "Sim" já entra marcado na primeira vez
        $filtro_ativo = isset($_SESSION['pessoas_filtro_ativo']) ? explode(',', $_SESSION['pessoas_filtro_ativo']) : array('S');

        if ($cnpj_cliente==97174041604 || $cnpj_cliente==71746307668){
            $tab_classe = mysqli_query($conector, "SELECT tab_codigo_tipo_pessoa, tab_descricao_tipo_pessoa FROM tabela_tipo_pessoas
                                                    WHERE tab_registro_lixeira_tipo_pessoa=0 ORDER BY tab_codigo_tipo_pessoa");
        }
        else {
            $tab_classe = mysqli_query($conector, "SELECT tab_codigo_tipo_pessoa, tab_descricao_tipo_pessoa FROM tabela_tipo_pessoas
                                                    WHERE tab_codigo_tipo_pessoa!=4 AND tab_registro_lixeira_tipo_pessoa=0 ORDER BY tab_codigo_tipo_pessoa");
        }

        $tab_fazendas = mysqli_query($conector, "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa
                                                  WHERE tbl_pessoa_classe=4 AND tbl_pessoa_lixeira=0
                                                  ORDER BY tbl_pessoa_id");
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Pessoas</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-id-card"></i> Pessoas</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                        <div  class="form-group">
                              <a href="form_cliente_fornecedor_incluir.php">
                                  <input type="button" class="btn btn-primary" aria-label="Left Align" 
                                  value="Incluir Novo"/>
                              </a>
                        </div>

                        <section class="panel">
                            <div class="panel-body">
                                <input type="hidden" id="pessoas_consultou" value="<?php echo $pessoas_consultou; ?>">

                                <fieldset class="scheduler-border" id="dados_consulta">
                                    <legend class="scheduler-border fonte-legend">Dados para Consultar</legend>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="classe_filtro" class="control-label">Classe</label>
                                            <select class="form-control selectpicker filtro_pessoas" multiple id="classe_filtro" name="classe_filtro" title="Todas">
                                            <?php while ($reg_classe = mysqli_fetch_object($tab_classe)) { ?>
                                                <option value="<?php echo $reg_classe->tab_codigo_tipo_pessoa; ?>"
                                                    <?php if (in_array((int)$reg_classe->tab_codigo_tipo_pessoa, array_map('intval', $filtro_classe), true)) { echo "selected"; } ?>>
                                                    <?php echo $reg_classe->tab_descricao_tipo_pessoa; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>

                                        <!-- Fazenda: só aparece quando Funcionário (05) está entre as classes escolhidas.
                                             Usa visibility para o botão Consultar não mudar de lugar. -->
                                        <div class="form-group col-md-4" id="div_fazenda_filtro" style="visibility:hidden;">
                                            <label for="fazenda_filtro" class="control-label">Fazenda</label>
                                            <select class="form-control selectpicker filtro_pessoas" multiple id="fazenda_filtro" name="fazenda_filtro" title="Todas">
                                            <?php while ($reg_fazenda = mysqli_fetch_object($tab_fazendas)) { ?>
                                                <option value="<?php echo $reg_fazenda->tbl_pessoa_id; ?>"
                                                    <?php if (in_array((int)$reg_fazenda->tbl_pessoa_id, array_map('intval', $filtro_fazenda), true)) { echo "selected"; } ?>>
                                                    <?php echo $reg_fazenda->tbl_pessoa_nome; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-2 col-md-offset-2">
                                            <label class="control-label">&nbsp;</label>
                                            <button type="button" class="form-control btn btn-primary pull-right" onclick="consultar_pessoas()">Consultar</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </section>

                        <div id="lista_pessoas"></div>
                        <?php mysqli_close($conector); ?>
    	        </div>
	        </div>
	        <!-- page end-->
            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pessoas - Erro</h4>
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
  $javascript_file_name = 'cliente_fornecedor.js';
  require 'rodape.php';
?>



                
                
