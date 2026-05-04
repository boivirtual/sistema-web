<?php
	$documento = $_POST['id_cobertura'];
	$item = $_POST['numero_item'];
	$codigo_animal_id = $_POST['id_animal'];

	include "conecta_mysql.inc";

    $rs = mysqli_query($conector, "SELECT * FROM tbl_cobertura
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_id ='$documento'");
    $num_rows = mysqli_num_rows($rs);

    if ($num_rows!=0){
        $fila = mysqli_fetch_object($rs);

        $controle = $fila->tbl_cobertura_controle;
        $qtd_animais = $fila->tbl_cobertura_qtd_animais;
        $qtd_animais--;

	    if ($qtd_animais==0) {
			$sql = ("DELETE FROM tbl_cobertura WHERE tbl_cobertura_id ='$documento'");
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
		        $valor[0]=9;
		        $valor[1]='Erro ao excluir o documento! ' . $erro_mysql;
		        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
		        die ($str);
			}
	    }
	    else {
			$sql = "UPDATE tbl_cobertura SET
						   tbl_cobertura_qtd_animais='$qtd_animais'
	   		    	 WHERE tbl_cobertura_id ='$documento'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			    $valor[0]=9;
			    $valor[1]='Erro ao atualizar o documento! ' . $erro_mysql;
			    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
			    die ($str);
			}
	    }
    }

    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        WHERE tbl_ite_cobertura_numero_id ='$documento' AND 
              tbl_ite_cobertura_codigo_id_animal = '$codigo_animal_id'");

	$num_rows_item = mysqli_num_rows($tbl_item);    

	if ($num_rows_item!=0) {
		$reg_item = mysqli_fetch_object($tbl_item);
		$d0 = $reg_item->tbl_ite_cobertura_dia_1;
	}
	else {
		$d0 = '';
	}

    $tbl_animal = mysqli_query($conector, "select * from tbl_animais 
            where tbl_animal_codigo_id ='$codigo_animal_id'"); 
    $num_row_animal = mysqli_num_rows($tbl_animal);

    if ($num_row_animal!=0) {
        $reg_animais = mysqli_fetch_object($tbl_animal);
        $numero_cobertura = $reg_animais->tbl_animal_numero_coberturas;

		if ($d0=='S' && $numero_cobertura>0) {
			$numero_cobertura--;
		}

        if ($numero_cobertura==0 || $numero_cobertura=='') {
        	if ($controle=='D') {
				$sql = "UPDATE tbl_animais SET
							   tbl_animal_selecioanada_reproducao='',
							   tbl_animal_em_estacao_monta=null,
							   tbl_animal_aguardando_diagnostico=null,
							   tbl_animal_prenhe=null,
							   tbl_animal_parida=null,
							   tbl_animal_solteira=null,
							   tbl_animal_numero_coberturas=null,

							   tbl_animal_descarte_reproducao='',
							   tbl_animal_descarte_em=null,
							   tbl_animal_descarte_por=null
				       	 WHERE tbl_animal_codigo_id='$codigo_animal_id'";
        	}
        	else {
				$sql = "UPDATE tbl_animais SET
							   tbl_animal_selecioanada_reproducao='',
							   tbl_animal_em_estacao_monta=null,
							   tbl_animal_aguardando_diagnostico=null,
							   tbl_animal_prenhe=null,
							   tbl_animal_parida=null,
							   tbl_animal_solteira=null,
							   tbl_animal_numero_coberturas=null
				       	 WHERE tbl_animal_codigo_id='$codigo_animal_id'";
        	}
        }
        else {
        	if ($controle=='D') {
				$sql = "UPDATE tbl_animais SET
							   tbl_animal_selecioanada_reproducao=null,
							   tbl_animal_numero_coberturas='$numero_cobertura',
							   tbl_animal_aguardando_diagnostico=null,
							   tbl_animal_descarte_reproducao='',
							   tbl_animal_descarte_em=null,
							   tbl_animal_descarte_por=null
				       	 WHERE tbl_animal_codigo_id='$codigo_animal_id'";
        	}
        	else {
				$sql = "UPDATE tbl_animais SET
							   tbl_animal_selecioanada_reproducao=null,
							   tbl_animal_numero_coberturas='$numero_cobertura',
							   tbl_animal_aguardando_diagnostico=null
				       	 WHERE tbl_animal_codigo_id='$codigo_animal_id'";
        	}
        }

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    $valor[0]=9;
		    $valor[1]='Erro ao atualizar o registro do animal! ' . $erro_mysql;
		    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
		    die ($str);
		}
    }

    $sql = ("DELETE FROM tbl_item_cobertura
       	           WHERE tbl_ite_cobertura_numero_id='$documento' AND 
       	                 tbl_ite_cobertura_numero_item = '$item'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
	    $valor[1]='Erro estornar o animal da lista! ' . $erro_mysql;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	// reorganizar o campo tbl_ite_cobertura_numero_item temporario

    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        WHERE tbl_ite_cobertura_numero_id ='$documento' 
            ORDER BY tbl_ite_cobertura_numero_item ASC");

	$num_rows_item = mysqli_num_rows($tbl_item);    
	$numero_item_temporario = 9000;

	if ($num_rows_item!=0) {
		while ($reg_item = mysqli_fetch_object($tbl_item)) {
		    $numero_item_antigo =  $reg_item->tbl_ite_cobertura_numero_item;

		    $numero_item_temporario++;

			$sql = "UPDATE tbl_item_cobertura SET
						   tbl_ite_cobertura_numero_item='$numero_item_temporario'
			       	 WHERE tbl_ite_cobertura_numero_id='$documento' AND 
			       	       tbl_ite_cobertura_numero_item='$numero_item_antigo'";
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			    $valor[0]=9;
			    $valor[1]='Erro refazer os itens temporários! ' . $erro_mysql;
			    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
			    die ($str);
			}
		}
	}	   

	// reorganizar o campo tbl_ite_cobertura_numero_item nova sequencia

    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        WHERE tbl_ite_cobertura_numero_id ='$documento' 
            ORDER BY tbl_ite_cobertura_numero_item ASC");

	$num_rows_item = mysqli_num_rows($tbl_item);    
	$numero_item_novo = 0;

	if ($num_rows_item!=0) {
		while ($reg_item = mysqli_fetch_object($tbl_item)) {
		    $numero_item_antigo =  $reg_item->tbl_ite_cobertura_numero_item;

		    $numero_item_novo++;

			$sql = "UPDATE tbl_item_cobertura SET
						   tbl_ite_cobertura_numero_item='$numero_item_novo'
			       	 WHERE tbl_ite_cobertura_numero_id='$documento' AND 
			       	       tbl_ite_cobertura_numero_item='$numero_item_antigo'";
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			    $valor[0]=9;
			    $valor[1]='Erro refazer os itens! ' . $erro_mysql;
			    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
			    die ($str);
			}
		}
	}	   

	if ($qtd_animais==0) {
	    $valor[0]=99;
	    $valor[1]='Registro excluido com sucesso! ';
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}
	else {
	    $valor[0]=0;
	    $valor[1]='Registro excluido com sucesso! ';
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	mysqli_close($conector);

?>