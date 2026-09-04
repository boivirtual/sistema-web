<?php 
	// Grava pesagem digitada no programa de inclui pesagem on-line
	// Conforme os ajustes incluidos no Trello item 'AJUSTES PARA PESAGEM ON-LINE E LISTA DO EXCEL' em 13/09/2024 esse programa deixou de ajustar o peso no cadastro de animais, isso será feito ao finalizar a pesagem on-line

	// O campo tipo_registro foi incluido na tabela 'tbl_pesagem' 13/09/2024 para identificar se foi pesagem on-line ou off-line

	// O campo Apartacao foi incluido na gravação da tabela tbl_item_pesagem em 14/03/2026
	// Alterar dos dados dos campos tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido em 22/04/2026

	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_pesagem_id= $_POST['numero_pesagem_id'];
	$tipo_gravacao = $_POST['tipo_gravacao'];
	$local= $_POST['local_pesagem'];
	$epoca_pesagem= $_POST['epoca_pesagem'];
	$descricao_lote= $_POST['descricao_lote'];
	$descricao_filtro= $_POST['descricao_filtro'];
	$data_pesagem = $_POST['data_pesagem'];
	$pesagem_finalizada='N';
	$array_itens = $_POST['array_itens'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);
	$tipo_registro = 'ONLINE';
	$total_a_pesar = $_POST['total_a_pesar'];

	if (empty($_POST['total_pesados'])){
		$total_pesados = 0.00;
	}
	else {
		$total_pesados= $_POST['total_pesados'];
	}

	if (empty($_POST['peso_total_kg'])){
		$peso_total_kg = 0.000;
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

	$data_sistema = date("Y-m-d H:i:s");

	if ($numero_pesagem_id && $tipo_gravacao==2) {

		// ------------------------------------------------------------------
		// Blindagem contra perda de itens (ajuste 2026-09-04)
		// Ver card do Trello "Pesagem on-line - edicao de item apaga os demais
		// itens quando a conexao falha". A rotina abaixo faz DELETE de todos os
		// itens + reinsercao a partir da lista enviada pelo navegador; se essa
		// lista chegar curta (POST truncado por queda de conexao, ou a tela
		// ainda nao terminou de carregar) os itens que faltarem sao perdidos.
		// ------------------------------------------------------------------

		$qtd_esperada = isset($_POST['qtd_esperada']) ? (int) $_POST['qtd_esperada'] : -1;
		$acao_item    = isset($_POST['acao']) ? $_POST['acao'] : '';

		// lista limpa: ignora entradas vazias ou com codigo 0
		$itens_limpos = array();
		foreach (explode("<|>", (string) $array_itens) as $linha_item) {
			$campos_item = explode("|", $linha_item);
			$cod0 = isset($campos_item[0]) ? trim($campos_item[0]) : '';
			if ($cod0 !== '' && $cod0 !== '0') {
				$itens_limpos[] = $linha_item;
			}
		}
		$qtd_recebida = count($itens_limpos);

		// quantos itens a pesagem tem hoje no banco
		$rs_contagem = mysqli_query($conector, "SELECT COUNT(*) AS c FROM tbl_item_pesagem
			WHERE tbl_ite_pesagem_numero_id='" . escapar($numero_pesagem_id, $conector) . "'");
		$qtd_atual = ($rs_contagem && ($reg_c = mysqli_fetch_assoc($rs_contagem))) ? (int) $reg_c['c'] : 0;

		// (A) o navegador informa quantas linhas mandou; tem que bater com o que chegou
		if ($qtd_esperada < 0 || $qtd_recebida !== $qtd_esperada) {
			error_log("gravar_pesagem_individual: ABORTADO pesagem {$numero_pesagem_id} - "
				. "esperava {$qtd_esperada} item(ns), chegaram {$qtd_recebida} (POST incompleto). Nada foi alterado.");
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' =>
				'A gravacao chegou incompleta ao servidor (provavel falha de conexao). '
				. 'Nada foi alterado. Recarregue a tela e tente de novo.'));
			mysqli_close($conector);
			exit;
		}

		// (B) esta rotina nunca reduz mais de 1 item; excluir 1 item manda acao=excluir_item
		$reducao_permitida = ($acao_item === 'excluir_item' && $qtd_recebida === $qtd_atual - 1);
		if ($qtd_atual > 0 && $qtd_recebida < $qtd_atual && !$reducao_permitida) {
			error_log("gravar_pesagem_individual: ABORTADO pesagem {$numero_pesagem_id} - "
				. "recebidos {$qtd_recebida}, tabela tem {$qtd_atual}, sem intencao de exclusao. Nada foi alterado.");
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' =>
				"A lista recebida ({$qtd_recebida}) tem menos animais do que a pesagem ja tem "
				. "gravados ({$qtd_atual}). Gravacao cancelada para nao perder dados. Recarregue a tela."));
			mysqli_close($conector);
			exit;
		}

		// daqui pra frente usa a lista limpa e validada
		$matriz_itens = $itens_limpos;
		$quantidade_itens = $qtd_recebida;

		mysqli_begin_transaction($conector);

	    $sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_codigo_local='" . escapar($local, $conector) . "',
			tbl_pesagem_codigo_epoca='" . escapar($epoca_pesagem, $conector) . "',
			tbl_pesagem_lote='" . escapar($descricao_lote, $conector) . "',
			tbl_pesagem_qtd_animais_a_pesar='" . escapar($total_a_pesar, $conector) . "',
			tbl_pesagem_qtd_animais_pesados='" . escapar($total_pesados, $conector) . "',
			tbl_pesagem_peso_kg='" . escapar($peso_total_kg, $conector) . "',
			tbl_pesagem_peso_arroba='" . escapar($peso_total_arroba, $conector) . "',
			tbl_pesagem_peso_medio_kg='" . escapar($peso_medio_kg, $conector) . "',
			tbl_pesagem_peso_medio_arroba='" . escapar($peso_medio_arroba, $conector) . "',
			tbl_pesagem_filtros='" . escapar($descricao_filtro, $conector) . "',
			tbl_pesagem_alterado_em='" . escapar($data_sistema, $conector) . "',
			tbl_pesagem_alterado_por='" . escapar($nomeusuario, $conector) . "'
	    WHERE tbl_pesagem_id='" . escapar($numero_pesagem_id, $conector) . "'";

	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Pesagem incluída com sucesso.' , 'numero_doc' => $numero_pesagem_id);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			mysqli_rollback($conector);
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a pesagem ' . $erro_mysql));
	    	exit;
		}

		$sql = ("DELETE FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id='" . escapar($numero_pesagem_id, $conector) . "'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			mysqli_rollback($conector);
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		if ($array_itens!='') {
			for($i=0; $i < $quantidade_itens; $i++) {
	    		$tabela_itens = $matriz_itens[$i];
	    		$itens = explode("|", $tabela_itens);

				$codigo_animal = escapar(trim((string)$itens[0]), $conector);
				$peso = escapar($itens[1], $conector);
				$sexo = escapar($itens[2], $conector);
				$nascimento = escapar($itens[3], $conector);
				$raca = escapar($itens[4], $conector);
				$pelagem = escapar($itens[5], $conector);
				$mae = escapar($itens[6], $conector);
				$observacao = escapar(trim((string)$itens[7]), $conector);
				$apartacao = escapar($itens[9], $conector);
				$mens_repetido = escapar($itens[10], $conector);
				$id_repetido = escapar($itens[11], $conector);
				$ultimo_peso = escapar($itens[12], $conector);

				$codigo_id = trim((string)$itens[8]);
				$codigo_id_sql = escapar($codigo_id, $conector);

				if ($codigo_id !== '') {
    				$animaisParaRecalcular[$codigo_id] = $codigo_id;
				}

	            $tbl_animal = mysqli_query($conector, "select * from tbl_animais
	            	where tbl_animal_codigo_id='$codigo_id_sql'");
	            $num_rows = mysqli_num_rows($tbl_animal);

	            if ($num_rows!=0){
	                $reg_animal = mysqli_fetch_object($tbl_animal);
	                $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
	            }
	            else {
	                 $data_nascimento = 0;
	            }

	            //$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
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
	            else {
	            	$codigo_categoria = 0;
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
						tbl_ite_pesagem_qtd_animais,
						tbl_ite_pesagem_criterio_apartacao,
						tbl_ite_pesagem_mens_repetido,
						tbl_ite_pesagem_id_repetido,
						tbl_ite_pesagem_ultimo_peso

			        ) VALUES (
			            '" . escapar($numero_pesagem_id, $conector) . "',
			            '$numero_item',
			            '" . escapar($data_pesagem, $conector) . "',
			            '$codigo_id_sql',
			            '$codigo_animal',
			            '$peso',
			            '$sexo',
			            '$nascimento',
			            '$raca',
			            '$pelagem',
			            '$mae',
			            '$observacao',
			            '$codigo_categoria',
			            1,
			            '$apartacao',
			            '$mens_repetido',
			            '$id_repetido',
			            '$ultimo_peso'
			    )";

			    $resultado = mysqli_query($conector,$sql);
			    if (!$resultado) { break; }
			}

	    	$erro_mysql = mysqli_error($conector);

			if (!$resultado){
				mysqli_rollback($conector);
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

		}

		// confere a contagem final antes de confirmar; so entao efetiva tudo
		$rs_conf = mysqli_query($conector, "SELECT COUNT(*) AS c FROM tbl_item_pesagem
			WHERE tbl_ite_pesagem_numero_id='" . escapar($numero_pesagem_id, $conector) . "'");
		$qtd_final = ($rs_conf && ($reg_f = mysqli_fetch_assoc($rs_conf))) ? (int) $reg_f['c'] : -1;

		if ($qtd_final !== $quantidade_itens) {
			mysqli_rollback($conector);
			error_log("gravar_pesagem_individual: ROLLBACK pesagem {$numero_pesagem_id} - "
				. "contagem final {$qtd_final} diferente de {$quantidade_itens}. Nada foi alterado.");
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' =>
				'Erro ao gravar os itens; nada foi alterado. Recarregue a tela e tente de novo.'));
			mysqli_close($conector);
			exit;
		}

		mysqli_commit($conector);

	    // Cria um array com todos os itens da pesagem não finalizada para poder depois verificar quais os itens repetidos e marcar de vermelhor no program form_pesagem_animais_editar_online

		$array_animais = array();

		$sql = "
		    SELECT DISTINCT i.tbl_ite_pesagem_codigo_id_animal
		    FROM tbl_item_pesagem i
		    INNER JOIN tbl_pesagem p
		        ON p.tbl_pesagem_id = i.tbl_ite_pesagem_numero_id
		    WHERE p.tbl_pesagem_finalizada = 'N'
		";

		$resultado = mysqli_query($conector, $sql);

		if ($resultado) {
		    if (mysqli_num_rows($resultado) > 0) {
		        while ($row = mysqli_fetch_assoc($resultado)) {
		            $idAnimal = trim((string)$row['tbl_ite_pesagem_codigo_id_animal']);

		            if ($idAnimal !== '') {
		                $array_animais[$idAnimal] = $idAnimal; // já evita duplicado
		            }
		        }
		    }
		} else {
		    // Se quiser só logar erro sem parar o sistema
		    error_log("Erro na consulta: " . mysqli_error($conector));
		}	

		// limpa todos as mensagens dos itens repetidos tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido
		$sql = "UPDATE tbl_item_pesagem ip
			INNER JOIN tbl_pesagem p 
	    			ON p.tbl_pesagem_id = ip.tbl_ite_pesagem_numero_id
			SET 
	    		ip.tbl_ite_pesagem_mens_repetido = NULL,
	    		ip.tbl_ite_pesagem_id_repetido = NULL
			WHERE 
	    		p.tbl_pesagem_finalizada = 'N'";
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro limpar os campos dos itens repetidos na tbl_item_pesahem -  ' . $erro_mysql));
		   	exit;
		} 

		// REFAZ A MESAGEM DOS ITENS REPETIDOS tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido
		foreach ($array_animais as $idAnimal) {
        	$recalcular = recalcularItensRepetidosPorAnimal($idAnimal, $conector);

        	if ($recalcular != true) {
           		header('Content-type: application/json');
           		echo json_encode(array('error' => true, 'message' => 'Ocorreu ao recalcular itens repetidos.'));
           		mysqli_close($conector);
           		exit;
        	}
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
	        'I',
	        '$data_pesagem',
			'$local',
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
			null,
			null,
			null,
			0,
			'$tipo_registro',
			'WEB'
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
			$apartacao = $itens[9];
			$mens_repetido = $itens[10];
			$id_repetido = $itens[11];
			$ultimo_peso = $itens[12];

            $sql = "SELECT tbl_animal_data_nascimento FROM tbl_animais 
            		WHERE tbl_animal_codigo_id='$codigo_id'";

            $tbl_animal = mysqli_query($conector, $sql);
            $num_rows = mysqli_num_rows($tbl_animal);

            if ($num_rows!=0){
                $reg_animal = mysqli_fetch_object($tbl_animal);
                $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
            }
            else {
				$formato_original = 'd/m/Y';
				$formato_desejado = 'Y-m-d';

				// Cria um objeto DateTime a partir da string e do formato original
				$data_obj = DateTime::createFromFormat($formato_original, $nascimento);

				// Verifica se a criação do objeto foi bem-sucedida
				if ($data_obj) {
				    // Formata o objeto para o novo formato
				    $data_nascimento = $data_obj->format($formato_desejado);
				} else {
				    $data_nascimento = 0;
				}            	
            }

            //$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
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
            else {
            	$codigo_categoria = 0;
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
					tbl_ite_pesagem_qtd_animais,
					tbl_ite_pesagem_criterio_apartacao,
					tbl_ite_pesagem_mens_repetido,
					tbl_ite_pesagem_id_repetido,
					tbl_ite_pesagem_ultimo_peso
		        ) VALUES (
		            '$numero_pesagem',
		            '$numero_item',
		            '$data_pesagem',
		            '$codigo_id',
		            '$codigo_animal',
		            '$peso',
		            '$sexo',
		            '$nascimento',
		            '$raca',
		            '$pelagem',
		            '$mae',
		            '$observacao',
		            '$codigo_categoria',
		            1,
		            '$apartacao',
		            '$mens_repetido',
		            '$id_repetido',
		            '$ultimo_peso'
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

	    // Cria um array com todos os itens da pesagem não finalizada para poder depois verificar quais os itens repetidos e marcar de vermelhor no program form_pesagem_animais_editar_online

		$array_animais = array();

		$sql = "
		    SELECT DISTINCT i.tbl_ite_pesagem_codigo_id_animal
		    FROM tbl_item_pesagem i
		    INNER JOIN tbl_pesagem p
		        ON p.tbl_pesagem_id = i.tbl_ite_pesagem_numero_id
		    WHERE p.tbl_pesagem_finalizada = 'N'
		";

		$resultado = mysqli_query($conector, $sql);

		if ($resultado) {
		    if (mysqli_num_rows($resultado) > 0) {
		        while ($row = mysqli_fetch_assoc($resultado)) {
		            $idAnimal = trim((string)$row['tbl_ite_pesagem_codigo_id_animal']);

		            if ($idAnimal !== '') {
		                $array_animais[$idAnimal] = $idAnimal; // já evita duplicado
		            }
		        }
		    }
		} else {
		    // Se quiser só logar erro sem parar o sistema
		    error_log("Erro na consulta: " . mysqli_error($conector));
		}	

		// limpa todos as mensagens dos itens repetidos tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido
		$sql = "UPDATE tbl_item_pesagem ip
			INNER JOIN tbl_pesagem p 
	    			ON p.tbl_pesagem_id = ip.tbl_ite_pesagem_numero_id
			SET 
	    		ip.tbl_ite_pesagem_mens_repetido = NULL,
	    		ip.tbl_ite_pesagem_id_repetido = NULL
			WHERE 
	    		p.tbl_pesagem_finalizada = 'N'";
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro limpar os campos dos itens repetidos na tbl_item_pesahem -  ' . $erro_mysql));
		   	exit;
		} 

		// REFAZ A MESAGEM DOS ITENS REPETIDOS tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido
		foreach ($array_animais as $idAnimal) {
        	$recalcular = recalcularItensRepetidosPorAnimal($idAnimal, $conector);

        	if ($recalcular != true) {
           		header('Content-type: application/json');
           		echo json_encode(array('error' => true, 'message' => 'Ocorreu ao recalcular itens repetidos.'));
           		mysqli_close($conector);
           		exit;
        	}
    	}

		$resposta = array('success' => true, 'message' => 'Pesagem Incluida com sucesso.', 'numero_doc' => $numero_pesagem);
				$erro_mysql = mysqli_error($conector);

		header('Content-type: application/json');
		echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}

	function escapar($valor, $conector) {
	    return mysqli_real_escape_string($conector, (string)$valor);
	}

	function recalcularItensRepetidosPorAnimal($idAnimal, $conector) {
	    mysqli_begin_transaction($conector);

	    try {
	        $idAnimal = trim((string)$idAnimal);

	        if ($idAnimal === '') {
	            throw new Exception("ID do animal não informado para recalcular repetidos.");
	        }

	        $idAnimalSql = escapar($idAnimal, $conector);

	        $sqlBusca = "
	            SELECT
	                i.tbl_ite_pesagem_numero_id AS pesagem_id,
	                i.tbl_ite_pesagem_numero_item AS numero_item,
	                i.tbl_ite_pesagem_codigo_id_animal AS id_animal,
	                COALESCE(p.tbl_pesagem_lote, '') AS lote
	            FROM tbl_item_pesagem i
	            INNER JOIN tbl_pesagem p
	                ON p.tbl_pesagem_id = i.tbl_ite_pesagem_numero_id
	            WHERE i.tbl_ite_pesagem_codigo_id_animal = '{$idAnimalSql}'
	              AND IFNULL(p.tbl_pesagem_lixeira, 0) = 0
	              AND IFNULL(p.tbl_pesagem_finalizada, 'N') = 'N'
	            ORDER BY i.tbl_ite_pesagem_numero_id
	        ";

	        $res = mysqli_query($conector, $sqlBusca);
	        if (!$res) {
	            throw new Exception("Erro ao buscar itens repetidos do animal: " . mysqli_error($conector));
	        }

	        $itens = [];
	        while ($row = mysqli_fetch_assoc($res)) {
	            $itens[] = [
	                'pesagem_id' => (string)$row['pesagem_id'],
	                'numero_item' => (string)$row['numero_item'],
	                'id_animal' => (string)$row['id_animal'],
	                'lote' => trim((string)$row['lote']),
	            ];
	        }

	        if (count($itens) === 0) {
	            mysqli_commit($conector);
	            return true;
	        }

	        foreach ($itens as $itemBase) {
	            $lotesOutros = [];
	            $idsOutros = [];

	            foreach ($itens as $itemOutro) {
	                $mesmoRegistro =
	                    $itemOutro['pesagem_id'] === $itemBase['pesagem_id'] &&
	                    $itemOutro['numero_item'] === $itemBase['numero_item'];

	                if ($mesmoRegistro) {
	                    continue;
	                }

	                if ($itemOutro['lote'] !== '') {
	                    $lotesOutros[] = $itemOutro['lote'];
	                }

	                $idsOutros[] = $itemOutro['pesagem_id'];
	            }

	            $lotesOutros = array_values(array_unique($lotesOutros));
	            $idsOutros = array_values(array_unique($idsOutros));

	            $mensagem = '';
	            $idsTexto = '';

	            if (count($idsOutros) > 0) {
	                $mensagem = 'Repetido em: ' . implode(', ', $lotesOutros);
	                $idsTexto = implode(',', $idsOutros);
	            }

	            $mensagemSql = escapar($mensagem, $conector);
	            $idsTextoSql = escapar($idsTexto, $conector);
	            $pesagemId = (int)$itemBase['pesagem_id'];
	            $numeroItem = (int)$itemBase['numero_item'];

	            $sqlUpdate = "
	                UPDATE tbl_item_pesagem
	                   SET tbl_ite_pesagem_mens_repetido = '{$mensagemSql}',
	                       tbl_ite_pesagem_id_repetido = '{$idsTextoSql}'
	                 WHERE tbl_ite_pesagem_numero_id = {$pesagemId}
	                   AND tbl_ite_pesagem_numero_item = {$numeroItem}
	            ";

	            if (!mysqli_query($conector, $sqlUpdate)) {
	                throw new Exception("Erro ao atualizar repetidos do item {$pesagemId}/{$numeroItem}: " . mysqli_error($conector));
	            }
	        }

	        mysqli_commit($conector);
	        return true;

	    } catch (Exception $e) {
	        mysqli_rollback($conector);
	        error_log("recalcularItensRepetidosPorAnimal: " . $e->getMessage());
	        return false;
	    }
	}
?>