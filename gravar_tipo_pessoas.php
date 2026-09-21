<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_tipo'];
$descricao = $_POST['descricao_tipo'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tabela_tipo_pessoas SET tab_descricao_tipo_pessoa='$descricao',
       	                             tab_alterado_tipo_pessoa_em='$data_sistema',
		                             tab_alterado_tipo_pessoa_por='$nomeusuario'
 		                       WHERE tab_codigo_tipo_pessoa='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_tipo_pessoas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_tipo_pessoas.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO tabela_tipo_pessoas (tab_descricao_tipo_pessoa,
	                                  tab_registro_lixeira_tipo_pessoa,
	                                  tab_incluido_tipo_pessoa_em,
	                                  tab_incluido_tipo_pessoa_por
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
				location.href="form_tabela_tipo_pessoas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
		header("Location: form_tabela_tipo_pessoas.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'");
	    /*echo '
			<script type="text/javascript">
				location.href="form_tabela_tipo_pessoas.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';*/
	}    
}


mysqli_close($conector);

?>