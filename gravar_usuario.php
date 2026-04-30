<?php 
function sonumero($str) {
	return preg_replace("/[^0-9]/", "", $str);
}

@ session_start();
$cnpj_cliente = $_SESSION['id_cliente'];
$nomeusuario = $_SESSION['nome_usuario'];

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo = $_POST['codigo_usuario'];
$nome = $_POST['nome_usuario'];
$situacao = $_POST['situacao'];
$cpf_usuario = sonumero($_POST['cpf_usuario']);
$email = $_POST['email_usuario'];
$grupo = $_POST['grupo'];
$data_sistema = date("Y-m-d H:i:s");
$senha = substr($cpf_usuario, 0, 6);

if (isset($_POST['local'])) {
	$local = implode(', ', $_POST['local']);
}
else {
	$local = '';
}

if (empty($nome)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Nome.'));
	exit;
}

if (empty($grupo)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Grupo.'));
	exit;
}

if (empty($_POST['cpf_usuario'])){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o CPF.'));
	exit;
}

if (empty($email)){
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o E-mail.'));
	exit;
}

include "conecta_mysql.inc";

if ($tipo_gravacao==2){
		$sql = "UPDATE usuario SET 
	                   lixeira_usuario=1,
	                   lixeira_em_usuario='$data_sistema',
	                   lixeira_por_usuario='$nomeusuario'
	                   WHERE id_usuario='$codigo'";
	    $resultado = mysqli_query($conector_acesso,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro enviado para lixeira com sucesso.');
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao enviar o registro para a lixeira' . $erro_mysql));
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector_acesso);
		exit;
}
else if ($tipo_gravacao==3){
		$sql = "UPDATE usuario SET 
	                   lixeira_usuario=0,
	                   lixeira_em_usuario=null,
	                   lixeira_por_usuario=null
	                   WHERE id_usuario='$codigo'";
	    $resultado = mysqli_query($conector_acesso,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro removido da lixeira com sucesso.');
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao remover o registro da lixeira' . $erro_mysql));
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector_acesso);
		exit;
}
else if ($tipo_gravacao==1){
	$sql = ("UPDATE usuario SET 
		  		nome_usuario='$nome',
	            cpf_usuario='$cpf_usuario',
	            email_usuario='$email',
	            alterado_em_usuario='$data_sistema',
	            alterado_por_usuario='$nomeusuario',
	            grupo_usuario='$grupo',
	            local_usuario='$local',
                situacao_usuario='$situacao'
 		WHERE id_usuario='$codigo'");
	    $resultado = mysqli_query($conector_acesso,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alateração ' . $erro_mysql));
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}

		mysqli_close($conector_acesso);
		exit;
}
else {

	$tbl_usuario = mysqli_query($conector_acesso,"SELECT * FROM usuario 
		                    WHERE cpf_usuario = '$cpf_usuario' AND lixeira_usuario=0");
    $num_registros = mysqli_num_rows($tbl_usuario); 

    if ($num_registros!=0){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Usuário já cadastrado com esse CPF.'));
		exit;
    }

	$sql = "INSERT INTO usuario (
		cpf_usuario,
		cnpj_cpf_empresa_usuario,
		email_usuario,
		nome_usuario,
		grupo_usuario,
		endereco_usuario,
		numero_usuario,
		complemento_usuario,
		bairro_usuario,
		cep_usuario,
		cidade_usuario,
		estado_usuario,
		senha_usuario,
		data_nascimento_usuario,
		idade_usuario,
		sexo_usuario,
		observacao_usuario,
		incluido_em_usuario,
		incluido_por_usuario,
		alterado_em_usuario,
		alterado_por_usuario,
		lixeira_usuario,
		lixeira_em_usuario,
		lixeira_por_usuario,
		situacao_usuario,
		autoriza_redefinir_senha_usuario,
		foto_usuario,
		local_usuario
        ) 
	    VALUES (
	    		'$cpf_usuario',
	    		'$cnpj_cliente',
	    		'$email',
	    		'$nome',
	    		'$grupo',
                null,
                null,
                null,
                null,
                0,
                null,
                null,
                '$senha',
                null,
                null,
                null,
                null,
                '$data_sistema',
                '$nomeusuario',
                null,
                null,
                0,
                null,
                null,
                '$situacao',
                'N',
                null,
                '$local'
        )";
	    $resultado = mysqli_query($conector_acesso,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro incluído com sucesso.');
		$erro_mysql = mysqli_error($conector_acesso);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
			mysqli_close($conector_acesso);
			exit;
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
			mysqli_close($conector_acesso);
			exit;
		}
}

mysqli_close($conector_acesso);


?>