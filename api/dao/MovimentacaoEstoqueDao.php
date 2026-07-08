<?php
class MovimentacaoEstoqueDao{

    private $con;

    public function __construct($banco){
        include_once __DIR__ . "/../../conecta_mysql_credenciais.inc";
        $this->con = mysqli_connect($servidor, $usuario_bd, $senha_bd, $banco);
    }

    public function gravarMovimentacaoEstoqueLoteMorte($sexo, $nascimento, $data, $local, $movId, $pasto, $categoria){
        $sql = "INSERT INTO tbl_movimentacao_estoque(
                tbl_mov_estoque_data_emissao,
                tbl_mov_estoque_nascimento,
                tbl_mov_estoque_local,
                tbl_mov_estoque_entrada_saida,
                tbl_mov_estoque_tipo_movimentacao,
                tbl_mov_estoque_local_origem,
                tbl_mov_estoque_codigo_movimentacao,
                tbl_mov_estoque_codigo_pasto,
                tbl_mov_estoque_codigo_categoria,
                tbl_mov_estoque_sexo
                ) VALUES (
                '{$data}',
                '{$nascimento}',
                '{$local}',
                'S',
                'M',
                '{$local}',
                '{$movId}',
                '{$pasto}',
                '{$categoria}',
                '{$sexo}')";

        mysqli_set_charset($this->con, "utf8");
        mysqli_query($this->con, $sql);

        echo mysqli_error($this->con);

        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro ao gravar a movimentação de estoque."
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
        
    }

    public function gravarMovimentacaoEstoqueIndividualMorte($animal, $data, $local, $movId, $pasto, $categoria){
        $sql = "INSERT INTO tbl_movimentacao_estoque
                (tbl_mov_estoque_codigo_id_animal,
                tbl_mov_estoque_data_emissao,
                tbl_mov_estoque_nascimento,
                tbl_mov_estoque_local,
                tbl_mov_estoque_entrada_saida,
                tbl_mov_estoque_tipo_movimentacao,
                tbl_mov_estoque_local_origem,
                tbl_mov_estoque_local_destino,
                tbl_mov_estoque_codigo_movimentacao,
                tbl_mov_estoque_codigo_pasto,
                tbl_mov_estoque_codigo_categoria,
                tbl_mov_estoque_codigo_raca,
                tbl_mov_estoque_codigo_pelagem,
                tbl_mov_estoque_sexo,
                tbl_mov_estoque_primeiro_peso
                ) VALUES 
                ('{$animal->getId()}',
                '{$data}',
                '{$animal->getNascimento()}',
                '{$local}',
                'S',
                'M',
                '{$local}',
                null,
                '{$movId}',
                '{$pasto}',
                '{$categoria}',
                '{$animal->getRaca()->getId()}',
                '{$animal->getPelagem()->getId()}',
                '{$animal->getSexo()}',
                null)";

        mysqli_set_charset($this->con, "utf8");
        mysqli_query($this->con, $sql);

        if(mysqli_error($this->con)){
            return[
                "error" => true,
                "message" => "Ocorreu um erro ao gravar a movimentação de estoque."
            ];
        }

        return[
            "error" => false,
            "message" => ""
        ];
        
    }
}