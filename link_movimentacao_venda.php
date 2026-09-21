<?php

@ session_start();
$nomeusuario = $_SESSION['nome_usuario'];
$data_sistema = date("Y/m/d H:i:s");
$mensagem = 0;

$id_faturamento = $_POST['id_faturamento'];
$id_movimentacao = $_POST['id_movimentacao'];

include "conecta_mysql.inc";
			
$sql = ("UPDATE tbl_movimentacao SET tbl_movimentacao_situacao='S',
                tbl_movimentacao_codigo_venda='$id_faturamento',
                tbl_movimentacao_aceite_financeiro_em='$data_sistema',
                tbl_movimentacao_aceite_financeiro_por='$nomeusuario'
                WHERE tbl_movimentacao_id ='$id_movimentacao'");
$resultado = mysqli_query($conector, $sql);
if (!$resultado) {
    $mensagem = "Erro na alteração da situação da movimentação" . "\n" . mysqli_error($conector);
    echo $mensagem;
    mysqli_close($conector);
    exit;
}

$sql = ("UPDATE tbl_venda SET tbl_venda_situacao='S',
                tbl_venda_codigo_movimentacao='$id_movimentacao',
                tbl_venda_alterado_em='$data_sistema',
                tbl_venda_alterado_por='$nomeusuario'
                WHERE tbl_venda_id  ='$id_faturamento'");
$resultado = mysqli_query($conector, $sql);
if (!$resultado) {
    $mensagem = "Erro na alteração da situação do faturamento" . "\n" . mysqli_error($conector);
    echo $mensagem;
    mysqli_close($conector);
    exit;
}

echo $mensagem;
mysqli_close($conector);

?>