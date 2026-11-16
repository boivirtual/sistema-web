<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_movimentacao_id= $_POST['numero_movimentacao_id'];
	$tem_itens_gravar= $_POST['tem_itens_gravar'];
	$codigo_pesagem= $_POST['codigo_pesagem'];
	$data_movimentacao= $_POST['data_movimentacao'];
	$data_sistema = date("Y-m-d H:i:s");

	$array_itens = $_POST['array_itens'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

	$sql = ("DELETE FROM tbl_item_movimentacao WHERE tbl_ite_movimentacao_numero_id='$numero_movimentacao_id'");
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
		$excluir_mov = $itens[9];

		if ($excluir_mov=='N') {
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
					tbl_ite_movimentacao_motivo_morte

		        ) VALUES (
		            '$numero_movimentacao_id',
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
		            0
			    )";
		    $resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);
		}
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
		$observacao = ltrim($itens[7]);
		$observacao = rtrim($observacao);
		$codigo_id = $itens[8];
		$excluir_mov = $itens[9];

	   	$atualizar_animal = gravar_movimento_animal($conector, $codigo_id, $observacao, $excluir_mov, $numero_movimentacao_id);
	}

	if ($tem_itens_gravar=='N') {
		$sql = ("DELETE FROM tbl_movimentacao 
				  WHERE tbl_movimentacao_id='$numero_movimentacao_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão da movimentação.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		if ($codigo_pesagem!=0) {
			$sql = "UPDATE tbl_pesagem SET
		   			tbl_pesagem_codigo_movimentacao=0
			    WHERE tbl_pesagem_id ='$codigo_pesagem'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização da pesagem.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			} 
		}

	    $resposta = array('success' => true, 'message' => 'Movimentação excluida com sucesso.');
		$erro_mysql = mysqli_error($conector);

		header('Content-type: application/json');
		echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}

    $resposta = array('success' => true, 'message' => 'Movimentação alterada com sucesso.');
	$erro_mysql = mysqli_error($conector);

	header('Content-type: application/json');
	echo json_encode($resposta);
	mysqli_close($conector);
	exit;


    function gravar_movimento_animal($conector, $codigo_id, $observacao, $excluir_mov, $numero_movimentacao_id) {

       	$data_movimentacao_animal = date("Y-m-d H:i:s");
		@ session_start(); 
		$nomeusuario = $_SESSION['nome_usuario'];

		$rs = mysqli_query($conector, "SELECT * FROM tbl_animais
              WHERE tbl_animal_codigo_id='$codigo_id'");

    	$num_rows = mysqli_num_rows($rs);
    	if ($num_rows!=0) {
        	$reg_animal = mysqli_fetch_object($rs);
        	$codigo_fazenda_anterior = $reg_animal->tbl_animal_codigo_fazenda_anterior;
        	$codigo_origem_anterior = $reg_animal->tbl_animal_codigo_origem_anterior;
        	$situacao = $reg_animal->tbl_animal_situacao;
    	}

        if ($excluir_mov=='N') {
			$sql = "UPDATE tbl_animais SET tbl_animal_observacao='$observacao'
			         WHERE tbl_animal_codigo_id='$codigo_id'";
        }
        else if ($situacao=='T'){
			$sql = "UPDATE tbl_animais SET tbl_animal_observacao='$observacao',
			                               tbl_animal_ativo='S',
			                               tbl_animal_situacao='',
			                               tbl_animal_observacao='',
			                               tbl_animal_baixado_em=null,
			                               tbl_animal_baixado_por=null
			         WHERE tbl_animal_codigo_id='$codigo_id'";
        }
        else {
			// em 19/08/2025 deixamos de atualizar a Origem (tbl_animal_codigo_origem)no Cadastro

			/*$sql = "UPDATE tbl_animais SET tbl_animal_observacao='$observacao',
			                               tbl_animal_ativo='S',
			                               tbl_animal_situacao='',
			                               tbl_animal_observacao='',
			                               tbl_animal_baixado_em=null,
			                               tbl_animal_baixado_por=null,
			                               tbl_animal_codigo_fazenda='$codigo_fazenda_anterior',
			                               tbl_animal_codigo_origem='$codigo_origem_anterior'
			         WHERE tbl_animal_codigo_id='$codigo_id'";*/

			$sql = "UPDATE tbl_animais SET tbl_animal_observacao='$observacao',
			                               tbl_animal_ativo='S',
			                               tbl_animal_situacao='',
			                               tbl_animal_observacao='',
			                               tbl_animal_baixado_em=null,
			                               tbl_animal_baixado_por=null,
			                               tbl_animal_codigo_fazenda='$codigo_fazenda_anterior',
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

		if ($excluir_mov=='S'){
			$sql = ("DELETE FROM tbl_movimentacao_estoque 
		  			WHERE tbl_mov_estoque_codigo_movimentacao='$numero_movimentacao_id' and  tbl_mov_estoque_codigo_id_animal='$codigo_id'");
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			  	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão da movimentação do estoque.' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
		}
    }
?>