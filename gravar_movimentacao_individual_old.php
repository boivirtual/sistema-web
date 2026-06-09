<?php 
    // Grava Venda com Pesagem
    // Grava Transferencia com Pesagem 
    // Morte
    // Outras Saidas

	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_movimentacao_id= $_POST['numero_movimentacao_id'];
	$tipo_gravacao = $_POST['tipo_gravacao'];
	$local_origem= $_POST['local_origem'];
	$local_destino= $_POST['local_destino'];
	$codigo_pesagem= $_POST['pesagem'];
	$data_movimentacao= $_POST['data_movimentacao'];
	$tipo_movimentacao = $_POST['tipo_movimentacao'];
	$controle_estoque= $_POST['controle_estoque'];
	$descricao_lote= $_POST['descricao_lote'];
	$descricao_filtro= $_POST['descricao_filtro'];
	$movimentacao_finalizada='N';

	$data_sistema = date("Y-m-d H:i:s");

    switch ($tipo_movimentacao) {
        case 'V':
            $codigo_tipo = 3;
            break;
        case 'C':
            $codigo_tipo = 4;
            break;
        case 'T':
            $codigo_tipo = 5;
	        break;
        case 'M':
            $codigo_tipo = 888;
	        break;
        default:
            $codigo_tipo = 999;
            break;
    }

	$array_itens = $_POST['array_itens'];

	$matriz_itens = explode("<|>", $array_itens);

	$quantidade_itens = count($matriz_itens);

