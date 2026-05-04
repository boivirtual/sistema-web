<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $numero_pesagem = $_REQUEST['id'];

    $tbl_pesagem = mysqli_query($conector, "select * from tbl_pesagem
                                                where tbl_pesagem_id='$numero_pesagem'"); 

    $reg_pesagem = mysqli_fetch_object($tbl_pesagem);

    $nome_inclusao = $reg_pesagem->tbl_pesagem_incluido_por;
    $data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_incluido_em);
    $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

    $data_emissao = $reg_pesagem->tbl_pesagem_data;

    $codigo_local = $reg_pesagem->tbl_pesagem_codigo_local;
    $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $lote = $reg_pesagem->tbl_pesagem_lote;
    $peso_kg = $reg_pesagem->tbl_pesagem_peso_kg;
    $peso_arroba = $reg_pesagem->tbl_pesagem_peso_arroba;
    $peso_medio_kg = $reg_pesagem->tbl_pesagem_peso_medio_kg;
    $peso_medio_arroba = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
    $filtros=$reg_pesagem->tbl_pesagem_filtros;

    $data_sistema = date("Y-m-d");

    $rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                                           WHERE tbl_ite_pesagem_numero_id='$numero_pesagem' AND 
                                                 tbl_ite_pesagem_peso=0");
    $animais_a_pesar = mysqli_num_rows($rs);

    $rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                                           WHERE tbl_ite_pesagem_numero_id='$numero_pesagem' AND 
                                                 tbl_ite_pesagem_peso!=0");
    $animais_pesados = mysqli_num_rows($rs);
    $enviar_lixeira="S";

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
    $controle_estoque= $_SESSION['controle_estoque'];

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
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_pesagem_animais.php"> Pesagem</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Editar</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="icon_box-checked"></i> Pesagem - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data" id="form_gravar_pesagem">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">

                                            <input type="hidden" name="controle_estoque" id="controle_estoque"
                                            <?php echo "value='".$controle_estoque."'";?>>

                                            <input name="numero_pesagem_id" type="hidden" id="numero_pesagem_id" <?php echo "value='".$numero_pesagem."'";?>>
                                            <input  name="epoca_pesagem" type="hidden" id="epoca_pesagem" <?php echo "value='".$codigo_epoca."'";?>>

                                            <input  name="finalizar_pesagem" type="hidden" id="finalizar_pesagem" value="N">

                                            <div class="row">
                                                <div class="form-group col-md-7">
                                                    <label class="control-label">Descrição da Pesagem</label>
                                                    <input class="form-control" type="text" name="lote" onkeyup="maiuscula(this)"
                                                    <?php echo "value='".$lote."'";?>>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="control-label">Data da Pesagem</label>
                                                    <input class="form-control" type="date" name="data_pesagem"
                                                    <?php echo "value='".$data_emissao."'";?>>
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label class="control-label">Nº Documento</label>
                                                    <input class="form-control" type="text" readonly
                                                    <?php echo "value='".$numero_pesagem."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-10">
                                                    <p class="text-muted-dark descricao_filtro" style="font-size: 13px"><?php echo $filtros;?></p>

                                                    <input type="hidden" name="descricao_filtro_itens" class="descricao_filtro"
                                                    <?php echo "value='".$filtros."'";?>>    
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <span class="text-primary qtd_a_pesar"><?php echo 'Animais para Pesar: ' . $animais_a_pesar;?></span>
                                                    <input type="hidden" name="qtd_a_pesar" class="qtd_a_pesar" id="qtd_a_pesar"
                                                        <?php echo "value='".$animais_a_pesar."'";?>>
                                                </div>

                                                <div class="col-md-4">
                                                    <span class="text-primary qtd_pesado"><?php echo 'Animais Pesados: ' . $animais_pesados;?></span>
                                                    <input type="hidden" name="qtd_pesado" class="qtd_pesado" id="qtd_pesado"
                                                        <?php echo "value='".$animais_pesados."'";?>>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-md-3">
                                                    <span class="text-primary peso_total_kg"><?php echo 'Peso Total Kg: ' . number_format($peso_kg,2,',','.');?></span>
                                                    <input type="hidden" name="peso_total_kg" class="peso_total_kg" id="peso_total_kg"
                                                        <?php echo "value='".$peso_kg."'";?>>
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary peso_total_arroba"><?php echo 'Peso Total @: ' . number_format($peso_arroba,2,',','.');?></span>
                                                    <input type="hidden" name="peso_total_arroba" class="peso_total_arroba" id="peso_total_arroba"
                                                        <?php echo "value='".$peso_arroba."'";?>>
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary peso_medio_kg"><?php echo 'Peso Médio Kg: ' . number_format($peso_medio_kg,2,',','.');?></span>
                                                    <input type="hidden" name="peso_medio_kg" class="peso_medio_kg" id="peso_medio_kg" <?php echo "value='".$peso_medio_kg."'";?>>
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary peso_medio_arroba"><?php echo 'Peso Médio @: ' . number_format($peso_medio_arroba,2,',','.');?></span>
                                                    <input type="hidden" name="peso_medio_arroba" class="peso_medio_arroba" id="peso_medio_arroba"
                                                        <?php echo "value='".$peso_medio_arroba."'";?>>
                                                </div>
                                            </div>

                                            <hr align="center"> 

                                            <div class="row">  
                                                <button type="button" class="btn btn-primary" onclick="reseta_confirma();finalizar_pesagem_editar()">Finalizar Pesagem
                                                </button>
                                                <button type="button" class="btn btn-info pull-right" onclick="fecha_consultar_pesagem()">Voltar</button>
                                            </div>

                                            <table class="table table-striped table-advance table-hover" id="tabela_itens" width="100%">

                                                <thead>
                                                    <tr>
                                                        <th> Id</th>
                                                        <th> Peso</th>
                                                        <th> Último Peso</th>
                                                        <th> Sexo</th>
                                                        <th> Nascimento</th>
                                                        <th> Raça</th>
                                                        <th> Pelagem</th>
                                                        <th> Mãe</th>
                                                        <th> Observação</th> 
                                                        <th> Descarte</th> 
                                                       <!-- <th> <i class="icon_cogs"></i> Ações</th>-->
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    INNER JOIN tbl_animais
            ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
    WHERE tbl_ite_pesagem_numero_id='$numero_pesagem'");
