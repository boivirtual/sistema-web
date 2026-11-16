<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_tipo'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tbl_tipo_documento SET tbl_tipo_doc_lixeira='1',
			                                   tbl_tipo_doc_lixeira_em='$data_sistema',
			                                   tbl_tipo_doc_lixeira_por='$nomeusuario'
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
				location.href="form_tabela_tipo_documento.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tbl_tipo_documento WHERE tbl_tipo_doc_id=$codigo");
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
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_tipo_documento.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tbl_tipo_documento SET tbl_tipo_doc_lixeira='0',
			                             tbl_tipo_doc_lixeira_em=null,
			                             tbl_tipo_doc_lixeira_por=null
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
				location.href="form_tabela_tipo_documento.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

				

	mysql_close($conector);

?>