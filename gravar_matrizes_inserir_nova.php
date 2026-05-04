<?php
// Inserir em um grupo já existente
@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$data_selecao = date("Y/m/d");
$mensagem = 0;

include "conecta_mysql.inc";

$codigo_id_animal = $_POST['codigo_id'];
$codigo_grupo = $_POST['codigo_grupo'];
$tipo_inserir = $_POST['tipo_inserir'];

if ($tipo_inserir==1) { 
	// atualiza o item da cobertura atual colocando como negativo 
	$cobertura_numero_id_atual = $_POST['cobertura_numero_id'];
	$estacao_monta = $_POST['estacao_monta'];
	$local = $_POST['local'];
	$ordem = $_POST['ordem'];
	
    $sql = "UPDATE tbl_item_cobertura SET
        tbl_ite_cobertura_resultado_diagnostico = 'N',
        tbl_ite_cobertura_destino = 'N'
        WHERE tbl_ite_cobertura_numero_id = $cobertura_numero_id_atual AND 
        	  tbl_ite_cobertura_numero_item = $ordem";
	    $resultado = mysqli_query($conector, $sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do item na cobertura. '. $erro_mysql));
		   	mysqli_close($conector);
			exit;
		} 

    // pega o id da cobertura do grupo que terá o animal incluido    
	$sql = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_codigo_grupo = '$codigo_grupo' AND 
              tbl_cobertura_codigo_local = '$local' AND 
              tbl_cobertura_codigo_estacao_monta = '$estacao_monta'");

    $num_rows_cobertura = mysqli_num_rows($sql); 

    if ($num_rows_cobertura!=0) {
	    $reg_cobertura = mysqli_fetch_object($sql);
	    $cobertura_numero_id = $reg_cobertura->tbl_cobertura_id;
    }
    else {
    	$cobertura_numero_id = 0;
    }
}
else {
	$cobertura_numero_id = $_POST['cobertura_numero_id'];
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_id ='$codigo_id_animal'");
$reg_animal = mysqli_fetch_object($tbl_animal); 
								
$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;

if ($codigo_alfa==''){
	$codigo_edi = $codigo_numerico; 
}
else {
	$codigo_edi = $codigo_alfa.'-'.$codigo_numerico; 
}

// Se for inserir em um novo grupo, então tem que inserir a nova cobertura
if ($cobertura_numero_id==0) {
	// pega os dados da cobertura atual do animal antes de criar a nova cobertura

	$sql = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
        WHERE tbl_cobertura_lixeira=0 AND 
              tbl_cobertura_id = $cobertura_numero_id_atual");

    $reg_cobertura_atual = mysqli_fetch_object($sql);
    $filtros = $reg_cobertura_atual->tbl_cobertura_filtros;
    $vacas_paridas = $reg_cobertura_atual->tbl_cobertura_filtro_vacas_paridas;
    $data_paridas = $reg_cobertura_atual->tbl_cobertura_filtro_data_paridas;

    if ($data_paridas=='') {
        $data_paridas = null;
    }

    $vacas_solteiras = $reg_cobertura_atual->tbl_cobertura_filtro_vacas_solteiras;
    $novilhas = $reg_cobertura_atual->tbl_cobertura_filtro_novilhas;
    $idade_de = $reg_cobertura_atual->tbl_cobertura_filtro_idade_de;
    $idade_ate = $reg_cobertura_atual->tbl_cobertura_filtro_idade_ate;
    $peso_acima = $reg_cobertura_atual->tbl_cobertura_filtro_peso_acima;

    // Insere a cobertura com o novo grupo
    $controle = 'C';
    $protocolo = null;
    $qtd_animais = 0;
    $alterado_em = null;
    $alterado_por = null;
    $lixeira = 0;
    $lixeira_em = null;
    $lixeira_por = null;
    $cobertura_encerrada = null;

    $sql = "INSERT INTO tbl_cobertura (
        tbl_cobertura_controle,
        tbl_cobertura_data,
        tbl_cobertura_codigo_local,
        tbl_cobertura_codigo_grupo,
        tbl_cobertura_codigo_estacao_monta,
        tbl_cobertura_protocoloiatf,
        tbl_cobertura_qtd_animais,
        tbl_cobertura_filtros,
        tbl_cobertura_incluido_em,
        tbl_cobertura_incluido_por,
        tbl_cobertura_alterado_em,
        tbl_cobertura_alterado_por,
        tbl_cobertura_lixeira,
        tbl_cobertura_lixeira_em,
        tbl_cobertura_lixeira_por,
        tbl_cobertura_filtro_vacas_paridas,
        tbl_cobertura_filtro_data_paridas,
        tbl_cobertura_filtro_vacas_solteiras,
        tbl_cobertura_filtro_novilhas,
        tbl_cobertura_filtro_idade_de,
        tbl_cobertura_filtro_idade_ate,
        tbl_cobertura_filtro_peso_acima,
        tbl_cobertura_encerrada
        ) VALUES (
         ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conector->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssssssssssssssssssss", 
                $controle,
                $data_selecao,
                $local,
                $codigo_grupo,
                $estacao_monta,
                $protocolo,
                $qtd_animais,
                $filtros,
                $data_sistema,
                $nomeusuario,
                $alterado_em,
                $alterado_por,
                $lixeira,
                $lixeira_em,
                $lixeira_por,
                $vacas_paridas,
                $data_paridas,
                $vacas_solterias,
                $novilhas,
                $idade_de,
                $idade_ate,
                $peso_acima,
                $cobertura_encerrada);

        $stmt->execute();
        $stmt->close();
    } 
    else {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o novo grupo de fêmeas. '. $conector->error));
		mysqli_close($conector);
		exit;
    }

	$cobertura_numero_id = mysqli_insert_id($conector);
	$cobertura_numero_id = str_pad($cobertura_numero_id, 9, "0", STR_PAD_LEFT);

	$numero_item = 1;

	$sql = "INSERT INTO tbl_item_cobertura (
			tbl_ite_cobertura_numero_id,
			tbl_ite_cobertura_numero_item,
			tbl_ite_cobertura_codigo_id_animal,
			tbl_ite_cobertura_codigo_animal,
			tbl_ite_cobertura_codigo_alfa,
			tbl_ite_cobertura_codigo_numerico,
			tbl_ite_cobertura_data_emissao,
			tbl_ite_cobertura_codigo_touro_semen,
			tbl_ite_cobertura_lote_semen,
			tbl_ite_cobertura_data_diagnostico,
			tbl_ite_cobertura_resultado_diagnostico,
			tbl_ite_cobertura_nome_inseminador,
			tbl_ite_cobertura_destino,
			tbl_ite_cobertura_dia_1,
			tbl_ite_cobertura_dia_2,
			tbl_ite_cobertura_dia_3,
			tbl_ite_cobertura_dia_4,
			tbl_ite_cobertura_dia_5,
			tbl_ite_cobertura_dia_6,
			tbl_ite_cobertura_observacao,
			tbl_ite_cobertura_numero_cobertura
			)
			VALUES ('$cobertura_numero_id', 
			        '$numero_item',
					'$codigo_id_animal',
					'$codigo_edi',
					'$codigo_alfa',
					'$codigo_numerico',
					'$data_selecao',
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
					null,
					null,
					null,
					0
			)";
															   
	$resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do novo grupo de femeas. '. $erro_mysql));
	   	mysqli_close($conector);
		exit;
	}
}
else {
	// Inserir em um grupo já existente
	$tbl_item = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
	    WHERE tbl_ite_cobertura_numero_id ='$cobertura_numero_id' 
	    ORDER BY tbl_ite_cobertura_numero_item DESC LIMIT 1");

	$num_rows_item = mysqli_num_rows($tbl_item);    

	if ($num_rows_item!=0) {
	    $reg_item = mysqli_fetch_object($tbl_item);
	    $numero_item =  $reg_item->tbl_ite_cobertura_numero_item;
	    $numero_item++;
	}
	else {
	    $numero_item = 1;
	}

	$sql = "INSERT INTO tbl_item_cobertura (
						tbl_ite_cobertura_numero_id,
						tbl_ite_cobertura_numero_item,
						tbl_ite_cobertura_codigo_id_animal,
						tbl_ite_cobertura_codigo_animal,
						tbl_ite_cobertura_codigo_alfa,
						tbl_ite_cobertura_codigo_numerico,
						tbl_ite_cobertura_data_emissao,
						tbl_ite_cobertura_codigo_touro_semen,
						tbl_ite_cobertura_lote_semen,
						tbl_ite_cobertura_data_diagnostico,
						tbl_ite_cobertura_resultado_diagnostico,
						tbl_ite_cobertura_nome_inseminador,
						tbl_ite_cobertura_destino,
						tbl_ite_cobertura_dia_1,
						tbl_ite_cobertura_dia_2,
						tbl_ite_cobertura_dia_3,
						tbl_ite_cobertura_dia_4,
						tbl_ite_cobertura_dia_5,
						tbl_ite_cobertura_dia_6,
						tbl_ite_cobertura_observacao,
						tbl_ite_cobertura_numero_cobertura
						)
				VALUES ('$cobertura_numero_id', 
						'$numero_item',
						'$codigo_id_animal',
						'$codigo_edi',
						'$codigo_alfa',
						'$codigo_numerico',
						'$data_selecao',
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
						null,
						null,
						null,
						0
					)";
														   
	$resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
		if ($tipo_inserir==1){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao inserir o item no grupo já existente. '. $erro_mysql));
		   	mysqli_close($conector);
			exit;
		}
		else {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar o item do registro da cobertura. '. $erro_mysql));
		   	mysqli_close($conector);
			exit;
		} 
	}
}

