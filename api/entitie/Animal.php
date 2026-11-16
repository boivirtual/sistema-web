<?php
class Animal implements JsonSerializable{
    
    private $id;
    private $alfa;
    private $numerico;
    private $reprodutor;
    private $nome;
    private $nascimento;
    private $sexo;
    private $grauSangue;
    private $idMae;
    private $nomeMae;
    private $idPai;
    private $nomePai;
    private $primeiroPeso;
    private $lotePrimeiroPeso;
    private $dataPrimeiroPeso;
    private $pesoDesmama;
    private $loteDesmama;
    private $dataDesmama;
    private $ultimoPeso;
    private $loteUltimo;
    private $dataUltimo;
    private $raca;
    private $fazenda;
    private $pelagem;
    private $origem;
    private $marca;
    private $registroRen;
    private $registroRgd;
    private $registroSisbov;
    private $certificadora;
    private $observacao;
    private $ativo;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;
    private $baixadoEm;
    private $baixadoPor;
    private $situacao;
    private $origemAnterior;
    private $fazendaAnterior;
    private $emEstacaoMonta;
    private $aguardandoDiagnostico;
    private $prenhe;
    private $parida;
    private $solteira;
    private $coberturas;
    private $partos;
    private $abortos;
    private $descarteReproducao;
    private $descarteEm;
    private $descartePor;
    private $selecionadaReproducao;

