<?php

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

    if(isset($_SESSION['menu_cadastros'])) {
        $array_cadastros = explode("!",$_SESSION['menu_cadastros']);

        if ($array_cadastros[3] == 0){
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
        include "opcoes_menu.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-file-text-o"></i> Produtos</h3>
                </div>
            </div>

          <div class="row">
            <div class="col-lg-12">

                <div  class="form-group">
                    <button type="button" class="btn btn-primary" id="botao_incluir">Incluir Novo</button>
                </div> 

               <section class="panel">
                <table class="table table-striped table-advance table-hover table-bordered" id="tabela_produtos">
                  <thead>
                    <tr>
                      <th> Código</th>
                      <th> Descrição</th>
                      <th> Unidade</th>
                      <th> Fabricante</th>
                      <th> Grupo</th>
                      <th>
                        <i class="icon_cogs"></i> Ações
                      </th>
                    </tr>
                  </thead>
                  

                  <tbody>
                    <?php 
                        include "conecta_mysql.inc";

                        $ssql = "select * from tbl_produto
                                    inner join tbl_pessoa
                                       on tbl_pessoa_id = tbl_produto_codigo_fabricante
                                    inner join tabela_grupo_produtos
                                       on tab_codigo_grupo_produtos=tbl_produto_codigo_grupo"; 
                        $rs = mysqli_query($conector, $ssql); 
             
                        while ($registro_tabela = mysqli_fetch_object($rs)){
                            $codigo = $registro_tabela->tbl_produto_codigo_id;
                            $descricao = $registro_tabela->tbl_produto_descricao;
                            $nome_fabricante = $registro_tabela->tbl_pessoa_nome;
                            $codigo_fabricante = $registro_tabela->tbl_produto_codigo_fabricante;
                            $unidade = $registro_tabela->tbl_produto_unidade;
                            $codigo_grupo = $registro_tabela->tbl_produto_codigo_grupo;
                            $descricao_grupo = $registro_tabela->tab_descricao_grupo_produtos;
                            $lixeira = $registro_tabela->tbl_produto_lixeira; 

                            if ($lixeira==1){
                                echo '<tr>';
                                echo '<td style="color:#ccc">'.$codigo.'</td>';
                                echo '<td style="color:#ccc">'.$descricao.'</td>';
                                echo '<td style="color:#ccc">'.$unidade.'</td>';
                                echo '<td style="color:#ccc">'.$nome_fabricante.'</td>';
                                echo '<td style="color:#ccc">'.$descricao_grupo.'</td>';

                                echo '<td>
                                      <a class="btn" href="#" 
                                      data-toggle="modal" 
                                      data-target="#modal_excluir" 
                                      data-whatever="'.$codigo.'"
                                      data-whatevernome="'.$descricao.'"
                                      data-whatevertipo="3">
                                      <i class="icon_refresh" title="Remover esse registro da lixeira" ></i>
                                      </a>
                                      </td>';
                                echo '</tr>'; 
                            }
                            else {
                                echo '<tr>';
                                echo '<td>'.$codigo.'</td>';
                                echo '<td>'.$descricao.'</td>';
                                echo '<td>'.$unidade.'</td>';
                                echo '<td>'.$nome_fabricante.'</td>';
                                echo '<td>'.$descricao_grupo.'</td>';
                                echo '<td>
                                      <a class="btn" href="#" 
                                      data-toggle="modal" 
                                      data-target="#modal_editar" 
                                      data-whatever="'.$codigo.'"
                                      data-whatevernome="'.$descricao.'">
                                      <i class="icon_pencil" title="Editar esse registro" ></i>
                                      </a>
                                
                                      <a class="btn" href="#" 
                                      data-toggle="modal" 
                                      data-target="#modal_excluir" 
                                      data-whatever="'.$codigo.'"
                                      data-whatevernome="'.$descricao.'"
                                      data-whatevertipo="2">
                                      <i class="icon_trash_alt" 
                                       title="Enviar esse registro para lixeira"></i>
                                      </a></td>';
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
            <div class="modal fade" id="modal_incluir" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle" aria-hidden="true"  data-backdrop="static">

                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modal_incluirLabel">Produtos - Incluir</h4>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="gravar_produtos.php" enctype="multipart/form-data">
                                
                                <div class="row">
                                  <div class="form-group col-md-4">
                                      <label for="codigo_raca" class="control-label"><span class="required">*</span>Código</label>
                                      <input name="codigo_raca" type="text" class="form-control" id="codigo_raca" required="">
                                  </div>
                                </div>
                                
                                <div class="row">
                                  <div class="form-group col-md-8">
                                      <label for="descricao_produto" class="control-label"><span class="required">*</span>Descrição</label>
                                      <input name="descricao_produto" type="text" class="form-control" id="descricao_produto" required="">
                                  </div>
                                  <div class="form-group col-md-4">
                                    <label for="unidade_id" class="control-label">Unidade</label>
                                    <select class="form-control">
                                      <option>Unidade 1</option>
                                      <option>Unidade 2</option>
                                      <option>Unidade 3</option>
                                      <option>Unidade 4</option>
                                      <option>Unidade 5</option>
                                    </select>
                                  </div>
                                </div>
                                
                                <div class="row">
                                  <div class="form-group col-md-5">
                                      <label for="fabricante_produto" class="control-label">Fabricante</label>
                                      <input name="fabricante_produto" type="text" class="form-control" id="fabricante_produto">
                                  </div>
                                </div>
                                
                                <div class="row">
                                  <div class="form-group col-md-4">
                                    <label for="grupo_id" class="control-label">Grupo</label>
                                    <select class="form-control">
                                      <option>Grupo 1</option>
                                      <option>Grupo 2</option>
                                      <option>Grupo 3</option>
                                      <option>Grupo 4</option>
                                      <option>Grupo 5</option>
                                    </select>
                                  </div>
                                  <div class="form-group col-md-4">
                                    <label for="via_de_uso_id" class="control-label">Via de Uso</label>
                                    <select class="form-control">
                                      <option>Grupo 1</option>
                                      <option>Grupo 2</option>
                                      <option>Grupo 3</option>
                                      <option>Grupo 4</option>
                                      <option>Grupo 5</option>
                                    </select>
                                  </div>
                                </div>
                                
                                <div class="row m-bot15">
                                  <div class="col-md-8">
                                    <label for="para_que_serve_produto" class="control-label">Para que serve</label>
                                    <textarea name="para_que_serve_produto" type="text" class="form-control" id="para_que_serve_produto" rows="4"></textarea>
                                  </div>
                                </div>

                                <input type="hidden" name="tipo_gravacao" id="tipo_gravacao" value="0">
                                <input type="hidden" name="status_gravacao" id="status_gravacao"
                                <?php echo "value='".$status_gravacao."'";?>>
                                <input type="hidden" name="status_erro"  size="100" id="status_erro"
                                <?php echo "value='".$erro_mysql."'";?>>

                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-primary">Confirmar Inclusão</button>
                                    <button type="button" class="btn btn-info pull-right" data-dismiss="modal">Voltar
                                    </button>
                                </div>
             
                            </form>
                        </div>
              
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Produtos</h4>
                  </div>
                  <div class="modal-body"></div>
                  <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                  </div>
                </div>
              </div>
            </div>
        </section>
    </section>

   

<?php 
$javascript_file_name = 'tabela_produtos.js';
require 'rodape.php' 
?>
