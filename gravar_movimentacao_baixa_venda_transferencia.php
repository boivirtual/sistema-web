<?php 
	// Grava Venda com item digitado (Gerado da lista)
    // Grava Transferencia com item digitado (Gerado da lista)

	// Esse programa mudou de nome em 08/01/2026 pois agora ele executa a baixa dos estoques a gravação da movimentacao ficou por conta do programa gravar_movimentacao_digitada.php que é a lista que pode ser motificada antes de baixar os estoques. 

	// A opcao do estoque por lotes saiu desse programa

	// Antes era gravar_movimentacao_individual_digitada.php
	// Em 08/01/2026 passou a ser gravar_movimentacao_baixa_venda_transferencia.php

	function buscarCategoria($idadeAnimal, $listaCategorias) {
	    foreach ($listaCategorias as $cat) {
	        // Exatamente a sua lógica de comparação:
	        if ($idadeAnimal >= $cat['idade_de'] && $idadeAnimal <= $cat['idade_ate']) {
	            return $cat; // Retorna o ID e a Descrição já formatada
	        }
	    }
	    return null; // Caso a idade não caia em nenhuma faixa
	}

	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$data_sistema = date("Y-m-d H:i:s");

    $idMovimentacaoGravada = $_POST['idMovimentacaoGravada'];
	$controle_estoque= $_POST['controle_estoque'];
	$local_origem= $_POST['local_origem'];
	$local_destino= $_POST['local_destino'];
	$data_movimentacao= $_POST['data_movimentacao'];
	$tipo_movimentacao = $_POST['tipo_movimentacao'];
	$descricao_filtro_itens= $_POST['descricao_filtro_dig'];

    switch ($tipo_movimentacao) {
        case 'V':
            $codigo_tipo = 3;
            break;
        case 'T':
            $codigo_tipo = 5;
	        break;
    }

	if (empty($_POST['total_digitados'])){
		$total_digitados = 0.00;
	}
	else {
		$total_digitados= $_POST['total_digitados'];
	}

    // para Venda/Transferencia tipo 3 ou 5 pega o codigo do pasto de saida
    //if ($codigo_tipo==3 || $codigo_tipo==5) {
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
		WHERE tbl_pasto_codigo_local='$local_origem' AND 
	          tbl_pasto_tipo_curral='S'");  
						
	$num_rows_pasto = mysqli_num_rows($tbl_pasto);

		if($num_rows_pasto!=0){
			$ln = mysqli_fetch_assoc($tbl_pasto);
		    $codigo_pasto = $ln['tbl_pasto_id'];
		}
		else {
			header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Não existe Pasto Saída para a Fazenda Origem ' . $local_origem ));
			exit;
		}
    //}

    // Pega categorias para consultas rápidas
    /*$desc_categoria = [];
    $arrayCategorias = [];
    $descricaoCategorias = [];

    $sql = "SELECT * FROM tabela_categoria_idade 
        WHERE tab_registro_lixeira_categoria_idade='0'"; 
        
    $rs = mysqli_query($conector,$sql); 

    while ($fila = mysqli_fetch_object($rs)){
        $codigo_id = $fila->tab_codigo_categoria_idade;
        $idade_de = $fila->tab_categoria_idade_de;
        $idade_ate = $fila->tab_categoria_idade_ate;

        if ($idade_ate==999999999){
            array_push($desc_categoria, '> 36 m');
                $descricaoCategorias = [
                    "id" => $codigo_id,
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
        else {
            array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
    }*/

    $arrayCategorias = [];

	$sql = "SELECT * FROM tabela_categoria_idade WHERE tab_registro_lixeira_categoria_idade='0'"; 
	$rs = mysqli_query($conector, $sql); 

	while ($fila = mysqli_fetch_object($rs)){
	    $id   = $fila->tab_codigo_categoria_idade;
	    $de   = (int)$fila->tab_categoria_idade_de;
	    $ate  = (int)$fila->tab_categoria_idade_ate;

	    // Criamos a descrição aqui para não precisar repetir a lógica depois
	    if ($ate == 999999999) {
	        $descricao = " > 36 meses";
	    } else 
	    {
	        $descricao = $de . " a " . $ate . " meses";
	    }

	    $arrayCategorias[] = [
	        "id"        => $id,
	        "idade_de"  => $de,
	        "idade_ate" => $ate,
	        "descricao" => $descricao
	    ];
	}

    // Pega os itens selecionados na lista e monta um array para consulta rapida

    $dados_itens=[];

    $tab_itens = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao 
        WHERE tbl_ite_movimentacao_numero_id='$idMovimentacaoGravada' AND
              tbl_ite_movimentacao_selecionado='S'");
            
    if (mysqli_num_rows($tab_itens)) {
   	    while ($reg_itens = mysqli_fetch_object($tab_itens)) {
   	    	$id = $reg_itens->tbl_ite_movimentacao_codigo_id_animal; 
        	$dados_itens[$id] = $reg_itens;
   	    }
    }

	$array_itens = $_POST['array_itens'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

	// Controle de estoque = I e se for venda ou transferencia, marca os animais/sexo/nascimento que já estão no pasto de saida 

	// O codigo da categoria foi substituido pela data da nascimento em 20/08/2024 por conta do ajuste "AJUSTAR AS SAIDAS DE ANIMAIS DO PASTO POR ID" para controle de estoque por ID

	//if ($controle_estoque=='I' && ($codigo_tipo==3 || $codigo_tipo==5)) {
		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];
    		$itens = explode("|", $tabela_itens);

    		$codigo_animal = ltrim($itens[0]);
			$codigo_animal = rtrim($codigo_animal);
			
			/*if ($itens[2]=='Macho') {
				$sexo='M';
			}
			else if ($itens[2]=='Femea' || $itens[2]=='Fêmea'){
				$sexo='F';
			}
			else {
				$sexo=$itens[2];
			}*/

			$sexo=$itens[2];
			$data_nascimento = $itens[3];
			$data = str_replace("/", "-", $data_nascimento);
    		$data = date('Y-m-d', strtotime($data));

			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$local_origem' AND 
				      tbl_animal_pasto_id = '$codigo_pasto' AND 
				      tbl_animal_pasto_sexo = '$sexo' AND 
				      tbl_animal_pasto_nascimento = '$data' AND 
				  	 (tbl_animal_pasto_marcado_baixar='' OR 
				   	  tbl_animal_pasto_marcado_baixar IS NULL)");
	        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

	        if ($num_rows_pasto!=0) {
				$reg_pasto = mysqli_fetch_object($tbl_pasto);
				$numero_item = $reg_pasto->tbl_animal_pasto_numero_item;

				$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto SET
					tbl_animal_pasto_marcado_baixar='S'
					WHERE tbl_animal_pasto_local = '$local_origem' AND 
					   	  tbl_animal_pasto_numero_item = '$numero_item' AND 
					      tbl_animal_pasto_id = '$codigo_pasto'");

				$itens[13] = 'S';
				$matriz_itens[$i] = implode("|", $itens);				
	        }
    	}
	//}

	// Controle de estoque = I e se for venda ou transferencia, verifica quais nao estao no pasto de saida e substitui 

	//if ($controle_estoque=='I' && ($codigo_tipo==3 || $codigo_tipo==5)) {
		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);

    		if ($itens[13]=='') {
	    		$codigo_animal = ltrim($itens[0]);
				$codigo_animal = rtrim($codigo_animal);

				/*if ($itens[2]=='Macho') {
					$sexo='M';
				}
				else if ($itens[2]=='Femea' || $itens[2]=='Fêmea'){
					$sexo='F';
				}
				else {
					$sexo=$itens[2];
				}*/

				$sexo=$itens[2];
				$data_nascimento = $itens[3];
				$data_nascimento_lista = str_replace("/", "-", $data_nascimento);
	    		$data_nascimento_lista = date('Y-m-d', strtotime($data_nascimento_lista));

                // Verifica qual categoria do animal que esta na lista 

	            $data_acompanhamento_calculo = date("Y-m-d");
	            $date = str_replace("/", "-", $data_nascimento);
		        $date = new DateTime($date);
		        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
		        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
		        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
		        $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');
		        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

				$categoriaEncontrada = buscarCategoria($idade, $arrayCategorias);

				if ($categoriaEncontrada) {
				    $codigo_categoria_lista = $categoriaEncontrada['id'];
				    $desc_categoria_lista   = $categoriaEncontrada['descricao'];
				} else {
				    // Segurança: caso a idade não esteja em nenhuma faixa do banco
				    $codigo_categoria_lista = 0;
				    $desc_categoria_lista   = "Não classificado";
				}

		        /*for($j = 0; $j < count($arrayCategorias); $j++){
		            $id_categoria = $arrayCategorias[$j]['id'];
		            $idade_de = $arrayCategorias[$j]['idade_de'];
		            $idade_ate = $arrayCategorias[$j]['idade_ate'];

		            if ($idade >= $idade_de && $idade <= $idade_ate) {
		                $codigo_categoria_lista = $id_categoria;

		                if ($idade_ate==999999999) {
		                    $desc_categoria_lista=' > 36 meses';
		                }
		                else {
		                    $desc_categoria_lista= $idade_de . ' a ' . $idade_ate . ' meses';
		                }
		            }
		        }*/                       

	    		// primeiro pega um item do pasto de saida qua nao esta marcado para baixar e que seja da mesma categoria e sexo

				$animal_encontrado_pasto = false;

				$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
					WHERE tbl_animal_pasto_local = '$local_origem' AND 
					      tbl_animal_pasto_id = '$codigo_pasto' AND 
					      tbl_animal_pasto_sexo = '$sexo' AND 
					      (tbl_animal_pasto_marcado_baixar='' OR 
					   	  tbl_animal_pasto_marcado_baixar IS NULL)");
		        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

		        if ($num_rows_pasto!=0) {
 					while ($reg_pasto = mysqli_fetch_object($tbl_pasto)){
			           	$data_nascimento_atual = $reg_pasto->tbl_animal_pasto_nascimento;

			           	// verifica qual a categoria conforme a data de nascimento

			            $data_acompanhamento_calculo = date("Y-m-d");
			            //$date = str_replace("/", "-", $data_nascimento_atual);
				        $date = new DateTime($data_nascimento_atual);
				        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
				        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
				        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
				        $idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');

				        $idade_atual = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

						$categoriaEncontrada = buscarCategoria($idade_atual, $arrayCategorias);

						if ($categoriaEncontrada) {
						    $codigo_categoria_atual = $categoriaEncontrada['id'];
						    $desc_categoria_atual = $categoriaEncontrada['descricao'];
						} else {
						    // Segurança: caso a idade não esteja em nenhuma faixa do banco
						    $codigo_categoria_atual = 0;
						    $desc_categoria_atual   = "Não classificado";
						}

				        // agora compara as categorias do animal da lista com o que esta procurando no pasto                       

				        if ($codigo_categoria_atual==$codigo_categoria_lista) {
							$numero_item_atual = $reg_pasto->tbl_animal_pasto_numero_item;

							// agora procura o item da tabela sexo/nascimento em outros pastos para substituir
							$sql =  "SELECT * FROM tbl_animal_pasto 
								WHERE tbl_animal_pasto_local = '$local_origem' AND 
								      tbl_animal_pasto_sexo = '$sexo' AND 
								      tbl_animal_pasto_nascimento = '$data_nascimento_lista' AND 
								  	 (tbl_animal_pasto_marcado_baixar='' OR 
								   	  tbl_animal_pasto_marcado_baixar IS NULL)";
							$tbl_pasto = mysqli_query($conector, $sql);
					        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

					        if ($num_rows_pasto!=0) {
					        	$reg_pasto_trocar = mysqli_fetch_object($tbl_pasto);
					           	$data_nascimento_trocar = $reg_pasto_trocar->tbl_animal_pasto_nascimento;
					           	$numero_item_trocar = $reg_pasto_trocar->tbl_animal_pasto_numero_item;
					           	$pasto_trocar = $reg_pasto_trocar->tbl_animal_pasto_id;

					           	//Salva o pasto atual com a nova data de nascimento
								$sql = "UPDATE tbl_animal_pasto SET
										tbl_animal_pasto_nascimento='$data_nascimento_trocar',
										tbl_animal_pasto_marcado_baixar='S'
									WHERE tbl_animal_pasto_local = '$local_origem' AND 
									      tbl_animal_pasto_numero_item = '$numero_item_atual' AND 
									      tbl_animal_pasto_id = '$codigo_pasto'";

								$resultado = mysqli_query($conector,$sql);
								$erro_mysql = mysqli_error($conector);

								if (!$resultado){
									$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
										SET tbl_animal_pasto_marcado_baixar=null");
								   	header('Content-type: application/json');
								   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto Saída' . $erro_mysql));
								   	exit;
								} 

					           	//Salva o pasto trocar com a data de nascimento anterior
								$sql = "UPDATE tbl_animal_pasto SET
										tbl_animal_pasto_nascimento='$data_nascimento_atual',
										tbl_animal_pasto_marcado_baixar=NULL
									WHERE tbl_animal_pasto_local = '$local_origem' AND 
									      tbl_animal_pasto_numero_item = '$numero_item_trocar' AND 
									      tbl_animal_pasto_id = '$pasto_trocar'";

								$resultado = mysqli_query($conector,$sql);
								$erro_mysql = mysqli_error($conector);

								if (!$resultado){
									$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
										SET tbl_animal_pasto_marcado_baixar=null");
								   	header('Content-type: application/json');
								   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto que será substuído' . $erro_mysql));
								   	exit;
								} 

								$itens[13] = 'S';
								$matriz_itens[$i] = implode("|", $itens);
								$animal_encontrado_pasto = true;
								break; // termina o while				
							}
							else {
								$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
									SET tbl_animal_pasto_marcado_baixar=null");
								header('Content-type: application/json');
								echo json_encode(array('error' => true, 'message' => 'Não existe animal do sexo: ' . $sexo . ' Nascimento: ' . $data_nascimento . ' nos pastos. Código do animal verificado: '. $codigo_animal));
								exit;
							}
				        }

 					} // fim do while $reg_pasto

 					if (!$animal_encontrado_pasto) {
						$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto	SET tbl_animal_pasto_marcado_baixar=null");
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Não existe animal do sexo: ' . $sexo . ' Nascimento: ' . $data_nascimento . ' Categoria: ' . $desc_categoria_lista . ' no pasto Saída. Código do animal verificado: '. $codigo_animal));
							exit;
 					}
		        }
		        else { // não achou nenhum outro animal no pasto de saida para substituir
						$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto
							SET tbl_animal_pasto_marcado_baixar=null");
						header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Não existe animal do sexo: ' . $sexo . ' Nascimento: ' . $data_nascimento . ' Categoria: ' . $desc_categoria_lista . ' no pasto Saída. Código do animal verificado: '. $codigo_animal));
						exit;
		        }
    		} // fim $itens[13]
    	} // fim for
	//} // fim primeiro if

	// Limpa o campo marcado para baixo apos verificar se todos os animais estão no pasto. Este campo é usado somente para isso
	$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
		SET tbl_animal_pasto_marcado_baixar=null");
    // 

	// Grava a movimentação 
	// Não precisa mais pois ja foi gravado antes 
	/*$sql = "INSERT INTO tbl_movimentacao (
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
			'$local_destino',
			'$codigo_tipo',
			'$total_digitados',
			null,
			null,
			null,
			null,
			'$descricao_filtro_itens',
			'$movimentacao_finalizada',
			'$data_sistema',
			'$nomeusuario',
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
	*/

	$resposta = array('success' => true, 'message' => 'Movimentação incluída com sucesso.');

	$peso_total=0;
	$peso_total_arroba=0;
	$peso_total_medio=0;
	$peso_total_arroba_medio=0;

	for($i=0; $i < $quantidade_itens; $i++) {
    	$tabela_itens = $matriz_itens[$i];

    	$itens = explode("|", $tabela_itens);

		$numero_item = $i + 1;

		$codigo_animal = ltrim($itens[0]);
		$codigo_animal = rtrim($codigo_animal);
		$sexo=$itens[2];
		$data_nascimento = $itens[3];
		$raca = $itens[4];
		$pelagem = $itens[5];
		$mae = $itens[6];
		$observacao = ltrim($itens[7]);
		$observacao = rtrim($observacao);
		$codigo_id = $itens[8];
		//$codigo_categoria=$itens[12];
		$qtd_categoria=1;

		$data = str_replace("/", "-", $data_nascimento);
   		$data = date('Y-m-d', strtotime($data));

        $peso = 0;

   		if (isset($dados_itens[$codigo_id])) {
   			$peso = $dados_itens[$codigo_id]->tbl_ite_movimentacao_peso;
   		}

    	$peso_total+=$peso;

		$data_acompanhamento_calculo = date("Y-m-d");
		$date = new DateTime($data);
		$idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
		$idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
		$idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
		$idade_acompanhamento_mostra_dias = $idade_acompanhamento->format('%d');
	    $idade= $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

		$categoriaEncontrada = buscarCategoria($idade, $arrayCategorias);

		if ($categoriaEncontrada) {
		    $codigo_categoria = $categoriaEncontrada['id'];
			$desc_categoria = $categoriaEncontrada['descricao'];
		} else {
		    $codigo_categoria = 0;
		    $desc_categoria = "Não classificado";
		}

	    $rs = mysqli_query($conector, "SELECT * FROM tbl_animais
	        WHERE tbl_animal_codigo_id='$codigo_id'");

	    $num_rows = mysqli_num_rows($rs);

	    if ($num_rows!=0) {
	        $reg_animal = mysqli_fetch_object($rs);
	        $codigo_fazenda_anterior = $reg_animal->tbl_animal_codigo_fazenda;
	        $codigo_origem_anterior = $reg_animal->tbl_animal_codigo_origem;
            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            $codigo_animal = $reg_animal->tbl_animal_codigo_alfa . $reg_animal->tbl_animal_codigo_numerico; 
    	}

	    if ($codigo_tipo==3) {
		    $sql = "UPDATE tbl_animais SET
				tbl_animal_ativo='N',
				tbl_animal_baixado_em='$data_movimentacao',
				tbl_animal_baixado_por='$nomeusuario',
				tbl_animal_observacao='$observacao',
				tbl_animal_codigo_fazenda='$local_destino',
				tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
				tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
				tbl_animal_situacao='V'
			WHERE tbl_animal_codigo_id='$codigo_id'";
	    }
	    else if ($codigo_tipo==5) {
		    $sql = "UPDATE tbl_animais SET
				tbl_animal_ativo='N',
				tbl_animal_baixado_em='$data_movimentacao',
				tbl_animal_baixado_por='$nomeusuario',
				tbl_animal_observacao='$observacao',
				tbl_animal_situacao='T'
		    WHERE tbl_animal_codigo_id='$codigo_id'";
	    }
		
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal ' . $codigo_animal. ' erro ' . $erro_mysql));
			mysqli_close($conector);
			exit; 
		} 

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
	             tbl_mov_estoque_codigo_movimentacao,
	             tbl_mov_estoque_codigo_pasto,
	             tbl_mov_estoque_codigo_raca,
	             tbl_mov_estoque_codigo_pelagem,
	             tbl_mov_estoque_sexo,
	             tbl_mov_estoque_codigo_categoria,
	             tbl_mov_estoque_primeiro_peso
	            ) 
	        VALUES ('$codigo_id',
	            '$data_movimentacao',
	            '$data_nascimento',
	            '$local_origem',
	            'S',
	            'V',
	            '$local_origem',
	            '$local_destino',
	            '$idMovimentacaoGravada',
	            '$codigo_pasto',
	            null,
	            null,
	            '$sexo',
	            '$codigo_categoria',
	            '$peso'
	        )";
	        
	        $resultado = mysqli_query($conector,$sql);
	        $erro_mysql = mysqli_error($conector);

	        if (!$resultado){
				header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Erro na gravacao do histórico saída Venda - Animal ' . $codigo_animal . ' erro ' .  $erro_mysql));
				mysqli_close($conector);
				exit; 
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
	            tbl_mov_estoque_codigo_movimentacao,
	            tbl_mov_estoque_codigo_pasto,
	            tbl_mov_estoque_codigo_raca,
	            tbl_mov_estoque_codigo_pelagem,
	            tbl_mov_estoque_sexo,
	            tbl_mov_estoque_codigo_categoria,
	            tbl_mov_estoque_primeiro_peso
	        ) 
	        VALUES ('$codigo_id',
	            '$data_movimentacao',
	            '$data_nascimento',
	            '$local_origem',
	            'S',
	            'T',
	            '$local_origem',
	            '$local_destino',
	            '$idMovimentacaoGravada',
	            '$codigo_pasto',
	            null,
	            null,
	            '$sexo',
	            '$codigo_categoria',
	            '$peso'
	        )";
	        
	        $resultado = mysqli_query($conector,$sql);
	        $erro_mysql = mysqli_error($conector);

	        if (!$resultado){
				header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Erro na gravacao do histórico saída transferência - Animal ' . $codigo_animal . ' erro ' .  $erro_mysql));
				mysqli_close($conector);
				exit; 
	        }
	    }

	    // Inclui o flag de vendido na tabela tbl_item_cobertura
	    if ($codigo_tipo==3){

			// A Leitura dessa tabela foi alterada em 02/10/2024 para atender os ajustes conforme o Trello cartão "VERIFICAR MORTE DA FÊMEA EM ESTAÇÃO DE MONTA PORÉM SEM TER O DIAGNOSTICO CONFIRMADO"

			$item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			    INNER JOIN tbl_cobertura 
			     	    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			    WHERE tbl_cobertura_lixeira=0 AND 
			          (tbl_cobertura_controle='C' OR tbl_cobertura_controle='M') AND
			          tbl_ite_cobertura_codigo_id_animal='$codigo_id'
			    ORDER BY tbl_cobertura_id DESC LIMIT 1");

		    $num_rows = mysqli_num_rows($item_cobertura);
			$positivo = 'P';
				
		    if ($num_rows!=0) {
		       	$reg_item = mysqli_fetch_object($item_cobertura);
		       	$cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
		       	$numero_item = $reg_item->tbl_ite_cobertura_numero_item;
		       	$controle = $reg_item->tbl_cobertura_controle;
			   	$situacao_d0 = $reg_item->tbl_ite_cobertura_dia_1;
			   	$diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
			   	$nascido = $reg_item->tbl_ite_cobertura_nascido;
			   	$data_prenhez = $reg_item->tbl_ite_cobertura_data_prenhes;
			   	$qtd_animais = $reg_item->tbl_cobertura_qtd_animais;
			   	$grupo = $reg_item->tbl_cobertura_codigo_grupo;
			   	$protocolo = $reg_item->tbl_cobertura_protocoloiatf;
					   
			   	if ((($protocolo==0 || $situacao_d0=='') && $controle=='C')) {
				   		$qtd_animais--;

			   		if ($qtd_animais == 0) {
						$sql = ("DELETE FROM tbl_cobertura 
						         WHERE tbl_cobertura_id ='$cobertura_id'");
						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
					   		header('Content-type: application/json');
							echo json_encode(array('error' => true, 'message' => 'Erro na exclusão do registro de cobertura ' . $erro_mysql));
							mysqli_close($conector);
						exit; 
						}
			   		}
			   		else {
						$sql = "UPDATE tbl_cobertura SET
							tbl_cobertura_qtd_animais='$qtd_animais',
							tbl_cobertura_alterado_em='$data_alteracao',
							tbl_cobertura_alterado_por='$nomeusuario'
					 		WHERE tbl_cobertura_id ='$cobertura_id'";

						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
					   		header('Content-type: application/json');
							echo json_encode(array('error' => true, 'message' => 'Erro na atualização da qtd de animais no grupo ' . $grupo . ' da cobertura ' . $cobertura_id . ' erro ' . $erro_mysql));
							mysqli_close($conector);
							exit; 
						} 
					}
					       	
					$sql = ("DELETE FROM tbl_item_cobertura 
					         WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
					               tbl_ite_cobertura_numero_item='$numero_item'");
					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
				   		header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Erro na exclusão do registro item de cobertura ' . $erro_mysql));
						mysqli_close($conector);
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
						   		header('Content-type: application/json');
								echo json_encode(array('error' => true, 'message' => 'Erro refazer os itens temporários (item de cobertura). ' . $erro_mysql));
								mysqli_close($conector);
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
						   		header('Content-type: application/json');
								echo json_encode(array('error' => true, 'message' => 'Erro refazer os itens (item de cobertura). ' . $erro_mysql));
								mysqli_close($conector);
								exit; 
							}
						}
					}	   
			   	}
			   	else if ($diagnostico==$positivo) {
			   		if ($nascido==''){
					    $sql = "UPDATE tbl_item_cobertura SET
								tbl_ite_cobertura_nascido='O',
								tbl_ite_cobertura_situacao_femea_nascido_outro='V'
				 			WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
				 	   			  tbl_ite_cobertura_numero_item='$numero_item'";
						       	
					   	$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
					   		header('Content-type: application/json');
							echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do item de cobertura animal ' . $codigo_animal . ' erro ' . $erro_mysql));
							mysqli_close($conector);
							exit;
						} 
			   		}
			   	}
			   	else{
					$sql = "UPDATE tbl_item_cobertura SET
						tbl_ite_cobertura_nascido='O',
						tbl_ite_cobertura_situacao_femea_nascido_outro='V',
						tbl_ite_cobertura_resultado_diagnostico='N',
						tbl_ite_cobertura_negativo_em='$data_alteracao',
						tbl_ite_cobertura_negativo_por='$nomeusuario'
				 		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
					 			  tbl_ite_cobertura_numero_item='$numero_item'";
				       	
					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
				   		header('Content-type: application/json');
						echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do item de cobertura animal ' . $codigo_animal . ' erro ' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}
			   	}
	        }
        } // Fim inclui flag de vendido
	        
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
					echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do fechamento mensal! '. $erro_mysql));
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
	    		$peso_venda = $reg->tbl_fechamento_peso_sai_venda;
	    		$peso_tranferencia = $reg->tbl_fechamento_peso_sai_transferencia;
    			$peso_final = $reg->tbl_fechamento_peso_final;

				if ($codigo_tipo==3) { 
		    		$peso_venda+=$peso;
		    		$peso_final-=$peso;
				}
				else if ($codigo_tipo==5) { 
		    		$peso_tranferencia+=$peso;
		    		$peso_final-=$peso;
				}

				$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
				   		tbl_fechamento_peso_sai_venda='$peso_venda',
				   		tbl_fechamento_peso_sai_transferencia='$peso_tranferencia',
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
		}

		// Fim adiciona fechamento mensal

	    // A categoria foi substituida pela data do nascimento em 20/08/2024 conforme 
	    // instruções para ajuste no Trello tituo 'AJUSTAR AS SAIDAS DE ANIMAIS DO PASTO POR ID'

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
			WHERE tbl_animal_pasto_local = '$local_origem' AND 
			      tbl_animal_pasto_id ='$codigo_pasto' AND 
			      tbl_animal_pasto_sexo = '$sexo' AND 
			      tbl_animal_pasto_nascimento = '$data_nascimento'");

        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

        if ($num_rows_pasto!=0) {
			$reg_pasto = mysqli_fetch_object($tbl_pasto);
			$numero_item = $reg_pasto->tbl_animal_pasto_numero_item;
				
			$sql = ("DELETE FROM tbl_animal_pasto 
			          WHERE tbl_animal_pasto_local ='$local_origem' AND 
			                tbl_animal_pasto_numero_item='$numero_item'");
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
	   			header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Erro na alteração do registro no pasto. Animal ' . $codigo_animal . ' erro ' .  $erro_mysql));
				mysqli_close($conector);
				exit;
			}
        }
        else {
	   		header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ' categoria ' .$desc_categoria. ' no pasto Saída. Para o animal ' . $codigo_animal));
			mysqli_close($conector);
			exit;
        }

 		$atualiza_lote_dias_animais_pasto = atualiza_lote_dias_animais_pasto($conector, $local_origem, $codigo_pasto);

	   	if ($atualiza_lote_dias_animais_pasto!='Gravei') {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => $atualizar_animal));
			mysqli_close($conector);
			exit;
		}
	} // fim do for   

	// Ajusta o registro da movimentação se for controle de estoque por ID
    if ($peso_total==0) {
    	$peso_total = 1;
    }

    $peso_total_medio= $peso_total/$quantidade_itens;
	$peso_total_arroba=$peso_total/30;
	$peso_total_arroba_medio=$peso_total_arroba/$quantidade_itens;
		
	$sql = "UPDATE tbl_movimentacao SET
		tbl_movimentacao_situacao='N',
	    tbl_movimentacao_qtd_animais_pesados='$quantidade_itens',
		tbl_movimentacao_peso_kg='$peso_total',
		tbl_movimentacao_peso_arroba='$peso_total_arroba',
		tbl_movimentacao_peso_medio_kg='$peso_total_medio',
		tbl_movimentacao_peso_medio_arroba='$peso_total_arroba_medio'
	    WHERE tbl_movimentacao_id='$idMovimentacaoGravada'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alterção da movimentação (Peso Total).' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
    
    // Limpa os dados da tabela de item movimentacao que estao com o item selecionado = 'N'

    $sql = ("DELETE FROM tbl_item_movimentacao
        WHERE tbl_ite_movimentacao_numero_id ='$idMovimentacaoGravada' AND 
              tbl_ite_movimentacao_selecionado='N'");
    $resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão dos itens da movimentação não selecionados. ' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	header('Content-type: application/json');
	echo json_encode($resposta);
	mysqli_close($conector);
	exit;

	// Limpa Descrição do Lote caso o pasto fique vazio e atualiza quantida de dias com animal no pasto

    function atualiza_lote_dias_animais_pasto($conector, $local_origem, $codigo_pasto) {

	@ session_start(); 
		$nome_usuario = $_SESSION['nome_usuario'];
		$data_sistema = date("Y-m-d H:i:s");

        if ($codigo_pasto==0 || $codigo_pasto=='') {
			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
				WHERE tbl_pasto_codigo_local='$local_origem' AND 
			          tbl_pasto_tipo_curral='S'");  
				
			$num_rows_pasto = mysqli_num_rows($tbl_pasto);

			if($num_rows_pasto !=0){
				$ln = mysqli_fetch_assoc($tbl_pasto);
			    $codigo_pasto = $ln['tbl_pasto_id'];
			}
			else {
				$codigo_pasto = 0;
			}
        }    

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
				return 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql;		    	
		        /*header('Content-type: application/json');
		        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql));
		        exit;*/
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
					return 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql;		    	
			        /*header('Content-type: application/json');
			        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql));
			        exit;*/
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
					return 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql;		

			        /*header('Content-type: application/json');
			        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
			        exit;*/
			    } 
			}

		} // Fim Atualiza Descrição do lote e dias com animal no pasto

		return 'Gravei';
    }

?>