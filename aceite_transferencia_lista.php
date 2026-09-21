<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;
$controle_estoque = $_SESSION['controle_estoque'];

$grupo_registros = $_POST['grupo_registros'];
$matriz_contas = explode("<|>", $grupo_registros);
$quantidade_itens = count($matriz_contas);

include "conecta_mysql.inc";
			
for($i=0; $i < $quantidade_itens; $i++) {
	$numero_id = $matriz_contas[$i];

    $tbl_movimentacao = mysqli_query($conector, "SELECT * FROM tbl_movimentacao
                               WHERE tbl_movimentacao_id='$numero_id'");
                                    
    $reg_mov = mysqli_fetch_object($tbl_movimentacao);

    $codigo_origem = $reg_mov->tbl_movimentacao_codigo_local_origem;
    $codigo_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;
    $data_movimentacao = $reg_mov->tbl_movimentacao_data;

    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
            WHERE tbl_pasto_codigo_local='$codigo_destino' AND 
                  tbl_pasto_tipo_curral='E'");  
            
    $num_rows_pasto = mysqli_num_rows($tbl_pasto);

    if($num_rows_pasto !=0){
        $ln = mysqli_fetch_assoc($tbl_pasto);
        $codigo_pasto = $ln['tbl_pasto_id'];
    }
    else {
        $codigo_pasto = 0;
    }

    // Verifica a descricao do lote no pasto
    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_id ='$codigo_pasto'");

    $num_rows_pasto = mysqli_num_rows($tbl_pasto);  

    if ($num_rows_pasto!=0) {
        $reg_pasto = mysqli_fetch_object($tbl_pasto);
        $id_pasto = $reg_pasto->tbl_pasto_id ;
        $descricao_pasto = $reg_pasto->tbl_pasto_descricao;
        $descricao_lote = $reg_pasto->tbl_pasto_descricao_lote;
        $data_com_incluir = $reg_pasto->tbl_pasto_data_com_animais;
        $data_com_incluir_anterior = $reg_pasto->tbl_pasto_data_com_animais_anterior;
        $data_sem_incluir = $reg_pasto->tbl_pasto_data_sem_animais;
        $data_sem_incluir_anterior = $reg_pasto->tbl_pasto_data_sem_animais_anterior;

        if ($descricao_lote==null) {
            $descricao_lote = '';
        }
    }
    else {
        $id_pasto = '';
        $descricao_pasto = '';
        $descricao_lote = '';
    }

    if ($controle_estoque=='I') {
        $tbl_estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE tbl_mov_estoque_codigo_movimentacao ='$numero_id'");

        $num_rows_estoque = mysqli_num_rows($tbl_estoque);

        if ($num_rows_estoque!=0){
            while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
                $codigo_animal_id = $reg_estoque->tbl_mov_estoque_codigo_id_animal;
                $data_nascimento = $reg_estoque->tbl_mov_estoque_nascimento;
                $codigo_categoria = $reg_estoque->tbl_mov_estoque_codigo_categoria;
                $sexo = $reg_estoque->tbl_mov_estoque_sexo;

                $sql = "INSERT INTO tbl_movimentacao_estoque
                                    (tbl_mov_estoque_codigo_id_animal,
                                     tbl_mov_estoque_data_emissao,
                                     tbl_mov_estoque_nascimento,
                                     tbl_mov_estoque_local,
                                     tbl_mov_estoque_entrada_saida,
                                     tbl_mov_estoque_tipo_movimentacao,
                                     tbl_mov_estoque_local_origem,
                                     tbl_mov_estoque_local_destino,
                                     tbl_mov_estoque_codigo_movimentacao,
                                     tbl_mov_estoque_codigo_pasto,
                                     tbl_mov_estoque_codigo_categoria,
                                     tbl_mov_estoque_codigo_raca,
                                     tbl_mov_estoque_codigo_pelagem,
                                     tbl_mov_estoque_sexo
                                    ) 
                                    VALUES ('$codigo_animal_id',
                                            '$data_movimentacao',
                                            '$data_nascimento',
                                            '$codigo_destino',
                                            'E',
                                            'T',
                                            '$codigo_origem',
                                            '$codigo_destino',
                                            '$numero_id',
                                            '$codigo_pasto',
                                            '$codigo_categoria',
                                            null,
                                            null,
                                            '$sexo'
                                    )";
                            
                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico entrada transferência. ' . $erro_mysql));
                    mysqli_close($conector);
                    exit;
                }

                $rs = mysqli_query($conector, "SELECT * FROM tbl_animais
                    WHERE tbl_animal_codigo_id='$codigo_animal_id'");

                $num_rows = mysqli_num_rows($rs);
                if ($num_rows!=0) {
                    $reg_animal = mysqli_fetch_object($rs);
                    $codigo_origem_anterior = $reg_animal->tbl_animal_codigo_origem;
                    $codigo_fazenda_anterior = $reg_animal->tbl_animal_codigo_fazenda;

                    if ($codigo_origem_anterior=='' || $codigo_origem_anterior==0) {
                        $codigo_origem_anterior = $codigo_fazenda_anterior;
                    }
                }

                // em 19/08/2025 deixamos de atualizar a Origem (tbl_animal_codigo_origem)no Cadastro
                /*$sql = ("UPDATE tbl_animais SET tbl_animal_ativo='S',
                                                tbl_animal_situacao='',
                                                tbl_animal_codigo_fazenda='$codigo_destino',
                                                tbl_animal_codigo_origem='$codigo_origem',
                                                tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
                                                tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
                                                tbl_animal_baixado_em=null,
                                                tbl_animal_baixado_por=null
                    WHERE tbl_animal_codigo_id ='$codigo_animal_id'");*/


                $sql = ("UPDATE tbl_animais SET tbl_animal_ativo='S',
                                                tbl_animal_situacao='',
                                                tbl_animal_codigo_fazenda='$codigo_destino',
                                                tbl_animal_codigo_origem_anterior='$codigo_origem_anterior',
                                                tbl_animal_codigo_fazenda_anterior='$codigo_fazenda_anterior',
                                                tbl_animal_baixado_em=null,
                                                tbl_animal_baixado_por=null
                    WHERE tbl_animal_codigo_id ='$codigo_animal_id'");

                $resultado = mysqli_query($conector, $sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado) {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na atualização do animal. ' . $erro_mysql));
                    mysqli_close($conector);
                    exit;
                }

                $tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                    WHERE tbl_animal_pasto_local ='$codigo_destino' 
                        ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");

                $num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);    

                if ($num_rows_animal_pasto!=0) {
                    $reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto);
                    $numero_item =  $reg_animal_pasto->tbl_animal_pasto_numero_item;
                    $numero_item++;
                }
                else {
                    $numero_item = 1;
                }

                $sql = "INSERT INTO tbl_animal_pasto (
                            tbl_animal_pasto_local,
                            tbl_animal_pasto_id,
                            tbl_animal_pasto_numero_item,
                            tbl_animal_pasto_nascimento,
                            tbl_animal_pasto_sexo,
                            tbl_animal_pasto_categoria,
                            tbl_animal_pasto_raca,
                            tbl_animal_pasto_situacao,
                            tbl_animal_pasto_motivo_morte,
                            tbl_animal_pasto_observacao,
                            tbl_animal_pasto_incluido_em,
                            tbl_animal_pasto_incluido_por,
                            tbl_animal_pasto_baixado_em,
                            tbl_animal_pasto_baixado_por
                                ) 
                                VALUES (
                                        '$codigo_destino',
                                        '$codigo_pasto',
                                        '$numero_item',
                                        '$data_nascimento',
                                        '$sexo',
                                        '$codigo_categoria',
                                        null,
                                        'A',
                                        null,
                                        null,
                                        '$data_sistema',
                                        '$nomeusuario',
                                        null,
                                        null
                                )";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado) {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação do animal no pasto de entrada. ' . $erro_mysql));
                    mysqli_close($conector);
                    exit;
                }

                // AJUSTA DIAS COM ANIMAIS NO PASTO SE O PASTO ESTIVER VAZIO
                if ($descricao_lote=='') {
                    $dataAtual = new DateTime();
                    $dataCom = new DateTime($data_com_incluir);
                    $diff = $dataAtual->diff($dataCom);

                    if ($diff->h + ($diff->days * 24) < 24){
                        $query = "UPDATE tbl_pasto SET
                            tbl_pasto_alterado_em = '$data_sistema',
                            tbl_pasto_alterado_por = '$nomeusuario',
                            tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
                            tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
                            tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
                            tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
                            WHERE tbl_pasto_id = $codigo_pasto";

                        $resultado = mysqli_query($conector, $query);
                        $erro_mysql = mysqli_error($conector);

                        if (!$resultado){
                            header('Content-type: application/json');
                            echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                            exit;
                        } 
                    }
                    else {
                        $dataAtual = new DateTime();
                        $dataSem = new DateTime($data_sem_incluir);
                        $diff = $dataAtual->diff($dataSem);

                        if ($diff->h + ($diff->days * 24) < 24){
                            $query = "UPDATE tbl_pasto SET
                                tbl_pasto_alterado_em = '$data_sistema',
                                tbl_pasto_alterado_por = '$nomeusuario',
                                tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
                                tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
                                tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
                                tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
                                WHERE tbl_pasto_id = $codigo_pasto";

                            $resultado = mysqli_query($conector, $query);
                            $erro_mysql = mysqli_error($conector);

                            if (!$resultado){
                                header('Content-type: application/json');
                                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                                exit;
                            } 
                        }
                        else {
                            $query = "UPDATE tbl_pasto SET
                                tbl_pasto_alterado_em = '$data_sistema',
                                tbl_pasto_alterado_por = '$nomeusuario',
                                tbl_pasto_data_com_animais = '$data_sistema',
                                tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                                WHERE tbl_pasto_id = $codigo_pasto";

                            $resultado = mysqli_query($conector, $query);
                            $erro_mysql = mysqli_error($conector);

                            if (!$resultado){
                                header('Content-type: application/json');
                                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                                exit;
                            } 
                        }
                    }
                } // FIM AJUSTA DIAS COM ANIMAIS NO PASTO SE O PASTO ESTIVER VAZIO

                // ler item movimentacao para pegar o peso

                $rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
                    WHERE tbl_ite_movimentacao_numero_id='$numero_id' AND 
                    tbl_ite_movimentacao_codigo_id_animal='$codigo_animal_id'");

                $num_rows = mysqli_num_rows($rs);

                if ($num_rows!=0) {
                    $reg_ite = mysqli_fetch_object($rs);
                    $peso = $reg_ite->tbl_ite_movimentacao_peso;
                }
                else {
                    $peso=0;
                }

                // Soma categoria no fechamento mensal se a data for do mes anterior

                $data_hoje = date("Y-m-d");
                $partes_hoje = explode("-", $data_hoje);
                $anomes_inicial = $partes_hoje[0].$partes_hoje[1];

                $partes_movimentacao = explode("-", $data_movimentacao);
                $anomes_final = $partes_movimentacao[0].$partes_movimentacao[1];
                $diferenca = $anomes_inicial - $anomes_final;

                if ($diferenca!=0) {
                    $date = new DateTime($data_movimentacao);
                    $date->modify('last day of this month');
                    $data_fechamento = $date->format('Y-m-d');

                    $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
                        WHERE tbl_fechamento_local='$codigo_destino' AND
                              tbl_fechamento_data='$data_fechamento' AND 
                              tbl_fechamento_categoria='$codigo_categoria' AND 
                              tbl_fechamento_sexo='$sexo'");

                    $num_rows = mysqli_num_rows($tbl_fechamento);    

                    if ($num_rows!=0) {
                        $reg = mysqli_fetch_object($tbl_fechamento);
                        $fechamento_id = $reg->tbl_fechamento_id;
                        $qtd_fechamento = $reg->tbl_fechamento_qtd;
                        $peso_fechamento = $reg->tbl_fechamento_peso;

                        $qtd_fechamento++;
                        $peso_fechamento+=$peso;

                        $sql = ("UPDATE tbl_fechamento_mensal_estoque SET 
                                tbl_fechamento_qtd='$qtd_fechamento',
                                tbl_fechamento_peso='$peso_fechamento'
                            WHERE tbl_fechamento_id ='$fechamento_id'");

                        $resultado = mysqli_query($conector,$sql);
                        $erro_mysql = mysqli_error($conector);

                        if (!$resultado) {
                            header('Content-type: application/json');
                            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal' . $erro_mysql));
                            mysqli_close($conector);
                            exit;
                        }
                    }

                    $tbl_fechamento_ent_sai = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque_ent_sai_peso
                        WHERE tbl_fechamento_local='$codigo_destino' AND
                              tbl_fechamento_data='$data_fechamento'");

                    $num_rows = mysqli_num_rows($tbl_fechamento_ent_sai);    

                    if ($num_rows!=0) {
                        $reg = mysqli_fetch_object($tbl_fechamento_ent_sai);
                        $fechamento_id = $reg->tbl_fechamento_id;
                        $peso_tranferencia = $reg->tbl_fechamento_peso_ent_transferencia;
                        $peso_final = $reg->tbl_fechamento_peso_final;

                        $peso_tranferencia+=$peso;
                        $peso_final+=$peso;

                        $sql = ("UPDATE tbl_fechamento_mensal_estoque_ent_sai_peso SET 
                                tbl_fechamento_peso_ent_transferencia='$peso_tranferencia',
                                tbl_fechamento_peso_final='$peso_final'
                                WHERE tbl_fechamento_id ='$fechamento_id'");

                        $resultado = mysqli_query($conector,$sql);
                        $erro_mysql = mysqli_error($conector);

                        if (!$resultado) {
                            header('Content-type: application/json');
                            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal Ent/Sai' . $erro_mysql));
                            mysqli_close($conector);
                            exit;
                        }
                    }
                }
                
                // Se o animal estiver na lista de monta da fazenda origem, transfere para a fazenda destino conforme Trello 
                // Cartão: MOVIMENTAÇÕES / REPRODUÇÃO
                // Cheklist: VENDA/TRANSFERENCIA

                $item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
                    INNER JOIN tbl_cobertura 
                            ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                    WHERE tbl_cobertura_lixeira=0 AND 
                          tbl_cobertura_controle='M' AND
                          tbl_ite_cobertura_codigo_id_animal='$codigo_animal_id' AND 
                          tbl_cobertura_codigo_local='$codigo_origem'
                    ORDER BY tbl_cobertura_id DESC LIMIT 1");

                $num_rows = mysqli_num_rows($item_cobertura);
                
                if ($num_rows!=0) {
                    $reg_item = mysqli_fetch_object($item_cobertura);
                    $cobertura_id = $reg_item->tbl_ite_cobertura_numero_id;

                    $sql = "UPDATE tbl_cobertura SET
                        tbl_cobertura_codigo_local='$codigo_destino',
                        tbl_cobertura_alterado_em='$data_sistema',
                        tbl_cobertura_alterado_por='$nomeusuario'
                        WHERE tbl_cobertura_id ='$cobertura_id'";

                    $resultado = mysqli_query($conector,$sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado) {
                        header('Content-type: application/json');
                        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na transferência da lista de monta ' . $erro_mysql));
                        mysqli_close($conector);
                        exit;
                    }
                }
                // Fim adiciona fechamento mensal
            }
        }
    }
    else {
        for ($i = 1; $i <=5; $i++) {
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            $qtd_animais_macho[$j] = 0;
            $peso_animais_macho[$j] = 0;
            $qtd_animais_femea[$j] = 0;
            $peso_animais_femea[$j] = 0;
        }

        $tbl_estoque = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            WHERE tbl_mov_estoque_codigo_movimentacao ='$numero_id'");

        $num_rows_estoque = mysqli_num_rows($tbl_estoque);

        if ($num_rows_estoque!=0){
            while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
                $data_nascimento = $reg_estoque->tbl_mov_estoque_nascimento;
                $codigo_categoria = $reg_estoque->tbl_mov_estoque_codigo_categoria;
                $sexo = $reg_estoque->tbl_mov_estoque_sexo;
                $peso = $reg_estoque->tbl_mov_estoque_primeiro_peso;

                $sql = "INSERT INTO tbl_movimentacao_estoque
                            (tbl_mov_estoque_codigo_id_animal,
                             tbl_mov_estoque_data_emissao,
                             tbl_mov_estoque_nascimento,
                             tbl_mov_estoque_local,
                             tbl_mov_estoque_entrada_saida,
                             tbl_mov_estoque_tipo_movimentacao,
                             tbl_mov_estoque_local_origem,
                             tbl_mov_estoque_local_destino,
                             tbl_mov_estoque_codigo_movimentacao,
                             tbl_mov_estoque_codigo_pasto,
                             tbl_mov_estoque_codigo_categoria,
                             tbl_mov_estoque_codigo_raca,
                             tbl_mov_estoque_codigo_pelagem,
                             tbl_mov_estoque_sexo,
                             tbl_mov_estoque_primeiro_peso
                            ) 
                            VALUES (0,
                                    '$data_movimentacao',
                                    '$data_nascimento',
                                    '$codigo_destino',
                                    'E',
                                    'T',
                                    '$codigo_origem',
                                    '$codigo_destino',
                                    '$numero_id',
                                    '$codigo_pasto',
                                    '$codigo_categoria',
                                    null,
                                    null,
                                    '$sexo',
                                    '$peso'
                            )";
                    
                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Erro na gravacao histórico entrada transferência. ' . $erro_mysql));
                    mysqli_close($conector);
                    exit;
                }

                if ($sexo=='M') {
                    $qtd_animais_macho[$codigo_categoria]++;
                    $peso_animais_macho[$codigo_categoria]+=$peso;
                }
                else {
                    $qtd_animais_femea[$codigo_categoria]++;
                    $peso_animais_femea[$codigo_categoria]+=$peso;
                }

                $tbl_animal_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                    WHERE tbl_animal_pasto_local ='$codigo_destino' 
                    ORDER BY tbl_animal_pasto_numero_item DESC LIMIT 1");

                $num_rows_animal_pasto = mysqli_num_rows($tbl_animal_pasto);    

                if ($num_rows_animal_pasto!=0) {
                    $reg_animal_pasto = mysqli_fetch_object($tbl_animal_pasto);
                    $numero_item =  $reg_animal_pasto->tbl_animal_pasto_numero_item;
                    $numero_item++;
                }
                else {
                    $numero_item = 1;
                }

                $sql = "INSERT INTO tbl_animal_pasto (
                    tbl_animal_pasto_local,
                    tbl_animal_pasto_id,
                    tbl_animal_pasto_numero_item,
                    tbl_animal_pasto_nascimento,
                    tbl_animal_pasto_sexo,
                    tbl_animal_pasto_categoria,
                    tbl_animal_pasto_raca,
                    tbl_animal_pasto_situacao,
                    tbl_animal_pasto_motivo_morte,
                    tbl_animal_pasto_observacao,
                    tbl_animal_pasto_incluido_em,
                    tbl_animal_pasto_incluido_por,
                    tbl_animal_pasto_baixado_em,
                    tbl_animal_pasto_baixado_por
                        ) 
                        VALUES (
                                '$codigo_destino',
                                '$codigo_pasto',
                                '$numero_item',
                                '$data_nascimento',
                                '$sexo',
                                '$codigo_categoria',
                                null,
                                'A',
                                null,
                                null,
                                '$data_sistema',
                                '$nomeusuario',
                                null,
                                null
                        )";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado) {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação do animal no pasto de entrada. ' . $erro_mysql));
                    mysqli_close($conector);
                    exit;
                }

                // AJUSTA DIAS COM ANIMAIS NO PASTO SE O PASTO ESTIVER VAZIO
                if ($descricao_lote=='') {
                    $dataAtual = new DateTime();
                    $dataCom = new DateTime($data_com_incluir);
                    $diff = $dataAtual->diff($dataCom);

                    if ($diff->h + ($diff->days * 24) < 24){
                        $query = "UPDATE tbl_pasto SET
                            tbl_pasto_alterado_em = '$data_sistema',
                            tbl_pasto_alterado_por = '$nomeusuario',
                            tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
                            tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
                            tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
                            tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
                            WHERE tbl_pasto_id = $codigo_pasto";

                        $resultado = mysqli_query($conector, $query);
                        $erro_mysql = mysqli_error($conector);

                        if (!$resultado){
                            header('Content-type: application/json');
                            echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                            exit;
                        } 
                    }
                    else {
                        $dataAtual = new DateTime();
                        $dataSem = new DateTime($data_sem_incluir);
                        $diff = $dataAtual->diff($dataSem);

                        if ($diff->h + ($diff->days * 24) < 24){
                            $query = "UPDATE tbl_pasto SET
                                tbl_pasto_alterado_em = '$data_sistema',
                                tbl_pasto_alterado_por = '$nomeusuario',
                                tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
                                tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
                                tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
                                tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
                                WHERE tbl_pasto_id = $codigo_pasto";

                            $resultado = mysqli_query($conector, $query);
                            $erro_mysql = mysqli_error($conector);

                            if (!$resultado){
                                header('Content-type: application/json');
                                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                                exit;
                            } 
                        }
                        else {
                            $query = "UPDATE tbl_pasto SET
                                tbl_pasto_alterado_em = '$data_sistema',
                                tbl_pasto_alterado_por = '$nomeusuario',
                                tbl_pasto_data_com_animais = '$data_sistema',
                                tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                                WHERE tbl_pasto_id = $codigo_pasto";

                            $resultado = mysqli_query($conector, $query);
                            $erro_mysql = mysqli_error($conector);

                            if (!$resultado){
                                header('Content-type: application/json');
                                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                                exit;
                            } 
                        }
                    }
                } // FIM AJUSTA DIAS COM ANIMAIS NO PASTO SE O PASTO ESTIVER VAZIO

                // Soma categoria no fechamento mensal se a data for do mes anterior

                $data_hoje = date("Y-m-d");
                $partes_hoje = explode("-", $data_hoje);
                $anomes_inicial = $partes_hoje[0].$partes_hoje[1];

                $partes_movimentacao = explode("-", $data_movimentacao);
                $anomes_final = $partes_movimentacao[0].$partes_movimentacao[1];
                $diferenca = $anomes_inicial - $anomes_final;

                if ($diferenca!=0) {
                    $date = new DateTime($data_movimentacao);
                    $date->modify('last day of this month');
                    $data_fechamento = $date->format('Y-m-d');

                    $tbl_fechamento = mysqli_query($conector, "SELECT * FROM tbl_fechamento_mensal_estoque
                        WHERE tbl_fechamento_local='$codigo_destino' AND
                              tbl_fechamento_data='$data_fechamento' AND 
                              tbl_fechamento_categoria='$codigo_categoria' AND 
                              tbl_fechamento_sexo='$sexo'");

                    $num_rows = mysqli_num_rows($tbl_fechamento);    

                    if ($num_rows!=0) {
                        $reg = mysqli_fetch_object($tbl_fechamento);
                        $fechamento_id = $reg->tbl_fechamento_id;
                        $qtd_fechamento = $reg->tbl_fechamento_qtd;

                        $qtd_fechamento++;

                        $sql = ("UPDATE tbl_fechamento_mensal_estoque SET 
                                tbl_fechamento_qtd='$qtd_fechamento'
                            WHERE tbl_fechamento_id ='$fechamento_id'");

                        $resultado = mysqli_query($conector,$sql);
                        $erro_mysql = mysqli_error($conector);

                        if (!$resultado) {
                            header('Content-type: application/json');
                            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na alteração do fechamento mensal' . $erro_mysql));
                            mysqli_close($conector);
                            exit;
                        }
                    }
                }
                // Fim adiciona fechamento mensal
            }
        }

        // Ajusta ou inclui a media de peso por categoria
        for ($i=1; $i <=5 ; $i++) { 
            $categoria = str_pad($i, 3, "0", STR_PAD_LEFT);

            if ($peso_animais_macho[$categoria] !=0 && 
                $qtd_animais_macho[$categoria] !=0) {

                // Pega ultima quantidade de animais e ultimo peso total
                $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                    WHERE tbl_pm_local_id='$codigo_destino' AND 
                          tbl_pm_categoria_id='$categoria' AND 
                          tbl_pm_sexo='M'");

                $num_rows_media = mysqli_num_rows($tbl_media);

                if ($num_rows_media!=0){
                    $reg_media = mysqli_fetch_object($tbl_media);
                    $id_media = $reg_media->tbl_pm_id;
                    $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
                    $peso_anterior = $reg_media->tbl_pm_peso_total_atual;
                }
                else {
                    $qtd_anterior=0;
                    $peso_anterior=0;
                }
                // Fim ultima quantidade de animais e ultimo peso total 
                
                // Calcula a media atual e grava no banco de dados
                $peso_medio_atual = ($peso_animais_macho[$categoria] + 
                                     $peso_anterior) /
                                    ($qtd_animais_macho[$categoria] + 
                                     $qtd_anterior);

                $qtd_animais_atual = $qtd_animais_macho[$categoria] + $qtd_anterior;
                $peso_total_atual = $peso_animais_macho[$categoria] + $peso_anterior;

                if ($num_rows_media==0) {
                    $sql = "INSERT INTO tbl_peso_medio_categoria (
                        tbl_pm_categoria_id,
                        tbl_pm_sexo,
                        tbl_pm_local_id,
                        tbl_pm_data,
                        tbl_pm_qtd_total_atual,
                        tbl_pm_peso_medio_atual,
                        tbl_pm_peso_total_atual
                        ) VALUES (
                        '$categoria',
                        'M',
                        '$codigo_destino',
                        '$data_movimentacao',
                        '$qtd_animais_atual',
                        '$peso_medio_atual',
                        '$peso_total_atual'
                    )";
                }
                else {
                   $sql = ("UPDATE tbl_peso_medio_categoria  SET 
                        tbl_pm_qtd_total_atual='$qtd_animais_atual',
                        tbl_pm_peso_medio_atual='$peso_medio_atual',
                        tbl_pm_peso_total_atual='$peso_total_atual'
                        WHERE tbl_pm_id ='$id_media'");
                }

                $resultado = mysqli_query($conector,$sql);
            }

            if ($peso_animais_femea[$categoria] !=0 && 
                $qtd_animais_femea[$categoria] !=0) {

                // Pega ultima quantidade de animais e ultimo peso total
                $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                    WHERE tbl_pm_local_id='$codigo_destino' AND 
                          tbl_pm_categoria_id='$categoria' AND 
                          tbl_pm_sexo='F'");

                $num_rows_media = mysqli_num_rows($tbl_media);

                if ($num_rows_media!=0){
                    $reg_media = mysqli_fetch_object($tbl_media);
                    $id_media = $reg_media->tbl_pm_id;
                    $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
                    $peso_anterior = $reg_media->tbl_pm_peso_total_atual;
                }
                else {
                    $qtd_anterior=0;
                    $peso_anterior=0;
                }
                // Fim ultima quantidade de animais e ultimo peso total 
                
                // Calcula a media atual e grava no banco de dados
                $peso_medio_atual = ($peso_animais_femea[$categoria] + 
                                     $peso_anterior) /
                                    ($qtd_animais_femea[$categoria] + $qtd_anterior);

                $qtd_animais_atual = $qtd_animais_femea[$categoria] + $qtd_anterior;
                $peso_total_atual = $peso_animais_femea[$categoria] + $peso_anterior;

                if ($num_rows_media==0) {
                    $sql = "INSERT INTO tbl_peso_medio_categoria (
                        tbl_pm_categoria_id,
                        tbl_pm_sexo,
                        tbl_pm_local_id,
                        tbl_pm_data,
                        tbl_pm_qtd_total_atual,
                        tbl_pm_peso_medio_atual,
                        tbl_pm_peso_total_atual
                        ) VALUES (
                        '$categoria',
                        'F',
                        '$codigo_destino',
                        '$data_movimentacao',
                        '$qtd_animais_atual',
                        '$peso_medio_atual',
                        '$peso_total_atual'
                    )";
                }
                else {
                   $sql = ("UPDATE tbl_peso_medio_categoria  SET 
                        tbl_pm_qtd_total_atual='$qtd_animais_atual',
                        tbl_pm_peso_medio_atual='$peso_medio_atual',
                        tbl_pm_peso_total_atual='$peso_total_atual'
                        WHERE tbl_pm_id ='$id_media'");
                }

                $resultado = mysqli_query($conector,$sql);
            }
        }
    }

    $sql = ("UPDATE tbl_movimentacao SET tbl_movimentacao_situacao='S',
                        tbl_movimentacao_aceite_transferencia_em='$data_sistema',
                        tbl_movimentacao_aceite_transferencia_por='$nomeusuario'
                        WHERE tbl_movimentacao_id ='$numero_id'");
    $resultado = mysqli_query($conector, $sql);
    if (!$resultado) {
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Erro na alteração da situação da transferência. ' . $erro_mysql));
        mysqli_close($conector);
        exit;
    }
}

header('Content-type: application/json');
echo json_encode(array('success' => true, 'message' => 'Aceite efetuado com sucesso.', 'id_pasto' => $id_pasto, 'descricao_pasto' => $descricao_pasto, 'descricao_lote' => $descricao_lote));
mysqli_close($conector);
exit;


?>