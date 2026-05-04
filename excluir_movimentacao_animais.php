<?php
	$id_excluir = $_POST['id_excluir'];

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

	include "conecta_mysql.inc";

    $tbl_movimentacao = mysqli_query($conector, "SELECT * FROM tbl_movimentacao
        WHERE tbl_movimentacao_id='$id_excluir'");
                                    
    $reg_mov = mysqli_fetch_object($tbl_movimentacao);
    $codigo_pesagem = $reg_mov->tbl_movimentacao_codigo_pesagem;
    $tipo_mov = $reg_mov->tbl_movimentacao_tipo;
    $data_mov = $reg_mov->tbl_movimentacao_data;

    if ($tipo_mov!=4) {
    	if ($controle_estoque=='I') {
		    $tbl_item = mysqli_query($conector, "select * from tbl_item_movimentacao
		        where tbl_ite_movimentacao_numero_id='$id_excluir'"); 

		    while ($reg_item = mysqli_fetch_object($tbl_item)) {
			    $codigo_animal_id = $reg_item->tbl_ite_movimentacao_codigo_id_animal;

				$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
		              WHERE tbl_animal_codigo_id='$codigo_animal_id'");

		    	$num_rows = mysqli_num_rows($rs);
		    	if ($num_rows!=0) {
		        	$reg_animal = mysqli_fetch_object($rs);
		        	$codigo_origem_anterior = $reg_animal->tbl_animal_codigo_origem_anterior;
		        	$codigo_fazenda_anterior = $reg_animal->tbl_animal_codigo_fazenda_anterior;
		        	$situacao = $reg_animal->tbl_animal_situacao;
		        	$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;

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

		    	if ($situacao=='T'){
					$sql = "UPDATE tbl_animais SET
								tbl_animal_ativo='S',
								tbl_animal_situacao='',
								tbl_animal_baixado_em=null,
								tbl_animal_baixado_por=null,
								tbl_animal_observacao='',
								tbl_animal_codigo_origem_anterior=0,
								tbl_animal_codigo_fazenda_anterior=0
						    WHERE tbl_animal_codigo_id='$codigo_animal_id'";
		    	}
		    	else {
                	// em 19/08/2025 deixamos de atualizar a Origem (tbl_animal_codigo_origem)no Cadastro

					/*$sql = "UPDATE tbl_animais SET
								tbl_animal_ativo='S',
								tbl_animal_situacao='',
								tbl_animal_baixado_em=null,
								tbl_animal_baixado_por=null,
								tbl_animal_observacao='',
								tbl_animal_codigo_fazenda='$codigo_fazenda_anterior',
								tbl_animal_codigo_origem='$codigo_origem_anterior',
								tbl_animal_codigo_origem_anterior=0,
								tbl_animal_codigo_fazenda_anterior=0
						    WHERE tbl_animal_codigo_id='$codigo_animal_id'";*/

					$sql = "UPDATE tbl_animais SET
								tbl_animal_ativo='S',
								tbl_animal_situacao='',
								tbl_animal_baixado_em=null,
								tbl_animal_baixado_por=null,
								tbl_animal_observacao='',
								tbl_animal_codigo_fazenda='$codigo_fazenda_anterior',
								tbl_animal_codigo_origem_anterior=0,
								tbl_animal_codigo_fazenda_anterior=0
						    WHERE tbl_animal_codigo_id='$codigo_animal_id'";
		    	}

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
				  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Erro ao estornar a baixa do animal! ' . $codigo_numerico . ' - ' . $erro_mysql));
					mysqli_close($conector);
					exit;
				}

			    // Exclui o flag de vendido, morte ou outras saidas na tabela tbl_item_cobertura
			    if ($tipo_mov==3 || $tipo_mov==888 || $tipo_mov==999){

				    if ($tipo_mov==3) {
				    	$situacao='V';
				    }
				    else if ($tipo_mov==888) {
				    	$situacao='M';
				    }
				    else {
				    	$situacao='O';
				    }

			        /*$item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			            INNER JOIN tbl_cobertura 
			        	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			            WHERE tbl_cobertura_controle='C' AND 
			                  tbl_cobertura_encerrada='S' AND
			                  tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
			                  tbl_ite_cobertura_resultado_diagnostico='P' AND 
			                  tbl_ite_cobertura_nascido='O' AND 
			                  tbl_ite_cobertura_situacao_femea_nascido_outro='$situacao'");
					*/

			        $item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			            INNER JOIN tbl_cobertura 
			        	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			            WHERE tbl_cobertura_lixeira=0 AND 
			                  (tbl_cobertura_controle='C' OR tbl_cobertura_controle='M') AND 
			                  tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' 
			            ORDER BY tbl_ite_cobertura_numero_id DESC LIMIT 1
			                  ");

				    $num_rows = mysqli_num_rows($item_cobertura);

				    if ($num_rows!=0) {
				       	$reg_item = mysqli_fetch_object($item_cobertura);
				       	$cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
				       	$numero_item = $reg_item->tbl_ite_cobertura_numero_item;
				       	$diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
						$cobertura_encerrada = $reg_item->tbl_cobertura_encerrada;

						if ($cobertura_encerrada=='S') {
						    $sql = "UPDATE tbl_item_cobertura SET
									tbl_ite_cobertura_nascido='',
									tbl_ite_cobertura_situacao_femea_nascido_outro=''
					 			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
					 	   			  tbl_ite_cobertura_numero_item='$numero_item'";
						}	
						else if ($diagnostico=='N') {
						    $sql = "UPDATE tbl_item_cobertura SET
								tbl_ite_cobertura_nascido='',
								tbl_ite_cobertura_situacao_femea_nascido_outro='',
								tbl_ite_cobertura_resultado_diagnostico=''
					 			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
					 	   			  tbl_ite_cobertura_numero_item='$numero_item'";
						} 					    
				       	
					   	$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
						  	header('Content-type: application/json');
						   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do item de cobertura animal - ' . $erro_mysql));
							mysqli_close($conector);
							exit;
						} 
				    }
			    }
			    // Fim inclui flag de vendido, morte ou outra saida
		    }
    	}

	    $tbl_estoque = mysqli_query($conector, "select * from tbl_movimentacao_estoque
	        where tbl_mov_estoque_codigo_movimentacao='$id_excluir'"); 

	    while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
		    $codigo_animal_id = $reg_estoque->tbl_mov_estoque_codigo_id_animal;
		    $nascimento = $reg_estoque->tbl_mov_estoque_nascimento;
			$sexo = $reg_estoque->tbl_mov_estoque_sexo;
		    $codigo_pasto = $reg_estoque->tbl_mov_estoque_codigo_pasto;
		    $codigo_local = $reg_estoque->tbl_mov_estoque_local;
		    $codigo_categoria = $reg_estoque->tbl_mov_estoque_codigo_categoria;
	        $ent_sai = $reg_estoque->tbl_mov_estoque_entrada_saida;
	        $tipo = $reg_estoque->tbl_mov_estoque_tipo_movimentacao;

			$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
		        WHERE tbl_animal_codigo_id='$codigo_animal_id'");

		    $num_rows = mysqli_num_rows($rs);
		    if ($num_rows!=0) {
		       	$reg_animal = mysqli_fetch_object($rs);

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

			// AJUSTA REGITROS MEDIA CATEGORIA E PESAGEM PARA SISTEMA POR LOTE
			if ($controle_estoque=='L') {
			    $qtd_animais_pesados = 1;
			    $peso_animais_pesados = $reg_estoque->tbl_mov_estoque_primeiro_peso;

			    if (!$peso_animais_pesados) {
				    $peso_animais_pesados=0;
			    }

			    $peso_animais_pesados_total = $peso_animais_pesados * $qtd_animais_pesados;

			    // Pega ultima quantidade de animais e ultimo peso total
			    $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
			        WHERE tbl_pm_local_id='$codigo_local' AND 
			              tbl_pm_categoria_id='$codigo_categoria' AND 
			              tbl_pm_sexo='$sexo'");

			    $num_rows_media = mysqli_num_rows($tbl_media);

			    if ($num_rows_media!=0){
			        $reg_media = mysqli_fetch_object($tbl_media);
			        $id_media = $reg_media->tbl_pm_id;
			        $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
			        $peso_anterior = $reg_media->tbl_pm_peso_total_atual;

				    // Calcula a media atual e grava no banco de dados
				    $peso_medio_atual = ($peso_anterior + $peso_animais_pesados_total) /
				                        ($qtd_anterior + $qtd_animais_pesados);

				    $qtd_animais_atual = $qtd_anterior + $qtd_animais_pesados;
				    $peso_total_atual = $peso_anterior + $peso_animais_pesados_total;

				    $sql = ("UPDATE tbl_peso_medio_categoria  SET 
				                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
				                    tbl_pm_peso_medio_atual='$peso_medio_atual',
				                    tbl_pm_peso_total_atual='$peso_total_atual'
				            WHERE tbl_pm_id ='$id_media'");

				    $resultado = mysqli_query($conector,$sql);
				    $erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação da media dos pesos: - ' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}
			    }
			}

		    $incluir_pasto = incluir_pasto($conector, $codigo_local, $codigo_pasto, $nascimento, $sexo, $codigo_categoria);

		    if ($incluir_pasto!='Gravei') {
				header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Erro: - ' . $incluir_pasto));
				mysqli_close($conector);
				exit;
		    }

		    $ajustar_dias = ajustar_dias_pasto($conector, $codigo_pasto);

		    if ($ajustar_dias!='Gravei') {
				header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Erro: - ' . $ajustar_dias));
				mysqli_close($conector);
				exit;
		    }

			// Soma registro no fechamento mensal
			$data_hoje = date("Y-m-d");
			$partes_hoje = explode("-", $data_hoje);
			$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

			$partes_mov = explode("-", $data_mov);
			$anomes_final = $partes_mov[0].$partes_mov[1];
			$diferenca = $anomes_inicial - $anomes_final;

			if ($diferenca!=0) {
				$date = new DateTime($data_mov);
				$date->modify('last day of this month');
				$data_fechamento = $date->format('Y-m-d');

				$tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
			   		WHERE tbl_fechamento_local='$codigo_local' AND
			       		  tbl_fechamento_data='$data_fechamento' AND 
			       		  tbl_fechamento_categoria='$codigo_categoria' AND
			       		  tbl_fechamento_sexo='$sexo'");

				$num_rows = mysqli_num_rows($tbl_fechamento);    

				if ($num_rows!=0) {
					$reg = mysqli_fetch_object($tbl_fechamento);
					$fechamento_id = $reg->tbl_fechamento_id;
					$qtd_fechamento = $reg->tbl_fechamento_qtd;
    				$peso_fechamento = $reg->tbl_fechamento_peso;

					$qtd_fechamento++;
    				$peso_fechamento+=$peso;

					$sql = ("UPDATE tbl_fechamento_mensal_estoque SET 
							   		tbl_fechamento_qtd='$qtd_fechamento',
							   		tbl_fechamento_peso='$peso_fechamento'
					 	WHERE tbl_fechamento_id ='$fechamento_id'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal! - '. $erro_mysql));
						mysqli_close($conector);
						exit;
					}
			    }

				$tbl_fechamento_ent_sai = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
	        		WHERE tbl_fechamento_local='$codigo_local' AND
	              		  tbl_fechamento_data='$data_fechamento'");

	    		$num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

	    		if ($num_rows!=0) {
	    			$reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
	    			$fechamento_id = $reg->tbl_fechamento_id;
	    			$peso_ent_transferencia = $reg->tbl_fechamento_peso_ent_transferencia;
	    			$peso_ent_outra = $reg->tbl_fechamento_peso_ent_outras;
	    			$peso_morte = $reg->tbl_fechamento_peso_sai_morte;
	    			$peso_venda = $reg->tbl_fechamento_peso_sai_venda;
	    			$peso_sai_transferencia = $reg->tbl_fechamento_peso_sai_transferencia;
	    			$peso_sai_outra = $reg->tbl_fechamento_peso_sai_outras;
	    			$peso_final = $reg->tbl_fechamento_peso_final;

	                if ($ent_sai=='E') {
                    	if ($tipo=='T') {
	                        $peso_ent_transferencia-=$peso;
	                        $peso_final-=$peso;
	                    }
	                    else{
	                        $peso_ent_outra-=$peso;
	                        $peso_final-=$peso;
	                    }
	                }
	                else {
	                    if ($tipo=='M') {
	                        $peso_morte-=$peso;   
	                        $peso_final+=$peso;
	                    }
	                    else if ($tipo=='V') {
	                        $peso_venda-=$peso;
	                        $peso_final+=$peso;
	                    }
	                    else if ($tipo=='T') {
	                        $peso_sai_transferencia-=$peso;
	                        $peso_final+=$peso;
	                    }
	                    else {
	                        $peso_sai_outra-=$peso;
	                        $peso_final+=$peso;
	                    }
	                }

					$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
							tbl_fechamento_peso_ent_transferencia='$peso_ent_transferencia',
							tbl_fechamento_peso_ent_outras='$peso_ent_outra',
					   		tbl_fechamento_peso_sai_morte='$peso_morte',
					   		tbl_fechamento_peso_sai_venda='$peso_venda',
					   		tbl_fechamento_peso_sai_transferencia='$peso_sai_transferencia',
					   		tbl_fechamento_peso_sai_outras='$peso_sai_outra',
					   		tbl_fechamento_peso_final='$peso_final'
					 	WHERE tbl_fechamento_id ='$fechamento_id'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal Ent/Sai! - '. $erro_mysql));
						mysqli_close($conector);
						exit;
					}
	    		}
			}

			// Fim Soma registro
		}
    }
    
    if ($tipo_mov==4) {
	    $tbl_estoque = mysqli_query($conector, "select * from tbl_movimentacao_estoque
	        where tbl_mov_estoque_codigo_movimentacao='$id_excluir'"); 

	    while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
		    $codigo_animal_id = $reg_estoque->tbl_mov_estoque_codigo_id_animal;
		    $nascimento = $reg_estoque->tbl_mov_estoque_nascimento;
			$sexo = $reg_estoque->tbl_mov_estoque_sexo;
		    $codigo_pasto = $reg_estoque->tbl_mov_estoque_codigo_pasto;
		    $codigo_local = $reg_estoque->tbl_mov_estoque_local;
			$data_mov = $reg_estoque->tbl_mov_estoque_data_emissao;
			$codigo_categoria = $reg_estoque->tbl_mov_estoque_codigo_categoria;
			$peso = $reg_estoque->tbl_mov_estoque_primeiro_peso;

		    $exclui_pasto = exclui_pasto($conector, $codigo_local, $codigo_pasto, $nascimento, $sexo);

		    if ($exclui_pasto=='Erro') {
			    $valor[0]=9;
			    $valor[1]='Erro ao excluir o animal na Entrada. Verifique se existem animais nesse curral.';
			    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
			    die ($str);
		    }

			// Limpa Descrição do Lote caso o pasto fique vazio e atualiza quantida de dias com animal no pasto
			$data_sistema = date("Y-m-d H:i:s");

			$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$codigo_local' AND 
					  tbl_animal_pasto_id ='$codigo_pasto'");

			$num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);    
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
				    tbl_pasto_alterado_por = '$nomeusuario'
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
				        tbl_pasto_alterado_por = '$nomeusuario',
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
					    tbl_pasto_alterado_por = '$nomeusuario',
					    tbl_pasto_data_sem_animais = '$data_sistema',
					    tbl_pasto_data_sem_animais_anterior = '$data_sem_remover'
					    WHERE tbl_pasto_id =$codigo_pasto";

					$resultado = mysqli_query($conector, $query);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
					    header('Content-type: application/json');
					    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
					    exit;
					} 
				}
			} // Fim Atualiza Descrição do lote e dias com animal no pasto

			$sql = ("DELETE FROM tbl_animais 
		  		WHERE tbl_animal_codigo_id='$codigo_animal_id'");	    		
			$resultado = mysqli_query($conector,$sql);

			// Subtrai registro no fechamento mensal
			$data_hoje = date("Y-m-d");
			$partes_hoje = explode("-", $data_hoje);
			$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

			$partes_mov = explode("-", $data_mov);
			$anomes_final = $partes_mov[0].$partes_mov[1];
			$diferenca = $anomes_inicial - $anomes_final;

			if ($diferenca!=0) {
				$date = new DateTime($data_mov);
				$date->modify('last day of this month');
				$data_fechamento = $date->format('Y-m-d');

				$tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
			   		WHERE tbl_fechamento_local='$codigo_local' AND
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
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal! - '. $erro_mysql));
						mysqli_close($conector);
						exit;
					}
			    }

				$tbl_fechamento_ent_sai = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
	        		WHERE tbl_fechamento_local='$codigo_local' AND
	              		  tbl_fechamento_data='$data_fechamento'");

	    		$num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

	    		if ($num_rows!=0) {
	    			$reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
	    			$fechamento_id = $reg->tbl_fechamento_id;
	    			$peso_ent_compra = $reg->tbl_fechamento_peso_ent_compra;
	    			$peso_final = $reg->tbl_fechamento_peso_final;

                    $peso_ent_compra-=$peso;
                    $peso_final-=$peso;

					$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
						tbl_fechamento_peso_ent_compra='$peso_ent_compra',
						tbl_fechamento_peso_final='$peso_final'
					 	WHERE tbl_fechamento_id ='$fechamento_id'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal Ent/Sai! - '. $erro_mysql));
						mysqli_close($conector);
						exit;
					}
	    		}
			}

			// Fim Subtrai registro
	    }

		$sql = ("DELETE FROM tbl_movimentacao_estoque 
			  WHERE tbl_mov_estoque_codigo_movimentacao='$id_excluir'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Erro ao excluir o registro do item da movimentação estoque! - '. $erro_mysql));
			mysqli_close($conector);
			exit;
		}
    }
    else {
		$sql = ("DELETE FROM tbl_movimentacao_estoque 
			  WHERE tbl_mov_estoque_codigo_movimentacao='$id_excluir'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Erro ao excluir o registro do item da movimentação estoque! - '. $erro_mysql));
			mysqli_close($conector);
			exit;
		}
    }    

	$sql = ("DELETE FROM tbl_item_movimentacao 
		  WHERE tbl_ite_movimentacao_numero_id='$id_excluir'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao excluir o registro do item da movimentação! - '. $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	$sql = ("DELETE FROM tbl_movimentacao 
			  WHERE tbl_movimentacao_id='$id_excluir'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao excluir a movimentação! - '. $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	$sql = "UPDATE tbl_pesagem SET
   			tbl_pesagem_codigo_movimentacao=0
	    WHERE tbl_pesagem_id ='$codigo_pesagem'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	/*if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Não existe pesagem vinculada a essa movimentação! - '. $erro_mysql));
		mysqli_close($conector);
		exit;
	}*/

	// se for tipo 4 (compra) entao o lote foi excluido, nesse caso ao sair desse programa a descricao do lote não pode ir vazia.
	if ($tipo_mov==4) { 
		$id_pasto = '';
		$descricao_pasto = '';
		$descricao_lote = 'exclusao de compra';
	}
	else {
		// Verifica se o pasto esta sem a descrição do lote
		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
			WHERE tbl_pasto_id ='$codigo_pasto'");

		$num_rows = mysqli_num_rows($tbl_pasto);    

		if ($num_rows!=0) {
			$reg_pasto = mysqli_fetch_object($tbl_pasto);
			$id_pasto = $reg_pasto->tbl_pasto_id ;
			$descricao_pasto = $reg_pasto->tbl_pasto_descricao;
			$descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;

			if ($descricao_lote==null) {
				$descricao_lote = '';
			}
		}
		else {
			$id_pasto = '';
			$descricao_pasto = '';
			$descricao_lote = '';
		}
	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Registro estornado com sucesso! ', 'id_pasto' => $id_pasto, 'descricao_pasto' => $descricao_pasto, 'descricao_lote' => $descricao_lote));
	mysqli_close($conector);
	exit;

	function exclui_pasto($conector, $codigo_local, $codigo_pasto, $nascimento, $sexo) {

		$sql = ("DELETE FROM tbl_animal_pasto 
				  WHERE tbl_animal_pasto_local='$codigo_local' AND 
				  tbl_animal_pasto_id ='$codigo_pasto' AND 
			      tbl_animal_pasto_nascimento='$nascimento' AND 
			      tbl_animal_pasto_sexo='$sexo'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
			return 'Erro';
		}
		else {
			return '';
		}

	}

	function incluir_pasto($conector, $codigo_local, $codigo_pasto, $nascimento, $sexo, $codigo_categoria) {
       	$data_sistema = date("Y-m-d H:i:s");

		@ session_start(); 
		$nomeusuario = $_SESSION['nome_usuario'];
	    $controle_estoque = $_SESSION['controle_estoque'];
		
		$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
		    WHERE tbl_animal_pasto_local ='$codigo_local' 
			    ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");

		$num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);	

		if ($num_rows_animal_pasto!=0) {
			$reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto);
			$numero_item =  $reg_animal_pasto->tbl_animal_pasto_numero_item;
			$numero_item++;
		}
		else {
			$numero_item = 1;
		}

		$sql = "INSERT INTO tbl_animal_pasto (
			tbl_animal_pasto_local,
			tbl_animal_pasto_id,
			tbl_animal_pasto_numero_item,
			tbl_animal_pasto_nascimento,
			tbl_animal_pasto_sexo,
			tbl_animal_pasto_categoria,
			tbl_animal_pasto_raca,
			tbl_animal_pasto_situacao,
			tbl_animal_pasto_motivo_morte,
			tbl_animal_pasto_observacao,
			tbl_animal_pasto_incluido_em,
			tbl_animal_pasto_incluido_por,
			tbl_animal_pasto_baixado_em,
			tbl_animal_pasto_baixado_por
		    ) 
		    VALUES (
		   		'$codigo_local',
			    '$codigo_pasto',
			    '$numero_item',
			    '$nascimento',
			    '$sexo',
			    '$codigo_categoria',
			    null,
				'A',
				null,
				null,
		        '$data_sistema',
		        '$nomeusuario',
		        null,
		        null
		    )";
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
			return 'Erro ao inserir o animal no pasto ' . $erro_mysql;
		}
		else {
			return 'Gravei';
		}
	}

	// Ajustar dias com animais no pasto
	function ajustar_dias_pasto($conector, $codigo_pasto) {
       	$data_sistema = date("Y-m-d H:i:s");

		@ session_start(); 
		$nomeusuario = $_SESSION['nome_usuario'];

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
			WHERE tbl_pasto_id ='$codigo_pasto'");

		$num_rows_pasto = mysqli_num_rows($tbl_pasto);	

		if ($num_rows_pasto!=0) {
			$reg_pasto = mysqli_fetch_object($tbl_pasto);
			$descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
			$data_com_incluir = $reg_pasto->tbl_pasto_data_com_animais;
			$data_com_incluir_anterior = $reg_pasto->tbl_pasto_data_com_animais_anterior;
			$data_sem_incluir = $reg_pasto->tbl_pasto_data_sem_animais;
			$data_sem_incluir_anterior = $reg_pasto->tbl_pasto_data_sem_animais_anterior;

			if ($descricao_lote==null) {
				$descricao_lote = '';
			}
		}

		if ($descricao_lote=='') {
			$dataAtual = new DateTime();
			$dataCom = new DateTime($data_com_incluir);
			$diff = $dataAtual->diff($dataCom);

			if ($diff->h + ($diff->days * 24) < 24){
			    $query = "UPDATE tbl_pasto SET
			        tbl_pasto_alterado_em = '$data_sistema',
			        tbl_pasto_alterado_por = '$nomeusuario',
			        tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
			        tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
			        tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
			        tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
			    WHERE tbl_pasto_id = $codigo_pasto";

			    $resultado = mysqli_query($conector, $query);
			    $erro_mysql = mysqli_error($conector);

				if (!$resultado) {
					return 'Erro ao ajustar os dias com animal no pasto ' . $erro_mysql;
				}
		    }
			else {
			    $dataAtual = new DateTime();
			    $dataSem = new DateTime($data_sem_incluir);
			    $diff = $dataAtual->diff($dataSem);

			    if ($diff->h + ($diff->days * 24) < 24){
			        $query = "UPDATE tbl_pasto SET
			            tbl_pasto_alterado_em = '$data_sistema',
			            tbl_pasto_alterado_por = '$nomeusuario',
			            tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
			            tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
			            tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
			            tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
			        WHERE tbl_pasto_id = $codigo_pasto";

			        $resultado = mysqli_query($conector, $query);
			        $erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						return 'Erro ao ajustar os dias com animal no pasto ' . $erro_mysql;
					}
		        }
		        else {
		            $query = "UPDATE tbl_pasto SET
		                tbl_pasto_alterado_em = '$data_sistema',
		                tbl_pasto_alterado_por = '$nomeusuario',
		                tbl_pasto_data_com_animais = '$data_sistema',
		                tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
		                WHERE tbl_pasto_id = $codigo_pasto";

			        $resultado = mysqli_query($conector, $query);
			        $erro_mysql = mysqli_error($conector);

					if (!$resultado) {
						return 'Erro ao ajustar os dias com animal no pasto ' . $erro_mysql;
					}
		        }
		    }
		}
		return 'Gravei';
	}
?>