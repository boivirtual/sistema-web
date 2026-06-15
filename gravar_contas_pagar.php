<?php
	 function sonumero($str) {
		return preg_replace("/[^0-9]/", "", $str);
	}

    /**
     * Processa os arquivos do input name="anexo[]" e grava na tbl_ctp_anexos.
     * Retorna array com erros (vazio = sucesso).
     */
    function salvar_anexos($ctp_id, $conector, $nomeusuario, $data_sistema) {
        $erros = [];
        if (!isset($_FILES['anexo']) || empty($_FILES['anexo']['name'][0])) {
            return $erros; // sem anexos
        }

        $pasta = __DIR__ . '/uploads/ctp/';
        if (!is_dir($pasta)) { mkdir($pasta, 0755, true); }

        $total = count($_FILES['anexo']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['anexo']['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (empty($_FILES['anexo']['name'][$i])) continue;

            $nome_original = basename($_FILES['anexo']['name'][$i]);
            $ext           = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
            $nome_arquivo  = uniqid('ctp_', true) . '.' . $ext;
            $destino       = $pasta . $nome_arquivo;
            $tamanho       = $_FILES['anexo']['size'][$i];

            if (!move_uploaded_file($_FILES['anexo']['tmp_name'][$i], $destino)) {
                $erros[] = 'Erro ao mover arquivo: ' . $nome_original;
                continue;
            }

            $nome_esc    = mysqli_real_escape_string($conector, $nome_original);
            $arq_esc     = mysqli_real_escape_string($conector, $nome_arquivo);
            $usuario_esc = mysqli_real_escape_string($conector, $nomeusuario);

            $sql = "INSERT INTO tbl_ctp_anexos
                        (anexo_ctp_id, anexo_nome, anexo_arquivo, anexo_tamanho, anexo_incluido_em, anexo_incluido_por)
                    VALUES
                        ('$ctp_id', '$nome_esc', '$arq_esc', '$tamanho', '$data_sistema', '$usuario_esc')";
            if (!mysqli_query($conector, $sql)) {
                $erros[] = 'Erro BD anexo: ' . mysqli_error($conector);
            }
        }
        return $erros;
    }

	$quantidade_prazos = 0;
	$descricao_compra = $_POST['descricao_compra'];
	$codigo_for= $_POST['codigo_cli_for'];
	$nome_for= $_POST['nome_for'];
	$codigo_conta = $_POST['codigo_conta'];
	$tipo_operacao = $_POST['tipo_operacao'];
	$codigo_c_custo = $_POST['codigo_cc'];

	if (!isset($_POST['codigo_fazenda'])) {
		$codigo_local = '';
	}
	else {
		$codigo_local = $_POST['codigo_fazenda'];
	}

	if ($tipo_operacao==1){
		$numero_doc = $_POST['number_doc'];
		$tipo_documento = $_POST['tipo_doc'];
		$data_emissao = $_POST['data_emissao'];
		$data_vencimento = $_POST['data_vencimento'];
		$vlr_primeira_parcela = $_POST['vlr_primeira_parcela'];
	    if(isset($_POST['pago'])) { $pago = 'S'; } else { $pago = 'N'; }
		$data_pagamento = $_POST['data_pagamento'];
		$vlr_pagamento = $_POST['vlr_pagamento'];
		$vlr_juros = $_POST['vlr_juros'];
		$vlr_desconto = $_POST['vlr_desconto'];
		$codigo_forma_pag = $_POST['codigo_forma_rec'];
		$codigo_forma_pag_parc = $_POST['codigo_forma_parc'];
		$numero_cheque = $_POST['number_cheque'];
		$qtd_parcelas = $_POST['qtd_parcelas'];
		$vlr_parcela_fixa = $_POST['vlr_parcela_fixa'];
		$frequencia = $_POST['frequencia'];
		$data_inicial = $_POST['data_inicial'];

    	$array_valores_fazendas = $_POST['array_fazendas'];

    	/*if ($array_valores_fazendas!='') {
			$codigo_c_custo = implode(', ', $codigo_c_custo);
		    $array_ccusto = explode(",", $codigo_c_custo);
		    $quantidade_centro_custos = count($array_ccusto);
    	}
    	else {
			$codigo_c_custo = implode(', ', $codigo_c_custo);
		    $array_ccusto = explode(",", $codigo_c_custo);
    		$quantidade_centro_custos = 1;
    		$codigo_c_custo = $array_ccusto[0];
		}*/

    	if ($array_valores_fazendas!='') {
			$codigo_local = implode(', ', $codigo_local);
		    $array_fazenda = explode(",", $codigo_local);
		    $quantidade_fazendas = count($array_fazenda);
    	}
    	else {
			$codigo_local = implode(', ', $codigo_local);
		    $array_fazenda = explode(",", $codigo_local);
    		$quantidade_fazendas = 1;
    		$codigo_local = $array_fazenda[0];
		}
	}
	else {
		$ctp_id = $_POST['ctp_id'];
		$numero_doc = $_POST['doc_editar'];
		$tipo_documento = $_POST['tipo_doc'];
		$codigo_forma_pag = $_POST['codigo_forma_rec'];
		$numero_cheque = $_POST['cheque_editar'];
		$data_emissao = $_POST['data_emissao'];
		$data_vencimento = $_POST['data_vencimento'];
		$vlr_parcela = $_POST['vlr_parcela'];
		$vlr_juros = $_POST['vlr_juros'];
		$desc_juros = $_POST['desc_juros'];
		$vlr_desconto = $_POST['vlr_desconto'];
		$desc_desconto = $_POST['desc_desconto'];
		$vlr_acrescimo = $_POST['vlr_acrescimo'];
		$desc_acrescimo = $_POST['desc_acrescimo'];
	}

	$data_sistema = date("Y-m-d H:i:s");

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];

	include "conecta_mysql.inc";

	// Número do documento: usa o informado pelo usuário ou deixa em branco

    // =========================================================
    // NOVO SISTEMA — parcelamento dinâmico (parcelamento >= 0)
    // =========================================================
    $parcelamento = isset($_POST['parcelamento']) ? intval($_POST['parcelamento']) : -1;

    if ($tipo_operacao == 1 && $parcelamento >= 0) {

        // --- Leitura dos campos do novo form ---
        $data_emissao_n   = isset($_POST['data_emissao'])   ? mysqli_real_escape_string($conector, $_POST['data_emissao'])   : '';
        $numero_doc_n     = isset($_POST['number_doc'])      ? mysqli_real_escape_string($conector, $_POST['number_doc'])     : '';
        $descricao_n      = mysqli_real_escape_string($conector, $descricao_compra);
        $codigo_for_n     = mysqli_real_escape_string($conector, $codigo_for);
        $codigo_conta_n   = mysqli_real_escape_string($conector, $codigo_conta);
        $codigo_ccusto_n  = isset($_POST['codigo_cc']) ? mysqli_real_escape_string($conector, $_POST['codigo_cc']) : '';
        $observacoes_n    = isset($_POST['observacoes']) ? mysqli_real_escape_string($conector, $_POST['observacoes']) : '';

        // Resolve local (fazenda) — pode ser array
        $cod_local_raw = isset($_POST['codigo_fazenda']) ? $_POST['codigo_fazenda'] : [];
        if (!is_array($cod_local_raw)) $cod_local_raw = [$cod_local_raw];

        // Resolve array_fazendas (rateio)
        $array_valores_fazendas_n = isset($_POST['array_fazendas']) ? $_POST['array_fazendas'] : '';

        if ($array_valores_fazendas_n != '') {
            $codigo_local_str = implode(', ', $cod_local_raw);
            $array_fazenda_n  = explode(',', $codigo_local_str);
            $qtd_fazendas_n   = count($array_fazenda_n);
        } else {
            $codigo_local_str = implode(', ', $cod_local_raw);
            $array_fazenda_n  = explode(',', $codigo_local_str);
            $qtd_fazendas_n   = 1;
            $codigo_local_str = trim($array_fazenda_n[0]);
        }

        // Resolve nome do fornecedor
        if ($codigo_for_n != '999999999') {
            $rs_for = mysqli_query($conector, "SELECT tbl_pessoa_nome FROM tbl_pessoa WHERE tbl_pessoa_id='$codigo_for_n'");
            $row_for = mysqli_fetch_object($rs_for);
            $razao_n = $row_for ? mysqli_real_escape_string($conector, $row_for->tbl_pessoa_nome) : '';
        } else {
            $razao_n = isset($_POST['nome_for']) ? mysqli_real_escape_string($conector, $_POST['nome_for']) : '';
        }

        // Valor total
        $vlr_total_n = isset($_POST['vlr_primeira_parcela']) ? str_replace(',', '.', str_replace('.', '', $_POST['vlr_primeira_parcela'])) : 0;
        $vlr_total_n = floatval($vlr_total_n);

        // --- Validações comuns ---
        if (empty($descricao_n)) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Informe a Descrição da Compra.'));
            mysqli_close($conector); exit;
        }
        if (empty($codigo_local_str) || $codigo_local_str == '000000000') {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Informe a Fazenda/Local.'));
            mysqli_close($conector); exit;
        }
        if (empty($codigo_for_n)) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Informe o Fornecedor.'));
            mysqli_close($conector); exit;
        }
        if ($codigo_conta_n == '0000000') {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Informe a Conta Contábil.'));
            mysqli_close($conector); exit;
        }
        if (empty($data_emissao_n)) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Informe a Data de Emissão.'));
            mysqli_close($conector); exit;
        }
        if ($vlr_total_n <= 0) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Informe o Valor da compra.'));
            mysqli_close($conector); exit;
        }

        // ---- FUNÇÃO AUXILIAR: insere um registro na tabela contas_pagar ----
        $insere_parcela = function(
            $numero_doc, $codigo_for, $numero_parcela, $tipo_doc, $razao,
            $qtd_total_parcelas, $data_emissao, $data_vencimento, $vlr_parcela,
            $codigo_local, $codigo_ccusto, $codigo_conta, $conta_pagamento,
            $descricao, $observacoes, $nomeusuario, $data_sistema, $conector
        ) {
            $sql = "INSERT INTO contas_pagar (
                ctp_numero_doc, ctp_codigo_fornecedor, ctp_parcela,
                ctp_tipo_documento, ctp_nome_fornecedor, ctp_numero_documento,
                ctp_qtd_parcelas, ctp_data_emissao, ctp_data_vencimento,
                ctp_valor_parcela, ctp_valor_desconto, ctp_descricao_valor_desconto,
                ctp_valor_juros, ctp_descricao_valor_juros,
                ctp_outro_valor, ctp_descricao_outro_valor,
                ctp_situacao, ctp_previsao_despesas, ctp_agendamento,
                ctp_data_agendamento, ctp_valor_total_agendamento, ctp_numero_agendamento,
                ctp_codigo_fazenda, ctp_codigo_centro_custos, ctp_codigo_conta,
                ctp_codigo_banco, ctp_numero_cheque, ctp_conta_pagamento,
                ctp_aceite, ctp_data_aceite, ctp_usuario_aceite,
                ctp_incluido_em, ctp_incluido_por,
                ctp_alterado_em, ctp_alterado_por,
                ctp_descricao_compra, ctp_observacoes
            ) VALUES (
                '$numero_doc', '$codigo_for', '$numero_parcela',
                '$tipo_doc', '$razao', '$numero_doc',
                '$qtd_total_parcelas', '$data_emissao', '$data_vencimento',
                '$vlr_parcela', 0.00, null,
                0.00, null,
                null, null,
                '', null, null,
                null, null, null,
                '$codigo_local', '$codigo_ccusto', '$codigo_conta',
                null, null, '$conta_pagamento',
                '', null, null,
                '$data_sistema', '$nomeusuario',
                null, null,
                '$descricao', '$observacoes'
            )";
            return mysqli_query($conector, $sql);
        };

        // =====================================================
        // CASO A: À Vista (parcelamento == 0)
        // =====================================================
        if ($parcelamento == 0) {
            $data_vencimento_n = isset($_POST['data_vencimento']) ? mysqli_real_escape_string($conector, $_POST['data_vencimento']) : '';
            $banco_n           = isset($_POST['codigo_forma_rec']) ? intval($_POST['codigo_forma_rec']) : 0;
            $tipo_doc_n        = isset($_POST['tipo_doc']) ? mysqli_real_escape_string($conector, $_POST['tipo_doc']) : '00';
            $pago_n            = isset($_POST['pago']) ? 'S' : 'N';

            if (empty($data_vencimento_n)) {
                header('Content-type: application/json');
                echo json_encode(array('error' => true, 'message' => 'Informe a Data de Vencimento.'));
                mysqli_close($conector); exit;
            }
            if ($banco_n == 0) {
                header('Content-type: application/json');
                echo json_encode(array('error' => true, 'message' => 'Informe o Banco/Conta Pagamento.'));
                mysqli_close($conector); exit;
            }

            if ($qtd_fazendas_n == 1) {
                // Única fazenda
                $cod_loc_esc = mysqli_real_escape_string($conector, trim($codigo_local_str));
                $ok = $insere_parcela(
                    $numero_doc_n, $codigo_for_n, 1, $tipo_doc_n, $razao_n,
                    1, $data_emissao_n, $data_vencimento_n, $vlr_total_n,
                    $cod_loc_esc, $codigo_ccusto_n, $codigo_conta_n, $banco_n,
                    $descricao_n, $nomeusuario, $data_sistema, $conector
                );
                if (!$ok) {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Erro ao gravar: ' . mysqli_error($conector)));
                    mysqli_close($conector); exit;
                }
                $novo_id = mysqli_insert_id($conector);
                if ($pago_n == 'S') {
                    $novo_id_fmt = str_pad($novo_id, 9, '0', STR_PAD_LEFT);
                    $hist = mysqli_real_escape_string($conector, 'Pag total do doc para: ' . $razao_n);
                    mysqli_query($conector, "INSERT INTO baixa_contas_pagar (bcp_id, bcp_numero_id, bcp_codigo_fornecedor, bcp_parcela, bcp_sequencia_pagamento, bcp_nome_fornecedor, bcp_numero_documento, bcp_data_pagamento, bcp_valor_pagamento, bcp_situacao, bcp_data_aceite, bcp_usuario_aceite, bcp_numero_agendamento, bcp_historico_pagamento) VALUES ('$novo_id_fmt','$numero_doc_n','$codigo_for_n',1,1,'$razao_n','$numero_doc_n','$data_emissao_n','$vlr_total_n','P','$data_sistema','$nomeusuario',null,'$hist')");
                    mysqli_query($conector, "UPDATE contas_pagar SET ctp_situacao='P' WHERE ctp_id='$novo_id'");
                }
            } else {
                // Múltiplas fazendas (rateio)
                $matriz_n = explode('<|>', $array_valores_fazendas_n);
                foreach ($matriz_n as $item) {
                    $partes = explode('|', $item);
                    $loc_i  = mysqli_real_escape_string($conector, trim($partes[0]));
                    $vlr_i  = floatval(str_replace(',', '.', str_replace('.', '', $partes[2])));
                    $ok = $insere_parcela(
                        $numero_doc_n, $codigo_for_n, 1, $tipo_doc_n, $razao_n,
                        1, $data_emissao_n, $data_vencimento_n, $vlr_i,
                        $loc_i, $codigo_ccusto_n, $codigo_conta_n, $banco_n,
                        $descricao_n, $nomeusuario, $data_sistema, $conector
                    );
                    if (!$ok) {
                        header('Content-type: application/json');
                        echo json_encode(array('error' => true, 'message' => 'Erro ao gravar fazenda: ' . mysqli_error($conector)));
                        mysqli_close($conector); exit;
                    }
                }
            }

            header('Content-type: application/json');
            echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
            mysqli_close($conector);
            exit;
        }

        // =====================================================
        // CASO B: Parcelado (parcelamento >= 1)
        // =====================================================
        $parcelas_post = isset($_POST['parcela']) ? $_POST['parcela'] : [];
        if (count($parcelas_post) == 0) {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Nenhuma parcela encontrada no envio.'));
            mysqli_close($conector); exit;
        }

        $qtd_total_n = count($parcelas_post);

        if ($qtd_fazendas_n == 1) {
            $cod_loc_esc = mysqli_real_escape_string($conector, trim($codigo_local_str));
            foreach ($parcelas_post as $idx => $parc) {
                $p_data   = mysqli_real_escape_string($conector, $parc['data_vencimento']);
                $p_vlr    = floatval(str_replace(',', '.', str_replace('.', '', $parc['valor'])));
                $p_banco  = intval($parc['banco_conta']);
                $p_tdoc   = mysqli_real_escape_string($conector, $parc['tipo_doc']);
                $p_pago   = isset($parc['pago']) ? 'S' : 'N';
                $p_num    = $idx + 1;

                $ok = $insere_parcela(
                    $numero_doc_n, $codigo_for_n, $p_num, $p_tdoc, $razao_n,
                    $qtd_total_n, $data_emissao_n, $p_data, $p_vlr,
                    $cod_loc_esc, $codigo_ccusto_n, $codigo_conta_n, $p_banco,
                    $descricao_n, $nomeusuario, $data_sistema, $conector
                );
                if (!$ok) {
                    header('Content-type: application/json');
                    echo json_encode(array('error' => true, 'message' => 'Erro ao gravar parcela ' . $p_num . ': ' . mysqli_error($conector)));
                    mysqli_close($conector); exit;
                }
                $novo_id = mysqli_insert_id($conector);
                if ($p_pago == 'S') {
                    $novo_id_fmt = str_pad($novo_id, 9, '0', STR_PAD_LEFT);
                    $hist = mysqli_real_escape_string($conector, 'Pag parcela ' . $p_num . ' para: ' . $razao_n);
                    mysqli_query($conector, "INSERT INTO baixa_contas_pagar (bcp_id, bcp_numero_id, bcp_codigo_fornecedor, bcp_parcela, bcp_sequencia_pagamento, bcp_nome_fornecedor, bcp_numero_documento, bcp_data_pagamento, bcp_valor_pagamento, bcp_situacao, bcp_data_aceite, bcp_usuario_aceite, bcp_numero_agendamento, bcp_historico_pagamento) VALUES ('$novo_id_fmt','$numero_doc_n','$codigo_for_n','$p_num',1,'$razao_n','$numero_doc_n','$p_data','$p_vlr','P','$data_sistema','$nomeusuario',null,'$hist')");
                    mysqli_query($conector, "UPDATE contas_pagar SET ctp_situacao='P' WHERE ctp_id='$novo_id'");
                }
            }
        } else {
            // Múltiplas fazendas com parcelamento: cada fazenda ganha N parcelas com valor proporcional
            $matriz_n = explode('<|>', $array_valores_fazendas_n);
            foreach ($matriz_n as $item) {
                $partes    = explode('|', $item);
                $loc_i     = mysqli_real_escape_string($conector, trim($partes[0]));
                $perc_i    = floatval($partes[1]) / 100;

                foreach ($parcelas_post as $idx => $parc) {
                    $p_data   = mysqli_real_escape_string($conector, $parc['data_vencimento']);
                    $p_vlr    = round(floatval(str_replace(',', '.', str_replace('.', '', $parc['valor']))) * $perc_i, 2);
                    $p_banco  = intval($parc['banco_conta']);
                    $p_tdoc   = mysqli_real_escape_string($conector, $parc['tipo_doc']);
                    $p_num    = $idx + 1;

                    $ok = $insere_parcela(
                        $numero_doc_n, $codigo_for_n, $p_num, $p_tdoc, $razao_n,
                        $qtd_total_n, $data_emissao_n, $p_data, $p_vlr,
                        $loc_i, $codigo_ccusto_n, $codigo_conta_n, $p_banco,
                        $descricao_n, $nomeusuario, $data_sistema, $conector
                    );
                    if (!$ok) {
                        header('Content-type: application/json');
                        echo json_encode(array('error' => true, 'message' => 'Erro ao gravar parcela ' . $p_num . ' da fazenda: ' . mysqli_error($conector)));
                        mysqli_close($conector); exit;
                    }
                }
            }
        }

        header('Content-type: application/json');
        echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
        mysqli_close($conector);
        exit;

    } // fim novo sistema parcelamento
    // =========================================================
    // FIM NOVO SISTEMA
    // =========================================================

    if (empty($descricao_compra)) {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe a Descrição da Compra.'));
	    mysqli_close($conector);
	    exit;
    }

    if ($codigo_local=='' || $codigo_local=='000000000') {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe a Fazenda.'));
	    mysqli_close($conector);
	    exit;
    }

    if (empty($codigo_for)) {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe o Fornecedor.'));
	    mysqli_close($conector);
	    exit;
    }

    if ($codigo_for==999999999 && empty($nome_for)){
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe a Razão/Nome do Fornecedor.'));
	    mysqli_close($conector);
	    exit;
    }

    /*if ($codigo_c_custo=='') {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe o Centro de Custos.'));
	    mysqli_close($conector);
	    exit;
    }*/

    if ($codigo_conta=='0000000') {
	    header('Content-type: application/json');
	    echo json_encode(array('error' => true, 'message' => 'Informe a Conta.'));
	    mysqli_close($conector);
	    exit;
    }

	if ($tipo_operacao==1){
		
	    if (empty($qtd_parcelas) && empty($data_vencimento)) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe os campos para a Forma de Pagamento'));
		    mysqli_close($conector);
		    exit;
	    }

	    if (empty($data_emissao)) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe a Data de Emissão.'));
		    mysqli_close($conector);
		    exit;
	    }

	    if (empty($data_vencimento)) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe a Data de Vencimento da 1ª Parcela ou Parcela Única.'));
		    mysqli_close($conector);
		    exit;
	    }

	    if ($data_vencimento<$data_emissao) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Data de Vencimento não pode ser < Data de Emissão.'));
		    mysqli_close($conector);
		    exit;
	    }

		if (empty($vlr_primeira_parcela)) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe o Valor da 1ª Parcela ou Parcela Única.'));
		    mysqli_close($conector);
		    exit;
		}

		if ($pago=="S") {
		  	if (empty($data_pagamento)){
			    header('Content-type: application/json');
			    echo json_encode(array('error' => true, 'message' => 'Informe a Data de Pagamento.'));
			    mysqli_close($conector);
			    exit;
		   	}

		    if (empty($vlr_pagamento)) {
			    header('Content-type: application/json');
			    echo json_encode(array('error' => true, 'message' => 'Informe o Valor do Pagamento.'));
			    mysqli_close($conector);
			    exit;
		    }
		}

		if ($codigo_forma_pag==0) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe a Conta Pagamento da 1ª Parcela ou Parcela Única.'));
		    mysqli_close($conector);
		    exit;
		}
		
	    if (!empty($qtd_parcelas)){
		    if (!isset($_POST['tipo_inclusao'])) {
			    header('Content-type: application/json');
			    echo json_encode(array('error' => true, 'message' => 'Selecione Programar repetição do pagamento ou Incluir parcelas por prazo de pagamento.'));
			    mysqli_close($conector);
			    exit;
		    }

            $tipo_inclusao = $_POST['tipo_inclusao'];

	    	if ($tipo_inclusao=='P'){
				$prazos = $_POST['prazo'];
				$vetor_prazos = explode(",", $prazos);
				$quantidade_prazos = count($vetor_prazos);
				$vlr_compra = $_POST['vlr_compra'];

			    if ($quantidade_prazos!=$qtd_parcelas){
				    header('Content-type: application/json');
				    echo json_encode(array('error' => true, 'message' => 'Informe o prazo conforme o Número de Ocorrências das Parcelas Restantes ou o Prazo foi digitado incorretamente.'));
				    mysqli_close($conector);
				    exit;
				}

			    if (empty($vlr_compra)) {
				    header('Content-type: application/json');
				    echo json_encode(array('error' => true, 'message' => 'Informe o Valor total da compra.'));
				    mysqli_close($conector);
				    exit;
			    }
	    	}
	    	else if ($tipo_inclusao=='F') {
			    if (empty($vlr_parcela_fixa)) {
				    header('Content-type: application/json');
				    echo json_encode(array('error' => true, 'message' => 'Informe o Valor das Parcelas.'));
				    mysqli_close($conector);
				    exit;
			    }

			    if (empty($frequencia)) {
				    header('Content-type: application/json');
				    echo json_encode(array('error' => true, 'message' => 'Selecione a Freqûência.'));
				    mysqli_close($conector);
				    exit;
			    }

			    if (empty($data_inicial)) {
				    header('Content-type: application/json');
				    echo json_encode(array('error' => true, 'message' => 'Data Inicial Próximos Pagamentos.'));
				    mysqli_close($conector);
				    exit;
			    }
	    	}
		}
	}
    else {
	    if ($data_vencimento<$data_emissao) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Data de Vencimento não pode ser < Data de Emissão.'));
		    mysqli_close($conector);
		    exit;
	    }

		if (empty($vlr_parcela)) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe o Valor da Parcela.'));
		    mysqli_close($conector);
		    exit;
		}
    }

    if ($codigo_for!=999999999) {
	    $rs = mysqli_query($conector, "SELECT * FROM tbl_pessoa
	                                           WHERE tbl_pessoa_id='$codigo_for'");
	    $fila = mysqli_fetch_object($rs);
	    $razao = $fila->tbl_pessoa_nome;
    }
    else {
    	$razao = $_POST['nome_for'];
    }

	if ($tipo_operacao==2) {
		if (empty($_POST['vlr_parcela'])) {
			$vlr_parcela = 0.00;
		}
		else {
			$vlr_parcela = str_replace(',','.', str_replace('.','', $_POST['vlr_parcela']));
		}

		if ($vlr_parcela==0.00) {
		    header('Content-type: application/json');
		    echo json_encode(array('error' => true, 'message' => 'Informe o Valor da Parcela.'));
		    mysqli_close($conector);
		    exit;
		}

		if (empty($_POST['vlr_juros'])) {
			$vlr_juros = 0.00;
		}
		else {
			$vlr_juros = str_replace(',','.', str_replace('.','', $_POST['vlr_juros']));
		}

		if (empty($_POST['vlr_desconto'])) {
			$vlr_desconto = 0.00;
		}
		else {
			$vlr_desconto = str_replace(',','.', str_replace('.','', $_POST['vlr_desconto']));
		}

		if (empty($_POST['vlr_acrescimo'])) {
			$vlr_acrescimo = 0.00;
		}
		else {
			$vlr_acrescimo = str_replace(',','.', str_replace('.','', $_POST['vlr_acrescimo']));
		}

	    $sql = "UPDATE contas_pagar SET
                ctp_numero_doc='$numero_doc',
                ctp_numero_documento='$numero_doc',
	            ctp_nome_fornecedor='$razao',
	            ctp_codigo_fazenda='$codigo_local',
	            ctp_codigo_conta='$codigo_conta',
	            ctp_codigo_centro_custos='$codigo_c_custo',
	            ctp_tipo_documento='$tipo_documento',
	            ctp_conta_pagamento='$codigo_forma_pag',
	            ctp_numero_cheque='$numero_cheque',
	            ctp_data_emissao='$data_emissao',
	            ctp_data_vencimento='$data_vencimento',
	            ctp_valor_parcela='$vlr_parcela',
	            ctp_valor_juros='$vlr_juros',
	            ctp_descricao_valor_juros='$desc_juros',
	            ctp_valor_desconto='$vlr_desconto',
	            ctp_descricao_valor_desconto='$desc_desconto',
	            ctp_outro_valor='$vlr_acrescimo',
	            ctp_descricao_outro_valor='$desc_acrescimo',
	            ctp_alterado_em='$data_sistema',
	            ctp_alterado_por='$nomeusuario',
	            ctp_descricao_compra='$descricao_compra'
	            WHERE ctp_id='$ctp_id'";
	    $resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);
		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao processar sua solicitação. ' . $erro_mysql));
			mysqli_close($conector);
			exit;
		}
		
		header('Content-type: application/json');
    	echo json_encode(array('success' => true, 'message' => 'Conta alterada com sucesso.'));
		mysqli_close($conector);
		exit;
    }

    if ($tipo_operacao==1){
    	// grava conta para apenas 1 centro de custos
		if ($quantidade_fazendas == 1) {
			if (empty($_POST['vlr_primeira_parcela'])) {
				$vlr_primeira_parcela = 0.00;
			}
			else {
				$vlr_primeira_parcela = str_replace(',','.', str_replace('.','', $_POST['vlr_primeira_parcela']));
			}

			if (empty($_POST['vlr_desconto'])) {
				$vlr_desconto = 0.00;
			}
			else {
				$vlr_desconto = str_replace(',','.', str_replace('.','', $_POST['vlr_desconto']));
			}

			if (empty($_POST['vlr_juros'])) {
				$vlr_juros = 0.00;
			}
			else {
				$vlr_juros = str_replace(',','.', str_replace('.','', $_POST['vlr_juros']));
			}

			if (empty($_POST['vlr_pagamento'])) {
				$vlr_pagamento = 0.00;
			}
			else {
				$vlr_pagamento = str_replace(',','.', str_replace('.','', $_POST['vlr_pagamento']));
			}

			if ($qtd_parcelas==0){
				$numero_parcelas = 1;
			}
	        else {
	        	$numero_parcelas = $qtd_parcelas + 1;
	        }

		    $sql = "INSERT INTO contas_pagar (
				ctp_numero_doc,
				ctp_codigo_fornecedor,
				ctp_parcela,
				ctp_tipo_documento,
				ctp_nome_fornecedor,
				ctp_numero_documento,
				ctp_qtd_parcelas,
				ctp_data_emissao,
				ctp_data_vencimento,
				ctp_valor_parcela,
				ctp_valor_desconto,
				ctp_descricao_valor_desconto,
				ctp_valor_juros,
				ctp_descricao_valor_juros,
				ctp_outro_valor,
				ctp_descricao_outro_valor,
				ctp_situacao,
				ctp_previsao_despesas,
				ctp_agendamento,
				ctp_data_agendamento,
				ctp_valor_total_agendamento,
				ctp_numero_agendamento,
				ctp_codigo_fazenda,
				ctp_codigo_centro_custos,
				ctp_codigo_conta,
				ctp_codigo_banco,
				ctp_numero_cheque,
				ctp_conta_pagamento,
				ctp_aceite,
				ctp_data_aceite,
				ctp_usuario_aceite,
				ctp_incluido_em,
				ctp_incluido_por,
				ctp_alterado_em,
				ctp_alterado_por,
				ctp_descricao_compra
		        ) VALUES (
				'$numero_doc',
				'$codigo_for',
				1,
				'$tipo_documento',
				'$razao',
				'$numero_doc',
				'$numero_parcelas',
				'$data_emissao',
				'$data_vencimento',
				'$vlr_primeira_parcela',
				'$vlr_desconto',
				null,
				'$vlr_juros',
				null,
				null,
				null,
				'',
				null,
				null,
				null,
				null,
				null,
				'$codigo_local',
				'$codigo_c_custo',
				'$codigo_conta',
				null,
				'$numero_cheque',
				'$codigo_forma_pag',
				'',
				null,
				null,
				'$data_sistema',
				'$nomeusuario',
				null,
				null,
				'$descricao_compra'
		    )";

		    $resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
		    	header('Content-type: application/json');
		    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro incluir o registro da 1ª parcela ou parcela única.' . $erro_mysql));
		    	exit;
			} else {
				$numero_id = mysqli_insert_id($conector);
				$numero_id = str_pad($numero_id, 9, "0", STR_PAD_LEFT);

				if ($pago=="S")	{
					$historico = "Pag total do doc para: " . $razao;

			        $sql = "INSERT INTO baixa_contas_pagar (
			        	bcp_id,
			        	bcp_numero_id,
					    bcp_codigo_fornecedor, 
					    bcp_parcela, 
						bcp_sequencia_pagamento, 
						bcp_nome_fornecedor, 
						bcp_numero_documento, 
						bcp_data_pagamento, 
						bcp_valor_pagamento, 
						bcp_situacao,
						bcp_data_aceite,
						bcp_usuario_aceite,
						bcp_numero_agendamento,
						bcp_historico_pagamento)
				           VALUES ('$numero_id',
				           		   '$numero_doc', 
						           '$codigo_for',
								   1,
								   1,
								   '$razao',
								   '$numero_doc', 
					               '$data_pagamento',
								   '$vlr_pagamento',
								   'P',
								   '$data_sistema',
								   '$nomeusuario',
								   null,
								   '$historico')";
								   
					$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);

			        if (!$resultado) {
				    	header('Content-type: application/json');
				    	echo json_encode(array('error' => true, 'message' => 'Ocorreu 2 um erro ao gravar a baixa da conta.' . $erro_mysql));
						mysqli_close($conector);
				    	exit;
					}

		    		$sql = ("UPDATE contas_pagar SET ctp_situacao='P' 
		    			                       WHERE ctp_id='$numero_id'");
		    		$resultado = mysqli_query($conector, $sql);
					$erro_mysql = mysqli_error($conector);
					
		        	if (!$resultado) {
				    	header('Content-type: application/json');
				    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a baixa da conta no ctp.' . $erro_mysql));
		    			mysqli_close($conector);
				    	exit;
					}
				}
			}

			if ($qtd_parcelas==0) {
		    	header('Content-type: application/json');
		    	echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
				mysqli_close($conector);
				exit;
			}

			if ($tipo_inclusao=='P'){
	 			$numero_parcela = 1;
				for ($i=0; $i < $quantidade_prazos; $i++) { 
					$numero_parcela++;
					$string_dias= "+".$vetor_prazos[$i]." days";
					$data_vencimento = date("Y-m-d", strtotime($string_dias,strtotime($data_emissao)));
					$vlr_compra = str_replace(',','.', str_replace('.','', $_POST['vlr_compra']));
		            $vlr_parcela = ($vlr_compra - $vlr_primeira_parcela) / $qtd_parcelas;

				    $sql = "INSERT INTO contas_pagar (
						ctp_numero_doc,
						ctp_codigo_fornecedor,
						ctp_parcela,
						ctp_tipo_documento,
						ctp_nome_fornecedor,
						ctp_numero_documento,
						ctp_qtd_parcelas,
						ctp_data_emissao,
						ctp_data_vencimento,
						ctp_valor_parcela,
						ctp_valor_desconto,
						ctp_descricao_valor_desconto,
						ctp_valor_juros,
						ctp_descricao_valor_juros,
						ctp_outro_valor,
						ctp_descricao_outro_valor,
						ctp_situacao,
						ctp_previsao_despesas,
						ctp_agendamento,
						ctp_data_agendamento,
						ctp_valor_total_agendamento,
						ctp_numero_agendamento,
						ctp_codigo_fazenda,
						ctp_codigo_centro_custos,
						ctp_codigo_conta,
						ctp_codigo_banco,
						ctp_numero_cheque,
						ctp_conta_pagamento,
						ctp_aceite,
						ctp_data_aceite,
						ctp_usuario_aceite,
						ctp_incluido_em,
						ctp_incluido_por,
						ctp_alterado_em,
						ctp_alterado_por,
						ctp_descricao_compra
				        ) VALUES (
						'$numero_doc',
						'$codigo_for',
						'$numero_parcela',
						'$tipo_documento',
						'$razao',
						'$numero_doc',
						'$numero_parcelas',
						'$data_emissao',
						'$data_vencimento',
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
						'$codigo_local',
						'$codigo_c_custo',
						'$codigo_conta',
						null,
						null,
						'$codigo_forma_pag_parc',
						'',
						null,
						null,
						'$data_sistema',
						'$nomeusuario',
						null,
						null,
						'$descricao_compra'
				    )";

				    $resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
					    header('Content-type: application/json');
					    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro incluir o registro por parcelas por prazo de pagamento.' . $erro_mysql));
						mysqli_close($conector);
						exit;
					} 
				}
			    header('Content-type: application/json');
			    echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
				exit;
			}
			else {
				$vlr_parcela_fixa = str_replace(',','.', str_replace('.','', $_POST['vlr_parcela_fixa']));
	 			$numero_parcela = 1;

				for ($i=0; $i < $qtd_parcelas; $i++) { 
					$numero_parcela++;

			       	if ($numero_parcela==2){
			       		$data_vencimento = $data_inicial;
			       	}
			       	else {
						switch ($frequencia) {
					    case 1:
					   		$data_vencimento = date("Y-m-d", strtotime('+1 day',strtotime($data_vencimento)));
					        break;
					    case 2:
					   		$data_vencimento = date("Y-m-d", strtotime('+1 week',strtotime($data_vencimento)));
					        break;
					    case 3:   
					   		$data_vencimento = date("Y-m-d", strtotime('+2 week',strtotime($data_vencimento)));
						  	break;
					    case 4:   
					   		$data_vencimento = date("Y-m-d", strtotime('+1 month',strtotime($data_vencimento)));
						  	break;
					    case 5:   
					   		$data_vencimento = date("Y-m-d", strtotime('+2 month',strtotime($data_vencimento)));
						  	break;
					    case 6:   
					   		$data_vencimento = date("Y-m-d", strtotime('+3 month',strtotime($data_vencimento)));
						  	break;
					    case 7:   
					   		$data_vencimento = date("Y-m-d", strtotime('+6 month',strtotime($data_vencimento)));
						  	break;
					    case 8:   
					   		$data_vencimento = date("Y-m-d", strtotime('+12 month',strtotime($data_vencimento)));
						  	break;
						} 
			       	}

				    $sql = "INSERT INTO contas_pagar (
						ctp_numero_doc,
						ctp_codigo_fornecedor,
						ctp_parcela,
						ctp_tipo_documento,
						ctp_nome_fornecedor,
						ctp_numero_documento,
						ctp_qtd_parcelas,
						ctp_data_emissao,
						ctp_data_vencimento,
						ctp_valor_parcela,
						ctp_valor_desconto,
						ctp_descricao_valor_desconto,
						ctp_valor_juros,
						ctp_descricao_valor_juros,
						ctp_outro_valor,
						ctp_descricao_outro_valor,
						ctp_situacao,
						ctp_previsao_despesas,
						ctp_agendamento,
						ctp_data_agendamento,
						ctp_valor_total_agendamento,
						ctp_numero_agendamento,
						ctp_codigo_fazenda,
						ctp_codigo_centro_custos,
						ctp_codigo_conta,
						ctp_codigo_banco,
						ctp_numero_cheque,
						ctp_conta_pagamento,
						ctp_aceite,
						ctp_data_aceite,
						ctp_usuario_aceite,
						ctp_incluido_em,
						ctp_incluido_por,
						ctp_alterado_em,
						ctp_alterado_por,
						ctp_descricao_compra
				        ) VALUES (
						null,
						'$codigo_for',
						'$numero_parcela',
						null,
						'$razao',
						'$numero_doc',
						'$numero_parcelas',
						'$data_emissao',
						'$data_vencimento',
						'$vlr_parcela_fixa',
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
						'$codigo_local',
						'$codigo_c_custo',
						'$codigo_conta',
						null,
						null,
						'$codigo_forma_pag_parc',
						'',
						null,
						null,
						'$data_sistema',
						'$nomeusuario',
						null,
						null,
						'$descricao_compra'
				    )";

				    $resultado = mysqli_query($conector,$sql);
					$erro_mysql = mysqli_error($conector);

					if (!$resultado){
					    header('Content-type: application/json');
					    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro incluir o registro por parcelas por repeticao.' . $erro_mysql));
						mysqli_close($conector);
						exit;
					} 
				}
			    header('Content-type: application/json');
			    echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
				mysqli_close($conector);
				exit;
			}
		} // fim do if $quantidade_fazendas
		else {
		    // grava conta para apenas 2 ou mais fazendas

			$array_itens = $_POST['array_fazendas'];
			$matriz_itens = explode("<|>", $array_itens);
			$quantidade_itens = count($matriz_itens);

			for($k=0; $k < $quantidade_itens; $k++) {
	    		$tabela_itens = $matriz_itens[$k];

	    		$itens = explode("|", $tabela_itens);
				$codigo_local = $itens[0];
				$percentual = $itens[1];
				$primeira_parcela = $itens[2];
				$parcela_restante= $itens[3];

				$vlr_desconto = 0.00;
				$vlr_juros = 0.00;
				$vlr_pagamento = 0.00;

				if ($qtd_parcelas==0){
					$numero_parcelas = 1;
				}
		        else {
		        	$numero_parcelas = $qtd_parcelas + 1;
		        }

				$numero_doc = $_POST['number_doc'];

			    $sql = "INSERT INTO contas_pagar (
					ctp_numero_doc,
					ctp_codigo_fornecedor,
					ctp_parcela,
					ctp_tipo_documento,
					ctp_nome_fornecedor,
					ctp_numero_documento,
					ctp_qtd_parcelas,
					ctp_data_emissao,
					ctp_data_vencimento,
					ctp_valor_parcela,
					ctp_valor_desconto,
					ctp_descricao_valor_desconto,
					ctp_valor_juros,
					ctp_descricao_valor_juros,
					ctp_outro_valor,
					ctp_descricao_outro_valor,
					ctp_situacao,
					ctp_previsao_despesas,
					ctp_agendamento,
					ctp_data_agendamento,
					ctp_valor_total_agendamento,
					ctp_numero_agendamento,
					ctp_codigo_fazenda,
					ctp_codigo_centro_custos,
					ctp_codigo_conta,
					ctp_codigo_banco,
					ctp_numero_cheque,
					ctp_conta_pagamento,
					ctp_aceite,
					ctp_data_aceite,
					ctp_usuario_aceite,
					ctp_incluido_em,
					ctp_incluido_por,
					ctp_alterado_em,
					ctp_alterado_por,
					ctp_descricao_compra
			        ) VALUES (
					'$numero_doc',
					'$codigo_for',
					1,
					'$tipo_documento',
					'$razao',
					'$numero_doc',
					'$numero_parcelas',
					'$data_emissao',
					'$data_vencimento',
					'$primeira_parcela',
					'$vlr_desconto',
					null,
					'$vlr_juros',
					null,
					null,
					null,
					'',
					null,
					null,
					null,
					null,
					null,
					'$codigo_local',
					'$codigo_c_custo',
					'$codigo_conta',
					null,
					'$numero_cheque',
					'$codigo_forma_pag',
					'',
					null,
					null,
					'$data_sistema',
					'$nomeusuario',
					null,
					null,
					'$descricao_compra'
			    )";

			    $resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
			    	header('Content-type: application/json');
			    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro incluir o registro da 1ª parcela ou parcela única para o C.Custo ' . $codigo_c_custo .' '. $erro_mysql));
			    	exit;
				} 
				else {
					$numero_id = mysqli_insert_id($conector);
					$numero_id = str_pad($numero_id, 9, "0", STR_PAD_LEFT);

					if ($pago=="S")	{
						$historico = "Pag total do doc para: " . $razao;

				        $sql = "INSERT INTO baixa_contas_pagar (
				        	bcp_id,
				        	bcp_numero_id,
						    bcp_codigo_fornecedor, 
						    bcp_parcela, 
							bcp_sequencia_pagamento, 
							bcp_nome_fornecedor, 
							bcp_numero_documento, 
							bcp_data_pagamento, 
							bcp_valor_pagamento, 
							bcp_situacao,
							bcp_data_aceite,
							bcp_usuario_aceite,
							bcp_numero_agendamento,
							bcp_historico_pagamento)
					           VALUES ('$numero_id',
					           		   '$numero_doc', 
							           '$codigo_for',
									   1,
									   1,
									   '$razao',
									   '$numero_doc', 
						               '$data_pagamento',
									   '$primeira_parcela',
									   'P',
									   '$data_sistema',
									   '$nomeusuario',
									   null,
									   '$historico')";
									   
						$resultado = mysqli_query($conector, $sql);
						$erro_mysql = mysqli_error($conector);

				        if (!$resultado) {
					    	header('Content-type: application/json');
					    	echo json_encode(array('error' => true, 'message' => 'Ocorreu 1 um erro ao gravar a baixa da conta.' . $erro_mysql));
							mysqli_close($conector);
					    	exit;
						}

			    		$sql = ("UPDATE contas_pagar SET ctp_situacao='P' 
			    			    WHERE ctp_id='$numero_id'");
			    		$resultado = mysqli_query($conector, $sql);
						$erro_mysql = mysqli_error($conector);
						
			        	if (!$resultado) {
					    	header('Content-type: application/json');
					    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao gravar a baixa da conta no ctp.' . $erro_mysql));
			    			mysqli_close($conector);
					    	exit;
						}
					}
				}

				/*if ($qtd_parcelas==0) {
			    	header('Content-type: application/json');
			    	echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
					mysqli_close($conector);
					exit;
				}*/

				if ($qtd_parcelas!=0) {
					if ($tipo_inclusao=='P'){
			 			$numero_parcela = 1;
						for ($i=0; $i < $quantidade_prazos; $i++) { 
							$numero_parcela++;
							$string_dias= "+".$vetor_prazos[$i]." days";
							$data_vencimento_par = date("Y-m-d", strtotime($string_dias,strtotime($data_emissao)));
						    $sql = "INSERT INTO contas_pagar (
								ctp_numero_doc,
								ctp_codigo_fornecedor,
								ctp_parcela,
								ctp_tipo_documento,
								ctp_nome_fornecedor,
								ctp_numero_documento,
								ctp_qtd_parcelas,
								ctp_data_emissao,
								ctp_data_vencimento,
								ctp_valor_parcela,
								ctp_valor_desconto,
								ctp_descricao_valor_desconto,
								ctp_valor_juros,
								ctp_descricao_valor_juros,
								ctp_outro_valor,
								ctp_descricao_outro_valor,
								ctp_situacao,
								ctp_previsao_despesas,
								ctp_agendamento,
								ctp_data_agendamento,
								ctp_valor_total_agendamento,
								ctp_numero_agendamento,
								ctp_codigo_fazenda,
								ctp_codigo_centro_custos,
								ctp_codigo_conta,
								ctp_codigo_banco,
								ctp_numero_cheque,
								ctp_conta_pagamento,
								ctp_aceite,
								ctp_data_aceite,
								ctp_usuario_aceite,
								ctp_incluido_em,
								ctp_incluido_por,
								ctp_alterado_em,
								ctp_alterado_por,
								ctp_descricao_compra
						        ) VALUES (
								'$numero_doc',
								'$codigo_for',
								'$numero_parcela',
								'$tipo_documento',
								'$razao',
								'$numero_doc',
								'$numero_parcelas',
								'$data_emissao',
								'$data_vencimento_par',
								'$parcela_restante',
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
								'$codigo_local',
								'$codigo_c_custo',
								'$codigo_conta',
								null,
								null,
								'$codigo_forma_pag_parc',
								'',
								null,
								null,
								'$data_sistema',
								'$nomeusuario',
								null,
								null,
								'$descricao_compra'
						    )";

						    $resultado = mysqli_query($conector,$sql);
							$erro_mysql = mysqli_error($conector);

							if (!$resultado){
							    header('Content-type: application/json');
							    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro incluir o registro por parcelas por prazo de pagamento.' . $erro_mysql));
								mysqli_close($conector);
								exit;
							} 
						}
					}
					else {
			 			$numero_parcela = 1;

						for ($i=0; $i < $qtd_parcelas; $i++) { 
							$numero_parcela++;

					       	if ($numero_parcela==2){
					       		$data_vencimento_par = $data_inicial;
					       	}
					       	else {
								switch ($frequencia) {
							    case 1:
							   		$data_vencimento_par = date("Y-m-d", strtotime('+1 day',strtotime($data_vencimento_par)));
							        break;
							    case 2:
							   		$data_vencimento_par = date("Y-m-d", strtotime('+1 week',strtotime($data_vencimento_par)));
							        break;
							    case 3:   
							   		$data_vencimento_par = date("Y-m-d", strtotime('+2 week',strtotime($data_vencimento_par)));
								  	break;
							    case 4:   
							   		$data_vencimento_par = date("Y-m-d", strtotime('+1 month',strtotime($data_vencimento_par)));
								  	break;
							    case 5:   
							   		$data_vencimento_par = date("Y-m-d", strtotime('+2 month',strtotime($data_vencimento_par)));
								  	break;
							    case 6:   
							   		$data_vencimento_par = date("Y-m-d", strtotime('+3 month',strtotime($data_vencimento_par)));
								  	break;
							    case 7:   
							   		$data_vencimento_par = date("Y-m-d", strtotime('+6 month',strtotime($data_vencimento_par)));
								  	break;
							    case 8:   
							   		$data_vencimento_par = date("Y-m-d", strtotime('+12 month',strtotime($data_vencimento_par)));
								  	break;
								} 
					       	}

						    $sql = "INSERT INTO contas_pagar (
								ctp_numero_doc,
								ctp_codigo_fornecedor,
								ctp_parcela,
								ctp_tipo_documento,
								ctp_nome_fornecedor,
								ctp_numero_documento,
								ctp_qtd_parcelas,
								ctp_data_emissao,
								ctp_data_vencimento,
								ctp_valor_parcela,
								ctp_valor_desconto,
								ctp_descricao_valor_desconto,
								ctp_valor_juros,
								ctp_descricao_valor_juros,
								ctp_outro_valor,
								ctp_descricao_outro_valor,
								ctp_situacao,
								ctp_previsao_despesas,
								ctp_agendamento,
								ctp_data_agendamento,
								ctp_valor_total_agendamento,
								ctp_numero_agendamento,
								ctp_codigo_fazenda,
								ctp_codigo_centro_custos,
								ctp_codigo_conta,
								ctp_codigo_banco,
								ctp_numero_cheque,
								ctp_conta_pagamento,
								ctp_aceite,
								ctp_data_aceite,
								ctp_usuario_aceite,
								ctp_incluido_em,
								ctp_incluido_por,
								ctp_alterado_em,
								ctp_alterado_por,
								ctp_descricao_compra
						        ) VALUES (
								null,
								'$codigo_for',
								'$numero_parcela',
								null,
								'$razao',
								'$numero_doc',
								'$numero_parcelas',
								'$data_emissao',
								'$data_vencimento_par',
								'$parcela_restante',
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
								'$codigo_local',
								'$codigo_c_custo',
								'$codigo_conta',
								null,
								null,
								'$codigo_forma_pag_parc',
								'',
								null,
								null,
								'$data_sistema',
								'$nomeusuario',
								null,
								null,
								'$descricao_compra'
						    )";

						    $resultado = mysqli_query($conector,$sql);
							$erro_mysql = mysqli_error($conector);

							if (!$resultado){
							    header('Content-type: application/json');
							    echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro incluir o registro por parcelas por repeticao.' . $erro_mysql));
								mysqli_close($conector);
								exit;
							} 
						}
					}
				}
			}

		} // fim do if grava 2 ou mais fazendas

		header('Content-type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Conta incluída com sucesso.'));
		mysqli_close($conector);
		exit;

	}// fim do if de gravacao do tipo 1


	mysqli_close($conector);
	exit;

?>