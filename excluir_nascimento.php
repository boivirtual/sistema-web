<?php 
@ session_start(); 
$nome_usuario = $_SESSION['nome_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];

include "conecta_mysql.inc";

$num_mov_nascimento = $_POST['num_mov_nascimento'];
$codigo_animal_id = $_POST['codigo_animal_id'];
$codigo_mae_id = $_POST['codigo_mae_animal'];
$local = $_POST['local_id'];
$pasto_id = $_POST['pasto_id'];
$sexo = $_POST['sexo_animal'];
$desc_pasto = $_POST['desc_pasto'];
$desc_categoria= '00 a 07 meses';

if ($sexo=='F') {
	$desc_sexo = 'Fêmea';
}
else {
	$desc_sexo = 'Macho';
}

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	WHERE tbl_animal_codigo_id='$codigo_animal_id'");

$num_rows_animal = mysqli_num_rows($tbl_animal);

if ($num_rows_animal==0) {
	$estacao_id = 0;
}
else {
	$reg_animal = mysqli_fetch_object($tbl_animal);
	$estacao_id = $reg_animal->tbl_animal_estacao_monta_nascimento;
	$data_nascimento = $reg_animal->tbl_animal_data_nascimento;
}

// O codigo da categoria foi substituido pela data da nascimento em 20/08/2024 por conta do ajuste "AJUSTAR AS SAIDAS DE ANIMAIS DO PASTO POR ID" para controle de estoque por ID
$data = $data_nascimento;

$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
	WHERE tbl_animal_pasto_local = '$local' AND 
	      tbl_animal_pasto_id = '$pasto_id' AND 
	      tbl_animal_pasto_sexo = '$sexo' AND 
	      tbl_animal_pasto_nascimento = '$data'");
$num_rows_pasto = mysqli_num_rows($tbl_pasto);    

if ($num_rows_pasto==0) { 
   	// Vai procurar um registro com a data de nascimento igual a data de nascimento do animal e substitui-la para depois baixar o animal do pasto

   	// procurar um registro por categoria com outra data de nascimento qualquer.
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
		WHERE tbl_animal_pasto_local = '$local' AND 
		      tbl_animal_pasto_id = '$pasto_id' AND 
		      tbl_animal_pasto_sexo = '$sexo' AND 
		      tbl_animal_pasto_categoria = 1
		ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
	$num_rows_pasto = mysqli_num_rows($tbl_pasto);    

	if ($num_rows_pasto==0) {	
	 	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', categoria ' .$desc_categoria. ' no pasto.'));
		exit;
	}
	else {
		$reg_pasto_atual = mysqli_fetch_object($tbl_pasto);
		$data_nascimento_atual = $reg_pasto_atual->tbl_animal_pasto_nascimento;
		$numero_item_atual = $reg_pasto_atual->tbl_animal_pasto_numero_item;

		// procura um outro animal com a mesma data de nascimento e sexo em outros pastos

		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
			WHERE tbl_animal_pasto_local = '$local' AND 
			      tbl_animal_pasto_sexo = '$sexo' AND 
			      tbl_animal_pasto_nascimento = '$data'
			ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");
	    $num_rows_pasto = mysqli_num_rows($tbl_pasto);    

	    if ($num_rows_pasto==0) {
		 	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Não existe animais com o sexo ' .$sexo . ', categoria ' .$desc_categoria. ', nascimento '.$data.' em outros pastos.'));
			exit;
	    }
	    else {
	  		$reg_pasto_trocar = mysqli_fetch_object($tbl_pasto);
	  		$data_nascimento_trocar = $reg_pasto_trocar->tbl_animal_pasto_nascimento;
	   		$numero_item_trocar = $reg_pasto_trocar->tbl_animal_pasto_numero_item;
	   		$pasto_trocar = $reg_pasto_trocar->tbl_animal_pasto_id;

	   		//Salva o pasto atual com a nova data de nascimento
			$sql = "UPDATE tbl_animal_pasto SET
					tbl_animal_pasto_nascimento='$data_nascimento_trocar'
			    WHERE tbl_animal_pasto_local = '$local' AND 
			    	  tbl_animal_pasto_numero_item = '$numero_item_atual' AND 
			    	  tbl_animal_pasto_id = '$pasto_id'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto atual ' . $erro_mysql));
			   	exit;
			} 

			//Salva o pasto trocar com a data de nascimento anterior
			$sql = "UPDATE tbl_animal_pasto SET
					tbl_animal_pasto_nascimento='$data_nascimento_atual'
			    WHERE tbl_animal_pasto_local = '$local' AND 
			    	  tbl_animal_pasto_numero_item = '$numero_item_trocar' AND 
			    	  tbl_animal_pasto_id = '$pasto_trocar'";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			   	header('Content-type: application/json');
			   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao ajustar o nascimento no pasto trocar ' . $erro_mysql));
			   	exit;
			} 
		}
	}

	// Pega novamente o registro do animal no pasto, agora com a data de nascimento já correta

	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
		WHERE tbl_animal_pasto_local = '$local' AND 
		      tbl_animal_pasto_id = '$pasto_id' AND 
		      tbl_animal_pasto_sexo = '$sexo' AND 
		      tbl_animal_pasto_nascimento = '$data'");
	$num_rows_pasto = mysqli_num_rows($tbl_pasto);    
}

