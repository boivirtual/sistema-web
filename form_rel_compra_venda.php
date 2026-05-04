<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
    $data_sistema = date("Y-m-d");

    @ session_start(); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where (tbl_pessoa_classe=1 or tbl_pessoa_classe=2 or tbl_pessoa_classe=4) and tbl_pessoa_lixeira=0"); 

    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $centro_custos = mysqli_query($conector, "select * from tbl_centro_custo
                                              where tbl_cc_lixeira=0");

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
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" rel="stylesheet"  crossorigin="anonymous">

  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <!--<link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">-->
  <link href="css/tabela_1300.css" rel="stylesheet">
  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

   @ session_start();  

     if(isset($_SESSION['menu_relatorios'])) {
        $array_relatorios = explode("!",$_SESSION['menu_relatorios']);

        if ($array_relatorios[2] == 0){
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

    $tipo_relatorio=$_REQUEST['tipo'];

    if ($_SESSION['data_inicial_compra_venda'] == '') {
        $data_inicial = '';
    } else {
        $data_inicial =  $_SESSION['data_inicial_compra_venda'];
    }

    if ($_SESSION['data_final_compra_venda'] == '') {
        $data_final = '';
    } else {
        $data_final =  $_SESSION['data_final_compra_venda'];
    }

    $tipo_compra_venda = $_SESSION['tipo_compra_venda'];
    $local_origem = $_SESSION['local_origem_compra_venda']; 
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php";
        include "limpar_secao_ctp.php";
        include "limpar_secao_ctr.php";
        include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

  <!-- container section start -->
  <section id="container" class="">

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_financeiros.php">Relatórios Financeiros</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Compras/Vendas</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Compras/Vendas</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                        <div class="row col-md-12" id="consulta_contas">
                            <form method="GET" action="#" enctype="multipart/form-data" >
                            
                                <div class="tab-panel ">
                                    <div class="tab-pane active">
                                        <fieldset class="scheduler-border " >
                                            <legend class="scheduler-border fonte-legend">Filtros</legend>
                                                
                                            <div class="row">
                                                <div class="form-group col-md-11">
                                                    <input type="hidden" id="tipo_relatorio" <?php echo "value='".$tipo_relatorio."'";?>>                    
                                                    <input id="exibe_local_origem" type="hidden" <?php echo "value='".$local_origem."'"; ?>>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <button type="button" class="form-control btn btn-info pull-right" onclick="voltar_relatorios()">Voltar
                                                    </button>
                                                </div>
                                            </div>                                                
                                            <div class="row ">
                                                <div class="form-group col-md-4">
                                                    <input type="hidden" name="lista_ao_entrar" id="lista_ao_entrar" value="S">  
                                                    <label for="data_inicial" class="control-label"><span class="required">*</span> Data Incial</label>
                                                    <input name="data_inicial" type="date" class="form-control" id="data_inicial" <?php echo "value='" . $data_inicial . "'"; ?>>

                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="data_final" class="control-label"><span class="required">*</span> Data Final</label>
                                                    <input name="data_final" type="date" class="form-control" id="data_final" <?php echo "value='" . $data_final . "'"; ?>>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="tipo_rel" class="control-label"><span class="required">*</span> Tipo do Relatório</label>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="tipo_rel" class="control-label">Tipo do Relatório:</label> 

                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_rel" value="2" <?php if ($tipo_compra_venda == 2) {
                                                            echo "checked";
                                                            } ?>> Compras
                                                    </label>
                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_rel" value="1" <?php if ($tipo_compra_venda == 1) {
                                                            echo "checked";
                                                            } ?>> Vendas
                                                    </label>
                                                </div>    
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="codigo_local" class="control-label">Fazenda</label>
                                                    <select class="form-control selectpicker" id="codigo_local" multiple name="codigo_local">

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
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="codigo_cc" class="control-label"> Centro de Custos</label>

                                                    <select class="form-control selectpicker" id="codigo_cc" name="codigo_cc" data-live-search="true" multiple style="z-index:5;">

                                                    <?php while ($reg_cc = mysqli_fetch_object($centro_custos)) { ?>

                                                        <option value="<?php
                                                        echo $reg_cc->tbl_cc_codigo_id ?>">

                                                        <?php
                                                        echo $reg_cc->tbl_cc_descricao;
                                                                ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-primary pull-right" onclick="listar_compra_venda_tela(1)">Listar
                                                    </button>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-success pull-right" onClick="listar_compra_venda_tela(2)">Excel
                                                    </button>
                                                </div>

                                                <!--<div class="form-group col-md-2">
                                                </div>

                                                <div class="form-group col-md-1 esconder" hidden="">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-info pull-right "  data-toggle="tooltip" data-placement="top" title="Minimizar tela filtros" onclick="esconder_filtro()"><i class="fa fa-sort-down"></i>&nbsp;<i class="fa fa-filter"></i>
                                                    </button>
                                                </div>-->
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                        </div> 

                    <div id="lista_compra_venda"></div>

    	        </div>
	        </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Compras/Vendas - Mensagem</h4>
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
	        <!-- page end-->
        </section>
    </section>

    <div class="text-center">
        <div class="credits">
            <font size="2"><p style="color:#C0C0C0">Copyright &copy; Agrolandes 2024</p></font>
        </div>
    </div>


</section> <!-- container section start end -->
  
<!-- javascripts -->

<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="js/compra_venda.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
</script>

<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

</body>
</html>


