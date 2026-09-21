<?php 
$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_animal'];
$alfa = $_POST['alfa_animal'];
$numero = $_POST['codigo_numerico_animal'];
$raca = $_POST['raca_id'];
$pelagem = $_POST['pelagem_id'];
$data_nascimento = $_POST['nascimento_animal'];

if ($tipo_gravacao==0) {
	$sexo = $_POST['sexo_animal'];
}

if(!isset($_POST["reprodutor"])){
	$reprodutor = '';
}else{
	$reprodutor = 'S';
}

$local = $_POST['local_id'];
$grau_sangue = $_POST['grau_sangue_animal'];
$origem = $_POST['origem_id'];
$nome_registro = $_POST['nome_registro_animal'];
$ren = $_POST['ren_animal'];
$rgd = $_POST['rgd_animal'];
$sisbov = $_POST['sisbov_animal'];
$certificadora = $_POST['certificadora_animal'];
$codigo_pai = $_POST['codigo_pai_animal'];
$nome_pai = $_POST['nome_pai_animal'];
$codigo_mae = $_POST['codigo_mae_animal'];
$nome_mae = $_POST['nome_mae_animal'];
$observacao = $_POST['observacao_animal'];
$data_sistema = date("Y-m-d H:i:s");

if (isset($_POST['observacao_animal'])) {
	$observacao_animal = $_POST['observacao_animal'];
}
else {
	$observacao = '';	
}

/*if(!isset($_POST["descarte_reproducao"])){
	$descarte_reproducao = '';
}
else{
	$descarte_reproducao = 'S';
}*/

if(!isset($_POST["estacaoMonta"])){
	$estacao_monta = 'N';
}
else{
	$estacao_monta = $_POST["estacaoMonta"];
}

if(!isset($_POST["situacao_atual"])){
	$parida = 'N';
	$solteira = 'N';
}
else if ($_POST["situacao_atual"]=='P'){
	$parida = 'S';
	$solteira = 'N';
}
else {
	$parida = 'N';
	$solteira = 'S';

}

$num_coberturas = $_POST["num_coberturas"];
$num_partos = $_POST["num_partos"];
$num_abortos = $_POST["num_abortos"];

if (empty($numero)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Código Numérico.'));
	exit;
}

if ($data_nascimento==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Data de Nascimento.'));
	exit;
}

if (empty($raca)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Raça.'));
	exit;
}

if ($local=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Local.'));
	exit;
}

