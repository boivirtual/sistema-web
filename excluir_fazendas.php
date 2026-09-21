<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_fazenda'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	//$rs = mysqli_query($conector, "SELECT * FROM tabela_fazendas WHERE fazenda_codigo=$codigo");  
	//$num_registros = mysqli_num_rows ($rs); 
	

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tabela_fazendas SET fazenda_registro_lixeira='1',
			                             fazenda_lixeira_em='$data_sistema',
			                             fazenda_lixeira_por='$nomeusuario'
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
				location.href="form_tabela_fazendas.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tabela_fazendas WHERE fazenda_codigo=$codigo");
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
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_fazendas.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tabela_fazendas SET fazenda_registro_lixeira='0',
			                             fazenda_lixeira_em=null,
			                             fazenda_lixeira_por=null
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
				location.href="form_tabela_fazendas.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

				

	mysql_close($conector);

?>