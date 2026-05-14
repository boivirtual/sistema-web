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

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

 <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet"> 
 <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">
</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_cadastros'])) {
        $array_cadastro = explode("!",$_SESSION['menu_cadastros']);

        if ($array_cadastro[4] == 0){
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
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cadastros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Produtos</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-tags"></i> Cadastro de Produtos</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Novo" onclick="incluir_novo()"/>
                        </a>
                    </div> 

                    <div id="lista_produtos">
                    </div>
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Produtos - Incluir</h4>
                        </div>

                        <div class="modal-body" >
                            <form method="POST" action="gravar_produtos.php" enctype="multipart/form-data" id="form_gravar_produto">
                                <input name="codigo_produto" type="hidden" id="codigo_produto">
                                <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                              
                                <input name="descricao_anterior" type="hidden" id="descricao_anterior">
                                <input name="apresentacao_anterior" type="hidden" id="apresentacao_anterior">
                                <input name="qtd_anterior" type="hidden" id="qtd_anterior">
                                <input name="unidade_anterior" type="hidden" id="unidade_anterior">

                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">

                                        <div class="row">  
                                          <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_produtos()">Confirmar Inclusão</button>
                                            <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
                                          </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="grupo" class="control-label"><span class="required">*</span> Modalidade</label>
                                                <select class="form-control" id="grupo" name="grupo" required="">
                                                    <?php
                                                        $sql = "select * FROM tbl_modalidade_produto where tbl_modalidade_lixeira=0";  
                                                        $qr = mysqli_query($conector, $sql);
                                                    
                                                        if(mysqli_num_rows($qr) == 0){
                                                            echo  '<option value="">'.htmlentities('Sem modalidades cadastradas').'</option>';
                                                        }
                                                        else{
                                                            while($ln = mysqli_fetch_assoc($qr)){
                                                                $codigo_gru=$ln['tbl_codigo_modalidade'];
                                                                $desc_gru=$ln['tbl_descricao_modalidade'];
                                                                echo '<option value="'.$codigo_gru.'">'.$desc_gru.'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="descricao_padrao" class="control-label">Descrição Padrão</label>
                                                <select class="form-control" id="descricao_padrao" name="descricao_padrao">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="descricao_complementar" class="control-label">Complemento da Descrição </label>
                                                <input name="descricao_complementar" type="text" class="form-control" id="descricao_complementar" onkeyup="maiuscula(this)">
                                            </div>
                                        </div>
                                  
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="apresentacao" class="control-label">Apresentação</label>
                                                <select class="form-control" id="apresentacao" name="apresentacao">
                                                    <?php
                                                        $sql = "select * FROM tbl_apresentacao_produtos";  
                                                        $qr = mysqli_query($conector, $sql);
                                                    
                                                        if(mysqli_num_rows($qr) == 0){
                                                            echo  '<option value="">'.htmlentities('Sem apresentações cadastradas').'</option>';
                                                        }
                                                        else{
                                                            while($ln = mysqli_fetch_assoc($qr)){
                                                                $codigo_apr=$ln['tab_codigo_apresentacao_id'];
                                                                $desc_apr=$ln['tab_descricao_apresentacao_produtos'];
                                                                echo '<option value="'.$codigo_apr.'">'.$desc_apr.'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="qtd_uni" class="control-label">Quantidade/Apresentação</label>
                                                <input name="qtd_uni" type="text" class="form-control" id="qtd_uni" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_qtd_uni()">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="unidade" class="control-label">Unidade</label>
                                                <select class="form-control" id="unidade" name="unidade">
                                                    <?php
                                                        $sql = "select * FROM tabela_unidade_produtos";  
                                                        $qr = mysqli_query($conector, $sql);
                                                    
                                                        if(mysqli_num_rows($qr) == 0){
                                                            echo  '<option value="">'.htmlentities('Sem unidades cadastradas').'</option>';
                                                        }
                                                        else{
                                                    
                                                            while($ln = mysqli_fetch_assoc($qr)){
                                                                $codigo_apr=$ln['tab_codigo_unidade_id'];
                                                                $desc_apr=$ln['tab_codigo_unidade_produtos'];
                                                                echo '<option value="'.$codigo_apr.'">'.$desc_apr.'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row m-bot15">
                                            <div class="col-md-12">
                                                <label for="observacao" class="control-label">Observação</label>
                                                <textarea name="observacao" type="text" class="form-control" id="observacao" rows="3" onkeyup="maiuscula(this)"></textarea>
                                            </div>
                                        </div>

                                        <fieldset class="scheduler-border" id="dados_consulta" style="font-size: 12px;">
                                            <legend class="scheduler-border fonte-legend">Estoques</legend>

                                            <div class="row">        
                                                <div class="col-md-12">

                                                    <div class="col-md-3">
                                                        <label class="control-label">Fazenda(s)</label>
                                                    </div>

                                                    <div class="col-md-3 label-center">
                                                        <label for="qtd_entrada" class="control-label">Entrada no Estoque (<span class="apresentacao_estoque"></span>)</label>
                                                    </div>

                                                    <div class="col-md-3 label-center">
                                                        <label for="qtd_estoque_atual_apr" class="control-label">Estoque Atual (<span class="apresentacao_estoque"></span>)</label>
                                                    </div>

                                                    <div class="col-md-3 label-center">
                                                        <label for="qtd_estoque_atual" class="control-label">Estoque Atual (<span class="apresentacao_estoque_atual"></span>)</label>
                                                    </div>
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
                                                        <div class="form-group col-md-12">
                                                            <div class="form-group col-md-3">
                                                                <input name="codigo_fazenda" type="hidden" value="'.$codigo_id.'">

                                                            <input name="nome_fazenda" type="hidden" value="'.$nome_fazenda.'">
                                                            <span>'.$nome_fazenda.'</span>
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <input name="qtd_entrada" type="text" class="form-control qtd_entrada"  placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_qtd_entrada()">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <input name="qtd_estoque_atual_apr" type="text" class="form-control"  readonly>
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <input name="qtd_estoque_atual" type="text" class="form-control"  readonly>

                                                                <input name="qtd_estoque_anterior" type="hidden" value="0" class="form-control" >
                                                            </div>
                                                        </div>
                                                        </div>';
                                                    }
                                                }
                                            }
                                        ?> 

                                            <div class="row exibe_totais">        
                                                <div class="col-md-12">

                                                    <div class="col-md-3">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <span style="font-size: 13px; font-weight: bold;">Estoque Total Fazenda(s):</span>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <input type="text" id="total_estoque_apr" class="form-control" readonly style="font-weight: bold; background-color: #F5FFFA;">

                                                       <!-- <span id="total_estoque_apr" style="font-size: 13px; font-weight: bold; padding-left: 12px;"></span>-->
                                                    </div>

                                                    <div class="col-md-3">
                                                        <input type="text" id="total_estoque" class="form-control" readonly style="font-weight: bold; background-color: #F5FFFA;">

                                                        <!--<span id="total_estoque" style="font-size: 13px; font-weight: bold; padding-left: 12px;"></span>-->
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>
                            
                                        <input name="array_codigo_fazenda" type="hidden" id="array_codigo_fazenda">

                                        <input name="array_estoque_atual" type="hidden" id="array_estoque_atual">

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_produtos()">Confirmar Inclusão</button>
                                                <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
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
                            <h4 class="modal-title">Produtos </h4>
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
                            <h4 class="modal-title">Produtos </h4>
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
                            <h4 class="modal-title">Produtos - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
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
                            <h4 class="modal-title">Produtos - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="cadastro_pasto();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

<?php 
  $javascript_file_name = 'tabela_produtos.js';
  require 'rodape.php';
?>


