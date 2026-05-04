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

    if(isset($_REQUEST['diagnostico'])) {
        $diagnostico = $_REQUEST['diagnostico'];
    }
    else {
        $diagnostico = '';
    }

    $previsao_parto_de_filtro = '';
    $previsao_parto_ate_filtro = '';
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
    $tipo_monta = $_SESSION['tipo_monta'];
    $tipo_iatf = $_SESSION['tipo_iatf'];
    $tipo_te = $_SESSION['tipo_te'];

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
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <span class="caminho-programa">Reprodução <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_cobertura_animais.php"> Protocolo</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Diagnóstico Final</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/cobertura.png"> Diagnóstico Final</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_matrizes.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">  
                                        <legend class="scheduler-border fonte-legend">Consultar Diagnóstico Final</legend>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary pull-right" onclick="voltar_cobertura()">Protocolos</button>
                                            </div>
                                        </div>

                                        <div class="row digitar_filtros">
                                            <div class="form-group col-md-3">

                                                <input type="hidden" id="local_request"
                                                <?php echo "value='".$local."'";?>>

                                                <input type="hidden" id="diagnostico_request"
                                                <?php echo "value='".$diagnostico."'";?>>

                                                <input type="hidden" id="estacao_request"
                                                <?php echo "value='".$estacao_monta."'";?>>

                                                <input type="hidden" id="codigo_estacao_request"
                                                <?php echo "value='".$id_estacao_monta."'";?>>

                                                <input type="hidden" id="tipo_registro"
                                                value="F">

                                                <label class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" id="codigo_local" name="codigo_local" onchange="popular_select_estacao_monta(this.value);"> 
                                                    <option value="0">...</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="estacao_monta_servidas" class="control-label"><span class="required">*</span> Estação de Monta</label>

                                                <select class="form-control" id="estacao_monta_servidas" name="estacao_monta_servidas">
                                                </select>
 
                                                <input type="hidden" id="estacao_monta_anterior" value="">
                                            </div>

                                            <div class="form-group col-md-5" hidden>
                                                <label class="control-label">&nbsp;</label>
                                                <p><span class="page-header data_estacao_monta" style="font-size: 13px;"></span></p>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label class="control-label">&nbsp;</label>

                                                <div class="clearfix"></div>

                                                <label class="control-label"><span class="required">*</span> Tipo:&nbsp;&nbsp;
                                                </label>

                                                <label class="checkbox-inline">
                                                <input type="checkbox" name="tipo_cobertura" id='M' value="M" class="tipo_cobertura_diagnostico" 
                                                <?php if ($tipo_monta == 'M'){echo "checked";}?>> Monta
                                                </label>

                                                <label class="checkbox-inline">
                                                <input type="checkbox" name="tipo_cobertura" id='I' value="I"
                                                class="tipo_cobertura_diagnostico"
                                                    <?php if ($tipo_iatf == 'I'){echo "checked";}?>> IATF
                                                </label>

                                                <label class="checkbox-inline">
                                                <input type="checkbox" name="tipo_cobertura" id='T' value="T" 
                                                class="tipo_cobertura_diagnostico"
                                                    <?php if ($tipo_te == 'T'){echo "checked";}?>>TE 
                                                </label>
                                            </div>  
                                        </div>

                                        <div class="row digitar_filtros">
                                            <div class="form-group col-xs-3 col-md-3">
                                                <label for="previsao_parto_de_filtro" class="control-label">Previsão do Parto (de)</label>
                                                <input name="previsao_parto_de_filtro" type="date" class="form-control" id="previsao_parto_de_filtro" 
                                                <?php echo "value='".$previsao_parto_de_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-3 col-md-3">
                                                <label for="previsao_parto_ate_filtro" class="control-label">Previsão do Parto (até)</label>
                                                <input name="previsao_parto_ate_filtro" type="date" class="form-control" id="previsao_parto_ate_filtro" 
                                                <?php echo "value='".$previsao_parto_ate_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="situacao_monta_servidas" class="control-label">Situação</label>

                                                <select class="form-control selectpicker" multiple data-live-search="true" id="situacao_monta_servidas" name="situacao_monta_servidas" >

                                                    <option value=" ">Não Nascidos</option>
                                                    <option value="N">Nascidos</option>
                                                    <option value="A">Aborto</option>
                                                    <option value="M">Natimorto</option>
                                                    <option value="O">Outras</option>
                                                </select>
                                            </div> 

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="listar_femeas_servidas_estacao('P')">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros_consulta" hidden>
                                            <div class="col-md-10">
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

                                            <!--<div class="col-md-2">
                                                <a href="#" style="font-size: 0.9em; font-weight: 600; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle="tooltip" data-placement="top" title="Histórico de Animais" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                            </div>-->
                                        </div> 
                                    </fieldset>
                                    <div id="lista_femeas_servidas"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- page end-->

            <div class="modal fade" id="modal_diagnostico_negativo" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_diagnostico_negativo();">&times;</button>
                            <h4 class="modal-title">Diagnóstico Negativo</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input type="hidden" id="ordem_negativo">

                                    <h4 class="page-header-animais codigo_matriz"></h4>

                                    <label class='control-label' style='font-weight: bold;'><span class='required'>*</span> Quanto ao diagnóstico negativo do animal, selecine uma opção</label>

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
                                                <input type='radio' name='opcao_nova_cobertura' id="liberar_matriz" value='L' onclick='opcao_nova_cobertura(this.id, this.value)'>Liberar para nova seleção de fêmeas
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" onclick="gravar_diagnostico_negativo_femeas_servidas();">Confirma</button>

                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_modal_diagnostico_negativo();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_diagnostico_positivo" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_diagnostico_positivo();">&times;</button>
                            <h4 class="modal-title">Diagnóstico Positivo</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input type="hidden" id="ordem_positivo">

                                    <h4 class="page-header-animais">Confirma o Diagnóstico POSITIVO para a Fêmea <span class="codigo_matriz"></span></h4>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" onclick="gravar_diagnostico_alterar_para_positivo_femeas_servidas();">Confirmar</button>

                            <button data-dismiss="modal" class="btn btn-danger" type="button" onclick="fechar_modal_diagnostico_positivo();">Não Alterar</button>
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
                            <h4 class="modal-title">Fêmeas Servidas </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_positiva" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Fêmeas Servidas </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
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
                            <h4 class="modal-title">Fêmeas Servidas - Mensagem</h4>
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
                            <h4 class="modal-title">Fêmeas Servidas </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_lista();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <?php
                    include "ajuda.php";
                ?>
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
        </section>
    </section>

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

<script src="js/cobertura.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
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

