<?php
include "conecta_mysql.inc";

@ session_start(); 

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

$tipo_movimentacao = ltrim($_POST['tipo_movimentacao']);
$local_origem = ltrim($_POST['local_origem']);

switch ($tipo_movimentacao) {
    case 'T':
       	$tipo_pessoa = [4];
        break;
    case 'C':
        if ($local_origem==1){
        	$tipo_pessoa = [2];
        }
        else {
        	$tipo_pessoa = [4];
        }
        break;
    case 'V':
        if ($local_origem==1){
        	$tipo_pessoa = [4];
        }
        else {
        	$tipo_pessoa = [1,2];
        }
        break;
    case 'O':
        if ($local_origem==1){
        	$tipo_pessoa = [4];
        }
        else {
        	$tipo_pessoa = [0];
        }
        break;
    case 'M':
        if ($local_origem==1){
            $tipo_pessoa = [4];
        }
        else {
            $tipo_pessoa = [0];
        }
        break;
}

$wlocal = " AND tbl_pessoa_classe IN(";
$wlocal.= implode(',', $tipo_pessoa);
$wlocal.= ")";

if ($tipo_pessoa==[4]) {
    $sql = "SELECT * FROM tbl_pessoa 
    WHERE tbl_pessoa_lixeira=0 " . $wlocal;  
}
else {
    $sql = "SELECT * FROM tbl_pessoa 
    WHERE tbl_pessoa_lixeira=0 " . $wlocal . 
    " ORDER BY tbl_pessoa_nome ASC" ;  
}

$qr = mysqli_query($conector, $sql);

$num_rows = mysqli_num_rows($qr);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($qr)){
   		$id = $ln['tbl_pessoa_id'];
        $nome = $ln['tbl_pessoa_nome'];

        if ($tipo_pessoa==[4] && $local_origem==1) {
			foreach ($array_locais_usuario as $value) {
				$value = ltrim($value);
				$value = rtrim($value);

				if ($value==$id) {
				    echo '<option value="'.$id.'">' .$nome. '</option>';
				}
			}                    	
        }
        else {
	    	echo '<option value="'.$id.'">' .$nome. '</option>';
        }
   	}

}

if ($tipo_movimentacao=='C' && $local_origem==1) {
    echo '<option value="999999999">ACERTO INICIAL DO ESTOQUE</option>';
}

mysqli_close($conector);

 
?>
