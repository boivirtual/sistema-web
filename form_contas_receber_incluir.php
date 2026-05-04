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

$plano_contas = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_debito_credito='C' and tbl_plano_contas_ana_sin='A' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_descricao ASC");

$conta_mensal = mysqli_query($conector, "select * from tbl_plano_contas where tbl_plano_contas_debito_credito='C' and tbl_plano_contas_ana_sin='A' and tbl_plano_contas_lixeira=0 order by tbl_plano_contas_descricao ASC");

$cli_for = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_lixeira=0 and (tbl_pessoa_classe=1 or tbl_pessoa_classe=2) order by tbl_pessoa_nome ASC");

$conta_pagamento = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

$forma_pagamento = mysqli_query($conector, "select * from tbl_forma_pagamento where tbl_forma_pagamento_lixeira=0 order by tbl_forma_pagamento_id  ASC");

$c_custo = mysqli_query($conector, "select * from tbl_centro_custo where tbl_cc_lixeira=0 order by tbl_cc_codigo_id ASC");

$conta_pagamento_mensal = mysqli_query($conector, "select * from tbl_conta_pagamento where tbl_conta_pagamento_lixeira=0 order by tbl_conta_pagamento_descricao ASC");

$forma_pagamento_mensal = mysqli_query($conector, "select * from tbl_forma_pagamento where tbl_forma_pagamento_lixeira=0 order by tbl_forma_pagamento_id  ASC");

