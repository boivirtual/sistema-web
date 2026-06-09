<?php
class Produto implements JsonSerializable{

    private $id;
    private $modalidade;
    private $generico;
    private $complementoDescricao;
    private $descricao;
    private $apresentacao;
    private $quantidadeUnidade;
    private $unidade;
    private $idFabricante;
    private $referenciaFornecedor;
    private $estoque;
    private $estoqueAtual;
    private $observacao;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;

    public function __construct(){
        $this->modalidade = new ModalidadeProduto();
        $this->generico = new ProdutoGenerico();
        $this->apresentacao = new ApresentacaoProduto();
        $this->unidade = new ProdutoUnidade();
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
     * Get the value of modalidade
     */ 
    public function getModalidade()
    {
        return $this->modalidade;
    }

    /**
     * Set the value of modalidade
     *
     * @return  self
     */ 
    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;

        return $this;
    }

    /**
     * Get the value of generico
     */ 
    public function getGenerico()
    {
        return $this->generico;
    }

    /**
     * Set the value of generico
     *
     * @return  self
     */ 
    public function setGenerico($generico)
    {
        $this->generico = $generico;

        return $this;
    }

    /**
     * Get the value of complementoDescricao
     */ 
    public function getComplementoDescricao()
    {
        return $this->complementoDescricao;
    }

    /**
     * Set the value of complementoDescricao
     *
     * @return  self
     */ 
    public function setComplementoDescricao($complementoDescricao)
    {
        $this->complementoDescricao = $complementoDescricao;

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
     * Get the value of apresentacao
     */ 
    public function getApresentacao()
    {
        return $this->apresentacao;
    }

    /**
     * Set the value of apresentacao
     *
     * @return  self
     */ 
    public function setApresentacao($apresentacao)
    {
        $this->apresentacao = $apresentacao;

        return $this;
    }

    /**
     * Get the value of quantidadeUnidade
     */ 
    public function getQuantidadeUnidade()
    {
        return $this->quantidadeUnidade;
    }

    /**
     * Set the value of quantidadeUnidade
     *
     * @return  self
     */ 
    public function setQuantidadeUnidade($quantidadeUnidade)
    {
        $this->quantidadeUnidade = $quantidadeUnidade;

        return $this;
    }

    /**
     * Get the value of unidade
     */ 
    public function getUnidade()
    {
        return $this->unidade;
    }

    /**
     * Set the value of unidade
     *
     * @return  self
     */ 
    public function setUnidade($unidade)
    {
        $this->unidade = $unidade;

        return $this;
    }

    /**
     * Get the value of idFabricante
     */ 
    public function getIdFabricante()
    {
        return $this->idFabricante;
    }

    /**
     * Set the value of idFabricante
     *
     * @return  self
     */ 
    public function setIdFabricante($idFabricante)
    {
        $this->idFabricante = $idFabricante;

        return $this;
    }

    /**
     * Get the value of referenciaFornecedor
     */ 
    public function getReferenciaFornecedor()
    {
        return $this->referenciaFornecedor;
    }

    /**
     * Set the value of referenciaFornecedor
     *
     * @return  self
     */ 
    public function setReferenciaFornecedor($referenciaFornecedor)
    {
        $this->referenciaFornecedor = $referenciaFornecedor;

        return $this;
    }

    /**
     * Get the value of estoque
     */ 
    public function getEstoque()
    {
        return $this->estoque;
    }

    /**
     * Set the value of estoque
     *
     * @return  self
     */ 
    public function setEstoque($estoque)
    {
        $this->estoque = $estoque;

        return $this;
    }

    /**
     * Get the value of estoqueAtual
     */ 
    public function getEstoqueAtual()
    {
        return $this->estoqueAtual;
    }

    /**
     * Set the value of estoqueAtual
     *
     * @return  self
     */ 
    public function setEstoqueAtual($estoqueAtual)
    {
        $this->estoqueAtual = $estoqueAtual;

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
}