<?php
include "conecta_mysql.inc";

$tbl_itens_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
    ");

$num_rows = mysqli_num_rows($tbl_itens_cobertura);  
        
if ($num_rows!=0) {
    while ($reg_item = mysqli_fetch_object($tbl_itens_cobertura)) {
        $diagnostico= $reg_item->tbl_ite_cobertura_resultado_diagnostico;
        $nascido= $reg_item->tbl_ite_cobertura_nascido;
        $id_animal = $reg_item->tbl_ite_cobertura_codigo_id_animal;
        $codigo_animal = $reg_item->tbl_ite_cobertura_codigo_animal;

        if ($nascido=='N' || $nascido=='A' || $nascido=='M' || $nascido=='O') {

            $sql = "UPDATE tbl_animais SET 
                           tbl_animal_em_estacao_monta='',
                           tbl_animal_selecioanada_reproducao=''

                   WHERE tbl_animal_codigo_id='$id_animal'";
            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
              echo 'Erro ao gravar ' . $erro_mysql . '</br>';
            } 

            echo $codigo_animal . '</br>';
        }
    }
}


echo 'Fim do processamento'; 

?>