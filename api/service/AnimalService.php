<?php
class AnimalService{
    public function getAnimalInfo($animal, $db){
        $animalDao = new AnimalDao($db);
        $loteAbertoInfo = $animalDao->getLoteAbertoPorAnimal($animal->getId());
        $criterioApartacaoMae = $animalDao->getCriterioApartacaoMaeBezerroNaoDesmamado($animal);
        $bezerroApartacaoMae = $animalDao->getBezerroNaoDesmamadoEmPesagemAberta($animal);

        if($animal->getAlfa() == ''){  
            $animal_alfaNum = $animal->getNumerico();
        }
        else {
            $animal_alfaNum = "{$animal->getAlfa()}-{$animal->getNumerico()}";
        }

        return [
            "id" => $animal->getId(),
            "sexo" => $animal->getSexo(),
            "nascimento" => date("d/m/Y", strtotime($animal->getNascimento())),
            "raca" => $animal->getRaca()->getDescricao(),
            "pelagem" => $animal->getPelagem()->getDescricao(),
            "idMae" => $animal->getIdMae(),
            "brincoMae" => $animal->getNomeMae(),
            "ultimoPeso" => $animal->getUltimoPeso(),
            "DataUltimo" => $animal->getDataUltimo(),
            "estacaoMonta" => ($animal->getEmEstacaoMonta() == null || $animal->getEmEstacaoMonta() == 'N') ? false : true,
            "loteAberto" => $loteAbertoInfo ? $loteAbertoInfo['lote'] : null,
            "pesagemIdLoteAberto" => $loteAbertoInfo ? (int)$loteAbertoInfo['pesagem_id'] : 0,
            "criterioApartacaoMae" => $criterioApartacaoMae,
            "bezerroApartacaoMae" => $bezerroApartacaoMae,
            "codigo" => $animal_alfaNum
        ];
    }    

    public function getAnimalNode($animal){
        if($animal->getAlfa() == ''){
            return [
                "id" => $animal->getId(),
                "codigo" => $animal->getNumerico(),
                "nascimento" => $animal->getNascimento()
            ];
        }

        return [
            "id" => $animal->getId(),
            "codigo" => "{$animal->getAlfa()}-{$animal->getNumerico()}",
            "nascimento" => $animal->getNascimento()
        ];
    }

    public function updateAnimalMorte($animal, $motivoMorte, $data, $obs, $user, $db){
        $animalDao = new AnimalDao($db);
        return $animalDao->updateAnimalMorte($animal, $motivoMorte, $data, $obs, $user);
    }

    public function getAnimalByIdLike($id, $local, $db){
        $a = [];
        $animalDao = new AnimalDao($db);
        $arrayAnimal = $animalDao->getAnimalByIdLike($id, $local);

        foreach($arrayAnimal as $animal){
            array_push($a, $this->getAnimalNode($animal));
        }

        return $a;
    }

    public function getAnimalById($id, $local, $db){
        $animalDao = new AnimalDao($db);

        return $animalDao->getAnimalById($id, $local);
    }

    public function getInfoMaeCompleta($idMae, $db) {
        $animalDao = new AnimalDao($db);
        
        $maeObj = $animalDao->getAnimalByIdSemLocal($idMae); 
        
        if (!$maeObj) return null;

        $infoMae = $this->getAnimalInfo($maeObj, $db);
        
        $filhos = $animalDao->getFilhosAtivosPorMae($idMae);
        
        $infoMae['filhos'] = [];
        foreach ($filhos as $f) {
            $infoMae['filhos'][] = [
                "id" => $f['tbl_animal_codigo_id'],
                "codigo" => ($f['tbl_animal_codigo_alfa'] != '') ? "{$f['tbl_animal_codigo_alfa']}-{$f['tbl_animal_codigo_numerico']}" : $f['tbl_animal_codigo_numerico'],
                "nascimento" => date("d/m/Y", strtotime($f['tbl_animal_data_nascimento'])),
                "sexo" => $f['tbl_animal_sexo'],
                "raca" => $f['raca_nome'],
                "pelagem" => $f['pelagem_nome'],
                "local_nome" => $f['local_nome']
            ];
        }
        
        return $infoMae;
    }    
}