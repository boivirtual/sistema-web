<?php

	$valor[0]=0;
	$valor[1]='';

	$ctp_id = $_POST['id'];
	$opcao = $_POST['opcao'];
    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 1:
		$sql = ("DELETE FROM contas_pagar WHERE ctp_id='$ctp_id'");
		$resultado = mysqli_query($conector,$sql);
		$mysql_erro = mysqli_error($conector);

		if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao excluir o registro! ' . $mysql_erro;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
		else {
	       $valor[0]=0;
	       $valor[1]='Registro excluido com sucesso! ';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
        break;
	} 
		

	mysql_close($conector);

?>