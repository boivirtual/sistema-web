<?php 
include "conecta_mysql.inc";

@ session_start(); 

$nomeusuario = $_SESSION['nome_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];

$tipo_gravacao = $_POST['tipo_gravacao'];
$num_mov_nascimento = $_POST['num_mov_nascimento'];
$codigo_animal_id = $_POST['codigo_animal_id'];
$local = $_POST['local_id'];
$pasto_id = preg_replace('/[^0-9 ]/', '', $_POST['pasto_id']);
$codigo_mae = $_POST['codigo_mae_animal'];

if (isset($_POST['codigo_pai_animal'])) {
	$codigo_pai = $_POST['codigo_pai_animal'];
}
else {
	$codigo_pai = 0;
}

$data_nascimento = $_POST['nascimento_animal'];
$alfa = $_POST['alfa_animal'];
$numero = $_POST['codigo_numerico_animal'];
$raca = $_POST['raca_id'];

if (isset($_POST['data_prenhes'])) {
	$data_prenhes = $_POST['data_prenhes'];

	if ($data_prenhes == '') {
		$data_prenhes = 0;
	}
}
else {
	$data_prenhes = 0;
}

$data_previsao = 0;

if (isset($_POST['dias_nascimento'])) {
	$dias_nascimento = $_POST['dias_nascimento'];

	if ($dias_nascimento == '') {
		$dias_nascimento = 0;
	}
}
else {
	$dias_nascimento = 0;
}

if (isset($_POST['cobertura_id'])) {
	$cobertura_id = $_POST['cobertura_id'];

	if ($cobertura_id == '') {
		$cobertura_id = 0;
	}
}
else {
	$cobertura_id = 0;
}


if (isset($_POST['item_cobertura'])) {
	$item_cobertura = $_POST['item_cobertura'];

	if ($item_cobertura=='') {
		$item_cobertura = 0;
	}
}
else {
	$item_cobertura = 0;
}

if (isset($_POST['estacao_monta_id'])) {
	$estacao_monta_id = $_POST['estacao_monta_id'];

	if ($estacao_monta_id=='') {
		$estacao_monta_id = 0;
	}
}
else {
	$estacao_monta_id = 0;
}

$monta_natural = '';
$tipo_cobertura = $_POST['tipo_cobertura'];

// Quando o tipo de cobertura for Monta Natural então a estacao de monta recebe o mesmo numero da cobertura (Para ser utilizado no Relatório Situação Reprodutiva Individual)
if ($tipo_cobertura=='M' || $tipo_cobertura=='') {
	$monta_natural = 'S';
	$estacao_monta_id = $cobertura_id;
}

/*header('Content-type: application/json');
echo json_encode(array('error' => true, 'message' => 'Cob: '.$cobertura_id
. ' Item: ' . $item_cobertura . ' Prenhes: ' . $data_prenhes. ' Tipo Cobertura: ' . $tipo_cobertura . ' dias: ' . $dias_nascimento));
exit;*/


if (isset($_POST['pelagem_id'])) {
	$pelagem = $_POST['pelagem_id'];
}
else {
	$pelagem = 0;
}

if (isset($_POST['qtd_animal'])) {
	$qtd_animal = $_POST['qtd_animal'];
}
else {
	$qtd_animal = 1;
}

$data_sistema = date("Y-m-d H:i:s");

if ($local=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Local.'));
	exit;
}

if (empty($codigo_mae) && $numero!='' && $controle_estoque=='I'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'FALTA VALIDAR O Nº DA MÃE: Após digitar número, selecione o código na LISTA SUSPENSA.'));
	exit;
}

if ($pasto_id=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Pasto.'));
	exit;
}

if ($data_nascimento==''){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Data de Nascimento.'));
	exit;
}

$data_hoje = date("Y-m-d");

if ($data_nascimento>$data_hoje){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Data do nascimento não pode ser superior a data de hoje!'));
	exit;
}

if (empty($qtd_animal) && $controle_estoque=='L' && $tipo_gravacao!=1){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Quantidade de Animais.'));
	exit; 
}
else if (empty($qtd_animal)){
	$qtd_animal=1;
}

