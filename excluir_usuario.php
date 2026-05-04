<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_usuario'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql_acesso.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE usuario SET lixeira_usuario='1',
			                         lixeira_em_usuario='$data_sistema',
			                         lixeira_por_usuario='$nomeusuario'
		                           WHERE id_usuario='$codigo'");

		$resultado = mysqli_query($conector_acesso,$sql);
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
		    echo '
				<script type="text/javascript">
					location.href="form_tabela_usuarios.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
				</script>"
			    ';
		}
		else {
		    echo '
				<script type="text/javascript">
				location.href="form_tabela_usuarios.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM usuario WHERE id_usuario=$codigo");
		$resultado = mysqli_query($conector_acesso,$sql);
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_usuarios.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_usuarios.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE usuario SET lixeira_usuario='0',
			                         lixeira_em_usuario=null,
			                         lixeira_por_usuario=''
		                           WHERE id_usuario='$codigo'");

		$resultado = mysqli_query($conector_acesso,$sql);
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_usuarios.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo '
				<script type="text/javascript">
				location.href="form_tabela_usuarios.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

	mysql_close($conector_acesso);

?>