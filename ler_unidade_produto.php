<?php

include "conecta_mysql.inc";

$idProduto = $_POST["idProduto"];
$query = "SELECT tbl_produto_unidade, tab_codigo_unidade_produtos FROM tbl_produto JOIN tabela_unidade_produtos ON tbl_produto_unidade = tab_codigo_unidade_id WHERE tbl_produto_codigo_id = $idProduto";

$objProduto = mysqli_query($conector, $query);
$regProduto = mysqli_fetch_object($objProduto);

echo $regProduto->tab_codigo_unidade_produtos;

?>