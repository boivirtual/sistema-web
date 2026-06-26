<?php
// Contas a Pagar
include "valida_sessao.inc";
include "conecta_mysql.inc";

function diferenca_data($data_validade) {
    $data_inicial = $data_sistema = date("Y-m-d H:i:s");;
    $data_final = $data_validade;
    $time_inicial = strtotime($data_inicial);
    $time_final = strtotime($data_final);
    $diferenca = $time_final - $time_inicial;
    $dias = (int)floor($diferenca / (60 * 60 * 24));
    return $dias;
}

$conta = mysqli_query($conector, "select * from tbl_plano_contas
        where tbl_plano_contas_lixeira=0 and
              tbl_plano_contas_debito_credito='D'
        order by tbl_plano_contas_codigo_id ASC");

$conta_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0");

$conta_pagamento_individual = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0");

$tipos_documentos_baixar = mysqli_query($conector, "select tbl_tipo_doc_id, tbl_tipo_doc_descricao from tbl_tipo_documento where tbl_tipo_doc_lixeira=0 order by tbl_tipo_doc_descricao ASC");

$c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

$fornecedor = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=3 or tbl_pessoa_classe=5) order by tbl_pessoa_nome ASC");

$tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

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

$data_sistema = date("Y-m-d");

$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario 
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
          width: 100% !important;
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
            width: 40%;       
        }

        /* Estilo para navegação de período */
        #btnMesAno {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            color: #333;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #btnMesAno:hover {
            background-color: #efefef;
        }

        .btn-group .btn-default {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            color: #777;
            transition: all 0.2s ease;
        }

        .btn-group .btn-default:hover {
            background-color: #efefef;
            color: #555;
        }

        /* Selectpicker dentro do modal: dropdown visível acima dos outros elementos */
        #modal_seletor_periodo .bootstrap-select .dropdown-menu {
            z-index: 9999 !important;
        }

        /* Botões do seletor de período sem bordas */
        #modal_seletor_periodo .list-group-item {
            border: none !important;
            border-radius: 4px !important;
            margin-bottom: 2px !important;
        }
        #modal_seletor_periodo .list-group {
            border: none !important;
            box-shadow: none !important;
        }

        /* Remove borda do topo do thead do DataTable (entre os cards e as colunas) */
        table#tabela_contas_pagar { border-top: none !important; }
        table#tabela_contas_pagar thead > tr:first-child > th,
        table#tabela_contas_pagar thead > tr:first-child > td { border-top: none !important; }

        /* Garante que o header fixo fique sempre acima do conteúdo da página */
        .header { z-index: 1030 !important; }

        /* Destaque temporário na linha após retorno da edição */
        .ctp-destaque {
            background-color: #fffde7 !important;
            transition: background-color 1.5s ease;
        }
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
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';
        echo '</div>';
        exit;
    }

    // Reset ao entrar pelo menu (parâmetro reset=1 adicionado no link do menu)
    if (isset($_GET['reset']) && $_GET['reset'] == '1') {
        $_SESSION['data_inicio_ctp']    = '';
        $_SESSION['data_fim_ctp']       = '';
        $_SESSION['periodo_label_ctp']  = '';
        $_SESSION['razao_nome_ctp']     = '';
        $_SESSION['codigo_c_custo_ctp'] = '';
        $_SESSION['codigo_local_ctp']   = '';
        $_SESSION['codigo_conta_ctp']   = '';
        $_SESSION['tipo_data_ctp']      = '';
    }

    $stored_inicio = $_SESSION['data_inicio_ctp'] ?? null;
    $stored_fim    = $_SESSION['data_fim_ctp']    ?? null;

    // Sem sessão ou start==end (padrão antigo que salvava só o dia atual): usa mês inteiro
    if (!$stored_inicio || !$stored_fim || $stored_inicio === $stored_fim) {
        $data_inicial = date('Y-m-01');
        $data_final   = date('Y-m-t');
    } else {
        $data_inicial = $stored_inicio;
        $data_final   = $stored_fim;
    }

    if (empty($_SESSION['tipo_data_ctp'])) {
        $tipo_data = "V";
    } else {
        $tipo_data = $_SESSION['tipo_data_ctp'];
    }

    $razao_nome         = $_SESSION['razao_nome_ctp']     ?? '';
    $codigo_c_custo     = $_SESSION['codigo_c_custo_ctp'] ?? '';
    $local              = $_SESSION['codigo_local_ctp']   ?? '';
    $contas             = $_SESSION['codigo_conta_ctp']   ?? '';
    $periodo_label_sessao = $_SESSION['periodo_label_ctp'] ?? '';

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
                            <a href="form_contas_pagar_incluir.php">
                                <input type="button" class="btn btn-primary" aria-label="Left Align" value="Incluir Nova" />
                            </a>

                            <?php

                            @session_start();
                            if (isset($_SESSION['menu_gestao_adm'])) {
                                $array_gestao_adm = explode("!", $_SESSION['menu_gestao_adm']);

                                if ($array_gestao_adm[2] == 1) {
                                    echo '<a href="form_contas_pagar_aceite.php">';
                                    echo '<input type="button" class="btn btn-info pull-right" aria-label="Left Align" 
                                          value="Aceite de Contas"/>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>

                        <div class="row col-md-12" id="consulta_contas">
                            <form method="GET" action="form_contas_pagar.php" enctype="multipart/form-data" id="form_consulta_contas">

                                <div class="tab-panel">
                                    <div class="tab-pane active">
                                        <input id="lista_ctp_automatico" type="hidden" <?php echo "value='" . $_SESSION['lista_ctp'] . "'"; ?>>

                                        <input id="limpar_filtro_contas" type="hidden" <?php echo "value='" . $_SESSION['limpa_conta_ctp'] . "'"; ?>>

                                        <input id="exibe_local" type="hidden" <?php echo "value='".$local."'"; ?>>
                                        <input id="exibe_cc" type="hidden" <?php echo "value='".$codigo_c_custo."'"; ?>>
                                        <input id="exibe_fornecedor" type="hidden" <?php echo "value='".$razao_nome."'"; ?>>
                                        <input id="exibe_conta" type="hidden" <?php echo "value='".$contas."'"; ?>>

                                        <input type="hidden" id="data_inicial" name="data_inicial" <?php echo "value='" . $data_inicial . "'"; ?>>
                                        <input type="hidden" id="data_final" name="data_final" <?php echo "value='" . $data_final . "'"; ?>>
                                        <input type="hidden" id="tipo_data" name="tipo_data" value="V">
                                        <input type="hidden" id="periodo_label" value="">
                                        <input type="hidden" id="periodo_label_sessao" value="<?php echo htmlspecialchars($periodo_label_sessao); ?>">

                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend">Consultar Contas a Pagar</legend>

                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label class="control-label">Período</label>
                                                    <div style="width: 100%; display: flex; gap: 1px;">
                                                        <button type="button" class="btn btn-default btn-seta-periodo" style="width: 34px; height: 34px; padding: 6px 8px; flex-shrink: 0; border-radius: 4px; background-color: #e0e0e0;" onclick="navegarMesAnterior()"><i class="fas fa-chevron-left"></i></button>
                                                        <button type="button" class="btn btn-default" id="btnMesAno" style="flex: 1; text-align: center; height: 34px; border-radius: 4px; color: #555; font-weight: 400; cursor: default;">Maio 2026</button>
                                                        <button type="button" class="btn btn-default btn-seta-periodo" style="width: 34px; height: 34px; padding: 6px 8px; flex-shrink: 0; border-radius: 4px; background-color: #e0e0e0;" onclick="navegarMesProximo()"><i class="fas fa-chevron-right"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="codigo_fazenda" class="control-label">Local</label>
                                                    <select class="form-control selectpicker" id="codigo_fazenda" multiple data-live-search="true" name="codigo_fazenda">
                                                        <option value="">Todos</option>
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

                                                <div class="form-group col-md-3">
                                                    <label for="razao_nome" class="control-label">Fornecedor</label>
                                                    <select class="form-control selectpicker" multiple data-live-search="true" name="razao_nome" id="razao_nome" data-size="6">
                                                        <option value="">Todos</option>
                                                        <?php
                                                        while ($reg_for = mysqli_fetch_object($fornecedor)) {
                                                            echo '<option value="' . $reg_for->tbl_pessoa_id . '">' . $reg_for->tbl_pessoa_nome . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div style="display: flex; gap: 6px;">
                                                        <button type="button" class="form-control btn btn-info" onclick="abrirSeletorData()"
                                                        data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
                                                        <button type="button" id="btn_consultar_filtro" class="form-control btn btn-primary" onclick="consultar_ctp()" style="display:none;">
                                                            Consultar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row filtros" hidden>
                                                <div class="col-md-12">
                                                    <p style="font-size: 12px; color: #829c9c">Filtros:
                                                        <span class="descricao_filtro" style="font-weight: normal;">
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="row filtros" hidden>
                                                <div class="col-md-12">
                                                    <a href="#" id="link_limpar_filtros" onclick="limparFiltrosModal(); return false;" style="font-size: 0.9em; font-weight: 500; color: #128cb8; float: left; display: none;">Limpar Filtros</a>
                                                    <a href="#" style="font-size: 0.9em; font-weight: 500; text-align: right; color: #128cb8; float: right;" onclick="mais_relatorios()" data-toggle='tooltip' data-placement='top' title="Análise de Pagamentos" class="pull-right"><i class="fa fa-plus"></i> Relatórios</a>
                                                </div>
                                            </div> 
                                        </fieldset>

                                        <div id="lista_contas_pagar"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- page end-->
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

                <!-- modal seletor de período -->
                <div class="modal fade" id="modal_seletor_periodo" tabindex="-1" role="dialog" aria-labelledby="modalSeletorPeriodoLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog" role="document" style="width: 780px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="modalSeletorPeriodoLabel">Mais Filtros</h4>
                            </div>

                            <div class="modal-body">
                                <!-- Linha 1: Períodos Rápidos | Período Customizado -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5 style="margin-bottom: 15px; font-weight: 600;">Períodos Rápidos</h5>
                                        <div class="list-group">
                                            <label class="list-group-item" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                <input type="radio" name="periodo_rapido" value="hoje"> &nbsp; Hoje
                                            </label>
                                            <label class="list-group-item" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                <input type="radio" name="periodo_rapido" value="semana"> &nbsp; Esta Semana
                                            </label>
                                            <label class="list-group-item" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                <input type="radio" name="periodo_rapido" value="mes"> &nbsp; Este Mês
                                            </label>
                                            <label class="list-group-item" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                <input type="radio" name="periodo_rapido" value="mes_passado"> &nbsp; Mês Passado
                                            </label>
                                            <label class="list-group-item" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                <input type="radio" name="periodo_rapido" value="30dias"> &nbsp; Últimos 30 Dias
                                            </label>
                                            <label class="list-group-item" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                <input type="radio" name="periodo_rapido" value="trimestre"> &nbsp; Este Trimestre
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <h5 style="margin-bottom: 15px; font-weight: 600;">Período Customizado</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="data_inicio_custom" class="control-label" style="font-size: 12px;">Data Inicial</label>
                                                    <input type="date" class="form-control" id="data_inicio_custom">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="data_fim_custom" class="control-label" style="font-size: 12px;">Data Final</label>
                                                    <input type="date" class="form-control" id="data_fim_custom">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Linha 2: Outros Filtros abaixo das datas -->
                                        <h5 style="margin-top: 5px; margin-bottom: 15px; font-weight: 600;">Outros Filtros</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="codigo_cc" class="control-label" style="font-size: 12px;">Centro de Custo</label>
                                                    <select class="form-control selectpicker" id="codigo_cc" name="codigo_cc" multiple data-live-search="true" data-size="6">
                                                        <?php while ($registo_cc = mysqli_fetch_object($c_custo)) { ?>
                                                        <option value="<?php echo $registo_cc->tbl_cc_codigo_id ?>">
                                                            <?php echo $registo_cc->tbl_cc_descricao; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="contas_selecionadas" class="control-label" style="font-size: 12px;">Conta Contábil</label>
                                                    <input type="text" name="contas_selecionadas" id="contas_selecionadas" class="form-control" value="Todas ou (Clique p/ selecionar contas)" style="cursor: pointer;" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="aplicarSelecaoPeriodo()">Aplicar</button>
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
                                        <div class="form-group col-md-5">
                                            <label for="tipo_doc_baixar" class="control-label">* Tipo Documento</label>
                                            <select class="form-control" id="tipo_doc_baixar" name="tipo_doc_baixar">
                                                <option value="">...</option>
                                                <?php while ($reg_tipo_doc = mysqli_fetch_object($tipos_documentos_baixar)) { ?>
                                                    <option value="<?php echo $reg_tipo_doc->tbl_tipo_doc_id; ?>">
                                                        <?php echo $reg_tipo_doc->tbl_tipo_doc_descricao; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="number_doc_baixar" class="control-label">Nº Documento</label>
                                            <input name="number_doc_baixar" type="number" class="form-control" id="number_doc_baixar" data-toggle='tooltip' data-placement='top' title="Obrigatório, exceto para Recibo (gerado automaticamente)">
                                            <input name="chave_ind" type="hidden" class="form-control" id="chave_ind" readonly="">
                                        </div>

                                        <div class="form-group col-md-3">
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
                    include "modal_anexos.php";
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

</section> <!-- container section start end -->
  
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

<script src="js/contas_pagar.js?<?php echo filemtime(__DIR__.'/js/contas_pagar.js'); ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao; ?>"></script>

<script>
    let dataSelecionada = new Date();

    // Inicializar com mês/ano atual e carregar listagem
    $(document).ready(function() {
        // Preserva datas da sessão antes que atualizarMesAno() as substitua
        var dataIniSessao = $('#data_inicial').val();
        var dataFimSessao = $('#data_final').val();

        atualizarMesAno(); // define dataSelecionada e sobrescreve #data_inicial/#data_final

        // Restaura o período da sessão se não for exatamente o mês atual
        if (dataIniSessao && dataFimSessao) {
            var dIni   = parseDateLocal(dataIniSessao);
            var dFim   = parseDateLocal(dataFimSessao);
            var primDia = new Date(dIni.getFullYear(), dIni.getMonth(), 1);
            var ultDia  = new Date(dFim.getFullYear(), dFim.getMonth() + 1, 0);
            var ehMesUnico = (dIni.getTime() === primDia.getTime()) &&
                             (dFim.getTime() === ultDia.getTime()) &&
                             (dIni.getMonth() === dFim.getMonth()) &&
                             (dIni.getFullYear() === dFim.getFullYear());

            if (ehMesUnico) {
                // Período é um mês completo: navega para ele com setas ativas
                dataSelecionada = new Date(dIni);
                atualizarMesAno();
            } else {
                // Período não é mês único: restaura datas e usa o label salvo na sessão
                var labelSalvo = $('#periodo_label_sessao').val();
                $('#data_inicial').val(dataIniSessao);
                $('#data_final').val(dataFimSessao);
                if (labelSalvo) {
                    $('#periodo_label').val(labelSalvo);
                    setModoNavegacao(labelSalvo);
                    var radioMap = {
                        'Hoje': 'hoje', 'Esta Semana': 'semana',
                        'Últimos 30 Dias': '30dias', 'Este Trimestre': 'trimestre'
                    };
                    var radioKey = radioMap[labelSalvo];
                    if (radioKey) {
                        ctpFiltroModal.radio = radioKey;
                    } else {
                        ctpFiltroModal.dataInicio = dataIniSessao;
                        ctpFiltroModal.dataFim    = dataFimSessao;
                    }
                } else {
                    setModoNavegacao('Período Customizado');
                    ctpFiltroModal.dataInicio = dataIniSessao;
                    ctpFiltroModal.dataFim    = dataFimSessao;
                }
            }
        }

        // Restaura selectpickers ANTES de consultar_ctp() porque $(window).load roda depois do document.ready
        var _exLoc = $('#exibe_local').val();
        if (_exLoc) {
            _exLoc.split(',').filter(function(v){ return v !== ''; }).forEach(function(v) {
                $('#codigo_fazenda option[value="' + v + '"]').attr('selected', true);
            });
            $('#codigo_fazenda').selectpicker('refresh');
        }
        var _exFor = $('#exibe_fornecedor').val();
        if (_exFor) {
            _exFor.split(',').filter(function(v){ return v !== ''; }).forEach(function(v) {
                $('#razao_nome option[value="' + v + '"]').attr('selected', true);
            });
            $('#razao_nome').selectpicker('refresh');
        }
        var _exCC = $('#exibe_cc').val();
        if (_exCC) {
            _exCC.split(',').filter(function(v){ return v !== ''; }).forEach(function(v) {
                $('#codigo_cc option[value="' + v + '"]').attr('selected', true);
            });
            $('#codigo_cc').selectpicker('refresh');
        }

        $('[data-toggle="tooltip"]').tooltip();
        // Se há filtro de Conta Contábil, o $(window).load carrega as opções e chama consultar_ctp()
        // Chamar aqui também causaria duplo modal #aguardar (backdrop preso na tela)
        var _exConta = $('#exibe_conta').val();
        var _limpaContaFiltro = $('#limpar_filtro_contas').val();
        if (!_exConta || _limpaContaFiltro === 'S') {
            consultar_ctp();
        }
        atualizarLinkLimparFiltros();

        // Exibir botão Consultar e ocultar listagem ao alterar Local ou Fornecedor
        $('#codigo_fazenda, #razao_nome').on('changed.bs.select', function() {
            $('#btn_consultar_filtro').show();
            $('#lista_contas_pagar').hide();
            atualizarLinkLimparFiltros();
        });

        // Inicializa selectpicker do CC e restaura seleções ao abrir o modal
        $('#modal_seletor_periodo').on('shown.bs.modal', function() {
            $('#codigo_cc').selectpicker('refresh');

            // Restaura radio button
            if (ctpFiltroModal.radio) {
                $('input[name="periodo_rapido"][value="' + ctpFiltroModal.radio + '"]').prop('checked', true);
            }
            // Restaura datas customizadas
            if (ctpFiltroModal.dataInicio) {
                $('#data_inicio_custom').val(ctpFiltroModal.dataInicio);
            }
            if (ctpFiltroModal.dataFim) {
                $('#data_fim_custom').val(ctpFiltroModal.dataFim);
            }
        });

        // Ao digitar nas datas customizadas, limpa o radio selecionado
        $(document).on('change', '#data_inicio_custom, #data_fim_custom', function() {
            $('input[name="periodo_rapido"]').prop('checked', false);
            ctpFiltroModal.radio = null;
        });

        // Traz #modal_conta para frente quando aberto sobre outro modal
        $('#modal_conta').on('show.bs.modal', function() {
            $(this).css('z-index', 1100);
        });
        $('#modal_conta').on('shown.bs.modal', function() {
            $('.modal-backdrop').not(':first').css('z-index', 1090);
        });
    });

    // Guarda o último estado dos filtros do modal para restaurar ao reabrir
    var ctpFiltroModal = { radio: null, dataInicio: null, dataFim: null };

    // Verifica se há filtros ativos e exibe/oculta o link "Limpar Filtros"
    function atualizarLinkLimparFiltros() {
        var temCC       = $('#codigo_cc').val() && $('#codigo_cc').val().length > 0;
        var temConta    = $('#contas_selecionadas').val() !== 'Todas ou (Clique p/ selecionar contas)';
        var temPeriodo  = ctpFiltroModal.radio !== null || ctpFiltroModal.dataInicio !== null;
        var temLocal    = $('#codigo_fazenda').val() && $('#codigo_fazenda').val().length > 0;
        var temFornec   = $('#razao_nome').val() && $('#razao_nome').val().length > 0;

        if (temCC || temConta || temPeriodo || temLocal || temFornec) {
            $('#link_limpar_filtros').show();
        } else {
            $('#link_limpar_filtros').hide();
        }
    }

    // Limpa todos os filtros e volta ao mês atual
    function limparFiltrosModal() {
        // Limpa Local e Fornecedor
        $('#codigo_fazenda').selectpicker('deselectAll');
        $('#razao_nome').selectpicker('deselectAll');

        // Limpa CC
        $('#codigo_cc').selectpicker('deselectAll');

        // Limpa conta contábil
        $('#contas_selecionadas').val('Todas ou (Clique p/ selecionar contas)');
        $('#exibe_conta').val('');
        $('input[name="conta_option"]').prop('checked', false);

        // Limpa período do modal
        ctpFiltroModal = { radio: null, dataInicio: null, dataFim: null };
        $('input[name="periodo_rapido"]').prop('checked', false);
        $('#data_inicio_custom').val('');
        $('#data_fim_custom').val('');

        // Volta ao mês atual com setas ativas
        dataSelecionada = new Date();
        $('#periodo_label').val('');
        setModoNavegacao(null);

        // Oculta o link
        $('#link_limpar_filtros').hide();

        consultar_ctp();
    }

    function atualizarMesAno() {
        const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        const mes = dataSelecionada.getMonth();
        const ano = dataSelecionada.getFullYear();
        $('#btnMesAno').text(meses[mes] + ' ' + ano);
        atualizarPeriodo();
    }

    // Controla o modo de exibição do navegador de período
    // label = null → modo mês (setas ativas, mostra Mês Ano)
    // label = texto → modo fixo (setas desabilitadas, mostra o label)
    function setModoNavegacao(label) {
        if (label) {
            $('#btnMesAno').text(label);
            $('.btn-seta-periodo').prop('disabled', true).css({ 'opacity': '0.4', 'cursor': 'default', 'pointer-events': 'none' });
        } else {
            atualizarMesAno();
            $('.btn-seta-periodo').prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer', 'pointer-events': 'auto' });
        }
    }

    function navegarMesAnterior() {
        $('#periodo_label').val('');
        ctpFiltroModal.radio = null;
        ctpFiltroModal.dataInicio = null;
        ctpFiltroModal.dataFim = null;
        dataSelecionada.setMonth(dataSelecionada.getMonth() - 1);
        atualizarMesAno();
        setModoNavegacao(null);
        consultar_ctp();
    }

    function navegarMesProximo() {
        $('#periodo_label').val('');
        ctpFiltroModal.radio = null;
        ctpFiltroModal.dataInicio = null;
        ctpFiltroModal.dataFim = null;
        dataSelecionada.setMonth(dataSelecionada.getMonth() + 1);
        atualizarMesAno();
        setModoNavegacao(null);
        consultar_ctp();
    }

    function abrirSeletorData() {
        $('#modal_seletor_periodo').modal('show');
    }

    function calcularDatasRapido(tipo) {
        const hoje = new Date();
        let dataInicio, dataFim;

        switch(tipo) {
            case 'hoje':
                dataInicio = new Date(hoje);
                dataFim = new Date(hoje);
                break;
            case 'semana':
                dataInicio = new Date(hoje);
                dataInicio.setDate(hoje.getDate() - hoje.getDay());
                dataFim = new Date(dataInicio);
                dataFim.setDate(dataInicio.getDate() + 6);
                break;
            case 'mes':
                dataInicio = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
                dataFim = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
                break;
            case '30dias':
                dataFim = new Date();
                dataInicio = new Date(dataFim);
                dataInicio.setDate(dataInicio.getDate() - 30);
                break;
            case 'mes_passado':
                dataInicio = new Date(hoje.getFullYear(), hoje.getMonth() - 1, 1);
                dataFim = new Date(hoje.getFullYear(), hoje.getMonth(), 0);
                break;
            case 'trimestre':
                var primMes = hoje.getMonth() - (hoje.getMonth() % 3);
                dataInicio = new Date(hoje.getFullYear(), primMes, 1);
                dataFim = new Date(hoje.getFullYear(), primMes + 3, 0);
                break;
        }

        return { inicio: dataInicio, fim: dataFim };
    }

    function parseDateLocal(strData) {
        // Evita problema de fuso: "2026-01-01" interpretado como UTC vira 31/12/2025 no fuso BR
        var p = strData.split('-');
        return new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
    }

    var labelsRapido = {
        'hoje':        'Hoje',
        'semana':      'Esta Semana',
        'mes':         'Este Mês',
        '30dias':      'Últimos 30 Dias',
        'mes_passado': 'Mês Passado',
        'trimestre':   'Este Trimestre'
    };

    function aplicarSelecaoPeriodo() {
        var radioSelecionado = $('input[name="periodo_rapido"]:checked').val();
        var dataInicio, dataFim, label;

        // Períodos baseados em mês → setas ativas | outros → setas desabilitadas
        var periodosComSeta = ['mes', 'mes_passado'];

        if (radioSelecionado) {
            var datas = calcularDatasRapido(radioSelecionado);
            dataInicio = datas.inicio;
            dataFim = datas.fim;
            label = labelsRapido[radioSelecionado] || '';

            $('#periodo_label').val(label);
            atualizarMesAnoFromDates(dataInicio, dataFim);
            $('#data_inicial').val(formatarData(dataInicio));
            $('#data_final').val(formatarData(dataFim));

            // Este Mês / Mês Passado → modo mês normal; outros → modo fixo com label
            if (periodosComSeta.indexOf(radioSelecionado) !== -1) {
                setModoNavegacao(null);
            } else {
                setModoNavegacao(label);
            }

        } else {
            var customInicio = $('#data_inicio_custom').val();
            var customFim = $('#data_fim_custom').val();

            if (customInicio && customFim) {
                dataInicio = parseDateLocal(customInicio);
                dataFim = parseDateLocal(customFim);

                if (dataInicio > dataFim) {
                    alert('A data inicial deve ser menor ou igual à data final.');
                    return;
                }

                label = 'Período Customizado';
                $('#periodo_label').val(label);
                atualizarMesAnoFromDates(dataInicio, dataFim);
                $('#data_inicial').val(formatarData(dataInicio));
                $('#data_final').val(formatarData(dataFim));
                setModoNavegacao(label);

            }
            // Se nenhum período foi selecionado, mantém o modo atual
        }

        // Salva estado para restaurar ao reabrir o modal
        ctpFiltroModal.radio      = radioSelecionado || null;
        ctpFiltroModal.dataInicio = radioSelecionado ? null : ($('#data_inicio_custom').val() || null);
        ctpFiltroModal.dataFim    = radioSelecionado ? null : ($('#data_fim_custom').val()    || null);

        // Limpa campos do modal (serão restaurados pelo shown.bs.modal)
        $('input[name="periodo_rapido"]').prop('checked', false);
        $('#data_inicio_custom').val('');
        $('#data_fim_custom').val('');

        $('#modal_seletor_periodo').modal('hide');

        atualizarLinkLimparFiltros();
        consultar_ctp();
    }

    function formatarData(data) {
        const d = new Date(data);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const year = d.getFullYear();
        return `${year}-${month}-${day}`;
    }

    function atualizarMesAnoFromDates(dataInicio, dataFim) {
        // Se o período é do mesmo mês, mostrar esse mês
        if (dataInicio.getMonth() === dataFim.getMonth() &&
            dataInicio.getFullYear() === dataFim.getFullYear()) {
            dataSelecionada = new Date(dataInicio);
            atualizarMesAno();
        } else {
            // Mostrar mês da data inicial
            dataSelecionada = new Date(dataInicio);
            atualizarMesAno();
        }
    }


    function atualizarPeriodo() {
        const ano = dataSelecionada.getFullYear();
        const mes = dataSelecionada.getMonth();

        // Primeiro dia do mês
        const dataInicial = new Date(ano, mes, 1);
        // Último dia do mês
        const dataFinal = new Date(ano, mes + 1, 0);

        // Formatar para YYYY-MM-DD
        const formatarData = (data) => {
            const d = new Date(data);
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            const year = d.getFullYear();
            return `${year}-${month}-${day}`;
        };

        $('#data_inicial').val(formatarData(dataInicial));
        $('#data_final').val(formatarData(dataFinal));
    }
</script>

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
                <table class="table table-condensed" id="tbl_erat" style="font-size:12px;margin-bottom:0;">
                    <thead>
                        <tr style="background:#f5f7fa;">
                            <th style="width:22%;">Local</th>
                            <th style="width:22%;">Centro de Custo</th>
                            <th style="width:30%;">Conta Contábil</th>
                            <th style="width:13%;text-align:right;">Valor (R$)</th>
                            <th style="width:9%;text-align:right;">%</th>
                            <th style="width:4%;"></th>
                        </tr>
                    </thead>
                    <tbody id="tbody_erat"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;font-size:12px;padding-top:8px;">
                                <strong>Total Digitado:</strong> <span id="erat_span_total" style="color:#2c3e50;">R$ 0,00</span>
                                &nbsp;&nbsp;<strong>Restante:</strong>
                                <span id="erat_span_rest" style="font-weight:700;">R$ 0,00</span>
                                <span id="erat_span_rest_pct" style="font-weight:700;margin-left:4px;">0,00%</span>
                            </td>
                            <td colspan="3" style="padding-top:8px;">
                                <button type="button" class="btn btn-info btn-xs" onclick="eratAdicionarLinha()">
                                    <i class="fas fa-plus"></i> Adicionar Linha
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
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

</body>

</html>
