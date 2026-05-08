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
		$sql = ("UPDATE tbl_pessoa SET tbl_pessoa_lixeira='1',
			                                   tbl_pessoa_lixeira_em='$data_sistema',
			                                   tbl_pessoa_lixeira_por='$nomeusuario'
		                                 WHERE tbl_pessoa_id='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$mysql_erro = mysqli_error($conector);

		if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao enviar o registro para a lixeira! ' . $mysql_erro;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
			//die("Erro ao resturar o registro da lixeira"  . "\n" . mysql_error() );
		}
		else {
	       $valor[0]=0;
	       $valor[1]='Registro enviado para lixeira! ';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
			//echo "<script> alert ('Registro restaurado da lixeira!'); location.href='form_cliente_fornecedor.php'</script>";
		}
        break;
    case 1:
		$sql = ("DELETE FROM tbl_pessoa WHERE tbl_pessoa_id=$codigo");
		$resultado = mysqli_query($conector,$sql);

		if (!$resultado){
			die("Erro ao excluir o registro da lixeira"  . "\n" . mysql_error($conector) );
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_cliente_fornecedor.php'</script>";
		}
        break;
    case 2:   
		$sql = ("UPDATE tbl_pessoa SET tbl_pessoa_lixeira='0',
			                        tbl_pessoa_lixeira_em=null,
			                        tbl_pessoa_lixeira_por=null
		                      WHERE tbl_pessoa_id='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$mysql_erro = mysqli_error($conector);

		if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao resturar o registro da lixeira! ' . $mysql_erro;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
			//die("Erro ao resturar o registro da lixeira"  . "\n" . mysql_error() );
		}
		else {
	       $valor[0]=0;
	       $valor[1]='Registro restaurado da lixeira! ';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
			//echo "<script> alert ('Registro restaurado da lixeira!'); location.href='form_cliente_fornecedor.php'</script>";
		}
	  	break;
	} 
		

	mysql_close($conector);

?>