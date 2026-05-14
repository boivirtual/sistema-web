<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");

    $data_inicial = $_REQUEST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = '01';

    $data_final = $_REQUEST['data_final'];
    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    if ($qtd_meses>12) {
    echo '
       <script type="text/javascript">
            alert ("Selecione no máximo 12 meses.");
            location.href="form_rel_gmd.php";
       </script>';
        exit;
    }

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));
    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

    $ano_mes = $data_array->format('Y').$data_array->format('m');
    $array_mes_ano[$ano_mes]=$ano_mes;
    $array_peso[$ano_mes]=0;

    for ($i=1; $i < $qtd_meses; $i++) { 
        $proximo_mes=1;
        $data_array->add(new DateInterval('P'.$proximo_mes.'M'));

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
        $mes_extenco = ucfirst(utf8_encode($mes_extenco));
        $array_mes_extenco[$i]=$mes_extenco.'/'.$ano_atual;

        if ($mes_extenco == 'Dezembro') {
            $ano_atual++;
        }

        $array_mes[$i]=$data_array->format('m');
        $array_ano[$i]=$data_array->format('Y');
        $ano_mes = $data_array->format('Y').$data_array->format('m');
        $array_mes_ano[$ano_mes]=$ano_mes;
        $array_peso[$ano_mes]=0;
    } 

    @ session_start(); 

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
    $wlocal_anterior='';

    if ($local_filtro!='') {
        $wlocal = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal.= $local;
        $wlocal.= ")";

        $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= ")";
        $wlocal_anterior.= " OR tbl_animal_codigo_fazenda_anterior IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= "))";
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
        table.dataTable thead th { border-bottom: 0; }
    </style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php";
            include "opcoes_menu.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Ganho de Peso</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Ganho de Peso</h3>
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

                                                <input type="hidden" id="controle_estoque"
                                                    <?php echo "value='".$controle_estoque."'";?>>

                                                <input type="hidden" id="codigo_alfa_filtro" 
                                                <?php echo "value='".$codigo_alfa_filtro."'";?>>

                                                <input type="hidden" id="codigo_numerico_filtro" 
                                                <?php echo "value='".$codigo_numerico_filtro."'";?>>

                                                <input type="hidden" id="data_inicial"
                                                    <?php echo "value='".$data_inicial."'";?>>

                                                <input type="hidden" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>

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
                                                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_gmd()">Voltar
                                                </button>

                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                onClick="lista_gmd_excel()">Excel</button>
                                                </div>
                                            </div>

                                            <hr align="center"> 

<table id="tabela_gmd_geral" class="table table-bordered table-advance table-hover" style="width:50%; font-size:10px; float: left; margin-left: 10px; border: none;">

<tbody>

