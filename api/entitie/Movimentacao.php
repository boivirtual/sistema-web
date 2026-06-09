<?php
class Movimentacao implements JsonSerializable{
    
    private $id;
    private $controle;
    private $data;
    private $origem;
    private $destino;
    private $tipo;
    private $qtdAnimalPesado;
    private $pesoKg;
    private $pesoArroba;
    private $pesoMedioKg;
    private $pesoMedioArroba;
    private $filtro;
    private $situacao;
    private $venda;
    private $pesagem;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;
    private $aceiteTransferenciaEm;
    private $aceiteTransferenciaPor;
    private $aceiteFinanceiroEm;
    private $aceiteFinanceiroPor;

    public function __construct(){
        $this->origem = new Pessoa();
        //$this->destino = new Pessoa();
        $this->pesagem = new Pesagem();
        $this->venda = new Venda();
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
     * Get the value of qtdAnimalPesado
     */ 
    public function getQtdAnimalPesado()
    {
        return $this->qtdAnimalPesado;
    }

    /**
     * Set the value of qtdAnimalPesado
     *
     * @return  self
     */ 
    public function setQtdAnimalPesado($qtdAnimalPesado)
    {
        $this->qtdAnimalPesado = $qtdAnimalPesado;

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
     * Get the value of venda
     */ 
    public function getVenda()
    {
        return $this->venda;
    }

    /**
     * Set the value of venda
     *
     * @return  self
     */ 
    public function setVenda($venda)
    {
        $this->venda = $venda;

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
     * Get the value of aceiteTransferenciaEm
     */ 
    public function getAceiteTransferenciaEm()
    {
        return $this->aceiteTransferenciaEm;
    }

    /**
     * Set the value of aceiteTransferenciaEm
     *
     * @return  self
     */ 
    public function setAceiteTransferenciaEm($aceiteTransferenciaEm)
    {
        $this->aceiteTransferenciaEm = $aceiteTransferenciaEm;

        return $this;
    }

    /**
     * Get the value of aceiteTransferenciaPor
     */ 
    public function getAceiteTransferenciaPor()
    {
        return $this->aceiteTransferenciaPor;
    }

    /**
     * Set the value of aceiteTransferenciaPor
     *
     * @return  self
     */ 
    public function setAceiteTransferenciaPor($aceiteTransferenciaPor)
    {
        $this->aceiteTransferenciaPor = $aceiteTransferenciaPor;

        return $this;
    }

    /**
     * Get the value of aceiteFinanceiroEm
     */ 
    public function getAceiteFinanceiroEm()
    {
        return $this->aceiteFinanceiroEm;
    }

    /**
     * Set the value of aceiteFinanceiroEm
     *
     * @return  self
     */ 
    public function setAceiteFinanceiroEm($aceiteFinanceiroEm)
    {
        $this->aceiteFinanceiroEm = $aceiteFinanceiroEm;

        return $this;
    }

    /**
     * Get the value of aceiteFinanceiroPor
     */ 
    public function getAceiteFinanceiroPor()
    {
        return $this->aceiteFinanceiroPor;
    }

    /**
     * Set the value of aceiteFinanceiroPor
     *
     * @return  self
     */ 
    public function setAceiteFinanceiroPor($aceiteFinanceiroPor)
    {
        $this->aceiteFinanceiroPor = $aceiteFinanceiroPor;

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
}