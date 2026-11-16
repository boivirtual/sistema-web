
<?php
    include "valida_sessao.inc";

    $tipo_relatorio = $_REQUEST["tipo"];
    $data_sistema = date("Y-m-d");
    $data_inicial = $_REQUEST['data_inicial'];
    $data_final = $_REQUEST['data_final'];
    $descricao_filtro= $_REQUEST["descricao_filtro"];
    $local_filtro = $_REQUEST["local"];
    $categoria_filtro = $_REQUEST["categoria"];
    $sexo_filtro = $_REQUEST["sexo"];

    $wsexo_pesagem='';

    if ($sexo_filtro!='Todos') {
        $wsexo_pesagem = " AND tbl_ite_pesagem_sexo IN(";
        $wsexo_pesagem .= "'" . $sexo_filtro . "'";
        $wsexo_pesagem.= ")";
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
        $wcategoria = " AND tbl_ite_pesagem_categoria IN(";
        $wcategoria.= $categoria;
        $wcategoria.= ")";
    }

    @ session_start(); 

    //$_SESSION['local_pesagem']=$array_fazenda;
    $_SESSION['data_inicial_pesagem']=$data_inicial; 
    $_SESSION['data_final_pesagem']=$data_final; 
    $_SESSION['local_pesagem_rel']=$local_filtro;
    $_SESSION['array_categoria_pesagem_rel']=$categoria;
    $_SESSION['sexo_pesagem_rel']=$sexo_filtro;

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
        .label_categoria_sexo{
            font-weight: 600;
            text-align: left !important;
        }
    </style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; 
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Histórico de Pesagem</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-weight"></i> Histórico de Pesagem</h3>
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

                                                <input type="hidden" id="tipo_relatorio" <?php echo "value='".$tipo_relatorio."'";?>>

                                                <input type="hidden" id="data_inicial"
                                                    <?php echo "value='".$data_inicial."'";?>>

                                                <input type="hidden" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>
                                                    
                                                <input type="hidden" id="codigo_local"
                                                    <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="codigo_categoria"
                                                    <?php echo "value='".$categoria_filtro."'";?>>

                                                <input type="hidden" id="sexo"
                                                    <?php echo "value='".$sexo_filtro."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <label class="label_consulta_rel">Filtros:</label>
                                                        <span><?php echo $descricao_filtro;?></span>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_historico_pesagem_lote()">Voltar
                                                        </button>

                                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                        onClick="lista_historico_pesagem_lote_excel()">Excel</button>
                                                    </div>
                                                </div>

