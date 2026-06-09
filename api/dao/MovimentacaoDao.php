<?php
class MovimentacaoDao{

    public function __construct($db){
        $this->con = mysqli_connect('localhost', 'root', 'a2ngei9Mxh', $db);
        $this->systemDate = date('Y-m-d H:i:s');
    }

    private function fillField($mov){
        $obj = new Movimentacao();

        $obj->setId($mov->tbl_movimentacao_id);
        $obj->setControle($mov->tbl_movimentacao_controle);
        $obj->setData($mov->tbl_movimentacao_data);

        $obj->getOrigem()->setId($mov->tbl_pessoa_id);
        $obj->getOrigem()->setClasse($mov->tbl_pessoa_classe);
        $obj->getOrigem()->setCpfCnpj($mov->tbl_pessoa_cpf_cnpj);
        $obj->getOrigem()->setTipo($mov->tbl_pessoa_tipo_pessoa);
        $obj->getOrigem()->setInscEstadual($mov->tbl_pessoa_insc_estadual);
        $obj->getOrigem()->setInscMunicipal($mov->tbl_pessoa_insc_municipal);
        $obj->getOrigem()->setNome($mov->tbl_pessoa_nome);
        $obj->getOrigem()->setContato($mov->tbl_pessoa_contato);
        $obj->getOrigem()->setCargoContato($mov->tbl_pessoa_cargo_contato);
        $obj->getOrigem()->setDdd($mov->tbl_pessoa_dd);
        $obj->getOrigem()->setTelefone($mov->tbl_pessoa_telefone);
        $obj->getOrigem()->setEmail($mov->tbl_pessoa_email);
        $obj->getOrigem()->getEndereco()->setEndereco($mov->tbl_pessoa_endereco);
        $obj->getOrigem()->getEndereco()->setNumero($mov->tbl_pessoa_numero);
        $obj->getOrigem()->getEndereco()->setComplemento($mov->tbl_pessoa_complemento);
        $obj->getOrigem()->getEndereco()->setBairro($mov->tbl_pessoa_bairro);
        $obj->getOrigem()->getEndereco()->setCep($mov->tbl_pessoa_cep);
        $obj->getOrigem()->getEndereco()->setCidade($mov->tbl_pessoa_municipio);
        $obj->getOrigem()->getEndereco()->setEstado($mov->tbl_pessoa_estado);       
        $obj->getOrigem()->setLixeira($mov->tbl_pessoa_lixeira);
        $obj->getOrigem()->setIncluidoEm($mov->tbl_pessoa_incluido_em);
        $obj->getOrigem()->setIncluidoPor($mov->tbl_pessoa_incluido_por);
        $obj->getOrigem()->setLixeiraEm($mov->tbl_pessoa_lixeira_em);
        $obj->getOrigem()->setLixeiraPor($mov->tbl_pessoa_lixeira_por);
        $obj->getOrigem()->setAlteradoEm($mov->tbl_pessoa_alterado_em);
        $obj->getOrigem()->setAlteradoPor($mov->tbl_pessoa_alterado_por);
        $obj->getOrigem()->setAtivo($mov->tbl_pessoa_ativo);

        $obj->setDestino($mov->tbl_pessoa_id);

        $obj->setTipo($mov->tbl_movimentacao_tipo);
        $obj->setQtdAnimalPesado($mov->tbl_movimentacao_qtd_animais_pesados);
        $obj->setPesoKg($mov->tbl_movimentacao_peso_kg);
        $obj->setPesoArroba($mov->tbl_movimentacao_peso_arroba);
        $obj->setPesoMedioKg($mov->tbl_movimentacao_peso_medio_kg);
        $obj->setPesoMedioArroba($mov->tbl_movimentacao_peso_medio_arroba);
        $obj->setFiltro($mov->tbl_movimentacao_filtros);
        $obj->setSituacao($mov->tbl_movimentacao_situacao);

        $obj->getVenda()->setId($mov->tbl_venda_id);
        $obj->getVenda()->setCategoria($mov->tbl_venda_categoria);
        
        $obj->getVenda()->getOrigem()->setId($mov->tbl_pessoa_id);
        $obj->getVenda()->getOrigem()->setClasse($mov->tbl_pessoa_classe);
        $obj->getVenda()->getOrigem()->setCpfCnpj($mov->tbl_pessoa_cpf_cnpj);
        $obj->getVenda()->getOrigem()->setTipo($mov->tbl_pessoa_tipo_pessoa);
        $obj->getVenda()->getOrigem()->setInscEstadual($mov->tbl_pessoa_insc_estadual);
        $obj->getVenda()->getOrigem()->setInscMunicipal($mov->tbl_pessoa_insc_municipal);
        $obj->getVenda()->getOrigem()->setNome($mov->tbl_pessoa_nome);
        $obj->getVenda()->getOrigem()->setContato($mov->tbl_pessoa_contato);
        $obj->getVenda()->getOrigem()->setCargoContato($mov->tbl_pessoa_cargo_contato);
        $obj->getVenda()->getOrigem()->setDdd($mov->tbl_pessoa_dd);
        $obj->getVenda()->getOrigem()->setTelefone($mov->tbl_pessoa_telefone);
        $obj->getVenda()->getOrigem()->setEmail($mov->tbl_pessoa_email);
        $obj->getVenda()->getOrigem()->getEndereco()->setEndereco($mov->tbl_pessoa_endereco);
        $obj->getVenda()->getOrigem()->getEndereco()->setNumero($mov->tbl_pessoa_numero);
        $obj->getVenda()->getOrigem()->getEndereco()->setComplemento($mov->tbl_pessoa_complemento);
        $obj->getVenda()->getOrigem()->getEndereco()->setBairro($mov->tbl_pessoa_bairro);
        $obj->getVenda()->getOrigem()->getEndereco()->setCep($mov->tbl_pessoa_cep);
        $obj->getVenda()->getOrigem()->getEndereco()->setCidade($mov->tbl_pessoa_municipio);
        $obj->getVenda()->getOrigem()->getEndereco()->setEstado($mov->tbl_pessoa_estado);       
        $obj->getVenda()->getOrigem()->setLixeira($mov->tbl_pessoa_lixeira);
        $obj->getVenda()->getOrigem()->setIncluidoEm($mov->tbl_pessoa_incluido_em);
        $obj->getVenda()->getOrigem()->setIncluidoPor($mov->tbl_pessoa_incluido_por);
        $obj->getVenda()->getOrigem()->setLixeiraEm($mov->tbl_pessoa_lixeira_em);
        $obj->getVenda()->getOrigem()->setLixeiraPor($mov->tbl_pessoa_lixeira_por);
        $obj->getVenda()->getOrigem()->setAlteradoEm($mov->tbl_pessoa_alterado_em);
        $obj->getVenda()->getOrigem()->setAlteradoPor($mov->tbl_pessoa_alterado_por);
        $obj->getVenda()->getOrigem()->setAtivo($mov->tbl_pessoa_ativo);
        
        $obj->getVenda()->setDestino($mov->tbl_pessoa_id);
        $obj->getVenda()->setSituacao($mov->tbl_venda_situacao);
        $obj->getVenda()->setEmissao($mov->tbl_venda_emissao);
        $obj->getVenda()->setTipo($mov->tbl_venda_tipo);
        $obj->getVenda()->setTotalVenda($mov->tbl_venda_total_venda);
        $obj->getVenda()->setTotalDesconto($mov->tbl_venda_total_desconto);
        $obj->getVenda()->setTotalReceber($mov->tbl_venda_total_receber);
        $obj->getVenda()->setValorPrimeiraParcela($mov->tbl_venda_valor_primeira_parcela);
        $obj->getVenda()->setVencimentoPrimeiraParcela($mov->tbl_venda_vencimento_primeira_parcela);
        
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setId($mov->tbl_forma_pagamento_id);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setDescricao($mov->tbl_forma_pagamento_descricao);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setIncluidoEm($mov->tbl_forma_pagamento_incluido_em);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setIncluidoPor($mov->tbl_forma_pagamento_incluido_por);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setAlteradoEm($mov->tbl_forma_pagamento_alterado_em);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setAlteradoPor($mov->tbl_forma_pagamento_alterado_por);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setLixeira($mov->tbl_forma_pagamento_lixeira);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setLixeiraEm($mov->tbl_forma_pagamento_lixeira_em);
        $obj->getVenda()->getFormaPgtoPrimeiraParcela()->setLixeiraPor($mov->tbl_forma_pagamento_lixeira_por);
        
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setId($mov->tbl_conta_pagamento_id);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setDescricao($mov->tbl_conta_pagamento_descricao);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setTipo($mov->tbl_conta_pagamento_tipo);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setId($mov->tbl_banco_id);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setCodigo($mov->tbl_banco_codigo);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setNome($mov->tbl_banco_nome);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setIncluidoEm($mov->tbl_banco_incluido_em);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setIncluidoPor($mov->tbl_banco_incluido_por);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setAlteradoEm($mov->tbl_banco_alterado_em);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setAlteradoPor($mov->tbl_banco_alterado_por);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setLixeira($mov->tbl_banco_lixeira);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setLixeiraEm($mov->tbl_banco_lixeira_em);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->getBanco()->setLixeiraPor($mov->tbl_banco_lixeira_por);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setAgencia($mov->tbl_conta_pagamento_agencia);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setConta($mov->tbl_conta_pagamento_conta);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setCartao($mov->tbl_conta_pagamento_numero_cartao);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setSaldoInicial($mov->tbl_conta_pagamento_saldo_inicial);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setDataSaldo($mov->tbl_conta_pagamento_data_saldo);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setIncluidoEm($mov->tbl_conta_pagamento_incluido_em);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setIncluidoPor($mov->tbl_conta_pagamento_incluido_por);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setAlteradoEm($mov->tbl_conta_pagamento_alterado_em);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setAlteradoPor($mov->tbl_conta_pagamento_alterado_por);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setLixeira($mov->tbl_conta_pagamento_lixeira);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setLixeiraEm($mov->tbl_conta_pagamento_lixeira_em);
        $obj->getVenda()->getContaPgtoPrimeiraParcela()->setLixeiraPor($mov->tbl_conta_pagamento_lixeira_por);
        
        $obj->getVenda()->setGta($mov->tbl_venda_gta);
        $obj->getVenda()->setTransportadora($mov->tbl_venda_nome_transportadora);
        $obj->getVenda()->setMotorista($mov->tbl_venda_dados_motorista);
        $obj->getVenda()->setContaContabil($mov->tbl_venda_conta_contabil);
        $obj->getVenda()->getCentroCusto();
        $obj->getVenda()->setArrayItem($mov->tbl_venda_array_itens);
        $obj->getVenda()->setArrayParcela($mov->tbl_venda_array_parcelas);
        $obj->getVenda()->setIncluidoEm($mov->tbl_venda_incluido_em);
        $obj->getVenda()->setIncluidoPor($mov->tbl_venda_incluido_por);
        $obj->getVenda()->setAlteradoEm($mov->tbl_venda_alterado_em);
        $obj->getVenda()->setAlteradoPor($mov->tbl_venda_alterado_por);
        $obj->getVenda()->setLixeira($mov->tbl_venda_lixeira);
        $obj->getVenda()->setLixeiraEm($mov->tbl_venda_lixeira_em);
        $obj->getVenda()->setLixeiraPor($mov->tbl_venda_lixeira_por);

        $obj->getPesagem()->setId($mov->tbl_pesagem_id);
        $obj->getPesagem()->setControle($mov->tbl_pesagem_controle);
        $obj->getPesagem()->setData($mov->tbl_pesagem_data);

        $obj->getPesagem()->getLocal()->setId($mov->tbl_pessoa_id);
        $obj->getPesagem()->getLocal()->setClasse($mov->tbl_pessoa_classe);
        $obj->getPesagem()->getLocal()->setCpfCnpj($mov->tbl_pessoa_cpf_cnpj);
        $obj->getPesagem()->getLocal()->setTipo($mov->tbl_pessoa_tipo_pessoa);
        $obj->getPesagem()->getLocal()->setInscEstadual($mov->tbl_pessoa_insc_estadual);
        $obj->getPesagem()->getLocal()->setInscMunicipal($mov->tbl_pessoa_insc_municipal);
        $obj->getPesagem()->getLocal()->setNome($mov->tbl_pessoa_nome);
        $obj->getPesagem()->getLocal()->setContato($mov->tbl_pessoa_contato);
        $obj->getPesagem()->getLocal()->setCargoContato($mov->tbl_pessoa_cargo_contato);
        $obj->getPesagem()->getLocal()->setDdd($mov->tbl_pessoa_dd);
        $obj->getPesagem()->getLocal()->setTelefone($mov->tbl_pessoa_telefone);
        $obj->getPesagem()->getLocal()->setEmail($mov->tbl_pessoa_email);
        $obj->getPesagem()->getLocal()->getEndereco()->setEndereco($mov->tbl_pessoa_endereco);
        $obj->getPesagem()->getLocal()->getEndereco()->setNumero($mov->tbl_pessoa_numero);
        $obj->getPesagem()->getLocal()->getEndereco()->setComplemento($mov->tbl_pessoa_complemento);
        $obj->getPesagem()->getLocal()->getEndereco()->setBairro($mov->tbl_pessoa_bairro);
        $obj->getPesagem()->getLocal()->getEndereco()->setCep($mov->tbl_pessoa_cep);
        $obj->getPesagem()->getLocal()->getEndereco()->setCidade($mov->tbl_pessoa_municipio);
        $obj->getPesagem()->getLocal()->getEndereco()->setEstado($mov->tbl_pessoa_estado);       
        $obj->getPesagem()->getLocal()->setLixeira($mov->tbl_pessoa_lixeira);
        $obj->getPesagem()->getLocal()->setIncluidoEm($mov->tbl_pessoa_incluido_em);
        $obj->getPesagem()->getLocal()->setIncluidoPor($mov->tbl_pessoa_incluido_por);
        $obj->getPesagem()->getLocal()->setLixeiraEm($mov->tbl_pessoa_lixeira_em);
        $obj->getPesagem()->getLocal()->setLixeiraPor($mov->tbl_pessoa_lixeira_por);
        $obj->getPesagem()->getLocal()->setAlteradoEm($mov->tbl_pessoa_alterado_em);
        $obj->getPesagem()->getLocal()->setAlteradoPor($mov->tbl_pessoa_alterado_por);
        $obj->getPesagem()->getLocal()->setAtivo($mov->tbl_pessoa_ativo);

        $obj->getPesagem()->getEpoca()->setId($mov->tab_codigo_epoca_pesagem);
        $obj->getPesagem()->getEpoca()->setDescricao($mov->tab_descricao_epoca_pesagem);
        $obj->getPesagem()->getEpoca()->setIncluidoEm($mov->tab_epoca_pesagem_incluido_em);
        $obj->getPesagem()->getEpoca()->setIncluidoPor($mov->tab_epoca_pesagem_incluido_por);
        $obj->getPesagem()->getEpoca()->setAlteradoEm($mov->tab_epoca_pesagem_alterado_em);
        $obj->getPesagem()->getEpoca()->setAlteradoPor($mov->tab_epoca_pesagem_alterado_por);
        $obj->getPesagem()->getEpoca()->setLixeira($mov->tab_registro_lixeira_epoca_pesagem);
        $obj->getPesagem()->getEpoca()->setLixeiraEm($mov->tab_epoca_pesagem_lixeira_em);
        $obj->getPesagem()->getEpoca()->setLixeiraPor($mov->tab_epoca_pesagem_lixeira_por);

        $obj->getPesagem()->setLote($mov->tbl_pesagem_lote);
        $obj->getPesagem()->setQuantidadeAnimais($mov->tbl_pesagem_qtd_animais_pesados);
        $obj->getPesagem()->setPesoKg($mov->tbl_pesagem_peso_kg);
        $obj->getPesagem()->setPesoArroba($mov->tbl_pesagem_peso_arroba);
        $obj->getPesagem()->setPesoMedioKg($mov->tbl_pesagem_peso_medio_kg);
        $obj->getPesagem()->setPesoMedioArroba($mov->tbl_pesagem_peso_medio_arroba);
        $obj->getPesagem()->setFiltro($mov->tbl_pesagem_filtros);
        $obj->getPesagem()->setFinalizada($mov->tbl_pesagem_finalizada);

        $obj->getPesagem()->getPasto()->getModulo()->setId($mov->tbl_modulo_id);
        $obj->getPesagem()->getPasto()->getModulo()->setDescricao($mov->tbl_modulo_descricao);
        $obj->getPesagem()->getPasto()->getModulo()->setIncluidoEm($mov->tbl_modulo_incluido_em);
        $obj->getPesagem()->getPasto()->getModulo()->setIncluidoPor($mov->tbl_modulo_incluido_por);
        $obj->getPesagem()->getPasto()->getModulo()->setAlteradoEm($mov->tbl_modulo_alterado_em);
        $obj->getPesagem()->getPasto()->getModulo()->setAlteradoPor($mov->tbl_modulo_alterado_por);
        $obj->getPesagem()->getPasto()->getModulo()->setLixeira($mov->tbl_modulo_lixeira);
        $obj->getPesagem()->getPasto()->getModulo()->setLixeiraEm($mov->tbl_modulo_lixeira_em);
        $obj->getPesagem()->getPasto()->getModulo()->setLixeiraPor($mov->tbl_modulo_lixeira_por);
        $obj->getPesagem()->getPasto()->getLocal()->setId($mov->tbl_pessoa_id);
        $obj->getPesagem()->getPasto()->getLocal()->setClasse($mov->tbl_pessoa_classe);
        $obj->getPesagem()->getPasto()->getLocal()->setCpfCnpj($mov->tbl_pessoa_cpf_cnpj);
        $obj->getPesagem()->getPasto()->getLocal()->setTipo($mov->tbl_pessoa_tipo_pessoa);
        $obj->getPesagem()->getPasto()->getLocal()->setInscEstadual($mov->tbl_pessoa_insc_estadual);
        $obj->getPesagem()->getPasto()->getLocal()->setInscMunicipal($mov->tbl_pessoa_insc_municipal);
        $obj->getPesagem()->getPasto()->getLocal()->setNome($mov->tbl_pessoa_nome);
        $obj->getPesagem()->getPasto()->getLocal()->setContato($mov->tbl_pessoa_contato);
        $obj->getPesagem()->getPasto()->getLocal()->setCargoContato($mov->tbl_pessoa_cargo_contato);
        $obj->getPesagem()->getPasto()->getLocal()->setDdd($mov->tbl_pessoa_ddd);
        $obj->getPesagem()->getPasto()->getLocal()->setTelefone($mov->tbl_pessoa_telefone);
        $obj->getPesagem()->getPasto()->getLocal()->setEmail($mov->tbl_pessoa_email);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setCep($mov->tbl_pessoa_cep);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setEndereco($mov->tbl_pessoa_endereco);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setNumero($mov->tbl_pessoa_numero);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setComplemento($mov->tbl_pessoa_complemento);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setBairro($mov->tbl_pessoa_bairro);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setCidade($mov->tbl_pessoa_municipio);
        $obj->getPesagem()->getPasto()->getLocal()->getEndereco()->setEstado($mov->tbl_pessoa_estado);
        $obj->getPesagem()->getPasto()->getLocal()->setIncluidoEm($mov->tbl_pessoa_incluido_em);
        $obj->getPesagem()->getPasto()->getLocal()->setIncluidoPor($mov->tbl_pessoa_incluido_por);
        $obj->getPesagem()->getPasto()->getLocal()->setAlteradoEm($mov->tbl_pessoa_alterado_em);
        $obj->getPesagem()->getPasto()->getLocal()->setAlteradoPor($mov->tbl_pessoa_alterado_por);
        $obj->getPesagem()->getPasto()->getLocal()->setLixeira($mov->tbl_pessoa_lixeira);
        $obj->getPesagem()->getPasto()->getLocal()->setLixeiraEm($mov->tbl_pessoa_lixeira_em);
        $obj->getPesagem()->getPasto()->getLocal()->setLixeiraPor($mov->tbl_pessoa_lixeira_por);
        $obj->getPesagem()->getPasto()->getLocal()->setObservacao($mov->tbl_pessoa_observacao);
        $obj->getPesagem()->getPasto()->getLocal()->setAtivo($mov->tbl_pessoa_ativo);
        $obj->getPesagem()->getPasto()->setDescricao($mov->tbl_pasto_descricao);
        $obj->getPesagem()->getPasto()->setLatitude($mov->tbl_pasto_latitude);
        $obj->getPesagem()->getPasto()->setLongitude($mov->tbl_pasto_longitude);
        $obj->getPesagem()->getPasto()->setArea($mov->tbl_pasto_area);
        $obj->getPesagem()->getPasto()->getCapim()->setId($mov->tbl_tipo_capim_id);
        $obj->getPesagem()->getPasto()->getCapim()->setDescricao($mov->tbl_tipo_capim_descricao);
        $obj->getPesagem()->getPasto()->getCapim()->setIncluidoEm($mov->tbl_tipo_capim_incluido_em);
        $obj->getPesagem()->getPasto()->getCapim()->setIncluidoPor($mov->tbl_tipo_capim_incluido_por);
        $obj->getPesagem()->getPasto()->getCapim()->setAlteradoEm($mov->tbl_tipo_capim_alterado_em);
        $obj->getPesagem()->getPasto()->getCapim()->setAlteradoPor($mov->tbl_tipo_capim_incluido_por);
        $obj->getPesagem()->getPasto()->getCapim()->setLixeira($mov->tbl_tipo_capim_lixeira);
        $obj->getPesagem()->getPasto()->getCapim()->setLixeiraEm($mov->tbl_tipo_capim_lixeira_em);
        $obj->getPesagem()->getPasto()->getCapim()->setLixeiraPor($mov->tbl_tipo_capim_lixeira_por);
        $obj->getPesagem()->getPasto()->setArrayCategoria($mov->tbl_pasto_array_categoria);
        $obj->getPesagem()->getPasto()->setObservacao($mov->tbl_pasto_descricao_lote);
        $obj->getPesagem()->getPasto()->setCurral($mov->tbl_pasto_tipo_curral);
        $obj->getPesagem()->getPasto()->setIncluidoEm($mov->tbl_pasto_incluido_em);
        $obj->getPesagem()->getPasto()->setIncluidoPor($mov->tbl_pasto_incluido_por);
        $obj->getPesagem()->getPasto()->setAlteradoEm($mov->tbl_pasto_alterado_em);
        $obj->getPesagem()->getPasto()->setAlteradoPor($mov->tbl_pasto_alterado_por);
        $obj->getPesagem()->getPasto()->setLixeira($mov->tbl_pasto_lixeira);
        $obj->getPesagem()->getPasto()->setLixeiraEm($mov->tbl_pasto_lixeira_em);
        $obj->getPesagem()->getPasto()->setLixeiraPor($mov->tbl_pasto_lixeira_por);

        $obj->getPesagem()->getCategoria()->setId($mov->tab_codigo_categoria_idade);
        $obj->getPesagem()->getCategoria()->setIdadeDe($mov->tab_categoria_idade_de);
        $obj->getPesagem()->getCategoria()->setIdadeAte($mov->tab_categoria_idade_ate);
        $obj->getPesagem()->getCategoria()->setIncluidoEm($mov->tab_incluido_categoria_idade_em);
        $obj->getPesagem()->getCategoria()->setIncluidoPor($mov->tab_incluido_categoria_idade_por);
        $obj->getPesagem()->getCategoria()->setAlteradoEm($mov->tab_alterado_categoria_idade_em);
        $obj->getPesagem()->getCategoria()->setAlteradoPor($mov->tab_alterado_categoria_idade_por);
        $obj->getPesagem()->getCategoria()->setLixeira($mov->tab_registro_lixeira_categoria_idade);
        $obj->getPesagem()->getCategoria()->setLixeiraEm($mov->tab_lixeira_categoria_idade_em);
        $obj->getPesagem()->getCategoria()->setLixeiraPor($mov->tab_lixeira_categoria_idade_por);

        $obj->getPesagem()->setSexo($mov->tbl_pesagem_sexo);
        $obj->getPesagem()->setIncluidoEm($mov->tbl_pesagem_incluido_em);
        $obj->getPesagem()->setIncluidoPor($mov->tbl_pesagem_incluido_por);
        $obj->getPesagem()->setAlteradoEm($mov->tbl_pesagem_alterado_em);
        $obj->getPesagem()->setAlteradoPor($mov->tbl_pesagem_alterado_por);
        $obj->getPesagem()->setLixeira($mov->tbl_pesagem_lixeira);
        $obj->getPesagem()->setLixeiraEm($mov->tbl_pesagem_lixeira_em);
        $obj->getPesagem()->setLixeiraPor($mov->tbl_pesagem_lixeira_por);

        $obj->setIncluidoEm($mov->tbl_movimentacao_incluido_em);
        $obj->setIncluidoPor($mov->tbl_movimentacao_incluido_por);
        $obj->setAlteradoEm($mov->tbl_movimentacao_alterado_em);
        $obj->setAlteradoPor($mov->tbl_movimentacao_alterado_por);
        $obj->setLixeira($mov->tbl_movimentacao_lixeira);
        $obj->setLixeiraEm($mov->tbl_movimentacao_lixeira_em);
        $obj->setLixeiraPor($mov->tbl_movimentacao_lixeira_por);
        $obj->setAceiteTransferenciaEm($mov->tbl_movimentacao_aceite_transferencia_em);
        $obj->setAceiteTransferenciaPor($mov->tbl_movimentacao_aceite_transferencia_por);
        $obj->setAceiteFinanceiroEm($mov->tbl_movimentacao_aceite_financeiro_em);
        $obj->setAceiteFinanceiroPor($mov->tbl_movimentacao_aceite_financeiro_por);

        return $obj;
    }

    private function fillFields($aMov){
        $a = [];

        foreach($aMov as $mov){
            array_push($a, $this->fillField($mov));
        }

        return $a;
    }

    public function gravarMovimentacaoMortePasto($controleEstoque, $data, $local, $user){
        $sql = "INSERT INTO tbl_movimentacao VALUES (null, '{$controleEstoque}', '{$data}', {$local}, null, 888, 1, 0.00, 0.00, 0.00, 0.00, null, 'N', '{$this->systemDate}', '{$user->getNome()}', null, null, 0, null, null, null, null, null, null, null, null)";
        mysqli_query($this->con, $sql);
        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Não foi possível registrar a movimentação."
            ];
        }

        $lastId = mysqli_insert_id($this->con);

        return[
            "error" => false,
            "message" => "",
            "movId" => "{$lastId}"
        ];
    }
}