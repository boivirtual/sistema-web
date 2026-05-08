<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);
$pasto = $_POST['pasto'];

$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_id='$pasto' AND 
              tbl_animal_pasto_local ='$local' AND 
              tbl_animal_pasto_situacao='A'");

$num_rows_pasto = mysqli_num_rows($tbl_pasto);
$tem_animais = '';

for ($i=0; $i<=5; $i++) { 
    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
    $registro_m[$j]=0;
    $registro_f[$j]=0;
    $descricao_cat[$j]='';   
}

if($num_rows_pasto != 0){
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)){
        $tem_animais = 'S';
        $sexo = $reg_pasto->tbl_animal_pasto_sexo;
        $data_nascimento = $reg_pasto->tbl_animal_pasto_nascimento;

        /*$array_categoria = explode("!", $reg_pasto->tbl_pasto_array_categoria);
        $array_qtd_macho = explode("!", $reg_pasto->tbl_pasto_array_qtd_animais_macho);
        $array_qtd_femea = explode("!", $reg_pasto->tbl_pasto_array_qtd_animais_femea);

        for ($j=0; $j<5; $j++) { 
            if ($array_qtd_macho[$j]!='' && $array_qtd_macho[$j]!=0) {
                $tem_animais = 'S';
            }

            if ($array_qtd_femea[$j]!='' && $array_qtd_femea[$j]!=0) {
                $tem_animais = 'S';
            }
        }*/


        //$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

        /*$data_inicial = $data_nascimento;
        $data_final = date("Y-m-d");
        $diferenca = strtotime($data_final) - strtotime($data_inicial);
        $idade = floor($diferenca / (60 * 60 * 24 * 30));
        $idade = str_pad($idade, 2, "0", STR_PAD_LEFT);*/

        $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");
        $num_rows_cat = mysqli_num_rows($categoria);    

        if ($num_rows_cat!=0) {
            while ($reg_categoria = mysqli_fetch_object($categoria)) {
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade_ate==999999999){
                    $desc_categoria='> 36 meses';
                }
                else {
                    $desc_categoria=$idade_de . ' a ' . $idade_ate . ' meses';
                }

                if ($idade >= $idade_de && $idade <= $idade_ate) {
                    $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
                    $descricao_cat[$id_categoria]=$desc_categoria;

                    if ($sexo=='M') {
                        $registro_m[$id_categoria]++;
                    }
                    else {
                        $registro_f[$id_categoria]++;
                    }
                }
            }
        }   

        /*for ($j=0; $j<5; $j++) { 
            $categoria = $array_categoria[$j];

            $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                                       WHERE tab_codigo_categoria_idade='$categoria' AND  tab_registro_lixeira_categoria_idade='0'");
            $num_rows = mysqli_num_rows($tbl_categoria);    

            if ($num_rows!=0) {
                $reg_categoria = mysqli_fetch_object($tbl_categoria);
                $idade_de = $reg_categoria->tab_categoria_idade_de;
                $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                if ($idade_ate==999999999){
                    $desc_categoria='> 36 meses';
                }
                else {
                    $desc_categoria=$idade_de . ' a ' . $idade_ate . ' meses';
                }
            }                   

            $descricao_cat[$j]=$desc_categoria;

            if ($array_qtd_macho[$j]!='' && $array_qtd_macho[$j]!=0) {
                $qtd_macho = intval($array_qtd_macho[$j]);
                $registro_m[$j]+=$qtd_macho;
            }

            if ($array_qtd_femea[$j]!='' && $array_qtd_femea[$j]!=0) {
                $qtd_femea = intval($array_qtd_femea[$j]);
                $registro_f[$j]+=$qtd_femea;
            }
        }*/
    }
}

if ($tem_animais=='') {
    echo 'N';
}
else {
    echo  '<option value="000">'.htmlentities('...').'</option>';

    for ($i=0; $i<=5; $i++) { 
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);

        if ($registro_m[$j]!=0) {
            //$categoria = $array_categoria[$j];
            echo '<option value="'.'M'.$j.$registro_m[$j].'">' .$descricao_cat[$j]. ' - Macho </option>';
        }

        if ($registro_f[$j]!=0) {
            //$categoria = $array_categoria[$j];
            echo '<option value="'.'F'.$j.$registro_f[$j].'">'.$descricao_cat[$j]. ' - Fêmea </option>';
        }
    }    
}

mysqli_close($conector);

?>
