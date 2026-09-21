<?php
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");

    $data_inicial = $_POST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = '01';

    $data_final = $_POST['data_final'];
    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    if ($qtd_meses>12) {
        echo 'Erro meses';
        exit;
    }

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));
    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

    $ano_mes = $data_array->format('Y').$data_array->format('m');
    $array_mes_ano[$ano_mes]=$ano_mes;
    $array_peso[$ano_mes]=0;

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
        $ano_mes = $data_array->format('Y').$data_array->format('m');
        $array_mes_ano[$ano_mes]=$ano_mes;
        $array_peso[$ano_mes]=0;
    } 

    $wlocal = "";
    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
        }
        else {
            $wlocal = " AND tbl_animal_codigo_fazenda IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ")";

            $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
            $wlocal_anterior.= implode(',', $local);
            $wlocal_anterior.= ")";
            $wlocal_anterior.= " OR tbl_animal_codigo_fazenda_anterior IN(";
            $wlocal_anterior.= implode(',', $local);
            $wlocal_anterior.= "))";
            }
    }
    else {
        $wlocal='';
        $wlocal_anterior='';
    }

    $wsexo = "";
    if (isset($_POST['sexo'])) {
        $sexo = $_POST['sexo'];

        if(in_array("Todos", $sexo)) {
            $wsexo='';
        }
        else {
            $wsexo = " AND tbl_animal_sexo IN(";
            $wsexo .= "'" . implode("','", $sexo) . "'";
            $wsexo.= ")";
            }
    }
    else {
        $wsexo='';
    }

    $wcategoria = "";
    if (isset($_POST['categoria'])) {
        $categoria_filtro = $_POST['categoria'];

        if(in_array("", $categoria_filtro)) {
            $wcategoria='';
        }
        else {
            //$wcategoria = " AND tbl_animal_categoria IN(";
            //$wcategoria.= implode(',', $categoria_filtro);
            //$wcategoria.= ")";
            $wcategoria = $categoria_filtro;
       }
    }
    else {
        $wcategoria='';
    }

    $wraca = "";
    if (isset($_POST['raca'])) {
        $raca = $_POST['raca'];

        if(in_array("", $raca)) {
            $wraca='';
        }
        else {
            $wraca = " AND tbl_animal_codigo_raca IN(";
            $wraca.= implode(',', $raca);
            $wraca.= ")";
            }
    }
    else {
        $wraca='';
    }

    $wpai = "";
    if (isset($_POST['pai'])) {
        $pai = $_POST['pai'];

        if(in_array("", $pai)) {
            $wpai='';
        }
        else {
            $wpai = " AND tbl_animal_codigo_pai IN(";
            $wpai.= implode(',', $pai);
            $wpai.= ")";
            }
    }
    else {
        $wpai='';
    }

    $wmae = "";
    if (isset($_POST['mae'])) {
        $mae = $_POST['mae'];

        if(in_array("", $mae)) {
            $wmae='';
        }
        else {
            $wmae = " AND tbl_animal_codigo_mae IN(";
            $wmae.= implode(',', $mae);
            $wmae.= ")";
            }
    }
    else {
        $wmae='';
    }

    $peso_nasc_inicial = $_POST["peso_nasc_inicial"];
    $peso_nasc_final = $_POST["peso_nasc_final"];

    $peso_desmama_inicial = $_POST["peso_desmama_inicial"];
    $peso_desmama_final = $_POST["peso_desmama_final"];

    $peso_ult_inicial = $_POST["peso_ult_inicial"];
    $peso_ult_final = $_POST["peso_ult_final"];

    if ($peso_nasc_inicial==0 && $peso_nasc_final==0){
        $wpeso_nasc = '';
    }
    else {
        $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial' AND tbl_animal_primeiro_peso <= '$peso_nasc_final'";
    }

    if ($peso_desmama_inicial==0 && $peso_desmama_final==0){
        $wpeso_desmama = '';
    }
    else {
        $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial' AND tbl_animal_peso_desmama <= '$peso_desmama_final'";
    }

    if ($peso_ult_inicial==0 && $peso_ult_final==0){
        $wpeso_ult = '';
    }
    else {
        $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial' AND tbl_animal_ultimo_peso <= '$peso_ult_final'";
    }

    $data_nasc_inicial = $_POST["data_nasc_inicial"];
    $data_nasc_final = $_POST["data_nasc_final"];

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial' AND tbl_animal_data_nascimento <= '$data_nasc_final'";
    }

    $tipo_rel = $_POST['tipo_rel'];
    $wativo = $_POST['ativo'];

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

  <style>
      /* div.dataTables_scrollBody{
          overflow-x: hidden !important;
      }
      div.dataTables_scroll{
          overflow-x: scroll !important;
      } */
      /* div.table_overflow{
        width: 100% !important;
        overflow-y: scroll !important;
        max-height: 400px;
      } */
      .table_overflow table thead th{
        position: sticky;
        top: 0;
        z-index: 1;
      }
      .table_overflow th{
         /* background-color: #eee;*/
      }

      table.dataTable.no-footer{
          border: none;
      }
      
      #tabela_lista_animais_wrapper{
          width: 100% !important;
          /*overflow-x: scroll !important;
          overflow-y: scroll !important;*/
          max-height: 500px;
      }

      #tabela_lista_animais_filter{
          float: left;
      }
  </style>
