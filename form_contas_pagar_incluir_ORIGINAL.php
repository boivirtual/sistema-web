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

$plano_contas = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_debito_credito='D' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_codigo_id");

$cli_for = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=3 or tbl_pessoa_classe=5) order by tbl_pessoa_nome ASC");

$conta_pag_pri = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

$conta_pag_seg = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

$c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

$tipos_documentos = mysqli_query($conector, "select * from tbl_tipo_documento where tbl_tipo_doc_lixeira=0");

$tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0");

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

    <!-- container section start -->
    <section id="container" class="">

        <!--sidebar start-->
        <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php";
        include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";

        ?>
        <!--sidebar end-->

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper" style="margin-left: 5px;">
                <span class="caminho-programa">Gestão Administrativa <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_contas_pagar.php"> Contas a Pagar</a> <i class="fa fa-angle-right seta-direita"></i>
                    <span class="titulo">Contas a Pagar Incluir</span></span>

                <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fas fa-search-dollar"></i> Contas a Pagar - Incluir</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST" action="gravar_contas_pagar.php" enctype="multipart/form-data" id="form_gravar_contas_pagar">

                            <div class="panel">
                                <div class=panel-body>
                                    <input name="tipo_gravacao" type="hidden" id="tipo_gravacao">

                                    <input name="array_fazendas" type="hidden" id="array_fazendas">

                                    <div class="row" id="errors"></div>

                                    <ul class="nav nav-tabs m-bot15">
                                        <li class="active">
                                            <a data-toggle="tab" href="#dados">Dados</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirmar_gravar" onclick="confirmar_fazendas()">Confirmar</button>
                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>

                                                    <input name="tipo_operacao" type="hidden" id="tipo_operacao" value="1">
                                                </div>
                                            </div>

                                            <div class="row m-bot15">
                                                <div class="col-md-6">
                                                    <label for="descricao_compra" class="control-label"><span class="required">*</span> Descrição da Compra</label>
                                                    <textarea name="descricao_compra" type="text" class="form-control" id="descricao_compra" rows="1" onkeyup="maiuscula(this)"></textarea>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="codigo_fazenda" class="control-label"><span class="required">*</span> Local</label>
                                                    <select class="form-control selectpicker" multiple id="codigo_fazenda" name="codigo_fazenda[]" style="z-index:5;">

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
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-5">
                                                    <label for="codigo_cli_for" class="control-label"><span class="required">*</span> Razão/Nome</label>
                                                    <select class="form-control" id="codigo_cli_for" name="codigo_cli_for" data-live-search="true">

                                                        <option value="999999999" selected="selected">Selecione um fornecedor</option>

                                                        <?php while ($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>

                                                            <option value="<?php
                                                                            echo $registo_cli_for->tbl_pessoa_id ?>" <?php
                                                                                                                        if ($registo_cli_for->tbl_pessoa_id == $ultimo_fornecedor_cadastrado) {
                                                                                                                            echo "selected";
                                                                                                                        }
                                                                                                                        ?>>

                                                                <?php
                                                                echo $registo_cli_for->tbl_pessoa_nome;
                                                                ?>
                                                            </option>
                                                        <?php } ?>

                                                    </select>
                                                </div>

                                                <div class="form-group col-md-1 incluir_mais">
                                                    <label for="codigo_cli_for" class="control-label">&nbsp;</label>
                                                    <p><a href="form_cliente_fornecedor_incluir.php?voltar=3">
                                                            <i class='fa fa-plus' style="font-size:18px" data-toggle='tooltip' data-placement='top' title='Cadastrar novo fornecedor'></i>
                                                        </a></p>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="nome_for" class="control-label">&nbsp;</label>
                                                    <input name="nome_for" type="text" class="form-control" id="nome_for" onkeyup="maiuscula(this)" placeholder="Digite a Razão/Nome não cadastrado">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="codigo_cc" class="control-label">Centro de Custo</label>
                                                    <select class="form-control" id="codigo_cc" name="codigo_cc">

                                                        <?php while ($registo_cc = mysqli_fetch_object($c_custo)) { ?>
                                                            <option value="<?php
                                                                            echo $registo_cc->tbl_cc_codigo_id ?>">

                                                                <?php
                                                                echo $registo_cc->tbl_cc_descricao;
                                                                ?>
                                                            </option>
                                                        <?php } ?>

                                                    </select>

                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="codigo_conta" class="control-label"><span class="required">*</span> Conta Contábil</label>
                                                    <select class="form-control" id="codigo_conta" name="codigo_conta" required="">

                                                        <option value="0000000">...</option>

                                                        <?php while ($registro_pcontas = mysqli_fetch_object($plano_contas)) {

                                                            if ($registro_pcontas->tbl_plano_contas_nivel == 1) {
                                                                echo "<option value='{$registro_pcontas->tbl_plano_contas_codigo_id}' disabled>
                                                                    $registro_pcontas->tbl_plano_contas_descricao
                                                                </option>";
                                                            } elseif ($registro_pcontas->tbl_plano_contas_nivel == 2) {
                                                                echo "<option value='{$registro_pcontas->tbl_plano_contas_codigo_id}' disabled>" .
                                                                    str_repeat('&nbsp;', 4) . $registro_pcontas->tbl_plano_contas_descricao .
                                                                    "</option>";
                                                            } else {
                                                                echo "<option value='{$registro_pcontas->tbl_plano_contas_codigo_id}'>" .
                                                                    str_repeat('&nbsp;', 8) . $registro_pcontas->tbl_plano_contas_descricao .
                                                                    "</option>";
                                                            }
                                                        } ?>

                                                    </select>
                                                </div>
                                            </div>

                                            <hr align="center">

                                            <p style="font-size: 16px">Inclusão da Forma de Pagamento</p>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="scheduler-border" id="servico_fornecedor" style="height: 600px">
                                                        <legend class="scheduler-border fonte-legend">1ª Parcela ou Parcela Única</legend>

                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <label for="number_doc" class="control-label">Nº do Documento</label>
                                                                <input name="number_doc" type="number" class="form-control" id="number_doc" maxlength="15" data-toggle='tooltip' data-placement='top' title="Caso não tenha o Nº, o sistema irá criar um automaticamente">
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="tipo_doc" class="control-label">Tipo do Documento</label>
                                                                <select class="form-control" id="tipo_doc" name="tipo_doc" required="">

                                                                    <option value="00" selected="selected">...</option>

                                                                    <?php while ($registro_tipo_doc = mysqli_fetch_object($tipos_documentos)) { ?>

                                                                        <option value="<?php
                                                                                        echo $registro_tipo_doc->tbl_tipo_doc_id ?>">

                                                                            <?php
                                                                            echo $registro_tipo_doc->tbl_tipo_doc_descricao;
                                                                            ?>
                                                                        </option>
                                                                    <?php } ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <label for="data_emissao" class="control-label"><span class="required">*</span>
                                                                    Data de Emissão</label>
                                                                <input name="data_emissao" type="date" class="form-control" id="data_emissao">
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="data_vencimento" class="control-label"><span class="required">*</span> Data de Vencimento</label>
                                                                <input name="data_vencimento" type="date" class="form-control" id="data_vencimento">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <label for="vlr_primeira_parcela" class="control-label"><span class="required">*</span> Valor da 1ª Parcela ou da Parcela Única</label>
                                                                <input name="vlr_primeira_parcela" type="text" class="form-control" id="vlr_primeira_parcela" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_primeira_parcela()">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <input class="form-check-input checkbox1" type="checkbox" value="" id="pago" name="pago"> Incluir como pago
                                                            </div>
                                                        </div>

                                                        <div id="dados_pagamento" hidden="">

                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="data_pagamento" class="control-label"><span class="required">*</span> Data do Pagamento</label>
                                                                    <input name="data_pagamento" type="date" class="form-control" id="data_pagamento">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-4">
                                                                    <label for="vlr_desconto" class="control-label">Desconto</label>
                                                                    <input name="vlr_desconto" type="text" class="form-control" id="vlr_desconto" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_desconto()">
                                                                </div>

                                                                <div class="form-group col-md-4">
                                                                    <label for="vlr_juros" class="control-label">Juros</label>
                                                                    <input name="vlr_juros" type="text" class="form-control" id="vlr_juros" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_juros()">
                                                                </div>

                                                                <div class="form-group col-md-4">
                                                                    <label for="vlr_pagamento" class="control-label"><span class="required">*</span> Valor Pago</label>
                                                                    <input name="vlr_pagamento" type="text" class="form-control" id="vlr_pagamento" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_vlr_pagamento()">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-6">
                                                                <label for="codigo_forma_rec" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                                                <select class="form-control" id="codigo_forma_rec" name="codigo_forma_rec">

                                                                    <option value="0" selected="selected">...</option>

                                                                    <?php
                                                                    while ($ln = mysqli_fetch_object($conta_pag_pri)) {
                                                                        $codigo_conta = $ln->tbl_conta_pagamento_id;
                                                                        $nome_banco = $ln->tbl_conta_pagamento_descricao;
                                                                        $agencia = $ln->tbl_conta_pagamento_agencia;
                                                                        $conta = $ln->tbl_conta_pagamento_conta;

                                                                        $descricao_conta = $nome_banco . ' (Age: ' . $agencia . ' Cta: ' . $conta . ')';

                                                                        echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
                                                                            '</option>';
                                                                    }

                                                                    ?>

                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6 cheque" hidden="">
                                                                <label for="number_cheque" class="control-label">Número do Cheque</label>
                                                                <input name="number_cheque" type="text" class="form-control" id="number_cheque" maxlength="10">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>

                                                <div class="col-md-6">
                                                    <fieldset class="scheduler-border" id="servico_fornecedor" style="height: 600px">
                                                        <legend class="scheduler-border fonte-legend">Restante das Parcelas
                                                        </legend>

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <label for="qtd_parcelas" class="control-label"><span class="required">*</span> Número de Ocorrências das Parcelas Restantes</label>
                                                                <input name="qtd_parcelas" type="number" class="form-control" id="qtd_parcelas">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <label for="codigo_forma_parc" class="control-label">Banco/Conta Pagamento</label>
                                                                <select class="form-control" id="codigo_forma_parc" name="codigo_forma_parc">

                                                                    <option value="0" selected="selected">...</option>

                                                                    <?php

                                                                    while ($ln = mysqli_fetch_object($conta_pag_seg)) {
                                                                        $codigo_conta = $ln->tbl_conta_pagamento_id;
                                                                        $nome_banco = $ln->tbl_conta_pagamento_descricao;
                                                                        $agencia = $ln->tbl_conta_pagamento_agencia;
                                                                        $conta = $ln->tbl_conta_pagamento_conta;

                                                                        $descricao_conta = $nome_banco . ' (Age: ' . $agencia . ' Cta: ' . $conta . ')';

                                                                        echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
                                                                            '</option>';
                                                                    }

                                                                    ?>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <input class="tipo_inclusao" type="radio" name="tipo_inclusao" id="tipo_vlr_fixo" value="F">

                                                                <label class="form-check-label" for="tipo_vlr_fixo" style="font-weight:bold;"> Repetir Pagamento por Frequência
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <input class="tipo_inclusao" type="radio" name="tipo_inclusao" id="tipo_prazo" value="P">

                                                                <label class="form-check-label" for="tipo_prazo" style="font-weight:bold;"> Parcelar por prazos em dias
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div id="incluir_valor_fixo" hidden="">
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="vlr_parcela_fixa" class="control-label"><span class="required">*</span> Valor das Parcelas</label>
                                                                    <input name="vlr_parcela_fixa" type="txt" class="form-control" id="vlr_parcela_fixa" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_parcela_fixa()">
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-6">
                                                                    <label for="frequencia" class="control-label"><span class="required">*</span> Selecionar Frequência</label>
                                                                    <select class="form-control" id="frequencia" name="frequencia">

                                                                        <option value="0">...</option>
                                                                        <option value="1">Diária</option>
                                                                        <option value="2">Semanal</option>
                                                                        <option value="3">Quinzenal</option>
                                                                        <option value="4">Mensal</option>
                                                                        <option value="5">Bimestral</option>
                                                                        <option value="6">Trimestral</option>
                                                                        <option value="7">Semestral</option>
                                                                        <option value="8">Anual</option>
                                                                    </select>
                                                                </div>

                                                                <div class="form-group col-md-6">
                                                                    <label for="data_inicial" class="control-label"><span class="required">*</span>
                                                                        Data Inicial Próximos Pagamentos</label>
                                                                    <input name="data_inicial" type="date" class="form-control" id="data_inicial">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="incluir_prazo" hidden="">
                                                            <div class="row">
                                                                <div class="form-group  col-md-12">
                                                                    <label for="prazo" class="control-label"><span class="required">*</span> Prazos</label>
                                                                    <input name="prazo" type="text" class="form-control" id="prazo" aria-describedby="passwordHelpBlock">
                                                                    <small id="passwordHelpBlock" class="form-text text-muted" style="color: #808080">(Digite o prazo seguido por uma vírgula. Exp: 30,60,90)</small>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="vlr_compra" class="control-label"><span class="required">*</span> Valor Total da Compra</label>
                                                                    <input name="vlr_compra" type="text" class="form-control" id="vlr_compra" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_compra()" data-toggle='tooltip' data-placement='top' title="O Valor das parcelas será o Total da compra menos o valor da 1ª parcela dividido pelo número de ocorrências">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary confirmar_gravar" onclick="confirmar_fazendas()">Confirmar</button>
                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                </div>
                                            </div>
                                        </div> <!-- dados-->

                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->
                        </form>
                    </div> <!--col-lg-12 2-->
                </div> <!--row 2-->

                <div class="modal fade" id="modal_fazendas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Totais por Fazenda</h4>
                            </div>

                            <div class="modal-body">
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <span class="text-primary total_compra"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-primary primeira_parcela"></span>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <span class="text-primary parcelas"></span>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-primary vlr_parcelas"></span>
                                    </div>
                                </div>

                                <table class="table table-striped table-advance table-hover" id="tabela_fazendas" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;"> Fazenda</th>
                                            <th> Percentual</th>
                                            <th> Valor</th>
                                            <th> Parcelas</th>
                                            <th align="right"> Código</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary confirmar_gravar" type="button" onclick="gravar_conta();">Confirmar Inclusão</button>
                                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                                </button>

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
                                <h4 class="modal-title">Contas a Pagar - Mensagem</h4>
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


        <?php
        $javascript_file_name = 'contas_pagar.js';
        require 'rodape.php';
        ?>