    public function __construct(){
        $this->raca = new Raca();
        $this->fazenda = new Pessoa();
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
     * Get the value of alfa
     */ 
    public function getAlfa()
    {
        return $this->alfa;
    }

    /**
     * Set the value of alfa
     *
     * @return  self
     */ 
    public function setAlfa($alfa)
    {
        $this->alfa = $alfa;

        return $this;
    }

    /**
     * Get the value of numerico
     */ 
    public function getNumerico()
    {
        return $this->numerico;
    }

    /**
     * Set the value of numerico
     *
     * @return  self
     */ 
    public function setNumerico($numerico)
    {
        $this->numerico = $numerico;

        return $this;
    }

    /**
     * Get the value of reprodutor
     */ 
    public function getReprodutor()
    {
        return $this->reprodutor;
    }

    /**
     * Set the value of reprodutor
     *
     * @return  self
     */ 
    public function setReprodutor($reprodutor)
    {
        $this->reprodutor = $reprodutor;

        return $this;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

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
     * Get the value of grauSangue
     */ 
    public function getGrauSangue()
    {
        return $this->grauSangue;
    }

    /**
     * Set the value of grauSangue
     *
     * @return  self
     */ 
    public function setGrauSangue($grauSangue)
    {
        $this->grauSangue = $grauSangue;

        return $this;
    }

    /**
     * Get the value of idMae
     */ 
    public function getIdMae()
    {
        return $this->idMae;
    }

    /**
     * Set the value of idMae
     *
     * @return  self
     */ 
    public function setIdMae($idMae)
    {
        $this->idMae = $idMae;

        return $this;
    }

    /**
     * Get the value of nomeMae
     */ 
    public function getNomeMae()
    {
        return $this->nomeMae;
    }

    /**
     * Set the value of nomeMae
     *
     * @return  self
     */ 
    public function setNomeMae($nomeMae)
    {
        $this->nomeMae = $nomeMae;

        return $this;
    }

    /**
     * Get the value of idPai
     */ 
    public function getIdPai()
    {
        return $this->idPai;
    }

    /**
     * Set the value of idPai
     *
     * @return  self
     */ 
    public function setIdPai($idPai)
    {
        $this->idPai = $idPai;

        return $this;
    }

    /**
     * Get the value of nomePai
     */ 
    public function getNomePai()
    {
        return $this->nomePai;
    }

    /**
     * Set the value of nomePai
     *
     * @return  self
     */ 
    public function setNomePai($nomePai)
    {
        $this->nomePai = $nomePai;

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

    /**
     * Get the value of lotePrimeiroPeso
     */ 
    public function getLotePrimeiroPeso()
    {
        return $this->lotePrimeiroPeso;
    }

    /**
     * Set the value of lotePrimeiroPeso
     *
     * @return  self
     */ 
    public function setLotePrimeiroPeso($lotePrimeiroPeso)
    {
        $this->lotePrimeiroPeso = $lotePrimeiroPeso;

        return $this;
    }

    /**
     * Get the value of dataPrimeiroPeso
     */ 
    public function getDataPrimeiroPeso()
    {
        return $this->dataPrimeiroPeso;
    }

    /**
     * Set the value of dataPrimeiroPeso
     *
     * @return  self
     */ 
    public function setDataPrimeiroPeso($dataPrimeiroPeso)
    {
        $this->dataPrimeiroPeso = $dataPrimeiroPeso;

        return $this;
    }

    /**
     * Get the value of pesoDesmama
     */ 
    public function getPesoDesmama()
    {
        return $this->pesoDesmama;
    }

    /**
     * Set the value of pesoDesmama
     *
     * @return  self
     */ 
    public function setPesoDesmama($pesoDesmama)
    {
        $this->pesoDesmama = $pesoDesmama;

        return $this;
    }

    /**
     * Get the value of loteDesmama
     */ 
    public function getLoteDesmama()
    {
        return $this->loteDesmama;
    }

    /**
     * Set the value of loteDesmama
     *
     * @return  self
     */ 
    public function setLoteDesmama($loteDesmama)
    {
        $this->loteDesmama = $loteDesmama;

        return $this;
    }

    /**
     * Get the value of dataDesmama
     */ 
    public function getDataDesmama()
    {
        return $this->dataDesmama;
    }

    /**
     * Set the value of dataDesmama
     *
     * @return  self
     */ 
    public function setDataDesmama($dataDesmama)
    {
        $this->dataDesmama = $dataDesmama;

        return $this;
    }

    /**
     * Get the value of ultimoPeso
     */ 
    public function getUltimoPeso()
    {
        return $this->ultimoPeso;
    }

    /**
     * Set the value of ultimoPeso
     *
     * @return  self
     */ 
    public function setUltimoPeso($ultimoPeso)
    {
        $this->ultimoPeso = $ultimoPeso;

        return $this;
    }

    /**
     * Get the value of loteUltimo
     */ 
    public function getLoteUltimo()
    {
        return $this->loteUltimo;
    }

    /**
     * Set the value of loteUltimo
     *
     * @return  self
     */ 
    public function setLoteUltimo($loteUltimo)
    {
        $this->loteUltimo = $loteUltimo;

        return $this;
    }

    /**
     * Get the value of dataUltimo
     */ 
    public function getDataUltimo()
    {
        return $this->dataUltimo;
    }

    /**
     * Set the value of dataUltimo
     *
     * @return  self
     */ 
    public function setDataUltimo($dataUltimo)
    {
        $this->dataUltimo = $dataUltimo;

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
     * Get the value of fazenda
     */ 
    public function getFazenda()
    {
        return $this->fazenda;
    }

    /**
     * Set the value of fazenda
     *
     * @return  self
     */ 
    public function setFazenda($fazenda)
    {
        $this->fazenda = $fazenda;

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
     * Get the value of marca
     */ 
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set the value of marca
     *
     * @return  self
     */ 
    public function setMarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get the value of registroRen
     */ 
    public function getRegistroRen()
    {
        return $this->registroRen;
    }

    /**
     * Set the value of registroRen
     *
     * @return  self
     */ 
    public function setRegistroRen($registroRen)
    {
        $this->registroRen = $registroRen;

        return $this;
    }

    /**
     * Get the value of registroRgd
     */ 
    public function getRegistroRgd()
    {
        return $this->registroRgd;
    }

    /**
     * Set the value of registroRgd
     *
     * @return  self
     */ 
    public function setRegistroRgd($registroRgd)
    {
        $this->registroRgd = $registroRgd;

        return $this;
    }

    /**
     * Get the value of registroSisbov
     */ 
    public function getRegistroSisbov()
    {
        return $this->registroSisbov;
    }

    /**
     * Set the value of registroSisbov
     *
     * @return  self
     */ 
    public function setRegistroSisbov($registroSisbov)
    {
        $this->registroSisbov = $registroSisbov;

        return $this;
    }

    /**
     * Get the value of certificadora
     */ 
    public function getCertificadora()
    {
        return $this->certificadora;
    }

    /**
     * Set the value of certificadora
     *
     * @return  self
     */ 
    public function setCertificadora($certificadora)
    {
        $this->certificadora = $certificadora;

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
     * Get the value of ativo
     */ 
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Set the value of ativo
     *
     * @return  self
     */ 
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

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
     * Get the value of origemAnterior
     */ 
    public function getOrigemAnterior()
    {
        return $this->origemAnterior;
    }

    /**
     * Set the value of origemAnterior
     *
     * @return  self
     */ 
    public function setOrigemAnterior($origemAnterior)
    {
        $this->origemAnterior = $origemAnterior;

        return $this;
    }

    /**
     * Get the value of fazendaAnterior
     */ 
    public function getFazendaAnterior()
    {
        return $this->fazendaAnterior;
    }

    /**
     * Set the value of fazendaAnterior
     *
     * @return  self
     */ 
    public function setFazendaAnterior($fazendaAnterior)
    {
        $this->fazendaAnterior = $fazendaAnterior;

        return $this;
    }

    /**
     * Get the value of emEstacaoMonta
     */ 
    public function getEmEstacaoMonta()
    {
        return $this->emEstacaoMonta;
    }

    /**
     * Set the value of emEstacaoMonta
     *
     * @return  self
     */ 
    public function setEmEstacaoMonta($emEstacaoMonta)
    {
        $this->emEstacaoMonta = $emEstacaoMonta;

        return $this;
    }

    /**
     * Get the value of aguardandoDiagnostico
     */ 
    public function getAguardandoDiagnostico()
    {
        return $this->aguardandoDiagnostico;
    }

    /**
     * Set the value of aguardandoDiagnostico
     *
     * @return  self
     */ 
    public function setAguardandoDiagnostico($aguardandoDiagnostico)
    {
        $this->aguardandoDiagnostico = $aguardandoDiagnostico;

        return $this;
    }

    /**
     * Get the value of prenhe
     */ 
    public function getPrenhe()
    {
        return $this->prenhe;
    }

    /**
     * Set the value of prenhe
     *
     * @return  self
     */ 
    public function setPrenhe($prenhe)
    {
        $this->prenhe = $prenhe;

        return $this;
    }

    /**
     * Get the value of parida
     */ 
    public function getParida()
    {
        return $this->parida;
    }

    /**
     * Set the value of parida
     *
     * @return  self
     */ 
    public function setParida($parida)
    {
        $this->parida = $parida;

        return $this;
    }

    /**
     * Get the value of solteira
     */ 
    public function getSolteira()
    {
        return $this->solteira;
    }

    /**
     * Set the value of solteira
     *
     * @return  self
     */ 
    public function setSolteira($solteira)
    {
        $this->solteira = $solteira;

        return $this;
    }

    /**
     * Get the value of coberturas
     */ 
    public function getCoberturas()
    {
        return $this->coberturas;
    }

    /**
     * Set the value of coberturas
     *
     * @return  self
     */ 
    public function setCoberturas($coberturas)
    {
        $this->coberturas = $coberturas;

        return $this;
    }

    /**
     * Get the value of partos
     */ 
    public function getPartos()
    {
        return $this->partos;
    }

    /**
     * Set the value of partos
     *
     * @return  self
     */ 
    public function setPartos($partos)
    {
        $this->partos = $partos;

        return $this;
    }

    /**
     * Get the value of abortos
     */ 
    public function getAbortos()
    {
        return $this->abortos;
    }

    /**
     * Set the value of abortos
     *
     * @return  self
     */ 
    public function setAbortos($abortos)
    {
        $this->abortos = $abortos;

        return $this;
    }

    /**
     * Get the value of descarteReproducao
     */ 
    public function getDescarteReproducao()
    {
        return $this->descarteReproducao;
    }

    /**
     * Set the value of descarteReproducao
     *
     * @return  self
     */ 
    public function setDescarteReproducao($descarteReproducao)
    {
        $this->descarteReproducao = $descarteReproducao;

        return $this;
    }

    /**
     * Get the value of descarteEm
     */ 
    public function getDescarteEm()
    {
        return $this->descarteEm;
    }

    /**
     * Set the value of descarteEm
     *
     * @return  self
     */ 
    public function setDescarteEm($descarteEm)
    {
        $this->descarteEm = $descarteEm;

        return $this;
    }

    /**
     * Get the value of descartePor
     */ 
    public function getDescartePor()
    {
        return $this->descartePor;
    }

    /**
     * Set the value of descartePor
     *
     * @return  self
     */ 
    public function setDescartePor($descartePor)
    {
        $this->descartePor = $descartePor;

        return $this;
    }

    /**
     * Get the value of selecionadaReproducao
     */ 
    public function getSelecionadaReproducao()
    {
        return $this->selecionadaReproducao;
    }

    /**
     * Set the value of selecionadaReproducao
     *
     * @return  self
     */ 
    public function setSelecionadaReproducao($selecionadaReproducao)
    {
        $this->selecionadaReproducao = $selecionadaReproducao;

        return $this;
    }
}