if ($controle_estoque=='L' && $tipo_gravacao!=1){
	$numero = '';
	$alfa = '';
	//$peso = $peso/$qtd_animal;
}

if ($tipo_gravacao!=1){ 
	if(!isset($_POST['sexo_animal'])) { 
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe o Sexo.'));
		exit;
	}
	else {
		$sexo = $_POST['sexo_animal'];
	}
}
else {
	if ($controle_estoque=='L') {
		$sexo = $_POST['sexo_animal'];
		$codigo_mae=0;
	}
}

/*header('Content-type: application/json');
echo json_encode(array('error' => true, 'message' => 'Raça: '.$raca.' '.$controle_estoque));
exit;*/

if (empty($raca) && $controle_estoque=='I'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Raça.'));
	exit; 
}
else if (empty($raca)){
	$raca=0;
}

if (empty($pelagem)){
	$pelagem=0;
}

if (empty($codigo_mae)){
	$codigo_mae=0;
}

if (empty($codigo_pai)){
	$codigo_pai=0;
}

$peso = $_POST['peso_animal'];

if (empty($peso) && $controle_estoque=='I'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Peso.'));
	exit; 
}
else if (empty($peso) && $controle_estoque=='L'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Peso Médio.'));
	exit; 
}

$id_mov_estoque = 0;


// Verifica a descricao do lote no pasto
$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
	WHERE tbl_pasto_id ='$pasto_id'");

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

if ($tipo_gravacao==0 && $controle_estoque=='I') {
	$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	    WHERE tbl_animal_codigo_alfa='$alfa' AND tbl_animal_codigo_numerico='$numero' AND 
	        	tbl_animal_codigo_mae='$codigo_mae'");
	$num_rows = mysqli_num_rows($tbl_animal);	

	if ($num_rows!=0) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'O código '. $alfa.$numero.' já existe cadastrado no sistema para essa Mãe.'));
		mysqli_close($conector);
	    exit;
	}
}

