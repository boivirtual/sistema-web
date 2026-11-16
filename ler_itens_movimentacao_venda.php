<?php

include "conecta_mysql.inc";

$numero_doc = str_pad($_POST['num_doc'], 9, "0", STR_PAD_LEFT);  
$controle_estoque = $_SESSION['controle_estoque'];

for ($i = 0; $i <= 30; $i++) {
	$valor[$i]=0;
}

$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
                                        WHERE tab_registro_lixeira_categoria_idade='0'");
$num_rows_cat = mysqli_num_rows($categoria);	

if ($num_rows_cat!=0) {
	while ($reg_categoria = mysqli_fetch_object($categoria)) {
		$id_categoria = $reg_categoria->tab_codigo_categoria_idade;
        $categoria_id[$id_categoria]='';
        $desc_categoria[$id_categoria]='';
        $total_categoria[$id_categoria]=0;
        $total_femea[$id_categoria]=0;
        $total_macho[$id_categoria]=0;
        $peso_femea[$id_categoria]=0;
        $peso_macho[$id_categoria]=0;
	}
}

$rs = mysqli_query($conector, "SELECT * FROM tbl_item_movimentacao
						   WHERE tbl_ite_movimentacao_numero_id ='$numero_doc'");
$num_rows = mysqli_num_rows($rs);

if ($num_rows!=0){
    while ($fila = mysqli_fetch_object($rs)){
    	$id_animal = $fila->tbl_ite_movimentacao_codigo_id_animal;
    	$peso = $fila->tbl_ite_movimentacao_peso;

    	if ($controle_estoque=='I') {
			$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	                                WHERE tbl_animal_codigo_id ='$id_animal'");
			$reg_animal = mysqli_fetch_object($tbl_animal);

			$sexo = $reg_animal->tbl_animal_sexo;

            $data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

			/*$data_nascimento = $reg_animal->tbl_animal_data_nascimento;
			$data_inicial = $data_nascimento;
			$data_final = date("Y-m-d");
			$diferenca = strtotime($data_final) - strtotime($data_inicial);
			$idade = floor($diferenca / (60 * 60 * 24 * 30));
			$idade = str_pad($idade, 2, "0", STR_PAD_LEFT);*/

			$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		                                WHERE tab_registro_lixeira_categoria_idade='0'");
			$num_rows_cat = mysqli_num_rows($categoria);	

			if ($num_rows_cat!=0) {
				while ($reg_categoria = mysqli_fetch_object($categoria)) {
					$id_categoria = $reg_categoria->tab_codigo_categoria_idade;
				    $idade_de = $reg_categoria->tab_categoria_idade_de;
				    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

				    if ($idade >= $idade_de && $idade <= $idade_ate) {
				    	$descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';
				    	if ($descricao_cat=='37 a 999999999 meses'){
				    		$descricao_cat = '> 36 meses';
				    	}

				    	$categoria_id[$id_categoria]=$id_categoria;
				    	$desc_categoria[$id_categoria]=$descricao_cat;
				    	$total_categoria[$id_categoria]++;

				        if ($sexo=="F") {
			        		$total_femea[$id_categoria]++;
			        		$peso_femea[$id_categoria]+=$peso;
				        }
				        else {
	       					$total_macho[$id_categoria]++;
			        		$peso_macho[$id_categoria]+=$peso;
				        }
				    }
				}
			}					
    	}
    	else {
    		$codigo_categoria = $fila->tbl_ite_movimentacao_codigo_categoria;
			$sexo = $fila->tbl_ite_movimentacao_sexo;
			$qtd_categoria = $fila->tbl_ite_movimentacao_qtde_categoria;

			$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
		                                WHERE tab_codigo_categoria_idade='$codigo_categoria' AND 
		                                      tab_registro_lixeira_categoria_idade='0'");
			$num_rows_cat = mysqli_num_rows($categoria);	

			if ($num_rows_cat!=0) {
				$reg_categoria = mysqli_fetch_object($categoria);
				$id_categoria = $reg_categoria->tab_codigo_categoria_idade;
				$idade_de = $reg_categoria->tab_categoria_idade_de;
				$idade_ate = $reg_categoria->tab_categoria_idade_ate;

			  	$descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';

			   	if ($descricao_cat=='37 a 999999999 meses'){
			   		$descricao_cat = '> 36 meses';
			   	}

			   	$categoria_id[$id_categoria]=$id_categoria;
			   	$desc_categoria[$id_categoria]=$descricao_cat;
			   	$total_categoria[$id_categoria]+=$qtd_categoria;

			    if ($sexo=="F") {
		       		$total_femea[$id_categoria]+=$qtd_categoria;
		       		$peso_femea[$id_categoria]+=$peso;
			    }
			    else {
       				$total_macho[$id_categoria]+=$qtd_categoria;
		       		$peso_macho[$id_categoria]+=$peso;
			    }
			}					
    	}
    }
	$categoria_id = implode("|", $categoria_id);
	$desc_categoria = implode("|", $desc_categoria);
	$total_categoria = implode("|", $total_categoria);
	$total_macho = implode("|", $total_macho);
	$total_femea = implode("|", $total_femea);
	$peso_macho = implode("|", $peso_macho);
	$peso_femea = implode("|", $peso_femea);

	$valor[0]= $categoria_id;
	$valor[1]= $desc_categoria;
	$valor[2]= $total_categoria;
	$valor[3]= $total_macho;
	$valor[4]= $total_femea;
	$valor[5]= $peso_macho;
	$valor[6]= $peso_femea;

	$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . $valor[3] . '<|>' . 
	     $valor[4] . '<|>' . $valor[5] . '<|>' . $valor[6];
	echo $str; 
}
else {
	$str = 0;
	echo $str;
}

mysqli_free_result($rs); 
mysqli_close($conector);
?>