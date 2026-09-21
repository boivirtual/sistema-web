<?php
    // Editar movimentações venda/transferencia
    // Chamada dos programas lista_animais_transferencia.php e
    //                       form_lista_movimentacao_sem_finalizar.php
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

  <!-- Bootstrap CSS 
  <link href="css/jquery-ui.css" rel="stylesheet" />-->
  <link href="css/bootstrap.min.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/bootstrap-theme.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
  <link href="css/style-responsive.css?<?php echo Versao; ?>" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style type="text/css">
    .label_descriao{
      font-weight: 600;
      text-align: left !important;
    }

    .bootstrap-select > .dropdown-toggle:hover,
    .bootstrap-select > .dropdown-toggle:focus {
        background-color: #fff !important; /* Cor de fundo branca */
        color: #333 !important; /* Cor do texto padrão (preto/cinza escuro) */
        outline: none !important; /* Remove qualquer outline de foco azul/cinza */
        box-shadow: none !important; /* Remove qualquer sombra de foco */
    }

    /* 2. Garante que o estado 'ativo' (enquanto a lista está aberta) também não fique cinza */
    .bootstrap-select.open > .dropdown-toggle {
        background-color: #fff !important;
        color: #333 !important; /* Cor do texto padrão (preto/cinza escuro) */
    }

    /* Opcional: Se a cor de fundo estiver sendo aplicada ao componente inteiro */
    .bootstrap-select.btn-group .dropdown-toggle:hover {
        background-color: #fff !important;
        color: #333 !important; /* Cor do texto padrão (preto/cinza escuro) */
    }  

    .selectpicker-erro .dropdown-toggle {
    border: 1px solid red !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.25) !important;
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
        padding: 0; /* Remove padding interno que possa atrapalhar */
        border: none;
        color: #007aff;
        background: transparent;
        font-size: 13px;
        font-weight: 500;        
    }
</style>

</head>

