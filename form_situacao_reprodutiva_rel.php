<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";


    $data_sistema = date("Y-m-d");
    $data_hoje = date("Y-m-d");

    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];
    $codigo_alfa = $_REQUEST["codigo_alfa"];
    $codigo_numerico = $_REQUEST["codigo_numerico"];
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

    if ($tipo_rel=='I') {

        if ($codigo_alfa=='') {
            $codigo_consulta = $codigo_numerico;            
        }
        else {
            $codigo_consulta = $codigo_alfa . '-' . $codigo_numerico;
        }

        $mensagem = '';

        $tbl_animais = mysqli_query($conector, "SELECT * from tbl_animais 
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_animal_codigo_fazenda
            WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
                  tbl_animal_codigo_numerico='$codigo_numerico' AND 
                  tbl_animal_sexo='F'"); 

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        if ($num_rows_animais!=0) {
            $reg_animal = mysqli_fetch_object($tbl_animais);
            $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
            $ativo = $reg_animal->tbl_animal_ativo;
            $animal_situacao = $reg_animal->tbl_animal_situacao;
            $descarte = $reg_animal->tbl_animal_descarte_reproducao;
            $descarte_em = new DateTime($reg_animal->tbl_animal_descarte_em);
            $descarte_em_edi = $descarte_em->format('d/m/Y H:i:s');
            $descarte_por = 'Por ' . $reg_animal->tbl_animal_descarte_por .' em '. $descarte_em_edi;
            $nome_pessoa = $reg_animal->tbl_pessoa_nome; 
            $descricao_filtro = $nome_pessoa;                        
            $num_coberturas =$reg_animal->tbl_animal_numero_coberturas;
            $num_abortos = $reg_animal->tbl_animal_numero_abortos;
            $mae =  $reg_animal->tbl_animal_numero_abortos;
            $pai = $reg_animal->tbl_animal_codigo_pai;

            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
            $num_rows_pai = mysqli_num_rows($tab_pai);

            if ($num_rows_pai!=0){
                $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_semem_codigo_alfa;
            }
            else {
                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                $num_rows_pai = mysqli_num_rows($tab_pai);

                if ($num_rows_pai!=0){
                    $reg = mysqli_fetch_object($tab_pai);
                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                }
                else {
                    $descricao_pai = '';
                }
            }

            $mae =  $reg_animal->tbl_animal_codigo_mae;

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

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');

            if ($idade_ano==0 && $idade_mes!=0) {
                $idade_animal = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $idade_animal = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else {
                $idade_animal = '';
            }

            $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
            $data_nascimento_edi = $data->format('d/m/Y');

            if ($ativo=='N') {
                $ativo = 'Não';
            }
            else {
                $ativo = 'Sim';
            }

            switch ($animal_situacao) {
            case 'T':
                $animal_situacao='Aguardando Transferência';
                break;
            case 'V':
                $animal_situacao='Vendido';
                break;
            case 'M':   
                $animal_situacao='Morte';
                break;
            case 'S':   
                $animal_situacao='Outra Saída';
                break;
            } 

            if ($reg_animal->tbl_animal_em_estacao_monta=='S') {
                $em_estacao_monta ='SIM';
            }
            else {
                $em_estacao_monta ='NÃO';
            }

            // Verifica quantas estações teve para a femea
            $qtd_estacoes = 0;
            $id_estacao_ant = 0;

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * from tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND  tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_lixeira = 0
                ORDER BY tbl_cobertura_codigo_estacao_monta ASC"); 

            $num_rows_itens = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_itens!=0) {
                while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
                    $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;

                    if ($id_estacao != $id_estacao_ant) {
                        $qtd_estacoes++;
                        $id_estacao_ant=$id_estacao;
                    }
                }
            }

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * from tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                INNER JOIN tbl_parametro_estacao_monta
                        ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
                WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND      tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_lixeira = 0
                ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1"); 
            
            $num_rows_itens = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_itens!=0) {
                $reg_item = mysqli_fetch_object($tbl_item_cobertura);
                $id_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;
                $estacao_monta = $reg_item->tbl_par_estacao_nome;
            }
            else {
                $estacao_monta = '';
                $id_estacao = 0;
            }

            // primeiro verifica quantos partos
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_animal_id'");

            $num_partos = mysqli_num_rows($tbl_filhos);
                                // verifica parto natimorto

            $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                      tbl_mov_estoque_codigo_id_animal=999999999 and 
                      tbl_mov_estoque_entrada_saida='E' and 
                      tbl_mov_estoque_tipo_movimentacao='N'");
            $num_natimorto = mysqli_num_rows($tbl_natimorto);
            $num_partos = $num_partos + $num_natimorto;

            // agora verifica qual o ultimo parto para saber a idade
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_animal_id'
                ORDER BY tbl_animal_data_nascimento DESC limit 1");

            $ultimo_filho = mysqli_num_rows($tbl_filhos);

            $parida = '';
            $solteira = '';
            $situacao = '';

            if ($ultimo_filho!=0) {
                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                $nascimento_filho = $reg_filhos->tbl_animal_data_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($nascimento_filho); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade_filho = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                if ($idade_filho < 8) {
                    $parida = 'S';
                    $situacao = 'Parida';
                }
                else {
                    $solteira = 'S';
                    $situacao = 'Solteira';
                }
            }
            else {
                $solteira = 'S';
                $situacao = 'Solteira';
            }
        }
        else {
            $mensagem = ' - Registro não encontrado';
        }
    }

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

                                                <input type="hidden" id="codigo_alfa"
                                                    <?php echo "value='".$codigo_alfa."'";?>>

                                                <input type="hidden" id="codigo_numerico"
                                                    <?php echo "value='".$codigo_numerico."'";?>>

                                            <div class="row" style="padding-top: 10px;">
                                                <div class="col-md-8" style="margin-bottom: 10px; font-size: 16px;">
                                                    <?php
                                                    if ($tipo_rel=='I') :
                                                    ?>
                                                    <label class="label_situacao_reprodutiva_rel">Fazenda:&nbsp;</label>
                                                    <span> <?php echo $descricao_filtro;?></span>
                                                
                                                    <?php
                                                    else :
                                                    ?>

                                                    <label class="label_consulta_rel_rel">Filtros:</label>
                                                    <span><?php echo $descricao_filtro;?></span>
                                                    
                                                    <?php
                                                    endif;
                                                    ?>

                                                </div>

                                                <div class="col-md-4">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="lista_animais_excel()">Excel</button>
                                                </div>
                                            </div>

