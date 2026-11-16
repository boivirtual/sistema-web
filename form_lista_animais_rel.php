
<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

    $codigo_alfa_filtro = $_REQUEST['codigo_alfa'];
    $codigo_numerico_filtro = $_REQUEST['codigo_numerico']; 
    $local_filtro = $_REQUEST["local"];
    $origem_filtro = $_REQUEST["origem"];
    $raca_filtro = $_REQUEST["raca"];
    $categoria_filtro = $_REQUEST["categoria"];
    $pai_filtro = $_REQUEST["pai"];
    $mae_filtro = $_REQUEST["mae"];
    $sexo_filtro = $_REQUEST["sexo"];

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

    $wsexo='';
    if ($sexo_filtro=='Todos') {
        $wsexo='';
    }
    else {
        $wsexo = " AND tbl_animal_sexo IN(";
        $wsexo .= "'" . $sexo_filtro . "'";
        $wsexo.= ")";
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

    if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial==0 && $peso_ult_final==0){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $data_nasc_inicial = $_REQUEST["data_nasc_inicial"];
    $data_nasc_final = $_REQUEST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $wativo = $_REQUEST['ativo'];

    $tipo_rel= $_REQUEST["tipo_rel"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    if ($tipo_rel == "C") {
        $desc_tipo_rel = 'Completa';
    }
    else {
        $desc_tipo_rel = 'Resumida';
    }

$solteiras = $_REQUEST["solteiras"];
$descarte = $_REQUEST["descarte"];
$paridas = $_REQUEST["paridas"];
$data_paridas_ate = $_REQUEST["data_paridas"];
$parto = $_REQUEST["parto"];
$num_parto_de = $_REQUEST['num_parto_de'];
$num_parto_ate = $_REQUEST['num_parto_ate'];
$aborto = $_REQUEST["aborto"];
$num_aborto_de = $_REQUEST['num_aborto_de'];
$num_aborto_ate = $_REQUEST['num_aborto_ate'];
$previsao_parto_de = $_REQUEST['previsao_parto_de'];
$previsao_parto_ate = $_REQUEST['previsao_parto_ate'];
$positivo = $_REQUEST['positivo'];
$negativo = $_REQUEST['negativo'];

if ($_REQUEST["solteiras"]=='' && $_REQUEST["paridas"]=='') {
    $solteiras='';
    $paridas='';
}
else {
    if ($solteiras=='') {
        $solteiras='N';
    }

    if ($paridas=='') {
        $paridas='N';
    }
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

<style type="text/css">
    table.dataTable thead th { border-bottom: 0;}
</style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php";
            include "opcoes_menu.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Listagem de Animais</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Listagem de Animais</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                                <div class=panel-body>
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="container" id="dados_cliente">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="codigo_alfa_filtro" 
                                                <?php echo "value='".$codigo_alfa_filtro."'";?>>

                                                <input type="hidden" id="codigo_numerico_filtro" 
                                                <?php echo "value='".$codigo_numerico_filtro."'";?>>

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

                                                <input type="hidden" id="sexo"
                                                    <?php echo "value='".$sexo_filtro."'";?>>

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
                                                    <?php echo "value='".$solteiras."'";?>>

                                                <input type="hidden" id="descarte"
                                                    <?php echo "value='".$descarte."'";?>>

                                                <input type="hidden" id="paridas"
                                                    <?php echo "value='".$paridas."'";?>>

                                                <input type="hidden" id="data_paridas_ate"
                                                    <?php echo "value='".$_REQUEST["data_paridas"]."'";?>>

                                                <input type="hidden" id="parto"
                                                    <?php echo "value='".$parto."'";?>>

                                                <input type="hidden" id="num_parto_de"
                                                    <?php echo "value='".$num_parto_de."'";?>>

                                                <input type="hidden" id="num_parto_ate"
                                                    <?php echo "value='".$num_parto_ate."'";?>>

                                                <input type="hidden" id="aborto"
                                                    <?php echo "value='".$aborto."'";?>>

                                                <input type="hidden" id="num_aborto_de"
                                                    <?php echo "value='".$num_aborto_de."'";?>>

                                                <input type="hidden" id="num_aborto_ate"
                                                    <?php echo "value='".$num_aborto_ate."'";?>>

                                                <input type="hidden" id="previsao_parto_de"
                                                    <?php echo "value='".$_REQUEST['previsao_parto_de']."'";?>>

                                                <input type="hidden" id="previsao_parto_ate"
                                                    <?php echo "value='".$_REQUEST['previsao_parto_ate']."'";?>>

                                                <input type="hidden" id="positivo"
                                                    <?php echo "value='".$positivo."'";?>>
                                                <input type="hidden" id="negativo"
                                                    <?php echo "value='".$negativo."'";?>>

                                                <input type="hidden" id="codigo_estacao_filtro"
                                                    <?php echo "value='".$estacao_filtro."'";?>>

                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <label class="label_consulta_rel">Filtros:</label>
                                                        <span><?php echo $descricao_filtro;?></span>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                        </button>

                                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                        onClick="lista_animais_excel()">Excel</button>
                                                    </div>
                                                </div>

<table class="table table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 10px;">

<tbody>

<?php
    if ($codigo_alfa_filtro!='' || $codigo_numerico_filtro!='') {
        $sql = "SELECT * from tbl_animais 
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_filtro' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_filtro'";
    }
    else {
        $sql = "SELECT * from tbl_animais 
            WHERE tbl_animal_lixeira=0 AND 
                  tbl_animal_ativo='$wativo'" . $wlocal . $worigem . $wsexo . 
                  $wraca . $wpai . $wmae . $wpeso_nasc . $wpeso_desmama . 
                  $wpeso_ult . $wdata_nasc .
            " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"; 
    }

                $rs = mysqli_query($conector, $sql); 
                $num_rows_animais = mysqli_num_rows($rs);

                $total_peso_nasc = 0;
                $qtd_peso_nasc = 0;
                $total_peso_ultimo = 0;
                $qtd_peso_ultimo = 0;
                $animais_listados = 0;
                $total_peso_desmama = 0;
                $qtd_peso_desmama = 0;
                $coluna_exibida = 1;
                $coluna_1= '';
                $coluna_2= '';
                $coluna_3= '';
                $coluna_4= '';
                $coluna_5= '';
                $coluna_6= '';

                if ($num_rows_animais!=0){
                    while ($reg_animal = mysqli_fetch_object($rs)){
                        $codigo = $reg_animal->tbl_animal_codigo_id;
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                        $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                        $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
                        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;

                        if ($reg_animal->tbl_animal_sexo=='M') {
                            $sexo = 'Macho';
                        }
                        else {
                            $sexo = 'Femea';
                        }

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

                        if ($descarte=='S') {
                            if ($animal_descarte=='Sim') {
                                $vaca_descarte = 'S';
                            }
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
                            if ($reg->tbl_animal_codigo_alfa=='') {
                                $descricao_mae = intval($reg->tbl_animal_codigo_numerico);
                            }
                            else {
                                $descricao_mae = $reg->tbl_animal_codigo_alfa. '-' . intval($reg->tbl_animal_codigo_numerico);
                            }
                        }
                        else {
                            $descricao_mae = '';
                        }

                        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                        $num_rows = mysqli_num_rows($tab_pai);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            $descricao_pai = $reg->tbl_semem_codigo_alfa;
                            $pai = $reg->tbl_semem_codigo_id;
                        }
                        else {
                            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                            $num_rows = mysqli_num_rows($tab_pai);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_pai);
                                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
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

                        // verifica a cobertura do animal
                        $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_item_cobertura
                            INNER JOIN tbl_cobertura
                                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                            INNER JOIN tbl_parametro_estacao_monta
                                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                            WHERE tbl_cobertura_lixeira=0 AND 
                                      tbl_ite_cobertura_codigo_id_animal='$codigo'" . $westacao . "
                            ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            $reg_cobertura = mysqli_fetch_object($sql);
                            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
                            $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
                            $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
                        }
                        else {
                            $codigo_local = 0;
                            $estacao_animal = 0;
                            $estacao_monta = '';
                        }

                        // verifica ultima estacao de monta do local

                        /*$tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
                            WHERE tbl_par_codigo_local = '$codigo_local' AND 
                                  tbl_par_lixeira=0
                            ORDER BY tbl_par_estacao_id DESC LIMIT 1");  

                        $num_rows_estacao = mysqli_num_rows($tbl_estacao);

                        if ($num_rows_estacao!=0) {
                            $reg_estacao = mysqli_fetch_object($tbl_estacao);
                            $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
                            $estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                        }
                        else {
                            $codigo_estacao = 0;
                            $estacao_monta = '';
                        }

                        $ultima_estacao = $codigo_estacao;*/

                        // verifica vacas solteiras
                        if ($solteiras=='S' || $paridas=='S') {
                            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_mae='$codigo'
                                ORDER BY tbl_animal_data_nascimento DESC limit 1");

                            $ultimo_filho = mysqli_num_rows($tbl_filhos);

                            if ($ultimo_filho!=0) {
                                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                                $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 

                                $data_acompanhamento_calculo = date("Y-m-d");
                                $date = new DateTime($ultimo_parto); // Data de Nascimento
                                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                                $idade_ano = $idade_acompanhamento->format('%Y');
                                $idade_mes = $idade_acompanhamento->format('%m');
                                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                                if ($idade < 8) {
                                    $vaca_parida = 'S';
                                    $vaca_solteira = '';
                                }
                                else {
                                    $vaca_solteira = 'S';
                                    $vaca_parida = '';
                                }
                            }
                            else {
                                $ultimo_parto = '0000-00-00';
                            }
                        }

                        // verifica partos
                        if ($sexo == 'Femea' && $num_parto_de!='' && $num_parto_ate!='') {
                            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                                WHERE tbl_animal_codigo_mae='$codigo'");

                            $num_partos = mysqli_num_rows($tbl_filhos);

                            if ($num_partos>=$num_parto_de && 
                                $num_partos<=$num_parto_ate && $idade_animal>=8) {
                                $tem_parto = "S";
                            }
                            else {
                                $tem_parto = "";
                            }
                        }

                        // verifica abortos
                        if ($sexo == 'Femea' && $num_aborto_de!='' && $num_aborto_ate!='') {
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

                        // Verifica previsão de parto
                        if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_cobertura_lixeira=0 AND 
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P'
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows_coberturas!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                                $cobertura_id = $reg_cobertura->tbl_cobertura_id;

                                $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

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
                                $data_previsao_parto = '0000-00-00';
                            }
                        }

                        // verifica natimortos, nascidos ou abortos na estacao

                        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_cobertura_lixeira=0 AND  
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                        $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

                        if ($num_rows_item!=0) {
                            $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);

                            $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
                        }
                        else {
                            $nascido_aborto = '';
                        }
                        
                        // Verifica diagnostico
                        if ($positivo=='S' || $negativo=='S'){
                            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                                INNER JOIN tbl_cobertura
                                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                                WHERE tbl_cobertura_lixeira=0 AND 
                                      tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                                      tbl_cobertura_controle = 'C' AND 
                                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                            $num_rows = mysqli_num_rows($tbl_item_cobertura);

                            if ($num_rows!=0) {
                                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                                $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

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
                        }


                        if ($positivo=='S' AND 
                            $nascido_aborto!='') {
                            $tem_positivo='';
                        }

                        if ($data_previsao_parto!='0000-00-00' AND 
                            $nascido_aborto!='') {
                            $data_previsao_parto='0000-00-00';
                        }

                        if ($tipo_rel=='C') {
                            if ($wcategoria=="" && 
                                $descarte==$vaca_descarte && 
                                $data_previsao_parto>=$previsao_parto_de && 
                                $data_previsao_parto<=$previsao_parto_ate && 

                                (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                                    $nascido=='M' || $nascido=='O'))) || 
                                ($paridas==$vaca_parida && 
                                $ultimo_parto>=$data_paridas_de && 
                                $ultimo_parto<=$data_paridas_ate)) &&

                                $parto==$tem_parto &&
                                $aborto==$tem_aborto && 
                                $positivo==$tem_positivo && 
                                $negativo==$tem_negativo 
                                ) {

                                // AJUSTE DO PESO DE DESMAMA

                                if ($peso_desmama!='' && $peso_desmama!=0) {
                                    if ($peso_nasc=='' || $peso_nasc==0) {
                                        $peso_nasc = 30;
                                        $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                                    }

                                    $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                                    $data_final = $reg_animal->tbl_animal_data_desmama;
                                    $diferenca = strtotime($data_final) - 
                                     strtotime($data_inicial);
                                    $dias = floor($diferenca / (60 * 60 * 24));

                                    $diferenca_peso = $peso_desmama - $peso_nasc;
                                    $gmd = $diferenca_peso/$dias;

                                    $peso_desmama = $peso_nasc + ($gmd * 205);
                                    $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                                }

                                // FIM AJUSTE DO PESO DE DESMAMA

                                if ($peso_nasc!='' && $peso_nasc!=0){
                                    $total_peso_nasc+= $peso_nasc;
                                    $qtd_peso_nasc++;
                                }

                                if ($peso_desmama!='' && $peso_desmama!=0){
                                    $total_peso_desmama+= $peso_desmama;
                                    $qtd_peso_desmama++;
                                }

                                // ULTIMO PESO
                                if ($peso_ultimo=='' || $peso_ultimo==0){
                                    if ($peso_desmama!='' && $peso_desmama!=0) {
                                        $peso_ultimo = $peso_desmama;
                                        $data_ultimo = new DateTime($reg_animal->tbl_animal_data_desmama);
                                        $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                                        $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                        $total_peso_ultimo+= $peso_ultimo;
                                        $qtd_peso_ultimo++;
                                    }
                                    else if ($peso_nasc!='' && $peso_nasc!=0){
                                        $peso_ultimo = $peso_nasc;
                                        $data_ultimo = new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                                        $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                                        $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                        $total_peso_ultimo+= $peso_ultimo;
                                        $qtd_peso_ultimo++;
                                    }
                                    else {
                                        $data_ultimo_edi = '';
                                    }
                                } 
                                else {
                                    $total_peso_ultimo+= $peso_ultimo;
                                    $qtd_peso_ultimo++;
                                }
                                // FIM ULTIMO PESO

                                echo '<tr>';
                                echo '<td width="8%" class="codigo">'.$codigo_edi.'</td>';    
                                echo '<td width="15%" class="local">'.$desc_local.'</td>';    
                                echo '<td width="3%" class="sexo" style="text-align: center;">'.$sexo.'</td>';   
                                echo '<td width="6%" class="data_nasc" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                echo '<td width="10%" class="raca">'.$descricao_raca.'</td>';    
                                echo '<td width="10%" class="pelagem">'.$descricao_pelagem.'</td>';    
                                echo '<td width="7%" class="mae">'.$descricao_mae.'</td>';    
                                echo '<td width="7%" class="pai">'.$descricao_pai.'</td>';    
                                echo '<td width="11%" style="white-space: normal;">'.$observacao.'</td>';    
                                echo '<td width="6%" class="peso_nasc" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                                echo '<td width="6%" class="peso_desmama" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                                echo '<td width="6%" class="peso_ult" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                                echo '<td width="6%" class="data_ult" style="text-align: center;">'.$data_ultimo_edi.'</td>';    
                                echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';    
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

                                        (($solteiras==$vaca_solteira && ($data_previsao_parto=='0000-00-00' || ($nascido=='N' || $nascido=='A' || 
                                            $nascido=='M' || $nascido=='O'))) || 
                                        ($paridas==$vaca_parida && 
                                        $ultimo_parto>=$data_paridas_de && 
                                        $ultimo_parto<=$data_paridas_ate)) &&

                                        $parto==$tem_parto &&
                                        $aborto==$tem_aborto && 
                                        $positivo==$tem_positivo && 
                                        $negativo==$tem_negativo
                                    ) {

                                        // AJUSTE DO PESO DE DESMAMA

                                        if ($peso_desmama!='' && $peso_desmama!=0) {
                                            if ($peso_nasc=='' || $peso_nasc==0) {
                                                $peso_nasc = 30;
                                                $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                                            }

                                            $data_inicial = $reg_animal->tbl_animal_data_nascimento;
                                            $data_final = $reg_animal->tbl_animal_data_desmama;
                                            $diferenca = strtotime($data_final) - 
                                             strtotime($data_inicial);
                                            $dias = floor($diferenca / (60 * 60 * 24));

                                            $diferenca_peso = $peso_desmama - $peso_nasc;
                                            $gmd = $diferenca_peso/$dias;

                                            $peso_desmama = $peso_nasc + ($gmd * 205);
                                            $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                                        }

                                        // FIM AJUSTE DO PESO DE DESMAMA


                                        if ($peso_nasc!='' && $peso_nasc!=0){
                                            $total_peso_nasc+= $peso_nasc;
                                            $qtd_peso_nasc++;
                                        }

                                        if ($peso_desmama!='' && $peso_desmama!=0){
                                            $total_peso_desmama+= $peso_desmama;
                                            $qtd_peso_desmama++;
                                        }

                                        // ULTIMO PESO
                                        if ($peso_ultimo=='' || $peso_ultimo==0){
                                            if ($peso_desmama!='' && $peso_desmama!=0) {
                                                $peso_ultimo = $peso_desmama;
                                                $data_ultimo = new DateTime($reg_animal->tbl_animal_data_desmama);
                                                $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                                                $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                                $total_peso_ultimo+= $peso_ultimo;
                                                $qtd_peso_ultimo++;
                                            }
                                            else if ($peso_nasc!='' && $peso_nasc!=0){
                                                $peso_ultimo = $peso_nasc;
                                                $data_ultimo = new DateTime($reg_animal->tbl_animal_data_primeiro_peso);
                                                $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                                                $data_ultimo_edi = $data_ultimo->format('d/m/Y');
                                                $total_peso_ultimo+= $peso_ultimo;
                                                $qtd_peso_ultimo++;
                                            }
                                            else {
                                                $data_ultimo_edi = '';
                                            }
                                        } 
                                        else {
                                            $total_peso_ultimo+= $peso_ultimo;
                                            $qtd_peso_ultimo++;
                                        }
                                        // FIM ULTIMO PESO

                                        echo '<tr>';
                                        echo '<td width="5%" class="codigo">'.$codigo_edi.'</td>';    
                                        echo '<td width="20%">'.$desc_local.'</td>';    
                                        echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                                        echo '<td width="5%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                        echo '<td width="15%">'.$descricao_raca.'</td>';    
                                        echo '<td width="15%">'.$descricao_pelagem.'</td>';    
                                        echo '<td width="5%">'.$descricao_mae.'</td>';    
                                        echo '<td width="5%">'.$descricao_pai.'</td>';    
                                        echo '<td width="17%" style="white-space: normal;">
                                             '.$observacao.'</td>';    
                                        echo '<td width="5%" style="text-align: right;">'.$peso_nasc_edi.'</td>'; 
                                        echo '<td width="5%" style="text-align: right;">'.$peso_desmama_edi.'</td>';    
                                        echo '<td width="5%" style="text-align: right;">'.$peso_ultimo_edi.'</td>';    
                                        echo '<td width="3%" style="text-align: center; color: red;">'.$animal_descarte.'</td>';
                                        echo '</tr>';
                                        $animais_listados++;
                                    }
                                }
                            }
                        }
                        else {
                            if ($wcategoria=="" && 
                                $descarte==$vaca_descarte && 
                                $data_previsao_parto>=$previsao_parto_de && 
                                $data_previsao_parto<=$previsao_parto_ate && 
                                $solteiras==$vaca_solteira && 
                                $paridas==$vaca_parida && 
                                $ultimo_parto>=$data_paridas_de && 
                                $ultimo_parto<=$data_paridas_ate &&
                                $parto==$tem_parto &&
                                $aborto==$tem_aborto && 
                                $positivo==$tem_positivo && 
                                $negativo==$tem_negativo
                            ) {

                                $animais_listados++;

                                if ($coluna_exibida<=6){
                                    if ($coluna_exibida==1) {
                                        $coluna_1=$codigo_edi;
                                        $coluna_exibida++;
                                    }
                                    else if($coluna_exibida==2) {
                                        $coluna_2=$codigo_edi;
                                        $coluna_exibida++;
                                    }
                                    else if($coluna_exibida==3) {
                                        $coluna_3=$codigo_edi;
                                        $coluna_exibida++;
                                    }
                                    else if($coluna_exibida==4) {
                                        $coluna_4=$codigo_edi;
                                        $coluna_exibida++;
                                    }
                                    else if($coluna_exibida==5) {
                                        $coluna_5=$codigo_edi;
                                        $coluna_exibida++;
                                    }
                                    else if($coluna_exibida==6) {
                                        $coluna_6=$codigo_edi;
                                        $coluna_exibida++;
                                    }
                                }
                                else {
                                    echo '<tr>';
                                    echo '<td width="10%">'.$coluna_1.'</td>';    
                                    echo '<td width="10%">'.$coluna_2.'</td>';    
                                    echo '<td width="10%">'.$coluna_3.'</td>';    
                                    echo '<td width="10%">'.$coluna_4.'</td>';    
                                    echo '<td width="10%">'.$coluna_5.'</td>';    
                                    echo '<td width="10%">'.$coluna_6.'</td>';    
                                    echo '</tr>';
                                    $coluna_exibida=1;
                                    $coluna_1= '';
                                    $coluna_2= '';
                                    $coluna_3= '';
                                    $coluna_4= '';
                                    $coluna_5= '';
                                    $coluna_6= '';
                                    $coluna_1=$codigo_edi;
                                    $coluna_exibida++;
                                }
                            }
                            else{
                                for ($k=0; $k < $quantidade_categoria; $k++) { 
                                    $value = $wcategoria[$k];
                                    if ($value==$codigo_categoria &&
                                        $descarte==$vaca_descarte && 
                                        $data_previsao_parto>=$previsao_parto_de && 
                                        $data_previsao_parto<=$previsao_parto_ate && 
                                        $solteiras==$vaca_solteira && 
                                        $paridas==$vaca_parida && 
                                        $ultimo_parto>=$data_paridas_de && 
                                        $ultimo_parto<=$data_paridas_ate &&
                                        $parto==$tem_parto &&
                                        $aborto==$tem_aborto && 
                                        $positivo==$tem_positivo && 
                                        $negativo==$tem_negativo
                                    ) {

                                        $animais_listados++;

                                        if ($coluna_exibida<=6){
                                            if ($coluna_exibida==1) {
                                                $coluna_1=$codigo_edi;
                                                $coluna_exibida++;
                                            }
                                            else if($coluna_exibida==2) {
                                                $coluna_2=$codigo_edi;
                                                $coluna_exibida++;
                                            }
                                            else if($coluna_exibida==3) {
                                                $coluna_3=$codigo_edi;
                                                $coluna_exibida++;
                                            }
                                            else if($coluna_exibida==4) {
                                                $coluna_4=$codigo_edi;
                                                $coluna_exibida++;
                                            }
                                            else if($coluna_exibida==5) {
                                                $coluna_5=$codigo_edi;
                                                $coluna_exibida++;
                                            }
                                            else if($coluna_exibida==6) {
                                                $coluna_6=$codigo_edi;
                                                $coluna_exibida++;
                                            }
                                        }
                                        else {
                                            echo '<tr>';
                                            echo '<td width="10%">'.$coluna_1.'</td>';    
                                            echo '<td width="10%">'.$coluna_2.'</td>';    
                                            echo '<td width="10%">'.$coluna_3.'</td>';    
                                            echo '<td width="10%">'.$coluna_4.'</td>';    
                                            echo '<td width="10%">'.$coluna_5.'</td>';    
                                            echo '<td width="10%">'.$coluna_6.'</td>';    
                                            echo '</tr>';
                                            $coluna_exibida=1;
                                            $coluna_1= '';
                                            $coluna_2= '';
                                            $coluna_3= '';
                                            $coluna_4= '';
                                            $coluna_5= '';
                                            $coluna_6= '';
                                            $coluna_1=$codigo_edi;
                                            $coluna_exibida++;
                                        }
                                    }
                                }
                            }

                        }
                    }
                    if ($tipo_rel=='R') {
                        echo '<tr>';
                        echo '<td width="10%">'.$coluna_1.'</td>';    
                        echo '<td width="10%">'.$coluna_2.'</td>';    
                        echo '<td width="10%">'.$coluna_3.'</td>';    
                        echo '<td width="10%">'.$coluna_4.'</td>';    
                        echo '<td width="10%">'.$coluna_5.'</td>';    
                        echo '<td width="10%">'.$coluna_6.'</td>';    
                        echo '</tr>';
                    }
                }

                echo '
                <script type="text/javascript">
                    $("#aguardar").modal("hide");
                </script>
                ';
            ?>

        </tbody>

