<?php
    include "conecta_mysql.inc";
    $data_sistema = date("Y-m-d");
    $partes = explode("-", $data_sistema);
    $dia_sistema = $partes[2];
    $mes_sistema = $partes[1];
    $ano_sistema = $partes[0];

    $data_inicial = $_POST['data_inicial'];
    $partes = explode("-", $data_inicial);
    $mes_inicial = $partes[1];
    $ano_inicial = $partes[0];

    $data_final = $_POST['data_final'];
	$partes = explode("-", $data_final);
	$mes_final = $partes[1];
	$ano_final = $partes[0];

    $tipo_rel = $_POST['tipo_rel'];

    @ session_start(); 

    $codigo_grupo_usuario = $_SESSION['grupo_usuario'];

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

    $wlocal = "";
    $wlocal_animal = "";

    if (isset($_POST['local'])) {
        $local = $_POST['local'];

        if(in_array("", $local)) {
            $wlocal='';
            $wlocal_animal='';
        }
        else {
            $wlocal = " AND tbl_mov_estoque_local IN(";
            $wlocal.= implode(',', $local);
            $wlocal.= ")";

            $wlocal_animal = " AND tbl_animal_codigo_fazenda IN(";
            $wlocal_animal.= implode(',', $local);
            $wlocal_animal.= ")";

            $local_animal = implode(',', $local);
        }
    }
    else {
        $wlocal='';
        $wlocal_animal='';
    }

    if ($wlocal_animal=='') {
        $local_animal = '';
        $qtd_local = 0;
    } 
    else {
        $locais = explode(",", $local_animal);
        $qtd_local = count($locais);
    }

    $wcategoria = "";
    if (isset($_POST['categoria'])) {
        $categoria_filtro = $_POST['categoria'];

        if(in_array("", $categoria_filtro)) {
            $wcategoria='';
        }
        else {
            //$wcategoria= explode(',', $categoria_filtro);
            $wcategoria=$categoria_filtro;
       }
    }
    else {
        $wcategoria='';
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

</head>

<body>
	<section class="panel"  style="overflow-x:auto">
        <table id="tabela_lista_estoque" class="table table-bordered table-advance table-hover table-reponsive" 
        style="width:100%; font-size:10px;">

        <thead>
            <?php
	            echo '<div class="row col-md-12 filtro_escondido" id="total_contas">';

                echo '<div class="form-group col-md-9">';
                echo '<p id="descricao_filtro"
                    class="text-muted" style="font-size: 12px; color: #829c9c"></p>';
                echo '</div>';

	            echo '<div class="form-group col-md-1">';
	            echo '<button type="button" class="form-control btn btn-success pull-right"
	                onClick="lista_estoque_excel()">Excel</button>';
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

	            if ($tipo_rel=='C') {
                    echo '<tr>';
                    echo '<th class="text-center" rowspan="2">Meses</th>';
                    echo '<th class="text-center" rowspan="2">Estoque Inicial</th>';
                    echo '<th class="text-center" colspan="4">Entradas</th>';
                    echo '<th class="text-center" colspan="4">Saídas</th>';
                    echo '<th class="text-center"rowspan="2">Estoque Final</th>';
                    echo'</tr>';

                    echo '<tr>';
                    echo '<th class="text-center">Nascimento</th>';
                    echo '<th class="text-center">Compra</th>';
                    echo '<th class="text-center">Transferência</th>';
                    echo '<th class="text-center">Outras Entradas</th>';
                    echo '<th class="text-center">Morte</th>';
                    echo '<th class="text-center">Venda</th>';
                    echo '<th class="text-center">Transferência</th>';
                    echo '<th class="text-center">Outras Saídas</th>';
                    echo '</tr>';
	            }
	            else {
                    echo '<tr>';
                    echo '<th width="8%" class="text-center" rowspan="2">Meses</th>';
                    echo '<th width="42%" class="text-center" colspan="5">Fêmeas</th>';
                    echo '<th width="42%" class="text-center" colspan="5">Machos</th>';
                    echo '<th width="8%" class="text-center"rowspan="2">Totais</th>';
                    echo'</tr>';

                    echo '<tr>';
                    echo '<th width="9%" class="text-center">0 a 7 meses</th>';
                    echo '<th width="8% "class="text-center">8 a 12 meses</th>';
                    echo '<th width="8% "class="text-center">13 a 24 meses</th>';
                    echo '<th width="8%" class="text-center">25 a 36 meses</th>';
                    echo '<th width="9% "class="text-center">> 36 meses</th>';
                    echo '<th width="9%" class="text-center">0 a 7 meses</th>';
                    echo '<th width="8% "class="text-center">8 a 12 meses</th>';
                    echo '<th width="8% "class="text-center">13 a 24 meses</th>';
                    echo '<th width="8%" class="text-center">25 a 36 meses</th>';
                    echo '<th width="9% "class="text-center">> 36 meses</th>';
                    echo '</tr>';
	            }
            ?>
        </thead>


        <tbody style="margin:0; padding: 0" >
            <?php
                // LISTA ESTOQUE CABECA
                if ($tipo_rel=='C') {

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

                    $data_inicial = $data_inicial . '-01';
                    $data_final = $data_final . '-31';

                    // Pega estoque anterior a data inicial
                    $movimentacao_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                            WHERE tbl_mov_estoque_data_emissao<'$data_inicial'" . 
                            $wlocal);

                    $num_rows = mysqli_num_rows($movimentacao_estoque);

                    if ($num_rows!=0) {
                        while ($reg_mov = mysqli_fetch_object($movimentacao_estoque)) {
                            $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                            $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                            if ($ent_sai=='E') {
                                if ($tipo=='N') {
                                    $estoque_ent_nasc++;   
                                }
                                else if ($tipo=='C') {
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

                            $estoque_inicial = $estoque_ent_nasc + $estoque_ent_compra + 
                                               $estoque_ent_transf + $estoque_ent_outra;

                            $estoque_inicial = $estoque_inicial - $estoque_sai_morte - 
                                             $estoque_sai_venda - $estoque_sai_transf -
                                             $estoque_sai_outra;
                        }
                    }
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

                        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                            WHERE year(tbl_mov_estoque_data_emissao)='$ano_lista' AND 
                                  month(tbl_mov_estoque_data_emissao)='$mes_lista'" . 
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
                                    else if ($tipo=='C') {
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

                            $estoque_final = $estoque_inicial + $estoque_ent_nasc +
                                             $estoque_ent_compra + $estoque_ent_transf + $estoque_ent_outra;

                            $estoque_final = $estoque_final - $estoque_sai_morte - 
                                             $estoque_sai_venda - $estoque_sai_transf -
                                             $estoque_sai_outra;
                        }
                        else {
                            $estoque_final = $estoque_inicial;
                        }

	                	echo '<tr>';
	                    echo '<td width="8%" class="text-right">'.$array_mes_extenco[$i].'</td>';
	                    echo '<td width="8%" class="text-center">'.$estoque_inicial.'</td>';
	                    echo '<td width="12%" class="text-center">'.$estoque_ent_nasc.'</td>';
	                    echo '<td width="13%" class="text-center">'.$estoque_ent_compra.'</td>';
	                    echo '<td width="13%" class="text-center">'.$estoque_ent_transf.'</td>';
                        echo '<td width="13%" class="text-center">'.$estoque_ent_outra.'</td>';
	                    echo '<td width="9%" class="text-center">'.$estoque_sai_morte.'</td>';
	                    echo '<td width="9%" class="text-center">'.$estoque_sai_venda.'</td>';
	                    echo '<td width="10%" class="text-center">'.$estoque_sai_transf.'</td>';
	                    echo '<td width="10%" class="text-center">'.$estoque_sai_outra.'</td>';
	                    echo '<td width="8%" class="text-center">'.$estoque_final.'</td>';
		               	echo '</tr>';

                        $estoque_inicial = $estoque_final;
                	}
                } 
                // LISTA ESTOQUE CATEGORIA  
                else {

                    $data_inicial = $data_inicial . '-01';
                    $data_final = $data_final . '-31';

                    for ($j=1; $j<=5; $j++) { 
                        $j = intval(str_pad($j, 3, "0", STR_PAD_LEFT));
                        $qtd_media_femea[$j]=0;
                        $qtd_media_macho[$j]=0;
                        $valor_media_femea[$j]=0;
                        $valor_media_macho[$j]=0;
                        $total_media_femea[$j]=0;
                        $total_media_macho[$j]=0;
                    }

                    for ($i=0; $i<$qtd_meses; $i++) { 
                        echo '<tr>';
                        echo '<td width="8%" class="text-right">'.$array_mes_extenco[$i].'</td>';

                        $mes_lista = $array_mes[$i];
                        $ano_lista = $array_ano[$i];
						$ano_mes_lista = $ano_lista.$mes_lista;

                        if ($mes_lista==$mes_sistema && $ano_lista==$ano_sistema){
                            $dia_lista = $dia_sistema;
                        }
                        else {
                            $dia_lista = cal_days_in_month(CAL_GREGORIAN, $mes_lista, $ano_lista);
                        }

                        $data_mov_estoque = $ano_lista.'-'.$mes_lista.'-'.$dia_lista;

                        $data_calculo = $ano_lista.'-'.$mes_lista.'-'.$dia_lista;

                        for ($j=1; $j<=5; $j++) { 
                            $j = intval(str_pad($j, 3, "0", STR_PAD_LEFT));
                            $array_macho[$j]=0;
                            $array_femea[$j]=0;
                        }

                        $total = 0;
                        $total_baixado = 0;

                        $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
                            WHERE tbl_animal_lixeira=0");

                        $num_rows = mysqli_num_rows($tbl_animais);  
                        if ($num_rows!=0) {
                            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                                $codigo_animal_id = $reg_animal->tbl_animal_codigo_id;
                                $codigo_animal_alfa = $reg_animal->tbl_animal_codigo_alfa;
                                $codigo_animal = $reg_animal->tbl_animal_codigo_numerico;
                                $data_nasc = $reg_animal->tbl_animal_data_nascimento;
                                $ano_mes_nasc = substr($data_nasc, 0, 4).substr($data_nasc, 5, 2);
                                $sexo = $reg_animal->tbl_animal_sexo;
                                $ativo = $reg_animal->tbl_animal_ativo;
                                $data_baixa = $reg_animal->tbl_animal_baixado_em;
                                $ano_baixa = substr($data_baixa, 0, 4);
                                $mes_baixa = substr($data_baixa, 5, 2);
                                $local_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
                                $local_anterior = $reg_animal->tbl_animal_codigo_fazenda_anterior;

                                // LISTA TODOS OS LOCAIS
                                if ($qtd_local==0) {
                                    $data_inicial = $data_nasc;
                                    $data_final = $data_calculo;
                                    $diferenca = strtotime($data_final) - 
                                                 strtotime($data_inicial);
                                    $idade = floor($diferenca / (60 * 60 * 24 * 30));
                                    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

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
                                                $codigo_categoria = intval(str_pad($reg_categoria->tab_codigo_categoria_idade, 3, "0", STR_PAD_LEFT)) ;

                                                if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                                                    $desc_categoria=' > 36 meses';
                                                }
                                                else {
                                                    $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
                                                }

                                                if ($sexo=='M'){
                                                    $array_macho[$codigo_categoria]++;
                                                    $total++;
                                                }
                                                else {
                                                    $array_femea[$codigo_categoria]++;
                                                    $total++;
                                                }

                                                if ($ativo!="S" && ($mes_baixa<=$mes_lista || $ano_baixa<=$ano_lista)) {
                                                    if ($sexo=='M'){
                                                        $array_macho[$codigo_categoria]--;
                                                        $total--;
                                                    }
                                                    else {
                                                        $array_femea[$codigo_categoria]--;
                                                        $total--;
                                                    }
                                                } 

                                                if ($ano_mes_nasc>$ano_mes_lista) {
                                                	if ($sexo=='M'){
                                                        $array_macho[$codigo_categoria]--;
                                                        $total--;
                                                    }
                                                    else {
                                                        $array_femea[$codigo_categoria]--;
                                                        $total--;
                                                    }

                                                }

											    $estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
											        WHERE tbl_mov_estoque_codigo_id_animal='$codigo_animal_id' AND   
											              tbl_mov_estoque_entrada_saida='S' AND tbl_mov_estoque_tipo_movimentacao!='T' AND tbl_mov_estoque_data_emissao>'$data_mov_estoque'");

											    $num_rows_est = mysqli_num_rows($estoque);

											    if ($num_rows_est!=0) {
											        while ($reg_est = mysqli_fetch_object($estoque)) {
											            $data_transf = $reg_est->tbl_mov_estoque_data_emissao;
											            $ano_transf = substr($data_transf, 0, 4);
											            $mes_transf = substr($data_transf, 5, 2);
											            if ($sexo=='M'){
											                $array_macho[$codigo_categoria]++;
											                $total++;
											            }
											            else {
											                $array_femea[$codigo_categoria]++;
											                $total++;
											            }
											        }                               
											    }  
                                            }
                                        }
                                    } 
                                }
                                // LISTA POR LOCAL
                                else {
                                	foreach ($locais as $value) {
                                		if ($local_fazenda==$value){

		                                    $data_inicial = $data_nasc;
		                                    $data_final = $data_calculo;
		                                    $diferenca = strtotime($data_final) - 
		                                                 strtotime($data_inicial);
		                                    $idade = floor($diferenca / (60 * 60 * 24 * 30));
		                                    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

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
		                                                $codigo_categoria = intval(str_pad($reg_categoria->tab_codigo_categoria_idade, 3, "0", STR_PAD_LEFT)) ;

		                                                if ($reg_categoria->tab_categoria_idade_ate==999999999) {
		                                                    $desc_categoria=' > 36 meses';
		                                                }
		                                                else {
		                                                    $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
		                                                }

		                                                if ($sexo=='M'){
		                                                    $array_macho[$codigo_categoria]++;
		                                                    $total++;
		                                                }
		                                                else {
		                                                    $array_femea[$codigo_categoria]++;
		                                                    $total++;
		                                                }

		                                                if ($ativo!="S" && ($mes_baixa<=$mes_lista || $ano_baixa<=$ano_lista)) {
		                                                    if ($sexo=='M'){
		                                                        $array_macho[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                    else {
		                                                        $array_femea[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                }

		                                                if ($ano_mes_nasc>$ano_mes_lista && $local_anterior==0) {
		                                                	if ($sexo=='M'){
		                                                        $array_macho[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                    else {
		                                                        $array_femea[$codigo_categoria]--;
		                                                        $total--;
		                                                    }

		                                                } 

														$estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
														WHERE tbl_mov_estoque_codigo_id_animal='$codigo_animal_id' AND   
														    tbl_mov_estoque_entrada_saida='S' AND tbl_mov_estoque_data_emissao>'$data_mov_estoque' AND tbl_mov_estoque_local='$local_fazenda' AND tbl_mov_estoque_tipo_movimentacao!='T'");

														$num_rows_est = mysqli_num_rows($estoque);
														if ($num_rows_est!=0) {
														    while ($reg_est = mysqli_fetch_object($estoque)) {
														        $data_transf = $reg_est->tbl_mov_estoque_data_emissao;
														        $ano_transf = substr($data_transf, 0, 4);
														        $mes_transf = substr($data_transf, 5, 2);
														        if ($sexo=='M'){
														            $array_macho[$codigo_categoria]++;
														            $total++;
														        }
														        else {
														            $array_femea[$codigo_categoria]++;
														            $total++;
														        }
														    }                               
													    }  

														$estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
														WHERE tbl_mov_estoque_codigo_id_animal='$codigo_animal_id' AND tbl_mov_estoque_tipo_movimentacao='T'");

														$num_rows_est = mysqli_num_rows($estoque);
														if ($num_rows_est!=0) {
														    while ($reg_est = mysqli_fetch_object($estoque)) {
														        $data_transf = $reg_est->tbl_mov_estoque_data_emissao;
														        $ano_mes_transf = substr($data_transf, 0, 4).substr($data_transf, 5, 2);
														        $origem_transf = $reg_est->tbl_mov_estoque_local_origem;
														        $ent_sai_transf = $reg_est->tbl_mov_estoque_entrada_saida;

														        if ($origem_transf!=$value && $ano_mes_transf>$ano_mes_lista && $ent_sai_transf=='S') {

														           if ($sexo=='M'){
														                $array_macho[$codigo_categoria]--;
														                $total--;
														            }
														            else {
														                $array_femea[$codigo_categoria]--;
														                $total--;
														            }

														        }
															}                               
														}  
		                                            }
		                                        }
		                                    } 
                                		}
                                		else if ($local_anterior==$value){
		                                    $data_inicial = $data_nasc;
		                                    $data_final = $data_calculo;
		                                    $diferenca = strtotime($data_final) - 
		                                                 strtotime($data_inicial);
		                                    $idade = floor($diferenca / (60 * 60 * 24 * 30));
		                                    $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);

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
		                                                $codigo_categoria = intval(str_pad($reg_categoria->tab_codigo_categoria_idade, 3, "0", STR_PAD_LEFT)) ;

		                                                if ($reg_categoria->tab_categoria_idade_ate==999999999) {
		                                                    $desc_categoria=' > 36 meses';
		                                                }
		                                                else {
		                                                    $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
		                                                }

		                                                if ($sexo=='M'){
		                                                    $array_macho[$codigo_categoria]++;
		                                                    $total++;
		                                                }
		                                                else {
		                                                    $array_femea[$codigo_categoria]++;
		                                                    $total++;
		                                                }

		                                            /*    if ($ativo!="S" && ($mes_baixa<=$mes_lista || $ano_baixa<=$ano_lista)) {
		                                                    if ($sexo=='M'){
		                                                        $array_macho[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                    else {
		                                                        $array_femea[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                }*/

		                                                if ($ano_mes_nasc>$ano_mes_lista && $local_anterior==0) {

		                                                	if ($sexo=='M'){
		                                                        $array_macho[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                    else {
		                                                        $array_femea[$codigo_categoria]--;
		                                                        $total--;
		                                                    }
		                                                }

													    $estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
													        WHERE tbl_mov_estoque_codigo_id_animal='$codigo_animal_id' AND   
													              tbl_mov_estoque_entrada_saida='S' AND tbl_mov_estoque_data_emissao>'$data_mov_estoque' AND tbl_mov_estoque_local='$local_fazenda' AND tbl_mov_estoque_tipo_movimentacao!='T'");

													    $num_rows_est = mysqli_num_rows($estoque);
													    if ($num_rows_est!=0) {
													        while ($reg_est = mysqli_fetch_object($estoque)) {
													            $data_transf = $reg_est->tbl_mov_estoque_data_emissao;
													            $ano_transf = substr($data_transf, 0, 4);
													            $mes_transf = substr($data_transf, 5, 2);
													            if ($sexo=='M'){
													                $array_macho[$codigo_categoria]++;
													                $total++;
													            }
													            else {
													                $array_femea[$codigo_categoria]++;
													                $total++;
													            }
													        }                               
													    }  

														$estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
														    WHERE tbl_mov_estoque_codigo_id_animal='$codigo_animal_id' AND tbl_mov_estoque_tipo_movimentacao='T'");

													    $num_rows_est = mysqli_num_rows($estoque);
													    if ($num_rows_est!=0) {
													        while ($reg_est = mysqli_fetch_object($estoque)) {
													            $data_transf = $reg_est->tbl_mov_estoque_data_emissao;
													            $ano_mes_transf = substr($data_transf, 0, 4).substr($data_transf, 5, 2);
													            $origem_transf = $reg_est->tbl_mov_estoque_local_origem;
													            $ent_sai_transf = $reg_est->tbl_mov_estoque_entrada_saida;

													            if ($origem_transf==$value && $ano_mes_transf<=$ano_mes_lista && $ent_sai_transf=='S') {

														            if ($sexo=='M'){
														                $array_macho[$codigo_categoria]--;
													                    $total--;
														            }
														            else {
														                $array_femea[$codigo_categoria]--;
														                $total--;
														            }
														        }
														    }                               
														}  
		                                            }
		                                        }
		                                    } 

                                		}
                                	}
                                }
                            }

                            for ($k=1; $k <=5; $k++) { 
                                if ($array_femea[$k]==0) {
                                    $array_femea[$k]='';
                                }
                                else {
                                    $qtd_media_femea[$k]++;
                                    $valor_media_femea[$k]+=$array_femea[$k];
                                }
                                if ($array_macho[$k]==0) {
                                    $array_macho[$k]='';
                                }
                                else {
                                    $qtd_media_macho[$k]++;
                                    $valor_media_macho[$k]+=$array_macho[$k];
                                }
                            }

                            echo '<td width="8%" class="text-right">'.$array_femea[1].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_femea[2].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_femea[3].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_femea[4].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_femea[5].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_macho[1].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_macho[2].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_macho[3].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_macho[4].'</td>';
                            echo '<td width="8%" class="text-right">'.$array_macho[5].'</td>';
                            echo '<td width="8%" class="text-right">'.$total.'</td>';
                        }
                        echo '</tr>';
                    }
                } 
            ?>
        </tbody>
            <?php
                if ($tipo_rel=='M') {
                    for ($k=1; $k <=5; $k++) { 
                        if ($valor_media_femea[$k]!=0 && $qtd_media_femea[$k]!=0) {
                            $total_media_femea[$k]=$valor_media_femea[$k]/$qtd_media_femea[$k];
                        }

                        if ($valor_media_macho[$k]!=0 && $qtd_media_macho[$k]!=0) {
                            $total_media_macho[$k]=$valor_media_macho[$k]/$qtd_media_macho[$k];
                        }
                    }

                    echo '<tfoot>';    
                    echo '<tr>';        
                    echo '<th>Média do Período</th>';            
                    echo '<th class="text-right">'.number_format($total_media_femea[1],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_femea[2],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_femea[3],2,',','.').'</th>';         
                    echo '<th class="text-right">'.number_format($total_media_femea[4],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_femea[5],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_macho[1],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_macho[2],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_macho[3],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_macho[4],2,',','.').'</th>';
                    echo '<th class="text-right">'.number_format($total_media_macho[5],2,',','.').'</th>';
                    echo '<th class="text-right"></th>';
                    echo '</tr>';                   
                    echo '</tfoot>';    
                }
            ?>
        </table>
    </section>

    <script>
        $(document).ready(function() {
            var table = $('#tabela').DataTable( {
                scrollY: "250px",
                scrollX:  false,
                paging:   false,
                search:   false,
                info: false,
                ordering: false,
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


                
                
