<?php
include "valida_sessao.inc";
include "conecta_mysql.inc";

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
    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

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

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

    $id_ctr = $_REQUEST['id_ctr'];

    $contas_receber = mysqli_query($conector, "select * from contas_receber 
        where ctr_id='$id_ctr'");

    $registro_ctr = mysqli_fetch_object($contas_receber);

    $numero_doc = $registro_ctr->ctr_numero_doc;
    $numero_parcela = $registro_ctr->ctr_parcela;
    $codigo_cli_for = $registro_ctr->ctr_codigo_cliente_fornecedor;
    $qtd_parcela = $registro_ctr->ctr_qtd_parcelas;
    $data_emissao = $registro_ctr->ctr_data_emissao;
    $data_vencimento = $registro_ctr->ctr_data_vencimento;
    $vlr_parcela = $registro_ctr->ctr_valor_parcela;
    $vlr_juros = $registro_ctr->ctr_valor_juros;
    $desc_juros = $registro_ctr->ctr_descricao_juros;
    $vlr_desconto = $registro_ctr->ctr_valor_desconto;
    $desc_desconto = $registro_ctr->ctr_descricao_desconto;
    $vlr_acrescimo = $registro_ctr->ctr_valor_acrescimo;
    $desc_acrescimo = $registro_ctr->ctr_descricao_acrescimo;
    $codigo_conta = $registro_ctr->ctr_codigo_conta;
    $codigo_c_custo = $registro_ctr->ctr_codigo_c_custo;
    $codigo_local =  $registro_ctr->ctr_codigo_fazenda;
    $codigo_conta_rec = $registro_ctr->ctr_codigo_conta_recebimento;
    $codigo_forma_rec = $registro_ctr->ctr_codigo_forma_recebimento;
    $codigo_banco = $registro_ctr->ctr_codigo_banco;
    $numero_cheque = $registro_ctr->ctr_numero_cheque;
    $situacao = $registro_ctr->ctr_situacao;
    $tipo_doc = $registro_ctr->ctr_tipo;
    $observacao = $registro_ctr->ctr_observacao;
    $nome_cliente = $registro_ctr->ctr_nome_cliente;

    if ($situacao == "P") {
        $desc_situacao = "Pago";
    } else if ($situacao == "C") {
        $desc_situacao = "Pago Parcial";
    } else {
        $desc_situacao = "Em Aberto";
    }

    $tipos_documentos = mysqli_query($conector, "select * from tbl_tipo_documento where tbl_tipo_doc_lixeira=0");

    $plano_contas = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_debito_credito='C' and tbl_plano_contas_ana_sin='A' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_descricao ASC");

    $cli_for = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=1 or tbl_pessoa_classe=2) order by tbl_pessoa_nome ASC");

    $conta_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

    $forma_pagamento = mysqli_query($conector, "select * from tbl_forma_pagamento where tbl_forma_pagamento_lixeira=0");

    $c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and 
        tbl_pessoa_classe=4");

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

    $nd_esc_an  = mysqli_real_escape_string($conector, $numero_doc);
    $cli_esc_an = intval($codigo_cli_for);
    if ($nd_esc_an !== '' && $nd_esc_an !== '0') {
        $rs_qtd_an = mysqli_query($conector, "SELECT COUNT(*) as qtd FROM tbl_ctr_anexos a INNER JOIN contas_receber c ON c.ctr_id = a.anexo_ctr_id WHERE c.ctr_numero_doc = '$nd_esc_an' AND c.ctr_codigo_cliente_fornecedor = '$cli_esc_an'");
    } else {
        $rs_qtd_an = mysqli_query($conector, "SELECT COUNT(*) as qtd FROM tbl_ctr_anexos WHERE anexo_ctr_id = '$id_ctr'");
    }
    $row_qtd_an = $rs_qtd_an ? mysqli_fetch_object($rs_qtd_an) : null;
    $qtd_anexos = $row_qtd_an ? (int)$row_qtd_an->qtd : 0;

    // Detecção de rateio: ctr_codigo_fazenda IS NULL indica conta com rateio
    $tem_rateio = is_null($registro_ctr->ctr_codigo_fazenda)
               || $registro_ctr->ctr_codigo_fazenda === ''
               || $registro_ctr->ctr_codigo_fazenda === '000000000';

    $rateio_primeiro_local = 'Rateio';
    $rateio_total_locais   = 0;
    $rateio_primeiro_cc    = '';
    $rateio_total_ccs      = 0;
    $rateio_primeira_conta = 'Rateio';
    $rateio_total_contas   = 0;
    $rateio_tooltip_locais = '';
    $rateio_tooltip_ccs    = '';
    $rateio_tooltip_contas = '';

    if ($tem_rateio) {
        $rs_prim_rat = mysqli_query($conector,
            "SELECT MIN(c2.ctr_id) AS primeiro_id
             FROM contas_receber c1
             JOIN contas_receber c2
               ON c2.ctr_numero_doc               = c1.ctr_numero_doc
              AND c2.ctr_codigo_cliente_fornecedor = c1.ctr_codigo_cliente_fornecedor
              AND c2.ctr_codigo_fazenda IS NULL
             WHERE c1.ctr_id = '$id_ctr'");
        $row_prim_rat     = $rs_prim_rat ? mysqli_fetch_object($rs_prim_rat) : null;
        $primeiro_ctr_rat = ($row_prim_rat && $row_prim_rat->primeiro_id)
                          ? (int)$row_prim_rat->primeiro_id : (int)$id_ctr;

        $rs_locais_rat = mysqli_query($conector,
            "SELECT rc_nome_local FROM tbl_ctr_rateio
             WHERE rc_ctr_id = '$primeiro_ctr_rat'
             GROUP BY rc_codigo_local, rc_nome_local
             ORDER BY MIN(rc_id) ASC");
        $rateio_locais_todos = [];
        while ($r = mysqli_fetch_object($rs_locais_rat)) { $rateio_locais_todos[] = $r->rc_nome_local; }
        $rateio_total_locais   = count($rateio_locais_todos);
        $rateio_primeiro_local = $rateio_locais_todos[0] ?? 'Rateio';
        $rateio_tooltip_locais = implode('&#10;', array_map('htmlspecialchars', $rateio_locais_todos));

        $rs_ccs_rat = mysqli_query($conector,
            "SELECT rc_nome_cc FROM tbl_ctr_rateio
             WHERE rc_ctr_id = '$primeiro_ctr_rat'
               AND rc_nome_cc IS NOT NULL AND rc_nome_cc != ''
             GROUP BY rc_codigo_cc, rc_nome_cc
             ORDER BY MIN(rc_id) ASC");
        $rateio_ccs_todos = [];
        while ($r = mysqli_fetch_object($rs_ccs_rat)) { $rateio_ccs_todos[] = $r->rc_nome_cc; }
        $rateio_total_ccs   = count($rateio_ccs_todos);
        $rateio_primeiro_cc = $rateio_ccs_todos[0] ?? '';
        $rateio_tooltip_ccs = implode('&#10;', array_map('htmlspecialchars', $rateio_ccs_todos));

        $rs_contas_rat = mysqli_query($conector,
            "SELECT rc_nome_conta FROM tbl_ctr_rateio
             WHERE rc_ctr_id = '$primeiro_ctr_rat'
               AND rc_nome_conta IS NOT NULL AND rc_nome_conta != ''
             GROUP BY rc_codigo_conta, rc_nome_conta
             ORDER BY MIN(rc_id) ASC");
        $rateio_contas_todos = [];
        while ($r = mysqli_fetch_object($rs_contas_rat)) { $rateio_contas_todos[] = $r->rc_nome_conta; }
        $rateio_total_contas   = count($rateio_contas_todos);
        $rateio_primeira_conta = $rateio_contas_todos[0] ?? 'Rateio';
        $rateio_tooltip_contas = implode('&#10;', array_map('htmlspecialchars', $rateio_contas_todos));
    }

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
                    <span class="titulo">Contas a Receber - Editar</span></span>

                <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fas fa-hand-holding-usd"></i> Contas a Receber - Editar</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel-group">
                            <form method="POST" action="gravar_contas_receber.php" enctype="multipart/form-data" id="form_gravar_contas_receber">

                                <div class="panel">
                                    <div class=panel-body>

                                        <div class="row" id="errors"></div>

                                        <ul class="nav nav-tabs m-bot15">
                                            <li class="active">
                                                <a data-toggle="tab" href="#dados">Dados</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div id="dados" class="tab-pane active">
                                                <div class="tab-content">
                                                    <div class="tab-pane active">
                                                        <?php
                                                        if ($situacao == '' && ($codigo_grupo_usuario == 1 || $codigo_grupo_usuario == 2)) {
                                                            echo '
                                                                <div class="row">                
                                                                    <div class="form-group col-md-12">
                                                                        <button type="button" class="btn btn-primary confirma_gravar_ctr">Confirmar Edição</button>
                                                                        <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                                    </div>
                                                                </div>';
                                                        } else {
                                                            echo '
                                                                <div class="row">                
                                                                    <div class="form-group col-md-12">
                                                                        <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                                    </div>
                                                                </div>
                                                            ';
                                                        }

                                                        ?>
                                                    </div> <!-- dados-->
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="observacao" class="control-label"><span class="required">*</span> Descrição</label>
                                                        <textarea name="observacao" type="text" class="form-control" id="observacao" rows="1" onkeyup="maiuscula(this)"><?php echo $observacao ?></textarea>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="codigo_local_editar" class="control-label">
                                                            <span class="required">*</span> Local
                                                        </label>
                                                        <select class="form-control" name="codigo_local_editar" id="codigo_local_editar">
                                                            <option value="000000000">...</option>
                                                        <?php while ($reg_pessoa = mysqli_fetch_object($tbl_local)) { ?>

                                                        <?php
                                                            foreach ($array_locais_usuario as $value) {
                                                                $value = ltrim($value);
                                                                $value = rtrim($value);

                                                                if ($value==$reg_pessoa->tbl_pessoa_id && $value==$codigo_local) {
                                                                    echo '<option value="'.$value.'" selected>';
                                                                }
                                                                else if ($value==$reg_pessoa->tbl_pessoa_id) {
                                                                    echo '<option value="'.$value.'">';
                                                                }
                                                            } 
                                                            ?>

                                                            <?php
                                                                echo $reg_pessoa->tbl_pessoa_nome;
                                                            ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <input name="tipo_operacao" type="hidden" class="form-control" id="tipo_operacao" value="2">

                                                    <input name="id_ctr" type="hidden" class="form-control" id="id_ctr" <?php echo "value='" . $id_ctr . "'"; ?>>

                                                    <input name="grupo_usuario" type="hidden" class="form-control" id="grupo_usuario" <?php echo "value='" . $codigo_grupo_usuario . "'"; ?>>

                                                    <div class="form-group col-md-3">
                                                        <label for="number_doc" class="control-label">Documento Nº</label>
                                                        <input name="number_doc" type="text" class="form-control" id="number_doc" readonly="" <?php echo "value='" . $numero_doc . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="tipo_doc" class="control-label">Tipo do Documento</label>
                                                        <select class="form-control" id="tipo_doc" name="tipo_doc">

                                                        <option value="00" selected="selected">...</option>

                                                        <?php while ($registro_tipo_doc = mysqli_fetch_object($tipos_documentos)) { ?>
                                                        <option value="<?php
                                                        echo $registro_tipo_doc->tbl_tipo_doc_id ?>" <?php
                                                        if ($registro_tipo_doc->tbl_tipo_doc_id == $tipo_doc) {
                                                            echo "selected";
                                                        }
                                                        ?>>
                                                        <?php
                                                        echo $registro_tipo_doc->tbl_tipo_doc_descricao;
                                                        ?>
                                                        </option>
                                                        <?php } ?>

                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="number_parcela" class="control-label">Parcela Nº</label>
                                                        <input name="number_parcela" type="text" class="form-control" id="number_parcela" readonly="" <?php echo "value='" . $numero_parcela . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="qtd_parcela" class="control-label">Qtde de Parcelas</label>
                                                        <input name="qtd_parcela" type="text" class="form-control" id="qtd_parcela" readonly="" <?php echo "value='" . $qtd_parcela . "'"; ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="codigo_cli_for" class="control-label"><span class="required">*</span> Cliente/Parceiro</label>
                                                        <select class="form-control" id="codigo_cli_for" name="codigo_cli_for" required="">

                                                        <option value="999999999" selected="selected">...</option>

                                                        <?php while ($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>
                                                        <option value="<?php
                                                        echo $registo_cli_for->tbl_pessoa_id ?>" <?php
                                                        if ($registo_cli_for->tbl_pessoa_id == $codigo_cli_for) {
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

                                                    <div class="form-group col-md-6">
                                                        <label for="nome_cli" class="control-label">&nbsp;</label>
                                                        <input name="nome_cli" type="text" class="form-control" id="nome_cli" aria-describedby="passwordHelpBlock" onkeyup="maiuscula(this)" <?php echo "value='" . $nome_cliente . "'"; ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="codigo_cc" class="control-label">Centro de Custo</label>
                                                        <select class="form-control" id="codigo_cc" name="codigo_cc" required="">

                                                        <?php while ($registo_cc = mysqli_fetch_object($c_custo)) { ?>
                                                        <option value="<?php
                                                            echo $registo_cc->tbl_cc_codigo_id ?>" <?php
                                                            if ($registo_cc->tbl_cc_codigo_id == $codigo_c_custo) {
                                                                echo "selected";
                                                            }
                                                            ?>>
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

                                                        <option value="" selected="selected">...</option>

                                                        <?php while ($registo_pcontas = mysqli_fetch_object($plano_contas)) { ?>
                                                        <option value="<?php
                                                        echo $registo_pcontas->tbl_plano_contas_codigo_id ?>" <?php
                                                        if ($registo_pcontas->tbl_plano_contas_codigo_id == $codigo_conta) {
                                                            echo "selected";
                                                        }
                                                        ?>>

                                                        <?php
                                                        echo $registo_pcontas->tbl_plano_contas_descricao;
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
                                                        <input name="data_emissao" type="date" class="form-control" id="data_emissao" <?php echo "value='" . $data_emissao . "'"; ?>>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="data_vencimento" class="control-label"><span class="required">*</span>
                                                            Data de Vencimento</label>
                                                        <input name="data_vencimento" type="date" class="form-control" id="data_vencimento" <?php echo "value='" . $data_vencimento . "'"; ?>>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="vlr_parcela" class="control-label"><span class="required">*</span> Valor da Parcela</label>
                                                        <input name="vlr_parcela" type="text" class="form-control" id="vlr_parcela" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_parcela()" <?php echo "value='" . number_format($vlr_parcela, 2, ',', '.') . "'"; ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="vlr_juros" class="control-label">Valor dos Juros</label>
                                                        <input name="vlr_juros" type="text" class="form-control" id="vlr_juros" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_juros()" <?php echo "value='" . number_format($vlr_juros, 2, ',', '.') . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-8">
                                                        <label for="desc_juros" class="control-label">Descrição dos Juros</label>
                                                        <input name="desc_juros" type="text" class="form-control" id="desc_juros" <?php echo "value='" . $desc_juros . "'"; ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="vlr_desconto" class="control-label">Valor do Desconto</label>
                                                        <input name="vlr_desconto" type="text" class="form-control" id="vlr_desconto" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_desconto()" <?php echo "value='" . number_format($vlr_desconto, 2, ',', '.') . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-8">
                                                        <label for="desc_desconto" class="control-label">Descrição do Desconto</label>
                                                        <input name="desc_desconto" type="text" class="form-control" id="desc_desconto" <?php echo "value='" . $desc_desconto . "'"; ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="vlr_acrescimo" class="control-label">Valor Outros Acréscimos</label>
                                                        <input name="vlr_acrescimo" type="text" class="form-control" id="vlr_acrescimo" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_acrescimo()" <?php echo "value='" . number_format($vlr_acrescimo, 2, ',', '.') . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-8">
                                                        <label for="desc_acrescimo" class="control-label">Descrição Outros Acréscimos</label>
                                                        <input name="desc_acrescimo" type="text" class="form-control" id="desc_acrescimo" <?php echo "value='" . $desc_acrescimo . "'"; ?>>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">

                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_forma_rec" class="control-label">Forma Pagamento</label>
                                                        <select class="form-control" id="codigo_forma_rec" name="codigo_forma_rec">

                                                            <option value="00" selected="selected">...</option>

                                                            <?php while ($reg_forma_pag = mysqli_fetch_object($forma_pagamento)) { ?>

                                                            <option value="<?php
                                                            echo $reg_forma_pag->tbl_forma_pagamento_id  ?>" 
                                                            <?php
                                                            if ($reg_forma_pag->tbl_forma_pagamento_id == $codigo_forma_rec) {
                                                                echo "selected";
                                                            }
                                                            ?>>
                                                            <?php
                                                            echo $reg_forma_pag->tbl_forma_pagamento_descricao;
                                                            ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="codigo_conta_rec" class="control-label">Banco/Conta Pagamento</label>
                                                        <select class="form-control" id="codigo_conta_rec" name="codigo_conta_rec">

                                                        <option value="00" selected="selected">...</option>

                                                        <?php 
while ($reg_conta_pag = mysqli_fetch_object($conta_pagamento)) { 
    $codigo_conta = $reg_conta_pag->tbl_conta_pagamento_id;
    $nome_banco = $reg_conta_pag->tbl_conta_pagamento_descricao;
    $agencia = $reg_conta_pag->tbl_conta_pagamento_agencia;
    $conta = $reg_conta_pag->tbl_conta_pagamento_conta;
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';

    if ($codigo_conta == $codigo_conta_rec) {
        echo '<option value="' . $codigo_conta . '" selected="selected">' . $descricao_conta .
         '</option>';
    }
    else {
        echo '<option value="'.$codigo_conta.'">' . $descricao_conta .
         '</option>';

    }
}?>

                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4 cheque">
                                                        <label for="number_cheque" class="control-label">Número do Cheque</label>
                                                        <input name="number_cheque" type="text" class="form-control" id="number_cheque" maxlength="10" <?php echo "value='" . $numero_cheque . "'"; ?>>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="desc_situacao" class="control-label">Situação da Conta
                                                        </label>
                                                        <input name="desc_situacao" type="text" class="form-control" id="desc_situacao" readonly <?php echo "value='" . $desc_situacao . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-1" id="baixa_conta_receber" hidden="true">
                                                        <label for="baixa_conta_receber" class="control-label">&nbsp;
                                                        </label>

                                                        <button type="button" class="form-control btn-primary" onClick="baixar_conta_receber()"> Baixar</button>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <img src='img/aguarde.gif' title='' alt='' width='42' height='35' id="img_aguarde_baixa" hidden="true" />
                                                    </div>

                                                </div>

                                                <div class="row">
                                                </div>

                                                <div id="baixar_conta" hidden="true">
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="data_pagamento" class="control-label"><span class="required">*</span>
                                                                Data do Pagamento</label>
                                                            <input name="data_pagamento" type="date" class="form-control" id="data_pagamento">
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="valor_pagamento" class="control-label"><span class="required">*</span>
                                                                Valor do Pagamento</label>
                                                            <input name="valor_pagamento" type="text" class="form-control" id="valor_pagamento" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_pagamento()">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-8">
                                                            <label for="historico" class="control-label"><span class="required">*</span>
                                                                Histórico</label>
                                                            <input name="historico" type="text" class="form-control" id="historico">
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label for="baixa_conta_receber" class="control-label">&nbsp;
                                                            </label>

                                                            <button type="button" class=" form-control btn btn-primary" onClick="executar_baixa_conta_receber_individual()">Confirmar Baixa</button>
                                                        </div>


                                                    </div>
                                                </div>

                                                <?php
                                                if ($situacao == "P" || $situacao == "C") {
                                                    echo '<table class="table table-advance table-hover" width="100%" >';
                                                    echo '<thead>';
                                                    echo '<tr>';
                                                    echo '<th>Pagamento</th>';
                                                    echo '<th>Valor</th>';
                                                    echo '<th>Histórico</th>';
                                                    echo '<th>Baixado Por</th>';
                                                    echo '<th>Confirmado por</th>';
                                                    echo '<th>Estornar</th>';
                                                    echo '</tr>';
                                                    echo '</thead>';
                                                    echo '<tbody>';
                                                    $ssql = "select * from baixa_contas_receber 
                                                        where bcr_id='$id_ctr'";
                                                    $rs = mysqli_query($conector, $ssql);
                                                    $num_total_registros = mysqli_num_rows($rs);

                                                    while ($fila = mysqli_fetch_object($rs)) {
                                                        $historico = $fila->bcr_historico;
                                                        $valor_pagamento = $fila->bcr_valor_pagamento;
                                                        $valor_pagamento = number_format($valor_pagamento, 2, ".", "");
                                                        $sequencia_baixa = $fila->bcr_sequencia;
                                                        $baixado_por = $fila->bcr_usuario_aceite;
                                                        $data_pagamento = new DateTime($fila->bcr_data_pagamento);
                                                        $data_baixa = new DateTime($fila->bcr_data_aceite);
                                                        $usuario_aceite = $fila->bcr_usuario_aceite_pagamento;

                                                        if (
                                                            $fila->bcr_data_aceite_pagamento == "0001-01-01 00:00:00" ||
                                                            $fila->bcr_data_aceite_pagamento == ""
                                                        ) {
                                                            $data_aceite = "";
                                                        } else {
                                                            $data_aceite = new DateTime($fila->bcr_data_aceite_pagamento);
                                                        }

                                                        echo "<tr>";
                                                        echo "<td width='10%' align='center'>" . $data_pagamento->format('d/m/Y') . "</td>";
                                                        echo "<td width='8%' align='right'>" . $valor_pagamento . "</td>";
                                                        echo "<td width='40%' align='left'>" . $historico . "</td>";
                                                        echo "<td width='20%' align='left'>" . $baixado_por . ' - ' . $data_baixa->format('d/m/Y H:i:s') . "</td>";
                                                        if ($data_aceite == '') {
                                                            echo "<td width='20%' align='left'></td>";
                                                        } else {
                                                            echo "<td width='20%' align='left'>" . $usuario_aceite . ' - ' . $data_aceite->format('d/m/Y H:i:s') . "</td>";
                                                        }
                                                        echo "<td align='center' width='8%'><a href='#'>
                                                            <i class='btn icon_trash_alt' data-toggle='tooltip' data-placement='top'  title='Estorna a baixa'
                                                             onClick='estornar_baixa_contas_receber(\"{$id_ctr}\",\"{$numero_doc}\",\"{$numero_parcela}\",\"{$sequencia_baixa}\")'></i> 
                                                             </a></td>";
                                                        echo "</tr>";
                                                    }
                                                    echo '</tbody>';
                                                    echo '</table>';
                                                }

                                                if ($situacao == '' && ($codigo_grupo_usuario == 1 || $codigo_grupo_usuario == 2)) {
                                                    echo '
				                                        <div class="row">                
			                                                <div class="form-group col-md-12">
			                                                    <button type="button" class="btn btn-primary confirma_gravar_ctr" >Confirmar Edição</button>
			                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados" >Voltar</button>
			                                                </div>
			                                            </div>';
                                                } else {
                                                    echo '
				                                        <div class="row">                
			                                                <div class="form-group col-md-12">
			                                                    <button type="button" class="btn btn-info pull-right fecha_editar_dados" >Voltar</button>
			                                                </div>
			                                            </div>
													';
                                                }
                                                ?>
                                            </div> <!-- dados-->
                                        </div> <!--tab-content -->
                                    </div> <!--panel-body -->
                                </div> <!--panel -->
                            </form>

                        </section> <!-- panel-group -->
                    </div> <!--col-lg-12 2-->
                </div> <!--row 2-->

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


        <?php
        $javascript_file_name = 'contas_receber.js';
        require 'rodape.php';
        ?>