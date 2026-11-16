<?php
class UsuarioService{

    public function getUser($user, $pass){
        $userDao = new UsuarioDao();
        $u = $userDao->getUser($user, $pass);
        //if(gettype($u) == 'object')
            //$u->setLocal(explode(", ", $u->getLocal()));
        return $u;
    }
    
    public function editUser($user){
        if($user["nome_usuario"] == ""){
            return [
                "error" => true,
                "message" => "Informe o nome."
            ];
        }elseif($user["cpf_usuario"] == ""){
            return [
                "error" => true,
                "message" => "Informe o CPF."
            ];
        }elseif($user["email_usuario"] == ""){
            return [
                "error" => true,
                "message" => "Informe o email."
            ];
        }elseif($user["senha_cad"] != "" && ($user["senha_cad"] != $user["senha_conf"])){
            return [
                "error" => true,
                "message" => "A Senhas informadas não conferem."
            ];
        }else{
            $user["cep_usuario"] = (empty($user["cep_usuario"])) ? 0 : $user["cep_usuario"];
            $userDao = new UsuarioDao();
            $errorp = "";
            $error = $userDao->editUser($user);
            if($user["senha_cad"] != ""){
                $errorp = $userDao->editUserPassword($user);
            }
            if($error){
                return [
                    "error" => true,
                    "message" => "Erro na alteração do registro!"
                ];
            }elseif($errorp != ""){
                return [
                    "error" => true,
                    "message" => "Erro na alteração da senha!"
                ];
            }

            return [
                "error" => false,
                "message" => "Registro alterado com sucesso!"
            ];
        }
    }
    
    public function allowPassChange($u){
        $dao = new UsuarioDao();
        return $dao->allowPassChange($u);
    }

    public function getUserByCpf($usuario){        
        $dao = new UsuarioDao();
        $user = $dao->getUserByCpf($usuario);
        if(!is_array($user) && !$this->allowPassChange($usuario)["error"]){
            return $this->sendEmail($user);
        }
        return $user;
    }

    public function getUserById($id){
        $dao = new UsuarioDao();
        return $dao->getUserById($id);
    }

    public function sendEmail($usuario){
        $mail = new PHPMailer();
        $mail->setLanguage('pt');

        $mail->IsSMTP();
		$mail->Host = "smtp.agrolandes.com.br";
		$mail->Port = 587;
		$mail->SMTPAutoTLS = false;
		$mail->SMTPAuth = true;
		$mail->Username = 'falecom@agrolandes.com.br';
		$mail->Password = 'Ag276293*';

		$mail->From = "falecom@agrolandes.com.br";
		$mail->FromName = "Fale com - Agrolandes";

		$mail->AddAddress($usuario->getEmail(), '');

		$mail->IsHTML(true);
		$mail->CharSet = 'utf-8';
        $mail->Subject = "Redefinição de Senha"; # Assunto da mensagem
		$mail->Body = '
		<table bgcolor="#F3F3F3" style="background-color: #F3F3F3; font-family: arial, helvetica, sans-serif; padding: 20px; width: 100%;">
		<tr>
	    	<td>
		        <table align="center" style="background-color: #0a5074; border: none; border-collapse: collapse; margin: 0 auto; width: 630px; " bgcolor="#F3F3F3">
				</table>

	            <table cellpadding="10" align="center">
	            	<tr>
	                	<td style="background-color: #0a5074; margin: 0 auto; width: 630px; padding: 40px 40px 40px 40px; text-align: center; color: #FFF; font-size: 20px; font-weight: bold;">
	                    	Redefinição da senha solicitada pelo usuário
						</td>
					</tr>

	    			<tr>
	        			<td style="margin: 0">
				            <h3><strong>Olá '.$usuario->getNome().' !</strong></h3>

				            <p style="font-family: arial, helvetica, sans-serif; font-size: 14px;">Você solicitou a redefinição da sua senha de login. Clique abaixo para redefini-la 
				            </p>

				            <a href="https://agrolandes.com.br/teste_api/sistema/form_redefinicao_senha.php?id='.$usuario->getEmail().'">Quero redefinir minha senha</a>

				            <p style="font-family: arial, helvetica, sans-serif; font-size: 14px;">Qualquer dúvida entre em contato com o nosso suporte (31) 99772-1904.
				            </p>

				            <p style="font-family: arial, helvetica, sans-serif; font-size: 14px;">Equipe Boi Virtual.
				            </p>
	                	</td>
					</tr>
				</table>
	        </td>
	    </tr>
		</table>';
        $e = $mail->Send();

        $mail->ClearAllRecipients();
		$mail->ClearAttachments();

        if($e){
            return [
                "error" => false,
                "message" => "Autorizado a redefinição da senha. Siga as instruções enviadas para o seu e-mail."
            ];
        }

        return [
            "error" => true,
            "message" => "Não foi possível enviar o e-mail. Informações do erro: {$mail->ErrorInfo}"
        ];
    }

}
