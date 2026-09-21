<?php
class Empresa implements JsonSerializable{

    private $id;
    private $cpfCnpj;
    private $tipoPessoa;
    private $inscEstadual;
    private $inscMunicipal;
    private $nome;
    private $nomeFantasia;
    private $contato;
    private $ddd;
    private $telefone;
    private $email;
    private $endereco;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;
    private $observacao;
    private $hostSmtp;
    private $hostPorta;
    private $usuarioEmail;
    private $senhaEmail;
    private $controlePesagem;
    private $termoUsoConfirmadoEm;
    private $termoUsoConfirmadoPor;

    public function __construct(){
        $this->endereco = new Endereco();
    }

    public function jsonSerialize(){
        return (object) get_object_vars($this);
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of cpfCnpj
     */ 
    public function getCpfCnpj()
    {
        return $this->cpfCnpj;
    }

    /**
     * Set the value of cpfCnpj
     *
     * @return  self
     */ 
    public function setCpfCnpj($cpfCnpj)
    {
        $this->cpfCnpj = $cpfCnpj;

        return $this;
    }

    /**
     * Get the value of tipoPessoa
     */ 
    public function getTipoPessoa()
    {
        return $this->tipoPessoa;
    }

    /**
     * Set the value of tipoPessoa
     *
     * @return  self
     */ 
    public function setTipoPessoa($tipoPessoa)
    {
        $this->tipoPessoa = $tipoPessoa;

        return $this;
    }

    /**
     * Get the value of inscEstadual
     */ 
    public function getInscEstadual()
    {
        return $this->inscEstadual;
    }

    /**
     * Set the value of inscEstadual
     *
     * @return  self
     */ 
    public function setInscEstadual($inscEstadual)
    {
        $this->inscEstadual = $inscEstadual;

        return $this;
    }

    /**
     * Get the value of inscMunicipal
     */ 
    public function getInscMunicipal()
    {
        return $this->inscMunicipal;
    }

    /**
     * Set the value of inscMunicipal
     *
     * @return  self
     */ 
    public function setInscMunicipal($inscMunicipal)
    {
        $this->inscMunicipal = $inscMunicipal;

        return $this;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of nomeFantasia
     */ 
    public function getNomeFantasia()
    {
        return $this->nomeFantasia;
    }

    /**
     * Set the value of nomeFantasia
     *
     * @return  self
     */ 
    public function setNomeFantasia($nomeFantasia)
    {
        $this->nomeFantasia = $nomeFantasia;

        return $this;
    }

    /**
     * Get the value of contato
     */ 
    public function getContato()
    {
        return $this->contato;
    }

    /**
     * Set the value of contato
     *
     * @return  self
     */ 
    public function setContato($contato)
    {
        $this->contato = $contato;

        return $this;
    }

    /**
     * Get the value of ddd
     */ 
    public function getDdd()
    {
        return $this->ddd;
    }

    /**
     * Set the value of ddd
     *
     * @return  self
     */ 
    public function setDdd($ddd)
    {
        $this->ddd = $ddd;

        return $this;
    }

    /**
     * Get the value of telefone
     */ 
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set the value of telefone
     *
     * @return  self
     */ 
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of endereco
     */ 
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * Set the value of endereco
     *
     * @return  self
     */ 
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;

        return $this;
    }

    /**
     * Get the value of incluidoEm
     */ 
    public function getIncluidoEm()
    {
        return $this->incluidoEm;
    }

    /**
     * Set the value of incluidoEm
     *
     * @return  self
     */ 
    public function setIncluidoEm($incluidoEm)
    {
        $this->incluidoEm = $incluidoEm;

        return $this;
    }

    /**
     * Get the value of incluidoPor
     */ 
    public function getIncluidoPor()
    {
        return $this->incluidoPor;
    }

    /**
     * Set the value of incluidoPor
     *
     * @return  self
     */ 
    public function setIncluidoPor($incluidoPor)
    {
        $this->incluidoPor = $incluidoPor;

        return $this;
    }

    /**
     * Get the value of alteradoEm
     */ 
    public function getAlteradoEm()
    {
        return $this->alteradoEm;
    }

    /**
     * Set the value of alteradoEm
     *
     * @return  self
     */ 
    public function setAlteradoEm($alteradoEm)
    {
        $this->alteradoEm = $alteradoEm;

        return $this;
    }

    /**
     * Get the value of alteradoPor
     */ 
    public function getAlteradoPor()
    {
        return $this->alteradoPor;
    }

    /**
     * Set the value of alteradoPor
     *
     * @return  self
     */ 
    public function setAlteradoPor($alteradoPor)
    {
        $this->alteradoPor = $alteradoPor;

        return $this;
    }

    /**
     * Get the value of lixeira
     */ 
    public function getLixeira()
    {
        return $this->lixeira;
    }

    /**
     * Set the value of lixeira
     *
     * @return  self
     */ 
    public function setLixeira($lixeira)
    {
        $this->lixeira = $lixeira;

        return $this;
    }

    /**
     * Get the value of lixeiraEm
     */ 
    public function getLixeiraEm()
    {
        return $this->lixeiraEm;
    }

    /**
     * Set the value of lixeiraEm
     *
     * @return  self
     */ 
    public function setLixeiraEm($lixeiraEm)
    {
        $this->lixeiraEm = $lixeiraEm;

        return $this;
    }

    /**
     * Get the value of lixeiraPor
     */ 
    public function getLixeiraPor()
    {
        return $this->lixeiraPor;
    }

    /**
     * Set the value of lixeiraPor
     *
     * @return  self
     */ 
    public function setLixeiraPor($lixeiraPor)
    {
        $this->lixeiraPor = $lixeiraPor;

        return $this;
    }

    /**
     * Get the value of observacao
     */ 
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * Set the value of observacao
     *
     * @return  self
     */ 
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * Get the value of hostSmtp
     */ 
    public function getHostSmtp()
    {
        return $this->hostSmtp;
    }

    /**
     * Set the value of hostSmtp
     *
     * @return  self
     */ 
    public function setHostSmtp($hostSmtp)
    {
        $this->hostSmtp = $hostSmtp;

        return $this;
    }

    /**
     * Get the value of hostPorta
     */ 
    public function getHostPorta()
    {
        return $this->hostPorta;
    }

    /**
     * Set the value of hostPorta
     *
     * @return  self
     */ 
    public function setHostPorta($hostPorta)
    {
        $this->hostPorta = $hostPorta;

        return $this;
    }

    /**
     * Get the value of usuarioEmail
     */ 
    public function getUsuarioEmail()
    {
        return $this->usuarioEmail;
    }

    /**
     * Set the value of usuarioEmail
     *
     * @return  self
     */ 
    public function setUsuarioEmail($usuarioEmail)
    {
        $this->usuarioEmail = $usuarioEmail;

        return $this;
    }

    /**
     * Get the value of senhaEmail
     */ 
    public function getSenhaEmail()
    {
        return $this->senhaEmail;
    }

    /**
     * Set the value of senhaEmail
     *
     * @return  self
     */ 
    public function setSenhaEmail($senhaEmail)
    {
        $this->senhaEmail = $senhaEmail;

        return $this;
    }

    /**
     * Get the value of controlePesagem
     */ 
    public function getControlePesagem()
    {
        return $this->controlePesagem;
    }

    /**
     * Set the value of controlePesagem
     *
     * @return  self
     */ 
    public function setControlePesagem($controlePesagem)
    {
        $this->controlePesagem = $controlePesagem;

        return $this;
    }

    /**
     * Get the value of termoUsoConfirmadoEm
     */ 
    public function getTermoUsoConfirmadoEm()
    {
        return $this->termoUsoConfirmadoEm;
    }

    /**
     * Set the value of termoUsoConfirmadoEm
     *
     * @return  self
     */ 
    public function setTermoUsoConfirmadoEm($termoUsoConfirmadoEm)
    {
        $this->termoUsoConfirmadoEm = $termoUsoConfirmadoEm;

        return $this;
    }

    /**
     * Get the value of termoUsoConfirmadoPor
     */ 
    public function getTermoUsoConfirmadoPor()
    {
        return $this->termoUsoConfirmadoPor;
    }

    /**
     * Set the value of termoUsoConfirmadoPor
     *
     * @return  self
     */ 
    public function setTermoUsoConfirmadoPor($termoUsoConfirmadoPor)
    {
        $this->termoUsoConfirmadoPor = $termoUsoConfirmadoPor;

        return $this;
    }
}