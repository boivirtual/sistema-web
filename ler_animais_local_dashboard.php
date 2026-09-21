<?php
include "conecta_mysql.inc";

$local = $_POST['local'];  
$controle_estoque= $_SESSION['controle_estoque'];

$valor[0]=0;
$valor[1]=0;
$valor[2]='';

@ session_start();
$codigo_usuario = $_SESSION['id_usuario'];

$tbl_usuario = "SELECT * FROM usuario WHERE id_usuario = '$codigo_usuario' AND 
                                       lixeira_usuario=0 ";  
$query = mysqli_query($conector_acesso, $tbl_usuario);

$num_rows_usuario = mysqli_num_rows($query);

if ($num_rows_usuario!=0){
	$reg_usuario = mysqli_fetch_assoc($query);

	$array_locais_usuario = explode(',', $reg_usuario['local_usuario']);
	$qtd_locais_usuario = count($array_locais_usuario);

	if ($qtd_locais_usuario==0) {
		$array_locais_usuario='';
	}
}
else {
	$array_locais_usuario='';
}

// ZERA ARRAY DAS QUANTIDADES DE CATEGORIAS
$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
    WHERE tab_registro_lixeira_categoria_idade='0'");
$num_rows_cat = mysqli_num_rows($categoria);	

if ($num_rows_cat!=0) {
	while ($reg_categoria = mysqli_fetch_object($categoria)) {
		$id_categoria = $reg_categoria->tab_codigo_categoria_idade;

	    $idade_de = $reg_categoria->tab_categoria_idade_de;
	    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

		$descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';
		
		if ($descricao_cat=='37 a 999999999 meses'){
			$descricao_cat = '> 36 meses';
		}
		
        $desc_categoria[$id_categoria]=$descricao_cat;
        $total_categoria[$id_categoria]=0;
        $total_femea[$id_categoria]=0;
        $total_macho[$id_categoria]=0;
        $percentual_categoria[$id_categoria]=0;
	}
}

