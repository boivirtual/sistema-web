<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_via_uso_produtos'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tabela_via_uso_produtos SET tab_registro_lixeira_via_uso_produtos='1',
			                             tab_via_uso_produtos_lixeira_em='$data_sistema',
			                             tab_via_uso_produtos_lixeira_por='$nomeusuario'
		                           WHERE tab_codigo_via_uso_produtos='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    echo '
				<script type="text/javascript">
					location.href="form_tabela_via_uso_produtos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
				</script>"
			    ';
		}
		else {
		    echo '
				<script type="text/javascript">
				location.href="form_tabela_via_uso_produtos.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tabela_via_uso_produtos WHERE tab_codigo_via_uso_produtos=$codigo");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_via_uso_produtos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_via_uso_produtos.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tabela_via_uso_produtos SET tab_registro_lixeira_via_uso_produtos='0',
			                             tab_via_uso_produtos_lixeira_em=null,
			                             tab_via_uso_produtos_lixeira_por=null
		                           WHERE tab_codigo_via_uso_produtos='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_via_uso_produtos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo '
				<script type="text/javascript">
				location.href="form_tabela_via_uso_produtos.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

				

	mysql_close($conector);

?>