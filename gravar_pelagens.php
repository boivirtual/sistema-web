<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_pelagem'];
$descricao = $_POST['descricao_pelagem'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tabela_pelagens SET tab_descricao_pelagem='$descricao',
       	                             tab_pelagem_alterado_em='$data_sistema',
		                             tab_pelagem_alterado_por='$nomeusuario'
 		                       WHERE tab_codigo_pelagem='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_pelagens.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_pelagens.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else  {
	$sql = "INSERT INTO tabela_pelagens (tab_descricao_pelagem,
	                                  tab_registro_lixeira_pelagem,
	                                  tab_pelagem_incluido_em,
	                                  tab_pelagem_incluido_por
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
				location.href="form_tabela_pelagens.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_pelagens.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}

mysqli_close($conector);


?>