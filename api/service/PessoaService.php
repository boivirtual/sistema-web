<?php
class PessoaService{

    public function getOption($pessoa){
        return [
            "id" => $pessoa->getId(),
            "descricao" => $pessoa->getNome()
        ];
    }

    public function getPessoaById($local, $db){
        $pessoaDao = new PessoaDao($db);
        return $pessoaDao->getPessoaById($local);
    }

    public function getPessoaByIds($arrayLocal, $db){
        $a = [];
        $pessoaDao = new PessoaDao($db);

        foreach($arrayLocal as $local){
            $objPessoa = $pessoaDao->getPessoaById($local);
            array_push($a, $this->getOption($objPessoa));
        }

        return $a;
    }

}