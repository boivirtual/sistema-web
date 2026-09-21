<?php 
	function sonumero($str) {
		return preg_replace("/[^0-9]/", "", $str);
	}

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];

	$codigo = $_POST['codigo_empresa'];
	$nome = $_POST['nome_empresa'];
	$nome_fantasia = $_POST['nome_fantasia'];
	$cnpj_cpf = sonumero($_POST['documento_pessoa']);
	$insc_estadual = $_POST['insc_estadual'];
	$insc_municipal = $_POST['insc_municipal'];
	$cep = $_POST['cep_pessoa'];
	$email = $_POST['email_pessoa'];
	$ddd = $_POST['ddd_pessoa'];
	$telefone = $_POST['telefone_pessoa'];
	$contato = $_POST['contato_pessoa'];
	$tipo_pessoa = $_POST['tipo_pessoa'];
	$endereco = $_POST['endereco_pessoa'];
	$numero = $_POST['numero_pessoa'];
	$complemento = $_POST['complemento_pessoa'];
	$bairro = $_POST['bairro_pessoa'];
	$municipio = $_POST['cidade_pessoa'];
	$uf = $_POST['estado_pessoa'];
	$observacao = $_POST['observacao_pessoa'];
	$tipo_gravacao = $_POST['tipo_gravacao'];

	if (isset($_POST['controle_pesagem'])) {
		$controle_pesagem = $_POST['controle_pesagem'];
	}
	else {
		$controle_pesagem = 'I';
	}

	$host = $_POST['host'];
	$porta = $_POST['porta_host'];
	$usuario_host = $_POST['usuario_host'];
	$senha_host = $_POST['senha_host'];

	$data_sistema = date("Y-m-d H:i:s");

    if (empty($ddd)) { $ddd=0;}
    if (empty($telefone)) { $telefone=0;}
    if (empty($cep)) { $cep=0;}
    if (empty($porta)) { $porta=0;}

	include "conecta_mysql.inc";

	if ($codigo) {
	    $sql = "UPDATE tbl_empresa SET
	            tbl_empresa_nome='$nome',
	            tbl_empresa_nome_fantasia='$nome_fantasia',
	            tbl_empresa_cpf_cnpj='$cnpj_cpf',
	            tbl_empresa_tipo_pessoa='$tipo_pessoa',
	            tbl_empresa_insc_estadual='$insc_estadual',
	            tbl_empresa_insc_municipal='$insc_municipal',
	            tbl_empresa_cep='$cep',
	            tbl_empresa_endereco='$endereco',
	            tbl_empresa_numero='$numero',
	            tbl_empresa_complemento='$complemento',
	            tbl_empresa_bairro='$bairro',
	            tbl_empresa_municipio='$municipio',
	            tbl_empresa_estado = '$uf',
	            tbl_empresa_ddd='$ddd',
	            tbl_empresa_telefone='$telefone',
	            tbl_empresa_email='$email',
	            tbl_empresa_contato='$contato',
	            tbl_empresa_observacao = '$observacao',
	            tbl_empresa_alterado_em='$data_sistema',
	            tbl_empresa_alterado_por='$nomeusuario',
	            tbl_empresa_host_smtp='$host',
	            tbl_empresa_host_porta='$porta',
	            tbl_empresa_usuario_email='$usuario_host',
	            tbl_empresa_senha_email='$senha_host',
	            tbl_empresa_controle_pesagem='$controle_pesagem'
	            WHERE tbl_empresa_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Empresa alterada com sucesso.');
	} else {
	    $sql = "INSERT INTO tbl_empresa (
	        tbl_empresa_nome,
            tbl_empresa_nome_fantasia,
	        tbl_empresa_cpf_cnpj,
	        tbl_empresa_tipo_pessoa,
	        tbl_empresa_insc_estadual,
	        tbl_empresa_insc_municipal,
	        tbl_empresa_cep,
	        tbl_empresa_endereco,
	        tbl_empresa_numero,
	        tbl_empresa_complemento,
	        tbl_empresa_bairro,
	        tbl_empresa_municipio,
	        tbl_empresa_estado,
	        tbl_empresa_ddd,
	        tbl_empresa_telefone,
	        tbl_empresa_email,
	        tbl_empresa_contato,
	        tbl_empresa_observacao,
	        tbl_empresa_lixeira,
	        tbl_empresa_incluido_em,
	        tbl_empresa_incluido_por,
	        tbl_empresa_alterado_em,
	        tbl_empresa_alterado_por,
	        tbl_empresa_lixeira_em,
	        tbl_empresa_lixeira_por,
	        tbl_empresa_host_smtp,
	        tbl_empresa_host_porta,
	        tbl_empresa_usuario_email,
	        tbl_empresa_senha_email,
            tbl_empresa_controle_pesagem

	        ) VALUES (
	          '$nome',   
	          '$nome_fantasia',   
	          '$cnpj_cpf',
	          '$tipo_pessoa',
	          '$insc_estadual',
	          '$insc_municipal',
	          '$cep',
	          '$endereco',
	          '$numero',
	          '$complemento',
	          '$bairro',
	          '$municipio',
	          '$uf',
	          '$ddd',
	          '$telefone',
	          '$email',
	          '$contato',
	          '$observacao',
	          '0',
	          '$data_sistema',
	          '$nomeusuario',
	          null,
	          null,
	          null,
	          null,
	          '$host',
	          '$porta',
	          '$usuario_host',
	          '$senha_host',
	          '$controle_pesagem'
	    )";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Empresa incluída com sucesso.');
	}
	  
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao processar sua solicitação.' . $erro_mysql));
	} else {
	    header('Content-type: application/json');
	    echo json_encode($resposta);
	}

	mysqli_close($conector);
?>