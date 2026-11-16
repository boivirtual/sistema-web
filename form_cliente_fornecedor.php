<?php
    include "valida_sessao.inc";

    function diferenca_data($data_validade) {
            
        $data_inicial = date("Y-m-d H:i:s");
        $data_final = $data_validade;
        $time_inicial = strtotime($data_inicial);
        $time_final = strtotime($data_final);
        $diferenca = $time_final - $time_inicial; 
        $dias = (int)floor( $diferenca / (60 * 60 * 24)); 
        return $dias;
    }

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
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_cadastros'])) {
        $array_cadastro = explode("!",$_SESSION['menu_cadastros']);

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
            <span class="titulo">Pessoas</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-id-card"></i> Pessoas</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">

                        <div  class="form-group">
                              <a href="form_cliente_fornecedor_incluir.php">
                                  <input type="button" class="btn btn-primary" aria-label="Left Align" 
                                  value="Incluir Novo"/>
                              </a>
                        </div> 

		       		<section class="panel">
                        <table class="table table-striped table-advance table-hover"  id="tabela_clientes" width="100%">

                        <thead>
                        	<tr>
                                <th> Razão Social/Nome</th>
                                <th> Classe</th>
                                <th> Contato</th>
                                <th> Telefone</th>
                                <th> Email</th> 
                                <th> <i class="icon_cogs"></i> Ações</th>
                            </tr>
                        </thead>
                          

		                <tbody>
                        	<?php
                                include "conecta_mysql.inc";

                                $ssql = "select * from tbl_pessoa
                                            inner join tabela_tipo_pessoas
                                                    on tbl_pessoa_classe=tab_codigo_tipo_pessoa"; 
                                $rs = mysqli_query($conector, $ssql); 
                     
                                while ($registro_cliente = mysqli_fetch_object($rs)){
                                    if (strlen($registro_cliente->tbl_pessoa_telefone)==9) {
                                        $telefone = '(' . $registro_cliente->tbl_pessoa_ddd . ') ' . 
                                        substr($registro_cliente->tbl_pessoa_telefone, 0, 5) . '-' . 
                                        substr($registro_cliente->tbl_pessoa_telefone, 5, 4);
                                    }
                                    else {
                                        $telefone = '(' . $registro_cliente->tbl_pessoa_ddd . ') ' . 
                                        substr($registro_cliente->tbl_pessoa_telefone, 0, 4) . '-' . 
                                        substr($registro_cliente->tbl_pessoa_telefone, 4, 4);
                                    }

                                    $codigo = $registro_cliente->tbl_pessoa_id;
                                    $descricao_classe = $registro_cliente->tab_descricao_tipo_pessoa;
                                    $razao = $registro_cliente->tbl_pessoa_nome; 
                                    $contato = $registro_cliente->tbl_pessoa_contato; 
                                    $email = $registro_cliente->tbl_pessoa_email; 
                                    $lixeira = $registro_cliente->tbl_pessoa_lixeira; 

                                    echo "<tr>";

                                    if ($lixeira==1){
                                        echo "<td width='30%' style='color:#ccc'>".$razao."</td>";
                                        echo "<td width='10%' style='color:#ccc'>".$descricao_classe."</td>";
                                        echo "<td width='10%' style='color:#ccc'>".$contato."</td>";
                                        echo "<td width='15%' style='color:#ccc'>".$telefone."</td>";
                                        echo "<td width='15%' style='color:#ccc'>".$email."</td>";
                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='#'><i class='icon_refresh' data-toggle='tooltip' data-placement='left'  title='Restaurar esse registro da lixeira' onClick='enviar_lixeira(\"{$codigo}\",2)' ></i></a>";
                                        echo "</div>";
                                        echo "</td>";
                                    }
                                    else {
                                        echo "<td width='30%'>".$razao."</td>";
                                        echo "<td width='10%'>".$descricao_classe."</td>";
                                        echo "<td width='10%'>".$contato."</td>";
                                        echo "<td width='15%'>".$telefone."</td>";
                                        echo "<td width='15%'>".$email."</td>";

                                        echo "<td width='10%'>";    
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_cliente_fornecedor_editar.php?id=".$codigo."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "<a class='btn' href='#'><i class='icon_trash_alt' data-toggle='tooltip' data-placement='left' title='Enviar para lixeira' onClick='enviar_lixeira(\"{$codigo}\",0)' ></i></a>"; 
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
                            <h4 class="modal-title">Pessoas</h4>
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
                            <h4 class="modal-title">Pessoas - Erro</h4>
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
  $javascript_file_name = 'cliente_fornecedor.js';
  require 'rodape.php';
?>



                
                
