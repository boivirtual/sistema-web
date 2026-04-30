<?php
	$valor[0]=0;
	$valor[1]='';

   include "conecta_mysql.inc";

	$email_usuario = $_POST["email_usuario"];
	$senha_nova = $_POST["senha_nova"];

    if ($email_usuario == ''){
       $valor[0]=9;
       $valor[1]='Informe um e-mail válido!';
       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
       die ($str);
    }

 	$usuario = mysqli_query($conector_acesso,"SELECT * FROM usuario WHERE email_usuario = '$email_usuario' and lixeira_usuario = 0");
 
   	if ($usuario === FALSE) {
    	  exit ("Erro ao acessar os dados: " . mysqli_error());
  	}

    $num_registros = mysqli_num_rows ($usuario); 

    if ($num_registros == 0){
       $valor[0]=9;
       $valor[1]='E-mail não cadastrado!';
       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
       die ($str);
    }
	
    if ($num_registros!=0){
		$sql = ("UPDATE usuario SET autoriza_redefinir_senha_usuario='N',
									 senha_usuario='$senha_nova'
	 		                   WHERE email_usuario = '$email_usuario'");
		$resultado = mysqli_query($conector_acesso,$sql);
		$erro_mysql = mysqli_error($conector_acesso);

	 	if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao registrar a sua solicitação: ' . $erro_mysql;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}
		else {
	       $valor[0]=0;
	       $valor[1]='Senha redefinida com sucesso. Faça login novamente';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);

		}
	}

	mysqli_close($conector_acesso);	


?>