/*	$mensagem = '';

	for($i=0; $i < $quantidade_itens; $i++) {
	    $tabela_itens = $matriz_itens[$i];
	    $itens = explode("|", $tabela_itens);

		$codigo_animal = ltrim($itens[0]);
		$codigo_animal = rtrim($codigo_animal);

		if ($itens[2]=='Macho') {
			$sexo='M';
		}
		else if ($itens[2]=='Femea' || $itens[2]=='Fêmea'){
			$sexo='F';
		}
		else {
			$sexo=$itens[2];
		}

		$data_nascimento = $itens[3];
		$codigo_id = $itens[8];
		$codigo_pasto = $itens[11];
		//$codigo_categoria = $itens[12];
		$desc_categoria='';

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

		$data = str_replace("/", "-", $data_nascimento);
    	$data = date('Y-m-d', strtotime($data));

	    $data_acompanhamento_calculo = date("Y-m-d");
	    $date = new DateTime($data); // Data de Nascimento
	    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
	    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
	    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
	    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

		$tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		    WHERE tab_registro_lixeira_categoria_idade='0'");

		$num_rows = mysqli_num_rows($tbl_categoria);    

		if ($num_rows!=0) {
		    while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
		        $idade_de = $reg_categoria->tab_categoria_idade_de;
		        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

		        if ($idade >= $idade_de && $idade <= $idade_ate) {
		            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
		        }
		    }
		} 

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
			WHERE tbl_animal_pasto_local = '$local_origem' AND 
			      tbl_animal_pasto_id = '$codigo_pasto' AND 
			      tbl_animal_pasto_sexo = '$sexo' AND 
			      tbl_animal_pasto_nascimento = '$data'");
		$num_rows_pasto = mysqli_num_rows($tbl_pasto);    

		if ($num_rows_pasto==0) {
			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$local_origem' AND 
				  tbl_animal_pasto_id = '$codigo_pasto' AND 
					  tbl_animal_pasto_sexo = '$sexo' AND 
					  tbl_animal_pasto_categoria = '$codigo_categoria' AND 
					  (tbl_animal_pasto_marcado_baixar='' OR 
					   tbl_animal_pasto_marcado_baixar IS NULL)
					ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
			$num_rows_pasto = mysqli_num_rows($tbl_pasto);    

			if ($num_rows_pasto==0) {
				$mensagem.= $data.'-'.$sexo.'-'.$codigo_categoria.'-'.$num_rows_pasto. ' não achei ';
			}
			else {
		        $reg_pasto_atual = mysqli_fetch_object($tbl_pasto);
		        $data_nascimento_atual = $reg_pasto_atual->tbl_animal_pasto_nascimento;
		        $numero_item_atual = $reg_pasto_atual->tbl_animal_pasto_numero_item;
				$mensagem.= $data.'-'.$sexo.'-'.$codigo_categoria.'-'.$num_rows_pasto. ' achei por categoria '.$data_nascimento_atual.'-'.$numero_item_atual;
			} 
		}
		else {
			
		    $reg_pasto = mysqli_fetch_object($tbl_pasto);
		    $numero_item = $reg_pasto->tbl_animal_pasto_numero_item;

			$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto SET
					tbl_animal_pasto_marcado_baixar='S'
			    WHERE tbl_animal_pasto_local = '$local_origem' AND 
			    	  tbl_animal_pasto_numero_item = '$numero_item' AND 
			  	      tbl_animal_pasto_id = '$codigo_pasto'");

			$mensagem.= $data.'-'.$sexo.'-'.$codigo_categoria.'-'.$num_rows_pasto. ' achei '; 
		}
	}

	header('Content-type: application/json');
   	echo json_encode(array('error' => true, 'message' => 'Saindo da gravacao' . $mensagem));
	mysqli_close($conector);
	exit;
*/

	if ($tipo_gravacao==1 && 
		($codigo_tipo==3 || 
		 $codigo_tipo==5 ||
		 $codigo_tipo==888 || 
		 $codigo_tipo==999)) {

		for($i=0; $i < $quantidade_itens; $i++) {
	    	$tabela_itens = $matriz_itens[$i];
	    	$itens = explode("|", $tabela_itens);

			$codigo_animal = ltrim($itens[0]);
			$codigo_animal = rtrim($codigo_animal);

			if ($itens[2]=='Macho') {
				$sexo='M';
			}
			else if ($itens[2]=='Femea' || $itens[2]=='Fêmea'){
				$sexo='F';
			}
			else {
				$sexo=$itens[2];
			}

			$data_nascimento = $itens[3];
			$codigo_id = $itens[8];
			$codigo_pasto = $itens[11];
			$codigo_categoria = $itens[12];

			// O codigo da categoria foi substituido pela data da nascimento em 20/08/2024 por conta do ajuste "AJUSTAR AS SAIDAS DE ANIMAIS DO PASTO POR ID" para controle de estoque por ID

			if ($controle_estoque=='I') {

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

				$data = str_replace("/", "-", $data_nascimento);
    			$data = date('Y-m-d', strtotime($data));

    			// ajusta a categoria conforme a data do nascimento
			    $data_acompanhamento_calculo = date("Y-m-d");
			    $date = new DateTime($data); // Data de Nascimento
			    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
			    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
			    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
			    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

				$tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
				    WHERE tab_registro_lixeira_categoria_idade='0'");

				$num_rows = mysqli_num_rows($tbl_categoria);    

				if ($num_rows!=0) {
				    while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
				        $idade_de = $reg_categoria->tab_categoria_idade_de;
				        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

				        if ($idade >= $idade_de && $idade <= $idade_ate) {
				            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
				        }
				    }
				} 

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
					      tbl_animal_pasto_id = '$codigo_pasto' AND 
					      tbl_animal_pasto_sexo = '$sexo' AND 
					      tbl_animal_pasto_nascimento = '$data' AND 
					  	 (tbl_animal_pasto_marcado_baixar='' OR 
					   	  tbl_animal_pasto_marcado_baixar IS NULL)");
		        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

		        /*if ($num_rows_pasto==0) {
					$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
						SET tbl_animal_pasto_marcado_baixar=null");

				 	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', Nascimento ' .$data_nascimento. ' no pasto.'));
					exit;
				}*/

		        if ($num_rows_pasto==0) {
		        	// procurar um registro por categoria.
					$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
						WHERE tbl_animal_pasto_local = '$local_origem' AND 
						      tbl_animal_pasto_sexo = '$sexo' AND 
						      tbl_animal_pasto_categoria = '$codigo_categoria' AND 
					  		 (tbl_animal_pasto_marcado_baixar='' OR 
					   		  tbl_animal_pasto_marcado_baixar IS NULL)
						ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
						//tbl_animal_pasto_id = '$codigo_pasto' AND
			        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

					if ($num_rows_pasto==0) {

						$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
							SET tbl_animal_pasto_marcado_baixar=null");

					 	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', categoria ' .$desc_categoria. ' no pasto 1.'));
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
							      tbl_animal_pasto_nascimento = '$data' AND 
							      (tbl_animal_pasto_marcado_baixar='' OR 
					   		  tbl_animal_pasto_marcado_baixar IS NULL)
							ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
				        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

				        if ($num_rows_pasto==0) {
							$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
								SET tbl_animal_pasto_marcado_baixar=null");

						 	header('Content-type: application/json');
						   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', categoria ' .$desc_categoria. ', nascimento '.$data.' em outros pastos 2.'));
							exit;
				        }
				        else {
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
							   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto atual' . $erro_mysql));
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
							   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto trocar' . $erro_mysql));
							   	exit;
							} 
				        }
					}	        	
		        }
		        else {
				    $reg_pasto = mysqli_fetch_object($tbl_pasto);
				    $numero_item = $reg_pasto->tbl_animal_pasto_numero_item;

					$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto SET
							tbl_animal_pasto_marcado_baixar='S'
					    WHERE tbl_animal_pasto_local = '$local_origem' AND 
					    	  tbl_animal_pasto_numero_item = '$numero_item' AND 
					  	      tbl_animal_pasto_id = '$codigo_pasto'");
		        }
			}
			else {
				$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
					WHERE tbl_animal_pasto_local = '$local_origem' AND 
					      tbl_animal_pasto_id = '$codigo_pasto' AND 
					      tbl_animal_pasto_sexo = '$sexo' AND 
					      tbl_animal_pasto_categoria = '$codigo_categoria'");
		        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

		        if ($num_rows_pasto==0) {
					$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
						SET tbl_animal_pasto_marcado_baixar=null");

				 	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ' categoria ' .$desc_categoria. ' no pasto. 3'));
					mysqli_close($conector);
					exit;
		        }
			}
		}
	}

