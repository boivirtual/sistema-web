<?php
    include "conecta_mysql.inc";

    include('exec_time.php');

    startExec();

    sleep(2);

    //$local = $_POST['local'];

    $local = '000000000';

    if ($local=='000000000') {
        $wlocal = '';
        $wlocal_anterior='';
    }
    else {
        $wlocal = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal.= $local;
        $wlocal.= ")";

        $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= ")";
        $wlocal_anterior.= " OR tbl_animal_codigo_fazenda_anterior IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= "))";
    }

    //$array_cat = $_POST['array-cat'];

    $array_cat = [001,002,003,004,005];

    if ($array_cat=='') {
        $wcategoria = '';
    }
    else {
        $wcategoria = " AND tab_codigo_categoria_idade IN(";
        $wcategoria.= array_cat;
        $wcategoria.= ")";
    }

    //$data_inicial = $_POST['data_inicial'];
    $data_inicial = '2022-05';

    $partes = explode("-", $data_inicial);
    $ano_inicial = $partes[0];
    $mes_inicial = $partes[1];
    $dia_inicial = '01';

    //$data_final = $_POST['data_final'];
    $data_final = '2023-04';

    $partes = explode("-", $data_final);
    $ano_final = $partes[0];
    $mes_final = $partes[1];
    $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

    $data1 = new DateTime($data_inicial);
    $data2 = new DateTime($data_final);
    $intervalo = $data1->diff($data2);
    $qtd_meses = $intervalo->y * 12 + $intervalo->m + $intervalo->d/30 + $intervalo->h / 24;
    $qtd_meses++;
    $ano_atual = $ano_inicial;

    $data_array=new DateTime($data_inicial);

    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
    $mes_extenco = ucfirst(utf8_encode($mes_extenco));
    $array_mes_extenco[0]=$mes_extenco.'/'.$ano_atual;

    $array_mes[0]=$data_array->format('m');
    $array_ano[0]=$data_array->format('Y');

    $ano_mes = $data_array->format('Y').$data_array->format('m');
    $array_mes_ano[$ano_mes]=$ano_mes;
    $array_peso[$ano_mes]=0;

    for ($i=1; $i < $qtd_meses; $i++) { 
        $proximo_mes=1;
        $data_array->add(new DateInterval('P'.$proximo_mes.'M'));

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        $mes_extenco =  strftime('%B', strtotime($data_array->format('Y-m')));
        $mes_extenco = ucfirst(utf8_encode($mes_extenco));
        $array_mes_extenco[$i]=$mes_extenco.'/'.$ano_atual;

        if ($mes_extenco == 'Dezembro') {
            $ano_atual++;
        }

        $array_mes[$i]=$data_array->format('m');
        $array_ano[$i]=$data_array->format('Y');
        $ano_mes = $data_array->format('Y').$data_array->format('m');
        $array_mes_ano[$ano_mes]=$ano_mes;
        $array_peso[$ano_mes]=0;
    } 

    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $gmd_total = 0;
    $numero_gmd = 0;

    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'" . $wcategoria);

    $num_rows = mysqli_num_rows($tbl_categoria);    

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;
            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

            if ($reg_categoria->tab_categoria_idade_ate==999999999) {
                $desc_categoria = ' > 36 meses';
            }
            else {
                $desc_categoria =  $reg_categoria->tab_categoria_idade_de . ' a ' .
                $reg_categoria->tab_categoria_idade_ate . ' meses';
            }

            $array_categoria[$codigo_categoria] = $codigo_categoria;
            $array_desc_categoria[$codigo_categoria] = $desc_categoria;
            $array_qtd_macho_categoria[$codigo_categoria] = 0;
            $array_qtd_femea_categoria[$codigo_categoria] = 0;
            $array_gmd_macho_categoria[$codigo_categoria] = 0;
            $array_gmd_femea_categoria[$codigo_categoria] = 0;
        }
    }   

    $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
            WHERE tbl_animal_lixeira=0 AND
                  tbl_animal_ativo='S'" . $wlocal .
                  " OR (DATE(tbl_animal_baixado_em)>='$data_inicial' AND DATE(tbl_animal_baixado_em)<='$data_final' AND tbl_animal_ativo='N' AND (tbl_animal_situacao='V' OR tbl_animal_situacao='M'))" . $wlocal_anterior .
                " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC"); 
    $num_rows_animais = mysqli_num_rows($tbl_animal);

    if ($num_rows_animais!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
            $codigo = $reg_animal->tbl_animal_codigo_id;
            $sexo = $reg_animal->tbl_animal_sexo; 

            $data_peso_nascimento=0;
            $peso_nascimento=0;

            if ($reg_animal->tbl_animal_primeiro_peso!='') {
                $data_primeiro_peso = substr($reg_animal->tbl_animal_data_primeiro_peso, 0, 10);

                if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                    $data_peso_nascimento = $data_primeiro_peso;
                    $peso_nascimento = $reg_animal->tbl_animal_primeiro_peso;
                }
            }

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
            $data_acompanhamento_calculo = $data_final;
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria_animal = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria_animal);    

            if ($num_rows!=0) {
                while ($reg_cat_animal = mysqli_fetch_object($categoria_animal)) {
                    $idade_de = $reg_cat_animal->tab_categoria_idade_de;
                    $idade_ate = $reg_cat_animal->tab_categoria_idade_ate;
                    $codigo_cat = $reg_cat_animal->tab_codigo_categoria_idade;
                    $codigo_categoria_animal = 0;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        if ($wcategoria=='') {
                            $codigo_categoria_animal = $codigo_cat;
                        }
                        else {
                            foreach ($array_categoria as $value) {
                                if ($value==$codigo_cat) {
                                    $codigo_categoria_animal = $codigo_cat;
                                }
                            }
                        }
                    }
                }                   
            }

            if ($codigo_categoria_animal!=0) {
                if ($data_peso_nascimento==0) {
                    $data_peso_inicial='0000-00-00';
                    $peso_inicial = 9999;
                }

                $data_peso_final = '0000-00-00';
                $peso_final = 9999;

                $tbl_peso = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                    WHERE tbl_ite_pesagem_data_emissao>='$data_inicial' AND 
                          tbl_ite_pesagem_data_emissao<='$data_final' AND 
                          tbl_ite_pesagem_codigo_id_animal='$codigo' AND 
                          tbl_ite_pesagem_peso !=0
                        ORDER BY tbl_ite_pesagem_data_emissao ASC");

                $num_rows_peso = mysqli_num_rows($tbl_peso);    

                if ($num_rows_peso!=0) {
                    if ($data_peso_nascimento!=0) {
                        $partes = explode("-", $data_peso_nascimento);

                        for ($i=0; $i < $qtd_meses; $i++) { 
                            if ($array_mes[$i]==$partes[1] && $array_ano[$i]==$partes[0]) {
                                $peso_inicial=$peso_nascimento;
                            }
                        }
                    }

                    while ($reg_peso = mysqli_fetch_object($tbl_peso)) {
                        $data_peso = $reg_peso->tbl_ite_pesagem_data_emissao;
                        $peso = $reg_peso->tbl_ite_pesagem_peso;

                        if ($peso == 0) {
                            $peso = 9999;
                        }

                        $partes = explode("-", $data_peso_inicial);
                        $ano_mes_peso_inicial = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso_final);
                        $ano_mes_peso_final = $partes[0].$partes[1];

                        $partes = explode("-", $data_peso);
                        $ano_mes_peso = $partes[0].$partes[1];

                        if ($data_peso_inicial=='0000-00-00') {
                            $data_peso_inicial=$data_peso;
                            $peso_inicial=$peso;
                        }

                        if ($ano_mes_peso_inicial==$ano_mes_peso) {
                            if ($peso<$peso_inicial && $peso!=0) {
                                $data_peso_inicial=$data_peso;
                                $peso_inicial = $peso;
                            }
                        }

                        if ($ano_mes_peso_inicial!=$ano_mes_peso) {
                            if ($ano_mes_peso_final==$ano_mes_peso) {
                                if ($peso<$peso_final && $peso!=0) {
                                    $data_peso_final=$data_peso;
                                    $peso_final = $peso;
                                }
                            }
                            else {
                                $data_peso_final=$data_peso;
                                $peso_final = $peso;
                            }
                        }
                    }  
                } 

                if ($peso_inicial==9999) {
                    $peso_inicial = 0;
                }

                if ($peso_final==9999) {
                    $peso_final = 0;
                }

                $diferenca = strtotime($data_peso_final) - strtotime($data_peso_inicial);
                $dias = floor($diferenca / (60 * 60 * 24)); 

                if ($peso_final && $peso_inicial) {
                    $ganho = $peso_final - $peso_inicial;
                }
                else {
                    $ganho = 0;
                }

                if ($ganho!=0 && $dias!=0) {
                    $gmd = $ganho / $dias;
                }
                else {
                    $gmd=0;
                }

                if ($gmd!=0 && $codigo_categoria_animal!=0) {
                    if ($sexo=="M") {
                        $array_gmd_macho_categoria[$codigo_categoria_animal]+=$gmd;
                        $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                    }
                    else {
                        $array_gmd_femea_categoria[$codigo_categoria_animal]+=$gmd;
                        $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                    }
                }
            }
        }
    }

    for ($i=1; $i <= 5; $i++) { 
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        if ($array_qtd_macho_categoria[$j]!=0) {
            $gmd = $array_gmd_macho_categoria[$j]/
                   $array_qtd_macho_categoria[$j];

            $gmd_total+= $array_gmd_macho_categoria[$j];

            if ($gmd!=0) {
                $numero_gmd+= $array_qtd_macho_categoria[$j];
            }
        }

        if ($array_qtd_femea_categoria[$j]!=0) {
            $gmd = $array_gmd_femea_categoria[$j]/
                   $array_qtd_femea_categoria[$j];

            $gmd_total+= $array_gmd_femea_categoria[$j];

            if ($gmd!=0) {
                $numero_gmd+= $array_qtd_femea_categoria[$j];
            }
        }
    }

    if ($gmd_total!=0 && $numero_gmd>0) {
        $media_gmd = $gmd_total / $numero_gmd;
        $media_gmd_edi = number_format($media_gmd,3,',','.');
    }
    else {
        $media_gmd_edi = 0;
    }

    $p = (object)[
        'valor' => $media_gmd_edi,
    ];
    $arr[] = $p;

    echo json_encode($arr, JSON_UNESCAPED_UNICODE);

    echo '</br>';

    echo endExec();

?>