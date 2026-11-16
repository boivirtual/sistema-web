<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$controle_estoque= $_SESSION['controle_estoque'];
	$nome_usuario = $_SESSION['nome_usuario'];

	$local_origem= $_POST['local_morte'];
	$data_movimentacao= $_POST['data_morte_animal'];
	$movimentacao_finalizada='N';
    $codigo_tipo = 888;
	$tipo_gravacao=1;	
	$data_sistema = date("Y-m-d H:i:s");

	$array_itens = $_POST['array_itens'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

	$total_digitados = 1;
	$peso_total_kg = 0.00;
	$peso_total_arroba = 0.00;
	$peso_medio_kg = 0.00;
	$peso_medio_arroba = 0.00;
	$data_nascimento = '0000-00-00';

	if ($controle_estoque=='I') {

		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];
    		$itens = explode("|", $tabela_itens);

			$sexo = $itens[2];
			$data_nascimento = $itens[3];
			$codigo_id = $itens[8];
			$codigo_pasto = $itens[11];
			$codigo_categoria = $itens[12];
			$desc_categoria='';

			$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
	              WHERE tbl_animal_codigo_id='$codigo_id'");

	    	$num_rows = mysqli_num_rows($rs);
	    	if ($num_rows!=0) {
	        	$reg_animal = mysqli_fetch_object($rs);
	            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
                $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
                $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

                if ($ultimo_peso!=0 && $ultimo_peso!='') {
                    $peso = $ultimo_peso;
                }
                else if ($peso_desmama!=0 && $peso_desmama!='') {
                    $peso = $peso_desmama;
                }
                else if ($primeiro_peso!=0 && $primeiro_peso!=''){
                    $peso = $primeiro_peso;
                }
                else {
                    $peso = 0;
                }
	        }

			switch ($codigo_categoria) {
			    case '001':
			        $desc_categoria= '00 a 07 meses';
			        break;
			    case '002':
			        $desc_categoria= '08 a 12 meses';
			        break;
			    case '003':
			        $desc_categoria= '13 a 24 meses';
			        break;
			    case '004':
			        $desc_categoria= '25 a 36 meses';
			        break;
			    case '005':
			        $desc_categoria= '> 36 meses';
			        break;
			}

			// O codigo da categoria foi substituido pela data da nascimento em 20/08/2024 por conta do ajuste "AJUSTAR AS SAIDAS DE ANIMAIS DO PASTO POR ID" para controle de estoque por ID
			$data = str_replace("/", "-", $data_nascimento);
   			$data = date('Y-m-d', strtotime($data));

			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$local_origem' AND 
				      tbl_animal_pasto_id = '$codigo_pasto' AND 
				      tbl_animal_pasto_sexo = '$sexo' AND 
				      tbl_animal_pasto_nascimento = '$data'");
		    $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

		    if ($num_rows_pasto==0) { 
		    	// Vai procurar um registro com a data de nascimento igual a data de nascimento do animal e substitui-la para depois baixar o animal do pasto

		       	// procurar um registro por categoria com outra data de nascimento qualquer.
				$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
					WHERE tbl_animal_pasto_local = '$local_origem' AND 
					      tbl_animal_pasto_id = '$codigo_pasto' AND 
					      tbl_animal_pasto_sexo = '$sexo' AND 
					      tbl_animal_pasto_categoria = '$codigo_categoria'
					ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
			    $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

				if ($num_rows_pasto==0) {	
				 	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', categoria ' .$desc_categoria. ' no pasto.'));
					exit;
				}
				else {
		       		$reg_pasto_atual = mysqli_fetch_object($tbl_pasto);
		       		$data_nascimento_atual = $reg_pasto_atual->tbl_animal_pasto_nascimento;
		       		$numero_item_atual = $reg_pasto_atual->tbl_animal_pasto_numero_item;

					// procura um outro animal com a mesma data de nascimento e sexo em outros pastos

					$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
						WHERE tbl_animal_pasto_local = '$local_origem' AND 
						      tbl_animal_pasto_sexo = '$sexo' AND 
						      tbl_animal_pasto_nascimento = '$data'
						ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
			        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

			        if ($num_rows_pasto==0) {
					 	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', categoria ' .$desc_categoria. ', nascimento '.$data.' em outros pastos.'));
						exit;
			        }
			        else {
			       		$reg_pasto_trocar = mysqli_fetch_object($tbl_pasto);
			       		$data_nascimento_trocar = $reg_pasto_trocar->tbl_animal_pasto_nascimento;
			       		$numero_item_trocar = $reg_pasto_trocar->tbl_animal_pasto_numero_item;
			       		$pasto_trocar = $reg_pasto_trocar->tbl_animal_pasto_id;

			       		//Salva o pasto atual com a nova data de nascimento
						$sql = "UPDATE tbl_animal_pasto SET
							tbl_animal_pasto_nascimento='$data_nascimento_trocar'
						    WHERE tbl_animal_pasto_local = '$local_origem' AND 
						    	  tbl_animal_pasto_numero_item = '$numero_item_atual' AND 
						    	  tbl_animal_pasto_id = '$codigo_pasto'";

						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
						   	header('Content-type: application/json');
						   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto atual ' . $erro_mysql));
						   	exit;
						} 

			       		//Salva o pasto trocar com a data de nascimento anterior
						$sql = "UPDATE tbl_animal_pasto SET
								tbl_animal_pasto_nascimento='$data_nascimento_atual'
						    WHERE tbl_animal_pasto_local = '$local_origem' AND 
						    	  tbl_animal_pasto_numero_item = '$numero_item_trocar' AND 
						    	  tbl_animal_pasto_id = '$pasto_trocar'";

						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
						   	header('Content-type: application/json');
						   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto trocar ' . $erro_mysql));
						   	exit;
						} 
			        }
				}

				// Pega novamente o registro do animal no pasto, agora com a data de nascimento já correta

				$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
					WHERE tbl_animal_pasto_local = '$local_origem' AND 
					      tbl_animal_pasto_id = '$codigo_pasto' AND 
					      tbl_animal_pasto_sexo = '$sexo' AND 
					      tbl_animal_pasto_nascimento = '$data'");
			    $num_rows_pasto = mysqli_num_rows($tbl_pasto);    
		    }

			$reg_pasto = mysqli_fetch_object($tbl_pasto);
			$numero_item = $reg_pasto->tbl_animal_pasto_numero_item;

			$sql = ("DELETE FROM tbl_animal_pasto 
					WHERE tbl_animal_pasto_local ='$local_origem' AND 
						tbl_animal_pasto_numero_item='$numero_item'");

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			 	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Erro na alteração do registro no pasto.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

			// Subtrai categoria no fechamento mensal se a data for do mes anterior

			$data_hoje = date("Y-m-d");
			$partes_hoje = explode("-", $data_hoje);
			$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

			$partes_movimentacao = explode("-", $data_movimentacao);
			$anomes_final = $partes_movimentacao[0].$partes_movimentacao[1];
			$diferenca = $anomes_inicial - $anomes_final;

			if ($diferenca!=0) {
				$date = new DateTime($data_movimentacao);
				$date->modify('last day of this month');
				$data_fechamento = $date->format('Y-m-d');

				$tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
		       		WHERE tbl_fechamento_local='$local_origem' AND
		           		  tbl_fechamento_data='$data_fechamento' AND 
		           		  tbl_fechamento_categoria='$codigo_categoria' AND
		           		  tbl_fechamento_sexo='$sexo'");

		    	$num_rows = mysqli_num_rows($tbl_fechamento);    

		    	if ($num_rows!=0) {
		    		$reg = mysqli_fetch_object($tbl_fechamento);
		    		$fechamento_id = $reg->tbl_fechamento_id;
		    		$qtd_fechamento = $reg->tbl_fechamento_qtd;
    				$peso_fechamento = $reg->tbl_fechamento_peso;

		    		$qtd_fechamento--;
    				$peso_fechamento-=$peso;

					$sql = ("UPDATE tbl_fechamento_mensal_estoque SET 
					   		tbl_fechamento_qtd='$qtd_fechamento',
				   			tbl_fechamento_peso='$peso_fechamento'
					 	WHERE tbl_fechamento_id ='$fechamento_id'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					 	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal!' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}
		    	}

				$tbl_fechamento_ent_sai = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
		        	WHERE tbl_fechamento_local='$local_origem' AND
		           		  tbl_fechamento_data='$data_fechamento'");

		    	$num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

		    	if ($num_rows!=0) {
		    		$reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
		    		$fechamento_id = $reg->tbl_fechamento_id;
		    		$peso_morte = $reg->tbl_fechamento_peso_sai_morte;
		    		$peso_final = $reg->tbl_fechamento_peso_final;

		    		$peso_morte+=$peso;
				    $peso_final-=$peso;

					$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
					   		tbl_fechamento_peso_sai_morte='$peso_morte',
					   		tbl_fechamento_peso_final='$peso_final'
					 	WHERE tbl_fechamento_id ='$fechamento_id'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						header('Content-type: application/json');
						echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal Ent/Sai' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}
		    	}
			} 	// Fim adiciona fechamento mensal
		}
	}
	else {
		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];
    		$itens = explode("|", $tabela_itens);

			$sexo = $itens[2];
			$codigo_pasto = $itens[11];
			$codigo_categoria = $itens[12];
			$desc_categoria='';

			switch ($codigo_categoria) {
			    case '001':
			        $desc_categoria= '00 a 07 meses';
			        break;
			    case '002':
			        $desc_categoria= '08 a 12 meses';
			        break;
			    case '003':
			        $desc_categoria= '13 a 24 meses';
			        break;
			    case '004':
			        $desc_categoria= '25 a 36 meses';
			        break;
			    case '005':
			        $desc_categoria= '> 36 meses';
			        break;
			}

			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$local_origem' AND 
				      tbl_animal_pasto_id ='$codigo_pasto' AND 
				      tbl_animal_pasto_sexo = '$sexo' AND 
				      tbl_animal_pasto_categoria = '$codigo_categoria'");

	        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

	        if ($num_rows_pasto!=0) {
				$reg_pasto = mysqli_fetch_object($tbl_pasto);
			    $numero_item = $reg_pasto->tbl_animal_pasto_numero_item;
	            $data_nascimento = $reg_pasto->tbl_animal_pasto_nascimento;

				$sql = ("DELETE FROM tbl_animal_pasto 
				         WHERE tbl_animal_pasto_local ='$local_origem' AND 
				               tbl_animal_pasto_numero_item='$numero_item'");
				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
				 	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Erro na alteração do registro no pasto.' . $erro_mysql));
					mysqli_close($conector);
					exit;
				}

				// Subtrai categoria no fechamento mensal se a data for do mes anterior

				$data_hoje = date("Y-m-d");
				$partes_hoje = explode("-", $data_hoje);
				$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

				$partes_movimentacao = explode("-", $data_movimentacao);
				$anomes_final = $partes_movimentacao[0].$partes_movimentacao[1];
				$diferenca = $anomes_inicial - $anomes_final;

				if ($diferenca!=0) {
					$date = new DateTime($data_movimentacao);
					$date->modify('last day of this month');
					$data_fechamento = $date->format('Y-m-d');

					$tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
		        		WHERE tbl_fechamento_local='$local_origem' AND
		              		  tbl_fechamento_data='$data_fechamento' AND 
		              		  tbl_fechamento_categoria='$codigo_categoria' AND
		              		  tbl_fechamento_sexo='$sexo'");

		    		$num_rows = mysqli_num_rows($tbl_fechamento);    

		    		if ($num_rows!=0) {
		    			$reg = mysqli_fetch_object($tbl_fechamento);
		    			$fechamento_id = $reg->tbl_fechamento_id;
		    			$qtd_fechamento = $reg->tbl_fechamento_qtd;
    					$peso_fechamento = $reg->tbl_fechamento_peso;

		    			$qtd_fechamento--;
    					$peso_fechamento-=$peso;

						$sql = ("UPDATE tbl_fechamento_mensal_estoque SET 
						   		tbl_fechamento_qtd='$qtd_fechamento',
				   				tbl_fechamento_peso='$peso_fechamento'
						 	WHERE tbl_fechamento_id ='$fechamento_id'");

						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado) {
						 	header('Content-type: application/json');
						   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal!' . $erro_mysql));
							mysqli_close($conector);
							exit;
						}
		    		}

					$tbl_fechamento_ent_sai = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
		        		WHERE tbl_fechamento_local='$local_origem' AND
		              		  tbl_fechamento_data='$data_fechamento'");

		    		$num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

		    		if ($num_rows!=0) {
		    			$reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
		    			$fechamento_id = $reg->tbl_fechamento_id;
		    			$peso_morte = $reg->tbl_fechamento_peso_sai_morte;
		    			$peso_final = $reg->tbl_fechamento_peso_final;

		    			$peso_morte+=$peso;
				    	$peso_final-=$peso;

						$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
						   		tbl_fechamento_peso_sai_morte='$peso_morte',
						   		tbl_fechamento_peso_final='$peso_final'
						 	WHERE tbl_fechamento_id ='$fechamento_id'");

						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado) {
					   		header('Content-type: application/json');
					   		echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal Ent/Sai' . $erro_mysql));
							mysqli_close($conector);
							exit;
						}
		    		}
				} 	// Fim adiciona fechamento mensal
			}
			else {
			 	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo '.$sexo.' e categoria '.$desc_categoria.' no pasto.'));
				mysqli_close($conector);
				exit; 
			}
		}
	}

	// Limpa Descrição do Lote caso o pasto fique vazio e atualiza quantida de dias com animal no pasto
	$tbl_animai_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
			WHERE tbl_animal_pasto_local = '$local_origem' AND 
			      tbl_animal_pasto_id ='$codigo_pasto'");

	$num_rows_animal_pasto = mysqli_num_rows($tbl_animai_pasto);    

	if ($num_rows_animal_pasto==0) {
		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
		    WHERE tbl_pasto_id = $codigo_pasto");

		$reg_pasto = mysqli_fetch_object($tbl_pasto);

		$data_com_remover = $reg_pasto->tbl_pasto_data_com_animais;
		$data_com_remover_anterior = $reg_pasto->tbl_pasto_data_com_animais_anterior;
		$data_sem_remover = $reg_pasto->tbl_pasto_data_sem_animais;
		$data_sem_remover_anterior = $reg_pasto->tbl_pasto_data_sem_animais_anterior;

	    $query = "UPDATE tbl_pasto SET 
	        tbl_pasto_descricao_lote = null,
	        tbl_pasto_id_lote = null, 
	        tbl_pasto_ano_lote = null,
	        tbl_pasto_descricao_lote_1 = null,
	        tbl_pasto_descricao_lote_2 = null,
	        tbl_pasto_descricao_lote_3 = null,
	        tbl_pasto_descricao_lote_4 = null,
	        tbl_pasto_descricao_lote_5 = null,
	        tbl_pasto_descricao_lote_6 = null,
	        tbl_pasto_alterado_em = '$data_sistema',
	        tbl_pasto_alterado_por = '$nome_usuario'
	        WHERE tbl_pasto_id = $codigo_pasto";

	    $resultado = mysqli_query($conector, $query);
	    $erro_mysql = mysqli_error($conector);

	    if (!$resultado){
	        header('Content-type: application/json');
	        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql));
	        exit;
	    }

		$dataAtual = new DateTime();
		$dataCom = new DateTime($data_com_remover);
		$diff = $dataAtual->diff($dataCom);

		if ($diff->h + ($diff->days * 24) < 24){
		    $query = "UPDATE tbl_pasto SET 
		            tbl_pasto_alterado_em = '$data_sistema',
		            tbl_pasto_alterado_por = '$nome_usuario',
		            tbl_pasto_data_com_animais = '$data_sem_remover_anterior',
		            tbl_pasto_data_com_animais_anterior = '$data_sem_remover_anterior',
		            tbl_pasto_data_sem_animais = '$data_sem_remover_anterior',
		            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover_anterior'
		        WHERE tbl_pasto_id = $codigo_pasto";

		    $resultado = mysqli_query($conector, $query);
		    $erro_mysql = mysqli_error($conector);

		    if (!$resultado){
		        header('Content-type: application/json');
		        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql));
		        exit;
		    } 
		}
		else {
		    $query = "UPDATE tbl_pasto SET 
		            tbl_pasto_alterado_em = '$data_sistema',
		            tbl_pasto_alterado_por = '$nome_usuario',
		            tbl_pasto_data_sem_animais = '$data_sistema',
		            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover'
		        WHERE tbl_pasto_id = $codigo_pasto";

		    $resultado = mysqli_query($conector, $query);
		    $erro_mysql = mysqli_error($conector);

		    if (!$resultado){
		        header('Content-type: application/json');
		        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
		        exit;
		    } 
		}

	} // Fim Atualiza Descrição do lote e dias com animal no pasto

    if ($tipo_gravacao==1){
	    $sql = "INSERT INTO tbl_movimentacao (
	    	tbl_movimentacao_controle,
	    	tbl_movimentacao_data,
			tbl_movimentacao_codigo_local_origem,
			tbl_movimentacao_codigo_local_destino,
			tbl_movimentacao_tipo,
			tbl_movimentacao_qtd_animais_pesados,
			tbl_movimentacao_peso_kg,
			tbl_movimentacao_peso_arroba,
			tbl_movimentacao_peso_medio_kg,
			tbl_movimentacao_peso_medio_arroba,
			tbl_movimentacao_filtros,
			tbl_movimentacao_situacao,
			tbl_movimentacao_incluido_em,
			tbl_movimentacao_incluido_por,
			tbl_movimentacao_alterado_em,
			tbl_movimentacao_alterado_por,
			tbl_movimentacao_lixeira,
			tbl_movimentacao_lixeira_em,
			tbl_movimentacao_lixeira_por,
			tbl_movimentacao_aceite_transferencia_em,
			tbl_movimentacao_aceite_transferencia_por,
			tbl_movimentacao_aceite_financeiro_em,
			tbl_movimentacao_aceite_financeiro_por,
			tbl_movimentacao_codigo_pesagem
	        ) VALUES (
	        '$controle_estoque',
	        '$data_movimentacao',
			'$local_origem',
			null,
			'$codigo_tipo',
			'$total_digitados',
			'$peso_total_kg',
			'$peso_total_arroba',
			'$peso_medio_kg',
			'$peso_medio_arroba',
			null,
			'$movimentacao_finalizada',
			'$data_sistema',
			'$nome_usuario',
			null,
			null,
			0,
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

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a movimentação'. $erro_mysql));
	    	mysqli_close($conector);
			exit;
		} 

		$numero_movimentacao = mysqli_insert_id($conector);
		$numero_movimentacao = str_pad($numero_movimentacao, 9, "0", STR_PAD_LEFT);

		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$codigo_animal = ltrim($itens[0]);
			$codigo_animal = rtrim($codigo_animal);
			$peso = $itens[1];
			$sexo = $itens[2];
			$nascimento = $itens[3];
			$raca = $itens[4];
			$pelagem = $itens[5];
			$mae = $itens[6];
			$observacao = ltrim($itens[7]);
			$observacao = rtrim($observacao);
			$codigo_id = $itens[8];
			$codigo_motivo_morte = $itens[10];
			$codigo_pasto = $itens[11];
			$codigo_categoria = $itens[12];

			$numero_item = $i + 1;
			
		    $sql = "INSERT INTO tbl_item_movimentacao (
		            tbl_ite_movimentacao_numero_id,
		            tbl_ite_movimentacao_numero_item,
		            tbl_ite_movimentacao_data_emissao,
		            tbl_ite_movimentacao_codigo_id_animal,
		            tbl_ite_movimentacao_codigo_animal,
					tbl_ite_movimentacao_peso,
					tbl_ite_movimentacao_sexo,
					tbl_ite_movimentacao_nascimento,
					tbl_ite_movimentacao_raca,
					tbl_ite_movimentacao_pelagem,
					tbl_ite_movimentacao_mae,
					tbl_ite_movimentacao_observacao,
					tbl_ite_movimentacao_motivo_morte,
					tbl_ite_movimentacao_codigo_pasto,
					tbl_ite_movimentacao_codigo_categoria
		        ) VALUES (
		            '$numero_movimentacao',
		            '$numero_item',
		            '$data_movimentacao',
		            '$codigo_id',
		            '$codigo_animal',
		            '$peso',
		            '$sexo',
		            '$nascimento',
		            '$raca',
		            '$pelagem',
		            '$mae',
		            '$observacao',
		            '$codigo_motivo_morte',
		            '$codigo_pasto',
		            '$codigo_categoria'
		    )";
		    $resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);
		}    

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
		
		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];
    		$itens = explode("|", $tabela_itens);
			$sexo=$itens[2];
			$observacao = ltrim($itens[7]);
			$observacao = rtrim($observacao);
			$codigo_id = $itens[8];
			$motivo_morte = $itens[9];
			$codigo_pasto = $itens[11];
			$codigo_categoria = $itens[12];

			$atualizar_animal = gravar_movimento_animal($conector, $codigo_id, $observacao, $codigo_tipo, $motivo_morte, $local_origem, $data_movimentacao, $numero_movimentacao, $codigo_pasto, $codigo_categoria, $sexo, $controle_estoque, $data_nascimento);

			if ($atualizar_animal=='Gravei') {
			    $resposta = array('success' => true, 'message' => 'Movimentação de morte processada com sucesso.');
			   	header('Content-type: application/json');
			   	echo json_encode($resposta);
				mysqli_close($conector);
				exit;
			}
			else {
			    $resposta = array('error' => true, 'message' => $atualizar_animal);
			   	header('Content-type: application/json');
			   	echo json_encode($resposta);
				mysqli_close($conector);
				exit;
			}
		}
	}

    function gravar_movimento_animal($conector, $codigo_id, $observacao, $codigo_tipo, $motivo_morte, $local_origem, $data_movimentacao, $numero_movimentacao, $codigo_pasto, $codigo_categoria, $sexo, $controle_estoque, $data_nascimento) {

		@ session_start(); 
		$nome_usuario = $_SESSION['nome_usuario'];
		$data_sistema = date("Y-m-d H:i:s");

		if ($controle_estoque=='I') {
			$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
	              WHERE tbl_animal_codigo_id='$codigo_id'");

	    	$num_rows = mysqli_num_rows($rs);
	    	if ($num_rows!=0) {
	        	$reg_animal = mysqli_fetch_object($rs);
	        	$codigo_origem_anterior = $reg_animal->tbl_animal_codigo_origem;
	        	$codigo_fazenda_anterior = $reg_animal->tbl_animal_codigo_fazenda;
	            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            	$codigo_animal = $reg_animal->tbl_animal_codigo_alfa .' '.
                             	 $reg_animal->tbl_animal_codigo_numerico;

		        $observacao = 'Motivo da morte: ' . $motivo_morte . '. Obs: ' . $observacao;
				$sql = "UPDATE tbl_animais SET
						tbl_animal_ativo='N',
						tbl_animal_baixado_em='$data_movimentacao',
						tbl_animal_baixado_por='$nome_usuario',
						tbl_animal_observacao='$observacao',
						tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
						tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
						tbl_animal_situacao='M'
				    WHERE tbl_animal_codigo_id='$codigo_id'";

			    $resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
					return 'Ocorreu um erro na atualização do animal.' . $erro_mysql;
				} 
	    	}
		}

		if ($controle_estoque=='I') {
	        $sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao,
	                 tbl_mov_estoque_codigo_pasto,
	                 tbl_mov_estoque_codigo_categoria,
	                 tbl_mov_estoque_codigo_raca,
	                 tbl_mov_estoque_codigo_pelagem,
	                 tbl_mov_estoque_sexo,
	                 tbl_mov_estoque_primeiro_peso
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_origem',
	                        'S',
	                        'M',
	                        '$local_origem',
	                        null,
	                        '$numero_movimentacao',
	                        '$codigo_pasto',
	                        '$codigo_categoria',
	                        null,
	                        null,
	                        '$sexo',
	                        null
	                )";
	        
	        $resultado = mysqli_query($conector,$sql);
	        $erro_mysql = mysqli_error($conector);

	        if (!$resultado){
				return 'Erro na gravacao histórico saída morte.' . $erro_mysql;
	        }
		}
		else {
        	$sql = "INSERT INTO tbl_movimentacao_estoque
	                (tbl_mov_estoque_codigo_id_animal,
	                 tbl_mov_estoque_data_emissao,
	                 tbl_mov_estoque_nascimento,
	                 tbl_mov_estoque_local,
	                 tbl_mov_estoque_entrada_saida,
	                 tbl_mov_estoque_tipo_movimentacao,
	                 tbl_mov_estoque_local_origem,
	                 tbl_mov_estoque_local_destino,
	                 tbl_mov_estoque_codigo_movimentacao,
	                 tbl_mov_estoque_codigo_pasto,
	                 tbl_mov_estoque_codigo_categoria,
	                 tbl_mov_estoque_codigo_raca,
	                 tbl_mov_estoque_codigo_pelagem,
	                 tbl_mov_estoque_sexo,
	                 tbl_mov_estoque_primeiro_peso
	                ) 
	                VALUES ('$codigo_id',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local_origem',
	                        'S',
	                        'M',
	                        '$local_origem',
	                        null,
	                        '$numero_movimentacao',
	                        '$codigo_pasto',
	                        '$codigo_categoria',
	                        null,
	                        null,
	                        '$sexo',
	                        null
	                )";
	        
	        $resultado = mysqli_query($conector,$sql);
	        $erro_mysql = mysqli_error($conector);

	        if (!$resultado){
				return 'Erro na gravacao histórico saída morte.' . $erro_mysql;
	        }
		}

	    // Inclui o flag de morte na tabela tbl_item_cobertura

        /*$item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	        INNER JOIN tbl_cobertura 
	        	    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
	        WHERE tbl_cobertura_controle='C' AND 
	              tbl_cobertura_encerrada='S' AND
	              tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND 
	              tbl_ite_cobertura_resultado_diagnostico='P'");*/
		//AND (tbl_ite_cobertura_nascido='' OR tbl_ite_cobertura_nascido IS NULL)

	    // A Leitura dessa tabela foi alterada em 02/10/2024 para atender os ajustes conforme o Trello cartão "VERIFICAR MORTE DA FÊMEA EM ESTAÇÃO DE MONTA PORÉM SEM TER O DIAGNOSTICO CONFIRMADO"

	    $item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
		    INNER JOIN tbl_cobertura 
		      	    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		    WHERE tbl_cobertura_lixeira=0 AND 
		          tbl_cobertura_controle='C' AND
		          tbl_ite_cobertura_codigo_id_animal='$codigo_id'
		    ORDER BY tbl_cobertura_id DESC LIMIT 1");

		$num_rows = mysqli_num_rows($item_cobertura);

		if ($num_rows!=0) {
			$situacao='M';
			$positivo='P';

		   	$reg_item = mysqli_fetch_object($item_cobertura);

		   	$cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
		   	$numero_item = $reg_item->tbl_ite_cobertura_numero_item;
		   	$situacao_d0 = $reg_item->tbl_ite_cobertura_dia_1;
		   	$diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
			$nascido = $reg_item->tbl_ite_cobertura_nascido;

		   	$qtd_animais = $reg_item->tbl_cobertura_qtd_animais;
		   	$grupo = $reg_item->tbl_cobertura_codigo_grupo;
		   	$protocolo = $reg_item->tbl_cobertura_protocoloiatf;

		   	if ($protocolo==0 || $situacao_d0=='') {
		   		$qtd_animais--;

				$sql = "UPDATE tbl_cobertura SET
					tbl_cobertura_qtd_animais='$qtd_animais',
					tbl_cobertura_alterado_em='$data_sistema',
					tbl_cobertura_alterado_por='$nome_usuario'
			 		WHERE tbl_cobertura_id ='$cobertura_id'";
			       	
				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
					return 'Erro na atualização da qtd de animais no grupo ' . $grupo . ' da cobertura ' . $cobertura_id . ' erro ' . $erro_mysql;
					exit;
				} 

				$sql = ("DELETE FROM tbl_item_cobertura 
				         WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
				               tbl_ite_cobertura_numero_item='$numero_item'");
				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
				   	return	'Erro na exclusão do registro item de cobertura ' . $erro_mysql;
					exit;
				}

				// reorganizar o campo tbl_ite_cobertura_numero_item temporario

			    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			        WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' 
			        ORDER BY tbl_ite_cobertura_numero_item ASC");

				$num_rows_item = mysqli_num_rows($tbl_item);    
				$numero_item_temporario = 9000;

				if ($num_rows_item!=0) {
					while ($reg_item = mysqli_fetch_object($tbl_item)) {
					    $numero_item_antigo =  $reg_item->tbl_ite_cobertura_numero_item;

					    $numero_item_temporario++;

						$sql = "UPDATE tbl_item_cobertura SET
									   tbl_ite_cobertura_numero_item='$numero_item_temporario'
						       	 WHERE tbl_ite_cobertura_numero_id='$cobertura_id' AND 
						       	       tbl_ite_cobertura_numero_item='$numero_item_antigo'";
						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado) {
					   		return	'Erro refazer os itens temporários! ' . $erro_mysql;
							exit;
						}
					}
				}	   

				// reorganizar o campo tbl_ite_cobertura_numero_item nova sequencia

			    $tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			        WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' 
			            ORDER BY tbl_ite_cobertura_numero_item ASC");

				$num_rows_item = mysqli_num_rows($tbl_item);    
				$numero_item_novo = 0;

				if ($num_rows_item!=0) {
					while ($reg_item = mysqli_fetch_object($tbl_item)) {
					    $numero_item_antigo =  $reg_item->tbl_ite_cobertura_numero_item;

					    $numero_item_novo++;

						$sql = "UPDATE tbl_item_cobertura SET
									   tbl_ite_cobertura_numero_item='$numero_item_novo'
						       	 WHERE tbl_ite_cobertura_numero_id='$cobertura_id' AND 
						       	       tbl_ite_cobertura_numero_item='$numero_item_antigo'";
						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado) {
					   		return	'Erro refazer os itens!  ' . $erro_mysql;
							exit;
						}
					}
				}	   
		   	}
		   	else if ($diagnostico==$positivo) {
		   		if ($nascido==''){
				    $sql = "UPDATE tbl_item_cobertura SET
							tbl_ite_cobertura_nascido='O',
							tbl_ite_cobertura_situacao_femea_nascido_outro='$situacao',
							tbl_ite_cobertura_negativo_em='$data_sistema',
							tbl_ite_cobertura_negativo_por='$nome_usuario'
			 			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			 	   			  tbl_ite_cobertura_numero_item='$numero_item'";
				       	
				   	$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
						return 'Ocorreu um erro na atualização do item de cobertura animal ' . $codigo_animal . ' erro ' . $erro_mysql;
					} 
		   		}
		   	}
		   	else{
				$sql = "UPDATE tbl_item_cobertura SET
					tbl_ite_cobertura_nascido='O',
					tbl_ite_cobertura_situacao_femea_nascido_outro='$situacao',
					tbl_ite_cobertura_resultado_diagnostico='N',
					tbl_ite_cobertura_negativo_em='$data_sistema',
					tbl_ite_cobertura_negativo_por='$nome_usuario'
			 		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			 			  tbl_ite_cobertura_numero_item='$numero_item'";
			       	
				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
					return 'Ocorreu um erro na atualização do item de cobertura animal ' . $codigo_animal . ' erro ' . $erro_mysql;
				}
		   	}
		}
	    // Fim inclui flag de vendido, morte ou outra saida

		return 'Gravei';	
	}

?>