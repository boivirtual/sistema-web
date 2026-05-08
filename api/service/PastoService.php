<?php
class PastoService{

    public function getOption($pasto){
        return [
            "id" => $pasto->getId(),
            "descricao" => $pasto->getDescricao()
        ];
    }

    public function getPastoById($id, $db){
        $pastoDao = new PastoDao($db); 
        return $pastoDao->getPastoById($id);
    }

    public function getPastoByLocal($local, $page, $db){
        $pastoDao =  new PastoDao($db);
        $offset = ($page * 20) - 20;
        return $pastoDao->getPastoByLocal($local, $offset);
    }

    public function getPasto($local, $bd){
        if($local == "000000000" || $local == ""){
            return [
                "error" => true,
                "message" => "Informe o local."
            ];
        }

        $dao = new PastoDao($bd);

        return $dao->getPasto($local); 
    }

    public function getPastoRowCountByLocal($local, $db){
        $dao = new PastoDao($db);
        return $dao->getPastoRowCountByLocal($local);
    }

    public function transferObs($obs, $pasto, $user, $db){
        $dao = new PastoDao($db);
        return $dao->transferObs($obs, $pasto->getId(), $user);
    }

    public function list($local, $db){
        $a = [];
        $dao = new PastoDao($db);
        $arrayPasto = $dao->getPasto($local);

        foreach($arrayPasto as $pasto){
            array_push($a, $this->getOption($pasto));
        }

        return $a;
    }
}