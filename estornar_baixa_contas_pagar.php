<?php
include "conecta_mysql.inc";

$bcp_id = $_POST['bcp_id'];
$bcp_sequencia = $_POST['bcp_sequencia'];

/*$agendamento = 0;

$codigo_for = substr($bcp_chave_baixa, 0, 9);
$parcela = substr($bcp_chave_baixa, 9, 3);
$sequencia = substr($bcp_chave_baixa, 12, 2);
$numero = substr($bcp_chave_baixa, 14, 15);
*/

$mensagem=0;

/*if ($agendamento!=0){
    $sql = ("DELETE FROM baixa_contas_pagar WHERE bcp_numero_agendamento='$agendamento'");

    $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));

    $sql = ("UPDATE contas_pagar_agendamento SET ctp_age_situacao=''
                                           WHERE ctp_age_numero_agendamento='$agendamento'");
    $resultado = mysqli_query($conector,$sql) or die(mysqli_error($conector));

}
else {*/
    /*$sql = ("DELETE FROM baixa_contas_pagar WHERE bcp_numero_id='$numero' and 
                                                  bcp_parcela='$parcela' and 
                                                  bcp_codigo_fornecedor = '$codigo_for' and
                                                  bcp_sequencia_pagamento='$sequencia'");*/
    $sql = ("DELETE FROM baixa_contas_pagar 
        WHERE bcp_id='$bcp_id' AND
              bcp_sequencia_pagamento = '$bcp_sequencia'");

    $resultado = mysqli_query($conector,$sql);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado) {
        $mensagem = "Erro no extorno da baixa" . "\n" . $erro_mysql;
        echo $mensagem;
        exit;
    }
//}


/*if ($agendamento!=0){
    $conta_pagar = mysqli_query($conector, "SELECT * FROM contas_pagar 
                                          WHERE ctp_numero_agendamento='$agendamento'");
    $num_rows = mysqli_num_rows($conta_pagar);

    if ($num_rows!=0){
        while($reg_ctp = mysqli_fetch_object($conta_pagar)){
            $ctp_numero_doc = $reg_ctp->ctp_numero_doc;
            $ctp_parcela = $reg_ctp->ctp_parcela;
            $ctp_codigo_fornecedor = $reg_ctp->ctp_codigo_fornecedor;

            $conta_baixada = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar 
                                                  WHERE bcp_numero_id='$ctp_numero_doc' and 
                                                        bcp_parcela='$ctp_parcela' and
                                                        bcp_codigo_fornecedor = '$ctp_codigo_fornecedor'");

            $num_rows_bcp = mysqli_num_rows($conta_baixada);

            if ($num_rows_bcp != 0) {
                $sql = ("UPDATE contas_pagar SET ctp_situacao='C'
                                           WHERE ctp_numero_doc='$ctp_numero_doc' and 
                                                 ctp_parcela='$ctp_parcela' and 
                                                 ctp_codigo_fornecedor='$ctp_codigo_fornecedor'");
            } else {
                $sql = ("UPDATE contas_pagar SET ctp_situacao=''
                                           WHERE ctp_numero_doc='$ctp_numero_doc' and 
                                                 ctp_parcela='$ctp_parcela' and
                                                 ctp_codigo_fornecedor='$ctp_codigo_fornecedor'");
            }
            $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));

            if (!$resultado) {
                $mensagem = "Erro na atualização da conta" . "\n" . mysqli_error($conector);
                echo $mensagem;
            } else {
                $mensagem = 0;
                echo $mensagem;
            }
        }
    }
}
else {*/
    $conta_baixada = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar 
        WHERE bcp_id='$bcp_id'");

    $num_rows = mysqli_num_rows($conta_baixada);

    if ($num_rows != 0) {
        $sql = ("UPDATE contas_pagar SET ctp_situacao='C'
                                   WHERE ctp_id='$bcp_id'");
    } else {
        $sql = ("UPDATE contas_pagar SET ctp_situacao=''
                                   WHERE ctp_id='$bcp_id'");
    }
    $resultado = mysqli_query($conector, $sql) or die(mysqli_error($conector));

    if (!$resultado) {
        $mensagem = "Erro na atualização da conta" . "\n" . mysqli_error($conector);
        echo $mensagem;
    } else {
        $mensagem = 0;
        echo $mensagem;
    }
//}

mysqli_close($conector);
?>


