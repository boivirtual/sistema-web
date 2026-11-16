<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data_selecao = date("Y-m-d");
$mensagem = 0;

include "conecta_mysql.inc";

$local = $_POST['local_id'];
$filtros = $_POST['filtros'];
$id_cobertura = $_POST['id_cobertura_lista_sem_grupo'];
$opcao_femeas_sem_grupo = $_POST['opcao_femeas_sem_grupo'];

if ($id_cobertura=='') {
	$paridas_ate = $_POST['paridas_ate'];

	if (isset($_POST['vacas_paridas'])) {
		$vacas_paridas=$_POST['vacas_paridas'];
	}
	else {
		$vacas_paridas='';
	}

	if (isset($_POST['vacas_solteiras'])) {
		$vacas_solteiras=$_POST['vacas_solteiras'];
	}
	else {
		$vacas_solteiras='';
	}

	if (isset($_POST['novilhas'])) {
		$novilhas=$_POST['novilhas'];
	}
	else {
		$novilhas='';
	}

	$idade_de = $_POST['idade_de'];
	$idade_ate = $_POST['idade_ate'];
	$peso_acima = $_POST['peso_acima'];

	if ($idade_de=='') {$idade_de=0;}
	if ($idade_ate=='') {$idade_ate=0;}
	if ($peso_acima=='') {$peso_acima=0;}
}
else {
    $tbl_cobertura = mysqli_query($conector, "select * from tbl_cobertura
        where tbl_cobertura_lixeira=0 and 
              tbl_cobertura_id = '$id_cobertura'");

    $num_row = mysqli_num_rows($tbl_cobertura);

    if ($num_row!=0) {
        $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
		$paridas_ate = $reg_cobertura->tbl_cobertura_filtro_data_paridas;
		$vacas_paridas=$reg_cobertura->tbl_cobertura_filtro_vacas_paridas;
		$vacas_solteiras=$reg_cobertura->tbl_cobertura_filtro_vacas_solteiras;
		$novilhas=$reg_cobertura->tbl_cobertura_filtro_novilhas;
		$idade_de = $reg_cobertura->tbl_cobertura_filtro_idade_de;
		$idade_ate = $reg_cobertura->tbl_cobertura_filtro_idade_ate;
		$peso_acima = $reg_cobertura->tbl_cobertura_filtro_peso_acima;
    }
    else {
		$paridas_ate = '';
		$vacas_paridas='';
		$vacas_solteiras='';
		$novilhas='';
		$idade_de = 0;
		$idade_ate = 0;
		$peso_acima = 0;
    }
}

$id_estacao_monta = $_POST['id_estacao_monta'];

$array_animais = $_POST['array_matrizes'];
$array_grupos = $_POST['array_grupos'];
$array_ordem_grupos = $_POST['ordem_grupos'];

$matriz_animais = explode("<|>", $array_animais);
$grupos_animais = explode("<|>", $array_grupos);
$ordem_grupos = explode("<|>", $array_ordem_grupos);
$quantidade_itens = count($matriz_animais);
$quantidade_grupos = count($ordem_grupos);

for($g=0; $g < $quantidade_grupos; $g++) {
	$codigo_grupo = $ordem_grupos[$g];

	$qtd_animais_grupo = 0;

	for ($i=0; $i < $quantidade_itens ; $i++) { 
		$codigo_grupo_animais = $grupos_animais[$i];

		if ($codigo_grupo_animais==$codigo_grupo) {
			$qtd_animais_grupo++;
		}
	}

	if ($codigo_grupo==999) {
		// Gravar descarte
		$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
	            WHERE tbl_cobertura_lixeira=0 AND 
	                  tbl_cobertura_controle ='D' AND 
	                  tbl_cobertura_codigo_local = '$local' AND 
	                  tbl_cobertura_codigo_estacao_monta = '$id_estacao_monta'");

		$num_rows = mysqli_num_rows($tbl_cobertura);    

		if ($num_rows!=0) {
	    	$reg_cobertura = mysqli_fetch_object($tbl_cobertura);
	    	$id_descarte = $reg_cobertura->tbl_cobertura_id;
	    	$qtd_animais = $reg_cobertura->tbl_cobertura_qtd_animais;

	    	$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	            WHERE tbl_ite_cobertura_numero_id ='$id_descarte' 
	            ORDER BY tbl_ite_cobertura_numero_item DESC LIMIT 1");

			$num_rows_item = mysqli_num_rows($tbl_item);    

			if ($num_rows_item!=0) {
			    $reg_item = mysqli_fetch_object($tbl_item);
			    $numero_item =  $reg_item->tbl_ite_cobertura_numero_item;
			}
			else {
				$numero_item = 0;
			}

			for ($i=0; $i < $quantidade_itens ; $i++) { 
				$codigo_grupo_animais = $grupos_animais[$i];

				if ($codigo_grupo_animais==$codigo_grupo) {
					$codigo_id_animal = $matriz_animais[$i];

					$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
				            WHERE tbl_animal_codigo_id ='$codigo_id_animal'");
					$reg_animal = mysqli_fetch_object($tbl_animal); 
										
					$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
					$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;

					if ($codigo_alfa==''){
						$codigo_edi = $codigo_numerico; 
					}
					else {
						$codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
					}

					$numero_item++;
	    			$qtd_animais++;

					$sql = "INSERT INTO tbl_item_cobertura (
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
										tbl_ite_cobertura_numero_cobertura
								)
								VALUES ('$id_descarte', 
								        '$numero_item',
										'$codigo_id_animal',
										'$codigo_edi',
										'$codigo_alfa',
										'$codigo_numerico',
										'$data_selecao',
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										0
						)";
															   
					$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura descarte'. $erro_mysql));
					   	mysqli_close($conector);
						exit;
					}

					$sql = "UPDATE tbl_animais SET
								tbl_animal_descarte_reproducao='S',
								tbl_animal_selecioanada_reproducao=null,
								tbl_animal_em_estacao_monta=null,
								tbl_animal_descarte_em='$data_sistema',
								tbl_animal_descarte_por='$nomeusuario'
				    		WHERE tbl_animal_codigo_id='$codigo_id_animal'";

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
				  		header('Content-type: application/json');
				   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal descarte.' . $erro_mysql));
						mysqli_close($conector);
						exit;
					} 

				}
			}

			$sql = "UPDATE tbl_cobertura SET tbl_cobertura_qtd_animais='$qtd_animais'
					 WHERE tbl_cobertura_id='$id_descarte'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a cobertura descarte'. $erro_mysql));
			   	mysqli_close($conector);
				exit;
			} 
		}
		else {
			// incluir a cobertura de descarte
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
							tbl_cobertura_filtro_peso_acima
				        ) VALUES (
				        'D',
				        '$data_selecao',
						'$local',
						999,
						'$id_estacao_monta',
						0,
						'$qtd_animais_grupo',
						null,
						'$data_sistema',
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
						null
					)";
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a cobertura descarte'. $erro_mysql));
			   	mysqli_close($conector);
				exit;
			} 

			$id_descarte = mysqli_insert_id($conector);
			$id_descarte = str_pad($id_descarte, 9, "0", STR_PAD_LEFT);
			
			$numero_item = 0;

			for ($i=0; $i < $quantidade_itens ; $i++) { 
				$codigo_grupo_animais = $grupos_animais[$i];

				if ($codigo_grupo_animais==$codigo_grupo) {
					$codigo_id_animal = $matriz_animais[$i];

					$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
				            WHERE tbl_animal_codigo_id ='$codigo_id_animal'");
					$reg_animal = mysqli_fetch_object($tbl_animal); 
										
					$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
					$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;

					if ($codigo_alfa==''){
						$codigo_edi = $codigo_numerico; 
					}
					else {
						$codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
					}

					$numero_item++;

					$sql = "INSERT INTO tbl_item_cobertura (
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
										tbl_ite_cobertura_numero_cobertura
								)
								VALUES ('$id_descarte', 
								        '$numero_item',
										'$codigo_id_animal',
										'$codigo_edi',
										'$codigo_alfa',
										'$codigo_numerico',
										'$data_selecao',
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										0
						)";
															   
					$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura descarte'. $erro_mysql));
					   	mysqli_close($conector);
						exit;
					}

					$sql = "UPDATE tbl_animais SET
								tbl_animal_descarte_reproducao='S',
								tbl_animal_selecioanada_reproducao=null,
								tbl_animal_em_estacao_monta=null,
								tbl_animal_descarte_em='$data_sistema',
								tbl_animal_descarte_por='$nomeusuario'
				    		WHERE tbl_animal_codigo_id='$codigo_id_animal'";

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
				  		header('Content-type: application/json');
				   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal descarte.' . $erro_mysql));
						mysqli_close($conector);
						exit;
					} 
				}
			}
		}
	}
	else {
		// gravar grupos 
		$controle = 'C';

		$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
	            WHERE tbl_cobertura_lixeira=0 AND 
	                  tbl_cobertura_controle ='$controle' AND 
	                  tbl_cobertura_codigo_local = '$local' AND 
	                  tbl_cobertura_codigo_estacao_monta = '$id_estacao_monta' AND
	                  tbl_cobertura_codigo_grupo = '$codigo_grupo'");

		$num_rows = mysqli_num_rows($tbl_cobertura);    

		if ($num_rows!=0) {
	    	$reg_cobertura = mysqli_fetch_object($tbl_cobertura);
	    	$num_cobertura = $reg_cobertura->tbl_cobertura_id;
	    	$qtd_animais = $reg_cobertura->tbl_cobertura_qtd_animais;

	    	$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	            WHERE tbl_ite_cobertura_numero_id ='$num_cobertura' 
	            ORDER BY tbl_ite_cobertura_numero_item DESC LIMIT 1");

			$num_rows_item = mysqli_num_rows($tbl_item);    

			if ($num_rows_item!=0) {
			    $reg_item = mysqli_fetch_object($tbl_item);
			    $numero_item =  $reg_item->tbl_ite_cobertura_numero_item;
			}
			else {
				$numero_item = 0;
			}

			for ($i=0; $i < $quantidade_itens ; $i++) { 
				$codigo_grupo_animais = $grupos_animais[$i];

				if ($codigo_grupo_animais==$codigo_grupo) {
					$codigo_id_animal = $matriz_animais[$i];

					$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
				            WHERE tbl_animal_codigo_id ='$codigo_id_animal'");
					$reg_animal = mysqli_fetch_object($tbl_animal); 
										
					$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
					$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;

					if ($codigo_alfa==''){
						$codigo_edi = $codigo_numerico; 
					}
					else {
						$codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
					}

					$numero_item++;
	    			$qtd_animais++;

					$sql = "INSERT INTO tbl_item_cobertura (
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
										tbl_ite_cobertura_numero_cobertura
								)
								VALUES ('$num_cobertura', 
								        '$numero_item',
										'$codigo_id_animal',
										'$codigo_edi',
										'$codigo_alfa',
										'$codigo_numerico',
										'$data_selecao',
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										0
						)";
															   
					$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura'. $erro_mysql));
					   	mysqli_close($conector);
						exit;
					}

					$sql = "UPDATE tbl_animais SET
	    						   tbl_animal_selecioanada_reproducao='S',
								   tbl_animal_em_estacao_monta='S'
				    		WHERE tbl_animal_codigo_id='$codigo_id_animal'";

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
				  		header('Content-type: application/json');
				   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal.' . $erro_mysql));
						mysqli_close($conector);
						exit;
					} 
				}
			} 

			$sql = "UPDATE tbl_cobertura SET tbl_cobertura_qtd_animais='$qtd_animais'
					 WHERE tbl_cobertura_id='$num_cobertura'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a cobertura'. $erro_mysql));
			   	mysqli_close($conector);
				exit;
			} 
		}
		else {
			if ($paridas_ate=='') {
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
								tbl_cobertura_filtro_peso_acima
					        ) VALUES (
					        '$controle',
					        '$data_selecao',
							'$local',
							'$codigo_grupo',
							'$id_estacao_monta',
							0,
							'$qtd_animais_grupo',
							'$filtros',
							'$data_sistema',
							'$nomeusuario',
							null,
							null,
							0,
							null,
							null,
							'$vacas_paridas',
							null,
							'$vacas_solteiras',
							'$novilhas',
							'$idade_de',
							'$idade_ate',
							'$peso_acima'
						)";
			}
			else {
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
									tbl_cobertura_filtro_peso_acima
						        ) VALUES (
						        '$controle',
						        '$data_selecao',
								'$local',
								'$codigo_grupo',
								'$id_estacao_monta',
								0,
								'$qtd_animais_grupo',
								'$filtros',
								'$data_sistema',
								'$nomeusuario',
								null,
								null,
								0,
								null,
								null,
								'$vacas_paridas',
								'$paridas_ate',
								'$vacas_solteiras',
								'$novilhas',
								'$idade_de',
								'$idade_ate',
								'$peso_acima'
							)";
			}

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o grupo de fêmeas'. $erro_mysql));
			   	mysqli_close($conector);
				exit;
			} 

			$numero_cobertura = mysqli_insert_id($conector);
			$numero_cobertura = str_pad($numero_cobertura, 9, "0", STR_PAD_LEFT);

			$numero_item = 0;

			for ($i=0; $i < $quantidade_itens ; $i++) { 
				$codigo_grupo_animais = $grupos_animais[$i];

				if ($codigo_grupo_animais==$codigo_grupo) {
					$codigo_id_animal = $matriz_animais[$i];

					$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
				            WHERE tbl_animal_codigo_id ='$codigo_id_animal'");
					$reg_animal = mysqli_fetch_object($tbl_animal); 
										
					$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
					$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;

					if ($codigo_alfa==''){
						$codigo_edi = $codigo_numerico; 
					}
					else {
						$codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
					}

					$numero_item++;

					$sql = "INSERT INTO tbl_item_cobertura (
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
										tbl_ite_cobertura_numero_cobertura
								)
								VALUES ('$numero_cobertura', 
								        '$numero_item',
										'$codigo_id_animal',
										'$codigo_edi',
										'$codigo_alfa',
										'$codigo_numerico',
										'$data_selecao',
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										null,
										0
						)";
															   
					$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura'. $erro_mysql));
					   	mysqli_close($conector);
						exit;
					}

					$sql = "UPDATE tbl_animais SET
	    						   tbl_animal_selecioanada_reproducao='S',
								   tbl_animal_em_estacao_monta='S'
				    		WHERE tbl_animal_codigo_id='$codigo_id_animal'";

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
				  		header('Content-type: application/json');
				   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal.' . $erro_mysql));
						mysqli_close($conector);
						exit;
					} 
				}
			} 

		} // fim do else num_rows
	} // fim do else controle = C
} // fim do for qtd grupos

