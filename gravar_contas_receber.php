<?php
// Captura todo output: warnings/notices do PHP ficam no buffer e são descartados
// antes do JSON, garantindo resposta limpa para o AJAX.
ob_start(function($buffer) {
	$pos = strpos($buffer, '{');
	return $pos !== false ? substr($buffer, $pos) : $buffer;
});

function sonumero($str)
{
	return preg_replace("/[^0-9]/", "", $str);
}

/**
 * Salva rateio da conta (rateio_json) na tbl_ctr_rateio.
 * $ctr_id = ID do registro principal em contas_receber (1ª parcela ou único)
 */
function salvar_rateio_ctr($ctr_id, $conector, $nomeusuario, $data_sistema) {
	$json = isset($_POST['rateio_json']) ? trim($_POST['rateio_json']) : '';
	if (empty($json) || $json === 'null' || $json === '[]') return;

	$locais = json_decode($json, true);
	if (!is_array($locais) || count($locais) === 0) return;

	$usuario_esc = mysqli_real_escape_string($conector, $nomeusuario);

	foreach ($locais as $loc) {
		$rc_cod_local  = (int)($loc['id'] ?? 0);
		$rc_nom_local  = mysqli_real_escape_string($conector, $loc['nome'] ?? '');
		$rc_perc_local = (float)($loc['perc'] ?? 0);
		$rc_val_local  = (float)($loc['valor'] ?? 0);

		$ccs = $loc['ccs'] ?? [];
		if (count($ccs) === 0) {
			$sql = "INSERT INTO tbl_ctr_rateio
			            (rc_ctr_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
			             rc_incluido_em, rc_incluido_por)
			        VALUES
			            ('$ctr_id','$rc_cod_local','$rc_nom_local','$rc_perc_local','$rc_val_local',
			             '$data_sistema','$usuario_esc')";
			mysqli_query($conector, $sql);
			continue;
		}

		foreach ($ccs as $cc) {
			$rc_cod_cc  = mysqli_real_escape_string($conector, $cc['id'] ?? '');
			$rc_nom_cc  = mysqli_real_escape_string($conector, $cc['nome'] ?? '');
			$rc_perc_cc = (float)($cc['perc'] ?? 0);
			$rc_val_cc  = (float)($cc['valor'] ?? 0);

			$contas = $cc['contas'] ?? [];
			if (count($contas) === 0) {
				$sql = "INSERT INTO tbl_ctr_rateio
				            (rc_ctr_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
				             rc_codigo_cc, rc_nome_cc, rc_perc_cc, rc_valor_cc,
				             rc_incluido_em, rc_incluido_por)
				        VALUES
				            ('$ctr_id','$rc_cod_local','$rc_nom_local','$rc_perc_local','$rc_val_local',
				             '$rc_cod_cc','$rc_nom_cc','$rc_perc_cc','$rc_val_cc',
				             '$data_sistema','$usuario_esc')";
				mysqli_query($conector, $sql);
				continue;
			}

			foreach ($contas as $ct) {
				$rc_cod_conta  = mysqli_real_escape_string($conector, $ct['id'] ?? '');
				$rc_nom_conta  = mysqli_real_escape_string($conector, $ct['nome'] ?? '');
				$rc_perc_conta = (float)($ct['perc'] ?? 0);
				$rc_val_conta  = (float)($ct['valor'] ?? 0);

				$sql = "INSERT INTO tbl_ctr_rateio
				            (rc_ctr_id, rc_codigo_local, rc_nome_local, rc_perc_local, rc_valor_local,
				             rc_codigo_cc, rc_nome_cc, rc_perc_cc, rc_valor_cc,
				             rc_codigo_conta, rc_nome_conta, rc_perc_conta, rc_valor_conta,
				             rc_incluido_em, rc_incluido_por)
				        VALUES
				            ('$ctr_id','$rc_cod_local','$rc_nom_local','$rc_perc_local','$rc_val_local',
				             '$rc_cod_cc','$rc_nom_cc','$rc_perc_cc','$rc_val_cc',
				             '$rc_cod_conta','$rc_nom_conta','$rc_perc_conta','$rc_val_conta',
				             '$data_sistema','$usuario_esc')";
				mysqli_query($conector, $sql);
			}
		}
	}
}

/**
 * Processa arquivos e links de anexo, gravando em tbl_ctr_anexos.
 * Links usam anexo_arquivo = URL e anexo_tamanho = 0.
 * Retorna array com erros (vazio = sucesso).
 */
