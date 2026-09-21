<?php
    include "conecta_mysql.inc";

    for ($i=1; $i<=21; $i++){
        $valor[$i]=0;
    }

    $local = $_POST['local'];

    // pega o codigo do pasto curral de saida do local

    $sql = mysqli_query($conector, "SELECT * FROM tbl_pasto
        WHERE tbl_pasto_codigo_local='$local' AND 
              tbl_pasto_modulo=999 AND 
              tbl_pasto_tipo_curral='S'"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0){
        $reg_pasto = mysqli_fetch_object($sql);
        $pasto = $reg_pasto->tbl_pasto_id;
    }
    else {
        $pasto = 0;
    }

    // popular array com animais do pasto de saida do local

    for ($i = 1; $i <=5; $i++) {
        $j = str_pad($i, 3, "0", STR_PAD_LEFT);
        $total_cat_macho[$j]=0;
        $total_cat_femea[$j]=0;
    }

    $sql = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_situacao='A' AND
              tbl_animal_pasto_local='$local' AND 
              tbl_animal_pasto_id='$pasto'"); 

    $num_rows = mysqli_num_rows($sql);

    if ($num_rows!=0){
        while ($reg_animal = mysqli_fetch_object($sql)){
            $sexo = $reg_animal->tbl_animal_pasto_sexo;
            
            $data_nascimento = $reg_animal->tbl_animal_pasto_nascimento;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');

            $idade_ano = $idade_acompanhamento->format('%Y');
            $idade_mes = $idade_acompanhamento->format('%m');

            if ($idade_ano==0 && $idade_mes!=0) {
                $idade_animal = $idade_mes . ' mes(es)';
            }
            else if ($idade_ano!=0 && $idade_mes==0){
                $idade_animal = $idade_ano . ' ano(s)';
            }
            else if ($idade_ano!=0 && $idade_mes!=0) {
                $idade_animal = $idade_ano . ' ano(s) e ' . $idade_mes . ' mes(es)';
            }
            else {
                $idade_animal = '';
            }

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

            if ($sexo=='M') {
                $total_cat_macho[$codigo_categoria]++;
            }
            else {
                $total_cat_femea[$codigo_categoria]++;
            }
        }
    }

    $valor[0] = $total_cat_macho['001'];
    $valor[1] = $total_cat_macho['002'];
    $valor[2] = $total_cat_macho['003'];
    $valor[3] = $total_cat_macho['004'];
    $valor[4] = $total_cat_macho['005'];
    $valor[5] = $total_cat_femea['001'];
    $valor[6] = $total_cat_femea['002'];
    $valor[7] = $total_cat_femea['003'];
    $valor[8] = $total_cat_femea['004'];
    $valor[9] = $total_cat_femea['005'];

    $str=$valor[0].'<|>'.$valor[1].'<|>'.$valor[2].'<|>'.$valor[3].'<|>'.
         $valor[4].'<|>'.$valor[5].'<|>'.$valor[6].'<|>'.$valor[7].'<|>'.
         $valor[8].'<|>'.$valor[9];
                                
    echo $str; 

    mysqli_close($conector);
            
?>


                
                
