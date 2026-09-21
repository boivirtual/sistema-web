<?php 
function sonumero($str) {
	return preg_replace("/[^0-9]/", "", $str);
}


$tipo_gravacao = $_POST['tipo_gravacao'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$codigo = sonumero($_POST['codigo_plano']);
	$codigo_ref = $_POST['ref_contabil'];
	$ana_sin = $_POST['analitico_sintetico'];
	$deb_cre = $_POST['debito_credito'];
	$descricao = $_POST['descricao_plano_contas'];
	$descricao_complementar = $_POST['descricao_complementar'];

	$sql = ("UPDATE tbl_plano_contas SET tbl_plano_contas_descricao='$descricao',
						                 tbl_plano_contas_refrencia_contabilidade='$codigo_ref',
						                 tbl_plano_contas_debito_credito='$deb_cre',
						                 tbl_plano_contas_ana_sin='$ana_sin',
						                 tbl_plano_contas_descricao_complementar='$descricao_complementar',
										 tbl_plano_contas_alterado_em='$data_sistema',
										 tbl_plano_contas_alterado_por='$nomeusuario'
 		                           WHERE tbl_plano_contas_codigo_id='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$resposta = array('success' => true, 'message' => 'Registro altarado com sucesso.');
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação.' . $erro_mysql));
		    mysqli_close($conector);
		    exit;
	}
	else {
	    header('Content-type: application/json');
	    echo json_encode($resposta);
	    mysqli_close($conector);
	    exit;
	}    
}
else{
	$codigo_pri = $_POST['codigo_pri'];
	$codigo_seg = $_POST['codigo_seg'];
	$codigo_ter = $_POST['codigo_ter'];

	$codigo_pri = sonumero($codigo_pri);
	$codigo_seg = sonumero($codigo_seg);
	$codigo_ter = sonumero($codigo_ter);

	$codigo_seg = str_pad($codigo_seg, 2, "0", STR_PAD_LEFT);
	$codigo_ter = str_pad($codigo_ter, 4, "0", STR_PAD_LEFT);

	if ($codigo_ter==0){
		if ($codigo_seg==0) {
			$codigo = str_pad($codigo_pri, 7, "0", STR_PAD_RIGHT);
			$descricao = strtoupper($_POST['descricao_plano_contas']);
			$nivel = 1;
		}
	    else {
	    	$codigo = str_pad($codigo_pri.$codigo_seg, 7, "0", STR_PAD_RIGHT);
			$descricao = strtoupper($_POST['descricao_plano_contas']);
			$nivel = 2;
	    }
	}
	else {
		$codigo = str_pad($codigo_pri.$codigo_seg.$codigo_ter, 7, "0", STR_PAD_RIGHT);
		$descricao = $_POST['descricao_plano_contas'];
		$nivel = 3;
	}

    $codigo = sonumero($codigo);

	$deb_cre = $_POST['debito_credito'];
	$ana_sin = $_POST['analitico_sintetico'];
	$codigo_ref = $_POST['ref_contabil'];
	$descricao_complementar = $_POST['descricao_complementar'];

	$sql = "INSERT INTO tbl_plano_contas (
	                    tbl_plano_contas_codigo_id,
						tbl_plano_contas_nivel,
						tbl_plano_contas_descricao,
						tbl_plano_contas_refrencia_contabilidade,
						tbl_plano_contas_debito_credito,
						tbl_plano_contas_ana_sin,
						tbl_plano_contas_descricao_complementar,
						tbl_plano_contas_incluido_em,
						tbl_plano_contas_incluido_por,
						tbl_plano_contas_alterado_em,
						tbl_plano_contas_alterado_por,
						tbl_plano_contas_lixeira,
						tbl_plano_contas_lixeira_em,
						tbl_plano_contas_lixeira_por
	                   ) 
	            VALUES ('$codigo',
	            		'$nivel',
	                    '$descricao',
	                    '$codigo_ref',
	                    '$deb_cre',
	                    '$ana_sin',
	                    '$descricao_complementar',
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
	    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação.' . $erro_mysql));
	}
	else {
	    header('Content-type: application/json');
	    echo json_encode($resposta);
	}    
}

mysqli_close($conector);


?>