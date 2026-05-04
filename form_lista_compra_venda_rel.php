
<?php
    include "valida_sessao.inc";

    $origem_relatorio = $_REQUEST["origem_relatorio"];
    $tipo_rel = $_REQUEST['tipo_rel'];
    $codigo_local = $_REQUEST["codigo_local"];
    $codigo_cc = $_REQUEST["codigo_cc"];
    $descricao_filtro= $_REQUEST["descricao_filtro"];

    $data_inicial = $_REQUEST["data_inicial"];
    $data_final = $_REQUEST["data_final"];

    if ($data_inicial==0 && $data_final==0){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_venda_emissao >= '$data_inicial' AND tbl_venda_emissao <= '$data_final'";
    }

    $local= array();
    $matriz_itens = explode(",", $codigo_local);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $local[$i]=$matriz_itens[$i];
    }

    $local = implode(',', $local);
    $local = substr($local,0, -1);

    $wlocal = '';

    if ($codigo_local!='') {
        if ($tipo_rel==1) {
            $wlocal = " AND tbl_venda_codigo_local_origem IN(";
            $wlocal.= $local;
            $wlocal.= ")";
        }
        else {
            $wlocal = " AND tbl_venda_codigo_local_destino IN(";
            $wlocal.= $local;
            $wlocal.= ")";
        }
    }

    $centro_custo= array();
    $matriz_itens = explode(",", $codigo_cc);
    $quantidade_itens = count($matriz_itens);

    for($i=0; $i < $quantidade_itens; $i++) {
        $centro_custo[$i]=$matriz_itens[$i];
    }

    $centro_custo = implode(',', $centro_custo);
    $centro_custo = substr($centro_custo,0, -1);

    $wcc = '';

    if ($codigo_cc!='') {
        $wcc = " AND tbl_venda_centro_custos IN(";
        $wcc.= $centro_custo;
        $wcc.= ")";
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
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/fixedcolumns/3.3.1/css/fixedColumns.dataTables.min.css" rel="stylesheet">
  <link href="css/tabela.css" rel="stylesheet">
  <link rel="stylesheet" href="css/select-1.13.14.css"> 
  <link href="css/style-busca.css?<?php echo Versao; ?>" rel="stylesheet">

<style>
      .table_overflow table thead th{
        position: sticky;
        top: 0;
        z-index: 1;
      }
      .table_overflow th{
        background-color: #eee;
      }
      
      table.dataTable.no-footer{
          border: none;
      }

      #tabela_compra_venda_wrapper{
          width: 100% !important;
          /*overflow-x: scroll !important;
          overflow-y: scroll !important;
          max-height: 300px;*/
      }

      #tabela_compra_venda_filter{
          float: right;
      }
  </style>

</head>

<body>
    <section id="container" class="">
        <?php
            include "cabecalho.php"; include "limpar_secao_ctp_aceite.php";
            include "opcoes_menu.php"; include "limpar_secao_selecao_matrizes.php";
            include "limpar_secao_ctp.php"; 
            include "limpar_secao_ctr.php"; 
            include "limpar_secao_pesagem.php"; include "limpar_secao_movimentacao.php"; include "limpar_secao_nutricao.php"; include "limpar_secao_nascimento.php";
        ?>
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper" style="margin-left: 5px;">
            <span class="caminho-programa">Relatórios <i class="fa fa-angle-right seta-direita"></i><a class="voltar-menu" href="form_relatorios_financeiros.php">Relatórios Financeiros</a><i class="fa fa-angle-right seta-direita"></i>
            <span class="titulo">Compras/Vendas</span></span>

            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fas fa-money-check-alt"></i> Compras/Vendas</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel"> 
                        <div class=panel-body>
                            <div class="tab-content">
                                <div class="container" id="dados_cliente">

                                    <input type="hidden" id="expande_tela" value="S">
                                    
                                    <input type="hidden" id="origem_relatorio" 
                                    <?php echo "value='".$origem_relatorio."'";?>>

                                    <input type="hidden" id="tipo_rel"
                                    <?php echo "value='".$tipo_rel."'";?>>

                                    <input type="hidden" id="data_inicial"
                                    <?php echo "value='".$data_inicial."'";?>>

                                    <input type="hidden" id="data_final"
                                    <?php echo "value='".$data_final."'";?>>

                                    <input type="hidden" id="descricao_filtro"
                                    <?php echo "value='".$descricao_filtro."'";?>>

                                    <input type="hidden" id="codigo_local"
                                    <?php echo "value='".$codigo_local."'";?>>

                                    <input type="hidden" id="codigo_cc"
                                    <?php echo "value='".$codigo_cc."'";?>>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="label_consulta_rel">Filtros:</label>
                                            <span><?php echo $descricao_filtro;?></span>
                                        </div>

                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-info pull-right" onclick="voltar_compa_venda()">Voltar
                                            </button>

                                            <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                    onClick="lista_compra_venda_excel()">Excel
                                            </button>
                                        </div>
                                    </div>

                                    <!--<hr align="center">-->

