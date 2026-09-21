<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $local = $_POST['local'];
    $nascimento_anterior = $_POST['nascimento_anterior'];
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];

    $codigo_categoria = 0;

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_situacao = 'A' AND 
                  tbl_animal_pasto_local = '$local' AND 
                  tbl_animal_pasto_nascimento = '$nascimento_anterior' AND 
                  tbl_animal_pasto_sexo = '$sexo'");

    $num_rows = mysqli_num_rows($tbl_animais);

    if ($num_rows!=0) {
        $reg_animal = mysqli_fetch_object($tbl_animais);
        $pasto = $reg_animal->tbl_animal_pasto_id;
        $item = $reg_animal->tbl_animal_pasto_numero_item;

        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
            WHERE tab_registro_lixeira_categoria_idade='0'");

        $num_rows = mysqli_num_rows($categoria);    

        if ($num_rows!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade >= $idade_de && $idade <= $idade_ate) {
                    $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                }
            }                   
        }

        $sql="UPDATE tbl_animal_pasto SET 
                     tbl_animal_pasto_categoria='$codigo_categoria', 
                     tbl_animal_pasto_nascimento='$data_nascimento' 
               WHERE tbl_animal_pasto_local='$local' and 
                     tbl_animal_pasto_numero_item='$item' and 
                     tbl_animal_pasto_id='$pasto'";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            $valor[0] = 1;
            $valor[1] = 'Erro ao atualizar a categoria no pasto ' . $erro_mysql;
            $str=$valor[0] . '<|>' . $valor[1] . '<|>';
        } 
        else {
            $valor[0] = 0;
            $valor[1] = '';
            $str=$valor[0] . '<|>' . $valor[1] . '<|>';
        }        
        
    }
    else {
        $valor[0] = 1;
        $valor[1] = 'Não foi encontrado no pasto o animal com a data de nascimento ' . $nascimento_anterior;
        $str=$valor[0] . '<|>' . $valor[1] . '<|>';
    }

    echo $str; 
    mysqli_close($conector);



?> 
