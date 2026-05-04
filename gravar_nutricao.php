<?php

include "conecta_mysql.inc";
@ session_start(); 
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y-m-d H:i:s");

if($_POST["tipoGravacao"] == '0'){
    $local = $_POST["local"];
    $pasto = $_POST["pasto"];
    $dataNutricao = $_POST["dataNutricao"];
    $idProduto = $_POST["idProduto"];
    $qtdProduto = $_POST["qtdProduto"];
    $undProduto = $_POST["undProduto"];
    $qtdAnimais = $_POST["qtdAnimais"];
    $codigoCocho = $_POST["idCocho"];
    $lote = $_POST["lote"];
    $id_lote = $_POST["id_lote"];
    $ano_lote = $_POST["ano_lote"];

    //$mediaCabeca = $qtdProduto / $qtdAnimais;

    $id_ano_lote = $id_lote.$ano_lote;
     
    if($codigoCocho == '006'){
        $sql = "INSERT INTO tbl_nutricao(
            tbl_nutricao_data,
            tbl_nutricao_codigo_local,
            tbl_nutricao_codigo_pasto,
            tbl_nutricao_codigo_produto,
            tbl_nutricao_qtd_animais,
            tbl_nutricao_quantidade_produto,
            tbl_nutricao_media_cabeca,
            tbl_nutricao_codigo_score_cocho,
            tbl_nutricao_dias_consumo,
            tbl_nutricao_consumo_cabeca_dia,
            tbl_nutricao_id_lote,
            tbl_nutricao_ano_lote,
            tbl_nutricao_lote_pasto,
            tbl_nutricao_incluido_em,
            tbl_nutricao_incluido_por,
            tbl_nutricao_lixeira
        )VALUES(
            '$dataNutricao',
            '$local',
            '$pasto',
            '$idProduto',
            '$qtdAnimais',
            '$qtdProduto',
            null,
            '$codigoCocho',
            0,
            0.00,
            '$id_ano_lote',
            '$ano_lote',
            '$lote',
            '$data_sistema',
            '$nomeusuario',
            0
        )";
    
        //mysqli_query($conector, $sql) or die(mysqli_error($conector));
        $resultado = mysqli_query($conector,$sql);
        $resposta = array('success' => true, 'message' => 'Registro gravado com sucesso.');
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
        } 
        else {

            $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_produto_estoque
                WHERE tbl_produto_estoque_codigo_id='$idProduto' AND 
                      tbl_produto_estoque_codigo_local='$local' AND 
                      tbl_produto_estoque_lixeira = 0"); 
            $num_rows = mysqli_num_rows($tbl_estoque);

            if ($num_rows!=0) {
                $reg_est = mysqli_fetch_object($tbl_estoque);
                $qtd_estoque = $reg_est->tbl_produto_estoque_atual;
                $qtd_estoque-= $qtdProduto;

                $sql = ("UPDATE tbl_produto_estoque SET
                            tbl_produto_estoque_atual='$qtd_estoque',
                            tbl_produto_estoque_alterado_em='$data_sistema',
                            tbl_produto_estoque_alterado_por='$nomeusuario'
                        WHERE tbl_produto_estoque_codigo_id='$idProduto' AND 
                              tbl_produto_estoque_codigo_local='$local'");

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);
                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Erro na alteração do estoque - ' . $erro_mysql));
                    exit;
                } 
                else {
                    header('Content-type: application/json');
                    echo json_encode($resposta);
                }
            }
            else {
                header('Content-type: application/json');
                echo json_encode($resposta);
            }
        }
    }
    else {
        $objNutricao = mysqli_query($conector, "SELECT * FROM `tbl_nutricao` WHERE tbl_nutricao_codigo_local = $local AND tbl_nutricao_codigo_pasto = $pasto AND tbl_nutricao_data < '$dataNutricao' ORDER BY `tbl_nutricao_data` DESC LIMIT 1");
        $retorno = mysqli_num_rows($objNutricao);

        if($retorno > 0){
            $reg_nutri = mysqli_fetch_object($objNutricao);
            $data_anterior = $reg_nutri->tbl_nutricao_data;
            $encerrada = $reg_nutri->tbl_nutricao_encerrada;
        }
        else {
            $data_anterior = 0;
        }    

        if ($encerrada=='S') {
            $data_anterior = 0;
        }
        
        $sql = "INSERT INTO tbl_nutricao(
            tbl_nutricao_data,
            tbl_nutricao_codigo_local,
            tbl_nutricao_codigo_pasto,
            tbl_nutricao_codigo_produto,
            tbl_nutricao_qtd_animais,
            tbl_nutricao_quantidade_produto,
            tbl_nutricao_media_cabeca,
            tbl_nutricao_codigo_score_cocho,
            tbl_nutricao_id_lote,
            tbl_nutricao_ano_lote,
            tbl_nutricao_lote_pasto,
            tbl_nutricao_dias_consumo,
            tbl_nutricao_consumo_cabeca_dia,
            tbl_nutricao_incluido_em,
            tbl_nutricao_incluido_por,
            tbl_nutricao_lixeira
            )VALUES(
            '$dataNutricao',
            '$local',
            '$pasto',
            '$idProduto',
            '$qtdAnimais',
            '$qtdProduto',
            null,
            0,
            '$id_ano_lote',
            '$ano_lote',
            '$lote',
            0,
            0.00,
            '$data_sistema',
            '$nomeusuario',
            0
            )";
        
        $resultado = mysqli_query($conector,$sql);
        $resposta = array('success' => true, 'message' => 'Registro gravado com sucesso.');
        $erro_mysql = mysqli_error($conector);

        if (!$resultado){
            header('Content-type: application/json');
            echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
            exit;
        } 
        else {

            $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_produto_estoque
                WHERE tbl_produto_estoque_codigo_id='$idProduto' AND 
                      tbl_produto_estoque_codigo_local='$local' AND 
                      tbl_produto_estoque_lixeira = 0"); 
            $num_rows = mysqli_num_rows($tbl_estoque);

            if ($num_rows!=0) {
                $reg_est = mysqli_fetch_object($tbl_estoque);
                $qtd_estoque = $reg_est->tbl_produto_estoque_atual;
                $qtd_estoque-= $qtdProduto;

                $sql = ("UPDATE tbl_produto_estoque SET
                            tbl_produto_estoque_atual='$qtd_estoque',
                            tbl_produto_estoque_alterado_em='$data_sistema',
                            tbl_produto_estoque_alterado_por='$nomeusuario'
                        WHERE tbl_produto_estoque_codigo_id='$idProduto' AND 
                              tbl_produto_estoque_codigo_local='$local'");

                $resultado = mysqli_query($conector,$sql);
                $erro_mysql = mysqli_error($conector);
                if (!$resultado){
                    header('Content-type: application/json');
                    echo json_encode(array('error' => $erro_mysql, 'message' => 'Erro na alteração do estoque - ' . $erro_mysql));
                    exit;
                } 
            }

            if($data_anterior!=0){

                $firstDate  = new DateTime($data_anterior);
                $secondDate = new DateTime($dataNutricao);
                $intvl = $firstDate->diff($secondDate);
                $dias_calculados = $intvl->days;

                if ($dias_calculados==0) {
                    $dias_calculados = 1;
                }

                $objNutricao = mysqli_query($conector, "SELECT * FROM `tbl_nutricao` 
                    WHERE tbl_nutricao_codigo_local = $local AND 
                          tbl_nutricao_codigo_pasto = $pasto AND 
                          tbl_nutricao_data = '$data_anterior'");

                while ($reg_nutri = mysqli_fetch_object($objNutricao)) {
                    $id_nutricao = $reg_nutri->tbl_nutricao_id;
                    $qtd_produto_ant = $reg_nutri->tbl_nutricao_quantidade_produto;
                    $qtd_animais_ant = $reg_nutri->tbl_nutricao_qtd_animais;

                    $consumo_ant = ($qtd_produto_ant/$qtd_animais_ant/$dias_calculados)*1000;

                    $sql = ("UPDATE tbl_nutricao SET
                            tbl_nutricao_codigo_score_cocho='$codigoCocho',
                            tbl_nutricao_dias_consumo='$dias_calculados',
                            tbl_nutricao_consumo_cabeca_dia='$consumo_ant'
                            WHERE tbl_nutricao_id='$id_nutricao'");

                    $resultado = mysqli_query($conector,$sql);
                    $resposta = array('success' => true, 'message' => 'Registro gravado com sucesso.');
                    $erro_mysql = mysqli_error($conector);

                    if (!$resultado){
                        header('Content-type: application/json');
                        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na gravação ' . $erro_mysql));
                        exit;
                    } 
                }                
                header('Content-type: application/json');
                echo json_encode($resposta);
            }
            else {
                header('Content-type: application/json');
                echo json_encode($resposta);
            }
        } // Fim else do insert
    }
}
else if($_POST["tipoGravacao"] == '2'){
    $idRegistro = $_POST["idRegistro"];

    $tbl_nutricao = mysqli_query($conector, "SELECT * from tbl_nutricao
        WHERE tbl_nutricao_id='$idRegistro'"); 
    $num_rows = mysqli_num_rows($tbl_nutricao);

    if ($num_rows!=0) {
        $reg_prod = mysqli_fetch_object($tbl_nutricao);
        $idProduto = $reg_prod->tbl_nutricao_codigo_produto;
        $local = $reg_prod->tbl_nutricao_codigo_local;
        $qtdProduto = $reg_prod->tbl_nutricao_quantidade_produto;
    }
    else {
        $idProduto = 0;
        $local = 0;
    }

    $sql = "DELETE FROM tbl_nutricao WHERE tbl_nutricao_id = '$idRegistro'";

    $resultado = mysqli_query($conector,$sql);
    $resposta = array('success' => true, 'message' => 'Registro excluido com sucesso.');
    $erro_mysql = mysqli_error($conector);

    if (!$resultado){
        header('Content-type: application/json');
        echo json_encode(array('error' => $erro_mysql, 'message' => 'Ocorreu um erro na exclusão ' . $erro_mysql));
        exit;
    } 
    else {

        $tbl_estoque = mysqli_query($conector, "SELECT * from tbl_produto_estoque
            WHERE tbl_produto_estoque_codigo_id='$idProduto' AND 
                  tbl_produto_estoque_codigo_local='$local' AND 
                  tbl_produto_estoque_lixeira = 0"); 
        $num_rows = mysqli_num_rows($tbl_estoque);

        if ($num_rows!=0) {
            $reg_est = mysqli_fetch_object($tbl_estoque);
            $qtd_estoque = $reg_est->tbl_produto_estoque_atual;
            $qtd_estoque+= $qtdProduto;

            $sql = ("UPDATE tbl_produto_estoque SET
                            tbl_produto_estoque_atual='$qtd_estoque',
                            tbl_produto_estoque_alterado_em='$data_sistema',
                            tbl_produto_estoque_alterado_por='$nomeusuario'
                        WHERE tbl_produto_estoque_codigo_id='$idProduto' AND 
                              tbl_produto_estoque_codigo_local='$local'");

            $resultado = mysqli_query($conector,$sql);
            $erro_mysql = mysqli_error($conector);
            if (!$resultado){
                header('Content-type: application/json');
                echo json_encode(array('error' => $erro_mysql, 'message' => 'Erro na alteração do estoque - ' . $erro_mysql));
                exit;
            } 
            else {
                header('Content-type: application/json');
                echo json_encode($resposta);
            }
        }
        else {
            header('Content-type: application/json');
            echo json_encode(array('error' => true, 'message' => 'Estoque não encontrado para o produto/local'));
            exit;
        }
    }
}


?>