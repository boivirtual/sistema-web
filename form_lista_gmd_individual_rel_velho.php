<?php
    include "conecta_mysql.inc";

    $data_sistema = date("Y-m-d");
    $partes = explode("-", $data_sistema);
    $dia_sistema = $partes[2];
    $mes_sistema = $partes[1];
    $ano_sistema = $partes[0];

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
        background-color: #eee;
      }
      
      table.dataTable.no-footer{
          border: none;
      }

      #tabela_lista_animais_wrapper{
          width: 100% !important;
          overflow-x: scroll !important;
          overflow-y: scroll !important;
          max-height: 400px;
      }

      #tabela_lista_animais_filter{
          float: left;
      }
  </style>
</head>

<body>
	<section class="panel table-responsive">
        <table id="tabela_lista_animais" class="table table-bordered table-advance table-hover" style="width:100%; font-size:10px;">

        <tbody>
            <?php
                $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
                $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
                $animais_listados=0;
                $gmd_total = 0;
                $numero_gmd = 0;

                // LISTA GMD INDIVIDUAL
                if ($tipo_rel=='I') {

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
                            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                            $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
                            $sexo = $reg_animal->tbl_animal_sexo; 
                            $mae = $reg_animal->tbl_animal_codigo_mae; 
                            $pai = $reg_animal->tbl_animal_codigo_pai; 
                            $ativo = $reg_animal->tbl_animal_ativo; 
                            $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
                            $data_nasc_edi = $data_nasc->format('d/m/Y');

                            $data_peso_nascimento=0;
                            $peso_nascimento=0;

                            if ($reg_animal->tbl_animal_primeiro_peso!='') {
                                $data_primeiro_peso = substr($reg_animal->tbl_animal_data_primeiro_peso, 0, 10);

                                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                                    $data_peso_nascimento = $data_primeiro_peso;
                                    $peso_nascimento = $reg_animal->tbl_animal_primeiro_peso;
                                }
                            }

                            if ($codigo_alfa=='') {
                                $codigo_edi = $codigo_numerico;
                            }
                            else {
                                $codigo_edi = $codigo_alfa.'-'.$codigo_numerico;
                            }

                            $tab_fazenda = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$codigo_fazenda'");
                            $num_rows = mysqli_num_rows($tab_fazenda);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_fazenda);
                                $desc_local = $reg->tbl_pessoa_nome;
                            }
                            else {
                                $desc_local = '';
                            }

                            $tab_mae = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$mae'");
                            $num_rows = mysqli_num_rows($tab_mae);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_mae);
                                $descricao_mae = $reg->tbl_animal_codigo_alfa. '-' . $reg->tbl_animal_codigo_numerico;
                            }
                            else {
                                $descricao_mae = '';
                            }

                            $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$pai'");
                            $num_rows = mysqli_num_rows($tab_pai);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_pai);
                                $descricao_pai = $reg->tbl_semem_codigo_alfa;
                                $pai = $reg->tbl_semem_codigo_id;
                            }
                            else {
                                $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$pai'");
                                $num_rows = mysqli_num_rows($tab_pai);

                                if ($num_rows!=0){
                                    $reg = mysqli_fetch_object($tab_pai);
                                    $descricao_pai = $reg->tbl_animal_codigo_alfa. ' ' . $reg->tbl_animal_codigo_numerico;
                                }
                                else {
                                    $descricao_pai = '';
                                }
                            }

                            $tab_raca = mysqli_query($conector, "select * from tabela_racas where tab_codigo_raca='$codigo_raca'");
                            $num_rows = mysqli_num_rows($tab_raca);

                            if ($num_rows!=0){
                                $reg = mysqli_fetch_object($tab_raca);
                                $descricao_raca = $reg->tab_descricao_raca;
                            }
                            else {
                                $descricao_raca = '';
                            }

                            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                            $data_acompanhamento_calculo = $data_final;
                            $date = new DateTime($data_nascimento); // Data de Nascimento
                            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                            $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                WHERE tab_registro_lixeira_categoria_idade='0'");

                            $num_rows = mysqli_num_rows($tbl_categoria);    

                            if ($num_rows!=0) {
                                while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
                                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                                    }
                                }
                            }                   

                            if ($wcategoria=="") {
                                foreach ($array_mes_ano as $value) { 
                                    $array_peso[$value]=0;
                                } 

                                $data_peso_inicial = 0;
                                $peso_inicial = 0;

                                if ($data_peso_nascimento!=0) {
                                    $partes = explode("-", $data_peso_nascimento);
                                    $ano_mes_peso = $partes[0].$partes[1];

                                    for ($i=0; $i < $qtd_meses; $i++) { 
                                        if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                            $array_peso[$ano_mes_peso]=$peso_nascimento;
                                            $data_peso_inicial = $data_peso_nascimento;
                                            $peso_inicial = $peso_nascimento;
                                        }
                                    }
                                }

                                if ($data_peso_inicial==0) {
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

                                        $array_peso[$ano_mes_peso]=$peso;
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
                                    $gmd_edi = number_format($gmd,3,',','.');
                                    $gmd_total+= $gmd;
                                    $numero_gmd++;
                                }
                                else {
                                    $gmd_edi = 0;
                                }

                                echo '<tr>';
                                if ($ativo=='N') {
                                    echo '<td width="5%" style="color: red;">'.$codigo_edi.'</td>';
                                    echo '<td width="29%" style="color: red;">'.$desc_local.'</td>';    
                                    echo '<td width="3%" style="color: red; text-align: center;">'.$sexo.'</td>';   
                                    echo '<td width="5%" style="color: red; text-align: center;">'.$data_nasc_edi.'</td>';
                                    echo '<td width="12%" style="color: red;">'.$descricao_raca.'</td>';    
                                    echo '<td width="5%" style="color: red;">'.$descricao_mae.'</td>';    
                                    echo '<td width="5%" style="color: red;">'.$dias.'</td>';    

                                    foreach ($array_peso as $value) { 
                                        if ($value>0) {
                                            echo '<td width="2.5%" style="color: red; text-align: right;">'.$value.'</td>';
                                        }
                                        else {
                                            echo '<td width="2.5%"></td>';
                                        }
                                    } 

                                    if ($ganho==0) {
                                        echo '<td width="3%" style="color: red;"></td>'; 
                                        echo '<td width="3%" style="color: red;"></td>'; 
                                    }
                                    else {
                                        echo '<td width="3%" style="color: red; text-align: right;">'.$ganho.'</td>'; 
                                        echo '<td width="3%" style="color: red; text-align: right;">'.$gmd_edi.'</td>'; 
                                    }
                                }
                                else {
                                    echo '<td width="5%">'.$codigo_edi.'</td>';    
                                    echo '<td width="29%">'.$desc_local.'</td>';    
                                    echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                                    echo '<td width="5%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                    echo '<td width="12%">'.$descricao_raca.'</td>';    
                                    echo '<td width="5%">'.$descricao_mae.'</td>';    
                                    echo '<td width="5%">'.$dias.'</td>';    

                                    foreach ($array_peso as $value) { 
                                        if ($value>0) {
                                            echo '<td width="2.5%" style="text-align: right;">'.$value.'</td>';
                                        }
                                        else {
                                            echo '<td width="2.5%"></td>';
                                        }
                                    } 

                                    if ($ganho==0) {
                                        echo '<td width="3%"></td>'; 
                                        echo '<td width="3%"></td>'; 
                                    }
                                    else {
                                        echo '<td width="3%" style="text-align: right;">'.$ganho.'</td>'; 
                                        echo '<td width="3%" style="text-align: right;">'.$gmd_edi.'</td>'; 
                                    }
                                }
                                echo '</tr>';
                                $animais_listados++;
                            }
                            else {
                                foreach ($wcategoria as $value) {
                                    if ($value==$codigo_categoria) {

                                        foreach ($array_mes_ano as $value) { 
                                            $array_peso[$value]=0;
                                        } 

                                        $data_peso_inicial = 0;
                                        $peso_inicial = 0;

                                        if ($data_peso_nascimento!=0) {
                                            $partes = explode("-", $data_peso_nascimento);
                                            $ano_mes_peso = $partes[0].$partes[1];

                                            for ($i=0; $i < $qtd_meses; $i++) { 
                                                if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                                    $array_peso[$ano_mes_peso]=$peso_nascimento;
                                                    $data_peso_inicial = $data_peso_nascimento;
                                                    $peso_inicial = $peso_nascimento;
                                                }
                                            }
                                        }

                                        if ($data_peso_inicial==0) {
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

                                                $array_peso[$ano_mes_peso]=$peso;
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
                                            $gmd_edi = number_format($gmd,3,',','.');
                                            $gmd_total+= $gmd;
                                            $numero_gmd++;
                                        }
                                        else {
                                            $gmd_edi = 0;
                                        }

                                        echo '<tr>';
                                        if ($ativo=='N') {
                                            echo '<td width="5%" style="color: red;">'.$codigo_edi.'</td>';    
                                            echo '<td width="29%" style="color: red;">'.$desc_local.'</td>';    
                                            echo '<td width="3%" style="color: red; text-align: center;">'.$sexo.'</td>';   
                                            echo '<td width="5%" style="color: red; text-align: center;">'.$data_nasc_edi.'</td>';
                                            echo '<td width="12%" style="color: red;">'.$descricao_raca.'</td>';    
                                            echo '<td width="5%" style="color: red;">'.$descricao_mae.'</td>';    
                                            echo '<td width="5%" style="color: red;">'.$descricao_pai.'</td>';    

                                            foreach ($array_peso as $value) { 
                                                if ($value>0) {
                                                    echo '<td width="2.5%" style="color: red; text-align: right;">'.$value.'</td>';
                                                }
                                                else {
                                                    echo '<td width="2.5%"></td>';
                                                }
                                            } 

                                            if ($ganho==0) {
                                                echo '<td width="3%" style="color: red;"></td>'; 
                                                echo '<td width="3%" style="color: red;"></td>'; 
                                            }
                                            else {
                                                echo '<td width="3%" style="color: red; text-align: right;">'.$ganho.'</td>'; 
                                                echo '<td width="3%" style="color: red; text-align: right;">'.$gmd_edi.'</td>'; 
                                            }
                                        }
                                        else {
                                            echo '<td width="5%">'.$codigo_edi.'</td>';    
                                            echo '<td width="29%">'.$desc_local.'</td>';    
                                            echo '<td width="3%" style="text-align: center;">'.$sexo.'</td>';   
                                            echo '<td width="5%" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                            echo '<td width="12%">'.$descricao_raca.'</td>';    
                                            echo '<td width="5%">'.$descricao_mae.'</td>';    
                                            echo '<td width="5%">'.$descricao_pai.'</td>';    

                                            foreach ($array_peso as $value) { 
                                                if ($value>0) {
                                                    echo '<td width="2.5%" style="text-align: right;">'.$value.'</td>';
                                                }
                                                else {
                                                    echo '<td width="2.5%"></td>';
                                                }
                                            } 

                                            if ($ganho==0) {
                                                echo '<td width="3%"></td>'; 
                                                echo '<td width="3%"></td>'; 
                                            }
                                            else {
                                                echo '<td width="3%" style="text-align: right;">'.$ganho.'</td>'; 
                                                echo '<td width="3%" style="text-align: right;">'.$gmd_edi.'</td>'; 
                                            }
                                        }

                                        echo '</tr>';
                                        $animais_listados++;
                                    }
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

                $qtd_span = $qtd_meses + 6;

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
                <th colspan="<?php echo $qtd_span?>"></th>
                <th style="text-align: center"></th>
                <th style="text-align: center">GMD Médio</th>
            </tr>
            <tr>
                <td style="text-align: center" class="animais_listados"><?php echo $animais_listados?></td>
                <td colspan="<?php echo $qtd_span?>"></td>
                <td style="text-align: right;"></td>
                <td style="text-align: right;"><?php echo $media_gmd_edi?></td>
            </tr>

            <tr>
                <th>Nº Animal</th>
                <th>Fazenda</th>
                <th>Sexo</th>
                <th>Nascimento</th>
                <th>Raça</th>
                <th>Mãe Id</th>
                <th>Pai Id</th>

                <?php
                    for ($i=0; $i < $qtd_meses; $i++) { 
                        echo '<th style="width: 5%" class="text-center">'.$array_mes[$i].'/'.$array_ano[$i].'</th>';
                    }
                ?>

                <th>Ganho</th>
                <th>GMD</th>
            </tr>
        </thead>
        </table>
    </section>

    <script>
        $(document).ready(function() {
            var table = $('#tabela_lista_animais').DataTable( {
                sDom: 'lfr<"table_overflow"t>ip',
                /* scrollY: "200px", */
                paging:   false,
                search:   true,
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


                
                
