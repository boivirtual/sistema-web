<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $contador = 0;
    $contador_abr = 0;
    $contador_pago = 0;
    $contator_nao_achei = 0;

    $tbl_pagar = mysqli_query($conector, "SELECT * FROM contas_pagar
        ORDER BY ctp_id ASC");

    while ($reg_ctp = mysqli_fetch_object($tbl_pagar)) {
        $ctp_id = $reg_ctp->ctp_id;
        $fornecedor = $reg_ctp->ctp_codigo_fornecedor;
        $ctp_numero_doc = $reg_ctp->ctp_numero_doc;
        $ctp_numero_documento = $reg_ctp->ctp_numero_documento;
        $ctp_parcela = $reg_ctp->ctp_parcela;
        $sit = $reg_ctp->ctp_situacao;

        if ($sit=='P') {
            $sit='PAGA';
        }
        else if ($sit=='C') {
            $sit='PAGA PARCIAL';
        }
        else {
            $sit='ABERTO';
        }

        $tbl_conta_paga = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
            WHERE bcp_numero_id = '$ctp_numero_doc' AND 
                  bcp_codigo_fornecedor = '$fornecedor' AND 
                  bcp_parcela='$ctp_parcela'");

        $num_rows_contas = mysqli_num_rows($tbl_conta_paga);

        if ($num_rows_contas!=0) {
            $sql = "UPDATE baixa_contas_pagar SET
                bcp_id='$ctp_id'
                WHERE bcp_numero_id = '$ctp_numero_doc' AND 
                    bcp_codigo_fornecedor = '$fornecedor' AND 
                    bcp_parcela='$ctp_parcela'";
                    
            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);
            if (!$resultado){
                print_r($ctp_id . ' - '. $erro_mysql .'</br>');
            }
        }
        else {
            $contador_abr++;
        }

        if ($num_rows_contas>1) {
            print_r($ctp_id . ' - ' . $ctp_numero_doc . ' - ' . $fornecedor . ' - ' . $ctp_parcela . ' DUPLICADO</br>');
        }
        
        if ($num_rows_contas==0)  {
            print_r($ctp_id . ' - ' . $ctp_numero_doc . ' Numero Documento ' . $ctp_numero_documento . ' NÃO ACHEI BCP ' . '</br>');
                $contator_nao_achei++;
        }
        else {
            if ($ctp_numero_doc=='') {
                print_r($ctp_id . ' - ' . $ctp_numero_doc . ' Numero Documento ' . $ctp_numero_documento . ' Sit: ' . $sit .' '.$num_rows_contas.' ACHEI</br>');
            }
            else {
                print_r($ctp_id . ' - ' . $ctp_numero_doc . ' Sit: ' . $sit.' '.$num_rows_contas  . '  ACHEI</br>');
            }

        }

        $contador++;
    }

    print_r($contador . '</br>');

    print_r('Pagos: ' . $contador_pago . '</br>');
    print_r('Abertos: ' . $contador_abr . '</br>');
    print_r('Não Achei: ' . $contator_nao_achei . '</br>');


/*    $contador = 0;
    $contador_pago=0;
    $contador_nao_achei=0; 

    $tbl_pagar = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
        ORDER BY bcp_numero_id ASC");

    while ($reg_bcp = mysqli_fetch_object($tbl_pagar)) {
        $bcp_id = $reg_bcp->bcp_numero_id;

        $tbl_conta = mysqli_query($conector, "SELECT * FROM contas_pagar
            WHERE ctp_numero_doc = '$bcp_id'");

        $num_rows_contas = mysqli_num_rows($tbl_conta);

        if ($num_rows_contas==0) {
            $tbl_conta = mysqli_query($conector, "SELECT * FROM contas_pagar
                WHERE ctp_numero_documento = '$bcp_id'");

            $num_rows_contas = mysqli_num_rows($tbl_conta);
        }


        if ($num_rows_contas!=0) {
            $contador_pago++;
        }
        else {
            $contador_nao_achei++;
        }

        
        if ($num_rows_contas==0)  {
            print_r($bcp_id . ' - Vencimento: ' . $reg_bcp->bcp_data_pagamento .' - NAO ACHEI'.'</br>');
            $sql = ("DELETE FROM baixa_contas_pagar WHERE bcp_numero_id='$bcp_id'");
            $resultado = mysqli_query($conector,$sql);
            $mysql_erro = mysqli_error($conector);

            if (!$resultado){
                print_r($mysql_erro .'</br>');
            }

        }
        else {
            print_r($bcp_id  . ' NUM rows ' . $num_rows_contas   . '</br>');
        }

        $contador++;
    }

    print_r($contador . '</br>');

    print_r('Pagos: ' . $contador_pago . '</br>');
    print_r('Não achei: ' . $contador_nao_achei . '</br>');
*/

?>
