<?php

class UsuarioDao{
    
    private $con;
    private $data;

    public function __construct(){
        require __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, 'acesso_boi_virtual');
        $this->data = date("Y-m-d H:i:s");
    }

    public function fillFields($param){
        $aObj = [];

        foreach($param as $val){
            $obj = new Usuario();
            $obj->setId($val->id_usuario);
            $obj->setCpf($val->cpf_usuario);
            $obj->setCnpj($val->cnpj_cpf_empresa_usuario);
            $obj->setEmail($val->email_usuario);
            $obj->setNome($val->nome_usuario);
            $obj->setGrupo($val->grupo_usuario);
            $obj->setSenha($val->senha_usuario);
            $obj->setDataNascimento($val->data_nascimento_usuario);
            $obj->setIdade($val->idade_usuario);
            $obj->setSexo($val->sexo_usuario);
            $obj->setObservacao($val->observacao_usuario);
            $obj->setIncluidoEm($val->incluido_em_usuario);
            $obj->setIncluidoPor($val->incluido_por_usuario);
            $obj->setAlteradoEm($val->alterado_em_usuario);
            $obj->setAlteradoPor($val->alterado_por_usuario);
            $obj->setLixeira($val->lixeira_usuario);
            $obj->setLixeiraEm($val->lixeira_em_usuario);
            $obj->setLixeiraPor($val->lixeira_por_usuario);
            $obj->setSituacao($val->situacao_usuario);
            $obj->setAutorizaSenha($val->autoriza_redefinir_senha_usuario);
            $obj->setFoto($val->foto_usuario);
            $obj->setLocal($val->local_usuario);
            $obj->endereco->setEndereco($val->endereco_usuario);
            $obj->endereco->setNumero($val->numero_usuario);
            $obj->endereco->setComplemento($val->complemento_usuario);
            $obj->endereco->setBairro($val->bairro_usuario);
            $obj->endereco->setCep($val->cep_usuario);
            $obj->endereco->setCidade($val->cidade_usuario);
            $obj->endereco->setEstado($val->estado_usuario);

            array_push($aObj, $obj);
        }

        return $aObj;
    }
    
    public function fillField($param){
        $obj = new Usuario();
        $obj->setId($param->id_usuario);
        $obj->setCpf($param->cpf_usuario);
        $obj->setCnpj($param->cnpj_cpf_empresa_usuario);
        $obj->setEmail($param->email_usuario);
        $obj->setNome($param->nome_usuario);
        $obj->setGrupo($param->grupo_usuario);
        $obj->setSenha($param->senha_usuario);
        $obj->setDataNascimento($param->data_nascimento_usuario);
        $obj->setIdade($param->idade_usuario);
        $obj->setSexo($param->sexo_usuario);
        $obj->setObservacao($param->observacao_usuario);
        $obj->setIncluidoEm($param->incluido_em_usuario);
        $obj->setIncluidoPor($param->incluido_por_usuario);
        $obj->setAlteradoEm($param->alterado_em_usuario);
        $obj->setAlteradoPor($param->alterado_por_usuario);
        $obj->setLixeira($param->lixeira_usuario);
        $obj->setLixeiraEm($param->lixeira_em_usuario);
        $obj->setLixeiraPor($param->lixeira_por_usuario);
        $obj->setSituacao($param->situacao_usuario);
        $obj->setAutorizaSenha($param->autoriza_redefinir_senha_usuario);
        $obj->setFoto($param->foto_usuario);

        //$obj->setLocal($param->local_usuario);

        $valorDoBanco = $param->local_usuario; 
        $nomeDoBancoCliente = $param->cnpj_cpf_empresa_usuario; // O nome do banco é o CNPJ

        if (empty($valorDoBanco)) {
            $obj->setLocal([]); 
        } else {
            $valorLimpo = preg_replace('/\s+/', '', $valorDoBanco);
            $codigosArray = explode(',', $valorLimpo);

            try {
                // 1. MUDANÇA DE BANCO: Aponta para o banco do cliente (CNPJ)
                if (mysqli_select_db($this->con, $nomeDoBancoCliente)) {
                        
                    $codigosParaSql = "'" . implode("','", $codigosArray) . "'";
                    $sql = "SELECT tbl_pessoa_id, tbl_pessoa_nome 
                            FROM tbl_pessoa 
                            WHERE tbl_pessoa_id IN ($codigosParaSql)";

                    $res = mysqli_query($this->con, $sql);
                    $listaFinal = [];

                    if ($res && mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $listaFinal[] = [
                                "id" => (string)$row['tbl_pessoa_id'],
                                "nome" => utf8_encode($row['tbl_pessoa_nome'])
                            ];
                        }
                    }

                    // 2. VOLTA PARA O BANCO PRINCIPAL (Opcional, mas boa prática)
                    mysqli_select_db($this->con, 'acesso_boi_virtual');

                    if (!empty($listaFinal)) {
                        $obj->setLocal($listaFinal);
                    } else {
                        $obj->setLocal($codigosArray);
                    }
                } else {
                    // Se não conseguir abrir o banco do CNPJ, retorna só os códigos
                    $obj->setLocal($codigosArray);
                }

            } catch (Exception $e) {
                $obj->setLocal($codigosArray);
            }
        }

        $obj->getEndereco()->setEndereco($param->endereco_usuario);
        $obj->getEndereco()->setNumero($param->numero_usuario);
        $obj->getEndereco()->setComplemento($param->complemento_usuario);
        $obj->getEndereco()->setBairro($param->bairro_usuario);
        $obj->getEndereco()->setCep($param->cep_usuario);
        $obj->getEndereco()->setCidade($param->cidade_usuario);
        $obj->getEndereco()->setEstado($param->estado_usuario);

        return $obj;
    }

    /*public function getUser($user, $pass){
		$sql = "SELECT * FROM usuario WHERE (cpf_usuario = '$user' OR email_usuario = '$user')";
        $uObj = mysqli_query($this->con, $sql);
        if(mysqli_num_rows($uObj) > 0){
            $sql = "SELECT * FROM usuario WHERE (cpf_usuario = '$user' OR email_usuario = '$user') AND senha_usuario = '$pass'";
            $uObj = mysqli_query($this->con, $sql);
            if(mysqli_num_rows($uObj) > 0){
                $userObj = mysqli_fetch_object($uObj);
                $user = $this->fillField($userObj);
                if($user->getSituacao() == 'D'){
                    return [
                        "error" => true,
                        "message" => "Usuário desativado"
                    ];
                }
                return $user;
            }else{
                return [
                    "error" => true,
                    "message" => "Senha incorreta"
                ];
            }
        }else{
            return [
                "error" => true,
                "message" => "Usuário não encontrado"
            ];
        }
	}*/

    public function getUser($user, $pass){
        // Busca simples do usuário
        $sql = "SELECT * FROM usuario WHERE (cpf_usuario = '$user' OR email_usuario = '$user')";
        $result = mysqli_query($this->con, $sql);

        if(mysqli_num_rows($result) > 0){
            $userObj = mysqli_fetch_object($result);

            if($userObj->senha_usuario === $pass){
                // CHAMADA DO MÉTODO QUE VAI MONTAR O OBJETO COM OS NOMES
                $user = $this->fillField($userObj);
                
                if($user->getSituacao() == 'D') return ["error" => true, "message" => "Usuário desativado"];
                return $user;
            } else {
                return ["error" => true, "message" => "Senha incorreta"];
            }
        }
        return ["error" => true, "message" => "Usuário não encontrado"];
    }    

    public function editUserPassword($u){
        $user = new Usuario();
        $user->setId($u["codigo_usuario"]);
        $user->setSenha($u["senha_cad"]);
        $sql = "UPDATE usuario SET senha_usuario = '{$user->getSenha()}' WHERE id_usuario = '{$user->getId()}'";
        
        mysqli_query($this->con, $sql);

        return mysqli_error($this->con);
    }

    public function editUser($u){
        $user = new Usuario();
        $user->setId($u["codigo_usuario"]);
        $user->setCpf(preg_replace("/[^0-9]/", "", $u["cpf_usuario"]));
        $user->setEmail($u["email_usuario"]);
        $user->setNome($u["nome_usuario"]);
        $user->getEndereco()->setEndereco($u["endereco_usuario"]);
        $user->getEndereco()->setNumero($u["numero_usuario"]);
        $user->getEndereco()->setCep($u["cep_usuario"]);
        $user->getEndereco()->setComplemento($u["complemento_usuario"]);
        $user->getEndereco()->setBairro($u["bairro_usuario"]);
        $user->getEndereco()->setEstado($u["estado_usuario"]);
        $user->getEndereco()->setCidade($u["cidade_usuario"]);

        $sql = "UPDATE usuario SET nome_usuario='{$user->getNome()}',
                                cpf_usuario='{$user->getCpf()}',
                                email_usuario='{$user->getEmail()}',
                                alterado_em_usuario='{$this->data}',
                                alterado_por_usuario='{$user->getNome()}',
                                endereco_usuario='{$user->getEndereco()->getEndereco()}',
                                numero_usuario='{$user->getEndereco()->getNumero()}',
                                complemento_usuario='{$user->getEndereco()->getComplemento()}',
                                bairro_usuario='{$user->getEndereco()->getBairro()}',
                                cep_usuario='{$user->getEndereco()->getCep()}',
                                cidade_usuario='{$user->getEndereco()->getCidade()}',
                                estado_usuario='{$user->getEndereco()->getEstado()}'
                WHERE id_usuario='{$user->getId()}'";
        
        mysqli_query($this->con, $sql);

        return mysqli_error($this->con); 
    }

    public function allowPassChange($usuario){
        $sql = "UPDATE usuario SET autoriza_redefinir_senha_usuario = 'S' WHERE cpf_usuario = '{$usuario}' OR email_usuario = '{$usuario}'";
        $o = mysqli_query($this->con, $sql);
        $e = mysqli_error($this->con);
        if(!$o){
            return [
                "error" => true,
                "message" => "Erro ao registrar a solicitação: {$e}"
            ];
        }

        return [
            "error" => false,
            "message" => ""
        ];
    }
    
    public function getUserByCpf($usuario){
        $sql = "SELECT * FROM usuario WHERE cpf_usuario = '{$usuario}' OR email_usuario = '{$usuario}'";
        $uObj = mysqli_query($this->con, $sql);
        if(mysqli_num_rows($uObj) > 0){
            return $this->fillField(mysqli_fetch_object($uObj));
        }
        
        return [
            "error" => true,
            "message" => "CPF ou E-mail não cadastrado!"
        ];
    }

    public function getUserById($id){
        return $this->fillField(mysqli_fetch_object(mysqli_query($this->con, "SELECT * FROM usuario WHERE id_usuario = {$id}")));
    }
}