<?php
class AnimalPastoDao{

    private $con;
    private $systemDateHour;

    public function __construct($banco){
        include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        $this->systemDateHour = date("Y-m-d H:i:s");
    }

    private function fillField($pasto){
        $obj = new AnimalPasto();
        $obj->getPasto()->setId($pasto->tbl_pasto_id);

        $obj->getPasto()->getModulo()->setId($pasto->tbl_modulo_id);
        $obj->getPasto()->getModulo()->setDescricao($pasto->tbl_modulo_descricao);
        $obj->getPasto()->getModulo()->setIncluidoEm($pasto->tbl_modulo_incluido_em);
        $obj->getPasto()->getModulo()->setIncluidoPor($pasto->tbl_modulo_incluido_por);
        $obj->getPasto()->getModulo()->setAlteradoEm($pasto->tbl_modulo_alterado_em);
        $obj->getPasto()->getModulo()->setAlteradoPor($pasto->tbl_modulo_alterado_por);
        $obj->getPasto()->getModulo()->setLixeira($pasto->tbl_modulo_lixeira);
        $obj->getPasto()->getModulo()->setLixeiraEm($pasto->tbl_modulo_lixeira_em);
        $obj->getPasto()->getModulo()->setLixeiraPor($pasto->tbl_modulo_lixeira_por);

        $obj->getPasto()->getLocal()->setId($pasto->tbl_pessoa_id);
        $obj->getPasto()->getLocal()->setClasse($pasto->tbl_pessoa_classe);
        $obj->getPasto()->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
        $obj->getPasto()->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
        $obj->getPasto()->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
        $obj->getPasto()->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
        $obj->getPasto()->getLocal()->setNome($pasto->tbl_pessoa_nome);
        $obj->getPasto()->getLocal()->setContato($pasto->tbl_pessoa_contato);
        $obj->getPasto()->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
        $obj->getPasto()->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
        $obj->getPasto()->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
        $obj->getPasto()->getLocal()->setEmail($pasto->tbl_pessoa_email);
        $obj->getPasto()->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
        $obj->getPasto()->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
        $obj->getPasto()->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
        $obj->getPasto()->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
        $obj->getPasto()->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
        $obj->getPasto()->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
        $obj->getPasto()->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
        $obj->getPasto()->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
        $obj->getPasto()->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
        $obj->getPasto()->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
        $obj->getPasto()->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
        $obj->getPasto()->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
        $obj->getPasto()->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
        $obj->getPasto()->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
        $obj->getPasto()->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
        $obj->getPasto()->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

        $obj->getPasto()->setDescricao($pasto->tbl_pasto_descricao);
        $obj->getPasto()->setLatitude($pasto->tbl_pasto_latitude);
        $obj->getPasto()->setLongitude($pasto->tbl_pasto_longitude);
        $obj->getPasto()->setArea($pasto->tbl_pasto_area);

        $obj->getPasto()->getCapim()->setId($pasto->tbl_tipo_capim_id);
        $obj->getPasto()->getCapim()->setDescricao($pasto->tbl_tipo_capim_descricao);
        $obj->getPasto()->getCapim()->setIncluidoEm($pasto->tbl_tipo_capim_incluido_em);
        $obj->getPasto()->getCapim()->setIncluidoPor($pasto->tbl_tipo_capim_incluido_por);
        $obj->getPasto()->getCapim()->setAlteradoEm($pasto->tbl_tipo_capim_alterado_em);
        $obj->getPasto()->getCapim()->setAlteradoPor($pasto->tbl_tipo_capim_incluido_por);
        $obj->getPasto()->getCapim()->setLixeira($pasto->tbl_tipo_capim_lixeira);
        $obj->getPasto()->getCapim()->setLixeiraEm($pasto->tbl_tipo_capim_lixeira_em);
        $obj->getPasto()->getCapim()->setLixeiraPor($pasto->tbl_tipo_capim_lixeira_por);

        $obj->getPasto()->setArrayCategoria($pasto->tbl_pasto_array_categoria);
        $obj->getPasto()->setObservacao($pasto->tbl_pasto_descricao_lote);
        $obj->getPasto()->setCurral($pasto->tbl_pasto_tipo_curral);
        $obj->getPasto()->setIncluidoEm($pasto->tbl_pasto_incluido_em);
        $obj->getPasto()->setIncluidoPor($pasto->tbl_pasto_incluido_por);
        $obj->getPasto()->setAlteradoEm($pasto->tbl_pasto_alterado_em);
        $obj->getPasto()->setAlteradoPor($pasto->tbl_pasto_alterado_por);
        $obj->getPasto()->setLixeira($pasto->tbl_pasto_lixeira);
        $obj->getPasto()->setLixeiraEm($pasto->tbl_pasto_lixeira_em);
        $obj->getPasto()->setLixeiraPor($pasto->tbl_pasto_lixeira_por);

        $obj->getLocal()->setId($pasto->tbl_pessoa_id);
        $obj->getLocal()->setClasse($pasto->tbl_pessoa_classe);
        $obj->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
        $obj->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
        $obj->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
        $obj->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
        $obj->getLocal()->setNome($pasto->tbl_pessoa_nome);
        $obj->getLocal()->setContato($pasto->tbl_pessoa_contato);
        $obj->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
        $obj->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
        $obj->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
        $obj->getLocal()->setEmail($pasto->tbl_pessoa_email);
        $obj->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
        $obj->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
        $obj->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
        $obj->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
        $obj->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
        $obj->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
        $obj->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
        $obj->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
        $obj->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
        $obj->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
        $obj->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
        $obj->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
        $obj->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
        $obj->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
        $obj->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
        $obj->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

        $obj->setAnimal($pasto->tbl_animal_pasto_numero_item);
        $obj->setNascimento($pasto->tbl_animal_pasto_nascimento);

        $obj->getCategoria()->setId($pasto->tab_codigo_categoria_idade);
        $obj->getCategoria()->setIdadeDe($pasto->tab_categoria_idade_de);
        $obj->getCategoria()->setIdadeAte($pasto->tab_categoria_idade_ate);
        $obj->getCategoria()->setIncluidoEm($pasto->tab_incluido_categoria_idade_em);
        $obj->getCategoria()->setIncluidoPor($pasto->tab_incluido_categoria_idade_por);
        $obj->getCategoria()->setAlteradoEm($pasto->tab_alterado_categoria_idade_em);
        $obj->getCategoria()->setAlteradoPor($pasto->tab_alterado_categoria_idade_por);
        $obj->getCategoria()->setLixeira($pasto->tab_registro_lixeira_categoria_idade);
        $obj->getCategoria()->setLixeiraEm($pasto->tab_lixeira_categoria_idade_em);
        $obj->getCategoria()->setLixeiraPor($pasto->tab_lixeira_categoria_idade_por);

        $obj->setSexo($pasto->tbl_animal_pasto_sexo);

        $obj->getRaca()->setId($pasto->tab_codigo_raca);
        $obj->getRaca()->setDescricao($pasto->tab_descricao_raca);
        $obj->getRaca()->setIncluidoEm($pasto->tab_raca_incluido_em);
        $obj->getRaca()->setIncluidoPor($pasto->tab_raca_incluido_por);
        $obj->getRaca()->setAlteradoEm($pasto->tab_raca_alterado_em);
        $obj->getRaca()->setAlteradoPor($pasto->tab_raca_alterado_por);
        $obj->getRaca()->setLixeira($pasto->tab_registro_lixeira_raca);
        $obj->getRaca()->setLixeiraEm($pasto->tab_raca_lixeira_em);
        $obj->getRaca()->setLixeiraPor($pasto->tab_raca_lixeira_por);

        $obj->setSituacao($pasto->tbl_animal_pasto_situacao);
        $obj->setMotivoMorte($pasto->tbl_animal_pasto_motivo_morte);
        $obj->setObservacao($pasto->tbl_animal_pasto_observacao);
        $obj->setIncluidoEm($pasto->tbl_animal_pasto_incluido_em);
        $obj->setIncluidoPor($pasto->tbl_animal_pasto_incluido_por);
        $obj->setAlteradoEm($pasto->tbl_animal_pasto_alterado_em);
        $obj->setAlteradoPor($pasto->tbl_animal_pasto_alterado_por);
        $obj->setBaixadoEm($pasto->tbl_animal_pasto_baixado_em);
        $obj->setBaixadoPor($pasto->tbl_animal_pasto_baixado_por);

        return $obj;
    }

