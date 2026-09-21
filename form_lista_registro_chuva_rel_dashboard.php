
<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $ano = $_REQUEST["ano"];
    $local = $_REQUEST["local"];
    $descricao_filtro = $_REQUEST["descricao_filtro"];

    $_SESSION['local_chuva']=$local;
    $_SESSION['ano_chuva']=$ano;
    
    $controle_estoque = $_SESSION['controle_estoque'];

    $ano_inicial = $ano - 4;
    $ano_final = $ano;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
    <title>Boi Virtual</title>

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
    <link href="css/tabela.css" rel="stylesheet">
    <link href="css/select-1.13.14.css" rel="stylesheet" > 

    <script src="https://www.gstatic.com/charts/loader.js" type="text/javascript" ></script>

    <script type="text/javascript">

    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
        var ano = $("#ano").val();
        var local = $("#local").val();
        var volumes = $("#volumes").val();

        var php = volumes.split("<|>");
        var jan = parseInt(php[0]);
        var dia_jan = parseInt(php[1]);
        var fev = parseInt(php[2]);
        var dia_fev = parseInt(php[3]);
        var mar = parseInt(php[4]);
        var dia_mar = parseInt(php[5]);
        var abr = parseInt(php[6]);
        var dia_abr = parseInt(php[7]);
        var mai = parseInt(php[8]);
        var dia_mai = parseInt(php[9]);
        var jun = parseInt(php[10]);
        var dia_jun = parseInt(php[11]);
        var jul = parseInt(php[12]);
        var dia_jul = parseInt(php[13]);
        var ago = parseInt(php[14]);
        var dia_ago = parseInt(php[15]);
        var set = parseInt(php[16]);
        var dia_set = parseInt(php[17]);
        var out = parseInt(php[18]);
        var dia_out = parseInt(php[19]);
        var nov = parseInt(php[20]);
        var dia_nov = parseInt(php[21]);
        var dez = parseInt(php[22]);
        var dia_dez = parseInt(php[23]);
        var chartDiv = document.getElementById('area_grafico');

        var sett = 'fill-color: #4169e1; fill-opacity: 0.3; stroke-color: #0d215e; stroke-width: 0.5;';
        var data = google.visualization.arrayToDataTable([
          ['Meses', 'mm Chuva', {role: 'style'}, 'Dias Chuvosos'],
          ['Jan', jan, sett, dia_jan],
          ['Fev', fev, sett, dia_fev],
          ['Mar', mar, sett, dia_mar],
          ['Abr', abr, sett, dia_abr],
          ['Mai', mai, sett, dia_mai],
          ['Jun', jun, sett, dia_jun],
          ['Jul', jul, sett, dia_jul],
          ['Ago', ago, sett, dia_ago],
          ['Set', set, sett, dia_set],
          ['Out', out, sett, dia_out],
          ['Nov', nov, sett, dia_nov],
          ['Dez', dez, sett, dia_dez]
        ]);

        var classicOptions = {
          series: {
            0: {targetAxisIndex: 0, type: 'bars', color: '#c7d1ed',},
            1: {targetAxisIndex: 1, type: 'line', lineWidth: 0.5, pointSize: 3,}
          },
          title: 'Preciptação x Dias Chuvosos',
          vAxes: {
            // Adds titles to each axis.
            0: {title: 'mm Chuva',},
            1: {title: 'Dias Chuvosos', gridlines: {count: 4, color: '#f8f8f8'}}
          }
        };

        function drawClassicChart() {
          var classicChart = new google.visualization.ColumnChart(chartDiv);
          classicChart.draw(data, classicOptions);
        }

        drawClassicChart();
    };    

