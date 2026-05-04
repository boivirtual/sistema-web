<?php 
function sonumero($str) {
	return preg_replace("/[^0-9]/", "", $str);
}

$codigo_usuario = $_POST['codigo_usuario'];
$nome_usuario = $_POST['nome_usuario'];
$cpf_usuario = sonumero($_POST['cpf_usuario']);
$email = $_POST['email_usuario'];
$senha_cad = $_POST['senha_cad'];
$senha_conf = $_POST['senha_conf'];
$endereco = $_POST['endereco_usuario'];
$numero = $_POST['numero_usuario'];
$complemento = $_POST['complemento_usuario'];
$bairro = $_POST['bairro_usuario'];
$cep = $_POST['cep_usuario'];
$cidade = $_POST['cidade_usuario'];
$estado = $_POST['estado_usuario'];

if (empty($nome_usuario)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Nome.'));
	exit;
}

if (empty($cpf_usuario)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o CPF.'));
	exit;
}

if (empty($email)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o E-mail.'));
	exit;
}

if($senha_cad != '' && ($senha_cad != $senha_conf)) { 
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'A Senhas informadas não conferem.'));
	exit;
}	

if (empty($cep)) {$cep=0;}

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$id_cliente = $_SESSION['id_cliente'];

include "conecta_mysql.inc";

$sql = ("UPDATE usuario SET nome_usuario='$nome_usuario',
	                        cpf_usuario='$cpf_usuario',
	                        email_usuario='$email',
	                        alterado_em_usuario='$data_sistema',
	                        alterado_por_usuario='$nomeusuario',
	                        endereco_usuario='$endereco',
	                        numero_usuario='$numero',
							complemento_usuario='$complemento',
	                        bairro_usuario='$bairro',
	                        cep_usuario='$cep',
	                        cidade_usuario='$cidade',
	                        estado_usuario='$estado'
 		              WHERE id_usuario='$codigo_usuario'");
$resultado = mysqli_query($conector_acesso,$sql);
$resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
$erro_mysql = mysqli_error($conector_acesso);

if (!$resultado){
	echo "<script> alert ('Erro na Alteração do registro.'); 
		      location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";

    //header('Content-type: application/json');
    //echo json_encode(array('error' => true, 'message' => 'Erro na Alteração do registro - ' . $erro_mysql));
	mysqli_close($conector_acesso);
    exit;
} 

if($senha_cad != '') { 
	$sql = ("UPDATE usuario SET senha_usuario='$senha_cad'
	                      WHERE id_usuario='$codigo_usuario'");

 	$resultado = mysqli_query($conector_acesso,$sql);
	$erro_mysql = mysqli_error($conector_acesso);
	if (!$resultado){
		echo "<script> alert ('Erro na Alteração da Senha . '); 
		      location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";

	    //header('Content-type: application/json');
	    //echo json_encode(array('error' => true, 'message' => 'Erro na Alteração da Senha - ' . $erro_mysql));
		mysqli_close($conector_acesso);
	    exit;
	} 
}

if ($_FILES['foto']['error']==4) {
	echo "<script> alert ('Registro Alterado com sucesso!'); location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";

	mysqli_close($conector_acesso);
	exit;
}
else {	
	$altura = "26";
	$largura = "39";

	switch($_FILES['foto']['type']):
		case 'image/jpeg';
		case 'image/pjpeg';
		    $nome_foto = 'img/fotos/' . $id_cliente .'-'. $codigo_usuario . '.jpg';

			$imagem_temporaria = imagecreatefromjpeg($_FILES['foto']['tmp_name']);
			
			$largura_original = imagesx($imagem_temporaria);
			
			$altura_original = imagesy($imagem_temporaria);
			
			$nova_largura = $largura ? $largura : floor (($largura_original / $altura_original) * $altura);
			
			$nova_altura = $altura ? $altura : floor (($altura_original / $largura_original) * $largura);
			
			$imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);

			imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);
			
			imagejpeg($imagem_redimensionada, $nome_foto);

		break;
		
		//Caso a imagem seja extensão PNG cai nesse CASE
		case 'image/png':
		case 'image/x-png';

		    $nome_foto = 'img/fotos/' . $id_cliente .'-'. $codigo_usuario . '.png';

			$imagem_temporaria = imagecreatefrompng($_FILES['foto']['tmp_name']);
			
			$largura_original = imagesx($imagem_temporaria);
			$altura_original = imagesy($imagem_temporaria);
			
			/* Configura a nova largura */
			$nova_largura = $largura ? $largura : floor(( $largura_original / $altura_original ) * $altura);

			/* Configura a nova altura */
			$nova_altura = $altura ? $altura : floor(( $altura_original / $largura_original ) * $largura);
			
			/* Retorna a nova imagem criada */
			$imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);
			
			/* Copia a nova imagem da imagem antiga com o tamanho correto */
			//imagealphablending($imagem_redimensionada, false);
			//imagesavealpha($imagem_redimensionada, true);

			imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);
			
			//função imagejpeg que envia para o browser a imagem armazenada no parâmetro passado
			imagepng($imagem_redimensionada, $nome_foto);
		break;
	endswitch;

 	$sql = ("UPDATE usuario SET foto_usuario='$nome_foto'
	                       WHERE id_usuario='$codigo_usuario'");

	$resultado = mysqli_query($conector_acesso,$sql);
	$erro_mysql = mysqli_error($conector_acesso);

	if (!$resultado){
		echo "<script> alert ('Erro ao gravar a imagem no banco de dados. '); 
		      location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";
		mysqli_close($conector_acesso);
	   	exit;
	} 

}

echo "<script> alert ('Registro Alterado com sucesso!'); location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";

mysqli_close($conector_acesso);

?>