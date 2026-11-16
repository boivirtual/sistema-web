<?php
class ProdutoEstoque implements JsonSerializable{

    private $id;
    private $local;
    private $quantidade;
    private $atual;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;

    public function __construct(){
        $this->local = new Pessoa();
        $this->id = new Produto();
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
     * Get the value of quantidade
     */ 
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set the value of quantidade
     *
     * @return  self
     */ 
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get the value of atual
     */ 
    public function getAtual()
    {
        return $this->atual;
    }

    /**
     * Set the value of atual
     *
     * @return  self
     */ 
    public function setAtual($atual)
    {
        $this->atual = $atual;

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