// ajusta lista de animais conforme opcao de gravar grupos

if ($opcao_femeas_sem_grupo!='') {
	if ($opcao_femeas_sem_grupo=='M') {

		for ($i=0; $i < $quantidade_itens; $i++) { 
			$codigo_id_animal = $matriz_animais[$i];

		    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
		            WHERE tbl_ite_cobertura_numero_id ='$id_cobertura' AND 
		                  tbl_ite_cobertura_codigo_id_animal = '$codigo_id_animal'");

		    $num_rows = mysqli_num_rows($tbl_item);

		    if ($num_rows!=0) {
		    	$reg_item = mysqli_fetch_object($tbl_item);
		    	$numero_item = $reg_item->tbl_ite_cobertura_numero_item;

				$sql = ("DELETE FROM tbl_item_cobertura 
					     WHERE tbl_ite_cobertura_numero_id='$id_cobertura' AND 
					           tbl_ite_cobertura_numero_item='$numero_item'");
				$resultado = mysqli_query($conector,$sql);
		    }
		}

	    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	            WHERE tbl_ite_cobertura_numero_id ='$id_cobertura'");
		$itens_restantes = mysqli_num_rows($tbl_item);

		$sql = "UPDATE tbl_cobertura SET
    				   tbl_cobertura_qtd_animais='$itens_restantes',
    				   tbl_cobertura_planilha_processada='S'
		   		WHERE tbl_cobertura_id='$id_cobertura'";

		$resultado = mysqli_query($conector,$sql);
	}
	else {
		$sql = ("DELETE FROM tbl_cobertura WHERE tbl_cobertura_id='$id_cobertura'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	  		header('Content-type: application/json');
	   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro exclusão da lista de fêmeas (tbl_cobertura).' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("DELETE FROM tbl_item_cobertura 
			     WHERE tbl_ite_cobertura_numero_id='$id_cobertura'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	  		header('Content-type: application/json');
	   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro exclusão da lista de fêmeas (tbl_item_cobertura.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
	}
}

$resposta = array('success' => true, 'message' => 'Grupo(s) de Fêmea(s) gravado(s) com sucesso.');

header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
?>