<body>
    <?php
        @ session_start();   

        if(isset($_SESSION['menu_manejo_animais'])) {
            $array_cadastro = explode("!",$_SESSION['menu_manejo_animais']);

            if ($array_cadastro[2] == 0){
                echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
                echo '<strong class="negrito">Atenção! </strong><span>Você não tem acesso a esse programa!</span>';  
                echo '</div>';         
                exit;
            }
        }
        else {
            echo '<div class="alert alert-danger alert_erro" id="alert_erro" >';
            echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';  
            echo '</div>';         
            exit;
        }

        $controle_estoque = $_SESSION['controle_estoque'];

        $numero_mov = $_REQUEST['id'];

        $tbl_movimentacao = mysqli_query($conector, "select * from tbl_movimentacao
            where tbl_movimentacao_id ='$numero_mov'"); 

        $reg_mov = mysqli_fetch_object($tbl_movimentacao);

        $nome_inclusao = $reg_mov->tbl_movimentacao_incluido_por;
        $data_movimentacao = $reg_mov->tbl_movimentacao_data;
        $data_inclusao = new DateTime($reg_mov->tbl_movimentacao_incluido_em);
        $incluido_por = $nome_inclusao . ' em ' . $data_inclusao->format('d/m/Y');

        $data_emissao = new DateTime($reg_mov->tbl_movimentacao_data);
        $data_emissao_edi = $data_emissao->format('d/m/Y');

        $codigo_local_origem = $reg_mov->tbl_movimentacao_codigo_local_origem;
        $codigo_local_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;
        $codigo_pesagem = $reg_mov->tbl_movimentacao_codigo_pesagem;
        $peso_kg = $reg_mov->tbl_movimentacao_peso_kg;
        $peso_arroba = $reg_mov->tbl_movimentacao_peso_arroba;
        $peso_medio_kg = $reg_mov->tbl_movimentacao_peso_medio_kg;
        $peso_medio_arroba = $reg_mov->tbl_movimentacao_peso_medio_arroba;
        $filtros=$reg_mov->tbl_movimentacao_filtros;
        $tipo_movimentacao = $reg_mov->tbl_movimentacao_tipo;

        $animais_listados = intval($reg_mov->tbl_movimentacao_qtd_animais_pesados);

        $rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
            WHERE tbl_ite_movimentacao_numero_id ='$numero_mov' AND 
                  tbl_ite_movimentacao_selecionado = 'S'");

        $animais_selecionados =  intval(mysqli_num_rows($rs));

        $checkBox = ''; 
        if ($animais_listados == $animais_selecionados) {
            $checkBox = 'checked';
        }

        switch ($tipo_movimentacao) {
            case 003:
                $tipo_mov = 'V';
                $descricao_movimentacao = 'Venda';
                break;
            case 005:
                $tipo_mov = 'T';
                $descricao_movimentacao = 'Tranferência';
                break;
        }

        $data_sistema = date("Y-m-d");

        $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
            WHERE tbl_pessoa_id ='$codigo_local_origem'");
        $num_rows = mysqli_num_rows($rs);
        if ($num_rows!=0) {
            $reg_origem = mysqli_fetch_object($rs);
            $desc_origem = $reg_origem->tbl_pessoa_nome;
        }
        else {
            $desc_origem ='';
        }

        $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
            WHERE tbl_pessoa_id='$codigo_local_destino'");
        $num_rows = mysqli_num_rows($rs);
        if ($num_rows!=0) {
            $reg_destino = mysqli_fetch_object($rs);
            $desc_destino = $reg_destino->tbl_pessoa_nome;
        }
        else {
            $desc_destino ='';
        }

        // Pega estacao atual da fazenda (ultima estacao)
        $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
            WHERE tbl_par_codigo_local='$codigo_local_origem' AND 
                  tbl_par_lixeira=0
            ORDER BY tbl_par_estacao_id DESC LIMIT 1");

        $num_rows_estacao = mysqli_num_rows($tbl_estacao);

        if ($num_rows_estacao!=0) {
            $reg_estacao = mysqli_fetch_object($tbl_estacao);
            $id_estacao_atual = $reg_estacao->tbl_par_estacao_id;
            $desc_estacao_atual = $reg_estacao->tbl_par_estacao_nome;
        }
        else {
            $id_estacao_atual = 0;
            $desc_estacao_atual = '';
        }

        $desc_categoria = [];
        $arrayCategorias = [];
        $descricaoCategorias = [];

        $sql = "SELECT * FROM tabela_categoria_idade 
            WHERE tab_registro_lixeira_categoria_idade='0'"; 
            
        $rs = mysqli_query($conector,$sql); 

        while ($fila = mysqli_fetch_object($rs)){
            $codigo_id = $fila->tab_codigo_categoria_idade;
            $idade_de = $fila->tab_categoria_idade_de;
            $idade_ate = $fila->tab_categoria_idade_ate;

            if ($idade_ate==999999999){
                array_push($desc_categoria, '> 36 m');
                    $descricaoCategorias = [
                        "id" => $codigo_id,
                        "idade_de" => $idade_de,
                        "idade_ate" => $idade_ate
                ];
                array_push($arrayCategorias, $descricaoCategorias);
            }
            else {
                array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
                $descricaoCategorias = [
                    "id" => $codigo_id,
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
                ];
                array_push($arrayCategorias, $descricaoCategorias);
            }
        }

        $codigos_racas = [];
        $codigos_pelagem = [];
        $codigos_maes = [];
        $dados_animais = [];

        $sql= "SELECT * FROM tbl_animais 
            WHERE tbl_animal_lixeira=0 AND 
                  tbl_animal_ativo='S' AND 
                  tbl_animal_codigo_fazenda='$codigo_local_origem'";
        $tbl_animais = mysqli_query($conector,$sql); 

        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            if ($reg_animal->tbl_animal_codigo_raca) {
                $codigos_racas[] = $reg_animal->tbl_animal_codigo_raca;
            }

            if ($reg_animal->tbl_animal_codigo_pelagem) {
                $codigos_pelagem[] = $reg_animal->tbl_animal_codigo_pelagem;
            }

            if ($reg_animal->tbl_animal_codigo_mae) {
                $codigos_maes[] = $reg_animal->tbl_animal_codigo_mae;
            }
        }

        $dados_racas = [];

        if (!empty($codigos_racas)) {
            $sql_racas = "SELECT tab_codigo_raca, tab_descricao_raca FROM tabela_racas WHERE tab_codigo_raca IN (" . implode(',', $codigos_racas) . ")";

            $rs_racas = mysqli_query($conector, $sql_racas);

            while ($reg_racas = mysqli_fetch_object($rs_racas)) {
                $dados_racas[$reg_racas->tab_codigo_raca] = $reg_racas->tab_descricao_raca;
            }
        }

        $dados_pelagem = [];

        if (!empty($codigos_pelagem)) {
            $sql_pelagem = "SELECT tab_codigo_pelagem , tab_descricao_pelagem FROM tabela_pelagens WHERE tab_codigo_pelagem IN (" . implode(',', $codigos_pelagem) . ")";

            $rs_pelagens = mysqli_query($conector, $sql_pelagem);

            while ($reg_pelagens = mysqli_fetch_object($rs_pelagens)) {
                $dados_pelagem[$reg_pelagens->tab_codigo_pelagem] = $reg_pelagens->tab_descricao_pelagem;
            }
        }

        $dados_maes = [];

        if (!empty($codigos_maes)) {
            $sql_maes = "SELECT tbl_animal_codigo_id, tbl_animal_codigo_alfa, tbl_animal_codigo_numerico FROM tbl_animais WHERE tbl_animal_codigo_id IN (" . implode(',', $codigos_maes) . ")";

            $rs_maes = mysqli_query($conector, $sql_maes);

            while ($reg_mae = mysqli_fetch_object($rs_maes)) {
                $dados_maes[$reg_mae->tbl_animal_codigo_id] = $reg_mae->tbl_animal_codigo_alfa . ' ' . intval($reg_mae->tbl_animal_codigo_numerico);
            }
        }
    ?>

    <section id="container" class="">
        <?php
            include "cabecalho.php"; 
            include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_selecao_matrizes.php"; 
            include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Animais <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_movimentacao_animais.php"> Movimentações</a> <i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Editar</span></span>

            <a href="#" style="color: gray; margin-left: 10px;" data-toggle='tooltip' data-placement='right' title="Orientações de uso" onclick="informacoes_uso()"><i class="far fa-question-circle"></i></a>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="icon_box-checked"></i> Movimentações - Editar</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" id="form_gravar" enctype="multipart/form-data" >
                            <div class="panel"> 
                               <div class=panel-body>
                                    <!-- <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">-->

                                <div class="tab-panel" id="itens_digitados">
                                    <div class="tab-pane active table-responsive">
                                        <fieldset class="scheduler-border" id="dados_consulta">
                                            <legend class="scheduler-border fonte-legend lista_animais">Animais Listados</legend>

                                            <div class="row">
                                                <div class="col-md-11"></div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-info pull-right"  onclick="finalizar_sair();">Voltar</button>
                                                </div>
                                            </div>

                                            <div class="row">  
                                                <div class="col-md-5" style="font-size: 14px">
                                                    <label class="label_descriao">Local Origem:&nbsp;</label>
                                                    <span class="descricao_origem_dig"><?php echo $desc_origem;?></span>

                                                    <input type="hidden" name="descricao_origem_dig" id="descricao_origem_dig">
                                                </div>

                                                <div class="col-md-6" style="font-size: 14px">
                                                    <label class="label_descriao">Local Destino:&nbsp;</label>
                                                    <span class="descricao_destino_dig"><?php echo $desc_destino;?></span>

                                                    <input type="hidden" name="descricao_destino_dig" id="descricao_destino_dig">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-5" style="font-size: 14px">
                                                    <label class="label_descriao">Movimentação:&nbsp;</label>
                                                    <span class="descricao_movimentacao"><?php echo $descricao_movimentacao;?></span>

                                                    <input name="idMovimentacaoGravada" type="hidden" id="idMovimentacaoGravada" <?php echo "value='".$numero_mov."'";?>>

                                <input type="hidden" name="editarMovimentacao" id="editarMovimentacao" value="E">

                                <input type="hidden" name="controle_estoque" id="controle_estoque"
                                <?php echo "value='".$controle_estoque."'";?>>

                                <input type="hidden" name="local_origem" id="local_origem" <?php echo "value='".$codigo_local_origem."'";?>>

                                <input type="hidden" name="local_destino" id="local_destino" <?php echo "value='".$codigo_local_destino."'";?>>

                                <input type="hidden" name="data_movimentacao" id="data_movimentacao" <?php echo "value='".$data_movimentacao."'";?>>

                                <input type="hidden" name="descricao_filtro_dig" id="descricao_filtro_dig" <?php echo "value='".$filtros."'";?>>

                                <input type="hidden" name="tipo_movimentacao" id="tipo_movimentacao" <?php echo "value='".$tipo_mov."'";?>>


                                                </div>
                                            </div>

                                            <div class="row">  
                                                <div class="col-md-7" style="font-size: 14px">
                                                    <label class="label_descriao">Filtro:&nbsp;</label>
                                                    <span class="descricao_filtro_dig"><?php echo $filtros;?></span>

                                                    <input type="hidden" name="descricao_filtro_dig" id="descricao_filtro_dig">
                                                </div>

                                                <div class="col-md-3">
                                                    <button type="button" class="form-control btn btn-success" onclick="finalizar_selecao_venda_transferencia();">Finalizar  Movimentação</button>
                                                </div>

                                                <div class="col-md-2">
                                                    <button type="button" class="form-control btn btn-info"  data-toggle='tooltip' data-placement='top' title='Você poderá continuar a digitação e finalizar mais tarde.' onclick="finalizar_sair();">Sair sem Finalizar</button>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="text-primary total_a_digitar">Animais Listados: <?php echo $animais_listados;?></span>

                                                    <input type="hidden" name="total_a_digitar" class="total_a_digitar" <?php echo "value='".$animais_listados."'";?> >
                                                </div>

                                                <div class="col-md-3">
                                                    <span class="text-primary total_digitados">Animais Selecionados: <?php echo $animais_selecionados;?></span>
                                                    
                                                    <input type="hidden" name="total_digitados" class="total_digitados" <?php echo "value='".$animais_selecionados."'";?>>
                                                </div>

                                                <div class="col-md-2">
                                                    <p id="data_digitados" class="text-primary">Data: <?php echo $data_emissao_edi;?></p>
                                                </div>
                                            </div>

                                            <div id="itens_listados">
    <table class="table table-striped table-advance table-hover" id="tabela_itens_digitados" width="100%" style="font-size: 13px;">

    <thead>
        <tr>
            <th colspan="4">
                <button type="button" 
                    id="btnAlternarFiltro" 
                    class="btn" 
                    style="border: none; color: #007bff; background-color: transparent; font-weight: 500;">
                    Ver Apenas Selecionados
                </button>
            </th>

            <th colspan="2"></th>

            <th colspan="8" style="vertical-align: middle; text-align:left; font-size: 10px;">Legenda:&nbsp;&nbsp;<i class="fa fa-square text-primary"></i> &nbsp;Em Estação de Monta <?php echo $desc_estacao_atual;?> &nbsp;&nbsp;<i class="fa fa-square" style="color: #060c54;"></i> &nbsp;Esta na Lista Monta Natural
            </th>
        </tr>

        <tr>
        <th><input type="checkbox" 
           class="seleciona_todos" 
           <?php echo $checkBox; ?> 
           data-toggle="tooltip" 
           data-placement="right" 
           title="Selecionar Todos">
        </th>
        <th> <i class="fa fa-sort-alpha-asc"></i></th>
        <th> Código Numérico</th>
        <th> Categoria</th>
        <th style="text-align: center;"> Sexo</th>
        <th style="text-align: center;"> Nascimento</th>
        <th> Raça</th>
        <th> Pelagem</th>
        <th> Mãe</th>
        <th> Descarte</th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        <th hidden></th>
        </tr>
    </thead>
    <tbody>
        <?php
            /*$tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
                INNER JOIN tbl_animais
                        ON tbl_ite_movimentacao_codigo_id_animal = tbl_animal_codigo_id 
                WHERE tbl_ite_movimentacao_numero_id ='$numero_mov'");

            $numero_itens = mysqli_num_rows($tbl_itens);

            // 1. Prepare os dados fixos FORA do loop
            $data_hoje = new DateTime(); 
            $qtd_categorias = count($arrayCategorias);

            if ($numero_itens) {
                // 2. Inicie um buffer de saída para melhorar a performance de impressão
                ob_start(); 

                while ($reg_itens = mysqli_fetch_object($tbl_itens)) {

                    $desc_raca = isset($dados_racas[$reg_itens->tbl_animal_codigo_raca]) ? $dados_racas[$reg_itens->tbl_animal_codigo_raca] : '';

                    $desc_pelagem = isset($dados_pelagem[$reg_itens->tbl_animal_codigo_pelagem]) ? $dados_pelagem[$reg_itens->tbl_animal_codigo_pelagem] : '';

                    $codigo_mae_alfa_numerico = isset($dados_maes[$reg_itens->tbl_animal_codigo_mae]) ? $dados_maes[$reg_itens->tbl_animal_codigo_mae] : '';

                    $nascimento = new DateTime($reg_itens->tbl_animal_data_nascimento);
                    $nascimento_edi = $nascimento->format('d/m/Y');
                    $intervalo = $data_hoje->diff($nascimento);
                    $idade_meses = ($intervalo->y * 12) + $intervalo->m;

                    // Busca categoria de forma mais direta
                    $desc_categoria = '';
                    $codigo_categoria = '';
                    
                    foreach ($arrayCategorias as $cat) {
                        if ($idade_meses >= $cat['idade_de'] && $idade_meses <= $cat['idade_ate']) {
                            $codigo_categoria = $cat['id'];
                            $desc_categoria = ($cat['idade_ate'] == 999999999) ? ' > 36 meses' : "{$cat['idade_de']} a {$cat['idade_ate']} meses";
                            break; // Sai do loop assim que achar a categoria
                        }
                    }

                    // Lógica de cores (simplificada com ternário)
                    $cor_linha = '';
                    if ($reg_itens->tbl_ite_movimentacao_femea_reproducao == 'S') {
                        $cor_linha = ($reg_itens->tbl_ite_movimentacao_controle_cobertura == 'C') ? 'class="text-primary"' : 'style="color: #060c54;"';
                    }

                    $checked = ($reg_itens->tbl_ite_movimentacao_selecionado == 'S') ? 'checked' : '';

                    // Impressão direta (mais rápido que criar 15 variáveis antes)
                    echo "<tr {$cor_linha}>";

                    echo "<td width='3%'><input type='checkbox' name='id_animal_selecao' class='checkbox1 animalSelecionado' {$checked} value='{$reg_itens->tbl_animal_codigo_id}'></td>";

                    echo "<td align='right' width='4%' class='id_animal_alfa'>{$reg_itens->tbl_animal_codigo_alfa}</td>";
                    echo "<td width='8%' class='id_animal'>{$reg_itens->tbl_animal_codigo_numerico}</td>";
                    echo "<td width='12%' class='desc_categoria'>{$desc_categoria}</td>";
                    echo "<td align='center' width='8%' class='sexo_animal'>{$reg_itens->tbl_animal_sexo}</td>";
                    echo "<td align='center' width='8%' 
                    class='nascimento_animal'>{$nascimento_edi}</td>";
                    echo "<td  width='10%' class='raca_animal'>{$desc_raca}</td>";
                    echo "<td  width='10%' class='pelagem_animal'>{$desc_pelagem}</td>";
                    echo "<td  width='12%' class='mae_animal'>{$codigo_mae_alfa_numerico}</td>";
                    echo "<td align='center' width='3%' style='text-align: center; color: red;'>{$reg_itens->tbl_animal_descarte_reproducao}</td>";
                    echo "<td class='animal_id'>{$reg_itens->tbl_animal_codigo_id}</td>";
                    echo "<td class='codigo_categoria'>{$codigo_categoria}</td>";
                    echo "<td class='femea_selecionada'>{$reg_itens->tbl_ite_movimentacao_femea_reproducao}</td>";
                    echo "<td class='controle'>{$reg_itens->tbl_ite_movimentacao_controle_cobertura}</td>";
                    echo "</tr>";
                }
                // Libera tudo de uma vez para o navegador
                echo ob_get_clean(); 
            }*/
        ?>

    <?php
        $tbl_itens = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
            INNER JOIN tbl_animais
                    ON tbl_ite_movimentacao_codigo_id_animal = tbl_animal_codigo_id 
            WHERE tbl_ite_movimentacao_numero_id ='$numero_mov'");

        $numero_itens = mysqli_num_rows($tbl_itens);

        // 1. Prepare os dados fixos FORA do loop
        $hoje_ano = (int)date("Y");
        $hoje_mes = (int)date("m");
        $qtd_categorias = count($arrayCategorias);

            if ($numero_itens) {
                while ($reg_itens = mysqli_fetch_object($tbl_itens)) {
                    $animal_id = $reg_itens->tbl_animal_codigo_id; 
                    $codigo_alfa = $reg_itens->tbl_animal_codigo_alfa; 
                    $codigo_numerico = intval($reg_itens->tbl_animal_codigo_numerico); 
                    $sexo = $reg_itens->tbl_animal_sexo; 
                    $data_nascimento = $reg_itens->tbl_animal_data_nascimento; 
                    $descarte = $reg_itens->tbl_animal_descarte_reproducao; 
                    $animal_selecionado = $reg_itens->tbl_ite_movimentacao_selecionado;

                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento);
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    for($i = 0; $i < count($arrayCategorias); $i++){
                        $id_categoria = $arrayCategorias[$i]['id'];
                        $idade_de = $arrayCategorias[$i]['idade_de'];
                        $idade_ate = $arrayCategorias[$i]['idade_ate'];

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $id_categoria;

                            if ($idade_ate==999999999) {
                                $desc_categoria=' > 36 meses';
                            }
                            else {
                                $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                            }
                        }
                    }                        

                    $nascimento = new DateTime($data_nascimento);
                    $nascimento_edi = $nascimento->format('d/m/Y');

                    $desc_raca = isset($dados_racas[$reg_itens->tbl_animal_codigo_raca]) ? $dados_racas[$reg_itens->tbl_animal_codigo_raca] : '';

                    $desc_pelagem = isset($dados_pelagem[$reg_itens->tbl_animal_codigo_pelagem]) ? $dados_pelagem[$reg_itens->tbl_animal_codigo_pelagem] : '';

                    $codigo_mae_alfa_numerico = isset($dados_maes[$reg_itens->tbl_animal_codigo_mae]) ? $dados_maes[$reg_itens->tbl_animal_codigo_mae] : '';

                    $femea_selecionada_cobertura = $reg_itens->tbl_ite_movimentacao_femea_reproducao;
                    $controle=$reg_itens->tbl_ite_movimentacao_controle_cobertura;

                    if ($femea_selecionada_cobertura=='S') {
                        if ($controle=='C') {
                            echo '<tr class="text-primary">';
                        }
                        else {
                            echo '<tr style="color: #060c54;">';
                        }
                    }
                    else {
                        echo '<tr>';
                    }

                    if ($animal_selecionado=='S') {
                        echo '<td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1 animalSelecionado" checked 
                            value="'.$animal_id.'"></td>';
                    }
                    else {
                        echo '<td width="3%"><input type="checkbox" name="id_animal_selecao" class="checkbox1 animalSelecionado" value="' .$animal_id.'"></td>';
                    }
                    echo "<td align='right' width='4%' class='id_animal_alfa'>{$codigo_alfa}</td>";
                    echo "<td width='8%' class='id_animal'>{$codigo_numerico}</td>";
                    echo "<td  width='12%' class='desc_categoria'>{$desc_categoria}</td>";
                    echo "<td align='center' width='8%' class='sexo_animal'>{$sexo}</td>";
                    echo "<td align='center' width='8%' class='nascimento_animal'>{$nascimento_edi}</td>";
                    echo "<td  width='10%' class='raca_animal'>{$desc_raca}</td>";
                    echo "<td  width='10%' class='pelagem_animal'>{$desc_pelagem}</td>";
                    echo "<td  width='12%' class='mae_animal'>{$codigo_mae_alfa_numerico}</td>";
                    echo "<td align='center' width='3%' style='text-align: center; color: red;'>{$descarte}</td>";
                    echo "<td hidden class='animal_id'>{$animal_id}</td>";
                    echo "<td hidden class='codigo_categoria'>{$codigo_categoria}</td>";
                    echo "<td hidden class='femea_selecionada'>{$femea_selecionada_cobertura}</td>";
                    echo "<td hidden class='controle'>{$controle}</td>";
                    echo "</tr>";
                }
            }
    ?>
    </tbody>
    </table>


                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <input type="hidden" class="form-control" name="array_itens" id="array_itens">

                                <input type="hidden" id="cat_pasto_m1">
                                <input type="hidden" id="cat_pasto_m2">
                                <input type="hidden" id="cat_pasto_m3">
                                <input type="hidden" id="cat_pasto_m4">
                                <input type="hidden" id="cat_pasto_m5">

                                <input type="hidden" id="cat_pasto_f1">
                                <input type="hidden" id="cat_pasto_f2">
                                <input type="hidden" id="cat_pasto_f3">
                                <input type="hidden" id="cat_pasto_f4">
                                <input type="hidden" id="cat_pasto_f5">

                                           <!-- </div>  fim container -->
                                     <!--   </div>  dados-->
                                   <!-- </div> tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="modal_gravar_venda_transf" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="fechar_modal_pesagem();">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>

                        <div class="modal-body">
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" onclick="gravar_movimentacao_venda_transferencia();">Confirmar</button>

                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_transferencia" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body fundo_red"></div>

                        <pre class="mens" style="margin-left: 5px; margin-right: 5px;">
                        </pre>

                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default fecha_editar_dados" type="button" onclick="finalizar_sair()">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="mensagem_erro_atencao" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Movimentação - Mensagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p><img src='img/exclamacao.png' class="fa fa-exclamation-triangle" width='20' height='23'/>&nbsp; ATENÇÃO!</p>
                                    <p class="desc_modal" style="font-weight: bold;">
                                    </p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <p class="aguardar">Aguarde <i class='fa fa-spinner fa-spin fa-2x' ></i></p>
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

        </section> <!-- wrapper -->
    </section><!--main-content -->

     <div class="text-center">
         <div class="credits">
             <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2026</p></font>
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

    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js?<?php echo Versao; ?>"></script> 

    <script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

    <script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>
    <script src="js/typeahead.js"></script>

    <script src="js/movimentacao.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  -->

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>

    <script>
        $(document).ready(function(){
            var table = $('#tabela_itens_digitados').DataTable({
                'responsive': true,
                'paging':   false,
                'ordering': true,
                'info':     true,
                'language': {
                    'sSearch': 'Busca:',
                    'zeroRecords': 'Nada encontrado',
                    'info': '',
                    'infoEmpty': 'Nenhum registro disponível',
                    'infoFiltered': '(filtrado de _MAX_ registros no total)',
                },
                initComplete: function() {
                    $('table.dataTable').css('width', '100%');
                }
            });

            // 1. Guardamos a instância da tabela numa variável
            var filtrandoSelecionados = false;

            $('#btnAlternarFiltro').on('click', function() {
                if (!filtrandoSelecionados) {
                    // Ativa o filtro customizado
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            // Seleciona a linha atual e verifica se o checkbox está marcado
                            var row = table.row(dataIndex).node();
                            return $(row).find('.animalSelecionado').is(':checked');
                        }
                    );
                    $(this).text('Ver Todos os Animais');
                    //$(this).removeClass('btn-outline-primary').addClass('btn-warning');
                } else {
                    // Remove o último filtro adicionado
                    $.fn.dataTable.ext.search.pop();
                    $(this).text('Ver Apenas Selecionados');
                    //$(this).removeClass('btn-warning').addClass('btn-outline-primary');
                }
                
                // Redesenha a tabela com o novo filtro
                table.draw();
                filtrandoSelecionados = !filtrandoSelecionados;
            });

            $('.seleciona_todos').click(function(event) {
                var total_selecionados = 0;

                const isMasterCheckboxChecked = this.checked; // Verifica se o checkbox 'Marcar Todos' foi marcado
                let femeaComSEncontrada = false; // Flag para verificar se encontrou 'S'

                // Itera sobre cada checkbox individual com a classe 'checkbox1'
                $('.checkbox1').each(function() {
                    // Marca ou desmarca o checkbox individual
                    this.checked = isMasterCheckboxChecked;

                    // Se o checkbox mestre está marcando todos (this.checked === true)
                    // e ainda não encontramos uma fêmea com 'S', faz a verificação.
                    if (isMasterCheckboxChecked && !femeaComSEncontrada) {
                        // Pega a linha (tr) pai do checkbox individual
                        const row = $(this).closest('tr');

                        if (row.length) { // Garante que a linha foi encontrada
                            const femeaSelecionadaElement = row.find('.femea_selecionada');
                            const femeaSelecionadaValue = femeaSelecionadaElement.text().trim();

                            if (femeaSelecionadaValue === 'S') {
                                femeaComSEncontrada = true; // Define a flag como true se encontrar 'S'
                            }
                        }
                    }
                });

                // Após marcar/desmarcar todos os checkboxes individuais:
                // Se o checkbox mestre foi marcado E uma fêmea com 'S' foi encontrada, emite o alert.
                if (isMasterCheckboxChecked && femeaComSEncontrada) {
                    $('#mensagem_erro_atencao').modal();
                    $('#mensagem_erro_atencao .modal-body .desc_modal').html('Exitem Fêmeas em Estação de Monta ou na Lista de Monta Natural!');
                }

                // Atualiza a contagem de selecionados
                // Se o master foi marcado, total_selecionados é o número total de checkboxes 'checkbox1'.
                // Se o master foi desmarcado, total_selecionados é 0.
                total_selecionados = isMasterCheckboxChecked ? $('.checkbox1').length : 0;

                // Atualiza os elementos que exibem o total
                $('.total_digitados').text('Animais Selecionados: ' + total_selecionados);
                $('.total_digitados').val(total_selecionados);

                selecionarMovimentacaoToda();
            });
        });

    </script>
</body>
</html>