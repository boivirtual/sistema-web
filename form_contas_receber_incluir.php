<?php
function diferenca_data($data_validade)
{
    $data_inicial = $data_sistema = date("Y-m-d H:i:s");;
    $data_final = $data_validade;
    $time_inicial = strtotime($data_inicial);
    $time_final = strtotime($data_final);
    $diferenca = $time_final - $time_inicial;
    $dias = (int)floor($diferenca / (60 * 60 * 24));
    return $dias;
}

include "valida_sessao.inc";
include "conecta_mysql.inc";

$plano_contas = mysqli_query($conector, "select tbl_plano_contas_codigo_id, tbl_plano_contas_descricao, tbl_plano_contas_nivel from tbl_plano_contas where tbl_plano_contas_debito_credito='C' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_codigo_id");

$cli_for = mysqli_query($conector, "select tbl_pessoa_id, tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=1 or tbl_pessoa_classe=2) order by tbl_pessoa_nome ASC");

$conta_pag_pri = mysqli_query($conector, "select tbl_conta_pagamento_id, tbl_conta_pagamento_descricao, tbl_conta_pagamento_agencia, tbl_conta_pagamento_conta from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

$forma_pagamento = mysqli_query($conector, "select tbl_forma_pagamento_id, tbl_forma_pagamento_descricao from tbl_forma_pagamento where tbl_forma_pagamento_lixeira=0 order by tbl_forma_pagamento_id ASC");

$c_custo = mysqli_query($conector, "select tbl_cc_codigo_id, tbl_cc_descricao from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

$tipos_documentos = mysqli_query($conector, "select tbl_tipo_doc_id, tbl_tipo_doc_descricao from tbl_tipo_documento where tbl_tipo_doc_lixeira=0");

$tbl_local = mysqli_query($conector, "select tbl_pessoa_id, tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT local_usuario FROM usuario
        WHERE id_usuario = '$codigo_usuario' AND
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

// Arrays para uso no JS — geração dinâmica da tabela de parcelas
$arr_banco_js = [];
$rs_b = mysqli_query($conector, "select tbl_conta_pagamento_id, tbl_conta_pagamento_descricao, tbl_conta_pagamento_agencia, tbl_conta_pagamento_conta from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");
while ($r = mysqli_fetch_object($rs_b)) {
    $arr_banco_js[] = [
        'id'   => $r->tbl_conta_pagamento_id,
        'desc' => $r->tbl_conta_pagamento_descricao . ' (Age: ' . $r->tbl_conta_pagamento_agencia . ' Cta: ' . $r->tbl_conta_pagamento_conta . ')'
    ];
}

$arr_tipodoc_js = [];
$rs_t = mysqli_query($conector, "select tbl_tipo_doc_id, tbl_tipo_doc_descricao from tbl_tipo_documento where tbl_tipo_doc_lixeira=0");
while ($r = mysqli_fetch_object($rs_t)) {
    $arr_tipodoc_js[] = ['id' => $r->tbl_tipo_doc_id, 'desc' => $r->tbl_tipo_doc_descricao];
}

// Arrays para o editor inline de rateio
$arr_local_rat_js = [];
$rs_loc_rat = mysqli_query($conector, "select tbl_pessoa_id, tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0 order by tbl_pessoa_nome");
while ($r = mysqli_fetch_object($rs_loc_rat)) {
    foreach ($array_locais_usuario as $v) {
        if (trim($v) == $r->tbl_pessoa_id) {
            $arr_local_rat_js[] = ['id' => $r->tbl_pessoa_id, 'nome' => $r->tbl_pessoa_nome];
        }
    }
}

$arr_cc_rat_js = [];
$rs_cc_rat = mysqli_query($conector, "select tbl_cc_codigo_id, tbl_cc_descricao from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id");
while ($r = mysqli_fetch_object($rs_cc_rat)) {
    $arr_cc_rat_js[] = ['id' => $r->tbl_cc_codigo_id, 'nome' => $r->tbl_cc_descricao];
}

$arr_conta_rat_js = [];
$rs_cta_rat = mysqli_query($conector, "select tbl_plano_contas_codigo_id, tbl_plano_contas_descricao, tbl_plano_contas_nivel from tbl_plano_contas where tbl_plano_contas_debito_credito='C' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_codigo_id");
while ($r = mysqli_fetch_object($rs_cta_rat)) {
    $arr_conta_rat_js[] = ['id' => $r->tbl_plano_contas_codigo_id, 'nome' => $r->tbl_plano_contas_descricao, 'nivel' => (int)$r->tbl_plano_contas_nivel];
}

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
    <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="css/select-1.13.14.css" rel="stylesheet">
    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <style>
        /* Toggle switch */
        .toggle-switch {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }
        .toggle-switch input[type="checkbox"] { display: none; }
        .toggle-track {
            position: relative;
            width: 46px;
            height: 24px;
            background-color: #ccc;
            border-radius: 24px;
            transition: background-color 0.2s;
        }
        .toggle-track::after {
            content: '';
            position: absolute;
            top: 3px; left: 3px;
            width: 18px; height: 18px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s;
        }
        .toggle-switch input:checked + .toggle-track { background-color: #337ab7; }
        .toggle-switch input:checked + .toggle-track::after { transform: translateX(22px); }
        .toggle-label { font-size: 13px; color: #555; font-weight: normal; }

        /* Checkbox Pago */
        .pago-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            padding-top: 28px;
        }
        .pago-wrap label { margin: 0; font-weight: normal; font-size: 13px; cursor: pointer; }

        /* Seção Condição de Recebimento */
        .secao-titulo {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin: 18px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Botão adicionar anexo */
        .btn-anexo-add {
            background: none;
            border: none;
            color: #337ab7;
            font-size: 20px;
            padding: 0 4px;
            cursor: pointer;
            vertical-align: middle;
        }
        .btn-anexo-add:hover { color: #23527c; }

        /* Ajuste selectpicker */
        .bootstrap-select { width: 100% !important; }
        .bootstrap-select .bs-searchbox { position: relative; }
        .bootstrap-select .bs-searchbox input.form-control { padding-left: 28px; }
        .bootstrap-select .bs-searchbox::before {
            content: "\f002";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 12px;
            pointer-events: none;
        }
        .bootstrap-select .bs-actionsbox { text-align: right; padding: 5px; }
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

        /* Tabela de parcelas */
        .tbl-parcelas { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        .tbl-parcelas th {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            padding: 6px 8px;
            border-bottom: 2px solid #ddd;
            white-space: nowrap;
            background: #f7f7f7;
        }
        .tbl-parcelas td { padding: 5px 6px; vertical-align: middle; }
        .tbl-parcelas tbody tr:nth-child(even) td { background-color: #fafafa; }
        .tbl-parcelas .lbl-parcela { font-size: 12px; color: #555; font-weight: 600; white-space: nowrap; }
        .tbl-parcelas input.form-control,
        .tbl-parcelas select.form-control { height: 30px; font-size: 13px; padding: 4px 8px; }
        .tbl-parcelas .pago-parc {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding-top: 0;
        }
        #parc_totais { margin-top: 8px; font-size: 13px; color: #555; }
        #parc_totais span.valor-ok  { color: #27ae60; font-weight: 600; }
        #parc_totais span.valor-err { color: #c0392b; font-weight: 600; }

        /* Selectpickers do rateio — dropdown não ultrapassa a coluna */
        #linhas_rateio .bootstrap-select.open > .dropdown-menu { max-width: 100% !important; }

        #rateio_badge { display:none; margin-left:10px; font-size:13px; color:#27ae60; font-weight:600; }
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

    @session_start();
    $ultimo_cliente_cadastrado = $_SESSION['ultimo_cliente_cadastrado'];
    $_SESSION['ultimo_cliente_cadastrado'] = 0;
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

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper" style="margin-left: 5px;">
                <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_contas_receber.php">Contas a Receber</a><i class="fa fa-angle-right seta-direita"></i>
                    <span class="titulo">Contas a Receber - Incluir</span></span>

                <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fas fa-hand-holding-usd"></i> Contas a Receber - Incluir</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel-group">
                            <form method="POST" action="gravar_contas_receber.php" enctype="multipart/form-data" id="form_gravar_contas_receber">

                                <input name="tipo_operacao" type="hidden" id="tipo_operacao" value="1">

                                <div class="panel">
                                    <div class="panel-body">

                                        <div class="row" id="errors"></div>

                                        <ul class="nav nav-tabs m-bot15">
                                            <li class="active">
                                                <a data-toggle="tab" href="#dados">Dados</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                        <div id="dados" class="tab-pane active">

                                        <!-- ===== BOTÕES (topo) ===== -->
                                        <div class="row" style="margin-bottom: 10px;">
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirmar_gravar_ctr" onclick="confirmar_incluir_ctr()">Confirmar</button>
                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                            </div>
                                        </div>

                                        <!-- ===== LINHA 1: Cliente | Emissão | Descrição | Valor | Nº Documento ===== -->
                                        <div class="row">

                                            <div class="form-group col-md-3">
                                                <label for="codigo_cli_for" class="control-label"><span class="required">*</span> Cliente
                                                    <a href="form_cliente_fornecedor_incluir.php?voltar=1" style="margin-left: 6px;" data-toggle='tooltip' data-placement='top' title='Cadastrar novo cliente'>
                                                        <i class="far fa-plus-square" style="font-size: 16px; color: #337ab7;"></i>
                                                    </a>
                                                </label>
                                                <select class="form-control selectpicker" id="codigo_cli_for" name="codigo_cli_for" data-live-search="true" data-size="8">
                                                    <option value="999999999" selected="selected">...</option>
                                                    <?php while ($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>
                                                        <option value="<?php echo $registo_cli_for->tbl_pessoa_id; ?>"
                                                            <?php if ($registo_cli_for->tbl_pessoa_id == $ultimo_cliente_cadastrado) echo 'selected'; ?>>
                                                            <?php echo $registo_cli_for->tbl_pessoa_nome; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" id="nome_cli" name="nome_cli" value="">
                                                <small id="nome_cli_badge" style="display:none;color:#27ae60;font-weight:600;"></small>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="data_emissao" class="control-label"><span class="required">*</span> Emissão</label>
                                                <input name="data_emissao" type="date" class="form-control" id="data_emissao"
                                                       value="<?php echo $data_sistema; ?>"
                                                       onchange="onEmissaoChangeCtr()">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label for="descricao_compra" class="control-label"><span class="required">*</span> Descrição</label>
                                                <input name="descricao_compra" type="text" class="form-control" id="descricao_compra" onkeyup="maiuscula(this)">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="vlr_primeira_parcela" class="control-label"><span class="required">*</span> Valor</label>
                                                <input name="vlr_primeira_parcela" type="text" class="form-control" id="vlr_primeira_parcela"
                                                       placeholder="0,00"
                                                       onkeypress="digita_valor()"
                                                       onblur="onValorTotalBlurCtr()">
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="number_doc" class="control-label">Número Documento</label>
                                                <input name="number_doc" type="number" class="form-control" id="number_doc" maxlength="15">
                                            </div>

                                        </div>
                                        <!-- FIM LINHA 1 -->

                                        <!-- ===== LINHA 2: Habilitar Rateio | Local | Conta Contábil | Centro de Custos ===== -->
                                        <div class="row" id="linha2_row">

                                            <div class="form-group col-md-2" id="col_habilitar_rateio">
                                                <label class="control-label">Habilitar Rateio</label>
                                                <div style="padding-top:6px;">
                                                    <label class="toggle-switch" style="margin:0;">
                                                        <input type="checkbox" id="habilitar_rateio" name="habilitar_rateio">
                                                        <span class="toggle-track"></span>
                                                    </label>
                                                </div>
                                                <input type="hidden" id="rateio_json" name="rateio_json" value="">
                                                <!-- Status após confirmar rateio -->
                                                <div id="rateio_status" style="display:none; margin-top:6px; white-space:nowrap;">
                                                    <span style="font-size:12px; color:#27ae60; font-weight:600; display:inline-block;">
                                                        <i class="fas fa-check-circle"></i> Rateio Configurado
                                                    </span>
                                                    <a href="#" id="link_editar_rateio" onclick="editarRateioCtr(); return false;"
                                                       style="font-size:12px; color:#337ab7; margin-left:8px; display:inline-block;">
                                                        <i class="fas fa-pen"></i> Editar
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-3" id="col_local">
                                                <label for="codigo_local" class="control-label"><span class="required">*</span> Local</label>
                                                <select class="form-control" id="codigo_local" name="codigo_local[]">
                                                    <option value="" disabled selected data-hidden="true">...</option>
                                                    <?php
                                                    while ($reg_local = mysqli_fetch_object($tbl_local)) {
                                                        foreach ($array_locais_usuario as $value) {
                                                            $value = trim($value);
                                                            if ($value == $reg_local->tbl_pessoa_id) {
                                                                echo '<option value="' . $value . '" data-nome="' . htmlspecialchars($reg_local->tbl_pessoa_nome) . '">' . $reg_local->tbl_pessoa_nome . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <!-- Botão Confirmar Locais — aparece ao lado do Local quando rateio ON -->
                                            <div class="col-md-2" id="col_btn_confirmar_locais" style="display:none; padding-top:25px;">
                                                <button type="button" id="btn_confirmar_locais" class="btn btn-primary" style="white-space:nowrap;" onclick="confirmarLocaisRateioCtr()">
                                                    Confirmar
                                                </button>
                                            </div>

                                            <div class="form-group col-md-3" id="col_cc">
                                                <label for="codigo_cc" class="control-label"><span class="required">*</span> Centro de Custos</label>
                                                <select class="form-control" id="codigo_cc" name="codigo_cc">
                                                    <option value="" disabled>...</option>
                                                    <?php while ($registo_cc = mysqli_fetch_object($c_custo)) {
                                                        $sel = ($registo_cc->tbl_cc_codigo_id == '001') ? ' selected' : '';
                                                        echo '<option value="' . $registo_cc->tbl_cc_codigo_id . '"' . $sel . '>' . $registo_cc->tbl_cc_descricao . '</option>';
                                                    } ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3" id="col_conta">
                                                <label for="codigo_conta" class="control-label"><span class="required">*</span> Conta Contábil</label>
                                                <select class="form-control" id="codigo_conta" name="codigo_conta">
                                                    <option value="0000000" disabled selected data-hidden="true">...</option>
                                                    <?php while ($registro_pcontas = mysqli_fetch_object($plano_contas)) {
                                                        if ($registro_pcontas->tbl_plano_contas_nivel == 1) {
                                                            echo "<option value='{$registro_pcontas->tbl_plano_contas_codigo_id}' disabled style='color:#777; font-weight:600;'>" .
                                                                $registro_pcontas->tbl_plano_contas_descricao . "</option>";
                                                        } elseif ($registro_pcontas->tbl_plano_contas_nivel == 2) {
                                                            echo "<option value='{$registro_pcontas->tbl_plano_contas_codigo_id}' disabled style='color:#888;'>" .
                                                                str_repeat('&nbsp;', 4) . $registro_pcontas->tbl_plano_contas_descricao . "</option>";
                                                        } else {
                                                            echo "<option value='{$registro_pcontas->tbl_plano_contas_codigo_id}'>" .
                                                                str_repeat('&nbsp;', 8) . $registro_pcontas->tbl_plano_contas_descricao . "</option>";
                                                        }
                                                    } ?>
                                                </select>
                                            </div>

                                        </div>
                                        <!-- FIM LINHA 2 -->

                                        <!-- ===== SEÇÃO DISTRIBUIR RATEIO (aparece dinamicamente) ===== -->
                                        <div id="secao_distribuir_rateio" style="display:none; margin-top:10px;">
                                            <fieldset class="scheduler-border">
                                                <legend class="scheduler-border fonte-legend">Distribuir Rateio</legend>
                                                <!-- Tabela preview: cabeçalho + linha do select Local -->
                                                <div id="rateio_preview_header" style="overflow:visible;">
                                                    <table class="tbl-parcelas" style="width:100%; table-layout:fixed;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:16%;">Local</th>
                                                                <th style="width:16%;">Centro de Custos</th>
                                                                <th style="width:26%;">Conta Contábil</th>
                                                                <th style="width:14%;text-align:right;">Valor (R$)</th>
                                                                <th style="width:9%;text-align:right;">%</th>
                                                                <th style="width:9%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr id="tr_local_input">
                                                                <td id="td_local_select" colspan="2" style="vertical-align:middle; padding:4px 6px;"></td>
                                                                <td id="td_local_confirm" style="vertical-align:middle; padding:4px 6px; white-space:nowrap;"></td>
                                                                <td colspan="3"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="linhas_rateio" style="display:none;">
                                                    <!-- linhas geradas dinamicamente por JS -->
                                                </div>
                                            </fieldset>
                                        </div>
                                        <!-- FIM SEÇÃO DISTRIBUIR RATEIO -->

                                        <!-- ===== SEÇÃO: Condição de Recebimento ===== -->
                                        <div id="secao_condicao_normal">
                                        <div class="secao-titulo">Condição de Recebimento</div>

                                        <div class="row">

                                            <!-- Parcelamento — sempre visível -->
                                            <input type="hidden" id="parcelamento" name="parcelamento" value="0">
                                            <div class="form-group col-md-3">
                                                <label class="control-label"><span class="required">*</span> Parcelamento</label>
                                                <select class="form-control" id="sel_modo_parc" onchange="onParcelamentoChangeCtr()">
                                                    <option value="avista">A Vista</option>
                                                    <option value="uma_parcela">1 Parcela</option>
                                                    <option value="parc">Parcelado em 2x ou mais</option>
                                                </select>
                                            </div>
                                            <!-- Nº de Parcelas — só aparece quando Parcelado em 2x ou mais -->
                                            <div class="form-group col-md-2" id="bloco_qtd_parcelas" style="display:none;">
                                                <label for="qtd_parcelas_input" class="control-label">Nº de Parcelas</label>
                                                <input type="number" class="form-control" id="qtd_parcelas_input" min="1" max="360" placeholder="Nº" style="text-align:center;" oninput="onQtdParcelasChangeCtr(this.value)">
                                            </div>

                                            <!-- Bloco À Vista: Vencimento | Banco | Tipo Doc | Pago -->
                                            <div id="bloco_avista" class="col-md-9" style="padding: 0;">
                                                <div class="row" style="margin: 0;">
                                                    <div class="form-group col-md-3">
                                                        <label for="data_vencimento" class="control-label"><span class="required">*</span> Vencimento</label>
                                                        <input name="data_vencimento" type="date" class="form-control" id="data_vencimento">
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_forma_rec" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                                        <select class="form-control selectpicker" id="codigo_forma_rec" name="codigo_forma_rec" data-live-search="true" data-size="8">
                                                            <option value="0" selected="selected">...</option>
                                                            <?php while ($ln = mysqli_fetch_object($conta_pag_pri)) {
                                                                $dc = $ln->tbl_conta_pagamento_descricao . ' (Age: ' . $ln->tbl_conta_pagamento_agencia . ' Cta: ' . $ln->tbl_conta_pagamento_conta . ')';
                                                                echo '<option value="' . $ln->tbl_conta_pagamento_id . '">' . $dc . '</option>';
                                                            } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="tipo_doc" class="control-label">Tipo Documento</label>
                                                        <select class="form-control selectpicker" id="tipo_doc" name="tipo_doc" data-live-search="true" data-size="8">
                                                            <option value="00" selected="selected">...</option>
                                                            <?php while ($registro_tipo_doc = mysqli_fetch_object($tipos_documentos)) { ?>
                                                                <option value="<?php echo $registro_tipo_doc->tbl_tipo_doc_id; ?>">
                                                                    <?php echo $registro_tipo_doc->tbl_tipo_doc_descricao; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1 pago-wrap">
                                                        <input class="form-check-input" type="checkbox" value="" id="pago" name="pago" onchange="onPagoAvistaChangeCtr()">
                                                        <label for="pago">Pago</label>
                                                    </div>
                                                </div>
                                                <!-- Dados do recebimento (aparece ao marcar Pago) -->
                                                <div id="bloco_pago_avista" style="display:none; margin-top:4px; padding:4px 0;">
                                                    <div class="row" style="margin:0;">
                                                        <div class="form-group col-md-3">
                                                            <label class="control-label">Data Recebimento</label>
                                                            <input type="date" class="form-control" id="pago_data_pagamento" name="pago_data_pagamento">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label class="control-label">Desconto</label>
                                                            <input type="text" class="form-control" id="pago_desconto" name="pago_desconto" placeholder="0,00"
                                                                   onkeypress="mask.money.call(this, event)"
                                                                   onblur="calcularValorPagoAvistaCtr()">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label class="control-label">Juros</label>
                                                            <input type="text" class="form-control" id="pago_juros" name="pago_juros" placeholder="0,00"
                                                                   onkeypress="mask.money.call(this, event)"
                                                                   onblur="calcularValorPagoAvistaCtr()">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label class="control-label">Valor Recebido</label>
                                                            <input type="text" class="form-control" id="pago_valor_pago" name="pago_valor_pago" placeholder="0,00"
                                                                   readonly style="background:#f0f8e8; font-weight:600;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Bloco cabeçalho parcelado: Intervalo | 1º Vencimento -->
                                            <div id="bloco_parc_header" class="col-md-7" style="padding: 0; display: none;">
                                                <div class="row" style="margin: 0;">
                                                    <div class="form-group col-md-3">
                                                        <label for="intervalo" class="control-label"><span class="required">*</span> Intervalo (dias)</label>
                                                        <input type="number" class="form-control" id="intervalo" name="intervalo"
                                                               value="30" min="1" onchange="onIntervaloChangeCtr()">
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="primeiro_vencimento" class="control-label"><span class="required">*</span> 1º Vencimento</label>
                                                        <input type="date" class="form-control" id="primeiro_vencimento" name="primeiro_vencimento"
                                                               onchange="recalcularDatasCtr()">
                                                    </div>
                                                </div>
                                            </div>

                                        </div><!-- /row condição -->

                                        <!-- Tabela dinâmica de parcelas -->
                                        <div id="bloco_parcelas" style="display: none; margin-top: 10px; overflow-x: auto;">
                                            <table class="tbl-parcelas">
                                                <thead>
                                                    <tr>
                                                        <th style="width:13%">Parcela</th>
                                                        <th style="width:12%">Vencimento</th>
                                                        <th style="width:10%">Valor (R$)</th>
                                                        <th style="width:8%">% Perc.</th>
                                                        <th style="width:35%">Banco/Conta Pagamento</th>
                                                        <th style="width:17%">Tipo Documento</th>
                                                        <th style="width:5%;text-align:center">Pago</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody_parcelas"></tbody>
                                            </table>
                                            <div id="parc_totais"></div>
                                        </div>
                                        <!-- FIM Condição de Recebimento -->
                                        </div><!-- /secao_condicao_normal -->

                                        <!-- ===== OBSERVAÇÕES ===== -->
                                        <div class="row" style="margin-top: 12px;">
                                            <div class="form-group col-md-12">
                                                <label for="observacoes" class="control-label">Observações</label>
                                                <input name="observacoes" type="text" class="form-control" id="observacoes">
                                            </div>
                                        </div>

                                        <!-- ===== ANEXO ===== -->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div style="display: flex; align-items: flex-start; gap: 48px;">
                                                    <div>
                                                        <label class="control-label"><i class="fas fa-paperclip" style="color:#337ab7;"></i> Anexar Documento</label>
                                                        <div style="display: flex; align-items: center; gap: 6px;">
                                                            <input type="file" id="anexo_picker" class="form-control" style="max-width: 320px;" onchange="onAnexoPickerChangeCtr(this)">
                                                        </div>
                                                        <div id="lista_anexos"></div>
                                                    </div>
                                                    <div style="margin-left: 16px;">
                                                        <label class="control-label"><i class="fas fa-link" style="color:#337ab7;"></i> Anexar Link</label>
                                                        <div style="display: flex; align-items: center; gap: 6px;">
                                                            <input type="text" id="link_desc_input" class="form-control" placeholder="Descrição do link" style="max-width: 200px;">
                                                            <input type="url" id="link_url_input" class="form-control" placeholder="https://..." style="max-width: 220px;" onkeydown="onLinkUrlKeydownCtr(event)" onblur="onLinkUrlBlurCtr()" data-toggle="tooltip" data-placement="top" title="Após digitar o https://, tecle ENTER para confirmar o Link">
                                                        </div>
                                                        <div id="lista_links"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ===== BOTÕES ===== -->
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-md-12">
                                                <button type="button" class="btn btn-primary confirmar_gravar_ctr" onclick="confirmar_incluir_ctr()">Confirmar</button>
                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                            </div>
                                        </div>

                                        </div> <!-- dados-->
                                        </div> <!--tab-content -->

                                    </div><!--panel-body-->
                                </div><!--panel-->
                            </form>
                        </section> <!-- panel-group -->
                    </div> <!--col-lg-12 2-->
                </div> <!--row 2-->

                <!-- Modal: Confirmar desativar rateio -->
                <div class="modal fade" id="modal_fechar_rateio" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:480px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Habilitar Rateio</h4>
                            </div>
                            <div class="modal-body">
                                <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                <p class="desc_modal">Ao desativar o rateio, toda a configuração realizada será perdida. Deseja continuar?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onclick="confirmarFecharRateioCtr()">Confirmar</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

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

                <div>
                    <?php
                    include "ajuda.php";
                    ?>
                </div>

            </section> <!-- wrapper -->
        </section><!--main-content -->

        <!-- ============================================================
             JAVASCRIPT — Dados PHP exportados para o editor de rateio e
             tabela de parcelas (a lógica em si vive em js/contas_receber.js)
        ============================================================ -->
        <script>
        var CTR_BANCOS     = <?php echo json_encode($arr_banco_js,     JSON_UNESCAPED_UNICODE); ?>;
        var CTR_TIPODOCS   = <?php echo json_encode($arr_tipodoc_js,   JSON_UNESCAPED_UNICODE); ?>;
        var CTR_LOCAIS     = <?php echo json_encode($arr_local_rat_js, JSON_UNESCAPED_UNICODE); ?>;
        var CTR_CCS        = <?php echo json_encode($arr_cc_rat_js,    JSON_UNESCAPED_UNICODE); ?>;
        var CTR_CONTAS_RAT = <?php echo json_encode($arr_conta_rat_js, JSON_UNESCAPED_UNICODE); ?>;
        </script>

        <?php
        $javascript_file_name = 'contas_receber.js';
        require 'rodape.php';
        ?>