$reg_pasto = mysqli_fetch_object($tbl_pasto);
$numero_item = $reg_pasto->tbl_animal_pasto_numero_item;

$data_sistema = date("Y-m-d H:i:s");

if ($pasto_id=='000000000'){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Pasto.'));
	exit;
}

if ($codigo_animal_id!=0) {
    $sql = ("DELETE FROM tbl_animais WHERE tbl_animal_codigo_id='$codigo_animal_id'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do animal no cadastro' . $erro_mysql));
		mysqli_close($conector);
    	exit;
	} 
}

$tbl_estoque = mysqli_query($conector, "select * from tbl_movimentacao_estoque
    where tbl_mov_estoque_numero_id ='$num_mov_nascimento'"); 

$reg_estoque = mysqli_fetch_object($tbl_estoque);
$data_nascimento = $reg_estoque->tbl_mov_estoque_nascimento;
$sexo = $reg_estoque->tbl_mov_estoque_sexo;
$peso = $reg_estoque->tbl_mov_estoque_primeiro_peso;
$cobertura_id= $reg_estoque->tbl_mov_estoque_cobertura_numero_id;
$item_cobertura = $reg_estoque->tbl_mov_estoque_cobertura_numero_item;

$sql = ("DELETE FROM tbl_animal_pasto 
    WHERE tbl_animal_pasto_local='$local' AND 
		 tbl_animal_pasto_id ='$pasto_id' AND 
         tbl_animal_pasto_numero_item='$numero_item'");
$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
   	header('Content-type: application/json');
   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do registro no pasto' . $erro_mysql));
	mysqli_close($conector);
   	exit;
}

// Exclui a movimentação do estoque

$sql = ("DELETE FROM tbl_movimentacao_estoque 
	           WHERE tbl_mov_estoque_numero_id ='$num_mov_nascimento'");
$resultado = mysqli_query($conector,$sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado){
   	header('Content-type: application/json');
   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão do registro do estoque' . $erro_mysql));
	mysqli_close($conector);
   	exit;
}

// AJUSTA REGITROS MEDIA CATEGORIA E PESAGEM PARA SISTEMA POR LOTE
if ($controle_estoque=='L') {
    $categoria = 1;
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
    if (($qtd_anterior - $qtd_animais_pesados)<=0) {
    	$peso_medio_atual = 0;
    }
    else {
	    $peso_medio_atual = ($peso_anterior - $peso_animais_pesados_total) /
    	                    ($qtd_anterior - $qtd_animais_pesados);
    }

    $qtd_animais_atual = $qtd_anterior - $qtd_animais_pesados;
    $peso_total_atual = $peso_anterior - $peso_animais_pesados_total;

    $sql = ("UPDATE tbl_peso_medio_categoria  SET 
                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
                    tbl_pm_peso_medio_atual='$peso_medio_atual',
                    tbl_pm_peso_total_atual='$peso_total_atual'
            WHERE tbl_pm_id ='$id_media'");

    $resultado = mysqli_query($conector,$sql);
    $erro_mysql = mysqli_error($conector);

	if (!$resultado) {
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação da media dos pesos' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}

// Exclui monta (quando ela era incluida como IATF)
$tbl_monta = mysqli_query($conector, "select * from tbl_item_cobertura
	inner join tbl_cobertura
	        on tbl_cobertura_id = tbl_ite_cobertura_numero_id
    where tbl_cobertura_lixeira=0 and 
          tbl_ite_cobertura_numero_id='$cobertura_id' and 
          tbl_ite_cobertura_numero_item='$item_cobertura' and 
          tbl_cobertura_controle='C' and
          tbl_cobertura_qtd_animais=1"); 

$num_rows_monta = mysqli_num_rows($tbl_monta);	

if ($num_rows_monta!=0) {
	$reg_monta = mysqli_fetch_object($tbl_monta);
	$cobertura_monta_id = $reg_monta->tbl_cobertura_id;
	$item_cobertura_monta = $reg_monta->tbl_ite_cobertura_numero_item;

	$sql = ("DELETE FROM tbl_cobertura
		           WHERE tbl_cobertura_id ='$cobertura_monta_id'");
	
	$resultado = mysqli_query($conector,$sql);

	$sql = ("DELETE FROM tbl_item_cobertura
		WHERE tbl_ite_cobertura_numero_id='$cobertura_monta_id' AND 
		      tbl_ite_cobertura_numero_item='$item_cobertura_monta'");
	
	$resultado = mysqli_query($conector,$sql);

	$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
		INNER JOIN tbl_cobertura
		        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
			 WHERE 
				   tbl_cobertura_lixeira=0 AND 
				   tbl_cobertura_controle = 'C' AND
				   tbl_ite_cobertura_codigo_id_animal = '$codigo_mae_id' AND
				   tbl_ite_cobertura_resultado_diagnostico = 'N' AND 
				   tbl_ite_cobertura_nascido = 'N' AND 
				   tbl_cobertura_codigo_estacao_monta='$estacao_id'"); 

	$num_rows = mysqli_num_rows($tbl_item_cobertura);

	if ($num_rows!=0){
	    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
	    $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
	    $item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;

		$sql = ("UPDATE tbl_item_cobertura SET 
			    		tbl_ite_cobertura_resultado_diagnostico='P',
			    		tbl_ite_cobertura_nascido=null
			 	  WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
			 	        tbl_ite_cobertura_numero_item='$item_cobertura'");

		$resultado = mysqli_query($conector,$sql);
	}
}
// Fim excluir monta natural

// Altera o item de cobertura limpando o nascimento quando for IATF
$data_sistema = date("Y-m-d");

$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
	INNER JOIN tbl_cobertura
	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		 WHERE tbl_cobertura_lixeira=0 AND 
		       tbl_cobertura_controle = 'C' AND
			   tbl_ite_cobertura_codigo_id_animal = '$codigo_mae_id' AND 
			   tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
			   tbl_ite_cobertura_nascido = 'N' AND 
			   tbl_cobertura_codigo_estacao_monta='$estacao_id'"); 

$num_rows = mysqli_num_rows($tbl_item_cobertura);

if ($num_rows!=0){
    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
    $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
    $item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;

	$sql = ("UPDATE tbl_item_cobertura SET 
			tbl_ite_cobertura_aborto_natimorto=null,
			tbl_ite_cobertura_nascido=null
		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
		      tbl_ite_cobertura_numero_item = '$item_cobertura'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura IATF.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}

// Altera o item de cobertura limpando o nascimento quando for Nova Monta
$data_sistema = date("Y-m-d");

$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
	INNER JOIN tbl_cobertura
	        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
		 WHERE tbl_cobertura_lixeira=0 AND 
		       tbl_cobertura_controle = 'M' AND
			   tbl_ite_cobertura_codigo_id_animal = '$codigo_mae_id' AND 
			   tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
			   tbl_ite_cobertura_nascido = 'N'"); 

$num_rows = mysqli_num_rows($tbl_item_cobertura);

if ($num_rows!=0){
    $reg_item = mysqli_fetch_object($tbl_item_cobertura);
    $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;
    $item_cobertura = $reg_item->tbl_ite_cobertura_numero_item;

	$sql = ("UPDATE tbl_item_cobertura SET 
			tbl_ite_cobertura_aborto_natimorto=null,
			tbl_ite_cobertura_nascido=null,
			tbl_ite_cobertura_resultado_diagnostico=null,
			tbl_ite_cobertura_data_prenhes=null,
			tbl_ite_cobertura_previsao_parto=null
		WHERE tbl_ite_cobertura_numero_id ='$cobertura_id' AND 
		      tbl_ite_cobertura_numero_item = '$item_cobertura'");

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	  	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Erro na atualização do item de cobertura Monta.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}
}

// Subtrai registro do fechamento mensal
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

		$qtd_fechamento--;
		$peso_fechamento-=$peso;

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

    	$peso_nascimento-=$peso;
    	$peso_final-=$peso;

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

// Fim Subtrai registro

// Limpa Descrição do Lote caso o pasto fique vazio e atualiza quantida de dias com animal no pasto
$data_sistema = date("Y-m-d H:i:s");

$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
	WHERE tbl_animal_pasto_local = '$local' AND 
		  tbl_animal_pasto_id ='$pasto_id'");

$num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);    

if ($num_rows_animal_pasto==0) {
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
	    WHERE tbl_pasto_id = $pasto_id");

	$reg_pasto = mysqli_fetch_object($tbl_pasto);

	$data_com_remover = $reg_pasto->tbl_pasto_data_com_animais;
	$data_com_remover_anterior = $reg_pasto->tbl_pasto_data_com_animais_anterior;
	$data_sem_remover = $reg_pasto->tbl_pasto_data_sem_animais;
	$data_sem_remover_anterior = $reg_pasto->tbl_pasto_data_sem_animais_anterior;

	$query = "UPDATE tbl_pasto SET 
	    tbl_pasto_descricao_lote = null,
	    tbl_pasto_id_lote = null, 
	    tbl_pasto_ano_lote = null,
	    tbl_pasto_descricao_lote_1 = null,
	    tbl_pasto_descricao_lote_2 = null,
	    tbl_pasto_descricao_lote_3 = null,
	    tbl_pasto_descricao_lote_4 = null,
	    tbl_pasto_descricao_lote_5 = null,
	    tbl_pasto_descricao_lote_6 = null,
	    tbl_pasto_alterado_em = '$data_sistema',
	    tbl_pasto_alterado_por = '$nome_usuario'
	    WHERE tbl_pasto_id = $pasto_id";

	$resultado = mysqli_query($conector, $query);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql));
	    exit;
	}

	$dataAtual = new DateTime();
	$dataCom = new DateTime($data_com_remover);
	$diff = $dataAtual->diff($dataCom);

	if ($diff->h + ($diff->days * 24) < 24){
	    $query = "UPDATE tbl_pasto SET 
	        tbl_pasto_alterado_em = '$data_sistema',
	        tbl_pasto_alterado_por = '$nome_usuario',
	        tbl_pasto_data_com_animais = '$data_sem_remover_anterior',
	        tbl_pasto_data_com_animais_anterior = '$data_sem_remover_anterior',
	        tbl_pasto_data_sem_animais = '$data_sem_remover_anterior',
	        tbl_pasto_data_sem_animais_anterior = '$data_sem_remover_anterior'
	        WHERE tbl_pasto_id = $pasto_id";

	    $resultado = mysqli_query($conector, $query);
	    $erro_mysql = mysqli_error($conector);

	    if (!$resultado){
	        header('Content-type: application/json');
	        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql));
	        exit;
	    } 
	}
	else {
	    $query = "UPDATE tbl_pasto SET 
	        tbl_pasto_alterado_em = '$data_sistema',
		    tbl_pasto_alterado_por = '$nome_usuario',
		    tbl_pasto_data_sem_animais = '$data_sistema',
		    tbl_pasto_data_sem_animais_anterior = '$data_sem_remover'
		    WHERE tbl_pasto_id =$pasto_id";

		$resultado = mysqli_query($conector, $query);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
		    exit;
		} 
	}
} // Fim Atualiza Descrição do lote e dias com animal no pasto

$resposta = array('success' => true, 'message' => 'Registro excluído com sucesso.');
header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;


?>