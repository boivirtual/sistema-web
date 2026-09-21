<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_categoria'];
$categoria_de = $_POST['categoria_de'];
$categoria_ate = $_POST['categoria_ate'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tabela_categoria_idade SET tab_categoria_idade_de='$categoria_de',
		                                       tab_categoria_idade_ate='$categoria_ate',
       	                                       tab_alterado_categoria_idade_em='$data_sistema',
		                                       tab_alterado_categoria_idade_por='$nomeusuario'
 		                                 WHERE tab_codigo_categoria_idade='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_categoria_idade.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_categoria_idade.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO tabela_categoria_idade (tab_categoria_idade_de,
	                                            tab_categoria_idade_ate,
	                                            tab_registro_lixeira_categoria_idade,
	                                            tab_incluido_categoria_idade_em,
	                                            tab_incluido_categoria_idade_por
                                     ) 
                              VALUES ('$categoria_de',
                                      '$categoria_ate',
                                      0,
                                      '$data_sistema',
                                      '$nomeusuario'
                                     )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_categoria_idade.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_categoria_idade.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}


mysqli_close($conector);

?>