</head>

<body>
	<section class="panel table-responsive">
        <table id="tabela_lista_animais" class="table table-bordered table-advance table-hover" style="width:50%; font-size:10px; float: left; margin-left: 10px; border: none;">

        <tbody>
            <?php
                $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
                $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
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
                        $array_qtd_macho_categoria[$codigo_categoria] = 0;
                        $array_qtd_femea_categoria[$codigo_categoria] = 0;
                        $array_gmd_macho_categoria[$codigo_categoria] = 0;
                        $array_gmd_femea_categoria[$codigo_categoria] = 0;
                    }
                }   

                $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
                        WHERE tbl_animal_lixeira=0 AND
                              tbl_animal_ativo='$wativo'" . $wlocal . $wsexo . $wraca . $wpai . 
                              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .

                              " OR (DATE(tbl_animal_baixado_em)>='$data_inicial' AND DATE(tbl_animal_baixado_em)<='$data_final' AND tbl_animal_ativo='N' AND (tbl_animal_situacao='V' OR tbl_animal_situacao='M'))" . $wlocal_anterior . $wsexo . $wraca . $wpai . 
                              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
                    " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"); 

                $num_rows_animais = mysqli_num_rows($tbl_animal);

                if ($num_rows_animais!=0) {
                    while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
                        $codigo = $reg_animal->tbl_animal_codigo_id;
                        $sexo = $reg_animal->tbl_animal_sexo; 

                        $data_peso_nascimento=0;
                        $peso_nascimento=0;

                        if ($reg_animal->tbl_animal_primeiro_peso!='') {
                            $data_primeiro_peso = substr($reg_animal->tbl_animal_data_primeiro_peso, 0, 10);

                            if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                                $data_peso_nascimento = $data_primeiro_peso;
                                $peso_nascimento = $reg_animal->tbl_animal_primeiro_peso;
                            }
                        }

                        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                        $data_acompanhamento_calculo = $data_final;
                        $date = new DateTime($data_nascimento); // Data de Nascimento
                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                        $categoria_animal = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                WHERE tab_registro_lixeira_categoria_idade='0'");

                        $num_rows = mysqli_num_rows($categoria_animal);    

                        if ($num_rows!=0) {
                            while ($reg_cat_animal = mysqli_fetch_object($categoria_animal)) {
                                $idade_de = $reg_cat_animal->tab_categoria_idade_de;
                                $idade_ate = $reg_cat_animal->tab_categoria_idade_ate;

                                if ($idade >= $idade_de && $idade <= $idade_ate) {
                                    $codigo_categoria_animal = $reg_cat_animal->tab_codigo_categoria_idade;
                                }
                            }
                        }                   

                        if ($data_peso_nascimento==0) {
                            $data_peso_inicial='0000-00-00';
                            $peso_inicial = 9999;
                        }

                        $data_peso_final = '0000-00-00';
                        $peso_final = 9999;

                        $tbl_peso = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                            WHERE tbl_ite_pesagem_data_emissao>='$data_inicial' AND 
                                  tbl_ite_pesagem_data_emissao<='$data_final' AND 
                                  tbl_ite_pesagem_codigo_id_animal='$codigo'
                                ORDER BY tbl_ite_pesagem_data_emissao ASC");

                        $num_rows_peso = mysqli_num_rows($tbl_peso);    

                        if ($num_rows_peso!=0) {
                            if ($data_peso_nascimento!=0) {
                                $partes = explode("-", $data_peso_nascimento);

                                for ($i=0; $i < $qtd_meses; $i++) { 
                                    if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                        $peso_inicial=$peso_nascimento;
                                    }
                                }
                            }

                            while ($reg_peso = mysqli_fetch_object($tbl_peso)) {
                                $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                                $peso = $reg_peso->tbl_ite_pesagem_peso;

                                if ($peso == 0) {
                                    $peso = 9999;
                                }

                                $partes = explode("-", $data_peso_inicial);
                                $ano_mes_peso_inicial = $partes[0].$partes[1];

                                $partes = explode("-", $data_peso_final);
                                $ano_mes_peso_final = $partes[0].$partes[1];

                                $partes = explode("-", $data_peso);
                                $ano_mes_peso = $partes[0].$partes[1];

                                if ($data_peso_inicial=='0000-00-00') {
                                    $data_peso_inicial=$data_peso;
                                    $peso_inicial=$peso;
                                }

                                if ($ano_mes_peso_inicial==$ano_mes_peso) {
                                    if ($peso<$peso_inicial && $peso!=0) {
                                        $data_peso_inicial=$data_peso;
                                        $peso_inicial = $peso;
                                    }
                                }

                                if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                                    if ($ano_mes_peso_final==$ano_mes_peso) {
                                        if ($peso<$peso_final && $peso!=0) {
                                            $data_peso_final=$data_peso;
                                            $peso_final = $peso;
                                        }
                                    }
                                    else {
                                        $data_peso_final=$data_peso;
                                        $peso_final = $peso;
                                    }
                                }

