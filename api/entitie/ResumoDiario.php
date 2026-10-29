<?php
/**
 * Resumo Diário da Fazenda (aplicativo) — um registro da tbl_resumo_diario.
 * Tipo: 'R' = Resumo, 'F' = Folga.
 */
class ResumoDiario implements JsonSerializable{

    const TIPO_RESUMO = 'R';
    const TIPO_FOLGA = 'F';

    private $id;
    private $fazenda;
    private $usuario;
    private $data;
    private $tipo;
    private $incluidoEm;

    public function jsonSerialize(){
        return (object) get_object_vars($this);
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getFazenda(){
        return $this->fazenda;
    }

    public function setFazenda($fazenda){
        $this->fazenda = $fazenda;
        return $this;
    }

    public function getUsuario(){
        return $this->usuario;
    }

    public function setUsuario($usuario){
        $this->usuario = $usuario;
        return $this;
    }

    public function getData(){
        return $this->data;
    }

    public function setData($data){
        $this->data = $data;
        return $this;
    }

    public function getTipo(){
        return $this->tipo;
    }

    public function setTipo($tipo){
        $this->tipo = $tipo;
        return $this;
    }

    public function getIncluidoEm(){
        return $this->incluidoEm;
    }

    public function setIncluidoEm($incluidoEm){
        $this->incluidoEm = $incluidoEm;
        return $this;
    }
}