function salvar_anexos_ctr($ctr_id, $conector, $nomeusuario, $data_sistema) {
	$erros       = [];
	$usuario_esc = mysqli_real_escape_string($conector, $nomeusuario);

	if (!empty($_FILES['anexo']['name'][0])) {
		$pasta = __DIR__ . '/uploads/ctr/';
		if (!is_dir($pasta)) { mkdir($pasta, 0755, true); }

		$total = count($_FILES['anexo']['name']);
		for ($i = 0; $i < $total; $i++) {
			if ($_FILES['anexo']['error'][$i] !== UPLOAD_ERR_OK) continue;
			if (empty($_FILES['anexo']['name'][$i])) continue;

			$nome_original = basename($_FILES['anexo']['name'][$i]);
			$ext           = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
			$nome_arquivo  = uniqid('ctr_', true) . '.' . $ext;
			$destino       = $pasta . $nome_arquivo;
			$tamanho       = $_FILES['anexo']['size'][$i];

			if (!move_uploaded_file($_FILES['anexo']['tmp_name'][$i], $destino)) {
				$erros[] = 'Erro ao mover arquivo: ' . $nome_original;
				continue;
			}

			$nome_esc = mysqli_real_escape_string($conector, $nome_original);
			$arq_esc  = mysqli_real_escape_string($conector, $nome_arquivo);

			$sql = "INSERT INTO tbl_ctr_anexos
			            (anexo_ctr_id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por)
			        VALUES
			            ('$ctr_id', '$nome_esc', '$arq_esc', '$tamanho', '$data_sistema', '$usuario_esc')";
			if (!mysqli_query($conector, $sql)) {
				$erros[] = 'Erro BD anexo: ' . mysqli_error($conector);
			}
		}
	}

	$links_url  = isset($_POST['anexo_link_url'])  ? $_POST['anexo_link_url']  : [];
	$links_desc = isset($_POST['anexo_link_desc']) ? $_POST['anexo_link_desc'] : [];
	foreach ($links_url as $i => $url) {
		$url = trim($url);
		if (empty($url)) continue;
		$desc = trim($links_desc[$i] ?? '');
		if (empty($desc)) $desc = $url;

		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			$erros[] = 'URL inválida: ' . htmlspecialchars($url);
			continue;
		}

		$url_esc  = mysqli_real_escape_string($conector, $url);
		$desc_esc = mysqli_real_escape_string($conector, $desc);

		$sql = "INSERT INTO tbl_ctr_anexos
		            (anexo_ctr_id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por)
		        VALUES
		            ('$ctr_id', '$desc_esc', '$url_esc', 0, '$data_sistema', '$usuario_esc')";
		if (!mysqli_query($conector, $sql)) {
			$erros[] = 'Erro BD link: ' . mysqli_error($conector);
		}
	}

	return $erros;
}

$tipo_operacao = $_POST['tipo_operacao'];
$id_ctr = $_POST['id_ctr'];
if ($tipo_operacao == 1) {
	// Tela nova (Incluir): Descrição + Observações separadas viram um único
	// campo ctr_observacao (a tabela contas_receber não tem coluna própria
	// para "descrição da conta" como a tbl_ctp de Contas a Pagar tem).
	$descricao_compra  = trim($_POST['descricao_compra'] ?? '');
	$observacoes_extra = trim($_POST['observacoes'] ?? '');
	$observacao = $descricao_compra . ($observacoes_extra !== '' ? '  |  Obs: ' . $observacoes_extra : '');
} else {
	$observacao = $_POST['observacao'];
}
$codigo_cli_for = $_POST['codigo_cli_for'];
$nome_cli = $_POST['nome_cli'];
$numero_doc = $_POST['number_doc'];
$tipo_documento = $_POST['tipo_doc'];
$data_emissao = $_POST['data_emissao'];
$codigo_c_custo = $_POST['codigo_cc'];

