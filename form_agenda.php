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
  <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css"/>
  <link href="css/select-1.13.14.css" rel="stylesheet" >
  <link href="css/fullcalendarmain.css?<?php echo Versao; ?>" rel="stylesheet" >
  <script src="js/fullcalendarmain.js"></script>

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style>
    @media (max-width: 767.98px) {
        .fc .fc-toolbar.fc-header-toolbar {
            display: block;
            text-align: center;
        }

        .fc-header-toolbar .fc-toolbar-chunk {
            display: block;
        }
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

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_cadastro[2] == 0){
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

    $atividade = mysqli_query($conector, "select * from tbl_atividades_padrao where tbl_atividade_padrao_lixeira=0"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

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

    $array_tipo = '';
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; 
        include "limpar_secao_ctp_aceite.php";
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
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Agenda</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-calendar"></i> Agenda</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Nova" onclick="incluir_nova()"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_agenda.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Agenda</legend>

                                        <div class="row">    
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local" class="control-label">Fazenda</label>
                                                <select class="form-control selectpicker" id="codigo_local" multiple name="codigo_local">
                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($tbl_local)) { 
                                                    
                                                        foreach ($array_locais_usuario as $value) {
                                                            $value = ltrim($value);
                                                            $value = rtrim($value);
                                                            if ($value==$reg_local->tbl_pessoa_id) {
                                                                echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                            }
                                                        }
                                                    } 
                                                 ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="tipo_agenda" class="control-label">Visualizar</label>
                                                <select class="form-control" id="tipo_agenda" name="tipo_agenda">

                                                <option value="1"
                                                    <?php 
                                                    /*    if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==1) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }*/
                                                    ?>>Dia</option>

                                                <option value="2" selected
                                                    <?php 
                                                    /*    if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==2) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }*/
                                                    ?>>Semana</option>

                                                <option value="3"
                                                    <?php 
                                                    /*    if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==3) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }*/
                                                    ?>>Mês</option>
                                                </select>
                                            </div>

                                            <!--<div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="consultar()">Consultar</button>
                                            </div>-->
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>    
                    <div id="dump"></div>
                    <div class="col-lg-12" id="exibir_agenda">
                        <div class="col-lg-12" id="calendar">
                        </div>
                    </div>
                </div>
            </div>
	        <!-- page end-->
            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Agenda - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="form_incluir.php" enctype="multipart/form-data" id="form_incluir" >

                                <input type="hidden" name="idEvento" id="idEvento">

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao">

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label"><span class="required">*</span> Fazenda(s)</label>
                                        <select class="form-control selectpicker local" id="local" name="local[]" multiple>
 
                                        <?php 
                                            while($reg_local = mysqli_fetch_object($local)) { 
                                                        
                                                foreach ($array_locais_usuario as $value) {
                                                    $value = ltrim($value);
                                                    $value = rtrim($value);
                                                    if ($value==$reg_local->tbl_pessoa_id) {
                                                        echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                    }
                                                }
                                            } 
                                        ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="control-label"><span class="required">*</span> Atividade</label>
                                        <select class="form-control" id="atividade" name="atividade">

                                        <option value="0">...</option>  

                                        <?php while($reg_atv = mysqli_fetch_object($atividade)) { ?>

                                        <option value="<?php 
                                            echo $reg_atv->tbl_atividade_padrao_id ?>">
                                                            
                                        <?php 
                                            echo $reg_atv->tbl_atividade_padrao_descricao;
                                        ?>
                                        </option>
                                        <?php } ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="" class="control-label"><span class="required">*</span> Título</label>
                                        <input type="text" class="form-control" name="titulo_agenda" id="titulo_agenda" maxlength="100">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-5 data_hora" hidden>
                                        <label for="" class="control-label label_data_hora"><span class="required">*</span> Data e Hora Início</label>
                                        <input type="datetime-local" class="form-control datas" name="data_hora_agenda_inicio" id="data_hora_agenda_inicio">
                                    </div>

                                    <div class="form-group col-md-5 data_hora" hidden>
                                        <label for="" class="control-label"> Data e Hora Fim</label>
                                        <input type="datetime-local" class="form-control datas" name="data_hora_agenda_fim" id="data_hora_agenda_fim">
                                    </div>

                                    <div class="form-group col-md-5 data">
                                        <label for="" class="control-label label_data"><span class="required">*</span> Data Início</label>
                                        <input type="date" class="form-control datas" name="data_agenda_inicio" id="data_agenda_inicio">
                                    </div>

                                    <div class="form-group col-md-5 data">
                                        <label for="" class="control-label">Data Fim</label>
                                        <input type="date" class="form-control datas" name="data_agenda_fim" id="data_agenda_fim">
                                    </div>

                                    <div class="form-group col-md-2 dia_todo">
                                        <label for="" class="control-label">&nbsp;</label>
                                        
                                        <div class="checkbox">
                                            <label>
                                            <input type="checkbox" name="dia_inteiro" id="dia_inteiro" checked> Dia Todo
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-bot15">
                                    <div class="col-md-12">
                                        <label for="descricao_agenda" class="control-label">Descrição</label>
                                        <textarea name="descricao_agenda" type="text" class="form-control" id="descricao_agenda" rows="5"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success confirma_gravar" onclick="gravar_evento()">Confirmar</button>

                            <button class="btn btn-danger confirma_exclusao" onclick="excluir_evento()">Excluir</button>
                            
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="modalEditarEvento" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Agenda - Editar/Excluir Evento</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="idEvento" id="idEvento">
                            <div class="form-group">
                                <label for="" class="control-label"><span class="required">*</span> Título</label>
                                <input type="text" class="form-control" name="tituloEvento" id="tituloEvento" maxlength="100">
                            </div>
                            <div class="row">
                                    <div class="form-group col-md-5 data_hora" hidden>
                                        <label for="" class="control-label"><span class="required">*</span> Data e Hora Início</label>
                                        <input type="datetime-local" class="form-control datas" name="data_hora_agenda_inicio" id="data_hora_agenda_inicio">
                                    </div>

                                    <div class="form-group col-md-5 data_hora" hidden>
                                        <label for="" class="control-label"> Data e Hora Fim</label>
                                        <input type="datetime-local" class="form-control datas" name="data_hora_agenda_fim" id="data_hora_agenda_fim">
                                    </div>

                                    <div class="form-group col-md-5 data">
                                        <label for="" class="control-label"><span class="required">*</span> Data Início</label>
                                        <input type="date" class="form-control datas" name="data_agenda_inicio" id="data_agenda_inicio">
                                    </div>

                                    <div class="form-group col-md-5 data">
                                        <label for="" class="control-label">Data Fim</label>
                                        <input type="date" class="form-control datas" name="data_agenda_fim" id="data_agenda_fim">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="" class="control-label">&nbsp;</label>
                                        
                                        <div class="checkbox">
                                            <label>
                                            <input type="checkbox" name="dia_inteiro" id="dia_inteiro" checked> Dia Todo
                                            </label>
                                        </div>
                                    </div>
                            </div>

                            <div class="form-group">
                                <label for="descricaoEvento" class="control-label">Descrição</label>
                                <textarea name="descricaoEvento" type="text" class="form-control" id="descricaoEvento" rows="5"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="">Fechar</button>
                            <button class="btn btn-danger" id="excluirEvento" onclick="excluirEvento()">Excluir</button>
                            <button class="btn btn-success" id="editarEvento" onclick="editar_evento()">Salvar</button>
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
                            <h4 class="modal-title">Agenda </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="consultar(); incluir_nova();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_editar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Agenda</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_editar();">Fechar</button>
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
                            <h4 class="modal-title">Agenda - Mensagem</h4>
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
</html>
<?php 
  $javascript_file_name = 'agenda.js';
  require 'rodape.php';
?>
