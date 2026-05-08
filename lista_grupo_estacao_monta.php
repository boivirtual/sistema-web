<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);
$estacao = ltrim($_POST['estacao']);

echo  '<option value="000">'.htmlentities('Incluir em um grupo já existente').'</option>';

$tbl_grupo =  mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
    WHERE tbl_grupo_codigo_estacao_monta = '$estacao' AND 
          tbl_grupo_codigo_local = '$local' AND 
          tbl_grupo_id!=999");  

$num_rows_grupo = mysqli_num_rows($tbl_grupo);

if($num_rows_grupo != 0){
    while ($reg_grupo = mysqli_fetch_object($tbl_grupo)) {
        $id_grupo = $reg_grupo->tbl_grupo_id;
        $descricao = $reg_grupo->tbl_grupo_descricao;

        $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura 
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_codigo_local='$local' and 
                  tbl_cobertura_controle='C' and 
                  tbl_cobertura_codigo_estacao_monta='$estacao' and 
                  tbl_cobertura_codigo_grupo='$id_grupo'
                  ORDER BY tbl_cobertura_codigo_grupo ASC"); 

        $num_rows = mysqli_num_rows($tbl_cobertura);

        if ($num_rows!=0) {
            while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)) {
                $id_cobertura = $reg_cobertura->tbl_cobertura_id;
                $codigo_grupo = $reg_cobertura->tbl_cobertura_codigo_grupo;

                $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                    WHERE  tbl_ite_cobertura_numero_id='$id_cobertura' AND 
                          (tbl_ite_cobertura_dia_1='' or tbl_ite_cobertura_dia_1 is null)"); 
                $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

                if ($num_rows_item!=0) {
                    echo '<option value="'.$id_grupo.'">' .$id_grupo.'-'.$descricao. '</option>';

                    /*$sql = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
                            WHERE tbl_grupo_id = '$codigo_grupo' AND 
                                  tbl_grupo_codigo_estacao_monta = '$estacao' AND 
                                  tbl_grupo_codigo_local = '$local'");  
                    $num_rows_grupo_item = mysqli_num_rows($sql);

                    if($num_rows_grupo_item != 0){
                        $reg_grupo_item = mysqli_fetch_object($sql);
                        $id = $reg_grupo_item->tbl_grupo_id;
                        $descricao = $reg_grupo_item->tbl_grupo_descricao;

                        echo '<option value="'.$id.'">' .$id.'-'.$descricao. '</option>';
                    }*/
                }
            }    
        }
        else {
            echo '<option value="'.$id_grupo.'">' .$id_grupo.'-'.$descricao. '</option>';
        }
    }
}


/*
$sql = mysqli_query($conector, "SELECT * FROM tbl_grupo_estacao_monta 
    WHERE tbl_grupo_codigo_estacao_monta = '$estacao' AND 
          tbl_grupo_codigo_local = '$local'
    ORDER BY tbl_grupo_id ASC"); 

$num_rows_grupo = mysqli_num_rows($sql);

if($num_rows_grupo != 0){
    while ($reg_grupo = mysqli_fetch_object($sql)) {
        $id_grupo = $reg_grupo->tbl_grupo_id;
        $descricao = $reg_grupo->tbl_grupo_descricao;

        $tbl_cobertura = mysqli_query($conector, "SELECT * FROM tbl_cobertura 
            WHERE tbl_cobertura_codigo_grupo='$id_grupo' AND 
                  tbl_cobertura_codigo_local='$local' AND 
                  tbl_cobertura_codigo_estacao_monta='$estacao'"); 

        $num_rows = mysqli_num_rows($tbl_cobertura);

        if ($num_rows==0) {
            echo '<option value="'.$id_grupo.'">' .$id_grupo.'-'.$descricao. '</option>';
        }
    }
}*/

mysqli_close($conector);

?>
