<?php
    include "conecta_mysql.inc";

    $tipo_rel = $_POST['tipo_rel'];

    $data_inicial = $_POST["data_inicial"];
    $data_final = $_POST["data_final"];

    if ($data_inicial==0 && $data_final==0){
        $wperiodo = '';
    }
    else {
        $wperiodo = " AND tbl_venda_emissao >= '$data_inicial' AND tbl_venda_emissao <= '$data_final'";
    }

    $wlocal = '';
    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
        }
        else {
            if ($tipo_rel==1) {
                $wlocal = " AND tbl_venda_codigo_local_origem IN(";
                $wlocal.= implode(',', $local);
                $wlocal.= ")";
            }
            else {
                $wlocal = " AND tbl_venda_codigo_local_destino IN(";
                $wlocal.= implode(',', $local);
                $wlocal.= ")";
            }
        }
    }
    else {
        $wlocal='';
    }

    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="img/boi_virtual_preto.ico">
  <title>Boi Virtual</title>

  <!-- Bootstrap CSS -->

</head>

<body>
	<section class="panel">
            <?php
                echo '<div class="row col-md-12 filtro_escondido" id="total_contas">';

                echo '<div class="form-group col-md-9">';
                echo '<p id="descricao_filtro"
                    class="text-muted" style="font-size: 12px; color: #829c9c"></p>';
                echo '</div>';

                echo '<div class="form-group col-md-1">';
                echo '<button type="button" class="form-control btn btn-success pull-right"
                    onClick="lista_compra_venda_excel()">Excel</button>';
                echo '</div>';

                echo '<div class="form-group col-md-1">';
                echo '<button type="button" class="form-control btn btn-info pull-right exibir"
                    data-toggle="tooltip" data-placement="top" title="Maximizar tela filtros" onClick="exibir_filtro()"><i class="fa fa-sort-up"></i>&nbsp;<i class="fa fa-filter"></i></button>';
                echo '</div>';

                echo '<div class="form-group col-md-1 voltar">';
                echo '<button type="button" class="form-control btn btn-info pull-right"
                    onClick="voltar_relatorios()">Voltar</button>';
                echo '</div>';
                echo '</div>';
            ?>

        <table id="tabela_contas" class="table table-bordered table-advance table-hover" 
        style="width:100%; font-size:10px;">

       <tbody>
            <?php

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
                      tbl_venda_categoria='$tipo_rel'" . $wlocal . $wperiodo .
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

                    /*    $desconto = $reg_item->tbl_venda_total_desconto;
                        $total_desconto+= $desconto;
                        $desconto_edi = number_format($desconto,2,',','.');
                        $total_desconto_edi = number_format($total_desconto,2,',','.');

                        $vlr_receber = $reg_item->tbl_venda_total_receber;
                        $total_receber+= $vlr_receber;
                        $vlr_receber_edi = number_format($vlr_receber,2,',','.');
                        $total_receber_edi = number_format($total_receber,2,',','.');
                    */
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
                                    $vlr_arroba = $vlr_total/$peso_morto;
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
                            echo "<td width='5%' >".$desc_tipo."</td>";
                            echo "<td width='5%' class='alinhar_direita'>".$qtd."</td>";
                            echo "<td width='10%'>".$descricao_cat."</td>";
                            echo "<td width='5%' align='center'>".$sexo."</td>";
                            echo "<td width='15%'>".$desc_origem."</td>";
                            echo "<td width='15%'>".$desc_destino."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$peso_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$arroba_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_total_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_arroba_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_unit_cabeca_edi."</td>";
                             echo "</tr>";
                        }
                        else {
                            echo "<tr>";
                            echo "<td width='8%'>".$data_venda_edi."</td>";
                            echo "<td width='8%'>".$desc_tipo."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$qtd."</td>";
                            echo "<td width='8%'>".$descricao_cat."</td>";
                            echo "<td width='8%' align='center'>".$sexo."</td>";
                            echo "<td width='15%' >".$desc_destino."</td>";
                            echo "<td width='15%' >".$desc_origem."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$peso_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$peso_morto_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$rendimento."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_total_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_desconto_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_receber_item_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_unit_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_arroba_edi."</td>";
                            echo "<td width='8%' class='alinhar_direita'>".$vlr_unit_cabeca_edi."</td>";
                            echo "</tr>";
                        } 
                    }
                }
            } 
            mysqli_close($conector);

            ?>
        </tbody>
        <thead>
            <?php
                if ($tipo_rel==2) {
                    echo '<tr>';
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
                    echo '</tr>';

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
                    echo '<th colspan="2"></th>';
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
                    echo '<td class="alinhar_direita">'.$total_medio_cabeca_edi.'</td>';
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
    </section>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_contas').DataTable( {
                scrollY: "180px",
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

    <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>

</body>
</html>


                
                
