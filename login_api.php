<?php
//    $conectado = $_POST["remember-me"];

//    if ($conectado=='sim'){
//    	echo "<html><body>";
//	    	echo "<p align=\"center\">Conectado marcado</p>";
//			echo "<p align=\"center\"><a href=\"index.php\">Voltar</a></p>";
//    }

	$valor[0]=0;
	$valor[1]='';
	$data_sistema = date("Y-m-d H:i:s");

    include "sistema/conecta_mysql_acesso.inc";

	$user = json_decode($_POST["user"]);

	@ session_start();
	$_SESSION["id_cliente"] = $user->cnpj;

	include "sistema/conecta_mysql.inc";

	$tblEmpresa = mysqli_query($conector, "select * from tbl_empresa where tbl_empresa_cpf_cnpj='$user->cnpj'"); 
					
	$registro_empresa = mysqli_fetch_object($tblEmpresa);
	
	$_SESSION['nome_empresa'] = $registro_empresa->tbl_empresa_nome_fantasia; 
	$_SESSION['controle_estoque'] = $registro_empresa->tbl_empresa_controle_pesagem; 

	$tblGrupoacesso = mysqli_query($conector,"select * from grupos_acessos where codigo_grupo_acesso='$user->grupo'"); 

	$registro_grupo = mysqli_fetch_array($tblGrupoacesso);

	$_SESSION['id_usuario'] = $user->id;
	$_SESSION['senha_usuario'] = $user->senha;
	$_SESSION['foto_usuario'] = $user->foto;
	$_SESSION['nome_usuario'] = $user->nome;
	$_SESSION['email_usuario'] = $user->email;
	$_SESSION['grupo_usuario'] = $user->grupo;
	$_SESSION['data_sistema'] = date("Y-m-d");
	$_SESSION['ultimo_cliente_cadastrado'] = 0;
	$_SESSION['abrir_agenda']='';
	
	$_SESSION['menu_manejo_animais'] = $registro_grupo['array_menu_manejo_animais_grupo_acesso'];
	$_SESSION['menu_manejo_reprodutivo'] = $registro_grupo['array_menu_manejo_reprodutivo_grupo_acesso'];
	$_SESSION['menu_suplemento_alimentar'] = $registro_grupo['array_menu_suplemento_alimentar_grupo_acesso'];
	$_SESSION['menu_controle_sanitario'] = $registro_grupo['array_menu_controle_sanitario_grupo_acesso'];
	$_SESSION['menu_gestao_adm'] = $registro_grupo['array_menu_gestao_adm_grupo_acesso'];
	$_SESSION['menu_cadastros'] = $registro_grupo['array_menu_cadastro_grupo_acesso'];
	$_SESSION['menu_parametros'] = $registro_grupo['array_menu_parametro_grupo_acesso'];
	$_SESSION['menu_relatorios'] = $registro_grupo['array_menu_relatorios_grupo_acesso'];

	$sql = "INSERT INTO tbl_historico_acesso (
								cpf_historico_acesso, 
								nome_historico_acesso,
								historico_acesso,
								incluido_em_historico_acesso
								) 
							VALUES ('$user->cpf',
									'$user->nome',
									'Acesso ao Sistema',
									'$data_sistema'
									)";
	mysqli_query($conector,$sql);
	mysqli_close($conector);	

	/* 
    	$registro_usuario = mysqli_fetch_array($usuario);
    	$id_usuario = $registro_usuario['id_usuario'];
    	$cpf_usuario = $registro_usuario['cpf_usuario'];
        $senha_usuario = $registro_usuario['senha_usuario'];
        $grupo_usuario = $registro_usuario['grupo_usuario'];
        $situação = $registro_usuario['situacao_usuario'];
		$nome_usuario = $registro_usuario['nome_usuario'];
		$email_usuario = $registro_usuario['email_usuario'];
		$id_cliente = $registro_usuario['cnpj_cpf_empresa_usuario'];
   		$foto = $registro_usuario['foto_usuario'];

		@ session_start();  
           
		$_SESSION['id_cliente'] = $id_cliente;

	   	if ($situação=="D"){
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Usuário desligado.'));
		    mysqli_close($conector_acesso);
		    exit;
    	}
		else if ($senha != $senha_usuario) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Senha inválida.'));
		    mysqli_close($conector_acesso);
		    exit;
    	}
    	else {
		   	include "sistema/conecta_mysql.inc";

	        $tbl_empresa = mysqli_query($conector, "select * from tbl_empresa 
	                                                      where tbl_empresa_cpf_cnpj='$id_cliente'"); 
	                     
	        $reg_empresa = mysqli_fetch_object($tbl_empresa);
	        $_SESSION['nome_empresa'] = $reg_empresa->tbl_empresa_nome_fantasia; 
	        $_SESSION['controle_estoque'] = $reg_empresa->tbl_empresa_controle_pesagem; 

    		$ssql = "select * from grupos_acessos where codigo_grupo_acesso='$grupo_usuario'"; 
            $rs = mysqli_query($conector, $ssql); 
    	    $registro_grupo = mysqli_fetch_array($rs);
   
			$_SESSION['id_usuario'] = $id_usuario;
			$_SESSION['senha_usuario'] = $senha_usuario;
			$_SESSION['foto_usuario'] = $foto;
			$_SESSION['nome_usuario'] = $nome_usuario;
			$_SESSION['email_usuario'] = $email_usuario;
	  		$_SESSION['grupo_usuario'] = $grupo_usuario;
	        $_SESSION['data_sistema'] = date("Y-m-d");
			$_SESSION['ultimo_cliente_cadastrado'] = 0;

	  		$_SESSION['menu_manejo_animais'] = $registro_grupo['array_menu_manejo_animais_grupo_acesso'];
	  		$_SESSION['menu_manejo_reprodutivo'] = $registro_grupo['array_menu_manejo_reprodutivo_grupo_acesso'];
	  		$_SESSION['menu_suplemento_alimentar'] = $registro_grupo['array_menu_suplemento_alimentar_grupo_acesso'];
	  		$_SESSION['menu_controle_sanitario'] = $registro_grupo['array_menu_controle_sanitario_grupo_acesso'];
	  		$_SESSION['menu_gestao_adm'] = $registro_grupo['array_menu_gestao_adm_grupo_acesso'];
	  		$_SESSION['menu_cadastros'] = $registro_grupo['array_menu_cadastro_grupo_acesso'];
	  		$_SESSION['menu_parametros'] = $registro_grupo['array_menu_parametro_grupo_acesso'];
	  		$_SESSION['menu_relatorios'] = $registro_grupo['array_menu_relatorios_grupo_acesso'];

			$sql = "INSERT INTO tbl_historico_acesso (
			                          cpf_historico_acesso, 
		                              nome_historico_acesso,
		                              historico_acesso,
		                              incluido_em_historico_acesso
		                             ) 
		                          VALUES ('$cpf_usuario',
		                                  '$nome_usuario',
		                                  'Acesso ao Sistema',
	                                      '$data_sistema'
		                                 )";
			$resultado = mysqli_query($conector,$sql);
    	}
    } */

	//mysqli_close($conector);	

?>
