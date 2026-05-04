<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animais");

    $num_rows = mysqli_num_rows($tbl_animais);

    echo 'Total animais: ' . $num_rows . '</br>';
    $listados = 0;

    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $id_animal = $reg_animal->tbl_animal_codigo_id;  
            $local = $reg_animal->tbl_animal_codigo_fazenda;  
            $data_nascimento = $reg_animal->tbl_animal_data_nascimento; 

            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            if ($idade>07) {
                $listados++;

                echo 'Local: ' . $local .' Idade: ' . $idade .' Nascimento: ' . $data_nascimento . '</br>';

                $sql = "UPDATE tbl_animais SET 
                               tbl_animal_data_nascimento='2021-04-19'
                               WHERE tbl_animal_codigo_id='$id_animal'";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    echo 'Erro ao gravar ' . $erro_mysql . '</br>';
                } 
            }
		}
	}

    echo 'Fim do processamento. Listados: ' . $listados;


?>
