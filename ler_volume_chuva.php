<?php 
	include "conecta_mysql.inc";

	$local= $_POST['local_id'];
	$data_chuva= $_POST['data_chuva'];

	$chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
	    WHERE tbl_chuva_data = '$data_chuva' AND tbl_chuva_local='$local'");

	$num_rows = mysqli_num_rows($chuva);  

	if ($num_rows!=0) {
	    while ($reg_chuva = mysqli_fetch_object($chuva)) {
	        $volume_chuva=$reg_chuva->tbl_chuva_volume_chuva;
	    }

	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Volume de chuva já cadastrado para essa data. Volume cadastrado: '. $volume_chuva . ' mm. Deseja alterar para o volume atual?'));
	    mysqli_close($conector);
		exit;
	}
	else {
	   	header('Content-type: application/json');
	    echo json_encode(array('success' => true, 'message' => 'Pode registrar'));
		mysqli_close($conector);
		exit;
	}

?>