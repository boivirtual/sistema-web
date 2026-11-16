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
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css"rel="stylesheet" >
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_parametros'])) {
        $array_parametros = explode("!",$_SESSION['menu_parametros']);

        if ($array_parametros[0] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
            echo '</div>';         
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuou o login!</span>';  
        echo '</div>';         
        exit;
    }

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Parâmetros <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Empresa</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-building"></i> Empresa</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                        <div  class="form-group">
                              <a href="form_empresas_incluir.php">
                                  <input type="button" class="btn btn-primary" aria-label="Left Align" 
                                  value="Incluir Novo"/>
                              </a>
                        </div> 
		       		<section class="panel">
                        <table class="table table-striped table-advance table-hover"  id="tabela_empresa" width="100%">

                        <thead>
                        	<tr>
                                <th> Razão Social</th>
                                <th> Nome Fantasia</th>
                                <th> CNPJ</th>
                                <th> Bairro</th>
                                <th> <i class="icon_cogs"></i> Ações</th>
                            </tr>
                        </thead>
                          
		                <tbody>
                        	<?php
                                                                 
                       			$ssql = "select * from tbl_empresa"; 
                                $rs = mysqli_query($conector, $ssql); 
                     
                                while ($registro_emp = mysqli_fetch_object($rs)){
                                    $codigo = $registro_emp->tbl_empresa_id;
                                    $razao = $registro_emp->tbl_empresa_nome; 
                                    $cnpj = $registro_emp->tbl_empresa_cpf_cnpj; 
                                    $tipo_pessoa = $registro_emp->tbl_empresa_tipo_pessoa;
                                    $nome_fantasia = $registro_emp->tbl_empresa_nome_fantasia; 
                                    $bairro = $registro_emp->tbl_empresa_bairro; 
                                    $lixeira = $registro_emp->tbl_empresa_lixeira; 

                                    if ($tipo_pessoa=="J") {
                                        $cnpj_editado = substr($cnpj,0,2) . "." . 
                                                        substr($cnpj,2,3) . "." .
                                                        substr($cnpj,5,3) . "/" . 
                                                        substr($cnpj,8,4) . "-" . 
                                                        substr($cnpj,12,2);
                                    }
                                    else {
                                        $cnpj_editado = substr($cnpj,0,3) . "." . 
                                                        substr($cnpj,3,3) . "." .
                                                        substr($cnpj,6,3) . "-" . 
                                                        substr($cnpj,9,2);
                                    }
                                    echo "<tr>";

                                    if ($lixeira==1){
                                        echo "<td width='30%' style='color:#ccc'>".$razao."</td>";
                                        echo "<td width='25%' style='color:#ccc'>".$nome_fantasia."</td>";
                                        echo "<td width='15%' style='color:#ccc'>".$cnpj_editado."</td>";
                                        echo "<td width='20%' style='color:#ccc'>".$bairro."</td>";
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='#'><i class='icon_refresh'  title='Restaurar esse registro da lixeira' onClick='enviar_lixeira(\"{$codigo}\",2,\"{$razao}\")' ></i></a>";
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                    else {
                                        echo "<td width='30%'>".$razao."</td>";
                                        echo "<td width='25%'>".$nome_fantasia."</td>";
                                        echo "<td width='15%'>".$cnpj_editado."</td>";
                                        echo "<td width='20%'>".$bairro."</td>";
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_empresas_editar.php?id=".$codigo."'><i class='icon_pencil'  title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' href='#'><i class='icon_trash_alt' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$codigo}\",0,\"{$razao}\")' ></i></a>"; 
                                        echo "</div>";
                                        echo "</td>";
                                    }

                                    echo "</tr>";
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
                            <h4 class="modal-title">Empresa</h4>
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
                            <h4 class="modal-title">Empresa - Erro</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </section>

<?php 
  $javascript_file_name = 'empresas.js';
  require 'rodape.php';
?>



                
                
