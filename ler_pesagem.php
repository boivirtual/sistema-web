<?php
include "conecta_mysql.inc";

@ session_start(); 
$controle_estoque = $_SESSION['controle_estoque'];

for ($i=1; $i<=20; $i++){
    $valor[$i]=0;
}

$tem_registros = 'N';
$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
    WHERE tab_registro_lixeira_categoria_idade='0'");
$num_rows_cat = mysqli_num_rows($categoria);    

if ($num_rows_cat!=0) {
    while ($reg_categoria = mysqli_fetch_object($categoria)) {
        $id_categoria = $reg_categoria->tab_codigo_categoria_idade;
        $qtd_animais_macho[$id_categoria] = 0;
        $qtd_animais_macho_pasto[$id_categoria] = 0;

        $qtd_animais_femea[$id_categoria] = 0;
        $qtd_animais_femea_pasto[$id_categoria] = 0;

        $descricao_categoria[$id_categoria]='';
    }
}

$codigo_pesagem = $_POST['id'];  
$tbl_pesagem = mysqli_query($conector, "SELECT * FROM tbl_pesagem
    WHERE tbl_pesagem_id='$codigo_pesagem'");

$num_rows = mysqli_num_rows($tbl_pesagem);

if ($num_rows!=0) {
	$reg_pesagem = mysqli_fetch_object($tbl_pesagem);

	$data_inclusao = new DateTime($reg_pesagem->tbl_pesagem_data);
    $epoca = $reg_pesagem->tbl_pesagem_codigo_epoca;
    $local = $reg_pesagem->tbl_pesagem_codigo_local;

    // pega codigo do pasto de saida
	$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_pasto 
			WHERE tbl_pasto_codigo_local='$local' AND 
		          tbl_pasto_tipo_curral='S'");  
						
	$num_rows_pasto = mysqli_num_rows($tbl_pasto);

	if($num_rows_pasto !=0){
		$ln = mysqli_fetch_assoc($tbl_pasto);
	    $codigo_pasto = $ln['tbl_pasto_id'];
	}
	else {
		$codigo_pasto = 0;
	}

	// verificar animais no pasto de saida
    $tbl_animais= mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
        WHERE tbl_animal_pasto_situacao='A' AND 
              tbl_animal_pasto_local = '$local' AND 
              tbl_animal_pasto_id = '$codigo_pasto'");

    $num_rows = mysqli_num_rows($tbl_animais);

    if ($num_rows!=0) {
        while ($reg_animal = mysqli_fetch_object($tbl_animais)) {
            //$categoria_pasto = $reg_animal->tbl_animal_pasto_categoria;  
            $data_nascimento = $reg_animal->tbl_animal_pasto_nascimento;  
            $sexo_animal = $reg_animal->tbl_animal_pasto_sexo;  
            $data_acompanhamento_calculo = date("Y-m-d");
            $date = new DateTime($data_nascimento); // Data de Nascimento
            $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
            $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
            $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
            $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

            if ($controle_estoque=='I') {
	            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
	                WHERE tab_registro_lixeira_categoria_idade='0'");

	            $num_rows = mysqli_num_rows($categoria);    

	            if ($num_rows!=0) {
	                while ($reg_categoria = mysqli_fetch_object($categoria)) {
	                    $idade_de = $reg_categoria->tab_categoria_idade_de;
	                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

	                    if ($idade >= $idade_de && $idade <= $idade_ate) {
	                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

	                        if ($sexo_animal=='M') {
	                            $qtd_animais_macho_pasto[$codigo_categoria]++;
	                        }
	                        else {
	                            $qtd_animais_femea_pasto[$codigo_categoria]++;
	                        }
	                    }
	                }                   
	            }

	            /*if ($sexo_animal=='M') {
	                $qtd_animais_macho_pasto[$categoria_pasto]++;
	            }
	            else {
	                $qtd_animais_femea_pasto[$categoria_pasto]++;
	            }*/
            }
            else {
	            $categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
	                WHERE tab_registro_lixeira_categoria_idade='0'");

	            $num_rows = mysqli_num_rows($categoria);    

	            if ($num_rows!=0) {
	                while ($reg_categoria = mysqli_fetch_object($categoria)) {
	                    $idade_de = $reg_categoria->tab_categoria_idade_de;
	                    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

	                    if ($idade >= $idade_de && $idade <= $idade_ate) {
	                        $codigo_categoria = $reg_categoria->tab_codigo_categoria_idade;

	                        if ($sexo_animal=='M') {
	                            $qtd_animais_macho_pasto[$codigo_categoria]++;
	                        }
	                        else {
	                            $qtd_animais_femea_pasto[$codigo_categoria]++;
	                        }
	                    }
	                }                   
	            }
            }
        }
    }

	$tbl_epoca = mysqli_query($conector, "SELECT * FROM tabela_epoca_pesagem
						   WHERE tab_codigo_epoca_pesagem ='$epoca'");
	$num_rows_epoca = mysqli_num_rows($tbl_epoca);

	if ($num_rows_epoca!=0){
	    $reg_epoca = mysqli_fetch_object($tbl_epoca);
		$valor[10]= $reg_epoca->tab_descricao_epoca_pesagem;   
	}
	else {
		$valor[10]= '';
	}

    $valor[0] = $reg_pesagem->tbl_pesagem_filtros;
    $valor[1] = $reg_pesagem->tbl_pesagem_lote;
    $valor[2] = $data_inclusao->format('d/m/Y');
    $valor[3] = $reg_pesagem->tbl_pesagem_qtd_animais_pesados;
    $valor[4] = $reg_pesagem->tbl_pesagem_peso_kg;
    $valor[5] = $reg_pesagem->tbl_pesagem_peso_arroba;
    $valor[6] = $reg_pesagem->tbl_pesagem_peso_medio_kg;
    $valor[7] = $reg_pesagem->tbl_pesagem_peso_medio_arroba;
    $valor[9] = $reg_pesagem->tbl_pesagem_codigo_epoca;

	$numero_do_item=0;
	$matriz_itens= array();

	$rs = mysqli_query($conector, "SELECT * FROM tbl_item_pesagem
    	INNER JOIN tbl_animais
            	ON tbl_animal_codigo_id = tbl_ite_pesagem_codigo_id_animal
		WHERE tbl_ite_pesagem_numero_id='$codigo_pesagem'
		ORDER BY tbl_animal_codigo_numerico ASC");
	$num_rows = mysqli_num_rows($rs);

	if ($num_rows!=0){
	    while ($reg_item = mysqli_fetch_object($rs)){
        	$codigo_alfa = $reg_item->tbl_animal_codigo_alfa;
        	$codigo_numerico = intval($reg_item->tbl_animal_codigo_numerico);

	    	$id_animal = $reg_item->tbl_ite_pesagem_codigo_id_animal;
			//$id_animal = $reg_item->tbl_ite_pesagem_codigo_animal;
			//$codigo_numerico = substr($id_animal, -9);

			//if (strlen($id_animal)!=9){
			//	$data = explode("-", $id_animal);
				//$codigo_alfa = $data[0];
			//}
			//else {
			//	$codigo_alfa = '';
			//}

			//$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
			  //  WHERE tbl_animal_codigo_alfa='$codigo_alfa' AND  tbl_animal_codigo_numerico='$codigo_numerico'");

			$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
			    WHERE tbl_animal_codigo_id='$id_animal'");

			$num_rows_animal = mysqli_num_rows($tbl_animal);

			if ($num_rows_animal!=0) {
				$reg_animal = mysqli_fetch_object($tbl_animal);
				$id_animal = $reg_animal->tbl_animal_codigo_id;
			}
			else {
				$id_animal = 0;
			}

			$codigo_categoria = $reg_item->tbl_ite_pesagem_categoria;
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

                $descricao_categoria[$codigo_categoria]=$desc_categoria;

                if ($reg_item->tbl_ite_pesagem_sexo=='M' || 
                	$reg_item->tbl_ite_pesagem_sexo=='Macho') {
                    $qtd_animais_macho[$codigo_categoria]++;
                }
                else {
                    $qtd_animais_femea[$codigo_categoria]++;
                }

	    	}
	    	else {
	    		$desc_categoria = '';
	    	}


        	$codigo_mae = $reg_item->tbl_ite_pesagem_mae;

        	$caracteres = strlen($codigo_mae);

        	if ($caracteres>=9){
            	$codigo_numerico_mae = intval(substr($codigo_mae, (strlen($codigo_mae) - 9), 9));
            	$codigo_alfa_mae = strrev(preg_replace('/\d/', '',  strrev($codigo_mae), 9));

            	if ($codigo_alfa_mae=='' && $codigo_numerico_mae==0) {
                $codigo_mae_edi = '';
            	}
            	else if ($codigo_alfa_mae==''){
                	$codigo_mae_edi = $codigo_numerico_mae;
            	}
            	else {
                	$codigo_mae_edi = $codigo_alfa_mae.'-'.$codigo_numerico_mae;
           		}
        	}
        	/*else if ($codigo_mae==0 || $codigo_mae=='') {
            	$codigo_mae_edi = '';
        	}*/
        	else {
            	$codigo_mae_edi = $codigo_mae;
        	}


	        /*$codigo_mae = $reg_item->tbl_ite_pesagem_mae;
	        $codigo_numerico_mae = intval(substr($codigo_mae, (strlen($codigo_mae) - 9), 9));
	        $codigo_alfa_mae = strrev(preg_replace('/\d/', '',  strrev($codigo_mae), 9));

	        if ($codigo_alfa_mae=='' && $codigo_numerico_mae==0) {
	            $codigo_mae_edi = '';
	        }
	        else if ($codigo_alfa_mae==''){
	            $codigo_mae_edi = $codigo_numerico_mae;
	        }
	        else {
	            $codigo_mae_edi = $codigo_alfa_mae.'-'.$codigo_numerico_mae;
	        }*/

			//$valor_item[0]=$reg_item->tbl_ite_pesagem_codigo_animal;
			$valor_item[0]=$codigo_numerico;
			$valor_item[16]=$codigo_alfa;
			$valor_item[1]=$reg_item->tbl_ite_pesagem_peso;

			if ($reg_item->tbl_ite_pesagem_sexo=='Macho') {
				$valor_item[2]='M';
			}
			else if ($reg_item->tbl_ite_pesagem_sexo=='Femea' || $reg_item->tbl_ite_pesagem_sexo=='Fêmea'){
				$valor_item[2]='F';
			}
			else {
				$valor_item[2]=$reg_item->tbl_ite_pesagem_sexo;
			}

			$valor_item[3]=$reg_item->tbl_ite_pesagem_nascimento;
			$valor_item[4]=utf8_decode($reg_item->tbl_ite_pesagem_raca);
			$valor_item[5]=utf8_decode($reg_item->tbl_ite_pesagem_pelagem);
			$valor_item[6]=$codigo_mae_edi;
			$valor_item[7]=$reg_item->tbl_ite_pesagem_observacao;
			$valor_item[8]=$id_animal;

			$valor_item[9]=$reg_item->tbl_ite_pesagem_numero_item;
			$valor_item[10]=$reg_item->tbl_ite_pesagem_categoria;
			$valor_item[11]=$reg_item->tbl_ite_pesagem_peso_medio;
			$valor_item[12]=$reg_item->tbl_ite_pesagem_arroba;
			$valor_item[13]=$reg_item->tbl_ite_pesagem_arroba_media;
			$valor_item[14]=$reg_item->tbl_ite_pesagem_qtd_animais;
			$valor_item[15]=$desc_categoria;

			$itens[$numero_do_item] = $valor_item[0] . '|' . $valor_item[1] . '|' . 
			  	$valor_item[2] . '|' . $valor_item[3] . '|' . $valor_item[4] . '|' . 
			  	$valor_item[5] . '|' . $valor_item[6] . '|' . $valor_item[7] . '|' . 
			  	$valor_item[8] . '|' . $valor_item[9] . '|' . $valor_item[10] . '|' .
			   	$valor_item[11] . '|' . $valor_item[12] . '|' . $valor_item[13] . '|' . 
			   	$valor_item[14] . '|' . $valor_item[15] . '|' . $valor_item[16];
					
			array_push($matriz_itens, $itens[$numero_do_item]);
			$numero_do_item++;
	    }

		$matriz_com_itens = implode("<!>", $matriz_itens);
	}
	else {
		$matriz_com_itens = 0;
	}


	// verifica se existe animais com as categorias certas no pasto

	$ajustar_macho = '';
	$ajustar_femea = '';

    for ($i=1; $i <=5 ; $i++) { 
        $categoria_ajustar = str_pad($i, 3, "0", STR_PAD_LEFT);

        if ($qtd_animais_macho[$categoria_ajustar]!=$qtd_animais_macho_pasto[$categoria_ajustar]) {
            $qtd_ajustar = $qtd_animais_macho[$categoria_ajustar] - 
            $qtd_animais_macho_pasto[$categoria_ajustar];

            if ($qtd_ajustar>0) {
	            $valor[16] = 'S';
	            $ajustar_macho.= $descricao_categoria[$categoria_ajustar] .' = '. $qtd_ajustar .'<br>';
	            $valor[17] = $ajustar_macho;
            }
        }

        if ($qtd_animais_femea[$categoria_ajustar]!=$qtd_animais_femea_pasto[$categoria_ajustar]) {
            $qtd_ajustar = $qtd_animais_femea[$categoria_ajustar] - 
                           $qtd_animais_femea_pasto[$categoria_ajustar];

            if ($qtd_ajustar>0) {
	            $valor[18] = 'S';
    	        $ajustar_femea.= $descricao_categoria[$categoria_ajustar] .' = '. $qtd_ajustar .'<br>';
            	$valor[19] = $ajustar_femea;
            }
        }
    }

    if ($ajustar_macho!='' || $ajustar_femea!='') {

		$str=$valor[16] . '<|>' . $valor[17] . '<|>' . $valor[18] . '<|>' . 
		          $valor[19] . '<|>';
		echo $str;  
		mysqli_close($conector);
		exit;
    }
    else {
	    $valor[8] = $matriz_com_itens;

		$str=$valor[0] . '<|>';

		for ($i=1; $i<=20; $i++){
		    $str.=$valor[$i] . '<|>';
		}
		echo $str; 
		mysqli_close($conector);
		exit;
    }
}					

$valor[0]=999999999;
$valor[1]='Pesagem não cadastrada.';
$str=$valor[0] . '<|>' . $valor[1] . '<|>';
echo $str; 
mysqli_close($conector);
?>