<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $local_filtro = $_REQUEST["local"];
    $estacao_filtro = $_REQUEST["estacao_monta"];
    $tipo_cobertura = $_REQUEST["tipo_cobertura"];
    $periodo_de = $_REQUEST["periodo_de"];
    $periodo_ate = $_REQUEST["periodo_ate"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $local= array();
    $matriz_itens = explode(",", $local_filtro);
    $quantidade_fazendas = count($matriz_itens);

    for($i=0; $i < $quantidade_fazendas; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal = '';

    if ($local_filtro!='') {
        $wlocal = " AND tbl_cobertura_codigo_local IN(";
        $wlocal.= $local;
        $wlocal.= ")";
    }

    if ($tipo_cobertura=='I') {
        $estacao= array();
        $matriz_itens = explode(",", $estacao_filtro);
        $quantidade_estacoes = count($matriz_itens);

        for($i=0; $i < $quantidade_estacoes; $i++) {
            $estacao[$i]=$matriz_itens[$i];
        }

        $estacao = implode(',', $estacao);
        $estacao = substr($estacao,0, -1);

        $westacao = '';

        if ($estacao_filtro!='') {
            $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
            $westacao.= $estacao;
            $westacao.= ")";
        }
    }

    /*$tipo_cobertura = $_REQUEST['tipo'];
    $tipo_cobertura = explode(',', $tipo_cobertura);

    $_SESSION['tipo_monta']='';
    $_SESSION['tipo_iatf']='';
    $_SESSION['tipo_te']='';

    for ($i=0; $i < count($tipo_cobertura) ; $i++) { 
        if ($tipo_cobertura[$i]=='M'){
            $_SESSION['tipo_monta']='M';
        }
        else if ($tipo_cobertura[$i]=='I'){
            $_SESSION['tipo_iatf']='I';
        }
        else if ($tipo_cobertura[$i]=='T'){
            $_SESSION['tipo_te']='T';
        }
    }*/
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

    <style type="text/css">
        #dados_cliente { margin-left: 20px;
        margin-right: 20px; }
    </style>

</head>

<body>
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
            include "limpar_secao_movimentacao.php"; 
            include "limpar_secao_nutricao.php"; 
            include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Reprodutivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Índices Reprodutivos</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-chart-line"></i> Índices Reprodutivos</h3>
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
                                            <div class="" id="dados_cliente">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="codigo_estacao_monta"
                                                    <?php echo "value='".$estacao_filtro."'";?>>

                                                <input type="hidden" id="codigo_local"
                                                    <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                                <!--<input type="hidden" id="tipo_cobertura"
                                                    <?php //echo "value='".$_REQUEST['tipo']."'";?>>-->

                                                <input type="hidden" id="tipo_cobertura"
                                                    <?php echo "value='".$tipo_cobertura."'";?>>

                                                <input type="hidden" id="periodo_de"
                                                    <?php echo "value='".$periodo_de."'";?>>

                                                <input type="hidden" id="periodo_ate"
                                                    <?php echo "value='".$periodo_ate."'";?>>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel_rel">Filtros:</label>
                                                    <span><?php echo $descricao_filtro;?></span>
                                                </div>

                                                <div class="col-md-4">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="lista_indice_excel()">Excel</button>
                                                </div>
                                            </div>

<table class="table table-bordered table-striped table-advance table-hover" id="tabela_itens_consulta" width="100%" style="font-size: 11px; border: none;">

<tbody>

<?php
    if ($tipo_cobertura=='I') {
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_cobertura_codigo_local
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_controle = 'C' AND 
                  tbl_cobertura_encerrada='S'" . $wlocal . $westacao . 
            "ORDER BY tbl_cobertura_codigo_estacao_monta ASC, 
                      tbl_ite_cobertura_codigo_id_animal ASC"); 
    }
    else {
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
            INNER JOIN tbl_pessoa
                    ON tbl_pessoa_id = tbl_cobertura_codigo_local
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_controle = 'M' AND 
                  tbl_ite_cobertura_data_prenhes>='$periodo_de' AND
                  tbl_ite_cobertura_data_prenhes<='$periodo_ate' AND 
                  tbl_cobertura_encerrada='S'" . $wlocal . 
            "ORDER BY tbl_cobertura_codigo_local ASC,
                      tbl_ite_cobertura_codigo_id_animal ASC"); 
    }

    $num_rows = mysqli_num_rows($tbl_item_cobertura);

    $estacao_anterior = 0;
    $local_anterior = 0;
    $animal_anterior = 0;
    $qtd_femeas = 0;
    $qtd_coberturas = 0;
    $qtd_positivos = 0;
    $qtd_nascidos = 0;
    $qtd_aborto = 0;
    $qtd_natimorto = 0;
    $qtd_desmame = 0;
    $indice = 0;
    $array_total_coberturas = array();
    $array_total_femeas = array();
    $array_total_eficiencia = array();
    $array_total_positivos = array();
    $array_total_nascidos = array();
    $array_total_aborto_natimorto = array();

    $total_coberturas = 0;
    $total_femeas = 0;
    $total_positivos = 0;
    $total_nascidos = 0;
    $total_abortos = 0;
    $total_natimorto = 0;
    $total_desmame = 0;

    $sub_categoria=array();
    $sub_qtd_femeas=array();
    $sub_qtd_coberturas=array();
    $sub_qtd_positivos=array();
    $sub_qtd_nascidos=array();
    $sub_qtd_aborto=array();
    $sub_qtd_natimorto=array();
    $sub_qtd_desmame=array();
    $sub_eficiencia_servico=array();
    $sub_taxa_prenhez=array();
    $sub_taxa_natalidade=array();
    $sub_perda_gestacao=array();
    $sub_taxa_desmame=array();

    for ($i=0; $i < 3; $i++) { 
        $sub_categoria[$i]='';
        $sub_qtd_femeas[$i]=0;
        $sub_qtd_coberturas[$i]=0;
        $sub_qtd_positivos[$i]=0;
        $sub_qtd_nascidos[$i]=0;
        $sub_qtd_aborto[$i]=0;
        $sub_qtd_natimorto[$i]=0;
        $sub_qtd_desmame[$i]=0;
        $sub_eficiencia_servico[$i]=0;
        $sub_taxa_prenhez[$i]=0;
        $sub_taxa_natalidade[$i]=0;
        $sub_perda_gestacao[$i]=0;
        $sub_taxa_desmame[$i]=0;
    }

    if ($num_rows!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
            $codigo_id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
            $codigo_numerico_animal = $reg_cobertura->tbl_ite_cobertura_codigo_numerico;
            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
            $codigo_estacao = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $nascidos = $reg_cobertura->tbl_ite_cobertura_nascido;
            $cobertura_id = $reg_cobertura->tbl_ite_cobertura_numero_id;
            $item_cobertura = $reg_cobertura->tbl_ite_cobertura_numero_item;
            $data_prenhes = $reg_cobertura->tbl_ite_cobertura_data_prenhes;
            // Verifica numero de partos
            $tbl_animais = mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_codigo_mae = '$codigo_id_animal'");  

            $num_rows = mysqli_num_rows($tbl_animais);

            if ($num_rows==0) {
                $sub_categoria[0]='Novilhas';
                $categoria_animal='N';
            }
            else if ($num_rows==1) {
                $sub_categoria[1]='Primiparas';
                $categoria_animal='P';
            }
            else {
                $sub_categoria[2]='Multiparas';
                $categoria_animal='M';
            }

            if ($tipo_cobertura=='I') { // IATF
                //if ($protocoloiatf_tipo == $tipo_cobertura[$i]) {

                    if ($codigo_estacao!=$estacao_anterior) {
                        if ($estacao_anterior==0){
                            $estacao_anterior=$codigo_estacao;
                            $desc_local = $reg_cobertura->tbl_pessoa_nome;

                            $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_parametro_estacao_monta
                                WHERE tbl_par_estacao_id = '$codigo_estacao'");  

                            $num_rows = mysqli_num_rows($sql);

                            if ($num_rows!=0) {
                                $reg_estacao = mysqli_fetch_object($sql);
                                $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                            }
                            else {
                                $desc_estacao_monta = '';
                            }
                        }
                        else {
                            for ($j=0; $j < 3; $j++) { 
                                $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];

                                $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                                $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                                $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                                $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                            }

                            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

                            $total_coberturas+= $qtd_coberturas;
                            $total_femeas+= $qtd_femeas;

                            $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;

                            echo '<tr style="font-weight: 700;">
                                <td>'.$desc_estacao_monta.'</td>
                                <td style="font-size: 10px;">'.$desc_local.'</td>
                                <td style="text-align: right;">'.$qtd_femeas.'</td>
                                <td style="text-align: right;">'.$qtd_coberturas.'</td>
                                <td style="text-align: right;">'.$qtd_positivos.'</td>
                                <td style="text-align: right;">'.$qtd_nascidos.'</td>
                                <td style="text-align: right;">'.$qtd_aborto.'</td>
                                <td style="text-align: right;">'.$qtd_natimorto.'</td>
                                <td style="text-align: right;">'.$qtd_desmame.'</td>
                                <td style="border: none;"></td>
                                <td style="text-align: right;">'.number_format($eficiencia_servico,2,',','.').'</td>
                                <td style="text-align: right;">'.number_format($taxa_prenhez,2,',','.').' %</td>
                                <td style="text-align: right;">'.number_format($taxa_natalidade,2,',','.').' %</td>
                                <td style="text-align: right;">'.number_format($perda_gestacao,2,',','.').' %</td>
                                <td style="text-align: right;">'.number_format($taxa_desmame,2,',','.').' %</td>
                            </tr>';

                            for ($i=0; $i < 3; $i++) { 
                                if ($sub_categoria[$i]!='') {
                                    echo '<tr style="color: #a5a7a8">
                                        <td></td>
                                        <td  style="font-size: 10px;text-align: right;">'.$sub_categoria[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_femeas[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_coberturas[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_positivos[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_nascidos[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_aborto[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_natimorto[$i].'</td>
                                        <td style="text-align: right;">'.$sub_qtd_desmame[$i].'</td>
                                        <td style="border: none;"></td>
                                        <td style="text-align: right;">'.number_format($sub_eficiencia_servico[$i],2,',','.').'</td>
                                        <td style="text-align: right;">'.number_format($sub_taxa_prenhez[$i],2,',','.').' %</td>
                                        <td style="text-align: right;">'.number_format($sub_taxa_natalidade[$i],2,',','.').' %</td>
                                        <td style="text-align: right;">'.number_format($sub_perda_gestacao[$i],2,',','.').' %</td>
                                        <td style="text-align: right;">'.number_format($sub_taxa_desmame[$i],2,',','.').' %</td>
                                    </tr>';
                                }
                            }

                            $estacao_anterior=$codigo_estacao;
                            $desc_local = $reg_cobertura->tbl_pessoa_nome;

                            $sql = mysqli_query($conector, "SELECT * FROM
                                tbl_parametro_estacao_monta
                                WHERE tbl_par_estacao_id = '$codigo_estacao'");  

                            $num_rows = mysqli_num_rows($sql);

                            if ($num_rows!=0) {
                                $reg_estacao = mysqli_fetch_object($sql);
                                $desc_estacao_monta = $reg_estacao->tbl_par_estacao_nome;
                            }
                            else {
                                $desc_estacao_monta = '';
                            }

                            $animal_anterior = 0;
                            $qtd_femeas = 0;
                            $qtd_coberturas = 0;
                            $qtd_positivos = 0;
                            $qtd_nascidos = 0;
                            $qtd_aborto = 0;
                            $qtd_natimorto = 0;
                            $qtd_desmame = 0;

                            for ($i=0; $i < 3; $i++) { 
                                $sub_qtd_femeas[$i]=0;
                                $sub_qtd_coberturas[$i]=0;
                                $sub_qtd_positivos[$i]=0;
                                $sub_qtd_nascidos[$i]=0;
                                $sub_qtd_aborto[$i]=0;
                                $sub_qtd_natimorto[$i]=0;
                                $sub_qtd_desmame[$i]=0;
                                $sub_eficiencia_servico[$i]=0;
                                $sub_taxa_prenhez[$i]=0;
                                $sub_taxa_natalidade[$i]=0;
                                $sub_perda_gestacao[$i]=0;
                                $sub_taxa_desmame[$i]=0;
                            }
                        }
                    }

                    $qtd_coberturas++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_coberturas[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_coberturas[1]++;
                    }   
                    else {
                        $sub_qtd_coberturas[2]++;
                    }                    

                    if ($codigo_id_animal!=$animal_anterior) {
                        $qtd_femeas++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_femeas[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_femeas[1]++;
                        }   
                        else {
                            $sub_qtd_femeas[2]++;
                        }                    

                        $animal_anterior=$codigo_id_animal;

                        // verifica desmama 
                        $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
                            INNER JOIN tbl_item_pesagem 
                                    ON tbl_ite_pesagem_codigo_id_animal=tbl_animal_codigo_id 
                            INNER JOIN tbl_pesagem
                                    ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                            WHERE tbl_animal_codigo_mae = '$codigo_id_animal' AND  tbl_pesagem_codigo_epoca = 2 AND 
                                  tbl_animal_estacao_monta_nascimento = '$codigo_estacao'");  

                        // verificar os animais que tiveram peso de desmama independente de estar ativo ou não. Não precisa considerar a idade 
                        $num_rows = mysqli_num_rows($sql);

                        if ($num_rows!=0) {
                            while ($reg_animal = mysqli_fetch_object($sql)) {
                                /*$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                                //VER AQUI QUANDO O ANIMAL FOI VENDIDO, MORTO OU OUTRA SAIDA PARA CALCULAR A IDADE. SO VALE PARA ANIMAIS DESMAMADOS <= DATA DA MOVIMENTACAO E TIVEREM > 7 MESES                          
                                $data_acompanhamento_calculo = date("Y-m-d");
                                $date = new DateTime($data_nascimento); // Data de Nascimento
                                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                                $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                                */
                                //if ($idade_animal>=7) {
                                    $qtd_desmame++;
                                    $total_desmame++;

                                    if ($categoria_animal=='N') {
                                        $sub_qtd_desmame[0]++;
                                    }
                                    else if ($categoria_animal=='P') {
                                        $sub_qtd_desmame[1]++;
                                    }   
                                    else {
                                        $sub_qtd_desmame[2]++;
                                    }                    
                                //}
                            }
                        }
                    }

                    if ($diagnostico == 'P') {
                        $qtd_positivos++;
                        $total_positivos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_positivos[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_positivos[1]++;
                        }   
                        else {
                            $sub_qtd_positivos[2]++;
                        }                    
                    }

                    if ($nascidos == 'N' and $diagnostico == 'P') {
                        $qtd_nascidos++;
                        $total_nascidos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_nascidos[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_nascidos[1]++;
                        }   
                        else {
                            $sub_qtd_nascidos[2]++;
                        }                    
                    }
                    else if ($nascidos == 'A') {
                        $qtd_aborto++;
                        $total_abortos++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_aborto[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_aborto[1]++;
                        }   
                        else {
                            $sub_qtd_aborto[2]++;
                        }                    
                    }
                    else if ($nascidos == 'M') {
                        $qtd_natimorto++;
                        $total_natimorto++;

                        if ($categoria_animal=='N') {
                            $sub_qtd_natimorto[0]++;
                        }
                        else if ($categoria_animal=='P') {
                            $sub_qtd_natimorto[1]++;
                        }   
                        else {
                            $sub_qtd_natimorto[2]++;
                        }                    
                    }

                    // VER O QUE FAZER QUANDO NASCIDOS FOR 'OUTRO' VENDA, MORTE, OURA SAIDA
                //}
            }
            else { // Monta
                if ($codigo_local!=$local_anterior) {
                    if ($local_anterior==0){
                        $local_anterior=$codigo_local;
                        $desc_local = $reg_cobertura->tbl_pessoa_nome;
                    }
                    else {
                        for ($j=0; $j < 3; $j++) { 
                            $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                            $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                            $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                            $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                            $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                        }

                        $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

                        $total_coberturas+= $qtd_coberturas;
                        $total_femeas+= $qtd_femeas;

                        $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
                        $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
                        $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
                        $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;

                        // $qtd_cobertura e $eficiencia_servico não será exibido para monta conforme o trello
                        // Cartão 'MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)'
                        // ChekList 'AJUSTE REUNIAO 20/05/2025'
                        // ChekList 'AJUSTE REUNIAO 09/06/2025'
                        echo '<tr style="font-weight: 700;">
                            <td>Monta</td>
                            <td style="font-size: 10px;">'.$desc_local.'</td>
                            <td style="text-align: right;">'.$qtd_femeas.'</td>
                            <td style="text-align: right;">'.$qtd_positivos.'</td>
                            <td style="text-align: right;">'.$qtd_nascidos.'</td>
                            <td style="text-align: right;">'.$qtd_aborto.'</td>
                            <td style="text-align: right;">'.$qtd_natimorto.'</td>
                            <td style="text-align: right;">'.$qtd_desmame.'</td>
                            <td style="border: none;"></td>
                            <td style="text-align: right;">'.number_format($taxa_prenhez,2,',','.').' %</td>
                            <td style="text-align: right;">'.number_format($taxa_natalidade,2,',','.').' %</td>
                            <td style="text-align: right;">'.number_format($perda_gestacao,2,',','.').' %</td>
                            <td style="text-align: right;">'.number_format($taxa_desmame,2,',','.').' %</td>
                            </tr>';

                        for ($i=0; $i < 3; $i++) { 
                            if ($sub_categoria[$i]!='') {
                                echo '<tr style="color: #a5a7a8">
                                    <td></td>
                                    <td  style="font-size: 10px;text-align: right;">'.$sub_categoria[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_femeas[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_positivos[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_nascidos[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_aborto[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_natimorto[$i].'</td>
                                    <td style="text-align: right;">'.$sub_qtd_desmame[$i].'</td>
                                    <td style="border: none;"></td>
                                    <td style="text-align: right;">'.number_format($sub_taxa_prenhez[$i],2,',','.').' %</td>
                                    <td style="text-align: right;">'.number_format($sub_taxa_natalidade[$i],2,',','.').' %</td>
                                    <td style="text-align: right;">'.number_format($sub_perda_gestacao[$i],2,',','.').' %</td>
                                    <td style="text-align: right;">'.number_format($sub_taxa_desmame[$i],2,',','.').' %</td>
                                </tr>';
                            }
                        }

                        $local_anterior=$codigo_local;
                        $desc_local = $reg_cobertura->tbl_pessoa_nome;

                        $animal_anterior = 0;
                        $qtd_femeas = 0;
                        $qtd_coberturas = 0;
                        $qtd_positivos = 0;
                        $qtd_nascidos = 0;
                        $qtd_aborto = 0;
                        $qtd_natimorto = 0;
                        $qtd_desmame = 0;

                        for ($i=0; $i < 3; $i++) { 
                            $sub_qtd_femeas[$i]=0;
                            $sub_qtd_coberturas[$i]=0;
                            $sub_qtd_positivos[$i]=0;
                            $sub_qtd_nascidos[$i]=0;
                            $sub_qtd_aborto[$i]=0;
                            $sub_qtd_natimorto[$i]=0;
                            $sub_qtd_desmame[$i]=0;
                            $sub_eficiencia_servico[$i]=0;
                            $sub_taxa_prenhez[$i]=0;
                            $sub_taxa_natalidade[$i]=0;
                            $sub_perda_gestacao[$i]=0;
                            $sub_taxa_desmame[$i]=0;
                        }
                    }
                }

                print_r('animal: ' . $codigo_numerico_animal . 'Data sistema: ' . $data_prenhes . '</br>');
                
                $qtd_coberturas++;

                if ($categoria_animal=='N') {
                    $sub_qtd_coberturas[0]++;
                }
                else if ($categoria_animal=='P') {
                    $sub_qtd_coberturas[1]++;
                }   
                else {
                    $sub_qtd_coberturas[2]++;
                }                   

                if ($codigo_id_animal!=$animal_anterior) {
                    $qtd_femeas++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_femeas[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_femeas[1]++;
                    }   
                    else {
                        $sub_qtd_femeas[2]++;
                    }                    

                    $animal_anterior=$codigo_id_animal;

                    // verifica desmama 
                    $sql = mysqli_query($conector, "SELECT * FROM tbl_animais
                        INNER JOIN tbl_item_pesagem 
                                ON tbl_ite_pesagem_codigo_id_animal=tbl_animal_codigo_id
                        INNER JOIN tbl_pesagem
                                ON tbl_pesagem_id = tbl_ite_pesagem_numero_id 
                        WHERE tbl_animal_codigo_mae = '$codigo_id_animal' AND  
                              tbl_pesagem_codigo_epoca = 2 AND 
                              tbl_animal_codigo_cobertura = '$cobertura_id'");

                    // verificar os animais que tiveram peso de desmama independente de estar ativo ou não. Não precisa considerar a idade 
                    $num_rows = mysqli_num_rows($sql);

                    if ($num_rows!=0) {
                        while ($reg_animal = mysqli_fetch_object($sql)) {
                            /*$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                            //VER AQUI QUANDO O ANIMAL FOI VENDIDO, MORTO OU OUTRA SAIDA PARA CALCULAR A IDADE. SO VALE PARA ANIMAIS DESMAMADOS <= DATA DA MOVIMENTACAO E TIVEREM > 7 MESES                          
                            $data_acompanhamento_calculo = date("Y-m-d");
                            $date = new DateTime($data_nascimento); // Data de Nascimento
                            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                            $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
                            */
                            //if ($idade_animal>=7) {
                            $qtd_desmame++;
                            $total_desmame++;

                            if ($categoria_animal=='N') {
                                $sub_qtd_desmame[0]++;
                            }
                            else if ($categoria_animal=='P') {
                                $sub_qtd_desmame[1]++;
                            }   
                            else {
                                $sub_qtd_desmame[2]++;
                            }                    
                            //}
                        }
                    }
                }

                if ($diagnostico == 'P') {
                    $qtd_positivos++;
                    $total_positivos++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_positivos[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_positivos[1]++;
                    }   
                    else {
                        $sub_qtd_positivos[2]++;
                    }                    
                }

                if ($nascidos == 'N' and $diagnostico == 'P') {
                    $qtd_nascidos++;
                    $total_nascidos++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_nascidos[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_nascidos[1]++;
                    }   
                    else {
                        $sub_qtd_nascidos[2]++;
                    }                    
                }
                else if ($nascidos == 'A') {
                    $qtd_aborto++;
                    $total_abortos++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_aborto[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_aborto[1]++;
                    }   
                    else {
                        $sub_qtd_aborto[2]++;
                    }                    
                }
                else if ($nascidos == 'M') {
                    $qtd_natimorto++;
                    $total_natimorto++;

                    if ($categoria_animal=='N') {
                        $sub_qtd_natimorto[0]++;
                    }
                    else if ($categoria_animal=='P') {
                        $sub_qtd_natimorto[1]++;
                    }   
                    else {
                        $sub_qtd_natimorto[2]++;
                    }                    
                }
                // VER O QUE FAZER QUANDO NASCIDOS FOR 'OUTRO' VENDA, MORTE, OURA SAIDA
            }

            /*$sql = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf 
                WHERE tbl_protocoloiatf_id = '$protocolo_id' AND 
                      tbl_protocoloiatf_lixeira = 0");
                    
            $reg_protocolo_iatf = mysqli_fetch_object($sql);

            $protocoloiatf_tipo = $reg_protocolo_iatf->tbl_protocoloiatf_tipo;*/

            //for ($i=0; $i < count($tipo_cobertura); $i++) { 
            //}

        } // FIM DO WHILE

        if ($tipo_cobertura=='I') {
            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

            $total_coberturas+= $qtd_coberturas;
            $total_femeas+= $qtd_femeas;

            $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;

            for ($j=0; $j < 3; $j++) { 
                if ($sub_qtd_femeas[$j]!=0) {
                    $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                    $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                    $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                }

                if ($sub_qtd_positivos[$j]!=0) {
                    $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                    $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                }
            }

            echo '<tr style="font-weight: 700;">
                <td>'.$desc_estacao_monta.'</td>
                <td style="font-size: 10px;">'.$desc_local.'</td>
                <td style="text-align: right;">'.$qtd_femeas.'</td>
                <td style="text-align: right;">'.$qtd_coberturas.'</td>
                <td style="text-align: right;">'.$qtd_positivos.'</td>
                <td style="text-align: right;">'.$qtd_nascidos.'</td>
                <td style="text-align: right;">'.$qtd_aborto.'</td>
                <td style="text-align: right;">'.$qtd_natimorto.'</td>
                <td style="text-align: right;">'.$qtd_desmame.'</td>
                <td style="border: none;"></td>
                <td style="text-align: right;">'.number_format($eficiencia_servico,2,',','.').'</td>
                <td style="text-align: right;">'.number_format($taxa_prenhez,2,',','.').' %</td>
                <td style="text-align: right;">'.number_format($taxa_natalidade,2,',','.').' %</td>
                <td style="text-align: right;">'.number_format($perda_gestacao,2,',','.').' %</td>
                <td style="text-align: right;">'.number_format($taxa_desmame,2,',','.').' %</td>
            </tr>';

            for ($i=0; $i < 3; $i++) { 
                if ($sub_categoria[$i]!='') {
                    echo '<tr style="color: #a5a7a8">
                        <td></td>
                        <td  style="font-size: 10px; text-align: right;">'.$sub_categoria[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_femeas[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_coberturas[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_positivos[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_nascidos[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_aborto[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_natimorto[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_desmame[$i].'</td>
                        <td style="border: none;"></td>
                        <td style="text-align: right;">'.number_format($sub_eficiencia_servico[$i],2,',','.').'</td>
                        <td style="text-align: right;">'.number_format($sub_taxa_prenhez[$i],2,',','.').' %</td>
                        <td style="text-align: right;">'.number_format($sub_taxa_natalidade[$i],2,',','.').' %</td>
                        <td style="text-align: right;">'.number_format($sub_perda_gestacao[$i],2,',','.').' %</td>
                        <td style="text-align: right;">'.number_format($sub_taxa_desmame[$i],2,',','.').' %</td>
                    </tr>';
                }
            }
        }
        else {
            //print_r('coberturas: ' . $qtd_coberturas);
            
            $eficiencia_servico = $qtd_coberturas/$qtd_femeas;

            $total_coberturas+= $qtd_coberturas;
            $total_femeas+= $qtd_femeas;

            $taxa_prenhez = ($qtd_positivos/$qtd_femeas)*100;
            $taxa_natalidade = ($qtd_nascidos/$qtd_positivos)*100;
            $perda_gestacao = (($qtd_aborto+$qtd_natimorto)/$qtd_positivos)*100;
            $taxa_desmame = ($qtd_desmame/$qtd_femeas)*100;

            for ($j=0; $j < 3; $j++) { 
                if ($sub_qtd_femeas[$j]!=0) {
                    $sub_eficiencia_servico[$j] = $sub_qtd_coberturas[$j]/$sub_qtd_femeas[$j];
                    $sub_taxa_prenhez[$j] = ($sub_qtd_positivos[$j]/$sub_qtd_femeas[$j])*100;
                    $sub_taxa_desmame[$j] = ($sub_qtd_desmame[$j]/$sub_qtd_femeas[$j])*100;
                }

                if ($sub_qtd_positivos[$j]!=0) {
                    $sub_taxa_natalidade[$j] = ($sub_qtd_nascidos[$j]/$sub_qtd_positivos[$j])*100;
                    $sub_perda_gestacao[$j] = (($sub_qtd_aborto[$j]+$sub_qtd_natimorto[$j])/$sub_qtd_positivos[$j])*100;
                }
            }

            echo '<tr style="font-weight: 700;">
                <td>Monta</td>
                <td style="font-size: 10px;">'.$desc_local.'</td>
                <td style="text-align: right;">'.$qtd_femeas.'</td>
                <td style="text-align: right;">'.$qtd_positivos.'</td>
                <td style="text-align: right;">'.$qtd_nascidos.'</td>
                <td style="text-align: right;">'.$qtd_aborto.'</td>
                <td style="text-align: right;">'.$qtd_natimorto.'</td>
                <td style="text-align: right;">'.$qtd_desmame.'</td>
                <td style="border: none;"></td>
                <td style="text-align: right;">'.number_format($taxa_prenhez,2,',','.').' %</td>
                <td style="text-align: right;">'.number_format($taxa_natalidade,2,',','.').' %</td>
                <td style="text-align: right;">'.number_format($perda_gestacao,2,',','.').' %</td>
                <td style="text-align: right;">'.number_format($taxa_desmame,2,',','.').' %</td>
            </tr>';

            for ($i=0; $i < 3; $i++) { 
                if ($sub_categoria[$i]!='') {
                    echo '<tr style="color: #a5a7a8">
                        <td></td>
                        <td  style="font-size: 10px; text-align: right;">'.$sub_categoria[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_femeas[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_positivos[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_nascidos[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_aborto[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_natimorto[$i].'</td>
                        <td style="text-align: right;">'.$sub_qtd_desmame[$i].'</td>
                        <td style="border: none;"></td>
                        <td style="text-align: right;">'.number_format($sub_taxa_prenhez[$i],2,',','.').' %</td>
                        <td style="text-align: right;">'.number_format($sub_taxa_natalidade[$i],2,',','.').' %</td>
                        <td style="text-align: right;">'.number_format($sub_perda_gestacao[$i],2,',','.').' %</td>
                        <td style="text-align: right;">'.number_format($sub_taxa_desmame[$i],2,',','.').' %</td>
                    </tr>';
                }
            }
        }

        // calculo do total da eficiencia do serviço
        $media_eficiencia_servico = $total_coberturas/$total_femeas;

        // calculo do total da taxa de prenhez
        $media_taxa_prenhez = ($total_positivos/$total_femeas)*100;

        // calculo do total da taxa de natalidade
        $media_taxa_natalidade = ($total_nascidos/$total_positivos)*100;

        // calculo do total da perda na gestação
        $media_perda_gestacao= (($total_abortos+$total_natimorto)/$total_positivos)*100;
        // calculo do total da taxa de desmame
        $media_taxa_desmame = 0.00;
    }

    echo '
        <script type="text/javascript">$("#aguardar").modal("hide");</script>';
?>

    </tbody>

    <thead>
    <?php
        if ($tipo_cobertura=='I') :
    ?>
        <tr> 
            <th style="border: none;">Dados</th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;">Índices</th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
        </tr>

        <tr>
            <th style="vertical-align: middle;text-align:center;font-size: 10px;">Estação Monta</th>
            <th style="vertical-align: middle;text-align:center;">Fazenda</th>
            <th style="vertical-align: middle;text-align:center;">Qtd Fêmeas</th>
            <th style="vertical-align: middle;text-align:center;">Qtd Coberturas</th>
            <th style="vertical-align: middle;text-align:center;">Qtd Positivas</th>
            <th style="vertical-align: middle;text-align:center;">Nascidos</th>
            <th style="vertical-align: middle;text-align:center;">Abortos</th>
            <th style="vertical-align: middle;text-align:center;">Natmorto</th>
            <th style="vertical-align: middle;text-align:center;">Desmamados</th>
            <th style="border: none;"></th>
            <th style="vertical-align: middle;text-align:center;">Eficiência do Serviço</th>
            <th style="vertical-align: middle;text-align:center;">Taxa Prenhez</th>
            <th style="vertical-align: middle;text-align:center;">Taxa Natalidade</th>
            <th style="vertical-align: middle;text-align:center;">Perda Gestação</th>
            <th style="vertical-align: middle;text-align:center;">Taxa Desmame</th>
        </tr>

    <?php
        else : // Monta sem a coluna Qtd Coberturas
    ?>

        <tr> 
            <th style="border: none;">Dados</th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;">Índices</th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
            <th style="border: none;"></th>
        </tr>

        <tr>
            <th style="vertical-align: middle;text-align:center;font-size: 10px;">Estação Monta</th>
            <th style="vertical-align: middle;text-align:center;">Fazenda</th>
            <th style="vertical-align: middle;text-align:center;">Qtd Fêmeas</th>
            <th style="vertical-align: middle;text-align:center;">Qtd Positivas</th>
            <th style="vertical-align: middle;text-align:center;">Nascidos</th>
            <th style="vertical-align: middle;text-align:center;">Abortos</th>
            <th style="vertical-align: middle;text-align:center;">Natmorto</th>
            <th style="vertical-align: middle;text-align:center;">Desmamados</th>
            <th style="border: none;"></th>
            <th style="vertical-align: middle;text-align:center;">Taxa Prenhez</th>
            <th style="vertical-align: middle;text-align:center;">Taxa Natalidade</th>
            <th style="vertical-align: middle;text-align:center;">Perda Gestação</th>
            <th style="vertical-align: middle;text-align:center;">Taxa Desmame</th>
        </tr>

    <?php
        endif;
    ?>

    </thead>

    <?php
        if ($quantidade_fazendas>2 || $quantidade_estacoes>2) :
    ?>

    <tfoot>
        <?php
            if ($tipo_cobertura=='I') :
        ?>
            <tr>
                <th></th>
                <th style="text-align: right;">Totais</th>
                <th style="text-align: right;"><?php echo $total_femeas;?></th>
                <th style="text-align: right;"><?php echo $total_coberturas;?></th>
                <th style="text-align: right;"><?php echo $total_positivos;?></th>
                <th style="text-align: right;"><?php echo $total_nascidos;?></th>
                <th style="text-align: right;"><?php echo $total_abortos;?></th>
                <th style="text-align: right;"><?php echo $total_natimorto;?></th>
                <th style="text-align: right;"><?php echo $total_desmame;?></th>
                <th style="border: none;"></th>
                <th style="text-align: right;"><?php echo number_format($media_eficiencia_servico,2,',','.');?></th>
                <th style="text-align: right;"><?php echo number_format($media_taxa_prenhez,2,',','.').' %';?></th>
                <th style="text-align: right;"><?php echo number_format($media_taxa_natalidade,2,',','.').' %';?></th>
                <th style="text-align: right;"><?php echo number_format($media_perda_gestacao,2,',','.').' %';?></th>
                <th style="text-align: right;"><?php echo number_format($media_taxa_desmame,2,',','.').' %';?></th>
            </tr>

        <?php
            else : // Monta sem o Total Coberturas
        ?>

            <tr>
                <th></th>
                <th style="text-align: right;">Totais</th>
                <th style="text-align: right;"><?php echo $total_femeas;?></th>
                <th style="text-align: right;"><?php echo $total_positivos;?></th>
                <th style="text-align: right;"><?php echo $total_nascidos;?></th>
                <th style="text-align: right;"><?php echo $total_abortos;?></th>
                <th style="text-align: right;"><?php echo $total_natimorto;?></th>
                <th style="text-align: right;"><?php echo $total_desmame;?></th>
                <th style="border: none;"></th>
                <th style="text-align: right;"><?php echo number_format($media_taxa_prenhez,2,',','.').' %';?></th>
                <th style="text-align: right;"><?php echo number_format($media_taxa_natalidade,2,',','.').' %';?></th>
                <th style="text-align: right;"><?php echo number_format($media_perda_gestacao,2,',','.').' %';?></th>
                <th style="text-align: right;"><?php echo number_format($media_taxa_desmame,2,',','.').' %';?></th>
            </tr>

        <?php
            endif;
        ?>

    </tfoot>
    <?php
        endif;
    ?>

</table>
                                            <div class="row">  
                                                <div class="col-md-12">  
                                                    <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                                                    </button>

                                                    <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="lista_indice_excel()">Excel</button>
                                                </div>
                                            </div>

                                            </div>  <!-- fim container -->
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
                            <h4 class="modal-title">Índices Reprodutivos</h4>
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
                            <h4 class="modal-title">Índices Reprodutivos - Mensagem</h4>
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


<?php 
  $javascript_file_name = 'relatorios_indice_reprodutivos.js';
  require 'rodape.php';
?>




