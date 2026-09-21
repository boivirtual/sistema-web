<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$data_sistema = date("Y-m-d H:i:s");

	$codigo_local_chuva= $_POST['codigo_local_chuva'];
	$volume_chuva = $_POST['volume_chuva'];
	$data_chuva= $_POST['data_chuva'];

	$sql = ("DELETE FROM tbl_chuva WHERE tbl_chuva_data='$data_chuva' AND 
		                                 tbl_chuva_local='$codigo_local_chuva'");
	$resultado = mysqli_query($conector,$sql);
    
	$sql = "INSERT INTO tbl_chuva (
	    	tbl_chuva_data,
	    	tbl_chuva_local,
			tbl_chuva_volume_chuva,    	
			tbl_chuva_observacao,
			tbl_chuva_incluido_em,
			tbl_chuva_incluido_por,
			tbl_chuva_alterado_em,
			tbl_chuva_alterado_por,
			tbl_chuva_lixeira,
			tbl_chuva_lixeira_em,
			tbl_chuva_lixeira_por
	        ) VALUES (
	        '$data_chuva',
	        '$codigo_local_chuva',
			'$volume_chuva',
			null,
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
	    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar o volume de chuva '. $erro_mysql));
	    mysqli_close($conector);
		exit;
	} 
	else {
	   	header('Content-type: application/json');
	    echo json_encode(array('success' => true, 'message' => 'Volume registrado com sucesso'));
		mysqli_close($conector);
		exit;
	}

?>