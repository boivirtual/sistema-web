<?php
class EmpresaService{

    public function getEmpresaByCnpj($cnpj){
        $dao = new EmpresaDao($cnpj);
        return $dao->getEmpresaByCnpj($cnpj);
    }
}