<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

    $local_filtro = $_REQUEST["local"];

    $local= array();
    $matriz_itens = explode(",", $local_filtro);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal = '';
    $wlocal_animais = '';
    $wlocal_animais_pasto = '';

    if ($local_filtro!='') {
        $wlocal = " AND tbl_fechamento_local IN(";
        $wlocal.= $local;
        $wlocal.= ")";

        $wlocal_animais = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal_animais.= $local;
        $wlocal_animais.= ")";

        $wlocal_animais_pasto = " WHERE tbl_animal_pasto_local IN(";
        $wlocal_animais_pasto.= $local;
        $wlocal_animais_pasto.= ")";
    }

    $data_hoje=new DateTime();
    $mes_hoje=$data_hoje->format('m');
    $ano_hoje=$data_hoje->format('Y');

    $data_inicial = $_REQUEST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $mes_inicial = $partes[1];
    $ano_inicial = $partes[0];

    $data_final = $_REQUEST['data_final'];
    $partes = explode("-", $data_final);
    $mes_final = $partes[1];
    $ano_final = $partes[0];

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));

    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

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
    } 

    $tipo_rel= $_REQUEST["tipo_rel"];
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
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php"; include "limpar_secao_compra_venda.php"; include "limpar_secao_ctp.php"; include "limpar_secao_ctr.php"; include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_produtivos.php">Relatórios Produtivos</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Estoque de Animais</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Estoque de Animais</h3>
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

                                                <input type="hidden" id="codigo_local_estoque"
                                                    <?php echo "value='".$local_filtro."'";?>>

                                                <input type="hidden" id="tipo_rel"
                                                    <?php echo "value='".$tipo_rel."'";?>>

                                                <input type="hidden" id="descricao_filtro"
                                                    <?php echo "value='".$descricao_filtro."'";?>>

                                                <input type="hidden" id="data_inicial"
                                                    <?php echo "value='".$data_inicial."'";?>>

                                                <input type="hidden" id="data_final"
                                                    <?php echo "value='".$data_final."'";?>>

                                                <div class="row">
                                                <div class="col-md-8">
                                                    <label class="label_consulta_rel">Filtros:</label>
                                                    <span><?php echo $descricao_filtro;?></span>
                                                </div>

                                                <div class="col-md-4">
                                                <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_estoque()">Voltar
                                                </button>

                                                <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                onClick="lista_estoque_excel()">Excel</button>
                                                </div>
                                            </div>

                                            <div class="row">  
                                                <div class="col-md-6">
                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_estoque" value="C" class="tipo_estoque_relatorio" checked 
                                                      > Lista por Cabeças
                                                    </label>

                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_estoque" value="P" class="tipo_estoque_relatorio"> Lista por Kg (peso)
                                                    </label>
                                                </div>
                                            </div>

                                            <hr align="center"> 
<table class="table table-bordered table-striped table-advance table-hover" id="tabela_lista_estoque" width="100%" style="font-size: 11px;">

