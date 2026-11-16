<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_id'];
$descricao = $_POST['descricao'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tbl_atividades_padrao SET 
			tbl_atividade_padrao_descricao='$descricao',
       	    tbl_atividade_padrao_alterado_em='$data_sistema',
		    tbl_atividade_padrao_alterado_por='$nomeusuario'
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
			location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else  {
    $cores = ['#282928', '#d6d5e8', '#ebced9', '#f0e4c2', '#d6f0c5', '#d3f0ef', '#ded4b1', '#959e44', '#3b7d3e', '#3b7d5b', '#3b7d71', '#3b747d', '#4a3b7d', '#733b7d', '#7d3b4c', '#7bcf70'];

	$sql = "INSERT INTO tbl_atividades_padrao 
			(tbl_atividade_padrao_descricao,
	         tbl_atividade_padrao_lixeira,
	         tbl_atividade_padrao_incluido_em,
	         tbl_atividade_padrao_incluido_por
            ) 
            VALUES ('$descricao',
                    0,
                    '$data_sistema',
                    '$nomeusuario'
                    )";
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
		$atividade_padrao_id = mysqli_insert_id($conector);

		if ($atividade_padrao_id>15) {
			$cor = $cores[0];
		}
		else {
			$cor = $cores[$atividade_padrao_id];
		} 


		$sql = ("UPDATE tbl_atividades_padrao SET 
				tbl_atividade_padrao_cor_fundo='$cor'
	 		WHERE tbl_atividade_padrao_id='$atividade_padrao_id'");

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
				location.href="form_tabela_atividade_padrao.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	    }
	}    
}

mysqli_close($conector);

?>