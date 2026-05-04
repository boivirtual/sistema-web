<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = date("Y");
    $mes = date("M");

    $conta_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC"); 

    $centro_custos = mysqli_query($conector, "select * from tbl_centro_custo
                                              where tbl_cc_lixeira=0"); 

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and 
        tbl_pessoa_classe=4");

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
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay"  rel="stylesheet" crossorigin="anonymous">

  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
  <link href="css/tabela_1300.css" rel="stylesheet">
  <link href="css/select-1.13.14.css" rel="stylesheet" >
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
            <span class="titulo">Fluxo de Caixa Diário</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Fluxo de Caixa Diário</h3>
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
                                                
                                        <div class="diario">
                                            <div class="row">
                                                <div class="form-group col-md-2" >
                                                    <select class="form-control" id="mes_diario" name="mes_diario">
                                                    <option value="01"
                                                    <?php if ($mes == 'Jan') {echo"selected";}?>>Janeiro
                                                    </option>
                                                    <option value="02"
                                                    <?php if ($mes == 'Feb') {echo"selected";}?>>Fevereiro
                                                    </option>
                                                    <option value="03"
                                                    <?php if ($mes == 'Mar') {echo"selected";}?>>Março
                                                    </option>
                                                    <option value="04"
                                                    <?php if ($mes == 'Apr') {echo"selected";}?>>Abril
                                                    </option>
                                                    <option value="05"
                                                    <?php if ($mes == 'May') {echo"selected";}?>>Maio
                                                    </option>
                                                    <option value="06"
                                                    <?php if ($mes == 'Jun') {echo"selected";}?>>Junho
                                                    </option>
                                                    <option value="07"
                                                    <?php if ($mes == 'Jul') {echo"selected";}?>>Julho
                                                    </option>
                                                    <option value="08"
                                                    <?php if ($mes == 'Aug') {echo"selected";}?>>Agosto
                                                    </option>
                                                    <option value="09"
                                                    <?php if ($mes == 'Sep') {echo"selected";}?>>Setembro
                                                    </option>
                                                    <option value="10"
                                                    <?php if ($mes == 'Oct') {echo"selected";}?>>Outubro
                                                    </option>
                                                    <option value="11"
                                                    <?php if ($mes == 'Nov') {echo"selected";}?>>Novembro
                                                    </option>
                                                    <option value="12"
                                                    <?php if ($mes == 'Dec') {echo"selected";}?>>Dezembro
                                                    </option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2" >
                                                    <select class="form-control" id="ano_diario" name="ano_diario">
                                                    <option value="2019"
                                                        <?php if ($ano == 2019) {echo"selected";}?>>2019
                                                    </option>
                                                    <option value="2020"
                                                        <?php if ($ano == 2020) {echo"selected";}?>>2020
                                                    </option>
                                                    <option value="2021"
                                                        <?php if ($ano == 2021) {echo"selected";}?>>2021
                                                    </option>
                                                    <option value="2022"
                                                        <?php if ($ano == 2022) {echo"selected";}?>>2022
                                                    </option>
                                                    <option value="2023"
                                                        <?php if ($ano == 2023) {echo"selected";}?>>2023
                                                    </option>
                                                    <option value="2024"
                                                        <?php if ($ano == 2024) {echo"selected";}?>>2024
                                                    </option>
                                                    <option value="2025"
                                                        <?php if ($ano == 2025) {echo"selected";}?>>2025
                                                    </option>
                                                    <option value="2026"
                                                        <?php if ($ano == 2026) {echo"selected";}?>>2026
                                                    </option>
                                                    <option value="2027"
                                                        <?php if ($ano == 2027) {echo"selected";}?>>2027
                                                    </option>
                                                    <option value="2028)"
                                                        <?php if ($ano == 2028) {echo"selected";}?>>2028
                                                    </option>
                                                    <option value="2029"
                                                        <?php if ($ano == 2029) {echo"selected";}?>>2029
                                                    </option>
                                                    <option value="2030"
                                                        <?php if ($ano == 2030) {echo"selected";}?>>2030
                                                    </option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <select class="form-control" id="tipo_rel" name="tipo_rel">
                                                    <option value="2">Realizado</option>
                                                    <option value="3">Não Realizado</option>
                                                    </select>
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
                                                    <label for="conta_pagamento" class="control-label"> Banco/Conta Pagamento</label>

                                                    <select class="form-control selectpicker" id="conta_pagamento" name="conta_pagamento" multiple style="z-index:5;" >

                                                    <?php 
while ($reg_conta_pag = mysqli_fetch_object($conta_pagamento)) { 
    $codigo_conta = $reg_conta_pag->tbl_conta_pagamento_id;
    $nome_banco = $reg_conta_pag->tbl_conta_pagamento_descricao;
    $agencia = $reg_conta_pag->tbl_conta_pagamento_agencia;
    $conta = $reg_conta_pag->tbl_conta_pagamento_conta;
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';

    echo '<option value="'.$codigo_conta.'">' . $descricao_conta .
         '</option>';

}?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-primary pull-right" data-toggle='tooltip' data-placement='top' title="Listar na Tela"  onclick="listar_fluxo_caixa_tela(1)">Listar
                                                    </button>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="form-control btn btn-success pull-right" onClick="listar_fluxo_caixa_tela(2)">Excel
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
                            <h4 class="modal-title">Relatório Fluxo de Caixa - Mensagem</h4>
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
<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>
</html>
