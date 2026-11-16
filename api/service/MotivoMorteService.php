<?php
class MotivoMorteService{

    public function getMotivoMorteById($id, $db){
        $motivoMorteDao = new MotivoMorteDao($db);
        return $motivoMorteDao->getMotivoMorteById($id);
    }
}