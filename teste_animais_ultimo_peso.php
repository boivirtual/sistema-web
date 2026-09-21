<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $local = 57;
    $total_mes = 0;
    $total_fazenda = 0;
    $total_macho = 0;
    $total_femea = 0;
    $animal_anterior = 0;

        $sql = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
            INNER JOIN tbl_animais
                    ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
            INNER JOIN tbl_pesagem
                    ON tbl_pesagem_id = tbl_ite_pesagem_numero_id
                 WHERE 
                       tbl_pesagem_data >= '2023-04-01' AND 
                       tbl_pesagem_data <= '2023-04-30' AND
                       tbl_pesagem_codigo_local = '$local' AND 
                       tbl_ite_pesagem_peso!=0 and 
                       tbl_pesagem_codigo_epoca=5
                ORDER BY tbl_pesagem_data ASC, tbl_ite_pesagem_codigo_id_animal ASC
              ");

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows!=0) {

            while ($reg_item = mysqli_fetch_object($sql)) {
                $peso = $reg_item->tbl_ite_pesagem_peso;
                $codigo_animal = $reg_item->tbl_ite_pesagem_codigo_animal;
                $sexo = $reg_item->tbl_ite_pesagem_sexo;
                $total_mes+=$peso;

                if ($sexo == 'Macho') {
                    $total_macho+=$peso;
                }
                else {
                    $total_femea+=$peso;
                }

                echo 'Código: ' . $codigo_animal . 
                     ' Peso: ' . $peso . '</br>';
            }

        }

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
    	where tbl_animal_codigo_fazenda='$local' AND 
              tbl_animal_lixeira=0 AND 
              tbl_animal_ativo='S'");

    while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
		$codigo = $reg_animal->tbl_animal_codigo_id;
		$codigo_numerico = $reg_animal->tbl_animal_codigo_numerico;
        $codigo_alfa = $reg_animal->tbl_animal_codigo_alfa;
        $peso = $reg_animal->tbl_animal_ultimo_peso;

        $total_fazenda+=$peso;
    }

    echo 'Total Fazenda: ' . $total_fazenda . ' Total MES: ' . $total_mes . '</br>';

    echo 'Machos: ' . $total_macho . ' Femeas: ' . $total_femea . '</br>';
    echo 'Fim do processamento';

?>
