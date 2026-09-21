<?php
	$documento = $_REQUEST['id'];

	include "conecta_mysql.inc";

    $rs = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        WHERE tbl_ite_cobertura_numero_id ='$documento'");
    $num_rows = mysqli_num_rows($rs);

    if ($num_rows!=0){
        while ($fila = mysqli_fetch_object($rs)){
            $codigo_animal_id = $fila->tbl_ite_cobertura_codigo_id_animal;
			$d0 = $fila->tbl_ite_cobertura_dia_1;

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
		        else {
					$sql = "UPDATE tbl_animais SET
								   tbl_animal_numero_coberturas='$numero_cobertura',
								   
								   tbl_animal_selecioanada_reproducao=''
								   
								   
					       	 WHERE tbl_animal_codigo_id='$codigo_animal_id'";
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
        }

        $sql = ("DELETE FROM tbl_item_cobertura WHERE tbl_ite_cobertura_numero_id='$documento'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	        $valor[0]=9;
	        $valor[1]='Erro ao estornar os itens da lista!' . $erro_mysql;
	        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	        die ($str);
		}

		$sql = ("DELETE FROM tbl_cobertura WHERE tbl_cobertura_id ='$documento'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	        $valor[0]=9;
	        $valor[1]='Erro ao estornar esse registro! ' . $erro_mysql;
	        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	        die ($str);
		}

	    $valor[0]=0;
	    $valor[1]='Registro estornado com sucesso! ';
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
    }
    else {
        $valor[0]=9;
        $valor[1]='Erro na leitura dos itens!';
        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
        die ($str);
    }

	mysqli_close($conector);

?>