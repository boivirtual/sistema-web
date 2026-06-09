<?php
include "conecta_mysql.inc";

$codigo_contato = substr($_POST['codigo_contato_pessoa'], 0,9);    
$codigo_pessoa = substr($_POST['codigo_contato_pessoa'], 9,9);    

for ($i = 0; $i <= 30; $i++) {
	$valor[$i]=0;
}

if ($codigo_contato==0) {
	$rs = mysqli_query($conector, "SELECT * FROM cliente_fornecedor
							   WHERE cliente_id='$codigo_pessoa'");
						
	$fila = mysqli_fetch_object($rs);

	$valor[0]=$fila->cliente_contato;
	$valor[1]=$fila->cliente_ddd;
	$valor[2]=$fila->cliente_telefone;
	$valor[3]=$fila->cliente_email;
	$valor[4]=$fila->cliente_cargo_contato;
}
else {
	$rs = mysqli_query($conector, "SELECT * FROM contatos_cliente_fornecedor
							   WHERE contato_id='$codigo_contato' and  contato_cliente_id='$codigo_pessoa'");
						
	$fila = mysqli_fetch_object($rs);

	$valor[0]=$fila->contato_cliente_nome;
	$valor[1]=$fila->contato_cliente_ddd;
	$valor[2]=$fila->contato_cliente_telefone;
	$valor[3]=$fila->contato_cliente_email;
	$valor[4]=$fila->contato_cliente_cargo;

}

$str=$valor[0] . '<|>';

for ($i=1; $i<=30; $i++){
    $str.=$valor[$i] . '<|>';
}
echo $str; 

mysqli_free_result($rs); 
mysqli_close($conector);
?>