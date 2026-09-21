<?php
    include "conecta_mysql.inc";
?>

<?php

    if(isset($_POST["identificador"]) && isset($_POST["codigo"]) && $_POST["codigo"] != '000000000'){
        $label = $_POST["identificador"];
        $codigo = $_POST["codigo"];
        $array_conta = [];

        if($label == 'SEMEM'){
            $sql = "SELECT * FROM tbl_semem WHERE tbl_semem_codigo_id = $codigo AND tbl_semem_lixeira = 0";
            $semem = mysqli_fetch_object(mysqli_query($conector, $sql));

            $sql = mysqli_query($conector, "SELECT * FROM tabela_racas 
                WHERE tab_codigo_raca = $semem->tbl_semem_codigo_raca AND 
                      tab_registro_lixeira_raca = 0");
            $num_rows = mysqli_num_rows($sql);

            if ($num_rows!=0) {
                $raca = mysqli_fetch_object($sql);
                $codigo_raca = $raca->tab_codigo_raca;
                $descricao_raca = $raca->tab_descricao_raca;
            }
            else {
                $codigo_raca = $semem->tbl_semem_codigo_raca;
                $descricao_raca = 'Sem raça';
            }

            array_push($array_conta, $descricao_raca);
            array_push($array_conta, $codigo_raca);
        }else{
            $sql = "SELECT * FROM tbl_animais WHERE tbl_animal_codigo_id = $codigo";
            $animal = mysqli_fetch_object(mysqli_query($conector, $sql));

            $sql = mysqli_query($conector, "SELECT * FROM tabela_racas 
                WHERE tab_codigo_raca = $animal->tbl_animal_codigo_raca AND 
                      tab_registro_lixeira_raca = 0");
            $num_rows = mysqli_num_rows($sql);

            if ($num_rows!=0) {
                $raca = mysqli_fetch_object($sql);
                $codigo_raca = $raca->tab_codigo_raca;
                $descricao_raca = $raca->tab_descricao_raca;
            }
            else {
                $codigo_raca = $semem->tbl_semem_codigo_raca;
                $descricao_raca = 'Sem raça';
            }

            array_push($array_conta, $descricao_raca);
            array_push($array_conta, $codigo_raca);
        }
        $array_string = implode("|", $array_conta);
        echo $array_string;
    }

?>