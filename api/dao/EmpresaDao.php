<?php
class EmpresaDao{

    private $con;

    public function __construct($banco){
        require __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    private function fillField($empresa){
        $obj = new Empresa();

        $obj->setId($empresa->tbl_empresa_id);
        $obj->setCpfCnpj($empresa->tbl_empresa_cpf_cnpj);
        $obj->setTipoPessoa($empresa->tbl_empresa_tipo_pessoa);
        $obj->setInscEstadual($empresa->tbl_empresa_insc_estadual);
        $obj->setInscMunicipal($empresa->tbl_empresa_insc_municipal);
        $obj->setNome($empresa->tbl_empresa_nome);
        $obj->setNomeFantasia($empresa->tbl_empresa_nome_fantasia);
        $obj->setContato($empresa->tbl_empresa_contato);
        $obj->setDdd($empresa->tbl_empresa_ddd);
        $obj->setTelefone($empresa->tbl_empresa_telefone);
        $obj->setEmail($empresa->tbl_empresa_email);

        $obj->getEndereco()->setCep($empresa->tbl_empresa_cep);
        $obj->getEndereco()->setEndereco($empresa->tbl_empresa_endereco);
        $obj->getEndereco()->setNumero($empresa->tbl_empresa_numero);
        $obj->getEndereco()->setComplemento($empresa->tbl_empresa_complemento);
        $obj->getEndereco()->setBairro($empresa->tbl_empresa_bairro);
        $obj->getEndereco()->setCidade($empresa->tbl_empresa_municipio);
        $obj->getEndereco()->setEstado($empresa->tbl_empresa_estado);

        $obj->setIncluidoEm($empresa->tbl_empresa_incluido_em);
        $obj->setIncluidoPor($empresa->tbl_empresa_incluido_por);
        $obj->setAlteradoEm($empresa->tbl_empresa_alterado_em);
        $obj->setAlteradoPor($empresa->tbl_empresa_alterado_por);
        $obj->setLixeira($empresa->tbl_empresa_lixeira);
        $obj->setLixeiraEm($empresa->tbl_empresa_lixeira_em);
        $obj->setLixeiraPor($empresa->tbl_empresa_lixeira_por);
        $obj->setObservacao($empresa->tbl_empresa_observacao);
        $obj->setHostSmtp($empresa->tbl_empresa_host_smtp);
        $obj->setHostPorta($empresa->tbl_empresa_host_porta);
        $obj->setUsuarioEmail($empresa->tbl_empresa_usuario_email);
        $obj->setSenhaEmail($empresa->tbl_empresa_senha_email);
        $obj->setControlePesagem($empresa->tbl_empresa_controle_pesagem);
        $obj->setTermoUsoConfirmadoEm($empresa->tbl_empresa_termo_uso_confirmado_em);
        $obj->setTermoUsoConfirmadoPor($empresa->tbl_empresa_termo_uso_confirmado_por);

        return $obj;
    }

    private function fillFields($array){

        $aObj = [];

        foreach($array as $empresa){
            array_push($aObj, $this->fillField($empresa));
        }

        return $aObj;
    }

    public function getEmpresaByCnpj($cnpj){
        $sql = "SELECT * FROM tbl_empresa WHERE tbl_empresa_cpf_cnpj = {$cnpj} AND tbl_empresa_lixeira = 0";
        mysqli_set_charset($this->con, 'utf8');
        
        return $this->fillField(mysqli_fetch_object(mysqli_query($this->con, $sql)));
    }
}