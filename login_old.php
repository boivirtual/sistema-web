<?php
   	include "conecta_mysql.inc";

	$email_cpf_usuario = $_POST["username"];
   	$senha = $_POST["pass"];
   //$senhausuario = md5($pass);

    if (strlen($email_cpf_usuario) == 11) {
   		$usuario = mysqli_query($conector,"SELECT * FROM usuario WHERE cpf_usuario = '$email_cpf_usuario' and lixeira_usuario = 0");
	}	
	else if (strlen($email_cpf_usuario) <= 8){
		$id_usuario = str_pad($email_cpf_usuario, 8, "0", STR_PAD_LEFT);
   		$usuario = mysqli_query($conector,"SELECT * FROM usuario WHERE id_usuario = '$id_usuario'
   			 and lixeira_usuario = 0");
	}
    else {
   		$usuario = mysqli_query($conector,"SELECT * FROM usuario WHERE email_usuario = '$email_cpf_usuario'  and lixeira_usuario = 0");
    }
 
   	if ($usuario === FALSE) {
    	  exit ("Erro ao acessar os dados: " . mysqli_error($conector));
  	}

    $num_registros = mysqli_num_rows ($usuario); 
	
    if ($num_registros!=0){
    	$registro_usuario = mysqli_fetch_array($usuario);
    	$id_usuario = $registro_usuario['id_usuario'];
        $senha_usuario = $registro_usuario['senha_usuario'];
        $grupo_usuario = $registro_usuario['grupo_usuario'];
        $situação = $registro_usuario['situacao_usuario'];

    	if ($situação=="D"){
        	echo "<html><body>";
	    	echo "<p align=\"center\">Usuário Desligado!</p>";
			echo "<p align=\"center\"><a href=\"index.php\">Voltar</a></p>";
			echo "</body></html>";
    	}
		else if ($senha != $senha_usuario) {
        	echo "<html><body>";
	    	echo "<p align=\"center\">Senha Invalida!</p>";
			echo "<p align=\"center\"><a href=\"index.php\">Voltar</a></p>";
			echo "</body></html>";
    	}
    	else {
    		$ssql = "select * from grupos_acessos where codigo_grupo_acesso='$grupo_usuario'"; 
            $rs = mysqli_query($conector, $ssql); 
    	    $registro_grupo = mysqli_fetch_array($rs);
   
			@ session_start();  
			
			$nome_usuario = $registro_usuario['nome_usuario'];
			$email_usuario = $registro_usuario['email_usuario'];
			$grupo_usuario = $registro_usuario['grupo_usuario'];
			$id_cliente = $registro_usuario['cnpj_cpf_empresa_usuario'];
    		//$foto = base64_encode($registro_usuario['usuario_foto']);
    		$foto = '';


			$_SESSION['id_usuario'] = $id_usuario;
			$_SESSION['foto_usuario'] = $foto;
			$_SESSION['nome_usuario'] = $nome_usuario;
			$_SESSION['email_usuario'] = $email_usuario;
	  		$_SESSION['grupo_usuario'] = $grupo_usuario;
	  		$_SESSION['id_cliente'] = $id_cliente;
	        $_SESSION['data_sistema'] = date("Y-m-d");
	  		$_SESSION['menu_manejos'] = $registro_grupo['array_menu_manejo_grupo_acesso'];
	  		$_SESSION['menu_movimentacoes'] = $registro_grupo['array_menu_movimentacao_grupo_acesso'];
	  		$_SESSION['menu_financeiros'] = $registro_grupo['array_menu_financeiro_grupo_acesso'];
	  		$_SESSION['menu_relatorios'] = $registro_grupo['array_menu_relatorio_grupo_acesso'];
	  		$_SESSION['menu_cadastros'] = $registro_grupo['array_menu_cadastro_grupo_acesso'];
	  		$_SESSION['menu_parametros'] = $registro_grupo['array_menu_parametro_grupo_acesso'];

			header ("Location: menu.php");
    	}
    }
    else {
        echo "<html><body>";
		echo "<p align=\"center\">Usuario nao encontrado!</p>";
		echo "<p align=\"center\"><a href=\"index.php\">Voltar</a></p>";
		echo "</body></html>";
    }

	mysqli_close($conector);		

?>
