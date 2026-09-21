<?php
include "valida_sessao.inc";

ob_start();
header('Content-Type: text/html; charset=utf-8');
require_once "inc/config.php";

@session_start();
$cnpj_cliente = '97174041604';
$_SESSION['id_cliente'] = $cnpj_cliente;

$banco = $cnpj_cliente;

$conector = mysqli_connect(SERVIDOR, USUARIO, SENHA, $banco);

if (mysqli_connect_error()) {
    printf("Falha na conexão: ", mysqli_connect_error());
    exit();
}

$bancoSelecionenado = mysqli_select_db($conector, $banco);

if ($bancoSelecionenado === FALSE) {
    exit("Falha na seleção do banco de dados: " . mysqli_error($conector));
}

mysqli_query($conector, "SET NAMES 'utf8'");
mysqli_query($conector, 'SET character_set_connection=utf8');
mysqli_query($conector, 'SET character_set_client=utf8');
mysqli_query($conector, 'SET character_set_results=utf8');

$data_sistema = date("Y-m-d");
$icone_empresa = 'img/boi_virtual_branco.ico';

$tab_estados = mysqli_query($conector, "select * from tabela_estados");
$tab_municipios = mysqli_query($conector, "select * from tabela_municipios
        where mun_estado='MG'");

$tab_estados_01 = mysqli_query($conector, "select * from tabela_estados");
$tab_municipios_01 = mysqli_query($conector, "select * from tabela_municipios
        where mun_estado='MG'");

$tab_estados_02 = mysqli_query($conector, "select * from tabela_estados");
$tab_municipios_02 = mysqli_query($conector, "select * from tabela_municipios
        where mun_estado='MG'");

$tab_estados_03 = mysqli_query($conector, "select * from tabela_estados");
$tab_municipios_03 = mysqli_query($conector, "select * from tabela_municipios
        where mun_estado='MG'");

$tab_estados_04 = mysqli_query($conector, "select * from tabela_estados");
$tab_municipios_04 = mysqli_query($conector, "select * from tabela_municipios
        where mun_estado='MG'");

$tab_estados_05 = mysqli_query($conector, "select * from tabela_estados");
$tab_municipios_05 = mysqli_query($conector, "select * from tabela_municipios
        where mun_estado='MG'");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
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

    <style>
        .input_vazio {
            border-color: red !important;
        }
    </style>
</head>

<body>
    <!-- container section start -->
    <section id="container" class="">

        <header class="header dark-bg">
            <a href="#" class="logo">
                <img src="<?php echo $icone_empresa; ?>" alt="" class="logo-img">
                <span class="nome_empresa lite">BOI VIRTUAL</span>
            </a>
        </header>

        <section id="main-content">
            <section class="wrapper" style="margin-left: 5px;">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="page-header"><i class="fa fa-address-card-o"></i> CADASTRO DE NOVO CLIENTE</h3>
                            </div>
                        </div>

                        <div class="row">
                            <div class="tab-panel">
                                <div class="tab-pane active" id="consulta_contas">
                                    <fieldset class="scheduler-border" id="dados_consulta">
                                        <legend class="scheduler-border fonte-legend">Formulário</legend>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <span style="font-size: 16px;">
                                                    O objetivo deste formulário é levantar
                                                    dados necessários para cadastro
                                                    das Fazendas e Usuários do Software "Boi Virtual".
                                                    Os dados aqui informados não serão compartilhados.
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-9">
                                                <p style="font-size: 16px;">Equipe Boi Virtual</p>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <button type="button" class="btn btn-info pull-right sair_programa">
                                                    Sair
                                                </button>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="col-md-12" style="display: none;" id="empresa_produtor_info">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <h3>Empresa ou Produtor</h3>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <a class='btn pull-right' href='#' id="edit_produtor_info" style="margin-top: 1em;">
                                                                    <i class='icon_pencil-edit' data-toggle='tooltip' data-placement='left' title='Editar'></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p id="nomeCpfProdutor" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p id="enderecoProdutor" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p id="cepProdutor" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p id="BCEProdutor" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p id="controlTypeFazendas" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p>&nbsp;</p>
                                                </div>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="col-md-12" style="display: none;" id="userp_info">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <h3>Usuário Principal</h3>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <a class='btn pull-right' href='#' id="edit_userp_info" style="margin-top: 1em;">
                                                                <i class='icon_pencil-edit' data-toggle='tooltip' data-placement='left' title='Editar'></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <p id="nomeCpfUserp" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p id="emailUserp" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p id="telefoneUserp" style="margin-bottom: 0.2em; font-size: 9pt;"></p>
                                                    <p style="margin-bottom: 0.2em; font-size: 9pt;">&nbsp;</p>
                                                    <p style="margin-bottom: 0.2em; font-size: 9pt;">&nbsp;</p>
                                                    <p style="margin-bottom: 0.2em; font-size: 9pt;">&nbsp;</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="col-md-12" style="display: none;" id="fazenda_info">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <h3>Fazendas</h3>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <a class='btn pull-right' href='#' id="edit_fazenda_info" style="margin-top: 1em;">
                                                                <i class='icon_pencil-edit' data-toggle='tooltip' data-placement='left' title='Editar'></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="row" id="fazenda_edit">
                                                    </div>
                                                    <p></p>
                                                </div>
                                            </div>
                                        </div>

                                        <form method="POST" action="#" enctype="multipart/form-data" id="firstForm">

                                            <div class="tab-panel">
                                                <div class="tab-pane active">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h2>
                                                                <strong>
                                                                    Empresa ou Produtor
                                                                </strong>
                                                            </h2>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="nome_empresa" class="control-label">
                                                                <span class="required">*</span> Nome da Empresa ou Produtor
                                                            </label>

                                                            <input type="text" name="nome_empresa" id="nome_empresa" class="form-control" required onkeyup="maiuscula(this)" onblur="repeatName(this.value)" onchange="checkRequiredInput(this.value, event)" />
                                                        </div>

                                                        <div class=" form-group col-md-6">
                                                            <label for="nome_fantasia" class="control-label">
                                                                <span class="required">*</span> Nome Fantasia
                                                            </label>

                                                            <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control" required onkeyup="maiuscula(this)" onchange="checkRequiredInput(this.value, event)" />
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="cpf_cnpj" class="control-label">
                                                                <span class="required">*</span> CPF ou CNPJ
                                                            </label>

                                                            <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control" required onBlur="valida_cpf_cnpj(this);" onkeypress="return numeros(this, event)" onchange="checkRequiredInput(this.value, event)" />
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="insc_est" class="control-label">
                                                                Inscrição Estadual (se houver)
                                                            </label>

                                                            <input type="text" name="insc_est" id="insc_est" class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-2">
                                                            <label for="cep_pessoa" class="control-label">
                                                                <span class="required">*</span> CEP
                                                            </label>
                                                            <input name="cep_pessoa" type="text" class="form-control" id="cep_pessoa" required onkeypress="return numeros(this, event)" onchange="checkRequiredInput(this.value, event)" />
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="endereco_pessoa" class="control-label">
                                                                Endereço
                                                            </label>
                                                            <input name="endereco_pessoa" type="text" class="form-control" id="endereco_pessoa" onkeyup="maiuscula(this)" />
                                                        </div>

                                                        <div class="form-group col-md-1">
                                                            <label for="num_pessoa" class="control-label">
                                                                Número
                                                            </label>
                                                            <input name="num_pessoa" type="text" class="form-control" id="num_pessoa" onkeyup="maiuscula(this)" maxlength="10" />
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="complemento_pessoa" class="control-label">
                                                                Complemento
                                                            </label>
                                                            <input name="complemento_pessoa" type="text" class="form-control" id="complemento_pessoa" onkeyup="maiuscula(this)" />
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-4">
                                                            <label for="bairro_pessoa" class="control-label">
                                                                Bairro
                                                            </label>
                                                            <input name="bairro_pessoa" type="text" class="form-control" id="bairro_pessoa" onkeyup="maiuscula(this)" />
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label for="estado_pessoa" class="control-label">Estado</label>
                                                            <select class="form-control" id="estado_pessoa" name="estado_pessoa">
                                                                <option value="" selected="selected">...</option>
                                                                <?php
                                                                while ($registro_estado = mysqli_fetch_object($tab_estados_01)) {
                                                                ?>
                                                                    <option value="<?= $registro_estado->est_codigo_id ?>">
                                                                        <?= $registro_estado->est_nome; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-4 col-sm-11 col-xs-10">
                                                            <label for="cidade_pessoa" class="control-label">Município</label>
                                                            <input name="cidade_pessoa" type="text" class="form-control" id="cidade_pessoa" onkeyup="maiuscula(this)" readonly />
                                                        </div>

                                                        <div class="form-group col-md-4 col-sm-1 col-xs-1 selecione_municipio">
                                                            <label for="lista_municipio" class="control-label">Selecione</label>
                                                            <select class="form-control" name="lista_municipio" id="lista_municipio">
                                                                <option value="" selected="selected">...</option>
                                                                <?php
                                                                while ($registro_mun = mysqli_fetch_array($tab_municipios)) { ?>
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
                                                        <div class="form-group col-md-10">
                                                            <label for="controle_estoque" class="control-label">
                                                                <span class="required">*</span> Controle de Estoque de Animais?
                                                            </label>

                                                            <div class="clearfix"></div>

                                                            <label class="radio-inline">
                                                                <input type="radio" name="controle_estoque" class="controle_estoque" value="L" required />
                                                                <strong>
                                                                    Controle por Lote (Gado não numerado)
                                                                </strong>
                                                            </label>

                                                            <label class="radio-inline">
                                                                <input type="radio" name="controle_estoque" class="controle_estoque" value="I" required />
                                                                <strong>
                                                                    Controle Individual (Gado numerado com Identificação Individual)
                                                                </strong>
                                                            </label>
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label for="qtdeFazenda">
                                                                <span class="required">*</span>
                                                                Qtde de Fazendas
                                                            </label>

                                                            <input type="text" name="qtdeFazenda" id="qtdeFazenda" class="form-control" onkeypress="return numeros(this, event)" onchange="checkRequiredInput(this.value, event)" required />
                                                        </div>
                                                    </div>

                                                    <div class=" row">
                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-primary" id="submitFirstForm">Confirma</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <form action="#" method="post" enctype="multipart/form-data" id="secondForm" style="display: none;">

                                            <div class="tab-panel">
                                                <div class="tab-pane active">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h2>
                                                                <strong>
                                                                    Usuário Principal
                                                                </strong>
                                                            </h2>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="userp_nome" class="control-label">
                                                                <span class="required">*</span> Nome
                                                            </label>

                                                            <input type="text" name="userp_nome" id="userp_nome" class="form-control" required onkeyup="maiuscula(this)" onchange="checkRequiredInput(this.value, event)">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="userp_cpf" class="control-label">
                                                                <span class="required">*</span> CPF
                                                            </label>

                                                            <input type="text" class="form-control" id="userp_cpf" name="userp_cpf" onBlur="valida_cpf_cnpj(this);" required onkeyup="return numeros(this, event)" maxlength="11" onchange="checkRequiredInput(this.value, event)">
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="userp_email" class="control-label">
                                                                <span class="required">*</span> E-mail
                                                            </label>

                                                            <input type="email" class="form-control" id="userp_email" name="userp_email" required onchange="checkRequiredInput(this.value, event)">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-1">
                                                            <label for="userp_ddd" class="control-label">
                                                                <span class="required">*</span> DDD
                                                            </label>

                                                            <input type="text" name="userp_ddd" id="userp_ddd" class="form-control" required onkeypress="return numeros(this, event)" maxlength="2" onchange="checkRequiredInput(this.value, event)">
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label for="userp_telefone" class="control-label">
                                                                <span class="required">*</span> Telefone
                                                            </label>

                                                            <input type="text" name="userp_telefone" id="userp_telefone" class="form-control" required onkeypress="return numeros(this, event)" maxlength="9" onchange="checkRequiredInput(this.value, event)">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-primary" id="submitSecondForm">Confirma</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>

                                        <form action="#" method="post" enctype="multipart/form-data" id="thirdForm" style="display: none;">
                                            <div class="tab-panel">
                                                <div class="tab-pane active">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h2>
                                                                <strong id="fazenda_titulo">
                                                                    Fazenda 01
                                                                </strong>
                                                            </h2>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="nome_fazenda" class="control-label">
                                                                <span class="required">*</span> Nome
                                                            </label>

                                                            <input type="text" name="nome_fazenda" id="nome_fazenda" class="form-control" required onkeyup="maiuscula(this)" onchange="checkRequiredInput(this.value, event)">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="fazenda_cpf_cnpj" class="control-label">
                                                                <span class="required">*</span> CPF ou CNPJ
                                                            </label>

                                                            <input type="text" name="fazenda_cpf_cnpj" id="fazenda_cpf_cnpj" class="form-control" required onBlur="valida_cpf_cnpj(this);" onkeypress="return numeros(this, event)" onchange="checkRequiredInput(this.value, event)">
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="fazenda_insc_est" class="control-label">
                                                                Inscrição Estadual
                                                            </label>

                                                            <input type="text" name="fazenda_insc_est" id="fazenda_insc_est" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-2">
                                                            <label for="fazenda_cep" class="control-label">
                                                                <span class="required">*</span> CEP
                                                            </label>
                                                            <input name="fazenda_cep" type="text" class="form-control" id="fazenda_cep" required="" onkeypress="return numeros(this, event)" onchange="checkRequiredInput(this.value, event)">
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label for="fazenda_endereco" class="control-label">
                                                                Endereço
                                                            </label>
                                                            <input name="fazenda_endereco" type="text" class="form-control" id="fazenda_endereco" onkeyup="maiuscula(this)">
                                                        </div>

                                                        <div class="form-group col-md-1">
                                                            <label for="fazenda_num" class="control-label">
                                                                Número
                                                            </label>
                                                            <input name="fazenda_num" type="text" class="form-control" id="fazenda_num" onkeyup="maiuscula(this)" maxlength="10">
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="fazenda_complemento" class="control-label">
                                                                Complemento
                                                            </label>
                                                            <input name="fazenda_complemento" type="text" class="form-control" id="fazenda_complemento" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-4">
                                                            <label for="fazenda_bairro" class="control-label">
                                                                Bairro
                                                            </label>
                                                            <input name="fazenda_bairro" type="text" class="form-control" id="fazenda_bairro" onkeyup="maiuscula(this)">
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label for="fazenda_estado" class="control-label">Estado</label>
                                                            <select class="form-control" id="fazenda_estado" name="fazenda_estado">
                                                                <option value="" selected="selected">...</option>
                                                                <?php
                                                                while ($registro_estado = mysqli_fetch_object($tab_estados)) {
                                                                ?>
                                                                    <option value="<?php echo $registro_estado->est_codigo_id ?>">
                                                                        <?php echo $registro_estado->est_nome; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-4 col-sm-11 col-xs-10">
                                                            <label for="fazenda_cidade" class="control-label">Município</label>
                                                            <input name="fazenda_cidade" type="text" class="form-control" id="fazenda_cidade" onkeyup="maiuscula(this)" readonly="">
                                                        </div>

                                                        <div class="form-group col-md-4 col-sm-1 col-xs-1 selecione_municipio_01">
                                                            <label for="fazenda_lista_municipio" class="control-label">Selecione</label>
                                                            <select class="form-control" name="fazenda_lista_municipio" id="fazenda_lista_municipio">
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
                                                        <div class="form-group col-md-3">
                                                            <label for="fazenda_area" class="control-label">
                                                                <span class="required">*</span> Área da Fazenda(Ha)
                                                            </label>
                                                            <input name="fazenda_area" type="text" class="form-control" id="fazenda_area" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area()" onchange="checkRequiredInput(this.value, event)">
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="fazenda_area_util" class="control-label">
                                                                <span class="required">*</span> Área Útil da Fazenda(Ha)
                                                            </label>
                                                            <input name="fazenda_area_util" type="text" class="form-control" id="fazenda_area_util" placeholder="0,00" onkeypress="digita_valor()" onblur="exibe_area_util()" onchange="checkRequiredInput(this.value, event)">
                                                        </div>

                                                        <!--<div class="form-group col-md-6">
                                                            <label for="fazenda_localizacao" class="control-label">Localização Geografica (fazenda_latitude,Logitude)</label>
                                                            <input name="fazenda_localizacao" type="text" class="form-control" id="fazenda_localizacao">
                                                        </div>-->

                                                        <div class="form-group col-md-3">
                                                            <label for="fazenda_latitude" class="control-label">Latitude</label>
                                                            <input name="fazenda_latitude" type="text" class="form-control" id="fazenda_latitude">
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            <label for="fazenda_longitude" class="control-label">Longitude</label>
                                                            <input name="fazenda_longitude" type="text" class="form-control" id="fazenda_longitude">
                                                        </div>

                                                    </div>

                                                    <fieldset class="scheduler-border">
                                                        <legend class="scheduler-border fonte-legend">Atividades</legend>

                                                        <div class="row">
                                                            <div class="form-group col-md-2">
                                                                <label class="checkbox-inline" for="atv_pec_corte">
                                                                    <input class="form-check-input" type="checkbox" value="" id="atv_pec_corte" name="atv_pec_corte"> Pecuária de Corte
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-2">
                                                                <label class="checkbox-inline" for="atv_pec_leite">
                                                                    <input class="form-check-input" type="checkbox" value="" id="atv_pec_leite" name="atv_pec_leite"> Pecuária de Leite
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-2">
                                                                <label class="checkbox-inline" for="atv_agricultura">
                                                                    <input class="form-check-input" type="checkbox" value="" id="atv_agricultura" name="atv_agricultura"> Agricultura
                                                                </label>
                                                            </div>

                                                            <div class="form-group col-md-10">
                                                                <label for="descricao_atv_agricola" class="control-label">Atividades Agrícolas</label>
                                                                <textarea name="descricao_atv_agricola" type="text" class="form-control" id="descricao_atv_agricola" rows="2" onkeyup="maiuscula(this)"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="form-group col-md-2">
                                                                <label class="checkbox-inline" for="atv_outra">
                                                                    <input class="form-check-input" type="checkbox" value="" id="atv_outra" name="atv_outra">Outra
                                                                </label>
                                                            </div>

                                                            <div class="form-group col-md-10">
                                                                <label for="descricao_atv_outra" class="control-label">Outras Atividades</label>
                                                                <textarea name="descricao_atv_outra" type="text" class="form-control" id="descricao_atv_outra" rows="2" onkeyup="maiuscula(this)"></textarea>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-primary" id="submitThirdForm">Confirma</button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="tab-panel" style="display: none;" id="div_enviar_formulario">
                                            <div class="tab-panel active">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button class="btn btn-success" id="enviar_formulario">Enviar Formulário</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>



                <!-- page end-->

                <div class="modal fade" id="edit_fazenda_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Editar Fazenda</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group col-md-12">
                                    <label for="s_fazenda_edit" class="control-label">Selecione:</label>
                                    <select name="s_fazenda_edit" class="form-control" id="s_fazenda_edit">
                                        <option value="">...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-primary" id="select_fazenda_edit" type="button">Selecionar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Formulário de Cadastro - Erro</h4>
                            </div>
                            <div class="modal-body">
                                <div id="log_error"></div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="msg_gravar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Formulário de Cadastro - Envio</h4>
                            </div>
                            <div class="modal-body">
                                <div id="mensagem_gravar"></div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default sair_programa" type="button">Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </section>

        <div class="text-center">
            <div class="credits">
                <font size="2">
                    <p style="color:#C0C0C0">Copyright &copy; Agrolandes 2023</p>
                </font>
            </div>
        </div>

    </section> <!-- container section start end -->

    <!-- javascripts -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="js/solicitar_cadastro.js?<?php echo Versao; ?>" type="text/javascript"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            //if (jQuery('#sidebar > ul').is(":visible") === true) {
            jQuery('#main-content').css({
                'margin-left': '0px'
            });
            jQuery('#sidebar').css({
                'margin-left': '-180px'
            });
            jQuery('#sidebar > ul').hide();
            jQuery("#container").addClass("sidebar-closed");
            //}

            $(".sair_programa").click(function() {
                location.href = "../index.php";
            });

            $("#nome_fantasia").dblclick(function() {
                var nome = $("#nome_empresa").val();
                $("#nome_fantasia").val(nome);
            });

            function limpa_formulário_cep(input) {
                input[0].value = "";
            }

            $("#cep_pessoa").blur(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if (validacep.test(cep)) {
                        $("#endereco_pessoa").val("...");
                        $("#num_pessoa").val("...");
                        $("#complemento_pessoa").val("...");
                        $("#bairro_pessoa").val("...");
                        $("#cidade_pessoa").val("...");
                        $("#estado_pessoa").val("");
                        $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                $("#endereco_pessoa").val(dados.logradouro.toUpperCase());
                                $("#num_pessoa").val("");
                                $("#complemento_pessoa").val("");
                                $("#bairro_pessoa").val(dados.bairro.toUpperCase());
                                $("#cidade_pessoa").val(dados.localidade.toUpperCase());
                                $("#estado_pessoa").val(dados.uf);
                                //$("#ibge").val(dados.ibge);

                                $("select[name=lista_municipio]").html('<option value="">Carregando...</option>');

                                $.post("lista_municipios.php", {
                                        estado: dados.uf
                                    },
                                    function(valor) {
                                        $("select[name=lista_municipio]").html(valor);
                                    });

                                $('#num_pessoa').focus();
                            } else {
                                limpa_formulário_cep($("#cep_pessoa"));
                                alert("CEP não encontrado.");
                            }
                        });
                    } else {
                        limpa_formulário_cep($("#cep_pessoa"));
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html("Formato de CEP inválido.");
                    }
                } else {
                    limpa_formulário_cep($("#cep_pessoa"));
                }
            });

            $("select[name=estado_pessoa]").change(function() {
                $("select[name=lista_municipio]").html('<option value="">Aguarde...</option>');
                $("#cidade_pessoa").val("");
                var estado = $(this).val();

                $.post("lista_municipios.php", {
                        estado: estado
                    },
                    function(valor) {

                        $("select[name=lista_municipio]").html(valor);
                    });

                //tout = setTimeout('exibe_cidade()', 1000);
            });

            $("#fazenda_cep").blur(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if (validacep.test(cep)) {
                        $("#fazenda_endereco").val("...");
                        $("#fazenda_num").val("...");
                        $("#fazenda_complemento").val("...");
                        $("#fazenda_bairro").val("...");
                        $("#fazenda_cidade").val("...");
                        $("#fazenda_estado").val("");
                        $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                $("#fazenda_endereco").val(dados.logradouro.toUpperCase());
                                $("#fazenda_num").val("");
                                $("#fazenda_complemento").val("");
                                $("#fazenda_bairro").val(dados.bairro.toUpperCase());
                                $("#fazenda_cidade").val(dados.localidade.toUpperCase());
                                $("#fazenda_estado").val(dados.uf);
                                //$("#ibge").val(dados.ibge);

                                $("select[name=fazenda_lista_municipio]").html('<option value="">Carregando...</option>');

                                $.post("lista_municipios.php", {
                                        estado: dados.uf
                                    },
                                    function(valor) {
                                        $("select[name=fazenda_lista_municipio]").html(valor);
                                    });

                                $('#fazenda_num').focus();
                            } else {
                                limpa_formulário_cep($("#fazenda_cep"));
                                alert("CEP não encontrado.");
                            }
                        });
                    } else {
                        limpa_formulário_cep($("#fazenda_cep"));
                        $("#mensagem_erro").modal();
                        $("#mensagem_erro .modal-body").html("Formato de CEP inválido.");
                    }
                } else {
                    limpa_formulário_cep($("#fazenda_cep"));
                }
            });

            $("select[name=fazenda_estado]").change(function() {
                $("select[name=fazenda_lista_municipio]").html('<option value="">Aguarde...</option>');
                $("#fazenda_cidade").val("");
                var estado = $(this).val();

                $.post("lista_municipios.php", {
                        estado: estado
                    },
                    function(valor) {

                        $("select[name=fazenda_lista_municipio]").html(valor);
                    });

                //tout = setTimeout('exibe_cidade()', 1000);
            });

            $('#lista_municipio').change(function() {
                var municipio_Selecioneando = $('#lista_municipio').val();
                $("#cidade_pessoa").val(municipio_Selecioneando);
                $("#lista_municipio").val('');
            });
        });

        // a função principal de validação CPF e CNPJ
        function valida_cpf_cnpj(obj) { // recebe um objeto
            var s = (obj.value).replace(/\D/g, '');

            if (s == "") {
                return false;
            }

            var tam = (s).length; // removendo os caracteres não numéricos
            if (!(tam == 11 || tam == 14)) { // validando o tamanho
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html("'" + s + "' Não é um CPF ou um CNPJ válido!");

                // alert("'"+s+"' Não é um CPF ou um CNPJ válido!" ); // tamanho inválido
                obj.value = "";
                obj.focus();
                return;
            }

            if (tam == 11) {
                if (!validaCPF(s)) { // chama a função que valida o CPF
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html("'" + s + "' Não é um código CPF válido!");
                    //alert("'"+s+"' Não é um código CPF válido!" ); // se quiser mostrar o erro
                    obj.value = "";
                    obj.focus();
                    return false;
                } else {
                    obj.value = maskCPF(s); // se validou o CPF mascaramos corretamente
                    return true;
                }
            } else if (tam == 14) {
                if (!validaCNPJ(s)) { // chama a função que valida o CNPJ
                    $("#mensagem_erro").modal();
                    $("#mensagem_erro .modal-body").html("'" + s + "' Não é um código CNPJ válido!");
                    //alert("'"+s+"' Não é um código CNPJ válido!" ); // se quiser mostrar o erro
                    obj.value = "";
                    obj.focus();
                    return false;
                } else {
                    obj.value = maskCNPJ(s); // se validou o CNPJ mascaramos corretamente
                    return true;
                }
            } else {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html("CPF/CNPJ Inválido!");
                //alert("CPF/CNPJ Inválido");
                obj.value = "";
                obj.focus();
                return false;
            }
        }
        // fim da funcao valida_cpf_cnpj()

        // função que valida CPF
        function validaCPF(s) {
            var c = s.substr(0, 9);
            var dv = s.substr(9, 2);
            var d1 = 0;
            for (var i = 0; i < 9; i++) {
                d1 += c.charAt(i) * (10 - i);
            }
            if (d1 == 0) return false;
            d1 = 11 - (d1 % 11);
            if (d1 > 9) d1 = 0;
            if (dv.charAt(0) != d1) {
                return false;
            }
            d1 *= 2;
            for (var i = 0; i < 9; i++) {
                d1 += c.charAt(i) * (11 - i);
            }
            d1 = 11 - (d1 % 11);
            if (d1 > 9) d1 = 0;
            if (dv.charAt(1) != d1) {
                return false;
            }
            return true;
        }

        // Função que valida CNPJ
        function validaCNPJ(CNPJ) {
            var a = new Array();
            var b = new Number;
            var c = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            for (i = 0; i < 12; i++) {
                a[i] = CNPJ.charAt(i);
                b += a[i] * c[i + 1];
            }
            if ((x = b % 11) < 2) {
                a[12] = 0
            } else {
                a[12] = 11 - x
            }
            b = 0;
            for (y = 0; y < 13; y++) {
                b += (a[y] * c[y]);
            }
            if ((x = b % 11) < 2) {
                a[13] = 0;
            } else {
                a[13] = 11 - x;
            }
            if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])) {
                return false;
            }
            return true;
        }

        //  função que mascara o CPF
        function maskCPF(CPF) {
            var cpf_cnpj = CPF;
            cpf_cnpj_editado = cpf_cnpj.substring(0, 3) + "." +
                cpf_cnpj.substring(3, 6) + "." +
                cpf_cnpj.substring(6, 9) + "-" +
                cpf_cnpj.substring(9, 11);

            return cpf_cnpj_editado;
        }

        //  função que mascara o CPF de registros lidos do banco de dados
        function maskCPFA(CPF) {
            return CPF.substring(3, 6) + "." + CPF.substring(6, 9) + "." + CPF.substring(9, 12) + "-" + CPF.substring(12, 14);
        }


        //  função que mascara o CNPJ
        function maskCNPJ(CNPJ) {
            return CNPJ.substring(0, 2) + "." + CNPJ.substring(2, 5) + "." + CNPJ.substring(5, 8) + "/" + CNPJ.substring(8, 12) + "-" + CNPJ.substring(12, 14);
        }

        /** permite digitar somente numeros nos campos numericos */
        function numeros(field, event) {
            var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

            if ((keyCode >= 48 && keyCode <= 57) || (keyCode == 8) || (keyCode == 9) || (keyCode == 13) || (keyCode == 46)) {
                if (keyCode == 13) {
                    var i;
                    for (i = 0; i < field.form.elements.length; i++)
                        if (field == field.form.elements[i])
                            break;
                    i = (i + 1) % field.form.elements.length;
                    field.form.elements[i].focus();
                    return false;
                } else
                    return true;
            } else {
                return false;
            }
        }

        function desabilita_enter(field, event) {
            var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

            if (keyCode == 13) {
                var i;
                for (i = 0; i < field.form.elements.length; i++)
                    if (field == field.form.elements[i])
                        break;
                i = (i + 1) % field.form.elements.length;
                field.form.elements[i].focus();
                return false;
            } else
                return true;
        }

        function digita_valor() {
            $('#fazenda_area').bind('keypress', mask.money);
            $('#fazenda_area_util').bind('keypress', mask.money);
        }

        function exibe_area() {
            var fazenda_area = $("#fazenda_area").val();
            if (verifica_virgula(fazenda_area) == ',') {
                fazenda_area = replace_valor(fazenda_area);
            }

            $("#fazenda_area").val(formatMoney(fazenda_area));
        }

        function exibe_area_util() {
            var fazenda_area_util = $("#fazenda_area_util").val();
            if (verifica_virgula(fazenda_area_util) == ',') {
                fazenda_area_util = replace_valor(fazenda_area_util);
            }

            $("#fazenda_area_util").val(formatMoney(fazenda_area_util));
        }

        var mask = {
            money: function() {
                var el = this,
                    exec = function(v) {
                        v = v.replace(/\D/g, "");
                        v = new String(Number(v));
                        var len = v.length;
                        if (1 == len)
                            v = v.replace(/(\d)/, "0.0$1");
                        else if (2 == len)
                            v = v.replace(/(\d)/, "0.$1");
                        else if (len > 2) {
                            v = v.replace(/(\d{2})$/, '.$1');
                        }
                        return v;
                    };

                setTimeout(function() {
                    el.value = exec(el.value);
                }, 1);
            }
        }

        var mask2 = {
            money: function() {
                var el = this,
                    exec = function(v) {
                        v = v.replace(/\D/g, "");
                        v = new String(Number(v));
                        var len = v.length;
                        if (1 == len)
                            v = v.replace(/(\d)/, "0.0$1");
                        else if (2 == len)
                            v = v.replace(/(\d)/, "0.$1");
                        else if (3 == len)
                            v = v.replace(/(\d)/, "0.$1");
                        else if (len > 3) {
                            v = v.replace(/(\d{3})$/, '.$1');
                        }
                        return v;
                    };

                setTimeout(function() {
                    el.value = exec(el.value);
                }, 1);
            }
        }

        function formatMoney(n, c, d, t) {
            c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        }

        function formatMoney2(n, c, d, t) {
            c = isNaN(c = Math.abs(c)) ? 3 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        }

        function replace_valor(valor_replace) {
            valor_replace = valor_replace.replace(".", "");
            valor_replace = valor_replace.replace(".", "");
            valor_replace = valor_replace.replace(".", "");
            valor_replace = valor_replace.replace(",", ".");
            return valor_replace;
        }

        function verifica_virgula(vlr) {
            var virgula = '';

            for (i = 0; i < vlr.length; i++) {
                if (vlr.charAt(i) == ',') {
                    virgula = ',';
                }
            }
            return virgula;
        }
    </script>

</body>

</html>