<?php
class AnimalPasto implements JsonSerializable{
    private $pasto;
    private $local;
    private $animal;
    private $nascimento;
    private $categoria;
    private $sexo;
    private $raca;
    private $situacao;
    private $motivoMorte;
    private $observacao;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $baixadoEm;
    private $baixadoPor;

    public function __construct(){
        $this->pasto = new Pasto();
        $this->local = new Pessoa();
        $this->categoria = new CategoriaIdade();
        $this->raca = new Raca();
    }

    public function jsonSerialize(){
        return (object) get_object_vars($this);
    }

    /**
     * Get the value of pasto
     */ 
    public function getPasto()
    {
        return $this->pasto;
    }

    /**
     * Set the value of pasto
     *
     * @return  self
     */ 
    public function setPasto($pasto)
    {
        $this->pasto = $pasto;

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
     * Get the value of animal
     */ 
    public function getAnimal()
    {
        return $this->animal;
    }

    /**
     * Set the value of animal
     *
     * @return  self
     */ 
    public function setAnimal($animal)
    {
        $this->animal = $animal;

        return $this;
    }

    /**
     * Get the value of nascimento
     */ 
    public function getNascimento()
    {
        return $this->nascimento;
    }

    /**
     * Set the value of nascimento
     *
     * @return  self
     */ 
    public function setNascimento($nascimento)
    {
        $this->nascimento = $nascimento;

        return $this;
    }

    /**
     * Get the value of categoria
     */ 
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set the value of categoria
     *
     * @return  self
     */ 
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

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
     * Get the value of raca
     */ 
    public function getRaca()
    {
        return $this->raca;
    }

    /**
     * Set the value of raca
     *
     * @return  self
     */ 
    public function setRaca($raca)
    {
        $this->raca = $raca;

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
     * Get the value of motivoMorte
     */ 
    public function getMotivoMorte()
    {
        return $this->motivoMorte;
    }

    /**
     * Set the value of motivoMorte
     *
     * @return  self
     */ 
    public function setMotivoMorte($motivoMorte)
    {
        $this->motivoMorte = $motivoMorte;

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
     * Get the value of baixadoEm
     */ 
    public function getBaixadoEm()
    {
        return $this->baixadoEm;
    }

    /**
     * Set the value of baixadoEm
     *
     * @return  self
     */ 
    public function setBaixadoEm($baixadoEm)
    {
        $this->baixadoEm = $baixadoEm;

        return $this;
    }

    /**
     * Get the value of baixadoPor
     */ 
    public function getBaixadoPor()
    {
        return $this->baixadoPor;
    }

    /**
     * Set the value of baixadoPor
     *
     * @return  self
     */ 
    public function setBaixadoPor($baixadoPor)
    {
        $this->baixadoPor = $baixadoPor;

        return $this;
    }
}