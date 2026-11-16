<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    @ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];
    $cnpj_cliente = $_SESSION['id_cliente'];
    $controle_estoque = $_SESSION['controle_estoque'];

    $data_sistema = date("Y-m-d H:i:s");

    $fazendas = mysqli_query($conector, "SELECT * FROM tbl_pessoa
        WHERE tbl_pessoa_classe='4' AND 
              tbl_pessoa_lixeira=0");
    $num_rows_fazendas = mysqli_num_rows($fazendas);    

    if ($num_rows_fazendas!=0) {
        while ($reg_fazenda = mysqli_fetch_object($fazendas)) {
            $id_fazenda = $reg_fazenda->tbl_pessoa_id;

            if ($cnpj_cliente=='97174041604' || $cnpj_cliente=='71746307668' || $cnpj_cliente=='08472976670') {
                if ($id_fazenda!=78 && $id_fazenda!=79) {
                   $array_fazendas[] = $id_fazenda;
                }
            }
            else {
                $array_fazendas[] = $id_fazenda;
            }
        }
    }

    $quantidade_fazendas = count($array_fazendas);

    //echo $quantidade_fazendas . '</br>';

    $data_hoje = new DateTime();
    $dia_hoje=$data_hoje->format('d');

    /*if ($dia_hoje!=02) {
        exit;
    }*/

    $dias = '-' . $dia_hoje;

    $data_hoje = date("Y-m-d");
    //$data_fechamento = date('Y-m-d', strtotime('-2 days', strtotime($data_hoje)));
    $data_fechamento = date('Y-m-d', strtotime($dias . ' days', strtotime($data_hoje)));

    $ano_fechamento=substr($data_fechamento, 0, 4);
    $mes_fechamento=substr($data_fechamento, 5, 2);

    $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
        WHERE tbl_fechamento_data='$data_fechamento'");

    $num_rows_fec = mysqli_num_rows($tbl_fechamento);    

    if ($num_rows_fec!=0) {
        /*header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Já existe o fechamento mensal'));
        mysqli_close($conector);*/
        exit;
    }

    for ($i=0; $i<$quantidade_fazendas ; $i++) {  
        $local = $array_fazendas[$i]; 
        $estoque_final = 0;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");
        $num_rows_cat = mysqli_num_rows($categoria);    

        if ($num_rows_cat!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
                $qtd_femea[$id_categoria] = 0;
                $qtd_macho[$id_categoria] = 0;
                $peso_femea[$id_categoria] = 0;
                $peso_macho[$id_categoria] = 0;
            }
        }

        if ($controle_estoque=='I') {
            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_ativo='S' AND 
                      tbl_animal_codigo_fazenda='$local'");

            $num_rows = mysqli_num_rows($tbl_animais);

            if ($num_rows!=0) {
                while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                    $sexo = $reg_animal->tbl_animal_sexo;  
                    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
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
                    else {
                        $peso = 0;
                    }

                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                        WHERE tab_registro_lixeira_categoria_idade='0'");

                    $num_rows_cat = mysqli_num_rows($categoria);    
                    $codigo_categoria = 0;

                    if ($num_rows_cat!=0) {
                        while ($reg_categoria = mysqli_fetch_object($categoria)) {
                            $idade_de = $reg_categoria->tab_categoria_idade_de;
                            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                            if ($idade >= $idade_de && $idade <= $idade_ate) {
                                $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                            }
                        }                   
                    }

                    if ($sexo=='M') {
                        $qtd_macho[$codigo_categoria]++;
                        $peso_macho[$codigo_categoria]+=$peso;
                    }
                    else {
                        $qtd_femea[$codigo_categoria]++;
                        $peso_femea[$codigo_categoria]+=$peso;
                    }
                }
            }
        }
        else {
            for ($j=1; $j<=5 ; $j++) { 
                $codigo_categoria = str_pad($j , 3 , '0' , STR_PAD_LEFT);

                $media_categoria = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria 
                    WHERE tbl_pm_local_id='$local' AND 
                          tbl_pm_categoria_id ='$codigo_categoria' AND 
                          tbl_pm_sexo ='M'");

                $num_rows_media = mysqli_num_rows($media_categoria);
                $qtd_animais = 0;
                $peso = 0;

                if ($num_rows_media!=0) {
                    $reg_media = mysqli_fetch_object($media_categoria);
                    $peso = $reg_media->tbl_pm_peso_total_atual ;
                    $qtd_animais = $reg_media->tbl_pm_qtd_total_atual;
                }

                $qtd_macho[$codigo_categoria]=$qtd_animais;
                $peso_macho[$codigo_categoria]=$peso;

                $media_categoria = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria 
                    WHERE tbl_pm_local_id='$local' AND 
                          tbl_pm_categoria_id ='$codigo_categoria' AND 
                          tbl_pm_sexo ='F'");

                $num_rows_media = mysqli_num_rows($media_categoria);
                $peso = 0;
                $qtd_animais = 0;

                if ($num_rows_media!=0) {
                    $reg_media = mysqli_fetch_object($media_categoria);
                    $peso = $reg_media->tbl_pm_peso_total_atual ;
                    $qtd_animais = $reg_media->tbl_pm_qtd_total_atual;
                }

                $qtd_femea[$codigo_categoria]=$qtd_animais;
                $peso_femea[$codigo_categoria]=$peso;
            }
        }

        for ($j=1; $j<=5 ; $j++) { 
            $index = str_pad($j , 3 , '0' , STR_PAD_LEFT);

            $sql = "INSERT INTO tbl_fechamento_mensal_estoque (
                tbl_fechamento_local,
                tbl_fechamento_data,
                tbl_fechamento_categoria,
                tbl_fechamento_sexo,
                tbl_fechamento_qtd,
                tbl_fechamento_peso,
                tbl_fechamento_incluido_em,
                tbl_fechamento_incluido_por,
                tbl_fechamento_alterado_em,
                tbl_fechamento_alterado_por,
                tbl_fechmento_excluido_em,
                tbl_fechmento_excluido_por
            ) 
            VALUES (
                '$local',
                '$data_fechamento',
                '$index',
                'M',
                '$qtd_macho[$index]',
                '$peso_macho[$index]',
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                null,
                null
            )";

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado) {
                header('Content-type: application/json');
                echo json_encode(array('error' => true, 'message' => 'Erro na geração de machos: ' . $erro_mysql));
                mysqli_close($conector);
                exit;
            }

            $sql = "INSERT INTO tbl_fechamento_mensal_estoque (
                tbl_fechamento_local,
                tbl_fechamento_data,
                tbl_fechamento_categoria,
                tbl_fechamento_sexo,
                tbl_fechamento_qtd,
                tbl_fechamento_peso,
                tbl_fechamento_incluido_em,
                tbl_fechamento_incluido_por,
                tbl_fechamento_alterado_em,
                tbl_fechamento_alterado_por,
                tbl_fechmento_excluido_em,
                tbl_fechmento_excluido_por
            ) 
            VALUES (
                '$local',
                '$data_fechamento',
                '$index',
                'F',
                '$qtd_femea[$index]',
                '$peso_femea[$index]',
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                null,
                null
            )";

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado) {
                header('Content-type: application/json');
                echo json_encode(array('error' => true, 'message' => 'Erro na geração de fêmeas: ' . $erro_mysql));
                mysqli_close($conector);
                exit;
            }
        }

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

        // Pega estoque final do mes anterior
        $mes_ant = $mes_fechamento - 1;
        $ano_ant = $ano_fechamento;

        if ($mes_ant==0) {
            $mes_ant=12;
            $ano_ant=$ano_ant - 1;
        }

        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
            WHERE year(tbl_fechamento_data)='$ano_ant' AND 
                  month(tbl_fechamento_data)='$mes_ant' AND 
                  tbl_fechamento_local='$local'");

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $estoque_inicial+= $reg_mov->tbl_fechamento_peso_final;
            }
        }

        // Pega estoque sem nascimento
        $mov_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE year(tbl_mov_estoque_data_emissao)='$ano_fechamento' AND 
                  month(tbl_mov_estoque_data_emissao)='$mes_fechamento' AND 
                  tbl_mov_estoque_tipo_movimentacao!='N' AND
                  tbl_mov_estoque_tipo_movimentacao!='A' AND 
                  tbl_mov_estoque_tipo_movimentacao!='B' AND 
                  tbl_mov_estoque_local ='$local'");

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                if ($controle_estoque=='I') {
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
                }
                else {
                    // Para o controle de estoque por Lote
                    $peso = $reg_mov->tbl_mov_estoque_primeiro_peso;
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
            WHERE year(tbl_mov_estoque_nascimento)='$ano_fechamento' AND 
                  month(tbl_mov_estoque_nascimento)='$mes_fechamento' AND 
                  tbl_mov_estoque_tipo_movimentacao='N' AND 
                  tbl_mov_estoque_local ='$local'");

        $num_rows = mysqli_num_rows($mov_estoque);  

        if ($num_rows!=0) {
            while ($reg_mov = mysqli_fetch_object($mov_estoque)) {
                $ent_sai = $reg_mov->tbl_mov_estoque_entrada_saida;
                $tipo = $reg_mov->tbl_mov_estoque_tipo_movimentacao;

                if ($controle_estoque=='I') {
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
                }
                else {
                    // Para o controle de estoque por Lote
                    $peso = $reg_mov->tbl_mov_estoque_primeiro_peso;
                }

                if ($ent_sai=='E') {
                    if ($tipo=='N') {
                        $estoque_ent_nasc+=$peso;   
                    }
                }
            }
        }

        // Pega estoque atual do cadastro em peso 
        if ($controle_estoque=='I') {
            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
                where tbl_animal_lixeira=0 AND 
                      tbl_animal_ativo='S' AND
                      tbl_animal_codigo_fazenda='$local'");

            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
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
                $estoque_final+=$peso;
            }
        }
        else {
            $tbl_media= mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                where tbl_pm_local_id='$local'");

            while ($reg_media = mysqli_fetch_object($tbl_media)) {
                $peso = $reg_media->tbl_pm_peso_total_atual; 
                $estoque_final+=$peso;
            }
        }

        /*$estoque_final = $estoque_inicial + $estoque_ent_nasc +
                         $estoque_ent_compra + $estoque_ent_transf + 
                         $estoque_ent_outra;

        $estoque_final = $estoque_final - $estoque_sai_morte - 
                         $estoque_sai_venda - $estoque_sai_transf -
                         $estoque_sai_outra;*/

        if ($estoque_final==0) {
            $estoque_final = $estoque_inicial;
        }

        $sql = "INSERT INTO tbl_fechamento_mensal_estoque_ent_sai_peso (
            tbl_fechamento_local,
            tbl_fechamento_data,
            tbl_fechamento_peso_inicial,
            tbl_fechamento_peso_ent_nascimento,
            tbl_fechamento_peso_ent_compra,
            tbl_fechamento_peso_ent_transferencia,
            tbl_fechamento_peso_ent_outras,
            tbl_fechamento_peso_sai_morte,
            tbl_fechamento_peso_sai_venda,
            tbl_fechamento_peso_sai_transferencia,
            tbl_fechamento_peso_sai_outras,
            tbl_fechamento_peso_final,
            tbl_fechamento_peso_sem_movimentacao,
            tbl_fechamento_incluido_em,
            tbl_fechamento_incluido_por,
            tbl_fechamento_alterado_em,
            tbl_fechamento_alterado_por,
            tbl_fechmento_excluido_em,
            tbl_fechmento_excluido_por
            ) 
            VALUES (
            '$local',
            '$data_fechamento',
            '$estoque_inicial',
            '$estoque_ent_nasc',
            '$estoque_ent_compra',
            '$estoque_ent_transf',
            '$estoque_ent_outra',
            '$estoque_sai_morte',
            '$estoque_sai_venda',
            '$estoque_sai_transf',
            '$estoque_sai_outra',
            '$estoque_final',
            0,
            '$data_sistema',
            '$nomeusuario',
            null,
            null,
            null,
            null
        )";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Erro na geração do registro entrada/saida: ' . $erro_mysql));
            mysqli_close($conector);
            exit;
        }
    }

    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Fim do processamento'));

?>
