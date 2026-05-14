<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
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
  <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <script>history.scrollRestoration = "manual"</script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style type="text/css">
    .label_descriao{
      font-weight: 600;
      text-align: left !important;
    }
  </style>

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_cadastro[2] == 0){
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

    $tbl_epoca_pesagem = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 

    $tbl_epoca_pesagem_filtro = mysqli_query($conector, "select * from tabela_epoca_pesagem where tab_registro_lixeira_epoca_pesagem=0"); 

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

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
        where tbl_semem_lixeira=0
        order by tbl_semem_nome asc"); 

    $mae_filtro = mysqli_query($conector, "select * from tbl_animais 
        where tbl_animal_lixeira=0 and 
              tbl_animal_ativo='S' and 
              tbl_animal_sexo='F'
        order by tbl_animal_codigo_numerico"); 

    $raca = mysqli_query($conector, "select * from tabela_racas where tab_registro_lixeira_raca=0"); 

    $raca_entrada = mysqli_query($conector, "select * from tabela_racas where tab_registro_lixeira_raca=0
        order by tab_descricao_raca asc"); 

    $pelagem_entrada = mysqli_query($conector, "select * from tabela_pelagens 
        where tab_registro_lixeira_pelagem=0
        order by tab_descricao_pelagem asc"); 

    $mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_lixeira=0 and tbl_animal_ativo='S' and tbl_animal_sexo='F'
    	order by tbl_animal_codigo_numerico"); 

    $tbl_motivo_morte = mysqli_query($conector, "select * from tabela_causa_morte where tab_registro_lixeira_causa_morte=0"); 

    $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $categoria_entrada = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $tbl_categoria_morte = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0");

    $tbl_categoria_outra = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0");

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

    $array_raca = $_SESSION['raca_mov'];
    $array_pai = $_SESSION['pai_mov'];
    $array_mae = $_SESSION['mae_mov'];
    $array_sexo = $_SESSION['sexo_mov'];
    $array_local = $_SESSION['local_mov'];
    $array_origem_filtro = $_SESSION['origem_mov'];
    $array_categoria = $_SESSION['categoria_mov'];
    $peso_inicial_nasc_filtro = $_SESSION['peso_nasc_inicial_mov'];
    $peso_final_nasc_filtro = $_SESSION['peso_nasc_final_mov'];
    $peso_inicial_desmama_filtro = $_SESSION['peso_desmama_inicial_mov']; 
    $peso_final_desmama_filtro = $_SESSION['peso_desmama_final_mov']; 
    $peso_inicial_ultimo_filtro = $_SESSION['peso_ultimo_inicial_mov']; 
    $peso_final_ultimo_filtro = $_SESSION['peso_ultimo_final_mov']; 
    $data_nasc_inicial_filtro = $_SESSION['data_nasc_inicial_mov']; 
    $data_nasc_final_filtro = $_SESSION['data_nasc_final_mov']; 
    $solteiras = $_SESSION["solteiras"];
    $descarte = $_SESSION["descarte"];
    $paridas = $_SESSION["paridas"];
    $data_paridas_ate = $_SESSION["data_paridas_ate"];
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

    $ultimo_cliente_cadastrado = str_pad($_SESSION['ultimo_cliente_cadastrado'], 9, "0", STR_PAD_LEFT);
    $voltar_movimentacao = $_SESSION['voltar_movimentacao'];
    $_SESSION['ultimo_cliente_cadastrado']=0;
    $_SESSION['voltar_movimentacao']='';
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
        include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_movimentacao_animais.php"> Movimentações</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Incluir</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-cogs"></i> Nova Movimentação</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12" id="selecionar_pasagem">
                        <form method="POST" action="gravar_movimentacao_individual.php" id="form_gravar" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <!--<div class="tab-pane active">-->
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Dados para movimentação</legend>

                                        <div class="row">
                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <input name="numero_movimentacao_id" type="hidden" id="numero_movimentacao_id" value="0">

                                            <input name="tipo_gravacao" type="hidden" id="tipo_gravacao" value="1">

                                            <input type="hidden" 
                                            name="ultimo_cliente_cadastrado" 
                                            id="ultimo_cliente_cadastrado" 
                                            <?php echo "value='".$ultimo_cliente_cadastrado."'";?>>

                                            <input type="hidden" 
                                            name="voltar_movimentacao" 
                                            id="voltar_movimentacao" 
                                            <?php echo "value='".$voltar_movimentacao."'";?>>

                                            <div class="form-group col-md-3">
                                                <label for="data_movimentacao" class="control-label"><span class="required">*</span> Data da Movimentação</label>

                                                <input type="date" name="data_movimentacao" id="data_movimentacao" class="form-control"
                                                <?php echo "value='".$data_sistema."'";?>>
                                            </div>

                                            <div class="form-group col-md-8">
                                                <label for="tipo_movimentacao" class="control-label"><span class="required">*</span> Movimentação</label>

                                                <div class="clearfix"></div>
                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_movimentacao" id="compra" value="C" class="tipo_movimentacao">Compra
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_movimentacao" id="venda" value="V" class="tipo_movimentacao">Venda
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_movimentacao" id="transferencia" value="T" class="tipo_movimentacao">Transferência
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_movimentacao" id="morte" value="M" class="tipo_movimentacao">Morte
                                                </label>

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_movimentacao" id="outras" value="O" class="tipo_movimentacao">Outras saídas
                                                </label>
                                            </div>

                                            <div class="form-group col-md-1">
                                                <label class="control-label">&nbsp;</label>
                                                <input type="button" class="form-control btn btn-info" onclick="finalizar_sair()" value="Voltar">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3 local_origem" hidden=''>
                                                <label for="local_origem" class="control-label origem"><span class="required">*</span> Fazenda de Origem</label>

                                                <select class="form-control" name="local_origem" id="local_origem">
                                                <option value="000000000">...</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-1 incluir_mais">
                                                <label class="control-label">&nbsp;&nbsp;</label>
                                                <p>
                                                    <a href="form_cliente_fornecedor_incluir.php?voltar=5" hidden='' class="incluir_mais_origem">
                                                    <i class='fa fa-plus' style="font-size:16px" data-toggle='tooltip' data-placement='top' title='Cadastrar novo produtor' >
                                                    </i>
                                                    </a>
                                                </p>
                                            </div>

                                            <div class="form-group col-md-3 local_destino" hidden=''>
                                                <label for="local_destino" class="control-label destino"><span class="required">*</span> Local de Destino</label>

                                                <select class="form-control" name="local_destino" id="local_destino">
                                                <option value="000000000" >...</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-1 incluir_mais">
                                                <label class="control-label">&nbsp;&nbsp;</label>
                                                <p>
                                                    <a href="form_cliente_fornecedor_incluir.php?voltar=6" hidden='' class="incluir_mais_destino">
                                                    <i class='fa fa-plus' style="font-size:16px" data-toggle='tooltip' data-placement='top' title='Cadastrar novo produtor/cliente' >
                                                    </i>
                                                    </a>
                                                </p>
                                            </div>

                                            <div class="form-group col-md-2 entrada_rapida" hidden="">
                                                <label for="entrada_animais" class="control-label">&nbsp;</label>

                                                <button type="button" class="form-control btn btn-primary entrada_animais">Iniciar Digitação</button>
                                            </div>
                                        </div>

                                        <div class="row mais_opcoes" hidden="">
                                            <div class="form-group col-md-3  selecionar_pesagem">
                                                <label for="pesagem" class="control-label desc_pesagem"> Selecione uma Pesagem</label>
                                                <select class="form-control" name="pesagem" id="pesagem">
                                                <option value="000000000">...</option>
                                                </select>
                                            </div>


                                            <div class="form-group col-md-2 filtro_movimentacao">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="filtros()"
                                                data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
                                            </div>

                                            <?php
                                                if ($controle_estoque=='I') :
                                            ?>
                                            <div class="form-group col-md-3 listar_animais_transferencia">
                                                <label class="control-label"><span style="font-size: 10px">ou</span> Listar e Selecionar os animais</label>
                                                <button type="button" class="form-control btn btn-primary" onclick="listar_animais_venda_transferencia()"
                                                >Listar</button>
                                            </div> 

                                            <?php
                                                else :
                                            ?>

                                            <div class="form-group col-md-3 listar_animais_transferencia">
                                                <label class="control-label"><span style="font-size: 10px">ou</span> Listar e Selecionar os animais</label>
                                                <button type="button" class="form-control btn btn-primary" onclick="listar_animais_venda_transferencia_lote()"
                                                >Listar</button>
                                            </div> 

                                            <?php
                                                endif;
                                            ?>

                                            <div class="form-group col-md-6 incluir_espacos"></div>

                                            <div class="form-group col-md-3">
                                                <label class="control-label">&nbsp;</label>
                                                <a href="#" class="pull-right" onclick="limpar_filtros();fechar_modal_pesagem()"
                                                >Limpar Dados Digitados</a>
                                            </div>
                                        </div>

                                        <div class="row filtro_primeira_tela" hidden>  
                                            <div class="col-md-12" style="font-size: 11px">
                                                <label class="label_descriao">Filtro:&nbsp;</label>
                                                <span class="descricao_filtro_dig"></span>
                                            </div>
                                        </div>

                                    </fieldset>
                                <!--</div>-->
                            </div>

                            <!--<div class="row col-md-12" id="itens" hidden="">-->
                                <div class="tab-panel" id="itens" hidden="">
                                    <div class="tab-pane active">
                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend">Animais Pesados</legend>

                                            <div class="row">  
                                                <div class="col-md-9">
                                                    <p class="text-muted-dark descricao_filtro" style="font-size: 13px"></p>

                                                    <input type="hidden" name="descricao_filtro" class="descricao_filtro">
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="button" class="form-control btn btn-primary" onclick="gravar_movimentacao_pesagem()" value="Confirmar">
                                                </div>

                                                <div class="col-md-1">
                                                    <input type="button" class="form-control btn btn-info " onclick="reseta_confirma(); voltar_movimentacao_selecionar()" value="Voltar">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <p class="text-muted-dark descricao_destino" style="font-size: 13px"></p>

                                                    <input type="hidden" name="descricao_destino" id="descricao_destino">
                                                </div>
                                            </div>
                                            <hr>
                                            <table class="table table-striped table-advance table-hover" id="tabela_itens" width="100%" style="font-size: 13px;">
                                                <thead>
                                                    <tr>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <p id="descricao_lote" class="text-primary"></p>
                                                                <input type="hidden" name="descricao_lote" class="descricao_lote">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <p id="data_pesados" class="text-primary"></p>
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">
                                                            <div class="col-md-2">
                                                                <span class="text-primary total_pesados"></span>
                                                                <input type="hidden" name="total_pesados" class="total_pesados">
                                                            </div>

                                                            <div class="col-md-2">
                                                                <span class="text-primary peso_total_kg"></span>
                                                                <input type="hidden" name="peso_total_kg" class="peso_total_kg">
                                                            </div>

                                                            <div class="col-md-2">
                                                                <span class="text-primary peso_total_arroba"></span>
                                                                <input type="hidden" name="peso_total_arroba" class="peso_total_arroba">
                                                            </div>

                                                            <div class="col-md-2">
                                                                <span class="text-primary peso_medio_kg"></span>
                                                                <input type="hidden" name="peso_medio_kg" class="peso_medio_kg">
                                                            </div>

                                                            <div class="col-md-2">
                                                                <span class="text-primary peso_medio_arroba"></span>
                                                                <input type="hidden" name="peso_medio_arroba" class="peso_medio_arroba">
                                                            </div>
                                                        </div>
                                                    </tr>
                                                    <tr></tr>

                                                    <tr>
                                                        <th> Id</th>
                                                        <th> Peso</th>
                                                        <th> Sexo</th>
                                                        <th> Nascimento</th>
                                                        <th> Raça</th>
                                                        <th> Pelagem</th>
                                                        <th> Mãe</th>
                                                        <th> Observação</th>
                                                        <th> <i class="icon_cogs"></i> Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>

                                            <div class="row">
                                                <div class="col-md-9">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="button" class="form-control btn btn-primary" onclick="gravar_movimentacao_pesagem()" value="Confirmar">
                                                </div>

                                                <div class="col-md-1">
                                                    <input type="button" class="form-control btn btn-info " onclick="reseta_confirma(); voltar_movimentacao_selecionar()" value="Voltar">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            <!--</div> -->   

                                <div class="tab-panel" id="itens_digitados" hidden="" >
                                    <div class="tab-pane active table-responsive">
                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend lista_animais">Animais Digitados</legend>

                                            <div class="row">  
                                                <div class="col-md-8" style="font-size: 14px">
                                                    <label class="label_descriao">Filtro:&nbsp;</label>
                                                    <span class="descricao_filtro_dig"></span>

                                                    <input type="hidden" name="descricao_filtro_dig" id="descricao_filtro_dig">
                                                </div>

                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_movimentacao_selecionar_digitados();">Voltar</button>
                                                </div>
                                            </div>

                                            <div class="row">  
                                                <div class="col-md-5" style="font-size: 14px">
                                                    <label class="label_descriao">Local Origem:&nbsp;</label>
                                                    <span class="descricao_origem_dig"></span>

                                                    <input type="hidden" name="descricao_origem_dig" id="descricao_origem_dig">
                                                </div>

                                                <div class="col-md-7" style="font-size: 14px">
                                                    <label class="label_descriao">Local Destino:&nbsp;</label>
                                                    <span class="descricao_destino_dig"></span>

                                                    <input type="hidden" name="descricao_destino_dig" id="descricao_destino_dig">
                                                </div>
                                            </div>

                                            <div class="row botoes_transferencia">
                                                <div class="col-md-5" style="font-size: 14px">
                                                    <label class="label_descriao">Movimentação:&nbsp;</label>
                                                    <span class="descricao_movimentacao" ></span>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-2">
                                                    <span class="text-primary total_a_digitar"></span>
                                                    <input type="hidden" name="total_a_digitar" class="total_a_digitar">
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary total_digitados"></span>
                                                    <input type="hidden" name="total_digitados" class="total_digitados">
                                                </div>

                                                <div class="col-md-4">
                                                    <p id="data_digitados" class="text-primary"></p>
                                                </div>

                                                <div class="col-md-3">
                                                    <button type="button" class="form-control btn btn-success" onclick="reseta_confirma(); finalizar_selecao_venda_transferencia();">Confirmar Movimentção</button>
                                                </div>

                                            </div>

                                            <div id="itens_listados">
                                            </div>

                                            <!--<div class="row botoes_transferencia">
                                                <div class="col-md-10">
                                                </div>

                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-success" onclick="reseta_confirma(); finalizar_selecao_venda_transferencia();">Confirmar Movimentação</button>
                                                </div>
                                            </div>-->
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="tab-panel" id="itens_digitados_entrada" hidden="" >
                                    <div class="tab-pane active table-responsive">
                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend">Itens Digitados - Compra</legend>

                                            <div class="row">  
                                                <div class="col-md-8">
                                                    <p class="text-muted-dark descricao_filtro_dig_entrada" style="font-size: 13px"></p>
                                                </div>

                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_movimentacao_compras();">Voltar</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <p class="text-muted-dark descricao_destino_dig" style="font-size: 13px"></p>

                                                    <input type="hidden" name="descricao_destino_dig" id="descricao_destino_dig">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                </div>

                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info" onclick="continuar_digitacao_entrada();">Continuar Digitação</button>

                                                    <button type="button" class="btn btn-primary" onclick="reseta_confirma(); finalizar_digitacao_entrada();">Finalizar Compra</button>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="text-primary total_a_digitar_entrada"></span>
                                                    <input type="hidden" name="total_a_digitar_entrada" class="total_a_digitar_entrada">
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary total_digitados_entrada"></span>
                                                    <input type="hidden" name="total_digitados_entrada" class="total_digitados_entrada">
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary total_restante_entrada"></span>
                                                    <input type="hidden" name="total_restante_entrada" class="total_restante_entrada">
                                                </div>

                                                <div class="col-md-3">
                                                    <p id="data_digitados_entrada" class="text-primary"></p>
                                                </div>
                                            </div>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_digitados_entrada" style="font-size: 13px;">

                                                <thead>
                                                <?php
                                                    if ($controle_estoque=='I') {
                                                        echo '
                                                            <tr>
                                                                <th>Categoria</th>
                                                                <th style="text-align: center;"> Idade (meses)</th>
                                                                <th style="text-align: center;">Sexo</th>
                                                                <th>Raça</th>
                                                                <th>Pelagem</th>
                                                                <th style="text-align: right;">Qtde Categoria</th>
                                                                <th style="text-align: right;">Seq Númerica</th>
                                                                <th>Marcação Alfa</th>
                                                                <th>Peso Médio</th>
                                                                <th><i class="icon_cogs"></i> Ações</th>
                                                            </tr>

                                                        ';
                                                    }
                                                    else {
                                                        echo '
                                                            <tr>
                                                                <th>Categoria</th>
                                                                <th style="text-align: center;"> Idade (meses)</th>
                                                                <th style="text-align: center;">Sexo</th>
                                                                <th>Raça</th>
                                                                <th style="text-align: right;">Qtde Categoria</th>
                                                                <th style="text-align: right;">Peso Médio</th>
                                                                <th><i class="icon_cogs"></i> Ações</th>
                                                            </tr>
                                                        ';
                                                    }
                                                ?>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>

                                            <div class="row"> 
                                                <div class="col-md-8">
                                                </div>

                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-info" onclick="continuar_digitacao_entrada();">Continuar Digitação</button>

                                                    <button type="button" class="btn btn-primary" onclick="reseta_confirma(); finalizar_digitacao_entrada();">Finalizar Compra</button>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>

                                <input type="hidden" class="form-control" name="array_itens" id="array_itens">

                                <input type="hidden" id="cat_pasto_m1">
                                <input type="hidden" id="cat_pasto_m2">
                                <input type="hidden" id="cat_pasto_m3">
                                <input type="hidden" id="cat_pasto_m4">
                                <input type="hidden" id="cat_pasto_m5">

                                <input type="hidden" id="cat_pasto_f1">
                                <input type="hidden" id="cat_pasto_f2">
                                <input type="hidden" id="cat_pasto_f3">
                                <input type="hidden" id="cat_pasto_f4">
                                <input type="hidden" id="cat_pasto_f5">
                        </form>
                    </div>  
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_gravar_venda_transf" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_pesagem();">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>

                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" onclick="gravar_movimentacao_venda_transferencia();">Confirmar</button>

                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_pesagem" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_pesagem();">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class='control-label titulo_pesagem' style='font-weight: bold;'> A pesagem para VENDA dos animais foi registrada?</label>

                                    <div class='clearfix'></div>

                                    <label class='radio-inline'>
                                    <input type='radio' name='opcao_pesagem' id="peso_registrado" value='S'>Já Registrei
                                    </label>

                                    <label class='radio-inline'>
                                    <input type='radio' name='opcao_pesagem' id="registrar_peso_sim" value='R'>Quero Registrar
                                    </label>

                                    <label class='radio-inline'>
                                    <input type='radio' name='opcao_pesagem' id="registrar_peso_nao" value='N'>Não Quero Registrar
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" onclick="confirmar_opcaoes_pesagem();">Confirma</button>

                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_modal_pesagem();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_morte" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-lg modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Morte </h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <input name="codigo_id_morte" type="hidden" id="codigo_id_morte" value="0">
                                <input name="sexo_animal_morte" type="hidden" id="sexo_animal_morte">
                                <input name="peso_animal_morte" type="hidden" id="peso_animal_morte">
                                <input name="nascimento_animal_morte" type="hidden" id="nascimento_animal_morte">
                                <input name="raca_animal_morte" type="hidden" id="raca_animal_morte">
                                <input name="pelagem_animal_morte" type="hidden" id="pelagem_animal_morte">
                                <input name="mae_animal_morte" type="hidden" id="mae_animal_morte">
                                <input name="motivo_animal_morte" type="hidden" id="motivo_animal_morte">
                                <input name="codigo_motivo_morte" type="hidden" id="codigo_motivo_morte">

                                <input type="hidden" name="sexo_morte" id="sexo_morte">
                                <input type="hidden" name="categoria_digitada_morte" id="categoria_digitada_morte">
                                <input type="hidden" name="desc_categoria_digitada_morte" id="desc_categoria_digitada_morte">
                                <input type="hidden" name="qtd_morte" id="qtd_morte">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div class="row">  
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-info pull-right" onclick="location.reload();">Voltar</button>
                                        </div>
                                    </div>

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-3 id_animal">
                                                <label for="id_animal_morte" class="control-label"><span class="required">*</span> Nº Animal</label>
                                                <input name="id_animal_morte" type="text" class="form-control" id="id_animal_morte" autocomplete="off"
                                                onchange="ler_animal_morte()" >
                                            </div>

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="motivo_morte" class="control-label"><span class="required">*</span> Motivo da Morte</label>
                                                <select class="form-control form-select" id="motivo_morte" name="motivo_morte">

                                                <option value="000">...</option>

                                                <?php while($reg_motivo = mysqli_fetch_object($tbl_motivo_morte)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_motivo->tab_codigo_causa_morte ?>">
                                                        
                                                    <?php 
                                                        echo $reg_motivo->tab_descricao_causa_morte;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>
                                            </div>

                                        </div>

                                        <div class='row'>
                                            <div class="form-group col-xs-5 col-md-6">
                                                <label for="pasto_morte" class="control-label"><span class="required">*</span> Pasto</label>
                                                <select class="form-control form-select" id="pasto_morte" name="pasto_morte">

                                                <option value="00000000">...</option>
                                                </select>
                                            </div>

                                            <div class='form-group col-md-6 info_modal_morte' hidden>
                                               <label for='categoria_morte' class='control-label'><span class='required'>*</span> Categoria/Sexo</label>
                                               <select class='form-control form-select' id='categoria_morte' name='categoria_morte'>

                                               <option value='000'>...</option>";
                                                    </option>
                                               </select>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="observacao_morte" class="control-label">Observação</label>

                                                <textarea name="observacao_morte" type="text" class="form-control" id="observacao_morte" rows="3" onkeyup="maiuscula(this)"></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-10">
                                                <label class="control-label">&nbsp;</label>
                                                <p id="descricao_animal_morte" class="text-primary"></p>
                                            </div>

                                            <div class="form-group col-md-2" id="incluir_morte">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" id="confirmar_morte" onClick="salvar_morte()">Confirmar</button>
                                            </div>
                                        </div>
                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_outra_saida" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                           <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" onclick="limpa_radio_tipo_movimentacao()" >&times;</span></button> -->
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Outras saídas</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <input name="codigo_id_outra" type="hidden" id="codigo_id_outra" value="0">
                                <input name="sexo_animal_outra" type="hidden" id="sexo_animal_outra">
                                <input name="peso_animal_outra" type="hidden" id="peso_animal_outra">
                                <input name="nascimento_animal_outra" type="hidden" id="nascimento_animal_outra">
                                <input name="raca_animal_outra" type="hidden" id="raca_animal_outra">
                                <input name="pelagem_animal_outra" type="hidden" id="pelagem_animal_outra">
                                <input name="mae_animal_outra" type="hidden" id="mae_animal_outra">

                                <input type="hidden" name="sexo_outra" id="sexo_outra">
                                <input type="hidden" name="categoria_digitada_outra" id="categoria_digitada_outra">
                                <input type="hidden" name="qtd_outra" id="qtd_outra">

                                <input type="hidden" name="categoria_digitada_outra" id="categoria_digitada_outra">
                                <input type="hidden" name="desc_categoria_digitada_outra" id="desc_categoria_digitada_outra">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div class="row">  
                                         <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-info pull-right" onclick="location.reload();">Voltar</button>
                                        </div>
                                    </div>

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-4 id_animal">
                                                <label for="id_animal_outra" class="control-label"><span class="required">*</span> Id Animal</label>
                                                <input name="id_animal_outra" type="text" class="form-control" id="id_animal_outra" autocomplete="off"
                                                onchange="ler_animal_outra()" >
                                            </div>

                                            <div class="form-group col-xs-5 col-md-5">
                                                <label for="pasto_outra" class="control-label"><span class="required">*</span> Pasto</label>
                                                <select class="form-control form-select" id="pasto_outra" name="pasto_outra">

                                                <option value="00000000">...</option>
                                                </select>
                                            </div>

                                            <div class='form-group col-md-5 info_modal_outra' hidden>
                                               <label for='categoria_outra' class='control-label'><span class='required'>*</span> Categoria/Sexo</label>
                                               <select class='form-control form-select' id='categoria_outra' name='categoria_outra'>

                                               <option value='000'>...</option>";
                                               </select>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="observacao_outra" class="control-label"><span class="required">*</span>Observação</label>

                                                <textarea name="observacao_outra" type="text" class="form-control" id="observacao_outra" rows="3" onkeyup="maiuscula(this)"></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-10">
                                                <label class="control-label">&nbsp;</label>
                                                <p id="descricao_animal_outra" class="text-primary"></p>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="salvar_outra()">Confirmar</button>
                                            </div>
                                        </div>


                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_individual" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                        <!--    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Individual - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <input name="codigo_id" type="hidden" id="codigo_id" value="0">
                                <input name="sexo_animal" type="hidden" id="sexo_animal">
                                <input name="nascimento_animal" type="hidden" id="nascimento_animal">
                                <input name="categoria_animal" type="hidden" id="categoria_animal">

                                <input name="raca_animal" type="hidden" id="raca_animal">
                                <input name="pelagem_animal" type="hidden" id="pelagem_animal">
                                <input name="mae_animal" type="hidden" id="mae_animal">

                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="qtd_a_digitar" class="control-label">Quantidade de Animais</label>
                                                <input name="qtd_a_digitar" type="number" class="form-control" id="qtd_a_digitar">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="qtd_digitado" class="control-label">Animais Digitados</label>
                                                <input name="qtd_digitado" type="number" class="form-control" id="qtd_digitado" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-3 id_animal">
                                                <label for="id_animal" class="control-label"><span class="required">*</span> Id Animal</label>
                                                <input name="id_animal" type="text" class="form-control" id="id_animal" autocomplete="off"
                                                onchange="ler_animal()" >
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4 digitacao_lote_individual" hidden="">
                                                <label for="codigo_categoria_individual" class="control-label"><span class="required">*</span> Categoria</label>
                                                <select class="form-control" id="codigo_categoria_individual" name="codigo_categoria_individual">
                                                <option value="000">...</option>                   </select>
                                            </div>

                                            <div class="form-group col-xs-12 col-md-2 digitacao_lote_individual" hidden="">
                                                <label for="qtd_cat_individual" class="control-label"><span class="required">*</span> Qtde</label>
                                                <input name="qtd_cat_individual" type="number" class="form-control" id="qtd_cat_individual" onchange="soma_total_item_lote()">

                                                <input type="hidden" name="sexo_lote" id="sexo_lote">
                                                <input type="hidden" name="categoria_lote" id="categoria_lote">
                                                <input type="hidden" name="qtd_lote" id="qtd_lote">
                                               <input type="hidden" name="qtd_digitado_anterior" id="qtd_digitado_anterior">

                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="observacao" class="control-label">Observação</label>
                                                <input name="observacao" type="text" class="form-control" id="observacao" maxlength="100"
                                                onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-2" id="incluir">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" id="btn_salvar_individual" onClick="salvar()">Confirmar</button>
                                            </div>

                                            <div class="form-group col-md-2" id="editar" hidden="" >
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn-success" onClick="salvar_editar()">Confirmar</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <p id="descricao_animal" class="text-primary"></p>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-primary" onclick="pausar_digitacao()">Pausar Digitação</button>
                                            </div>
                                        </div>

                                    </div> <!-- fim tab-pane active-->
                                </div> <!-- Fim tab-content-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_entrada_rapida" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Entrada Rápida de Animais ao Cadastro</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data" >
                                <div class="tab-content">
                                    <div class="alert alert-danger alert_erro_animal" hidden="true">
                                        <strong class="negrito"></strong><span></span>
                                    </div> 

                                    <div id="dados" class="tab-pane active">
                                        <div class="row">
                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="qtd_total_animais" class="control-label"><span class="required">*</span> Quantidade Total de Animais</label>
                                                <input name="qtd_total_animais" type="number" class="form-control" id="qtd_total_animais">
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="qtd_total_digitado" class="control-label">Animais Digitados</label>
                                                <input name="qtd_total_digitado" type="number" class="form-control" id="qtd_total_digitado" readonly="">
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="qtd_total_restante" class="control-label">Faltam Digitar</label>
                                                <input name="qtd_total_restante" type="number" class="form-control" id="qtd_total_restante" readonly="">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="codigo_categoria_entrada" class="control-label"><span class="required">*</span> Categoria</label>
                                                <select class="form-control" id="codigo_categoria_entrada" name="codigo_categoria_entrada">
                                                          
                                                <option value="000">...</option>                
                                                <?php while($reg_catagoria = mysqli_fetch_object($categoria_entrada)) { ?>

                                                    <option value="<?php 
                                                            echo $reg_catagoria->tab_codigo_categoria_idade ?>">
                                                            
                                                    <?php 
                                                        if ($reg_catagoria->tab_categoria_idade_ate==999999999) {
                                                            echo '> 36 meses';
                                                        }
                                                        else {
                                                            echo $reg_catagoria->tab_categoria_idade_de . ' a ' . $reg_catagoria->tab_categoria_idade_ate . ' meses';
                                                        }
                                                        ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="idade_entrada" class="control-label"><span class="required">*</span> Idade (meses)</label>
                                                <input name="idade_entrada" type="number" class="form-control" id="idade_entrada">
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="sexo_entrada" class="control-label"><span class="required">*</span> Sexo</label>
                                                <div class="clearfix"></div>
                                                <label class="radio-inline">
                                                  <input type="radio" name="sexo_entrada" id="macho_entrada" value="M" class="sexo_entrada">Macho
                                                </label>
                                                <label class="radio-inline">
                                                  <input type="radio" name="sexo_entrada" id="femea_entrada" value="F" class="sexo_entrada">Fêmea
                                                </label>

                                                <p class="mens_reprodutor text-danger" style="font-size: 11px;">Se REPRODUTORES, marcar a opção no cadastro de animais</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-xs-12 col-md-4">

                                                <label for="codigo_raca_entrada" class="control-label"><span class="required" id="span_odigo_raca_entrada">*</span> Raça</label>

                                                <select class="form-control" id="codigo_raca_entrada" name="codigo_raca_entrada">

                                                <option value="000">...</option>   

                                                <?php while($reg_raca = mysqli_fetch_object($raca_entrada)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_raca->tab_codigo_raca ?>">
                                                        
                                                    <?php 
                                                        echo $reg_raca->tab_descricao_raca;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4 codigo_pelagem_entrada">
                                                <label for="codigo_pelagem_entrada" class="control-label">Pelagem</label>
                                                <select class="form-control" id="codigo_pelagem_entrada" name="codigo_pelagem_entrada">

                                                <option value="000">...</option> 

                                                <?php while($reg_pelagem = mysqli_fetch_object($pelagem_entrada)) { ?>

                                                    <option value="<?php 
                                                       echo $reg_pelagem->tab_codigo_pelagem  ?>">
                                                        
                                                    <?php 
                                                        echo $reg_pelagem->tab_descricao_pelagem;
                                                    ?>
                                                    </option>
                                                <?php } ?>

                                                </select>
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4">
                                                <label for="qtd_cat_entrada" class="control-label"><span class="required">*</span> Quantidade da Categoria</label>
                                                <input name="qtd_cat_entrada" type="number" class="form-control" id="qtd_cat_entrada">

                                                <input name="qtd_cat_anterior" type="number" id="qtd_cat_anterior" hidden="" >
                                            </div>

                                            <div class="form-group col-xs-12 col-md-4 peso_medio">
                                                <label for="peso_medio" class="control-label"><span class="required">*</span> Peso Médio da Categorias</label>
                                                <input name="peso_medio" type="number" class="form-control" id="peso_medio"
                                                ><small id="sequenciaHelpBlock" class="form-text text-muted" style="color: #808080">Caso queira informar o peso por individuo após entrada ir para o menu Animais>Pesagem</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="sequencia_id">
                                                <div class="form-group col-xs-12 col-md-4">
                                                    <label for="sequencia_numeria_entrada" class="control-label"><span class="required">*</span> Sequência Numérica Inicial</label>
                                                    <input name="sequencia_numeria_entrada" type="number" class="form-control" id="sequencia_numeria_entrada" aria-describedby="sequenciaHelpBlock">
                                                    <small id="sequenciaHelpBlock" class="form-text text-muted" style="color: #808080" hidden=""></small>
                                                </div>

                                                <div class="form-group col-xs-12 col-md-4">
                                                    <label for="marcacao_alfa_entrada" class="control-label">Marcação Alfanumérica</label>
                                                    <input name="marcacao_alfa_entrada" type="text" class="form-control" id="marcacao_alfa_entrada"
                                                    maxlength="4" onkeyup="maiuscula(this)">
                                                </div>

                                                <div class="form-group col-xs-12 col-md-4">
                                                    <label for="peso" class="control-label"><span class="required">*</span> Peso Médio da Categoria</label>
                                                    <input name="peso" type="number" class="form-control" id="peso_entrada"
                                                    ><small id="sequenciaHelpBlock" class="form-text text-muted" style="color: #808080">Caso queira informar o peso por individuo após entrada ir para o menu Animais>Pesagem</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row"> 
                                            <div class="form-group col-md-3" id="incluir_entrada">
                                                <button type="button" class="form-control btn-success" id="btn_salvar_entrada" onClick="salvar_entrada()">Confirmar Digitação</button>
                                            </div>

                                            <div class="form-group col-md-3" id="editar_entrada" hidden="" >
                                                <button type="button" class="form-control btn-success" onClick="salvar_editar_entrada()">Confirmar Edição</button>
                                            </div>
   
                                            <div class="form-group col-md-2">
                                                <button type="button" class="btn btn-primary" onclick="pausar_digitacao_entrada()">Pausar Digitação</button>
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
                                                <button type="button" class="btn btn-primary" 
                                                onclick="ler_animal_filtro()">Aplicar Filtros</button>

                                                <a href="#" class="pull-right" onclick="limpar_filtros()">Limpar Filtros</a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-6 col-md-3">
                                                <label for="codigo_number_filtro" class="control-label">Código do Animal</label>
                                                <input name="codigo_number_filtro" type="text" class="form-control" id="codigo_number_filtro" autocomplete="off"
                                                >
                                            </div>
                                        </div>

                                        <div class="row">
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
                                                  <input type="checkbox" id="sim_filtro" name="ativo_filtro" value="S" checked disabled>Sim
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="checkbox" id="nao_filtro" name="ativo_filtro" value="N" disabled>Não
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
                                            <div class="form-group col-md-4">
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

                                            <?php
                                                endif;
                                            ?>

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
                                        </div>

                                        <?php
                                            if ($controle_estoque=='I') :
                                        ?>

                                        <div class="row">
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

                                            <div class="form-group col-xs-4 col-md-4">
                                                <label for="codigo_pai_filtro" class="control-label">Pai</label>
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_pai_filtro" name="codigo_pai_filtro" data-size="6">

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
                                                <select class="form-control selectpicker" multiple data-live-search="true" id="codigo_mae_filtro" name="codigo_mae_filtro" data-size="6">

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
                                                <label class="control-label">&nbsp;</label>                                
                                                <div class="checkbox">
                                                    <label>
                                                    <input type="checkbox" value="VS" name="vacas_solteiras" id="vacas_solteiras" <?php if ($solteiras=='S'){echo 'checked="checked"';}?>> Vacas Solteiras <span style="border: none; color: #bdbbbb">&nbsp;&nbsp;(Paridas há 8 meses+ e Novilhas)</span>
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

                                        </div> <!-- Fim outros Filtros-->

                                        <?php
                                            endif;
                                        ?>

                                        <div class="row">  
                                            <div class="form-group col-xs-12 col-md-12">
                                                <button type="button" class="btn btn-primary" 
                                                onclick="ler_animal_filtro()">Aplicar Filtros</button>

                                                <a href="#" class="pull-right" 
                                                onclick="limpar_filtros()">Limpar Filtros</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                            <h4 class="modal-title">Movimentação </h4>
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
                            <h4 class="modal-title">Movimentação </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_morte" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="continuar_digitacao_morte()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_outra" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="continuar_digitacao_outra()">Fechar</button>
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
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body fundo_red"></div>
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
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
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

            <div class="modal fade" id="mensagem_sair_sem_confirmar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body fundo_red"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-success" type="button" id="btnConfirmarReload">Confirmar
                            </button>

                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_atencao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p class="desc_modal" style="font-weight: bold;">
                                    </p>
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


            <div class="modal fade" id="mensagem_erro_transferencia" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body fundo_red"></div>

                        <pre class="mens" style="margin-left: 5px; margin-right: 5px;">
                        </pre>

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

            <div class="modal fade" id="mensagem_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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

                            <input type="hidden" id="id_pasto">
                            <input type="hidden" id="descricao_pasto">
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
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
<script src="js/typeahead.js"></script>

<script src="js/movimentacao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  -->

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
                        url:"fetch.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#local_origem').val()},
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

            $('#id_animal').typeahead({
                source: function(query, result) {
                    $.ajax({
                        url:"fetch.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#local_origem').val()},
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

            $('#id_animal_morte').typeahead({
                source: function(query, result) {
                    $.ajax({
                        url:"fetch.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#local_origem').val()},
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

             $('#id_animal_outra').typeahead({
                source: function(query, result) {
                    $.ajax({
                        url:"fetch.php",
                        method:"POST",
                        data:{query:query,
                              local: $('#local_origem').val()},
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
                //$("#id_animal").val('');
                $("#codigo_id").val(0);
                $("#descricao_animal").text('');
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                return;
            });

            $("#codigo_number_filtro").click(function(){
                $("#codigo_number_filtro").val('');
                document.getElementById("codigo_number_filtro").style.borderColor = "";
                return;
            });

            $("#id_animal_morte").click(function(){
                //$("#id_animal_morte").val('');
                $("#codigo_id_morte").val(0);
                $("#descricao_animal_morte").text('');
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                return;
            });


            $("#id_animal_outra").click(function(){
                //$("#id_animal_outra").val('');
                $("#codigo_id_outra").val(0);
                $("#descricao_animal_outra").text('');
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
                return;
            });

            $("#peso_animal").click(function(){
                $("#peso_animal").val('');
                $(".alert_erro_animal .negrito").html('');
                $(".alert_erro_animal span").html('');
                $(".alert_erro_animal").hide();
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

        $('#btn_salvar_entrada').click(function(){
            var a_pesar = $('#qtd_total_animais').val();
            var pesados = $('#qtd_total_digitado').val();

            if(a_pesar != pesados){  
                needToConfirm = true;
            }else{
                needToConfirm = false;
            }
        });

        $('#btn_salvar_individual').click(function(){
            var a_pesar = $('#qtd_a_digitar').val();
            var pesados = $('#qtd_digitado').val();
            if(a_pesar != pesados){  
                needToConfirm = true;
            }else{
                needToConfirm = false;
            }
        });

        // Espera o DOM ser completamente carregado antes de executar o script
        document.addEventListener('DOMContentLoaded', function() {
          // Pega o botão Confirmar pelo seu ID
          const btnConfirmar = document.getElementById('btnConfirmarReload');

          // Adiciona um "listener" de evento de clique ao botão
          if (btnConfirmar) { // Verifica se o botão existe para evitar erros
            btnConfirmar.addEventListener('click', function() {
                // Recarrega a página
                //location.reload();
                $("#itens_digitados").hide();
                $("#dados_consulta").show();
            });
          }
        });
    </script>

</body>
</html>