//  Grafico volume anual
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawStuff_anual);

    function drawStuff_anual() {
        var volumes = $("#volumes_anuais").val();

        var php = volumes.split("<|>");
        var ano_1 = php[0];
        var vol_1 = parseInt(php[1]);
        var dia_1 = parseInt(php[2]);
        var ano_2 = php[3];
        var vol_2 = parseInt(php[4]);
        var dia_2 = parseInt(php[5]);
        var ano_3 = php[6];
        var vol_3 = parseInt(php[7]);
        var dia_3 = parseInt(php[8]);
        var ano_4 = php[9];
        var vol_4 = parseInt(php[10]);
        var dia_4 = parseInt(php[11]);
        var ano_5 = php[12];
        var vol_5 = parseInt(php[13]);
        var dia_5 = parseInt(php[14]);
        var chartDiv = document.getElementById('area_grafico_anual');

        var sett = 'fill-color: #4169e1; fill-opacity: 0.3; stroke-color: #0d215e; stroke-width: 0.5;';

        var data = google.visualization.arrayToDataTable([
          ['Ano', 'mm Chuva', {role: 'style'}, 'Dias Chuvosos'],
          [ano_1, vol_1, sett, dia_1],
          [ano_2, vol_2, sett, dia_2],
          [ano_3, vol_3, sett, dia_3],
          [ano_4, vol_4, sett, dia_4],
          [ano_5, vol_5, sett, dia_5],
        ]);

        var classicOptions = {
          bar: {groupWidth: '70%'},

          series: {
            0: {targetAxisIndex: 0, type: 'bars', color: '#c7d1ed',},
            1: {targetAxisIndex: 1, type: 'line', lineWidth: 0.5, pointSize: 3,}
          },
          vAxes: {
            0: {title: 'mm Chuva Anual',},
            1: {title: 'Dias Chuvosos', gridlines: {count: 4, color: '#f8f8f8'}}

          }
        };

        function drawClassicChart() {
        	var classicChart = new google.visualization.ColumnChart(chartDiv);
	        classicChart.draw(data, classicOptions);
        }

        drawClassicChart();
    };    
    </script>

</head>

<body>

   <?php

   @ session_start();   
/*    if(isset($_SESSION['menu_relatorios'])) {
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
        echo '<strong class="negrito">Atenção! </strong><span>Você não efetuol o login!</span>';  
        echo '</div>';         
        exit;
    }*/

?>

