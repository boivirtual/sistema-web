<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$codigo = $_POST['codigo_tipo_editar'];
	$descricao = $_POST['descricao_tipo_editar'];
	$tipo_conta = $_POST['tipo_conta_editar'];
	$codigo_banco = $_POST['codigo_banco_editar'];
	$codigo_agencia = $_POST['codigo_agencia_editar'];
	$numero_conta = $_POST['num_conta_editar'];
	$numero_cartao = $_POST['num_cartao_editar'];

	if (empty($_POST['saldo_inicial_editar'])) {
		$saldo_inicial = 0.00;
	}
	else {
		$saldo_inicial = str_replace(',','.', str_replace('.','', $_POST['saldo_inicial_editar']));
	}
	$data_saldo = $_POST['data_saldo_editar'];

	if ($tipo_conta==3) {
		$sql = ("UPDATE tbl_forma_rec_pag SET
				tbl_forma_rec_pag_descricao='$descricao',
				tbl_forma_rec_pag_numero_cartao='$numero_cartao',
				tbl_forma_rec_pag_alterado_em='$data_sistema',
				tbl_forma_rec_pag_alterado_por='$nomeusuario'
	      WHERE tbl_forma_rec_pag_id='$codigo'");
	}
	else {
		$sql = ("UPDATE tbl_forma_rec_pag SET
				tbl_forma_rec_pag_descricao='$descricao',
				tbl_forma_rec_pag_banco='$codigo_banco',
				tbl_forma_rec_pag_agencia='$codigo_agencia',
				tbl_forma_rec_pag_conta='$numero_conta',
				tbl_forma_rec_pag_numero_cartao='$numero_cartao',
				tbl_forma_rec_pag_saldo_inicial='$saldo_inicial',
				tbl_forma_rec_pag_data_saldo='$data_saldo',
				tbl_forma_rec_pag_alterado_em='$data_sistema',
				tbl_forma_rec_pag_alterado_por='$nomeusuario'
	  	  WHERE tbl_forma_rec_pag_id='$codigo'");
	}
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_forma_recebimento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_forma_recebimento.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>';
    }
}
else {
	$codigo = $_POST['codigo_tipo'];
	$descricao = $_POST['descricao_tipo'];
	$tipo_conta = $_POST['tipo_conta'];
	$codigo_banco = $_POST['codigo_banco'];
	$codigo_agencia = $_POST['codigo_agencia'];
	$numero_conta = $_POST['num_conta'];
	$numero_cartao = $_POST['num_cartao'];

	if (empty($_POST['saldo_inicial'])) {
		$saldo_inicial = 0.00;
	}
	else {
		$saldo_inicial = str_replace(',','.', str_replace('.','', $_POST['saldo_inicial']));
	}
	$data_saldo = $_POST['data_saldo'];

	if ($tipo_conta==3) {
		$sql = "INSERT INTO tbl_forma_rec_pag (
				tbl_forma_rec_pag_descricao,
				tbl_forma_rec_pag_tipo,
				tbl_forma_rec_pag_banco,
				tbl_forma_rec_pag_agencia,
				tbl_forma_rec_pag_conta,
				tbl_forma_rec_pag_numero_cartao,
				tbl_forma_rec_pag_saldo_inicial,
				tbl_forma_rec_pag_data_saldo,
				tbl_forma_rec_pag_incluido_em,
				tbl_forma_rec_pag_incluido_por,
				tbl_forma_rec_pag_alterado_em,
				tbl_forma_rec_pag_alterado_por,
				tbl_forma_rec_pag_lixeira,
				tbl_forma_rec_pag_lixeira_em,
				tbl_forma_rec_pag_lixeira_por
		       ) 
		VALUES ('$descricao',
				'$tipo_conta',
				'$codigo_banco',
				'$codigo_agencia',
				'$numero_conta',
				'$numero_cartao',
				null,
				null,
				'$data_sistema',
				'$nomeusuario',
				null,
				null,
				0,
				null,
				null
		   )";

	}
	else {
		$sql = "INSERT INTO tbl_forma_rec_pag (
				tbl_forma_rec_pag_descricao,
				tbl_forma_rec_pag_tipo,
				tbl_forma_rec_pag_banco,
				tbl_forma_rec_pag_agencia,
				tbl_forma_rec_pag_conta,
				tbl_forma_rec_pag_numero_cartao,
				tbl_forma_rec_pag_saldo_inicial,
				tbl_forma_rec_pag_data_saldo,
				tbl_forma_rec_pag_incluido_em,
				tbl_forma_rec_pag_incluido_por,
				tbl_forma_rec_pag_alterado_em,
				tbl_forma_rec_pag_alterado_por,
				tbl_forma_rec_pag_lixeira,
				tbl_forma_rec_pag_lixeira_em,
				tbl_forma_rec_pag_lixeira_por
		       ) 
		VALUES ('$descricao',
				'$tipo_conta',
				'$codigo_banco',
				'$codigo_agencia',
				'$numero_conta',
				null,
				'$saldo_inicial',
				'$data_saldo',
				'$data_sistema',
				'$nomeusuario',
				null,
				null,
				0,
				null,
				null
			   )";
	}
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_forma_recebimento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_forma_recebimento.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>';
	}    
}

mysqli_close($conector);


?>