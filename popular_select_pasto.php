<?php
    include "conecta_mysql.inc";

    $local_id = $_POST["local_id"];
    $pasto_origem = $_POST["pasto_origem"];

    echo '<option value="000000000">'.htmlentities('Selecione Novo Pasto').'</option>';

    // Exibe primeiro os pastos de Entrada e Saida
    $tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_lixeira=0 AND 
              tbl_pasto_modulo = '999' AND 
              tbl_pasto_codigo_local = '$local_id' 
        ORDER BY tbl_pasto_modulo, tbl_pasto_codigo_local ASC");

/*    $query = "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_codigo_local = '$local_id' AND 
             (tbl_pasto_tipo_curral = 'E' OR 
              tbl_pasto_tipo_curral = 'S')
        ORDER BY tbl_pasto_descricao ASC";  

    $request = mysqli_query($conector, $query);*/

    while($reg_pastos = mysqli_fetch_object($tbl_pasto)){
        $id = $reg_pastos->tbl_pasto_id;
        $nome = $reg_pastos->tbl_pasto_descricao;

        if ($id!=$pasto_origem) {
            echo '<option value="'.$id.'">'.$nome.'</option>';
        }
    }

    // Exibe os pastos sem o pasto origem
    $query = "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_lixeira=0 AND 
              tbl_pasto_codigo_local = $local_id AND 
              tbl_pasto_modulo != '999' AND 
              tbl_pasto_modulo != '1006' AND 
              tbl_pasto_modulo != '1007'
        ORDER BY tbl_pasto_modulo, tbl_pasto_codigo_local ASC";

    /*$query = "SELECT * FROM tbl_pasto 
        WHERE tbl_pasto_codigo_local = $local_id AND 
              tbl_pasto_id != $pasto_origem AND
              tbl_pasto_lixeira = 0 AND 
              (tbl_pasto_tipo_curral IS NULL OR tbl_pasto_tipo_curral='')
        ORDER BY tbl_pasto_descricao ASC";*/

    $request = mysqli_query($conector, $query);

    while($reg_pastos = mysqli_fetch_object($request)){
        $id = $reg_pastos->tbl_pasto_id;
        $nome = $reg_pastos->tbl_pasto_descricao;

        if ($id!=$pasto_origem) {
            echo '<option value="'.$id.'">'.$nome.'</option>';
        }
    }

?>