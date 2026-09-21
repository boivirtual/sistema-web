<?php

require_once "Endereco.php"; 

class Usuario implements JsonSerializable{
	
	private $id;
	private $cpf;
	private $cnpj;
	private $email;
	private $nome;
	private $grupo;
	private $senha;
	private $dataNascimento;
	private $idade;
	private $sexo;
	private $observacao;
	private $incluidoEm;
	private $incluidoPor;
	private $alteradoEm;
	private $alteradoPor;
	private $lixeira;
	private $lixeiraEm;
	private $lixeiraPor;
	private $situacao;
	private $autorizaSenha;
	private $foto;
	private $local;
	private $endereco;

	public function __construct(){
		$this->endereco = new Endereco();
	}

	public function __destruct(){

	}

	public function jsonSerialize() {
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
	 * Get the value of cpf
	 */ 
	public function getCpf()
	{
		return $this->cpf;
	}

	/**
	 * Set the value of cpf
	 *
	 * @return  self
	 */ 
	public function setCpf($cpf)
	{
		$this->cpf = $cpf;

		return $this;
	}

	/**
	 * Get the value of cnpj
	 */ 
	public function getCnpj()
	{
		return $this->cnpj;
	}

	/**
	 * Set the value of cnpj
	 *
	 * @return  self
	 */ 
	public function setCnpj($cnpj)
	{
		$this->cnpj = $cnpj;

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
	 * Get the value of grupo
	 */ 
	public function getGrupo()
	{
		return $this->grupo;
	}

	/**
	 * Set the value of grupo
	 *
	 * @return  self
	 */ 
	public function setGrupo($grupo)
	{
		$this->grupo = $grupo;

		return $this;
	}

	/**
	 * Get the value of senha
	 */ 
	public function getSenha()
	{
		return $this->senha;
	}

	/**
	 * Set the value of senha
	 *
	 * @return  self
	 */ 
	public function setSenha($senha)
	{
		$this->senha = $senha;

		return $this;
	}

	/**
	 * Get the value of dataNascimento
	 */ 
	public function getDataNascimento()
	{
		return $this->dataNascimento;
	}

	/**
	 * Set the value of dataNascimento
	 *
	 * @return  self
	 */ 
	public function setDataNascimento($dataNascimento)
	{
		$this->dataNascimento = $dataNascimento;

		return $this;
	}

	/**
	 * Get the value of idade
	 */ 
	public function getIdade()
	{
		return $this->idade;
	}

	/**
	 * Set the value of idade
	 *
	 * @return  self
	 */ 
	public function setIdade($idade)
	{
		$this->idade = $idade;

		return $this;
	}

	/**
	 * Get the value of sexo
	 */ 
	public function getSexo()
	{
		return $this->sexo;
	}

	/**
	 * Set the value of sexo
	 *
	 * @return  self
	 */ 
	public function setSexo($sexo)
	{
		$this->sexo = $sexo;

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
	 * Get the value of situacao
	 */ 
	public function getSituacao()
	{
		return $this->situacao;
	}

	/**
	 * Set the value of situacao
	 *
	 * @return  self
	 */ 
	public function setSituacao($situacao)
	{
		$this->situacao = $situacao;

		return $this;
	}

	/**
	 * Get the value of autorizaSenha
	 */ 
	public function getAutorizaSenha()
	{
		return $this->autorizaSenha;
	}

	/**
	 * Set the value of autorizaSenha
	 *
	 * @return  self
	 */ 
	public function setAutorizaSenha($autorizaSenha)
	{
		$this->autorizaSenha = $autorizaSenha;

		return $this;
	}

	/**
	 * Get the value of foto
	 */ 
	public function getFoto()
	{
		return $this->foto;
	}

	/**
	 * Set the value of foto
	 *
	 * @return  self
	 */ 
	public function setFoto($foto)
	{
		$this->foto = $foto;

		return $this;
	}

	/**
	 * Get the value of local
	 */ 
	public function getLocal()
	{
		return $this->local;
	}

	/**
	 * Set the value of local
	 *
	 * @return  self
	 */ 
	public function setLocal($local)
	{
		$this->local = $local;

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
}