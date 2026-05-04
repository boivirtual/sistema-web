<?php 
include "conecta_mysql.inc";

$local = $_POST['local'];
$arr = [];

$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
	INNER JOIN tbl_pessoa 
			ON tbl_pessoa_id = tbl_pasto_codigo_local
	WHERE tbl_pasto_codigo_local='$local' AND 
		  tbl_pasto_lixeira=0");

$num_rows_pasto = mysqli_num_rows($tbl_pasto);	

if ($num_rows_pasto==0) {
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);;
	mysqli_close($conector);
	exit;
}

while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
	$id_pasto = $reg_pasto->tbl_pasto_id;
	$id_pasto = strval($id_pasto);
	$descricao = $reg_pasto->tbl_pasto_descricao;
	$id_capim = $reg_pasto->tbl_pasto_tipo_capim;
	$latitude = $reg_pasto->tbl_pessoa_latitude_fazenda;
	$longitude = $reg_pasto->tbl_pessoa_longitude_fazenda;

	$tbl_capim = mysqli_query($conector, "SELECT * FROM tbl_tipo_capim
		WHERE tbl_tipo_capim_id='$id_capim' AND 
			  tbl_tipo_capim_lixeira=0");

	$num_rows_capim = mysqli_num_rows($tbl_capim);	

	if ($num_rows_capim!=0){
		$reg_capim = mysqli_fetch_object($tbl_capim);
		$descricao_capim = $reg_capim->tbl_tipo_capim_descricao;
	}
	else {
		$descricao_capim = '';
	}

	$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
		WHERE tbl_animal_pasto_id='$id_pasto'");

	$num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);	

	$dias_com_animais = 0;
	$dias_sem_animais = 0;

	if ($num_rows_animal_pasto!=0) {
		// Calcula dias com animais no pasto
		$dataAtual = new DateTime();
		$dataCom = new DateTime($reg_pasto->tbl_pasto_data_com_animais);
		$diff = $dataAtual->diff($dataCom);
		$dias_com_animais = $diff->days;

		$tem_animal = 'S';
	}
	else {
		// Calcula dias sem animais no pasto
	    $dataAtual = new DateTime();
	    $dataSem = new DateTime($reg_pasto->tbl_pasto_data_sem_animais);
	    $diff = $dataAtual->diff($dataSem);
	    $dias_sem_animais = $diff->days;

		$tem_animal = 'N';
	}

    $p = (object)[
    	'id_pasto' => $id_pasto,
        'descricao' => $descricao,
        'tem_animal' => $tem_animal,
        'total_animais' => $num_rows_animal_pasto,
        'dias_com_animais' => $dias_com_animais,
        'dias_sem_animais' => $dias_sem_animais,
        'descricao_capim' => $descricao_capim,
        'latitude' => $latitude,
        'longitude' => $longitude,
    ];

    $arr[] = $p;
}


//echo $tem_animal;

/*$resposta = array('success' => true, 'message' => 'Sucesso', 'tem_animal' => $tem_animal);
header('Content-type: application/json');
echo json_encode($resposta);*/

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
mysqli_close($conector);
exit;

?>