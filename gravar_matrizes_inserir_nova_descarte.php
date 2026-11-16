<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$data_selecao = date("Y-m-d");

include "conecta_mysql.inc";

$codigo_alfa_numerico = $_POST['codigo_id'];
$local = $_POST['codigo_local'];

$codigo_numerico_consulta = substr($codigo_alfa_numerico, -9);

if (strlen($codigo_alfa_numerico)!=9){
    $data = explode("-", $codigo_alfa_numerico);
    $codigo_alfa_consulta = $data[0];
}
else {
    $codigo_alfa_consulta = '';
}

if ($codigo_alfa_consulta==''){
	$codigo_edi = $codigo_numerico_consulta; 
}
else {
	$codigo_edi = $codigo_alfa_consulta.'-'.$codigo_numerico_consulta; 
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_alfa = '$codigo_alfa_consulta' AND 
          tbl_animal_codigo_numerico = '$codigo_numerico_consulta'");

$reg_animal = mysqli_fetch_object($tbl_animal); 
$codigo_id_animal = $reg_animal->tbl_animal_codigo_id;
								
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
		null
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
						tbl_ite_cobertura_previsao_parto,
        				tbl_ite_cobertura_resultado_diagnostico,
        				tbl_ite_cobertura_data_diagnostico,
        				tbl_ite_cobertura_qtd_diagnosticos_positivo,
        				tbl_ite_cobertura_positivo_alterado_em,
        				tbl_ite_cobertura_positivo_alterado_por
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
						0,
						null,
						null,
						null,
						null,
						null,
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

$sql = "UPDATE tbl_animais SET
        tbl_animal_descarte_reproducao='S',
	    tbl_animal_descarte_em='$data_sistema',
		tbl_animal_descarte_por='$nomeusuario'
	WHERE tbl_animal_codigo_id='$codigo_id_animal'";

$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal.' . $erro_mysql));
	mysqli_close($conector);
	exit;
} 

$resposta = array('success' => true, 'message' => 'Fêmea incluída com sucesso.');
header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);

?>