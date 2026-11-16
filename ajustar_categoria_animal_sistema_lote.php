<?php
    // Chamada pelo Dashboard.js

    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    @ session_start();
    $codigo_usuario = $_SESSION['id_usuario'];

    $tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
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

        $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_situacao='A' AND 
                  tbl_animal_pasto_local='$local'
            ORDER BY tbl_animal_pasto_nascimento DESC");

        $num_rows = mysqli_num_rows($tbl_animais);
        $vai_mudar = 'N';

        if ($num_rows!=0) {
            while ($reg_animais = mysqli_fetch_object($tbl_animais)) {
                $categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
                $data_nascimento = $reg_animais->tbl_animal_pasto_nascimento;  
                $data_acompanhamento_calculo = date("Y-m-d");
                $date = new DateTime($data_nascimento);
                $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

                $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                    WHERE tab_registro_lixeira_categoria_idade='0'");

                $num_rows_cat = mysqli_num_rows($categoria);    

                $codigo_categoria = 0;

                if ($num_rows_cat!=0) {
                    while ($reg_categoria = mysqli_fetch_object($categoria)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;
                        }                   
                    }
                }

                if ($categoria_pasto!=$codigo_categoria) {
                    $vai_mudar = 'S';
                }
            }

            if ($vai_mudar == 'S') {
                //echo 'Tem mudança para local ' . $local . '</br>';
                $muda_categorias = funcao_mudar_categorias($conector, $local);

                if ($muda_categorias!='') {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => $muda_categorias));
                }
                else {
                    header('Content-type: application/json');
                    echo json_encode(array('success' => true, 'message' => 'Fim do processamento'));
                }
            }
        }
    } // fim do for

    //echo 'Fim do processamento: ';


    function funcao_mudar_categorias($conector, $local) {
        // Guarda os valores dos pesos antes de iniciar as atualizações. 
        
        for ($i = 1; $i <=5; $i++) {
            $j = str_pad($i, 3, "0", STR_PAD_LEFT);
            $qtd_cat_macho[$j]=0;
            $media_cat_macho[$j]=0;
            $total_cat_macho[$j]=0;

            $qtd_cat_femea[$j]=0;
            $media_cat_femea[$j]=0;
            $total_cat_femea[$j]=0;
        }

        $media_categoria = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria 
            WHERE tbl_pm_local_id='$local'");

        $num_rows_media = mysqli_num_rows($media_categoria);

        if ($num_rows_media!=0){
            while ($reg_media = mysqli_fetch_object($media_categoria)){
                $codigo_categoria = $reg_media->tbl_pm_categoria_id;
                $sexo = $reg_media->tbl_pm_sexo;
                $qtd = $reg_media->tbl_pm_qtd_total_atual;
                $media = $reg_media->tbl_pm_peso_medio_atual;
                $total = $reg_media->tbl_pm_peso_total_atual;

                if ($sexo=='M') {
                    $qtd_cat_macho[$codigo_categoria]=$qtd;
                    $media_cat_macho[$codigo_categoria]=$media;
                    $total_cat_macho[$codigo_categoria]=$total;
                }
                else {
                    $qtd_cat_femea[$codigo_categoria]=$qtd;
                    $media_cat_femea[$codigo_categoria]=$media;
                    $total_cat_femea[$codigo_categoria]=$total;
                }
            }
        }

        /*var_dump($qtd_cat_macho);
        var_dump($media_cat_macho);
        var_dump($total_cat_macho);
        */

        // Fim guarda valores anteriores

        // Atualiza categorias
        $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
            WHERE tbl_animal_pasto_situacao='A' AND 
                  tbl_animal_pasto_local='$local'
            ORDER BY tbl_animal_pasto_nascimento DESC");

        $num_rows_animais = mysqli_num_rows($tbl_animais);

        $erro = '';

        if ($num_rows_animais!=0) {
            while ($reg_animais = mysqli_fetch_object($tbl_animais)) {
                $item = $reg_animais->tbl_animal_pasto_numero_item;  
                $categoria_pasto = $reg_animais->tbl_animal_pasto_categoria;
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

                $num_rows_cat = mysqli_num_rows($categoria);    
                $codigo_categoria_novo = 0;

                if ($num_rows_cat!=0) {
                    while ($reg_categoria = mysqli_fetch_object($categoria)) {
                        $idade_de = $reg_categoria->tab_categoria_idade_de;
                        $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                        if ($idade >= $idade_de && $idade <= $idade_ate) {
                            $codigo_categoria_novo = $reg_categoria->tab_codigo_categoria_idade;
                        }                   
                    }
                }

                if ($codigo_categoria_novo!=$categoria_pasto) {

                    // Atualiza pesos e quantidade na tabela tbl_peso_medio_categoria com a categoria que esta saindo

                    $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                        WHERE tbl_pm_local_id='$local' AND 
                              tbl_pm_categoria_id='$categoria_pasto' AND 
                              tbl_pm_sexo='$sexo'");

                    $num_rows_media = mysqli_num_rows($tbl_media);

                    if ($num_rows_media!=0){
                        $reg_media = mysqli_fetch_object($tbl_media);
                        $id_media = $reg_media->tbl_pm_id;
                        $qtd_anterior = $reg_media->tbl_pm_qtd_total_atual;
                        $peso_anterior = $reg_media->tbl_pm_peso_total_atual;

                        if ($sexo=='M') {
                            $peso_medio_anterior = 
                            $media_cat_macho[$categoria_pasto];
                        }
                        else {
                            $peso_medio_anterior = 
                            $media_cat_femea[$categoria_pasto];
                        }

                        // Calcula a media atual e grava no banco de dados
                        $peso_medio_atual = ($peso_anterior - $peso_medio_anterior) /
                                            ($qtd_anterior - 1);
                        $qtd_animais_atual = $qtd_anterior - 1;
                        $peso_total_atual = $peso_anterior - $peso_medio_anterior;

                        $sql = ("UPDATE tbl_peso_medio_categoria  SET 
                                    tbl_pm_qtd_total_atual='$qtd_animais_atual',
                                    tbl_pm_peso_medio_atual='$peso_medio_atual',
                                    tbl_pm_peso_total_atual='$peso_total_atual'
                              WHERE tbl_pm_id ='$id_media'");

                        $resultado = mysqli_query($conector,$sql);
                        $erro_mysql = mysqli_error($conector);

                        if (!$resultado){
                            $erro = 'Erro ao atualizar a categoria velha ' . $erro_mysql . '</br>';
                        } 
                    }

                    // Atualiza pesos e quantidade na tabela tbl_peso_medio_categoria com a categoria que esta entrando

                    $tbl_media = mysqli_query($conector, "SELECT * FROM tbl_peso_medio_categoria
                        WHERE tbl_pm_local_id='$local' AND 
                              tbl_pm_categoria_id='$codigo_categoria_novo' AND 
                              tbl_pm_sexo='$sexo'");

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
                    
                    if ($sexo=='M') {
                        $peso_medio_anterior=
                        $media_cat_macho[$categoria_pasto];
                    }
                    else {
                        $peso_medio_anterior=
                        $media_cat_femea[$categoria_pasto];
                        }

                    // Calcula a media atual e grava no banco de dados
                    $peso_medio_atual = ($peso_anterior + $peso_medio_anterior) /
                             ($qtd_anterior + 1);

                    $qtd_animais_atual = $qtd_anterior + 1;
                    $peso_total_atual = $peso_anterior + $peso_medio_anterior;

                    if ($num_rows_media==0) {
                        $data_hoje = date("Y/m/d");

                        $sql = "INSERT INTO tbl_peso_medio_categoria (
                            tbl_pm_categoria_id,
                            tbl_pm_sexo,
                            tbl_pm_local_id,
                            tbl_pm_data,
                            tbl_pm_qtd_total_atual,
                            tbl_pm_peso_medio_atual,
                            tbl_pm_peso_total_atual
                            ) VALUES (
                            '$codigo_categoria_novo',
                            '$sexo',
                            '$local',
                            '$data_hoje',
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
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                      $erro = 'Erro ao gravar nova categoria' . $erro_mysql . '</br>';
                    } 

                    // Atualiza categoria no tbl_animal_pasto
                    $sql = "UPDATE tbl_animal_pasto SET 
                        tbl_animal_pasto_categoria='$codigo_categoria_novo'
                        WHERE tbl_animal_pasto_local='$local' and 
                              tbl_animal_pasto_numero_item='$item'";

                    $resultado = mysqli_query($conector,$sql);
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                      $erro = 'Erro ao gravar atualizar animais no pasto' . $erro_mysql . '</br>';
                    } 
                } // Fim do if ($codigo_categoria_novo!=$categoria_pasto)
            } // Fim do while ($reg_animais)
        } // Fim do if ($num_rows_animais!=0)

        return $erro;
    }


?> 
