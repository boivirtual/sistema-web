<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
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
  <link href="css/tabela_1300.css" rel="stylesheet">
  <link href="css/select-1.13.14.css" rel="stylesheet" >

<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.js"></script>-->

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

    if ($_SESSION['data_inicio_ctp_rel']==0){
        $data_inicial = $data_sistema;
    }
    else {
        $data_inicial =  $_SESSION['data_inicio_ctp_rel'];  
    }

    if ($_SESSION['data_fim_ctp_rel']==0){
        $data_final = $data_sistema;
    }
    else {
        $data_final =  $_SESSION['data_fim_ctp_rel'];   
    }

    if ($_SESSION['tipo_data_ctp_rel']==''){
        $tipo_data = "V";
    }
    else {
        $tipo_data =  $_SESSION['tipo_data_ctp_rel'];   
    }

    if ($_SESSION['tipo_rel_ctp_rel']==''){
        $tipo_rel = "S";
    }
    else {
        $tipo_rel =  $_SESSION['tipo_rel_ctp_rel'];     
    }

    $cli_for = mysqli_query($conector, "select * from tbl_pessoa 
        where tbl_pessoa_lixeira=0 and 
              tbl_pessoa_classe=3  
        order by tbl_pessoa_nome ASC"); 

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $conta = mysqli_query($conector, "select * from tbl_plano_contas
        where tbl_plano_contas_lixeira=0 and
              tbl_plano_contas_debito_credito='D'
        order by tbl_plano_contas_codigo_id ASC"); 

    $centro_custos = mysqli_query($conector, "select * from tbl_centro_custo
                                              where tbl_cc_lixeira=0"); 

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
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_financeiros.php">Relatórios Financeiros</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Análise de Pagamentos</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-search-dollar"></i> Análise de Pagamentos</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <div class="row col-md-12 filtro_exibido" id="consulta_contas">
                        <form method="GET" action="#" enctype="multipart/form-data" >

                            <input type="hidden" name="lista_ao_entrar" id="lista_ao_entrar" value="S">                            
                            <div class="tab-panel ">
                                <div class="tab-pane active">
                                    <fieldset class="scheduler-border " >
                                        <legend class="scheduler-border fonte-legend">Filtros</legend>

                                        <div class="row">
                                            <div class="form-group col-md-11">
                                            </div>
                                        
                                            <div class="form-group col-md-1">
                                                <button type="button" class="form-control btn btn-info pull-right" onclick="onclick=voltar_relatorios()">Voltar
                                                </button>
                                            </div>
                                        </div>    
                                                
                                        <div class="row ">
                                            <div class="form-group col-md-4">
                                                <label for="data_inicial" class="control-label">Data Incial</label>

                                                <input name="data_inicial" type="date" class="form-control" id="data_inicial"
                                                    <?php echo "value='".$data_inicial."'";?>>
                                            </div>  

                                            <div class="form-group col-md-4">
                                                <label for="data_final" class="control-label">Data Final</label>
                                                
                                                <input name="data_final" type="date" class="form-control" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                            </div>  

                                            <div class="form-group col-md-4">
                                                <label for="tipo_data" class="control-label">Tipo de Data</label>
                                            </div>
                                            
                                            <div class="form-group col-md-4">
                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_data" value="V"   
                                                      <?php if ($tipo_data == 'V'){echo "checked";}?>> Vencimento 
                                                </label>
                                                
                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_data" value="E"
                                                       <?php if ($tipo_data == 'E'){echo "checked";}?>> Emissão
                                                </label>
                                                
                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_data" value="P"
                                                       <?php if ($tipo_data == 'P'){echo "checked";}?> > Pagamento
                                                </label>
                                            </div>  
                                        </div>  

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="codigo_fazenda" class="control-label">Local</label>
                                                <select class="form-control selectpicker" id="codigo_fazenda" multiple name="codigo_fazenda">
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

                                            <div class="form-group col-md-3">
                                                <label for="codigo_cc" class="control-label"> Centro de Custos</label>
                                                
                                                <select class="form-control selectpicker" id="codigo_cc" name="codigo_cc" data-live-search="true" multiple style="z-index:5;">

                                                    <?php while($reg_cc = mysqli_fetch_object($centro_custos)) { ?>

                                                    <option value="<?php 
                                                    echo $reg_cc->tbl_cc_codigo_id ?>">

                                                    <?php 
                                                    echo $reg_cc->tbl_cc_descricao;
                                                    ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="codigo_fornecedor" class="control-label">Fornecedor</label>
                                                <select class="form-control selectpicker" id="codigo_fornecedor" name="codigo_fornecedor" data-live-search="true" multiple style="z-index:5;">

                                                    <?php while($reg_for = mysqli_fetch_object($cli_for)) { ?>

                                                    <option value="<?php 
                                                    echo $reg_for->tbl_pessoa_id ?>">

                                                    <?php 
                                                    echo $reg_for->tbl_pessoa_nome;
                                                    ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="codigo_conta" class="control-label"> Conta Contábil</label>
                                                
                                                <select class="form-control selectpicker" id="codigo_conta" name="codigo_conta" data-live-search="true" style="z-index:5;">

                                                    <option value="0">...</option>

                                                    <?php while($reg_conta = mysqli_fetch_object($conta)) { ?>

                                                    <option value="<?php 
                                                    echo $reg_conta->tbl_plano_contas_codigo_id ?>">

                                                    <?php 
                                                    echo $reg_conta->tbl_plano_contas_descricao . ' - ' .  $reg_conta->tbl_plano_contas_codigo_id ;
                                                      ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                        </div>
                                        
                                        <div class="row">    
                                            <div class="form-group col-md-4">
                                                <label for="tipo_rel" class="control-label">Tipo do Relatório:&nbsp;</label> 

                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_rel" value="A"   
                                                      <?php if ($tipo_rel == 'A'){echo "checked";}?>> Analítico
                                                </label>
                                                
                                                <label class="radio-inline">
                                                    <input type="radio" name="tipo_rel" value="S"
                                                       <?php if ($tipo_rel == 'S'){echo "checked";}?>> Sintético
                                                </label>
                                            </div>    

                                            <div class="form-group col-md-1">
                                                <button type="button" class="btn btn-primary pull-right" onclick="listar_contas_pagar_tela()">Listar
                                                </button>
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
            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Análise de Pagamanto - Mensagem</h4>
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
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
     </div>
 </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->

<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="js/relatorios_financeiros.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
</script>

<script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
" charset="utf-8" type="text/javascript" >
</script>
<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

</body>
</html>
