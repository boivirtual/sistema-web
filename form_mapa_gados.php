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
  <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css" />

  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <script src="js/DragDropTouch.js"></script>


<!--    <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" rel="stylesheet" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="assets/materialize/css/materialize.css?<?php echo Versao; ?>" rel="stylesheet" media="screen,projection" />

    <link href="css/select-1.13.14.css" rel="stylesheet">
    <link href="css/fullcalendarmain.css" rel="stylesheet">
    <script src="js/fullcalendarmain.js"></script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> -->
</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_manejo = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_manejo[0] == 0){
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
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
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

    <section id="main-content" style="overflow: hidden;">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Mapa de Gado</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-map-o"></i> Mapa de Gado</h3>
                    <ol class="breadcrumb">

                        <li id="fazendas-select">
                            <label>Fazendas:</label>
                            <select class="select-empresa-menu-control custom-select" id="codigo_local" name="codigo_local" onchange="consultar_mapa(); initMap();"> 
                                <option value="0">...</option>
                            </select>

                            <input type="hidden" name="local_sessao" id="local_sessao" <?php if(isset($_POST["mapa_local_id"])){echo "value='".$_POST["mapa_local_id"]."'";}?>>

                            <input type="hidden" id="tipo_mapa_gado" 
                            <?php echo "value='".$_SESSION['tipo_mapa_gado']."'";?>>

                            <input type="text" id="id_cliente" 
                            <?php echo "value='".$_SESSION['id_cliente']."'";?>>


                        </li>

                        <li id="mapa_satelite" class="no-before">
                            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Mapa Satelite" onclick="mapa_satelite()"><i class="fa fa-map-o" style="font-size:20px;"></i></a>
                        </li>
                            
                        <li id="mapa_tabuleiro" class="no-before">
                            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Mapa Tabuleiro" onclick="mapa_tabuleiro()"><i class="fa fa-object-group" style="font-size:20px;"></i></a>
                        </li>

                        <li class="pull-right no-before"><label id='totalAnimais'></label></li>
                    </ol>

                    <div class="col-xs-12" id="divTotalAnimais" style="background-color: white; text-align: center;" hidden><label id='totalAnimais'></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 esconder" hidden="true">

                    <div id="consulta_contas" class="consulta_contas" style="color: black; overflow-y: auto !important;
                        overflow-x: hidden; height: 400px;" hidden="true">
                    </div>
 
                    <div id="map" style="width: 100%; height: 400px; margin-top: 10px;">
                    </div>
                </div>
            </div>

	        <!-- page end-->

            <div class="modal fade" id="modal_composicao_descricao_lote" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle myLargeModalLabel"  data-backdrop="static">
                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Composição da Descrição do Lote
                            </h4>

                            <input type="hidden" name="numero_item" id="numero_item">

                            <input type="hidden" name="edicao" id="edicao">
                            <input type='hidden' id='descricao_lote'>
                            <input type="hidden" id="id_pasto_destino">
                            <input type="hidden" id="desc_pasto_destino">
                            <input type="hidden" id="desc_lote_destino">
                            <input type="hidden" id="qual_pasto">
                            <input type="hidden" id="qual_programa" value="tabuleiro">
                            <input type="hidden" id="descricao_lote_montada">
                            <input type="hidden" id="pasto_destino_estava_vazio">
                        </div>

                        <div class="modal-body">
                            <div class="container">

                            <div class='row'>
                                <div class="col-xs-12 col-md-12 span_centro">
                                     <span class="info_pasto desc_pasto">
                                    </span>
                                </div>                             
                            </div>

                            <div class='row'>
                                <div class="col-xs-12 col-md-12 span_centro">
                                    <span class="info_lote desc_lote">
                                    </span>
                                </div>                             
                            </div>

                            <div class='row opcoes_descricao_lote' hidden>
                                <div class='col-xs-12 col-md-12'>
                                    <div class="form-check manter_lote">
                                        <input class="form-check-input opcao_lote" type="radio" name="opcao_lote" id="manter_lote" value="M">
                                        <label class="form-check-label" for="manter_lote"> Manter a Descrição do Lote
                                        </label>
                                    </div>

                                    <div class="form-check novo_lote">
                                        <input class="form-check-input opcao_lote" type="radio" name="opcao_lote" id="novo_lote" value="N">
                                        <label class="form-check-label" for="novo_lote"> Criar nova Descrição do Lote
                                        </label>
                                    </div>

                                    <div class="form-check levar_lote">
                                        <input class="form-check-input opcao_lote" type="radio" name="opcao_lote" id="levar_lote" value="L">
                                        <label class="form-check-label" for="levar_lote"> Levar a Descrição do Lote
                                        </label>
                                    </div>


                                </div>
                            </div>

                            <hr class="linha_hr">

                            <div class="monta_descricao_lote" hidden>
                            <div class='row'>
                                <div class="form-group col-md-3 descricao_principal">
                                    <label class="control-label"><span class="required">*</span> Descrição do Lote</label>
                                    <select class="form-control" name="descricao_principal" id="descricao_principal" onchange="popular_situacao()">
                                    </select>
                                </div>

                                <div class='form-group col-md-3 exibir_parametro_2' hidden>
                                    <label class="control-label label_parametro_2">Situação</label>
                                    <select class="form-control" name="situacao_principal" id="situacao_principal" onchange="exibir_parametro_3()">
                                    </select>
                                </div>

                                <div class='form-group col-md-4 exibir_parametro_3' hidden>
                                    <label class="control-label label_parametro_3">Informar Data da Parição? </label>

                                    <div class="clearfix"></div>
                                    
                                    <label class="checkbox-inline">
                                        <input type="checkbox" id="com_data" name="data_paricao" value="S"> Sim
                                    </label>
                                </div>

                                <div class='form-group col-md-3 exibir_parametro_4' hidden>
                                    <label class="control-label label_parametro_4">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal" id="data_paricao_principal" onchange="exibe_descricao_lote()">
                                </div>

                                <div class='form-group col-md-3 exibir_parametro_4_data_mais' hidden>
                                    <label class="control-label label_parametro_4_mais">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal_mais" id="data_paricao_principal_mais" onchange="exibe_descricao_lote_mais_data()">
                                </div>

                                <div class='col-md-3 exibir_parametro_4_mais' hidden>
                                    <label class="control-label">&nbsp;</label>

                                    <a class='btn' href='#' style="font-size: 0.8em; font-weight: 500; color: #128cb8; float: right;" onclick="incluir_mais_data(1)"><i class="fa fa-plus" data-toggle='tooltip' data-placement='left' title='Informar mais datas'></i> Incluir mais Data</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 exibir_incluir_mais">
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.8em; font-weight: 500; text-align: right; color: #128cb8;" onclick="incluir_mais_lote()"><i class="fa fa-plus" data-toggle='tooltip' data-placement='left' title=''></i> Incluir mais lote</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao">
                                    <input type="text" id='descricao_novo_lote' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>
                                <div class="col-md-1 exibir_opcoes">
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(1)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao2" hidden> 
                                    <input type="text" id='descricao_novo_lote2' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>

                                <div class="col-md-1 exibir_opcoes2" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(2)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao3" hidden> 
                                    <input type="text" id='descricao_novo_lote3' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes3" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(3)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao4" hidden> 
                                    <input type="text" id='descricao_novo_lote4' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes4" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(4)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao5" hidden> 
                                    <input type="text" id='descricao_novo_lote5' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>

                                <div class="col-md-1 exibir_opcoes5" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(5)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao6" hidden> 
                                    <input type="text" id='descricao_novo_lote6' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes6" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(6)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>
                            </div> <!--Fim monta descricao lote -->
                        </div> <!-- Fim container --> 
                        </div> <!-- Fim modal-body-->

                        <div class="modal-footer">
                            <div class=" monta_descricao_lote" hidden>
                                <button type="button" class="btn btn-primary confirma_composicao" onclick="confirma_composicao_descricao_lote()">Confirmar
                                </button>

                                <button type='button' class='btn btn-info pull-right voltar_descricao_lote' data-dismiss='modal'>Voltar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='modal_mover_todos_tabuleiro' tabindex='-1' role='dialog' 
                aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                            <h4 class='modal-title'>Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class='modal-body'>
                            <p id="primeira_mensagem"></p>
                            <p id="segunda_mensagem"></p>
                        </div>

                        <div class='modal-footer'>
                            <input type="hidden" id="id_entrada">
                            <input type="hidden" id="id_saida">
                            <button class='btn btn-success' type='button' onclick='gravar_retirar_tudo_tabuleiro();'>Sim</button>

                            <button data-dismiss='modal' class='btn btn-default' type='button'>Não</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sucesso_mover_animais" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Mapa de Gado - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" onclick="exibe_opcoes_desc_lote_pasto_destino_tabuleiro()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sucesso_descricao_novo_lote_destino" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="fechar_mensagem_sucesso_tabuleiro();">Fechar</button>
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
                            <h4 class="modal-title">Mapa de Gados</h4>
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
                            <h4 class="modal-title">Mapa de Gados - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='modal fade' id='mensagem_manter_desc_pasto_destino' tabindex='-1' role='dialog' 
                aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>
                <div class='modal-dialog modal-dialog-centered' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                            <h4 class='modal-title'>Composição da Descrição do Lote</h4>
                        </div>
                        <div class='modal-body'>
                        </div>

                        <div class='modal-footer'>
                            <button class='btn btn-success' type='button' onclick='fechar_mensagem_sucesso_tabuleiro();'>Sim</button>

                            <button data-dismiss='modal' class='btn btn-default' type='button' onclick='retorna_composicao_descricao_lote();'>Não</button>
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
 <!--main content end-->
 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
     </div>
 </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->
<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/ga.js?<?php echo Versao; ?>" type="text/javascript" ></script>
<script src="js/bootstrap-switch.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.tagsinput.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.hotkeys.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg-custom.js?<?php echo Versao; ?>"></script>
<script src="js/moment.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="js/mapa_gados.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

<script>
    $(document).ready(function(){
 	   $('[data-toggle="tooltip"]').tooltip();   
    });

</script>

<script>
    var map;
    function initMap() {
        let local = $("#codigo_local").val();
        let id_cliente = $("#id_cliente").val();

        if (local==0 || local=='') {
            local = $("#local_sessao").val();
        }

        pastos_animais = JSON.parse(readAnimalPasto(local));

        //let coordenadas_fazenda = pastos_animais[0].coordenadas_fazenda;
        /*if (id_cliente=='10956925774') {
            let latitude = -20.4785175323; //parseFloat(pastos_animais[0].latitude);
            let longitude = -41.5155754089; //parseFloat (pastos_animais[0].longitude);
        }
        else {*/
            let latitude = parseFloat(pastos_animais[0].latitude);
            let longitude = parseFloat (pastos_animais[0].longitude);
        //}

        var mapOptions = {
            //center: new google.maps.LatLng(-19.97033256, -42.48485871),
            //center: new google.maps.LatLng(lat, let),
            center: {lat: latitude, lng: longitude},
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.HYBRID
        };

        /*map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: {lat: -19.97033256, lng: -42.48485871},
            mapTypeId: 'hybrid'
        });*/

        var map = new google.maps.Map(document.getElementById("map"),
            mapOptions);

        //map.data.loadGeoJson('https://www.agrolandes.com.br/teste_boivirtual/sistema/mapa/'+id_cliente+'/'+local+'.json');

        map.data.loadGeoJson('https://boivirtual.com.br/sistema/mapa/'+id_cliente+'/'+local+'.json');

        map.data.setStyle({
            fillColor: 'green',
            strokeWeight: 1,
            strokeColor: 'white'
        });

        var infowindow = new google.maps.InfoWindow();
        
        map.data.addListener('click', function(event) {
            let pasto = event.feature.getProperty("name");
            pasto = pasto.toUpperCase();

            let total_animais = '';
            let animais_pasto = '';
            let descricao_capim = '';

            for (var i = 0; i < pastos_animais.length; i++) {
                if (pasto==pastos_animais[i].descricao){
                    total_animais = pastos_animais[i].total_animais;
                    dias_com = pastos_animais[i].dias_com_animais;
                    dias_sem = pastos_animais[i].dias_sem_animais;
                    descricao_capim = pastos_animais[i].descricao_capim;

                    if (total_animais!=0) {
                        total_animais+= ' animais';
                        animais_pasto = 'Animais no pasto há ' + dias_com + ' dia(s)'; 
                    }
                    else {
                       total_animais = '';
                       animais_pasto = 'Pasto vazio há ' + dias_sem + ' dia(s)';
                    }
                }
            }

            if (animais_pasto=='') {
                animais_pasto = 'Este pasto nao existe no sistema';
            }

            let html = pasto + '<br>' + 
                       total_animais + '<br>' + 
                       animais_pasto + '<br>' + 
                       descricao_capim;

            infowindow.setContent(html); // show the html variable in the infowindow
            infowindow.setPosition(event.latLng); // anchor the infowindow at the marker
            infowindow.setOptions({pixelOffset: new google.maps.Size(0,-20)}); // move the infowindow up slightly to the top of the marker icon
            infowindow.open(map);
        });

        map.data.addListener('dblclick', function(event) {
            let pasto = event.feature.getProperty("name");
            pasto = pasto.toUpperCase();

            for (var i = 0; i < pastos_animais.length; i++) {
                if (pasto==pastos_animais[i].descricao) {
                    let id = pastos_animais[i].id_pasto;
                    id = '"'+id+'"';
                    mais_info_mapa_satelite(id);
                }
            }
        });

        //var requestURL = 'https://www.agrolandes.com.br/teste_boivirtual/sistema/mapa/'+id_cliente+'/'+local+'.json';

        var requestURL = 'https://boivirtual.com.br/sistema/mapa/'+id_cliente+'/'+local+'.json';

        var request = new XMLHttpRequest();
        request.open('POST', requestURL);
        request.responseType = 'json';
        request.send();

        request.onload = function() {
            var json = request.response;
            //populateHeader(json);
            showJson(json);
        }

        function showJson(jsonObj) {
            var pastos = jsonObj['features'];

            for (var i = 0; i < pastos.length; i++) {
                geometry = pastos[i].geometry;
                pasto = pastos[i].properties.name;
                pasto = pasto.toUpperCase();

                for (var j = 0; j < pastos_animais.length; j++) {
                    if (pasto==pastos_animais[j].descricao && pastos_animais[j].tem_animal=='S') {

                        center = centroid(geometry);
                        var farolAveiro = new google.maps.LatLng(center[0],center[1]);
                        var minhaImagem = 'img/cow-export.png';

                        const image = {
                            //url: "https://agrolandes.com.br/teste_boivirtual/sistema/img/cow-export.png",
                            url: "https://boivirtual.com.br/sistema/img/cow-export.png",
                            // This marker is 20 pixels wide by 32 pixels high.
                            size: new google.maps.Size(28, 33),
                            // The origin for this image is (0, 0).
                            origin: new google.maps.Point(0, 0),
                            // The anchor for this image is the base of the flagpole at (0, 32).
                            anchor: new google.maps.Point(10, 32),
                        };

                        var marker = new google.maps.Marker({
                        //var marker = new google.maps.marker.AdvancedMarkerElement({
                            position: farolAveiro, // variável com as coordenadas Lat e Lng
                            map: map,
                            //title: pasto,
                            icon: image,
                            zIndex: image[-1]
                        });

                        google.maps.event.addDomListener(window, 'load', marker);
                    }
                }
            }
        }


        function area(poly){
            var s = 0.0;
            var ring = poly.coordinates[0];
            for(i= 0; i < (ring.length-1); i++){
              s += (ring[i][0] * ring[i+1][1] - ring[i+1][0] * ring[i][1]);
            }
            return 0.5 *s;
        }

        function centroid(poly){
            var c = [0,0];
            var ring = poly.coordinates[0];
            for(i= 0; i < (ring.length-1); i++){
              c[1] += (ring[i][0] + ring[i+1][0]) * (ring[i][0]*ring[i+1][1] - ring[i+1][0]*ring[i][1]);
              c[0] += (ring[i][1] + ring[i+1][1]) * (ring[i][0]*ring[i+1][1] - ring[i+1][0]*ring[i][1]);
            }
            var a = area(poly);
            c[0] /= a*6;
            c[1] /= a*6;
            return [c[0],c[1]];
        }


        function readAnimalPasto(local) {
            return $.ajax({
                type: "POST",
                data: { local: local },
                url: "ler_animal_pasto_mapa_satelite.php",
                async: false,
            }).responseText;
        }

    }
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41i-kN1gTpkttSheLSvB6MdU8tAVD6x4&callback=initMap">
</script>

<script src='js/jquery.redirect.js'></script>

<script>
    function mais_info_mapa_satelite(clicked_id){
        $.redirect('form_mapa_gados_movimentacao.php', {'pasto_id': clicked_id});
    }
</script>

</body>

</html>
