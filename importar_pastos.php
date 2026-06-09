<?php 

@ session_start(); 
$id_cliente = $_SESSION['id_cliente'];
$local = $_POST['codigo_local_importar'];

if ($_FILES['arquivo_kml']['error']==4) {
	echo "<script> alert ('Não foi possível verificar o arquivo KML.'); location.href='form_tabela_pastos.php'</script>";
	exit;
}

$_UP['pasta'] = 'kml/';
$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
$_UP['extensoes'] = array('kml');

// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
 
if ($_FILES['arquivo_kml']['error'] != 0) {
	die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo_kml']['error']]);
	exit; 
}
 
$tmp = explode('.', $_FILES['arquivo_kml']['name']);
$extensao = end($tmp);

if (array_search($extensao, $_UP['extensoes']) === false) {
	echo "<script> alert ('Envie arquivo com a extensão kml.'); location.href='form_tabela_pastos.php'</script>";
	exit;
}
else if ($_UP['tamanho'] < $_FILES['arquivo_kml']['size']) { 
	echo "<script> alert ('O arquivo enviado é muito grande, envie arquivos de até 2Mb.'); location.href='form_tabela_pastos.php'</script>";
	exit;
}
else {
	$nome_final = $_FILES['arquivo_kml']['name'];
}
 
if (move_uploaded_file($_FILES['arquivo_kml']['tmp_name'], $_UP['pasta'] . $nome_final)) {
	$name = $_UP['pasta'] . $nome_final;
} 
else {
	echo "<script> alert ('Não foi possível enviar o arquivo, tente novamente.'); location.href='form_tabela_pastos.php'</script>";
	exit;
}
 
if (file_exists($name)) {
    $xml = simplexml_load_file($name);
} 
else {
	echo "<script> alert ('Não foi possível abrir o arquivo KML.'); location.href='form_tabela_pastos.php'</script>";
	exit;
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
$cnpj_cliente = $_SESSION['id_cliente'];

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
	echo "<script> alert ('Não achei ENTRADA ou SAIDA no kml.'); location.href='form_tabela_pastos.php'</script>";
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

    $area = calcular_area($array_coordenadas);
    $area = floatval(substr($area, 0, 7));

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
                $modulo = 1;
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
                            '$area',
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
				mysqli_close($conector);
				echo "<script> alert ('Ocorreu um erro na gravação '" . $erro_mysql ."); location.href='form_tabela_pastos.php'</script>";
            }
        }
    }
    else {
    	$reg_pasto = mysqli_fetch_object($tbl_pasto);
    	$codigo_id = $reg_pasto->tbl_pasto_id;

		$sql = ("UPDATE tbl_pasto SET tbl_pasto_area='$area'
			WHERE tbl_pasto_id='$codigo_id'");
		$resultado = mysqli_query($conector,$sql);

        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
			mysqli_close($conector);
			echo "<script> alert ('Ocorreu um erro na atualizaçao da area '" . $erro_mysql ."); location.href='form_tabela_pastos.php'</script>";
    	}
    }
}

mysqli_close($conector);

echo "<script> alert ('Mapa importado com sucesso!'); location.href='form_tabela_pastos.php'</script>";


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

?>