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

  <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
  </style>

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_manejo_reprodutivo'])) {
        $array_gestao_adm = explode("!",$_SESSION['menu_manejo_reprodutivo']);

        if ($array_gestao_adm[2] == 0){
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
    $array_local = $_SESSION['local_nascimento'];

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

    if ($_SESSION['data_inicial_nascimento']==''){
        $data_inicial = $ano . '-' . $mes . '-01';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_nascimento'];  
    }

    if ($_SESSION['data_final_nascimento']==''){
        $data_final = $ano . '-' . $mes . '-' . $dias_mes;
    }
    else {
        $data_final =  $_SESSION['data_final_nascimento'];   
    }

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

    $array_local = $_SESSION['local_pastos'];

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
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Protocolos IATF</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-star-o"></i> Protocolos IATF</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div id="lista_protocolos">
                    </div>

                </div>
            </div>
            <!-- page end-->


            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-xl modal-direita" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                            <h4 class="modal-title" id="modal_incluirLabel">Protocolos IATF - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_protocoloIATF.php" enctype="multipart/form-data" id="form_gravar_protocolo">
                              
                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">
                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <!-- <button type="button" class="btn btn-info pull-right voltar_inclusao" data-dismiss="modal" onclick="location.reload();">Voltar</button> -->
                                                <button type="button" class="btn btn-info pull-right voltar" data-dismiss="modal">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div>
                                                <input name="codigo_conta" type="hidden" class="form-control m-bot15" id="codigo_conta">
                                                <input name="tipo_gravacao" type="hidden" class="form-control m-bot15" id="tipo_gravacao">
                                            </div>
                                            <div class="form-group col-md-6 col-xs-12">
                                                <label for="nome_protocolo" class="control-label"><span class="required">*</span> Nome do Protocolo</label>
                                                <input name="nome_protocolo" type="text" class="form-control m-bot15" id="nome_protocolo">
                                            </div>
                                            <div class="form-group col-md-2 col-xs-12">
                                                <label for="quant_protocolo" class="control-label"><span class="required">*</span> Qtd de Protocolos</label>
                                                <input name="quant_protocolo" type="text" class="form-control m-bot15" id="quant_protocolo" onkeypress = "return numeros(this, event)" maxlength="4" style="width: 5em;">
                                            </div>
                                            <div class="form-group col-md-2 col-xs-12">
                                                <label for="qtd_dias" class="control-label"><span class="required">*</span> Dias para Diagnóstico</label>
                                                <input name="qtd_dias" type="text" class="form-control m-bot15" id="qtd_dias" onkeypress = "return numeros(this, event)" maxlength="2" data-toggle='tooltip' data-placement='top' title="Quantidade de dias para o diagnóstico a partir da data da inseminação" style="width: 4em;">
                                            </div>
                                        </div>


                                        <hr>
                                        <div class="row" id="div_0">
                                            <div>
                                                <input name="codigo_item_0" type="hidden" class="form-control m-bot15" id="codigo_item_0">
                                            </div>
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_0" class="control-label"><span class="required">*</span> Dia 0</label>
                                                    <input name="nome_prod_0" type="text" class="form-control m-bot15" id="nome_prod_0" placeholder="Produto ou orientação">
                                                    <input name="descricao_0" type="hidden" class="form-control m-bot15" id="descricao_0" value="Dia 0">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1">
                                                    <label for="quantidade_0" class="control-label">Qtd</label>
                                                    <input name="quantidade_0" type="number" class="form-control m-bot15" id="quantidade_0" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="unidade_0" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0" id="unidade_0_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0" id="unidade_0_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_0" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0" onclick="mais_med(this.name, this.value);" id="mais_med_0_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0" onclick="mais_med(this.name, this.value);" id="mais_med_0_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_0_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                    <label for="numero_dias_0" class="control-label">Dias p/ próxima etapa à partir do 1° dia</label>
                                                    <input name="numero_dias_0" type="number" class="form-control m-bot15" id="numero_dias_0" onkeyup="mostrar_linhas(this.id, this.value);">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_0_linha_1">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_0_1" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_0_1" type="text" class="form-control m-bot15" id="nome_prod_0_1" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_0_1">
                                                    <label for="quantidade_0_1" class="control-label">Qtd</label>
                                                    <input name="quantidade_0_1" type="number" class="form-control m-bot15" id="quantidade_0_1" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_0_1">
                                                    <label for="unidade_0_1" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0_1" id="unidade_0_1_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0_1" id="unidade_0_1_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_0_1" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_1" onclick="mais_med(this.name, this.value);" id="mais_med_0_1_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_1" onclick="mais_med(this.name, this.value);" id="mais_med_0_1_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_1" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_0_1_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_0_linha_2">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_0_2" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_0_2" type="text" class="form-control m-bot15" id="nome_prod_0_2" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_0_2">
                                                    <label for="quantidade_0_2" class="control-label">Qtd</label>
                                                    <input name="quantidade_0_2" type="number" class="form-control m-bot15" id="quantidade_0_2" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_0_2">
                                                    <label for="unidade_0_2" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0_2" id="unidade_0_2_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0_2" id="unidade_0_2_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_0_2" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_2" onclick="mais_med(this.name, this.value);" id="mais_med_0_2_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_2" onclick="mais_med(this.name, this.value);" id="mais_med_0_2_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_2" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_0_2_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_0_linha_3">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_0_3" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_0_3" type="text" class="form-control m-bot15" id="nome_prod_0_3" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_0_3">
                                                    <label for="quantidade_0_3" class="control-label">Qtd</label>
                                                    <input name="quantidade_0_3" type="number" class="form-control m-bot15" id="quantidade_0_3" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_0_3">
                                                    <label for="unidade_0_3" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0_3" id="unidade_0_3_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_0_3" id="unidade_0_3_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_0_3" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_3" id="mais_med_0_3_O" value="S" disabled>Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_3" id="mais_med_0_3_M" value="N" disabled>Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_0_3" onclick="mostrar_dias(this.id);" id="mais_med_0_3_N" value="N" checked>Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <hr id="hr_1" hidden>
                                        <div class="row" hidden id="div_1">
                                            <div>
                                                <input name="codigo_item_1" type="hidden" class="form-control m-bot15" id="codigo_item_1">
                                                <input name="lixeira_item_1" type="hidden" class="form-control m-bot15" id="lixeira_item_1">
                                            </div>
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-lg-12 col-md-12 pull-right" style="padding-right: 0;">
                                                    <a id="enviar_lixeira_1" class='btn pull-right' style="color: red;" onclick="enviar_item_lixeira(this.id);"><i class='icon_trash_alt' title='Enviar para lixeira'></i></a>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_1" id="lbl_1" class="control-label">Dia X1</label>
                                                    <input name="nome_prod_1" type="text" class="form-control m-bot15" id="nome_prod_1" placeholder="Produto ou orientação">
                                                    <input name="descricao_1" type="hidden" class="form-control m-bot15" id="descricao_1">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1">
                                                    <label for="quantidade_1" class="control-label">Qtd</label>
                                                    <input name="quantidade_1" type="number" class="form-control m-bot15" id="quantidade_1" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="unidade_1" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1" id="unidade_1_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1" id="unidade_1_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_1" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1" onclick="mais_med(this.name, this.value);" id="mais_med_1_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1" onclick="mais_med(this.name, this.value);" id="mais_med_1_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_1_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                    <label for="numero_dias_1" class="control-label">Dias p/ próxima etapa à partir do 1° dia</label>
                                                    <input name="numero_dias_1" type="number" class="form-control m-bot15" id="numero_dias_1" onkeyup="mostrar_linhas(this.id, this.value);">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_1_linha_1">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_1_1" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_1_1" type="text" class="form-control m-bot15" id="nome_prod_1_1" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_1_1">
                                                    <label for="quantidade_1_1" class="control-label">Qtd</label>
                                                    <input name="quantidade_1_1" type="number" class="form-control m-bot15" id="quantidade_1_1" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_1_1">
                                                    <label for="unidade_1_1" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1_1" id="unidade_1_1_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1_1" id="unidade_1_1_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_1_1" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_1" onclick="mais_med(this.name, this.value);" id="mais_med_1_1_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_1" onclick="mais_med(this.name, this.value);" id="mais_med_1_1_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_1" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_1_1_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_1_linha_2">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_1_2" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_1_2" type="text" class="form-control m-bot15" id="nome_prod_1_2" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_1_2">
                                                    <label for="quantidade_1_2" class="control-label">Qtd</label>
                                                    <input name="quantidade_1_2" type="number" class="form-control m-bot15" id="quantidade_1_2" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_1_2">
                                                    <label for="unidade_1_2" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1_2" id="unidade_1_2_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1_2" id="unidade_1_2_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_1_2" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_2" onclick="mais_med(this.name, this.value);" id="mais_med_1_2_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_2" onclick="mais_med(this.name, this.value);" id="mais_med_1_2_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_2" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_1_2_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_1_linha_3">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_1_3" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_1_3" type="text" class="form-control m-bot15" id="nome_prod_1_3" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_1_3">
                                                    <label for="quantidade_1_3" class="control-label">Qtd</label>
                                                    <input name="quantidade_1_3" type="number" class="form-control m-bot15" id="quantidade_1_3" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_1_3">
                                                    <label for="unidade_1_3" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1_3" id="unidade_1_3_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_1_3" id="unidade_1_3_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_1_3" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_3" id="mais_med_1_3_O" value="O" disabled>Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_3" id="mais_med_1_3_M" value="M" disabled>Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_1_3" onclick="mostrar_dias(this.id);" id="mais_med_1_3_N" value="N" checked>Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <hr id="hr_2" hidden>
                                        <div class="row" hidden id="div_2">
                                            <div>
                                                <input name="codigo_item_2" type="hidden" class="form-control m-bot15" id="codigo_item_2">
                                                <input name="lixeira_item_2" value="" type="hidden" class="form-control m-bot15" id="lixeira_item_2">
                                            </div>
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-lg-12 col-md-12 pull-right" style="padding-right: 0;">
                                                    <a id="enviar_lixeira_2" class='btn pull-right' style="color: red;" onclick="enviar_item_lixeira(this.id);"><i class='icon_trash_alt' title='Enviar para lixeira'></i></a>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_2" id="lbl_2" class="control-label">Dia X2</label>
                                                    <input name="nome_prod_2" type="text" class="form-control m-bot15" id="nome_prod_2" placeholder="Produto ou orientação">
                                                    <input name="descricao_2" type="hidden" class="form-control m-bot15" id="descricao_2">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1">
                                                    <label for="quantidade_2" class="control-label">Qtd</label>
                                                    <input name="quantidade_2" type="number" class="form-control m-bot15" id="quantidade_2" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="unidade_2" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2" id="unidade_2_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2" id="unidade_2_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_2" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2" onclick="mais_med(this.name, this.value);" id="mais_med_2_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2" onclick="mais_med(this.name, this.value);" id="mais_med_2_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_2_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                    <label for="numero_dias_2" class="control-label">Dias p/ próxima etapa à partir do 1° dia</label>
                                                    <input name="numero_dias_2" type="number" class="form-control m-bot15" id="numero_dias_2" onkeyup="mostrar_linhas(this.id, this.value);">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_2_linha_1">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_2_1" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_2_1" type="text" class="form-control m-bot15" id="nome_prod_2_1" placeholder="Produto ou orientação">
                                                </div>
                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_2_1">
                                                    <label for="quantidade_2_1" class="control-label">Qtd</label>
                                                    <input name="quantidade_2_1" type="number" class="form-control m-bot15" id="quantidade_2_1" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>
                                                <div class="form-group col-lg-2 col-md-2" id="div_und_2_1">
                                                    <label for="unidade_2_1" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2_1" id="unidade_2_1_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2_1" id="unidade_2_1_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_2_1" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_1" onclick="mais_med(this.name, this.value);" id="mais_med_2_1_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_1" onclick="mais_med(this.name, this.value);" id="mais_med_2_1_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_1" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_2_1_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_2_linha_2">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_2_2" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_2_2" type="text" class="form-control m-bot15" id="nome_prod_2_2" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_2_2">
                                                    <label for="quantidade_2_2" class="control-label">Qtd</label>
                                                    <input name="quantidade_2_2" type="number" class="form-control m-bot15" id="quantidade_2_2" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_2_2">
                                                    <label for="unidade_2_2" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2_2" id="unidade_2_2_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2_2" id="unidade_2_2_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_2_2" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_2" onclick="mais_med(this.name, this.value);" id="mais_med_2_2_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_2" onclick="mais_med(this.name, this.value);" id="mais_med_2_2_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_2" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_2_2_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_2_linha_3">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_2_3" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_2_3" type="text" class="form-control m-bot15" id="nome_prod_2_3" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_2_3">
                                                    <label for="quantidade_2_3" class="control-label">Qtd</label>
                                                    <input name="quantidade_2_3" type="number" class="form-control m-bot15" id="quantidade_2_3" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_2_3">
                                                    <label for="unidade_2_3" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2_3" id="unidade_2_3_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_2_3" id="unidade_2_3_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_2_3" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_3" id="mais_med_2_3_O" value="S" disabled>Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_3" id="mais_med_2_3_M" value="N" disabled>Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_2_3" onclick="mostrar_dias(this.id);" id="mais_med_2_3_N" value="N" checked>Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <hr id="hr_3" hidden>
                                        <div class="row" hidden id="div_3">
                                            <div>
                                                <input name="codigo_item_3" type="hidden" class="form-control m-bot15" id="codigo_item_3">
                                                <input name="lixeira_item_3" value="" type="hidden" class="form-control m-bot15" id="lixeira_item_3" placeholder="0,000" onblur="troca_virgula(this.id);">
                                            </div>
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-lg-12 col-md-12 pull-right" style="padding-right: 0;">
                                                    <a id="enviar_lixeira_3" class='btn pull-right' style="color: red;" onclick="enviar_item_lixeira(this.id);"><i class='icon_trash_alt' title='Enviar para lixeira'></i></a>
                                                </div>
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_3" id="lbl_3" class="control-label">Dia X3</label>
                                                    <input name="nome_prod_3" type="text" class="form-control m-bot15" id="nome_prod_3" placeholder="Produto ou orientação">
                                                    <input name="descricao_3" type="hidden" class="form-control m-bot15" id="descricao_3">
                                                </div>
                                                <div class="form-group col-lg-1 col-md-1">
                                                    <label for="quantidade_3" class="control-label">Qtd</label>
                                                    <input name="quantidade_3" type="number" class="form-control m-bot15" id="quantidade_3" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="unidade_3" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3" id="unidade_3_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3" id="unidade_3_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_3" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3" onclick="mais_med(this.name, this.value);" id="mais_med_3_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3" onclick="mais_med(this.name, this.value);" id="mais_med_3_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_3_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                    <label for="numero_dias_3" class="control-label">Dias p/ próxima etapa à partir do 1° dia</label>
                                                    <input name="numero_dias_3" type="number" class="form-control m-bot15" id="numero_dias_3" onkeyup="mostrar_linhas(this.id, this.value);">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_3_linha_1">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_3_1" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_3_1" type="text" class="form-control m-bot15" id="nome_prod_3_1" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_3_1">
                                                    <label for="quantidade_3_1" class="control-label">Qtd</label>
                                                    <input name="quantidade_3_1" type="number" class="form-control m-bot15" id="quantidade_3_1" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_3_1">
                                                    <label for="unidade_3_1" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3_1" id="unidade_3_1_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3_1" id="unidade_3_1_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_3_1" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_1" onclick="mais_med(this.name, this.value);" id="mais_med_3_1_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_1" onclick="mais_med(this.name, this.value);" id="mais_med_3_1_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_1" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_3_1_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_3_linha_2">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_3_2" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_3_2" type="text" class="form-control m-bot15" id="nome_prod_3_2" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_3_2">
                                                    <label for="quantidade_3_2" class="control-label">Qtd</label>
                                                    <input name="quantidade_3_2" type="number" class="form-control m-bot15" id="quantidade_3_2" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_3_2">
                                                    <label for="unidade_3_2" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3_2" id="unidade_3_2_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3_2" id="unidade_3_2_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_3_2" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_2" onclick="mais_med(this.name, this.value);" id="mais_med_3_2_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_2" onclick="mais_med(this.name, this.value);" id="mais_med_3_2_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_2" onclick="mais_med(this.name, this.value);mostrar_dias(this.id);" id="mais_med_3_2_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_3_linha_3">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_3_3" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_3_3" type="text" class="form-control m-bot15" id="nome_prod_3_3" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_3_3">
                                                    <label for="quantidade_3_3" class="control-label">Qtd</label>
                                                    <input name="quantidade_3_3" type="number" class="form-control m-bot15" id="quantidade_3_3" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_3_3">
                                                    <label for="unidade_3_3" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3_3" id="unidade_3_3_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_3_3" id="unidade_3_3_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_3_3" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_3" id="mais_med_3_3_O" value="S" disabled>Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_3" id="mais_med_3_3_M" value="N" disabled>Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_3_3" onclick="mostrar_dias(this.id);" id="mais_med_3_3_N" value="N" checked>Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <hr id="hr_4" hidden>
                                        <div class="row" hidden id="div_4">
                                            <div>
                                                <input name="codigo_item_4" type="hidden" class="form-control m-bot15" id="codigo_item_4">
                                                <input name="lixeira_item_4" value="" type="hidden" class="form-control m-bot15" id="lixeira_item_4">
                                            </div>
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="col-lg-12 col-md-12 pull-right" style="padding-right: 0;">
                                                    <a id="enviar_lixeira_4" class='btn pull-right' style="color: red;" onclick="enviar_item_lixeira(this.id);"><i class='icon_trash_alt' title='Enviar para lixeira'></i></a>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_4" id="lbl_4" class="control-label">Dia X4</label>
                                                    <input name="nome_prod_4" type="text" class="form-control m-bot15" id="nome_prod_4" placeholder="Produto ou orientação">
                                                    <input name="descricao_4" type="hidden" class="form-control m-bot15" id="descricao_4">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1">
                                                    <label for="quantidade_4" class="control-label">Qtd</label>
                                                    <input name="quantidade_4" type="number" class="form-control m-bot15" id="quantidade_4" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="unidade_4" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4" id="unidade_4_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4" id="unidade_4_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_4" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4" onclick="mais_med(this.name, this.value);" id="mais_med_4_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4" onclick="mais_med(this.name, this.value);" id="mais_med_4_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4" onclick="mais_med(this.name, this.value);" id="mais_med_4_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <!-- <div class="form-group col-lg-3 col-md-3">
                                                    <label for="numero_dias_4" class="control-label">N° de dias p/ próxima etapa à partir do 1° dia</label>
                                                    <input name="numero_dias_4" type="number" class="form-control m-bot15" id="numero_dias_4" onchange="mostrar_linhas(this.id, this.value);">
                                                </div> -->
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_4_linha_1">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_4_1" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_4_1" type="text" class="form-control m-bot15" id="nome_prod_4_1" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_4_1">
                                                    <label for="quantidade_4_1" class="control-label">Qtd</label>
                                                    <input name="quantidade_4_1" type="number" class="form-control m-bot15" id="quantidade_4_1" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_4_1">
                                                    <label for="unidade_4_1" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4_1" id="unidade_4_1_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4_1" id="unidade_4_1_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_4_1" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_1" onclick="mais_med(this.name, this.value);" id="mais_med_4_1_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_1" onclick="mais_med(this.name, this.value);" id="mais_med_4_1_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_1" onclick="mais_med(this.name, this.value);" id="mais_med_4_1_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_4_linha_2">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_4_2" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_4_2" type="text" class="form-control m-bot15" id="nome_prod_4_2" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_4_2">
                                                    <label for="quantidade_4_2" class="control-label">Qtd</label>
                                                    <input name="quantidade_4_2" type="number" class="form-control m-bot15" id="quantidade_4_2" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_4_2">
                                                    <label for="unidade_4_2" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4_2" id="unidade_4_2_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4_2" id="unidade_4_2_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_4_2" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_2" onclick="mais_med(this.name, this.value);" id="mais_med_4_2_O" value="O">Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_2" onclick="mais_med(this.name, this.value);" id="mais_med_4_2_M" value="M">Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_2" onclick="mais_med(this.name, this.value);" id="mais_med_4_2_N" value="N">Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="div_4_linha_3">
                                            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group col-lg-2 col-md-2">
                                                    <label for="nome_prod_4_3" class="control-label">&nbsp</label>
                                                    <input name="nome_prod_4_3" type="text" class="form-control m-bot15" id="nome_prod_4_3" placeholder="Produto ou orientação">
                                                </div>

                                                <div class="form-group col-lg-1 col-md-1" id="div_qtd_4_3">
                                                    <label for="quantidade_4_3" class="control-label">Qtd</label>
                                                    <input name="quantidade_4_3" type="number" class="form-control m-bot15" id="quantidade_4_3" placeholder="0,00" onblur="troca_virgula(this.id);" onkeypress="digita_valor()">
                                                </div>

                                                <div class="form-group col-lg-2 col-md-2" id="div_und_4_3">
                                                    <label for="unidade_4_3" class="control-label">&nbsp</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4_3" id="unidade_4_3_und" value="Und">Und
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="unidade_4_3" id="unidade_4_3_ml" value="mL">mL
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-4 col-md-4">
                                                    <label for="mais_med_4_3" class="control-label">Acrescentar:</label>
                                                    <div class="clearfix"></div>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_3" id="mais_med_4_3_O" value="S" disabled>Orientação
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_3" id="mais_med_4_3_M" value="N" disabled>Medicamento
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="mais_med_4_3" id="mais_med_4_3_N" value="N" checked>Nada
                                                    </label>
                                                </div>

                                                <div class="form-group col-lg-3 col-md-3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" 
                                                id="gravar"
                                                onClick="gravar_protocolo()">Confirmar Inclusão</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_ler_dados" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-xl modal-direita" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Protocolos IATF - Consulta</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_protocoloIATF.php" enctype="multipart/form-data" id="form_gravar_protocolo">
                              
                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">
                                        <!-- <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-info pull-right voltar_inclusao" data-dismiss="modal" onclick="location.reload();">Voltar</button>
                                                <button type="button" class="btn btn-info pull-right voltar" data-dismiss="modal">Voltar</button>
                                            </div>
                                        </div> -->

                                        <div class="row">
                                            <div class="form-group col-md-6 col-xs-12">
                                                <span>Protocolo:</span>
                                                <span class="control-label" id="lbl_nome_protocolo"></span>
                                            </div>
                                            <div class="form-group col-md-3 col-xs-12">
                                                <span>Quantidade:</span>
                                                <span class="control-label" id="lbl_quant_protocolo"></span>
                                            </div>
                                            <div class="form-group col-md-3 col-xs-12">
                                                <span>Dias Diagnóstico:</span>
                                                <span class="control-label" id="lbl_dias_diagnostico"></span>
                                            </div>
                                        </div>

                                        <div class="row">&nbsp</div>

                                        <!-- <hr> --><!-- produto 0 -->
                                        <div class="row" id="div_0">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>Dia 0</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span class="control-label" id="lbl_nome_prod_0"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_0"></span>
                                                <span id="lbl_unidade_0"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_0_1">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_0_1"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_0_1"></span>
                                                <span id="lbl_unidade_0_1"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_0_2">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_0_2"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_0_2"></span>
                                                <span id="lbl_unidade_0_2"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_0_3">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_0_3"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_0_3"></span>
                                                <span id="lbl_unidade_0_3"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="row" id="r1" hidden>&nbsp</div><!-- produto 1 -->

                                        <div class="row" hidden id="ler_1">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span id="lbl_descricao_1"></span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_1"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_1"></span>
                                                <span id="lbl_unidade_1"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_1_1">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_1_1"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_1_1"></span>
                                                <span id="lbl_unidade_1_1"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_1_2">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_1_2"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_1_2"></span>
                                                <span id="lbl_unidade_1_2"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_1_3">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_1_3"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_1_3"></span>
                                                <span id="lbl_unidade_1_3"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="row" id="r2" hidden>&nbsp</div><!-- produto 2 -->

                                        <div class="row" hidden id="ler_2">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span id="lbl_descricao_2"></span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_2"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_2"></span>
                                                <span id="lbl_unidade_2"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_2_1">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_2_1"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_2_1"></span>
                                                <span id="lbl_unidade_2_1"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_2_2">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_2_2"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_2_2"></span>
                                                <span id="lbl_unidade_2_2"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_2_3">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_2_3"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_2_3"></span>
                                                <span id="lbl_unidade_2_3"></span>
                                            </div>
                                        </div>

                                        <div class="row" id="r3" hidden>&nbsp</div><!-- produto 3 -->

                                        <div class="row" hidden id="ler_3">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span id="lbl_descricao_3"></span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_3"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_3"></span>
                                                <span id="lbl_unidade_3"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_3_1">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_3_1"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_3_1"></span>
                                                <span id="lbl_unidade_3_1"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_3_2">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_3_2"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_3_2"></span>
                                                <span id="lbl_unidade_3_2"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_3_3">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_3_3"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_3_3"></span>
                                                <span id="lbl_unidade_3_3"></span>
                                            </div>
                                        </div>

                                        <div class="row" id="r4" hidden>&nbsp</div><!-- produto 4 -->

                                        <div class="row" hidden id="ler_4">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span id="lbl_descricao_4"></span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_4"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_4"></span>
                                                <span id="lbl_unidade_4"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_4_1">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_4_1"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_4_1"></span>
                                                <span id="lbl_unidade_4_1"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_4_2">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_4_2"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_4_2"></span>
                                                <span id="lbl_unidade_4_2"></span>
                                            </div>
                                        </div>

                                        <div class="row" hidden id="ler_4_3">
                                            <div class="form-group col-lg-2 col-md-3">
                                                <span>&nbsp</span>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-3">
                                                <span id="lbl_nome_prod_4_3"></span>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-2">
                                                <span id="lbl_quantidade_4_3"></span>
                                                <span id="lbl_unidade_4_3"></span>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                                <button type="button" onclick="botao_modal_ler();" class="btn btn-success botao_modal_ler">Editar</button>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-xs-6">
                                                <button type="button" data-dismiss="modal" class="btn btn-primary pull-right">Voltar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            </form>
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
                            <h4 class="modal-title">Protocolos IATF </h4>
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
                            <h4 class="modal-title">Protocolos IATF </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_novo();">Fechar</button>
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
                            <h4 class="modal-title">Protocolos IATF - Erro</h4>
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

 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Agrolandes 2021</p></font>
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

<script src="js/protocolosIATF.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  -->

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>

</body>
</html>

