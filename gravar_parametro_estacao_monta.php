<?php 

$array_fazenda= $_POST['array_codigo_fazenda'];
//$array_codigo_alfa = $_POST['array_codigo_alfa'];
//$array_codigo_numerico = $_POST['array_codigo_numerico'];
$array_codigo_parametro = $_POST['array_codigo_parametro'];
$array_nome_estacao = $_POST['array_nome_estacao'];
$array_inicio_estacao = $_POST['array_inicio_estacao'];
$array_fim_estacao = $_POST['array_fim_estacao'];

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

$array_local = explode("!", $array_fazenda);
//$array_alfa = explode("!", $array_codigo_alfa);
//$array_numerico = explode("!", $array_codigo_numerico);
$array_id_parametro = explode("!", $array_codigo_parametro);
$array_desc_estacao = explode("!", $array_nome_estacao);
$array_data_inicial = explode("!", $array_inicio_estacao);
$array_data_final = explode("!", $array_fim_estacao);

for($i=0; $i < count($array_local); $i++) {
	$codigo_id_parametro = $array_id_parametro[$i];
	$codigo_local = $array_local[$i];
	//$codigo_alfa = $array_alfa[$i];
	//$codigo_numerico = $array_numerico[$i];
	$nome_estacao = $array_desc_estacao[$i];
	$data_inicial = $array_data_inicial[$i];
	$data_final = $array_data_final[$i];

    //if ($codigo_numerico!='') {
   		//$codigo_numerico = intval($codigo_numerico);
   	//}

   	if ($nome_estacao!='') {
		if ($codigo_id_parametro!=0) {
			$sql = ("UPDATE tbl_parametro_estacao_monta SET 
							tbl_par_estacao_nome='$nome_estacao',
			     			tbl_par_codigo_alfa=null,
							tbl_par_codigo_numerico=null,
							tbl_par_estacao_monta_inicial='$data_inicial',
							tbl_par_estacao_monta_final='$data_final',
							tbl_par_alterado_em='$data_sistema',
							tbl_par_alterado_por='$nomeusuario'
						WHERE tbl_par_estacao_id ='$codigo_id_parametro'");

			$resultado = mysqli_query($conector,$sql);
			$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
			    header('Content-type: application/json');
			    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alateração ' . $erro_mysql));
			    exit;
			} 
		}
		else {
			$sql = "INSERT INTO tbl_parametro_estacao_monta (
					tbl_par_estacao_nome,
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
					'$nome_estacao',
					'$codigo_local',
					null,
					null,
					'$data_inicial',
				    '$data_final',
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
}

header('Content-type: application/json');
echo json_encode($resposta);
mysqli_close($conector);
exit;

?>