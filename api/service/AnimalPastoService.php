<?php
class AnimalPastoService{

    public function getAge($animal){
        $dataTemp = date("Y-m-d");
        $nascimento = new DateTime($animal->getNascimento());
        $idade = $nascimento->diff(new DateTime($dataTemp));
        return (($idade->format('%Y')*12) + ($idade->format('%m')));
    }

    public function getAnimalPastoByPasto($pasto, $db){
        $animalPastoDao = new AnimalPastoDao($db);
        return $animalPastoDao->getAnimalPastoByPasto($pasto);
    }

    public function getAnimalPastoByPastoAndLocal($pasto, $local, $db){
        $animalPastoDao = new AnimalPastoDao($db);
        return $animalPastoDao->getAnimalPastoByPasto($pasto, $local);
    }

    public function getAnimalCategoria($arrayCategoria, $idade){
        foreach($arrayCategoria as $categoria){
            if($idade >= $categoria->getIdadeDe() && $idade <= $categoria->getIdadeAte()){
                return $categoria->getId();
            }
        }
    }

    public function compareAnimalAge($arrayAnimalPasto, $arrayCategoria, $catAnimal, $sexo){
        foreach($arrayAnimalPasto as $animalPasto){
            $idade = $this->getAge($animalPasto);
            if($this->getAnimalCategoria($arrayCategoria, $idade) == $catAnimal && $animalPasto->getSexo() == $sexo){
                return $animalPasto;
            }
        }
        
        return[
            "error" => true,
            "message" => "Não existem animais com a categoria/sexo selecionada no pasto!"
        ];
    }

    public function gravarMovDeleteAnimalPasto($animalPastoToDelete, $controleEstoque, $data, $local, $user, $db){
        $animalPastoDao = new AnimalPastoDao($db);
        $r = $animalPastoDao->deleteAnimalPastoByPastoAndLocalAndNumeroItem($animalPastoToDelete->getPasto()->getId(), $animalPastoToDelete->getLocal()->getId(), $animalPastoToDelete->getAnimal());
        if($r["error"]){
            return $r;
        }

        $movimentacaoService = new MovimentacaoService();
        $r = $movimentacaoService->gravarMovimentacaoMortePasto($controleEstoque, $data, $local, $user, $db);

        return $r;
    }

    public function gravarMorteLote($animal, $motivo, $local, $pasto, $data, $obs, $controleEstoque, $user, $db){
        $animal = explode("-", $animal);

        $categoriaIdadeService = new CategoriaIdadeService();
        $aCategoria = $categoriaIdadeService->getCategoria($db);

        $animalPastoToDelete = $this->compareAnimalAge($this->getAnimalPastoByPastoAndLocal($pasto, $local, $db), $aCategoria, $animal[1], $animal[0]);
        if(!is_array($animalPastoToDelete)){
            $r = $this->gravarMovDeleteAnimalPasto($animalPastoToDelete, $controleEstoque, $data, $local, $user, $db);
            if($r["error"]){
                return $r;
            }

            $movId = $r["movId"];

            $itemMovimentacaoService = new ItemMovimentacaoService();
            $r = $itemMovimentacaoService->gravarItemMovimentacaoLoteMortePasto($movId, $animal, $data, $obs, $motivo, $pasto, $animalPastoToDelete->getCategoria()->getId(), $db);
            if($r["error"]){
                return $r;
            }

            $movimentacaoEstoqueService = new MovimentacaoEstoqueService();
            $r = $movimentacaoEstoqueService->gravarMovimentacaoEstoqueLoteMorte($animal[0], $animalPastoToDelete->getNascimento(), $data, $local, $movId, $pasto, $animalPastoToDelete->getCategoria()->getId(), $db);
            if($r["error"]){
                return $r;
            }

            return[
                "error" => false,
                "message" => ""
            ];
        }

        return $animalPastoToDelete;
    }

    public function gravarMorteIndividual($idAnimal, $motivo, $local, $pasto, $data, $obs, $controleEstoque, $user, $db){
        $animalService = new AnimalService();
        $animal = $animalService->getAnimalById($idAnimal, $local, $db);

        $categoriaIdadeService = new CategoriaIdadeService();
        $aCategoria = $categoriaIdadeService->getCategoria($db);

        $idade = $this->getAge($animal);
        $catAnimal = $this->getAnimalCategoria($aCategoria, $idade);

        $animalPastoToDelete = $this->compareAnimalAge($this->getAnimalPastoByPastoAndLocal($pasto, $local, $db), $aCategoria, $catAnimal, $animal->getSexo());
        if(!is_array($animalPastoToDelete)){
            $r = $this->gravarMovDeleteAnimalPasto($animalPastoToDelete, $controleEstoque, $data, $local, $user, $db);
            if($r["error"]){
                return $r;
            }

            $movId = $r["movId"];

            $itemMovimentacaoService = new ItemMovimentacaoService();
            $r = $itemMovimentacaoService->gravarItemMovimentacaoIndividualMortePasto($movId, $animal, $data, $obs, $motivo, $pasto, $animalPastoToDelete->getCategoria()->getId(), $db);
            if($r["error"]){
                return $r;
            }

            $movimentacaoEstoqueService = new MovimentacaoEstoqueService();
            $r = $movimentacaoEstoqueService->gravarMovimentacaoEstoqueIndividualMorte($animal, $data, $local, $movId, $pasto, $animalPastoToDelete->getCategoria()->getId(), $db);
            if($r["error"]){
                return $r;
            }

            $motivoMorteService = new MotivoMorteService();
            $motivoMorte = $motivoMorteService->getMotivoMorteById($motivo, $db);

            $r = $animalService->updateAnimalMorte($animal, $motivoMorte, $data, $obs, $user, $db);
            if($r["error"]){
                return $r;
            }

            return[
                "error" => false,
                "message" => ""
            ];
        }

        return $animalPastoToDelete;
    }

    public function gravarMorte($animal, $motivo, $local, $pasto, $data, $obs, $userId, $db){
        if($animal == ""){
            return [
                "error" => true,
                "message" => "Informe o animal!"
            ];
        }
        if($motivo == ""){
            return[
                "error" => true,
                "message" => "Informe o motivo!"
            ];
        }
        if($data == ""){
            return[
                "error" => true,
                "message" => "Informe a data!"
            ];
        }

        $empresaService = new EmpresaService();
        $empresa = $empresaService->getEmpresaByCnpj($db);

        $usuarioService = new UsuarioService();
        $user = $usuarioService->getUserById($userId);

        if($empresa->getControlePesagem() == "I"){
            return $this->gravarMorteIndividual($animal, $motivo, $local, $pasto, $data, $obs, 'I', $user, $db);
        }

        return $this->gravarMorteLote($animal, $motivo, $local, $pasto, $data, $obs, 'L', $user, $db);
    }
    
    public function transferAll($pastoIncluir, $pastoRemover, $user, $fazenda, $db){
        $dao = new AnimalPastoDao($db);
        return $dao->transferAll($pastoIncluir->getId(), $pastoRemover->getId(), $user, $fazenda);
    }
}