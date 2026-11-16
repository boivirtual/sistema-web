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
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_manejo_animais'])) {
        $array_movimentacao = explode("!",$_SESSION['menu_manejo_animais']);

        if ($array_movimentacao[2] == 0){
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

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php";
        include "limpar_secao_selecao_matrizes.php";
        include "limpar_secao_compra_venda.php"; 
        include "limpar_secao_ctp.php"; 
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_movimentacao_animais.php"> Movimentações</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Aceite Transferência</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-cogs"></i> Aceite Transferência</h3>
                </div>
            </div>

            <div class="row col-lg-12">
                <div class="form-group">
                    <button type="button" class="btn btn-primary" id="botao_confirma" 
                        onClick="confirmar_aceite_transferencia_selecionados()">Confirmar Transferência</button>

                   <button type="button" class="btn btn-info pull-right" 
                        onClick="finalizar_sair()">Voltar</button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover" width="100%" 
                        style="font-size: 12px">
                        <thead>
                            <tr>
                                <th></th> 
                                <th>Data</th> 
                                <th>Local Origem</th>
                                <th>Local Destino</th> 
                                <th>Qtde Animais</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                $chave_anterior = '';

                                $rs = mysqli_query($conector, "SELECT * FROM tbl_movimentacao
                                    INNER JOIN tbl_pessoa
                                            ON tbl_pessoa_id =tbl_movimentacao_codigo_local_origem
                                         WHERE tbl_movimentacao_tipo=5 AND 
                                               tbl_movimentacao_situacao='N'
                                      ORDER BY tbl_movimentacao_data ASC");
                                    
                                while ($fila = mysqli_fetch_object($rs)){
                                    $local_destino = $fila->tbl_movimentacao_codigo_local_destino;

                                    foreach ($array_locais_usuario as $value) {
                                        $value = ltrim($value);
                                        $value = rtrim($value);

                                        if ($value==$local_destino) {
                                            $data_emissao = new DateTime($fila->tbl_movimentacao_data);
                                            $data_emissao_edi = $data_emissao->format('d/m/Y');
                                            $numero_id = $fila->tbl_movimentacao_id ;
                                            $desc_origem = $fila->tbl_pessoa_nome;
                                            $qtd_animais = $fila->tbl_movimentacao_qtd_animais_pesados;

                                            $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$local_destino'");
                                            $num_rows = mysqli_num_rows($tbl_local);

                                            if ($num_rows!=0){
                                                $reg = mysqli_fetch_object($tbl_local);
                                                $desc_destino = $reg->tbl_pessoa_nome;
                                            }
                                            else {
                                                $desc_destino = '';
                                            }

                                            echo "<tr>";
                                            echo "<td width='2%'>
                                            <input type='radio' name='id_mov' class='radiocheck' value='".$numero_id."'>
                                                </td>";
                                            echo "<td width='15%'>".$data_emissao_edi."</td>";
                                            echo "<td width='30%'>".$desc_origem."</td>";
                                            echo "<td width='30%'>".$desc_destino."</td>";
                                            echo "<td width='10%' align='center'>".$qtd_animais."</td>";
                                            echo "</tr>";
                                        }
                                    }
                                }
                            ?>    
                        </tbody>

                        <tfoot>
                            
                        </tfoot>
                        </table>
                    </div> <!-- fecha div responsivo -->
                    </section>
                </div>
            </div>

            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentações</h4>
                        </div>

                        <div class="modal-body"></div>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-info" type="button" onclick="abrir_modal_descricao_lote()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_composicao_descricao_lote" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle myLargeModalLabel"  data-backdrop="static">
                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Composição da Descrição do Lote
                            </h4>

                            <input type="hidden" name="numero_item" id="numero_item">

                            <input type="hidden" id="id_pasto">
                            <input type="hidden" id="descricao_pasto">
                            <input type="hidden" id="desc_lote_destino">
                            <input type="hidden" id="qual_pasto">
                            <input type="hidden" id="qual_programa" value="movimentacao">
                            <input type="hidden" id="descricao_lote_montada">
                            <input type="hidden" id="pasto_destino_estava_vazio">
                        </div>

                        <div class="modal-body">
                            <div class="container">

                            <div class='row'>
                                <div class="col-xs-12 col-md-12 span_centro">
                                     <span class="info_pasto desc_pasto">
                                    </span>
                                </div>                             
                            </div>

                            <div class="monta_descricao_lote" hidden>
                            <div class='row'>
                                <div class="form-group col-md-3 descricao_principal">
                                    <label class="control-label"><span class="required">*</span> Descrição do Lote</label>
                                    <select class="form-control" name="descricao_principal" id="descricao_principal" onchange="popular_situacao()">
                                    </select>
                                </div>

                                <div class='form-group col-md-3 exibir_parametro_2' hidden>
                                    <label class="control-label label_parametro_2">Situação</label>
                                    <select class="form-control" name="situacao_principal" id="situacao_principal" onchange="exibir_parametro_3()">
                                    </select>
                                </div>

                                <div class='form-group col-md-4 exibir_parametro_3' hidden>
                                    <label class="control-label label_parametro_3">Informar Data da Parição? </label>

                                    <div class="clearfix"></div>
                                    
                                    <label class="checkbox-inline">
                                        <input type="checkbox" id="com_data" name="data_paricao" value="S"> Sim
                                    </label>
                                </div>

                                <div class='col-md-3 exibir_parametro_4' hidden>
                                    <label class="control-label label_parametro_4">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal" id="data_paricao_principal" onchange="exibe_descricao_lote()">
                                </div>

                                <div class='col-md-3 exibir_parametro_4_data_mais' hidden>
                                    <label class="control-label label_parametro_4_mais">Mês/Ano da Parição</label>

                                    <input type="month" class="form-control" name="data_paricao_principal_mais" id="data_paricao_principal_mais" onchange="exibe_descricao_lote_mais_data()">
                                </div>

                                <div class='col-md-3 exibir_parametro_4_mais' hidden>
                                    <label class="control-label">&nbsp;</label>

                                    <a class='btn' href='#' style="font-size: 0.8em; font-weight: 500; color: #128cb8; float: right;" onclick="incluir_mais_data(1)"><i class="fa fa-plus" data-toggle='tooltip' data-placement='left' title='Informar mais datas'></i> Incluir mais Data</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 exibir_incluir_mais">
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.8em; font-weight: 500; text-align: right; color: #128cb8;" onclick="incluir_mais_lote()"><i class="fa fa-plus" data-toggle='tooltip' data-placement='left' title=''></i> Incluir mais lote</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao">
                                    <input type="text" id='descricao_novo_lote' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>
                                <div class="col-md-1 exibir_opcoes">
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(1)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao2" hidden> 
                                    <input type="text" id='descricao_novo_lote2' class="form-control" readonly style="border: none; background-color: transparent;">
                                </div>

                                <div class="col-md-1 exibir_opcoes2" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(2)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao3" hidden> 
                                    <input type="text" id='descricao_novo_lote3' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes3" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(3)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao4" hidden> 
                                    <input type="text" id='descricao_novo_lote4' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes4" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(4)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao5" hidden> 
                                    <input type="text" id='descricao_novo_lote5' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes5" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(5)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-10 col-md-11 exibir_descricao6" hidden> 
                                    <input type="text" id='descricao_novo_lote6' class="form-control" readonly style="border: none; background-color: transparent;">
                                    
                                </div>

                                <div class="col-md-1 exibir_opcoes6" hidden>
                                    <div class='btn-group'>
                                        <a class='btn' href='#' style="font-size: 0.9em; text-align: right; color: #128cb8;" onclick="excluir_lote(6)"><i class="fa fa-trash-o" data-toggle='tooltip' data-placement='left' title='Excluir esse lote'></i></a>
                                    </div>
                                </div>
                            </div>
                            </div> <!--Fim monta descricao lote -->
                        </div> <!-- Fim container --> 
                        </div> <!-- Fim modal-body-->

                        <div class="modal-footer">
                            <div class=" monta_descricao_lote" hidden>
                                <button type="button" class="btn btn-primary confirma_composicao" onclick="confirma_composicao_descricao_lote()">Confirmar
                                </button>

                                <!--<button type='button' class='btn btn-info pull-right voltar_descricao_lote' data-dismiss='modal'>Voltar
                                </button>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentações </h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_descricao_lote" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Composição da Descrição do Lote - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" onclick="fecha_mensagem_erro_descricao_lote();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </section>

<?php 
  $javascript_file_name = 'movimentacao.js';
  require 'rodape.php';
?>



                
                