/*	header('Content-type: application/json');
   	echo json_encode(array('error' => true, 'message' => 'Saindo da gravacao'));
	mysqli_close($conector);
	exit;
*/

    // Limpa o campo marcado para baixo apos verificar se todos os animais estão no pasto. Este campo é usado somente para isso
	$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
		SET tbl_animal_pasto_marcado_baixar=null");
    // 


	if (empty($_POST['total_pesados'])){
		$total_digitados = 0.00;
	}
	else {
		$total_digitados= $_POST['total_pesados'];
	}

	if (empty($_POST['peso_total_kg'])){
		$peso_total_kg = 0.00;
	}
	else {
		$peso_total_kg= $_POST['peso_total_kg'];
	}

	if (empty($_POST['peso_total_arroba'])){
		$peso_total_arroba = 0.00;
	}
	else {
		$peso_total_arroba= $_POST['peso_total_arroba'];
	}

	if (empty($_POST['peso_medio_kg'])){
		$peso_medio_kg = 0.00;
	}
	else {
		$peso_medio_kg= $_POST['peso_medio_kg'];
	}

	if (empty($_POST['peso_medio_arroba'])){
		$peso_medio_arroba = 0.00;
	}
	else {
		$peso_medio_arroba= $_POST['peso_medio_arroba'];
	}

	if ($numero_movimentacao_id && $tipo_gravacao==3) {
		$sql = "UPDATE tbl_animais SET
				tbl_animal_ativo='S',
				tbl_animal_baixado_em=null,
				tbl_animal_baixado_por=null,
				tbl_animal_observacao=null
		    WHERE tbl_animal_codigo_id='$id_animal_excluir'";

	    $resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao tornar o animal ativo ' . $erro_mysql));
		} 
		else {
	    	header('Content-type: application/json');
			echo json_encode(array('success' => true, 'message' => 'Item excluido com sucesso.', 'numero_doc' => $numero_movimentacao_id));

		}
		$tipo_gravacao=2;
	}

	if ($numero_movimentacao_id && $tipo_gravacao==2) {

	    $sql = "UPDATE tbl_movimentacao SET
			tbl_movimentacao_codigo_local_origem='$local_origem',
			tbl_movimentacao_codigo_local_destino='$local_destino',
			tbl_movimentacao_qtd_animais_pesados='$total_digitados',
			tbl_movimentacao_alterado_em='$data_sistema',
			tbl_movimentacao_alterado_por='$nomeusuario'
	    WHERE tbl_movimentacao_id='$numero_movimentacao_id'";

	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Movimentação incluída com sucesso.' , 'numero_doc' => $numero_movimentacao_id);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
				SET tbl_animal_pasto_marcado_baixar=null");
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a movimentação ' . $erro_mysql));
	    	exit;
		} 

		$sql = ("DELETE FROM tbl_item_movimentacao WHERE tbl_ite_movimentacao_numero_id='$numero_movimentacao_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			$sql = mysqli_query($conector, "UPDATE tbl_animal_pasto 
				SET tbl_animal_pasto_marcado_baixar=null");
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$codigo_animal = ltrim($itens[0]);
			$codigo_animal = rtrim($codigo_animal);
			$peso = $itens[1];
			$sexo = $itens[2];
			$data_nascimento = $itens[3];
			$raca = $itens[4];
			$pelagem = $itens[5];
			$mae = $itens[6];
			$observacao = ltrim($itens[7]);
			$observacao = rtrim($observacao);
			$codigo_id = $itens[8];

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
					tbl_ite_movimentacao_observacao
		        ) VALUES (
		            '$numero_movimentacao_id',
		            '$numero_item',
		            '$data_movimentacao',
		            '$codigo_id',
		            '$codigo_animal',
		            '$peso',
		            '$sexo',
		            '$data_nascimento',
		            '$raca',
		            '$pelagem',
		            '$mae',
		            '$observacao'
		    )";
		    $resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);
		}

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens. 1' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$observacao = ltrim($itens[7]);
			$observacao = rtrim($observacao);
			$codigo_id = $itens[8];

		   	$atualizar_animal = gravar_movimento_animal($conector, $codigo_id, $observacao, $codigo_tipo, $data_movimentacao, $numero_movimentacao_id);
		}

	    $resposta = array('success' => true, 'message' => 'Movimentação Incluida com sucesso.', 'numero_doc' => $numero_movimentacao_id);
		$erro_mysql = mysqli_error($conector);

		header('Content-type: application/json');
		echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}

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
			'$local_destino',
			'$codigo_tipo',
			'$total_digitados',
			'$peso_total_kg',
			'$peso_total_arroba',
			'$peso_medio_kg',
			'$peso_medio_arroba',
			'$descricao_filtro',
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
			'$codigo_pesagem'
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

	    $resposta = array('success' => true, 'message' => 'Movimentação incluída com sucesso.', 'numero_doc' => $numero_movimentacao);

	    if ($codigo_pesagem!=0) {
		    $sql = "UPDATE tbl_pesagem SET
				tbl_pesagem_codigo_movimentacao='$numero_movimentacao'
		    WHERE tbl_pesagem_id ='$codigo_pesagem'";

		    $resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
		    	header('Content-type: application/json');
		    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro atualizar o registro da pesagem ' . $erro_mysql));
		    	exit;
			} 
	    }

	    if ($controle_estoque=='I') { // grava movimentações estoque por ID
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
				//if ($mae=='') {$mae=0;}
				$observacao = ltrim($itens[7]);
				$observacao = rtrim($observacao);
				$codigo_id = $itens[8];
				$codigo_motivo_morte = $itens[10];
				$codigo_pasto = $itens[11];
				$codigo_categoria = $itens[12];

				$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
			        WHERE tbl_animal_codigo_id='$codigo_id'");

		    	$num_rows = mysqli_num_rows($rs);
		    	
		    	if ($num_rows!=0) {
		        	$reg_animal = mysqli_fetch_object($rs);
		           	$data_nascimento = $reg_animal->tbl_animal_data_nascimento;
		            $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
		            $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
		            $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

		            if ($codigo_tipo==888 || $codigo_tipo==999) {
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
		        }

				if ($codigo_categoria=='' || $codigo_categoria==0) {
	                $data_acompanhamento_calculo = date("Y-m-d");
	                $date = new DateTime($data_nascimento); // Data de Nascimento
	                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
	                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
	                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
	                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

		            $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		                        WHERE tab_registro_lixeira_categoria_idade='0'");

		            $num_rows = mysqli_num_rows($tbl_categoria);    

		            if ($num_rows!=0) {
		                while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
		                    $idade_de = $reg_categoria->tab_categoria_idade_de;
		                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

		                    if ($idade >= $idade_de && $idade <= $idade_ate) {
		                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
		                    }
		                }
		            } 
				}

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
						tbl_ite_movimentacao_codigo_categoria,
						tbl_ite_movimentacao_qtde_categoria
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
			            '$codigo_categoria',
			            1
			    )";

			    $resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
				  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens. 2' . $erro_mysql));
					mysqli_close($conector);
					exit;
				} 

				$motivo_morte = $itens[9];

				if ($itens[2]=='Macho') {
					$sexo='M';
				}
				else if ($itens[2]=='Femea' || $itens[2]=='Fêmea'){
					$sexo='F';
				}
				else {
					$sexo=$itens[2];
				}

				$atualizar_animal = gravar_movimento_animal($conector, $codigo_id, $observacao, $codigo_tipo, $motivo_morte, $local_origem, $local_destino, $data_movimentacao, $numero_movimentacao, $codigo_pasto, $codigo_categoria, $sexo, $controle_estoque, $desc_categoria, $peso, $data_nascimento);

				$atualiza_lote_dias_animais_pasto = atualiza_lote_dias_animais_pasto($conector, $local_origem, $codigo_pasto);

			}  // fim do for 

		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
			mysqli_close($conector);
			exit;
	    }
	    else { // grava movimentações estoque por lote
			for($i=0; $i < $quantidade_itens; $i++) {
	    		$tabela_itens = $matriz_itens[$i];

	    		$itens = explode("|", $tabela_itens);
				$item = $itens[0];
				$peso = $itens[1];

				if ($itens[2]=='Macho') {
					$sexo='M';
				}
				else if($itens[2]=='Femea' || $itens[2]=='Fêmea'){
					$sexo='F';
				}
				else {
					$sexo = $itens[2];
				}

				$qtde = $itens[3];
				$peso_medio = $itens[4];
				$peso_arroba = $itens[5];
				$peso_medio_arroba = $itens[6];
				$observacao = ltrim($itens[7]);
				$observacao = rtrim($observacao);
	            $motivo_morte=$itens[9];
                $codigo_morte=$itens[10];
				$codigo_categoria = $itens[12];
				$codigo_animal='';
				$codigo_id='0';

				if ($itens[11]==0) {
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
				else {
					$codigo_pasto = $itens[11];
				}

				// Pega o peso medio para morte e outra saida ou venda/transferencia sem peso

				if ($codigo_tipo==888 || $codigo_tipo==999 || $peso==0) {
		            $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
		                WHERE tbl_pm_local_id='$local_origem' AND 
		                      tbl_pm_categoria_id='$codigo_categoria' AND 
		                      tbl_pm_sexo='$sexo'");

		            $num_rows_media = mysqli_num_rows($tbl_media);

		            if ($num_rows_media!=0){
		                $reg_media = mysqli_fetch_object($tbl_media);
		                $id_media = $reg_media->tbl_pm_id;
		                $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
		                $peso_anterior = $reg_media->tbl_pm_peso_total_atual;
		                $peso_medio = $reg_media->tbl_pm_peso_medio_atual;
		                $peso = $reg_media->tbl_pm_peso_medio_atual;
		            }
		            else {
		                $qtd_anterior=0;
		                $peso_anterior=0;
		                $peso_medio=0;
		            }

	                $peso_animais_pesados_total = $peso_medio * $qtde;
					$peso_arroba = $peso_animais_pesados_total/30;
					$peso_medio_arroba = $peso_medio/30;

		            // Calcula a media atual e grava no banco de dados
		            if (($qtd_anterior - $qtde)<=0) {
		            	$peso_medio_atual = 0;
		            }
		            else {
			            $peso_medio_atual = ($peso_anterior - 
			            	$peso_animais_pesados_total) / ($qtd_anterior - $qtde);
		            }

		            $qtd_animais_atual = $qtd_anterior - $qtde;
		            $peso_total_atual = $peso_anterior - $peso_animais_pesados_total;

		            if ($num_rows_media==0) {
			            $sql = "INSERT INTO tbl_peso_medio_categoria (
			                tbl_pm_categoria_id,
			                tbl_pm_sexo,
			                tbl_pm_local_id,
			                tbl_pm_data,
			                tbl_pm_qtd_total_atual,
			                tbl_pm_peso_medio_atual,
			                tbl_pm_peso_total_atual
			                ) VALUES (
			                '$codigo_categoria',
			                '$sexo',
			                '$local_origem',
			                '$data_movimentacao',
			                '$qtd_animais_atual',
			                '$peso_medio_atual',
			                '$peso_total_atual'
			            )";
		            }
		            else {
		               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
		                        tbl_pm_qtd_total_atual='$qtd_animais_atual',
		                        tbl_pm_peso_medio_atual='$peso_medio_atual',
		                        tbl_pm_peso_total_atual='$peso_total_atual'
		                  WHERE tbl_pm_id ='$id_media'");
		            }

		            $resultado = mysqli_query($conector,$sql);
		            $erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação da media dos pesos' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}
				}
				else {
					// atualiza tabela peso medio com o peso digitado 
		            $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
		                WHERE tbl_pm_local_id='$local_origem' AND 
		                      tbl_pm_categoria_id='$codigo_categoria' AND 
		                      tbl_pm_sexo='$sexo'");

		            $num_rows_media = mysqli_num_rows($tbl_media);

		            if ($num_rows_media!=0){
		                $reg_media = mysqli_fetch_object($tbl_media);
		                $id_media = $reg_media->tbl_pm_id;
		                $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
		                $peso_anterior = $reg_media->tbl_pm_peso_total_atual;
		            }
		            else {
		                $qtd_anterior=0;
		                $peso_anterior=0;
		            }

	                $peso_animais_pesados_total = $peso;

		            // Calcula a media atual e grava no banco de dados
		            if (($qtd_anterior - $qtde)<=0) {
		            	$peso_medio_atual = 0;
		            }
		            else {
			            $peso_medio_atual = ($peso_anterior - $peso_animais_pesados_total) /
		                         ($qtd_anterior - $qtde);
		            }

		            $qtd_animais_atual = $qtd_anterior - $qtde;
		            $peso_total_atual = $peso_anterior - $peso_animais_pesados_total;

		            if ($num_rows_media==0) {
			            $sql = "INSERT INTO tbl_peso_medio_categoria (
			                tbl_pm_categoria_id,
			                tbl_pm_sexo,
			                tbl_pm_local_id,
			                tbl_pm_data,
			                tbl_pm_qtd_total_atual,
			                tbl_pm_peso_medio_atual,
			                tbl_pm_peso_total_atual
			                ) VALUES (
			                '$codigo_categoria',
			                '$sexo',
			                '$local_origem',
			                '$data_movimentacao',
			                '$qtd_animais_atual',
			                '$peso_medio_atual',
			                '$peso_total_atual'
			            )";
		            }
		            else {
		               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
		                        tbl_pm_qtd_total_atual='$qtd_animais_atual',
		                        tbl_pm_peso_medio_atual='$peso_medio_atual',
		                        tbl_pm_peso_total_atual='$peso_total_atual'
		                  WHERE tbl_pm_id ='$id_media'");
		            }

		            $resultado = mysqli_query($conector,$sql);
		            $erro_mysql = mysqli_error($conector);

					if (!$resultado) {
					   	header('Content-type: application/json');
					   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação da media dos pesos' . $erro_mysql));
						mysqli_close($conector);
						exit;
					}
				}

				$numero_item = $i + 1;
				
			    $sql = "INSERT INTO tbl_item_movimentacao (
			            tbl_ite_movimentacao_numero_id,
			            tbl_ite_movimentacao_numero_item,
			            tbl_ite_movimentacao_data_emissao,
			            tbl_ite_movimentacao_codigo_id_animal,
			            tbl_ite_movimentacao_codigo_animal,
						tbl_ite_movimentacao_peso,
						tbl_ite_movimentacao_sexo,
						tbl_ite_movimentacao_observacao,
						tbl_ite_movimentacao_codigo_pasto,
						tbl_ite_movimentacao_codigo_categoria,
						tbl_ite_movimentacao_peso_medio,
						tbl_ite_movimentacao_peso_arroba,
						tbl_ite_movimentacao_peso_arroba_medio,
						tbl_ite_movimentacao_qtde_categoria,
						tbl_ite_movimentacao_motivo_morte
			        ) VALUES (
			            '$numero_movimentacao',
			            '$numero_item',
			            '$data_movimentacao',
			            '$codigo_id',
			            '$codigo_animal',
			            '$peso_animais_pesados_total',
			            '$sexo',
			            '$observacao',
			            '$codigo_pasto',
			            '$codigo_categoria',
			            '$peso_medio',
			            '$peso_arroba',
			            '$peso_medio_arroba',
			            '$qtde',
			            '$codigo_morte'
			    )";
			    $resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
				  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens 3.' . $erro_mysql));
					mysqli_close($conector);
					exit;
				} 

				// ajusta os pesos da movimentaçao

				$peso_animais_pesados_total = $peso*$qtde;

		        $sql = ("UPDATE tbl_movimentacao SET 
		        		tbl_movimentacao_peso_kg='$peso_animais_pesados_total',
						tbl_movimentacao_peso_arroba='$peso_arroba',
						tbl_movimentacao_peso_medio_kg='$peso_medio',
						tbl_movimentacao_peso_medio_arroba='$peso_medio_arroba'
		                WHERE tbl_movimentacao_id  ='$numero_movimentacao'");

		        $resultado = mysqli_query($conector,$sql);

				for($j=0; $j < $qtde; $j++) {

					$codigo_categoria_atual = $codigo_categoria;
					
					$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
						WHERE tbl_animal_pasto_local = '$local_origem' AND 
						      tbl_animal_pasto_id ='$codigo_pasto' AND 
						      tbl_animal_pasto_sexo = '$sexo'");

			        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    
			        $codigo_categoria_pasto = 0;

			        if ($num_rows_pasto!=0) {
			           	while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {

	                        $data_nascimento = $reg_pasto->tbl_animal_pasto_nascimento;  
	                        $data_acompanhamento_calculo = date("Y-m-d");
	                        $date = new DateTime($data_nascimento); // Data de Nascimento
	                        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
	                        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
	                        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
	                        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

				            $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
				                        WHERE tab_registro_lixeira_categoria_idade='0'");
				            $num_rows_categoria = mysqli_num_rows($tbl_categoria);    

			                while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
			                    $idade_de = $reg_categoria->tab_categoria_idade_de;
			                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

			                    if ($idade >= $idade_de && $idade <= $idade_ate) {
			                        $codigo_categoria_pasto = $reg_categoria->tab_codigo_categoria_idade;
			                    }

		                        if ($codigo_categoria_pasto==$codigo_categoria_atual) {
					            	$numero_item_pasto = $reg_pasto->tbl_animal_pasto_numero_item;
									$sql = ("DELETE FROM tbl_animal_pasto 
										WHERE tbl_animal_pasto_local ='$local_origem' AND 
										      tbl_animal_pasto_numero_item='$numero_item_pasto'");
									$resultado = mysqli_query($conector,$sql);
									$erro_mysql = mysqli_error($conector);

									if (!$resultado){
									 	header('Content-type: application/json');
									   	echo json_encode(array('error' => true, 'message' => 'Erro na alteração do registro no pasto.' . $erro_mysql));
										mysqli_close($conector);
										exit;
									}

	                				$peso_animais_pesados_total = $peso/$qtde;

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
							                        'V',
							                        '$local_origem',
							                        '$local_destino',
							                        '$numero_movimentacao',
							                        '$codigo_pasto',
							                        '$codigo_categoria',
							                        null,
							                        null,
							                        '$sexo',
							                        $peso_animais_pesados_total
							                )";
							        
							            $resultado = mysqli_query($conector,$sql);
							            $erro_mysql = mysqli_error($conector);

							            if (!$resultado){
										  	header('Content-type: application/json');
										   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída venda. 1' . $erro_mysql));
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
							                        'T',
							                        '$local_origem',
							                        '$local_destino',
							                        '$numero_movimentacao',
							                        '$codigo_pasto',
							                        '$codigo_categoria',
							                        null,
							                        null,
							                        '$sexo',
							                        $peso_animais_pesados_total
							                )";
							        
							            $resultado = mysqli_query($conector,$sql);
							            $erro_mysql = mysqli_error($conector);

							            if (!$resultado){
										  	header('Content-type: application/json');
										   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída transferencia.' . $erro_mysql));
											mysqli_close($conector);
											exit;
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
							                        'O',
							                        '$local_origem',
							                        '$local_destino',
							                        '$numero_movimentacao',
							                        '$codigo_pasto',
							                        '$codigo_categoria',
							                        null,
							                        null,
							                        '$sexo',
							                        $peso_animais_pesados_total
							                )";
							        
							            $resultado = mysqli_query($conector,$sql);
							            $erro_mysql = mysqli_error($conector);

							            if (!$resultado){
										  	header('Content-type: application/json');
										   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico outra saída.' . $erro_mysql));
											mysqli_close($conector);
											exit;
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
							                        '$local_destino',
							                        '$numero_movimentacao',
							                        '$codigo_pasto',
							                        '$codigo_categoria',
							                        null,
							                        null,
							                        '$sexo',
							                        $peso_animais_pesados_total
							                )";
							        
							            $resultado = mysqli_query($conector,$sql);
							            $erro_mysql = mysqli_error($conector);

							            if (!$resultado){
										  	header('Content-type: application/json');
										   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída morte.' . $erro_mysql));
											mysqli_close($conector);
											exit;
							            }
									}
					            	$codigo_categoria_atual = 0;
					            	break;
		                        }
			                }
					    }
		            }
		            else {
					 	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ' no pasto ' . $erro_mysql));
						mysqli_close($conector);
						exit;
		            }
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
					   		echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal' . $erro_mysql));
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
			    		$peso_venda = $reg->tbl_fechamento_peso_sai_venda;
			    		$peso_tranferencia = $reg->tbl_fechamento_peso_sai_transferencia;
			    		$peso_outra = $reg->tbl_fechamento_peso_sai_outras;
			    		$peso_final = $reg->tbl_fechamento_peso_final;

					    $peso_final-=$peso;

						if ($codigo_tipo==3) { 
				    		$peso_venda+=$peso;
						}
						else if ($codigo_tipo==5) { 
				    		$peso_tranferencia+=$peso;
						}
						else if ($codigo_tipo==888) { 
				    		$peso_morte+=$peso;
						}
						else {
				    		$peso_outra+=$peso;
						}

						$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
						   		tbl_fechamento_peso_sai_morte='$peso_morte',
						   		tbl_fechamento_peso_sai_venda='$peso_venda',
						   		tbl_fechamento_peso_sai_transferencia='$peso_tranferencia',
						   		tbl_fechamento_peso_sai_outras='$peso_outra',
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

				$atualiza_lote_dias_animais_pasto = atualiza_lote_dias_animais_pasto($conector, $local_origem, $codigo_pasto);

				// Fim adiciona fechamento mensal
			}    

		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
			mysqli_close($conector);
			exit;
	    }
	}

    function gravar_movimento_animal($conector, $codigo_id, $observacao, $codigo_tipo, $motivo_morte, $local_origem, $local_destino, $data_movimentacao, $numero_movimentacao, $codigo_pasto, $codigo_categoria, $sexo, $controle_estoque, $desc_categoria, $peso, $data_nascimento) {

		@ session_start(); 
		$nomeusuario = $_SESSION['nome_usuario'];
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
                             
	        if ($codigo_tipo==888) {
	        	$observacao = 'Motivo da morte: ' . $motivo_morte . '. Obs: ' . $observacao;
			    $sql = "UPDATE tbl_animais SET
						tbl_animal_ativo='N',
						tbl_animal_baixado_em='$data_movimentacao',
						tbl_animal_baixado_por='$nomeusuario',
						tbl_animal_observacao='$observacao',
						tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
						tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
						tbl_animal_situacao='M'
				    WHERE tbl_animal_codigo_id='$codigo_id'";
	        }
	        else if ($codigo_tipo==999) {
			    $sql = "UPDATE tbl_animais SET
						tbl_animal_ativo='N',
						tbl_animal_baixado_em='$data_movimentacao',
						tbl_animal_baixado_por='$nomeusuario',
						tbl_animal_observacao='$observacao',
						tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
						tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
						tbl_animal_situacao='S'
				    WHERE tbl_animal_codigo_id='$codigo_id'";
	        }
	        else if ($codigo_tipo==3) {
				// em 19/08/2025 deixamos de atualizar a Origem (tbl_animal_codigo_origem)no Cadastro
			    /*$sql = "UPDATE tbl_animais SET
						tbl_animal_ativo='N',
						tbl_animal_baixado_em='$data_movimentacao',
						tbl_animal_baixado_por='$nomeusuario',
						tbl_animal_observacao='$observacao',
						tbl_animal_codigo_fazenda='$local_destino',
						tbl_animal_codigo_origem='$local_origem',
						tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
						tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
						tbl_animal_situacao='V'
				    WHERE tbl_animal_codigo_id='$codigo_id'";*/

			    $sql = "UPDATE tbl_animais SET
						tbl_animal_ativo='N',
						tbl_animal_baixado_em='$data_movimentacao',
						tbl_animal_baixado_por='$nomeusuario',
						tbl_animal_observacao='$observacao',
						tbl_animal_codigo_fazenda='$local_destino',
						tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
						tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
						tbl_animal_situacao='V'
				    WHERE tbl_animal_codigo_id='$codigo_id'";
	       	}
	        else if ($codigo_tipo==5) {
			    $sql = "UPDATE tbl_animais SET
						tbl_animal_ativo='N',
						tbl_animal_baixado_em='$data_movimentacao',
						tbl_animal_baixado_por='$nomeusuario',
						tbl_animal_observacao='$observacao',
						tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
						tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
						tbl_animal_situacao='T'
				    WHERE tbl_animal_codigo_id='$codigo_id'";
	       	}

		    $resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			  	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			} 
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
	                        'V',
	                        '$local_origem',
	                        '$local_destino',
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
			 	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída venda. ' . $data_nascimento . ' - ' . $codigo_id .' '. $erro_mysql));
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
	                        'T',
	                        '$local_origem',
	                        '$local_destino',
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
			  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída transferência. 1' . $erro_mysql));
				mysqli_close($conector);
				exit;
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
	                        '$local_destino',
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
			  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída morte.' . $erro_mysql));
				mysqli_close($conector);
				exit;
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
	                        'O',
	                        '$local_origem',
	                        '$local_destino',
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
			  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico saída outras saídas.' . $erro_mysql));
				mysqli_close($conector);
				exit;
	        }
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
			   		echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal' . $erro_mysql));
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
	    		$peso_venda = $reg->tbl_fechamento_peso_sai_venda;
	    		$peso_tranferencia = $reg->tbl_fechamento_peso_sai_transferencia;
	    		$peso_outra = $reg->tbl_fechamento_peso_sai_outras;
	    		$peso_final = $reg->tbl_fechamento_peso_final;

			    $peso_final-=$peso;

				if ($codigo_tipo==3) { 
		    		$peso_venda+=$peso;
				}
				else if ($codigo_tipo==5) { 
		    		$peso_tranferencia+=$peso;
				}
				else if ($codigo_tipo==888) { 
		    		$peso_morte+=$peso;
				}
				else {
		    		$peso_outra+=$peso;
				}

				$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
				   		tbl_fechamento_peso_sai_morte='$peso_morte',
				   		tbl_fechamento_peso_sai_venda='$peso_venda',
				   		tbl_fechamento_peso_sai_transferencia='$peso_tranferencia',
				   		tbl_fechamento_peso_sai_outras='$peso_outra',
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

	    // Inclui o flag de vendido, morte ou outras saidas na tabela tbl_item_cobertura
	    if ($codigo_tipo==3 || $codigo_tipo==888 || $codigo_tipo==999){


	    	// A Leitura dessa tabela foi alterada em 02/10/2024 para atender os ajustes conforme o Trello cartão "VERIFICAR MORTE DA FÊMEA EM ESTAÇÃO DE MONTA PORÉM SEM TER O DIAGNOSTICO CONFIRMADO"

		    $item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
			    INNER JOIN tbl_cobertura 
			      	    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			    WHERE tbl_cobertura_lixeira=0 AND 
			          (tbl_cobertura_controle='C' || tbl_cobertura_controle='M') AND
			          tbl_ite_cobertura_codigo_id_animal='$codigo_id'
			    ORDER BY tbl_cobertura_id DESC LIMIT 1");

		    $num_rows = mysqli_num_rows($item_cobertura);

		    if ($num_rows!=0) {
		    	if ($codigo_tipo==3) {
		    		$situacao='V';
		    	}
		    	else if ($codigo_tipo==888) {
		    		$situacao='M';
		    	}
		    	else {
		    		$situacao='O';
		    	}

				$positivo='P';
				
		       	$reg_item = mysqli_fetch_object($item_cobertura);
		       	$cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
		       	$numero_item = $reg_item->tbl_ite_cobertura_numero_item;
			   	$situacao_d0 = $reg_item->tbl_ite_cobertura_dia_1;
			   	$diagnostico = $reg_item->tbl_ite_cobertura_resultado_diagnostico;
			   	$nascido = $reg_item->tbl_ite_cobertura_nascido;
		        $controle = $reg_item->tbl_cobertura_controle;
				$data_prenhez = $reg_item->tbl_ite_cobertura_data_prenhes;

			   	$qtd_animais = $reg_item->tbl_cobertura_qtd_animais;
			   	$grupo = $reg_item->tbl_cobertura_codigo_grupo;
			   	$protocolo = $reg_item->tbl_cobertura_protocoloiatf;
				   
			   	if ((($protocolo==0 || $situacao_d0=='') && $controle=='C') || 
				   		($data_prenhez=='' && $controle=='M')) {
			   		$qtd_animais--;

				   	if ($qtd_animais == 0) {
						$sql = ("DELETE FROM tbl_cobertura 
						         WHERE tbl_cobertura_id ='$cobertura_id'");

						$resultado = mysqli_query($conector,$sql);
						$erro_mysql = mysqli_error($conector);

						if (!$resultado){
						   	return	'Erro na exclusão do registro de cobertura ' . $erro_mysql;
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
							return 'Erro na atualização da qtd de animais no grupo ' . $grupo . ' da cobertura ' . $cobertura_id . ' erro ' . $erro_mysql;
							exit;
						} 
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
								tbl_ite_cobertura_situacao_femea_nascido_outro='$situacao'
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
						tbl_ite_cobertura_negativo_por='$nomeusuario'
				 		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
				 			  tbl_ite_cobertura_numero_item='$numero_item'";
				       	
					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
						return 'Ocorreu um erro na atualização do item de cobertura animal ' . $codigo_animal . ' erro ' . $erro_mysql;
					}
			   	}
		    }
	    }
	    // Fim inclui flag de vendido, morte ou outra saida

	    // A categoria foi substituida pela data do nascimento em 20/08/2024 conforme 
	    // instruções para ajuste no Trello tituo 'AJUSTAR AS SAIDAS DE ANIMAIS DO PASTO POR ID'

	    if ($controle_estoque=='I') {
			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$local_origem' AND 
				      tbl_animal_pasto_id = '$codigo_pasto' AND 
				      tbl_animal_pasto_sexo = '$sexo' AND 
				      tbl_animal_pasto_nascimento = '$data_nascimento'");
	    } 
	    else {
			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
				WHERE tbl_animal_pasto_local = '$local_origem' AND 
				      tbl_animal_pasto_id = '$codigo_pasto' AND 
				      tbl_animal_pasto_sexo = '$sexo' AND 
				      tbl_animal_pasto_categoria = '$codigo_categoria'");
	    }

        $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

        //$codigo_categoria_pasto = 0;

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
			   	echo json_encode(array('error' => true, 'message' => 'Erro na alteração do registro no pasto.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
        }
        else {
		 	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ' categoria ' .$desc_categoria. ' nascimento '.$data_nascimento. ' no pasto.' . $erro_mysql));
			mysqli_close($conector);
			exit;
        }

    }

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
    }
?>