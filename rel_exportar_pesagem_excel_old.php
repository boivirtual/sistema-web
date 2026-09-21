<?php
// O campo tipo_registro foi incluido na tabela 'tbl_pesagem' 13/09/2024 para identificar se foi pesagem on-line ou off-line

include "valida_sessao.inc";
include "conecta_mysql.inc";

$data_sistema = date("d/m/Y");
$data_impressao = date("Y-m-d H:i:s");
$tipo_registro = 'OFFLINE';

@session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$cnpj_cliente = $_SESSION['id_cliente'];

$d = json_decode($_POST["dataObj"]);

$local = $d->local;
$epoca = $d->epoca;
$origem_filtro = $d->origem;
$desc_filtro = $d->desc_filtro;
$desc_filtro_gravar = $d->desc_filtro;
$sexo_filtro = $d->sexo;
$raca_filtro = $d->raca;

$categoria_filtro = $d->categoria;
$pai_filtro = $d->pai;
$mae_filtro = $d->mae;
$data_nasc_inicial_filtro = $d->data_nasc_inicial;
$data_nasc_final_filtro = $d->data_nasc_final;
$peso_nasc_inicial_filtro = $d->peso_nasc_inicial;
$peso_nasc_final_filtro = $d->peso_nasc_final;
$peso_desmama_inicial_filtro = $d->peso_desmama_inicial;
$peso_desmama_final_filtro = $d->peso_desmama_final;
$peso_ult_inicial_filtro = $d->peso_ult_inicial;
$peso_ult_final_filtro = $d->peso_ult_final;
$data_pesagem = $d->data_pesagem;

$solteiras = $d->solteiras;
$descarte = $d->descarte;
$paridas = $d->paridas;
$data_paridas_ate = $d->data_paridas;
$parto = $d->parto;
$num_parto_de = $d->num_parto_de;
$num_parto_ate = $d->num_parto_ate;
$aborto = $d->aborto;
$num_aborto_de = $d->num_aborto_de;
$num_aborto_ate = $d->num_aborto_ate;
$previsao_parto_de = $d->previsao_parto_de;
$previsao_parto_ate = $d->previsao_parto_ate;
$positivo = $d->positivo;
$negativo = $d->negativo;
$estacao_filtro = $d->estacao;

if ($estacao_filtro!='') {
    $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
        WHERE tbl_par_estacao_nome='$estacao_filtro'
        ORDER BY tbl_par_estacao_id ASC");  

    $num_rows = mysqli_num_rows($sql);
    $array_estacao = array();

    if ($num_rows!=0) {
        while ($reg_estacao = mysqli_fetch_object($sql)){
            $codigo_estacao = $reg_estacao->tbl_par_estacao_id;
            $array_estacao[] = $codigo_estacao;
        }

        $array_estacao = implode(',', $array_estacao);
    }
}

$westacao = "";
if (!empty ($array_estacao)) {
    
    $array_estacao = explode(',', $array_estacao);

    $westacao = " AND tbl_cobertura_codigo_estacao_monta IN(";
    $westacao.= implode(',', $array_estacao);
    $westacao.= ")";
}

/*if (($positivo=='' && $negativo=='') || ($positivo=='S' && $negativo=='S')) {
    $positivo = '';
    $negativo = '';
}
*/

if ($data_paridas_ate == '') {
    $data_paridas_ate = '9999-99-99';
    $data_paridas_de = '0000-00-00';
} else {
    $data_paridas_de = date("Y-m-d", strtotime('-8 month', strtotime($data_paridas_ate)));
    $data_paridas_de = date("Y-m-d", strtotime('-1 day', strtotime($data_paridas_de)));
}

if ($previsao_parto_de == '') {
    $previsao_parto_de = '0000-00-00';
    $previsao_parto_ate = '9999-99-99';
}

$vaca_solteira = '';
$vaca_parida = '';
$vaca_descarte = '';
$tem_parto = '';
$tem_aborto = '';
$tem_previsao_parto = '';
$ultimo_parto = '0000-00-00';
$data_previsao_parto = '0000-00-00';
$tem_positivo = '';
$tem_negativo = '';

$data_sistema = date('d/m/Y', strtotime($data_pesagem));

$raca = $raca_filtro;

$raca = implode(',', $raca);
//$raca = substr($raca, 0, -1);

$wraca = '';

