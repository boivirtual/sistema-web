<?php 
$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_conta = $_POST['codigo_conta'];
$conta_contabil = $_POST['conta_contabil_id'];
$local = $_POST['codigo_local'];
$ano = $_POST['ano_conta'];
$data_sistema = date("Y-m-d H:i:s");

if ($conta_contabil==0){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Conta Contábil.'));
	exit;
}

if ($local==0){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Fazenda.'));
	exit;
}

if ($_POST['valor_previsto_jan']==0){
	$valor_jan=0.00;
}
else {
	$valor_jan = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_jan']));
}

if ($_POST['valor_previsto_fev']==0){
	$valor_fev=0.00;
}
else {
	$valor_fev = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_fev']));
}

if ($_POST['valor_previsto_mar']==0){
	$valor_mar=0.00;
}
else {
	$valor_mar = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_mar']));
}

if ($_POST['valor_previsto_abr']==0){
	$valor_abr=0.00;
}
else {
	$valor_abr = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_abr']));
}

if ($_POST['valor_previsto_mai']==0){
	$valor_mai=0.00;
}
else {
	$valor_mai = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_mai']));
}

if ($_POST['valor_previsto_jun']==0){
	$valor_jun=0.00;
}
else {
	$valor_jun = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_jun']));
}

if ($_POST['valor_previsto_jul']==0){
	$valor_jul=0.00;
}
else {
	$valor_jul = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_jul']));
}

if ($_POST['valor_previsto_ago']==0){
	$valor_ago=0.00;
}
else {
	$valor_ago = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_ago']));
}

if ($_POST['valor_previsto_set']==0){
	$valor_set=0.00;
}
else {
	$valor_set = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_set']));
}

if ($_POST['valor_previsto_out']==0){
	$valor_out=0.00;
}
else {
	$valor_out = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_out']));
}

if ($_POST['valor_previsto_nov']==0){
	$valor_nov=0.00;
}
else {
	$valor_nov = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_nov']));
}

if ($_POST['valor_previsto_dez']==0){
	$valor_dez=0.00;
}
else {
	$valor_dez = str_replace(',','.', str_replace('.','', $_POST['valor_previsto_dez']));
}

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==2){
		$sql = "UPDATE tbl_previsao_conta SET 
	                   tbl_previsao_conta_lixeira=1,
	                   tbl_previsao_conta_lixeira_em='$data_sistema',
	                   tbl_previsao_conta_lixeira_por='$nomeusuario'
	                   WHERE tbl_previsao_conta_id='$codigo_conta'";
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
		$sql = "UPDATE tbl_previsao_conta SET 
	                   tbl_previsao_conta_lixeira=0,
	                   tbl_previsao_conta_lixeira_em=null,
	                   tbl_previsao_conta_lixeira_por=null
	                   WHERE tbl_previsao_conta_id='$codigo_conta'";
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
	$sql = ("UPDATE tbl_previsao_conta SET 
		  		tbl_previsao_conta_codigo='$conta_contabil',
		  		tbl_previsao_conta_codigo_fazenda='$local',
				tbl_previsao_conta_ano='$ano',
				tbl_previsao_conta_valor_jan='$valor_jan',
				tbl_previsao_conta_valor_fev='$valor_fev',
				tbl_previsao_conta_valor_mar='$valor_mar',
				tbl_previsao_conta_valor_abr='$valor_abr',
				tbl_previsao_conta_valor_mai='$valor_mai',
				tbl_previsao_conta_valor_jun='$valor_jun',
				tbl_previsao_conta_valor_jul='$valor_jul',
				tbl_previsao_conta_valor_ago='$valor_ago',
				tbl_previsao_conta_valor_set='$valor_set',
				tbl_previsao_conta_valor_out='$valor_out',
				tbl_previsao_conta_valor_nov='$valor_nov',
				tbl_previsao_conta_valor_dez='$valor_dez',
				tbl_previsao_conta_alterado_em='$data_sistema',
				tbl_previsao_conta_alterado_por='$nomeusuario'
				WHERE tbl_previsao_conta_id='$codigo_conta'");
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
	$sql = "INSERT INTO tbl_previsao_conta (
			tbl_previsao_conta_codigo,
			tbl_previsao_conta_codigo_fazenda,
			tbl_previsao_conta_ano,
			tbl_previsao_conta_valor_jan,
			tbl_previsao_conta_valor_fev,
			tbl_previsao_conta_valor_mar,
			tbl_previsao_conta_valor_abr,
			tbl_previsao_conta_valor_mai,
			tbl_previsao_conta_valor_jun,
			tbl_previsao_conta_valor_jul,
			tbl_previsao_conta_valor_ago,
			tbl_previsao_conta_valor_set,
			tbl_previsao_conta_valor_out,
			tbl_previsao_conta_valor_nov,
			tbl_previsao_conta_valor_dez,
			tbl_previsao_conta_incluido_em,
			tbl_previsao_conta_incluido_por,
			tbl_previsao_conta_alterado_em,
			tbl_previsao_conta_alterado_por,
			tbl_previsao_conta_lixeira,
			tbl_previsao_conta_lixeira_em,
			tbl_previsao_conta_lixeira_por
	        ) 
		    VALUES (
		    		'$conta_contabil',
		    		'$local',
		    		'$ano',
		    		'$valor_jan',
		    		'$valor_fev',
		    		'$valor_mar',
		    		'$valor_abr',
		    		'$valor_mai',
		    		'$valor_jun',
		    		'$valor_jul',
		    		'$valor_ago',
		    		'$valor_set',
		    		'$valor_out',
		    		'$valor_nov',
		    		'$valor_dez',
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
	} 
	else {
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
	}

	mysqli_close($conector);
	exit;
}

mysqli_close($conector);


?>