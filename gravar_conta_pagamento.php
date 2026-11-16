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
		$sql = ("UPDATE tbl_conta_pagamento SET
				tbl_conta_pagamento_descricao='$descricao',
				tbl_conta_pagamento_numero_cartao='$numero_cartao',
				tbl_conta_pagamento_alterado_em='$data_sistema',
				tbl_conta_pagamento_alterado_por='$nomeusuario'
	      WHERE tbl_conta_pagamento_id='$codigo'");
	}
	else {
		if (empty($data_saldo)) {
			$sql = ("UPDATE tbl_conta_pagamento SET
					tbl_conta_pagamento_descricao='$descricao',
					tbl_conta_pagamento_banco='$codigo_banco',
					tbl_conta_pagamento_agencia='$codigo_agencia',
					tbl_conta_pagamento_conta='$numero_conta',
					tbl_conta_pagamento_numero_cartao='$numero_cartao',
					tbl_conta_pagamento_saldo_inicial='$saldo_inicial',
					tbl_conta_pagamento_alterado_em='$data_sistema',
					tbl_conta_pagamento_alterado_por='$nomeusuario'
		  	  WHERE tbl_conta_pagamento_id='$codigo'");
		}
		else {
			$sql = ("UPDATE tbl_conta_pagamento SET
					tbl_conta_pagamento_descricao='$descricao',
					tbl_conta_pagamento_banco='$codigo_banco',
					tbl_conta_pagamento_agencia='$codigo_agencia',
					tbl_conta_pagamento_conta='$numero_conta',
					tbl_conta_pagamento_numero_cartao='$numero_cartao',
					tbl_conta_pagamento_saldo_inicial='$saldo_inicial',
					tbl_conta_pagamento_data_saldo='$data_saldo',
					tbl_conta_pagamento_alterado_em='$data_sistema',
					tbl_conta_pagamento_alterado_por='$nomeusuario'
		  	  WHERE tbl_conta_pagamento_id='$codigo'");
		}
	}
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_conta_pagamento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_conta_pagamento.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
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
		$sql = "INSERT INTO tbl_conta_pagamento (
				tbl_conta_pagamento_descricao,
				tbl_conta_pagamento_tipo,
				tbl_conta_pagamento_banco,
				tbl_conta_pagamento_agencia,
				tbl_conta_pagamento_conta,
				tbl_conta_pagamento_numero_cartao,
				tbl_conta_pagamento_saldo_inicial,
				tbl_conta_pagamento_data_saldo,
				tbl_conta_pagamento_incluido_em,
				tbl_conta_pagamento_incluido_por,
				tbl_conta_pagamento_alterado_em,
				tbl_conta_pagamento_alterado_por,
				tbl_conta_pagamento_lixeira,
				tbl_conta_pagamento_lixeira_em,
				tbl_conta_pagamento_lixeira_por
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
		if (empty($data_saldo)) {
			$sql = "INSERT INTO tbl_conta_pagamento (
					tbl_conta_pagamento_descricao,
					tbl_conta_pagamento_tipo,
					tbl_conta_pagamento_banco,
					tbl_conta_pagamento_agencia,
					tbl_conta_pagamento_conta,
					tbl_conta_pagamento_numero_cartao,
					tbl_conta_pagamento_saldo_inicial,
					tbl_conta_pagamento_data_saldo,
					tbl_conta_pagamento_incluido_em,
					tbl_conta_pagamento_incluido_por,
					tbl_conta_pagamento_alterado_em,
					tbl_conta_pagamento_alterado_por,
					tbl_conta_pagamento_lixeira,
					tbl_conta_pagamento_lixeira_em,
					tbl_conta_pagamento_lixeira_por
			       ) 
			VALUES ('$descricao',
					'$tipo_conta',
					'$codigo_banco',
					'$codigo_agencia',
					'$numero_conta',
					null,
					'$saldo_inicial',
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
			$sql = "INSERT INTO tbl_conta_pagamento (
					tbl_conta_pagamento_descricao,
					tbl_conta_pagamento_tipo,
					tbl_conta_pagamento_banco,
					tbl_conta_pagamento_agencia,
					tbl_conta_pagamento_conta,
					tbl_conta_pagamento_numero_cartao,
					tbl_conta_pagamento_saldo_inicial,
					tbl_conta_pagamento_data_saldo,
					tbl_conta_pagamento_incluido_em,
					tbl_conta_pagamento_incluido_por,
					tbl_conta_pagamento_alterado_em,
					tbl_conta_pagamento_alterado_por,
					tbl_conta_pagamento_lixeira,
					tbl_conta_pagamento_lixeira_em,
					tbl_conta_pagamento_lixeira_por
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
	}
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_conta_pagamento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_conta_pagamento.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>';
	}    
}

mysqli_close($conector);


?>