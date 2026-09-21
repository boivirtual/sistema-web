<?php 
@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_pasto = $_POST['codigo_conta'];
$local = $_POST['codigo_local'];
$modulo = $_POST['modulo'];
$capim = $_POST['capim'];
$descricao = $_POST['descricao'];
$descricao_anterior = $_POST['descricao_anterior'];
//$latitude = $_POST['latitude'];
//$logitude = $_POST['logitude'];

if ($controle_estoque=='I') {
	$array_categoria = '001!002!003!004!005';

	$array_qtd_categoria_macho = $_POST['array_qtd_categoria_macho'];
	$array_qtd_categoria_femea = $_POST['array_qtd_categoria_femea'];

	$array_qtd_macho_anterior = $_POST['array_qtd_macho_anterior'];
	$array_qtd_femea_anterior = $_POST['array_qtd_femea_anterior'];
}
else {
	$array_categoria = '001!002!003!004!005';
	$array_qtd_categoria_macho = '';
	$array_qtd_categoria_femea = '';
	$array_qtd_macho_anterior = '';
	$array_qtd_femea_anterior = ''; 
}


$observacao = $_POST['observacao'];

$data_sistema = date("Y-m-d H:i:s");

if ($local==0){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Local.'));
	exit;
}

if ($modulo==0){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Módulo.'));
	exit;
}

if ($descricao==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Descrição.'));
	exit;
}

if (empty($_POST['area'])){
	$area = 0.00;
}
else {
	$area = str_replace(',','.', str_replace('.','', $_POST['area']));
}

include "conecta_mysql.inc";

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_pasto SET 
	                   tbl_pasto_lixeira=1,
	                   tbl_pasto_lixeira_em='$data_sistema',
	                   tbl_pasto_lixeira_por='$nomeusuario'
	                   WHERE tbl_pasto_id='$codigo_pasto'";
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
		$sql = "UPDATE tbl_pasto SET 
	                   tbl_pasto_lixeira=0,
	                   tbl_pasto_lixeira_em=null,
	                   tbl_pasto_lixeira_por=null
	                   WHERE tbl_pasto_id='$codigo_pasto'";
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
	$sql = ("UPDATE tbl_pasto SET 
		  		tbl_pasto_codigo_local='$local',
				tbl_pasto_descricao='$descricao',
				tbl_pasto_area='$area',
				tbl_pasto_modulo='$modulo',
				tbl_pasto_tipo_capim='$capim',
				tbl_pasto_descricao_lote='$observacao',
				tbl_pasto_alterado_em='$data_sistema',
				tbl_pasto_alterado_por='$nomeusuario'
				WHERE tbl_pasto_id='$codigo_pasto'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração ' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
	else {
		if ($controle_estoque=='I') {
			$gravar_animais_pasto = gravar_animais_pasto($conector, $codigo_pasto, 
			$local, $array_categoria, $array_qtd_categoria_macho, 
			$array_qtd_categoria_femea, $array_qtd_macho_anterior, 
			$array_qtd_femea_anterior);

			if ($gravar_animais_pasto!='Gravei') {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do animal no pasto. ' . $gravar_animais_pasto));
				mysqli_close($conector);
				exit;
			}
		}

		if ($descricao!=$descricao_anterior) {
			$alterar_mapa = alterar_mapa($local, $descricao, $descricao_anterior);

			if ($alterar_mapa=='Alterado') {
			    $resposta = array('success' => true, 'message' => 'Registro alterado com sucesso no Banco de dados e no Mapa do Google.');
			   	header('Content-type: application/json');
			   	echo json_encode($resposta);
				mysqli_close($conector);
				exit;
			}
			else {
			    $resposta = array('success' => true, 'message' => 'Não existe esse Pasto no Mapa do Google para alterar a descrição. Registro alterado com sucesso somente no Banco de dados.' . $alterar_mapa);
			   	header('Content-type: application/json');
			   	echo json_encode($resposta);
				mysqli_close($conector);
				exit;
			}
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
			mysqli_close($conector);
			exit;
		}
	}
}
else{
	$sql = "INSERT INTO tbl_pasto (
    		tbl_pasto_codigo_local,
			tbl_pasto_descricao,
			tbl_pasto_latitude,
			tbl_pasto_longitude,
			tbl_pasto_area,
			tbl_pasto_modulo,
			tbl_pasto_tipo_capim,
			tbl_pasto_descricao_lote,
			tbl_pasto_tipo_curral,
			tbl_pasto_incluido_em,
			tbl_pasto_incluido_por,
			tbl_pasto_alterado_em,
			tbl_pasto_alterado_por,
			tbl_pasto_lixeira,
			tbl_pasto_lixeira_em,
			tbl_pasto_lixeira_por,
			tbl_pasto_array_categoria,
			tbl_pasto_array_qtd_animais_macho,
			tbl_pasto_array_qtd_animais_femea,
			tbl_pasto_array_qtd_animais_ambos,
			tbl_pasto_data_com_animais,
			tbl_pasto_data_com_animais_anterior,
			tbl_pasto_data_sem_animais,
			tbl_pasto_data_sem_animais_anterior
	        ) 
		    VALUES (
		  		    '$local',
				    '$descricao',
				    null,
				    null,
				    '$area',
					'$modulo',
					'$capim',
					'$observacao',
					null,
	                '$data_sistema',
	                '$nomeusuario',
	                null,
	                null,
	                0,
	                null,
	                null,
	                '$array_categoria',
					'!!!!',
					'!!!!',
					null,
					'$data_sistema',
					'$data_sistema',
					'$data_sistema',
					'$data_sistema'
	        )";


	$resultado = mysqli_query($conector,$sql);

	$resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
	} 
	else {
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
	}

	mysqli_close($conector);
	exit;
}

