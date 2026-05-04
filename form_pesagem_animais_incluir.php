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

    $tbl_epoca_pesagem_lote = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 

    $local_filtro = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $categoria_filtro = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $raca_filtro = mysqli_query($conector, "select * from tabela_racas 
        where tab_registro_lixeira_raca=0
        order by tab_descricao_raca asc"); 

    $pai_filtro = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and 
              tbl_animal_ativo='S' and 
              tbl_animal_sexo='M'
        order by tbl_animal_codigo_numerico"); 

    $semem_filtro = mysqli_query($conector, "select * from tbl_semem 
        where tbl_semem_lixeira=0 and 
              tbl_semem_ativo='S'
              order by tbl_semem_nome asc"); 

    $mae_filtro = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and 
              tbl_animal_ativo='S' and 
              tbl_animal_sexo='F'
        order by tbl_animal_codigo_numerico"); 

    $origem_filtro = mysqli_query($conector, "select * from tbl_pessoa 
        where (tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and 
               tbl_pessoa_lixeira=0
        order by tbl_pessoa_nome asc");

    $previsao_parto_de_filtro = '';
    $previsao_parto_ate_filtro = '';
    $num_parto_de_filtro = '';
    $num_parto_ate_filtro = '';
    $num_aborto_de_filtro = '';
    $num_aborto_ate_filtro = '';
    $data_parida = '';

    $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

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
    $data_paridas_ate = $_SESSION['data_paridas_ate'];
    $positivo = $_SESSION['positivo'];
    $negativo = $_SESSION['negativo'];

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

    $data_sistema = date("Y-m-d");

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
                                                <label for="epoca_pesagem" class="control-label"><span class="required">*</span> Motivo da Pesagem</label>

                                                <select class="form-control" name="epoca_pesagem_filtro" id="epoca_pesagem_filtro">

                                                <option value="000" selected disabled>...</option>

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

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right filtros" onclick="filtros()"
                                                data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
                                            </div>
                                        </div>

                                        <?php
                                            if ($controle_estoque=='L') :
                                        ?>

                                        <div class="row">
                                            <div class="form-group col-md-3 pasto">
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

                                        <?php
                                            endif;
                                        ?>

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

                            <div class="tab-panel" id="itens" hidden="">
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
                            </div>

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

                                                       <!-- <div class="row">
                                                            <div class="col-md-8">
                                                                <p id="descricao_lote_pesagem_lote" class="text-primary"></p>
                                                                <input type="hidden" name="descricao_lote_pesagem_lote" class="descricao_lote_pesagem_lote">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <p id="data_pesados_pesagem_lote" class="text-primary"></p>
                                                            </div>
                                                        </div>-->



                                                        <div class="row">
                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Data </label>
                                                                <input class="form-control data_pesagem" type="date" name="data_pesagem">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <label class="control-label"><span class="required">*</span> Motivo da Pesagem </label>

                                                            <select class="form-control" name="epoca_pesagem_lote" id="epoca_pesagem_lote">

                                                            <?php 

                                                            while($reg_ep = mysqli_fetch_object($tbl_epoca_pesagem_lote)) { ?>

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

                                                                <input class="form-control descricao_lote_lote" type="text" name="descricao_lote_lote" id="descricao_lote_lote" maxlength="50"
                                                                onkeyup="maiuscula(this)">
                                                            </div>
                                                        </div>









                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <span class="text-primary total_a_pesar_pesagem_lote"></span>
                                                                <input type="hidden" name="total_a_pesar_pesagem_lote" class="total_a_pesar_pesagem_lote">
                                                            </div>
                                                        
                                                            <div class="col-md-3">
                                                                <span class="text-primary total_pesados_pesagem_lote"></span>
                                                                <input type="hidden" name="total_pesados_pesagem_lote" id="total_pesados_pesagem_lote" class="total_pesados_pesagem_lote">
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <span class="text-primary peso_total_kg_pesagem_lote"></span>
                                                                <input type="hidden" name="peso_total_kg_pesagem_lote" class="peso_total_kg_pesagem_lote">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <span class="text-primary peso_total_arroba_pesagem_lote"></span>
                                                                <input type="hidden" name="peso_total_arroba_pesagem_lote" class="peso_total_arroba_pesagem_lote">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <span class="text-primary peso_medio_kg_pesagem_lote"></span>
                                                                <input type="hidden" name="peso_medio_kg_pesagem_lote" class="peso_medio_kg_pesagem_lote">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <span class="text-primary peso_medio_arroba_pesagem_lote"></span>
                                                                <input type="hidden" name="peso_medio_arroba_pesagem_lote" class="peso_medio_arroba_pesagem_lote">
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

            <div class="modal fade" id="modal_pesar_individual" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <!--    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                            <h4 class="modal-title" id="modal_incluirLabel">Pesagem - Individual - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <input name="codigo_id" type="hidden" id="codigo_id" value="0">
                                <input name="sexo_animal" type="hidden" id="sexo_animal">
                                <input name="nascimento_animal" type="hidden" id="nascimento_animal">
                                <input name="raca_animal" type="hidden" id="raca_animal">
                                <input name="pelagem_animal" type="hidden" id="pelagem_animal">
                                <input name="mae_animal" type="hidden" id="mae_animal">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" id="alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <p class="text-muted-dark descricao_filtro_modal" style="font-size: 12px; color:#b5b3b3;"></p>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="lote" class="control-label"><span class="required">*</span> Descrição do Lote</label>
                                                <input name="lote" type="text" class="form-control" id="lote" maxlength="50"
                                                onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_a_pesar" class="control-label"><span class="required">*</span> Animais para Pesar</label>
                                                <input name="qtd_a_pesar" type="number" class="form-control" id="qtd_a_pesar">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_pesado" class="control-label">Animais Pesados</label>
                                                <input name="qtd_pesado" type="number" class="form-control" id="qtd_pesado" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="codigo_number_filtro" class="control-label"><span class="required">*</span> Id Animal</label>
                                                <input name="codigo_number_filtro" type="text" class="form-control" id="codigo_number_filtro" autocomplete="off">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="peso_animal" class="control-label"><span class="required">*</span> Peso (Kg)</label>
                                                <input name="peso_animal" type="number" class="form-control" id="peso_animal" onkeypress = "return desabilita_enter (this, event)">
                                            </div>

                                            <div class="form-group col-md-5">
                                                <label for="observacao" class="control-label">Observação</label>
                                                <input name="observacao" type="text" class="form-control" id="observacao" maxlength="100"
                                                onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-2" id="incluir">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" id="btn_salvar" onClick="Salvar()">Confirmar</button>
                                            </div>

                                            <div class="form-group col-md-2" id="editar" hidden="" >
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="Salvar_editar()">Confirmar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <p id="descricao_animal" class="text-primary"></p>
                                            </div>
                                            <div class="form-group col-md-12" style="text-align: center;">
                                                <span id="ultimo_peso" class="text-success" style="font-size: 20px; font-weight: 600;"></span>
                                                <span id="desc_descarte" class="text-danger" style="font-size: 20px; font-weight: 600;"></span>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-primary" onclick="pausar_pesagem()">Pausar Pesagem</button>
                                            </div>
                                        </div>

                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_pesar_estimada" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Pesagem - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_pesagem_lote.php" enctype="multipart/form-data" id="form_gravar_pesagem_lote">

                                <input name="numero_pesagem_lote" type="hidden" id="numero_pesagem_lote">
                                <input  name="tipo_gravacao_lote" type="hidden" id="tipo_gravacao_lote" value="1">

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
                                                <input name="descricao_pasto" type="text" class="form-control" id="descricao_pasto" readonly="">
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

                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>

                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" 
                                                data-dismiss="modal" aria-label="Close" onclick="aplicar_filtros()">Aplicar Filtros</button>

                                                <a href="#" class="pull-right" onclick="limpar_filtros(),aplicar_filtros()">Limpar Filtros</a>

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
                                                  <input type="checkbox" id="sim_filtro" name="ativo_filtro" value="S" checked disabled>Sim
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
                                                <label for="codigo_local_filtro" class="control-label">Fazenda</label>
                                                <select class="form-control" id="codigo_local_filtro" name="codigo_local_filtro">

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

                                            <?php
                                                if ($controle_estoque=='I') :
                                            ?>

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

                                            <?php
                                                endif;
                                            ?>

                                            <div class="col-md-4">
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
                                        </div>


                                        <div class="row">
                                            <?php
                                                if ($controle_estoque=='I') :
                                            ?>
                                            <div class="form-group col-md-4">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_origem()'>Limpa Seleção</a>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_categoria()'>Limpa Seleção</a>
                                            </div>

                                            <?php
                                                else :
                                            ?>
                                            <div class="form-group col-md-4">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <a class='informacao' href='#' onClick='limpar_selecao_categoria()'>Limpa Seleção</a>
                                            </div>

                                            <?php
                                                endif;
                                            ?>
                                        </div>

                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>

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
                                        <!--<div class="row filtro_reproducao" hidden>
                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">Diagnóstico (Atual) </label> 
                                            </div>
                                        </div> 

                                        <div class="row filtro_reproducao" hidden>
                                            <div class="col-xs-2 col-md-2">
                                                <label class="control-label">&nbsp;
                                                </label>  

                                                 <div class="">
                                                    <label>
                                                    <input type="checkbox" id="positivo" name="positivo" value="DP" <?php //if ($positivo=='S'){echo 'checked="checked"';}?>>&nbsp; Positivo
                                                    </label>
                                                 </div>
                                            </div>

                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;
                                                </label>
                                                <select class="form-control selectpicker" multiple id="codigo_estacao_filtro" name="codigo_estacao_filtro" title="Selecione a Estação">
                                                </select>
                                            </div>

                                            <div class="col-xs-2 col-md-2">
                                                <label class="control-label">&nbsp;
                                                </label>  

                                                 <div class="">
                                                    <label>
                                                    <input type="checkbox" value="DN" name="negativo" id="negativo" <?php //if ($negativo=='S'){echo 'checked="checked"';}?>>&nbsp; Negativo
                                                    </label>
                                                 </div>
                                            </div>

                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;</label> 

                                                <div class="">
                                                    <label class="control-label">&nbsp;</label> 

                                                    <label>
                                                    <input type="checkbox" value="MN" name="monta_natural" id="monta_natural"> Monta Natural
                                                    </label>
                                                 </div>
                                            </div>

                                            <div class="col-xs-3 col-md-3">
                                                <label class="control-label">&nbsp;</label> 

                                                <div class="">
                                                    <label class="control-label">&nbsp;</label> 

                                                    <label>
                                                    <input type="checkbox" value="DC" name="descarte" id="descarte" <?php //if ($descarte=='S'){echo 'checked="checked"';}?>> Vacas Descarte
                                                    </label>
                                                 </div>
                                            </div>

                                        </div>-->

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

                                        <?php
                                            endif;
                                        ?>

                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" 
                                                data-dismiss="modal" aria-label="Close" onclick="aplicar_filtros()">Aplicar Filtros</button>

                                            <?php
                                                if ($controle_estoque=='L') :
                                            ?>

                                                <a href="#" class="pull-right" onclick="limpar_filtros(),aplicar_filtros()">Limpar Filtros</a>

                                            <?php
                                            endif;
                                            ?>

                                            </div>
                                        </div>
                                    </div> <!-- Fim tab-pane active"-->
                                </div> <!-- Fim tab-content -->
                            </form>
                        </div> <!-- Fim modal body-->
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_filtro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="redigita_animal_filtro()">Fechar
                            </button>
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

            <div class="modal fade" id="mensagem_erro_animal_filtro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem - Mensagem</h4>
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
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
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
                            <h4 class="modal-title">Pesagem - Mensagem</h4>
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

<script src="js/pesagem.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

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

        $(document).ready(function(){
             $('#codigo_number_filtro').typeahead({
                source: function(query, result) {  
                    $.ajax({
                        url:"fetch.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#local_pesagem').val()},
                        dataType:"json",
                        success:function(data)
                        {
                            result($.map(data, function(item){
                            return item;
                        }));
                        }
                    })
                },
                updater: function(item) {
                    setTimeout(ler_animal_aplicar_filtro, 50); 
                    return item;
                }
            });

            $("#codigo_number_filtro").click(function(){
                $('#codigo_number_filtro').val('');
                $("#codigo_id").val(0);                
                $("#descricao_animal").text('');
                $("#ultimo_peso").text('');
                $("#desc_descarte").text('');
                $("#alert_erro_animal .negrito").html('');
                $("#alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                return false;
            });

            $("#peso_animal").click(function(){
                $("#peso_animal").val('');
                $("#alert_erro_animal .negrito").html('');
                $("#alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
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
