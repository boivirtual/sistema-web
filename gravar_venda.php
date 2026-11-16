<?php 
	include "conecta_mysql.inc";

	@ session_start(); 
	$nomeusuario = $_SESSION['nome_usuario'];
	$numero_venda_id= $_POST['numero_venda_id'];

	$data_emissao= $_POST['data_venda'];
	$tem_movimentacao= $_POST['tem_movimentacao'];
	$codigo_movimentacao= $_POST['lista_movimentacao'];
	$local= $_POST['local'];
	$codigo_cliente= $_POST['codigo_cliente'];
	$tipo_venda= $_POST['tipo_venda'];
	$total_venda= $_POST['total_venda'];
	$desconto_final= $_POST['desconto_final'];
	$total_receber= $_POST['total_receber'];
	$conta_contabil= $_POST['conta_contabil'];
	$centro_custos= $_POST['centro_custos'];
	$valor_pri_parcela= $_POST['valor_pri_parcela'];
	$vencimento_pri_parcela= $_POST['vencimento_pri_parcela'];
	$forma_pri= $_POST['forma_pri'];
	$conta_pri= $_POST['conta_pri'];
	$gta= $_POST['gta'];
	$transportadora= $_POST['transportadora'];
	$nome_motorista= $_POST['nome_motorista'];

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
	        1,
			'$local',
			'$codigo_cliente',
			'$situacao',
			'$codigo_movimentacao',
			'$data_emissao',
			'$tipo_venda',
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
	   	echo json_encode(array('error' => true, 'message' => 'Ocorreu um erro ao registrar a venda'. $erro_mysql));
	   	mysqli_close($conector);
		exit;
	} 

	$numero_venda = mysqli_insert_id($conector);
	$numero_venda = str_pad($numero_venda, 9, "0", STR_PAD_LEFT);

	$resposta = array('success' => true, 'message' => 'Venda incluída com sucesso.');

	if ($codigo_movimentacao!=0) {
	    $sql = "UPDATE tbl_movimentacao SET
			tbl_movimentacao_codigo_venda='$numero_venda',
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
		$peso_ajustado = $itens[11];
		$fator_arroba = $itens[12];

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
		            '$numero_venda',
		            '$numero_item',
		            '$data_emissao',
		            '$categoria',
		            '$sexo',
		            '$qtd',
		            '$peso',
		            '$peso_ajustado',
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

	$gerar_contas = gerar_contas_receber($numero_venda, $codigo_cliente, $conta_contabil, $valor_pri_parcela, $vencimento_pri_parcela, $forma_pri, $conta_pri, $array_parcelas, $data_emissao, $centro_custos, $local, $conector);

	if ($gerar_contas=='Gravei') {
	   	header('Content-type: application/json');
	   	echo json_encode($resposta);
		mysqli_close($conector);
		exit;
	}
	else {
	   	header('Content-type: application/json');
	   	echo json_encode($gerar_contas);
		mysqli_close($conector);
		exit;
	}

	function gerar_contas_receber($numero_venda, $codigo_cliente, $conta_contabil, $valor_pri_parcela, $vencimento_pri_parcela, $forma_pri, $conta_pri, $array_parcelas, $data_emissao, $centro_custos, $local, $conector) {
	    $data_sistema = date("Y-m-d H:i:s");

		@ session_start(); 
	    $nomeusuario = $_SESSION['nome_usuario'];

		$matriz_parcelas = explode("<|>", $array_parcelas);
		$quantidade_parcelas = count($matriz_parcelas);

	    $tbl_cliente = "select * from tbl_pessoa where tbl_pessoa_id='$codigo_cliente'";
	    $rs_cliente = mysqli_query($conector, $tbl_cliente); 
	    $reg_cliente = mysqli_fetch_object($rs_cliente);
		$nome_cliente = $reg_cliente->tbl_pessoa_nome; 

		$numero_parcela=1;

		if ($array_parcelas!='') {
			$ctr_qtd_parcelas = $quantidade_parcelas + 1;
		}
		else {
			$ctr_qtd_parcelas =1;
		}

		// INSERIR PRIMEIRA PARCELA
		$sql = "INSERT INTO contas_receber (
				ctr_numero_doc,
				ctr_parcela,
				ctr_qtd_parcelas,
				ctr_tipo,
				ctr_ano_base,
				ctr_semestre,
				ctr_codigo_cliente_fornecedor,
				ctr_nome_cliente,
				ctr_codigo_conta_recebimento,
				ctr_codigo_forma_recebimento,
				ctr_codigo_conta,
				ctr_codigo_fazenda,
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
				ctr_lixeira,
				ctr_lixeira_em,
				ctr_lixeira_por

		) VALUES (
				'$numero_venda',
				'$numero_parcela',
				'$ctr_qtd_parcelas',
				null,
				null,
				null,
				'$codigo_cliente',
				'$nome_cliente',
				'$conta_pri',
				'$forma_pri',
				'$conta_contabil',
				'$local',
				'$centro_custos',
				null,
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
				null,
				null,
				null,
				null,
				'Venda',
				null,
				null,
				null,
				null,
				null,
				'$data_sistema',
				'$nomeusuario',
				0,
				null,
				null
			    )";

		$resultado = mysqli_query($conector,$sql);
		$erro_mysql = mysqli_error($conector);

		if (!$resultado){
			return 'Erro ao gravar a conta primeira parcela! ' . $erro_mysql;
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

				$sql = "INSERT INTO contas_receber (
						ctr_numero_doc,
						ctr_parcela,
						ctr_qtd_parcelas,
						ctr_tipo,
						ctr_ano_base,
						ctr_semestre,
						ctr_codigo_cliente_fornecedor,
						ctr_nome_cliente,
						ctr_codigo_conta_recebimento,
						ctr_codigo_forma_recebimento,
						ctr_codigo_conta,
						ctr_codigo_fazenda,
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
						ctr_lixeira,
						ctr_lixeira_em,
						ctr_lixeira_por

				) VALUES (
						'$numero_venda',
						'$numero_parcela',
						'$ctr_qtd_parcelas',
						null,
						null,
						null,
						'$codigo_cliente',
						'$nome_cliente',
						'$conta',
						'$forma',
						'$conta_contabil',
						'$local',
						'$centro_custos',
						null,
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
						null,
						null,
						null,
						null,
						'Venda',
						null,
						null,
						null,
						null,
						null,
						'$data_sistema',
						'$nomeusuario',
						0,
						null,
						null
					    )";

				$resultado = mysqli_query($conector,$sql);
				$erro_mysql = mysqli_error($conector);

				if (!$resultado){
					return 'Erro ao gravar a conta parcela ' . $numero_parcela . ' - ' . $erro_mysql;
				} 
	        }
	    	return 'Gravei';
		}
		else {
	    	return 'Gravei';
		}

	} // Fim Function

?>