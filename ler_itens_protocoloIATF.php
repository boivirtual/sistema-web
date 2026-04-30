<?php
    include "conecta_mysql.inc";
?>

<?php
$protocolo_id = $_POST['protocolo_id'];

$sql = "SELECT * FROM tbl_item_protocoloiatf WHERE tbl_ite_protocoloiatf_lixeira = 0 AND tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' ORDER BY tbl_ite_protocoloiatf_id ASC";
$rs = mysqli_query($conector, $sql);
$array_contas = [mysqli_num_rows($rs)];

while($reg_protocolo = mysqli_fetch_object($rs)){
    $id_item = $reg_protocolo->tbl_ite_protocoloiatf_id;
    $descricao = $reg_protocolo->tbl_ite_protocoloiatf_descricao;
    $med_1 = $reg_protocolo->tbl_ite_protocoloiatf_medicamento_1;
    $qtd_1 = $reg_protocolo->tbl_ite_protocoloiatf_qtde_1;
    $und_1 = $reg_protocolo->tbl_ite_protocoloiatf_unidade_1;
    $med_2 = $reg_protocolo->tbl_ite_protocoloiatf_medicamento_2;
    $qtd_2 = $reg_protocolo->tbl_ite_protocoloiatf_qtde_2;
    $und_2 = $reg_protocolo->tbl_ite_protocoloiatf_unidade_2;
    $med_3 = $reg_protocolo->tbl_ite_protocoloiatf_medicamento_3;
    $qtd_3 = $reg_protocolo->tbl_ite_protocoloiatf_qtde_3;
    $und_3 = $reg_protocolo->tbl_ite_protocoloiatf_unidade_3;
    $med_4 = $reg_protocolo->tbl_ite_protocoloiatf_medicamento_4;
    $qtd_4 = $reg_protocolo->tbl_ite_protocoloiatf_qtde_4;
    $und_4 = $reg_protocolo->tbl_ite_protocoloiatf_unidade_4;

    array_push($array_contas, 
                $id_item,
                $descricao,
                $med_1,
                $qtd_1,
                $und_1,
                $med_2,
                $qtd_2,
                $und_2,
                $med_3,
                $qtd_3,
                $und_3,
                $med_4,
                $qtd_4,
                $und_4
            );
}
$array_retorno = implode("|", $array_contas);
echo $array_retorno;

?>