<?php 
	function sonumero($str) {
		return preg_replace("/[^0-9]/", "", $str);
	}

	$codigo = $_POST['codigo_pessoa'];
	$nome = $_POST['nome_pessoa'];
	$cnpj_cpf = sonumero($_POST['documento_pessoa']);
	$insc_estadual = $_POST['insc_estadual'];
	$insc_municipal = $_POST['insc_municipal'];
	$cep = $_POST['cep_pessoa'];
	$email = $_POST['email_pessoa'];
	$ddd = $_POST['ddd_pessoa'];
	$telefone = $_POST['telefone_pessoa'];
	$contato = $_POST['contato_pessoa'];
	$cargo = $_POST['contato_cargo'];
	$tipo_pessoa = $_POST['tipo_pessoa'];
	$endereco = $_POST['endereco_pessoa'];
	$numero = $_POST['num_pessoa'];
	$complemento = $_POST['complemento_pessoa'];
	$bairro = $_POST['bairro_pessoa'];
	$municipio = $_POST['cidade_pessoa'];
	$uf = $_POST['estado_pessoa'];
	$observacao = $_POST['observacao_pessoa'];
	$tipo_gravacao = $_POST['tipo_gravacao'];
	if(isset($_POST['cliente_ativo'])) { $cliente_ativo = 'S'; } else { $cliente_ativo = 'N'; }
	$voltar = $_POST['voltar'];	

	$data_sistema = date("Y-m-d H:i:s");
	$_SESSION['abre_inclusao'] = false;

	if ($codigo) {
		$classe_escondida = $_POST['classe_cliente'];
		$cnpj_cpf_empresa = $_POST['cpf_cnpj_empresa'];

		if ($cnpj_cpf_empresa!=97174041604 && $cnpj_cpf_empresa!=71746307668 && 
		   $classe_escondida==4){
		   $classe =	$_POST['classe_cliente'];
		}
		else {
			$classe = $_POST['classe_pessoa'];
		}
	}
	else {
		$classe = $_POST['classe_pessoa'];
	}

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

    if (empty($nome)) {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe a Razaõ Social/Nome.'));
	    mysqli_close($conector);
	    exit;
    }

    if (empty($classe)) {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Selecione uma Classe.'));
	    mysqli_close($conector);
	    exit;
    }

    //if (empty($cnpj_cpf)) {
	//    header('Content-type: application/json');
	//    echo json_encode(array('error' => true, 'message' => 'Informe o CNPJ/CPF.'));
	//    mysqli_close($conector);
	//    exit;
    //}

    if (empty($cep)){$cep=0;}
    if (empty($ddd)){$ddd=0;}
    if (empty($telefone)){$telefone=0;}

    if ($classe==4) {
		if (empty($_POST['area'])) {
			$area=0.00;
		}
		else {
			$area=str_replace(',','.', str_replace('.','', $_POST['area']));
		}

		if (empty($_POST['area_util'])) {
			$area_util=0.00;
		}
		else {
			$area_util=str_replace(',','.', str_replace('.','', $_POST['area_util']));
		}

		//$localizacao = $_POST['localizacao'];
		$latitude = $_POST['latitude'];
		$longitude = $_POST['longitude'];

		if(isset($_POST['atv_pec_corte'])) { 
			$atv_pec_corte = 'S'; 
		} 
		else { 
			$atv_pec_corte = 'N'; 
		}

		if(isset($_POST['atv_pec_leite'])) { 
			$atv_pec_leite = 'S'; 
		} 
		else { 
			$atv_pec_leite = 'N'; 
		}

		if(isset($_POST['atv_agricultura'])) { 
			$atv_agricultura = 'S'; 
		} 
		else { 
			$atv_agricultura = 'N'; 
		}

		if(isset($_POST['atv_outra'])) { 
			$atv_outra = 'S'; 
		} 
		else { 
			$atv_outra = 'N'; 
		}

		$descricao_atv_agricola = $_POST['descricao_atv_agricola'];
		$descricao_atv_outra = $_POST['descricao_atv_outra'];
    }
    else {
		$area=0.00;
		$area_util=0.00;
		$latitude=0.00;
		$longitude=0.00;
		$atv_pec_corte = 'N'; 
		$atv_pec_leite = 'N'; 
		$atv_agricultura = 'N'; 
		$atv_outra = 'N'; 
		$descricao_atv_agricola = '';
		$descricao_atv_outra = '';
    }

	if ($codigo && $tipo_gravacao == '2') {
		$sql = "UPDATE tbl_pessoa SET 
	                   tbl_pessoa_lixeira=1,
	                   tbl_pessoa_lixeira_em='$data_sistema',
	                   tbl_pessoa_lixeira_por='$nomeusuario'
	                   WHERE tbl_pessoa_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Pessoa enviada para lixeira com sucesso.');

	} else if ($codigo && $tipo_gravacao == '3') { 
	    $sql = "UPDATE tbl_pessoa SET 
	                   tbl_pessoa_lixeira=0,
	                   tbl_pessoa_lixeira_em=null,
	                   tbl_pessoa_lixeira_por=null
	                   WHERE tbl_pessoa_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Pessoa restaurada com sucesso.');

	} else if ($codigo) {
	    $sql = "UPDATE tbl_pessoa SET
				tbl_pessoa_classe='$classe',
				tbl_pessoa_cpf_cnpj='$cnpj_cpf',
				tbl_pessoa_tipo_pessoa='$tipo_pessoa',
				tbl_pessoa_insc_estadual='$insc_estadual',
				tbl_pessoa_insc_municipal='$insc_municipal',
				tbl_pessoa_nome='$nome',
				tbl_pessoa_contato='$contato',
				tbl_pessoa_cargo_contato='$cargo',
				tbl_pessoa_ddd='$ddd',
				tbl_pessoa_telefone='$telefone',
				tbl_pessoa_email='$email',
				tbl_pessoa_cep='$cep',
				tbl_pessoa_endereco='$endereco',
				tbl_pessoa_numero='$numero',
				tbl_pessoa_complemento='$complemento',
				tbl_pessoa_bairro='$bairro',
				tbl_pessoa_municipio='$municipio',
				tbl_pessoa_estado='$uf',
	            tbl_pessoa_alterado_em='$data_sistema',
	            tbl_pessoa_alterado_por='$nomeusuario',
				tbl_pessoa_observacao='$observacao',
				tbl_pessoa_ativo='$cliente_ativo',
				tbl_pessoa_area_fazenda='$area',
				tbl_pessoa_area_util_fazenda='$area_util',
				tbl_pessoa_latitude_fazenda='$latitude',
				tbl_pessoa_longitude_fazenda='$longitude',
				tbl_pessoa_atv_pec_corte='$atv_pec_corte',
				tbl_pessoa_atv_pec_leite='$atv_pec_leite',
				tbl_pessoa_atv_agricultura='$atv_agricultura',
				tbl_pessoa_atv_outra='$atv_outra',
				tbl_pessoa_descricao_atv_agricola='$descricao_atv_agricola',
				tbl_pessoa_descricao_atv_outra='$descricao_atv_outra'
           
	            WHERE tbl_pessoa_id='$codigo'";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Pessoa alterada com sucesso.');

	} else {
	    $sql = "INSERT INTO tbl_pessoa (
				tbl_pessoa_classe,
				tbl_pessoa_cpf_cnpj,
				tbl_pessoa_tipo_pessoa,
				tbl_pessoa_insc_estadual,
				tbl_pessoa_insc_municipal,
				tbl_pessoa_nome,
				tbl_pessoa_contato,
				tbl_pessoa_cargo_contato,
				tbl_pessoa_ddd,
				tbl_pessoa_telefone,
				tbl_pessoa_email,
				tbl_pessoa_cep,
				tbl_pessoa_endereco,
				tbl_pessoa_numero,
				tbl_pessoa_complemento,
				tbl_pessoa_bairro,
				tbl_pessoa_municipio,
				tbl_pessoa_estado,
				tbl_pessoa_lixeira,
				tbl_pessoa_incluido_em,
				tbl_pessoa_incluido_por,
				tbl_pessoa_lixeira_em,
				tbl_pessoa_lixeira_por,
				tbl_pessoa_alterado_em,
				tbl_pessoa_alterado_por,
				tbl_pessoa_observacao,
				tbl_pessoa_ativo,
				tbl_pessoa_area_fazenda,
				tbl_pessoa_area_util_fazenda,
				tbl_pessoa_latitude_fazenda,
				tbl_pessoa_longitude_fazenda,
				tbl_pessoa_atv_pec_corte,
				tbl_pessoa_atv_pec_leite,
				tbl_pessoa_atv_agricultura,
				tbl_pessoa_atv_outra,
				tbl_pessoa_descricao_atv_agricola,
				tbl_pessoa_descricao_atv_outra
        ) VALUES (
			'$classe', 
	        '$cnpj_cpf',
	        '$tipo_pessoa',
	        '$insc_estadual',
	        '$insc_municipal',
			'$nome', 
	        '$contato',
	        '$cargo',
	        '$ddd',
	        '$telefone',
	        '$email',
	        '$cep',
	        '$endereco',
	        '$numero',
	        '$complemento',
	        '$bairro',
	        '$municipio',
	        '$uf',
	        '0',
	        '$data_sistema',
	        '$nomeusuario',
			null,
			null,
			null,
			null,
	        '$observacao',
	        'S',
	        '$area',
	        '$area_util',
			'$latitude',
			'$longitude',
	        '$atv_pec_corte',
	        '$atv_pec_leite',
	        '$atv_agricultura',
	        '$atv_outra',
	        '$descricao_atv_agricola',
	        '$descricao_atv_outra'

	    )";
	    $resultado = mysqli_query($conector,$sql);
	    $resposta = array('success' => true, 'message' => 'Pessoa incluída com sucesso.');
	}
	  
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao processar sua solicitação.'));
	} else {
		$ultimo_cliente =  mysqli_insert_id($conector);

		if ($voltar==1 || $voltar==2 || $voltar==3 || $voltar==4 || $voltar==5 || $voltar==6) {
			$_SESSION['ultimo_cliente_cadastrado'] = $ultimo_cliente;
			$_SESSION['voltar_movimentacao'] = $voltar;
		}
		else {
			$_SESSION['ultimo_cliente_cadastrado'] = 0;
			$_SESSION['voltar_movimentacao'] = '';
		}

	    header('Content-type: application/json');
	    echo json_encode($resposta);
	}

	mysqli_close($conector);
?>