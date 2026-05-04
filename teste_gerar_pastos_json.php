<?php

$local = '000000191';
$arquivo = 'mapa/97174041604/'.$local.'.json';
$tem_entrada_saida = 'N';
$total_ha = 0;

$json_data = json_decode(file_get_contents($arquivo));

foreach ($json_data->features as $data) {
    $array_coordenadas = $data->geometry->coordinates;

    $str = mb_strtoupper($data->properties->name, 'UTF-8');
    $type = $data->geometry->type;

    if ($type=='Polygon') {
        $area = calcular_area($array_coordenadas);

        $area = floatval(substr($area, 0, 7));
        $total_ha+=$area;

        echo 'Pasto: ' . $str . ' ' . $type . ' Ha: ' . $area .'<br>';
    }
}

echo 'Total HA da Fazenda: ' . $total_ha .'<br>';

function calcular_area($array_coordenadas) {
    // Calcular x, 
    $lat_long = array();
    $x = [];
    $y = [];
    $totalx = 0;

    foreach ($array_coordenadas as $value1) {
        foreach ($value1 as $value) {
            $x[] = $value[0];
        }
    }

    foreach ($array_coordenadas as $value1) {
        foreach ($value1 as $value) {
            $y[] = $value[1];
        }
    }

    for ($i=0; $i <(count($x)-1) ; $i++) { 
        $coordenadas = [$x[$i], $y[$i+1]];
        array_push($lat_long, $coordenadas);
    }

    foreach ($lat_long as $value) {
        $total = $value[0] * $value[1];
        $totalx+= $total;
    }

    // Calcular Y, 
    $lat_long = array();
    $x = [];
    $y = [];
    $totaly = 0;

    foreach ($array_coordenadas as $value1) {
        foreach ($value1 as $value) {
            $x[] = $value[1];
        }
    }

    foreach ($array_coordenadas as $value1) {
        foreach ($value1 as $value) {
            $y[] = $value[0];
        }
    }

    for ($i=0; $i <(count($x)-1) ; $i++) { 
        $coordenadas = [$x[$i], $y[$i+1]];
        array_push($lat_long, $coordenadas);
    }

    foreach ($lat_long as $value) {
        $total = $value[0] * $value[1];
        $totaly+= $total;
    }


    // Cacula HA
    $m2 = 0.5 * ($totaly - $totalx);
    $ha = abs($m2 / 10000);
    //$ha = round($ha, 2, PHP_ROUND_HALF_DOWN);
    return $ha;
}

/*
foreach ($json_data->features as $data) {
    $array_coordenadas = $data->geometry->coordinates;
    $qtd_coordenadas = count($array_coordenadas);

    $str = mb_strtoupper($data->properties->name, 'UTF-8');

    if ($str=='ENTRADA' || $str=='SAIDA' || $str=='SAÍDA') {
        $tem_entrada_saida = 'S';
    }
}

if ($tem_entrada_saida=='N') {
    echo 'Nao achei ENTRADA ou SAIDA';
    exit;
}

include "conecta_mysql.inc";

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");
$array_categoria = '001!002!003!004!005';

foreach ($json_data->features as $data) {
    $array_coordenadas = $data->geometry->coordinates;
    $qtd_coordenadas = count($array_coordenadas);

    $pasto = $data->properties->name;
    $type = $data->geometry->type;

    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_descricao='$pasto' AND 
              tbl_pasto_lixeira=0"); 

    $num_rows_pasto = mysqli_num_rows($tbl_pasto);

    if ($num_rows_pasto==0) {
        if ($type=='Polygon') {
            $str = mb_strtoupper($data->properties->name, 'UTF-8');

            if ($str=='ENTRADA') {
                $tipo_curral = 'E';
                $modulo = 999;
            }
            else if ($str=='SAIDA' || $str=='SAÍDA') {
                $tipo_curral = 'S';
                $modulo = 999;
            }
            else {
                $tipo_curral = '';
                $modulo = 1006;
            }

            $sql = "INSERT INTO tbl_pasto (
                    tbl_pasto_codigo_local,
                    tbl_pasto_descricao,
                    tbl_pasto_latitude,
                    tbl_pasto_longitude,
                    tbl_pasto_area,
                    tbl_pasto_modulo,
                    tbl_pasto_tipo_capim,
                    tbl_pasto_descricao_lote,
                    tbl_pasto_tipo_curral,
                    tbl_pasto_incluido_em,
                    tbl_pasto_incluido_por,
                    tbl_pasto_alterado_em,
                    tbl_pasto_alterado_por,
                    tbl_pasto_lixeira,
                    tbl_pasto_lixeira_em,
                    tbl_pasto_lixeira_por,
                    tbl_pasto_array_categoria,
                    tbl_pasto_array_qtd_animais_macho,
                    tbl_pasto_array_qtd_animais_femea,
                    tbl_pasto_array_qtd_animais_ambos,
                    tbl_pasto_data_com_animais,
                    tbl_pasto_data_com_animais_anterior,
                    tbl_pasto_data_sem_animais,
                    tbl_pasto_data_sem_animais_anterior
                    ) 
                    VALUES (
                            '$local',
                            '$pasto',
                            null,
                            null,
                            null,
                            '$modulo',
                            0,
                            null,
                            '$tipo_curral',
                            '$data_sistema',
                            '$nomeusuario',
                            null,
                            null,
                            0,
                            null,
                            null,
                            '$array_categoria',
                            '!!!!',
                            '!!!!',
                            null,
                            '$data_sistema',
                            '$data_sistema',
                            '$data_sistema',
                            '$data_sistema'
                    )";


            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                //header('Content-type: application/json');
                //echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
                echo 'Ocorreu um erro na gravação ' . $erro_mysql . '<br>';
            }
        }
    }
}
*/
echo 'FIM';
exit;
?>