<?php 
    include "conecta_mysql.inc";
    $data_sistema = date("Y-m-d H:i:s");
    $local_processamento = 77;

    /*$tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id 
            WHERE tbl_cobertura_id=319 AND 
                  tbl_cobertura_controle='C' AND 
                  tbl_cobertura_protocoloiatf = 999");*/

    $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
        INNER JOIN tbl_cobertura
                ON tbl_cobertura_id = tbl_ite_cobertura_numero_id 
        INNER JOIN tbl_parametro_estacao_monta 
                ON tbl_par_estacao_id  = tbl_cobertura_codigo_estacao_monta
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_cobertura_controle='C' AND 
                  tbl_cobertura_protocoloiatf = 999");

//tbl_cobertura_codigo_local='$local_processamento' AND
    $num_rows = mysqli_num_rows($tbl_item_cobertura);   

    if ($num_rows!=0) {
        while ($reg_cobertura = mysqli_fetch_object($tbl_item_cobertura)){
            $cobertura_id = $reg_cobertura->tbl_cobertura_id;
            $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
            $estacao_id = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $local = $reg_cobertura->tbl_cobertura_codigo_local;
            $data = $reg_cobertura->tbl_cobertura_data;
            $filtros = $reg_cobertura->tbl_cobertura_filtros;
            $paridas = $reg_cobertura->tbl_cobertura_filtro_vacas_paridas;
            $data_paridas = $reg_cobertura->tbl_cobertura_filtro_data_paridas;
            if ($data_paridas=='') {
                $data_paridas = null;
            }
            $solterias = $reg_cobertura->tbl_cobertura_filtro_vacas_solteiras;
            $novilhas = $reg_cobertura->tbl_cobertura_filtro_novilhas;
            $idade_de = $reg_cobertura->tbl_cobertura_filtro_idade_de;
            $idade_ate = $reg_cobertura->tbl_cobertura_filtro_idade_ate;
            $peso_acima = $reg_cobertura->tbl_cobertura_filtro_peso_acima;
            $incluido_em = $reg_cobertura->tbl_cobertura_incluido_em;
            $incluido_por = $reg_cobertura->tbl_cobertura_incluido_por;
            $id_animal = $reg_cobertura->tbl_ite_cobertura_codigo_id_animal;
            $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;
            $data_diagnostico = $reg_cobertura->tbl_ite_cobertura_data_diagnostico;
            $nascido = $reg_cobertura->tbl_ite_cobertura_nascido;
            $nascido_outro = $reg_cobertura->tbl_ite_cobertura_situacao_femea_nascido_outro;

            if ($diagnostico=='N') {
                $data_diagnostico_negativo=$reg_cobertura->tbl_ite_cobertura_data_emissao;
            }
            else {
                $data_diagnostico_negativo='';
            }

            $tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_codigo_id= '$id_animal'");

            $reg_animal = mysqli_fetch_object($tbl_animal); 

            $codigo_alfa_consulta = $reg_animal->tbl_animal_codigo_alfa;
            $codigo_numerico_consulta = $reg_animal->tbl_animal_codigo_numerico;

            if ($codigo_alfa_consulta==''){
                $codigo_edi = $codigo_numerico_consulta; 
            }
            else {
                $codigo_edi = $codigo_alfa_consulta.'-'.$codigo_numerico_consulta; 
            }

            //if ($diagnostico!='P') {
            echo 'Cobertura atual: '. $cobertura_id .' Animal: ' . $codigo_edi .' Estação: ' . $reg_cobertura->tbl_par_estacao_nome;
            echo 'Diagnostico: ' . $diagnostico . '</br>';
            //}

            $controle = 'M';
            $grupo = null;
            $estacao = null;
            $protocolo = null;
            $qtd_animais = 1;
            $incluido_por = 'George Nova Monta';
            $alterado_em = null;
            $alterado_por = null;
            $lixeira = 0;
            $lixeira_em = null;
            $lixeira_por = null;
            $cobertura_encerrada = 'S';


            $sql = "INSERT INTO tbl_cobertura (
                    tbl_cobertura_controle,
                    tbl_cobertura_data,
                    tbl_cobertura_codigo_local,
                    tbl_cobertura_codigo_grupo,
                    tbl_cobertura_codigo_estacao_monta,
                    tbl_cobertura_protocoloiatf,
                    tbl_cobertura_qtd_animais,
                    tbl_cobertura_filtros,
                    tbl_cobertura_incluido_em,
                    tbl_cobertura_incluido_por,
                    tbl_cobertura_alterado_em,
                    tbl_cobertura_alterado_por,
                    tbl_cobertura_lixeira,
                    tbl_cobertura_lixeira_em,
                    tbl_cobertura_lixeira_por,
                    tbl_cobertura_filtro_vacas_paridas,
                    tbl_cobertura_filtro_data_paridas,
                    tbl_cobertura_filtro_vacas_solteiras,
                    tbl_cobertura_filtro_novilhas,
                    tbl_cobertura_filtro_idade_de,
                    tbl_cobertura_filtro_idade_ate,
                    tbl_cobertura_filtro_peso_acima,
                    tbl_cobertura_encerrada
                    ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conector->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssssssssssssssssssssss", 
                    $controle,
                    $data,
                    $local,
                    $grupo,
                    $estacao,
                    $protocolo,
                    $qtd_animais,
                    $filtros,
                    $incluido_em,
                    $incluido_por,
                    $alterado_em,
                    $alterado_por,
                    $lixeira,
                    $lixeira_em,
                    $lixeira_por,
                    $paridas,
                    $data_paridas,
                    $solterias,
                    $novilhas,
                    $idade_de,
                    $idade_ate,
                    $peso_acima,
                    $cobertura_encerrada);

                if ($stmt->execute()) {
                    echo 'Cobertura gravada: ';

                } else {
                    echo "Erro ao inserir registro nova cobertura: " . $stmt->error;
                    $stmt->close();
                    exit;
                }

                // Fecha a declaração preparada
                $stmt->close();
            } else {
                echo "Erro na preparação da consulta stm: " . $conector->error;
                exit;
            }


                $numero_cobertura = mysqli_insert_id($conector);
                $numero_cobertura = str_pad($numero_cobertura, 9, "0", STR_PAD_LEFT);

                echo $numero_cobertura . '</br>';
                         
                $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                    WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                $num_protocolo = mysqli_num_rows($sql);  

                //echo ' Numrows: ' . $protocolo_id . '</br>';

                if ($num_protocolo!=0) {
                    $reg_protocolo_cobertura = mysqli_fetch_object($sql);
                }

                $sql =  mysqli_query($conector,"SELECT * FROM tbl_item_protocoloiatf 
                    WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                          tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                    ORDER BY tbl_ite_protocoloiatf_id ASC");

                    $dias_previsao_parto = 282;

                    while($reg_itens_iatf = mysqli_fetch_object($sql)){
                        $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                        $data_prenhes = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                        $data_previsao_parto = date("Y-m-d", strtotime($data_prenhes . "+{$dias_previsao_parto} days"));

                        //echo ' Data Protocolo: ' . $reg_protocolo_cobertura->tbl_protocolo_cobertura_data . ' Dias: ' . $dias . ' Prenhes: ' . $data_prenhes . ' Previsao: ' . $data_previsao_parto;
                    }
    
                if ($nascido!='') {
                    if ($diagnostico=='N') {
                        $sql = "INSERT INTO tbl_item_cobertura (
                            tbl_ite_cobertura_numero_id,
                            tbl_ite_cobertura_numero_item,
                            tbl_ite_cobertura_codigo_id_animal,
                            tbl_ite_cobertura_codigo_animal,
                            tbl_ite_cobertura_codigo_alfa,
                            tbl_ite_cobertura_codigo_numerico,
                            tbl_ite_cobertura_data_emissao,
                            tbl_ite_cobertura_codigo_touro_semen,
                            tbl_ite_cobertura_lote_semen,
                            tbl_ite_cobertura_nome_inseminador,
                            tbl_ite_cobertura_destino,
                            tbl_ite_cobertura_dia_1,
                            tbl_ite_cobertura_dia_2,
                            tbl_ite_cobertura_dia_3,
                            tbl_ite_cobertura_dia_4,
                            tbl_ite_cobertura_dia_5,
                            tbl_ite_cobertura_dia_6,
                            tbl_ite_cobertura_observacao,
                            tbl_ite_cobertura_numero_cobertura,
                            tbl_ite_cobertura_data_prenhes,
                            tbl_ite_cobertura_previsao_parto,
                            tbl_ite_cobertura_resultado_diagnostico,
                            tbl_ite_cobertura_data_diagnostico,
                            tbl_ite_cobertura_qtd_diagnosticos_positivo,
                            tbl_ite_cobertura_positivo_alterado_em,
                            tbl_ite_cobertura_positivo_alterado_por,
                            tbl_ite_cobertura_nascido,
                            tbl_ite_cobertura_situacao_femea_nascido_outro,
                            tbl_ite_cobertura_negativo_em
                            )
                            VALUES ('$numero_cobertura', 
                                1,
                                '$id_animal',
                                '$codigo_edi',
                                '$codigo_alfa_consulta',
                                '$codigo_numerico_consulta',
                                '$incluido_em',
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                0,
                                '$data_prenhes',
                                '$data_previsao_parto',
                                '$diagnostico',
                                '$data_diagnostico',
                                1,
                                null,
                                null,
                                '$nascido',
                                '$nascido_outro',
                                '$data_diagnostico_negativo'
                            )";

                    }
                    else {
                        $sql = "INSERT INTO tbl_item_cobertura (
                            tbl_ite_cobertura_numero_id,
                            tbl_ite_cobertura_numero_item,
                            tbl_ite_cobertura_codigo_id_animal,
                            tbl_ite_cobertura_codigo_animal,
                            tbl_ite_cobertura_codigo_alfa,
                            tbl_ite_cobertura_codigo_numerico,
                            tbl_ite_cobertura_data_emissao,
                            tbl_ite_cobertura_codigo_touro_semen,
                            tbl_ite_cobertura_lote_semen,
                            tbl_ite_cobertura_nome_inseminador,
                            tbl_ite_cobertura_destino,
                            tbl_ite_cobertura_dia_1,
                            tbl_ite_cobertura_dia_2,
                            tbl_ite_cobertura_dia_3,
                            tbl_ite_cobertura_dia_4,
                            tbl_ite_cobertura_dia_5,
                            tbl_ite_cobertura_dia_6,
                            tbl_ite_cobertura_observacao,
                            tbl_ite_cobertura_numero_cobertura,
                            tbl_ite_cobertura_data_prenhes,
                            tbl_ite_cobertura_previsao_parto,
                            tbl_ite_cobertura_resultado_diagnostico,
                            tbl_ite_cobertura_data_diagnostico,
                            tbl_ite_cobertura_qtd_diagnosticos_positivo,
                            tbl_ite_cobertura_positivo_alterado_em,
                            tbl_ite_cobertura_positivo_alterado_por,
                            tbl_ite_cobertura_nascido,
                            tbl_ite_cobertura_situacao_femea_nascido_outro,
                            tbl_ite_cobertura_negativo_em
                            )
                            VALUES ('$numero_cobertura', 
                                1,
                                '$id_animal',
                                '$codigo_edi',
                                '$codigo_alfa_consulta',
                                '$codigo_numerico_consulta',
                                '$incluido_em',
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                null,
                                0,
                                '$data_prenhes',
                                '$data_previsao_parto',
                                '$diagnostico',
                                '$data_diagnostico',
                                1,
                                null,
                                null,
                                '$nascido',
                                '$nascido_outro',
                                null
                            )";
                        }
                    }
                    else {
                        if ($diagnostico=='N') {
                            $sql = "INSERT INTO tbl_item_cobertura (
                                tbl_ite_cobertura_numero_id,
                                tbl_ite_cobertura_numero_item,
                                tbl_ite_cobertura_codigo_id_animal,
                                tbl_ite_cobertura_codigo_animal,
                                tbl_ite_cobertura_codigo_alfa,
                                tbl_ite_cobertura_codigo_numerico,
                                tbl_ite_cobertura_data_emissao,
                                tbl_ite_cobertura_codigo_touro_semen,
                                tbl_ite_cobertura_lote_semen,
                                tbl_ite_cobertura_nome_inseminador,
                                tbl_ite_cobertura_destino,
                                tbl_ite_cobertura_dia_1,
                                tbl_ite_cobertura_dia_2,
                                tbl_ite_cobertura_dia_3,
                                tbl_ite_cobertura_dia_4,
                                tbl_ite_cobertura_dia_5,
                                tbl_ite_cobertura_dia_6,
                                tbl_ite_cobertura_observacao,
                                tbl_ite_cobertura_numero_cobertura,
                                tbl_ite_cobertura_data_prenhes,
                                tbl_ite_cobertura_previsao_parto,
                                tbl_ite_cobertura_resultado_diagnostico,
                                tbl_ite_cobertura_data_diagnostico,
                                tbl_ite_cobertura_qtd_diagnosticos_positivo,
                                tbl_ite_cobertura_positivo_alterado_em,
                                tbl_ite_cobertura_positivo_alterado_por,
                                tbl_ite_cobertura_nascido,
                                tbl_ite_cobertura_situacao_femea_nascido_outro,
                                tbl_ite_cobertura_negativo_em
                                )
                                VALUES ('$numero_cobertura', 
                                    1,
                                    '$id_animal',
                                    '$codigo_edi',
                                    '$codigo_alfa_consulta',
                                    '$codigo_numerico_consulta',
                                    '$incluido_em',
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    0,
                                    '$data_prenhes',
                                    '$data_previsao_parto',
                                    '$diagnostico',
                                    '$data_diagnostico',
                                    1,
                                    null,
                                    null,
                                    null,
                                    null,
                                    '$data_diagnostico_negativo'
                                )";

                        }   
                        else {
                            $sql = "INSERT INTO tbl_item_cobertura (
                                tbl_ite_cobertura_numero_id,
                                tbl_ite_cobertura_numero_item,
                                tbl_ite_cobertura_codigo_id_animal,
                                tbl_ite_cobertura_codigo_animal,
                                tbl_ite_cobertura_codigo_alfa,
                                tbl_ite_cobertura_codigo_numerico,
                                tbl_ite_cobertura_data_emissao,
                                tbl_ite_cobertura_codigo_touro_semen,
                                tbl_ite_cobertura_lote_semen,
                                tbl_ite_cobertura_nome_inseminador,
                                tbl_ite_cobertura_destino,
                                tbl_ite_cobertura_dia_1,
                                tbl_ite_cobertura_dia_2,
                                tbl_ite_cobertura_dia_3,
                                tbl_ite_cobertura_dia_4,
                                tbl_ite_cobertura_dia_5,
                                tbl_ite_cobertura_dia_6,
                                tbl_ite_cobertura_observacao,
                                tbl_ite_cobertura_numero_cobertura,
                                tbl_ite_cobertura_data_prenhes,
                                tbl_ite_cobertura_previsao_parto,
                                tbl_ite_cobertura_resultado_diagnostico,
                                tbl_ite_cobertura_data_diagnostico,
                                tbl_ite_cobertura_qtd_diagnosticos_positivo,
                                tbl_ite_cobertura_positivo_alterado_em,
                                tbl_ite_cobertura_positivo_alterado_por,
                                tbl_ite_cobertura_nascido,
                                tbl_ite_cobertura_situacao_femea_nascido_outro,
                                tbl_ite_cobertura_negativo_em
                                )
                                VALUES ('$numero_cobertura', 
                                    1,
                                    '$id_animal',
                                    '$codigo_edi',
                                    '$codigo_alfa_consulta',
                                    '$codigo_numerico_consulta',
                                    '$incluido_em',
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    0,
                                    '$data_prenhes',
                                    '$data_previsao_parto',
                                    '$diagnostico',
                                    '$data_diagnostico',
                                    1,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null
                                )";
                        } 

                    }

                $resultado = mysqli_query($conector, $sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado) {
                    echo 'Erro ao grava o item novo do registro: ' . $codigo_numerico_consulta. ' erro: ' . $erro_mysql .'</br>';
                    mysqli_close($conector);
                    exit;
                }

                echo 'Item Cobertura gravada: </br>';

                // Quando o tipo de cobertura for Monta Natural então a estacao de monta recebe o mesmo numero da cobertura (Para ser utilizado no Relatório Situação Reprodutiva Individual)

                $sql = ("UPDATE tbl_cobertura SET 
                                tbl_cobertura_codigo_estacao_monta='$numero_cobertura'
                        WHERE tbl_cobertura_id ='$numero_cobertura'");
                $resultado = mysqli_query($conector, $sql);
            //} 

            $sql = ("UPDATE tbl_cobertura SET 
                    tbl_cobertura_lixeira=1,
                    tbl_cobertura_lixeira_em='$data_sistema',
                    tbl_cobertura_lixeira_por='George Nova Monta'
                WHERE tbl_cobertura_id ='$cobertura_id'");
            $resultado = mysqli_query($conector, $sql);

            // grava o numero da cobertura nova no registro de nascimento do bezerro dessa femea / estacao monta

            if (($nascido == 'N' || $nascido == 'M') && $diagnostico == 'P') {
                $tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
                    WHERE tbl_animal_codigo_mae = '$id_animal' AND 
                          tbl_animal_estacao_monta_nascimento = '$estacao_id'");

                $num_rows = mysqli_num_rows($tbl_animal); 

                if ($num_rows!=0) {
                    $reg_animal = mysqli_fetch_object($tbl_animal); 
                    $codigo_id_bezzero = $reg_animal->tbl_animal_codigo_id;
                    $codigo_num_bezzero = $reg_animal->tbl_animal_codigo_numerico;

                    $sql = ("UPDATE tbl_animais SET 
                        tbl_animal_codigo_cobertura='$numero_cobertura'
                    WHERE tbl_animal_codigo_id ='$codigo_id_bezzero'");

                    $resultado = mysqli_query($conector, $sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado) {
                        echo 'Erro ao grava a cobertura no bezerro: ' . $codigo_num_bezzero. ' erro: ' . $erro_mysql .'</br>';
                    }
                    else {
                        echo 'Bezerro: ' . $codigo_num_bezzero . ' alterado </br>';
                    }
                }
            }

        } // fim while

    }

    print_r('Fim');

/*<?php

$host = 'localhost';
$username = 'seu_usuario';
$password = 'sua_senha';
$dbname = 'seu_banco_de_dados';

// Dados a serem inseridos
$nome = 'João da Silva';
$email = 'joao.silva@exemplo.com';
$telefone = null;
$ultimo_login = null;
$observacoes = null;

// Conexão com o banco de dados
$conn = new mysqli($host, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Prepara a consulta SQL
$sql = "INSERT INTO usuarios (nome, email, telefone, ultimo_login, observacoes) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Verifica se a preparação da consulta foi bem-sucedida
if ($stmt) {
    // Vincula os parâmetros. Note que mesmo para NULL, precisamos definir um tipo (geralmente 's' para string ou 'i' para inteiro, mas como é NULL, o tipo não influenciará o valor final no banco).
    // É importante que o número de tipos na string do bind_param corresponda ao número de placeholders '?'.
    $stmt->bind_param("sss", $nome, $email, $telefone, $ultimo_login, $observacoes);

    // Para inserir valores NULL, podemos simplesmente passar a variável com valor null para o bind_param.
    // O MySQLi interpretará isso corretamente.

    // Executa a consulta
    if ($stmt->execute()) {
        echo "Novo registro inserido com sucesso com campos NULL.";
    } else {
        echo "Erro ao inserir registro: " . $stmt->error;
    }

    // Fecha a declaração preparada
    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $conn->error;
}

// Fecha a conexão com o banco de dados
$conn->close();
?>*/
?>