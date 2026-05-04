<?php 
    include "conecta_mysql.inc";

    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura");

    while($reg_itensCobertura = mysqli_fetch_object($tbl_item_cobertura)){
        $codigo = $reg_itensCobertura->tbl_ite_cobertura_codigo_animal;
        $id = $reg_itensCobertura->tbl_ite_cobertura_numero_id; 
        $item = $reg_itensCobertura->tbl_ite_cobertura_numero_item;

        //echo $codigo . '</br>';
        $codigo = explode("-",$codigo);

        if (isset($codigo[1])) {
            echo $codigo[0] . ' ' . $codigo[1] . '</br>';
            $codigo_alfa = $codigo[0];
            $codigo_numerico = $codigo[1];
        }
        else {
            echo $codigo[0]. '</br>';
            $codigo_alfa = '';
            $codigo_numerico = $codigo[0];
        }

        $sql = "UPDATE tbl_item_cobertura SET
            tbl_ite_cobertura_codigo_alfa = '$codigo_alfa',
            tbl_ite_cobertura_codigo_numerico = '$codigo_numerico'
            WHERE tbl_ite_cobertura_numero_id='$id' AND 
                  tbl_ite_cobertura_numero_item='$item'"; 

        $resultado = mysqli_query($conector,$sql);                           
        $erro_mysql = mysqli_error($conector);  

        if (!$resultado) {
            echo $erro_mysql . '</br>';
        }
    }

    echo 'fim';
?>