<?php 

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_unidade_produtos'];
$descricao = $_POST['descricao_unidade_produtos'];
$data_sistema = date("Y-m-d H:i:s");

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE tabela_unidade_produtos SET 
		tab_descricao_unidade_produtos='$descricao',
       	tab_unidade_produtos_alterado_em='$data_sistema',
		tab_unidade_produtos_alterado_por='$nomeusuario'
 		WHERE tab_codigo_unidade_produtos='$codigo'");
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_unidade_produtos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
    else {
	    echo '
		<script type="text/javascript">
			location.href="form_tabela_unidade_produtos.php?editar=true&status_gravacao=A&erro_mysql='.$erro_mysql.'";
		</script>"
	    ';
    }
}
else {
	$sql = "INSERT INTO tabela_unidade_produtos 
		(tab_codigo_unidade_produtos,
		 tab_descricao_unidade_produtos,
		 tab_registro_lixeira_unidade_produtos,
	     tab_unidade_produtos_incluido_em,
	     tab_unidade_produtos_incluido_por
        ) 
        VALUES ('$codigo',
                '$descricao',
                0,
                '$data_sistema',
                '$nomeusuario'
            )";
	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

 	if (!$resultado){
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_unidade_produtos.php?editar=true&status_gravacao=E&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}
	else {
	    echo '
			<script type="text/javascript">
				location.href="form_tabela_unidade_produtos.php?editar=true&status_gravacao=I&erro_mysql='.$erro_mysql.'";
			</script>"
		    ';
	}    
}


mysqli_close($conector);


?>