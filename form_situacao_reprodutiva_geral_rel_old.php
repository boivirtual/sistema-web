<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";



    $data_sistema = date("Y-m-d");
    $data_hoje = date("Y-m-d");

    @ session_start(); 
    $_SESSION['opcao_situacao_reprodutica_rel']='G';

    $local_filtro = $_REQUEST["local"];
    $origem_filtro = $_REQUEST["origem"];
    $raca_filtro = $_REQUEST["raca"];
    $categoria_filtro = $_REQUEST["categoria"];
    $pai_filtro = $_REQUEST["pai"];
    $mae_filtro = $_REQUEST["mae"];

    if (isset($_REQUEST['estacao'])) {
        $estacao_filtro = $_REQUEST['estacao'];

        $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
            WHERE tbl_par_estacao_nome='$estacao_filtro'
            ORDER BY tbl_par_estacao_id ASC");  

        $num_rows = mysqli_num_rows($sql);
        $array_estacao = array();

        if ($num_rows!=0) {
            while ($reg_estacao = mysqli_fetch_object($sql)){
                $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
                $array_estacao[] = $codigo_estacao;
            }

            $array_estacao = implode(',', $array_estacao);
        }
    }

    $westacao = "";
    if (!empty ($array_estacao)) {
    
        $array_estacao = explode(',', $array_estacao);

        $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
        $westacao.= implode(',', $array_estacao);
        $westacao.= ")";
    }


    $local= array();
    $matriz_itens = explode(",", $local_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal = '';

    if ($local_filtro!='') {
        $wlocal = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal.= $local;
        $wlocal.= ")";
    }

    $origem= array();
    $matriz_itens = explode(",", $origem_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $origem[$i]=$matriz_itens[$i];
    }

    $origem = implode(',', $origem);
    $origem = substr($origem,0, -1);

    $worigem = '';

    if ($origem_filtro!='') {
        $worigem = " AND tbl_animal_codigo_origem IN(";
        $worigem.= $origem;
        $worigem.= ")";
    }

    $raca= array();
    $matriz_itens = explode(",", $raca_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $raca[$i]=$matriz_itens[$i];
    }

    $raca = implode(',', $raca);
    $raca = substr($raca,0, -1);

    $wraca = '';

    if ($raca_filtro!='') {
        $wraca = " AND tbl_animal_codigo_raca IN(";
        $wraca.= $raca;
        $wraca.= ")";
    }

    $categoria= array();
    $matriz_itens = explode(",", $categoria_filtro);
    $quantidade_categoria = count($matriz_itens);

    for($i=0; $i < $quantidade_categoria; $i++) {
        $categoria[$i]=$matriz_itens[$i];
    }

    $categoria = implode(',', $categoria);
    $categoria = substr($categoria,0, -1);
    $quantidade_categoria--;

    $wcategoria = '';

    if ($categoria_filtro!='') {
        $wcategoria = explode(",", $categoria);
    }

    $pai= array();
    $matriz_itens = explode(",", $pai_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $pai[$i]=$matriz_itens[$i];
    }

    $pai = implode(',', $pai);
    $pai = substr($pai,0, -1);

    $wpai = '';

    if ($pai_filtro!='') {
        $wpai = " AND tbl_animal_codigo_pai IN(";
        $wpai.= $pai;
        $wpai.= ")";
    }

    $mae= array();
    $matriz_itens = explode(",", $mae_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $mae[$i]=$matriz_itens[$i];
    }

    $mae = implode(',', $mae);
    $mae = substr($mae,0, -1);

    $wmae = '';

    if ($mae_filtro!='') {
        $wmae = " AND tbl_animal_codigo_mae IN(";
        $wmae.= $mae;
        $wmae.= ")";
    }

    $peso_nasc_inicial = $_REQUEST["peso_nasc_inicial"];
    $peso_nasc_final = $_REQUEST["peso_nasc_final"];

    $peso_desmama_inicial = $_REQUEST["peso_desmama_inicial"];
    $peso_desmama_final = $_REQUEST["peso_desmama_final"];

    $peso_ult_inicial = $_REQUEST["peso_ult_inicial"];
    $peso_ult_final = $_REQUEST["peso_ult_final"];

    if ($peso_nasc_inicial=='' && $peso_nasc_final==''){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial=='' && $peso_desmama_final==''){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial=='' && $peso_ult_final==''){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
    $data_nasc_final = $_REQUEST["data_nasc_final"];

    if ($data_nasc_inicial=='' && $data_nasc_final==''){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $wativo = $_REQUEST['ativo'];
    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $filtro_solteiras = $_REQUEST["solteiras"];
    $descarte = $_REQUEST["descarte"];
    $filtro_paridas = $_REQUEST["paridas"];
    $data_paridas_ate = $_REQUEST["data_paridas"];
    $filtro_parto = $_REQUEST["parto"];
    $num_parto_de = $_REQUEST['num_parto_de'];
    $num_parto_ate = $_REQUEST['num_parto_ate'];
    $filtro_aborto = $_REQUEST["aborto"];
    $num_aborto_de = $_REQUEST['num_aborto_de'];
    $num_aborto_ate = $_REQUEST['num_aborto_ate'];
    $previsao_parto_de = $_REQUEST['previsao_parto_de'];
    $previsao_parto_ate = $_REQUEST['previsao_parto_ate'];
    $filtro_positivo = $_REQUEST['positivo'];
    $filtro_negativo = $_REQUEST['negativo'];
    

    if ($_REQUEST["paridas"]=='S') {
        $filtro_paridas = 'S';
    }
    else {
        $filtro_paridas = 'N';
    }

    if ($_REQUEST["solteiras"]=='S') {
        $filtro_solteiras = 'S';
    }
    else {
        $filtro_solteiras = 'N';
    }

    if ($_REQUEST["paridas"]=='' && $_REQUEST["solteiras"]=='') {
        $filtro_paridas = '';
        $filtro_solteiras = '';
    }

    if ($data_paridas_ate=='') {
        $data_paridas_ate='9999-99-99';
        $data_paridas_de='0000-00-00';
    }
    else {
        $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
        $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
    }

    if ($previsao_parto_de=='') {
        $previsao_parto_de = '0000-00-00';
        $previsao_parto_ate = '9999-99-99';
    }

    $vaca_solteira = '';
    $vaca_parida = '';
    $vaca_descarte = '';
    $tem_parto = '';
    $tem_aborto = '';
    $tem_previsao_parto = '';
    $ultimo_parto = '0000-00-00';
    $data_previsao_parto = '0000-00-00';
    $tem_positivo = '';
    $tem_negativo = '';
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
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/style.css?<?php echo Versao;?>" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />

    <script src="https://kit.fontawesome.com/30604bf5d3.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.css"/>

    <link rel="stylesheet" href="css/select-1.13.14.css"> 
    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <!--<style type="text/css">
        .card {
          position: relative;
          margin: 0 0 0 0;
          background-color: #fff;
          transition: box-shadow .25s;
          border-radius: 2px;
          box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            color: #939ba2;
            opacity: 0.8;
            font-size: 1.625rem;
            font-weight: 500;
            padding-left: 10px;
            padding-top: 10px;
        }
    </style>-->

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php";
            include "opcoes_menu.php"; 
            include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; 
            include "limpar_secao_movimentacao.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Reprodutivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Situação Reprodutiva</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="far fa-file-alt"></i> Situação Reprodutiva</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                                <!--<div class=panel-body>-->
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="" style="padding-right: 15px; padding-left: 15px;">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="codigo_local"
                                                    <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="codigo_origem"
                                                    <?php echo "value='".$origem_filtro."'";?>>

                                                <input type="hidden" id="codigo_raca"
                                                    <?php echo "value='".$raca_filtro."'";?>>

                                                <input type="hidden" id="codigo_categoria"
                                                    <?php echo "value='".$categoria_filtro."'";?>>

                                                <input type="hidden" id="codigo_pai"
                                                    <?php echo "value='".$pai_filtro."'";?>>

                                                <input type="hidden" id="codigo_mae"
                                                    <?php echo "value='".$mae_filtro."'";?>>

                                                <input type="hidden" id="peso_nasc_inicial"
                                                    <?php echo "value='".$peso_nasc_inicial."'";?>>

                                                <input type="hidden" id="peso_nasc_final"
                                                    <?php echo "value='".$peso_nasc_final."'";?>>

                                                <input type="hidden" id="peso_desmama_inicial"
                                                    <?php echo "value='".$peso_desmama_inicial."'";?>>

                                                <input type="hidden" id="peso_desmama_final"
                                                    <?php echo "value='".$peso_desmama_final."'";?>>

                                                <input type="hidden" id="peso_ult_inicial"
                                                    <?php echo "value='".$peso_ult_inicial."'";?>>

                                                <input type="hidden" id="peso_ult_final"
                                                    <?php echo "value='".$peso_ult_final."'";?>>

                                                <input type="hidden" id="data_nasc_inicial"
                                                    <?php echo "value='".$data_nasc_inicial."'";?>>

                                                <input type="hidden" id="data_nasc_final"
                                                    <?php echo "value='".$data_nasc_final."'";?>>

                                                <input type="hidden" id="ativo"
                                                    <?php echo "value='".$wativo."'";?>>

                                                <input type="hidden" id="tipo_rel"
                                                    <?php echo "value='".$tipo_rel."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                                <input type="hidden" id="solteiras"
                                                    <?php echo "value='".$filtro_solteiras."'";?>>

                                                <input type="hidden" id="descarte"
                                                    <?php echo "value='".$descarte."'";?>>

                                                <input type="hidden" id="paridas"
                                                    <?php echo "value='".$filtro_paridas."'";?>>

                                                <input type="hidden" id="data_paridas_ate"
                                                    <?php echo "value='".$_REQUEST["data_paridas"]."'";?>>

                                                <input type="hidden" id="parto"
                                                    <?php echo "value='".$filtro_parto."'";?>>

                                                <input type="hidden" id="num_parto_de"
                                                    <?php echo "value='".$num_parto_de."'";?>>

                                                <input type="hidden" id="num_parto_ate"
                                                    <?php echo "value='".$num_parto_ate."'";?>>

                                                <input type="hidden" id="aborto"
                                                    <?php echo "value='".$filtro_aborto."'";?>>

                                                <input type="hidden" id="num_aborto_de"
                                                    <?php echo "value='".$num_aborto_de."'";?>>

                                                <input type="hidden" id="num_aborto_ate"
                                                    <?php echo "value='".$num_aborto_ate."'";?>>

                                                <input type="hidden" id="previsao_parto_de"
                                                    <?php echo "value='".$_REQUEST['previsao_parto_de']."'";?>>

                                                <input type="hidden" id="previsao_parto_ate"
                                                    <?php echo "value='".$_REQUEST['previsao_parto_ate']."'";?>>

                                                <input type="hidden" id="positivo"
                                                    <?php echo "value='".$filtro_positivo."'";?>>

                                                <input type="hidden" id="negativo"
                                                    <?php echo "value='".$filtro_negativo."'";?>>

                                                <input type="hidden" id="codigo_estacao_filtro"
                                                    <?php echo "value='".$estacao_filtro."'";?>>


<div class="row">
    <div class="col-md-9" style="margin-bottom: 10px; margin-top: 10px;">
        <label class="label_consulta_rel_rel">Filtros:</label>
        <span><?php echo $descricao_filtro;?></span>
    </div>

    <div class="col-md-3" style="padding-top: 10px;">  
        <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
        </button>

        <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="situacao_reprodutiva_geral_excel()">Excel
        </button>
    </div>
</div>

<table class="table table-bordered table-striped table-advance table-hover" id="tabela_situacao_reprodutiva" width="100%" style="font-size: 8px;">

<tbody>

<?php
    /*$sql = "SELECT * from tbl_animais 
        WHERE tbl_animal_codigo_numerico=1259 or tbl_animal_codigo_numerico=1874
         ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC";*/ 

    $sql = "SELECT * from tbl_animais 
        WHERE tbl_animal_lixeira=0 AND 
              tbl_animal_ativo='$wativo' AND 
              tbl_animal_sexo='F'" . $wlocal . $worigem . $wraca . $wpai . 
              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
        " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC";

    $rs = mysqli_query($conector, $sql); 
    $num_rows_animais = mysqli_num_rows($rs);

    $animais_listados = 0;
    $ultimo_parto = '0000-00-00';
    $data_previsao_servico = '0000-00-00';
    $data_previsao_parto = '0000-00-00';

    if ($num_rows_animais!=0){
        while ($reg_animal = mysqli_fetch_object($rs)){
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
            $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
            $mae = $reg_animal->tbl_animal_codigo_mae; 
            $pai = $reg_animal->tbl_animal_codigo_pai; 
            $ativo = $reg_animal->tbl_animal_ativo; 
            $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

            if ($animal_descarte=='S') {
                $animal_descarte = 'Sim';
            }
            else {
                $animal_descarte = '';   
            }

            $tem_negativo = '';
            $tem_positivo = '';
            $vaca_descarte = '';
            $nascido = '';
            $data_aborto_natimorto = '0000-00-00';

            if ($descarte=='S') {
                if ($animal_descarte=='Sim') {
                    $vaca_descarte = 'S';
                }
            }

            // verifica a cobertura do animal

            $sql = "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR  
                       tbl_cobertura_controle = 'M')
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"; 

            $tbl_item_cobertura = mysqli_query($conector, $sql);

            $num_rows = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                $controle = $reg_cobertura->tbl_cobertura_controle;
                $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                
                if ($controle == 'C') {
                    $tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
                        WHERE tbl_par_estacao_id ='$estacao_animal'");

                    $num_rows_estacao = mysqli_num_rows($tbl_estacao);

                    if ($num_rows_estacao!=0) {
                        $reg_estacao = mysqli_fetch_object($tbl_estacao);
                        $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                    }
                    else {
                        $estacao_animal = 0;
                        $desc_estacao_monta = 'Desconhecida';                    
                    }
                }
                else {
                    $estacao_animal = 0;
                    $desc_estacao_monta = 'Monta';                    
                }
            }
            else {
                $estacao_animal = 0;
                $desc_estacao_monta = '';
            }

            // verifica numero de coberturas na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'"); 

            $num_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_coberturas==0) {
                $num_coberturas = '';
            } 

            $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
            $data_nasc_edi = $data_nasc->format('d/m/Y');
            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso; 
            $peso_nasc_edi = number_format($peso_nasc,2,',','.');
            $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
            $peso_desmama_edi = number_format($peso_desmama,2,',','.');
            $peso_ultimo = $reg_animal->tbl_animal_ultimo_peso; 
            $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
            $data_ultimo = new DateTime($reg_animal->tbl_animal_data_ultimo);
            $data_ultimo_edi = $data_ultimo->format('d/m/Y');
            $observacao = ltrim($reg_animal->tbl_animal_observacao); 
            $observacao = rtrim($observacao); 

            if ($codigo_alfa=='') {
                $codigo_edi = intval($codigo_numerico);
            }
            else {
                $codigo_edi = $codigo_alfa.'-'.intval($codigo_numerico);
            }

            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
            $num_rows = mysqli_num_rows($tab_fazenda);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_fazenda);
                $desc_local = $reg->tbl_pessoa_nome;
            }
            else {
                $desc_local = '';
            }

            $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
            $num_rows = mysqli_num_rows($tab_mae);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_mae);
                if ($reg->tbl_animal_codigo_alfa==''){
                    $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                }
                else {
                    $descricao_mae = $reg->tbl_animal_codigo_alfa.'-'. intval($reg->tbl_animal_codigo_numerico);
                }
            }
            else {
                $descricao_mae = '';
            }

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows = mysqli_num_rows($tab_pai);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pai);
                $descricao_pai = $reg->tbl_semem_nome;
                $pai = $reg->tbl_semem_codigo_id;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows = mysqli_num_rows($tab_pai);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    if ($reg->tbl_animal_codigo_alfa==''){
                        $descricao_pai = $reg->tbl_animal_codigo_numerico;
                    }
                    else {
                        $descricao_pai = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                    }
                }
                else {
                    $descricao_pai = '';
                }
            }

            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
            $num_rows = mysqli_num_rows($tab_raca);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_raca);
                $descricao_raca = $reg->tab_descricao_raca;
            }
            else {
                $descricao_raca = '';
            }

            $tab_pelagem = mysqli_query($conector, "select * from tabela_pelagens where tab_codigo_pelagem ='$codigo_pelagem'");
            $num_rows = mysqli_num_rows($tab_pelagem);

            if ($num_rows!=0){
                $reg = mysqli_fetch_object($tab_pelagem);
                $descricao_pelagem = $reg->tab_descricao_pelagem;
            }
            else {
                $descricao_pelagem = '';
            }

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $data_baixa = $reg_animal->tbl_animal_baixado_em;

            if ($data_baixa!='') {
                $data_acompanhamento_calculo = date($data_baixa);
            }
            else {
                $data_acompanhamento_calculo = date("Y-m-d");
            }

            //$data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); 
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade_animal >= $idade_de && 
                        $idade_animal <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }
            }                   

            // verifica vacas solteiras
            if ($filtro_solteiras=='S' || $filtro_paridas=='S') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $bezzero_ativo = $reg_filhos->tbl_animal_ativo;
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $data_ref = date("Y-m-d");
                    $diferenca = strtotime($data_ref) - strtotime($ultimo_parto);
                    $dias_nascimento_bezerro = floor($diferenca / (60 * 60 * 24));

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai_ult_filho'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                    $descricao_pai_ult_filho = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_pai_ult_filho = '';
                        }
                    }

                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($ultimo_parto); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_ano = $idade_acompanhamento->format('%Y');
                    $idade_mes = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade < 8) {
                        if ($bezzero_ativo=='S') {
                            $vaca_parida = 'S';
                            $vaca_solteira = '';
                        }
                        else {
                            if ($dias_nascimento_bezerro<=35) {
                                $vaca_parida = 'S';
                                $vaca_solteira = '';
                            }
                            else {
                                $vaca_parida = '';
                                $vaca_solteira = 'S';
                            }
                        }
                    }
                    else {
                        $vaca_solteira = 'S';
                        $vaca_parida = '';
                    }
                }
                else {
                    $ultimo_parto = '0000-00-00';
                    $codigo_edi_filho = '';
                    $vaca_solteira = 'S';
                    $vaca_parida = '';
                    $descricao_pai_ult_filho = '';
                    $bezzero_ativo='';
                    $dias_nascimento_bezerro='';
                }

                if ($ultimo_parto=='0000-00-00') {
                    $ultimo_parto_edi = '';
                }
                else {
                    $data = new DateTime($ultimo_parto);
                    $ultimo_parto_edi = $data->format('d/m/Y');
                }

                // VERIFICA SE A VACA ESTA PRENHE

                $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                    INNER JOIN tbl_item_cobertura 
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal='$codigo' AND  
                          tbl_ite_cobertura_resultado_diagnostico='P' AND  
                          (tbl_ite_cobertura_nascido='' OR 
                           tbl_ite_cobertura_nascido IS NULL)");

                $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

                if ($num_rows_prenhe!=0) {
                    //print_r('Vaca prenhe: ' . $codigo_edi . '</br>');
                    $vaca_solteira = '';
                }
            }
            else {
                $ultimo_parto_edi = '';
                $codigo_edi_filho = '';
                $descricao_pai_ult_filho = '';
                $bezzero_ativo='';
                $dias_nascimento_bezerro='';

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $bezzero_ativo = $reg_filhos->tbl_animal_ativo;
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $data_ref = date("Y-m-d");
                    $diferenca = strtotime($data_ref) - strtotime($ultimo_parto);
                    $dias_nascimento_bezerro = floor($diferenca / (60 * 60 * 24));

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai_ult_filho'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_pai_ult_filho = $reg->tbl_animal_codigo_numerico;
                            }
                        else {
                            $descricao_pai_ult_filho = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_pai_ult_filho = '';
                        }
                    }
                } 
                else {
                    $ultimo_parto = '0000-00-00';
                }
                           
                if ($ultimo_parto=='0000-00-00') {
                    $ultimo_parto_edi = '';
                }
                else {
                    $data = new DateTime($ultimo_parto);
                    $ultimo_parto_edi = $data->format('d/m/Y');
                }
            }

            // verifica partos
            if ($num_parto_de!='' && $num_parto_ate!='') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                // verifica parto natimorto

                $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo' and 
                              tbl_mov_estoque_codigo_id_animal=999999999 and 
                              tbl_mov_estoque_entrada_saida='E' and 
                              tbl_mov_estoque_tipo_movimentacao='N'");
                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                $num_partos = $num_partos + $num_natimorto;

                if ($num_partos>=$num_parto_de && 
                    $num_partos<=$num_parto_ate && $idade_animal>=8) {
                    $tem_parto = "S";
                }
                else {
                    $tem_parto = "";
                }
            }
            else {
                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'");

                $num_partos = mysqli_num_rows($tbl_filhos);

                // verifica parto natimorto

                $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                    where tbl_mov_estoque_codigo_mae='$codigo' and 
                          tbl_mov_estoque_codigo_id_animal=999999999 and 
                          tbl_mov_estoque_entrada_saida='E' and 
                          tbl_mov_estoque_tipo_movimentacao='N'");
                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                $num_partos = $num_partos + $num_natimorto;
            }

            // verifica se tem abortos ou natimortos
            if ($num_aborto_de!='' && $num_aborto_ate!='') {
                $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                    WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                          tbl_mov_estoque_codigo_id_animal=999999999 AND
                          (tbl_mov_estoque_entrada_saida='A' OR 
                           tbl_mov_estoque_entrada_saida='S') AND 
                          (tbl_mov_estoque_tipo_movimentacao='M' OR
                           tbl_mov_estoque_tipo_movimentacao='A' OR
                           tbl_mov_estoque_tipo_movimentacao='B')");

                $num_natimorto = mysqli_num_rows($tbl_natimorto);

                if ($num_natimorto>=$num_aborto_de && 
                    $num_natimorto<=$num_aborto_ate) {
                    $tem_aborto = "S";
                }
                else {
                    $tem_aborto = "";
                }
            } 

            // agora verifica o numero de natimortos
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M'
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto==0) {
                $num_natimorto = '';
                $data_natimorto='0000-00-00';
            }
            else {
                $reg_natimorto = mysqli_fetch_object($tbl_natimorto);
                $data_natimorto=$reg_natimorto->tbl_mov_estoque_nascimento;
            }

            // agora verifica o numero de abortos
            $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='A' AND 
                      (tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B')
                ORDER BY tbl_mov_estoque_nascimento DESC");

            $num_aborto = mysqli_num_rows($tbl_aborto);

            if ($num_aborto==0) {
                $num_aborto = '';
                $data_aborto='0000-00-00';
            }
            else {
                $reg_aborto = mysqli_fetch_object($tbl_aborto);
                $data_aborto=$reg_aborto->tbl_mov_estoque_nascimento;
            }

            //print_r('Aborto ' . $data_aborto . '</br>');

            // Verifica qual data será considerada para calcular a aptidao

            if ($data_natimorto=='0000-00-00' && $data_aborto=='0000-00-00') {
                $data_aborto_natimorto='0000-00-00';
            }
            else if ($data_natimorto>$data_aborto){
                $data_aborto_natimorto=$data_natimorto;
            }
            else if ($data_aborto>$data_natimorto) {
                $data_aborto_natimorto=$data_aborto;
            }


            // Se tem natimorto e a data é maior o ultimo parto considera como ultimo parto
            //if ($data_natimorto>$ultimo_parto) {
            if ($data_aborto_natimorto>$ultimo_parto) {
                $data = new DateTime($data_aborto_natimorto);
                $ultimo_parto_edi = $data->format('d/m/Y');
                $natimorto = 'S';
            }
            else {
                $natimorto = 'N';
            }

            // verifica previsao de parto
            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                      (tbl_cobertura_controle = 'C' OR
                      tbl_cobertura_controle = 'M') AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' 
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_coberturas!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                $cobertura_id = $reg_cobertura->tbl_cobertura_id;
                $controle = $reg_cobertura->tbl_cobertura_controle;
                $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

                if ($controle=='C') {
                    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                            WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                    $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                    $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf
                        WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                              tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                        ORDER BY tbl_ite_protocoloiatf_id ASC");

                    $dias_previsao_parto = 282;

                    while($reg_itens_iatf = mysqli_fetch_object($sql)){
                        $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                        $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                    }
                }
                else {
                    $data_previsao_parto = $reg_cobertura->tbl_ite_cobertura_previsao_parto;
                }
            }
            else {
                $data_previsao_parto = '';
            }
        
            if ($data_previsao_parto=='' || $data_previsao_parto=='0000-00-00') {
                $data_previsao_parto = '0000-00-00';
                $previsao_parto_edi = '';
            }
            else {
                $data = new DateTime($data_previsao_parto);
                $previsao_parto_edi = $data->format('d/m/Y');
            }

            // calcula data da aptidão

            $data_aptidao_edi = '';
            
            if ($ultimo_parto!='0000-00-00') {
                $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
                //print_r('Aptidao pelo ultimo parto ' . $data_aptidao_edi . '</br>');
            }

            if ($data_aborto_natimorto!='0000-00-00' && $data_aborto_natimorto>$ultimo_parto) {
                $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
                //print_r('Aptidao pelo ultimo aborto/natimorto ' . $data_aptidao_edi . '</br>');
            }

            // Verifica diagnostico
            if ($filtro_positivo=='S' || $filtro_negativo=='S'){

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                         (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                    ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$semen'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_semen = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_semen = '';
                        }
                    }

                    if ($diagnostico=='P'){
                        $tem_positivo = 'S';
                        $tem_negativo = '';
                    } 
                    else if ($diagnostico=='N') {
                        $tem_negativo = 'S';
                        $tem_positivo = '';
                    }
                    else {
                        $tem_negativo = '';
                        $tem_positivo = '';
                    }
                }
                else {
                    $tem_negativo = '';
                    $tem_positivo = '';
                }
            }
            else {
                $tem_negativo = '';
                $tem_positivo = '';
                $diagnostico = '';
                $descricao_semen = '';

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          (tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M')
                    ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_nome;
                    }
                    else {
                        $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$semen'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            if ($reg->tbl_animal_codigo_alfa==''){
                                $descricao_semen = $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_semen = $reg->tbl_animal_codigo_alfa.'-'. $reg->tbl_animal_codigo_numerico;
                            }
                        }
                        else {
                            $descricao_semen = '';
                        }
                    }
                }
            }

            // verifica natimortos, nascidos ou abortos na estacao

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND 
                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND
                      ((tbl_cobertura_controle = 'C' AND tbl_cobertura_codigo_estacao_monta ='$estacao_animal') OR (tbl_cobertura_controle = 'M')) AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P'
                ORDER BY tbl_cobertura_incluido_em DESC limit 1"); 

            $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_item!=0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
            }
            else {
                $nascido_aborto = '';
            }

            if ($filtro_positivo=='S' AND 
                $nascido_aborto!='') {
                $tem_positivo='';
            }

            if ($data_previsao_parto!='0000-00-00' AND 
                $nascido_aborto!='') {
                $data_previsao_parto='0000-00-00';
            }

            if ($num_partos==0 && $num_aborto=='' && $num_natimorto=='' && $num_coberturas=='') {
                $vaca_solteira = '';
            }

            if ($wcategoria=="" && 
                $descarte==$vaca_descarte && 
                $data_previsao_parto>=$previsao_parto_de &&  
                $data_previsao_parto<=$previsao_parto_ate && 

                (($filtro_solteiras==$vaca_solteira && 
                 ($previsao_parto_edi=='' || 
                 ($nascido=='N' || $nascido=='A' || 
                 $nascido=='M' || $nascido=='O'))) ||

                 ($filtro_paridas==$vaca_parida && 
                 $ultimo_parto>=$data_paridas_de && 
                 $ultimo_parto<=$data_paridas_ate)) &&

                $filtro_parto==$tem_parto &&
                $filtro_aborto==$tem_aborto && 
                $filtro_positivo==$tem_positivo &&
                $filtro_negativo==$tem_negativo 
                ) {
                    if ($diagnostico=='N') {
                        $previsao_parto_edi='';
                        $desc_diagnostico = 'Negativo';
                    }
                    else if ($diagnostico=='P') {
                        switch ($nascido) {
                            case 'N':
                                $desc_diagnostico = 'Nascido';
                                break;
                            case 'A':
                                $desc_diagnostico = 'Aborto';
                                break;
                            case 'M':
                                $desc_diagnostico = 'Natimorto';
                                break;
                            case 'S':
                                $desc_diagnostico = 'Outro';
                                break;
                            default:
                                $desc_diagnostico = 'Positivo';
                                break;       
                        }
                    }
                    else {
                        $desc_diagnostico = '';
                    }

                    echo '<tr>';
                    echo '<td width="7%">'.$codigo_edi.'</td>';
                    echo '<td width="7%">'.$descricao_raca.'</td>';    
                    echo '<td width="7%">'.$descricao_pelagem.'</td>';   
                    echo '<td width="5%">'.$data_nasc_edi.'</td>';
                    echo '<td width="7%">'.$descricao_pai.'</td>';
                    echo '<td width="7%">'.$descricao_mae.'</td>';    
                    echo '<td width="3%" style="text-align: center;">'.$num_partos.'</td>';    
                    echo '<td width="3%" style="text-align: center;">'.$num_aborto.'</td>';    
                    echo '<td width="3%" style="text-align: center;">'.$num_natimorto.'</td>';    

                    if ($natimorto=='S') {
                        echo '<td width="6%">'.$ultimo_parto_edi.'&nbsp;&nbsp;<i class="icon_info_alt" data-toggle="tooltip" data-placement="right" title="Considerado aqui a data do Natimorto/Aborto." style="color: red;"></i></td>';
                    }  
                    else if ($bezzero_ativo=='N' && 
                            ($dias_nascimento_bezerro>0 && 
                             $dias_nascimento_bezerro<=35)) {
                        echo '<td width="6%">'.$ultimo_parto_edi.'&nbsp;&nbsp;<i class="icon_info_alt" data-toggle="tooltip" data-placement="right" title="Bezerro Morreu a 35 dias ou menos" style="color: red;"></i></td>';
                    }
                    else {
                        echo '<td width="6%">'.$ultimo_parto_edi.'</td>';  
                    }

                    echo '<td width="6%">'.$codigo_edi_filho.'</td>';    
                    echo '<td width="6%">'.$descricao_pai_ult_filho.'</td>';
                    echo '<td width="6%">'.$desc_estacao_monta.'</td>';    
                    echo '<td width="3%" style="text-align: center;">'.$num_coberturas.'</td>';    
                    echo '<td width="3%"  style="text-align: center;">'.$desc_diagnostico.'</td>';    
                    echo '<td width="8%">'.$descricao_semen.'</td>';    

                    if ($nascido=='N' || $nascido=='A' || 
                        $nascido=='M' || $nascido=='O') {
                        echo '<td width="5%" style="color: #DCDCDC;">'.$previsao_parto_edi.'</td>';    
                    } 
                    else {
                        $data_aptidao_edi = '';
                        echo '<td width="5%">'.$previsao_parto_edi.'</td>';
                    }
                        
                    echo '<td width="5%">'.$data_aptidao_edi.'</td>';    
                    echo '<td width="3%" style="color: red; text-align: center;">'.$animal_descarte.'</td>';    
                    echo '</tr>';
                    $animais_listados++;
            }
            else {
                for ($k=0; $k < $quantidade_categoria; $k++) { 
                    $value = $wcategoria[$k];
                    if ($value==$codigo_categoria &&
                        $descarte==$vaca_descarte && 
                        $data_previsao_parto>=$previsao_parto_de && 
                        $data_previsao_parto<=$previsao_parto_ate && 

                        (($filtro_solteiras==$vaca_solteira && ($previsao_parto_edi=='' || ($nascido=='N' || $nascido=='A' || 
                        $nascido=='M' || $nascido=='O'))) ||
                        ($filtro_paridas==$vaca_parida && 
                        $ultimo_parto>=$data_paridas_de && 
                        $ultimo_parto<=$data_paridas_ate)) &&

                        $filtro_parto==$tem_parto &&
                        $filtro_aborto==$tem_aborto && 
                        $filtro_positivo==$tem_positivo && 
                        $filtro_negativo==$tem_negativo  
                        ) {

                        if ($diagnostico=='N') {
                            $previsao_parto_edi='';
                            $desc_diagnostico = 'Negativo';
                        }
                        else if ($diagnostico=='P') {
                            switch ($nascido) {
                                case 'N':
                                    $desc_diagnostico = 'Nascido';
                                    break;
                                case 'A':
                                    $desc_diagnostico = 'Aborto';
                                    break;
                                case 'M':
                                    $desc_diagnostico = 'Natimorto';
                                    break;
                                case 'S':
                                    $desc_diagnostico = 'Outro';
                                    break;
                                default:
                                    $desc_diagnostico = 'Positivo';
                                    break;       
                            }
                        }
                        else {
                            $desc_diagnostico = '';
                        }

                        echo '<tr>';
                        echo '<td width="7%">'.$codigo_edi.'</td>';
                        echo '<td width="7%">'.$descricao_raca.'</td>';    
                        echo '<td width="7%">'.$descricao_pelagem.'</td>';   
                        echo '<td width="5%">'.$data_nasc_edi.'</td>';
                        echo '<td width="7%">'.$descricao_pai.'</td>';
                        echo '<td width="7%">'.$descricao_mae.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$num_partos.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$num_aborto.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$num_natimorto.'</td>';    

                        if ($natimorto=='S') {
                            echo '<td width="6%">'.$ultimo_parto_edi.'&nbsp;&nbsp;<i class="icon_info_alt" data-toggle="tooltip" data-placement="right" title="Considerado aqui a data do Natimorto/Aborto." style="color: red;"></i></td>';
                        }  
                        else if ($bezzero_ativo=='N' && 
                                ($dias_nascimento_bezerro>0 && 
                                 $dias_nascimento_bezerro<=35)) {
                            echo '<td width="6%">'.$ultimo_parto_edi.'&nbsp;&nbsp;<i class="icon_info_alt" data-toggle="tooltip" data-placement="right" title="Bezerro Morreu a 35 dias ou menos" style="color: red;"></i></td>';
                        }
                        else {
                            echo '<td width="6%">'.$ultimo_parto_edi.'</td>';  
                        }

                        echo '<td width="6%">'.$codigo_edi_filho.'</td>';    
                        echo '<td width="6%">'.$descricao_pai_ult_filho.'</td>';
                        echo '<td width="6%">'.$desc_estacao_monta.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$num_coberturas.'</td>';    
                        echo '<td width="3%"  style="text-align: center;">'.$desc_diagnostico.'</td>';    
                        echo '<td width="8%">'.$descricao_semen.'</td>';    

                        if ($nascido=='N' || $nascido=='A' || 
                            $nascido=='M' || $nascido=='O') {
                            echo '<td width="5%" style="color: #DCDCDC;">'.$previsao_parto_edi.'</td>';    
                        } 
                        else {
                            $data_aptidao_edi = '';
                            echo '<td width="5%">'.$previsao_parto_edi.'</td>';
                        }
                        
                        echo '<td width="5%">'.$data_aptidao_edi.'</td>';    
                        echo '<td width="3%" style="color: red; text-align: center;">'.$animal_descarte.'</td>';    
                        echo '</tr>';
                        $animais_listados++;
                    }
                }
            }
        } // Fim While $reg_animais
    } // Fim if $num_rows_animais

    echo '<script type="text/javascript">
            $("#aguardar").modal("hide");
          </script>';