$tipos_documentos = mysqli_query($conector, "select * from tbl_tipo_documento where 
      tbl_tipo_doc_lixeira=0");


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

                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                <button type="button" class="btn btn-primary confirma_gravar_ctr">Confirmar Inclusão</button>
                                                                <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                            </div>
                                                        </div>

                                                    </div> <!-- dados-->
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="observacao" class="control-label"><span class="required">*</span> Descrição</label>
                                                        <textarea name="observacao" type="text" class="form-control" id="observacao" rows="1" onkeyup="maiuscula(this)"></textarea>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="codigo_local" class="control-label"><span class="required">*</span> Local</label>
                                                        <select class="form-control" name="codigo_local" id="codigo_local">
                                                            <option value="000000000">...</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <input name="tipo_operacao" type="hidden" class="form-control" id="tipo_operacao" value="1">

                                                    <div class="form-group col-md-5">
                                                        <label for="codigo_cli_for" class="control-label">Cliente/Parceiro</label>

                                                        <select class="form-control" id="codigo_cli_for" name="codigo_cli_for" data-size="6">
<option value="999999999" selected="selected">...</option>
<?php while ($registo_cli_for = mysqli_fetch_object($cli_for)) { ?>
    <option value="<?php
echo $registo_cli_for->tbl_pessoa_id ?>" <?php
if ($registo_cli_for->tbl_pessoa_id == $ultimo_cliente_cadastrado) {
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
                                                        <label class="control-label">&nbsp;</label>
                                                        <p><a href="form_cliente_fornecedor_incluir.php?voltar=1">
                                                            <i class='fa fa-plus' style="font-size:18px" data-toggle='tooltip' data-placement='top' title='Cadastrar novo cliente'></i>
                                                            </a></p>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="nome_cli" class="control-label">&nbsp;</label>
                                                        <input name="nome_cli" type="text" class="form-control" id="nome_cli" onkeyup="maiuscula(this)" placeholder="Digite a Razão/Nome não cadastrado">
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-3">
                                                        <label for="number_doc" class="control-label">Documento Nº</label>
                                                        <input name="number_doc" type="number" class="form-control" id="number_doc" data-toggle='tooltip' data-placement='top' title="Caso não tenha o Nº, o sistema irá criar um automaticamente">
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="tipo_doc" class="control-label">Tipo do Documento</label>
                                                        <select class="form-control" id="tipo_doc" name="tipo_doc">

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

                                                    <div class="form-group col-md-3">
                                                        <label for="data_emissao" class="control-label"><span class="required">*</span>
                                                            Data de Emissão</label>
                                                        <input name="data_emissao" type="date" class="form-control" id="data_emissao" <?php echo "value='" . $data_sistema . "'"; ?>>
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="codigo_cc" class="control-label">Centro de Custo</label>
                                                        <select class="form-control" id="codigo_cc" name="codigo_cc" required="">

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
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="vlr_parcela" class="control-label"><span class="required">*</span>
                                                            Parcela Única ou 1ª Parcela</label>
                                                        <input name="vlr_parcela" type="text" class="form-control" id="vlr_parcela" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_parcela()">
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="vencimento_primeira_parcela" class="control-label"><span class="required">*</span>
                                                            Data de Vencimento</label>
                                                        <input name="vencimento_primeira_parcela" type="date" class="form-control" id="vencimento_primeira_parcela">
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="conta_primeira_parcela" class="control-label"><span class="required">*</span> Conta Contábil</label>
                                                        <select class="form-control" id="conta_primeira_parcela" name="conta_primeira_parcela">

                                                            <option value="0" selected="selected">...</option>
                                                            <?php
                                                            while ($ln = mysqli_fetch_object($plano_contas)) {
                                                                $codigo_conta = $ln->tbl_plano_contas_codigo_id;
                                                                $descricao_conta = $ln->tbl_plano_contas_descricao;
                                                                echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
                                                                    '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="forma_pgto_primeira_parcela" class="control-label"><span class="required">*</span> Forma Pagamento</label>
                                                        <select class="form-control" id="forma_pgto_primeira_parcela" name="forma_pgto_primeira_parcela">

                                                            <option value="00" selected="selected">...</option>
                                                            <?php
                                                            while ($ln = mysqli_fetch_object($forma_pagamento)) {
                                                                $codigo_forma = $ln->tbl_forma_pagamento_id;
                                                                $descricao_forma = $ln->tbl_forma_pagamento_descricao;
                                                                echo ' <option value="' . $codigo_forma . '" >' . $descricao_forma .
                                                                    '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="conta_pgto_primeira_parcela" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                                        <select class="form-control" id="conta_pgto_primeira_parcela" name="conta_pgto_primeira_parcela">

                                                        <option value="00" selected="selected">...</option>
                                                        <?php
while ($ln = mysqli_fetch_object($conta_pagamento)) {
    $codigo_conta = $ln->tbl_conta_pagamento_id;
    $nome_banco = $ln->tbl_conta_pagamento_descricao;
    $agencia = $ln->tbl_conta_pagamento_agencia;
    $conta = $ln->tbl_conta_pagamento_conta;
    
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';
    
    echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
         '</option>';
}
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <input class="form-check-input checkbox3" type="checkbox" value="" id="pago" name="pago"> Incluir a Parcela Única ou 1ª Parcela como paga
                                                    </div>
                                                </div>

                                                <div id="dados_pagamento" hidden="">

                                                    <div class="row">
                                                        <div class="form-group col-md-3">
                                                            <label for="data_pagamento" class="control-label"><span class="required">*</span> Data do Recebimento</label>
                                                            <input name="data_pagamento" type="date" class="form-control" id="data_pagamento">
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="vlr_desconto" class="control-label">Desconto</label>
                                                            <input name="vlr_desconto" type="text" class="form-control" id="vlr_desconto" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_desconto()">
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="vlr_juros" class="control-label">Juros</label>
                                                            <input name="vlr_juros" type="text" class="form-control" id="vlr_juros" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_juros()">
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="vlr_pagamento" class="control-label"><span class="required">*</span> Valor Recebido</label>
                                                            <input name="vlr_pagamento" type="text" class="form-control" id="vlr_pagamento" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_vlr_pagamento()">
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-2">
                                                        <input class="form-check-input checkbox1" type="checkbox" value="" id="repetir" name="repetir"> Repetir
                                                    </div>
                                                </div>

                                                <div class="row" hidden="true" id="sel_frequencia">
                                                    <div class="form-group col-md-4">
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

                                                    <div class="form-group col-md-4">
                                                        <label for="ocorrencias" class="control-label"><span class="required">*</span>
                                                            Nº Ocorrências</label>
                                                        <input name="ocorrencias" type="number" class="form-control" id="ocorrencias">
                                                    </div>
                                                </div>

                                                <div class="row" id="dados_mensalidades" hidden="true">
                                                    <div class="col-md-4">
                                                        <label for="valor_mensal" class="control-label"><span class="required">*</span>
                                                            Valor das Parcelas</label>
                                                        <input name="valor_mensal" type="text" class="form-control" id="valor_mensal" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_valor_mensal()">
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="data_inicial" class="control-label"><span class="required">*</span>
                                                            Data Inicial p/ Próximos Recebimentos</label>
                                                        <input name="data_inicial" type="date" class="form-control" id="data_inicial">
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="conta_mensal" class="control-label"><span class="required">*</span> Conta Contábil</label>
                                                        <select class="form-control" id="conta_mensal" name="conta_mensal">

                                                            <option value="0" selected="selected">...</option>
                                                            <?php
                                                            while ($ln = mysqli_fetch_object($conta_mensal)) {
                                                                $codigo_conta = $ln->tbl_plano_contas_codigo_id;
                                                                $descricao_conta = $ln->tbl_plano_contas_descricao;
                                                                echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
                                                                    '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row" id="contas_mensalidades" hidden="true">
                                                    <div class="form-group col-md-6">
                                                        <label for="forma_pgto_mensal" class="control-label"><span class="required">*</span> Forma Pagamento</label>
                                                        <select class="form-control" id="forma_pgto_mensal" name="forma_pgto_mensal">

                                                            <option value="00" selected="selected">...</option>
                                                            <?php
                                                            while ($ln = mysqli_fetch_object($forma_pagamento_mensal)) {
                                                                $codigo_forma = $ln->tbl_forma_pagamento_id;
                                                                $descricao_forma = $ln->tbl_forma_pagamento_descricao;
                                                                echo ' <option value="' . $codigo_forma . '" >' . $descricao_forma .
                                                                    '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="conta_pgto_mensal" class="control-label"><span class="required">*</span> Banco/Conta Pagamento</label>
                                                        <select class="form-control" id="conta_pgto_mensal" name="conta_pgto_mensal">

                                                            <option value="00" selected="selected">...</option>
                                                        <?php
while ($ln = mysqli_fetch_object($conta_pagamento_mensal)) {
    $codigo_conta = $ln->tbl_conta_pagamento_id;
    $nome_banco = $ln->tbl_conta_pagamento_descricao;
    $agencia = $ln->tbl_conta_pagamento_agencia;
    $conta = $ln->tbl_conta_pagamento_conta;
    
    $descricao_conta = $nome_banco .' (Age: '.$agencia.' Cta: '.$conta.')';
    
    echo ' <option value="' . $codigo_conta . '" >' . $descricao_conta .
         '</option>';
}
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <button type="button" class="btn btn-primary confirma_gravar_ctr">Confirmar Inclusão</button>
                                                        <button type="button" class="btn btn-info pull-right fecha_editar_dados">Voltar</button>
                                                    </div>
                                                </div>
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