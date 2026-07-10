<?php
class ItemMovimentacaoDao{

    private $con;

    public function __construct($banco){
        require __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
        $this->systemDate = date('Y-m-d H:i:s');
    }

    public function gravarItemMovimentacaoLoteMortePasto($movId, $data, $animal, $obs, $motivoMorte, $pasto, $catIdade){
        $sql = "INSERT INTO tbl_item_movimentacao (
            tbl_ite_movimentacao_numero_id,
            tbl_ite_movimentacao_numero_item,
            tbl_ite_movimentacao_data_emissao,
            tbl_ite_movimentacao_sexo,
            tbl_ite_movimentacao_observacao,
            tbl_ite_movimentacao_motivo_morte,
            tbl_ite_movimentacao_codigo_pasto,
            tbl_ite_movimentacao_codigo_categoria,
            tbl_ite_movimentacao_qtde_categoria
        ) VALUES (
            '$movId',
            0001,
            '$data',
            '{$animal[0]}',
            '$obs',
            '$motivoMorte',
            '$pasto',
            '$catIdade',
            1
        )";

        mysqli_set_charset($this->con, "utf8");
        mysqli_query($this->con, $sql);
        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro na gravação do item da movimentação."
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
    }

    public function gravarItemMovimentacaoIndividualMortePasto($movId, $data, $animal, $obs, $motivoMorte, $pasto, $catIdade){
        $sql = "INSERT INTO tbl_item_movimentacao (
            tbl_ite_movimentacao_numero_id,
            tbl_ite_movimentacao_numero_item,
            tbl_ite_movimentacao_data_emissao,
            tbl_ite_movimentacao_codigo_id_animal,
            tbl_ite_movimentacao_codigo_animal,
            tbl_ite_movimentacao_peso,
            tbl_ite_movimentacao_sexo,
            tbl_ite_movimentacao_nascimento,
            tbl_ite_movimentacao_raca,
            tbl_ite_movimentacao_pelagem,
            tbl_ite_movimentacao_mae,
            tbl_ite_movimentacao_observacao,
            tbl_ite_movimentacao_motivo_morte,
            tbl_ite_movimentacao_codigo_pasto,
            tbl_ite_movimentacao_codigo_categoria,
            tbl_ite_movimentacao_qtde_categoria
        ) VALUES (
            '$movId',
            0001,
            '$data',
            '{$animal->getId()}',
            '{$animal->getNumerico()}',
            '{$animal->getUltimoPeso()}',
            '{$animal->getSexo()}',
            '{$animal->getNascimento()}',
            '{$animal->getRaca()->getDescricao()}',
            '{$animal->getPelagem()->getDescricao()}',
            '{$animal->getIdMae()}',
            '$obs',
            '$motivoMorte',
            '$pasto',
            '$catIdade',
            1
        )";

        mysqli_set_charset($this->con, "utf8");
        mysqli_query($this->con, $sql);
        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro na gravação do item da movimentação."
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
    }
}