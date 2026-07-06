<?php 
	// Grava Nova Pesagem com o Novo Motivo gerado pelo programa form_pesagem_animais_editar_online.php
	// Conforme os ajustes incluidos no Trello quadro 'AJUSTE DO PROGRAMA DE PESAGEM PARA FINALIZAR PESAGEM DO APP' cheklist 'AJUSTE REUNIÃO 15/04/2026' em 22/04/2026 

	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
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
	$total_pesados= $_POST['total_pesados'];
	$peso_total_kg= $_POST['peso_total_kg'];
	$peso_total_arroba= $_POST['peso_total_arroba'];
	$peso_medio_kg= $_POST['peso_medio_kg'];
	$peso_medio_arroba= $_POST['peso_medio_arroba'];

	$data_sistema = date("Y-m-d H:i:s");

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
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a nova pesagem. '. $erro_mysql));
	   	mysqli_close($conector);
		exit;
	} 

	$numero_pesagem = mysqli_insert_id($conector);
	$numero_pesagem = str_pad($numero_pesagem, 9, "0", STR_PAD_LEFT);

	$resposta = array('success' => true, 'message' => 'Pesagem incluída com sucesso.');

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
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens da nova pesagem. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
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

	header('Content-type: application/json');
	echo json_encode($resposta);
	mysqli_close($conector);
	exit;
	
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