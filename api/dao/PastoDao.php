<?php
class PastoDao{

    private $con;
    private $systemDateHour;

    public function __construct($banco){
        include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        $this->systemDateHour = date('Y-m-d H:i:s');
    }

    private function fillField($pasto){
        $obj = new Pasto();
        $obj->setId($pasto->tbl_pasto_id);

        $obj->getModulo()->setId($pasto->tbl_modulo_id);
        $obj->getModulo()->setDescricao($pasto->tbl_modulo_descricao);
        $obj->getModulo()->setIncluidoEm($pasto->tbl_modulo_incluido_em);
        $obj->getModulo()->setIncluidoPor($pasto->tbl_modulo_incluido_por);
        $obj->getModulo()->setAlteradoEm($pasto->tbl_modulo_alterado_em);
        $obj->getModulo()->setAlteradoPor($pasto->tbl_modulo_alterado_por);
        $obj->getModulo()->setLixeira($pasto->tbl_modulo_lixeira);
        $obj->getModulo()->setLixeiraEm($pasto->tbl_modulo_lixeira_em);
        $obj->getModulo()->setLixeiraPor($pasto->tbl_modulo_lixeira_por);

        $obj->getLocal()->setId($pasto->tbl_pessoa_id);
        $obj->getLocal()->setClasse($pasto->tbl_pessoa_classe);
        $obj->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
        $obj->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
        $obj->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
        $obj->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
        $obj->getLocal()->setNome($pasto->tbl_pessoa_nome);
        $obj->getLocal()->setContato($pasto->tbl_pessoa_contato);
        $obj->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
        $obj->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
        $obj->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
        $obj->getLocal()->setEmail($pasto->tbl_pessoa_email);
        $obj->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
        $obj->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
        $obj->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
        $obj->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
        $obj->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
        $obj->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
        $obj->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
        $obj->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
        $obj->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
        $obj->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
        $obj->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
        $obj->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
        $obj->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
        $obj->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
        $obj->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
        $obj->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

        $obj->setDescricao($pasto->tbl_pasto_descricao);
        $obj->setLatitude($pasto->tbl_pasto_latitude);
        $obj->setLongitude($pasto->tbl_pasto_longitude);
        $obj->setArea($pasto->tbl_pasto_area);

        $obj->getCapim()->setId($pasto->tbl_tipo_capim_id);
        $obj->getCapim()->setDescricao($pasto->tbl_tipo_capim_descricao);
        $obj->getCapim()->setIncluidoEm($pasto->tbl_tipo_capim_incluido_em);
        $obj->getCapim()->setIncluidoPor($pasto->tbl_tipo_capim_incluido_por);
        $obj->getCapim()->setAlteradoEm($pasto->tbl_tipo_capim_alterado_em);
        $obj->getCapim()->setAlteradoPor($pasto->tbl_tipo_capim_incluido_por);
        $obj->getCapim()->setLixeira($pasto->tbl_tipo_capim_lixeira);
        $obj->getCapim()->setLixeiraEm($pasto->tbl_tipo_capim_lixeira_em);
        $obj->getCapim()->setLixeiraPor($pasto->tbl_tipo_capim_lixeira_por);
        
        $obj->setArrayCategoria($pasto->tbl_pasto_array_categoria);
        $obj->setObservacao($pasto->tbl_pasto_descricao_lote);
        $obj->setCurral($pasto->tbl_pasto_tipo_curral);
        $obj->setIncluidoEm($pasto->tbl_pasto_incluido_em);
        $obj->setIncluidoPor($pasto->tbl_pasto_incluido_por);
        $obj->setAlteradoEm($pasto->tbl_pasto_alterado_em);
        $obj->setAlteradoPor($pasto->tbl_pasto_alterado_por);
        $obj->setLixeira($pasto->tbl_pasto_lixeira);
        $obj->setLixeiraEm($pasto->tbl_pasto_lixeira_em);
        $obj->setLixeiraPor($pasto->tbl_pasto_lixeira_por);

        return $obj;
    }

