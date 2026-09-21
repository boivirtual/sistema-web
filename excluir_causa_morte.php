<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_causa_morte'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tabela_causa_morte SET tab_registro_lixeira_causa_morte='1',
			                             tab_causa_morte_lixeira_em='$data_sistema',
			                             tab_causa_morte_lixeira_por='$nomeusuario'
		                           WHERE tab_codigo_causa_morte='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    echo '
				<script type="text/javascript">
					location.href="form_tabela_causa_morte.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
				</script>"
			    ';
		}
		else {
		    echo '
				<script type="text/javascript">
				location.href="form_tabela_causa_morte.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tabela_causa_morte WHERE tab_codigo_causa_morte=$codigo");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_causa_morte.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_causa_morte.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tabela_causa_morte SET tab_registro_lixeira_causa_morte='0',
			                             tab_causa_morte_lixeira_em=null,
			                             tab_causa_morte_lixeira_por=null
		                           WHERE tab_codigo_causa_morte='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_causa_morte.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo '
				<script type="text/javascript">
				location.href="form_tabela_causa_morte.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

				

	mysql_close($conector);

?>