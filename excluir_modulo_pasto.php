<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_modulo'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tbl_modulo_pasto SET tbl_modulo_lixeira='1',
			                             tbl_modulo_lixeira_em='$data_sistema',
			                             tbl_modulo_lixeira_por='$nomeusuario'
		                           WHERE tbl_modulo_id='$codigo'");

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
				location.href="form_tabela_modulo_pasto.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tbl_modulo_pasto WHERE tbl_modulo_id=$codigo");
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
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_modulo_pasto.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tbl_modulo_pasto SET tbl_modulo_lixeira='0',
			                             tbl_modulo_lixeira_em=null,
			                             tbl_modulo_lixeira_por=null
		                           WHERE tbl_modulo_id='$codigo'");

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
				location.href="form_tabela_modulo_pasto.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

	mysql_close($conector);

?>