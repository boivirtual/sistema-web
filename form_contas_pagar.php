<?php
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

$c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

$fornecedor = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=3 or tbl_pessoa_classe=5) order by tbl_pessoa_nome ASC");

$tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

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

    if ($_SESSION['data_inicio_ctp'] == 0) {
        $data_inicial = $data_sistema;
    } else {
        $data_inicial =  $_SESSION['data_inicio_ctp'];
    }

    if ($_SESSION['data_fim_ctp'] == 0) {
        $data_final = $data_sistema;
    } else {
        $data_final =  $_SESSION['data_fim_ctp'];
    }

    if ($_SESSION['tipo_data_ctp'] == '') {
        $tipo_data = "V";
    } else {
        $tipo_data =  $_SESSION['tipo_data_ctp'];
    }

    $razao_nome = $_SESSION['razao_nome_ctp'];
    $codigo_c_custo = $_SESSION['codigo_c_custo_ctp'];
    $local = $_SESSION['codigo_local_ctp']; 
    $contas = $_SESSION['codigo_conta_ctp']; 

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

                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend">Consultar Contas a Pagar</legend>

                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label class="control-label">Período</label>
                                                    <div class="btn-group" style="width: 100%;">
                                                        <button type="button" class="btn btn-default" style="width: 35px; height: 34px; padding: 0;" onclick="navegarMesAnterior()"><i class="fas fa-chevron-left"></i></button>
                                                        <button type="button" class="btn btn-default" id="btnMesAno" style="flex: 1; text-align: center; height: 34px;" onclick="abrirSeletorData()">Maio 2026</button>
                                                        <button type="button" class="btn btn-default" style="width: 35px; height: 34px; padding: 0;" onclick="navegarMesProximo()"><i class="fas fa-chevron-right"></i></button>
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

                                                <div class="form-group col-md-3">
                                                    <label class="control-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-info" style="width: 100%; height: 34px; padding: 6px 12px; font-size: 12px;" data-toggle='tooltip' data-placement='top' title="Mais Filtros"><i class="fas fa-filter"></i> + Filtros</button>
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
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="modalSeletorPeriodoLabel">Selecione o Período</h4>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 style="margin-bottom: 15px; font-weight: 600;">Períodos Rápidos</h5>
                                        <div class="list-group">
                                            <button type="button" class="list-group-item list-group-item-action text-left" onclick="selecionarPeriodoRapido('hoje')">
                                                <i class="fas fa-calendar-day"></i> Hoje
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-left" onclick="selecionarPeriodoRapido('semana')">
                                                <i class="fas fa-calendar-week"></i> Esta Semana
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-left" onclick="selecionarPeriodoRapido('mes')">
                                                <i class="fas fa-calendar-alt"></i> Este Mês
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-left" onclick="selecionarPeriodoRapido('30dias')">
                                                <i class="fas fa-calendar"></i> Últimos 30 Dias
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-left" onclick="selecionarPeriodoRapido('mes_passado')">
                                                <i class="fas fa-calendar"></i> Mês Passado
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-left" onclick="selecionarPeriodoRapido('trimestre')">
                                                <i class="fas fa-calendar"></i> Este Trimestre
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 style="margin-bottom: 15px; font-weight: 600;">Período Customizado</h5>
                                        <div class="form-group">
                                            <label for="data_inicio_custom" class="control-label" style="font-size: 12px;">Data Inicial</label>
                                            <input type="date" class="form-control" id="data_inicio_custom">
                                        </div>
                                        <div class="form-group">
                                            <label for="data_fim_custom" class="control-label" style="font-size: 12px;">Data Final</label>
                                            <input type="date" class="form-control" id="data_fim_custom">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="aplicarPeriodoCustomizado()">Aplicar</button>
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

<script src="js/contas_pagar.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="js/select-1.13.14.js?<?php echo Versao; ?>"></script>

<script>
    let dataSelecionada = new Date();

    // Inicializar com mês/ano atual
    $(document).ready(function() {
        atualizarMesAno();
        $('[data-toggle="tooltip"]').tooltip();
    });

    function atualizarMesAno() {
        const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        const mes = dataSelecionada.getMonth();
        const ano = dataSelecionada.getFullYear();
        $('#btnMesAno').text(meses[mes] + ' ' + ano);
        atualizarPeriodo();
    }

    function navegarMesAnterior() {
        dataSelecionada.setMonth(dataSelecionada.getMonth() - 1);
        atualizarMesAno();
    }

    function navegarMesProximo() {
        dataSelecionada.setMonth(dataSelecionada.getMonth() + 1);
        atualizarMesAno();
    }

    function abrirSeletorData() {
        $('#modal_seletor_periodo').modal('show');
    }

    function selecionarPeriodoRapido(tipo) {
        const hoje = new Date();
        let dataInicio, dataFim;

        switch(tipo) {
            case 'hoje':
                dataInicio = hoje;
                dataFim = hoje;
                break;
            case 'semana':
                const primeiroDiaSemana = new Date(hoje.setDate(hoje.getDate() - hoje.getDay()));
                const ultimoDiaSemana = new Date(primeiroDiaSemana);
                ultimoDiaSemana.setDate(ultimoDiaSemana.getDate() + 6);
                dataInicio = primeiroDiaSemana;
                dataFim = ultimoDiaSemana;
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
                const mesPassado = new Date(hoje.getFullYear(), hoje.getMonth() - 1);
                dataInicio = new Date(mesPassado.getFullYear(), mesPassado.getMonth(), 1);
                dataFim = new Date(mesPassado.getFullYear(), mesPassado.getMonth() + 1, 0);
                break;
            case 'trimestre':
                const mesAtual = hoje.getMonth();
                const primeiraMesDoTrimestre = mesAtual - (mesAtual % 3);
                dataInicio = new Date(hoje.getFullYear(), primeiraMesDoTrimestre, 1);
                dataFim = new Date(hoje.getFullYear(), primeiraMesDoTrimestre + 3, 0);
                break;
        }

        aplicarPeriodo(dataInicio, dataFim);
    }

    function aplicarPeriodoCustomizado() {
        const dataInicio = document.getElementById('data_inicio_custom').value;
        const dataFim = document.getElementById('data_fim_custom').value;

        if (!dataInicio || !dataFim) {
            alert('Por favor, preencha as duas datas');
            return;
        }

        if (new Date(dataInicio) > new Date(dataFim)) {
            alert('A data inicial deve ser menor que a data final');
            return;
        }

        aplicarPeriodo(new Date(dataInicio), new Date(dataFim));
    }

    function aplicarPeriodo(dataInicio, dataFim) {
        $('#data_inicial').val(formatarData(dataInicio));
        $('#data_final').val(formatarData(dataFim));
        atualizarMesAnoFromDates(dataInicio, dataFim);
        $('#modal_seletor_periodo').modal('hide');
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

</body>

</html>
