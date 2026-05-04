<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $numero_pesagem = $_REQUEST['id'];

    $tbl_pesagem = mysqli_query($conector, "select * from tbl_pesagem
                                                where tbl_pesagem_id='$numero_pesagem'"); 

    $reg_pesagem = mysqli_fetch_object($tbl_pesagem);

    $nome_inclusao = utf8_decode($reg_pesagem->tbl_pesagem_incluido_por);
    $data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = new DateTime($reg_pesagem->tbl_pesagem_data);
    $data_emissao_edi =$data_emissao->format('d/m/Y');

    $controle = $reg_pesagem->tbl_pesagem_controle;
    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $lote = $reg_pesagem->tbl_pesagem_lote;
    $animais_pesados = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
    $peso_kg = number_format($reg_pesagem->tbl_pesagem_peso_kg,2,',','.');
    $peso_arroba = number_format($reg_pesagem->tbl_pesagem_peso_arroba,2,',','.');
    $peso_medio_kg = number_format($reg_pesagem->tbl_pesagem_peso_medio_kg,2,',','.');
    $peso_medio_arroba = number_format($reg_pesagem->tbl_pesagem_peso_medio_arroba,2,',','.');
    $filtros=$reg_pesagem->tbl_pesagem_filtros;
    $sexo = $reg_pesagem->tbl_pesagem_sexo;
    $pasto = $reg_pesagem->tbl_pesagem_pasto;
    $categoria = $reg_pesagem->tbl_pesagem_categoria;
    $num_movimentacao = $reg_pesagem->tbl_pesagem_codigo_movimentacao;

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

  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

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
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_pesagem_animais.php"> Pesagem</a> <i class="fa fa-angle-right seta-direita"></i>
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
                                                <button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="num_orc" class="label_consulta">Nº do Documento:&nbsp;</label>
                                                    <span id="num_orc"><?php echo $numero_pesagem;?></span>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="label_consulta">Data da Pesagem:&nbsp;</label>
                                                    <span><?php echo $data_emissao_edi;?></span>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="label_consulta">Incluido por:&nbsp;</label>
                                                    <span id="includido_por" ><?php echo $incluido_por;?></span>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label class="label_consulta">Lote:&nbsp;</label>
                                                    <span><?php echo $lote;?></span>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="num_movimentacao" class="label_consulta">Nº da Movimentação:&nbsp;</label>
                                                    <span id="num_movimentacao" ><?php echo $num_movimentacao;?></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="text-muted-dark descricao_filtro" style="font-size: 12px; color:lightgray;"><?php echo 'Filtro: ' . $filtros;?></p>
                                                </div>
                                            </div>

                                            <hr align="center"> 


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


                                            <table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%">

                                                <thead>
                                                    <tr>
                                                        <th> <i class='fa fa-sort-alpha-asc'></i></th>
                                                        <th> Código Numeric</th>
                                                        <th> Peso</th>
                                                        <th> Sexo</th>
                                                        <th> Nascimento</th>
                                                        <th> Raça</th>
                                                        <th> Pelagem</th>
                                                        <th> Mãe</th>
                                                        <th> Observação</th> 
                                                        <th> Descarte</th> 
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    INNER JOIN tbl_animais
            ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
    WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'
    ORDER BY tbl_animal_codigo_numerico ASC");

$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($fila = mysqli_fetch_object($rs)){
        $codigo_alfa = $fila->tbl_animal_codigo_alfa;
        $codigo_numerico = intval($fila->tbl_animal_codigo_numerico);

        $descarte = $fila->tbl_animal_descarte_reproducao; 
        if ($descarte=='S') {
            $animal_descarte='Sim';
        }
        else {
            $animal_descarte='';
        }

        $codigo_mae = $fila->tbl_ite_pesagem_mae;

        $caracteres = strlen($codigo_mae);

        if ($caracteres>=9){
            $codigo_numerico_mae = intval(substr($codigo_mae, (strlen($codigo_mae) - 9), 9));
            $codigo_alfa_mae = strrev(preg_replace('/\d/', '',  strrev($codigo_mae), 9));

            if ($codigo_alfa_mae=='' && $codigo_numerico_mae==0) {
                $codigo_mae_edi = '';
            }
            else if ($codigo_alfa_mae==''){
                $codigo_mae_edi = $codigo_numerico_mae;
            }
            else {
                $codigo_mae_edi = $codigo_alfa_mae.'-'.$codigo_numerico_mae;
            }
        }
        /*else if ($codigo_mae==0 || $codigo_mae=='') {
            $codigo_mae_edi = '';
        }*/
        else {
            $codigo_mae_edi = $codigo_mae;
        }


        /*$codigo_mae = $fila->tbl_ite_pesagem_mae;
        $codigo_numerico_mae = intval(substr($codigo_mae, (strlen($codigo_mae) - 9), 9));
        $codigo_alfa_mae = strrev(preg_replace('/\d/', '',  strrev($codigo_mae), 9));

        if ($codigo_alfa_mae=='' && $codigo_numerico_mae==0) {
            $codigo_mae_edi = '';
        }
        else if ($codigo_alfa_mae==''){
            $codigo_mae_edi = $codigo_numerico_mae;
        }
        else {
            $codigo_mae_edi = $codigo_alfa_mae.'-'.$codigo_numerico_mae;
        }*/

        echo '<tr>';
        echo '<td align="right" width="4%">' .$codigo_alfa.'</td>';
        echo '<td width="10%">' .$codigo_numerico.'</td>';
        echo '<td align="right" width="10%">' . number_format($fila->tbl_ite_pesagem_peso,2,',','.') . '</td>';
        echo '<td width="10%">' . $fila->tbl_ite_pesagem_sexo . '</td>';
        echo '<td align="center" width="10%">' . $fila->tbl_ite_pesagem_nascimento . '</td>';
        echo '<td width="10%">' . utf8_decode($fila->tbl_ite_pesagem_raca). '</td>';
        echo '<td width="10%">' . utf8_decode($fila->tbl_ite_pesagem_pelagem). '</td>';
        echo '<td width="10%">' .$codigo_mae_edi. '</td>';
        echo '<td width="18%">' . $fila->tbl_ite_pesagem_observacao. '</td>';
        echo '<td width="8%" style="text-align: left; color: red;">'.$animal_descarte.'</td>';
        echo '</tr>';
                                                            }
                                                        }
                                                    ?>    
                                                </tbody>
                                            </table>
                                                                                        
                                                <hr align="center"> 

                                                <div class="row">  
                                                    <?php
                                                        echo '<button type="button" class="btn btn-info pull-right" onclick="finalizar_sair()">Voltar</button>';
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
                        <button data-dismiss="modal" class="btn btn-default fecha_editar_dados" type="button">Fechar</button>
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
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
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




