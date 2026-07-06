<?php 
//  Grava pesagem digitada para sistema em lotes
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_pesagem_id= $_POST['numero_pesagem_id'];
	$tipo_gravacao = $_POST['tipo_gravacao'];
	$data_pesagem = $_POST['data_pesagem'];
	$loca_pesagem= $_POST['local_pesagem'];
	$epoca_pesagem= $_POST['epoca_pesagem'];
	$descricao_filtro= $_POST['descricao_filtro'];
	$descricao_lote= $_POST['descricao_lote'];
	$pesagem_finalizada='N';
	$total_a_pesar = $_POST['total_a_pesar'];
	$total_pesados= $_POST['total_pesados'];
	$peso_total_kg= $_POST['peso_total_kg'];
	$peso_total_arroba= $_POST['peso_total_arroba'];
	$peso_medio_kg= $_POST['peso_medio_kg'];
	$peso_medio_arroba= $_POST['peso_medio_arroba'];

    // Pegar quantos animais existem na fazenda por categoria
	for ($i = 1; $i <=5; $i++) {
	    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
	    $total_cat_macho[$j]=0;
	    $total_cat_femea[$j]=0;
	}

	$sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	    WHERE tbl_animal_pasto_situacao='A' AND
	          tbl_animal_pasto_local='$loca_pesagem'"); 

	$num_rows = mysqli_num_rows($sql);

	if ($num_rows!=0){
	    while ($reg_animal = mysqli_fetch_object($sql)){
	        $sexo = $reg_animal->tbl_animal_pasto_sexo;

		    $data_acompanhamento_calculo = date("Y-m-d");
		    $date = new DateTime($reg_animal->tbl_animal_pasto_nascimento);
		    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
		    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
		    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
		    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

		    $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		            WHERE tab_registro_lixeira_categoria_idade='0'");
		    $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

		    while ($reg_categoria_pasto = mysqli_fetch_object($tbl_categoria_pasto)) {
		        $idade_de = $reg_categoria_pasto->tab_categoria_idade_de;
		        $idade_ate = $reg_categoria_pasto->tab_categoria_idade_ate;

		        if ($idade >= $idade_de && $idade <= $idade_ate) {
		            $codigo_categoria = $reg_categoria_pasto->tab_codigo_categoria_idade;
		        }
		    }

	        if ($sexo=='M') {
	            $total_cat_macho[$codigo_categoria]++;
	        }
	        else {
	            $total_cat_femea[$codigo_categoria]++;
	        }
	    }
	}
    // Fim pegar quantos animais existem na fazenda por categoria

	if (isset($_POST['pasto'])) {
		$pasto = $_POST['pasto'];
	}
	else {
		$pasto = 0;
	}

	if (empty($total_pesados)) {
		$total_pesados = 0;
	}

	$array_itens = $_POST['array_itens_pesagem_lote'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

	$data_sistema = date("Y-m-d H:i:s");

	/*if ($numero_pesagem_id && $tipo_gravacao==2 && $total_pesados==0) {
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
	}*/

	if ($numero_pesagem_id && $tipo_gravacao==2) {

	    $sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_codigo_local='$loca_pesagem',
			tbl_pesagem_codigo_epoca='$epoca_pesagem',
			tbl_pesagem_lote='$descricao_lote',
			tbl_pesagem_qtd_animais_a_pesar='$total_a_pesar',
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


		// GRAVAR A MEDIA POR CATEGORIA 

		// DEVERA SER INCLUIDO NO FINALIZAR PESAGEM

/*		$sql = ("DELETE FROM tbl_peso_medio_categoria 
			WHERE tbl_pm_id ='$numero_pesagem_id'");
		$resultado = mysqli_query($conector,$sql);

	    for ($i=1; $i <=5 ; $i++) { 
        	$categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

	        $qtd_animais_macho[$categoria] = 0;
    	    $peso_animais_macho[$categoria] = 0;
        	$qtd_animais_femea[$categoria] = 0;
        	$peso_animais_femea[$categoria] = 0;
        }

        $item_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
			WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id'");
		
		$num_rows = mysqli_num_rows($item_pesagem);

		if ($num_rows!=0){
		    while ($reg_item = mysqli_fetch_object($item_pesagem)){
		    	$categoria = $reg_item->tbl_ite_pesagem_categoria;
		    	$peso = $reg_item->tbl_ite_pesagem_peso;
		    	$sexo = $reg_item->tbl_ite_pesagem_sexo;
		    	$qtd_animais = $reg_item->tbl_ite_pesagem_qtd_animais;

		    	if ($sexo=='M') {
		    		$qtd_animais_macho[$categoria]+=$qtd_animais;
		    		$peso_animais_macho[$categoria]+=$peso;
		    	}
		    	else {
		    		$qtd_animais_femea[$categoria]+=$qtd_animais;
		    		$peso_animais_femea[$categoria]+=$peso;
		    	}
		    }
		}

	    for ($i=1; $i <=5 ; $i++) { 
        	$categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

        	if ($peso_animais_macho[$categoria] !=0 && 
        		$qtd_animais_macho[$categoria] !=0) {

        		// Verifica se ficou animal Macho da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria
        		if ($qtd_animais_macho[$categoria] < $total_cat_macho[$categoria]) {
        			$qtd_sem_pesar = $total_cat_macho[$categoria] - 
        			                 $qtd_animais_macho[$categoria];

        		    // pega o peso medio da pesagem anterior 
			        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
						WHERE tbl_pm_local_id='$loca_pesagem' AND 
						      tbl_pm_categoria_id='$categoria' AND 
						      tbl_pm_sexo='M'
						ORDER BY tbl_pm_id DESC LIMIT 1");

					$num_rows_media = mysqli_num_rows($tbl_media);

					if ($num_rows_media!=0){
						$reg_media = mysqli_fetch_object($tbl_media);
						$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
						$peso_total_anterior = $peso_medio_anterior * $qtd_sem_pesar;
					}
					else {
						$qtd_sem_pesar=0;
						$peso_total_anterior=0;
					}
					// fim pega o peso medio da pesagem anterior
        		}
        		else {
        			$qtd_sem_pesar=0;
					$peso_total_anterior=0;
        		}
        		// Fim verifica se ficou animal Macho da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria

	        	$media = ($peso_animais_macho[$categoria] + $peso_total_anterior) / 
	        	         ($qtd_animais_macho[$categoria] + $qtd_sem_pesar);

	        	$total_animais_gravar = $qtd_animais_macho[$categoria] + $qtd_sem_pesar;
	        	$peso_total_gravar = $peso_animais_macho[$categoria] + $peso_total_anterior;

			    $sql = "INSERT INTO tbl_peso_medio_categoria (
					tbl_pm_local_id,
					tbl_pm_peso_medio_atual,
					tbl_pm_categoria_id,
					tbl_pm_sexo,
					tbl_pm_data,
					tbl_pm_qtd_total_atual,
					tbl_pm_peso_total_atual
			        ) VALUES (
			        '$loca_pesagem',
					'$media',
					'$categoria',
					'M',
					'$data_pesagem',
					'$total_animais_gravar',
					'$peso_total_gravar'
				)";

			    $resultado = mysqli_query($conector,$sql);
        	}
        	else {
	        	// pega o peso medio da pesagem anterior se não houve pesagem da categoria
				$tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
					WHERE tbl_pm_local_id='$loca_pesagem' AND 
					      tbl_pm_categoria_id='$categoria' AND 
					      tbl_pm_sexo='M' AND 
					      tbl_pm_qtd_total_atual!=0
					ORDER BY tbl_pm_id DESC LIMIT 1");

				$num_rows_media = mysqli_num_rows($tbl_media);

				if ($num_rows_media!=0){
					$reg_media = mysqli_fetch_object($tbl_media);
					$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
					$peso_total_anterior = $reg_media->tbl_pm_peso_total_atual;
					$total_animais_anterior = $reg_media->tbl_pm_qtd_total_atual;
				}
				else {
					$peso_medio_anterior=0;
					$peso_total_anterior=0;
					$total_animais_anterior=0;
				}
				// fim pega o peso medio da pesagem anterior

				$sql = "INSERT INTO tbl_peso_medio_categoria (
						tbl_pm_local_id,
						tbl_pm_peso_medio_atual,
						tbl_pm_categoria_id,
						tbl_pm_sexo,
						tbl_pm_data,
						tbl_pm_qtd_total_atual,
						tbl_pm_peso_total_atual
				        ) VALUES (
				        '$loca_pesagem',
						'$peso_medio_anterior',
						'$categoria',
						'M',
						'$data_pesagem',
						'$total_animais_anterior',
						'$peso_total_anterior'
					)";

				$resultado = mysqli_query($conector,$sql);
        	}

        	if ($peso_animais_femea[$categoria] !=0 && 
        		$qtd_animais_femea[$categoria] !=0) {

        		// Verifica se ficou animal Femea da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria
        		if ($qtd_animais_femea[$categoria] < $total_cat_femea[$categoria]) {
        			$qtd_sem_pesar = $total_cat_femea[$categoria] - 
        			                 $qtd_animais_femea[$categoria];

        		    // pega o peso medio da pesagem anterior 
			        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
						WHERE tbl_pm_local_id='$loca_pesagem' AND 
						      tbl_pm_categoria_id='$categoria' AND 
						      tbl_pm_sexo='F'
						ORDER BY tbl_pm_id DESC LIMIT 1");

					$num_rows_media = mysqli_num_rows($tbl_media);

					if ($num_rows_media!=0){
						$reg_media = mysqli_fetch_object($tbl_media);
						$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
						$peso_total_anterior = $peso_medio_anterior * $qtd_sem_pesar;
					}
					else {
						$qtd_sem_pesar=0;
						$peso_total_anterior=0;
					}
					// fim pega o peso medio da pesagem anterior
        		}
        		else {
        			$qtd_sem_pesar=0;
					$peso_total_anterior=0;
        		}
        		// Fim verifica se ficou animal Femea da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria

	        	$media = ($peso_animais_femea[$categoria] + $peso_total_anterior) / 
	        	         ($qtd_animais_femea[$categoria] + $qtd_sem_pesar);

	        	$total_animais_gravar = $qtd_animais_femea[$categoria] + $qtd_sem_pesar;
	        	$peso_total_gravar = $peso_animais_femea[$categoria] + $peso_total_anterior;

			    $sql = "INSERT INTO tbl_peso_medio_categoria (
					tbl_pm_local_id,
					tbl_pm_peso_medio_atual,
					tbl_pm_categoria_id,
					tbl_pm_sexo,
					tbl_pm_data,
					tbl_pm_qtd_total_atual,
					tbl_pm_peso_total_atual

			        ) VALUES (
			        '$loca_pesagem',
					'$media',
					'$categoria',
					'F',
					'$data_pesagem',
					'$total_animais_gravar',
					'$peso_total_gravar'
				)";

			    $resultado = mysqli_query($conector,$sql);
        	}
        	else {
	        	// pega o peso medio da pesagem anterior se não houve pesagem da categoria
				$tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
					WHERE tbl_pm_local_id='$loca_pesagem' AND 
					      tbl_pm_categoria_id='$categoria' AND 
					      tbl_pm_sexo='F' AND 
					      tbl_pm_qtd_total_atual!=0
					ORDER BY tbl_pm_id DESC LIMIT 1");

				$num_rows_media = mysqli_num_rows($tbl_media);

				if ($num_rows_media!=0){
					$reg_media = mysqli_fetch_object($tbl_media);
					$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
					$peso_total_anterior = $reg_media->tbl_pm_peso_total_atual;
					$total_animais_anterior = $reg_media->tbl_pm_qtd_total_atual;
				}
				else {
					$peso_medio_anterior=0;
					$peso_total_anterior=0;
					$total_animais_anterior=0;
				}
				// fim pega o peso medio da pesagem anterior

				$sql = "INSERT INTO tbl_peso_medio_categoria (
						tbl_pm_local_id,
						tbl_pm_peso_medio_atual,
						tbl_pm_categoria_id,
						tbl_pm_sexo,
						tbl_pm_data,
						tbl_pm_qtd_total_atual,
						tbl_pm_peso_total_atual
				        ) VALUES (
				        '$loca_pesagem',
						'$peso_medio_anterior',
						'$categoria',
						'F',
						'$data_pesagem',
						'$total_animais_anterior',
						'$peso_total_anterior'
					)";

				$resultado = mysqli_query($conector,$sql);
        	}
        }
		*/
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
			tbl_pesagem_qtd_animais_a_pesar,
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
			tbl_pesagem_tipo_registro,
			tbl_pesagem_origem
	        ) VALUES (
	        'L',
	        '$data_pesagem',
			'$loca_pesagem',
			'$epoca_pesagem',
			'$descricao_lote',
			'$total_a_pesar',
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
			0,
			'ONLINE'
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

			if (!$grupo_destino=='') {
				$grupo_destino=0;
			}

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

?>