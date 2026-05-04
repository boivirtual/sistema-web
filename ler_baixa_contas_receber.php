<?php
include "conecta_mysql.inc";

$numero_ctr = $_POST['numero_ctr'];      
$parcela_ctr = $_POST['parcela_ctr'];      
$ctr_id = $_POST['ctr_id'];      
$valor_pagamento=0.00;

$valor[0]=0;
$valor[1]=0;
$valor[2]=0;
$valor[3]=0;
$valor[4]=0;
$valor[5]=0;
$valor[6]=0;
$valor[7]=0;
$valor[8]=0;
$valor[9]=0;
$valor[10]=0;
$valor[11]=0;

$rs = mysqli_query ($conector, "SELECT * FROM contas_receber 
                                        WHERE ctr_id='$ctr_id'");
$registro = mysqli_fetch_array($rs);
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
	$valor[0] = $registro["ctr_valor_parcela"];
	$valor[1] = $registro["ctr_valor_desconto"];
	$valor[2] = $registro["ctr_valor_juros"];
	$valor[3] = $registro["ctr_valor_acrescimo"];
	$valor[5] = $registro["ctr_numero_doc"];
	$valor[6] = $registro["ctr_parcela"];
	$valor[7] = $registro["ctr_codigo_cliente_fornecedor"];
	$valor[8] = $registro["ctr_data_vencimento"];
	$valor[9] = $registro["ctr_nome_cliente"];
}

//  Calcula o valor da baixa --------------------------------------------------------------------------	
$rs = mysqli_query ($conector, "SELECT * FROM baixa_contas_receber WHERE bcr_id='$ctr_id'");
	
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($registro = mysqli_fetch_array($rs)){
		$valor_pagamento=$valor_pagamento + $registro["bcr_valor_pagamento"];
	}
	$valor[4]=$valor_pagamento;
}
else {
	$valor[4]=$valor_pagamento;
}


$str = $valor[0]. '<|>' . $valor[1]. '<|>'. $valor[2]. '<|>' . $valor[3]. '<|>' . $valor[4]. '<|>' . $valor[5]. '<|>' .
       $valor[6]. '<|>' . $valor[7]. '<|>' . $valor[8]. '<|>' . $valor[9]. '<|>' . $valor[10]. '<|>' . $valor[11]. '<|>'; 
echo $str; 

mysqli_close($conector);
?>