
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

    $wlocal = "";

    if ($local_filtro!='') {
        $wlocal = " AND tbl_mov_estoque_local IN(";
        $wlocal.= $local;
        $wlocal.= ")";
}

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
                                                      <input type="radio" name="tipo_estoque" class="tipo_estoque_relatorio" value="C" checked 
                                                      > Lista por Cabeças
                                                    </label>

                                                    <label class="radio-inline">
                                                      <input type="radio" name="tipo_estoque" class="tipo_estoque_relatorio" value="P"> Lista por Kg (peso)
                                                    </label>
                                                </div>
                                            </div>

                                            <hr align="center"> 

<table class="table table-bordered table-striped table-advance table-hover" id="tabela_lista_estoque" width="100%" style="font-size: 12px;">

<tbody>
<?php
    $estoque_final = 0;
    $estoque_inicial = 0;
    $estoque_ent_nasc = 0;
    $estoque_ent_compra = 0;
    $estoque_ent_transf = 0;
    $estoque_ent_outra = 0;
    $estoque_sai_morte = 0;
    $estoque_sai_venda = 0;
    $estoque_sai_transf = 0;
    $estoque_sai_outra = 0;

    $total_ent_nasc = 0;
    $total_ent_compra = 0;
    $total_ent_transf = 0;
    $total_ent_outra = 0;
    $total_sai_morte = 0;
    $total_sai_venda = 0;
    $total_sai_transf = 0;
    $total_sai_outra = 0;
    $total_meses = 0;
    //$media_final = 0;

    $data_inicial = $data_inicial . '-01';
    $data_final = $data_final . '-31';

                // Pega estoque anterior a data inicial sem nascimento
                $movimentacao_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                    WHERE tbl_mov_estoque_data_emissao<'$data_inicial' AND 
                          tbl_mov_estoque_tipo_movimentacao!='N' AND 
                          tbl_mov_estoque_tipo_movimentacao!='A' AND
                          tbl_mov_estoque_tipo_movimentacao!='B'" . $wlocal);

                $num_rows = mysqli_num_rows($movimentacao_estoque);

                if ($num_rows!=0) {
                    while ($reg_mov = mysqli_fetch_object($movimentacao_estoque)) {
                        $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                        $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;
                        $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

                        if ($codigo_id_animal!=999999999) {
                            if ($ent_sai=='E') {
                                if ($tipo=='C') {
                                    $estoque_ent_compra++;
                                }
                                else if ($tipo=='T') {
                                    $estoque_ent_transf++;
                                }
                                else {
                                    $estoque_ent_outra++;
                                }
                            }
                            else {
                                if ($tipo=='M') {
                                    $estoque_sai_morte++;   
                                }
                                else if ($tipo=='V') {
                                    $estoque_sai_venda++;
                                }
                                else if ($tipo=='T') {
                                    $estoque_sai_transf++;
                                }
                                else {
                                    $estoque_sai_outra++;
                                }
                            }

                        }
                    }
                }

                // Pega estoque anterior a data inicial nascimento
                $movimentacao_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                    WHERE tbl_mov_estoque_nascimento<'$data_inicial' AND 
                          tbl_mov_estoque_tipo_movimentacao='N'" . $wlocal);

                $num_rows = mysqli_num_rows($movimentacao_estoque);

                if ($num_rows!=0) {
                    while ($reg_mov = mysqli_fetch_object($movimentacao_estoque)) {
                        $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                        $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;
                        $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

                        if ($codigo_id_animal!=999999999) {
                            if ($ent_sai=='E') {
                                if ($tipo=='N') {
                                    $estoque_ent_nasc++;   
                                }
                            }
                        }
                    }
                }

                $estoque_inicial = $estoque_ent_nasc + $estoque_ent_compra + 
                                   $estoque_ent_transf + $estoque_ent_outra;

                $estoque_inicial = $estoque_inicial - $estoque_sai_morte - 
                                   $estoque_sai_venda - $estoque_sai_transf -
                                   $estoque_sai_outra;

                // Fim estoque anterior 

                for ($i=0; $i<$qtd_meses; $i++) { 

                    $mes_lista = $array_mes[$i];
                    $ano_lista = $array_ano[$i];

                    $estoque_ent_nasc = 0;
                    $estoque_ent_compra = 0;
                    $estoque_ent_transf = 0;
                    $estoque_ent_outra = 0;

                    $estoque_sai_morte = 0;
                    $estoque_sai_venda = 0;
                    $estoque_sai_transf = 0;
                    $estoque_sai_outra = 0;

                    // Pega estoque sem nascimento
                    $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                        WHERE year(tbl_mov_estoque_data_emissao)='$ano_lista' AND 
                              month(tbl_mov_estoque_data_emissao)='$mes_lista' AND 
                              tbl_mov_estoque_tipo_movimentacao!='N' AND
                              tbl_mov_estoque_tipo_movimentacao!='A' AND 
                              tbl_mov_estoque_tipo_movimentacao!='B' AND 
                              tbl_mov_estoque_codigo_id_animal!=999999999" . 
                            $wlocal);

                    $num_rows = mysqli_num_rows($mov_estoque);  

                    if ($num_rows!=0) {
                        while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                            $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                            $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                            if ($ent_sai=='E') {
                                if ($tipo=='C') {
                                    $estoque_ent_compra++;
                                }
                                else if ($tipo=='T') {
                                    $estoque_ent_transf++;
                                }
                                else{
                                    $estoque_ent_outra++;
                                }
                            }
                            else {
                                if ($tipo=='M') {
                                    $estoque_sai_morte++;   
                                }
                                else if ($tipo=='V') {
                                    $estoque_sai_venda++;
                                }
                                else if ($tipo=='T') {
                                    $estoque_sai_transf++;
                                }
                                else {
                                    $estoque_sai_outra++;
                                }
                            }
                        }
                    }

                    // Pega estoque nascimento

                    $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                        WHERE year(tbl_mov_estoque_nascimento)='$ano_lista' AND 
                              month(tbl_mov_estoque_nascimento)='$mes_lista' AND
                              tbl_mov_estoque_tipo_movimentacao='N' AND
                              tbl_mov_estoque_codigo_id_animal!=999999999" . 
                              $wlocal);

                    $num_rows = mysqli_num_rows($mov_estoque);  

                    if ($num_rows!=0) {
                        while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                            $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                            $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                            if ($ent_sai=='E') {
                                if ($tipo=='N') {
                                    $estoque_ent_nasc++;   
                                }
                            }
                        }
                    }

                    $estoque_final = $estoque_inicial + $estoque_ent_nasc +
                                     $estoque_ent_compra + $estoque_ent_transf + 
                                     $estoque_ent_outra;

                    $estoque_final = $estoque_final - $estoque_sai_morte - 
                                     $estoque_sai_venda - $estoque_sai_transf -
                                     $estoque_sai_outra;

                    if ($estoque_final==0) {
                        $estoque_final = $estoque_inicial;
                    }

                    $total_meses+= $estoque_final;
                    //$media_final = $total_meses/$qtd_meses;

                    echo '<tr>';
                    echo '<td width="9%" class="text-right">'.$array_mes_extenco[$i].'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_inicial.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_ent_nasc.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_ent_compra.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_ent_transf.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_ent_outra.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_sai_morte.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_sai_venda.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_sai_transf.'</td>';
                    echo '<td width="9%" class="text-center">'.$estoque_sai_outra.'</td>';
                    echo '<td width="10%" class="text-center">'.$estoque_final.'</td>';
                    echo '</tr>';

                    $estoque_inicial = $estoque_final;

                    $total_ent_nasc+= $estoque_ent_nasc;
                    $total_ent_compra+= $estoque_ent_compra;
                    $total_ent_transf+= $estoque_ent_transf;
                    $total_ent_outra+= $estoque_ent_outra;
                    $total_sai_morte+= $estoque_sai_morte;
                    $total_sai_venda+= $estoque_sai_venda;
                    $total_sai_transf+= $estoque_sai_transf;
                    $total_sai_outra+= $estoque_sai_outra;
                }


