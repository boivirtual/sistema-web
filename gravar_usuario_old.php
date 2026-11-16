<?php 
function sonumero($str) {
	return preg_replace("/[^0-9]/", "", $str);
}

$tipo_gravacao = $_POST['tipo_gravacao'];
$codigo_usuario = $_POST['codigo_usuario'];
$nome_usuario = $_POST['nome_usuario'];
$cpf_usuario = sonumero($_POST['cpf_usuario']);
$email = $_POST['email_usuario'];
$grupo = $_POST['grupo'];

/*
if (isset($_POST['local'])) {
	$local = implode(', ', $_POST['local']);
}
else {
	$local = '';
}
*/
if ($tipo_gravacao==1) {
	$situacao = $_POST['situacao'];
}
else {
	$senha_conf = substr($cpf_usuario, 0,6);
}

$data_sistema = date("Y-m-d H:i:s");

print_r($nome_usuario);
exit;



if (empty($nome_usuario)) {
	header('Content-type: application/json');
   		echo json_encode(array('error' => true, 'message' => 'Informe o Nome'));
   	exit;
}

if (empty($cpf_usuario)) {
	header('Content-type: application/json');
   		echo json_encode(array('error' => true, 'message' => 'Informe o CPF'));
   	exit;
}

if (empty($email)) {
	header('Content-type: application/json');
   		echo json_encode(array('error' => true, 'message' => 'Informe o E-mail'));
   	exit;
}

if (empty($grupo)) {
	header('Content-type: application/json');
   		echo json_encode(array('error' => true, 'message' => 'Informe o Grupo'));
   	exit;
}

if (empty($local)) {
	header('Content-type: application/json');
   		echo json_encode(array('error' => true, 'message' => 'Informe o Local'));
   	exit;
}

@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$id_cliente = $_SESSION['id_cliente'];

include "conecta_mysql.inc";

if ($tipo_gravacao==1){
	$sql = ("UPDATE usuario SET nome_usuario='$nome_usuario',
	                             cpf_usuario='$cpf_usuario',
	                             email_usuario='$email',
	                             alterado_em_usuario='$data_sistema',
	                             alterado_por_usuario='$nomeusuario',
	                             grupo_usuario='$grupo',
	                             local_usuario='$local',
	                             situacao_usuario='$situacao'
	                      WHERE id_usuario='$codigo_usuario'");
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro alterado com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na alteração do registro' . $erro_mysql));
		} 
		else {
	        $ssql = "select * from grupos_acessos where codigo_grupo_acesso = '$grupo'"; 
	        $rs = mysqli_query($conector, $ssql); 
	                     
	        $registro_tabela = mysqli_fetch_object($rs);
	        $_SESSION['menu_manejo_animais'] = $registro_tabela->array_menu_manejo_animais_grupo_acesso;
	        $_SESSION['menu_manejo_reprodutivo'] = $registro_tabela->array_menu_manejo_reprodutivo_grupo_acesso;
	        $_SESSION['menu_suplemento_alimentar'] = $registro_tabela->array_menu_suplemento_alimentar_grupo_acesso;
	        $_SESSION['menu_controle_sanitario'] = $registro_tabela->array_menu_controle_sanitario_grupo_acesso;
	        $_SESSION['menu_gestao_adm'] = $registro_tabela->array_menu_gestao_adm_grupo_acesso;
	        $_SESSION['menu_cadastros'] = $registro_tabela->array_menu_cadastro_grupo_acesso;
	        $_SESSION['menu_parametros'] = $registro_tabela->array_menu_parametro_grupo_acesso;

		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
    }
}
else {
	$usuario = mysqli_query($conector_acesso,"SELECT * FROM usuario 
		                    WHERE cpf_usuario = '$cpf_usuario' AND lixeira_usuario=0");
    $num_registros = mysqli_num_rows ($usuario); 

    if ($num_registros!=0){
    	header('Content-type: application/json');
    	echo json_encode(array('error' => true, 'message' => 'Usuário já cadastrado com esse CPF/CNPJ'));
    	exit;
    }
    else {
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
			foto_usuario,
			local_usuario,
			incluido_em_usuario,
			incluido_por_usuario,
			alterado_em_usuario,
			alterado_por_usuario,
			lixeira_usuario,
			lixeira_em_usuario,
			lixeira_por_usuario,
			situacao_usuario,
			autoriza_redefinir_senha_usuario
        ) 
		VALUES (
		    '$cpf_usuario',
		    '$id_cliente',
		    '$email',
			'$nome_usuario',
		    '$grupo',
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    null,
		    '$senha_conf',
		    null,
		    null,
		    null,
		    null,
		    null,
		    '$local',
	        '$data_sistema',
	        '$nomeusuario',
	        null,
	        null,
	        0,
	        null,
	        null,
		    'A',
	        'N'
		)";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Registro incluido com sucesso.');
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na inclusao do registro' . $erro_mysql));
		} 
		else {
		   	header('Content-type: application/json');
		   	echo json_encode($resposta);
		}
    }
}


mysqli_close($conector);

?>