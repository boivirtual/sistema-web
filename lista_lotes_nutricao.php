<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);
$data_inicial = ltrim($_POST['data_inicial']);
$data_final = ltrim($_POST['data_final']);
$tipo_rel = ltrim($_POST['tipo_rel']);

if ($data_inicial=='' || $data_final==''){
    $wperiodo = '';
}
else {
    $wperiodo = " AND tbl_nutricao_data >= '$data_inicial' AND tbl_nutricao_data <= '$data_final'";
}

if ($tipo_rel=='L') {
    echo  '<option value="00000000">'.htmlentities('...').'</option>';
}

$sql = "SELECT * FROM tbl_nutricao
    WHERE tbl_nutricao_codigo_local = '$local' AND 
          tbl_nutricao_lixeira=0 " . $wperiodo .
    " ORDER BY tbl_nutricao_id_lote ASC";        

    // ORDER BY tbl_nutricao_lote_pasto DESC, tbl_nutricao_id DESC";        
print_r($sql);

$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if($num_rows != 0){
    $id_lote_anterior = '';

    while($ln = mysqli_fetch_assoc($qr)){
        $id_lote = $ln['tbl_nutricao_id_lote'];
        //$ano = $ln['tbl_nutricao_ano_lote'];
        //$descricao = $ln['tbl_nutricao_lote_pasto'];

        //$id_ano = $id.$ano;

        if ($id_lote!=$id_lote_anterior) {
            echo '<option value="'.$id_lote.'">' .substr($id_lote, 0, 4).'/'.substr($id_lote, 4, 4). '</option>';
            $id_lote_anterior=$id_lote;
        }
    }
}

mysqli_close($conector);

 
?>
