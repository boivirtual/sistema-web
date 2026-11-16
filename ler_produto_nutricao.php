<?php

include "conecta_mysql.inc";

// Só os produtos ativos (Ativo = "N" no cadastro fica de fora; vazio conta
// como ativo).
$objProduto = mysqli_query($conector, "SELECT tbl_produto_codigo_id, tbl_produto_descricao
    FROM tbl_produto
    WHERE tbl_produto_lixeira=0 AND IFNULL(tbl_produto_ativo, 'S') <> 'N'");

echo "<option value='000000000'>...</option>";

while($regProduto = mysqli_fetch_object($objProduto)){
    echo"<option value='$regProduto->tbl_produto_codigo_id'>$regProduto->tbl_produto_descricao</option>";
}

?>