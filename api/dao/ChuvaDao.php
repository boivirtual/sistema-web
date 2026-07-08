<?php
include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
class ChuvaDao{

    private $con;

    public function __construct($banco){
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    public function fillFields($param){
        $aObj = [];

        foreach($param as $val){
            $obj = new Chuva();
            $obj->setId($val->tbl_chuva_id);
            $obj->setData($val->tbl_chuva_data);
            $obj->getLocal()->setId($val->tbl_pessoa_id);
            $obj->getLocal()->setClasse($val->tbl_pessoa_classe);
            $obj->getLocal()->setCpfCnpj($val->tbl_pessoa_cpf_cnpj);
            $obj->getLocal()->setTipo($val->tbl_pessoa_tipo_pessoa);
            $obj->getLocal()->setInscEstadual($val->tbl_pessoa_insc_estadual);
            $obj->getLocal()->setInscMunicipal($val->tbl_pessoa_insc_estadual);
            $obj->getLocal()->setNome($val->tbl_pessoa_nome);
            $obj->getLocal()->setContato($val->tbl_pessoa_contato);
            $obj->getLocal()->setCargoContato($val->tbl_pessoa_cargo_contato);
            $obj->getLocal()->setDdd($val->tbl_pessoa_ddd);
            $obj->getLocal()->setTelefone($val->tbl_pessoa_telefone);
            $obj->getLocal()->setEmail($val->tbl_pessoa_email);
            $obj->getLocal()->getEndereco()->setCep($val->tbl_pessoa_cep);
            $obj->getLocal()->getEndereco()->setEndereco($val->tbl_pessoa_endereco);
            $obj->getLocal()->getEndereco()->setNumero($val->tbl_pessoa_numero);
            $obj->getLocal()->getEndereco()->setComplemento($val->tbl_pessoa_complemento);
            $obj->getLocal()->getEndereco()->setBairro($val->tbl_pessoa_bairro);
            $obj->getLocal()->getEndereco()->setCidade(utf8_encode($val->tbl_pessoa_municipio));
            $obj->getLocal()->getEndereco()->setEstado($val->tbl_pessoa_estado);
            $obj->getLocal()->setLixeira($val->tbl_pessoa_lixeira);
            $obj->getLocal()->setLixeiraEm($val->tbl_pessoa_lixeira_em);
            $obj->getLocal()->setLixeiraPor($val->tbl_pessoa_lixeira_por);
            $obj->getLocal()->setAlteradoEm($val->tbl_pessoa_alterado_em);
            $obj->getLocal()->setAlteradoPor($val->tbl_pessoa_alterado_por);
            $obj->getLocal()->setObservacao($val->tbl_pessoa_observacao);
            $obj->getLocal()->setAtivo($val->tbl_pessoa_ativo);
            $obj->setVolume($val->tbl_chuva_volume_chuva);
            $obj->setObservacao($val->tbl_chuva_observacao);
            $obj->setIncluidoEm($val->tbl_chuva_incluido_em);
            $obj->setIncluidoPor($val->tbl_chuva_incluido_por);
            $obj->setAlteradoEm($val->tbl_chuva_alterado_em);
            $obj->setAlteradoPor($val->tbl_chuva_alterado_por);
            $obj->setLixeira($val->tbl_chuva_lixeira);
            $obj->setLixeiraEm($val->tbl_chuva_lixeira_em);
            $obj->setLixeiraPor($val->tbl_chuva_lixeira_por);

            array_push($aObj, $obj);
        }

        return $aObj;
    }

    public function fillField($param){
        $obj = new Chuva();
        $obj->setId($param->tbl_chuva_id);
        $obj->setData($param->tbl_chuva_data);
        $obj->getLocal()->setId($param->tbl_pessoa_id);
        $obj->getLocal()->setClasse($param->tbl_pessoa_classe);
        $obj->getLocal()->setCpfCnpj($param->tbl_pessoa_cpf_cnpj);
        $obj->getLocal()->setTipo($param->tbl_pessoa_tipo_pessoa);
        $obj->getLocal()->setInscEstadual($param->tbl_pessoa_insc_estadual);
        $obj->getLocal()->setInscMunicipal($param->tbl_pessoa_insc_estadual);
        $obj->getLocal()->setNome($param->tbl_pessoa_nome);
        $obj->getLocal()->setContato($param->tbl_pessoa_contato);
        $obj->getLocal()->setCargoContato($param->tbl_pessoa_cargo_contato);
        $obj->getLocal()->setDdd($param->tbl_pessoa_ddd);
        $obj->getLocal()->setTelefone($param->tbl_pessoa_telefone);
        $obj->getLocal()->setEmail($param->tbl_pessoa_email);
        $obj->getLocal()->getEndereco()->setCep($param->tbl_pessoa_cep);
        $obj->getLocal()->getEndereco()->setEndereco($param->tbl_pessoa_endereco);
        $obj->getLocal()->getEndereco()->setNumero($param->tbl_pessoa_numero);
        $obj->getLocal()->getEndereco()->setComplemento($param->tbl_pessoa_complemento);
        $obj->getLocal()->getEndereco()->setBairro($param->tbl_pessoa_bairro);
        $obj->getLocal()->getEndereco()->setCidade(utf8_encode($param->tbl_pessoa_municipio));
        $obj->getLocal()->getEndereco()->setEstado($param->tbl_pessoa_estado);
        $obj->getLocal()->setLixeira($param->tbl_pessoa_lixeira);
        $obj->getLocal()->setLixeiraEm($param->tbl_pessoa_lixeira_em);
        $obj->getLocal()->setLixeiraPor($param->tbl_pessoa_lixeira_por);
        $obj->getLocal()->setAlteradoEm($param->tbl_pessoa_alterado_em);
        $obj->getLocal()->setAlteradoPor($param->tbl_pessoa_alterado_por);
        $obj->getLocal()->setObservacao($param->tbl_pessoa_observacao);
        $obj->getLocal()->setAtivo($param->tbl_pessoa_ativo);
        $obj->setVolume($param->tbl_chuva_volume_chuva);
        $obj->setObservacao($param->tbl_chuva_observacao);
        $obj->setIncluidoEm($param->tbl_chuva_incluido_em);
        $obj->setIncluidoPor($param->tbl_chuva_incluido_por);
        $obj->setAlteradoEm($param->tbl_chuva_alterado_em);
        $obj->setAlteradoPor($param->tbl_chuva_alterado_por);
        $obj->setLixeira($param->tbl_chuva_lixeira);
        $obj->setLixeiraEm($param->tbl_chuva_lixeira_em);
        $obj->setLixeiraPor($param->tbl_chuva_lixeira_por);

        return $obj;
    }

    public function getVolume($d, $l){
        $sql = "SELECT * FROM tbl_chuva JOIN tbl_pessoa ON tbl_chuva_local = tbl_pessoa_id WHERE tbl_chuva_data = '$d' AND tbl_chuva_local = '$l'";
        mysqli_set_charset($this->con, 'utf8');
        $r = mysqli_query($this->con, $sql);
        if(mysqli_num_rows($r) > 0){
            return $this->fillField(mysqli_fetch_object($r));
        }else{
            return [
                "success" => true,
                "message" => "Não foram encontrados registros para essa data/local!"
            ];
        }
    }

    public function getDias($l, $a){
        $sql = "SELECT * FROM tbl_chuva JOIN tbl_pessoa ON tbl_chuva_local = tbl_pessoa_id WHERE year(tbl_chuva_data) = '$a' AND tbl_chuva_local='$l'";
        $r = mysqli_query($this->con, $sql);
        if(mysqli_num_rows($r) > 1){
            $a = [];
            while($v = mysqli_fetch_object($r)){
                array_push($a, $v);
            }
            return $this->fillFields($a);
        }elseif(mysqli_num_rows($r) == 1){
            return $this->fillField(mysqli_fetch_object($r));
        }else{
            return [
                "error" => true,
                "message" => "Volume de chuva não cadastrado para esse mês."
            ];
        }
    }

    public function createChuva($chuva){
        $data = date("Y-m-d H:i:s");

        $sql = ("DELETE FROM tbl_chuva WHERE tbl_chuva_data='{$chuva["data_chuva"]}' AND tbl_chuva_local='{$chuva["codigo_local_chuva"]}'");
        mysqli_query($this->con, $sql);

        $sql = "INSERT INTO tbl_chuva (
	    	tbl_chuva_data,
	    	tbl_chuva_local,
			tbl_chuva_volume_chuva,    	
			tbl_chuva_observacao,
			tbl_chuva_incluido_em,
			tbl_chuva_incluido_por,
			tbl_chuva_alterado_em,
			tbl_chuva_alterado_por,
			tbl_chuva_lixeira,
			tbl_chuva_lixeira_em,
			tbl_chuva_lixeira_por
	        ) VALUES (
	        '{$chuva["data_chuva"]}',
	        '{$chuva["codigo_local_chuva"]}',
			'{$chuva["volume_chuva"]}',
			null,
			'$data',
			'{$chuva["user"]}',
			null,
			null,
			0,
			null,
			null
		)";

        mysqli_query($this->con, $sql);
        $msg = mysqli_error($this->con);
        if($msg){
            return [
                "error" => true,
                "message" => "Ocorreu um erro ao gravar o registro! {$msg}"
            ];
        }else{
            return [
                "error" => false,
                "message" => "Volume registrado com sucesso!"
            ];
        }
    }
}