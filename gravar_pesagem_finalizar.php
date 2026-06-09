<?php 
	// Finaliza as pesagens na edição da lista excel e altera os dados da tabela tbl_peso_medio_categoria quanto for controle de estoque por lote

	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
    $controle_estoque= $_SESSION['controle_estoque'];
	$data_sistema = date("Y-m-d H:i:s");
	$numero_pesagem_id= $_POST['numero_pesagem_id'];
	$data_pesagem= $_POST['data_pesagem'];
	$finalizar_pesagem= $_POST['finalizar_pesagem'];
	//$excluir_id= $_POST['excluir_id'];

	// Pega o local e o tipo do registro (online ou offline)
	$tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_pesagem
		WHERE tbl_pesagem_id='$numero_pesagem_id'");
			
	$num_rows = mysqli_num_rows($tbl_pesagem);

	$reg_pesagem = mysqli_fetch_object($tbl_pesagem);
   	$local = $reg_pesagem->tbl_pesagem_codigo_local;
   	$tipo_registro = $reg_pesagem->tbl_pesagem_tipo_registro;
   	$epoca_pesagem = $reg_pesagem->tbl_pesagem_codigo_epoca;
   	// Fim pega local e tipo de registeo

   	if ($tipo_registro=='OFFLINE') {
		$descricao_lote = $_POST['lote'];
		$epoca_pesagem = $_POST['epoca_pesagem'];
   	}
   	else {
		$descricao_lote = $_POST['descricao_lote'];
		$epoca_pesagem = $_POST['epoca_pesagem'];
   	}

    // Pegar quantos animais existem na fazenda por categoria para controle de estoque por Lote
   	if ($controle_estoque=='L') {
	    for ($i = 1; $i <=5; $i++) {
	        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
	        $total_cat_macho[$j]=0;
	        $total_cat_femea[$j]=0;
	    }

	    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	        WHERE tbl_animal_pasto_situacao='A' AND
	              tbl_animal_pasto_local='$local'"); 

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
   	}

	if ($finalizar_pesagem=='S') {
		$sql = ("DELETE FROM tbl_item_pesagem
			WHERE tbl_ite_pesagem_numero_id = '$numero_pesagem_id' AND 
			      tbl_ite_pesagem_peso = 0");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir o item.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		if ($controle_estoque=='L') {
			// GRAVAR A MEDIA POR CATEGORIA

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
	        		if ($qtd_animais_macho[$categoria] <= $total_cat_macho[$categoria]) {
	        			$qtd_sem_pesar = $total_cat_macho[$categoria] - 
	        			                 $qtd_animais_macho[$categoria];

	        		    // pega o peso medio da pesagem anterior 
				        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
							WHERE tbl_pm_local_id='$local' AND 
							      tbl_pm_categoria_id='$categoria' AND 
							      tbl_pm_sexo='M'");

						$num_rows_media = mysqli_num_rows($tbl_media);

						if ($num_rows_media!=0){
							$reg_media = mysqli_fetch_object($tbl_media);
			        		$id_media = $reg_media->tbl_pm_id;
							$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
							$peso_total_anterior = $peso_medio_anterior * $qtd_sem_pesar;

				        	$media_atual = ($peso_animais_macho[$categoria] + 
				        					$peso_total_anterior) / 
				        	         	   ($qtd_animais_macho[$categoria] + 
				        	         	   	$qtd_sem_pesar);

				        	$peso_total_atual = $peso_animais_macho[$categoria] + $peso_total_anterior;

					        $sql = ("UPDATE tbl_peso_medio_categoria  SET 
					                	tbl_pm_peso_medio_atual='$media_atual',
					                	tbl_pm_peso_total_atual='$peso_total_atual'
					    		    WHERE tbl_pm_id ='$id_media'");
	        			    $resultado = mysqli_query($conector,$sql);
						}
					}
	        		// Fim verifica se ficou animal Macho da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria
	        	}

	        	if ($peso_animais_femea[$categoria] !=0 && 
	        		$qtd_animais_femea[$categoria] !=0) {

	        		// Verifica se ficou animal Femea da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria
	        		if ($qtd_animais_femea[$categoria] <= $total_cat_femea[$categoria]) {
	        			$qtd_sem_pesar = $total_cat_femea[$categoria] - 
	        			                 $qtd_animais_femea[$categoria];

	        		    // pega o peso medio da pesagem anterior 
				        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
							WHERE tbl_pm_local_id='$local' AND 
							      tbl_pm_categoria_id='$categoria' AND 
							      tbl_pm_sexo='F'");

						$num_rows_media = mysqli_num_rows($tbl_media);

						if ($num_rows_media!=0){
							$reg_media = mysqli_fetch_object($tbl_media);
			        		$id_media = $reg_media->tbl_pm_id;
							$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
							$peso_total_anterior = $peso_medio_anterior * $qtd_sem_pesar;

				        	$media_atual = ($peso_animais_femea[$categoria] + 
				        					$peso_total_anterior) / 
				        	         	   ($qtd_animais_femea[$categoria] + 
				        	         	   	$qtd_sem_pesar);

				        	$peso_total_atual = $peso_animais_femea[$categoria] + $peso_total_anterior;

					        $sql = ("UPDATE tbl_peso_medio_categoria  SET 
					                	tbl_pm_peso_medio_atual='$media_atual',
					                	tbl_pm_peso_total_atual='$peso_total_atual'
					    		    WHERE tbl_pm_id ='$id_media'");
	        			    $resultado = mysqli_query($conector,$sql);
						}
					}
				}
	        }
		}

		$sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_data='$data_pesagem',
		  	tbl_pesagem_finalizada='$finalizar_pesagem',
		  	tbl_pesagem_codigo_movimentacao=0
		WHERE tbl_pesagem_id='$numero_pesagem_id'";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a pesagem.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
	}

   	if ($tipo_registro=='OFFLINE') {
		$qtd_a_pesar= $_POST['qtd_a_pesar'];
		$qtd_pesado= $_POST['qtd_pesado'];
   	}
   	else {
		$qtd_a_pesar= $_POST['total_a_pesar'];
		$qtd_pesado= $_POST['total_pesados'];
   	}

	$peso_total_kg= $_POST['peso_total_kg'];
	$peso_total_arroba= $_POST['peso_total_arroba'];
	$peso_medio_kg= $_POST['peso_medio_kg'];
	$peso_medio_arroba= $_POST['peso_medio_arroba'];

	$sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_data='$data_pesagem',
	  		tbl_pesagem_lote='$descricao_lote',
	  		tbl_pesagem_codigo_epoca='$epoca_pesagem',
	  		tbl_pesagem_qtd_animais_a_pesar='$qtd_a_pesar',
			tbl_pesagem_qtd_animais_pesados='$qtd_pesado',
			tbl_pesagem_peso_kg='$peso_total_kg',
			tbl_pesagem_peso_arroba='$peso_total_arroba',
			tbl_pesagem_peso_medio_kg='$peso_medio_kg',
			tbl_pesagem_peso_medio_arroba='$peso_medio_arroba',
			tbl_pesagem_alterado_em='$data_sistema',
			tbl_pesagem_alterado_por='$nomeusuario',
			tbl_pesagem_codigo_movimentacao=0
	WHERE tbl_pesagem_id='$numero_pesagem_id'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a pesagem.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	if ($controle_estoque=='I') {
	    $item_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
	        WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id'");
		$num_rows_itens = mysqli_num_rows($item_pesagem);

		if ($num_rows_itens==0) {
			$sql = "DELETE FROM tbl_pesagem
				WHERE tbl_pesagem_id='$numero_pesagem_id'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir a pesagem sem itens.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			} 
		}
		else {
			while ($reg_item = mysqli_fetch_object($item_pesagem)){
			  	$id_animal = $reg_item->tbl_ite_pesagem_codigo_id_animal;
			   	$peso = $reg_item->tbl_ite_pesagem_peso;

		        if ($epoca_pesagem==1) {
				    $sql = "UPDATE tbl_animais SET
						tbl_animal_primeiro_peso='$peso',
						tbl_animal_lote_primeiro_peso='$descricao_lote',
						tbl_animal_data_primeiro_peso='$data_pesagem',
						tbl_animal_ultimo_peso='$peso',
						tbl_animal_lote_ultimo='$descricao_lote',
						tbl_animal_data_ultimo='$data_pesagem'
				    WHERE tbl_animal_codigo_id='$id_animal'";
		        }
		        else if ($epoca_pesagem==2 || $epoca_pesagem==8) {
				    $sql = "UPDATE tbl_animais SET
						tbl_animal_peso_desmama='$peso',
						tbl_animal_lote_desmama='$descricao_lote',
						tbl_animal_data_desmama='$data_pesagem',
						tbl_animal_ultimo_peso='$peso',
						tbl_animal_lote_ultimo='$descricao_lote',
						tbl_animal_data_ultimo='$data_pesagem'
				    WHERE tbl_animal_codigo_id='$id_animal'";
		        }
		        else {
				    $sql = "UPDATE tbl_animais SET
						tbl_animal_ultimo_peso='$peso',
						tbl_animal_lote_ultimo='$descricao_lote',
						tbl_animal_data_ultimo='$data_pesagem'
				    WHERE tbl_animal_codigo_id='$id_animal'";
		        }

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
			   		header('Content-type: application/json');
			   		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar o peso no cadastro de animais.' . $erro_mysql));
					mysqli_close($conector);
					exit;
				} 
			}
		}
	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Pesagem finalizada com suscesso'));
	mysqli_close($conector);
	exit;

