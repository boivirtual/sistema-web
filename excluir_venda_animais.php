<?php
	$numero_venda = $_REQUEST['id'];

	include "conecta_mysql.inc";
   
	$sql = ("DELETE FROM contas_receber 
			  WHERE ctr_numero_doc='$numero_venda'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
	    $valor[1]='Erro ao excluir o registro de contas a receber! ' . $erro_mysql;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	$sql = "UPDATE tbl_movimentacao SET
   			tbl_movimentacao_situacao='N',
   			tbl_movimentacao_aceite_financeiro_em=null,
   			tbl_movimentacao_aceite_financeiro_por=null,
   			tbl_movimentacao_codigo_venda=0
	    WHERE tbl_movimentacao_codigo_venda ='$numero_venda'";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
	    $valor[1]='Erro ao atualizar o registro da movimentação! ' . $erro_mysql;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	$sql = ("DELETE FROM tbl_item_venda WHERE tbl_ite_venda_numero_id ='$numero_venda'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
	    $valor[1]='Erro ao excluir o registro do item da venda! ' . $erro_mysql;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	$sql = ("DELETE FROM tbl_venda WHERE tbl_venda_id ='$numero_venda'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    $valor[0]=9;
	    $valor[1]='Erro ao excluir o registro da venda! ' . $erro_mysql;
	    $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	    die ($str);
	}

	$valor[0]=0;
	$valor[1]='Registro excluido com sucesso! ';
	$str = $valor[0] . '<|>' . $valor[1] . '<|>';
	die ($str);

	mysqli_close($conector);

?>