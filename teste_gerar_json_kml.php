<?php

if (file_exists('000000077.kml')) {
    $xml = simplexml_load_file('000000077.kml');
} else {
    exit('Failed to open 000000077.kml');
}

$geojson = array( 'type' => 'FeatureCollection', 'features' => array());

if ($xml->Document->Folder) {
    // pega coordenadas da tag folder
    foreach ($xml->Document->Folder->Placemark as $placemark) {
        if ($placemark->Polygon) {
            $coordinates = $placemark->Polygon->outerBoundaryIs->LinearRing->coordinates->__toString();
        }
        else if ($placemark->Point) {
            $coordinates = $placemark->Point->coordinates->__toString();
        }
        else if ($placemark->LineString) {
            $coordinates = $placemark->LineString->coordinates->__toString();
        }

        $nome = $placemark->name->__toString();

        $lat_long = array();
        $coor = explode(" ", $coordinates);
        $qtd_coor = count($coor);

        if ($placemark->Polygon || $placemark->LineString) {
            $qtd_coor--;
        }

        for ($i=0; $i < $qtd_coor; $i++) { 
            $dados = $coor[$i];

            $dados = ltrim($dados);
            $dados = rtrim($dados);        
            $dados = explode(",", $dados);

            $qtd_dados = count($dados);

            $dados[0] = ltrim($dados[0]);
            $dados[0] = rtrim($dados[0]);  
            $dados[1] = ltrim($dados[1]);
            $dados[1] = rtrim($dados[1]);  
            $dados[2] = ltrim($dados[2]);
            $dados[2] = rtrim($dados[2]);  
            $dados[0] = (float)$dados[0];
            $dados[1] = (float)$dados[1];

            if ($dados[2]!=0) {
                $dados[2] = (float)$dados[2];
            } 
            else {
                $dados[2] = 0;
            }

            if ($placemark->Point) {
                $coordenadas = [$dados[0],$dados[1],$dados[2]];
            }
            else {
                $coordenadas = array(
                    $dados[0],
                    $dados[1],
                    $dados[2]
                    );

                array_push($lat_long, $coordenadas);
            }
        }

        if ($placemark->Polygon) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'Polygon','coordinates' => array($lat_long)
                )
            );
        }
        else if ($placemark->Point) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'Point','coordinates' => $coordenadas
                )
            );
        }
        /*else if ($placemark->LineString) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'LineString','coordinates' => array($lat_long)
                )
            );
        }*/

        array_push($geojson['features'], $marker);
    }

    // pega coordenadas fora da tag folder
    foreach ($xml->Document->Placemark as $placemark) {
        if ($placemark->Polygon) {
            $coordinates = $placemark->Polygon->outerBoundaryIs->LinearRing->coordinates->__toString();
        }
        else if ($placemark->Point) {
            $coordinates = $placemark->Point->coordinates->__toString();
        }
        else if ($placemark->LineString) {
            $coordinates = $placemark->LineString->coordinates->__toString();
        }

        $nome = $placemark->name->__toString();

        $lat_long = array();
        $coor = explode(" ", $coordinates);
        $qtd_coor = count($coor);

        if ($placemark->Polygon || $placemark->LineString) {
            $qtd_coor--;
        }

        for ($i=0; $i < $qtd_coor; $i++) { 
            $dados = $coor[$i];

            $dados = ltrim($dados);
            $dados = rtrim($dados);        
            $dados = explode(",", $dados);

            $qtd_dados = count($dados);

            $dados[0] = ltrim($dados[0]);
            $dados[0] = rtrim($dados[0]);  
            $dados[1] = ltrim($dados[1]);
            $dados[1] = rtrim($dados[1]);  
            $dados[2] = ltrim($dados[2]);
            $dados[2] = rtrim($dados[2]);  
            $dados[0] = (float)$dados[0];
            $dados[1] = (float)$dados[1];

            if ($dados[2]!=0) {
                $dados[2] = (float)$dados[2];
            } 
            else {
                $dados[2] = 0;
            }

            if ($placemark->Point) {
                $coordenadas = [$dados[0],$dados[1],$dados[2]];
            }
            else {
                $coordenadas = array(
                    $dados[0],
                    $dados[1],
                    $dados[2]
                    );

                array_push($lat_long, $coordenadas);
            }
        }

        if ($placemark->Polygon) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'Polygon','coordinates' => array($lat_long)
                )
            );
        }
        else if ($placemark->Point) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'Point','coordinates' => $coordenadas
                )
            );
        }
        /*else if ($placemark->LineString) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'LineString','coordinates' => array($lat_long)
                )
            );
        }*/

        array_push($geojson['features'], $marker);
    }

}
else {
    foreach ($xml->Document->Placemark as $placemark) {
        if ($placemark->Polygon) {
            $coordinates = $placemark->Polygon->outerBoundaryIs->LinearRing->coordinates->__toString();
        }
        else if ($placemark->Point) {
            $coordinates = $placemark->Point->coordinates->__toString();
        }
        else if ($placemark->LineString) {
            $coordinates = $placemark->LineString->coordinates->__toString();
        }

        $nome = $placemark->name->__toString();

        $lat_long = array();
        $coor = explode(" ", $coordinates);
        $qtd_coor = count($coor);

        if ($placemark->Polygon || $placemark->LineString) {
            $qtd_coor--;
        }

        for ($i=0; $i < $qtd_coor; $i++) { 
            $dados = $coor[$i];

            $dados = ltrim($dados);
            $dados = rtrim($dados);        
            $dados = explode(",", $dados);

            $qtd_dados = count($dados);

            $dados[0] = ltrim($dados[0]);
            $dados[0] = rtrim($dados[0]);  
            $dados[1] = ltrim($dados[1]);
            $dados[1] = rtrim($dados[1]);  
            $dados[2] = ltrim($dados[2]);
            $dados[2] = rtrim($dados[2]);  
            $dados[0] = (float)$dados[0];
            $dados[1] = (float)$dados[1];

            if ($dados[2]!=0) {
                $dados[2] = (float)$dados[2];
            } 
            else {
                $dados[2] = 0;
            }

            if ($placemark->Point) {
                $coordenadas = [$dados[0],$dados[1],$dados[2]];
            }
            else {
                $coordenadas = array(
                    $dados[0],
                    $dados[1],
                    $dados[2]
                    );

                array_push($lat_long, $coordenadas);
            }
        }

        if ($placemark->Polygon) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'Polygon','coordinates' => array($lat_long)
                )
            );
        }
        else if ($placemark->Point) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'Point','coordinates' => $coordenadas
                )
            );
        }
        /*else if ($placemark->LineString) {
            $marker = array(
                'type' => 'Feature',
                'properties' => array('name' => $nome),
                'geometry' => array('type' => 'LineString','coordinates' => array($lat_long)
                )
            );
        }*/

        array_push($geojson['features'], $marker);
    }
}


