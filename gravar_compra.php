<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_compra_id= $_POST['numero_compra_id'];

	$data_emissao= $_POST['data_compra'];
	$tem_movimentacao= $_POST['tem_movimentacao_compra'];
	$codigo_movimentacao= $_POST['lista_movimentacao_compra'];
	$local_origem= $_POST['local_origem'];
	$local_destino= $_POST['local_destino'];
	$tipo_compra= $_POST['tipo_compra'];
	$total_venda= $_POST['total_venda'];
	$desconto_final= $_POST['desconto_final'];
	$total_receber= $_POST['total_receber'];
	$conta_contabil= $_POST['conta_contabil'];
	$centro_custos= $_POST['centro_custos'];
	$valor_pri_parcela= $_POST['valor_pri_parcela'];
	$vencimento_pri_parcela= $_POST['vencimento_pri_parcela'];
	$forma_pri= $_POST['forma_pri'];
	$conta_pri= $_POST['conta_pri'];
	$gta= '';
	$transportadora= '';
	$nome_motorista= '';

	$data_sistema = date("Y-m-d H:i:s");

	$array_itens = $_POST['array_itens'];
	$matriz_itens = explode("<|>", $array_itens);
	$quantidade_itens = count($matriz_itens);

	$array_parcelas = $_POST['array_parcelas'];
	$matriz_parcelas = explode("<|>", $array_parcelas);
	$quantidade_parcelas = count($matriz_parcelas);

	if (empty($_POST['total_venda'])){
		$total_venda = 0.00;
	}
	else {
		$total_venda= str_replace(',','.', str_replace('.','', $_POST['total_venda']));
	}

	if (empty($_POST['desconto_final'])){
		$desconto_final = 0.00;
	}
	else {
		$desconto_final= str_replace(',','.', str_replace('.','', $_POST['desconto_final']));
	}

	if (empty($_POST['total_receber'])){
		$total_receber = 0.00;
	}
	else {
		$total_receber= str_replace(',','.', str_replace('.','', $_POST['total_receber']));
	}

	if (empty($_POST['valor_pri_parcela'])){
		$valor_pri_parcela = 0.00;
	}
	else {
		$valor_pri_parcela= str_replace(',','.', str_replace('.','', $_POST['valor_pri_parcela']));
	}

	if ($codigo_movimentacao==0 || $codigo_movimentacao=='') {
		$situacao = 'N';
	}
	else {
		$situacao = 'S';
	}

	$sql = "INSERT INTO tbl_venda (
			tbl_venda_categoria,
			tbl_venda_codigo_local_origem,
			tbl_venda_codigo_local_destino,
			tbl_venda_situacao,
			tbl_venda_codigo_movimentacao,
			tbl_venda_emissao,
			tbl_venda_tipo,
			tbl_venda_total_venda,
			tbl_venda_total_desconto,
			tbl_venda_total_receber,
			tbl_venda_valor_primeira_parcela,
			tbl_venda_vencimento_primeira_parcela,
			tbl_venda_forma_pgto_primeira_parcela,
			tbl_venda_conta_pgto_primeira_parcela,
			tbl_venda_gta,
			tbl_venda_nome_transportadora,
			tbl_venda_dados_motorista,
			tbl_venda_conta_contabil,
			tbl_venda_centro_custos,
			tbl_venda_array_itens,
			tbl_venda_array_parcelas,
			tbl_venda_incluido_em,
			tbl_venda_incluido_por,
			tbl_venda_alterado_em,
			tbl_venda_alterado_por,
			tbl_venda_lixeira,
			tbl_venda_lixeira_em,
			tbl_venda_lixeira_por
	        ) VALUES (
	        2,
			'$local_origem',
			'$local_destino',
			'$situacao',
			'$codigo_movimentacao',
			'$data_emissao',
			'$tipo_compra',
			'$total_venda',
			'$desconto_final',
			'$total_receber',
			'$valor_pri_parcela',
			'$vencimento_pri_parcela',
			'$forma_pri',
			'$conta_pri',
			'$gta',
			'$transportadora',
			'$nome_motorista',
			'$conta_contabil',
			'$centro_custos',
			'$array_itens',
			'$array_parcelas',
			'$data_sistema',
			'$nomeusuario',
			null,
			null,
			0,
			null,
			null
	)";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
	   	header('Content-type: application/json');
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a compra'. $erro_mysql));
	   	mysqli_close($conector);
		exit;
	} 

	$numero_compra = mysqli_insert_id($conector);
	$numero_compra = str_pad($numero_compra, 9, "0", STR_PAD_LEFT);

	if ($codigo_movimentacao!=0) {
	    $sql = "UPDATE tbl_movimentacao SET
			tbl_movimentacao_codigo_venda='$numero_compra',
			tbl_movimentacao_situacao='S',
			tbl_movimentacao_aceite_financeiro_em='$data_sistema',
			tbl_movimentacao_aceite_financeiro_por='$nomeusuario'
	    WHERE tbl_movimentacao_id  ='$codigo_movimentacao'";

	    $resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
	    	header('Content-type: application/json');
	    	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro atualizar o registro da movimentação ' . $erro_mysql));
	    	exit;
		} 
	}

	//gravar itens	
	for($i=0; $i < $quantidade_itens; $i++) {
    	$tabela_itens = $matriz_itens[$i];

    	$itens = explode("|", $tabela_itens);
		$categoria = $itens[0];
		$sexo = $itens[1];
		$qtd = $itens[2];
		$peso = $itens[3];
		$peso_morto = $itens[4];
		$unidade = $itens[5];
		$vlr_uni = $itens[6];
		$vlr_total = $itens[7];
		$arroba = $itens[8];
		$per_rendimento = $itens[9];
		$conta = $itens[10];
		$fator_arroba = $itens[11];

		$numero_item = $i + 1;
			
	    $sql = "INSERT INTO tbl_item_venda (
					tbl_ite_venda_numero_id,
					tbl_ite_venda_numero_item,
					tbl_ite_venda_data_emissao,
					tbl_ite_venda_categoria,
					tbl_ite_venda_sexo,
					tbl_ite_venda_quantidade,
					tbl_ite_venda_peso_vivo,
					tbl_ite_venda_peso_vivo_ajustado,
					tbl_ite_venda_peso_morto,
					tbl_ite_venda_arroba,
					tbl_ite_venda_unidade_negociada,
					tbl_ite_venda_valor_unitario,
					tbl_ite_venda_valor_total,
					tbl_ite_percentual_rendimento,
					tbl_ite_conta_contabil,
					tbl_ite_fator_arroba
		        ) VALUES (
		            '$numero_compra',
		            '$numero_item',
		            '$data_emissao',
		            '$categoria',
		            '$sexo',
		            '$qtd',
		            '$peso',
		            0,
		            '$peso_morto',
		            '$arroba',
		            '$unidade',
		            '$vlr_uni',
		            '$vlr_total',
		            '$per_rendimento',
		            '$conta',
		            '$fator_arroba'
	    )";
	    $resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
		  	header('Content-type: application/json');
		   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro na gravação dos itens.' . $erro_mysql));
			mysqli_close($conector);
			exit;
		} 
	}    

	$tbl_cliente = "select * from tbl_pessoa where tbl_pessoa_id='$local_origem'";
	$rs_cliente = mysqli_query($conector, $tbl_cliente); 
	$reg_cliente = mysqli_fetch_object($rs_cliente);
	$nome_cliente = $reg_cliente->tbl_pessoa_nome; 

	$numero_parcela=1;

	if ($array_parcelas!='') {
		$ctp_qtd_parcelas = $quantidade_parcelas + 1;
	}
	else {
		$ctp_qtd_parcelas =1;
	}

	// INSERIR PRIMEIRA PARCELA
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
			ctp_forma_pagamento,
			ctp_aceite,
			ctp_data_aceite,
			ctp_usuario_aceite,
			ctp_incluido_em,
			ctp_incluido_por,
			ctp_alterado_em,
			ctp_alterado_por,
			ctp_descricao_compra

		) VALUES (
				'$numero_compra',
				'$local_origem',
				'$numero_parcela',
				0,
				'$nome_cliente',
				'$numero_compra',
				'$ctp_qtd_parcelas',
				'$data_emissao',
				'$vencimento_pri_parcela',
				'$valor_pri_parcela',
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
				'$local_destino',
				'$centro_custos',
				'$conta_contabil',
				null,
				null,
				'$conta_pri',
				'$forma_pri',
				'',
				null,
				null,
				'$data_sistema',
				'$nomeusuario',
				null,
				null,
				'Compra'
			    )";

	$resultado = mysqli_query($conector,$sql);
	$erro_mysql = mysqli_error($conector);

	if (!$resultado){
		header('Content-type: application/json');
		echo json_encode(array('error' => true, 'message' => 'Erro ao gravar a conta primeira parcela.' . $erro_mysql));
		mysqli_close($conector);
		exit;
	} 

	if ($array_parcelas!='') {
		for($i=0; $i < $quantidade_parcelas; $i++) {
	    	$tabela_parcelas = $matriz_parcelas[$i];

	    	$itens = explode("|", $tabela_parcelas);
			$prazo = $itens[0];
			$valor = $itens[1];
			$forma = $itens[2];
			$conta = $itens[3];

			$string_dias= "+".$prazo." days";
			$data_vencimento = date("Y-m-d", strtotime($string_dias,strtotime($data_emissao)));
			$numero_parcela++;

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
					ctp_forma_pagamento,
					ctp_aceite,
					ctp_data_aceite,
					ctp_usuario_aceite,
					ctp_incluido_em,
					ctp_incluido_por,
					ctp_alterado_em,
					ctp_alterado_por,
					ctp_descricao_compra
				) VALUES (
						'$numero_compra',
						'$local_origem',
						'$numero_parcela',
						0,
						'$nome_cliente',
						'$numero_compra',
						'$ctp_qtd_parcelas',
						'$data_emissao',
						'$data_vencimento',
						'$valor',
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
						'$local_destino',
						'$centro_custos',
						'$conta_contabil',
						null,
						null,
						'$conta_pri',
						'$forma_pri',
						'',
						null,
						null,
						'$data_sistema',
						'$nomeusuario',
						null,
						null,
						'Compra'
			    )";

			$resultado = mysqli_query($conector,$sql);
			$erro_mysql = mysqli_error($conector);

			if (!$resultado){
				header('Content-type: application/json');
				echo json_encode(array('error' => true, 'message' => 'Erro ao gravar a conta parcela ' . $numero_parcela . ' - ' . $erro_mysql));
				mysqli_close($conector);
				exit;
			} 
	    }
	}

	$resposta = array('success' => true, 'message' => 'Compra incluída com sucesso.');

   	header('Content-type: application/json');
  	echo json_encode($resposta);
	mysqli_close($conector);
	exit;

?>