/*
if (empty($_POST['primeiro_peso_animal'])) {
	$primeiro_peso = 0.000;
}
else {
	$primeiro_peso = str_replace(',','.', str_replace('.','', $_POST['primeiro_peso_animal']));
}

if (empty($_POST['peso_desmama_animal'])) {
	$peso_desmama = 0.000;
}
else {
	$peso_desmama = str_replace(',','.', str_replace('.','', $_POST['peso_desmama_animal']));
}

if (empty($_POST['ultimo_peso_animal'])) {
	$ultimo_peso = 0.000;
}
else {
	$ultimo_peso = str_replace(',','.', str_replace('.','', $_POST['ultimo_peso_animal']));
}
*/

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_animais SET 
	                   tbl_animal_lixeira=1,
	                   tbl_animal_lixeira_em='$data_sistema',
	                   tbl_animal_lixeira_por='$nomeusuario'
	                   WHERE tbl_animal_codigo_id='$codigo'";
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
		$sql = "UPDATE tbl_animais SET 
	                   tbl_animal_lixeira=0,
	                   tbl_animal_lixeira_em=null,
	                   tbl_animal_lixeira_por=null
	                   WHERE tbl_animal_codigo_id='$codigo'";
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
	/*if ($descarte_reproducao=='S') {
		$sql = ("UPDATE tbl_animais SET 
			  		tbl_animal_codigo_alfa='$alfa',
					tbl_animal_codigo_numerico='$numero',
					tbl_animal_nome='$nome_registro',
					tbl_animal_data_nascimento='$data_nascimento',
					tbl_animal_grau_sangue='$grau_sangue',
					tbl_animal_codigo_mae='$codigo_mae',
					tbl_animal_nome_mae='$nome_mae',
					tbl_animal_codigo_pai='$codigo_pai',
					tbl_animal_nome_pai='$nome_pai',
					tbl_animal_codigo_raca='$raca',
					tbl_animal_codigo_fazenda='$local',
					tbl_animal_codigo_pelagem='$pelagem',
					tbl_animal_codigo_origem='$origem',
					tbl_animal_codigo_origem_anterior='$origem',
					tbl_animal_registro_ren='$ren',
					tbl_animal_registro_rgd='$rgd',
					tbl_animal_registro_sisbov='$sisbov',
					tbl_animal_certificadora='$certificadora',
					tbl_animal_observacao='$observacao',
					tbl_animal_alterado_em='$data_sistema',
					tbl_animal_alterado_por='$nomeusuario',
					tbl_animal_em_estacao_monta='$estacao_monta',
					tbl_animal_parida='$parida',
					tbl_animal_solteira='$solteira',
					tbl_animal_numero_coberturas='$num_coberturas',
					tbl_animal_numero_partos='$num_partos',
					tbl_animal_numero_abortos='$num_abortos',
					tbl_animal_descarte_reproducao='$descarte_reproducao',
					tbl_animal_descarte_em='$data_sistema',
					tbl_animal_descarte_por='$nomeusuario',
					tbl_animal_reprodutor='$reprodutor'
	 		WHERE tbl_animal_codigo_id='$codigo'");
	}
	else {*/
		// No dia 25/02/2025, os campos CODIGO ALFA, CODIGO NUMERICO, DATA DO NASCIMENTO E FAZENDA forem retirados da alteração por causarem erro no mapa de gados e no estoque
		$sql = ("UPDATE tbl_animais SET 
					tbl_animal_nome='$nome_registro',
					tbl_animal_grau_sangue='$grau_sangue',
					tbl_animal_codigo_mae='$codigo_mae',
					tbl_animal_nome_mae='$nome_mae',
					tbl_animal_codigo_pai='$codigo_pai',
					tbl_animal_nome_pai='$nome_pai',
					tbl_animal_codigo_raca='$raca',
					tbl_animal_codigo_pelagem='$pelagem',
					tbl_animal_codigo_origem='$origem',
					tbl_animal_codigo_origem_anterior='$origem',
					tbl_animal_registro_ren='$ren',
					tbl_animal_registro_rgd='$rgd',
					tbl_animal_registro_sisbov='$sisbov',
					tbl_animal_certificadora='$certificadora',
					tbl_animal_observacao='$observacao',
					tbl_animal_alterado_em='$data_sistema',
					tbl_animal_alterado_por='$nomeusuario',
					tbl_animal_em_estacao_monta='$estacao_monta',
					tbl_animal_parida='$parida',
					tbl_animal_solteira='$solteira',
					tbl_animal_numero_coberturas='$num_coberturas',
					tbl_animal_numero_partos='$num_partos',
					tbl_animal_numero_abortos='$num_abortos',
					tbl_animal_reprodutor='$reprodutor'
	 		WHERE tbl_animal_codigo_id='$codigo'");
	//}

	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração ' . $erro_mysql));
	} 
	else {
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
	}

	mysqli_close($conector);
	exit;
}
else{
	if (empty($data_nascimento)) {
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
				tbl_animal_peso_desmama,
				tbl_animal_ultimo_peso,
				tbl_animal_codigo_raca,
				tbl_animal_codigo_fazenda,
				tbl_animal_codigo_fazenda_anterior,
				tbl_animal_codigo_pelagem,
				tbl_animal_codigo_origem,
				tbl_animal_codigo_origem_anterior,
				tbl_animal_marca,
				tbl_animal_registro_ren,
				tbl_animal_registro_rgd,
				tbl_animal_registro_sisbov,
				tbl_animal_certificadora,
				tbl_animal_observacao,
				tbl_animal_incluido_em,
				tbl_animal_incluido_por,
				tbl_animal_alterado_em,
				tbl_animal_alterado_por,
				tbl_animal_lixeira,
				tbl_animal_lixeira_em,
				tbl_animal_lixeira_por,
				tbl_animal_ativo,
				tbl_animal_baixado_em,
				tbl_animal_baixado_por,
				tbl_animal_situacao,
				tbl_animal_reprodutor
	        ) 
		    VALUES (
		    		'$alfa',
		    		'$numero',
		    		'$nome_registro',
		    		null,
		    		'$sexo',
		    		'$grau_sangue',
		            '$codigo_mae',
		    		'$nome_mae',
		            '$codigo_pai',
		    		'$nome_pai',
		    		0,
		    		0,
		    		0,
		            '$raca',
		            '$local',
		            0,
		            '$pelagem',
		            '$origem',
		            0,
		            null,
		            '$ren',
		            '$rgd',
		            '$sisbov',
		            '$certificadora',
		            '$observacao',
	                '$data_sistema',
	                '$nomeusuario',
	                null,
	                null,
	                0,
	                null,
	                null,
	                'S',
	                null,
	                null,
	                '',
	                '$reprodutor'
	        )";
	}
	else {
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
				tbl_animal_peso_desmama,
				tbl_animal_ultimo_peso,
				tbl_animal_codigo_raca,
				tbl_animal_codigo_fazenda,
				tbl_animal_codigo_pelagem,
				tbl_animal_codigo_origem,
				tbl_animal_codigo_origem_anterior,
				tbl_animal_marca,
				tbl_animal_registro_ren,
				tbl_animal_registro_rgd,
				tbl_animal_registro_sisbov,
				tbl_animal_certificadora,
				tbl_animal_observacao,
				tbl_animal_incluido_em,
				tbl_animal_incluido_por,
				tbl_animal_alterado_em,
				tbl_animal_alterado_por,
				tbl_animal_lixeira,
				tbl_animal_lixeira_em,
				tbl_animal_lixeira_por,
				tbl_animal_ativo,
				tbl_animal_baixado_em,
				tbl_animal_baixado_por,
				tbl_animal_situacao,
				tbl_animal_reprodutor
	        ) 
		    VALUES (
		    		'$alfa',
		    		'$numero',
		    		'$nome_registro',
		    		'$data_nascimento',
		    		'$sexo',
		    		'$grau_sangue',
		            '$codigo_mae',
		    		'$nome_mae',
		            '$codigo_pai',
		    		'$nome_pai',
		    		0,
		    		0,
		    		0,
		            '$raca',
		            '$local',
		            '$pelagem',
		            '$origem',
		            '$origem',
		            null,
		            '$ren',
		            '$rgd',
		            '$sisbov',
		            '$certificadora',
		            '$observacao',
	                '$data_sistema',
	                '$nomeusuario',
	                null,
	                null,
	                0,
	                null,
	                null,
	                'S',
	                null,
	                null,
	                '',
	                '$reprodutor'
	        )";
	}

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
		$id_animal = mysqli_insert_id($conector);
		$id_animal = str_pad($id_animal, 9, "0", STR_PAD_LEFT);

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
		    WHERE tbl_pasto_codigo_local='$local' AND 
		          tbl_pasto_modulo=999 AND 
		          tbl_pasto_tipo_curral='E'");

		$num_rows = mysqli_num_rows($tbl_pasto);	
		$codigo_pasto = 0;

		if ($num_rows!=0) {
			$reg_pasto = mysqli_fetch_object($tbl_pasto);
			$codigo_pasto =  $reg_pasto->tbl_pasto_id;
			//$array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
		    //$array_qtd_macho = explode("!", $reg_pasto->tbl_pasto_array_qtd_animais_macho);
	    	//$array_qtd_femea = explode("!", $reg_pasto->tbl_pasto_array_qtd_animais_femea);

			$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
			    WHERE tbl_animal_pasto_local ='$local' 
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

            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    
			$codigo_categoria = 0;

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    }
                }                   
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
				    		'$local',
				  		    '$codigo_pasto',
						    '$numero_item',
						    '$data_nascimento',
						    '$codigo_categoria',
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

			/*
			for ($j=0; $j<5; $j++) { 
			  	if ($array_categoria[$j]==$codigo_categoria) {

				    if ($array_qtd_macho[$j]=='') {
				    	$qtd_macho = 0;
				    }
				    else {
					    $qtd_macho = intval($array_qtd_macho[$j]);
				    }

				    if ($array_qtd_femea[$j]=='') {
				    	$qtd_femea = 0;
				    }
				    else {
					    $qtd_femea = intval($array_qtd_femea[$j]);
				    }

				    if ($sexo=='F'){
						$qtd_femea++;
						$array_qtd_femea[$j] = $qtd_femea;  	
				    }
				    else {
						$qtd_macho++;
						$array_qtd_macho[$j] = $qtd_macho;  	
				    }

				  	$array_qtd_femea_ajustado = implode("!", $array_qtd_femea);
				   	$array_qtd_macho_ajustado = implode("!", $array_qtd_macho);
					   	
					$sql = ("UPDATE tbl_pasto SET 
   			            tbl_pasto_array_qtd_animais_macho='$array_qtd_macho_ajustado',
						tbl_pasto_array_qtd_animais_femea='$array_qtd_femea_ajustado',
						tbl_pasto_alterado_em='$data_sistema',
						tbl_pasto_alterado_por='$nomeusuario'
						WHERE tbl_pasto_id ='$codigo_pasto'");

					$resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
					 	header('Content-type: application/json');
					   	echo json_encode(array('error' => true, 'message' => 'Erro na alteração do registro no pasto.' . $erro_mysql));
							mysqli_close($conector);
						exit;
					}
					break;
			    }
			} */
		}

		$data_movimentacao=date("Y-m-d");

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
                 tbl_mov_estoque_primeiro_peso
                ) 
                VALUES ('$id_animal',
                        '$data_movimentacao',
                        '$data_nascimento',
                        '$local',
                        'E',
                        'O',
                        '$local',
                        '$local',
                        0,
                        '$codigo_pasto',
                        '$raca',
                        '$pelagem',
                        '$sexo',
                        null
                )";
        
        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico entrada do animal.' . $erro_mysql));
			mysqli_close($conector);
			exit;
        }

	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
	}

	mysqli_close($conector);
	exit;
}

mysqli_close($conector);


?>