if ($raca != '') {
    $wraca = " AND tbl_animal_codigo_raca IN(";
    $wraca .= $raca;
    $wraca .= ")";
}

$origem = $origem_filtro;

$origem = implode(',', $origem);
//$origem = substr($origem, 0, -1);

$worigem = '';

if ($origem != '') {
    $worigem = " AND tbl_animal_codigo_origem IN(";
    $worigem .= $origem;
    $worigem .= ")";
}

$categoria = $categoria_filtro;

$categoria = implode(',', $categoria);
//$categoria = substr($categoria, 0, -1);

$wcategoria = '';

if ($categoria_filtro != '') {
    $wcategoria.= $categoria;
}

$pai = $pai_filtro;

$pai = implode(',', $pai);
//$pai = substr($pai, 0, -1);

$wpai = '';

if ($pai != '') {
    $wpai = " AND tbl_animal_codigo_pai IN(";
    $wpai .= $pai;
    $wpai .= ")";
}

$mae = $mae_filtro;

$mae = implode(',', $mae);
//$mae = substr($mae, 0, -1);

$wmae = '';

if ($mae != '') {
    $wmae = " AND tbl_animal_codigo_mae IN(";
    $wmae .= $mae;
    $wmae .= ")";
}

$wsexo = '';
if ($sexo_filtro[0] == 'Todos') {
    $wsexo = '';
} else {
    $wsexo = " AND tbl_animal_sexo IN(";
    $wsexo .= "'" . $sexo_filtro[0] . "'";
    $wsexo .= ")";
}

if ($data_nasc_inicial_filtro == '' && $data_nasc_final_filtro == '') {
    $wdata_nasc = '';
} else {
    $wdata_nasc = " AND tbl_animal_data_nascimento >= '$data_nasc_inicial_filtro' AND tbl_animal_data_nascimento <= '$data_nasc_final_filtro'";
}

if ($peso_nasc_inicial_filtro == '' && $peso_nasc_final_filtro == '') {
    $wpeso_nasc = '';
} else {
    $wpeso_nasc = " AND tbl_animal_primeiro_peso >= '$peso_nasc_inicial_filtro' AND tbl_animal_primeiro_peso <= '$peso_nasc_final_filtro'";
}

if ($peso_desmama_inicial_filtro == '' && $peso_desmama_final_filtro == '') {
    $wpeso_desmama = '';
} else {
    $wpeso_desmama = " AND tbl_animal_peso_desmama >= '$peso_desmama_inicial_filtro' AND tbl_animal_peso_desmama <= '$peso_desmama_final_filtro'";
}

if ($peso_ult_inicial_filtro == '' && $peso_ult_final_filtro == '') {
    $wpeso_ult = '';
} else {
    $wpeso_ult = " AND tbl_animal_ultimo_peso >= '$peso_ult_inicial_filtro' AND tbl_animal_ultimo_peso <= '$peso_ult_final_filtro'";
}

$tbl_local = mysqli_query($conector, "SELECT * FROM tbl_pessoa
                                        WHERE tbl_pessoa_id='$local'");

$num_rows = mysqli_num_rows($tbl_local);

if ($num_rows != 0) {
    $reg_local = mysqli_fetch_object($tbl_local);
    $desc_local = $reg_local->tbl_pessoa_nome;
} else {
    $desc_local = 'Não Informado';
}

$tbl_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem
                                        WHERE tab_codigo_epoca_pesagem='$epoca'");

$num_rows = mysqli_num_rows($tbl_epoca);

if ($num_rows != 0) {
    $reg_epoca = mysqli_fetch_object($tbl_epoca);
    $desc_epoca = utf8_encode($reg_epoca->tab_descricao_epoca_pesagem);
} else {
    $desc_epoca = 'Não Informada';
}

$sql = "INSERT INTO tbl_pesagem (
        tbl_pesagem_controle,
        tbl_pesagem_data,
        tbl_pesagem_codigo_local,
        tbl_pesagem_codigo_epoca,
        tbl_pesagem_lote,
        tbl_pesagem_qtd_animais_pesados,
        tbl_pesagem_peso_kg,
        tbl_pesagem_peso_arroba,
        tbl_pesagem_peso_medio_kg,
        tbl_pesagem_peso_medio_arroba,
        tbl_pesagem_filtros,
        tbl_pesagem_finalizada,
        tbl_pesagem_incluido_em,
        tbl_pesagem_incluido_por,
        tbl_pesagem_alterado_em,
        tbl_pesagem_alterado_por,
        tbl_pesagem_lixeira,
        tbl_pesagem_lixeira_em,
        tbl_pesagem_lixeira_por,
        tbl_pesagem_codigo_movimentacao,
        tbl_pesagem_tipo_registro

        ) VALUES (
        'I',
        '$data_pesagem',
        '$local',
        '$epoca',
        '',
        0,
        0,
        0,
        0,
        0,
        '$desc_filtro_gravar',
        'N',
        '$data_impressao',
        '$nomeusuario',
        null,
        null,
        0,
        null,
        null,
        0,
        '$tipo_registro'
    )";

