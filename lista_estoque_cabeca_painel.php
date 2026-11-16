<?php
    include "conecta_mysql.inc";

    @ session_start(); 
    $controle_estoque = $_SESSION['controle_estoque'];

    $arr[] = '';

    $array_cat = $_POST['array_cat'];
    $array_macho = $_POST['array_macho'];
    $array_femea = $_POST['array_femea'];
    $qtd_categoria = count($array_cat);

    $array_cat = implode(',', $array_cat);

    if ($array_cat=='') {
        $wcategoria = '';
    }
    else {
        $wcategoria = " AND tab_codigo_categoria_idade IN(";
        $wcategoria.= $array_cat;
        $wcategoria.= ")";
    }

    $local = $_POST['local'];

    if ($local=='000000000') {
        $wlocal = '';
        $wlocal_fechamento = '';
        $wlocal_pessoa = '';
        $wlocal_fazenda='';
        $wlocal_anterior='';
    }
    else {
        $wlocal = " AND tbl_mov_estoque_local IN(";
        $wlocal.= $local;
        $wlocal.= ")";

        $wlocal_fechamento = " AND tbl_fechamento_local IN(";
        $wlocal_fechamento.= $local;
        $wlocal_fechamento.= ")";

        $wlocal_pessoa = " AND tbl_pessoa_id IN(";
        $wlocal_pessoa.= $local;
        $wlocal_pessoa.= ")";

        $wlocal_fazenda = " AND tbl_animal_codigo_fazenda IN(";
        $wlocal_fazenda.= $local;
        $wlocal_fazenda.= ")";

        $wlocal_anterior = " AND (tbl_animal_codigo_fazenda IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= ")";
        $wlocal_anterior.= " OR (tbl_animal_codigo_origem IN(";
        $wlocal_anterior.= $local;
        $wlocal_anterior.= ") AND tbl_animal_situacao='V'))";
    }

    $data_hoje=new DateTime();
    $mes_hoje=$data_hoje->format('m');
    $ano_hoje=$data_hoje->format('Y');

    $data_inicial = $_POST['data_inicial'];

    $partes = explode("-", $data_inicial);
    $mes_inicial = $partes[1];
    $ano_inicial = $partes[0];
    $dia_inicial = '01';

    $data_final = $_POST['data_final'];

    $partes = explode("-", $data_final);
    $mes_final = $partes[1];
    $ano_final = $partes[0];
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

    // ESTOQUE CABECA

    $estoque_final = 0;
    $estoque_inicial = 0;
    $estoque_ent_nasc = 0;
    $estoque_ent_compra = 0;
    $estoque_ent_transf = 0;
    $estoque_ent_outra = 0;

    $estoque_sai_morte = 0;
    $estoque_sai_venda = 0;
    $estoque_sai_transf = 0;
    $estoque_sai_outra = 0;

    $total_ent_nasc = 0;
    $total_ent_compra = 0;
    $total_ent_transf = 0;
    $total_ent_outra = 0;
    $total_sai_morte = 0;
    $total_sai_venda = 0;
    $total_sai_transf = 0;
    $total_sai_outra = 0;

    $total_media_cabeca = 0;
    $total_media_peso = 0;
    $total_ha = 0;

    //$data_inicial = $data_inicial . '-01';
    //$data_final = $data_final . '-31';

    $data_inicial = $ano_inicial .'-'. $mes_inicial .'-01' ;
    $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
    $gmd_total = 0;
    $numero_gmd = 0;

    // Iniciar array categorias para calculo do GMD
    $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'" . $wcategoria);

    $num_rows = mysqli_num_rows($tbl_categoria);    

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
            $array_qtd_macho_categoria[$codigo_categoria] = 0;
            $array_qtd_femea_categoria[$codigo_categoria] = 0;
            $array_gmd_macho_categoria[$codigo_categoria] = 0;
            $array_gmd_femea_categoria[$codigo_categoria] = 0;

            $valor[] = $codigo_categoria;
        }
    }   

    // Pega hectares das fazentas
    $pessoa= mysqli_query($conector, "SELECT * FROM tbl_pessoa
        WHERE tbl_pessoa_classe=4 AND 
              tbl_pessoa_lixeira=0" . $wlocal_pessoa);
               
    $num_rows_pessoa = mysqli_num_rows($pessoa);

    if ($num_rows_pessoa!=0) {
        while ($reg_pessoa = mysqli_fetch_object($pessoa)) {
            $ha = $reg_pessoa->tbl_pessoa_area_util_fazenda;
            $total_ha+=$ha;
        }
    }

    // Pega estoque anterior a data inicial sem nascimento
    $movimentacao_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
        WHERE tbl_mov_estoque_data_emissao<'$data_inicial' AND 
              tbl_mov_estoque_tipo_movimentacao!='N' AND 
              tbl_mov_estoque_tipo_movimentacao!='A' AND
              tbl_mov_estoque_tipo_movimentacao!='B'" . $wlocal);
               
    $num_rows = mysqli_num_rows($movimentacao_estoque);

    if ($num_rows!=0) {
        while ($reg_mov = mysqli_fetch_object($movimentacao_estoque)) {
            $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
            $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;
            $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

            if ($codigo_id_animal!=999999999) {
                if ($ent_sai=='E') {
                    if ($tipo=='C') {
                        $estoque_ent_compra++;
                    }
                    else if ($tipo=='T') {
                        $estoque_ent_transf++;
                    }
                    else {
                        $estoque_ent_outra++;
                    }
                }
                else {
                    if ($tipo=='M') {
                        $estoque_sai_morte++;   
                    }
                    else if ($tipo=='V') {
                        $estoque_sai_venda++;
                    }
                    else if ($tipo=='T') {
                        $estoque_sai_transf++;
                    }
                    else {
                        $estoque_sai_outra++;
                    }
                }
            }
        }
    }

    // Pega estoque anterior a data inicial nascimento
    $movimentacao_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
        WHERE tbl_mov_estoque_nascimento<'$data_inicial' AND 
              tbl_mov_estoque_tipo_movimentacao='N'" . $wlocal);

    $num_rows = mysqli_num_rows($movimentacao_estoque);

    if ($num_rows!=0) {
        while ($reg_mov = mysqli_fetch_object($movimentacao_estoque)) {
            $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
            $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;
            $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

            if ($codigo_id_animal!=999999999) {
                if ($ent_sai=='E') {
                    if ($tipo=='N') {
                        $estoque_ent_nasc++;   
                    }
                }
            }
        }
    }

    $estoque_inicial = $estoque_ent_nasc + $estoque_ent_compra + 
                       $estoque_ent_transf + $estoque_ent_outra;
    $estoque_inicial = $estoque_inicial - $estoque_sai_morte - 
                       $estoque_sai_venda - $estoque_sai_transf -
                       $estoque_sai_outra;
    // Fim estoque anterior 

    for ($i=0; $i<$qtd_meses; $i++) { 
        $mes_lista = $array_mes[$i];
        $ano_lista = $array_ano[$i];

        $estoque_ent_nasc = 0;
        $estoque_ent_compra = 0;
        $estoque_ent_transf = 0;
        $estoque_ent_outra = 0;

        $estoque_sai_morte = 0;
        $estoque_sai_venda = 0;
        $estoque_sai_transf = 0;
        $estoque_sai_outra = 0;

        // Pega estoque sem nascimento
        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE year(tbl_mov_estoque_data_emissao)='$ano_lista' AND 
                  month(tbl_mov_estoque_data_emissao)='$mes_lista' AND 
                  tbl_mov_estoque_tipo_movimentacao!='N' AND
                  tbl_mov_estoque_tipo_movimentacao!='A' AND 
                  tbl_mov_estoque_tipo_movimentacao!='B' AND 
                  tbl_mov_estoque_codigo_id_animal!=999999999" . $wlocal);

            $num_rows = mysqli_num_rows($mov_estoque);  

            if ($num_rows!=0) {
                while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                    $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                    $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                    if ($ent_sai=='E') {
                        if ($tipo=='C') {
                            $estoque_ent_compra++;
                        }
                        else if ($tipo=='T') {
                            $estoque_ent_transf++;
                        }
                        else{
                            $estoque_ent_outra++;
                        }
                    }
                    else {
                        if ($tipo=='M') {
                            $estoque_sai_morte++;   
                        }
                        else if ($tipo=='V') {
                            $estoque_sai_venda++;
                        }
                        else if ($tipo=='T') {
                          $estoque_sai_transf++;
                        }
                        else {
                           $estoque_sai_outra++;
                        }
                    }
                }
            }

            // Pega estoque nascimento

            $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
                WHERE year(tbl_mov_estoque_nascimento)='$ano_lista' AND 
                      month(tbl_mov_estoque_nascimento)='$mes_lista' AND
                      tbl_mov_estoque_tipo_movimentacao='N' AND
                      tbl_mov_estoque_codigo_id_animal!=999999999" . $wlocal);

            $num_rows = mysqli_num_rows($mov_estoque);  

            if ($num_rows!=0) {
                while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                    $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                    $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                    if ($ent_sai=='E') {
                        if ($tipo=='N') {
                            $estoque_ent_nasc++;   
                        }
                    }
                }
            }

            $estoque_final = $estoque_inicial + $estoque_ent_nasc +
                             $estoque_ent_compra + $estoque_ent_transf + 
                             $estoque_ent_outra;

            $estoque_final = $estoque_final - $estoque_sai_morte - 
                             $estoque_sai_venda - $estoque_sai_transf -
                             $estoque_sai_outra;

            if ($estoque_final==0) {
                $estoque_final = $estoque_inicial;
            }

            $valor[0] = $estoque_final;
            $total_media_cabeca+=$estoque_final;
            $estoque_inicial = $estoque_final;

    }
    // FIM ESTOQUE CABECA

    // ESTOQUE PESO

    $estoque_final = 0;
    $estoque_inicial = 0;
    $estoque_ent_nasc = 0;
    $estoque_ent_compra = 0;
    $estoque_ent_transf = 0;
    $estoque_ent_outra = 0;
    $estoque_sai_morte = 0;
    $estoque_sai_venda = 0;
    $estoque_sai_transf = 0;
    $estoque_sai_outra = 0;

    $total_ent_nasc = 0;
    $total_ent_compra = 0;
    $total_ent_transf = 0;
    $total_ent_outra = 0;
    $total_sai_morte = 0;
    $total_sai_venda = 0;
    $total_sai_transf = 0;
    $total_sai_outra = 0;
    $qtd_mes_media = 0;
    $string_array = array();
    $estoque_inicial_primeiro_mes = 0;
    $somou_estoque_inicial = '';

    for ($i=0; $i<$qtd_meses; $i++) { 

        $mes_lista = $array_mes[$i];
        $ano_lista = $array_ano[$i];

        $ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, $mes_lista, $ano_lista);
        
        $estoque_ent_nasc = 0;
        $estoque_ent_compra = 0;
        $estoque_ent_transf = 0;
        $estoque_ent_outra = 0;
        $estoque_sai_morte = 0;
        $estoque_sai_venda = 0;
        $estoque_sai_transf = 0;
        $estoque_sai_outra = 0;
        $estoque_sem_mov = 0;

        // Pega estoque fechamento
        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
            WHERE year(tbl_fechamento_data)='$ano_lista' AND 
                  month(tbl_fechamento_data)='$mes_lista'" . $wlocal_fechamento);

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $estoque_inicial+= $reg_mov->tbl_fechamento_peso_inicial;

                if ($somou_estoque_inicial=='') {
                    $estoque_inicial_primeiro_mes=$estoque_inicial;
                    $somou_estoque_inicial='S';                    
                }

                $ent_nascimento = $reg_mov->tbl_fechamento_peso_ent_nascimento;
                $ent_compra = $reg_mov->tbl_fechamento_peso_ent_compra;
                $ent_tranferencia = $reg_mov->tbl_fechamento_peso_ent_transferencia;
                $ent_outras = $reg_mov->tbl_fechamento_peso_ent_outras;

                $sai_morte = $reg_mov->tbl_fechamento_peso_sai_morte;
                $sai_venda = $reg_mov->tbl_fechamento_peso_sai_venda;
                $sai_tranferencia = $reg_mov->tbl_fechamento_peso_sai_transferencia;
                $sai_outras = $reg_mov->tbl_fechamento_peso_sai_outras;
                $peso_sem_mov = $reg_mov->tbl_fechamento_peso_sem_movimentacao;

                $estoque_ent_nasc+=$ent_nascimento;
                $estoque_ent_compra+=$ent_compra;
                $estoque_ent_transf+=$ent_tranferencia;
                $estoque_ent_outra+=$ent_outras;
                $estoque_sai_morte+=$sai_morte;   
                $estoque_sai_venda+=$sai_venda;
                $estoque_sai_transf+=$sai_tranferencia;
                $estoque_sai_outra+=$sai_outras;
                $estoque_sem_mov+=$peso_sem_mov;
            }

            $estoque_final = $estoque_inicial + $estoque_ent_nasc +
                             $estoque_ent_compra + $estoque_ent_transf +
                             $estoque_ent_outra + $estoque_sem_mov;

            $estoque_final = $estoque_final - $estoque_sai_morte - 
                             $estoque_sai_venda - $estoque_sai_transf -
                             $estoque_sai_outra;

            $total_mes = $estoque_final;

            $total_ent_nasc+= $estoque_ent_nasc;
            $total_ent_compra+= $estoque_ent_compra;
            $total_ent_transf+= $estoque_ent_transf;
            $total_ent_outra+= $estoque_ent_outra;
            $total_sai_morte+= $estoque_sai_morte;
            $total_sai_venda+= $estoque_sai_venda;
            $total_sai_transf+= $estoque_sai_transf;
            $total_sai_outra+= $estoque_sai_outra;

            if ($estoque_final==0) {
                $estoque_final = $estoque_inicial;
            }

            $valor[1] = $estoque_final;
            $estoque_inicial = 0;
            $total_media_peso+=$estoque_final;

            if ($estoque_final!=0) {
                $qtd_mes_media++;
                $data_processamento = $ano_lista.'-'.$mes_lista.'-'.$ultimo_dia_mes;

                $p = (object)[
                    'data' => $data_processamento,
                    'valor' => intval($estoque_final),
                ];
                $arr[] = $p;
            }
        }
    }

    // Pega movimentação e estoque final do mes atual
    if ($mes_hoje==$mes_lista && $ano_hoje==$ano_lista) {

        // pega o estoque final do mes anterior
        if ($estoque_inicial==0) {

            $mes_ant = $mes_lista - 1;
            $ano_ant = $ano_lista;

            if ($mes_ant==0) {
                $mes_ant=12;
                $ano_ant=$ano_ant - 1;
            }

            $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
                WHERE year(tbl_fechamento_data)='$ano_ant' AND 
                      month(tbl_fechamento_data)='$mes_ant'" . $wlocal_fechamento);

            $num_rows = mysqli_num_rows($mov_estoque);  

            if ($num_rows!=0) {
                while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                    $estoque_inicial+= $reg_mov->tbl_fechamento_peso_final;
                }
            }
        }

        $estoque_ent_nasc = 0;
        $estoque_ent_compra = 0;
        $estoque_ent_transf = 0;
        $estoque_ent_outra = 0;

        $estoque_sai_morte = 0;
        $estoque_sai_venda = 0;
        $estoque_sai_transf = 0;
        $estoque_sai_outra = 0;
        $estoque_final = 0;

        // Pega estoque sem nascimento
        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE year(tbl_mov_estoque_data_emissao)='$ano_lista' AND 
                  month(tbl_mov_estoque_data_emissao)='$mes_lista' AND 
                      tbl_mov_estoque_tipo_movimentacao!='N' AND
                      tbl_mov_estoque_tipo_movimentacao!='A' AND 
                      tbl_mov_estoque_tipo_movimentacao!='B' AND 
                      tbl_mov_estoque_codigo_id_animal!=999999999" . $wlocal);

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;
                $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

                $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_id='$codigo_id_animal'");

                $num_rows_animais = mysqli_num_rows($tbl_animais);
                $peso = 0;

                if ($num_rows_animais!=0) {
                    $reg_animal = mysqli_fetch_object($tbl_animais);

                    $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
                    $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                    $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

                    if ($ultimo_peso!=0 && $ultimo_peso!='') {
                        $peso = $ultimo_peso;
                    }
                    else if ($peso_desmama!=0 && $peso_desmama!='') {
                        $peso = $peso_desmama;
                    }
                    else if ($primeiro_peso!=0 && $primeiro_peso!=''){
                        $peso = $primeiro_peso;
                    }
                }

                if ($ent_sai=='E') {
                    if ($tipo=='C') {
                        $estoque_ent_compra+=$peso;
                    }
                    else if ($tipo=='T') {
                        $estoque_ent_transf+=$peso;
                    }
                    else{
                        $estoque_ent_outra+=$peso;
                    }
                }
                else {
                    if ($tipo=='M') {
                        $estoque_sai_morte+=$peso;   
                    }
                    else if ($tipo=='V') {
                        $estoque_sai_venda+=$peso;
                    }
                    else if ($tipo=='T') {
                        $estoque_sai_transf+=$peso;
                    }
                    else {
                        $estoque_sai_outra+=$peso;
                    }
                }
            }
        }

        // Pega estoque nascimento

        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE year(tbl_mov_estoque_nascimento)='$ano_lista' AND 
                  month(tbl_mov_estoque_nascimento)='$mes_lista' AND
                  tbl_mov_estoque_tipo_movimentacao='N' AND
                  tbl_mov_estoque_codigo_id_animal!=999999999" . $wlocal);

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;
                $codigo_id_animal = $reg_mov->tbl_mov_estoque_codigo_id_animal;

                $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais 
                    WHERE tbl_animal_codigo_id='$codigo_id_animal'");

                $num_rows_animais = mysqli_num_rows($tbl_animais);
                $peso = 0;

                if ($num_rows_animais!=0) {
                    $reg_animal = mysqli_fetch_object($tbl_animais);

                    $primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
                    $peso_desmama = $reg_animal->tbl_animal_peso_desmama; 
                    $ultimo_peso = $reg_animal->tbl_animal_ultimo_peso; 

                    if ($ultimo_peso!=0 && $ultimo_peso!='') {
                        $peso = $ultimo_peso;
                    }
                    else if ($peso_desmama!=0 && $peso_desmama!='') {
                        $peso = $peso_desmama;
                    }
                    else if ($primeiro_peso!=0 && $primeiro_peso!=''){
                        $peso = $primeiro_peso;
                    }
                }

                if ($ent_sai=='E') {
                    if ($tipo=='N') {
                        $estoque_ent_nasc+=$peso;   
                    }
                }
            }
        }

        // Pega estoque atual do cadastro em peso 
        $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
            where tbl_animal_lixeira=0 AND 
                  tbl_animal_ativo='S'" . $wlocal_fazenda);

        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $peso = $reg_animal->tbl_animal_ultimo_peso;
            $estoque_final+=$peso;
        }

        /*$estoque_final = $estoque_final + $estoque_ent_nasc +
                         $estoque_ent_compra + $estoque_ent_transf + 
                         $estoque_ent_outra;

        $estoque_final = $estoque_final - $estoque_sai_morte - 
                         $estoque_sai_venda - $estoque_sai_transf -
                         $estoque_sai_outra;*/

        if ($estoque_final==0) {
            $estoque_final = $estoque_inicial;
        }

        $total_ent_nasc+= $estoque_ent_nasc;
        $total_ent_compra+= $estoque_ent_compra;
        $total_ent_transf+= $estoque_ent_transf;
        $total_ent_outra+= $estoque_ent_outra;
        $total_sai_morte+= $estoque_sai_morte;
        $total_sai_venda+= $estoque_sai_venda;
        $total_sai_transf+= $estoque_sai_transf;
        $total_sai_outra+= $estoque_sai_outra;

        $valor[1] = $estoque_final;
        $estoque_inicial = $estoque_final;
        $total_media_peso+=$estoque_final;

        if ($estoque_final!=0) {
            $qtd_mes_media++;
            $data_processamento = $ano_lista.'-'.$mes_lista.'-'.$ultimo_dia_mes;

            $p = (object)[
                'data' => $data_processamento,
                'valor' => intval($estoque_final),
            ];
            $arr[] = $p;
        }
    }

    $valor[2] = $valor[1]/30;

    $total_media_cabeca = $total_media_cabeca/$qtd_meses;

    if ($total_media_peso!=0 && $qtd_mes_media!=0) {
        $total_media_peso = $total_media_peso/$qtd_mes_media;
    }

    $valor[5] = $total_media_peso/30;

    $locacao_media = $total_media_cabeca/$total_ha;
    $locacao_media_peso = $total_media_peso/$total_ha;
    $locacao_media_ua = $locacao_media_peso/450;

    //$producao_arroba = ($estoque_inicial_primeiro_mes - $estoque_final)/30;

    $producao_kg = $estoque_final - 
                   $estoque_inicial_primeiro_mes +
                   $total_sai_venda -
                   $total_ent_compra + 
                   $total_sai_transf -
                   $total_ent_transf;

    $producao_arroba = $producao_kg/30;

    $valor[0] = number_format($valor[0],0,',','.');
    $valor[1] = number_format($valor[1],0,',','.');
    $valor[2] = number_format($valor[2],0,',','.');
    $valor[3] = number_format($total_media_cabeca,0,',','.');
    $valor[4] = number_format($total_media_peso,0,',','.');
    $valor[5] = number_format($valor[5],0,',','.');

    $valor[6] = number_format($locacao_media,2,',','.');
    $valor[7] = number_format($locacao_media_peso,0,',','.');
    $valor[8] = number_format($locacao_media_ua,2,',','.');
    $valor[9] = intval($total_media_cabeca);
    $valor[10] = intval($total_ha);
    $valor[11] = json_encode($arr, JSON_UNESCAPED_UNICODE);
    $valor[12] = number_format($producao_kg,2,',','.');
    $valor[13] = number_format($producao_arroba,2,',','.');

    // Calculo do GMD 
    if ($controle_estoque=='I') {
        $situacoes_animal = "''".','."'V'".','."'M'".','."'O'";

        $wsituacao = " AND tbl_animal_situacao IN(";
        $wsituacao.=$situacoes_animal;
        $wsituacao.= ")";

        /*$tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
                WHERE tbl_animal_lixeira=0 AND 
                      tbl_animal_ativo='S' AND tbl_animal_situacao=''" . 
                      $wlocal_fazenda . 
              " ORDER BY tbl_animal_codigo_fazenda, tbl_animal_codigo_numerico ASC");*/

        $tbl_animal = mysqli_query($conector, "SELECT * from tbl_animais 
                WHERE tbl_animal_lixeira=0 " . 
                      $wlocal_anterior . $wsituacao .
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
                else {
                    if ($reg_animal->tbl_animal_movimentacao_compra!='') {
                        $data_primeiro_peso = $reg_animal->tbl_animal_data_compra;

                        if ($data_primeiro_peso>=$data_inicial && $data_primeiro_peso<=$data_final) { 
                            $data_peso_nascimento = $data_primeiro_peso;
                            $peso_nascimento = $reg_animal->tbl_animal_ultimo_peso;
                        }
                    }
                }

                $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                $data_baixa = $reg_animal->tbl_animal_baixado_em;

                if ($data_baixa!='') {
                    $data_acompanhamento_calculo = date($data_baixa);
                }
                else {
                    $data_acompanhamento_calculo = $data_final;
                }

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

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $reg_cat_animal->tab_codigo_categoria_idade;
                        }
                    }
                }       

                $codigo_categoria_animal = 0;
                $array_cat = $_POST['array_cat'];

                for ($c=0; $c < $qtd_categoria; $c++) { 
                    if ($codigo_categoria==$array_cat[$c]) {
                        $codigo_categoria_animal = $codigo_categoria;
                    }
                }
                
                if ($data_peso_nascimento!=0) {
                    $data_peso_inicial = $data_peso_nascimento;
                    $peso_inicial = $peso_nascimento;
                }
                else {
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
                            if ($peso_inicial==9999) {
                                if ($peso<$peso_inicial && $peso!=0) {
                                    $data_peso_inicial=$data_peso;
                                    $peso_inicial = $peso;
                                }
                            }
                        }

                        /*if ($ano_mes_peso_inicial==$ano_mes_peso) {
                            if ($peso<$peso_inicial && $peso!=0) {
                                $data_peso_inicial=$data_peso;
                                $peso_inicial = $peso;
                            }
                        }*/

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
                    for ($c=0; $c < $qtd_categoria; $c++) { 
                        if ($sexo=="M" && $array_macho[$c]=='S') {
                            $array_gmd_macho_categoria[$codigo_categoria_animal]+=$gmd;
                            $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                        }
                        else if ($sexo=="F" && $array_femea[$c]=='S') {
                            $array_gmd_femea_categoria[$codigo_categoria_animal]+=$gmd;
                            $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                        }
                    }
                }
            }
        }

        for ($c=0; $c < $qtd_categoria; $c++) { 
            $codigo_categoria = $array_cat[$c];

            $j = str_pad($codigo_categoria, 3, "0", STR_PAD_LEFT);

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
        $valor[14] = $media_gmd_edi;
    }
    else {
        // GMD Sistema por Lote

        $data_sistema = date("Y-m-d");
        $partes = explode("-", $data_sistema);
        $ano_sistema = $partes[0];
        $mes_sistema = $partes[1];
        $dia_sistema = cal_days_in_month(CAL_GREGORIAN, $mes_sistema, $ano_sistema);

        $data_inicial = $_REQUEST['data_inicial'];
        $partes = explode("-", $data_inicial);
        $ano_inicial = $partes[0];
        $mes_inicial = $partes[1];
        $dia_inicial = cal_days_in_month(CAL_GREGORIAN, $mes_inicial, $ano_inicial);

        $data_final = $_REQUEST['data_final'];
        $partes = explode("-", $data_final);
        $ano_final = $partes[0];
        $mes_final = $partes[1];
        $dia_final = cal_days_in_month(CAL_GREGORIAN, $mes_final, $ano_final);

        $local_filtro = $_POST["local"];
        /*$categoria_filtro = $_POST["categoria"];
        $sexo_filtro = $_POST["sexo"];*/

        $wsexo_media='';
        $wsexo_fechamento='';

        /*if ($sexo_filtro!='Todos') {
            $wsexo_media = " AND tbl_pm_sexo IN(";
            $wsexo_media .= "'" . $sexo_filtro . "'";
            $wsexo_media.= ")";

            $wsexo_fechamento = " AND tbl_fechamento_sexo IN(";
            $wsexo_fechamento .= "'" . $sexo_filtro . "'";
            $wsexo_fechamento.= ")";
        }*/

        $arr[] = '';

        $array_cat = $_POST['array_cat'];
        $array_macho = $_POST['array_macho'];
        $array_femea = $_POST['array_femea'];
        $quantidade_categoria = count($array_cat);
        //$array_cat = implode(',', $array_cat);

        if ($array_cat=='') {
            $wcategoria = '';
        }
        else {
            //$wcategoria = " AND tab_codigo_categoria_idade IN(";
            $wcategoria= $array_cat;
            //$wcategoria.= ")";
        }

        /*$categoria= array();
        $matriz_itens = explode(",", $categoria_filtro);
        $quantidade_categoria = count($matriz_itens);

        for($i=0; $i < $quantidade_categoria; $i++) {
            $categoria[$i]=$matriz_itens[$i];
        }

        $categoria = implode(',', $categoria);
        $categoria = substr($categoria,0, -1);
        $quantidade_categoria--;

        $wcategoria = '';

        if ($categoria_filtro!='') {
            $wcategoria = explode(",", $categoria);
        }*/

        $data_inicial = $ano_inicial .'-'. $mes_inicial .'-'. $dia_inicial;
        $data_final = $ano_final .'-'. $mes_final .'-'. $dia_final;
        $data_sistema = $ano_sistema .'-'. $mes_sistema .'-'. $dia_sistema;

        // Cria data inicial e final para verificar se houve pesagem no periodo. Não considera a data inicial do periodo e sim o proximo mes do inicial

        $data_inicial_pesagem = date('Y-m-d', strtotime('+1 month', strtotime($ano_inicial .'-'. $mes_inicial .'-01')));

        if ($ano_final.$mes_final==$ano_sistema.$mes_sistema) {
            $data_final_pesagem = $data_sistema;
        }
        else {
            $data_final_pesagem = $data_final;
        }
        // Fim cria data inicial e final para pesagem

        $animais_listados=0;
        $gmd_total = 0;
        $numero_gmd = 0;

        $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

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

                $array_qtd_inicial_macho[$codigo_categoria] = 0;
                $array_qtd_final_macho[$codigo_categoria] = 0;
                $array_peso_medio_inicial_macho[$codigo_categoria] = 0;
                $array_peso_medio_final_macho[$codigo_categoria] = 0;
                $array_data_inicial_macho[$codigo_categoria] = '0000-00-00';
                $array_data_final_macho[$codigo_categoria] = '0000-00-00';
                $array_gmd_macho_categoria[$codigo_categoria] = 0;

                $array_qtd_inicial_femea[$codigo_categoria] = 0;
                $array_qtd_final_femea[$codigo_categoria] = 0;
                $array_peso_medio_inicial_femea[$codigo_categoria] = 0;
                $array_peso_medio_final_femea[$codigo_categoria] = 0;
                $array_data_inicial_femea[$codigo_categoria] = '0000-00-00';
                $array_data_final_femea[$codigo_categoria] = '0000-00-00';
                $array_gmd_femea_categoria[$codigo_categoria] = 0;
            }
        }   

        $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
            WHERE tbl_fechamento_data='$data_inicial' AND 
                  tbl_fechamento_local='$local_filtro'" . $wsexo_fechamento);

        $num_rows_fechamento = mysqli_num_rows($tbl_fechamento);  

        if ($num_rows_fechamento!=0) {
            while ($reg_fechamento = mysqli_fetch_object($tbl_fechamento)) {
                $data_peso = $reg_fechamento->tbl_fechamento_data;
                $sexo = $reg_fechamento->tbl_fechamento_sexo;
                $categoria = $reg_fechamento->tbl_fechamento_categoria;
                $qtd_animais = $reg_fechamento->tbl_fechamento_qtd;
                $peso = $reg_fechamento->tbl_fechamento_peso;

                $peso_medio=0;

                if ($qtd_animais!=0 && $peso!=0) {
                    $peso_medio=$peso/$qtd_animais;
                }

                if ($sexo=='M') {
                    $array_data_inicial_macho[$categoria]=$data_peso;
                    $array_peso_medio_inicial_macho[$categoria]+=$peso_medio;
                    $array_qtd_inicial_macho[$categoria]+=$qtd_animais;
                }
                else {
                    $array_data_inicial_femea[$categoria]=$data_peso;
                    $array_peso_medio_inicial_femea[$categoria]+=$peso_medio;
                    $array_qtd_inicial_femea[$categoria]+=$qtd_animais;
                }
            } // Fim while fechamento data inicial
        } // Fim if fechamento data inicial


        if ($ano_final.$mes_final==$ano_sistema.$mes_sistema) {

            // Gera dados finais pela tbl_peso_medio_categoria

            $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                WHERE tbl_pm_qtd_total_atual!=0 AND 
                      tbl_pm_local_id='$local_filtro'" . $wsexo_media);

            // ver where vazio

            $num_rows_media = mysqli_num_rows($tbl_media);  

            if ($num_rows_media!=0) {
                while ($reg_media = mysqli_fetch_object($tbl_media)) {
                    $data_peso = $data_sistema;
                    $local = $reg_media->tbl_pm_local_id;
                    $sexo = $reg_media->tbl_pm_sexo;
                    $categoria = $reg_media->tbl_pm_categoria_id;
                    $qtd_animais = $reg_media->tbl_pm_qtd_total_atual;
                    $peso_medio = $reg_media->tbl_pm_peso_medio_atual;

                    $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                        INNER JOIN tbl_pesagem
                                ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                        WHERE tbl_pesagem_finalizada='S' AND 
                              tbl_pesagem_codigo_local='$local' AND 
                              tbl_ite_pesagem_data_emissao>='$data_inicial_pesagem' AND 
                              tbl_ite_pesagem_data_emissao<='$data_final_pesagem' AND 
                              tbl_ite_pesagem_categoria='$categoria' AND 
                              tbl_ite_pesagem_sexo='$sexo'");

                    $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);    

                    if ($num_rows_pesagem!=0) {
                        if ($sexo=='M') {
                            $array_data_final_macho[$categoria]=$data_peso;
                            $array_peso_medio_final_macho[$categoria]+=$peso_medio;
                            $array_qtd_final_macho[$categoria]+=$qtd_animais;
                        }
                        else {
                            $array_data_final_femea[$categoria]=$data_peso;
                            $array_peso_medio_final_femea[$categoria]+=$peso_medio;
                            $array_qtd_final_femea[$categoria]+=$qtd_animais;
                        }
                    }

                } // Fim while fechamento data final
            } // Fim if fechamento data final
        }
        else {
            // Gera dados finais pela tabela tbl_fechamento_mensal_estoque
            $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
                WHERE tbl_fechamento_data='$data_final' AND 
                      tbl_fechamento_local='$local_filtro'" .$wsexo_fechamento);

            $num_rows_fechamento = mysqli_num_rows($tbl_fechamento);  

            if ($num_rows_fechamento!=0) {
                while ($reg_fechamento = mysqli_fetch_object($tbl_fechamento)) {
                    $data_peso = $reg_fechamento->tbl_fechamento_data;
                    $local = $reg_fechamento->tbl_fechamento_local;
                    $sexo = $reg_fechamento->tbl_fechamento_sexo;
                    $categoria = $reg_fechamento->tbl_fechamento_categoria;
                    $qtd_animais = $reg_fechamento->tbl_fechamento_qtd;
                    $peso = $reg_fechamento->tbl_fechamento_peso;

                    $peso_medio=0;

                    if ($qtd_animais!=0 && $peso!=0) {
                        $peso_medio=$peso/$qtd_animais;
                    }

                    $tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
                        INNER JOIN tbl_pesagem
                                ON tbl_pesagem_id  = tbl_ite_pesagem_numero_id 
                        WHERE tbl_pesagem_finalizada='S' AND 
                              tbl_pesagem_codigo_local='$local' AND 
                              tbl_ite_pesagem_data_emissao>='$data_inicial_pesagem' AND 
                              tbl_ite_pesagem_data_emissao<='$data_final_pesagem' AND 
                              tbl_ite_pesagem_categoria='$categoria' AND 
                              tbl_ite_pesagem_sexo='$sexo'");

                    $num_rows_pesagem = mysqli_num_rows($tbl_pesagem);    

                    if ($num_rows_pesagem!=0) {
                        if ($sexo=='M') {
                            $array_data_final_macho[$categoria]=$data_peso;
                            $array_peso_medio_final_macho[$categoria]+=$peso_medio;
                            $array_qtd_final_macho[$categoria]+=$qtd_animais;
                        }
                        else {
                            $array_data_final_femea[$categoria]=$data_peso;
                            $array_peso_medio_final_femea[$categoria]+=$peso_medio;
                            $array_qtd_final_femea[$categoria]+=$qtd_animais;
                        }
                    }
                } // Fim while fechamento data final
            } // Fim if fechamento data final
        }

        // Gera ganho de peso

        for ($i=1; $i <= 5; $i++) { 
            $categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

            // Macho
            $diferenca = strtotime($array_data_final_macho[$categoria]) - 
                         strtotime($array_data_inicial_macho[$categoria]);
            $dias = floor($diferenca / (60 * 60 * 24));             
                        
            if ($array_peso_medio_final_macho[$categoria]!=0 && 
                $array_peso_medio_inicial_macho[$categoria]!=0) {
                $ganho = $array_peso_medio_final_macho[$categoria] - 
                         $array_peso_medio_inicial_macho[$categoria];
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

            if ($gmd!=0) {
                $array_gmd_macho_categoria[$categoria]=$gmd;
            }

            // Fêmea
            $diferenca = strtotime($array_data_final_femea[$categoria]) - 
                         strtotime($array_data_inicial_femea[$categoria]);
            $dias = floor($diferenca / (60 * 60 * 24));             
                        
            if ($array_peso_medio_final_femea[$categoria]!=0 && 
                $array_peso_medio_inicial_femea[$categoria]!=0) {
                $ganho = $array_peso_medio_final_femea[$categoria] - 
                         $array_peso_medio_inicial_femea[$categoria];
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

            if ($gmd!=0) {
                $array_gmd_femea_categoria[$categoria]=$gmd;
            }
        }

        /*            for ($c=0; $c < $qtd_categoria; $c++) { 
                        if ($sexo=="M" && $array_macho[$c]=='S') {
                            $array_gmd_macho_categoria[$codigo_categoria_animal]+=$gmd;
                            $array_qtd_macho_categoria[$codigo_categoria_animal]++;
                        }
                        else if ($sexo=="F" && $array_femea[$c]=='S') {
                            $array_gmd_femea_categoria[$codigo_categoria_animal]+=$gmd;
                            $array_qtd_femea_categoria[$codigo_categoria_animal]++;
                        }
                    }*/

        for ($i=1; $i <= 5; $i++) { 
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);

            for ($k=0; $k < $quantidade_categoria; $k++) { 
                $value = $wcategoria[$k];

                if ($value==$j) {
                    if ($array_macho[$k]=='S') {
                        if ($array_qtd_inicial_macho[$j]!=0 && 
                            $array_qtd_final_macho[$j]!=0) {

                            $gmd_total+= ($array_qtd_final_macho[$j] * $array_gmd_macho_categoria[$j]);
                            $numero_gmd+= $array_qtd_final_macho[$j];
                        }
                    }

                    if ($array_femea[$k]=='S') {
                        if ($array_qtd_inicial_femea[$j]!=0 && 
                            $array_qtd_final_femea[$j]!=0) {

                            $gmd_total+= ($array_qtd_final_femea[$j] * $array_gmd_femea_categoria[$j]);
                            $numero_gmd+= $array_qtd_final_femea[$j];
                        }
                    }
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

        $valor[14] = $media_gmd_edi;
    }

    $str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . 
         $valor[3] . '<|>' . $valor[4] . '<|>' . $valor[5] . '<|>' . 
         $valor[6] . '<|>' . $valor[7] . '<|>' . $valor[8] . '<|>' . 
         $valor[9] . '<|>' . $valor[10] . '<|>' . $valor[11] . '<|>' . 
         $valor[12] . '<|>' . $valor[13] . '<|>' . $valor[14] . '<|>';

    echo $str; 

?>