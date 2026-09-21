<?php

include "conecta_mysql.inc";

$numero_doc = str_pad($_POST['numero_doc'], 9, "0", STR_PAD_LEFT);  

for ($i = 0; $i <= 30; $i++) {
	$valor[$i]=0;
}

$matriz_com_itens = 0;
$numero_do_item=0;
$matriz_itens= array();

$rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
						   WHERE tbl_ite_movimentacao_numero_id ='$numero_doc'");
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($fila = mysqli_fetch_object($rs)){

		$codigo_categoria = $fila->tbl_ite_movimentacao_codigo_categoria;
		$tbl_categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
			                        WHERE tab_codigo_categoria_idade ='$codigo_categoria'");

		$num_rows_categoria = mysqli_num_rows($tbl_categoria);

		if ($num_rows_categoria!=0) {
			$reg_cat = mysqli_fetch_object($tbl_categoria);
		    $idade_de = $reg_cat->tab_categoria_idade_de;
	   		$idade_ate = $reg_cat->tab_categoria_idade_ate;

	        if ($idade_ate==999999999){
		    	$desc_categoria = '> 36 meses';
            }
           	else {
		   		$desc_categoria = $idade_de . ' a ' . $idade_ate . ' meses';
        	}
	    }
	    else {
	    	$desc_categoria = '';
	    }

		$valor[0]=$fila->tbl_ite_movimentacao_codigo_id_animal;
		$valor[1]=$fila->tbl_ite_movimentacao_codigo_animal;
		$valor[2]=$fila->tbl_ite_movimentacao_peso;
		$valor[3]=$fila->tbl_ite_movimentacao_sexo;
		$valor[4]=$fila->tbl_ite_movimentacao_nascimento;
		$valor[5]=$fila->tbl_ite_movimentacao_raca;
		$valor[6]=$fila->tbl_ite_movimentacao_pelagem;
		$valor[7]=$fila->tbl_ite_movimentacao_mae;
		$valor[8]=$fila->tbl_ite_movimentacao_observacao;

		$valor[9]=$fila->tbl_ite_movimentacao_numero_item ;
		$valor[10]=$fila->tbl_ite_movimentacao_codigo_categoria;
		$valor[11]=$fila->tbl_ite_movimentacao_peso_medio;
		$valor[12]=$fila->tbl_ite_movimentacao_peso_arroba;
		$valor[13]=$fila->tbl_ite_movimentacao_peso_arroba_medio;
		$valor[14]=$fila->tbl_ite_movimentacao_qtde_categoria;
		$valor[15]=$desc_categoria;

		$itens[$numero_do_item] = $valor[0] . '|' . $valor[1] . '|' . $valor[2] . '|' . $valor[3] . '|' . $valor[4] . '|' . $valor[5] . '|' . $valor[6] . '|' . $valor[7] . '|' . $valor[8] . '|' . $valor[9] . '|' . $valor[10] . '|' . $valor[11] . '|' . $valor[12] . '|' . $valor[13] . '|' . $valor[14] . '|' . $valor[15];
				
		array_push($matriz_itens, $itens[$numero_do_item]);
		$numero_do_item++;
    }
	$matriz_com_itens = implode("<|>", $matriz_itens);
}

echo $matriz_com_itens;

mysqli_free_result($rs); 
mysqli_close($conector);
?>