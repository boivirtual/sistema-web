<?php
    include "conecta_mysql.inc";

    $sql = "SELECT * from tbl_animais"; 

    $rs = mysqli_query($conector, $sql); 

    while ($reg_animais = mysqli_fetch_object($rs)){
        $codigo_id = $reg_animais->tbl_animal_codigo_id;
        $codigo_fazenda_anterior = $reg_animais->tbl_animal_codigo_fazenda_anterior;
        $codigo_fazenda = $reg_animais->tbl_animal_codigo_fazenda;

        if ($codigo_fazenda_anterior!=0) {
            $codigo_fazenda_inicial = $codigo_fazenda_anterior;
        }
        else {
             $codigo_fazenda_inicial = $codigo_fazenda;
        }    

        $sql = "UPDATE tbl_animais SET tbl_animal_codigo_fazenda_inicial='$codigo_fazenda_inicial'
            WHERE tbl_animal_codigo_id='$codigo_id'";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            echo 'Erro ' . $erro_mysql . '</br>';
        }
        else {
            echo 'Animal ' . $codigo_id . ' - ' . $codigo_fazenda_inicial . '</br>';
        }
    }
?>