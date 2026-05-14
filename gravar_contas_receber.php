<?php

function sonumero($str)
{
	return preg_replace("/[^0-9]/", "", $str);
}

$tipo_operacao = $_POST['tipo_operacao'];
$id_ctr = $_POST['id_ctr'];
$observacao = $_POST['observacao'];
$codigo_cli_for = $_POST['codigo_cli_for'];
$nome_cli = $_POST['nome_cli'];
$numero_doc = $_POST['number_doc'];
$tipo_documento = $_POST['tipo_doc'];
$data_emissao = $_POST['data_emissao'];
$codigo_c_custo = $_POST['codigo_cc'];

if ($tipo_operacao==1) {
	$local = $_POST['codigo_local'];
}
else {
	$local = $_POST['codigo_local_editar'];
}

if (empty($observacao)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe Descrição.'));
	exit;
}

if ($local == '000000000') {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Local.'));
	exit;
}

if (empty($codigo_cli_for)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Cliente/ Parceiro.'));
	exit;
}

if ($codigo_cli_for == 999999999 && empty($nome_cli)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe o Cliente/ Parceiro ou Razão/Nome para cliente não cadastrado.'));
	exit;
}

if (empty($data_emissao)) {
	header('Content-type: application/json');
	echo json_encode(array('error' => true, 'message' => 'Informe a Data de Emissão.'));
	exit;
}

if ($tipo_operacao == 1) {
	$conta_primeira_parcela = $_POST['conta_primeira_parcela'];
	$vencimento_primeira_parcela = $_POST['vencimento_primeira_parcela'];
	$forma_pgto_primeira_parcela = $_POST['forma_pgto_primeira_parcela'];
	$conta_pgto_primeira_parcela = $_POST['conta_pgto_primeira_parcela'];

	if (isset($_POST['repetir'])) {
		$repetir = 'S';
	} else {
		$repetir = 'N';
	}
	$frequencia = $_POST['frequencia'];
	$ocorrencias = $_POST['ocorrencias'];
	$conta_mensal = $_POST['conta_mensal'];
	$data_inicial = $_POST['data_inicial'];
	$forma_pgto_mensal = $_POST['forma_pgto_mensal'];
	$conta_pgto_mensal = $_POST['conta_pgto_mensal'];

	if (isset($_POST['pago'])) {
		$pago = 'S';
	} else {
		$pago = 'N';
	}
	$data_pagamento = $_POST['data_pagamento'];
	$vlr_juros = $_POST['vlr_juros'];
	$vlr_desconto = $_POST['vlr_desconto'];

	if (empty($_POST['vlr_parcela'])) {
		$vlr_parcela = 0.00;
	} else {
		$vlr_parcela = str_replace(',', '.', str_replace('.', '', $_POST['vlr_parcela']));
	}

	if (empty($_POST['valor_mensal'])) {
		$valor_mensal = 0.00;
	} else {
		$valor_mensal = str_replace(',', '.', str_replace('.', '', $_POST['valor_mensal']));
	}

	if (empty($_POST['vlr_pagamento'])) {
		$vlr_pagamento = 0.00;
	} else {
		$vlr_pagamento = str_replace(',', '.', str_replace('.', '', $_POST['vlr_pagamento']));
	}

	if (empty($_POST['vlr_juros'])) {
		$vlr_juros = 0.00;
	} else {
		$vlr_juros = str_replace(',', '.', str_replace('.', '', $_POST['vlr_juros']));
	}

	if (empty($_POST['vlr_desconto'])) {
		$vlr_desconto = 0.00;
	} else {
		$vlr_desconto = str_replace(',', '.', str_replace('.', '', $_POST['vlr_desconto']));
	}

	if (empty($vlr_parcela)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe o Valor da 1ª Parcela.'));
		exit;
	}

	if (empty($vencimento_primeira_parcela)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Data de Vencimento da 1ª Parcela.'));
		exit;
	}

	if (empty($conta_primeira_parcela)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Conta Contábil da 1ª Parcela.'));
		exit;
	}

	if ($forma_pgto_primeira_parcela == 0) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Forma de Pagamento da 1ª Parcela.'));
		exit;
	}

	if ($conta_pgto_primeira_parcela == 0) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Conta de Pagamento da 1ª Parcela.'));
		exit;
	}

	if ($pago == "S") {
		if (empty($data_pagamento)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Data de Pagamento da 1ª Parcela.'));
			exit;
		}
		if (empty($vlr_pagamento)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe o Valor de Pagamento da 1ª Parcela.'));
			exit;
		}
	}

	if ($repetir == 'S') {
		if (empty($frequencia)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Frequência das parcelas.'));
			exit;
		}

		if (empty($ocorrencias)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe o Número de Ocorrências das parcelas.'));
			exit;
		}

		if (empty($valor_mensal)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe o Valor das parcelas.'));
			exit;
		}

		if (empty($data_inicial)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Data Inicial das parcelas.'));
			exit;
		}

		if (empty($conta_mensal)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Conta Contábil das parcelas.'));
			exit;
		}

		if ($forma_pgto_mensal == 0) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Forma de Pagamento das parcelas.'));
			exit;
		}

		if ($conta_pgto_mensal == 0) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Conta de Pagamento das parcelas.'));
			exit;
		}
	} else {
		if (empty($data_inicial)) {
			$data_inicial = null;
		}

		if (empty($ocorrencias)) {
			$ocorrencias = 0;
		}
	}
} else {
	$numero_parcela = $_POST['number_parcela'];
	$codigo_conta = $_POST['codigo_conta'];
	$data_vencimento = $_POST['data_vencimento'];
	$codigo_forma_rec = $_POST['codigo_forma_rec'];
	$codigo_conta_rec = $_POST['codigo_conta_rec'];
	$numero_cheque = $_POST['number_cheque'];
	$desc_juros = $_POST['desc_juros'];
	$desc_desconto = $_POST['desc_desconto'];
	$desc_acrescimo = $_POST['desc_acrescimo'];

	if (empty($codigo_conta)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Conta.'));
		exit;
	}

	if (empty($data_vencimento)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Data do Vencimento.'));
		exit;
	}

	if ($data_vencimento < $data_emissao) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'A Data do Vencimento não pode ser menor = a Data de Emissão.'));
		exit;
	}

	if (empty($_POST['vlr_parcela'])) {
		$vlr_parcela = 0.00;
	} else {
		$vlr_parcela = str_replace(',', '.', str_replace('.', '', $_POST['vlr_parcela']));
	}

	if ($vlr_parcela == 0.00) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe o Valor da Parcela.'));
		exit;
	}

	if (empty($_POST['vlr_juros'])) {
		$vlr_juros = 0.00;
	} else {
		$vlr_juros = str_replace(',', '.', str_replace('.', '', $_POST['vlr_juros']));
	}

	if (empty($_POST['vlr_desconto'])) {
		$vlr_desconto = 0.00;
	} else {
		$vlr_desconto = str_replace(',', '.', str_replace('.', '', $_POST['vlr_desconto']));
	}

	if (empty($_POST['vlr_acrescimo'])) {
		$vlr_acrescimo = 0.00;
	} else {
		$vlr_acrescimo = str_replace(',', '.', str_replace('.', '', $_POST['vlr_acrescimo']));
	}
}

