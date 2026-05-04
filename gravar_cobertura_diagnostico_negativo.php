<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$data_selecao = date("Y-m-d");
$mensagem = 0;

include "conecta_mysql.inc";

$cobertura_numero_id = $_POST['cobertura_numero_id'];
$codigo_id_animal = $_POST['codigo_id'];
$codigo_animal = $_POST['codigo_animal'];
$estacao_monta = $_POST['estacao_monta'];
$local = $_POST['local'];
$ordem = $_POST['ordem'];
$opcao_nova_cobertura = $_POST['opcao_nova_cobertura'];

if ($opcao_nova_cobertura=='D') {
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
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização para descarte do animal. '. $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	// leitura do animal para pegar o codigo alfa / numerico separados
	$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_codigo_id='$codigo_id_animal'");

	$num_rows_animais = mysqli_num_rows($tbl_animal);    

	if ($num_rows_animais!=0) {
    	$reg_animais = mysqli_fetch_object($tbl_animal);
    	$codigo_alfa = $reg_animais->tbl_animal_codigo_alfa;
    	$codigo_numerico = $reg_animais->tbl_animal_codigo_numerico;
    }
    else {
    	$codigo_alfa = '';
    	$codigo_numerico = 0;
    }

	$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_controle ='D' AND 
              tbl_cobertura_codigo_local = '$local' AND 
              tbl_cobertura_codigo_estacao_monta = '$estacao_monta'");

	$num_rows = mysqli_num_rows($tbl_cobertura);    

	if ($num_rows!=0) {
    	$reg_cobertura = mysqli_fetch_object($tbl_cobertura);
    	$id_descarte = $reg_cobertura->tbl_cobertura_id;
    	$qtd_animais = $reg_cobertura->tbl_cobertura_qtd_animais;
    	$qtd_animais++;

    	$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
            WHERE tbl_ite_cobertura_numero_id ='$id_descarte' 
            ORDER BY tbl_ite_cobertura_numero_item DESC LIMIT 1");

		$num_rows_item = mysqli_num_rows($tbl_item);    

		if ($num_rows_item!=0) {
		    $reg_item = mysqli_fetch_object($tbl_item);
		    $numero_item_novo =  $reg_item->tbl_ite_cobertura_numero_item;
		    $numero_item_novo++;
		}
		else {
			$numero_item_novo = 1;
		}

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
							'$numero_item_novo',
							'$codigo_id_animal',
							'$codigo_animal',
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

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do animal no grupo descarte. '. $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		$sql = "UPDATE tbl_cobertura SET tbl_cobertura_qtd_animais='$qtd_animais'
			WHERE tbl_cobertura_id='$id_descarte'";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração da qtd de animais na cobertura. '. $erro_mysql));
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
					'$estacao_monta',
					0,
					1,
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
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão da cobertura descarte. '. $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		$id_descarte = mysqli_insert_id($conector);
		$id_descarte = str_pad($id_descarte, 9, "0", STR_PAD_LEFT);

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
							1,
							'$codigo_id_animal',
							'$codigo_animal',
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

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do animal no novo grupo descarte. '. $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
	}
	// Fim incluir cobertura de descarte novo

	$sql = "UPDATE tbl_item_cobertura SET
        tbl_ite_cobertura_resultado_diagnostico = 'N',
        tbl_ite_cobertura_destino = 'E',
        tbl_ite_cobertura_negativo_em = '$data_sistema',
        tbl_ite_cobertura_negativo_por = '$nomeusuario'
        WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id' AND 
        	  tbl_ite_cobertura_numero_item = '$ordem'";

    $resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização para descarte do animal na cobertura. '. $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
}
else {
	$sql = "UPDATE tbl_animais SET
		tbl_animal_selecioanada_reproducao=null,
		tbl_animal_em_estacao_monta=null,
		tbl_animal_aguardando_diagnostico=null
	WHERE tbl_animal_codigo_id='$codigo_id_animal'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização para liberar o animal. '. $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	$sql = "UPDATE tbl_item_cobertura SET
        tbl_ite_cobertura_resultado_diagnostico = 'N',
        tbl_ite_cobertura_destino = null,
        tbl_ite_cobertura_negativo_em = '$data_sistema',
        tbl_ite_cobertura_negativo_por = '$nomeusuario'
        
        WHERE tbl_ite_cobertura_numero_id = '$cobertura_numero_id' AND 
        	  tbl_ite_cobertura_numero_item = '$ordem'";

    $resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização para descarte do animal na cobertura. '. $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
}

?>