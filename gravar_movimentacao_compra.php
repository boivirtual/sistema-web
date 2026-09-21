<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
    $controle_estoque = $_SESSION['controle_estoque'];

	$local_origem= $_POST['local_origem'];
	$local_destino= $_POST['local_destino'];
	$codigo_pesagem= $_POST['pesagem'];
	$data_movimentacao= $_POST['data_movimentacao'];
	$tipo_movimentacao = $_POST['tipo_movimentacao'];
	$descricao_lote= '';
	$descricao_filtro_itens= '';
	$movimentacao_finalizada='N';
	$total_digitados = $_POST['total_digitados_entrada'];
    $codigo_tipo = 4;

	$data_sistema = date("Y-m-d H:i:s");

	$array_itens = $_POST['array_itens'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

    if ($controle_estoque=="L") {
		for ($i = 1; $i <=5; $i++) {
		    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
	        $qtd_animais_macho[$j] = 0;
    	    $peso_animais_macho[$j] = 0;
        	$qtd_animais_femea[$j] = 0;
        	$peso_animais_femea[$j] = 0;
		}
	}

	// Soma total de peso
	$total_peso = 0;
	$total_arroba = 0;
	$total_peso_medio = 0;
	$total_peso_medio_arroba = 0;

	for($i=0; $i < $quantidade_itens; $i++) {
    	$tabela_itens = $matriz_itens[$i];

    	$itens = explode("|", $tabela_itens);
		$qtd = $itens[5];
        $peso = $itens[11];

		$total_peso+= $peso*$qtd;
		$total_arroba = $total_peso/30;

		$total_peso_medio+= $peso;
		$total_peso_medio_arroba = $total_peso_medio/30;
	}
	// Fim soma

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
			'$total_peso',
			'$total_arroba',
			'$total_peso_medio',
			'$total_peso_medio_arroba',
			null,
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
			0
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

	$resposta = array('success' => true, 'message' => 'Movimentação incluída com sucesso.');

	for($i=0; $i < $quantidade_itens; $i++) {
    	$tabela_itens = $matriz_itens[$i];

    	$itens = explode("|", $tabela_itens);
		$categoria = $itens[0];
		$idade = $itens[1];
		$sexo = $itens[2];
		$raca = $itens[3];
		$pelagem = $itens[4];
		$qtd = $itens[5];
		$sequencia = $itens[6];
		$alfa = $itens[7];
        $desc_raca = $itens[8];
        $desc_pelagem = $itens[9];
        $desc_categoria = $itens[10];
        $peso = $itens[11];

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
					tbl_ite_categoria_compra,
					tbl_ite_qtd_categoria_compra,
					tbl_ite_idade_meses_compra,
					tbl_ite_sequencia_numerica_compra,
					tbl_ite_marcacao_alfa_compra
		        ) VALUES (
		            '$numero_movimentacao',
		            '$numero_item',
		            '$data_movimentacao',
		            null,
		            null,
		            '$peso',
		            '$sexo',
		            null,
		            '$desc_raca',
		            '$desc_pelagem',
		            null,
		            null,
		            null,
		            '$categoria',
		            '$qtd',
		            '$idade',
		            '$sequencia',
		            '$alfa'
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

	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
	    WHERE tbl_pasto_codigo_local='$local_destino' AND 
	          tbl_pasto_modulo=999 AND 
	          tbl_pasto_tipo_curral='E'");

	$num_rows = mysqli_num_rows($tbl_pasto);	
	$codigo_pasto = 0;

	if ($num_rows!=0) {
		$reg_pasto = mysqli_fetch_object($tbl_pasto);
		$codigo_pasto =  $reg_pasto->tbl_pasto_id;
	}

	// Verifica a descricao do lote no pasto
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
		WHERE tbl_pasto_id ='$codigo_pasto'");

	$num_rows_pasto = mysqli_num_rows($tbl_pasto);	

	if ($num_rows_pasto!=0) {
		$reg_pasto = mysqli_fetch_object($tbl_pasto);
		$id_pasto = $reg_pasto->tbl_pasto_id ;
		$descricao_pasto = $reg_pasto->tbl_pasto_descricao;
		$descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
		$data_com_incluir = $reg_pasto->tbl_pasto_data_com_animais;
		$data_com_incluir_anterior = $reg_pasto->tbl_pasto_data_com_animais_anterior;
		$data_sem_incluir = $reg_pasto->tbl_pasto_data_sem_animais;
		$data_sem_incluir_anterior = $reg_pasto->tbl_pasto_data_sem_animais_anterior;

		if ($descricao_lote==null) {
			$descricao_lote = '';
		}
	}
	else {
		$id_pasto = '';
		$descricao_pasto = '';
		$descricao_lote = '';
	}

	if ($controle_estoque=='L') {
		// GERAR DADOS POR CATEGORIA PARA GRAVAR A MEDIA

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
	        '$data_movimentacao',
			'$local_destino',
			4,
			'PESAGEM INCLUIDA NA COMPRA',
			'$total_digitados',
			'$total_peso',
			'$total_arroba',
			'$total_peso_medio',
			'$total_peso_medio_arroba',
			null,
			'S',
			'$data_sistema',
			'$nomeusuario',
			null,
			null,
			0,
			null,
			null,
			'$codigo_pasto',
			null,
			null,
			'$numero_movimentacao',
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

		for($i=0; $i < $quantidade_itens; $i++) {
	   		$tabela_itens = $matriz_itens[$i];
    		$itens = explode("|", $tabela_itens);
			$categoria = $itens[0];
			$sexo = $itens[2];
			$qtd = $itens[5];
			$peso = $itens[11];

			$peso_total = $peso*$qtd;
			$peso_total_arroba = $peso_total/30;
			$peso_medio = $peso;
			$peso_medio_arroba = $peso_medio/30;

	    	if ($sexo=='M') {
			    $qtd_animais_macho[$categoria]+=$qtd;
			    $peso_animais_macho[$categoria]+=$peso*$qtd;
			}
			else {
				$qtd_animais_femea[$categoria]+=$qtd;
				$peso_animais_femea[$categoria]+=$peso*$qtd;
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
		            '$data_movimentacao',
		            null,
		            null,
		            '$peso_total',
		            '$sexo',
		            null,
		            null,
		            null,
		            null,
		            null,
		            '$categoria',
		            '$peso_medio',
		            '$peso_total_arroba',
		            '$peso_medio_arroba',
					'$qtd',
					0
		    )";
		    $resultado = mysqli_query($conector,$sql);
		}
	}

	for($i=0; $i < $quantidade_itens; $i++) {
   		$tabela_itens = $matriz_itens[$i];

   		$itens = explode("|", $tabela_itens);
		$categoria = $itens[0];
		$idade = $itens[1];
		$sexo = $itens[2];
		$raca = $itens[3];
		$pelagem = $itens[4];
		$qtd = $itens[5];
		$sequencia = $itens[6];
		$alfa = $itens[7];
		$peso = $itens[11];

	   	$incluir_animal = gravar_movimento_animal($conector, $categoria, $idade, $sexo, $raca, $pelagem, $qtd, $sequencia, $alfa, $local_origem, $local_destino, $numero_movimentacao, $data_movimentacao, $peso);
	}

	// AJUSTA DIAS COM ANIMAIS NO PASTO SE O PASTO ESTIVER VAZIO
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

		   if (!$resultado){
		        header('Content-type: application/json');
		        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
		        exit;
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

			    if (!$resultado){
			        header('Content-type: application/json');
			        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
			        exit;
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

			    if (!$resultado){
			        header('Content-type: application/json');
			        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
			        exit;
			    } 
			}
	    }
	}

	// GRAVAR A MEDIA POR CATEGORIA E PESAGEM
	if ($controle_estoque=='L') {

	    for ($i=1; $i <=5 ; $i++) { 
	       	$categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

	       	if ($peso_animais_macho[$categoria] !=0 && 
	       		$qtd_animais_macho[$categoria] !=0) {

	       		// Pega ultima quantidade de animais e ultimo peso total
			    $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
					WHERE tbl_pm_local_id='$local_destino' AND 
						  tbl_pm_categoria_id='$categoria' AND 
						  tbl_pm_sexo='M'");

				$num_rows_media = mysqli_num_rows($tbl_media);

				if ($num_rows_media!=0){
					$reg_media = mysqli_fetch_object($tbl_media);
	                $id_media = $reg_media->tbl_pm_id;
					$qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
					$peso_anterior = $reg_media->tbl_pm_peso_total_atual;
					$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
				}
				else {
					$qtd_anterior=0;
					$peso_anterior=0;
					$peso_medio_anterior=0;
				}
				// Fim ultima quantidade de animais e ultimo peso total 
	       		
	       		// Calcula a media atual e grava no banco de dados
	        	$peso_medio_atual = ($peso_animais_macho[$categoria] + $peso_anterior) /
	        	         ($qtd_animais_macho[$categoria] + $qtd_anterior);

	        	$qtd_animais_atual = $qtd_animais_macho[$categoria] + $qtd_anterior;
	        	$peso_total_atual = $peso_animais_macho[$categoria] + $peso_anterior;

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
						'$categoria',
						'M',
				        '$local_destino',
				        '$data_movimentacao',
				        '$qtd_animais_atual',
						'$peso_medio_atual',
						'$peso_total_atual'
					)";
	        	}
	        	else {
	        	   if ($peso_medio_anterior!=$peso_medio_atual) {
		               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
		               		tbl_pm_data='$data_movimentacao',
		                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
		                    tbl_pm_peso_medio_atual='$peso_medio_atual',
		                    tbl_pm_peso_total_atual='$peso_total_atual'
		                  	WHERE tbl_pm_id ='$id_media'");
	        	   }
	        	   else {
		               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
		                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
		                    tbl_pm_peso_medio_atual='$peso_medio_atual',
		                    tbl_pm_peso_total_atual='$peso_total_atual'
		                  	WHERE tbl_pm_id ='$id_media'");
	        	   }
	        	}

			    $resultado = mysqli_query($conector,$sql);
	       	}

        	if ($peso_animais_femea[$categoria] !=0 && 
        		$qtd_animais_femea[$categoria] !=0) {

	       		// Pega ultima quantidade de animais e ultimo peso total
			    $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
					WHERE tbl_pm_local_id='$local_destino' AND 
						  tbl_pm_categoria_id='$categoria' AND 
						  tbl_pm_sexo='F'");

				$num_rows_media = mysqli_num_rows($tbl_media);

				if ($num_rows_media!=0){
					$reg_media = mysqli_fetch_object($tbl_media);
					$id_media = $reg_media->tbl_pm_id;
					$qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
					$peso_anterior = $reg_media->tbl_pm_peso_total_atual;
					$peso_medio_anterior = $reg_media->tbl_pm_peso_medio_atual;
				}
				else {
					$qtd_anterior=0;
					$peso_anterior=0;
					$peso_medio_anterior=0;
				}
				// Fim ultima quantidade de animais e ultimo peso total 
	       		
	       		// Calcula a media atual e grava no banco de dados
	        	$peso_medio_atual = ($peso_animais_femea[$categoria] + $peso_anterior) /
	        	         ($qtd_animais_femea[$categoria] + $qtd_anterior);

	        	$qtd_animais_atual = $qtd_animais_femea[$categoria] + $qtd_anterior;
	        	$peso_total_atual = $peso_animais_femea[$categoria] + $peso_anterior;

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
						'$categoria',
						'F',
				        '$local_destino',
				        '$data_movimentacao',
				        '$qtd_animais_atual',
						'$peso_medio_atual',
						'$peso_total_atual'
					)";
		        }
		        else {
	        	   if ($peso_medio_anterior!=$peso_medio_atual) {
		               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
		               		tbl_pm_data='$data_movimentacao',
		                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
		                    tbl_pm_peso_medio_atual='$peso_medio_atual',
		                    tbl_pm_peso_total_atual='$peso_total_atual'
		                  	WHERE tbl_pm_id ='$id_media'");
	        	   }
	        	   else {
		               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
		                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
		                    tbl_pm_peso_medio_atual='$peso_medio_atual',
		                    tbl_pm_peso_total_atual='$peso_total_atual'
		                  	WHERE tbl_pm_id ='$id_media'");
	        	   }
		        }

			    $resultado = mysqli_query($conector,$sql);
        	}
        }
 	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Movimentação incluída com sucesso.', 'id_pasto' => $id_pasto, 'descricao_pasto' => $descricao_pasto, 'descricao_lote' => $descricao_lote));
	mysqli_close($conector);
	exit;


    function gravar_movimento_animal($conector, $categoria, $idade, $sexo, $raca, $pelagem, $qtd, $sequencia, $alfa, $local_origem, $local_destino, $numero_movimentacao, $data_movimentacao, $peso) {

       	$data_sistema = date("Y-m-d H:i:s");

		@ session_start(); 
		$nomeusuario = $_SESSION['nome_usuario'];
	    $controle_estoque = $_SESSION['controle_estoque'];

		$inicio=date("Y-m-d");
		$data_termino = new DateTime($inicio);
		$data_termino->sub(new DateInterval('P'.$idade.'M'));
		$data_nascimento=$data_termino->format('Y-m-d');
 
 		//$peso_unitario = $peso/$qtd;
 		$peso_unitario = $peso;

 		for ($i=0; $i < $qtd; $i++) { 
 			if ($controle_estoque=='I') {
				do {
					$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
					    WHERE tbl_animal_codigo_numerico='$sequencia' AND 
					          tbl_animal_codigo_alfa='$alfa'");
					$num_rows = mysqli_num_rows($tbl_animal);	

					if ($num_rows==1) {
						$sequencia++;
					}

				} while ($num_rows==1);

			    $sql = "INSERT INTO tbl_animais (
					tbl_animal_codigo_alfa,
					tbl_animal_codigo_numerico,
					tbl_animal_nome,
					tbl_animal_data_nascimento,
					tbl_animal_sexo,
					tbl_animal_grau_sangue,
					tbl_animal_codigo_mae,
					tbl_animal_nome_mae,
					tbl_animal_codigo_pai,
					tbl_animal_nome_pai,
					tbl_animal_primeiro_peso,
					tbl_animal_lote_primeiro_peso,
					tbl_animal_data_primeiro_peso,
					tbl_animal_peso_desmama,
					tbl_animal_lote_desmama,
					tbl_animal_ultimo_peso,
					tbl_animal_lote_ultimo,
					tbl_animal_data_ultimo,
					tbl_animal_data_desmama,
					tbl_animal_codigo_raca,
					tbl_animal_codigo_fazenda,
					tbl_animal_codigo_pelagem,
					tbl_animal_codigo_origem,
					tbl_animal_marca,
					tbl_animal_registro_ren,
					tbl_animal_registro_rgd,
					tbl_animal_registro_sisbov,
					tbl_animal_certificadora,
					tbl_animal_observacao,
					tbl_animal_ativo,
					tbl_animal_incluido_em,
					tbl_animal_incluido_por,
					tbl_animal_alterado_em,
					tbl_animal_alterado_por,
					tbl_animal_lixeira,
					tbl_animal_lixeira_em,
					tbl_animal_lixeira_por,
					tbl_animal_baixado_em,
					tbl_animal_baixado_por,
					tbl_animal_situacao,
					tbl_animal_codigo_origem_anterior,
					tbl_animal_codigo_fazenda_anterior,
					tbl_animal_movimentacao_compra,
					tbl_animal_data_compra
			        ) VALUES (
					'$alfa',
					'$sequencia',
					null,
					'$data_nascimento',
					'$sexo',
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					'$peso_unitario',
					null,
					'$data_sistema',
					null,
					'$raca',
					'$local_destino',
					'$pelagem',
					'$local_origem',
					null,
					null,
					null,
					null,
					null,
					null,
					'S',
					'$data_sistema',
					'$nomeusuario',
					null,
					null,
					0,
					null,
					null,
					null,
					null,
					'',
					0,
					0,
					'$numero_movimentacao',
					'$data_movimentacao'
			    )";

		    	$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);
				$sequencia++;

				if (!$resultado){
				  	header('Content-type: application/json');
				   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do animal.' . $erro_mysql));
					mysqli_close($conector);
					exit;
				} 

				$id_animal = mysqli_insert_id($conector);
				$id_animal = str_pad($id_animal, 9, "0", STR_PAD_LEFT);
 			}
 			else {
				$id_animal = 0;
				$id_animal = str_pad($id_animal, 9, "0", STR_PAD_LEFT);
 			}

			$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
			    WHERE tbl_pasto_codigo_local='$local_destino' AND 
			          tbl_pasto_modulo=999 AND 
			          tbl_pasto_tipo_curral='E'");

			$num_rows = mysqli_num_rows($tbl_pasto);	
			$codigo_pasto = 0;

			if ($num_rows!=0) {
				$reg_pasto = mysqli_fetch_object($tbl_pasto);
				$codigo_pasto =  $reg_pasto->tbl_pasto_id;

				$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
				    WHERE tbl_animal_pasto_local ='$local_destino' 
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
					tbl_animal_pasto_categoria,
					tbl_animal_pasto_sexo,
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
					    		'$local_destino',
					  		    '$codigo_pasto',
							    '$numero_item',
							    '$data_nascimento',
							    '$categoria',
							    '$sexo',
							    '$raca',
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
				   	header('Content-type: application/json');
				   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação do animal no pasto de entrada' . $erro_mysql));
					mysqli_close($conector);
					exit;
				}

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
                 tbl_mov_estoque_codigo_movimentacao,
                 tbl_mov_estoque_codigo_pasto,
                 tbl_mov_estoque_codigo_raca,
                 tbl_mov_estoque_codigo_pelagem,
                 tbl_mov_estoque_sexo,
                 tbl_mov_estoque_codigo_categoria,
                 tbl_mov_estoque_primeiro_peso
                ) 
                VALUES ('$id_animal',
                        '$data_movimentacao',
                        '$data_nascimento',
                        '$local_destino',
                        'E',
                        'C',
                        '$local_origem',
                        '$local_destino',
                        '$numero_movimentacao',
                        '$codigo_pasto',
                        '$raca',
                        '$pelagem',
                        '$sexo',
                        '$categoria',
                        '$peso_unitario'
                )";
        
            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
			  	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico entrada compra.' . $erro_mysql));
				mysqli_close($conector);
				exit;
            }

			// Adiciona nascimento no fechamento mensal se a data de nascimento for do mes anterior

			$data_hoje = date("Y-m-d");
			$partes_hoje = explode("-", $data_hoje);
			$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

			$partes_mov = explode("-", $data_movimentacao);
			$anomes_final = $partes_mov[0].$partes_mov[1];
			$diferenca = $anomes_inicial - $anomes_final;

			if ($diferenca!=0) {
				$date = new DateTime($data_movimentacao);
				$date->modify('last day of this month');
				$data_fechamento = $date->format('Y-m-d');

				$tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
	        		WHERE tbl_fechamento_local='$local_destino' AND
	              		  tbl_fechamento_data='$data_fechamento' AND 
	              		  tbl_fechamento_categoria='$categoria' AND 
	              		  tbl_fechamento_sexo='$sexo'");

	    		$num_rows = mysqli_num_rows($tbl_fechamento);    

	    		if ($num_rows!=0) {
	    			$reg = mysqli_fetch_object($tbl_fechamento);
	    			$fechamento_id = $reg->tbl_fechamento_id;
	    			$qtd_fechamento = $reg->tbl_fechamento_qtd;
	    			$peso_fechamento = $reg->tbl_fechamento_peso;

	    			$qtd_fechamento++;
	    			$peso_fechamento+=$peso_unitario;

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
	        		WHERE tbl_fechamento_local='$local_destino' AND
	              		  tbl_fechamento_data='$data_fechamento'");

	    		$num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

	    		if ($num_rows!=0) {
	    			$reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
	    			$fechamento_id = $reg->tbl_fechamento_id;
	    			$peso_compra = $reg->tbl_fechamento_peso_ent_compra;
	    			$peso_final = $reg->tbl_fechamento_peso_final;

	    			$peso_compra+=$peso_unitario;
	    			$peso_final+=$peso_unitario;

					$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
					   		tbl_fechamento_peso_ent_compra='$peso_compra',
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
		}
    }
?>