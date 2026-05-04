<?php
include "conecta_mysql.inc";

$ano = $_POST['ano'];  
$local = $_POST['local']; 

for ($i = 0; $i <= 24; $i++) {
	$valor[$i]=0;
}

for ($m=1; $m <=12; $m++) { 
    $mes = ltrim($m, "0");
    $total_volume_mes[$mes]=0;
    $dias_chuva[$mes]=0;
}

$chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
    WHERE year(tbl_chuva_data) = '$ano' AND tbl_chuva_local='$local'");

$num_rows = mysqli_num_rows($chuva);  

if ($num_rows!=0) {
    while ($reg_chuva = mysqli_fetch_object($chuva)) {
        $data_chuva=new DateTime($reg_chuva->tbl_chuva_data);
        $mes_chuva=$data_chuva->format('m');
        $mes_chuva=ltrim($mes_chuva, "0");

        if ($reg_chuva->tbl_chuva_volume_chuva!=0 && 
            $reg_chuva->tbl_chuva_volume_chuva!='') {
            $total_volume_mes[$mes_chuva]+=$reg_chuva->tbl_chuva_volume_chuva;
            $dias_chuva[$mes_chuva]++;
        }
    }
}

$valor[0]=$total_volume_mes[1];
$valor[1]=$dias_chuva[1];
$valor[2]=$total_volume_mes[2];
$valor[3]=$dias_chuva[2];
$valor[4]=$total_volume_mes[3];
$valor[5]=$dias_chuva[3];
$valor[6]=$total_volume_mes[4];
$valor[7]=$dias_chuva[4];
$valor[8]=$total_volume_mes[5];
$valor[9]=$dias_chuva[5];
$valor[10]=$total_volume_mes[6];
$valor[11]=$dias_chuva[6];
$valor[12]=$total_volume_mes[7];
$valor[13]=$dias_chuva[7];
$valor[14]=$total_volume_mes[8];
$valor[15]=$dias_chuva[8];
$valor[16]=$total_volume_mes[9];
$valor[17]=$dias_chuva[9];
$valor[18]=$total_volume_mes[10];
$valor[19]=$dias_chuva[10];
$valor[20]=$total_volume_mes[11];
$valor[21]=$dias_chuva[11];
$valor[22]=$total_volume_mes[12];
$valor[23]=$dias_chuva[12];

$str=$valor[0] . '<|>';

for ($i=1; $i<=24; $i++){
    $str.=$valor[$i] . '<|>';
}
echo $str; 
mysqli_close($conector);

?>