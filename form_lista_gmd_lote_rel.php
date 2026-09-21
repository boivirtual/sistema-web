<?php
    include "valida_sessao.inc";

    $data_sistema = date("Y-m-d");
    $partes = explode("-", $data_sistema);
    $ano_sistema = $partes[0];
    $mes_sistema = $partes[1];
    $dia_sistema = cal_days_in_month(CAL_GREGORIAN, $mes_sistema, $ano_sistema);

    $data_inicial = $_REQUEST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = cal_days_in_month(CAL_GREGORIAN, $mes_inicial, $ano_inicial);

    $data_final = $_REQUEST['data_final'];
    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    @ session_start(); 

    $local_filtro = $_REQUEST["local"];
    $categoria_filtro = $_REQUEST["categoria"];
    $sexo_filtro = $_REQUEST["sexo"];

    $wsexo_media='';
    $wsexo_fechamento='';

    if ($sexo_filtro!='Todos') {
        $wsexo_media = " AND tbl_pm_sexo IN(";
        $wsexo_media .= "'" . $sexo_filtro . "'";
        $wsexo_media.= ")";

        $wsexo_fechamento = " AND tbl_fechamento_sexo IN(";
        $wsexo_fechamento .= "'" . $sexo_filtro . "'";
        $wsexo_fechamento.= ")";
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

    $descricao_filtro= $_REQUEST["descricao_filtro"];
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

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
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

                                                <input type="hidden" id="data_inicial" <?php echo "value='".$data_inicial."'";?>>

                                                <input type="hidden" id="data_final"
                                                <?php echo "value='".$data_final."'";?>>
                                                    
                                                <input type="hidden" id="codigo_local" <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="codigo_categoria" <?php echo "value='".$categoria_filtro."'";?>>

                                                <input type="hidden" id="sexo" <?php echo "value='".$sexo_filtro."'";?>>

                                                <input type="hidden" id="descricao_filtro" <?php echo "value='".$descricao_filtro."'";?>>

                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <label class="label_consulta_rel">Filtros:</label>
                                                        <span><?php echo $descricao_filtro;?></span>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_gmd_lote()">Voltar
                                                        </button>

                                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                        onClick="lista_gmd_excel()">Excel</button>
                                                    </div>
                                                </div>

<table id="tabela_gmd_geral" class="table table-bordered table-advance table-hover" style="width:55%; font-size:10px; float: left; margin-left: 10px; border: none;">

<tbody>

<?php
    include "conecta_mysql.inc";

    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-'. $dia_inicial;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $data_sistema = $ano_sistema .'-'. $mes_sistema .'-'. $dia_sistema;

    // Cria data inicial e final para verificar se houve pesagem no periodo. Não considera a data inicial do periodo e sim o proximo mes do inicial

    $data_inicial_pesagem = date('Y-m-d', strtotime('+1 month', strtotime($ano_inicial .'-'. $mes_inicial .'-01')));

    if ($ano_final.$mes_final==$ano_sistema.$mes_sistema) {
        $data_final_pesagem = $data_sistema;
    }
    else {
        $data_final_pesagem = $data_final;
    }
    // Fim cria data inicial e final para pesagem

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

            $array_qtd_inicial_macho[$codigo_categoria] = 0;
            $array_qtd_final_macho[$codigo_categoria] = 0;
            $array_peso_medio_inicial_macho[$codigo_categoria] = 0;
            $array_peso_medio_final_macho[$codigo_categoria] = 0;
            $array_data_inicial_macho[$codigo_categoria] = '0000-00-00';
            $array_data_final_macho[$codigo_categoria] = '0000-00-00';
            $array_gmd_macho_categoria[$codigo_categoria] = 0;

            $array_qtd_inicial_femea[$codigo_categoria] = 0;
            $array_qtd_final_femea[$codigo_categoria] = 0;
            $array_peso_medio_inicial_femea[$codigo_categoria] = 0;
            $array_peso_medio_final_femea[$codigo_categoria] = 0;
            $array_data_inicial_femea[$codigo_categoria] = '0000-00-00';
            $array_data_final_femea[$codigo_categoria] = '0000-00-00';
            $array_gmd_femea_categoria[$codigo_categoria] = 0;
        }
    }   

    $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
        WHERE tbl_fechamento_data='$data_inicial' AND 
              tbl_fechamento_local='$local_filtro'" . $wsexo_fechamento);

    $num_rows_fechamento = mysqli_num_rows($tbl_fechamento);  

    if ($num_rows_fechamento!=0) {
        while ($reg_fechamento = mysqli_fetch_object($tbl_fechamento)) {
            $data_peso = $reg_fechamento->tbl_fechamento_data;
            $sexo = $reg_fechamento->tbl_fechamento_sexo;
            $categoria = $reg_fechamento->tbl_fechamento_categoria;
            $qtd_animais = $reg_fechamento->tbl_fechamento_qtd;
            $peso = $reg_fechamento->tbl_fechamento_peso;

            $peso_medio=0;

            if ($qtd_animais!=0 && $peso!=0) {
                $peso_medio=$peso/$qtd_animais;
            }

            if ($sexo=='M') {
                /*$partes = explode("-", $array_data_inicial_macho[$categoria]);
                $ano_mes_inicial_macho = $partes[0].$partes[1];
                $partes = explode("-", $array_data_final_macho[$categoria]);
                $ano_mes_final_macho = $partes[0].$partes[1];
                $partes = explode("-", $data_peso);
                $ano_mes_peso = $partes[0].$partes[1];*/

                //if ($array_data_inicial_macho[$categoria]=='0000-00-00') {
                    $array_data_inicial_macho[$categoria]=$data_peso;
                    $array_peso_medio_inicial_macho[$categoria]+=$peso_medio;
                    $array_qtd_inicial_macho[$categoria]+=$qtd_animais;
                //}

                /*if ($ano_mes_inicial_macho==$ano_mes_peso) {
                    if ($peso_medio<$array_peso_medio_inicial_macho[$categoria] && $peso_medio!=0) {
                        $array_data_inicial_macho[$categoria]=$data_peso;
                        $array_peso_medio_inicial_macho[$categoria]=$peso_medio;
                        $array_qtd_inicial_macho[$categoria]=$qtd_animais;
                    }
                }

                if ($ano_mes_inicial_macho!=$ano_mes_peso) {
                    if ($ano_mes_final_macho==$ano_mes_peso) {
                        if ($peso_medio>$array_peso_medio_final_macho[$categoria] && $peso_medio!=0) {
                            $array_data_final_macho[$categoria]=$data_peso;
                            $array_peso_medio_final_macho[$categoria]=$peso_medio;
                            $array_qtd_final_macho[$categoria]=$qtd_animais;
                        }
                    }
                    else {
                        $array_data_final_macho[$categoria]=$data_peso;
                        $array_peso_medio_final_macho[$categoria]=$peso_medio;
                        $array_qtd_final_macho[$categoria]=$qtd_animais;
                    }

                    $diferenca = strtotime($array_data_final_macho[$categoria]) - strtotime($array_data_inicial_macho[$categoria]);
                    $dias = floor($diferenca / (60 * 60 * 24)); 

                    if ($array_peso_medio_final_macho[$categoria]!=0 && 
                        $array_peso_medio_inicial_macho[$categoria]!=0) {
                        $ganho = $array_peso_medio_final_macho[$categoria] - 
                                 $array_peso_medio_inicial_macho[$categoria];
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
                        $array_gmd_macho_categoria[$categoria]=$gmd;
                    }
                }*/
            }
            else {
                /*$partes = explode("-", $array_data_inicial_femea[$categoria]);
                $ano_mes_inicial_femea = $partes[0].$partes[1];
                $partes = explode("-", $array_data_final_femea[$categoria]);
                $ano_mes_final_femea = $partes[0].$partes[1];
                $partes = explode("-", $data_peso);
                $ano_mes_peso = $partes[0].$partes[1];*/

                //if ($array_data_inicial_femea[$categoria]=='0000-00-00') {
                    $array_data_inicial_femea[$categoria]=$data_peso;
                    $array_peso_medio_inicial_femea[$categoria]+=$peso_medio;
                    $array_qtd_inicial_femea[$categoria]+=$qtd_animais;
                //}

                /*if ($ano_mes_inicial_femea==$ano_mes_peso) {
                    if ($peso_medio>$array_peso_medio_inicial_femea[$categoria] && $peso_medio!=0) {
                            $array_data_inicial_femea[$categoria]=$data_peso;
                            $array_peso_medio_inicial_femea[$categoria]=$peso_medio;
                            $array_qtd_inicial_femea[$categoria]=$qtd_animais;
                    }
                }

                if ($ano_mes_inicial_femea!=$ano_mes_peso) {
                    if ($ano_mes_final_femea==$ano_mes_peso) {
                        if ($peso_medio<$array_peso_medio_final_femea[$categoria] && $peso_medio!=0) {
                            $array_data_final_femea[$categoria]=$data_peso;
                            $array_peso_medio_final_femea[$categoria]=$peso_medio;
                            $array_qtd_final_femea[$categoria]=$qtd_animais;
                        }
                    }
                    else {
                        $array_data_final_femea[$categoria]=$data_peso;
                        $array_peso_medio_final_femea[$categoria]=$peso_medio;
                        $array_qtd_final_femea[$categoria]=$qtd_animais;
                    }

                    $diferenca = strtotime($array_data_final_femea[$categoria]) - strtotime($array_data_inicial_femea[$categoria]);
                    $dias = floor($diferenca / (60 * 60 * 24)); 

                    if ($array_peso_medio_final_femea[$categoria]!=0 && 
                        $array_peso_medio_inicial_femea[$categoria]!=0) {
                        $ganho = $array_peso_medio_final_femea[$categoria] - 
                                 $array_peso_medio_inicial_femea[$categoria];
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
                        $array_gmd_femea_categoria[$categoria]=$gmd;
                    }
                }*/
            }
        } // Fim while fechamento data inicial
    } // Fim if fechamento data inicial


    if ($ano_final.$mes_final==$ano_sistema.$mes_sistema) {

        // Gera dados finais pela tbl_peso_medio_categoria

        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
            WHERE tbl_pm_qtd_total_atual!=0 AND 
                  tbl_pm_local_id='$local_filtro'" . $wsexo_media);

        // ver where vazio

        $num_rows_media = mysqli_num_rows($tbl_media);  

        if ($num_rows_media!=0) {
            while ($reg_media = mysqli_fetch_object($tbl_media)) {
                $data_peso = $data_sistema;
                $local = $reg_media->tbl_pm_local_id;
                $sexo = $reg_media->tbl_pm_sexo;
                $categoria = $reg_media->tbl_pm_categoria_id;
                $qtd_animais = $reg_media->tbl_pm_qtd_total_atual;
                $peso_medio = $reg_media->tbl_pm_peso_medio_atual;

                $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    INNER JOIN tbl_pesagem
                            ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                    WHERE tbl_pesagem_finalizada='S' AND 
                          tbl_pesagem_codigo_local='$local' AND 
                          tbl_ite_pesagem_data_emissao>='$data_inicial_pesagem' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final_pesagem' AND 
                          tbl_ite_pesagem_categoria='$categoria' AND 
                          tbl_ite_pesagem_sexo='$sexo'");

                $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);    

                if ($num_rows_pesagem!=0) {
                    if ($sexo=='M') {
                        $array_data_final_macho[$categoria]=$data_peso;
                        $array_peso_medio_final_macho[$categoria]+=$peso_medio;
                        $array_qtd_final_macho[$categoria]+=$qtd_animais;
                    }
                    else {
                        $array_data_final_femea[$categoria]=$data_peso;
                        $array_peso_medio_final_femea[$categoria]+=$peso_medio;
                        $array_qtd_final_femea[$categoria]+=$qtd_animais;
                    }
                }

            } // Fim while fechamento data final
        } // Fim if fechamento data final
    }
    else {
        // Gera dados finais pela tabela tbl_fechamento_mensal_estoque
        $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
            WHERE tbl_fechamento_data='$data_final' AND 
                  tbl_fechamento_local='$local_filtro'" .$wsexo_fechamento);

        $num_rows_fechamento = mysqli_num_rows($tbl_fechamento);  

        if ($num_rows_fechamento!=0) {
            while ($reg_fechamento = mysqli_fetch_object($tbl_fechamento)) {
                $data_peso = $reg_fechamento->tbl_fechamento_data;
                $local = $reg_fechamento->tbl_fechamento_local;
                $sexo = $reg_fechamento->tbl_fechamento_sexo;
                $categoria = $reg_fechamento->tbl_fechamento_categoria;
                $qtd_animais = $reg_fechamento->tbl_fechamento_qtd;
                $peso = $reg_fechamento->tbl_fechamento_peso;

                $peso_medio=0;

                if ($qtd_animais!=0 && $peso!=0) {
                    $peso_medio=$peso/$qtd_animais;
                }

                $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    INNER JOIN tbl_pesagem
                            ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                    WHERE tbl_pesagem_finalizada='S' AND 
                          tbl_pesagem_codigo_local='$local' AND 
                          tbl_ite_pesagem_data_emissao>='$data_inicial_pesagem' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final_pesagem' AND 
                          tbl_ite_pesagem_categoria='$categoria' AND 
                          tbl_ite_pesagem_sexo='$sexo'");

                $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);    

                if ($num_rows_pesagem!=0) {
                    if ($sexo=='M') {
                        $array_data_final_macho[$categoria]=$data_peso;
                        $array_peso_medio_final_macho[$categoria]+=$peso_medio;
                        $array_qtd_final_macho[$categoria]+=$qtd_animais;
                    }
                    else {
                        $array_data_final_femea[$categoria]=$data_peso;
                        $array_peso_medio_final_femea[$categoria]+=$peso_medio;
                        $array_qtd_final_femea[$categoria]+=$qtd_animais;
                    }
                }
            } // Fim while fechamento data final
        } // Fim if fechamento data final
    }

    // Gera ganho de peso

    for ($i=1; $i <= 5; $i++) { 
        $categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

        // Macho
        $diferenca = strtotime($array_data_final_macho[$categoria]) - 
                     strtotime($array_data_inicial_macho[$categoria]);
        $dias = floor($diferenca / (60 * 60 * 24));             
                    
        if ($array_peso_medio_final_macho[$categoria]!=0 && 
            $array_peso_medio_inicial_macho[$categoria]!=0) {
            $ganho = $array_peso_medio_final_macho[$categoria] - 
                     $array_peso_medio_inicial_macho[$categoria];
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
            $array_gmd_macho_categoria[$categoria]=$gmd;
        }

        // Fêmea
        $diferenca = strtotime($array_data_final_femea[$categoria]) - 
                     strtotime($array_data_inicial_femea[$categoria]);
        $dias = floor($diferenca / (60 * 60 * 24));             
                    
        if ($array_peso_medio_final_femea[$categoria]!=0 && 
            $array_peso_medio_inicial_femea[$categoria]!=0) {
            $ganho = $array_peso_medio_final_femea[$categoria] - 
                     $array_peso_medio_inicial_femea[$categoria];
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
            $array_gmd_femea_categoria[$categoria]=$gmd;
        }
    }

    if ($wcategoria=="") {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            if ($array_qtd_inicial_macho[$j]!=0 && $array_qtd_final_macho[$j]!=0){
                        
                /*if ($array_data_inicial_macho[$j]==$array_data_final_macho[$j]) {
                    $array_data_final_macho[$j]=0;
                    $array_peso_medio_final_macho[$j]=0;
                    $array_qtd_final_macho[$j]=0;
                }*/
                        
                echo '<tr>';

                $gmd_total+= ($array_qtd_final_macho[$j] * $array_gmd_macho_categoria[$j]);
                $numero_gmd+= $array_qtd_final_macho[$j];

                $gmd_edi = number_format($array_gmd_macho_categoria[$j],3,',','.');

                echo '<td width="15%">'.$array_desc_categoria[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">M</td>';
                echo '<td width="15%" style="text-align: center;">'.$array_qtd_inicial_macho[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_inicial_macho[$j],2,',','.').'</td>';
                echo '<td width="15%" style="text-align: center;">'.$array_qtd_final_macho[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_final_macho[$j],2,',','.').'</td>';
                echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';
                echo '<td style="border: none"></td>';    
                echo '<td style="border: none"></td>';    
                echo '<td width="15%" style="border: none"></td>';    
                echo '</tr>';
            }

            if ($array_qtd_inicial_femea[$j]!=0 && $array_qtd_final_femea[$j]!=0) {
                        
                /*if ($array_data_inicial_femea[$j]==$array_data_final_femea[$j]) {
                    $array_data_final_femea[$j]=0;
                    $array_peso_medio_final_femea[$j]=0;
                    $array_qtd_final_femea[$j]=0;
                }*/
                        
                echo '<tr>';

                $gmd_total+= ($array_qtd_final_femea[$j] * $array_gmd_femea_categoria[$j]);
                $numero_gmd+= $array_qtd_final_femea[$j];

                $gmd_edi = number_format($array_gmd_femea_categoria[$j],3,',','.');

                echo '<td width="15%">'.$array_desc_categoria[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">F</td>';
                echo '<td width="15%" style="text-align: center;">'.$array_qtd_inicial_femea[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_inicial_femea[$j],2,',','.').'</td>';
                echo '<td width="15%" style="text-align: center;">'.$array_qtd_final_femea[$j].'</td>';
                echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_final_femea[$j],2,',','.').'</td>';
                echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';
                echo '<td style="border: none"></td>';    
                echo '<td style="border: none"></td>';    
                echo '<td width="15%" style="border: none"></td>';    
                echo '</tr>';
            }
        }
    }
    else {
        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            for ($k=0; $k < $quantidade_categoria; $k++) { 
                $value = $wcategoria[$k];
                if ($value==$j) {
                    if ($array_qtd_inicial_macho[$j]!=0 && 
                        $array_qtd_final_macho[$j]!=0) {
                        /*if ($array_data_inicial_macho[$j]==$array_data_final_macho[$j]) {
                            $array_data_final_macho[$j]=0;
                            $array_peso_medio_final_macho[$j]=0;
                            $array_qtd_final_macho[$j]=0;
                        }*/
                                
                        echo '<tr>';

                        $gmd_total+= ($array_qtd_final_macho[$j] * $array_gmd_macho_categoria[$j]);
                        $numero_gmd+= $array_qtd_final_macho[$j];

                        $gmd_edi = number_format($array_gmd_macho_categoria[$j],3,',','.');

                        echo '<td width="15%">'.$array_desc_categoria[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">M</td>';
                        echo '<td width="15%" style="text-align: center;">'.$array_qtd_inicial_macho[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_inicial_macho[$j],2,',','.').'</td>';
                        echo '<td width="15%" style="text-align: center;">'.$array_qtd_final_macho[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_final_macho[$j],2,',','.').'</td>';
                        echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td width="15%" style="border: none"></td>';    
                        echo '</tr>';
                    }

                    if ($array_qtd_inicial_femea[$j]!=0 && 
                        $array_qtd_final_femea[$j]!=0) {
                        /*if ($array_data_inicial_femea[$j]==$array_data_final_femea[$j]) {
                            $array_data_final_femea[$j]=0;
                            $array_peso_medio_final_femea[$j]=0;
                            $array_qtd_final_femea[$j]=0;
                        }*/
                        
                        echo '<tr>';

                        $gmd_total+= ($array_qtd_final_femea[$j] * $array_gmd_femea_categoria[$j]);
                        $numero_gmd+= $array_qtd_final_femea[$j];

                        $gmd_edi = number_format($array_gmd_femea_categoria[$j],3,',','.');

                        echo '<td width="15%">'.$array_desc_categoria[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">F</td>';
                        echo '<td width="15%" style="text-align: center;">'.$array_qtd_inicial_femea[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_inicial_femea[$j],2,',','.').'</td>';
                        echo '<td width="15%" style="text-align: center;">'.$array_qtd_final_femea[$j].'</td>';
                        echo '<td width="10%" style="text-align: center;">'.number_format($array_peso_medio_final_femea[$j],2,',','.').'</td>';
                        echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td style="border: none"></td>';    
                        echo '<td width="15%" style="border: none"></td>';    
                        echo '</tr>';
                    }
                }
            }
        }
    }

    echo '<script type="text/javascript">$("#aguardar").modal("hide");</script>';
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
        <th rowspan="2">Categoria</th>
        <th rowspan="2" style="text-align: center">Sexo</th>
        <th colspan="2" style="text-align: center">Pesagem Inicial</th>
        <th colspan="2" style="text-align: center">Pesagem Final</th>
        <th rowspan="2" style="text-align: center">GMD</th>
        <th style="border: none;"></th>
        <th style="border: none;"></th>
        <th style="text-align: center">GMD Global</th>
    </tr>

    <tr>
        <th style="text-align: center">Qtd Animais</th>
        <th style="text-align: center">Peso Médio</th>
        <th style="text-align: center">Qtd Animais</th>
        <th style="text-align: center">Peso Médio</th>
        <th style="border: none;"></th>
        <th style="border: none;"></th>
        <td style="text-align: center;"><?php echo $media_gmd_edi?></td>
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




