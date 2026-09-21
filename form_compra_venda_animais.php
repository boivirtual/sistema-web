<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");

    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS />-->
  <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style type="text/css">
    .bootstrap-select {
      width: 340px !important;
    }

    /* 1. Alinha o container de texto à direita */
    .bootstrap-select .bs-actionsbox {
        text-align: right; 
        padding: 5px 5px 5px 5px; /* Ajusta o padding para melhor visualização */
    }

    /* 2. Garante que o link de deselect seja um bloco de texto que se mova */
    .bootstrap-select .bs-actionsbox .bs-deselect-all {
        display: inline-block; /* Garante que o link se comporte como um bloco inline */
        float: none; /* Garante que não haja float de versões antigas do Bootstrap */
        padding: 0; /* Remove padding interno que possa atrapalhar */
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

    if(isset($_SESSION['menu_gestao_adm'])) {
        $array_cadastro = explode("!",$_SESSION['menu_gestao_adm']);

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

    $tbl_local_origem = mysqli_query($conector, "select * from tbl_pessoa where (tbl_pessoa_classe=1 or tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and tbl_pessoa_lixeira=0"); 

    $tbl_local_destino = mysqli_query($conector, "select * from tbl_pessoa where (tbl_pessoa_classe=1 or tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and tbl_pessoa_lixeira=0"); 

    if ($_SESSION['data_inicial_compra_venda']==''){
        $data_inicial = $ano . '-' . $mes . '-01';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_compra_venda'];  
    }

    if ($_SESSION['data_final_compra_venda']==''){
        $data_final = $ano . '-' . $mes . '-' . $dias_mes;
    }
    else {
        $data_final =  $_SESSION['data_final_compra_venda'];   
    }

    $array_local_origem= $_SESSION['local_origem_compra_venda'];
    $array_local_destino= $_SESSION['local_destino_compra_venda'];
    $array_tipo= $_SESSION['tipo_compra_venda'];

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
                                           lixeira_usuario=0 ";  
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; 
        include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
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
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Compra/Venda Animais</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Compra/Venda Animais</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="form_compra_animais_incluir.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Nova Compra"/>
                        </a>

                        <a href="form_venda_animais_incluir.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Nova Venda"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="#" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <input id="lista_reg_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_compra_venda'] . "'"; ?>>
                                    <input id="exibe_local_origem" type="hidden" <?php echo "value='".$array_local_origem."'"; ?>>
                                    <input id="exibe_local_destino" type="hidden" <?php echo "value='".$array_local_destino."'"; ?>>
                                    <input id="exibe_compra_venda" type="hidden" <?php echo "value='".$array_tipo."'"; ?>>

                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Compra e Venda de Animais</legend>

                                        <div class="row digitar_filtros">
                                            <div class="form-group col-md-4">
                                                <label for="data_inicial" class="control-label">Data Inicial</label>

                                                <input type="date" name="data_inicial" id="data_inicial" class="form-control"
                                                    <?php echo "value='".$data_inicial."'";?>>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="data_final" class="control-label">Data Final</label>
                                                <input name="data_final" type="date" class="form-control" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="tipo_movimentacao" class="control-label">Compra/Venda</label>
                                                <select class="form-control selectpicker" multiple id="tipo_movimentacao" name="tipo_movimentacao">

                                                <option value="2">Compra</option>
                                                <option value="1">Venda</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row digitar_filtros">    
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local_origem" class="control-label">Local Origem</label>
                                                <select class="form-control selectpicker" id="codigo_local_origem" multiple name="codigo_local_origem" data-live-search="true" data-size="7">
                                                <?php while($reg_local = mysqli_fetch_object($tbl_local_origem)) { ?>

                                                    <option value="<?php echo $reg_local->tbl_pessoa_id ?>">
                                                        <?php 
                                                            echo $reg_local->tbl_pessoa_nome;
                                                        ?>
                                                    </option>
                                                <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="codigo_local_destino" class="control-label">Local Destino</label>
                                                <select class="form-control selectpicker" id="codigo_local_destino" multiple name="codigo_local_destino" data-live-search="true" data-size="7">
                                                <?php while($reg_local = mysqli_fetch_object($tbl_local_destino)) { ?>

                                                    <option value="<?php echo $reg_local->tbl_pessoa_id ?>">
                                                        <?php 
                                                            echo $reg_local->tbl_pessoa_nome;
                                                        ?>
                                                    </option>
                                                <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right consultar" onclick="consultar()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros" hidden>
                                            <div class="col-md-11">
                                                <p style="font-size: 12px; color: #829c9c">Filtros: 
                                                    <span class="descricao_filtro" style="font-weight: normal;">
                                                    </span>

                                                    <span class="mais_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Exibir Filtros" onclick="exibe_mais_filtros()"> 
                                                        <i class="fas fa-filter"></i> +
                                                        </a>
                                                    </span>

                                                    <span class="menos_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Esconder Filtros" onclick="exibe_menos_filtros()"> 
                                                        <i class="fas fa-filter"></i> -
                                                        </a>
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="form-group col-md-1 voltar">
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="exibe_mais_filtros()">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros" hidden>
                                            <div class="col-md-12">
                                                <a href="#" style="font-size: 0.9em; font-weight: 500; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle='tooltip' data-placement='top' title="Compras/Vendas" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                            </div>
                                        </div> 
                                    </fieldset> 
                                </div>
                            </div>
                        </form>
                    </div>    
                    <div id="lista_movimentacoes">
                    </div>
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Compra/Venda </h4>
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
                            <h4 class="modal-title">Compra/Venda - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>

        </section>
    </section>

<?php 
  $javascript_file_name = 'compra_venda.js';
  require 'rodape.php';
?>
