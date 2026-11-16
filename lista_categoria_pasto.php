<?php
include "conecta_mysql.inc";

$local = ltrim($_POST['local']);
$pasto = $_POST['pasto'];
$categoria = $_POST['categoria'];
$sexo = $_POST['sexo'];
$wcategoria = "";

if (isset($_POST["data_nasc_inicial"])) {
    $data_nasc_inicial = $_POST["data_nasc_inicial"];
}
else {
    $data_nasc_inicial = 0;
}

if (isset($_POST["data_nasc_final"])) {
    $data_nasc_final = $_POST["data_nasc_final"];
}
else {
    $data_nasc_final = 0;
}

if ($pasto=='999999999') {
    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
        INNER JOIN tbl_pasto
                ON tbl_pasto_id = tbl_animal_pasto_id
             WHERE tbl_animal_pasto_local ='$local' AND 
                   tbl_animal_pasto_situacao = 'A' AND
                   tbl_pasto_tipo_curral='S'");  
}
else {
    $wpasto = "";
    if (isset($_POST['pasto'])) {
        $pasto = $_POST['pasto'];

        if(in_array("", $pasto)) {
            $wpasto='';
        }
        else {
            $wpasto = " AND tbl_animal_pasto_id IN(";
            $wpasto.= implode(',', $pasto);
            $wpasto.= ")";
            }
    }
    else {
        $wpasto='';
    }

    $wraca = "";
    if (isset($_POST['raca'])) {
        $raca = $_POST['raca'];

        if(in_array("", $raca)) {
            $wraca='';
        }
        else {
            $wraca = " AND tbl_animal_pasto_raca IN(";
            $wraca.= implode(',', $raca);
            $wraca.= ")";
            }
    }
    else {
        $wraca='';
    }

    $wcategoria = "";
    if (isset($_POST['categoria'])) {
        $categoria_filtro = $_POST['categoria'];

        if(in_array("", $categoria_filtro)) {
            $wcategoria='';
        }
        else {
            //$wcategoria= explode(',', $categoria_filtro);
            $wcategoria=$categoria_filtro;
       }
    }
    else {
        $wcategoria='';
    }

    if ($data_nasc_inicial==0 && $data_nasc_final==0){
        $wdata_nasc = '';
    }
    else {
        $wdata_nasc = " AND tbl_animal_pasto_nascimento >= '$data_nasc_inicial' AND tbl_animal_pasto_nascimento <= '$data_nasc_final'";
    }

    if ($sexo=='T') {
        $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
            INNER JOIN tbl_pasto
                    ON tbl_pasto_id = tbl_animal_pasto_id
                 WHERE tbl_animal_pasto_local ='$local' AND 
                       tbl_animal_pasto_situacao = 'A'" . $wpasto . $wraca. $wdata_nasc);  
    }
    else {
        $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto 
            INNER JOIN tbl_pasto
                    ON tbl_pasto_id = tbl_animal_pasto_id
                 WHERE tbl_animal_pasto_local ='$local' AND 
                       tbl_animal_pasto_situacao = 'A' AND 
                       tbl_animal_pasto_sexo='$sexo'" . $wpasto . $wraca . $wdata_nasc);  
    }
}

$num_rows_pasto = mysqli_num_rows($tbl_pasto);
$tem_animais = '';

for ($i=0; $i<=5; $i++) { 
    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
    $registro_m[$j]=0;
    $registro_f[$j]=0;
    $descricao_cat[$j]='';
}

echo  '<option value="000">'.htmlentities('...').'</option>';

if($num_rows_pasto != 0){
    while ($reg_pasto = mysqli_fetch_object($tbl_pasto)){
        $sexo = $reg_pasto->tbl_animal_pasto_sexo;
        $data_nascimento = $reg_pasto->tbl_animal_pasto_nascimento;
        $tem_animais = 'S';

        $data_acompanhamento_calculo = date("Y-m-d");
        $date = new DateTime($data_nascimento); // Data de Nascimento
        $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
        $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
        $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
        $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

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

                    if ($wcategoria=="") {
                        $descricao_cat[$id_categoria]=$desc_categoria;

                        if ($sexo=='M') {
                            $registro_m[$id_categoria]++;
                        }
                        else {
                            $registro_f[$id_categoria]++;
                        }
                    }
                    else {
                        foreach ($wcategoria as $value) {
                            if ($value==$id_categoria) {
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
                }
            }
        }   
    }
}

if ($tem_animais=='') {
    echo 'N';
}
else {
    for ($i=0; $i<=5; $i++) { 
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);

        if ($registro_m[$j]!=0) {
            echo '<option value="'.'M'.$j.$registro_m[$j].'">' .$descricao_cat[$j]. ' - Macho </option>';
        }

        if ($registro_f[$j]!=0) {
            echo '<option value="'.'F'.$j.$registro_f[$j].'">'.$descricao_cat[$j]. ' - Fêmea </option>';
        }
    }    
}

mysqli_close($conector);

?>
