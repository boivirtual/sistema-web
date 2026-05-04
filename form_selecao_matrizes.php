<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $data_parida = date('Y-m-d', strtotime('-1 month', strtotime($data_sistema)));

    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
        $erro_importar_pesagem = $_REQUEST["erro"];
    }
    else {
        $erro_importar_pesagem = '';
    }

    $_REQUEST["editar"] = false;

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
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" />

  <link  href="css/select-1.13.14.css" rel="stylesheet"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

</head>

<body>

  <?php

    @ session_start();

    $controle_estoque = $_SESSION['controle_estoque'];

    if(isset($_SESSION['menu_manejo_reprodutivo'])) {
        $array_manejo_reprodutivo = explode("!",$_SESSION['menu_manejo_reprodutivo']);

        if ($array_manejo_reprodutivo[0] == 0){
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
    $codigo_local = $_SESSION['local_matrizes'];
    $tipo_registro = $_SESSION['tipo_registro_matrizes'];
    $estacao_monta = $_SESSION['estacao_monta_matrizes'];
    $codigo_alfa = $_SESSION['codigo_alfa_matrizes'];
    $codigo_numerico = $_SESSION['codigo_numerico_matrizes'];
    $lista_matrizes = $_SESSION['lista_matrizes'];

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

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
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
            <span class="caminho-programa">Reprodução <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Seleção de Fêmeas</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/matrizes.png"> Seleção de Fêmeas</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="form_selecao_matrizes_incluir.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Nova"/>
                        </a>

                        <input type="hidden" id="erro_importar_excel" <?php echo "value='".$erro_importar_pesagem."'";?>>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_matrizes.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Fêmeas Selecionadas para Reprodução</legend>

                                        <input type="hidden" id="lista_automatica" <?php echo "value='".$lista_matrizes."'";?>>

                                        <input type="hidden" id="estacao_monta_anterior" <?php echo "value='".$estacao_monta."'";?>>

                                        <input type="hidden" id="cobertura_programa" value="S">

                                        <div class="row digitar_filtros">  
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" id="codigo_local"  name="codigo_local">

                                                <option value="000000000">...</option>

                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($local_filtro)) { 
                                                        foreach ($array_locais_usuario as $value) {
                                                            $value = ltrim($value);
                                                            $value = rtrim($value); 

                                                            if ($value==$reg_local->tbl_pessoa_id) {

                                                                if ($codigo_local==$reg_local->tbl_pessoa_id) { 
                                                                    echo '<option value="'.$value.'" selected>' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                                }
                                                                else {
                                                                    echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                                }
                                                            }
                                                        }
                                                    } 
                                                ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label class="control-label"><span class="required">*</span> Tipo de Registro</label>

                                                <div class="clearfix"></div>

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_registro" value="C" 
                                                    class="tipo_registro"
                                                    <?php if ($tipo_registro == 'C'){echo "checked";}?>> IATF 
                                                </label>

                                                <label class="radio-inline">
                                                      <input type="radio" name="tipo_registro" value="M"
                                                      class="tipo_registro"
                                                      <?php if ($tipo_registro == 'M'){echo "checked";}?>> Monta
                                                </label>

                                                <label class="radio-inline">
                                                      <input type="radio" name="tipo_registro" value="D"
                                                      class="tipo_registro"
                                                      <?php if ($tipo_registro == 'D'){echo "checked";}?>> Descarte
                                                </label>
                                            </div>

                                            <div class="form-group col-md-3 estacao_monta" hidden>
                                                <label for="estacao_monta" class="control-label"><span class="required">*</span> Estação de Monta</label>
                                                <select class="form-control" id="estacao_monta" name="estacao_monta">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right consultar" onclick="consultar()">Consultar</button>
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

                                            <div class="col-md-2">
                                                <a href="#" style="font-size: 0.9em; font-weight: 600; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle="tooltip" data-placement="top" title="Situação Reprodutiva" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                            </div>
                                        </div> 
                                    </fieldset>

                                    <div class="row busca">
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
                                            <button type="button" class="form-control btn btn-info pull-right" onclick="consultar()">Buscar</button>
                                        </div>
                                    </div>

                                    <div id="lista_matrizes"></div>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
            <!-- page end-->

            <div class="modal fade" id="inserir_nova_matriz_monta" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas Monta - Inserir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_inserir_matriz">

                                <input name="tipo_inserir" type="hidden" id="tipo_inserir" value="0">

                                <div class="alert alert-danger alert_erro_animal" id="alert_erro_animal" hidden="true">
                                    <strong class="negrito"></strong><span></span>
                                </div> 

                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="control-label"><span class="required">*</span> Nº da Fêmea</label>
                                        <input name="id_animal" type="text" class="form-control" id="id_animal" autocomplete="off" onchange="ler_animal_monta()" >
                                    </div>

                                    <div class="form-group col-md-9">
                                        <label class="control-label">&nbsp;</label>
                                        <p id="descricao_animal" class="text-primary"></p>
                                    </div>
                                </div>

                                <!--<div class="row exibe_campos" hidden>
                                    <div class="form-group col-md-4" hidden>
                                        <label class="control-label">Data da Prenhes</label>
                                        <input name="data_prenhes" type="date" class="form-control" id="data_prenhes" onkeypress="return desabilita_enter (this, event)" onblur="calcular_data_previsao()">
                                    </div>

                                    <div class="form-group col-md-4" hidden>
                                        <label class="control-label">Previsão do Parto</label>
                                        <input name="data_previsao" type="date" class="form-control" id="data_previsao" onkeypress="return desabilita_enter (this, event)" onblur="calcular_data_prenhes()">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="control-label">&nbsp;</label>
                                    </div>
                                </div>-->
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success gravar_inserir pull-left" type="button" onclick="gravar_inserir_matrizes_monta()">Gravar</button> 

                            <button data-dismiss="modal" class="btn btn-info pull-right" id="voltar" type="button">Voltar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="inserir_nova_matriz_descarte" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas Descarte - Inserir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_inserir_matriz">

                                <input name="tipo_inserir" type="hidden" id="tipo_inserir" value="0">

                                <div class="alert alert-danger alert_erro_animal" id="alert_erro_animal" hidden="true">
                                    <strong class="negrito"></strong><span></span>
                                </div> 

                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label class="control-label"><span class="required">*</span> Nº da Fêmea</label>
                                        <input name="id_animal_d" type="text" class="form-control" id="id_animal_d" autocomplete="off" onchange="ler_animal_descarte()" >
                                    </div>

                                    <div class="form-group col-md-9">
                                        <label class="control-label">&nbsp;</label>
                                        <p id="descricao_animal_d" class="text-primary"></p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success gravar_inserir pull-left" type="button" onclick="gravar_inserir_matrizes_descarte()">Gravar</button> 

                            <button data-dismiss="modal" class="btn btn-info pull-right" id="voltar" type="button">Voltar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_importar_excel" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="modal_incluirLabel">Importar Excel da pesagem</h3>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="importar_excel_lista_femeas.php" enctype="multipart/form-data" id="form_importar_excel">
                                  
                                <div class="tab-content">
                                    <div class="tab-pane active">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="numero_doc" class="control-label">Nº Documento</label>
                                                <input type="text" class="form-control input-sm numero_doc" readonly=""> 

                                                <input type="hidden" name="numero_doc" id="numero_doc">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="codigo_local" class="control-label">Fazenda</label>
                                                <input type="text" class="form-control input-sm codigo_local" readonly=""> 

                                                <input type="hidden" name="codigo_local" id="codigo_local">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="femeas_listadas" class="control-label">Quantidade de Fêmeas</label>
                                                <input type="text" class="form-control input-sm femeas_listadas" readonly=""> 

                                                <input type="hidden" name="femeas_listadas" id="femeas_listadas">
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row form-group">
                                            <div class="form-group col-md-6">
                                                <label for="arquivo_excel"><span class="required">*</span> Informe o arquivo excel</label>
                                                <input type="file" class="form-control-file" name="arquivo_excel" required>                             
                                            </div>
                                        </div>
                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button">Voltar</button>
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
                            <h4 class="modal-title">Seleção de Fêmeas </h4>
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
                            <h4 class="modal-title">Seleção de Fêmeas </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_nova();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_sair" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Seleção de Fêmeas</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
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
                            <h4 class="modal-title">Seleção de Fêmeas - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript" ></script>
<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>
<script src="js/matrizes.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

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

        $('#id_animal').typeahead({
            source: function(query, result) {  
                $.ajax({
                    url:"fetch_matriz_cobertura.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#codigo_local').val()},
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

        $("#id_animal").click(function(){
            $('#id_animal').val('');
            $('#data_prenhes').val('');
            $('#data_previsao').val('');
            $("#descricao_animal").text('');
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('');
            $(".alert_erro_animal").hide();
            $(".gravar_inserir").hide();
            return false;
        });

        $('#id_animal_d').typeahead({
            source: function(query, result) {  
                $.ajax({
                    url:"fetch_matriz_cobertura.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#codigo_local').val()},
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

        $("#id_animal_d").click(function(){
            $('#id_animal_d').val('');
            $("#descricao_animal_d").text('');
            $("#alert_erro_animal .negrito").html('');
            $("#alert_erro_animal span").html('');
            $(".alert_erro_animal").hide();
            $(".gravar_inserir").hide();
            return false;
        });
    });
</script>

</body>
</html>

