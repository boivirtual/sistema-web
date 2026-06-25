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
  <link rel="stylesheet" href="css/select-1.13.14.css">
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <style type="text/css">
        .bootstrap-select {
          width: 230px !important;
        }

        /* 1. Alinha o container de texto à direita */
        .bootstrap-select .bs-actionsbox {
            text-align: right;
            padding: 5px 5px 5px 5px;
        }

        /* 2. Garante que o link de deselect seja um bloco de texto que se mova */
        .bootstrap-select .bs-actionsbox .bs-deselect-all {
            display: inline-block;
            float: none;
            border: none;
            padding: 0;
            color: #007aff;
            background: transparent;
            font-size: 13px;
            font-weight: 500;
            width: 40%;
        }

    </style>

</head>

<body>

  <?php

   @ session_start();   
    if(isset($_SESSION['menu_gestao_adm'])) {
        $array_gestao_adm = explode("!",$_SESSION['menu_gestao_adm']);

        if ($array_gestao_adm[2] == 0){
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

    $data_inicial =  $_SESSION['data_inicial_aceite'];
    $data_final =  $_SESSION['data_final_aceite'];
    $tipo_data =  $_SESSION['tipo_data_aceite'];
    $fornecedor = $_SESSION['codigo_fornecedor_aceite'];
    $local = $_SESSION['codigo_local_aceite']; 
    $contas = $_SESSION['codigo_conta_aceite']; 

    $tbl_local = mysqli_query($conector, "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_classe=4 AND tbl_pessoa_lixeira=0");

    $tbl_fornecedor = mysqli_query($conector, "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_lixeira=0 AND (tbl_pessoa_classe=3 OR tbl_pessoa_classe=5) ORDER BY tbl_pessoa_nome ASC");

    // Arrays para o modal de edição de rateio
    $arr_local_rat_js = [];
    $rs_loc_erat = mysqli_query($conector, "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_classe=4 AND tbl_pessoa_lixeira=0 ORDER BY tbl_pessoa_nome");
    while ($r = mysqli_fetch_object($rs_loc_erat)) {
        $arr_local_rat_js[] = ['id' => $r->tbl_pessoa_id, 'nome' => $r->tbl_pessoa_nome];
    }

    $arr_cc_rat_js = [];
    $rs_cc_erat = mysqli_query($conector, "SELECT tbl_cc_codigo_id, tbl_cc_descricao FROM tbl_centro_custo WHERE tbl_cc_lixeira=0 ORDER BY tbl_cc_codigo_id");
    while ($r = mysqli_fetch_object($rs_cc_erat)) {
        $arr_cc_rat_js[] = ['id' => $r->tbl_cc_codigo_id, 'nome' => $r->tbl_cc_descricao];
    }

    $arr_conta_rat_js = [];
    $rs_cta_erat = mysqli_query($conector, "SELECT tbl_plano_contas_codigo_id, tbl_plano_contas_descricao, tbl_plano_contas_nivel FROM tbl_plano_contas WHERE tbl_plano_contas_debito_credito='D' AND tbl_plano_contas_lixeira=0 ORDER BY tbl_plano_contas_codigo_id");
    while ($r = mysqli_fetch_object($rs_cta_erat)) {
        $arr_conta_rat_js[] = ['id' => $r->tbl_plano_contas_codigo_id, 'nome' => $r->tbl_plano_contas_descricao, 'nivel' => (int)$r->tbl_plano_contas_nivel];
    }

    $codigo_usuario = intval($_SESSION['id_usuario']);

    $tbl_usuario = "SELECT id_usuario, lixeira_usuario, local_usuario FROM usuario
            WHERE id_usuario = $codigo_usuario AND
                  lixeira_usuario=0 ";
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario != 0) {
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario == 0) {
            $array_locais_usuario = '';
        }
    } else {
        $array_locais_usuario = '';
    }

