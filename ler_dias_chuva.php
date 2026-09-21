<?php 
	include "conecta_mysql.inc";

	$local= $_POST['local_id'];
	$data_sistema = date("Y-m-d");
	$partes = explode("-", $data_sistema);
	$mes = $partes[1];
	$ano = $partes[0];
	$volume_mes = 0;
	$volume_ano = 0;
	$dias_chuva = 0;
    $ano_inicial = $ano - 4;
    $ano_final = $ano;
	$arr = [];
	$valor = array();

    for ($i = 0; $i <= 24; $i++) {
        $valor[$i]=0;
    }

	for ($m=1; $m <=12; $m++) { 
        $mes_index = ltrim($m, "0");
        $total_volume_mes[$mes_index]=0;
        $dias_chuva_mes[$mes_index]=0;
    }

	$data_extenco=new DateTime($data_sistema);

	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$mes_extenco =  strftime('%B', strtotime($data_extenco->format('Y-m')));
	$mes_extenco = ucfirst(utf8_encode($mes_extenco));

	$chuva= mysqli_query($conector, "SELECT * FROM tbl_chuva
	    WHERE year(tbl_chuva_data)='$ano' AND 
              tbl_chuva_local='$local'");

	$num_rows = mysqli_num_rows($chuva);  

	if ($num_rows!=0) {
	    while ($reg_chuva = mysqli_fetch_object($chuva)) {
	        $volume_chuva=$reg_chuva->tbl_chuva_volume_chuva;
	        $volume_ano+=$volume_chuva;

	        $data_chuva=$reg_chuva->tbl_chuva_data;
			$partes = explode("-", $data_chuva);
			$mes_chuva = $partes[1];
			$ano_chuva = $partes[0];

            $data_chuva=new DateTime($reg_chuva->tbl_chuva_data);
            $index_chuvas=$data_chuva->format('m');
            $index_chuvas=ltrim($index_chuvas, "0");

			if ($mes_chuva==$mes && $ano_chuva==$ano && $volume_chuva>0) {
				$volume_mes+=$volume_chuva;
				$dias_chuva++;
			}

            if ($volume_chuva!=0 && $volume_chuva!='') {
                $total_volume_mes[$index_chuvas]+=$volume_chuva;
                $dias_chuva_mes[$index_chuvas]++;
            }
	    }

        $valor[0]=$total_volume_mes[1];
        $valor[1]=$dias_chuva_mes[1];
        $valor[2]=$total_volume_mes[2];
        $valor[3]=$dias_chuva_mes[2];
        $valor[4]=$total_volume_mes[3];
        $valor[5]=$dias_chuva_mes[3];
        $valor[6]=$total_volume_mes[4];
        $valor[7]=$dias_chuva_mes[4];
        $valor[8]=$total_volume_mes[5];
        $valor[9]=$dias_chuva_mes[5];
        $valor[10]=$total_volume_mes[6];
        $valor[11]=$dias_chuva_mes[6];
        $valor[12]=$total_volume_mes[7];
        $valor[13]=$dias_chuva_mes[7];
        $valor[14]=$total_volume_mes[8];
        $valor[15]=$dias_chuva_mes[8];
        $valor[16]=$total_volume_mes[9];
        $valor[17]=$dias_chuva_mes[9];
        $valor[18]=$total_volume_mes[10];
        $valor[19]=$dias_chuva_mes[10];
        $valor[20]=$total_volume_mes[11];
        $valor[21]=$dias_chuva_mes[11];
        $valor[22]=$total_volume_mes[12];
        $valor[23]=$dias_chuva_mes[12];

        /*$str=$valor[0] . '<|>';

        for ($i=1; $i<=24; $i++){
            $str.=$valor[$i] . '<|>';
        }*/

        $str = json_encode($valor);

	   	header('Content-type: application/json');
	    echo json_encode(array('success' => true, 'dias_chuva' => $dias_chuva, 'volume_mes' => $volume_mes, 'volume_ano' => $volume_ano, 'mes_extenco' => $mes_extenco, 'array_chuva' => $str));
		mysqli_close($conector);
		exit;
	}
	else {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Volume de chuva não cadastrado para esse mes'));
	    mysqli_close($conector);
		exit;
	}

?>