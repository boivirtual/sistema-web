<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");

    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>
  <link rel="stylesheet" href="css/select-1.13.14.css"> 

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
  
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style type="text/css">
        .bootstrap-select {
          width: 240px !important;
        }
  </style>

</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_cadastro[3] == 0){
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

    $tbl_local_distribuir = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

    if ($_SESSION['data_inicial_nutricao']==''){
        $data_inicial = $ano . '-' . $mes . '-01';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_nutricao'];  
    }

    if ($_SESSION['data_final_nutricao']==''){
        $data_final = $ano . '-' . $mes . '-' . $dias_mes;
    }
    else {
        $data_final =  $_SESSION['data_final_nutricao'];   
    }

    $array_local= $_SESSION['local_nutricao'];
    $array_produto= $_SESSION['produto_nutricao'];

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
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php";
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Nutrição</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/nutricao.png"> Nutrição</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="form_mapa_gados.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Distribuir Nutrição"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_produtos.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Distribuição e Consumo de Nutrição</legend>

                                        <div class="row primeiro_filtro">
                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <div class="form-group col-md-3">
                                                <label for="data_inicial" class="control-label">Data Inicial</label>

                                                <input type="date" name="data_inicial" id="data_inicial" class="form-control"
                                                    <?php echo "value='".$data_inicial."'";?>>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="data_final" class="control-label">Data Final</label>
                                                <input name="data_final" type="date" class="form-control" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="codigo_local" class="control-label"><span class="required">*</span> Fazenda</label>
                                                <select class="form-control" id="codigo_local"  name="codigo_local">

                                                <?php 
                                                    if ($qtd_locais_usuario!=1) {
                                                        echo '<option value="000000000">...</option>';
                                                    }

                                                    while($reg_local = mysqli_fetch_object($tbl_local)) { 
                                                    
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

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="consultar_primeiro_filtro()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row filtros_consulta" hidden>
                                            <div class="col-md-10">
                                                <p style="font-size: 12px; color: #829c9c">Filtros: 
                                                    <span class="descricao_filtro" style="font-weight: normal;">
                                                    </span>

                                                    <span class="mais_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Exibir Filtros" onclick="exibe_mais_filtros()"> 
                                                            <i class="fas fa-filter"></i> +
                                                        </a>
                                                    </span>

                                                    <span class="menos_filtros" hidden>&nbsp;
                                                        <a href="#" data-toggle='tooltip' data-placement='top' title="Esconder Filtros" onclick="exibe_menos_filtros()"> 
                                                            <i class="fas fa-filter"></i> -
                                                        </a>
                                                    </span>
                                                </p>
                                            </div>

                                        </div> 
                                        
                                        <div class="row segundo_filtro" hidden>
                                            <div class="form-group col-md-3">
                                                <label for="descricao_lote" class="control-label">Descriçao do Lote</label>
                                                <select class="form-control selectpicker" multiple id="descricao_lote" name="descricao_lote" data-live-search="true">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="codigo_pasto" class="control-label">Pasto</label>
                                                <select class="form-control selectpicker" multiple id="codigo_pasto" name="codigo_pasto" data-live-search="true">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="codigo_produto" class="control-label">Produto</label>
                                                <select class="form-control" id="codigo_produto" name="codigo_produto" data-live-search="true">
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="consultar_segundo_filtro()">Consultar</button>
                                            </div>
                                        </div>

                                        <div class="row segundo_filtro" hidden>
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <a href="#" style="font-size: 0.9em; font-weight: 600; text-align: right; color: #128cb8; float: right;" onclick="relatorio_consumo_nutricao()" data-toggle='tooltip' data-placement='top' title="Verificar aqui o que escrever"><i class="fa fa-plus"></i> Relatórios</a>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>    

                    <div id="lista_nutricao">
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_nutricao" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Nutrição - Editar</h4>
                        </div>

                        <div class="modal-body" >
                            <form method="POST" action="gravar_nutricao.php" enctype="multipart/form-data" id="form_gravar_nutricao">
                                <input name="id_nutricao" type="hidden" id="id_nutricao">
                                <input name="id_produto" type="hidden" id="id_produto">
                                <input name="id_local" type="hidden" id="id_local">
                                <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">
                              
                                <ul class="nav nav-tabs m-bot15">
                                    <li class="active">
                                        <a data-toggle="tab" href="#dados">Dados</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="dados" class="tab-pane active">

<!--                                        <div class="row">  
                                          <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_nutricao()">Confirmar Edição</button>
                                            <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
                                          </div>
                                        </div> -->

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="descricao_local" class="control-label">Local</label>

                                                <input name="descricao_local" type="text" class="form-control" id="descricao_local" readonly>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="descricao_pasto" class="control-label">Pasto </label>

                                                <input name="descricao_pasto" id="descricao_pasto" type="text" class="form-control" readonly>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="qtd_cabecas" class="control-label">Qtde Cabeças </label>

                                                <input name="qtd_cabecas" id="qtd_cabecas" type="text" class="form-control" readonly>
                                            </div>
                                        </div>
                                  
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="descricao_cocho" class="control-label">Situação do Cocho</label>

                                                <input name="descricao_cocho" type="text" class="form-control" id="descricao_cocho" readonly>
                                            </div>

                                            <div class="form-group col-md-8">
                                                <label for="descricao_produto" class="control-label">Produto</label>

                                                <input name="descricao_produto" type="text" class="form-control" id="descricao_produto" readonly>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="quantidade" class="control-label">Quantidade (<span id="apresentacao_estoque"></span>)</label>
                                                <input name="quantidade" type="text" class="form-control" id="quantidade" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_quantidade()">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="qtd_media" class="control-label">Média/Cabeças </label>
                                                <input name="qtd_media" type="text" class="form-control" id="qtd_media" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_qtd_media()">
                                            </div>
                                        </div>

                                        <div class="row m-bot15">
                                            <div class="col-md-12">
                                                <label for="observacao" class="control-label">Descrição do Lote</label>
                                                <textarea name="observacao" type="text" class="form-control" id="observacao" rows="3" onkeyup="maiuscula(this)"></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12" style="font-size: 10px;">
                                                <label>Incluído por: </label>
                                                <span id="incluido_por" style="color: green"></span>
                                                &nbsp; 
                                                <span id="incluido_em" style="color: green; margin-right: 10px"></span>

                                                <label class="registro_alterado"> Alterado por: </label>
                                                <span class="registro_alterado" id="alterado_por" style="color: red"></span>
                                                &nbsp;
                                                <span class="registro_alterado" id="alterado_em" style="color: red; margin-right: 10px"></span>
                                            </div>
                                        </div>

                                        <div class="row">  
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_nutricao()">Confirmar Inclusão</button>
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
	        <!-- page end-->

<!--
            <div class="modal fade" id="distribuir_nutricao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Distribuir Nutrição </h4>
                        </div>
                        <div class="modal-body">
                            
                            <div class="row">    
                                <div class="form-group col-md-6">
                                    <label for="codigo_local_distribuir" class="control-label">Local</label>
                                    <select class="form-control" id="codigo_local_distribuir"  name="codigo_local_distribuir">

                                    <option value="000000000">...</option>
                                    <?php 
                                        while($reg_local = mysqli_fetch_object($tbl_local_distribuir)) { 
                                                    
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
                                    <label for="codigo_pasto_distribuir" class="control-label">Pasto</label>
                                    <select class="form-control" id="codigo_pasto_distribuir" name="codigo_pasto_distribuir">
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <button class="btn btn-primary" type="button" onclick="carregar_mapa_gado();">Distribuir</button>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
-->
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

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Nutrição </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
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
                            <h4 class="modal-title">Nutrição - Mensagem</h4>
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

 <!--main content end-->
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

<script src="js/nutricao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao; ?>"></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>

</html>