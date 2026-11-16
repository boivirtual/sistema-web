<?php
include "conecta_mysql.inc";

$id_animal = $_POST['id_animal'];  

$tbl_cobertura = mysqli_query($conector, "select * from tbl_item_cobertura
    inner join tbl_cobertura
            on tbl_ite_cobertura_numero_id = tbl_cobertura_id
         where tbl_cobertura_lixeira=0 and
               tbl_cobertura_controle='C' and 
               tbl_ite_cobertura_codigo_id_animal = '$id_animal'
      order by tbl_cobertura_codigo_estacao_monta ASC");  

$num_rows_cobertura = mysqli_num_rows($tbl_cobertura);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if ($num_rows_cobertura!=0) {
    $estacao_anterior = 0;

    while ($reg_cobertura = mysqli_fetch_object($tbl_cobertura)){
        $id_estacao=$reg_cobertura->tbl_cobertura_codigo_estacao_monta;
        $local=$reg_cobertura->tbl_cobertura_codigo_local;

        $inicias = pegar_inicias($local, $conector);

        $sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
            WHERE tbl_par_estacao_id = '$id_estacao' AND 
                  tbl_par_lixeira=0");  

        $num_rows = mysqli_num_rows($sql);

        if($num_rows != 0){
            $reg_estacao = mysqli_fetch_object($sql);
            $id = $reg_estacao->tbl_par_estacao_id;
            $nome = $reg_estacao->tbl_par_estacao_nome;
            $nome.='-'.$inicias;

            if ($estacao_anterior!=$id) {
                echo '<option value="'.$id.'" selected>' .$nome. '</option>';
                $estacao_anterior=$id;
            }
        }
    }
}

// lista a ultima estacao ativa

$data_hoje = date('Y-m-d');
$codigo_local = $_POST['local'];  

$inicias = pegar_inicias($codigo_local, $conector);

$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
    WHERE tbl_par_codigo_local = '$codigo_local' AND 
          tbl_par_lixeira=0 AND 
          tbl_par_estacao_monta_inicial<='$data_hoje' AND 
          tbl_par_estacao_monta_final>='$data_hoje'");  

$num_rows = mysqli_num_rows($sql);

if($num_rows != 0){
    $reg_estacao = mysqli_fetch_object($sql);
    $id = $reg_estacao->tbl_par_estacao_id;
    $nome = $reg_estacao->tbl_par_estacao_nome;
    $nome.='-'.$inicias;

    if ($id!=$estacao_anterior) {
        echo '<option value="'.$id.'" selected>' .$nome. '</option>';
    }
}

function pegar_inicias($local, $conector) {
    $local = mysqli_query($conector, "select * from tbl_pessoa where tbl_pessoa_id='$local'"); 
    $reg = mysqli_fetch_object($local);

    $nome_fazenda = $reg->tbl_pessoa_nome;

    $nome = preg_split("/((de|da|do|dos|das)?)[\s,_-]+/", $nome_fazenda);
    $iniciais = "";
    foreach($nome as $n) {
        if (strlen($n) > 0) {
            $iniciais .= $n[0];
        }
    }
    return $nome_fazenda;
}

/*
$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
	WHERE tbl_par_codigo_local = '$local' AND 
          tbl_par_lixeira=0
    ORDER BY tbl_par_estacao_monta_inicial ASC");  

$num_rows = mysqli_num_rows($sql);
echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($ln = mysqli_fetch_assoc($sql)){
   		$id = $ln['tbl_par_estacao_id'];
        $nome = $ln['tbl_par_estacao_nome'];

        if ($estacao_monta!='') {
        	if ($estacao_monta==$id) {
				echo '<option value="'.$id.'" selected>' .$nome. '</option>';
        	}
        	else {
				echo '<option value="'.$id.'">' .$nome. '</option>';
        	}
        }
        else {
			echo '<option value="'.$id.'" selected>' .$nome. '</option>';
        }
   	}
}
*/

mysqli_close($conector);

 
?>
