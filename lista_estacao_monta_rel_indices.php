<?php
include "conecta_mysql.inc";

$wlocal = "";

if (isset($_POST['local'])) {
    $local = $_POST['local'];

    if(in_array("", $local)) {
        $wlocal='';
    }
    else {
        $wlocal = " AND tbl_par_codigo_local IN(";
        $wlocal.= implode(',', $local);
        $wlocal.= ")";
        }
}

$sql = mysqli_query($conector, "SELECT * FROM tbl_parametro_estacao_monta 
	WHERE tbl_par_lixeira=0" . $wlocal .
    "ORDER BY tbl_par_estacao_monta_inicial ASC");  

$num_rows = mysqli_num_rows($sql);
//echo  '<option value="000000000">'.htmlentities('...').'</option>';

if($num_rows != 0){
   	while($reg_estacao = mysqli_fetch_object($sql)){
        $id = $reg_estacao->tbl_par_estacao_id;
        $nome = $reg_estacao->tbl_par_estacao_nome;
        $codigo_local = $reg_estacao->tbl_par_codigo_local;
        
        $inicias = pegar_inicias($codigo_local, $conector);

        $nome.='-'.$inicias;
		echo '<option value="'.$id.'">' .$nome. '</option>';
   	}
}

mysqli_close($conector);


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
 
?>
