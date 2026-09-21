<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_raca'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	//$rs = mysqli_query($conector, "SELECT * FROM tabela_racas WHERE tab_codigo_raca=$codigo");  
	//$num_registros = mysqli_num_rows ($rs); 
	

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tabela_racas SET tab_registro_lixeira_raca='1',
			                             tab_raca_lixeira_em='$data_sistema',
			                             tab_raca_lixeira_por='$nomeusuario'
		                           WHERE tab_codigo_raca='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    echo '
				<script type="text/javascript">
					location.href="form_tabela_racas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
				</script>"
			    ';
		}
		else {
		    echo '
				<script type="text/javascript">
				location.href="form_tabela_racas.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tabela_racas WHERE tab_codigo_raca=$codigo");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_racas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_racas.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tabela_racas SET tab_registro_lixeira_raca='0',
			                             tab_raca_lixeira_em=null,
			                             tab_raca_lixeira_por=null
		                           WHERE tab_codigo_raca='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_racas.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo '
				<script type="text/javascript">
				location.href="form_tabela_racas.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

				

	mysql_close($conector);

?>