if ($codigo_grupo==999) {
	$sql = "UPDATE tbl_animais SET
	               tbl_animal_descarte_reproducao='S',
			       tbl_animal_descarte_em='$data_sistema',
				   tbl_animal_descarte_por='$nomeusuario'
		  	 WHERE tbl_animal_codigo_id='$codigo_id_animal'";
}
else {
	$sql = "UPDATE tbl_animais SET tbl_animal_selecioanada_reproducao='S'
			WHERE tbl_animal_codigo_id='$codigo_id_animal'";
}

$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal. ' . $erro_mysql));
	mysqli_close($conector);
	exit;
} 

$tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura
    WHERE tbl_cobertura_lixeira=0 AND
          tbl_cobertura_id = '$cobertura_numero_id'");

$num_rows_cobertura = mysqli_num_rows($tbl_cobertura);    

if ($num_rows_cobertura!=0) {
    $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
    $qtd_animais =  $reg_cobertura->tbl_cobertura_qtd_animais;
    $qtd_animais++;

	$sql = "UPDATE tbl_cobertura SET tbl_cobertura_qtd_animais='$qtd_animais'
			WHERE tbl_cobertura_id='$cobertura_numero_id'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do registro da cobertura. ' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 
}

if ($tipo_inserir==1){
	$cobertura_numero_id = $_POST['cobertura_numero_id'];
	$sql = "SELECT * FROM tbl_protocolo_cobertura WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_numero_id'";
	$cob = mysqli_fetch_object(mysqli_query($conector, $sql));
	$array_conta = array(
	    $cobertura_numero_id,
	    $cob->tbl_protocolo_cobertura_codigoiatf
	);
	$array_string = implode('|', $array_conta);

	echo $array_string;
}
else {
	$resposta = array('success' => true, 'message' => 'Fêmea incluída com sucesso.');
	header('Content-type: application/json');
	echo json_encode($resposta);
	mysqli_close($conector);
}

?>