$resultado = mysqli_query($conector, $sql);
$erro_mysql = mysqli_error($conector);

if (!$resultado) {
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusão da pesagem. Erro: ' . $erro_mysql));
    exit;
}

$numero_pesagem = mysqli_insert_id($conector);
$numero_pesagem = str_pad($numero_pesagem, 9, "0", STR_PAD_LEFT);
$numero_item = 0;

$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_fazenda='$local' AND
          tbl_animal_ativo='S' AND
          tbl_animal_lixeira=0" . $wsexo . $worigem . $wraca . $wpai . $wmae . $wdata_nasc .
    $wpeso_nasc . $wpeso_desmama . $wpeso_ult . "
    ORDER BY tbl_animal_codigo_numerico ASC ");


/*$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
    WHERE tbl_animal_codigo_id=1234 
    ORDER BY tbl_animal_codigo_numerico ASC ");*/

$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows != 0) {

    while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
        $codigo_id_animal = $reg_animal->tbl_animal_codigo_id;
        $id_alfa = $reg_animal->tbl_animal_codigo_alfa;
        $id_numero = $reg_animal->tbl_animal_codigo_numerico;
        $nascimento = new DateTime($reg_animal->tbl_animal_data_nascimento);
        $nascimento_edi = $nascimento->format('d/m/Y');
        $raca = $reg_animal->tbl_animal_codigo_raca;
        $pelagem = $reg_animal->tbl_animal_codigo_pelagem;
        $sexo_animal = $reg_animal->tbl_animal_sexo;
        $mae_animal = $reg_animal->tbl_animal_codigo_mae;
        $animal_descarte = $reg_animal->tbl_animal_descarte_reproducao;

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

        $tem_negativo = '';
        $tem_positivo = '';
        $vaca_descarte = '';

        if ($descarte == 'S') {
            if ($animal_descarte == 'S') {
                $vaca_descarte = 'S';
            }
        }

        $data_nascimento = $reg_animal->tbl_animal_data_nascimento;
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y') * 12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade_animal = $idade_acompanhamento_mostra_anos + $idade_acompanhamento_mostra_meses;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

        $num_rows = mysqli_num_rows($categoria);

        if ($num_rows != 0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade_animal >= $idade_de && $idade_animal <= $idade_ate) {
                    if ($idade_ate == 999999999) {
                        $desc_categoria = '> 36 meses';
                    } else {
                        $desc_categoria = $idade_de . ' a ' . $idade_ate . ' meses';
                    }
                    $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                }
            }
        } else {
            $codigo_categoria = 0;
        }

        if ($id_alfa != '') {
            $id_edi = $id_alfa . '-' . intval($id_numero);
        } else {
            $id_edi = intval($id_numero);
        }

        if ($reg_animal->tbl_animal_sexo == 'M') {
            $sexo = 'Macho';
        } else {
            $sexo = 'Femea';
        }

        $tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas
                                                WHERE tab_codigo_raca='$raca'");

        $num_rows = mysqli_num_rows($tbl_raca);

        if ($num_rows != 0) {
            $reg_raca = mysqli_fetch_object($tbl_raca);
            $desc_raca = utf8_encode($reg_raca->tab_descricao_raca);
        } else {
            $desc_raca = '';
        }

        $tbl_pelagem = mysqli_query($conector, "SELECT * FROM tabela_pelagens
                                                WHERE tab_codigo_pelagem='$pelagem'");

        $num_rows = mysqli_num_rows($tbl_pelagem);

        if ($num_rows != 0) {
            $reg_pelagem = mysqli_fetch_object($tbl_pelagem);
            $desc_pelagem = utf8_encode($reg_pelagem->tab_descricao_pelagem);
        } else {
            $desc_pelagem = '';
        }

        $tbl_mae = mysqli_query($conector, "SELECT * FROM tbl_animais
                                                WHERE tbl_animal_codigo_id='$mae_animal'");

        $num_rows = mysqli_num_rows($tbl_mae);

        if ($num_rows != 0) {
            $reg_mae = mysqli_fetch_object($tbl_mae);

            if ($reg_mae->tbl_animal_codigo_alfa == '') {
                $desc_mae = intval($reg_mae->tbl_animal_codigo_numerico);
            } else {
                $desc_mae = $reg_mae->tbl_animal_codigo_alfa . '-' . intval($reg_mae->tbl_animal_codigo_numerico);
            }
        } else {
            $desc_mae = '';
        }

        // verifica vacas solteiras
        if ($solteiras == 'S' || $paridas == 'S') {
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_id_animal'
                ORDER BY tbl_animal_data_nascimento DESC limit 1");

            $ultimo_filho = mysqli_num_rows($tbl_filhos);

            if ($ultimo_filho != 0) {
                $reg_filhos = mysqli_fetch_object($tbl_filhos);
                $ultimo_parto = $reg_filhos->tbl_animal_data_nascimento;

                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($ultimo_parto); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y') * 12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade_ano = $idade_acompanhamento->format('%Y');
                $idade_mes = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos + $idade_acompanhamento_mostra_meses;

                if ($idade < 8) {
                    $vaca_parida = 'S';
                    $vaca_solteira = '';
                } else {
                    $vaca_solteira = 'S';
                    $vaca_parida = '';
                }
            } else {
                $ultimo_parto = '0000-00-00';
            }
        }

        // verifica partos
        if ($sexo == 'Femea' && $num_parto_de != '' && $num_parto_ate != '') {
            $tbl_filhos = mysqli_query($conector, "SELECT * FROM tbl_animais 
                WHERE tbl_animal_codigo_mae='$codigo_id_animal'");

            $num_partos = mysqli_num_rows($tbl_filhos);

            if (
                $num_partos >= $num_parto_de &&
                $num_partos <= $num_parto_ate && $idade_animal >= 8
            ) {
                $tem_parto = "S";
            } else {
                $tem_parto = "N";
            }
        }
        else {

        }

        // verifica abortos
        if ($sexo == 'Femea' && $num_aborto_de != '' && $num_aborto_ate != '') {
            $tbl_natimorto = mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque 
                WHERE tbl_mov_estoque_codigo_mae='$codigo_id_animal' AND 
                      tbl_mov_estoque_codigo_id_animal=999999999 AND
                      (tbl_mov_estoque_entrada_saida='A' OR 
                       tbl_mov_estoque_entrada_saida='S') AND 
                      (tbl_mov_estoque_tipo_movimentacao='M' OR
                       tbl_mov_estoque_tipo_movimentacao='A' OR
                       tbl_mov_estoque_tipo_movimentacao='B')");

            $num_natimorto = mysqli_num_rows($tbl_natimorto);

            if (
                $num_natimorto >= $num_aborto_de &&
                $num_natimorto <= $num_aborto_ate
            ) {
                $tem_aborto = "S";
            } else {
                $tem_aborto = "N";
            }
        }

        // Verifica previsão de parto
        if ($previsao_parto_de != '' && $previsao_parto_ate != '') {

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON  tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id_animal' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P'
                ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1");

            $num_rows_coberturas = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows_coberturas != 0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
                $protocolo_id = $reg_cobertura->tbl_cobertura_protocoloiatf;
                $cobertura_id = $reg_cobertura->tbl_cobertura_id;

                $sql = mysqli_query($conector, "SELECT * FROM tbl_protocolo_cobertura 
                        WHERE tbl_protocolo_cobertura_codigo_id = '$cobertura_id'");

                $reg_protocolo_cobertura = mysqli_fetch_object($sql);

                $sql =  mysqli_query($conector, "SELECT * FROM tbl_item_protocoloiatf 
                    WHERE tbl_ite_protocoloiatf_lixeira = 0 AND 
                          tbl_ite_protocoloiatf_protocolo_id = '$protocolo_id' 
                    ORDER BY tbl_ite_protocoloiatf_id ASC");

                $dias_previsao_parto = 282;

                while ($reg_itens_iatf = mysqli_fetch_object($sql)) {
                    $dias = substr($reg_itens_iatf->tbl_ite_protocoloiatf_descricao, 3);

                    $data_servico = date("Y-m-d", strtotime($reg_protocolo_cobertura->tbl_protocolo_cobertura_data . "+{$dias} days"));

                    $data_previsao_parto = date("Y-m-d", strtotime($data_servico . "+{$dias_previsao_parto} days"));
                }
            } else {
                $data_previsao_parto = '0000-00-00';
            }
        }

        // verifica a cobertura do animal
        $sql = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura
            INNER JOIN tbl_cobertura
                    ON tbl_ite_cobertura_numero_id = tbl_cobertura_id
            INNER JOIN tbl_parametro_estacao_monta
                    ON tbl_par_estacao_id = tbl_cobertura_codigo_estacao_monta
            WHERE tbl_cobertura_lixeira=0 AND 
                  tbl_ite_cobertura_codigo_id_animal='$codigo_id_animal'" . $westacao . "
            ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

        $num_rows = mysqli_num_rows($sql);

        if ($num_rows!=0) {
            $reg_cobertura = mysqli_fetch_object($sql);
            $codigo_local = $reg_cobertura->tbl_cobertura_codigo_local;
            $estacao_animal = $reg_cobertura->tbl_cobertura_codigo_estacao_monta;
            $estacao_monta = $reg_cobertura->tbl_par_estacao_nome;
        }
        else {
            $codigo_local = 0;
            $estacao_animal = 0;
            $estacao_monta = '';
        }

        // Verifica diagnostico
        if ($positivo == 'S' || $negativo == 'S') {

            $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
                INNER JOIN tbl_cobertura
                        ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id_animal' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_cobertura_codigo_estacao_monta = '$estacao_animal'
                ORDER BY tbl_ite_cobertura_numero_id DESC limit 1");

            $num_rows = mysqli_num_rows($tbl_item_cobertura);

            if ($num_rows != 0) {
                $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);

                $diagnostico = $reg_cobertura->tbl_ite_cobertura_resultado_diagnostico;

                if ($diagnostico == 'P') {
                    $tem_positivo = 'S';
                    $tem_negativo = '';
                } else if ($diagnostico == 'N') {
                    $tem_negativo = 'S';
                    $tem_positivo = '';
                } else {
                    $tem_negativo = '';
                    $tem_positivo = '';
                }
            } else {
                $tem_negativo = '';
                $tem_positivo = '';
            }
        } else {
            $tem_negativo = '';
            $tem_positivo = '';
        }

        // verifica natimortos, nascidos ou abortos na estacao
        $tbl_item_cobertura = mysqli_query($conector, "SELECT * FROM tbl_item_cobertura 
            INNER JOIN tbl_cobertura
                    ON tbl_cobertura_id = tbl_ite_cobertura_numero_id
                WHERE tbl_cobertura_lixeira=0 AND
                      tbl_ite_cobertura_codigo_id_animal = '$codigo_id_animal' AND 
                      tbl_cobertura_controle = 'C' AND 
                      tbl_ite_cobertura_resultado_diagnostico = 'P' AND 
                      tbl_cobertura_codigo_estacao_monta ='$estacao_animal'  
            ORDER BY tbl_ite_cobertura_data_emissao DESC limit 1"); 

        $num_rows_item = mysqli_num_rows($tbl_item_cobertura);

        if ($num_rows_item!=0) {
            $reg_cobertura = mysqli_fetch_object($tbl_item_cobertura);
            $nascido_aborto = $reg_cobertura->tbl_ite_cobertura_nascido;
        }
        else {
            $nascido_aborto = '';
        }

        if ($positivo=='S' AND 
            $nascido_aborto!='') {
            $tem_positivo='';
        }

        if ($wcategoria == "" && $solteiras == $vaca_solteira && $descarte == $vaca_descarte && $paridas == $vaca_parida && $ultimo_parto >= $data_paridas_de && $ultimo_parto <= $data_paridas_ate &&
            $parto == $tem_parto && $aborto == $tem_aborto &&
            $data_previsao_parto >= $previsao_parto_de &&
            $data_previsao_parto <= $previsao_parto_ate &&
            $positivo == $tem_positivo &&
            $negativo == $tem_negativo) {

            $numero_item++;

            $sql = "INSERT INTO tbl_item_pesagem (
                                tbl_ite_pesagem_numero_id,
                                tbl_ite_pesagem_numero_item,
                                tbl_ite_pesagem_data_emissao,
                                tbl_ite_pesagem_codigo_id_animal,
                                tbl_ite_pesagem_codigo_animal,
                                tbl_ite_pesagem_peso,
                                tbl_ite_pesagem_sexo,
                                tbl_ite_pesagem_nascimento,
                                tbl_ite_pesagem_raca,
                                tbl_ite_pesagem_pelagem,
                                tbl_ite_pesagem_mae,
                                tbl_ite_pesagem_observacao,
                                tbl_ite_pesagem_categoria,
                                tbl_ite_pesagem_qtd_animais,
                                tbl_ite_pesagem_ultimo_peso

                            ) VALUES (
                                '$numero_pesagem',
                                '$numero_item',
                                '$data_pesagem',
                                '$codigo_id_animal',
                                '$id_edi',
                                0,
                                '$sexo',
                                '$nascimento_edi',
                                '$desc_raca',
                                '$desc_pelagem',
                                '$desc_mae',
                                '',
                                '$codigo_categoria',
                                1,
                                '$peso'
                        )";
            $resultado = mysqli_query($conector, $sql);
            $erro_mysql = mysqli_error($conector);
            if (!$resultado) {
                header('Content-type: application/json');
                echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro do item. Erro: ' . $erro_mysql));
                exit;
            }
        } 
        else {

            $matriz_itens = explode(",", $wcategoria);
            $quantidade_itens = count($matriz_itens);

            for ($i = 0; $i < $quantidade_itens; $i++) {
                if ($matriz_itens[$i] == $codigo_categoria && $solteiras == $vaca_solteira  && $descarte == $vaca_descarte && $paridas == $vaca_parida && $ultimo_parto >= $data_paridas_de && $ultimo_parto <= $data_paridas_ate && $parto == $tem_parto && $aborto == $tem_aborto &&
                    $data_previsao_parto >= $previsao_parto_de &&
                    $data_previsao_parto <= $previsao_parto_ate &&
                    $positivo == $tem_positivo &&
                    $negativo == $tem_negativo) {

                    $numero_item++;

                    $sql = "INSERT INTO tbl_item_pesagem (
                                tbl_ite_pesagem_numero_id,
                                tbl_ite_pesagem_numero_item,
                                tbl_ite_pesagem_data_emissao,
                                tbl_ite_pesagem_codigo_id_animal,
                                tbl_ite_pesagem_codigo_animal,
                                tbl_ite_pesagem_peso,
                                tbl_ite_pesagem_sexo,
                                tbl_ite_pesagem_nascimento,
                                tbl_ite_pesagem_raca,
                                tbl_ite_pesagem_pelagem,
                                tbl_ite_pesagem_mae,
                                tbl_ite_pesagem_observacao,
                                tbl_ite_pesagem_categoria,
                                tbl_ite_pesagem_qtd_animais,
                                tbl_ite_pesagem_ultimo_peso
                            ) VALUES (
                                '$numero_pesagem',
                                '$numero_item',
                                '$data_pesagem',
                                '$codigo_id_animal',
                                '$id_edi',
                                0,
                                '$sexo',
                                '$nascimento_edi',
                                '$desc_raca',
                                '$desc_pelagem',
                                '$desc_mae',
                                '',
                                '$codigo_categoria',
                                1,
                                '$peso'
                        )";
                    $resultado = mysqli_query($conector, $sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado) {
                        header('Content-type: application/json');
                        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro do item. Erro: ' . $erro_mysql));
                        exit;
                    }
                }
            }
        }
    }
}

if ($numero_item == 0) {
    $sql = ("DELETE FROM tbl_pesagem
        WHERE tbl_pesagem_id='$numero_pesagem'");

    $resultado = mysqli_query($conector, $sql);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado) {
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na exclusão da pesagem sem itens. Erro: ' . $erro_mysql));
    }
} else {
    header('Content-type: application/json');
    echo json_encode(array('error' => false, 'message' => '', 'num_pesagem' => $numero_pesagem, 'desc_filtro' => $desc_filtro, 'desc_local' => $desc_local, 'desc_epoca' => $desc_epoca, 'estacao' => $estacao_animal));
}
