<?php
include "conecta_mysql.inc";

//$id_url = $_POST['id_url'];

$query = mysqli_query($conector, "SELECT * FROM tbl_ajuda_url 
	ORDER BY tbl_nome_programa_url ASC"); 

$num_rows = mysqli_num_rows($query);

if($num_rows != 0){
	//echo  '<option value="000000000">'.htmlentities('...').'</option>';

   	while($reg_ajuda = mysqli_fetch_assoc($query)){
 		$codigo_id = $reg_ajuda["tbl_id_url"];
 		$descricao = $reg_ajuda["tbl_nome_programa_url"];

 		//if ($codigo_id==$id_url) {
			//echo '<option value="'.$codigo_id.'" selected>' .$descricao. '</option>';
 		//}
 		//else {
			echo '<option value="'.$codigo_id.'">' .$descricao. '</option>';
		//}
   	}
}

mysqli_close($conector);

 
?>
