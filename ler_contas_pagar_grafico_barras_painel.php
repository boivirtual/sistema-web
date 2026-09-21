<?php

include "conecta_mysql.inc";

$local = $_GET["local"];
$codigo_cc = $_GET["codigo_cc"];
//$local = '000000000';
//$codigo_cc = 1;

if ($local=='000000000') {
    $wlocal = '';
}
else {
    $wlocal = " AND ctp_codigo_fazenda IN(";
    $wlocal.= $local;
    $wlocal.= ")";
}

$data_inicial = $_GET["data_inicial"];
$data_final = $_GET["data_final"];

$partes = explode("-", $data_final);
$ano_final = $partes[0];
$mes_final = $partes[1];
$dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

$data_inicial = $data_inicial . '-01';
$data_final = $data_final . '-' . $dia_final;

$arr = [];
$id_subconta_anterior = 0;
$total_conta = 0;

$contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas 
    WHERE tbl_plano_contas_debito_credito='D' AND 
          tbl_plano_contas_lixeira=0 AND 
          (tbl_plano_contas_nivel = 2 OR tbl_plano_contas_nivel = 3)
          ORDER BY tbl_plano_contas_codigo_id ASC");

$num_rows = mysqli_num_rows($contas);  

if ($num_rows!=0) {
    while ($reg_conta = mysqli_fetch_object($contas)) {
        $id = $reg_conta->tbl_plano_contas_codigo_id;
        $nivel = $reg_conta->tbl_plano_contas_nivel;

        if (substr($id, 0, 1)==3) {
            if ($nivel==2) {
                $id_subconta = $reg_conta->tbl_plano_contas_codigo_id;

                if ($id_subconta_anterior!=$id_subconta && $id_subconta_anterior!=0) {

                    if ($total_conta!=0) {
                        $p = (object)[
                            'descricao' => $descricao_conta,
                            'valor' => intval($total_conta),
                        ];
                        $arr[] = $p;
                    }

                    /*print_r($id_subconta_anterior .' '.
                            $descricao_conta .' '.
                            $total_conta .'</br>');*/

                    $id_subconta_anterior=$id_subconta;
                    $descricao_conta=$reg_conta->tbl_plano_contas_descricao;
                    $total_conta = 0;
                }
                else {
                    $id_subconta_anterior=$id_subconta;
                    $descricao_conta=$reg_conta->tbl_plano_contas_descricao;
                    $total_conta = 0;
                }
            }

            $ctp = mysqli_query($conector, "SELECT * FROM contas_pagar 
                WHERE ctp_codigo_conta='$id' AND 
                      ctp_data_vencimento>='$data_inicial' AND 
                      ctp_data_vencimento<='$data_final' AND 
                      ctp_codigo_centro_custos='$codigo_cc'" . $wlocal);

            $num_rows_ctp = mysqli_num_rows($ctp);  

            if ($num_rows_ctp!=0) {
                while ($reg_conta_ctp = mysqli_fetch_object($ctp)) {
                    $parcela = $reg_conta_ctp->ctp_valor_parcela;
                    $juros = $reg_conta_ctp->ctp_valor_juros;
                    $desconto = $reg_conta_ctp->ctp_valor_desconto;
                    $outro = $reg_conta_ctp->ctp_outro_valor;
                    $valor_conta = $parcela - $desconto + $juros + $outro;
                    $total_conta+=intval($valor_conta);
                }
            }
        }
    }

    /*print_r($id_subconta_anterior .' '.
            $descricao_conta .' '.
            $total_conta .'</br>');*/

    if ($total_conta!=0) {
        $p = (object)[
            'descricao' => $descricao_conta,
            'valor' => intval($total_conta),
        ];
        $arr[] = $p;
    }

}

//var_dump($arr);

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