<?php
    if ($tipo_rel=='I') :
?>
        <div class="row">

        <?php
            if ($num_rows_animais==0) :
                mysqli_close($conector);
        ?>
        <div class="col-md-4" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">
                Fêmea: <?php echo $codigo_consulta . $mensagem;?>
            </<label>
        </div>

        </div> <!-- fecha a div row -->

        <?php
            else :
        ?>

        <div class="col-md-2" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">Fêmea:&nbsp;</label>
            <span> <?php echo $codigo_consulta . ' - ' . $situacao;?>
            </span>
        </div>

        <div class="col-md-4" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">Nascimento:&nbsp;</label>
            <span><?php echo $data_nascimento_edi;?></span>

            <label class="label_situacao_reprodutiva_rel">&nbsp;Idade:&nbsp;</label>
            <span><?php echo $idade_animal;?></span>
        </div>

        <div class="col-md-2" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">Animal Ativo:&nbsp;</label>

            <?php
            if ($ativo == 'Sim') :
            ?>

            <span style="color: green;"><?php echo $ativo;?></span>

            <?php
            else :
            ?>

            <span style="color: red;"><?php echo $ativo;?></span>

            <?php
            endif;
            ?>

        </div>

        <?php
            if ($ativo == 'Não') :
        ?>
            <div class="col-md-2"style="font-size: 16px; ">
                <label class="label_situacao_reprodutiva_rel">Situação:&nbsp;</label>
                <span style="color: red;"><?php echo $animal_situacao;?></span>
            </div>

        <?php
            endif;
        ?>
    </div> <!-- fim div pading 15 -->

    <div class="row">
        <div class="col-md-2"></div>

        <div class="col-md-2" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">Pai:&nbsp;</label>
            <span><?php echo $descricao_pai;?></span>
        </div>

        <?php
            if ($descarte == 'S') :
        ?>

        <div class="col-md-2" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">Mãe:&nbsp;</label>
            <span><?php echo $descricao_mae;?></span>
        </div>

        <?php
            else:
        ?>

        <div class="col-md-2" style="font-size: 16px;  margin-bottom: 30px;">
            <label class="label_situacao_reprodutiva_rel">Mãe:&nbsp;</label>
            <span><?php echo $descricao_mae;?></span>
        </div>

        <?php
            endif;
        ?>
    </div>    

    <div class="row">
        <?php
            if ($descarte == 'S') :
        ?>
            <div class="col-md-2"></div>
            <div class="col-md-8" style="font-size: 16px; margin-bottom: 30px;">
                <label class="label_situacao_reprodutiva_rel">Descartado para reprodução:&nbsp;</label>
                <span style="color: red"><?php echo $descarte_por;?></span>
            </div>
        <?php
            endif;
        ?>
    </div>

    <div class="row">
        <div class="col-md-6" style="font-size: 16px;">
            <label class="label_situacao_reprodutiva_rel">Estações de Monta:&nbsp;</label>
            <span><?php echo $qtd_estacoes;?></span>

            <span>
                <a class='btn' href='#'><i class='icon_sort_down' style="font-size: 25px; font-weight: 700;" data-toggle='tooltip' data-placement='right' title='Listar Estações' onClick='listar_estacoes()'></i></a>
            </span>
        </div>
    </div>

    <!-- Continua com a tela se o registro existir -->

    <fieldset class="scheduler-border">
        <legend class="scheduler-border fonte-legend" style="color: #ccc">ESTAÇÃO DE MONTA</legend>

        <div class="row">
            <div class="col-md-3 col-sm-12">
                <label class="label_consulta_rel">Estação de Monta:&nbsp;</label>
                <span> <?php echo $estacao_monta;?>
                </span>
            </div>                                            

            <div class="col-md-3 col-sm-12">
                <label class="label_consulta_rel">N° de coberturas:&nbsp;</label>
                <span><?php echo $num_coberturas;?>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-sm-12">
                <div id="lista_cobertura" style="height: 80px; overflow-y: scroll;">
                    <?php
                    echo '<section class="panel">';
                    echo '<table class="table table-striped table-advance table-hover" id="tabela_cobertura">';
                                          
                    echo '<tbody>'; 
                          
                    $tbl_cobertura = mysqli_query($conector, "select * from tbl_item_cobertura
                        inner join tbl_cobertura
                                on tbl_ite_cobertura_numero_id = tbl_cobertura_id
                        inner join tbl_protocoloiatf
                                on tbl_protocoloiatf_id = tbl_cobertura_protocoloiatf   
                        where tbl_cobertura_codigo_estacao_monta='$id_estacao' and 
                              tbl_cobertura_controle='C' and 
                              tbl_ite_cobertura_codigo_id_animal = '$codigo_animal_id' and 
                              tbl_ite_cobertura_numero_cobertura !=0
                        order by tbl_ite_cobertura_numero_cobertura DESC");

                    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
                    $numero_cobertura = $num_rows_cobertura;

                    if ($num_rows_cobertura!=0) {
                        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){

                            $cobertura = $reg_cobertura->tbl_cobertura_id;
                            $protocolo = $reg_cobertura->tbl_cobertura_protocoloiatf;

                            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                                WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura'");
                            $num_rows_protocolo = mysqli_num_rows($sql);
                            $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                            $tbl_item_iatf = mysqli_query($conector, "select * from tbl_item_protocoloiatf where tbl_ite_protocoloiatf_protocolo_id='$protocolo'
                                order by tbl_ite_protocoloiatf_id ASC");
                            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);

                            $tem_inseminacao = '';

                            while ($reg_itens_iatf = mysqli_fetch_object($tbl_item_iatf)) {
                                $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                                $data = date("d/m/Y", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                                $data_inseminacao = date("Y-m-d", strtotime(str_replace('/', '-', $data)));
                            }

                            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                            $tem_d0 = $reg_cobertura->tbl_ite_cobertura_dia_1;

                            if ($qtd_item_iatf==2) {
                                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_2;
                            }

                            if ($qtd_item_iatf==3) {
                                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_3;
                            }

                            if ($qtd_item_iatf==4) {
                                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_4;
                            }

                            if ($qtd_item_iatf==5) {
                                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_5;
                            }

                            if ($qtd_item_iatf==6) {
                                $tem_inseminacao = $reg_cobertura->tbl_ite_cobertura_dia_6;
                            }

                            if ($tem_inseminacao=='S' && $diagnostico!='P' && $diagnostico!='N') {
                                $desc_diagnostico = 'Aguardando Diagnostico';
                            }
                            else if ($diagnostico=='P') {
                                $desc_diagnostico = 'Diagnostico Positivo';
                            }
                            else if ($diagnostico=='N'){
                                $desc_diagnostico = 'Diagnostico Negativo';
                            }
                            else {
                                $desc_diagnostico = 'Aguardando Inseminação';
                            } 

                            $id_touro_semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                            $tbl_semen = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$id_touro_semen'");
                            $num_rows = mysqli_num_rows($tbl_semen);

                            if ($num_rows!=0) {
                                $reg_touro_semen = mysqli_fetch_object($tbl_semen);

                                if ($reg_touro_semen->tbl_semem_nome!='') {
                                    $desc_touro_semen = $reg_touro_semen->tbl_semem_codigo_alfa .'-'.
                                                        $reg_touro_semen->tbl_semem_nome;
                                }
                                else {
                                    $desc_touro_semen = $reg_touro_semen->tbl_semem_codigo_alfa;
                                }
                            }
                            else {
                                $tbl_touro = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$id_touro_semen'");
                                $num_rows = mysqli_num_rows($tbl_touro);

                                if ($num_rows!=0) {
                                    $reg_touro_semen = mysqli_fetch_object($tbl_touro);

                                    if ($reg_touro_semen->tbl_animal_codigo_alfa!='') {
                                        $desc_touro_semen = $reg_touro_semen->tbl_animal_codigo_alfa .'-'.
                                                            $reg_touro_semen->tbl_animal_codigo_numerico;
                                    }
                                    else {
                                        $desc_touro_semen = $reg_touro_semen->tbl_animal_codigo_numerico;
                                    }
                                }
                                else {
                                    $desc_touro_semen = '';
                                }
                            }

                            if ($tem_d0=='S') {
                                echo "<tr>";
                                echo "<td class='numero_cobertura' width='8%'>".$numero_cobertura."</td>";
                                echo "<td width='10%'>".$data."</td>";
                                echo "<td width='35%'>".$desc_touro_semen."</td>";
                                echo "<td width='35%'>".$desc_diagnostico."</td>";
                                echo "<td width='12%'></td>";
                                echo "</tr>";
                            }

                            $numero_cobertura--;
                        } 
                    }

                    $tbl_cobertura = mysqli_query($conector, "select * from tbl_historico_monta_natural
                                    where tbl_historico_monta_codigo_id_mae = '$codigo_animal_id'
                                    order by tbl_historico_monta_data_diagnostico DESC");  
                    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
                    $numero_cobertura = $num_rows_cobertura;

                    if ($num_rows_cobertura!=0) {
                        while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){

                            $desc_diagnostico = 'Diagnostico Positivo';
                            $desc_touro_semen = 'Monta Natural';
                            $data = date("d/m/Y", strtotime($reg_cobertura->tbl_historico_monta_data_diagnostico));

                            echo "<tr>";
                            echo "<td width='8%'></td>";
                            echo "<td width='10%'>".$data."</td>";
                            echo "<td width='35%'>".$desc_touro_semen."</td>";
                            echo "<td width='35%'>".$desc_diagnostico."</td>";
                            echo "<td width='12%'></td>";
                            echo "</tr>";
                        } 
                    }

                    echo '</tbody>';
                    echo '</table>';

                    echo '</section>';
                    ?>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset class="scheduler-border">
        <legend class="scheduler-border fonte-legend" style="color: #ccc">HISTÓRICOS</legend>
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <label class="label_consulta_rel">N° de partos:&nbsp;</label>
                <span><?php echo $num_partos;?>
                </span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 col-sm-12">
                <div id="lista_partos" style="height: 80px; overflow-y: scroll;">
                    <?php
                    echo '<section class="panel">';
                    echo '<table class="table table-hover" id="tabela_partos">';
                                          
                    echo '<tbody>';

                    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                              tbl_mov_estoque_codigo_id_animal=999999999 and 
                              tbl_mov_estoque_entrada_saida='E' and 
                              tbl_mov_estoque_tipo_movimentacao='N'");

                    $num_natimorto = mysqli_num_rows($tbl_natimorto);

                    if ($num_natimorto!=0) {
                        while ($reg_natimorto = mysqli_fetch_object($tbl_natimorto)) {
                            $codigo_fazenda = $reg_natimorto->tbl_mov_estoque_local;
                            $codigo_edi = 'Natimorto';
                            $data = new DateTime($reg_natimorto->tbl_mov_estoque_nascimento); 
                            $data_edi = $data->format('d/m/Y');
                            $data_nascimento = $reg_natimorto->tbl_mov_estoque_nascimento; 
                            $sexo = $reg_natimorto->tbl_mov_estoque_sexo; 

                            if ($sexo == 'N') {
                                $sexo = 'Não identificado';
                            }

                            $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
                                where tbl_pessoa_id='$codigo_fazenda'");
                            $num_rows = mysqli_num_rows($tab_origem);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_origem);
                                $desc_fazenda = $reg->tbl_pessoa_nome;
                            }
                            else {
                                $desc_fazenda = '';
                            }

                            $desc_categoria = '';
                            $descricao_pai = '';

                            echo "<tr>";
                            echo "<td width='8%' hidden>".$data_nascimento."</td>";
                            echo "<td width='8%'>".$codigo_edi."</td>";
                            echo "<td width='8%'>".$data_edi."</td>";
                            echo "<td width='20%'>".$desc_categoria."</td>";
                            echo "<td width='5%'>".$sexo."</td>";
                            echo "<td width='15%'>".$descricao_pai."</td>";
                            echo "<td width='44%'>".$desc_fazenda."</td>";
                            echo "</tr>";
                        }
                    }
                          
                    $sql = "select * from tbl_animais 
                               inner join tabela_racas
                                       on tab_codigo_raca = tbl_animal_codigo_raca   
                                    where tbl_animal_codigo_mae='$codigo_animal_id'
                                    order by tbl_animal_data_nascimento DESC"; 
                    $rs = mysqli_query($conector, $sql); 

                    while ($reg_animal = mysqli_fetch_object($rs)){
                        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                        $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                        $data = new DateTime($reg_animal->tbl_animal_data_nascimento); 
                        $data_edi = $data->format('d/m/Y');
                        $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
                        $sexo = $reg_animal->tbl_animal_sexo; 
                        $raca = $reg_animal->tab_descricao_raca;
                        $ativo = $reg_animal->tbl_animal_ativo;;
                        $pai = $reg_animal->tbl_animal_codigo_pai; 
                        $estacao_nascido = $reg_animal->tbl_animal_estacao_monta_nascimento; 

                        if ($codigo_alfa=='') {
                            $codigo_edi = intval($codigo_numerico);
                        }
                        else {
                            $codigo_edi = $codigo_alfa . '-' . intval($codigo_numerico);
                        }

                        $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
                            where tbl_pessoa_id='$codigo_fazenda'");
                        $num_rows = mysqli_num_rows($tab_origem);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tab_origem);
                            $desc_fazenda = $reg->tbl_pessoa_nome;
                        }
                        else {
                            $desc_fazenda = '';
                        }

                        $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                        $num_rows_pai = mysqli_num_rows($tab_pai);

                        if ($num_rows_pai!=0){
                            $reg = mysqli_fetch_object($tab_pai);
                            $descricao_pai = $reg->tbl_semem_codigo_alfa;
                        }
                        else {
                            $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                            $num_rows_pai = mysqli_num_rows($tab_pai);

                            if ($num_rows_pai!=0){
                                $reg = mysqli_fetch_object($tab_pai);
                                $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_pai = '';
                            }
                        }

                        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                        $data_acompanhamento_calculo = date("Y-m-d");
                        $date = new DateTime($data_nascimento); // Data de Nascimento
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                            WHERE tab_registro_lixeira_categoria_idade='0'");
                        $num_rows = mysqli_num_rows($categoria);    

                        if ($num_rows!=0) {
                            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                                $idade_de = $reg_categoria->tab_categoria_idade_de;
                                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                                if ($idade >= $idade_de && $idade <= $idade_ate) {
                                    if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                                        $desc_categoria=' > 36 meses';
                                    }
                                    else {
                                        $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                                    }
                                }
                            }
                        }                   

                        $estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
                            WHERE tbl_par_estacao_id='$estacao_nascido'");
                        $num_rows = mysqli_num_rows($estacao);    

                        if ($num_rows!=0) {
                            $reg_estacao = mysqli_fetch_object($estacao);
                            $desc_estacao = $reg_estacao->tbl_par_estacao_nome;
                        }
                        else {
                            $desc_estacao = '';
                        }

                        echo "<tr>";
                        if ($ativo=='S') {
                            echo "<td width='8%' hidden>".$data_nascimento."</td>";
                            echo "<td width='8%'>".$codigo_edi."</td>";
                            echo "<td width='8%'>".$data_edi."</td>";
                            echo "<td width='20%'>".$desc_categoria."</td>";
                            echo "<td width='5%'>".$sexo."</td>";
                            echo "<td width='10%'>".$descricao_pai."</td>";
                            echo "<td width='34%'>".$desc_fazenda."</td>";
                            echo "<td width='15%'>".$desc_estacao."</td>";
                        }
                        else {
                            echo "<td width='8%' hidden>".$data_nascimento."</td>";
                            echo "<td width='8%' style='color: red;'>".$codigo_edi."</td>";
                            echo "<td width='8%' style='color: red;'>".$data_edi."</td>";
                            echo "<td width='20%' style='color: red;'>".$desc_categoria."</td>";
                            echo "<td width='5%' style='color: red;'>".$sexo."</td>";
                            echo "<td width='10%' style='color: red;'>".$descricao_pai."</td>";
                            echo "<td width='34%' style='color: red;'>".$desc_fazenda."</td>";
                            echo "<td width='15%' style='color: red;'>".$desc_estacao."</td>";
                        }
                        echo "</tr>";
                    } 

                    echo '</tbody>';

                    echo ' <tr>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        </tr>
                        </thead>';

                    echo '</table>';

                    echo '</section>';

                    ?>
                </div>
            </div>
        </div> 

        <div class="row">&nbsp;</div>

        <div class="row">   
            <div class="col-md-3 col-sm-12">
                <label class="label_consulta_rel">N° de abortos:&nbsp;</label>
                <span><?php echo $num_abortos;?>
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-sm-12">
                <div id="lista_partos" style="height: 80px; overflow-y: scroll;">

                <?php
                    echo '<section class="panel">';
                    echo '<table class="table table-hover" id="tabela_abortos">';
                                          
                    echo '<tbody>';

                    $tbl_natimorto = mysqli_query($conector, "select * from tbl_movimentacao_estoque 
                        where tbl_mov_estoque_codigo_mae='$codigo_animal_id' and 
                              tbl_mov_estoque_codigo_id_animal=999999999 and 
                              tbl_mov_estoque_entrada_saida='A'");

                    $num_natimorto = mysqli_num_rows($tbl_natimorto);

                    if ($num_natimorto!=0) {
                        while ($reg_natimorto = mysqli_fetch_object($tbl_natimorto)) {
                            $codigo_fazenda = $reg_natimorto->tbl_mov_estoque_local;
                            $data = new DateTime($reg_natimorto->tbl_mov_estoque_nascimento); 
                            $data_edi = $data->format('d/m/Y');
                            $data_nascimento = $reg_natimorto->tbl_mov_estoque_nascimento; 
                            $sexo = $reg_natimorto->tbl_mov_estoque_sexo; 
                            $tipo_ocorrencia = $reg_natimorto->tbl_mov_estoque_tipo_movimentacao; 

                            if ($sexo == 'N') {
                                $sexo = 'Sexo não identificado';
                            }

                            if ($tipo_ocorrencia=='A') {
                                $codigo_edi = 'Aborto';
                            }
                            else {
                                $codigo_edi = 'Absorção';
                            }

                            $tab_origem = mysqli_query($conector, "select * from tbl_pessoa 
                                where tbl_pessoa_id='$codigo_fazenda'");
                            $num_rows = mysqli_num_rows($tab_origem);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_origem);
                                $desc_fazenda = $reg->tbl_pessoa_nome;
                            }
                            else {
                                $desc_fazenda = '';
                            }

                            $desc_categoria = '';
                            $descricao_pai = '';

                            echo "<tr>";
                            echo "<td width='8%' hidden>".$data_nascimento."</td>";
                            echo "<td width='8%'>".$codigo_edi."</td>";
                            echo "<td width='8%'>".$data_edi."</td>";
                            echo "<td width='20%'>".$desc_categoria."</td>";
                            echo "<td width='5%'>".$sexo."</td>";
                            echo "<td width='15%'>".$descricao_pai."</td>";
                            echo "<td width='44%'>".$desc_fazenda."</td>";
                            echo "</tr>";
                        }
                    }

                    echo '</tbody>';

                    echo ' <tr>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        <th hidden></th>
                        </tr>
                        </thead>';

                    echo '</table>';
                    echo '</section>';
                ?>
                </div>
            </div>
        </div>
    </fieldset>

    <!-- fim da tela se tipo_rel for I -->
    <?php
        endif;
    ?>