/*	$codigo_id= $_POST['codig_id'];
	$item_id= $_POST['item_id'];
	$peso= $_POST['peso_id'];
	$observacao = $_POST['obs_id'];

	$peso_arroba = $peso/30;

	if ($excluir_id=="S") {
		$sql = ("DELETE FROM tbl_item_pesagem
			WHERE tbl_ite_pesagem_numero_id = '$numero_pesagem_id' AND 
			      tbl_ite_pesagem_numero_item = '$item_id' AND 
			      tbl_ite_pesagem_codigo_id_animal = '$codigo_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao excluir o item.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode(array('success' => true, 'message' => 'Item excluir com suscesso'));
			mysqli_close($conector);
			exit;
		}
	}

	if ($controle_estoque=='L') {
		$grupo = $_POST['grupo_id'];

		if (!$grupo) {
			$grupo=0;
		}
	}

	if ($controle_estoque=='L') {
		$sql = "UPDATE tbl_item_pesagem SET
			tbl_ite_pesagem_data_emissao='$data_pesagem',
			tbl_ite_pesagem_peso='$peso',
			tbl_ite_pesagem_grupo_pasto_destino='$grupo',
			tbl_ite_pesagem_peso_medio='$peso',
			tbl_ite_pesagem_arroba='$peso_arroba',
			tbl_ite_pesagem_arroba_media='$peso_arroba',
			tbl_ite_pesagem_observacao='$observacao'
		WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id' AND tbl_ite_pesagem_numero_item='$item_id'";
	}
	else {
		$sql = "UPDATE tbl_item_pesagem SET
			tbl_ite_pesagem_data_emissao='$data_pesagem',
			tbl_ite_pesagem_peso='$peso',
			tbl_ite_pesagem_peso_medio='$peso',
			tbl_ite_pesagem_arroba='$peso_arroba',
			tbl_ite_pesagem_arroba_media='$peso_arroba',
			tbl_ite_pesagem_observacao='$observacao'
		WHERE tbl_ite_pesagem_numero_id='$numero_pesagem_id' AND tbl_ite_pesagem_numero_item='$item_id'";
	}

	$resultado = mysqli_query($conector,$sql);
    $erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do item da pesagem.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

    if ($peso!=0 && $controle_estoque=='I') {
        if ($epoca_pesagem==1) {
		    $sql = "UPDATE tbl_animais SET
				tbl_animal_primeiro_peso='$peso',
				tbl_animal_lote_primeiro_peso='$descricao_lote',
				tbl_animal_data_primeiro_peso='$data_pesagem'
		    WHERE tbl_animal_codigo_id='$codigo_id'";
    		$resultado = mysqli_query($conector,$sql);
        }
        else if ($epoca_pesagem==2 || $epoca_pesagem==8) {
		    $sql = "UPDATE tbl_animais SET
				tbl_animal_peso_desmama='$peso',
				tbl_animal_lote_desmama='$descricao_lote',
				tbl_animal_data_desmama='$data_pesagem'
		    WHERE tbl_animal_codigo_id='$codigo_id'";
		    $resultado = mysqli_query($conector,$sql);
        }

        // alterado em 17/05/2023 toda pesagem tem que gravar no ultimo peso
        // reunião pelo telefone nessa data por volta da 10:00

	    $sql = "UPDATE tbl_animais SET
			tbl_animal_ultimo_peso='$peso',
			tbl_animal_lote_ultimo='$descricao_lote',
			tbl_animal_data_ultimo='$data_pesagem'
	    WHERE tbl_animal_codigo_id='$codigo_id'";
	    $resultado = mysqli_query($conector,$sql);

	    $erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
    }
*/
	/*if ($controle_estoque=='L') {
		// GRAVAR A MEDIA POR CATEGORIA

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
						WHERE tbl_pm_local_id='$local' AND 
						      tbl_pm_categoria_id='$categoria' AND 
						      tbl_pm_sexo='M'");

					$num_rows_media = mysqli_num_rows($tbl_media);

					if ($num_rows_media!=0){
						$reg_media = mysqli_fetch_object($tbl_media);
		        		$id_media = $reg_media->tbl_pm_id;
						$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
						$peso_total_anterior = $peso_medio_anterior * $qtd_sem_pesar;

			        	$media_atual = ($peso_animais_macho[$categoria] + 
			        					$peso_total_anterior) / 
			        	         	   ($qtd_animais_macho[$categoria] + 
			        	         	   	$qtd_sem_pesar);

			        	$peso_total_atual = $peso_animais_macho[$categoria] + $peso_total_anterior;

				        $sql = ("UPDATE tbl_peso_medio_categoria  SET 
				                	tbl_pm_peso_medio_atual='$media_atual',
				                	tbl_pm_peso_total_atual='$peso_total_atual'
				    		    WHERE tbl_pm_id ='$id_media'");
        			    $resultado = mysqli_query($conector,$sql);
					}
				}
        		// Fim verifica se ficou animal Macho da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria
        	}

        	if ($peso_animais_femea[$categoria] !=0 && 
        		$qtd_animais_femea[$categoria] !=0) {

        		// Verifica se ficou animal Femea da categoria sem pesar e pega media anterior para o calculo correto da media atual por categoria
        		if ($qtd_animais_femea[$categoria] < $total_cat_femea[$categoria]) {
        			$qtd_sem_pesar = $total_cat_femea[$categoria] - 
        			                 $qtd_animais_femea[$categoria];

        		    // pega o peso medio da pesagem anterior 
			        $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
						WHERE tbl_pm_local_id='$local' AND 
						      tbl_pm_categoria_id='$categoria' AND 
						      tbl_pm_sexo='F'");

					$num_rows_media = mysqli_num_rows($tbl_media);

					if ($num_rows_media!=0){
						$reg_media = mysqli_fetch_object($tbl_media);
		        		$id_media = $reg_media->tbl_pm_id;
						$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
						$peso_total_anterior = $peso_medio_anterior * $qtd_sem_pesar;

			        	$media_atual = ($peso_animais_femea[$categoria] + 
			        					$peso_total_anterior) / 
			        	         	   ($qtd_animais_femea[$categoria] + 
			        	         	   	$qtd_sem_pesar);

			        	$peso_total_atual = $peso_animais_femea[$categoria] + $peso_total_anterior;

				        $sql = ("UPDATE tbl_peso_medio_categoria  SET 
				                	tbl_pm_peso_medio_atual='$media_atual',
				                	tbl_pm_peso_total_atual='$peso_total_atual'
				    		    WHERE tbl_pm_id ='$id_media'");
        			    $resultado = mysqli_query($conector,$sql);
					}
				}
			}
        }
	}*/

	mysqli_close($conector);
	exit;

?>