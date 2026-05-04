<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $data_parida = date('Y-m-d', strtotime('-1 month', strtotime($data_sistema)));

    if(isset($_REQUEST['local'])) {
        $local = $_REQUEST['local'];
        $estacao_monta = $_REQUEST['estacao'];
        $id_estacao_monta = $_REQUEST['id_estacao'];
    }
    else {
        $local = 0;
        $estacao_monta = '';
        $id_estacao_monta = 0;      
    }

    /*$tipo_monta = $_SESSION['tipo_monta'];
    $tipo_iatf = $_SESSION['tipo_iatf'];
    $tipo_te = $_SESSION['tipo_te'];*/
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

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

    @ session_start();

    $controle_estoque = $_SESSION['controle_estoque'];

    if(isset($_SESSION['menu_manejo_reprodutivo'])) {
        $array_manejo_reprodutivo = explode("!",$_SESSION['menu_manejo_reprodutivo']);

        if ($array_manejo_reprodutivo[1] == 0){
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

    $grupo_usuario = $_SESSION['grupo_usuario'];
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
            <span class="caminho-programa">Reprodução<i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Protocolo IATF</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/cobertura.png"> Protocolo IATF</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_matrizes.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">  
                                        <legend class="scheduler-border fonte-legend">Consultar e Preencher Protocolos</legend>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <input type="button" class="btn btn-primary pull-right" aria-label="Left Align" value="Agenda de Protocolos" onclick="agenda_protocolo()">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3 digitar_filtros">
                                                <input id="lista_cobertura_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_cobertura'] . "'"; ?>>

                                                <input type="hidden" id="local_request"
                                                <?php echo "value='".$local."'";?>>

                                                <input type="hidden" id="estacao_request"
                                                <?php echo "value='".$estacao_monta."'";?>>

                                                <input type="hidden" id="codigo_estacao_request"
                                                <?php echo "value='".$id_estacao_monta."'";?>>

                                                <input type="hidden" id="tipo_registro"
                                                value="C">

                                                <label class="control-label"><span class="required">*</span> Fazenda</label>

                                                <select class="form-control" id="codigo_local" name="codigo_local" onchange="listar_coberturas(this.value);"> 
                                                    <option value="0">...</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3 digitar_filtros">
                                                <label for="estacao_monta" class="control-label"><span class=" digitar_filtrosrequired">*</span> Estação de Monta</label>

                                                <select class="form-control" id="estacao_monta" name="estacao_monta"  onchange="listar_cobertura_estacao(this.value);">
                                                </select>

                                                <input type="hidden" id="estacao_monta_anterior" value="" <?php //echo "value='".$estacao_monta."'";?>>
                                            </div>

                                            <div class="form-group col-md-5" hidden>
                                                <label class="control-label">&nbsp;</label>
                                                <p><span class="page-header data_estacao_monta" style="font-size: 13px;"></span></p>
                                            </div>

                                            <div class="form-group col-md-2 digitar_filtros consultar">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="consultar_cobertura()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros_consulta" hidden>
                                            <div class="col-md-12">
                                                <p style="font-size: 12px; color: #829c9c">Filtros: 
                                                    <span class="descricao_filtro" style="font-weight: normal;">
                                                    </span>

                                                    <span class="mais_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Exibir Filtros" onclick="exibe_mais_filtros()"> 
                                                        <i class="fas fa-filter"></i> +
                                                        </a>
                                                    </span>

                                                    <span class="menos_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Esconder Filtros" onclick="exibe_menos_filtros()"> <i class="fas fa-filter"></i> -
                                                        </a>
                                                    </span>
                                                </p>
                                            </div>

                                        </div> 
                                    </fieldset>

                                    <div class="row">
                                        <div class="form-group col-md-7"></div>

                                        <div class="form-group col-md-2 busca_animal" hidden style="padding: 0px 5px 0px 0px;">
                                            <label class="control-label">&nbsp;</label>
                                            <p style="margin-top: 5px; text-align: right;">Busca:</p>
                                        </div>

                                        <div class="form-group col-md-2 busca_animal" hidden style="padding: 0;">
                                            <label for="codigo_numerico" class="control-label">&nbsp;</label>
                                            <input name="codigo_numerico" type="text" class="form-control" id="codigo_numerico" onchange="show_consulta()"
                                            autocomplete="off" placeholder="Código do Animal">
                                        </div>

                                        <div class="col-md-1 busca_limpar" hidden>
                                            <label class="control-label">&nbsp;</label>
                                            <p>
                                            <a href="#" onclick="limpar_filtros_animal()">Limpar
                                            </a>
                                            </p>
                                        </div>

                                        <div class="form-group col-md-1 busca_consultar" hidden>
                                            <label class="control-label">&nbsp;</label>
                                            <button type="button" class="form-control btn btn-info pull-right" onclick="consultar_cobertura()">Buscar</button>
                                        </div>
                                    </div>

                                    <div id="lista_cobertura"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- page end-->

            <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-xxl modal-dialog-centered modal-direita" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header" style="padding-bottom: 12px">
                            <button data-dismiss="modal" class="btn btn-info pull-right" type="button" onclick="atualizar_lista();">Fechar</button>

                            <button data-dismiss="modal" class="btn btn-success pull-right" type="button" style="margin-right: 6px;" onclick="gerar_lista_excel();">Excel</button>

                            <h4 class="modal-title">Cobertura - Registrar Etapas</h4>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div id="lista_dias_protocolo">

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">

                            <button data-dismiss="modal" class="btn btn-success" type="button" onclick="gerar_lista_excel();">Excel</button>

                            <button data-dismiss="modal" class="btn btn-info" type="button" onclick="atualizar_lista();">Fechar</button>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_diagnostico_negativo" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_diagnostico_negativo();">&times;</button>
                            <h4 class="modal-title">Diagnóstico Negativo</h4>
                        </div>

                        <!--<div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input type="hidden" id="ordem_negativo">

                                    <h4 class="page-header-animais codigo_matriz"></h4>

                                    <label class='control-label' style='font-weight: bold;'><span class='required'>*</span> Quanto ao diagnóstico negativo do animal, selecione uma opção</label>

                                    <div class='clearfix'></div>

                                    <label class='radio-inline'>
                                    <input type='radio' name='opcao_diagnostico' id="nova_cobertura" value='N' onclick='opcao_diagnostico(this.id, this.value)'>Nova Cobertura
                                    </label>

                                    <label class='radio-inline'>
                                    <input type='radio' name='opcao_diagnostico' id="descartar" value='D' onclick='opcao_diagnostico(this.id, this.value)'>Descartar
                                    </label>
                                </div>
                            </div>

                            <div class="row nova_cobertura" hidden>
                                <hr>
                                <div class="form-group col-md-12">
                                    <label class='control-label' style='font-weight: bold;'><span class='required'>*</span> Quanto a nova cobertura, selecine uma opção</label>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="radio-inline">
                                                <input type='radio' name='opcao_nova_cobertura' id="novo_grupo" value='G' onclick='opcao_nova_cobertura(this.id, this.value)'>Incluir em um grupo já existente
                                            </label>
                                        </div>

                                        <div class="form-group col-md-5 grupo_nova_cobertura" hidden>
                                            <label class="control-label"><span class='required'>*</span> Grupo</label>
                                            <select class="form-control" name="grupo_nova_cobertura" id="grupo_nova_cobertura">
                                                <option value="000">...</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="radio-inline">
                                                <input type='radio' name='opcao_nova_cobertura' id="liberar_matriz" value='L' onclick='opcao_nova_cobertura(this.id, this.value)'>Liberar para nova seleção de matrizes
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input type="hidden" id="ordem_negativo">

                                    <h4 class="page-header-animais codigo_matriz" style="text-align: center;"></h4>

                                    <hr>

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <p style='font-weight: bold;'><span class='required'>*</span> Quanto ao diagnóstico negativo do animal, selecine uma opção</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6 label_nova_cobertura">
                                            <label class="radio-inline">
                                            <input type='radio' class="nova_cobertura" name='opcao_nova_cobertura' id="novo_grupo" value='G'>Incluir em um grupo já existente
                                            </label>
                                        </div>

                                        <div class="col-md-6 grupo_nova_cobertura" hidden>
                                            <select class="form-control" name="grupo_nova_cobertura" id="grupo_nova_cobertura">
                                                <option value="000">Selecione Grupo</option>
                                                </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="radio-inline">
                                                <input type='radio' class="nova_cobertura" name='opcao_nova_cobertura' id="liberar_matriz" value='L'>Liberar para nova seleção de fêmeas
                                            </label>
                                        </div>
                                    </div>
                                            
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class='radio-inline'>
                                                <input type='radio' class="nova_cobertura" name='opcao_nova_cobertura' id="descartar" value='D'>Descartar
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" onclick="gravar_diagnostico_negativo();">Confirma</button>

                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_modal_diagnostico_negativo();">Fechar</button>
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
                            <h4 class="modal-title">Cobertura </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_inclusao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Cobertura </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_nova();">Fechar</button>
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
                            <h4 class="modal-title">Cobertura - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>

                            <td style='text-align: left;'></td>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="volta_lista_cobertura" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Cobertura </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_lista();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                </div>
                            </div>
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

 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2025</p></font>
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
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"type="text/javascript" ></script>
<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>
<script src="js/cobertura.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

<script>
    $(document).ready(function(){
        $('#codigo_numerico').typeahead({
            source: function(query, result) {  
                $.ajax({
                    url:"fetch_femeas_servidas.php",
                    method:"POST",
                    data:{query:query,
                          local:$('#codigo_local').val()},
                    dataType:"json",
                    success:function(data)
                    {
                        result($.map(data, function(item){
                        return item;
                        }));
                    }
                })
            }
        });

        $("#codigo_numerico").click(function(){
            var local = $("#codigo_local").val();
            var estacao = $("#estacao_monta").val();

            if (local=='000000000') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe a Fazenda!');
                return;
            }

            if (estacao=='000000000') {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe a Estação de Monta!');
                return;
            }
        });
    });
</script>

<script>
    function reseta_confirma(){
        clickedConfirm = true;
    }

    $(document).ready(function() {
        needToConfirm = false;
        clickedConfirm = false; 
        window.onbeforeunload = askConfirm;
    });

    function askConfirm() {
        if(clickedConfirm){
            needToConfirm = false;
        }
        if (needToConfirm) {
            return ''; 
        }
    }
</script>

</body>
</html>

