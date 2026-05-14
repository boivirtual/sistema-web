<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");

    if (isset($_REQUEST['local']) && $_REQUEST['local']!='') {
        $codigo_local = $_REQUEST['local'];
        @ session_start(); 
        $_SESSION['local_pastos'] = $codigo_local;
    }
    else {
        $codigo_local = '';
    }

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

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_parametros'])) {
        $array_gestao_adm = explode("!",$_SESSION['menu_parametros']);

        if ($array_gestao_adm[3] == 0){
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

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $local_importar = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_modulo = mysqli_query($conector, "select * from tbl_modulo_pasto where tbl_modulo_lixeira=0 order by tbl_modulo_descricao ASC"); 

    $tbl_capim = mysqli_query($conector, "select * from tbl_tipo_capim where tbl_tipo_capim_lixeira=0"); 

    $array_local = $_SESSION['local_pastos'];
    $controle_estoque = $_SESSION['controle_estoque'];

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
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Parametros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Mapa/Pastos</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-map-o"></i> Mapa/Pastos</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Importar Mapa" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_contas.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Dados para Consultar</legend>

                                        <div class="row">    
                                            <div class="form-group col-md-6">
                                                <input type="hidden" name="controle_estoque" id="controle_estoque"
                                                <?php echo "value='".$controle_estoque."'";?>>

                                                <label for="codigo_local_filtro" class="control-label"><span class="required">*</span> Selecione a Fazenda</label>
                                                <select class="form-control" id="codigo_local_filtro"  name="codigo_local_filtro">

                                                <option value="000000000">...</option>    

                                                <?php while($reg_local = mysqli_fetch_object($local_filtro)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_local->tbl_pessoa_id ?>"

                                                    <?php 

                                                        if ($array_local!="") {
                                                            if ($array_local==$reg_local->tbl_pessoa_id) {
                                                                echo "selected";       
                                                            }
                                                        }
                                                        else {
                                                            if ($codigo_local==$reg_local->tbl_pessoa_id) { 
                                                                echo "selected";       
                                                            }
                                                        }
                                                    ?>>
                                                        
                                                    <?php 
                                                        echo $reg_local->tbl_pessoa_nome;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>
                                            </div>

                                                <div class="form-group col-md-2">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="listar_pastos()">Consultar</button>
                                                </div>

                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="lista_pastos">
                    </div>

                </div>
            </div>
            <!-- page end-->


            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Pastos - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_pastos.php" enctype="multipart/form-data" id="form_gravar_pasto">
                                <input name="codigo_conta" type="hidden" id="codigo_conta">
                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">

                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">
                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_pasto()">Confirmar Inclusão</button>

                                                <button type="button" class="btn btn-info pull-right voltar_inclusao" onClick="voltar_inclusao()">Voltar</button>

                                                <button type="button" class="btn btn-info pull-right voltar" data-dismiss="modal">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row mens_descricao" style="padding-top: 10px;">
                                            <div class="form-group col-md-12" style="color: red;">
                                                <p>Altere a Descrição  somente aqui.&nbsp;&nbsp;&nbsp;  
                                                <span>
                                                As áreas comuns criadas deverão ser associadas ao MÓDULO ÁREA COMUM. 
                                                </span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="codigo_local" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" id="codigo_local"name="codigo_local">
                                                <option value="000000000">...</option>

                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($local)) { 
                                                    
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

                                                <input type="text" class="form-control" id="local_readonly" readonly>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="modulo" class="control-label"><span class="required">*</span> Módulo</label>
                                                <select class="form-control" id="modulo"name="modulo">
                                                <option value="000">...</option>

                                                <?php while($reg_mod = mysqli_fetch_object($tbl_modulo)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_mod->tbl_modulo_id ?>">
                                                        
                                                    <?php 
                                                        echo $reg_mod->tbl_modulo_descricao;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>

                                                <input type="text" class="form-control" id="modulo_readonly" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="descricao" class="control-label"><span class="required">*</span> Descrição</label>
                                                 <input name="descricao" type="text" class="form-control" id="descricao" onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="capim" class="control-label">Tipo de Forragem</label>
                                                <select class="form-control" id="capim"name="capim">
                                                <option value="000">...</option>

                                                <?php while($reg_capim = mysqli_fetch_object($tbl_capim)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_capim->tbl_tipo_capim_id  ?>">
                                                        
                                                    <?php 
                                                        echo $reg_capim->tbl_tipo_capim_descricao;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>

                                                <input type="text" class="form-control" id="capim_readonly" readonly>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="area" class="control-label">Área (ha)</label>
                                                <input name="area" type="text" class="form-control" id="area" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_area()">
                                            </div>
                                        </div>

                                                
                                        <!--<div class="row" >        
                                           <div class="form-group col-md-4">
                                                <label for="latitude" class="control-label">Latitude</label>
                                                <input name="latitude" type="text" class="form-control" id="latitude">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="logitude" class="control-label">Longitude</label>
                                                <input name="logitude" type="text" class="form-control" id="logitude">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="area" class="control-label">Área (ha)</label>
                                                <input name="area" type="text" class="form-control" id="area" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_area()">
                                            </div>
                                        </div>-->

                                        <fieldset class="scheduler-border" id="servico_propriedade"> 
                                            <legend class="scheduler-border fonte-legend">Categoria/Qtd Animais</legend>


                                            <div class="row">        
                                                <div class="form-group col-md-3">
                                                    <label for="descricao_categoria" class="control-label">Categoria</label>
                                                </div>

                                                <div class="form-group col-md-3 label-center">
                                                    <label for="qtd_categoria">Quantidade</label>
                                                </div>

                                                <?php
                                                    if ($controle_estoque == 'I') {
                                                        echo '<div class="form-group col-md-3 text-center">
                                                            <label class="control-label">Machos
                                                            </label>
                                                            </div>

                                                            <div class="form-group col-md-3 text-center">
                                                            <label class="control-label" style="text-align: center">Fêmeas</label>
                                                            </div>';
                                                    }
                                                    else {
                                                        echo '<div class="form-group col-md-3 text-center">
                                                            <label for="idade_qtde" class="control-label">Idade em meses/Qtde</label>
                                                            </div>

                                                            <div class="form-group col-md-3 text-center">
                                                            <label for="qtd_categoria" class="control-label" style="text-align: center">Nascimento/Sexo</label>
                                                            </div>';
                                                    }
                                                ?>
                                            </div> 
                            
                                            <?php
                                                $ssql = "SELECT * FROM tabela_categoria_idade 
                                                    WHERE tab_registro_lixeira_categoria_idade='0'"; 
                                                $rs = mysqli_query($conector,$ssql); 
                                                while ($fila = mysqli_fetch_object($rs)){
                                                    $codigo_id = $fila->tab_codigo_categoria_idade ;
                                                    $idade_de = $fila->tab_categoria_idade_de;
                                                    $idade_ate = $fila->tab_categoria_idade_ate;

                                                    if ($idade_ate==999999999){
                                                        $desc_categoria = '> 36 meses';
                                                    }
                                                    else {
                                                        $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                                                    }
                                                    echo "
                                                        <div class='row'>
                                                            <div class = 'form-group col-md-3'>
                                                                <input name='codigo_categoria' type='hidden' value='{$codigo_id}'>
                                                                <span>{$desc_categoria}</span>
                                                            </div>
                                                            <div class = 'form-group col-md-3'>
                                                                <span name='qtdeAnimaisPasto'>0</span>
                                                            </div>";

                                                    if ($controle_estoque=='I') {
                                                        echo "<div class = 'form-group col-md-3'>
<input name='qtd_categoria_macho' type='text' class='form-control qtd_categoria' onkeypress = 'return numeros(this, event)'>
<input name='qtd_macho_anterior' type='hidden'>
                                                            </div>

                                                            <div class = 'form-group col-md-3'>
<input name='qtd_categoria_femea' type='text' class='form-control qtd_categoria' onkeypress = 'return numeros(this, event)'>
<input name='qtd_femea_anterior' type='hidden'>
                                                            </div>
                                                        </div>
                                                        ";
                                                    }   
                                                    else {
                                                        echo "<div class = 'form-group col-md-3'>
                                                                <select class='form-control' id='selectIdade{$codigo_id}' onchange='preencherNascimento(this.id, this.value);'>
                                                                    <option value='N'>...</option>
                                                                </select>
                                                            </div>
                                                            <div class = 'form-group col-md-3'>
                                                                <select class='form-control' id='selectNascimento{$codigo_id}' onchange='editarAnimal(this.value);'>
                                                                    <option value='0'>...</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        ";
                                                    }     
                                                }
                                            ?> 

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="observacao" class="control-label">Observação</label>
                                                     <input name="observacao" type="text" class="form-control" id="observacao" onkeyup="maiuscula(this)" maxlength="200">
                                                </div>
                                            </div>

                                            <input name="array_codigo_categoria" type="hidden" id="array_codigo_categoria" >

                                            <input name="array_qtd_categoria_macho" type="hidden" id="array_qtd_categoria_macho" >

                                            <input name="array_qtd_macho_anterior" type="hidden" id="array_qtd_macho_anterior" >

                                            <input name="array_qtd_categoria_femea" type="hidden" id="array_qtd_categoria_femea" >

                                            <input name="array_qtd_femea_anterior" type="hidden" id="array_qtd_femea_anterior" >

                                            <input name="descricao_anterior" type="hidden" id="descricao_anterior" >

                                        </fieldset>

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_pasto()">Confirmar Inclusão</button>

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

            <div class="modal fade" id="modal_importar_mapa" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Pastos - Importar Mapa</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="importar_pastos.php" enctype="multipart/form-data" id="gravar_mapa">

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">
                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-info pull-right voltar_inclusao" onClick="voltar_inclusao()">Voltar</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="codigo_local_importar" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" id="codigo_local_importar"name="codigo_local_importar" required>
                                                <option value="">...</option>

                                                <?php 
                                                    while($reg_local = mysqli_fetch_object($local_importar)) { 
                                                    
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

                                            <div class="form-group col-md-6">
                                                <label for="arquivo_excel"><span class="required">*</span> Selecione o arquivo kml</label>
                                                <input type="file" class="form-control-file" name="arquivo_kml" required>       
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="submit" class="btn btn-primary gravar" id="botao_gravar">Confirmar</button>
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
                            <h4 class="modal-title">Pastos </h4>
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
                            <h4 class="modal-title">Pastos </h4>
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
                            <h4 class="modal-title">Pastos - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalEditarAnimal" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pastos - Editar Animal</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="idAnimal" id="idAnimal">
                                <div class="form-group col-md-6">
                                    <label for="nascimentoAnimal" class="control-label">Data de nascimento:</label>
                                    <input type="date" name="nascimentoAnimal" id="nascimentoAnimal" class='form-control'>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="sexoAnimal">Sexo:</label>
                                    <select name="sexoAnimal" id="sexoAnimal" class='form-control'>
                                        <option value='0'>...</option>
                                        <option value="F">F</option>
                                        <option value="M">M</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">  
                                <div class="form-group col-md-12">
                                    <button type="button" class="btn btn-primary confirma_gravar" onClick="gravarAnimal()">Confirmar Inclusão</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

<?php 
  $javascript_file_name = 'tabela_pastos.js';
  require 'rodape.php';
?>
