<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_modulo'];
$descricao = $_POST['descricao_modulo'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_modulo_pasto SET tbl_modulo_descricao='$descricao',
       	                             tbl_modulo_alterado_em='$data_sistema',
		                             tbl_modulo_alterado_por='$nomeusuario'
 		                       WHERE tbl_modulo_id ='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_modulo_pasto.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_modulo_pasto.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else  {
	$sql = "INSERT INTO tbl_modulo_pasto (tbl_modulo_descricao,
	                                  tbl_modulo_lixeira,
	                                  tbl_modulo_incluido_em,
	                                  tbl_modulo_incluido_por
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
				location.href="form_tabela_modulo_pasto.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_modulo_pasto.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}

mysqli_close($conector);


?>