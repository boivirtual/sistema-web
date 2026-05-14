<?php
// ANTIGO LER_ANIMAL_FILTRO, MUDOU DE NOME PARA PODER ATENDER A ROTINA DE MOVIMENTACAO DE MORTE E OUTRA SADA - 28/10/2025
include "conecta_mysql.inc";

for ($i=1; $i<=30; $i++){
    $valor[$i]=0;
}

$codigo_categoria = 0;
$tem_categoria = 'N';
$tem_origem = 'N';
$tem_raca = 'N';
$tem_pai = 'N';
$tem_mae = 'N';
$tem_sexo = 'S';
$tem_data_nasc = 'N';
$tem_peso_nasc = 'N';
$tem_peso_desmama = 'N';
$tem_peso_ult = 'N';
$tem_solteira = 'N';
$tem_parida = 'N';
$tem_descarte = 'N';
$tem_parto = '';
$tem_aborto = '';
$ultimo_parto = '0000-00-00';
$tem_previsao_parto = "N";
$tem_positivo = 'N';
$tem_negativo = 'N';

$codigo_alfa_numerico = $_POST['id_animal'];  
$local_filtro = $_POST['local'];  

if (isset($_POST['origem'])) {
	$origem_filtro = $_POST['origem'];  
}
else {
	$origem_filtro = ''; 
}
 
$categoria_filtro = $_POST['categoria']; 
$sexo_filtro = $_POST['sexo'];  
$raca_filtro = $_POST['raca'];  
$pai_filtro = $_POST['pai'];  
$mae_filtro = $_POST['mae'];  
$data_nasc_inicial = $_POST['data_nasc_inicial'];
$data_nasc_final = $_POST['data_nasc_final'];
$peso_nasc_inicial = $_POST['peso_nasc_inicial'];
$peso_nasc_final = $_POST['peso_nasc_final'];
$peso_desmama_inicial = $_POST['peso_desmama_inicial'];
$peso_desmama_final = $_POST['peso_desmama_final'];
$peso_ult_inicial = $_POST['peso_ult_inicial'];
$peso_ult_final = $_POST['peso_ult_final'];
$num_parto_de = $_POST['num_parto_de'];
$num_parto_ate = $_POST['num_parto_ate'];
$num_aborto_de = $_POST['num_aborto_de'];
$num_aborto_ate = $_POST['num_aborto_ate'];
$solteiras = $_POST["solteiras"];
$descarte = $_POST["descarte"];
$paridas = $_POST["paridas"];
$data_paridas_ate = $_POST["data_paridas"];
$previsao_parto_de = $_POST["previsao_parto_de"];
$previsao_parto_ate = $_POST["previsao_parto_ate"];
$positivo = $_POST["positivo"];
$negativo = $_POST["negativo"];

if (isset($_POST['estacao'])) {
    $estacao_filtro = $_POST['estacao'];

    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
        WHERE tbl_par_estacao_nome='$estacao_filtro'
        ORDER BY tbl_par_estacao_id ASC");  

    $num_rows = mysqli_num_rows($sql);
    $array_estacao = array();

    if ($num_rows!=0) {
        while ($reg_estacao = mysqli_fetch_object($sql)){
            $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
            $array_estacao[] = $codigo_estacao;
        }

        $array_estacao = implode(',', $array_estacao);
    }
}

$westacao = "";
if (!empty ($array_estacao)) {
    
    $array_estacao = explode(',', $array_estacao);

    $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
    $westacao.= implode(',', $array_estacao);
    $westacao.= ")";
}

if ($data_paridas_ate=='') {
    $data_paridas_ate='9999-99-99';
    $data_paridas_de='0000-00-00';
}
else {
    $data_paridas_de = date("Y-m-d", strtotime('-8 month',strtotime($data_paridas_ate)));
    $data_paridas_de = date("Y-m-d", strtotime('-1 day',strtotime($data_paridas_de)));
}

$codigo_numerico = substr($codigo_alfa_numerico, -9);

if (strlen($codigo_alfa_numerico)!=9){
	$data = explode("-", $codigo_alfa_numerico);
	$codigo_alfa = $data[0];
}
else {
	$codigo_alfa = '';
}

$sql = "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
          tbl_animal_codigo_numerico='$codigo_numerico' AND
          tbl_animal_ativo='S' AND 
          tbl_animal_lixeira=0";