?>

</tbody>

<thead>
    <tr>
        <th colspan="4" style="vertical-align: middle;text-align:center;">Fêmeas</th>
        <th colspan="2" style="vertical-align: middle;text-align:center;"><?php echo $animais_listados?></th>
        <th colspan="6" style="vertical-align: middle;text-align:center;">Situação Atual</th>
        <th colspan="7" style="vertical-align: middle;text-align:center;">Situação Reprodutiva</th>
    </tr>
    <tr>
        <th style="vertical-align: middle;text-align:center;">Id Fêmea</th>
        <th style="vertical-align: middle;text-align:center;">Raça</th>
        <th style="vertical-align: middle;text-align:center;">Pelagem</th>
        <th style="vertical-align: middle;text-align:center;">Nascimento</th>
        <th style="vertical-align: middle;text-align:center;">Pai</th>
        <th style="vertical-align: middle;text-align:center;">Mãe</th>
        <th style="vertical-align: middle;text-align:center;">Nº Partos</th>
        <th style="vertical-align: middle;text-align:center;">Aborto</th>
        <th style="vertical-align: middle;text-align:center;">Natimorto</th>
        <th style="vertical-align: middle;text-align:center;">Último Parto</th>
        <th style="vertical-align: middle;text-align:center;">Último  Bezerro Vivo</th>
        <th style="vertical-align: middle;text-align:center;">Pai Semen Embrião</th>
        <th style="vertical-align: middle;text-align:center;">Última Estação de Monta</th>
        <th style="vertical-align: middle;text-align:center;">Nº Coberturas</th>
        <th style="vertical-align: middle;text-align:center;">Diagnóstico</th>
        <th style="vertical-align: middle;text-align:center;">Pai Semen Embrião</th>
        <th style="vertical-align: middle;text-align:center;">Previsão Parto</th>
        <th style="vertical-align: middle;text-align:center;">Data Aptidão</th>
        <th style="vertical-align: middle;text-align:center;">Descarte</th>
    </tr>
</thead>
</table>

                                            <div class="row">  
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="situacao_reprodutiva_geral_excel()">Excel</button>
                                                </div>
                                            </div>

                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                              <!--  </div> panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Situação Reprodutiva</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
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
                            <h4 class="modal-title">Situação Reprodutiva - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" onclick="finalizar_sair();">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="aguardar" tabindex="-1" role="dialog"    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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
        </section> <!-- wrapper -->
    </section><!--main-content -->

    <script>
        $(document).ready(function(){
           $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>

    <?php 
      $javascript_file_name = 'relatorios_reprodutivos.js';
      require 'rodape.php';
    ?>