if ($tipo_operacao==1) {
	// codigo_local vem como array (name="codigo_local[]"); sem rateio é
	// sempre um único valor selecionado.
	$local_raw = isset($_POST['codigo_local']) ? $_POST['codigo_local'] : '';
	$local = is_array($local_raw) ? (string)($local_raw[0] ?? '') : $local_raw;
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
	// Extração e validação específicas da tela nova (parcelamento, rateio,
	// forma/banco de pagamento, anexos) ficam junto da gravação, mais abaixo,
	// depois da conexão com o banco.
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

	// ---- Função auxiliar: insere um registro (parcela) em contas_receber ----
	$insere_parcela_ctr = function(
		$numero_doc, $codigo_cli_for, $nome_cliente, $numero_parcela, $tipo_doc,
		$qtd_total_parcelas, $data_emissao, $data_vencimento, $vlr_parcela,
		$codigo_local, $codigo_ccusto, $codigo_conta, $codigo_conta_rec, $codigo_forma_rec,
		$observacao, $nomeusuario, $data_sistema, $conector
	) {
		$sql_local  = ($codigo_local  === null || $codigo_local  === '') ? 'NULL' : "'$codigo_local'";
		$sql_ccusto = ($codigo_ccusto === null || $codigo_ccusto === '') ? 'NULL' : "'$codigo_ccusto'";
		$sql_conta  = ($codigo_conta  === null || $codigo_conta  === '') ? 'NULL' : "'$codigo_conta'";

		$sql = "INSERT INTO contas_receber (
				ctr_numero_doc, ctr_parcela, ctr_qtd_parcelas, ctr_tipo, ctr_ano_base, ctr_semestre,
				ctr_codigo_cliente_fornecedor, ctr_nome_cliente, ctr_codigo_fazenda,
				ctr_codigo_forma_recebimento, ctr_codigo_conta_recebimento, ctr_codigo_conta,
				ctr_codigo_c_custo, ctr_codigo_banco,
				ctr_data_emissao, ctr_data_vencimento, ctr_valor_parcela,
				ctr_valor_juros, ctr_descricao_juros, ctr_valor_desconto, ctr_descricao_desconto,
				ctr_valor_acrescimo, ctr_descricao_acrescimo,
				ctr_situacao, ctr_aceite, ctr_usuario_aceite, ctr_data_aceite, ctr_numero_cheque,
				ctr_cobranca_processada, ctr_nosso_numero, ctr_nome_remessa, ctr_carteira, ctr_variacao,
				ctr_observacao, ctr_usuario_juros, ctr_data_juros, ctr_aceite_juros,
				ctr_alterado_em, ctr_alterado_por, ctr_incluido_em, ctr_incluido_por,
				ctr_lixeira_em, ctr_lixeira_por, ctr_lixeira
			) VALUES (
				'$numero_doc', '$numero_parcela', '$qtd_total_parcelas', '$tipo_doc', null, null,
				'$codigo_cli_for', '$nome_cliente', $sql_local,
				'$codigo_forma_rec', '$codigo_conta_rec', $sql_conta,
				$sql_ccusto, null,
				'$data_emissao', '$data_vencimento', '$vlr_parcela',
				null, null, null, null,
				null, null,
				'', null, null, null, null,
				null, null, null, null, null,
				'$observacao', null, null, null,
				'$data_sistema', '$nomeusuario', null, null,
				null, null, 0
			    )";
		return mysqli_query($conector, $sql);
	};

	// ---- Função auxiliar: grava a baixa (Pago) de uma parcela recém-incluída ----
	$baixa_parcela_ctr = function(
		$novo_id, $numero_doc, $numero_parcela, $tipo_doc, $codigo_cli_for, $nome_cliente,
		$data_pagamento, $vlr_pago, $vlr_juros, $vlr_desconto, $historico,
		$nomeusuario, $data_sistema, $conector
	) {
		$novo_id_fmt = str_pad($novo_id, 10, '0', STR_PAD_LEFT);
		$historico_esc = mysqli_real_escape_string($conector, $historico);
		mysqli_query($conector, "INSERT INTO baixa_contas_receber (
				bcr_id, bcr_numero_doc, bcr_parcela, bcr_sequencia, bcr_tipo,
				bcr_codigo_cliente_fornecedor, bcr_nome_cliente,
				bcr_data_pagamento, bcr_valor_pagamento, bcr_valor_juros, bcr_valor_desconto,
				bcr_valor_acrescimo, bcr_descricao_acrescimo,
				bcr_usuario_aceite, bcr_data_aceite, bcr_historico, bcr_situacao,
				bcr_usuario_aceite_pagamento, bcr_data_aceite_pagamento, bcr_comissao_paga
			) VALUES (
				'$novo_id_fmt', '$numero_doc', '$numero_parcela', 1, '$tipo_doc',
				'$codigo_cli_for', '$nome_cliente',
				'$data_pagamento', '$vlr_pago', '$vlr_juros', '$vlr_desconto',
				null, null,
				'$nomeusuario', '$data_sistema', '$historico_esc', 'P',
				null, null, null
			)");
		mysqli_query($conector, "UPDATE contas_receber SET
				ctr_situacao='P', ctr_valor_juros='$vlr_juros', ctr_valor_desconto='$vlr_desconto'
			WHERE ctr_id='$novo_id'");
	};

	// ---- Local, Centro de Custos, Conta Contábil e Rateio ----
	$parcelamento     = isset($_POST['parcelamento']) ? intval($_POST['parcelamento']) : 0;
	$habilitar_rateio = isset($_POST['habilitar_rateio']);
	$rateio_json      = isset($_POST['rateio_json']) ? trim($_POST['rateio_json']) : '';
	$tem_rateio       = $habilitar_rateio && !empty($rateio_json) && $rateio_json !== '[]' && $rateio_json !== 'null';

	$codigo_conta_n = isset($_POST['codigo_conta']) ? mysqli_real_escape_string($conector, $_POST['codigo_conta']) : '';
	$local_esc      = mysqli_real_escape_string($conector, $local);
	$codigo_cc_esc  = mysqli_real_escape_string($conector, $codigo_c_custo);

	if ($tem_rateio) {
		// Com rateio: Local/CC/Conta do topo são ignorados — vêm do rateio_json
		$local_esc     = null;
		$codigo_cc_esc = null;
		$codigo_conta_n = null;
	}

	$codigo_cli_for_esc = mysqli_real_escape_string($conector, $codigo_cli_for);
	$nome_cliente_esc   = mysqli_real_escape_string($conector, $nome_cliente);
	$observacao_esc     = mysqli_real_escape_string($conector, $observacao);
	$numero_doc_esc     = mysqli_real_escape_string($conector, $numero_doc);
	$data_emissao_esc   = mysqli_real_escape_string($conector, $data_emissao);

	$vlr_total = isset($_POST['vlr_primeira_parcela'])
		? floatval(str_replace(',', '.', str_replace('.', '', $_POST['vlr_primeira_parcela'])))
		: 0;

	if ($vlr_total <= 0) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe o Valor.'));
		mysqli_close($conector); exit;
	}
	if (!$tem_rateio && empty($local_esc)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe o Local.'));
		mysqli_close($conector); exit;
	}
	if (!$tem_rateio && empty($codigo_cc_esc)) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe o Centro de Custos.'));
		mysqli_close($conector); exit;
	}
	if (!$tem_rateio && (empty($codigo_conta_n) || $codigo_conta_n == '0000000')) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Informe a Conta Contábil.'));
		mysqli_close($conector); exit;
	}

	// =====================================================
	// CASO A: À Vista / 1 Parcela (parcelamento == 0)
	// =====================================================
	if ($parcelamento == 0) {
		$data_vencimento_n = isset($_POST['data_vencimento']) ? mysqli_real_escape_string($conector, $_POST['data_vencimento']) : '';
		$codigo_conta_rec_n = isset($_POST['codigo_conta_rec']) ? intval($_POST['codigo_conta_rec']) : 0;
		$codigo_forma_rec_n = isset($_POST['codigo_forma_rec']) ? mysqli_real_escape_string($conector, $_POST['codigo_forma_rec']) : '00';
		$tipo_doc_n         = isset($_POST['tipo_doc']) ? mysqli_real_escape_string($conector, $_POST['tipo_doc']) : '00';
		$pago_n             = isset($_POST['pago']) ? 'S' : 'N';

		$pago_dt_pag_n   = (!empty($_POST['pago_data_pagamento'])) ? mysqli_real_escape_string($conector, $_POST['pago_data_pagamento']) : $data_vencimento_n;
		$pago_desconto_n = (!empty($_POST['pago_desconto'])) ? floatval(str_replace(',', '.', str_replace('.', '', $_POST['pago_desconto']))) : 0;
		$pago_juros_n    = (!empty($_POST['pago_juros']))    ? floatval(str_replace(',', '.', str_replace('.', '', $_POST['pago_juros'])))    : 0;
		$pago_vlr_pago_n = (!empty($_POST['pago_valor_pago'])) ? floatval(str_replace(',', '.', str_replace('.', '', $_POST['pago_valor_pago']))) : $vlr_total;

		if (empty($data_vencimento_n)) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe o Vencimento.'));
			mysqli_close($conector); exit;
		}
		if ($codigo_conta_rec_n == 0) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe o Banco/Conta Pagamento.'));
			mysqli_close($conector); exit;
		}
		if ($codigo_forma_rec_n == '00') {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Forma Pagamento.'));
			mysqli_close($conector); exit;
		}

		$ok = $insere_parcela_ctr(
			$numero_doc_esc, $codigo_cli_for_esc, $nome_cliente_esc, 1, $tipo_doc_n,
			1, $data_emissao_esc, $data_vencimento_n, $vlr_total,
			$local_esc, $codigo_cc_esc, $codigo_conta_n, $codigo_conta_rec_n, $codigo_forma_rec_n,
			$observacao_esc, $nomeusuario, $data_sistema, $conector
		);
		if (!$ok) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Erro ao gravar: ' . mysqli_error($conector)));
			mysqli_close($conector); exit;
		}
		$novo_id = mysqli_insert_id($conector);
		salvar_anexos_ctr($novo_id, $conector, $nomeusuario, $data_sistema);
		if ($tem_rateio) salvar_rateio_ctr($novo_id, $conector, $nomeusuario, $data_sistema);

		if ($pago_n == 'S') {
			$baixa_parcela_ctr(
				$novo_id, $numero_doc_esc, 1, $tipo_doc_n, $codigo_cli_for_esc, $nome_cliente_esc,
				$pago_dt_pag_n, $pago_vlr_pago_n, $pago_juros_n, $pago_desconto_n,
				'Recebimento total de: ' . $nome_cliente, $nomeusuario, $data_sistema, $conector
			);
		}

		header('Content-type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
		mysqli_close($conector);
		exit;
	}

	// =====================================================
	// CASO B: Parcelado em 2x ou mais (parcelamento >= 1)
	// =====================================================
	$parcelas_post = isset($_POST['parcela']) ? $_POST['parcela'] : [];
	if (count($parcelas_post) == 0) {
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Nenhuma parcela encontrada no envio.'));
		mysqli_close($conector); exit;
	}

	$qtd_total_n   = count($parcelas_post);
	$primeiro_id_n = null;

	foreach ($parcelas_post as $idx => $parc) {
		$p_data   = mysqli_real_escape_string($conector, $parc['data_vencimento']);
		$p_vlr    = floatval(str_replace(',', '.', str_replace('.', '', $parc['valor'])));
		$p_banco  = intval($parc['banco_conta']);
		$p_forma  = mysqli_real_escape_string($conector, $parc['forma_pagamento']);
		$p_tdoc   = mysqli_real_escape_string($conector, $parc['tipo_doc']);
		$p_pago   = isset($parc['pago']) ? 'S' : 'N';
		$p_num    = $idx + 1;
		$p_dt_pag = (!empty($parc['data_pagamento'])) ? mysqli_real_escape_string($conector, $parc['data_pagamento']) : $p_data;
		$p_desconto = (!empty($parc['desconto'])) ? floatval(str_replace(',', '.', str_replace('.', '', $parc['desconto']))) : 0;
		$p_juros    = (!empty($parc['juros']))    ? floatval(str_replace(',', '.', str_replace('.', '', $parc['juros'])))    : 0;
		$p_vlr_pago = (!empty($parc['valor_pago'])) ? floatval(str_replace(',', '.', str_replace('.', '', $parc['valor_pago']))) : $p_vlr;

		if ($p_banco == 0) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe o Banco/Conta Pagamento da parcela ' . $p_num . '.'));
			mysqli_close($conector); exit;
		}
		if ($p_forma == '00' || $p_forma === '') {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Informe a Forma Pagamento da parcela ' . $p_num . '.'));
			mysqli_close($conector); exit;
		}

		$ok = $insere_parcela_ctr(
			$numero_doc_esc, $codigo_cli_for_esc, $nome_cliente_esc, $p_num, $p_tdoc,
			$qtd_total_n, $data_emissao_esc, $p_data, $p_vlr,
			$local_esc, $codigo_cc_esc, $codigo_conta_n, $p_banco, $p_forma,
			$observacao_esc, $nomeusuario, $data_sistema, $conector
		);
		if (!$ok) {
			header('Content-type: application/json');
			echo json_encode(array('error' => true, 'message' => 'Erro ao gravar parcela ' . $p_num . ': ' . mysqli_error($conector)));
			mysqli_close($conector); exit;
		}
		$novo_id = mysqli_insert_id($conector);
		if ($primeiro_id_n === null) {
			$primeiro_id_n = $novo_id;
			salvar_anexos_ctr($primeiro_id_n, $conector, $nomeusuario, $data_sistema);
			if ($tem_rateio) salvar_rateio_ctr($primeiro_id_n, $conector, $nomeusuario, $data_sistema);
		}
		if ($p_pago == 'S') {
			$baixa_parcela_ctr(
				$novo_id, $numero_doc_esc, $p_num, $p_tdoc, $codigo_cli_for_esc, $nome_cliente_esc,
				$p_dt_pag, $p_vlr_pago, $p_juros, $p_desconto,
				'Recebimento parcela ' . $p_num . ' de: ' . $nome_cliente, $nomeusuario, $data_sistema, $conector
			);
		}
	}

	header('Content-type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
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
