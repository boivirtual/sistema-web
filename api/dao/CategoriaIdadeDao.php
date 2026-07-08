<?php
class CategoriaIdadeDao{

    private $con;

    public function __construct($banco){
        include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    private function fillField($categoriaIdade){
        $obj = new CategoriaIdade();

        $obj->setId($categoriaIdade->tab_codigo_categoria_idade);
        $obj->setIdadeDe($categoriaIdade->tab_categoria_idade_de);
        $obj->setIdadeAte($categoriaIdade->tab_categoria_idade_ate);
        $obj->setIncluidoEm($categoriaIdade->tab_incluido_categoria_idade_em);
        $obj->setIncluidoPor($categoriaIdade->tab_incluido_categoria_idade_por);
        $obj->setAlteradoEm($categoriaIdade->tab_alterado_categoria_idade_em);
        $obj->setAlteradoPor($categoriaIdade->tab_alterado_categoria_idade_por);
        $obj->setLixeira($categoriaIdade->tab_registro_lixeira_categoria_idade);
        $obj->setLixeiraEm($categoriaIdade->tab_lixeira_categoria_em);
        $obj->setLixeiraPor($categoriaIdade->tab_lixeira_categoria_idade_por);

        return $obj;
    }

    private function fillFields($arrayCategoriaIdade){
        $a = [];

        foreach($arrayCategoriaIdade as $categoriaIdade){
            $obj = new CategoriaIdade();

            $obj->setId($categoriaIdade->tab_codigo_categoria_idade);
            $obj->setIdadeDe($categoriaIdade->tab_categoria_idade_de);
            $obj->setIdadeAte($categoriaIdade->tab_categoria_idade_ate);
            $obj->setIncluidoEm($categoriaIdade->tab_incluido_categoria_idade_em);
            $obj->setIncluidoPor($categoriaIdade->tab_incluido_categoria_idade_por);
            $obj->setAlteradoEm($categoriaIdade->tab_alterado_categoria_idade_em);
            $obj->setAlteradoPor($categoriaIdade->tab_alterado_categoria_idade_por);
            $obj->setLixeira($categoriaIdade->tab_registro_lixeira_categoria_idade);
            $obj->setLixeiraEm($categoriaIdade->tab_lixeira_categoria_idade_em);
            $obj->setLixeiraPor($categoriaIdade->tab_lixeira_categoria_idade_por);

            array_push($a, $obj);
        }

        return $a;
    }

    public function getCategoria(){
        $a = [];

        $sql = "SELECT * FROM tabela_categoria_idade WHERE tab_registro_lixeira_categoria_idade = 0";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }
        
        return $this->fillFields($a);
    }
}