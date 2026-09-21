<?php
include "conecta_mysql.inc";

$codigo = $_POST['codigo'];  

$conta_rec_pag = mysqli_query($conector, "SELECT * FROM tbl_conta_pagamento
                                                  WHERE tbl_conta_pagamento_id='$codigo'");

$num_rows = mysqli_num_rows($conta_rec_pag);	

if ($num_rows!=0) {
	$registro_rec_pag = mysqli_fetch_object($conta_rec_pag);

    $valor[0] = $registro_rec_pag->tbl_conta_pagamento_descricao;
    $valor[1] = $registro_rec_pag->tbl_conta_pagamento_tipo;
	$valor[2] = $registro_rec_pag->tbl_conta_pagamento_banco;
	$valor[3] = $registro_rec_pag->tbl_conta_pagamento_agencia;
	$valor[4] = $registro_rec_pag->tbl_conta_pagamento_conta;
	$valor[5] = $registro_rec_pag->tbl_conta_pagamento_numero_cartao;
	$valor[6] = $registro_rec_pag->tbl_conta_pagamento_saldo_inicial;
	$valor[7] = $registro_rec_pag->tbl_conta_pagamento_data_saldo;

	$str=$valor[0] . '<|>';

	for ($i=1; $i<=7; $i++){
	    $str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;
}					

$valor[0]=9;
$valor[1]='Conta Pagamento não cadastrada.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>