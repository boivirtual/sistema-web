<?php
    include "conecta_mysql.inc";
    include "valida_sessao.inc";

    $data_sistema = date("Y-m-d");

    $data_inicial = date("Y-m");
    $data_final = date("Y-m");

    $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_classe=4 and tbl_pessoa_lixeira=0"); 

    $tbl_cc = mysqli_query($conector, "select * from tbl_centro_custo 
        where tbl_cc_lixeira=0 and
              tbl_cc_codigo_id=1");

    $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade where tab_registro_lixeira_categoria_idade=0"); 

    @ session_start(); 
    $codigo_usuario = $_SESSION['id_usuario'];
    $grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

    $array_categoria = '';

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND lixeira_usuario=0 ";  
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }

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
    <link href="css/style.css?<?php echo Versao; ?>" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay"  rel="stylesheet" crossorigin="anonymous">

    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
    <link href="css/select-1.13.14.css" rel="stylesheet" > 
    <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

    <!--<link href="assets/materialize/css/materialize.css?<?php echo Versao;?>" rel="stylesheet" media="screen,projection" />-->

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
    <script type="text/javascript">
        var estoque_medio_periodo = 0;

        function processar_dados() {
            var data_inicial = $("#data_inicial").val();
            var data_final = $("#data_final").val();
            var local = $("#codigo_local").val();
            var codigo_cc = $("#codigo_cc").val();
            $(".menu_show").show();
            
            if (data_inicial==0) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Informe as Datas Inicial e Final!');
                $("#data_inicial").focus();
                document.getElementById("data_inicial").style.borderColor = "#FF0000";
                $(".dashboard-cards").hide();
                return;
            }

            if (data_final<data_inicial) {
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Data Final menor que a Data Inicial!');
                $("#data_final").focus();
                document.getElementById("data_final").style.borderColor = "#FF0000";
                $(".dashboard-cards").hide();
                return;
            }

            var filtro_categoria = $("#categoria option:selected").text();
            $("#filtro_gmd_global").text('*Filtro GMD: ' + filtro_categoria);

            if (filtro_categoria=='Selecione Categorias'){
                $("#mensagem_erro").modal();
                $("#mensagem_erro .modal-body").html('Selecione Categorias para o cálculo GMD Global!');
                return;
            }

            var c001 = '';
            var m001 = '';
            var f001 = '';
            var c002 = '';
            var m002 = '';
            var f002 = '';
            var c003 = '';
            var m003 = '';
            var f003 = '';
            var c004 = '';
            var m004 = '';
            var f004 = '';
            var c005 = '';
            var m005 = '';
            var f005 = '';
            var array_cat = [];
            var array_macho = [];
            var array_femea = [];

            if ($("#c001").is(":checked") == true){
                array_cat.push('001');

                if ($("#M001").is(":checked") == true) {
                    m001 = 'M';
                    array_macho.push('S');
                }
                else{
                    array_macho.push('N');
                }

                if ($("#F001").is(":checked") == true) {
                    f001 = 'F';
                    array_femea.push('S');
                }
                else{
                    array_femea.push('N');
                }
            }

            if ($("#c002").is(":checked") == true){
                array_cat.push('002');
                
                if ($("#M002").is(":checked") == true) {
                    m002 = 'M';
                    array_macho.push('S');
                }
                else{
                    array_macho.push('N');
                }

                if ($("#F002").is(":checked") == true) {
                    f002 = 'F';
                    array_femea.push('S');
                }
                else{
                    array_femea.push('N');
                }
            }

            if ($("#c003").is(":checked") == true){
                array_cat.push('003');
                
                if ($("#M003").is(":checked") == true) {
                    m003 = 'M';
                    array_macho.push('S');
                }
                else{
                    array_macho.push('N');
                }

                if ($("#F003").is(":checked") == true) {
                    f003 = 'F';
                    array_femea.push('S');
                }
                else{
                    array_femea.push('N');
                }
            }

            if ($("#c004").is(":checked") == true){
                array_cat.push('004');
                
                if ($("#M004").is(":checked") == true) {
                    m004 = 'M';
                    array_macho.push('S');
                }
                else{
                    array_macho.push('N');
                }

                if ($("#F004").is(":checked") == true) {
                    f004 = 'F';
                    array_femea.push('S');
                }
                else{
                    array_femea.push('N');
                }
            }

            if ($("#c005").is(":checked") == true){
                array_cat.push('005');
                
                if ($("#M005").is(":checked") == true) {
                    m005 = 'M';
                    array_macho.push('S');
                }
                else{
                    array_macho.push('N');
                }

                if ($("#F005").is(":checked") == true) {
                    f005 = 'F';
                    array_femea.push('S');
                }
                else{
                    array_femea.push('N');
                }
            }

            filtro_categoria = $("#categoria option:selected").text();

            if (filtro_categoria=='Todas') {
                array_cat = ['001','002','003','004','005'];
                array_macho = ['S','S','S','S','S'];
                array_femea = ['S','S','S','S','S'];
            }

            var expande_tela = $("#expande_tela").val();

            if (expande_tela=="S"){
                if (jQuery('#sidebar > ul').is(":visible") === true) {
                    jQuery('#main-content').css({
                        'margin-left': '0px'
                    });
                    jQuery('#sidebar').css({
                        'margin-left': '-180px'
                    });
                    jQuery('#sidebar > ul').hide();
                    jQuery("#container").addClass("sidebar-closed");
                }
            }

            $(".exibe_mais_filtros").hide();
            $(".mais_filtros").show();
            $(".menos_filtros").hide();

            $('#aguardar').modal('show');

            estoque_medio_periodo = 0;

            $.post("lista_estoque_cabeca_painel.php",{local: local, data_inicial:data_inicial, data_final:data_final, array_cat:array_cat, array_macho:array_macho, array_femea:array_femea}, function(retorno){

                var php = retorno.split("<|>");
                var total_ha = php[10];

                //alert (php[14]);

                var dados = JSON.parse(php[11]);

                /*for (var i = 0; i < dados.length; i++) {
                    var data = dados[i].data;
                    var valor = (dados[i].valor/total_ha).toFixed(2);
                    valor = parseFloat(valor);
                }*/

                $("#total_unidade").text(php[0] + ' Un');
                $("#total_peso").text(php[1] + ' Kg');
                $("#total_arroba").text(php[2] + ' @');

                $("#media_unidade").text(php[3] + ' Un');
                $("#media_peso").text(php[4] + ' Kg');
                $("#media_arroba").text(php[5] + ' @');

                estoque_medio_periodo = php[9];

                $("#locacao_unidade").text(php[6] + ' Cab/Ha');
                $("#lotacao_media").text(php[7] + ' KgPc/Ha');
                $("#lotacao_ua").text(php[8] + ' Ua/Ha');

                $("#gmd_global").text(php[14] + '*');

                $("#producao_kg").text(php[12] + ' Kg');
                $("#producao_arroba").text(php[13] + ' @');

                // LOTAÇÃO DA FAZENDA (GRAFICO LINHAS)
                google.charts.load('current', {'packages':['corechart'], 'language': 'pt-BR'});
                google.charts.setOnLoadCallback(lineChart);

                function lineChart() {
                    var mesAnoTicks=[];
                    var dataArrayAnoMes = [[{type: 'date', label: 'Mês/Ano'}, {type: 'number', label: 'KgPc/Ha'}, {type: 'number', label: 'Lotação Média'}],];

                    for (var i = 1; i < dados.length; i++) {
                        var data = dados[i].data;
                        var valor = (dados[i].valor/total_ha).toFixed(2);
                        valor = parseFloat(valor);

                        var data_dividida = data.split("-");
                        var ano_div = data_dividida[0];
                        var mes_div = data_dividida[1];
                        var dia_div = data_dividida[2];

                        mes_div--;

                        var data_row = 'Date('+ano_div+','+mes_div+','+dia_div+')';

                        var row = [data_row, valor, php[7]];
                        dataArrayAnoMes.push(row);

                        // monta ticks
                        var data_ticks = new Date(ano_div,mes_div,dia_div);
                        mesAnoTicks.push(data_ticks);

                    }
                    
                    var data = google.visualization.arrayToDataTable(dataArrayAnoMes);

                    var options = {
                        seriesType: 'line',
                        series: {
                            0: {
                                pointSize: 3,
                                lineWidth: 1.5,
                            },

                            1: {
                                type: 'line',
                                lineWidth: 1,                                
                            },
                        },

                        vAxis: {
                            //minValue: 100,
                            //maxValue: 600,
                            format: 'decimal',
                            //gridlines: { count: 5 },
                        }, 

                        hAxis: {
                            format: 'MMM',
                            gridlines: {color: 'none'},
                            ticks: mesAnoTicks,
                        },

                        titlePosition: 'none',
                        backgroundColor: 'transparent',
                        fontName: 'Futura Std Light',
                        fontSize: 12,
                        chartArea: {
                            left: 80,
                            bottom:50,  
                            top:5,                    
                            width: "60%",
                            height: "auto"
                        },
                      //curveType: 'function',
                        //legend: { position: 'bottom' }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('line_chart'));

                    chart.draw(data, options);
                }
                // FIM LOTAÇÃO DA FAZENDA (GRAFICO LINHAS)

                //GASTO POR CONTAS (GRAFICO PIZZA) 
                contas = JSON.parse(readContas(local, data_inicial, data_final, codigo_cc));

                contas.sort(function(a,b) {
                    if(a.valor > b.valor) {
                        return -1;
                    } 
                    else {
                        return true;
                    }
                });

                google.charts.load('current', {'packages':['corechart'], 'language': 'pt-BR'});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var dataArray = [['Conta', 'Valor'],];

                    for (var i = 0; i < contas.length; i++) {

                        var row = [contas[i].descricao, contas[i].valor];
                        dataArray.push(row);
                    }
                    
                    var data = google.visualization.arrayToDataTable(dataArray);
                    
                    var options = {
                        titlePosition: 'none',
                        backgroundColor: 'transparent',
                        fontName: 'Futura Std Light',
                        fontSize: 12,
                        chartArea: {
                            left: 20,
                            bottom:25,  
                            top:5,                    
                            width: "80%",
                            height: "auto"
                        },
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                    chart.draw(data, options);

                }// FIM GASTO POR CONTAS (GRAFICO PIZZA)

                //CUSTO POR CABEÇA (BARRAS) 

                contas_cabeca = JSON.parse(readContasCabeca(local, data_inicial, data_final, codigo_cc));

                google.charts.load('current', {packages: ['corechart', 'bar'], 'language': 'pt-BR'});
                google.charts.setOnLoadCallback(drawBasic);

                function drawBasic() {
                    var total_contas = 0;
                    var meses = calcular_meses(data_inicial, data_final);

                    var dataArray = [['Conta', 'Custo/Cab/Mês'],];

                    for (var i = 0; i < contas_cabeca.length; i++) {
                        valor_conta = (contas_cabeca[i].valor/estoque_medio_periodo/meses).toFixed(2);

                        valor_conta = parseFloat(valor_conta);

                        if (valor_conta > 1) {
                            var row = [contas_cabeca[i].descricao, valor_conta];
                            total_contas+=valor_conta;
                            dataArray.push(row);
                        }
                    }
                    
                    var total_contas_edi = formatMoney(total_contas);
                    $("#custo_total").text('R$ ' + total_contas_edi);

                    var data = google.visualization.arrayToDataTable(dataArray);

                    var options = {
                        titlePosition: 'none',
                        backgroundColor: 'transparent',
                        fontName: 'Futura Std Light',
                        fontSize: 12,
                        chartArea: {
                            left:80,
                            bottom:25,  
                            top:5,                    
                                width: "60%",
                                height: "auto"
                        },

                        vAxis: {
                            textStyle : {
                                fontSize: 8,
                            },
                        }, 

                    };

                    var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                } 
                //FIM CUSTO POR CABEÇA (BARRAS) 

                $('#aguardar').modal('hide');
                $(".dashboard-cards").show();
            });

        } // FIM FUNÇÃO PROCESSAR_DADOS

        function readContas(local, data_inicial, data_final, codigo_cc) {
            return $.ajax({
                type: "GET",
                data: { local: local, data_inicial: data_inicial, data_final: data_final, codigo_cc: codigo_cc },
                url: "ler_contas_pagar_grafico_pizza_painel.php",
                async: false,
            }).responseText;
        }

        function readContasCabeca(local, data_inicial, data_final, codigo_cc) {
            return $.ajax({
                type: "GET",
                data: { local: local, data_inicial: data_inicial, data_final: data_final, codigo_cc: codigo_cc },
                url: "ler_contas_pagar_grafico_barras_painel.php",
                async: false,
            }).responseText;
        }

        function readGmd(local, data_inicial, data_final) {

/*, c001:c001, m001:m001, f001:f001, 
                    c002:c002, m002:m002, f002:f002,
                    c003:c003, m003:m003, f003:f003,
                    c004:c004, m004:m004, f004:f004,
                    c005:c005, m005:m005, f005:f005
                   */

        }

        function calcular_meses(dt1, dt2){
            var ano_mes = dt2.split("-");
            var mes_fim = ano_mes[1];
            var ano_fim = ano_mes[0];
            var data = new Date(ano_fim, mes_fim, 0);
            var ultimo_dia = data.getDate();

            dt1 = dt1 + '-01';
            dt2 = dt2 + '-' + ultimo_dia;

            var data1 = new Date(dt1); 
            var data2 = new Date(new Date(dt2));

            var total = (data2.getFullYear() - data1.getFullYear())*12 + (data2.getMonth() - data1.getMonth());
            //document.getElementById("result").value = total;
            return total;
        }        
    </script>

    <style type="text/css">
        @media (max-width: 767.98px) {
            .fc .fc-toolbar.fc-header-toolbar {
            display: block;
            text-align: center;
            }

            .fc-header-toolbar .fc-toolbar-chunk {
            display: block;
            }
        }

        @media (min-width: 400px) {
            /*#chart_div, #piechart {
                width: 90%;
            }*/
        }

        @media (max-width: 420px) {
            .margem_row {
                margin-top: 20px;
            }
        }

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
            font-size: .925rem;
            font-weight: 600;
            padding-left: 10px;
            padding-top: 10px;
        }

        table.dataTable thead th{
            border-bottom: 1px solid transparent;
            padding-bottom: 1px; 
            padding-top: 1px;
            font-weight: 600;        
        }

        table.dataTable tfoot th{
            border-top: 1px solid transparent;
        }

        table.dataTable {
            border: 1px solid transparent;
            font-weight: 600;
        }

        .chart-title {
            color: #6b6d6e;
            font-size: 16px;
            font-weight: 600;
            padding: 0;
            margin: 0;
            text-align: center;
            margin-top: 25px;
            margin-bottom: 25px;
            opacity: 0.8;
        }

        .margem_esquerda {
            margin-left: 2px;
        }
    </style>