if ($tipo_gravacao==1){
	if ($codigo_animal_id!=0) {
		$sql = ("UPDATE tbl_animais SET 
					tbl_animal_data_nascimento='$data_nascimento',
					tbl_animal_codigo_pai='$codigo_pai',
					tbl_animal_codigo_raca='$raca',
					tbl_animal_codigo_pelagem='$pelagem',
					tbl_animal_alterado_em='$data_sistema',
					tbl_animal_alterado_por='$nomeusuario',
					tbl_animal_primeiro_peso='$peso',
					tbl_animal_data_primeiro_peso='$data_nascimento',
					tbl_animal_ultimo_peso='$peso',
					tbl_animal_data_ultimo='$data_nascimento'
	 		WHERE tbl_animal_codigo_id='$codigo_animal_id'");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração do animal no cadastro' . $erro_mysql));
			mysqli_close($conector);
	    	exit;
		} 
	}

    // Pega sexo anterior anterior para ajustar no pasto

    $tbl_estoque = "SELECT * FROM tbl_movimentacao_estoque 
                 WHERE tbl_mov_estoque_numero_id ='$num_mov_nascimento'";
    $rs = mysqli_query($conector, $tbl_estoque);
    $reg_estoque = mysqli_fetch_object($rs);

    $sexo_anterior = $reg_estoque->tbl_mov_estoque_sexo;
	$nascimento_anterior = $reg_estoque->tbl_mov_estoque_nascimento;

	// Ajusta registro do estoque
	if ($controle_estoque=='L') {
		$sql = ("UPDATE tbl_movimentacao_estoque SET 
		    			tbl_mov_estoque_nascimento='$data_nascimento',
		    			tbl_mov_estoque_sexo='$sexo',
						tbl_mov_estoque_codigo_raca='$raca',
						tbl_mov_estoque_codigo_pelagem='$pelagem',
						tbl_mov_estoque_primeiro_peso='$peso'
		 		WHERE tbl_mov_estoque_numero_id ='$num_mov_nascimento'");
	}
	else {
		$sql = ("UPDATE tbl_movimentacao_estoque SET 
		    			tbl_mov_estoque_nascimento='$data_nascimento',
						tbl_mov_estoque_codigo_raca='$raca',
						tbl_mov_estoque_codigo_pelagem='$pelagem',
						tbl_mov_estoque_primeiro_peso='$peso'
		 		WHERE tbl_mov_estoque_numero_id ='$num_mov_nascimento'");
	}
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração do registro do estoque' . $erro_mysql));
		mysqli_close($conector);
	   	exit;
	}

	// Ajusta animal no pasto
	$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
		WHERE tbl_animal_pasto_local='$local' AND 
	   	  	  tbl_animal_pasto_id ='$pasto_id' AND 
			  tbl_animal_pasto_nascimento='$nascimento_anterior' AND 
			  tbl_animal_pasto_sexo='$sexo_anterior'
		ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");

	$num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);	

	if ($num_rows_animal_pasto!=0) {
		$reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto);
		$numero_item =  $reg_animal_pasto->tbl_animal_pasto_numero_item;

		if ($controle_estoque=='L') {
			$sql = ("UPDATE tbl_animal_pasto SET 
						tbl_animal_pasto_nascimento='$data_nascimento',
						tbl_animal_pasto_sexo='$sexo',
						tbl_animal_pasto_raca='$raca',
						tbl_animal_pasto_alterado_em='$data_sistema',
						tbl_animal_pasto_alterado_por='$nomeusuario'
			 	     WHERE tbl_animal_pasto_id ='$pasto_id' AND 
			 	           tbl_animal_pasto_numero_item='$numero_item'");
		}
		else {
			$sql = ("UPDATE tbl_animal_pasto SET 
						tbl_animal_pasto_nascimento='$data_nascimento',
						tbl_animal_pasto_raca='$raca',
						tbl_animal_pasto_alterado_em='$data_sistema',
						tbl_animal_pasto_alterado_por='$nomeusuario'
			 	     WHERE tbl_animal_pasto_id ='$pasto_id' AND 
			 	           tbl_animal_pasto_numero_item='$numero_item'");
		}

	    $resultado = mysqli_query($conector,$sql);
	    $erro_mysql = mysqli_error($conector);

	    if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao do nascimento no pasto.' . $erro_mysql));
				mysqli_close($conector);
			exit;
	    }
	    else {
		   	header('Content-type: application/json');
	   		echo json_encode(array('success' => true, 'message' => 'Registro incluído com sucesso.', 'id_pasto' => $id_pasto, 'descricao_pasto' => $descricao_pasto, 'descricao_lote' => $descricao_lote));
			mysqli_close($conector);
			exit;
		}
	}
}
else {
	if ($numero!='') {
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
					tbl_animal_lote_primeiro_peso,
					tbl_animal_data_primeiro_peso,
					tbl_animal_lote_desmama,
					tbl_animal_data_desmama,
					tbl_animal_lote_ultimo,
					tbl_animal_data_ultimo, 
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
					tbl_animal_codigo_fazenda_anterior,
					tbl_animal_estacao_monta_nascimento,
					tbl_animal_codigo_cobertura
		        ) 
			    VALUES (
			    		'$alfa',
			    		'$numero',
			    		null,
			    		'$data_nascimento',
			    		'$sexo',
			    		null,
			            '$codigo_mae',
			    		null,
			            '$codigo_pai',
			    		null,
			    		'$peso',
			    		0,
			    		'$peso',
			    		null,
			    		'$data_nascimento',
			    		null,
			    		null,
			    		null,
			    		'$data_nascimento',
			            '$raca',
			            '$local',
			            '$pelagem',
			            '$local',
			            null,
			            null,
			            null,
			            null,
			            null,
			            null,
			            null,
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
		                null, 
		                '$estacao_monta_id',
		                '$cobertura_id'
		        )";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na inclusão do animal ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		$id_animal = mysqli_insert_id($conector);
		$id_animal = str_pad($id_animal, 9, "0", STR_PAD_LEFT);
	      
	    $numero++;
		$sql = ("UPDATE tbl_parametro_nascimento SET 
			    tbl_par_codigo_numerico='$numero'
	 	        WHERE tbl_par_codigo_local ='$local'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do número no parametro de nascimento' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
	}
	else {
		$id_animal = 0;
		$id_animal = str_pad($id_animal, 9, "0", STR_PAD_LEFT);
	}

	$data_movimentacao=date("Y-m-d");

	if ($controle_estoque=='I') {
		$sql = ("UPDATE tbl_item_cobertura SET 
		   		tbl_ite_cobertura_nascido='N'
		 	WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
		 	      tbl_ite_cobertura_numero_item='$item_cobertura'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
	   		header('Content-type: application/json');
	   		echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do item de cobertura' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
	}

	for ($i=1; $i<=$qtd_animal; $i++) { 
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
	                 tbl_mov_estoque_primeiro_peso,
	                 tbl_mov_estoque_codigo_categoria,
	                 tbl_mov_estoque_codigo_mae,
					 tbl_mov_estoque_cobertura_numero_id,  
		 	         tbl_mov_estoque_cobertura_numero_item,
		 	         tbl_mov_estoque_cobertura_monta_natural
	                ) 
	                VALUES ('$id_animal',
	                        '$data_movimentacao',
	                        '$data_nascimento',
	                        '$local',
	                        'E',
	                        'N',
	                        '$local',
	                        null,
	                        0,
	                        '$pasto_id',
	                        '$raca',
	                        '$pelagem',
	                        '$sexo',
	                        '$peso',
	                        1,
	                        '$codigo_mae',
	                        '$cobertura_id',
	                        '$item_cobertura',
	                        '$monta_natural'
	                )";
	        
	    $resultado = mysqli_query($conector,$sql);
	    $erro_mysql = mysqli_error($conector);

	    if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico entrada do animal.' . $erro_mysql));
				mysqli_close($conector);
			exit;
	    }

		$id_mov_estoque = mysqli_insert_id($conector);
		$id_mov_estoque = str_pad($id_mov_estoque, 9, "0", STR_PAD_LEFT);

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
					  		    '$pasto_id',
							    '$numero_item',
							    '$data_nascimento',
							    1,
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
		   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação do animal no pasto' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		// AJUSTA DIAS COM ANIMAIS NO PASTO SE O PASTO ESTIVER VAZIO
		if ($descricao_lote=='') {
			$dataAtual = new DateTime();
			$dataCom = new DateTime($data_com_incluir);
			$diff = $dataAtual->diff($dataCom);

			//if ($qtd_animais_pasto_entrada==0) {
			    if ($diff->h + ($diff->days * 24) < 24){
			        $query = "UPDATE tbl_pasto SET
			            tbl_pasto_alterado_em = '$data_sistema',
			            tbl_pasto_alterado_por = '$nomeusuario',
			            tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
			            tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
			            tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
			            tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
			        WHERE tbl_pasto_id = $pasto_id";

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
			            WHERE tbl_pasto_id = $pasto_id";

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
			                WHERE tbl_pasto_id = $pasto_id";

			            $resultado = mysqli_query($conector, $query);
			            $erro_mysql = mysqli_error($conector);

			            if (!$resultado){
			                header('Content-type: application/json');
			                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
			                exit;
			            } 
			        }
			    }
			//}
		}

		// GRAVAR A MEDIA POR CATEGORIA E PESAGEM PARA SISTEMA POR LOTE
		if ($controle_estoque=='L') {
            $categoria = 1;
            $data = $data_nascimento;
            $qtd_animais_pesados = 1;
            $peso_animais_pesados = $peso;

            $peso_animais_pesados_total = $peso_animais_pesados * $qtd_animais_pesados;

            // Pega ultima quantidade de animais e ultimo peso total
            $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                WHERE tbl_pm_local_id='$local' AND 
                      tbl_pm_categoria_id='$categoria' AND 
                      tbl_pm_sexo='$sexo'");

            $num_rows_media = mysqli_num_rows($tbl_media);

            if ($num_rows_media!=0){
                $reg_media = mysqli_fetch_object($tbl_media);
                $id_media = $reg_media->tbl_pm_id;
                $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
                $peso_anterior = $reg_media->tbl_pm_peso_total_atual;
            }
            else {
                $qtd_anterior=0;
                $peso_anterior=0;
            }

            // Calcula a media atual e grava no banco de dados
            $peso_medio_atual = ($peso_animais_pesados_total + $peso_anterior) /
                         ($qtd_animais_pesados + $qtd_anterior);

            $qtd_animais_atual = $qtd_animais_pesados + $qtd_anterior;
            $peso_total_atual = $peso_animais_pesados_total + $peso_anterior;

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
	                '$sexo',
	                '$local',
	                '$data',
	                '$qtd_animais_atual',
	                '$peso_medio_atual',
	                '$peso_total_atual'
	            )";
            }
            else {
               $sql = ("UPDATE tbl_peso_medio_categoria  SET 
                        tbl_pm_qtd_total_atual='$qtd_animais_atual',
                        tbl_pm_peso_medio_atual='$peso_medio_atual',
                        tbl_pm_peso_total_atual='$peso_total_atual'
                  WHERE tbl_pm_id ='$id_media'");
            }

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação da media dos pesos' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
		}

		// Adiciona nascimento no fechamento mensal se a data de nascimento for do mes anterior

		$data_hoje = date("Y-m-d");
		$partes_hoje = explode("-", $data_hoje);
		$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

		$partes_nascimento = explode("-", $data_nascimento);
		$anomes_final = $partes_nascimento[0].$partes_nascimento[1];
		$diferenca = $anomes_inicial - $anomes_final;

		if ($diferenca!=0) {
			$date = new DateTime($data_nascimento);
			$date->modify('last day of this month');
			$data_fechamento = $date->format('Y-m-d');

			$tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
        		WHERE tbl_fechamento_local='$local' AND
              		  tbl_fechamento_data='$data_fechamento' AND 
              		  tbl_fechamento_categoria='001' AND 
              		  tbl_fechamento_sexo='$sexo'");

    		$num_rows = mysqli_num_rows($tbl_fechamento);    

    		if ($num_rows!=0) {
    			$reg = mysqli_fetch_object($tbl_fechamento);
    			$fechamento_id = $reg->tbl_fechamento_id;
    			$qtd_fechamento = $reg->tbl_fechamento_qtd;
    			$peso_fechamento = $reg->tbl_fechamento_peso;

    			$qtd_fechamento++;
    			$peso_fechamento+=$peso;

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
        		WHERE tbl_fechamento_local='$local' AND
              		  tbl_fechamento_data='$data_fechamento'");

    		$num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

    		if ($num_rows!=0) {
    			$reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
    			$fechamento_id = $reg->tbl_fechamento_id;
    			$peso_nascimento = $reg->tbl_fechamento_peso_ent_nascimento;
	    		$peso_final = $reg->tbl_fechamento_peso_final;

    			$peso_nascimento+=$peso;
	    		$peso_final+=$peso;

				$sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
				   		tbl_fechamento_peso_ent_nascimento='$peso_nascimento',
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


if ($tipo_gravacao==3) { // Substitui ou Cria Monta caso o $tipo_gravacao for 3 
	$data_inclusao = date("Y-m-d");

	// cria data da prenhes a partir da data de nascimento
	if ($dias_nascimento<252 || $dias_nascimento>303 || $data_prenhes==0) {
		$data_previsao = $data_nascimento;

		$dias = 282;
    	$data_prenhes = date("Y-m-d", strtotime($data_nascimento . "-{$dias} days"));
	} // Fim Cria prenhes

	// Criar ou alterar a cobertura monta natural nova versao em 11/04/2025
	if ($tipo_cobertura=='M' || $tipo_cobertura=='') { //cria ou altera monta 

		$tbl_cobertura = mysqli_query($conector,"SELECT * FROM tbl_cobertura 
			INNER JOIN tbl_item_cobertura
			        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
				 WHERE tbl_cobertura_lixeira=0 AND 
				       tbl_cobertura_id='$cobertura_id'"); 

		$num_rows = mysqli_num_rows($tbl_cobertura);

		if ($num_rows!=0) { 
			if ($data_previsao == 0) { // Não alterar as datas prenhes e previsao
				$sql = ("UPDATE tbl_item_cobertura SET 
					tbl_ite_cobertura_nascido='N',
					tbl_ite_cobertura_resultado_diagnostico='P'
					WHERE tbl_ite_cobertura_numero_id='$cobertura_id' AND 
					      tbl_ite_cobertura_numero_item='$item_cobertura'");
			}
			else {
				$sql = ("UPDATE tbl_item_cobertura SET 
					tbl_ite_cobertura_nascido='N',  
					tbl_ite_cobertura_data_prenhes='$data_prenhes',
					tbl_ite_cobertura_previsao_parto='$data_previsao',
					tbl_ite_cobertura_data_diagnostico='$data_previsao',
					tbl_ite_cobertura_resultado_diagnostico='P'
					WHERE tbl_ite_cobertura_numero_id='$cobertura_id' AND 
					      tbl_ite_cobertura_numero_item ='$item_cobertura'");
			}

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização da cobertura monta natural' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
		}
		else { // Monta não existe, então cria o registro
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
								tbl_cobertura_encerrada,
								tbl_cobertura_planilha_processada
					        ) VALUES (
						        'M',
						        '$data_inclusao',
								'$local',
								0,
								0,
								0,
								1,
								null,
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
								null,
								null,
								null,
								'S',
								null
									)";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação do registro de monta natural' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

			$id_monta_natural = mysqli_insert_id($conector);
			$id_monta_natural = str_pad($id_monta_natural, 9, "0", STR_PAD_LEFT);

			$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
				WHERE tbl_animal_codigo_id='$codigo_mae'");

			$num_rows = mysqli_num_rows($tbl_animal);	

			if ($num_rows!=0) {
				$reg_animal = mysqli_fetch_object($tbl_animal);
				$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
				$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
			}
			else {
				$codigo_alfa = '';
				$codigo_numerico = '';
			}	

			if ($codigo_alfa=='') {
				$codigo_animal_edi = $codigo_numerico;
			}
			else {
				$codigo_animal_edi = $codigo_alfa.'-'.$codigo_numerico;
			}

			$sql = "INSERT INTO tbl_item_cobertura(
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
					tbl_ite_cobertura_numero_cobertura,
					tbl_ite_cobertura_qtd_diagnosticos_positivo,
					tbl_ite_cobertura_aborto_natimorto,
					tbl_ite_cobertura_nascido,
					tbl_ite_cobertura_data_prenhes,
					tbl_ite_cobertura_previsao_parto

			    )VALUES(
			        '$id_monta_natural',
			        1,
			        '$codigo_mae',
			        '$codigo_animal_edi',
					'$codigo_alfa',
					'$codigo_numerico',
			        '$data_inclusao',
			        null,
			        null,
			        '$data_previsao',
			        'P',
			        null,
			        null,
			        null,
			        null,
			        null,
			        null,
			        null,
			        null,
			        null,
			        1,
			        1,
			        null,
			        'N',
			        '$data_prenhes',
			        '$data_previsao'
			    )";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do item para monta natural' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

			$sql = ("UPDATE tbl_movimentacao_estoque SET 
						 tbl_mov_estoque_cobertura_numero_id='$id_monta_natural',  
			 	         tbl_mov_estoque_cobertura_numero_item=1,
			 	         tbl_mov_estoque_cobertura_monta_natural='S'
				 	WHERE tbl_mov_estoque_numero_id  ='$id_mov_estoque'");

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do número da cobertura monta natural no registro do estoque' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}


			// Quando o tipo de cobertura for Monta Natural então a estacao de monta recebe o mesmo numero da cobertura (Para ser utilizado no Relatório Situação Reprodutiva Individual)

			$sql = ("UPDATE tbl_cobertura SET 
				    		tbl_cobertura_codigo_estacao_monta='$id_monta_natural'
				    WHERE tbl_cobertura_id ='$id_monta_natural'");

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro de cobertura' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}

			$sql = ("UPDATE tbl_animais SET 
				    		tbl_animal_estacao_monta_nascimento='$id_monta_natural',
				    		tbl_animal_codigo_cobertura='$id_monta_natural'
				    WHERE tbl_animal_codigo_id ='$id_animal'");

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro do animal' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}
		}
	}
	else { // substituir cobertura IATF por Monta
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
							tbl_cobertura_encerrada,
							tbl_cobertura_planilha_processada
				        ) VALUES (
					        'M',
					        '$data_inclusao',
							'$local',
							0,
							0,
							0,
							1,
							null,
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
							null,
							null,
							null,
							'S',
							null
								)";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação da monta natural substituição' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$id_monta_natural = mysqli_insert_id($conector);
		$id_monta_natural = str_pad($id_monta_natural, 9, "0", STR_PAD_LEFT);

		    /*$sql = "INSERT INTO tbl_protocolo_cobertura(
		        tbl_protocolo_cobertura_codigo_id,
		        tbl_protocolo_cobertura_codigoiatf,
		        tbl_protocolo_cobertura_data
		    )VALUES(
		        '$id_monta_natural',
		        '$protocolo',
		        '$data_servico'
		    )";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do protocolo para monta natural' . $erro_mysql));
				mysqli_close($conector);
				exit;
			}*/

		$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
			WHERE tbl_animal_codigo_id='$codigo_mae'");

		$num_rows = mysqli_num_rows($tbl_animal);	

		if ($num_rows!=0) {
			$reg_animal = mysqli_fetch_object($tbl_animal);
			$codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
			$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
		}
		else {
			$codigo_alfa = '';
			$codigo_numerico = '';
		}	

		if ($codigo_alfa=='') {
			$codigo_animal_edi = $codigo_numerico;
		}
		else {
			$codigo_animal_edi = $codigo_alfa.'-'.$codigo_numerico;
		}

		$sql = "INSERT INTO tbl_item_cobertura(
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
			tbl_ite_cobertura_numero_cobertura,
			tbl_ite_cobertura_qtd_diagnosticos_positivo,
			tbl_ite_cobertura_aborto_natimorto,
			tbl_ite_cobertura_nascido,
			tbl_ite_cobertura_data_prenhes,
			tbl_ite_cobertura_previsao_parto
	    )VALUES(
	        '$id_monta_natural',
	        1,
	        '$codigo_mae',
	        '$codigo_animal_edi',
			'$codigo_alfa',
			'$codigo_numerico',
	        '$data_inclusao',
	        null,
	        null,
	        '$data_previsao',
	        'P',
	        null,
	        null,
	        null,
	        null,
	        null,
	        null,
	        null,
	        null,
	        null,
	        1,
	        1,
	        null,
	        'N',
		    '$data_prenhes',
		    '$data_previsao'
	    )";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do item para monta natural substituição' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("UPDATE tbl_movimentacao_estoque SET 
					 tbl_mov_estoque_cobertura_numero_id='$id_monta_natural',  
		 	         tbl_mov_estoque_cobertura_numero_item=1,
		 	         tbl_mov_estoque_cobertura_monta_natural='S'
			 	WHERE tbl_mov_estoque_numero_id  ='$id_mov_estoque'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do número da cobertura monta natural no registro do estoque' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("UPDATE tbl_item_cobertura SET 
			    		tbl_ite_cobertura_resultado_diagnostico='N',
			    		tbl_ite_cobertura_nascido=null
			 	WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			 	      tbl_ite_cobertura_numero_item='$item_cobertura'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na substituição para diganostico negativo no item da cobertura' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		// Quando o tipo de cobertura for Monta Natural então a estacao de monta recebe o mesmo numero da cobertura (Para ser utilizado no Relatório Situação Reprodutiva Individual)

		$sql = ("UPDATE tbl_cobertura SET 
			    		tbl_cobertura_codigo_estacao_monta='$id_monta_natural'
			    WHERE tbl_cobertura_id ='$id_monta_natural'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro de cobertura' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("UPDATE tbl_animais SET 
			    		tbl_animal_estacao_monta_nascimento='$id_monta_natural',
			    		tbl_animal_codigo_cobertura='$id_monta_natural'
			    WHERE tbl_animal_codigo_id ='$id_animal'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
		   	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro no ajuste do código da estação (monta natural) no cadastro do animal' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
	} // Fim Criar ou alterar a cobertura
} // Fim Substitui


	// criar protocolo monta natura 

	/*$tbl_protocolo = mysqli_query($conector, "SELECT * FROM tbl_protocoloiatf
		WHERE tbl_protocoloiatf_id=999");

	$num_rows = mysqli_num_rows($tbl_protocolo);	

	if ($num_rows!=0) {
		$reg_protocolo = mysqli_fetch_object($tbl_protocolo);
		$protocolo = $reg_protocolo->tbl_protocoloiatf_id;
	}
	else {
		$protocolo = 999;
		$descricao_protocolo = 'MONTA NATURAL';

		$sql = "INSERT INTO tbl_protocoloiatf (
					tbl_protocoloiatf_id,
					tbl_protocoloiatf_descricao,
					tbl_protocoloiatf_qtde,
					tbl_protocoloiatf_dias_diagnostico,
					tbl_protocoloiatf_incluido_em,
					tbl_protocoloiatf_incluido_por,
					tbl_protocoloiatf_alterado_em,
					tbl_protocoloiatf_alterado_por,
					tbl_protocoloiatf_lixeira,
					tbl_protocoloiatf_lixeira_em,
					tbl_protocoloiatf_lixeira_por
			        ) 
				    VALUES (
				    		'$protocolo',
				    		'$descricao_protocolo',
				    		999,
				            33,
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
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do protocolo monta natural' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 

		$sql = "INSERT INTO tbl_item_protocoloiatf (
					tbl_ite_protocoloiatf_protocolo_id,
					tbl_ite_protocoloiatf_descricao,
					tbl_ite_protocoloiatf_medicamento_1,
					tbl_ite_protocoloiatf_qtde_1,
					tbl_ite_protocoloiatf_unidade_1,
					tbl_ite_protocoloiatf_medicamento_2,
					tbl_ite_protocoloiatf_qtde_2,
					tbl_ite_protocoloiatf_unidade_2,
					tbl_ite_protocoloiatf_medicamento_3,
					tbl_ite_protocoloiatf_qtde_3,
					tbl_ite_protocoloiatf_unidade_3,
					tbl_ite_protocoloiatf_medicamento_4,
					tbl_ite_protocoloiatf_qtde_4,
					tbl_ite_protocoloiatf_unidade_4,
					tbl_ite_protocoloiatf_incluido_em,
					tbl_ite_protocoloiatf_incluido_por,
					tbl_ite_protocoloiatf_alterado_em,
					tbl_ite_protocoloiatf_alterado_por,
					tbl_ite_protocoloiatf_lixeira,
					tbl_ite_protocoloiatf_lixeira_em,
					tbl_ite_protocoloiatf_lixeira_por
			        ) 
				    VALUES (
				    		'$protocolo',
				    		'Dia 0',
				    		'Monta Natural',
				            1,
				            'Und',
				            null,
				            null,
				            null,
				            null,
				            null,
				            null,
				            null,
				            null,
				            null,
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
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do item protocolo monta natural' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
	}*/	

	// criar grupo quando for substituir a cobertura
	/*if ($estacao_monta_id!=0) {
		$descricao_grupo = 'MONTA NATURAL';

		$tbl_grupo = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta
			WHERE tbl_grupo_codigo_estacao_monta ='$estacao_monta_id' AND 
		   	  	  tbl_grupo_id!=999
			ORDER BY tbl_grupo_id DESC LIMIT 1");

		$num_rows = mysqli_num_rows($tbl_grupo);	

		if ($num_rows!=0) {
			$reg_grupo = mysqli_fetch_object($tbl_grupo);
			$codigo_grupo = $reg_grupo->tbl_grupo_id;
			$local_grupo = $reg_grupo->tbl_grupo_codigo_local;
			$codigo_grupo++;
		}
		else {
			$codigo_grupo = 1;
		}	

		$sql = "INSERT INTO tbl_grupo_estacao_monta (
				  		tbl_grupo_id,
						tbl_grupo_codigo_estacao_monta,
						tbl_grupo_descricao,
						tbl_grupo_codigo_local
			        ) 
				    VALUES (
				    		'$codigo_grupo',
				    		'$estacao_monta_id',
				    		'$descricao_grupo',
				            '$local_grupo'
			        )";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão do novo grupo monta natural' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} */


header('Content-type: application/json');
echo json_encode(array('success' => true, 'message' => 'Registro incluído com sucesso.', 'id_pasto' => $id_pasto, 'descricao_pasto' => $descricao_pasto, 'descricao_lote' => $descricao_lote));
mysqli_close($conector);
exit;
?>