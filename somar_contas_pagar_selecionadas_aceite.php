<?php

$mensagem = 0;

include "conecta_mysql.inc";

$grupo_contas = $_POST['grupo_contas'];

$matriz_contas = explode("<|>", $grupo_contas);
$quantidade_itens = count($matriz_contas);
$total_selecionado=0;
$mensagem = '';

/*for($i=0; $i < $quantidade_itens; $i++) {
	$ctp_id = substr($matriz_contas[$i],18,15);

	$mensagem.= $ctp_id . ' ';
}

echo $mensagem;
mysqli_close($conector);
exit;*/

for($i=0; $i < $quantidade_itens; $i++) {
    /*$codigo_fazenda = substr($matriz_contas[$i],0,9);
    $codigo_fornecedor = substr($matriz_contas[$i],9,9);
    $codigo_conta = substr($matriz_contas[$i],18,7);
    $emissao = substr($matriz_contas[$i],25,8);
	$ctp_id = substr($matriz_contas[$i],33,15);*/
	$ctp_id = $matriz_contas[$i];

    $ssql= "SELECT * FROM contas_pagar 
    WHERE ctp_id='$ctp_id'";

    /*WHERE ctp_numero_documento='$ctp_id' AND 
          ctp_codigo_fornecedor='$codigo_fornecedor' AND
          ctp_codigo_fazenda='$codigo_fazenda' AND 
          ctp_codigo_conta='$codigo_conta' AND 
          ctp_data_emissao='$emissao'";*/

	$conta_pagar = mysqli_query($conector,$ssql);
	$num_rows_contas = mysqli_num_rows($conta_pagar);

	if ($num_rows_contas != 0) {
		while ($registro_conta = mysqli_fetch_object($conta_pagar)) {
	        $vlr_parcela = $registro_conta->ctp_valor_parcela;
	        $vlr_juros = $registro_conta->ctp_valor_juros;
	        $vlr_desconto = $registro_conta->ctp_valor_desconto;
	        $vlr_outro = $registro_conta->ctp_outro_valor;
	        $total_parcela = $vlr_parcela - $vlr_desconto + $vlr_juros + $vlr_outro;

	        $total_selecionado+=$total_parcela;
		}
	}
}

if ($total_selecionado!=0){
    $mensagem = $total_selecionado;
}
else {
    $mensagem = '';
}
	
echo $mensagem;
mysqli_close($conector);

?>