mysqli_close($conector);

function gravar_animais_pasto($conector, $codigo_pasto, $local, 
					$array_categoria, $array_qtd_categoria_macho, $array_qtd_categoria_femea, 
					$array_qtd_macho_anterior, $array_qtd_femea_anterior) {

    $array_categoria = explode("!", $array_categoria);
    $array_qtd_macho = explode("!", $array_qtd_categoria_macho);
    $array_qtd_femea = explode("!", $array_qtd_categoria_femea);

    $array_macho_anterior = explode("!", $array_qtd_macho_anterior);
    $array_femea_anterior = explode("!", $array_qtd_femea_anterior);

	$categoria_alterar = '';	
	$qtd_alterar = 0;
	$mensagem_retorno = '';

    for ($j=0; $j<5; $j++) { 
        if ($array_qtd_macho[$j] != $array_macho_anterior[$j]) {
            $categoria_alterar = $array_categoria[$j];
            $qtd_alterar = $array_qtd_macho[$j] - $array_macho_anterior[$j];

            if ($qtd_alterar < 0) {
            	$qtd_registro = abs($qtd_alterar);

            	$resposta = alterar_animal_pasto($conector, $codigo_pasto, $local, 
            		$qtd_registro, $categoria_alterar, 'M', 'E');

            	if ($resposta!='Gravei') {
            		$mensagem_retorno = $resposta;
            		break;
            	}
            }
            else {
            	$qtd_registro = $qtd_alterar;

            	$resposta = alterar_animal_pasto($conector, $codigo_pasto, $local, 
            		$qtd_registro, $categoria_alterar, 'M', 'I');

            	if ($resposta!='Gravei') {
            		$mensagem_retorno = $resposta;
            		break;
            	}
            }
        }

        if ($array_qtd_femea[$j] != $array_femea_anterior[$j]) {
            $categoria_alterar = $array_categoria[$j];
            $qtd_alterar = $array_qtd_femea[$j] - $array_femea_anterior[$j];

            if ($qtd_alterar < 0) {
            	$qtd_registro = abs($qtd_alterar);

            	$resposta = alterar_animal_pasto($conector, $codigo_pasto, $local, 
            		$qtd_registro, $categoria_alterar, 'F', 'E');

            	if ($resposta!='Gravei') {
            		$mensagem_retorno = $resposta;
            		break;
            	}
            }
            else {
            	$qtd_registro = $qtd_alterar;

            	$resposta = alterar_animal_pasto($conector, $codigo_pasto, $local, 
            		$qtd_registro, $categoria_alterar, 'F', 'I');

            	if ($resposta!='Gravei') {
            		$mensagem_retorno = $resposta;
            		break;
            	}
            }
        }
    }

    if ($mensagem_retorno==''){
    	return 'Gravei';
    }
    else {
    	return $mensagem_retorno;
    }
}

