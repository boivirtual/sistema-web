<?php
class MorteService{

    public function getDescricao($idPasto, $local, $db){
        $pessoaService = new PessoaService();
        $fazenda = $pessoaService->getPessoaById($local, $db);
        
        $pastoService = new PastoService();
        $pasto = $pastoService->getPastoById($idPasto, $db);

        $empresaService = new EmpresaService();
        $empresa = $empresaService->getEmpresaByCnpj($db);

        return [
            "pasto"           => $pastoService->getOption($pasto),
            "fazenda"         => $pessoaService->getOption($fazenda),
            "controlePesagem" => $empresa->getControlePesagem()
        ];
    }

    public function getMotivoMorte($db){
        $motivoMorteDao = new MotivoMorteDao($db);

        return $motivoMorteDao->getMotivoMorte();
    }
}