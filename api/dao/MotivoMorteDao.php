<?php
class MotivoMorteDao{

    private $con;

    public function __construct($banco){
        $this->con = mysqli_connect('localhost', 'root', 'a2ngei9Mxh', $banco);
    }

    private function fillField($motivoMorte){
        $obj = new MotivoMorte();

        $obj->setId($motivoMorte->tab_codigo_causa_morte);
        $obj->setDescricao($motivoMorte->tab_descricao_causa_morte);
        $obj->setIncluidoEm($motivoMorte->tab_causa_morte_incluido_em);
        $obj->setIncluidoPor($motivoMorte->tab_causa_morte_incluido_por);
        $obj->setAlteradoEm($motivoMorte->tab_causa_morte_alterado_em);
        $obj->setAlteradoPor($motivoMorte->tab_causa_morte_alterado_por);
        $obj->setLixeira($motivoMorte->tab_registro_lixeira_causa_morte);
        $obj->setLixeiraEm($motivoMorte->tab_causa_morte_lixeira_em);
        $obj->setLixeiraPor($motivoMorte->tab_causa_morte_lixeira_por);

        return $obj;
    }

    private function fillFields($arrayMotivoMorte){
        $a = [];

        foreach($arrayMotivoMorte as $motivoMorte){
            $obj = new MotivoMorte();

            $obj->setId($motivoMorte->tab_codigo_causa_morte);
            $obj->setDescricao($motivoMorte->tab_descricao_causa_morte);
            $obj->setIncluidoEm($motivoMorte->tab_causa_morte_incluido_em);
            $obj->setIncluidoPor($motivoMorte->tab_causa_morte_incluido_por);
            $obj->setAlteradoEm($motivoMorte->tab_causa_morte_alterado_em);
            $obj->setAlteradoPor($motivoMorte->tab_causa_morte_alterado_por);
            $obj->setLixeira($motivoMorte->tab_registro_lixeira_causa_morte);
            $obj->setLixeiraEm($motivoMorte->tab_causa_morte_lixeira_em);
            $obj->setLixeiraPor($motivoMorte->tab_causa_morte_lixeira_por);

            array_push($a, $obj);
        }
        
        return $a;
    }

    public function getMotivoMorte(){
        $a = [];
        $sql = "SELECT * FROM tabela_causa_morte WHERE tab_registro_lixeira_causa_morte = 0";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }

        return $this->fillFields($a);
    }

    public function getMotivoMorteById($id){
        $sql = "SELECT * FROM tabela_causa_morte WHERE tab_codigo_causa_morte = $id";
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        return $this->fillField(mysqli_fetch_object($r));
    }
}