<?php

$data_nascimento = '2022-04-08';

$partes = explode("-", $data_nascimento);
$ano_final = $partes[0];
$mes_final = $partes[1];
$dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);
$estoque_final = 378.52;

$data_processamento = $ano_final.'-'.$mes_final.'-'.$dia_final;
$array_processamento = array(
    $data_processamento,
    $estoque_final
);   
                                    
$string_array[] = implode('|', $array_processamento);

$data_nascimento = '2022-03-16';

$partes = explode("-", $data_nascimento);
$ano_final = $partes[0];
$mes_final = $partes[1];
$dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);
$estoque_final = 758.32;

$data_processamento = $ano_final.'-'.$mes_final.'-'.$dia_final;
$array_processamento = array(
    $data_processamento,
    $estoque_final
);   
                                    
$string_array[] = implode('|', $array_processamento);

var_dump($string_array);


/*$date = new DateTime($data_nascimento);
$date->modify('last day of this month');
$date = $date->format('Y-m-d');
echo $date->format('d'); // somente o dia

//echo 'Data Nascimento: ' . $data_nascimento . '</br>';
//echo 'Último dia do Mes: ' . $date . '</br>';

//echo 'DATA ANTERIOR' . '</br>';

/*$data_hoje = date("Y-m-d");
$partes_hoje = explode("-", $data_hoje);
$anomes_inicial = $partes_hoje[0].$partes_hoje[1];

$partes_nascimento = explode("-", $data_nascimento);
$anomes_final = $partes_nascimento[0].$partes_nascimento[1];

//echo 'Incial: ' . $anomes_inicial . ' Final: ' . $anomes_final . '</br>';
$diferenca = $anomes_inicial - $anomes_final;
//echo 'Diferença: ' . $diferenca . '</br>';
 
if ($diferenca!=0) {
	echo 'O Nascimento ' . $data_nascimento . ' Pertence ao mes anterior: '  . $date . ' Diferença: '.$diferenca.'</br>';
}
else {
	echo 'O Nascimento ' . $data_nascimento . ' Pertence a este mes: '  . $date . '</br>';
}
  
/*
echo $date->format('d'); // somente o dia
echo $date->format('d/m'); //dia e mês
echo $date->format('d/m/Y'); //dia mês e ano
*/
?>