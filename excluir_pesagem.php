<?php
	$documento = $_REQUEST['id'];

	include "conecta_mysql.inc";

	$sql = ("DELETE FROM tbl_item_pesagem WHERE tbl_ite_pesagem_numero_id ='$documento'");
	$resultado = mysqli_query($conector,$sql);
	$mysql_erro = mysqli_error($conector);

	if (!$resultado){
    	$valor[0]=9;
       	$valor[1]='Erro ao excluir o itens da pesagem! ' . $mysql_erro;
       	$str = $valor[0] . '<|>' . $valor[1] . '<|>';
       	die ($str);
	}

	$sql = ("DELETE FROM tbl_pesagem WHERE tbl_pesagem_id='$documento'");
	$resultado = mysqli_query($conector,$sql);
	$mysql_erro = mysqli_error($conector);

	if (!$resultado){
    	$valor[0]=9;
       	$valor[1]='Erro ao excluir o registro da pesagem! ' . $mysql_erro;
       	$str = $valor[0] . '<|>' . $valor[1] . '<|>';
       	die ($str);
	}
	else {
        $valor[0]=0;
        $valor[1]='Registro excluido com sucesso! ';
        $str = $valor[0] . '<|>' . $valor[1] . '<|>';
        die ($str);
	}


	mysqli_close($conector);

?>