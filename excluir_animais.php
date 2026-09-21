<?php
	$codigo = $_REQUEST['id'];
	$opcao = $_REQUEST['opcao'];
    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	//$rs = mysqli_query($conector, "SELECT * FROM animais WHERE animal_codigo=$codigo");  
	//$num_registros = mysqli_num_rows ($rs); 
	

	switch ($opcao) {
    case 0:
		$sql = ("UPDATE animais SET animal_registro_lixeira='1',
			                             animal_lixeira_em='$data_sistema',
			                             animal_lixeira_por='$nomeusuario'
		                           WHERE animal_codigo='$codigo'");

		$resultado = mysqli_query($conector,$sql) or die(mysqli_error());

		if (!$resultado){
			die("Erro ao enviar o registro para a lixeira"  . "\n" . mysql_error() );
		}
		else {
			echo "<script> alert ('Registro enviado para lixeira!'); location.href='form_tabela_animais.php'</script>";
		}
        break;
    case 1:
		$sql = ("DELETE FROM animais WHERE animal_codigo=$codigo");
		$resultado = mysqli_query($conector,$sql) or die(mysqli_error());

		if (!$resultado){
			die("Erro ao excluir o registro da lixeira"  . "\n" . mysql_error() );
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_animais.php'</script>";
		}
        break;
    case 2:   
		$sql = ("UPDATE animais SET animal_registro_lixeira='0',
			                        animal_lixeira_em='',
			                        animal_lixeira_por=''
		                      WHERE animal_codigo='$codigo'");

		$resultado = mysqli_query($conector,$sql) or die(mysqli_error());

		if (!$resultado){
			die("Erro ao remover o registro da lixeira"  . "\n" . mysql_error() );
		}
		else {
			echo "<script> alert ('Registro removido da lixeira!'); location.href='form_tabela_animais.php'</script>";
		}
	  	break;
	} 

				

	mysql_close($conector);

?>