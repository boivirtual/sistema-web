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
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_gestao_adm'])) {
        $array_cadastro = explode("!",$_SESSION['menu_gestao_adm']);

        if ($array_cadastro[0] == 0){
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


    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_cliente = mysqli_query($conector, "select * from tbl_pessoa where (tbl_pessoa_classe=1 or tbl_pessoa_classe=2) and tbl_pessoa_lixeira=0"); 

    $categoria_vivo = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $categoria_morto = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $categoria_cabeca = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    $conta_pri = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC"); 

    $conta_parcela = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0  order by tbl_conta_pagamento_descricao ASC"); 

    $forma_pri = mysqli_query($conector, "select * from tbl_forma_pagamento where tbl_forma_pagamento_lixeira=0"); 

    $forma_parcela = mysqli_query($conector, "select * from tbl_forma_pagamento where tbl_forma_pagamento_lixeira=0"); 

    $tbl_conta_contabil = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_debito_credito='C' and tbl_plano_contas_nivel=3 order by tbl_plano_contas_descricao ASC"); 

    $tbl_conta_contabil_morto = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_debito_credito='C' and tbl_plano_contas_nivel=3 order by tbl_plano_contas_descricao ASC"); 

    $tbl_conta_contabil_vivo = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_debito_credito='C' and tbl_plano_contas_nivel=3 order by tbl_plano_contas_descricao ASC"); 

    $tbl_conta_contabil_cabeca = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_lixeira=0 and tbl_plano_contas_debito_credito='C' and tbl_plano_contas_nivel=3 order by tbl_plano_contas_descricao ASC"); 

    $tbl_cc = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0"); 

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
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_compra_venda_animais.php"> Compra/Venda Animais</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Venda - Incluir</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-shopping-cart"></i> Venda - Incluir</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_venda">
                            <div class="panel"> 
                                <div class=panel-body>
                                    <ul class="nav nav-tabs m-bot15">
                                        <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados da Venda</a>
                                        </li>
                                        <li>
                                        <a data-toggle="tab" href="#totais" class="aba_totais">Totais/Prazos</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">

                                            <input type="hidden" id="tem_itens" value="N">
                                            <fieldset class="scheduler-border tela_dados">
                                                <legend class="scheduler-border fonte-legend"></legend>

                                                <input type="hidden" name="numero_venda_id" id="numero_venda_id" value="0">

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button> 
                                                    </div>
                                                </div>

                                                <div class="row ">
                                                    <div class="form-group col-md-4">
                                                        <label for="data_venda" class="control-label"><span class="required">*</span> Data</label>

                                                        <input type="date" name="data_venda" id="data_venda" class="form-control input-sm" <?php echo "value='".$data_sistema."'";?>>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="tem_movimentacao" class="control-label"><span class="required">*</span> Animais já foram pesados e retirados do pasto?</label>

                                                        <div class="clearfix"></div>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tem_movimentacao" id="mov_sim" value="S" class="tem_movimentacao">Sim
                                                        </label>

                                                        <label class="radio-inline">
                                                            <input type="radio" name="tem_movimentacao" id="mov_nao" value="N" class="tem_movimentacao">Mais Tarde
                                                        </label>
                                                    </div>

                                                <!--   <div class="form-group col-md-4 opcao_fazer_movimentacao" hidden="">
                                                        <label for="fazer_movimentacao" class="control-label"><span class="required">*</span> Deseja Fazer a movimentação agora?</label>

                                                        <div class="clearfix"></div>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="fazer_movimentacao" id="fazer_sim" value="S" class="fazer_movimentacao">Sim
                                                        </label>

                                                        <label class="radio-inline">
                                                            <input type="radio" name="fazer_movimentacao" id="fazer_nao" value="N" class="fazer_movimentacao">Mais Tarde
                                                        </label>
                                                    </div> -->

                                                    <div class="form-group col-md-4 lista_movimentacao" hidden="">
                                                        <label for="lista_movimentacao" class="control-label">Movimentação</label>
                                                        <select class="form-control input-sm" id="lista_movimentacao" name="lista_movimentacao">
                                                        <option value="000000000">...</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row local_comprador" hidden="">
                                                    <div class="form-group col-md-4">
                                                        <label for="local" class="control-label"><span class="required">*</span> Local</label>

                                                        <select class="form-control input-sm" name="local" id="local">
                                                        <option value="000000000">...</option>
                                                        
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_cliente" class="control-label"><span class="required">*</span> Comprador</label>
                                                        <select class="form-control input-sm" id="codigo_cliente" name="codigo_cliente">
                                                        <option value="000000000">...</option>
                                                        <?php while($reg_cliente = mysqli_fetch_object($tbl_cliente)) { ?>

                                                            <option value="<?php 
                                                               echo $reg_cliente->tbl_pessoa_id?>">
                                                                
                                                            <?php 
                                                                echo $reg_cliente->tbl_pessoa_nome;
                                                            ?>
                                                            </option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="tipo_venda" class="control-label"><span class="required">*</span> Tipo da Venda</label>

                                                        <div class="clearfix"></div>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipo_venda" id="peso_vivo" value="V" class="tipo_venda">Peso Vivo
                                                        </label>

                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipo_venda" id="peso_morto" value="M" class="tipo_venda">Peso Morto
                                                        </label>

                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipo_venda" id="cabeca" value="C" class="tipo_venda">Cabeça
                                                        </label>
                                                    </div>

                                                </div>

                                            </fieldset>

                                            <fieldset class="scheduler-border tela_peso_vivo" hidden="">
                                                <legend class="scheduler-border fonte-legend">Peso Vivo</legend>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <button type="button" class="btn btn-primary aba_totais">Próximo </button>
                                                        
                                                        <button type="button" class="btn btn-info pull-right exibe_tela_dados">Voltar</button> 
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <p class="text-primary data_venda"></p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p class="text-primary local_venda"></p>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <p class="text-primary cliente_venda"></p>
                                                    </div>
                                                </div>

                                                <div class="row linha_escondida">
                                                    <div class="form-group col-md-2">
                                                        <label for="categoria_vivo" class="control-label"><span class="required">*</span> Categoria</label>
                                                        <select class="form-control input-sm" id="categoria_vivo" name="categoria_vivo">
                                                        
                                                        <option value="000">...</option>      
                                                        <?php while($reg_catagoria = mysqli_fetch_object($categoria_vivo)) { ?>

                                                            <option value="<?php 
                                                                echo $reg_catagoria->tab_codigo_categoria_idade ?>">
                                                               
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
                                                        <label for="sexo_vivo" class="control-label"><span class="required">*</span> Sexo</label>
                                                        <select class="form-control input-sm" id="sexo_vivo" name="sexo_vivo">
                                                        
                                                        <option value="">...</option>      
                                                        <option value="M">Macho</option>      
                                                        <option value="F">Fêmea</option>      
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="qtd_vivo" class="control-label"><span class="required">*</span> Qtde Animais</label>
                                                        <input type="number" name="qtd_vivo" id="qtd_vivo" class="form-control input-sm" onkeypress="return desabilita_enter (this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="peso_categoria_vivo" class="control-label"><span class="required">*</span> Peso Total kg</label>
                                                        <input type="text" name="peso_categoria_vivo" id="peso_categoria_vivo" class="form-control input-sm" placeholder="0,00" 
                                                        onkeypress="return numeros(this, event)" 
                                                        onblur="exibe_peso_categoria_vivo()" oninput="digita_valor()">
                                                    </div>
                                                </div> 
                                                
                                                <div class="row linha_escondida">    
                                                    <div class="form-group col-md-2">
                                                        <label for="fator_arroba_vivo" class="control-label"><span class="required">*</span> Fator Multiplicação @</label>
                                                        <input type="text" name="fator_arroba_vivo" id="fator_arroba_vivo" class="form-control input-sm fator_arroba_vivo" aria-describedby="arrobaHelpBlock" placeholder="0,000000" 
                                                        onkeypress="return numeros(this, event)"
                                                        onblur="exibe_fator_arroba()">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="arroba_categoria_vivo" class="control-label"><span class="required">*</span> Peso @</label>
                                                        <input type="text" name="arroba_categoria_vivo" id="arroba_categoria_vivo" class="form-control input-sm" aria-describedby="arrobaHelpBlock" placeholder="0,00" 
                                                        readonly="">
                                                        <small id="arrobaHelpBlock" class="form-text text-muted" style="color: #808080; font-size: 8px;"></small>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="unidade_vivo" class="control-label"><span class="required">*</span> Und Negociada</label>
                                                        <select class="form-control input-sm" id="unidade_vivo" name="unidade_vivo" onkeypress="return numeros(this, event)">
                                                        
                                                        <option value="">...</option>      
                                                        <option value="1">@</option>      
                                                        <option value="2">Kg</option>      
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="valor_unitario_vivo" class="control-label"><span class="required">*</span>Valor Unitário</label>
                                                        <input name="valor_unitario_vivo" type="text" class="form-control input-sm" id="valor_unitario_vivo" placeholder="0,00"
                                                        oninput="digita_valor_vivo()" 
                                                        onblur="exibe_valor_unitario_vivo()"
                                                        onkeypress="return numeros(this, event)"
                                                        >
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="total_vivo" class="control-label">Valor Total</label>
                                                        <input name="total_vivo" type="text" class="form-control input-sm" id="total_vivo" placeholder="0,00" readonly="">
                                                    </div>
                                                </div>
                                                
                                                <div class="row linha_escondida">    
                                                    <div class="form-group col-md-4">
                                                        <label for="conta_vivo" class="control-label"><span class="required">*</span> Conta Contabil</label>
                                                        <select class="form-control input-sm" id="conta_vivo" name="conta_vivo" >
                                                        <option value="0000000" selected="selected">...</option>

                                                        <?php while($reg_conta = mysqli_fetch_object($tbl_conta_contabil_vivo)) { ?>

                                                        <option value="<?php 
                                                            echo $reg_conta->tbl_plano_contas_codigo_id  ?>">
                                                                                            
                                                        <?php 
                                                            echo $reg_conta->tbl_plano_contas_descricao;
                                                        ?>
                                                        </option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label class="control-label">&nbsp;</label>
                                                        <button type="button" class="form-control input-sm btn btn-success pull-right incluir" onclick="salvar_item_animal_vivo()">Confirma</button>
                                                        <button type="button" class="form-control input-sm btn btn-success editar" hidden="" onclick="salvar_editar_item_vivo()">Confirma Edição</button> 
                                                    </div>
                                                </div>

                                            <!--    <hr align="center" class="linha_escondida"> -->

                                                <table class="table table-striped table-advance table-hover tabela_itens_vivo" id="tabela_itens_vivo" width="100%" style="font-size: 13px;" hidden="">
                                                    <thead>
                                                        <tr>
                                                            <th> Categoria</th>
                                                            <th> Sexo</th>
                                                            <th style="text-align: right;"> Qtde Animais</th>
                                                            <th style="text-align: right;"> Peso Total kg</th>
                                                            <th style="text-align: right;"> Peso @</th>
                                                            <th style="text-align: center;"> Und Negociada</th>
                                                            <th style="text-align: right;"> Valor Unitário</th>
                                                            <th style="text-align: right;"> Valor Total</th>
                                                            <th> <i class="icon_cogs"></i> Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </fieldset>

                                            <fieldset class="scheduler-border tela_peso_morto" hidden="">
                                                <legend class="scheduler-border fonte-legend">Peso Morto</legend>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <button type="button" class="btn btn-primary aba_totais">Próximo </button>
                                                        
                                                        <button type="button" class="btn btn-info pull-right exibe_tela_dados">Voltar</button> 
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <p class="text-primary data_venda"></p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p class="text-primary local_venda"></p>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <p class="text-primary cliente_venda"></p>
                                                    </div>
                                                </div>

                                            <!--    <hr align="center" class="linha_escondida">-->

                                                <div class="row linha_escondida">
                                                    <div class="form-group col-md-1" hidden="">
                                                        <label for="categoria_morto" class="control-label"><span class="required">*</span> Categoria</label>
                                                        <select class="form-control input-sm" id="categoria_morto" name="categoria_morto">
                                                        
                                                        <option value="000">...</option>      
                                                        <?php while($reg_catagoria = mysqli_fetch_object($categoria_morto)) { ?>

                                                            <option value="<?php 
                                                                echo $reg_catagoria->tab_codigo_categoria_idade ?>">
                                                               
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
                                                        <label for="qtd_morto" class="control-label"><span class="required">*</span> Qtde Animais</label>
                                                        <input type="number" name="qtd_morto" id="qtd_morto" class="form-control input-sm" onkeypress = "return desabilita_enter (this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="sexo_morto" class="control-label"><span class="required">*</span> Sexo</label>
                                                        <select class="form-control input-sm" id="sexo_morto" name="sexo_morto" onkeypress="return numeros(this, event)">
                                                        
                                                        <option value="">...</option>      
                                                        <option value="M">Macho</option>      
                                                        <option value="F">Fêmea</option>      
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="peso_categoria_morto" class="control-label">Peso Vivo kg</label>
                                                        <input type="text" name="peso_categoria_morto" id="peso_categoria_morto" class="form-control input-sm" placeholder="0,00" 
                                                        oninput="digita_valor()" 
                                                        onblur="exibe_peso_categoria_morto()"
                                                        onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="peso_categoria_ajustado_morto" class="control-label">Peso Ajustado kg</label>
                                                        <input type="text" name="peso_categoria_ajustado_morto" id="peso_categoria_ajustado_morto" class="form-control input-sm" placeholder="0,00" 
                                                        oninput="digita_valor()" 
                                                        onblur="exibe_peso_categoria_ajustado_morto()"
                                                        onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="valor_unitario_morto" class="control-label"><span class="required">*</span>Vlr Unitário Negociado</label>
                                                        <input name="valor_unitario_morto" type="text" class="form-control input-sm" id="valor_unitario_morto" placeholder="0,00" oninput="digita_valor()" onblur="exibe_valor_unitario_morto()"
                                                        onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="unidade_morto" class="control-label"><span class="required">*</span> Und Negociada</label>
                                                        <select class="form-control input-sm" id="unidade_morto" name="unidade_morto"
                                                        onkeypress="return numeros(this, event)">
                                                        
                                                        <option value="">...</option>      
                                                        <option value="1">@</option>      
                                                        <option value="2">Kg</option>      
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row linha_escondida">

                                                    <div class="form-group col-md-2">
                                                        <label for="peso_abate_morto" class="control-label"><span class="required">*</span> Peso Morto kg</label>
                                                        <input type="text" name="peso_abate_morto" id="peso_abate_morto" class="form-control input-sm" placeholder="0,00" 
                                                        oninput="digita_valor()" 
                                                        onblur="exibe_peso_abate_morto()"
                                                        onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="arroba_abate_morto" class="control-label"><span class="required">*</span> Peso Morto @</label>
                                                        <input type="text" name="arroba_abate_morto" id="arroba_abate_morto" class="form-control input-sm" aria-describedby="arrobamortoHelpBlock" placeholder="0,00" 
                                                        oninput="digita_valor()" 
                                                        onblur="exibe_arroba_abate_morto"
                                                        onkeypress="return numeros(this, event)">
                                                        <small id="arrobamortoHelpBlock" class="form-text text-muted" style="color: #808080"></small>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="total_morto" class="control-label">Valor Total</label>
                                                        <input name="total_morto" type="text" class="form-control input-sm" id="total_morto" placeholder="0,00" oninput="digita_valor()" onblur="exibe_valor_total_morto()" onkeypress="return numeros(this, event)" aria-describedby="totalmortoHelpBlock">
                                                        <small id="totalmortoHelpBlock" class="form-text text-muted" style="color: #808080">(Sem o desconto de impostos)</small>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="rendimento_morto" class="control-label">% Rendimento Médio da Carcaça</label>
                                                        <input name="rendimento_morto" type="text" class="form-control input-sm" id="rendimento_morto" readonly="">
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="conta_morto" class="control-label"><span class="required">*</span> Conta Contabil</label>
                                                        <select class="form-control input-sm" id="conta_morto" name="conta_morto" >
                                                        <option value="0000000" selected="selected">...</option>

                                                        <?php while($reg_conta = mysqli_fetch_object($tbl_conta_contabil_morto)) { ?>

                                                        <option value="<?php 
                                                            echo $reg_conta->tbl_plano_contas_codigo_id  ?>">
                                                                                            
                                                        <?php 
                                                            echo $reg_conta->tbl_plano_contas_descricao;
                                                        ?>
                                                        </option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">    
                                                    <div class="form-group col-md-2">
                                                        <button type="button" class="form-control input-sm btn btn-success incluir" onclick="salvar_item_animal_morto()">Confirma</button> 
                                                        <button type="button" class="form-control input-sm btn btn-success editar" hidden="" onclick="salvar_editar_item_morto()">Confirma Edição</button> 
                                                    </div>
                                                </div>

                                            <!--    <hr align="center" class="linha_escondida">-->

                                                <table class="table table-striped table-advance table-hover tabela_itens_morto" id="tabela_itens_morto" width="100%" style="font-size: 11px;" hidden="">
                                                    <thead>
                                                        <tr>
                                                            <!--<th> Categoria</th>-->
                                                            <th> Qtde Animais</th>
                                                            <th> Sexo</th>
                                                            <th> Peso Vivo Kg</th>
                                                            <th> Peso Ajustado Kg</th>
                                                            <th> Peso Morto Kg</th>
                                                            <th> Peso Morto @</th>
                                                            <th> Und Negociada</th>
                                                            <th> Valor Unitário</th>
                                                            <!--<th> Total Und Negociada</th>-->
                                                            <th> Valor Total</th>
                                                            <th> Rendimento Carcaça</th>
                                                            <th> <i class="icon_cogs"></i> Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </fieldset>

                                            <fieldset class="scheduler-border tela_cabeca" hidden="">
                                                <legend class="scheduler-border fonte-legend">Cabeça</legend>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <button type="button" class="btn btn-primary aba_totais">Próximo </button>
                                                        
                                                        <button type="button" class="btn btn-info pull-right exibe_tela_dados">Voltar</button> 
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <p class="text-primary data_venda"></p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p class="text-primary local_venda"></p>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <p class="text-primary cliente_venda"></p>
                                                    </div>
                                                </div>

                                            <!--    <hr align="center" class="linha_escondida">-->

                                                <div class="row linha_escondida">
                                                    <div class="form-group col-md-2">
                                                        <label for="categoria_cabeca" class="control-label"><span class="required">*</span> Categoria</label>
                                                        <select class="form-control input-sm" id="categoria_cabeca" name="categoria_cabeca">
                                                        
                                                        <option value="000">...</option>      
                                                        <?php while($reg_catagoria = mysqli_fetch_object($categoria_cabeca)) { ?>

                                                            <option value="<?php 
                                                                echo $reg_catagoria->tab_codigo_categoria_idade ?>">
                                                               
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
                                                        <label for="sexo_cabeca" class="control-label"><span class="required">*</span> Sexo</label>
                                                        <select class="form-control input-sm" id="sexo_cabeca" name="sexo_cabeca">
                                                        
                                                        <option value="">...</option>      
                                                        <option value="M">Macho</option>      
                                                        <option value="F">Fêmea</option>      
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="qtd_cabeca" class="control-label"><span class="required">*</span> Qtde Animais</label>
                                                        <input type="number" name="qtd_cabeca" id="qtd_cabeca" class="form-control input-sm" onkeypress="return desabilita_enter (this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="peso_cabeca" class="control-label"><span class="required">*</span> Peso kg</label>
                                                        <input type="text" name="peso_cabeca" id="peso_cabeca" class="form-control input-sm" placeholder="0,00" 
                                                        onkeypress="return numeros(this, event)" 
                                                        onblur="exibe_peso_cabeca()" oninput="digita_valor()">
                                                    </div>

                                                </div>

                                                <div class="row linha_escondida">
                                                    <div class="form-group col-md-2">
                                                        <label for="valor_unitario_cabeca" class="control-label"><span class="required">*</span>Valor Unitário</label>
                                                        <input name="valor_unitario_cabeca" type="text" class="form-control input-sm" id="valor_unitario_cabeca" placeholder="0,00" oninput="digita_valor()" onblur="exibe_valor_unitario_cabeca()"
                                                        onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="total_cabeca" class="control-label">Valor Total</label>
                                                        <input name="total_cabeca" type="text" class="form-control input-sm" id="total_cabeca" placeholder="0,00" readonly="">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="arroba_cabeca" class="control-label">R$/@ Aproximado</label>
                                                        <input name="arroba_cabeca" type="text" class="form-control input-sm" id="arroba_cabeca" placeholder="0,00" readonly="">

                                                        <input type="hidden" name="peso_cabeca" id="peso_cabeca">
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="conta_cabeca" class="control-label"><span class="required">*</span> Conta Contabil</label>
                                                        <select class="form-control input-sm" id="conta_cabeca" name="conta_cabeca" >
                                                        <option value="0000000" selected="selected">...</option>

                                                        <?php while($reg_conta = mysqli_fetch_object($tbl_conta_contabil_cabeca)) { ?>

                                                        <option value="<?php 
                                                            echo $reg_conta->tbl_plano_contas_codigo_id  ?>">
                                                                                            
                                                        <?php 
                                                            echo $reg_conta->tbl_plano_contas_descricao;
                                                        ?>
                                                        </option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label class="control-label">&nbsp;</label>
                                                        <button type="button" class="form-control input-sm btn btn-success incluir" onclick="salvar_item_animal_cabeca()">Confirma</button> 
                                                        <button type="button" class="form-control input-sm btn btn-success editar" hidden="" onclick="salvar_editar_item_cabeca()">Confirma Edição</button> 
                                                    </div>
                                                </div>

                                            <!--    <hr align="center" class="linha_escondida">-->

                                                <table class="table table-striped table-advance table-hover tabela_itens_cabeca" id="tabela_itens_cabeca" width="100%" style="font-size: 13px;" hidden="">
                                                    <thead>
                                                        <tr>
                                                            <th> Categoria</th>
                                                            <th> Sexo</th>
                                                            <th> Qtde Animais</th>
                                                            <th> Peso</th>
                                                            <th> Valor Unitário</th>
                                                            <th> Valor Total</th>
                                                            <th> R$/@ Aproximado</th>
                                                            <th> <i class="icon_cogs"></i> Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </fieldset>

                                            <input type="hidden" name="array_itens" id="array_itens">

                                        </div>

                                        <div id="totais" class="tab-pane">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary gravar_venda" onclick="gravar_venda()">Confirmar </button> 

                                                    <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-3">
                                                    <p class="text-primary data_venda"></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="text-primary local_venda"></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="text-primary cliente_venda"></p>
                                                </div>

                                                <div class="col-md-3">
                                                    <p class="text-primary tipo_venda"></p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label for="total_venda" class="control-label">Total da Venda</label>
                                                    <input name="total_venda" type="text" class="form-control input-sm" id="total_venda" readonly="">
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label for="desconto_final" class="control-label">Desconto Final</label>
                                                    <input name="desconto_final" type="text" class="form-control input-sm" id="desconto_final" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_desconto_final()">
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label for="total_receber" class="control-label">Total a Receber</label>
                                                    <input name="total_receber" type="text" class="form-control input-sm" id="total_receber" readonly="">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="conta_contabil" class="control-label"><span class="required">*</span> Conta Contabil</label>
                                                    <select class="form-control input-sm" id="conta_contabil" name="conta_contabil" >
                                                    <option value="0" selected="selected">...</option>

                                                    <?php while($reg_conta = mysqli_fetch_object($tbl_conta_contabil)) { ?>

                                                    <option value="<?php 
                                                        echo $reg_conta->tbl_plano_contas_codigo_id  ?>">
                                                                                        
                                                    <?php 
                                                        echo $reg_conta->tbl_plano_contas_descricao;
                                                    ?>
                                                    </option>
                                                    <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="centro_custos" class="control-label"> Centro de Custos</label>
                                                    <select class="form-control input-sm" id="centro_custos" name="centro_custos" >

                                                    <?php while($reg_cc = mysqli_fetch_object($tbl_cc)) { ?>

                                                    <option value="<?php 
                                                        echo $reg_cc->tbl_cc_codigo_id   ?>">
                                                                                        
                                                    <?php 
                                                        echo $reg_cc->tbl_cc_descricao;
                                                    ?>
                                                    </option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="scheduler-border">
                                                        <legend class="scheduler-border fonte-legend">1ª Parcela ou Parcela Única</legend>

                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <label for="valor_pri_parcela" class="control-label"><span class="required">*</span> Valor</label>
                                                                <input name="valor_pri_parcela" type="text" class="form-control input-sm" id="valor_pri_parcela" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_pri_parcela()">
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="vencimento_pri_parcela" class="control-label"><span class="required">*</span> Vencimento</label>
                                                                <input name="vencimento_pri_parcela" type="date" class="form-control input-sm" id="vencimento_pri_parcela">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <label for="forma_pri" class="control-label"><span class="required">*</span> Forma de Pagamento</label>
                                                                <select class="form-control input-sm" id="forma_pri" name="forma_pri" >
                                                                <option value="000" selected="selected">...</option>

                                                                <?php while($reg_forma_pag = mysqli_fetch_object($forma_pri)) { ?>

                                                                <option value="<?php 
                                                                    echo $reg_forma_pag->tbl_forma_pagamento_id ?>">
                                                                                                
                                                                    <?php 
                                                                    echo $reg_forma_pag->tbl_forma_pagamento_descricao    ;
                                                                                              ?>
                                                                </option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="conta_pri" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                                                <select class="form-control input-sm" id="conta_pri" name="conta_pri" >
                                                                <option value="0" selected="selected">...</option>

                                                            <?php
while ($ln = mysqli_fetch_object($conta_pri)) {
    $codigo_conta = $ln->tbl_conta_pagamento_id;
    $nome_banco = $ln->tbl_conta_pagamento_descricao;
    $agencia = $ln->tbl_conta_pagamento_agencia;
    $conta = $ln->tbl_conta_pagamento_conta;
    
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';
    
    echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
         '</option>';
}
?>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-6">
                                                    <fieldset class="scheduler-border">
                                                        <legend class="scheduler-border fonte-legend">Restante da Parcelas</legend>
                                                        <div id="itens" class="tab-pane" style="padding-bottom: 38px;">
                                                            <div class="row">  
                                                                <div class="form-group col-md-12">
                                                                    <a class='btn btnAdicionar' href='#' style="font-size: 16px" onclick="modal_inserir_parcela()">
                                                                    <i class='fa fa-plus'></i>&nbsp;Inserir Parcelas
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <table class="table table-advance table-hover" id="tabela_parcelas" width="100%" style="font-size: 10px;">

                                                                <thead>
                                                                    <tr>
                                                                        <th> Prazo (dias)</th>
                                                                        <th style="text-align: right"> Valor</th>
                                                                        <th> Forma Pgto</th>
                                                                        <th> Conta Pgto</th>
                                                                        <th> <i class="icon_cogs"></i> Ações</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                </tbody>
                                                            </table>

                                                            <input type="hidden" name="array_parcelas" id="array_parcelas">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <fieldset class="scheduler-border">
                                                <legend class="scheduler-border fonte-legend">Transporte</legend>
                                                <div class="row">
                                                    <div class="form-group col-md-2">
                                                        <label for="gta" class="control-label">Nº GTA</label>
                                                        <input name="gta" type="text" class="form-control input-sm" id="gta" >
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="transportadora" class="control-label">Transportadora</label>
                                                        <input name="transportadora" type="text" class="form-control input-sm" id="transportadora" >
                                                    </div>

                                                    <div class="form-group col-md-7">
                                                        <label for="nome_motorista" class="control-label">Motorista/CPF/Telefone</label>
                                                        <input name="nome_motorista" type="text" class="form-control input-sm" id="nome_motorista">
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary gravar_venda" onclick="gravar_venda()">Confirmar </button>

                                                    <button type="button" class="btn btn-info pull-right aba_dados">Voltar</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </section>

        <div class="modal fade modal_inserir_parcela" tabindex="-1" role="dialog" 
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="exampleModalLabel">Inserir Parcela</h4>
                    </div>

                    <div class="modal-body">
                        <form>
                            <div class="alert alert-danger alert_erro_parcela" hidden="true">
                                <strong class="negrito"></strong><span></span>
                            </div> 

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="prazo" class="control-label"><span class="required">*</span> Prazo (dias)</label>
                                    <input name="prazo" type="text" class="form-control input-sm" id="prazo" onkeypress = "return desabilita_enter (this, event)">
                                </div>    

                                <div class="form-group col-md-6">
                                    <label for="valor_parcela" class="control-label"><span class="required">*</span> Valor Parcela</label>
                                    <input name="valor_parcela" type="text" class="form-control" id="valor_parcela" placeholder="0,00"
                                            onkeypress='digita_valor()' onblur="exibe_valor_parcela()">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="forma_parcela" class="control-label"><span class="required">*</span> Forma de Pagamento</label>
                                    <select class="form-control input-sm" id="forma_parcela" name="forma_parcela" >
                                    <option value="000" selected="selected">...</option>

                                    <?php while($reg_forma_pag = mysqli_fetch_object($forma_parcela)) { ?>
                                        <option value="<?php 
                                            echo $reg_forma_pag->tbl_forma_pagamento_id ?>">
                                                                                            
                                            <?php 
                                                echo $reg_forma_pag->tbl_forma_pagamento_descricao;
                                            ?>
                                        </option>
                                    <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="conta_parcela" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                    <select class="form-control input-sm" id="conta_parcela" name="conta_parcela" >
                                    <option value="0" selected="selected">...</option>

                                    <?php 
while ($ln = mysqli_fetch_object($conta_parcela)) {
    $codigo_conta = $ln->tbl_conta_pagamento_id;
    $nome_banco = $ln->tbl_conta_pagamento_descricao;
    $agencia = $ln->tbl_conta_pagamento_agencia;
    $conta = $ln->tbl_conta_pagamento_conta;
    
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';
    
    echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
         '</option>';
}
?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <button type="button" class="btn btn-primary pull-left" id="inserir" onClick="salvar()">Confirme</button>

                                    <button type="button" class="btn btn-primary pull-left" id="editar" onClick="salvar_edicao()">Confirme Edição</button>

                                    <button type="button" class="btn btn-info pull-right fecha_inserir_parcela">Fechar</button>
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
                            <h4 class="modal-title">Venda </h4>
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
                            <h4 class="modal-title">Venda - Mensagem</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                        </button>
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
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Agrolandes 2025</p></font>
     </div>
 </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->
<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="js/compra_venda.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

<script type="text/javascript" src="js/jquery.maskMoney.js" ></script>

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 

        $("input.fator_arroba_vivo").maskMoney({showSymbol:false, symbol:"", decimal:",", thousands:".", allowZero:true, precision: 6});
    });

</script>

</body>
</html>
