<?php
include "conecta_mysql.inc";

$tipo = $_POST['tipo'];  

$sql = "SELECT * FROM tbl_movimentacao 
        WHERE tbl_movimentacao_tipo = '$tipo' AND tbl_movimentacao_situacao='N' AND 
              tbl_movimentacao_lixeira=0 AND tbl_movimentacao_codigo_local_origem!=999999999";  

$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['tbl_movimentacao_id'];
        $data_mov = new DateTime($ln['tbl_movimentacao_data']);
        $data_mov_edi = $data_mov->format('d/m/Y');
        $local_origem = $ln['tbl_movimentacao_codigo_local_origem'];
        $local_destino = $ln['tbl_movimentacao_codigo_local_destino'];

        $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
                                               WHERE tbl_pessoa_id ='$local_origem'");
        $num_rows = mysqli_num_rows($rs);
        if ($num_rows!=0) {
            $reg_origem = mysqli_fetch_object($rs);
            $desc_origem = $reg_origem->tbl_pessoa_nome;
        }
        else {
            $desc_origem ='';
        }

        $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
                                               WHERE tbl_pessoa_id='$local_destino'");
        $num_rows = mysqli_num_rows($rs);
        if ($num_rows!=0) {
            $reg_destino = mysqli_fetch_object($rs);
            $desc_destino = $reg_destino->tbl_pessoa_nome;
        }
        else {
            $desc_destino ='';
        }

	    echo '<option value="'.$id.'">' .$data_mov_edi. ' - ' .$desc_origem. '/'.$desc_destino.'</option>';
   	}
}

mysqli_close($conector);

 
?>