<!-- Se tipo_rel não for I -->
<?php
    else :
?>

<table class="table table-bordered table-striped table-advance table-hover" id="tabela_situacao_reprodutiva" width="100%" style="font-size: 8px;">

<tbody>

<?php
/*    $sql = "SELECT * from tbl_animais 
        WHERE tbl_animal_codigo_id=175 or 
              tbl_animal_codigo_id=19 or 
              tbl_animal_codigo_id=120"; 
*/

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

            $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
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

            // verifica numero de coberturas na estacao
            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
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
                $descricao_pai = $reg->tbl_semem_codigo_alfa;
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
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
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

/*
            // VERIFICA SE ANIMAL TEM PARTO A MENOS DE 35 DIAS
            $data_nasc_bezerro = '0000-00-00';
            $animal_tem_parto = '';

            $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo'
                ORDER BY tbl_animal_codigo_numerico DESC LIMIT 1"); 

            $numero_rows_partos = mysqli_num_rows($tbl_filhos);

            if ($numero_rows_partos!=0) {
                $reg_parto = mysqli_fetch_object($tbl_filhos);
                $codigo_bezerro = $reg_parto->tbl_animal_codigo_numerico;
                $data_nasc_bezerro=$reg_parto->tbl_animal_data_nascimento;

                $bezerro_ativo = $reg_parto->tbl_animal_ativo;
                $bezerro_situacao = $reg_parto->tbl_animal_situacao;

                $data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));

                if ($bezerro_situacao=='M') {
                    $data_morte = substr($reg_parto->tbl_animal_baixado_em, 0, 10) ;
                }
                else {
                    $data_morte = '0000-00-00';
                }

                $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
                $dias_parto = floor($diferenca / (60 * 60 * 24));

                $animal_tem_parto = 'S';
            }
            else {
                $animal_tem_parto = 'N';
            }

            // VERIFICA TAMBEM SE TEVE NATIMORTO A MENOS 35 DIAS
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      tbl_mov_estoque_entrada_saida='S' AND 
                      tbl_mov_estoque_tipo_movimentacao='M' 
                ORDER BY tbl_mov_estoque_nascimento DESC LIMIT 1");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if ($num_natimorto!=0) {
                $reg_natmorto = mysqli_fetch_object($tbl_natimorto);

                if ($reg_natmorto->tbl_mov_estoque_nascimento>$data_nasc_bezerro) {
                    $data_nasc_bezerro=$reg_natmorto->tbl_mov_estoque_nascimento;

                    $bezerro_situacao = 'M';
                    $data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));
                    $data_morte = $data_nasc_bezerro;

                    $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
                    $dias_parto = floor($diferenca / (60 * 60 * 24));

                    $animal_tem_parto = 'S';
                }
            }

            // VERIFICAR FILTROS DE VACAS SOLTEIRAS

            $vaca_solteira='';
            $vaca_parida='';
            
            if ($filtro_solteiras=='S') {
                if ($animal_tem_parto=='S') { // alternativas 1, 2, 3, 4, 5, 6
                    $date = new DateTime($data_nasc_bezerro); // Data de Nascimento do bezzero
                    $idade_acompanhamento = $date->diff(new DateTime($data_hoje));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade_bezerro>=8) { // alternativas 1, 2, 3, 4

                        if ($bezerro_situacao!='M') { // 1, 2, bezerro vivo

                            $aborto = VerAborto($conector, $codigo, $data_hoje);
                            
                            if ($aborto[0]=='N') { // alternativa 1
                                $vaca_solteira='S';
                            }
                            else { // alternativa 2
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                        }
                        else { // 3, 4 bezzero não está vivo
                            $aborto = VerAborto($conector, $codigo, $data_hoje);

                            if ($aborto[0]=='S') { // alternativa 3
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                            else { // alternativa 4
                                $vaca_solteira='S';
                            }
                        }
                    }
                    else { // alternativas 5, 6

                        if ($bezerro_situacao=='M') { // Bezerro vivo não
                            $aborto = VerAborto($conector, $codigo, $data_hoje);

                            if ($aborto[0]=='S') { // alternativa 5
                                $data_aborto = $aborto[2];
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_aborto<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                            else { // alternativa 6
                                $data_ref = CalcularDataRef($data_hoje);

                                if ($data_morte<=$data_ref) {
                                    $vaca_solteira='S';
                                }
                            }
                        }
                    }
                }
                else { // alternativa 7
                    $aborto = VerAborto($conector, $codigo, $data_hoje);

                    if ($aborto[0]=='S') {
                        $data_aborto = $aborto[2];
    
                        $data_ref = CalcularDataRef($data_hoje);

                        if ($data_aborto<=$data_ref) {
                            $vaca_solteira='S';
                        }
                    }

                    $natimorto = VerNatimorto($conector, $codigo, $data_hoje);

                    if ($natimorto[0]=='S') {
                        $data_natimorto = $natimorto[2];
    
                        $data_ref = CalcularDataRef($data_hoje);

                        if ($data_natimorto<=$data_ref) {
                            $vaca_solteira='S';
                        }
                    }
                    else {
                       $data_natimorto='0000-00-00'; 
                    }
                }

                // VERIFICA SE A VACA ESTA PRENHE

                $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_cobertura
                    INNER JOIN tbl_item_cobertura 
                            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
                    WHERE tbl_ite_cobertura_codigo_id_animal='$codigo' AND  
                          tbl_ite_cobertura_resultado_diagnostico='P' AND  
                          (tbl_ite_cobertura_nascido='' OR 
                           tbl_ite_cobertura_nascido IS NULL)");

                $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

                if ($num_rows_prenhe!=0) {
                    $vaca_solteira = '';
                }

            }
*/






            // verifica vacas solteiras
            if ($filtro_solteiras=='S' || $filtro_paridas=='S') {

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento; 
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = $reg->tbl_semem_codigo_alfa;
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
                    $codigo_edi_filho = '';
                    $vaca_solteira = 'S';
                    $vaca_parida = '';
                    $descricao_pai_ult_filho = '';
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
                    WHERE tbl_ite_cobertura_codigo_id_animal='$codigo' AND  
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

                $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_mae='$codigo'
                    ORDER BY tbl_animal_data_nascimento DESC limit 1");

                $ultimo_filho = mysqli_num_rows($tbl_filhos);

                if ($ultimo_filho!=0) {
                    $reg_filhos = mysqli_fetch_object($tbl_filhos);
                    $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;
                    $codigo_alfa_filho = $reg_filhos->tbl_animal_codigo_alfa;
                    $codigo_numerico_filho = $reg_filhos->tbl_animal_codigo_numerico;

                    if ($codigo_alfa_filho=='') {
                        $codigo_edi_filho = intval($codigo_numerico_filho);
                    }
                    else {
                        $codigo_edi_filho = $codigo_alfa_filho.'-'.intval($codigo_numerico_filho);
                    }

                    $pai_ult_filho = $reg_filhos->tbl_animal_codigo_pai;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai_ult_filho'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_pai_ult_filho = $reg->tbl_semem_codigo_alfa;
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

            if ($data_natimorto>$ultimo_parto) {
                $data = new DateTime($data_natimorto);
                $ultimo_parto_edi = $data->format('d/m/Y');
                $natimorto = 'S';
            }
            else {
                $natimorto = 'N';
            }

            // Verifica previsão de parto
            if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
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

                if ($data_previsao_parto=='0000-00-00') {
                    $previsao_parto_edi = '';
                }
                else {
                    $data = new DateTime($data_previsao_parto);
                    $previsao_parto_edi = $data->format('d/m/Y');
                }
            }
            else {
                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
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

                        $data_previsao_servico = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                    }
                }
                else {
                    $data_previsao_servico = '0000-00-00';
                }

                if ($data_previsao_servico=='0000-00-00') {
                    $previsao_parto_edi = '';
                }
                else {
                    $data = new DateTime($data_previsao_servico);
                    $previsao_parto_edi = $data->format('d/m/Y');
                }
            }

            // calcula data da aptidão

            $data_aptidao_edi = '';
            
            if ($ultimo_parto!='0000-00-00') {
                $data_aptidao_edi = date("d/m/Y", strtotime($ultimo_parto . "+ 35 days"));
            }

            if ($data_aborto_natimorto!='0000-00-00' && $data_aborto_natimorto>$ultimo_parto) {
                $data_aptidao_edi = date("d/m/Y", strtotime($data_aborto_natimorto . "+ 35 days"));
            }

            // Verifica diagnostico
            if ($filtro_positivo=='S' || $filtro_negativo=='S'){
                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    INNER JOIN tbl_cobertura
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
                    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_codigo_alfa;
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
                    WHERE tbl_ite_cobertura_codigo_id_animal = '$codigo' AND 
                          tbl_cobertura_controle = 'C' AND 
                          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
                    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

                $num_rows = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows!=0) {
                    $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                    $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
                    $semen = $reg_cobertura->tbl_ite_cobertura_codigo_touro_semen;

                    $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$semen'");
                    $num_rows = mysqli_num_rows($tab_pai);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tab_pai);
                        $descricao_semen = $reg->tbl_semem_codigo_alfa;
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

            /*print_r('Filtro Solteira: ' . $filtro_solteiras . ' Vaca é solteira: ' . $vaca_solteira . ' - ' . $codigo_edi . '</br>');

            print_r('Filtro Parida: ' . $filtro_paridas . ' Vaca é parida: ' . $vaca_parida . ' - ' . $codigo_edi . '</br>');

            print_r('Filtro Positivo: ' . $filtro_positivo . ' Vaca é positiva: ' . $tem_positivo . ' - ' . $codigo_edi . ' Estacao: ' . $estacao_monta . '</br>');*/

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
                            echo '<td width="6%">'.$ultimo_parto_edi.'&nbsp;&nbsp;<i class="icon_info_alt" data-toggle="tooltip" data-placement="right" title="Considerado aqui a data do Natimorto." style="color: red;"></i></td>';
                        }  
                        else {
                            echo '<td width="6%">'.$ultimo_parto_edi.'</td>';  
                        }

                        echo '<td width="6%">'.$codigo_edi_filho.'</td>';    
                        echo '<td width="6%">'.$descricao_pai_ult_filho.'</td>';
                        echo '<td width="6%">'.$estacao_monta.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$num_coberturas.'</td>';    
                        echo '<td width="3%"  style="text-align: center;">'.$diagnostico.'</td>';    
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
                            echo '<td width="6%">'.$ultimo_parto_edi.'&nbsp;&nbsp;<i class="icon_info_alt" data-toggle="tooltip" data-placement="right" title="Considerado aqui a data do Natimorto." style="color: red;"></i></td>';
                        }  
                        else {
                            echo '<td width="6%">'.$ultimo_parto_edi.'</td>';  
                        }

                        echo '<td width="6%">'.$codigo_edi_filho.'</td>';    
                        echo '<td width="6%">'.$descricao_pai_ult_filho.'</td>';
                        echo '<td width="6%">'.$estacao_monta.'</td>';    
                        echo '<td width="3%" style="text-align: center;">'.$num_coberturas.'</td>';    
                        echo '<td width="3%"  style="text-align: center;">'.$diagnostico.'</td>';    
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
        <th colspan="4" style="vertical-align: middle;text-align:center;">Total de Fêmeas</th>
        <th colspan="2" style="vertical-align: middle;text-align:center;"><?php echo $animais_listados?></th>
        <th colspan="6" style="vertical-align: middle;text-align:center;">Atual</th>
        <th colspan="7" style="vertical-align: middle;text-align:center;">Estação de Monta</th>
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

<!-- fim da tela se tipo_rel for G -->
<?php
    endif;
?>
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