if ($controle_estoque=='I') {
	if ($local==0) {
		$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	                                WHERE tbl_animal_ativo='S' AND 
	                                      tbl_animal_lixeira=0");
	}
	else {
		$tbl_animal = mysqli_query($conector, "SELECT * FROM tbl_animais
	                                WHERE tbl_animal_codigo_fazenda='$local' AND 
	                                      tbl_animal_ativo='S' AND 
	                                      tbl_animal_lixeira=0");
	}

	/*if ($local==0) {
		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	                                WHERE tbl_animal_pasto_situacao='A'");
	}
	else {
		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	                                WHERE tbl_animal_pasto_local ='$local' AND 
	                                      tbl_animal_pasto_situacao='A'");
	}*/

	$num_rows_animais = mysqli_num_rows($tbl_animal);	
	//$num_rows_animais = mysqli_num_rows($tbl_pasto);	

	$quantidade_total = 0;

	if ($num_rows_animais!=0){
		while ($reg_animal = mysqli_fetch_object($tbl_animal)) {
			$data_nascimento = $reg_animal->tbl_animal_data_nascimento;
			$id_fazenda = $reg_animal->tbl_animal_codigo_fazenda;
			$sexo = $reg_animal->tbl_animal_sexo ;
		
		/*while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
			$id_fazenda = $reg_pasto->tbl_animal_pasto_local;
			$data_nascimento = $reg_pasto->tbl_animal_pasto_nascimento;
			$sexo = $reg_pasto->tbl_animal_pasto_sexo;
			*/
			foreach ($array_locais_usuario as $value) {
				$value = ltrim($value);
				$value = rtrim($value);

				if ($value==$id_fazenda) {
					$quantidade_total++;

                    //$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

					$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
					    WHERE tab_registro_lixeira_categoria_idade='0'");
					$num_rows_cat = mysqli_num_rows($categoria);	

					if ($num_rows_cat!=0) {
						while ($reg_categoria = mysqli_fetch_object($categoria)) {
							$id_categoria = $reg_categoria->tab_codigo_categoria_idade ;
						    $idade_de = $reg_categoria->tab_categoria_idade_de;
						    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

						    if ($idade >= $idade_de && $idade <= $idade_ate) {
						    	$descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';
						    	if ($descricao_cat=='37 a 999999999 meses'){
						    		$descricao_cat = '> 36 meses';
						    	}

						    	$desc_categoria[$id_categoria]=$descricao_cat;

						    	$total_categoria[$id_categoria]++;

						        $percentual = ($total_categoria[$id_categoria] * 100) / $quantidade_total;
						        $percentual_categoria[$id_categoria] = $percentual;

						        if ($sexo=="F") {
					        		$total_femea[$id_categoria]++;
						        }
						        else {
		        					$total_macho[$id_categoria]++;
						        }
						    }
						}
					}					
				}
			}                    	
		}
	}
}
else {
	if ($local==0) {
		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	                                WHERE tbl_animal_pasto_situacao='A'");
	}
	else {
		$tbl_pasto = mysqli_query($conector, "SELECT * FROM tbl_animal_pasto
	                                WHERE tbl_animal_pasto_local ='$local' AND 
	                                      tbl_animal_pasto_situacao='A'");
	}

	$num_rows_pasto = mysqli_num_rows($tbl_pasto);	

	$quantidade_total = 0;

	if ($num_rows_pasto!=0){
		while ($reg_pasto = mysqli_fetch_object($tbl_pasto)) {
			$id_fazenda = $reg_pasto->tbl_animal_pasto_local;
			$data_nascimento = $reg_pasto->tbl_animal_pasto_nascimento;
			$sexo = $reg_pasto->tbl_animal_pasto_sexo;

			foreach ($array_locais_usuario as $value) {
				$value = ltrim($value);
				$value = rtrim($value);
				if ($value==$id_fazenda) {

                    //$data_nascimento = $reg_animal->tbl_animal_data_nascimento;  
                    $data_acompanhamento_calculo = date("Y-m-d");
                    $date = new DateTime($data_nascimento); // Data de Nascimento
                    $idade_acompanhamento = $date->diff(new DateTime($data_acompanhamento_calculo));
                    $idade_acompanhamento_mostra_anos = $idade_acompanhamento->format('%Y')*12;
                    $idade_acompanhamento_mostra_meses = $idade_acompanhamento->format('%m');
                    $idade = $idade_acompanhamento_mostra_anos+$idade_acompanhamento_mostra_meses;

					$categoria = mysqli_query($conector, "SELECT * FROM tabela_categoria_idade
					        WHERE tab_registro_lixeira_categoria_idade='0'");
					$num_rows_cat = mysqli_num_rows($categoria);	

					if ($num_rows_cat!=0) {
						while ($reg_categoria = mysqli_fetch_object($categoria)) {
						    $idade_de = $reg_categoria->tab_categoria_idade_de;
						    $idade_ate = $reg_categoria->tab_categoria_idade_ate;

							$descricao_cat = $idade_de . ' a ' . $idade_ate . ' meses';

							if ($idade_ate==999999999){
								$descricao_cat = '> 36 meses';
							}

						    if ($idade >= $idade_de && $idade <= $idade_ate) {
								$id_categoria = $reg_categoria->tab_codigo_categoria_idade;

								$desc_categoria[$id_categoria]=$descricao_cat;

								if ($sexo=='F') {
							        $total_femea[$id_categoria]++;
									$total_categoria[$id_categoria]++;
									$quantidade_total++;
								}
								else {
							        $total_macho[$id_categoria]++;
									$total_categoria[$id_categoria]++;
									$quantidade_total++;
								}

							    if ($total_categoria[$id_categoria]!=0 && $quantidade_total!=0) {
									$percentual = ($total_categoria[$id_categoria] * 100) / $quantidade_total;
									$percentual_categoria[$id_categoria] = $percentual;
							    }
						    }
						}
					}
				}
			}
		}
	}
}

$sql = "SELECT * from tbl_movimentacao 
        WHERE tbl_movimentacao_lixeira=0 AND tbl_movimentacao_tipo=5 AND 
              tbl_movimentacao_situacao='N'"; 

$rs = mysqli_query($conector, $sql); 
$num_rows_mov = mysqli_num_rows($rs);	
$existe_transferencia = 'N';

if ($num_rows_mov!=0) {
	while ($reg_mov = mysqli_fetch_object($rs)){
	    $local_destino = $reg_mov->tbl_movimentacao_codigo_local_destino;

		foreach ($array_locais_usuario as $value) {
			$value = ltrim($value);
			$value = rtrim($value);

			if ($value==$local_destino) {
				$existe_transferencia='S';
			}
		}
	}
}

$desc_categoria = implode("|", $desc_categoria);
$total_categoria = implode("|", $total_categoria);
$percentual_categoria = implode("|", $percentual_categoria);
$total_macho = implode("|", $total_macho);
$total_femea = implode("|", $total_femea);

$valor[0]= $quantidade_total;
$valor[1]= $desc_categoria;
$valor[2]= $total_categoria;
$valor[3]= $percentual_categoria;
$valor[4]= $total_macho;
$valor[5]= $total_femea;
$valor[6]= $existe_transferencia;

$str=$valor[0] . '<|>' . $valor[1] . '<|>' . $valor[2] . '<|>' . $valor[3] . '<|>' . 
     $valor[4] . '<|>' . $valor[5] . '<|>' . $valor[6];
echo $str; 
?>