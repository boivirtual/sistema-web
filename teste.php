<?php
include "conecta_mysql.inc";

for ($i = 1; $i <=5; $i++) {
    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
    $total_cat_macho[$j]=0;
    $total_cat_femea[$j]=0;
}

$local = 56;
$sexo = 'M';
$pasto = 47;

$sql = "SELECT * FROM tbl_animais 
    WHERE tbl_animal_lixeira=0 AND 
          tbl_animal_ativo='S' AND
          tbl_animal_codigo_fazenda='$local'"; 

$rs = mysqli_query($conector, $sql); 

while ($reg_animal = mysqli_fetch_object($rs)){
    $sexo = $reg_animal->tbl_animal_sexo;

    $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
    $data_acompanhamento_calculo = date("Y-m-d");
    $date = new DateTime($data_nascimento); // Data de Nascimento
    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
    $idade_ano = $idade_acompanhamento->format('%Y');
    $idade_mes = $idade_acompanhamento->format('%m');
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

                if ($sexo=='M') {
                    $total_cat_macho[$codigo_categoria]++;
                }
                else {
                    $total_cat_femea[$codigo_categoria]++;
                }
            }
        }
    }                   
}

echo 'MACHOS </br>';
var_dump($total_cat_macho);
echo '</br> FEMEAS';
var_dump($total_cat_femea);

// animais pasto
for ($i = 1; $i <=5; $i++) {
    $j = str_pad($i, 3, "0", STR_PAD_LEFT);
    $total_cat_macho[$j]=0;
    $total_cat_femea[$j]=0;
}

$sql = "SELECT * FROM tbl_animal_pasto
    WHERE tbl_animal_pasto_situacao='A' AND
          tbl_animal_pasto_local='$local' AND 
          tbl_animal_pasto_id='$pasto'"; 

$rs = mysqli_query($conector, $sql); 

while ($reg_animal = mysqli_fetch_object($rs)){
    $sexo = $reg_animal->tbl_animal_pasto_sexo;
    $codigo_categoria = $reg_animal->tbl_animal_pasto_categoria;

    if ($sexo=='M') {
        $total_cat_macho[$codigo_categoria]++;
    }
    else {
        $total_cat_femea[$codigo_categoria]++;
    }
}

echo '</br></br> ANIMAIS NO PASTO </br>';

echo 'MACHOS </br>';
var_dump($total_cat_macho);
echo '</br> FEMEAS';
var_dump($total_cat_femea);

?>
