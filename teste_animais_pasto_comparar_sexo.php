<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $total_macho_pasto = 0;
    $total_macho_cadastro = 0;

    $total_femea_pasto = 0;
    $total_femea_cadastro = 0;

    $local = 77;

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_local = '$local' AND 
              tbl_animal_pasto_situacao = 'A'");

    $num_rows = mysqli_num_rows($tbl_animais);


    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $sexo = $reg_animal->tbl_animal_pasto_sexo;  

            if ($sexo=='M') {
                $total_macho_pasto++;
            }
            else {
                $total_femea_pasto++;
            }
    	}

	}

    echo 'Local: ' .$local. ' Total animais pasto: ' . $num_rows . '</br>';
    echo 'Machos: ' . $total_macho_pasto . '</br>';
    echo 'Fêmeas: ' . $total_femea_pasto . '</br>';
    echo 'Fim do processamento';

?>
