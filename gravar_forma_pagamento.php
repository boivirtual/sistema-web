<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$codigo = $_POST['codigo_tipo_editar'];
	$descricao = $_POST['descricao_tipo_editar'];

	$sql = ("UPDATE tbl_forma_pagamento SET
				tbl_forma_pagamento_descricao='$descricao',
				tbl_forma_pagamento_alterado_em='$data_sistema',
				tbl_forma_pagamento_alterado_por='$nomeusuario'
	WHERE tbl_forma_pagamento_id='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_forma_pagamento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_forma_pagamento.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>';
    }
}
else {
	$codigo = $_POST['codigo_tipo'];
	$descricao = $_POST['descricao_tipo'];

	$sql = "INSERT INTO tbl_forma_pagamento (
				tbl_forma_pagamento_descricao,
				tbl_forma_pagamento_incluido_em,
				tbl_forma_pagamento_incluido_por,
				tbl_forma_pagamento_alterado_em,
				tbl_forma_pagamento_alterado_por,
				tbl_forma_pagamento_lixeira,
				tbl_forma_pagamento_lixeira_em,
				tbl_forma_pagamento_lixeira_por
		       ) 
		VALUES ('$descricao',
				'$data_sistema',
				'$nomeusuario',
				null,
				null,
				0,
				null,
				null
			   )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_forma_pagamento.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_forma_pagamento.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>';
	}    
}

mysqli_close($conector);


?>