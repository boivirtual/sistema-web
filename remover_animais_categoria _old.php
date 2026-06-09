<?php 
include "conecta_mysql.inc";

$nome_usuario = $_SESSION['nome_usuario'];
$controle_estoque = $_SESSION['controle_estoque'];
$data_sistema = date("Y-m-d H:i:s");
$teve_erro = '';
$mens ='';

for ($i=0; $i <=9 ; $i++) { 
   $array_pastos_destino[$i]='';
}

$total_animais_destino = 0;
$pasto_bezerros_id = 0;
$pasto_macho_002 = 0;
$pasto_femea_002 = 0;
$pasto_macho_003 = 0;
$pasto_femea_003 = 0;
$pasto_macho_004 = 0;
$pasto_femea_004 = 0;
$pasto_macho_005 = 0;
$pasto_femea_005 = 0;

if(isset($_POST["descricao_lote_gravar"])){
    $descricao_lote = $_POST["descricao_lote_gravar"];
}
else {
    $descricao_lote = '';
}

$opcao_descricao_lote = $_POST["opcao_descricao_lote"];

//categoria 001 00 a 07 meses
if(isset($_POST['qtde_bezerros']) && $_POST['qtde_bezerros'] != ''){
    $qtde_bezerros = $_POST['qtde_bezerros'];
    $pasto_bezerros_id = $_POST['pasto_bezerros_id'];
    $pasto_remover_id = $_SESSION["pasto_id"];
    $total_animais_destino+=$qtde_bezerros;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_bezerros_id'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["pasto_bezerros_id"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[0]['id'];
        $idade_de = $arrayCategorias[0]['idade_de'];
        $idade_ate = $arrayCategorias[0]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_bezerros!=0){
                $query = "UPDATE tbl_animal_pasto SET
                        tbl_animal_pasto_id = $pasto_incluir_id,
                        tbl_animal_pasto_alterado_em = '$data_sistema',
                        tbl_animal_pasto_alterado_por = '$nome_usuario'
                    WHERE tbl_animal_pasto_id = $pasto_remover_id AND
                          tbl_animal_pasto_numero_item = $numero_item AND
                          tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_bezerros -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_bezerros != 0){
                $query = "UPDATE tbl_animal_pasto SET
                        tbl_animal_pasto_id = $pasto_incluir_id,
                        tbl_animal_pasto_categoria=1,
                        tbl_animal_pasto_alterado_em = '$data_sistema',
                        tbl_animal_pasto_alterado_por = '$nome_usuario'
                    WHERE tbl_animal_pasto_id = $pasto_remover_id AND
                          tbl_animal_pasto_numero_item = $numero_item AND
                          tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_bezerros -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

//categoria 002 08 a 12 meses
if(isset($_POST["qtde_macho_002"]) && $_POST["qtde_macho_002"] != ''){
    $qtde_macho_002 = $_POST["qtde_macho_002"];
    $pasto_macho_002 = $_POST["select_pasto_macho_002"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_macho_002;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_macho_002'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_macho_002"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[1]['id'];
        $idade_de = $arrayCategorias[1]['idade_de'];
        $idade_ate = $arrayCategorias[1]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_macho_002!=0 && $sexo=="M"){
            //if($categoria_pasto == $id_categoria && $qtde_macho_002 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_002 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_macho_002 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=2,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_002 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

if(isset($_POST["qtde_femea_002"]) && $_POST["qtde_femea_002"] != ''){
    $qtde_femea_002 = $_POST["qtde_femea_002"];
    $pasto_femea_002 = $_POST["select_pasto_femea_002"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_femea_002;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_femea_002'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_femea_002"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);
    
    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[1]['id'];
        $idade_de = $arrayCategorias[1]['idade_de'];
        $idade_ate = $arrayCategorias[1]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_femea_002!=0 && $sexo=="F"){
            //if($categoria_pasto == $id_categoria && $qtde_femea_002 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_002 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_femea_002 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=2,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_002 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

//categoria 003 13 a 24 meses
if(isset($_POST["qtde_macho_003"]) && $_POST["qtde_macho_003"] != ''){
    $qtde_macho_003 = $_POST["qtde_macho_003"];
    $pasto_macho_003 = $_POST["select_pasto_macho_003"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_macho_003;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_macho_003'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_macho_003"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $id_categoria = $arrayCategorias[2]['id'];
        $idade_de = $arrayCategorias[2]['idade_de'];
        $idade_ate = $arrayCategorias[2]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_macho_003!=0 && $sexo=="M"){
            //if($categoria_pasto == $id_categoria && $qtde_macho_003 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_003 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_macho_003 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=3,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_003 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

if(isset($_POST["qtde_femea_003"]) && $_POST["qtde_femea_003"] != ''){
    $qtde_femea_003 = $_POST["qtde_femea_003"];
    $pasto_femea_003 = $_POST["select_pasto_femea_003"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_femea_003;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_femea_003'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_femea_003"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[2]['id'];
        $idade_de = $arrayCategorias[2]['idade_de'];
        $idade_ate = $arrayCategorias[2]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_femea_003!=0 && $sexo=="F"){
            //if($categoria_pasto == $id_categoria && $qtde_femea_003 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_003 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_femea_003 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=3,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_003 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

//categoria 004
if(isset($_POST["qtde_macho_004"]) && $_POST["qtde_macho_004"] != ''){
    $qtde_macho_004 = $_POST["qtde_macho_004"];
    $pasto_macho_004 = $_POST["select_pasto_macho_004"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_macho_004;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_macho_004'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_macho_004"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[3]['id'];
        $idade_de = $arrayCategorias[3]['idade_de'];
        $idade_ate = $arrayCategorias[3]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_macho_004!=0 && $sexo=="M"){
            //if($categoria_pasto == $id_categoria && $qtde_macho_004 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_004 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_macho_004 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=4,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_004 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

if(isset($_POST["qtde_femea_004"]) && $_POST["qtde_femea_004"] != ''){
    $qtde_femea_004 = $_POST["qtde_femea_004"];
    $pasto_femea_004 = $_POST["select_pasto_femea_004"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_femea_004;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $resultado = mysqli_query($conector, $query);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado) {
            $teve_erro = 'S';
            $mens.='Erro atualizar Lote Pasto Remover: ' . $erro_mysql . '</br>';
        }
    
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_femea_004'";
        $resultado = mysqli_query($conector, $query);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado) {
            $teve_erro = 'S';
            $mens.='Erro atualizar Lote Pasto: Femea 25 a 36 ' . $erro_mysql . '</br>';
        }
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_femea_004"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[3]['id'];
        $idade_de = $arrayCategorias[3]['idade_de'];
        $idade_ate = $arrayCategorias[3]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_femea_004!=0 && $sexo=="F"){
            //if($categoria_pasto == $id_categoria && $qtde_femea_004 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_004-=1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_femea_004 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=4,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_004 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

//categoria 005
if(isset($_POST["qtde_macho_005"]) && $_POST["qtde_macho_005"] != ''){
    $qtde_macho_005 = $_POST["qtde_macho_005"];
    $pasto_macho_005 = $_POST["select_pasto_macho_005"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_macho_005;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_macho_005'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_macho_005"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[4]['id'];
        $idade_de = $arrayCategorias[4]['idade_de'];
        $idade_ate = $arrayCategorias[4]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_macho_005!=0 && $sexo=="M"){
            //if($categoria_pasto == $id_categoria && $qtde_macho_005 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_005 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_macho_005 != 0 && $sexo == "M"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=5,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_macho_005 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
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
            WHERE tbl_pasto_id = $pasto_incluir_id";

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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

if(isset($_POST["qtde_femea_005"]) && $_POST["qtde_femea_005"] != ''){
    $qtde_femea_005 = $_POST["qtde_femea_005"];
    $pasto_femea_005 = $_POST["select_pasto_femea_005"];
    $pasto_remover_id = $_SESSION["pasto_id"];
    
    $total_animais_destino+=$qtde_femea_005;

    /*if ($opcao_descricao_lote==2) {
        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = null,
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_remover_id'";
        $request = mysqli_query($conector, $query);

        $query = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '$descricao_lote',
            tbl_pasto_alterado_em = '$data_sistema',
            tbl_pasto_alterado_por = '$nome_usuario'
            WHERE tbl_pasto_id = '$pasto_femea_005'";
        $request = mysqli_query($conector, $query);
    }*/

    $query = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_remover_id AND tbl_pasto_lixeira = 0";
    $request = mysqli_query($conector, $query);
    $pasto_remover = mysqli_fetch_object($request);

    $pasto_incluir_id = $_POST["select_pasto_femea_005"];

    // VERIFICA SE PASTO QUE SERÁ INCLUIDO ESTA VAZIO ANTES DE INCLUIR
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_incluir_id'");
    $num_rows_pasto_incluir = mysqli_num_rows($sql);

    $array_categoria = explode("!", $pasto_remover->tbl_pasto_array_categoria);
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

    //pegando info do nascimento dos animais no pasto
    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_remover_id AND tbl_animal_pasto_situacao = 'A'";
    $rs = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($rs)){
        //$categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $numero_item = $reg_animais->tbl_animal_pasto_numero_item;

        $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;
        
        $id_categoria = $arrayCategorias[4]['id'];
        $idade_de = $arrayCategorias[4]['idade_de'];
        $idade_ate = $arrayCategorias[4]['idade_ate'];

        if ($controle_estoque=='I') {
            if($meses>=$idade_de && $meses<=$idade_ate && $qtde_femea_005!=0 && $sexo=="F"){
            //if($categoria_pasto == $id_categoria && $qtde_femea_005 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_005 -= 1;
            }
        }
        else {
            if($meses >= $idade_de && $meses <= $idade_ate && $qtde_femea_005 != 0 && $sexo == "F"){
                $query = "UPDATE tbl_animal_pasto SET
                tbl_animal_pasto_id = $pasto_incluir_id,
                tbl_animal_pasto_categoria=5,
                tbl_animal_pasto_alterado_em = '$data_sistema',
                tbl_animal_pasto_alterado_por = '$nome_usuario'
                WHERE tbl_animal_pasto_id = $pasto_remover_id
                AND tbl_animal_pasto_numero_item = $numero_item
                AND tbl_animal_pasto_situacao = 'A'";

                $resultado = mysqli_query($conector, $query) or die(mysqli_error($conector));
                $qtde_femea_005 -= 1;
            }
        }
    }

    // AJUSTA DATAS COM E SEM ANIMAIS NO TBL_PASTO
    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_remover_id' AND  
              tbl_animal_pasto_situacao = 'A'");

    if (mysqli_num_rows($sql) == 0){
        //$dataSemAnimais = $pasto_remover->tbl_pasto_data_sem_animais;

        $data_com_remover = $pasto_remover->tbl_pasto_data_com_animais;
        $data_com_remover_anterior = $pasto_remover->tbl_pasto_data_com_animais_anterior;
        $data_sem_remover = $pasto_remover->tbl_pasto_data_sem_animais;
        $data_sem_remover_anterior = $pasto_remover->tbl_pasto_data_sem_animais_anterior;

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
                WHERE tbl_pasto_id = $pasto_remover_id";

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
        WHERE tbl_pasto_id = '$pasto_incluir_id' AND 
              tbl_pasto_lixeira = 0");

    $reg_pasto_incluir = mysqli_fetch_object($tbl_pasto_entrar);

    $data_com_incluir = $reg_pasto_incluir->tbl_pasto_data_com_animais;
    $data_com_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_com_animais_anterior;
    $data_sem_incluir = $reg_pasto_incluir->tbl_pasto_data_sem_animais;
    $data_sem_incluir_anterior = $reg_pasto_incluir->tbl_pasto_data_sem_animais_anterior;

    if ($num_rows_pasto_incluir==0) {
        $dataAtual = new DateTime();
        $dataCom = new DateTime($data_com_incluir);
        $diff = $dataAtual->diff($dataCom);

        if ($diff->h + ($diff->days * 24) < 24){
            if ($num_rows_pasto_incluir==0) {
                $query = "UPDATE tbl_pasto SET
                    tbl_pasto_alterado_em = '$data_sistema',
                    tbl_pasto_alterado_por = '$nome_usuario',
                    tbl_pasto_data_com_animais = '$data_com_incluir_anterior',
                    tbl_pasto_data_com_animais_anterior = '$data_com_incluir_anterior',
                    tbl_pasto_data_sem_animais = '$data_com_incluir_anterior',
                    tbl_pasto_data_sem_animais_anterior = '$data_com_incluir_anterior'
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
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
                WHERE tbl_pasto_id = $pasto_incluir_id";

                $resultado = mysqli_query($conector, $query);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao atualizar as datas COM' . $erro_mysql));
                    exit;
                } 
            }
            else {
                if ($num_rows_pasto_incluir==0) {
                    $query = "UPDATE tbl_pasto SET
                        tbl_pasto_alterado_em = '$data_sistema',
                        tbl_pasto_alterado_por = '$nome_usuario',
                        tbl_pasto_data_com_animais = '$data_sistema',
                        tbl_pasto_data_com_animais_anterior = '$data_com_incluir'
                    WHERE tbl_pasto_id = $pasto_incluir_id";

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
}

// ALTERAR DESCRICAO DO LOTE DOS PASTOS

$item_pastos = 0;

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
}

// Pega a descrição do lote do pasto destino para ver se esta vazio
$sql = mysqli_query($conector, "SELECT * FROM tbl_pasto
    WHERE tbl_pasto_id = '$pasto_destino_referencia'");

$reg_pasto_incluir = mysqli_fetch_object($sql);
$descricao_lote_incluir = $reg_pasto_incluir->tbl_pasto_descricao_lote;
//------------------------------------------------------------------

$total_animais_origem = $_POST['total_animais'];
$descricao_lote_gravar = $_POST['descricao_lote_gravar'];
$id_lote = $_POST['id_lote'];
$ano_lote = $_POST['ano_lote'];
$descricao_lote_1 = $_POST['descricao_lote_1'];
$descricao_lote_2 = $_POST['descricao_lote_2'];
$descricao_lote_3 = $_POST['descricao_lote_3'];
$descricao_lote_4 = $_POST['descricao_lote_4'];
$descricao_lote_5 = $_POST['descricao_lote_5'];
$descricao_lote_6 = $_POST['descricao_lote_6'];

/* Premissa 1 - Levar todos os animais para outro pasto vazio:
                Mover a Descrição do Lote para o Pasto Destino
                Limpar a Descrição do Lote do Pasto Origem
                Mover a nutrição do dia para pasto Destino
*/

if ($total_animais_origem==$total_animais_destino &&
    $move_tudo=='S' && 
    $descricao_lote_incluir=='') {

    $query = "UPDATE tbl_pasto SET 
        tbl_pasto_descricao_lote = '$descricao_lote_gravar',
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
    WHERE tbl_pasto_id = '$pasto_destino_referencia'";

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
        WHERE tbl_pasto_id = '$pasto_remover_id'";

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
        tbl_nutricao_codigo_pasto = $pasto_destino_referencia
        WHERE tbl_nutricao_codigo_pasto = $pasto_remover_id AND 
              tbl_nutricao_data = '$data_atual'";

    $resultado = mysqli_query($conector, $query);
}
else if ($total_animais_origem==$total_animais_destino &&
    $move_tudo=='S') {
    /* Premissa 6 - Levar todos os animais para outro pasto com animais:
                    Manter a Descrição do Lote do Pasto Destino
                    Limpar a Descrição do Lote do Pasto Origem
                    Mover a nutrição do dia para pasto Destino
    */
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
        WHERE tbl_pasto_id = '$pasto_remover_id'";

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
        tbl_nutricao_codigo_pasto = $pasto_destino_referencia
        WHERE tbl_nutricao_codigo_pasto = $pasto_remover_id AND 
              tbl_nutricao_data = '$data_atual'";

    $resultado = mysqli_query($conector, $query);
}

if ($teve_erro=='S') {
    header('Content-type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao atualizar os animais no pasto' . $mens));
    exit;
} 
else {
    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Animais movidos com sucesso.'));
    exit;
}

?>