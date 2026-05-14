<?php
    include "conecta_mysql.inc";

	$request = mysqli_real_escape_string($conector, $_POST["query"]);
	$local = mysqli_real_escape_string($conector, $_POST["local"]);
	$data_sistema = date("Y-m-d");

/*    $tbl_par = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
                        WHERE tbl_par_codigo_local='$local' AND 
                              tbl_par_lixeira=0 AND 
                              tbl_par_estacao_monta_final>='$data_sistema'");  

    $num_rows = mysqli_num_rows($tbl_par);

    if ($num_rows!=0){
        $reg_para = mysqli_fetch_object($tbl_par);
        $id_estacao = $reg_para->tbl_par_estacao_id;
    }
    else {
    	$id_estacao = 0;
    }

	$query = "SELECT * FROM tbl_animais 
		INNER JOIN tbl_item_cobertura
			    ON tbl_ite_cobertura_codigo_id_animal = tbl_animal_codigo_id
		INNER JOIN tbl_cobertura
		        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			 WHERE tbl_animal_lixeira=0 AND 
				   tbl_animal_ativo='S' AND 
				   tbl_animal_sexo='F' AND
				   tbl_animal_codigo_fazenda = $local AND
				   tbl_cobertura_controle = 'C' AND
				   tbl_cobertura_codigo_estacao_monta = '$id_estacao' AND
				   tbl_cobertura_encerrada = 'S' AND 
				   tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
				   (tbl_animal_codigo_alfa LIKE '%".$request."%' OR
				    tbl_animal_codigo_numerico LIKE '%".$request."%')"; 
*/

	$query = "SELECT * FROM tbl_animais 
		WHERE tbl_animal_lixeira=0 AND 
	    	  tbl_animal_ativo='S' AND 
	          tbl_animal_sexo='F' AND
		      tbl_animal_codigo_fazenda = $local AND
	         (tbl_animal_codigo_alfa LIKE '%".$request."%' OR
	          tbl_animal_codigo_numerico LIKE '%".$request."%')
	    ORDER BY tbl_animal_codigo_numerico ASC"; 

	$result = mysqli_query($conector, $query);

	$data = array();

	if(mysqli_num_rows($result) > 0) {
 		while($row = mysqli_fetch_assoc($result)) {
	        $data_nascimento =  $row["tbl_animal_data_nascimento"];
	        $data_acompanhamento_calculo = date("Y-m-d");
	        $date = new DateTime($data_nascimento); 
	        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
	        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
	        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
	        $idade = $idade_acompanhamento_mostra_anos+
	                 $idade_acompanhamento_mostra_meses;

	        if ($idade>7) {
	 			$codigo_alfa = $row["tbl_animal_codigo_alfa"];

	 			if ($row["tbl_animal_codigo_alfa"]!='') {
		  			$data[] = $row["tbl_animal_codigo_alfa"] . '-' . $row["tbl_animal_codigo_numerico"];
	 			}
	 			else {
		  			$data[] = $row["tbl_animal_codigo_numerico"];
	 			}
	        }
 		}

 		echo json_encode($data);
	}
?>
