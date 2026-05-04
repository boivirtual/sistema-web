<?php
    $data_sistema = date("Y-m-d");

    @ session_start(); 
    $_SESSION['data_inicio_ctr']=0; 
	$_SESSION['data_fim_ctr']=0; 
	$_SESSION['tipo_data_ctr']=''; 
	$_SESSION['lista_ctr']=''; 
	$_SESSION['razao_nome_ctr']=''; 
	$_SESSION['codigo_c_custo_ctr']=''; 
    $_SESSION['tipo_rel_ctr']=''; 
    $_SESSION['codigo_conta_ctr']=''; 
    $_SESSION['codigo_local_ctr']=''; 
    $_SESSION['limpa_conta_ctr']='';

    $_SESSION['data_inicio_ctp']=0; 
	$_SESSION['data_fim_ctp']=0; 
	$_SESSION['tipo_data_ctp']=''; 
	$_SESSION['lista_ctp']=''; 
	$_SESSION['razao_nome_ctp']=''; 
	$_SESSION['codigo_c_custo_ctp']=''; 
    $_SESSION['codigo_local_ctp']=''; 
    $_SESSION['codigo_conta_ctp']=''; 
    $_SESSION['tipo_rel_ctp']=''; 
    $_SESSION['limpa_conta_ctp']='';

    $_SESSION['data_inicial_aceite']=0; 
    $_SESSION['data_final_aceite']=0; 
    $_SESSION['tipo_data_aceite']=''; 
    $_SESSION['codigo_fornecedor_aceite']=''; 
    $_SESSION['codigo_local_aceite']=''; 
    $_SESSION['codigo_conta_aceite']=''; 
    $_SESSION['limpa_conta_aceite']='';
    
    $_SESSION['codigo_local_previsao']=''; 
    $_SESSION['codigo_conta_previsao']=''; 
    $_SESSION['limpa_conta_previsao']='';
    $_SESSION['lista_previsao']='';

    $_SESSION['data_inicial_ped_compra']=0; 
	$_SESSION['data_final_ped_compra']=0; 
	$_SESSION['situacao_ped_compra']=''; 
	$_SESSION['lista_ped_compra']='S'; 

    $_SESSION['data_inicial_ped']=0; 
	$_SESSION['data_final_ped']=0; 
	$_SESSION['situacao_ped']=''; 
	$_SESSION['lista_ped']='S'; 

	$_SESSION['raca']=[''];
    $_SESSION['pai']=[''];
    $_SESSION['mae']=[''];
    $_SESSION['sexo']=['Todos'];
    $_SESSION['ativo']='S';
    $_SESSION['ativo_filtro']=['S'];
    $_SESSION['local']=[''];
    $_SESSION['origem']=[''];
    $_SESSION['categoria']=[''];
    $_SESSION['codigo_alfa']='';
    $_SESSION['codigo_numerico']=''; 
    $_SESSION['peso_nasc_inicial']=''; 
    $_SESSION['peso_desmama_inicial']=''; 
    $_SESSION['peso_ultimo_inicial']=''; 
    $_SESSION['peso_nasc_final']=''; 
    $_SESSION['peso_desmama_final']=''; 
    $_SESSION['peso_ultimo_final']=''; 
    $_SESSION['data_nasc_inicial']=''; 
    $_SESSION['data_nasc_final']=''; 
    $_SESSION['lista_animais']='';
    $_SESSION['previsao_parto_de']='';
    $_SESSION['previsao_parto_ate']='';
    $_SESSION['data_paricao_de']='';
    $_SESSION['data_paricao_ate']='';
    $_SESSION['num_parto_de']='';
    $_SESSION['num_parto_ate']='';
    $_SESSION['num_aborto_de']='';
    $_SESSION['num_aborto_ate']='';
    $_SESSION['solteiras']='';
    $_SESSION['descarte']='';
    $_SESSION['prenhes']='';
    $_SESSION['paridas']='';
    $_SESSION['data_paridas_ate']='';
    $_SESSION['estacao_monta']='';
    $_SESSION['positivo']='';
    $_SESSION['negativo']='';
    
    $_SESSION['conta_contabil']=[''];
    $_SESSION['local_precisao_contas']=[''];

    $_SESSION['forma_pag_rel']=[''];
    $_SESSION['local_precisao_contas_rel']=[''];

    $_SESSION['local_pastos']=[''];

    $_SESSION['local_movimentacao']='';
    $_SESSION['tipo_movimentacao']=[''];
    $_SESSION['data_inicial_movimentacao']=''; 
    $_SESSION['data_final_movimentacao']=''; 
    $_SESSION['lista_movimentacao']='';

    $_SESSION['local_nutricao']='000000000';
    $_SESSION['produto_nutricao']='';
    $_SESSION['data_inicial_nutricao']=''; 
    $_SESSION['data_final_nutricao']=''; 
    $_SESSION['tipo_rel_nutricao']='';

    $_SESSION['local_origem_compra_venda']='';
    $_SESSION['local_destino_compra_venda']='';
    $_SESSION['tipo_compra_venda']='';
    $_SESSION['data_inicial_compra_venda']=''; 
    $_SESSION['data_final_compra_venda']=''; 
    $_SESSION['lista_compra_venda']='';

    $_SESSION['local_pesagem']='';
    $_SESSION['epoca_pesagem']=[''];
    $_SESSION['data_inicial_pesagem']=''; 
    $_SESSION['data_final_pesagem']=''; 
    $_SESSION['lista_pesagem']='';
    $_SESSION['local_pesagem_rel']='';
    $_SESSION['array_categoria_pesagem_rel']='';
    $_SESSION['sexo_pesagem_rel']='';

    $_SESSION['tipo_rel_historico_animais']='G'; 
    $_SESSION['categoria_historico_animais']=''; 

    $_SESSION['raca_peso']=[''];
    $_SESSION['pai_peso']=[''];
    $_SESSION['mae_peso']=[''];
    $_SESSION['sexo_peso']=['Todos'];
    $_SESSION['local_peso']=[''];
    $_SESSION['origem_peso']=[''];
    $_SESSION['categoria_peso']=[''];
    $_SESSION['peso_nasc_inicial_peso']=''; 
    $_SESSION['peso_desmama_inicial_peso']=''; 
    $_SESSION['peso_ultimo_inicial_peso']=''; 
    $_SESSION['peso_nasc_final_peso']=''; 
    $_SESSION['peso_desmama_final_peso']=''; 
    $_SESSION['peso_ultimo_final_peso']=''; 
    $_SESSION['data_nasc_inicial_peso']=''; 
    $_SESSION['data_nasc_final_peso']=''; 
    $_SESSION['lista_animais_peso']='';

    $_SESSION['raca_mov']=[''];
    $_SESSION['pai_mov']=[''];
    $_SESSION['mae_mov']=[''];
    $_SESSION['sexo_mov']=['Todos'];
    $_SESSION['local_mov']=[''];
    $_SESSION['origem_mov']=[''];
    $_SESSION['categoria_mov']=[''];
    $_SESSION['peso_nasc_inicial_mov']=''; 
    $_SESSION['peso_desmama_inicial_mov']=''; 
    $_SESSION['peso_ultimo_inicial_mov']=''; 
    $_SESSION['peso_nasc_final_mov']=''; 
    $_SESSION['peso_desmama_final_mov']=''; 
    $_SESSION['peso_ultimo_final_mov']=''; 
    $_SESSION['data_nasc_inicial_mov']=''; 
    $_SESSION['data_nasc_final_mov']=''; 
    $_SESSION['lista_animais_mov']='';

    $_SESSION['local_chuva']='';
    $_SESSION['ano_chuva']='';

    $_SESSION['data_inicial_nascimento']=''; 
    $_SESSION['data_final_nascimento']=''; 
    $_SESSION['local_nascimento']=''; 
    $_SESSION['estacao_nascimento']=''; 
    $_SESSION['ocorrencia_nascimento']=''; 
    $_SESSION['lista_nascimento']=''; 

    $_SESSION['local_matrizes']=''; 
    $_SESSION['estacao_monta_matrizes']=''; 
    $_SESSION['tipo_registro_matrizes']='C'; 
    $_SESSION['codigo_alfa_matrizes']='';
    $_SESSION['codigo_numerico_matrizes']='';
    $_SESSION['lista_matrizes']='';

    $_SESSION['voltar_movimentacao']='';

    $_SESSION['lista_embrioes']='';
    $_SESSION['array_cliente_embrioes']='';

    $_SESSION['tipo_monta'] = 'M';
    $_SESSION['tipo_iatf'] = 'I';
    $_SESSION['tipo_te'] = 'T';
    $_SESSION['cobertura_controle'] = 'I';
    $_SESSION['lista_cobertura'] = '';
    $_SESSION['local_cobertura_diagnostico'] = 0;
    $_SESSION['diagnostico'] = 'P';

    $_SESSION['tipo_mapa_gado'] = 'T'; //M=Mapa do Google, T=Tabuleiro

    $_SESSION['opcao_situacao_reprodutica_rel']='G';

?>
