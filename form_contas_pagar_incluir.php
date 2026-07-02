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

// Arrays para o modal de rateio
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
$rs_cta_rat = mysqli_query($conector, "select tbl_plano_contas_codigo_id, tbl_plano_contas_descricao, tbl_plano_contas_nivel from tbl_plano_contas where tbl_plano_contas_debito_credito='D' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_codigo_id");
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

        /* ── Modal Rateio ── */
        /* CSS rateio reservado para nova tela */

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

                                    <!-- ===== BOTÕES (topo) ===== -->
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-primary confirmar_gravar" onclick="confirmar_incluir()">Confirmar</button>
                                            <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                        </div>
                                    </div>

                                    <!-- ===== LINHA 1: Fornecedor | Emissão | Descrição | Valor | Nº Documento ===== -->
                                    <div class="row">

                                        <div class="form-group col-md-3">
                                            <label for="codigo_cli_for" class="control-label"><span class="required">*</span> Fornecedor
                                                <a href="form_cliente_fornecedor_incluir.php?voltar=3" style="margin-left: 6px;" data-toggle='tooltip' data-placement='top' title='Cadastrar novo fornecedor'>
                                                    <i class="far fa-plus-square" style="font-size: 16px; color: #337ab7;"></i>
                                                </a>
                                            </label>
                                            <select class="form-control selectpicker" id="codigo_cli_for" name="codigo_cli_for" data-live-search="true" data-size="8">
                                                <option value="999999999" selected="selected">...</option>
                                                <?php while ($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>
                                                    <option value="<?php echo $registo_cli_for->tbl_pessoa_id; ?>"
                                                        <?php if ($registo_cli_for->tbl_pessoa_id == $ultimo_fornecedor_cadastrado) echo 'selected'; ?>>
                                                        <?php echo $registo_cli_for->tbl_pessoa_nome; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" id="nome_for" name="nome_for" value="">
                                            <small id="nome_for_badge" style="display:none;color:#27ae60;font-weight:600;"></small>
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
                                            <input name="number_doc" type="number" class="form-control" id="number_doc" maxlength="15" onblur="verificarDocDuplicado()">
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
                                                <a href="#" id="link_editar_rateio" onclick="editarRateio(); return false;"
                                                   style="font-size:12px; color:#337ab7; margin-left:8px; display:inline-block;">
                                                    <i class="fas fa-pen"></i> Editar
                                                </a>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3" id="col_local">
                                            <label for="codigo_fazenda" class="control-label"><span class="required">*</span> Local</label>
                                            <select class="form-control" id="codigo_fazenda" name="codigo_fazenda[]">
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
                                            <button type="button" id="btn_confirmar_locais" class="btn btn-primary" style="white-space:nowrap;" onclick="confirmarLocaisRateio()">
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

                                    <!-- ===== LINHA 3: Repetir Lançamento ===== -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="area-toggles">
                                                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                                    <label class="toggle-label" style="margin-right: 4px;">Repetir Lançamento?</label>
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" id="repetir_lancamento" name="repetir_lancamento" onchange="onRepetirLancamentoChange()">
                                                        <span class="toggle-track"></span>
                                                    </label>
                                                    <!-- Resumo e link editar (aparecem após confirmar modal) -->
                                                    <span id="rep_resumo_wrap" style="display:none; align-items:center; gap:8px;">
                                                        <span style="font-size:13px; color:#555; font-weight:500;">Repetição *</span>
                                                        <span id="rep_resumo_texto" style="font-size:13px; background:#eaf2ff; color:#2471a3; border:1px solid #aed6f1; border-radius:4px; padding:3px 10px; font-weight:600;"></span>
                                                        <a href="#" onclick="abrirModalRepeticao(); return false;" style="font-size:12px; color:#337ab7;">
                                                            <i class="fas fa-pen"></i> Editar
                                                        </a>
                                                    </span>
                                                    <!-- Campos hidden que serão enviados ao backend -->
                                                    <input type="hidden" id="rep_cada_hidden"   name="rep_cada">
                                                    <input type="hidden" id="rep_freq_hidden"   name="rep_frequencia">
                                                    <input type="hidden" id="rep_ocorr_hidden"  name="rep_ocorrencias" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FIM LINHA 3 -->

                                    <!-- ===== SEÇÃO: Condição de Pagamento ===== -->
                                    <div id="secao_condicao_normal">
                                    <div class="secao-titulo">Condição de Pagamento</div>

                                    <div class="row">

                                        <!-- Parcelamento — sempre visível -->
                                        <!-- sel_modo_parc: controla A Vista / Parcelado (sem name, não vai ao POST) -->
                                        <!-- #parcelamento (hidden): valor numérico real enviado ao backend e lido pelo JS -->
                                        <input type="hidden" id="parcelamento" name="parcelamento" value="0">
                                        <div class="form-group col-md-3">
                                            <label class="control-label"><span class="required">*</span> Parcelamento</label>
                                            <select class="form-control" id="sel_modo_parc" onchange="onParcelamentoChange()">
                                                <option value="avista">A Vista</option>
                                                <option value="uma_parcela">1 Parcela</option>
                                                <option value="parc">Parcelado em 2x ou mais</option>
                                            </select>
                                        </div>
                                        <!-- Nº de Parcelas — só aparece quando Parcelado em 2x ou mais -->
                                        <div class="form-group col-md-2" id="bloco_qtd_parcelas" style="display:none;">
                                            <label for="qtd_parcelas_input" class="control-label">Nº de Parcelas</label>
                                            <input type="number" class="form-control" id="qtd_parcelas_input" min="1" max="360" placeholder="Nº" style="text-align:center;" oninput="onQtdParcelasChange(this.value)">
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
                                                    <input class="form-check-input" type="checkbox" value="" id="pago" name="pago" onchange="onPagoAvistaChange()">
                                                    <label for="pago">Pago</label>
                                                </div>
                                            </div>
                                            <!-- Dados do pagamento (aparece ao marcar Pago) -->
                                            <div id="bloco_pago_avista" style="display:none; margin-top:4px; padding:4px 0;">
                                                <div class="row" style="margin:0;">
                                                    <div class="form-group col-md-3">
                                                        <label class="control-label">Data Pagamento</label>
                                                        <input type="date" class="form-control" id="pago_data_pagamento" name="pago_data_pagamento">
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label class="control-label">Desconto</label>
                                                        <input type="text" class="form-control" id="pago_desconto" name="pago_desconto" placeholder="0,00"
                                                               onkeypress="mask.money.call(this, event)"
                                                               onblur="calcularValorPagoAvista()">
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label class="control-label">Juros</label>
                                                        <input type="text" class="form-control" id="pago_juros" name="pago_juros" placeholder="0,00"
                                                               onkeypress="mask.money.call(this, event)"
                                                               onblur="calcularValorPagoAvista()">
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label class="control-label">Valor Pago</label>
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
                                                           value="30" min="1" onchange="onIntervaloChange()">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="primeiro_vencimento" class="control-label"><span class="required">*</span> 1º Vencimento</label>
                                                    <input type="date" class="form-control" id="primeiro_vencimento" name="primeiro_vencimento"
                                                           onchange="recalcularDatas()">
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
                                    <!-- FIM Condição de Pagamento -->
                                    </div><!-- /secao_condicao_normal -->

                                    <!-- ===== SEÇÃO: Condição de Pagamento — RECORRENTE ===== -->
                                    <div id="secao_condicao_recorrente" style="display:none;">
                                        <div class="secao-titulo">Condição de Pagamento — Recorrente</div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label for="rep_primeiro_venc" class="control-label"><span class="required">*</span> 1º Vencimento</label>
                                                <input type="date" class="form-control" id="rep_primeiro_venc" name="rep_primeiro_venc" onchange="gerarPreviewRecorrencias()">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="rep_cobrar_no" class="control-label"><span class="required">*</span> Cobrar Sempre No</label>
                                                <select class="form-control selectpicker" id="rep_cobrar_no" name="rep_cobrar_no" onchange="gerarPreviewRecorrencias()">
                                                    <option value="dia_vencimento" selected>Mesmo dia do 1º Vencimento</option>
                                                    <option value="dia_emissao">Mesmo dia da Emissão</option>
                                                    <option value="01">Todo dia 1</option>
                                                    <option value="05">Todo dia 5</option>
                                                    <option value="10">Todo dia 10</option>
                                                    <option value="15">Todo dia 15</option>
                                                    <option value="20">Todo dia 20</option>
                                                    <option value="25">Todo dia 25</option>
                                                    <option value="ultimo">Último dia do mês</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="rep_banco" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                                <select class="form-control selectpicker" id="rep_banco" name="rep_banco" data-live-search="true" data-size="8">
                                                    <option value="0">...</option>
                                                    <?php
                                                    // Re-query banco para o bloco recorrente
                                                    $conta_rep = mysqli_query($conector, "select tbl_conta_pagamento_id, tbl_conta_pagamento_descricao, tbl_conta_pagamento_agencia, tbl_conta_pagamento_conta from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");
                                                    while ($ln = mysqli_fetch_object($conta_rep)) {
                                                        $dc = $ln->tbl_conta_pagamento_descricao . ' (Age: ' . $ln->tbl_conta_pagamento_agencia . ' Cta: ' . $ln->tbl_conta_pagamento_conta . ')';
                                                        echo '<option value="' . $ln->tbl_conta_pagamento_id . '">' . $dc . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="rep_tipodoc" class="control-label">Tipo Documento</label>
                                                <select class="form-control selectpicker" id="rep_tipodoc" name="rep_tipodoc" data-live-search="true" data-size="8">
                                                    <option value="00">...</option>
                                                    <?php
                                                    $tdoc_rep = mysqli_query($conector, "select tbl_tipo_doc_id, tbl_tipo_doc_descricao from tbl_tipo_documento where tbl_tipo_doc_lixeira=0");
                                                    while ($r = mysqli_fetch_object($tdoc_rep)) {
                                                        echo '<option value="' . $r->tbl_tipo_doc_id . '">' . $r->tbl_tipo_doc_descricao . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Tabela preview recorrências -->
                                        <div style="margin-top:10px; overflow-x:auto;">
                                            <div style="font-size:13px; font-weight:600; color:#555; margin-bottom:6px; padding-bottom:4px; border-bottom:1px solid #e0e0e0;">
                                                Recorrências Previstas
                                            </div>
                                            <table class="tbl-parcelas">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Data Emissão</th>
                                                        <th>Vencimento</th>
                                                        <th>Descrição</th>
                                                        <th style="text-align:right;">Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody_recorrencias"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- FIM Condição de Pagamento Recorrente -->

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
                                                        <input type="file" id="anexo_picker" class="form-control" style="max-width: 320px;" onchange="onAnexoPickerChange(this)">
                                                    </div>
                                                    <div id="lista_anexos"></div>
                                                </div>
                                                <div style="margin-left: 16px;">
                                                    <label class="control-label"><i class="fas fa-link" style="color:#337ab7;"></i> Anexar Link</label>
                                                    <div style="display: flex; align-items: center; gap: 6px;">
                                                        <input type="text" id="link_desc_input" class="form-control" placeholder="Descrição do link" style="max-width: 200px;">
                                                        <input type="url" id="link_url_input" class="form-control" placeholder="https://..." style="max-width: 220px;" onkeydown="onLinkUrlKeydown(event)" onblur="onLinkUrlBlur()" data-toggle="tooltip" data-placement="top" title="Após digitar o https://, tecle ENTER para confirmar o Link">
                                                    </div>
                                                    <div id="lista_links"></div>
                                                </div>
                                            </div>
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

                <!-- Modal: Repetir Lançamento -->
                <div class="modal fade" id="modal_repetir_lancamento" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:480px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"><i class="fas fa-redo" style="color:#337ab7;"></i> Repetir Lançamento</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label"><span class="required">*</span> Repetir a Cada</label>
                                        <input type="number" class="form-control" id="rep_cada" min="1" value="1" style="text-align:center;">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label"><span class="required">*</span> Frequência</label>
                                        <select class="form-control" id="rep_freq">
                                            <option value="1">Dia</option>
                                            <option value="2">Semana</option>
                                            <option value="3">Quinzena</option>
                                            <option value="4" selected>Mês</option>
                                            <option value="5">Bimestre</option>
                                            <option value="6">Trimestre</option>
                                            <option value="7">Semestre</option>
                                            <option value="8">Ano</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label"><span class="required">*</span> Nº de Ocorrências</label>
                                        <input type="number" class="form-control" id="rep_ocorr" min="2" value="3" style="text-align:center;">
                                    </div>
                                </div>
                                <div id="rep_modal_erro" style="display:none; color:#c0392b; font-size:13px; margin-top:6px;"></div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="button" onclick="confirmarRepeticao()">Confirmar</button>
                                <button class="btn btn-default" type="button" onclick="cancelarRepeticao()">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <button type="button" class="btn btn-primary" onclick="confirmarFecharRateio()">Confirmar</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
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

                <!-- Modal: Rateio — REMOVIDO (nova tela a implementar) -->
                <div class="modal fade" id="modal_rateio" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" style="display:none!important">
                    <div class="modal-dialog" style="width:92%;max-width:860px;" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title"><i class="fas fa-sliders-h"></i> Configurar Rateio</h4>
                            </div>
                            <div class="modal-body" style="padding-bottom:4px;">
                                <!-- Passos -->
                                <div class="rt-steps">
                                    <div class="rt-step ativo" data-painel="1">1. Locais</div>
                                    <div class="rt-step" data-painel="2">2. Centros de Custo</div>
                                    <div class="rt-step" data-painel="3">3. Contas Contábeis</div>
                                    <div class="rt-step" data-painel="4">4. Resumo</div>
                                </div>

                                <!-- Painel 1: Locais -->
                                <div id="rt_painel_1" class="rt-painel">
                                    <div class="row" style="margin-bottom:8px;">
                                        <div class="col-md-8">
                                            <label class="control-label">Selecione os Locais</label>
                                            <select id="rt_sel_locais" class="selectpicker form-control" multiple data-live-search="true" data-width="100%" title="Selecione os locais...">
                                                <?php foreach ($arr_local_rat_js as $loc): ?>
                                                <option value="<?php echo $loc['id']; ?>" data-nome="<?php echo htmlspecialchars($loc['nome']); ?>"><?php echo htmlspecialchars($loc['nome']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4" style="padding-top:24px;">
                                            <button type="button" class="btn btn-info btn-sm btn-block" onclick="rtAdicionarLocais()">
                                                <i class="fas fa-plus"></i> Adicionar Locais
                                            </button>
                                        </div>
                                    </div>
                                    <div style="overflow-x:auto;">
                                        <table class="tbl-rateio" id="rt_tab_locais">
                                            <thead>
                                                <tr>
                                                    <th>Local</th>
                                                    <th style="width:120px;">% Rateio</th>
                                                    <th style="width:130px;">Valor (R$)</th>
                                                    <th style="width:40px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <td><strong>Total</strong></td>
                                                    <td><strong id="rt_tot_perc_loc">0,00%</strong></td>
                                                    <td><strong id="rt_tot_val_loc">R$ 0,00</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Painel 2: Centros de Custo por Local -->
                                <div id="rt_painel_2" class="rt-painel" style="display:none;">
                                    <div id="rt_tabs_cc" class="rt-tabs-nav"></div>
                                    <div id="rt_corpo_cc">
                                        <div class="row" style="margin-bottom:8px;">
                                            <div class="col-md-8">
                                                <label class="control-label">Selecione os Centros de Custo</label>
                                                <select id="rt_sel_cc" class="selectpicker form-control" multiple data-live-search="true" data-width="100%" title="Selecione CCs...">
                                                    <?php foreach ($arr_cc_rat_js as $cc): ?>
                                                    <option value="<?php echo htmlspecialchars($cc['id']); ?>" data-nome="<?php echo htmlspecialchars($cc['nome']); ?>"><?php echo htmlspecialchars($cc['nome']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4" style="padding-top:24px;">
                                                <button type="button" class="btn btn-info btn-sm btn-block" onclick="rtAdicionarCCs()">
                                                    <i class="fas fa-plus"></i> Adicionar CCs
                                                </button>
                                            </div>
                                        </div>
                                        <div style="overflow-x:auto;">
                                            <table class="tbl-rateio" id="rt_tab_cc">
                                                <thead>
                                                    <tr>
                                                        <th>Centro de Custo</th>
                                                        <th style="width:120px;">% Rateio</th>
                                                        <th style="width:130px;">Valor (R$)</th>
                                                        <th style="width:40px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td><strong>Total</strong></td>
                                                        <td><strong id="rt_tot_perc_cc">0,00%</strong></td>
                                                        <td><strong id="rt_tot_val_cc">R$ 0,00</strong></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Painel 3: Contas Contábeis por Local×CC -->
                                <div id="rt_painel_3" class="rt-painel" style="display:none;">
                                    <div id="rt_tabs_conta" class="rt-tabs-nav"></div>
                                    <div id="rt_corpo_conta">
                                        <div class="row" style="margin-bottom:8px;">
                                            <div class="col-md-8">
                                                <label class="control-label">Selecione as Contas Contábeis</label>
                                                <select id="rt_sel_conta" class="selectpicker form-control" multiple data-live-search="true" data-width="100%" title="Selecione contas...">
                                                    <?php foreach ($arr_conta_rat_js as $ct): ?>
                                                    <option value="<?php echo htmlspecialchars($ct['id']); ?>" data-nome="<?php echo htmlspecialchars($ct['nome']); ?>"><?php echo htmlspecialchars($ct['nome']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4" style="padding-top:24px;">
                                                <button type="button" class="btn btn-info btn-sm btn-block" onclick="rtAdicionarContas()">
                                                    <i class="fas fa-plus"></i> Adicionar Contas
                                                </button>
                                            </div>
                                        </div>
                                        <div style="overflow-x:auto;">
                                            <table class="tbl-rateio" id="rt_tab_conta">
                                                <thead>
                                                    <tr>
                                                        <th>Conta Contábil</th>
                                                        <th style="width:120px;">% Rateio</th>
                                                        <th style="width:130px;">Valor (R$)</th>
                                                        <th style="width:40px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td><strong>Total</strong></td>
                                                        <td><strong id="rt_tot_perc_conta">0,00%</strong></td>
                                                        <td><strong id="rt_tot_val_conta">R$ 0,00</strong></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Painel 4: Resumo -->
                                <div id="rt_painel_4" class="rt-painel" style="display:none;">
                                    <div style="overflow-x:auto;">
                                        <table class="tbl-rateio" id="rt_tab_resumo">
                                            <thead>
                                                <tr>
                                                    <th>Local</th>
                                                    <th>% Local</th>
                                                    <th>Vlr. Local</th>
                                                    <th>Centro de Custo</th>
                                                    <th>% CC</th>
                                                    <th>Vlr. CC</th>
                                                    <th>Conta Contábil</th>
                                                    <th>% Conta</th>
                                                    <th>Vlr. Conta</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- modal-body -->
                            <div class="modal-footer">
                                <div style="display:flex;justify-content:space-between;width:100%;align-items:center;">
                                    <div>
                                        <button type="button" id="rt_btn_voltar" class="btn btn-default" onclick="rtVoltar()" style="display:none;">
                                            Voltar
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                        <button type="button" id="rt_btn_avancar" class="btn btn-primary" onclick="rtAvancar()">
                                            Avançar
                                        </button>
                                        <button type="button" id="rt_btn_confirmar" class="btn btn-primary" onclick="rtConfirmar()" style="display:none;">
                                            <i class="fas fa-check"></i> Confirmar Rateio
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /modal_rateio -->

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
        var CTP_LOCAIS    = <?php echo json_encode($arr_local_rat_js, JSON_UNESCAPED_UNICODE); ?>;
        var CTP_CCS       = <?php echo json_encode($arr_cc_rat_js,    JSON_UNESCAPED_UNICODE); ?>;
        var CTP_CONTAS_RAT= <?php echo json_encode($arr_conta_rat_js, JSON_UNESCAPED_UNICODE); ?>;

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
            var modo = $('#sel_modo_parc').val();
            if (modo === 'avista') {
                $('#data_vencimento').val($('#data_emissao').val());
            } else if (modo === 'uma_parcela') {
                var emissao = $('#data_emissao').val();
                if (emissao) $('#data_vencimento').val(addDias(emissao, 30));
            } else {
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
                tr += '<td><input type="text"  class="form-control parc-valor" name="parcela[' + i + '][valor]" id="parc_valor_' + i + '" value="' + ctpFormatMoney(vlrEsta) + '" onblur="recalcularPorValor(' + i + ')" onkeypress="mask.money.call(this, event)"></td>';
                tr += '<td><input type="text"  class="form-control parc-perc"  name="parcela[' + i + '][percentual]" id="parc_perc_' + i + '"  value="' + ctpFormatMoney(percEsta) + '" readonly style="background:#f5f5f5;color:#777;"></td>';
                tr += '<td>' + buildSelectBanco('parcela[' + i + '][banco_conta]', 'parc_banco_' + i, '', i) + '</td>';
                tr += '<td>' + buildSelectTipoDoc('parcela[' + i + '][tipo_doc]', 'parc_tipodoc_' + i, '', i) + '</td>';
                tr += '<td class="pago-parc" style="text-align:center;"><input type="checkbox" name="parcela[' + i + '][pago]" id="parc_pago_' + i + '" value="S" onchange="togglePagoParc(' + i + ')"></td>';
                tr += '</tr>';
                tr += '<tr id="parc_pago_row_' + i + '" style="display:none; background:#fffde7;">';
                tr += '<td style="padding:0; background:#fffde7;"></td>';
                tr += '<td style="padding:0; background:#fffde7;"></td>';
                tr += '<td colspan="2" style="padding:4px 8px;"><small style="color:#888;">Data Pagamento</small><br>';
                tr += '<input type="date" class="form-control" name="parcela[' + i + '][data_pagamento]" id="parc_dt_pag_' + i + '" style="height:28px;font-size:12px;padding:2px 6px;"></td>';
                tr += '<td style="padding:4px 8px;"><div style="display:flex;gap:6px;">';
                tr += '<div style="flex:1;"><small style="color:#888;">Desconto</small><br>';
                tr += '<input type="text" class="form-control" name="parcela[' + i + '][desconto]" id="parc_desconto_' + i + '" placeholder="0,00" style="height:28px;font-size:12px;padding:2px 6px;" onkeypress="mask.money.call(this, event)" onblur="recalcularValorPago(' + i + ')"></div>';
                tr += '<div style="flex:1;"><small style="color:#888;">Juros</small><br>';
                tr += '<input type="text" class="form-control" name="parcela[' + i + '][juros]" id="parc_juros_' + i + '" placeholder="0,00" style="height:28px;font-size:12px;padding:2px 6px;" onkeypress="mask.money.call(this, event)" onblur="recalcularValorPago(' + i + ')"></div>';
                tr += '</div></td>';
                tr += '<td style="padding:4px 8px;"><small style="color:#888;">Valor Pago</small><br>';
                tr += '<input type="text" class="form-control" name="parcela[' + i + '][valor_pago]" id="parc_vlr_pago_' + i + '" placeholder="0,00" style="height:28px;font-size:12px;padding:2px 6px;background:#f0f8e8;font-weight:600;" readonly></td>';
                tr += '<td style="padding:0; background:#fffde7;"></td>';
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
        // Ao alterar o intervalo: atualiza 1º Vencimento = emissão + intervalo
        // ----------------------------------------------------------------
        function onIntervaloChange() {
            var emissao   = $('#data_emissao').val();
            var intervalo = parseInt($('#intervalo').val()) || 30;
            if (emissao) {
                $('#primeiro_vencimento').val(addDias(emissao, intervalo));
            }
            recalcularDatas();
        }

        // ----------------------------------------------------------------
        // Recalcular ao alterar VALOR de uma parcela
        // ----------------------------------------------------------------
        function recalcularPorValor(idx) {
            var n     = parseInt($('#parcelamento').val());
            var total = ctpGetValorTotal();
            if (n < 1 || total === 0) return;

            var novoVlr = parseMoneyVal($('#parc_valor_' + idx).val());
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
        // Alterna entre À Vista, 1 Parcela e Parcelado em 2x ou mais
        // ----------------------------------------------------------------
        function onParcelamentoChange() {
            var modo = $('#sel_modo_parc').val();

            // Sempre reseta o bloco de pagamento ao trocar de modo
            $('#pago').prop('checked', false);
            $('#pago_data_pagamento').val('');
            $('#pago_desconto').val('');
            $('#pago_juros').val('');
            $('#pago_valor_pago').val('');
            $('#bloco_pago_avista').hide();

            if (modo === 'avista') {
                $('#parcelamento').val(0);
                $('#bloco_qtd_parcelas').hide();
                $('#qtd_parcelas_input').val('');
                $('#bloco_avista').show();
                $('#bloco_parc_header').hide();
                $('#bloco_parcelas').hide();
                $('#tbody_parcelas').empty();
                $('#parc_totais').empty();
                var emissao = $('#data_emissao').val();
                if (emissao) $('#data_vencimento').val(emissao);
            } else if (modo === 'uma_parcela') {
                $('#parcelamento').val(0);
                $('#bloco_qtd_parcelas').hide();
                $('#qtd_parcelas_input').val('');
                $('#bloco_avista').show();
                $('#bloco_parc_header').hide();
                $('#bloco_parcelas').hide();
                $('#tbody_parcelas').empty();
                $('#parc_totais').empty();
                var emissao = $('#data_emissao').val();
                if (emissao) $('#data_vencimento').val(addDias(emissao, 30));
            } else {
                // Parcelado em 2x ou mais
                $('#parcelamento').val(0);
                $('#bloco_qtd_parcelas').show();
                $('#qtd_parcelas_input').attr('min', 2).val('');
                $('#bloco_avista').hide();
                $('#bloco_parc_header').hide();
                $('#bloco_parcelas').hide();
                $('#tbody_parcelas').empty();
                $('#parc_totais').empty();
                setTimeout(function(){ $('#qtd_parcelas_input').focus(); }, 50);
            }
        }

        function onQtdParcelasChange(val) {
            var n = parseInt(val);
            if (!n || n < 1) {
                $('#parcelamento').val(0);
                $('#bloco_parc_header').hide();
                $('#bloco_parcelas').hide();
                $('#tbody_parcelas').empty();
                return;
            }
            if (n < 2) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('Para "Parcelado em 2x ou mais", informe no mínimo 2 parcelas.');
                $('#qtd_parcelas_input').val('');
                $('#parcelamento').val(0);
                $('#bloco_parc_header').hide();
                $('#bloco_parcelas').hide();
                $('#tbody_parcelas').empty();
                return;
            }
            // Atualiza o hidden que as funções JS e o backend leem
            $('#parcelamento').val(n);

            $('#bloco_parc_header').show();
            $('#bloco_parcelas').show();
            if (!$('#primeiro_vencimento').val()) {
                $('#primeiro_vencimento').val(calcPrimeiroVencimento());
            }
            gerarTabelaParcelas(n);
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

        // ----------------------------------------------------------------
        // Validação dos campos obrigatórios, na ordem exigida pelo negócio:
        // Fornecedor, Emissão, Descrição, Valor, (sem rateio) Local, Centro
        // de Custos, Código Contábil, Vencimento, Banco/Conta Pagamento.
        // Número do Documento NÃO é obrigatório.
        // ----------------------------------------------------------------
        function validarCamposObrigatoriosPagar() {
            function erro(msg, $campo) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html(msg);
                if ($campo && $campo.length) {
                    $('#mensagem_erro').one('hidden.bs.modal', function () {
                        $campo.focus();
                    });
                }
                return false;
            }

            var codigoFor = $('#codigo_cli_for').val();
            var nomeForManual = ($('#nome_for').val() || '').trim();
            if ((!codigoFor || codigoFor === '999999999') && !nomeForManual) {
                return erro('Informe o Fornecedor ou digite o nome do fornecedor não cadastrado.', $('#codigo_cli_for'));
            }
            if (!$('#data_emissao').val()) {
                return erro('Informe a Data de Emissão.', $('#data_emissao'));
            }
            if (!$('#descricao_compra').val().trim()) {
                return erro('Informe a Descrição da Compra.', $('#descricao_compra'));
            }
            if (ctpGetValorTotal() <= 0) {
                return erro('Informe o Valor.', $('#vlr_primeira_parcela'));
            }

            if (!$('#habilitar_rateio').is(':checked')) {
                var local = $('#codigo_fazenda').val();
                if (!local || (Array.isArray(local) && local.length === 0)) {
                    return erro('Informe o Local.', $('#codigo_fazenda'));
                }
                if (!$('#codigo_cc').val()) {
                    return erro('Informe o Centro de Custos.', $('#codigo_cc'));
                }
                var conta = $('#codigo_conta').val();
                if (!conta || conta === '0000000') {
                    return erro('Informe o Código Contábil.', $('#codigo_conta'));
                }
            }

            if ($('#repetir_lancamento').is(':checked')) {
                if (!$('#rep_primeiro_venc').val()) {
                    return erro('Informe o Vencimento.', $('#rep_primeiro_venc'));
                }
                var repBanco = $('#rep_banco').val();
                if (!repBanco || repBanco === '0') {
                    return erro('Informe o Banco/Conta Pagamento.', $('#rep_banco'));
                }
            } else {
                var n = parseInt($('#parcelamento').val());
                if (n === 0) {
                    if (!$('#data_vencimento').val()) {
                        return erro('Informe o Vencimento.', $('#data_vencimento'));
                    }
                    var banco = $('#codigo_forma_rec').val();
                    if (!banco || banco === '0') {
                        return erro('Informe o Banco/Conta Pagamento.', $('#codigo_forma_rec'));
                    }
                } else if (n >= 1) {
                    if (!$('#primeiro_vencimento').val()) {
                        return erro('Informe o Vencimento.', $('#primeiro_vencimento'));
                    }
                    // Banco/Conta Pagamento de cada parcela é validado em validarParcelamento()
                }
            }

            return true;
        }

        // Ponto de entrada do botão Confirmar — valida campos obrigatórios e parcelamento antes de gravar
        function confirmar_incluir() {
            if (!validarCamposObrigatoriosPagar()) return;
            if (!validarParcelamento()) return;
            // Se rateio novo estiver ativo, vai direto gravar (sem modal de fazendas)
            // Se rateio desligado com 1 só fazenda, também vai direto
            var rateioAtivo = $('#habilitar_rateio').is(':checked');
            var fazendas = $('#codigo_fazenda').val();
            var qtdFazendas = Array.isArray(fazendas) ? fazendas.length : (fazendas ? 1 : 0);

            if (rateioAtivo || qtdFazendas <= 1) {
                gravar_conta();
            } else {
                confirmar_fazendas(); // múltiplas fazendas sem rateio novo — fluxo legado
            }
        }

        // ================================================================
        // HABILITAR RATEIO — alterna Local entre select simples e selectpicker múltiplo
        // ================================================================
        // Handler do toggle Rateio — registrado após jQuery carregar (ver bloco pós-rodape.php)

        // ================================================================
        // RATEIO — Nova tela a implementar
        // ================================================================
        var RT = { locais: [], reset: function(){ this.locais = []; } };

        function rtAbrirModal() { /* Nova tela de rateio a implementar */ }

        /* === Funções rt* removidas — nova tela a implementar === */
        function rtIrPainel(n) { return; } function rtVoltar(){} function rtAvancar(){}
        function rtAdicionarLocais(){} function rtAtualizarTabLocais(){} function rtAtualizarTotaisLocais(){}
        function rtRemoverLocal(){} function rtRenderizarTabsCC(){} function rtSelecionarTabCC(){}
        function rtCarregarTabCC(){} function rtAdicionarCCs(){} function rtRemoverCC(){}
        function rtAtualizarTotaisCC(){} function rtRenderizarTabsConta(){} function rtSelecionarTabConta(){}
        function rtCarregarTabConta(){} function rtAdicionarContas(){} function rtRemoverConta(){}
        function rtAtualizarTotaisConta(){} function rtGerarResumo(){} function rtConfirmar(){}
        /* === fim placeholder === */
        // ================================================================
        // REPETIR LANÇAMENTO
        // ================================================================

        var REP_FREQ_LABELS = {
            '1': 'dia(s)',      '2': 'semana(s)',    '3': 'quinzena(s)',
            '4': 'mês(es)',     '5': 'bimestre(s)',  '6': 'trimestre(s)',
            '7': 'semestre(s)', '8': 'ano(s)'
        };

        // Abre modal ao ligar o toggle
        function onRepetirLancamentoChange() {
            if ($('#repetir_lancamento').is(':checked')) {
                // Só abre modal se ainda não foi confirmado
                if (!$('#rep_ocorr_hidden').val() || $('#rep_ocorr_hidden').val() == '0') {
                    abrirModalRepeticao();
                } else {
                    mostrarBlocoRecorrente();
                }
            } else {
                cancelarRepeticao();
            }
        }

        function abrirModalRepeticao() {
            $('#rep_modal_erro').hide();
            $('#modal_repetir_lancamento').modal('show');
        }

        function cancelarRepeticao() {
            $('#modal_repetir_lancamento').modal('hide');
            // Desliga toggle e limpa
            $('#repetir_lancamento').prop('checked', false);
            $('#rep_ocorr_hidden').val('0');
            $('#rep_resumo_wrap').hide();
            // Volta a exibir condição normal
            $('#secao_condicao_normal').show();
            $('#secao_condicao_recorrente').hide();
            $('#tbody_recorrencias').empty();
        }

        function confirmarRepeticao() {
            var cada  = parseInt($('#rep_cada').val());
            var freq  = $('#rep_freq').val();
            var ocorr = parseInt($('#rep_ocorr').val());

            if (!cada || cada < 1) {
                $('#rep_modal_erro').text('Informe "Repetir a Cada" (mínimo 1).').show();
                return;
            }
            if (!ocorr || ocorr < 2) {
                $('#rep_modal_erro').text('Informe as Ocorrências (mínimo 2).').show();
                return;
            }

            // Salva nos hiddens
            $('#rep_cada_hidden').val(cada);
            $('#rep_freq_hidden').val(freq);
            $('#rep_ocorr_hidden').val(ocorr);

            // Monta texto resumo
            var label = REP_FREQ_LABELS[freq] || '';
            var vezStr = parseInt(ocorr) === 1 ? 'vez' : 'vezes';
            $('#rep_resumo_texto').text('A cada ' + cada + ' ' + label + ' por ' + ocorr + ' ' + vezStr);

            // Pré-preenche 1º Vencimento = emissão + 1 intervalo (sempre recalcula ao confirmar)
            var emissao = $('#data_emissao').val();
            if (emissao) {
                var primVenc = repAvancarData(emissao, freq, cada, 1);
                $('#rep_primeiro_venc').val(primVenc);
            }

            $('#modal_repetir_lancamento').modal('hide');
            mostrarBlocoRecorrente();
        }

        function mostrarBlocoRecorrente() {
            $('#rep_resumo_wrap').css('display', 'inline-flex');
            $('#secao_condicao_normal').hide();
            $('#secao_condicao_recorrente').show();
            // Inicializa selectpicker no bloco recorrente (pode ter sido ocultado antes)
            $('#secao_condicao_recorrente .selectpicker').selectpicker('refresh');
            gerarPreviewRecorrencias();
        }

        // ----------------------------------------------------------------
        // Gera preview da tabela "Recorrências Previstas"
        // ----------------------------------------------------------------
        function gerarPreviewRecorrencias() {
            var cada      = parseInt($('#rep_cada_hidden').val()) || 1;
            var freq      = $('#rep_freq_hidden').val() || '4';
            var ocorr     = parseInt($('#rep_ocorr_hidden').val()) || 0;
            var primVenc  = $('#rep_primeiro_venc').val();
            var emissao   = $('#data_emissao').val();
            var descricao = $('#descricao_compra').val() || '—';
            var vlr       = ctpParseMoney($('#vlr_primeira_parcela').val());
            var cobrar    = $('#rep_cobrar_no').val() || 'dia_vencimento';

            var tbody = $('#tbody_recorrencias');
            tbody.empty();

            if (!primVenc || !emissao || ocorr < 1) return;

            // Dia base para cálculo de vencimento
            var diaBase = null;
            if (cobrar === 'dia_vencimento') {
                diaBase = parseInt(primVenc.split('-')[2]);
            } else if (cobrar === 'dia_emissao') {
                diaBase = parseInt(emissao.split('-')[2]);
            } else if (cobrar === 'ultimo') {
                diaBase = null; // calculado por mês
            } else {
                diaBase = parseInt(cobrar);
            }

            for (var i = 0; i < ocorr; i++) {
                var dataEmissaoI  = repAvancarData(emissao,  freq, cada, i);
                var dataVencI     = repCalcularVencimento(primVenc, freq, cada, i, cobrar, diaBase);
                var descI         = descricao + (ocorr > 1 ? ' (' + (i+1) + '/' + ocorr + ')' : '');

                var tr = '<tr>';
                tr += '<td style="color:#888; font-size:12px;">' + (i+1) + '</td>';
                tr += '<td>' + repFormatDate(dataEmissaoI) + '</td>';
                tr += '<td>' + repFormatDate(dataVencI)    + '</td>';
                tr += '<td style="font-size:12px;">' + descI + '</td>';
                tr += '<td style="text-align:right;">R$ ' + ctpFormatMoney(vlr) + '</td>';
                tr += '</tr>';
                tbody.append(tr);
            }
        }

        // Avança data de emissão conforme frequência (YYYY-MM-DD)
        function repAvancarData(baseStr, freq, cada, n) {
            if (n === 0) return baseStr;
            var p = baseStr.split('-');
            var d = new Date(parseInt(p[0]), parseInt(p[1])-1, parseInt(p[2]));
            var total = cada * n;
            switch (freq) {
                case '1': d.setDate(d.getDate() + total);        break; // diária
                case '2': d.setDate(d.getDate() + total * 7);    break; // semanal
                case '3': d.setDate(d.getDate() + total * 15);   break; // quinzenal
                case '4': d.setMonth(d.getMonth() + total);      break; // mensal
                case '5': d.setMonth(d.getMonth() + total * 2);  break; // bimestral
                case '6': d.setMonth(d.getMonth() + total * 3);  break; // trimestral
                case '7': d.setMonth(d.getMonth() + total * 6);  break; // semestral
                case '8': d.setFullYear(d.getFullYear() + total); break; // anual
            }
            return repDateToStr(d);
        }

        // Calcula vencimento de cada ocorrência respeitando "Cobrar Sempre No"
        function repCalcularVencimento(primVencStr, freq, cada, n, cobrar, diaBase) {
            if (n === 0) return primVencStr;
            var p = primVencStr.split('-');
            var d = new Date(parseInt(p[0]), parseInt(p[1])-1, parseInt(p[2]));
            var total = cada * n;

            // Avança meses/semanas/dias conforme frequência
            switch (freq) {
                case '1': d.setDate(d.getDate() + total);        break;
                case '2': d.setDate(d.getDate() + total * 7);    break;
                case '3': d.setDate(d.getDate() + total * 15);   break;
                case '4': d.setMonth(d.getMonth() + total);      break;
                case '5': d.setMonth(d.getMonth() + total * 2);  break;
                case '6': d.setMonth(d.getMonth() + total * 3);  break;
                case '7': d.setMonth(d.getMonth() + total * 6);  break;
                case '8': d.setFullYear(d.getFullYear() + total); break;
            }

            // Ajusta dia conforme "Cobrar Sempre No"
            if (cobrar === 'ultimo') {
                // Último dia do mês atual de d
                d = new Date(d.getFullYear(), d.getMonth() + 1, 0);
            } else if (diaBase !== null && (cobrar === 'dia_vencimento' || cobrar === 'dia_emissao' || parseInt(cobrar) > 0)) {
                var ultimoDia = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
                d.setDate(Math.min(diaBase, ultimoDia));
            }

            return repDateToStr(d);
        }

        function repDateToStr(d) {
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var dd = String(d.getDate()).padStart(2, '0');
            return d.getFullYear() + '-' + mm + '-' + dd;
        }

        function repFormatDate(str) {
            if (!str) return '';
            var p = str.split('-');
            return p[2] + '/' + p[1] + '/' + p[0];
        }

        // Regatilha preview quando descrição ou valor mudam
        $(document).on('blur', '#descricao_compra, #vlr_primeira_parcela', function() {
            if ($('#repetir_lancamento').is(':checked')) gerarPreviewRecorrencias();
        });


        // ----------------------------------------------------------------
        // Anexos
        // ----------------------------------------------------------------
        function onAnexoPickerChange(input) {
            if (!input.files || !input.files.length) return;
            criarLinhaAnexoArquivo(input.files[0]);
            input.value = ''; // limpa para permitir escolher o próximo arquivo
        }

        function criarLinhaAnexoArquivo(file) {
            var dt = new DataTransfer();
            dt.items.add(file);

            var hidden = document.createElement('input');
            hidden.type = 'file';
            hidden.name = 'anexo[]';
            hidden.style.display = 'none';
            hidden.files = dt.files;

            var nome = document.createElement('span');
            nome.textContent = file.name;
            nome.style.cssText = 'max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

            var btnRemover = criarBotaoRemover(function () { removerAnexo(btnRemover); });

            var div = document.createElement('div');
            div.className = 'linha-anexo-arquivo';
            div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';
            div.appendChild(nome);
            div.appendChild(btnRemover);
            div.appendChild(hidden);
            document.getElementById('lista_anexos').appendChild(div);
        }

        function criarBotaoRemover(onRemove) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn-anexo-add';
            btn.title = 'Remover';
            btn.setAttribute('data-toggle', 'tooltip');
            btn.setAttribute('data-placement', 'top');
            btn.innerHTML = '<i class="fas fa-trash" style="font-size:12px; color:#337ab7;"></i>';
            btn.onclick = onRemove;
            $(btn).tooltip();
            return btn;
        }

        // Enter no campo URL sai do foco (dispara onLinkUrlBlur) em vez de tentar submeter o formulário.
        function onLinkUrlKeydown(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                event.target.blur();
            }
        }

        // Ao sair do foco da URL, se preenchida, cria a linha de exibição do
        // link (igual ao chip do anexo de arquivo) e limpa os inputs fixos
        // de Descrição/URL para permitir digitar o próximo link direto.
        function onLinkUrlBlur() {
            var inputDesc = document.getElementById('link_desc_input');
            var inputUrl  = document.getElementById('link_url_input');
            var url = inputUrl.value.trim();
            if (!url) return; // nada digitado

            var desc = inputDesc.value.trim() || url;
            criarLinhaLink(desc, url);

            inputDesc.value = '';
            inputUrl.value = '';
        }

        function criarLinhaLink(desc, url) {
            var hiddenDesc = document.createElement('input');
            hiddenDesc.type = 'hidden';
            hiddenDesc.name = 'anexo_link_desc[]';
            hiddenDesc.value = desc;

            var hiddenUrl = document.createElement('input');
            hiddenUrl.type = 'hidden';
            hiddenUrl.name = 'anexo_link_url[]';
            hiddenUrl.value = url;

            var texto = document.createElement('span');
            texto.textContent = desc + ' — ' + url;
            texto.style.cssText = 'max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

            var div = document.createElement('div');
            div.className = 'linha-anexo-link';
            div.style.cssText = 'display:flex;align-items:center;gap:8px;margin-top:6px;';

            var btnRemover = criarBotaoRemover(function () { removerAnexo(btnRemover); });

            div.appendChild(texto);
            div.appendChild(btnRemover);
            div.appendChild(hiddenDesc);
            div.appendChild(hiddenUrl);
            document.getElementById('lista_links').appendChild(div);
        }

        function removerAnexo(btn) {
            btn.parentElement.remove();
        }

        // ----------------------------------------------------------------
        // Pago checkbox — bloco À Vista / 1 Parcela
        // ----------------------------------------------------------------
        function onPagoAvistaChange() {
            var checked = $('#pago').is(':checked');
            if (checked) {
                $('#pago_data_pagamento').val($('#data_vencimento').val());
                $('#pago_desconto').val('');
                $('#pago_juros').val('');
                calcularValorPagoAvista();
                $('#bloco_pago_avista').show();
            } else {
                $('#pago_data_pagamento').val('');
                $('#pago_desconto').val('');
                $('#pago_juros').val('');
                $('#pago_valor_pago').val('');
                $('#bloco_pago_avista').hide();
            }
        }

        // Parseia valor digitado — suporta tanto "30.00" (US, do mask.money durante digitação)
        // quanto "3.000,00" (BR, após formatação). Usa verifica_virgula/replace_valor do contas_pagar.js
        function parseMoneyVal(v) {
            if (!v) return 0;
            if (verifica_virgula(v) === ',') v = replace_valor(v);
            return parseFloat(v) || 0;
        }

        function calcularValorPagoAvista() {
            var vlrTotal = ctpGetValorTotal();
            var dv = $('#pago_desconto').val();
            var jv = $('#pago_juros').val();
            var d = parseMoneyVal(dv);
            var j = parseMoneyVal(jv);
            if (dv) $('#pago_desconto').val(ctpFormatMoney(d));
            if (jv) $('#pago_juros').val(ctpFormatMoney(j));
            var vlrPago = vlrTotal - d + j;
            if (vlrPago < 0) vlrPago = 0;
            $('#pago_valor_pago').val(ctpFormatMoney(vlrPago));
        }

        // ----------------------------------------------------------------
        // Pago checkbox — tabela de parcelas
        // ----------------------------------------------------------------
        function togglePagoParc(idx) {
            var checked = $('#parc_pago_' + idx).is(':checked');
            var $sub = $('#parc_pago_row_' + idx);
            if (checked) {
                $('#parc_dt_pag_' + idx).val($('#parc_data_' + idx).val());
                $('#parc_desconto_' + idx).val('');
                $('#parc_juros_' + idx).val('');
                recalcularValorPago(idx);
                $sub.show();
            } else {
                $('#parc_dt_pag_' + idx).val('');
                $('#parc_desconto_' + idx).val('');
                $('#parc_juros_' + idx).val('');
                $('#parc_vlr_pago_' + idx).val('');
                $sub.hide();
            }
        }

        function recalcularValorPago(idx) {
            var vlrParc = parseMoneyVal($('#parc_valor_' + idx).val());
            var dv = $('#parc_desconto_' + idx).val();
            var jv = $('#parc_juros_' + idx).val();
            var d = parseMoneyVal(dv);
            var j = parseMoneyVal(jv);
            if (dv) $('#parc_desconto_' + idx).val(ctpFormatMoney(d));
            if (jv) $('#parc_juros_' + idx).val(ctpFormatMoney(j));
            var vlrPago = vlrParc - d + j;
            if (vlrPago < 0) vlrPago = 0;
            $('#parc_vlr_pago_' + idx).val(ctpFormatMoney(vlrPago));
        }

        // ----------------------------------------------------------------
        // Verifica documento duplicado (mesmo fornecedor + mesmo nº doc)
        // ----------------------------------------------------------------
        function verificarDocDuplicado() {
            var numero_doc = $('#number_doc').val().trim();
            var codigo_for = $('#codigo_cli_for').val();

            if (!numero_doc || !codigo_for || codigo_for === '999999999') return;

            $.ajax({
                type: 'POST',
                url: 'verificar_doc_ctp.php',
                data: { numero_doc: numero_doc, codigo_for: codigo_for },
                success: function(resp) {
                    if (resp.trim() === '1') {
                        $('#mensagem_erro').modal();
                        $('#mensagem_erro .modal-body').html(
                            '<div class="row"><div class="form-group col-md-12">' +
                            '<p><img src="img/exclamacao.png" class="fa fa-exclamation-triangle" width="20" height="23"/>&nbsp; ATENÇÃO!</p>' +
                            '<p>Este Número de Documento já está cadastrado para o fornecedor selecionado. Verifique o número e tente novamente.</p>' +
                            '</div></div>'
                        );
                        $('#mensagem_erro').one('hidden.bs.modal', function () {
                            $('#number_doc').val('').focus();
                        });
                    }
                }
            });
        }
        </script>

    </section><!-- container section start end -->

    <?php
    $javascript_file_name = 'contas_pagar.js';
    require 'rodape.php';
    ?>

    <!-- ================================================================
         Override de gravar_conta — usa FormData para incluir arquivos.
         Handlers que dependem de jQuery — DEVE ficar após rodape.php.
    ================================================================ -->
    <script>
    // ── Dados de CC e Conta Contábil disponíveis para JS (gerados pelo PHP) ──
    var ccOpcoes    = <?php echo json_encode($arr_cc_rat_js); ?>;
    var contaOpcoes = <?php echo json_encode($arr_conta_rat_js); ?>;
    var _locaisAntesEdicao = [];

    // ── Controle de formulário não salvo ──
    var _formDirty = false;

    function _marcarDirty() { _formDirty = true; }
    function _limparDirty()  { _formDirty = false; }

    // Monitora qualquer alteração nos campos do formulário
    $(document).on('change input', '#form_gravar_contas_pagar input, #form_gravar_contas_pagar select, #form_gravar_contas_pagar textarea', _marcarDirty);

    // Aviso ao fechar/recarregar aba ou navegar pelo browser
    window.addEventListener('beforeunload', function(e) {
        if (!_formDirty) return;
        e.preventDefault();
        e.returnValue = '';
    });

    // Sobrepõe o click do botão Voltar para perguntar antes de sair
    $(document).off('click', '.fecha_editar_dados').on('click', '.fecha_editar_dados', function(e) {
        e.preventDefault();
        if (_formDirty) {
            if (!confirm('Existem dados digitados que não foram gravados.\nDeseja realmente sair sem salvar?')) return;
        }
        _limparDirty();
        location.href = 'form_contas_pagar.php';
    });

    // ── ENTER navega como TAB em inputs/selects do formulário ──
    $(document).on('keydown', 'input, select', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();

        // Dentro da seção de rateio: navega apenas entre os campos do rateio;
        // no último campo aciona o botão Confirmar Rateio.
        if ($(this).closest('#secao_distribuir_rateio').length) {
            var $rateioFields = $('#secao_distribuir_rateio')
                .find('input:not([disabled]):not([readonly]), select:not([disabled])')
                .filter(':visible');
            var rIdx = $rateioFields.index(this);
            if (rIdx >= 0 && rIdx < $rateioFields.length - 1) {
                $rateioFields.eq(rIdx + 1).focus();
            } else {
                var $btnRateio = $('#btn_confirmar_rateio_final');
                if ($btnRateio.length && $btnRateio.is(':visible')) {
                    $btnRateio.focus();
                }
            }
            return;
        }

        var focusable = $('input:not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled])').filter(':visible');
        var idx = focusable.index(this);
        if (idx >= 0 && idx < focusable.length - 1) {
            focusable.eq(idx + 1).focus();
        }
    });

    // ── Handler do toggle Rateio ──
    $(document).ready(function () {

        // Máscara money nos campos de valor do rateio (delegada — funciona em linhas dinâmicas)
        $(document).on('keypress', '.rat-valor', function(e) {
            mask.money.call(this, e);
            if (_modoRateio !== 'valor') _setModoRateio('valor');
        });
        $(document).on('blur', '.rat-valor', function() {
            // Ao sair: converte formato US → BR e recalcula
            var n = parseFloat($(this).val()) || 0;
            $(this).val(formatMoney(n));
            recalcularRateio();
        });
        // Máscara e handler do campo % — permite dígitos e vírgula
        $(document).on('keypress', '.rat-perc', function(e) {
            var c = e.which;
            if (c === 0 || c === 8) return true;
            if (c === 44) { return $(this).val().replace('%','').indexOf(',') === -1; } // permite 1 vírgula
            if (c < 48 || c > 57) return false;
            if (_modoRateio !== 'perc') _setModoRateio('perc');
            return true;
        });
        $(document).on('blur', '.rat-perc', function() {
            var raw = $(this).val().replace('%','').replace(',','.');
            var n = parseFloat(raw) || 0;
            $(this).val(n > 0 ? n.toFixed(2).replace('.', ',') + '%' : '');
            recalcularRateio();
        });

        // Ao carregar: pré-preenche vencimento conforme modo selecionado
        (function() {
            var modo = $('#sel_modo_parc').val();
            var emissao = $('#data_emissao').val();
            if (modo === 'avista' && emissao) {
                $('#data_vencimento').val(emissao);
            } else if (modo === 'uma_parcela' && emissao) {
                $('#data_vencimento').val(addDias(emissao, 30));
            }
        })();

        $('#habilitar_rateio').on('change', function () {
            var on = $(this).is(':checked');
            var $local = $('#codigo_fazenda');

            if (on) {
                // Valida se o valor foi digitado antes de habilitar o rateio
                var vlrTotal = ctpGetValorTotal();
                if (!vlrTotal || vlrTotal <= 0) {
                    $(this).prop('checked', false);
                    alert('Digite o Valor da conta antes de habilitar o Rateio.');
                    $('#vlr_primeira_parcela').focus();
                    return;
                }
                // Rateio ON → oculta CC e Conta Contábil, move Local para dentro do fieldset
                $('#col_cc').hide();
                $('#col_conta').hide();

                // Restaurar linha de input e limpar fases anteriores
                $('#tr_local_input').show();
                $('#linhas_rateio').hide().empty();
                // Mover select Local para dentro da tabela (coluna Local) e botão para célula ao lado
                $('#td_local_select').append($local);
                $('#td_local_confirm').append($('#col_btn_confirmar_locais button')).children().hide();
                $('#secao_distribuir_rateio').show();

                $local.find('option').prop('selected', false);
                $local.attr('multiple', 'multiple')
                      .attr('data-live-search', 'true')
                      .attr('data-size', '8')
                      .addClass('selectpicker');
                $local.selectpicker({ actionsBox: true, width: '100%', noneSelectedText: '...' });
                $local.val([]);
                $local.selectpicker('refresh');

                var $bs = $local.closest('.bootstrap-select');
                $bs.css('width', '100%');
                $bs.find('.bs-select-all').hide();
                $bs.find('.dropdown-menu').css({ 'min-width': '250px', 'max-width': 'none', 'width': 'auto' });

                // Monitora seleção para mostrar/ocultar coluna do botão Confirmar
                $local.on('changed.bs.select.rateio', function () {
                    var selecionados = $local.val();
                    if (selecionados && selecionados.length > 0) {
                        $('#btn_confirmar_locais').show();
                    } else {
                        $('#btn_confirmar_locais').hide();
                        $('#linhas_rateio').hide().empty();
                        $('#rodape_fase1, #rodape_fase2, #rodape_rateio').remove();
                        $('#tr_local_input').show();
                    }
                });

            } else {
                // Se rateio já está configurado, pede confirmação antes de perder os dados
                if ($('#rateio_status').is(':visible') || $('#linhas_rateio').children().length > 0) {
                    $(this).prop('checked', true);
                    $('#modal_fechar_rateio').modal('show');
                    return;
                }
                _executarRateioOff();
            }
        });
    });

    // ── FASE 1: Confirma locais → tabela com selectpicker CC por local (1 confirmar global) ──
    function confirmarLocaisRateio() {
        var $local = $('#codigo_fazenda');
        var selecionados = $local.val();
        if (!selecionados || selecionados.length === 0) return;

        // Se seleção não mudou, apenas fecha o selector sem reconstruir
        var novosSorted   = selecionados.slice().sort();
        var antesSorted   = _locaisAntesEdicao.slice().sort();
        var semMudanca    = (novosSorted.length === antesSorted.length &&
                            novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
        if (semMudanca && _locaisAntesEdicao.length > 0) {
            $('#btn_fechar_local').remove();
            $('#tr_local_input').hide();
            return;
        }

        // ── Se Phase 3 está ativa, apenas adiciona/remove locais incrementalmente ──
        var $linhasFase3 = $('#tbl_rateio tbody tr.linha-valor-rateio');
        if ($linhasFase3.length > 0) {
            var locaisJaPresentes = [];
            $linhasFase3.each(function() {
                var lid = String($(this).data('local-id'));
                if (locaisJaPresentes.indexOf(lid) === -1) locaisJaPresentes.push(lid);
            });
            // Também conta as linhas tr-novo-local pendentes
            $('#tbl_rateio tbody tr.tr-novo-local').each(function() {
                var lid = String($(this).data('local-id'));
                if (locaisJaPresentes.indexOf(lid) === -1) locaisJaPresentes.push(lid);
            });

            // Remove locais que foram desmarcados
            var locaisRemovidos = locaisJaPresentes.filter(function(id) {
                return selecionados.indexOf(id) === -1 && selecionados.indexOf(Number(id)) === -1;
            });
            $.each(locaisRemovidos, function(i, localId) {
                $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]').remove();
                $('#tbl_rateio tbody tr.tr-novo-local[data-local-id="' + localId + '"]').remove();
            });

            // Adiciona locais novos
            var locaisNovos = selecionados.filter(function(id) {
                return locaisJaPresentes.indexOf(String(id)) === -1;
            });
            $.each(locaisNovos, function(i, idLocal) {
                var $opt = $local.find('option[value="' + idLocal + '"]');
                var nomeLocal = $opt.data('nome') || $opt.text();
                _adicionarNovoLocalFase3(idLocal, nomeLocal);
            });

            $('#btn_fechar_local').remove();
            $('#tr_local_input').hide();
            fixarIconeSelecLocais();
            recalcularRateio();
            return;
        }

        $('#linhas_rateio').hide().empty();
        $('#rodape_fase2').remove();
        $('#rodape_rateio').remove();
        $('#col_btn_confirmar_locais').hide();
        $('#rodape_fase1').remove();

        var optionsCC = '';
        $.each(ccOpcoes, function(i, cc) {
            optionsCC += '<option value="' + cc.id + '">' + cc.nome + '</option>';
        });

        var html = '<table class="tbl-parcelas" id="tbl_rateio" style="width:100%;table-layout:fixed;">';
        html += '<colgroup><col style="width:16%"><col style="width:16%"><col style="width:26%"><col style="width:14%"><col style="width:9%"><col style="width:9%"></colgroup><tbody>';

        $.each(selecionados, function(i, idLocal) {
            var $opt = $local.find('option[value="' + idLocal + '"]');
            var nomeLocal = $opt.data('nome') || $opt.text();
            var idxCC = 'cc_rateio_' + i;
            var isLast = (i === selecionados.length - 1);
            html += '<tr class="linha-fase1" data-local-id="' + idLocal + '" data-local-nome="' + nomeLocal.replace(/"/g,'&quot;') + '">';
            html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + nomeLocal + '</span></td>';
            html += '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker fase1-cc" id="' + idxCC + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsCC + '</select></td>';
            if (isLast) {
                html += '<td style="vertical-align:middle;padding:4px 8px;"><button type="button" class="btn btn-primary" onclick="confirmarTodoCC()">Confirmar</button></td><td colspan="3"></td>';
            } else {
                html += '<td colspan="4"></td>';
            }
            html += '</tr>';
        });

        html += '</tbody></table>';
        $('#linhas_rateio').html(html);

        $('#linhas_rateio .fase1-cc').each(function() {
            var $s = $(this);
            $s.find('option:first').prop('selected', true);
            $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
            var $bs = $s.closest('.bootstrap-select');
            $bs.css({ 'width': '100%', 'display': 'block' });
            $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
            $bs.find('.dropdown-menu').css({ 'min-width': '280px', 'width': 'auto' });
        });

        $('#btn_fechar_local').remove();
        $('#tr_local_input').hide();
        $('#linhas_rateio').show();
        fixarIconeSelecLocais();
    }

    // ── FASE 2: Lê CC de todas as linhas → tabela com selectpicker Conta por linha Local+CC ──
    function confirmarTodoCC() {
        document.activeElement && document.activeElement.blur();
        var linhas = [];
        var valido = true;

        $('#tbl_rateio tbody tr.linha-fase1').each(function() {
            var localId   = $(this).data('local-id');
            var localNome = $(this).data('local-nome');
            var ccIds = [];
            $(this).find('.fase1-cc option:selected').each(function() {
                ccIds.push($(this).val());
            });
            if (ccIds.length === 0) {
                alert('Selecione pelo menos um Centro de Custos para cada local.');
                valido = false; return false;
            }
            $.each(ccIds, function(j, ccId) {
                var ccNome = ccId;
                $.each(ccOpcoes, function(k, cc) { if (String(cc.id) === String(ccId)) { ccNome = cc.nome; return false; } });
                linhas.push({ localId: localId, localNome: localNome, ccId: ccId, ccNome: ccNome });
            });
        });

        if (!valido) return;
        $('#rodape_fase1').remove();

        var optionsConta = '';
        $.each(contaOpcoes, function(k, cta) {
            if (cta.nivel === 1)      optionsConta += '<option value="' + cta.id + '" disabled style="color:#777;font-weight:600;">' + cta.nome + '</option>';
            else if (cta.nivel === 2) optionsConta += '<option value="' + cta.id + '" disabled style="color:#888;">&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
            else                      optionsConta += '<option value="' + cta.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
        });

        var html = '<table class="tbl-parcelas" id="tbl_rateio" style="width:100%;table-layout:fixed;">';
        html += '<colgroup><col style="width:16%"><col style="width:16%"><col style="width:26%"><col style="width:14%"><col style="width:9%"><col style="width:9%"></colgroup><tbody>';

        var lastLocalId = null;
        $.each(linhas, function(i, ln) {
            var idxConta = 'conta_rateio_' + i;
            var showLocal = (ln.localId !== lastLocalId);
            lastLocalId = ln.localId;
            html += '<tr class="linha-fase2"';
            html += ' data-local-id="'   + ln.localId   + '"';
            html += ' data-local-nome="' + ln.localNome.replace(/"/g,'&quot;') + '"';
            html += ' data-cc-id="'      + ln.ccId      + '"';
            html += ' data-cc-nome="'    + ln.ccNome.replace(/"/g,'&quot;') + '">';
            if (showLocal) {
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + ln.localNome + '</span></td>';
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                        '<span class="lbl-parcela">' + ln.ccNome + '</span>' +
                        ' <a href="#" onclick="editarCCDoLocal(\'' + ln.localId + '\',\'' + ln.localNome.replace(/'/g,"\\'") + '\');return false;" data-toggle="tooltip" data-placement="top" title="Selecione Centro de Custos" style="color:#337ab7;font-size:11px;margin-left:4px;">' +
                        '<i class="fas fa-pen"></i></a></td>';
            } else {
                html += '<td style="vertical-align:middle;padding:4px 8px;"></td>';
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + ln.ccNome + '</span></td>';
            }
            html += '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker fase2-conta" id="' + idxConta + '" multiple data-live-search="true" data-size="8" data-width="100%">';
            html += '<option value="" disabled>...</option>' + optionsConta;
            html += '</select></td>';
            html += '<td class="td-confirmar-conta" style="vertical-align:middle;padding:4px 8px;"></td>';
            html += '<td colspan="2"></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        $('#linhas_rateio').html(html);

        $('#linhas_rateio .fase2-conta').each(function() {
            var $s = $(this);
            $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
            var $bs = $s.closest('.bootstrap-select');
            $bs.css({ 'width': '100%', 'display': 'block' });
            $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
            $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
        });
        $('#linhas_rateio .fase2-conta').each(function() {
            _bindReplicarConta($(this));
        });
        $('#linhas_rateio [data-toggle="tooltip"]').tooltip();
        fixarConfirmarContaButton();
        fixarIconeSelecLocais();
    }

    // ── FASE 3: Lê Conta de todas as linhas → tabela final com Valor/% ──
    function confirmarTodaConta() {
        // Se algum dropdown de conta está aberto, fecha-o primeiro
        // (isso dispara hidden.bs.select → diálogo de replicação aparece antes da validação)
        var $dropdownAberto = $('#tbl_rateio .bootstrap-select.open');
        if ($dropdownAberto.length) {
            $dropdownAberto.find('button.dropdown-toggle').trigger('click');
            setTimeout(confirmarTodaConta, 50);
            return;
        }
        // Aguarda o diálogo de replicação ser processado antes de validar
        if (_replicarContaPendente) return;

        var linhas = [];
        var valido = true;

        $('#tbl_rateio tbody tr.linha-fase2').each(function() {
            var localId   = $(this).data('local-id');
            var localNome = $(this).data('local-nome');
            var ccId      = $(this).data('cc-id');
            var ccNome    = $(this).data('cc-nome');
            var contaIds = [];
            $(this).find('.fase2-conta option:selected').each(function() {
                if ($(this).val()) contaIds.push($(this).val());
            });
            if (contaIds.length === 0) {
                alert('Selecione pelo menos uma Conta Contábil para cada linha.');
                valido = false; return false;
            }
            $.each(contaIds, function(k, contaId) {
                var contaNome = contaId;
                $.each(contaOpcoes, function(m, ct) { if (String(ct.id) === String(contaId)) { contaNome = ct.nome; return false; } });
                linhas.push({ localId: localId, localNome: localNome, ccId: ccId, ccNome: ccNome, contaId: contaId, contaNome: contaNome });
            });
        });

        if (!valido) return;
        $('#rodape_fase2').remove();

        var html = '<table class="tbl-parcelas" id="tbl_rateio" style="width:100%;table-layout:fixed;">';
        html += '<colgroup><col style="width:16%"><col style="width:16%"><col style="width:26%"><col style="width:14%"><col style="width:9%"><col style="width:9%"></colgroup><tbody>';

        var lastLocalId = null, lastGroupKey = null;
        $.each(linhas, function(i, ln) {
            var groupKey    = ln.localId + '_' + ln.ccId;
            var showLocal   = (ln.localId !== lastLocalId);
            var showCC      = (groupKey !== lastGroupKey);
            lastLocalId     = ln.localId;
            lastGroupKey    = groupKey;
            var localNomeJs = ln.localNome.replace(/'/g,"\\'");
            var ccNomeJs    = ln.ccNome.replace(/'/g,"\\'");

            html += '<tr class="linha-valor-rateio"' +
                ' data-local-id="'   + ln.localId   + '"' +
                ' data-local-nome="' + ln.localNome.replace(/"/g,'&quot;') + '"' +
                ' data-cc-id="'      + ln.ccId      + '"' +
                ' data-cc-nome="'    + ln.ccNome.replace(/"/g,'&quot;') + '"' +
                ' data-conta-id="'   + ln.contaId   + '"' +
                ' data-conta-nome="' + ln.contaNome.replace(/"/g,'&quot;') + '">';

            if (showLocal) {
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ln.localNome + '</span>' +
                    '<input type="hidden" name="rat2_local_id[]" value="' + ln.localId + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + ln.localNome + '">' +
                    '</td>';
            } else {
                html += '<td style="vertical-align:middle;padding:4px 8px;">' +
                    '<input type="hidden" name="rat2_local_id[]" value="' + ln.localId + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + ln.localNome + '">' +
                    '</td>';
            }

            if (showCC) {
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ln.ccNome + '</span>' +
                    (showLocal ? ' <a href="#" onclick="editarCCDoLocalFase3(\'' + ln.localId + '\',\'' + localNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' : '') +
                    '<input type="hidden" name="rat2_cc_id[]" value="' + ln.ccId + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ln.ccNome + '">' +
                    '</td>';
            } else {
                html += '<td style="vertical-align:middle;padding:4px 8px;">' +
                    '<input type="hidden" name="rat2_cc_id[]" value="' + ln.ccId + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ln.ccNome + '">' +
                    '</td>';
            }

            if (showCC) {
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ln.contaNome + '</span>' +
                    ' <a href="#" onclick="editarContaDoCC(\'' + ln.localId + '\',\'' + ln.ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Contas"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                    '<input type="hidden" name="rat2_conta_id[]" value="' + ln.contaId + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + ln.contaNome + '">' +
                    '</td>';
            } else {
                html += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ln.contaNome + '</span>' +
                    '<input type="hidden" name="rat2_conta_id[]" value="' + ln.contaId + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + ln.contaNome + '">' +
                    '</td>';
            }

            html += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
                '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
            html += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
                '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
            html += '<td></td></tr>';
        });

        html += '<tr id="tr_rateio_restante">';
        html += '<td colspan="4" style="text-align:right;font-size:12px;color:#666;padding:6px 8px;border-top:1px solid #ddd;">Total Digitado: <span id="span_rat_total" style="color:#27ae60;font-weight:600;font-size:13px;margin-right:14px;">R$ 0,00</span>&nbsp;&nbsp;&nbsp;Restante a distribuir:</td>';
        html += '<td id="td_rat_vlr_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;white-space:nowrap;border-top:1px solid #ddd;">R$ 0,00</td>';
        html += '<td id="td_rat_pct_rest" style="font-size:13px;font-weight:600;color:#c0392b;text-align:right;padding:6px 8px;border-top:1px solid #ddd;">0,00%</td>';
        html += '</tr></tbody></table>';

        $('#linhas_rateio').html(html);
        $('#linhas_rateio').after(
            '<div id="rodape_rateio" style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding:4px 2px;">' +
            '<button type="button" id="btn_confirmar_rateio_final" class="btn btn-success" onclick="confirmarRateioFinal()">Confirmar Rateio</button>' +
            '<button type="button" class="btn btn-default" onclick="voltarRateio()">Fechar</button>' +
            '</div>'
        );

        $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
        fixarIconeSelecLocais();
        _modoRateio = null; // libera ambos os campos ao iniciar a fase de valores
        recalcularRateio();
    }

    // ── Abre seletor de Contas para reeditar um grupo Local+CC ──
    function editarContaDoCC(localId, ccId, localNome, ccNome) {
        if (_temEditorAberto()) return;
        var gKey     = (localId + '_' + ccId).replace(/\W/g,'_');
        var editorId = 'tr_editar_conta_' + gKey;
        if ($('#' + editorId).length) return;

        var $linhasDoGrupo = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"][data-cc-id="' + ccId + '"]');
        var contaIdsAtuais = [], valoresAtuais = {};
        $linhasDoGrupo.each(function() {
            var cid = String($(this).data('conta-id'));
            contaIdsAtuais.push(cid);
            valoresAtuais[cid] = $(this).find('.rat-valor').val();
        });

        var optionsConta = '';
        $.each(contaOpcoes, function(k, ct) {
            optionsConta += '<option value="' + ct.id + '">' + ct.nome + '</option>';
        });

        var selectId    = 'editar_conta_sel_' + gKey;
        var localNomeJs = localNome.replace(/'/g,"\\'");
        var ccNomeJs    = ccNome.replace(/'/g,"\\'");

        var editorHtml = '<tr id="' + editorId + '" class="tr-editar-conta"' +
            ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
            ' data-local-nome="' + localNome.replace(/"/g,'&quot;') + '" data-cc-nome="' + ccNome.replace(/"/g,'&quot;') + '">' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            '<span class="lbl-parcela">' + localNome + '</span></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            '<span class="lbl-parcela">' + ccNome + '</span></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<select class="selectpicker" id="' + selectId + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsConta + '</select></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;" colspan="3">' +
            '<button type="button" class="btn btn-primary" onmousedown="confirmarContaDoCC(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\')">Confirmar</button>' +
            ' <button type="button" class="btn btn-default" onclick="fecharEdicaoConta(\'' + localId + '\',\'' + ccId + '\')">Fechar</button></td></tr>';

        var $firstRow = $linhasDoGrupo.first();
        if ($firstRow.length) { $firstRow.before(editorHtml); } else { $('#tr_rateio_restante').before(editorHtml); }

        var $s = $('#' + selectId);
        $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
        $s.val(contaIdsAtuais);
        $s.selectpicker('refresh');
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
        $('#' + editorId).data('valores-atuais', valoresAtuais).data('conta-ids-antes', contaIdsAtuais);

        fixarIconeSelecLocais();
    }

    // ── Confirma reedição de Contas de um grupo Local+CC ──
    function confirmarContaDoCC(localId, ccId, localNome, ccNome) {
        var gKey   = (localId + '_' + ccId).replace(/\W/g,'_');
        var $edRow = $('#tr_editar_conta_' + gKey);
        var valoresAtuais  = $edRow.data('valores-atuais') || {};
        var contaIdsAntes  = $edRow.data('conta-ids-antes') || [];

        var contaIds = [];
        $('#editar_conta_sel_' + gKey + ' option:selected').each(function() {
            if ($(this).val()) contaIds.push($(this).val());
        });
        if (contaIds.length === 0) { alert('Selecione pelo menos uma Conta Contábil.'); return; }

        var novosSorted = contaIds.slice().sort();
        var antesSorted = contaIdsAntes.slice().sort();
        var semMudanca  = (novosSorted.length === antesSorted.length &&
                          novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
        if (semMudanca && contaIdsAntes.length > 0) {
            $edRow.remove();
            fixarIconeSelecLocais();
            return;
        }

        var $linhasDoGrupo = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"][data-cc-id="' + ccId + '"]');
        var $insertBefore  = $linhasDoGrupo.length > 0 ? $linhasDoGrupo.last().next('tr') : $edRow.next('tr');
        $edRow.remove();
        $linhasDoGrupo.remove();

        var showLocal    = ($('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]').length === 0);
        var localNomeEsc = localNome.replace(/"/g,'&quot;');
        var ccNomeEsc    = ccNome.replace(/"/g,'&quot;');
        var localNomeJs  = localNome.replace(/'/g,"\\'");
        var ccNomeJs     = ccNome.replace(/'/g,"\\'");

        var newRowsHtml = '';
        $.each(contaIds, function(i, contaId) {
            var contaNome = '';
            $.each(contaOpcoes, function(m, ct) { if (String(ct.id) === String(contaId)) { contaNome = ct.nome; return false; } });
            var valorSalvo = valoresAtuais[contaId] || '';
            var isFirst    = (i === 0);

            newRowsHtml += '<tr class="linha-valor-rateio"' +
                ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
                ' data-conta-id="' + contaId + '"' +
                ' data-local-nome="' + localNomeEsc + '" data-cc-nome="' + ccNomeEsc + '"' +
                ' data-conta-nome="' + contaNome.replace(/"/g,'&quot;') + '">';

            if (showLocal && isFirst) {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + localNome + '</span>' +
                    '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                    '</td>';
            } else {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                    '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                    '</td>';
            }

            if (isFirst) {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ccNome + '</span>' +
                    ' <a href="#" onclick="editarCCDoLocalFase3(\'' + localId + '\',\'' + localNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                    '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                    '</td>';
            } else {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                    '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                    '</td>';
            }

            if (isFirst) {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + contaNome + '</span>' +
                    ' <a href="#" onclick="editarContaDoCC(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Contas"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                    '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                    '</td>';
            } else {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + contaNome + '</span>' +
                    '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                    '</td>';
            }

            newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
                '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"' +
                (valorSalvo ? ' value="' + valorSalvo + '"' : '') + '></td>';
            newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
                '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
            newRowsHtml += '<td></td></tr>';
        });

        if ($insertBefore.length) { $insertBefore.before(newRowsHtml); } else { $('#tr_rateio_restante').before(newRowsHtml); }

        $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
        fixarIconeSelecLocais();
        _sincronizarIconesCC();
        recalcularRateio();
    }

    // ── Gera HTML de uma linha de valor/rateio ──
    function gerarLinhaValorRateio(localId, localNome, ccId, ccNome, contaId, contaNome) {
        var uid = (localId + '_' + ccId + '_' + contaId).replace(/\W/g,'_') + '_' + Date.now();
        var html = '<tr class="linha-valor-rateio">';
        html += '<td><span class="lbl-parcela">' + localNome + '</span>' +
                    '<input type="hidden" name="rat2_local_id[]"   value="' + localId   + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                '</td>';
        html += '<td><span class="lbl-parcela">' + ccNome + '</span>' +
                    '<input type="hidden" name="rat2_cc_id[]"   value="' + ccId   + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                '</td>';
        html += '<td><span class="lbl-parcela">' + contaNome + '</span>' +
                    '<input type="hidden" name="rat2_conta_id[]"   value="' + contaId   + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                '</td>';
        html += '<td style="text-align:right;">' +
                    '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
                    ' style="height:30px;font-size:13px;text-align:right;">' +
                '</td>';
        html += '<td style="text-align:right;">' +
                    '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
                    ' style="height:30px;font-size:13px;text-align:right;">' +
                '</td>';
        html += '<td style="text-align:center;">' +
                    '<button type="button" class="btn btn-xs" onclick="excluirLinhaRateio(this)" title="Remover" style="background:transparent;border:none;color:#2980b9;padding:2px 6px;">' +
                    '<i class="fas fa-trash"></i></button>' +
                '</td>';
        html += '</tr>';
        return html;
    }

    // ── Remove uma linha de rateio ──
    function excluirLinhaRateio(btn) {
        $(btn).closest('tr').remove();
        recalcularRateio();
        // Se não restam linhas, reabilita Confirmar Rateio
        if ($('.linha-valor-rateio').length === 0) {
            $('#btn_confirmar_rateio_final').removeClass('btn-default').addClass('btn-success')
                .text('Confirmar Rateio').prop('disabled', false);
        }
    }

    // ── Adiciona linha em branco para distribuição manual ──
    function adicionarLinhaRateio() {
        // Linha com selects simples para escolher Local, CC e Conta
        var optLocal = '', optCC = '', optConta = '';

        // Opções de Local (das fazendas disponíveis no select principal)
        $('#codigo_fazenda option:not([disabled])').each(function() {
            optLocal += '<option value="' + $(this).val() + '" data-nome="' + $(this).data('nome') + '">' + $(this).text() + '</option>';
        });
        $.each(ccOpcoes, function(i, cc) {
            optCC += '<option value="' + cc.id + '"' + (cc.id==='001'?' selected':'') + '>' + cc.nome + '</option>';
        });
        $.each(contaOpcoes, function(i, ct) {
            if (ct.nivel === 1) optConta += '<option value="' + ct.id + '" disabled style="color:#777;font-weight:600;">' + ct.nome + '</option>';
            else if (ct.nivel === 2) optConta += '<option value="' + ct.id + '" disabled style="color:#888;">    ' + ct.nome + '</option>';
            else optConta += '<option value="' + ct.id + '">        ' + ct.nome + '</option>';
        });

        var uid = 'manual_' + Date.now();
        var html = '<tr class="linha-valor-rateio linha-manual" id="tr_' + uid + '">';
        html += '<td><select class="form-control sel-local-manual" style="height:30px;font-size:12px;">' +
                '<option value="">...</option>' + optLocal + '</select></td>';
        html += '<td><select class="form-control sel-cc-manual" style="height:30px;font-size:12px;">' +
                optCC + '</select></td>';
        html += '<td><select class="form-control sel-conta-manual" style="height:30px;font-size:12px;">' +
                '<option value="">...</option>' + optConta + '</select></td>';
        html += '<td style="text-align:right;">' +
                '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
        html += '<td><input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
        html += '<td style="text-align:center;"><button type="button" class="btn btn-primary btn-xs"' +
                ' onclick="confirmarLinhaManual(this)" style="white-space:nowrap; font-size:11px; padding:3px 7px;">Confirmar</button></td>';
        html += '</tr>';

        // Insere antes da linha de totais
        $('#tr_rateio_restante').before(html);
        recalcularRateio();
    }

    // ── Confirma linha adicionada manualmente ──
    function confirmarLinhaManual(btn) {
        var $tr = $(btn).closest('tr');

        var $selLocal = $tr.find('.sel-local-manual');
        var $selCC    = $tr.find('.sel-cc-manual');
        var $selConta = $tr.find('.sel-conta-manual');

        var localId   = $selLocal.val();
        var localNome = $selLocal.find('option:selected').data('nome') || $selLocal.find('option:selected').text();
        var ccId      = $selCC.val();
        var ccNome    = $selCC.find('option:selected').text();
        var contaId   = $selConta.val();
        var contaNome = $selConta.find('option:selected').text();

        if (!localId || localId === '') { alert('Selecione o Local.'); return; }
        if (!ccId   || ccId   === '') { alert('Selecione o Centro de Custos.'); return; }
        if (!contaId || contaId === '') { alert('Selecione a Conta Contábil.'); return; }

        // Pega o valor já digitado na linha antes de substituir
        var valorAtual = $tr.find('.rat-valor').val() || '';

        // Gera linha normal (com lixeira)
        var novaLinha = $(gerarLinhaValorRateio(localId, localNome, ccId, ccNome, contaId, contaNome));
        if (valorAtual) {
            novaLinha.find('.rat-valor').val(valorAtual);
        }
        $tr.replaceWith(novaLinha);
        recalcularRateio();
    }

    // ── Recalcula restante e percentuais em tempo real ──
    function recalcularRateio() {
        var total = ctpGetValorTotal();
        if (!total || total <= 0) total = 0;

        var somaValores = 0, restante = 0;

        if (_modoRateio === 'perc') {
            // Modo %: calcula Valor a partir do percentual digitado
            $('.rat-perc').each(function() {
                var raw = $(this).val().replace('%','').replace(',','.');
                var pct = parseFloat(raw) || 0;
                var valor = total > 0 ? (pct / 100 * total) : 0;
                $(this).closest('tr').find('.rat-valor').val(valor > 0 ? formatMoney(valor) : '');
                somaValores += valor;
            });
        } else {
            // Modo Valor (ou sem modo): calcula % a partir do valor digitado
            $('.rat-valor').each(function() {
                var raw = $(this).val();
                var v = raw.indexOf(',') !== -1
                    ? raw.replace(/\./g,'').replace(',','.')
                    : raw;
                somaValores += parseFloat(v) || 0;
            });
            // Atualiza percentuais de cada linha
            $('.rat-valor').each(function() {
                var $row = $(this).closest('tr');
                var v = parseFloat($(this).val().replace(/\./g,'').replace(',','.')) || 0;
                var pct = total > 0 ? (v / total * 100) : 0;
                $row.find('.rat-perc').val(pct > 0 ? pct.toFixed(2).replace('.',',') + '%' : '');
            });
        }

        restante = total - somaValores;

        // Atualiza soma distribuída e restante
        $('#span_rat_total').text('R$ ' + somaValores.toFixed(2).replace('.',','));
        var corRest = (Math.abs(restante) < 0.01) ? '#27ae60' : '#c0392b';
        $('#td_rat_vlr_rest').text('R$ ' + restante.toFixed(2).replace('.',',')).css('color', corRest);
        $('#td_rat_pct_rest').text((total > 0 ? restante/total*100 : 0).toFixed(2).replace('.',',') + '%').css('color', corRest);

        // Garante que novas linhas recebam o estado correto de readonly
        _setModoRateio(_modoRateio);
    }

    // ── Valida e confirma o rateio completo ──
    function confirmarRateioFinal() {
        // Verifica se ainda há linhas de CC ou Conta pendentes de confirmação
        if ($('.linha-conta-rateio').length > 0) {
            alert('Confirme todos os Centros de Custos antes de fechar o rateio.');
            return;
        }

        var total = ctpGetValorTotal();
        var somaValores = 0;
        $('.rat-valor').each(function() {
            var raw = $(this).val();
            var v = raw.indexOf(',') !== -1
                ? raw.replace(/\./g,'').replace(',','.')
                : raw;
            somaValores += parseFloat(v) || 0;
        });

        if (Math.abs(total - somaValores) > 0.01) {
            alert('O valor distribuído (R$ ' + somaValores.toFixed(2).replace('.',',') + ') não corresponde ao valor total (R$ ' + total.toFixed(2).replace('.',',') + ').\nAjuste os valores antes de confirmar.');
            return;
        }

        if ($('.rat-valor').length === 0) {
            alert('Nenhuma distribuição informada.');
            return;
        }

        // Monta rateio_json no formato esperado pelo backend (local → ccs → contas)
        var total = ctpGetValorTotal();
        var locaisMap = {};
        var locaisOrder = [];

        $('.linha-valor-rateio').each(function() {
            var localId   = $(this).find('input[name="rat2_local_id[]"]').val();
            var localNome = $(this).find('input[name="rat2_local_nome[]"]').val();
            var ccId      = $(this).find('input[name="rat2_cc_id[]"]').val();
            var ccNome    = $(this).find('input[name="rat2_cc_nome[]"]').val();
            var contaId   = $(this).find('input[name="rat2_conta_id[]"]').val();
            var contaNome = $(this).find('input[name="rat2_conta_nome[]"]').val();
            var raw       = $(this).find('.rat-valor').val() || '0';
            var v         = raw.indexOf(',') !== -1
                            ? parseFloat(raw.replace(/\./g,'').replace(',','.'))
                            : (parseFloat(raw) || 0);
            var perc      = total > 0 ? parseFloat((v / total * 100).toFixed(4)) : 0;

            if (!locaisMap[localId]) {
                locaisMap[localId] = { id: localId, nome: localNome, valor: 0, perc: 0, ccs: {}, ccsOrder: [] };
                locaisOrder.push(localId);
            }
            locaisMap[localId].valor += v;

            var ccKey = ccId;
            if (!locaisMap[localId].ccs[ccKey]) {
                locaisMap[localId].ccs[ccKey] = { id: ccId, nome: ccNome, valor: 0, perc: 0, contas: [] };
                locaisMap[localId].ccsOrder.push(ccKey);
            }
            locaisMap[localId].ccs[ccKey].valor += v;
            locaisMap[localId].ccs[ccKey].contas.push({ id: contaId, nome: contaNome, valor: v, perc: perc });
        });

        var locaisArr = [];
        $.each(locaisOrder, function(i, localId) {
            var loc = locaisMap[localId];
            loc.perc = total > 0 ? parseFloat((loc.valor / total * 100).toFixed(4)) : 0;
            var ccsArr = [];
            $.each(loc.ccsOrder, function(j, ccKey) {
                var cc = loc.ccs[ccKey];
                cc.perc = total > 0 ? parseFloat((cc.valor / total * 100).toFixed(4)) : 0;
                ccsArr.push({ id: cc.id, nome: cc.nome, valor: cc.valor, perc: cc.perc, contas: cc.contas });
            });
            locaisArr.push({ id: loc.id, nome: loc.nome, valor: loc.valor, perc: loc.perc, ccs: ccsArr });
        });

        $('#rateio_json').val(JSON.stringify(locaisArr));

        // Tudo ok — oculta a seção de distribuição e exibe status "Rateio Configurado"
        $('#secao_distribuir_rateio').hide();
        $('#col_local').hide();
        $('#col_btn_confirmar_locais').hide();
        $('#rateio_status').show();
        $('#habilitar_rateio').prop('checked', true); // garante que o flag está ativo
    }

    // ── Coloca ícone Selecionar Locais após o nome do primeiro Local da tabela ──
    function fixarIconeSelecLocais() {
        $('#tbl_rateio .ico-selec-locais').remove();
        var $td = $('#tbl_rateio tbody tr:first td:first');
        if ($td.length) {
            $td.append(
                '<a href="#" onclick="editarLocaisRateio();return false;" class="ico-selec-locais"' +
                ' data-toggle="tooltip" data-placement="top" title="Selecionar Locais"' +
                ' style="color:#337ab7;font-size:11px;margin-left:6px;">' +
                '<i class="fas fa-pen"></i></a>'
            );
            $td.find('.ico-selec-locais').tooltip();
        }
    }

    // Garante que o ícone de editar CC aparece apenas na primeira linha de cada local
    function _sincronizarIconesCC() {
        var seenLocalIds = {};
        $('#tbl_rateio tbody tr.linha-valor-rateio').each(function() {
            var $tr        = $(this);
            var localId    = String($tr.data('local-id'));
            var localNome  = String($tr.data('local-nome') || '');
            var localNomeJs = localNome.replace(/'/g, "\\'");
            var $ccTd      = $tr.find('td').eq(1);
            var $ccSpan    = $ccTd.find('span.lbl-parcela');
            $ccTd.find('a').remove();
            if ($ccSpan.length && !seenLocalIds[localId]) {
                $ccSpan.after(
                    '<a href="#" onclick="editarCCDoLocalFase3(\'' + localId + '\',\'' + localNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>'
                );
                seenLocalIds[localId] = true;
            }
        });
        $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
    }

    // ── Move botão Confirmar Conta para a última linha-fase2 ──
    function fixarConfirmarContaButton() {
        $('#tbl_rateio tbody tr.linha-fase2 .td-confirmar-conta').html('');
        $('#tbl_rateio tbody tr.linha-fase2:last .td-confirmar-conta').html(
            '<button type="button" class="btn btn-primary" onclick="confirmarTodaConta()">Confirmar</button>'
        );
    }

    var _replicarContaPendente = false;
    var _modoRateio = null; // null | 'valor' | 'perc'

    function _bindReplicarConta($s) {
        var _ultimaSelecao = null;
        $s.on('hidden.bs.select', function() {
            var vals = $(this).val();
            if (!vals || vals.length === 0) return;

            var valsKey = vals.slice().sort().join(',');
            if (valsKey === _ultimaSelecao) return;
            _ultimaSelecao = valsKey;

            var selfEl = this;
            _replicarContaPendente = true; // bloqueia confirmarTodaConta até o diálogo ser exibido
            setTimeout(function() {
                _replicarContaPendente = false;
                // Usa 'select.fase2-conta' para não contar os wrappers do bootstrap-select
                var $vazios = $('#tbl_rateio select.fase2-conta').not(selfEl).filter(function() {
                    var v = $(this).val();
                    return !v || v.length === 0;
                });
                if ($vazios.length === 0) return;

                var msg = 'Deseja replicar esta seleção para as ' + $vazios.length + ' linha(s) seguinte(s)?';
                if (!confirm(msg)) return;

                $vazios.each(function() {
                    $(this).val(vals).selectpicker('refresh');
                });

                var $aindaVazios = $('#tbl_rateio select.fase2-conta').filter(function() {
                    var v = $(this).val();
                    return !v || v.length === 0;
                });
                if ($aindaVazios.length === 0) {
                    confirmarTodaConta();
                }
            }, 0);
        });
    }

    function _setModoRateio(modo) {
        _modoRateio = modo;
        if (modo === 'valor') {
            $('.rat-valor').prop('readonly', false).css({'background': '', 'color': ''});
            $('.rat-perc').prop('readonly', true).css({'background': '#f9f9f9', 'color': '#555'});
        } else if (modo === 'perc') {
            $('.rat-valor').prop('readonly', true).css({'background': '#f9f9f9', 'color': '#555'});
            $('.rat-perc').prop('readonly', false).css({'background': '', 'color': ''});
        } else {
            $('.rat-valor').prop('readonly', false).css({'background': '', 'color': ''});
            $('.rat-perc').prop('readonly', false).css({'background': '', 'color': ''});
        }
    }

    function _temEditorAberto() {
        if ($('#tr_local_input').is(':visible')) return true;
        if ($('#tbl_rateio .tr-editar-cc, #tbl_rateio .tr-editar-conta, #tbl_rateio .tr-novo-local').length > 0) return true;
        return false;
    }

    // ── Reabre seleção de CC para um Local específico (fase 2) ──
    function editarCCDoLocal(localId, localNome) {
        if (_temEditorAberto()) return;
        var editorId = 'tr_editar_cc_' + localId;
        if ($('#' + editorId).length) return;

        var $linhasDoLocal = $('#tbl_rateio tbody tr.linha-fase2[data-local-id="' + localId + '"]');
        var ccIdsAtuais = [];
        $linhasDoLocal.each(function() { ccIdsAtuais.push(String($(this).data('cc-id'))); });

        var optionsCC = '';
        $.each(ccOpcoes, function(k, cc) {
            optionsCC += '<option value="' + cc.id + '">' + cc.nome + '</option>';
        });

        var selectId    = 'editar_cc_sel_' + localId;
        var localNomeJs = localNome.replace(/'/g,"\\'");
        var editorHtml  = '<tr id="' + editorId + '" class="tr-editar-cc"' +
            ' data-local-id="' + localId + '" data-local-nome="' + localNome.replace(/"/g,'&quot;') + '">' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + localNome + '</span></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker" id="' + selectId + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsCC + '</select></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;">' +
            '<button type="button" class="btn btn-primary" onclick="confirmarCCDoLocal(\'' + localId + '\',\'' + localNomeJs + '\')">Confirmar</button>' +
            ' <button type="button" class="btn btn-default" onclick="fecharEdicaoCCFase2(\'' + localId + '\')">Fechar</button></td>' +
            '<td colspan="3"></td></tr>';

        var $firstRow = $linhasDoLocal.first();
        if ($firstRow.length) { $firstRow.before(editorHtml); } else { $('#tbl_rateio tbody').append(editorHtml); }

        var $s = $('#' + selectId);
        $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
        $s.val(ccIdsAtuais);
        $s.selectpicker('refresh');
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '280px', 'width': 'auto' });
        $('#' + editorId).data('cc-ids-antes', ccIdsAtuais);
    }

    function fecharEdicaoCCFase2(localId) {
        var $s = $('#editar_cc_sel_' + localId);
        if ($s.length) $s.selectpicker('destroy');
        $('#tr_editar_cc_' + localId).remove();
        fixarConfirmarContaButton();
        fixarIconeSelecLocais();
    }

    // ── Confirma nova seleção de CC para um Local e reconstrói suas linhas ──
    function confirmarCCDoLocal(localId, localNome) {
        var selectId = 'editar_cc_sel_' + localId;
        var $select  = $('#' + selectId);
        var newCcIds = $select.val();
        if (!newCcIds || newCcIds.length === 0) { alert('Selecione pelo menos um Centro de Custos.'); return; }

        var $edRow     = $('#tr_editar_cc_' + localId);
        var ccIdsAntes = $edRow.data('cc-ids-antes') || [];
        var novosSorted = newCcIds.slice().sort();
        var antesSorted = ccIdsAntes.slice().sort();
        var semMudanca  = (novosSorted.length === antesSorted.length &&
                          novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
        if (semMudanca && ccIdsAntes.length > 0) {
            $select.selectpicker('destroy');
            $edRow.remove();
            fixarConfirmarContaButton();
            fixarIconeSelecLocais();
            return;
        }

        var $linhasDoLocal = $('#tbl_rateio tbody tr.linha-fase2[data-local-id="' + localId + '"]');
        var $insertBefore  = $linhasDoLocal.length > 0 ? $linhasDoLocal.last().next('tr') : $edRow.next('tr');
        $linhasDoLocal.each(function() {
            var $sp = $(this).find('.selectpicker');
            if ($sp.length) $sp.selectpicker('destroy');
        });
        $linhasDoLocal.remove();

        var optionsConta = '';
        $.each(contaOpcoes, function(k, c) { optionsConta += '<option value="' + c.id + '">' + c.nome + '</option>'; });

        var localNomeEsc = localNome.replace(/"/g,'&quot;');
        var localNomeJs  = localNome.replace(/'/g,"\\'");
        var newRowsHtml  = '';

        $.each(newCcIds, function(i, ccId) {
            var ccNome = ccId;
            $.each(ccOpcoes, function(k, cc) { if (String(cc.id) === String(ccId)) { ccNome = cc.nome; return false; } });
            var idxConta = 'conta_edit_' + localId + '_' + ccId;
            newRowsHtml += '<tr class="linha-fase2"' +
                ' data-local-id="' + localId + '" data-local-nome="' + localNomeEsc + '"' +
                ' data-cc-id="' + ccId + '" data-cc-nome="' + ccNome.replace(/"/g,'&quot;') + '">';
            if (i === 0) {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + localNome + '</span></td>';
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                               '<span class="lbl-parcela">' + ccNome + '</span>' +
                               ' <a href="#" onclick="editarCCDoLocal(\'' + localId + '\',\'' + localNomeJs + '\');return false;" data-toggle="tooltip" data-placement="top" title="Selecione Centro de Custos" style="color:#337ab7;font-size:11px;margin-left:4px;">' +
                               '<i class="fas fa-pen"></i></a></td>';
            } else {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;"></td>';
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + ccNome + '</span></td>';
            }
            newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker fase2-conta" id="' + idxConta + '" multiple data-live-search="true" data-size="8" data-width="100%">';
            newRowsHtml += '<option value="" disabled>...</option>' + optionsConta + '</select></td>';
            newRowsHtml += '<td class="td-confirmar-conta" style="vertical-align:middle;padding:4px 8px;"></td>';
            newRowsHtml += '<td colspan="2"></td></tr>';
        });

        $select.selectpicker('destroy');
        $edRow.remove();
        if ($insertBefore.length && $insertBefore.is('tr')) { $insertBefore.before(newRowsHtml); } else { $('#tbl_rateio tbody').append(newRowsHtml); }

        $.each(newCcIds, function(i, ccId) {
            var $s = $('#conta_edit_' + localId + '_' + ccId);
            $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
            var $bs = $s.closest('.bootstrap-select');
            $bs.css({ 'width': '100%', 'display': 'block' });
            $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
            $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
            _bindReplicarConta($s);
        });

        $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
        fixarConfirmarContaButton();
        fixarIconeSelecLocais();
    }

    // ── Abre seletor de CC para reeditar um local dentro da fase 3 ──
    function editarCCDoLocalFase3(localId, localNome) {
        if (_temEditorAberto()) return;
        var editorId = 'tr_editar_cc_f3_' + String(localId).replace(/\W/g,'_');
        if ($('#' + editorId).length) return;

        var $linhasDoLocal = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]');
        var ccIdsAtuais = [];
        $linhasDoLocal.each(function() {
            var cid = String($(this).data('cc-id'));
            if (ccIdsAtuais.indexOf(cid) === -1) ccIdsAtuais.push(cid);
        });

        var optionsCC = '';
        $.each(ccOpcoes, function(k, cc) {
            optionsCC += '<option value="' + cc.id + '">' + cc.nome + '</option>';
        });

        var selectId    = 'editar_cc_f3_sel_' + String(localId).replace(/\W/g,'_');
        var localNomeJs = localNome.replace(/'/g,"\\'");
        var editorHtml  = '<tr id="' + editorId + '" class="tr-editar-cc"' +
            ' data-local-id="' + localId + '" data-local-nome="' + localNome.replace(/"/g,'&quot;') + '">' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><span class="lbl-parcela">' + localNome + '</span></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;"><select class="selectpicker" id="' + selectId + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsCC + '</select></td>' +
            '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;">' +
            '<button type="button" class="btn btn-primary" onclick="confirmarCCDoLocalFase3(\'' + localId + '\',\'' + localNomeJs + '\')">Confirmar</button>' +
            ' <button type="button" class="btn btn-default" onclick="fecharEdicaoCC(\'' + localId + '\')">Fechar</button></td>' +
            '<td colspan="3"></td></tr>';

        var $firstRow = $linhasDoLocal.first();
        if ($firstRow.length) { $firstRow.before(editorHtml); } else { $('#tr_rateio_restante').before(editorHtml); }

        var $s = $('#' + selectId);
        $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
        $s.val(ccIdsAtuais);
        $s.selectpicker('refresh');
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '280px', 'width': 'auto' });
        $('#' + editorId).data('cc-ids-antes', ccIdsAtuais);
        fixarIconeSelecLocais();
    }

    // ── Confirma CC re-selecionado na fase 3 → preserva CCs existentes, abre editor só para CCs novos ──
    function confirmarCCDoLocalFase3(localId, localNome) {
        var selectId = 'editar_cc_f3_sel_' + String(localId).replace(/\W/g,'_');
        var ccIds = [];
        $('#' + selectId + ' option:selected').each(function() {
            if ($(this).val()) ccIds.push($(this).val());
        });
        if (ccIds.length === 0) { alert('Selecione pelo menos um Centro de Custos.'); return; }

        var $edRow     = $('#tr_editar_cc_f3_' + String(localId).replace(/\W/g,'_'));
        var ccIdsAntes = $edRow.data('cc-ids-antes') || [];

        var novosSorted = ccIds.slice().sort();
        var antesSorted = ccIdsAntes.slice().sort();
        var semMudanca  = (novosSorted.length === antesSorted.length &&
                          novosSorted.every(function(v, i) { return v === antesSorted[i]; }));
        if (semMudanca && ccIdsAntes.length > 0) {
            $edRow.remove();
            fixarIconeSelecLocais();
            return;
        }

        // CCs que foram adicionados e CCs que foram removidos
        var ccsNovos     = ccIds.filter(function(id) { return ccIdsAntes.indexOf(id) === -1; });
        var ccsRemovidos = ccIdsAntes.filter(function(id) { return ccIds.indexOf(id) === -1; });

        // Remove apenas as linhas dos CCs que foram desmarcados
        $.each(ccsRemovidos, function(i, ccId) {
            $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"][data-cc-id="' + ccId + '"]').remove();
        });

        // Ponto de inserção: após os rows existentes do local (que foram mantidos)
        var $linhasRestantes = $('#tbl_rateio tbody tr.linha-valor-rateio[data-local-id="' + localId + '"]');
        var $insertBefore    = $linhasRestantes.length > 0 ? $linhasRestantes.last().next('tr') : $edRow.next('tr');
        $edRow.remove();

        // Se não há CCs novos, apenas atualiza ícones e encerra
        if (ccsNovos.length === 0) {
            fixarIconeSelecLocais();
            _sincronizarIconesCC();
            return;
        }

        // Monta editor de conta apenas para os CCs novos
        var optionsConta = '';
        $.each(contaOpcoes, function(k, ct) {
            optionsConta += '<option value="' + ct.id + '">' + ct.nome + '</option>';
        });

        var localNomeJs    = localNome.replace(/'/g,"\\'");
        var temLinhasLocal = $linhasRestantes.length > 0;
        var newRowsHtml = '';
        $.each(ccsNovos, function(j, ccId) {
            var ccNome = '';
            $.each(ccOpcoes, function(k, cc) { if (String(cc.id) === String(ccId)) { ccNome = cc.nome; return false; } });
            var ccNomeJs = ccNome.replace(/'/g,"\\'");
            var gKey     = (localId + '_' + ccId).replace(/\W/g,'_');
            newRowsHtml += '<tr id="tr_editar_conta_' + gKey + '" class="tr-editar-conta"' +
                ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
                ' data-local-nome="' + localNome.replace(/"/g,'&quot;') + '" data-cc-nome="' + ccNome.replace(/"/g,'&quot;') + '">' +
                '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                (!temLinhasLocal && j === 0 ? '<span class="lbl-parcela">' + localNome + '</span>' : '') + '</td>' +
                '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                '<span class="lbl-parcela">' + ccNome + '</span></td>' +
                '<td style="vertical-align:middle;padding:4px 8px;">' +
                '<select class="selectpicker" id="editar_conta_sel_' + gKey + '" multiple data-live-search="true" data-size="8" data-width="100%">' + optionsConta + '</select></td>' +
                '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;" colspan="3">' +
                '<button type="button" class="btn btn-primary" onmousedown="confirmarContaDoCC(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\')">Confirmar</button>' +
                ' <button type="button" class="btn btn-default" onclick="fecharEdicaoConta(\'' + localId + '\',\'' + ccId + '\')">Fechar</button></td></tr>';
        });

        if ($insertBefore.length) { $insertBefore.before(newRowsHtml); } else { $('#tr_rateio_restante').before(newRowsHtml); }

        $.each(ccsNovos, function(j, ccId) {
            var gKey = (localId + '_' + ccId).replace(/\W/g,'_');
            var $s = $('#editar_conta_sel_' + gKey);
            $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
            var $bs = $s.closest('.bootstrap-select');
            $bs.css({ 'width': '100%', 'display': 'block' });
            $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
            $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });
            $('#tr_editar_conta_' + gKey).data('valores-atuais', {});
        });

        fixarIconeSelecLocais();
        _sincronizarIconesCC();
    }

    function editarLocaisRateio() {
        if (_temEditorAberto()) return;
        _locaisAntesEdicao = [];
        $('#tbl_rateio tbody tr.linha-fase1, #tbl_rateio tbody tr.linha-fase2, #tbl_rateio tbody tr.linha-valor-rateio, #tbl_rateio tbody tr.tr-novo-local').each(function() {
            var localId = String($(this).data('local-id'));
            if (localId && _locaisAntesEdicao.indexOf(localId) === -1) _locaisAntesEdicao.push(localId);
        });

        $('#tr_local_input').show();
        var $local = $('#codigo_fazenda');
        $local.val(_locaisAntesEdicao);
        $local.selectpicker('refresh');
        $('#btn_confirmar_locais').show();
        if (!$('#btn_fechar_local').length) {
            $('#td_local_confirm').append('<button type="button" id="btn_fechar_local" class="btn btn-default" style="margin-left:6px;" onclick="fecharEdicaoLocal()">Fechar</button>');
        }
    }

    function _executarRateioOff() {
        var $local = $('#codigo_fazenda');
        $('#col_cc').show();
        $('#col_conta').show();
        $local.off('changed.bs.select.rateio');
        if ($local.hasClass('selectpicker')) { $local.selectpicker('destroy'); }
        $local.removeAttr('multiple').removeAttr('data-live-search').removeAttr('data-size')
              .removeClass('selectpicker').addClass('form-control');
        $local.val('');
        $('#col_local label').after($local);
        $('#btn_fechar_local').remove();
        $('#col_btn_confirmar_locais').append($('#btn_confirmar_locais'));
        $('#tr_local_input').show();
        $('#col_btn_confirmar_locais').hide();
        $('#secao_distribuir_rateio').hide();
        $('#linhas_rateio').hide().empty();
        $('#rodape_rateio').remove();
        $('#rateio_status').hide();
        $('#col_local').show();
        $('#rateio_json').val('');
        RT.reset();
    }

    function confirmarFecharRateio() {
        $('#modal_fechar_rateio').modal('hide');
        $('#habilitar_rateio').prop('checked', false);
        _executarRateioOff();
    }

    function fecharEdicaoLocal() {
        $('#btn_fechar_local').remove();
        $('#tr_local_input').hide();
    }

    // ── Insere editor inline para novo local adicionado em Phase 3 ──
    function _adicionarNovoLocalFase3(localId, localNome) {
        var safeId = String(localId).replace(/\W/g,'_');
        if ($('#tr_novo_local_' + safeId).length) return;

        var primeiroCCId   = ccOpcoes.length > 0 ? String(ccOpcoes[0].id)   : '';
        var primeiroCCNome = ccOpcoes.length > 0 ? String(ccOpcoes[0].nome) : '';

        var optionsConta = '';
        $.each(contaOpcoes, function(k, cta) {
            if (cta.nivel === 1)      optionsConta += '<option value="' + cta.id + '" disabled style="color:#777;font-weight:600;">' + cta.nome + '</option>';
            else if (cta.nivel === 2) optionsConta += '<option value="' + cta.id + '" disabled style="color:#888;">&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
            else                      optionsConta += '<option value="' + cta.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + cta.nome + '</option>';
        });

        var optionsCC = '';
        $.each(ccOpcoes, function(k, cc) {
            optionsCC += '<option value="' + cc.id + '"' + (String(cc.id) === primeiroCCId ? ' selected' : '') + '>' + cc.nome + '</option>';
        });

        var localNomeJs  = localNome.replace(/'/g,"\\'");
        var localNomeEsc = localNome.replace(/"/g,'&quot;');
        var ccNomeEsc    = primeiroCCNome.replace(/"/g,'&quot;');

        var html = '<tr id="tr_novo_local_' + safeId + '" class="tr-novo-local"' +
            ' data-local-id="' + localId + '" data-local-nome="' + localNomeEsc + '"' +
            ' data-cc-id="' + primeiroCCId + '" data-cc-nome="' + ccNomeEsc + '">' +
            '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
            '<span class="lbl-parcela">' + localNome + '</span>' +
            '</td>' +
            '<td style="vertical-align:top;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
            '<span class="cc-nome-nv lbl-parcela">' + primeiroCCNome + '</span>' +
            ' <a href="#" onclick="_editarCCNovoLocal(\'' + localId + '\');return false;"' +
            ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
            ' style="color:#337ab7;font-size:11px;margin-left:4px;" class="ico-editar-cc-nv"><i class="fas fa-pen"></i></a>' +
            '<div class="cc-editor-nv" style="display:none;margin-top:4px;">' +
            '<select class="form-control cc-select-nv" style="height:30px;font-size:13px;">' + optionsCC + '</select>' +
            '<div style="margin-top:4px;">' +
            '<button type="button" class="btn btn-primary btn-xs" onclick="_confirmarCCNovoLocal(\'' + localId + '\')">OK</button>' +
            ' <button type="button" class="btn btn-default btn-xs" onclick="_fecharCCNovoLocal(\'' + localId + '\')">Fechar</button>' +
            '</div></div>' +
            '</td>' +
            '<td style="vertical-align:middle;padding:4px 8px;">' +
            '<select class="selectpicker conta-sel-nv" id="conta_nv_' + safeId + '" multiple data-live-search="true" data-size="8" data-width="100%">' +
            '<option value="" disabled>...</option>' + optionsConta + '</select>' +
            '</td>' +
            '<td style="vertical-align:middle;padding:4px 8px;white-space:nowrap;" colspan="3">' +
            '<button type="button" class="btn btn-primary" onclick="confirmarNovoLocalFase3(\'' + localId + '\')">Confirmar</button>' +
            '</td>' +
            '</tr>';

        $('#tr_rateio_restante').before(html);

        var $s = $('#conta_nv_' + safeId);
        $s.selectpicker({ actionsBox: false, noneSelectedText: '...', selectedTextFormat: 'values' });
        var $bs = $s.closest('.bootstrap-select');
        $bs.css({ 'width': '100%', 'display': 'block' });
        $bs.find('button.dropdown-toggle').css({ 'height': '30px', 'font-size': '13px', 'padding': '4px 8px', 'width': '100%', 'overflow': 'hidden', 'text-overflow': 'ellipsis', 'white-space': 'nowrap' });
        $bs.find('.dropdown-menu').css({ 'min-width': '360px', 'width': 'auto' });

        $('#tr_novo_local_' + safeId + ' [data-toggle="tooltip"]').tooltip();
    }

    function _editarCCNovoLocal(localId) {
        var safeId = String(localId).replace(/\W/g,'_');
        var $row = $('#tr_novo_local_' + safeId);
        $row.find('.cc-nome-nv').hide();
        $row.find('.ico-editar-cc-nv').hide();
        $row.find('.cc-editor-nv').show();
    }

    function _fecharCCNovoLocal(localId) {
        var safeId = String(localId).replace(/\W/g,'_');
        var $row = $('#tr_novo_local_' + safeId);
        $row.find('.cc-editor-nv').hide();
        $row.find('.cc-nome-nv').show();
        $row.find('.ico-editar-cc-nv').show();
    }

    function _confirmarCCNovoLocal(localId) {
        var safeId = String(localId).replace(/\W/g,'_');
        var $row = $('#tr_novo_local_' + safeId);
        var $sel = $row.find('.cc-select-nv');
        var ccId   = $sel.val();
        var ccNome = $sel.find('option:selected').text();
        $row.attr('data-cc-id', ccId).attr('data-cc-nome', ccNome.replace(/"/g,'&quot;'));
        $row.find('.cc-nome-nv').text(ccNome);
        _fecharCCNovoLocal(localId);
    }

    function confirmarNovoLocalFase3(localId) {
        var safeId = String(localId).replace(/\W/g,'_');
        var $row = $('#tr_novo_local_' + safeId);

        var localNome = String($row.attr('data-local-nome') || '');
        var ccId      = String($row.attr('data-cc-id')    || '');
        var ccNome    = String($row.attr('data-cc-nome')  || '');

        var contaIds = [];
        $('#conta_nv_' + safeId + ' option:selected').each(function() {
            if ($(this).val()) contaIds.push($(this).val());
        });
        if (contaIds.length === 0) { alert('Selecione pelo menos uma Conta Contábil.'); return; }

        $('#conta_nv_' + safeId).selectpicker('destroy');

        var localNomeEsc = localNome.replace(/"/g,'&quot;');
        var ccNomeEsc    = ccNome.replace(/"/g,'&quot;');
        var localNomeJs  = localNome.replace(/'/g,"\\'");
        var ccNomeJs     = ccNome.replace(/'/g,"\\'");

        var newRowsHtml = '';
        $.each(contaIds, function(i, contaId) {
            var contaNome = '';
            $.each(contaOpcoes, function(m, ct) { if (String(ct.id) === String(contaId)) { contaNome = ct.nome; return false; } });
            var isFirst = (i === 0);

            newRowsHtml += '<tr class="linha-valor-rateio"' +
                ' data-local-id="' + localId + '" data-cc-id="' + ccId + '"' +
                ' data-conta-id="' + contaId + '"' +
                ' data-local-nome="' + localNomeEsc + '" data-cc-nome="' + ccNomeEsc + '"' +
                ' data-conta-nome="' + contaNome.replace(/"/g,'&quot;') + '">';

            if (isFirst) {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + localNome + '</span>' +
                    '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                    '</td>';
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + ccNome + '</span>' +
                    ' <a href="#" onclick="editarCCDoLocalFase3(\'' + localId + '\',\'' + localNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Centro de Custos"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                    '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                    '</td>';
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + contaNome + '</span>' +
                    ' <a href="#" onclick="editarContaDoCC(\'' + localId + '\',\'' + ccId + '\',\'' + localNomeJs + '\',\'' + ccNomeJs + '\');return false;"' +
                    ' data-toggle="tooltip" data-placement="top" title="Selecionar Contas"' +
                    ' style="color:#337ab7;font-size:11px;margin-left:4px;"><i class="fas fa-pen"></i></a>' +
                    '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                    '</td>';
            } else {
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                    '<input type="hidden" name="rat2_local_id[]" value="' + localId + '">' +
                    '<input type="hidden" name="rat2_local_nome[]" value="' + localNome + '">' +
                    '</td>';
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;">' +
                    '<input type="hidden" name="rat2_cc_id[]" value="' + ccId + '">' +
                    '<input type="hidden" name="rat2_cc_nome[]" value="' + ccNome + '">' +
                    '</td>';
                newRowsHtml += '<td style="vertical-align:middle;padding:4px 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' +
                    '<span class="lbl-parcela">' + contaNome + '</span>' +
                    '<input type="hidden" name="rat2_conta_id[]" value="' + contaId + '">' +
                    '<input type="hidden" name="rat2_conta_nome[]" value="' + contaNome + '">' +
                    '</td>';
            }

            newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
                '<input type="text" class="form-control rat-valor" placeholder="0,00" name="rat2_valor[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
            newRowsHtml += '<td style="vertical-align:middle;text-align:right;padding:4px 8px;">' +
                '<input type="text" class="form-control rat-perc" placeholder="0,00%" name="rat2_perc[]"' +
                ' style="height:30px;font-size:13px;text-align:right;"></td>';
            newRowsHtml += '<td></td></tr>';
        });

        $row.before(newRowsHtml);
        $row.remove();

        $('#tbl_rateio [data-toggle="tooltip"]').tooltip();
        fixarIconeSelecLocais();
        _sincronizarIconesCC();
        recalcularRateio();
    }

    function fecharEdicaoCC(localId) {
        $('#tr_editar_cc_f3_' + String(localId).replace(/\W/g,'_')).remove();
        fixarIconeSelecLocais();
    }

    function fecharEdicaoConta(localId, ccId) {
        var gKey = (localId + '_' + ccId).replace(/\W/g,'_');
        $('#tr_editar_conta_' + gKey).remove();
        fixarIconeSelecLocais();
    }

    // ── Reabre a configuração do rateio para edição ──
    function editarRateio() {
        $('#rateio_status').hide();
        $('#col_local').show();
        $('#secao_distribuir_rateio').show();
        // Reabilita o botão Confirmar Rateio
        $('#btn_confirmar_rateio_final')
            .removeClass('btn-default').addClass('btn-success')
            .text('Confirmar Rateio').prop('disabled', false);
    }

    function voltarRateio() {
        if ($('#rateio_json').val()) {
            $('#secao_distribuir_rateio').hide();
            $('#col_local').hide();
            $('#rateio_status').show();
        } else {
            $('#habilitar_rateio').prop('checked', false);
            _executarRateioOff();
        }
    }

    (function () {
        // Aguarda o DOM estar pronto para garantir que contas_pagar.js já definiu gravar_conta
        window.gravar_conta = function () {

            // ── Coleta dados do modal de rateio por fazenda (igual ao original) ──
            var array_fazendas_arr = [];
            var grupo_itens        = '';
            var total_percentual   = 0;

            var vlr_pp = $('#vlr_primeira_parcela').val();
            if (typeof verifica_virgula === 'function' && verifica_virgula(vlr_pp) === ',') {
                vlr_pp = replace_valor(vlr_pp);
            }
            vlr_pp = parseFloat(vlr_pp) || 0;

            var ocorrencias   = $('#qtd_parcelas').val();
            var tipo_inclusao = $("input[name='tipo_inclusao']:checked").val();
            var parc_restantes = 0;

            if (tipo_inclusao === 'F') {
                parc_restantes = $('#vlr_parcela_fixa').val();
                if (typeof verifica_virgula === 'function' && verifica_virgula(parc_restantes) === ',') {
                    parc_restantes = replace_valor(parc_restantes);
                }
            } else if (tipo_inclusao === 'P') {
                var vlr_compra = $('#vlr_compra').val();
                if (typeof verifica_virgula === 'function' && verifica_virgula(vlr_compra) === ',') {
                    vlr_compra = replace_valor(vlr_compra);
                }
                parc_restantes = (parseFloat(vlr_compra) - vlr_pp) / parseFloat(ocorrencias);
            }

            var total_pp   = 0;
            var total_parc = 0;

            $('#tabela_fazendas tbody tr').each(function () {
                var codigo     = $(this).find('.codigo_id').html();
                var percentual = $(this).find('.percentual').val();
                var pp         = $(this).find('.primeira_parcela').val();
                var pr         = $(this).find('.parcela_restante').val();

                if (typeof verifica_virgula === 'function') {
                    if (verifica_virgula(pp) === ',') pp = replace_valor(pp);
                    if (verifica_virgula(pr) === ',') pr = replace_valor(pr);
                }

                if (percentual !== '') total_percentual += parseFloat(percentual);
                total_pp   += parseFloat(pp)  || 0;
                total_parc += parseFloat(pr)  || 0;

                if (codigo !== undefined && codigo != 0) {
                    array_fazendas_arr.push([codigo, percentual, pp, pr].join('|'));
                    grupo_itens = array_fazendas_arr.join('<|>');
                }
            });

            if (total_percentual !== 100 && total_percentual !== 0) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('Total do Percentual das Fazendas inválido.');
                return;
            }
            if (total_pp !== vlr_pp && total_pp !== 0 && total_percentual === 0) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('Total da Primeira Parcela das Fazendas inválido.');
                return;
            }
            if (total_parc !== parc_restantes && total_parc !== 0 && total_percentual === 0) {
                $('#mensagem_erro').modal();
                $('#mensagem_erro .modal-body').html('Total do Valor das Parcelas das Fazendas inválido.');
                return;
            }

            // Grava array_fazendas no input hidden antes de montar o FormData
            $('#array_fazendas').val(grupo_itens);

            // ── FormData captura TODOS os campos inclusive inputs file ──
            var formData = new FormData(document.getElementById('form_gravar_contas_pagar'));

            $(".confirmar_gravar").attr("disabled", true);

            $.ajax({
                type        : 'POST',
                url         : 'gravar_contas_pagar.php',
                data        : formData,
                processData : false,   // NÃO deixa o jQuery serializar
                contentType : false,   // NÃO define Content-Type (browser coloca multipart/form-data)
                success: function (data) {
                    $(".confirmar_gravar").attr("disabled", false);
                    if (data.error) {
                        $('#mensagem_erro').modal();
                        $('#mensagem_erro .modal-body').html(data.message);
                    } else {
                        _limparDirty();
                        $('#mensagem_retorno').modal();
                        $('#mensagem_retorno .modal-body').html(data.message);
                    }
                },
                error: function (xhr) {
                    $(".confirmar_gravar").attr("disabled", false);
                    $('#mensagem_erro').modal();
                    $('#mensagem_erro .modal-body').html('Erro na requisição: ' + xhr.status);
                }
            });
        };
    })();
    </script>
</body>
</html>
