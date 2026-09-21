<?php
include "conecta_mysql.inc";

for ($i=1; $i<=20; $i++){
    $valor[$i]=0;
}

$codigo_animal = $_POST['id_animal'];  
$local_filtro = $_POST['local'];  
$estacao_monta = $_POST['estacao_monta'];  

$codigo_numerico = substr($codigo_animal, -9);

if (strlen($codigo_animal)!=9){
	$data = explode("-", $codigo_animal);
	$codigo_alfa = $data[0];
}
else {
	$codigo_alfa = '';
}

$pode_inserir_femea = '';
$animal_prenhe = '';
$idade_femea = 0;
$femea_selecionada_na_estacao = '';
$animal_tem_parto = '';
$animal_tem_natimorto = '';
$animal_tem_aborto = '';
$desc_grupo = '';
$codigo_grupo = '';
$mesmo_grupo = '';

// 1ª PREMISSA Animal Fêmea (Já veio selecionado na digitação)
$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
          tbl_animal_codigo_numerico='$codigo_numerico'");

$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal);

	$id_animal = $reg_animal->tbl_animal_codigo_id;
	$descarte = $reg_animal->tbl_animal_descarte_reproducao;
	$nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);
	$raca = $reg_animal->tbl_animal_codigo_raca;
	$pelagem = $reg_animal->tbl_animal_codigo_pelagem;
	$data_nasc_animal = $reg_animal->tbl_animal_data_nascimento;
	$sexo = 'Fêmea';

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

    // VERIFICA IDADE > 12 2ª PREMISSA

    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
    $data_acompanhamento_calculo = date("Y-m-d");
    $date = new DateTime($data_nascimento); // Data de Nascimento
    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
    $idade_femea = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

    // VERIFICA SE A VACA ESTA PRENHE 3º PREMISSA

    $tbl_prenhe = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	 	INNER JOIN tbl_cobertura
	 	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
             WHERE tbl_cobertura_lixeira=0 AND
                   tbl_ite_cobertura_codigo_id_animal='$id_animal' AND  
                   tbl_ite_cobertura_resultado_diagnostico='P' AND  
                  (tbl_ite_cobertura_nascido='' OR 
                   tbl_ite_cobertura_nascido IS NULL)
       	  ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1");

    $num_rows_prenhe = mysqli_num_rows($tbl_prenhe);

    if ($num_rows_prenhe!=0) {
    	$animal_prenhe='S';
    }

    //VERIFICA SE A FÊMEA JA FOI SELECIONADA NESSA ESTACAO 4ª PREMISSA

    $femea_selecionada = '';

    $tbl_selecao = mysqli_query($conector, "SELECT * FROM tbl_cobertura
        INNER JOIN tbl_item_cobertura 
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
             WHERE tbl_cobertura_lixeira=0 AND 
                    tbl_ite_cobertura_codigo_id_animal = '$id_animal'
      ORDER BY tbl_cobertura_incluido_em DESC LIMIT 1");

    /*$tbl_selecao = mysqli_query($conector, "SELECT  * FROM tbl_cobertura
        INNER JOIN tbl_item_cobertura 
                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
             WHERE tbl_cobertura_lixeira=0 AND 
                   tbl_cobertura_codigo_local = '$local_filtro' AND 
                   tbl_cobertura_codigo_estacao_monta = '$estacao_monta' AND
                   tbl_ite_cobertura_codigo_id_animal = '$id_animal'
          ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1");*/

    $selecionada_estacao = mysqli_num_rows($tbl_selecao);

    if ($selecionada_estacao!=0) {
        $reg_selecao = mysqli_fetch_object($tbl_selecao);
	    $femea_selecionada_na_estacao = 'S';

		$codigo_grupo = $reg_selecao->tbl_cobertura_codigo_grupo;
        $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;
        $cobertura_controle = $reg_selecao->tbl_cobertura_controle;
        $nascido = $reg_selecao->tbl_ite_cobertura_nascido;
        $estacao = $reg_selecao->tbl_cobertura_codigo_estacao_monta;

        // VERIFICA SE A FÊMEA JA ESTA NO GRUPO QUE DESEJA SER INSERIDA 5ª PREMISSA

        if ($cobertura_controle=='C') {
            if ($estacao!=$estacao_monta || $diagnostico_selecao=='N') {
                $femea_selecionada_na_estacao = '';
            }
        }
        else {
            if ($diagnostico_selecao=='N') {
                $femea_selecionada_na_estacao = '';
                $codigo_grupo='';
            }
        }

	    if ($nascido=='A' || $nascido=='M' || $nascido=='O' || $nascido=='N') {
	    	$femea_selecionada_na_estacao = '';
	    }
        
    } 
	    //VERIFICA SE A FÊMEA JA FOI SELECIONADA NA LISTA DE MONTA 

	/*    $tbl_selecao = mysqli_query($conector, "SELECT  * FROM tbl_cobertura
	        INNER JOIN tbl_item_cobertura 
	                ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
	             WHERE tbl_cobertura_lixeira=0 AND
	                   tbl_cobertura_codigo_local = '$local_filtro' AND 
	             	   tbl_cobertura_controle = 'M' AND
	                   tbl_ite_cobertura_codigo_id_animal = '$id_animal'
	          ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1");

	    $selecionada_monta = mysqli_num_rows($tbl_selecao);

	    if ($selecionada_monta!=0) {
	        $reg_selecao = mysqli_fetch_object($tbl_selecao);
	        $diagnostico_selecao = $reg_selecao->tbl_ite_cobertura_resultado_diagnostico;
			$cobertura_controle = $reg_selecao->tbl_cobertura_controle;

		    $femea_selecionada_na_estacao = 'S';

		    if ($diagnostico_selecao=='N') {
		        $femea_selecionada_na_estacao = '';
		    }

	        $codigo_grupo='';
	    }*/

    // VERIFICA SE ANIMAL TEM PARTO A MENOS DE 35 DIAS
    $data_nasc_bezerro = '0000-00-00';

    $tbl_filhos = mysqli_query($conector,"SELECT * FROM tbl_animais 
        WHERE tbl_animal_codigo_mae='$id_animal'
        ORDER BY tbl_animal_codigo_id  DESC LIMIT 1"); 

    $numero_rows_partos = mysqli_num_rows($tbl_filhos);

    if ($numero_rows_partos!=0) {
        $reg_parto = mysqli_fetch_object($tbl_filhos);
        $data_nasc_bezerro = $reg_parto->tbl_animal_data_nascimento;
        // $data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));
        $data_ref = date("Y-m-d");
        $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
        $dias_parto = floor($diferenca / (60 * 60 * 24));

	    /*$data_acompanhamento_calculo = date("Y-m-d");
	    $date = new DateTime($data_nasc_bezerro); // Data de Nascimento
	    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
	    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
	    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
	    $idade_bezerro = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        $bezerro_ativo = $reg_parto->tbl_animal_ativo;*/

        if ($dias_parto<35) {
	        $animal_tem_parto = 'S';
	    }
	    //else {
	    	//$femea_selecionada_na_estacao = '';
	    //}
	    /*else {
            if ($idade_bezerro<8 && $bezerro_ativo=='S') {
            	$animal_tem_parto = 'S';
            }
	    }*/
    }

    // VERIFICA TAMBEM SE TEVE NATIMORTO A MENOS 35 DIAS
    $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$id_animal' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND
              tbl_mov_estoque_entrada_saida='S' AND 
              tbl_mov_estoque_tipo_movimentacao='M' 
     ORDER BY tbl_mov_estoque_nascimento DESC LIMIT 1");

    $num_natimorto = mysqli_num_rows($tbl_natimorto);

    if ($num_natimorto!=0) {
        $reg_natmorto = mysqli_fetch_object($tbl_natimorto);
        $data_nasc_bezerro=$reg_natmorto->tbl_mov_estoque_nascimento;
        //$data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));
        $data_ref = date("Y-m-d");
        $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
        $dias_parto = floor($diferenca / (60 * 60 * 24));

        if ($dias_parto<35) {
        	$animal_tem_natimorto = 'S';
        }
	    //else {
	    	//$femea_selecionada_na_estacao = '';
	    //}
    }

	// VERIFICA TAMBEM SE TEVE ABORTO A MENOS 35 DIAS
    $tbl_aborto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
        WHERE tbl_mov_estoque_codigo_mae='$id_animal' AND 
              tbl_mov_estoque_codigo_id_animal=999999999 AND
              tbl_mov_estoque_entrada_saida='A' AND 
              (tbl_mov_estoque_tipo_movimentacao='A' OR
               tbl_mov_estoque_tipo_movimentacao='B') 
        ORDER BY tbl_mov_estoque_nascimento DESC LIMIT 1");

    $num_aborto = mysqli_num_rows($tbl_aborto);

    if ($num_aborto!=0) {
        $reg_aborto = mysqli_fetch_object($tbl_aborto);
        $data_nasc_bezerro=$reg_aborto->tbl_mov_estoque_nascimento;
        //$data_ref = date("Y-m-d", strtotime($data_hoje . "- 35 days"));
        $data_ref = date("Y-m-d");
        $diferenca = strtotime($data_ref) - strtotime($data_nasc_bezerro);
        $dias_parto = floor($diferenca / (60 * 60 * 24));

        if ($dias_parto<35) {
        	$animal_tem_aborto = 'S';
        }
	    //else {
	    	//$femea_selecionada_na_estacao = '';
	    //}
    }

/*	$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	 	INNER JOIN tbl_cobertura
	 	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
	    WHERE tbl_ite_cobertura_codigo_id_animal='$id_animal' AND 
	          tbl_cobertura_codigo_local='$local_filtro' AND 
	          tbl_cobertura_codigo_estacao_monta='$estacao_monta'
	    ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1");

	$num_rows = mysqli_num_rows($tbl_cobertura);
	$desc_grupo = '';
	$codigo_grupo = '';

	if ($num_rows!=0) {
		$reg_cobertura = mysqli_fetch_object($tbl_cobertura);

		$diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
		
		if ($diagnostico=='N')  {
			$pode_inserir_femea = 'S';
		}
		else {
			$pode_inserir_femea = 'N';
		}

		$codigo_grupo = $reg_cobertura->tbl_cobertura_codigo_grupo;

		$tbl_grupo = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta
	    WHERE tbl_grupo_id='$codigo_grupo' AND 
	          tbl_grupo_codigo_estacao_monta='$estacao_monta' AND 
	          tbl_grupo_codigo_local='$local_filtro'");

		$num_rows = mysqli_num_rows($tbl_grupo);

		if ($num_rows!=0) {
			$reg_grupo = mysqli_fetch_object($tbl_grupo);
			$desc_grupo = $reg_grupo->tbl_grupo_descricao;
		}
		else {
			$desc_grupo = '';
		}
	}
	else {
		$pode_inserir_femea = 'S';
	}
*/

    $descricao_animal = $sexo . ' - Nasc: ' . $nascimento->format('d/m/Y') . ' - '. $desc_raca . ' ' . $desc_pelagem . ' - Mãe: ' . $codigo_mae;

    $valor[0] = $id_animal;
    $valor[1] = $sexo;
    $valor[2] = $nascimento->format('d/m/Y');
	$valor[3] = $desc_raca;
	$valor[4] = $desc_pelagem;
	$valor[5] = $codigo_mae;
	$valor[6] = $descricao_animal;
	$valor[7] = $idade_femea;
	$valor[8] = $codigo_grupo;
	//$valor[9] = $desc_grupo;
	$valor[10] = $animal_prenhe;
	$valor[11] = $femea_selecionada_na_estacao;
	$valor[12] = $animal_tem_parto;
	$valor[13] = $animal_tem_natimorto;
	$valor[14] = $animal_tem_aborto;
	//$valor[15] = $dias_parto;
	//$valor[16] = $mesmo_grupo;
	$valor[17] = $descarte;
	$str=$valor[0] . '<|>';

	for ($i=1; $i<=20; $i++){
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
}				

?>