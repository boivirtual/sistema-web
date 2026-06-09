<?php
include "valida_sessao.inc";
include "conecta_mysql.inc";

$data_sistema = date("Y-m-d");

$conta = mysqli_query($conector, "select * from tbl_plano_contas
        where tbl_plano_contas_lixeira=0 and
              tbl_plano_contas_debito_credito='C'
        order by tbl_plano_contas_codigo_id ASC");

$conta_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0");

$conta_pagamento_modal_baixar = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0");

$c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

$tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

$cliente = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=1 or tbl_pessoa_classe=2) order by tbl_pessoa_nome ASC"); 

$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario 
    WHERE id_usuario = '$codigo_usuario' AND 
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
    <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/daterangepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
    <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />
    <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <link rel="stylesheet" href="css/select-1.13.14.css"> 
    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <style type="text/css">
        .bootstrap-select {
          width: 340px !important;
        }

        /* 1. Alinha o container de texto à direita */
        .bootstrap-select .bs-actionsbox {
            text-align: right; 
            padding: 5px 5px 5px 5px; /* Ajusta o padding para melhor visualização */
        }

        /* 2. Garante que o link de deselect seja um bloco de texto que se mova */
        .bootstrap-select .bs-actionsbox .bs-deselect-all {
            display: inline-block; /* Garante que o link se comporte como um bloco inline */
            float: none; /* Garante que não haja float de versões antigas do Bootstrap */
            border: none;
            padding: 0; /* Remove padding interno que possa atrapalhar */
            color: #007aff;
            background: transparent;
            font-size: 13px;
            font-weight: 500; 
            width: 30%;       
        }
    </style>

</head>

