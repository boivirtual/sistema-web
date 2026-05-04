<?php
    include "conecta_mysql.inc";

	$request = mysqli_real_escape_string($conector, $_POST["query"]);
	$local = mysqli_real_escape_string($conector, $_POST["local"]);

	$query = "SELECT * FROM tbl_animais 
		WHERE tbl_animal_sexo='F' AND 
              tbl_animal_ativo='S' AND
              tbl_animal_lixeira=0 AND 
             (tbl_animal_descarte_reproducao='' OR 
              tbl_animal_descarte_reproducao IS NULL) AND
              tbl_animal_codigo_fazenda='$local' AND
		     (tbl_animal_codigo_alfa LIKE '%".$request."%' OR
	 	      tbl_animal_codigo_numerico LIKE '%".$request."%')
	 	ORDER BY tbl_animal_codigo_numerico ASC"; 

	$result = mysqli_query($conector, $query);

	$data = array();

	if(mysqli_num_rows($result) > 0) {
 		while($row = mysqli_fetch_assoc($result)) {
 			$codigo_alfa = $row["tbl_animal_codigo_alfa"];

 			if ($row["tbl_animal_codigo_alfa"]!='') {
	  			$data[] = $row["tbl_animal_codigo_alfa"] . '-' . $row["tbl_animal_codigo_numerico"];
 			}
 			else {
	  			$data[] = $row["tbl_animal_codigo_numerico"];
 			}
 		}

 		echo json_encode($data);
	}
?>
