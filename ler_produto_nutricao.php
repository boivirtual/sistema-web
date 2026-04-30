<?php

include "conecta_mysql.inc";

$objProduto = mysqli_query($conector, "SELECT * FROM tbl_produto
    WHERE tbl_produto_lixeira=0");

echo "<option value='000000000'>...</option>";

while($regProduto = mysqli_fetch_object($objProduto)){
    echo"<option value='$regProduto->tbl_produto_codigo_id'>$regProduto->tbl_produto_descricao</option>";
}

?>