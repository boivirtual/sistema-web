<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_tipo'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tabela_tipo_pessoas SET tab_registro_lixeira_tipo_pessoa='1',
			                             tab_lixeira_tipo_pessoa_em='$data_sistema',
			                             tab_lixeira_tipo_pessoa_por='$nomeusuario'
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
				location.href="form_tabela_tipo_pessoas.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tabela_tipo_pessoas WHERE tab_codigo_tipo_pessoa=$codigo");
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
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_tipo_pessoas.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tabela_tipo_pessoas SET tab_registro_lixeira_tipo_pessoa='0',
			                             tab_lixeira_tipo_pessoa_em=null,
			                             tab_lixeira_tipo_pessoa_por=null
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
				location.href="form_tabela_tipo_pessoas.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

	mysql_close($conector);

?>