function alterar_animal_pasto($conector, $codigo_pasto, $local, 
            		$qtd_registro, $categoria_alterar, $sexo, $tipo_registro) {

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$data_sistema = date("Y-m-d H:i:s");

	if ($tipo_registro=='E') {
		for ($i=0; $i < $qtd_registro; $i++) { 

	    	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	            WHERE tbl_animal_pasto_local = '$local' AND 
	                  tbl_animal_pasto_id = '$codigo_pasto' AND 
	                  tbl_animal_pasto_categoria = '$categoria_alterar' AND 
	                  tbl_animal_pasto_sexo = '$sexo'");
	    	$num_rows_pasto = mysqli_num_rows($tbl_pasto);

    		if ($num_rows_pasto!=0) {
    			$reg_pasto = mysqli_fetch_object($tbl_pasto);
    			$item_pasto = $reg_pasto->tbl_animal_pasto_numero_item;

			    $sql = ("DELETE FROM tbl_animal_pasto 
			    	           WHERE tbl_animal_pasto_local='$local' AND 
			    	                 tbl_animal_pasto_numero_item='$item_pasto'");
				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
					return $erro_mysql;
					break;
				}
    		}
		}
		return 'Gravei';
	}
	else {
		$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
		    WHERE tbl_animal_pasto_local ='$local' 
		    ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");

		$num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);	

		if ($num_rows_animal_pasto!=0) {
			$reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto);
			$numero_item =  $reg_animal_pasto->tbl_animal_pasto_numero_item;
		}
		else {
			$numero_item = 0;
		}

		for ($i=0; $i < $qtd_registro; $i++) { 
			$numero_item++;

			$sql = "INSERT INTO tbl_animal_pasto (
				tbl_animal_pasto_local,
				tbl_animal_pasto_numero_item,
				tbl_animal_pasto_id,
				tbl_animal_pasto_nascimento,
				tbl_animal_pasto_categoria,
				tbl_animal_pasto_sexo,
				tbl_animal_pasto_raca,
				tbl_animal_pasto_situacao,
				tbl_animal_pasto_motivo_morte,
				tbl_animal_pasto_observacao,
				tbl_animal_pasto_incluido_em,
				tbl_animal_pasto_incluido_por,
				tbl_animal_pasto_alterado_em,
				tbl_animal_pasto_alterado_por,
				tbl_animal_pasto_baixado_em,
				tbl_animal_pasto_baixado_por
		        ) 
			    VALUES (
			  		    '$local',
					    '$numero_item',
					    '$codigo_pasto',
					    null,
					    '$categoria_alterar',
						'$sexo',
						null,
						'A',
						null,
						null,
		                '$data_sistema',
		                '$nomeusuario',
		                null,
		                null,
		                null,
		                null
		        )";
			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
				return $erro_mysql;
				break;
			}
   		}
	}
	return 'Gravei';
}

function alterar_mapa($local, $descricao, $descricao_anterior) {
  	@ session_start(); 
  	$cnpj_cliente = $_SESSION['id_cliente'];

	$arquivo = 'mapa/'.$cnpj_cliente.'/'.$local.'.json';

	$json_data = json_decode(file_get_contents($arquivo));

	foreach ($json_data->features as $data) {
		$array_coordenadas = $data->geometry->coordinates;

		$pasto = mb_strtoupper($data->properties->name, 'UTF-8');

	    if ($pasto==$descricao_anterior) {
	        $data->properties->name=$descricao;

	        $json_str = json_encode($json_data, JSON_UNESCAPED_UNICODE);

	        $file = fopen(__DIR__ . '/' . $arquivo,'w');
	        fwrite($file, $json_str);
	        fclose($file);

	    	return 'Alterado';
			break;
	    }
	}

	return $arquivo;
}
?>