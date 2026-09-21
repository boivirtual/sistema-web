<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_tipo'];
$descricao = $_POST['descricao_tipo'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_tipo_documento SET tbl_tipo_doc_descricao='$descricao',
       	                                   tbl_tipo_doc_alterado_em='$data_sistema',
		                                   tbl_tipo_doc_alterado_por='$nomeusuario'
 		                             WHERE tbl_tipo_doc_id='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_tipo_documento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_tipo_documento.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO tbl_tipo_documento (
						tbl_tipo_doc_descricao,
						tbl_tipo_doc_incluido_em,
						tbl_tipo_doc_incluido_por,
						tbl_tipo_doc_alterado_em,
						tbl_tipo_doc_alterado_por,
						tbl_tipo_doc_lixeira,
						tbl_tipo_doc_lixeira_em,
						tbl_tipo_doc_lixeira_por
                       ) 
                VALUES ('$descricao',
                        '$data_sistema',
                        '$nomeusuario',
                        null,
                        null,
                        0,
                        null,
                        null
                                     )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_tipo_documento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_tipo_documento.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}



mysqli_close($conector);


?>