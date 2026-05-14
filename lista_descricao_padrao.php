<?php
include "conecta_mysql.inc";

$modalidade = $_POST['modalidade'];  

$sql = "SELECT * FROM tabela_produto_generico WHERE pro_codigo_modalidade = '$modalidade' AND 
                                                    pro_generico_registro_lixeira=0";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if ($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['pro_generico_codigo'];
        $nome = $ln['pro_generico_descricao'];
	    echo '<option value="'.$id.'">' .$nome. '</option>';
   	}
}
else {
	echo '<option value="000">'.htmlentities('...').'</option>';

}

mysqli_close($conector);

 
?>
