<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

	$codigo_local = 77;

    $sql = mysqli_query($conector, "SELECT * FROM tbl_nutricao
        INNER JOIN tbl_pasto 
                ON tbl_pasto_id = tbl_nutricao_codigo_pasto
        WHERE tbl_nutricao_lixeira = 0 AND 
              tbl_nutricao_codigo_local = '$codigo_local' AND 
              tbl_nutricao_dias_consumo = 999
        ORDER BY tbl_nutricao_id ASC"); 

    $num_rows = mysqli_num_rows($sql);

    $qtd = 0;
	$zerado = 0;
	$calculado = 0;

    if ($num_rows>0) {
        while ($reg = mysqli_fetch_object($sql)) {
      
      		$qtd++;

	        $codigo_id = $reg->tbl_nutricao_id;
	        $pasto = $reg->tbl_nutricao_codigo_pasto;
			$desc_pasto = $reg->tbl_pasto_descricao;
			$data_pasto_sem = $reg->tbl_pasto_data_sem_animais;
			$data_nutricao = $reg->tbl_nutricao_data;
			$qtd_produto = $reg->tbl_nutricao_quantidade_produto;
			$qtd_animais = $reg->tbl_nutricao_qtd_animais;

            $tbl_pasto = mysqli_query($conector, "select * from tbl_animal_pasto where tbl_animal_pasto_id='$pasto'");
            $num_rows = mysqli_num_rows($tbl_pasto);

			echo 'Pasto: ' . $desc_pasto . ' Animais: ' . $num_rows .'</br>';

            if ($num_rows!=0){
	            $grava = ("UPDATE tbl_nutricao SET
	                tbl_nutricao_dias_consumo=0,
	                tbl_nutricao_consumo_cabeca_dia=0
	                WHERE tbl_nutricao_id='$codigo_id'");

	            $resultado = mysqli_query($conector, $grava);

				echo 'ID: ' . $codigo_id . ' Registro Zerado: </br>';
				$zerado++;
            }
            else {
            	$data_anterior = substr($data_pasto_sem, 0, 10);
            	$firstDate  = new DateTime($data_anterior);
                $secondDate = new DateTime($data_nutricao);
                $intvl = $firstDate->diff($secondDate);
                $dias_calculados = $intvl->days;

                if ($dias_calculados==0) {
                    $dias_calculados = 1;
                }

                $consumo = ($qtd_produto/$qtd_animais/$dias_calculados)*1000;

	            $grava = ("UPDATE tbl_nutricao SET
	                tbl_nutricao_dias_consumo=$dias_calculados,
	                tbl_nutricao_consumo_cabeca_dia=$consumo,
	                tbl_nutricao_encerrada='S'
	                WHERE tbl_nutricao_id='$codigo_id'");

	            $resultado = mysqli_query($conector, $grava);

				echo 'ID: ' . $codigo_id . ' Registro 999 calculado: </br>';
				$calculado++;

            }
		}
	}

	echo 'Total de Registros: ' . $qtd .'</br>';
	echo 'Total Zerado: ' . $zerado. '</br>';
	echo 'Total calculado: ' . $calculado . '</br>';
    echo 'Fim do processamento';

?>
