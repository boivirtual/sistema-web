<?php
class NutricaoService{

    public function transferNutricao($incluirId, $removerId, $db){
        $nutricaoDao = new NutricaoDao($db);
        return $nutricaoDao->transferNutricao($incluirId, $removerId);
    }
}