<?php
class MapaGadoService{

    public function getColor($modulo){
        $cores = ['#7FFFD4', '#66CDAA', '#40E0D0', '#00FF7F', '#48D1CC', '#3CB371', '#00CED1', '#2E8B57'];

        if($modulo != 999){
            return $cores[($modulo - 1) % 8];
        }

        return '#FFFFFF';
    }

    public function getNode($b, $f, $m, $t, $p, $c){
        return [
            "id"            => "{$p->getId()}",
            "descricao"     => "{$p->getDescricao()}",
            "color"         => "$c",
            "modulo"        => [
                "id"        => "{$p->getModulo()->getId()}",
                "descricao" => "{$p->getModulo()->getDescricao()}"
            ],
            "capim"         => [
                "id"        => "{$p->getCapim()->getId()}",
                "descricao" => "{$p->getCapim()->getDescricao()}"
            ],
            "qtdBezerro"    => "{$b}",
            "qtdFemea"      => "{$f}",
            "qtdMacho"      => "{$m}",
            "qtdTotal"      => "{$t}"
        ];
    }

    public function animalInfoIndividual($arrayAnimal, $pasto){
        $bezerro = 0;
        $femea = 0;
        $macho = 0;
        if($arrayAnimal != null){
            foreach($arrayAnimal as $animal){
                if($animal->getCategoria()->getId() == 1){
                    $bezerro++;
                }elseif($animal->getSexo() == "F"){
                    $femea++;
                }elseif($animal->getSexo() == "M"){
                    $macho++;
                }
            }
        }

        return $this->getNode($bezerro, $femea, $macho, ($bezerro+$femea+$macho), $pasto, $this->getColor($pasto->getModulo()->getId()));
    }

    public function animalInfoLote($arrayAnimal, $pasto){
        $animalPastoService = new AnimalPastoService();
        $bezerro = 0;
        $femea = 0;
        $macho = 0;

        if($arrayAnimal != null){
            foreach($arrayAnimal as $animal){
                $i = $animalPastoService->getAge($animal);
                if(($i != "e") && ($i >= 0 && $i <= 7)){
                    $bezerro++;
                }elseif($i != "e" && $animal->getSexo() == "F"){
                    $femea++;
                }elseif($i != "e" && $animal->getSexo() == "M"){
                    $macho++;
                }
            }
        }

        return $this->getNode($bezerro, $femea, $macho, ($bezerro+$femea+$macho), $pasto, $this->getColor($pasto->getModulo()->getId()));
    }

    public function getMapaIndividual($arrayPasto, $db, $numRow, $page){
        $a = [];
        $animalPastoService = new AnimalPastoService();

        foreach($arrayPasto as $pasto){
            $arrayAnimal = $animalPastoService->getAnimalPastoByPasto($pasto->getId(), $db);
            array_push($a, $this->animalInfoIndividual($arrayAnimal, $pasto));
        }

        return [
            "data" => $a,
            "rowCount" => $numRow,
            "currentPage" => (int) $page,
            "nextPage" => $page + 1
        ];
    }

    public function getMapaLote($arrayPasto, $db, $numRow, $page){
        $a = [];
        $animalPastoService = new AnimalPastoService();

        foreach($arrayPasto as $pasto){
            $arrayAnimal = $animalPastoService->getAnimalPastoByPasto($pasto->getId(), $db);
            array_push($a, $this->animalInfoLote($arrayAnimal, $pasto));
        }

        return [
            "data" => $a,
            "rowCount" => $numRow,
            "previousPage" => (int) $page,
            "nextPage" => $page + 1
        ];
    }

    public function getMapaByLocal($local, $page, $db){
        $pastoService = new PastoService();
        $empresaService = new EmpresaService();
        
        $empresa = $empresaService->getEmpresaByCnpj($db);

        $arrayPasto = $pastoService->getPastoByLocal($local, $page, $db);
        $rowCount = $pastoService->getPastoRowCountByLocal($local, $db);

        if($empresa->getControlePesagem() == 'I'){
            return $this->getMapaIndividual($arrayPasto, $db, $rowCount, $page);
        }
        
        return $this->getMapaLote($arrayPasto, $db, $rowCount, $page);

    }

    public function transferAll($pastoIncluir, $pastoRemover, $userId, $fazenda, $db){
        /* $nutricaoService = new NutricaoService();
        $r = $nutricaoService->transferNutricao($pastoIncluir, $pastoRemover, $db);

        if($r["error"]){
            return $r; 
        } */

        $userService = new UsuarioService();
        $user = $userService->getUserById($userId);

        $pastoService = new PastoService();
        $pastoIncluir = $pastoService->getPastoById($pastoIncluir, $db);
        $pastoRemover = $pastoService->getPastoById($pastoRemover, $db);

        if($pastoIncluir->getObservacao() != ''){
            $r = $pastoService->transferObs('null', $pastoIncluir, $user, $db);
        }else{
            $r = $pastoService->transferObs($pastoRemover->getObservacao(), $pastoIncluir, $user, $db);
        }

        if($r["error"]){
            return $r;
        }

        $r = $pastoService->transferObs('null', $pastoRemover, $user, $db);

        if($r["error"]){
            return $r;
        }

        $animalPastoService = new AnimalPastoService();
        $r = $animalPastoService->transferAll($pastoIncluir, $pastoRemover, $user, $fazenda, $db);

        return $r;
    }

}