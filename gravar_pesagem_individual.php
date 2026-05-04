<?php 
	// Grava pesagem digitada no programa de inclui pesagem on-line
	// Conforme os ajustes incluidos no Trello item 'AJUSTES PARA PESAGEM ON-LINE E LISTA DO EXCEL' em 13/09/2024 esse programa deixou de ajustar o peso no cadastro de animais, isso será feito ao finalizar a pesagem on-line

	// O campo tipo_registro foi incluido na tabela 'tbl_pesagem' 13/09/2024 para identificar se foi pesagem on-line ou off-line

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

	    $sql = "UPDATE tbl_pesagem SET
			tbl_pesagem_codigo_local='$local',
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
	    $resposta = array('success' => true, 'message' => 'Pesagem incluída com sucesso.' , 'numero_doc' => $numero_pesagem_id);
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

            $tbl_animal = mysqli_query($conector, "select * from tbl_animais 
            	where tbl_animal_codigo_id='$codigo_id'");
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
					tbl_ite_pesagem_qtd_animais
		        ) VALUES (
		            '$numero_pesagem_id',
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
		            1
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

		/*for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$peso = $itens[1];
			$codigo_id = $itens[8];

		   	$atualizar_animal = gravar_peso_animal($conector, $epoca_pesagem, $descricao_lote, $codigo_id, $peso, $data_pesagem);
		}*/

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
			tbl_pesagem_tipo_registro
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
			'$tipo_registro'
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
					tbl_ite_pesagem_qtd_animais
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
		            1
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
		
		/*for($i=0; $i < $quantidade_itens; $i++) {
    		$tabela_itens = $matriz_itens[$i];

    		$itens = explode("|", $tabela_itens);
			$peso = $itens[1];
			$codigo_id = $itens[8];

		   	$atualizar_animal = gravar_peso_animal($conector, $epoca_pesagem, $descricao_lote, $codigo_id, $peso, $data_pesagem);
		}*/

	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}

    /*function gravar_peso_animal($conector, $epoca_pesagem, $descricao_lote, $codigo_id, $peso, $data_pesagem) {

       //$data_alteracao = date("Y-m-d H:i:s");

        if ($epoca_pesagem==1) {
		    $sql = "UPDATE tbl_animais SET
				tbl_animal_primeiro_peso='$peso',
				tbl_animal_lote_primeiro_peso='$descricao_lote',
				tbl_animal_data_primeiro_peso='$data_pesagem',
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
    }*/
?>