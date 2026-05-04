<?php 
    include "conecta_mysql.inc";
	$cobertura = 267;
    $vet = array();

    $vet[]=001;
    $vet[]=002;

    var_dump($vet);

    for($i = 0; $i < count($vet); $i++){
        $ordem = $vet[$i];

        do {
            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            WHERE tbl_ite_cobertura_numero_id='$cobertura' AND 
                  tbl_ite_cobertura_numero_item = '$ordem'");

            $num_rows = mysqli_num_rows($tbl_item_cobertura);   

            if ($num_rows==0) {
                print_r ('Não Achei o Registro ' . $ordem . '</br>');
                $ordem++;
            }

        } while ($num_rows==0);

        if ($num_rows==1) {
            print_r ('Achei o Registro ' . $ordem . '</br>');
        }
    }

    print_r('Fim');
?>