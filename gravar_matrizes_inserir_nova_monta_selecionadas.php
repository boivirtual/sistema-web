<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$data_selecao = date("Y-m-d");

include "conecta_mysql.inc";

$local = $_POST['codigo_local'];
$id_cobertura_lista_excel = $_POST['id_cobertura'];
$array_itens = $_POST['array_itens'];
$matriz_itens = explode("<|>", $array_itens);
$quantidade_itens = count($matriz_itens);

for($i=0; $i < $quantidade_itens; $i++) {
	$codigo_id_animal = $matriz_itens[$i];

	$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	    WHERE tbl_animal_codigo_id = '$codigo_id_animal'");

	$reg_animal = mysqli_fetch_object($tbl_animal); 
	$codigo_alfa_consulta = $reg_animal->tbl_animal_codigo_alfa;
	$codigo_numerico_consulta = $reg_animal->tbl_animal_codigo_numerico;

	if ($codigo_alfa_consulta==''){
		$codigo_edi = $codigo_numerico_consulta; 
	}
	else {
		$codigo_edi = $codigo_alfa_consulta.'-'.$codigo_numerico_consulta; 
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
		tbl_cobertura_planilha_processada
		) VALUES (
		    'M',
			'$data_selecao',
			'$local',
			0,
			0,
			0,
			1,
			'',
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
			null,
			''
		)";
				
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a fêmea'. $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	$numero_cobertura = mysqli_insert_id($conector);
	$numero_cobertura = str_pad($numero_cobertura, 9, "0", STR_PAD_LEFT);

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
						tbl_ite_cobertura_numero_cobertura,
						tbl_ite_cobertura_data_prenhes,
						tbl_ite_cobertura_previsao_parto
						)
		VALUES ('$numero_cobertura', 
			1,
			'$codigo_id_animal',
			'$codigo_edi',
			'$codigo_alfa_consulta',
			'$codigo_numerico_consulta',
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
			0,
			null,
			null
		)";

	$resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura'. $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	// Quando o tipo de cobertura for Monta Natural então a estacao de monta recebe o mesmo numero da cobertura (Para ser utilizado no Relatório Situação Reprodutiva Individual)

	$sql = ("UPDATE tbl_cobertura SET 
		    		tbl_cobertura_codigo_estacao_monta='$numero_cobertura'
		    WHERE tbl_cobertura_id ='$numero_cobertura'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro de cobertura' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}

// se os registros vierão de uma lista de excel, então tem que deletar a lista do banco de dados

if ($id_cobertura_lista_excel!=0 || $id_cobertura_lista_excel!='') {
    $sql = ("DELETE FROM tbl_item_cobertura 
       	WHERE tbl_ite_cobertura_numero_id='$id_cobertura_lista_excel'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir os itens da lista de cobertura em excel' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	$sql = ("DELETE FROM tbl_cobertura 
		WHERE tbl_cobertura_id ='$id_cobertura_lista_excel'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir a lista de cobertura em excel' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}		 		

$resposta = array('success' => true, 'message' => 'Fêmeas selecionadas incluídas com sucesso.');
header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);

?>