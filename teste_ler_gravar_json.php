<?php

$json_data = json_decode(file_get_contents('kml/000000077.json'));
//$json_data = json_decode(file_get_contents('77.json'));
//$arquivo = '77.json';

$achei = 'N';

foreach ($json_data->features as $data) {

    $array_coordenadas = $data->geometry->coordinates;
    $qtd_coordenadas = count($array_coordenadas);

    $str = mb_strtoupper($data->properties->name, 'UTF-8');

    if ($str=='ENTRADA' || $str=='SAIDA' || $str=='SAÍDA') {
        echo '<br>Pasto: ' .$data->properties->name;
        $achei = 'S';
    }

/*    if ($data->properties->name=='FRENTE BAIXADA') {

        echo '<br>Pasto: ' .$data->properties->name;

        $data->properties->name='FRENTE ALTERADA';

        $json_str = json_encode($json_data, JSON_UNESCAPED_UNICODE);

        print_r($json_str);

        $file = fopen(__DIR__ . '/' . $arquivo,'w');
        fwrite($file, $json_str);
        fclose($file);
    }*/

    /*for ($i=0; $i < $qtd_coordenadas; $i++) { 
        $qtd = count($array_coordenadas[$i]);
        $array = $array_coordenadas[$i];

        echo ' - Quantas coordenadas: ' . $qtd;

        for ($j=0; $j < $qtd; $j++) { 
            print_r($array . '<br>');
        }
    }*/

}

if ($achei=='N') {
    echo 'Nao achei ENTRADA ou SAIDA';
}
exit;
?>