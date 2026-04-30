<?php
include "conecta_mysql.inc";

$codigo_id = $_POST['codigo_id'];  

$sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
    INNER JOIN tbl_cobertura
            ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
    INNER JOIN tbl_parametro_estacao_monta
            ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
    INNER JOIN tbl_pessoa
            ON tbl_pessoa_id = tbl_cobertura_codigo_local
    WHERE tbl_cobertura_lixeira=0 AND 
          tbl_ite_cobertura_codigo_id_animal='$codigo_id' AND 
          tbl_cobertura_controle = 'D'");

$num_rows = mysqli_num_rows($sql);

if ($num_rows!=0) {
    $reg_cobertura = mysqli_fetch_object($sql);
    $nome_local = $reg_cobertura->tbl_pessoa_nome;
    $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
    $descarte = ' Fazenda: ' . $nome_local . ' - Estação: ' . $estacao_monta;
}
else {
    $descarte = ' ';
}

echo $descarte; 
mysqli_close($conector);
exit;
?>