<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

	$data_sistema = date("Y-m-d H:i:s");
	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$total_nascido = 0;
	$total_cobertura = 0;
	$total_natimorto = 0;
	$total_aborto = 0;

	$local = 77;

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
    	WHERE tbl_animal_codigo_fazenda='$local' AND 
    	      tbl_animal_ativo = 'S'
        ORDER BY tbl_animal_data_nascimento ASC");

    $num_rows = mysqli_num_rows($tbl_animais);

    echo 'Total de animais: ' . $num_rows . '</br>';

    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
			$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
			$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
			$data_nascimento = $reg_animal->tbl_animal_data_nascimento;

			echo 'Animal: ' . $codigo_alfa.$codigo_numerico . ' Nascimento: ' . $data_nascimento;

		    $animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
		    	WHERE tbl_animal_pasto_local ='$local' AND 
		    	      tbl_animal_pasto_situacao = 'A' AND 
		    	      tbl_animal_pasto_nascimento = '$data_nascimento'");

		    $num_rows_pasto = mysqli_num_rows($animal_pasto);

		    if ($num_rows_pasto==0) {
		    	echo '</br>';
		    }
		    else {
		    	echo ' Não achei o nascimento </br>';
		    }

		}
	}


	echo 'Animais no pasto sem nascimento </br>';

	$animal_pasto= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
		WHERE tbl_animal_pasto_local ='$local' AND 
		      tbl_animal_pasto_situacao = 'A' AND 
		      tbl_animal_pasto_nascimento is null");

		$num_rows_pasto = mysqli_num_rows($animal_pasto);

		if ($num_rows_pasto!=0) {
			while ($reg_animal = mysqli_fetch_object($animal_pasto)){
				$item = $reg_animal->tbl_animal_pasto_numero_item;

				echo 'Item: ' . $item . '</br>'; 
			}
		}

