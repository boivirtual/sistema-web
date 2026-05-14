<?php
include "conecta_mysql.inc";

$tipo_movimentacao = ltrim($_POST['tipo_movimentacao']);
$local= ltrim($_POST['local']);

switch ($tipo_movimentacao) {
    case 'T':
       	$epoca_pesagem = 005;
        break;
    case 'C':
       	$epoca_pesagem = 004;
        break;
    case 'V':
      	$epoca_pesagem = 003;
        break;
    case 'O':
      	$epoca_pesagem = 0;
        break;
    case 'M':
        $epoca_pesagem = 0;
        break;
    }

if ($epoca_pesagem==0) {
    $sql = "SELECT * FROM tbl_pesagem WHERE 
        tbl_pesagem_codigo_local = '$local' AND 
        tbl_pesagem_codigo_epoca!=003 AND 
        tbl_pesagem_codigo_epoca!=004 AND 
        tbl_pesagem_codigo_epoca!=005 AND 
        (tbl_pesagem_codigo_movimentacao=0 OR tbl_pesagem_codigo_movimentacao IS NULL) AND 
        tbl_pesagem_lixeira=0 AND 
        tbl_pesagem_finalizada='S'";  
}
else {
    $sql = "SELECT * FROM tbl_pesagem WHERE 
        tbl_pesagem_codigo_local = '$local' AND 
        tbl_pesagem_codigo_epoca='$epoca_pesagem' AND 
        (tbl_pesagem_codigo_movimentacao=0 OR tbl_pesagem_codigo_movimentacao IS NULL)  AND 
        tbl_pesagem_lixeira=0 AND 
        Tbl_pesagem_finalizada='S'";  
}

$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['tbl_pesagem_id'];
        $lote = $ln['tbl_pesagem_lote'];
        $data_pesagem = new DateTime($ln['tbl_pesagem_data']);
        $data_pesagem_edi = $data_pesagem->format('d/m/Y');
	    echo '<option value="'.$id.'">' .$lote. ' - ' .$data_pesagem_edi.'</option>';
   	}
}

mysqli_close($conector);

 
?>
