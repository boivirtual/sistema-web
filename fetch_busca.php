<?php
function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
}

//fetch.php
    include "conecta_mysql.inc";

	$request = mysqli_real_escape_string($conector, $_POST["query"]);
	//$request = str_replace(' ', '%', $request);
	$request = tirarAcentos($request);

	$query = "SELECT * FROM tbl_ajuda 
		INNER JOIN tbl_ajuda_url
		        ON tbl_id_url  = codigo_url_ajuda
		WHERE MATCH (palavra_chave_ajuda) AGAINST ('$request')
		ORDER BY tbl_nome_programa_url ASC";

		//WHERE palavra_chave_ajuda  LIKE '%".$request."%'";

	$result = mysqli_query($conector, $query);
	$url_anterior = '';
	$programa_anterior = '';

	if(mysqli_num_rows($result) > 0) {
		echo '<ul style="padding: 0 0 0 0;">
		    <li>
		        <a href="#" class="pull-right" onclick="sair_busca()" onMouseOver="this.style.color=`#4169e1`" onMouseOut="this.style.color=`#5c5e61`" style="color: #5c5e61;margin-right: 5px;"><i class="icon_key_alt"></i> Fechar</a>
		    </li>

		    <li>&nbsp;</li>';

 		while($row = mysqli_fetch_assoc($result)) {
 			$url=$row["tbl_url_programa"];
 			$programa=$row["tbl_nome_programa_url"];

 			if ($programa_anterior!=$programa) {
 				$programa_anterior=$programa;

				echo '
				    <li>
				        <a href="'.$url.'" onMouseOver="this.style.color=`#4169e1`" onMouseOut="this.style.color=`#898b8f`" style="color: #898b8f;"> '.$row["tbl_nome_programa_url"].'</a>
				    </li>';
 			}
 		}

 		echo '</ul>';
	}

	mysqli_close($conector);

?>
