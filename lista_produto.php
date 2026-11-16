<?php
include "conecta_mysql.inc";

$tbl_produto = mysqli_query($conector, "SELECT * FROM tbl_produto 
                            WHERE tbl_produto_codigo_modalidade = 1 AND 
                                  tbl_produto_lixeira=0");  

$num_rows = mysqli_num_rows($tbl_produto);

echo  '<option value="000000000">'.htmlentities('Todos').'</option>';

if ($num_rows!=0) {
    while($reg_produto = mysqli_fetch_object($tbl_produto)) { 
        $codigo_produto = $reg_produto->tbl_produto_codigo_id;
        $codigo_apresentacao = $reg_produto->tbl_produto_apresentacao;
        $codigo_unidade = $reg_produto->tbl_produto_unidade; 

        $qtd_apresentacao = $reg_produto->tbl_produto_qtd_unidade; 
        $tab_unidade = mysqli_query($conector, "select * from tabela_unidade_produtos where tab_codigo_unidade_id='$codigo_unidade'");
        $num_rows = mysqli_num_rows($tab_unidade);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_unidade);
            $simbolo_unidade = $reg->tab_codigo_unidade_produtos;
        }
        else {
            $simbolo_unidade = '';
        }

        $tab_apresentacao = mysqli_query($conector, "select * from tbl_apresentacao_produtos where tab_codigo_apresentacao_id='$codigo_apresentacao'");
        $num_rows = mysqli_num_rows($tab_apresentacao);

        if ($num_rows!=0){
            $reg = mysqli_fetch_object($tab_apresentacao);
            $desc_apresentacao = $reg->tab_descricao_apresentacao_produtos;
        }
        else {
            $desc_apresentacao = '';
        }

        $apresentacao = $desc_apresentacao . ' ' . number_format($qtd_apresentacao, 2, ",", ".") . ' ' . $simbolo_unidade;

        //$descricao = $reg_produto->tbl_produto_descricao . ' ' . $apresentacao;
        $descricao = $reg_produto->tbl_produto_descricao;

        echo '<option value="'.$codigo_produto.'">' .$descricao. '</option>';
                                                
    }
}

mysqli_close($conector);

 
?>