</head>

<body>
    <!-- container section start -->
    <section id="container" class="">

        <?php
            include "cabecalho.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
            include "start_session.php";
        ?>

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper" style="margin-left: 5px;"> 
                <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i> 
                <span class="titulo">Painel Estratégico</span></span>

                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="page-header"><i class="fa fa-laptop"></i> 
                        Painel Estratégico</h3>
                    </div>
                </div>

                <div class="row">
                    <input type="hidden" id="expande_tela" value="S">

                    <div class="col-lg-12">
                        <div class="row col-md-12 card" id="consulta_contas">
                            <div class="row">  
                                <div class="form-group col-md-3" >
                                    <label for="data_inicial" class="control-label" style="font-size: 14px;"><span class="required">*</span> Data Incial</label>
                                    <input name="data_inicial" type="month" class="form-control" id="data_inicial" <?php //echo "value='".$data_inicial."'";?>>
                                </div>

                                <div class="form-group col-md-3"> 
                                    <label for="data_final" class="control-label" style="font-size: 14px;"><span class="required">*</span> Data Final</label>
                                    <input name="data_final" type="month" class="form-control" id="data_final" <?php //echo "value='".$data_final."'";?>>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="codigo_local" class="control-label" style="font-size: 14px;"><span class="required">*</span> Fazenda</label>
                                    <select class="form-control" id="codigo_local" name="codigo_local">
                                        <option value="000000000">Todas</option>
                                    <?php 
                                        while($reg_local = mysqli_fetch_object( $tbl_local)) { 
                                            foreach ($array_locais_usuario as $value) {
                                                $value = ltrim($value);
                                                $value = rtrim($value); 

                                                if ($value==$reg_local->tbl_pessoa_id) {
                                                    echo '<option value="'.$value.'">' .$reg_local->tbl_pessoa_nome. '</option>'; 
                                                }
                                            }
                                        } 
                                    ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-1 mais_filtros" hidden>
                                    <label class="control-label">&nbsp;</label>

                                    <p><a href="#" class="control-label" data-toggle='tooltip' data-placement='top' title="Mais Filtros" onclick="exibe_mais_filtros()"> 
                                    <i class="fas fa-filter"></i> +
                                    </a></p>
                                </div>

                                <div class="form-group col-md-1 menos_filtros" hidden>
                                    <label class="control-label">&nbsp;</label>

                                    <p><a href="#" class="control-label" data-toggle='tooltip' data-placement='top' title="Menos Filtros" onclick="exibe_menos_filtros()"> 
                                    <i class="fas fa-filter"></i> -
                                    </a></p>
                                </div>

                                <div class="form-group col-md-1">
                                    <label class="control-label">&nbsp;</label>

                                    <button type="button" class="form-control btn btn-primary pull-right" onclick="processar_dados()">Listar
                                    </button>
                                </div>

                                <div class="form-group col-md-1 menu_show" hidden>
                                    <p><a href="#" class="control-label btn pull-right" data-toggle='tooltip' data-placement='top' title="Exibir o Menu" onclick="voltar_menu()"> 
                                    <i class="fa fa-bars"></i> Menu
                                    </a></p>
                                </div>
                            </div> 

                            <div class="row exibe_mais_filtros">
                                <div class="form-group col-md-5">
                                    <label for="codigo_cc" class="control-label" style="font-size: 14px;">Centro de Custos</label>
                                    <select class="form-control" id="codigo_cc" name="codigo_cc">
                                    <?php 
                                        while($reg_cc = mysqli_fetch_object($tbl_cc)) { 
                                            echo '<option value="'.$reg_cc->tbl_cc_codigo_id.'">' .$reg_cc->tbl_cc_descricao. '
                                                </option>'; 
                                        } 
                                    ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-5">
                                    <label for="categoria" class="control-label" style="font-size: 14px;">Categoria para cálculo GMD Global</label>
                                    <select class="form-control"  id="categoria" name="categoria">
                                        <option value="000">Todas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="page-inner">
                    <div class="dashboard-cards"> 
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="card card-title">

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <span class="titulo_gray"> 
                                                ZOOTÉCNICO
                                            </span>
                                                
                                            <img src="img/categoria.png" class="img-categoria" alt="">
                                                
                                        </div>
                                    </div>

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <table class="" style="width:100%; font-size: 1.35rem; margin-top: 15px;">
                                                <tbody>
                                                    <tr> 
                                                        <td width="20%">Rebanho Total:</td>
                                                        <td id="total_unidade" width="20%" align="right" style="color: #548235 !important;"></td>
                                                        <td id="total_peso" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td id="total_arroba" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td width="20%"></td>

                                                    </tr>
                                                    <tr> 
                                                        <td width="20%">Rebanho Médio:</td>
                                                        <td id="media_unidade" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td id="media_peso" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td id="media_arroba" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td width="20%"></td>
                                                    </tr>
                                                    <tr> 
                                                        <td width="20%">Lotação Média:</td>
                                                        <td id="locacao_unidade" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td id="lotacao_media" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td id="lotacao_ua" width="20%" align="right" style="color: #548235 !important"></td>
                                                        <td width="20%"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <h2 class="chart-title">Lotação da Fazenda KgPc/Ha</h2>
                                            <div id="line_chart" style="height: 260px;">
                                            </div>
                                        </div>
                                    </div>

                                    <hr align="center">

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <table class="" style="width:100%; font-size: 1.35rem; margin-top: 5px; margin-bottom: 15px;">
                                                <tbody>
                                                    <tr> 
                                                        <td width="18%">GMD Global:</td>
                                                        <td id="gmd_global" width="18%" align="right" style="color: #548235 !important"></td>
                                                        <td width="5%"></td>
                                                        <td width="15%"></td>
                                                        <td width="15%"></td>
                                                        <td width="29%"></td>
                                                    </tr>

                                                    <tr>
                                                        <td width="18%">Produção Kg:</td>
                                                        <td id="producao_kg" width="18%" align="right" style="color: #548235 !important"></td>

                                                        <td width="5%"></td>

                                                        <td width="15%">Produção @:</td>
                                                        <td id="producao_arroba" width="15%" align="right" style="color: #548235 !important"></td>
                                                        <td width="29%"></td>
                                                    </tr>

                                                    <tr>
                                                        <td width="18%">Giro Estoque:</td>
                                                        <td id="giro_estoque" width="18%" align="right" style="color: #548235 !important">0</td>
                                                        <td width="5%"></td>
                                                        <td width="15%"></td>
                                                        <td width="15%"></td>
                                                        <td width="29%"></td>
                                                    </tr>

                                                    <tr> 
                                                        <td width="100%" colspan="6" id="filtro_gmd_global" style="font-size: 10px; padding-top: 10px;"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="card card-title">
                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <span class="titulo_gray"> 
                                                FINANCEIROS
                                            </span>

                                            <img src="img/financeiro.png" class="img_financeiro" alt="">
                                        </div>
                                    </div>

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <table class="" style="width:100%; font-size: 1.35rem; margin-top: 15px;">
                                                <tbody>
                                                    <tr> 
                                                        <td width="25%">Custo Total/Cab/Mês:</td>
                                                        <td id="custo_total" width="20%" align="Left" style="color: #548235 !important;"></td>
                                                        <td width="55%"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <h2 class="chart-title">Custo/Cabeça/Mês</h2>
                                            <div id="chart_div" style="height: 260px; padding: 0; margin: 0;">
                                            </div>
                                        </div>
                                    </div>

                                    <hr align="center">

                                    <div class="row margem_esquerda">
                                        <div class="col-md-12">
                                            <h2 class="chart-title" style="margin-top: 0;">% Gastos por Conta Contábil</h2>
                                            <div id="piechart" style="margin-top: 0; height: 380px; padding: 0;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

                <div class="modal fade" id="mensagem_erro" tabindex="-1" role="dialog" 
                    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Relatórios Estratégicos - Mensagem</h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button" >Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="categoria_gmd" tabindex="-1" role="dialog" 
                    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Selecione Categorias para Cálculo do GMD Glocal</h4>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="categorias_gmd" style="width:100%;">
                                            <tbody>
                                                <tr> 
                                                    <td width="28%">
                                                        <input type="checkbox" id="c001">&nbsp;&nbsp;00 a 07 meses
                                                    </td>
                                                    <td width="20%" class="sexo1">
                                                        <input type="checkbox" id="M001">&nbsp;&nbsp;Macho

                                                        <input type="checkbox" id="F001">&nbsp;&nbsp;Fêmea
                                                    </td>
                                                    <td width="52%"></td>
                                                </tr>
                                                <tr> 
                                                    <td width="28%">
                                                        <input type="checkbox" id="c002">&nbsp;&nbsp;
                                                        08 a 12 meses
                                                    </td>
                                                    <td width="20%" class="sexo2">
                                                        <input type="checkbox" id="M002">&nbsp;&nbsp;Macho

                                                        <input type="checkbox" id="F002">&nbsp;&nbsp;Fêmea
                                                    </td>
                                                    <td width="52%"></td>
                                                </tr>
                                                <tr> 
                                                    <td width="28%">
                                                        <input type="checkbox" id="c003">&nbsp;&nbsp;
                                                        13 a 24 meses
                                                    </td>
                                                    <td width="20%" class="sexo3">
                                                        <input type="checkbox" id="M003">&nbsp;&nbsp;Macho

                                                        <input type="checkbox" id="F003">&nbsp;&nbsp;Fêmea
                                                    </td>
                                                    <td width="52%"></td>
                                                </tr>
                                                <tr> 
                                                    <td width="28%">
                                                        <input type="checkbox" id="c004">&nbsp;&nbsp;
                                                        25 a 36 meses
                                                    </td>
                                                    <td width="20%" class="sexo4">
                                                        <input type="checkbox" id="M004">&nbsp;&nbsp;Macho

                                                        <input type="checkbox" id="F004">&nbsp;&nbsp;Fêmea
                                                    </td>
                                                    <td width="52%"></td>
                                                </tr>
                                                <tr> 
                                                    <td width="28%">
                                                        <input type="checkbox" id="c005">&nbsp;&nbsp;
                                                        > 36 meses
                                                    </td>
                                                    <td width="20%" class="sexo5">
                                                        <input type="checkbox" id="M005">&nbsp;&nbsp;Macho

                                                        <input type="checkbox" id="F005">&nbsp;&nbsp;Fêmea
                                                    </td>
                                                    <td width="52%"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary" onclick="gerar_categoria()" type="button">Confirmar
                                </button>
                                <button data-dismiss="modal" class="btn btn-info pull-right" type="button">Voltar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </section> <!--main content end-->
        </section>   <!-- container section start -->

        <div class="text-center">
            <div class="credits">
                <font size="2"><p style="color:#C0C0C0">Copyright &copy; Boi Virtual 2024</p></font>
            </div>
        </div>

    </section> <!-- container section start end -->

    <!--<script src="js/jquery.js?<?php echo Versao; ?>"></script>
    <script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
    <script src="js/jquery-ui-1.9.2.custom.min.js?<?php echo Versao; ?>"></script>
    <script src="js/scripts.js?<?php echo Versao; ?>"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.22/r-2.2.6/datatables.min.js" type="text/javascript" ></script>
    <script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>
    <script src='js/jquery.redirect.js'></script>
    <script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>-->

    <script src="js/jquery.js?<?php echo Versao; ?>"></script>
    <script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
    <script src="js/scripts.js?<?php echo Versao; ?>"></script>
    <script src="js/relatorios_estrategicos.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
    <script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
    </script>

    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
    " charset="utf-8" type="text/javascript" >
    </script>
    <script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>


    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip({html:true}); 
        });
    </script>




</body>

</html>
