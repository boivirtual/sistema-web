<?php
class MovimentacaoService{

    public function gravarMovimentacaoMortePasto($controleEstoque, $data, $local, $user, $db){
        $movimentacaoDao = new MovimentacaoDao($db);
        return $movimentacaoDao->gravarMovimentacaoMortePasto($controleEstoque, $data, $local, $user);
    }
}