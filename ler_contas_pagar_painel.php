<?php

include "conecta_mysql.inc";

$local = $_GET["local"];

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

$data_inicial = $data_inicial . '-01';
$data_final = $data_final . '-31';
$arr = [];

$contas = mysqli_query($conector, "SELECT * FROM tbl_plano_contas 
    WHERE tbl_plano_contas_debito_credito='D' AND 
          tbl_plano_contas_ana_sin='A' AND 
          tbl_plano_contas_lixeira=0
          ORDER BY tbl_plano_contas_descricao ASC");

$num_rows = mysqli_num_rows($contas);  

if ($num_rows!=0) {
    while ($reg_conta = mysqli_fetch_object($contas)) {
        $id = $reg_conta->tbl_plano_contas_codigo_id;
        $descricao_conta=$reg_conta->tbl_plano_contas_descricao;

        $total_conta = 0;

        $ctp = mysqli_query($conector, "SELECT * FROM contas_pagar 
            WHERE ctp_codigo_conta='$id' AND 
                  ctp_data_vencimento>='$data_inicial' AND 
                  ctp_data_vencimento<='$data_final'" . $wlocal);

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

        if ($total_conta!=0) {
            $p = (object)[
                'descricao' => $descricao_conta,
                'valor' => intval($total_conta),
            ];
            $arr[] = $p;
        }
    }
}

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
