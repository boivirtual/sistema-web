<?php
include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
class MunicipioDao{

    private $con;

    public function __construct($banco){
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    public function fillFields($param){
        $aObj = [];

        foreach($param as $val){
            $obj = new Municipio();
            $obj->setId($val->mun_codigo_id);
            $obj->setEstado($val->mun_estado);
            $obj->setNome(utf8_encode($val->mun_nome));
            $obj->setCodIBGE($val->mun_codigo_ibge);
            $obj->setLixeira($val->mun_registro_lixeira);

            array_push($aObj, $obj);
        }
        return $aObj;
    }

    public function fillField($param){
        $obj = new Municipio();
        $obj->setId($param->mun_codigo_id);
        $obj->setEstado($param->mun_estado);
        $obj->setNome($param->mun_nome);
        $obj->setCodIBGE($param->mun_codigo_ibge);
        $obj->setLixeira($param->mun_registro_lixeira);

        return $obj;
    }

    public function getMunicipio($uf){
        $sql = "SELECT * FROM tabela_municipios WHERE mun_estado = '$uf' AND mun_registro_lixeira = 0";
        $m = mysqli_query($this->con, $sql);
        if(mysqli_num_rows($m) > 0){
            $a = [];
            while($reg = mysqli_fetch_object($m)){
                array_push($a, $reg);
            }
            return $a;
        }else{
            return [
                "error" => true,
                "message" => "Não existem municípios para este estado"
            ];
        }
    }
}