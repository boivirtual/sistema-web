<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_tipo'];
$descricao = $_POST['descricao_tipo'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_centro_custo SET tbl_cc_descricao='$descricao',
       	                             tbl_cc_alterado_em='$data_sistema',
		                             tbl_cc_alterado_por='$nomeusuario'
 		                       WHERE tbl_cc_codigo_id='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_centro_custos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_centro_custos.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO tbl_centro_custo (tbl_cc_descricao,
	                                  tbl_cc_lixeira,
	                                  tbl_cc_incluido_em,
	                                  tbl_cc_incluido_por
                                     ) 
                              VALUES ('$descricao',
                              		  0,
                                      '$data_sistema',
                                      '$nomeusuario'
                                     )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_centro_custos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
		header("Location: form_tabela_centro_custos.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'");
	}    
}


mysqli_close($conector);

?>