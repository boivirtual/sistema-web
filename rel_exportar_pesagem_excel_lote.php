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
$desc_filtro = $d->desc_filtro;
$desc_filtro_gravar = $d->desc_filtro;
$pasto_filtro = $d->pasto_filtro;
$sexo_filtro = $d->sexo_filtro;
$categoria_filtro = $d->categoria_filtro;
$data_pesagem = $d->data_pesagem;

/*
$peso_nasc_inicial_filtro = $d->peso_nasc_inicial;
$peso_nasc_final_filtro = $d->peso_nasc_final;
$peso_desmama_inicial_filtro = $d->peso_desmama_inicial;
$peso_desmama_final_filtro = $d->peso_desmama_final;
$peso_ult_inicial_filtro = $d->peso_ult_inicial;
$peso_ult_final_filtro = $d->peso_ult_final;
*/

$data_sistema = date('d/m/Y', strtotime($data_pesagem));

$pasto = $pasto_filtro;
$pasto = implode(',', $pasto);
$pasto = substr($pasto,0, -1);

$wpasto = '';

if ($pasto != '') {
    $wpasto = " AND tbl_animal_pasto_id IN(";
    $wpasto .= $pasto;
    $wpasto .= ")";
}

$categoria = $categoria_filtro;
$categoria = implode(',', $categoria);
$categoria = substr($categoria,0, -1);
$wcategoria = '';

if ($categoria != '') {
    $wcategoria = " AND tbl_animal_pasto_categoria IN(";
    $wcategoria .= $categoria;
    $wcategoria .= ")";
}

$wsexo = '';

if ($sexo_filtro[0] == 'Todos') {
    $wsexo = '';
} else {
    $wsexo = " AND tbl_animal_pasto_sexo IN(";
    $wsexo .= "'" . $sexo_filtro[0] . "'";
    $wsexo .= ")";
}

/*if ($peso_nasc_inicial_filtro == '' && $peso_nasc_final_filtro == '') {
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
*/

$tbl_local = mysqli_query($conector, "SELECT * FROM tbl_pessoa
    WHERE tbl_pessoa_id='$local'");

$num_rows = mysqli_num_rows($tbl_local);

if ($num_rows != 0) {
    $reg_local = mysqli_fetch_object($tbl_local);
    $desc_local = $reg_local->tbl_pessoa_nome;
} 
else {
    $desc_local = 'Não Informado';
}

$tbl_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem
    WHERE tab_codigo_epoca_pesagem='$epoca'");

$num_rows = mysqli_num_rows($tbl_epoca);

if ($num_rows != 0) {
    $reg_epoca = mysqli_fetch_object($tbl_epoca);
    $desc_epoca = utf8_encode($reg_epoca->tab_descricao_epoca_pesagem);
} 
else {
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
        tbl_pesagem_tipo_registro,
        tbl_pesagem_origem

        ) VALUES (
        'L',
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
        '$tipo_registro',
        'WEB'
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

$sql = "SELECT * FROM tbl_animal_pasto
    WHERE tbl_animal_pasto_local ='$local' AND
          tbl_animal_pasto_situacao = 'A'" . $wsexo . $wcategoria . $wpasto."
    ORDER BY tbl_animal_pasto_numero_item ASC ";

$tbl_animal = mysqli_query($conector, $sql);
$num_rows = mysqli_num_rows($tbl_animal);

if ($num_rows != 0) {

    while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
        $nascimento = new DateTime($reg_animal->tbl_animal_pasto_nascimento);
        $nascimento_edi = $nascimento->format('d/m/Y');
        $raca = $reg_animal->tbl_animal_pasto_raca;
        $sexo_animal = $reg_animal->tbl_animal_pasto_sexo;
        $codigo_pasto = $reg_animal->tbl_animal_pasto_id;
        //$primeiro_peso = $reg_animal->tbl_animal_primeiro_peso; 
        $peso = 0;
        $codigo_categoria = 0;
        $desc_categoria = '';

        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($reg_animal->tbl_animal_pasto_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $tbl_categoria_pasto = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");
        $num_rows_categoria = mysqli_num_rows($tbl_categoria_pasto);    

        while ($reg_categoria_pasto = mysqli_fetch_object($tbl_categoria_pasto)) {
            $idade_de = $reg_categoria_pasto->tab_categoria_idade_de;
            $idade_ate = $reg_categoria_pasto->tab_categoria_idade_ate;

            if ($idade >= $idade_de && $idade <= $idade_ate) {
                $codigo_categoria = $reg_categoria_pasto->tab_codigo_categoria_idade;

                if ($idade_ate==999999999){
                    $desc_categoria='> 36 meses';
                    }
                else {
                    $desc_categoria=$idade_de . ' a ' . $idade_ate . ' meses';
                }
            }
        }

        $sexo = $sexo_animal;

        /*if ($sexo_animal == 'M') {
            $sexo = 'Macho';
        } else {
            $sexo = 'Femea';
        }*/

        $tbl_raca = mysqli_query($conector, "SELECT * FROM tabela_racas
                                                WHERE tab_codigo_raca='$raca'");

        $num_rows = mysqli_num_rows($tbl_raca);

        if ($num_rows != 0) {
            $reg_raca = mysqli_fetch_object($tbl_raca);
            $desc_raca = utf8_encode($reg_raca->tab_descricao_raca);
        } else {
            $desc_raca = '';
        }

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
            tbl_ite_pesagem_ultimo_peso,
            tbl_ite_pesagem_pasto,
            tbl_ite_pesagem_grupo_pasto_destino

            ) VALUES (
                '$numero_pesagem',
                '$numero_item',
                '$data_pesagem',
                null,
                null,
                0,
                '$sexo',
                '$nascimento_edi',
                '$desc_raca',
                null,
                null,
                '',
                '$codigo_categoria',
                1,
                '$peso',
                '$codigo_pasto',
                null
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
    echo json_encode(array('error' => false, 'message' => '', 'num_pesagem' => $numero_pesagem, 'desc_filtro' => $desc_filtro, 'desc_local' => $desc_local, 'desc_epoca' => $desc_epoca, 'estacao' => ''));
}