<!-- container section start -->
<section id="container" class="container-fluid">

    <!--sidebar start-->
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
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content"  style="overflow-x:hidden">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Home <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="menu.php">Painel</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Chuvas</span></span>

           <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-cloud"></i> Chuvas</h3>
                </div>
            </div>

            <section class="panel" style="overflow-x:auto; overflow-y:hidden;">
                <div class="row col-md-12"  id="total_contas">

                    <input type="hidden" id="expande_tela" value="S">
                    <input type="hidden" id="ano" <?php echo "value='".$ano."'";?>>
                    <input type="hidden" id="local" <?php echo "value='".$local."'";?>>
                    <input type="hidden" id="descricao_filtro" <?php echo "value='".$descricao_filtro."'";?>>
                    <input type="hidden" id="controle_estoque" <?php echo "value='".$controle_estoque."'";?>>

                    <div class="row col-md-12">
                        <div class="col-md-8">
                            <label class="label_consulta_rel">Filtros:</label>
                            <span id="descricao_filtro" class="text-muted" 
                            style="color: #829c9c"><?php echo $descricao_filtro;?></span>
                        </div>
                    
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info pull-right" onclick="voltar_painel()">Voltar
                            </button>

                            <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                            onClick="lista_chuvas_excel()">Excel</button>
                        </div>
                    </div>
                </div>

        	    <div class="row col-md-12">
                    <div class="col-md-6"> 
                        <table id="tabela_lista_chuva" class="table table-bordered table-advance table-hover" style="font-size: 10px;">

                        <thead>
                            <tr>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Dia</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Jan</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Fev</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Mar</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Abr</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Mai</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Jun</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Jul</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Ago</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Set</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Out</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Nov</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Dez</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                $total_volume_ano_atual[$ano]=0;
                                $total_dias_ano_atual[$ano]=0;

                                for ($i = 0; $i <= 24; $i++) {
                                    $valor[$i]=0;
                                }

                                for ($a=$ano_inicial; $a<=$ano_final ; $a++) { 
									$total_volume_ano[$a]=0;
                                    $total_dias_ano[$a]=0;

									for ($m=1; $m <=12; $m++) { 
                                    	$mes = ltrim($m, "0");
										$volume_anual[$a][$mes]=0;
									}
                                }

                                for ($m=1; $m <=12; $m++) { 
                                    $mes = ltrim($m, "0");
                                    $total_volume_mes[$mes]=0;
                                    $dias_chuva[$mes]=0;
									
                                    for ($d=1; $d <=31; $d++) { 
                                        $dia = ltrim($d, "0");
                                        $volume[$dia][$mes]='';
                                    }
                                }

                                $chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
                                            WHERE year(tbl_chuva_data) = '$ano' AND tbl_chuva_local='$local'");

                                $num_rows = mysqli_num_rows($chuva);  

                                if ($num_rows!=0) {
                                    while ($reg_chuva = mysqli_fetch_object($chuva)) {
                                        $data_chuva=new DateTime($reg_chuva->tbl_chuva_data);
                                        $mes_chuva=$data_chuva->format('m');
                                        $dia_chuva=$data_chuva->format('d');
                                        $ano_chuva=$data_chuva->format('Y');

                                        $mes_chuva=ltrim($mes_chuva, "0");
                                        $dia_chuva=ltrim($dia_chuva, "0");

                                        $volume[$dia_chuva][$mes_chuva]=$reg_chuva->tbl_chuva_volume_chuva;

                                        if ($reg_chuva->tbl_chuva_volume_chuva!=0 && 
                                            $reg_chuva->tbl_chuva_volume_chuva!='') {

                                            $total_volume_mes[$mes_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
                                            $dias_chuva[$mes_chuva]++;

                                            $total_volume_ano_atual[$ano_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
                                            $total_dias_ano_atual[$ano_chuva]++;
                                        }
                                    }
                                }

                                $valor[0]=$total_volume_mes[1];
                                $valor[1]=$dias_chuva[1];
                                $valor[2]=$total_volume_mes[2];
                                $valor[3]=$dias_chuva[2];
                                $valor[4]=$total_volume_mes[3];
                                $valor[5]=$dias_chuva[3];
                                $valor[6]=$total_volume_mes[4];
                                $valor[7]=$dias_chuva[4];
                                $valor[8]=$total_volume_mes[5];
                                $valor[9]=$dias_chuva[5];
                                $valor[10]=$total_volume_mes[6];
                                $valor[11]=$dias_chuva[6];
                                $valor[12]=$total_volume_mes[7];
                                $valor[13]=$dias_chuva[7];
                                $valor[14]=$total_volume_mes[8];
                                $valor[15]=$dias_chuva[8];
                                $valor[16]=$total_volume_mes[9];
                                $valor[17]=$dias_chuva[9];
                                $valor[18]=$total_volume_mes[10];
                                $valor[19]=$dias_chuva[10];
                                $valor[20]=$total_volume_mes[11];
                                $valor[21]=$dias_chuva[11];
                                $valor[22]=$total_volume_mes[12];
                                $valor[23]=$dias_chuva[12];

                                $str=$valor[0] . '<|>';

                                for ($i=1; $i<=24; $i++){
                                    $str.=$valor[$i] . '<|>';
                                }

                                for ($d=1; $d <=31; $d++) { 
                                    echo '<tr>';
                                    $dia = ltrim($d, "0");
                                    echo '<td width="3%" align="center">'.$dia.'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][1].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][2].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][3].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][4].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][5].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][6].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][7].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][8].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][9].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][10].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][11].'</td>';
                                    echo '<td width="3%" align="right">'.$volume[$dia][12].'</td>';
                                    echo '</tr>'; 
                                }

                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="background-color: #C2E0E0; color: #1C1C1C">mm</th>
                            <?php

                                for ($m=1; $m <=12; $m++) {
                                    $mes = ltrim($m, "0");
                                    if ($total_volume_mes[$mes]==0){
                                        $total_volume_mes[$mes]='';
                                    } 
                                }

                                echo '<td width="3%" align="right" class="total_jan" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[1].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[2].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[3].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[4].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[5].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[6].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[7].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[8].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[9].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[10].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[11].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_mes[12].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #C2E0E0; color: #1C1C1C">'.$total_volume_ano_atual[$ano].'</td>';
                            ?>
                            </tr>
                            <tr>
                                <th style="background-color: #DEE; color: #1C1C1C">Dias</th>
                            <?php

                                for ($m=1; $m <=12; $m++) {
                                    $mes = ltrim($m, "0");
                                    if ($dias_chuva[$mes]==0){
                                        $dias_chuva[$mes]='';
                                    } 
                                }

                                echo '<td width="3%" align="right" class="jan" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[1].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[2].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[3].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[4].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[5].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[6].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[7].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[8].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[9].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[10].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[11].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$dias_chuva[12].'</td>';
                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$total_dias_ano_atual[$ano].'</td>';
                            ?>
                            </tr>
                            
                        </tfoot>
                        </table>

                    </div>

                    <div class="col-md-6"> 
                        <input type="hidden" id="volumes" <?php echo "value='".$str."'";?>>
                        <div id="area_grafico" style="width:700px; height:340px"></div>

                        <p class="text-muted" 
                        style="color: #829c9c">Histórico dos últimos 5 anos</p>

                        <table id="tabela_resumo_anual" class="table table-bordered table-advance table-hover" style="font-size: 10px;">

                        <thead>
                            <tr>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Ano</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Jan</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Fev</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Mar</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Abr</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Mai</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Jun</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Jul</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Ago</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Set</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Out</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Nov</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Dez</th>
                                <th style="text-align: center; background-color: #DEE; color: #1C1C1C">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
                                $chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
                                            WHERE year(tbl_chuva_data)>= '$ano_inicial' AND
                                                  year(tbl_chuva_data)<= '$ano_final' AND
                                                  tbl_chuva_local='$local'");

                                $num_rows = mysqli_num_rows($chuva);  

                                if ($num_rows!=0) {
                                    while ($reg_chuva = mysqli_fetch_object($chuva)) {
                                        $data_chuva=new DateTime($reg_chuva->tbl_chuva_data);
                                        $mes_chuva=$data_chuva->format('m');
                                        $ano_chuva=$data_chuva->format('Y');

                                        $mes_chuva=ltrim($mes_chuva, "0");

                                        if ($reg_chuva->tbl_chuva_volume_chuva!=0 && 
                                            $reg_chuva->tbl_chuva_volume_chuva!='') {

                                        	$volume_anual[$ano_chuva][$mes_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;

                                            $total_volume_ano[$ano_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
                                            $total_dias_ano[$ano_chuva]++;
                                        }
                                    }
                                }

								$str_anual='';

                                for ($a=$ano_inicial; $a<=$ano_final; $a++) { 
									$str_anual.=$a . '<|>';
									$str_anual.=$total_volume_ano[$a] . '<|>';
                                    $str_anual.=$total_dias_ano[$a] . '<|>';
								}

                               // $str_anual.=$ano . '<|>';
                               // $str_anual.=$total_volume_ano_atual[$ano] . '<|>';

                                for ($a=$ano_inicial; $a<=$ano_final; $a++) { 
                                    echo '<tr>';

	                                for ($m=1; $m <=12; $m++) {
	                                    $mes = ltrim($m, "0");
	                                    if ($volume_anual[$a][$mes]==0){
	                                        $volume_anual[$a][$mes]='';
	                                    } 
	                                }

                                    echo '<td width="3%" align="center">'.$a.'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][1].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][2].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][3].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][4].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][5].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][6].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][7].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][8].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][9].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][10].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][11].'</td>';
                                    echo '<td width="3%" align="right">'.$volume_anual[$a][12].'</td>';
	                                echo '<td width="3%" align="right" style="background-color: #DEE; color: #1C1C1C">'.$total_volume_ano[$a].'</td>';
                                    echo '</tr>'; 
                                }
                            ?>
                        </tbody>
                    	</table>

                        <input type="hidden" id="volumes_anuais" <?php echo "value='".$str_anual."'";?>>
                        <div id="area_grafico_anual" style="width:650px; height:340px"></div>

                    </div>
            	</div>

            </section>
        </section>
    </section>

    <div class="text-center">
        <div class="credits">
            <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
        </div>
    </div>

</section> <!-- container section start end -->
  
<!-- javascripts -->

<script src="js/jquery.js?<?php echo Versao; ?>"></script>
<script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
<script src="js/scripts.js?<?php echo Versao; ?>"></script>
<script src="js/dashboard.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
<script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
</script>

<script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
" charset="utf-8" type="text/javascript" >
</script>
<script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>


<?php 
 // $javascript_file_name = 'contas_pagar.js';
 // require 'rodape.php';
?>


