<?php
class CategoriaIdadeService{
    
    public function getCategoria($db){
        $categoriaIdadeDao = new CategoriaIdadeDao($db);

        return $categoriaIdadeDao->getCategoria();
    }
}