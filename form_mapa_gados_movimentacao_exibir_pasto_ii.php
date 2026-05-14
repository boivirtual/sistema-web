<?php
include "valida_sessao.inc";
include "conecta_mysql.inc";

@ session_start();
$controle_estoque = $_SESSION['controle_estoque'];

//pegando informações do pasto do banco
$pasto_id = $_POST["pasto_id"];

$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
    INNER JOIN tbl_pessoa
            ON tbl_pessoa_id = tbl_pasto_codigo_local
    WHERE tbl_pasto_id = $pasto_id AND 
          tbl_pasto_lixeira = 0");

$reg_pasto = mysqli_fetch_object($tbl_pasto);

if ($reg_pasto->tbl_pasto_id_lote!=0) {
    $id_lote = $reg_pasto->tbl_pasto_id_lote;
    $ano_lote = $reg_pasto->tbl_pasto_ano_lote;
    $desc_id_lote = 'L-'.$id_lote.'/'.substr($ano_lote, 2, 2);
}
else {
    $desc_id_lote = '';
}

$_SESSION["pasto_id"] = $pasto_id;


?>