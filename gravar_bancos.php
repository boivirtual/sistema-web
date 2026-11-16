<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_id = $_POST['codigo_id'];
$codigo_banco = $_POST['codigo_bancos'];
$nome = $_POST['nome_bancos'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_banco SET tbl_banco_codigo='$codigo_banco',
		                          tbl_banco_nome='$nome',
       	                          tbl_banco_alterado_em='$data_sistema',
		                          tbl_banco_alterado_por='$nomeusuario'
 		                    WHERE tbl_banco_id='$codigo_id'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_bancos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_bancos.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else{
	$sql = "INSERT INTO tbl_banco (tbl_banco_codigo, 
	                               tbl_banco_nome,
	                               tbl_banco_incluido_em,
	                               tbl_banco_incluido_por,
	                               tbl_banco_lixeira,
	                               tbl_banco_alterado_em,
	                               tbl_banco_alterado_por,
	                               tbl_banco_lixeira_em,
	                               tbl_banco_lixeira_por
	                                 ) 
	                          VALUES ('$codigo_banco',
	                                  '$nome',
                                      '$data_sistema',
                                      '$nomeusuario',
                                      0,
                                      null,
                                      null,
                                      null,
                                      null
	                                 )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_bancos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_bancos.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}


mysqli_close($conector);


?>