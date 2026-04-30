<?php
include "valida_sessao.inc";
include "conecta_mysql.inc";

$data_sistema = date("Y-m-d");;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
    <title>Boi Virtual</title>

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/daterangepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="css/bootstrap-colorpicker.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css"></script>
    <link href="DataTables-1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

</head>

<body>

    <?php

    if (isset($_REQUEST['id'])) {
        $codigo = $_REQUEST['id'];
    } else {
        $codigo = 0;
    }

    if ($codigo == 0 || $codigo == '') {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Algo deu errado, acesse o programa pelo menu do sistema</span>';
        echo '</div>';
        exit;
    }
    ?>

    <!-- container section start -->
    <section id="container" class="">

        <!--sidebar start-->
        <?php
        include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";

        $count = 1;

        $tbl_cliente = mysqli_query($conector, "select * from tbl_cliente_boi_virtual where tbl_cliente_id ='$codigo'");

        $reg_cli = mysqli_fetch_object($tbl_cliente);

        $cliente_ativo = $reg_cli->tbl_cliente_ativo;
        $nome_empresa = $reg_cli->tbl_cliente_nome_empresa;
        $nome_fantasia = $reg_cli->tbl_cliente_nome_fantasia_empresa;
        $cpf_cnpj_empresa = $reg_cli->tbl_cliente_cpf_cnpj_empresa;
        $insc_estadual = $reg_cli->tbl_cliente_insc_estadual_empresa;
        $cep_empresa = $reg_cli->tbl_cliente_cep_empresa;
        $endereco_empresa = $reg_cli->tbl_cliente_endereco_empresa;
        $numero_empresa = $reg_cli->tbl_cliente_numero_empresa;
        $complemento_empresa = $reg_cli->tbl_cliente_complemento_empresa;
        $bairro_empresa = $reg_cli->tbl_cliente_bairro_empresa;
        $cidade_empresa = $reg_cli->tbl_cliente_municipio_empresa;
        $estado_empresa = $reg_cli->tbl_cliente_estado_empresa;
        $cpf_adm = $reg_cli->tbl_cliente_cpf_adm;
        $nome_adm = $reg_cli->tbl_cliente_nome_adm;
        $ddd_adm = $reg_cli->tbl_cliente_ddd_adm;
        $telefone_adm = $reg_cli->tbl_cliente_telefone_adm;
        $email_adm = $reg_cli->tbl_cliente_email_adm;
        $controle_estoque = $reg_cli->tbl_cliente_controle_estoque;

        $data_cadastro = new DateTime($reg_cli->tbl_cliente_incluido_em);

        $tam = strlen($cpf_cnpj_empresa);

        if ($tam == 11) {
            $cnpj_cpf_emp_edi = substr($cpf_cnpj_empresa, 0, 3) . "." .
                substr($cpf_cnpj_empresa, 3, 3) . "." .
                substr($cpf_cnpj_empresa, 6, 3) . "-" .
                substr($cpf_cnpj_empresa, 9, 2);
        } else {
            $cnpj_cpf_emp_edi = substr($cpf_cnpj_empresa, 0, 2) . "." .
                substr($cpf_cnpj_empresa, 2, 3) . "." .
                substr($cpf_cnpj_empresa, 5, 3) . "/" .
                substr($cpf_cnpj_empresa, 8, 4) . "-" .
                substr($cpf_cnpj_empresa, 12, 2);
        }

        $cpf_adm_edi = substr($cpf_adm, 0, 3) . "." .
            substr($cpf_adm, 3, 3) . "." .
            substr($cpf_adm, 6, 3) . "-" .
            substr($cpf_adm, 9, 2);

        $tab_estados = mysqli_query($conector, "select * from tabela_estados");
        $tab_municipios = mysqli_query($conector, "select * from tabela_municipios 
                                                          where mun_estado='$estado_empresa'");

        $tab_estados_01 = mysqli_query($conector, "select * from tabela_estados");
        $tab_municipios_01 = mysqli_query($conector, "select * from tabela_municipios");

        $tab_estados_02 = mysqli_query($conector, "select * from tabela_estados");
        $tab_municipios_02 = mysqli_query($conector, "select * from tabela_municipios");

        $tab_estados_03 = mysqli_query($conector, "select * from tabela_estados");
        $tab_municipios_03 = mysqli_query($conector, "select * from tabela_municipios");

        $tab_estados_04 = mysqli_query($conector, "select * from tabela_estados");
        $tab_municipios_04 = mysqli_query($conector, "select * from tabela_municipios");

        $tab_estados_05 = mysqli_query($conector, "select * from tabela_estados");
        $tab_municipios_05 = mysqli_query($conector, "select * from tabela_municipios");

        $fazendas = mysqli_query($conector, "select * from tbl_cliente_fazenda where cliente_id = '{$codigo}'");
        ?>
        <!--sidebar end-->

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper" style="margin-left: 5px;">
                <span class="caminho-programa">Cabeçalho <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_cliente_boi_virtual.php"> Validar Clientes</a> <i class="fa fa-angle-right seta-direita"></i>
                    <span class="titulo">Validar Clientes Editar</span></span>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fa fa-users"></i> Validar Clientes - Editar</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel-group">
                            <form method="POST" action="gravar_clientes.php" enctype="multipart/form-data" id="form_gravar_cliente">

                                <div class="panel">
                                    <div class=panel-body>
                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend">Formulário</legend>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <button type="button" class="btn btn-primary" title="Somente Confirma as alterções" data-toggle='tooltip' data-placement='top' onclick="gravar_alteracao(0)">Confirmar Edição</button>

                                                    <button type="button" class="btn btn-success" title="Confirma as alterções e valida o cliente criando os dados no banco de dados" data-toggle='tooltip' data-placement='top' onclick="gravar_alteracao(1)">Autorizar</button>

                                                    <button type="button" class="btn btn-info pull-right sair_programa">Voltar</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="nome_empresa" class="control-label"><span class="required">*</span> Nome da Empresa ou Produtor</label>

                                                    <input type="text" name="nome_empresa" id="nome_empresa" class="form-control" required="" onkeyup="maiuscula(this)" <?php echo "value='" . $nome_empresa . "'"; ?>>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="nome_fantasia" class="control-label"><span class="required">*</span> Nome Fantasia</label>

                                                    <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control" required="" onkeyup="maiuscula(this)" placeholder="Duplo click Repetir mesmo nome anterior" <?php echo "value='" . $nome_fantasia . "'"; ?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="cpf_cnpj" class="control-label"><span class="required">*</span> CPF ou CNPJ</label>

                                                    <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" required="" onBlur="valida_cpf_cnpj(this);" <?php echo "value='" . $cnpj_cpf_emp_edi . "'"; ?> onkeypress="return numeros(this, event)">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="insc_est" class="control-label">Inscrição Estadual (se houver)</label>

                                                    <input type="text" name="insc_est" id="insc_est" class="form-control" <?php echo "value='" . $insc_estadual . "'"; ?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label for="cep_pessoa" class="control-label"><span class="required">*</span> CEP</label>
                                                    <input name="cep_pessoa" type="text" class="form-control" id="cep_pessoa" required="" <?php echo "value='" . $cep_empresa . "'"; ?> onkeypress="return numeros(this, event)">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="endereco_pessoa" class="control-label">Endereço</label>
                                                    <input name="endereco_pessoa" type="text" class="form-control" id="endereco_pessoa" onkeyup="maiuscula(this)" <?php echo "value='" . $endereco_empresa . "'"; ?>>
                                                </div>

                                                <div class="form-group col-md-1">
                                                    <label for="num_pessoa" class="control-label">Número</label>
                                                    <input name="num_pessoa" type="text" class="form-control" id="num_pessoa" onkeyup="maiuscula(this)" <?php echo "value='" . $numero_empresa . "'"; ?>>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="complemento_pessoa" class="control-label">Complemento</label>
                                                    <input name="complemento_pessoa" type="text" class="form-control" id="complemento_pessoa" onkeyup="maiuscula(this)" <?php echo "value='" . $complemento_empresa . "'"; ?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="bairro_pessoa" class="control-label">Bairro</label>
                                                    <input name="bairro_pessoa" type="text" class="form-control" id="bairro_pessoa" onkeyup="maiuscula(this)" <?php echo "value='" . $bairro_empresa . "'"; ?>>
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label for="estado_pessoa" class="control-label">Estado</label>
                                                    <select class="form-control" id="estado_pessoa" name="estado_pessoa">
                                                        <option value="" selected="selected">...</option>

                                                        <?php while ($registro_estado = mysqli_fetch_object($tab_estados)) { ?>
                                                            <option value="<?php
                                                                            echo $registro_estado->est_codigo_id ?>" <?php
                                                                                                                        if ($registro_estado->est_codigo_id == $estado_empresa) {
                                                                                                                            echo "selected";
                                                                                                                        }
                                                                                                                        ?>>

                                                                <?php
                                                                echo $registro_estado->est_nome;
                                                                ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label for="cidade_pessoa" class="control-label">Município</label>
                                                    <input name="cidade_pessoa" type="text" class="form-control" id="cidade_pessoa" onkeyup="maiuscula(this)" readonly="" <?php echo "value='" . $cidade_empresa . "'"; ?>>
                                                </div>

                                                <div class="form-group col-md-4 selecione_municipio">
                                                    <label for="lista_municipio" class="control-label">Selecione</label>
                                                    <select class="form-control" name="lista_municipio" id="lista_municipio">
                                                        <option value="" selected="selected">...</option>
                                                        <?php while ($registro_mun = mysqli_fetch_array($tab_municipios)) { ?>
                                                            <option value="<?php echo $registro_mun['mun_nome']; ?>">
                                                                <?php
                                                                echo $registro_mun['mun_nome'];
                                                                ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="controle_estoque" class="control-label"><span class="required">*</span> Controle de Estoque de Animais?</label>

                                                    <div class="clearfix"></div>

                                                    <label class="radio-inline">
                                                        <input type="radio" name="controle_estoque" class="controle_estoque" value="I" required="" <?php if ($controle_estoque == 'I') {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?>> Controle Individual (Gado numerado com Identificação Individual)
                                                    </label>

                                                    <label class="radio-inline">
                                                        <input type="radio" name="controle_estoque" class="controle_estoque" value="L" required="" <?php if ($controle_estoque == 'L') {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?>> Controle por Lote (Gado não numerado)
                                                    </label>
                                                </div>
                                            </div>

                                            <?php while ($f = mysqli_fetch_object($fazendas)) {
                                                if (strlen($f->cpf_cnpj) == 11) {
                                                    $f->cpf_cnpj = substr($f->cpf_cnpj, 0, 3) . "." .
                                                        substr($f->cpf_cnpj, 3, 3) . "." .
                                                        substr($f->cpf_cnpj, 6, 3) . "-" .
                                                        substr($f->cpf_cnpj, 9, 2);
                                                } else {
                                                    $f->cpf_cnpj = substr($f->cpf_cnpj, 0, 2) . "." .
                                                        substr($f->cpf_cnpj, 2, 3) . "." .
                                                        substr($f->cpf_cnpj, 5, 3) . "/" .
                                                        substr($f->cpf_cnpj, 8, 4) . "-" .
                                                        substr($f->cpf_cnpj, 12, 2);
                                                }
                                            ?>

                                                <legend>Fazenda 0<?= $count ?></legend>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <span>Necessário para visualização de dados lançados por fazenda.
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="nome_fazenda_01" class="control-label"><span class="required">*</span> Nome da Fazenda</label>

                                                        <input type="text" name="nome_fazenda_01" id="nome_fazenda_01" class="form-control" required="" onkeyup="maiuscula(this)" value="<?= $f->nome; ?>">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label for="cpf_cnpj_01" class="control-label"><span class="required">*</span> CPF ou CNPJ Fazenda</label>

                                                        <input type="text" name="cpf_cnpj_01" id="cpf_cnpj_01" class="form-control" required="" onBlur="valida_cpf_cnpj_01(this);" placeholder="Duplo Click para repetir o mesmo da Empresa ou do Produtor já cadastrado" value="<?= $f->cpf_cnpj; ?>" onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="insc_est_01" class="control-label">Inscrição Estadual Fazenda</label>

                                                        <input type="text" name="insc_est_01" id="insc_est_01" class="form-control" value="<?= $f->insc_est ?>">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-2">
                                                        <label for="cep_01" class="control-label"><span class="required">*</span> CEP Fazenda</label>
                                                        <input name="cep_01" type="text" class="form-control" id="cep_01" required="" value="<?= $f->cep ?>" onkeypress="return numeros(this, event)">
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="endereco_01" class="control-label">Endereço</label>
                                                        <input name="endereco_01" type="text" class="form-control" id="endereco_01" onkeyup="maiuscula(this)" value="<?= $f->endereco ?>">
                                                    </div>

                                                    <div class="form-group col-md-1">
                                                        <label for="num_01" class="control-label">Número</label>
                                                        <input name="num_01" type="text" class="form-control" id="num_01" onkeyup="maiuscula(this)" value="<?= $f->numero ?>">
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="complemento_01" class="control-label">Complemento</label>
                                                        <input name="complemento_01" type="text" class="form-control" id="complemento_01" onkeyup="maiuscula(this)" value="<?= $f->complemento ?>">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label for="bairro_01" class="control-label">Bairro</label>
                                                        <input name="bairro_01" type="text" class="form-control" id="bairro_01" onkeyup="maiuscula(this)" value="<?= $f->bairro ?>">
                                                    </div>

                                                    <div class="form-group col-md-2">
                                                        <label for="estado_01" class="control-label">Estado</label>
                                                        <select class="form-control" id="estado_01" name="estado_01">
                                                            <option value="" selected="selected">...</option>

                                                            <?php while ($registro_estado = mysqli_fetch_object($tab_estados_01)) { ?>
                                                                <option value="<?= $registro_estado->est_codigo_id ?>" <?php
                                                                                                                        if ($registro_estado->est_codigo_id == $f->estado) {
                                                                                                                            echo "selected";
                                                                                                                        }
                                                                                                                        ?>>
                                                                    <?php
                                                                    echo $registro_estado->est_nome;
                                                                    ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label for="cidade_01" class="control-label">Município</label>
                                                        <input name="cidade_01" type="text" class="form-control" id="cidade_01" onkeyup="maiuscula(this)" readonly="" value="<?= $f->municipio ?>">
                                                    </div>

                                                    <div class="form-group col-md-4 selecione_municipio_01">
                                                        <label for="lista_municipio_01" class="control-label">Selecione</label>
                                                        <select class="form-control" name="lista_municipio_01" id="lista_municipio_01">
                                                            <option value="" selected="selected">...</option>
                                                            <?php while ($registro_mun = mysqli_fetch_array($tab_municipios_01)) { ?>
                                                                <option value="<?php echo $registro_mun['mun_nome']; ?>">
                                                                    <?php
                                                                    echo $registro_mun['mun_nome'];
                                                                    ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-3">
                                                        <label for="area_01" class="control-label">Área da Fazenda(Ha)</label>
                                                        <input name="area_01" type="text" class="form-control" id="area_01" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area_01()" value="<?= $f->area ?>">
                                                    </div>

                                                    <div class="form-group col-md-3">
                                                        <label for="area_util_01" class="control-label">Área Útil da Fazenda(Ha)</label>
                                                        <input name="area_util_01" type="text" class="form-control" id="area_util_01" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area_util_01()" value="<?= $f->area_util ?>">
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="localizacao_01" class="control-label">Localização Geografica (Latitude,Logitude)</label>
                                                        <input name="localizacao_01" type="text" class="form-control" id="localizacao_01" value="<?= $f->localizacao ?>">
                                                    </div>
                                                </div>

                                                <fieldset class="scheduler-border">
                                                    <legend class="scheduler-border fonte-legend">Atividades</legend>

                                                    <div class="row">
                                                        <div class="form-group col-md-2">
                                                            <label class="checkbox-inline" for="atv_pec_corte_01">
                                                                <input class="form-check-input" type="checkbox" value="" id="atv_pec_corte_01" name="atv_pec_corte_01" <?php if ($f->atv_pec_corte == 'S') {
                                                                                                                                                                            echo "checked";
                                                                                                                                                                        } ?>> Pecuária de Corte
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-2">
                                                            <label class="checkbox-inline" for="atv_pec_leite_01">
                                                                <input class="form-check-input" type="checkbox" value="" id="atv_pec_leite_01" name="atv_pec_leite_01" <?php if ($f->atv_pec_leite == 'S') {
                                                                                                                                                                            echo "checked";
                                                                                                                                                                        } ?>> Pecuária de Leite
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-2">
                                                            <label class="checkbox-inline" for="atv_agricultura_01">
                                                                <input class="form-check-input" type="checkbox" value="" id="atv_agricultura_01" name="atv_agricultura_01" <?php if ($f->atv_agricultura == 'S') {
                                                                                                                                                                                echo "checked";
                                                                                                                                                                            } ?>> Agricultura
                                                            </label>
                                                        </div>

                                                        <div class="form-group col-md-10">
                                                            <label for="descricao_atv_agricola_01" class="control-label">Atividades Agrícolas</label>
                                                            <textarea name="descricao_atv_agricola_01" type="text" class="form-control" id="descricao_atv_agricola_01" rows="2" onkeyup="maiuscula(this)"><?= $f->descricao_atv_agricola ?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-2">
                                                            <label class="checkbox-inline" for="atv_outra_01">
                                                                <input class="form-check-input" type="checkbox" value="" id="atv_outra_01" name="atv_outra_01" <?php if ($f->atv_outra == 'S') {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                } ?>>Outra
                                                            </label>
                                                        </div>

                                                        <div class="form-group col-md-10">
                                                            <label for="descricao_atv_outra_01" class="control-label">Outras Atividades</label>
                                                            <textarea name="descricao_atv_outra_01" type="text" class="form-control" id="descricao_atv_outra_01" rows="2" onkeyup="maiuscula(this)"><?= $f->descricao_atv_outra ?></textarea>
                                                        </div>
                                                    </div>
                                                </fieldset>

                                            <?php $count++;
                                            } ?>

                                            <legend>Administrador da Fazenda</legend>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <span>Pessoa que irá administrar o sistema.
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="nome_adm" class="control-label"><span class="required">*</span> Nome do Administrador</label>

                                                    <input type="text" name="nome_adm" id="nome_adm" class="form-control" onkeyup="maiuscula(this)" required="" <?php echo "value='" . $nome_adm . "'"; ?>>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="cpf_adm" class="control-label"><span class="required">*</span> CPF</label>

                                                    <input type="text" name="cpf_adm" id="cpf_adm" class="form-control" onBlur="valida_cpf_adm(this);" placeholder="Duplo Click para repetir o mesmo da Empresa ou do Produtor já cadastrado" required="" <?php echo "value='" . $cpf_adm_edi . "'"; ?> onkeypress="return numeros(this, event)">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="email_adm" class="control-label"><span class="required">*</span> E-mail</label>

                                                    <input type="email" name="email_adm" id="email_adm" class="form-control" required="" onkeyup="minuscula(this)" <?php echo "value='" . $email_adm . "'"; ?>>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-1">
                                                    <label for="ddd_adm" class="control-label"><span class="required">*</span> DDD</label>
                                                    <input name="ddd_adm" type="text" class="form-control" id="ddd_adm" required="" maxlength="2" <?php echo "value='" . $ddd_adm . "'"; ?> onkeypress="return numeros(this, event)">
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="telefone_adm" class="control-label"><span class="required">*</span> Telefone</label>

                                                    <input name="telefone_adm" type="text" class="form-control" id="telefone_adm" maxlength="9" required="" <?php echo "value='" . $telefone_adm . "'"; ?> onkeypress="return numeros(this, event)">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <input type="hidden" name="opcao_validar" id="opcao_validar">
                                                    <input type="hidden" name="tipo_gravacao" value="2">
                                                    <input type="hidden" name="codigo_cliente" <?php echo "value='" . $codigo . "'"; ?>>

                                                    <button type="button" class="btn btn-primary" title="Somente Confirma as alterções" data-toggle='tooltip' data-placement='top' onclick="gravar_alteracao(0)">Confirmar Edição</button>

                                                    <button type="button" class="btn btn-success" title="Confirma as alterções e valida o cliente criando os dados no banco de dados" data-toggle='tooltip' data-placement='top' onclick="gravar_alteracao(1)">Autorizar</button>

                                                    <button type="button" class="btn btn-info pull-right sair_programa">Voltar</button>
                                                </div>
                                            </div>
                                        </fieldset>


                                    </div> <!--panel-body -->
                                </div> <!--panel -->
                            </form>
                        </section> <!-- panel-group -->
                    </div> <!--col-lg-12 2-->
                </div> <!--row 2-->

                <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" aria-labelledby=" 
            myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Validar Cliente</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button" onclick="location.reload();">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby=" 
            myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Validar Cliente</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </section> <!-- wrapper -->
        </section><!--main-content -->


        <?php
        $javascript_file_name = 'validar_cliente.js';
        require 'rodape.php';
        ?>