?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; 
        include "limpar_secao_selecao_matrizes.php"; 
        include "limpar_secao_compra_venda.php";
        include "limpar_secao_ctr.php"; 
        include "limpar_secao_pesagem.php"; 
        include "limpar_secao_movimentacao.php"; 
        include "limpar_secao_nutricao.php"; 
        include "limpar_secao_nascimento.php";

    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_contas_pagar.php"> Conta a Pagar</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Aceite Contas a Pagar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-hand-holding"></i> Aceite Contas a Pagar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <a href="#">
                            <input type="button" class="btn btn-primary" aria-label="Left Align" value="Confirmar Selecionados" onClick="confirmar_aceite()"/>
                        </a>

                        <a href="#">
                            <input type="button" class="btn btn-info pull-right" aria-label="Left Align" 
                            value="Voltar" onClick="aceite_sair()"/>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div id="lista_contas_pagar"></div>
                </div>
            </div>

            <div class="modal fade" id="modal_filtro_aceite" tabindex="-1" role="dialog" 
             aria-labelledby="modal_incluirCenterTitle myLargeModalLabel"  data-backdrop="static">
                <div class="modal-lg modal-dialog modal-dialog-centered" role="document" style="width: 100%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Contas a Pagar Aceite - Filtros</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-2 pull-right">
                                    <a href="#" onclick="limpar_filtros()">Limpar Filtros
                                    </a>
                                </div>
                            </div>

                            <form>
                                <input id="exibe_local" type="hidden" <?php echo "value='".$local."'"; ?>>

                                <input id="exibe_fornecedor" type="hidden" <?php echo "value='".$fornecedor."'"; ?>>

                                <input id="exibe_conta" type="hidden" <?php echo "value='".$contas."'"; ?>>

                                <input id="limpar_filtro_contas" type="hidden" <?php echo "value='" . $_SESSION['limpa_conta_aceite'] . "'"; ?>>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="data_inicial" class="control-label">Data Incial</label>

                                        <input name="data_inicial" type="date" class="form-control" id="data_inicial" <?php echo "value='" . $data_inicial . "'"; ?>>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="data_final" class="control-label">Data Final</label>

                                        <input name="data_final" type="date" class="form-control" id="data_final" <?php echo "value='" . $data_final . "'"; ?>>
                                    </div>

                                    <div class="form-group">
                                        <label for="tipo_data" class="control-label">Tipo de Data</label>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="radio-inline">

                                        <input type="radio" name="tipo_data" id="vencimento" value="V" checked="true" <?php if ($tipo_data == 'V') {
                                            echo "checked";} ?>> Vencimento
                                        </label>
                                        
                                        <label class="radio-inline">
                                            <input type="radio" name="tipo_data" id="emissao" value="E" <?php if ($tipo_data == 'E') {
                                            echo "checked";} ?>> Emissão
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="codigo_fazenda" class="control-label">Local</label>
                                    
                                        <select class="form-control selectpicker" id="codigo_fazenda" multiple name="codigo_fazenda">
                                        <?php
                                            while ($reg_local = mysqli_fetch_object($tbl_local)) {

                                                foreach ($array_locais_usuario as $value) {
                                                    $value = ltrim($value);
                                                    $value = rtrim($value);
                                                    if ($value == $reg_local->tbl_pessoa_id) {
                                                        echo '<option value="' . $value . '">' . $reg_local->tbl_pessoa_nome . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="razao_nome" class="control-label">Fornecedor</label>
                                                    
                                        <select class="form-control selectpicker" multiple data-live-search="true" name="razao_nome" id="razao_nome" style="z-index:5;" data-size="6">

                                        <?php
                                        while ($reg_for = mysqli_fetch_object($tbl_fornecedor)) {

                                            echo '<option value="' . $reg_for->tbl_pessoa_id . '">' . $reg_for->tbl_pessoa_nome . '</option>';
                                            }?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="control-label">Conta Contábil</label>
                                                    
                                        <input type="text" name="contas_selecionadas" id="contas_selecionadas" class="form-control" value="Todas ou (Clique p/ selecionar contas)">
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-primary pull-right" onClick="aplicar_filtros()">Aplicar Filtros</button>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_conta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="exibe_contas_selecionadas()">&times;</button>
                            <h4 class="modal-title">Selecione a conta</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-3 pull-right">
                                        <a href="#" onclick="limpa_contas_selecionadas()">Limpar Seleção
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12" id="modal_conta_info" style="height: 50vh; overflow-y: auto;">
                                  </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button data-dismiss="modal" class="btn btn-primary pull-right" type="button" onclick="exibe_contas_selecionadas()">Fechar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Distribuição do Rateio -->
            <div class="modal fade" id="modal_rateio_aceite" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog" style="width:92%;max-width:920px;" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"><i class="fas fa-sitemap" style="color:#337ab7;"></i> Distribuição do Rateio</h4>
                        </div>
                        <div class="modal-body" id="rateio_aceite_body" style="overflow-x:auto;padding:12px 16px;"></div>
                        <div class="modal-footer">
                            <button class="btn btn-default" type="button" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Contas a Pagar Aceite</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="fechar_aceite_sucesso()">Fechar</button>
                        </div>
                     </div>
                 </div>
            </div>

            <div>
                <?php
                    include "ajuda.php";
                ?>
            </div>

        </section>
    </section>

<!-- Arrays para o editor de rateio (locais / CC / contas) -->
<script>
var _eratLocais = <?php echo json_encode($arr_local_rat_js); ?>;
var _eratCC     = <?php echo json_encode($arr_cc_rat_js); ?>;
var _eratContas = <?php echo json_encode($arr_conta_rat_js); ?>;
</script>

<!-- Modal: Editar Rateio -->
<div class="modal fade" id="modal_editar_rateio" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:96%;max-width:1100px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fas fa-edit" style="color:#337ab7;margin-right:6px;"></i>Editar Rateio &mdash; <span id="erat_titulo_doc" style="font-size:13px;font-weight:400;"></span></h4>
            </div>
            <div class="modal-body" style="padding:10px 16px;">
                <div id="erat_aviso" class="alert alert-danger" style="display:none;margin-bottom:8px;"></div>
                <div style="overflow-x:auto;">
                    <table class="tbl-parcelas" id="tbl_erat" style="width:100%;table-layout:fixed;">
                        <colgroup>
                            <col style="width:16%">
                            <col style="width:16%">
                            <col style="width:26%">
                            <col style="width:14%">
                            <col style="width:9%">
                            <col style="width:9%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Centro de Custos</th>
                                <th>Conta Contábil</th>
                                <th style="text-align:right;">Valor (R$)</th>
                                <th style="text-align:right;">%</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_erat"></tbody>
                        <tbody id="tbody_erat_footer">
                            <tr id="tr_erat_restante">
                                <td colspan="4" style="text-align:right;font-size:12px;color:#666;padding:6px 8px;border-top:1px solid #ddd;">
                                    Total Digitado: <span id="span_rat_total" style="color:#27ae60;font-weight:600;font-size:13px;margin-right:14px;">R$ 0,00</span>
                                    &nbsp;&nbsp;&nbsp;Restante a distribuir:
                                </td>
                                <td id="td_rat_vlr_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;white-space:nowrap;border-top:1px solid #ddd;">R$ 0,00</td>
                                <td id="td_rat_pct_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;border-top:1px solid #ddd;">0,00%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:10px;">
                    <button type="button" class="btn btn-info btn-sm" onclick="eratAdicionarLinha()">
                        <i class="fas fa-plus"></i> Adicionar Linha
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="eratSalvar()" style="float:left;">
                    <i class="fas fa-save"></i> Salvar Rateio
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?php
  $javascript_file_name = 'contas_pagar_aceite.js';
  require 'rodape.php';
?>



                
                
