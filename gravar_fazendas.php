<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_fazenda'];
$nome = $_POST['nome_fazenda'];
$area_total = $_POST['area_total'];
$area_total_construida = $_POST['area_total_construida'];

$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tabela_fazendas SET fazenda_nome='$nome',
		                                fazenda_area_total='$area_total',
		                                fazenda_area_construida='$area_total_construida',
       	                                fazenda_alterado_em='$data_sistema',
		                                fazenda_alterado_por='$nomeusuario'
 		                       WHERE fazenda_codigo='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_fazendas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_fazendas.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO tabela_fazendas (fazenda_nome,
									 fazenda_area_total,
									 fazenda_area_construida,
									 fazenda_registro_lixeira,
	                                 fazenda_incluido_em,
	                                 fazenda_incluido_por
                                     ) 
                              VALUES ('$nome',
                              		  '$area_total',
                              		  '$area_total_construida',
                              		  0,
                                      '$data_sistema',
                                      '$nomeusuario'
                                     )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_fazendas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_fazendas.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}

mysqli_close($conector);

?>