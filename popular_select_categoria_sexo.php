<?php
    include "conecta_mysql.inc";

    $pasto_origem = $_POST["pasto_origem"];

    $arrayMacho = [
        '001' => 0,
        '002' => 0,
        '003' => 0,
        '004' => 0,
        '005' => 0
    ];
    $arrayFemea = [
        '001' => 0,
        '002' => 0,
        '003' => 0,
        '004' => 0,
        '005' => 0
    ];

    echo '<option value="000000000">'.htmlentities('Qual Categoria').'</option>';

    $tbl_animais_pasto = mysqli_query($conector,"SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id = '$pasto_origem' AND 
              tbl_animal_pasto_situacao = 'A'");
    
    $num_rows_animais = mysqli_num_rows($tbl_animais_pasto);

    if ($num_rows_animais > 0){
        while ($reg_animais = mysqli_fetch_object($tbl_animais_pasto)) {
            $sexo = $reg_animais->tbl_animal_pasto_sexo;
            $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); 
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && 
                        $idade <= $idade_ate && 
                        $sexo == "F"){
                        $arrayFemea[$codigo_categoria]+= 1;
                    }
                    else if($idade >= $idade_de && 
                            $idade <= $idade_ate && 
                            $sexo == "M"){
                        $arrayMacho[$codigo_categoria]+= 1;
                    }
                }
            }                   
        } 
    } 

    $codigo_categoria=1;
    $codigo_categoria = str_pad($codigo_categoria, 3, "0", STR_PAD_LEFT);

    $total_bezerros = $arrayFemea[$codigo_categoria];
    $total_bezerros+= $arrayMacho[$codigo_categoria];

    if ($total_bezerros>0) {
        $desc_categoria = '00 a 07 meses';
        echo '<option value="'.$codigo_categoria.$total_bezerros.'">'.$desc_categoria.'</option>';
    }

    // Machos
    for ($j=2; $j <=5; $j++) { 
        $codigo_categoria=str_pad($j, 3, "0", STR_PAD_LEFT);

        switch ($codigo_categoria) {
            case '002':
                $desc_categoria= '08 a 12 meses';
                break;
            case '003':
                $desc_categoria= '13 a 24 meses';
                break;
            case '004':
                $desc_categoria= '25 a 36 meses';
                break;
            case '005':
                $desc_categoria= '> 36 meses';
                break;
        }

        if ($arrayMacho[$codigo_categoria]>0) {
            echo '<option value="'.'M'.$codigo_categoria.$arrayMacho[$codigo_categoria].'">'.$desc_categoria.' - Macho'.'</option>';
        }
    }

    // Femeas
    for ($j=2; $j <=5; $j++) { 
        $codigo_categoria=str_pad($j, 3, "0", STR_PAD_LEFT);

        switch ($codigo_categoria) {
            case '002':
                $desc_categoria= '08 a 12 meses';
                break;
            case '003':
                $desc_categoria= '13 a 24 meses';
                break;
            case '004':
                $desc_categoria= '25 a 36 meses';
                break;
            case '005':
                $desc_categoria= '> 36 meses';
                break;
        }

        if ($arrayFemea[$codigo_categoria]>0) {
            echo '<option value="'.'F'.$codigo_categoria.$arrayFemea[$codigo_categoria].'">'.$desc_categoria.' - Fêmea'.'</option>';
        }
    }

?>