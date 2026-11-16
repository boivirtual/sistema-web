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

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

  <!-- container section start -->
  <section id="container" class="">

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Cabeçalho <i class="fa fa-angle-right seta-direita"></i><span class="titulo">Validar Clientes</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-users"></i> Validar Clientes</h3>
                </div>
            </div>

	        <div class="row">
		        <div class="col-lg-12">
                    <!--    <div  class="form-group">
                              <a href="form_contas_receber_incluir.php">
                                  <input type="button" class="btn btn-primary" aria-label="Left Align" 
                                  value="Incluir Nova"/>
                              </a>
                        </div> 
                    -->
                    <div class="row col-md-12" id="consulta_contas">
                        <form method="GET" action="form_contas_receber.php" enctype="multipart/form-data" >
                                
                            <div class="tab-panel">
                                <div class="tab-pane active">

                                <table class="table table-striped table-advance table-hover" id="tabela_clientes" 
                                width="100%" >
                          
                                <tbody>
                                <?php
                                    $ssql = "SELECT * FROM tbl_cliente_boi_virtual
                                             WHERE tbl_cliente_lixeira = 0
                                    ORDER BY tbl_cliente_id DESC"; 

                                    $rs = mysqli_query($conector, $ssql); 
                                
                                    $total_ativos = 0;
                                    $total_validar = 0;
                                    $total_inativos = 0;

                                    $registros_encontrados = mysqli_num_rows($rs);

                                    if ($registros_encontrados!=0) {
                                        while ($reg_cli = mysqli_fetch_object($rs)){
                                            $cliente_id = $reg_cli->tbl_cliente_id;
                                            $cpf_cnpj = $reg_cli->tbl_cliente_cpf_cnpj_empresa;

                                            if (strlen($reg_cli->tbl_cliente_telefone_adm)==9) {
                                                $telefone = '('.$reg_cli->tbl_cliente_ddd_adm.')'. substr($reg_cli->tbl_cliente_telefone_adm, 0, 5).'-'.
                                                     substr($reg_cli->tbl_cliente_telefone_adm, 5, 4);
                                            }
                                            else {
                                                $telefone = '('.$reg_cli->tbl_cliente_ddd_adm.')'. substr($reg_cli->tbl_cliente_telefone_adm, 0, 4).'-'. 
                                                     substr($reg_cli->tbl_cliente_telefone_adm, 4, 4);
                                            }

                                            $nome_adm = $reg_cli->tbl_cliente_nome_adm;
                                            $data_inclusao = new Datetime($reg_cli->tbl_cliente_incluido_em);
                                            $nome_empresa = $reg_cli->tbl_cliente_nome_empresa;
                                            $ativo = $reg_cli->tbl_cliente_ativo;
                                            $validado = $reg_cli->tbl_cliente_validado;

                                            if ($ativo=="S") {
                                                $total_ativos++;
                                                $situacao = 'Ativo';
                                            }
                                            else {
                                                $total_inativos++;
                                                $situacao = 'Desativado';
                                            }

                                            if ($validado=="N") {
                                                $total_validar++;
                                                $situacao = 'Aguardando Validação';
                                            }

                                            echo "<tr>";
                                echo "<td width='8%'>".$data_inclusao->format('d/m/Y')."</td>";
                                echo "<td width='30%'>".$nome_empresa."</td>";
                                echo "<td width='25%'>".$nome_adm."</td>";
                                echo "<td width='11%'>".$telefone."</td>";
                                echo "<td width='16%'>".$situacao."</td>";
                                if ($ativo=='S') {
                                        echo "<td width='10%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_cliente_boi_virtual_consultar.php?id=".$cliente_id."'><i class='icon_search' data-toggle='tooltip' data-placement='left' title='Consultar esse registro' ></i></a>";
                                        echo "</div>";
                                        echo "</td>";
                                }
                                else if ($validado=="N") {
                                        echo "<td width='10%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_cliente_boi_virtual_editar.php?id=".$cliente_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "</div>";
                                        echo "</td>";
                                } else {
                                        echo "<td width='10%'>";
                                        echo "<div class='btn-group'>";
                                        echo "<a class='btn' href='form_cliente_boi_virtual_editar.php?id=".$cliente_id."'><i class='icon_pencil' data-toggle='tooltip' data-placement='left' title='Editar esse registro' ></i></a>";
                                        echo "</div>";
                                        echo "</td>";
                                }
                                            echo "</tr>";

                                        }
                                    }

                                ?>    

                                </tbody>

                                <thead>
                <tr>
                    <div class="row col-md-8" id="total_contas">
                        <div class="form-group col-md-3">
                            <label class="control-label">Registros encontrados</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".$registros_encontrados."'";?>>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label">Total Ativos</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".$total_ativos."'";?>>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label">Total Inativos</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".$total_inativos."'";?>>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label">Aguardando Validação</label>
                            <input class="form-control form-control-sm" type="text" readonly=""
                            <?php echo "value='".$total_validar."'";?>>
                        </div>
                    </div>
                </tr>

                                <tr>
                                    <th> Solicitação</th> 
                                    <th> Nome Empresa</th>
                                    <th> Administrador</th>
                                    <th> Telefone</th>                                   
                                    <th> Situação</th>
                                    <th> <i class="icon_cogs"></i> Ações</th>
                                </tr>
                                </thead>
                                <tfoot>
                                </tfoot>
                                </table>

                                </div>
                            </div>
                        </form>
                    </div>    
    	        </div>
	        </div>

	        <!-- page end-->
            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Validar Clientes</h4>
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
                            <h4 class="modal-title">Validar Clientes - Erro</h4>
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
  $javascript_file_name = 'validar_cliente.js';
  require 'rodape.php';
?>



                
                
