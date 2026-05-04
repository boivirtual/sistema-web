<?php 

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];

$tipo_gravacao = $_POST['tipo_gravacao'];
$num_mov_nascimento = $_POST['num_mov_nascimento'];
$tipo_cobertura = $_POST['tipo_cobertura'];

if (isset($_POST['data_prenhes'])) {
	$data_prenhes = $_POST['data_prenhes'];

	if ($data_prenhes == '') {
		$data_prenhes = 0;
	}
}
else {
	$data_prenhes = 0;
}

$local = $_POST['local_id'];
$pasto_id = preg_replace('/[^0-9 ]/', '', $_POST['pasto_id']);
$ocorrencia = $_POST['opcao_nascimento'];
$data_ocorrencia = $_POST['nascimento_animal'];
$codigo_mae = $_POST['codigo_mae_animal'];
$data_sistema = date("Y-m-d");

$cobertura_id = $_POST['cobertura_id'];
$item_cobertura = $_POST['item_cobertura'];

if ($item_cobertura=='') {
	$item_cobertura=0;
}

if ($cobertura_id=='') {
	$cobertura_id=0;
}

// dados para a tabela tbl_movimentacao
$movimentacao_finalizada='N';
$codigo_tipo = 881;
$total_digitados = 1;
$peso_total_kg = 0.00;
$peso_total_arroba = 0.00;
$peso_medio_kg = 0.00;
$peso_medio_arroba = 0.00;
$data_inclusao = date("Y-m-d H:i:s");
$codigo_motivo_morte = 7;
$numero_item = 1;
$numero_movimentacao=0;
$id_mov_estoque_morte=0;
$id_mov_estoque_aborto=0;
$id_mov_estoque_nascimento=0;

if ($data_ocorrencia>$data_sistema){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Data da ocorrência não pode ser superior a data de hoje!'));
	exit;
}

$data = date('d-m-Y', strtotime($data_ocorrencia));
$data_nascimento = str_replace("-", "/", $data);

/*$resposta = array('success' => true, 'message' => $data_nascimento);

header('Content-type: application/json');
echo json_encode($resposta);
exit;
*/

if ($local=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Local.'));
	exit;
}

if ($codigo_mae==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Nº da Fêmea.'));
	exit;
}

if ($data_ocorrencia==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Data da Ocorrência.'));
	exit;
}

if(!isset($_POST['sexo_animal'])) { 
	$sexo = 'N';
}
else {
	$sexo = $_POST['sexo_animal'];
}

// entrada_saida = A (Aborto/Absorsao)
// entrada_saida = E (Entrada)

// ocorrencia = A (Aborto)
// ocorrencia = B (Absorção)
// ocorrencia = M (Nascimento - Natimorto)

if ($ocorrencia=='A') {
	$entrada_saida = "A";
	$tipo_movimentacao = "A";
	$nascido = 'A';
}
else if ($ocorrencia=='B') {
	$entrada_saida = "A";
	$tipo_movimentacao = "B";
	$nascido = 'A';
}
else {
	$entrada_saida = "E";
	$tipo_movimentacao = "N";
	$nascido = 'M';
}

/*$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');

header('Content-type: application/json');
echo json_encode($resposta);
exit;*/


include "conecta_mysql.inc";

$id_animal = 999999999;

if ($codigo_mae!=''){
	$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
	    WHERE tbl_animal_codigo_id='$codigo_mae'");

	$num_rows = mysqli_num_rows($rs);
	if ($num_rows!=0) {
	   	$reg_animal = mysqli_fetch_object($rs);
		$codigo_numerico_mae = $reg_animal->tbl_animal_codigo_alfa.$reg_animal->tbl_animal_codigo_numerico;
	}
}
else {
	$codigo_numerico_mae = '';
}

