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

include "conecta_mysql.inc";
include 'valida_sessao.inc';

$plano_contas = mysqli_query($conector, "select tbl_plano_contas_codigo_id, tbl_plano_contas_descricao, tbl_plano_contas_nivel from tbl_plano_contas where tbl_plano_contas_debito_credito='D' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_codigo_id");

$cli_for = mysqli_query($conector, "select tbl_pessoa_id, tbl_pessoa_nome from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=3 or tbl_pessoa_classe=5) order by tbl_pessoa_nome ASC");

$conta_pag_pri = mysqli_query($conector, "select tbl_conta_pagamento_id, tbl_conta_pagamento_descricao, tbl_conta_pagamento_agencia, tbl_conta_pagamento_conta from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

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

        /* Seção Condição de Pagamento */
        .secao-titulo {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin: 18px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Área de toggles */
        .area-toggles {
            display: flex;
            align-items: center;
            gap: 30px;
            margin: 14px 0 4px 0;
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
        .tbl-parcelas { width: 100%; border-collapse: collapse; margin-top: 8px; }
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
    </style>
</head>

<body>

    <?php
    @session_start();
    if (isset($_SESSION['menu_gestao_adm'])) {
        $array_gestao_adm = explode("!", $_SESSION['menu_gestao_adm']);
        if ($array_gestao_adm[1] == 0) {
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
    $ultimo_fornecedor_cadastrado = $_SESSION['ultimo_cliente_cadastrado'];
    $_SESSION['ultimo_cliente_cadastrado'] = 0;
    ?>

    <section id="container" class="">

        <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php";
        include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php";
        include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
        ?>

        <section id="main-content">
            <section class="wrapper" style="margin-left: 5px;">

                <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i>
                    <a class="voltar-menu" href="form_contas_pagar.php"> Contas a Pagar</a>
                    <i class="fa fa-angle-right seta-direita"></i>
                    <span class="titulo">Contas a Pagar Incluir</span>
                </span>

                <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fas fa-search-dollar"></i> Contas a Pagar - Incluir</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="gravar_contas_pagar.php" enctype="multipart/form-data" id="form_gravar_contas_pagar">

                            <input name="tipo_gravacao"  type="hidden" id="tipo_gravacao">
                            <input name="tipo_operacao"  type="hidden" id="tipo_operacao" value="1">
                            <input name="array_fazendas" type="hidden" id="array_fazendas">

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

                                    <!-- ===== LINHA 1: Fornecedor | Emissão | Descrição | Valor | Nº Documento ===== -->
                                    <div class="row">

                                        <div class="form-group col-md-3">
                                            <label for="codigo_cli_for" class="control-label"><span class="required">*</span> Fornecedor</label>
                                            <div style="display: flex; align-items: flex-start; gap: 6px;">
                                                <div style="flex: 1;">
                                                    <select class="form-control selectpicker" id="codigo_cli_for" name="codigo_cli_for" data-live-search="true" data-size="8">
                                                        <option value="999999999" selected="selected">...</option>
                                                        <?php while ($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>
                                                            <option value="<?php echo $registo_cli_for->tbl_pessoa_id; ?>"
                                                                <?php if ($registo_cli_for->tbl_pessoa_id == $ultimo_fornecedor_cadastrado) echo 'selected'; ?>>
                                                                <?php echo $registo_cli_for->tbl_pessoa_nome; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <a href="form_cliente_fornecedor_incluir.php?voltar=3" style="line-height: 34px;" data-toggle='tooltip' data-placement='top' title='Cadastrar novo fornecedor'>
                                                    <i class="far fa-plus-square" style="font-size: 16px; color: #337ab7;"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="data_emissao" class="control-label"><span class="required">*</span> Emissão</label>
                                            <input name="data_emissao" type="date" class="form-control" id="data_emissao"
                                                   value="<?php echo $data_sistema; ?>"
                                                   onchange="onEmissaoChange()">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="descricao_compra" class="control-label"><span class="required">*</span> Descrição da Compra</label>
                                            <input name="descricao_compra" type="text" class="form-control" id="descricao_compra" onkeyup="maiuscula(this)">
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="vlr_primeira_parcela" class="control-label"><span class="required">*</span> Valor</label>
                                            <input name="vlr_primeira_parcela" type="text" class="form-control" id="vlr_primeira_parcela"
                                                   placeholder="0,00"
                                                   onkeypress="digita_valor()"
                                                   onblur="onValorTotalBlur()">
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="number_doc" class="control-label">Número Documento</label>
                                            <input name="number_doc" type="number" class="form-control" id="number_doc" maxlength="15">
                                        </div>

                                    </div>
                                    <!-- FIM LINHA 1 -->

                                    <!-- ===== LINHA 2: Habilitar Rateio | Local | Conta Contábil | Centro de Custos ===== -->
                                    <div class="row">

                                        <div class="form-group col-md-2">
                                            <label class="control-label">Habilitar Rateio</label>
                                            <div style="padding-top: 6px;">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" id="habilitar_rateio" name="habilitar_rateio">
                                                    <span class="toggle-track"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="codigo_fazenda" class="control-label"><span class="required">*</span> Local</label>
                                            <select class="form-control selectpicker" id="codigo_fazenda" name="codigo_fazenda[]" multiple data-live-search="true" data-size="8">
                                                <?php
                                                while ($reg_local = mysqli_fetch_object($tbl_local)) {
                                                    foreach ($array_locais_usuario as $value) {
                                                        $value = trim($value);
                                                        if ($value == $reg_local->tbl_pessoa_id) {
                                                            echo '<option value="' . $value . '">' . $reg_local->tbl_pessoa_nome . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="codigo_conta" class="control-label"><span class="required">*</span> Conta Contábil</label>
                                            <select class="form-control selectpicker" id="codigo_conta" name="codigo_conta" data-live-search="true" data-size="8">
                                                <option value="0000000">...</option>
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

                                        <div class="form-group col-md-3">
                                            <label for="codigo_cc" class="control-label"><span class="required">*</span> Centro de Custos</label>
                                            <select class="form-control selectpicker" id="codigo_cc" name="codigo_cc" data-live-search="true" data-size="8">
                                                <option value="">...</option>
                                                <?php while ($registo_cc = mysqli_fetch_object($c_custo)) { ?>
                                                    <option value="<?php echo $registo_cc->tbl_cc_codigo_id; ?>">
                                                        <?php echo $registo_cc->tbl_cc_descricao; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <!-- FIM LINHA 2 -->

                                    <!-- ===== LINHA 3: Repetir Lançamento ===== -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="area-toggles">
                                                <div>
                                                    <label class="toggle-label" style="margin-right: 8px;">Repetir Lançamento?</label>
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" id="repetir_lancamento" name="repetir_lancamento">
                                                        <span class="toggle-track"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FIM LINHA 3 -->

                                    <!-- ===== SEÇÃO: Condição de Pagamento ===== -->
                                    <div class="secao-titulo">Condição de Pagamento</div>

                                    <div class="row">

                                        <!-- Parcelamento — sempre visível -->
                                        <div class="form-group col-md-2">
                                            <label for="parcelamento" class="control-label"><span class="required">*</span> Parcelamento</label>
                                            <select class="form-control" id="parcelamento" name="parcelamento" onchange="onParcelamentoChange()">
                                                <option value="0">A Vista</option>
                                                <option value="1">1x</option>
                                                <option value="2">2x</option>
                                                <option value="3">3x</option>
                                                <option value="4">4x</option>
                                                <option value="5">5x</option>
                                                <option value="6">6x</option>
                                                <option value="7">7x</option>
                                                <option value="8">8x</option>
                                                <option value="9">9x</option>
                                                <option value="10">10x</option>
                                                <option value="11">11x</option>
                                                <option value="12">12x</option>
                                                <option value="13">13x</option>
                                                <option value="14">14x</option>
                                                <option value="15">15x</option>
                                                <option value="16">16x</option>
                                                <option value="17">17x</option>
                                                <option value="18">18x</option>
                                                <option value="19">19x</option>
                                                <option value="20">20x</option>
                                                <option value="21">21x</option>
                                                <option value="22">22x</option>
                                                <option value="23">23x</option>
                                                <option value="24">24x</option>
                                            </select>
                                        </div>

                                        <!-- Bloco À Vista: Vencimento | Banco | Tipo Doc | Pago -->
                                        <div id="bloco_avista" class="col-md-10" style="padding: 0;">
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
                                                    <input class="form-check-input" type="checkbox" value="" id="pago" name="pago">
                                                    <label for="pago">Pago</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bloco cabeçalho parcelado: 1º Vencimento | Intervalo -->
                                        <div id="bloco_parc_header" class="col-md-10" style="padding: 0; display: none;">
                                            <div class="row" style="margin: 0;">
                                                <div class="form-group col-md-3">
                                                    <label for="primeiro_vencimento" class="control-label"><span class="required">*</span> 1º Vencimento</label>
                                                    <input type="date" class="form-control" id="primeiro_vencimento" name="primeiro_vencimento"
                                                           onchange="recalcularDatas()">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="intervalo" class="control-label"><span class="required">*</span> Intervalo (dias)</label>
                                                    <input type="number" class="form-control" id="intervalo" name="intervalo"
                                                           value="30" min="1" onchange="recalcularDatas()">
                                                </div>
                                            </div>
                                        </div>

                                    </div><!-- /row condição -->

                                    <!-- Tabela dinâmica de parcelas -->
                                    <div id="bloco_parcelas" style="display: none; margin-top: 10px; overflow-x: auto;">
                                        <table class="tbl-parcelas">
                                            <thead>
                                                <tr>
                                                    <th>Parcela</th>
                                                    <th>Vencimento</th>
                                                    <th style="width:120px">Valor (R$)</th>
                                                    <th style="width:90px">% Perc.</th>
                                                    <th>Banco/Conta Pagamento</th>
                                                    <th>Tipo Documento</th>
                                                    <th style="text-align:center">Pago</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_parcelas"></tbody>
                                        </table>
                                        <div id="parc_totais"></div>
                                    </div>
                                    <!-- FIM Condição de Pagamento -->

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
                                            <label class="control-label">Anexo</label>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <input type="file" name="anexo[]" id="anexo_0" class="form-control" style="max-width: 320px;">
                                                <button type="button" class="btn-anexo-add" onclick="adicionarAnexo()" data-toggle='tooltip' data-placement='top' title="Adicionar mais anexos">
                                                    <i class="far fa-plus-square" style="font-size: 16px;"></i>
                                                </button>
                                            </div>
                                            <div id="lista_anexos"></div>
                                        </div>
                                    </div>

                                    <!-- ===== BOTÕES ===== -->
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirmar_gravar" onclick="confirmar_incluir()">Confirmar</button>
                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                        </div>
                                    </div>

                                    </div><!-- tab-pane #dados -->
                                    </div><!-- tab-content -->

                                </div><!--panel-body-->
                            </div><!--panel-->
                        </form>
                    </div><!--col-lg-12-->
                </div><!--row-->

                <!-- Modal: Totais por Fazenda (rateio) -->
                <div class="modal fade" id="modal_fazendas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Totais por Fazenda</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row form-group">
                                    <div class="col-md-6"><span class="text-primary total_compra"></span></div>
                                    <div class="col-md-6"><span class="text-primary primeira_parcela"></span></div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6"><span class="text-primary parcelas"></span></div>
                                    <div class="col-md-6"><span class="text-primary vlr_parcelas"></span></div>
                                </div>
                                <table class="table table-striped table-advance table-hover" id="tabela_fazendas" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center">Fazenda</th>
                                            <th>Percentual</th>
                                            <th>Valor</th>
                                            <th>Parcelas</th>
                                            <th hidden>Código</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary confirmar_gravar" type="button" onclick="gravar_conta();">Confirmar Inclusão</button>
                                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal: Retorno -->
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

                <!-- Modal: Erro -->
                <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Contas a Pagar - Mensagem</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div><?php include "ajuda.php"; ?></div>

            </section><!-- wrapper -->
        </section><!--main-content-->

        <!-- ============================================================
             JAVASCRIPT — Parcelamento dinâmico
        ============================================================ -->
        <script>
        // Dados PHP exportados para JS
        var CTP_BANCOS    = <?php echo json_encode($arr_banco_js,    JSON_UNESCAPED_UNICODE); ?>;
        var CTP_TIPODOCS  = <?php echo json_encode($arr_tipodoc_js,  JSON_UNESCAPED_UNICODE); ?>;

        // ----------------------------------------------------------------
        // Helpers de formatação / parse de moeda BR
        // ----------------------------------------------------------------
        function ctpFormatMoney(n) {
            if (isNaN(n) || n === '' || n === null) n = 0;
            return parseFloat(n).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function ctpParseMoney(str) {
            if (!str) return 0;
            str = String(str).trim();
            // Remove pontos de milhar e troca vírgula por ponto
            str = str.replace(/\./g, '').replace(',', '.');
            var v = parseFloat(str);
            return isNaN(v) ? 0 : v;
        }

        function ctpGetValorTotal() {
            return ctpParseMoney($('#vlr_primeira_parcela').val());
        }

        // ----------------------------------------------------------------
        // Soma ordinal: "1º", "2º" … com acento correto
        // ----------------------------------------------------------------
        function ordinal(n) {
            return n + 'º';
        }

        // ----------------------------------------------------------------
        // Soma de dias a uma data (YYYY-MM-DD) sem fuso horário
        // ----------------------------------------------------------------
        function addDias(dataStr, dias) {
            var p = dataStr.split('-');
            var d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
            d.setDate(d.getDate() + parseInt(dias));
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            return d.getFullYear() + '-' + mm + '-' + dd;
        }

        // ----------------------------------------------------------------
        // Calcula 1º vencimento padrão: emissão + 30 dias
        // ----------------------------------------------------------------
        function calcPrimeiroVencimento() {
            var emissao = $('#data_emissao').val();
            if (!emissao) return '';
            return addDias(emissao, 30);
        }

        // Chamado ao alterar data de emissão
        function onEmissaoChange() {
            var n = parseInt($('#parcelamento').val());
            if (n > 0) {
                $('#primeiro_vencimento').val(calcPrimeiroVencimento());
                recalcularDatas();
            }
        }

        // Chamado ao sair do campo Valor total
        function onValorTotalBlur() {
            exibe_valor_primeira_parcela(); // formata exibição (função do contas_pagar.js)
            var n = parseInt($('#parcelamento').val());
            if (n > 0) {
                redistribuirIgual(n);
                atualizarTotais(n);
            }
        }

        // ----------------------------------------------------------------
        // Replica valor para parcelas seguintes (com confirmação)
        // ----------------------------------------------------------------
        function replicarSeDesejado(tipo, el, idx) {
            var n = parseInt($('#parcelamento').val());
            if (idx >= n - 1) return; // já é a última parcela, nada a replicar

            var resposta = confirm('Deseja replicar esta seleção para as ' + (n - idx - 1) + ' parcela(s) seguinte(s)?');
            if (!resposta) return;

            for (var i = idx + 1; i < n; i++) {
                if (tipo === 'banco') {
                    $('#parc_banco_' + i).val($(el).val());
                } else if (tipo === 'tipodoc') {
                    $('#parc_tipodoc_' + i).val($(el).val());
                } else if (tipo === 'pago') {
                    $('#parc_pago_' + i).prop('checked', $(el).is(':checked'));
                }
            }
        }

        // ----------------------------------------------------------------
        // Monta o HTML de um <select> de bancos
        // ----------------------------------------------------------------
        function buildSelectBanco(name, id, val, idx) {
            var html = '<select class="form-control" name="' + name + '" id="' + id + '" style="height:30px;font-size:13px;padding:2px 6px;" onchange="replicarSeDesejado(\'banco\', this, ' + idx + ')">';
            html += '<option value="0">...</option>';
            CTP_BANCOS.forEach(function(b) {
                var sel = (val && String(val) === String(b.id)) ? ' selected' : '';
                html += '<option value="' + b.id + '"' + sel + '>' + b.desc + '</option>';
            });
            html += '</select>';
            return html;
        }

        // ----------------------------------------------------------------
        // Monta o HTML de um <select> de tipo documento
        // ----------------------------------------------------------------
        function buildSelectTipoDoc(name, id, val, idx) {
            var html = '<select class="form-control" name="' + name + '" id="' + id + '" style="height:30px;font-size:13px;padding:2px 6px;" onchange="replicarSeDesejado(\'tipodoc\', this, ' + idx + ')">';
            html += '<option value="00">...</option>';
            CTP_TIPODOCS.forEach(function(t) {
                var sel = (val && String(val) === String(t.id)) ? ' selected' : '';
                html += '<option value="' + t.id + '"' + sel + '>' + t.desc + '</option>';
            });
            html += '</select>';
            return html;
        }

        // ----------------------------------------------------------------
        // Gera / Regera a tabela de parcelas
        // ----------------------------------------------------------------
        function gerarTabelaParcelas(n) {
            var total      = ctpGetValorTotal();
            var vlrParc    = (n > 0 && total > 0) ? total / n : 0;
            var percParc   = (n > 0) ? 100 / n : 0;
            var primVenc   = $('#primeiro_vencimento').val();
            var intervalo  = parseInt($('#intervalo').val()) || 30;

            var tbody = $('#tbody_parcelas');
            tbody.empty();

            for (var i = 0; i < n; i++) {
                // Calcula data desta parcela
                var dataParc = '';
                if (primVenc) {
                    dataParc = (i === 0) ? primVenc : addDias(primVenc, intervalo * i);
                }

                // Arredonda — última parcela absorve centavos
                var vlrEsta  = (i < n - 1) ? Math.round(vlrParc * 100) / 100 : Math.round((total - vlrParc * (n - 1)) * 100) / 100;
                var percEsta = (i < n - 1) ? Math.round(percParc * 100) / 100 : Math.round((100 - percParc * (n - 1)) * 100) / 100;

                var tr = '<tr id="parc_row_' + i + '">';
                tr += '<td><span class="lbl-parcela">' + ordinal(i + 1) + ' Vencimento</span></td>';
                tr += '<td><input type="date" class="form-control parc-data" name="parcela[' + i + '][data_vencimento]" id="parc_data_' + i + '" value="' + dataParc + '" style="height:30px;font-size:13px;padding:2px 6px;"></td>';
                tr += '<td><input type="text"  class="form-control parc-valor" name="parcela[' + i + '][valor]" id="parc_valor_' + i + '" value="' + ctpFormatMoney(vlrEsta) + '" onblur="recalcularPorValor(' + i + ')" onkeypress="digita_valor()"></td>';
                tr += '<td><input type="text"  class="form-control parc-perc"  name="parcela[' + i + '][percentual]" id="parc_perc_' + i + '"  value="' + ctpFormatMoney(percEsta) + '" onblur="recalcularPorPercentual(' + i + ')"></td>';
                tr += '<td>' + buildSelectBanco('parcela[' + i + '][banco_conta]', 'parc_banco_' + i, '', i) + '</td>';
                tr += '<td>' + buildSelectTipoDoc('parcela[' + i + '][tipo_doc]', 'parc_tipodoc_' + i, '', i) + '</td>';
                tr += '<td class="pago-parc" style="text-align:center;"><input type="checkbox" name="parcela[' + i + '][pago]" id="parc_pago_' + i + '" value="S" onchange="replicarSeDesejado(\'pago\', this, ' + i + ')"></td>';
                tr += '</tr>';

                tbody.append(tr);
            }

            atualizarTotais(n);
        }

        // ----------------------------------------------------------------
        // Recalcular todas as datas (ao alterar 1º vencimento ou intervalo)
        // ----------------------------------------------------------------
        function recalcularDatas() {
            var n         = parseInt($('#parcelamento').val());
            var primVenc  = $('#primeiro_vencimento').val();
            var intervalo = parseInt($('#intervalo').val()) || 30;

            if (!primVenc || n < 1) return;

            for (var i = 0; i < n; i++) {
                var dataParc = (i === 0) ? primVenc : addDias(primVenc, intervalo * i);
                $('#parc_data_' + i).val(dataParc);
            }
        }

        // ----------------------------------------------------------------
        // Recalcular ao alterar VALOR de uma parcela
        // ----------------------------------------------------------------
        function recalcularPorValor(idx) {
            var n     = parseInt($('#parcelamento').val());
            var total = ctpGetValorTotal();
            if (n < 1 || total === 0) return;

            var novoVlr = ctpParseMoney($('#parc_valor_' + idx).val());
            // Atualiza percentual desta parcela
            var novoPerc = total > 0 ? (novoVlr / total) * 100 : 0;
            $('#parc_perc_' + idx).val(ctpFormatMoney(novoPerc));
            $('#parc_valor_' + idx).val(ctpFormatMoney(novoVlr));

            // Distribui o restante igualmente entre as demais
            var somaFixa = novoVlr;
            var restantes = n - 1;
            if (restantes > 0) {
                var vlrRestante = (total - novoVlr) / restantes;
                for (var i = 0; i < n; i++) {
                    if (i === idx) continue;
                    var vlrI = (i === n - 1 && i !== idx)
                        ? Math.round((total - somaFixa) * 100) / 100
                        : Math.round(vlrRestante * 100) / 100;
                    somaFixa += vlrI;
                    var percI = total > 0 ? (vlrI / total) * 100 : 0;
                    $('#parc_valor_' + i).val(ctpFormatMoney(vlrI));
                    $('#parc_perc_' + i).val(ctpFormatMoney(percI));
                }
            }

            atualizarTotais(n);
        }

        // ----------------------------------------------------------------
        // Recalcular ao alterar PERCENTUAL de uma parcela
        // ----------------------------------------------------------------
        function recalcularPorPercentual(idx) {
            var n     = parseInt($('#parcelamento').val());
            var total = ctpGetValorTotal();
            if (n < 1 || total === 0) return;

            var novoPerc = ctpParseMoney($('#parc_perc_' + idx).val());
            var novoVlr  = (novoPerc / 100) * total;
            $('#parc_valor_' + idx).val(ctpFormatMoney(novoVlr));
            $('#parc_perc_'  + idx).val(ctpFormatMoney(novoPerc));

            // Distribui percentual restante igualmente entre as demais
            var percRestante = (100 - novoPerc) / (n - 1);
            var somaPerc = novoPerc;
            for (var i = 0; i < n; i++) {
                if (i === idx) continue;
                var percI = (i === n - 1 && i !== idx)
                    ? Math.round((100 - somaPerc) * 100) / 100
                    : Math.round(percRestante * 100) / 100;
                somaPerc += percI;
                var vlrI = (percI / 100) * total;
                $('#parc_valor_' + i).val(ctpFormatMoney(vlrI));
                $('#parc_perc_'  + i).val(ctpFormatMoney(percI));
            }

            atualizarTotais(n);
        }

        // ----------------------------------------------------------------
        // Atualiza linha de totais abaixo da tabela
        // ----------------------------------------------------------------
        function atualizarTotais(n) {
            var total    = ctpGetValorTotal();
            var somaVlr  = 0;
            var somaPerc = 0;

            for (var i = 0; i < n; i++) {
                somaVlr  += ctpParseMoney($('#parc_valor_' + i).val());
                somaPerc += ctpParseMoney($('#parc_perc_'  + i).val());
            }

            somaVlr  = Math.round(somaVlr  * 100) / 100;
            somaPerc = Math.round(somaPerc * 100) / 100;

            var okVlr  = Math.abs(somaVlr  - total) <= 0.02;
            var okPerc = Math.abs(somaPerc - 100)   <= 0.02;

            var clVlr  = okVlr  ? 'valor-ok' : 'valor-err';
            var clPerc = okPerc ? 'valor-ok' : 'valor-err';

            $('#parc_totais').html(
                'Total das parcelas: <span class="' + clVlr  + '">R$ ' + ctpFormatMoney(somaVlr)  + '</span> &nbsp;|&nbsp; ' +
                'Total %: <span class="' + clPerc + '">'       + ctpFormatMoney(somaPerc) + '%</span>' +
                (okVlr && okPerc ? '' : ' &nbsp;<span style="color:#c0392b;">⚠ Ajuste os valores antes de confirmar</span>')
            );
        }

        // ----------------------------------------------------------------
        // Alterna entre À Vista e Parcelado
        // ----------------------------------------------------------------
        function onParcelamentoChange() {
            var n = parseInt($('#parcelamento').val());

            if (n === 0) {
                // À Vista
                $('#bloco_avista').show();
                $('#bloco_parc_header').hide();
                $('#bloco_parcelas').hide();
                $('#tbody_parcelas').empty();
                $('#parc_totais').empty();
            } else {
                // Parcelado
                $('#bloco_avista').hide();
                $('#bloco_parc_header').show();
                $('#bloco_parcelas').show();

                // Preenche 1º vencimento se ainda vazio
                if (!$('#primeiro_vencimento').val()) {
                    $('#primeiro_vencimento').val(calcPrimeiroVencimento());
                }

                gerarTabelaParcelas(n);
            }
        }

        // ----------------------------------------------------------------
        // Validação na hora de confirmar (sobrepõe a do contas_pagar.js)
        // ----------------------------------------------------------------
        function validarParcelamento() {
            var n = parseInt($('#parcelamento').val());
            if (n === 0) return true; // À Vista — sem validação extra

            var total    = ctpGetValorTotal();
            var somaVlr  = 0;
            var somaPerc = 0;

            for (var i = 0; i < n; i++) {
                var banco = $('#parc_banco_' + i).val();
                if (!banco || banco === '0') {
                    $('#mensagem_erro').modal();
                    $('#mensagem_erro .modal-body').html('Informe o Banco/Conta Pagamento da parcela ' + (i + 1) + '.');
                    return false;
                }
                somaVlr  += ctpParseMoney($('#parc_valor_' + i).val());
                somaPerc += ctpParseMoney($('#parc_perc_'  + i).val());
            }

            somaVlr  = Math.round(somaVlr  * 100) / 100;
            somaPerc = Math.round(somaPerc * 100) / 100;

            if (Math.abs(somaVlr - total) > 0.02) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html(
                    'A soma das parcelas (R$ ' + ctpFormatMoney(somaVlr) + ') é diferente do valor total (R$ ' + ctpFormatMoney(total) + ').'
                );
                return false;
            }

            if (Math.abs(somaPerc - 100) > 0.02) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html(
                    'A soma dos percentuais (' + ctpFormatMoney(somaPerc) + '%) deve ser igual a 100%.'
                );
                return false;
            }

            return true;
        }

        // Ponto de entrada do botão Confirmar — valida parcelamento antes de gravar
        function confirmar_incluir() {
            if (!validarParcelamento()) return;
            confirmar_fazendas(); // função de contas_pagar.js (já disponível no clique)
        }

        // ----------------------------------------------------------------
        // Anexos
        // ----------------------------------------------------------------
        function adicionarAnexo() {
            var div = document.createElement('div');
            div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';
            div.innerHTML =
                '<input type="file" name="anexo[]" class="form-control" style="max-width:320px;">' +
                '<button type="button" class="btn-anexo-add" onclick="removerAnexo(this)" title="Remover">' +
                '<i class="far fa-times-circle" style="font-size:16px; color:#c0392b;"></i></button>';
            document.getElementById('lista_anexos').appendChild(div);
        }

        function removerAnexo(btn) {
            btn.parentElement.remove();
        }
        </script>

    </section><!-- container section start end -->

    <?php
    $javascript_file_name = 'contas_pagar.js';
    require 'rodape.php';
    ?>
</body>
</html>
