<?php
	$id_item = $_POST['id_item'];
	$num_item = $_POST['item'];
	$numero_pesagem = $_POST['numero_pesagem'];

	include "conecta_mysql.inc";

	$sql = ("DELETE FROM tbl_item_pesagem
						   WHERE tbl_ite_pesagem_numero_id = '$numero_pesagem' AND 
						         tbl_ite_pesagem_numero_item = '$num_item' AND 
						         tbl_ite_pesagem_codigo_id_animal = '$id_item'");
	$resultado = mysqli_query($conector,$sql);
	$mysql_erro = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
	    $valor[1]='Erro ao enviar o registro para a lixeira! ' . $mysql_erro;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
						   WHERE tbl_ite_pesagem_numero_id='$numero_pesagem' AND 
						   		 tbl_ite_pesagem_peso!=0");
	$qtd_item_pesados = mysqli_num_rows($rs);

	$sql = ("UPDATE tbl_pesagem SET tbl_pesagem_qtd_animais_pesados='$qtd_item_pesados'
		                      WHERE tbl_pesagem_id='$numero_pesagem'");

	$resultado = mysqli_query($conector,$sql);
	$mysql_erro = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
        $valor[1]='Erro atualizar a quantidade de itens pesados! ' . $mysql_erro;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}
	else {
	    $valor[0]=0;
	    $valor[1]='Registro enviado para lixeira! ';
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
    }
	mysqli_close($conector);
?>