/*
    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
        WHERE tbl_animal_estacao_monta_nascimento!=''");

    $num_rows = mysqli_num_rows($tbl_animais);

    echo 'Total de animais: ' . $num_rows . '</br>';

    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
			$codigo_id = $reg_animal->tbl_animal_codigo_id ;
			$codigo_mae = $reg_animal->tbl_animal_codigo_mae;
            $estacao = $reg_animal->tbl_animal_estacao_monta_nascimento; 

		    $tbl_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
		        WHERE tbl_mov_estoque_codigo_id_animal='$codigo_id' AND 
		              tbl_mov_estoque_codigo_mae='$codigo_mae'");

		    $num_rows_estoque = mysqli_num_rows($tbl_estoque);

		    if ($num_rows_estoque!=0) {
		    	$reg_estoque = mysqli_fetch_object($tbl_estoque);
		    	$tipo_movimentacao = $reg_estoque->tbl_mov_estoque_tipo_movimentacao;
		    	$id_estoque = $reg_estoque->tbl_mov_estoque_numero_id;

		    	$total_nascido++;
		    }

		    $tbl_item= mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
		        INNER JOIN tbl_cobertura
		                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		             WHERE tbl_ite_cobertura_codigo_id_animal='$codigo_mae' AND 
		                   tbl_cobertura_codigo_estacao_monta='$estacao'"); 

		    $num_rows_item = mysqli_num_rows($tbl_item);

		    if ($num_rows_item!=0) {
		    	$reg_item = mysqli_fetch_object($tbl_item);

		    	$nascido = $reg_item->tbl_ite_cobertura_nascido;
				$cobertura = $reg_item->tbl_ite_cobertura_numero_id;
				$item = $reg_item->tbl_ite_cobertura_numero_item;

		    	echo 'Animal: ' . $codigo_id . ' Mae: ' . $codigo_mae .
		    	     ' Estação: ' . $estacao . ' Tipo: ' . $tipo_movimentacao .
		    	     ' Cobertura: ' . $cobertura.'-'.$item . '</br>';

		    	$total_cobertura++;

		    	$sql = ("UPDATE tbl_movimentacao_estoque SET 
					tbl_mov_estoque_cobertura_numero_id='$cobertura',
	   				tbl_mov_estoque_cobertura_numero_item='$item'
				WHERE tbl_mov_estoque_numero_id  ='$id_estoque'");

    			$resultado = mysqli_query($conector,$sql);
    			$erro_mysql = mysqli_error($conector);
    			
    			if (!$resultado) {
    				echo $erro_mysql . '</br>';

    			}
		    }
        }
    }

    echo 'Total de nascidos: ' . $total_nascido . '</br>';
    echo 'Total de cobertura: ' . $total_cobertura . '</br></br>';


	$tbl_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
		WHERE tbl_mov_estoque_codigo_id_animal=999999999 AND 
		      (tbl_mov_estoque_tipo_movimentacao='N' OR tbl_mov_estoque_tipo_movimentacao='M')");

	$num_rows_estoque = mysqli_num_rows($tbl_estoque);

	if ($num_rows_estoque!=0) {
		while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) { 
			$codigo_mae = $reg_estoque->tbl_mov_estoque_codigo_mae;
		    $id_estoque = $reg_estoque->tbl_mov_estoque_numero_id;

		    $tbl_item= mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
		        INNER JOIN tbl_cobertura
		                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		             WHERE tbl_ite_cobertura_nascido='M' AND
		                   tbl_ite_cobertura_codigo_id_animal='$codigo_mae'"); 

			$num_rows_item = mysqli_num_rows($tbl_item);

			if ($num_rows_item!=0) {
			    $reg_item = mysqli_fetch_object($tbl_item); 
				$data = $reg_item->tbl_ite_cobertura_data_emissao;
				$codigo_vaca = $reg_item->tbl_ite_cobertura_codigo_id_animal;
				$codigo_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;

				$cobertura = $reg_item->tbl_ite_cobertura_numero_id;
				$item = $reg_item->tbl_ite_cobertura_numero_item;

				echo 'Mae: ' . $codigo_vaca . ' Estação: ' . $codigo_estacao . ' Cobertura: ' . $cobertura.'-'.$item .'</br>';

				$total_natimorto++;

		    	$sql = ("UPDATE tbl_movimentacao_estoque SET 
					tbl_mov_estoque_cobertura_numero_id='$cobertura',
	   				tbl_mov_estoque_cobertura_numero_item='$item'
				WHERE tbl_mov_estoque_numero_id  ='$id_estoque'");

    			$resultado = mysqli_query($conector,$sql);
    			$erro_mysql = mysqli_error($conector);
    			
    			if (!$resultado) {
    				echo $erro_mysql . '</br>';
    			}
			}
		}
	}

    echo 'Total Natimorto: ' . $total_natimorto . '</br></br>';

	$tbl_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
		WHERE tbl_mov_estoque_codigo_id_animal=999999999 AND 
		      (tbl_mov_estoque_tipo_movimentacao='A' OR 
		       tbl_mov_estoque_tipo_movimentacao='B')");

	$num_rows_estoque = mysqli_num_rows($tbl_estoque);

	if ($num_rows_estoque!=0) {
		while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) { 
			$codigo_mae = $reg_estoque->tbl_mov_estoque_codigo_mae;
		    $id_estoque = $reg_estoque->tbl_mov_estoque_numero_id;

		    $tbl_item= mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
		        INNER JOIN tbl_cobertura
		                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		             WHERE tbl_ite_cobertura_nascido='A' AND
		                   tbl_ite_cobertura_codigo_id_animal='$codigo_mae'"); 

			$num_rows_item = mysqli_num_rows($tbl_item);

			if ($num_rows_item!=0) {
			    $reg_item = mysqli_fetch_object($tbl_item); 
				$data = $reg_item->tbl_ite_cobertura_data_emissao;
				$codigo_vaca = $reg_item->tbl_ite_cobertura_codigo_id_animal;
				$codigo_estacao = $reg_item->tbl_cobertura_codigo_estacao_monta;

				$cobertura = $reg_item->tbl_ite_cobertura_numero_id;
				$item = $reg_item->tbl_ite_cobertura_numero_item;

				echo 'Mae: ' . $codigo_vaca . ' Estação: ' . $codigo_estacao . ' Cobertura: ' . $cobertura.'-'.$item .'</br>';

				$total_aborto++;

		    	$sql = ("UPDATE tbl_movimentacao_estoque SET 
					tbl_mov_estoque_cobertura_numero_id='$cobertura',
	   				tbl_mov_estoque_cobertura_numero_item='$item'
				WHERE tbl_mov_estoque_numero_id  ='$id_estoque'");

    			$resultado = mysqli_query($conector,$sql);
    			$erro_mysql = mysqli_error($conector);
    			
    			if (!$resultado) {
    				echo $erro_mysql . '</br>';
    			}
			}
		}
	}

    echo 'Total Aborto: ' . $total_aborto . '</br></br>';
*/


    echo 'Fim do processamento';

?>
