<?php
class Pessoa implements JsonSerializable{

    private $id;
    private $classe;
    private $cpfCnpj;
    private $tipo;
    private $inscEstadual;
    private $inscMunicipal;
    private $nome;
    private $contato;
    private $cargoContato;
	private $ddd;
	private $telefone;
	private $email;
	private $endereco;
	private $lixeira;
	private $incluidoEm;
	private $incluidoPor;
	private $lixeiraEm;
	private $lixeiraPor;
	private $alteradoEm;
	private $alteradoPor;
	private $observacao;
	private $ativo;

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
     * Get the value of classe
     */ 
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set the value of classe
     *
     * @return  self
     */ 
    public function setClasse($classe)
    {
        $this->classe = $classe;

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
     * Get the value of tipo
     */ 
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     *
     * @return  self
     */ 
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

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
     * Get the value of cargoContato
     */ 
    public function getCargoContato()
    {
        return $this->cargoContato;
    }

    /**
     * Set the value of cargoContato
     *
     * @return  self
     */ 
    public function setCargoContato($cargoContato)
    {
        $this->cargoContato = $cargoContato;

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
	 * Get the value of ativo
	 */ 
	public function getAtivo()
	{
		return $this->ativo;
	}

	/**
	 * Set the value of ativo
	 *
	 * @return  self
	 */ 
	public function setAtivo($ativo)
	{
		$this->ativo = $ativo;

		return $this;
	}
}