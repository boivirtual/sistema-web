<?php

include "valida_sessao.inc";
include "conecta_mysql.inc";

if(isset($_POST["idPasto"])){
    $pasto_id = $_POST["idPasto"];

    $sql = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_id";
    $rs = mysqli_query($conector, $sql);
    $ac = [];

    while($reg_pasto = mysqli_fetch_object($rs)){
        $array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
        $arrayCategorias = [];
        
        for($i = 0; $i < count($array_categoria); $i++){
            $codigo_categoria = $array_categoria[$i];
        
            $ssql = "SELECT * FROM tabela_categoria_idade 
            WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
                    tab_registro_lixeira_categoria_idade='0'"; 
            
            $query = mysqli_query($conector,$ssql); 
            $fila = mysqli_fetch_object($query);
        
            $codigo_id = $fila->tab_codigo_categoria_idade ;
            $idade_de = $fila->tab_categoria_idade_de;
            $idade_ate = $fila->tab_categoria_idade_ate;
            array_push($ac, $idade_de);
            array_push($ac, $idade_ate);
        
            if ($idade_ate==999999999){
                $descricaoCategorias = [
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
                ];
                array_push($arrayCategorias, $descricaoCategorias);
            }
            else {
                $descricaoCategorias = [
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
                ];
                array_push($arrayCategorias, $descricaoCategorias);
            }
        }
    }

    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_id AND tbl_animal_pasto_situacao = 'A' ORDER BY tbl_animal_pasto_nascimento DESC";
            $query = mysqli_query($conector, $sql);

    $arrayMeses = [];
    $arrayIdade = [];

    $str = 0;

    while($reg_animais = mysqli_fetch_object($query)){
        $nascimento = $reg_animais->tbl_animal_pasto_nascimento;

        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        /*$data_hoje = date("Y-m-d");
        $diferenca = strtotime($data_hoje) - strtotime($nascimento);
        $meses = floor($diferenca / (60 * 60 * 24 * 30));
        $meses = str_pad($meses, 2, "0", STR_PAD_LEFT);*/

        if(!in_array($meses, $arrayMeses)){
            array_push($arrayMeses, $meses);
            array_push($arrayIdade, 1);
        }else{
            $key = array_search($meses, $arrayMeses);
            $arrayIdade[$key] += 1;
        }
    }

    $array_string = array(
        implode("!", $ac),
        implode("!", $arrayMeses),
        implode("!", $arrayIdade)
    );
    $str = implode("|", $array_string);

    echo $str;
}elseif(isset($_POST["numMeses"])){
    $numMeses = $_POST["numMeses"];
    $pasto_id = $_POST["pastoID"];

    $sql = "SELECT * FROM tbl_pasto WHERE tbl_pasto_id = $pasto_id";
    $rs = mysqli_query($conector, $sql);
    $ac = [];

    while($reg_pasto = mysqli_fetch_object($rs)){
        $array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
        $arrayCategorias = [];
        
        for($i = 0; $i < count($array_categoria); $i++){
            $codigo_categoria = $array_categoria[$i];
        
            $ssql = "SELECT * FROM tabela_categoria_idade 
            WHERE tab_codigo_categoria_idade ='$codigo_categoria' and 
                    tab_registro_lixeira_categoria_idade='0'"; 
            
            $query = mysqli_query($conector,$ssql); 
            $fila = mysqli_fetch_object($query);
        
            $codigo_id = $fila->tab_codigo_categoria_idade ;
            $idade_de = $fila->tab_categoria_idade_de;
            $idade_ate = $fila->tab_categoria_idade_ate;
            array_push($ac, $idade_de);
            array_push($ac, $idade_ate);
        
            if ($idade_ate==999999999){
                $descricaoCategorias = [
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
                ];
                array_push($arrayCategorias, $descricaoCategorias);
            }
            else {
                $descricaoCategorias = [
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
                ];
                array_push($arrayCategorias, $descricaoCategorias);
            }
        }
    }

    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_id = $pasto_id AND tbl_animal_pasto_situacao = 'A'";
            $query = mysqli_query($conector, $sql);

    $arraySexo = [];
    $arrayIdade = [];
    $arrayID = [];

    while($reg_animais = mysqli_fetch_object($query)){
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $animalID = $reg_animais->tbl_animal_pasto_numero_item;

        $nascimento = $reg_animais->tbl_animal_pasto_nascimento;

        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $meses = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        /*$data_hoje = date("Y-m-d");
        $diferenca = strtotime($data_hoje) - strtotime($nascimento);
        $meses = floor($diferenca / (60 * 60 * 24 * 30));
        $meses = str_pad($meses, 2, "0", STR_PAD_LEFT);*/

        if($meses == $numMeses){
            array_push($arraySexo, $sexo);
            array_push($arrayIdade, date("d/m/Y", strtotime($nascimento)));
            array_push($arrayID, $animalID);
        }
    }

    $arrayString = array(
        implode("!", $arrayID),
        implode("!", $arrayIdade),
        implode("!", $arraySexo)
    );

    $str = implode("|", $arrayString);

    echo $str;
}elseif(isset($_POST["idAnimal"])){
    $idAnimal = $_POST["idAnimal"];
    $pasto_id = $_POST["pastoID"];

    $sql = "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_numero_item = $idAnimal AND tbl_animal_pasto_id = $pasto_id AND tbl_animal_pasto_situacao = 'A'";
    $query = mysqli_query($conector, $sql);

    while($reg_animais = mysqli_fetch_object($query)){
        $sexo = $reg_animais->tbl_animal_pasto_sexo;
        $nascimento = $reg_animais->tbl_animal_pasto_nascimento;
    }

    $array_conta = [];
    array_push($array_conta, $sexo);
    array_push($array_conta, date("Y-m-d", strtotime($nascimento)));

    echo implode("|", $array_conta);
}

?>