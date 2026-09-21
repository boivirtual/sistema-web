<?php 
	include "conecta_mysql.inc";

	$registros = 0;

	$rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
		INNER JOIN tbl_movimentacao
		        ON tbl_movimentacao_id = tbl_ite_movimentacao_numero_id
        WHERE tbl_ite_movimentacao_data_emissao>='2021-04-01' AND 
              tbl_ite_movimentacao_data_emissao<='2021-04-30'");

    $num_rows = $num_rows = mysqli_num_rows($rs);

    if ($num_rows!=0) {
    	while ($reg_mov = mysqli_fetch_object($rs)) {
    		$codigo_id = $reg_mov->tbl_ite_movimentacao_codigo_id_animal;
	        $codigo_tipo = $reg_mov->tbl_movimentacao_tipo;
	        $data_movimentacao = $reg_mov->tbl_ite_movimentacao_data_emissao;
			$local_origem = $reg_mov->tbl_movimentacao_codigo_local_origem;
			$local_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;
			$numero_movimentacao = $reg_mov->tbl_movimentacao_id;

			$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	              WHERE tbl_animal_codigo_id='$codigo_id'");

	       	$reg_animal = mysqli_fetch_object($tbl_animal);
	        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;

	        if ($codigo_tipo==3) {
	        	$sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_origem',
	                        'S',
	                        'V',
	                        '$local_origem',
	                        '$local_destino',
	                        '$numero_movimentacao'
	                )";
	        
	            $resultado = mysqli_query($conector,$sql);
	            $erro_mysql = mysqli_error($conector);

	            if (!$resultado){
	            	echo 'erro mov venda: '. $erro_mysql  . '</br>';
	            }
	            else {
	            	$registros++;
	            	echo 'Venda: ' . $codigo_id . ' - ' . $data_movimentacao   . '</br>';
	            }
	        }
	        else if ($codigo_tipo==5) {
	        	$sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_origem',
	                        'S',
	                        'T',
	                        '$local_origem',
	                        '$local_destino',
	                        '$numero_movimentacao'
	                )";
	        
	            $resultado = mysqli_query($conector,$sql);
	            $erro_mysql = mysqli_error($conector);

	            if (!$resultado){
	            	echo 'erro saida transf: '. $erro_mysql . '</br>';
	            }
	            else {
	            	$registros++;
	            	echo 'Saida Transf: '.$codigo_id.' - '.$data_movimentacao.'</br>';
	            }

	            $sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_destino',
	                        'E',
	                        'T',
	                        '$local_origem',
	                        '$local_destino',
	                        '$numero_movimentacao'
	                )";
	        
	            $resultado = mysqli_query($conector,$sql);
	            $erro_mysql = mysqli_error($conector);

	            if (!$resultado){
	                echo 'Erro entrada transf' . $erro_mysql . '</br>';
	            }
	            else {
	            	$registros++;
	            	echo 'Entrada Transf: '.$codigo_id.' - '.$data_movimentacao.'</br>';
	            }

	        }
	        else if ($codigo_tipo==888) {
	        	$sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_origem',
	                        'S',
	                        'M',
	                        '$local_origem',
	                        '$local_destino',
	                        '$numero_movimentacao'
	                )";
	        
	            $resultado = mysqli_query($conector,$sql);
	            $erro_mysql = mysqli_error($conector);

	            if (!$resultado){
	                echo 'Erro saida morte' . $erro_mysql . '</br>';
	            }
	            else {
	            	$registros++;
	            	echo 'Morte: '.$codigo_id.' - '.$data_movimentacao.'</br>';
	            }
	        }
	        else if ($codigo_tipo==999) {
	        	$sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_origem',
	                        'S',
	                        'O',
	                        '$local_origem',
	                        '$local_destino',
	                        '$numero_movimentacao'
	                )";
	        
	            $resultado = mysqli_query($conector,$sql);
	            $erro_mysql = mysqli_error($conector);

	            if (!$resultado){
	                echo 'Erro outra saida' . $erro_mysql . '</br>';
	            }
	            else {
	            	$registros++;
	            	echo 'Outra Saida: '.$codigo_id.' - '.$data_movimentacao.'</br>';
	            }
	        }
    	}
    }

    echo 'Fim ' . $registros;
?>