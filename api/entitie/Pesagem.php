<?php
class Pesagem implements JsonSerializable{

    private $id;
    private $controle;
    private $data;
    private $local;
    private $epoca;
    private $lote;
    private $quantidadeAnimais;
    private $pesoKg;
    private $pesoArroba;
    private $pesoMedioKg;
    private $pesoMedioArroba;
    private $filtro;
    private $finalizada;
    private $pasto;
    private $categoria;
    private $sexo;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;
    private $tipo_registro;
    private $criteriosApartacao;

    public function __construct(){
        $this->local = new Pessoa();
        $this->epoca = new EpocaPesagem();
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
     * Get the value of controle
     */ 
    public function getControle()
    {
        return $this->controle;
    }

    /**
     * Set the value of controle
     *
     * @return  self
     */ 
    public function setControle($controle)
    {
        $this->controle = $controle;

        return $this;
    }

    /**
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    public function setData($data)
    {
        $this->data = $data;

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
     * Get the value of epoca
     */ 
    public function getEpoca()
    {
        return $this->epoca;
    }

    /**
     * Set the value of epoca
     *
     * @return  self
     */ 
    public function setEpoca($epoca)
    {
        $this->epoca = $epoca;

        return $this;
    }

    /**
     * Get the value of lote
     */ 
    public function getLote()
    {
        return $this->lote;
    }

    /**
     * Set the value of lote
     *
     * @return  self
     */ 
    public function setLote($lote)
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get the value of quantidadeAnimais
     */ 
    public function getQuantidadeAnimais()
    {
        return $this->quantidadeAnimais;
    }

    /**
     * Set the value of quantidadeAnimais
     *
     * @return  self
     */ 
    public function setQuantidadeAnimais($quantidadeAnimais)
    {
        $this->quantidadeAnimais = $quantidadeAnimais;

        return $this;
    }

    /**
     * Get the value of pesoKg
     */ 
    public function getPesoKg()
    {
        return $this->pesoKg;
    }

    /**
     * Set the value of pesoKg
     *
     * @return  self
     */ 
    public function setPesoKg($pesoKg)
    {
        $this->pesoKg = $pesoKg;

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
     * Get the value of pesoMedioKg
     */ 
    public function getPesoMedioKg()
    {
        return $this->pesoMedioKg;
    }

    /**
     * Set the value of pesoMedioKg
     *
     * @return  self
     */ 
    public function setPesoMedioKg($pesoMedioKg)
    {
        $this->pesoMedioKg = $pesoMedioKg;

        return $this;
    }

    /**
     * Get the value of pesoMedioArroba
     */ 
    public function getPesoMedioArroba()
    {
        return $this->pesoMedioArroba;
    }

    /**
     * Set the value of pesoMedioArroba
     *
     * @return  self
     */ 
    public function setPesoMedioArroba($pesoMedioArroba)
    {
        $this->pesoMedioArroba = $pesoMedioArroba;

        return $this;
    }

    /**
     * Get the value of filtro
     */ 
    public function getFiltro()
    {
        return $this->filtro;
    }

    /**
     * Set the value of filtro
     *
     * @return  self
     */ 
    public function setFiltro($filtro)
    {
        $this->filtro = $filtro;

        return $this;
    }

    /**
     * Get the value of finalizada
     */ 
    public function getFinalizada()
    {
        return $this->finalizada;
    }

    /**
     * Set the value of finalizada
     *
     * @return  self
     */ 
    public function setFinalizada($finalizada)
    {
        $this->finalizada = $finalizada;

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
     * Get the value of tipoRegistro
     */ 
    public function gettipoRegistro()
    {
        return $this->tipoRegistro;
    }

    /**
     * Set the value of tipoRegistro
     *
     * @return  self
     */ 
    public function settipoRegistro($tipoRegistro)
    {
        $this->tipoRegistro = $tipoRegistro;

        return $this;
    }

    // métodos getter/setter criteriosApartacao
    public function getCriteriosApartacao() {
        return $this->criteriosApartacao;
    }

    public function setCriteriosApartacao($criteriosApartacao) {
        $this->criteriosApartacao = $criteriosApartacao;
        return $this;
    }
}