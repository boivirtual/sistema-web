<?php
class ItemPesagem implements JsonSerializable{

    private $id;
    private $pesagem;
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
    private $categoria;
    private $pesoMedio;
    private $arroba;
    private $arrobaMedio;
    private $qtdAnimais;
    private $criterioApartacao;
    private $mensItemRepetido;
    private $idPesagemItemRepetido;
    private $ultimoPeso = 0;

    public function __construct(){
        $this->pesagem = new Pesagem();
        $this->animal = new Animal();
        $this->raca = new Raca();
        $this->pelagem = new Pelagem();
        $this->mae = new Animal();
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
     * Get the value of pesagem
     */ 
    public function getPesagem()
    {
        return $this->pesagem;
    }

    /**
     * Set the value of pesagem
     *
     * @return  self
     */ 
    public function setPesagem($pesagem)
    {
        $this->pesagem = $pesagem;

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
     * Get the value of arroba
     */ 
    public function getArroba()
    {
        return $this->arroba;
    }

    /**
     * Set the value of arroba
     *
     * @return  self
     */ 
    public function setArroba($arroba)
    {
        $this->arroba = $arroba;

        return $this;
    }

    /**
     * Get the value of arrobaMedio
     */ 
    public function getArrobaMedio()
    {
        return $this->arrobaMedio;
    }

    /**
     * Set the value of arrobaMedio
     *
     * @return  self
     */ 
    public function setArrobaMedio($arrobaMedio)
    {
        $this->arrobaMedio = $arrobaMedio;

        return $this;
    }

    /**
     * Get the value of qtdAnimais
     */ 
    public function getQtdAnimais()
    {
        return $this->qtdAnimais;
    }

    /**
     * Set the value of qtdAnimais
     *
     * @return  self
     */ 
    public function setQtdAnimais($qtdAnimais)
    {
        $this->qtdAnimais = $qtdAnimais;

        return $this;
    }

    public function getCriterioApartacao() {
        return $this->criterioApartacao;
    }

    public function setCriterioApartacao($criterioApartacao) {
        $this->criterioApartacao = $criterioApartacao;
        return $this;
    }    

    public function getMensItemRepetido() {
        return $this->mensItemRepetido;
    }

    public function setMensItemRepetido($mensItemRepetido) {
        $this->mensItemRepetido = $mensItemRepetido;
        return $this;
    }    

    public function getIdPesagemItemRepetido() {
        return $this->idPesagemItemRepetido;
    }

    public function setIdPesagemItemRepetido($idPesagemItemRepetido) {
        $this->idPesagemItemRepetido = $idPesagemItemRepetido;
        return $this;
    }    

    public function getUltimoPeso() {
        return $this->ultimoPeso;
    }

    public function setUltimoPeso($ultimoPeso) {
        $this->ultimoPeso = $ultimoPeso;
    }
}