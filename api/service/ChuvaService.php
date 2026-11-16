<?php
class ChuvaService{

    public function getVolume($bd, $data, $local, $volume){
        if($data == ''){
            return [
                "error" => true,
                "message" => "Informe a data."
            ];
        }elseif($local == '000000000'){
            return [
                "error" => true,
                "message" => "Informe o local."
            ];
        }elseif($volume == ''){
            return [
                "error" => true,
                "message" => "Informe o volume."
            ];
        }
        $objDao = new ChuvaDao($bd);
        return $objDao->getVolume($data, $local);
    }

    public function getDias($bd, $local){
        if($local == '000000000'){
            return [
                "error" => true,
                "message" => "Informe o local."
            ];
        }
        
        $mes = date('m');
        $ano = date('Y');
        $data = new DateTime(date('Y-m-d'));
        $volumeAno = 0;
        $volumeMes = 0;
        $diasChuva = 0;

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	    date_default_timezone_set('America/Sao_Paulo');
        $mesE = ucfirst(utf8_encode(strftime('%B', strtotime($data->format('Y-m')))));

        $objDao = new ChuvaDao($bd);
        $dias = $objDao->getDias($local, $ano);

        if(!isset($dias['error'])){
            foreach($dias as $dia){
                $volumeAno += $dia->getVolume();
                if(date('m', strtotime($dia->getData())) == $mes && date('Y', strtotime($dia->getData())) == $ano && $dia->getVolume()){
                    $volumeMes += $dia->getVolume();
                    $diasChuva++;
                }
            }
        }else{
            return $dias;
        }

        return [
            "error" => false,
            "message" => "",
            "diasChuva" => "{$diasChuva}",
            "volumeMes" => "{$volumeMes}",
            "volumeAno" => "{$volumeAno}",
            "mes" => "{$mesE}"
        ];
    }

    /**
     * Exportação em massa para o cache local do app (tela de Chuva
     * offline). Só os últimos 5 anos: é tudo que a tela exibe (o gráfico
     * anual mostra o ano atual e os 4 anteriores).
     */
    public function listarParaApp($bd, $fazendas){
        $idsFazendas = array_values(array_filter(
            array_map('intval', is_array($fazendas) ? $fazendas : []),
            function ($id) { return $id > 0; }
        ));
        if (trim((string) $bd) === '' || count($idsFazendas) === 0) {
            return ["success" => false, "message" => "Parâmetros inválidos."];
        }

        $dao = new ChuvaDao($bd);
        if (!$dao->getConexao()) {
            return ["success" => false, "message" => "Não foi possível conectar ao banco."];
        }

        $lista = [];
        foreach ($dao->listarDesdeAno($idsFazendas, (int) date('Y') - 5) as $row) {
            $lista[] = [
                "id"     => (int) $row['tbl_chuva_id'],
                "local"  => (int) $row['tbl_chuva_local'],
                "data"   => $row['tbl_chuva_data'],
                "volume" => (float) $row['tbl_chuva_volume_chuva'],
            ];
        }

        return ["success" => true, "chuvas" => $lista];
    }

    public function createChuva($chuva){
        if($chuva["data_chuva"] == ''){
            return [
                "error" => true,
                "message" => "Informe a data."
            ];
        }elseif($chuva["codigo_local_chuva"] == '000000000' || $chuva["codigo_local_chuva"] == ''){
            return [
                "error" => true,
                "message" => "Informe o local."
            ];
        }elseif($chuva["volume_chuva"] == ''){
            return [
                "error" => true,
                "message" => "Informe o volume."
            ];
        }

        $objDao = new ChuvaDao($chuva["bd"]);
        return $objDao->createChuva($chuva);
    }
}