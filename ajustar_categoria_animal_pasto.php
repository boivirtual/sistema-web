<?php
    // Chamada pelo Dashboard.js

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $desc_categoria = [];
    $arrayCategorias = [];
    $descricaoCategorias = [];

    $sql = "SELECT * FROM tabela_categoria_idade 
        WHERE tab_registro_lixeira_categoria_idade='0'"; 
            
    $rs = mysqli_query($conector,$sql); 

    while ($fila = mysqli_fetch_object($rs)){
        $codigo_id = $fila->tab_codigo_categoria_idade;
        $idade_de = $fila->tab_categoria_idade_de;
        $idade_ate = $fila->tab_categoria_idade_ate;

        if ($idade_ate==999999999){
            array_push($desc_categoria, '> 36 m');
                $descricaoCategorias = [
                    "id" => $codigo_id,
                    "idade_de" => $idade_de,
                    "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
        else {
            array_push($desc_categoria, $idade_de . ' a ' . $idade_ate . ' m');
            $descricaoCategorias = [
                "id" => $codigo_id,
                "idade_de" => $idade_de,
                "idade_ate" => $idade_ate
            ];
            array_push($arrayCategorias, $descricaoCategorias);
        }
    }

    @ session_start();
    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario 
        WHERE id_usuario = '$codigo_usuario' AND
              lixeira_usuario=0 ";  
    $query = mysqli_query($conector_acesso, $tbl_usuario);

    $num_rows_usuario = mysqli_num_rows($query);

    if ($num_rows_usuario!=0){
        $reg_usuario = mysqli_fetch_assoc($query);

        $array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
        $qtd_locais_usuario = count($array_locais_usuario);

        if ($qtd_locais_usuario==0) {
            $array_locais_usuario='';
        }
    }
    else {
        $array_locais_usuario='';
    }

    foreach ($array_locais_usuario as $value) {
        $local = rtrim($value);

        $tbl_pessoa = mysqli_query($conector, "SELECT * FROM tbl_pessoa
            WHERE tbl_pessoa_id = '$local' AND 
                  tbl_pessoa_lixeira='0'");
        $num_rows_pessoa = mysqli_num_rows($tbl_pessoa);    

        if ($num_rows_pessoa!=0) {
            $reg_pessoa = mysqli_fetch_object($tbl_pessoa);
            $nome_pessoa = $reg_pessoa->tbl_pessoa_nome;
        }
        else {
            $nome_pessoa = 'Não cadastrada';
        }

        $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
            WHERE tbl_animal_ativo='S' AND 
                  tbl_animal_lixeira=0 AND 
                  tbl_animal_codigo_fazenda = '$local'");

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_situacao='A' AND 
                  tbl_animal_pasto_local = '$local'");

        $num_rows_pasto = mysqli_num_rows($tbl_animais);

        if ($num_rows_pasto != $num_rows_animais) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Qtde de animais no pasto está diferente dos animais cadastrados. Ajuste os animais no pasto para a Fazenda: ' . $nome_pessoa));
            mysqli_close($conector);
            exit;
        }

        if ($num_rows_pasto!=0) {
            while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                //$local = $reg_animal->tbl_animal_pasto_local; 
                $item = $reg_animal->tbl_animal_pasto_numero_item;
                $categoria_pasto = $reg_animal->tbl_animal_pasto_categoria;
                $data_nascimento = $reg_animal->tbl_animal_pasto_nascimento; 

                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento); // Data de Nascimento
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                for($i = 0; $i < count($arrayCategorias); $i++){
                    $id_categoria = $arrayCategorias[$i]['id'];
                    $idade_de = $arrayCategorias[$i]['idade_de'];
                    $idade_ate = $arrayCategorias[$i]['idade_ate'];

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $id_categoria;
                    }
                }                        

                if ($codigo_categoria!=$categoria_pasto) {
                    $sql = "UPDATE tbl_animal_pasto SET 
                        tbl_animal_pasto_categoria='$codigo_categoria'
                        WHERE tbl_animal_pasto_local='$local' AND 
                              tbl_animal_pasto_numero_item='$item'";

                    $resultado = mysqli_query($conector,$sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado) {
                        header('Content-type: application/json');
                        echo json_encode(array('error' => true, 'message' => 'Erro ao atualizar a categoria ' . $erro_mysql));
                        mysqli_close($conector);
                        exit;

                    }
                }
            }
        }

        /*do {
            $tem_registros = 'N';
            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");
            $num_rows_cat = mysqli_num_rows($categoria);    

            if ($num_rows_cat!=0) {
                while ($reg_categoria = mysqli_fetch_object($categoria)) {
                    $id_categoria = $reg_categoria->tab_codigo_categoria_idade;

                    $qtd_animais_macho[$id_categoria] = 0;
                    $qtd_animais_macho_pasto[$id_categoria] = 0;

                    $qtd_animais_femea[$id_categoria] = 0;
                    $qtd_animais_femea_pasto[$id_categoria] = 0;
                }
            }

            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais
                WHERE tbl_animal_ativo='S' AND 
                      tbl_animal_lixeira=0 AND 
                      tbl_animal_codigo_fazenda = '$local'");

            $num_rows_animais = mysqli_num_rows($tbl_animais);

            if ($num_rows_animais!=0) {
                while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                    $sexo_animal = $reg_animal->tbl_animal_sexo;  
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

                                if ($sexo_animal=='M') {
                                    $qtd_animais_macho[$codigo_categoria]++;
                                }
                                else {
                                    $qtd_animais_femea[$codigo_categoria]++;
                                }
                            }
                        }                   
                    }
                }
            }

            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                    WHERE tbl_animal_pasto_situacao='A' AND 
                          tbl_animal_pasto_local = '$local'");

            $num_rows_pasto = mysqli_num_rows($tbl_animais);

            if ($num_rows_pasto != $num_rows_animais) {
                header('Content-type: application/json');
                echo json_encode(array('error' => true, 'message' => 'Qtde de animais no pasto está diferente dos animais cadastrados. Ajuste os animais no pasto para a Fazenda: ' . $nome_pessoa));
                mysqli_close($conector);
                exit;
            }

            if ($num_rows_pasto!=0) {
                while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
                    $codigo_categoria = $reg_animal->tbl_animal_pasto_categoria;
                    $sexo = $reg_animal->tbl_animal_pasto_sexo;

                    if ($sexo=='M') {
                        $qtd_animais_macho_pasto[$codigo_categoria]++;
                    }
                    else {
                        $qtd_animais_femea_pasto[$codigo_categoria]++;
                    }
                }
            }

            for ($i=1; $i <=5 ; $i++) { 
                $categoria_ajustar = str_pad($i, 3, "0", STR_PAD_LEFT);

                if ($qtd_animais_macho[$categoria_ajustar]!=$qtd_animais_macho_pasto[$categoria_ajustar]) {
                    $qtd_ajustar = $qtd_animais_macho[$categoria_ajustar] - 
                                   $qtd_animais_macho_pasto[$categoria_ajustar];

                    $sexo = 'M';
                    $tem_registros = 'S';

                    $categoria_ajustada = ajustar_categoria($categoria_ajustar, $local, $qtd_ajustar, $sexo, $conector);

                    if ($qtd_ajustar<0) {
                        $tem_registros = 'N';
                    }
                }
            }

            for ($i=1; $i <=5 ; $i++) { 
                $categoria_ajustar = str_pad($i, 3, "0", STR_PAD_LEFT);

                if ($qtd_animais_femea[$categoria_ajustar]!=$qtd_animais_femea_pasto[$categoria_ajustar]) {
                    $qtd_ajustar = $qtd_animais_femea[$categoria_ajustar] - 
                                   $qtd_animais_femea_pasto[$categoria_ajustar];

                    $sexo = 'F';
                    $tem_registros = 'S';

                    $categoria_ajustada = ajustar_categoria($categoria_ajustar, $local, $qtd_ajustar, $sexo, $conector);

                    if ($qtd_ajustar<0) {
                        $tem_registros = 'N';
                    }

                }
            }

        } while ($tem_registros=='S');*/
    }

    header('Content-type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Fim do processamento'));

    mysqli_close($conector);
    exit;

