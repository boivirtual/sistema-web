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
  <link href="css/select-1.13.14.css" rel="stylesheet" > 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <style>
        .radio-inline {
            min-width: 100px; /* Ajuste este valor conforme necessário */
            /* Opcional: Para alinhamento vertical se o texto for diferente em altura */
            vertical-align: middle;
        }
    </style>
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

    if ($_SESSION['tipo_rel_nutricao'] == '') {
        $tipo_rel = "P";
    } else {
        $tipo_rel =  $_SESSION['tipo_rel_nutricao'];
    }

    $tipo_relatorio=$_REQUEST['tipo'];

    $tbl_local_distribuir = mysqli_query($conector, "SELECT * FROM tbl_pessoa 
        WHERE tbl_pessoa_classe=4 AND tbl_pessoa_lixeira=0"); 

    $tbl_produto = mysqli_query($conector, "SELECT * FROM tbl_produto 
        INNER JOIN tabela_unidade_produtos
                ON tab_codigo_unidade_id = tbl_produto_unidade 
        INNER JOIN tbl_apresentacao_produtos
                ON tab_codigo_apresentacao_id = tbl_produto_apresentacao 
        WHERE tbl_produto_lixeira=0"); 

    if ($_SESSION['data_inicial_nutricao']==''){
        //$data_inicial = $ano . '-' . $mes . '-01';
        $data_inicial = '';
    }
    else {
        $data_inicial =  $_SESSION['data_inicial_nutricao'];  
    }

    if ($_SESSION['data_final_nutricao']==''){
        //$data_final = $ano . '-' . $mes . '-' . $dias_mes;
        $data_final = '';
    }
    else {
        $data_final =  $_SESSION['data_final_nutricao'];   
    }

    $local_nutricao= $_SESSION['local_nutricao'];
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
        include "cabecalho.php";
        include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
    ?>
    <!--sidebar end-->

  <!-- container section start -->
  <section id="container" class="">

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Consumo de Nutrição</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/nutricao.png"> Consumo de Nutrição</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                        <div class="row col-md-12 filtro_exibido" id="consulta_contas">
                            <input type="hidden" id="tipo_relatorio" <?php echo "value='".$tipo_relatorio."'";?>>

                            <form method="GET" action="#" enctype="multipart/form-data" >
                            
                                <div class="tab-panel ">
                                    <div class="tab-pane active">
                                        <fieldset class="scheduler-border " >
                                            <legend class="scheduler-border fonte-legend">Filtros</legend>

                                            <div class="row">
                                                <div class="form-group col-md-11">
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="voltar_relatorios()">Voltar
                                                    </button>
                                                </div>
                                            </div> 

                                            <div class="row filtro_relatorio">
                                                <div class="form-group col-md-3">
                                                    <label class="control-label"><span class="required">*</span> Tipo do Relatório</label>

                                                    <div class="clearfix"></div>

                                                    <label class="radio-inline" data-toggle='tooltip' data-placement='top' title="Relatório por Período, permite filtrar por um ou mais lotes.">
                                                    <input type="radio" class="tipo_rel" name="tipo_rel" value="P" <?php if ($tipo_rel == 'P') {
                                                            echo "checked";
                                                            } ?>> Por Período
                                                    </label>

                                                    <label class="radio-inline" data-toggle='tooltip' data-placement='top' title="Relatório por lote, permite filtrar por um Período.">
                                                    <input type="radio" class="tipo_rel"  name="tipo_rel" value="L" <?php if ($tipo_rel == 'L') {
                                                            echo "checked";
                                                            } ?>> Por Lote
                                                    </label>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="codigo_local" class="control-label"><span class="required">*</span> Fazenda</label>
                                                    <select class="form-control" id="codigo_local"  name="codigo_local">

                                                    <?php 
                                                        if ($qtd_locais_usuario!=1) {
                                                            echo '<option value="000000000">...</option>';
                                                        }

                                                        while($reg_local = mysqli_fetch_object($tbl_local_distribuir)) { 
                                                            foreach ($array_locais_usuario as $value) {
                                                                $value = ltrim($value);
                                                                $value = rtrim($value);

                                                                if ($value==$reg_local->tbl_pessoa_id) {
                                                                    if ($local_nutricao==$value) {
                                                                        echo '<option value="'.$value.'" selected>' .$reg_local->tbl_pessoa_nome. '</option>'; 
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
                                            
                                            </div>

                                            <div class="row filtro_relatorio">
                                                <div class="col-md-3">
                                                    <label for="data_inicial" class="control-label label_data_inicial">
                                                    * Data Inicial</label>

                                                    <input type="date" name="data_inicial" id="data_inicial" <?php echo "value='".$data_inicial."'";?> class="form-control">
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="data_final" class="control-label label_data_final">
                                                    * Data Final</label>

                                                    <input name="data_final" type="date" class="form-control" id="data_final" <?php echo "value='".$data_final."'";?>
                                                    onchange='validar_datas_consumo_nutricao()'>
                                                </div>

                                                <div class="col-md-3 descricao_lote">
                                                    <label for="descricao_lote" class="control-label">Lote(s)</label>
                                                    <select class="form-control selectpicker" multiple id="descricao_lote" name="descricao_lote" data-live-search="true">
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3 um_lote" hidden>
                                                    <label for="um_lote" class="control-label"><span class="required">*</span> Lote</label>
                                                    <select class="form-control" id="um_lote" name="um_lote">
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row filtro_relatorio">
                                                <div class="col-md-6"></div>

                                                <div class="col-md-3 descricao_lote">
                                                    <a class='informacao' href='#' onClick='limpar_selecao_lote()'>Limpa Seleção</a>
                                                </div>
                                            </div>
                                            
                                            <div class="row filtro_relatorio">
                                                <div class="col-md-3">
                                                    <label class="control-label">Pasto</label>
                                                    <select class="form-control selectpicker" multiple id="codigo_pasto" name="codigo_pasto" data-live-search="true">
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="codigo_produto" class="control-label">Produto</label>
                                                    <select class="form-control selectpicker" multiple id="codigo_produto" name="codigo_produto" data-live-search="true">
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="listar_consumo_nutricao(1)">Listar</button>
                                                </div>

                                                <div class="col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-success pull-right" onClick="listar_consumo_nutricao(2)">Excel
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row filtro_relatorio">
                                                <div class="form-group col-md-3">
                                                    <a class='informacao' href='#' onClick='limpar_selecao_pasto()'>Limpa Seleção</a>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <a class='informacao' href='#' onClick='limpar_selecao_produto()'>Limpa Seleção</a>
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

                    <div id="lista_consumo_nutricao"></div>

    	        </div>
	        </div>

	        <!-- page end-->
            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Consumo de Nutrição - Mensagem</h4>
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
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2025</p></font>
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
<script src="js/nutricao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   

        $("#data_final").click(function(){
            $('#data_final').val('');
            document.getElementById("data_final").style.borderColor = "";
            return;
        });
    });
</script>

</body>
</html>