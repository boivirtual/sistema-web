<?php
include "conecta_mysql.inc";

for ($i=1; $i<=25; $i++){
    $valor[$i]=0;
}

$id_animal = $_POST['id_animal'];  
$local = $_POST['local'];  
$data_nascimento = $_POST['data_nascimento'];  

$codigo_numerico = substr($id_animal, -9);

if (strlen($id_animal)!=9){
	$data = explode("-", $id_animal);
	$codigo_alfa = $data[0];
}
else {
	$codigo_alfa = '';
}

// pega a ultima estacao da fazenda
$tbl_estacao = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta
    WHERE tbl_par_codigo_local='$local' AND 
          tbl_par_lixeira=0
    ORDER BY tbl_par_estacao_id DESC LIMIT 1");

$num_rows_estacao = mysqli_num_rows($tbl_estacao);

if ($num_rows_estacao!=0) {
	$reg_estacao = mysqli_fetch_object($tbl_estacao);
	$id_ultima_estacao = $reg_estacao->tbl_par_estacao_id;
}
else {
	$id_ultima_estacao = 0;
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND 
          tbl_animal_codigo_numerico='$codigo_numerico' AND
          tbl_animal_ativo='S' AND 
          tbl_animal_lixeira=0");

$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows!=0) {
	$reg_animal = mysqli_fetch_object($tbl_animal);
	$raca = $reg_animal->tbl_animal_codigo_raca;
	$local = $reg_animal->tbl_animal_codigo_fazenda;
    $codigo_id_animal = $reg_animal->tbl_animal_codigo_id;

	if ($local!=$local) {
		$valor[0]='Nao tem animal';
		$valor[1]='Animal não consta no local ou Id não cadastrado.';
		$str=$valor[0] . '<|>' . $valor[1] . '<|>';
		echo $str; 
		mysqli_close($conector);
		exit;	
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

	$tbl_item_cobertura = mysqli_query($conector,"SELECT * FROM tbl_item_cobertura 
		INNER JOIN tbl_cobertura
		        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			 WHERE tbl_cobertura_lixeira=0 AND 
			       tbl_ite_cobertura_codigo_id_animal='$codigo_id_animal' AND 
			 		(tbl_cobertura_controle = 'C' OR tbl_cobertura_controle = 'M') AND 
				   (tbl_ite_cobertura_resultado_diagnostico = 'P' OR 
				   	tbl_ite_cobertura_resultado_diagnostico = '' OR 
				   	tbl_ite_cobertura_resultado_diagnostico = 'N' OR 
				   	tbl_ite_cobertura_resultado_diagnostico IS NULL
				   	) 
	    ORDER BY tbl_cobertura_incluido_em DESC LIMIT 1"); 

	$num_rows = mysqli_num_rows($tbl_item_cobertura);

	$controle = '';
	$dias_calculados_aborto = 0;
	$meses_calculados_aborto = 0;
	
	if ($num_rows!=0) {
		while ($reg_item = mysqli_fetch_object($tbl_item_cobertura)) {
			$cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
			$item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;
			$item_cobertura_nascido = $reg_item->tbl_ite_cobertura_nascido;

			$aborto_estacao = $reg_item->tbl_ite_cobertura_aborto_natimorto;
			$diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
			$data_diagnostico_negativo = 0;
			$controle = $reg_item->tbl_cobertura_controle;

	        // VERIFICA SE TEVE ABORTO OU NATMORTO NOS ULTIMOS 90 DIAS

			$tbl_estoque = mysqli_query($conector,"SELECT * FROM tbl_movimentacao_estoque 
				WHERE tbl_mov_estoque_codigo_mae='$codigo_id_animal' AND
					  (tbl_mov_estoque_entrada_saida = 'A' OR (tbl_mov_estoque_entrada_saida = 'S' AND 
					  	  tbl_mov_estoque_tipo_movimentacao = 'M')) 
				ORDER BY tbl_mov_estoque_numero_id DESC LIMIT 1"); 

			$num_rows_estoque = mysqli_num_rows($tbl_estoque);

			if ($num_rows_estoque!=0) {
				$reg_estoque = mysqli_fetch_object($tbl_estoque);
				$data_ocorrencia = $reg_estoque->tbl_mov_estoque_nascimento;

				$firstDate  = new DateTime($data_ocorrencia);
	    	    $secondDate = new DateTime($data_nascimento);
	    	    $diff = $firstDate->diff($secondDate);
	    	    $dias_calculados_aborto = $diff->days;
	    	    $meses_calculados_aborto = $diff->m + ($diff->y * 12);;
			}

			if ($controle=='M') {
				$estacao_monta_id = 0;
            	$desc_estacao = '';

				$codigo_pai = '000000000';
				$raca_pai = '';
				$protocolo_id = 0;

	            if ($diagnostico!='P' && $diagnostico!='N') {
	                $diagnostico = ''; //Sem data da prenhes digitada
	            }
	            else if ($diagnostico=='P') {
	                $diagnostico = 'P'; //Positivo
	            }

	            if ( $diagnostico=='N') {
	            	$data_diagnostico_negativo = $reg_item->tbl_ite_cobertura_negativo_em;
	            }

	            $dias_calculados = 0;	
	            $data_servico = $reg_item->tbl_ite_cobertura_data_prenhes;
			} 
			else {
				$estacao_monta_id = $reg_item->tbl_cobertura_codigo_estacao_monta;
				$codigo_pai = $reg_item->tbl_ite_cobertura_codigo_touro_semen;
				$protocolo_id = $reg_item->tbl_cobertura_protocoloiatf;

	            $tbl_item_iatf = mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf 
            	WHERE tbl_ite_protocoloiatf_protocolo_id='$protocolo_id'");

	            $qtd_item_iatf = mysqli_num_rows($tbl_item_iatf);

	            $tem_inseminacao = '';
	            
	            if ($qtd_item_iatf==2) {
	                $tem_inseminacao = $reg_item->tbl_ite_cobertura_dia_2;
	            }

	            if ($qtd_item_iatf==3) {
	                $tem_inseminacao = $reg_item->tbl_ite_cobertura_dia_3;
	            }

	            if ($qtd_item_iatf==4) {
	                $tem_inseminacao = $reg_item->tbl_ite_cobertura_dia_4;
	            }

	            if ($qtd_item_iatf==5) {
	                $tem_inseminacao = $reg_item->tbl_ite_cobertura_dia_5;
	            }

	            if ($qtd_item_iatf==6) {
	                $tem_inseminacao = $reg_item->tbl_ite_cobertura_dia_6;
	            }

	            if ($tem_inseminacao=='S' && $diagnostico!='P' && $diagnostico!='N') {
	                $diagnostico = 'D'; //Aguardando Diagnóstico
	            }
	            else if ($diagnostico=='P') {
	                $diagnostico = 'P'; //Positivo
	            }
	            else if ($diagnostico=='N'){
	                $diagnostico = 'N'; //Negativo
	            	$data_diagnostico_negativo = $reg_item->tbl_ite_cobertura_negativo_em;
	            }
	            else {
	                $diagnostico = 'I'; //Aguardando Inseminação
	            } 

	            $tbl_estacao = mysqli_query($conector, "select * from tbl_parametro_estacao_monta
	            	where tbl_par_estacao_id ='$estacao_monta_id'");
	            $num_rows_estacao = mysqli_num_rows($tbl_estacao);

	            if ($num_rows_estacao!=0){
	                $reg = mysqli_fetch_object($tbl_estacao);
	                $desc_estacao = $reg->tbl_par_estacao_nome;
	            }
	            else {
	            	$desc_estacao = 'Não encontrada';
	            }

				if ($codigo_pai=='') {
					$codigo_pai = '000000000';
					$raca_pai = '';
				}
				else {
	                $tab_pai = mysqli_query($conector, "select * from tbl_semem where tbl_semem_codigo_id='$codigo_pai'");
	                $num_rows_pai = mysqli_num_rows($tab_pai);

	                if ($num_rows_pai!=0){
	                    $reg = mysqli_fetch_object($tab_pai);
	                    $raca_pai = $reg->tbl_semem_codigo_raca;
	                }
	                else {
	                    $tab_pai = mysqli_query($conector, "select * from tbl_animais where tbl_animal_codigo_id='$codigo_pai'");
	                    $num_rows_pai = mysqli_num_rows($tab_pai);

	                    if ($num_rows_pai!=0){
	                        $reg = mysqli_fetch_object($tab_pai);
	                        $raca_pai = $reg->tbl_animal_codigo_raca;
	                    }
	                    else {
	                        $raca_pai = '';
	                    }
	                }
				}
			}
		}
	}
	else {
		$codigo_pai = '000000000';
		$raca_pai = '';
		$cobertura_id = 0;
		$item_cobertura = 0;
		$item_cobertura_nascido = '';
		$protocolo_id = 0;
		$estacao_monta_id =	0;	
		$dias_calculados = 0;	
		$data_servico = '';
		$aborto_estacao =0;	
		$desc_estacao = '';
		$diagnostico = '';
       	$data_diagnostico_negativo = 0;
	}

	// acha mes do diagnostico negativo
	if ($diagnostico=='N' && $data_diagnostico_negativo!=0) {
		$data_ocorrencia = 
		new DateTime($data_diagnostico_negativo);
		$dataAtual = new DateTime();
		$diferenca = $data_ocorrencia->diff($dataAtual);
		$meses_calculados_negativo = ($diferenca->y * 12) + $diferenca->m;
	}
	else {
		$meses_calculados_negativo=0;
	}

	if ($cobertura_id!=0) {
		if ($controle=='C') {
		    $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
		        WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");
		    $reg_protocolo_cobertura = mysqli_fetch_object($sql);

		    $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
		        WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
		              tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
		        ORDER BY tbl_ite_protocoloiatf_id ASC");
		        
		    $dias_previsao_parto = 282;

		    while($reg_itens = mysqli_fetch_object($sql)){
		        $dias = substr($reg_itens->tbl_ite_protocoloiatf_descricao, 3);

		        $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

		        $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
		    }
		}

	    $firstDate  = new DateTime($data_servico);
	    $secondDate = new DateTime($data_nascimento);
	    $intvl = $firstDate->diff($secondDate);
	    $dias_calculados = $intvl->days;
	}

	$nascidos_estacao = 0;
	$data_nascimento_ultimo = 0;
	$meses_calculados = 0;
	$dias_nascimento = '';

	if ($estacao_monta_id!=0) {
	    $sql = mysqli_query($conector, "SELECT * FROM tbl_animais 
	        WHERE tbl_animal_codigo_mae = '$codigo_id_animal' AND 
	              tbl_animal_estacao_monta_nascimento = '$estacao_monta_id'");

	    $nascidos_estacao = mysqli_num_rows($sql);

	    if ($nascidos_estacao!=0) {
	    	$reg_nacimento = mysqli_fetch_object($sql);
	    	$data_nascimento_ultimo = $reg_nacimento->tbl_animal_data_nascimento;

	    	$firstDate  = new DateTime($data_nascimento_ultimo);
	    	$secondDate = new DateTime($data_nascimento);
	    	$diff = $firstDate->diff($secondDate);
	    	$meses_calculados = $diff->m + ($diff->y * 12);
	    	$dias_nascimento = $diff->days;
	    }
	}
	else {
		if ($controle=='M') {
		    $sql = mysqli_query($conector, "SELECT * FROM tbl_animais 
		        WHERE tbl_animal_codigo_mae = '$codigo_id_animal'
		        ORDER BY tbl_animal_codigo_id DESC LIMIT 1");

		    $nascidos_estacao = mysqli_num_rows($sql);

		    if ($nascidos_estacao!=0) {
		    	$reg_nacimento = mysqli_fetch_object($sql);
		    	$data_nascimento_ultimo = $reg_nacimento->tbl_animal_data_nascimento;

	    		$firstDate  = new DateTime($data_nascimento_ultimo);
	    		$secondDate = new DateTime($data_nascimento);
	    		$diff = $firstDate->diff($secondDate);
	    		$meses_calculados = $diff->m + ($diff->y * 12);

	    		$dias_nascimento = $diff->days;
		    }
		}
	}

  	$valor[0] = $codigo_id_animal;
	$valor[1] = $diagnostico;
	$valor[2] = $desc_raca;
	$valor[3] = $codigo_pai;
	$valor[4] = $cobertura_id;
	$valor[5] = $item_cobertura;
	$valor[6] = $data_servico;
	$valor[7] = $dias_calculados;
	$valor[8] = $estacao_monta_id;
	$valor[9] = $nascidos_estacao;
	$valor[10] = $aborto_estacao;
	$valor[11] = $raca_pai;
	$valor[12] = $desc_estacao;
	$valor[13] = $data_nascimento_ultimo;
	$valor[14] = $id_ultima_estacao;
	$valor[15] = $controle;
	$valor[16] = $data_servico;
	$valor[17] = $meses_calculados;
	$valor[18] = $dias_calculados_aborto;
	$valor[19] = $dias_nascimento;
	$valor[20] = $item_cobertura_nascido;
	$valor[21] = $meses_calculados_aborto;
	$valor[22] = $meses_calculados_negativo;
	$valor[23] = $data_diagnostico_negativo;
	$str=$valor[0] . '<|>';

	for ($i=1; $i<=25; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;

}					

$valor[0]='Nao tem animal';
$valor[1]='Animal não cadastrado com esse código.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>