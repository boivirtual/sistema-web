<?php
include "conecta_mysql.inc";

@ session_start(); 

$sql = "SELECT * FROM tbl_protocoloiatf WHERE tbl_protocoloiatf_lixeira = 0";
$rs = mysqli_query($conector, $sql);

if(mysqli_num_rows($rs) != 0){
    echo "<option value='000000000'>...</option>";
    while($protocolo = mysqli_fetch_object($rs)){
        $nome = $protocolo->tbl_protocoloiatf_descricao;
        $id = $protocolo->tbl_protocoloiatf_id;

        echo "<option value='{$id}'>{$nome}</option>";
    }
}else{
    echo "<option value='000000000'>...</option>";
}

mysqli_close($conector);

?>