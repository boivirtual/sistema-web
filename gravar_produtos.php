<?php 
include "conecta_mysql.inc";

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_produto'];
$modalidade = $_POST['grupo'];
$codigo_padrao = $_POST['descricao_padrao'];
$descricao_complementar = $_POST['descricao_complementar'];
$apresentacao = $_POST['apresentacao'];
$unidade = $_POST['unidade'];
$observacao = $_POST['observacao'];
$data_sistema = date("Y-m-d H:i:s");

if ($codigo_padrao=='000' && $descricao_complementar=='') {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Complemento da Descrição.'));
	exit;
} 

$tbl_padrao = mysqli_query($conector, "select * from tabela_produto_generico where pro_generico_codigo='$codigo_padrao'");
$num_rows = mysqli_num_rows($tbl_padrao);

if ($num_rows!=0){
    $reg = mysqli_fetch_object($tbl_padrao);
    $descricao_padrao = $reg->pro_generico_descricao;
}
else {
    $descricao_padrao = '';
}

$descricao = $descricao_padrao . ' ' . $descricao_complementar;
$descricao = ltrim($descricao);
$descricao = rtrim($descricao);

if ($tipo_gravacao==0 || $tipo_gravacao==1) {
	$array_fazenda= $_POST['array_codigo_fazenda'];
	$array_estoque_atual = $_POST['array_estoque_atual'];

	$array_local = explode("!", $array_fazenda);
	$array_estoque = explode("!", $array_estoque_atual);

	//$qtd_estoque_anterior = $_POST['qtd_estoque_anterior'];

	if (empty($_POST['qtd_uni'])) {
		$qtd_uni = 0;
	}
	else {
		$qtd_uni = str_replace(',','.', str_replace('.','', $_POST['qtd_uni']));
	}

	if (empty($_POST['qtd_entrada'])) {
		$qtd_entrada = 0;
	}
	else {
		$qtd_entrada = str_replace(',','.', str_replace('.','', $_POST['qtd_entrada']));
	}

	//$qtd_estoque_atual = $qtd_estoque_anterior + ($qtd_entrada * $qtd_uni);

	$tbl_produto = mysqli_query($conector, "select * from tbl_produto 
			    where tbl_produto_descricao='$descricao' and 
			          tbl_produto_apresentacao='$apresentacao' and 
			          tbl_produto_qtd_unidade='$qtd_uni' and 
			          tbl_produto_unidade='$unidade' and 
			          tbl_produto_lixeira=0");
	$num_rows = mysqli_num_rows($tbl_produto);

	if ($num_rows!=0){
		if ($tipo_gravacao==0) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Já existe produto cadastrado com essa Descrição, Apresentação, Quantidade/Apresentação e Unidade.'));
				exit;
		}
		else {
			$reg = mysqli_fetch_object($tbl_produto);
	    	$descricao_produto = $reg->tbl_produto_descricao;
	    	$apresentacao_produto = $reg->tbl_produto_apresentacao;
	    	$qtd_apresentacao_produto = $reg->tbl_produto_qtd_unidade;
	    	$unidade_produto = $reg->tbl_produto_unidade;

			$descricao_ant = $_POST['descricao_anterior'];
			$apresentacao_ant = $_POST['apresentacao_anterior'];
			$qtd_ant = $_POST['qtd_anterior'];
			$unidade_ant = $_POST['unidade_anterior'];

			if ($descricao_ant != $descricao_produto ||  
			    $apresentacao_ant != $apresentacao_produto || 
			    $qtd_ant != $qtd_apresentacao_produto ||
			    $unidade_ant != $unidade_produto) {
					header('Content-type: application/json');
					echo json_encode(array('error' => true, 'message' => 'Já existe produto cadastrado com essa Descrição, Apresentação, Quantidade/Apresentação e Unidade.'));
						exit;
			}
		}
	}
}

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_produto SET 
	                   tbl_produto_lixeira=1,
	                   tbl_produto_lixeira_em='$data_sistema',
	                   tbl_produto_lixeira_por='$nomeusuario'
	                   WHERE tbl_produto_codigo_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro enviado para lixeira com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao enviar o registro para a lixeira' . $erro_mysql));
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector);
		exit;
}
else if ($tipo_gravacao==3){
		$sql = "UPDATE tbl_produto SET 
	                   tbl_produto_lixeira=0,
	                   tbl_produto_lixeira_em=null,
	                   tbl_produto_lixeira_por=null
	                   WHERE tbl_produto_codigo_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro removido da lixeira com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao remover o registro da lixeira' . $erro_mysql));
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector);
		exit;
}
else if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_produto SET
				tbl_produto_codigo_generico='$codigo_padrao',
				tbl_produto_complemento_descricao='$descricao_complementar',
	 			tbl_produto_descricao='$descricao',
				tbl_produto_codigo_modalidade='$modalidade',
				tbl_produto_apresentacao='$apresentacao',
				tbl_produto_qtd_unidade='$qtd_uni',
				tbl_produto_unidade='$unidade',
				tbl_produto_observacao='$observacao',
				tbl_produto_alterado_em='$data_sistema',
				tbl_produto_alterado_por='$nomeusuario'
	 		WHERE tbl_produto_codigo_id='$codigo'");

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração ' . $erro_mysql));
	} 
	else {
		$gravar_estoque = gravar_estoque($conector, $tipo_gravacao, $codigo, $array_local, $array_estoque, $data_sistema, $nomeusuario);

		if ($gravar_estoque=='') {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => $gravar_estoque));
			mysqli_close($conector);
			exit;
		}
	}

	mysqli_close($conector);
	exit;
}
else{
	$sql = "INSERT INTO tbl_produto (
			tbl_produto_codigo_generico,
			tbl_produto_complemento_descricao,
			tbl_produto_descricao,
			tbl_produto_codigo_modalidade,
			tbl_produto_apresentacao,
			tbl_produto_qtd_unidade,
			tbl_produto_unidade,
			tbl_produto_codigo_fabricante,
			tbl_produto_referencia_fornecedor,
			tbl_produto_observacao,
			tbl_produto_incluido_em,
			tbl_produto_incluido_por,
			tbl_produto_alterado_em,
			tbl_produto_alterado_por,
			tbl_produto_lixeira,
			tbl_produto_lixeira_em,
			tbl_produto_lixeira_por
	        ) 
		    VALUES (
		    		'$codigo_padrao',
		    		'$descricao_complementar',
		    		'$descricao',
		    		'$modalidade',
		    		'$apresentacao',
		    		'$qtd_uni',
		    		'$unidade',
		    		null,
		            null,
		    		'$observacao',
	                '$data_sistema',
	                '$nomeusuario',
	                null,
	                null,
	                0,
	                null,
	                null
	        )";

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
	else {
		$id_produto = mysqli_insert_id($conector);
		$id_produto = str_pad($id_produto, 9, "0", STR_PAD_LEFT);

		$gravar_estoque = gravar_estoque($conector, $tipo_gravacao, $id_produto, $array_local, $array_estoque, $data_sistema, $nomeusuario);

		if ($gravar_estoque=='') {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => $gravar_estoque));
			mysqli_close($conector);
			exit;
		}
	}

	mysqli_close($conector);
	exit;
}