    private function fillFields($objPasto){
        $aObj = [];

        foreach($objPasto as $pasto){
            $obj = new AnimalPasto();
            $obj->getPasto()->setId($pasto->tbl_pasto_id);

            $obj->getPasto()->getModulo()->setId($pasto->tbl_modulo_id);
            $obj->getPasto()->getModulo()->setDescricao($pasto->tbl_modulo_descricao);
            $obj->getPasto()->getModulo()->setIncluidoEm($pasto->tbl_modulo_incluido_em);
            $obj->getPasto()->getModulo()->setIncluidoPor($pasto->tbl_modulo_incluido_por);
            $obj->getPasto()->getModulo()->setAlteradoEm($pasto->tbl_modulo_alterado_em);
            $obj->getPasto()->getModulo()->setAlteradoPor($pasto->tbl_modulo_alterado_por);
            $obj->getPasto()->getModulo()->setLixeira($pasto->tbl_modulo_lixeira);
            $obj->getPasto()->getModulo()->setLixeiraEm($pasto->tbl_modulo_lixeira_em);
            $obj->getPasto()->getModulo()->setLixeiraPor($pasto->tbl_modulo_lixeira_por);

            $obj->getPasto()->getLocal()->setId($pasto->tbl_pessoa_id);
            $obj->getPasto()->getLocal()->setClasse($pasto->tbl_pessoa_classe);
            $obj->getPasto()->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
            $obj->getPasto()->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
            $obj->getPasto()->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
            $obj->getPasto()->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
            $obj->getPasto()->getLocal()->setNome($pasto->tbl_pessoa_nome);
            $obj->getPasto()->getLocal()->setContato($pasto->tbl_pessoa_contato);
            $obj->getPasto()->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
            $obj->getPasto()->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
            $obj->getPasto()->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
            $obj->getPasto()->getLocal()->setEmail($pasto->tbl_pessoa_email);
            $obj->getPasto()->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
            $obj->getPasto()->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
            $obj->getPasto()->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
            $obj->getPasto()->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
            $obj->getPasto()->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
            $obj->getPasto()->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
            $obj->getPasto()->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
            $obj->getPasto()->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
            $obj->getPasto()->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
            $obj->getPasto()->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
            $obj->getPasto()->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
            $obj->getPasto()->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
            $obj->getPasto()->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
            $obj->getPasto()->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
            $obj->getPasto()->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
            $obj->getPasto()->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

            $obj->getPasto()->setDescricao($pasto->tbl_pasto_descricao);
            $obj->getPasto()->setLatitude($pasto->tbl_pasto_latitude);
            $obj->getPasto()->setLongitude($pasto->tbl_pasto_longitude);
            $obj->getPasto()->setArea($pasto->tbl_pasto_area);

            $obj->getPasto()->getCapim()->setId($pasto->tbl_tipo_capim_id);
            $obj->getPasto()->getCapim()->setDescricao($pasto->tbl_tipo_capim_descricao);
            $obj->getPasto()->getCapim()->setIncluidoEm($pasto->tbl_tipo_capim_incluido_em);
            $obj->getPasto()->getCapim()->setIncluidoPor($pasto->tbl_tipo_capim_incluido_por);
            $obj->getPasto()->getCapim()->setAlteradoEm($pasto->tbl_tipo_capim_alterado_em);
            $obj->getPasto()->getCapim()->setAlteradoPor($pasto->tbl_tipo_capim_incluido_por);
            $obj->getPasto()->getCapim()->setLixeira($pasto->tbl_tipo_capim_lixeira);
            $obj->getPasto()->getCapim()->setLixeiraEm($pasto->tbl_tipo_capim_lixeira_em);
            $obj->getPasto()->getCapim()->setLixeiraPor($pasto->tbl_tipo_capim_lixeira_por);

            $obj->getPasto()->setArrayCategoria($pasto->tbl_pasto_array_categoria);
            $obj->getPasto()->setObservacao($pasto->tbl_pasto_descricao_lote);
            $obj->getPasto()->setCurral($pasto->tbl_pasto_tipo_curral);
            $obj->getPasto()->setIncluidoEm($pasto->tbl_pasto_incluido_em);
            $obj->getPasto()->setIncluidoPor($pasto->tbl_pasto_incluido_por);
            $obj->getPasto()->setAlteradoEm($pasto->tbl_pasto_alterado_em);
            $obj->getPasto()->setAlteradoPor($pasto->tbl_pasto_alterado_por);
            $obj->getPasto()->setLixeira($pasto->tbl_pasto_lixeira);
            $obj->getPasto()->setLixeiraEm($pasto->tbl_pasto_lixeira_em);
            $obj->getPasto()->setLixeiraPor($pasto->tbl_pasto_lixeira_por);

            $obj->getLocal()->setId($pasto->tbl_pessoa_id);
            $obj->getLocal()->setClasse($pasto->tbl_pessoa_classe);
            $obj->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
            $obj->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
            $obj->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
            $obj->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
            $obj->getLocal()->setNome($pasto->tbl_pessoa_nome);
            $obj->getLocal()->setContato($pasto->tbl_pessoa_contato);
            $obj->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
            $obj->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
            $obj->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
            $obj->getLocal()->setEmail($pasto->tbl_pessoa_email);
            $obj->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
            $obj->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
            $obj->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
            $obj->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
            $obj->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
            $obj->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
            $obj->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
            $obj->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
            $obj->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
            $obj->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
            $obj->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
            $obj->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
            $obj->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
            $obj->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
            $obj->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
            $obj->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

            $obj->setAnimal($pasto->tbl_animal_pasto_numero_item);
            $obj->setNascimento($pasto->tbl_animal_pasto_nascimento);

            $obj->getCategoria()->setId($pasto->tab_codigo_categoria_idade);
            $obj->getCategoria()->setIdadeDe($pasto->tab_categoria_idade_de);
            $obj->getCategoria()->setIdadeAte($pasto->tab_categoria_idade_ate);
            $obj->getCategoria()->setIncluidoEm($pasto->tab_incluido_categoria_idade_em);
            $obj->getCategoria()->setIncluidoPor($pasto->tab_incluido_categoria_idade_por);
            $obj->getCategoria()->setAlteradoEm($pasto->tab_alterado_categoria_idade_em);
            $obj->getCategoria()->setAlteradoPor($pasto->tab_alterado_categoria_idade_por);
            $obj->getCategoria()->setLixeira($pasto->tab_registro_lixeira_categoria_idade);
            $obj->getCategoria()->setLixeiraEm($pasto->tab_lixeira_categoria_idade_em);
            $obj->getCategoria()->setLixeiraPor($pasto->tab_lixeira_categoria_idade_por);

            $obj->setSexo($pasto->tbl_animal_pasto_sexo);

            $obj->getRaca()->setId($pasto->tab_codigo_raca);
            $obj->getRaca()->setDescricao($pasto->tab_descricao_raca);
            $obj->getRaca()->setIncluidoEm($pasto->tab_raca_incluido_em);
            $obj->getRaca()->setIncluidoPor($pasto->tab_raca_incluido_por);
            $obj->getRaca()->setAlteradoEm($pasto->tab_raca_alterado_em);
            $obj->getRaca()->setAlteradoPor($pasto->tab_raca_alterado_por);
            $obj->getRaca()->setLixeira($pasto->tab_registro_lixeira_raca);
            $obj->getRaca()->setLixeiraEm($pasto->tab_raca_lixeira_em);
            $obj->getRaca()->setLixeiraPor($pasto->tab_raca_lixeira_por);

            $obj->setSituacao($pasto->tbl_animal_pasto_situacao);
            $obj->setMotivoMorte($pasto->tbl_animal_pasto_motivo_morte);
            $obj->setObservacao($pasto->tbl_animal_pasto_observacao);
            $obj->setIncluidoEm($pasto->tbl_animal_pasto_incluido_em);
            $obj->setIncluidoPor($pasto->tbl_animal_pasto_incluido_por);
            $obj->setAlteradoEm($pasto->tbl_animal_pasto_alterado_em);
            $obj->setAlteradoPor($pasto->tbl_animal_pasto_alterado_por);
            $obj->setBaixadoEm($pasto->tbl_animal_pasto_baixado_em);
            $obj->setBaixadoPor($pasto->tbl_animal_pasto_baixado_por);

            array_push($aObj, $obj);
        }

        return $aObj;
    }

