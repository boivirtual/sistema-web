<?php
    include "conecta_mysql.inc";

    $numero_pesagem = $_REQUEST['id'];

    $tbl_pesagem = mysqli_query($conector, "select * from tbl_pesagem
                                                where tbl_pesagem_id='$numero_pesagem'"); 

    $reg_pesagem = mysqli_fetch_object($tbl_pesagem);

    $nome_inclusao = $reg_pesagem->tbl_pesagem_incluido_por;
    $data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $lote = $reg_pesagem->tbl_pesagem_lote;
    $animais_pesados = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
    $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
    $peso_arroba = $reg_pesagem->tbl_pesagem_peso_arroba;
    $peso_medio_kg = $reg_pesagem->tbl_pesagem_peso_medio_kg;
    $peso_medio_arroba = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
    $filtros=$reg_pesagem->tbl_pesagem_filtros;

    $data_sistema = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 

</head>

<body>
    <?php
    @ session_start();   

    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_cadastro[1] == 0){
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

    <section id="container" class="">
        <?php
            include "cabecalho.php";
            include "opcoes_menu.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Manejo de Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_pesagem_animais.php"> Pesagem</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Consultar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="icon_box-checked"></i> Pesagem - Consultar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_pedido">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label for="num_orc" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="num_orc"><?php echo $numero_pesagem;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="includido_por" class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>
                                                </div>
                                            </div>

                                            <div class="row"> 
                                                <div class="col-md-8">
                                                    <label class="label_consulta">Filtros:&nbsp;</label> <span><?php echo $filtros;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="label_consulta">Descrição Pesagem:&nbsp;</label>
                                                    <span><?php echo $lote;?></span>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="label_consulta">Data da Pesagem:&nbsp;</label>
                                                    <span><?php echo $data_inclusao->format('d/m/Y H:i:s');?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="label_consulta">Animais Pesados:&nbsp;</label> <span ><?php echo $animais_pesados;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Total Kg:&nbsp;</label><span>
                                                    <?php echo $peso_kg;?>
                                                    </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Total Arrobas:&nbsp;</label><span><?php echo $peso_arroba;?></span>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Médio Kg:&nbsp;</label><span>
                                                    <?php echo $peso_medio_kg;?>
                                                    </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="label_consulta">Peso Médio Arrobas:&nbsp;</label><span><?php echo $peso_medio_arroba;?></span>
                                                </div>
                                            </div>

                                            <hr align="center"> 

                                            <p style="font-weight: bold;">Animais Pesados</p>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%">

                                                <thead>
                                                    <tr>
                                                        <th> Id</th>
                                                        <th> Peso</th>
                                                        <th> Sexo</th>
                                                        <th> Nascimento</th>
                                                        <th> Raça</th>
                                                        <th> Pelagem</th>
                                                        <th> Mãe</th>
                                                        <th> Observação</th> 
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                        $rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                                                            WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'");
                                                        $num_rows = mysqli_num_rows($rs);

                                                        if ($num_rows!=0){
                                                            while ($fila = mysqli_fetch_object($rs)){
                                                            echo '<tr>';
                                                            echo '<td >' .$fila->tbl_ite_pesagem_codigo_animal.'</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_peso . '</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_sexo . '</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_nascimento . '</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_raca. '</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_pelagem. '</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_mae. '</td>';
                                                            echo '<td>' . $fila->tbl_ite_pesagem_observacao. '</td>';
                                                            echo '</tr>';
                                                            }
                                                        }
                                                    ?>    
                                                </tbody>
                                            </table>
                                                                                        
                                                <hr align="center"> 

                                                <div class="row">  
                                                    <?php
                                                        echo '<button type="button" class="btn btn-info pull-right" onclick="fecha_consultar_pesagem()">Voltar</button>';
                                                        echo '</div>';
                                                    ?>
                                                </div>
                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default fecha_editar_dados" type="button">Close</button>
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
                            <h4 class="modal-title">Pesagem - Erro</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>
            
        </section> <!-- wrapper -->
    </section><!--main-content -->


<?php 
  $javascript_file_name = 'pesagem.js';
  require 'rodape.php';
?>




