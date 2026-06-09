<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";
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
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_cadastro[1] == 0){
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

    $tbl_epoca_pesagem_filtro = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 

    $tbl_epoca_pesagem = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $categoria_filtro = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $array_sexo = $_SESSION['sexo_peso'];
    $array_categoria = $_SESSION['categoria_peso'];
    $data_sistema = date("Y-m-d");
    $controle_estoque = $_SESSION['controle_estoque'];

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
        include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_pesagem_animais.php"> Pesagem</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Incluir</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-weight"></i> Nova Pesagem</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12" id="selecionar_pasagem">
                        <form method="POST" action="#" id="form_gravar_pesagem" enctype="multipart/form-data" >
                            
                            <div class="tab-panel selecionar_dados_pesagem">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Dados para pesagem</legend>

                                        <div class="row">
                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <input name="numero_pesagem_id" type="hidden" id="numero_pesagem_id">

                                            <input  name="finalizar_pesagem" type="hidden" id="finalizar_pesagem" value="N">

                                            <input  name="tipo_gravacao" type="hidden" id="tipo_gravacao" value="1">

                                            <div class="form-group col-md-3">
                                                <label for="data_pesagem" class="control-label"><span class="required">*</span> Data</label>

                                                <input type="date" id="data_pesagem" class="form-control"
                                                <?php echo "value='".$data_sistema."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="local_pesagem" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" name="local_pesagem" id="local_pesagem">
                                                <option value="000000000">...</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="epoca_pesagem_filtro" class="control-label"><span class="required">*</span> Motivo da Pesagem</label>

                                                <select class="form-control" name="epoca_pesagem_filtro" id="epoca_pesagem_filtro">

                                                <option value="000">...</option>

                                                <?php 

                                                while($reg_ep = mysqli_fetch_object($tbl_epoca_pesagem_filtro)) { ?>

                                                <option value="<?php 
                                                    echo $reg_ep->tab_codigo_epoca_pesagem ?>">
                                                                    
                                                    <?php 
                                                    echo $reg_ep->tab_descricao_epoca_pesagem;
                                                    ?>
                                                </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="pasto" class="control-label"><span class="required">*</span> Pasto</label>
                                                <select class="form-control selectpicker" id="pasto" name="pasto" multiple=""  data-live-search="true" data-size="10">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="categoria_filtro" class="control-label">Categoria</label>
                                                <select class="form-control selectpicker" multiple id="categoria_filtro" name="categoria_filtro">
                                                              
                                                <?php while($reg_catagoria = mysqli_fetch_object($categoria_filtro)) { ?>

                                                <option value="<?php 
                                                    echo $reg_catagoria->tab_codigo_categoria_idade ?>"

                                                    <?php 

                                                    if ($array_categoria!="") {
                                                        foreach ($array_categoria as $value) {
                                                            if ($value==$reg_catagoria->tab_codigo_categoria_idade) { 
                                                                echo "selected";       
                                                            }
                                                        }                           
                                                    }
                                                    ?>>
                                                                
                                                    <?php 
                                                        if ($reg_catagoria->tab_categoria_idade_ate==999999999) {
                                                            echo ' > 36 meses';
                                                        }
                                                        else {
                                                            echo $reg_catagoria->tab_categoria_idade_de . ' a ' . $reg_catagoria->tab_categoria_idade_ate . ' meses';
                                                        }
                                                    ?>
                                                    </option>
                                                    <?php } ?>

                                                </select>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label class="control-label">Sexo</label>
                                                <div class="clearfix"></div>

                                                <label class="checkbox-inline">
                                                    <?php
                                                    if ($array_sexo[0]=="Todos" || $array_sexo[0]=="M") {
                                                        echo '<input type="checkbox" checked="checked" value="M" name="macho" id="macho"> Macho';
                                                    }
                                                    else if ($array_sexo[0]!="Todos"){
                                                        foreach ($array_sexo as $value) {
                                                            if ($value=="M") { 
                                                                echo '<input type="checkbox" checked="checked" value="M" name="macho" id="macho"> Macho';
                                                            }
                                                            else {
                                                                echo '<input type="checkbox"  value="M" name="macho" id="macho"> Macho';
                                                            }
                                                        }                       
                                                    }
                                                    else {
                                                        echo '<input type="checkbox"  value="M" name="macho" id="macho"> Macho';
                                                    }
                                                    ?>
                                                </label>
                                                
                                                <label class="checkbox-inline">
                                                    <?php
                                                    if ($array_sexo[0]=="Todos" || $array_sexo[0]=="F") {
                                                        echo '<input type="checkbox" checked="checked" value="F" name="femea" id="femea"> Fêmea';
                                                    }
                                                    else if ($array_sexo[0]!="Todos"){
                                                        foreach ($array_sexo as $value) {
                                                            if ($value=="F") { 
                                                                echo '<input type="checkbox" checked="checked" value="F" name="femea" id="femea"> F';
                                                            }
                                                            else {
                                                                echo '<input type="checkbox"  value="F" name="femea" id="femea"> F';
                                                            }
                                                        }                       
                                                    }
                                                    else {
                                                        echo '<input type="checkbox"  value="F" name="femea" id="femea"> F';
                                                        }
                                                    ?>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <textarea name="descricao_filtro" id='descricao_filtro'
                                                class="form-control text-muted descricao_filtro" wrap="hard" style="font-size: 12px; border: none; color: #ccc" maxlength="200"></textarea>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-primary" onclick="iniciar_pesagem()"
                                                >Iniciar - Pesagem On-line</button>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-success" onclick="exportar_excel_pesagem()"
                                                data-toggle='tooltip' data-placement='top' title="Exportar Excel para registro manual do Peso">Gerar Excel - Pesagem Off-line</button>
                                            </div>

                                            <div class="form-group col-md-5">
                                            </div>

                                            <div class="form-group col-md-1">
                                                <label class="control-label">&nbsp;</label>
                                                <input type="button" class="form-control btn btn-info " onclick="finalizar_sair()" value="Volta">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <!--<div class="tab-panel" id="itens" hidden="">
                                <div class="tab-pane active table-responsive">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Animais Pesados</legend>
                                            <table class="table table-striped table-advance table-hover" id="tabela_itens" style="font-size: 13px; width:100%;">
                                                <thead>
                                                    <tr>
                                                        <div class="row">
                                                            <div class="form-group col-md-7">
                                                                <p class="text-muted-dark descricao_filtro" style="font-size: 11px; color:lightgray;"></p>
                                                            </div>

                                                            <div class="form-group col-md-5">
                                                                <button type="button" class="btn btn-success" onclick="continuar_pesagem()" data-toggle='tooltip' data-placement='top' title="Continuar digitando os pesos"><i class="fas fa-weight"></i> Pesar
                                                                </button>

                                                                <button type="button" class="btn btn-primary" onclick="terminar_pesagem()">Finalizar Pesagem</button>

                                                                <button type="button" class="btn btn-info pull-right" data-toggle='tooltip' data-placement='top' title="Você poderá continuar a digitação mais tarde" onclick="fecha_consultar_pesagem()">Sair sem Finalizar</button> 
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Data </label>
                                                                <input class="form-control data_pesagem" type="date" name="data_pesagem">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Motivo da Pesagem </label>

                                                            <select class="form-control" name="epoca_pesagem" id="epoca_pesagem">

                                                            <?php 

                                                            //while($reg_ep = mysqli_fetch_object($tbl_epoca_pesagem)) { ?>

                                                                <option value="<?php 
                                                                    //echo $reg_ep->tab_codigo_epoca_pesagem ?>">
                                                                                
                                                                <?php 
                                                                //echo $reg_ep->tab_descricao_epoca_pesagem;
                                                                ?>
                                                                </option>
                                                                    <?php //} ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label class="control-label"><span class="required">*</span> Lote </label>

                                                                <input class="form-control descricao_lote" type="text" name="descricao_lote" id="descricao_lote" maxlength="50"
                                                                onkeyup="maiuscula(this)">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Animais para Pesar:&nbsp;
                                                                <span class="total_a_pesar" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="total_a_pesar" class="total_a_pesar" id="total_a_pesar">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Animais Pesados:&nbsp;
                                                                <span class="total_pesados" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="total_pesados" id="total_pesados" class="total_pesados">
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Total Kg:&nbsp;
                                                                <span class="peso_total_kg" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_total_kg" class="peso_total_kg">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Total @:&nbsp;
                                                                <span class="peso_total_arroba" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_total_arroba" class="peso_total_arroba">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Médio Kg:&nbsp;
                                                                <span class="peso_medio_kg" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_medio_kg" class="peso_medio_kg">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">"Peso Médio @:&nbsp;
                                                                <span class="peso_medio_arroba" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_medio_arroba" class="peso_medio_arroba">
                                                            </div>
                                                        </div>
                                                    </tr>
                                                    <tr></tr>

                                                    <tr>
                                                        <th> Id</th>
                                                        <th> Peso (Kg)</th>
                                                        <th> Sexo</th>
                                                        <th> Nascimento</th>
                                                        <th> Raça</th>
                                                        <th> Cor</th>
                                                        <th> Mãe</th>
                                                        <th> Observação</th>
                                                        <th> <i class="icon_cogs"></i> Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        <input type="hidden" name="array_itens" id="array_itens">    

                                        <div class="row">
                                            <div class="col-md-7">
                                            </div>

                                            <div class="form-group col-md-5 botoes_final">
                                                <button type="button" class="btn btn-success" onclick="continuar_pesagem()" data-toggle='tooltip' data-placement='top' title="Continuar digitando os pesos"><i class="fas fa-weight"></i> Pesar
                                                </button>

                                                <button type="button" class="btn btn-primary" onclick="terminar_pesagem()">Finalizar Pesagem</button>

                                                <button type="button" class="btn btn-info pull-right" data-toggle='tooltip' data-placement='top' title="Você poderá continuar a digitação mais tarde" onclick="fecha_consultar_pesagem()">Sair sem Finalizar</button> 
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div> 
                            </div> -->
                           
                            <div class="tab-panel" id="itens_pesagem_lote" hidden="">
                                <div class="tab-pane active table-responsive">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Animais Pesados</legend>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_pesagem_lote" style="font-size: 13px; width:100%;">
                                                <thead>
                                                    <tr>
                                                        <div class="row">
                                                            <div class="form-group col-md-7">
                                                                <p class="text-muted-dark descricao_filtro" style="font-size: 11px; color:lightgray;"></p>
                                                            </div>

                                                            <div class="form-group col-md-5">
                                                                <button type="button" class="btn btn-success" onclick="continuar_pesagem_lote()" data-toggle='tooltip' data-placement='top' title="Continuar digitando os pesos"><i class="fas fa-weight"></i> Pesar
                                                                </button>

                                                                <button type="button" class="btn btn-primary" onclick="terminar_pesagem_lote()">Finalizar Pesagem</button>

                                                                <button type="button" class="btn btn-info pull-right" data-toggle='tooltip' data-placement='top' title="Você poderá continuar a digitação mais tarde" onclick="fecha_consultar_pesagem()">Sair sem Finalizar</button> 
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Data </label>
                                                                <input class="form-control data_pesagem" type="date" name="data_pesagem">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Motivo da Pesagem </label>

                                                            <select class="form-control" name="epoca_pesagem" id="epoca_pesagem">

                                                            <?php 

                                                            while($reg_ep = mysqli_fetch_object($tbl_epoca_pesagem)) { ?>

                                                                <option value="<?php 
                                                                    echo $reg_ep->tab_codigo_epoca_pesagem ?>">
                                                                                
                                                                <?php 
                                                                echo $reg_ep->tab_descricao_epoca_pesagem;
                                                                ?>
                                                                </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label class="control-label"><span class="required">*</span> Lote </label>

                                                                <input class="form-control descricao_lote" type="text" name="descricao_lote" id="descricao_lote" maxlength="50"
                                                                onkeyup="maiuscula(this)">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Animais para Pesar:&nbsp;
                                                                <span class="total_a_pesar" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="total_a_pesar" class="total_a_pesar" id="total_a_pesar">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Animais Pesados:&nbsp;
                                                                <span class="total_pesados" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="total_pesados" id="total_pesados" class="total_pesados">
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Total Kg:&nbsp;
                                                                <span class="peso_total_kg" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_total_kg" class="peso_total_kg">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Total @:&nbsp;
                                                                <span class="peso_total_arroba" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_total_arroba" class="peso_total_arroba">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">Peso Médio Kg:&nbsp;
                                                                <span class="peso_medio_kg" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_medio_kg" class="peso_medio_kg">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="label_consulta">"Peso Médio @:&nbsp;
                                                                <span class="peso_medio_arroba" style="font-weight: normal;">
                                                                </span></label>  

                                                                <input type="hidden" name="peso_medio_arroba" class="peso_medio_arroba">
                                                            </div>
                                                        </div>
                                                    </tr>
                                                    <tr></tr>

                                                    <tr>
                                                        <th> Categoria</th>
                                                        <th> Quantidade</th>
                                                        <th> Peso (Kg)</th>
                                                        <th> Peso Médio (Kg)</th>
                                                        <th> Peso (@)</th>
                                                        <th> Peso Médio (@)</th>
                                                        <th> Grupo Destino</th>
                                                        <th> <i class="icon_cogs"></i> Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>

                                        <input type="hidden" name="array_itens_pesagem_lote" id="array_itens_pesagem_lote">    

                                        <div class="row">
                                            <div class="col-md-7">
                                            </div>

                                            <div class="form-group col-md-5 botoes_final">
                                                <button type="button" class="btn btn-success" onclick="continuar_pesagem_lote()" data-toggle='tooltip' data-placement='top' title="Continuar digitando os pesos"><i class="fas fa-weight"></i> Pesar
                                                </button>

                                                <button type="button" class="btn btn-primary" onclick="terminar_pesagem_lote()">Finalizar Pesagem</button>

                                                <button type="button" class="btn btn-info pull-right" data-toggle='tooltip' data-placement='top' title="Você poderá continuar a digitação mais tarde" onclick="fecha_consultar_pesagem()">Sair sem Finalizar</button> 
                                            </div>
                                        </div>
                                    </fieldset>
                                </div> 
                            </div>
                        </form>
                    </div>    
                </div>
            </div>
	        <!-- page end-->


            <div class="modal fade" id="modal_pesar_estimada" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Pesagem - Digitação On-line</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_pesagem_lote.php" enctype="multipart/form-data" id="form_gravar_pesagem_lote">

                                <input name="numero_pesagem_lote" type="hidden" id="numero_pesagem_lote">

                                <input name="data_pesagem_lote" type="hidden" id="data_pesagem_lote">

                                <input name="local_pesagem_lote" type="hidden" id="local_pesagem_lote">

                                <input name="filtros_lote" type="hidden" id="filtros_lote">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_estimado" id="alert_erro_estimado" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div id="dados" class="tab-pane active">

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="descricao_local" class="control-label"> Fazenda</label>
                                                <input name="descricao_local" type="text" class="form-control" id="descricao_local" readonly="">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="descricao_epoca" class="control-label"> Motivo da Pesagem</label>
                                                <input name="descricao_epoca" type="text" class="form-control" id="descricao_epoca" readonly="">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="descricao_pasto" class="control-label"> Pasto(s)</label>
                                                <input name="descricao_pasto" type="text" class="form-control" id="descricao_pasto" value="Todos" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="lote_pesagem_lote" class="control-label"><span class="required">*</span> Descrição da Pesagem</label>
                                                <input name="lote_pesagem_lote" type="text" class="form-control" id="lote_pesagem_lote" maxlength="50"
                                                onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_a_pesar_pesagem_lote" class="control-label">Animais para Pesar</label>
                                                <input name="qtd_a_pesar_pesagem_lote" type="number" class="form-control" id="qtd_a_pesar_pesagem_lote">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_pesado_pesagem_lote" class="control-label">Animais Pesados</label>
                                                <input name="qtd_pesado_pesagem_lote" type="number" class="form-control" id="qtd_pesado_pesagem_lote" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="codigo_categoria" class="control-label"><span class="required">*</span> Categoria/Sexo</label>
                                                <select class="form-control" id="codigo_categoria" name="codigo_categoria">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_estimada" class="control-label"><span class="required">*</span> Quantidade</label>
                                                <input name="qtd_estimada" type="number" class="form-control" id="qtd_estimada" onchange="soma_total_item_lote()">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="peso_estimado" class="control-label"><span class="required">*</span> Peso (Kg)</label>
                                                <input name="peso_estimado" type="text" class="form-control" id="peso_estimado" onchange="soma_total_item_lote()">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="grupo_destino" class="control-label">Grupo Destino &nbsp; 

                                                <i class="icon_info_alt" data-toggle='tooltip' data-placement='right' title="Informe aqui um número para agrupar animais que posteriormente poderam ser transferidos para outros pastos." style="color: blue;"></i>

                                                </label>
                                                
                                                <input name="grupo_destino" type="number" class="form-control" id="grupo_destino">

                                            </div>

                                            <input type="hidden" name="sexo_lote" id="sexo_lote">
                                            <input type="hidden" name="categoria_lote" id="categoria_lote">
                                            <input type="hidden" name="qtd_lote" id="qtd_lote">
                                           <input type="hidden" name="qtd_digitado_anterior" id="qtd_digitado_anterior">
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="peso_medio_estimado" class="control-label">Peso Médio (Kg)</label>
                                                <input name="peso_medio_estimado" type="text" class="form-control" id="peso_medio_estimado"
                                                readonly="">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="peso_estimado_arroba" class="control-label">Peso (@)</label>
                                                <input name="peso_estimado_arroba" type="text" class="form-control" id="peso_estimado_arroba" readonly="">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="peso_medio_estimado_arroba" class="control-label">Peso Médio (@)</label>
                                                <input name="peso_medio_estimado_arroba" type="text" class="form-control" id="peso_medio_estimado_arroba" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-12 mensagem_venda" hidden="">
                                                <p style="color: red; font-size: 16px; line-height: 10px; opacity: 0.8;">Atenção: Digitar o peso apenas dos animais que serão vendidos</p>
                                            </div>
                                        </div> 

                                        <div class="row">
                                            <div class="form-group col-md-2" id="incluir_lote">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="Salvar_estimada()">Confirmar</button>
                                            </div>

                                            <div class="form-group col-md-2" id="editar_lote" hidden="" >
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="Salvar_editar_estimado()">Confirmar</button>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="btn btn-primary" onclick="pausar_pesagem_estimada()">Pausar Pesagem</button>
                                            </div>
                                        </div>

                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_finalizar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-success" type="button" onclick="gravar_pesagem_finalizar();">Sim

                            <button data-dismiss="modal" class="btn btn-danger" type="button">Não
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_sair_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
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
                            <h4 class="modal-title">Pesagem </h4>
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
                            <h4 class="modal-title">Pesagem - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
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

<script src="js/pesagem_lote.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

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
        var mask = {
             money: function() {
                var el = this
                ,exec = function(v) {
                v = v.replace(/\D/g,"");
                v = new String(Number(v));
                var len = v.length;
                if (1== len)
                v = v.replace(/(\d)/,"0.0$1");
                else if (2 == len)
                v = v.replace(/(\d)/,"0.$1");
                else if (len > 2) {
                v = v.replace(/(\d{2})$/,'.$1');
                }
                return v;
                };

                setTimeout(function(){
                el.value = exec(el.value);
                },1);
             }
        }
    </script>

</body>
</html>
