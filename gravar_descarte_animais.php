<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$data_descarte = date("Y/m/d");
$mensagem = 0;

include "conecta_mysql.inc";

$local = $_POST['local_id'];
$filtros = $_POST['filtros'];
$array_animais = $_POST['array_animais_selecionados'];

$matriz_animais = explode("<|>", $array_animais);
$quantidade_itens = count($matriz_animais);

$sql = "INSERT INTO tbl_matrizes (
			tbl_matrizes_controle,
			tbl_matrizes_data,
			tbl_matrizes_codigo_local,
			tbl_matrizes_qtd_animais,
			tbl_matrizes_filtros,
			tbl_matrizes_incluido_em,
			tbl_matrizes_incluido_por,
			tbl_matrizes_alterado_em,
			tbl_matrizes_alterado_por,
			tbl_matrizes_lixeira,
			tbl_matrizes_lixeira_em,
			tbl_matrizes_lixeira_por
	        ) VALUES (
	        'D',
	        '$data_descarte',
			'$local',
			'$quantidade_itens',
			'$filtros',
			'$data_sistema',
			'$nomeusuario',
			null,
			null,
			0,
			null,
			null
		)";

$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
   	header('Content-type: application/json');
   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o registro do descarte'. $erro_mysql));
   	mysqli_close($conector);
	exit;
} 

$numero_descarte = mysqli_insert_id($conector);
$numero_descarte = str_pad($numero_descarte, 9, "0", STR_PAD_LEFT);

$resposta = array('success' => true, 'message' => 'Descarte registrado com sucesso.');

for($i=0; $i < $quantidade_itens; $i++) {
	$codigo_id_animal = $matriz_animais[$i];

	$numero_item = $i + 1;

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

	$sql = "INSERT INTO tbl_item_matrizes (
			tbl_ite_matrizes_numero_id,
			tbl_ite_matrizes_numero_item,
			tbl_ite_matrizes_codigo_id_animal,
			tbl_ite_matrizes_codigo_animal,
			tbl_ite_matrizes_data_emissao,
			tbl_ite_matrizes_observacao
			)
			VALUES ('$numero_descarte', 
			        '$numero_item',
					'$codigo_id_animal',
					'$codigo_edi',
					'$data_descarte',
					null
	)";
										   
	$resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro do descarte'. $erro_mysql));
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
}

header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
?>