<?php
include "conecta_mysql.inc";

$tbl_ctp = mysqli_query($conector, "SELECT * FROM contas_pagar");  

$num_rows = mysqli_num_rows($tbl_ctp);

if ($num_rows!=0){
    while($reg_ctp = mysqli_fetch_object($tbl_ctp)){
        $ctp_id = $reg_ctp->ctp_id;
        $ctp_documento = $reg_ctp->ctp_numero_doc;
        $ctp_forncedor = $reg_ctp->ctp_codigo_fornecedor;
        $ctp_parcela = $reg_ctp->ctp_parcela;

        $tbl_bcp = mysqli_query($conector, "SELECT * FROM baixa_contas_pagar
            WHERE bcp_numero_id='$ctp_documento' AND 
                  bcp_codigo_fornecedor='$ctp_forncedor' AND 
                  bcp_parcela='$ctp_parcela'");

        $num_rows = mysqli_num_rows($tbl_bcp);  
        
        if ($num_rows!=0) {
            while ($reg_bcp = mysqli_fetch_object($tbl_bcp)) {
                $bcp_documento = $reg_bcp->bcp_numero_id;
                $bcp_forncedor = $reg_bcp->bcp_codigo_fornecedor;
                $bcp_parcela = $reg_bcp->bcp_parcela;
                $sequencia = $reg_bcp->bcp_sequencia_pagamento;

                $sql = "UPDATE baixa_contas_pagar SET
                    bcp_id='$ctp_id'

                    WHERE bcp_numero_id='$bcp_documento' AND 
                          bcp_codigo_fornecedor='$bcp_forncedor' AND 
                          bcp_parcela='$bcp_parcela' AND 
                          bcp_sequencia_pagamento='$sequencia'";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    echo 'erro doc: ' . $bcp_documento .' '.
                                        $bcp_forncedor .' '.
                                        $bcp_parcela .' '.
                                        $sequencia .' '. $erro_mysql . '</br>';
                }
                else {
                    echo 'Gravado doc: ' . $bcp_documento .' '.
                                           $bcp_forncedor .' '.
                                           $bcp_parcela .' '.
                                           $sequencia . '</br>';
                }   
            }
        }
    }
}
else {
    echo 'Não achei nada'. '</br>';
}


echo 'Fim do processamento'; 

?>