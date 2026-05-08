<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay"  rel="stylesheet" crossorigin="anonymous">

  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
  <link href="css/tabela_1300.css" rel="stylesheet">
  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style type="text/css">
    .bootstrap-select > .dropdown-toggle:hover,
    .bootstrap-select > .dropdown-toggle:focus {
        background-color: #fff !important; /* Cor de fundo branca */
        color: #333 !important; /* Cor do texto padrão (preto/cinza escuro) */
        outline: none !important; /* Remove qualquer outline de foco azul/cinza */
        box-shadow: none !important; /* Remove qualquer sombra de foco */
    }

    /* 2. Garante que o estado 'ativo' (enquanto a lista está aberta) também não fique cinza */
    .bootstrap-select.open > .dropdown-toggle {
        background-color: #fff !important;
        color: #333 !important; /* Cor do texto padrão (preto/cinza escuro) */
    }

    /* Opcional: Se a cor de fundo estiver sendo aplicada ao componente inteiro */
    .bootstrap-select.btn-group .dropdown-toggle:hover {
        background-color: #fff !important;
        color: #333 !important; /* Cor do texto padrão (preto/cinza escuro) */
    }  

    .selectpicker-erro .dropdown-toggle {
        border: 1px solid red !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.25) !important;
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
    if (isset($_REQUEST['tipo'])) {
        $origem_relatorio=$_REQUEST['tipo'];
    }
    else {
        $origem_relatorio=1;
    }
    
    @ session_start();   

    $controle_estoque= $_SESSION['controle_estoque'];

    if(isset($_SESSION['menu_relatorios'])) {
        $array_relatorios = explode("!",$_SESSION['menu_relatorios']);

        if ($array_relatorios[0] == 0){
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

    $raca_filtro = mysqli_query($conector, "select * from tabela_racas where tab_registro_lixeira_raca=0"); 

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local_filtro_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $categoria_filtro = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 
    $categoria_filtro_filtro = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 
    $pai_filtro = mysqli_query($conector, "select * from tbl_animais where tbl_animal_lixeira=0 and tbl_animal_ativo='S' and tbl_animal_sexo='M'
        order by tbl_animal_codigo_numerico"); 

    $semem_filtro = mysqli_query($conector, "select * from tbl_semem where tbl_semem_lixeira=0"); 

    $mae_filtro = mysqli_query($conector, "select * from tbl_animais where tbl_animal_lixeira=0 and tbl_animal_ativo='S' and tbl_animal_sexo='F'
        order by tbl_animal_codigo_numerico"); 

    $origem_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=2 or tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0
        order by tbl_pessoa_nome");

    $codigo_alfa = $_SESSION['codigo_alfa'];
    $codigo_numerico = $_SESSION['codigo_numerico']; 
    $previsao_parto_de_filtro = $_SESSION['previsao_parto_de'];
    $previsao_parto_ate_filtro = $_SESSION['previsao_parto_ate'];
    $num_parto_de_filtro = $_SESSION['num_parto_de'];
    $num_parto_ate_filtro = $_SESSION['num_parto_ate'];
    $num_aborto_de_filtro = $_SESSION['num_aborto_de'];
    $num_aborto_ate_filtro = $_SESSION['num_aborto_ate'];
    $solteiras = $_SESSION["solteiras"];
    $descarte = $_SESSION["descarte"];
    $paridas = $_SESSION["paridas"];
    $data_paridas_ate = $_SESSION["data_paridas_ate"];
    $positivo = $_SESSION['positivo'];
    $negativo = $_SESSION['negativo'];
    $array_raca = $_SESSION['raca'];
    $array_pai = $_SESSION['pai'];
    $array_mae = $_SESSION['mae'];
    $array_sexo = $_SESSION['sexo'];
    $array_ativo = $_SESSION['ativo_filtro'];
    $array_local = $_SESSION['local'];
    $array_origem_filtro = $_SESSION['origem'];
    $array_categoria = $_SESSION['categoria'];
    $peso_inicial_nasc_filtro = $_SESSION['peso_nasc_inicial'];
    $peso_final_nasc_filtro = $_SESSION['peso_nasc_final'];
    $peso_inicial_desmama_filtro = $_SESSION['peso_desmama_inicial']=''; 
    $peso_final_desmama_filtro = $_SESSION['peso_desmama_final']=''; 
    $peso_inicial_ultimo_filtro = $_SESSION['peso_ultimo_inicial']=''; 
    $peso_final_ultimo_filtro = $_SESSION['peso_ultimo_final']=''; 
    $data_nasc_inicial_filtro = $_SESSION['data_nasc_inicial']; 
    $data_nasc_final_filtro = $_SESSION['data_nasc_final']; 

    $tipo_rel = $_SESSION['tipo_rel_historico_animais']; 
    $local = $_SESSION['local_pesagem']; 
    $categoria = $_SESSION['categoria_historico_animais'];
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

  <!-- container section start -->
  <section id="container" class="">

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Hitórico de Animais</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="far fa-file-alt"></i> Histórico de Animais</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                        <div class="row col-md-12 filtro_exibido" id="consulta_contas">
                            <form method="GET" action="#" enctype="multipart/form-data" >
                            
                                <div class="tab-panel ">
                                    <div class="tab-pane active">
                                        <fieldset class="scheduler-border " >
                                            <legend class="scheduler-border fonte-legend">Filtros</legend>

                                            <div class="row">
                                                <div class="form-group col-md-11">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="onclick=voltar_relatorios()">Voltar
                                                    </button>
                                                    <input type="hidden" id="origem_relatorio" <?php echo "value='".$origem_relatorio."'";?>>                    
                                                    <input id="exibe_local" type="hidden" <?php echo "value='".$local."'"; ?>>

                                                    <input id="exibe_categoria" type="hidden" <?php echo "value='".$categoria."'"; ?>>

                                                    <input type="hidden" name="controle_estoque" id="controle_estoque"
                                                    <?php echo "value='".$controle_estoque."'";?>>
                                                </div>
                                            </div>    
                                                
                                            <div class="row ">
                                                <div class="form-group col-md-6">
                                                    <label class="control-label"><span class="required">*</span> Tipo do Relatório:&nbsp;</label>

                                                    <label class="radio-inline"> 
                                                      <input type="radio" name="tipo_rel" value="G" class="tipo_rel"  
                                                      <?php if ($tipo_rel == 'G'){echo "checked";}?>> Geral
                                                    </label>
                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_rel" value="I"  class="tipo_rel"
                                                       <?php if ($tipo_rel == 'I'){echo "checked";}?>> Por Indivíduo
                                                    </label>
                                                </div>  
                                            </div>  

                                            <div class="row">
                                                <div class="form-group col-md-3 geral">
                                                    <label for="codigo_local_filtro" class="control-label">Fazenda</label>
                                                    <select class="form-control selectpicker" id="codigo_local_filtro" multiple name="codigo_local_filtro">

                                                    <?php 
                                                    while($reg_local = mysqli_fetch_object($local_filtro)) { 
                                                        foreach ($array_locais_usuario as $value) {
                                                            $value = ltrim($value);
                                                            $value = rtrim($value);
                                                            
                                                            if ($value == $reg_local->tbl_pessoa_id) {
                                                                echo '<option value="' . $value . '">' . $reg_local->tbl_pessoa_nome . '</option>';
                                                            }
                                                        }
                                                    } 
                                                    ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3 geral">
                                                    <label for="codigo_categoria_filtro" class="control-label">Categoria</label>
                                                    <select class="form-control selectpicker" multiple id="codigo_categoria_filtro" name="codigo_categoria_filtro">
                                                          
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

                                                <div class="form-group col-md-3 codigo" hidden>
                                                    <label for="codigo_number_filtro" class="control-label"><span class="required">*</span> Código do Animal</label>
                                                    <input name="codigo_number_filtro" type="text" class="form-control" id="codigo_number_filtro"
                                                    autocomplete="off">
                                                </div>

                                                <div class="form-group col-md-2 geral">
                                                    <label class="control-label">&nbsp;</label>

                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="filtros()"
                                                    data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-primary pull-right" data-toggle='tooltip' data-placement='top' title="Listar na Tela" onclick="veriricar_tipo_relatorio(1)">Listar
                                                    </button>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-success pull-right" onClick="veriricar_tipo_relatorio(2)">Excel
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row filtro_aplicado" hidden>
                                                <div class="col-md-12" style="margin-bottom: 5px; margin-top: 5px; color: #c0c3c4; font-size: 12px;">
                                                    <span id="filtro_aplicado"></span>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                        </div> 

                    <div id="lista_animais"></div>

    	        </div>
	        </div>
	        <!-- page end-->
            <div class="modal fade" id="modal_filtros" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="aplicar_filtros()"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="modal_incluirLabel">Filtros</h3>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_filtrar">
                              
                                <div class="tab-content">
                                    <div class="tab-pane active">
                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" 
                                                data-dismiss="modal" aria-label="Close" onclick="aplicar_filtros()">Aplicar Filtros</button>

                                                <a href="#" class="pull-right" onclick="limpar_filtros(),aplicar_filtros()">Limpar Filtros</a>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-3 ativo">
                                                <label for="animal_ativo" class="control-label">Ativo</label>  

                                                <div class="clearfix"></div>

                                                <label class="checkbox-inline">
                                                <?php
                                                if ($array_ativo[0]=="Todos" || $array_ativo[0]=="S") {
                                                    echo '<input type="checkbox" checked="checked" value="S" name="sim_filtro" id="sim_filtro"> Sim';
                                                }
                                                else if ($array_ativo[0]!="Todos"){
                                                    foreach ($array_ativo as $value) {
                                                        if ($value=="S") { 
                                                            echo '<input type="checkbox" checked="checked" value="S" name="sim_filtro" id="sim_filtro"> Sim';
                                                        }
                                                        else {
                                                            echo '<input type="checkbox"  value="S" name="sim_filtro" id="sim_filtro"> Sim';
                                                        }
                                                    }                       
                                                }
                                                else {
                                                    echo '<input type="checkbox"  value="S" name="sim_filtro" id="sim_filtro"> Sim';
                                                }

                                                ?>

                                                </label>
                                                <label class="checkbox-inline">

                                                <?php
                                                if ($array_ativo[0]=="Todos" || $array_ativo[0]=="N") {
                                                    echo '<input type="checkbox" checked="checked" value="N" name="nao_filtro" id="nao_filtro"> Não';
                                                }
                                                else if ($array_ativo[0]!="Todos"){
                                                    foreach ($array_ativo as $value) {
                                                        if ($value=="N") { 
                                                            echo '<input type="checkbox" checked="checked" value="N" name="nao_filtro" id="nao_filtro"> Não';
                                                        }
                                                        else {
                                                            echo '<input type="checkbox"  value="N" name="nao_filtro" id="nao_filtro"> Não';
                                                        }
                                                    }                       
                                                }
                                                else {
                                                    echo '<input type="checkbox"  value="N" name="nao_filtro" id="nao_filtro"> Não';
                                                    }
                                                ?>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-5 situacao">
                                                <label class="control-label">Situações p/ Ativos Não</label>
                                                <div class="clearfix"></div>

                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="V" name="vendido" id="vendido"> Vendidos
                                                </label>

                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="M" name="morte" id="morte" checked="checked"> Mortes
                                                </label>

                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="O" name="outro" id="outro" checked="checked"> Outras Saídas
                                                </label>
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
                                                            echo '<input type="checkbox" checked="checked" value="F" name="femea" id="femea"> Fêmea';
                                                        }
                                                        else {
                                                            echo '<input type="checkbox"  value="F" name="femea" id="femea"> Fêmea';
                                                        }
                                                    }                       
                                                }
                                                else {
                                                    echo '<input type="checkbox"  value="F" name="femea" id="femea"> Fêmea';
                                                    }
                                                ?>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row ">
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local_filtro_filtro" class="control-label">Fazenda</label>

                                                <select class="form-control selectpicker" id="codigo_local_filtro_filtro" multiple name="codigo_local_filtro_filtro">

                                                <?php 
                                                while($reg_local = mysqli_fetch_object($local_filtro_filtro)) { 
                                                    
                                                    foreach ($array_locais_usuario as $value) {
                                                        $value = ltrim($value);
                                                        $value = rtrim($value); 

                                                        if ($value==$reg_local->tbl_pessoa_id) {

                                                            if ($array_local!="") {
                                                                foreach ($array_local as $values) {
                                                                    if ($values==$reg_local->tbl_pessoa_id) { 
       
                                                                        echo '<option value="'.$value.'" selected>' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                                    }
                                                                    else {
                                                                        echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                                    }
                                                                }                           
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

                                            <div class="form-group col-md-4">
                                                <label for="codigo_origem_filtro" class="control-label">Origem</label>
                                                <select class="form-control selectpicker" data-live-search="true" multiple id="codigo_origem_filtro" name="codigo_origem_filtro" data-size="6">

                                                <?php while($reg_origem = mysqli_fetch_object($origem_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_origem->tbl_pessoa_id ?>"
                                                        <?php 
                                                            if ($array_origem_filtro!="") {
                                                                foreach ($array_origem_filtro as $value) {
                                                                    if ($value==$reg_origem->tbl_pessoa_id) { 
                                                                        echo "selected";       
                                                                    }
                                                                }                           
                                                            }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_origem->tbl_pessoa_nome;
                                                    ?>
                                                    </option>
                                                <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="codigo_categoria_filtro_filtro" class="control-label">Categoria</label>
                                                <select class="form-control selectpicker" multiple id="codigo_categoria_filtro_filtro" name="codigo_categoria_filtro_filtro">
                                                          
                                                <?php while($reg_catagoria = mysqli_fetch_object($categoria_filtro_filtro)) { ?>

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
                                        </div>

                                        <!--<div class="row">
                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_local()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_origem()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_categoria()'>Limpa Seleção</a>
                                            </div>
                                        </div>-->

                                        <div class="row">
                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="codigo_raca_filtro" class="control-label">Raça</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_raca_filtro" name="codigo_raca_filtro" data-size="7">

                                                <?php while($reg_raca = mysqli_fetch_object($raca_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_raca->tab_codigo_raca ?>"

                                                    <?php 
                                                        if ($array_raca!="") {
                                                            foreach ($array_raca as $value) {
                                                                if ($value==$reg_raca->tab_codigo_raca) {
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_raca->tab_descricao_raca;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>
                                            </div>

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="codigo_pai_filtro" class="control-label">Pai</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_pai_filtro" name="codigo_pai_filtro" data-size="7">

                                                <optgroup label="SEMEM">  

                                                <?php while($reg_pai = mysqli_fetch_object($semem_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_pai->tbl_semem_codigo_id ?>"

                                                    <?php 
                                                        if ($array_pai!="") {
                                                            foreach ($array_pai as $value) {
                                                                if ($value==$reg_pai->tbl_semem_codigo_id) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_pai->tbl_semem_nome;
                                                    ?>
                                                    </option>
                                                <?php } ?>
                                                </optgroup>

                                                <optgroup label="ANIMAIS">  

                                                <?php while($reg_pai = mysqli_fetch_object($pai_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_pai->tbl_animal_codigo_id ?>"

                                                    <?php 
                                                        if ($array_pai!="") {
                                                            foreach ($array_pai as $value) {
                                                                if ($value==$reg_pai->tbl_animal_codigo_id) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_pai->tbl_animal_codigo_alfa. ' ' . $reg_pai->tbl_animal_codigo_numerico;
                                                    ?>
                                                    </option>
                                                <?php } ?>
                                                </optgroup>

                                                </select>
                                            </div>

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="codigo_mae_filtro" class="control-label">Mãe</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_mae_filtro" name="codigo_mae_filtro" data-size="7">

                                                <?php while($reg_mae = mysqli_fetch_object($mae_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_mae->tbl_animal_codigo_id ?>"

                                                    <?php 
                                                        if ($array_mae!="") {
                                                            foreach ($array_mae as $value) {
                                                                if ($value==$reg_mae->tbl_animal_codigo_id) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_mae->tbl_animal_codigo_alfa. ' ' . $reg_mae->tbl_animal_codigo_numerico;
                                                    ?>
                                                    </option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!--<div class="row">
                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_raca()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_pai()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_mae()'>Limpa Seleção</a>
                                            </div>
                                        </div>-->

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="data_nasc_inicial_filtro" class="control-label">Nascimento Início</label>
                                                <input name="data_nasc_inicial_filtro" type="date" class="form-control" id="data_nasc_inicial_filtro">
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="data_nasc_final_filtro" class="control-label">Nascimento Fim</label>
                                                <input name="data_nasc_final_filtro" type="date" class="form-control" id="data_nasc_final_filtro">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_nasc_filtro" class="control-label">Peso Nascimento Início</label>
                                                <input name="peso_inicial_nasc_filtro" type="text" class="form-control" id="peso_inicial_nasc_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="4">
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_nasc_filtro" class="control-label">Peso Nascimento Fim</label>
                                                <input name="peso_final_nasc_filtro" type="text" class="form-control" id="peso_final_nasc_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                > 
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_desmama_filtro" class="control-label">Peso Desmama Início</label>
                                                <input name="peso_inicial_desmama_filtro" type="text" class="form-control" id="peso_inicial_desmama_filtro"            
                                                onkeypress = "return numeros(this, event)" maxlength="4">
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_desmama_filtro" class="control-label">Peso Desmama Fim</label>
                                                <input name="peso_final_desmama_filtro" type="text" class="form-control" id="peso_final_desmama_filtro"
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                >
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_ultimo_filtro" class="control-label">Último Peso Início</label>
                                                <input name="peso_inicial_ultimo_filtro" type="text" class="form-control" id="peso_inicial_ultimo_filtro"
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                >
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_ultimo_filtro" class="control-label">Últmo Peso Fim</label>
                                                <input name="peso_final_ultimo_filtro" type="text" class="form-control" id="peso_final_ultimo_filtro"
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                >
                                            </div>
                                        </div>

                                        <div class="row abrir_filtro_reproducao">
                                            <div class="form-group col-xs-6 col-md-12" style="text-align: center;">
                                                <a href="#" onclick="abrir_filtro_reproducao(),limpar_filtros_reproducao()">Abrir Filtros Reprodução</a>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="col-xs-6 col-md-12" >
                                                <h3>Filtros Reprodução <span style="border: none; color: #bdbbbb; font-size: 13px; font-weight: 500;">(Somente Fêmeas <strong>&ge;</strong> 12 meses)</span></h3>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;</label>                       
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VP" name="vacas_paridas" id="vacas_paridas" > Vacas Paridas
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-xs-6 col-md-6">
                                                <label class="control-label">&nbsp;</label>                                   
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VS" name="vacas_solteiras" id="vacas_solteiras" > Vacas Solteiras <span style="border: none; color: #bdbbbb">&nbsp;&nbsp;(Paridas há 8 meses+ e Novilhas)</span>
                                                    </label> 
                                                </div>
                                            </div>

                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;</label>                                   
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="PR" name="vacas_prenhes" id="vacas_prenhes"> Vacas Prenhas
                                                    </label> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="filtro_reproducao" hidden style="padding: 0; margin-top: 5px; border-radius: 0;"> 
                                            <label class="control-label" style="margin-left: 0px; margin-top: 5px; margin-bottom: 5px; font-size: 14px;">
                                                Diagnóstico (Atual)
                                            </label>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="col-xs-12 col-sm-3">
                                                <div class="clearfix"></div>

                                                <label class="checkbox-inline" style="margin-top: 10px;">
                                                    <input type="checkbox" id="positivo" name="positivo" value="DP" 
                                                    <?php if ($positivo=='S'){echo 'checked="checked"';}?>> Positivo
                                                </label>

                                                <label class="checkbox-inline" style="margin-top: 10px;">
                                                    <input type="checkbox" value="DN" name="negativo" id="negativo"
                                                    <?php if ($negativo=='S'){echo 'checked="checked"';}?>> Negativo
                                                </label>
                                            </div>

                                            <div class="col-xs-12 col-sm-1">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="IATF" name="iatf" id="iatf"> IATF
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-3">
                                                <label for="selectEstacao" class="sr-only">Estação de Monta</label>
                                                <select class="form-control selectpicker" multiple id="codigo_estacao_filtro" name="codigo_estacao_filtro" title="Selecione a Estação">
                                                </select>
                                            </div>

                                            <div class="col-xs-12 col-sm-3">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" value="MN" name="monta_natural" id="monta_natural"> Monta Natural
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-12">
                                                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                                    
                                                    <label style="margin-bottom: 0; margin-top: 5px;">Vacas Descarte:</label>

                                                    <label class="checkbox-inline" style="margin-top: 0;">
                                                        <input type="checkbox" id="descarte" name="descarte" value="S" 
                                                        <?php if ($descarte=='S'){echo 'checked="checked"';}?>> Sim
                                                    </label>

                                                    <label class="checkbox-inline" style="margin-top: 0;">
                                                        <input type="checkbox" value="N" name="descarte_nao" id="descarte_nao"
                                                        <?php if ($descarte=='N'){echo 'checked="checked"';}?>> Não
                                                    </label>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="previsao_parto_de_filtro" class="control-label">Previsão de Parto (de)</label>
                                                <input name="previsao_parto_de_filtro" type="date" class="form-control" id="previsao_parto_de_filtro" 
                                                <?php echo "value='".$previsao_parto_de_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="previsao_parto_ate_filtro" class="control-label">Previsão de Parto (até)</label>
                                                <input name="previsao_parto_ate_filtro" type="date" class="form-control" id="previsao_parto_ate_filtro" 
                                                >
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="data_paricao_de_filtro" class="control-label">Data de Parição (de)</label>
                                                <input name="data_paricao_de_filtro" type="date" class="form-control" id="data_paricao_de_filtro" 
                                                >
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="data_paricao_ate_filtro" class="control-label">Data de Parição (até)</label>
                                                <input name="data_paricao_ate_filtro" type="date" class="form-control" id="data_paricao_ate_filtro" 
                                                >
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_parto_de_filtro" class="control-label">Nº Partos (de)</label>
                                                <input name="num_parto_de_filtro" type="text" class="form-control" id="num_parto_de_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                >
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_parto_ate_filtro" class="control-label">Nº Partos (até)</label>
                                                <input name="num_parto_ate_filtro" type="text" class="form-control" id="num_parto_ate_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                >
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_aborto_de_filtro" class="control-label">Nº Abortos (de)</label>
                                                <input name="num_aborto_de_filtro" type="text" class="form-control" id="num_aborto_de_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                >
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_aborto_ate_filtro" class="control-label">Nº Abortos (até)</label>
                                                <input name="num_aborto_ate_filtro" type="text" class="form-control" id="num_aborto_ate_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                               >
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_natimorto_de_filtro" class="control-label">Nº Natimortos (de)</label>
                                                <input name="num_natimorto_de_filtro" type="text" class="form-control" id="num_natimorto_de_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                >
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_natimorto_ate_filtro" class="control-label">Nº Natimortos (até)</label>
                                                <input name="num_natimorto_ate_filtro" type="text" class="form-control" id="num_natimorto_ate_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                               >
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" 
                                                data-dismiss="modal" aria-label="Close" onclick="aplicar_filtros()">Aplicar Filtros</button>

                                            </div>
                                        </div>
                                    </div> <!-- Fim tab-pane active"-->
                                </div> <!-- Fim tab-content -->
                            </form>
                        </div> <!-- Fim modal body-->
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_animal_filtro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Histórico de Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p class="desc_modal">FALTA VALIDAR O CÓDIGO DO ANIMAL.</p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_1">Após digitar número, selecione o código na LISTA SUSPENSA.</span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_2">Se não aparecer o codigo na Lista Luspensa é porque o animal não existe na fazenda.</span></p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_filtro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Histórico de Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="redigita_animal_filtro()">Fechar
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
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Histórico de Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_filtro_reproducao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title">Relatório Histórico de Animais - Mensagem</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p>O Filtro de Reprodução só considera FÊMEAS <strong>&ge;</strong> 12 MESES.</p>

                                    <p>As OUTRAS categorias e os machos NÃO SERÃO EXIBIDOS.</p>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-primary" type="button" onclick="abrir_filtro_reproducao_continuar()">Com Filtros Reprodução
                            </button>

                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_filtro_reproducao()">Sem Filtros Reprodução
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
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
</script>
<script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
" charset="utf-8" type="text/javascript" >
</script>
<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>
<script src="js/relatorio_historico_animais.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

<script>
    $(document).ready(function(){
        $('#codigo_number_filtro').typeahead({
            source: function(query, result) {  
                $.ajax({
                    url:"fetch_animais.php",
                    method:"POST",
                    data:{query:query},
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

        $("#codigo_number_filtro").click(function(){
            $("#codigo_number_filtro").val('');
            document.getElementById("codigo_number_filtro").style.borderColor = "";
            return;
        });

        $("#peso_inicial_nasc_filtro").click(function(){
            $("#peso_inicial_nasc_filtro").val('');
            document.getElementById("peso_inicial_nasc_filtro").style.borderColor = "";
            return;
        });

        $("#peso_final_nasc_filtro").click(function(){
            $("#peso_final_nasc_filtro").val('');
            document.getElementById("peso_final_nasc_filtro").style.borderColor = "";
            return;
        });

        $("#peso_inicial_desmama_filtro").click(function(){
            $("#peso_inicial_desmama_filtro").val('');
            document.getElementById("peso_inicial_desmama_filtro").style.borderColor = "";
            return;
        });

        $("#peso_final_desmama_filtro").click(function(){
            $("#peso_final_desmama_filtro").val('');
            document.getElementById("peso_final_desmama_filtro").style.borderColor = "";
            return;
        });

        $("#peso_inicial_ultimo_filtro").click(function(){
            $("#peso_inicial_ultimo_filtro").val('');
            document.getElementById("peso_inicial_ultimo_filtro").style.borderColor = "";
            return;
        });

        $("#peso_final_ultimo_filtro").click(function(){
            $("#peso_final_ultimo_filtro").val('');
            document.getElementById("peso_final_ultimo_filtro").style.borderColor = "";
            return;
        });

        $("#data_nasc_inicial_filtro").click(function(){
            $("#data_nasc_inicial_filtro").val('');
            document.getElementById("data_nasc_inicial_filtro").style.borderColor = "";
            return;
        });

        $("#data_nasc_final_filtro").click(function(){
            $("#data_nasc_final_filtro").val('');
            document.getElementById("data_nasc_final_filtro").style.borderColor = "";
            return;
        });

        $("#num_parto_de_filtro").click(function(){
            $("#num_parto_de_filtro").val('');
            document.getElementById("num_parto_de_filtro").style.borderColor = "";
            return;
        });

        $("#num_parto_ate_filtro").click(function(){
            $("#num_parto_ate_filtro").val('');
            document.getElementById("num_parto_ate_filtro").style.borderColor = "";
            return;
        });

        $("#num_aborto_de_filtro").click(function(){
            $("#num_aborto_de_filtro").val('');
            document.getElementById("num_aborto_de_filtro").style.borderColor = "";
            return;
        });

        $("#num_aborto_ate_filtro").click(function(){
            $("#num_aborto_ate_filtro").val('');
            document.getElementById("num_aborto_ate_filtro").style.borderColor = "";
            return;
        });

        $("#num_natimorto_de_filtro").click(function(){
            $("#num_natimorto_de_filtro").val('');
            document.getElementById("num_natimorto_de_filtro").style.borderColor = "";
            return;
        });

        $("#num_natimorto_ate_filtro").click(function(){
            $("#num_natimorto_ate_filtro").val('');
            document.getElementById("num_natimorto_ate_filtro").style.borderColor = "";
            return;
        });

        $("#previsao_parto_de_filtro").click(function(){
            $("#previsao_parto_de_filtro").val('');
            document.getElementById("previsao_parto_de_filtro").style.borderColor = "";
            return;
        });

        $("#previsao_parto_ate_filtro").click(function(){
            $("#previsao_parto_ate_filtro").val('');
            document.getElementById("previsao_parto_ate_filtro").style.borderColor = "";
            return;
        });

        $("#data_paricao_de_filtro").click(function(){
            $("#data_paricao_de_filtro").val('');
            document.getElementById("data_paricao_de_filtro").style.borderColor = "";
            return;
        });

        $("#data_paricao_ate_filtro").click(function(){
            $("#data_paricao_ate_filtro").val('');
            document.getElementById("data_paricao_ate_filtro").style.borderColor = "";
                return;
        });
    });
</script>

</body>
</html>