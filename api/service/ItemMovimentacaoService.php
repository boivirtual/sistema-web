<?php
class ItemMovimentacaoService{

    public function gravarItemMovimentacaoIndividualMortePasto($movId, $animal, $data, $obs, $motivo, $pasto, $categoria, $db){
        $itemMovDao = new ItemMovimentacaoDao($db);
        return $itemMovDao->gravarItemMovimentacaoIndividualMortePasto($movId, $data, $animal, $obs, $motivo, $pasto, $categoria);
    }

    public function gravarItemMovimentacaoLoteMortePasto($movId, $animal, $data, $obs, $motivo, $pasto, $categoria, $db){
        $itemMovDao = new ItemMovimentacaoDao($db);
        return $itemMovDao->gravarItemMovimentacaoLoteMortePasto($movId, $data, $animal, $obs, $motivo, $pasto, $categoria);
    }
}