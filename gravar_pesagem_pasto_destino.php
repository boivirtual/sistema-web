<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$id_pesagem= $_POST['id_pesagem'];
	$pasto= $_POST['pasto'];
	$grupo= $_POST['grupo'];

	$sql = "UPDATE tbl_item_pesagem SET
			tbl_ite_pesagem_pasto_destino='$pasto'
	    WHERE tbl_ite_pesagem_numero_id ='$id_pesagem' AND 
	          tbl_ite_pesagem_grupo_pasto_destino='$grupo'";

	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Gruop alterado com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao alterar o grupo ' . $erro_mysql));
	    	exit;
		} 

	header('Content-type: application/json');
	echo json_encode($resposta);
	mysqli_close($conector);
	exit;

/*	if (empty($total_pesados)) {
		$total_pesados = 0;
	}

	$array_itens = $_POST['array_itens_pesagem_lote'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

	$data_sistema = date("Y-m-d H:i:s");

	if ($numero_pesagem_id && $tipo_gravacao==2 && $total_pesados==0) {
		$sql = ("DELETE FROM tbl_pesagem WHERE tbl_pesagem_id ='$numero_pesagem_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão da pesagem.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("DELETE FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		mysqli_close($conector);
		exit;
	}

	if ($numero_pesagem_id && $tipo_gravacao==2) {

	    $sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_codigo_local='$local',
			tbl_pesagem_codigo_epoca='$epoca_pesagem',
			tbl_pesagem_lote='$descricao_lote',
			tbl_pesagem_qtd_animais_pesados='$total_pesados',
			tbl_pesagem_peso_kg='$peso_total_kg',
			tbl_pesagem_peso_arroba='$peso_total_arroba',
			tbl_pesagem_peso_medio_kg='$peso_medio_kg',
			tbl_pesagem_peso_medio_arroba='$peso_medio_arroba',
			tbl_pesagem_filtros='$descricao_filtro',
			tbl_pesagem_alterado_em='$data_sistema',
			tbl_pesagem_alterado_por='$nomeusuario'
	    WHERE tbl_pesagem_id='$numero_pesagem_id'";

	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Pesagem alterada com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a pesagem ' . $erro_mysql));
	    	exit;
		} 

		$sql = ("DELETE FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$categoria = ltrim($itens[0]);
			$categoria = rtrim($categoria);
			$peso = $itens[1];
			$sexo = $itens[2];
			$peso_medio = $itens[3];
			$arroba = $itens[4];
			$arroba_media = $itens[5];
			$qtd_animais = $itens[6];
			$grupo_destino = $itens[7];

			$numero_item = $i + 1;
			
		    $sql = "INSERT INTO tbl_item_pesagem (
		            tbl_ite_pesagem_numero_id,
		            tbl_ite_pesagem_numero_item,
		            tbl_ite_pesagem_data_emissao,
		            tbl_ite_pesagem_codigo_id_animal,
		            tbl_ite_pesagem_codigo_animal,
					tbl_ite_pesagem_peso,
					tbl_ite_pesagem_sexo,
					tbl_ite_pesagem_nascimento,
					tbl_ite_pesagem_raca,
					tbl_ite_pesagem_pelagem,
					tbl_ite_pesagem_mae,
					tbl_ite_pesagem_observacao,
					tbl_ite_pesagem_categoria,
					tbl_ite_pesagem_peso_medio,
					tbl_ite_pesagem_arroba,
					tbl_ite_pesagem_arroba_media,
					tbl_ite_pesagem_qtd_animais,
					tbl_ite_pesagem_grupo_pasto_destino
		        ) VALUES (
		            '$numero_pesagem_id',
		            '$numero_item',
		            '$data_pesagem',
		            null,
		            null,
		            '$peso',
		            '$sexo',
		            null,
		            null,
		            null,
		            null,
		            null,
		            '$categoria',
		            '$peso_medio',
		            '$arroba',
		            '$arroba_media',
					'$qtd_animais',
					'$grupo_destino'
		    )";
		    $resultado = mysqli_query($conector,$sql);
		}    

		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
		
	    $resposta = array('success' => true, 'message' => 'Pesagem Incluida com sucesso.', 'numero_doc' => $numero_pesagem_id);
		$erro_mysql = mysqli_error($conector);

		header('Content-type: application/json');
		echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}

    if ($tipo_gravacao==1){
	    $sql = "INSERT INTO tbl_pesagem (
	    	tbl_pesagem_controle,
	    	tbl_pesagem_data,
			tbl_pesagem_codigo_local,
			tbl_pesagem_codigo_epoca,
			tbl_pesagem_lote,
			tbl_pesagem_qtd_animais_pesados,
			tbl_pesagem_peso_kg,
			tbl_pesagem_peso_arroba,
			tbl_pesagem_peso_medio_kg,
			tbl_pesagem_peso_medio_arroba,
			tbl_pesagem_filtros,
			tbl_pesagem_finalizada,
			tbl_pesagem_incluido_em,
			tbl_pesagem_incluido_por,
			tbl_pesagem_alterado_em,
			tbl_pesagem_alterado_por,
			tbl_pesagem_lixeira,
			tbl_pesagem_lixeira_em,
			tbl_pesagem_lixeira_por,
			tbl_pesagem_pasto,
			tbl_pesagem_categoria,
			tbl_pesagem_sexo,
			tbl_pesagem_codigo_movimentacao,
			tbl_pesagem_origem
	        ) VALUES (
	        'L',
	        '$data_pesagem',
			'$local',
			'$epoca_pesagem',
			'$descricao_lote',
			'$total_pesados',
			'$peso_total_kg',
			'$peso_total_arroba',
			'$peso_medio_kg',
			'$peso_medio_arroba',
			'$descricao_filtro',
			'$pesagem_finalizada',
			'$data_sistema',
			'$nomeusuario',
			null,
			null,
			0,
			null,
			null,
			'$pasto',
			null,
			null,
			0
		)";

	    $resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a pesagem'. $erro_mysql));
	    	mysqli_close($conector);
			exit;
		} 

		$numero_pesagem = mysqli_insert_id($conector);
		$numero_pesagem = str_pad($numero_pesagem, 9, "0", STR_PAD_LEFT);

	    $resposta = array('success' => true, 'message' => 'Pesagem incluída com sucesso.', 'numero_doc' => $numero_pesagem);

		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$categoria = ltrim($itens[0]);
			$categoria = rtrim($categoria);
			$peso = $itens[1];
			$sexo = $itens[2];
			$peso_medio = $itens[3];
			$arroba = $itens[4];
			$arroba_media = $itens[5];
			$qtd_animais = $itens[6];
			$grupo_destino = $itens[7];

			$numero_item = $i + 1;
			
		    $sql = "INSERT INTO tbl_item_pesagem (
		            tbl_ite_pesagem_numero_id,
		            tbl_ite_pesagem_numero_item,
		            tbl_ite_pesagem_data_emissao,
		            tbl_ite_pesagem_codigo_id_animal,
		            tbl_ite_pesagem_codigo_animal,
					tbl_ite_pesagem_peso,
					tbl_ite_pesagem_sexo,
					tbl_ite_pesagem_nascimento,
					tbl_ite_pesagem_raca,
					tbl_ite_pesagem_pelagem,
					tbl_ite_pesagem_mae,
					tbl_ite_pesagem_observacao,
					tbl_ite_pesagem_categoria,
					tbl_ite_pesagem_peso_medio,
					tbl_ite_pesagem_arroba,
					tbl_ite_pesagem_arroba_media,
					tbl_ite_pesagem_qtd_animais,
					tbl_ite_pesagem_grupo_pasto_destino
		        ) VALUES (
		            '$numero_pesagem',
		            '$numero_item',
		            '$data_pesagem',
		            null,
		            null,
		            '$peso',
		            '$sexo',
		            null,
		            null,
		            null,
		            null,
		            null,
		            '$categoria',
		            '$peso_medio',
		            '$arroba',
		            '$arroba_media',
					'$qtd_animais',
					'$grupo_destino'
		    )";
		    $resultado = mysqli_query($conector,$sql);
		}    

		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
		
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}
*/
?>