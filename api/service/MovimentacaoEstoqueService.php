<?php
class MovimentacaoEstoqueService{

    public function gravarMovimentacaoEstoqueIndividualMorte($animal, $data, $local, $movId, $pasto, $categoria, $db){
        $movimentacaoEstoqueDao = new MovimentacaoEstoqueDao($db);
        return $movimentacaoEstoqueDao->gravarMovimentacaoEstoqueIndividualMorte($animal, $data, $local, $movId, $pasto, $categoria);
    }

    public function gravarMovimentacaoEstoqueLoteMorte($sexo, $nascimento, $data, $local, $movId, $pasto, $categoria, $db){
        $movimentacaoEstoqueDao = new MovimentacaoEstoqueDao($db);
        return $movimentacaoEstoqueDao->gravarMovimentacaoEstoqueLoteMorte($sexo, $nascimento, $data, $local, $movId, $pasto, $categoria);
    }
}