    public function fillFields($objPasto){
        $aObj = [];

        foreach($objPasto as $pasto){
            $obj = new Pasto();
            $obj->setId($pasto->tbl_pasto_id);

            $obj->getModulo()->setId($pasto->tbl_modulo_id);
            $obj->getModulo()->setDescricao($pasto->tbl_modulo_descricao);
            $obj->getModulo()->setIncluidoEm($pasto->tbl_modulo_incluido_em);
            $obj->getModulo()->setIncluidoPor($pasto->tbl_modulo_incluido_por);
            $obj->getModulo()->setAlteradoEm($pasto->tbl_modulo_alterado_em);
            $obj->getModulo()->setAlteradoPor($pasto->tbl_modulo_alterado_por);
            $obj->getModulo()->setLixeira($pasto->tbl_modulo_lixeira);
            $obj->getModulo()->setLixeiraEm($pasto->tbl_modulo_lixeira_em);
            $obj->getModulo()->setLixeiraPor($pasto->tbl_modulo_lixeira_por);

            $obj->getLocal()->setId($pasto->tbl_pessoa_id);
            $obj->getLocal()->setClasse($pasto->tbl_pessoa_classe);
            $obj->getLocal()->setCpfCnpj($pasto->tbl_pessoa_cpf_cnpj);
            $obj->getLocal()->setTipo($pasto->tbl_pessoa_tipo_pessoa);
            $obj->getLocal()->setInscEstadual($pasto->tbl_pessoa_insc_estadual);
            $obj->getLocal()->setInscMunicipal($pasto->tbl_pessoa_insc_municipal);
            $obj->getLocal()->setNome($pasto->tbl_pessoa_nome);
            $obj->getLocal()->setContato($pasto->tbl_pessoa_contato);
            $obj->getLocal()->setCargoContato($pasto->tbl_pessoa_cargo_contato);
            $obj->getLocal()->setDdd($pasto->tbl_pessoa_ddd);
            $obj->getLocal()->setTelefone($pasto->tbl_pessoa_telefone);
            $obj->getLocal()->setEmail($pasto->tbl_pessoa_email);
            $obj->getLocal()->getEndereco()->setCep($pasto->tbl_pessoa_cep);
            $obj->getLocal()->getEndereco()->setEndereco($pasto->tbl_pessoa_endereco);
            $obj->getLocal()->getEndereco()->setNumero($pasto->tbl_pessoa_numero);
            $obj->getLocal()->getEndereco()->setComplemento($pasto->tbl_pessoa_complemento);
            $obj->getLocal()->getEndereco()->setBairro($pasto->tbl_pessoa_bairro);
            $obj->getLocal()->getEndereco()->setCidade($pasto->tbl_pessoa_municipio);
            $obj->getLocal()->getEndereco()->setEstado($pasto->tbl_pessoa_estado);
            $obj->getLocal()->setIncluidoEm($pasto->tbl_pessoa_incluido_em);
            $obj->getLocal()->setIncluidoPor($pasto->tbl_pessoa_incluido_por);
            $obj->getLocal()->setAlteradoEm($pasto->tbl_pessoa_alterado_em);
            $obj->getLocal()->setAlteradoPor($pasto->tbl_pessoa_alterado_por);
            $obj->getLocal()->setLixeira($pasto->tbl_pessoa_lixeira);
            $obj->getLocal()->setLixeiraEm($pasto->tbl_pessoa_lixeira_em);
            $obj->getLocal()->setLixeiraPor($pasto->tbl_pessoa_lixeira_por);
            $obj->getLocal()->setObservacao($pasto->tbl_pessoa_observacao);
            $obj->getLocal()->setAtivo($pasto->tbl_pessoa_ativo);

            $obj->setDescricao($pasto->tbl_pasto_descricao);
            $obj->setLatitude($pasto->tbl_pasto_latitude);
            $obj->setLongitude($pasto->tbl_pasto_longitude);
            $obj->setArea($pasto->tbl_pasto_area);

            $obj->getCapim()->setId($pasto->tbl_tipo_capim_id);
            $obj->getCapim()->setDescricao($pasto->tbl_tipo_capim_descricao);
            $obj->getCapim()->setIncluidoEm($pasto->tbl_tipo_capim_incluido_em);
            $obj->getCapim()->setIncluidoPor($pasto->tbl_tipo_capim_incluido_por);
            $obj->getCapim()->setAlteradoEm($pasto->tbl_tipo_capim_alterado_em);
            $obj->getCapim()->setAlteradoPor($pasto->tbl_tipo_capim_incluido_por);
            $obj->getCapim()->setLixeira($pasto->tbl_tipo_capim_lixeira);
            $obj->getCapim()->setLixeiraEm($pasto->tbl_tipo_capim_lixeira_em);
            $obj->getCapim()->setLixeiraPor($pasto->tbl_tipo_capim_lixeira_por);
            
            $obj->setArrayCategoria($pasto->tbl_pasto_array_categoria);
            $obj->setObservacao($pasto->tbl_pasto_descricao_lote);
            $obj->setCurral($pasto->tbl_pasto_tipo_curral);
            $obj->setIncluidoEm($pasto->tbl_pasto_incluido_em);
            $obj->setIncluidoPor($pasto->tbl_pasto_incluido_por);
            $obj->setAlteradoEm($pasto->tbl_pasto_alterado_em);
            $obj->setAlteradoPor($pasto->tbl_pasto_alterado_por);
            $obj->setLixeira($pasto->tbl_pasto_lixeira);
            $obj->setLixeiraEm($pasto->tbl_pasto_lixeira_em);
            $obj->setLixeiraPor($pasto->tbl_pasto_lixeira_por);

            array_push($aObj, $obj);
        }

        return $aObj;
    }

