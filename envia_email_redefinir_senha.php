<?php

	$valor[0]=0;
	$valor[1]='';

	$email_cpf_usuario = $_POST["email_usuario"];

    if ($email_cpf_usuario == ''){
       $valor[0]=9;
       $valor[1]='Informe um CPF ou E-mail válido!';
       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
       die ($str);
    }

    include_once "conecta_mysql_credenciais.inc";
  	$banco = "acesso_boi_virtual";
   
  	$conector_acesso = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
  
  	if (mysqli_connect_error()) {
       $valor[0]=9;
       $valor[1]='Falha na conexão com o banco de dados : ' . mysqli_connect_error();
       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
       die ($str);
  	}

  	$bancoselecionado = mysqli_select_db($conector_acesso,$banco);

  	if ($bancoselecionado === FALSE) {
       $valor[0]=9;
       $valor[1]='Falha na seleção do banco de dados: ' . mysqli_error($conector);
       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
       die ($str);
  	}

	if (strlen($email_cpf_usuario) == 11) {
   		$usuario = mysqli_query($conector_acesso,"SELECT * FROM usuario WHERE cpf_usuario = '$email_cpf_usuario' and lixeira_usuario = 0");
	}	
    else {
	 	$usuario = mysqli_query($conector_acesso,"SELECT * FROM usuario WHERE email_usuario = '$email_cpf_usuario' and lixeira_usuario = 0");
    }
  
   	if ($usuario === FALSE) {
    	  exit ("Erro ao acessar os dados: " . mysqli_error($conector_acesso));
  	}

    $num_registros = mysqli_num_rows ($usuario); 

    if ($num_registros == 0){
       $valor[0]=9;
       $valor[1]='CPF ou E-mail não cadastrado!';
       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
       die ($str);
    }
	
    if ($num_registros!=0){
    	$registro_usuario = mysqli_fetch_array($usuario);
    	$nome_usuario = $registro_usuario['nome_usuario'];
    	$email_cpf_usuario = $registro_usuario['email_usuario'];

		$sql = ("UPDATE usuario SET autoriza_redefinir_senha_usuario='S'
	 		                   WHERE email_usuario = '$email_cpf_usuario'");
		$resultado = mysqli_query($conector_acesso,$sql);
		$erro_mysql = mysqli_error($conector_acesso);

	 	if (!$resultado){
	       $valor[0]=9;
	       $valor[1]='Erro ao registrar a sua solicitação: ' . $erro_mysql;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}

		$mens='';
		require_once 'phpmailer/class.phpmailer.php';
		require_once 'phpmailer/class.smtp.php';

		$mail = new PHPMailer();
		$mail->setLanguage('pt');

		//Variaveis de configuração do servidor do GMAIL

		# Define os dados do servidor e tipo de conexão
		$mail->IsSMTP(); // Define que a mensagem será SMTP
		$mail->Host = "smtp.agrolandes.com.br"; # Endereço do servidor SMTP
		$mail->Port = 587; // Porta TCP para a conexão
		$mail->SMTPAutoTLS = false; // Utiliza TLS Automaticamente se disponível
		$mail->SMTPAuth = true; # Usar autenticação SMTP - Sim
		$mail->Username = 'falecom@agrolandes.com.br'; # Usuário de e-mail
		$mail->Password = 'Ag276293*'; // # Senha do usuário de e-mail

		# Define o remetente (você)
		$mail->From = "falecom@agrolandes.com.br"; # Seu e-mail
		$mail->FromName = "Fale com - Agrolandes"; // Seu nome

		# Define os destinatário(s)
		$mail->AddAddress($email_cpf_usuario, ''); # Os campos podem ser substituidos por variáveis
		#$mail->AddAddress('webmaster@nomedoseudominio.com'); # Caso queira receber uma copia
		#$mail->AddCC('ciclano@site.net', 'Ciclano'); # Copia
		#$mail->AddBCC('fulano@dominio.com.br', 'Fulano da Silva'); # Cópia Oculta

		# Define os dados técnicos da Mensagem
		$mail->IsHTML(true); # Define que o e-mail será enviado como HTML
		$mail->CharSet = 'utf-8'; # Charset da mensagem (opcional)

		$message ='
        <table bgcolor="#F3F3F3" style="background-color: #F3F3F3; font-family: arial, helvetica, sans-serif; padding: 20px; width: 100%;">
            <tbody>
                <tr>
                    <td>
                        <table align="center" style="background-color: #0a5074; border: none; border-collapse: collapse; margin: 0 auto; width: 630px; " bgcolor="#F3F3F3">
                        </table>

                        <table cellpadding="10" align="center">
                            <tbody>
                                <tr>
                                    <td style="background-color: #0a5074; margin: 0 auto; width: 630px; padding: 40px 40px 40px 40px; text-align: center; color: #FFF; font-size: 20px; font-weight: bold;">
                                        Redefinição da senha solicitada pelo usuário
                                    </td>
                                </tr>

                                <tr>
                                    <td style="margin: 0">
                                        <h3><strong>Olá '.$nome_usuario.' !</strong></h3>

                                        <p style="font-family: arial, helvetica, sans-serif; font-size: 14px;">Você solicitou a redefinição da sua senha de login. Clique abaixo para redefini-la 
                                        </p>

                                        <a href="https://boivirtual.com.br/sistema/form_redefinicao_senha.php?id='.$email_cpf_usuario.'">Quero redefinir minha senha</a>

                                        <p style="font-family: arial, helvetica, sans-serif; font-size: 14px;">Qualquer dúvida entre em contato com o nosso suporte (31) 9 8968-4546.
                                        </p>

                                        <p style="font-family: arial, helvetica, sans-serif; font-size: 14px;">Equipe Agrolandes.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>';

		# Define a mensagem (Texto e Assunto)
		$mail->Subject = "Redefinição de Senha"; # Assunto da mensagem
		$mail->Body = $message;

		# Define os anexos (opcional)
		//$mail->AddAttachment("pedidos_compras/ordem_compra000000001.pdf", "ordem_compra000000001.pdf"); # Insere um anexo

		# Envia o e-mail
		$enviado = $mail->Send();

		# Limpa os destinatários e os anexos
		$mail->ClearAllRecipients();
		$mail->ClearAttachments();

		# Exibe uma mensagem de resultado (opcional)
		if ($enviado) {
	       $valor[0]=0;
	       $valor[1]='Autorizado a redefinição da senha. Siga as instruções enviadas para o seu e-mail.';
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		} 
		else {
	       $valor[0]=9;
	       $valor[1]='Não foi possível enviar o e-mail. Informações do erro:' . $mail->ErrorInfo;
	       $str = $valor[0] . '<|>' . $valor[1] . '<|>';
	       die ($str);
		}	

    }

	mysqli_close($conector_acesso);	
?>
