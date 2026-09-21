<?php
class MunicipioService{

    public function getMunicipio($uf, $bd){
        $municipioDao = new MunicipioDao($bd);
        $municipio = $municipioDao->getMunicipio($uf);
        if(isset($municipio["error"])){
            return $municipio;
        }else{
            return $municipioDao->fillFields($municipio);
        }
    }

    function json_encode_privates($object) {
        $a = [];
        foreach($object as $o){
            array_push($a, json_encode($o, JSON_UNESCAPED_UNICODE));
        }
        return $a;
    }

    function json_encode_private($object) {
        if(is_array($object)){
            return json_encode($object);
        }else{
            function extract_props($object) {
                $public = [];
        
                $reflection = new ReflectionClass(get_class($object));
        
                foreach ($reflection->getProperties() as $property) {
                    $property->setAccessible(true);
        
                    $value = $property->getValue($object);
                    $name = $property->getName();
        
                    if(is_array($value)) {
                        $public[$name] = [];
        
                        foreach ($value as $item) {
                            if (is_object($item)) {
                                $itemArray = extract_props($item);
                                $public[$name][] = $itemArray;
                            } else {
                                $public[$name][] = $item;
                            }
                        }
                    } else if(is_object($value)) {
                        $public[$name] = extract_props($value);
                    } else $public[$name] = $value;
                }
        
                return $public;
            }
        
            return json_encode(extract_props($object));
        }
    }
}