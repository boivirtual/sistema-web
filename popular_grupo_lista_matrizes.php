<?php
include "conecta_mysql.inc";

$local = $_POST['local'];
$id_parametro_estacao = $_POST['estacao_monta'];

echo  '<option value="000">'.htmlentities('...').'</option>';

$grupo_estacao = mysqli_query($conector, "select * from tbl_grupo_estacao_monta 
    where tbl_grupo_codigo_estacao_monta ='$id_parametro_estacao' and 
          tbl_grupo_codigo_local='$local'
    order by tbl_grupo_id  ASC"); 
    
$num_grupo = mysqli_num_rows($grupo_estacao);

if ($num_grupo!=0) {
    while ($reg_grupo = mysqli_fetch_object($grupo_estacao)){
        $codigo_grupo = $reg_grupo->tbl_grupo_id;
        $desc_grupo = $reg_grupo->tbl_grupo_descricao;

	    $tbl_cobertura = mysqli_query($conector,"select * from tbl_cobertura 
	        where tbl_cobertura_lixeira=0 and
	              tbl_cobertura_codigo_grupo='$codigo_grupo' and 
	              tbl_cobertura_codigo_estacao_monta='$id_parametro_estacao' and 
	              tbl_cobertura_codigo_local='$local'"); 

	    $num_rows_cobertura = mysqli_num_rows($tbl_cobertura);

	    if ($num_rows_cobertura==0 || $codigo_grupo==999) {
	        echo '<option value="'.$codigo_grupo.'">' .$desc_grupo. '</option>';
	    }
	    else if ($num_rows_cobertura!=0) {
	        $reg_cobertura = mysqli_fetch_object($tbl_cobertura);
	        $qtd_animais = trim($reg_cobertura->tbl_cobertura_qtd_animais, "0");
	        $protocolo_iatf = $reg_cobertura->tbl_cobertura_protocoloiatf;

	        if ($protocolo_iatf==0) {
	            echo '<option value="'.$codigo_grupo.'">' .$desc_grupo. ' - ' . $qtd_animais .' Fêmea(s)</option>';
	        }
	    }
    }
}

mysqli_close($conector);

?>
