<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$inicio_estacao = date("Y-m-d");
$fim_estacao = date("Y-m-d");
$array_fazenda= $_POST['array_codigo_fazenda'];
$array_codigo_alfa = $_POST['array_codigo_alfa'];
$array_codigo_numerico = $_POST['array_codigo_numerico'];

/*
if (empty($inicio_estacao)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a data incial da estação de monta.'));
	exit;
}

if (empty($fim_estacao)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a data final da estação de monta.'));
	exit;
}
*/

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

$array_local = explode("!", $array_fazenda);
$array_alfa = explode("!", $array_codigo_alfa);
$array_numerico = explode("!", $array_codigo_numerico);

//if ($tipo_gravacao==1) {

//}
//else {
	for($i=0; $i < count($array_local); $i++) {
		$codigo_local = $array_local[$i];
		$codigo_alfa = $array_alfa[$i];
		$codigo_numerico = $array_numerico[$i];

	    if ($codigo_numerico!='') {
    		$codigo_numerico = intval($codigo_numerico);
    	}

    	$tbl_par = "SELECT * FROM tbl_parametro_nascimento 
    	       WHERE tbl_par_codigo_local='$codigo_local' AND 
                     tbl_par_lixeira=0";  
		$qr = mysqli_query($conector, $tbl_par);
		$num_rows = mysqli_num_rows($qr);

		if ($num_rows!=0){
			$sql = ("UPDATE tbl_parametro_nascimento SET 
						tbl_par_codigo_alfa='$codigo_alfa',
						tbl_par_codigo_numerico='$codigo_numerico',
						tbl_par_estacao_monta_inicial='$inicio_estacao',
						tbl_par_estacao_monta_final='$fim_estacao',
						tbl_par_alterado_em='$data_sistema',
						tbl_par_alterado_por='$nomeusuario'
					WHERE tbl_par_codigo_local='$codigo_local'");

			$resultado = mysqli_query($conector,$sql);
			$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			    header('Content-type: application/json');
			    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração ' . $erro_mysql));
			    exit;
			} 
		}
		else{
			$sql = "INSERT INTO tbl_parametro_nascimento (
				tbl_par_codigo_local,
				tbl_par_codigo_alfa,
				tbl_par_codigo_numerico,
				tbl_par_estacao_monta_inicial,
				tbl_par_estacao_monta_final,
				tbl_par_incluido_em,
				tbl_par_incluido_por,
				tbl_par_alterado_em,
				tbl_par_alterado_por,
				tbl_par_lixeira,
				tbl_par_lixeira_em,
				tbl_par_lixeira_por
			    ) 
			VALUES (
				'$codigo_local',
				'$codigo_alfa',
				'$codigo_numerico',
				'$inicio_estacao',
			    '$fim_estacao',
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
			   	exit;
			} 
    	}
	}
//}

header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;

?>