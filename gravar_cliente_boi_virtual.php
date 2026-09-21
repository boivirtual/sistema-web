<?php
function sonumero($str)
{
	return preg_replace("/[^0-9]/", "", $str);
}
$obj = json_decode($_POST['data']);

$tipo_gravacao = $obj->tipo_gravacao;

$data_sistema = date("Y-m-d H:i:s");

include "conecta_mysql.inc";

$codigo = 0;

if ($codigo && $tipo_gravacao == '3') {
	$sql = "UPDATE tbl_pessoa SET 
					tbl_pessoa_lixeira=1,
					tbl_pessoa_lixeira_em='$data_sistema',
					tbl_pessoa_lixeira_por='$nomeusuario'
					WHERE tbl_pessoa_id='$codigo'";
	$resultado = mysqli_query($conector, $sql);
	$resposta = array('success' => true, 'message' => 'Dados enviado para lixeira com sucesso.');
} else if ($codigo && $tipo_gravacao == '4') {
	$sql = "UPDATE tbl_pessoa SET 
					tbl_pessoa_lixeira=0,
					tbl_pessoa_lixeira_em=null,
					tbl_pessoa_lixeira_por=null
					WHERE tbl_pessoa_id='$codigo'";
	$resultado = mysqli_query($conector, $sql);
	$resposta = array('success' => true, 'message' => 'dados restaurado com sucesso.');
} else if ($codigo && $tipo_gravacao == '2') {
	$sql = "UPDATE tbl_cliente_boi_virtual SET
		tbl_cliente_nome_empresa='$nome_empresa',
		tbl_cliente_nome_fantasia_empresa='$nome_fantasia',
		tbl_cliente_cpf_cnpj_empresa='$cpf_cnpj',
		tbl_cliente_insc_estadual_empresa='$insc_est',
		tbl_cliente_cep_empresa='$cep_pessoa',
		tbl_cliente_endereco_empresa='$endereco_pessoa',
		tbl_cliente_numero_empresa='$num_pessoa',
		tbl_cliente_complemento_empresa='$complemento_pessoa',
		tbl_cliente_bairro_empresa='$bairro_pessoa',
		tbl_cliente_municipio_empresa='$municipio_pessoa',
		tbl_cliente_estado_empresa='$estado_pessoa',
		tbl_cliente_nome_adm='$nome_adm',
		tbl_cliente_cpf_adm='$cpf_adm',
		tbl_cliente_ddd_adm='$ddd_adm',
		tbl_cliente_telefone_adm='$telefone_adm',
		tbl_cliente_email_adm='$email_adm',

		tbl_cliente_nome_fazenda_01='$nome_fazenda_01',
		tbl_cliente_cpf_cnpj_fazenda_01='$cpf_cnpj_01',
		tbl_cliente_insc_est_fazenda_01='$insc_est_01',
		tbl_cliente_cep_fazenda_01='$cep_01',
		tbl_cliente_endereco_fazenda_01='$endereco_01',
		tbl_cliente_numero_fazenda_01='$num_01',
		tbl_cliente_complemento_fazenda_01='$complemento_01',
		tbl_cliente_bairro_fazenda_01='$bairro_01',
		tbl_cliente_municipio_fazenda_01='$municipio_01',
		tbl_cliente_estado_fazenda_01='$estado_01',

		tbl_cliente_area_fazenda_01='$area_01',
		tbl_cliente_area_util_fazenda_01='$area_util_01',
		tbl_cliente_localizacao_fazenda_01='$localizacao_01',
		tbl_cliente_atv_pec_corte_fazenda_01='$atv_pec_corte_01',
		tbl_cliente_atv_pec_leite_fazenda_01='$atv_pec_leite_01',
		tbl_cliente_atv_agricultura_fazenda_01='$atv_agricultura_01',
		tbl_cliente_atv_outra_fazenda_01='$atv_outra_01',
		tbl_cliente_descricao_atv_agricola_fazenda_01='$descricao_atv_agricola_01',
		tbl_cliente_descricao_atv_outra_fazenda_01='$descricao_atv_outra_01',

		tbl_cliente_nome_fazenda_02='$nome_fazenda_02',
		tbl_cliente_cpf_cnpj_fazenda_02='$cpf_cnpj_02',
		tbl_cliente_insc_est_fazenda_02='$insc_est_02',
		tbl_cliente_cep_fazenda_02='$cep_02',
		tbl_cliente_endereco_fazenda_02='$endereco_02',
		tbl_cliente_numero_fazenda_02='$num_02',
		tbl_cliente_complemento_fazenda_02='$complemento_02',
		tbl_cliente_bairro_fazenda_02='$bairro_02',
		tbl_cliente_municipio_fazenda_02='$municipio_02',
		tbl_cliente_estado_fazenda_02='$estado_02',

		tbl_cliente_area_fazenda_02='$area_02',
		tbl_cliente_area_util_fazenda_02='$area_util_02',
		tbl_cliente_localizacao_fazenda_02='$localizacao_02',
		tbl_cliente_atv_pec_corte_fazenda_02='$atv_pec_corte_02',
		tbl_cliente_atv_pec_leite_fazenda_02='$atv_pec_leite_02',
		tbl_cliente_atv_agricultura_fazenda_02='$atv_agricultura_02',
		tbl_cliente_atv_outra_fazenda_02='$atv_outra_02',
		tbl_cliente_descricao_atv_agricola_fazenda_02='$descricao_atv_agricola_02',
		tbl_cliente_descricao_atv_outra_fazenda_02='$descricao_atv_outra_02',

		tbl_cliente_nome_fazenda_03='$nome_fazenda_03',
		tbl_cliente_cpf_cnpj_fazenda_03='$cpf_cnpj_03',
		tbl_cliente_insc_est_fazenda_03='$insc_est_03',
		tbl_cliente_cep_fazenda_03='$cep_03',
		tbl_cliente_endereco_fazenda_03='$endereco_03',
		tbl_cliente_numero_fazenda_03='$num_03',
		tbl_cliente_complemento_fazenda_03='$complemento_03',
		tbl_cliente_bairro_fazenda_03='$bairro_03',
		tbl_cliente_municipio_fazenda_03='$municipio_03',
		tbl_cliente_estado_fazenda_03='$estado_03',

		tbl_cliente_area_fazenda_03='$area_03',
		tbl_cliente_area_util_fazenda_03='$area_util_03',
		tbl_cliente_localizacao_fazenda_03='$localizacao_03',
		tbl_cliente_atv_pec_corte_fazenda_03='$atv_pec_corte_03',
		tbl_cliente_atv_pec_leite_fazenda_03='$atv_pec_leite_03',
		tbl_cliente_atv_agricultura_fazenda_03='$atv_agricultura_03',
		tbl_cliente_atv_outra_fazenda_03='$atv_outra_03',
		tbl_cliente_descricao_atv_agricola_fazenda_03='$descricao_atv_agricola_03',
		tbl_cliente_descricao_atv_outra_fazenda_03='$descricao_atv_outra_03',

		tbl_cliente_nome_fazenda_04='$nome_fazenda_04',
		tbl_cliente_cpf_cnpj_fazenda_04='$cpf_cnpj_04',
		tbl_cliente_insc_est_fazenda_04='$insc_est_04',
		tbl_cliente_cep_fazenda_04='$cep_04',
		tbl_cliente_endereco_fazenda_04='$endereco_04',
		tbl_cliente_numero_fazenda_04='$num_04',
		tbl_cliente_complemento_fazenda_04='$complemento_04',
		tbl_cliente_bairro_fazenda_04='$bairro_04',
		tbl_cliente_municipio_fazenda_04='$municipio_04',
		tbl_cliente_estado_fazenda_04='$estado_04',

		tbl_cliente_area_fazenda_04='$area_04',
		tbl_cliente_area_util_fazenda_04='$area_util_04',
		tbl_cliente_localizacao_fazenda_04='$localizacao_04',
		tbl_cliente_atv_pec_corte_fazenda_04='$atv_pec_corte_04',
		tbl_cliente_atv_pec_leite_fazenda_04='$atv_pec_leite_04',
		tbl_cliente_atv_agricultura_fazenda_04='$atv_agricultura_04',
		tbl_cliente_atv_outra_fazenda_04='$atv_outra_04',
		tbl_cliente_descricao_atv_agricola_fazenda_04='$descricao_atv_agricola_04',
		tbl_cliente_descricao_atv_outra_fazenda_04='$descricao_atv_outra_04',

		tbl_cliente_nome_fazenda_05='$nome_fazenda_05',
		tbl_cliente_cpf_cnpj_fazenda_05='$cpf_cnpj_05',
		tbl_cliente_insc_est_fazenda_05='$insc_est_05',
		tbl_cliente_cep_fazenda_05='$cep_05',
		tbl_cliente_endereco_fazenda_05='$endereco_05',
		tbl_cliente_numero_fazenda_05='$num_05',
		tbl_cliente_complemento_fazenda_05='$complemento_05',
		tbl_cliente_bairro_fazenda_05='$bairro_05',
		tbl_cliente_municipio_fazenda_05='$municipio_05',
		tbl_cliente_estado_fazenda_05='$estado_05',

		tbl_cliente_area_fazenda_05='$area_05',
		tbl_cliente_area_util_fazenda_05='$area_util_05',
		tbl_cliente_localizacao_fazenda_05='$localizacao_05',
		tbl_cliente_atv_pec_corte_fazenda_05='$atv_pec_corte_05',
		tbl_cliente_atv_pec_leite_fazenda_05='$atv_pec_leite_05',
		tbl_cliente_atv_agricultura_fazenda_05='$atv_agricultura_05',
		tbl_cliente_atv_outra_fazenda_05='$atv_outra_05',
		tbl_cliente_descricao_atv_agricola_fazenda_05='$descricao_atv_agricola_05',
		tbl_cliente_descricao_atv_outra_fazenda_05='$descricao_atv_outra_05',

		tbl_cliente_alterado_em='$data_sistema',
		tbl_cliente_alterado_por='$nome_adm',
		tbl_cliente_controle_estoque='$controle_estoque'
	WHERE tbl_cliente_id ='$codigo'";
	$resultado = mysqli_query($conector, $sql);

	if ($opcao_validar == 1) {
		$resposta = array('success' => true, 'message' => 'Dados alterados com sucesso. Vou validar');
	} else {
		$resposta = array('success' => true, 'message' => 'Dados alterados com sucesso.');
	}
} else {
	$obj->empresa->cpf_cnpj = sonumero($obj->empresa->cpf_cnpj);
	$obj->user->userp_cpf = sonumero($obj->user->userp_cpf);

	$sql = "INSERT INTO tbl_cliente_boi_virtual(
		tbl_cliente_ativo,
		tbl_cliente_nome_empresa,
		tbl_cliente_nome_fantasia_empresa,
		tbl_cliente_cpf_cnpj_empresa,
		tbl_cliente_insc_estadual_empresa,
		tbl_cliente_cep_empresa,
		tbl_cliente_endereco_empresa,
		tbl_cliente_numero_empresa,
		tbl_cliente_complemento_empresa,
		tbl_cliente_bairro_empresa,
		tbl_cliente_municipio_empresa,
		tbl_cliente_estado_empresa,
		tbl_cliente_nome_adm,
		tbl_cliente_cpf_adm,
		tbl_cliente_ddd_adm,
		tbl_cliente_telefone_adm,
		tbl_cliente_email_adm,
		tbl_cliente_lixeira,
		tbl_cliente_incluido_em,
		tbl_cliente_incluido_por,
		tbl_cliente_alterado_em,
		tbl_cliente_alterado_por,
		tbl_cliente_lixeira_em,
		tbl_cliente_lixeira_por,
		tbl_cliente_validado,
		tbl_cliente_validado_em,
		tbl_cliente_validado_por,
		tbl_cliente_controle_estoque
	) VALUES (
		'N',
		'{$obj->empresa->nome_empresa}',
		'{$obj->empresa->nome_fantasia}',
		'{$obj->empresa->cpf_cnpj}',
		'{$obj->empresa->insc_est}',
		'{$obj->empresa->cep_pessoa}',
		'{$obj->empresa->endereco_pessoa}',
		'{$obj->empresa->num_pessoa}',
		'{$obj->empresa->complemento_pessoa}',
		'{$obj->empresa->bairro_pessoa}',
		'{$obj->empresa->cidade_pessoa}',
		'{$obj->empresa->estado_pessoa}',
		'{$obj->user->userp_nome}',
		'{$obj->user->userp_cpf}',
		'{$obj->user->userp_ddd}',
		'{$obj->user->userp_telefone}',
		'{$obj->user->userp_email}',
		0,
		'{$data_sistema}',
		'{$obj->user->userp_nome}',
		null,
		null,
		null,
		null,
		'N',
		null,
		null,
		'{$obj->empresa->controle_estoque}'
	)";

	$resultado = mysqli_query($conector, $sql);
	if (!$resultado) {
		$err = mysqli_error($conector);
		echo json_encode(
			array(
				"error" => true,
				"message" => "Ocorreu um erro ao processar sua solicitação. {$err}"
			)
		);
		exit;
	}

	$cliente_id = mysqli_insert_id($conector);

	for ($i = 0; $i < $obj->empresa->qtdeFazenda; $i++) {
		$obj->fazenda[$i]->fazenda_cpf_cnpj = sonumero($obj->fazenda[$i]->fazenda_cpf_cnpj);
		$obj->fazenda[$i]->atv_pec_corte = ($obj->fazenda[$i]->atv_pec_corte == 'true') ? "S" : "N";
		$obj->fazenda[$i]->atv_pec_leite = ($obj->fazenda[$i]->atv_pec_leite == 'true') ? "S" : "N";
		$obj->fazenda[$i]->atv_agricultura = ($obj->fazenda[$i]->atv_agricultura == 'true') ? "S" : "N";
		$obj->fazenda[$i]->atv_outra = ($obj->fazenda[$i]->atv_outra == 'true') ? "S" : "N";

		if (empty($obj->fazenda[$i]->fazenda_area)) {
			$obj->fazenda[$i]->fazenda_area=0.00;
		}
		else {
			$obj->fazenda[$i]->fazenda_area=str_replace(',','.', str_replace('.','', $obj->fazenda[$i]->fazenda_area));
		}

		if (empty($obj->fazenda[$i]->fazenda_area_util)) {
			$obj->fazenda[$i]->fazenda_area_util=0.00;
		}
		else {
			$obj->fazenda[$i]->fazenda_area_util=str_replace(',','.', str_replace('.','', $obj->fazenda[$i]->fazenda_area_util));
		}

		if (empty($obj->fazenda[$i]->fazenda_latitude)) {
			$obj->fazenda[$i]->fazenda_latitude=0.00;
		}
		
		if (empty($obj->fazenda[$i]->fazenda_longitude)) {
			$obj->fazenda[$i]->fazenda_longitude=0.00;
		}
		
		$sql = "INSERT INTO tbl_cliente_fazenda (
			nome,
			cpf_cnpj,
			insc_est,
			cep,
			endereco,
			numero,
			complemento,
			bairro,
			municipio,
			estado,
			area,
			area_util,
			latitude,
			longitude,
			atv_pec_corte,
			atv_pec_leite,
			atv_agricultura,
			atv_outra,
			descricao_atv_agricola,
			descricao_atv_outra,
			incluido_em,
			incluido_por,
			alterado_em,
			alterado_por,
			lixeira,
			lixeira_em,
			lixeira_por,
			cliente_id
		) VALUES (
			'{$obj->fazenda[$i]->nome_fazenda}',
			'{$obj->fazenda[$i]->fazenda_cpf_cnpj}',
			'{$obj->fazenda[$i]->fazenda_insc_est}',
			'{$obj->fazenda[$i]->fazenda_cep}',
			'{$obj->fazenda[$i]->fazenda_endereco}',
			'{$obj->fazenda[$i]->fazenda_num}',
			'{$obj->fazenda[$i]->fazenda_complemento}',
			'{$obj->fazenda[$i]->fazenda_bairro}',
			'{$obj->fazenda[$i]->fazenda_cidade}',
			'{$obj->fazenda[$i]->fazenda_estado}',
			'{$obj->fazenda[$i]->fazenda_area}',
			'{$obj->fazenda[$i]->fazenda_area_util}',
			'{$obj->fazenda[$i]->fazenda_latitude}',
			'{$obj->fazenda[$i]->fazenda_longitude}',
			'{$obj->fazenda[$i]->atv_pec_corte}',
			'{$obj->fazenda[$i]->atv_pec_leite}',
			'{$obj->fazenda[$i]->atv_agricultura}',
			'{$obj->fazenda[$i]->atv_outra}',
			'{$obj->fazenda[$i]->descricao_atv_agricola}',
			'{$obj->fazenda[$i]->descricao_atv_outra}',
			'{$data_sistema}',
			'{$obj->user->userp_nome}',
			null,
			null,
			0,
			null,
			null,
			'{$cliente_id}'
		)";
		$resultado = mysqli_query($conector, $sql);

		if (!$resultado) {
			header("content-type: application/json");
			$err = mysqli_error($conector);
			echo json_encode(
				array(
					"error" => true,
					"message" => "Ocorreu um erro ao processar sua solicitação. {$err}"
				)
			);
			exit;
		}
	}

	$resposta = array(
		'success' => true,
		'message' => 'Formulário enviado com sucesso. Os dados serão validados em até 1 dia útil. Em seguida, você receberá usuário e senha via e-mail cadastrado.'
	);
}

//$erro_mysql = mysqli_error($conector);

//if (!$resultado) {
//	header('Content-type: application/json');
//	echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro ao processar sua solicitação.'));
//} else {
	header('Content-type: application/json');
	echo json_encode($resposta);
//}

mysqli_close($conector);
