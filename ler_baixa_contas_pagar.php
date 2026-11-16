<?php
include "conecta_mysql.inc";

//$numero_ctp = $_POST['numero_ctp'];      
//$parcela_ctp = $_POST['parcela_ctp'];      
$ctp_id = $_POST['ctp_id'];      
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

//$rs = mysqli_query ($conector, "SELECT * FROM contas_pagar 
  //  WHERE ctp_numero_doc='$numero_ctp' and ctp_parcela='$parcela_ctp' ");
$rs = mysqli_query ($conector, "SELECT * FROM contas_pagar 
    WHERE ctp_id='$ctp_id'");

$registro = mysqli_fetch_array($rs);
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
	$valor[0] = $registro["ctp_valor_parcela"];
	$valor[1] = $registro["ctp_valor_desconto"];
	$valor[2] = $registro["ctp_valor_juros"];
	$valor[3] = $registro["ctp_outro_valor"];
	$valor[5] = $registro["ctp_numero_doc"];
	$valor[6] = $registro["ctp_parcela"];
	$valor[7] = $registro["ctp_codigo_fornecedor"];
	$valor[8] = $registro["ctp_data_vencimento"];
	$valor[9] = $registro["ctp_nome_fornecedor"];
}

//  Calcula o valor da baixa --------------------------------------------------------------------------	
$rs = mysqli_query ($conector, "SELECT * FROM baixa_contas_pagar WHERE bcp_id='$ctp_id'");
	
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($registro = mysqli_fetch_array($rs)){
		$valor_pagamento=$valor_pagamento + $registro["bcp_valor_pagamento"];
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