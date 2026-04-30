<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

	$data_sistema = date("Y-m-d H:i:s");
	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];

	for ($i=0; $i < 129; $i++) { 
			$sql = "INSERT INTO tbl_movimentacao_estoque (
			tbl_mov_estoque_codigo_id_animal,
			tbl_mov_estoque_data_emissao,
			tbl_mov_estoque_nascimento,
			tbl_mov_estoque_codigo_categoria,
			tbl_mov_estoque_local,
			tbl_mov_estoque_entrada_saida,
			tbl_mov_estoque_tipo_movimentacao,
			tbl_mov_estoque_local_origem,
			tbl_mov_estoque_local_destino,
			tbl_mov_estoque_codigo_movimentacao,
			tbl_mov_estoque_codigo_pasto,
			tbl_mov_estoque_codigo_raca,
			tbl_mov_estoque_codigo_pelagem,
			tbl_mov_estoque_sexo,
			tbl_mov_estoque_primeiro_peso
			        ) 
				    VALUES (
				    		null,
				  		    '2021-10-05',
						    null,
						    null,
						    57,
						    'E',
							'C',
							57,
							null,
			                null,
			                null,
			                null,
			                null,
			                null,
			                null
			        )";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
				echo $erro_mysql . '</br>';
			}
			else {
				echo 'gravei ' . $i . '</br>'; 
			}
		}
