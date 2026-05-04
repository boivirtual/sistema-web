<?php

	$valor[0]=0;
	$valor[1]='';

	$id_ctr = $_POST['id_ctr'];
    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	$sql = ("UPDATE contas_receber SET ctr_lixeira='1',
	    ctr_lixeira_em='$data_sistema',
	    ctr_lixeira_por='$nomeusuario'
	    WHERE ctr_id='$id_ctr'");

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
	
	mysql_close($conector);

?>