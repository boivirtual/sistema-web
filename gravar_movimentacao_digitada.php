<?php
    include "conecta_mysql.inc";

    $idMovimentacaoGravada = $_POST['idMovimentacaoGravada'] ?? 0;
    $flagSelecionados = $_POST['flagSelecionados'];

    if ($flagSelecionados==1) {
        $animalId = $_POST['animalId'] ?? [];
        $animalSelecionado = $_POST['animalSelecionado'] ?? 'N';
    
        $sql = "UPDATE tbl_item_movimentacao SET
                tbl_ite_movimentacao_selecionado='$animalSelecionado'
            WHERE tbl_ite_movimentacao_numero_id ='$idMovimentacaoGravada' AND 
                  tbl_ite_movimentacao_codigo_id_animal='$animalId'";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            $resposta = [
                'status' => 'error',
                'mensagem' => 'Ocorreu um erro ao registrar a movimentação do item'. $erro_mysql
            ];
            echo json_encode($resposta);
            mysqli_close($conector);
            exit;
        }
        else {
            header('Content-Type: application/json');
            $resposta = [
                'status' => 'sucesso',
                'mensagem' => ''
            ];
            echo json_encode($resposta);
            mysqli_close($conector);
            exit;
        }
    }
    else {
        $listaAnimais = $_POST['listaAnimais'] ?? [];

        foreach ($listaAnimais as $item) {
            $animalId = (int)$item['id'];
            $animalSelecionado = $item['selecionado'];

            $sql = "UPDATE tbl_item_movimentacao SET
                    tbl_ite_movimentacao_selecionado='$animalSelecionado'
                WHERE tbl_ite_movimentacao_numero_id ='$idMovimentacaoGravada' AND 
                      tbl_ite_movimentacao_codigo_id_animal='$animalId'";

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);

            if (!$resultado){
                header('Content-type: application/json');
                $resposta = [
                    'status' => 'error',
                    'mensagem' => 'Ocorreu um erro ao registrar a movimentação do item'. $erro_mysql
                ];
                echo json_encode($resposta);
                mysqli_close($conector);
                exit;
            }
        }
    }


    /*if ($idMovimentacaoGravada!=0 && $selecionados==0) {

        $sql = ("DELETE FROM tbl_item_movimentacao
            WHERE tbl_ite_movimentacao_numero_id ='$idMovimentacaoGravada'");
        $resultado = mysqli_query($conector,$sql);

        $sql = ("DELETE FROM tbl_movimentacao
            WHERE tbl_movimentacao_id ='$idMovimentacaoGravada'");
        $resultado = mysqli_query($conector,$sql);

        $idMovimentacaoGravada=0;

        header('Content-Type: application/json');
        $resposta = [
            'status' => 'sucesso',
            'mensagem' => '',
            'idMovimentacao' => $idMovimentacaoGravada
        ];
        echo json_encode($resposta);
        mysqli_close($conector);
        exit;
    }*/


    /*$sql = "UPDATE tbl_movimentacao SET
            tbl_movimentacao_qtd_animais_pesados='$total_digitados'
            WHERE tbl_movimentacao_id='$idMovimentacaoGravada'";

        $resultado = mysqli_query($conector,$sql);
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            $resposta = [
                'status' => 'error',
                'mensagem' => 'Ocorreu um erro ao registrar a movimentação'. $erro_mysql
            ];
            echo json_encode($resposta);
            mysqli_close($conector);
            exit;
    }*/


            
        /*    if ($peso_total!=0) {
                $peso_total_medio= $peso_total/$quantidade_itens;
                $peso_total_arroba=$peso_total/30;
                $peso_total_arroba_medio=$peso_total_arroba/$quantidade_itens;
                
                $sql = "UPDATE tbl_movimentacao SET
                    tbl_movimentacao_peso_kg='$peso_total',
                    tbl_movimentacao_peso_arroba='$peso_total_arroba',
                    tbl_movimentacao_peso_medio_kg='$peso_total_medio',
                    tbl_movimentacao_peso_medio_arroba='$peso_total_arroba_medio'
                    WHERE tbl_movimentacao_id='$idMovimentacaoGravada'";

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);

                if (!$resultado){
                    return ['Ocorreu um erro na alterção da movimentação (Peso Total).', $erro_mysql];
                } 
            }*/

?>