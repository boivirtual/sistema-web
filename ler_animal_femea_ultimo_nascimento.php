<?php
include "conecta_mysql.inc";

for ($i=1; $i<=20; $i++){
    $valor[$i]=0;
}

$meses_calculados = 999;

$id_animal = $_POST['id_animal'];  

if (isset($_POST['data_previsao'])) {
	$data_previsao = $_POST['data_previsao'];
}
else {
	$valor[0] = $meses_calculados;
	$str=$valor[0] . '<|>';

	for ($i=1; $i<=20; $i++){
    	$str.=$valor[$i] . '<|>';
	}
	echo $str; 
	mysqli_close($conector);
	exit;
}
 
$sql = mysqli_query($conector, "SELECT * FROM tbl_animais 
    WHERE tbl_animal_codigo_mae = '$id_animal'
	ORDER BY tbl_animal_codigo_id DESC LIMIT 1");

$num_rows = mysqli_num_rows($sql);

if ($num_rows!=0) {
	$reg_nacimento = mysqli_fetch_object($sql);
	$data_nascimento_ultimo = $reg_nacimento->tbl_animal_data_nascimento;

	$firstDate  = new DateTime($data_nascimento_ultimo);
	$secondDate = new DateTime($data_previsao);
	$diff = $firstDate->diff($secondDate);
	$meses_calculados=$diff->m + ($diff->y * 12);

	if ($firstDate > $secondDate) {
    	$meses_calculados =  "-" . $meses_calculados;
	}
}

$valor[0] = $meses_calculados;
$str=$valor[0] . '<|>';

for ($i=1; $i<=20; $i++){
    $str.=$valor[$i] . '<|>';
}
echo $str; 
mysqli_close($conector);
exit;

?>