    public function getPastoById($id){
        $sql = "SELECT * 
        FROM tbl_pasto
        JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
        LEFT OUTER JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
        JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
        WHERE tbl_pasto_id = '$id' AND tbl_pasto_lixeira = 0";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);

        return $this->fillField(mysqli_fetch_object($r));
    }

    public function getPastoByLocal($local, $offset){
        $a = [];

        $sql = "SELECT *
        FROM tbl_pasto
        JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
        LEFT OUTER JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
        JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
        WHERE tbl_pasto_codigo_local = '$local' AND tbl_pasto_lixeira = 0
        ORDER BY tbl_pasto_tipo_curral DESC, tbl_pasto_modulo ASC
        LIMIT 20 OFFSET {$offset}";

        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }

        return $this->fillFields($a);
    }

    public function getPasto($local){
        $a = [];

        $sql = "SELECT *
                FROM tbl_pasto
                JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
                LEFT OUTER JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
                JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
                WHERE tbl_pasto_codigo_local = '$local'
                ORDER BY tbl_pasto_tipo_curral DESC";
        
        mysqli_set_charset($this->con, "utf8");
        $r = mysqli_query($this->con, $sql);
        while($obj = mysqli_fetch_object($r)){
            array_push($a, $obj);
        }
        
        return $this->fillFields($a);
    }

    public function getPastoRowCountByLocal($local){
        $sql = "SELECT * FROM tbl_pasto
        JOIN tbl_modulo_pasto ON tbl_pasto_modulo = tbl_modulo_id
        LEFT OUTER JOIN tbl_tipo_capim ON tbl_pasto_tipo_capim = tbl_tipo_capim_id
        JOIN tbl_pessoa ON tbl_pasto_codigo_local = tbl_pessoa_id
        WHERE tbl_pasto_codigo_local = '$local' AND tbl_pasto_lixeira = 0
        ORDER BY tbl_pasto_tipo_curral DESC, tbl_pasto_modulo ASC";

        $r = mysqli_query($this->con, $sql);
        return mysqli_num_rows($r);
    }

    public function transferObs($obs, $pasto, $user){
        if($obs != 'null'){
            $sql = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = '{$obs}', 
            tbl_pasto_alterado_em = '{$this->systemDateHour}', 
            tbl_pasto_alterado_por = '{$user->getNome()}'
            WHERE tbl_pasto_id = $pasto";
        }else{
            $sql = "UPDATE tbl_pasto SET 
            tbl_pasto_descricao_lote = $obs, 
            tbl_pasto_alterado_em = '{$this->systemDateHour}', 
            tbl_pasto_alterado_por = '{$user->getNome()}'
            WHERE tbl_pasto_id = $pasto";
        }

        mysqli_set_charset($this->con, "utf8");
        mysqli_query($this->con, $sql);

        if(mysqli_error($this->con)){
            return [
                "error" => true,
                "message" => "Ocorreu um erro ao alterar a observação do pasto."
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
    }
}