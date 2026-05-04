<?php
include "conecta_mysql.inc";

@ session_start(); 

$tipo = $_POST['tipo'];  
$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario 
	WHERE id_usuario = '$codigo_usuario' AND 
          lixeira_usuario=0 ";  
$query = mysqli_query($conector_acesso, $tbl_usuario);

$num_rows_usuario = mysqli_num_rows($query);

if ($num_rows_usuario!=0){
	$reg_usuario = mysqli_fetch_assoc($query);

	$array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
	$qtd_locais_usuario = count($array_locais_usuario);

	if ($qtd_locais_usuario==0) {
		$array_locais_usuario='';
	}
}
else {
	$array_locais_usuario='';
}

$sql = "SELECT * FROM tbl_pessoa WHERE tbl_pessoa_classe = 4 AND 
                                       tbl_pessoa_lixeira=0 ";  
$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);

if ($tipo==0) {
	if ($qtd_locais_usuario>1) {
		echo  '<option value="000000000">'.htmlentities('...').'</option>';
	}
}
else {
	if ($qtd_locais_usuario>1) {
		echo  '<option value="000000000">'.htmlentities('Todas').'</option>';
	}
}

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['tbl_pessoa_id'];
        $nome = $ln['tbl_pessoa_nome'];

		foreach ($array_locais_usuario as $value) {
			$value = ltrim($value);
			$value = rtrim($value);

			if ($value==$id) {
			    echo '<option value="'.$id.'">' .$nome. '</option>';
			}
		}                    	
   	}
}

mysqli_close($conector);

 
?>