/*function ajustar_categoria($categoria_ajustar, $local, $qtd_ajustar, $sexo, $conector){

    if ($qtd_ajustar<0) {
        $nova_categoria = $categoria_ajustar + 1;
        $qtd_ajustar = abs($qtd_ajustar);

        if ($nova_categoria>5) {
            $nova_categoria=5;
        }

        for ($j=1; $j <= $qtd_ajustar; $j++) { 

            $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
                WHERE tbl_animal_pasto_situacao='A' AND 
                      tbl_animal_pasto_local = '$local' AND 
                      tbl_animal_pasto_categoria = '$categoria_ajustar' AND 
                      tbl_animal_pasto_sexo = '$sexo'");

            $num_rows = mysqli_num_rows($tbl_animais);

            if ($num_rows!=0) {
                $reg_animal = mysqli_fetch_object($tbl_animais);
                $pasto = $reg_animal->tbl_animal_pasto_local;
                $item = $reg_animal->tbl_animal_pasto_numero_item;

                $sql = "UPDATE tbl_animal_pasto SET 
                               tbl_animal_pasto_categoria='$nova_categoria'
                               WHERE tbl_animal_pasto_local='$local' AND 
                                     tbl_animal_pasto_numero_item='$item' AND 
                                     tbl_animal_pasto_local='$pasto'";
                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    echo 'Erro ao gravar ' . $erro_mysql . '</br>';
                } 
                else {
                    echo $local . ' ' . $item . '</br>';
                }
            }
        }
    }
}*/

?> 