include "conecta_mysql.inc";
$data_sistema = date("Y-m-d H:i:s");

if ($codigo_cli_for != 999999999) {
	$rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
	                                           WHERE tbl_pessoa_id='$codigo_cli_for'");
	$fila = mysqli_fetch_object($rs);
	$nome_cliente = $fila->tbl_pessoa_nome;
} else {
	$nome_cliente = $_POST['nome_cli'];
}

@session_start();
$nomeusuario = $_SESSION['nome_usuario'];

if ($tipo_operacao == 1) {

	$qtd_parcela = $ocorrencias + 1;
	$numero_parcela = 1;

	if ($numero_doc == 0 || $numero_doc == '') {
		do {
			$data_doc = date("y/m/d");
			$numero_randomico = mt_rand();
			$numero_quatro_digitos = substr($numero_randomico, 0, 4);
			$numero_doc = sonumero($data_doc) . $numero_quatro_digitos;

			$rs = mysqli_query($conector, "SELECT ctr_numero_doc
		        	                  		   	  FROM contas_receber
		        	                             WHERE ctr_numero_doc ='$numero_doc'");

			$num_rows_ctr = mysqli_num_rows($rs);
		} while ($num_rows_ctr == 1);
	}

	$sql = "INSERT INTO contas_receber (
			ctr_numero_doc,
			ctr_parcela,
			ctr_qtd_parcelas,
			ctr_tipo,
			ctr_ano_base,
			ctr_semestre,
			ctr_codigo_cliente_fornecedor,
			ctr_nome_cliente,
			ctr_codigo_fazenda,
			ctr_codigo_forma_recebimento,
			ctr_codigo_conta_recebimento,
			ctr_codigo_conta,
			ctr_codigo_c_custo,
			ctr_codigo_banco,
			ctr_data_emissao,
			ctr_data_vencimento,
			ctr_valor_parcela,
			ctr_valor_juros,
			ctr_descricao_juros,
			ctr_valor_desconto,
			ctr_descricao_desconto,
			ctr_valor_acrescimo,
			ctr_descricao_acrescimo,
			ctr_situacao,
			ctr_aceite,
			ctr_usuario_aceite,
			ctr_data_aceite,
			ctr_numero_cheque,
			ctr_cobranca_processada,
			ctr_nosso_numero,
			ctr_nome_remessa,
			ctr_carteira,
			ctr_variacao,
			ctr_observacao,
			ctr_usuario_juros,
			ctr_data_juros,
			ctr_aceite_juros,
			ctr_alterado_em,
			ctr_alterado_por,
			ctr_incluido_em,
			ctr_incluido_por,
			ctr_lixeira_em,
			ctr_lixeira_por,
			ctr_lixeira

		) VALUES (
			'$numero_doc',
			'$numero_parcela',
			'$qtd_parcela',
			'$tipo_documento',
			null,
			null,
			'$codigo_cli_for',
			'$nome_cliente',
			'$local',
			'$forma_pgto_primeira_parcela',
			'$conta_pgto_primeira_parcela',
			'$conta_primeira_parcela',
			'$codigo_c_custo',
			null,
			'$data_emissao',
			'$vencimento_primeira_parcela',
			'$vlr_parcela',
			null,
			null,
			null,
			null,
			null,
			null,
			'',
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			'$observacao',
			null,
			null,
			null,
			null,
			null,
			'$data_sistema',
			'$nomeusuario',
			null,
			null,
			0
		    )";

	$resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao incluir a primeira parcela.' .  $erro_mysql));
		exit;
	}

	$id_ctr = mysqli_insert_id($conector);
	$id_ctr = str_pad($id_ctr, 10, "0", STR_PAD_LEFT);

	if ($pago == "S") {
		$historico = "Recebimento total de: " . $nome_cliente;

		$sql = "INSERT INTO baixa_contas_receber (
										bcr_id,
		        						bcr_numero_doc,
										bcr_parcela,
										bcr_sequencia,
										bcr_tipo,
										bcr_codigo_cliente_fornecedor,
										bcr_nome_cliente,
										bcr_data_pagamento,
										bcr_valor_pagamento,
										bcr_valor_juros,
										bcr_valor_desconto,
										bcr_valor_acrescimo,
										bcr_descricao_acrescimo,
										bcr_usuario_aceite,
										bcr_data_aceite,
										bcr_historico,
										bcr_situacao,
										bcr_usuario_aceite_pagamento,
										bcr_data_aceite_pagamento,
										bcr_comissao_paga)
			           VALUES ('$id_ctr',
			           		   '$numero_doc', 
							   '$numero_parcela',
							   1,
							   '$tipo_documento',
					           '$codigo_cli_for',
							   '$nome_cliente',
				               '$data_pagamento',
							   '$vlr_pagamento',
							   '$vlr_juros',
							   '$vlr_desconto',
							   null,
							   null,
							   '$nomeusuario',
							   '$data_sistema',
							   '$historico',
							   'P',
							   null,
							   null,
							   null)";

		$resultado = mysqli_query($conector, $sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a baixa da conta.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}

		$sql = ("UPDATE contas_receber SET 
			ctr_situacao='P',
	    	ctr_valor_juros='$vlr_juros',
	    	ctr_valor_desconto='$vlr_desconto' 
	    	WHERE ctr_id ='$id_ctr'");

		$resultado = mysqli_query($conector, $sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a baixa da conta no ctr.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
	}

	if ($repetir == "S") {
		for ($i = 1; $i <= $ocorrencias; $i++) {
			if ($i == 1) {
				$data_vencimento = $data_inicial;
			} else {
				switch ($frequencia) {
					case 1:
						$data_vencimento = date("Y-m-d", strtotime('+1 day', strtotime($data_vencimento)));
						break;
					case 2:
						$data_vencimento = date("Y-m-d", strtotime('+1 week', strtotime($data_vencimento)));
						break;
					case 3:
						$data_vencimento = date("Y-m-d", strtotime('+2 week', strtotime($data_vencimento)));
						break;
					case 4:
						$data_vencimento = date("Y-m-d", strtotime('+1 month', strtotime($data_vencimento)));
						break;
					case 5:
						$data_vencimento = date("Y-m-d", strtotime('+2 month', strtotime($data_vencimento)));
						break;
					case 6:
						$data_vencimento = date("Y-m-d", strtotime('+3 month', strtotime($data_vencimento)));
						break;
					case 7:
						$data_vencimento = date("Y-m-d", strtotime('+6 month', strtotime($data_vencimento)));
						break;
					case 8:
						$data_vencimento = date("Y-m-d", strtotime('+12 month', strtotime($data_vencimento)));
						break;
				}
			}

			$numero_parcela++;

			$sql = "INSERT INTO contas_receber (
					ctr_numero_doc,
					ctr_parcela,
					ctr_qtd_parcelas,
					ctr_tipo,
					ctr_ano_base,
					ctr_semestre,
					ctr_codigo_cliente_fornecedor,
					ctr_nome_cliente,
					ctr_codigo_fazenda,
					ctr_codigo_forma_recebimento,
					ctr_codigo_conta_recebimento,
					ctr_codigo_conta,
					ctr_codigo_c_custo,
					ctr_codigo_banco,
					ctr_data_emissao,
					ctr_data_vencimento,
					ctr_valor_parcela,
					ctr_valor_juros,
					ctr_descricao_juros,
					ctr_valor_desconto,
					ctr_descricao_desconto,
					ctr_valor_acrescimo,
					ctr_descricao_acrescimo,
					ctr_situacao,
					ctr_aceite,
					ctr_usuario_aceite,
					ctr_data_aceite,
					ctr_numero_cheque,
					ctr_cobranca_processada,
					ctr_nosso_numero,
					ctr_nome_remessa,
					ctr_carteira,
					ctr_variacao,
					ctr_observacao,
					ctr_usuario_juros,
					ctr_data_juros,
					ctr_aceite_juros,
					ctr_alterado_em,
					ctr_alterado_por,
					ctr_incluido_em,
					ctr_incluido_por,
					ctr_lixeira_em,
					ctr_lixeira_por,
					ctr_lixeira

				) VALUES (
					'$numero_doc',
					'$numero_parcela',
					'$qtd_parcela',
					'$tipo_documento',
					null,
					null,
					'$codigo_cli_for',
					'$nome_cliente',
					'$local',
					'$forma_pgto_mensal',
					'$conta_pgto_mensal',
					'$conta_mensal',
					'$codigo_c_custo',
					null,
					'$data_emissao',
					'$data_vencimento',
					'$valor_mensal',
					null,
					null,
					null,
					null,
					null,
					null,
					'',
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					'$observacao',
					null,
					null,
					null,
					null,
					null,
					'$data_sistema',
					'$nomeusuario',
					null,
					null,
					0
				    )";

			$resultado = mysqli_query($conector, $sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado) {
				header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao incluir a parcela ' . $numero_parcela . ' - ' . $erro_mysql));
				exit;
			}
		}
	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Conta Incluida com sucesso.'));
	mysqli_close($conector);
	exit;
} else {
	$sql = "UPDATE contas_receber SET
				ctr_codigo_cliente_fornecedor='$codigo_cli_for',
				ctr_nome_cliente='$nome_cliente',
				ctr_codigo_fazenda='$local',
				ctr_codigo_forma_recebimento='$codigo_forma_rec',
				ctr_codigo_conta_recebimento='$codigo_conta_rec',
				ctr_codigo_conta='$codigo_conta',
				ctr_codigo_c_custo='$codigo_c_custo',
				ctr_data_emissao='$data_emissao',
				ctr_data_vencimento='$data_vencimento',
				ctr_valor_parcela='$vlr_parcela',
				ctr_valor_juros='$vlr_juros',
				ctr_descricao_juros='$desc_juros',
				ctr_valor_desconto='$vlr_desconto',
				ctr_descricao_desconto='$desc_desconto',
				ctr_valor_acrescimo='$vlr_acrescimo',
				ctr_descricao_acrescimo='$desc_acrescimo',
				ctr_numero_cheque='$numero_cheque',
				ctr_observacao='$observacao',
				ctr_alterado_em='$data_sistema',
				ctr_alterado_por='$nomeusuario'
	    WHERE ctr_id ='$id_ctr'";

	$resultado = mysqli_query($conector, $sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao alterar o registro ' . $erro_mysql));
		mysqli_close($conector);
		exit;
	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Conta alterada com sucesso.'));
	exit;
}
