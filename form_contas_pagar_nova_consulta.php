<?php
include "valida_sessao.inc";
include "conecta_mysql.inc";
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
        #modalMaisFiltros .bootstrap-select {
            width: 100% !important;
        }

        #modalMaisFiltros .modal-dialog {
            width: 760px;
        }

        #modalMaisFiltros .form-control {
            height: 34px;
            font-size: 13px;
        }

        #modalMaisFiltros .bootstrap-select .btn {
            height: 34px;
            font-size: 13px;
        }

        #modalMaisFiltros .radio-inline {
            display: block;
            margin-left: 0;
            margin-bottom: 5px;
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

        /* NOVA TELA DE CONSULTA - CONTAS A PAGAR */
        .ctp-box-consulta {
            border: 1px solid #333;
            margin: 20px 0 30px 0;
            padding: 35px 25px 25px 25px;
            min-height: 560px;
            position: relative;
            background: #fff;
        }

        .ctp-box-titulo {
            position: absolute;
            top: -18px;
            left: 45px;
            background: #fff;
            padding: 0 15px;
            font-size: 24px;
            color: #000;
        }

        .ctp-btn-mes,
        .ctp-btn-filtro {
            height: 40px;
            background: #d9d9d9;
            color: #777;
            border: 1px solid #c5c5c5;
            font-size: 13px;
        }

        .ctp-card-total {
            border: 1px solid #c8c8c8;
            border-top: 1px solid #c8c8c8;
            height: 52px;
            padding-top: 5px;
            text-align: center;
            color: #777;
            font-size: 14px;
            cursor: pointer;
            transition: all .2s ease;
            background: #fff;
        }

        .ctp-card-total:hover {
            background: #f8f9fa;
        }

        .ctp-card-total .valor {
            font-size: 16px;
            margin-top: -2px;
            font-weight: 400;
        }

        .ctp-card-total.ativo .valor {
            font-weight: 600;
            font-size: 19px;
        }

        .ctp-card-total.ativo.vermelho {
            border-top: 1px solid #d9534f;
        }

        .ctp-card-total.ativo.azul {
            border-top: 1px solid #4a90e2;
        }

        .ctp-card-total.ativo.verde {
            border-top: 1px solid #5cb85c;
        }

        .ctp-texto-vermelho {
            color: red;
        }

        .ctp-texto-azul {
            color: #005ecb;
        }

        .ctp-texto-verde {
            color: #00b050;
        }

        .ctp-area-info {
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .ctp-campo-busca {
            height: 44px;
            border-radius: 0;
        }

        #tabela_contas_pagar_nova {
            font-size: 11px;
            color: #4d5b66;
            width: 100% !important;
            table-layout: auto;
        }

        #tabela_contas_pagar_nova th,
        #tabela_contas_pagar_nova td {
            vertical-align: middle;
            white-space: nowrap !important;
            word-break: break-word;
        }

        #tabela_contas_pagar_nova td:nth-child(4),
        #tabela_contas_pagar_nova td:nth-child(5),
        #tabela_contas_pagar_nova td:nth-child(6) {
            white-space: normal !important;
        }

        #tabela_contas_pagar_nova th:last-child,
        #tabela_contas_pagar_nova td:last-child {
            width: 70px !important;
            min-width: 70px !important;
            white-space: nowrap !important;
        }

        .ctp-check-pago {
            color: green;
            font-size: 17px;
            font-weight: bold;
        }

        .ctp-icone-editar,
        .ctp-icone-pagar,
        .ctp-icone-excluir {
            color: #128cb8;
            font-size: 14px;
            transition: all .2s ease;
        }

        .ctp-icone-editar:hover,
        .ctp-icone-pagar:hover,
        .ctp-icone-excluir:hover {
            color: #0d6efd;
            transform: scale(1.15);
            text-decoration: none;
        }

        .ctp-acoes {
            white-space: nowrap;
            min-width: 80px;
        }

        .ctp-acoes a {
            display: inline-block;
            margin-right: 5px;
        }

        .ctp-acoes a:last-child {
            margin-right: 0;
        }

        #tabela_contas_pagar_nova th:last-child,
        #tabela_contas_pagar_nova td:last-child {
            width: 80px;
            min-width: 80px;
            white-space: nowrap;
        }

        .ctp-ordenacao {
            color: #c0c0c0;
            font-size: 10px;
            margin-left: 5px;
        }

        #modalMaisFiltros .modal-title {
            color: #333;
        }

        #modalMaisFiltros label {
            font-weight: 500;
            color: #666;
        }

    </style>
