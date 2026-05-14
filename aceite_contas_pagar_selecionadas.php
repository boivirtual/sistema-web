<?php

@ session_start();
$usuario_agendamento = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;

$grupo_contas = $_POST['grupo_contas'];
$matriz_contas = explode("<|>", $grupo_contas);
$quantidade_itens = count($matriz_contas);

include "conecta_mysql.inc";
			
for($i=0; $i < $quantidade_itens; $i++) {
    /*$codigo_fazenda = substr($matriz_contas[$i],0,9);
    $codigo_fornecedor = substr($matriz_contas[$i],9,9);
    $codigo_conta = substr($matriz_contas[$i],18,7);
    $emissao = substr($matriz_contas[$i],25,8);
    $numero_ctp = substr($matriz_contas[$i],33,15);*/
    $ctp_id = $matriz_contas[$i];

	$sql = ("UPDATE contas_pagar SET ctp_aceite='S',
   	                                 ctp_data_aceite='$data_sistema',
            				 		 ctp_usuario_aceite='$usuario_agendamento'
                               WHERE ctp_id='$ctp_id'");

                               /*WHERE ctp_numero_documento='$numero_ctp' AND 
                                     ctp_codigo_fornecedor='$codigo_fornecedor' AND
                                     ctp_codigo_fazenda='$codigo_fazenda' AND 
                                     ctp_codigo_conta='$codigo_conta' AND 
                                     ctp_data_emissao='$emissao'*/

    $resultado = mysqli_query($conector, $sql);
    if (!$resultado) {
    	$mensagem = "Erro no aceite da conta" . "\n" . mysqli_error($conector);
	}
}

echo $mensagem;
mysqli_close($conector);

?>