<?php
    include "conecta_mysql.inc";

    //$categoria_anterior = 0;
    //$sexo_anterior = '';
    $chave_anterior = '';
    $chave_cat_sexo_anterior = '';

    $sql = "SELECT * FROM tbl_item_pesagem
        INNER JOIN tbl_pesagem 
                ON tbl_pesagem_id = tbl_ite_pesagem_numero_id 
        WHERE tbl_pesagem_data>='$data_inicial' AND 
              tbl_pesagem_data<='$data_final' AND 
              tbl_pesagem_codigo_local='$local_filtro' AND 
              tbl_ite_pesagem_peso!=0" . 
              $wsexo_pesagem . $wcategoria . 
        " ORDER BY tbl_ite_pesagem_categoria ASC, 
                   tbl_ite_pesagem_sexo ASC,
                   tbl_ite_pesagem_data_emissao ASC, 
                   tbl_pesagem_codigo_epoca ASC";

    $tbl_item_pesagem = mysqli_query($conector, $sql);
    $num_rows_pesagem = mysqli_num_rows($tbl_item_pesagem);  

    if ($num_rows_pesagem!=0) {
        while ($reg_item = mysqli_fetch_object($tbl_item_pesagem)) {
            $data_peso = new DateTime($reg_item->tbl_ite_pesagem_data_emissao);
            $data_edi = $data_peso->format('d/m/Y');
            $sexo = $reg_item->tbl_ite_pesagem_sexo;
            $categoria = $reg_item->tbl_ite_pesagem_categoria;
            $qtd_animais = $reg_item->tbl_ite_pesagem_qtd_animais;
            $peso_medio = $reg_item->tbl_ite_pesagem_peso_medio;
            $codigo_epoca = $reg_item->tbl_pesagem_codigo_epoca;

            $chave = $categoria.$sexo.$data_peso->format('Y').$data_peso->format('m').
                     $data_peso->format('d').$codigo_epoca;

            $chave_cat_sexo = $categoria.$sexo;

            $tbl_categoria = mysqli_query($conector,"SELECT * FROM tabela_categoria_idade
                WHERE tab_codigo_categoria_idade ='$categoria'");

            $num_rows_categoria = mysqli_num_rows($tbl_categoria);  

            if ($num_rows_categoria!=0) {
                $reg_categoria = mysqli_fetch_object($tbl_categoria);

                if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                    $desc_categoria = '> 36 meses';
                }
                else {
                    $desc_categoria = $reg_categoria->tab_categoria_idade_de.' a '.
                    $reg_categoria->tab_categoria_idade_ate.' meses';
                }
            }
            else {
                $desc_categoria = 'Sem categoria';
            }

            $tbl_epoca_pesagem = mysqli_query($conector,"SELECT * FROM tabela_epoca_pesagem
                WHERE tab_codigo_epoca_pesagem  ='$codigo_epoca'");

            $num_rows_epoca = mysqli_num_rows($tbl_epoca_pesagem);  

            if ($num_rows_epoca!=0) {
                $reg_epoca = mysqli_fetch_object($tbl_epoca_pesagem);
                $desc_epoca=$reg_epoca->tab_descricao_epoca_pesagem;
            }
            else {
                $desc_epoca = '';
            }

            $tbl_peso_medio = mysqli_query($conector,"SELECT * FROM tbl_peso_medio_categoria
                WHERE tbl_pm_categoria_id='$categoria' AND 
                      tbl_pm_sexo='$sexo' AND 
                      tbl_pm_local_id='$local_filtro'");

            $num_rows_peso_medio = mysqli_num_rows($tbl_peso_medio);  

            if ($num_rows_peso_medio!=0) {
                $reg_peso_medio = mysqli_fetch_object($tbl_peso_medio);
                $peso_medio_atual=$reg_peso_medio->tbl_pm_peso_medio_atual;
            }
            else {
                $peso_medio_atual = 0;
            }
            
            if ($sexo=='M') {
                $sexo='Macho';
            }
            else {
                $sexo='Fêmea';
            }
            
            if ($chave_cat_sexo!=$chave_cat_sexo_anterior) {
                if ($chave_cat_sexo_anterior=='') {
                    $chave_cat_sexo_anterior=$chave_cat_sexo;
                    $chave_anterior=$chave;
                    $data_anterior = $data_edi;
                    $epoca_anterior = $desc_epoca;
                    $qtd_animais_anterior = $qtd_animais;
                    $peso_medio_anterior = $peso_medio*$qtd_animais;

                    echo '
                        <div class="row">
                            <div class="col-md-2" style="font-size: 13px;">
                                <label class="label_categoria_sexo">Categoria:&nbsp;</label>
                                <span>'.$desc_categoria.'</span>
                            </div>

                            <div class="col-md-4" style="font-size: 13px;">
                                <label class="label_categoria_sexo">Sexo:&nbsp;</label>
                                <span>'.$sexo.'</span>

                                <span>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>

                                <label class="label_categoria_sexo">Peso Médio Atual:&nbsp;</label>
                                <span>'.number_format($peso_medio_atual,2,',','.').'</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">';

                            $cabecalho = imprimir_cabecalho();
                            echo '<tbody>';
                }
                else {

                    $peso_medio_anterior = $peso_medio_anterior/$qtd_animais_anterior;

                    echo '<tr>';
                    echo '<td width="16%" style="text-align: center;">'.$data_anterior.'</td>';
                    echo '<td width="16%" style="text-align: center;">'.$qtd_animais_anterior.'</td>';
                    echo '<td width="26%" style="text-align: center;">'.$epoca_anterior.'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio_anterior,2,',','.').'</td>';
                    echo '</tr>';

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div></div>';

                    $chave_anterior=$chave;
                    $chave_cat_sexo_anterior=$chave_cat_sexo;
                    $data_anterior = $data_edi;
                    $epoca_anterior = $desc_epoca;
                    $qtd_animais_anterior = $qtd_animais;
                    $peso_medio_anterior = $peso_medio*$qtd_animais;

                    echo '
                    <div class="row">
                        <div class="col-md-2" style="font-size: 13px;">
                            <label class="label_categoria_sexo">Categoria:&nbsp;</label>
                            <span>'.$desc_categoria.'</span>
                        </div>

                        <div class="col-md-4" style="font-size: 13px;">
                            <label class="label_categoria_sexo">Sexo:&nbsp;</label>
                            <span>'.$sexo.'</span>

                            <span>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>

                            <label class="label_categoria_sexo">Peso Médio Atual:&nbsp;</label>
                            <span>'.number_format($peso_medio_atual,2,',','.').'</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">';
                        $cabecalho = imprimir_cabecalho();
                        echo '<tbody>';
                }
            }
            else 
                if ($chave!=$chave_anterior) {
                    $peso_medio_anterior = $peso_medio_anterior/$qtd_animais_anterior;

                    echo '<tr>';
                    echo '<td width="16%" style="text-align: center;">'.$data_anterior.'</td>';
                    echo '<td width="16%" style="text-align: center;">'.$qtd_animais_anterior.'</td>';
                    echo '<td width="26%" style="text-align: center;">'.$epoca_anterior.'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio_anterior,2,',','.').'</td>';
                    echo '</tr>';

                    $chave_anterior=$chave;
                    $data_anterior = $data_edi;
                    $epoca_anterior = $desc_epoca;
                    $qtd_animais_anterior = $qtd_animais;
                    $peso_medio_anterior = $peso_medio*$qtd_animais;
                }
                else {
                    $qtd_animais_anterior+= $qtd_animais;
                    $peso_medio_anterior+= $peso_medio*$qtd_animais;
                }

/*            if ($categoria!=$categoria_anterior) {
                if ($categoria_anterior==0) {
                    $categoria_anterior=$categoria;
                    $sexo_anterior=''; 

                    if ($sexo!=$sexo_anterior) {
                        if ($sexo_anterior=='') {
                            $sexo_anterior=$sexo;

                            echo '
                            <div class="row">
                                <div class="col-md-3" style="font-size: 14px;">
                                    <label class="label_categoria_sexo">Categoria:&nbsp;</label>
                                    <span>'.$desc_categoria.'</span>
                                </div>

                                <div class="col-md-2" style="font-size: 14px;">
                                    <label class="label_categoria_sexo">Sexo:&nbsp;</label>
                                    <span>'.$sexo_anterior.'</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">';

                            $cabecalho = imprimir_cabecalho();

                            echo '<tbody>';
                            echo '<tr>';
                            echo '<td width="16%" style="text-align: center;">'.$data_edi.'</td>';
                            echo '<td width="16%" style="text-align: center;">'.$qtd_animais.'</td>';
                            echo '<td width="26%" style="text-align: center;">'.$desc_epoca.'</td>';
                            echo '<td width="14%" style="text-align: center;">'.number_format($anterior,2,',','.').'</td>';
                            echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio,2,',','.').'</td>';
                            echo '<td width="14%" style="text-align: center;">'.number_format($atual,2,',','.').'</td>';
                            echo '</tr>';
                        }
                    }
                }
                else {
                    $categoria_anterior=$categoria;
                    $sexo_anterior='';

                    if ($sexo!=$sexo_anterior) {
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div></div>';

                        $sexo_anterior=$sexo;

                        echo '
                        <div class="row">
                            <div class="col-md-3" style="font-size: 14px;">
                                <label class="label_categoria_sexo">Categoria:&nbsp;</label>
                                <span>'.$desc_categoria.'</span>
                            </div>

                            <div class="col-md-2" style="font-size: 14px;">
                                <label class="label_categoria_sexo">Sexo:&nbsp;</label>
                                <span>'.$sexo_anterior.'</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">';

                        $cabecalho = imprimir_cabecalho();

                        echo '<tbody>';
                        echo '<tr>';
                        echo '<td width="16%" style="text-align: center;">'.$data_edi.'</td>';
                        echo '<td width="16%" style="text-align: center;">'.$qtd_animais.'</td>';
                        echo '<td width="26%" style="text-align: center;">'.$desc_epoca.'</td>';
                        echo '<td width="14%" style="text-align: center;">'.number_format($anterior,2,',','.').'</td>';
                        echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio,2,',','.').'</td>';
                        echo '<td width="14%" style="text-align: center;">'.number_format($atual,2,',','.').'</td>';
                        echo '</tr>';
                    }
                    else {
                        echo '<tr>';
                        echo '<td width="16%" style="text-align: center;">'.$data_edi.'</td>';
                        echo '<td width="16%" style="text-align: center;">'.$qtd_animais.'</td>';
                        echo '<td width="26%" style="text-align: center;">'.$desc_epoca.'</td>';
                        echo '<td width="14%" style="text-align: center;">'.number_format($anterior,2,',','.').'</td>';
                        echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio,2,',','.').'</td>';
                        echo '<td width="14%" style="text-align: center;">'.number_format($atual,2,',','.').'</td>';
                        echo '</tr>';
                    }
                }
            }
            else {
                if ($sexo!=$sexo_anterior) {
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div></div>';

                    $sexo_anterior=$sexo;

                    echo '
                    <div class="row">
                        <div class="col-md-3" style="font-size: 14px;">
                            <label class="label_categoria_sexo">Categoria:&nbsp;</label>
                            <span>'.$desc_categoria.'</span>
                        </div>

                        <div class="col-md-2" style="font-size: 14px;">
                            <label class="label_categoria_sexo">Sexo:&nbsp;</label>
                            <span>'.$sexo_anterior.'</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">';

                    $cabecalho = imprimir_cabecalho();

                    echo '<tbody>';
                    echo '<tr>';
                    echo '<td width="16%" style="text-align: center;">'.$data_edi.'</td>';
                    echo '<td width="16%" style="text-align: center;">'.$qtd_animais.'</td>';
                    echo '<td width="26%" style="text-align: center;">'.$desc_epoca.'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($anterior,2,',','.').'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio,2,',','.').'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($atual,2,',','.').'</td>';
                    echo '</tr>';
                }
                else {
                    echo '<tr>';
                    echo '<td width="16%" style="text-align: center;">'.$data_edi.'</td>';
                    echo '<td width="16%" style="text-align: center;">'.$qtd_animais.'</td>';
                    echo '<td width="26%" style="text-align: center;">'.$desc_epoca.'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($anterior,2,',','.').'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio,2,',','.').'</td>';
                    echo '<td width="14%" style="text-align: center;">'.number_format($atual,2,',','.').'</td>';
                    echo '</tr>';
                }
            }*/
        } // Fim while item pesagem
    } // Fim if item pesagem

    $peso_medio_anterior = $peso_medio_anterior/$qtd_animais_anterior;

    echo '<tr>';
    echo '<td width="16%" style="text-align: center;">'.$data_anterior.'</td>';
    echo '<td width="16%" style="text-align: center;">'.$qtd_animais_anterior.'</td>';
    echo '<td width="26%" style="text-align: center;">'.$epoca_anterior.'</td>';
    echo '<td width="14%" style="text-align: center;">'.number_format($peso_medio_anterior,2,',','.').'</td>';
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
    echo '</div></div>';

    echo '<script type="text/javascript">$("#aguardar").modal("hide");</script>';
   
?>

<?php
    function imprimir_cabecalho(){
        /*echo '

        <table id="tabela_gmd_geral" class="table table-bordered table-advance table-hover" style="width:50%; font-size:11px; float: left;">

        <thead>
            <tr>
                <th colspan="3"></th>
                <th colspan="3" style="text-align: center">Peso Médio/Cabeça</th>
            </tr>

            <tr>
                <th style="text-align: center">Data</th>
                <th style="text-align: center">Qtd Animais</th>
                <th style="text-align: center">Motivo da Pesagem</th>
                <th style="text-align: center">Anterior</th>
                <th style="text-align: center">Digitado</th>
                <th style="text-align: center">Atual</th>
            </tr>
        </thead>';
        */

        echo '

        <table id="tabela_gmd_geral" class="table table-bordered table-advance table-hover" style="width:40%; font-size:11px; float: left;">

        <thead>
            <tr>
                <th style="text-align: center">Data</th>
                <th style="text-align: center">Qtd Animais</th>
                <th style="text-align: center">Motivo da Pesagem</th>
                <th style="text-align: center">Peso Médio</th>
            </tr>
        </thead>';
    }

?>


                                            </div> <!-- fim container -->
                                        </div> <!-- dados-->
                                    </div> <!--tab-content -->

                                </div> <!--panel-body -->
                            </div> <!--panel -->      
                        </form>
                    </section> <!-- panel-group -->
                </div> <!--col-lg-12 2-->
            </div> <!--row 2-->
        </section> <!-- wrapper -->
    </section><!--main-content -->

<?php 
  $javascript_file_name = 'pesagem.js';
  require 'rodape.php';
?>