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

    $tipo_rel = $_POST['tipo_rel'];

    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

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
            }
    }
    else {
        $wlocal='';
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

    if ($tipo_rel=="I") {
        $desc_rel='Individual';
    }
    else {
        $desc_rel='Geral';
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
  <style>
      div.dataTables_scrollBody{
          overflow-x: hidden !important;
      }
  </style>

</head>

<body>
	<section class="panel">
        <table id="tabela_lista_animais" class="table table-bordered table-advance table-hover" 
        style="width:100%; font-size:10px;">
        <?php
        /*    if ($tipo_rel=='I') {
                echo '<table id="tabela_lista_animais" class="table table-bordered table-advance table-hover table-reponsive" style="width:100%; font-size:10px; margin-left: 1%; margin-right: 1%;">';
            }
            else {
                echo '<table id="tabela_lista_animais" class="table table-bordered table-advance table-hover table-reponsive" style="width:50%; font-size:10px; margin-left: 1%; margin-right: 1%;">';
            }*/
        ?>

        <tbody>
            <?php
                $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
                $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
                $animais_listados=0;

                // LISTAGMD INDIVIDUAL
                if ($tipo_rel=='I') {
                    $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
                        WHERE tbl_animal_lixeira=0 AND 
                              tbl_animal_ativo='$wativo'" . $wlocal . $wsexo . $wraca . $wpai . 
                              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
                    " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"); 

                    $num_rows_animais = mysqli_num_rows($tbl_animal);

                    if ($num_rows_animais!=0) {
                        while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
                            $codigo = $reg_animal->tbl_animal_codigo_id;
                            $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
                            $codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
                            $codigo_raca = $reg_animal->tbl_animal_codigo_raca;
                            $codigo_pelagem = $reg_animal->tbl_animal_codigo_pelagem;
                            $codigo_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
                            $sexo = $reg_animal->tbl_animal_sexo; 
                            $mae = $reg_animal->tbl_animal_codigo_mae; 
                            $pai = $reg_animal->tbl_animal_codigo_pai; 
                            $ativo = $reg_animal->tbl_animal_ativo; 
                            $data_nasc = new DateTime($reg_animal->tbl_animal_data_nascimento);
                            $data_nasc_edi = $data_nasc->format('d/m/Y');
                            $peso_nasc = $reg_animal->tbl_animal_primeiro_peso; 
                            $peso_nasc_edi = number_format($peso_nasc,2,',','.');
                            $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                            $peso_desmama_edi = number_format($peso_desmama,2,',','.');
                            $peso_ultimo = $reg_animal->tbl_animal_ultimo_peso; 
                            $peso_ultimo_edi = number_format($peso_ultimo,2,',','.');
                            $data_ultimo = new DateTime($reg_animal->tbl_animal_data_ultimo);
                            $data_ultimo_edi = $data_ultimo->format('d/m/Y');

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

                            $data_inicio = $reg_animal->tbl_animal_data_nascimento;
                            $data_fim = $data_final;
                            $diferenca = strtotime($data_fim) - 
                                         strtotime($data_inicio);
                            $idade = floor($diferenca / (60 * 60 * 24 * 30));
                            $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

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
                                echo '<tr>';
                                echo '<td width="5%" class="codigo">'.$codigo_edi.'</td>';    
                                echo '<td width="37%" class="local">'.$desc_local.'</td>';    
                                echo '<td width="3%" class="sexo" style="text-align: center;">'.$sexo.'</td>';   
                                echo '<td width="5%" class="data_nasc" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                echo '<td width="15%" class="raca">'.$descricao_raca.'</td>';    
                                echo '<td width="5%" class="mae">'.$descricao_mae.'</td>';    
                                echo '<td width="5%" class="pai">'.$descricao_pai.'</td>';    
                                echo '</tr>';
                                $animais_listados++;
                            }
                            else {
                                foreach ($wcategoria as $value) {
                                    if ($value==$codigo_categoria) {
                                    echo '<tr>';
                                    echo '<td width="5%" class="codigo">'.$codigo_edi.'</td>';    
                                    echo '<td width="37%" class="local">'.$desc_local.'</td>';    
                                    echo '<td width="3%" class="sexo" style="text-align: center;">'.$sexo.'</td>';   
                                    echo '<td width="5%" class="data_nasc" style="text-align: center;">'.$data_nasc_edi.'</td>';
                                    echo '<td width="15%" class="raca">'.$descricao_raca.'</td>';    
                                    echo '<td width="5%" class="mae">'.$descricao_mae.'</td>';    
                                    echo '<td width="5%" class="pai">'.$descricao_pai.'</td>';    
                                    echo '</tr>';
                                    $animais_listados++;
                                    }
                                }
                            }
                        }
                    }
                }
                else {
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
                        }
                    }   

                    $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
                        WHERE tbl_animal_lixeira=0 AND 
                              tbl_animal_ativo='$wativo'" . $wlocal . $wsexo . $wraca . $wpai . 
                              $wmae . $wpeso_nasc . $wpeso_desmama . $wpeso_ult . $wdata_nasc .
                    " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"); 

                    $num_rows_animais = mysqli_num_rows($tbl_animal);

                    if ($num_rows_animais!=0) {
                        while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
                            $sexo = $reg_animal->tbl_animal_sexo; 
                            $data_inicio = $reg_animal->tbl_animal_data_nascimento;
                            $data_fim = $data_final;
                            $diferenca = strtotime($data_fim) - 
                                         strtotime($data_inicio);
                            $idade = floor($diferenca / (60 * 60 * 24 * 30));
                            $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

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

                            if ($sexo=="M") {
                                $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                            }
                            else {
                                $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                            }

                            $gmd = 153;
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
                                echo '<td width="10%" style="text-align: right;">'.$gmd.'</td>';    
                                echo '</tr>';
                                $animais_listados+=$array_qtd_macho_categoria[$j];
                            }

                            if ($array_qtd_femea_categoria[$j]!=0) {
                                echo '<tr>';
                                echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';    
                                echo '<td width="10%" style="text-align: center;">F</td>';   
                                echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';
                                echo '<td width="10%" style="text-align: right;">'.$gmd.'</td>';    
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
                                        echo '<td width="10%" style="text-align: right;">'.$gmd.'</td>';    
                                        echo '</tr>';
                                        $animais_listados+=$array_qtd_macho_categoria[$j];
                                    }

                                    if ($array_qtd_femea_categoria[$j]!=0) {
                                        echo '<tr>';
                                        echo '<td width="25%">'.$array_desc_categoria[$j].'</td>';
                                        echo '<td width="10%" style="text-align: center;">F</td>';
                                        echo '<td width="10%" style="text-align: center;">'.$array_qtd_femea_categoria[$j].'</td>';
                                        echo '<td width="10%" style="text-align: right;">'.$gmd.'</td>';    
                                        echo '</tr>';
                                        $animais_listados+=$array_qtd_femea_categoria[$j];
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
                echo '<div class="row filtro_escondido" id="total_contas">';

                echo '<div class="col-md-9">';
                echo '<p id="descricao_filtro"
                    class="text-muted" style="font-size: 12px; color: #829c9c"></p>';
                echo '</div>';

                echo '<div class="col-md-1">';
                echo '<button type="button" class="form-control btn btn-success pull-right"
                    onClick="lista_estoque_excel()">Excel</button>';
                echo '</div>';

                echo '<div class="col-md-1">';
                echo '<button type="button" class="form-control btn btn-info pull-right exibir"
                    data-toggle="tooltip" data-placement="bottom" title="Maximizar tela filtros" onClick="exibir_filtro()"><i class="fa fa-sort-up"></i>&nbsp;<i class="fa fa-filter"></i></button>';

                echo '<button type="button" class="form-control btn btn-info pull-right esconder" hidden=""
                    data-toggle="tooltip" data-placement="bottom" title="Minimizar tela filtros" onClick="esconder_filtro()"><i class="fa fa-sort-down"></i>&nbsp;<i class="fa fa-filter"></i></button>';
                echo '</div>';

                echo '<div class="col-md-1 voltar">';
                echo '<button type="button" class="form-control btn btn-info pull-right" onclick="onclick=voltar_relatorios()">Voltar</button>';
                echo '</div>';

                echo '</div>';

                echo '<div class="row col-md-12">';

                echo '<div class="form-group col-md-3">';
                echo '<p class="text-muted" style="font-size: 12px; color: #829c9c">Período da Pesagem: ' . '01' .'/'. $mes_inicial .'/'. $ano_inicial .' até '. 
                    $dia_final .'/'. $mes_final .'/'. $ano_final . '</p>';
                echo '</div>';

                echo '</div>';

                if ($tipo_rel=='I') {
                    echo '<tr>';
                    echo '<th class="text-center">Animais</th>';
                    $qtd_span = $qtd_meses + 6;
                    echo '<th style="width: 5%" class="text-center" colspan="'.$qtd_span.'"></th>';
                    echo '<th class="text-center"></th>';
                    echo '<th class="text-center">GMD Médio</th>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td style="text-align: center" class="animais_listados">'.$animais_listados.'</td>';
                    echo '<th style="width: 5%" class="text-center" colspan="'.$qtd_span.'"></th>';
                    echo '<th class="text-center"></th>';
                    echo '<th class="text-center"></th>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th class="text-center">Nº Animal</th>';
                    echo '<th class="text-center">Fazenda</th>';
                    echo '<th class="text-center">Sexo</th>';
                    echo '<th class="text-center">Nascimento</th>';
                    echo '<th class="text-center">Raça</th>';
                    echo '<th class="text-center">Mãe</th>';
                    echo '<th class="text-center">Pai</th>';

                    for ($i=0; $i < $qtd_meses; $i++) { 
                        echo '<th style="width: 5%" class="text-center">'.$array_mes[$i].'/'.$array_ano[$i].'</th>';
                    }

                    echo '<th class="text-center">Ganho</th>';
                    echo '<th class="text-center">GMD</th>';
                    echo '</tr>';
                }
                else {
                    echo '<tr>';
                    echo '<th class="text-center">Animais</th>';
                    echo '<th style="width: 5%" class="text-center" colspan="2"></th>';
                    echo '<th class="text-center">GMD Global</th>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<td style="text-align: center" class="animais_listados">'.$animais_listados.'</td>';
                    echo '<th style="width: 5%" class="text-center" colspan="2"></th>';
                    echo '<th class="text-center"></th>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th class="text-center">Categoria</th>';
                    echo '<th class="text-center">Sexo</th>';
                    echo '<th class="text-center">Qtde</th>';
                    echo '<th class="text-center">GMD</th>';
                    echo '</tr>';

                }
            ?>
        </thead>

        </table>
    </section>

    <script>
        $(document).ready(function() {
            var table = $('#tabela').DataTable( {
                scrollY: "200px",
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


                
                
