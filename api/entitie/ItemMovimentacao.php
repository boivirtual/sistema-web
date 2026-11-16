<?php
class ItemMovimentacao implements JsonSerializable{

    private $id;
    private $movimentacao;
    private $animal;
    private $idAnimal;
    private $dataEmissao;
    private $peso;
    private $sexo;
    private $nascimento;
    private $raca;
    private $pelagem;
    private $mae;
    private $observacao;
    private $morte;
    private $categoriaCompra;
    private $qtdCategoriaCompra;
    private $idadeMesesCompra;
    private $sequenciaNumericaCompra;
    private $alfaCompra;
    private $pasto;
    private $categoria;
    private $qtdeCategoria;
    private $pesoMedio;
    private $pesoArroba;
    private $pesoArrobaMedio;

    public function __construct(){
        $this->movimentacao = new Movimentacao();
        $this->animal = new Animal();
        $this->raca = new Raca();
        $this->pelagem = new Pelagem();
        $this->mae = new Animal();
        $this->pasto = new Pasto();
        $this->categoria = new CategoriaIdade();
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
     * Get the value of idAnimal
     */ 
    public function getIdAnimal()
    {
        return $this->idAnimal;
    }

    /**
     * Set the value of idAnimal
     *
     * @return  self
     */ 
    public function setIdAnimal($idAnimal)
    {
        $this->idAnimal = $idAnimal;

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
     * Get the value of peso
     */ 
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set the value of peso
     *
     * @return  self
     */ 
    public function setPeso($peso)
    {
        $this->peso = $peso;

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
     * Get the value of mae
     */ 
    public function getMae()
    {
        return $this->mae;
    }

    /**
     * Set the value of mae
     *
     * @return  self
     */ 
    public function setMae($mae)
    {
        $this->mae = $mae;

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
     * Get the value of morte
     */ 
    public function getMorte()
    {
        return $this->morte;
    }

    /**
     * Set the value of morte
     *
     * @return  self
     */ 
    public function setMorte($morte)
    {
        $this->morte = $morte;

        return $this;
    }

    /**
     * Get the value of categoriaCompra
     */ 
    public function getCategoriaCompra()
    {
        return $this->categoriaCompra;
    }

    /**
     * Set the value of categoriaCompra
     *
     * @return  self
     */ 
    public function setCategoriaCompra($categoriaCompra)
    {
        $this->categoriaCompra = $categoriaCompra;

        return $this;
    }

    /**
     * Get the value of qtdCategoriaCompra
     */ 
    public function getQtdCategoriaCompra()
    {
        return $this->qtdCategoriaCompra;
    }

    /**
     * Set the value of qtdCategoriaCompra
     *
     * @return  self
     */ 
    public function setQtdCategoriaCompra($qtdCategoriaCompra)
    {
        $this->qtdCategoriaCompra = $qtdCategoriaCompra;

        return $this;
    }

    /**
     * Get the value of idadeMesesCompra
     */ 
    public function getIdadeMesesCompra()
    {
        return $this->idadeMesesCompra;
    }

    /**
     * Set the value of idadeMesesCompra
     *
     * @return  self
     */ 
    public function setIdadeMesesCompra($idadeMesesCompra)
    {
        $this->idadeMesesCompra = $idadeMesesCompra;

        return $this;
    }

    /**
     * Get the value of sequenciaNumericaCompra
     */ 
    public function getSequenciaNumericaCompra()
    {
        return $this->sequenciaNumericaCompra;
    }

    /**
     * Set the value of sequenciaNumericaCompra
     *
     * @return  self
     */ 
    public function setSequenciaNumericaCompra($sequenciaNumericaCompra)
    {
        $this->sequenciaNumericaCompra = $sequenciaNumericaCompra;

        return $this;
    }

    /**
     * Get the value of alfaCompra
     */ 
    public function getAlfaCompra()
    {
        return $this->alfaCompra;
    }

    /**
     * Set the value of alfaCompra
     *
     * @return  self
     */ 
    public function setAlfaCompra($alfaCompra)
    {
        $this->alfaCompra = $alfaCompra;

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
     * Get the value of qtdeCategoria
     */ 
    public function getQtdeCategoria()
    {
        return $this->qtdeCategoria;
    }

    /**
     * Set the value of qtdeCategoria
     *
     * @return  self
     */ 
    public function setQtdeCategoria($qtdeCategoria)
    {
        $this->qtdeCategoria = $qtdeCategoria;

        return $this;
    }

    /**
     * Get the value of pesoMedio
     */ 
    public function getPesoMedio()
    {
        return $this->pesoMedio;
    }

    /**
     * Set the value of pesoMedio
     *
     * @return  self
     */ 
    public function setPesoMedio($pesoMedio)
    {
        $this->pesoMedio = $pesoMedio;

        return $this;
    }

    /**
     * Get the value of pesoArroba
     */ 
    public function getPesoArroba()
    {
        return $this->pesoArroba;
    }

    /**
     * Set the value of pesoArroba
     *
     * @return  self
     */ 
    public function setPesoArroba($pesoArroba)
    {
        $this->pesoArroba = $pesoArroba;

        return $this;
    }

    /**
     * Get the value of pesoArrobaMedio
     */ 
    public function getPesoArrobaMedio()
    {
        return $this->pesoArrobaMedio;
    }

    /**
     * Set the value of pesoArrobaMedio
     *
     * @return  self
     */ 
    public function setPesoArrobaMedio($pesoArrobaMedio)
    {
        $this->pesoArrobaMedio = $pesoArrobaMedio;

        return $this;
    }
}