/*                                if ($data_peso_nascimento==0) {
                                    $peso_inicial=$peso;
                                    $data_peso_nascimento=$data_peso;
                                    $data_peso_inicial=$data_peso;
                                }

                                $peso_final = $peso;
                                $data_peso_final=$data_peso; */
                            }  
                        } 

                        if ($peso_inicial==9999) {
                            $peso_inicial = 0;
                        }

                        if ($peso_final==9999) {
                            $peso_final = 0;
                        }

                        $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
                        $dias = floor($diferenca / (60 * 60 * 24)); 

                        if ($peso_final && $peso_inicial) {
                            $ganho = $peso_final - $peso_inicial;
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
                            if ($sexo=="M") {
                                $array_gmd_macho_categoria[$codigo_categoria_animal]+=$gmd;
                                $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                            }
                            else {
                                $array_gmd_femea_categoria[$codigo_categoria_animal]+=$gmd;
                                $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                            }
                        }
                    }
                }

                if ($wcategoria=="") {
                    for ($i=1; $i <= 5; $i++) { 
                        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                        if ($array_qtd_macho_categoria[$j]!=0) {
                            echo '<tr>';
                            echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                            echo '<td width="10%" style="text-align: center;">M</td>';
                            echo '<td width="10%" style="text-align: center;">'.$array_qtd_macho_categoria[$j].'</td>';

                            $gmd = $array_gmd_macho_categoria[$j]/
                                   $array_qtd_macho_categoria[$j];

                            $gmd_total+= $array_gmd_macho_categoria[$j];

                            if ($gmd!=0) {
                                $numero_gmd+= $array_qtd_macho_categoria[$j];
                            }

                            $gmd_edi = number_format($gmd,3,',','.');
                            echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                            echo '<td style="border: none"></td>';    
                            echo '<td style="border: none"></td>';    
                            echo '<td style="border: none"></td>';    
                            echo '</tr>';
                            $animais_listados+=$array_qtd_macho_categoria[$j];
                        }

                        if ($array_qtd_femea_categoria[$j]!=0) {
                            echo '<tr>';
                            echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                            echo '<td width="10%" style="text-align: center;">F</td>';
                            echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';

                            $gmd = $array_gmd_femea_categoria[$j]/
                                   $array_qtd_femea_categoria[$j];

                            $gmd_total+= $array_gmd_femea_categoria[$j];

                            if ($gmd!=0) {
                                $numero_gmd+= $array_qtd_femea_categoria[$j];
                            }

                            $gmd_edi = number_format($gmd,3,',','.');
                            echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';
                            echo '<td style="border: none"></td>';    
                            echo '<td style="border: none"></td>';    
                            echo '<td style="border: none"></td>';    
                            echo '</tr>';
                            $animais_listados+=$array_qtd_femea_categoria[$j];
                        }
                    }
                }
                else {
                    for ($i=1; $i <= 5; $i++) { 
                        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
                        foreach ($wcategoria as $value) {
                            if ($value==$j) {
                                if ($array_qtd_macho_categoria[$j]!=0) {
                                    echo '<tr>';
                                    echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                                    echo '<td width="10%" style="text-align: center;">M</td>';
                                    echo '<td width="10%" style="text-align: center;">'.$array_qtd_macho_categoria[$j].'</td>';

                                    $gmd = $array_gmd_macho_categoria[$j]/
                                           $array_qtd_macho_categoria[$j];

                                    $gmd_total+= $array_gmd_macho_categoria[$j];
                                    $numero_gmd+= $array_qtd_macho_categoria[$j];

                                    $gmd_edi = number_format($gmd,3,',','.');
                                    echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                                    echo '<td style="border: none"></td>';    
                                    echo '<td style="border: none"></td>';    
                                    echo '<td style="border: none"></td>';    
                                    echo '</tr>';
                                    $animais_listados+=$array_qtd_macho_categoria[$j];
                                }

                                if ($array_qtd_femea_categoria[$j]!=0) {
                                    echo '<tr>';
                                    echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                                    echo '<td width="10%" style="text-align: center;">F</td>';
                                    echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';

                                    $gmd = $array_gmd_femea_categoria[$j]/
                                           $array_qtd_femea_categoria[$j];

                                    $gmd_total+= $array_gmd_femea_categoria[$j];
                                    $numero_gmd+= $array_qtd_femea_categoria[$j];

                                    $gmd_edi = number_format($gmd,3,',','.');
                                    echo '<td width="10%" style="text-align: right;">'.$gmd_edi.'</td>';    
                                    echo '<td style="border: none"></td>';    
                                    echo '<td style="border: none"></td>';    
                                    echo '<td style="border: none"></td>';    
                                    echo '</tr>';
                                    $animais_listados+=$array_qtd_femea_categoria[$j];
                                }
                            }
                        }
                    }
                }
            ?>
        </tbody>

        <thead>
            <?php
                echo '<div class="row col-md-12 filtro_escondido" id="total_contas">';

                    echo '<div class="form-group col-md-9">';
                    echo '<p id="descricao_filtro"
                    class="text-muted" style="font-size: 12px; color: #829c9c"></p>';
                    echo '</div>';

                    echo '<div class="form-group col-md-1">';
                        echo '<button type="button" class="form-control btn btn-success pull-right"
                            onClick="lista_gmd_excel()">Excel</button>';
                    echo '</div>';

                    echo '<div class="form-group col-md-1">';
                        echo '<button type="button" class="form-control btn btn-info pull-right exibir"
                            data-toggle="tooltip" data-placement="bottom" title="Maximizar tela filtros" onClick="exibir_filtro()"><i class="fa fa-sort-up"></i>&nbsp;<i class="fa fa-filter"></i></button>';

                        echo '<button type="button" class="form-control btn btn-info pull-right esconder" hidden=""
                            data-toggle="tooltip" data-placement="bottom" title="Minimizar tela filtros" onClick="esconder_filtro()"><i class="fa fa-sort-down"></i>&nbsp;<i class="fa fa-filter"></i></button>';
                        echo '</div>';

                    echo '<div class="form-group col-md-1 voltar">';
                        echo '<button type="button" class="form-control btn btn-info pull-right" onclick="onclick=voltar_relatorios()">Voltar</button>';
                    echo '</div>';

                echo '</div>';

                if ($gmd_total!=0 && $numero_gmd>0) {
                    $media_gmd = $gmd_total / $numero_gmd;
                    $media_gmd_edi = number_format($media_gmd,3,',','.');
                }
                else {
                    $media_gmd_edi = 0;
                }

            ?>

            <tr>
                <th style="text-align: center">Animais</th>
                <th colspan="3"></th>
                <th style="border: none"></th>
                <th style="border: none"></th>
                <th style="text-align: center">GMD Global</th>
            </tr>
            <tr>
                <td style="text-align: center" class="animais_listados"><?php echo $animais_listados?></td>
                <td colspan="3"></td>
                <td style="border: none"></td>
                <td style="border: none"></td>
                <td style="text-align: center;"><?php echo $media_gmd_edi?></td>
            </tr>
            <tr>
                <th>Categoria</th>
                <th>Sexo</th>
                <th>Qtde</th>
                <th>GMD</th>
                <th style="border: none"></th>
                <th style="border: none"></th>
                <th style="border: none"></th>
            </tr>
                
        </thead>
        </table>
    </section>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_lista_animais').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                bFilter: false,
                /* scrollY: "200px", */
                paging:   false,
                search:   false,
                info: false,
                ordering: false,
                language: {
                  sSearch: "Buscar na lista:",
                  zeroRecords: "Nada encontrado",
                  info: "Registros listados: _END_ ",
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


                
                
