<?php 
include "conecta_mysql.inc";

$nome_usuario = $_SESSION['nome_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];
$data_sistema = date("Y-m-d H:i:s");

$pasto_origem = $_POST["pasto_origem"];
$qtde_destino = $_POST["qtde_destino"];
$pasto_destino = $_POST["pasto_destino"];
$categoria_destino = $_POST["categoria_destino"];
$sexo_destino = $_POST["sexo_destino"];
$total_animais_origem = $_POST['total_animais'];
$descricao_lote = $_POST["descricao_lote"];
$opcao_descricao_lote = $_POST["opcao_descricao_lote"];
$id_lote = $_POST["id_lote"];
$ano_lote = $_POST["ano_lote"];
$descricao_lote_1 = $_POST["descricao_lote_1"];
$descricao_lote_2 = $_POST["descricao_lote_2"];
$descricao_lote_3 = $_POST["descricao_lote_3"];
$descricao_lote_4 = $_POST["descricao_lote_4"];
$descricao_lote_5 = $_POST["descricao_lote_5"];
$descricao_lote_6 = $_POST["descricao_lote_6"];

$total_animais_destino=$qtde_destino;

$tbl_pasto_origem =  mysqli_query($conector,"SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = '$pasto_origem' AND 
          tbl_pasto_lixeira = 0");

$reg_pasto_origem = mysqli_fetch_object($tbl_pasto_origem);


$array_categoria = explode("!", $reg_pasto_origem->tbl_pasto_array_categoria);
$desc_categoria = [];
$arrayCategorias = [];
$descricaoCategorias = [];

for($i = 0; $i < count($array_categoria); $i++){
    $codigo_categoria = $array_categoria[$i];

    $ssql = "SELECT * FROM tabela_categoria_idade 
        WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
              tab_registro_lixeira_categoria_idade='0'"; 
        
    $rs = mysqli_query($conector,$ssql); 
    $fila = mysqli_fetch_object($rs);

    $codigo_id = $fila->tab_codigo_categoria_idade ;
    $idade_de = $fila->tab_categoria_idade_de;
    $idade_ate = $fila->tab_categoria_idade_ate;

    if ($idade_ate==999999999){
        array_push($desc_categoria, '> 36 meses');
        $descricaoCategorias = [
            "id" => $codigo_id,
            "idade_de" => $idade_de,
            "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
    else {
        array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' meses');
        $descricaoCategorias = [
            "id" => $codigo_id,
            "idade_de" => $idade_de,
            "idade_ate" => $idade_ate
        ];
        array_push($arrayCategorias, $descricaoCategorias);
    }
}

// Pega total de animais do pasto destino antes de incluir os novos animais

$sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
    WHERE tbl_animal_pasto_id = '$pasto_destino'");

$num_rows_pasto_destino = mysqli_num_rows($sql);

// Move os animais para o Pasto Destino

$sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
    WHERE tbl_animal_pasto_id = '$pasto_origem' AND 
          tbl_animal_pasto_situacao = 'A'");

while($reg_animais_origem = mysqli_fetch_object($sql)){
    $sexo = $reg_animais_origem->tbl_animal_pasto_sexo;
    $numero_item = $reg_animais_origem->tbl_animal_pasto_numero_item;
    $data_nascimento = $reg_animais_origem->tbl_animal_pasto_nascimento;  
    $data_acompanhamento_calculo = date("Y-m-d");
    $date = new DateTime($data_nascimento); // Data de Nascimento
    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
    $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
    $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
        WHERE tab_registro_lixeira_categoria_idade='0'");

    $num_rows = mysqli_num_rows($categoria);    

    if ($num_rows!=0) {
        while ($reg_categoria = mysqli_fetch_object($categoria)) {
            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
            $idade_de = $reg_categoria->tab_categoria_idade_de;
            $idade_ate = $reg_categoria->tab_categoria_idade_ate;

            if ($qtde_destino!=0) {
                if($meses>=$idade_de && $meses<=$idade_ate){
                    if ($codigo_categoria==$categoria_destino){
                        if ($sexo_destino=='') {
                            $query = "UPDATE tbl_animal_pasto SET
                                    tbl_animal_pasto_id = $pasto_destino,
                                    tbl_animal_pasto_categoria=$codigo_categoria,
                                    tbl_animal_pasto_alterado_em = '$data_sistema',
                                    tbl_animal_pasto_alterado_por = '$nome_usuario'
                                WHERE tbl_animal_pasto_id = $pasto_origem AND
                                      tbl_animal_pasto_numero_item = $numero_item AND
                                      tbl_animal_pasto_situacao = 'A'";

                            $resultado = mysqli_query($conector, $query);
                            $erro_mysql = mysqli_error($conector);

                            if (!$resultado){
                                header('Content-type: application/json');
                                echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                                exit;
                            } 
                            else {
                                $qtde_destino-= 1;
                            }
                        }
                        else {
                            if ($sexo==$sexo_destino) {
                                $query = "UPDATE tbl_animal_pasto SET
                                        tbl_animal_pasto_id = $pasto_destino,
                                        tbl_animal_pasto_categoria=$codigo_categoria,
                                        tbl_animal_pasto_alterado_em = '$data_sistema',
                                        tbl_animal_pasto_alterado_por = '$nome_usuario'
                                    WHERE tbl_animal_pasto_id = $pasto_origem AND
                                          tbl_animal_pasto_numero_item = $numero_item AND
                                          tbl_animal_pasto_situacao = 'A'";

                                $resultado = mysqli_query($conector, $query);
                                $erro_mysql = mysqli_error($conector);

                                if (!$resultado){
                                    header('Content-type: application/json');
                                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                                    exit;
                                } 
                                else {
                                    $qtde_destino-= 1;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

//pegando info do nascimento dos animais no pasto origem

// AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
$sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
     WHERE tbl_animal_pasto_id = '$pasto_origem' AND  
           tbl_animal_pasto_situacao = 'A'");

if (mysqli_num_rows($sql) == 0){
    $data_com_remover = $reg_pasto_origem->tbl_pasto_data_com_animais;
    $data_com_remover_anterior = $reg_pasto_origem->tbl_pasto_data_com_animais_anterior;
    $data_sem_remover = $reg_pasto_origem->tbl_pasto_data_sem_animais;
    $data_sem_remover_anterior = $reg_pasto_origem->tbl_pasto_data_sem_animais_anterior;

    $dataAtual = new DateTime();
    $dataCom = new DateTime($data_com_remover);
    $diff = $dataAtual->diff($dataCom);

    if ($diff->h + ($diff->days * 24) < 24){
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_com_animais = '$data_sem_remover_anterior',
            tbl_pasto_data_com_animais_anterior = '$data_sem_remover_anterior',
            tbl_pasto_data_sem_animais = '$data_sem_remover_anterior',
            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover_anterior'
            WHERE tbl_pasto_id = $pasto_origem";

        $resultado = mysqli_query($conector, $query);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas SEM retornar data anterior ' . $erro_mysql));
            exit;
        } 
    }
    else {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_sem_animais = '$data_sistema',
            tbl_pasto_data_sem_animais_anterior = '$data_sem_remover'
            WHERE tbl_pasto_id = $pasto_origem";

        $resultado = mysqli_query($conector, $query);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas SEM ' . $erro_mysql));
            exit;
        } 
    }
}

$tbl_pasto_entrar = mysqli_query($conector, "SELECT * FROM tbl_pasto 
    WHERE tbl_pasto_id = '$pasto_destino' AND 
          tbl_pasto_lixeira = 0");

$reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

$data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
$data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
$data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
$data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

if ($num_rows_pasto_destino==0) {
    $dataAtual = new DateTime();
    $dataCom = new DateTime($data_com_incluir);
    $diff = $dataAtual->diff($dataCom);

    if ($diff->h + ($diff->days * 24) < 24){
        $query = "UPDATE tbl_pasto SET
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario',
            tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
            tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
            tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
            tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
            WHERE tbl_pasto_id = $pasto_destino";

        $resultado = mysqli_query($conector, $query);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
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
                tbl_pasto_alterado_por = '$nome_usuario',
                tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
                tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
                tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
                tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
                WHERE tbl_pasto_id = $pasto_destino";

            $resultado = mysqli_query($conector, $query);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                header('Content-type: application/json');
                echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                exit;
            } 
        }
        else {
            if ($num_rows_pasto_destino==0) {
                $query = "UPDATE tbl_pasto SET
                    tbl_pasto_alterado_em = '$data_sistema',
                    tbl_pasto_alterado_por = '$nome_usuario',
                    tbl_pasto_data_com_animais = '$data_sistema',
                    tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                WHERE tbl_pasto_id = $pasto_destino";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
        }
    }
}

// ALTERAR DESCRICAO DO LOTE DOS PASTOS

/*$item_pastos = 0;

for ($i=0; $i <9 ; $i++) { 
    if ($pasto_bezerros_id) {
        $array_pastos_destino[$i]=$pasto_bezerros_id;
        $pasto_bezerros_id=0;
        $item_pastos++;
    }
    else if ($pasto_macho_002) {
        $array_pastos_destino[$i]=$pasto_macho_002;
        $pasto_macho_002=0;
        $item_pastos++;
    }
    else if ($pasto_femea_002) {
        $array_pastos_destino[$i]=$pasto_femea_002;
        $pasto_femea_002=0;
        $item_pastos++;
    }
    else if ($pasto_macho_003) {
        $array_pastos_destino[$i]=$pasto_macho_003;
        $pasto_macho_003=0;
        $item_pastos++;
    }
    else if ($pasto_femea_003) {
        $array_pastos_destino[$i]=$pasto_femea_003;
        $pasto_femea_003=0;
        $item_pastos++;
    }
    else if ($pasto_macho_004) {
        $array_pastos_destino[$i]=$pasto_macho_004;
        $pasto_macho_004=0;
        $item_pastos++;
    }
    else if ($pasto_femea_004) {
        $array_pastos_destino[$i]=$pasto_femea_004;
        $pasto_femea_004=0;
        $item_pastos++;
    }
    else if ($pasto_macho_005) {
        $array_pastos_destino[$i]=$pasto_macho_005;
        $pasto_macho_005=0;
        $item_pastos++;
    }
    else if ($pasto_femea_005) {
        $array_pastos_destino[$i]=$pasto_femea_005;
        $pasto_femea_005=0;
        $item_pastos++;
    }
}

$pasto_destino_referencia = $array_pastos_destino[0];
$move_tudo = '';

for ($i=0; $i <$item_pastos ; $i++) { 
    if ($array_pastos_destino[$i]==$pasto_destino_referencia){
    
        if ($move_tudo!='N') {
            $move_tudo = 'S';
        }
    }
    else {
        $move_tudo = 'N';
    }
}*/

// Pega a descrição do lote do pasto destino para ver se esta vazio
$sql = mysqli_query($conector, "SELECT * FROM tbl_pasto
    WHERE tbl_pasto_id = '$pasto_destino'");

$reg_pasto_incluir = mysqli_fetch_object($sql);
$descricao_lote_pasto_destino = $reg_pasto_incluir->tbl_pasto_descricao_lote;
//------------------------------------------------------------------

/* Premissa 1 - Levar todos os animais para outro pasto vazio:
                Mover a Descrição do Lote para o Pasto Destino
                Limpar a Descrição do Lote do Pasto Origem
                Mover a nutrição do dia para pasto Destino
*/

if ($total_animais_origem==$total_animais_destino && 
    $descricao_lote_pasto_destino=='') {

    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = '$descricao_lote',
        tbl_pasto_id_lote = '$id_lote', 
        tbl_pasto_ano_lote = '$ano_lote',
        tbl_pasto_descricao_lote_1 = '$descricao_lote_1',
        tbl_pasto_descricao_lote_2 = '$descricao_lote_2',
        tbl_pasto_descricao_lote_3 = '$descricao_lote_3',
        tbl_pasto_descricao_lote_4 = '$descricao_lote_4',
        tbl_pasto_descricao_lote_5 = '$descricao_lote_5',
        tbl_pasto_descricao_lote_6 = '$descricao_lote_6',
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
    WHERE tbl_pasto_id = '$pasto_destino'";

    $descricao_lote_pasto_destino = $descricao_lote;
    $descricao_lote = '';
    
    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto destino' . $erro_mysql));
        exit;
    } 

    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = null,
        tbl_pasto_id_lote = null, 
        tbl_pasto_ano_lote = null,
        tbl_pasto_descricao_lote_1 = null,
        tbl_pasto_descricao_lote_2 = null,
        tbl_pasto_descricao_lote_3 = null,
        tbl_pasto_descricao_lote_4 = null,
        tbl_pasto_descricao_lote_5 = null,
        tbl_pasto_descricao_lote_6 = null,
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
        WHERE tbl_pasto_id = '$pasto_origem'";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql));
        exit;
    } 

    // SE HOUVER NUTRIÇÃO NO DIA DA TRANSFERENCIA DOS ANIMAIS, ENTÃO DEVERÁ SER MOVIDA PARA O PASTO DESTINO CONFORME A PREMISSA 1

    $data_atual= date("Y-m-d");
    $query = "UPDATE tbl_nutricao SET
        tbl_nutricao_codigo_pasto = $pasto_destino
        WHERE tbl_nutricao_codigo_pasto = $pasto_origem AND 
              tbl_nutricao_data = '$data_atual'";

    $resultado = mysqli_query($conector, $query);
}
else if ($total_animais_origem==$total_animais_destino) {
    /* Premissa 6 - Levar todos os animais para outro pasto com animais:
                    Manter a Descrição do Lote do Pasto Destino
                    Limpar a Descrição do Lote do Pasto Origem
                    Mover a nutrição do dia para pasto Destino
    */

    $descricao_lote = '';

    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = null,
        tbl_pasto_id_lote = null, 
        tbl_pasto_ano_lote = null,
        tbl_pasto_descricao_lote_1 = null,
        tbl_pasto_descricao_lote_2 = null,
        tbl_pasto_descricao_lote_3 = null,
        tbl_pasto_descricao_lote_4 = null,
        tbl_pasto_descricao_lote_5 = null,
        tbl_pasto_descricao_lote_6 = null,
        tbl_pasto_alterado_em = '$data_sistema',
        tbl_pasto_alterado_por = '$nome_usuario'
        WHERE tbl_pasto_id = '$pasto_origem'";

    $resultado = mysqli_query($conector, $query);
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar a descrição do lote no pasto origem' . $erro_mysql));
        exit;
    } 

    // SE HOUVER NUTRIÇÃO NO DIA DA TRANSFERENCIA DOS ANIMAIS, ENTÃO DEVERÁ SER MOVIDA PARA O PASTO DESTINO CONFORME A PREMISSA 1

    $data_atual= date("Y-m-d");
    $query = "UPDATE tbl_nutricao SET
        tbl_nutricao_codigo_pasto = $pasto_destino
        WHERE tbl_nutricao_codigo_pasto = $pasto_origem AND 
              tbl_nutricao_data = '$data_atual'";

    $resultado = mysqli_query($conector, $query);
}

header('Content-type: application/json');
echo json_encode(array('success' => true, 'message' => 'Animais movidos com sucesso.', 'descricao_lote_pasto_destino' => $descricao_lote_pasto_destino, 'descricao_lote_pasto_origem' => $descricao_lote));
exit;

?>