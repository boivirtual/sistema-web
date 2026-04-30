<?php
include "conecta_mysql.inc";

$codigo = $_POST['codigo'];  

$forma_rec_pag = mysqli_query($conector, "SELECT * FROM tbl_forma_rec_pag
                                                  WHERE tbl_forma_rec_pag_id='$codigo'");

$num_rows = mysqli_num_rows($forma_rec_pag);	

if ($num_rows!=0) {
	$registro_rec_pag = mysqli_fetch_object($forma_rec_pag);

    $valor[0] = $registro_rec_pag->tbl_forma_rec_pag_descricao;
    $valor[1] = $registro_rec_pag->tbl_forma_rec_pag_tipo;
	$valor[2] = $registro_rec_pag->tbl_forma_rec_pag_banco;
	$valor[3] = $registro_rec_pag->tbl_forma_rec_pag_agencia;
	$valor[4] = $registro_rec_pag->tbl_forma_rec_pag_conta;
	$valor[5] = $registro_rec_pag->tbl_forma_rec_pag_numero_cartao;
	$valor[6] = $registro_rec_pag->tbl_forma_rec_pag_saldo_inicial;
	$valor[7] = $registro_rec_pag->tbl_forma_rec_pag_data_saldo;

	$str=$valor[0] . '<|>';

	for ($i=1; $i<=7; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;
}					

$valor[0]=9;
$valor[1]='Forma de Pagamento/Recebimento não cadastrada.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>