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

  <!-- Bootstrap CSS -->
    <link href="css/jquery-ui.css" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/daterangepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
    <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />
    <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <link rel="stylesheet" href="css/select-1.13.14.css">
    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">
    <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php
    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("m");

    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

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

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    if ($_SESSION['data_inicial_movimentacao']==''){
        $data_inicial = $ano . '-' . $mes . '-01';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_movimentacao'];  
    }

    if ($_SESSION['data_final_movimentacao']==''){
        $data_final = $ano . '-' . $mes . '-' . $dias_mes;
    }
    else {
        $data_final =  $_SESSION['data_final_movimentacao'];   
    }

    $array_local= $_SESSION['local_movimentacao'];
    $array_tipo= $_SESSION['tipo_movimentacao'];

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
        include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Movimentações</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-cogs"></i> Movimentações</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group">
                        <a href="form_movimentacao_animais_incluir.php">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Nova Movimentação"/>
                        </a>
                    </div> 

                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_produtos.php" enctype="multipart/form-data" >
                            
                            <div class="tab-panel">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Consultar Movimentações</legend>

                                        <div class="row digitar_filtros">
                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <input id="lista_movimentacao_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_movimentacao'] . "'"; ?>>

                                            <input id="exibe_local" type="hidden" <?php echo "value='".$array_local."'"; ?>>

                                            <input id="id_excluir" type="hidden">

                                            <input id="id_pasto" type="hidden">

                                            <input id="descricao_pasto" type="hidden">

                                            <input id="descricao_lote" type="hidden">

                                            <div class="form-group col-md-4">
                                                <label for="data_inicial" class="control-label">Data Inicial</label>

                                                <input type="date" name="data_inicial" id="data_inicial" class="form-control"
                                                    <?php echo "value='".$data_inicial."'";?>>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="data_final" class="control-label">Data Final</label>
                                                <input name="data_final" type="date" class="form-control" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                            </div>
                                        </div>
                                        
                                        <div class="row digitar_filtros">    
                                            <div class="form-group col-md-4">
                                                <label for="codigo_local" class="control-label">Fazenda</label>
                                                <select class="form-control selectpicker" id="codigo_local" multiple name="codigo_local">
                                                <?php 
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

                                            <div class="form-group col-md-4">
                                                <label for="tipo_movimentacao" class="control-label">Movimentação</label>
                                                <select class="form-control selectpicker" multiple id="tipo_movimentacao" name="tipo_movimentacao">

                                                <option value="5"
                                                    <?php 
                                                        if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==5) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>Transferência</option>
                                                <option value="4"
                                                    <?php 
                                                        if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==4) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>Compra</option>
                                                <option value="3"
                                                    <?php 
                                                        if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==3) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>Venda</option>
                                                <option value="888"
                                                    <?php 
                                                        if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==888) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>Morte</option>
                                                <option value="881"
                                                    <?php 
                                                        if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==881) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>Natimorto</option>
                                                <option value="999"
                                                    <?php 
                                                        if ($array_tipo!="") {
                                                            foreach ($array_tipo as $value) {
                                                                if ($value==999) { 
                                                                    echo "selected";       
                                                                }
                                                            }                           
                                                        }
                                                    ?>>Outras Saídas</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label class="control-label">&nbsp;</label>
                                                <button type="button" class="form-control btn btn-info pull-right consultar" onclick="consultar()">Consultar</button>
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
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>    
                    <div id="lista_movimentacoes">
                    </div>
                </div>
            </div>
	        <!-- page end-->

            <div class="modal fade" id="modal_confirmar_venda" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Confirmar Faturamento de Venda</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data">
                                <input name="codigo_animal" type="hidden" id="codigo_animal">
                              
                            <div class="tab-content">
                                <div id="dados" class="tab-pane active">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">

                                <p class="numero_movimento"></p>
                                <input class="numero_movimento" type="hidden">

                                <table class="table table-advance table-hover" id="tabela_vendas"
                                style="font-size: 12px;">
                                <thead>
                                    <tr> 
                                        <th>Faturamento</th>
                                        <th>Data</th>
                                        <th>Origem</th>
                                        <th>Destino</th>
                                        <th align="center">Qtde Animais</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $sql = "select * from tbl_venda 
                                            inner join tbl_pessoa
                                            on tbl_venda_codigo_local_origem=tbl_pessoa_id 
                                            where tbl_venda_categoria=1 and tbl_venda_codigo_movimentacao=0"; 
                                        $rs = mysqli_query($conector, $sql); 
                                        while ($reg_venda = mysqli_fetch_object($rs)){
                                            $numero_venda = $reg_venda->tbl_venda_id;
                                            $data_venda = new DateTime($reg_venda->tbl_venda_emissao);
                                            $data_venda = $data_venda->format('d/m/Y');
                                            $desc_origem = $reg_venda->tbl_pessoa_nome;
                                            $codigo_destino = $reg_venda->tbl_venda_codigo_local_destino;

                                            $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_destino'");
                                            $reg = mysqli_fetch_object($tbl_local);
                                            $desc_destino = $reg->tbl_pessoa_nome;


                                            $tbl_itens = mysqli_query($conector, "select * from tbl_item_venda where tbl_ite_venda_numero_id ='$numero_venda'");

                                            $total_cabecas = 0;
                                            while ($reg_item = mysqli_fetch_object($tbl_itens)) {
                                                $total_cabecas+= $reg_item->tbl_ite_venda_quantidade;
                                            }

                                            echo "<tr>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_venda(\"{$numero_venda}\")'>".$numero_venda."</a></td>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_venda(\"{$numero_venda}\")'>".$data_venda."</a></td>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_venda(\"{$numero_venda}\")'>".$desc_origem."</a></td>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_venda(\"{$numero_venda}\")'>".$desc_destino."</a></td>";
                                            echo "<td align='center'><a class='' href='#' onclick='confirmar_faturamento_venda(\"{$numero_venda}\")'>".$total_cabecas."</a></td>";
                                            echo "</tr>";
                                        } 
                                    ?>
                                </tbody>
                                <tfoot>
                                </tfoot>
                                </table>  
                                </div>  
                            </div>

                            <div class="row">  
                                <div class="form-group col-md-12">
                                    <!--<button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_animais()">Confirmar Inclusão</button>-->
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_confirmar_compra" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Movimentação - Confirmar Faturamento de Compra</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="#" enctype="multipart/form-data">
                                <input name="codigo_animal" type="hidden" id="codigo_animal">
                              
                            <div class="tab-content">
                                <div id="dados" class="tab-pane active">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">

                                <p class="numero_movimento"></p>
                                <input class="numero_movimento" type="hidden">

                                <table class="table table-advance table-hover" id="tabela_vendas"
                                style="font-size: 12px;">
                                <thead>
                                    <tr> 
                                        <th>Faturamento</th>
                                        <th>Data</th>
                                        <th>Origem</th>
                                        <th>Destino</th>
                                        <th align="center">Qtde Animais</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $sql = "select * from tbl_venda 
                                            inner join tbl_pessoa
                                            on tbl_venda_codigo_local_origem=tbl_pessoa_id 
                                            where tbl_venda_categoria=2 and tbl_venda_codigo_movimentacao=0"; 
                                        $rs = mysqli_query($conector, $sql); 
                                        while ($reg_venda = mysqli_fetch_object($rs)){
                                            $numero_venda = $reg_venda->tbl_venda_id;
                                            $data_venda = new DateTime($reg_venda->tbl_venda_emissao);
                                            $data_venda = $data_venda->format('d/m/Y');
                                            $desc_origem = $reg_venda->tbl_pessoa_nome;
                                            $codigo_destino = $reg_venda->tbl_venda_codigo_local_destino;

                                            $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_destino'");
                                            $reg = mysqli_fetch_object($tbl_local);
                                            $desc_destino = $reg->tbl_pessoa_nome;


                                            $tbl_itens = mysqli_query($conector, "select * from tbl_item_venda where tbl_ite_venda_numero_id ='$numero_venda'");

                                            $total_cabecas = 0;
                                            while ($reg_item = mysqli_fetch_object($tbl_itens)) {
                                                $total_cabecas+= $reg_item->tbl_ite_venda_quantidade;
                                            }

                                            echo "<tr>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_compra(\"{$numero_venda}\")'>".$numero_venda."</a></td>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_compra(\"{$numero_venda}\")'>".$data_venda."</a></td>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_compra(\"{$numero_venda}\")'>".$desc_origem."</a></td>";
                                            echo "<td><a class='' href='#' onclick='confirmar_faturamento_compra(\"{$numero_venda}\")'>".$desc_destino."</a></td>";
                                            echo "<td align='center'><a class='' href='#' onclick='confirmar_faturamento_compra(\"{$numero_venda}\")'>".$total_cabecas."</a></td>";
                                            echo "</tr>";
                                        } 
                                    ?>
                                </tbody>
                                <tfoot>
                                </tfoot>
                                </table>  
                                </div>  
                            </div>

                            <div class="row">  
                                <div class="form-group col-md-12">
                                    <!--<button type="button" class="btn btn-primary confirma_gravar" onClick="gravar_animais()">Confirmar Inclusão</button>-->
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal" onclick="sair_inclusao()">Voltar</button>
                                </div>
                            </div>
                            </form>
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

            <div class="modal fade" id="confirma_excluir" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentações - Excluir</h4>
                        </div>

                        <div class="modal-body"></div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-success" type="button" onclick="confirmar_excluir_movimentacao()">Sim</button>

                            <button data-dismiss="modal" class="btn btn-default" type="button">Não</button>
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
                            <h4 class="modal-title">Movimentações </h4>
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
                            <h4 class="modal-title">Movimentações - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
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

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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

<script src="js/movimentacao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

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