<body>

    <?php

    @session_start();
    if (isset($_SESSION['menu_gestao_adm'])) {
        $array_gestao_adm = explode("!", $_SESSION['menu_gestao_adm']);

        if ($array_gestao_adm[3] == 0) {
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';
            echo '</div>';
            exit;
        }
    } else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuou o login!</span>';
        echo '</div>';
        exit;
    }

    if ($_SESSION['data_inicio_ctr'] == 0 && $_SESSION['razao_nome_ctr'] == '') {
        $data_inicial = $data_sistema;
    } else {
        $data_inicial =  $_SESSION['data_inicio_ctr'];
    }

    if ($_SESSION['data_fim_ctr'] == 0 && $_SESSION['razao_nome_ctr'] == '') {
        $data_final = $data_sistema;
    } else {
        $data_final =  $_SESSION['data_fim_ctr'];
    }

    if ($_SESSION['tipo_data_ctr'] == '') {
        $tipo_data = "V";
    } else {
        $tipo_data =  $_SESSION['tipo_data_ctr'];
    }

    $razao_nome = $_SESSION['razao_nome_ctr'];
    $codigo_c_custo = $_SESSION['codigo_c_custo_ctr'];
    $codigo_conta = $_SESSION['codigo_conta_ctr']; 
    $codigo_local = $_SESSION['codigo_local_ctr']; 

    $total_parcelas = 0;

    ?>

    <!-- container section start -->
    <section id="container" class="">

        <!--sidebar start-->
        <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php";
        include "limpar_secao_ctp.php";
        ?>
        <!--sidebar end-->

        <!-- container section start -->
        <section id="container" class="">

            <!--main content start-->
            <section id="main-content">
                <section class="wrapper" style="margin-left: 5px;">
                    <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><span class="titulo">Contas a Receber</span></span>

                    <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="page-header"><i class="fas fa-hand-holding-usd"></i> Contas a Receber</h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <a href="form_contas_receber_incluir.php">
                                    <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Nova" />
                                </a>
                            </div>

                            <div class="row col-md-12" id="consulta_contas">
                                <form method="GET" action="form_contas_receber.php" enctype="multipart/form-data">

                                    <div class="tab-panel">
                                        <div class="tab-pane active">
                                            <input id="lista_ctr_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_ctr'] . "'"; ?>>
                                        <input id="limpar_filtro_contas" type="hidden" <?php echo "value='" . $_SESSION['limpa_conta_ctr'] . "'"; ?>>
                                        <input id="exibe_local" type="hidden" <?php echo "value='".$codigo_local."'"; ?>>
                                        <input id="exibe_cc" type="hidden" <?php echo "value='".$codigo_c_custo."'"; ?>>
                                        <input id="exibe_cliente" type="hidden" <?php echo "value='".$razao_nome."'"; ?>>
                                        <input id="exibe_conta" type="hidden" <?php echo "value='".$codigo_conta."'"; ?>>

                                            <fieldset class="scheduler-border dados_consulta">
                                                <legend class="scheduler-border fonte-legend">Consultar Contas a Receber</legend>
                                                <div class="row digitar_filtros">
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
                                                        <input type="radio" name="tipo_data" value="V" <?php if ($tipo_data == 'V') {
                                                        echo "checked";
                                                        }?>> Vencimento
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipo_data" value="E" <?php if ($tipo_data == 'E') {
                                                        echo "checked";
                                                        } ?>> Emissão
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipo_data" value="P" <?php if ($tipo_data == 'P') {
                                                        echo "checked";
                                                        } ?>>Recebimento
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="row digitar_filtros">
                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_fazenda" class="control-label">Local</label>
                                                        <select class="form-control selectpicker" id="codigo_fazenda" multiple name="codigo_fazenda">
                                                        <?php 
                                                        while($reg_local = mysqli_fetch_object($tbl_local)) { 
                                                            foreach ($array_locais_usuario as $value) {
                                                                $value = ltrim($value);
                                                                $value = rtrim($value);
                                                                if ($value==$reg_local->tbl_pessoa_id) {
                                                                    echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                                }
                                                            }
                                                        } 
                                                         ?>

                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_cc" class="control-label">Centro de Custo</label>
                                                        <select class="form-control selectpicker" id="codigo_cc" name="codigo_cc" multiple>
                                                              
                                                        <?php while($registo_cc = mysqli_fetch_object($c_custo)) { ?>

                                                        <option value="<?php 
                                                           echo $registo_cc->tbl_cc_codigo_id ?>"

                                                        <?php 
                                                            if($registo_cc->tbl_cc_codigo_id==$codigo_c_custo) 
                                                                     { echo "selected"; }
                                                        ?>>
                                                                
                                                        <?php 
                                                            echo $registo_cc->tbl_cc_descricao;
                                                        ?>
                                                        </option>
                                                        <?php } ?>

                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="row digitar_filtros">    
                                                    <div class="form-group col-md-4">
                                                        <label for="razao_nome" class="control-label">Cliente/Parceiro</label>
                                                        <select class="form-control selectpicker" multiple data-live-search="true" name="razao_nome" id="razao_nome" style="z-index:5;" data-size="6">

                                                        <?php 
                                                            while($reg_cli = mysqli_fetch_object($cliente)) { 
                                                            
                                                            echo '<option value="'.$reg_cli->tbl_pessoa_id.'">' .$reg_cli->tbl_pessoa_nome. '</option>'; 
                                                            } 
                                                         ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_conta" class="control-label">Conta Contábil</label>

                                                        <input type="text" name="contas_selecionadas" id="contas_selecionadas" class="form-control" value="Todas ou (Clique p/ selecionar contas)">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label class="control-label">&nbsp;</label>
                                                        <button type="button" class="form-control btn btn-info pull-right consultar" onclick="consultar_ctr()">Consultar</button>
                                                    </div>
                                                </div>

                                                <div class="row filtros" hidden>
                                                    <div class="col-md-11">
                                                        <p style="font-size: 12px; color: #829c9c">Filtros: 
                                                            <span class="descricao_filtro" style="font-weight: normal;">
                                                            </span>

                                                            <span class="mais_filtros" hidden>&nbsp;
                                                                <a href="#" data-toggle='tooltip' data-placement='top' title="Exibir Filtros" onclick="exibe_mais_filtros()"> 
                                                                <i class="fas fa-filter"></i> +
                                                                </a>
                                                            </span>

                                                            <span class="menos_filtros" hidden>&nbsp;
                                                                <a href="#" data-toggle='tooltip' data-placement='top' title="Esconder Filtros" onclick="exibe_menos_filtros()"> 
                                                                <i class="fas fa-filter"></i> -
                                                                </a>
                                                            </span>
                                                        </p>
                                                    </div>

                                                    <div class="form-group col-md-1 voltar">
                                                        <!--<label class="control-label">&nbsp;</label>-->
                                                        <button type="button" class="form-control btn btn-info pull-right" onclick="exibe_mais_filtros()">Voltar</button>
                                                    </div>
                                                </div>

                                                <div class="row filtros" hidden>
                                                    <div class="col-md-12">
                                                        <a href="#" style="font-size: 0.9em; font-weight: 500; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle='tooltip' data-placement='top' title="Análise de Pagamentos" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                                    </div>
                                                </div> 
                                            </fieldset>
                                            <div id="lista_contas_receber"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- modal baixa das contas selecionadas-->
                    <div class="modal fade" id="modal_conta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered" role="document" style="overflow-y: initial !important">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="exibe_contas_selecionadas()">&times;</button>
                                    <h4 class="modal-title">Selecione a conta</h4>
                                </div>

                                <div class="modal-body" >
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

                    <div class="modal fade dados_baixa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Contas a Receber - Baixar Registros</h4>
                                </div>

                                <div class="modal-body">
                                    <form>
                                        <div class="form-group">
                                            <label for="total_baixar" class="control-label">Valor total para baixar</label>
                                            <input name="total_baixar" type="text" class="form-control" id="total_baixar" readonly="">
                                        </div>

                                        <div class="form-group">
                                            <label for="data_pagamento" class="control-label">Data para Pagamento</label>
                                            <input name="data_pagamento" type="date" class="form-control" id="data_pagamento">
                                        </div>

                                        <div class="form-group">
                                            <label for="codigo_conta_rec" class="control-label">Conta Pagamento</label>
                                            <select class="form-control" id="codigo_conta_rec" name="codigo_conta_rec">

                                                <option value="00" selected="selected">...</option>

                                                <?php while ($reg_conta_pag = mysqli_fetch_object($conta_pagamento)) { ?>

                                                    <option value="<?php
                                                                    echo $reg_conta_pag->tbl_conta_pagamento_id ?>">

                                                        <?php
                                                        echo $reg_conta_pag->tbl_conta_pagamento_descricao;
                                                        ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                <button type="button" class="btn btn-primary pull-left" id="baixar_selecionadas" onClick="baixar_contas_selecionadas()">Confirme a Baixa</button>

                                                <button data-dismiss="modal" class="btn btn-info pull-right fecha_dados_baixa" type="button">Fechar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- modal baixa conta individual-->
                    <div class="modal fade" id="modal_baixar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Contas a Receber - Baixar Registro</h4>
                                </div>

                                <div class="modal-body">
                                    <form>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <input name="id_baixar" type="hidden" class="form-control" id="id_baixar" >

                                                <label for="number_doc_baixar" class="control-label">Nº Documento</label>
                                                <input name="number_doc_baixar" type="text" class="form-control" id="number_doc_baixar">

                                                <input name="chave_ind" type="hidden" class="form-control" id="chave_ind" readonly="">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="number_parcela_baixar" class="control-label">Parcela</label>
                                                <input name="number_parcela_baixar" type="text" class="form-control" id="number_parcela_baixar" readonly="">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="vlr_total_baixar" class="control-label">Valor total para baixar</label>
                                            <input name="vlr_total_baixar" type="text" class="form-control" id="vlr_total_baixar" readonly="">

                                        </div>

                                        <div class="form-group">
                                            <label for="data_pagamento_baixar" class="control-label">Data para Recebimento</label>
                                            <input name="data_pagamento_baixar" type="date" class="form-control" id="data_pagamento_baixar">
                                        </div>

                                        <div class="form-group">
                                            <label for="codigo_conta_pagto_baixar" class="control-label">Conta Pagamento</label>
                                            <select class="form-control" id="codigo_conta_pagto_baixar" name="codigo_conta_pagto_baixar">

                                                <option value="00" selected="selected">...</option>

                                                <?php while ($reg_conta_pag = mysqli_fetch_object($conta_pagamento_modal_baixar)) { ?>

                                                    <option value="<?php
                                                                    echo $reg_conta_pag->tbl_conta_pagamento_id ?>">

                                                        <?php
                                                        echo $reg_conta_pag->tbl_conta_pagamento_descricao;
                                                        ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                <button type="button" class="btn btn-primary pull-left" id="baixar_selecionadas" onClick="baixar_conta_selecionada()">Confirme a Baixa</button>

                                                <button data-dismiss="modal" class="btn btn-info pull-right fecha_dados_baixa" type="button">Fechar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- page end-->
                    <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Contas a Receber</h4>
                                </div>
                                <div class="modal-body"></div>
                                <div class="modal-footer">
                                    <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Contas a Receber - Erro</h4>
                                </div>
                                <div class="modal-body"></div>
                                <div class="modal-footer">
                                    <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
                                        </div>
                                    </div>
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

            <?php
            $javascript_file_name = 'contas_receber.js';
            require 'rodape.php';
            ?>