// tipo de gravacao = 0 Inclusao
if ($tipo_gravacao==0) {
	// Ocorrencia A = Aborto ou B = Absorsão, então tem que somar 1 aborto no cadastro de animal codigo da mae 
	if ($ocorrencia=='A' || $ocorrencia=='B') {
		$sql = "INSERT INTO tbl_movimentacao_estoque
			                (tbl_mov_estoque_codigo_id_animal,
			                 tbl_mov_estoque_data_emissao,
			                 tbl_mov_estoque_nascimento,
			                 tbl_mov_estoque_local,
			                 tbl_mov_estoque_entrada_saida,
			                 tbl_mov_estoque_tipo_movimentacao,
			                 tbl_mov_estoque_local_origem,
			                 tbl_mov_estoque_local_destino,
			                 tbl_mov_estoque_codigo_movimentacao,
			                 tbl_mov_estoque_codigo_pasto,
			                 tbl_mov_estoque_codigo_raca,
			                 tbl_mov_estoque_codigo_pelagem,
			                 tbl_mov_estoque_sexo,
			                 tbl_mov_estoque_primeiro_peso,
			                 tbl_mov_estoque_codigo_mae,
					 		 tbl_mov_estoque_cobertura_numero_id,  
		 	                 tbl_mov_estoque_cobertura_numero_item
			                ) 
			                VALUES ('$id_animal',
			                        '$data_sistema',
			                        '$data_ocorrencia',
			                        '$local',
			                        '$entrada_saida',
			                        '$tipo_movimentacao',
			                        '$local',
			                        null,
			                        '$numero_movimentacao',
			                        '$pasto_id',
			                        null,
			                        null,
			                        '$sexo',
			                        null,
			                        '$codigo_mae',
			                        '$cobertura_id',
	                        		'$item_cobertura'
			                )";
			        
		$resultado = mysqli_query($conector,$sql);
		$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		 	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico do animal. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$id_mov_estoque_aborto = mysqli_insert_id($conector);
		$id_mov_estoque_aborto = str_pad($id_mov_estoque_aborto, 9, "0", STR_PAD_LEFT);

		$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
		    WHERE tbl_animal_codigo_id ='$codigo_mae'");

		$num_rows_animal = mysqli_num_rows($tbl_animal);	

		if ($num_rows_animal!=0) {
			$reg_animal = mysqli_fetch_object($tbl_animal);
			$numero_aborto =  $reg_animal->tbl_animal_numero_abortos;
			$numero_aborto++;
		}
		else {
			$numero_aborto = 1;
		}

		$sql = ("UPDATE tbl_animais SET 
				tbl_animal_numero_abortos='$numero_aborto'
			WHERE tbl_animal_codigo_id ='$codigo_mae'");

	    $resultado = mysqli_query($conector,$sql);
		$resposta = array('success' => true, 'message' => 'Registro incluido com sucesso.');
	    $erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do número de abortos do animal. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
	}

	// Ocorrencia M = Natimorto, então tem que gerar um historico de morte
	if ($ocorrencia=='M') {

		$sql = "INSERT INTO tbl_movimentacao (
			    	tbl_movimentacao_controle,
			    	tbl_movimentacao_data,
					tbl_movimentacao_codigo_local_origem,
					tbl_movimentacao_codigo_local_destino,
					tbl_movimentacao_tipo,
					tbl_movimentacao_qtd_animais_pesados,
					tbl_movimentacao_peso_kg,
					tbl_movimentacao_peso_arroba,
					tbl_movimentacao_peso_medio_kg,
					tbl_movimentacao_peso_medio_arroba,
					tbl_movimentacao_filtros,
					tbl_movimentacao_situacao,
					tbl_movimentacao_incluido_em,
					tbl_movimentacao_incluido_por,
					tbl_movimentacao_alterado_em,
					tbl_movimentacao_alterado_por,
					tbl_movimentacao_lixeira,
					tbl_movimentacao_lixeira_em,
					tbl_movimentacao_lixeira_por,
					tbl_movimentacao_aceite_transferencia_em,
					tbl_movimentacao_aceite_transferencia_por,
					tbl_movimentacao_aceite_financeiro_em,
					tbl_movimentacao_aceite_financeiro_por,
					tbl_movimentacao_codigo_pesagem
			        ) VALUES (
			        '$controle_estoque',
			        '$data_ocorrencia',
					'$local',
					null,
					'$codigo_tipo',
					'$total_digitados',
					'$peso_total_kg',
					'$peso_total_arroba',
					'$peso_medio_kg',
					'$peso_medio_arroba',
					null,
					'$movimentacao_finalizada',
					'$data_inclusao',
					'$nomeusuario',
					null,
					null,
					0,
					null,
					null,
					null,
					null,
					null,
					null,
					null
				)";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a movimentação da morte '. $erro_mysql));
		   	mysqli_close($conector);
			exit;
		} 

		$numero_movimentacao = mysqli_insert_id($conector);
		$numero_movimentacao = str_pad($numero_movimentacao, 9, "0", STR_PAD_LEFT);

		$sql = "INSERT INTO tbl_item_movimentacao (
			            tbl_ite_movimentacao_numero_id,
			            tbl_ite_movimentacao_numero_item,
			            tbl_ite_movimentacao_data_emissao,
			            tbl_ite_movimentacao_codigo_id_animal,
			            tbl_ite_movimentacao_codigo_animal,
						tbl_ite_movimentacao_peso,
						tbl_ite_movimentacao_sexo,
						tbl_ite_movimentacao_nascimento,
						tbl_ite_movimentacao_raca,
						tbl_ite_movimentacao_pelagem,
						tbl_ite_movimentacao_mae,
						tbl_ite_movimentacao_observacao,
						tbl_ite_movimentacao_motivo_morte,
						tbl_ite_movimentacao_codigo_pasto,
						tbl_ite_movimentacao_codigo_categoria
			        ) VALUES (
			            '$numero_movimentacao',
			            '$numero_item',
			            '$data_ocorrencia',
			            '$id_animal',
			            0,
			            null,
			            '$sexo',
			            '$data_nascimento',
			            null,
			            null,
			            '$codigo_numerico_mae',
			            null,
			            '$codigo_motivo_morte',
			            '$pasto_id',
			            1
			    )";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação do item na morte. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		// Grava o Nascimento
		$sql = "INSERT INTO tbl_movimentacao_estoque
			                (tbl_mov_estoque_codigo_id_animal,
			                 tbl_mov_estoque_data_emissao,
			                 tbl_mov_estoque_nascimento,
			                 tbl_mov_estoque_local,
			                 tbl_mov_estoque_entrada_saida,
			                 tbl_mov_estoque_tipo_movimentacao,
			                 tbl_mov_estoque_local_origem,
			                 tbl_mov_estoque_local_destino,
			                 tbl_mov_estoque_codigo_movimentacao,
			                 tbl_mov_estoque_codigo_pasto,
			                 tbl_mov_estoque_codigo_raca,
			                 tbl_mov_estoque_codigo_pelagem,
			                 tbl_mov_estoque_sexo,
			                 tbl_mov_estoque_primeiro_peso,
			                 tbl_mov_estoque_codigo_mae,
					 		 tbl_mov_estoque_cobertura_numero_id,  
		 	                 tbl_mov_estoque_cobertura_numero_item
			                ) 
			                VALUES ('$id_animal',
			                        '$data_sistema',
			                        '$data_ocorrencia',
			                        '$local',
			                        '$entrada_saida',
			                        '$tipo_movimentacao',
			                        '$local',
			                        null,
			                        '$numero_movimentacao',
			                        '$pasto_id',
			                        null,
			                        null,
			                        '$sexo',
			                        null,
			                        '$codigo_mae',
	                        		'$cobertura_id',
	                        		'$item_cobertura'
			                )";
			        
		$resultado = mysqli_query($conector,$sql);
		$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		 	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico do animal. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$id_mov_estoque_nascimento = mysqli_insert_id($conector);
		$id_mov_estoque_nascimento = str_pad($id_mov_estoque_nascimento, 9, "0", STR_PAD_LEFT);

		// Grava a Morte
		$sql = "INSERT INTO tbl_movimentacao_estoque
					                (tbl_mov_estoque_codigo_id_animal,
					                 tbl_mov_estoque_data_emissao,
					                 tbl_mov_estoque_nascimento,
					                 tbl_mov_estoque_local,
					                 tbl_mov_estoque_entrada_saida,
					                 tbl_mov_estoque_tipo_movimentacao,
					                 tbl_mov_estoque_local_origem,
					                 tbl_mov_estoque_local_destino,
					                 tbl_mov_estoque_codigo_movimentacao,
					                 tbl_mov_estoque_codigo_pasto,
					                 tbl_mov_estoque_codigo_raca,
					                 tbl_mov_estoque_codigo_pelagem,
					                 tbl_mov_estoque_sexo,
					                 tbl_mov_estoque_primeiro_peso,
					                 tbl_mov_estoque_codigo_mae,
					 		 		 tbl_mov_estoque_cobertura_numero_id,  
		 	                         tbl_mov_estoque_cobertura_numero_item
					                ) 
					                VALUES ('$id_animal',
					                        '$data_sistema',
					                        '$data_ocorrencia',
					                        '$local',
					                        'S',
					                        'M',
					                        '$local',
					                        null,
					                        '$numero_movimentacao',
					                        '$pasto_id',
					                        null,
					                        null,
					                        '$sexo',
					                        null,
					                        '$codigo_mae',
	                        				'$cobertura_id',
	                        				'$item_cobertura'
					                )";
					        
		$resultado = mysqli_query($conector,$sql);
		$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico de morte do animal. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$id_mov_estoque_morte = mysqli_insert_id($conector);
		$id_mov_estoque_morte = str_pad($id_mov_estoque_morte, 9, "0", STR_PAD_LEFT);
	}

    // Altera o item de cobertura informando que houve aborto, absorção ou natimorto
    
    if ($tipo_cobertura=='M' || $tipo_cobertura=='') {
		$tbl_cobertura = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
			INNER JOIN tbl_item_cobertura
			        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
				 WHERE tbl_cobertura_lixeira=0 AND 
				       tbl_cobertura_id='$cobertura_id'"); 

		$num_rows = mysqli_num_rows($tbl_cobertura);

		if ($num_rows!=0) { 
	    	if ($data_prenhes==0) {

	    		if ($entrada_saida=='A') {
	    			// Para Aborto/Absorcão calcula a prenhes -30 dias da data do aborto 
	    			// Conforme Trello 
	    			// Cartão MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
	    			// Cheklist AJUSTE REUNIAO 09/06/2025

	    			// Calular Data da Previsão + 282 a partir a Prenhez calculada
	    			// Conforme o Trello
	    			// Cartão MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
	    			// Cheklist AJUSTE REUNIÃO 23/06/2025

					$dias = 30;
			    	$data_prenhes = date("Y-m-d", strtotime($data_ocorrencia . "-{$dias} days"));

					$dias = 282;
			    	$data_previsao = date("Y-m-d", strtotime($data_prenhes . "+{$dias} days"));

					$sql = ("UPDATE tbl_item_cobertura SET 
							tbl_ite_cobertura_aborto_natimorto=1,
							tbl_ite_cobertura_resultado_diagnostico='P',
							tbl_ite_cobertura_data_prenhes='$data_prenhes',
							tbl_ite_cobertura_previsao_parto='$data_previsao',
					   		tbl_ite_cobertura_nascido='$nascido'
						WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
						      tbl_ite_cobertura_numero_item = '$item_cobertura'");
	    		}
	    		else { // Natimorto
					$data_previsao = $data_ocorrencia;
					$dias = 282;
			    	$data_prenhes = date("Y-m-d", strtotime($data_ocorrencia . "-{$dias} days"));

					$sql = ("UPDATE tbl_item_cobertura SET 
							tbl_ite_cobertura_aborto_natimorto=1,
							tbl_ite_cobertura_resultado_diagnostico='P',
							tbl_ite_cobertura_data_prenhes='$data_prenhes',
							tbl_ite_cobertura_previsao_parto='$data_previsao',
					   		tbl_ite_cobertura_nascido='$nascido'
						WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
						      tbl_ite_cobertura_numero_item = '$item_cobertura'");
	    		}
	    	}
	    	else {
				$sql = ("UPDATE tbl_item_cobertura SET 
						tbl_ite_cobertura_aborto_natimorto=1,
				   		tbl_ite_cobertura_nascido='$nascido'
					WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
					      tbl_ite_cobertura_numero_item = '$item_cobertura'");
	    	}

		    $resultado = mysqli_query($conector,$sql);
			$resposta = array('success' => true, 'message' => 'Registro incluido com sucesso.');
		    $erro_mysql = mysqli_error($conector);

			if (!$resultado){
			  	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura. ' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
		}
		else { // Monta não existe, então cria o registro
			if ($entrada_saida!='A') {
				$data_previsao = $data_ocorrencia;
				$dias = 282;
			    $data_prenhes = date("Y-m-d", strtotime($data_ocorrencia . "-{$dias} days"));
			}
			else {
	    		// Para Aborto/Absorcão calcula a prenhes -30 dias da data do aborto 
	    		// Conforme Trello 
	    		// Cartão MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
	    		// Cheklist AJUSTE REUNIAO 09/06/2025

	    		// Calular Data da Previsão + 282 a partir a Prenhez calculada
	    		// Conforme o Trello
	    		// Cartão MELHORIA NA REPRODUÇÃO (PARA DEPOIS DA ESTAÇÃO 24/2025)
	    		// Cheklist AJUSTE REUNIÃO 23/06/2025

				$dias = 30;
			    $data_prenhes = date("Y-m-d", strtotime($data_ocorrencia . "-{$dias} days"));

			    $dias = 282;
			    $data_previsao = date("Y-m-d", strtotime($data_prenhes . "+{$dias} days"));
			}

			$sql = "INSERT INTO tbl_cobertura (
								tbl_cobertura_controle,
								tbl_cobertura_data,
								tbl_cobertura_codigo_local,
								tbl_cobertura_codigo_grupo,
								tbl_cobertura_codigo_estacao_monta,
								tbl_cobertura_protocoloiatf,
								tbl_cobertura_qtd_animais,
								tbl_cobertura_filtros,
								tbl_cobertura_incluido_em,
								tbl_cobertura_incluido_por,
								tbl_cobertura_alterado_em,
								tbl_cobertura_alterado_por,
								tbl_cobertura_lixeira,
								tbl_cobertura_lixeira_em,
								tbl_cobertura_lixeira_por,
								tbl_cobertura_filtro_vacas_paridas,
								tbl_cobertura_filtro_data_paridas,
								tbl_cobertura_filtro_vacas_solteiras,
								tbl_cobertura_filtro_novilhas,
								tbl_cobertura_filtro_idade_de,
								tbl_cobertura_filtro_idade_ate,
								tbl_cobertura_filtro_peso_acima,
								tbl_cobertura_encerrada,
								tbl_cobertura_planilha_processada
					        ) VALUES (
						        'M',
						        '$data_sistema',
								'$local',
								0,
								0,
								0,
								1,
								null,
								'$data_inclusao',
								'$nomeusuario',
								null,
								null,
								0,
								null,
								null,
								null,
								null,
								null,
								null,
								null,
								null,
								null,
								'S',
								null
									)";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação do registro de monta natural. ' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

			$id_monta_natural = mysqli_insert_id($conector);
			$id_monta_natural = str_pad($id_monta_natural, 9, "0", STR_PAD_LEFT);

			$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
				WHERE tbl_animal_codigo_id='$codigo_mae'");

			$num_rows = mysqli_num_rows($tbl_animal);	

			if ($num_rows!=0) {
				$reg_animal = mysqli_fetch_object($tbl_animal);
				$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
				$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
			}
			else {
				$codigo_alfa = '';
				$codigo_numerico = '';
			}	

			if ($codigo_alfa=='') {
				$codigo_animal_edi = $codigo_numerico;
			}
			else {
				$codigo_animal_edi = $codigo_alfa.'-'.$codigo_numerico;
			}

			if ($entrada_saida=='A') {
				$sql = "INSERT INTO tbl_item_cobertura(
						tbl_ite_cobertura_numero_id,
						tbl_ite_cobertura_numero_item,
						tbl_ite_cobertura_codigo_id_animal,
						tbl_ite_cobertura_codigo_animal,
						tbl_ite_cobertura_codigo_alfa,
						tbl_ite_cobertura_codigo_numerico,
						tbl_ite_cobertura_data_emissao,
						tbl_ite_cobertura_codigo_touro_semen,
						tbl_ite_cobertura_lote_semen,
						tbl_ite_cobertura_data_diagnostico,
						tbl_ite_cobertura_resultado_diagnostico,
						tbl_ite_cobertura_nome_inseminador,
						tbl_ite_cobertura_destino,
						tbl_ite_cobertura_dia_1,
						tbl_ite_cobertura_dia_2,
						tbl_ite_cobertura_dia_3,
						tbl_ite_cobertura_dia_4,
						tbl_ite_cobertura_dia_5,
						tbl_ite_cobertura_dia_6,
						tbl_ite_cobertura_observacao,
						tbl_ite_cobertura_numero_cobertura,
						tbl_ite_cobertura_qtd_diagnosticos_positivo,
						tbl_ite_cobertura_aborto_natimorto,
						tbl_ite_cobertura_nascido,
						tbl_ite_cobertura_data_prenhes,
						tbl_ite_cobertura_previsao_parto

				    )VALUES(
				        '$id_monta_natural',
				        1,
				        '$codigo_mae',
				        '$codigo_animal_edi',
						'$codigo_alfa',
						'$codigo_numerico',
				        '$data_sistema',
				        null,
				        null,
				        null,
				        'P',
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        1,
				        1,
				        1,
				        '$nascido',
				        '$data_prenhes',
				        '$data_previsao'
				    )";

			}
			else {
				$sql = "INSERT INTO tbl_item_cobertura(
						tbl_ite_cobertura_numero_id,
						tbl_ite_cobertura_numero_item,
						tbl_ite_cobertura_codigo_id_animal,
						tbl_ite_cobertura_codigo_animal,
						tbl_ite_cobertura_codigo_alfa,
						tbl_ite_cobertura_codigo_numerico,
						tbl_ite_cobertura_data_emissao,
						tbl_ite_cobertura_codigo_touro_semen,
						tbl_ite_cobertura_lote_semen,
						tbl_ite_cobertura_data_diagnostico,
						tbl_ite_cobertura_resultado_diagnostico,
						tbl_ite_cobertura_nome_inseminador,
						tbl_ite_cobertura_destino,
						tbl_ite_cobertura_dia_1,
						tbl_ite_cobertura_dia_2,
						tbl_ite_cobertura_dia_3,
						tbl_ite_cobertura_dia_4,
						tbl_ite_cobertura_dia_5,
						tbl_ite_cobertura_dia_6,
						tbl_ite_cobertura_observacao,
						tbl_ite_cobertura_numero_cobertura,
						tbl_ite_cobertura_qtd_diagnosticos_positivo,
						tbl_ite_cobertura_aborto_natimorto,
						tbl_ite_cobertura_nascido,
						tbl_ite_cobertura_data_prenhes,
						tbl_ite_cobertura_previsao_parto

				    )VALUES(
				        '$id_monta_natural',
				        1,
				        '$codigo_mae',
				        '$codigo_animal_edi',
						'$codigo_alfa',
						'$codigo_numerico',
				        '$data_sistema',
				        null,
				        null,
				        '$data_ocorrencia',
				        'P',
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        null,
				        1,
				        1,
				        1,
				        '$nascido',
				        '$data_prenhes',
				        '$data_previsao'
				    )";
			}

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do item para monta natural. ' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

			// grava numero cobertura na movimentacao do estoque nascimento
			if ($id_mov_estoque_nascimento!=0) {
				$sql = ("UPDATE tbl_movimentacao_estoque SET 
							 tbl_mov_estoque_cobertura_numero_id='$id_monta_natural',  
				 	         tbl_mov_estoque_cobertura_numero_item=1,
				 	         tbl_mov_estoque_cobertura_monta_natural='S'
					 	WHERE tbl_mov_estoque_numero_id  ='$id_mov_estoque_nascimento'");

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado) {
				   	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do número da cobertura monta natural no registro do estoque nascimento. ' . $erro_mysql));
					mysqli_close($conector);
					exit;
				}
			}

			// grava numero cobertura na movimentacao do estoque morte
			if ($id_mov_estoque_morte!=0) {
				$sql = ("UPDATE tbl_movimentacao_estoque SET 
							 tbl_mov_estoque_cobertura_numero_id='$id_monta_natural',  
				 	         tbl_mov_estoque_cobertura_numero_item=1,
				 	         tbl_mov_estoque_cobertura_monta_natural='S'
					 	WHERE tbl_mov_estoque_numero_id  ='$id_mov_estoque_morte'");

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado) {
				   	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do número da cobertura monta natural no registro do estoque morte. ' . $erro_mysql));
					mysqli_close($conector);
					exit;
				}
			}

			// grava numero cobertura na movimentacao do estoque aborto
			if ($id_mov_estoque_aborto!=0) {
				$sql = ("UPDATE tbl_movimentacao_estoque SET 
							 tbl_mov_estoque_cobertura_numero_id='$id_monta_natural',  
				 	         tbl_mov_estoque_cobertura_numero_item=1,
				 	         tbl_mov_estoque_cobertura_monta_natural='S'
					 	WHERE tbl_mov_estoque_numero_id  ='$id_mov_estoque_aborto'");

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado) {
				   	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do número da cobertura monta natural no registro do estoque aborto. ' . $erro_mysql));
					mysqli_close($conector);
					exit;
				}
			}

			$sql = ("UPDATE tbl_cobertura SET 
				    		tbl_cobertura_codigo_estacao_monta='$id_monta_natural'
				    WHERE tbl_cobertura_id ='$id_monta_natural'");

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro de cobertura. ' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
		}
    }
    else {
		$sql = ("UPDATE tbl_item_cobertura SET 
				tbl_ite_cobertura_aborto_natimorto=1,
		   		tbl_ite_cobertura_nascido='$nascido'
			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			      tbl_ite_cobertura_numero_item = '$item_cobertura'");
	    $resultado = mysqli_query($conector,$sql);
		$resposta = array('success' => true, 'message' => 'Registro incluido com sucesso.');
	    $erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
    }
}

header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;

?>