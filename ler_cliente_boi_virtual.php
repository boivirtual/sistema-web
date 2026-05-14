<?php
    include "conecta_mysql.inc";

    @ session_start(); 
    $_SESSION['validar_cliente']='S';

    $cliente_boi = mysqli_query($conector, "SELECT * FROM tbl_cliente_boi_virtual 
        WHERE tbl_cliente_ativo = 'N' AND 
              tbl_cliente_lixeira = 0
        ORDER BY tbl_cliente_id DESC");

    $num_rows = mysqli_num_rows($cliente_boi);
    $str = '';

    if ($num_rows!=0) {
        while ($reg = mysqli_fetch_object($cliente_boi)) {
            $nome = $reg->tbl_cliente_nome_empresa;
            $data_inclusao = new Datetime($reg->tbl_cliente_incluido_em);
            $str.='Cliente: '.$nome.' - Solicitação: '.$data_inclusao->format('d/m/Y') . '</br>';
        }
        echo json_encode(array('success' => true, 'message' => $str));
    }

?>