<div class="row" >
<table id="tabela_compra_venda" class="table table-advance table-hover table-borderless" style="font-size:11px;" width="100%">
 

<tbody>
<?php
    include "conecta_mysql.inc";

    $total_qtd = 0;
    $total_peso_vivo = 0;
    $total_peso_morto = 0;
    $total_rendimento_medio = 0;
    $total_negociado = 0;
    $total_real = 0;
    $total_desconto = 0;
    $total_receber = 0;            
    $total_medio_arroba = 0;
    $total_medio_arroba_vendida = 0;
    $total_medio_cabeca = 0;
    $qtd_arroba_media = 0;
    $qtd_cabeca_media = 0;
    $qtd_redimento_media = 0;          
    $qtd_negociado_media = 0;          
    $total_rendimento_medio_edi = 0;
    $total_peso_vivo_edi = 0;
    $total_peso_morto_edi = 0;
    $total_real_edi = 0;
    $total_medio_arroba_edi = 0;
    $total_medio_cabeca_edi = 0;  
    $total_peso_arroba_edi = 0;
    $desconto_edi = 0;
    $total_desconto_edi = 0;
    $total_receber_edi = 0;
    $vlr_receber_edi = 0;
    $total_arroba = 0;
    $total_medio_negociado_edi = 0;

            $sql = "SELECT * from tbl_venda 
                WHERE tbl_venda_lixeira=0 AND 
                      tbl_venda_categoria='$tipo_rel'" . 
                      $wlocal . 
                      $wcc . 
                      $wperiodo .
                " ORDER BY tbl_venda_emissao ASC"; 

            $rs = mysqli_query($conector, $sql); 

            while ($reg_venda = mysqli_fetch_object($rs)){
                $numero_doc = $reg_venda->tbl_venda_id ;
                $codigo_origem = $reg_venda->tbl_venda_codigo_local_origem;
                $codigo_destino = $reg_venda->tbl_venda_codigo_local_destino;

                $total_venda = $reg_venda->tbl_venda_total_venda;
                $desconto = $reg_venda->tbl_venda_total_desconto;

                if ($desconto != 0) {
                    $total_desconto+= $desconto;
                    $desconto_edi = number_format($desconto,2,',','.');
                    $total_desconto_edi = number_format($total_desconto,2,',','.');

                    $per_desconto = ($desconto / $total_venda) * 100;
                }
                else {
                    $per_desconto = 0;
                }

                $vlr_receber = $reg_venda->tbl_venda_total_receber;
                $total_receber+= $vlr_receber;
                $vlr_receber_edi = number_format($vlr_receber,2,',','.');
                $total_receber_edi = number_format($total_receber,2,',','.');
                $per_receber = ($vlr_receber / $total_venda) * 100;
                
                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_origem'");
                $num_rows = mysqli_num_rows($tbl_local);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_local);
                    $desc_origem = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_origem = '';
                }

                $tbl_local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_destino'");
                $num_rows = mysqli_num_rows($tbl_local);

                if ($num_rows!=0){
                    $reg = mysqli_fetch_object($tbl_local);
                    $desc_destino = $reg->tbl_pessoa_nome;
                }
                else {
                    $desc_destino = '';
                }

                $data_venda = new DateTime($reg_venda->tbl_venda_emissao);
                $data_venda_edi = $data_venda->format('d/m/Y');

                $tipo = $reg_venda->tbl_venda_tipo;

                switch ($tipo) {
                    case 'V':
                        $desc_tipo = 'Vivo';
                        break;
                    case 'M':
                        $desc_tipo = 'Morto';
                        break;
                    default:
                        $desc_tipo = 'Cabeça';
                        break;
                }

                $tbl_item = mysqli_query($conector, "select * from tbl_item_venda
                         where tbl_ite_venda_numero_id  ='$numero_doc'"); 
                $num_rows = mysqli_num_rows($tbl_item);

                if ($num_rows!=0) {
                    while ($reg_item = mysqli_fetch_object($tbl_item)) {
                        $categoria = $reg_item->tbl_ite_venda_categoria;
                        $sexo = $reg_item->tbl_ite_venda_sexo;
                        $qtd = $reg_item->tbl_ite_venda_quantidade;
                        $total_qtd+=$qtd;

                        if ($reg_item->tbl_ite_venda_peso_vivo_ajustado==0) {
                            $peso = $reg_item->tbl_ite_venda_peso_vivo;
                            $peso_edi = number_format($reg_item->tbl_ite_venda_peso_vivo,2,',','.');
                        }
                        else {
                            $peso = $reg_item->tbl_ite_venda_peso_vivo_ajustado;
                            $peso_edi = number_format($reg_item->tbl_ite_venda_peso_vivo_ajustado,2,',','.');
                        }

                        $total_peso_vivo+=$peso;
                        $total_peso_vivo_edi = number_format($total_peso_vivo,2,',','.');

                        if ($reg_item->tbl_ite_venda_peso_morto!=0) {
                            $peso_morto = $reg_item->tbl_ite_venda_peso_morto;
                            $peso_morto_edi = number_format($reg_item->tbl_ite_venda_peso_morto,2,',','.');
                        }
                        else {
                            $peso_morto = $reg_item->tbl_ite_venda_peso_morto;
                            $peso_morto_edi = '';
                        }

                        $total_peso_morto+=$peso_morto;
                        $total_peso_morto_edi = number_format($total_peso_morto,2,',','.');

                        $arroba = $reg_item->tbl_ite_venda_arroba;
                        $total_arroba+= $arroba;
                        $total_peso_arroba_edi = number_format($total_arroba,2,',','.');
                        
                        $arroba_edi = number_format($reg_item->tbl_ite_venda_arroba,2,',','.');
                        $und = $reg_item->tbl_ite_venda_unidade_negociada;

                        $vlr_unit = $reg_item->tbl_ite_venda_valor_unitario;

                        if ($tipo=='M' && $und==2) {
                            $vlr_unit = ($peso_morto/$arroba)*$vlr_unit;
                        }

                        if ($tipo!='M') {
                            $vlr_unit_edi = '';
                        }
                        else {
                            $vlr_unit_edi = number_format($vlr_unit,2,',','.');
                            $qtd_negociado_media++;
                            $total_negociado+=$vlr_unit;
                            $total_medio_negociado_div = $total_negociado/$qtd_negociado_media;
                            $total_medio_negociado_edi = number_format($total_medio_negociado_div,2,',','.');
                        }

                        $vlr_total = $reg_item->tbl_ite_venda_valor_total;
                        $vlr_total_edi = number_format($vlr_total,2,',','.');

                        $total_real+=$vlr_total;
                        $total_real_edi = number_format($total_real,2,',','.');

                        $vlr_unit_cabeca = $vlr_total/$qtd;
                        $vlr_unit_cabeca_edi = number_format($vlr_unit_cabeca,2,',','.');

                        if ($per_desconto!=0) {
                            $vlr_desconto = ($vlr_total * $per_desconto) / 100;
                            $vlr_desconto_edi = number_format($vlr_desconto,2,',','.');
                        }
                        else {
                            $vlr_desconto_edi = '';
                        }

                        $vlr_receber_item = ($vlr_total * $per_receber) / 100;
                        $vlr_receber_item_edi = number_format($vlr_receber_item,2,',','.');

                        $total_medio_cabeca+= $vlr_unit_cabeca;
                        $qtd_cabeca_media++;
                        $total_medio_cabeca_div = $total_medio_cabeca/$qtd_cabeca_media;
                        $total_medio_cabeca_edi = number_format($total_medio_cabeca_div,2,',','.');

                        if ($arroba!=0) {
                            if ($tipo=='C') {
                                $arroba = $peso/30;
                                $vlr_arroba = $vlr_total/$arroba;
                                $vlr_arroba_edi = number_format($vlr_arroba,2,',','.');
                                $qtd_arroba_media++;
                                $total_medio_arroba+= $vlr_arroba;
                            }
                            else {
                                if ($und==1) {
                                    $vlr_arroba = $vlr_total/$arroba;
                                    $vlr_arroba_edi = number_format($vlr_arroba,2,',','.');
                                    $qtd_arroba_media++;
                                    $total_medio_arroba+= $vlr_arroba;
                                }
                                else {
                                    if ($tipo=='V' && $und==2) {
                                        $vlr_arroba = $arroba * $vlr_unit;
                                    }
                                    else {
                                        $vlr_arroba = $vlr_total/$arroba;
                                    }
                                    
                                    $vlr_arroba_edi = number_format($vlr_arroba,2,',','.');
                                    $qtd_arroba_media++;
                                    $total_medio_arroba+= $vlr_arroba;
                                }
                            }
                        }
                        else {
                            $vlr_arroba_edi = number_format(0,2,',','.');
                        }

                        if ($total_medio_arroba==0 || $qtd_arroba_media==0) {
                            $total_medio_arroba_div = 0;
                            $total_medio_arroba_edi = number_format($total_medio_arroba_div,2,',','.');
                        }
                        else {
                            $total_medio_arroba_div = $total_medio_arroba/$qtd_arroba_media;
                            $total_medio_arroba_edi = number_format($total_medio_arroba_div,2,',','.');
                        }

                        if ($reg_item->tbl_ite_percentual_rendimento==0) {
                            //if ($peso!=0) {
                                //$rendimento = number_format(50,2,',','.');
                                //$qtd_redimento_media++;
                                //$total_rendimento_medio+=50;

                                //$total_rendimento_medio_div = $total_rendimento_medio/$qtd_redimento_media;
                                //$total_rendimento_medio_edi = number_format($total_rendimento_medio_div,2,',','.');
                           // }
                           // else {
                                $rendimento = '';
                            //}
                        }
                        else {
                            $rendimento = number_format($reg_item->tbl_ite_percentual_rendimento,2,',','.');
                            $qtd_redimento_media++;
                            $total_rendimento_medio+=$reg_item->tbl_ite_percentual_rendimento;

                            $total_rendimento_medio_div = $total_rendimento_medio/$qtd_redimento_media;
                            $total_rendimento_medio_edi = number_format($total_rendimento_medio_div,2,',','.');
                        }

                        $tbl_categoria = mysqli_query($conector, "select * from tabela_categoria_idade
                                where tab_codigo_categoria_idade='$categoria'"); 
                        $num_rows = mysqli_num_rows($tbl_categoria);
                        if ($num_rows!=0) {
                            $reg_cat = mysqli_fetch_object($tbl_categoria);
                            $idade_de = $reg_cat->tab_categoria_idade_de;
                            $idade_ate = $reg_cat->tab_categoria_idade_ate;
                            $descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';

                            if ($descricao_cat=='37 a 999999999 meses'){
                                $descricao_cat = '> 36 meses';
                            }
                        }
                        else {
                            $descricao_cat = '';
                        }

                        if ($und==1) {
                            $und = '@';
                        }
                        else {
                            $und = 'Kg';
                        }

                        if ($tipo_rel==2) {
                            echo "<tr>";
                            echo "<td width='5%'>".$data_venda_edi."</td>";
                            echo "<td width='5%' align='center'>".$desc_tipo."</td>";
                            echo "<td width='5%' align='center'>".$qtd."</td>";
                            echo "<td width='10%'>".$descricao_cat."</td>";
                            echo "<td width='5%' align='center'>".$sexo."</td>";
                            echo "<td width='15%'>".$desc_origem."</td>";
                            echo "<td width='15%'>".$desc_destino."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$peso_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$arroba_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_total_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_arroba_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_unit_cabeca_edi."</td>";
                             echo "</tr>";
                        }
                        else {
                            echo "<tr>";
                            echo "<td width='8%'>".$data_venda_edi."</td>";
                            echo "<td width='8%'  align='center'>".$desc_tipo."</td>";
                            echo "<td width='8%' align='center'>".$qtd."</td>";
                            echo "<td width='8%'>".$descricao_cat."</td>";
                            echo "<td width='8%' align='center'>".$sexo."</td>";
                            echo "<td width='15%'>".$desc_destino."</td>";
                            echo "<td width='15%'>".$desc_origem."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$peso_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$peso_morto_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$rendimento."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_total_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_desconto_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_receber_item_edi."</td>";
                            echo "<td width='8%'>".$vlr_unit_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%' class='alinhar_direita'>".$vlr_arroba_edi."</td>";
                            echo "<td class='alinhar_direita' width='8%'>".$vlr_unit_cabeca_edi."</td>";
                            echo "</tr>";
                        } 
                    }
                }
            } 
            mysqli_close($conector);


echo '<script type="text/javascript">
        $("#aguardar").modal("hide");
      </script>
    ';

?>
</tbody>

<thead>
<?php

    if ($tipo_rel==2) {
        echo '<tr>';
        echo '<div class="row" style="padding-left: 15px;">';
        echo '<div class="col-md-3">';
        echo '<span>Total Cabeças:&nbsp;&nbsp;<span class="text-primary">'.$total_qtd.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>Total Kg:&nbsp;<span class="text-primary">'.$total_peso_vivo_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>Total @:&nbsp;&nbsp;<span class="text-primary">'.$total_peso_arroba_edi.'</span></span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="row" style="padding-left: 15px;">';
        echo '<div class="col-md-3">';
        echo '<span>R$ Total:&nbsp;&nbsp;<span class="text-primary">'.$total_real_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>R$ Médio @:&nbsp;&nbsp;<span class="text-primary">'.$total_medio_arroba_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>R$ Médio Cabeça:&nbsp;&nbsp;<span class="text-primary">'.$total_medio_cabeca_edi.'</span></span>';
        echo '</div>';
        echo '</div>';
        echo '</tr>';

        /*echo '<tr>';
        echo '<th colspan="2"></th>';
        echo '<th>Total Cabeças</th>';
        echo '<th colspan="4"></th>';
        echo '<th>Total Kg</th>';
        echo '<th>Total @</th>';
        echo '<th>Total</th>';
        echo '<th>R$ Médio @</th>';
        echo '<th>R$ Médio Cabeça</th>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="2"></td>';
        echo '<td class="alinhar_direita">'.$total_qtd.'</td>';
        echo '<td colspan="4"></td>';
        echo '<td class="alinhar_direita">'.$total_peso_vivo_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_peso_arroba_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_real_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_medio_arroba_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_medio_cabeca_edi.'</td>';
        echo '</tr>';*/

        echo '<tr>';
        echo '<th>Data</th>';
        echo '<th>Tipo Compra</th>';
        echo '<th>Nº Cabeças</th>';
        echo '<th>Categoria</th>';
        echo '<th>Sexo</th>';
        echo '<th>Fornecdor</th>';
        echo '<th>Fazenda</th>';
        echo '<th>Peso Total Kg</th>';
        echo '<th>Peso Total @</th>';
        echo '<th>Valor Total</th>';
        echo '<th>R$/@</th>';
        echo '<th>R$/Cabeça</th>';
        echo '</tr>';
    }
    else {
        echo '<tr>';
        echo '<div class="row" style="padding-left: 15px;">';
        echo '<div class="col-md-3">';
        echo '<span>Total Cabeças:&nbsp;&nbsp;<span class="text-primary">'.$total_qtd.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>Total Kg Fazenda:&nbsp;<span class="text-primary">'.$total_peso_vivo_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>Total Kg Morto:&nbsp;&nbsp;<span class="text-primary">'.$total_peso_morto_edi.'</span></span>';
        echo '</div>';

        echo '<div class="col-md-3">';
        echo '<span>Rendimento Médio:&nbsp;&nbsp;<span class="text-primary">'.$total_rendimento_medio_edi.'</span></span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="row" style="padding-left: 15px;">';
        echo '<div class="col-md-3">';
        echo '<span>Total Real:&nbsp;&nbsp;<span class="text-primary">'.$total_real_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>Total Desconto:&nbsp;&nbsp;<span class="text-primary">'.$total_desconto_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>Total Receber:&nbsp;&nbsp;<span class="text-primary">'.$total_receber_edi.'</span></span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="row" style="padding-left: 15px;">';
        echo '<div class="col-md-3">';
        echo '<span>R$ Médio @ Negociado:&nbsp;&nbsp;<span class="text-primary">'.$total_medio_negociado_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>R$ Médio @ Vendida:&nbsp;&nbsp;<span class="text-primary">'.$total_medio_arroba_edi.'</span></span>';
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<span>R$ Médio Cabeça:&nbsp;&nbsp;<span class="text-primary">'.$total_medio_cabeca_edi.'</span></span>';
        echo '</div>';
        echo '</div>';

        /*echo '<th colspan="2"></th>';
        echo '<th>Total Cabeças</th>';
        echo '<th colspan="4"></th>';
        echo '<th>Total Kg</th>';
        echo '<th>Total Kg</th>';
        echo '<th>Rendimento Médio</th>';
        echo '<th>Total real</th>';
        echo '<th>Total Desconto</th>';
        echo '<th>Total Receber</th>';
        echo '<th>R$ Médio @</th>';
        echo '<th>R$ Médio @ Vendida</th>';
        echo '<th>R$ Médio Cabeça</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="2"></td>';
        echo '<td class="alinhar_direita">'.$total_qtd.'</td>';
        echo '<td colspan="4"></td>';
        echo '<td class="alinhar_direita">'.$total_peso_vivo_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_peso_morto_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_rendimento_medio_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_real_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_desconto_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_receber_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_medio_negociado_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_medio_arroba_edi.'</td>';
        echo '<td class="alinhar_direita">'.$total_medio_cabeca_edi.'</td>';*/
        echo '</tr>';

        echo '<tr>';
        echo '<th>Data</th>';
        echo '<th>Tipo Venda</th>';
        echo '<th>Nº Cabeças</th>';
        echo '<th>Categoria</th>';
        echo '<th>Sexo</th>';
        echo '<th>Comprador</th>';
        echo '<th>Fazenda</th>';
        echo '<th>Peso Fazenda</th>';
        echo '<th>Peso Morto</th>';
        echo '<th>Rend Carcaça (%)</th>';
        echo '<th>Valor Real</th>';
        echo '<th>Desconto Funrural</th>';
        echo '<th>Valor Receber</th>';
        echo '<th>R$ @ Negociada</th>';
        echo '<th>R$ @ Vendida</th>';
        echo '<th>R$/Cabeça</th>';
        echo '</tr>';
    }
?>

</thead>

</table>
</div>                                                                            
<!--                                    <hr align="center"> 

                                    <div class="row">  
                                        <button type="button" class="btn btn-info pull-right" onclick="voltar_filtro_previsao()">Voltar
                                        </button>

                                        <button type="button" class="btn btn-success pull-right" style="margin-right: 6px;" 
                                                onClick="listar_previsao_excel()">Excel
                                        </button>
                                    </div> -->
                                </div> <!-- fim container -->
                            </div> <!--tab-content -->
                        </div> <!--panel-body -->
                    </div> <!--panel -->      
                </div> <!--col-lg-12 2-->
            </div> <!--row -->

            <div class="modal fade" id="mensagem_retorno" tabindex="-1" role="dialog" 
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Relatório Compra/Venda</h4>
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
                            <h4 class="modal-title">Relatório Compra/Venda - Mensagem</h4>
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

    <div class="text-center">
         <div class="credits">
             <font size="2"><p style="color:#C0C0C0">Copyright &copy; Agrolandes 2023</p></font>
         </div>
     </div>

    </section> <!-- container section start end -->
      
    <script src="js/jquery.js?<?php echo Versao; ?>"></script>
    <script src="js/jquery.nicescroll.js?<?php echo Versao; ?>" type="text/javascript"></script>
    <script src="js/scripts.js?<?php echo Versao; ?>"></script>
    <script src="js/compra_venda.js?<?php echo Versao; ?>" charset="utf-8" type="text/javascript" ></script>
    <script src="js/opcoes_menu.js?<?php echo Versao; ?>"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js?<?php echo Versao; ?>"></script>

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" charset="utf-8" type="text/javascript" >
    </script>

    <script src="https://cdn.datatables.net/fixedcolumns/3.3.1/js/dataTables.fixedColumns.min.js
    " charset="utf-8" type="text/javascript" >
    </script>
    <script src="js/select-1.13.14.js?<?php echo Versao;?>"></script>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_compra_venda').DataTable( {
                /*fixedColumns:   {
                    leftColumns: 1,
                    rightColumns: 0,
                },*/
                scrollY:  "180px",
                scrollX:  true,
                paging:   false,
                search:   true,
                ordering: false,
                info: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Registros encontrados: _END_ ",
                  infoEmpty: "Nenhum registro disponível",
                  infoFiltered: "(filtrado de _MAX_ registros no total)",
                }
            });
        });

    </script>
    
</body>
</html>

