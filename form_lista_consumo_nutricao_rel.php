<?php
    include "valida_sessao.inc";

    @ session_start(); 

    $data_sistema = date("Y-m-d");

    $local_filtro = $_REQUEST['local'];
    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];
    $tipo_periodo_lote = $_REQUEST["tipo_periodo_lote"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $origem_relatorio=$_REQUEST['tipo_relatorio'];

    $_SESSION['local_nutricao']=$local_filtro;
    $_SESSION['data_inicial_nutricao']=$data_inicial; 
    $_SESSION['data_final_nutricao']=$data_final; 
    $_SESSION['tipo_rel_nutricao']=$tipo_periodo_lote; 

    $lote_filtro = $_REQUEST["lote"];

    if ($tipo_periodo_lote=='P') {
        $lote= array();
        $matriz_itens = explode(",", $lote_filtro);
        $quantidade_itens = count($matriz_itens);

        for($i=0; $i < $quantidade_itens; $i++) {
            $lote[$i]=$matriz_itens[$i];
        }

        $lote = implode(',', $lote);
        $lote = substr($lote,0, -1);

        $wlote = '';

        if ($lote_filtro!='') {
            $wlote = " AND tbl_nutricao_id_lote IN(";
            $wlote.= $lote;
            $wlote.= ")";
        }
    }
    else {
        $wlote = " AND tbl_nutricao_id_lote IN(";
            $wlote.= $lote_filtro;
            $wlote.= ")";
    }

    $pasto_filtro = $_REQUEST["pasto"];
    $pasto= array();
    $matriz_itens = explode(",", $pasto_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $pasto[$i]=$matriz_itens[$i];
    }

    $pasto = implode(',', $pasto);
    $pasto = substr($pasto,0, -1);

    $wpasto= '';

    if ($pasto_filtro!='') {
        $wpasto = " AND tbl_nutricao_codigo_pasto IN(";
        $wpasto.= $pasto;
        $wpasto.= ")";
    }

    $produto_filtro = $_REQUEST["produto"];
    $produto= array();
    $matriz_itens = explode(",", $produto_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $produto[$i]=$matriz_itens[$i];
    }

    $produto = implode(',', $produto);
    $produto = substr($produto,0, -1);

    $wproduto= '';

    if ($produto_filtro!='') {
        $wproduto = " AND tbl_nutricao_codigo_produto IN(";
        $wproduto.= $produto;
        $wproduto.= ")";
    }

    if ($data_inicial=='' && $data_final==''){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_nutricao_data >= '$data_inicial' AND tbl_nutricao_data <= '$data_final'";
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
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

  <style type="text/css">
    .dataTables_wrapper {
        margin: 0 auto;
    }

    table.dataTable {
        margin: 0 auto;
    }

    #tabela_nutricao_lote th:nth-child(1),
    #tabela_nutricao_lote td:nth-child(1) {
        display: none;
    }
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
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Consumo de Nutrição</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><img src="img/nutricao.png"> Consumo de Nutrição</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel-group">
                        <form method="POST" action="#" enctype="multipart/form-data">

                            <div class="panel"> 
                            <!--    <div class=panel-body> -->
                                    <div class="tab-content">
                                        <div id="dados" class="tab-pane active">
                                            <div class="" style="padding-right: 15px; padding-left: 15px;">

                                                <input type="hidden" id="expande_tela" value="S">

                                                <input type="hidden" id="tipo_relatorio" <?php echo "value='".$origem_relatorio."'";?>>        

                                                <input type="hidden" id="descricao_filtro"
                                                <?php echo "value='".$descricao_filtro."'";?>>

                                                <input type="hidden" id="codigo_local"
                                                <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="data_inicial"
                                                <?php echo "value='".$data_inicial."'";?>>

                                                <input type="hidden" id="data_final"
                                                <?php echo "value='".$data_final."'";?>>

                                                <input type="hidden" id="descricao_lote"
                                                <?php echo "value='".$lote_filtro."'";?>>

                                                <input type="hidden" id="codigo_pasto"
                                                <?php echo "value='".$pasto_filtro."'";?>>

                                                <input type="hidden" id="codigo_produto"
                                                <?php echo "value='".$produto_filtro."'";?>>

                                                <input type="hidden" id="tipo_periodo_lote"
                                                <?php echo "value='".$tipo_periodo_lote."'";?>>


<?php
    echo '
        <div class="row">
            <div class="col-md-12" style="padding-top: 10px;">  
                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro()">Voltar
                </button>

                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="lista_consumo_nutricao_excel()">Excel
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12" style="margin-bottom: 10px; margin-top: 10px;">
                <label class="label_consulta_rel_rel">Filtros:</label>
                <span>'.$descricao_filtro.'</span>
            </div>
        </div>';

    if ($tipo_periodo_lote=='P') { 
        // Tipo de relatório por Periodo pode ser por varios lotes
        echo '<table class="table table-striped table-advance table-hover" id="tabela_nutricao_periodo" width="100%" style="font-size: 12px; align:center;">';

        echo '<tbody>';

        $sql = "SELECT * from tbl_nutricao
            INNER JOIN tbl_pessoa
                    ON tbl_nutricao_codigo_local = tbl_pessoa_id
            INNER JOIN tbl_produto
                    ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 

            WHERE tbl_nutricao_lixeira=0 AND 
                  tbl_nutricao_codigo_local='$local_filtro'".$wperiodo . $wlote . $wproduto . $wpasto .
            " ORDER BY tbl_nutricao_id_lote DESC, tbl_nutricao_data ASC"; 

        $tbl_nutricao = mysqli_query($conector, $sql);

        $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

        $lote_anterior = 0;
        $total_nutricao_dia = 0;

        if ($num_rows_nutricao!=0) {
            while ($reg_nutricao = mysqli_fetch_object($tbl_nutricao)) {
                $qtd_produto = $reg_nutricao->tbl_nutricao_quantidade_produto;
                $qtd_animais = $reg_nutricao->tbl_nutricao_qtd_animais;
                $consumo_cabeca_gramas = ($qtd_produto/$qtd_animais)*1000;
                $codigo_produto = $reg_nutricao->tbl_nutricao_codigo_produto;
                $codigo_pasto = $reg_nutricao->tbl_nutricao_codigo_pasto;
                $lote_id = $reg_nutricao->tbl_nutricao_id_lote;

                if ($lote_id!=$lote_anterior) {
                    if ($lote_anterior==0) {
                        $lote_anterior=$lote_id;
                        $qtd_animais_anterior = $reg_nutricao->tbl_nutricao_qtd_animais;

                        //$descricao_lote = 
                        //strstr($reg_nutricao->tbl_nutricao_lote_pasto, " L-", true);

                        //if ($descricao_lote=='') {
                           // $descricao_lote = $reg_nutricao->tbl_pasto_descricao_lote;
                        //}

                        $total_nutricao_dia = $consumo_cabeca_gramas;

                        for ($i=1; $i<=31; $i++){
                            $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                            $valor[$i]='';
                        }

                        $dia = substr($reg_nutricao->tbl_nutricao_data, 8, 2);
                        $valor[$dia] = $consumo_cabeca_gramas;
                    } 
                    else {
                        // Imprime lote
                        $quantidade_dias = calcular_dias($conector, $local_filtro, $lote_anterior, $data_inicial, $data_final, $tipo_periodo_lote);

                        $media_consumo = $total_nutricao_dia/$quantidade_dias[0];

                        $consumo_edi = number_format($media_consumo, 0, ",", ".");

                        $descricao_produto = 
                        monta_produto($conector, $local_filtro, $lote_anterior, $data_inicial, $data_final);


                        if (strpos($lote_anterior, '/') === false) {
                            $lote_anterior = substr_replace($lote_anterior, '/', -4, 0);
                        }

                        $descricao_pasto_lote= pega_descricao_pasto($conector, $local_filtro, $lote_anterior, $wpasto, $pasto_filtro);

                        $descricao_pasto = $descricao_pasto_lote[0];
                        $descricao_lote = $descricao_pasto_lote[1];

                        if ($descricao_pasto!='') {
                            echo '<tr>';
                            echo '<td width="16%">'.$descricao_lote.'</td>';
                            echo '<td width="10%">'.$lote_anterior.'</td>';
                            echo '<td width="8%">'.$qtd_animais_anterior.'</td>';
                            echo '<td width="20%">'.$descricao_pasto.'</td>';
                            echo '<td width="30%">'.$descricao_produto.'</td>';
                            echo '<td width="8%" align="center">'.$quantidade_dias[0].'</td>';
                            echo '<td width="8%" style="text-align: right;">'.$consumo_edi.' g</td>';
                            echo '</tr>';                        
                        }

                        $lote_anterior=$lote_id;
                        //$descricao_pasto_atual = $reg_nutricao->tbl_pasto_descricao;
                        $qtd_animais_anterior = $reg_nutricao->tbl_nutricao_qtd_animais;

                        /*$descricao_lote = 
                        strstr($reg_nutricao->tbl_nutricao_lote_pasto, " L-", true);

                        if ($descricao_lote=='') {
                            $descricao_lote = $reg_nutricao->tbl_pasto_descricao_lote;
                        }*/

                        $total_nutricao_dia = $consumo_cabeca_gramas;

                        for ($i=1; $i<=31; $i++){
                            $i = str_pad($i, 2, "0", STR_PAD_LEFT);
                            $valor[$i]='';
                        }

                        $dia = substr($reg_nutricao->tbl_nutricao_data, 8, 2);
                        $valor[$dia] = $consumo_cabeca_gramas;

                    }
                }
                else {
                    // faz contas aqui
                    $total_nutricao_dia+=$consumo_cabeca_gramas;

                    $dia = substr($reg_nutricao->tbl_nutricao_data, 8, 2);
                    if ($valor[$dia]=='') {
                        $valor[$dia]=0;
                    }

                    $valor[$dia]+= $consumo_cabeca_gramas;
                }
            }

            // Imprime lote final do while

            $quantidade_dias = calcular_dias($conector, $local_filtro, $lote_anterior, $data_inicial, $data_final, $tipo_periodo_lote);

            //print_r($quantidade_dias);

            $media_consumo =$total_nutricao_dia/$quantidade_dias[0];

            $consumo_edi = number_format($media_consumo, 0, ",", ".");

            $descricao_produto = 
            monta_produto($conector, $local_filtro, $lote_anterior, 
                $data_inicial, $data_final);

            if (strpos($lote_anterior, '/') === false) {
                $lote_anterior = substr_replace($lote_anterior, '/', -4, 0);
            }

            $descricao_pasto_lote= pega_descricao_pasto($conector, $local_filtro, $lote_anterior, $wpasto, $pasto_filtro);

            $descricao_pasto = $descricao_pasto_lote[0];
            $descricao_lote = $descricao_pasto_lote[1];

            if ($descricao_pasto!='') {
                echo '<tr>';
                echo '<td width="16%">'.$descricao_lote.'</td>';
                echo '<td width="10%">'.$lote_anterior.'</td>';
                echo '<td width="8%">'.$qtd_animais_anterior.'</td>';
                echo '<td width="20%">'.$descricao_pasto.'</td>';
                echo '<td width="30%">'.$descricao_produto.'</td>';
                echo '<td width="8%" align="center">'.$quantidade_dias[0].'</td>';
                echo '<td width="8%" style="text-align: right;">'.$consumo_edi.' g</td>';
                echo '</tr>';                        
            }
        }

        // imprime os lotes que estão sem nutricao
        $sql = "SELECT * from tbl_pasto
            WHERE tbl_pasto_lixeira=0 AND 
                tbl_pasto_codigo_local='$local_filtro' AND 
                (tbl_pasto_id_lote!='' OR tbl_pasto_id_lote IS NOT NULL)
            ORDER BY tbl_pasto_id_lote DESC, tbl_pasto_ano_lote DESC"; 

        $tbl_pasto = mysqli_query($conector, $sql);

        $num_rows_pasto = mysqli_num_rows($tbl_pasto);

        if ($num_rows_pasto!=0) {
            while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
                $pasto_id = $reg_pasto->tbl_pasto_id;
                $lote_id = $reg_pasto->tbl_pasto_id_lote.$reg_pasto->tbl_pasto_ano_lote;

                $sql = "SELECT * from tbl_nutricao
                    WHERE tbl_nutricao_id_lote='$lote_id' AND 
                          tbl_nutricao_lixeira=0 AND 
                          tbl_nutricao_codigo_local='$local_filtro'" . $wperiodo; 

                $tbl_nutricao = mysqli_query($conector, $sql);
                $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

                if ($num_rows_nutricao==0) {
                    $descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
                    $descricao_pasto = $reg_pasto->tbl_pasto_descricao;
                    $lote_edi = $reg_pasto->tbl_pasto_id_lote.'/'.$reg_pasto->tbl_pasto_ano_lote;
                    
                    $sql = mysqli_query($conector, "SELECT * from tbl_animal_pasto
                        WHERE tbl_animal_pasto_id='$pasto_id'"); 

                    $num_rows_animais = mysqli_num_rows($sql);

                    echo '<tr style="color: #ed7672;">';
                    echo '<td width="16%">'.$descricao_lote.'</td>';
                    echo '<td width="10%">'.$lote_edi.'</td>';
                    echo '<td width="8%">'.$num_rows_animais.'</td>';
                    echo '<td width="20%">'.$descricao_pasto.'</td>';
                    echo '<td width="30%"></td>';
                    echo '<td width="8%" align="center"></td>';
                    echo '<td width="8%" style="text-align: right;"></td>';
                    echo '</tr>';  
                }
            }                      
        }
    }
    else { 
    // Tipo de relatório por apenas 1 lote (pode ser por periodo tambem)
        echo '<table class="table table-striped table-advance table-hover" id="tabela_nutricao_lote" style="font-size: 12px">';
                          
        echo '<tbody>';
          
        $data_anterior = 0;
        $total_nutricao_dia = 0;
        $desc_produto_anterior = '';
        $qtd_produto_anterior = 0;
        $qtd_por_cabeca_grama_anterior =0;
        $dias_consumo_anterior = 0;
        $consumo_cabeca_dia_anterior = 0;
        $codigo_score_anterior = 0;
        $total_consumo_cabeca_dia=0;
        $total_dias=0;

        $sql = "SELECT * FROM tbl_nutricao
            INNER JOIN tbl_pasto 
                    ON tbl_pasto_id = tbl_nutricao_codigo_pasto
            INNER JOIN tbl_produto
                    ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 
            WHERE tbl_nutricao_lixeira=0 AND 
                  tbl_nutricao_codigo_local='$local_filtro'" . $wperiodo . $wlote . $wpasto . $wproduto .
            "ORDER BY tbl_nutricao_data DESC";

        $rs = mysqli_query($conector, $sql); 

        while ($reg_nut = mysqli_fetch_object($rs)){
            $codigo_nutricao_id = $reg_nut->tbl_nutricao_id;
            $codigo_local = $reg_nut->tbl_nutricao_codigo_local;
            $codigo_produto = $reg_nut->tbl_nutricao_codigo_produto;
            $desc_produto = $reg_nut->tbl_produto_descricao;
            $codigo_pasto = $reg_nut->tbl_nutricao_codigo_pasto;
            $desc_pasto = $reg_nut->tbl_pasto_descricao;
            $lote = $reg_nut->tbl_nutricao_lote_pasto;
            $id_lote = $reg_nut->tbl_nutricao_id_lote;
                
            $dias_consumo = $reg_nut->tbl_nutricao_dias_consumo;
            $encerrada = $reg_nut->tbl_nutricao_encerrada;
            $consumo_cabeca_dia = $reg_nut->tbl_nutricao_consumo_cabeca_dia;
            $qtd_animais = intval($reg_nut->tbl_nutricao_qtd_animais); 
            $qtd_produto = $reg_nut->tbl_nutricao_quantidade_produto; 

            $data_nutricao = new DateTime($reg_nut->tbl_nutricao_data);
            $data_nutricao_edi = $data_nutricao->format('d/m/Y');
            $data_nutricao = $reg_nut->tbl_nutricao_data;

            $qtd_por_cabeca_grama = ($qtd_produto / $qtd_animais)*1000;

            if ($data_nutricao!=$data_anterior) {
                if ($data_anterior==0) {
                    $data_anterior=$data_nutricao;
                    $qtd_animais_anterior = $qtd_animais;
                    $desc_pasto_anterior = $desc_pasto;

                    /*$numero_lote = substr($id_lote, 0, 4);
                    $ano_lote = substr($id_lote, 4, 4);

                    $sql = "SELECT * from tbl_pasto
                        WHERE tbl_pasto_codigo_local = '$local_filtro' AND 
                              tbl_pasto_id_lote='$numero_lote' AND 
                              tbl_pasto_ano_lote='$ano_lote'"; 

                    $tbl_pasto = mysqli_query($conector, $sql);

                    $num_rows_pasto = mysqli_num_rows($tbl_pasto);

                    if ($num_rows_pasto!=0) {
                        $reg_pasto = mysqli_fetch_object($tbl_pasto);
                        $desc_pasto_anterior = $reg_pasto->tbl_pasto_descricao;
                    }
                    else {
                        $desc_pasto_anterior = 'Não Encontrado';
                    }*/

                    $qtd_produto_anterior=$qtd_produto;
                    $qtd_por_cabeca_grama_anterior=$qtd_por_cabeca_grama;
                    $total_nutricao_dia=$qtd_por_cabeca_grama;

                    $desc_produto_anterior = $desc_produto.'/';

                    if ($encerrada=='S') {
                        $desc_score_anterior = 'Nutrição encerrada';
                        $consumo_cabeca_dia_anterior = $consumo_cabeca_dia; 
                        $dias_consumo_anterior = $dias_consumo;
                    }
                    else {
                        $calculos = calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                        $dias_consumo_anterior = $calculos[0];
                        $consumo_cabeca_dia_anterior = $calculos[1];
                        $codigo_score = $calculos[2];

                        $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                        $num_rows = mysqli_num_rows($tbl_score);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tbl_score);
                            $desc_score_anterior = $reg->tbl_score_descricao;
                        }
                        else {
                            $desc_score_anterior = '';
                        }
                    }
                }
                else {
                    $data_anterior = new DateTime($data_anterior);
                    $data_nutricao_edi = $data_anterior->format('d/m/Y');

                    $qtd_produto_edi = number_format($qtd_produto_anterior, 2, ",", ".");
                    $qtd_por_cabeca_grama_edi = number_format($qtd_por_cabeca_grama_anterior, 2, ",", ".").' g';

                    $quantidade_dias = calcular_dias($conector, $local_filtro, $id_lote, $data_inicial, $data_final, $tipo_periodo_lote);

                    $total_consumo_cabeca_dia+=$qtd_por_cabeca_grama_anterior;

                    if ($dias_consumo_anterior==0) {
                        $consumo_cabeca = '';
                        $total_dias+=$quantidade_dias[0];
                    }
                    else {
                        $consumo_cabeca = number_format($consumo_cabeca_dia_anterior, 0, ",", ".") .' g em ' . $dias_consumo_anterior . ' dia(s)';
                        $total_dias+=$dias_consumo_anterior;
                    }

                    $desc_produto_anterior = substr($desc_produto_anterior, 0, -1);

                    echo "<tr>";
                    echo "<td width='8%'>".$id_lote."</td>";
                    echo "<td width='8%'>".$data_nutricao_edi."</td>";
                    echo "<td width='17%'>".$lote."</td>";
                    echo "<td align='center' width='8%'>".$qtd_animais_anterior."</td>";
                    echo "<td width='14%'>".$desc_pasto_anterior."</td>";
                    echo "<td width='14%'>".$desc_produto_anterior."</td>";
                    echo "<td align='center' width='8%'>".$qtd_produto_edi." Kg</td>";
                    echo "<td style='vertical-align: middle;text-align:center;' width='8%'>";
                    echo $qtd_por_cabeca_grama_edi; 
                    echo "</td>";                

                    if ($consumo_cabeca=='') {
                        echo "<td style='vertical-align: middle;text-align:center;' width='15%'></td>";                
                    }
                    else {
                        echo "<td style='vertical-align: middle;text-align:center;' width='15%'>";
                        echo $consumo_cabeca; // Mantém o valor original
                        echo "<i class='icon_info_alt btn' data-toggle='tooltip' data-placement='left' title='Cocho: ".$desc_score_anterior."' style='font-size: 10px;'></i>"; // Ícone com o tooltip
                        echo "</td>";                
                    }

                    $data_anterior=$data_nutricao;
                    $qtd_animais_anterior = $qtd_animais;
                    $desc_pasto_anterior = $desc_pasto;

                    /*$numero_lote = substr($id_lote, 0, 4);
                    $ano_lote = substr($id_lote, 4, 4);

                    $sql = "SELECT * from tbl_pasto
                        WHERE tbl_pasto_codigo_local = '$local_filtro' AND 
                              tbl_pasto_id_lote='$numero_lote' AND 
                              tbl_pasto_ano_lote='$ano_lote'"; 

                    $tbl_pasto = mysqli_query($conector, $sql);

                    $num_rows_pasto = mysqli_num_rows($tbl_pasto);

                    if ($num_rows_pasto!=0) {
                        $reg_pasto = mysqli_fetch_object($tbl_pasto);
                        $desc_pasto_anterior = $reg_pasto->tbl_pasto_descricao;
                    }
                    else {
                        $desc_pasto_anterior = 'Não Encontrado';
                    }*/

                    $qtd_produto_anterior=$qtd_produto;
                    $qtd_por_cabeca_grama_anterior=$qtd_por_cabeca_grama;
                    $total_nutricao_dia=$qtd_por_cabeca_grama;

                    $desc_produto_anterior = $desc_produto.'/';

                    if ($encerrada=='S') {
                        $desc_score_anterior = 'Nutrição encerrada';
                        $consumo_cabeca_dia_anterior = $consumo_cabeca_dia; 
                        $dias_consumo_anterior = $dias_consumo;
                    }
                    else {
                        $calculos = calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                        $dias_consumo_anterior = $calculos[0];
                        $consumo_cabeca_dia_anterior = $calculos[1];
                        $codigo_score = $calculos[2];

                        $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                        $num_rows = mysqli_num_rows($tbl_score);

                        if ($num_rows!=0){
                            $reg = mysqli_fetch_object($tbl_score);
                            $desc_score_anterior = $reg->tbl_score_descricao;
                        }
                        else {
                            $desc_score_anterior = '';
                        }
                    }
                }
            }
            else {
                $qtd_produto_anterior+=$qtd_produto;
                $qtd_por_cabeca_grama_anterior+=$qtd_por_cabeca_grama;
                $desc_produto_anterior.= $desc_produto.'/';
                $total_nutricao_dia+=$qtd_por_cabeca_grama;

                if ($encerrada=='S') {
                    $desc_score_anterior = 'Nutrição encerrada';
                    $consumo_cabeca_dia_anterior+= $consumo_cabeca_dia; 
                    $dias_consumo_anterior = $dias_consumo;
                }
                else {
                    $calculos = calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto);

                    $dias_consumo_anterior = $calculos[0];
                    $consumo_cabeca_dia_anterior+= $calculos[1];
                    $codigo_score = $calculos[2];

                    $tbl_score = mysqli_query($conector, "select * from tbl_score_cocho where tbl_score_id='$codigo_score'");
                    $num_rows = mysqli_num_rows($tbl_score);

                    if ($num_rows!=0){
                        $reg = mysqli_fetch_object($tbl_score);
                        $desc_score_anterior = $reg->tbl_score_descricao;
                    }
                    else {
                        $desc_score_anterior = '';
                    }
                }
            }
        } // Fim while 

        $data_anterior = new DateTime($data_anterior);
        $data_nutricao_edi = $data_anterior->format('d/m/Y');

        $qtd_produto_edi = number_format($qtd_produto_anterior, 2, ",", ".");
        $qtd_por_cabeca_grama_edi = number_format($qtd_por_cabeca_grama_anterior, 2, ",", ".").' g';

        $quantidade_dias = calcular_dias($conector, $local_filtro, $id_lote, $data_inicial, $data_final, $tipo_periodo_lote);

        $total_consumo_cabeca_dia+=$qtd_por_cabeca_grama_anterior;

        if ($dias_consumo_anterior==0) {
            $consumo_cabeca = '';
            $total_dias+=$quantidade_dias[0];
        }
        else {
            $consumo_cabeca = number_format($consumo_cabeca_dia_anterior, 0, ",", ".") .' g em ' . $dias_consumo_anterior . ' dia(s)';
            $total_dias+= $dias_consumo_anterior;
        }


        $desc_produto_anterior = substr($desc_produto_anterior, 0, -1);

        echo "<tr>";
        echo "<td width='8%'>".$id_lote."</td>";
        echo "<td width='8%'>".$data_nutricao_edi."</td>";
        echo "<td width='17%'>".$lote."</td>";
        echo "<td align='center' width='8%'>".$qtd_animais_anterior."</td>";
        echo "<td width='14%'>".$desc_pasto_anterior."</td>";
        echo "<td width='14%'>".$desc_produto_anterior."</td>";
        echo "<td align='center' width='8%'>".$qtd_produto_edi." Kg</td>";
        echo "<td style='vertical-align: middle;text-align:center;' width='8%'>";
        echo $qtd_por_cabeca_grama_edi; 
        echo "</td>";                

        if ($consumo_cabeca=='') {
            echo "<td style='vertical-align: middle;text-align:center;' width='15%'></td>";                
        }
        else {
            echo "<td style='vertical-align: middle;text-align:center;' width='15%'>";
            echo $consumo_cabeca; // Mantém o valor original
            echo "<i class='icon_info_alt btn' data-toggle='tooltip' data-placement='left' title='Cocho: ".$desc_score_anterior."' style='font-size: 10px;'></i>"; // Ícone com o tooltip
            echo "</td>";                
        }

        //print_r($total_consumo_cabeca_dia .' '.$total_dias);

        $media_geral = $total_consumo_cabeca_dia/$total_dias;
        $media_geral_edi = number_format($media_geral, 0, ",", ".");
    }

    mysqli_close($conector);

    echo '
        <script type="text/javascript">
            $("#aguardar").modal("hide");
        </script>';
            
    echo '</tbody>';

    echo '<thead>';
    if ($tipo_periodo_lote=='P') {
        echo '
            <tr>
            <th colspan="7" style="text-align:left; font-size: 10px; border-top: none;">Legenda:&nbsp;&nbsp;<i class="fa fa-square" style="color: #ed7672;"></i>&nbsp;<span style="color: #ed7672;">Sem Nutrição no período</span></th>
            </tr>

            <tr>
            <th style="vertical-align: middle;text-align:left;">Descrição do Lote</th>
            <th style="vertical-align: middle;text-align:left;">Lote</th>
            <th style="vertical-align: middle;text-align:left;">Nº de Cabeças</th>
            <th style="vertical-align: middle;text-align:left;">Pasto Atual</th>
            <th style="vertical-align: middle;text-align:left;">Tipo de Nutrição</th>
            <th style="vertical-align: middle;text-align:center;">Nº Dias</th>
            <th style="vertical-align: middle;text-align:center;">Consumo/Cab/Dia</th>
            </tr>
        ';
    }
    else {
        echo '<tr>
        <div class="row">
            <div class="col-md-7">
            </div>
            <div class="col-md-5 text-primary"  style="text-align:right;">
                <label class="control-label">Média de Consumo Geral dentro do período:</label>
                <span >'.$media_geral_edi. ' g/cab/dia em ' . $total_dias . ' dia(s)</span>
            </div>
        </div>
        </tr>';

        echo '
            <tr>
            <th style="vertical-align: middle;text-align:center;"><i class="fa fa-sort-numeric-desc"></i></th>
            <th style="vertical-align: middle;text-align:center;">Data</th>
            <th style="vertical-align: middle;text-align:center;">Descrição do Lote</th>
            <th style="vertical-align: middle;text-align:center;">Nº de Cabeças</th>
            <th style="vertical-align: middle;text-align:center;">Pasto</th>
            <th style="vertical-align: middle;text-align:center;">Produto</th>
            <th style="vertical-align: middle;text-align:center;">Quantidade Colocada no Cocho (Kg)</th>
            <th style="vertical-align: middle;text-align:center;">Qtde/Cabeça</th>
            <th style="vertical-align: middle;text-align:center;">Consumo Cabeça g/dia</th>
            </tr>';
    }

    echo '</thead>';
    echo '</table>';

    function pega_descricao_pasto($conector, $local_filtro, $lote_anterior, $wpasto, $pasto_filtro) {
        $partes = explode("/", $lote_anterior);

        $numero_lote = $partes[0];
        $ano_lote = $partes[1];        

        $descricao_pasto_atual = '';

        //$numero_lote = substr($lote_id, 0, 4);
        //$ano_lote = substr($lote_id, 4, 4);

        $sql = "SELECT * from tbl_pasto
            WHERE tbl_pasto_codigo_local = '$local_filtro' AND 
                  tbl_pasto_id_lote='$numero_lote' AND 
                  tbl_pasto_ano_lote='$ano_lote'"; 

        $tbl_pasto = mysqli_query($conector, $sql);
        $num_rows_pasto = mysqli_num_rows($tbl_pasto);

        if ($num_rows_pasto!=0) {
            $reg_pasto = mysqli_fetch_object($tbl_pasto);
            $id_pasto = $reg_pasto->tbl_pasto_id;

            if ($pasto_filtro!='') {
                $matriz_itens = explode(",", $pasto_filtro);
                $quantidade_itens = count($matriz_itens);

                for($i=0; $i < $quantidade_itens; $i++) {
                    if ($id_pasto==$matriz_itens[$i]) {
                        $descricao_pasto_atual = $reg_pasto->tbl_pasto_descricao;
                        $descricao_lote_atual = $reg_pasto->tbl_pasto_descricao_lote;
                    }
                }
            }
            else {
                $descricao_pasto_atual = $reg_pasto->tbl_pasto_descricao;
                $descricao_lote_atual = $reg_pasto->tbl_pasto_descricao_lote;
            }
        }
        else {
            $lote_id = $numero_lote.$ano_lote;

            $sql = "SELECT * from tbl_nutricao
                INNER JOIN tbl_pasto
                        ON tbl_nutricao_codigo_pasto = tbl_pasto_id  

                WHERE tbl_nutricao_lixeira=0 AND 
                      tbl_nutricao_codigo_local='$local_filtro' AND 
                      tbl_nutricao_id_lote='$lote_id'
                ORDER BY tbl_nutricao_id DESC LIMIT 1"; 

            $tbl_nutricao = mysqli_query($conector, $sql);

            $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);

            if ($num_rows_nutricao!=0) {
                $reg_nutricao = mysqli_fetch_object($tbl_nutricao);
                $descricao_pasto_atual = $reg_nutricao->tbl_pasto_descricao;
                $descricao_lote_atual = strstr($reg_nutricao->tbl_nutricao_lote_pasto, " L-", true);
            }
            else {
                $descricao_pasto_atual = 'Não Encontrado';
                $descricao_lote_atual = 'Não Encontrado';
            }
        }

        return [$descricao_pasto_atual,$descricao_lote_atual];
    }

    function calcular_dias($conector, $local_filtro, $id_lote, $data_inicial, $data_final, $tipo_periodo_lote){
        $data_hoje = date("Y-m-d");

        // Se não teve nutricao no dia atual, então subtrai 1 dia na data final conforme o trello Cartão: RELATORIO NUTRICAO - Cheklist: Relatorio Consumo de Nutrição Tela e Excel : Se houver nutrição para o dia atual então o dia e o valor entra na media final, caso contrario a media só será calculada com os dias até o anterior ao dia atual 

        // verifica se tem nutricao para o dia atual
        /*$sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$local_filtro' AND 
                  tbl_nutricao_id_lote = '$id_lote' AND 
                  tbl_nutricao_data = '$data_hoje'"); 
        $num_rows = mysqli_num_rows($sql);

        if ($num_rows==0) { 
            $data = new DateTime($data_final);
            $data->modify('-1 day');
            $data_final = $data->format('Y-m-d');
        }*/

        $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$local_filtro' AND 
                  tbl_nutricao_id_lote = '$id_lote' 
            ORDER BY tbl_nutricao_data ASC"); 

        $num_rows = mysqli_num_rows($sql);
        $quantidade_dias = 1;
        $data_calculo_inicial = 0;
        $data_calculo_final = $data_final;

        if ($num_rows>0) {
            while ($reg = mysqli_fetch_object($sql)) {
                $data_nutricao = $reg->tbl_nutricao_data;
                $data_encerramento = $reg->tbl_nutricao_data_encerramento;
                $dias_de_consumo = $reg->tbl_nutricao_dias_consumo;

                if ($tipo_periodo_lote=='L') {
                    if ($dias_de_consumo==0 || $dias_de_consumo=='') {
                        $data_calculo_inicial = $data_nutricao;
                    }
                }

                if ($data_nutricao<$data_inicial) {
                    $data_calculo_inicial = $data_inicial;
                }
                else if ($data_nutricao>=$data_inicial && 
                    $data_calculo_inicial==0){

                    $data_calculo_inicial = $data_nutricao;
                }

                //print_r($data_final . ' ' . $data_nutricao .'</br>');

                if ($data_nutricao>$data_final) {
                    $data_calculo_final = $data_final;
                }

                if ($data_encerramento !='' && $data_encerramento>=$data_inicial && 
                    $data_encerramento<=$data_final) {
                    $data_calculo_final = $data_encerramento;
                }

                $data_inicial_str = new DateTime($data_calculo_inicial);
                $data_final_str = new DateTime($data_calculo_final);

                $intervalo = $data_inicial_str->diff($data_final_str);
                $quantidade_dias = $intervalo->days + 1;
            }
        }

        return [$quantidade_dias];
    }

    function monta_produto($conector, $local_filtro, $id_lote, $data_inicial, $data_final){

        $tbl_nutricao = mysqli_query($conector, "SELECT * from tbl_nutricao
            INNER JOIN tbl_produto
                    ON tbl_nutricao_codigo_produto = tbl_produto_codigo_id 

            WHERE tbl_nutricao_lixeira=0 AND 
                  tbl_nutricao_codigo_local='$local_filtro' AND
                  tbl_nutricao_id_lote = '$id_lote' 
            ORDER BY tbl_produto_descricao ASC"); 

        $num_rows_nutricao = mysqli_num_rows($tbl_nutricao);
        $descricao_produto_anterior = '';
        $descricao_produto= '';


        if ($num_rows_nutricao>0) {
            while ($reg = mysqli_fetch_object($tbl_nutricao)) {

                if ($reg->tbl_produto_descricao!=$descricao_produto_anterior) {
                    $descricao_produto_anterior=$reg->tbl_produto_descricao;
                    $descricao_produto.= $reg->tbl_produto_descricao.'/';
                }
            }
        }

        return $descricao_produto = substr($descricao_produto, 0, -1);
    }

    function calcular_consumo($conector, $codigo_nutricao_id, $codigo_local, $id_lote, $data_nutricao, $qtd_animais, $qtd_produto ){
        $dias = 0;
        $consumo = 0;
        $codigo_score = 0;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$codigo_local' AND 
                  tbl_nutricao_id_lote = '$id_lote' AND 
                  tbl_nutricao_data > '$data_nutricao' 
            ORDER BY tbl_nutricao_data ASC"); 

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows>0) {
            $reg = mysqli_fetch_object($sql);
            $data_posterior = $reg->tbl_nutricao_data;
            $codigo_score = $reg->tbl_nutricao_codigo_score_cocho; 
            $firstDate  = new DateTime($data_nutricao);
            $secondDate = new DateTime($data_posterior);
            $intvl = $firstDate->diff($secondDate);
            $dias = $intvl->days;

            if ($dias==0) {
                $dias = 1;
            }

            $consumo = ($qtd_produto/$qtd_animais/$dias)*1000;

            /*$sql = ("UPDATE tbl_nutricao SET
                tbl_nutricao_dias_consumo='$dias',
                tbl_nutricao_consumo_cabeca_dia='$consumo'
                WHERE tbl_nutricao_id='$codigo_nutricao_id'");

            $resultado = mysqli_query($conector,$sql);*/

            //$consumo = number_format($consumo, 0, ",", ".");
            //$consumo = intval($consumo);
        } 

        return [$dias, $consumo, $codigo_score];
    }

    function busca_score($conector, $codigo_local, $id_lote, $data_nutricao) {
        $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
            WHERE tbl_nutricao_lixeira = 0 AND 
                  tbl_nutricao_codigo_local = '$codigo_local' AND 
                  tbl_nutricao_id_lote = '$id_lote' 
            ORDER BY tbl_nutricao_data ASC"); 

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows>0) {
            while ($reg = mysqli_fetch_object($sql)){
                $data_anterior = $reg->tbl_nutricao_data;

                if ($data_anterior<=$data_nutricao) {
                    $codigo_score = $reg->tbl_nutricao_codigo_score_cocho; 
                }
            }
        }
        else {
            $codigo_score = 0;
        }

        return $codigo_score;
    }

?>

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
                            <h4 class="modal-title">Consumo de Nutrição</h4>
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
                            <h4 class="modal-title">Consumo de Nutrição - Mensagem</h4>
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
  $javascript_file_name = 'nutricao.js';
  require 'rodape.php';
?>




