<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("M");
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
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
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
            border: none;
            padding: 0; /* Remove padding interno que possa atrapalhar */
            color: #007aff;
            background: transparent;
            font-size: 13px;
            font-weight: 500; 
            width: 30%;       
        }
    </style>

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_gestao_adm'])) {
        $array_gestao_adm = explode("!",$_SESSION['menu_gestao_adm']);

        if ($array_gestao_adm[5] == 0){
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

    $plano_conta_receita = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_nivel != 1 and tbl_plano_contas_debito_credito='C'"); 

    $plano_conta_despesa = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_nivel != 1 and tbl_plano_contas_debito_credito='D'"); 

    $plano_receita_filtro = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_nivel != 1  and tbl_plano_contas_debito_credito='C'"); 

    $plano_despesa_filtro = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_nivel != 1  and tbl_plano_contas_debito_credito='D'"); 

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local_id = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

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

    $local = $_SESSION['codigo_local_previsao']; 
    $contas = $_SESSION['codigo_conta_previsao']; 
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Previsão de Contas</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Cadastro Previsão de Contas</h3>
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
                        <form method="GET" action="form_contas.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                        <input id="lista_previsao_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_previsao'] . "'"; ?>>

                                    <input id="limpar_filtro_contas" type="hidden" <?php echo "value='" . $_SESSION['limpa_conta_previsao'] . "'"; ?>>

                                    <input id="exibe_conta" type="hidden" <?php echo "value='".$contas."'"; ?>>
                                    <input id="exibe_local" type="hidden" <?php echo "value='".$local."'"; ?>>

                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Previsão de Contas</legend>

                                        <div class="row digitar_filtros">
                                            <div class="form-group col-md-2" >
                                                <label for="ano_filtro" class="control-label">Ano</label>

                                                <select class="form-control" id="ano_filtro" name="ano_filtro">
                                                <option value="2020"
                                                    <?php if ($ano == 2020) {echo"selected";}?>>2020
                                                </option>
                                                <option value="2021"
                                                    <?php if ($ano == 2021) {echo"selected";}?>>2021
                                                </option>
                                                <option value="2022"
                                                    <?php if ($ano == 2022) {echo"selected";}?>>2022
                                                </option>
                                                <option value="2023"
                                                    <?php if ($ano == 2023) {echo"selected";}?>>2023
                                                </option>
                                                <option value="2024"
                                                    <?php if ($ano == 2024) {echo"selected";}?>>2024
                                                </option>
                                                <option value="2025"
                                                    <?php if ($ano == 2025) {echo"selected";}?>>2025
                                                </option>
                                                <option value="2026"
                                                    <?php if ($ano == 2026) {echo"selected";}?>>2026
                                                </option>
                                                <option value="2027"
                                                    <?php if ($ano == 2027) {echo"selected";}?>>2027
                                                </option>
                                                <option value="2028)"
                                                    <?php if ($ano == 2028) {echo"selected";}?>>2028
                                                </option>
                                                <option value="2029"
                                                    <?php if ($ano == 2029) {echo"selected";}?>>2029
                                                </option>
                                                <option value="2030"
                                                    <?php if ($ano == 2030) {echo"selected";}?>>2030
                                                </option>
                                                <option value="2031"
                                                    <?php if ($ano == 2031) {echo"selected";}?>>2031
                                                </option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row digitar_filtros">    
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local_filtro" class="control-label">Fazenda</label>
                                                <select class="form-control selectpicker" id="codigo_local_filtro" multiple name="codigo_local_filtro">
                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($local_filtro)) { 
                                                    
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
                                                <label for="codigo_conta_contabil" class="control-label">Conta Contábil</label>

                                                <label for="codigo_conta" class="control-label" style="">Conta Contábil</label>
                                                    
                                                <input type="text" name="contas_selecionadas" id="contas_selecionadas" class="form-control" value="Todas ou (Clique p/ selecionar contas)">
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
                                                <!--<label class="control-label">&nbsp;</label>-->
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="exibe_mais_filtros()">Voltar</button>
                                            </div>
                                        </div> 
                                    </fieldset> 
                                    
                                    <div id="lista_contas"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- page end-->

            <div class="modal fade" id="modal_conta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="exibe_contas_selecionadas()">&times;</button>
                            <h4 class="modal-title">Selecione a conta</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-3 pull-right">
                                        <a href="#" onclick="limpa_contas_selecionadas()">Limpar Seleção
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12" id="modal_conta_info" style="height: 50vh; overflow-y: auto;">
                                  </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button data-dismiss="modal" class="btn btn-primary pull-right" type="button" onclick="exibe_contas_selecionadas()">Fechar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Previsão de Despesas - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_previsao_contas.php" enctype="multipart/form-data" id="form_gravar_conta">
                                <input name="codigo_conta" type="hidden" id="codigo_conta">
                              
                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="conta_contabil_id" class="control-label"><span class="required">*</span> Conta Contábil</label>

                                                <select class="form-control" id="conta_contabil_id" name="conta_contabil_id">

                                                <option value="0000000">...</option>

                                                <optgroup label="RECEITAS">  

                                                <?php while($reg_conta = mysqli_fetch_object($plano_conta_receita)) { ?>

                                                <option value="<?php 
                                                    echo $reg_conta->tbl_plano_contas_codigo_id ?>">
                                                                    
                                                    <?php 
                                                    echo $reg_conta->tbl_plano_contas_descricao;
                                                    ?>
                                                </option>
                                                    <?php } ?>
                                                </optgroup>

                                                <optgroup label="DESPESAS">

                                                <?php while($reg_conta = mysqli_fetch_object($plano_conta_despesa)) { ?>

                                                <option value="<?php 
                                                    echo $reg_conta->tbl_plano_contas_codigo_id ?>">
                                                                    
                                                    <?php 
                                                    echo $reg_conta->tbl_plano_contas_descricao;
                                                    ?>
                                                </option>
                                                    <?php } ?>
                                                </optgroup> 
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="codigo_local" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" id="codigo_local"name="codigo_local">
                                                <option value="000000000">...</option>

                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($local_id)) { 
                                                    
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
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3" >
                                                <label for="ano_conta" class="control-label">Ano</label>
                                                <select class="form-control" id="ano_conta" name="ano_conta">
                                                <option value="2020"
                                                    <?php if ($ano == 2020) {echo"selected";}?>>2020
                                                </option>
                                                <option value="2021"
                                                    <?php if ($ano == 2021) {echo"selected";}?>>2021
                                                </option>
                                                <option value="2022"
                                                    <?php if ($ano == 2022) {echo"selected";}?>>2022
                                                </option>
                                                <option value="2023"
                                                    <?php if ($ano == 2023) {echo"selected";}?>>2023
                                                </option>
                                                <option value="2024"
                                                    <?php if ($ano == 2024) {echo"selected";}?>>2024
                                                </option>
                                                <option value="2025"
                                                    <?php if ($ano == 2025) {echo"selected";}?>>2025
                                                </option>
                                                <option value="2026"
                                                    <?php if ($ano == 2026) {echo"selected";}?>>2026
                                                </option>
                                                <option value="2027"
                                                    <?php if ($ano == 2027) {echo"selected";}?>>2027
                                                </option>
                                                <option value="2028)"
                                                    <?php if ($ano == 2028) {echo"selected";}?>>2028
                                                </option>
                                                <option value="2029"
                                                    <?php if ($ano == 2029) {echo"selected";}?>>2029
                                                </option>
                                                <option value="2030"
                                                    <?php if ($ano == 2030) {echo"selected";}?>>2030
                                                </option>
                                                <option value="2031"
                                                    <?php if ($ano == 2031) {echo"selected";}?>>2031
                                                </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">        
                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_jan" class="control-label">Janeiro</label>
                                                <input name="valor_previsto_jan" type="text" class="form-control" id="valor_previsto_jan"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_jan()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_fev" class="control-label">Fevereiro</label>
                                                <input name="valor_previsto_fev" type="text" class="form-control" id="valor_previsto_fev"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_fev()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_mar" class="control-label">Março</label>
                                                <input name="valor_previsto_mar" type="text" class="form-control" id="valor_previsto_mar"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_mar()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_abr" class="control-label">Abril</label>
                                                <input name="valor_previsto_abr" type="text" class="form-control" id="valor_previsto_abr"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_abr()" >
                                            </div>

                                        </div>

                                        <div class="row">        
                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_mai" class="control-label">Maio</label>
                                                <input name="valor_previsto_mai" type="text" class="form-control" id="valor_previsto_mai"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_mai()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_jun" class="control-label">Junho</label>
                                                <input name="valor_previsto_jun" type="text" class="form-control" id="valor_previsto_jun"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_jun()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_jul" class="control-label">Julho</label>
                                                <input name="valor_previsto_jul" type="text" class="form-control" id="valor_previsto_jul"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_jul()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_ago" class="control-label">Agosto</label>
                                                <input name="valor_previsto_ago" type="text" class="form-control" id="valor_previsto_ago"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_ago()" >
                                            </div>
                                        </div>


                                        <div class="row">        
                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_set" class="control-label">Setembro</label>
                                                <input name="valor_previsto_set" type="text" class="form-control" id="valor_previsto_set"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_set()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_out" class="control-label">Outubro</label>
                                                <input name="valor_previsto_out" type="text" class="form-control" id="valor_previsto_out"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_out()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_nov" class="control-label">Novembro</label>
                                                <input name="valor_previsto_nov" type="text" class="form-control" id="valor_previsto_nov"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_nov()" >
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="valor_previsto_dez" class="control-label">Dezembro</label>
                                                <input name="valor_previsto_dez" type="text" class="form-control" id="valor_previsto_dez"
                                                placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_previsto_dez()" >
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12" style="font-size: 10px; " id="informacao">
                                                <label>Incluído por: </label>
                                                <span id="incluido_por" style="color: green"></span>
                                                    &nbsp; 
                                                <span id="incluido_em" style="color: green; margin-right: 10px"></span>

                                                <label id="registro_alterado"> Alterado por: </label>
                                                <span id="alterado_por" style="color: red"></span>
                                                &nbsp;
                                                <span id="alterado_em" style="color: red; margin-right: 10px"></span>
                                            </div>
                                        </div>

                                        <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                                        <div class="row">  
                                              <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_conta()">Confirmar Inclusão</button>

                                                <button type="button" class="btn btn-info pull-right voltar_inclusao" onClick="voltar_inclusao()">Voltar</button>
                                                
                                                <button type="button" class="btn btn-info pull-right voltar" data-dismiss="modal">Voltar</button>
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
                            <h4 class="modal-title">Precisão de Contas </h4>
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
                            <h4 class="modal-title">Precisão de Contas </h4>
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
                            <h4 class="modal-title">Previsão de Contas - Erro</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
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

<?php 
  $javascript_file_name = 'tabela_previsao_contas.js';
  require 'rodape.php';
?>
