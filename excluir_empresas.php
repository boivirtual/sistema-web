<?php

	$valor[0]=0;
	$valor[1]='';

	$codigo = $_POST['id'];
	$opcao = $_POST['opcao'];
    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 0:
		$sql = ("UPDATE tbl_empresa SET tbl_empresa_lixeira='1',
			                                   tbl_empresa_lixeira_em='$data_sistema',
			                                   tbl_empresa_lixeira_por='$nomeusuario'
		                                 WHERE tbl_empresa_id='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$mysql_erro = mysqli_error($conector);

		if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao enviar o registro para a lixeira! ' . $mysql_erro;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
		else {
	       $valor[0]=0;
	       $valor[1]='Registro enviado para lixeira! ';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
        break;
    case 1:
		$sql = ("DELETE FROM tbl_empresa WHERE tbl_empresa_id=$codigo");
		$resultado = mysqli_query($conector,$sql);

		if (!$resultado){
			die("Erro ao excluir o registro da lixeira"  . "\n" . mysql_error($conector) );
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_empresas.php'</script>";
		}
        break;
    case 2:   
		$sql = ("UPDATE tbl_empresa SET tbl_empresa_lixeira='0',
			                        tbl_empresa_lixeira_em=null,
			                        tbl_empresa_lixeira_por=null
		                      WHERE tbl_empresa_id='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$mysql_erro = mysqli_error($conector);

		if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao resturar o registro da lixeira! ' . $mysql_erro;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
		else {
	       $valor[0]=0;
	       $valor[1]='Registro restaurado da lixeira! ';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
	  	break;
	} 
		
	mysql_close($conector);
?>