    public function getAnimalPasto($local){
        $a = [];

        $sql = "SELECT * FROM tbl_pasto 
                LEFT JOIN tbl_animal_pasto ON tbl_animal_pasto_id = tbl_pasto_id
                LEFT JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
                JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
                LEFT JOIN tabela_racas ON tbl_animal_pasto_raca = tab_codigo_raca
                LEFT JOIN tabela_categoria_idade ON tbl_animal_pasto_categoria = tab_codigo_categoria_idade
                JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
                WHERE tbl_pasto_codigo_local = '$local' AND tbl_pasto_lixeira = 0
                ORDER BY `tbl_pasto_tipo_curral` DESC, tbl_pasto_modulo ASC, tbl_pasto_id ASC";
        
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        
        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }
        
        return $this->fillFields($a);
    }

    public function getAnimalPastoByPasto($pasto){
        $a = [];

        $sql = "SELECT * FROM tbl_pasto 
        LEFT JOIN tbl_animal_pasto ON tbl_animal_pasto_id = tbl_pasto_id
        LEFT JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
        JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
        LEFT JOIN tabela_racas ON tbl_animal_pasto_raca = tab_codigo_raca
        LEFT JOIN tabela_categoria_idade ON tbl_animal_pasto_categoria = tab_codigo_categoria_idade
        JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
        WHERE tbl_animal_pasto_id = {$pasto} AND tbl_animal_pasto_situacao = 'A'";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }

        return $this->fillFields($a);
    }

    public function getAnimalPastoByPastoAndLocal($pasto, $local){
        $a = [];

        $sql = "SELECT * FROM tbl_pasto 
        LEFT JOIN tbl_animal_pasto ON tbl_animal_pasto_id = tbl_pasto_id
        LEFT JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
        JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
        LEFT JOIN tabela_racas ON tbl_animal_pasto_raca = tab_codigo_raca
        LEFT JOIN tabela_categoria_idade ON tbl_animal_pasto_categoria = tab_codigo_categoria_idade
        JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
        WHERE tbl_animal_pasto_id = {$pasto} AND tbl_animal_pasto_local = {$local} tbl_animal_pasto_situacao = 'A'";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }

        return $this->fillFields($a);
    }

    public function deleteAnimalPastoByPastoAndLocalAndNumeroItem($pasto, $local, $numeroItem){
        $sql = "DELETE FROM tbl_animal_pasto WHERE tbl_animal_pasto_id = $pasto AND tbl_animal_pasto_local = $local AND tbl_animal_pasto_numero_item = $numeroItem";
        mysqli_query($this->con, $sql);
        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro ao excluir o registro. {mysqli_error($this->con)}"
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
    }

    public function transferAll($pastoIncluir, $pastoRemover, $user, $fazenda){
        $sql = "UPDATE tbl_animal_pasto SET
            tbl_animal_pasto_id = $pastoIncluir,
            tbl_animal_pasto_alterado_em = '{$this->systemDateHour}',
            tbl_animal_pasto_alterado_por = '{$user->getNome()}'
            WHERE tbl_animal_pasto_id = $pastoRemover AND tbl_animal_pasto_situacao = 'A' AND tbl_animal_pasto_local = $fazenda";

        mysqli_set_charset($this->con, "utf8");
        mysqli_query($this->con, $sql);

        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro ao transferir os registros. {mysqli_error($this->con)}"
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
    }
}