</head>

<body>

    <?php


    /*
        DADOS DE TESTE - NOVA CONSULTA
        Depois você troca esse array pela consulta real do banco.
    */
    $registros_teste_ctp = [
        [
            'pago' => true,
            'documento' => '2601071402',
            'parcela' => '001',
            'local' => 'FAZENDA CASA BRANCA',
            'conta' => 'Refeições e despesas de viagens',
            'razao' => 'EXAGRO',
            'emissao' => '02/01/2026',
            'vencimento' => '02/01/2026',
            'valor_parcela' => '76,93',
            'pagamento' => '02/01/2026',
            'valor_pago' => '76,93'
        ],
        [
            'pago' => true,
            'documento' => '2601071656',
            'parcela' => '001',
            'local' => 'FAZENDA PEDRA BONITA',
            'conta' => 'Refeições e despesas de viagens',
            'razao' => 'EXAGRO',
            'emissao' => '02/01/2026',
            'vencimento' => '02/01/2026',
            'valor_parcela' => '153,86',
            'pagamento' => '02/01/2026',
            'valor_pago' => '153,86'
        ],
        [
            'pago' => true,
            'documento' => '000037895',
            'parcela' => '001',
            'local' => 'FAZENDA PEDRA BONITA',
            'conta' => 'Medicamentos',
            'razao' => 'CASA DO AGRICULTOR - JAIME GOMES FERREIRA EPP',
            'emissao' => '03/12/2025',
            'vencimento' => '02/01/2026',
            'valor_parcela' => '73,98',
            'pagamento' => '05/01/2026',
            'valor_pago' => '73,98'
        ],
        [
            'pago' => false,
            'documento' => '000088888',
            'parcela' => '001',
            'local' => 'FAZENDA MODELO',
            'conta' => 'Energia Elétrica',
            'razao' => 'CEMIG',
            'emissao' => '10/05/2026',
            'vencimento' => '20/05/2026',
            'valor_parcela' => '890,00',
            'pagamento' => '',
            'valor_pago' => ''
        ],
        [
            'pago' => false,
            'documento' => '000099999',
            'parcela' => '001',
            'local' => 'FAZENDA CASA BRANCA',
            'conta' => 'Internet',
            'razao' => 'VIVO',
            'emissao' => '15/05/2026',
            'vencimento' => '25/05/2026',
            'valor_parcela' => '149,90',
            'pagamento' => '',
            'valor_pago' => ''
        ]
    ];

    function ctpValorBrParaFloat($valor)
    {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return floatval($valor);
    }

    function ctpDataBrParaDateTime($data)
    {
        return DateTime::createFromFormat('d/m/Y', $data);
    }

    function ctpFormatarMoedaBr($valor)
    {
        return number_format($valor, 2, ',', '.');
    }

    /*
        Para teste, deixei a data de hoje como 20/05/2026.
        Depois, no sistema real, você pode trocar por:
        $data_hoje_ctp = new DateTime();
    */
    $data_hoje_ctp = ctpDataBrParaDateTime('20/05/2026');
    $data_hoje_ctp->setTime(0, 0, 0);

    $total_vencidos_ctp = 0;
    $total_vencem_hoje_ctp = 0;
    $total_a_vencer_ctp = 0;
    $total_pagos_ctp = 0;
    $total_periodo_ctp = 0;

    foreach ($registros_teste_ctp as $item_ctp) {
        $valor_parcela_ctp = ctpValorBrParaFloat($item_ctp['valor_parcela']);
        $valor_pago_ctp = ctpValorBrParaFloat($item_ctp['valor_pago']);

        $total_periodo_ctp += $valor_parcela_ctp;

        if ($item_ctp['pago']) {
            $total_pagos_ctp += $valor_pago_ctp;
        } else {
            $data_vencimento_ctp = ctpDataBrParaDateTime($item_ctp['vencimento']);
            $data_vencimento_ctp->setTime(0, 0, 0);

            if ($data_vencimento_ctp < $data_hoje_ctp) {
                $total_vencidos_ctp += $valor_parcela_ctp;
            } elseif ($data_vencimento_ctp == $data_hoje_ctp) {
                $total_vencem_hoje_ctp += $valor_parcela_ctp;
            } else {
                $total_a_vencer_ctp += $valor_parcela_ctp;
            }
        }
    }

    $total_registros_ctp = count($registros_teste_ctp);


    ?>

    <!-- container section start -->
    <section id="container" class="">

        <!--sidebar start-->
        <?php
        include "cabecalho.php"; 
        include "opcoes_menu.php"; 
        include "limpar_secao_ctp_aceite.php";
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
                <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i>
                    <span class="titulo">Contas a Pagar</span></span>

                <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fas fa-search-dollar"></i> Contas a Pagar</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group opcoes_topo">
                            <a href="#">
                                <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Nova" />
                            </a>

                            <?php

                            @session_start();
                            if (isset($_SESSION['menu_gestao_adm'])) {
                                $array_gestao_adm = explode("!", $_SESSION['menu_gestao_adm']);

                                if ($array_gestao_adm[2] == 1) {
                                    echo '<a href="#">';
                                    echo '<input type="button" class="btn btn-info pull-right" aria-label="Left Align" 
                                          value="Aceite de Contas"/>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>

                            <div class="row" id="consulta_contas">
                                <div class="col-md-12">
                                <form method="GET" action="form_contas_pagar.php" enctype="multipart/form-data" id="form_consulta_contas">
                                <div class="tab-panel">
                                    <div class="tab-pane active">

                                <input id="lista_ctp_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_ctp'] . "'"; ?>>
                                <input id="limpar_filtro_contas" type="hidden" <?php echo "value='" . $_SESSION['limpa_conta_ctp'] . "'"; ?>>
                                <input id="exibe_local" type="hidden" <?php echo "value='".$local."'"; ?>>
                                <input id="exibe_cc" type="hidden" <?php echo "value='".$codigo_c_custo."'"; ?>>
                                <input id="exibe_fornecedor" type="hidden" <?php echo "value='".$razao_nome."'"; ?>>
                                <input id="exibe_conta" type="hidden" <?php echo "value='".$contas."'"; ?>>

                                <fieldset class="scheduler-border" id="dados_consulta">
                                    <legend class="scheduler-border fonte-legend">Consultar Contas a Pagar</legend>

                                    <div class="row">
                                        <div class="col-md-4 col-sm-8">
                                            <div class="btn-group" style="width: 100%;">
                                                <button type="button" class="btn ctp-btn-mes" style="width: 55px;">&lt;</button>
                                                <button type="button" class="btn ctp-btn-mes" style="width: calc(100% - 110px);">Maio 2026</button>
                                                <button type="button" class="btn ctp-btn-mes" style="width: 55px;">&gt;</button>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-4">
                                            <button type="button" class="btn ctp-btn-filtro btn-block" data-toggle="modal" data-target="#modalMaisFiltros">
                                                Mais Filtros
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-md-12">
                                            <div class="row" style="margin-left: 0; margin-right: 0;">
                                                    <div style="width: 20%; float: left; padding-left: 0; padding-right: 0;">
                                                    <div class="ctp-card-total vermelho" data-filtro="vencidos">
                                                        <div>Vencidos R$</div>
                                                        <div class="valor ctp-texto-vermelho">
                                                            <?php echo ctpFormatarMoedaBr($total_vencidos_ctp); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                    <div style="width: 20%; float: left; padding-left: 0; padding-right: 0;">
                                                
                                                    <div class="ctp-card-total vermelho" data-filtro="vencem_hoje">
                                                        <div>Vencem Hoje R$</div>
                                                        <div class="valor ctp-texto-vermelho">
                                                            <?php echo ctpFormatarMoedaBr($total_vencem_hoje_ctp); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                    <div style="width: 20%; float: left; padding-left: 0; padding-right: 0;">
                                                    <div class="ctp-card-total azul" data-filtro="a_vencer">
                                                        <div>A Vencer R$</div>
                                                        <div class="valor ctp-texto-azul">
                                                            <?php echo ctpFormatarMoedaBr($total_a_vencer_ctp); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                    <div style="width: 20%; float: left; padding-left: 0; padding-right: 0;">
                                                    <div class="ctp-card-total verde" data-filtro="pagos">
                                                        <div>Pagos R$</div>
                                                        <div class="valor ctp-texto-verde">
                                                            <?php echo ctpFormatarMoedaBr($total_pagos_ctp); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                    <div style="width: 20%; float: left; padding-left: 0; padding-right: 0;">
                                                    <div class="ctp-card-total azul" data-filtro="total_periodo">
                                                        <div>Total do Período R$</div>
                                                        <div class="valor ctp-texto-azul">
                                                            <?php echo ctpFormatarMoedaBr($total_periodo_ctp); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                   <div class="table-responsive" style="width: 100%; overflow-x: auto; font-size: 12px;">
                                        <table class="table table-striped table-hover" id="tabela_contas_pagar_nova">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"><input type="checkbox"></th>
                                                    <th>Documento</th>
                                                    <th>Parcela</th>
                                                    <th>Local</th>
                                                    <th>Conta</th>
                                                    <th>Fornecedor</th>
                                                    <th>Emissão</th>
                                                    <th>Vencimento</th>
                                                    <th>Valor Parcela</th>
                                                    <th>Pagamento</th>
                                                    <th>Valor Pago</th>
                                                    <th class="text-center">Ações</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($registros_teste_ctp as $item_ctp) { ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?php if ($item_ctp['pago']) { ?>
                                                                <span class="ctp-check-pago">✓</span>
                                                            <?php } ?>
                                                        </td>

                                                        <td><?php echo $item_ctp['documento']; ?></td>
                                                        <td><?php echo $item_ctp['parcela']; ?></td>
                                                        <td><?php echo $item_ctp['local']; ?></td>
                                                        <td><?php echo $item_ctp['conta']; ?></td>
                                                        <td><?php echo $item_ctp['razao']; ?></td>
                                                        <td><?php echo $item_ctp['emissao']; ?></td>
                                                        <td><?php echo $item_ctp['vencimento']; ?></td>
                                                        <td class="text-right"><?php echo $item_ctp['valor_parcela']; ?></td>
                                                        <td><?php echo $item_ctp['pagamento']; ?></td>
                                                        <td class="text-right"><?php echo $item_ctp['valor_pago']; ?></td>

                                                        <td class="text-center ctp-acoes">
                                                            <a href="#" class="ctp-icone-editar" title="Editar" data-toggle="tooltip" data-placement="top">
                                                                <i class="fas fa-pen"></i>
                                                            </a>

                                                            <?php if (!$item_ctp['pago']) { ?>
                                                                <a href="#" class="ctp-icone-pagar" title="Pagar Conta" data-toggle="tooltip" data-placement="top">
                                                                    <i class="far fa-check-circle"></i>
                                                                </a>

                                                                <a href="#" class="ctp-icone-excluir" title="Excluir" data-toggle="tooltip" data-placement="top">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

                <!-- page end-->
                                <!-- Modal Mais Filtros -->
                                <div class="modal fade" id="modalMaisFiltros" tabindex="-1" role="dialog" aria-labelledby="modalMaisFiltrosLabel" aria-hidden="true" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>

                                                <h4 class="modal-title" id="modalMaisFiltrosLabel">
                                                    Consultar Contas a Pagar
                                                </h4>
                                            </div>

                                            <div class="modal-body">

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="data_inicial" class="control-label">Data Inicial</label>
                                                        <input name="data_inicial" type="date" class="form-control" id="data_inicial" <?php echo "value='" . $data_inicial . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="data_final" class="control-label">Data Final</label>
                                                        <input name="data_final" type="date" class="form-control" id="data_final" <?php echo "value='" . $data_final . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="tipo_data" class="control-label">Tipo de Data</label>

                                                        <div style="margin-top: 8px;">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="tipo_data" value="V" <?php if ($tipo_data == 'V') { echo "checked"; } ?>> Vencimento
                                                            </label>

                                                            <label class="radio-inline">
                                                                <input type="radio" name="tipo_data" value="E" <?php if ($tipo_data == 'E') { echo "checked"; } ?>> Emissão
                                                            </label>

                                                            <label class="radio-inline">
                                                                <input type="radio" name="tipo_data" value="P" <?php if ($tipo_data == 'P') { echo "checked"; } ?>> Pagamento
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
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

                                                    <div class="form-group col-md-6">
                                                        <label for="codigo_cc" class="control-label">Centro de Custo</label>
                                                        <select class="form-control selectpicker" id="codigo_cc" name="codigo_cc" multiple>
                                                            <?php while ($registo_cc = mysqli_fetch_object($c_custo)) { ?>
                                                                <option value="<?php echo $registo_cc->tbl_cc_codigo_id ?>">
                                                                    <?php echo $registo_cc->tbl_cc_descricao; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="razao_nome" class="control-label">Fornecedor</label>
                                                        <select class="form-control selectpicker" multiple data-live-search="true" name="razao_nome" id="razao_nome" style="z-index:5;" data-size="6">
                                                            <?php
                                                            while ($reg_for = mysqli_fetch_object($fornecedor)) {
                                                                echo '<option value="' . $reg_for->tbl_pessoa_id . '">' . $reg_for->tbl_pessoa_nome . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="contas_selecionadas" class="control-label">Conta Contábil</label>
                                                        <input type="text" name="contas_selecionadas" id="contas_selecionadas" class="form-control" value="Todas ou (Clique p/ selecionar contas)">
                                                    </div>
                                                </div>

                                                <div class="row filtros">
                                                    <div class="col-md-12">
                                                        <p style="font-size: 12px; color: #829c9c; margin-bottom: 0;">
                                                            Filtros:
                                                            <span class="descricao_filtro" style="font-weight: normal;"></span>

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
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                    Fechar
                                                </button>

                                                <button type="button" class="btn btn-primary consultar" onclick="consultar_ctp()">
                                                    Consultar
                                                </button>
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

                <!-- modal baixa das contas selecionadas-->
                <div class="modal fade dados_baixa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Contas a Pagar - Baixar Registros</h4>
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
                                        <label for="codigo_forma_rec" class="control-label">Conta Pagamento</label>
                                        <select class="form-control" id="codigo_forma_rec" name="codigo_forma_rec">

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

                                            <button type="button" class="btn btn-primary pull-left" id="baixar_selecionadas" onClick="baixar_contas_selecionadas()">Confirme a Baixa
                                            </button>

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
                                <h4 class="modal-title">Contas a Pagar - Baixar Registro</h4>
                            </div>

                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="number_doc_baixar" class="control-label">Nº Documento</label>
                                            <input name="number_doc_baixar" type="number" class="form-control" id="number_doc_baixar" data-toggle='tooltip' data-placement='top' title="Caso não tenha o Nº, o sistema irá criar um automaticamente">
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
                                        <label for="data_pagamento_baixar" class="control-label">Data para Pagamento</label>
                                        <input name="data_pagamento_baixar" type="date" class="form-control" id="data_pagamento_baixar">
                                    </div>

                                    <div class="form-group">
                                        <label for="codigo_forma_pagto_baixar" class="control-label">Conta Pagamento</label>
                                        <select class="form-control" id="codigo_forma_pagto_baixar" name="codigo_forma_pagto_baixar">

                                            <option value="00" selected="selected">...</option>

                                            <?php while ($reg_conta_pag = mysqli_fetch_object($conta_pagamento_individual)) { ?>

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

                                            <button data-dismiss="modal" class="btn btn-info pull-right" type="button">Fechar</button>
                                        </div>
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
                                <h4 class="modal-title">Contas a Pagar</h4>
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
                                <h4 class="modal-title">Contas a Pagar - Erro</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="aguardar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x'></i></p>
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
                <!-- page end-->

            </section>
        </section>
    </section>

<!--main content end-->
 <div class="text-center">
     <div class="credits">
         <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
     </div>
 </div>


<!-- javascripts -->
<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.scrollTo.min.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
<script src="js/ga.js?<?php echo Versao; ?>" type="text/javascript" ></script>
<script src="js/bootstrap-switch.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.tagsinput.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.hotkeys.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg.js?<?php echo Versao; ?>"></script>
<script src="js/bootstrap-wysiwyg-custom.js?<?php echo Versao; ?>"></script>
<script src="js/moment.js?<?php echo Versao; ?>"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js"></script>

<script src="js/contas_pagar.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao; ?>"></script>


<script>
    $(document).ready(function () {
        $('#tabela_contas_pagar_nova').DataTable({
            paging: false,
            ordering: true,
            order: [[7, 'asc']],
            info: true,
            searching: true,
            autoWidth: false,
            scrollX: false,

            language: {
                sSearch: "Buscar na lista:",
                zeroRecords: "Nada encontrado",
                info: "Registros encontrados: _END_",
                infoEmpty: "Nenhum registro disponível",
                infoFiltered: "(filtrado de _MAX_ registros no total)",
                decimal: ",",
                thousands: "."
            },

            aoColumns: [
                { orderable: false },
                null,
                null,
                null,
                null,
                null,
                { sType: "date-br" },
                { sType: "date-br" },
                null,
                { sType: "date-br" },
                null,
                { orderable: false }
            ],

            dom: "<'row'<'col-md-6'i><'col-md-6'f>>t",

            initComplete: function () {
                $('#tabela_contas_pagar_nova').css('width', '100%');
            }
        });

        $('.ctp-card-total').on('click', function () {

            $('.ctp-card-total').removeClass('ativo');
            $(this).addClass('ativo');

            var filtroSelecionado = $(this).data('filtro');

            console.log('Filtro selecionado:', filtroSelecionado);

            /*
                Depois você pode chamar sua função:

                if (filtroSelecionado === 'vencidos') {
                    listarContasVencidas();
                }

                if (filtroSelecionado === 'vencem_hoje') {
                    listarContasVencemHoje();
                }

                if (filtroSelecionado === 'a_vencer') {
                    listarContasAVencer();
                }

                if (filtroSelecionado === 'pagos') {
                    listarContasPagas();
                }

                if (filtroSelecionado === 'total_periodo') {
                    listarTotalPeriodo();
                }
            */
        });

    });
</script>

<script>
    $(document).ready(function(){
       $('[data-toggle="tooltip"]').tooltip();   
    });
</script>

</body>

</html>
