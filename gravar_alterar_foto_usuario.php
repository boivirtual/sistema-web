<?php 
function sonumero($str) {
	return preg_replace("/[^0-9]/", "", $str);
}

$codigo_usuario = $_POST['codigo_usuario'];

@ session_start(); 
$id_cliente = $_SESSION['id_cliente'];

include "conecta_mysql.inc";

if ($_FILES['foto']['error']==4) {
	echo "<script> alert ('Não foi possível fazer upload da foto.'); location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";

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

echo "<script> alert ('Foto Alterada com sucesso!'); location.href='form_usuario_editar.php?id=$codigo_usuario'</script>";

mysqli_close($conector_acesso);

?>