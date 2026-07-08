<?php
class PessoaDao{
    
    private $con;

    public function __construct($banco){
        include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    public function fillField($pessoa){
        $obj = new Pessoa();

        $obj->setId($pessoa->tbl_pessoa_id);
        $obj->setClasse($pessoa->tbl_pessoa_classe);
        $obj->setCpfCnpj($pessoa->tbl_pessoa_cpf_cnpj);
        $obj->setTipo($pessoa->tbl_pessoa_tipo_pessoa);
        $obj->setInscEstadual($pessoa->tbl_pessoa_insc_estadual);
        $obj->setInscMunicipal($pessoa->tbl_pessoa_insc_municipal);
        $obj->setNome($pessoa->tbl_pessoa_nome);
        $obj->setContato($pessoa->tbl_pessoa_contato);
        $obj->setDdd($pessoa->tbl_pessoa_ddd);
        $obj->setTelefone($pessoa->tbl_pessoa_telefone);
        $obj->setEmail($pessoa->tbl_pessoa_email);

        $obj->getEndereco()->setCep($pessoa->tbl_pessoa_cep);
        $obj->getEndereco()->setEndereco($pessoa->tbl_pessoa_endereco);
        $obj->getEndereco()->setNumero($pessoa->tbl_pessoa_numero);
        $obj->getEndereco()->setComplemento($pessoa->tbl_pessoa_complemento);
        $obj->getEndereco()->setBairro($pessoa->tbl_pessoa_bairro);
        $obj->getEndereco()->setCidade($pessoa->tbl_pessoa_municipio);
        $obj->getEndereco()->setEstado($pessoa->tbl_pessoa_estado);

        $obj->setIncluidoEm($pessoa->tbl_pessoa_incluido_em);
        $obj->setIncluidoPor($pessoa->tbl_pessoa_incluido_por);
        $obj->setAlteradoEm($pessoa->tbl_pessoa_alterado_em);
        $obj->setAlteradoPor($pessoa->tbl_pessoa_alterado_por);
        $obj->setLixeira($pessoa->tbl_pessoa_lixeira);
        $obj->setLixeiraEm($pessoa->tbl_pessoa_lixeira_em);
        $obj->setLixeiraPor($pessoa->tbl_pessoa_lixeira_por);
        $obj->setObservacao($pessoa->tbl_pessoa_observacao);
        $obj->setAtivo($pessoa->tbl_pessoa_ativo);

        return $obj;
    }

    public function fillFields($objPessoa){
        $a = [];

        foreach($objPessoa as $pessoa){
            $obj = new Pessoa();

            $obj->setId($pessoa->tbl_pessoa_id);
            $obj->setClasse($pessoa->tbl_pessoa_classe);
            $obj->setCpfCnpj($pessoa->tbl_pessoa_cpf_cnpj);
            $obj->setTipo($pessoa->tbl_pessoa_tipo_pessoa);
            $obj->setInscEstadual($pessoa->tbl_pessoa_insc_estadual);
            $obj->setInscMunicipal($pessoa->tbl_pessoa_insc_municipal);
            $obj->setNome($pessoa->tbl_pessoa_nome);
            $obj->setContato($pessoa->tbl_pessoa_contato);
            $obj->setDdd($pessoa->tbl_pessoa_ddd);
            $obj->setTelefone($pessoa->tbl_pessoa_telefone);
            $obj->setEmail($pessoa->tbl_pessoa_email);

            $obj->getEndereco()->setCep($pessoa->tbl_pessoa_cep);
            $obj->getEndereco()->setEndereco($pessoa->tbl_pessoa_endereco);
            $obj->getEndereco()->setNumero($pessoa->tbl_pessoa_numero);
            $obj->getEndereco()->setComplemento($pessoa->tbl_pessoa_complemento);
            $obj->getEndereco()->setBairro($pessoa->tbl_pessoa_bairro);
            $obj->getEndereco()->setCidade($pessoa->tbl_pessoa_municipio);
            $obj->getEndereco()->setEstado($pessoa->tbl_pessoa_estado);

            $obj->setIncluidoEm($pessoa->tbl_pessoa_incluido_em);
            $obj->setIncluidoPor($pessoa->tbl_pessoa_incluido_por);
            $obj->setAlteradoEm($pessoa->tbl_pessoa_alterado_em);
            $obj->setAlteradoPor($pessoa->tbl_pessoa_alterado_por);
            $obj->setLixeira($pessoa->tbl_pessoa_lixeira);
            $obj->setLixeiraEm($pessoa->tbl_pessoa_lixeira_em);
            $obj->setLixeiraPor($pessoa->tbl_pessoa_lixeira_por);
            $obj->setObservacao($pessoa->tbl_pessoa_observacao);
            $obj->setAtivo($pessoa->tbl_pessoa_ativo);

            array_push($a, $obj);
        }

        return $a;
    }

    public function getPessoaById($pessoa){
        $sql = "SELECT * FROM tbl_pessoa WHERE tbl_pessoa_id = $pessoa";
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        return $this->fillField(mysqli_fetch_object($r));
    }
}