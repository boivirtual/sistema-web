<?php
class ContaPagamento implements JsonSerializable{

    private $id;
    private $descricao;
    private $tipo;
    private $banco;
    private $agencia;
    private $conta;
    private $cartao;
    private $saldoInicial;
    private $dataSaldo;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;

    public function __construct(){
        $this->banco = new Banco();
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
     * Get the value of descricao
     */ 
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     *
     * @return  self
     */ 
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

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
     * Get the value of banco
     */ 
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set the value of banco
     *
     * @return  self
     */ 
    public function setBanco($banco)
    {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get the value of agencia
     */ 
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Set the value of agencia
     *
     * @return  self
     */ 
    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;

        return $this;
    }

    /**
     * Get the value of conta
     */ 
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * Set the value of conta
     *
     * @return  self
     */ 
    public function setConta($conta)
    {
        $this->conta = $conta;

        return $this;
    }

    /**
     * Get the value of cartao
     */ 
    public function getCartao()
    {
        return $this->cartao;
    }

    /**
     * Set the value of cartao
     *
     * @return  self
     */ 
    public function setCartao($cartao)
    {
        $this->cartao = $cartao;

        return $this;
    }

    /**
     * Get the value of saldoInicial
     */ 
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * Set the value of saldoInicial
     *
     * @return  self
     */ 
    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;

        return $this;
    }

    /**
     * Get the value of dataSaldo
     */ 
    public function getDataSaldo()
    {
        return $this->dataSaldo;
    }

    /**
     * Set the value of dataSaldo
     *
     * @return  self
     */ 
    public function setDataSaldo($dataSaldo)
    {
        $this->dataSaldo = $dataSaldo;

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
}