$num_rows = mysqli_num_rows($rs);

                                                        if ($num_rows!=0){
                                                            while ($fila = mysqli_fetch_object($rs)){
                                                                $codigo = $fila->tbl_ite_pesagem_codigo_animal;
                                                                $item = $fila->tbl_ite_pesagem_numero_item;
                                                                $codigo_id = $fila->tbl_ite_pesagem_codigo_id_animal;
                                                                $peso = $fila->tbl_ite_pesagem_peso;
                                                                $ultimo_peso = $fila->tbl_ite_pesagem_ultimo_peso;

                                                                $raca = utf8_decode($fila->tbl_ite_pesagem_raca);
                                                                $pelagem = utf8_decode($fila->tbl_ite_pesagem_pelagem);
                                                                $nascimento = $fila->tbl_ite_pesagem_nascimento; 
                                                                $descarte = $fila->tbl_animal_descarte_reproducao; 

                                                                $sexo = utf8_decode($fila->tbl_ite_pesagem_sexo);
                                                                $mae = $fila->tbl_ite_pesagem_mae;
                                                                $obs = $fila->tbl_ite_pesagem_observacao;
if ($descarte=='S') {
    $animal_descarte='Sim';
}
else {
    $animal_descarte='';
}

if ($peso==0) {
    echo '<tr>';
    echo '<td class="item" hidden="">'.$item.'</td>';
    echo '<td class="codigo_id" hidden="">'.$codigo_id.'</td>';
    echo '<td>'.$codigo.'</td>';
    echo '<td class="peso"><input class="form-control input-sm peso" name="peso" type="number" onkeypress = "return numeros(this, event)" onblur="gravar_item_editar_pesagem()"> </td>';
    echo '<td align="center">'.$ultimo_peso.'</td>';
    echo '<td>'.$sexo.'</td>';
    echo '<td>'.$nascimento.'</td>';
    echo '<td>'.$raca.'</td>';
    echo '<td>'.$pelagem.'</td>';
    echo '<td>'.$mae.'</td>';
    echo '<td class="obs"><input class="form-control input-sm obs" name="obs" type="text" onkeypress = "return desabilita_enter (this, event)" onblur="gravar_item_editar_pesagem()" onkeyup="maiuscula(this)"></td>';
    echo '<td style="text-align: center; color: red;">'.$animal_descarte.'</td>';
    echo '</tr>';
}
else {
    echo '<tr>';
    echo '<td class="item" hidden="">'.$item.'</td>';
    echo '<td class="codigo_id" hidden="">'.$codigo_id.'</td>';
    echo '<td>'.$codigo.'</td>';
    echo '<td class="peso"><input class="form-control input-sm peso" name="peso" type="number" onkeypress = "return numeros(this, event)" value="'.$peso.'"> </td>';
    echo '<td align="center">'.$ultimo_peso.'</td>';
    echo '<td>'.$sexo.'</td>';
    echo '<td>'.$nascimento.'</td>';
    echo '<td>'.$raca.'</td>';
    echo '<td>'.$pelagem.'</td>';
    echo '<td>'.$mae.'</td>';
    echo '<td class="obs"><input class="form-control input-sm obs" name="obs" type="text" onkeypress = "return desabilita_enter (this, event)" onkeyup="maiuscula(this)" value="'.$obs.'"></td>';
    echo '<td style="text-align: center; color: red;">'.$animal_descarte.'</td>';
    echo '</tr>';
}

                                                            }
                                                        }
                                                    ?>   
                                                </tbody>
                                            </table>

                                            <input type="hidden" name="array_itens" id="array_itens">   

                                            <input type="hidden" name="codig_id" id="codig_id">          
                                            <input type="hidden" name="item_id" id="item_id">          
                                            <input type="hidden" name="peso_id" id="peso_id">          
                                            <input type="hidden" name="obs_id" id="obs_id">          
                                            <input type="hidden" name="excluir_id" id="excluir_id">

                                            <hr align="center"> 

                                            <div class="row"> 
                                                <button type="button" class="btn btn-primary" onclick="reseta_confirma();finalizar_pesagem_editar()">Finalizar Pesagem
                                                            </button>
                                                <button type="button" class="btn btn-info pull-right" onclick="fecha_consultar_pesagem()">Voltar</button>
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
                        <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno_excluir" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem</h4>
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

            <div class="modal fade" id="modal_finalizar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Pesagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-success" type="button" onclick="gravar_finalizar_pesagem();">Sim

                            <button data-dismiss="modal" class="btn btn-danger" type="button">Não
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- wrapper -->
    </section><!--main-content -->

<?php 
  $javascript_file_name = 'pesagem.js';
  require 'rodape.php';
?>

<script>

    function reseta_confirma(){
        clickedConfirm = true;
    }

    $(document).ready(function() {
        needToConfirm = false;
        clickedConfirm = false; 
        window.onbeforeunload = askConfirm;
    });

    function askConfirm() {
        if(clickedConfirm){
            needToConfirm = false;
        }
        if (needToConfirm) {
            return ''; 
        }
    }

    $('td.peso input.peso').change(function() {
        needToConfirm = true;
    });

</script>




