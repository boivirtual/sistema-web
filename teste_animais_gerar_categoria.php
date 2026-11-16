<?php
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

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        order by tbl_animal_pasto_local, tbl_animal_pasto_numero_item desc");

    $num_rows = mysqli_num_rows($tbl_animais);

    //echo 'Total animais: ' . $num_rows . '</br>';
    $listados = 0;

    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $local = $reg_animal->tbl_animal_pasto_local; 
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
                $listados++;

                //echo 'Local: ' . $local . ' Item: ' . $item . ' Idade: ' . $idade .' Nascimento: ' . $data_nascimento . ' Cat Antes e Depois: '. $categoria_pasto .' ' . $codigo_categoria . '</br>';

                $sql = "UPDATE tbl_animal_pasto SET 
                    tbl_animal_pasto_categoria='$codigo_categoria'
                    WHERE tbl_animal_pasto_local='$local' AND 
                          tbl_animal_pasto_numero_item='$item'";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                /*if (!$resultado){
                    echo 'Erro ao gravar ' . $erro_mysql . '</br>';
                }*/ 
            }
		}
	}

    mysqli_close($conector);
    exit;
    //echo 'Fim do processamento. Alterados: ' . $listados;
?>
