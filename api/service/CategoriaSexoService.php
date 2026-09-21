<?php
class CategoriaSexoService{

    public function getOption($regM, $regF, $cat){
        $a = [];

        for($i = 1; $i <= 5; $i++){
            if($regM[str_pad($i, 3, "0", STR_PAD_LEFT)] != 0){
                array_push($a, [
                    "value" => "M-".str_pad($i, 3, "0", STR_PAD_LEFT),
                    "descricao" => "{$cat[str_pad($i, 3, "0", STR_PAD_LEFT)]} - Macho"
                ]);
            }
            if($regF[str_pad($i, 3, "0", STR_PAD_LEFT)] != 0){
                array_push($a, [
                    "value" => "F-".str_pad($i, 3, "0", STR_PAD_LEFT),
                    "descricao" => "{$cat[str_pad($i, 3, "0", STR_PAD_LEFT)]} - Fêmea"
                ]);
            }
        }

        return $a;
    }

    public function getCategoriaSexo($pasto, $local, $db){
        $animalPastoService = new AnimalPastoService();
        $arrayAnimalPasto = $animalPastoService->getAnimalPastoByPastoAndLocal($pasto, $local, $db);

        $categoriaIdadeService = new CategoriaIdadeService();
        $arrayCategoriaIdade = $categoriaIdadeService->getCategoria($db);

        for($i = 1; $i <= 5; $i++){
            $regMacho[str_pad($i, 3, "0", STR_PAD_LEFT)] = 0;
            $regFemea[str_pad($i, 3, "0", STR_PAD_LEFT)] = 0;
            $descricaoCategoria[str_pad($i, 3, "0", STR_PAD_LEFT)] = '';
        }

        foreach($arrayAnimalPasto as $animal){
            $idade = $animalPastoService->getAge($animal);

            foreach($arrayCategoriaIdade as $categoria){
                $descricaoCategoria[$categoria->getId()] = ($categoria->getIdadeAte() == 999999999) ? '> 36 meses' : "{$categoria->getIdadeDe()} a {$categoria->getIdadeAte()} meses";

                if($idade >= $categoria->getIdadeDe() && $idade <= $categoria->getIdadeAte()){
                    if($animal->getSexo() == 'M'){
                        $regMacho[$categoria->getId()]++;
                    }else{
                        $regFemea[$categoria->getId()]++;
                    }
                }
            }
        }

        return $this->getOption($regMacho, $regFemea, $descricaoCategoria);
    }
}