<tbody>
<?php
    $data_inicial = $data_inicial . '-01';
    $data_final = $data_final . '-31';

    $total_final = 0;
    $media_final = 0;

    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'");
    $num_rows_cat = mysqli_num_rows($categoria);    

    if ($num_rows_cat!=0) {
        while ($reg_categoria = mysqli_fetch_object($categoria)) {
            $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
            $qtd_media_femea[$id_categoria] = 0;
            $valor_media_femea[$id_categoria] = 0;
            $total_media_femea[$id_categoria] = 0;            

            $qtd_media_macho[$id_categoria] = 0;
            $valor_media_macho[$id_categoria] = 0;
            $total_media_macho[$id_categoria] = 0;            
        }
    }

    for ($i=0; $i<($qtd_meses); $i++) { 
        $mes_lista = $array_mes[$i];
        $ano_lista = $array_ano[$i];

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");
            $num_rows_cat = mysqli_num_rows($categoria);    

        if ($num_rows_cat!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
                $qtd_femea[$id_categoria] = 0;
                $qtd_macho[$id_categoria] = 0;
            }
        }

        $total = 0;

        $fechamento= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
            WHERE year(tbl_fechamento_data)='$ano_lista' AND 
                  month(tbl_fechamento_data)='$mes_lista'" . $wlocal);

        $num_rows = mysqli_num_rows($fechamento);

        if ($num_rows!=0) {
            while ($reg_fec = mysqli_fetch_object($fechamento)) {
                $sexo = $reg_fec->tbl_fechamento_sexo; 
                $qtd = $reg_fec->tbl_fechamento_qtd;
                $categoria = $reg_fec->tbl_fechamento_categoria;

                if ($sexo=='M') {
                    $qtd_macho[$categoria]+=$qtd;
                    $total+=$qtd;
                } 
                else {
                    $qtd_femea[$categoria]+=$qtd;
                    $total+=$qtd;
                }

                $total_final+=$qtd;
            }
        }

        for ($k=1; $k <=5; $k++) { 

            $index = str_pad($k , 3 , '0' , STR_PAD_LEFT);

            if ($qtd_femea[$index]==0) {
                $qtd_femea[$index]='';
            }
            else {
                $qtd_media_femea[$index]++;
                $valor_media_femea[$index]+=$qtd_femea[$index];
            }

            if ($qtd_macho[$index]==0) {
                $qtd_macho[$index]='';
            }
            else {
                $qtd_media_macho[$index]++;
                $valor_media_macho[$index]+=$qtd_macho[$index];
            }
        }


        if (($mes_hoje!=$mes_lista || $ano_hoje!=$ano_lista || $qtd_meses==1) && $total!=0) {
            echo '<tr>';
            echo '<td width="12%" class="text-right">'.$array_mes_extenco[$i].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_femea['001'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_femea['002'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_femea['003'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_femea['004'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_femea['005'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_macho['001'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_macho['002'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_macho['003'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_macho['004'].'</td>';
            echo '<td width="8%" class="text-right">'.$qtd_macho['005'].'</td>';
            echo '<td width="8%" class="text-right">'.$total.'</td>';
            echo '</tr>';
        }
    }


    // calcular estoque do ultimo mes se for o mes atual

    if ($mes_hoje==$mes_lista && $ano_hoje==$ano_lista) {
        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");
            $num_rows_cat = mysqli_num_rows($categoria);    

        if ($num_rows_cat!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
                $qtd_femea[$id_categoria] = 0;
                $qtd_macho[$id_categoria] = 0;
            }
        }

        $total = 0;

        if ($controle_estoque=='I') {
            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_lixeira=0 AND tbl_animal_ativo='S'" . $wlocal_animais);

            $num_rows = mysqli_num_rows($tbl_animais);  
            if ($num_rows!=0) {
                while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                    $data_nasc = $reg_animal->tbl_animal_data_nascimento;
                    $sexo = $reg_animal->tbl_animal_sexo;
            
                    $data_nascimento = $data_nasc;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade<0) {
                        $idade=1;
                    }

                    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");

                    $num_rows = mysqli_num_rows($tbl_categoria);    

                    if ($num_rows!=0) {
                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                                if ($sexo=='M'){
                                    $qtd_macho[$codigo_categoria]++;
                                    $total++;
                                }
                                else {
                                    $qtd_femea[$codigo_categoria]++;
                                    $total++;
                                }

                                $total_final++;

                            }
                        }
                    }
                }
            }
        }
        else {
            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                " . $wlocal_animais_pasto);

            $num_rows = mysqli_num_rows($tbl_animais);  
            if ($num_rows!=0) {
                while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                    $data_nasc = $reg_animal->tbl_animal_pasto_nascimento;
                    $sexo = $reg_animal->tbl_animal_pasto_sexo;
            
                    $data_nascimento = $data_nasc;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    if ($idade<0) {
                        $idade=1;
                    }

                    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");

                    $num_rows = mysqli_num_rows($tbl_categoria);    

                    if ($num_rows!=0) {
                        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                                if ($sexo=='M'){
                                    $qtd_macho[$codigo_categoria]++;
                                    $total++;
                                }
                                else {
                                    $qtd_femea[$codigo_categoria]++;
                                    $total++;
                                }

                                $total_final++;
                            }
                        }
                    }
                }
            }
        }

        for ($k=1; $k <=5; $k++) { 

            $index = str_pad($k , 3 , '0' , STR_PAD_LEFT);

            if ($qtd_femea[$index]==0) {
                $qtd_femea[$index]='';
            }
            else {
                $qtd_media_femea[$index]++;
                $valor_media_femea[$index]+=$qtd_femea[$index];
            }

            if ($qtd_macho[$index]==0) {
                $qtd_macho[$index]='';
            }
            else {
                $qtd_media_macho[$index]++;
                $valor_media_macho[$index]+=$qtd_macho[$index];
            }
        }

        echo '<tr>';
        echo '<td width="12%" class="text-right">'.$array_mes_extenco[$i-1].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_femea['001'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_femea['002'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_femea['003'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_femea['004'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_femea['005'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_macho['001'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_macho['002'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_macho['003'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_macho['004'].'</td>';
        echo '<td width="8%" class="text-right">'.$qtd_macho['005'].'</td>';
        echo '<td width="8%" class="text-right">'.$total.'</td>';
        echo '</tr>';

    }
                                    

    echo '<script type="text/javascript">
        $("#aguardar").modal("hide");
      </script>
    ';
?>

</tbody>

<thead>
    <tr>
        <th width="10%" class="text-center" rowspan="2" style="vertical-align: middle;text-align:center;">Meses</th>
        <th width="40%" class="text-center" colspan="5">Fêmeas</th>
        <th width="40%" class="text-center" colspan="5">Machos</th>
        <th width="10%" class="text-center"rowspan="2" style="vertical-align: middle;text-align:center;">Totais</th>
    </tr>

    <tr>
        <th width="8%" class="text-center">0 a 7 meses</th>
        <th width="8% "class="text-center">8 a 12 meses</th>
        <th width="8% "class="text-center">13 a 24 meses</th>
        <th width="8%" class="text-center">25 a 36 meses</th>
        <th width="8% "class="text-center">> 36 meses</th>
        <th width="8%" class="text-center">0 a 7 meses</th>
        <th width="8% "class="text-center">8 a 12 meses</th>
        <th width="8% "class="text-center">13 a 24 meses</th>
        <th width="8%" class="text-center">25 a 36 meses</th>
        <th width="8% "class="text-center">> 36 meses</th>
    </tr>
</thead>

<tfoot>
<tr>

<?php    
    for ($k=1; $k <=5; $k++) { 
        $index = str_pad($k , 3 , '0' , STR_PAD_LEFT);

        if ($valor_media_femea[$index]!=0 && $qtd_media_femea[$index]!=0) {
            $total_media_femea[$index]=$valor_media_femea[$index]/$qtd_media_femea[$index];
        }

        if ($valor_media_macho[$index]!=0 && $qtd_media_macho[$index]!=0) {
            $total_media_macho[$index]=$valor_media_macho[$index]/$qtd_media_macho[$index];
        }
    }

    $media_final = $total_final/$qtd_meses;

    echo '<th style="vertical-align: middle;text-align:center;">Média do Período</th>';            
    echo '<th class="text-right">'.number_format($total_media_femea['001'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_femea['002'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_femea['003'],2,',','.').'</th>';         
    echo '<th class="text-right">'.number_format($total_media_femea['004'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_femea['005'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_macho['001'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_macho['002'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_macho['003'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_macho['004'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($total_media_macho['005'],2,',','.').'</th>';
    echo '<th class="text-right">'.number_format($media_final,2,',','.').'</th>';

?>
</tfoot>    

</table>
                                                                                    
<?php
    if ($qtd_meses>4) {
        echo '<hr align="center">'; 
        echo '<div class="row">';  
        echo '<button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_estoque()">Voltar</button>';
        echo '<button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" onClick="lista_estoque_excel()">Excel</button>';
        echo '</div>';
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

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Estoque Animal</h4>
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
                            <h4 class="modal-title">Estoque Animal - Mensagem</h4>
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




