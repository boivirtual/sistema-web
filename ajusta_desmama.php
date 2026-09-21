<?php
    include "conecta_mysql.inc";

    $sql = "SELECT * from tbl_pesagem 
        WHERE tbl_pesagem_lixeira=0 AND tbl_pesagem_controle='I' AND tbl_pesagem_codigo_epoca=2  
        ORDER BY tbl_pesagem_data ASC"; 

    $rs = mysqli_query($conector, $sql); 

    while ($reg_pesagem = mysqli_fetch_object($rs)){
        $codigo = $reg_pesagem->tbl_pesagem_id;
        $codigo_epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
        $data_pesagem = $reg_pesagem->tbl_pesagem_data;


        $itens = mysqli_query($conector, "SELECT * from tbl_item_pesagem 
            WHERE tbl_ite_pesagem_numero_id ='$codigo'"); 

        $qtd_animais = mysqli_num_rows($itens);

        echo $codigo . ' - ' . $codigo_epoca . ' - ' . $data_pesagem . ' Qtd Animais' .$qtd_animais. '</br>';

        while ($reg_item = mysqli_fetch_object($itens)){
            $codigo_id = $reg_item->tbl_ite_pesagem_codigo_id_animal;
            $codigo_animal = $reg_item->tbl_ite_pesagem_codigo_animal;
            $peso = $reg_item->tbl_ite_pesagem_peso;

            if ($peso!=0) {
                $sql = "UPDATE tbl_animais SET
                    tbl_animal_peso_desmama='$peso',
                    tbl_animal_data_desmama='$data_pesagem'
                WHERE tbl_animal_codigo_id='$codigo_id'";
                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    echo 'Erro ' . $erro_mysql . '</br>';
                }
                else {
                    echo 'Animal ' . $codigo_id . ' - ' . $codigo_animal . ' - ' . $peso . '</br>';
                }
            }
        }
    }
?>