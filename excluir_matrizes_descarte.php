<?php
	$documento = $_REQUEST['id'];

	include "conecta_mysql.inc";

    $rs = mysqli_query($conector, "SELECT * FROM tbl_item_matrizes
        WHERE tbl_ite_matrizes_numero_id ='$documento'");
    $num_rows = mysqli_num_rows($rs);

    if ($num_rows!=0){
        while ($fila = mysqli_fetch_object($rs)){
            $codigo_animal_id = $fila->tbl_ite_matrizes_codigo_id_animal;

            $tbl_animal = mysqli_query($conector, "select * from tbl_animais 
                        where tbl_animal_codigo_id ='$codigo_animal_id'"); 
            $num_row_animal = mysqli_num_rows($tbl_animal);

            if ($num_row_animal!=0) {
				$sql = "UPDATE tbl_animais SET
						tbl_animal_descarte_reproducao='',
						tbl_animal_descarte_em=null,
						tbl_animal_descarte_por=null
				    	WHERE tbl_animal_codigo_id='$codigo_animal_id'";

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
			       $valor[0]=9;
			       $valor[1]='Erro ao atualizar o registro do animal! ' . $mysql_erro;
			       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
			       die ($str);
				}
            }
        }

        $sql = ("DELETE FROM tbl_item_matrizes WHERE tbl_ite_matrizes_numero_id='$documento'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	        $valor[0]=9;
	        $valor[1]='Erro estornar o item do descarte! ' . $mysql_erro;
	        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	        die ($str);
		}

		$sql = ("DELETE FROM tbl_matrizes WHERE tbl_matrizes_id ='$documento'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	        $valor[0]=9;
	        $valor[1]='Erro estornar o descarte! ' . $mysql_erro;
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
        $valor[1]='Erro na leitura dos itens do descarte! ' . $mysql_erro;
        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
        die ($str);
    }

	mysqli_close($conector);

?>