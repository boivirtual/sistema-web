<?php
class MovimentacaoEstoque implements JsonSerializable{

    private $id;
    private $animal;
    private $dataEmissao;
    private $nascimento;
    private $categoria;
    private $local;
    private $entradaSaida;
    private $tipo;
    private $origem;
    private $destino;
    private $movimentacao;
    private $pasto;
    private $raca;
    private $pelagem;
    private $sexo;
    private $primeiroPeso;

    public function __construct(){
        $this->animal = new Animal();
        $this->categoria = new CategoriaIdade();
        $this->local = new Pessoa();
        $this->origem = new Pessoa();
        $this->destino = new Pessoa();
        $this->movimentacao = new Movimentacao();
        $this->pasto = new Pasto();
        $this->raca = new Raca();
        $this->pelagem = new Pelagem();
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
     * Get the value of dataEmissao
     */ 
    public function getDataEmissao()
    {
        return $this->dataEmissao;
    }

    /**
     * Set the value of dataEmissao
     *
     * @return  self
     */ 
    public function setDataEmissao($dataEmissao)
    {
        $this->dataEmissao = $dataEmissao;

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
     * Get the value of entradaSaida
     */ 
    public function getEntradaSaida()
    {
        return $this->entradaSaida;
    }

    /**
     * Set the value of entradaSaida
     *
     * @return  self
     */ 
    public function setEntradaSaida($entradaSaida)
    {
        $this->entradaSaida = $entradaSaida;

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
     * Get the value of origem
     */ 
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * Set the value of origem
     *
     * @return  self
     */ 
    public function setOrigem($origem)
    {
        $this->origem = $origem;

        return $this;
    }

    /**
     * Get the value of destino
     */ 
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Set the value of destino
     *
     * @return  self
     */ 
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }

    /**
     * Get the value of movimentacao
     */ 
    public function getMovimentacao()
    {
        return $this->movimentacao;
    }

    /**
     * Set the value of movimentacao
     *
     * @return  self
     */ 
    public function setMovimentacao($movimentacao)
    {
        $this->movimentacao = $movimentacao;

        return $this;
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
     * Get the value of pelagem
     */ 
    public function getPelagem()
    {
        return $this->pelagem;
    }

    /**
     * Set the value of pelagem
     *
     * @return  self
     */ 
    public function setPelagem($pelagem)
    {
        $this->pelagem = $pelagem;

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
     * Get the value of primeiroPeso
     */ 
    public function getPrimeiroPeso()
    {
        return $this->primeiroPeso;
    }

    /**
     * Set the value of primeiroPeso
     *
     * @return  self
     */ 
    public function setPrimeiroPeso($primeiroPeso)
    {
        $this->primeiroPeso = $primeiroPeso;

        return $this;
    }
}