$tbl_animal = mysqli_query($conector, $sql);
$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal);
	$codigo_id = $reg_animal->tbl_animal_codigo_id;
	$nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);
	$codigo_origem = $reg_animal->tbl_animal_codigo_origem;
	$raca = $reg_animal->tbl_animal_codigo_raca;
	$pelagem = $reg_animal->tbl_animal_codigo_pelagem;
	$local = $reg_animal->tbl_animal_codigo_fazenda;
	$sexo_animal = $reg_animal->tbl_animal_sexo;
	$pai_animal = $reg_animal->tbl_animal_codigo_pai;
	$mae_animal = $reg_animal->tbl_animal_codigo_mae;
	$data_nasc_animal = $reg_animal->tbl_animal_data_nascimento;
	$peso_nasc_animal = $reg_animal->tbl_animal_primeiro_peso;
	$peso_desmama_animal = $reg_animal->tbl_animal_peso_desmama;
	$peso_ult_animal = $reg_animal->tbl_animal_ultimo_peso;
	$animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

    $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
    $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
    $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

    if ($ultimo_peso!=0 && $ultimo_peso!='') {
        $peso = $ultimo_peso;
    }
    else if ($peso_desmama!=0 && $peso_desmama!='') {
        $peso = $peso_desmama;
    }
    else if ($primeiro_peso!=0 && $primeiro_peso!=''){
        $peso = $primeiro_peso;
    }
    else {
        $peso = 0;
    }

	if ($animal_descarte=='S') {
		$tem_descarte = 'S';
	}

	if ($reg_animal->tbl_animal_sexo=='M') {
		$sexo = 'Macho';
	}
	else {
		$sexo = 'Fêmea';
	}

	if ($local!=$local_filtro) {
		$valor[0]='Nao tem animal';
		$valor[1]='Animal não consta no local ou Id não cadastrado.';
		$str=$valor[0] . '<|>' . $valor[1] . '<|>';
		echo $str; 
		mysqli_close($conector);
		exit;	
	}

	if ($sexo_filtro!='Todos' && $sexo_filtro!=$sexo_animal) {
		$tem_sexo='N';
	}

    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
    $data_acompanhamento_calculo = date("Y-m-d");
    $date = new DateTime($data_nascimento); // Data de Nascimento
    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
    $idade_animal = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

    $num_rows = mysqli_num_rows($categoria); 

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($categoria)) {
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

            if ($idade_animal >= $idade_de && $idade_animal <= $idade_ate) {
            	if ($idade_ate==999999999){
	                $desc_categoria= '> 36 meses';
            	}
            	else {
	                $desc_categoria= $idade_de . ' a ' . $idade_ate . ' meses';
            	}
                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
            }
        }
    }                   

    if ($categoria_filtro!='') {
		foreach ($categoria_filtro as $value) {
	        if ($value==$codigo_categoria) {
	        	$tem_categoria = 'S';
	        }
	    }
    }

    if ($origem_filtro!='') {
		foreach ($origem_filtro as $value) {
	        if ($value==$codigo_origem) {
	        	$tem_origem = 'S';
	        }
	    }
    }

    if ($raca_filtro!='') {
		foreach ($raca_filtro as $value) {
	        if ($value==$raca) {
	        	$tem_raca = 'S';
	        }
	    }
    }

    if ($pai_filtro!='') {
		foreach ($pai_filtro as $value) {
	        if ($value==$pai_animal) {
	        	$tem_pai = 'S';
	        }
	    }
    }

    if ($mae_filtro!='') {
		foreach ($mae_filtro as $value) {
	        if ($value==$mae_animal) {
	        	$tem_mae = 'S';
	        }
	    }
    }

    if ($data_nasc_animal>=$data_nasc_inicial && $data_nasc_animal<=$data_nasc_final) {
    	$tem_data_nasc='S';
    }

    if ($peso_nasc_animal>=$peso_nasc_inicial  && $peso_nasc_animal<=$peso_nasc_final) {
    	$tem_peso_nasc='S';
    }

    if ($peso_desmama_animal>=$peso_desmama_inicial  && 
    	$peso_desmama_animal<=$peso_desmama_final) {
    	$tem_peso_desmama='S';
    }

    if ($peso_ult_animal>=$peso_ult_inicial  && 
    	$peso_ult_animal<=$peso_ult_final) {
    	$tem_peso_ult='S';
    }

	$tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas
	                        WHERE tab_codigo_raca='$raca'");
	$num_rows = mysqli_num_rows($tbl_raca);

	if ($num_rows!=0) {
		$reg_raca = mysqli_fetch_object($tbl_raca);
		$desc_raca = $reg_raca->tab_descricao_raca;
	}
	else {
		$desc_raca = '';
	}

	$tbl_pelagem = mysqli_query($conector, "SELECT * FROM tabela_pelagens
	                        WHERE tab_codigo_pelagem='$pelagem'");
	$num_rows = mysqli_num_rows($tbl_pelagem);

	if ($num_rows!=0) {
		$reg_pelagem = mysqli_fetch_object($tbl_pelagem);
		$desc_pelagem = $reg_pelagem->tab_descricao_pelagem;
	}
	else {
		$desc_pelagem = '';
	}

	$tbl_mae = mysqli_query($conector, "SELECT * FROM tbl_animais
	                        WHERE tbl_animal_codigo_id='$reg_animal->tbl_animal_codigo_mae'");
	$num_rows = mysqli_num_rows($tbl_mae);

	if ($num_rows!=0) {
		$reg_mae = mysqli_fetch_object($tbl_mae);
		$codigo_mae = $reg_mae->tbl_animal_codigo_alfa . $reg_mae->tbl_animal_codigo_numerico;
	}
	else {
		$codigo_mae = '';
	}

    // verifica a cobertura do animal
    $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
        INNER JOIN tbl_parametro_estacao_monta
                ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_ite_cobertura_codigo_id_animal='$codigo_id'" . $westacao . "
        ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0) {
        $reg_cobertura = mysqli_fetch_object($sql);
        $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
        $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
        $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
    }
    else {
        $codigo_local = 0;
        $estacao_animal = 0;
        $estacao_monta = '';
    }

    //$ultima_estacao = $codigo_estacao;

	// verifica vacas solteiras
	if ($solteiras=='S' || $paridas=='S') {
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo_id'
            ORDER BY tbl_animal_data_nascimento DESC limit 1");

        $ultimo_filho = mysqli_num_rows($tbl_filhos);

        if ($ultimo_filho!=0) {
            $reg_filhos = mysqli_fetch_object($tbl_filhos);
            $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($ultimo_parto); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            if ($idade < 8 && $ultimo_parto>=$data_paridas_de && $ultimo_parto<=$data_paridas_ate) {
                $tem_parida = 'S';
            }
            else {
                $tem_solteira = 'S';
            }
        }
	}

	// verifica partos
	if ($sexo == 'Fêmea' && $num_parto_de!='' && $num_parto_ate!='') {
        $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
            WHERE tbl_animal_codigo_mae='$codigo_id'");

        $num_partos = mysqli_num_rows($tbl_filhos);

        if ($num_partos>=$num_parto_de && 
        	$num_partos<=$num_parto_ate && $idade_animal>=8) {
        	$tem_parto = "S";
        }
        else {
        	$tem_parto = "N";
        }
	}

	// verifica abortos
	if ($sexo == 'Fêmea' && $num_aborto_de!='' && $num_aborto_ate!='') {
	    $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
	        WHERE tbl_mov_estoque_codigo_mae='$codigo_id' AND 
	              tbl_mov_estoque_codigo_id_animal=999999999 AND
	              (tbl_mov_estoque_entrada_saida='A' OR 
	               tbl_mov_estoque_entrada_saida='S') AND 
	              (tbl_mov_estoque_tipo_movimentacao='M' OR
	               tbl_mov_estoque_tipo_movimentacao='A' OR
	               tbl_mov_estoque_tipo_movimentacao='B')");

	    $num_natimorto = mysqli_num_rows($tbl_natimorto);

        if ($num_natimorto>=$num_aborto_de && 
        	$num_natimorto<=$num_aborto_ate) {
        	$tem_aborto = "S";
        }
        else {
        	$tem_aborto = "N";
        }
	}	    

	// Verifica previsão de parto
	if ($previsao_parto_de!='' && $previsao_parto_ate!='') {

		$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
		    INNER JOIN tbl_cobertura
		            ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
		    WHERE tbl_cobertura_lixeira=0 AND
		          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
		          tbl_cobertura_controle = 'C' AND 
		          tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
		          tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
		    ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

		$num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

		if ($num_rows_coberturas!=0) {
			$reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
            $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
			$cobertura_id = $reg_cobertura->tbl_cobertura_id;

            $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                    WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

            $reg_protocolo_cobertura = mysqli_fetch_object($sql);

            $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                      tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                ORDER BY tbl_ite_protocoloiatf_id ASC");

			$dias_previsao_parto = 282;

            while($reg_itens_iatf = mysqli_fetch_object($sql)){
                $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
            }
		}

		if ($data_previsao_parto>=$previsao_parto_de && 
			$data_previsao_parto<=$previsao_parto_ate) {

			$tem_previsao_parto = 'S';
		}
	}

	// Verifica animal em estação de monta
	$em_estacao_monta = 'N';

	$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
	    INNER JOIN tbl_cobertura
	            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
	    WHERE tbl_cobertura_lixeira=0 AND
	          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
	    	  tbl_cobertura_controle = 'C' AND 
	    	  tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
	    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

	$num_rows = mysqli_num_rows($tbl_item_cobertura);

	if ($num_rows!=0) {
		$reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
		$nascido = $reg_cobertura->tbl_ite_cobertura_nascido;

		if ($nascido!='') {
			$em_estacao_monta = 'N';
		}
		else {
			$em_estacao_monta = 'S';
		}
 
	}

	// Fim verifica em estação de monta

	// Verifica diagnostico
	if ($positivo=='S' || $negativo=='S'){
		$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
		    INNER JOIN tbl_cobertura
		            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		    WHERE tbl_cobertura_lixeira=0 AND
		          tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
		    	  tbl_cobertura_controle = 'C' AND 
		    	  tbl_cobertura_codigo_estacao_monta ='$estacao_animal'
		    ORDER BY tbl_ite_cobertura_numero_id DESC limit 1"); 

		$num_rows = mysqli_num_rows($tbl_item_cobertura);

		if ($num_rows!=0) {
			$reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
			$diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

			if ($diagnostico=='P'){
				$tem_positivo = 'S';
				$tem_negativo = 'N';
			} 
			else if ($diagnostico=='N') {
				$tem_negativo = 'S';
				$tem_positivo = 'N';
			}
			else {
				$tem_negativo = 'N';
				$tem_positivo = 'N';
			}			
		}
	}

    // verifica natimortos, nascidos ou abortos na estacao
    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
        INNER JOIN tbl_cobertura
                ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_ite_cobertura_codigo_id_animal = '$codigo_id' AND 
              tbl_cobertura_controle = 'C' AND 
              tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
              tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
        ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

    $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

    if ($num_rows_item!=0) {
        $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
        $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
    }
    else {
        $nascido_aborto = '';
    }

    if ($positivo=='S' AND 
        $nascido_aborto!='') {
        $tem_positivo='';
    }

    if ($animal_descarte=='S') {
    	$animal_descarte = ' DESCARTE';
    }
    else {
    	$animal_descarte = '';
    }

    $descricao_animal = $sexo . ' - Nasc: ' . $nascimento->format('d/m/Y') . ' - '. $desc_raca . ' ' . $desc_pelagem . ' - Mãe: ' . $codigo_mae;

    $valor[0] = $reg_animal->tbl_animal_codigo_id;
    $valor[1] = $sexo;
    $valor[2] = $nascimento->format('d/m/Y');
	$valor[3] = $desc_raca;
	$valor[4] = $desc_pelagem;
	$valor[5] = $codigo_mae;
	$valor[6] = $descricao_animal;
	$valor[7] = $tem_categoria;
	$valor[8] = $tem_sexo;
	$valor[9] = $tem_raca;
	$valor[10] = $tem_pai;
	$valor[11] = $tem_mae;
	$valor[12] = $tem_data_nasc;
	$valor[13] = $tem_peso_nasc;
	$valor[14] = $tem_peso_desmama;
	$valor[15] = $tem_peso_ult;
	$valor[16] = $codigo_categoria;
	$valor[17] = $desc_categoria;
	$valor[18] = $em_estacao_monta;
	$valor[19] = $tem_solteira;
	$valor[20] = $tem_descarte;
	$valor[21] = $tem_parida;
	$valor[22] = $tem_parto;
	$valor[23] = $tem_aborto;
	$valor[24] = $tem_previsao_parto;
	$valor[25] = $tem_origem;
	$valor[26] = $tem_positivo;
	$valor[27] = $tem_negativo;
	$valor[28] = $peso;
	$valor[29] = $animal_descarte;

	$str=$valor[0] . '<|>';

	for ($i=1; $i<=29; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;

}
else {
	$valor[0]='Nao tem animal';
	$valor[1]='Animal não cadastrado com esse código.';
	$str=$valor[0] . '<|>' . $valor[1] . '<|>';
	echo $str; 
	mysqli_close($conector);
	exit;	
}					

?>