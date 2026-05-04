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

  <!-- Bootstrap CSS -->
  <link href="css/jquery-ui.css" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
  <link href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css" rel="stylesheet" type="text/css" />
  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet"> 
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
</style>

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_cadastros'])) {
        $array_cadastro = explode("!",$_SESSION['menu_cadastros']);

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

    $raca = mysqli_query($conector, "select * from tabela_racas 
        where tab_registro_lixeira_raca=0
        order by tab_descricao_raca asc"); 

    $raca_filtro = mysqli_query($conector, "select * from tabela_racas 
        where tab_registro_lixeira_raca=0
        order by tab_descricao_raca asc"); 

    $pelagem = mysqli_query($conector, "select * from tabela_pelagens 
        where tab_registro_lixeira_pelagem=0
        order by tab_descricao_pelagem asc"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local_filtro_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $categoria_filtro = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $categoria_filtro_filtro = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $origem = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe>=1 and tbl_pessoa_classe<=4 and tbl_pessoa_lixeira=0
        order by tbl_pessoa_nome asc"); 

    $pai = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and 
              tbl_animal_sexo='M'
    	order by tbl_animal_codigo_numerico"); 

    $pai_filtro = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and 
              tbl_animal_sexo='M'
    	order by tbl_animal_codigo_numerico"); 

    $semem = mysqli_query($conector, "select * from tbl_semem 
        where tbl_semem_lixeira=0 and 
              tbl_semem_ativo='S'
        order by tbl_semem_nome asc"); 

    $semem_filtro = mysqli_query($conector, "select * from tbl_semem 
        where tbl_semem_lixeira=0 and 
              tbl_semem_ativo='S'
        order by tbl_semem_nome asc"); 

    $mae = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and
              tbl_animal_sexo='F'
    	order by tbl_animal_codigo_numerico"); 

    $mae_filtro = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and 
              tbl_animal_sexo='F'
    	order by tbl_animal_codigo_numerico"); 

    $origem_filtro = mysqli_query($conector, "select * from tbl_pessoa 
        where (tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and  
               tbl_pessoa_lixeira=0
        order by tbl_pessoa_nome asc");

    $array_raca = $_SESSION['raca'];
    $array_pai = $_SESSION['pai'];
    $array_mae = $_SESSION['mae'];
    $array_sexo = $_SESSION['sexo'];
    $ativo_filtro = $_SESSION['ativo'];
    $array_local = $_SESSION['local'];
    $array_origem_filtro = $_SESSION['origem'];
    $array_categoria = $_SESSION['categoria'];
    $codigo_alfa = $_SESSION['codigo_alfa'];
    $codigo_numerico = $_SESSION['codigo_numerico']; 
    $peso_inicial_nasc_filtro = $_SESSION['peso_nasc_inicial'];
    $peso_final_nasc_filtro = $_SESSION['peso_nasc_final'];
    $peso_inicial_desmama_filtro = $_SESSION['peso_desmama_inicial']=''; 
    $peso_final_desmama_filtro = $_SESSION['peso_desmama_final']=''; 
    $peso_inicial_ultimo_filtro = $_SESSION['peso_ultimo_inicial']=''; 
    $peso_final_ultimo_filtro = $_SESSION['peso_ultimo_final']=''; 
    $data_nasc_inicial_filtro = $_SESSION['data_nasc_inicial']; 
    $data_nasc_final_filtro = $_SESSION['data_nasc_final']; 
    $previsao_parto_de_filtro = $_SESSION['previsao_parto_de'];
    $previsao_parto_ate_filtro = $_SESSION['previsao_parto_ate'];
    $data_paricao_de_filtro = $_SESSION['data_paricao_de'];
    $data_paricao_ate_filtro = $_SESSION['data_paricao_ate'];
    $num_parto_de = $_SESSION['num_parto_de'];
    $num_parto_ate = $_SESSION['num_parto_ate'];
    $num_aborto_de = $_SESSION['num_aborto_de'];
    $num_aborto_ate = $_SESSION['num_aborto_ate'];
    $solteiras = $_SESSION['solteiras'];
    $prenhas = $_SESSION['prenhes'];
    $descarte = $_SESSION['descarte'];
    $paridas = $_SESSION['paridas'];
    $positivo = $_SESSION['positivo'];
    $negativo = $_SESSION['negativo'];

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
            <span class="caminho-programa">Cadastros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Animais</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Cadastro de Animais</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <!--<div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div>--> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                        <input id="lista_animais_automatico" type="hidden"
                                        <?php echo "value='".$_SESSION['lista_animais']."'";?>>

                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Dados para Consultar</legend>

                                        <div class="row ">
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local_filtro" class="control-label">Local</label>
                                                <select class="form-control selectpicker" id="codigo_local_filtro" multiple name="codigo_local_filtro">

                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($local_filtro)) { 
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

                                                <button type="button" class="form-control btn btn-info pull-right" onclick="filtros()"
                                                data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-primary pull-right" onclick="consultar()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <p class="text-muted-dark descricao_filtro" style="font-size: 12px; color:gray;"></p>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>    

                    <div id="lista_animais">
                    </div>
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
                                            <div class="col-xs-6 col-md-3">
                                                <label for="codigo_number_filtro" class="control-label">Código do Animal</label>
                                                <input name="codigo_number_filtro" type="text" class="form-control" id="codigo_number_filtro" autocomplete="off"
                                                <?php echo "value='".$codigo_numerico."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-12 col-md-12">
                                            <span class="informacao"><i class='icon_info_alt'></i> A busca pelo 'código do animal' ignora os outros filtros.</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-3 ativo">
                                                <label for="animal_ativo" class="control-label">Ativo</label>  
                                                <div class="clearfix"></div>
                                                <label class="checkbox-inline">
                                                  <input type="checkbox" id="sim_filtro" name="ativo_filtro" value="S" <?php if ($ativo_filtro=='S'){echo 'checked="checked"';}?>>Sim
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="checkbox" id="nao_filtro" name="ativo_filtro" value="N" <?php if ($ativo_filtro=='N'){echo 'checked="checked"';}?>>Não
                                                </label>
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
                                                    <input type="checkbox" value="S" name="outro" id="outro" checked="checked"> Outras Saídas
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

                                        <div class="row ">
                                            <div class="col-md-4">
                                                <label for="codigo_local_filtro_filtro" class="control-label">Local</label>
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

                                            <div class="col-md-4">
                                                <label for="codigo_origem_filtro" class="control-label">Origem</label>
                                                <select class="form-control selectpicker" data-live-search="true" multiple id="codigo_origem_filtro" name="codigo_origem_filtro" data-size="7">

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

                                            <div class="col-md-4">
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

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_local()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_origem()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_categoria()'>Limpa Seleção</a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-4 col-md-4">
                                                <label for="codigo_raca_filtro" class="control-label">Raça</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_raca_filtro" name="codigo_raca_filtro" data-size="10">

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

                                            <div class="col-xs-4 col-md-4">
                                                <label for="codigo_pai_filtro" class="control-label">Pai</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_pai_filtro" name="codigo_pai_filtro" data-size="10">

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

                                            <div class="col-xs-4 col-md-4">
                                                <label for="codigo_mae_filtro" class="control-label">Mãe</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_mae_filtro" name="codigo_mae_filtro" data-size="10">

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

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_raca()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_pai()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_mae()'>Limpa Seleção</a>
                                            </div>
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
                                                <input name="peso_inicial_nasc_filtro" type="text" class="form-control" id="peso_inicial_nasc_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                <?php echo "value='".$peso_inicial_nasc_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_nasc_filtro" class="control-label">Peso Nascimento Fim</label>
                                                <input name="peso_final_nasc_filtro" type="text" class="form-control" id="peso_final_nasc_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                <?php echo "value='".$peso_final_nasc_filtro."'";?>> 
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_desmama_filtro" class="control-label">Peso Desmama Início</label>
                                                <input name="peso_inicial_desmama_filtro" type="text" class="form-control" id="peso_inicial_desmama_filtro"            
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                <?php echo "value='".$peso_inicial_desmama_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_desmama_filtro" class="control-label">Peso Desmama Fim</label>
                                                <input name="peso_final_desmama_filtro" type="text" class="form-control" id="peso_final_desmama_filtro"
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                <?php echo "value='".$peso_final_desmama_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_inicial_ultimo_filtro" class="control-label">Último Peso Início</label>
                                                <input name="peso_inicial_ultimo_filtro" type="text" class="form-control" id="peso_inicial_ultimo_filtro"
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                <?php echo "value='".$peso_inicial_ultimo_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-4">
                                                <label for="peso_final_ultimo_filtro" class="control-label">Últmo Peso Fim</label>
                                                <input name="peso_final_ultimo_filtro" type="text" class="form-control" id="peso_final_ultimo_filtro"
                                                onkeypress = "return numeros(this, event)" maxlength="4"
                                                <?php echo "value='".$peso_final_ultimo_filtro."'";?>>
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
                                                    <input type="checkbox" value="VP" name="vacas_paridas" id="vacas_paridas" <?php if ($paridas=='S'){echo 'checked="checked"';}?>> Vacas Paridas
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-xs-6 col-md-6">
                                                <label class="control-label">&nbsp;</label>                                   
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VS" name="vacas_solteiras" id="vacas_solteiras" <?php if ($solteiras=='S'){echo 'checked="checked"';}?>> Vacas Solteiras <span style="border: none; color: #bdbbbb">&nbsp;&nbsp;(Paridas há 8 meses+ e Novilhas)</span>
                                                    </label> 
                                                </div>
                                            </div>

                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;</label>                                   
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="PR" name="vacas_prenhes" id="vacas_prenhes" <?php if ($prenhas=='S'){echo 'checked="checked"';}?>> Vacas Prenhas
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
                                                <div class="checkbox" style="margin-top: 5px; margin-bottom: 5px;"> 
                                                    <label>
                                                        <input type="checkbox" value="DC" name="descarte" id="descarte" 
                                                        <?php if ($descarte=='S'){echo 'checked="checked"';}?>> Vacas Descarte
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
                                                <?php echo "value='".$previsao_parto_ate_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="data_paricao_de_filtro" class="control-label">Data de Parição (de)</label>
                                                <input name="data_paricao_de_filtro" type="date" class="form-control" id="data_paricao_de_filtro" 
                                                <?php echo "value='".$data_paricao_de_filtro."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="data_paricao_ate_filtro" class="control-label">Data de Parição (até)</label>
                                                <input name="data_paricao_ate_filtro" type="date" class="form-control" id="data_paricao_ate_filtro" 
                                                <?php echo "value='".$data_paricao_ate_filtro."'";?>>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_parto_de_filtro" class="control-label">Nº Partos (de)</label>
                                                <input name="num_parto_de_filtro" type="text" class="form-control" id="num_parto_de_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                <?php echo "value='".$num_parto_de."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_parto_ate_filtro" class="control-label">Nº Partos (até)</label>
                                                <input name="num_parto_ate_filtro" type="text" class="form-control" id="num_parto_ate_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                <?php echo "value='".$num_parto_ate."'";?>>
                                            </div>
                                        </div>

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_aborto_de_filtro" class="control-label">Nº Abortos (de)</label>
                                                <input name="num_aborto_de_filtro" type="text" class="form-control" id="num_aborto_de_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                <?php echo "value='".$num_aborto_de."'";?>>
                                            </div>

                                            <div class="form-group col-xs-6 col-md-3">
                                                <label for="num_aborto_ate_filtro" class="control-label">Nº Abortos (até)</label>
                                                <input name="num_aborto_ate_filtro" type="text" class="form-control" id="num_aborto_ate_filtro" 
                                                onkeypress = "return numeros(this, event)" maxlength="3"
                                                <?php echo "value='".$num_aborto_ate."'";?>>
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

            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Animais - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_animais.php" enctype="multipart/form-data" id="form_gravar_animal">
                                <input name="codigo_animal" type="hidden" id="codigo_animal">
                              
                            <ul class="nav nav-tabs m-bot15">
                                <li class="active">
                                    <a data-toggle="tab" href="#dados">Dados</a>
                                </li>

                                <li class="historico">
                                    <a data-toggle="tab" href="#historicos">Históricos</a>
                                </li>

                                <li class="reprod">
                                    <a data-toggle="tab" href="#reproducao">Reprodução</a>
                                </li>

                            </ul>

                            <div class="tab-content">
                                <div id="dados" class="tab-pane active">

                                <div class="row">  
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_animais()">Confirmar Inclusão</button>

                                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal" aria-label="Close">Voltar</button>
                                    </div>
                                </div>
                                    
                                <div class="row">  
                                    <div class="form-group col-md-12">
                                        <p class="label_situacao">Situação do Animal: <span id="situacao" class="text-danger"></span></p>
                                    </div>
                                </div>

                                  <div class="row">
                                    <div class="col-md-2">
                                        <label for="alfa_animal" class="control-label">Código Alfa</label>
                                        <input name="alfa_animal" type="text" class="form-control" id="alfa_animal" maxlength="4"
                                        onkeyup="maiuscula(this)" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="codigo_numerico_animal" class="control-label"><span class="required">*</span> Número</label>
                                        <input name="codigo_numerico_animal" type="text" class="form-control" id="codigo_numerico_animal" readonly>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="sexo_animal" class="control-label"><span class="required">*</span> Sexo</label>
                                        <div class="clearfix"></div>
                                        <label class="radio-inline">
                                          <input type="radio" name="sexo_animal" id="M" class="sexo_animal" value="M">Macho
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="sexo_animal" id="F" class="sexo_animal" value="F">Fêmea
                                        </label>

                                        <p id="mudar_sexo" style="color: red; font-size: 10px">Para mudar o sexo entre em contato com o suporte técnico: (31) 99772-1904 - falecomboivirtual@gmail.com</p>
                                    </div>

                                    <div class="form-group col-md-2 reprodutor" hidden>
                                        <label class="control-label">&nbsp;</label>
                                        
                                        <div class="clearfix"></div>
                                        <label class="checkbox-inline">
                                          <input type="checkbox" name="reprodutor" id="reprodutor">Reprodutor
                                        </label>
                                    </div>

                                    <div class="col-md-3 ativo">
                                        <label for="animal_ativo" class="control-label">Ativo</label>  
                                        <div class="clearfix"></div>
                                        <label class="radio-inline">
                                          <input type="radio" name="animal_ativo" id="ativoS" value="S" disabled>Sim
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="animal_ativo" id="ativoN" value="N" disabled >Não
                                        </label>
                                    </div>
                                  </div>

                                  <div class="row form-group col-md-12"></div>

                                  <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="nascimento_animal" class="control-label"><span class="required">*</span> Data Nascimento</label>
                                        <input name="nascimento_animal" type="date" class="form-control" id="nascimento_animal" readonly>

                                        <input type="hidden" name="nascimento_anterior" id="nascimento_anterior">
                                    </div>

                                    <div class="form-group col-md-4">
                                      <label for="categoria_id" class="control-label">Categoria</label>
                                      <input name="categoria_id" type="text" class="form-control" id="categoria_id" readonly="">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="idade_animal" class="control-label">Idade</label>
                                        <input name="idade_animal" type="text" class="form-control" id="idade_animal" readonly="">
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-4">
                                      <label for="raca_id" class="control-label"><span class="required">*</span> Raça</label>
                                      <select class="form-control" required="" name="raca_id"
                                      id="raca_id">
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
                                    <div class="form-group col-md-4">
                                      <label for="pelagem_id" class="control-label">Pelagem</label>
                                      <select class="form-control" name="pelagem_id" id="pelagem_id">
                                        <option value="000">...</option>

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

                                    <div class="form-group col-md-4">
                                        <label for="grau_sangue_animal" class="control-label">Grau de Sangue</label>
                                        <input name="grau_sangue_animal" type="text" class="form-control" id="grau_sangue_animal">
                                    </div>
                                  </div>
                                  
                                  <div class="row">
                                    <div class="form-group col-md-6">
                                      <label for="local_id" class="control-label"><span class="required">*</span> Local</label>

                                      <input type="text" class="form-control" name="local_id" id="local_id" readonly>

                                      <!--<select class="form-control" required="" name="local_id" id="local_id">
                                        <option value="000000000">...</option>
                                        <?php //while($reg_local = mysqli_fetch_object($local)) { ?>

                                        <option value="<?php 
                                            //echo $reg_local->tbl_pessoa_id ?>">
                                                            
                                            <?php 
                                            //echo $reg_local->tbl_pessoa_nome;
                                            ?>
                                        </option>
                                            <?php //} ?>
                                        </select>-->
                                    </div>

                                    <div class="form-group col-md-6">
                                      <label for="origem_id" class="control-label">Origem</label>

                                      <input type="text" class="form-control" name="origem_id" id="origem_id" readonly>

                                      <!--<select class="form-control" name="origem_id" id="origem_id">
                                        <option value="000000000">...</option>

                                        <?php //while($reg_origem = mysqli_fetch_object($origem)) { ?>

                                        <option value="<?php 
                                            //echo $reg_origem->tbl_pessoa_id ?>">
                                                            
                                            <?php 
                                            //echo $reg_origem->tbl_pessoa_nome;
                                            ?>
                                        </option>
                                            <?php //} ?>
                                        </select>-->
                                    </div>
                                  </div>
                                  
                                <fieldset class="scheduler-border">
                                        <legend class="scheduler-border fonte-legend" style="color: #ccc">FILIAÇÃO</legend>                                  
                                  <!--<legend>Filiação</legend>-->
                                  
                                  <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="codigo_pai_animal" class="control-label">Pai Nº</label>

                                        <select class="form-control" id="codigo_pai_animal" name="codigo_pai_animal">

                                        <option value="000000000">...</option>

                                        <optgroup label="SEMEM">

                                        <?php while($reg_pai = mysqli_fetch_object($semem)) { ?>

                                        <option value="<?php 
                                            echo $reg_pai->tbl_semem_codigo_id ?>">
                                                        
                                            <?php 
                                            echo $reg_pai->tbl_semem_nome;
                                            ?>
                                        </option>
                                        <?php } ?>
                                        </optgroup>

										<optgroup label="TOUROS">

                                        <?php while($reg_pai = mysqli_fetch_object($pai)) { ?>

                                        <option value="<?php 
                                            echo $reg_pai->tbl_animal_codigo_id ?>">
                                                        
                                            <?php 
                                            echo $reg_pai->tbl_animal_codigo_alfa. ' ' . $reg_pai->tbl_animal_codigo_numerico;
                                            ?>
                                        </option>
                                            <?php } ?>
                                        </optgroup>
                                        
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="nome_pai_animal" class="control-label">Nome</label>
                                        <input name="nome_pai_animal" type="text" class="form-control" id="nome_pai_animal" maxlength="20">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="codigo_mae_animal" class="control-label">Mãe Nº</label>

                                        <input name="codigo_mae_animal" type="text" class="form-control" id="codigo_mae_animal" readonly>

                                        <!--<select class="form-control" id="codigo_mae_animal" name="codigo_mae_animal">

                                        <option value="000000000">...</option>

                                        <?php //while($reg_mae = mysqli_fetch_object($mae)) { ?>

                                        <option value="<?php 
                                            //echo $reg_mae->tbl_animal_codigo_id ?>">
                                                        
                                            <?php 
                                            //echo $reg_mae->tbl_animal_codigo_alfa. ' ' . $reg_mae->tbl_animal_codigo_numerico;
                                            ?>
                                        </option>
                                            <?php //} ?>
                                        </select>-->
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="nome_mae_animal" class="control-label">Nome</label>
                                        <input name="nome_mae_animal" type="text" class="form-control" id="nome_mae_animal" maxlength="20">
                                    </div>

                                  </div>
                                </fieldset>
                                  
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border fonte-legend" style="color: #ccc">PESAGEM</legend>                                  
                                  <!--<legend>Dados de Pesagem</legend>-->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="primeiro_peso_animal" class="control-label">Peso no Nascimento</label>
                                            <input name="primeiro_peso_animal" type="text" class="form-control" id="primeiro_peso_animal"
                                            readonly="">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="peso_desmama_animal" class="control-label">Peso de Desmama</label>
                                            <input name="peso_desmama_animal" type="text" class="form-control" id="peso_desmama_animal" readonly="">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="ultimo_peso_animal" class="control-label">Último Peso</label>
                                            <input name="ultimo_peso_animal" type="text" class="form-control" id="ultimo_peso_animal" readonly="">
                                        </div>
                                    </div>

                                    <div class="row" style="font-size:10px; color: darkgray;">
                                        <div class="col-md-4">
                                            <label id="pesagem_nasc" class="data_peso_nasc">Data Pesagem: 
                                            </label>
                                            <span id="data_peso_nasc" class="data_peso_nasc"></span>
                                        </div>

                                        <div class="col-md-4">
                                            <label id="pesagem_desmama" class="data_peso_desmama">Data Pesagem: 
                                            </label>
                                            <span id="data_peso_desmama" class="data_peso_desmama"></span>
                                        </div>

                                        <div class="col-md-4">
                                            <label id="pesagem_ultimo" class="data_peso_ultimo">Data Pesagem: 
                                            </label>
                                            <span id="data_peso_ultimo" class="data_peso_ultimo"></span>
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="row m-bot15">
                                    <div class="col-md-12">
                                        <label for="observacao_animal" class="control-label">Observação</label>
                                        <textarea name="observacao_animal" type="text" class="form-control" id="observacao_animal" rows="5"></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12" style="font-size: 10px;" id="informacao">
                                        <label>Incluído por: </label>
                                        <span id="incluido_por" style="color: green"></span>
                                        &nbsp; 
                                        <span id="incluido_em" style="color: green; margin-right: 10px"></span>

                                        <label id="registro_alterado"> Alterado por: </label>
                                        <span id="alterado_por" style="color: red"></span>
                                        &nbsp;
                                        <span id="alterado_em" style="color: red; margin-right: 10px"></span>
                                        <label id="registro_baixado"> Baixado por: </label>
                                        <span id="baixado_por" style="color: red"></span>
                                            &nbsp;
                                        <span id="baixado_em" style="color: red"></span>
                                    </div>
                                </div>

                                <div class="row">  
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_animais()">Confirmar Inclusão</button>

                                        <button type="button" class="btn btn-info pull-right" data-dismiss="modal" aria-label="Close">Voltar</button>
                                    </div>
                                </div>

                            </div>

                                <div id="historicos" class="tab-pane">
                                    <div class="row">  
                                        <div class="col-md-4">
                                            <h4 class="page-header-animais codigo_consulta"></h4>
                                        </div>

                                        <div class="col-md-8">
                                            <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
                                        </div>
                                    </div>

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <h6 class="compra"></h6>
                                        </div>
                                    </div>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border fonte-legend" style="color: #ccc">MOVIMENTAÇÕES</legend>

                                        <div id="lista_movimentacoes"></div>
                                    </fieldset>


                                    <!--<p class="page-header">Movimentações</p>-->

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border fonte-legend" style="color: #ccc">PESAGENS</legend>

                                        <div id="lista_pesagem"></div>
                                    </fieldset>

                                  
                                    <!--<p class="page-header">Pesagens</p>-->

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="reproducao" class="tab-pane">
                                    <div class="row">
                                        <div class="form-group col-md-8">
                                            <h4 class="page-header-animais codigo_consulta"></h4>
                                        </div>

                                        <!--<div class="col-md-4">
                                            <label class="checkbox">&nbsp;
                                                <input type="checkbox" name="descarte_reproducao" id="descarte_reproducao" disabled>&nbsp; Descartada para Reprodução
                                            </label>
                                        </div>-->

                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-12 descarte" style="font-size: 12px;">
                                            <label id="registro_descartado"> Descartado: </label>
                                            <span id="dados_descarte" style="color: red"></span>&nbsp;
                                            <span id="descarte_por" style="color: red"></span>
                                            &nbsp;
                                            <span id="descarte_em" style="color: red"></span>
                                            
                                        </div>
                                    </div>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border fonte-legend" style="color: #ccc">ESTAÇÃO DE MONTA</legend>

                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-12">
                                                <label for="estacao_monta" class="control-label">Estação de Monta</label>
                                                <select class="form-control" id="estacao_monta" name="estacao_monta">
                                                </select>
                                            </div>                                            
                                            <div class="form-group col-md-4">
                                                <label class="control-label">&nbsp;</label>
                                                <label class="checkbox">Em estação de monta
                                                    <input type="checkbox"  value="S" name="estacaoMonta" id="estacaoMonta">
                                                </label>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4 col-sm-12">
                                                <label for="num_coberturas" cl
                                                ass="control-label">N° de coberturas</label>
                                                <input name="num_coberturas" type="number" class="form-control" id="num_coberturas">
                                            </div>

                                            <div class="form-group col-md-8 coberturas">
                                                <label class="control-label">Coberturas na estação</label>
                                                <div id="lista_cobertura" style="height: 100px; overflow-y: scroll;"></div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border fonte-legend" style="color: #ccc">SITUAÇÃO ATUAL</legend>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="clearfix"></div>
                                                <label class="radio-inline">
                                                  <input type="radio" name="situacao_atual" id="parida" value="P">Parida
                                                </label>
                                                <label class="radio-inline">
                                                  <input type="radio" name="situacao_atual" id="solteira" value="S">Solteira
                                                </label>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border fonte-legend" style="color: #ccc">HISTÓRICOS</legend>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="control-label">N° de partos</label>
                                                <input name="num_partos" type="number" class="form-control m-bot15" id="num_partos">
                                            </div>

                                            <div class="form-group col-md-10 nascimentos">
                                                <label class="control-label">Nascimentos</label>
                                                <div id="lista_partos" style="height: 100px; overflow-y: scroll;"></div>
                                            </div>
                                        </div> 

                                        <div class="row">&nbsp;</div>

                                        <div class="row">   
                                            <div class="form-group col-md-2">
                                                <label for="num_abortos" class="control-label">N° de abortos</label>
                                                <input name="num_abortos" type="number" class="form-control m-bot15" id="num_abortos">
                                            </div>
                                            <div class="form-group col-md-10 abortos">
                                                <label class="control-label">Abortos</label>
                                                <div id="lista_abortos" style="height: 100px; overflow-y: scroll;"></div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_animais()">Confirmar Inclusão</button>
                                            <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="registros" class="tab-pane">
                                    <div class="row">  
                                        <div class="form-group col-md-4">
                                            <h4 class="page-header-animais codigo_consulta"></h4>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="nome_registro_animal" class="control-label">Nome</label>
                                            <input name="nome_registro_animal" type="text" class="form-control m-bot15" id="nome_registro_animal">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="ren_animal" class="control-label">Registro de Nascimento - REN</label>
                                            <input name="ren_animal" type="text" class="form-control m-bot15" id="ren_animal">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="rgd_animal" class="control-label">Registro Definitivo - RGD</label>
                                            <input name="rgd_animal" type="text" class="form-control m-bot15" id="rgd_animal">
                                        </div>
                                    </div>
                                  
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="sisbov_animal" class="control-label">SISBOV</label>
                                            <input name="sisbov_animal" type="text" class="form-control m-bot15" id="sisbov_animal">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="certificadora_animal" class="control-label">Certificadora</label>
                                            <input name="certificadora_animal" type="text" class="form-control m-bot15" id="certificadora_animal">
                                        </div>
                                    </div>

                                    <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_animais()">Confirmar Inclusão</button>
                                            <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
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
                            <h4 class="modal-title">Animais </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="incluir_novo();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_edicao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Animais </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="sair_inclusao();">Fechar</button>
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
                            <h4 class="modal-title">Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
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
                            <h4 class="modal-title">Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="redigita_animal_filtro()">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_pasto" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="cadastro_pasto();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_animal_filtro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Animais - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p class="desc_modal">FALTA VALIDAR O CÓDIGO DO ANIMAL.</p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_1">Após digitar número, selecione o código na LISTA SUSPENSA.</span></p>

                                    <p style="margin-left: 15px;">
                                        <span class="desc_modal_2">Se não aparecer o codigo na Lista Suspensa é porque o animal não existe na fazenda.</span></p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="redigita_animal_filtro()">Fechar
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
                            <h4 class="modal-title">Animais - Mensagem</h4>
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
            <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2025</p></font>
        </div>
    </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->
<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript"></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
<script src="js/select-1.13.14.js?<?php echo Versao; ?>"></script>
<script src="js/typeahead.js"></script>
<script src="js/tabela_animais.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

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

