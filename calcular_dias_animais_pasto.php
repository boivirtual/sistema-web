<?php
include "conecta_mysql.inc";

$pasto = 7; // pegar no post
$local = 56; // pegar no post
$dias_pasto = 0;

$tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
                            WHERE tbl_animal_pasto_local = '$local' AND 
                                  tbl_animal_pasto_id='$pasto' AND 
                                  tbl_animal_pasto_situacao = 'A'");  

$num_rows = mysqli_num_rows($tbl_animal_pasto);

if ($num_rows!=0) {
    while ($reg = mysqli_fetch_object($tbl_animal_pasto)) {
        $data_inclusao = $reg->tbl_animal_pasto_incluido_em;
        $data_alteracao = $reg->tbl_animal_pasto_alterado_em;

        if ($data_alteracao!='') {
            $firstDate  = new DateTime($data_alteracao);
            $secondDate = new DateTime();
            $intvl = $firstDate->diff($secondDate);
            $dias_calculados = $intvl->days;
        }
        else {
            $firstDate  = new DateTime($data_inclusao);
            $secondDate = new DateTime();
            $intvl = $firstDate->diff($secondDate);
            $dias_calculados = $intvl->days;
        }

        if ($dias_calculados>$dias_pasto) {
            $dias_pasto=$dias_calculados;
        }
    }
}


echo 'dias calculados: ' . $dias_pasto;
mysqli_close($conector);


?> 