mysqli_close($conector);

function gravar_estoque($conector, $tipo_gravacao, $id_produto, $array_local, $array_estoque, $data_sistema, $nomeusuario) {

	$mens_erro = '';

	for($i=0; $i < count($array_local); $i++) {
		$codigo_local = $array_local[$i];
		$estoque_atual = $array_estoque[$i];

		if ($estoque_atual=='0,00' || $estoque_atual=='') {
			$estoque_atual = 0;
		}
		else {
			$estoque_atual = str_replace(',','.', str_replace('.','', $estoque_atual));
		}

		if ($tipo_gravacao==0) {
			$sql = "INSERT INTO tbl_produto_estoque (
							tbl_produto_estoque_codigo_id,
							tbl_produto_estoque_codigo_local,
							tbl_produto_estoque_qtd,
							tbl_produto_estoque_atual,
							tbl_produto_estoque_incluido_em,
							tbl_produto_estoque_incluido_por,
							tbl_produto_estoque_alterado_em,
							tbl_produto_estoque_alterado_por,
							tbl_produto_estoque_lixeira,
							tbl_produto_estoque_lixeira_em,
							tbl_produto_estoque_lixeira_por
			        ) 
				    VALUES (
				    		'$id_produto',
				    		'$codigo_local',
				    		null,
				    		'$estoque_atual',
			                '$data_sistema',
			                '$nomeusuario',
			                null,
			                null,
			                0,
			                null,
			                null
			        )";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
				$mens_erro = 'Erro na inclusão do estoque - ' . $erro_mysql;
			} 
		}             // fim tipo gravacao = 0
		else {
            $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_produto_estoque
                WHERE tbl_produto_estoque_codigo_id='$id_produto' AND 
                	  tbl_produto_estoque_codigo_local='$codigo_local' AND 
                      tbl_produto_estoque_lixeira = 0"); 
            $num_rows = mysqli_num_rows($tbl_estoque);

            if ($num_rows!=0) {
				$sql = ("UPDATE tbl_produto_estoque SET
							tbl_produto_estoque_atual='$estoque_atual',
							tbl_produto_estoque_alterado_em='$data_sistema',
							tbl_produto_estoque_alterado_por='$nomeusuario'
				 		WHERE tbl_produto_estoque_codigo_id='$id_produto' AND 
				 		      tbl_produto_estoque_codigo_local='$codigo_local'");

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);
				if (!$resultado){
					$mens_erro = 'Erro na alteração do estoque - ' . $erro_mysql;
				} 
            }
            else {
				$sql = "INSERT INTO tbl_produto_estoque (
								tbl_produto_estoque_codigo_id,
								tbl_produto_estoque_codigo_local,
								tbl_produto_estoque_qtd,
								tbl_produto_estoque_atual,
								tbl_produto_estoque_incluido_em,
								tbl_produto_estoque_incluido_por,
								tbl_produto_estoque_alterado_em,
								tbl_produto_estoque_alterado_por,
								tbl_produto_estoque_lixeira,
								tbl_produto_estoque_lixeira_em,
								tbl_produto_estoque_lixeira_por
				        ) 
					    VALUES (
					    		'$id_produto',
					    		'$codigo_local',
					    		null,
					    		'$estoque_atual',
				                '$data_sistema',
				                '$nomeusuario',
				                null,
				                null,
				                0,
				                null,
				                null
				        )";

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
					$mens_erro = 'Erro na inclusão do estoque - ' . $erro_mysql;
				} 
            }
		}             // fim tipo gravacao = 1
	}                 // fim do for

	return $mens_erro;
}

?>