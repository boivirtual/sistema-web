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

    .tooltip {
        pointer-events: none;
    }

    .tooltip-inner {
        white-space: pre-line;
    }

    .agenda-body-flex {
        display: flex;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid #e3e6e8;
        border-radius: 4px;
    }

    .agenda-sidebar {
        flex: 0 0 240px;
        width: 240px;
        padding: 16px 14px;
        background: #f7f8f9;
        border-right: 1px solid #e3e6e8;
    }

    .agenda-btn-incluir {
        width: 100%;
    }

    .agenda-filtro-titulo {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #939ba2;
        margin: 16px 0 6px;
    }

    .agenda-fazenda-lista {
        max-height: 180px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .agenda-fazenda-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: normal;
        padding: 6px 2px;
        margin: 0;
        cursor: pointer;
    }

    .agenda-fazenda-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .agenda-main {
        flex: 1 1 320px;
        min-width: 0;
    }

    .agenda-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid #e3e6e8;
    }

    .agenda-toolbar-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .agenda-toolbar-nav i {
        font-size: 14px;
        color: #5c6670;
        cursor: pointer;
    }

    #agenda_periodo_titulo {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .agenda-view-switch {
        display: inline-flex;
        background: #eef0f2;
        border-radius: 6px;
        padding: 2px;
    }

    .agenda-view-btn {
        border: none;
        background: transparent;
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 5px;
        cursor: pointer;
        color: #5c6670;
    }

    .agenda-view-btn.active {
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.12);
        font-weight: 600;
        color: #222;
    }

    .agenda-calendar-area {
        padding: 14px 16px;
        max-height: 650px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .agenda-calendar-area .fc-scrollgrid-section-sticky > * {
        position: relative !important;
        top: auto !important;
    }

    .agenda-calendar-area .fc-timegrid-slot-lane {
        pointer-events: none;
    }

    .agenda-calendar-area table[aria-hidden="true"] {
        pointer-events: none;
    }

    @media (max-width: 767.98px) {
        .agenda-sidebar {
            flex: 1 1 100%;
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e3e6e8;
        }

        .agenda-main {
            flex: 1 1 100%;
        }

        .agenda-toolbar {
            justify-content: center;
            text-align: center;
        }

        #calendar {
            min-width: 700px;
        }
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

    if (isset($_REQUEST['local'])) {
        $local_id = $_REQUEST['local'];
    }
    else {
        $local_id = 0;
    }

    $estacao_monta_id = isset($_REQUEST['estacao_monta_id']) ? $_REQUEST['estacao_monta_id'] : 0;

    $atividade = mysqli_query($conector, "select * from tbl_atividades_padrao 
        where tbl_atividade_padrao_lixeira=0 and 
              tbl_atividade_padrao_id=2"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario 
        WHERE id_usuario = '$codigo_usuario' AND
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
            <span class="caminho-programa">Reprodução <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="#" onclick="voltar_protocolo()">Protocolo IATF</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Agenda de Protocolos</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-10">
                    <h3 class="page-header"><i class="fa fa-calendar"></i> Agenda de Protocolos</h3>
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-info pull-right" style="margin-top: 28px; margin-bottom: 15px;" onclick="voltar_protocolo()">Voltar</button>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <input type="hidden" id="local_request"
                    <?php echo "value='".$local_id."'";?>>

                    <input type="hidden" id="estacao_monta_id_request"
                    <?php echo "value='".$estacao_monta_id."'";?>>

                    <div class="agenda-body-flex">
                        <div class="agenda-sidebar">
                            <button type="button" class="btn btn-primary agenda-btn-incluir" onclick="incluir_nova()">Incluir Nova</button>

                            <div class="agenda-filtro-titulo">Fazendas</div>
                            <div class="agenda-fazenda-lista">
                                <?php
                                    $paleta_cores_fazenda_agenda = array('#378ADD', '#1D9E75', '#D85A30', '#993556', '#BA7517', '#7F77DD', '#639922', '#4A90A4');
                                    $indice_cor_fazenda_agenda = 0;

                                    while ($reg_local_filtro_agenda = mysqli_fetch_object($tbl_local)) {
                                        foreach ($array_locais_usuario as $value) {
                                            $value = ltrim($value);
                                            $value = rtrim($value);

                                            if ($value == $reg_local_filtro_agenda->tbl_pessoa_id) {
                                                $cor_fazenda_agenda = $paleta_cores_fazenda_agenda[$indice_cor_fazenda_agenda % count($paleta_cores_fazenda_agenda)];
                                                $indice_cor_fazenda_agenda++;
                                                $marcado_filtro_local = ($local_id == '' || $local_id == 0 || $local_id == $value) ? 'checked' : '';
                                ?>
                                                <label class="agenda-fazenda-item">
                                                    <input type="checkbox" class="agenda-fazenda-check" value="<?php echo $value; ?>" <?php echo $marcado_filtro_local; ?>>
                                                    <span class="agenda-fazenda-dot" style="background: <?php echo $cor_fazenda_agenda; ?>;"></span>
                                                    <?php echo $reg_local_filtro_agenda->tbl_pessoa_nome; ?>
                                                </label>
                                <?php
                                            }
                                        }
                                    }
                                ?>
                            </div>

                            <div class="agenda-filtro-titulo">Atividade</div>
                            <select class="form-control input-sm" disabled>
                                <option>Reprodução</option>
                            </select>
                        </div>

                        <div class="agenda-main">
                            <div class="agenda-toolbar">
                                <div class="agenda-toolbar-nav">
                                    <button type="button" class="btn btn-default btn-sm" id="agenda_hoje">Hoje</button>
                                    <i class="fas fa-chevron-left" id="agenda_anterior" title="Período anterior"></i>
                                    <i class="fas fa-chevron-right" id="agenda_proximo" title="Próximo período"></i>
                                    <span id="agenda_periodo_titulo"></span>
                                </div>
                                <div class="agenda-view-switch">
                                    <button type="button" class="agenda-view-btn" data-view="timeGridDay">Dia</button>
                                    <button type="button" class="agenda-view-btn active" data-view="timeGridWeek">Semana</button>
                                    <button type="button" class="agenda-view-btn" data-view="dayGridMonth">Mês</button>
                                </div>
                            </div>

                            <div class="agenda-calendar-area">
                                <div id="calendar"></div>
                            </div>
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

                                <input type="hidden" name="posicao_sigla" value="inicio">

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label"><span class="required">*</span> Fazenda(s)</label>
                                        <select class="form-control selectpicker local" id="local" name="local[]" multiple title="Selecione a Fazenda" data-none-selected-text="Selecione a Fazenda">
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
                                        <textarea name="descricao_agenda" type="text" class="form-control" id="descricao_agenda" rows="3" style="min-height: 55px;"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success confirma_gravar pull-left" onclick="gravar_evento()">Confirmar</button>

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

            <div class="modal fade" id="modalPreviewEvento" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
                        <div class="modal-header" style="border-bottom: none; padding: 20px 22px 14px; display: flex; align-items: center; justify-content: flex-end; gap: 22px;">
                            <i class="fas fa-pen" data-toggle="tooltip" data-placement="bottom" title="Editar" style="cursor: pointer; color: #5c6670; font-size: 15px;" onclick="editarEventoDoPreview()" aria-hidden="true"></i>
                            <i class="fas fa-trash-alt" data-toggle="tooltip" data-placement="bottom" title="Excluir" style="cursor: pointer; color: #5c6670; font-size: 15px;" onclick="excluirEventoDoPreview()" aria-hidden="true"></i>
                            <i class="fas fa-times" data-toggle="tooltip" data-placement="bottom" title="Fechar" data-dismiss="modal" style="cursor: pointer; color: #5c6670; font-size: 15px;" aria-hidden="true"></i>
                        </div>
                        <div class="modal-body" style="padding-top: 22px;">
                            <input type="hidden" id="idEventoPreview">

                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <span id="preview_evento_cor" style="width: 12px; height: 12px; border-radius: 50%; margin-top: 6px; flex-shrink: 0;"></span>
                                <div style="flex: 1; min-width: 0;">
                                    <h4 id="preview_evento_titulo" style="margin: 0 0 4px; font-size: 17px; font-weight: 500; word-wrap: break-word;"></h4>
                                    <div id="preview_evento_data" style="font-size: 13px; color: #6c757d; margin-bottom: 26px;"></div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <i class="fas fa-align-left" style="color: #6c757d; width: 12px; margin-top: 3px;" aria-hidden="true"></i>
                                <p id="preview_evento_descricao" style="white-space: pre-wrap; word-wrap: break-word; margin: 0; font-size: 14px; color: #333; flex: 1; min-width: 0;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Excluir evento</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="idEventoExclusao">

                            <h4 id="exclusao_evento_titulo" style="margin: 0 0 6px; font-size: 16px; font-weight: 500; word-wrap: break-word;"></h4>
                            <div id="exclusao_evento_data" style="font-size: 13px; color: #6c757d; margin-bottom: 4px;"></div>
                            <div id="exclusao_evento_atividade" style="font-size: 13px; color: #6c757d; margin-bottom: 14px;"></div>
                            <p id="exclusao_evento_descricao" style="white-space: pre-wrap; word-wrap: break-word; margin: 0; font-size: 14px; color: #333;"></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger pull-left" type="button" onclick="confirmarExclusaoEvento()">Excluir</button>
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
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
  $javascript_file_name = 'agenda_protocolos.js';
  require 'rodape.php';
?>
