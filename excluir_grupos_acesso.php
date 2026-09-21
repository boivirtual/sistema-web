<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_grupo'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	//$rs = mysqli_query($conector, "SELECT * FROM grupos_acessos WHERE tab_codigo_grupo_produtos=$codigo");  
	//$num_registros = mysqli_num_rows ($rs); 
	

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE grupos_acessos SET registro_lixeira_grupo_acesso='1',
			                             grupo_acesso_lixeira_em='$data_sistema',
			                             grupo_acesso_lixeira_por='$nomeusuario'
		                           WHERE codigo_grupo_acesso='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    echo '
				<script type="text/javascript">
					location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
				</script>"
			    ';
		}
		else {
		    echo '
				<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM grupos_acessos WHERE tab_codigo_grupo_produtos=$codigo");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_grupo_acessos.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE grupos_acessos SET registro_lixeira_grupo_acesso='0',
			                             grupo_acesso_lixeira_em=null,
			                             grupo_acesso_lixeira_por=null
		                           WHERE codigo_grupo_acesso='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo '
				<script type="text/javascript">
				location.href="form_tabela_grupo_acessos.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

				

	mysql_close($conector);

?>