@ session_start(); 
//$cnpj_cliente = $_SESSION['id_cliente'];
$cnpj_cliente = '97174041604';
$local = '000000191';

$arquivo = 'mapa/'.$cnpj_cliente.'/'.$local.'.json';

$json = json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$file = fopen(__DIR__ . '/' . $arquivo,'w');
fwrite($file, $json);
fclose($file);

// Gerar os pastos no banco de dados do local
$tem_entrada_saida = 'N';

$json_data = json_decode(file_get_contents($arquivo));

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

$json_data = json_decode(file_get_contents($arquivo));

foreach ($json_data->features as $data) {
    $array_coordenadas = $data->geometry->coordinates;
    $qtd_coordenadas = count($array_coordenadas);

    $pasto = mb_strtoupper($data->properties->name, 'UTF-8');
    $type = $data->geometry->type;

    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_descricao='$pasto' AND 
              tbl_pasto_codigo_local='$local' AND
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

echo 'FIM';


/*$coordenadas1 = array(
array(-42.47505207071206,-19.96941782149035,0),
array(-42.47487393213748,-19.97053690777131,0),
array(-42.47481536908192,-19.97067690642956,0),
array(-42.47452205357528,-19.97136002531859,0),
array(-42.47440328521007,-19.97208166619932,0),
array(-42.47478033209956,-19.97252638322318,0),
array(-42.47421114260601,-19.97273337695487,0),
array(-42.47391419293471,-19.97270541005402,0),
array(-42.47371655547168,-19.97206676624955,0),
array(-42.47381052546316,-19.97098431908647,0),
array(-42.47382959709633,-19.96997600283261,0),
array(-42.47386879721966,-19.96904805676627,0),
array(-42.47451792670747,-19.96887661491661,0),
array(-42.47453950237763,-19.9691788584498,0),
array(-42.47505207071206,-19.96941782149035,0)
);

$coordenadas2 = array(
array(-42.47383161826744,-19.96921874662537,0),
array(-42.47364126660569,-19.96933946868695,0),
array(-42.47352515318722,-19.96927448670965,0),
array(-42.47338251122642,-19.96994728574661,0),
array(-42.4731991457243,-19.97142398467132,0),
array(-42.47316913498775,-19.9723600058816,0),
array(-42.47339748799013,-19.97281353837557,0),
array(-42.47351286996099,-19.97313433578064,0),
array(-42.47374288998698,-19.9733914829079,0),
array(-42.47407784406623,-19.97341942458882,0),
array(-42.47438006527586,-19.97334020145963,0),
array(-42.47492171386853,-19.97314913352254,0),
array(-42.47525068035674,-19.97286927516778,0),
array(-42.4753811463348,-19.97251617349552,0),
array(-42.4757186279269,-19.97259625303921,0),
array(-42.47542259187986,-19.97311790753902,0),
array(-42.47519081588791,-19.973536966422,0),
array(-42.47431754904449,-19.97365472345583,0),
array(-42.47352528273794,-19.97372995382927,0),
array(-42.47258724071703,-19.97387217424591,0),
array(-42.47267763649808,-19.97280413579806,0),
array(-42.47287823071488,-19.97148585230441,0),
array(-42.47305065919154,-19.97014746704742,0),
array(-42.47322223869309,-19.96913719357259,0),
array(-42.47364136805376,-19.96906289497199,0),
array(-42.47383161826744,-19.96921874662537,0)
);


$lat_long1 = array();
$lat_long2 = array();

for ($i=0; $i < count($coordenadas1); $i++) { 
    $dados = $coordenadas1[$i];

    $coor = array(
        $dados[0],
        $dados[1],
        $dados[2]
    );

    array_push($lat_long1, $coor);
}

for ($i=0; $i < count($coordenadas2); $i++) { 
    $dados = $coordenadas2[$i];

    $coor = array(
        $dados[0],
        $dados[1],
        $dados[2]
    );

    array_push($lat_long2, $coor);
}

$geojson = array( 'type' => 'FeatureCollection', 'features' => array());

$marker = array(
    'type' => 'Feature',
    'properties' => array('name' => 'FRENTE TOMBADA'),
    'geometry' => array('type' => 'Polygon','coordinates' => array($lat_long1)
    )
);

array_push($geojson['features'], $marker);

$marker = array(
    'type' => 'Feature',
    'properties' => array('name' => 'Frente Baixada'),
    'geometry' => array('type' => 'Polygon','coordinates' => array($lat_long2)
    )
);

array_push($geojson['features'], $marker);

$arquivo = '000000077.json';
$json = json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$file = fopen(__DIR__ . '/' . $arquivo,'w');
fwrite($file, $json);
fclose($file);

*/

?>