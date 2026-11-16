<?php
class ProdutoUnidade implements JsonSerializable{

    private $id;
    private $unidade;
    private $descricao;
    private $incluidoEm;
    private $incluidoPor;
    private $alteradoEm;
    private $alteradoPor;
    private $lixeira;
    private $lixeiraEm;
    private $lixeiraPor;

    public function jsonSerialize(){
        return (object) get_object_vars($this);
    }
}