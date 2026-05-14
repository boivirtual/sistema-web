<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");
    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $opcao_nascimento = "'N'";
    $opcao_absorcao = "'B'";
    $opcao_aborto = "'A'";
    $opcao_natimorto = "'M'";

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

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link href="css/select-1.13.14.css" rel="stylesheet" > 

  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <style type="text/css">
        .bootstrap-select {
          width: 240px !important;
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

    $controle_estoque = $_SESSION['controle_estoque'];

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
    $array_ocorrencia = $_SESSION['ocorrencia_nascimento'];
    $array_estacao =  $_SESSION['estacao_nascimento'];

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
        $data_inicial = '';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_nascimento'];  
    }

    if ($_SESSION['data_final_nascimento']==''){
        $data_final = '';
    }
    else {
        $data_final =  $_SESSION['data_final_nascimento'];   
    }

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

    $pai = mysqli_query($conector, "select * from tbl_animais 
        inner join tabela_racas
                on tab_codigo_raca=tbl_animal_codigo_raca
                where tbl_animal_lixeira=0 and tbl_animal_sexo='M'
    	order by tbl_animal_codigo_numerico"); 

    $semem = mysqli_query($conector, "select * from tbl_semem 
        inner join tabela_racas
                on tab_codigo_raca=tbl_semem_codigo_raca
        where tbl_semem_lixeira=0"); 

    $mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_lixeira=0  and tbl_animal_sexo='F'
    	order by tbl_animal_codigo_numerico"); 

    $raca = mysqli_query($conector, "select * from tabela_racas where tab_registro_lixeira_raca=0");
    
    $pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_registro_lixeira_pelagem=0"); 
    
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php";
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Reprodução <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Nascimento</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-star-o"></i> Nascimento</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_produtos.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Nascimento/Natimorto/Aborto/Absorção</legend>

                                        <div class="row digitar_filtros">
                                            <input id="lista_nascimento_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_nascimento'] . "'"; ?>>

                                            <input id="exibe_estacao" type="hidden" <?php echo "value='".$array_estacao."'"; ?>>

                                            <input id="exibe_local" type="hidden" <?php echo "value='".$array_local."'"; ?>>


                                            <div class="form-group col-md-3">
                                                <label for="codigo_local" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control selectpicker" title="..." id="codigo_local" multiple name="codigo_local">

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

                                            <?php
                                                if ($controle_estoque=='I') :
                                            ?>

                                            <div class="form-group col-md-3 geral">
                                                <label for="codigo_estacao_monta" class="control-label">Estação de Monta&nbsp;&nbsp; OU</label>
                                                <select class="form-control selectpicker" multiple id="codigo_estacao_monta" name="codigo_estacao_monta" title="..." data-live-search="true">
                                                </select>
                                            </div>  

                                            <?php
                                                endif;
                                            ?>

                                            <div class="form-group col-md-3">
                                                <label for="data_inicial" class="control-label">Período - Inicial</label>

                                                <input type="date" name="data_inicial" id="data_inicial" class="form-control" <?php echo "value='".$data_inicial."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="data_final" class="control-label">Final</label>
                                                <input name="data_final" type="date" class="form-control" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                            </div>

                                        </div>
                                        <div class="row digitar_filtros">    
                                            <div class="form-group col-md-3">
                                                <label for="tipo_ocorrencia" class="control-label"> Situação</label>
                                                <select class="form-control selectpicker" title="Todas" multiple id="tipo_ocorrencia" name="tipo_ocorrencia">

                                                <option value="<?php 
                                                    echo $opcao_nascimento; ?>"
                                                    <?php 
                                                        if ($array_ocorrencia!="") {
                                                            foreach ($array_ocorrencia as $value) {
                                                                if ($value==$opcao_nascimento) { 
                                                                    echo "selected";
                                                                }
                                                            }                         
                                                        }
                                                    ?>>Nascimento
                                                </option>

                                                <option value="<?php 
                                                    echo $opcao_absorcao; ?>"
                                                    <?php 
                                                        if ($array_ocorrencia!="") {
                                                            foreach ($array_ocorrencia as $value) {
                                                                if ($value==$opcao_absorcao) { 
                                                                    echo "selected";
                                                                }
                                                            }                         
                                                        }
                                                    ?>>Absorção
                                                </option>

                                                <option value="<?php 
                                                    echo $opcao_aborto; ?>"
                                                    <?php 
                                                        if ($array_ocorrencia!="") {
                                                            foreach ($array_ocorrencia as $value) {
                                                                if ($value==$opcao_aborto) { 
                                                                    echo "selected";
                                                                }
                                                            }                         
                                                        }
                                                    ?>>Aborto
                                                </option>

                                                <option value="<?php 
                                                    echo $opcao_natimorto; ?>"
                                                    <?php 
                                                        if ($array_ocorrencia!="") {
                                                            foreach ($array_ocorrencia as $value) {
                                                                if ($value==$opcao_natimorto) {
                                                                    echo "selected";
                                                                }
                                                            }                         
                                                        }
                                                    ?>>Natimorto
                                                </option>
                                                </select>
                                            </div>


                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right consultar" onclick="consultar()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros_consulta" hidden>
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
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Esconder Filtros" onclick="exibe_menos_filtros()"> <i class="fas fa-filter"></i> -
                                                        </a>
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="form-group col-md-1 voltar">
                                                <!--<label class="control-label">&nbsp;</label>-->
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="exibe_mais_filtros()">Voltar</button>
                                            </div>

                                            <!--<div class="col-md-2">
                                                <a href="#" style="font-size: 0.9em; font-weight: 600; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle="tooltip" data-placement="top" title="Histórico de Animais" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                            </div>-->
                                        </div> 
                                    </fieldset>
                                    <div id="lista_nascimentos"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- page end-->

            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" aria-labelledby="modal_incluirCenterTitle" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal_incluirLabel">Nascimento - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_nascimento.php" enctype="multipart/form-data" id="form_gravar_animal">

                                <input type="hidden" name="controle_estoque" id="controle_estoque" <?php echo "value='".$controle_estoque."'";?>>

                                <input type="hidden" name="grupo_usuario" id="grupo_usuario" <?php echo "value='".$grupo_usuario."'";?>>
                              
                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                                <input type="hidden" name="num_mov_nascimento"  id="num_mov_nascimento">

                                <input name="dias_nascimento" type="hidden"  id="dias_nascimento">

                                <input name="data_inseminacao" type="hidden"  id="data_inseminacao">

                                <input type="hidden" id="data_hoje" <?php echo "value='".$data_sistema."'";?>>
                               
                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#nascimento">Dados</a>
                                    </li>

                                    <li class="parametros">
                                        <a data-toggle="tab" href="#parametros">Parametros de Nascimento</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="parametros" class="tab-pane">
                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="location.reload();">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row">        
                                            <div class="form-group col-md-4">
                                                <label for="fazendas" class="">Fazendas</label>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Código Alfa</label>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label class="">Sequência Numérica
                                                <span style="font-size: 10px; color: blue;">(Digitar o primeiro número sequencial)</span>
                                                </label>
                                            </div>
                                        </div>

                                        <?php
                                            $ssql = "SELECT * FROM tbl_pessoa 
                                                 WHERE tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"; 
                                            $rs = mysqli_query($conector,$ssql); 
                                            while ($fila = mysqli_fetch_object($rs)){
                                                $codigo_id = $fila->tbl_pessoa_id;
                                                $nome_fazenda = $fila->tbl_pessoa_nome;

                                                foreach ($array_locais_usuario as $value) {
                                                    $value = ltrim($value);
                                                    $value = rtrim($value);

                                                    if ($value==$codigo_id) {
                                                        echo ' 
                                                            <div class="row">        
                                                            <div class="form-group col-md-4">
                                                            <input name="codigo_fazenda" type="hidden" value="'.$codigo_id.'">
                                                            <span>'.$nome_fazenda.'</span>
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <input name="cod_alfa" type="text" class="form-control cod_alfa" maxlength="4" 
                                                                    onkeyup="maiuscula(this)" onkeypress="return desabilita_enter (this, event)"
                                                                    style="background-color: #f0f3f5; width: 70%">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                            <input name="cod_numerico" type="text" class="form-control cod_numerico" onkeypress = "return numeros(this, event)">
                                                            </div>
                                                            </div>';
                                                    }
                                                }
                                            }
                                        ?> 
                            
                                        <input name="array_codigo_fazenda" type="hidden" id="array_codigo_fazenda" >

                                        <input name="array_codigo_alfa" type="hidden" id="array_codigo_alfa" >

                                        <input name="array_codigo_numerico" type="hidden" id="array_codigo_numerico" >

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary" onClick="gravar_parametros()">Confirmar</button>
                                            </div>
                                        </div>
                                    </div> <!-- Fim Parametros-->

                                    <div id="nascimento" class="tab-pane active">
                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="location.reload();">Voltar</button>
                                            </div>
                                        </div>

                                        <!-- Campos comuns-->
                                        <div class="row ocorrencias">
                                            <div class="form-group col-md-6">
                                                <label class="control-label label_opcao"><span class="required">*</span> Selecione uma opção</label>

                                                <div class="clearfix"></div>

                                                <label class="radio-inline">
                                                    <input type="radio" name="opcao_nascimento" id="opcao_nascimento" value="N" class="opcao_nascimento">Nascimento
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="opcao_nascimento" id="opcao_aborto" value="A" class="opcao_nascimento">Aborto
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="opcao_nascimento" id="opcao_absorcao" value="B" class="opcao_nascimento">Absorção
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="opcao_nascimento" id="opcao_morte" value="M" class="opcao_nascimento">Natimorto
                                                </label>
                                            </div>
                                        </div>

                                        <hr align="center">

                                        <div class="row fazenda_pasto" hidden>
                                            <div class="form-group col-md-6">
                                                <label for="local_id" class="control-label"><span class="required">*</span> Fazenda </label>

                                                <select class="form-control" name="local_id" id="local_id" onchange="consultar_pastos();">
                                                </select>

                                                <input type="text" name="desc_local" id="desc_local" class="form-control" readonly="">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label class="control-label label_pasto">Pasto</label>

                                                <select class="form-control" name="pasto_id" id="pasto_id">
                                                    <option value="">...</option>
                                                </select>

                                                <input type="text" name="desc_pasto" id="desc_pasto" class="form-control" readonly="">
                                            </div>
                                        </div>

                                        <div class="campos_data_mae_pai" hidden>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="control-label label_data">Data Nascimento</label>

                                                    <input name="nascimento_animal" type="date" class="form-control" id="nascimento_animal" <?php echo "value='".$data_sistema."'";?>>
                                                </div>

                                                <div class="form-group col-md-4 codigo_mae_animal">
                                                    <label for="codigo_mae_consulta" class="control-label label_mae"><span class="required">*</span> Nº Mãe</label>

                                                    <input name="codigo_mae_consulta" type="text" class="form-control" id="codigo_mae_consulta" autocomplete="off"
                                                    
                                                    onchange="ler_animal_mae()" onkeypress="return desabilita_enter (this, event)">

                                                </div>

                                                <div class="form-group col-md-4 codigo_pai_animal">
                                                    <label class="control-label">Pai Nº
                                                    </label>

                                                    <select class="form-control" id="codigo_pai_animal" name="codigo_pai_animal">

                                                        <option value="000000000">...</option>

                                                        <optgroup label="SEMEM">

                                                            <?php while($reg_pai = mysqli_fetch_object($semem)) { ?>

                                                            <option value="<?php echo $reg_pai->tbl_semem_codigo_id ?>">
                                                                                                            
                                                                <?php 
                                                                echo $reg_pai->tbl_semem_nome . ' - ' . $reg_pai->tab_descricao_raca;
                                                                ?>
                                                            </option> 

                                                            <?php } ?>
                                                        </optgroup>

                                                        <optgroup label="ANIMAIS">

                                                            <?php while($reg_pai = mysqli_fetch_object($pai)) { ?>

                                                            <option value="<?php echo $reg_pai->tbl_animal_codigo_id ?>">
                                                                                                            
                                                                <?php 
                                                                echo $reg_pai->tbl_animal_codigo_alfa. ' ' . $reg_pai->tbl_animal_codigo_numerico  . ' - ' . $reg_pai->tab_descricao_raca;
                                                                ?>
                                                            </option>
                                                                
                                                            <?php } ?>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="nascimento_id" hidden>
                                            <div class="row">
                                                <div class="form-group col-md-2 alfa_animal">
                                                    <label for="alfa_animal" class="control-label">Código Alfa</label>
                                                    <input name="alfa_animal" type="text" class="form-control" id="alfa_animal" maxlength="4" placeholder="Letras" 
                                                    onkeyup="maiuscula(this)">

                                                    <input type="hidden" id="codigo_alfa_anterior" >

                                                </div>

                                                <div class="form-group col-md-2">
                                                    <input type="hidden" name="codigo_animal_id" id="codigo_animal_id">

                                                    <label for="codigo_numerico_animal" class="control-label"> Nº Animal</label>

                                                    <input name="codigo_numerico_animal" type="number" class="form-control" id="codigo_numerico_animal" maxlength="9" placeholder="Números">

                                                    <input type="hidden" id="codigo_numerico_anterior" >

                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="control-label">&nbsp;</label>

                                                    <h5>
                                                        <span class="tipo_estacao_monta">Estação de Monta:</span>
                                                        <span id="ultima_estacao" >
                                                        </span>
                                                        <a href="#" class="" style="color: yellow; font-size: 12px;" onclick="lista_femeas_servidas()">
                                                            <img src='img/exclamacao.png' class="fa fa-exclamation-triangle icon_nascimentos_previstos" data-toggle='tooltip' data-placement='right' title="Existem nascimentos dessa estação atrasados." width='25' height='28'/>

                                                        <!--<i class="fa fa-exclamation-triangle icon_nascimentos_previstos" data-toggle='tooltip' data-placement='right' title="Existem nascimentos dessa estação atrasados."></i>-->
                                                        </a> 
                                                    </h5>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="control-label">&nbsp;</label>
                                                    <h5 class="desc_novo_nascimento" style="font-weight: 700;color: red; font-size: 13px;">
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="campos_id_aborto_lote" hidden>
                                            <div class="row">
                                                <div class="form-group col-md-3 qtd_animal">
                                                    <label class="control-label"><span class="required">*</span> Qtd Animal</label>

                                                    <input name="qtd_animal" type="number" class="form-control" id="qtd_animal"
                                                    aria-describedby="arrobaHelpBlock">

                                                    <small id="arrobaHelpBlock" class="form-text text-muted" style="color: #808080">Nascimento e Sexo iguais</small>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="control-label"><span class="required">*</span> Sexo</label>

                                                    <div class="clearfix"></div>

                                                    <label class="radio-inline">
                                                      <input type="radio" name="sexo_animal" id="M" value="M" class="sexo_animal">Macho
                                                    </label>

                                                    <label class="radio-inline">
                                                      <input type="radio" name="sexo_animal" id="F" value="F" class="sexo_animal">Fêmea
                                                    </label>

                                                    <p id="mudar_sexo" style="color: red; font-size: 10px">Para mudar o sexo entre em contato com o suporte técnico: (31) 99772-1904 - falecomboivirtual@gmail.com</p>
                                                </div>

                                                <div class="form-group col-md-3 raca_id">
                                                    <?php
                                                    if ($controle_estoque=='I') :
                                                    ?>
                                                        <label for="raca_id" class="control-label"><span class="required">*</span> Raça</label>

                                                    <?php
                                                    else :
                                                    ?>
                                                        <label for="raca_id" class="control-label"> Raça</label>
                                                        
                                                    <?php
                                                        endif;
                                                    ?>
                                                    <select class="form-control" name="raca_id" id="raca_id">
                                                    <option value="">...</option>

                                                    <?php while($reg_raca = mysqli_fetch_object($raca)) { ?>

                                                    <option value="<?php 
                                                        echo $reg_raca->tab_codigo_raca ?>">
                                                                            
                                                        <?php 
                                                        echo $reg_raca->tab_descricao_raca;
                                                        ?>
                                                    </option>
                                                    <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3 pelagem_id">
                                                  <label class="control-label">Pelagem</label>
                                                  <select class="form-control" name="pelagem_id" id="pelagem_id">
                                                    <option value="">...</option>

                                                    <?php while($reg_pelagem = mysqli_fetch_object($pelagem)) { ?>

                                                    <option value="<?php 
                                                        echo $reg_pelagem->tab_codigo_pelagem ?>">
                                                                        
                                                        <?php 
                                                        echo $reg_pelagem->tab_descricao_pelagem;
                                                        ?>
                                                    </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3 peso_animal">
                                                    <?php
                                                    if ($controle_estoque=='I') {
                                                        echo '<label for="peso_animal" class="control-label"><span class="required">*</span> Peso</label>';
                                                    }
                                                    else {
                                                        echo '<label for="peso_animal" class="control-label"><span class="required">*</span> Peso Médio</label>';
                                                    }
                                                    ?>    
                                                    <input name="peso_animal" type="number" class="form-control" id="peso_animal">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row confirmar" hidden>  
                                            <div class="form-group col-md-6">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="confirmar_nascimento()">Confirmar</button>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <!--<button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="location.reload();">Voltar</button>-->

                                                <input name="codigo_mae_animal" type="hidden" id="codigo_mae_animal">

                                                <input name="cobertura_id" type="hidden"  id="cobertura_id">

                                                <input name="item_cobertura" type="hidden"  id="item_cobertura">

                                                <input name="estacao_monta_id" type="hidden" id="estacao_monta_id">

                                                <input name="tipo_cobertura" type="hidden" id="tipo_cobertura">

                                                <input name="data_prenhes" type="hidden" id="data_prenhes">
                                            </div>
                                        </div>
                                    </div><!-- Fim novo nascimmento-->
                                </div>    
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_estacao" tabindex="-1" role="dialog" aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">


                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p class="desc_modal" style="font-weight: bold;">Essa Fêmea não está em estação de monta.</p>

                                    <p class="mens_administrador" style="color: red;">Entre em contato com o Administrador do Sistema</p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_1"></span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_2"></span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_3"></span></p>
                                </div>
                            </div>

                            <div class="row estacao_monta">
                                <div class="form-group col-md-6">
                                    <label for="estacao_monta" class="control-label"><span class="required">*</span> Nova Estação de Monta</label>

                                    <select class="form-control" id="estacao_monta" name="estacao_monta">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success outra_estacao" type="button" onclick="confirmaEstacao()">Confirma Estação de Monta
                            </button>

                            <button data-dismiss="modal" class="btn btn-primary substituir" type="button" onclick="substituir_por_monta_natural();">Substituir por Monta Natural</button>

                            <button class="btn btn-default alterar_diagnostico" type="button" onclick="alterardiagnostico()">Alterar Diagnóstico
                            </button>

                            <button data-dismiss="modal" class="btn btn-danger outra_femea" type="button" onclick="selecinarOutraFemea()">Selecione Outra Fêmea
                            </button>

                            <button data-dismiss="modal" class="btn btn-default voltar" type="button" onclick="voltarModalEstacao()">Voltar
                            </button>

                            <button data-dismiss="modal" class="btn btn-default fechar" type="button" onclick="fecharModalEstacao()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_sem_estacao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p style="font-weight: bold;">Atenção!</p>    
                            <p class="mens_sem_1">Não está em estação de monta;</p>    
                            <p class="mens_sem_2">Não está na lista de Monta Natural;</p>  

                            <p class="mens_administrador" style="color: red;">Entre em contato com o Administrador do Sistema</p>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success alterar_diagnostico" type="button" onclick="gravar_nascimento_monta_natural();">Confirmar Nascimento Monta Natural</button>

                            <button data-dismiss="modal" class="btn btn-danger outra_femea" type="button" onclick="fechar_nascimento_erro();">Não Confirmar</button>
 
                            <button data-dismiss="modal" class="btn btn-default fechar" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Movimentações</h4>
                        </div>

                        <div class="modal-body"></div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button" onclick="abrir_modal_descricao_lote()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_composicao_descricao_lote" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle myLargeModalLabel"  data-backdrop="static">
                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Composição da Descrição do Lote
                            </h4>

                            <input type="hidden" name="numero_item" id="numero_item">

                            <input type="hidden" id="id_pasto_destino">
                            <input type="hidden" id="desc_pasto_destino">
                            <input type="hidden" id="desc_lote_destino">
                            <input type="hidden" id="qual_pasto">
                            <input type="hidden" id="qual_programa" value="movimentacao">
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

                                <div class='col-md-3 exibir_parametro_4' hidden>
                                    <label class="control-label label_parametro_4">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal" id="data_paricao_principal" onchange="exibe_descricao_lote()">
                                </div>

                                <div class='col-md-3 exibir_parametro_4_data_mais' hidden>
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

                                <!--<button type='button' class='btn btn-info pull-right voltar_descricao_lote' data-dismiss='modal'>Voltar
                                </button>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="fecha_mensagem_erro_descricao_lote();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_novo();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento </h4>
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
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_novo();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_nascimento_nove_meses" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                            <p class="mensagem_nove_meses"></p>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_nascimento_nove_meses()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_data" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body" style="font-weight: bold;"></div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" onclick="fechar_erro_gravar()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="nascimento_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p style="font-weight: bold;"><span id="tem_estacao">Estação </span><span id="estacao_nascimento"></span> Animal com ( <span id="calculo_dias_nascimento"></span> dias ) de gestação. Previsão (~ 282 dias)</p>    

                            <p class="mens_dias_gestacao" style="color: red;">Entre em contato com o Administrador do Sistema</p>

                            <p class="mens_alterar_prenhes" style="color: red;">Atenção! Ao confirmar o nascimento você estará alterando a data da prenhes.</p>
                        </div>

                        <div class="modal-footer">

                            <button class="btn btn-success gravar" type="button" onclick="gravar_nascimento();">Confirmar Nascimento</button>

                            <button class="btn btn-primary substituir" type="button" onclick="gravar_nascimento_monta_natural();">Substituir por Monta Natural</button>

                            <button data-dismiss="modal" class="btn btn-danger" type="button" onclick="fechar_nascimento_erro();">Não Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="nascimento_gemelar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p class="desc_gemelar"></p>    
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="volta_nascimento_mensagem();">Voltar e conferir a Fêmea</button>

                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true" onclick="nascimento_gemelar()">Nascimento Gemelar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="nascimento_aborto_natimorto" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Nascimento - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                            <p class="mensagem_aborto_natimorto"></p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="volta_nascimento_mensagem();">Fechar</button>
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

<script src="js/nascimento.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

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
    $(document).ready(function(){
        $('#codigo_mae_consulta').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch_femeas_servidas.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#local_id').val()},
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

        $('#codigo_mae_consulta_natimorto').typeahead({
            source: function(query, result) {
                $.ajax({
                    url:"fetch_femeas_servidas.php",
                    method:"POST",
                    data:{query:query,
                          local: $('#local_id_natimorto').val()},
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

        $("#codigo_mae_consulta").click(function(){
            $("#codigo_mae_consulta").val('');
            $("#codigo_mae_animal").val('');
            document.getElementById("codigo_mae_consulta").style.borderColor = "";
            $(".desc_novo_nascimento").html('');
            return;
        });

        $("#codigo_mae_consulta_natimorto").click(function(){
            $("#codigo_mae_consulta_natimorto").val('');
            $("#codigo_mae_natimorto").val('');
            return;
        });
    });

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
