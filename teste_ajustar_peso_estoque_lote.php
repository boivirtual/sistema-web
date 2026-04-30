<?php
    include "valida_sessao.inc";
    include "conecta_mysql.inc";

    $local = 77;

    $tbl_estoque= mysqli_query($conector, "SELECT * FROM tbl_movimentacao_estoque
            INNER JOIN tbl_movimentacao 
                    ON tbl_movimentacao_id = tbl_mov_estoque_codigo_movimentacao
            WHERE tbl_mov_estoque_data_emissao>='2024-03-01' and tbl_mov_estoque_data_emissao<='2024-03-31' and 
                tbl_mov_estoque_tipo_movimentacao='C'");

    $num_rows = mysqli_num_rows($tbl_estoque);

    echo 'Registros: ' . $num_rows . '</br>';

    $total = 0;

    while ($reg_estoque = mysqli_fetch_object($tbl_estoque)) {
        $id_mov = $reg_estoque->tbl_mov_estoque_codigo_movimentacao;
        $data_mov = $reg_estoque->tbl_mov_estoque_data_emissao;
        $id_estoque = $reg_estoque->tbl_mov_estoque_numero_id;
        $peso_medio = $reg_estoque->tbl_movimentacao_peso_medio_kg;
        $qtd_animais = $reg_estoque->tbl_movimentacao_qtd_animais_pesados;

        $total+=$peso_medio;

        echo 'Mov: ' . $id_mov . ' Peso Medio: ' . $peso_medio . ' Qtd: ' . 
        $qtd_animais . ' Peso estoque: ' . $reg_estoque->tbl_mov_estoque_primeiro_peso . ' Tipo Registro: ' . $reg_estoque->tbl_mov_estoque_tipo_movimentacao;

        $sql = ("UPDATE tbl_movimentacao_estoque  SET 
                        tbl_mov_estoque_primeiro_peso='$peso_medio'
              WHERE tbl_mov_estoque_numero_id ='$id_estoque'");

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            echo ' Erro ao atualizar o registro' . $erro_mysql . '</br>';
        }
        else {
            echo ' Atualizado para: ' . $peso_medio. '</br>';
        }

    }

    echo 'Fim do processamento, peso total: ' . $total;



?>
