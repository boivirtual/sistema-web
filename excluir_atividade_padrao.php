<?php
	$opcao = $_POST['tipo_gravacao'];
	$codigo = $_POST['codigo_id'];

    $data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
    $nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	switch ($opcao) {
    case 2:
		$sql = ("UPDATE tbl_atividades_padrao SET 
			tbl_atividade_padrao_lixeira='1',
			tbl_atividade_padrao_lixeira_em='$data_sistema',
			tbl_atividade_padrao_lixeira_por='$nomeusuario'
		    WHERE tbl_atividade_padrao_id='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		    echo '
				<script type="text/javascript">
					location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
				</script>"
			    ';
		}
		else {
		    echo '
				<script type="text/javascript">
				location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=EL&erro_mysql='.$erro_mysql.'";
				</script>"
   				';
		}
        break;
    case 1:
		$sql = ("DELETE FROM tbl_atividades_padrao 
			WHERE tbl_atividade_padrao_id=$codigo");
		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo "<script> alert ('Registro excluido da lixeira!'); location.href='form_tabela_atividade_padrao.php'</script>";
		}
        break;
    case 3:   
		$sql = ("UPDATE tbl_atividades_padrao SET 
			tbl_atividade_padrao_lixeira='0',
			tbl_atividade_padrao_lixeira_em=null,
			tbl_atividade_padrao_lixeira_por=null
		    WHERE tbl_atividade_padrao_id='$codigo'");

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
		}
		else {
			echo '
				<script type="text/javascript">
				location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=RL&erro_mysql='.$erro_mysql.'";
				</script>"
    		';
		}
	  	break;
	} 

	mysql_close($conector);

?>