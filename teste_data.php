<?php
    include "conecta_mysql.inc";

/*    $sql = "SELECT * from tbl_movimentacao 
            WHERE tbl_movimentacao_lixeira=0 AND 
            tbl_movimentacao_data >= '2024-04-01' AND tbl_movimentacao_data <= '2024-04-30'
            ORDER BY tbl_movimentacao_data DESC"; 

    $rs = mysqli_query($conector, $sql); 

    while ($reg_mov = mysqli_fetch_object($rs)){
        $data_movimentacao = new DateTime($reg_mov->tbl_movimentacao_data);
        $data_movimentacao_edi = $data_movimentacao->format('d/m/Y');

        $partes = explode("/", $data_movimentacao_edi);
        $mes = $partes[1];
        $ano = $partes[2];

        $anomes_mov = $ano.$mes;

        $data_sistema = date("Y-m-d");
        $partes = explode("-", $data_sistema);
        $mes = $partes[1];
        $ano = $partes[0];

        $anomes_sistema = $ano.$mes;

        if ($anomes_mov>=$anomes_sistema) {
       		echo 'Data Mov: ' . $anomes_mov . ' Data Sistema: ' . $anomes_sistema . '</br>';
       	}
   }
*/

    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_local=57 and 
        tbl_animal_pasto_numero_item=262");

    $num_rows = mysqli_num_rows($tbl_animais);  
    
    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            $data_nasc = $reg_animal->tbl_animal_pasto_nascimento;
            $sexo = $reg_animal->tbl_animal_pasto_sexo;
            
            $data_nascimento = $data_nasc;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            if ($idade<0) {
                $idade=1;
            }

            $tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                WHERE tab_registro_lixeira_categoria_idade='0'");

            $num_rows = mysqli_num_rows($tbl_categoria);    

            if ($num_rows!=0) {
                while ($reg_categoria = mysqli_fetch_object($tbl_categoria)) {
                    $idade_de = $reg_categoria->tab_categoria_idade_de;
                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

                    if ($idade >= $idade_de && $idade <= $idade_ate) {
                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

                    }
                }

            echo 'Idade: ' . $idade . ' Categoria: ' . $codigo_categoria . '</br>';   
            }
        }
    }

   echo 'Fim processamento';
?>