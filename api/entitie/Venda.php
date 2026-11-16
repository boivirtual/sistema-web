<?php
class Venda implements JsonSerializable{

    private $id;
    private $categoria;
    private $origem;
    private $destino;
    private $situacao;
    private $emissao;
    private $tipo;
    private $totalVenda;
    private $totalDesconto;
    private $totalReceber;
    private $valorPrimeiraParcela;
    private $vencimentoPrimeiraParcela;
    private $formaPgtoPrimeiraParcela;
    private $contaPgtoPrimeiraParcela;
    private $gta;
    private $transportadora;
    private $motorista;
    private $contaContabil;
    private $centroCusto;
    private $arrayItem;
    private $arrayParcela;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;

    public function __construct(){
        $this->origem = new Pessoa();
        //$this->destino = new Pessoa();
        $this->formaPgtoPrimeiraParcela = new FormaPagamento();
        $this->contaPgtoPrimeiraParcela = new ContaPagamento();
        $this->centroCusto = new CentroCusto();
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
     * Get the value of emissao
     */ 
    public function getEmissao()
    {
        return $this->emissao;
    }

    /**
     * Set the value of emissao
     *
     * @return  self
     */ 
    public function setEmissao($emissao)
    {
        $this->emissao = $emissao;

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
     * Get the value of totalVenda
     */ 
    public function getTotalVenda()
    {
        return $this->totalVenda;
    }

    /**
     * Set the value of totalVenda
     *
     * @return  self
     */ 
    public function setTotalVenda($totalVenda)
    {
        $this->totalVenda = $totalVenda;

        return $this;
    }

    /**
     * Get the value of totalDesconto
     */ 
    public function getTotalDesconto()
    {
        return $this->totalDesconto;
    }

    /**
     * Set the value of totalDesconto
     *
     * @return  self
     */ 
    public function setTotalDesconto($totalDesconto)
    {
        $this->totalDesconto = $totalDesconto;

        return $this;
    }

    /**
     * Get the value of totalReceber
     */ 
    public function getTotalReceber()
    {
        return $this->totalReceber;
    }

    /**
     * Set the value of totalReceber
     *
     * @return  self
     */ 
    public function setTotalReceber($totalReceber)
    {
        $this->totalReceber = $totalReceber;

        return $this;
    }

    /**
     * Get the value of valorPrimeiraParcela
     */ 
    public function getValorPrimeiraParcela()
    {
        return $this->valorPrimeiraParcela;
    }

    /**
     * Set the value of valorPrimeiraParcela
     *
     * @return  self
     */ 
    public function setValorPrimeiraParcela($valorPrimeiraParcela)
    {
        $this->valorPrimeiraParcela = $valorPrimeiraParcela;

        return $this;
    }

    /**
     * Get the value of vencimentoPrimeiraParcela
     */ 
    public function getVencimentoPrimeiraParcela()
    {
        return $this->vencimentoPrimeiraParcela;
    }

    /**
     * Set the value of vencimentoPrimeiraParcela
     *
     * @return  self
     */ 
    public function setVencimentoPrimeiraParcela($vencimentoPrimeiraParcela)
    {
        $this->vencimentoPrimeiraParcela = $vencimentoPrimeiraParcela;

        return $this;
    }

    /**
     * Get the value of formaPgtoPrimeiraParcela
     */ 
    public function getFormaPgtoPrimeiraParcela()
    {
        return $this->formaPgtoPrimeiraParcela;
    }

    /**
     * Set the value of formaPgtoPrimeiraParcela
     *
     * @return  self
     */ 
    public function setFormaPgtoPrimeiraParcela($formaPgtoPrimeiraParcela)
    {
        $this->formaPgtoPrimeiraParcela = $formaPgtoPrimeiraParcela;

        return $this;
    }

    /**
     * Get the value of contaPgtoPrimeiraParcela
     */ 
    public function getContaPgtoPrimeiraParcela()
    {
        return $this->contaPgtoPrimeiraParcela;
    }

    /**
     * Set the value of contaPgtoPrimeiraParcela
     *
     * @return  self
     */ 
    public function setContaPgtoPrimeiraParcela($contaPgtoPrimeiraParcela)
    {
        $this->contaPgtoPrimeiraParcela = $contaPgtoPrimeiraParcela;

        return $this;
    }

    /**
     * Get the value of gta
     */ 
    public function getGta()
    {
        return $this->gta;
    }

    /**
     * Set the value of gta
     *
     * @return  self
     */ 
    public function setGta($gta)
    {
        $this->gta = $gta;

        return $this;
    }

    /**
     * Get the value of transportadora
     */ 
    public function getTransportadora()
    {
        return $this->transportadora;
    }

    /**
     * Set the value of transportadora
     *
     * @return  self
     */ 
    public function setTransportadora($transportadora)
    {
        $this->transportadora = $transportadora;

        return $this;
    }

    /**
     * Get the value of motorista
     */ 
    public function getMotorista()
    {
        return $this->motorista;
    }

    /**
     * Set the value of motorista
     *
     * @return  self
     */ 
    public function setMotorista($motorista)
    {
        $this->motorista = $motorista;

        return $this;
    }

    /**
     * Get the value of contaContabil
     */ 
    public function getContaContabil()
    {
        return $this->contaContabil;
    }

    /**
     * Set the value of contaContabil
     *
     * @return  self
     */ 
    public function setContaContabil($contaContabil)
    {
        $this->contaContabil = $contaContabil;

        return $this;
    }

    /**
     * Get the value of centroCusto
     */ 
    public function getCentroCusto()
    {
        return $this->centroCusto;
    }

    /**
     * Set the value of centroCusto
     *
     * @return  self
     */ 
    public function setCentroCusto($centroCusto)
    {
        $this->centroCusto = $centroCusto;

        return $this;
    }

    /**
     * Get the value of arrayItem
     */ 
    public function getArrayItem()
    {
        return $this->arrayItem;
    }

    /**
     * Set the value of arrayItem
     *
     * @return  self
     */ 
    public function setArrayItem($arrayItem)
    {
        $this->arrayItem = $arrayItem;

        return $this;
    }

    /**
     * Get the value of arrayParcela
     */ 
    public function getArrayParcela()
    {
        return $this->arrayParcela;
    }

    /**
     * Set the value of arrayParcela
     *
     * @return  self
     */ 
    public function setArrayParcela($arrayParcela)
    {
        $this->arrayParcela = $arrayParcela;

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