<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link href="css/select-1.13.14.css" rel="stylesheet" >
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style>
    .relfaz-body-flex {
        display: flex;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid #e3e6e8;
        border-radius: 4px;
    }

    .relfaz-sidebar {
        flex: 0 0 240px;
        width: 240px;
        padding: 16px 14px;
        background: #f7f8f9;
        border-right: 1px solid #e3e6e8;
    }

    .relfaz-filtro-titulo {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #939ba2;
        margin: 0 0 6px;
    }

    .relfaz-fazenda-lista {
        max-height: 180px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .relfaz-fazenda-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: normal;
        padding: 6px 2px;
        margin: 0;
        cursor: pointer;
    }

    .relfaz-fazenda-item input {
        margin: 0;
    }

    .relfaz-fazenda-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .relfaz-main {
        flex: 1 1 320px;
        min-width: 0;
    }

    .relfaz-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid #e3e6e8;
    }

    .relfaz-toolbar-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .relfaz-toolbar-nav i {
        font-size: 14px;
        color: #5c6670;
        cursor: pointer;
    }

    /* Diário: ícone de calendário no lugar das setas; o input date fica escondido embaixo do ícone
       (sem tooltip nativo do navegador) e é aberto pelo clique no ícone */
    .relfaz-calendario {
        position: relative;
        display: none;
        width: 18px;
        height: 18px;
        line-height: 18px;
        text-align: center;
    }

    .relfaz-calendario input {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 1px;
        height: 1px;
        opacity: 0;
        border: 0;
        padding: 0;
        pointer-events: none;
    }

    #relfaz_periodo_titulo {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .relfaz-view-switch {
        display: inline-flex;
        background: #eef0f2;
        border-radius: 6px;
        padding: 2px;
    }

    .relfaz-view-btn {
        border: none;
        background: transparent;
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 5px;
        cursor: pointer;
        color: #5c6670;
    }

    .relfaz-view-btn.active {
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.12);
        font-weight: 600;
        color: #222;
    }

    .relfaz-dados-area {
        padding: 14px 16px;
        min-height: 450px;
    }

    .relfaz-fazenda-titulo {
        margin: 0 0 10px;
        font-size: 13px;
        font-weight: 400;
        color: #555;
        text-align: left;
    }

    /* Cards do Diário: modelo B (igual ao desenho sugerido) */
    .relfaz-cards {
        display: flex;
        flex-wrap: wrap;
    }

    .relfaz-cards:before,
    .relfaz-cards:after {
        display: none;
    }

    .relfaz-cards > div {
        display: flex;
        margin-bottom: 15px;
    }

    .relfaz-card {
        width: 100%;
        border: 1px solid #e3e6e8;
        border-radius: 12px;
        background: #fff;
        padding: 14px 16px 8px;
    }

    .relfaz-card-titulo {
        font-size: 14px;
        font-weight: 500;
        color: #333;
        margin-bottom: 6px;
    }

    .relfaz-card-lista {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .relfaz-item {
        font-size: 13px;
        color: #333;
        padding: 7px 0;
    }

    .relfaz-item-linha {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Mudou pasto / Nutrição: seta que abre a lista abaixo do item */
    .relfaz-item-expansivel .relfaz-item-linha {
        cursor: pointer;
    }

    .relfaz-item-seta {
        font-size: 10px;
        color: #8a9097;
        margin-left: 4px;
        transition: transform .15s;
    }

    .relfaz-item-aberto .relfaz-item-seta {
        transform: rotate(180deg);
    }

    .relfaz-item-lista {
        display: none;
        margin: 6px 0 2px 44px;
        padding: 8px 12px;
        background: #f7f8f9;
        border-radius: 8px;
        font-size: 12px;
        color: #555;
    }

    .relfaz-lista-pastos {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .relfaz-lista-pastos li {
        padding: 2px 0;
    }

    .relfaz-lista-nutricao {
        width: 100%;
    }

    .relfaz-lista-nutricao td {
        padding: 2px 8px 2px 0;
        vertical-align: top;
    }

    .relfaz-lista-nutricao td:last-child {
        padding-right: 0;
        white-space: nowrap;
    }

    .relfaz-lista-nutricao tr.relfaz-nutricao-grupo td {
        padding-top: 8px;
    }

    .relfaz-item-icone {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .relfaz-item-texto {
        flex: 1;
        min-width: 0;
    }

    .relfaz-item-inativo .relfaz-item-texto {
        color: #a0a6ab;
    }

    .relfaz-item-detalhe {
        display: block;
        font-size: 11px;
        color: #6c757d;
        margin-top: 2px;
    }

    .relfaz-badge {
        font-size: 11px;
        padding: 3px 9px;
        border-radius: 8px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .relfaz-cor-azul     { background: #E6F1FB; color: #185FA5; }
    .relfaz-cor-roxo     { background: #EEEDFE; color: #534AB7; }
    .relfaz-cor-verde    { background: #E1F5EE; color: #0F6E56; }
    .relfaz-cor-amarelo  { background: #FAEEDA; color: #854F0B; }
    .relfaz-cor-vermelho { background: #FCEBEB; color: #A32D2D; }
    .relfaz-cor-cinza    { background: #f1f2f4; color: #a0a6ab; }

    .relfaz-badge.relfaz-cor-cinza { color: #6c757d; }

    .relfaz-texto-azul  { color: #378ADD; margin-right: 4px; }
    .relfaz-texto-verde { color: #1D9E75; margin-right: 4px; }

    .relfaz-mensagem-vazia {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 420px;
        font-size: 16px;
        color: #939ba2;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .relfaz-sidebar {
            flex: 1 1 100%;
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e3e6e8;
        }

        .relfaz-main {
            flex: 1 1 100%;
        }

        .relfaz-toolbar {
            justify-content: center;
            text-align: center;
        }

        .relfaz-toolbar-nav i {
            font-size: 18px;
        }

        #relfaz_periodo_titulo {
            font-size: 16px;
        }
    }
  </style>
</head>

<body>

<?php
    @ session_start();

    if(isset($_SESSION['menu_relatorios'])) {
        $array_relatorios = explode("!",$_SESSION['menu_relatorios']);

        if ($array_relatorios[0] == 0){
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';
            echo '</div>';
            exit;
        }
    }
    else {
        echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuou o login!</span>';
        echo '</div>';
        exit;
    }

    // Fazendas liberadas para o usuário logado
    $codigo_usuario = (int) $_SESSION['id_usuario'];

    $array_locais_usuario = array();

    $query_usuario = mysqli_query($conector_acesso, "SELECT local_usuario FROM usuario
                                                     WHERE id_usuario = $codigo_usuario AND lixeira_usuario = 0");

    if ($query_usuario && mysqli_num_rows($query_usuario) != 0) {
        $reg_usuario = mysqli_fetch_assoc($query_usuario);

        foreach (explode(',', $reg_usuario['local_usuario']) as $value) {
            $value = trim($value);
            if ($value !== '') {
                $array_locais_usuario[] = $value;
            }
        }
    }

    $tbl_local = mysqli_query($conector, "SELECT tbl_pessoa_id, tbl_pessoa_nome FROM tbl_pessoa
                                          WHERE tbl_pessoa_classe = 4 AND tbl_pessoa_lixeira = 0
                                          ORDER BY tbl_pessoa_id");
?>

<!-- container section start -->
<section id="container" class="">

    <!--sidebar start-->
    <?php
        include "cabecalho.php";
        include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
    ?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i>
            <a href="form_relatorios_produtivos.php" style="color: inherit;">Relatórios Produtivos</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Relatórios da Fazenda</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-file-alt"></i> Relatórios da Fazenda</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">

                    <div class="relfaz-body-flex">
                        <div class="relfaz-sidebar">
                            <div class="relfaz-filtro-titulo">Fazendas</div>
                            <div class="relfaz-fazenda-lista">
                                <?php
                                    // Mesmo layout e mesma paleta de cores da Agenda (seleção única: radio)
                                    $paleta_cores_fazenda = array('#378ADD', '#1D9E75', '#D85A30', '#993556', '#BA7517', '#7F77DD', '#639922', '#4A90A4');
                                    $indice_cor_fazenda = 0;

                                    while ($reg_local = mysqli_fetch_object($tbl_local)) {
                                        if (in_array((string) $reg_local->tbl_pessoa_id, $array_locais_usuario)) {
                                            $cor_fazenda = $paleta_cores_fazenda[$indice_cor_fazenda % count($paleta_cores_fazenda)];
                                            $indice_cor_fazenda++;
                                ?>
                                            <label class="relfaz-fazenda-item">
                                                <input type="radio" name="relfaz_fazenda" class="relfaz-fazenda-radio" value="<?php echo (int) $reg_local->tbl_pessoa_id; ?>">
                                                <span class="relfaz-fazenda-dot" style="background: <?php echo $cor_fazenda; ?>;"></span>
                                                <?php echo htmlspecialchars($reg_local->tbl_pessoa_nome); ?>
                                            </label>
                                <?php
                                        }
                                    }
                                ?>
                            </div>
                        </div>

                        <div class="relfaz-main">
                            <div class="relfaz-toolbar">
                                <div class="relfaz-toolbar-nav">
                                    <button type="button" class="btn btn-default btn-sm" id="relfaz_hoje">Hoje</button>
                                    <i class="fas fa-chevron-left" id="relfaz_anterior" data-toggle="tooltip" data-placement="bottom" title="Período anterior"></i>
                                    <i class="fas fa-chevron-right" id="relfaz_proximo" data-toggle="tooltip" data-placement="bottom" title="Próximo período"></i>
                                    <span id="relfaz_periodo_titulo"></span>
                                    <span class="relfaz-calendario" id="relfaz_calendario">
                                        <i class="far fa-calendar-alt"></i>
                                        <input type="date" id="relfaz_data_dia">
                                    </span>
                                </div>
                                <div class="relfaz-view-switch">
                                    <button type="button" class="relfaz-view-btn active" data-periodo="D">Diário</button>
                                    <button type="button" class="relfaz-view-btn" data-periodo="S">Semanal</button>
                                    <button type="button" class="relfaz-view-btn" data-periodo="M">Mensal</button>
                                </div>
                            </div>

                            <div class="relfaz-dados-area" id="relfaz_dados">
                                <div class="relfaz-mensagem-vazia">Selecione uma Fazenda</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- page end-->
        </section>
    </section>
</section>

<?php
  $javascript_file_name = 'rel_fazenda.js';
  require 'rodape.php';
?>