<thead>
            <?php
                if ($tipo_rel=='C') :
                    if ($total_peso_nasc!=0){
                        $media_total_peso_nasc = $total_peso_nasc/$qtd_peso_nasc;
                        $media_total_peso_nasc_edi = number_format($media_total_peso_nasc,2,',','.');
                    }
                    else {
                        $media_total_peso_nasc_edi = '';
                    }

                    if ($total_peso_desmama!=0){
                        $media_total_peso_desmama = $total_peso_desmama/$qtd_peso_desmama;
                        $media_total_peso_desmama_edi = number_format($media_total_peso_desmama,2,',','.');
                    }
                    else {
                        $media_total_peso_desmama_edi = '';
                    }

                    if ($total_peso_ultimo!=0){
                        $media_total_peso_ultimo = $total_peso_ultimo/$qtd_peso_ultimo;
                        $total_peso_ultimo_edi = number_format($total_peso_ultimo,2,',','.');
                        $media_total_peso_ultimo_edi = number_format($media_total_peso_ultimo,2,',','.');
                    }
                    else {
                        $total_peso_ultimo_edi = '';  
                        $media_total_peso_ultimo_edi = ''; 
                    }
            ?>
            <tr>
                <th style="vertical-align: middle;text-align:center;">Animais</th>
                <th rowspan="2" colspan="8"></th>
                <th style="vertical-align: middle;text-align:center;">Média Nasc</th>
                <th style="vertical-align: middle;text-align:center;">Média Desmama</th>
                <th style="vertical-align: middle;text-align:center;">Peso Médio</th>
                <th style="vertical-align: middle;text-align:center;">Peso Total</th>
            </tr>
            <tr>
                <td style="text-align: center" class="animais_listados"><?php echo $animais_listados?></td>
                <td style="text-align: right;"><?php echo $media_total_peso_nasc_edi?></td>
                <td style="text-align: right;"><?php echo $media_total_peso_desmama_edi?></td>
                <td style="text-align: right;"><?php echo $media_total_peso_ultimo_edi?></td>
                <td style="text-align: right;"><?php echo $total_peso_ultimo_edi?></td>
            </tr>

            <tr>
                <th style="vertical-align: middle;text-align:center;">Id Animal</th>
                <th style="vertical-align: middle;text-align:center;">Fazenda</th>
                <th style="vertical-align: middle;text-align:center;">Sexo</th>
                <th style="vertical-align: middle;text-align:center;">Nascimento</th>
                <th style="vertical-align: middle;text-align:center;">Raça</th>
                <th style="vertical-align: middle;text-align:center;">Pelagem</th>
                <th style="vertical-align: middle;text-align:center;">Mãe Id</th>
                <th style="vertical-align: middle;text-align:center;">Pai Id</th>
                <th style="vertical-align: middle;text-align:center;">Observação</th>
                <th style="vertical-align: middle;text-align:center;">Peso Nasc Kg</th>
                <th style="vertical-align: middle;text-align:center;">Peso Desmama Kg</th>
                <th style="vertical-align: middle;text-align:center;">Peso Atual Kg</th>
                <th style="vertical-align: middle;text-align:center;">Última Pesagem</th>
                <th style="vertical-align: middle;text-align:center;">Descarte</th>
            </tr>

            <?php
                else :
            ?>
            <tr>
                <th style="text-align: center">Id Animais Listados</th>
                <th style="text-align: center"><?php echo  $animais_listados?></th>
                <th colspan="4"></th>
            </tr>
            <tr>
                <th>Id Animal</th>
                <th>Id Animal</th>
                <th>Id Animal</th>
                <th>Id Animal</th>
                <th>Id Animal</th>
                <th>Id Animal</th>
            </tr>
                
            <?php
                endif;
            ?>
        </thead>
</table>
                                            <div class="row">  
                                                <div class="col-md-12">
                                                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                </button>

                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                onClick="lista_animais_excel()">Excel</button>
                                                </div>
                                            </div>

                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
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
                            <h4 class="modal-title">Listagem Animal</h4>
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
                            <h4 class="modal-title">Listagem Animal - Mensagem</h4>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                            </button>
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


<?php 
  $javascript_file_name = 'tabela_animais.js';
  require 'rodape.php';
?>




