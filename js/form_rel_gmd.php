<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
    $data_sistema = date("Y-m-d");

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
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay"  rel="stylesheet" crossorigin="anonymous">

  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
  <link href="css/tabela_1300.css" rel="stylesheet">
  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

   @ session_start();   
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

    $local = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0"); 

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

    $controle_estoque = $_SESSION['controle_estoque'];

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php";
    ?>
    <!--sidebar end-->

  <!-- container section start -->
  <section id="container" class="">

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Ganho de Peso</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Ganho de Peso</h3>
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
                                                    <input type="hidden" id="controle_estoque" <?php echo "value='".$controle_estoque."'";?>>

                                                    <input type="hidden" id="rel_gmd" value="rel_gmd">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="onclick=voltar_relatorios_gmd()">Voltar
                                                    </button>
                                                </div>
                                            </div>    
                                                
                                            <div class="row ">
                                                <div class="form-group col-md-3">
                                                    <input type="hidden" name="lista_ao_entrar" id="lista_ao_entrar" value="S">  
                                                    
                                                    <label for="data_inicial" class="control-label"><span class="required">*</span> Período da Pesagens - Inicial</label>
                                                    <input name="data_inicial" type="month" class="form-control" id="data_inicial">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="data_final" class="control-label"><span class="required">*</span> Final</label>
                                                    <input name="data_final" type="month" class="form-control" id="data_final">
                                                </div>

                                                <?php
                                                    if ($controle_estoque=='I') :
                                                ?>
                                                    <div class="form-group col-md-4">
                                                        <label for="tipo_rel" class="control-label"><span class="required">*</span> Tipo do Relatório</label>
                                                    </div>

                                                    <div class="form-group col-md-5">
                                                        <label class="radio-inline tipo_rel">
                                                          <input type="radio" name="tipo_rel" value="C"
                                                          <?php //if ($tipo_rel == 'A'){echo "checked";}?>> Por Categoria
                                                        </label>
                                                        <label class="radio-inline tipo_rel">
                                                          <input type="radio" name="tipo_rel" value="I"<?php //if ($tipo_rel == 'S'){echo "checked";}?>> Por Indivíduo
                                                        </label>
                                                    </div>  
                                                    </a>
                                                <?php
                                                    endif;
                                                ?>

                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-5 local">
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

                                                <div class="form-group col-md-2">
                                                    <label class="control-label">&nbsp;</label>

                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="filtros_gmd()"
                                                    data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-primary pull-right" onclick="listar_gmd()">Listar
                                                    </button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                        </div> 
                        
                    <div id="lista_gmd"></div>
    	        </div>
	        </div>

	        <!-- page end-->

            <div class="modal fade" id="modal_filtros" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title" id="modal_incluirLabel">Filtros</h3>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" id="form_filtrar">
                              
                                <div class="tab-content">
                                    <div class="tab-pane active">
                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>

                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" onclick="listar_gmd()">Aplicar Filtros</button>
                                                <button type="button" class="btn btn-info pull-right " onclick="limpar_filtros()">Limpar Filtros</button>
                                            </div>
                                        </div>

                                        <div class="row filtro_codigo_animal">
                                            <div class="col-xs-6 col-md-2">
                                                <label for="codigo_alfa_filtro" class="control-label">Código Alfa</label>
                                                <input name="codigo_alfa_filtro" type="text" class="form-control" id="codigo_alfa_filtro" maxlength="4" onkeyup="maiuscula(this)"
                                                <?php echo "value='".$codigo_alfa."'";?>>
                                            </div>

                                            <div class="col-xs-6 col-md-3">
                                                <label for="codigo_numerico_filtro" class="control-label">Código Numérico</label>
                                                <input name="codigo_numerico_filtro" type="text" class="form-control" id="codigo_numerico_filtro"
                                                <?php echo "value='".$codigo_numerico."'";?>>
                                            </div>
                                        </div>

                                        <div class="row filtro_codigo_animal">
                                            <div class="form-group col-xs-12 col-md-12">
                                            <span class="informacao"><i class='icon_info_alt'></i> A busca pelo 'código do animal' ignora os outros filtros.</span>
                                            </div>
                                        </div>

                                        <?php
                                            endif;
                                        ?>

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
                                                            echo '<input type="checkbox" 
                                                                checked="checked"
                                                                value="S" name="sim_filtro" id="sim_filtro"> Sim';
                                                        }
                                                    }                       
                                                }
                                                else {
                                                    echo '<input type="checkbox"
                                                          checked="checked"
                                                          value="S" name="sim_filtro" id="sim_filtro"> Sim';
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
                                                            echo '<input type="checkbox"
                                                                checked="checked"  
                                                                value="N"
                                                                name="nao_filtro" 
                                                                id="nao_filtro"> Não';
                                                        }
                                                    }                       
                                                }
                                                else {
                                                    echo '<input type="checkbox" 
                                                    checked="checked"
                                                    value="N" name="nao_filtro" 
                                                    id="nao_filtro"> Não';
                                                    }
                                                ?>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-5 situacao">
                                                <label class="control-label">Situações p/ Ativos Não</label>
                                                <div class="clearfix"></div>

                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="V" name="vendido" id="vendido" checked="checked"> Vendidos
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

                                            <?php
                                                if ($controle_estoque=='I') :
                                            ?>

                                            <div class="form-group col-md-4">
                                                <label for="codigo_origem_filtro" class="control-label">Origem</label>
                                                <select class="form-control selectpicker" data-live-search="true" multiple id="codigo_origem_filtro" name="codigo_origem_filtro">

                                                <option value="0">...</option>  

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

                                            <?php
                                                endif;
                                            ?>

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

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="codigo_raca_filtro" class="control-label">Raça</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_raca_filtro" name="codigo_raca_filtro" data-size="6">

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

                                            <?php
                                                if ($controle_estoque=='I') :
                                            ?>

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="codigo_pai_filtro" class="control-label">Pai</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_pai_filtro" name="codigo_pai_filtro">

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
                                                        echo $reg_pai->tbl_semem_codigo_alfa;
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
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_mae_filtro" name="codigo_mae_filtro">

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

                                            <?php
                                                endif;
                                            ?>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="data_nasc_inicial_filtro" class="control-label">Nascimento Início</label>
                                                <input name="data_nasc_inicial_filtro" type="date" class="form-control" id="data_nasc_inicial_filtro" 
                                                <?php echo "value='".$data_nasc_inicial_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="data_nasc_final_filtro" class="control-label">Nascimento Fim</label>
                                                <input name="data_nasc_final_filtro" type="date" class="form-control" id="data_nasc_final_filtro" 
                                                <?php echo "value='".$data_nasc_final_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_nasc_filtro" class="control-label">Peso Nascimento Início</label>
                                                <input name="peso_inicial_nasc_filtro" type="text" class="form-control" id="peso_inicial_nasc_filtro" placeholder="0.00" onkeypress="digita_valor()" onblur="peso_inicial_nasc_filtro()" 
                                                <?php echo "value='".$peso_inicial_nasc_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_nasc_filtro" class="control-label">Peso Nascimento Fim</label>
                                                <input name="peso_final_nasc_filtro" type="text" class="form-control" id="peso_final_nasc_filtro" placeholder="0.00" onkeypress="digita_valor()" onblur="peso_final_nasc_filtro()" 
                                                <?php echo "value='".$peso_final_nasc_filtro."'";?>> 
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_desmama_filtro" class="control-label">Peso Desmama Início</label>
                                                <input name="peso_inicial_desmama_filtro" type="text" class="form-control" id="peso_inicial_desmama_filtro" placeholder="0.00" onkeypress="digita_valor()" onblur="peso_inicial_desmama_filtro()" 
                                                <?php echo "value='".$peso_inicial_desmama_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_desmama_filtro" class="control-label">Peso Desmama Fim</label>
                                                <input name="peso_final_desmama_filtro" type="text" class="form-control" id="peso_final_desmama_filtro" placeholder="0.00" onkeypress="digita_valor()" onblur="peso_final_desmama_filtro()" 
                                                <?php echo "value='".$peso_final_desmama_filtro."'";?>>
                                            </div>
                                        </div>

                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_ultimo_filtro" class="control-label">Último Peso Início</label>
                                                <input name="peso_inicial_ultimo_filtro" type="text" class="form-control" id="peso_inicial_ultimo_filtro" placeholder="0.00" onkeypress="digita_valor()" onblur="peso_inicial_ultimo_filtro()" 
                                                <?php echo "value='".$peso_inicial_ultimo_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_ultimo_filtro" class="control-label">Últmo Peso Fim</label>
                                                <input name="peso_final_ultimo_filtro" type="text" class="form-control" id="peso_final_ultimo_filtro" placeholder="0.00" onkeypress="digita_valor()" onblur="peso_final_ultimo_filtro()" 
                                                <?php echo "value='".$peso_final_ultimo_filtro."'";?>>
                                            </div>
                                        </div>

                                        <h3>Filtros Reprodução</h3>
                                        <hr>
                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="previsao_parto_de_filtro" class="control-label">Previsão do Parto (de)</label>
                                                <input name="previsao_parto_de_filtro" type="date" class="form-control" id="previsao_parto_de_filtro" 
                                                <?php echo "value='".$previsao_parto_de_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="previsao_parto_ate_filtro" class="control-label">Previsão do Parto (até)</label>
                                                <input name="previsao_parto_ate_filtro" type="date" class="form-control" id="previsao_parto_ate_filtro" 
                                                <?php echo "value='".$previsao_parto_ate_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_parto_de_filtro" class="control-label">Nº Partos (de)</label>
                                                <input name="num_parto_de_filtro" type="number" class="form-control" id="num_parto_de_filtro" 
                                                <?php echo "value='".$num_parto_de_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_parto_ate_filtro" class="control-label">Nº Partos (até)</label>
                                                <input name="num_parto_ate_filtro" type="number" class="form-control" id="num_parto_ate_filtro" 
                                                <?php echo "value='".$num_parto_ate_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_aborto_de_filtro" class="control-label">Nº Abortos (de)</label>
                                                <input name="num_aborto_de_filtro" type="number" class="form-control" id="num_aborto_de_filtro" 
                                                <?php echo "value='".$num_aborto_de_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_aborto_ate_filtro" class="control-label">Nº Abortos (até)</label>
                                                <input name="num_aborto_ate_filtro" type="number" class="form-control" id="num_aborto_ate_filtro" 
                                                <?php echo "value='".$num_aborto_ate_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;</label>                       
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VP" name="vacas_paridas" id="vacas_paridas" <?php if ($paridas=='S'){echo 'checked="checked"';}?>> Vacas Paridas
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group col-xs-3 col-md-3">
                                                <label class="control-label">Paridas até</label>
                                                <input type="date" name="paridas_ate" id="paridas_ate" class="form-control" <?php echo "value='".$data_paridas_ate."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-6">
                                                <label class="control-label">&nbsp;</label>                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VS" name="vacas_solteiras" id="vacas_solteiras" <?php if ($solteiras=='S'){echo 'checked="checked"';}?>> Solteiras <span style="border: none; color: #bdbbbb">&nbsp;&nbsp;(Paridas há 8 meses+ e Novilhas)</span>
                                                    </label> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-4 col-md-3">
                                                <label class="control-label">Diagnóstico</label>  

                                                 <div class="">
                                                    <label>
                                                    <input type="checkbox" id="positivo" name="positivo" value="DP" <?php if ($positivo=='S'){echo 'checked="checked"';}?>>&nbsp; Positivo
                                                    </label>

                                                    <label class="control-label">&nbsp;</label>  

                                                    <label>
                                                    <input type="checkbox" value="DN" name="negativo" id="negativo" <?php if ($negativo=='S'){echo 'checked="checked"';}?>>&nbsp; Negativo
                                                    </label>
                                                 </div>
                                            </div>

                                            <div class="form-group col-xs-4 col-md-3">
                                                <label class="control-label">Estação de Monta</label>
                                                <select class="form-control" id="codigo_estacao_filtro" name="codigo_estacao_filtro">
                                                </select>
                                            </div>

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label class="control-label">&nbsp;</label> 

                                                 <div class="checkbox">
                                                    <label class="control-label">&nbsp;</label>  

                                                    <label>
                                                    <input type="checkbox" value="DC" name="descarte" id="descarte" <?php if ($descarte=='S'){echo 'checked="checked"';}?>> Descarte
                                                    </label>
                                                 </div>
                                            </div>
                                        </div>

                                        <?php
                                            endif;
                                        ?>

                                        </div> <!-- Fim outros Filtros-->

                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" onclick="listar_gmd()">Aplicar Filtros</button>
                                                <button type="button" class="btn btn-info pull-right " onclick="limpar_filtros()">Limpar Filtros</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                            <h4 class="modal-title">Relatório Ganho de Peso - Mensagem</h4>
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
<script src="js/tabela_animais.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
</script>

<script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
" charset="utf-8" type="text/javascript" >
</script>
<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>


