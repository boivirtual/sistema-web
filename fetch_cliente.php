<?php
//fetch.php
    include "conecta_mysql.inc";

	$request = mysqli_real_escape_string($conector, $_POST["query"]);
	$query = "SELECT * FROM tbl_ajuda 
		INNER JOIN tbl_ajuda_url
		        ON tbl_id_url  = codigo_url_ajuda
		WHERE descricao_ajuda  LIKE '%".$request."%'";

	$result = mysqli_query($conector, $query);

	if(mysqli_num_rows($result) > 0) {
		echo '<ul>
		    <li>
		        <a href="#" class="pull-right" onclick="sair_ajuda()" onMouseOver="this.style.color=`#4169e1`" onMouseOut="this.style.color=`#5c5e61`" style="color: #5c5e61;margin-right: 5px;"><i class="icon_key_alt"></i> Fechar</a>
		    </li>
		</ul>';

		/*echo '<ul>
		    <li>
		    	<button class="btn btn-default pull-right" type="button" onclick="sair_ajuda()">Fechar</button>
		    </li>
		</ul>';*/

 		while($row = mysqli_fetch_assoc($result)) {
 			$url=$row["tbl_url_programa"];

			echo '<ul>
			    <li>
			        <a href="'.$url.'" onMouseOver="this.style.color=`#4169e1`" onMouseOut="this.style.color=`#898b8f`" style="color: #898b8f;"> '.$row["descricao_ajuda"].'</a>
			    </li>
			</ul>';

 		}
	}

	mysqli_close($conector);

?>
