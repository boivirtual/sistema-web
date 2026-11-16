<?php
include "conecta_mysql.inc";

$local = $_POST['local'];  

$query = mysqli_query($conector, "SELECT * FROM tbl_animais 
	WHERE tbl_animal_lixeira=0 AND 
	      tbl_animal_ativo='S' AND 
	      tbl_animal_sexo='F' AND
		  tbl_animal_codigo_fazenda = '$local'
	ORDER BY tbl_animal_codigo_numerico ASC"); 

$num_rows = mysqli_num_rows($query);

if($num_rows != 0){
	echo  '<option value="000000000">'.htmlentities('...').'</option>';

   	while($reg_animal = mysqli_fetch_assoc($query)){
 		$codigo_id = $reg_animal["tbl_animal_codigo_id"];
 		$codigo_alfa = $reg_animal["tbl_animal_codigo_alfa"];
 		$codigo_numerico = $reg_animal["tbl_animal_codigo_numerico"];
 		$codigo_numerico = ltrim($codigo_numerico, "0");

 		if ($codigo_alfa!='') {
	  		$codigo_edi = $codigo_alfa.'-'.$codigo_numerico;
 		}
 		else {
	  		$codigo_edi = $codigo_numerico;
 		}

        $data_nascimento =  $reg_animal["tbl_animal_data_nascimento"];
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); 
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+
                 $idade_acompanhamento_mostra_meses;

        if ($idade>7) {
			echo '<option value="'.$codigo_id.'">' .$codigo_edi. '</option>';
        }
   	}
}

mysqli_close($conector);

 
?>