echo '<script type="text/javascript">
        $("#aguardar").modal("hide");
      </script>
    ';
?>

</tbody>

<thead>
    <tr>
        <th class="text-center" rowspan="2" style="vertical-align: middle;text-align:center;">Meses</th>
        <th class="text-center" rowspan="2" style="vertical-align: middle;text-align:center;">Estoque Inicial</th>
        <th class="text-center" colspan="4">Entradas</th>
        <th class="text-center" colspan="4">Saídas</th>
        <th class="text-center"rowspan="2" style="vertical-align: middle;text-align:center;">Estoque Final</th>
    </tr>

    <tr>
        <th class="text-center">Nascimento</th>
        <th class="text-center">Compra</th>
        <th class="text-center">Transferência</th>
        <th class="text-center">Outras Entradas</th>
        <th class="text-center">Morte</th>
        <th class="text-center">Venda</th>
        <th class="text-center">Transferência</th>
        <th class="text-center">Outras Saídas</th>
    </tr>
    </thead>

    <tfoot>
        <tr>
            <td width="18%" colspan="2" class="text-right">Totais</td>
            <td width="9%" class="text-center"><?php echo $total_ent_nasc; ?></td>
            <td width="9%" class="text-center"><?php echo $total_ent_compra;?></td>
            <td width="9%" class="text-center"><?php echo $total_ent_transf;?></td>
            <td width="9%" class="text-center"><?php echo $total_ent_outra;?></td>
            <td width="9%" class="text-center"><?php echo $total_sai_morte;?></td>
            <td width="9%" class="text-center"><?php echo $total_sai_venda;?></td>
            <td width="9%" class="text-center"><?php echo $total_sai_transf;?></td>
            <td width="9%" class="text-center"><?php echo $total_sai_outra;?></td>
            <td width="10%" class="text-center"></td>
            <!--<td width="10%" class="text-center"><?php //echo number_format($media_final,0,'','');?></td>-->
        </tr>
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




