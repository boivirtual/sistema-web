<?php
    include "valida_sessao.inc";

    if(isset($_REQUEST["editar"]) && $_REQUEST["editar"] == true) {
            $status_gravacao = $_REQUEST["status_gravacao"];
            $erro_mysql = $_REQUEST["erro_mysql"];
    }
    else {
        $status_gravacao = '';
        $erro_mysql = '';
    }

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
  <link href="font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet" />
  <link href="css/daterangepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
  <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>
</head>

<body>

  <?php

    @ session_start();

    if(isset($_SESSION['menu_parametros'])) {
        $array_parametros = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametros[15] == 0){
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

    $cnpj_cliente = $_SESSION['id_cliente'];
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

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Plano de Contas</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                    <div  class="form-group opcoes_topo">
                        <a href="form_plano_contas_incluir.php">
                            <input type="button" class="btn btn-primary " aria-label="Left Align" 
                                value="Incluir Novo"/>
                        </a>
                    </div> 

		       		<section class="panel">
                        <table class="table table-striped table-advance table-hover table-bordered" 
                          id="tabela_plano_contas">

                        <thead>
                        	<tr>
			                    <th> Código</th>
			                    <th> Ref Contabil</th>
			                    <th> Descrição</th>
                                <th align="center"> Débito/Crédido</th>
                                <th align="center"> Analítica/Sintética</th>
			                    <th><i class="icon_cogs"></i> Ações</th>
                            </tr>
                        </thead>
                          

		                <tbody>
                            <?php 
                                include "conecta_mysql.inc";

                                $tbl_plano_contas = mysqli_query($conector, "select * from tbl_plano_contas 
                                    where tbl_plano_contas_nivel=1 and  
                                          tbl_plano_contas_lixeira=0"); 

                                $ssql = "select * from tbl_plano_contas"; 
                                $rs = mysqli_query($conector, $ssql); 
                     
                                while ($reg_pla_contas = mysqli_fetch_object($rs)){
                                    $codigo = $reg_pla_contas->tbl_plano_contas_codigo_id;
                                    $descricao = $reg_pla_contas->tbl_plano_contas_descricao; 
                                    $descricao_complementar = $reg_pla_contas->tbl_plano_contas_descricao_complementar; 
                                    $ref_contabil = $reg_pla_contas->tbl_plano_contas_refrencia_contabilidade; 
                                    $deb_cre = $reg_pla_contas->tbl_plano_contas_debito_credito; 
                                    $ana_sin = $reg_pla_contas->tbl_plano_contas_ana_sin; 
                                    $lixeira = $reg_pla_contas->tbl_plano_contas_lixeira; 
									$codigo_edi = substr($codigo, 0,1) .'.'.  
									              substr($codigo, 1,2) .'.'.  
									              substr($codigo, 3,4);

                                    if ($lixeira==1){
                                        echo '<tr>';
                                        echo '<td width="10%" style="color:#ccc">'.$codigo_edi.'</td>';
                                        echo '<td width="15%" align="center" style="color:#ccc">'.$ref_contabil.'</td>';
                                        echo '<td width="40%" style="color:#ccc">'.$descricao.'</td>';
                                        echo '<td width="10%" align="center" style="color:#ccc">'.$deb_cre.'</td>';
                                        echo '<td width="15%" align="center" style="color:#ccc">'.$ana_sin.'</td>';
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='#'><i class='icon_refresh' data-toggle='tooltip' data-placement='left'  title='Restaurar esse registro da lixeira' onClick='enviar_lixeira(\"{$codigo}\",2)' ></i></a>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo '</tr>'; 
                                    }
                                    else {
                                        echo '<tr>';
                                        echo '<td width="10%">'.$codigo_edi.'</td>';
                                        echo '<td width="15%" align="center">'.$ref_contabil.'</td>';
                                        echo '<td width="40%">'.$descricao.'</td>';
                                        echo '<td width="10%" align="center">'.$deb_cre.'</td>';
                                        echo '<td width="15%" align="center">'.$ana_sin.'</td>';
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_plano_contas_editar.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$codigo}\",0)' ></i></a>"; 
                                        echo "</div>";
                                        echo "</td>";
                                        echo '</tr>'; 
                                    }
                                } 
                                
                                mysqli_close($conector);
                            ?>
                        </tbody>
		                </table>
		            </section>
		        </div>
	        </div>
	        <!-- page end-->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Plano de Contas</h4>
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
                            <h4 class="modal-title">Plano de Contas - Erro</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_editar" tabindex="-1" role="dialog" 
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exampleModalLabel">Plano de Contas - Editar</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_plano_contas.php" enctype="multipart/form-data">
                                <div class="form-group col-md-12">
                                    <label for="codigo_plano_contas_alt" class="control-label">Código</label>
                                    <input name="codigo_plano_contas_alt" type="text" class="form-control" id="codigo_plano_contas_alt" 
                                    readonly="">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="descricao_plano_contas_alt" class="control-label"><span class="required">*</span>Descrição</label>
                                    <input name="descricao_plano_contas_alt" type="text" class="form-control" id="descricao_plano_contas_alt" required="" onkeyup="destrava_alteracao()" >
                                </div>

                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="configuracao_conta" class="control-label">
                                        <span class="required">*</span>Opções da Conta
                                    </label>
                                </div>

                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="debito_credito_alt"></label>

                                    <label class="radio-inline">
                                        <input type="radio" name="debito_credito_alt" id="debito_alt" value="D" 
                                         onclick="destrava_alteracao()" required >
                                        Débito
                                    </label>

                                    <label class="radio-inline">
                                        <input type="radio" name="debito_credito_alt" id="credito_alt" value="C" 
                                        onclick="destrava_alteracao()" required>
                                        Crédito
                                    </label>
                                </div>

                                <div class="form-group col-md-12 col-sm-12">
                                    <label class="radio-inline">
                                        <input type="radio" name="fixa_variavel_alt" id="fixa_alt" value="F" 
                                        onclick="destrava_alteracao()" required >
                                        Fixa
                                    </label>

                                    <label class="radio-inline">
                                        <input type="radio" name="fixa_variavel_alt" id="variavel_alt" value="V" 
                                        onclick="destrava_alteracao()" required>
                                        Variável
                                    </label>
                                </div>

                                <div class="form-group col-md-12 col-sm-12">
                                    <label class="radio-inline">
                                        <input type="radio" name="analitico_sintetico_alt" id="analitico_alt" value="A" 
                                        onclick="destrava_alteracao()" required >
                                        Analítico
                                    </label>

                                    <label class="radio-inline">
                                        <input type="radio" name="analitico_sintetico_alt" id="sintetico_alt" value="S" 
                                        onclick="destrava_alteracao()" required>
                                        Sintético
                                    </label>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="1">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                
                                <div class="form-group col-md-12">
                                    <button type="submit" id="confirmar" class="btn btn-primary">Confirmar Alteração</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal"
                                      onClick='trava_alteracao()'>Voltar
                                    </button>
                                </div>
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>

        </section>
    </section>

   <!-- </div> -->

<?php 
  $javascript_file_name = 'tabela_plano_contas.js';
  require 'rodape.php';
?>