<?php
    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $animais_listados=0;
    $gmd_total = 0;
    $numero_gmd = 0;

    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'");

    $num_rows = mysqli_num_rows($tbl_categoria);    

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;
            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

            if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                $desc_categoria = ' > 36 meses';
            }
            else {
                $desc_categoria =  $reg_categoria->tab_categoria_idade_de . ' a ' .
                $reg_categoria->tab_categoria_idade_ate . ' meses';
            }

            $array_categoria[$codigo_categoria] = $codigo_categoria;
            $array_desc_categoria[$codigo_categoria] = $desc_categoria;
            $array_qtd_macho_categoria[$codigo_categoria] = 0;
            $array_qtd_femea_categoria[$codigo_categoria] = 0;
            $array_gmd_macho_categoria[$codigo_categoria] = 0;
            $array_gmd_femea_categoria[$codigo_categoria] = 0;
        }
    }   

    if ($codigo_alfa_filtro!='' || $codigo_numerico_filtro!='') {
        $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
            WHERE tbl_animal_codigo_alfa = '$codigo_alfa_filtro' AND 
                  tbl_animal_codigo_numerico = '$codigo_numerico_filtro'");
    }
    else {
        $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
            WHERE tbl_animal_lixeira=0 AND
                  tbl_animal_ativo='$wativo'" . $wlocal . $wsexo . $wraca . $wpai . $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
                  " OR (DATE(tbl_animal_baixado_em)>='$data_inicial' AND DATE(tbl_animal_baixado_em)<='$data_final' AND tbl_animal_ativo='N' AND (tbl_animal_situacao='V' OR tbl_animal_situacao='M'))" . $wlocal_anterior . $wsexo . $wraca . $wpai . 
                          $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
            " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"); 
    }

    $num_rows_animais = mysqli_num_rows($tbl_animal);

    if ($num_rows_animais!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $sexo = $reg_animal->tbl_animal_sexo; 
            $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

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

            $tem_negativo = '';
            $tem_positivo = '';
            $vaca_descarte = '';

            if ($descarte=='S') {
                if ($animal_descarte=='S') {
                    $vaca_descarte = 'S';
                }
            }

            $data_peso_nascimento=0;
            $peso_nascimento=0;

            if ($reg_animal->tbl_animal_primeiro_peso!='') {
                $data_primeiro_peso = substr($reg_animal->tbl_animal_data_primeiro_peso, 0, 10);

                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                    $data_peso_nascimento = $data_primeiro_peso;
                    $peso_nascimento = $reg_animal->tbl_animal_primeiro_peso;
                }
            }
            else {
                if ($reg_animal->tbl_animal_movimentacao_compra!='') {
                    $data_primeiro_peso = $reg_animal->tbl_animal_data_compra;

                    if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                        $data_peso_nascimento = $data_primeiro_peso;
                        $peso_nascimento = $reg_animal->tbl_animal_ultimo_peso;
                    }
                }
            }

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
            $data_acompanhamento_calculo = $data_final;
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria_animal = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria_animal);    

            if ($num_rows!=0) {
                while ($reg_cat_animal = mysqli_fetch_object($categoria_animal)) {
                    $idade_de = $reg_cat_animal->tab_categoria_idade_de;
                    $idade_ate = $reg_cat_animal->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria_animal = $reg_cat_animal->tab_codigo_categoria_idade;
                    }
                }
            }                   

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
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P'
                    ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

                $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows_coberturas!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                    $cobertura_id = $reg_cobertura->tbl_cobertura_id;

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
                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
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
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_cobertura_codigo_estacao_monta =
                          '$estacao_animal'  
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

            if ($positivo=='S' AND $nascido_aborto!='') {
                $tem_positivo='';
            }

            if ($data_previsao_parto!='0000-00-00' AND 
                $nascido_aborto!='') {
                $data_previsao_parto='0000-00-00';
            }

            if ($data_peso_nascimento!=0) {
                $data_peso_inicial = $data_peso_nascimento;
                $peso_inicial = $peso_nascimento;
            }
            else {
                $data_peso_inicial='0000-00-00';
                $peso_inicial = 9999;
            }

            if ($descarte==$vaca_descarte && 
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

                $data_peso_final = '0000-00-00';
                $peso_final = 9999;

                $tbl_peso = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    WHERE tbl_ite_pesagem_data_emissao>='$data_inicial' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final' AND 
                          tbl_ite_pesagem_codigo_id_animal='$codigo' AND 
                          tbl_ite_pesagem_peso !=0
                        ORDER BY tbl_ite_pesagem_data_emissao ASC");

                $num_rows_peso = mysqli_num_rows($tbl_peso);    

                if ($num_rows_peso!=0) {
                    if ($data_peso_nascimento!=0) {
                        $partes = explode("-", $data_peso_nascimento);

                        for ($i=0; $i < $qtd_meses; $i++) { 
                            if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                $peso_inicial=$peso_nascimento;
                            }
                        }
                    }

                    while ($reg_peso = mysqli_fetch_object($tbl_peso)) {
                        $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                        $peso = $reg_peso->tbl_ite_pesagem_peso;

                        if ($peso == 0) {
                            $peso = 9999;
                        }

                        $partes = explode("-", $data_peso_inicial);
                        $ano_mes_peso_inicial = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso_final);
                        $ano_mes_peso_final = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso);
                        $ano_mes_peso = $partes[0].$partes[1];

                        if ($data_peso_inicial=='0000-00-00') {
                            $data_peso_inicial=$data_peso;
                            $peso_inicial=$peso;
                        }

                        if ($ano_mes_peso_inicial==$ano_mes_peso) {
                            if ($peso_inicial==9999) {
                                if ($peso<$peso_inicial && $peso!=0) {
                                    $data_peso_inicial=$data_peso;
                                    $peso_inicial = $peso;
                                }
                            }
                        }

                        if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                            if ($ano_mes_peso_final==$ano_mes_peso) {
                                if ($peso<$peso_final && $peso!=0) {
                                    $data_peso_final=$data_peso;
                                    $peso_final = $peso;
                                }
                            }
                            else {
                                $data_peso_final=$data_peso;
                                $peso_final = $peso;
                            }
                        }
                    }  
                } 

                if ($peso_inicial==9999) {
                    $peso_inicial = 0;
                }

                if ($peso_final==9999) {
                    $peso_final = 0;
                }

                $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
                $dias = floor($diferenca / (60 * 60 * 24)); 

                if ($peso_final && $peso_inicial) {
                    $ganho = $peso_final - $peso_inicial;
                }
                else {
                    $ganho = 0;
                }

                if ($ganho!=0 && $dias!=0) {
                    $gmd = $ganho / $dias;
                }
                else {
                    $gmd=0;
                }

                if ($gmd!=0) {
                    if ($sexo=="M") {
                        $array_gmd_macho_categoria[$codigo_categoria_animal]+=$gmd;
                        $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                    }
                    else {
                        $array_gmd_femea_categoria[$codigo_categoria_animal]+=$gmd;
                        $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                    }
                }
            }
        }
    }

    if ($wcategoria=="") {

        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            if ($array_qtd_macho_categoria[$j]!=0) {
                echo '<tr>';
                echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">M</td>';
                echo '<td width="10%" style="text-align: center;">'.$array_qtd_macho_categoria[$j].'</td>';

                $gmd = $array_gmd_macho_categoria[$j]/
                       $array_qtd_macho_categoria[$j];

                $gmd_total+= $array_gmd_macho_categoria[$j];

                if ($gmd!=0) {
                    $numero_gmd+= $array_qtd_macho_categoria[$j];
                }

                $gmd_edi = number_format($gmd,3,',','.');
                echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                echo '<td style="border: none"></td>';    
                echo '<td style="border: none"></td>';    
                echo '<td style="border: none"></td>';    
                echo '</tr>';
                $animais_listados+=$array_qtd_macho_categoria[$j];
            }

            if ($array_qtd_femea_categoria[$j]!=0) {
                echo '<tr>';
                echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">F</td>';
                echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';

                $gmd = $array_gmd_femea_categoria[$j]/
                       $array_qtd_femea_categoria[$j];

                $gmd_total+= $array_gmd_femea_categoria[$j];

                if ($gmd!=0) {
                    $numero_gmd+= $array_qtd_femea_categoria[$j];
                }

                $gmd_edi = number_format($gmd,3,',','.');
                echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';
                echo '<td style="border: none"></td>';    
                echo '<td style="border: none"></td>';    
                echo '<td style="border: none"></td>';    
                echo '</tr>';
                $animais_listados+=$array_qtd_femea_categoria[$j];
            }
        }
    }
    else {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);

            for ($k=0; $k < $quantidade_categoria; $k++) { 
                $value = $wcategoria[$k];
                if ($value==$j) {
                    
                    if ($array_qtd_macho_categoria[$j]!=0) {
                        echo '<tr>';
                        echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">M</td>';
                        echo '<td width="10%" style="text-align: center;">'.$array_qtd_macho_categoria[$j].'</td>';

                        $gmd = $array_gmd_macho_categoria[$j]/
                               $array_qtd_macho_categoria[$j];

                        $gmd_total+= $array_gmd_macho_categoria[$j];
                        $numero_gmd+= $array_qtd_macho_categoria[$j];

                        $gmd_edi = number_format($gmd,3,',','.');
                        echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td style="border: none"></td>';    
                        echo '</tr>';
                        $animais_listados+=$array_qtd_macho_categoria[$j];
                    }

                    if ($array_qtd_femea_categoria[$j]!=0) {
                        echo '<tr>';
                        echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">F</td>';
                        echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';

                        $gmd = $array_gmd_femea_categoria[$j]/
                               $array_qtd_femea_categoria[$j];

                        $gmd_total+= $array_gmd_femea_categoria[$j];
                        $numero_gmd+= $array_qtd_femea_categoria[$j];

                        $gmd_edi = number_format($gmd,3,',','.');
                        echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td style="border: none"></td>';    
                        echo '</tr>';
                        $animais_listados+=$array_qtd_femea_categoria[$j];
                    }
                }
            }
        }
    }
    echo '
       <script type="text/javascript">
        $("#aguardar").modal("hide");
       </script>';
?>

</tbody>

<thead>
<?php
    if ($gmd_total!=0 && $numero_gmd>0) {
        $media_gmd = $gmd_total / $numero_gmd;
        $media_gmd_edi = number_format($media_gmd,3,',','.');
    }
    else {
        $media_gmd_edi = 0;
    }
?>
    <tr>
        <th style="text-align: center">Animais</th>
        <th colspan="3"></th>
        <th style="border: none"></th>
        <th style="border: none"></th>
        <th style="text-align: center">GMD Global</th>
    </tr>
    <tr>
        <td style="text-align: center" class="animais_listados"><?php echo $animais_listados?></td>
        <td colspan="3"></td>
        <td style="border: none"></td>
        <td style="border: none"></td>
        <td style="text-align: center;"><?php echo $media_gmd_edi?></td>
    </tr>
    <tr>
        <th>Categoria</th>
        <th>Sexo</th>
        <th>Qtde</th>
        <th>GMD</th>
        <th style="border: none"></th>
        <th style="border: none"></th>
        <th style="border: none"></th>
    </tr>
</thead>

</table>

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
                            <h4 class="modal-title">Relatório Ganho de Peso</h4>
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
                            <h4 class="modal-title">Relatório Ganho de Peso - Mensagem</h4>
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




