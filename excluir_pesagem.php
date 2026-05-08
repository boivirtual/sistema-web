<?php
	// Alterar dos dados dos campos tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido em 22/04/2026

	include "conecta_mysql.inc";

	$documento = $_REQUEST['id'];

	$sql = ("DELETE FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id ='$documento'");
	$resultado = mysqli_query($conector,$sql);
	$mysql_erro = mysqli_error($conector);

	if (!$resultado){
    	$valor[0]=9;
       	$valor[1]='Erro ao excluir o itens da pesagem! ' . $mysql_erro;
       	$str = $valor[0] . '<|>' . $valor[1] . '<|>';
       	die ($str);
	}

	$sql = ("DELETE FROM tbl_pesagem WHERE tbl_pesagem_id='$documento'");
	$resultado = mysqli_query($conector,$sql);
	$mysql_erro = mysqli_error($conector);

	if (!$resultado){
    	$valor[0]=9;
       	$valor[1]='Erro ao excluir o registro da pesagem! ' . $mysql_erro;
       	$str = $valor[0] . '<|>' . $valor[1] . '<|>';
       	die ($str);
	}

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
		$valor[0]=9;
		$valor[1]='Ocorreu um erro limpar os campos dos itens repetidos na tbl_item_pesagem -  ' . $erro_mysql;
		$str = $valor[0] . '<|>' . $valor[1] . '<|>';
		die ($str);
	} 

	// REFAZ A MESAGEM DOS ITENS REPETIDOS tbl_ite_pesagem_mens_repetido e tbl_ite_pesagem_id_repetido
	foreach ($array_animais as $idAnimal) {
       	$recalcular = recalcularItensRepetidosPorAnimal($idAnimal, $conector);

       	if ($recalcular != true) {
			$valor[0]=9;
			$valor[1]='Ocorreu ao recalcular itens repetidos.';
			$str = $valor[0] . '<|>' . $valor[1] . '<|>';
			die ($str);
       	}
   	}

    $valor[0]=0;
    $valor[1]='Registro excluido com sucesso! ';
    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
    die ($str);

	mysqli_close($conector);

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